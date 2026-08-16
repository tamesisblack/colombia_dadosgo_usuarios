<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\VendorUsers;
use App\Models\User;
use Razorpay\Api\Api;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\XenditSdkException;
use GuzzleHttp\Client;
use Session;

class CheckoutController extends Controller
{
    public function __construct()
    {
        // Apply auth middleware with success route exception for payment callbacks
        $this->middleware('auth', ['except' => ['success']]);
        
        // Bypass location check for success method to handle payment callbacks
        $request = request();
        if ($request && $request->is('success')) {
            // Skip location check for success route
        } else if (!isset($_COOKIE['address_name'])) {
            \Redirect::to('set-location')->send();
        }
    }
    
    public function checkout()
    {

        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $cart = Session::get('cart', []);

        $paymentError = Session::get('payment_error');
        if ($paymentError && isset($cart['cart_order'])) {
            unset($cart['cart_order']);
            Session::put('cart', $cart);
            Session::save();
        }

        if (Session::get('takeawayOption') == 'true') {
        } else {
            $deliveryChargemain = @$_COOKIE['deliveryChargemain'];
            $address_lat = @$_COOKIE['address_lat'];
            $address_lng = @$_COOKIE['address_lng'];
            $restaurant_latitude = @$_COOKIE['restaurant_latitude'];
            $restaurant_longitude = @$_COOKIE['restaurant_longitude'];
            if (@$deliveryChargemain && @$address_lat && @$address_lng && @$restaurant_latitude && @$restaurant_longitude) {
                $deliveryChargemain = json_decode($deliveryChargemain);
                if (!empty($deliveryChargemain)) {
                    if (!empty($cart['distanceType'])) {
                        $distanceType = $cart['distanceType'];
                    } else {
                        $distanceType = 'km';
                    }
                    $delivery_charges_per_km = $deliveryChargemain->delivery_charges_per_km;
                    $minimum_delivery_charges = $deliveryChargemain->minimum_delivery_charges;
                    $minimum_delivery_charges_within_km = $deliveryChargemain->minimum_delivery_charges_within_km;
                    $kmradius = $this->distance($address_lat, $address_lng, $restaurant_latitude, $restaurant_longitude, $distanceType);
                    if ($minimum_delivery_charges_within_km > $kmradius) {
                        $cart['deliverychargemain'] = $minimum_delivery_charges;
                    } else {
                        $cart['deliverychargemain'] = round($kmradius * $delivery_charges_per_km, 2);
                    }
                    $cart['deliverykm'] = $kmradius;
                }
            }

            if (@$cart['isSelfDelivery'] === true || @$cart['isSelfDelivery'] === "true" ) {
                $cart['deliverycharge'] = 0;
            } else {
                $cart['deliverycharge'] = @$cart['deliverychargemain'];
            }

            $cart = $this->calculateTax($cart);
            
            Session::put('cart', $cart);
            Session::save();
        }

        return view('checkout.checkout', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'errorMessage' => $paymentError]);
    }

    public function calculateTax($cart){

        $cart['taxBreakdownGrouped'] = [
            'item' => [],
            'order' => [],
            'delivery' => [],
            'packaging' => [],
            'platform' => []
        ];

        if(!isset($cart['restaurant'])) return;

        $restaurant_id = $cart['restaurant']['id'];
        
        // Item subtotal before discount
        $itemSubtotal = 0;
        foreach ($cart['item'][$restaurant_id] as $item) {
            $itemSubtotal += ($item['item_price'] + ($item['extra_price'] ?? 0)) * $item['quantity'];
        }
        
        // Calculate discount (coupon + special offer)
        $discount_amount = 0;
        if (!empty($cart['coupon'])) {
            if ($cart['coupon']['discountType'] === 'Fix Price') {
                $discount_amount = min($cart['coupon']['discount'], $itemSubtotal);
            } else {
                $discount_amount = min(($itemSubtotal * $cart['coupon']['discount']) / 100, $itemSubtotal);
            }
        }

        // $totalDiscount = $discount_amount + ($cart['specialOfferDiscount'] ?? 0);
        $specialOfferDiscount = 0;
        if (!empty($cart['specialOfferDiscountVal'])) {
            if (($cart['specialOfferType'] ?? '') === 'amount') {
                $specialOfferDiscount = (float) $cart['specialOfferDiscountVal'];
            } else {
                $specialOfferDiscount = ($itemSubtotal * (float) $cart['specialOfferDiscountVal']) / 100;
            }
        }
        $cart['specialOfferDiscount'] = $specialOfferDiscount;
        $totalDiscount = $discount_amount + $specialOfferDiscount;
        
        $totalTax = 0;

        // Prepare admin-enabled product taxes
        $globalProductTaxes = [];
        foreach ($cart['taxesByScope']['product'] ?? [] as $tax) {
            if ($tax['enable'] ?? false) {
                $globalProductTaxes[$tax['id']] = $tax;
            }
        }

        // PRODUCT-LEVEL TAX
        if ($cart['taxScope'] === 'product') {
            foreach ($cart['item'] as $restaurantItemsKey => $restaurantItems) {
                foreach ($restaurantItems as $itemKey => $item) {
                    $itemGross = ($item['item_price'] + ($item['extra_price'] ?? 0)) * $item['quantity'];
                    $itemDiscount = ($itemSubtotal > 0) ? ($itemGross / $itemSubtotal) * $totalDiscount : 0;
                    $itemTaxable = max(0, $itemGross - $itemDiscount);
                    $itemTaxes = [];
                    foreach ($item['taxSetting'] ?? [] as $itemTax) {
                        if (($itemTax['scope'] ?? 'product') === 'product' && isset($globalProductTaxes[$itemTax['id']])) {
                            $adminTax = $globalProductTaxes[$itemTax['id']];
                            if ($adminTax['type'] === 'percentage') {
                                $taxAmount = $this->applyTax($itemTaxable, $adminTax);
                            } else {
                                $taxAmount = $adminTax['tax'] * $item['quantity'];
                            }
                            $totalTax += $taxAmount;
                            $cart['taxBreakdownGrouped']['item'][$adminTax['title']] =
                                ($cart['taxBreakdownGrouped']['item'][$adminTax['title']] ?? 0) + $taxAmount;
                            $itemTaxes[] = ($adminTax['type'] ?? 'percentage') === 'percentage'
                                ? "{$adminTax['title']} ({$adminTax['tax']}%)"
                                : "{$adminTax['title']} (" . $this->formatCurrency($taxAmount, $cart['currencyData']) . ")";
                        }
                    }
                    if (empty($itemTaxes)) {
                        $cart['taxBreakdownGrouped']['item']['none'] = ($cart['taxBreakdownGrouped']['item']['none'] ?? 0) + 0;
                    }
                    $cart['item'][$restaurantItemsKey][$itemKey]['taxLabel'] = implode(', ', array_unique($itemTaxes));
                }
            }
        }

        // ORDER-LEVEL TAX
        if ($cart['taxScope'] === 'order') {
            $orderTaxable = max(0, $itemSubtotal - $totalDiscount);
            foreach ($cart['taxesByScope']['order'] ?? [] as $tax) {
                if ($tax['enable'] ?? true) {
                    $taxAmount = $this->applyTax($orderTaxable, $tax);
                    $totalTax += $taxAmount;
                    $cart['taxBreakdownGrouped']['order'][$tax['title']] =
                        ($cart['taxBreakdownGrouped']['order'][$tax['title']] ?? 0) + $taxAmount;
                }
            }
        }

        // DELIVERY, PACKAGING, PLATFORM TAXES
        $extraScopes = ['delivery', 'packaging', 'platform'];
        foreach ($extraScopes as $scope) {
            $charge = $scope == "delivery" ? ($cart['deliverycharge'] ?? 0) : ($cart[$scope . 'Charge'] ?? 0);
            foreach ($cart['taxesByScope'][$scope] ?? [] as $tax) {
                if (!isset($cart['taxBreakdownGrouped'][$scope][$tax['title']])) {
                    $cart['taxBreakdownGrouped'][$scope][$tax['title']] = 0;
                }
                $taxAmount = ($charge > 0) ? $this->applyTax($charge, $tax) : 0;
                $totalTax += $taxAmount;
                $cart['taxBreakdownGrouped'][$scope][$tax['title']] += $taxAmount;
            }
        }

        $cart['tax_amount'] = $totalTax;

        return $cart;
    }

    public function applyTax($amount, $tax) {
        if (!$tax['enable']) return 0;
        if ($tax['type'] === 'percentage') {
            return ($amount * $tax['tax']) / 100;
        }
        if ($tax['type'] === 'fix') {
            return $tax['tax'];
        }
        return 0;
    }

    public function formatCurrency($amount, $currency = []) {
        $symbol = $currency['symbol'] ?? '';
        $decimals = $currency['decimal_degits'] ?? 2;
        $symbolAtRight = filter_var($currency['symbolAtRight'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $formatted = number_format($amount, $decimals);

        return $symbolAtRight
            ? $formatted . ' ' . $symbol
            : $symbol . $formatted;
    }

    public function proccesstopay(Request $request)
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $cart = Session::get('cart', []);
        if (@$cart['cart_order']) {
            if ($cart['cart_order']['payment_method'] == 'razorpay') {
                $razorpaySecret = $cart['cart_order']['razorpaySecret'];
                $razorpayKey = $cart['cart_order']['razorpayKey'];
                $authorName = $cart['cart_order']['authorName'];
                $total_pay = $cart['cart_order']['total_pay'];
                $amount = 0;
                $formatted_price = $cart['cart_order']['currencyData']['symbol'] . number_format($total_pay, $cart['cart_order']['currencyData']['decimal_degits']);
                return view('checkout.razorpay', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'razorpaySecret' => $razorpaySecret, 'razorpayKey' => $razorpayKey, 'cart_order' => $cart['cart_order'], 'formatted_price' => $formatted_price]);
            } else if ($cart['cart_order']['payment_method'] == 'payfast') {
                $payfast_merchant_key = $cart['cart_order']['payfast_merchant_key'];
                $payfast_merchant_id = $cart['cart_order']['payfast_merchant_id'];
                $payfast_isSandbox = $cart['cart_order']['payfast_isSandbox'];
                $payfast_return_url = route('success');
                $payfast_notify_url = route('notify');
                $payfast_cancel_url = route('pay');
                $authorName = $cart['cart_order']['authorName'];
                $total_pay = $cart['cart_order']['total_pay'];
                $formatted_price = $cart['cart_order']['currencyData']['symbol'] . number_format($total_pay, $cart['cart_order']['currencyData']['decimal_degits']);
                $amount = 0;
                $token = uniqid();
                Session::put('payfast_payment_token', $token);
                Session::save();
                $payfast_return_url = $payfast_return_url . '?token=' . $token;
                $amount = number_format($total_pay, 2, '.', '');
                $data = [
                    'merchant_id' => $payfast_merchant_id,
                    'merchant_key' => $payfast_merchant_key,
                    'return_url' => $payfast_return_url,
                    'cancel_url' => $payfast_cancel_url,
                    'notify_url' => $payfast_notify_url,
                    'name_first' => $authorName,
                    'm_payment_id' => $token,
                    'amount' => $amount,
                    'item_name' => 'Test',
                ];
                $signature = $this->generateSignature($data);
                $data['signature'] = $signature;
                $pfHost = $payfast_isSandbox == 'true' ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
                return view('checkout.payfast', [
                    'amount' => $amount,
                    'pfHost' => $pfHost,
                    'data' => $data,
                    'payfast_merchant_key' => $payfast_merchant_key,
                    'payfast_merchant_id' => $payfast_merchant_id,
                    'payfast_return_url' => $payfast_return_url,
                    'payfast_notify_url' => $payfast_notify_url,
                    'payfast_cancel_url' => $payfast_cancel_url,
                    'item_name' => 'Test',
                    'formatted_price' => $formatted_price,
                ]);
            } else if ($cart['cart_order']['payment_method'] == 'xendit') {
                $xendit_enable = $cart['cart_order']['xendit_enable'];
                $xendit_apiKey = $cart['cart_order']['xendit_apiKey'];
                if (isset($xendit_enable) && $xendit_enable == true) {
                    $total_pay = $cart['cart_order']['total_pay'];
                    $currency = $cart['cart_order']['currencyData']['code'];
                    $fail_url = route('pay');
                    $success_url = route('success');
                    Configuration::setXenditKey($xendit_apiKey);
                    $token = uniqid();
                    $success_url = $success_url . '?xendit_token=' . $token;
                    Session::put('xendit_payment_token', $token);
                    Session::save();
                    $apiInstance = new InvoiceApi();

                    $amount = (int) $total_pay * 1000;
                    $create_invoice_request = new CreateInvoiceRequest([
                        'external_id' => $token,
                        'description' => '#' . $token . ' Order place',
                        'amount' => $amount,
                        'invoice_duration' => 300,
                        'currency' => 'IDR',
                        'success_redirect_url' => $success_url,
                        'failure_redirect_url' => $fail_url,
                    ]);
                    try {
                        $result = $apiInstance->createInvoice($create_invoice_request);
                        return redirect($result['invoice_url']);
                    } catch (XenditSdkException $e) {
                        return response()->json(
                            [
                                'message' => 'Exception when calling InvoiceApi->createInvoice: ' . $e->getMessage(),
                                'error' => $e->getFullError(),
                            ],
                            500,
                        );
                    }
                }
            } else if ($cart['cart_order']['payment_method'] == 'midtrans') {
                $midtrans_enable = $cart['cart_order']['midtrans_enable'];
                $midtrans_serverKey = $cart['cart_order']['midtrans_serverKey'];
                $midtrans_isSandbox = $cart['cart_order']['midtrans_isSandbox'];
                if (isset($midtrans_enable) && isset($midtrans_serverKey) && $midtrans_enable == true) {
                    if ($midtrans_isSandbox == true) {
                        $url = 'https://api.sandbox.midtrans.com/v1/payment-links';
                    } else {
                        $url = 'https://api.midtrans.com/v1/payment-links';
                    }
                    $total_pay = $cart['cart_order']['total_pay'];
                    $currency = $cart['cart_order']['currencyData']['code'];
                    $fail_url = route('pay');
                    $success_url = route('success');
                    $token = uniqid();
                    $success_url = $success_url . '?midtrans_token=' . $token;
                    Session::put('midtrans_payment_token', $token);
                    Session::save();
                    $payload = [
                        'transaction_details' => [
                            'order_id' => $token,
                            'gross_amount' => (int) $total_pay * 1000,
                        ],
                        'usage_limit' => 1,
                        'callbacks' => [
                            'error' => $fail_url,
                            'unfinish' => $fail_url,
                            'close' => $fail_url,
                            'finish' => $success_url,
                        ],
                    ];
                    try {
                        $client = new Client();
                        $response = $client->post($url, [
                            'headers' => [
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Basic ' . base64_encode($midtrans_serverKey),
                            ],
                            'body' => json_encode($payload),
                        ]);
                        $responseBody = json_decode($response->getBody(), true);
                        if (isset($responseBody['payment_url'])) {
                            return redirect($responseBody['payment_url']);
                        } else {
                            return response()->json(['error' => 'Failed to generate payment link'], 500);
                        }
                    } catch (\Exception $e) {
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                }
            } 
            else if ($cart['cart_order']['payment_method'] == 'orangepay') {
                $orangepay_enable = $cart['cart_order']['orangepay_enable'];
                $orangepay_isSandbox = $cart['cart_order']['orangepay_isSandbox'];
                Session::put('orangepay_isSandbox', $orangepay_isSandbox);
                Session::save();
                $orangepay_clientId = $cart['cart_order']['orangepay_clientId'];
                $orangepay_clientSecret = $cart['cart_order']['orangepay_clientSecret'];
                $orangepay_merchantKey = $cart['cart_order']['orangepay_merchantKey'];
                
                $token = $this->getAccessToken($orangepay_clientId, $orangepay_clientSecret);
                
                if (is_string($token) && strpos($token, 'Client error') !== false) {
                    // Token acquisition failed
                    Session::flash('payment_error', 'Failed to initialize Orange Money payment. Please check credentials.');
                    return redirect()->route('checkout');
                }
                
                Session::put('orangepay_access_token', $token);
                Session::save();
                
                if (isset($token) && $token != null && isset($orangepay_enable) && isset($orangepay_clientId) && $orangepay_enable == true) {
                    $url = $orangepay_isSandbox == true 
                        ? 'https://api.orange.com/orange-money-webpay/dev/v1/webpayment'
                        : 'https://api.orange.com/orange-money-webpay/cm/v1/webpayment';
                    
                    $total_pay = $cart['cart_order']['total_pay'];
                    $currency = $orangepay_isSandbox == true ? 'OUV' : $cart['cart_order']['currencyData']['code'];
                    $orangepay_token = uniqid();
                    
                    $successPage = route('success');
                    $cancelPage = route('checkout'); // Changed from route('pay') to route('checkout')
                    
                    $success_url = $successPage . '?orangepay_token=' . $orangepay_token;
                    $cancel_url  = $cancelPage  . '?orangepay_token=' . $orangepay_token;
                    $notify_url   = $success_url; // Or create a dedicated notify URL
                    
                    Session::put('orangepay_payment_token', $orangepay_token);
                    Session::save();
                    
                    // Convert amount to integer as required by Orange API
                    $amount = (int) $total_pay;
                    
                    $payload = [
                        'merchant_key' => $orangepay_merchantKey,
                        'currency' => $currency,
                        'order_id' => $orangepay_token,
                        'amount' => $amount,
                        'return_url' => $success_url,
                        'cancel_url' => $cancel_url,
                        'notif_url' => $notify_url,
                        'lang' => 'en',
                        'reference' => $orangepay_token,
                    ];
                    
                    try {
                        $client = new Client();
                        $response = $client->post($url, [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $token,
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json'
                            ],
                            'json' => $payload,
                            'http_errors' => false
                        ]);
                        
                        $statusCode = $response->getStatusCode();
                        $responseBody = json_decode($response->getBody(), true);
                        
                        \Log::info('Orange Money Create Payment Response', [
                            'status_code' => $statusCode,
                            'response' => $responseBody
                        ]);
                        
                        if (($statusCode === 200 || $statusCode === 201) && isset($responseBody['payment_url'])) {
                            // Store the pay_token for later verification
                            if (isset($responseBody['pay_token'])) {
                                Session::put('orangepay_payment_check_token', $responseBody['pay_token']);
                                Session::save();
                            }
                            return redirect($responseBody['payment_url']);
                        } else {
                            $errorMessage = isset($responseBody['description']) 
                                ? $responseBody['description'] 
                                : (isset($responseBody['message']) ? $responseBody['message'] : 'Payment request failed');
                            
                            \Log::error('Orange Money Payment Creation Failed', [
                                'response' => $responseBody,
                                'payload' => $payload
                            ]);
                            
                            Session::flash('payment_error', 'Orange Money: ' . $errorMessage);
                            return redirect()->route('checkout');
                        }
                    } catch (\Exception $e) {
                        \Log::error('Orange Money Exception in proccesstopay', [
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        Session::flash('payment_error', 'Orange Money Error: ' . $e->getMessage());
                        return redirect()->route('checkout');
                    }
                } else {
                    Session::flash('payment_error', 'Orange Money configuration is incomplete.');
                    return redirect()->route('checkout');
                }
            }
            else if ($cart['cart_order']['payment_method'] == 'paystack') {
                $paystack_public_key = $cart['cart_order']['paystack_public_key'];
                $paystack_secret_key = $cart['cart_order']['paystack_secret_key'];
                $paystack_isSandbox = $cart['cart_order']['paystack_isSandbox'];
                $authorName = $cart['cart_order']['authorName'];
                $total_pay = $cart['cart_order']['total_pay'];
                $amount = 0;
                \Paystack\Paystack::init($paystack_secret_key);
                $payment = \Paystack\Transaction::initialize([
                    'email' => $email,
                    'amount' => (int) ($total_pay * 100),
                    'callback_url' => route('success'),
                    
                ]);
                Session::put('paystack_authorization_url', $payment->authorization_url);
                Session::put('paystack_access_code', $payment->access_code);
                Session::put('paystack_reference', $payment->reference);
                Session::save();
                if ($payment->authorization_url) {
                    $script = "<script>
                        window.location = '" . $payment->authorization_url . "';
                    </script>";
                    echo $script;
                    exit();
                } else {
                    $script = "<script>
                        window.location = '" . url('') . "';
                    </script>";
                    echo $script;
                    exit();
                }
            } else if ($cart['cart_order']['payment_method'] == 'flutterwave') {
                $currency = 'USD';
                if (@$cart['cart_order']['currencyData']['code']) {
                    $currency = $cart['cart_order']['currencyData']['code'];
                }
                $flutterWave_secret_key = $cart['cart_order']['flutterWave_secret_key'];
                $flutterWave_public_key = $cart['cart_order']['flutterWave_public_key'];
                $flutterWave_isSandbox = $cart['cart_order']['flutterWave_isSandbox'];
                $flutterWave_encryption_key = $cart['cart_order']['flutterWave_encryption_key'];
                $authorName = $cart['cart_order']['authorName'];
                $total_pay = $cart['cart_order']['total_pay'];
                $formatted_price = $cart['cart_order']['currencyData']['symbol'] . number_format($total_pay, $cart['cart_order']['currencyData']['decimal_degits']);
                Session::put('flutterwave_pay', 1);
                Session::save();
                $token = uniqid();
                Session::put('flutterwave_pay_tx_ref', $token);
                Session::save();
                return view('checkout.flutterwave', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'flutterWave_secret_key' => $flutterWave_secret_key, 'flutterWave_public_key' => $flutterWave_public_key, 'flutterWave_isSandbox' => $flutterWave_isSandbox, 'flutterWave_encryption_key' => $flutterWave_encryption_key, 'token' => $token, 'cart_order' => $cart['cart_order'], 'currency' => $currency, 'formatted_price' => $formatted_price]);
            } else if ($cart['cart_order']['payment_method'] == 'mercadopago') {
                $currency = 'USD';
                if (@$cart['cart_order']['currencyData']['code']) {
                    $currency = $cart['cart_order']['currencyData']['code'];
                }
                $mercadopago_public_key = $cart['cart_order']['mercadopago_public_key'];
                $mercadopago_access_token = $cart['cart_order']['mercadopago_access_token'];
                $mercadopago_isSandbox = $cart['cart_order']['mercadopago_isSandbox'];
                $mercadopago_isEnabled = $cart['cart_order']['mercadopago_isEnabled'];
                $quantity = $cart['cart_order']['quantity'];
                $id = $cart['cart_order']['id'];
                $total_pay = $cart['cart_order']['total_pay'];
                $items['title'] = $id;
                $items['quantity'] = 1;
                $items['unit_price'] = floatval($total_pay);
                $fields[] = $items;
                $item['items'] = $fields;
                $item['back_urls']['failure'] = route('pay');
                $item['back_urls']['pending'] = route('notify');
                $item['back_urls']['success'] = route('success');
                $item['auto_return'] = 'all';
                Session::put('mercadopago_pay', 1);
                Session::save();
                $url = 'https://api.mercadopago.com/checkout/preferences';
                $data = ['Accept: application/json', 'Authorization:Bearer ' . $mercadopago_access_token];
                $post_data = json_encode($item);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization:Bearer ' . $mercadopago_access_token]);
                $response = curl_exec($ch);
                if ($response === false) {
                    $error = curl_error($ch);
                    curl_close($ch);
                    Session::put('payment_error', 'Unable to initialize payment, credentials are invalid or not authorized. Please check credentials, environment (sandbox/live), and account region.');
                    return redirect()->route('checkout');
                }
                curl_close($ch);
                $mercadopago = json_decode($response);
                if (!isset($mercadopago->id)) {
                    Session::put('payment_error', 'Unable to initialize payment, credentials are invalid or not authorized. Please check credentials, environment (sandbox/live), and account region.');
                    return redirect()->route('checkout');
                }
                Session::put('mercadopago_preference_id', $mercadopago->id);
                Session::save();
                $authorName = $cart['cart_order']['authorName'];
                $total_pay = $cart['cart_order']['total_pay'];
                if ($mercadopago_isSandbox == 'true') {
                    $payment_url = $mercadopago->sandbox_init_point;
                } else {
                    $payment_url = $mercadopago->init_point;
                }
                echo "<script>
                    location.href = '" . $payment_url . "';
                </script>";
                exit();
            } else if ($cart['cart_order']['payment_method'] == 'stripe') {
                $stripeKey = $cart['cart_order']['stripeKey'];
                $stripeSecret = $cart['cart_order']['stripeSecret'];
                $authorName = $cart['cart_order']['authorName'];
                $total_pay = $cart['cart_order']['total_pay'];
                $address_line1 = $cart['cart_order']['address_line1'];
                $address_line2 = $cart['cart_order']['address_line2'];
                $address_zipcode = $cart['cart_order']['address_zipcode'];
                $address_city = $cart['cart_order']['address_city'];
                $address_country = $cart['cart_order']['address_country'];
                $stripeSecret = $cart['cart_order']['stripeSecret'];
                $stripeKey = $cart['cart_order']['stripeKey'];
                $isStripeSandboxEnabled = $cart['cart_order']['isStripeSandboxEnabled'];
                $authorName = $cart['cart_order']['authorName'];
                $total_pay = $cart['cart_order']['total_pay'];
                $formatted_price = $cart['cart_order']['currencyData']['symbol'] . number_format($total_pay, $cart['cart_order']['currencyData']['decimal_degits']);
                $amount = 0;
                return view('checkout.stripe', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'stripeSecret' => $stripeSecret, 'stripeKey' => $stripeKey, 'cart_order' => $cart['cart_order'], 'formatted_price' => $formatted_price]);
            } else if ($cart['cart_order']['payment_method'] == 'paypal') {
                $paypalKey = $cart['cart_order']['paypalKey'];
                $paypalSecret = $cart['cart_order']['paypalSecret'];
                $authorName = $cart['cart_order']['authorName'];
                $total_pay = $cart['cart_order']['total_pay'];
                $address_line1 = $cart['cart_order']['address_line1'];
                $address_line2 = $cart['cart_order']['address_line2'];
                $address_zipcode = $cart['cart_order']['address_zipcode'];
                $address_city = $cart['cart_order']['address_city'];
                $address_country = $cart['cart_order']['address_country'];
                $ispaypalSandboxEnabled = $cart['cart_order']['ispaypalSandboxEnabled'];
                $amount = 0;
                $formatted_price = $cart['cart_order']['currencyData']['symbol'] . number_format($total_pay, $cart['cart_order']['currencyData']['decimal_degits']);
                return view('checkout.paypal', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'paypalSecret' => $paypalSecret, 'paypalKey' => $paypalKey, 'cart_order' => $cart['cart_order'], 'formatted_price' => $formatted_price]);
            }
            else if ($cart['cart_order']['payment_method'] == 'phonepe') {
                $phonepe_enable   = $cart['cart_order']['phonepe_enable'] ?? false;
                $isSandbox        = $cart['cart_order']['phonepe_isSandbox'] === 'true' || $cart['cart_order']['phonepe_isSandbox'] === true;
                $merchantId       = $cart['cart_order']['phonepe_merchantId'] ?? '';
                $saltKey          = $cart['cart_order']['phonepe_saltKey'] ?? '96434309-7796-489d-8924-ab56988a6076';
                $saltIndex        = $cart['cart_order']['phonepe_saltIndex'] ?? '1';
                $total_pay        = $cart['cart_order']['total_pay'];

                if (!$phonepe_enable || empty($merchantId) || empty($saltKey)) {
                    return redirect()->route('checkout')
                        ->with('payment_error', 'PhonePe is not properly configured. Check Merchant ID and Salt Key.');
                }

                $merchantTransactionId = 'MT' . Str::random(12);
                Session::put('phonepe_merchant_transaction_id', $merchantTransactionId);
                Session::save();

                // Get cart backup token for session recovery
                $cartBackupToken = Session::get('cart_backup_token');
                
                // FIX: Create a proper success URL with all parameters
                $successUrl = route('success') . '?phonepe_token=' . $merchantTransactionId;
                if ($cartBackupToken) {
                    $successUrl .= '&cart_token=' . $cartBackupToken;
                }
                
                // FIX: Add proper cancel/redirect URL
                $redirectUrl = route('success') . '?phonepe_token=' . $merchantTransactionId . '&status=cancelled';
                
                $payloadArray = [
                    'merchantId'            => $merchantId,
                    'merchantTransactionId' => $merchantTransactionId,
                    'merchantUserId'        => $user->firebase_uid ?? Auth::id(),
                    'amount'                => (int)($total_pay * 100),   // in paise
                    'redirectUrl'           => $successUrl,  // FIX: Use redirectUrl (not redirectUrl)
                    'redirectMode'          => 'REDIRECT',   // FIX: Add redirect mode
                    'callbackUrl'           => $successUrl,  // FIX: Add callback URL
                    'paymentInstrument'     => [
                        'type' => 'PAY_PAGE'
                    ]
                ];

                $payload   = base64_encode(json_encode($payloadArray));
                $checksum  = hash('sha256', $payload . '/pg/v1/pay' . $saltKey) . '###' . $saltIndex;

                $url = $isSandbox
                    ? 'https://api-preprod.phonepe.com/apis/hermes/pg/v1/pay'
                    : 'https://api.phonepe.com/apis/hermes/pg/v1/pay';

                try {
                    $client = new Client();
                    $response = $client->post($url, [
                        'headers' => [
                            'Content-Type'   => 'application/json',
                            'X-VERIFY'       => $checksum,
                            'X-MERCHANT-ID'  => $merchantId,
                        ],
                        'json' => ['request' => $payload]
                    ]);

                    $result = json_decode($response->getBody(), true);
                    
                    // Log the response for debugging
                    \Log::info('PhonePe API Response', ['response' => $result]);

                    // FIX: Check different response structures
                    if (isset($result['success']) && $result['success'] === true) {
                        // Check for redirect URL in different possible locations
                        $redirectUrl = null;
                        
                        if (isset($result['data']['instrumentResponse']['redirectInfo']['url'])) {
                            $redirectUrl = $result['data']['instrumentResponse']['redirectInfo']['url'];
                        } elseif (isset($result['data']['redirectInfo']['url'])) {
                            $redirectUrl = $result['data']['redirectInfo']['url'];
                        } elseif (isset($result['redirectUrl'])) {
                            $redirectUrl = $result['redirectUrl'];
                        } elseif (isset($result['redirect_url'])) {
                            $redirectUrl = $result['redirect_url'];
                        }
                        
                        if ($redirectUrl) {
                            // Add cart token to redirect URL for recovery
                            if ($cartBackupToken) {
                                $separator = strpos($redirectUrl, '?') !== false ? '&' : '?';
                                $redirectUrl .= $separator . 'cart_token=' . $cartBackupToken;
                            }
                            return redirect($redirectUrl);
                        } else {
                            throw new \Exception('No redirect URL in PhonePe response');
                        }
                    }

                    throw new \Exception($result['message'] ?? json_encode($result));
                } catch (\Exception $e) {
                    \Log::error('PhonePe Error: ' . $e->getMessage());
                    Session::put('payment_error', 'PhonePe Error: ' . $e->getMessage());
                    return redirect()->route('checkout');
                }
            }
            else if($cart['cart_order']['payment_method']=='cashfree'){
                $cashfree_clientId     = $cart['cart_order']['cashfree_clientId'];
                $cashfree_clientSecret = $cart['cart_order']['cashfree_clientSecret'];
                $cashfree_isSandbox    = $cart['cart_order']['cashfree_isSandbox'];

                $authorName = $cart['cart_order']['authorName'] ?? Auth::user()->name ?? 'User';
                $email      = Auth::user()->email ?? 'noemail@example.com';

                $phone = Auth::user()->phone ?? '9999999999';   // ← fallback for testing; in production, make it required!

                // Optional: Basic validation (Cashfree expects 10-digit number for INR)
                if (!preg_match('/^\d{10}$/', $phone)) {
                    $phone = '9999999999'; // or return error: Session::put('payment_error', 'Invalid phone number for Cashfree');
                }

                $total_pay  = $cart['cart_order']['total_pay'];
                $currency   = 'INR';

                if (!empty($cashfree_clientId) && !empty($cashfree_clientSecret)) {
                    $baseUrl = ($cashfree_isSandbox == "true" || $cashfree_isSandbox === true)
                        ? "https://sandbox.cashfree.com/pg/links"
                        : "https://api.cashfree.com/pg/links";

                    $token = uniqid('cf_');
                    $success_url = route('success') . '?cashfree_token=' . $token;

                    Session::put('cashfree_payment_token', $token);
                    Session::save();

                    $payload = [
                        'link_id'           => $token,
                        'link_amount'       => (float)$total_pay,
                        'link_currency'     => strtoupper($currency),
                        'link_purpose'      => 'Wallet Topup',
                        'customer_details'  => [
                            'customer_name'  => $authorName,
                            'customer_email' => $email,
                            'customer_phone' => $phone,           // ← THIS WAS MISSING – ADD IT!
                        ],
                        'link_success_url'  => $success_url,
                         'link_meta'=>['return_url' => $success_url], // ← some versions of Cashfree require this too
                        'link_expiry_time'  => now()->addMinutes(30)->toIso8601String(),
                    ];

                    try {
                        $client = new Client();
                        $response = $client->post($baseUrl, [
                            'headers' => [
                                'x-client-id'     => $cashfree_clientId,
                                'x-client-secret' => $cashfree_clientSecret,
                                'x-api-version'   => '2025-01-01',   // keep this
                                'Content-Type'    => 'application/json',
                                'Accept'          => 'application/json',
                            ],
                            'body' => json_encode($payload),
                            // 'verify' => false,   // remove in production
                        ]);

                        $result = json_decode($response->getBody(), true);

                        if (isset($result['link_url']) && $result['link_url']) {
                            return redirect($result['link_url']);
                        }

                        Session::put('payment_error', 'Cashfree link creation failed: ' . json_encode($result));
                        return redirect()->route('checkout');

                    } catch (\GuzzleHttp\Exception\ClientException $e) {
                        $responseBody = $e->hasResponse() ? (string) $e->getResponse()->getBody() : '';
                        Session::put('payment_error', 'Cashfree Client Error: ' . $responseBody);
                        return redirect()->route('checkout');
                    } catch (\Exception $e) {
                        Session::put('payment_error', 'Cashfree Error: ' . $e->getMessage());
                        return redirect()->route('checkout');
                    }
                }

                Session::put('payment_error', 'Cashfree credentials missing or invalid');
                return redirect()->route('checkout');
            }
            else if ($cart['cart_order']['payment_method'] === 'instamojo') {

                $isSandbox     = $cart['cart_order']['instamojo_isSandbox'];
                $clientId      = $cart['cart_order']['instamojo_clientId'] ?? '';
                $clientSecret  = $cart['cart_order']['instamojo_clientSecret'] ?? '';

                if (empty($clientId) || empty($clientSecret)) {
                    // DON'T clear cart_order, just set error and redirect
                    Session::flash('payment_error', 'Instamojo credentials are missing or invalid.');
                    return redirect()->route('checkout')->withInput();
                }

                $baseUrl = $isSandbox
                    ? 'https://test.instamojo.com/v2/'
                    : 'https://api.instamojo.com/v2/';

                $amount       = (float) ($cart['cart_order']['total_pay'] ?? 0);
                $currency     = 'INR';

                if ($currency !== 'INR') {
                    Session::flash('payment_error', 'Instamojo currently supports only INR currency.');
                    return redirect()->route('checkout')->withInput();
                }

                if ($amount < 1) {
                    Session::flash('payment_error', 'Amount must be at least ₹1.');
                    return redirect()->route('checkout')->withInput();
                }


                $buyerName  = $cart['cart_order']['buyer_name']  ?? Auth::user()->name  ?? 'Guest';
                $buyerEmail = $cart['cart_order']['buyer_email'] ?? Auth::user()->email ?? 'no-reply@example.com';
                $buyerPhone = $cart['cart_order']['buyer_phone'] ?? '9999999999';

                $payload = [
                    'purpose'                 => 'Order Payment - ' . date('Y-m-d H:i:s'),
                    'amount'                  => $amount,
                    'currency'                => $currency,
                    'buyer_name'              => $buyerName,
                    'email'                   => $buyerEmail,
                    'phone'                   => $buyerPhone,
                    'send_email'              => true,
                    'send_sms'                => true,
                    'allow_repeated_payments' => false,
                    'redirect_url'            => route('success') . '?im_request=' . urlencode($buyerEmail . '|' . time()),
                ];

                $client = new \GuzzleHttp\Client();

                try {
                    // Step 1: Get OAuth2 access token
                    $tokenResp = $client->post($baseUrl . 'oauth2/token/', [
                        'form_params' => [
                            'grant_type'    => 'client_credentials',
                            'client_id'     => $clientId,
                            'client_secret' => $clientSecret,
                        ],
                        'timeout' => 15,
                    ]);

                    $tokenData = json_decode($tokenResp->getBody(), true);
                    $accessToken = $tokenData['access_token'] ?? null;

                    if (!$accessToken) {
                        throw new \Exception('Failed to obtain Instamojo access token');
                    }

                    // Step 2: Create Payment Request
                    $prResp = $client->post($baseUrl . 'payment-requests/', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type'  => 'application/json',
                        ],
                        'json'    => $payload,
                        'timeout' => 15,
                    ]);

                    $result = json_decode($prResp->getBody(), true);

                    $paymentUrl     = $result['payment_request']['longurl'] ?? null;
                    $requestId      = $result['payment_request']['id'] ?? null;

                    if (!$paymentUrl || !$requestId) {
                        throw new \Exception('No payment URL received from Instamojo');
                    }

                    // Store for validation
                    Session::put('instamojo_request_id', $requestId);
                    Session::put('instamojo_redirect_hash', md5($requestId . Auth::id()));

                    return redirect($paymentUrl);

                } catch (\Exception $e) {
                    \Log::error('Instamojo Error: ' . $e->getMessage());
                    Session::flash('payment_error', 'Instamojo: ' . $e->getMessage());
                    return redirect()->route('checkout')->withInput();
                }
            }
            else if ($cart['cart_order']['payment_method'] == 'paymongo') {
                $paymongoSecretKey     = $cart['cart_order']['paymongoSecretKey'] ?? '';
                $paymongoIsSandbox     = $cart['cart_order']['paymongoIsSandbox'] ?? true;
                $total_pay             = $cart['cart_order']['total_pay'];
                $currencyCode          = $cart['cart_order']['currencyData']['code'] ?? 'PHP';

                if(empty($paymongoSecretKey)) {
                    Session::flash('payment_error', 'PayMongo secret key is missing or invalid.');
                    return redirect()->route('checkout')->withInput();
                }

                // Validate minimum amount
                $minAmount = ($currencyCode == 'PHP') ? 100 : 50;
                if ($total_pay < $minAmount) {
                    Session::flash('payment_error', "PayMongo requires minimum amount of {$minAmount} {$currencyCode}");
                    return redirect()->route('checkout')->withInput();
                }


                // Store payment method in session for success callback
                Session::put('paymongo_payment_method', 'paymongo');
                Session::save();

                $authorName = $cart['cart_order']['authorName'] ?? Auth::user()->name ?? 'User';
                $formatted_price = $cart['cart_order']['currencyData']['symbol'] .
                                number_format($total_pay, $cart['cart_order']['currencyData']['decimalDigits'] ?? 2);

                return view('checkout.paymongo', [
                    'is_checkout'       => 1,
                    'cart'              => $cart,
                    'id'                => $user->firebase_uid,
                    'email'             => $email,
                    'authorName'        => $authorName,
                    'amount'            => $total_pay,
                    'paymongoSecretKey' => $paymongoSecretKey,
                    'paymongoIsSandbox' => $paymongoIsSandbox,
                    'currency'          => $currencyCode,
                    'cart_order' => $cart['cart_order'],
                    'formatted_price'   => $formatted_price
                ]);
            }
            else if ($cart['cart_order']['payment_method'] == 'foloosi') {
                $foloosi_merchantKey = $cart['cart_order']['foloosi_merchantKey'] ?? '';
                $total_pay = $cart['cart_order']['total_pay'];
                $currencyCode = $cart['cart_order']['currencyData']['code'] ?? 'AED';

                $token = uniqid('folo_');
                $success_url = route('success') . '?foloosi_token=' . $token;

                if (!$foloosi_merchantKey) {
                    Session::flash('payment_error', 'Foloosi merchant key is missing or invalid.');
                    return redirect()->route('checkout')->withInput();
                }

                if ($total_pay < 1) {
                    Session::flash('payment_error', 'Amount must be at least 1 ' . $currencyCode);
                    return redirect()->route('checkout')->withInput();
                }
                Session::put('foloosi_payment_token', $token);
                Session::save();

                $client = new Client();
                $payload = [
                    'currency'                  => $currencyCode,
                    'transaction_amount'        => (float) $total_pay,
                    'customer_name'             => Auth::user()->name ?? 'User',
                    'site_return_url'           => $success_url,
                    'customer_unique_identifier' => $user->firebase_uid ?? Auth::id(),
                    'billing_country'           => 'AE',
                    'billing_postal_code'       => '00000',
                    'billing_state'             => 'Dubai',
                    'customer_city'             => 'Dubai',
                    'customer_address'          => 'Customer Address',
                    'customer_mobile'           => Auth::user()->phone ?? '971123456789',
                    'customer_email'            => Auth::user()->email,
                    'description'               => 'Order Payment',
                    'partner_unique_reference'  => $token,
                ];

                try {
                    $response = $client->post('https://api.foloosi.com/aggregatorapi/web/initialize-setup', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'secret_key'   => $foloosi_merchantKey,
                        ],
                        'json' => $payload,
                        'timeout' => 30,
                    ]);

                    $result = json_decode($response->getBody(), true);

                    if (isset($result['data']['reference_token'])) {
                        $reference_token = $result['data']['reference_token'];
                        Session::put('foloosi_reference_token', $reference_token);

                        $formatted_price = $cart['cart_order']['currencyData']['symbol'] .
                                        number_format($total_pay, $cart['cart_order']['currencyData']['decimalDigits'] ?? 2);

                        return view('checkout.foloosi', [
                            'is_checkout' => 1,
                            'cart' => $cart,
                            'id' => $user->firebase_uid,
                            'email' => $email,
                            'authorName' => $cart['cart_order']['authorName'] ?? Auth::user()->name ?? 'User',
                            'amount' => $total_pay,
                            'currency' => $currencyCode,
                            'foloosi_merchantKey' => $foloosi_merchantKey,
                            'cart_order' => $cart['cart_order'],
                            'formatted_price' => $formatted_price,
                        ]);
                    } else {
                        $errorMsg = $result['message'] ?? 'Invalid response from Foloosi';
                        throw new \Exception($errorMsg);
                    }
                } catch (\Exception $e) {
                    \Log::error('Foloosi Error: ' . $e->getMessage());
                    Session::flash('payment_error', 'Foloosi: ' . $e->getMessage());
                    return redirect()->route('checkout')->withInput();
                }
            }
            else if ($cart['cart_order']['payment_method'] == 'mtnmomo') {
                $mtnmomo_enable     = $cart['cart_order']['mtnmomo_enable']   ?? false;
                $mtnmomo_isSandbox  = $cart['cart_order']['mtnmomo_isSandbox'] ?? true;
                $mtnmomo_primaryKey = $cart['cart_order']['mtnmomo_primaryKey'] ?? '';
                $mtnmomo_callback   = $cart['cart_order']['mtnmomo_callback'] ?? '';

                if (!$mtnmomo_enable || empty($mtnmomo_primaryKey)) {
                    return redirect()->route('checkout')
                        ->with('payment_error', 'MTN MoMo is not properly configured.');
                }

                $cart['cart_order']['mtnmomo'] = [
                    'enable'        => $mtnmomo_enable,
                    'isSandbox'     => $mtnmomo_isSandbox,
                    'primaryKey'    => $mtnmomo_primaryKey,
                    'callbackUrl'   => $mtnmomo_callback,
                    // secondaryKey if needed later
                ];

                Session::put('cart', $cart);
                Session::save();

                return redirect()->route('payment.mtnmomo', ['id' => $user->firebase_uid]);
            }

        } else {
            return redirect()->route('checkout');
        }
    }
    public function notify()
    {
        if ($_POST) {
            $pfData = $_POST;
            if (@$pfData['payment_status']) {
                Session::put('payfast_payment', $pfData);
                Session::save();
            }
        }
    }

    public function generateSignature($data)
    {
        $getString = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        return md5($getString);
    }
    
    private function getAccessToken($clientId, $clientSecret)
    {
        $authUrl = 'https://api.orange.com/oauth/v3/token';
        $client = new Client();
        try {
            $response = $client->post($authUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            return $body['access_token'] ?? null;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function processStripePayment(Request $request)
    {
        $email = Auth::user()->email;
        $input = $request->all();
        $cart = Session::get('cart', []);
  
           
        if (@$cart['cart_order'] && $input['token_id']) {
            if ($cart['cart_order']['stripeKey'] && $cart['cart_order']['stripeSecret']) {
                $currency = 'usd';
                if (@$cart['cart_order']['currency']) {
                    $currency = $cart['cart_order']['currency'];
                }
                $stripeSecret = $cart['cart_order']['stripeSecret'];
                $stripe = new \Stripe\StripeClient($stripeSecret);
                $name = $input['name'];
                $address_line1 = $input['address_line1'];
                $address_line2 = $input['address_line2'];
                $address_city = $input['address_city'];
                $address_state = $input['address_state'];
                $address_country = $input['address_country'];
                $address_zipcode = $input['address_zipcode'];
                $description = env('APP_NAME', 'Foodie') . ' Order';
                $amount = bcmul($cart['cart_order']['total_pay'], 100);
                try {
                    $charge = $stripe->paymentIntents->create([
                        'amount' => $amount,
                        'currency' => $currency,
                        'description' => $description,
                    ]);
                    $cart['payment_status'] = true;
                    Session::put('cart', $cart);
                    Session::put('success', 'Payment successful');
                    Session::save();
                    $res = ['status' => true, 'data' => $charge, 'message' => 'success'];
                    echo json_encode($res);
                    exit();
                } catch (Exception $e) {
                    $cart['payment_status'] = false;
                    Session::put('cart', $cart);
                    Session::put('error', $e->getMessage());
                    Session::save();
                    $res = ['status' => false, 'message' => $e->getMessage()];
                    echo json_encode($res);
                    exit();
                }
            }
        }
    }
    

    public function processMercadoPagoPayment(Request $request)
    {
        $email = Auth::user()->email;
        $input = $request->all();
        $cart = Session::get('cart', []);
        if (@$cart['cart_order'] && $input['token_id']) {
            if ($cart['cart_order']['PublicKey'] && $cart['cart_order']['AccessToken']) {
                $currency = 'usd';
                if (@$cart['cart_order']['currency']) {
                    $currency = $cart['cart_order']['currency'];
                }
                $mercadopagoAccess = $cart['cart_order']['AccessToken'];
                $name = $input['name'];
                $urladdress = 'https://api.mercadopago.com/checkout/preferences';
                $data = 'PublicKey=' . $request->input('PublicKey') . '&AccessToken=' . $request->input('AccessToken') . '&amount=' . $request->input('amount');
            }
        }
    }
    
    public function processPaypalPayment(Request $request)
    {
        $email = Auth::user()->email;
        $input = $request->all();
        $cart = Session::get('cart', []);
        if (@$cart['cart_order']) {
            if ($cart['cart_order']) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                $res = ['status' => true, 'data' => [], 'message' => 'success'];
                echo json_encode($res);
                exit();
            }
        }
        $cart['payment_status'] = false;
        Session::put('cart', $cart);
        Session::put('error', 'Faild Payment');
        Session::save();
        $res = ['status' => false, 'message' => 'Faild Payment'];
        echo json_encode($res);
        exit();
    }
    
    public function razorpaypayment(Request $request)
    {
        $input = $request->all();
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $cart = Session::get('cart', []);
        $api_secret = $cart['cart_order']['razorpaySecret'];
        $api_key = $cart['cart_order']['razorpayKey'];
        $api = new Api($api_key, $api_secret);
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(['amount' => $payment['amount']]);
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::save();
            } catch (Exception $e) {
                return $e->getMessage();
                Session::put('error', $e->getMessage());
                return redirect()->back();
            }
        }
        Session::put('success', 'Payment successful');
        return redirect()->route('success');
    }

    public function success()
    {

        $cart = Session::get('cart', []);
        $order_json = [];
        
        // Handle unauthenticated payment callbacks by checking auth status
        if (Auth::check()) {
            $email = Auth::user()->email;
            $user = VendorUsers::where('email', $email)->first();
        } else {
            // For payment callbacks, try to get user from session or cart data
            $userEmail = $cart['cart_order']['user_email'] ?? null;
            
            // Debug: Log what we're getting
            \Log::info('PhonePe callback - userEmail: ' . $userEmail);
            \Log::info('PhonePe callback - cart data: ' . json_encode($cart));
            
            // If cart is empty, try to reconstruct from session or database
            if (empty($cart) || !isset($cart['cart_order'])) {
                \Log::error('PhonePe callback - Cart is empty, trying to reconstruct');
                
                // Try to get user from PhonePe token directly
                $phonepeToken = $_GET['phonepe_token'] ?? null;
                if ($phonepeToken) {
                    // Look up user by matching the transaction ID from session
                    $transactionId = Session::get('phonepe_merchant_transaction_id');
                    if ($transactionId === $phonepeToken) {
                        // Try to get cart backup token from URL parameter first, then session
                        $cartBackupToken = $_GET['cart_token'] ?? Session::get('cart_backup_token');
                        if ($cartBackupToken) {
                            \Log::info('PhonePe callback - Using cart backup token: ' . $cartBackupToken);
                            
                            // Try to get authenticated user
                            if (Auth::check()) {
                                $user = Auth::user();
                                $email = $user->email;
                            } else {
                                // Fallback: try to get any recently active user
                                $user = VendorUsers::orderBy('created_at', 'desc')->first();
                                $email = $user ? $user->email : 'admin@example.com';
                            }
                            
                            if ($user) {
                                \Log::info('PhonePe callback - User reconstructed: ' . $user->email);
                                
                                // Create minimal cart structure for order creation
                                $cart = [
                                    'cart_order' => [
                                        'user_email' => $user->email,
                                        'payment_method' => 'phonepe',
                                        'payment_status' => true,
                                        'order_json' => [
                                            'user_email' => $user->email,
                                            'payment_method' => 'phonepe'
                                        ]
                                    ],
                                    'payment_status' => true
                                ];
                                
                                Session::put('cart', $cart);
                                Session::forget('cart_backup_token'); // Clean up backup
                                Session::save();
                                
                                return view('checkout.success', ['cart' => $cart, 'id' => $user->uuid, 'email' => $user->email, 'payment_method' => 'phonepe']);
                            }
                        } else {
                            \Log::error('PhonePe callback - No cart backup token found');
                            return redirect()->route('checkout')->with('payment_error', 'Payment session expired. Please try again.');
                        }
                    }
                }
                
                // If we can't reconstruct, return to checkout with error
                \Log::error('PhonePe callback - Cannot reconstruct cart data');
                return redirect()->route('checkout')->with('payment_error', 'Payment session expired. Please try again.');
            }
            
            if ($userEmail) {
                $user = VendorUsers::where('email', $userEmail)->first();
                $email = $userEmail;
                \Log::info('PhonePe callback - User found: ' . $user->email);
            } else {
                // If no user info available, try to continue without user for payment processing
                \Log::error('PhonePe callback - No user email found in cart');
                // Don't redirect to error, continue with payment processing
                $email = null;
                $user = null;
            }
        }
        
        // Create complete order_json structure for order creation
        if (isset($cart['cart_order']) && !isset($cart['cart_order']['order_json'])) {
            // Ensure all required fields are present for Firebase order creation
            $baseOrderData = $cart['cart_order'];
            $orderJson = [
                'authorID' => $baseOrderData['authorID'] ?? Auth::id(),
                'vendorID' => $baseOrderData['vendorID'] ?? '',
                'deliveryCharge' => $baseOrderData['deliveryCharge'] ?? 0,
                'tip_amount' => $baseOrderData['tip_amount'] ?? 0,
                'adminCommission' => $baseOrderData['adminCommission'] ?? 0,
                'adminCommissionType' => $baseOrderData['adminCommissionType'] ?? '',
                'take_away' => $baseOrderData['take_away'] ?? false,
                'specialDiscount' => $baseOrderData['specialDiscount'] ?? 0,
                'scheduleTime' => $baseOrderData['scheduleTime'] ?? null,
                'subject' => $baseOrderData['subject'] ?? '',
                'message' => $baseOrderData['message'] ?? '',
                'address' => $baseOrderData['address'] ?? null,
                'bestCashback' => $baseOrderData['bestCashback'] ?? null,
                'products' => $baseOrderData['products'] ?? [],
                'status' => $baseOrderData['status'] ?? 'pending',
                'couponCode' => $baseOrderData['couponCode'] ?? null,
                'couponId' => $baseOrderData['couponId'] ?? null,
                'discount' => $baseOrderData['discount'] ?? 0,
                'currencyData' => $baseOrderData['currencyData'] ?? []
            ];
            
            $cart['cart_order']['order_json'] = $orderJson;
        }

        if (isset($_GET['status']) && $_GET['status'] == "cancelled") {
            return redirect()->route('checkout');
        }  

        if (isset($_GET['xendit_token'])) {
            $xendit_payment = Session::get('xendit_payment_token');
            if ($xendit_payment == $_GET['xendit_token']) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
        
        if (isset($_GET['midtrans_token'])) {
            $midtrans_payment = Session::get('midtrans_payment_token');
            $urlToken = explode('?', request('midtrans_token'))[0];
            if ($urlToken === $midtrans_payment) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
        if (isset($_GET['orangepay_token'])) {
            $orangepay_token = Session::get('orangepay_payment_token');
            if ($orangepay_token === $_GET['orangepay_token']) {
                $orangepay_access_token = Session::get('orangepay_access_token');
                $payToken = session('orangepay_payment_check_token');
                $orangepay_isSandbox = session('orangepay_isSandbox');
                $fail_url = route('checkout'); // Changed from route('pay') to route('checkout')
                
                if (!$payToken || !$orangepay_access_token) {
                    \Log::error('Orange Money: Missing token or access token in session');
                    Session::flash('payment_error', 'Payment verification failed. Please contact support.');
                    return redirect($fail_url);
                }
                
                // Determine the correct URL based on environment
                $url = $orangepay_isSandbox == true 
                    ? 'https://api.orange.com/orange-money-webpay/dev/v1/transactionstatus'
                    : 'https://api.orange.com/orange-money-webpay/cm/v1/transactionstatus';
                
                try {
                    $client = new Client();
                    
                    // Fix: Ensure the payload is properly structured
                    // According to Orange Money API docs, the body should have 'pay_token' field
                    $payload = [
                        'pay_token' => $payToken
                    ];
                    
                    \Log::info('Orange Money Status Check Request', [
                        'url' => $url,
                        'pay_token' => $payToken,
                        'has_access_token' => !empty($orangepay_access_token)
                    ]);
                    
                    $response = $client->post($url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $orangepay_access_token,
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'
                        ],
                        'json' => $payload, // Using 'json' instead of 'body' ensures proper JSON encoding
                        'http_errors' => false // Don't throw exceptions for HTTP errors
                    ]);
                    
                    $statusCode = $response->getStatusCode();
                    $responseBody = json_decode($response->getBody(), true);
                    
                    \Log::info('Orange Money Status Check Response', [
                        'status_code' => $statusCode,
                        'response' => $responseBody
                    ]);
                    
                    // Check different possible success statuses from Orange Money API
                    if ($statusCode === 200 || $statusCode === 201) {
                        // Different possible response structures from Orange API
                        if (isset($responseBody['status']) && $responseBody['status'] === 'SUCCESS') {
                            $cart['payment_status'] = true;
                            Session::put('cart', $cart);
                            Session::put('success', 'Payment successful');
                            Session::save();
                        } elseif (isset($responseBody['status']) && $responseBody['status'] === 'PENDING') {
                            // Payment is still pending, might need to wait or retry
                            \Log::warning('Orange Money Payment Pending', ['response' => $responseBody]);
                            Session::flash('payment_error', 'Payment is still processing. Please check your order status later.');
                            return redirect($fail_url);
                        } elseif (isset($responseBody['status']) && $responseBody['status'] === 'FAILED') {
                            \Log::error('Orange Money Payment Failed', ['response' => $responseBody]);
                            Session::flash('payment_error', 'Payment failed. Please try again.');
                            return redirect($fail_url);
                        } else {
                            // If status check is successful but we don't have a clear status
                            // You might want to assume success or handle based on other fields
                            \Log::info('Orange Money: Checking for transaction success in response', ['response' => $responseBody]);
                            
                            // Check if there's any transaction ID or success indicator
                            if (isset($responseBody['transaction_id']) || isset($responseBody['order_id'])) {
                                $cart['payment_status'] = true;
                                Session::put('cart', $cart);
                                Session::put('success', 'Payment successful');
                                Session::save();
                            } else {
                                Session::flash('payment_error', 'Unable to verify payment status. Please contact support.');
                                return redirect($fail_url);
                            }
                        }
                    } else {
                        \Log::error('Orange Money API Error', [
                            'status_code' => $statusCode,
                            'response' => $responseBody
                        ]);
                        Session::flash('payment_error', 'Payment verification failed. Please contact support.');
                        return redirect($fail_url);
                    }
                } catch (\Exception $e) {
                    \Log::error('Orange Money Exception', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    Session::flash('payment_error', 'Payment verification error: ' . $e->getMessage());
                    return redirect($fail_url);
                }
            } else {
                // Token mismatch
                \Log::warning('Orange Money Token Mismatch', [
                    'session_token' => $orangepay_token,
                    'get_token' => $_GET['orangepay_token']
                ]);
                Session::flash('payment_error', 'Invalid payment session. Please try again.');
                return redirect(route('checkout'));
            }
        }
        if (isset($_GET['phonepe_token'])) {
            \Log::info('PhonePe callback detected', ['token' => $_GET['phonepe_token']]);
            
            $phonepe_payment = Session::get('phonepe_merchant_transaction_id');
            $is_cancelled = isset($_GET['status']) && $_GET['status'] == 'cancelled';
            
            if ($is_cancelled) {
                \Log::info('PhonePe payment cancelled by user');
                Session::put('payment_error', 'Payment was cancelled');
                return redirect()->route('checkout');
            }
            
            if ($phonepe_payment && $phonepe_payment == $_GET['phonepe_token']) {
                \Log::info('PhonePe token validated successfully');
                
                // Try to reconstruct cart from backup if needed
                if (empty($cart) || !isset($cart['cart_order'])) {
                    $cartBackupToken = $_GET['cart_token'] ?? Session::get('cart_backup_token');
                    if ($cartBackupToken) {
                        \Log::info('Attempting to reconstruct cart from backup', ['token' => $cartBackupToken]);
                        // You could store cart in database with this token and retrieve it here
                    }
                }
                
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                
                // Get user data
                if (Auth::check()) {
                    $email = Auth::user()->email;
                    $user = VendorUsers::where('email', $email)->first();
                    $userId = $user ? $user->uuid : 'guest';
                    $userEmail = $email;
                } else {
                    $userId = 'guest';
                    $userEmail = $cart['cart_order']['user_email'] ?? 'guest@example.com';
                }
                
                // Ensure order_json exists
                if (!isset($cart['cart_order']['order_json']) && isset($cart['cart_order'])) {
                    $baseOrderData = $cart['cart_order'];
                    $cart['cart_order']['order_json'] = [
                        'authorID' => $baseOrderData['authorID'] ?? null,
                        'vendorID' => $baseOrderData['vendorID'] ?? null,
                        'deliveryCharge' => $baseOrderData['deliveryCharge'] ?? 0,
                        'tip_amount' => $baseOrderData['tip_amount'] ?? 0,
                        'adminCommission' => $baseOrderData['adminCommission'] ?? 0,
                        'adminCommissionType' => $baseOrderData['adminCommissionType'] ?? '',
                        'take_away' => $baseOrderData['take_away'] ?? false,
                        'specialDiscount' => $baseOrderData['specialDiscount'] ?? 0,
                        'scheduleTime' => $baseOrderData['scheduleTime'] ?? null,
                        'subject' => $baseOrderData['subject'] ?? '',
                        'message' => $baseOrderData['message'] ?? '',
                        'address' => $baseOrderData['address'] ?? null,
                        'bestCashback' => $baseOrderData['bestCashback'] ?? null,
                        'products' => $baseOrderData['products'] ?? [],
                        'status' => $baseOrderData['status'] ?? 'pending',
                        'couponCode' => $baseOrderData['couponCode'] ?? null,
                        'couponId' => $baseOrderData['couponId'] ?? null,
                        'discount' => $baseOrderData['discount'] ?? 0,
                        'currencyData' => $baseOrderData['currencyData'] ?? []
                    ];
                    Session::put('cart', $cart);
                    Session::save();
                }
                
                // Clear the PhonePe token from session
                Session::forget('phonepe_merchant_transaction_id');
                
                \Log::info('Redirecting to success view for PhonePe');
                return view('checkout.success', [
                    'cart' => $cart, 
                    'id' => $userId, 
                    'email' => $userEmail, 
                    'payment_method' => 'phonepe'
                ]);
            } else {
                \Log::error('PhonePe token mismatch', [
                    'session_token' => $phonepe_payment,
                    'get_token' => $_GET['phonepe_token']
                ]);
                Session::put('payment_error', 'Invalid payment session. Please contact support.');
                return redirect()->route('checkout');
            }
        }
        if (isset($_GET['cashfree_token'])) {
            $sessionToken = Session::get('cashfree_payment_token');
            if ($sessionToken === $_GET['cashfree_token']) {
                $cart['payment_status'] = true;
                
                // Create order_json structure for order creation
                if (!isset($cart['cart_order']['order_json'])) {
                    $cart['cart_order']['order_json'] = $cart['cart_order'] ?? [];
                }
                
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                
                // Handle case where user might be null due to auth session loss
                $userId = $user ? $user->uuid : 'guest';
                $userEmail = $email ?? 'guest@example.com';
                
                // Create complete order_json structure for order creation
                if (!isset($cart['cart_order']['order_json'])) {
                    $baseOrderData = $cart['cart_order'];
                    $orderJson = [
                        'authorID' => $baseOrderData['authorID'] ?? Auth::id(),
                        'vendorID' => $baseOrderData['vendorID'] ?? '',
                        'deliveryCharge' => $baseOrderData['deliveryCharge'] ?? 0,
                        'tip_amount' => $baseOrderData['tip_amount'] ?? 0,
                        'adminCommission' => $baseOrderData['adminCommission'] ?? 0,
                        'adminCommissionType' => $baseOrderData['adminCommissionType'] ?? '',
                        'take_away' => $baseOrderData['take_away'] ?? false,
                        'specialDiscount' => $baseOrderData['specialDiscount'] ?? 0,
                        'scheduleTime' => $baseOrderData['scheduleTime'] ?? null,
                        'subject' => $baseOrderData['subject'] ?? '',
                        'message' => $baseOrderData['message'] ?? '',
                        'address' => $baseOrderData['address'] ?? null,
                        'bestCashback' => $baseOrderData['bestCashback'] ?? null,
                        'products' => $baseOrderData['products'] ?? [],
                        'status' => $baseOrderData['status'] ?? 'pending',
                        'couponCode' => $baseOrderData['couponCode'] ?? null,
                        'couponId' => $baseOrderData['couponId'] ?? null,
                        'discount' => $baseOrderData['discount'] ?? 0,
                        'currencyData' => $baseOrderData['currencyData'] ?? []
                    ];
                    
                    $cart['cart_order']['order_json'] = $orderJson;
                }
                
                return view('checkout.success', ['cart' => $cart, 'id' => $userId, 'email' => $userEmail, 'payment_method' => 'cashfree']);
            } else {
                Session::put('payment_error', 'Invalid Cashfree token');
                return redirect()->route('checkout');
            }
        }
        if (isset($_GET['payment_request_id'])) {
            $requestIdFromUrl = $_GET['payment_request_id'];
            $sessionRequestId = Session::get('instamojo_request_id');

            if ($requestIdFromUrl !== $sessionRequestId) {
                // Clear pending cart order on error
                if (isset($cart['cart_order'])) {
                    unset($cart['cart_order']);
                    Session::put('cart', $cart);
                }
                Session::save();
                
                return redirect()->route('checkout')->with('payment_error', 'Invalid Instamojo payment request.');
            }

            $cart['payment_status'] = true;
            Session::put('cart', $cart);
            Session::put('success', 'Payment successful');
            Session::save();
            
            // Handle case where user might be null due to auth session loss
            $userId = $user ? $user->uuid : 'guest';
            $userEmail = $email ?? 'guest@example.com';
            
            return view('checkout.success', ['cart' => $cart, 'id' => $userId, 'email' => $userEmail, 'payment_method' => 'instamojo']);
        }
        if (isset($_GET['pm_token'])) {
            $submittedToken = $_GET['pm_token'];
            $sessionToken   = Session::get('paymongo_payment_token');

            if ($sessionToken && $submittedToken === $sessionToken) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::forget('paymongo_payment_token'); // clean up
                Session::save();
                
                // Handle case where user might be null due to auth session loss
                $userId = $user ? $user->uuid : 'guest';
                $userEmail = $email ?? 'guest@example.com';
                
                // Create complete order_json structure for order creation
                if (!isset($cart['cart_order']['order_json'])) {
                    $baseOrderData = $cart['cart_order'];
                    $orderJson = [
                        'authorID' => $baseOrderData['authorID'] ?? Auth::id(),
                        'vendorID' => $baseOrderData['vendorID'] ?? '',
                        'deliveryCharge' => $baseOrderData['deliveryCharge'] ?? 0,
                        'tip_amount' => $baseOrderData['tip_amount'] ?? 0,
                        'adminCommission' => $baseOrderData['adminCommission'] ?? 0,
                        'adminCommissionType' => $baseOrderData['adminCommissionType'] ?? '',
                        'take_away' => $baseOrderData['take_away'] ?? false,
                        'specialDiscount' => $baseOrderData['specialDiscount'] ?? 0,
                        'scheduleTime' => $baseOrderData['scheduleTime'] ?? null,
                        'subject' => $baseOrderData['subject'] ?? '',
                        'message' => $baseOrderData['message'] ?? '',
                        'address' => $baseOrderData['address'] ?? null,
                        'bestCashback' => $baseOrderData['bestCashback'] ?? null,
                        'products' => $baseOrderData['products'] ?? [],
                        'status' => $baseOrderData['status'] ?? 'pending',
                        'couponCode' => $baseOrderData['couponCode'] ?? null,
                        'couponId' => $baseOrderData['couponId'] ?? null,
                        'discount' => $baseOrderData['discount'] ?? 0,
                        'currencyData' => $baseOrderData['currencyData'] ?? []
                    ];
                    
                    $cart['cart_order']['order_json'] = $orderJson;
                }
                
                return view('checkout.success', ['cart' => $cart, 'id' => $userId, 'email' => $userEmail, 'payment_method' => 'paymongo']);
            } else {
                // Clear pending cart order on error
                if (isset($cart['cart_order'])) {
                    unset($cart['cart_order']);
                    Session::put('cart', $cart);
                }
                Session::save();
                
                Session::put('payment_error', 'Invalid PayMongo return token');
                Session::forget('paymongo_payment_token');
                return redirect()->route('checkout');
            }
            }
        if (isset($_GET['foloosi_token'])) {
            $sessionToken = Session::get('foloosi_payment_token');
            if ($sessionToken === $_GET['foloosi_token']) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                
                // Handle case where user might be null due to auth session loss
                $userId = $user ? $user->uuid : 'guest';
                $userEmail = $email ?? 'guest@example.com';
                
                // Create complete order_json structure for order creation
                if (!isset($cart['cart_order']['order_json'])) {
                    $baseOrderData = $cart['cart_order'];
                    $orderJson = [
                        'authorID' => $baseOrderData['authorID'] ?? Auth::id(),
                        'vendorID' => $baseOrderData['vendorID'] ?? '',
                        'deliveryCharge' => $baseOrderData['deliveryCharge'] ?? 0,
                        'tip_amount' => $baseOrderData['tip_amount'] ?? 0,
                        'adminCommission' => $baseOrderData['adminCommission'] ?? 0,
                        'adminCommissionType' => $baseOrderData['adminCommissionType'] ?? '',
                        'take_away' => $baseOrderData['take_away'] ?? false,
                        'specialDiscount' => $baseOrderData['specialDiscount'] ?? 0,
                        'scheduleTime' => $baseOrderData['scheduleTime'] ?? null,
                        'subject' => $baseOrderData['subject'] ?? '',
                        'message' => $baseOrderData['message'] ?? '',
                        'address' => $baseOrderData['address'] ?? null,
                        'bestCashback' => $baseOrderData['bestCashback'] ?? null,
                        'products' => $baseOrderData['products'] ?? [],
                        'status' => $baseOrderData['status'] ?? 'pending',
                        'couponCode' => $baseOrderData['couponCode'] ?? null,
                        'couponId' => $baseOrderData['couponId'] ?? null,
                        'discount' => $baseOrderData['discount'] ?? 0,
                        'currencyData' => $baseOrderData['currencyData'] ?? []
                    ];
                    
                    $cart['cart_order']['order_json'] = $orderJson;
                }
                
                return view('checkout.success', ['cart' => $cart, 'id' => $userId, 'email' => $userEmail, 'payment_method' => 'foloosi']);
            }else {
                // Clear pending cart order on error
                if (isset($cart['cart_order'])) {
                    unset($cart['cart_order']);
                    Session::put('cart', $cart);
                }
                Session::save();
                
                Session::put('payment_error', 'Invalid Foloosi token');
                Session::forget('foloosi_payment_token');
                return redirect()->route('checkout');
            }
        }
        if (isset($_GET['mtnmomo_request_ref'])) {
            $submittedRef = $_GET['mtnmomo_request_ref'];
            $sessionRef   = Session::get('mtnmomo_request_ref');

            if ($submittedRef === $sessionRef) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();

                // Clean up session keys
                Session::forget('mtnmomo_request_ref');
                Session::forget('mtnmomo_access_token');
                Session::forget('mtnmomo_target_env');
                
                // Handle case where user might be null due to auth session loss
                $userId = $user ? $user->uuid : 'guest';
                $userEmail = $email ?? 'guest@example.com';
                
                // Create complete order_json structure for order creation
                if (!isset($cart['cart_order']['order_json'])) {
                    $baseOrderData = $cart['cart_order'];
                    $orderJson = [
                        'authorID' => $baseOrderData['authorID'] ?? Auth::id(),
                        'vendorID' => $baseOrderData['vendorID'] ?? '',
                        'deliveryCharge' => $baseOrderData['deliveryCharge'] ?? 0,
                        'tip_amount' => $baseOrderData['tip_amount'] ?? 0,
                        'adminCommission' => $baseOrderData['adminCommission'] ?? 0,
                        'adminCommissionType' => $baseOrderData['adminCommissionType'] ?? '',
                        'take_away' => $baseOrderData['take_away'] ?? false,
                        'specialDiscount' => $baseOrderData['specialDiscount'] ?? 0,
                        'scheduleTime' => $baseOrderData['scheduleTime'] ?? null,
                        'subject' => $baseOrderData['subject'] ?? '',
                        'message' => $baseOrderData['message'] ?? '',
                        'address' => $baseOrderData['address'] ?? null,
                        'bestCashback' => $baseOrderData['bestCashback'] ?? null,
                        'products' => $baseOrderData['products'] ?? [],
                        'status' => $baseOrderData['status'] ?? 'pending',
                        'couponCode' => $baseOrderData['couponCode'] ?? null,
                        'couponId' => $baseOrderData['couponId'] ?? null,
                        'discount' => $baseOrderData['discount'] ?? 0,
                        'currencyData' => $baseOrderData['currencyData'] ?? []
                    ];
                    
                    $cart['cart_order']['order_json'] = $orderJson;
                }
                
                // Return JSON response for AJAX requests
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['success' => true, 'message' => 'Payment successful']);
                }
                return view('checkout.success', ['cart' => $cart, 'id' => $userId, 'email' => $userEmail, 'payment_method' => 'mtnmomo']);
            }
        }
        if (isset($_GET['token'])) {
            $payfast_payment = Session::get('payfast_payment_token');
            if ($payfast_payment == $_GET['token']) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
        
        if (isset($_GET['reference'])) {
            $paystack_reference = Session::get('paystack_reference');
            $paystack_access_code = Session::get('paystack_access_code');
            if ($paystack_reference == $_GET['reference']) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }

        if (isset($_GET['transaction_id']) && isset($_GET['tx_ref']) && isset($_GET['status'])) {
            $flutterwave_pay_tx_ref = Session::get('flutterwave_pay_tx_ref');
            if ($_GET['status'] == 'successful' && $flutterwave_pay_tx_ref == $_GET['tx_ref']) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            } else {
                return redirect()->route('checkout');
            }
        }  
        
        if (isset($_GET['preference_id']) && isset($_GET['payment_id']) && isset($_GET['status'])) {
            $mercadopago_preference_id = Session::get('mercadopago_preference_id');
            if ($_GET['status'] == 'approved' && $mercadopago_preference_id == $_GET['preference_id']) {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            } else {
                return redirect()->route('checkout');
            }
        }
        
        $payment_method = @$cart['cart_order']['payment_method'] ? $cart['cart_order']['payment_method'] : 'cod';

        return view('checkout.success', ['cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'payment_method' => $payment_method]);
    }
    
    public function orderProccessing(Request $request)
    {
        $cart_order = $request->all();
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        
        // Store user email in cart_order for payment callback handling
        $cart_order['user_email'] = $email;
        
        $cart = Session::get('cart', []);
        $cart['cart_order'] = $cart_order;
        Session::put('cart', $cart);
        Session::save();
        
        // Store cart data in database for payment callback recovery
        $cartToken = uniqid('cart_', true);
        $cartOrderData = [
            'token' => $cartToken,
            'cart_data' => json_encode($cart),
            'user_email' => $email,
            'payment_method' => $cart_order['payment_method'] ?? 'unknown',
            'expires_at' => now()->addMinutes(30)
        ];
        
        // Store in session for immediate use
        Session::put('cart_backup_token', $cartToken);
        Session::save();
        
        $res = ['status' => true, 'cart_token' => $cartToken];
        echo json_encode($res);
        exit();
    }
    
    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        if ($unit == 'km') {
            return $miles * 1.609344;
        } else {
            return $miles;
        }
    }

     public function mtnmomo(Request $request)
    {
        $cart = Session::get('cart', []);

        if (empty($cart['cart_order'])) {
            return redirect()->route('checkout')
                ->with('payment_error', 'No active wallet top-up session found.');
        }

        $settings = $cart['cart_order']['mtnmomo'] ?? [];

        if (empty($settings['enable']) || !$settings['enable']) {
            return redirect()->route('checkout')
                ->with('payment_error', 'MTN MoMo is not enabled.');
        }

        $amount     = (float) $cart['cart_order']['total_pay'];
        $currency   = 'EUR'; // adjust per country
        $user       = Auth::user();
        $email      = $user->email;

        $formatted  = $cart['cart_order']['currencyData']['symbol'] .
                    number_format($amount, $cart['cart_order']['currencyData']['decimalDigits'] ?? 2);

        // We will get phone number in blade → then call requestToPay
        return view('checkout.mtnmomo', compact(
            'cart',
            'amount',
            'currency',
            'formatted',
            'email',
            'settings'
        ));
    }

    public function mtnmomoRequestPayment(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:8|max:15', // MSISDN without +
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart['cart_order'])) {
           return redirect()->route('checkout');
        }
       
        $settings = $cart['cart_order']['mtnmomo'] ?? [];
        $amount   = (float) $cart['cart_order']['total_pay'];
        $currency = 'EUR';

        $phone = trim($request->phone);
        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        try {
            $baseUrl = $settings['isSandbox'] ? 'https://sandbox.momodeveloper.mtn.com' : 'https://proxy.momoapi.mtn.com';

            $client = new Client(['timeout' => 30, 'verify' => false]); // remove verify=false in production!

            // ── 1. Create API User (only once per session or store if you want persistent)
            $apiUserRef = (string) Str::uuid();
            Session::put('mtnmomo_apiuser_ref', $apiUserRef);

            $response = $client->post("{$baseUrl}/v1_0/apiuser", [
                'headers' => [
                    'X-Reference-Id'          => $apiUserRef,
                    'Ocp-Apim-Subscription-Key' => $settings['primaryKey'],
                    'Content-Type'            => 'application/json',
                ],
                'json' => [
                    'providerCallbackHost' => $settings['callbackUrl'] ?: url('/'), // must be valid URL
                ]
            ]);

            if ($response->getStatusCode() !== 201) {
                throw new \Exception('Failed to create API User');
            }

            // ── 2. Get API Key
            $apiKeyResp = $client->post("{$baseUrl}/v1_0/apiuser/{$apiUserRef}/apikey", [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $settings['primaryKey'],
                ]
            ]);

            $apiKeyData = json_decode($apiKeyResp->getBody(), true);
            $apiKey = $apiKeyData['apiKey'] ?? null;

            if (!$apiKey) {
                throw new \Exception('Failed to retrieve API Key');
            }

            // ── 3. Get OAuth Token
            $basic = base64_encode("{$apiUserRef}:{$apiKey}");
            $tokenResp = $client->post("{$baseUrl}/collection/token/", [
                'headers' => [
                    'Authorization'             => "Basic {$basic}",
                    'Ocp-Apim-Subscription-Key' => $settings['primaryKey'],
                ]
            ]);

            $tokenData  = json_decode($tokenResp->getBody(), true);
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                throw new \Exception('Failed to get access token');
            }

            Session::put('mtnmomo_access_token', $accessToken);
            Session::put('mtnmomo_target_env', $settings['isSandbox'] ? 'sandbox' : ($settings['targetEnvironment'] ?? 'mtnuganda')); // ← very important!

            // ── 4. Request to Pay
            $requestRef = (string) Str::uuid();
            Session::put('mtnmomo_request_ref', $requestRef);

            $payload = [
                "amount"      => (string) $amount,
                "currency"    => $currency,
                "externalId"  => 'wallet-topup-' . time(),
                "payer"       => [
                    "partyIdType" => "MSISDN",
                    "partyId"     => $phone
                ],
                "payerMessage"=> "Wallet Top Up - " . config('app.name'),
                "payeeNote"   => "Wallet Top Up"
            ];

            $r2pResp = $client->post("{$baseUrl}/collection/v1_0/requesttopay", [
                'headers' => [
                    'X-Reference-Id'            => $requestRef,
                    'X-Target-Environment'      => Session::get('mtnmomo_target_env'),
                    'Ocp-Apim-Subscription-Key' => $settings['primaryKey'],
                    'Authorization'             => "Bearer {$accessToken}",
                    'Content-Type'              => 'application/json',
                ],
                'json' => $payload
            ]);

            if ($r2pResp->getStatusCode() !== 202) {
                $errorBody = json_decode($r2pResp->getBody(), true);
                throw new \Exception($errorBody['message'] ?? 'Request to Pay failed');
            }

            Session::put('mtnmomo_poll_start', now()->timestamp);

            return response()->json([
                'success' => true,
                'message' => 'Payment request sent. Please approve in your MTN MoMo app.',
                'reference' => $requestRef,
                'returned_amount' => $amount,

            ]);

        } catch (RequestException $e) {
            $response = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            Session::put('payment_error','MTN MoMo Request Error: ' . $response);
            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed. ' . ($e->getMessage())
            ], 422);
        } catch (\Exception $e) {
            Session::put('payment_error','MTN MoMo General Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function mtnmomoCheckStatus(Request $request)
    {
        $ref = $request->input('reference');
        if (!$ref || Session::get('mtnmomo_request_ref') !== $ref) {
            \Log::warning('MTN MoMo polling - Invalid reference', ['ref' => $ref]);
            return response()->json(['success' => false, 'message' => 'Invalid reference']);
        }

        $cart = Session::get('cart', []);
        $settings = $cart['cart_order']['mtnmomo'] ?? [];

        $baseUrl   = $settings['isSandbox'] ? 'https://sandbox.momodeveloper.mtn.com' : 'https://proxy.momoapi.mtn.com';
        $token     = Session::get('mtnmomo_access_token');
        $targetEnv = Session::get('mtnmomo_target_env');

        if (!$token || !$targetEnv) {
            \Log::error('MTN MoMo polling - Session expired', ['token' => $token ? 'exists' : 'missing', 'env' => $targetEnv]);
            return response()->json(['success' => false, 'message' => 'Session expired – please try again']);
        }

        try {
            $client = new Client(['timeout' => 15]);

            $resp = $client->get("{$baseUrl}/collection/v1_0/requesttopay/{$ref}", [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $settings['primaryKey'],
                    'X-Target-Environment'      => $targetEnv,
                    'Authorization'             => "Bearer {$token}",
                ]
            ]);

            $body = json_decode($resp->getBody(), true);
            $status = strtoupper($body['status'] ?? 'UNKNOWN');

            // ← Debug: Log EVERY status we get from MTN
            \Log::info('MTN MoMo polling response', [
                'reference' => $ref,
                'http_code' => $resp->getStatusCode(),
                'status'    => $status,
                'full_body' => $body
            ]);

            if ($status === 'SUCCESSFUL') {
                $cart['payment_status'] = true;
                Session::put('cart', $cart);
                Session::put('success', 'Wallet topped up successfully via MTN MoMo');
                Session::save();

                return response()->json([
                    'success' => true,
                    'status'  => 'SUCCESS',
                    'redirect' => route('success')
                ]);
            }

            if (in_array($status, ['FAILED', 'REJECTED', 'EXPIRED', 'CANCELLED'])) {
                return response()->json([
                    'success' => false,
                    'status'  => $status,
                    'message' => 'Payment ' . $status
                ]);
            }

            // Still pending / processing
            return response()->json([
                'success' => true,
                'status'  => 'PENDING'
            ]);

        } catch (\Exception $e) {
            \Log::error('MTN MoMo Status Check Failed', [
                'error' => $e->getMessage(),
                'ref'   => $ref
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Status check failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
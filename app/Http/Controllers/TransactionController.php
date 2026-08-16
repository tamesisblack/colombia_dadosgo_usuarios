<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorUsers;
use Illuminate\Support\Facades\Auth;
use Session;
use Razorpay\Api\Api;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\XenditSdkException;
use GuzzleHttp\Client;


class TransactionController extends Controller
{
    
    public function __construct()
    {
       $this->middleware('auth');
    }
 
    public function index()
    {
        return view('transactions.index');
    }

    public function proccesstopaywallet(Request $request)
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $user_wallet = Session::get('user_wallet', []);
        
        // Debug: Check if wallet data exists
        if (!$user_wallet || !isset($user_wallet['data'])) {
            Session::flash('payment_error', 'Wallet data not found. Please try again.');
            return redirect()->route('transactions');
        }
        
        if (!isset($user_wallet['data']['payment_method'])) {
            Session::flash('payment_error', 'Payment method not selected. Please try again.');
            return redirect()->route('transactions');
        }
        
        if ($user_wallet) {
            if ($user_wallet['data']['payment_method'] == 'razorpay') {
                $razorpaySecret = $user_wallet['data']['razorpaySecret'];
                $razorpayKey = $user_wallet['data']['razorpayKey'];
                $authorName = $user_wallet['user']['firstName'];
                $total_pay = $user_wallet['data']['amount'];
                return view('transactions.razorpay', ['is_checkout' => 1, 'user_wallet' => $user_wallet, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'razorpaySecret' => $razorpaySecret, 'razorpayKey' => $razorpayKey]);
            } else if ($user_wallet['data']['payment_method'] == 'payfast') {
                $payfast_merchant_key = $user_wallet['data']['payfast_merchant_key'];
                $payfast_merchant_id = $user_wallet['data']['payfast_merchant_id'];
                $payfast_isSandbox = $user_wallet['data']['payfast_isSandbox'];
                $payfast_return_url = route('wallet-success');
                $payfast_notify_url = route('wallet-notify');
                $payfast_cancel_url = route('pay-wallet');
                $authorName = $user_wallet['user']['firstName'];
                $total_pay = $user_wallet['data']['amount'];
                $currency = "USD";
                $decimal_digit = 2;
                if (@$user_wallet['data']['currencyData']['code']) {
                    $currency = $user_wallet['data']['currencyData']['code'];
                    $decimal_digit = $user_wallet['data']['currencyData']['decimal_degits'];
                }
                $formatted_price =  $currency.number_format($total_pay,$decimal_digit) ;
                $token = uniqid();
                Session::put('payfast_payment_token', $token);
                Session::save();
                $payfast_return_url = $payfast_return_url . '?token=' . $token;
                $amount = 0;
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
                    'item_name' => "Test",
                ];
                $signature = $this->generateSignature($data);  
                $data['signature'] = $signature; 
                $pfHost = $payfast_isSandbox == "true" ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
                return view('transactions.payfast', [
                    'amount'=>$amount,
                    'pfHost'=>$pfHost,
                    'data' => $data,
                    'payfast_merchant_key' => $payfast_merchant_key, 
                    'payfast_merchant_id' => $payfast_merchant_id,  
                    'payfast_return_url' => $payfast_return_url,
                    'payfast_notify_url' => $payfast_notify_url, 
                    'payfast_cancel_url' => $payfast_cancel_url,
                    'item_name' => "Test",
                    'formatted_price' => $formatted_price,
                ]);
            } else if ($user_wallet['data']['payment_method'] == 'paystack') {
                $paystack_public_key = $user_wallet['data']['paystack_public_key'];
                $paystack_secret_key = $user_wallet['data']['paystack_secret_key'];
                $paystack_isSandbox = $user_wallet['data']['paystack_isSandbox'];
                $userEmail = $user_wallet['user']['email'];
                $authorName = $user_wallet['user']['firstName'];
                $total_pay = $user_wallet['data']['amount'];
                \Paystack\Paystack::init($paystack_secret_key);
                $payment = \Paystack\Transaction::initialize([
                    'email' => $userEmail,
                    'amount' => (int)($total_pay * 100),
                    'callback_url' => route('wallet-success')
                ]);
                Session::put('paystack_authorization_url', $payment->authorization_url);
                Session::put('paystack_access_code', $payment->access_code);
                Session::put('paystack_reference', $payment->reference);
                Session::save();
                if ($payment->authorization_url) {
                    $script = "<script>window.location = '" . $payment->authorization_url . "';</script>";
                    echo $script;
                    exit;
                } else {
                    $script = "<script>window.location = '" . url('') . "';</script>";
                    echo $script;
                    exit;
                }
            } else if ($user_wallet['data']['payment_method'] == 'flutterwave') {
                $currency = "USD";
                if (@$user_wallet['data']['currencyData']['code']) {
                    $currency = $user_wallet['data']['currencyData']['code'];
                }
                $userEmail = $user_wallet['user']['email'];
                $flutterWave_secret_key = $user_wallet['data']['flutterWave_secret_key'];
                $flutterWave_public_key = $user_wallet['data']['flutterWave_public_key'];
                $flutterWave_isSandbox = $user_wallet['data']['flutterWave_isSandbox'];
                $flutterWave_encryption_key = $user_wallet['data']['flutterWave_encryption_key'];
                $authorName = $user_wallet['user']['firstName'];
                $total_pay = $user_wallet['data']['amount'];
                Session::put('flutterwave_pay', 1);
                Session::save();
                $token = uniqid();
                Session::put('flutterwave_pay_tx_ref', $token);
                Session::save();
                return view('transactions.flutterwave', ['is_checkout' => 1, 'user_wallet' => $user_wallet, 'id' => $user->uuid, 'email' => $userEmail, 'authorName' => $authorName, 'amount' => $total_pay, 'flutterWave_secret_key' => $flutterWave_secret_key, 'flutterWave_public_key' => $flutterWave_public_key, 'flutterWave_isSandbox' => $flutterWave_isSandbox, 'flutterWave_encryption_key' => $flutterWave_encryption_key, 'token' => $token, 'data' => $user_wallet['data'], 'currency' => $currency]);
            } else if ($user_wallet['data']['payment_method'] == 'mercadopago') {
                $currency = "USD";
                if (@$user_wallet['data']['currencyData']['code']) {
                    $currency = $user_wallet['data']['currencyData']['code'];
                }
                $mercadopago_public_key = $user_wallet['data']['mercadopago_public_key'];
                $mercadopago_access_token = $user_wallet['data']['mercadopago_access_token'];
                $mercadopago_isSandbox = $user_wallet['data']['mercadopago_isSandbox'];
                $mercadopago_isEnabled = $user_wallet['data']['mercadopago_isEnabled'];
                $id = $user_wallet['user']['id'];
                $total_pay = $user_wallet['data']['amount'];
                $items['title'] = $id;
                $items['quantity'] = 1;
                $items['unit_price'] = floatval($total_pay);
                $fields[] = $items;
                $item['items'] = $fields;
                $item['back_urls']['failure'] = route('pay-wallet');
                $item['back_urls']['pending'] = route('wallet-notify');
                $item['back_urls']['success'] = route('wallet-success');
                $item['auto_return'] = 'all';
                Session::put('mercadopago_pay', 1);
                Session::save();
                $url = "https://api.mercadopago.com/checkout/preferences";
                $data = array('Accept: application/json', 'Authorization:Bearer ' . $mercadopago_access_token);
                $post_data = json_encode($item);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization:Bearer " . $mercadopago_access_token));
                $response = curl_exec($ch);
                if ($response === false) {
                    $error = curl_error($ch);
                    curl_close($ch);
                    return redirect()->back()->with('error',
                        'Unable to initialize payment, credentials are invalid or not authorized. Please check credentials, environment (sandbox/live), and account region.'
                    );
                }
                curl_close($ch);
                $mercadopago = json_decode($response);
                if (!isset($mercadopago->id)) {
                    return redirect()->back()->with('error',
                        'Unable to initialize payment, credentials are invalid or not authorized. Please check credentials, environment (sandbox/live), and account region.'
                    );
                }
                Session::put('mercadopago_preference_id', $mercadopago->id);
                Session::save();
                if ($mercadopago_isSandbox == "true") {
                    $payment_url = $mercadopago->sandbox_init_point;
                } else {
                    $payment_url = $mercadopago->init_point;
                }
                echo "<script>location.href = '" . $payment_url . "';</script>";
                exit;
            } else if ($user_wallet['data']['payment_method'] == 'stripe') {
                $stripeKey = $user_wallet['data']['stripeKey'];
                $stripeSecret = $user_wallet['data']['stripeSecret'];
                $authorName = $user_wallet['user']['firstName'];
                $total_pay = $user_wallet['data']['amount'];
                $isStripeSandboxEnabled = $user_wallet['data']['isStripeSandboxEnabled'];
                return view('transactions.stripe', ['is_checkout' => 1, 'cart' => $user_wallet, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'stripeSecret' => $stripeSecret, 'stripeKey' => $stripeKey, 'data' => $user_wallet['data']]);
            } else if ($user_wallet['data']['payment_method'] == 'paypal') {
                $paypalSecret = $user_wallet['data']['paypalSecret'];
                $paypalKey = $user_wallet['data']['paypalKey'];
                $ispaypalSandboxEnabled = $user_wallet['data']['ispaypalSandboxEnabled'];
                $authorName = $user_wallet['user']['firstName'];
                $total_pay = $user_wallet['data']['amount'];
                return view('transactions.paypal', ['is_checkout' => 1, 'user_wallet' => $user_wallet, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'paypalSecret' => $paypalSecret, 'paypalKey' => $paypalKey, 'data' => $user_wallet['data']]);
            } else if ($user_wallet['data']['payment_method'] == 'xendit') {
                $xendit_enable = $user_wallet['data']['xendit_enable'];
                $xendit_apiKey = $user_wallet['data']['xendit_apiKey'];
                if (isset($xendit_enable) && $xendit_enable == true) {
                    $total_pay = $user_wallet['data']['amount'];
                    $currency = "USD";
                    $fail_url = route('pay-wallet');
                    $success_url = route('wallet-success');
                    Configuration::setXenditKey($xendit_apiKey);
                    $token = uniqid();
                    $success_url = $success_url . '?xendit_token=' . $token;
                    Session::put('xendit_payment_token', $token);
                    Session::save();
                    $apiInstance = new InvoiceApi();
                    $create_invoice_request = new CreateInvoiceRequest([
                        'external_id' => $token,
                        'description' => '#' . $token . ' Order place',
                        'amount' => (int)($total_pay) * 1000,
                        'invoice_duration' => 300,
                        'currency' => "IDR",
                        'success_redirect_url' => $success_url,
                        'failure_redirect_url' => $fail_url
                    ]);
                    try {
                        $result = $apiInstance->createInvoice($create_invoice_request);
                        return redirect($result['invoice_url']);
                    } catch (XenditSdkException $e) {
                        return response()->json([
                            'message' => 'Exception when calling InvoiceApi->createInvoice: ' . $e->getMessage(),
                            'error' => $e->getFullError(),
                        ], 500);
                    }
                }
            } else if ($user_wallet['data']['payment_method'] == 'midtrans') {
                $midtrans_enable = $user_wallet['data']['midtrans_enable'];
                $midtrans_serverKey = $user_wallet['data']['midtrans_serverKey'];
                $midtrans_isSandbox = $user_wallet['data']['midtrans_isSandbox'];
                if (isset($midtrans_enable) && isset($midtrans_serverKey) && $midtrans_enable == true) {
                    if ($midtrans_isSandbox == true)
                        $url = 'https://api.sandbox.midtrans.com/v1/payment-links';
                    else
                        $url = 'https://api.midtrans.com/v1/payment-links';
                    $total_pay = $user_wallet['data']['amount'];
                    $currency = "USD";
                    $fail_url = route('pay-wallet');
                    $success_url = route('wallet-success');
                    $token = uniqid();
                    $success_url = $success_url . '?midtrans_token=' . $token;
                    Session::put('midtrans_payment_token', $token);
                    Session::save();
                    $payload = [
                        'transaction_details' => [
                            'order_id' => $token,
                            'gross_amount' => (int)($total_pay) * 1000,
                        ],
                        'usage_limit' => 1,
                        'callbacks' => [
                            'error' => $fail_url,
                            'unfinish' => $fail_url,
                            'close' => $fail_url,
                            'finish' => $success_url,
                        ]
                    ];
                    try {
                        $client = new Client();
                        $response = $client->post($url, [
                            'headers' => [
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Basic ' . base64_encode($midtrans_serverKey)
                            ],
                            'body' => json_encode($payload)
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
            } else if ($user_wallet['data']['payment_method'] == 'orangepay') {
                $total_pay = $user_wallet['data']['amount'];
                $orangepay_enable = $user_wallet['data']['orangepay_enable'];
                $orangepay_isSandbox = $user_wallet['data']['orangepay_isSandbox'];
                Session::put('orangepay_isSandbox', $orangepay_isSandbox);
                Session::put('orangepay_access_token', $user_wallet['data']['orangepay_access_token']);
                Session::save();
                return redirect()->route('wallet.orangepay', ['id' => $user->uuid]);
            } else if ($user_wallet['data']['payment_method'] == 'phonepe') {
                $phonepe_enable = $user_wallet['data']['phonepe_enable'] ?? false;
                $isSandbox = filter_var($user_wallet['data']['phonepe_isSandbox'], FILTER_VALIDATE_BOOLEAN);
                $merchantId = $user_wallet['data']['phonepe_merchantId'] ?? '';
                $saltKey = $user_wallet['data']['phonepe_saltKey'] ?? '';
                $saltIndex = $user_wallet['data']['phonepe_saltIndex'] ?? '1';
                $amount = (float) $user_wallet['data']['amount'];

                if (!$phonepe_enable || empty($merchantId) || empty($saltKey)) {
                    Session::flash('payment_error', 'PhonePe is not properly configured. Check Merchant ID and Salt Key.');
                    return redirect()->route('transactions');
                }

                $merchantTransactionId = 'WT' . time() . rand(1000, 9999);
                
                Session::put('phonepe_merchant_transaction_id', $merchantTransactionId);
                Session::put('phonepe_salt_key', $saltKey);
                Session::put('phonepe_salt_index', $saltIndex);
                Session::put('phonepe_is_sandbox', $isSandbox);
                Session::put('phonepe_merchant_id', $merchantId);
                Session::save();

                $successUrl = route('wallet-success') . '?phonepe_token=' . $merchantTransactionId;
                $failUrl = route('transactions');
                
                $payloadArray = [
                    'merchantId' => $merchantId,
                    'merchantTransactionId' => $merchantTransactionId,
                    'merchantUserId' => Auth::id(),
                    'amount' => (int)($amount * 100),
                    'redirectUrl' => $successUrl,
                    'redirectMode' => 'REDIRECT',
                    'callbackUrl' => route('wallet-success'),
                    'paymentInstrument' => [
                        'type' => 'PAY_PAGE'
                    ]
                ];

                $encodedPayload = base64_encode(json_encode($payloadArray));
                $string = $encodedPayload . '/pg/v1/pay' . $saltKey;
                $sha256 = hash('sha256', $string);
                $XVerify = $sha256 . '###' . $saltIndex;

                $apiEndpoint = $isSandbox 
                    ? 'https://api-preprod.phonepe.com/apis/hermes/pg/v1/pay'
                    : 'https://api.phonepe.com/apis/hermes/pg/v1/pay';

                try {
                    $client = new Client(['timeout' => 30, 'verify' => false]);
                    $response = $client->post($apiEndpoint, [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'X-VERIFY' => $XVerify,
                            'X-ACCEPT' => 'application/json',
                        ],
                        'json' => [
                            'request' => $encodedPayload
                        ]
                    ]);

                    $result = json_decode($response->getBody(), true);

                    if (isset($result['success']) && $result['success'] === true && isset($result['data']['instrumentResponse']['redirectInfo']['url'])) {
                        Session::put('phonepe_pending_transaction', $merchantTransactionId);
                        Session::save();
                        return redirect($result['data']['instrumentResponse']['redirectInfo']['url']);
                    } else {
                        Session::flash('payment_error', $result['message'] ?? 'No payment URL received from PhonePe');
                        return redirect()->route('transactions');
                    }
                } catch (\Exception $e) {
                    \Log::error('PhonePe Error: ' . $e->getMessage());
                    Session::flash('payment_error', 'PhonePe Error: ' . $e->getMessage());
                    return redirect()->route('transactions');
                }
            }   else if ($user_wallet['data']['payment_method'] == 'cashfree') {
            $cashfree_enable = $user_wallet['data']['cashfree_enable'] ?? false;
            $isSandbox = filter_var($user_wallet['data']['cashfree_isSandbox'], FILTER_VALIDATE_BOOLEAN);
            $clientId = $user_wallet['data']['cashfree_clientId'] ?? '';
            $clientSecret = $user_wallet['data']['cashfree_clientSecret'] ?? '';
            $amount = (float) $user_wallet['data']['amount'];

            if (!$cashfree_enable || empty($clientId) || empty($clientSecret)) {
                Session::flash('payment_error', 'Cashfree credentials missing');
                return redirect()->route('transactions');
            }

            $authorName = $user_wallet['user']['firstName'] ?? Auth::user()->name ?? 'User';
            $email = Auth::user()->email;
            $phone = $user_wallet['user']['mobile'] ?? Auth::user()->phone ?? '9999999999';
            
            // Ensure phone is 10 digits for INR
            if (!preg_match('/^\d{10}$/', $phone)) {
                $phone = '9999999999';
            }

            $currency = 'INR'; // Cashfree only supports INR
            
            $token = uniqid('cf_wallet_');
            $success_url = route('wallet-success') . '?cashfree_token=' . $token;

            Session::put('cashfree_payment_token', $token);
            Session::put('cashfree_order_amount', $amount);
            Session::save();

            // Use Payment Links API (working pattern from CheckoutController)
            $baseUrl = $isSandbox 
                ? "https://sandbox.cashfree.com/pg/links"
                : "https://api.cashfree.com/pg/links";

            $payload = [
                'link_id' => $token,
                'link_amount' => (float)$amount,
                'link_currency' => strtoupper($currency),
                'link_purpose' => 'Wallet Top-up',
                'customer_details' => [
                    'customer_name' => $authorName,
                    'customer_email' => $email,
                    'customer_phone' => $phone,
                ],
                'link_success_url' => $success_url,
                'link_meta' => ['return_url' => $success_url],
                'link_expiry_time' => now()->addMinutes(30)->toIso8601String(),
            ];

            try {
                $client = new Client(['timeout' => 30, 'verify' => false]);
                
                $response = $client->post($baseUrl, [
                    'headers' => [
                        'x-client-id' => $clientId,
                        'x-client-secret' => $clientSecret,
                        'x-api-version' => '2025-01-01',
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => json_encode($payload),
                ]);

                $result = json_decode($response->getBody(), true);
                
                
                if (isset($result['link_url']) && $result['link_url']) {
                    return redirect($result['link_url']);
                }

                $errorMsg = $result['message'] ?? json_encode($result);
                Session::flash('payment_error', 'Cashfree link creation failed: ' . $errorMsg);
                return redirect()->route('transactions');

            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $responseBody = $e->hasResponse() ? (string) $e->getResponse()->getBody() : '';
                \Log::error('Cashfree Client Error', ['response' => $responseBody]);
                Session::flash('payment_error', 'Cashfree Error: ' . $responseBody);
                return redirect()->route('transactions');
            } catch (\Exception $e) {
                \Log::error('Cashfree Error', ['message' => $e->getMessage()]);
                Session::flash('payment_error', 'Cashfree Error: ' . $e->getMessage());
                return redirect()->route('transactions');
            }
        } else if ($user_wallet['data']['payment_method'] == 'instamojo') {
                $isSandbox = filter_var($user_wallet['data']['instamojo_isSandbox'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $clientId = $user_wallet['data']['instamojo_clientId'] ?? '';
                $clientSecret = $user_wallet['data']['instamojo_clientSecret'] ?? '';
                $amount = (float) $user_wallet['data']['amount'];

                if (empty($clientId) || empty($clientSecret)) {
                    Session::flash('payment_error', 'Instamojo credentials missing');
                    return redirect()->route('transactions');
                }

                $requestId = uniqid();
                Session::put('instamojo_request_id', $requestId);
                Session::save();

                $payload = [
                    'purpose' => 'Wallet Top-up',
                    'amount' => $amount,
                    'buyer_name' => $user_wallet['user']['firstName'] ?? 'User',
                    'email' => Auth::user()->email,
                    'phone' => '9999999999',
                    'redirect_url' => route('wallet-success') . '?payment_request_id=' . $requestId,
                    'webhook' => route('wallet-notify'),
                    'allow_repeated_payments' => false,
                ];

                $headers = [
                    'X-Api-Key' => $clientId,
                    'X-Auth-Token' => $clientSecret,
                ];

                $url = $isSandbox 
                    ? 'https://test.instamojo.com/api/1.1/payment-requests/' 
                    : 'https://www.instamojo.com/api/1.1/payment-requests/';

                try {
                    $client = new Client();
                    $response = $client->post($url, [
                        'headers' => $headers,
                        'form_params' => $payload
                    ]);

                    $result = json_decode($response->getBody(), true);

                    if (isset($result['success']) && $result['success'] === true && isset($result['payment_request']['longurl'])) {
                        return redirect($result['payment_request']['longurl']);
                    } else {
                        Session::flash('payment_error', 'Instamojo payment initialization failed');
                        return redirect()->route('transactions');
                    }
                } catch (\Exception $e) {
                    Session::flash('payment_error', 'Instamojo Error: ' . $e->getMessage());
                    return redirect()->route('transactions');
                }
            } else if ($user_wallet['data']['payment_method'] == 'mtnmomo') {
                // Store MTN MoMo settings in session for view access
                $mtnmomo_enable = $user_wallet['data']['mtnmomo_enable'] ?? false;
                $mtnmomo_isSandbox = $user_wallet['data']['mtnmomo_isSandbox'] ?? true;
                $mtnmomo_primaryKey = $user_wallet['data']['mtnmomo_primaryKey'] ?? '';
                $mtnmomo_callback = $user_wallet['data']['mtnmomo_callback'] ?? route('wallet-success');
                
                // Store MTN MoMo settings in session
                Session::put('mtnmomo_enable', filter_var($mtnmomo_enable, FILTER_VALIDATE_BOOLEAN));
                Session::put('mtnmomo_isSandbox', filter_var($mtnmomo_isSandbox, FILTER_VALIDATE_BOOLEAN));
                Session::put('mtnmomo_primaryKey', $mtnmomo_primaryKey);
                Session::put('mtnmomo_callback', $mtnmomo_callback);
                Session::save();
                
                return redirect()->route('wallet.mtnmomo', ['id' => $user->uuid]);
            } else if ($user_wallet['data']['payment_method'] == 'foloosi') {
                $foloosi_merchant_key = $user_wallet['data']['foloosi_merchant_key'] ?? '';
                $amount = (float) $user_wallet['data']['amount'];
                $currency = $user_wallet['data']['currencyData']['code'] ?? 'USD';
                
                if (empty($foloosi_merchant_key)) {
                    Session::flash('payment_error', 'Foloosi merchant key is missing');
                    return redirect()->route('transactions');
                }
                
                $token = uniqid();
                Session::put('foloosi_payment_token', $token);
                Session::save();
                
                $payload = [
                    'amount'                   => $amount,
                    'currency'                 => $currency,
                    'customer_name'            => $user_wallet['user']['firstName'] ?? 'User',
                    'customer_city'            => 'Dubai',
                    'customer_address'         => 'Customer Address',
                    'customer_mobile'          => $user_wallet['user']['mobile'] ?? '971123456789',
                    'customer_email'           => Auth::user()->email,
                    'description'              => 'Wallet Top-up',
                    'partner_unique_reference' => $token,
                ];
                
                try {
                    $client = new Client(['timeout' => 30, 'verify' => false]);
                    $response = $client->post('https://api.foloosi.com/aggregatorapi/web/initialize-setup', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'secret_key'   => $foloosi_merchant_key,
                        ],
                        'json' => $payload,
                    ]);
                    
                    $result = json_decode($response->getBody(), true);
                    
                    if (isset($result['data']['reference_token'])) {
                        $reference_token = $result['data']['reference_token'];
                        Session::put('foloosi_reference_token', $reference_token);
                        Session::save();
                        
                        return redirect()->route('wallet.foloosi');
                    } else {
                        Session::flash('payment_error', 'Foloosi payment initialization failed');
                        return redirect()->route('transactions');
                    }
                } catch (\Exception $e) {
                    Session::flash('payment_error', 'Foloosi Error: ' . $e->getMessage());
                    return redirect()->route('transactions');
                }
            } else if ($user_wallet['data']['payment_method'] == 'paymongo') {
                $paymongo_secret_key = $user_wallet['data']['paymongo_secret_key'] ?? '';
                $amount = (float) $user_wallet['data']['amount'];
                $currencyCode = $user_wallet['data']['currencyData']['code'] ?? 'PHP';
                
                if (empty($paymongo_secret_key)) {
                    Session::flash('payment_error', 'PayMongo secret key is missing');
                    return redirect()->route('transactions');
                }
                
                // Create payment token for return URL
                $paymentToken = uniqid('pm_');
                Session::put('paymongo_payment_token', $paymentToken);
                Session::save();
                
                return redirect()->route('wallet.paymongo');
            } else {
                return redirect()->route('transactions');
            }
        } else {
            return response()->json(['error' => 'Wallet is empty or invalid'], 400);
        }
    }
    

    public function notify() {
        if ($_POST) {
            $pfData = $_POST;
            if (@$pfData['payment_status']) {
                Session::put('payfast_payment', $pfData);
                Session::save();
            }
        }
    }

    function generateSignature($data) {
        $getString = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        return md5( $getString );
    } 
    
    private function getAccessToken($clientId, $clientSecret) {
        $authUrl = 'https://api.orange.com/oauth/v3/token';
        $client = new Client();
        try {
            $response = $client->post($authUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => ['grant_type' => 'client_credentials'],
            ]);
            $body = json_decode($response->getBody(), true);
            return $body['access_token'] ?? null;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function processStripePayment(Request $request) {

        $email = Auth::user()->email;
        $input = $request->all();
        $user_wallet = Session::get('user_wallet', []);
        if ($user_wallet['data'] && $input['token_id']) {
            if ($user_wallet['data']['stripeKey'] && $user_wallet['data']['stripeSecret']) {
                $currency = @$user_wallet['data']['currency'] ?: 'usd';
                $stripeSecret = $user_wallet['data']['stripeSecret'];
                $stripe = new \Stripe\StripeClient($stripeSecret);
                $description = env('APP_NAME', 'Foodie') . ' Order';
                $amount = bcmul($user_wallet['data']['amount'], 100);
                try {
                    $charge = $stripe->paymentIntents->create([
                        'amount' => $amount,
                        'currency' => $currency,
                        'description' => $description,
                    ]);
                    $user_wallet['payment_status'] = true;
                    $user_wallet['transaction_id'] = $charge->id;
                    Session::put('user_wallet', $user_wallet);
                    Session::put('success', 'Payment successful');
                    Session::save();
                    $res = ['status' => true, 'data' => $charge, 'message' => 'success'];
                    echo json_encode($res);
                    exit;
                } catch (Exception $e) {
                    $user_wallet['payment_status'] = false;
                    Session::put('user_wallet', $user_wallet);
                    Session::put('error', $e->getMessage());
                    Session::save();
                    $res = ['status' => false, 'message' => $e->getMessage()];
                    echo json_encode($res);
                    exit;
                }
            }
        }
    }

    public function processMercadoPagoPayment(Request $request) {
        
        $email = Auth::user()->email;
        $input = $request->all();
        $user_wallet = Session::get('cart', []);
        if (@$user_wallet['data'] && $input['token_id']) {
            if ($user_wallet['data']['PublicKey'] && $user_wallet['data']['AccessToken']) {
                $currency = @$user_wallet['data']['currency'] ?: 'usd';
                $mercadopagoAccess = $user_wallet['data']['AccessToken'];
                $name = $input['name'];
                $urladdress = "https://api.mercadopago.com/checkout/preferences";
                $data = "PublicKey=" . $request->input('PublicKey') . "&AccessToken=" . $request->input('AccessToken') . "&amount=" . $request->input('amount');
            }
        }
    }
    
    public function processPaypalPayment(Request $request) {

        $email = Auth::user()->email;
        $input = $request->all();
        $user_wallet = Session::get('user_wallet', []);
        if (@$user_wallet['data']) {
            $user_wallet['transaction_id'] = $request->transactionId;
            $user_wallet['payment_status'] = true;
            Session::put('user_wallet', $user_wallet);
            Session::put('success', 'Payment successful');
            Session::save();
            $res = ['status' => true, 'data' => [], 'message' => 'success'];
            echo json_encode($res);
            exit;
        }
        $user_wallet['payment_status'] = false;
        Session::put('user_wallet', $user_wallet);
        Session::put('error', 'Failed Payment');
        Session::save();
        $res = ['status' => false, 'message' => 'Failed Payment'];
        echo json_encode($res);
        exit;
    }
    
    public function razorpaypayment(Request $request) {

        $input = $request->all();
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $user_wallet = Session::get('user_wallet', []);
        $api_secret = $user_wallet['data']['razorpaySecret'];
        $api_key = $user_wallet['data']['razorpayKey'];
        $api = new Api($api_key, $api_secret);
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(['amount' => $payment['amount']]);
                $user_wallet['transaction_id'] = $response->id;
                $user_wallet['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::save();
            } catch (Exception $e) {
                return $e->getMessage();
                Session::put('error', $e->getMessage());
                return redirect()->back();
            }
        }
        Session::put('success', 'Payment successful');
        return redirect()->route('wallet-success');
    }

    public function success() {

        // Get the most recent wallet data after payment processing
        $user_wallet = Session::get('user_wallet', []);
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
    
        if (isset($_GET['status']) && $_GET['status'] == "cancelled") {
            return redirect()->route('transactions');
        } 
        
        // Xendit payment check
        if (isset($_GET['xendit_token'])) {
            $xendit_payment = Session::get('xendit_payment_token');
            if ($xendit_payment == $_GET['xendit_token']) {
                $user_wallet['transaction_id'] = $xendit_payment;
                $user_wallet['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
    
        // Midtrans payment check
        if (isset($_GET['midtrans_token'])) {
            $midtrans_payment = Session::get('midtrans_payment_token');
            $urlToken = explode('?', request('midtrans_token'))[0];
            if ($urlToken === $midtrans_payment) {
                $user_wallet['transaction_id'] = $midtrans_payment;
                $user_wallet['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
        
        // OrangePay payment check
        if (isset($_GET['orangepay_token'])) {
            $orangepay_token = Session::get('orangepay_payment_token');
            if ($orangepay_token === $_GET['orangepay_token']) {
                $orangepay_access_token = Session::get('orangepay_access_token');
                $payToken = session('orangepay_payment_check_token');
                $orangepay_isSandbox = session('orangepay_isSandbox');
                $fail_url = route('pay-wallet');
                if (!$payToken && !$orangepay_access_token) {
                    return response()->json(['error' => 'Payment token not found in session']);
                }
                $url = ($orangepay_isSandbox == false) ? 
                    'https://api.orange.com/orange-money-webpay/cm/v1/transactionstatus' : 
                    'https://api.orange.com/orange-money-webpay/dev/v1/transactionstatus';
                try {
                    $client = new Client();
                    $payload = ['pay_token' => $payToken];
                    $response = $client->post($url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $orangepay_access_token,
                            'Content-Type' => 'application/json',
                        ],
                        'body' => json_encode($payload),
                    ]);
                    $responseBody = json_decode($response->getBody(), true);
                    if (isset($responseBody['status']) && $responseBody['status'] == 'SUCCESS') {
                        $user_wallet['transaction_id'] = $payToken;
                        $user_wallet['payment_status'] = true;
                        Session::put('user_wallet', $user_wallet);
                        Session::put('success', 'Payment successful');
                        Session::save();
                    } else {
                        return redirect($fail_url);
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()]);
                }
            }
        }
    
        // Payfast payment check
        if (isset($_GET['token'])) {
            $payfast_payment = Session::get('payfast_payment_token');
            if ($payfast_payment == $_GET['token']) {
                $user_wallet['transaction_id'] = $payfast_payment;
                $user_wallet['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
    
        // Paystack payment check
        if (isset($_GET['reference'])) {
            $paystack_reference = Session::get('paystack_reference');
            if ($paystack_reference == $_GET['reference']) {
                $user_wallet['transaction_id'] = "";
                $user_wallet['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
    
        // Flutterwave payment check
        if (isset($_GET['transaction_id']) && isset($_GET['tx_ref']) && isset($_GET['status'])) {
            $flutterwave_pay_tx_ref = Session::get('flutterwave_pay_tx_ref');
            if ($_GET['status'] == 'successful' && $flutterwave_pay_tx_ref == $_GET['tx_ref']) {
                $user_wallet['transaction_id'] = $_GET['transaction_id'];
                $user_wallet['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Payment successful');
                Session::save();
            } else {
                return redirect()->route('transactions');
            }
        }
    
        // MercadoPago payment check
        if (isset($_GET['preference_id']) && isset($_GET['payment_id']) && isset($_GET['status'])) {
            $mercadopago_preference_id = Session::get('mercadopago_preference_id');
            if ($_GET['status'] == 'approved' && $mercadopago_preference_id == $_GET['preference_id']) {
                $user_wallet['transaction_id'] = $_GET['payment_id'];
                $user_wallet['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Payment successful');
                Session::save();
            } else {
                return redirect()->route('transactions');
            }
        }
    
        // PhonePe payment check
        if (isset($_GET['phonepe_token'])) {
            $phonepe_payment = Session::get('phonepe_merchant_transaction_id');
            $is_cancelled = isset($_GET['status']) && $_GET['status'] == 'cancelled';
            
            if ($is_cancelled) {
                Session::flash('payment_error', 'Payment was cancelled');
                return redirect()->route('transactions');
            }
            
            if ($phonepe_payment && $phonepe_payment == $_GET['phonepe_token']) {
                if (isset($user_wallet['data'])) {
                    // CRITICAL: Set payment_status to true BEFORE putting in session
                    $user_wallet['payment_status'] = true;  // Add this line
                    $user_wallet['transaction_id'] = $phonepe_payment;
                    $user_wallet['data']['payment_status'] = true;
                    Session::put('user_wallet', $user_wallet);
                    Session::put('success', 'Wallet top-up successful');
                    Session::save();
                }
                
                Session::forget('phonepe_merchant_transaction_id');
                Session::forget('phonepe_salt_key');
                Session::forget('phonepe_salt_index');
                Session::forget('phonepe_isSandbox');
                Session::forget('phonepe_merchant_id');
                
                // Remove the redirect here and let it go to the success view
                // return redirect()->route('wallet-success');
            } else {
                Session::flash('payment_error', 'Invalid payment session');
                return redirect()->route('transactions');
            }
        }
        
        // Cashfree payment check
        if (isset($_GET['cashfree_token'])) {
            $sessionToken = Session::get('cashfree_payment_token');
            $orderId = $_GET['cashfree_token'];            
           
            
            if ($sessionToken && $sessionToken === $orderId) {
                $user_wallet['payment_status'] = true;
                $user_wallet['transaction_id'] = $sessionToken;
                $user_wallet['data']['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Wallet top-up successful');
                Session::save();
                
                Session::forget('cashfree_payment_token');
                Session::forget('cashfree_order_amount');
                
            } else {
                \Log::error('Cashfree token mismatch', [
                    'session_token' => $sessionToken,
                    'get_token' => $orderId
                ]);
                Session::flash('payment_error', 'Invalid payment session');
                return redirect()->route('transactions');
            }
        }
        
        // Instamojo payment check
        if (isset($_GET['payment_request_id'])) {
            $requestIdFromUrl = $_GET['payment_request_id'];
            $sessionRequestId = Session::get('instamojo_request_id');
            
            if ($requestIdFromUrl && $sessionRequestId && $requestIdFromUrl === $sessionRequestId) {
                if (isset($_GET['payment_status']) && $_GET['payment_status'] == 'Credit') {
                    $user_wallet['data']['payment_status'] = true;
                    Session::put('user_wallet', $user_wallet);
                    Session::put('success', 'Wallet top-up successful');
                    Session::save();
                } else {
                    Session::flash('payment_error', 'Instamojo payment was not successful');
                    return redirect()->route('transactions');
                }
                Session::forget('instamojo_request_id');
                
                return redirect()->route('wallet-success');
            }
        }
    
        // Foloosi payment check
        if (isset($_GET['foloosi_token'])) {
            $sessionToken = Session::get('foloosi_payment_token');
            if ($sessionToken && $sessionToken === $_GET['foloosi_token']) {
                $user_wallet['data']['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Wallet top-up successful');
                Session::save();
                Session::forget('foloosi_payment_token');
                Session::forget('foloosi_reference_token');
                
                return redirect()->route('wallet-success');
            }
        }
        
        // PayMongo payment check
        if (isset($_GET['pm_token'])) {
            $sessionToken = Session::get('paymongo_payment_token');
            if ($sessionToken && $sessionToken === $_GET['pm_token']) {
                $user_wallet['data']['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Wallet top-up successful');
                Session::save();
                Session::forget('paymongo_payment_token');
                Session::forget('paymongo_secret_key');
                Session::forget('paymongo_is_sandbox');
                
                return redirect()->route('wallet-success');
            }
        }
        
        // MTN MoMo payment check (only special handling needed like gift card)
        if (isset($_GET['mtnmomo_request_ref'])) {
            $submittedRef = $_GET['mtnmomo_request_ref'];
            $sessionRef = Session::get('mtnmomo_request_ref');
            
            if ($submittedRef && $sessionRef && $submittedRef === $sessionRef) {
                $user_wallet['data']['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Wallet top-up successful');
                Session::save();
                Session::forget('mtnmomo_request_ref');
                Session::forget('mtnmomo_access_token');
                Session::forget('mtnmomo_target_env');
                
                return redirect()->route('wallet-success');
            } else {
                Session::flash('payment_error', 'Invalid MTN MoMo payment reference');
                return redirect()->route('transactions');
            }
        }
    
        $payment_method = $user_wallet['data']['payment_method'];
    
        return view('transactions.success', [
            'user_wallet' => $user_wallet, 
            'id' => $user->uuid, 
            'email' => $email, 
            'payment_method' => $payment_method
        ]);
    }
    
    public function walletProccessing(Request $request) {
        $data = $request->all();
        $user = Auth::user();
        $user_wallet = [];
        $user_wallet['data'] = $data;
        $user_wallet['user'] = json_decode($request->author, true);
        Session::put('user_wallet', $user_wallet);
        Session::save();
        $res = ['status' => true];
        echo json_encode($res);
        exit;
    }

    
    public function walletMtnmomo(Request $request, $id)
    {
        try {
            $user_wallet = Session::get('user_wallet', []);
            
           
            if (!isset($user_wallet['data'])) {
                \Log::error('MTN MoMo: No wallet data found');
                return redirect()->route('transactions')
                    ->with('payment_error', 'No active wallet session found. Please start payment process again.');
            }

            // FIX: The settings should come directly from $user_wallet['data'] with proper boolean conversion
            $settings = [
                'enable' => filter_var($user_wallet['data']['mtnmomo_enable'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'isSandbox' => filter_var($user_wallet['data']['mtnmomo_isSandbox'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'primaryKey' => $user_wallet['data']['mtnmomo_primaryKey'] ?? '',
                'callbackUrl' => $user_wallet['data']['mtnmomo_callback'] ?? url('/'),
            ];

            if (empty($settings['enable']) || !$settings['enable']) {
                \Log::error('MTN MoMo: Not configured', ['settings' => $settings]);
                return redirect()->route('transactions')
                    ->with('payment_error', 'MTN MoMo is not configured.');
            }

            $amount = (float) $user_wallet['data']['amount'];
            $currency = 'EUR'; // MTN MoMo sandbox only supports EUR
            $formatted_price = '€' . number_format($amount, 2);

            // Make sure to pass all required variables to the view
            return view('transactions.mtnmomo', compact(
                'user_wallet',
                'amount',
                'currency',
                'formatted_price',
                'settings'
            ));
            
        } catch (\Exception $e) {
            \Log::error('MTN MoMo Exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('transactions')
                ->with('payment_error', 'MTN MoMo Error: ' . $e->getMessage());
        }
    }

    public function walletMtnmomoRequest(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:8|max:15',
        ]);

        $user_wallet = Session::get('user_wallet', []);

        if (empty($user_wallet['data'])) {
            return redirect()->route('transactions')
                ->with('payment_error', 'No active wallet session found.');
        }

        // FIX: Construct settings from individual fields like in the view method
        $settings = [
            'enable' => filter_var($user_wallet['data']['mtnmomo_enable'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'isSandbox' => filter_var($user_wallet['data']['mtnmomo_isSandbox'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'primaryKey' => $user_wallet['data']['mtnmomo_primaryKey'] ?? '',
            'callbackUrl' => $user_wallet['data']['mtnmomo_callback'] ?? url('/'),
        ];
        $amount   = (float) $user_wallet['data']['amount'];
        $currency = 'EUR'; // MTN MoMo only supports EUR in sandbox
        
        // Debug: Check settings            
        if (empty($settings['primaryKey'])) {
            throw new \Exception('MTN MoMo primary key is not configured');
        }

        $phone = trim($request->phone);
        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        try {
            if (empty($settings['primaryKey'])) {
                throw new \Exception('MTN MoMo primary key is not configured');
            }

            $baseUrl = $settings['isSandbox'] ? 'https://sandbox.momodeveloper.mtn.com' : 'https://proxy.momoapi.mtn.com';

            $client = new Client(['timeout' => 30, 'verify' => false]);

            // ── 1. Create API User
            $apiUserRef = (string) \Illuminate\Support\Str::uuid();
            Session::put('mtnmomo_apiuser_ref', $apiUserRef);

            $response = $client->post("{$baseUrl}/v1_0/apiuser", [
                'headers' => [
                    'X-Reference-Id'          => $apiUserRef,
                    'Ocp-Apim-Subscription-Key' => $settings['primaryKey'],
                    'Content-Type'            => 'application/json',
                ],
                'json' => [
                    'providerCallbackHost' => $settings['callbackUrl'] ?? url('/'),
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
            Session::put('mtnmomo_target_env', $settings['isSandbox'] ? 'sandbox' : ($settings['targetEnvironment'] ?? 'mtnuganda'));

            // ── 4. Request to Pay
            $requestRef = (string) \Illuminate\Support\Str::uuid();
            Session::put('mtnmomo_request_ref', $requestRef);
            Session::save();

            $payload = [
                "amount"      => (string) $amount,
                "currency"    => $currency,
                "externalId"  => 'wallet-' . time(),
                "payer"       => [
                    "partyIdType" => "MSISDN",
                    "partyId"     => $phone
                ],
                "payerMessage"=> "Wallet Top-up - " . config('app.name'),
                "payeeNote"   => "Wallet Top-up"
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
                throw new \Exception('Request to pay failed');
            }

            Session::put('mtnmomo_poll_start', now()->timestamp);

            return response()->json([
                'success' => true,
                'reference' => $requestRef,
                'message' => 'Payment request sent successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function walletMtnmomoCheckStatus(Request $request)
    {
        $ref = $request->input('reference');
        $sessionRef = Session::get('mtnmomo_request_ref');
                
        if (!$ref || Session::get('mtnmomo_request_ref') !== $ref) {
            \Log::warning('MTN MoMo polling - Invalid reference', [
                'request_ref' => $ref,
                'session_ref' => $sessionRef,
                'session_ref_exists' => !empty($sessionRef)
            ]);
            return response()->json(['success' => false, 'message' => 'Invalid reference']);
        }

        $user_wallet = Session::get('user_wallet', []);
        // FIX: Construct settings from individual fields like in other methods
        $settings = [
            'enable' => filter_var($user_wallet['data']['mtnmomo_enable'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'isSandbox' => filter_var($user_wallet['data']['mtnmomo_isSandbox'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'primaryKey' => $user_wallet['data']['mtnmomo_primaryKey'] ?? '',
            'callbackUrl' => $user_wallet['data']['mtnmomo_callback'] ?? url('/'),
        ];

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

            if ($status === 'SUCCESSFUL') {
                $user_wallet['data']['payment_status'] = true;
                Session::put('user_wallet', $user_wallet);
                Session::put('success', 'Wallet topped up successfully via MTN MoMo');
                Session::save();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful',
                    'redirect' => route('wallet-success')
                ]);
            } elseif ($status === 'FAILED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed',
                    'details' => $body
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment still processing',
                    'status' => $status
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('MTN MoMo Status Check Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Status check failed: ' . $e->getMessage()]);
        }
    }

    
    
    // Wallet Foloosi Method
    public function walletFoloosi(Request $request)
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $user_wallet = Session::get('user_wallet', []);
        $reference_token = Session::get('foloosi_reference_token');
        
        return view('transactions.foloosi', [
            'user_wallet' => $user_wallet,
            'id' => $user->uuid,
            'email' => $email,
            'authorName' => $user_wallet['user']['firstName'] ?? $user->firstName,
            'amount' => $user_wallet['data']['amount'],
            'foloosi_merchant_key' => $user_wallet['data']['foloosi_merchant_key'],
            'foloosi_secret_key' => $user_wallet['data']['foloosi_secret_key'],
            'foloosi_isSandbox' => $user_wallet['data']['foloosi_isSandbox'],
            'currency' => $user_wallet['data']['currencyData']['code'] ?? 'USD',
            'formatted_price' => ($user_wallet['data']['currencyData']['symbol'] ?? '$') . number_format($user_wallet['data']['amount'], $user_wallet['data']['currencyData']['decimal_degits'] ?? 2),
            'reference_token' => $reference_token
        ]);
    }

    // Wallet PayMongo Method
    public function walletPaymongo(Request $request)
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $user_wallet = Session::get('user_wallet', []);
        
        return view('transactions.paymongo', [
            'user_wallet' => $user_wallet,
            'id' => $user->uuid,
            'email' => $email,
            'authorName' => $user_wallet['user']['firstName'] ?? $user->firstName,
            'amount' => $user_wallet['data']['amount'],
            'public_key' => $user_wallet['data']['paymongo_public_key'],
            'paymongo_secret_key' => $user_wallet['data']['paymongo_secret_key'],
            'paymongo_isSandbox' => $user_wallet['data']['paymongo_isSandbox'],
            'currency' => $user_wallet['data']['currencyData']['code'] ?? 'USD',
            'formatted_price' => ($user_wallet['data']['currencyData']['symbol'] ?? '$') . number_format($user_wallet['data']['amount'], $user_wallet['data']['currencyData']['decimal_degits'] ?? 2)
        ]);
    }
}

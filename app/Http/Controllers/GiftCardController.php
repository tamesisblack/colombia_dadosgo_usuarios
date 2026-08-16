<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;
use App\Models\User;
use Razorpay\Api\Api;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\XenditSdkException;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

use Session;
class GiftCardController extends Controller
{
    public function __construct()
    {
        if (!isset($_COOKIE['address_name'])) {
            \Redirect::to('set-location')->send();
        }
        $this->middleware('auth');
    }
    
    public function index()
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        return view('gift_card.giftcard')->with('id',$user->uuid);
    }

    public function giftCardProcessing(Request $request){
        $gift_cart_order = $request->all();
        $cart = array();
        Session::put('gift_cart', $cart);
        $cart = Session::get('gift_cart', []);
        $cart['gift_cart_order'] = $gift_cart_order;
        Session::put('gift_cart', $cart);
        Session::save();
        $res = array('status' => true);
        echo json_encode($res);
        exit;
    }

    public function proccesstopay(Request $request)
    {
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $cart = Session::get('gift_cart', []);
        if (@$cart['gift_cart_order']) {
            $giftOrderData = $cart['gift_cart_order'];
            $orderJson = [
                'giftId' => $giftOrderData['order_json']['giftId'] ?? $giftOrderData['giftId'] ?? '',
                'price' => $giftOrderData['order_json']['price'] ?? $giftOrderData['total_pay'] ?? 0,
                'message' => $giftOrderData['order_json']['message'] ?? '',
                'redeem' => false,
                'userid' => $giftOrderData['order_json']['userid'] ?? '',
                'id' => $giftOrderData['order_json']['id'] ?? '',
                'giftTitle' => $giftOrderData['order_json']['giftTitle'] ?? '',
                'giftPin' => $giftOrderData['order_json']['giftPin'] ?? '',
                'giftCode' => $giftOrderData['order_json']['giftCode'] ?? '',
                'expiryDay' => $giftOrderData['order_json']['expiryDay'] ?? 30
            ];
            
            $cart['gift_cart_order']['order_json'] = $orderJson;
            Session::put('gift_cart', $cart);
            Session::save();
            if ($cart['gift_cart_order']['payment_method'] == 'razorpay') {
                $razorpaySecret = $cart['gift_cart_order']['razorpaySecret'];
                $razorpayKey = $cart['gift_cart_order']['razorpayKey'];
                $authorName = '';
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $amount = 0;
                $formatted_price =  $cart['gift_cart_order']['currencyData']['symbol'].number_format($total_pay,$cart['gift_cart_order']['currencyData']['decimal_degits']) ;
                return view('gift_card.razorpay', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'email' => $email,'authorName'=>$authorName, 'amount' => $total_pay, 'razorpaySecret' => $razorpaySecret, 'razorpayKey' => $razorpayKey, 'gift_cart_order' => $cart['gift_cart_order'], 'formatted_price' => $formatted_price]);
            } 
            else if ($cart['gift_cart_order']['payment_method'] == 'payfast') {
                $payfast_merchant_key = $cart['gift_cart_order']['payfast_merchant_key'];
                $payfast_merchant_id = $cart['gift_cart_order']['payfast_merchant_id'];
                $payfast_isSandbox = $cart['gift_cart_order']['payfast_isSandbox'];
                $payfast_return_url = route('giftcard.success');
                $payfast_notify_url = route('notify');
                $payfast_cancel_url = route('giftcard.pay'); 
                $authorName = $cart['gift_cart_order']['authorName'];
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $formatted_price =  $cart['gift_cart_order']['currencyData']['symbol'].number_format($total_pay,$cart['gift_cart_order']['currencyData']['decimal_degits']) ;
                $token = uniqid();
                Session::put('payfast_payment_token', $token);
                Session::save();
                $payfast_return_url = $payfast_return_url . '?token=' . $token;
                $pfHost = $payfast_isSandbox == "true" ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
                return view('gift_card.payfast', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'email' => $email,  'pfHost'=> $pfHost,'authorName' => $authorName, 'amount' => $total_pay, 'payfast_merchant_key' => $payfast_merchant_key, 'payfast_merchant_id' => $payfast_merchant_id, 'payfast_isSandbox' => $payfast_isSandbox, 'payfast_return_url' => $payfast_return_url, 'payfast_notify_url' => $payfast_notify_url, 'payfast_cancel_url' => $payfast_cancel_url, 'gift_cart_order' => $cart['gift_cart_order'], 'formatted_price' => $formatted_price]);
            } else if ($cart['gift_cart_order']['payment_method'] == 'paystack') {
                $paystack_public_key = $cart['gift_cart_order']['paystack_public_key'];
                $paystack_secret_key = $cart['gift_cart_order']['paystack_secret_key'];
                $paystack_isSandbox = $cart['gift_cart_order']['paystack_isSandbox'];
                $authorName = $cart['gift_cart_order']['authorName'];
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $amount = 0;
                // require_once(base_path() . '/paystack-php-master/vendor/autoload.php');
                define("PaystackPublicKey", $paystack_public_key);
                define("PaystackSecretKey", $paystack_secret_key);
                \Paystack\Paystack::init($paystack_secret_key);
                $payment = \Paystack\Transaction::initialize([
                    'email' => $email,
                    'amount' => (int) ($total_pay * 100),
                    'callback_url'=>route('giftcard.success')
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
            } else if ($cart['gift_cart_order']['payment_method'] == 'flutterwave') {
                $currency = "USD";
                if (@$cart['gift_cart_order']['currencyData']['code']) {
                    $currency = $cart['gift_cart_order']['currencyData']['code'];
                }
                $flutterWave_secret_key = $cart['gift_cart_order']['flutterWave_secret_key'];
                $flutterWave_public_key = $cart['gift_cart_order']['flutterWave_public_key'];
                $flutterWave_isSandbox = $cart['gift_cart_order']['flutterWave_isSandbox'];
                $flutterWave_encryption_key = $cart['gift_cart_order']['flutterWave_encryption_key'];
                $authorName = $cart['gift_cart_order']['authorName'];
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $formatted_price =  $cart['gift_cart_order']['currencyData']['symbol'].number_format($total_pay,$cart['gift_cart_order']['currencyData']['decimal_degits']) ;
                Session::put('flutterwave_pay', 1);
                Session::save();
                $token = uniqid();
                Session::put('flutterwave_pay_tx_ref', $token);
                Session::save();
                return view('gift_card.flutterwave', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'flutterWave_secret_key' => $flutterWave_secret_key, 'flutterWave_public_key' => $flutterWave_public_key, 'flutterWave_isSandbox' => $flutterWave_isSandbox, 'flutterWave_encryption_key' => $flutterWave_encryption_key, 'token' => $token, 'gift_cart_order' => $cart['gift_cart_order'], 'currency' => $currency, 'formatted_price' => $formatted_price]);
            }else if($cart['gift_cart_order']['payment_method']=='xendit'){
                $xendit_enable=$cart['gift_cart_order']['xendit_enable'];
                $xendit_apiKey=$cart['gift_cart_order']['xendit_apiKey'];
                if (isset($xendit_enable) && $xendit_enable == true) {
                    $total_pay = $cart['gift_cart_order']['total_pay'];
                    $currency = $cart['gift_cart_order']['currencyData']['code'];
                    $fail_url = route('giftcard.pay');
                    $success_url = route('giftcard.success');
                    Configuration::setXenditKey($xendit_apiKey);
                    $token = uniqid();
                    $success_url = $success_url . '?xendit_token=' . $token;
                    Session::put('xendit_payment_token', $token);
                    Session::save();
                    $apiInstance = new InvoiceApi();
                    $create_invoice_request = new CreateInvoiceRequest([
                        'external_id' => $token,
                        'description' => '#'.$token.' Order place',
                        'amount' => (int)($total_pay)*1000,
                        'invoice_duration' => 300,
                        'currency' => 'IDR',
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
            } else if($cart['gift_cart_order']['payment_method']=='midtrans'){
                $midtrans_enable = $cart['gift_cart_order']['midtrans_enable'];
                $midtrans_serverKey = $cart['gift_cart_order']['midtrans_serverKey'];
                $midtrans_isSandbox = $cart['gift_cart_order']['midtrans_isSandbox'];
                if (isset($midtrans_enable) && isset($midtrans_serverKey) && $midtrans_enable == true) {
                    if ($midtrans_isSandbox == true)
                        $url = 'https://api.sandbox.midtrans.com/v1/payment-links';
                    else
                        $url = 'https://api.midtrans.com/v1/payment-links';
                    $total_pay = $cart['gift_cart_order']['total_pay'];
                    $fail_url = route('giftcard.pay');
                    $success_url = route('giftcard.success');
                    $token = uniqid();
                    $success_url = $success_url . '?midtrans_token=' . $token;
                    Session::put('midtrans_payment_token', $token);
                    Session::save();
                    $payload = [
                        'transaction_details' => [
                            'order_id' => $token,
                            'gross_amount' => (int)($total_pay)*1000,
                        ],
                        'usage_limit' => 1,
                        'callbacks'=> [
                                        'error'=> $fail_url,
                                        'unfinish'=> $fail_url,
                                        'close'=> $fail_url,
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
            } else if($cart['gift_cart_order']['payment_method']=='orangepay'){
                $orangepay_enable = $cart['gift_cart_order']['orangepay_enable'];
                $orangepay_isSandbox = $cart['gift_cart_order']['orangepay_isSandbox'];
                Session::put('orangepay_isSandbox', $orangepay_isSandbox);
                Session::save();
                $orangepay_clientId = $cart['gift_cart_order']['orangepay_clientId'];
                $orangepay_clientSecret = $cart['gift_cart_order']['orangepay_clientSecret'];
                $orangepay_merchantKey = $cart['gift_cart_order']['orangepay_merchantKey'];
                $token = $this->getAccessToken($orangepay_clientId,$orangepay_clientSecret);
                Session::put('orangepay_access_token', $token);
                Session::save();
                if (isset($token) && $token != null && isset($orangepay_enable) && isset($orangepay_clientId) && $orangepay_enable == true) {
                   if ($orangepay_isSandbox == true)
                        $url = 'https://api.orange.com/orange-money-webpay/dev/v1/webpayment';
                    else
                        $url = 'https://api.orange.com/orange-money-webpay/cm/v1/webpayment';
                    $total_pay = $cart['gift_cart_order']['total_pay'];
                    $currency = ($orangepay_isSandbox == true) ? 'OUV' : $cart['gift_cart_order']['currencyData']['code'];
                    $orangepay_token = uniqid();
                    $fail_url = route('giftcard.pay');
                    $success_url = route('giftcard.success');
                    $success_url = $success_url . '?orangepay_token=' . $orangepay_token;
                    $notify_url = $success_url . '?orangepay_token=' . $orangepay_token;
                    Session::put('orangepay_payment_token', $orangepay_token);
                    Session::save();
                    $payload = [
                        'merchant_key' => $orangepay_merchantKey, 
                        'currency' => $currency,  
                        'order_id' => $orangepay_token,
                        'amount' => (int)($total_pay),
                        'return_url' => $success_url,
                        'cancel_url' => $fail_url,
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
                            ],
                            'body' => json_encode($payload),
                        ]);
                        $responseBody = json_decode($response->getBody(), true);
                        if (isset($responseBody['payment_url'])) {
                            Session::put('orangepay_payment_check_token', $responseBody['pay_token']);
                            Session::save();
                            return redirect($responseBody['payment_url']);
                        } else {
                            return response()->json(['error' => 'Payment request failed']);
                        }
                    } catch (\Exception $e) {
                        return response()->json(['error' => $e->getMessage()]);
                    }
                }
            } else if ($cart['gift_cart_order']['payment_method'] == 'mercadopago') {
                $currency = "USD";
                if (@$cart['gift_cart_order']['currencyData']['code']) {
                    $currency = $cart['gift_cart_order']['currencyData']['code'];
                }
                $mercadopago_public_key = $cart['gift_cart_order']['mercadopago_public_key'];
                $mercadopago_access_token = $cart['gift_cart_order']['mercadopago_access_token'];
                $mercadopago_isSandbox = $cart['gift_cart_order']['mercadopago_isSandbox'];
                $mercadopago_isEnabled = $cart['gift_cart_order']['mercadopago_isEnabled'];
                $id = $cart['gift_cart_order']['id'];
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $items['title'] = $id;
                $items['quantity'] = 1;
                $items['unit_price'] = floatval($total_pay);
                $fields[] = $items;
                $item['items'] = $fields;
                $item['back_urls']['failure'] = route('giftcard.pay');
                $item['back_urls']['pending'] = route('notify');
                $item['back_urls']['success'] = route('giftcard.success');
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
                $authorName = '';
                $total_pay = $cart['gift_cart_order']['total_pay'];
                if ($mercadopago_isSandbox == "true") {
                    $payment_url = $mercadopago->sandbox_init_point;
                } else {
                    $payment_url = $mercadopago->init_point;
                }
                echo "<script>location.href = '" . $payment_url . "';</script>";
                exit;
            } else if ($cart['gift_cart_order']['payment_method'] == 'stripe') {
                $stripeKey = $cart['gift_cart_order']['stripeKey'];
                $stripeSecret = $cart['gift_cart_order']['stripeSecret'];
                $authorName = $cart['gift_cart_order']['authorName'];
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $address_line1 = $cart['gift_cart_order']['address_line1'];
                $address_line2 = $cart['gift_cart_order']['address_line2'];
                $address_zipcode = $cart['gift_cart_order']['address_zipcode'];
                $address_city = $cart['gift_cart_order']['address_city'];
                $address_country = $cart['gift_cart_order']['address_country'];
                $stripeSecret = $cart['gift_cart_order']['stripeSecret'];
                $stripeKey = $cart['gift_cart_order']['stripeKey'];
                $isStripeSandboxEnabled = $cart['gift_cart_order']['isStripeSandboxEnabled'];
                $authorName = $cart['gift_cart_order']['authorName'];
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $formatted_price =  $cart['gift_cart_order']['currencyData']['symbol'].number_format($total_pay,$cart['gift_cart_order']['currencyData']['decimal_degits']) ;
                $amount = 0;
                return view('gift_card.stripe', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'stripeSecret' => $stripeSecret, 'stripeKey' => $stripeKey, 'gift_cart_order' => $cart['gift_cart_order'], 'formatted_price' => $formatted_price]);
            } else if ($cart['gift_cart_order']['payment_method'] == 'paypal') {
                $paypalKey = $cart['gift_cart_order']['paypalKey'];
                $paypalSecret = $cart['gift_cart_order']['paypalSecret'];
                $authorName = $cart['gift_cart_order']['authorName'];
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $address_line1 = $cart['gift_cart_order']['address_line1'];
                $address_line2 = $cart['gift_cart_order']['address_line2'];
                $address_zipcode = $cart['gift_cart_order']['address_zipcode'];
                $address_city = $cart['gift_cart_order']['address_city'];
                $address_country = $cart['gift_cart_order']['address_country'];
                $paypalSecret = $cart['gift_cart_order']['paypalSecret'];
                $paypalKey = $cart['gift_cart_order']['paypalKey'];
                $ispaypalSandboxEnabled = $cart['gift_cart_order']['ispaypalSandboxEnabled'];
                $authorName = $cart['gift_cart_order']['authorName'];
                $total_pay = $cart['gift_cart_order']['total_pay'];
                $amount = 0;
                $formatted_price =  $cart['gift_cart_order']['currencyData']['symbol'].number_format($total_pay,$cart['gift_cart_order']['currencyData']['decimal_degits']) ;
                return view('gift_card.paypal', ['is_checkout' => 1, 'cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'authorName' => $authorName, 'amount' => $total_pay, 'paypalSecret' => $paypalSecret, 'paypalKey' => $paypalKey, 'gift_cart_order' => $cart['gift_cart_order'], 'formatted_price' => $formatted_price]);
            }
            // FIXED: MTN MoMo
            else if ($cart['gift_cart_order']['payment_method'] == 'mtnmomo') {
                $mtnmomo_enable     = $cart['gift_cart_order']['mtnmomo_enable'] ?? false;
                $mtnmomo_isSandbox  = $cart['gift_cart_order']['mtnmomo_isSandbox'] ?? true;
                $mtnmomo_primaryKey = $cart['gift_cart_order']['mtnmomo_primaryKey'] ?? '';

                if (!$mtnmomo_enable || empty($mtnmomo_primaryKey)) {
                    Session::flash('payment_error', 'MTN MoMo is not properly configured.');
                    return redirect()->route('customize.giftcard');
                }

                $cart['gift_cart_order']['mtnmomo'] = [
                    'enable'        => $mtnmomo_enable,
                    'isSandbox'     => $mtnmomo_isSandbox,
                    'primaryKey'    => $mtnmomo_primaryKey,
                ];

                Session::put('gift_cart', $cart);
                Session::save();

                // Store required data for the view
                $amount = $cart['gift_cart_order']['total_pay'];
                $formatted_price = $cart['gift_cart_order']['currencyData']['symbol'] . number_format($amount, $cart['gift_cart_order']['currencyData']['decimal_degits'] ?? 2);
                
                // Redirect to separate MTN MoMo route like checkout does
                return redirect()->route('gift_card.mtnmomo', ['id' => $user->firebase_uid ?? $user->uuid]);
            }
            // FIXED: PhonePe - Updated with proper redirect handling
            else if ($cart['gift_cart_order']['payment_method'] == 'phonepe') {
                $phonepe_enable   = $cart['gift_cart_order']['phonepe_enable'] ?? false;
                $isSandbox        = filter_var($cart['gift_cart_order']['phonepe_isSandbox'], FILTER_VALIDATE_BOOLEAN);
                $merchantId       = $cart['gift_cart_order']['phonepe_merchantId'] ?? '';
                $saltKey          = $cart['gift_cart_order']['phonepe_saltKey'] ?? '';
                $saltIndex        = $cart['gift_cart_order']['phonepe_saltIndex'] ?? '1';
                $total_pay        = (float) $cart['gift_cart_order']['total_pay'];

                if (!$phonepe_enable || empty($merchantId) || empty($saltKey)) {
                    Session::flash('payment_error', 'PhonePe is not properly configured. Check Merchant ID and Salt Key.');
                    return redirect()->route('customize.giftcard');
                }

                // Generate unique transaction ID
                $merchantTransactionId = 'GT' . time() . rand(1000, 9999);
                
                // Store ALL cart data in session with a backup key
                Session::put('phonepe_merchant_transaction_id', $merchantTransactionId);
                Session::put('phonepe_salt_key', $saltKey);
                Session::put('phonepe_salt_index', $saltIndex);
                Session::put('phonepe_is_sandbox', $isSandbox);
                Session::put('phonepe_merchant_id', $merchantId);
                
                // IMPORTANT: Create a backup of the cart data
                $cartBackupToken = uniqid('cart_backup_');
                Session::put('gift_cart_backup_token', $cartBackupToken);
                Session::put('gift_cart_backup_data_' . $cartBackupToken, $cart);
                Session::save();

                // Create success URL with the transaction ID
                $successUrl = route('giftcard.success') . '?phonepe_token=' . $merchantTransactionId;
                $failUrl = route('customize.giftcard');
                
                // Prepare PhonePe payload
                $payloadArray = [
                    'merchantId' => $merchantId,
                    'merchantTransactionId' => $merchantTransactionId,
                    'merchantUserId' => $user->firebase_uid ?? Auth::id(),
                    'amount' => (int)($total_pay * 100),
                    'redirectUrl' => $successUrl,
                    'redirectMode' => 'REDIRECT',
                    'callbackUrl' => $successUrl,
                    'paymentInstrument' => [
                        'type' => 'PAY_PAGE'
                    ]
                ];

                // Encode payload
                $payload = base64_encode(json_encode($payloadArray));
                
                // Generate checksum
                $string = $payload . '/pg/v1/pay' . $saltKey;
                $sha256 = hash('sha256', $string);
                $checksum = $sha256 . '###' . $saltIndex;

                // Determine API endpoint
                $apiEndpoint = $isSandbox 
                    ? 'https://api-preprod.phonepe.com/apis/hermes/pg/v1/pay'
                    : 'https://api.phonepe.com/apis/hermes/pg/v1/pay';

                try {
                    $client = new Client(['timeout' => 30, 'verify' => false]);
                    $response = $client->post($apiEndpoint, [
                        'headers' => [
                            'Content-Type'   => 'application/json',
                            'X-VERIFY'       => $checksum,
                            'X-MERCHANT-ID'  => $merchantId,
                        ],
                        'json' => ['request' => $payload]
                    ]);

                    $result = json_decode($response->getBody(), true);
                    
                    if (isset($result['success']) && $result['success'] === true) {
                        $redirectUrl = $result['data']['instrumentResponse']['redirectInfo']['url'] ?? 
                                    $result['data']['redirectUrl'] ?? 
                                    $result['redirectUrl'] ?? null;

                        if ($redirectUrl) {
                            // Store the transaction ID in session for the redirect
                            Session::put('phonepe_pending_transaction', $merchantTransactionId);
                            Session::save();
                            
                            // Redirect to PhonePe payment page
                            return redirect($redirectUrl);
                        }
                    }

                    // If we get here, something went wrong
                    Session::flash('payment_error', $result['message'] ?? 'No payment URL received from PhonePe');
                    return redirect()->route('customize.giftcard');
                    
                } catch (\Exception $e) {
                    \Log::error('PhonePe Error: ' . $e->getMessage());
                    Session::flash('payment_error', 'PhonePe Error: ' . $e->getMessage());
                    return redirect()->route('customize.giftcard');
                }
            }
            // FIXED: Cashfree - Use proper customer name instead of email
            else if($cart['gift_cart_order']['payment_method']=='cashfree'){
                $cashfree_clientId     = $cart['gift_cart_order']['cashfree_clientId'] ?? '';
                $cashfree_clientSecret = $cart['gift_cart_order']['cashfree_clientSecret'] ?? '';
                $cashfree_isSandbox    = $cart['gift_cart_order']['cashfree_isSandbox'] ?? false;

                if (empty($cashfree_clientId) || empty($cashfree_clientSecret)) {
                    Session::flash('payment_error', 'Cashfree credentials missing');
                    return redirect()->route('customize.giftcard');
                }

                // IMPORTANT: Use actual customer name, not email
                $authorName = $cart['gift_cart_order']['authorName'] ?? '';
                
                // If authorName is empty or looks like an email, try to get from user record
                if (empty($authorName) || filter_var($authorName, FILTER_VALIDATE_EMAIL)) {
                    $user = Auth::user();
                    $authorName = $user->name ?? '';
                    
                    // If still empty or looks like email, try to get from VendorUsers
                    if (empty($authorName) || filter_var($authorName, FILTER_VALIDATE_EMAIL)) {
                        $email = Auth::user()->email;
                        $vendorUser = VendorUsers::where('email', $email)->first();
                        $authorName = $vendorUser->name ?? 'Customer';
                    }
                }
                
                // Final fallback - ensure it's a valid name (not an email)
                if (empty($authorName) || filter_var($authorName, FILTER_VALIDATE_EMAIL)) {
                    $authorName = 'Customer';
                }
                
                // Ensure name has proper format (at least 2 characters, no special chars)
                $authorName = preg_replace('/[^a-zA-Z\s]/', '', $authorName);
                if (strlen($authorName) < 2) {
                    $authorName = 'Customer';
                }
                
                $email = Auth::user()->email ?? 'noemail@example.com';
                $phone = Auth::user()->phone ?? '9999999999';
                
                // Validate phone number (remove any non-digit characters)
                $phone = preg_replace('/[^0-9]/', '', $phone);
                if (strlen($phone) < 10) {
                    $phone = '9999999999';
                }
                
                $total_pay = (float) $cart['gift_cart_order']['total_pay'];
                $currency = 'INR';

                if (!empty($cashfree_clientId) && !empty($cashfree_clientSecret)) {
                    $baseUrl = ($cashfree_isSandbox == "true" || $cashfree_isSandbox === true)
                        ? "https://sandbox.cashfree.com/pg/links"
                        : "https://api.cashfree.com/pg/links";

                    $token = uniqid('cf_');
                    $success_url = route('giftcard.success') . '?cashfree_token=' . $token;

                    // Store cart backup
                    $cartBackupToken = uniqid('cart_backup_');
                    Session::put('gift_cart_backup_token', $cartBackupToken);
                    Session::put('gift_cart_backup_data_' . $cartBackupToken, $cart);
                    
                    Session::put('cashfree_payment_token', $token);
                    Session::save();

                    $payload = [
                        'link_id'           => $token,
                        'link_amount'       => $total_pay,
                        'link_currency'     => strtoupper($currency),
                        'link_purpose'      => 'Gift Card Purchase',
                        'customer_details'  => [
                            'customer_name'  => $authorName,  // Now using actual name, not email
                            'customer_email' => $email,
                            'customer_phone' => $phone,
                        ],
                        'link_success_url'  => $success_url,
                        'link_meta'         => ['return_url' => $success_url],
                        'link_notify_url'   => $success_url,
                        'link_expiry_time'  => now()->addMinutes(30)->toIso8601String(),
                    ];
                    

                    try {
                        $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);
                        $response = $client->post($baseUrl, [
                            'headers' => [
                                'x-client-id'     => $cashfree_clientId,
                                'x-client-secret' => $cashfree_clientSecret,
                                'x-api-version'   => '2023-08-01',
                                'Content-Type'    => 'application/json',
                                'Accept'          => 'application/json',
                            ],
                            'json' => $payload,
                        ]);

                        $result = json_decode($response->getBody(), true);

                        if (isset($result['link_url']) && $result['link_url']) {
                            return redirect($result['link_url']);
                        }

                        // Also check for payment_url or redirect_url in response
                        if (isset($result['payment_url']) && $result['payment_url']) {
                            return redirect($result['payment_url']);
                        }

                        Session::flash('payment_error', 'Cashfree link creation failed: ' . json_encode($result));
                        return redirect()->route('customize.giftcard');

                    } catch (\GuzzleHttp\Exception\ClientException $e) {
                        $responseBody = $e->hasResponse() ? (string) $e->getResponse()->getBody() : '';
                        \Log::error('Cashfree Client Error: ' . $responseBody);
                        
                        // Try to decode the error for better message
                        $errorData = json_decode($responseBody, true);
                        $errorMessage = $errorData['message'] ?? $responseBody;
                        
                        Session::flash('payment_error', 'Cashfree: ' . $errorMessage);
                        return redirect()->route('customize.giftcard');
                    } catch (\Exception $e) {
                        \Log::error('Cashfree Error: ' . $e->getMessage());
                        Session::flash('payment_error', 'Cashfree Error: ' . $e->getMessage());
                        return redirect()->route('customize.giftcard');
                    }
                }

                Session::flash('payment_error', 'Cashfree credentials missing or invalid');
                return redirect()->route('customize.giftcard');
            }
            // FIXED: Instamojo
            else if ($cart['gift_cart_order']['payment_method'] === 'instamojo') {
                $isSandbox     = filter_var($cart['gift_cart_order']['instamojo_isSandbox'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $clientId      = $cart['gift_cart_order']['instamojo_clientId'] ?? '';
                $clientSecret  = $cart['gift_cart_order']['instamojo_clientSecret'] ?? '';

                if (empty($clientId) || empty($clientSecret)) {
                    Session::flash('payment_error', 'Instamojo credentials are missing.');
                    return redirect()->route('customize.giftcard');
                }

                $baseUrl = $isSandbox
                    ? 'https://test.instamojo.com/v2/'
                    : 'https://api.instamojo.com/v2/';

                $amount = (float) ($cart['gift_cart_order']['total_pay'] ?? 0);
                $currency = 'INR';

                if ($currency !== 'INR') {
                    Session::flash('payment_error', 'Instamojo supports only INR currency.');
                    return redirect()->route('customize.giftcard');
                }

                if ($amount < 1) {
                    Session::flash('payment_error', 'Amount must be at least ₹1.');
                    return redirect()->route('customize.giftcard');
                }

                $buyerName  = $cart['gift_cart_order']['buyer_name'] ?? Auth::user()->name ?? 'Guest';
                $buyerEmail = $cart['gift_cart_order']['buyer_email'] ?? Auth::user()->email ?? 'no-reply@example.com';
                $buyerPhone = $cart['gift_cart_order']['buyer_phone'] ?? '9999999999';

                $payload = [
                    'purpose'                 => 'Gift Card Purchase - ' . date('Y-m-d H:i:s'),
                    'amount'                  => $amount,
                    'currency'                => $currency,
                    'buyer_name'              => $buyerName,
                    'email'                   => $buyerEmail,
                    'phone'                   => $buyerPhone,
                    'send_email'              => true,
                    'send_sms'                => true,
                    'allow_repeated_payments' => false,
                    'redirect_url'            => route('giftcard.success'),
                ];

                $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);

                try {
                    // Get OAuth2 access token
                    $tokenResp = $client->post($baseUrl . 'oauth2/token/', [
                        'form_params' => [
                            'grant_type'    => 'client_credentials',
                            'client_id'     => $clientId,
                            'client_secret' => $clientSecret,
                        ],
                    ]);

                    $tokenData = json_decode($tokenResp->getBody(), true);
                    $accessToken = $tokenData['access_token'] ?? null;

                    if (!$accessToken) {
                        throw new \Exception('Failed to obtain Instamojo access token');
                    }

                    // Create Payment Request
                    $prResp = $client->post($baseUrl . 'payment-requests/', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type'  => 'application/json',
                        ],
                        'json' => $payload,
                    ]);

                    $result = json_decode($prResp->getBody(), true);

                    $paymentUrl = $result['payment_request']['longurl'] ?? null;
                    $requestId  = $result['payment_request']['id'] ?? null;

                    if (!$paymentUrl || !$requestId) {
                        throw new \Exception('No payment URL received from Instamojo');
                    }

                    // Store for validation
                    Session::put('instamojo_request_id', $requestId);
                    Session::put('instamojo_redirect_hash', md5($requestId . Auth::id()));
                    Session::save();

                    return redirect($paymentUrl);

                } catch (\Exception $e) {
                    \Log::error('Instamojo Error: ' . $e->getMessage());
                    Session::flash('payment_error', 'Instamojo: ' . $e->getMessage());
                    return redirect()->route('customize.giftcard');
                }
            }
            // FIXED: Paymongo
            else if ($cart['gift_cart_order']['payment_method'] == 'paymongo') {
                $paymongoSecretKey = $cart['gift_cart_order']['paymongo_secret_key'] ?? '';
                $paymongoIsSandbox = filter_var($cart['gift_cart_order']['paymongo_isSandbox'] ?? true, FILTER_VALIDATE_BOOLEAN);
                $total_pay = (float) $cart['gift_cart_order']['total_pay'];
                $currencyCode = $cart['gift_cart_order']['currencyData']['code'] ?? 'PHP';

                if (empty($paymongoSecretKey)) {
                    Session::flash('payment_error', 'PayMongo secret key is missing.');
                    return redirect()->route('customize.giftcard');
                }

                // Validate minimum amount
                $minAmount = ($currencyCode == 'PHP') ? 100 : 50;
                if ($total_pay < $minAmount) {
                    Session::flash('payment_error', "PayMongo requires minimum amount of {$minAmount} {$currencyCode}");
                    return redirect()->route('customize.giftcard');
                }

                // Store payment method in session for success callback
                Session::put('paymongo_payment_method', 'paymongo');
                Session::put('paymongo_secret_key', $paymongoSecretKey);
                Session::put('paymongo_is_sandbox', $paymongoIsSandbox);
                Session::save();

                $authorName = $cart['gift_cart_order']['authorName'] ?? Auth::user()->name ?? 'User';
                $formatted_price = $cart['gift_cart_order']['currencyData']['symbol'] .
                                number_format($total_pay, $cart['gift_cart_order']['currencyData']['decimal_degits'] ?? 2);

                return view('gift_card.paymongo', [
                    'is_checkout'       => 1,
                    'cart'              => $cart,
                    'id'                => $user->firebase_uid ?? $user->uuid,
                    'email'             => $email,
                    'authorName'        => $authorName,
                    'amount'            => $total_pay,
                    'paymongoSecretKey' => $paymongoSecretKey,
                    'paymongoIsSandbox' => $paymongoIsSandbox,
                    'currency'          => $currencyCode,
                    'gift_cart_order'   => $cart['gift_cart_order'],
                    'formatted_price'   => $formatted_price
                ]);
            }
            // FIXED: Foloosi
            else if ($cart['gift_cart_order']['payment_method'] == 'foloosi') {
                $foloosi_merchantKey = $cart['gift_cart_order']['foloosi_merchantKey'] ?? '';
                $total_pay = (float) $cart['gift_cart_order']['total_pay'];
                $currencyCode = $cart['gift_cart_order']['currencyData']['code'] ?? 'AED';

                if (empty($foloosi_merchantKey)) {
                    Session::flash('payment_error', 'Foloosi merchant key is missing.');
                    return redirect()->route('customize.giftcard');
                }

                if ($total_pay < 1) {
                    Session::flash('payment_error', 'Amount must be at least 1 ' . $currencyCode);
                    return redirect()->route('customize.giftcard');
                }

                $token = uniqid('folo_');
                $success_url = route('giftcard.success') . '?foloosi_token=' . $token;
                
                Session::put('foloosi_payment_token', $token);
                Session::put('foloosi_merchant_key', $foloosi_merchantKey);
                Session::save();

                $client = new Client(['timeout' => 30, 'verify' => false]);
                $payload = [
                    'currency'                  => $currencyCode,
                    'transaction_amount'        => $total_pay,
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
                    'description'               => 'Gift Card Purchase',
                    'partner_unique_reference'  => $token,
                ];

                try {
                    $response = $client->post('https://api.foloosi.com/aggregatorapi/web/initialize-setup', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'secret_key'   => $foloosi_merchantKey,
                        ],
                        'json' => $payload,
                    ]);

                    $result = json_decode($response->getBody(), true);

                    if (isset($result['data']['reference_token'])) {
                        $reference_token = $result['data']['reference_token'];
                        Session::put('foloosi_reference_token', $reference_token);
                        Session::save();

                        $formatted_price = $cart['gift_cart_order']['currencyData']['symbol'] .
                                        number_format($total_pay, $cart['gift_cart_order']['currencyData']['decimal_degits'] ?? 2);

                        return view('gift_card.foloosi', [
                            'is_checkout' => 1,
                            'cart' => $cart,
                            'id' => $user->firebase_uid ?? $user->uuid,
                            'email' => $email,
                            'authorName' => $cart['gift_cart_order']['authorName'] ?? Auth::user()->name ?? 'User',
                            'amount' => $total_pay,
                            'currency' => $currencyCode,
                            'reference_token' => $reference_token,
                            'foloosi_merchantKey' => $foloosi_merchantKey,
                            'gift_cart_order' => $cart['gift_cart_order'],
                            'formatted_price' => $formatted_price,
                        ]);
                    } else {
                        $errorMsg = $result['message'] ?? 'Invalid response from Foloosi';
                        throw new \Exception($errorMsg);
                    }
                } catch (\Exception $e) {
                    \Log::error('Foloosi Error: ' . $e->getMessage());
                    Session::flash('payment_error', 'Foloosi: ' . $e->getMessage());
                    return redirect()->route('customize.giftcard');
                }
            }
        } else {
            return redirect()->route('customize.giftcard');
        }
    }
    public function razorpaypayment(Request $request)
    {
        $input = $request->all();
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        $cart = Session::get('gift_cart', []);
        $api_secret = $cart['gift_cart_order']['razorpaySecret'];
        $api_key = $cart['gift_cart_order']['razorpayKey'];
        $api = new Api($api_key, $api_secret);
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::save();
            } catch (Exception $e) {
                return $e->getMessage();
                Session::put('error', $e->getMessage());
                return redirect()->back();
            }
        }
        Session::put('success', 'Payment successful');
        return redirect()->route('giftcard.success');
    }
    public function processStripePayment(Request $request)
    {
        $email = Auth::user()->email;
        $input = $request->all();
        $cart = Session::get('gift_cart', []);
        if (@$cart['gift_cart_order'] && $input['token_id']) {
            if ($cart['gift_cart_order']['stripeKey'] && $cart['gift_cart_order']['stripeSecret']) {
                $currency = "usd";
                if (@$cart['gift_cart_order']['currency']) {
                    $currency = $cart['gift_cart_order']['currency'];
                }
                $stripeSecret = $cart['gift_cart_order']['stripeSecret'];
                $stripe = new \Stripe\StripeClient($stripeSecret);
                $name = $input['name'];
                $address_line1 = $input['address_line1'];
                $address_line2 = $input['address_line2'];
                $address_city = $input['address_city'];
                $address_state = $input['address_state'];
                $address_country = $input['address_country'];
                $address_zipcode = $input['address_zipcode'];
                $description = env('APP_NAME', 'Foodie') . ' Order';
                $amount = bcmul($cart['gift_cart_order']['total_pay'], 100);
                try {
                    $charge = $stripe->paymentIntents->create([
                        'amount' => $amount,
                        'currency' => $currency,
                        'description' => $description,
                    ]);
                    $cart['payment_status'] = true;
                    Session::put('gift_cart', $cart);
                    Session::put('success', 'Payment successful');
                    Session::save();
                    $res = array('status' => true, 'data' => $charge, 'message' => 'success');
                    echo json_encode($res);
                    exit;
                } catch (Exception $e) {
                    $cart['payment_status'] = false;
                    Session::put('gift_cart', $cart);
                    Session::put('error', $e->getMessage());
                    Session::save();
                    $res = array('status' => false, 'message' => $e->getMessage());
                    echo json_encode($res);
                    exit;
                }
            }
        }
    }
    public function processPaypalPayment(Request $request)
    {
        $email = Auth::user()->email;
        $input = $request->all();
        $cart = Session::get('gift_cart', []);
        if (@$cart['gift_cart_order']) {
            if ($cart['gift_cart_order']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                $res = array('status' => true, 'data' => array(), 'message' => 'success');
                echo json_encode($res);
                exit;
            }
        }
        $cart['payment_status'] = false;
        Session::put('gift_cart', $cart);
        Session::put('error', 'Faild Payment');
        Session::save();
        $res = array('status' => false, 'message' => 'Faild Payment');
        echo json_encode($res);
        exit;
    }
    private function getAccessToken($clientId,$clientSecret)
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
 
    public function success()
    {
        // First, check if this is a PhonePe callback
        if (isset($_GET['phonepe_token'])) {
            
            $phonepe_payment = Session::get('phonepe_merchant_transaction_id');
            
            // Validate the token
            if ($phonepe_payment && $phonepe_payment == $_GET['phonepe_token']) {
                
                // Try to restore cart from backup if needed
                $cart = Session::get('gift_cart', []);
                if (empty($cart) || !isset($cart['gift_cart_order'])) {
                    $cartBackupToken = Session::get('gift_cart_backup_token');
                    if ($cartBackupToken) {
                        $backupCart = Session::get('gift_cart_backup_data_' . $cartBackupToken);
                        if ($backupCart) {
                            $cart = $backupCart;
                            Session::put('gift_cart', $cart);
                            
                            // Clean up backup
                            Session::forget('gift_cart_backup_data_' . $cartBackupToken);
                            Session::forget('gift_cart_backup_token');
                        }
                    }
                }
                
                // Set payment status
                if (isset($cart['gift_cart_order'])) {
                    $cart['payment_status'] = true;
                    Session::put('gift_cart', $cart);
                    Session::put('success', 'Payment successful');
                    Session::save();
                }
                
                // Clean up PhonePe session data
                Session::forget('phonepe_merchant_transaction_id');
                Session::forget('phonepe_salt_key');
                Session::forget('phonepe_salt_index');
                Session::forget('phonepe_is_sandbox');
                Session::forget('phonepe_merchant_id');
                Session::forget('phonepe_pending_transaction');
                
                // CRITICAL: Get the latest cart after all updates
                $cart = Session::get('gift_cart', []);
                $email = Auth::user()->email;
                $user = VendorUsers::where('email', $email)->first();
                $userId = $user ? ($user->uuid ?? 'guest') : 'guest';
                $userEmail = $email ?? 'guest@example.com';
                $payment_method = (@$cart['gift_cart_order']['payment_method']) ? $cart['gift_cart_order']['payment_method'] : 'phonepe';
                
                // Return the success view directly - NO REDIRECT
                return view('gift_card.success', [
                    'cart' => $cart,
                    'id' => $userId,
                    'email' => $userEmail,
                    'payment_method' => $payment_method
                ]);
            } else {
                \Log::error('PhonePe token mismatch', [
                    'session_token' => $phonepe_payment,
                    'get_token' => $_GET['phonepe_token']
                ]);
                Session::flash('payment_error', 'Invalid payment session. Please contact support.');
                return redirect()->route('customize.giftcard');
            }
        }
        
        // For all other payment methods, get the cart normally
        $cart = Session::get('gift_cart', []);
        $email = Auth::user()->email;
        $user = VendorUsers::where('email', $email)->first();
        
        // Check for Cashfree success
        if (isset($_GET['cashfree_token'])) {
            $sessionToken = Session::get('cashfree_payment_token');
            if ($sessionToken && $sessionToken === $_GET['cashfree_token']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                Session::forget('cashfree_payment_token');
            } else {
                Session::flash('payment_error', 'Invalid Cashfree token');
                return redirect()->route('customize.giftcard');
            }
        }
        
        // Check for Instamojo success
        if (isset($_GET['payment_request_id'])) {
            $requestIdFromUrl = $_GET['payment_request_id'];
            $sessionRequestId = Session::get('instamojo_request_id');
            
            if ($requestIdFromUrl && $sessionRequestId && $requestIdFromUrl === $sessionRequestId) {
                if (isset($_GET['payment_status']) && $_GET['payment_status'] == 'Credit') {
                    $cart['payment_status'] = true;
                    Session::put('gift_cart', $cart);
                    Session::put('success', 'Payment successful');
                    Session::save();
                } else {
                    Session::flash('payment_error', 'Instamojo payment was not successful');
                    return redirect()->route('customize.giftcard');
                }
                Session::forget('instamojo_request_id');
            } else {
                Session::flash('payment_error', 'Invalid Instamojo payment request');
                return redirect()->route('customize.giftcard');
            }
        }
        
        // Check for Foloosi success
        if (isset($_GET['foloosi_token'])) {
            $sessionToken = Session::get('foloosi_payment_token');
            if ($sessionToken && $sessionToken === $_GET['foloosi_token']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                Session::forget('foloosi_payment_token');
                Session::forget('foloosi_reference_token');
            } else {
                Session::flash('payment_error', 'Invalid Foloosi token');
                return redirect()->route('customize.giftcard');
            }
        }
        
        // Check for PayMongo success
        if (isset($_GET['pm_token'])) {
            $sessionToken = Session::get('paymongo_payment_token');
            if ($sessionToken && $sessionToken === $_GET['pm_token']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                Session::forget('paymongo_payment_token');
                Session::forget('paymongo_secret_key');
                Session::forget('paymongo_is_sandbox');
            } else {
                Session::flash('payment_error', 'Invalid PayMongo token');
                return redirect()->route('customize.giftcard');
            }
        }
        
        // Check for MTN MoMo success
        if (isset($_GET['mtnmomo_request_ref'])) {
            $submittedRef = $_GET['mtnmomo_request_ref'];
            $sessionRef = Session::get('mtnmomo_request_ref');
            
            if ($submittedRef && $sessionRef && $submittedRef === $sessionRef) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                Session::forget('mtnmomo_request_ref');
                Session::forget('mtnmomo_access_token');
                Session::forget('mtnmomo_target_env');
            } else {
                Session::flash('payment_error', 'Invalid MTN MoMo payment reference');
                return redirect()->route('customize.giftcard');
            }
        }
        
        if (isset($_GET['xendit_token'])) {
            $xendit_payment = Session::get('xendit_payment_token');
            if ($xendit_payment == $_GET['xendit_token']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                Session::forget('xendit_payment_token');
                
                // Return success view immediately
                return view('gift_card.success', ['cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'payment_method' => 'xendit']);
            }
        }
        
        if (isset($_GET['midtrans_token'])) {
            $midtrans_payment = Session::get('midtrans_payment_token');
            $urlToken = explode('?', request('midtrans_token'))[0];
            if ($urlToken === $midtrans_payment) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
                Session::forget('midtrans_payment_token');
                
                // Return success view immediately
                return view('gift_card.success', ['cart' => $cart, 'id' => $user->uuid, 'email' => $email, 'payment_method' => 'midtrans']);
            }
        }
        
        if (isset($_GET['orangepay_token'])) {
            $orangepay_token = Session::get('orangepay_payment_token');
            if ($orangepay_token === $_GET['orangepay_token']) {
                $orangepay_access_token = Session::get('orangepay_access_token');
                $payToken = session('orangepay_payment_check_token');
                $orangepay_isSandbox = session('orangepay_isSandbox');
                $fail_url = route('giftcard.pay');
                if (!$payToken && !$orangepay_access_token) {
                    return response()->json(['error' => 'Payment token not found in session']);
                }
                $url = ($orangepay_isSandbox == false) ? 'https://api.orange.com/orange-money-webpay/cm/v1/transactionstatus' : 'https://api.orange.com/orange-money-webpay/dev/v1/transactionstatus';
                try {
                    $client = new Client();
                    $payload = [
                        'pay_token' => $payToken
                    ];
                    $response = $client->post($url, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $orangepay_access_token,
                            'Content-Type' => 'application/json',
                        ],
                        'body' => json_encode($payload),
                    ]);
                    $responseBody = json_decode($response->getBody(), true);
                    if (isset($responseBody['status']) && $responseBody['status'] == 'SUCCESS') {
                        $cart['payment_status'] = true;
                        Session::put('gift_cart', $cart);
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
        
        if (isset($_GET['token'])) {
            $payfast_payment = Session::get('payfast_payment_token');
            if ($payfast_payment == $_GET['token']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
        
        if (isset($_GET['reference'])) {
            $paystack_reference = Session::get('paystack_reference');
            $paystack_access_code = Session::get('paystack_access_code');
            if ($paystack_reference == $_GET['reference']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            }
        }
        
        if (isset($_GET['transaction_id']) && isset($_GET['tx_ref']) && isset($_GET['status'])) {
            $flutterwave_pay_tx_ref = Session::get('flutterwave_pay_tx_ref');
            if ($_GET['status'] == 'successful' && $flutterwave_pay_tx_ref == $_GET['tx_ref']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            } else {
                return redirect()->route('buy-gift-card');
            }
        }
        
        if (isset($_GET['preference_id']) && isset($_GET['payment_id']) && isset($_GET['status'])) {
            $mercadopago_preference_id = Session::get('mercadopago_preference_id');
            if ($_GET['status'] == 'approved' && $mercadopago_preference_id == $_GET['preference_id']) {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Payment successful');
                Session::save();
            } else {
                return redirect()->route('buy-gift-card');
            }
        }
        
        if (@$cart['gift_cart_order']['payment_method'] && $cart['gift_cart_order']['payment_method'] == "wallet") {
            $cart['payment_status'] = true;
            Session::put('gift_cart', $cart);
            Session::put('success', 'Payment successful');
            Session::save();
        }
        
        // Clean up MTN MoMo session keys if payment was successful
        if (@$cart['gift_cart_order']['payment_method'] == 'mtnmomo' && @$cart['payment_status'] == true) {
            Session::forget('mtnmomo_request_ref');
            Session::forget('mtnmomo_access_token');
            Session::forget('mtnmomo_target_env');
            Session::forget('mtnmomo_apiuser_ref');
            Session::forget('mtnmomo_poll_start');
            Session::save();
        }
        
        // Get the final cart data
        $cart = Session::get('gift_cart', []);
        $payment_method = (@$cart['gift_cart_order']['payment_method']) ? $cart['gift_cart_order']['payment_method'] : 'cod';
        $userId = $user ? ($user->uuid ?? 'guest') : 'guest';
        $userEmail = $email ?? 'guest@example.com';
        
        return view('gift_card.success', [
            'cart' => $cart,
            'id' => $userId,
            'email' => $userEmail,
            'payment_method' => $payment_method
        ]);
    }
    public function giftcards()
    {
        return view('gift_card.my_giftcards');
    }

    // Gift Card MTN MoMo Methods
    public function giftCardMtnmomo(Request $request, $id)
    {
        $cart = Session::get('gift_cart', []);
        
        if (!isset($cart['gift_cart_order'])) {
            return redirect()->route('customize.giftcard')
                ->with('payment_error', 'No active gift card session found.');
        }

        $settings = $cart['gift_cart_order']['mtnmomo'] ?? [];

        if (empty($settings['enable']) || !$settings['enable']) {
            return redirect()->route('customize.giftcard')
                ->with('payment_error', 'MTN MoMo is not configured.');
        }

        $amount = (float) $cart['gift_cart_order']['total_pay'];
        $currency = $cart['gift_cart_order']['currencyData']['code'] ?? 'XOF';
        $formatted_price = $cart['gift_cart_order']['currencyData']['symbol'] . 
                          number_format($amount, $cart['gift_cart_order']['currencyData']['decimal_degits'] ?? 2);

        // We will get phone number in blade → then call requestToPay
        return view('gift_card.mtnmomo', compact(
            'cart',
            'amount',
            'currency',
            'formatted_price',
            'settings'
        ));
    }

    // In GiftCardController.php, add logging to MTN MoMo methods:

    public function giftcardMtnmomoRequestPayment(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:8|max:15',
        ]);

        $cart = Session::get('gift_cart', []);

        if (empty($cart['gift_cart_order'])) {
            return redirect()->route('customize.giftcard');
        }
       
        $settings = $cart['gift_cart_order']['mtnmomo'] ?? [];
        $amount   = (float) $cart['gift_cart_order']['total_pay'];
        $currency = 'EUR'; // MTN MoMo only supports EUR in sandbox

        $phone = trim($request->phone);
        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        try {
            // Debug: Check settings            

            if (empty($settings['primaryKey'])) {
                throw new \Exception('MTN MoMo primary key is not configured');
            }

            $baseUrl = $settings['isSandbox'] ? 'https://sandbox.momodeveloper.mtn.com' : 'https://proxy.momoapi.mtn.com';

            $client = new Client(['timeout' => 30, 'verify' => false]);

            // ── 1. Create API User
            $apiUserRef = (string) Str::uuid();
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
                throw new \Exception('Failed to create API User. Status: ' . $response->getStatusCode());
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
            $requestRef = (string) Str::uuid();
            Session::put('mtnmomo_request_ref', $requestRef);
            Session::save(); // Ensure session is saved            
            
            $payload = [
                "amount"      => (string) $amount,
                "currency"    => $currency,
                "externalId"  => 'giftcard-' . time(),
                "payer"       => [
                    "partyIdType" => "MSISDN",
                    "partyId"     => $phone
                ],
                "payerMessage"=> "Gift Card Purchase - " . config('app.name'),
                "payeeNote"   => "Gift Card Purchase"
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

    public function giftcardMtnmomoCheckStatus(Request $request)
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

        $cart = Session::get('gift_cart', []);
        $settings = $cart['gift_cart_order']['mtnmomo'] ?? [];

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
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('success', 'Gift Card purchased successfully via MTN MoMo');
                Session::save();

                // Don't clean up session keys immediately - let JavaScript handle redirect
                // Session cleanup will happen on the next request after redirect

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful',
                    'redirect' => route('giftcard.success')
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

        public function giftcardWalletPaymongo(Request $request)
    {
        try {
            $input = $request->all();
            $cart = Session::get('gift_cart', []);
            $paymongoSecretKey = Session::get('paymongo_secret_key');
            $isSandbox = Session::get('paymongo_is_sandbox');
            
            if (empty($paymongoSecretKey)) {
                return response()->json(['status' => false, 'message' => 'PayMongo not configured'], 400);
            }

            // Validate card input
            $cardNumber = preg_replace('/\s+/', '', $input['card_number']);
            $expMonth = str_pad($input['exp_month'], 2, '0', STR_PAD_LEFT);
            $expYear = $input['exp_year'];
            $cvc = $input['cvc'];
            $name = $input['name'];

            // Create payment intent with PayMongo
            $baseUrl = $isSandbox 
                ? 'https://api.paymongo.com/v1/' 
                : 'https://api.paymongo.com/v1/';

            $client = new \GuzzleHttp\Client(['verify' => false]);

            // Step 1: Create payment method
            $paymentMethodResponse = $client->post($baseUrl . 'payment_methods', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($paymongoSecretKey . ':'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'data' => [
                        'attributes' => [
                            'details' => [
                                'card_number' => $cardNumber,
                                'exp_month' => (int)$expMonth,
                                'exp_year' => (int)$expYear,
                                'cvc' => $cvc,
                            ],
                            'type' => 'card',
                            'billing' => [
                                'name' => $name,
                            ]
                        ]
                    ]
                ]
            ]);

            $pmResult = json_decode($paymentMethodResponse->getBody(), true);
            $paymentMethodId = $pmResult['data']['id'] ?? null;

            if (!$paymentMethodId) {
                return response()->json(['status' => false, 'message' => 'Failed to create payment method'], 400);
            }

            // Step 2: Create payment intent
            $amount = (float) $cart['gift_cart_order']['total_pay'];
            $currency = $cart['gift_cart_order']['currencyData']['code'] ?? 'PHP';
            
            // Convert amount to cents
            $amountInCents = (int)($amount * 100);

            $paymentIntentResponse = $client->post($baseUrl . 'payment_intents', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($paymongoSecretKey . ':'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'data' => [
                        'attributes' => [
                            'amount' => $amountInCents,
                            'payment_method_allowed' => ['card'],
                            'payment_method_options' => [
                                'card' => [
                                    'request_three_d_secure' => 'any'
                                ]
                            ],
                            'currency' => strtoupper($currency),
                            'description' => 'Gift Card Purchase',
                            'statement_descriptor' => 'Gift Card',
                        ]
                    ]
                ]
            ]);

            $piResult = json_decode($paymentIntentResponse->getBody(), true);
            $paymentIntentId = $piResult['data']['id'] ?? null;
            $clientKey = $piResult['data']['attributes']['client_key'] ?? null;

            if (!$paymentIntentId || !$clientKey) {
                return response()->json(['status' => false, 'message' => 'Failed to create payment intent'], 400);
            }

            // Step 3: Attach payment method to payment intent
            $attachResponse = $client->post($baseUrl . 'payment_intents/' . $paymentIntentId . '/attach', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($paymongoSecretKey . ':'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'data' => [
                        'attributes' => [
                            'payment_method' => $paymentMethodId,
                            'client_key' => $clientKey,
                            'return_url' => route('giftcard.success') . '?pm_token=' . Session::get('paymongo_payment_token', ''),
                        ]
                    ]
                ]
            ]);

            $attachResult = json_decode($attachResponse->getBody(), true);
            
            if (isset($attachResult['data']['attributes']['status']) && 
                $attachResult['data']['attributes']['status'] === 'succeeded') {
                $cart['payment_status'] = true;
                Session::put('gift_cart', $cart);
                Session::put('paymongo_payment_token', uniqid('pm_'));
                Session::save();
                
                return response()->json([
                    'status' => true, 
                    'message' => 'Payment successful',
                    'redirect' => route('giftcard.success')
                ]);
            }

            return response()->json(['status' => false, 'message' => 'Payment failed'], 400);

        } catch (\Exception $e) {
            \Log::error('PayMongo Payment Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
  

    // Gift Card PayMongo method (add this after giftcardWalletPaymongo method)
    public function giftCardPaymongo()
    {
        $cart = Session::get('gift_cart', []);
        
        if (!isset($cart['gift_cart_order'])) {
            Session::flash('payment_error', 'No active gift card session found.');
            return redirect()->route('customize.giftcard');
        }
        
        $paymongoSecretKey = $cart['gift_cart_order']['paymongo_secret_key'] ?? '';
        $paymongoIsSandbox = filter_var($cart['gift_cart_order']['paymongo_isSandbox'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $total_pay = (float) $cart['gift_cart_order']['total_pay'];
        $currencyCode = $cart['gift_cart_order']['currencyData']['code'] ?? 'PHP';
        
        if (empty($paymongoSecretKey)) {
            Session::flash('payment_error', 'PayMongo secret key is missing.');
            return redirect()->route('customize.giftcard');
        }
        
        Session::put('paymongo_payment_method', 'paymongo');
        Session::put('paymongo_secret_key', $paymongoSecretKey);
        Session::put('paymongo_is_sandbox', $paymongoIsSandbox);
        Session::save();
        
        $authorName = Auth::user()->name ?? 'User';
        $formatted_price = $cart['gift_cart_order']['currencyData']['symbol'] .
                        number_format($total_pay, $cart['gift_cart_order']['currencyData']['decimal_degits'] ?? 2);
        
        return view('gift_card.paymongo', [
            'cart' => $cart,
            'authorName' => $authorName,
            'amount' => $total_pay,
            'paymongoSecretKey' => $paymongoSecretKey,
            'paymongoIsSandbox' => $paymongoIsSandbox,
            'currency' => $currencyCode,
            'formatted_price' => $formatted_price,
        ]);
    }

    // Gift Card Foloosi method
    public function giftCardFoloosi()
    {
        $cart = Session::get('gift_cart', []);
        
        if (!isset($cart['gift_cart_order'])) {
            Session::flash('payment_error', 'No active gift card session found.');
            return redirect()->route('customize.giftcard');
        }
        
        $foloosi_merchantKey = $cart['gift_cart_order']['foloosi_merchantKey'] ?? '';
        $total_pay = (float) $cart['gift_cart_order']['total_pay'];
        $currencyCode = $cart['gift_cart_order']['currencyData']['code'] ?? 'AED';
        
        if (empty($foloosi_merchantKey)) {
            Session::flash('payment_error', 'Foloosi merchant key is missing.');
            return redirect()->route('customize.giftcard');
        }
        
        $token = uniqid('folo_');
        $success_url = route('giftcard.success') . '?foloosi_token=' . $token;
        
        Session::put('foloosi_payment_token', $token);
        Session::put('foloosi_merchant_key', $foloosi_merchantKey);
        Session::save();
        
        $client = new Client(['timeout' => 30, 'verify' => false]);
        $payload = [
            'currency' => $currencyCode,
            'transaction_amount' => $total_pay,
            'customer_name' => Auth::user()->name ?? 'User',
            'site_return_url' => $success_url,
            'customer_unique_identifier' => Auth::id(),
            'billing_country' => 'AE',
            'billing_postal_code' => '00000',
            'billing_state' => 'Dubai',
            'customer_city' => 'Dubai',
            'customer_address' => 'Customer Address',
            'customer_mobile' => Auth::user()->phone ?? '971123456789',
            'customer_email' => Auth::user()->email,
            'description' => 'Gift Card Purchase',
            'partner_unique_reference' => $token,
        ];
        
        try {
            $response = $client->post('https://api.foloosi.com/aggregatorapi/web/initialize-setup', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'secret_key' => $foloosi_merchantKey,
                ],
                'json' => $payload,
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            if (isset($result['data']['reference_token'])) {
                $reference_token = $result['data']['reference_token'];
                Session::put('foloosi_reference_token', $reference_token);
                Session::save();
                
                $formatted_price = $cart['gift_cart_order']['currencyData']['symbol'] .
                                number_format($total_pay, $cart['gift_cart_order']['currencyData']['decimal_degits'] ?? 2);
                
                return view('gift_card.foloosi', [
                    'cart' => $cart,
                    'amount' => $total_pay,
                    'currency' => $currencyCode,
                    'reference_token' => $reference_token,
                    'formatted_price' => $formatted_price,
                ]);
            } else {
                throw new \Exception($result['message'] ?? 'Invalid response from Foloosi');
            }
        } catch (\Exception $e) {
            \Log::error('Foloosi Error: ' . $e->getMessage());
            Session::flash('payment_error', 'Foloosi: ' . $e->getMessage());
            return redirect()->route('customize.giftcard');
        }
    }

    // Gift Card Cashfree method
    public function giftCardCashfree()
    {
        $cart = Session::get('gift_cart', []);
        
        if (!isset($cart['gift_cart_order'])) {
            Session::flash('payment_error', 'No active gift card session found.');
            return redirect()->route('customize.giftcard');
        }
        
        $cashfree_clientId = $cart['gift_cart_order']['cashfree_clientId'] ?? '';
        $cashfree_clientSecret = $cart['gift_cart_order']['cashfree_clientSecret'] ?? '';
        $cashfree_isSandbox = $cart['gift_cart_order']['cashfree_isSandbox'] ?? false;
        
        if (empty($cashfree_clientId) || empty($cashfree_clientSecret)) {
            Session::flash('payment_error', 'Cashfree credentials missing');
            return redirect()->route('customize.giftcard');
        }
        
        $authorName = Auth::user()->name ?? 'User';
        $email = Auth::user()->email ?? 'noemail@example.com';
        $phone = Auth::user()->phone ?? '9999999999';
        $total_pay = (float) $cart['gift_cart_order']['total_pay'];
        $currency = 'INR';
        
        $baseUrl = ($cashfree_isSandbox == "true" || $cashfree_isSandbox === true)
            ? "https://sandbox.cashfree.com/pg/links"
            : "https://api.cashfree.com/pg/links";
        
        $token = uniqid('cf_');
        $success_url = route('giftcard.success') . '?cashfree_token=' . $token;
        
        Session::put('cashfree_payment_token', $token);
        Session::save();
        
        $payload = [
            'link_id' => $token,
            'link_amount' => $total_pay,
            'link_currency' => strtoupper($currency),
            'link_purpose' => 'Gift Card Purchase',
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
                    'x-client-id' => $cashfree_clientId,
                    'x-client-secret' => $cashfree_clientSecret,
                    'x-api-version' => '2023-08-01',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            if (isset($result['link_url']) && $result['link_url']) {
                return redirect($result['link_url']);
            }
            
            Session::flash('payment_error', 'Cashfree link creation failed');
            return redirect()->route('customize.giftcard');
            
        } catch (\Exception $e) {
            \Log::error('Cashfree Error: ' . $e->getMessage());
            Session::flash('payment_error', 'Cashfree Error: ' . $e->getMessage());
            return redirect()->route('customize.giftcard');
        }
    }

    // Gift Card Instamojo method
    public function giftCardInstamojo()
    {
        $cart = Session::get('gift_cart', []);
        
        if (!isset($cart['gift_cart_order'])) {
            Session::flash('payment_error', 'No active gift card session found.');
            return redirect()->route('customize.giftcard');
        }
        
        $isSandbox = filter_var($cart['gift_cart_order']['instamojo_isSandbox'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $clientId = $cart['gift_cart_order']['instamojo_clientId'] ?? '';
        $clientSecret = $cart['gift_cart_order']['instamojo_clientSecret'] ?? '';
        
        if (empty($clientId) || empty($clientSecret)) {
            Session::flash('payment_error', 'Instamojo credentials are missing.');
            return redirect()->route('customize.giftcard');
        }
        
        $baseUrl = $isSandbox ? 'https://test.instamojo.com/v2/' : 'https://api.instamojo.com/v2/';
        $amount = (float) ($cart['gift_cart_order']['total_pay'] ?? 0);
        $currency = 'INR';
        
        if ($currency !== 'INR') {
            Session::flash('payment_error', 'Instamojo supports only INR currency.');
            return redirect()->route('customize.giftcard');
        }
        
        $buyerName = Auth::user()->name ?? 'Guest';
        $buyerEmail = Auth::user()->email ?? 'no-reply@example.com';
        $buyerPhone = Auth::user()->phone ?? '9999999999';
        
        $payload = [
            'purpose' => 'Gift Card Purchase - ' . date('Y-m-d H:i:s'),
            'amount' => $amount,
            'currency' => $currency,
            'buyer_name' => $buyerName,
            'email' => $buyerEmail,
            'phone' => $buyerPhone,
            'send_email' => true,
            'send_sms' => true,
            'allow_repeated_payments' => false,
            'redirect_url' => route('giftcard.success'),
        ];
        
        $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);
        
        try {
            $tokenResp = $client->post($baseUrl . 'oauth2/token/', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ],
            ]);
            
            $tokenData = json_decode($tokenResp->getBody(), true);
            $accessToken = $tokenData['access_token'] ?? null;
            
            if (!$accessToken) {
                throw new \Exception('Failed to obtain Instamojo access token');
            }
            
            $prResp = $client->post($baseUrl . 'payment-requests/', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);
            
            $result = json_decode($prResp->getBody(), true);
            $paymentUrl = $result['payment_request']['longurl'] ?? null;
            $requestId = $result['payment_request']['id'] ?? null;
            
            if (!$paymentUrl || !$requestId) {
                throw new \Exception('No payment URL received from Instamojo');
            }
            
            Session::put('instamojo_request_id', $requestId);
            Session::save();
            
            return redirect($paymentUrl);
            
        } catch (\Exception $e) {
            \Log::error('Instamojo Error: ' . $e->getMessage());
            Session::flash('payment_error', 'Instamojo: ' . $e->getMessage());
            return redirect()->route('customize.giftcard');
        }
    }
}

@include('layouts.app')
@include('layouts.header')
<div class="siddhi-popular">
    <div class="container">
        <div class="transactions-banner p-4 rounded">
            <div class="row align-items-center text-center">
                <div class="col-md-12">
                    <h3 class="font-weight-bold h4 text-light" id="total_payment"></h3>
                </div>
                <div class="col-md-12">
                    <button class="btn btn-light">
                        <a data-toggle="modal" data-target="#add_wallet_money"
                            class="d-flex w-100 align-items-center border-bottom p-3">
                            <div class="left mr-3 text-green">
                                <h6 class="font-weight-bold mb-1 text-dark">{{trans('lang.add_wallet_money')}}</h6>
                            </div>
                            <div class="right ml-auto">
                                <h6 class="font-weight-bold m-0"><i class="feather-chevron-right"></i></h6>
                            </div>
                        </a>
                    </button>
                </div>
            </div>
        </div>
        @if(session('payment_error') || !empty($errorMessage))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="feather-alert-circle mr-2"></i>
                <strong>Payment Error!</strong> 
                {{ session('payment_error') ?: $errorMessage }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @php 
                session()->forget('payment_error');
            @endphp
        @endif
        @if(session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif
        <div class="text-center py-5 not_found_div" style="display:none">
            <p class="h4 mb-4"><i class="feather-search bg-primary rounded p-2"></i></p>
            <p class="font-weight-bold text-dark h5">{{trans('lang.nothing_found')}}</p>
            <p>{{trans('lang.please_try_again')}}</p>
        </div>
        <div id="append_list1" class="res-search-list"></div>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="container mt-4 mb-4 p-0">
                    <div class="data-table_paginate">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                <li class="page-item ">
                                    <a class="page-link" href="javascript:void(0);" id="users_table_previous_btn"
                                       onclick="prev()" data-dt-idx="0" tabindex="0">{{trans('lang.previous')}}</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0);" id="users_table_next_btn"
                                       onclick="next()" data-dt-idx="2" tabindex="0">{{trans('lang.next')}}</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add_wallet_money" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('lang.add_wallet_money') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="col-md-12 error_top_pass"></div>
                    <div class="col-md-12 form-group">
                        <label class="form-label">{{ trans('lang.amount') }}</label>
                        <div class="control-inner">
                            <!-- <div class="currentCurrency"></div> -->
                            <input type="number" class="form-control wallet_amount">
                        </div>
                    </div>
                    <div class="accordion col-md-12 mb-3 rounded shadow-sm bg-white border" id="accordionExample">
                        <div class="siddhi-card overflow-hidden checkout-payment-options">
                            <!-- Razorpay -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="razorpay_box">
                                <input type="radio" name="payment_method" id="razorpay" value="razorpay" class="custom-control-input" checked>
                                <label class="custom-control-label" for="razorpay">{{ trans('lang.razorpay') }}</label>
                                <input type="hidden" id="isEnabled">
                                <input type="hidden" id="isSandboxEnabled">
                                <input type="hidden" id="razorpayKey">
                                <input type="hidden" id="razorpaySecret">
                            </div>
                            <!-- Stripe -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="stripe_box">
                                <input type="radio" name="payment_method" id="stripe" value="stripe" class="custom-control-input">
                                <label class="custom-control-label" for="stripe">{{ trans('lang.stripe') }}</label>
                                <input type="hidden" id="isStripeSandboxEnabled">
                                <input type="hidden" id="stripeKey">
                                <input type="hidden" id="stripeSecret">
                            </div>
                            <!-- PayPal -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="paypal_box">
                                <input type="radio" name="payment_method" id="paypal" value="paypal" class="custom-control-input">
                                <label class="custom-control-label" for="paypal">{{ trans('lang.pay_pal') }}</label>
                                <input type="hidden" id="ispaypalSandboxEnabled">
                                <input type="hidden" id="paypalKey">
                                <input type="hidden" id="paypalSecret">
                            </div>
                            <!-- Payfast -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="payfast_box">
                                <input type="radio" name="payment_method" id="payfast" value="payfast" class="custom-control-input">
                                <label class="custom-control-label" for="payfast">{{ trans('lang.pay_fast') }}</label>
                                <input type="hidden" id="payfast_isEnabled">
                                <input type="hidden" id="payfast_isSandbox">
                                <input type="hidden" id="payfast_merchant_key">
                                <input type="hidden" id="payfast_merchant_id">
                                <input type="hidden" id="payfast_notify_url">
                                <input type="hidden" id="payfast_return_url">
                                <input type="hidden" id="payfast_cancel_url">
                            </div>
                            <!-- Paystack -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="paystack_box">
                                <input type="radio" name="payment_method" id="paystack" value="paystack" class="custom-control-input">
                                <label class="custom-control-label" for="paystack">{{ trans('lang.pay_stack') }}</label>
                                <input type="hidden" id="paystack_isEnabled">
                                <input type="hidden" id="paystack_isSandbox">
                                <input type="hidden" id="paystack_public_key">
                                <input type="hidden" id="paystack_secret_key">
                            </div>
                            <!-- FlutterWave -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="flutterWave_box">
                                <input type="radio" name="payment_method" id="flutterwave" value="flutterwave" class="custom-control-input">
                                <label class="custom-control-label" for="flutterwave">{{ trans('lang.flutter_wave') }}</label>
                                <input type="hidden" id="flutterWave_isEnabled">
                                <input type="hidden" id="flutterWave_isSandbox">
                                <input type="hidden" id="flutterWave_encryption_key">
                                <input type="hidden" id="flutterWave_public_key">
                                <input type="hidden" id="flutterWave_secret_key">
                            </div>
                            <!-- MercadoPago -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="mercadopago_box">
                                <input type="radio" name="payment_method" id="mercadopago" value="mercadopago" class="custom-control-input">
                                <label class="custom-control-label" for="mercadopago">{{ trans('lang.mercadopago') }}</label>
                                <input type="hidden" id="mercadopago_isEnabled">
                                <input type="hidden" id="mercadopago_isSandbox">
                                <input type="hidden" id="mercadopago_public_key">
                                <input type="hidden" id="mercadopago_access_token">
                                <input type="hidden" id="title">
                                <input type="hidden" id="quantity">
                                <input type="hidden" id="unit_price">
                            </div>
                            <!-- Xendit -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="xendit_box">
                                <input type="radio" name="payment_method" id="xendit" value="xendit" class="custom-control-input">
                                <label class="custom-control-label" for="xendit">{{ trans('lang.xendit') }}</label>
                                <input type="hidden" id="xendit_enable">
                                <input type="hidden" id="xendit_apiKey">
                                <input type="hidden" id="xendit_image">
                                <input type="hidden" id="xendit_isSandbox">
                            </div>
                            <!-- Midtrans -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="midtrans_box">
                                <input type="radio" name="payment_method" id="midtrans" value="midtrans" class="custom-control-input">
                                <label class="custom-control-label" for="midtrans">{{ trans('lang.midtrans') }}</label>
                                <input type="hidden" id="midtrans_enable">
                                <input type="hidden" id="midtrans_serverKey">
                                <input type="hidden" id="midtrans_image">
                                <input type="hidden" id="midtrans_isSandbox">
                            </div>
                            <!-- OrangePay -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="orangepay_box">
                                <input type="radio" name="payment_method" id="orangepay" value="orangepay" class="custom-control-input">
                                <label class="custom-control-label" for="orangepay">{{ trans('lang.orangepay') }}</label>
                                <input type="hidden" id="orangepay_auth">
                                <input type="hidden" id="orangepay_clientId">
                                <input type="hidden" id="orangepay_clientSecret">
                                <input type="hidden" id="orangepay_image">
                                <input type="hidden" id="orangepay_isSandbox">
                                <input type="hidden" id="orangepay_merchantKey">
                                <input type="hidden" id="orangepay_cancelUrl">
                                <input type="hidden" id="orangepay_notifyUrl">
                                <input type="hidden" id="orangepay_returnUrl">
                                <input type="hidden" id="orangepay_enable">
                            </div>
                            <!-- PhonePe -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="phonepe_box">
                                <input type="radio" name="payment_method" id="phonepe" value="phonepe" class="custom-control-input">
                                <label class="custom-control-label" for="phonepe">PhonePe</label>
                                <input type="hidden" id="phonepe_enable">
                                <input type="hidden" id="phonepe_merchantId">
                                <input type="hidden" id="phonepe_saltKey">
                                <input type="hidden" id="phonepe_saltIndex">
                                <input type="hidden" id="phonepe_isSandbox">
                            </div>
                            <!-- Cashfree -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="cashfree_box">
                                <input type="radio" name="payment_method" id="cashfree" value="cashfree" class="custom-control-input">
                                <label class="custom-control-label" for="cashfree">Cashfree</label>
                                <input type="hidden" id="cashfree_enable">
                                <input type="hidden" id="cashfree_clientId">
                                <input type="hidden" id="cashfree_clientSecret">
                                <input type="hidden" id="cashfree_isSandbox">
                            </div>
                            <!-- Instamojo -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="instamojo_box">
                                <input type="radio" name="payment_method" id="instamojo" value="instamojo" class="custom-control-input">
                                <label class="custom-control-label" for="instamojo">Instamojo</label>
                                <input type="hidden" id="instamojo_enable">
                                <input type="hidden" id="instamojo_api_key">
                                <input type="hidden" id="instamojo_auth_token">
                                <input type="hidden" id="instamojo_isSandbox">
                            </div>
                            <!-- Foloosi -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="foloosi_box">
                                <input type="radio" name="payment_method" id="foloosi" value="foloosi" class="custom-control-input">
                                <label class="custom-control-label" for="foloosi">Foloosi</label>
                                <input type="hidden" id="foloosi_enable">
                                <input type="hidden" id="foloosi_merchant_key">
                                <input type="hidden" id="foloosi_secret_key">
                                <input type="hidden" id="foloosi_isSandbox">
                            </div>
                            <!-- PayMongo -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="paymongo_box">
                                <input type="radio" name="payment_method" id="paymongo" value="paymongo" class="custom-control-input">
                                <label class="custom-control-label" for="paymongo">PayMongo</label>
                                <input type="hidden" id="paymongo_enable">
                                <input type="hidden" id="paymongo_public_key">
                                <input type="hidden" id="paymongo_secret_key">
                                <input type="hidden" id="paymongo_isSandbox">
                            </div>
                            <!-- MTN MoMo -->
                            <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="mtnmomo_box">
                                <input type="radio" name="payment_method" id="mtnmomo" value="mtnmomo" class="custom-control-input">
                                <label class="custom-control-label" for="mtnmomo">MTN MoMo</label>
                                <input type="hidden" id="mtnmomo_enable">
                                <input type="hidden" id="mtnmomo_primaryKey">
                                <input type="hidden" id="mtnmomo_isSandbox">
                                <input type="hidden" id="mtnmomo_callback">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-0 border-0">
                <div class="col-6 m-0 p-0">
                    <button type="button" class="btn border-top btn-lg btn-block" data-dismiss="modal">
                        {{ trans('lang.close') }}
                    </button>
                </div>
                <div class="col-6 m-0 p-0">
                    <button type="button" onclick="finalCheckout()" class="btn btn-primary btn-lg btn-block change_user_password remove_hover">
                        {{ trans('lang.next') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
@include('layouts.nav')
<script type="text/javascript">
    var ref = database.collection('wallet').where('user_id', '==', user_uuid).orderBy('date', 'desc');
    var pagesize = 10;
    var offest = 1;
    var end = null;
    var endarray = [];
    var start = null;
    var append_list = '';
    var totalPayment = 0;
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_digits = 0;
    var currencyData = '';
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        if(snapshots.docs.length > 0){
            currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_digits = currencyData.decimal_degits;
            }
            $('.currentCurrency').html(currentCurrency);
        }
    });

    var refMinDeposite = database.collection('settings').doc('globalSettings');
    var minimumAmountToDeposit = 0;

    var PaytmSettings = database.collection('settings').doc('PaytmSettings');
    var flutterWave = database.collection('settings').doc('flutterWave');
    var midtrans_settings = database.collection('settings').doc('midtrans_settings');
    var orange_money_settings = database.collection('settings').doc('orange_money_settings');
    var payFastSettings = database.collection('settings').doc('payFastSettings');
    var payStack = database.collection('settings').doc('payStack');
    var paypalSettings = database.collection('settings').doc('paypalSettings');
    var razorpaySettings = database.collection('settings').doc('razorpaySettings');
    var stripeSettings = database.collection('settings').doc('stripeSettings');
    var xendit_settings= database.collection('settings').doc('xendit_settings');
    var MercadoPagoSettings = database.collection('settings').doc('MercadoPago');
    var phonepe_settings = database.collection('settings').doc('phonepay_settings');
    var cashfree_settings = database.collection('settings').doc('cashfree_settings');
    var instamojo_settings = database.collection('settings').doc('instamojo_settings');
    var foloosi_settings = database.collection('settings').doc('foloosi_settings');
    var paymongo_settings = database.collection('settings').doc('paymongo_settings');
    var mtnmomo_settings = database.collection('settings').doc('mtnMomo_settings');    

    $(document).ready(async function () {
        getPaymentGateways();
        $("#data-table_processing").show();
        var totalPayment = 0;
        await refMinDeposite.get().then(async function (snapshot) {
            var data = snapshot.data();
            if (data.hasOwnProperty('minimumAmountToDeposit') && data.minimumAmountToDeposit != null && data.minimumAmountToDeposit != '') {
                minimumAmountToDeposit = data.minimumAmountToDeposit;
            }
        });
        await database.collection('users').where("id", "==", user_uuid).get().then(
            (amountsnapshot) => {
                var paymentDatas = amountsnapshot.docs[0].data();
                if (paymentDatas.hasOwnProperty('wallet_amount') && paymentDatas.wallet_amount != null && !isNaN(paymentDatas.wallet_amount)) {
                    totalPayment = parseFloat(paymentDatas.wallet_amount);
                }
                if (currencyAtRight) {
                    totalPayment = totalPayment.toFixed(decimal_digits) + '' + currentCurrency;
                } else {
                    totalPayment = currentCurrency + '' + totalPayment.toFixed(decimal_digits);
                }
        });

        jQuery("#total_payment").html('{{trans("lang.total")}} {{trans("lang.Payment")}} : ' + totalPayment);
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        ref.limit(pagesize).get().then(async function (snapshots) {
            if (snapshots != undefined) {
                var html = '';
                html = buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.push(snapshots.docs[0]);
                    $("#data-table_processing").hide();
                }
            }
        });
    });
    function getPaymentGateways() {
   
        // Razorpay Settings
        razorpaySettings.get().then(async function (razorpaySettingsSnapshots) {
            let razorpaySetting = razorpaySettingsSnapshots.data();

            if (razorpaySetting.isEnabled) {
                $("#isEnabled").val(razorpaySetting.isEnabled);
                $("#isSandboxEnabled").val(razorpaySetting.isSandboxEnabled);
                $("#razorpayKey").val(razorpaySetting.razorpayKey);
                $("#razorpaySecret").val(razorpaySetting.razorpaySecret);
                $("#razorpay_box").show();
            }
        });

        // Stripe Settings
        stripeSettings.get().then(async function (stripeSettingsSnapshots) {
            let stripeSetting = stripeSettingsSnapshots.data();

            if (stripeSetting.isEnabled) {
                $("#isStripeSandboxEnabled").val(stripeSetting.isSandboxEnabled);
                $("#stripeKey").val(stripeSetting.clientpublishableKey);
                $("#stripeSecret").val(stripeSetting.stripeSecret);
                $("#stripe_box").show();
            }
        });

        // PayPal Settings
        paypalSettings.get().then(async function (paypalSettingsSnapshots) {
            let paypalSetting = paypalSettingsSnapshots.data();

            if (paypalSetting.isEnabled) {
                $("#paypalKey").val(paypalSetting.paypalClient);
                $("#paypalSecret").val(paypalSetting.paypalSecret);
                $("#paypal_box").show();
            }
        });

        // PayFast Settings
        payFastSettings.get().then(async function (payfastSettingsSnapshots) {
            let payFastSetting = payfastSettingsSnapshots.data();

            if (payFastSetting.isEnable) {
                $("#payfast_isEnabled").val(payFastSetting.isEnable);
                $("#payfast_isSandbox").val(payFastSetting.isSandbox);
                $("#payfast_merchant_id").val(payFastSetting.merchant_id);
                $("#payfast_merchant_key").val(payFastSetting.merchant_key);
                $("#payfast_return_url").val(payFastSetting.return_url);
                $("#payfast_cancel_url").val(payFastSetting.cancel_url);
                $("#payfast_notify_url").val(payFastSetting.notify_url);
                $("#payfast_box").show();
            }
        });

        // PayStack Settings
        payStack.get().then(async function (payStackSettingsSnapshots) {
            let payStackSetting = payStackSettingsSnapshots.data();

            if (payStackSetting.isEnable) {
                $("#paystack_isEnabled").val(payStackSetting.isEnable);
                $("#paystack_isSandbox").val(payStackSetting.isSandbox);
                $("#paystack_public_key").val(payStackSetting.publicKey);
                $("#paystack_secret_key").val(payStackSetting.secretKey);
                $("#paystack_box").show();
            }
        });

        // FlutterWave Settings
        flutterWave.get().then(async function (flutterWaveSettingsSnapshots) {
            let flutterWaveSetting = flutterWaveSettingsSnapshots.data();

            if (flutterWaveSetting.isEnable) {
                $("#flutterWave_isEnabled").val(flutterWaveSetting.isEnable);
                $("#flutterWave_isSandbox").val(flutterWaveSetting.isSandbox);
                $("#flutterWave_encryption_key").val(flutterWaveSetting.encryptionKey);
                $("#flutterWave_secret_key").val(flutterWaveSetting.secretKey);
                $("#flutterWave_public_key").val(flutterWaveSetting.publicKey);
                $("#flutterWave_box").show();
            }
        });

        MercadoPagoSettings.get().then(async function(MercadoPagoSettingsSnapshots) {
            MercadoPagoSetting = MercadoPagoSettingsSnapshots.data();
            if (MercadoPagoSetting.isEnabled) {
                var isEnable = MercadoPagoSetting.isEnabled;
                $("#mercadopago_isEnabled").val(isEnable);
                var isSandboxEnabled = MercadoPagoSetting.isSandboxEnabled;
                $("#mercadopago_isSandbox").val(isSandboxEnabled);
                var PublicKey = MercadoPagoSetting.PublicKey;
                $("#mercadopago_public_key").val(PublicKey);
                var AccessToken = MercadoPagoSetting.AccessToken;
                $("#mercadopago_access_token").val(AccessToken);
                var AccessToken = MercadoPagoSetting.AccessToken;
                $("#mercadopago_box").show();
            }
        });

        // Xendit Settings
        xendit_settings.get().then(async function (XenditSettingsSnapshots) {
            let XenditSetting = XenditSettingsSnapshots.data();

            if (XenditSetting.enable) {
                $("#xendit_enable").val(XenditSetting.enable);
                $("#xendit_apiKey").val(XenditSetting.apiKey);
                $("#xendit_image").val(XenditSetting.image);
                $("#xendit_isSandbox").val(XenditSetting.isSandbox);
                $("#xendit_box").show();
            }
        });

        // Midtrans Settings
        midtrans_settings.get().then(async function (Midtrans_settingsSnapshots) {
            let Midtrans_setting = Midtrans_settingsSnapshots.data();

            if (Midtrans_setting.enable) {
                $("#midtrans_enable").val(Midtrans_setting.enable);
                $("#midtrans_serverKey").val(Midtrans_setting.serverKey);
                $("#midtrans_image").val(Midtrans_setting.image);
                $("#midtrans_isSandbox").val(Midtrans_setting.isSandbox);
                $("#midtrans_box").show();
            }
        });

        // OrangePay Settings
        orange_money_settings.get().then(async function (OrangePaySettingsSnapshots) {
            let OrangePaySetting = OrangePaySettingsSnapshots.data();

            if (OrangePaySetting.enable) {
                $("#orangepay_enable").val(OrangePaySetting.enable);
                $("#orangepay_auth").val(OrangePaySetting.auth);
                $("#orangepay_image").val(OrangePaySetting.image);
                $("#orangepay_isSandbox").val(OrangePaySetting.isSandbox);
                $("#orangepay_clientId").val(OrangePaySetting.clientId);
                $("#orangepay_clientSecret").val(OrangePaySetting.clientSecret);
                $("#orangepay_merchantKey").val(OrangePaySetting.merchantKey);
                $("#orangepay_notifyUrl").val(OrangePaySetting.notifyUrl);
                $("#orangepay_returnUrl").val(OrangePaySetting.returnUrl);
                $("#orangepay_cancelUrl").val(OrangePaySetting.cancelUrl);
                $("#orangepay_box").show();
            }
        });

        
        // PhonePe Settings
        phonepe_settings.get().then(async function (phonepeSettingsSnapshots) {
            let phonepeSetting = phonepeSettingsSnapshots.data();

            if (phonepeSetting && phonepeSetting.enable) {
                $("#phonepe_enable").val(phonepeSetting.enable);
                $("#phonepe_merchantId").val(phonepeSetting.merchantId);
                $("#phonepe_saltKey").val(phonepeSetting.saltKey);
                $("#phonepe_saltIndex").val(phonepeSetting.saltIndex);
                $("#phonepe_isSandbox").val(phonepeSetting.isSandbox);
                $("#phonepe_box").show();
            }
        });

        // Cashfree Settings
        cashfree_settings.get().then(async function (cashfreeSettingsSnapshots) {
            let cashfreeSetting = cashfreeSettingsSnapshots.data();

            if (cashfreeSetting && cashfreeSetting.enable) {
                $("#cashfree_enable").val(cashfreeSetting.enable);
                $("#cashfree_clientId").val(cashfreeSetting.clientId);
                $("#cashfree_clientSecret").val(cashfreeSetting.clientSecret);
                $("#cashfree_isSandbox").val(cashfreeSetting.isSandbox);
                $("#cashfree_box").show();
            }
        });

        // Instamojo Settings
        instamojo_settings.get().then(async function (instamojoSettingsSnapshots) {
            let instamojoSetting = instamojoSettingsSnapshots.data();

            if (instamojoSetting && instamojoSetting.enable) {
                $("#instamojo_enable").val(instamojoSetting.enable);
                $("#instamojo_api_key").val(instamojoSetting.api_key);
                $("#instamojo_auth_token").val(instamojoSetting.auth_token);
                $("#instamojo_isSandbox").val(instamojoSetting.isSandbox);
                $("#instamojo_box").show();
            }
        });

        // Foloosi Settings
        foloosi_settings.get().then(async function (foloosiSettingsSnapshots) {
            let foloosiSetting = foloosiSettingsSnapshots.data();

            if (foloosiSetting && foloosiSetting.enable) {
                $("#foloosi_enable").val(foloosiSetting.enable);
                $("#foloosi_merchant_key").val(foloosiSetting.merchant_key);
                $("#foloosi_secret_key").val(foloosiSetting.secret_key);
                $("#foloosi_isSandbox").val(foloosiSetting.isSandbox);
                $("#foloosi_box").show();
            }
        });

        // PayMongo Settings
        paymongo_settings.get().then(async function (paymongoSettingsSnapshots) {
            let paymongoSetting = paymongoSettingsSnapshots.data();

            if (paymongoSetting && paymongoSetting.enable) {
                $("#paymongo_enable").val(paymongoSetting.enable);
                $("#paymongo_public_key").val(paymongoSetting.public_key);
                $("#paymongo_secret_key").val(paymongoSetting.secret_key);
                $("#paymongo_isSandbox").val(paymongoSetting.isSandbox);
                $("#paymongo_box").show();
            }
        });

        // MTN MoMo Settings
        mtnmomo_settings.get().then(async function (mtnmomoSettingsSnapshots) {
            let mtnmomoSetting = mtnmomoSettingsSnapshots.data();

            if (mtnmomoSetting && mtnmomoSetting.enable) {
                $("#mtnmomo_enable").val(mtnmomoSetting.enable);
                $("#mtnmomo_primaryKey").val(mtnmomoSetting.primaryKey);
                $("#mtnmomo_isSandbox").val(mtnmomoSetting.isSandbox);
                $("#mtnmomo_callback").val(mtnmomoSetting.callback);
                $("#mtnmomo_box").show();
            }
        });
    }

    function buildHTML(snapshots) {
        var html = '';
        var alldata = [];
        var number = [];
        var vendorIDS = [];
        snapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
        alldata.forEach((listval) => {
            var val = listval;
            var date = val.date.toDate().toDateString();
            var time = val.date.toDate().toLocaleTimeString('en-US');
            var price_val = '';
            if (currencyAtRight) {
                price_val = parseFloat(val.amount).toFixed(decimal_digits) + '' + currentCurrency;
            } else {
                price_val = currentCurrency + '' + parseFloat(val.amount).toFixed(decimal_digits);
            }
            html = html + '<div class="transactions-list-wrap mt-4"><div class="bg-white px-4 py-3 border rounded-lg mb-3 transactions-list-view shadow-sm"><div class="gold-members d-flex align-items-center transactions-list">';
            var desc = '';
            if ((val.hasOwnProperty('isTopUp') && val.isTopUp) || (val.payment_method == "Cancelled Order Payment")) {
                price_val = '<div class="float-right ml-auto"><span class="price font-weight-bold h4">+ ' + price_val + '</span>';
                desc = "Wallet Topup";
            } else if (val.hasOwnProperty('isTopUp') && !val.isTopUp) {
                price_val = '<div class="float-right ml-auto"><span class="font-weight-bold h4" style="color: red">- ' + price_val + '</span>';
                desc = "Wallet Amount Deducted";
            } else {
                price_val = '<div class="float-right ml-auto"><span class="font-weight-bold h4">' + price_val + '</span>';
            }
            html = html + '<div class="media transactions-list-left"><div class="mr-3 font-weight-bold card-icon"><span><i class="fa fa-credit-card"></i></span></div><div class="media-body"><h6 class="date">' + desc + '</h6><h6>' + date + ' ' + time + '</h6><p class="text-muted mb-0">' + val.payment_method + '</p><p class="mb-0 badge badge-success text-light">' + val.payment_status + '</p></div></div>';
            html = html + price_val;
            if (val.hasOwnProperty('order_id') && val.order_id != "" && val.order_id != null) {
                if(val.status == "Order Completed"){
                    var view_details = "{{ route('completed_order',':id')}}";
                    view_details = view_details.replace(':id', 'id=' + val.order_id);
                    html = html + '<a href="' + view_details + '"><span class="go-arror text-dark btn-block text-right mt-2"><i class="fa fa-angle-right"></i></span></a>';
                }
                
            }
            html = html + '</div> </div></div></div>';
        });
        return html;
    }
    async function next() {
        if (start != undefined || start != null) {
            jQuery("#data-table_processing").hide();
            listener = ref.startAfter(start).limit(pagesize).get();
            listener.then(async (snapshots) => {
                html = '';
                html = await buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    if (endarray.indexOf(snapshots.docs[0]) != -1) {
                        endarray.splice(endarray.indexOf(snapshots.docs[0]), 1);
                    }
                    endarray.push(snapshots.docs[0]);
                }
            });
        }
    }
    async function prev() {
        if (endarray.length == 1) {
            return false;
        }
        end = endarray[endarray.length - 2];
        if (end != undefined || end != null) {
            jQuery("#data-table_processing").show();
            listener = ref.startAt(end).limit(pagesize).get();
            listener.then(async (snapshots) => {
                html = '';
                html = await buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    append_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.splice(endarray.indexOf(endarray[endarray.length - 1]), 1);
                    if (snapshots.docs.length < pagesize) {
                        jQuery("#users_table_previous_btn").hide();
                    }
                }
            });
        }
    }

    async function finalCheckout() {
        var amount = parseFloat($('.wallet_amount').val());
        
        if (amount < 0 || amount == 0 || amount == null || amount == '' || isNaN(amount)) {
            amount = 0;
            amount = currencyAtRight
                ? `${parseFloat(amount).toFixed(decimal_digits)}${currentCurrency}`
                : `${currentCurrency}${parseFloat(amount).toFixed(decimal_digits)}`;
            Swal.fire({ text: `{{trans('lang.min_deposite_amount_err')}}`, icon: "error" });
            return false;
        }

        const userSnapshots = await database.collection('users').where("id", "==", user_uuid).get();
        const userDetails = userSnapshots.docs[0]?.data();
        const userDetailsJSON = JSON.stringify(userDetails);
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        const routeUrl = "<?php echo route('wallet-proccessing'); ?>";
        const redirectUrl = "<?php echo route('pay-wallet'); ?>";

        if (!paymentMethod) return Swal.fire({ text: "{{trans('lang.please_select_a_payment_method')}}", icon: "error" });

        const ajaxData = {
            _token: '<?php echo csrf_token(); ?>',
            amount,
            payment_method: paymentMethod,
            currencyData: currencyData,
            author: userDetailsJSON 
        };

        if (paymentMethod === "razorpay") {
            Object.assign(ajaxData, {
                razorpayKey: $("#razorpayKey").val(),
                razorpaySecret: $("#razorpaySecret").val()
            });
        } else if (paymentMethod === "mercadopago") {
            Object.assign(ajaxData, {
                mercadopago_public_key: $("#mercadopago_public_key").val(),
                mercadopago_access_token: $("#mercadopago_access_token").val(),
                mercadopago_isSandbox: $("#mercadopago_isSandbox").val(),
                mercadopago_isEnabled: $("#mercadopago_isEnabled").val()
            });
        } else if (paymentMethod === "stripe") {
            Object.assign(ajaxData, {
                stripeKey: $("#stripeKey").val(),
                stripeSecret: $("#stripeSecret").val(),
                isStripeSandboxEnabled: $("#isStripeSandboxEnabled").val()
            });
        } else if (paymentMethod === "paypal") {
            Object.assign(ajaxData, {
                paypalKey: $("#paypalKey").val(),
                paypalSecret: $("#paypalSecret").val(),
                ispaypalSandboxEnabled: $("#ispaypalSandboxEnabled").val()
            });
        } else if (paymentMethod === "payfast") {
            Object.assign(ajaxData, {
                payfast_merchant_key: $("#payfast_merchant_key").val(),
                payfast_merchant_id: $("#payfast_merchant_id").val(),
                payfast_return_url: $("#payfast_return_url").val(),
                payfast_notify_url: $("#payfast_notify_url").val(),
                payfast_cancel_url: $("#payfast_cancel_url").val(),
                payfast_isSandbox: $("#payfast_isSandbox").val()
            });
        } else if (paymentMethod === "paystack") {
            Object.assign(ajaxData, {
                paystack_public_key: $("#paystack_public_key").val(),
                paystack_secret_key: $("#paystack_secret_key").val(),
                paystack_isSandbox: $("#paystack_isSandbox").val(),
                email: userDetails.email
            });
        } else if (paymentMethod === "flutterwave") {
            Object.assign(ajaxData, {
                flutterwave_isenabled: $("#flutterWave_isEnabled").val(),
                flutterWave_encryption_key: $("#flutterWave_encryption_key").val(),
                flutterWave_public_key: $("#flutterWave_public_key").val(),
                flutterWave_secret_key: $("#flutterWave_secret_key").val(),
                flutterWave_isSandbox: $("#flutterWave_isSandbox").val(),
                email: userDetails.email
            });
        } else if (paymentMethod === "xendit") {
            if (!['IDR', 'PHP', 'USD', 'VND', 'THB', 'MYR', 'SGD'].includes(currencyData.code)) {
                alert("Currency restriction");
                return false;
            }
            Object.assign(ajaxData, {
                xendit_enable: $("#xendit_enable").val(),
                xendit_apiKey: $("#xendit_apiKey").val(),
                xendit_image: $("#xendit_image").val(),
                xendit_isSandbox: $("#xendit_isSandbox").val()
            });
        } else if (paymentMethod === "midtrans") {
            Object.assign(ajaxData, {
                midtrans_enable: $("#midtrans_enable").val(),
                midtrans_serverKey: $("#midtrans_serverKey").val(),
                midtrans_image: $("#midtrans_image").val(),
                midtrans_isSandbox: $("#midtrans_isSandbox").val()
            });
        } else if (paymentMethod === "orangepay") {
            Object.assign(ajaxData, {
                orangepay_enable: $("#orangepay_enable").val(),
                orangepay_auth: $("#orangepay_auth").val(),
                orangepay_image: $("#orangepay_image").val(),
                orangepay_isSandbox: $("#orangepay_isSandbox").val(),
                orangepay_clientId: $("#orangepay_clientId").val(),
                orangepay_clientSecret: $("#orangepay_clientSecret").val(),
                orangepay_merchantKey: $("#orangepay_merchantKey").val(),
                orangepay_notifyUrl: $("#orangepay_notifyUrl").val(),
                orangepay_returnUrl: $("#orangepay_returnUrl").val(),
                orangepay_cancelUrl: $("#orangepay_cancelUrl").val()
            });
        } else if (paymentMethod === "phonepe") {
            Object.assign(ajaxData, {
                phonepe_enable: $("#phonepe_enable").val(),
                phonepe_merchantId: $("#phonepe_merchantId").val(),
                phonepe_saltKey: $("#phonepe_saltKey").val(),
                phonepe_saltIndex: $("#phonepe_saltIndex").val(),
                phonepe_isSandbox: $("#phonepe_isSandbox").val()
            });
        } else if (paymentMethod === "cashfree") {
            Object.assign(ajaxData, {
                cashfree_enable: $("#cashfree_enable").val(),
                cashfree_clientId: $("#cashfree_clientId").val(),
                cashfree_clientSecret: $("#cashfree_clientSecret").val(),
                cashfree_isSandbox: $("#cashfree_isSandbox").val()
            });
        } else if (paymentMethod === "instamojo") {
            Object.assign(ajaxData, {
                instamojo_enable: $("#instamojo_enable").val(),
                instamojo_api_key: $("#instamojo_api_key").val(),
                instamojo_auth_token: $("#instamojo_auth_token").val(),
                instamojo_isSandbox: $("#instamojo_isSandbox").val()
            });
        } else if (paymentMethod === "foloosi") {
            Object.assign(ajaxData, {
                foloosi_enable: $("#foloosi_enable").val(),
                foloosi_merchant_key: $("#foloosi_merchant_key").val(),
                foloosi_secret_key: $("#foloosi_secret_key").val(),
                foloosi_isSandbox: $("#foloosi_isSandbox").val()
            });
        } else if (paymentMethod === "phonepe") {
            Object.assign(ajaxData, {
                phonepe_enable: $("#phonepe_enable").val(),
                phonepe_merchantId: $("#phonepe_merchantId").val(),
                phonepe_saltKey: $("#phonepe_saltKey").val(),
                phonepe_saltIndex: $("#phonepe_saltIndex").val(),
                phonepe_isSandbox: $("#phonepe_isSandbox").val()
            });
        } else if (paymentMethod === "cashfree") {
            Object.assign(ajaxData, {
                cashfree_enable: $("#cashfree_enable").val(),
                cashfree_clientId: $("#cashfree_clientId").val(),
                cashfree_clientSecret: $("#cashfree_clientSecret").val(),
                cashfree_isSandbox: $("#cashfree_isSandbox").val()
            });
        } else if (paymentMethod === "instamojo") {
            Object.assign(ajaxData, {
                instamojo_enable: $("#instamojo_enable").val(),
                instamojo_api_key: $("#instamojo_api_key").val(),
                instamojo_auth_token: $("#instamojo_auth_token").val(),
                instamojo_isSandbox: $("#instamojo_isSandbox").val()
            });
        } else if (paymentMethod === "paymongo") {
            Object.assign(ajaxData, {
                paymongo_enable: $("#paymongo_enable").val(),
                paymongo_public_key: $("#paymongo_public_key").val(),
                paymongo_secret_key: $("#paymongo_secret_key").val(),
                paymongo_isSandbox: $("#paymongo_isSandbox").val()
            });
        } else if (paymentMethod === "mtnmomo") {
            Object.assign(ajaxData, {
                mtnmomo_enable: $("#mtnmomo_enable").val(),
                mtnmomo_primaryKey: $("#mtnmomo_primaryKey").val(),
                mtnmomo_isSandbox: $("#mtnmomo_isSandbox").val(),
                mtnmomo_callback: $("#mtnmomo_callback").val()
            });
        } else return;

        $.ajax({
            type: 'POST',
            url: routeUrl,
            data: ajaxData,
            success: () => window.location.href = redirectUrl,
            error: (error) => Swal.fire({ text: "{{trans('lang.payment_processing_failed')}}", icon: "error" })
        });
    }

</script>
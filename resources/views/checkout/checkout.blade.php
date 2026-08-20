@include('layouts.app')
@include('layouts.header')
@php
    $cityToCountry = file_get_contents(public_path('tz-cities-to-countries.json'));
    $cityToCountry = json_decode($cityToCountry, true);
    $countriesJs = [];
    foreach ($cityToCountry as $key => $value) {
        $countriesJs[$key] = $value;
    }
@endphp
<div class="siddhi-checkout">
    <div class="container position-relative">
        <div class="py-5 row">
            <div class="col-md-7 mb-3 checkout-left">
                <div class="checkout-left-inner">
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
                  
                    <?php if (Session::get('takeawayOption') == "true") { ?>
                    <div class="siddhi-cart-item mb-4 rounded shadow-sm bg-white checkout-left-box border" style="display:none;">
                        <?php } else { ?>
                        <div class="siddhi-cart-item mb-4 rounded shadow-sm bg-white checkout-left-box border">
                            <?php } ?>
                            <div class="siddhi-cart-item-profile p-3">
                                <div class="d-flex flex-column">
                                    <div class="chec-out-header d-flex mb-3">
                                        <div class="chec-out-title">
                                            <h6 class="mb-0 font-weight-bold pb-1">{{ trans('lang.delivery_address') }}
                                            </h6>
                                            <span>{{ trans('lang.save_address_location') }}</span>
                                        </div>
                                        <a href="{{ route('delivery-address.index') }}" class="ml-auto font-weight-bold">{{ trans('lang.change') }}</a>
                                    </div>
                                    <div class="row">
                                        <div class="custom-control col-lg-12 mb-3 position-relative" id="address_box" style="display: none;">
                                            <div class="addres-innerbox">
                                                <div class="p-3 w-100">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <h6 class="mb-0 pb-1">{{ trans('lang.address') }}</h6>
                                                    </div>
                                                    <p class="text-dark m-0" id="line_1"></p>
                                                    <p class="text-dark m-0" id="line_2">
                                                        {{ trans('lang.rewood_city') }}</p>
                                                    <input type="text" id="addressId" hidden>
                                                    <input type="text" id="addressIdLat" hidden>
                                                    <input type="text" id="addressIdLong" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a id="add_address" class="btn btn-primary" href="#" data-toggle="modal" data-target="#locationModalAddress" style="display: none;">
                                        {{ trans('lang.add_new_address') }} </a>
                                </div>
                            </div>
                        </div>
                        <div class="accordion mb-3 rounded shadow-sm bg-white checkout-left-box border" id="accordionExample">
                            <div class="siddhi-card border-bottom overflow-hidden">
                                <div class="siddhi-card-header" id="headingTwo">
                                    <h6 class="mb-2 ml-3 mt-3">{{ trans('lang.select_payment_option') }}</h6>
                                </div>
                            </div>
                            <div class="siddhi-card overflow-hidden checkout-payment-options">
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="cod_box">
                                    <input type="radio" name="payment_method" id="cod" value="cod" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="cod">{{ trans('lang.cash_on_delivery') }}</label>
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="razorpay_box">
                                    <input type="radio" name="payment_method" id="razorpay" value="razorpay" class="custom-control-input">
                                    <label class="custom-control-label" for="razorpay">{{ trans('lang.razorpay') }}</label>
                                    <input type="hidden" id="isEnabled">
                                    <input type="hidden" id="isSandboxEnabled">
                                    <input type="hidden" id="razorpayKey">
                                    <input type="hidden" id="razorpaySecret">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="stripe_box">
                                    <input type="radio" name="payment_method" id="stripe" value="stripe" class="custom-control-input">
                                    <label class="custom-control-label" for="stripe">{{ trans('lang.stripe') }}</label>
                                    <input type="hidden" id="isStripeSandboxEnabled">
                                    <input type="hidden" id="stripeKey">
                                    <input type="hidden" id="stripeSecret">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="paypal_box">
                                    <input type="radio" name="payment_method" id="paypal" value="paypal" class="custom-control-input">
                                    <label class="custom-control-label" for="paypal">{{ trans('lang.pay_pal') }}</label>
                                    <input type="hidden" id="ispaypalSandboxEnabled">
                                    <input type="hidden" id="paypalKey">
                                    <input type="hidden" id="paypalSecret">
                                </div>
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
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="paystack_box">
                                    <input type="radio" name="payment_method" id="paystack" value="paystack" class="custom-control-input">
                                    <label class="custom-control-label" for="paystack">{{ trans('lang.pay_stack') }}</label>
                                    <input type="hidden" id="paystack_isEnabled">
                                    <input type="hidden" id="paystack_isSandbox">
                                    <input type="hidden" id="paystack_public_key">
                                    <input type="hidden" id="paystack_secret_key">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="flutterWave_box">
                                    <input type="radio" name="payment_method" id="flutterwave" value="flutterwave" class="custom-control-input">
                                    <label class="custom-control-label" for="flutterwave">{{ trans('lang.flutter_wave') }}</label>
                                    <input type="hidden" id="flutterWave_isEnabled">
                                    <input type="hidden" id="flutterWave_isSandbox">
                                    <input type="hidden" id="flutterWave_encryption_key">
                                    <input type="hidden" id="flutterWave_public_key">
                                    <input type="hidden" id="flutterWave_secret_key">
                                </div>
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
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="xendit_box">
                                    <input type="radio" name="payment_method" id="xendit" value="xendit" class="custom-control-input">
                                    <label class="custom-control-label" for="xendit">{{ trans('lang.xendit') }}</label>
                                    <input type="hidden" id="xendit_enable">
                                    <input type="hidden" id="xendit_apiKey">
                                    <input type="hidden" id="xendit_image">
                                    <input type="hidden" id="xendit_isSandbox">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="midtrans_box">
                                    <input type="radio" name="payment_method" id="midtrans" value="midtrans" class="custom-control-input">
                                    <label class="custom-control-label" for="midtrans">{{ trans('lang.midtrans') }}</label>
                                    <input type="hidden" id="midtrans_enable">
                                    <input type="hidden" id="midtrans_serverKey">
                                    <input type="hidden" id="midtrans_image">
                                    <input type="hidden" id="midtrans_isSandbox">
                                </div>
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
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="phonepe_box">
                                    <input type="radio" name="payment_method" id="phonepe" value="phonepe"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="phonepe">{{ trans('lang.phonepe') }}</label>
                                    <input type="hidden" id="phonepe_enable">
                                    <input type="hidden" id="phonepe_isSandbox">
                                    <input type="hidden" id="phonepe_clientId">
                                    <input type="hidden" id="phonepe_clientSecret">
                                    <input type="hidden" id="phonepe_merchantId">
                                    <input type="hidden" id="phonepe_flowId">
                                    <input type="hidden" id="phonepe_saltKey">
                                    <input type="hidden" id="phonepe_saltIndex" value="1">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;"
                                    id="cashfree_box">
                                    <input type="radio" name="payment_method" id="cashfree" value="cashfree"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="cashfree">{{ trans('lang.Cashfree') }}</label>
                                    <input type="hidden" id="cashfree_isSandbox">
                                    <input type="hidden" id="cashfree_clientId">
                                    <input type="hidden" id="cashfree_clientSecret">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;"
                                    id="instamojo_box">
                                    <input type="radio" name="payment_method" id="instamojo" value="instamojo"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="instamojo">{{ trans('lang.Instamojo') }}</label>
                                    <input type="hidden" id="instamojo_isEnabled">
                                    <input type="hidden" id="instamojo_isSandbox">
                                    <input type="hidden" id="instamojo_clientId">
                                    <input type="hidden" id="instamojo_clientSecret">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;"
                                    id="paymongo_box">
                                    <input type="radio" name="payment_method" id="paymongo" value="paymongo"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="paymongo">{{ trans('lang.PayMongo') }}</label>
                                    <input type="hidden" id="paymongo_enable">
                                    <input type="hidden" id="paymongo_isSandbox">
                                    <input type="hidden" id="paymongo_secret_key">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;"
                                    id="foloosi_box">
                                    <input type="radio" name="payment_method" id="foloosi" value="foloosi"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="foloosi">{{ trans('lang.Foloosi') }}</label>
                                    <input type="hidden" id="foloosi_enable">
                                    <input type="hidden" id="foloosi_isSandbox">
                                    <input type="hidden" id="foloosi_merchantKey">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;"
                                    id="mtnmomo_box">
                                    <input type="radio" name="payment_method" id="mtnmomo" value="mtnmomo"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="mtnmomo">{{ trans('lang.MTN_MoMo') }}</label>
                                    <input type="hidden" id="mtnmomo_enable">
                                    <input type="hidden" id="mtnmomo_isSandbox">
                                    <input type="hidden" id="mtnmomo_primaryKey">
                                    <input type="hidden" id="mtnmomo_callback">
                                </div>
                                <div class="custom-control custom-radio border-bottom py-2" style="display:none;" id="wallet_box">
                                    <input type="radio" name="payment_method" disabled id="wallet" value="wallet" class="custom-control-input">
                                    <label class="custom-control-label" for="wallet">{{trans('lang.wallet')}} ( {{trans('lang.you_have')}} <span id="wallet_amount"></span> )</label>
                                    <input type="hidden" id="user_wallet_amount">
                                </div>
                            </div>
                        </div>
                        <div class="add-note">
                            <h3>{{ trans('lang.add_note') }}</h3>
                            <textarea name="add-note" id="add-note" onchange="changeNote();"><?php echo @$cart['order-note']; ?></textarea>
                        </div>
                        <div class="addres-innerbox d-none cashback-div">
                            <h6 class="mb-0 font-weight-bold pb-1 py-2 px-3">{{ trans('lang.cashback_offer') }}</h6>
                            <div id="cashback-offer" class="p-3 w-100" style="color: green; font-weight: bold;"></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-5">
                    <div class="siddhi-cart-item rounded rounded shadow-sm overflow-hidden bg-white sticky_sidebar" id="cart_list">
                        @include('restaurant.cart_item')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@include('layouts.nav')
<div class="modal fade" id="exampleModalAddress" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('lang.delivery_address') }}</h5>
                <button type="button" id="close_button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="">
                    <div class="form-row">
                        <div class="col-md-12 form-group">
                            <label class="form-label">{{ trans('lang.street_1') }}</label>
                            <div class="input-group">
                                <input placeholder="Zona de entrega" type="text" id="address_line1" class="form-control">
                                <div class="input-group-append">
                                    <button onclick="getCurrentLocationAddress1()" type="button" class="btn btn-outline-secondary"><i class="feather-map-pin"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.landmark') }}</label><input placeholder="Complete Address e.g. house number, street name, landmark" value="" id="address_line2" type="text" class="form-control"></div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.zip_code') }}</label><input placeholder="Código postal" id="address_zipcode" type="text" class="form-control">
                        </div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.city') }}</label><input placeholder="Ciudad" id="address_city" type="text" class="form-control"></div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.country') }}</label><input placeholder="País" id="address_country" type="text" class="form-control">
                        </div>
                        <input type="hidden" name="address_lat" id="address_lat">
                        <input type="hidden" name="address_lng" id="address_lng">
                    </div>
                </form>
            </div>
            <div class="modal-footer p-0 border-0">
                <div class="col-6 m-0 p-0">
                    <button type="button" class="btn border-top btn-lg btn-block" data-dismiss="modal">{{ trans('lang.close') }}</button>
                </div>
                <div class="col-6 m-0 p-0">
                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="saveShippingAddress()">{{ trans('lang.save_changes') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="siddhi-menu-fotter fixed-bottom bg-white page px-3 py-2 text-center d-none">
    <div class="row">
        <div class="col selected">
            <a href="home.html" class="text-danger small font-weight-bold text-decoration-none">
                <p class="h4 m-0"><i class="feather-home text-danger"></i></p>
                {{ trans('lang.home') }}
            </a>
        </div>
        <div class="col">
            <a href="most_popular.html" class="text-dark small font-weight-bold text-decoration-none">
                <p class="h4 m-0"><i class="feather-map-pin"></i></p>
                {{ trans('lang.trending') }}
            </a>
        </div>
        <div class="col bg-white rounded-circle mt-n4 px-3 py-2">
            <div class="bg-danger rounded-circle mt-n0 shadow">
                <a href="checkout.html" class="text-white small font-weight-bold text-decoration-none">
                    <i class="feather-shopping-cart"></i>
                </a>
            </div>
        </div>
        <div class="col">
            <a href="favorites.html" class="text-dark small font-weight-bold text-decoration-none">
                <p class="h4 m-0"><i class="feather-heart"></i></p>
                {{ trans('lang.favorites') }}
            </a>
        </div>
        <div class="col">
            <a href="profile.html" class="text-dark small font-weight-bold text-decoration-none">
                <p class="h4 m-0"><i class="feather-user"></i></p>
                {{ trans('lang.profile') }}
            </a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>

<script type="text/javascript">

    cityToCountry = '<?php echo json_encode($countriesJs); ?>';
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function(snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });
    cityToCountry = JSON.parse(cityToCountry);
    var userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    userCity = userTimeZone.split('/')[1];
    userCountry = cityToCountry[userCity];
    var wallet_amount = 0;
    var fcmToken = '';
    var id_order = database.collection('tmp').doc().id;
    var userId = "<?php echo $id; ?>";
    var userDetailsRef = database.collection('users').where('id', "==", userId);
    var uservendorDetailsRef = database.collection('users');
    var vendorDetailsRef = database.collection('vendors');
    var main_restaurant_id = $("#main_vendor_id").val();
    var venZoneId = '';
    var refCashback = database.collection('cashback').where('isEnabled', '==', true);
    async function getAdminCommission() {
        database.collection('settings').doc('AdminCommission').get().then(async function(AdminCommissionSnapshots) {
            if (AdminCommissionSnapshots.exists) {
                var AdminCommissionRes = AdminCommissionSnapshots.data();
                var AdminCommissionValueBase = AdminCommissionRes.fix_commission;
                var AdminCommissionTypeBase = AdminCommissionRes.commissionType;
                if (AdminCommissionRes.isEnabled && main_restaurant_id) {
                    await database.collection('vendors').where('id', '==', main_restaurant_id).get()
                        .then(async function(
                            snapshot) {
                            var data = snapshot.docs[0].data();
                            if (data.hasOwnProperty('adminCommission') && data
                                .adminCommission != null &&
                                data.adminCommission != '') {
                                $("#adminCommission").val(data.adminCommission.fix_commission);
                                $("#adminCommissionType").val(data.adminCommission
                                    .commissionType);
                            } else {
                                $("#adminCommission").val(AdminCommissionValueBase);
                                $("#adminCommissionType").val(AdminCommissionTypeBase);
                            }
                            venZoneId = data.zoneId;
                            
                        })
                } else {
                    $("#adminCommission").val(0);
                    $("#adminCommissionType").val('Fixed');
                }
            } else {
                $("#adminCommission").val(0);
                $("#adminCommissionType").val('Fixed');
            }
        });
    }

    var razorpaySettings = database.collection('settings').doc('razorpaySettings');
    var codSettings = database.collection('settings').doc('CODSettings');
    var stripeSettings = database.collection('settings').doc('stripeSettings');
    var paypalSettings = database.collection('settings').doc('paypalSettings');
    var walletSettings = database.collection('settings').doc('walletSettings');
    
    var cart = @json($cart);
    var taxSetting = cart?.taxSetting ?? [];
    var taxScope = cart?.taxScope ?? 'order';
    var driverDeliveryTax = cart?.taxesByScope?.delivery ?? [];
    var packagingTax       = cart?.taxesByScope?.packaging ?? [];
    var platformTax        = cart?.taxesByScope?.platform ?? [];
    var productTax        = cart?.taxesByScope?.product ?? [];
    var platformCharge = cart?.platformCharge ?? '0';
    var packagingChargeEnable = cart?.packagingChargeEnable ?? false;
        
    var payFastSettings = database.collection('settings').doc('payFastSettings');
    var payStackSettings = database.collection('settings').doc('payStack');
    var flutterWaveSettings = database.collection('settings').doc('flutterWave');
    var MercadoPagoSettings = database.collection('settings').doc('MercadoPago');
    var XenditSettings = database.collection('settings').doc('xendit_settings');
    var Midtrans_settings = database.collection('settings').doc('midtrans_settings');
    var OrangePaySettings = database.collection('settings').doc('orange_money_settings');
    var mtnmomoSettings = database.collection('settings').doc('mtnMomo_settings');
    var phonepeSettings = database.collection('settings').doc('phonepay_settings');
    var foloosiSettings = database.collection('settings').doc('foloosi_settings');
    var cashfreeSettings = database.collection('settings').doc('cashfree_settings');
    var paymongoSettings = database.collection('settings').doc('paymongo_settings');
    var instamojoSettings = database.collection('settings').doc('instamojo_settings');


    var currentCurrency = '';
    var currencyAtRight = false;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    var currencyData = '';
    refCurrency.get().then(async function(snapshots) {
        currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        loadcurrencynew();
    });

    let bestCashback = null;
    async function getCashback() {
        var enableCashbackOffer = false;
        var cashbackOfferSetting = database.collection('settings').doc('cashbackOffer');

        await cashbackOfferSetting.get().then(async function(settingSnapshots) {
            if (settingSnapshots.data()) {
                var settingData = settingSnapshots.data();
                if (settingData.isEnable) {
                    enableCashbackOffer = true;
                    
                }
            }
        })
        if (!enableCashbackOffer) {
            return false;
        }
        refCashback.get().then(async function(snapshot) {
            const eligibleCashbacks = [];
            var payment_method = $('input[name="payment_method"]:checked').val();
            var orderTotal = $('#subTotal').val();

            const now = new Date();
            for (const doc of snapshot.docs) {
                const data = doc.data();
                const startDate = data.startDate.toDate(); // If Firestore Timestamp
                const endDate = data.endDate.toDate();
                const withinDateRange = startDate <= now && endDate >= now;
                const meetsMinAmount = parseFloat(orderTotal) >= parseFloat(data.minumumPurchaseAmount);
                const paymentMatch = data.allPayment === true || (Array.isArray(data.paymentMethods) && data.paymentMethods.includes(payment_method));
                const isEligibleCustomer = data.allCustomer === true || (Array.isArray(data.customerIds) && data.customerIds.includes(userId));
                var currentRedeemCount = 0;
                const redeemCountSnapshot = await database.collection('cashback_redeem')
                    .where('userId', '==', userId)
                    .where('cashbackId', '==', data.id)
                    .get();
                currentRedeemCount = redeemCountSnapshot.size;

                const underLimit = currentRedeemCount < data.redeemLimit;
               
                if (withinDateRange && meetsMinAmount && paymentMatch && isEligibleCustomer && underLimit) {
                    eligibleCashbacks.push({
                        id: doc.id,
                        ...data
                    });
                }
            }

            let maxCashbackValue = 0;
            eligibleCashbacks.forEach(cashback => {
                let cashbackValue = 0;
                if (cashback.cashbackType === 'Percent') {
                    cashbackValue = (cashback.cashbackAmount / 100) * orderTotal;
                } else if (cashback.cashbackType === 'Fixed') {
                    cashbackValue = cashback.cashbackAmount;
                }
                if (cashbackValue > parseFloat(cashback.maximumDiscount)) {
                    cashbackValue = parseFloat(cashback.maximumDiscount);
                }
                if (cashbackValue > maxCashbackValue) {
                    maxCashbackValue = cashbackValue;
                    bestCashback = {
                        ...cashback,
                        cashbackValue: cashbackValue
                    };
                }
            });

            if (eligibleCashbacks.length > 0 && bestCashback) {
                $('.cashback-div').removeClass('d-none')
               
                    if (currencyAtRight) {
                        var cashbackAmout = parseFloat(bestCashback.cashbackValue).toFixed(decimal_degits) + currentCurrency;
                    } else {
                        var cashbackAmout = currentCurrency + parseFloat(bestCashback.cashbackValue).toFixed(decimal_degits);
                    }
                    $('#cashback-offer').html("<h6 class='font-weight-bold'>{{trans('lang.cashback_name')}}: "+bestCashback.title+"</h6>{{ trans('lang.you_will_get') }}" + ' ' + cashbackAmout + ' {{ trans('lang.cashback_after_completing_the_order') }} ');

                
            } else {
                bestCashback = null;
                $('#cashback-offer').html("");
                $('.cashback-div').addClass('d-none');
            }


        })
    }

    function loadcurrencynew() {
        if (currencyAtRight) {
            jQuery('.currency-symbol-left').hide();
            jQuery('.currency-symbol-right').show();
            jQuery('.currency-symbol-right').text(currentCurrency);
        } else {
            jQuery('.currency-symbol-left').show();
            jQuery('.currency-symbol-right').hide();
            jQuery('.currency-symbol-left').text(currentCurrency);
        }
    }
    var orderPlacedSubject = '';
    var orderPlacedMsg = '';
    var scheduleOrderPlacedSubject = '';
    var scheduleOrderPlacedMsg = '';
    database.collection('dynamic_notification').get().then(async function(snapshot) {
        if (snapshot.docs.length > 0) {
            snapshot.docs.map(async (listval) => {
                val = listval.data();
                if (val.type == "order_placed") {
                    orderPlacedSubject = val.subject;
                    orderPlacedMsg = val.message;
                } else if (val.type == "schedule_order") {
                    scheduleOrderPlacedSubject = val.subject;
                    scheduleOrderPlacedMsg = val.message;
                }
            })
        }
    })
    
    $(document).ready(async function() {

        var today = new Date().toISOString().slice(0, 16);
        if (document.getElementsByName("scheduleTime").length > 0) {
            document.getElementsByName("scheduleTime")[0].min = today;
        }
        getUserDetails();
        getAdminCommission();
        getCashback();
        $(document).on("click", '.remove_item', function(event) {
            var id = $(this).attr('data-id');
            var restaurant_id = $(this).attr('data-vendor-id');
            $.ajax({
                type: 'POST',
                url: "<?php echo route('remove-from-cart'); ?>",
                data: {
                    _token: '<?php echo csrf_token(); ?>',
                    restaurant_id: restaurant_id,
                    id: id,
                    is_checkout: 1
                },
                success: function(data) {
                    data = JSON.parse(data);
                    $('#cart_list').html(data.html);
                    loadcurrencynew();
                    var today = new Date().toISOString().slice(0, 16);
                    if (document.getElementsByName("scheduleTime").length > 0) {
                        document.getElementsByName("scheduleTime")[0].min = today;
                    }
                    getAdminCommission();
                    getCashback();
                }
            });
        });
        $(document).on("click", '.count-number-input-cart', function(event) {

            var id = $(this).attr('data-id');
            var restaurant_id = $(this).attr('data-vendor-id');
            var quantity = $('.count_number_' + id).val();
            var stock_quantity = $(this).attr('data-vqty');

            if (stock_quantity != "" && stock_quantity != undefined && parseInt(stock_quantity) != -1) {

                if (parseInt(quantity) > parseInt(stock_quantity)) {
                    Swal.fire({
                        text: "{{ trans('lang.invalid_stock_qty') }}",
                        icon: "error"
                    });
                    $('.count_number_' + id).val(quantity - 1);
                    return false;
                }

            }

            $.ajax({
                type: 'POST',
                url: "<?php echo route('change-quantity-cart'); ?>",
                data: {
                    _token: '<?php echo csrf_token(); ?>',
                    restaurant_id: restaurant_id,
                    id: id,
                    quantity: quantity,
                    is_checkout: 1
                },
                success: function(data) {
                    data = JSON.parse(data);
                    $('#cart_list').html(data.html);
                    loadcurrencynew();
                    var today = new Date().toISOString().slice(0, 16);
                    if (document.getElementsByName("scheduleTime").length > 0) {
                        document.getElementsByName("scheduleTime")[0].min = today;
                    }
                    getAdminCommission();
                    getCashback();
                }
            });
        });

        $(document).on("click", '#apply-coupon-code', function(event) {

            var coupon_code = $("#coupon_code").val();
            var restaurant_id = $(this).attr('data-vendor-id');
            var couponCodeRef = database.collection('coupons').where('code', "==", coupon_code).where('isEnabled', '==', true).where('expiresAt', '>=', new Date());
            couponCodeRef.get().then(async function(couponSnapshots) {
                if (couponSnapshots.docs.length) {
                    var coupondata = couponSnapshots.docs[0].data();
                    if (coupondata.resturant_id != undefined && coupondata.resturant_id !=
                        '') {
                        if (coupondata.resturant_id == restaurant_id) {
                            discount = coupondata.discount;
                            discountType = coupondata.discountType;
                            $.ajax({
                                type: 'POST',
                                url: "<?php echo route('apply-coupon'); ?>",
                                data: {
                                    _token: '<?php echo csrf_token(); ?>',
                                    coupon_code: coupon_code,
                                    discount: discount,
                                    discountType: discountType,
                                    is_checkout: 1,
                                    coupon_id: coupondata.id
                                },
                                success: function(data) {
                                    Swal.fire({
                                        text: "{{ trans('lang.discount_applied') }}",
                                        icon: "success"
                                    });
                                    data = JSON.parse(data);
                                    $('#cart_list').html(data.html);
                                    loadcurrencynew();
                                    var today = new Date().toISOString().slice(0, 16);
                                    if (document.getElementsByName("scheduleTime").length > 0) {
                                        document.getElementsByName("scheduleTime")[0].min = today;
                                    }
                                    getAdminCommission();
                                    $("#coupon_code").val('');
                                }
                            });
                        } else {
                            Swal.fire({
                                text: "{{ trans('lang.coupon_not_valid') }}",
                                icon: "error"
                            });
                            $("#coupon_code").val('');
                        }
                    } else {
                        Swal.fire({
                            text: "{{ trans('lang.coupon_not_valid') }}",
                            icon: "error"
                        });
                        $("#coupon_code").val('');
                    }
                } else {
                    Swal.fire({
                        text: "{{ trans('lang.coupon_not_valid') }}",
                        icon: "error"
                    });
                    $("#coupon_code").val('');
                }
            });
        });

        $(document).on("click", '.remove-coupon a', function(event) {
            $.ajax({
                type: 'POST',
                url: "<?php echo route('remove-coupon'); ?>",
                data: {
                    _token: '<?php echo csrf_token(); ?>',
                },
                success: function(data) {
                    Swal.fire({
                        text: "{{ trans('lang.discount_removed') }}",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        });
        $(document).on('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                $("#data-table_processing").hide();
            }
        });
    });
    async function getUserDetails() {
        codSettings.get().then(async function(codSettingsSnapshots) {
            codSettings = codSettingsSnapshots.data();
            if (codSettings.isEnabled) {
                $("#cod_box").show();
            } else {
                $("#cod_box").remove();
            }
        });
        razorpaySettings.get().then(async function(razorpaySettingsSnapshots) {
            razorpaySetting = razorpaySettingsSnapshots.data();
            if (razorpaySetting.isEnabled) {
                var isEnabled = razorpaySetting.isEnabled;
                $("#isEnabled").val(isEnabled);
                var isSandboxEnabled = razorpaySetting.isSandboxEnabled;
                $("#isSandboxEnabled").val(isSandboxEnabled);
                var razorpayKey = razorpaySetting.razorpayKey;
                $("#razorpayKey").val(razorpayKey);
                var razorpaySecret = razorpaySetting.razorpaySecret;
                $("#razorpaySecret").val(razorpaySecret);
                $("#razorpay_box").show();
            }
        });
        stripeSettings.get().then(async function(stripeSettingsSnapshots) {
            stripeSetting = stripeSettingsSnapshots.data();
            if (stripeSetting.isEnabled) {
                var isEnabled = stripeSetting.isEnabled;
                var isSandboxEnabled = stripeSetting.isSandboxEnabled;
                $("#isStripeSandboxEnabled").val(isSandboxEnabled);
                var stripeKey = stripeSetting.stripeKey;
                $("#stripeKey").val(stripeKey);
                var stripeSecret = stripeSetting.stripeSecret;
                $("#stripeSecret").val(stripeSecret);
                $("#stripe_box").show();
            }
        });
        paypalSettings.get().then(async function(paypalSettingsSnapshots) {
            paypalSetting = paypalSettingsSnapshots.data();
            if (paypalSetting.isEnabled) {
                var isEnabled = paypalSetting.isEnabled;
                var isLive = paypalSetting.isLive;
                if (isLive) {
                    $("#ispaypalSandboxEnabled").val(false);
                } else {
                    $("#ispaypalSandboxEnabled").val(true);
                }
                var paypalAppId = paypalSetting.paypalAppId;
                $("#paypalKey").val(paypalAppId);
                var paypalSecret = paypalSetting.paypalSecret;
                $("#paypalSecret").val(paypalSecret);
                $("#paypal_box").show();
            }
        });
        walletSettings.get().then(async function(walletSettingsSnapshots) {
            walletSetting = walletSettingsSnapshots.data();
            if (walletSetting.isEnabled) {
                var isEnabled = walletSetting.isEnabled;
                if (isEnabled) {
                    $("#walletenabled").val(true);
                } else {
                    $("#walletenabled").val(false);
                }
                $("#wallet_box").show();
            }
        });
        payFastSettings.get().then(async function(payfastSettingsSnapshots) {
            payFastSetting = payfastSettingsSnapshots.data();
            if (payFastSetting.isEnable) {
                var isEnable = payFastSetting.isEnable;
                $("#payfast_isEnabled").val(isEnable);
                var isSandboxEnabled = payFastSetting.isSandbox;
                $("#payfast_isSandbox").val(isSandboxEnabled);
                var merchant_id = payFastSetting.merchant_id;
                $("#payfast_merchant_id").val(merchant_id);
                var merchant_key = payFastSetting.merchant_key;
                $("#payfast_merchant_key").val(merchant_key);
                var return_url = payFastSetting.return_url;
                $("#payfast_return_url").val(return_url);
                var cancel_url = payFastSetting.cancel_url;
                $("#payfast_cancel_url").val(cancel_url);
                var notify_url = payFastSetting.notify_url;
                $("#payfast_notify_url").val(notify_url);
                $("#payfast_box").show();
            }
        });
        payStackSettings.get().then(async function(payStackSettingsSnapshots) {
            payStackSetting = payStackSettingsSnapshots.data();
            if (payStackSetting.isEnable) {
                var isEnable = payStackSetting.isEnable;
                $("#paystack_isEnabled").val(isEnable);
                var isSandboxEnabled = payStackSetting.isSandbox;
                $("#paystack_isSandbox").val(isSandboxEnabled);
                var publicKey = payStackSetting.publicKey;
                $("#paystack_public_key").val(publicKey);
                var secretKey = payStackSetting.secretKey;
                $("#paystack_secret_key").val(secretKey);
                $("#paystack_box").show();
            }
        });
        flutterWaveSettings.get().then(async function(flutterWaveSettingsSnapshots) {
            flutterWaveSetting = flutterWaveSettingsSnapshots.data();
            if (flutterWaveSetting.isEnable) {
                var isEnable = flutterWaveSetting.isEnable;
                $("#flutterWave_isEnabled").val(isEnable);
                var isSandboxEnabled = flutterWaveSetting.isSandbox;
                $("#flutterWave_isSandbox").val(isSandboxEnabled);
                var encryptionKey = flutterWaveSetting.encryptionKey;
                $("#flutterWave_encryption_key").val(encryptionKey);
                var secretKey = flutterWaveSetting.secretKey;
                $("#flutterWave_secret_key").val(secretKey);
                var publicKey = flutterWaveSetting.publicKey;
                $("#flutterWave_public_key").val(publicKey);
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
        XenditSettings.get().then(async function(XenditSettingsSnapshots) {
            XenditSetting = XenditSettingsSnapshots.data();
            if (XenditSetting.enable) {
                var enable = XenditSetting.enable;
                $("#xendit_enable").val(enable);
                var apiKey = XenditSetting.apiKey;
                $("#xendit_apiKey").val(apiKey);
                var image = XenditSetting.image;
                $("#xendit_image").val(image);
                var isSandbox = XenditSetting.isSandbox;
                $("#xendit_isSandbox").val(isSandbox);
                $("#xendit_box").show();
            }
        });
        Midtrans_settings.get().then(async function(Midtrans_settingsSnapshots) {
            Midtrans_setting = Midtrans_settingsSnapshots.data();
            if (Midtrans_setting.enable) {
                var enable = Midtrans_setting.enable;
                $("#midtrans_enable").val(enable);
                var serverKey = Midtrans_setting.serverKey;
                $("#midtrans_serverKey").val(serverKey);
                var image = Midtrans_setting.image;
                $("#midtrans_image").val(image);
                var isSandbox = Midtrans_setting.isSandbox;
                $("#midtrans_isSandbox").val(isSandbox);
                $("#midtrans_box").show();
            }
        });
        OrangePaySettings.get().then(async function(OrangePaySettingsSnapshots) {
            OrangePaySetting = OrangePaySettingsSnapshots.data();
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
        mtnmomoSettings.get().then(async function(mtnmomoSettingSanpshot) {
            mtnmomoSetting = mtnmomoSettingSanpshot.data();
            if (mtnmomoSetting.enable) {
                $("#mtnmomo_enable").val(mtnmomoSetting.enable);
                $("#mtnmomo_isSandbox").val(mtnmomoSetting.isSandbox);
                $("#mtnmomo_primaryKey").val(mtnmomoSetting.primaryKey);
                $("#mtnmomo_callback").val(mtnmomoSetting.callbackUrl);
                $("#mtnmomo_box").show();
            }
        });
        phonepeSettings.get().then(async function(phonepeSettingSanpshot) {
            phonepeSetting = phonepeSettingSanpshot.data();
            if (phonepeSetting.enable) {
                $("#phonepe_enable").val(phonepeSetting.enable);
                $("#phonepe_isSandbox").val(phonepeSetting.isSandbox);
                $("#phonepe_clientId").val(phonepeSetting.clientId);
                $("#phonepe_clientSecret").val(phonepeSetting.clientSecret);
                $("#phonepe_merchantId").val(phonepeSetting.merchantId);
                $("#phonepe_flowId").val(phonepeSetting.flowId);
                $("#phonepe_saltKey").val(phonepeSetting.saltKey || '');
                $("#phonepe_box").show();
            }
        });
        foloosiSettings.get().then(async function(foloosiSettingSanpshot) {
            foloosiSetting = foloosiSettingSanpshot.data();
            if (foloosiSetting.enable) {
                $("#foloosi_enable").val(foloosiSetting.enable);
                $("#foloosi_isSandbox").val(foloosiSetting.isSandbox);
                $("#foloosi_merchantKey").val(foloosiSetting.merchantKey);
                $("#foloosi_box").show();
            }
        });
        cashfreeSettings.get().then(async function(cashfreeSettingSanpshot) {
            cashfreeSetting = cashfreeSettingSanpshot.data();
            if (cashfreeSetting.enable) {
                $("#cashfree_isSandbox").val(cashfreeSetting.isSandbox);
                $("#cashfree_clientId").val(cashfreeSetting.clientId);
                $("#cashfree_clientSecret").val(cashfreeSetting.clientSecret);
                $("#cashfree_box").show();
            }
        });
        paymongoSettings.get().then(async function(paymongoSettingSanpshot) {
            paymongoSetting = paymongoSettingSanpshot.data();
            if (paymongoSetting.enable) {
                $("#paymongo_enable").val(paymongoSetting.enable);
                $("#paymongo_isSandbox").val(paymongoSetting.isSandbox);
                $("#paymongo_secret_key").val(paymongoSetting.secretKey);
                $("#paymongo_box").show();
            }
        });
        instamojoSettings.get().then(async function(instamojoSettingSanpshot) {
            instamojoSetting = instamojoSettingSanpshot.data();
            if (instamojoSetting.enable) {
                $("#instamojo_isSandbox").val(instamojoSetting.isSandbox);
                $("#instamojo_clientId").val(instamojoSetting.clientId);
                $("#instamojo_clientSecret").val(instamojoSetting.clientSecret);
                $("#instamojo_isEnabled").val(instamojoSetting.enable);
                $("#instamojo_box").show();
            }
        });
        userDetailsRef.get().then(async function(userSnapshots) {
            var userDetails = userSnapshots.docs[0].data();
            var sessionAdrsId = sessionStorage.getItem('addressId');
            var full_address = '';
            var takeaway_options = '<?php echo Session::get('takeawayOption'); ?>';
            if (userDetails.hasOwnProperty('shippingAddress') && Array.isArray(userDetails.shippingAddress)) {
                shippingAddress = userDetails.shippingAddress;
                var isShipping = false;
                shippingAddress.forEach((listval) => {
                    if (sessionAdrsId != '' && sessionAdrsId != null) {
                        if (listval.id == sessionAdrsId) {
                            $("#line_1").html(listval.address);
                            $('#line_2').html(listval.locality + " " + listval.landmark);
                            $('#addressId').val(listval.id);
                            if (listval.location && 
                                listval.location.latitude && 
                                listval.location.longitude) {
                                $('#addressIdLat').val(listval.location.latitude);
                                $('#addressIdLong').val(listval.location.longitude);

                            } else {
                                $('#addressIdLat').val('');
                                $('#addressIdLong').val('');
                            }
                            $("#address_box").show();
                            isShipping = true;
                        }
                    }

                });
                if (isShipping == false) {
                    if (takeaway_options == "false" || takeaway_options == false) {
                        window.location.href = "{{ route('delivery-address.index') }}";
                    }
                }
            } else {
                if (takeaway_options == "false" || takeaway_options == false) {
                    window.location.href = "{{ route('delivery-address.index') }}";
                }
            }
            if (userDetails.wallet_amount != undefined && userDetails.wallet_amount != '' && !isNaN(
                    userDetails.wallet_amount)) {
                wallet_amount = parseFloat(userDetails.wallet_amount);
                $("#wallet").attr('disabled', false);
                $("#user_wallet_amount").val(wallet_amount);
            }
            var wallet_balance = 0;
            if (currencyAtRight) {
                wallet_balance = parseFloat(wallet_amount).toFixed(decimal_degits) + "" +
                    currentCurrency;
            } else {
                wallet_balance = currentCurrency + "" + parseFloat(wallet_amount).toFixed(
                    decimal_degits);
            }
            $("#wallet_amount").html(wallet_balance);
        });
        if (main_restaurant_id) {
            uservendorDetailsRef.where('vendorID', "==", main_restaurant_id).get().then(async function(
                uservendorSnapshots) {
                if (uservendorSnapshots.docs.length) {
                    var userVendorDetails = uservendorSnapshots.docs[0].data();
                    if (userVendorDetails && userVendorDetails.fcmToken) {
                        fcmToken = userVendorDetails.fcmToken;
                    }

                }
            });
        }

    }
    async function manageInventory(products) {
        for (let i = 0; i < products.length; i++) {
            var item = products[i];
            var product_id = item.id;
            var quantity = item.quantity;
            var variant_info = item.variant_info;
            var productDoc = await database.collection('vendor_products').doc(product_id).get();
            var productInfo = productDoc.data();
            if (variant_info) {
                var new_varients = [];
                $.each(productInfo.item_attribute.variants, function(key, value) {
                    if (value.variant_sku == variant_info.variant_sku && value.variant_quantity !=
                        undefined && value.variant_quantity != '-1') {
                        value.variant_quantity = value.variant_quantity - quantity;
                        value.variant_quantity = (value.variant_quantity <= 0) ? 0 : value.variant_quantity;
                        value.variant_quantity = value.variant_quantity.toString();
                        new_varients.push(value);
                    } else {
                        new_varients.push(value);
                    }
                });
                database.collection('vendor_products').doc(product_id).update({
                    'item_attribute.variants': new_varients
                });
            } else {
                if (productInfo.quantity != undefined && productInfo.quantity != '-1') {
                    var new_quantity = productInfo.quantity - quantity;
                    new_quantity = (new_quantity <= 0) ? 0 : new_quantity;
                    database.collection('vendor_products').doc(product_id).update({
                        'quantity': new_quantity
                    });
                }
            }
        }
    }

    async function getVendorUser(vendorUserId) {
        var vendorUSerData = '';
        await database.collection('users').where('id', "==", vendorUserId).get().then(async function(
            uservendorSnapshots) {
            if (uservendorSnapshots.docs.length) {
                vendorUSerData = uservendorSnapshots.docs[0].data();
            }
        });
        return vendorUSerData;
    }

    async function finalCheckout() {
        var isAllowed = await checkVendorZone();
        if(!isAllowed){
            return;
        }
        var payment_method = $('input[name="payment_method"]:checked').val();
        if (payment_method == false || payment_method == undefined || payment_method == '') {
            Swal.fire({
                text: "{{ trans('lang.select_payment_option') }}",
                icon: "error"
            });
            return false;
        }
        payableAmount = $('#total_pay').val();
        if (payableAmount == 0 || payableAmount == '' || payableAmount == "0") {
            return false;
        }

        $("#data-table_processing").show();

            var vendorID = $("#main_vendor_id").val();

            // Get vendor details first
            const vendorSnap = await vendorDetailsRef.where('id', "==", vendorID).get();
            if (vendorSnap.empty) {
                Swal.fire({ text: "Vendor not found", icon: "error" });
                $("#data-table_processing").hide();
                return false;
            }

            var vendorDetails = vendorSnap.docs[0].data();

            // ──────  Restaurant open/closed check  ──────
            var isOpen = false;

            var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var currentdate = new Date();
            var currentDay = days[currentdate.getDay()];
            var hour = currentdate.getHours();
            var minute = currentdate.getMinutes();
            if (hour < 10) hour = '0' + hour;
            if (minute < 10) minute = '0' + minute;
            var currentHours = hour + ':' + minute;

            if (vendorDetails.hasOwnProperty('workingHours')) {
                for (let i = 0; i < vendorDetails.workingHours.length; i++) {
                    if (vendorDetails.workingHours[i]['day'] === currentDay) {
                        let slots = vendorDetails.workingHours[i]['timeslot'] || [];
                        for (let j = 0; j < slots.length; j++) {
                            let slot = slots[j];
                            let from = slot['from'];
                            let to   = slot['to'];

                            // Compare strings directly (HH:mm format)
                            if (currentHours >= from && currentHours <= to) {
                                isOpen = true;
                                break;
                            }
                        }
                        if (isOpen) break;
                    }
                }
            }

            if (!isOpen) {
                Swal.fire({
                    text: "{{trans('lang.restaurant_closed_message')}}",
                    icon: "error"
                });
                $("#data-table_processing").hide();
                return false;
            }

        var id = $('.count-number-input-cart').attr('data-id');
        var quantity = $('.count_number_' + id).val();
        var stock_quantity = $('.count-number-input-cart').attr('data-vqty');
        
        userDetailsRef.get().then(async function(userSnapshots) {
            var vendorID = $("#main_vendor_id").val();
            var userDetails = userSnapshots.docs[0].data();
            vendorDetailsRef.where('id', "==", vendorID).get().then(async function(vendorSnapshots) {
                var vendorDetails = vendorSnapshots.docs[0].data();
                if (vendorDetails) {
                    var vendorUser = await getVendorUser(vendorDetails.author);
                    var products = [];
                    $(".product-item").each(function(index) {
                        product_id = $(this).attr("data-id");
                        item_id = product_id;
                        price = $("#price_" + product_id).val();
                        item_price = $("#item_price_" + product_id).val();
                        photo = $("#photo_" + product_id).val();
                        total_pay = $("#total_pay").val();
                        extras_price = $("#extras_price_" + product_id).val();
                        size = $("#size_" + product_id).val();
                        name = $("#name_" + product_id).val();
                        quantity = $("#quantity_" + product_id).val();
                        extras = [];
                        $(".extras_" + product_id).each(function(index) {
                            val = $(this).val();
                            if (val) {
                                extras.push(val);
                            }
                        })
                        var category_id = $("#category_id_" + product_id).val();
                        var variant_info = $("#variant_info_" + product_id).val();
                        if (variant_info) {
                            variant_info = $.parseJSON(atob(variant_info));
                            product_id = product_id.split("PV")[0];
                        } else {
                            var variant_info = null;
                        }

                        let taxSettingConvert = cart.item[vendorID][item_id].taxSetting || [];
                        taxSettingConvert = taxSettingConvert.filter(tax => {
                            if (!tax) return false;
                            const match = productTax.find(t => t.id === tax.id);
                            return match && tax.enable;
                        })
                        .map(tax => ({
                            ...tax,
                            enable: tax.enable === 'true' || tax.enable === true
                        }));
                        
                        products.push({
                            'id': product_id,
                            'name': name,
                            'photo': photo,
                            'price': item_price,
                            'quantity': parseInt(quantity),
                            'vendorID': vendorDetails.id,
                            'extras_price': extras_price,
                            'extras': extras,
                            'size': size,
                            'variant_info': variant_info,
                            'category_id': category_id,
                            'taxSetting': taxSettingConvert,
                        })
                    });
                    
                    manageInventory(products);
                    var address = '';
                    shippingAdrs = userDetails.shippingAddress;
                    addressId = $('#addressId').val();
                    shippingAdrs.forEach((listval) => {
                        if (listval.id == addressId) {
                            address = listval;
                        }
                    })
                    var author = userDetails;
                    var authorID = userId;
                    var authorName = userDetails.firstName;
                    var authorEmail = userDetails.email;
                    var couponCode = $("#coupon_code_main").val();
                    var couponId = $("#coupon_id").val();
                    var createdAt = firebase.firestore.FieldValue.serverTimestamp();
                    var discount = $("#discount_amount").val();
                    var driver = [];
                    var vendor = vendorDetails;
                    var status = 'Order Placed';
                    var deliveryCharge = $("#deliveryCharge").val();
                    var tip_amount = $("#tip_amount").val();
                    var adminCommission = $("#adminCommission").val();
                    var adminCommissionType = $("#adminCommissionType").val();
                    var delivery_option = $('input[name="delivery_option"]').val();
                    var take_away = false;
                    if (delivery_option == "takeaway") {
                        take_away = true;
                    }
                    var notes = $("#add-note").val();
                    var specialOfferDiscountAmount = $('#specialOfferDiscountAmount').val();
                    var specialOfferType = $('#specialOfferType').val();
                    var specialOfferDiscountVal = $('#specialOfferDiscountVal').val();
                    var specialDiscount = [];
                    var specialDiscount = {
                        'special_discount': specialOfferDiscountAmount,
                        'specialType': specialOfferType,
                        'special_discount_label': specialOfferDiscountVal,
                    }
                    var subject = orderPlacedSubject;
                    var message = orderPlacedMsg;
                    var scheduleTime = $('#scheduleTime').val();
                    var scheduleTime = "";
                    if ($('#scheduleTime').val() && $('#scheduleTime').val() != undefined) {
                        scheduleTime = new Date($('#scheduleTime').val());
                        subject = scheduleOrderPlacedSubject;
                        message = scheduleOrderPlacedMsg;
                    }
                    var now = new Date();
                    if (scheduleTime != '' && now.getTime() > scheduleTime.getTime()) {
                        Swal.fire({
                            text: "{{ trans('lang.invalid_schedule_current_time') }}",
                            icon: "error"
                        });
                        $("#data-table_processing").hide();
                        return false;
                    } else if (scheduleTime != '' && scheduleTime < new Date()) {
                        Swal.fire({
                            text: "{{ trans('lang.invalid_schedule_today_date') }}",
                            icon: "error"
                        });
                        $("#data-table_processing").hide();
                        return false;
                    }

                    //specialDiscount = object;
                    if (payment_method == "razorpay") {
                        var razorpayKey = $("#razorpayKey").val();
                        var razorpaySecret = $("#razorpaySecret").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommissionType: adminCommissionType,
                            adminCommission: adminCommission,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                razorpaySecret: razorpaySecret,
                                razorpayKey: razorpayKey,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "mercadopago") {

                        var mercadopago_public_key = $("#mercadopago_public_key").val();
                        var mercadopago_access_token = $("#mercadopago_access_token").val();
                        var mercadopago_isSandbox = $("#mercadopago_isSandbox").val();
                        var mercadopago_isEnabled = $("#mercadopago_isEnabled").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            quantity: quantity,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                mercadopago_public_key: mercadopago_public_key,
                                mercadopago_access_token: mercadopago_access_token,
                                payment_method: payment_method,
                                authorName: authorName,
                                id: id_order,
                                quantity: quantity,
                                total_pay: total_pay,
                                mercadopago_isSandbox: mercadopago_isSandbox,
                                mercadopago_isEnabled: mercadopago_isEnabled,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "stripe") {
                        var stripeKey = $("#stripeKey").val();
                        var stripeSecret = $("#stripeSecret").val();
                        var isStripeSandboxEnabled = $("#isStripeSandboxEnabled").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                stripeKey: stripeKey,
                                stripeSecret: stripeSecret,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                isStripeSandboxEnabled: isStripeSandboxEnabled,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "paypal") {

                        var paypalKey = $("#paypalKey").val();
                        var paypalSecret = $("#paypalSecret").val();
                        var ispaypalSandboxEnabled = $("#ispaypalSandboxEnabled").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                paypalKey: paypalKey,
                                paypalSecret: paypalSecret,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                ispaypalSandboxEnabled: ispaypalSandboxEnabled,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "payfast") {

                        var payfast_merchant_key = $("#payfast_merchant_key").val();
                        var payfast_merchant_id = $("#payfast_merchant_id").val();
                        var payfast_return_url = $("#payfast_return_url").val();
                        var payfast_notify_url = $("#payfast_notify_url").val();
                        var payfast_cancel_url = $("#payfast_cancel_url").val();
                        var payfast_isSandbox = $("#payfast_isSandbox").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                payfast_merchant_key: payfast_merchant_key,
                                payfast_merchant_id: payfast_merchant_id,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                payfast_isSandbox: payfast_isSandbox,
                                payfast_return_url: payfast_return_url,
                                payfast_notify_url: payfast_notify_url,
                                payfast_cancel_url: payfast_cancel_url,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "paystack") {

                        var paystack_public_key = $("#paystack_public_key").val();
                        var paystack_secret_key = $("#paystack_secret_key").val();
                        var paystack_isSandbox = $("#paystack_isSandbox").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                paystack_isSandbox: paystack_isSandbox,
                                paystack_public_key: paystack_public_key,
                                paystack_secret_key: paystack_secret_key,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "flutterwave") {

                        var flutterwave_isenabled = $("#flutterWave_isEnabled").val();
                        var flutterWave_encryption_key = $("#flutterWave_encryption_key")
                            .val();
                        var flutterWave_public_key = $("#flutterWave_public_key").val();
                        var flutterWave_secret_key = $("#flutterWave_secret_key").val();
                        var flutterWave_isSandbox = $("#flutterWave_isSandbox").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                flutterWave_isSandbox: flutterWave_isSandbox,
                                flutterWave_public_key: flutterWave_public_key,
                                flutterWave_secret_key: flutterWave_secret_key,
                                flutterwave_isenabled: flutterwave_isenabled,
                                flutterWave_encryption_key: flutterWave_encryption_key,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "xendit") {

                        if (!['IDR', 'PHP', 'USD', 'VND', 'THB', 'MYR', 'SGD'].includes(
                                currencyData.code)) {
                            Swal.fire({
                                text: "{{ trans('lang.currencu_restriction') }}",
                                icon: "error"
                            });
                            $("#data-table_processing").hide();
                            return false;
                        }
                        var xendit_enable = $("#xendit_enable").val();
                        var xendit_apiKey = $("#xendit_apiKey").val();
                        var xendit_image = $("#xendit_image").val();
                        var xendit_isSandbox = $("#xendit_isSandbox").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                xendit_enable: xendit_enable,
                                xendit_apiKey: xendit_apiKey,
                                xendit_image: xendit_image,
                                xendit_isSandbox: xendit_isSandbox,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "midtrans") {

                        var midtrans_enable = $("#midtrans_enable").val();
                        var midtrans_serverKey = $("#midtrans_serverKey").val();
                        var midtrans_image = $("#midtrans_image").val();
                        var midtrans_isSandbox = $("#midtrans_isSandbox").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                midtrans_enable: midtrans_enable,
                                midtrans_serverKey: midtrans_serverKey,
                                midtrans_image: midtrans_image,
                                midtrans_isSandbox: midtrans_isSandbox,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "orangepay") {

                        var orangepay_enable = $("#orangepay_enable").val();
                        var orangepay_auth = $("#orangepay_auth").val();
                        var orangepay_image = $("#orangepay_image").val();
                        var orangepay_isSandbox = $("#orangepay_isSandbox").val();
                        var orangepay_clientId = $("#orangepay_clientId").val();
                        var orangepay_clientSecret = $("#orangepay_clientSecret").val();
                        var orangepay_merchantKey = $("#orangepay_merchantKey").val();
                        var orangepay_notifyUrl = $("#orangepay_notifyUrl").val();
                        var orangepay_returnUrl = $("#orangepay_returnUrl").val();
                        var orangepay_cancelUrl = $("#orangepay_cancelUrl").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                orangepay_enable: orangepay_enable,
                                orangepay_auth: orangepay_auth,
                                orangepay_image: orangepay_image,
                                orangepay_isSandbox: orangepay_isSandbox,
                                orangepay_clientId: orangepay_clientId,
                                orangepay_clientSecret: orangepay_clientSecret,
                                orangepay_merchantKey: orangepay_merchantKey,
                                orangepay_notifyUrl: orangepay_notifyUrl,
                                orangepay_returnUrl: orangepay_returnUrl,
                                orangepay_cancelUrl: orangepay_cancelUrl,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });

                    } else if (payment_method == "phonepe") {
                        var phonepe_enable = $("#phonepe_enable").val();
                        var phonepe_isSandbox = $("#phonepe_isSandbox").val();
                        var phonepe_clientId = $("#phonepe_clientId").val();
                        var phonepe_clientSecret = $("#phonepe_clientSecret").val();
                        var phonepe_merchantId = $("#phonepe_merchantId").val();
                        var phonepe_flowId = $("#phonepe_flowId").val();
                        var phonepe_saltKey = $("#phonepe_saltKey").val();
                        var phonepe_saltIndex = $("#phonepe_saltIndex").val();
                        var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                        $.ajax({
                            type: 'POST',
                            url: "<?php echo route('order-proccessing'); ?>",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                order_json: order_json,
                                payment_method: payment_method,
                                authorName: authorName,
                                total_pay: total_pay,
                                phonepe_enable: phonepe_enable,
                                phonepe_isSandbox: phonepe_isSandbox,
                                phonepe_clientId: phonepe_clientId,
                                phonepe_clientSecret: phonepe_clientSecret,
                                phonepe_merchantId: phonepe_merchantId,
                                phonepe_flowId: phonepe_flowId,
                                phonepe_saltKey: phonepe_saltKey,
                                phonepe_saltIndex: phonepe_saltIndex,
                                address_line1: $("#address_line1").val(),
                                address_line2: $("#address_line2").val(),
                                address_zipcode: $("#address_zipcode").val(),
                                address_city: $("#address_city").val(),
                                address_country: $("#address_country").val(),
                                currencyData: currencyData
                            },
                            success: function(data) {
                                data = JSON.parse(data);
                                $('#cart_list').html(data.html);
                                loadcurrencynew();
                                window.location.href = "<?php echo route('pay'); ?>";
                            }
                        });
            } else if (payment_method == "cashfree") {
                var cashfree_isSandbox = $("#cashfree_isSandbox").val();
                var cashfree_clientId = $("#cashfree_clientId").val();
                var cashfree_clientSecret = $("#cashfree_clientSecret").val();  
                var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };              
                $.ajax({
                    type: 'POST',
                    url: "<?php echo route('order-proccessing'); ?>",
                    data: {
                        _token: '<?php echo csrf_token(); ?>',
                        order_json: order_json,
                        payment_method: payment_method,
                        authorName: authorName,
                        total_pay: total_pay,
                        cashfree_isSandbox: cashfree_isSandbox,
                        cashfree_clientId: cashfree_clientId,
                        cashfree_clientSecret: cashfree_clientSecret,
                        address_line1: $("#address_line1").val(),
                        address_line2: $("#address_line2").val(),
                        address_zipcode: $("#address_zipcode").val(),
                        address_city: $("#address_city").val(),
                        address_country: $("#address_country").val(),
                        currencyData: currencyData
                    },
                    success: function(data) {
                        data = JSON.parse(data);
                        $('#cart_list').html(data.html);
                        loadcurrencynew();
                        window.location.href = "<?php echo route('pay'); ?>";
                    }
                });

            } else if (payment_method == "instamojo") {
                var instamojo_isEnabled = $("#instamojo_isEnabled").val();
                var instamojo_isSandbox = $("#instamojo_isSandbox").val();
                var instamojo_clientId = $("#instamojo_clientId").val();
                var instamojo_clientSecret = $("#instamojo_clientSecret").val();
                var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                if (instamojo_isEnabled !== "true" || !instamojo_clientId || !instamojo_clientSecret) {
                    Swal.fire({
                        text: "{{trans('lang.instamojo_is_not_properly_configured')}}",
                        icon: "error"
                    });                   
                    return false;
                }

                $.ajax({
                    type: 'POST',
                    url: "<?php echo route('order-proccessing'); ?>",
                    data: {
                        _token: '<?php echo csrf_token(); ?>',
                        order_json: order_json,
                        payment_method: payment_method,
                        authorName: authorName,
                        total_pay: total_pay,
                        instamojo_isSandbox: instamojo_isSandbox,
                        instamojo_clientId: instamojo_clientId,
                        instamojo_clientSecret: instamojo_clientSecret,
                        currencyData: currencyData,
                        buyer_name: '{{ auth()->user()->name ?? 'User' }}',
                        buyer_email: '{{ auth()->user()->email ?? '' }}',
                        buyer_phone: '{{ auth()->user()->phone ?? '' }}',
                        address_line1: $("#address_line1").val(),
                        address_line2: $("#address_line2").val(),
                        address_zipcode: $("#address_zipcode").val(),
                        address_city: $("#address_city").val(),
                        address_country: $("#address_country").val()
                    },
                    success: function(data) {
                        try {
                            data = JSON.parse(data);
                            $('#cart_list').html(data.html);
                            loadcurrencynew();
                            window.location.href = "<?php echo route('pay'); ?>";
                        } catch (e) {
                            Swal.fire({
                                text: "{{trans('lang.invalid_server_response')}}",
                                icon: "error"
                            });  
                        }
                    },
                    error: function() {
                        Swal.fire({
                            text: "{{trans('lang.error_while_preparing_instamojo_payment')}}",
                            icon: "error"
                        });                         
                    }
                });
            } else if (payment_method == "paymongo") {
                var paymongo_enable = $("#paymongo_enable").val();
                var paymongo_isSandbox = $("#paymongo_isSandbox").val();
                var paymongo_secret_key = $("#paymongo_secret_key").val();
                var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                $.ajax({
                    type: 'POST',
                    url: "<?php echo route('order-proccessing'); ?>",
                    data: {
                        _token: '<?php echo csrf_token(); ?>',
                        order_json: order_json,
                        payment_method: payment_method,
                        authorName: authorName,
                        total_pay: total_pay,
                        paymongo_enable: paymongo_enable,
                        paymongo_isSandbox: paymongo_isSandbox,
                        paymongo_secret_key: paymongo_secret_key,
                        address_line1: $("#address_line1").val(),
                        address_line2: $("#address_line2").val(),
                        address_zipcode: $("#address_zipcode").val(),
                        address_city: $("#address_city").val(),
                        address_country: $("#address_country").val(),
                        currencyData: currencyData
                    },
                    success: function(data) {
                        data = JSON.parse(data);
                        $('#cart_list').html(data.html);
                        loadcurrencynew();
                        window.location.href = "<?php echo route('pay'); ?>";
                    }
                });
            } else if (payment_method == "foloosi") {
                var foloosi_merchantKey = $("#foloosi_merchantKey").val();
                var foloosi_isSandbox = $("#foloosi_isSandbox").val();
                var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                $.ajax({
                    type: 'POST',
                    url: "<?php echo route('order-proccessing'); ?>",
                    data: {
                        _token: '<?php echo csrf_token(); ?>',
                        order_json: order_json,
                        payment_method: payment_method,
                        authorName: authorName,
                        total_pay: total_pay,
                        foloosi_merchantKey: foloosi_merchantKey,
                        foloosi_isSandbox: foloosi_isSandbox,
                        address_line1: $("#address_line1").val(),
                        address_line2: $("#address_line2").val(),
                        address_zipcode: $("#address_zipcode").val(),
                        address_city: $("#address_city").val(),
                        address_country: $("#address_country").val(),
                        currencyData: currencyData
                    },
                    success: function(data) {
                        data = JSON.parse(data);
                        $('#cart_list').html(data.html);
                        loadcurrencynew();
                        window.location.href = "<?php echo route('pay'); ?>";
                    }
                });
            } else if (payment_method == "mtnmomo") {
                var mtnmomo_enable = $("#mtnmomo_enable").val();
                var mtnmomo_primaryKey = $("#mtnmomo_primaryKey").val();
                var mtnmomo_isSandbox = $("#mtnmomo_isSandbox").val();
                var mtnmomo_callback = $("#mtnmomo_callback").val();
                var order_json = {
                            authorID: authorID,
                            couponCode: couponCode,
                            couponId: couponId,
                            discount: discount,
                            id: id_order,
                            products: products,
                            status: status,
                            vendorID: vendorDetails.id,
                            deliveryCharge: deliveryCharge,
                            tip_amount: tip_amount,
                            adminCommission: adminCommission,
                            adminCommissionType: adminCommissionType,
                            take_away: take_away,
                            specialDiscount: specialDiscount,
                            scheduleTime: scheduleTime,
                            subject: subject,
                            message: message,
                            address: address,
                            bestCashback: bestCashback ? JSON.stringify(bestCashback) : null
                        };
                $.ajax({
                    type: 'POST',
                    url: "<?php echo route('order-proccessing'); ?>",
                    data: {
                        _token: '<?php echo csrf_token(); ?>',
                        order_json: order_json,
                        payment_method: payment_method,
                        authorName: authorName,
                        total_pay: total_pay,
                        mtnmomo_enable: mtnmomo_enable,
                        mtnmomo_primaryKey: mtnmomo_primaryKey,
                        mtnmomo_isSandbox: mtnmomo_isSandbox,
                        mtnmomo_callback: mtnmomo_callback,
                        address_line1: $("#address_line1").val(),
                        address_line2: $("#address_line2").val(),
                        address_zipcode: $("#address_zipcode").val(),
                        address_city: $("#address_city").val(),
                        address_country: $("#address_country").val(),
                        currencyData: currencyData
                    },
                    success: function(data) {
                        data = JSON.parse(data);
                        $('#cart_list').html(data.html);
                        loadcurrencynew();
                        window.location.href = "<?php echo route('pay'); ?>";
                    }
                });
            } else {
                        if (payment_method == "wallet") {
                            payment_method = "wallet";
                            if (wallet_amount < total_pay) {
                                Swal.fire({
                                    text: "{{ trans('lang.invalid_balance') }}",
                                    icon: "error"
                                });
                                $("#data-table_processing").hide();
                                return false;
                            }
                        } else {
                            payment_method = "cod";
                        }
                        if (take_away == 'true') {
                            take_away = true;
                        }
                        if (take_away == 'false') {
                            take_away = false;
                        }
                        for (var n = 0; n < products.length; n++) {
                            if (products[n].photo == null && products[n].photo == "") {
                                products[n].photo = "";
                            }
                            if (products[n].size == null) {
                                products[n].size = "";
                            }
                            products[n].quantity = parseInt(products[n].quantity);
                        }
                        if (scheduleTime == "") {
                            scheduleTime = null;
                        }
                        if (address == "") {
                            var location = {
                                'latitude': parseFloat(getCookie('address_lat')),
                                'longitude': parseFloat(getCookie('address_lng'))
                            };
                            var address = {
                                'address': null,
                                'addressAs': null,
                                'id': null,
                                'isDefault': null,
                                'landmark': null,
                                'locality': getCookie('address_name'),
                                'location': location
                            };
                        }
                        database.collection('restaurant_orders').doc(id_order).set({
                            'address': address,
                            'author': author,
                            'authorID': authorID,
                            'couponCode': couponCode,
                            'couponId': couponId,
                            'couponId': couponId,
                            'discount': parseFloat(discount),
                            "createdAt": createdAt,
                            'id': id_order,
                            'products': products,
                            'status': status,
                            'vendor': vendorDetails,
                            'vendorID': vendorDetails.id,
                            'deliveryCharge': deliveryCharge,
                            'tip_amount': tip_amount,
                            'adminCommission': adminCommission,
                            'adminCommissionType': adminCommissionType,
                            'payment_method': payment_method,
                            'takeAway': take_away,
                            "taxSetting": taxSetting,
                            "notes": notes,
                            "specialDiscount": specialDiscount,
                            "scheduleTime": scheduleTime,
                            'cashback': bestCashback,
                            'isPosOrder': false,
                            'taxScope': taxScope,
                            "driverDeliveryTax": driverDeliveryTax,
                            "packagingTax": packagingTax,
                            "platformFee": platformCharge,
                            "platformTax": platformTax,
                            "packagingChargeEnable": packagingChargeEnable
                        }).then(function(result) {

                            var sendnotification = "<?php echo url('/'); ?>";
                            
                            $.ajax({
                                type: 'POST',
                                url: "<?php echo route('order-complete'); ?>",
                                data: {
                                    _token: '<?php echo csrf_token(); ?>',
                                    'fcm': fcmToken,
                                    'authorName': authorName,
                                    'subject': subject,
                                    'message': message
                                },
                                success: async function(data) {

                                    if (bestCashback != null) {
                                        var cId = database.collection('temp').doc().id;
                                        await database.collection('cashback_redeem').doc(cId).set({
                                            'id': cId,
                                            'createdAt': firebase.firestore.FieldValue.serverTimestamp(),
                                            'cashbackId': bestCashback.id,
                                            'userId': userId,
                                            'orderId': id_order
                                        })
                                    }

                                    if (payment_method == "wallet") {
                                        wallet_amount = wallet_amount - total_pay;
                                        database.collection('users').doc(userId).update({
                                            'wallet_amount': wallet_amount
                                        }).then(async function(
                                            result) {
                                            walletId = database.collection("tmp").doc().id;
                                            database.collection('wallet').doc(walletId).set({
                                                'amount': parseFloat(total_pay),
                                                'date': createdAt,
                                                'id': walletId,
                                                'isTopUp': false,
                                                'order_id': id_order,
                                                'payment_method': "Wallet",
                                                'payment_status': 'success',
                                                'serviceType': '',
                                                'user_id': authorID
                                            }).then(
                                                async function(result) {
                                                    $('#cart_list').html(data.html);
                                                    loadcurrencynew();
                                                    var emailUserData = await sendMailData(id_order,userDetails.id);
                                                    if (vendorUser && vendorUser != undefined) {
                                                        var emailVendorData = await (id_order,vendorDetails.author);
                                                    }
                                                    window.location.href = "<?php echo url('success'); ?>";
                                                });
                                        });

                                    }  else  {

                                        $('#cart_list').html(data.html);
                                        var emailUserData = await sendMailData(id_order,userDetails.id);
                                        if (vendorUser && vendorUser != undefined) {
                                            var emailVendorData = await (id_order,vendorDetails.author);
                                        }

                                        window.location.href = "<?php echo url('success'); ?>";
                                    }
                                }
                            });
                        });
                    }
                }
            });
        });
    }

    //tip_amount
    $(document).on("click", '#Other_tip', function(event) {
        $("#tip_amount").val('');
        $("#add_tip_box").show();
    });
    
    $(document).on("click", '.this_tip', function(event) {
        var this_tip = $(this).val();
        var data = $(this);
        $("#tip_amount").val(this_tip);
        $("#add_tip_box").hide();
        if ((data).is('.tip_checked')) {
            data.removeClass('tip_checked');
            $(this).prop('checked', false);
            $("#tip_amount").val('');
            tipAmountChange('minus');
        } else {
            $(this).addClass('tip_checked');
            tipAmountChange('plus');
        }
    });

    $(document).on("onchange", '#tip_amount', function(event) {
        tipAmountChange();
    });
    
    $(document).on("change", '#scheduleTime', function(event) {
        var scheduleTime = $(this).val();
        $.ajax({
            type: 'POST',
            url: "<?php echo route('order-schedule-time-add'); ?>",
            data: {
                _token: '<?php echo csrf_token(); ?>',
                is_checkout: 1,
                scheduleTime: scheduleTime
            },
            success: function(data) {
                data = JSON.parse(data);
                $('#cart_list').html(data.html);
                loadcurrencynew();
                var today = new Date().toISOString().slice(0, 16);
                if (document.getElementsByName("scheduleTime").length > 0) {
                    document.getElementsByName("scheduleTime")[0].min = today;
                }
                getAdminCommission();
             
            }
        });
    });

    function tipAmountChange() {
        var this_tip = $("#tip_amount").val();
        $.ajax({
            type: 'POST',
            url: "<?php echo route('order-tip-add'); ?>",
            data: {
                _token: '<?php echo csrf_token(); ?>',
                is_checkout: 1,
                tip: this_tip
            },
            success: function(data) {
                data = JSON.parse(data);
                $('#cart_list').html(data.html);
                loadcurrencynew();
                var today = new Date().toISOString().slice(0, 16);
                if (document.getElementsByName("scheduleTime").length > 0) {
                    document.getElementsByName("scheduleTime")[0].min = today;
                }
                getAdminCommission();
              
            }
        });
    }

    function changeNote() {
        var addnote = $("#add-note").val();
        $.ajax({
            type: 'POST',
            url: "<?php echo route('add-cart-note'); ?>",
            data: {
                _token: '<?php echo csrf_token(); ?>',
                addnote: addnote
            },
            success: function(data) {}
        });
    }

    function noNegative(e) {
        if (e.key === '-' || e.key === 'e') {
            e.preventDefault();
        }
    }

    $('input[name="payment_method"]').on('change', function() {
        getCashback();
    })

    async function checkVendorZone() {
        
        const addressId = $('#addressId').val();
        if (!addressId) {
            if (takeaway_options == "false" || takeaway_options == false) {
                Swal.fire({ text: "{{trans('lang.please_select_delivery_address')}}", icon: "warning" });
                return false;
            }
        }

        const lat = parseFloat($('#addressIdLat').val() || '0');   
        const lng = parseFloat($('#addressIdLong').val() || '0');
       
        if (lat === 0 || lng === 0) {
            if (takeaway_options == "false" || takeaway_options == false) {
                Swal.fire({ text: "{{trans('lang.address_missing_location_data')}}", icon: "error" });
                return false;
            }
        }
        
        try {
            
            var zone_list = [];
            var snapshots = await database.collection('zone').where("id", "==", venZoneId).get();
            if (snapshots.docs.length > 0) {
                snapshots.docs.forEach((snapshot) => {
                    var zone_data = snapshot.data();
                    zone_list.push(zone_data);
                });
            }
            if (zone_list.length > 0) {
                for (i = 0; i < zone_list.length; i++) {
                    var zone = zone_list[i];
                    var vertices_x = [];
                    var vertices_y = [];
                    for (j = 0; j < zone.area.length; j++) {
                        var geopoint = zone.area[j];
                        vertices_x.push(geopoint.longitude);
                        vertices_y.push(geopoint.latitude);
                    }
                    var points_polygon = (vertices_x.length) - 1;
                    if (!is_in_polygon(points_polygon, vertices_x, vertices_y, lng, lat)) {                        
                        Swal.fire({
                            title: "{{trans('lang.outside_service_area')}}",
                            text: "{{trans('lang.this_delivery_address_is_not_in_the_vendors_zone_try_another_address')}}",
                            icon: "error"
                        });
                        return false;
                    }else{
                        return true;
                    }
                }
            }

        } catch (err) {
            console.error(err);
            Swal.fire({ text: "{{trans('lang.could_not_verify_delivery_zone')}}", icon: "error" });
            return false;
        }
    }
    
</script>

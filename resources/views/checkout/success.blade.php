@include('layouts.app')
@include('layouts.header')
@php
    $filepath = public_path('tz-cities-to-countries.json');
    $cityToCountry = file_get_contents($filepath);
    $cityToCountry = json_decode($cityToCountry, true);
    $countriesJs = [];
    foreach ($cityToCountry as $key => $value) {
        $countriesJs[$key] = $value;
    }
@endphp
<div class="siddhi-checkout">
    <div class="container position-relative">
        <div class="py-5 row">
            <div class="col-md-12 mb-3">
                <div>
                    <div class="siddhi-cart-item mb-3 rounded shadow-sm bg-white overflow-hidden">
                        <div class="siddhi-cart-item-profile bg-white p-3">
                            <div class="card card-default">
                                <?php $authorName = @$cart['cart_order']['authorName']; ?>
                                @if ($message = Session::get('success'))
                                    <div class="py-5 linus-coming-soon d-flex justify-content-center align-items-center">
                                        <div class="col-md-6">
                                            <div class="text-center pb-3">
                                                <h1 class="font-weight-bold"><?php if (@$authorName) {
                                                    echo @$authorName . ',';
                                                } ?> {{ trans('lang.your_order_has_been_successful') }}</h1>
                                                <p>{{ trans('lang.check_your_order_status_in') }} <a href="{{ route('my_order') }}?activeTab=progress" class="font-weight-bold text-decoration-none text-primary">{{trans('lang.my_orders')}}</a> {{trans('lang.about_next_steps_information')}}</p>
                                            </div>
                                            <div class="bg-white rounded text-center p-4 shadow-sm">
                                                <h1 class="display-1 mb-4">🎉</h1>
                                                <h6 class="font-weight-bold mb-2">{{ trans('lang.preparing_your_order') }}</h6>
                                                <p class="small text-muted">{{ trans('lang.your_order_will_be_prepared_and_will_come_soon') }}</p>
                                                <a href="{{ url('my_order') }}?activeTab=progress" class="btn rounded btn-primary btn-lg btn-block">{{ trans('lang.view_order') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@include('layouts.nav')

@if ($message = Session::get('success'))

    <script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>

    <script type="text/javascript">

        var cityToCountry = '<?php echo json_encode($countriesJs); ?>';
        cityToCountry = JSON.parse(cityToCountry);
        var userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        var userCity = userTimeZone.split('/')[1];
        var userCountry = cityToCountry[userCity];
        var fcmToken = '';
        var id_order = database.collection('tmp').doc().id;
        var userId = "<?php echo $id; ?>";
        var userDetailsRef = database.collection('users').where('id', "==", userId);
        var vendorDetailsRef = database.collection('vendors');
        var uservendorDetailsRef = database.collection('users');
        var AdminCommission = database.collection('settings').doc('AdminCommission');
        var razorpaySettings = database.collection('settings').doc('razorpaySettings');

        var cart = @json($cart);
        var taxSetting = cart?.taxSetting ?? [];
        var taxScope = cart?.taxScope ?? 'order';
        var driverDeliveryTax = cart?.taxesByScope?.delivery ?? [];
        var packagingTax       = cart?.taxesByScope?.packaging ?? [];
        var platformTax        = cart?.taxesByScope?.platform ?? [];
        var platformCharge = cart?.platformCharge ?? '0';
        var packagingChargeEnable = cart?.packagingChargeEnable ?? false;

        <?php if(@$cart['payment_status'] == true && !empty(@$cart['cart_order']['order_json'])){ ?>
            
            $("#data-table_processing").show();

            <?php 
                if (isset($cart['cart_order']['order_json']['bestCashback'])) {
                    $cart['cart_order']['order_json']['bestCashback'] = json_decode($cart['cart_order']['order_json']['bestCashback'], true);
                } 
            ?>
            
            var order_json = '<?php echo json_encode($cart['cart_order']['order_json']); ?>';
            var notes = '<?php echo @$cart['order-note']; ?>';
            order_json = JSON.parse(order_json);
            var main_restaurant_id = $("#main_restaurant_id").val();
            uservendorDetailsRef.where('vendorID', "==", order_json.vendorID).get().then(async function(uservendorSnapshots) {
                if (uservendorSnapshots.docs.length) {
                    var userVendorDetails = uservendorSnapshots.docs[0].data();
                    if (userVendorDetails && userVendorDetails.fcmToken) {
                        fcmToken = userVendorDetails.fcmToken;
                    }
                }
            });
            
            finalCheckout();

            function finalCheckout() {
                userDetailsRef.get().then(async function(userSnapshots) {
                    var userDetails = userSnapshots.docs[0].data();
                    var payment_method = '<?php echo $payment_method; ?>';
                    var vendorID = order_json.vendorID;
                    vendorDetailsRef.where('id', '==', vendorID).get().then(async function(vendorSnapshots) {
                        if (vendorSnapshots.docs.length > 0) {
                            var vendorDetails = vendorSnapshots.docs[0].data();
                            var vendorUser = await getVendorUser(vendorDetails.author);
                            var createdAt = firebase.firestore.FieldValue.serverTimestamp();
                            if (order_json.take_away == 'true') {
                                order_json.take_away = true;
                            }
                            if (order_json.take_away == 'false') {
                                order_json.take_away = false;
                            }
                            for (var n = 0; n < order_json.products.length; n++) {
                                if (order_json.products[n].photo == null && order_json.products[n].photo == "") {
                                    order_json.products[n].photo = "";
                                }
                                if (order_json.products[n].size == null) {
                                    order_json.products[n].size = "";
                                }
                                order_json.products[n].quantity = parseInt(order_json.products[n].quantity);

                                let taxSettingConvert = order_json.products[n].taxSetting;

                                if (Array.isArray(taxSettingConvert) && taxSettingConvert.length > 0) {

                                    order_json.products[n].taxSetting = taxSettingConvert.map(function(tax) {
                                        return {
                                            ...tax,
                                            enable: tax.enable === true
                                                ? true
                                                : tax.enable === false
                                                ? false
                                                : tax.enable === 'true'
                                                ? true
                                                : tax.enable === 'false'
                                                ? false
                                                : tax.enable
                                        };
                                    });
                                }
                            }
                            var discount = 0;
                            if (order_json.discount) {
                                discount = parseInt(order_json.discount);
                            }
                            var scheduleTime = null;
                            if (order_json.scheduleTime && order_json.scheduleTime != '' && order_json.scheduleTime != undefined) {
                                scheduleTime = new Date(order_json.scheduleTime);
                            }
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
                            if (order_json.address) {
                                var location = {
                                    'latitude': parseFloat(order_json.address.location.latitude),
                                    'longitude': parseFloat(order_json.address.location.longitude)
                                };
                                address = {
                                    'address': order_json.address.address,
                                    'addressAs': order_json.address.addressAs,
                                    'id': order_json.address.id,
                                    'isDefault': (order_json.address.isDefault == "true" || order_json.address.isDefault == true) ? true : false,
                                    'landmark': order_json.address.landmark,
                                    'locality': order_json.address.locality,
                                    'location': location
                                };
                            }
                            database.collection('restaurant_orders').doc(id_order).set({
                                'address': address,
                                'author': userDetails,
                                'authorID': order_json.authorID,
                                'couponCode': (order_json.couponCode == null) ? "" : order_json.couponCode,
                                'couponId': (order_json.couponId == null) ? "" : order_json.couponId,
                                'discount': parseFloat(discount),
                                "createdAt": createdAt,
                                'id': id_order,
                                'products': order_json.products,
                                'status': order_json.status,
                                'vendor': vendorDetails,
                                'vendorID': vendorDetails.id,
                                'deliveryCharge': order_json.deliveryCharge,
                                'tip_amount': order_json.tip_amount,
                                'adminCommission': order_json.adminCommission,
                                'adminCommissionType': order_json.adminCommissionType,
                                'payment_method': payment_method,
                                'takeAway': order_json.take_away,
                                'taxSetting': taxSetting,
                                "notes": notes,
                                "specialDiscount": order_json.specialDiscount,
                                "scheduleTime": scheduleTime,
                                'cashback': order_json.bestCashback,
                                'isPosOrder': false,
                                'taxScope': taxScope,
                                "driverDeliveryTax": driverDeliveryTax,
                                "packagingTax": packagingTax,
                                "platformFee": platformCharge,
                                "platformTax": platformTax,
                                "packagingChargeEnable": packagingChargeEnable

                            }).then(async function(result) {

                                if (order_json.bestCashback != null && order_json.bestCashback != undefined) {
                                    var cId = database.collection('temp').doc().id;
                                    await database.collection('cashback_redeem').doc(cId).set({
                                        'id': cId,
                                        'createdAt': firebase.firestore.FieldValue.serverTimestamp(),
                                        'cashbackId': order_json.bestCashback.id,
                                        'userId': order_json.authorID,
                                        'orderId': id_order
                                    });
                                }
                                $.ajax({
                                    type: 'POST',
                                    url: "<?php echo route('order-complete'); ?>",
                                    data: {
                                        _token: '<?php echo csrf_token(); ?>',
                                        'fcm': fcmToken,
                                        'authorName': userDetails.firstName,
                                        'subject': order_json.subject,
                                        'message': order_json.message
                                    },
                                    success: async function(data) {
                                        var emailUserData = await sendMailData(id_order, userDetails.id); // FIXED: Added comma
                                        if (vendorUser && vendorUser != undefined) {
                                            var emailVendorData = await sendMailData(id_order, vendorDetails.author); // FIXED: Added sendMailData function call and comma
                                        }
                                        $("#data-table_processing").hide();
                                    }
                                });
                            });
                        }
                    });
                });
            }
            
            async function getVendorUser(vendorUserId) { 
                var vendorUSerData = '';
                await database.collection('users').where('id', "==", vendorUserId).get().then(async function(uservendorSnapshots) {
                    if (uservendorSnapshots.docs.length) {
                        vendorUSerData = uservendorSnapshots.docs[0].data();
                    }
                });
                return vendorUSerData;
            }

        <?php } ?>
        
    </script>
@endif

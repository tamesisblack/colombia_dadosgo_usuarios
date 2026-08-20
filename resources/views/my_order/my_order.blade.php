@include('layouts.app')
@include('layouts.header')
@php
    $filepath = public_path('tz-cities-to-countries.json');
    $cityToCountry = file_get_contents($filepath);
    $cityToCountry = json_decode($cityToCountry);
    $countriesJs = [];
    foreach ($cityToCountry as $key => $value) {
        $countriesJs[$key] = $value;
    }
@endphp
<div class="d-none">
    <div class="bg-primary border-bottom p-3 d-flex align-items-center">
        <a class="toggle togglew toggle-2" href="#"><span></span></a>
        <h4 class="font-weight-bold m-0 text-white">{{ trans('lang.my_orders') }}</h4>
    </div>
</div>
<section class="py-4 siddhi-main-body">
    <input type="hidden" name="deliveryChargeMain" id="deliveryChargeMain">
    <div class="container">
        <div class="row">
            <div class="col-md-12 top-nav mb-3">
                <ul class="nav nav-tabsa custom-tabsa border-0 bg-white rounded overflow-hidden shadow-sm p-2 c-t-order" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link border-0 text-dark py-3 active" id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="true">
                            <i class="feather-check mr-2 text-success mb-0"></i> {{ trans('lang.completed') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" id="progress-tab" data-toggle="tab" href="#progress" role="tab" aria-controls="progress" aria-selected="false">
                            <i class="feather-clock mr-2 text-warning mb-0"></i> {{ trans('lang.on_progress') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false">
                            <i class="feather-slash mr-2 text-danger mb-0"></i> {{ trans('lang.rejected') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" id="canceled-tab" data-toggle="tab" href="#canceled" role="tab" aria-controls="canceled" aria-selected="false">
                            <i class="feather-x-circle mr-2 text-danger mb-0"></i> {{ trans('lang.canceled') }}</a>
                    </li>

                </ul>
            </div>
            <div class="tab-content col-md-12" id="myTabContent">
                <div class="tab-pane fade show active" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <div class="order-body">
                        <div id="completed_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                    <div class="order-body">
                        <div id="pending_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                    <div class="order-body">
                        <div id="rejected_orders"></div>
                    </div>
                </div>
                <div class="tab-pane fade" id="canceled" role="tabpanel" aria-labelledby="canceled-tab">
                    <div class="order-body">
                        <div id="cancelled_orders"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('layouts.footer')
@include('layouts.nav')

<script type="text/javascript">

    cityToCountry = '<?php echo json_encode($countriesJs); ?>';
    cityToCountry = JSON.parse(cityToCountry);
    var userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    userCity = userTimeZone.split('/')[1];
    userCountry = cityToCountry[userCity];

    var append_categories = '';
    var completedorsersref = database.collection('restaurant_orders').where("author.id", "==", user_uuid).orderBy('createdAt', 'desc');

    var deliveryCharge = 0;
    var inValidVendors = new Set();

    var currencyData = '';
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;

    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function(snapshots) {
        currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });
    
    var deliveryChargeRef = database.collection('settings').doc('DeliveryCharge');
    deliveryChargeRef.get().then(async function(deliveryChargeSnapshots) {
        var deliveryChargeData = deliveryChargeSnapshots.data();
        deliveryCharge = deliveryChargeData.amount;
        $("#deliveryChargeMain").val(deliveryCharge);
    });
    var distanceType = 'km';
    
    var radiusRef = database.collection('settings').doc('RestaurantNearBy');
    radiusRef.get().then(async function(snapshot) {
        var radiusData = snapshot.data();
        distanceType = radiusData.distanceType;
    })
    
    var taxScope = '';
    var isSelfDeliveryGlobally = false;
    var refGlobal = database.collection('settings').doc("globalSettings");
    refGlobal.get().then(async function(
        settingSnapshots) {
        if (settingSnapshots.data()) {
            var settingData = settingSnapshots.data();
            if (settingData.isSelfDelivery) {
                isSelfDeliveryGlobally = true;
            }
            taxScope = settingData.taxScope;
        }
    })

    var taxSetting = [];
    const scopes = ['delivery', 'order', 'packaging', 'platform', 'product'];
    const taxesByScope = {};
    database.collection('tax').where('country', '==', userCountry).where('enable', '==', true).where('scope', 'in', scopes).get().then(snapshot => {
        snapshot.forEach(doc => {
            const tax = doc.data();
            (taxesByScope[tax.scope] ??= []).push(tax);
        });
    });

    var platformCharge = '0';
    database.collection('settings').doc("adminSettings").get().then(snapshot => {
        var platformData = snapshot.data();
        if(platformData.enable){
            platformCharge = platformData.amount;
        }
    });

    var packagingCharge = '0';
    var packagingChargeEnable = false;
    database.collection('settings').doc("restaurant").get().then(snapshot => {
        var restaurant_data = snapshot.data();
        packagingChargeEnable = restaurant_data.packagingChargeEnable
    });

    var place_holder_image = '';
    var ref_placeholder_image = database.collection('settings').doc("placeHolderImage");
    ref_placeholder_image.get().then(async function(snapshots) {
        var placeHolderImage = snapshots.data();
        place_holder_image = placeHolderImage.image;
    });
    $(document).ready(async function() {
        // Retrieve all invalid vendors
        await checkVendors().then(expiredStores => {
            inValidVendors = expiredStores;
        });
        
        getOrders();
        getActiveTab();

        $(document).on("click", '.reorder-add-to-cart', async function(event) {

            var order_id = $(this).attr('data-id');
            
            var item = [];
            jQuery(".order_" + order_id).each(function() {
                var category_id = jQuery(this).find('.category_id').val();
                var id = jQuery(this).find('.product_id').val();
                var name = jQuery(this).find('.name').val();
                var price = jQuery(this).find('.price').val();
                var image = jQuery(this).find('.image').val();
                var quantity = jQuery(this).find('.quantity').val();
                var extra_price = jQuery(this).find('.extra_price').val();
                var extra = jQuery(this).find('.extra').val();
                var size = jQuery(this).find('.size').val();
                var item_price = jQuery(this).find('.item_price').val();
                var taxSetting = JSON.parse(jQuery(this).find('.taxSetting').val());
                var item_arr = {
                    'category_id': category_id,
                    'id': id,
                    'name': name,
                    'image': image,
                    'price': price,
                    'quantity': quantity,
                    'extra_price': extra_price,
                    'extra': extra,
                    'size': size,
                    'item_price': item_price,
                    'taxSetting': taxSetting,
                }
                item.push(item_arr);
            });

            var restaurant_id = jQuery(".restid_" + order_id).val();
            var isSelfDeliveryByVendor = false;
            
            await database.collection('vendors').doc(restaurant_id).get().then(async function(snapshot) {
                if (snapshot.exists) {
                    data = snapshot.data();
                    if (data.hasOwnProperty('isSelfDelivery') && data.isSelfDelivery) {
                        isSelfDeliveryByVendor = true;
                    }
                    packagingCharge = data.packagingCharge;
                }
            })
            
            var restaurant_name = jQuery(".resttitle_" + order_id).val();
            var restaurant_location = jQuery(".restlocation_" + order_id).val();
            var restaurant_latitude = jQuery(".restlatitude_" + order_id).val();
            var restaurant_longitude = jQuery(".restlongitude_" + order_id).val();
            
            setCookie('restaurant_longitude', restaurant_longitude, 365);
            setCookie('restaurant_latitude', restaurant_latitude, 365);
            
            var restaurant_image = jQuery(".restphoto_" + order_id).val();
            var deliveryCharge = $("#deliveryChargeMain").val();
            
            const payload = {
                _token: '<?php echo csrf_token(); ?>',
                restaurant_id,
                restaurant_location,
                restaurant_name,
                restaurant_image,
                restaurant_latitude,
                restaurant_longitude,
                item,
                deliveryCharge,
                taxSetting,
                decimal_degits,
                distanceType,
                taxScope,
                taxesByScope,
                packagingCharge,
                platformCharge,
                packagingChargeEnable,
                currencyData,
                isSelfDelivery:(isSelfDeliveryByVendor && isSelfDeliveryGlobally) ? true : false
            };

            $.ajax({
                type: 'POST',
                url: "<?php echo route('reorder-add-to-cart'); ?>",
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(payload),
                success: function(data) {
                    window.location.href = '{{ route('checkout') }}';
                }
            });
        });
    });

    function getActiveTab() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('activeTab');
        const newUrl = window.location.href.replace(/[?&]activeTab=[^&]+/, '').replace(/&$/, '').replace(/\?$/, '');
        history.replaceState(null, null, newUrl);
        if (activeTab) {
            const defaultActiveTab = document.querySelector('.tab-pane.fade.show.active');
            const defaultActiveTabClass = document.querySelector('.nav-link.border-0.text-dark.py-3.active');
            if (defaultActiveTab) {
                defaultActiveTab.classList.remove('show', 'active');
                defaultActiveTabClass.classList.remove('show', 'active');
            }
            const tabElement = document.querySelector(`#${activeTab}-tab`);
            if (tabElement) {
                tabElement.classList.add('active');
                const tabContentElement = document.querySelector(`#${activeTab}`);
                if (tabContentElement) {
                    tabContentElement.classList.add('show', 'active');
                }
            }
        }
    }
    async function getOrders() {
        jQuery("#data-table_processing").show();
        completedorsersref.get().then(async function(completedorderSnapshots) {
            completed_orders = document.getElementById('completed_orders');
            pending_orders = document.getElementById('pending_orders');
            rejected_orders = document.getElementById('rejected_orders');
            cancelled_orders = document.getElementById('cancelled_orders');
            completed_orders.innerHTML = '';
            pending_orders.innerHTML = '';
            rejected_orders.innerHTML = '';
            cancelled_orders.innerHTML = '';
            completedOrderHtml = await buildHTMLCompletedOrders(completedorderSnapshots);
            completed_orders.innerHTML = completedOrderHtml;
            pendingOrderHtml = await buildHTMLPendingOrders(completedorderSnapshots);
            pending_orders.innerHTML = pendingOrderHtml;
            rejectedOrdersHtml = await buildHTMLRejectedOrders(completedorderSnapshots);
            rejected_orders.innerHTML = rejectedOrdersHtml;
            cancelledOrdersHtml = await buildHTMLCancelledOrders(completedorderSnapshots);
            cancelled_orders.innerHTML = cancelledOrdersHtml;            
        })
        jQuery("#data-table_processing").hide();
    }

    async function buildHTMLCompletedOrders(completedorderSnapshots) {
        jQuery("#data-table_processing").show();
        var html = '';
        var alldata = [];
        var number = [];
        completedorderSnapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
        for (const listval of alldata) {
            var val = listval;
            if (val.status == "Order Completed") {
                var order_id = val.id;
                var view_details = "{{ route('completed_order', ':id') }}";
                view_details = view_details.replace(':id', 'id=' + order_id);
                var orderDetails = "{{ route('orderDetails', ':id') }}";
                orderDetails = orderDetails.replace(':id', 'id=' + order_id);
                var view_contact = "{{ route('contact_us') }}";
                var view_checkout = "{{ route('checkout') }}";
                let showReorder = false;

            try {
                const productSnap = await database
                    .collection('vendor_products')
                    .where("vendorID", "==", val.vendorID)
                    .where("categoryID", "==", val.products[0].category_id)
                    .limit(1)
                    .get();

                if (!productSnap.empty) {
                    showReorder = productSnap.docs[0].data().publish === true;
                }
            } catch (e) {
                console.error("Reorder check failed:", e);
                jQuery("#data-table_processing").hide();
            }    
            jQuery("#data-table_processing").hide();
                if (!inValidVendors.has(val.vendorID)) {
                    var view_restaurant_details = "{{ route('restaurant', ':id') }}";
                    view_restaurant_details = view_restaurant_details.replace(':id', 'id=' + val.vendorID);
                } else {
                    view_restaurant_details = "javascript:void(0)";
                }
                var orderRestaurantImage = '';
                if (val.vendor.hasOwnProperty('photo') && val.vendor.photo != '' && val.vendor.photo != null) {
                    orderRestaurantImage = val.vendor.photo;
                } else {
                    orderRestaurantImage = place_holder_image;
                }
                html = html +
                    '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><img onerror="this.onerror=null;this.src=\'' +
                    place_holder_image + '\'" alt="#" src="' + orderRestaurantImage +
                    '" class="img-fluid order_img rounded"></div><div><p class="mb-0 font-weight-bold"><a href="' +
                    view_restaurant_details + '" class="text-dark">' + val.vendor.title +
                    '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.vendor.location +
                    '</p><p>{{trans("lang.order_caps")}} ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details +
                    '">{{trans("lang.view_details")}}</a></p></div><div class="ml-auto ord-com-btn"><p class="bg-success text-white py-1 px-2 rounded small mb-1">' +
                    val.status +
                    '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + val
                    .createdAt.toDate().toDateString() +
                    '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small">';
                
                let order_subtotal = 0;
                let total_discount = 0;
                let total_tax_amount = 0;
                let tip_amount = parseFloat(val.tip_amount || 0);
                let deliveryCharge = parseFloat(val.deliveryCharge || 0);
                let platformFee = parseFloat(val.platformFee || 0);
                let packagingCharge = val.packagingChargeEnable ? parseFloat(val.vendor.packagingCharge || 0) : 0;

                //  Calculate subtotal and product extras
                for (let i = 0; i < val.products.length; i++) {
                    let product = val.products[i];
                    let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                    let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                    order_subtotal += itemGross;
                }

                // Total discounts
                let order_discount = parseFloat(val.discount || 0);
                let special_discount = parseFloat(val.specialDiscount?.special_discount || 0);
                    total_discount = order_discount + special_discount;

                // Calculate item-level taxes (if product-level)
                if (val.taxScope === "product") {
                    let adminTaxes = taxesByScope['product'] || [];
                    let itemSubtotal = order_subtotal;
                    val.products.forEach(product => {
                        let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                        let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                        let itemDiscount = (itemSubtotal > 0) ? (itemGross / itemSubtotal) * total_discount : 0;
                        let itemTaxable = Math.max(0, itemGross - itemDiscount);
                        let itemTaxes = product.taxSetting || [];
                        itemTaxes.forEach(tax => {
                            let match = adminTaxes.find(t => t.id === tax.id);
                            if (tax.enable && match) {
                                let taxAmount = 0;
                                if (tax.type === "percentage") {
                                    taxAmount = (tax.tax / 100) * itemTaxable;
                                } else {
                                    taxAmount = tax.tax * product.quantity;
                                }
                                total_tax_amount += parseFloat(taxAmount);
                            }
                        });
                    });
                } 

                // Order-level taxes (if order-level)
                if (val.taxScope === "order") {
                    let orderTaxable = Math.max(0, order_subtotal - total_discount);
                    (val.taxSetting || []).forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * orderTaxable;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                }

                // Delivery, packaging, platform taxes
                let extraCharges = [
                    {amount: deliveryCharge, taxes: val.driverDeliveryTax || []},
                    {amount: packagingCharge, taxes: val.packagingTax || []},
                    {amount: platformFee, taxes: val.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * scope.amount;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                });

                //Final subtotal after discounts
                order_subtotal = order_subtotal - total_discount;

                // Final total
                let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;

                if (currencyAtRight) {
                    order_total_val = parseFloat(order_total).toFixed(decimal_degits) + '' + currentCurrency;
                } else {
                    order_total_val = currentCurrency + '' + parseFloat(order_total).toFixed(decimal_degits);
                }

                for (let i = 0; i < val.products.length; i++) {
                    var productExtras = 0;
                    if (val.products[i].hasOwnProperty('extras_price') && val.products[i].hasOwnProperty('extras')) {
                        if (val.products[i].extras_price) {
                            productExtras = val.products[i].extras_price;
                        }
                    }
                    var extras = '';
                    if (val.products[i].hasOwnProperty('extras') && val.products[i].extras != '') {
                        extras = val.products[i].extras;
                    }
                    var size = '';
                    if (val.products[i].hasOwnProperty('size') && val.products[i].size != '') {
                        size = val.products[i].size;
                    }
                    html = html + '<p class="text- font-weight-bold mb-0">' + val.products[i]['name'] + ' x ' +
                        val.products[i]['quantity'] + '</p>';
                    if (val.products[i]['variant_info']) {
                        html = html + '<div class="variant-info">';
                        html = html + '<ul>';
                        $.each(val.products[i]['variant_info']['variant_options'], function(label, value) {
                            html = html + '<li class="variant"><span class="label">' + label +
                                '</span><span class="value">' + value + '</span></li>';
                        });
                        html = html + '</ul>';
                        html = html + '</div>';
                    }
                    
                    html = html + '<div class="order_' + String(order_id) + '">';
                    html = html + '<input type="hidden" class="category_id" value="' + String(val.products[i][
                        'category_id'
                    ]) + '">';
                    html = html + '<input type="hidden" class="product_id" value="' + String(val.products[i][
                        'id'
                    ]) + '">';
                    html = html + '<input type="hidden" class="name" value="' + String(val.products[i][
                        'name'
                    ]) + '">';
                    html = html + '<input type="hidden" class="image" value="' + String(val.products[i][
                        'photo'
                    ]) + '">';
                    html = html + '<input type="hidden" class="price" value="' + parseFloat(val.products[i][
                        'price'
                    ]) + '">';
                    html = html + '<input type="hidden" class="quantity" value="' + parseFloat(val.products[i][
                        'quantity'
                    ]) + '">';
                    html = html + '<input type="hidden" class="extra_price" value="' + parseFloat(
                        productExtras) + '">';
                    html = html + '<input type="hidden" class="item_price" value="' + parseFloat(val.products[i]
                        ['price']) + '">';
                    html = html + '<input type="hidden" class="extra" value="' + extras + '">';
                    html = html + '<input type="hidden" class="size" value="' + size + '">';
                    html = html + '<input type="hidden" class="taxSetting" value=\'' + JSON.stringify(val.products[i].taxSetting) + '\'>';
                    
                    html = html + '</div>';
                }
                
                html = html + '<input type="hidden" class="restid_' + String(order_id) + '" value="' + val
                    .vendor.id + '">';
                html = html + '<input type="hidden" class="resttitle_' + String(order_id) + '" value="' + val
                    .vendor.title + '">';
                html = html + '<input type="hidden" class="restlocation_' + String(order_id) + '" value="' + val
                    .vendor.location + '">';
                html = html + '<input type="hidden" class="restlatitude_' + String(order_id) + '" value="' + val
                    .vendor.latitude + '">';
                html = html + '<input type="hidden" class="restlongitude_' + String(order_id) + '" value="' +
                    val
                    .vendor.longitude + '">';
                html = html + '<input type="hidden" class="restphoto_' + String(order_id) + '" value="' + val
                    .vendor.photo + '">';
                html = html + '<input type="hidden" class="deliveryCharge_' + String(order_id) + '" value="' +
                    deliveryCharge + '">';
                html = html +
                    '</div><div class="text-muted m-0 ml-auto mr-3 small">Total a pagar<br><span class="text-dark font-weight-bold">' +
                    order_total_val +
                    '</span></div><div class="text-right">';
                if (showReorder && !inValidVendors.has(val.vendorID)) {
                    html +=
                        ' <a href="javascript:void(0);" class="btn btn-primary px-3 reorder-add-to-cart mr-2" data-id="' +
                        String(order_id) + '">Volver a pedir</a>';
                }
                html = html + '<a href="' + view_contact +
                    '" class="btn btn-outline-primary px-3">{{trans("lang.help")}}</a> </div></div></div></div></div></div>';
            }
        }
        jQuery("#data-table_processing").hide();
        return html;
    }

 
    async function buildHTMLPendingOrders(completedorderSnapshots) {
        jQuery("#data-table_processing").show();
        var html = '';
        var alldata = [];
        var number = [];

        completedorderSnapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });

        for (const listval of alldata) {

            var val = listval;
            var order_id = val.id;

            var view_details = "{{ route('pending_order', ':id') }}";
            view_details = view_details.replace(':id', 'id=' + order_id);
            var view_contact = "{{ route('contact_us') }}";

            let showReorder = false;

            try {
                const productSnap = await database
                    .collection('vendor_products')
                    .where("vendorID", "==", val.vendorID)
                    .where("categoryID", "==", val.products[0].category_id)
                    .limit(1)
                    .get();

                if (!productSnap.empty) {
                    showReorder = productSnap.docs[0].data().publish === true;
                }
            } catch (e) {
                console.error("Reorder check failed:", e);
                jQuery("#data-table_processing").hide();
            }           
            jQuery("#data-table_processing").hide();
            if (!inValidVendors.has(val.vendorID)) {
                var view_restaurant_details = "{{ route('restaurant', ':id') }}";
                view_restaurant_details = view_restaurant_details.replace(':id', 'id=' + val.vendorID);
            } else {
                view_restaurant_details = "javascript:void(0)";
            }

            if (
                val.status == "Order Placed" ||
                val.status == "Order Accepted" ||
                val.status == "Driver Pending" ||
                val.status == "Order Shipped" ||
                val.status == "In Transit"
            ) {

                var orderRestaurantImage = (val.vendor.photo) ? val.vendor.photo : place_holder_image;

                html +=
                    '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white">' +
                    '<div class="d-flex border-bottom pb-3 m-d-flex">' +
                    '<div class="text-muted mr-3">' +
                    '<img onerror="this.onerror=null;this.src=\'' + place_holder_image + '\'" src="' + orderRestaurantImage + '" class="img-fluid order_img rounded">' +
                    '</div>' +
                    '<div>' +
                    '<p class="mb-0 font-weight-bold"><a href="' + view_restaurant_details + '" class="text-dark">' + val.vendor.title + '</a></p>' +
                    '<p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.vendor.location + '</p>' +
                    '<p>{{trans("lang.order_caps")}} ' + val.id + '</p>' +
                    '<p class="mb-0 small view-det"><a href="' + view_details + '">{{trans("lang.view_details")}}</a></p>' +
                    '</div>' +
                    '<div class="ml-auto ord-com-btn">' +
                    '<p class="bg-pending text-white py-1 px-2 rounded small mb-1">' + val.status + '</p>' +
                    '<p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' +
                    val.createdAt.toDate().toDateString() + '</p>' +
                    '</div></div>' +
                    '<div class="d-flex pt-3 m-d-flex"><div class="small ">';

                let order_subtotal = 0;
                let total_discount = 0;
                let total_tax_amount = 0;
                let tip_amount = parseFloat(val.tip_amount || 0);
                let deliveryCharge = parseFloat(val.deliveryCharge || 0);
                let platformFee = parseFloat(val.platformFee || 0);
                let packagingCharge = val.packagingChargeEnable ? parseFloat(val.vendor.packagingCharge || 0) : 0;

                for (let i = 0; i < val.products.length; i++) {
                    let product = val.products[i];
                    let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                    let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                    order_subtotal += itemGross;
                }

                let order_discount = parseFloat(val.discount || 0);
                let special_discount = parseFloat(val.specialDiscount?.special_discount || 0);
                total_discount = order_discount + special_discount;

                // Calculate item-level taxes (if product-level)
                if (val.taxScope === "product") {
                    let adminTaxes = taxesByScope['product'] || [];
                    let itemSubtotal = order_subtotal;
                    val.products.forEach(product => {
                        let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                        let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                        let itemDiscount = (itemSubtotal > 0) ? (itemGross / itemSubtotal) * total_discount : 0;
                        let itemTaxable = Math.max(0, itemGross - itemDiscount);
                        let itemTaxes = product.taxSetting || [];
                        itemTaxes.forEach(tax => {
                            let match = adminTaxes.find(t => t.id === tax.id);
                            if (tax.enable && match) {
                                let taxAmount = 0;
                                if (tax.type === "percentage") {
                                    taxAmount = (tax.tax / 100) * itemTaxable;
                                } else {
                                    taxAmount = tax.tax * product.quantity;
                                }
                                total_tax_amount += parseFloat(taxAmount);
                            }
                        });
                    });
                } 

                // Order-level taxes (if order-level)
                if (val.taxScope === "order") {
                    let orderTaxable = Math.max(0, order_subtotal - total_discount);
                    (val.taxSetting || []).forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * orderTaxable;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                }

                // Delivery, packaging, platform taxes
                let extraCharges = [
                    {amount: deliveryCharge, taxes: val.driverDeliveryTax || []},
                    {amount: packagingCharge, taxes: val.packagingTax || []},
                    {amount: platformFee, taxes: val.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * scope.amount;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                });

                order_subtotal = order_subtotal - total_discount;

                let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;

               if (currencyAtRight) {
                    order_total_val = parseFloat(order_total).toFixed(decimal_degits) + '' + currentCurrency;
                } else {
                    order_total_val = currentCurrency + '' + parseFloat(order_total).toFixed(decimal_degits);
                }
                
                for (let i = 0; i < val.products.length; i++) {
                    var productExtras = 0;
                    if (val.products[i].hasOwnProperty('extras_price') && val.products[i].hasOwnProperty('extras')) {
                        if (val.products[i].extras_price) {
                            productExtras = val.products[i].extras_price;
                        }
                    }
                    var extras = '';
                    if (val.products[i].hasOwnProperty('extras') && val.products[i].extras != '') {
                        extras = val.products[i].extras;
                    }
                    var size = '';
                    if (val.products[i].hasOwnProperty('size') && val.products[i].size != '') {
                        size = val.products[i].size;
                    }
                    html = html + '<p class="text- font-weight-bold mb-0">' + val.products[i]['name'] + ' x ' +
                        val.products[i]['quantity'] + '</p>';
                    if (val.products[i]['variant_info']) {
                        html = html + '<div class="variant-info">';
                        html = html + '<ul>';
                        $.each(val.products[i]['variant_info']['variant_options'], function(label, value) {
                            html = html + '<li class="variant"><span class="label">' + label +
                                '</span><span class="value">' + value + '</span></li>';
                        });
                        html = html + '</ul>';
                        html = html + '</div>';
                    }
                    
                    html = html + '<div class="order_' + String(order_id) + '">';
                    html = html + '<input type="hidden" class="category_id" value="' + String(val.products[i][
                        'category_id'
                    ]) + '">';
                    html = html + '<input type="hidden" class="product_id" value="' + String(val.products[i][
                        'id'
                    ]) + '">';
                    html = html + '<input type="hidden" class="name" value="' + String(val.products[i][
                        'name'
                    ]) + '">';
                    html = html + '<input type="hidden" class="image" value="' + String(val.products[i][
                        'photo'
                    ]) + '">';
                    html = html + '<input type="hidden" class="price" value="' + parseFloat(val.products[i][
                        'price'
                    ]) + '">';
                    html = html + '<input type="hidden" class="quantity" value="' + parseFloat(val.products[i][
                        'quantity'
                    ]) + '">';
                    html = html + '<input type="hidden" class="extra_price" value="' + parseFloat(
                        productExtras) + '">';
                    html = html + '<input type="hidden" class="item_price" value="' + parseFloat(val.products[i]
                        ['price']) + '">';
                    html = html + '<input type="hidden" class="extra" value="' + extras + '">';
                    html = html + '<input type="hidden" class="size" value="' + size + '">';
                    html = html + '<input type="hidden" class="taxSetting" value=\'' + JSON.stringify(val.products[i].taxSetting) + '\'>';
                    
                    html = html + '</div>';
                }
                
                html = html + '<input type="hidden" class="restid_' + String(order_id) + '" value="' + val
                    .vendor.id + '">';
                html = html + '<input type="hidden" class="resttitle_' + String(order_id) + '" value="' + val
                    .vendor.title + '">';
                html = html + '<input type="hidden" class="restlocation_' + String(order_id) + '" value="' + val
                    .vendor.location + '">';
                html = html + '<input type="hidden" class="restlatitude_' + String(order_id) + '" value="' + val
                    .vendor.latitude + '">';
                html = html + '<input type="hidden" class="restlongitude_' + String(order_id) + '" value="' +
                    val
                    .vendor.longitude + '">';
                html = html + '<input type="hidden" class="restphoto_' + String(order_id) + '" value="' + val
                    .vendor.photo + '">';
                html = html + '<input type="hidden" class="deliveryCharge_' + String(order_id) + '" value="' +
                    deliveryCharge + '">';
                html = html +
                    '</div><div class="text-muted m-0 ml-auto mr-3 small">Total a pagar<br><span class="text-dark font-weight-bold">' +
                    order_total_val +
                    '</span></div><div class="text-right">';
                if (showReorder && !inValidVendors.has(val.vendorID)) {
                    html +=
                        ' <a href="javascript:void(0);" class="btn btn-primary px-3 reorder-add-to-cart mr-2" data-id="' +
                        String(order_id) + '">Volver a pedir</a>';
                }
                html = html + '<a href="' + view_contact +
                    '" class="btn btn-outline-primary px-3">{{trans("lang.help")}}</a> </div></div></div></div></div></div>';
            }
        }
        jQuery("#data-table_processing").hide();
        return html;
    }


    async function buildHTMLRejectedOrders(completedorderSnapshots) {
        jQuery("#data-table_processing").show();
        var html = '';
        var alldata = [];
        var number = [];
        completedorderSnapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
         for (const listval of alldata) {
            var val = listval;
            var order_id = val.id;
            var view_details = "{{ route('rejected_order', ':id') }}";
            view_details = view_details.replace(':id', 'id=' + order_id);
            var view_contact = "{{ route('contact_us') }}";
            var view_checkout = "{{ route('checkout') }}";

            let showReorder = false;

            try {
                const productSnap = await database
                    .collection('vendor_products')
                    .where("vendorID", "==", val.vendorID)
                    .where("categoryID", "==", val.products[0].category_id)
                    .limit(1)
                    .get();

                if (!productSnap.empty) {
                    showReorder = productSnap.docs[0].data().publish === true;
                }
            } catch (e) {
                console.error("Reorder check failed:", e);
                jQuery("#data-table_processing").hide();
            }    
            jQuery("#data-table_processing").hide();
            if (!inValidVendors.has(val.vendorID)) {
                var view_restaurant_details = "{{ route('restaurant', ':id') }}";
                view_restaurant_details = view_restaurant_details.replace(':id', 'id=' + val.vendorID);
            } else {
                view_restaurant_details = "javascript:void(0)";
            }
            if (val.status == "Driver Rejected" || val.status == "Order Rejected") {
                var orderRestaurantImage = '';
                if (val.vendor.hasOwnProperty('photo') && val.vendor.photo != '' && val.vendor.photo != null) {
                    orderRestaurantImage = val.vendor.photo;
                } else {
                    orderRestaurantImage = place_holder_image;
                }
                html = html +
                    '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><img onerror="this.onerror=null;this.src=\'' +
                    place_holder_image + '\'" alt="#" src="' + orderRestaurantImage +
                    '" class="img-fluid order_img rounded"></div><div><p class="mb-0 font-weight-bold"><a href="' +
                    view_restaurant_details + '" class="text-dark">' + val.vendor.title +
                    '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.vendor.location +
                    '</p><p>{{trans("lang.order_caps")}} ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details +
                    '">{{trans("lang.view_details")}}</a></p></div><div class="ml-auto ord-com-btn"><p class="bg-rejected text-white py-1 px-2 rounded small mb-1">' +
                    val.status +
                    '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + val
                    .createdAt.toDate().toDateString() +
                    '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small ">';
                
                let order_subtotal = 0;
                let total_discount = 0;
                let total_tax_amount = 0;
                let tip_amount = parseFloat(val.tip_amount || 0);
                let deliveryCharge = parseFloat(val.deliveryCharge || 0);
                let platformFee = parseFloat(val.platformFee || 0);
                let packagingCharge = val.packagingChargeEnable ? parseFloat(val.vendor.packagingCharge || 0) : 0;

                //  Calculate subtotal and product extras
                for (let i = 0; i < val.products.length; i++) {
                    let product = val.products[i];
                    let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                    let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                    order_subtotal += itemGross;
                }

                // Total discounts
                let order_discount = parseFloat(val.discount || 0);
                let special_discount = parseFloat(val.specialDiscount?.special_discount || 0);
                    total_discount = order_discount + special_discount;

                // Calculate item-level taxes (if product-level)
                if (val.taxScope === "product") {
                    let adminTaxes = taxesByScope['product'] || [];
                    let itemSubtotal = order_subtotal;
                    val.products.forEach(product => {
                        let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                        let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                        let itemDiscount = (itemSubtotal > 0) ? (itemGross / itemSubtotal) * total_discount : 0;
                        let itemTaxable = Math.max(0, itemGross - itemDiscount);
                        let itemTaxes = product.taxSetting || [];
                        itemTaxes.forEach(tax => {
                            let match = adminTaxes.find(t => t.id === tax.id);
                            if (tax.enable && match) {
                                let taxAmount = 0;
                                if (tax.type === "percentage") {
                                    taxAmount = (tax.tax / 100) * itemTaxable;
                                } else {
                                    taxAmount = tax.tax * product.quantity;
                                }
                                total_tax_amount += parseFloat(taxAmount);
                            }
                        });
                    });
                } 

                // Order-level taxes (if order-level)
                if (val.taxScope === "order") {
                    let orderTaxable = Math.max(0, order_subtotal - total_discount);
                    (val.taxSetting || []).forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * orderTaxable;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                }

                // Delivery, packaging, platform taxes
                let extraCharges = [
                    {amount: deliveryCharge, taxes: val.driverDeliveryTax || []},
                    {amount: packagingCharge, taxes: val.packagingTax || []},
                    {amount: platformFee, taxes: val.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * scope.amount;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                });
                //Final subtotal after discounts
                order_subtotal = order_subtotal - total_discount;

                // Final total
                let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;

                if (currencyAtRight) {
                    order_total_val = parseFloat(order_total).toFixed(decimal_degits) + '' + currentCurrency;
                } else {
                    order_total_val = currentCurrency + '' + parseFloat(order_total).toFixed(decimal_degits);
                }

                for (let i = 0; i < val.products.length; i++) {
                    var productExtras = 0;
                    if (val.products[i].hasOwnProperty('extras_price') && val.products[i].hasOwnProperty('extras')) {
                        if (val.products[i].extras_price) {
                            productExtras = val.products[i].extras_price;
                        }
                    }
                    var extras = '';
                    if (val.products[i].hasOwnProperty('extras') && val.products[i].extras != '') {
                        extras = val.products[i].extras;
                    }
                    var size = '';
                    if (val.products[i].hasOwnProperty('size') && val.products[i].size != '') {
                        size = val.products[i].size;
                    }
                    html = html + '<p class="text- font-weight-bold mb-0">' + val.products[i]['name'] + ' x ' +
                        val.products[i]['quantity'] + '</p>';
                    if (val.products[i]['variant_info']) {
                        html = html + '<div class="variant-info">';
                        html = html + '<ul>';
                        $.each(val.products[i]['variant_info']['variant_options'], function(label, value) {
                            html = html + '<li class="variant"><span class="label">' + label +
                                '</span><span class="value">' + value + '</span></li>';
                        });
                        html = html + '</ul>';
                        html = html + '</div>';
                    }
                    
                    html = html + '<div class="order_' + String(order_id) + '">';
                    html = html + '<input type="hidden" class="category_id" value="' + String(val.products[i][
                        'category_id'
                    ]) + '">';
                    html = html + '<input type="hidden" class="product_id" value="' + String(val.products[i][
                        'id'
                    ]) + '">';
                    html = html + '<input type="hidden" class="name" value="' + String(val.products[i][
                        'name'
                    ]) + '">';
                    html = html + '<input type="hidden" class="image" value="' + String(val.products[i][
                        'photo'
                    ]) + '">';
                    html = html + '<input type="hidden" class="price" value="' + parseFloat(val.products[i][
                        'price'
                    ]) + '">';
                    html = html + '<input type="hidden" class="quantity" value="' + parseFloat(val.products[i][
                        'quantity'
                    ]) + '">';
                    html = html + '<input type="hidden" class="extra_price" value="' + parseFloat(
                        productExtras) + '">';
                    html = html + '<input type="hidden" class="item_price" value="' + parseFloat(val.products[i]
                        ['price']) + '">';
                    html = html + '<input type="hidden" class="extra" value="' + extras + '">';
                    html = html + '<input type="hidden" class="size" value="' + size + '">';
                    html = html + '<input type="hidden" class="taxSetting" value=\'' + JSON.stringify(val.products[i].taxSetting) + '\'>';

                    html = html + '</div>';
                }

                html = html + '<input type="hidden" class="restid_' + String(order_id) + '" value="' + val
                    .vendor.id + '">';
                html = html + '<input type="hidden" class="resttitle_' + String(order_id) + '" value="' + val
                    .vendor.title + '">';
                html = html + '<input type="hidden" class="restlocation_' + String(order_id) + '" value="' + val
                    .vendor.location + '">';
                html = html + '<input type="hidden" class="restlatitude_' + String(order_id) + '" value="' + val
                    .vendor.latitude + '">';
                html = html + '<input type="hidden" class="restlongitude_' + String(order_id) + '" value="' +
                    val
                    .vendor.longitude + '">';
                html = html + '<input type="hidden" class="restphoto_' + String(order_id) + '" value="' + val
                    .vendor.photo + '">';
                html = html + '<input type="hidden" class="deliveryCharge_' + String(order_id) + '" value="' +
                    deliveryCharge + '">';
                html = html +
                    '</div><div class="text-muted m-0 ml-auto mr-3 small">Total a pagar<br><span class="text-dark font-weight-bold">' +
                    order_total_val +
                    '</span></div><div class="text-right">';
                if (showReorder && !inValidVendors.has(val.vendorID)) {
                    html +=
                        ' <a href="javascript:void(0);" class="btn btn-primary px-3 reorder-add-to-cart mr-2" data-id="' +
                        String(order_id) + '">Volver a pedir</a>';
                }
                html = html + '<a href="' + view_contact +
                    '" class="btn btn-outline-primary px-3">{{trans("lang.help")}}</a> </div></div></div></div></div></div>';
            }
        }
        jQuery("#data-table_processing").show();
        return html;
    }

    async function buildHTMLCancelledOrders(completedorderSnapshots) {
        jQuery("#data-table_processing").show();
        var html = '';
        var alldata = [];
        var number = [];
        completedorderSnapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
         for (const listval of alldata) {
            var val = listval;
            var order_id = val.id;
            var view_details = "{{ route('cancelled_order', ':id') }}";
            view_details = view_details.replace(':id', 'id=' + order_id);
            var view_contact = "{{ route('contact_us') }}";
            var view_checkout = "{{ route('checkout') }}";
            let showReorder = false;

            try {
                const productSnap = await database
                    .collection('vendor_products')
                    .where("vendorID", "==", val.vendorID)
                    .where("categoryID", "==", val.products[0].category_id)
                    .limit(1)
                    .get();

                if (!productSnap.empty) {
                    showReorder = productSnap.docs[0].data().publish === true;
                }
            } catch (e) {
                console.error("Reorder check failed:", e);
                jQuery("#data-table_processing").hide();
            }    
            jQuery("#data-table_processing").hide();
            if (!inValidVendors.has(val.vendorID)) {
                var view_restaurant_details = "{{ route('restaurant', ':id') }}";
                view_restaurant_details = view_restaurant_details.replace(':id', 'id=' + val.vendorID);
            } else {
                view_restaurant_details = "javascript:void(0)";
            }
            if (val.status == "Order Cancelled") {
                var orderRestaurantImage = '';
                if (val.vendor.hasOwnProperty('photo') && val.vendor.photo != '' && val.vendor.photo != null) {
                    orderRestaurantImage = val.vendor.photo;
                } else {
                    orderRestaurantImage = place_holder_image;
                }
                html = html +
                    '<div class="pb-3"><div class="p-3 rounded shadow-sm bg-white"><div class="d-flex border-bottom pb-3 m-d-flex"><div class="text-muted mr-3"><img onerror="this.onerror=null;this.src=\'' +
                    place_holder_image + '\'" alt="#" src="' + orderRestaurantImage +
                    '" class="img-fluid order_img rounded"></div><div><p class="mb-0 font-weight-bold"><a href="' +
                    view_restaurant_details + '" class="text-dark">' + val.vendor.title +
                    '</a></p><p class="mb-0"><span class="fa fa-map-marker"></span> ' + val.vendor.location +
                    '</p><p>{{trans("lang.order_caps")}} ' + val.id + '</p><p class="mb-0 small view-det"><a href="' + view_details +
                    '">{{trans("lang.view_details")}}</a></p></div><div class="ml-auto ord-com-btn"><p class="bg-rejected text-white py-1 px-2 rounded small mb-1">' +
                    val.status +
                    '</p><p class="small font-weight-bold text-center"><i class="feather-clock"></i> ' + val
                    .createdAt.toDate().toDateString() +
                    '</p></div></div><div class="d-flex pt-3 m-d-flex"><div class="small ">';
                
                let order_subtotal = 0;
                let total_discount = 0;
                let total_tax_amount = 0;
                let tip_amount = parseFloat(val.tip_amount || 0);
                let deliveryCharge = parseFloat(val.deliveryCharge || 0);
                let platformFee = parseFloat(val.platformFee || 0);
                let packagingCharge = val.packagingChargeEnable ? parseFloat(val.vendor.packagingCharge || 0) : 0;

                //  Calculate subtotal and product extras
                for (let i = 0; i < val.products.length; i++) {
                    let product = val.products[i];
                    let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                    let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                    order_subtotal += itemGross;
                }

                // Total discounts
                let order_discount = parseFloat(val.discount || 0);
                let special_discount = parseFloat(val.specialDiscount?.special_discount || 0);
                    total_discount = order_discount + special_discount;

                 // Calculate item-level taxes (if product-level)
                if (val.taxScope === "product") {
                    let adminTaxes = taxesByScope['product'] || [];
                    let itemSubtotal = order_subtotal;
                    val.products.forEach(product => {
                        let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                        let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                        let itemDiscount = (itemSubtotal > 0) ? (itemGross / itemSubtotal) * total_discount : 0;
                        let itemTaxable = Math.max(0, itemGross - itemDiscount);
                        let itemTaxes = product.taxSetting || [];
                        itemTaxes.forEach(tax => {
                            let match = adminTaxes.find(t => t.id === tax.id);
                            if (tax.enable && match) {
                                let taxAmount = 0;
                                if (tax.type === "percentage") {
                                    taxAmount = (tax.tax / 100) * itemTaxable;
                                } else {
                                    taxAmount = tax.tax * product.quantity;
                                }
                                total_tax_amount += parseFloat(taxAmount);
                            }
                        });
                    });
                } 

                // Order-level taxes (if order-level)
                if (val.taxScope === "order") {
                    let orderTaxable = Math.max(0, order_subtotal - total_discount);
                    (val.taxSetting || []).forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * orderTaxable;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                }

                // Delivery, packaging, platform taxes
                let extraCharges = [
                    {amount: deliveryCharge, taxes: val.driverDeliveryTax || []},
                    {amount: packagingCharge, taxes: val.packagingTax || []},
                    {amount: platformFee, taxes: val.platformTax || []},
                ];

                extraCharges.forEach(scope => {
                    scope.taxes?.forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * scope.amount;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                        }
                    });
                });

                //Final subtotal after discounts
                order_subtotal = order_subtotal - total_discount;

                // Final total
                let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;
                
                 if (currencyAtRight) {
                        order_total_val = parseFloat(order_total).toFixed(decimal_degits) + '' + currentCurrency;
                    } else {
                        order_total_val = currentCurrency + '' + parseFloat(order_total).toFixed(decimal_degits);
                    }

                for (let i = 0; i < val.products.length; i++) {
                    var productExtras = 0;
                    if (val.products[i].hasOwnProperty('extras_price') && val.products[i].hasOwnProperty(
                            'extras')) {
                        if (val.products[i].extras_price) {
                            productExtras = val.products[i].extras_price;
                        }
                    }
                    var extras = '';
                    if (val.products[i].hasOwnProperty('extras') && val.products[i].extras != '') {
                        extras = val.products[i].extras;
                    }
                    var size = '';
                    if (val.products[i].hasOwnProperty('size') && val.products[i].size != '') {
                        size = val.products[i].size;
                    }
                    html = html + '<p class="text- font-weight-bold mb-0">' + val.products[i]['name'] + ' x ' +
                        val.products[i]['quantity'] + '</p>';
                    if (val.products[i]['variant_info']) {
                        html = html + '<div class="variant-info">';
                        html = html + '<ul>';
                        $.each(val.products[i]['variant_info']['variant_options'], function(label, value) {
                            html = html + '<li class="variant"><span class="label">' + label +
                                '</span><span class="value">' + value + '</span></li>';
                        });
                        html = html + '</ul>';
                        html = html + '</div>';
                    }
                    
                    html = html + '<div class="order_' + String(order_id) + '">';
                    html = html + '<input type="hidden" class="category_id" value="' + String(val.products[i][
                        'category_id'
                    ]) + '">';
                    html = html + '<input type="hidden" class="product_id" value="' + String(val.products[i][
                        'id'
                    ]) + '">';
                    html = html + '<input type="hidden" class="name" value="' + String(val.products[i][
                        'name'
                    ]) + '">';
                    html = html + '<input type="hidden" class="image" value="' + String(val.products[i][
                        'photo'
                    ]) + '">';
                    html = html + '<input type="hidden" class="price" value="' + parseFloat(val.products[i][
                        'price'
                    ]) + '">';
                    html = html + '<input type="hidden" class="quantity" value="' + parseFloat(val.products[i][
                        'quantity'
                    ]) + '">';
                    html = html + '<input type="hidden" class="extra_price" value="' + parseFloat(
                        productExtras) + '">';
                    html = html + '<input type="hidden" class="item_price" value="' + parseFloat(val.products[i]
                        ['price']) + '">';
                    html = html + '<input type="hidden" class="extra" value="' + extras + '">';
                    html = html + '<input type="hidden" class="size" value="' + size + '">';
                    html = html + '<input type="hidden" class="taxSetting" value=\'' + JSON.stringify(val.products[i].taxSetting) + '\'>';

                    html = html + '</div>';
                }
                
                html = html + '<input type="hidden" class="restid_' + String(order_id) + '" value="' + val
                    .vendor.id + '">';
                html = html + '<input type="hidden" class="resttitle_' + String(order_id) + '" value="' + val
                    .vendor.title + '">';
                html = html + '<input type="hidden" class="restlocation_' + String(order_id) + '" value="' + val
                    .vendor.location + '">';
                html = html + '<input type="hidden" class="restlatitude_' + String(order_id) + '" value="' + val
                    .vendor.latitude + '">';
                html = html + '<input type="hidden" class="restlongitude_' + String(order_id) + '" value="' +
                    val
                    .vendor.longitude + '">';
                html = html + '<input type="hidden" class="restphoto_' + String(order_id) + '" value="' + val
                    .vendor.photo + '">';
                html = html + '<input type="hidden" class="deliveryCharge_' + String(order_id) + '" value="' +
                    deliveryCharge + '">';
                html = html +
                    '</div><div class="text-muted m-0 ml-auto mr-3 small">Total a pagar<br><span class="text-dark font-weight-bold">' +
                    order_total_val +
                    '</span></div><div class="text-right">';
                if (showReorder && !inValidVendors.has(val.vendorID)) {
                    html +=
                        ' <a href="javascript:void(0);" class="btn btn-primary px-3 reorder-add-to-cart mr-2" data-id="' +
                        String(order_id) + '">Volver a pedir</a>';
                }
                html = html + '<a href="' + view_contact +
                    '" class="btn btn-outline-primary px-3">{{trans("lang.help")}}</a> </div></div></div></div></div></div>';
            }
        }
        jQuery("#data-table_processing").hide();
        return html;
    }

</script>

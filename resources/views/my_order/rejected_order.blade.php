@include('layouts.app')
@include('layouts.header')
<div class="d-none">
    <div class="bg-primary p-3 d-flex align-items-center">
        <a class="toggle togglew toggle-2" href="#"><span></span></a>
        <h4 class="font-weight-bold m-0 text-white">{{ trans('lang.my_orders') }}</h4>
    </div>
</div>
<section class="py-4 siddhi-main-body">
    <div class="container">
        <div class="row">
            <div class="col-md-12 top-nav mb-3">
                <ul class="nav nav-tabsa custom-tabsa border-0 bg-white rounded overflow-hidden shadow-sm p-2 c-t-order" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link border-0 text-dark py-3 " href="{{ url('my_order') }}?activeTab=completed"> <i class="feather-check mr-2 text-success mb-0"></i>
                            {{ trans('lang.completed') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" href="{{ url('my_order') }}?activeTab=progress"> <i class="feather-clock mr-2 text-warning mb-0"></i>
                            {{ trans('lang.on_progress') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                         <a class="nav-link border-0 text-dark py-3 active" href="{{ url('my_order') }}?activeTab=rejected">
                            <i class="feather-slash mr-2 text-danger mb-0"></i> {{ trans('lang.rejected') }}</a>
                    </li>
                    <li class="nav-item border-top" role="presentation">
                        <a class="nav-link border-0 text-dark py-3" href="{{ url('my_order') }}?activeTab=canceled"> <i class="feather-x-circle mr-2 text-danger mb-0"></i>
                            {{ trans('lang.canceled') }}</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-12">
                <section class="bg-white siddhi-main-body rounded shadow-sm overflow-hidden">
                    <div class="container p-0">
                        <div class="p-3 border-bottom gendetail-row">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="card p-3">
                                        <h3>{{ trans('lang.general_details') }}</h3>
                                        <div class="form-group widt-100 gendetail-col">
                                            <label class="control-label"><strong>{{ trans('lang.date_created') }}
                                                    : </strong><span id="order-date"></span></label>
                                        </div>
                                        <div class="form-group widt-100 gendetail-col">
                                            <label class="control-label"><strong>{{ trans('lang.order_number') }}
                                                    : </strong><span id="order-number"></span></label>
                                        </div>
                                        <div class="form-group widt-100 gendetail-col">
                                            <label class="control-label"><strong>{{ trans('lang.status') }}
                                                    : </strong><span id="order-status"></span></label>
                                        </div>
                                        <div class="form-group widt-100 gendetail-col">
                                            <label class="control-label"><strong>{{ trans('lang.order_type') }}:
                                                </strong><span id="order-type"></span></label>
                                        </div>
                                        <div class="form-group widt-100 gendetail-col schedule_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card p-3">
                                        <h3>{{ trans('lang.billing_details') }}</h3>
                                        <div class="form-group widt-100 gendetail-col">
                                            <div class="bill-address">
                                                <p><strong>{{ trans('lang.name') }} : </strong><span id="billing_name"></span></p>
                                                <p><strong>{{ trans('lang.address') }} : </strong><span id="billing_line1"></span><br>
                                                    <span id="billing_line2"></span><br>
                                                    <span id="billing_country"></span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="clear-both ml-auto addreview-btn">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-bottom order-secdetail">
                            <div class="row">
                                <div class="col-6">
                                    <div class=" order-deta-btm-right">
                                        <div class="resturant-detail">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-header-title">{{ trans('lang.restaurant') }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <a href="#" class="row redirecttopage" id="resturant-view">
                                                        <div class="col-4">
                                                            <img src="" class="resturant-img rounded-circle" alt="restaurant" width="70px" height="70px">
                                                        </div>
                                                        <div class="col-8">
                                                            <h4 class="restaurant-title"></h4>
                                                        </div>
                                                    </a>
                                                    <h5 class="contact-info">{{ trans('lang.contact_info') }}:</h5>
                                                    <p><strong>{{ trans('lang.phone') }}:</strong>
                                                        <span id="restaurant_phone"></span>
                                                    </p>
                                                    <p><strong>{{ trans('lang.address') }}:</strong>
                                                        <span id="restaurant_address"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class=" order-deta-btm-right">
                                        <div class="resturant-detail">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-header-title">{{ trans('lang.driver') }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <a href="#" class="row redirecttopage" id="resturant-view">
                                                        <div class="col-4">
                                                            <img src="{{ asset('img/food_delivery.png' )}}" class="driver-img rounded-circle" alt="driver" width="70px" height="70px">
                                                        </div>
                                                        <div class="col-8">
                                                            <h4 class="driver-name-title"></h4>
                                                        </div>
                                                    </a>
                                                    <h5 class="contact-info">{{ trans('lang.contact_info') }}:</h5>
                                                    <p><strong>{{ trans('lang.phone') }}:</strong>
                                                        <span id="driver_phone"></span>
                                                    </p>
                                                    <p><strong>{{ trans('lang.car_number') }}:</strong>
                                                        <span id="driver_car_number"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="order-note-box" style="display: none;">
                            <div class="col-lg-12">
                                <div class="py-3 border-bottom">
                                    <h6 class="font-weight-bold">{{ trans('lang.order_notes') }}</h6>
                                    <div id="order-note" class="order-note"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-3 border-bottom">
                                    <h6 class="font-weight-bold">{{ trans('lang.order_items') }}</h6>
                                    <div id="order-items"></div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.order_subtotal') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="order-subtotal"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.order_discount') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1 text-red" id="order-discount" style="color:red"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.special') }}
                                            {{ trans('lang.offer') }} {{ trans('lang.discount') }} <span id="special_offer_text"></span></h6>
                                        <h6 class="font-weight-bold ml-auto mb-1 " id="special_offer_discount" style="color:red"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom order_shopping_div">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.deliveryCharge') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="order-shipping"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom used_coupon_code_div" style="display:none">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.used_coupon') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="used_coupon_code"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom order_tip_div">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.tip_amount') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="order-tip-amount"></h6>
                                    </div>
                                </div>
                                <div class="p-3 border-bottom order_tax_div">
                                    <div id="order-tax"></div>
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.order_tax') }}</h6>
                                        <h6 class="font-weight-bold mb-1 ml-auto" id="total_tax_amount"></h6>
                                    </div>
                                </div>
                                <div class="p-3 bg-white">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-1">{{ trans('lang.order_total') }}</h6>
                                        <h6 class="font-weight-bold ml-auto mb-1" id="order-total"></h6>
                                    </div>

                                    <div class="cashback-offer d-none text-success font-weight-bold mt-3"></div>

                                    <p class="m-0 small text-muted">
                                        <br>
                                        {{ trans('lang.thank_you_for_order') }}.
                                    </p>
                                </div>
                                <div class="p-3 border-bottom">
                                    <p class="font-weight-bold small mb-1">
                                        {{ trans('lang.courier') }}
                                    </p>
                                    <img alt="#" src="https://dadosgo.com/img/logo_header.png" class="img-fluid sc-siddhi-logo mr-2"><span class="small text-primary font-weight-bold">{{ trans('lang.grocery_courier') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="reviewModel" tabindex="-1" role="dialog" aria-labelledby="reviewModelLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ trans('lang.review_order') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="error_top" style="display:none"></div>
                                        <div class="form-group row" id="default_review">
                                            <label class="col-sm-2 col-form-label">{{ trans('lang.rate') }}</label>
                                            <div class="col-sm-10">
                                                <div class="rating-wrap d-flex align-items-center mt-4 mb-3" id="#">
                                                    <fieldset class="rating rate_this">
                                                        <input type="radio" class="main_rating" name="rating" id="star5" value="5" /><label for="star5" class="full"></label>
                                                        <input type="radio" class="main_rating" name="rating" id="star4.5" value="4.5" /><label for="star4.5" class="half"></label>
                                                        <input type="radio" class="main_rating" name="rating" id="star4" value="4" /><label for="star4" class="full"></label>
                                                        <input type="radio" class="main_rating" name="rating" id="star3.5" value="3.5" /><label for="star3.5" class="half"></label>
                                                        <input type="radio" class="main_rating" name="rating" id="star3" value="3" /><label for="star3" class="full"></label>
                                                        <input type="radio" class="main_rating" name="rating" id="star2.5" value="2.5" /><label for="star2.5" class="half"></label>
                                                        <input type="radio" class="main_rating" name="rating" id="star2" value="2" /><label for="star2" class="full"></label>
                                                        <input type="radio" class="main_rating" name="rating" id="star1.5" value="1.5" /><label for="star1.5" class="half"></label>
                                                        <input type="radio" class="main_rating" name="rating" id="star1" value="1" /><label for="star1" class="full"></label>
                                                        <input type="radio" class="main_rating" name="rating" id="star0.5" value="0.5" /><label for="star0.5" class="half"></label>
                                                        <input type="hidden" value="0" id="rating-value" />
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row" id="attribute_review"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">{{ trans('lang.comment') }}</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control review_comment" id="review_comment" name="review_comment" placeholder="Comentario de la reseña" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">{{ trans('lang.image') }}</label>
                                            <div class="col-sm-10">
                                                <input type="file" id="review_image">
                                                <div id="uploding_image"></div>
                                                <div id="uploded_image">
                                                    <ul></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary add_review_btn" data-parent="modal-body">{{ trans('lang.add_review') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
</section>
</div>
</div>
</div>
</section>
@include('layouts.footer')
@include('layouts.nav')

<script type="text/javascript">

    
    var orderId = "<?php echo $_GET['id']; ?>";
    var append_categories = '';
    var completedorsersref = database.collection('restaurant_orders').where('id', "==", orderId);
    var vendor = database.collection('vendors');
    var vendor_products = database.collection('vendor_products');
    var reviewOrderImage = [];
    var orderVendorId = '';
    var reviewUserName = '';
    var decimal_degits = 0;
    var reviewUserProfile = '';
    var inValidVendors = new Set();

    let userCountry = getCookie('userCountryName');
    const scopes = ['delivery', 'order', 'packaging', 'platform', 'product'];
    const taxesByScope = {};
    database.collection('tax').where('country', '==', userCountry).where('enable', '==', true).where('scope', 'in', scopes).get().then(snapshot => {
        snapshot.forEach(doc => {
            const tax = doc.data();
            (taxesByScope[tax.scope] ??= []).push(tax);
        });
    });

    $(document).ready(async function() {
        // Retrieve all invalid vendors
        await checkVendors().then(expiredStores => {
            inValidVendors = expiredStores;
        });
        getOrderDetails();
        jQuery(document).on("click", ".mdi-delete", function() {
            var url = jQuery(this).data('url');
            if (url) {
                firebase.storage().refFromURL(url).delete();
                jQuery(this).parent().remove();
                reviewOrderImage = $.grep(reviewOrderImage, function(value) {
                    return value != url;
                });
            }
        });
        $(document).on('shown.bs.modal', '#reviewModel', function() {
            var pid = $(this).attr('data-pid');
            var cid = $(this).attr('data-cid');
            if (pid && cid) {
                database.collection('vendor_categories').doc(cid).get().then(async function(
                    snapshots) {
                    var catData = snapshots.data();
                    database.collection('foods_review').where('orderid', '==', orderId)
                        .where('productId', '==', pid).get().then((docSnapshot) => {
                            var itemReviewDoc = '';
                            if (docSnapshot.size) {
                                $("#reviewModel").find('.add_review_btn').text(
                                    '{{trans("lang.update_review")}}');
                                itemReviewDoc = docSnapshot.docs[0].data();
                                $("#default_review").find('.rating').attr(
                                    'data-rating', itemReviewDoc.rating);
                                $("#reviewModel").find('#review_comment').val(
                                    itemReviewDoc.comment);
                                if (itemReviewDoc.photos.length > 0) {
                                    $.each(itemReviewDoc.photos, function(key,
                                        url) {
                                        $("#uploded_image ul").append(
                                            '<li><img onerror="this.onerror=null;this.src=\'' +
                                            place_image + '\'" src="' +
                                            url +
                                            '" width="100"><span class="mdi mdi-delete" onerror="this.onerror=null; this.remove();" data-url="' +
                                            url + '">X</span></li>');
                                        reviewOrderImage.push(url);
                                    });
                                }
                            } else {
                                $("#reviewModel").find('.add_review_btn').text(
                                    '{{trans("lang.add_review")}}');
                            }
                            if (catData.review_attributes) {
                                database.collection('review_attributes').where('id',
                                    "in", catData.review_attributes).get().then(
                                    async function(docsSnap) {
                                        var html = '';
                                        var count = 0;
                                        html +=
                                            '<div class="review-ratings">';
                                        html += '<ul class="rating-block">';
                                        docsSnap.forEach((doc) => {
                                            var at_id = doc.data()
                                                .id;
                                            var at_title = doc
                                                .data().title;
                                            if (itemReviewDoc) {
                                                var at_value =
                                                    itemReviewDoc
                                                    .reviewAttributes[
                                                        at_id];
                                            } else {
                                                var at_value = 0;
                                            }
                                            html +=
                                                '<li data-id="' +
                                                at_id +
                                                '" data-rating="' +
                                                at_value +
                                                '" class="li_attr_rating" id="li_attr_rating_' +
                                                count + '">';
                                            html += '<label>' +
                                                at_title +
                                                '</label>';
                                            html +=
                                                '<div class="rating-wrap d-flex align-items-center mt-4 mb-3" id="#">';
                                            html +=
                                                '<fieldset class="attribute" id="attribute_' +
                                                count +
                                                '" data-rating="' +
                                                at_value + '">';
                                            var css_0_5 = css_1 =
                                                css_1_5 = css_2 =
                                                css_2_5 = css_3 =
                                                css_3_5 = css_4 =
                                                css_4_5 = css_5 =
                                                '';
                                            if (at_value == "0.5") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                            } else if (at_value ==
                                                "1") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                                css_1 =
                                                    'color: #eca700;';
                                            } else if (at_value ==
                                                "1.5") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                                css_1 =
                                                    'color: #eca700;';
                                                css_1_5 =
                                                    'color: #eca700;';
                                            } else if (at_value ==
                                                "2") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                                css_1 =
                                                    'color: #eca700;';
                                                css_1_5 =
                                                    'color: #eca700;';
                                                css_2 =
                                                    'color: #eca700;';
                                            } else if (at_value ==
                                                "2.5") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                                css_1 =
                                                    'color: #eca700;';
                                                css_1_5 =
                                                    'color: #eca700;';
                                                css_2 =
                                                    'color: #eca700;';
                                                css_2_5 =
                                                    'color: #eca700;';
                                            } else if (at_value ==
                                                "3") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                                css_1 =
                                                    'color: #eca700;';
                                                css_1_5 =
                                                    'color: #eca700;';
                                                css_2 =
                                                    'color: #eca700;';
                                                css_2_5 =
                                                    'color: #eca700;';
                                                css_3 =
                                                    'color: #eca700;';
                                            } else if (at_value ==
                                                "3.5") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                                css_1 =
                                                    'color: #eca700;';
                                                css_1_5 =
                                                    'color: #eca700;';
                                                css_2 =
                                                    'color: #eca700;';
                                                css_2_5 =
                                                    'color: #eca700;';
                                                css_3 =
                                                    'color: #eca700;';
                                                css_3_5 =
                                                    'color: #eca700;';
                                            } else if (at_value ==
                                                "4") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                                css_1 =
                                                    'color: #eca700;';
                                                css_1_5 =
                                                    'color: #eca700;';
                                                css_2 =
                                                    'color: #eca700;';
                                                css_2_5 =
                                                    'color: #eca700;';
                                                css_3 =
                                                    'color: #eca700;';
                                                css_3_5 =
                                                    'color: #eca700;';
                                                css_4 =
                                                    'color: #eca700;';
                                            } else if (at_value ==
                                                "4.5") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                                css_1 =
                                                    'color: #eca700;';
                                                css_1_5 =
                                                    'color: #eca700;';
                                                css_2 =
                                                    'color: #eca700;';
                                                css_2_5 =
                                                    'color: #eca700;';
                                                css_3 =
                                                    'color: #eca700;';
                                                css_3_5 =
                                                    'color: #eca700;';
                                                css_4 =
                                                    'color: #eca700;';
                                                css_4_5 =
                                                    'color: #eca700;';
                                            } else if (at_value ==
                                                "5") {
                                                css_0_5 =
                                                    'color: #eca700;';
                                                css_1 =
                                                    'color: #eca700;';
                                                css_1_5 =
                                                    'color: #eca700;';
                                                css_2 =
                                                    'color: #eca700;';
                                                css_2_5 =
                                                    'color: #eca700;';
                                                css_3 =
                                                    'color: #eca700;';
                                                css_3_5 =
                                                    'color: #eca700;';
                                                css_4 =
                                                    'color: #eca700;';
                                                css_4_5 =
                                                    'color: #eca700;';
                                                css_5 =
                                                    'color: #eca700;';
                                            }
                                            html +=
                                                '<input type="radio" class="rating_attribute" id="star5_' +
                                                count +
                                                '" value="5"/><label\n' +
                                                '                                    for="star5_' +
                                                count + '"\n' +
                                                '                                        class="full hover_class" style="' +
                                                css_5 +
                                                '" ></label>\n' +
                                                '                                        <input type="radio" class="rating_attribute" id="star4.5_' +
                                                count + '"\n' +
                                                '                                    value="4.5"/><label\n' +
                                                '                                    for="star4.5_' +
                                                count +
                                                '" class="half hover_class" style="' +
                                                css_4_5 +
                                                '"></label>\n' +
                                                '                                        <input type="radio" class="rating_attribute" id="star4_' +
                                                count +
                                                '" value="4"/><label\n' +
                                                '                                    for="star4_' +
                                                count + '"\n' +
                                                '                                        class="full hover_class" style="' +
                                                css_4 +
                                                '"></label>\n' +
                                                '                                        <input type="radio" class="rating_attribute" id="star3.5_' +
                                                count + '"\n' +
                                                '                                    value="3.5"/><label\n' +
                                                '                                    for="star3.5_' +
                                                count +
                                                '" class="half hover_class" style="' +
                                                css_3_5 +
                                                '"></label>\n' +
                                                '                                        <input type="radio" class="rating_attribute" id="star3_' +
                                                count +
                                                '" value="3"/><label\n' +
                                                '                                    for="star3_' +
                                                count + '"\n' +
                                                '                                        class="full hover_class" style="' +
                                                css_3 +
                                                '"></label>\n' +
                                                '                                        <input type="radio" class="rating_attribute" id="star2.5_' +
                                                count + '"\n' +
                                                '                                    value="2.5"/><label\n' +
                                                '                                    for="star2.5_' +
                                                count +
                                                '" class="half hover_class" style="' +
                                                css_2_5 +
                                                '"></label>\n' +
                                                '                                        <input type="radio" class="rating_attribute" id="star2_' +
                                                count +
                                                '" value="2"/><label\n' +
                                                '                                    for="star2_' +
                                                count + '"\n' +
                                                '                                        class="full hover_class" style="' +
                                                css_2 +
                                                '"></label>\n' +
                                                '                                        <input type="radio" class="rating_attribute" id="star1.5_' +
                                                count + '"\n' +
                                                '                                    value="1.5"/><label\n' +
                                                '                                    for="star1.5_' +
                                                count +
                                                '" class="half hover_class" style="' +
                                                css_1_5 +
                                                '"></label>\n' +
                                                '                                        <input type="radio" class="rating_attribute" id="star1_' +
                                                count +
                                                '" value="1"/><label\n' +
                                                '                                    for="star1_' +
                                                count + '"\n' +
                                                '                                        class="full hover_class" style="' +
                                                css_1 +
                                                '"></label>\n' +
                                                '                                        <input type="radio" class="rating_attribute" id="star0.5_' +
                                                count + '"\n' +
                                                '                                    value="0.5"><label\n' +
                                                '                                    for="star0.5_' +
                                                count +
                                                '" class="half hover_class" style="' +
                                                css_0_5 +
                                                '"></label>\n' +
                                                '                                        <input type="hidden" value="' +
                                                at_value +
                                                '" id="rating-attribute-value"/>';
                                            html += '</fieldset>';
                                            html += '</div>';
                                            html += '</li>';
                                            count++;
                                        });
                                        html += '</ul>';
                                        html += '</div>';
                                        $("#attribute_review").html(html);
                                    });
                            }
                        });
                });
            }
        });
        $(document).on('click', '.item_review_btn', function() {
            jQuery("#reviewModel").attr('data-pid', $(this).data('pid')).attr('data-cid', $(this)
                .data('cid')).modal("show");
        });
        $(document).on('hide.bs.modal', '#reviewModel', function() {
            $(this).removeAttr('data-pid').removeAttr('data-cid');
            $(this).find("#attribute_review").empty();
            $(this).find('#review_comment').val('');
            $(this).find('#uploded_image ul').empty();
        });
        var star = document.querySelectorAll('input[name="rating"]');
        for (var i = 0; i < star.length; i++) {
            star[i].addEventListener('click', function() {
                var rating = this.value;
                $('#rating-value').val(rating);
                $("#default_review").find('.rating').attr('data-rating', rating);
            })
        }
        $(document).on('click', '.rating_attribute', function() {
            var rating = this.value;
            var id = '#' + $(this).closest('fieldset').prop('id');
            $(id).attr('data-rating', rating);
            $(id).find('#rating-attribute-value').val(rating);
            $(id).find('input ~ label.half[for="star0.5_' + spli_id + '"]').attr('style',
                'color: #ccc');
            $(id).find('input ~ label.full[for="star1_' + spli_id + '"]').attr('style',
                'color: #ccc');
            $(id).find('input ~ label.half[for="star1.5_' + spli_id + '"]').attr('style',
                'color: #ccc');
            $(id).find('input ~ label.full[for="star2_' + spli_id + '"]').attr('style',
                'color: #ccc');
            $(id).find('input ~ label.half[for="star2.5_' + spli_id + '"]').attr('style',
                'color: #ccc');
            $(id).find('input ~ label.full[for="star3_' + spli_id + '"]').attr('style',
                'color: #ccc');
            $(id).find('input ~ label.half[for="star3.5_' + spli_id + '"]').attr('style',
                'color: #ccc');
            $(id).find('input ~ label.full[for="star4_' + spli_id + '"]').attr('style',
                'color: #ccc');
            $(id).find('input ~ label.half[for="star4.5_' + spli_id + '"]').attr('style',
                'color: #ccc');
            $(id).find('input ~ label.full[for="star5_' + spli_id + '"]').attr('style',
                'color: #ccc');
            var spli_id = id.split('_')[1];
            $('#li_attr_rating_' + spli_id).attr('data-rating', rating);
            var css_0_5 = css_1 = css_1_5 = css_2 = css_2_5 = css_3 = css_3_5 = css_4 = css_4_5 =
                css_5 = '';
            if (rating == "0.5") {
                css_0_5 = 'color: #eca700';
            } else if (rating == "1") {
                css_0_5 = 'color: #eca700';
                css_1 = 'color: #eca700';
            } else if (rating == "1.5") {
                css_0_5 = 'color: #eca700';
                css_1 = 'color: #eca700';
                css_1_5 = 'color: #eca700';
            } else if (rating == "2") {
                css_0_5 = 'color: #eca700';
                css_1 = 'color: #eca700';
                css_1_5 = 'color: #eca700';
                css_2 = 'color: #eca700';
            } else if (rating == "2.5") {
                css_0_5 = 'color: #eca700';
                css_1 = 'color: #eca700';
                css_1_5 = 'color: #eca700';
                css_2 = 'color: #eca700';
                css_2_5 = 'color: #eca700';
            } else if (rating == "3") {
                css_0_5 = 'color: #eca700';
                css_1 = 'color: #eca700';
                css_1_5 = 'color: #eca700';
                css_2 = 'color: #eca700';
                css_2_5 = 'color: #eca700';
                css_3 = 'color: #eca700';
            } else if (rating == "3.5") {
                css_0_5 = 'color: #eca700';
                css_1 = 'color: #eca700';
                css_1_5 = 'color: #eca700';
                css_2 = 'color: #eca700';
                css_2_5 = 'color: #eca700';
                css_3 = 'color: #eca700';
                css_3_5 = 'color: #eca700';
            } else if (rating == "4") {
                css_0_5 = 'color: #eca700';
                css_1 = 'color: #eca700';
                css_1_5 = 'color: #eca700';
                css_2 = 'color: #eca700';
                css_2_5 = 'color: #eca700';
                css_3 = 'color: #eca700';
                css_3_5 = 'color: #eca700';
                css_4 = 'color: #eca700';
            } else if (rating == "4.5") {
                css_0_5 = 'color: #eca700';
                css_1 = 'color: #eca700';
                css_1_5 = 'color: #eca700';
                css_2 = 'color: #eca700';
                css_2_5 = 'color: #eca700';
                css_3 = 'color: #eca700';
                css_3_5 = 'color: #eca700';
                css_4 = 'color: #eca700';
                css_4_5 = 'color: #eca700';
            } else if (rating == "5") {
                css_0_5 = 'color: #eca700';
                css_1 = 'color: #eca700';
                css_1_5 = 'color: #eca700';
                css_2 = 'color: #eca700';
                css_2_5 = 'color: #eca700';
                css_3 = 'color: #eca700';
                css_3_5 = 'color: #eca700';
                css_4 = 'color: #eca700';
                css_4_5 = 'color: #eca700';
                css_5 = 'color: #eca700';
            }
            $(id).find('input ~ label.half[for="star0.5_' + spli_id + '"]').attr('style', css_0_5);
            $(id).find('input ~ label.full[for="star1_' + spli_id + '"]').attr('style', css_1);
            $(id).find('input ~ label.half[for="star1.5_' + spli_id + '"]').attr('style', css_1_5);
            $(id).find('input ~ label.full[for="star2_' + spli_id + '"]').attr('style', css_2);
            $(id).find('input ~ label.half[for="star2.5_' + spli_id + '"]').attr('style', css_2_5);
            $(id).find('input ~ label.full[for="star3_' + spli_id + '"]').attr('style', css_3);
            $(id).find('input ~ label.half[for="star3.5_' + spli_id + '"]').attr('style', css_3_5);
            $(id).find('input ~ label.full[for="star4_' + spli_id + '"]').attr('style', css_4);
            $(id).find('input ~ label.half[for="star4.5_' + spli_id + '"]').attr('style', css_4_5);
            $(id).find('input ~ label.full[for="star5_' + spli_id + '"]').attr('style', css_5);
        });
        $(".add_review_btn").click(function() {
            $(this).css({
                'opacity': 0.5,
                'cursor': 'default'
            });
            var pclass = $(this).data('parent');
            var default_review = $('.' + pclass).find('#default_review');
            var attribute_review = $('.' + pclass).find('#attribute_review');
            var rating = default_review.find('.rating').attr('data-rating');
            rating = parseFloat(rating);
            var reviewAttributes = {};
            var reviewAttributes2 = {};
            if (attribute_review.children().length > 0) {
                attribute_review.find('.rating-block > li').each(function(li) {
                    var id = $(this).attr('data-id');
                    var value = $(this).attr('data-rating');
                    reviewAttributes[id] = parseFloat(value);
                    reviewAttributes2[id] = {
                        'reviewsCount': 1,
                        'reviewsSum': value
                    };
                });
            }
            var comment = $(".review_comment").val();
            var photos = reviewOrderImage;
            var orderid = "<?php echo $_GET['id']; ?>";
            var CustomerId = user_uuid;
            var VendorId = orderVendorId;
            var uname = reviewUserName;
            var reviewId = database.collection("tmp").doc().id;
            var userProfile = reviewUserProfile;
            var productId = $("#reviewModel").attr('data-pid');
            if (isNaN(rating)) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.please_select_rating')}}</p>");
            } else {
                database.collection('foods_review').where('orderid', '==', orderid).where(
                    'productId', '==', productId).get().then((docSnapshot) => {
                    if (docSnapshot.size) {
                        var itemReviewDoc = docSnapshot.docs[0].data();
                        var timeStamp = firebase.firestore.FieldValue.serverTimestamp();
                        database.collection('foods_review').doc(itemReviewDoc.Id).update({
                            'comment': comment,
                            'photos': photos,
                            'rating': rating,
                            'reviewAttributes': reviewAttributes,
                            'createdAt': timeStamp
                        });
                        vendor_data = database.collection('vendors').where('id', "==",
                            VendorId);
                        vendor_data.get().then(async function(snapshots) {
                            if (snapshots.docs[0]) {
                                vendor = snapshots.docs[0].data();
                                var reviewsCount = 0;
                                var reviewsSum = 0;
                                if (vendor.reviewsCount != undefined && vendor
                                    .reviewsCount != '') {
                                    reviewsCount = vendor.reviewsCount;
                                    reviewsCount = reviewsCount - 1;
                                }
                                if (vendor.reviewsSum != undefined && vendor
                                    .reviewsSum != '') {
                                    reviewsSum = vendor.reviewsSum;
                                    reviewsSum = reviewsSum - itemReviewDoc.rating;

                                }
                                reviewsCount = Number(reviewsCount) + 1;
                                reviewsSum = Number(reviewsSum) + Number(rating);
                                database.collection('vendors').doc(VendorId)
                                    .update({
                                        'reviewsCount': reviewsCount,
                                        'reviewsSum': reviewsSum
                                    });
                            }
                        });
                        product_data = vendor_products.where('id', "==", productId);
                        product_data.get().then(async function(snapshots) {
                            if (snapshots.docs[0]) {
                                product = snapshots.docs[0].data();
                                var reviewsCount = 0;
                                var reviewsSum = 0;
                                if (product.reviewsCount != undefined && product
                                    .reviewsCount != '') {
                                    reviewsCount = product.reviewsCount;
                                    reviewsCount = Number(reviewsCount) - 1;
                                }
                                if (product.reviewsSum != undefined && product
                                    .reviewsSum != '') {
                                    reviewsSum = product.reviewsSum;
                                    if (itemReviewDoc.rating != undefined && itemReviewDoc.rating != null && itemReviewDoc.rating != "") {
                                        reviewsSum = Number(reviewsSum) - Number(itemReviewDoc.rating);
                                    }
                                }
                                reviewsCount = Number(reviewsCount) + 1;
                                reviewsSum = Number(reviewsSum) + Number(rating);
                                if (product.reviewAttributes != undefined &&
                                    product.reviewAttributes != '') {
                                    var resetReviewAtrributes = {};
                                    productreviewAttributes = product
                                        .reviewAttributes;
                                    $.each(productreviewAttributes, function(
                                        key, data) {
                                        var reviewsCount = Number(data
                                            .reviewsCount) - 1;
                                        var reviewsSum = Number(data
                                            .reviewsSum) - Number(itemReviewDoc
                                            .reviewAttributes[key]);
                                        reviewsCount = Number(reviewsCount) + 1
                                        reviewsSum = Number(reviewsSum) +
                                            Number(reviewAttributes[key]);
                                        resetReviewAtrributes[key] = {
                                            'reviewsCount': reviewsCount,
                                            'reviewsSum': reviewsSum
                                        };
                                    });
                                    reviewAttributes = resetReviewAtrributes;
                                } else {
                                    reviewAttributes = reviewAttributes2;
                                }
                                if ($.isEmptyObject(reviewAttributes)) {
                                    reviewAttributes = null;
                                }
                                database.collection('vendor_products').doc(
                                    productId).update({
                                    'reviewsCount': reviewsCount,
                                    'reviewsSum': reviewsSum,
                                    'reviewAttributes': reviewAttributes
                                }).then(function(result) {
                                    location.reload();
                                });
                            }
                        });
                    } else {
                        var timeStamp = firebase.firestore.FieldValue.serverTimestamp();
                        database.collection('foods_review').doc(reviewId).set({
                            'CustomerId': CustomerId,
                            'Id': reviewId,
                            'VendorId': VendorId,
                            'comment': comment,
                            'orderid': orderid,
                            'photos': photos,
                            'productId': productId,
                            'rating': rating,
                            "uname": uname,
                            'profile': userProfile,
                            'reviewAttributes': reviewAttributes,
                            'createdAt': timeStamp
                        }).then(function(result) {
                            vendor_data = database.collection('vendors').where('id',
                                "==", VendorId);
                            vendor_data.get().then(async function(snapshots) {
                                if (snapshots.docs[0]) {
                                    vendor = snapshots.docs[0].data();
                                    var reviewsCount = 0;
                                    var reviewsSum = 0;
                                    if (vendor.reviewsCount !=
                                        undefined && vendor
                                        .reviewsCount != '') {
                                        reviewsCount = vendor
                                            .reviewsCount;
                                    }
                                    if (vendor.reviewsSum !=
                                        undefined && vendor
                                        .reviewsSum != '') {
                                        reviewsSum = vendor.reviewsSum;
                                    }
                                    reviewsCount = reviewsCount + 1;
                                    reviewsSum = reviewsSum + rating;
                                    database.collection('vendors').doc(
                                        VendorId).update({
                                        'reviewsCount': reviewsCount,
                                        'reviewsSum': reviewsSum
                                    });
                                }
                            });
                            product_data = vendor_products.where('id', "==",
                                productId);
                            product_data.get().then(async function(snapshots) {
                                if (snapshots.docs[0]) {
                                    product = snapshots.docs[0].data();
                                    var reviewsCount = 0;
                                    var reviewsSum = 0;
                                    if (product.reviewsCount !=
                                        undefined && product
                                        .reviewsCount != '') {
                                        reviewsCount = product
                                            .reviewsCount;
                                    }
                                    if (product.reviewsSum !=
                                        undefined && product
                                        .reviewsSum != '') {
                                        reviewsSum = product.reviewsSum;
                                    }
                                    reviewsCount = Number(reviewsCount) + 1;
                                    reviewsSum = Number(reviewsSum) + Number(rating);
                                    if (product.reviewAttributes !=
                                        undefined && product
                                        .reviewAttributes != '') {
                                        var resetReviewAtrributes = {};
                                        productreviewAttributes =
                                            product.reviewAttributes;
                                        $.each(productreviewAttributes,
                                            function(key, data) {
                                                var reviewsCount =
                                                    Number(data
                                                        .reviewsCount) +
                                                    1
                                                var reviewsSum =
                                                    Number(data
                                                        .reviewsSum) +
                                                    Number(reviewAttributes[
                                                        key]);
                                                resetReviewAtrributes
                                                    [key] = {
                                                        'reviewsCount': reviewsCount,
                                                        'reviewsSum': reviewsSum
                                                    };
                                            });
                                        reviewAttributes =
                                            resetReviewAtrributes;
                                    } else {
                                        reviewAttributes =
                                            reviewAttributes2;
                                    }
                                    if ($.isEmptyObject(
                                            reviewAttributes)) {
                                        reviewAttributes = null;
                                    }
                                    database.collection(
                                        'vendor_products').doc(
                                        productId).update({
                                        'reviewsCount': reviewsCount,
                                        'reviewsSum': reviewsSum,
                                        'reviewAttributes': reviewAttributes
                                    }).then(function(result) {
                                        location.reload();
                                    });
                                }
                            });
                        });
                    }
                });
            }
        })
    });
    
    var place_image = '';
    var ref_place = database.collection('settings').doc("placeHolderImage");
    ref_place.get().then(async function(snapshots) {
        var placeHolderImage = snapshots.data();
        place_image = placeHolderImage.image;
    });
    
    var currentCurrency = '';
    var currencyAtRight = false;
    var currencyData = '';

    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function(snapshots) {
        currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });

    async function getOrderDetails() {
        completedorsersref.get().then(async function(completedorderSnapshots) {
            var orderDetails = completedorderSnapshots.docs[0].data();
            if (orderDetails.author.id != user_uuid) {
                window.location.href = '{{ route('login') }}';
            } else {
                var orderDate = orderDetails.createdAt.toDate().toDateString();
                var time = orderDetails.createdAt.toDate().toLocaleTimeString('en-US');
                $("#order-date").html(orderDate + ' ' + time);
                var billingAddressstring = '';
                var billingName = '';
                if (orderDetails.author.hasOwnProperty('firstName')) {
                    billingName = orderDetails.author.firstName;
                }
                if (orderDetails.author.hasOwnProperty('lastName')) {
                    billingName += orderDetails.author.lastName;
                }
                $("#billing_name").text(billingName);
                if (orderDetails.address.hasOwnProperty('address')) {
                    $("#billing_line1").text(orderDetails.address.address);
                }
                if (orderDetails.address.hasOwnProperty('locality')) {
                    billingAddressstring = billingAddressstring + orderDetails.address.locality;
                }
                if (orderDetails.address.hasOwnProperty('landmark') && orderDetails.address.landmark !=
                    null && orderDetails.address.landmark != '') {
                    billingAddressstring = billingAddressstring + " " + orderDetails.address.landmark;
                }
                $("#billing_line2").text(billingAddressstring);
                var scheduleTime = '';
                if (orderDetails.hasOwnProperty('scheduleTime') && orderDetails.scheduleTime != null &&
                    orderDetails.scheduleTime != '') {
                    scheduleTime = orderDetails.scheduleTime;
                    var scheduleDate = scheduleTime.toDate().toDateString();
                    var time = orderDetails.scheduleTime.toDate().toLocaleTimeString('en-US');
                    scheduleDateTime = scheduleDate + " " + time;
                    $('.schedule_date').append(
                        '<label class="col-12 control-label pl-0"><strong>{{ trans('lang.schedule_date_time') }}:</strong><span id=""> ' +
                        scheduleDateTime + '</span></label>')
                }
                $("#order-addreess").html(billingAddressstring);
                
                var order_items = order_status = '';
                
                let order_subtotal = 0;
                let total_discount = 0;
                let total_tax_amount = 0;
                let tip_amount = parseFloat(orderDetails.tip_amount || 0);
                let deliveryCharge = parseFloat(orderDetails.deliveryCharge || 0);
                let platformFee = parseFloat(orderDetails.platformFee || 0);
                let packagingCharge = orderDetails.packagingChargeEnable ? parseFloat(orderDetails.vendor.packagingCharge || 0) : 0;

                //  Calculate subtotal and product extras
                for (let i = 0; i < orderDetails.products.length; i++) {
                    let product = orderDetails.products[i];
                    let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                    let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                    order_subtotal += itemGross;
                }

                // Total discounts
                let order_discount = parseFloat(orderDetails.discount || 0);
                let special_discount = parseFloat(orderDetails.specialDiscount?.special_discount || 0);
                    total_discount = order_discount + special_discount;

                // Calculate item-level taxes (if product-level)
                if (orderDetails.taxScope === "product") {
                    let adminTaxes = taxesByScope['product'] || [];
                    let itemSubtotal = order_subtotal;
                    let itemCombinedTax = 0;
                    orderDetails.products.forEach(product => {
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
                                itemCombinedTax += parseFloat(taxAmount);
                            }
                        });
                    });
                    if(itemCombinedTax > 0){
                        taxBreakdownGrouped.item[''] = itemCombinedTax;
                    }
                } 

                // Order-level taxes (if order-level)
                if (orderDetails.taxScope === "order") {
                    let orderTaxable = Math.max(0, order_subtotal - total_discount);
                    let orderCombinedTax = 0;
                    (orderDetails.taxSetting || []).forEach(tax => {
                        if (tax.enable) {
                            let taxAmount = 0;
                            if (tax.type === "percentage") {
                                taxAmount = (tax.tax / 100) * orderTaxable;
                            } else {
                                taxAmount = tax.tax;
                            }
                            total_tax_amount += parseFloat(taxAmount);
                            orderCombinedTax += parseFloat(taxAmount);
                        }
                    });
                    if(orderCombinedTax > 0){
                        taxBreakdownGrouped.order[''] = orderCombinedTax;
                    }
                }

                // Delivery, packaging, platform taxes
                let extraCharges = [
                    {key: 'delivery', amount: deliveryCharge, taxes: orderDetails.driverDeliveryTax || []},
                    {key: 'packaging', amount: packagingCharge, taxes: orderDetails.packagingTax || []},
                    {key: 'platform', amount: platformFee, taxes: orderDetails.platformTax || []},
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
                            taxBreakdownGrouped[scope.key][tax.title] = (taxBreakdownGrouped[scope.key][tax.title] || 0) + parseFloat(taxAmount);
                        }
                    });
                });

                $("#total_tax_amount").text(formatCurrency(total_tax_amount,currencyData));
                if(total_tax_amount > 0){
                    $("#order-tax").after("<hr>");
                    renderTaxSection('item', 'Tax on Item Total');
                    renderTaxSection('order', 'Tax on Order Total');
                    renderTaxSection('delivery', 'Tax on Delivery Fee');
                    renderTaxSection('packaging', 'Tax on Packaging Fee');
                    renderTaxSection('platform', 'Tax on Platform Fee');
                }

                //Final subtotal after discounts
                order_subtotal_main = order_subtotal;
                order_subtotal = order_subtotal - total_discount;

                // Final total
                let order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;

                order_items += '<tr>';
                order_items += '<th></th>';
                order_items += '<th class="prod-name">{{trans("lang.item_name")}}</th>';
                order_items += '<th class="qunt">{{trans("lang.review")}}</th>';
                order_items += '<th class="qunt">{{trans("lang.quantity_plural")}}</th>';
                order_items += '<th class="qunt">{{trans("lang.extras")}}</th>';
                order_items += '<th class="price">{{trans("lang.price")}}</th>';
                order_items += '<th class="price text-right">{{trans("lang.total")}}</th>';
                order_items += '</tr>';
                for (let i = 0; i < orderDetails.products.length; i++) {
                    
                var extra_html = '';
                    var productPriceTotal = parseFloat(orderDetails.products[i]['price']) * parseFloat(orderDetails.products[i]['quantity']);
                    var productExtras = 0;
                    if (orderDetails.products[i].hasOwnProperty('extras_price') && orderDetails.products[i].hasOwnProperty('extras')) {
                        productPriceTotal += parseFloat(orderDetails.products[i].extras_price) * parseInt(orderDetails.products[i]['quantity']);
                        productExtras = parseFloat(orderDetails.products[i].extras_price) * parseInt(orderDetails.products[i]['quantity']);
                    }

                    products_price = "";
                    productPriceTotal_val = "";
                    productExtras_val = "";

                    if (currencyAtRight) {
                        products_price = parseFloat(orderDetails.products[i]['price']).toFixed(
                            decimal_degits) + "" + currentCurrency;
                        productPriceTotal_val = parseFloat(productPriceTotal).toFixed(decimal_degits) +
                            "" + currentCurrency;
                        productExtras_val = parseFloat(productExtras).toFixed(decimal_degits) + "" +
                            currentCurrency;
                    } else {
                        products_price = currentCurrency + "" + parseFloat(orderDetails.products[i][
                            'price'
                        ]).toFixed(decimal_degits);
                        productPriceTotal_val = currentCurrency + "" + parseFloat(productPriceTotal)
                            .toFixed(decimal_degits);
                        productExtras_val = currentCurrency + "" + parseFloat(productExtras).toFixed(
                            decimal_degits);
                    }

                    var extra_html = '';
                    if (orderDetails.products[i].extras != undefined && orderDetails.products[i]
                        .extras != '' && orderDetails.products[i].extras.length > 0) {
                        extra_html = extra_html + '<span>';
                        var extra_count = 1;
                        try {
                            orderDetails.products[i].extras.forEach((extra) => {
                                if (extra_count > 1) {
                                    extra_html = extra_html + ',' + extra;
                                } else {
                                    extra_html = extra_html + extra;
                                }
                                extra_count++;
                            });
                        } catch (error) {}
                        extra_html = extra_html + '</span>';
                    }

                    order_items += '<tr>';

                    if (orderDetails.products[i]['photo'] != '' && orderDetails.products[i]['photo'] !=
                        null) {
                        order_items +=
                            '<td class="ord-photo"><img onerror="this.onerror=null;this.src=\'' +
                            place_image + '\'" alt="#" src="' + orderDetails.products[i]['photo'] +
                            '" class="img-fluid order_img rounded"></td>';
                    } else {
                        order_items += '<td class="ord-photo"><img alt="#" src="' + place_image +
                            '" class="img-fluid order_img rounded"></td>';
                    }

                    var variant_info = '';
                    if (orderDetails.products[i]['variant_info']) {
                        variant_info += '<div class="variant-info">';
                        variant_info += '<ul>';
                        $.each(orderDetails.products[i]['variant_info']['variant_options'], function(
                            label, value) {
                            variant_info += '<li class="variant"><span class="label">' + label +
                                '</span><span class="value">' + value + '</span></li>';
                        });
                        variant_info += '</ul>';
                        variant_info += '</div>';
                    }

                    order_items += '<td class="prod-name">' + orderDetails.products[i]['name'] +
                        '<div class="extra"><span>{{ trans('lang.extras') }} :</span><span class="ext-item">' +
                        extra_html + variant_info + '</span></div></td>';
                    order_items += '<td class="qunt">';
                    checkItemReview(orderId, orderDetails.products[i]['id']);
                    order_items +=
                        '<button type="button" class="btn btn-primary item_review_btn" data-pid="' +
                        orderDetails.products[i]['id'] + '" data-cid="' + orderDetails.products[i][
                            'category_id'
                        ] + '" id="review_btn">{{trans("lang.add_review")}}</button>';
                    order_items += '</td>';
                    order_items += '<td class="qunt">x ' + orderDetails.products[i]['quantity'] +
                        '</td>';
                    order_items += '<td class="extras_price">+ ' + productExtras_val + '</td>';
                    order_items += '<td class="product_price">' + products_price + '</td>';
                    order_items += '<td class="total_product_price text-right">' +
                        productPriceTotal_val + '</td>';
                    order_items += '</tr>';
                }

                var special_discount_html = "";
                if (orderDetails.hasOwnProperty('specialDiscount') && orderDetails.specialDiscount) {
                    special_discount = orderDetails.specialDiscount.special_discount;
                    if (orderDetails.specialDiscount.specialType != "" && orderDetails.specialDiscount
                        .specialType != null) {
                        if (orderDetails.specialDiscount.specialType == "percentage") {
                            special_discount_html = " (" + orderDetails.specialDiscount
                                .special_discount_label + "%)";
                        }
                    }
                }

                order_number = orderDetails['id'];
                order_status = orderDetails['status'];
                order_total_val = formatCurrency(order_total, currencyData);
                order_subtotal_main = formatCurrency(order_subtotal, currencyData);
                order_discount_val = formatCurrency(order_discount, currencyData);
                order_shipping_val = formatCurrency(deliveryCharge, currencyData);
                order_tip_amount_val = formatCurrency(tip_amount, currencyData);
                order_special_discount = formatCurrency(special_discount, currencyData);
                order_packaging_charge = formatCurrency(packagingCharge, currencyData);
                order_platform_charge = formatCurrency(platformFee, currencyData);
                
                $("#order-number").html(order_number);
                $("#order-status").html(order_status);
                $("#order-items").html('<table class="order-list">' + order_items + '</table>');
                $("#order-subtotal").html(order_subtotal_main);
                $("#order-shipping").html(order_shipping_val);
                if (orderDetails.hasOwnProperty('couponCode') && orderDetails.couponCode != '') {
                    $('.used_coupon_code_div').show();
                    $("#used_coupon_code").html(orderDetails.couponCode);
                }
                $("#order-discount").html("(-" + order_discount_val + ")");
                $('#special_offer_text').text(special_discount_html);
                $('#special_offer_discount').html("(-" + order_special_discount + ")");
                $("#order-tip-amount").html(order_tip_amount_val);
                $("#order-total").append(order_total_val);
                if (orderDetails.hasOwnProperty('takeAway') && orderDetails.takeAway == true) {
                    $("#order-type").html("{{trans('lang.take_away')}}");
                } else {
                    $("#order-type").html("{{trans('lang.delivery')}}");
                }

                var order_restaurant = '<tr>';
                var restaurantImage = orderDetails.vendor.photo;
                if (!inValidVendors.has(orderDetails.vendorID)) {
                    var view_vendor_details = "{{ route('restaurant', ':id') }}";
                    view_vendor_details = view_vendor_details.replace(':id', 'id=' + orderDetails
                        .vendorID);
                } else {
                    view_vendor_details = "javascript:void(0)";
                }
                
                orderVendorId = orderDetails.vendorID;
                reviewUserName = orderDetails.author.firstName + ' ' + orderDetails.author.lastName;
                reviewUserProfile = orderDetails.author.profilePictureURL;
                if (restaurantImage == '' || restaurantImage == null) {
                    restaurantImage = place_image;
                } else {
                    restaurantImage = orderDetails.vendor.photo;
                }

                $('.resturant-img').attr('src', restaurantImage);
                if (orderDetails.vendor.title) {
                    $('.restaurant-title').html('<a href="' + view_vendor_details +
                        '" class="row redirecttopage" id="resturant-view">' + orderDetails.vendor
                        .title + '</a>');
                }
                if (orderDetails.vendor.phonenumber) {
                    $('#restaurant_phone').text(orderDetails.vendor.phonenumber);
                }
                if (orderDetails.vendor.location) {
                    $('#restaurant_address').text(orderDetails.vendor.location);
                }
                if (orderDetails.hasOwnProperty('takeAway') && orderDetails.takeAway == true) {
                    $(".order_driver_details").hide();
                    $(".order_shopping_div").hide();
                    $(".order_tip_div").hide();
                } else if (orderDetails.hasOwnProperty('driver')) {
                    var driverImage = orderDetails.driver.profilePictureURL;
                    if (driverImage == '' || driverImage == null) {
                        driverImage = place_image;
                    } else {
                        driverImage = orderDetails.driver.profilePictureURL;
                    }
                    var name = orderDetails.driver.firstName + " " + orderDetails.driver.lastName;
                    $('.driver-img').attr('src', driverImage);
                    if (name) {
                        $('.driver-name-title').html(name);
                    }
                    if (orderDetails.driver.phoneNumber) {
                        $('#driver_phone').text(orderDetails.driver.phoneNumber);
                    }
                    if (orderDetails.driver.carNumber) {
                        $('#driver_car_number').text(orderDetails.driver.carNumber);
                    }
                }
                if (!orderDetails.driver) {
                    $("#order_driver_details").hide();
                }
                if (orderDetails.notes) {
                    $("#order-note-box").show();
                    $("#order-note").html(orderDetails.notes);
                }
                var order_cashback = '';
                if (orderDetails.hasOwnProperty('cashback') && orderDetails.cashback != null && orderDetails.cashback != '') {
                    order_cashback = orderDetails.cashback.cashbackValue;
                    if (currencyAtRight) {
                        order_cashback = parseFloat(order_cashback).toFixed(decimal_degits) + "" +
                            currentCurrency;
                    } else {
                        order_cashback = currentCurrency + "" + parseFloat(order_cashback).toFixed(decimal_degits);
                    }
                    $('.cashback-offer').removeClass('d-none');
                    $('.cashback-offer').html("🎉 {{ trans('lang.you_get') }}" + " " + order_cashback + " " + "{{ trans('lang.cashback_on_this_order') }}");
                }

            }
        })
    }

    function checkItemReview(orderid, productId) {
        database.collection('foods_review').where('orderid', '==', orderid).where('productId', '==', productId).get()
            .then((docSnapshot) => {
                if (docSnapshot.size) {
                    $("#order-items").find('[data-pid="' + productId + '"]').text('{{ trans("lang.update_review") }}');
                    $("#reviewModel").find('.add_review_btn').text('{{ trans("lang.update_review") }}');
                }
            });
    }
    var storageRef = firebase.storage().ref('images');
    $("#review_image").resizeImg({
        callback: function(base64str) {
            var val = $('#review_image').val().toLowerCase();
            var ext = val.split('.')[1];
            var fileExtension = ['jpeg', 'jpg', 'png'];
            if ($.inArray(ext, fileExtension) == -1) {
                Swal.fire({
                    text: "{{ trans('lang.allowed_format') }}" + fileExtension.join(', '),
                    icon: "error"
                });
                return false;
            }
            var docName = val.split('fakepath')[1];
            var filename = $('#review_image').val().replace(/C:\\fakepath\\/i, '')
            var timestamp = Number(new Date());
            var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
            //upload base64str encoded string as a image to firebase
            var uploadTask = storageRef.child(filename).putString(base64str.split(',')[1], "base64", {
                contentType: 'image/' + ext
            })
            uploadTask.on('state_changed', function(snapshot) {
                var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
            }, function(error) {}, function() {
                uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
                    jQuery("#uploding_image").text("{{trans('lang.upload_is_completed')}}");
                    if (jQuery("#uploded_image ul").length > 0 && reviewOrderImage.length ==
                        0) {
                        jQuery("#uploded_image ul li").each(function() {
                            reviewOrderImage.push($(this).find('img').attr('src'));
                        });
                    }
                    reviewOrderImage.push(downloadURL);
                    jQuery("#uploded_image ul").append(
                        '<li><img onerror="this.onerror=null;this.src=\'' +
                        place_image + '\'" src="' + downloadURL +
                        '" width="100"><span class="mdi mdi-delete" data-url="' +
                        downloadURL + '">X</span></li>');
                    setTimeout(function() {
                        jQuery("#uploding_image").empty();
                    }, 1000);
                });
            });
        }
    });

    function handleFileSelect(evt) {
        var f = evt.target.files[0];
        var reader = new FileReader();
        var fileExtension = ['jpeg', 'jpg', 'png'];
        var extension = f.name.replace(/^.*\./, '');
        if ($.inArray(extension, fileExtension) == -1) {
            Swal.fire({
                text: "{{ trans('lang.allowed_format') }}" + fileExtension.join(', '),
                icon: "error"
            });
            return false;
        }
        if (f.size > 2000000) {
            Swal.fire({
                text: "{{ trans('lang.file_size_error') }}",
                icon: "error"
            });
            return false;
        }
        reader.onload = (function(theFile) {
            return function(e) {
                var filePayload = e.target.result;
                var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                var val = f.name;
                var ext = val.split('.').pop();
                var docName = val.split('fakepath')[1];
                var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                var timestamp = Number(new Date());
                var filename = timestamp + '.' + ext;
                var uploadTask = storageRef.child(filename).put(theFile);
                uploadTask.on('state_changed', function(snapshot) {
                    var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                    console.log('Upload is ' + progress + '% done');
                    jQuery("#uploding_image").text("{{trans('lang.image_is_uploading')}}");
                }, function(error) {}, function() {
                    uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
                        jQuery("#uploding_image").text("{{trans('lang.upload_is_completed')}}");
                        if (jQuery("#uploded_image ul").length > 0 && reviewOrderImage
                            .length == 0) {
                            jQuery("#uploded_image ul li").each(function() {
                                reviewOrderImage.push($(this).find('img').attr(
                                    'src'));
                            });
                        }
                        reviewOrderImage.push(downloadURL);
                        jQuery("#uploded_image ul").append(
                            '<li><img onerror="this.onerror=null;this.src=\'' +
                            place_image + '\'" src="' + downloadURL +
                            '" width="100"><span class="mdi mdi-delete" data-url="' +
                            downloadURL + '">X</span></li>');
                        setTimeout(function() {
                            jQuery("#uploding_image").empty();
                        }, 1000);
                    });
                });
            };
        })(f);
        reader.readAsDataURL(f);
    }

    function renderTaxSection(section, labelSuffix) {
        if (!taxBreakdownGrouped[section]) return;
        for (let title in taxBreakdownGrouped[section]) {
            let taxlabel = title;
            let taxAmount = parseFloat(taxBreakdownGrouped[section][title]);
            $("#order-tax").append(
                "<div class='d-flex align-items-center mb-2'>" +
                "<h6 class='font-weight-bold mb-1'>" + taxlabel + " " + labelSuffix + "</h6>" +
                "<h6 class='font-weight-bold mb-1 ml-auto'>" + formatCurrency(taxAmount, currencyData) + "</h6>" +
                "</div>"
            );
        }
    }
</script>

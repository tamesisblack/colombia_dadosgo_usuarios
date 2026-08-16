@include('layouts.app')
@include('layouts.header')
<div class="vendor-page bg-white ecom-vendor-page category-listing-page">
    <div class="offer-section py-3 resturant-banner">
        <div class="container position-relative">
            <div class="resturant-banner-inner">
                <div class="row">
                    <div class="col-md-8 resturant-banner-left" id="restaurant-pic"></div>
                    <div class="col-md-4 resturant-banner-right" id="restaurant-gallery"></div>
                </div>
            </div>
            <div id="popup-gallary" style="display:none"></div>
        </div>
    </div>
    <div class="container">
        <div class="pb-3 rounded position-relative text-dark rest-basic-detail">
            <div class="d-flex align-items-start">
                <div class="text-dark">
                    <h2 class="font-weight-bold h6" id="vendor_title"></h2>
                    <div class="d-flex">
                        <p class="text-gray mb-1" id="vendor_address"><span class="fa fa-map-marker"></span></p>
                        <div class="rest-time">
                            <span class="text-dark-50 font-weight-bold m-0 pl-3 time"></span><span
                                class="text-dark m-0 font-weight-bold" id="vendor_open_time1"></span>
                        </div>
                    </div>
                    <div class="store-opening-time">
                        <p class="text-dark-50 text-danger" id="next_open_time"></p>
                    </div>
                    <div class="rating-wrap d-flex align-items-center mt-2" id="restaurant_ratings"></div>
                </div>
                <div class="feather_icon ml-auto">
                    <div class="row fu-review">
                        <?php if (Auth::check()) : ?>
                        <a href="javascript:void(0)"
                            class="text-decoration-none mx-1 p-2 rest-right-btn addToFavorite"><i
                                class="font-weight-bold feather-heart"></i></a>
                        <?php else : ?>
                        <a href="javascript:void(0)" class="text-decoration-none mx-1 p-2 rest-right-btn loginAlert"><i
                                class="font-weight-bold feather-heart"></i></a>
                        <?php endif; ?>
                        <a class="text-decoration-none mx-1 p-2 rest-right-btn restaurant_location_btn" target="_blank"><i class="font-weight-bold feather-map-pin"></i></a>
                        <a class="btn restaurant-contact">{{ trans('lang.contact') }}</a>
                    </div>
                    <div class="row fu-time">
                        <a class="text-decoration-none mx-1 p-2 rest-right-btn" style="pointer-events: none">
                            <span class="text-dark-50 font-weight-bold m-0 pl-3 time ">{{ trans('lang.time') }} :
                            </span>
                            <span class="text-dark m-0 font-weight-bold" id="vendor_open_time"></span>
                        </a>
                    </div>
                    <div class="row fu-status">
                        <a class="text-decoration-none mx-1 p-2 rest-right-btn">
                            <span class="text-dark m-0 font-weight-bold" style="pointer-events: none"
                                id="vendor_shop_status"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container position-relative">
        <div class="foodies-detail-coupon">
            <div class="offers-coupons mb-4" id="offers_coupons"></div>
        </div>
        <div class="ecom-vendor-product-section">
            <div class="row">
                <div class="col-md-3 restaurant-detail-left">
                    <div id="category-list"></div>
                </div>
                <div class="col-md-9 restaurant-detail-right">
                    <div id="product-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="vendorContactModal" tabindex="-1" role="dialog" aria-labelledby="vendorContactLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="vendorContactLabel">{{ trans('lang.contact_vendor') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>{{ trans('lang.name') }}:</strong> <span id="vendorName"></span></p>
        <p><strong>{{ trans('lang.email') }}:</strong> <span id="vendorEmail"></span></p>
        <p><strong>{{ trans('lang.phone') }}:</strong> <span id="vendorPhone"></span></p>
      </div>
    </div>
  </div>
</div>

<input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $_GET['id']; ?>">
<input type="hidden" name="restaurant_name" id="restaurant_name" value="">
<input type="hidden" name="restaurant_location" id="restaurant_location" value="">
<input type="hidden" name="restaurant_latitude" id="restaurant_latitude" value="">
<input type="hidden" name="restaurant_longitude" id="restaurant_longitude" value="">
<input type="hidden" name="restaurant_image" id="restaurant_image" value="">
@include('layouts.footer')
@include('layouts.nav')
<!-- GeoFirestore -->
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script type="text/javascript">
    var vendorOpen = false;
    var vendorId = "<?php echo $_GET['id']; ?>";
    var takeaway = "<?php echo Session::get('takeawayOption'); ?>";
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
    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');
    var placeholderImageSrc = '';
    placeholderImageRef.get().then(async function(placeholderImageSnapshots) {
        var placeHolderImageData = placeholderImageSnapshots.data();
        placeholderImageSrc = placeHolderImageData.image;
    })
    var enableDinein = false;
    var refDineinForRestaurant = database.collection('settings').doc("DineinForRestaurant");
    refDineinForRestaurant.get().then(async function(snapshotsDineinForRestaurant) {
        var dineinForRestaurantData = snapshotsDineinForRestaurant.data();
        enableDinein = dineinForRestaurantData.isEnabledForCustomer;
    });
    var specialOfferVendor = [];
    let specialOfferForHour = [];
    var enableSpecialOffer = false;
    var specialOfferRef = database.collection('settings').doc('specialDiscountOffer');
    specialOfferRef.get().then(async function(snapShots) {
        var specialOfferData = snapShots.data();
        if (specialOfferData.isEnable) {
            enableSpecialOffer = specialOfferData.isEnable;
        }
    });
    var catsRef = database.collection('vendor_categories').where("publish", "==", true);
    var vendorDetailsRef = database.collection('vendors').where('id', "==", vendorId);
    var vendorProductsRef = database.collection('vendor_products').where('vendorID', "==", vendorId).where("publish",
        "==", true).orderBy('createdAt', 'asc');
    var productLimit = 0;
    if (takeaway == 'false' || takeaway == false) {
        vendorProductsRef = vendorProductsRef.where('takeawayOption', '==', false);
    }

    var priceData = {};
    jQuery("#data-table_processing").show();
    $(document).ready(async function() {
        var subscriptionModel = localStorage.getItem('subscriptionModel');

        if (subscriptionModel == true || subscriptionModel == "true") {

            await database.collection('vendors').doc(vendorId).get().then(async function(snapshots) {
                var vendorData = snapshots.data();
                if (vendorData && vendorData.hasOwnProperty('subscription_plan') && vendorData
                    .subscription_plan != null) {
                    if (vendorData.subscription_plan.itemLimit != "-1") {
                        vendorProductsRef = vendorProductsRef.limit(Number(vendorData
                            .subscription_plan.itemLimit));
                        productLimit = Number(vendorData.subscription_plan.itemLimit);
                    }

                }
            });
        }


        /* Add to favorite Code start*/
        priceData = await fetchVendorPriceData();
        var store_id = vendorId;
        if (user_uuid != undefined) {
            var user_id = user_uuid;
        } else {
            var user_id = '';
        }
        database.collection('favorite_restaurant').where('restaurant_id', '==', store_id).where('user_id',
            '==',
            user_id).get().then(async function(favoritevendorsnapshots) {
            if (favoritevendorsnapshots.docs.length > 0) {
                $('.addToFavorite').html(
                    '<i class="font-weight-bold fa fa-heart" style="color:red"></i>');
            } else {
                $('.addToFavorite').html('<i class="font-weight-bold feather-heart" ></i>');
            }
        });
        $('.loginAlert').on('click', function() {
            Swal.fire({
                text: "{{ trans('lang.login_to_favorite') }}",
                icon: "error"
            });
        });
        $('.addToFavorite').on('click', function() {
            var user_id = user_uuid;
            database.collection('favorite_restaurant').where('restaurant_id', '==', store_id).where(
                'user_id', '==', user_id).get().then(async function(favoritevendorsnapshots) {
                if (favoritevendorsnapshots.docs.length > 0) {
                    var id = favoritevendorsnapshots.docs[0].id;
                    database.collection('favorite_restaurant').doc(id).delete().then(
                        function() {
                            $('.addToFavorite').html(
                                '<i class="font-weight-bold feather-heart" ></i>'
                                );
                        });
                } else {
                    var id = "<?php echo uniqid(); ?>";
                    database.collection('favorite_restaurant').doc(id).set({
                        'restaurant_id': store_id,
                        'user_id': user_id
                    }).then(function(result) {
                        $('.addToFavorite').html(
                            '<i class="font-weight-bold fa fa-heart" style="color:red"></i>'
                        );
                    });
                }
            });
        });
        /* Add to favorite Code End*/
        getVendorDetails();
        getCategories();
        $(document).on("click", ".category-item", function() {
            if (!$(this).hasClass('active')) {
                $(this).addClass('active').siblings().removeClass('active');
                getProducts($(this).data('category-id'));
            }
        });
        getCouponDetails();
    });
    async function getVendorDetails() {
        vendorDetailsRef.get().then(async function(vendorSnapshots) {
            if (!vendorSnapshots.empty) {
                var vendorDetails = vendorSnapshots.docs[0].data();

                $("#vendor_title").append(vendorDetails.title);
                $("#vendor_address").append(vendorDetails.location);
                $("#vendor_shop_status").html("{{ trans('lang.closed') }}");
                $("#vendor_shop_status").addClass('close');
                var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
                    'Saturday'
                ];
                $(".resturant-banner-inner").addClass('store-closed');

                if (!checkIfStoreIsOpen(vendorDetails)) {
                    let nextOpenTime = getStoreNextOpeningTime(vendorDetails);
                    $("#next_open_time").html(nextOpenTime);
                }

                var currentdate = new Date();
                var currentDay = days[currentdate.getDay()];
                var hour = currentdate.getHours();
                var minute = currentdate.getMinutes();
                if (hour < 10) {
                    hour = '0' + hour
                }
                if (minute < 10) {
                    minute = '0' + minute
                }
                var currentHours = hour + ':' + minute;
                if (vendorDetails.hasOwnProperty('workingHours')) {
                    for (i = 0; i < vendorDetails.workingHours.length; i++) {
                        var day = vendorDetails.workingHours[i]['day'];
                        if (vendorDetails.workingHours[i]['day'] == currentDay) {
                            if (vendorDetails.workingHours[i]['timeslot'].length != 0) {
                                for (j = 0; j < vendorDetails.workingHours[i]['timeslot'].length; j++) {
                                    var timeslot = vendorDetails.workingHours[i]['timeslot'][j];
                                    var TimeslotHourVar = {
                                        'from': timeslot[`from`],
                                        'to': timeslot[`to`],
                                        'closeingType': timeslot[`closeingType`]
                                    };
                                    var [h, m] = timeslot[`from`].split(":");
                                    var from = ((h % 12 ? h % 12 : 12) + ":" + m, h >= 12 ? 'PM' :
                                        'AM');
                                    var from_time = (h % 12 ? h % 12 : 12) + ":" + m;
                                    var [h2, m2] = timeslot[`to`].split(":");
                                    var to = ((h2 % 12 ? h2 % 12 : 12) + ":" + m2, h2 >= 12 ? 'PM' :
                                        'AM');
                                    var time = (h2 % 12 ? h2 % 12 : 12) + ":" + m2;
                                    $('#vendor_open_time').append(from_time + ' ' + from + ' - ' +
                                        time + ' ' + to +
                                        '<br/><span class="margine" style="margin-right: 65px;"></span>'
                                    );
                                    if (currentHours >= timeslot[`from`] && currentHours <= timeslot[
                                            `to`]) {
                                        $("#vendor_shop_status").html("{{ trans('lang.open') }}");
                                        $("#vendor_shop_status").removeClass('close');
                                        $("#vendor_shop_status").addClass('open');
                                        $(".resturant-banner-inner").removeClass('store-closed');
                                    }
                                }
                            } else {
                                $('.time').html('');
                            }
                        }
                    }
                }
                if (vendorDetails.hasOwnProperty('reststatus') && vendorDetails.reststatus == true) {
                    vendorOpen = vendorDetails.reststatus;
                } else {}
                var newdeliveryCharge = [];
                try {
                    if (deliveryChargemain.vendor_can_modify) {
                        if (vendorDetails.deliveryCharge) {
                            if (vendorDetails.deliveryCharge.delivery_charges_per_km && vendorDetails
                                .deliveryCharge.minimum_delivery_charges && vendorDetails.deliveryCharge
                                .minimum_delivery_charges_within_km) {
                                deliveryChargemain = vendorDetails.deliveryCharge;
                            }
                        }
                    }
                } catch (error) {}
                if (vendorDetails.hasOwnProperty('specialDiscount')) {
                    specialOfferVendor = vendorDetails.specialDiscount;
                }
                var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
                    'Saturday'
                ];
                var currentdate = new Date();
                var currentDay = days[currentdate.getDay()];
                var currentTime = currentdate.getHours() + ":" + currentdate.getMinutes();
                if (enableSpecialOffer) {
                    if (specialOfferVendor.length != 0) {
                        for (i = 0; i < specialOfferVendor.length; i++) {
                            if (specialOfferVendor[i]['day'] == currentDay) {
                                if (specialOfferVendor[i]['timeslot'].length > 0) {
                                    for (j = 0; j < specialOfferVendor[i]['timeslot'].length; j++) {
                                        if (currentTime >= specialOfferVendor[i]['timeslot'][j][
                                                'from'
                                            ] && currentTime <= specialOfferVendor[i]['timeslot'][
                                                j
                                            ]['to']) {
                                            if (specialOfferVendor[i]['timeslot'][j]['discount_type'] ==
                                                'delivery') {
                                                specialOfferForHour = [];
                                                specialOfferForHour.push(specialOfferVendor[i][
                                                    'timeslot'
                                                ][j]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                setCookie('specialOfferForHourMain', JSON.stringify(specialOfferForHour), 365);
                $(".restaurant_location_btn").attr("href", "http://maps.google.com?q=" + vendorDetails
                    .latitude + "," + vendorDetails.longitude);
                if (vendorDetails.hasOwnProperty('photo') && vendorDetails.photo != '' && vendorDetails
                    .photo != null) {
                    photo = vendorDetails.photo;
                } else {
                    photo = placeholderImageSrc;
                }
                $("#restaurant-pic").html('<img onerror="this.onerror=null;this.src=\'' +
                    placeholderImageSrc + '\'" alt="#" class="restaurant-pic" src="' + photo + '">');
                if (vendorDetails.photos.length > 0) {
                    var gallery = '<div class="row">';
                    gallery += '<div class="col-md-6">';
                    gallery += '<div class="resturant-banner-right-block">';
                    if (vendorDetails.photos[0] != "" && vendorDetails.photos[0] != null) {
                        gallery += '<img onerror="this.onerror=null;this.src=\'' + placeholderImageSrc +
                            '\'" src="' + vendorDetails.photos[0] + '" class="banner-small-pic">';
                    } else {
                        gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';
                    }
                    gallery += '</div>';
                    gallery += '<div class="resturant-banner-right-block">';
                    if (vendorDetails.photos[1] != "" && vendorDetails.photos[1] != null) {
                        gallery += '<img onerror="this.onerror=null;this.src=\'' + placeholderImageSrc +
                            '\'" src="' + vendorDetails.photos[1] + '" class="banner-small-pic">';
                    } else {
                        gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';
                    }
                    gallery += '</div>';
                    gallery += '</div>';
                    gallery += '<div class="col-md-6">';
                    gallery += '<div class="resturant-banner-right-block view-all-blc">';
                    gallery += '<span class="see-gallary">{{ trans('lang.see_gallary') }}</span>';
                    if (vendorDetails.photos[2] != "" && vendorDetails.photos[2] != null) {
                        gallery += '<img onerror="this.onerror=null;this.src=\'' + placeholderImageSrc +
                            '\'" src="' + vendorDetails.photos[2] + '" class="banner-small-pic">';
                    } else {
                        gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';
                    }
                    gallery += '</div>';
                    gallery += '</div>';
                    gallery += '</div>';
                    $("#restaurant-gallery").html(gallery);
                    var popup_gallery = '';
                    $.each(vendorDetails.photos, function(key, value) {
                        popup_gallery += '<a href="' + value +
                            '"><img onerror="this.onerror=null;this.src=\'' +
                            placeholderImageSrc + '\'" src="' + value + '"></a>';
                    });
                    $("#popup-gallary").html(popup_gallery);
                    $('#popup-gallary').slickLightbox();
                    $('.see-gallary').click(function() {
                        $('#popup-gallary a:first-child').click();
                    });
                } else {
                    var gallery = '<div class="row">';
                    gallery += '<div class="col-md-6">';
                    gallery += '<div class="resturant-banner-right-block">';
                    gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';
                    gallery += '</div>';
                    gallery += '<div class="resturant-banner-right-block">';
                    gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';
                    gallery += '</div>';
                    gallery += '</div>';
                    gallery += '<div class="col-md-6">';
                    gallery += '<div class="resturant-banner-right-block view-all-blc">';
                    gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';
                    gallery += '</div>';
                    gallery += '</div>';
                    gallery += '</div>';
                    $("#restaurant-gallery").html(gallery);
                }
                if (vendorDetails.hasOwnProperty('reviewsCount') && vendorDetails.reviewsCount != '' &&
                    vendorDetails.reviewsCount != null) {
                    rating = Math.round(parseFloat(vendorDetails.reviewsSum) / parseInt(vendorDetails
                        .reviewsCount));
                    reviewsCount = vendorDetails.reviewsCount;
                } else {
                    reviewsCount = 0;
                    rating = 0;
                }
                var html_rating = '<ul class="rating" data-rating="' + rating + '">';
                html_rating = html_rating + '<li class="rating__item"></li>';
                html_rating = html_rating + '<li class="rating__item"></li>';
                html_rating = html_rating + '<li class="rating__item"></li>';
                html_rating = html_rating + '<li class="rating__item"></li>';
                html_rating = html_rating + '<li class="rating__item"></li>';
                html_rating = html_rating +
                    '</ul><p class="label-rating ml-2 small" id="vendor_reviews">(' + reviewsCount +
                    ' {{ trans('lang.review') }})</p>';
                $("#restaurant_ratings").html(html_rating);
                if ($("#restaurant_place").length) {
                    $("#vendor_name_place").html(vendorDetails.title);
                    if (vendorDetails.photo) {
                        $("#restaurant_name_place").attr('src', vendorDetails.photo);
                        setTimeout(function() {
                            $("#restaurant_image_place").show()
                        }, 1000);
                    } else {
                        $("#restaurant_image_place").remove();
                    }
                    $("#restaurant_location_place").html('<i class="feather-map-pin"></i>' +
                        vendorDetails.location);
                    $("#restaurant_place").show();
                }
            }
        })
    }
    async function getCategories() {

        var vendorCategoryIds = [];
        await vendorProductsRef.get().then(async function(snapshots) {

            snapshots.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                if (jQuery.inArray(datas.categoryID, vendorCategoryIds) == -1) {
                    vendorCategoryIds.push(datas.categoryID);
                }
            });
        });
        await catsRef.get().then(async function(snapshots) {
            if (snapshots != undefined) {
                var html = '';
                var alldata = [];
                snapshots.docs.forEach((listval) => {
                    var datas = listval.data();
                    for (var i = 0; i < vendorCategoryIds.length; i++) {
                        if (vendorCategoryIds[i] == datas.id) {
                            datas.id = listval.id;
                            alldata.push(datas);
                        }
                    }
                });
                html = html + '<div class="vandor-sidebar">';
                html = html + '<h3>{{ trans('lang.categories') }}</h3>';
                if (alldata.length > 0) {
                    html = html + '<ul class="vandorcat-list">';
                    alldata.forEach((listval) => {
                        var val = listval;
                        if (val.photo != "" && val.photo != null) {
                            photo = val.photo;
                        } else {
                            photo = placeholderImageSrc;
                        }
                        html = html + '<li class="category-item" data-category-id="' + val.id +
                            '">';
                        html = html +
                            '<a href="javascript:void(0)"><span><img onerror="this.onerror=null;this.src=\'' +
                            placeholderImageSrc + '\'" src="' + photo + '"></span>' + val
                            .title + '</a>';
                        html = html + '</li>';
                    });
                    html = html + '</ul>';
                } else {
                    html = html + '<p>{{ trans('lang.no_results') }}</p>';
                }
                if (html != '') {
                    var append_list = document.getElementById('category-list');
                    append_list.innerHTML = html;
                    var category_id = $('#category-list .category-item').first().addClass('active')
                        .data('category-id');
                    if (category_id) {
                        getProducts(category_id);
                    }
                }
            }
            jQuery("#data-table_processing").hide();
        });
    }

    async function getCouponDetails() {
        var date = new Date();
        var couponRef = database.collection('coupons').where('isEnabled', '==', true).where("isPublic", '==', true)
            .where("resturant_id", "==", vendorId).where('expiresAt', '>=', date);
        var couponHtml = '';
        let menuHtmlx = couponRef.get().then(async function(couponRefSnapshots) {
            if (couponRefSnapshots.docs.length > 0) {
                couponHtml +=
                    '<div class="coupon-code"><label>{{ trans('lang.available_coupon') }}</label><span></span></div>';
                couponHtml += '<div class="copupon-list">';
                couponHtml += '<ul>';
                couponRefSnapshots.docs.forEach((doc) => {
                    coupon = doc.data();
                    if (coupon.expiresAt) {
                        var date1 = coupon.expiresAt.toDate().toDateString();
                        var date = new Date(date1);
                        var dd = String(date.getDate()).padStart(2, '0');
                        var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                        var yyyy = date.getFullYear();
                        var expiresDate = yyyy + '-' + mm + '-' + dd;
                    }
                    if (coupon.discountType == 'Percentage') {
                        var discount = coupon.discount + '%'
                    } else {
                        coupon.discount = parseFloat(coupon.discount);
                        if (currencyAtRight) {
                            var discount = coupon.discount.toFixed(decimal_degits) + "" +
                                currentCurrency;
                        } else {
                            var discount = currentCurrency + "" + coupon.discount.toFixed(
                                decimal_degits);
                        }
                    }
                    couponHtml += '<li value="' + coupon.code + '"><span class="per-off">' +
                        discount + ' OFF </span><span>' + coupon.code + ' | Valid till ' +
                        expiresDate + '</span></li>';
                });
                couponHtml += '</ul></div>';
            }
            return couponHtml;
        })
        let menuHtml = await menuHtmlx.then(function(html) {
            if (html != undefined) {
                return html;
            }
        })
        $('#offers_coupons').html(menuHtml);
    }
    async function getProducts(category_id) {
        jQuery("#data-table_processing").show();
        var product_list = document.getElementById('product-list');
        product_list.innerHTML = '';
        var html = '';
        vendorProductsRef.get().then(async function(snapshots) {

            html = buildProductsHTML(snapshots, category_id);
            if (html != '') {
                product_list.innerHTML = html;
                jQuery("#data-table_processing").hide();
            }
        });
    }

    function buildProductsHTML(snapshots, category_id) {
        var html = '';
        var alldata = [];
        snapshots.docs.forEach((listval) => {
            var datas = listval.data();
            if (datas.categoryID == category_id) {
                datas.id = listval.id;
                alldata.push(datas);
            }
        });
        var count = 0;
        var popularFoodCount = 0;
        html = html + '<div class="row">';
        alldata.forEach((listval) => {
            var val = listval;
            var vendor_id_single = val.id;
            var view_vendor_details = "{{ route('productDetail', ':id') }}";
            view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);
            var rating = 0;
            var reviewsCount = 0;
            if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.hasOwnProperty('reviewsCount') &&
                val.reviewsCount != 0 && val.reviewsCount != null) {
                rating = (val.reviewsSum / val.reviewsCount);
                rating = Math.round(rating * 10) / 10;
                reviewsCount = val.reviewsCount;
            }
            html = html +
                '<div class="col-md-4 product-list"><div class="list-card position-relative"><div class="list-card-image">';
            if (val.photo != "" && val.photo != null) {
                photo = val.photo;
            } else {
                photo = placeholderImageSrc;
            }
            status = '{{ trans('lang.veg') }}';
            statusclass = 'open';
            if (val.hasOwnProperty('nonveg')) {
                if (val.nonveg == true) {
                    status = '{{ trans('lang.non_veg') }}';
                    statusclass = 'closed';
                }
            }
            html = html + '<div class="member-plan position-absolute"><span class="badge badge-dark ' +
                statusclass + '">' + status + '</span></div><a href="' + view_vendor_details +
                '"><img onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" alt="#" src="' +
                photo +
                '" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body position-relative"><h6 class="product-title mb-1"><a href="' +
                view_vendor_details + '" class="text-black">' + val.name + '</a></h6>';
            html = html + '<h6 class="mb-1 popular_food_category_ pro-cat" id="popular_food_category_' + val
                .categoryID + '_' + val.id + '" ></h6>';
            let final_price = priceData[val.id];

            if (val.disPrice && val.disPrice !== '0' && !val.item_attribute) {
                let or_price = getProductFormattedPrice(parseFloat(final_price.price));
                let dis_price = getProductFormattedPrice(parseFloat(final_price.dis_price));
                html = html + '<span class="pro-price">' + dis_price + '  <s>' + or_price + '</s></span>';
            } else if (val.item_attribute && val.item_attribute.variants?.length > 0) {
                let variantPrices = val.item_attribute.variants.map(v => v.variant_price);
                let minPrice = Math.min(...variantPrices);
                let maxPrice = Math.max(...variantPrices);
                let or_price = minPrice !== maxPrice ?
                    `${getProductFormattedPrice(final_price.min)} - ${getProductFormattedPrice(final_price.max)}` :
                    getProductFormattedPrice(final_price.max);
                html = html + '<span class="pro-price">' + or_price + '</span>'
            } else {
                let or_price = getProductFormattedPrice(final_price.price);
                html = html + '<span class="pro-price">' + or_price + '</span>'
            }
            html = html +
                '<div class="star position-relative mt-3"><span class="badge badge-success"><i class="feather-star"></i>' +
                rating + ' (' + reviewsCount + ')</span></div>';
            html = html + '</div>';
            html = html + '</div></div></div>';
        });
        html = html + '</div>';
        return html;
    }

     $('.restaurant-contact').on('click', async function() {
        if(vendorId){
            let vendorRef = await database.collection('vendors').doc(vendorId).get();
            if(vendorRef.exists){
                let vendorData = vendorRef.data();
                let vendorUserRef = await database.collection('users').where('role',"==",'vendor').where('vendorID',"==", vendorId).get();
                if(!vendorUserRef.empty){
                    let vendorUserData = vendorUserRef.docs[0].data();
                    $('#vendorPhone').text(vendorData.phonenumber || '-');
                    $('#vendorName').text(vendorUserData.firstName + ' '+ vendorUserData.lastName || '-');
                    $('#vendorEmail').text(vendorUserData.email || '-');
                    $('#vendorContactModal').modal('show');
                }
            }
        }
     });
</script>

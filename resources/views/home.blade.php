@include('layouts.app')
@include('layouts.header')
<div class="siddhi-home-page">
    <div class="bg-primary px-3 d-none mobile-filter pb-3 section-content">
        <div class="row align-items-center">
            <div class="input-group rounded shadow-sm overflow-hidden col-md-9 col-sm-9">
                <div class="input-group-prepend">
                    <button class="border-0 btn btn-outline-secondary text-dark bg-white btn-block">
                        <i class="feather-search"></i>
                    </button>
                </div>
                <input type="text" class="shadow-none border-0 form-control" placeholder="Busca restaurantes o platillos">
            </div>
            <div class="text-white col-md-3 col-sm-3">
                <div class="title d-flex align-items-center">
                    <a class="text-white font-weight-bold ml-auto" href="{{ url('search') }}">{{ trans('lang.filter') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="ecommerce-banner multivendor-banner section-content">
        <div class="ecommerce-inner">
            <div class="" id="top_banner"></div>
        </div>
    </div>
    <div class="ecommerce-content multi-vendore-content section-content">
        <section class="restaurant_stories">
            <div class="container swiper-stories">
                <div id="stories" class="storiesWrapper swiper-wrapper"></div>
            </div>
        </section>
        <section class="top-categories top-categories-section">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.top_categories') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ url('categories') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div class="top_categories" id="top_categories"></div>
                
            </div>
        </section>
        <section class="top-categories highlights-section d-none">
            <div class="container">
                <div class="highlights-section-inner">
                    <div class="title d-flex align-items-center border-0 mb-0">
                        <h5>{{ trans('lang.highlights_for_you') }}</h5>
                    </div>
                    <div class="row">
                        <div class="highlights-slider highlights" id="highlights">

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="most-popular-item-section">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.popular') }} {{ trans('lang.item') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('productlist.all') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div id="most_popular_item"></div>
            </div>
        </section>
        <section class="most-popular-store-section">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.popular') }} {{ trans('lang.restaurants') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('restaurants', 'popular=yes') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div id="most_popular_store"></div>
            </div>
        </section>
        <section class="new-arrivals-section">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.new_arrivals') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('productlist.all') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div id="new_arrival"></div>
            </div>
        </section>
        <section class="offers-coupons-section">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.offers') }} {{ trans('lang.for_you') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ route('offers') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div style="display:none" class="coupon_code_copied_div mt-4 error_top text-center">
                    <p>{{ trans('lang.coupon_code_copied') }}</p>
                </div>
                <div id="offers_coupons"></div>
            </div>
        </section>
        <section class="middle-banners-section">
            <div class="container">
                <div id="middle_banner"></div>
            </div>
        </section>
        <section class="home-categories-section">
            <div class="container" id="home_categories"></div>
        </section>
        <section class="all-stores-section">
            <div class="container">
                <div class="title d-flex align-items-center">
                    <h5>{{ trans('lang.all_stores') }}</h5>
                    <span class="see-all ml-auto">
                        <a href="{{ url('restaurants') }}">{{ trans('lang.see_all') }}</a>
                    </span>
                </div>
                <div id="all_stores"></div>
                <div class="row fu-loadmore-btn">
                    <a class="page-link loadmore-btn" href="javascript:void(0);" onclick="moreload()" data-dt-idx="0" tabindex="0" id="loadmore" style="display:none">{{ trans('lang.see') }} {{ trans('lang.more') }}</a>
                    <p class="text-danger" style="display:none;" id="noMoreCoupons">{{ trans('lang.no_results') }}</p>
                </div>
            </div>
        </section>
    </div>
    <div class="zone-error m-5 p-5" style="display: none;">
        <div class="zone-image text-center">
            <img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" src="{{ asset('img/zone_logo.png') }}" width="100">
        </div>
        <div class="zone-content text-center text-center font-weight-bold text-danger">
            <h3 class="title">{{ trans('lang.zone_error_title') }}</h3>
            <h6 class="text">{{ trans('lang.zone_error_text') }}</h6>
        </div>
    </div>
    <img id="cashback-button" class="d-none" src="{{ asset('img/cashback.png') }}" alt="Cashback" />

    <!-- Cashback Offers Popup -->
    <div id="cashback-offer-popup">
        <h4>{{trans('lang.available_cashback_offer')}}</h4>
        <ul id="cashback-list">
        </ul>
    </div>
</div>
@include('layouts.footer')

<!-- lib styles -->
<link rel="stylesheet" href="{{ asset('css/dist/zuck.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/dist/skins/snapssenger.css') }}">
<script src="{{ asset('js/dist/zuck.min.js') }}"></script>
<script src="{{ asset('js/geofirestore.js') }}"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script type="text/javascript" src="{{ asset('vendor/swiper/swiper.min.js') }}"></script>

<script type="text/javascript">
    jQuery("#data-table_processing").show();

    var firestore = firebase.firestore();
    var geoFirestore = new GeoFirestore(firestore);
    var vendorId;
    var ref;
    var append_list = '';
    var top_categories = '';
    var most_popular = '';
    var most_sale = '';
    var new_product = '';
    var offers_coupons = '';
    var appName = '';
    var popularStoresList = [];
    var currentCurrency = '';
    var currencyAtRight = false;
    var storyEnabled = false;
    var VendorNearBy = '';
    var pagesize = 12;
    var offest = 1;
    var end = null;
    var endarray = [];
    var start = null;
    var nearByVendorsForStory = [];
    var vendorIds = [];
    var priceData = {};
    var enableAdvertisement = false;
    var enableCashbackOffer = false;
    var highlightsSetting = database.collection('settings').doc('globalSettings');
    var cashbackOfferSetting = database.collection('settings').doc('cashbackOffer');
    var DriverNearByRef = database.collection('settings').doc('RestaurantNearBy');
    var itemCategoriesref = database.collection('vendor_categories').where('publish', '==', true).limit(7);
    var vendorsref = geoFirestore.collection('vendors');
    var productref = database.collection('vendor_products').where('publish', '==', true);
    var bannerref = database.collection('menu_items').where("is_publish", "==", true).orderBy('set_order', 'asc');
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    var currentDate = new Date();
    var inValidVendors = new Set();
    var decimal_degits = 0;
    var isSelfDeliveryGlobally = false;
    var refGlobal = database.collection('settings').doc("globalSettings");
    refGlobal.get().then(async function(
        settingSnapshots) {
        if (settingSnapshots.data()) {
            var settingData = settingSnapshots.data();
            if (settingData.isSelfDelivery) {
                isSelfDeliveryGlobally = true;
            }
        }
    })
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
    highlightsSetting.get().then(async function(settingSnapshots) {
        if (settingSnapshots.data()) {
            var settingData = settingSnapshots.data();
            if (settingData.isEnableAdsFeature) {
                enableAdvertisement = true;
            }
        }
    })
    cashbackOfferSetting.get().then(async function(settingSnapshots) {
        if (settingSnapshots.data()) {
            var settingData = settingSnapshots.data();
            if (settingData.isEnable) {
                enableCashbackOffer = true;
            }
            (enableCashbackOffer) ? $('#cashback-button').removeClass('d-none'): $('#cashback-button').addClass('d-none');
        }
    })
    database.collection('settings').doc("story").get().then(async function(snapshots) {
        var story_data = snapshots.data();
        if (story_data.isEnabled) {
            storyEnabled = true;
        } else {
            $(".restaurant_stories").remove();
        }
    });
    var subscriptionModel = localStorage.getItem('subscriptionModel');
    var refs = database.collection('vendors').where('title', '!=', '').orderBy('title').limit(pagesize);
    var couponsRef = database.collection('coupons').where('isEnabled', '==', true).orderBy("expiresAt").startAt(
        new Date()).limit(4);

    function getBanners() {
        var available_stores = [];
        geoFirestore.collection('vendors').where('zoneId', '==', user_zone_id).get().then(async function(snapshots) {
            snapshots.docs.forEach((doc) => {
                if (!inValidVendors.has(doc.id)) {
                    available_stores.push(doc.id);
                }
            });
        });

        bannerref.get().then(async function(banners) {

            let position1_banners = [];
            let position2_banners = [];
            
            for (const banner of banners.docs) {

                const bannerData = banner.data();
                
                let redirect_type = bannerData.redirect_type || '';
                let redirect_id   = bannerData.redirect_id   || '';
                
                let isValidZone = true;

                if (redirect_type === "store") {
                    const vendorDoc = await geoFirestore.collection('vendors').doc(redirect_id).get();
                    if (!vendorDoc.exists) {
                        isValidZone = false;
                    } else {
                        const vendorData = vendorDoc.data();
                        if (vendorData.zoneId !== user_zone_id) {
                            isValidZone = false;
                        }
                    }
                }

                if (redirect_type === "product") {
                    const productDoc = await geoFirestore.collection('vendor_products').doc(redirect_id).get();
                    if (!productDoc.exists) {
                        isValidZone = false;
                    } else {
                        const productData = productDoc.data();
                        const vendorId = productData.vendorID;

                        const vendorDoc = await geoFirestore.collection('vendors').doc(vendorId).get();
                        if (!vendorDoc.exists) {
                            isValidZone = false;
                        } else {
                            const vendorData = vendorDoc.data();
                            if (vendorData.zoneId !== user_zone_id) {
                                isValidZone = false;
                            }
                        }
                    }
                }

                if (!isValidZone) {
                    redirect_type = '';
                    redirect_id = '';
                }

                if (bannerData.position == 'top') {
                    position1_banners.push({
                        photo: bannerData.photo,
                        redirect_type,
                        redirect_id
                    });
                }

                if (bannerData.position == 'middle') {
                    position2_banners.push({
                        photo: bannerData.photo,
                        redirect_type,
                        redirect_id
                    });
                }
            }

            if (position1_banners.length > 0) {
                var html = '';
                for (banner of position1_banners) {
                    html += '<div class="banner-item">';
                    html += '<div class="banner-img">';
                    var redirect_id = '#';
                    if (banner.redirect_type != '') {
                        if (banner.redirect_type == "store") {
                            redirect_id = "{{ route('restaurant', ':id') }}";
                            redirect_id = redirect_id.replace(':id', 'id=' + banner.redirect_id);
                        } else if (banner.redirect_type == "product") {
                            redirect_id = "{{ route('productDetail', ':id') }}";
                            redirect_id = redirect_id.replace(':id', banner.redirect_id);
                        } else if (banner.redirect_type == "external_link") {
                            redirect_id = banner.redirect_id;
                        }
                    }
                    html += '<a href="' + redirect_id + '"><img onerror="this.onerror=null;this.src=\'' +
                        placeholderImage + '\'" src="' + banner.photo + '"></a>';
                    html += '</div>';
                    html += '</div>';
                }
                $("#top_banner").html(html);
            } else {
                $('.ecommerce-banner').remove();
            }

            if (position2_banners.length > 0) {
                var html = '';
                for (banner of position2_banners) {
                    html += '<div class="banner-item">';
                    html += '<div class="banner-img">';
                    var redirect_id = '#';
                    if (banner.redirect_type != '') {
                        if (banner.redirect_type == "store") {
                            redirect_id = "{{ route('restaurant', ':id') }}";
                            redirect_id = redirect_id.replace(':id', 'id=' + banner.redirect_id);
                        } else if (banner.redirect_type == "product") {
                            redirect_id = "{{ route('productDetail', ':id') }}";
                            redirect_id = redirect_id.replace(':id', banner.redirect_id);
                        } else if (banner.redirect_type == "external_link") {
                            redirect_id = banner.redirect_id;
                        }
                    }
                    html += '<a href="' + redirect_id + '"><img onerror="this.onerror=null;this.src=\'' +
                        placeholderImage + '\'" src="' + banner.photo + '"></a>';
                    html += '</div>';
                    html += '</div>';
                }
                $("#middle_banner").html(html);
            } else {
                $('.middle-banners').remove();
            }

            setTimeout(function() {
                slickcatCarousel();
            }, 200)

        });
    }

    var myInterval = '';
    $(document).ready(async function() {

        // Retrieve all invalid vendors

        await checkVendors().then(expiredStores => {
            inValidVendors = expiredStores;
        });
        // Fetch and render banners before initializing Slick
        getBanners();
        if (enableAdvertisement) {
            getHighlights();
        }

        myInterval = setInterval(callStore, 1000);
    });

    function myStopTimer() {
        clearInterval(myInterval);
    }

    async function callStore() {
        if (address_lat == '' || address_lng == '' || address_lng == NaN || address_lat == NaN || address_lat ==
            null || address_lng == null) {
            return false;
        }
        DriverNearByRef.get().then(async function(DriverNearByRefSnapshots) {
            var DriverNearByRefData = DriverNearByRefSnapshots.data();
            VendorNearBy = parseInt(DriverNearByRefData.radios);
            radiusUnit = DriverNearByRefData.distanceType;

            if (radiusUnit == 'miles') {
                VendorNearBy = parseInt(VendorNearBy * 1.60934)
            }
            address_lat = parseFloat(address_lat);
            address_lng = parseFloat(address_lng);
            if (user_zone_id == null) {
                jQuery(".section-content").remove();
                jQuery(".zone-error").show();
                jQuery("#data-table_processing").hide();
                return false;
            }
            priceData = await fetchVendorPriceData();
            myStopTimer();
            getItemCategories();
            getHomepageCategory();
            getMostPopularStores();
            getAllStore();

        })
    }

    function slickcatCarousel() {
        if ($("#top_banner").length > 0 && $("#top_banner").html().trim() !== "") {
            $('#top_banner').slick({
                slidesToShow: 1,
                dots: true,
                arrows: true,
                autoplay: true, // Optional: autoplay
                autoplaySpeed: 3000, // Optional: 3 seconds autoplay delay
            });
        } else {
            console.log("Top banner element not found or empty.");
        }
        if ($("#middle_banner").length > 0 && $("#middle_banner").html().trim() !== "") {
            $('#middle_banner').slick({
                slidesToShow: 3,
                dots: true,
                arrows: true,
                responsive: [{
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 3,
                        }
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            slidesToShow: 2,
                        }
                    },
                    {
                        breakpoint: 650,
                        settings: {
                            slidesToShow: 1,
                        }
                    }
                ]
            });
        } else {
            console.log("Middle banner element not found or empty.");
        }
    }

    async function getAllStore() {
        if (VendorNearBy) {
            var nearestRestauantRefnew = geoFirestore.collection('vendors').near({
                center: new firebase.firestore.GeoPoint(address_lat, address_lng),
                radius: VendorNearBy
            }).where('zoneId', '==', user_zone_id);
        } else {
            var nearestRestauantRefnew = geoFirestore.collection('vendors').where('zoneId', '==', user_zone_id);
        }
        nearestRestauantRefnew.get().then(async function(snapshots) {
            if (snapshots.docs.length > 0) {
                var html = buildAllStoresHTML(snapshots);
                var all_stores = document.getElementById('all_stores');
                all_stores.innerHTML = html;
                start = snapshots.docs[snapshots.docs.length - 1];
                endarray.push(snapshots.docs[0]);
                // if(snapshots.docs.length<pagesize) {
                $('#loadmore').hide();
                // }
            } else {

                $(".all-stores-section").hide();
                $(".new-arrivals-section").hide();
                $(".section-content").hide();
                jQuery(".zone-error").show();
                jQuery(".zone-error").find('.title').text(
                    '{{ trans('lang.restaurant_error_title') }}');
                jQuery(".zone-error").find('.text').text('{{ trans('lang.restaurant_error_text') }}');
            }
        });
    }

    function buildAllStoresHTML(snapshots) {
        var html = '';
        var alldata = [];
        if (snapshots.docs.length > 0) {
            snapshots.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                datas.isOpen = checkIfStoreIsOpen(datas);  
                if (!inValidVendors.has(listval.id)) {
                    alldata.push(datas);
                    vendorIds.push(listval.id);
                }
            });
            alldata.sort((a, b) => b.isOpen - a.isOpen);
            alldata = alldata.slice(0, pagesize);
            
            //New arrivals products
            var newProductRef = database.collection('vendor_products').where('publish', '==', true);
            newProductRef.get().then(async function(newProductSnapshot) {
                if (newProductSnapshot.docs.length > 0) {
                    new_arrival = document.getElementById('new_arrival');
                    new_arrival.innerHTML = '';
                    var newproducthtml = await buildHTMLNewProducts(newProductSnapshot);
                    new_arrival.innerHTML = newproducthtml;
                } else {
                    $(".new-arrivals-section").remove();
                }
            });
            var count = 0;
            html = html + '<div class="row">';
            alldata.forEach((listval) => {
                var val = listval;

                var status = val.isOpen ? "{{ trans('lang.open') }}" : "{{ trans('lang.closed') }}";
                var statusclass = val.isOpen ? "open" : "closed";
                var statusclass2 = val.isOpen ? "" : "store-closed";

                var rating = 0;
                var reviewsCount = 0;
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.reviewsSum != null && val
                    .reviewsSum != '' && val.hasOwnProperty(
                        'reviewsCount') && val.reviewsCount != 0 && val.reviewsCount != null && val
                    .reviewsCount != '') {
                    rating = (val.reviewsSum / val.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = val.reviewsCount;
                }

                var vendor_id_single = val.id;
                var view_vendor_details = "{{ route('restaurant', ':id') }}";
                view_vendor_details = view_vendor_details.replace(':id', 'id=' + vendor_id_single);
                count++;
                getMinDiscount(val.id);
                
                html = html +
                    '<div class="col-md-3 product-list"><div class="list-card position-relative"><div class="list-card-image '+statusclass2+'">';
                if (val.photo != "" && val.photo != null) {
                    photo = val.photo;
                } else {
                    photo = placeholderImageSrc;
                }
                html = html + '<div class="member-plan position-absolute"><span class="badge badge-dark ' +
                    statusclass + '">' + status + '</span></div><div class="offer-icon position-absolute free-delivery-' + val.id + '"></div><a href="' + view_vendor_details +
                    '"><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="#" src="' +
                    photo +
                    '" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body"><h6 class="mb-1 popul-title"><a href="' +
                    view_vendor_details + '" class="text-black">' + val.title +
                    '</a></h6><p class="text-gray mb-1 small address"><span class="fa fa-map-marker"></span>' +val.location + '</p>';
                if (!checkIfStoreIsOpen(val)) {
                    let nextOpenTime = getStoreNextOpeningTime(val);
                    html=html+'<p class="text-gray mb-1 small text-danger"><span class="fa fa-clock-o"></span> '+nextOpenTime+'</p>';
                }
                html = html + '<span class="pro-price vendor_dis_' + val.id + ' " ></span>';
                html = html +
                    '<div class="star position-relative mt-3"><span class="badge badge-success "><i class="feather-star"></i>' +
                    rating + ' (' + reviewsCount + ')</span></div>';
                html = html + '</div>';
                html = html + '</div></div></div>';
                checkSelfDeliveryForVendor(val.id);
            });
            html = html + '</div>';
        } else {
            $('#noMoreCoupons').show();
            $('#loadmore').hide();
            setTimeout(function() {
                $("#noMoreCoupons").hide();
            }, 4000);
        }
        return html;
    }


    async function getItemCategories() {
        itemCategoriesref.get().then(async function(foodCategories) {
            top_categories = document.getElementById('top_categories');
            top_categories.innerHTML = '';
            foodCategorieshtml = await buildHTMLItemCategory(foodCategories);
            top_categories.innerHTML = foodCategorieshtml;
            jQuery("#data-table_processing").hide();
        })
    }

    async function getHomepageCategory() {
        var home_cat_ref = database.collection('vendor_categories').where("publish", "==", true).where(
            'show_in_homepage', '==', true).limit(5);
        home_cat_ref.get().then(async function(homeCategories) {
            home_categories = document.getElementById('home_categories');
            home_categories.innerHTML = '';
            var homeCategorieshtml = '';
            var alldata = [];
            homeCategories.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                alldata.push(datas);
            });
            for (listval of alldata) {
                var val = listval;
                var category_id = val.id;
                var category_route = "{{ route('RestaurantsbyCategory', [':id']) }}";
                category_route = category_route.replace(':id', category_id);
                if (val.photo != "" && val.photo != null) {
                    photo = val.photo;
                } else {
                    photo = placeholderImageSrc;
                }
                var haveStores = await catHaveStores(category_id);

                if (haveStores == true) {
                    var productHtml = await buildHTMLHomeCategoryStores(category_id);
                    if (productHtml != '') {
                        homeCategorieshtml += '<div class="category-content mb-5" id="category-content-' + category_id + '">';
                        homeCategorieshtml += '<div class="title d-flex align-items-center">';
                        homeCategorieshtml += '<h5>' + val.title + '</h5>';
                        homeCategorieshtml += '<span class="see-all ml-auto"><a href="' + category_route +
                            '">{!! trans('lang.see_all') !!}</a></span>';
                        homeCategorieshtml += '</div>';
                        homeCategorieshtml += productHtml;
                        homeCategorieshtml += '</div>';
                    }
                }
            }
            if (homeCategorieshtml != '') {
                home_categories.innerHTML = homeCategorieshtml;
            } else {
                $('.home-categories-section').remove();
            }
        })
    }

    async function catHaveStores(categoryId) {
        var snapshots = await database.collection('vendors').where("categoryID", "array-contains", categoryId).where('zoneId',
            '==', user_zone_id).get();
        if (snapshots.docs.length > 0) {
            return true;
        } else {
            return false;
        }
    }

    async function buildHTMLHomeCategoryStores(category_id) {
        var html = '';
        var snapshots = await database.collection('vendors').where('categoryID', "array-contains", category_id).where('zoneId',
            '==', user_zone_id).limit(4).get();
        var alldata = [];
        snapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            datas.isOpen = checkIfStoreIsOpen(datas);  
            if (!inValidVendors.has(listval.id)) {
                alldata.push(datas);
            }
        });
        alldata.sort((a, b) => b.isOpen - a.isOpen);

        if (alldata.length > 0) {
            var count = 0;
            html = html + '<div class="row">';
            alldata.forEach((listval) => {
                var val = listval;

                var rating = 0;
                var reviewsCount = 0;
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.reviewsSum != null && val
                    .reviewsSum != '' && val.hasOwnProperty(
                        'reviewsCount') && val.reviewsCount != 0 && val.reviewsCount != null && val
                    .reviewsCount != '') {
                    rating = (val.reviewsSum / val.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = val.reviewsCount;
                }
                
                var status = val.isOpen ? "{{ trans('lang.open') }}" : "{{ trans('lang.closed') }}";
                var statusclass = val.isOpen ? "open" : "closed";
                var statusclass2 = val.isOpen ? "" : "store-closed";

                var vendor_id_single = val.id;
                var view_vendor_details = "{{ route('restaurant', ':id') }}";
                view_vendor_details = view_vendor_details.replace(':id', 'id=' + vendor_id_single);
                count++;
                getMinDiscount(val.id);
                
                html = html +
                    '<div class="col-md-3 product-list"><div class="list-card position-relative"><div class="list-card-image ' + statusclass2 + '">';
                if (val.photo != "" && val.photo != null) {
                    photo = val.photo;
                } else {
                    photo = placeholderImageSrc;
                }
                html = html + '<div class="member-plan position-absolute"><span class="badge badge-dark ' +
                    statusclass + '">' + status + '</span></div><div class="offer-icon position-absolute free-delivery-' + val.id + '"></div><a href="' + view_vendor_details +
                    '"><img  onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="#" src="' +
                    photo +
                    '" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body"><h6 class="mb-1 popul-title"><a href="' +
                    view_vendor_details + '" class="text-black">' + val.title +
                    '</a></h6><p class="text-gray mb-1 small address"><span class="fa fa-map-marker"></span>' +
                    val.location + '</p>';
                if (!checkIfStoreIsOpen(val)) {
                    let nextOpenTime = getStoreNextOpeningTime(val);
                    html=html+'<p class="text-gray mb-1 small text-danger"><span class="fa fa-clock-o"></span> '+nextOpenTime+'</p>';
                }
                html = html + '<span class="pro-price vendor_dis_' + val.id + ' " ></span>';
                html = html +
                    '<div class="star position-relative mt-3"><span class="badge badge-success "><i class="feather-star"></i>' +
                    rating + ' (' + reviewsCount + ')</span></div>';
                html = html + '</div>';
                html = html + '</div></div></div>';
                checkSelfDeliveryForVendor(val.id);
            });
            html = html + '</div>';
        }
        return html;
    }

    async function buildHTMLItemCategory(foodCategories) {
        var html = '';
        var alldata = [];
        for (const listval of foodCategories.docs) {
            var datas = listval.data();
            datas.id = listval.id;
            var haveStores = await catHaveStores(datas.id);
            if (haveStores === true) {
                alldata.push(datas);
            }
        }
        html += '<div class="row">';
        alldata.forEach((listval) => {
            var val = listval;
            var category_id = val.id;
            var trending_route = "{{ route('RestaurantsbyCategory', [':id']) }}";
            trending_route = trending_route.replace(':id', category_id);
            if (val.photo != "" && val.photo != null) {
                photo = val.photo;
            } else {
                photo = placeholderImageSrc;
            }
            html = html + '<div class="col-md-2 top-cat-list"><a class="d-block text-center cat-link" href="' +
                trending_route + '"><span class="cat-img"><img  onerror="this.onerror=null;this.src=\'' +
                placeholderImage + '\'" alt="#" src="' + photo +
                '" class="img-fluid mb-2"></span><h4 class="m-0">' + val.title + '</h4></a></div>';
        });
        html += '</div>';
        return html;
    }

    async function getPopularItem() {

        if (popularStoresList.length > 0) {
            var popularStoresListnw = [];
            most_popular_item = document.getElementById('most_popular_item');
            if (most_popular_item) {
                most_popular_item.innerHTML = '';
            }
            var from = 0;
            var total = 0;
            for (let i = 0; i < (popularStoresList.length / 10); i++) {
                from = i * 10;
                popularStoresListnw = [];
                total = 0;
                for (let j = 0; j < popularStoresList.length; j++) {
                    if (j > from && total < 10) {
                        total++;
                        popularStoresListnw.push(popularStoresList[j]);
                    }
                }

                if (popularStoresListnw.length) {
                    var refpopularItem = database.collection('vendor_products').where("vendorID", "in", popularStoresListnw).where('publish', '==', true)
                    refpopularItem.get().then(async function(snapshotsPopularItem) {
                        var trendingStorehtml = await buildHTMLPopularItem(snapshotsPopularItem);
                        if (most_popular_item) {
                            most_popular_item.innerHTML = trendingStorehtml;
                        }
                    });
                } else {
                    $(".most-popular-item-section").remove();
                }
            }
        }
    }

    async function getMostPopularStores() {
        var popularRestauantRefnew = geoFirestore.collection('vendors').near({
            center: new firebase.firestore.GeoPoint(address_lat, address_lng),
            radius: VendorNearBy
        }).limit(200).where('zoneId', '==', user_zone_id);

        await popularRestauantRefnew.get().then(async function(popularRestauantSnapshot) {
            if (popularRestauantSnapshot.docs.length > 0) {
                var most_popular_store = document.getElementById('most_popular_store');
                most_popular_store.innerHTML = '';
                var popularStorehtml = await buildHTMLPopularStore(popularRestauantSnapshot);
                most_popular_store.innerHTML = popularStorehtml;
            } else {
                $(".most-popular-store-section").remove();
                $(".most-popular-item-section").remove();
                $('.offers-coupons-section').remove();
            }
        });
        if (storyEnabled) {

            await getStories();
        }
    }

    function buildHTMLMostSaleStore(mostSaleSnapshot) {
        var html = '';
        var alldata = [];
        mostSaleSnapshot.docs.forEach((listval) => {
            var datas = listval.data();
            if (!inValidVendors.has(listval.id)) {
                alldata.push(datas);
            }
            var rating = 0;
            var reviewsCount = 0;
            if (datas.hasOwnProperty('reviewsSum') && datas.reviewsSum > 0 && datas.hasOwnProperty('reviewsCount') && datas.reviewsCount > 0) {
                rating = (datas.reviewsSum / datas.reviewsCount);
                rating = Math.round(rating * 10) / 10;
            }
            datas.rating = rating;
            datas.isOpen = checkIfStoreIsOpen(datas);  
            alldata.push(datas);
        });
        if (alldata.length) {
            alldata.sort((a, b) => b.isOpen - a.isOpen);
            alldata = sortArrayOfObjects(alldata, "rating");
            alldata = alldata.slice(0, 4);
        }
        html = html + '<div class="row">';
        alldata.forEach((listval) => {
            var val = listval;
            var vendor_id_single = val.id;
            var view_vendor_details = "";
            if (vendor_id_single) {
                view_vendor_details = "{{ route('restaurant', ':id') }}";
                view_vendor_details = view_vendor_details.replace(':id', 'id=' + vendor_id_single);
            }
            var rating = 0;
            var reviewsCount = 0;
            if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.reviewsSum != null && val
                .reviewsSum != '' && val.hasOwnProperty(
                    'reviewsCount') && val.reviewsCount != 0 && val.reviewsCount != null && val.reviewsCount !=
                '') {
                rating = (val.reviewsSum / val.reviewsCount);
                rating = Math.round(rating * 10) / 10;
                reviewsCount = val.reviewsCount;
            }
            
            var status = val.isOpen ? "{{ trans('lang.open') }}" : "{{ trans('lang.closed') }}";
            var statusclass = val.isOpen ? "open" : "closed";
            var statusclass2 = val.isOpen ? "" : "store-closed";

            getMinDiscount(val.id);

            html = html + '<div class="col-md-3 pro-list">' +
                '<div class="list-card position-relative">' +
                '<div class="py-2 position-relative">' +
                '<div class="list-card-body">' +
                '<div class="list-card-top">' +
                '<h6 class="mb-1 popul-title"><a href="' + view_vendor_details + '" class="text-black">' + val
                .title + '</a></h6><h6>' + val.location + '</h6>';
            html = html + '<span class="pro-price vendor_dis_' + val.id + ' " ></span>';
            html = html +
                '<div class="star position-relative mt-3"><span class="badge badge-success "><i class="feather-star"></i>' +
                rating + ' (' + reviewsCount + ')</span></div>';
            html = html + '</div><div class="list-card-image ' + statusclass2 + '">';
            if (val.photo != "" && val.photo != null) {
                photo = val.photo;
            } else {
                photo = placeholderImageSrc;
            }
            html = html + '<div class="member-plan position-absolute"><span class="badge badge-dark ' +
                statusclass + '">' + status + '</span></div><a href="' + view_vendor_details +
                '"><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'"  alt="#" src="' +
                photo + '" class="img-fluid item-img w-100"></a></div>';
            html = html + '</div>';
            html = html + '</div></div></div>';
        });
        html = html + '</div>';
        return html;
    }

    async function buildHTMLNewProducts(newProductSnapshot) {
        var html = '';
        var alldata = [];
        newProductSnapshot.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            if ($.inArray(datas.vendorID, vendorIds) !== -1) {
                const exists = alldata.some(record => record.vendorID === datas.vendorID);
                if (!exists) {
                    alldata.push(datas);
                }
            }
        });
        alldata = alldata.slice(0, 4);
        html = html + '<div class="row">';
        await Promise.all(alldata.map(async (listval) => {
            var val = listval;
            var vendor_id_single = val.id;
            var view_vendor_details = "{{ route('productDetail', ':id') }}";
            view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);
            // Compute rating and reviews
            let rating = val.reviewsSum && val.reviewsCount ? (val.reviewsSum / val.reviewsCount)
                .toFixed(1) : 0;
            let reviewsCount = val.reviewsCount || 0;
            // Determine veg/non-veg status
            let status = val.veg ? '{{ trans('lang.veg') }}' : '{{ trans('lang.non_veg') }}';
            let statusclass = val.veg ? "open" : "closed";
            // Fallback for image
            let photo = val.photo && val.photo !== "" ? val.photo : placeholderImageSrc;
            // Append product card
            html += `
        <div class="col-md-3 product-list">
            <div class="list-card position-relative">
                <div class="list-card-image">
                    <div class="member-plan position-absolute">
                        <span class="badge badge-dark ${statusclass}">${status}</span>
                    </div>
                    <a href="${view_vendor_details}">
                        <img onerror="this.onerror=null;this.src='${placeholderImage}'" alt="#" src="${photo}" class="img-fluid item-img w-100">
                    </a>
                </div>
                <div class="py-2 position-relative">
                    <div class="list-card-body">
                        <h6 class="mb-1 popul-title">
                            <a href="${view_vendor_details}" class="text-black">${val.name}</a>
                        </h6>
                        <h6 class="text-gray mb-1 cat-title" id="popular_food_category_${val.categoryID}_${val.id}"></h6>
    `;
            // Append price information
            let final_price = priceData[val.id];
            if (val.disPrice && val.disPrice !== '0' && !val.item_attribute) {
                let or_price = getProductFormattedPrice(parseFloat(final_price.price));
                let dis_price = getProductFormattedPrice(parseFloat(final_price.dis_price));
                html += `<h6 class="text-gray mb-1 pro-price">${dis_price} <s>${or_price}</s></h6>`;
            } else if (val.item_attribute && val.item_attribute.variants?.length > 0) {
                let variantPrices = val.item_attribute.variants.map(v => v.variant_price);
                let minPrice = Math.min(...variantPrices);
                let maxPrice = Math.max(...variantPrices);
                let or_price = minPrice !== maxPrice ?
                    `${getProductFormattedPrice(final_price.min)} - ${getProductFormattedPrice(final_price.max)}` :
                    getProductFormattedPrice(minPrice);
                html += `<h6 class="text-gray mb-1 pro-price">${or_price}</h6>`;
            } else {
                let or_price = getProductFormattedPrice(final_price.price);
                html += `<h6 class="text-gray mb-1 pro-price">${or_price}</h6>`;
            }
            // Append rating information
            html += `
                        <div class="star position-relative mt-3">
                            <span class="badge badge-success"><i class="feather-star"></i>${rating} (${reviewsCount})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
        }));
        html = html + '</div>';
        return html;
    }

    sortArrayOfObjects = (arr, key) => {
        return arr.sort((a, b) => {
            return b[key] - a[key];
        });
    };

    function copyToClipboard(text) {
        var tempInput = document.createElement("input");
        document.body.appendChild(tempInput);
        tempInput.value = text;
        tempInput.select();
        tempInput.setSelectionRange(0, 99999);
        document.execCommand("copy");
        document.body.removeChild(tempInput);
    }

    function buildHTMLPopularStore(popularRestauantSnapshot) {
        var html = '';
        var alldata = [];
        popularRestauantSnapshot.docs.forEach((listval) => {
            var datas = listval.data();
            checkSelfDeliveryForVendor(datas.id);
            var rating = 0;
            var reviewsCount = 0;
            if (datas.hasOwnProperty('reviewsSum') && datas.reviewsSum != 0 && datas.reviewsSum != null && datas.hasOwnProperty(
                    'reviewsCount') && datas.reviewsCount != 0 && datas.reviewsCount != null) {
                rating = (datas.reviewsSum / datas.reviewsCount);
                rating = Math.round(rating * 10) / 10;
            }
            datas.rating = rating;
            datas.isOpen = checkIfStoreIsOpen(datas);  
            if (datas.title != '' && !inValidVendors.has(datas.id)) {
                alldata.push(datas);
                if (nearByVendorsForStory.includes(datas.id)) {} else {
                    nearByVendorsForStory.push(datas.id);
                }
            }
        });
        alldata.sort((a, b) => b.isOpen - a.isOpen);
        if (alldata.length) {
            alldata = sortArrayOfObjects(alldata, "rating");
            alldata = alldata.slice(0, 4);
            var count = 0;
            var popularItemCount = 0;
            html = html + '<div class="row">';
            alldata.forEach((listval) => {
                var val = listval;
                var rating = 0;
                var reviewsCount = 0;
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.reviewsSum != null && val
                    .reviewsSum != '' && val.hasOwnProperty(
                        'reviewsCount') && val.reviewsCount != 0 && val.reviewsCount != null && val
                    .reviewsCount != '') {
                    rating = (val.reviewsSum / val.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = val.reviewsCount;
                }
                if (popularItemCount < 10) {
                    popularItemCount++;
                    popularStoresList.push(val.id);
                }
                
                var status = val.isOpen ? "{{ trans('lang.open') }}" : "{{ trans('lang.closed') }}";
                var statusclass = val.isOpen ? "open" : "closed";
                var statusclass2 = val.isOpen ? "" : "store-closed";

                var vendor_id_single = val.id;
                var view_vendor_details = "{{ route('restaurant', ':id') }}";
                view_vendor_details = view_vendor_details.replace(':id', 'id=' + vendor_id_single);
                count++;
                getMinDiscount(val.id);
                
                html = html +
                    '<div class="col-md-3 product-list"><div class="list-card position-relative"><div class="list-card-image ' + statusclass2 + '"><span class="discount-price vendor_dis_' +
                    val.id + ' " ></span>';
                if (val.photo != "" && val.photo != null) {
                    photo = val.photo;
                } else {
                    photo = placeholderImageSrc;
                }
                html = html + '<div class="member-plan position-absolute"><span class="badge badge-dark ' +
                    statusclass + '">' + status + '</span></div><div class="offer-icon position-absolute free-delivery-' + val.id + '"></div><a href="' + view_vendor_details +
                    '"><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="#" src="' +
                    photo +
                    '" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body position-relative"><h6 class="mb-1 popul-title"><a href="' +
                    view_vendor_details + '" class="text-black">' + val.title +
                    '</a></h6><p class="text-gray mb-1 small address"><span class="fa fa-map-marker"></span>' +
                    val.location + '</p>';
                if (!checkIfStoreIsOpen(val)) {
                    let nextOpenTime = getStoreNextOpeningTime(val);
                    html=html+'<p class="text-gray mb-1 small text-danger"><span class="fa fa-clock-o"></span> '+nextOpenTime+'</p>';
                }
                html = html +
                    '<div class="star position-relative mt-3"><span class="badge badge-success "><i class="feather-star"></i>' +
                    rating + ' (' + reviewsCount + ')</span></div>';
                html = html + '</div>';
                html = html + '</div></div></div>';
            });
            html = html + '</div>';
        } else {
            html = '<p class="text-danger text-center">{{ trans('lang.no_results') }}</p>';
        }
        getPopularItem();
        getCouponsList();
        return html;
    }

    async function buildHTMLPopularItem(popularItemsnapshot) {
        var html = '';
        var alldata = [];
        let sortedAndMergedData = [];
        var groupedData = {};
        popularItemsnapshot.docs.forEach((listval) => {
            var datas = listval.data();
            var rating = 0;
            var reviewsCount = 0;
            if (datas.hasOwnProperty('reviewsSum') && datas.reviewsSum != 0 && datas.reviewsSum != null && datas.hasOwnProperty(
                    'reviewsCount') && datas.reviewsCount != 0 && datas.reviewsCount != null) {
                rating = (datas.reviewsSum / datas.reviewsCount);
                rating = Math.round(rating * 10) / 10;
            }
            datas.rating = rating;


            if (subscriptionModel == true || subscriptionModel == "true") {

                if (!groupedData[datas.vendorID]) {
                    groupedData[datas.vendorID] = [];
                }
                groupedData[datas.vendorID].push(datas);
            } else {

                alldata.push(datas);
            }

        });
        if (subscriptionModel == true || subscriptionModel == "true") {
            await Promise.all(Object.keys(groupedData).map(async (vendorID) => {
                let products = groupedData[vendorID];

                var vendorItemLimit = await getVendorItemLimit(vendorID);
                await products.sort((a, b) => {
                    if (a.hasOwnProperty('createdAt') && b.hasOwnProperty('createdAt')) {
                        const timeA = new Date(a.createdAt.toDate()).getTime();
                        const timeB = new Date(b.createdAt.toDate()).getTime();
                        return timeA - timeB; // Ascending order
                    }
                });

                if (parseInt(vendorItemLimit) != -1) {
                    products = products.slice(0, vendorItemLimit);
                }

                sortedAndMergedData = sortedAndMergedData.concat(products);
            }));

            sortedAndMergedData = sortArrayOfObjects(sortedAndMergedData, "rating");
            alldata = sortedAndMergedData.slice(0, 5);
        } else {
            alldata = sortArrayOfObjects(alldata, "rating");

            alldata = alldata.slice(0, 5);
        }
        var count = 1;
        html += '<div class="row">';
        await Promise.all(alldata.map(async (listval, index) => {
            //if(index>=5) return; // Limit to 5 items
            let val = listval;


            let vendor_id_single = val.id;
            let view_vendor_details = "{{ route('productDetail', ':id') }}".replace(':id',
                vendor_id_single);
            // Compute rating and reviews
            let rating = val.reviewsSum && val.reviewsCount ? (val.reviewsSum / val.reviewsCount)
                .toFixed(1) : 0;
            let reviewsCount = val.reviewsCount || 0;
            // Determine veg/non-veg status
            let status = val.veg ? '{{ trans('lang.veg') }}' : '{{ trans('lang.non_veg') }}';
            let statusclass = val.veg ? "open" : "closed";
            // Fallback for image
            let photo = val.photo && val.photo !== "" ? val.photo : placeholderImageSrc;
            // Append product card
            html += `
        <div class="col-md-3 product-list">
            <div class="list-card position-relative">
                <div class="list-card-image">
                    <div class="member-plan position-absolute">
                        <span class="badge badge-dark ${statusclass}">${status}</span>
                    </div>
                    <a href="${view_vendor_details}">
                        <img onerror="this.onerror=null;this.src='${placeholderImage}'" alt="#" src="${photo}" class="img-fluid item-img w-100">
                    </a>
                </div>
                <div class="py-2 position-relative">
                    <div class="list-card-body">
                        <h6 class="mb-1 popul-title">
                            <a href="${view_vendor_details}" class="text-black">${val.name}</a>
                        </h6>
                        <h6 class="text-gray mb-1 cat-title" id="popular_food_category_${val.categoryID}_${val.id}"></h6>
    `;
            // Append price information
            let final_price = priceData[val.id];
            if (val.disPrice && val.disPrice !== '0' && !val.item_attribute) {
                let or_price = getProductFormattedPrice(parseFloat(final_price.price));
                let dis_price = getProductFormattedPrice(parseFloat(final_price.dis_price));
                html += `<h6 class="text-gray mb-1 pro-price">${dis_price} <s>${or_price}</s></h6>`;
            } else if (val.item_attribute && val.item_attribute.variants?.length > 0) {
                let variantPrices = val.item_attribute.variants.map(v => v.variant_price);
                let minPrice = Math.min(...variantPrices);
                let maxPrice = Math.max(...variantPrices);
                let or_price = minPrice !== maxPrice ?
                    `${getProductFormattedPrice(final_price.min)} - ${getProductFormattedPrice(final_price.max)}` :
                    getProductFormattedPrice(final_price.max);
                html += `<h6 class="text-gray mb-1 pro-price">${or_price}</h6>`;
            } else {
                let or_price = getProductFormattedPrice(final_price.price);
                html += `<h6 class="text-gray mb-1 pro-price">${or_price}</h6>`;
            }
            // Append rating information
            html += `
                        <div class="star position-relative mt-3">
                            <span class="badge badge-success"><i class="feather-star"></i>${rating} (${reviewsCount})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
        }));
        html += '</div>';
        return html;
    }

    async function popularItemCategory(categoryId, foodId) {
        var popularItemCategory = '';
        await database.collection('vendor_categories').where("id", "==", categoryId).get().then(async function(
            categorySnapshots) {
            if (categorySnapshots.docs[0]) {
                var categoryData = categorySnapshots.docs[0].data();
                popularItemCategory = categoryData.title;
                jQuery("#popular_food_category_" + categoryId + "_" + foodId).text(popularItemCategory);
            }
        });
        return popularItemCategory;
    }

    async function getMinDiscount(vendorId) {
        var min_discount = '';
        var disdata = [];
        var couponSnapshots = await couponsRef.where('resturant_id', '==', vendorId).get();
        if (couponSnapshots.docs.length > 0) {
            couponSnapshots.docs.forEach((coupon) => {
                var cdata = coupon.data();
                disdata.push(parseInt(cdata.discount));
            });
            if (disdata.length > 0) {
                discount = Math.min.apply(Math, disdata);
                min_discount = "Min " + discount + "% off";
            }
        }
        if (min_discount) {
            $('.vendor_dis_' + vendorId).text(min_discount);
        } else {
            $('.vendor_dis_' + vendorId).hide();
        }
    }

    async function getCouponsList() {
        if (popularStoresList.length > 0) {
            var popularStoresList2 = popularStoresList.slice(0, 4);
            var couponsRef2 = database.collection('coupons').where('resturant_id', 'in', popularStoresList2).where(
                'isEnabled', '==', true).where('isPublic', '==', true).where('expiresAt', '>=', new Date());
            couponsRef2.get().then(async function(couponListSnapshot) {
                if (couponListSnapshot.docs.length > 0) {
                    offers_coupons = document.getElementById('offers_coupons');
                    offers_coupons.innerHTML = '';
                    var couponlistHTML = buildHTMLCouponList(couponListSnapshot);
                    offers_coupons.innerHTML = couponlistHTML;
                } else {
                    $('.offers-coupons-section').remove();
                }
            })
        }
    }

    function buildHTMLCouponList(couponListSnapshot) {
        var html = '';
        var alldata = [];
        couponListSnapshot.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
        if (alldata.length > 0) {
            html = html + '<div class="row">';
            alldata.forEach((listval) => {
                var val = listval;
                var status = '{{ trans('lang.closed') }}';
                var statusclass = "closed";
                if (val.hasOwnProperty('reststatus') && val.reststatus) {
                    status = '{{ trans('lang.open') }}';
                    statusclass = "open";
                }
                var vendor_id_single = val.resturant_id;
                var view_vendor_details = "";
                if (vendor_id_single) {
                    view_vendor_details = "{{ route('restaurant', ':id') }}";
                    view_vendor_details = view_vendor_details.replace(':id', 'id=' + vendor_id_single);
                }
                html = html +
                    '<div class="col-md-3 pro-list"><div class="list-card position-relative"><div class="list-card-image">';
                if (val.image != "" && val.image != null) {
                    photo = val.image;
                } else {
                    photo = placeholderImageSrc;
                }
                getVendorName(vendor_id_single);
                html = html + '<a href="' + view_vendor_details +
                    '"><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="#" src="' +
                    photo +
                    '" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body"><h6 class="mb-1 popul-title"><a href="' +
                    view_vendor_details + '" class="text-black vendor_title_' + vendor_id_single +
                    '"></a></h6>';
                html = html +
                    '<div class="text-gray mb-1 small offer-code"><a href="javascript:void(0)" onclick="copyToClipboard(`' +
                    val.code + '`)"><i class="fa fa-file-text-o"></i> ' + val.code + '</a></div>';
                html = html + '</div>';
                html = html + '</div></div></div>';
            });
            html = html + '</div>';
        }
        return html;
    }

    async function getVendorName(vendorId) {
        await database.collection('vendors').where("id", "==", vendorId).get().then(async function(
            categorySnapshots) {
            if (categorySnapshots.docs[0]) {
                var categoryData = categorySnapshots.docs[0].data();
                vendorName = categoryData.title;
                jQuery(".vendor_title_" + vendorId).text(vendorName);
            }
        });
    }

    async function getStories() {

        var alldata = [];
        var storyDatas = [];
        var queryPromises = [];
        for (var i = 0; i < nearByVendorsForStory.length; i++) {
            const query = await database.collection('story').where('vendorID', '==', nearByVendorsForStory[i]).limit(2).get();
            queryPromises.push(query);

        }

        await Promise.all(queryPromises).then((querySnapshots) => {
            for (const querySnapshot of querySnapshots) {
                querySnapshot.forEach((doc) => {
                    alldata.push(doc.data());
                });
            }
        });
        for (data of alldata) {

            var vendorDataRes = await database.collection('vendors').doc(data.vendorID).get();
            var vendorData = vendorDataRes.data();

            if (vendorData != undefined) {

                var vendorRating = '';
                if (vendorData.hasOwnProperty('reviewsSum') && vendorData.reviewsSum != 0 && vendorData
                    .hasOwnProperty('reviewsCount') && vendorData.reviewsCount != 0) {
                    rating = (vendorData.reviewsSum / vendorData.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = vendorData.reviewsCount;
                    vendorRating = vendorRating +
                        '<div class="star position-relative ml-1 mt-3"><span class="badge badge-success "><i class="feather-star"></i>' +
                        rating + ' (' + reviewsCount + ')</span></div>';
                }

                var vendorLink = "{{ route('restaurant', ':id') }}";
                vendorLink = vendorLink.replace(':id', 'id=' + vendorData.id);

                var itemsObject = [];

                data.videoUrl.forEach((video) => {
                    var itemObject = {
                        id: vendorData.id,
                        type: "video",
                        length: 5,
                        src: video,
                        link: vendorLink,
                        linkText: vendorData.title,
                        time: new Date(data.createdAt.toDate()).getTime() / 1000,
                        seen: false
                    };
                    itemsObject.push(itemObject);
                });

                var storyObject = {
                    id: vendorData.id,
                    photo: data.videoThumbnail,
                    name: vendorData.title,
                    link: vendorLink,
                    seen: false,
                    items: itemsObject
                }
                storyDatas.push(storyObject);
            }
        }

        if (storyDatas.length) {

            new Zuck('stories', {
                backNative: true,
                previousTap: true,
                skin: 'snapssenger',
                autoFullScreen: true,
                avatars: true,
                list: false,
                cubeEffect: true,
                localStorage: true,
                stories: storyDatas,
                language: {
                    unmute: '<i class="fa fa-volume-up"></i>',
                }
            });

            new Swiper('.swiper-stories', {
                slidesPerView: 5,
                breakpoints: {
                    991: {
                        slidesPerView: 4,
                    },
                    767: {
                        slidesPerView: 3,
                    },
                    650: {
                        slidesPerView: 2,
                    },
                },
            });
        }
    }
    async function getVendorItemLimit(vendorID) {
        var itemLimit = 0;
        await database.collection('vendors').where('id', '==', vendorID).get().then(async function(snapshots) {
            if (snapshots.docs.length > 0) {
                var data = snapshots.docs[0].data();
                if (data.hasOwnProperty('subscription_plan') && data.subscription_plan != null && data.subscription_plan != '') {
                    itemLimit = data.subscription_plan.itemLimit;
                }
            }
        })
        return itemLimit;
    }
    async function getHighlights() {
        var html = '';
        var advlength = 0;
        database.collection('advertisements')
            .where('status', '==', 'approved')
            .where('paymentStatus', '==', true)
            .get()
            .then(async function(snapshots) {
                if (snapshots.docs.length === 0) {
                    $('.highlights-section').addClass('d-none');
                    return;
                }


                let advertisements = [];

                snapshots.docs.forEach(doc => {
                    advertisements.push({
                        ...doc.data()
                    });
                });

                let filteredAds = [];

                for (const data of advertisements) {
                    const ExpiryDate = data.endDate;
                    const startDate = data.startDate;

                    const vendorDoc = await geoFirestore.collection('vendors').doc(data.vendorId).get();
                    if (!vendorDoc.exists) continue;

                    const vendorData = vendorDoc.data();
                    if (vendorData.zoneId !== user_zone_id) continue;
                    if (data.isPaused) continue;
                    let rating = 0;
                    let reviewsCount = 0;

                    if (vendorData.reviewsSum && vendorData.reviewsCount) {
                        rating = Math.round((vendorData.reviewsSum / vendorData.reviewsCount) * 10) / 10;
                        reviewsCount = vendorData.reviewsCount;
                    }

                    const start = startDate && new Date(startDate.seconds * 1000);
                    const end = ExpiryDate && new Date(ExpiryDate.seconds * 1000);

                    if (start && start < new Date() && end && end > new Date()) {
                        filteredAds.push({
                            ...data,
                            rating,
                            reviewsCount
                        });
                    }
                }

                filteredAds.sort((a, b) => {
                    const aPriority = (a.priority === "N/A" || a.priority === null || a.priority === undefined) ? Infinity : parseInt(a.priority);
                    const bPriority = (b.priority === "N/A" || b.priority === null || b.priority === undefined) ? Infinity : parseInt(b.priority);
                    return aPriority - bPriority;
                });
                advlength = filteredAds.length;
                for (const data of filteredAds) {
                    const view_vendor_details = `{{ route('restaurant', ':id') }}`.replace(':id', 'id=' + data.vendorId);

                    if (data.type === 'restaurant_promotion') {
                        html += `<div id="profile-preview-box" class="cat-item profile-preview-box pt-4"><div class=" profile-preview-box-inner">
                        <div class="profile-preview-img">
                            <div class="profile-preview-img-inner">
                                <img src="${data.coverImage}">
                            </div>
                            <div class="review-rating-demo ${data.showRating || data.showReview ? '' : 'd-none'}" >
                                <div class="rating-text static-text ${data.showRating ? '' : 'd-none'}" style="display: block;" id="preview-rating">
                                    <div class="rating-number d-flex align-items-center ">
                                        <i class="fa fa-star"></i><span id="rating_data">${data.rating}</span>
                                    </div>
                                </div>
                                <span class="review--text static-text ${data.showReview ? '' : 'd-none'}" style="display: inline;" id="preview-review">(${data.reviewsCount === 0 ? '0' : '+' + data.reviewsCount})</span>
                            </div>
                        </div>
                        <div class="profile-preview-content">
                            <?php if (Auth::check()) : ?>
                            <div class="profile-preview-wishlist">
                                <a href="javascript:void(0)" id="${data.vendorId}" class="preview-wishlist-icon addToFavorite">
                                    <i class="fa fa-heart-o"></i>
                                </a>
                            </div>
                            <?php else : ?>
                            <div class="profile-preview-wishlist">
                                <a href="javascript:void(0)" class="preview-wishlist-icon loginAlert"><i class="fa fa-heart-o"></i></a>
                            </div>
                            <?php endif; ?>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div class="prev-profile-image">
                                    <img src="${data.profileImage}">
                                </div>
                                <div class="prev-profile-detail">
                                    <a href="${view_vendor_details}"><h3>${data.title}</h3></a>
                                    <a href="${view_vendor_details}"><p>${data.description}</p></a>
                                </div>
                            </div>
                        </div>
                    </div></div>`;
                    } else {
                        html += `<div id="profile-preview-box" class="cat-item profile-preview-box pt-4"><div class="profile-preview-box-inner">
                        <div class="profile-preview-img">
                            <div class="profile-preview-img-inner">
                                <video width="400px" height="250px" controls autoplay muted playsinline>
                                    <source src="${data.video}" type="video/mp4">
                                </video>
                            </div>
                        </div>
                        <div class="profile-preview-content">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div class="prev-profile-detail">
                                    <a href="${view_vendor_details}"><h3>${data.title}</h3></a>
                                    <a href="${view_vendor_details}"><p>${data.description}</p></a>                                   
                                </div> 		
                            </div>
                            <div class="prev-profile-btn">
                                <a href="${view_vendor_details}" class="btn btn-primary py-1 px-3 cursor-auto text-white" id="preview-arrow" tabindex="0">
                                    <span class="fa fa-arrow-right"></span>
                                </a>
                            </div>
                        </div>
                    </div></div>`;
                    }
                }
                if (html != '') {
                    $('#highlights').html(html);
                    setTimeout(() => {
                        slickHightlightsCarousel(advlength);
                    }, 1000);
                } else {
                    $('.highlights-section').addClass('d-none');
                }




                <?php if (Auth::check()) : ?>
                $('.addToFavorite').each(function() {
                    var vendorId = $(this).attr('id');
                    checkFavVendor(vendorId);
                });
                <?php endif; ?>
            });

    }



    async function slickHightlightsCarousel(advlength) {
        const highlightCount = advlength;
        if ($('.highlights-slider').hasClass('slick-initialized')) {
            $('.highlights-slider').slick('unslick');
        }
        $('.highlights-section').removeClass('d-none');
        if (highlightCount <= 1) return;
        $('.highlights-slider').slick({
            slidesToShow: (advlength <= 3) ? advlength - 1 : 3,
            arrows: true,
            responsive: [{
                    breakpoint: 1199,
                    settings: {
                        arrows: true,
                        centerMode: true,
                        centerPadding: '40px',
                        slidesToShow: (advlength <= 3) ? advlength - 1 : 3,
                    }
                }, {
                    breakpoint: 992,
                    settings: {
                        arrows: true,
                        centerMode: true,
                        centerPadding: '40px',
                        slidesToShow: (advlength <= 3) ? advlength - 1 : 3,
                    }
                }, {
                    breakpoint: 768,
                    settings: {
                        arrows: true,
                        centerMode: true,
                        centerPadding: '40px',
                        slidesToShow: (advlength <= 3) ? advlength - 1 : 3,
                    }
                },
                {
                    breakpoint: 560,
                    settings: {
                        arrows: false,
                        centerMode: true,
                        centerPadding: '20px',
                        slidesToShow: 1,
                    }
                }
            ]
        });

    }

    async function checkFavVendor(vendorId) {
        var user_id = user_uuid;
        database.collection('favorite_restaurant').where('restaurant_id', '==', vendorId).where('user_id', '==', user_id).get().then(async function(favoritevendorsnapshots) {
            if (favoritevendorsnapshots.docs.length > 0) {
                $('.addToFavorite[id="' + vendorId + '"]').html(
                    '<i class="font-weight-bold fa fa-heart" style="color:red"></i>');
            } else {
                $('.addToFavorite[id="' + vendorId + '"]').html('<i class="font-weight-bold feather-heart" ></i>');
            }
        });
    }
    $(document).on('click', '.loginAlert', function() {
        Swal.fire({
            text: "{{ trans('lang.login_to_favorite') }}",
            icon: "error"
        });
    });

    $(document).on('click', '.addToFavorite', function() {

        var user_id = user_uuid;
        var vendorId = this.id;
        database.collection('favorite_restaurant').where('restaurant_id', '==', vendorId).where(
            'user_id', '==', user_id).get().then(async function(favoritevendorsnapshots) {
            if (favoritevendorsnapshots.docs.length > 0) {
                var id = favoritevendorsnapshots.docs[0].id;
                database.collection('favorite_restaurant').doc(id).delete().then(
                    function() {
                        $('.addToFavorite[id="' + vendorId + '"]').html(
                            '<i class="font-weight-bold feather-heart" ></i>'
                        );
                    });
            } else {
                var id = database.collection('tmp').doc().id;
                database.collection('favorite_restaurant').doc(id).set({
                    'restaurant_id': vendorId,
                    'user_id': user_id
                }).then(function(result) {
                    $('.addToFavorite[id="' + vendorId + '"]').html(
                        '<i class="font-weight-bold fa fa-heart" style="color:red"></i>'
                    );
                });
            }
        });
    });

    function checkSelfDeliveryForVendor(vendorId) {
        setTimeout(function() {
            database.collection('vendors').doc(vendorId).get().then(async function(snapshots) {
                if (snapshots.exists) {
                    var data = snapshots.data();
                    if (data.hasOwnProperty('isSelfDelivery') && data.isSelfDelivery != null && data.isSelfDelivery != '') {
                        if (data.isSelfDelivery && isSelfDeliveryGlobally) {
                            $('.free-delivery-' + vendorId).html('<span><img src="{{ asset('img/free_delivery.png') }}" width="100px"> {{ trans('lang.free_delivery') }}</span> ');
                        }
                    }
                }
            })
        }, 3000);
    }
    document.getElementById("cashback-button").addEventListener("click", async () => {
        await loadCashbackOffers();
        const popup = document.getElementById("cashback-offer-popup");
        popup.style.display = (popup.style.display === "none" || popup.style.display === "") ? "block" : "none";

    });
    async function loadCashbackOffers() {
        <?php $id = null;
        if (Auth::user()) {
            $id = Auth::user()->getvendorId();
        } ?>
        var userId="{{$id}}";
        var newdate = new Date();
        var todaydate = new Date(newdate.setHours(23, 59, 59, 999));
        var ref = database.collection('cashback').where('isEnabled', '==', true).where('startDate', '<=', newdate);
        ref.get().then(async function(snapshots) {
            var alldata = [];
            snapshots.docs.forEach((listval) => {
                var datas = listval.data();
                const isActive = datas?.endDate?.toDate && datas.endDate.toDate().getTime() >= newdate.getTime();
                const isAllCustomer = datas?.allCustomer === true;
                const allowedIds = datas?.customerIds || [];
                if (isActive) {
                    if (!userId) {
                        if (isAllCustomer) {
                            alldata.push(datas);
                        }
                    } else {

                        if (isAllCustomer || allowedIds.includes(userId)) {
                            alldata.push(datas);
                        }
                    }
                }

            });
            var html = '';

            alldata.forEach(listval => {
                var data = listval;
                var minimumPurchase = '';
                if (currencyAtRight) {
                    minimumPurchase = parseFloat(data.minumumPurchaseAmount).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    minimumPurchase = currentCurrency + "" + parseFloat(data.minumumPurchaseAmount).toFixed(decimal_degits);
                }
                var endDate = '';
                var endTime = '';

                if (data.hasOwnProperty('endDate') && data.endDate) {
                    try {
                        endDate = data.endDate.toDate().toDateString();
                        endTime = data.endDate.toDate().toLocaleTimeString('en-US');
                    } catch (err) {}
                }
                html += `<li><div class="media-body"><h6 class="date">${data.title}</h6><p class="text-dark mb-0 offer-address">{{ trans('lang.minimum_spent') }} ${minimumPurchase} | {{ trans('lang.valid_till') }} ${endDate}</p></div></li>`;
            });

            $('#cashback-list').html(html);
        })
    }
</script>
@include('layouts.nav')

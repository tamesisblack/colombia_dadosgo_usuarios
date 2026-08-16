@include('layouts.app')
@include('layouts.header')
<div class="d-none">
    <div class="bg-primary p-3 d-flex align-items-center">
        <a class="toggle togglew toggle-2" href="#"><span></span></a>
        <h4 class="font-weight-bold m-0 text-white">{{ trans('lang.search') }}</h4>
    </div>
</div>
<div class="siddhi-popular">
    <div class="container">
        <div class="search py-5">
            <div class="input-group mb-4">
                <input type="text" class="form-control form-control-lg input_search border-right-0 food_search" id="inlineFormInputGroup" value="" placeholder="{{ trans('lang.search_product_items') }}">
                <div class="input-group-prepend">
                    <div class="btn input-group-text bg-white border_search border-left-0 text-primary search_food_btn">
                        <i class="feather-search"></i>
                    </div>
                </div>
            </div>
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active border-0 bg-light text-dark rounded" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><i class="feather-home mr-2"></i><span class="restaurant_counts"></span></a>
                </li>
            </ul>
            <div class="text-center py-5 not_found_div" style="display:none">
                <p class="h4 mb-4"><i class="feather-search bg-primary rounded p-2"></i></p>
                <p class="font-weight-bold text-dark h5">{{ trans('lang.nothing_found') }}</p>
                <p>{{ trans('lang.please_try_again') }}</p>
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="container mt-4 mb-4 p-0">
                        <div id="append_list1" class="res-search-list-1"></div>
                    </div>
                </div>
            </div>
            <ul class="nav nav-tabs border-0 d-none" id="myTab2" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active border-0 bg-light text-dark rounded" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><i class="feather-home mr-2"></i><span class="products_counts"></span></a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="container mt-4 mb-4 p-0">
                        <div id="append_list2" class="res-search-list-1"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="row d-flex align-items-center justify-content-center py-5">
                    <div class="col-md-4 py-5">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@include('layouts.nav')
<script type="text/javascript">
    jQuery("#data-table_processing").show();
    var currentCurrency = '';
    var currencyAtRight = false;
    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
    placeholder.get().then(async function(snapshotsimage) {
        var placeholderImageData = snapshotsimage.data();
        placeholderImage = placeholderImageData.image;
    })
    var currentDate = new Date();
    var inValidVendors = new Set();
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
    var productdata = [];
    var vendordata = [];
    var productsref = database.collection('vendor_products').where('publish', '==', true);
    var vendorsref = database.collection('vendors');
    var append_list = document.getElementById('append_list1');
    var append_list2 = document.getElementById('append_list2');
    var priceData = {};
    var subscriptionModel = localStorage.getItem('subscriptionModel');

    async function getProductList() {
        var vendorIds = [];
        var vendorsSnapshots = await database.collection('vendors').where('zoneId', '==', user_zone_id).get();
        if (vendorsSnapshots.docs.length > 0) {
            vendorsSnapshots.docs.forEach((listval) => {
                if (!inValidVendors.has(listval.id)) {
                    vendorIds.push(listval.id);
                }
            });
        }

        var vendorIdsChunk = [];
        let sortedAndMergedData = [];
        var groupedData = {};

        // Split vendor IDs into chunks of 10
        while (vendorIds.length > 0) {
            vendorIdsChunk.push(vendorIds.splice(0, 10));
        }

        // Fetch products for each chunk
        await Promise.all(
            vendorIdsChunk.map(async (vendorIds) => {
                const productsnapshot = await productsref.where("vendorID", "in", vendorIds).get();
                productsnapshot.docs.forEach((listval) => {
                    const val = listval.data();

                    if (subscriptionModel == true || subscriptionModel == "true") {
                        if (!groupedData[val.vendorID]) {
                            groupedData[val.vendorID] = [];
                        }
                        groupedData[val.vendorID].push(val);
                    } else {
                        productdata.push(val);
                    }
                });
            })
        );
        // Process groupedData for subscriptionModel
        if (subscriptionModel == true || subscriptionModel == "true") {
            await Promise.all(
                Object.keys(groupedData).map(async (vendorID) => {
                    let products = groupedData[vendorID];

                    const vendorItemLimit = await getVendorItemLimit(vendorID);

                    // Sort by createdAt
                    products.sort((a, b) => {
                        if (a.hasOwnProperty("createdAt") && b.hasOwnProperty("createdAt")) {
                            const timeA = new Date(a.createdAt.toDate()).getTime();
                            const timeB = new Date(b.createdAt.toDate()).getTime();
                            return timeA - timeB; // Ascending order
                        }
                    });

                    // Apply vendor item limit
                    if (parseInt(vendorItemLimit) != -1) {
                        products = products.slice(0, vendorItemLimit);
                    }
                    sortedAndMergedData = sortedAndMergedData.concat(products);
                })
            );

            productdata = sortedAndMergedData;
        }
    }

    async function getVendorList() {
        vendorsref.where('zoneId', '==', user_zone_id).get().then(async function(vendorsnapshot) {
            vendorsnapshot.docs.forEach((listval) => {
                var val = listval.data();
                val.isOpen = checkIfStoreIsOpen(val);  
                if (!inValidVendors.has(listval.id)) {
                    vendordata.push(val);
                }
            });
        });
    }
    $(document).ready(async function() {
        $("#data-table_processing").show();
        priceData = await fetchVendorPriceData();
        // Retrieve all invalid vendors
        await checkVendors().then(expiredStores => {
            inValidVendors = expiredStores;
        });

        setTimeout(async function() {
            await getProductList();
            getVendorList();
            getResults();
            $("#data-table_processing").hide();
        }, 1500);
        $(".food_search").keypress(function(e) {
            if (e.which == 13) {
                getResults();
            }
        })
        $(".search_food_btn").click(function() {
            getResults();
        });
    });
    async function getResults() {
        var vendors = [];
        var foodsearch = $(".food_search").val();
        var filter_product = [];
        var products = [];
        var delivery_option = '';
        <?php 
        if (Session::get('takeawayOption') == "true") { ?>
        delivery_option = "takeaway";
        <?php } else { ?>
        delivery_option = "delivery";
        <?php } ?>
        if (foodsearch != '') {
            productdata.forEach((listval) => {
                var data = listval;
                var Name = data.name.toLowerCase();
                var Ans = Name.indexOf(foodsearch.toLowerCase());
                if (Ans > -1) {
                    if (data.takeawayOption == true && delivery_option == "takeaway") {
                        filter_product.push(data);
                    } else if (data.takeawayOption == false && delivery_option == "takeaway") {
                        filter_product.push(data);
                    } else if (data.takeawayOption == false && delivery_option == "delivery") {
                        filter_product.push(data);
                    }
                    if (!products.includes(data.vendorID)) {
                        products.push(data.vendorID);
                    }
                }
            });
            if (products.length > 0) {
                for (i = 0; i < products.length; i++) {
                    var vendorId = products[i];
                    await database.collection('vendors').doc(vendorId).get().then(async function(snapshotss) {
                        var vendor_data = snapshotss.data();
                        vendor_data.isOpen = checkIfStoreIsOpen(vendor_data);  
                        if (vendor_data != undefined) {
                            vendors.push(vendor_data);
                        }
                    });
                }
            }
            vendordata.forEach((listval) => {
                var data = listval;
                var Name = data.title.toLowerCase();
                var Ans = Name.indexOf(foodsearch.toLowerCase());
                if (Ans > -1) {
                    if (!products.includes(data.id)) {
                        vendors.push(data);
                    }
                }
            });
        } else {
            await vendorsref.where('zoneId', '==', user_zone_id).get().then(async function(snapshots) {
                if (snapshots != undefined) {
                    snapshots.docs.forEach((listval) => {
                        var datas = listval.data();
                        datas.isOpen = checkIfStoreIsOpen(datas);  
                        if (!inValidVendors.has(datas.id)) {
                            vendors.push(datas);
                        }
                    });
                }
            });
            $('#myTab2').hide();
        }
        var html_keypress = '';
        html_keypress = buildHTML(vendors);
        product_keypress = buildProductHTML(filter_product);
        if (html_keypress == '' && product_keypress == '') {
            $(".not_found_div").show();
            append_list.innerHTML = '';
            append_list2.innerHTML = '';
            $(".restaurant_counts").text('{{ trans('lang.stores') }} (0)');
            $(".products_counts").text('{{ trans('lang.products') }} (0)');
            $("#data-table_processing").hide();
        } else if (html_keypress != '' || product_keypress != '') {
            $(".not_found_div").hide();
            append_list.innerHTML = '';
            append_list.innerHTML = html_keypress;
            append_list2.innerHTML = '';
            append_list2.innerHTML = product_keypress;
            $("#data-table_processing").hide();
        } else {}
    }

    function buildHTML(alldata) {
        var html = '';
        var count = 0;
        $(".restaurant_counts").text('{{ trans('lang.stores') }} (' + alldata.length + ')');

        alldata.sort((a, b) => b.isOpen - a.isOpen);
        alldata.forEach((listval) => {
            var val = listval;
            if (val.vendorID != '' && val.title != '') {
                count++;
                if (count == 1) {
                    html = html + '<div class="row">';
                }
                productStoreImage = val.photo;
                productStoreTitle = val.title;
                var view_vendor_details = "{{ route('restaurant', ':id') }}";
                view_vendor_details = view_vendor_details.replace(':id', 'id=' + val.id);
                var rating = 0;
                var reviewsCount = 0;
                
                var status = val.isOpen ? "{{ trans('lang.open') }}" : "{{ trans('lang.closed') }}";
                var statusclass = val.isOpen ? "open" : "closed";
                var statusclass2 = val.isOpen ? "" : "store-closed";
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.reviewsSum != '' && val.reviewsSum != null && val.hasOwnProperty(
                        'reviewsCount') && val.reviewsCount != 0 && val.reviewsCount != '' && val.reviewsCount != null) {
                    rating = (val.reviewsSum / val.reviewsCount);
                    reviewsCount = val.reviewsCount;
                    rating = Math.round(rating * 10) / 10;
                    rating = parseInt(rating);
                }
                if (productStoreImage == '' && productStoreImage == null) {
                    productStoreImage = placeholderImage;
                }
                var ratinghtml = '<ul class="rating-stars list-unstyled"><li>';
                if (rating >= 1) {
                    ratinghtml = ratinghtml + '<i class="feather-star star_active"></i>';
                } else {
                    ratinghtml = ratinghtml + '<i class="feather-star"></i>';
                }
                if (rating >= 2) {
                    ratinghtml = ratinghtml + '<i class="feather-star star_active"></i>';
                } else {
                    ratinghtml = ratinghtml + '<i class="feather-star"></i>';
                }
                if (rating >= 3) {
                    ratinghtml = ratinghtml + '<i class="feather-star star_active"></i>';
                } else {
                    ratinghtml = ratinghtml + '<i class="feather-star"></i>';
                }
                if (rating >= 4) {
                    ratinghtml = ratinghtml + '<i class="feather-star star_active"></i>';
                } else {
                    ratinghtml = ratinghtml + '<i class="feather-star"></i>';
                }
                if (rating == 5) {
                    ratinghtml = ratinghtml + '<i class="feather-star star_active"></i>';
                } else {
                    ratinghtml = ratinghtml + '<i class="feather-star"></i>';
                }
                ratinghtml = ratinghtml + '</li></ul>';
                html = html +
                    '<div class="col-md-3 pb-3"><div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm"><div class="list-card-image ' + statusclass2 + '">';
                html = html +
                    '<div class="star position-absolute"><span class="badge badge-success"><i class="feather-star"></i>' +
                    rating + ' (' + reviewsCount + '+)</span></div>';
                html = html + '<div class="member-plan position-absolute"><span class="badge badge-dark ' +
                    statusclass + '">' + status + '</span></div><div class=""><div class="offer-icon position-absolute free-delivery-' + val.id + '"></div><a href="' + view_vendor_details +
                    '"><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="#" src="' +
                    productStoreImage + '" class="img-fluid item-img w-100"></a></div>';
                html = html + '</div>';
                html = html + '<div class="p-3 position-relative">';
                html = html + '<div class="list-card-body" ><h6 class="mb-1"><a href="' + view_vendor_details +
                    '" class="text-black">' + productStoreTitle +
                    '</a></h6><p class="text-gray mb-3"><span class="fa fa-map-marker"></span> ' + val
                    .location + '</p>';
                if (!checkIfStoreIsOpen(val)) {
                    let nextOpenTime = getStoreNextOpeningTime(val);
                    html=html+'<p class="text-gray mb-1 small text-danger"><span class="fa fa-clock-o"></span> '+nextOpenTime+'</p>';
                }
                html = html + '' + ratinghtml + '</div>';
                html = html + '</div></div></div>';
                if (count == 4) {
                    html = html + '</div>';
                    count = 0;
                }
            }
            checkSelfDeliveryForVendor(val.id);

        });
        return html;
    }

    function buildProductHTML(allProductdata) {
        var html = '';
        var count = 0;
        $(".products_counts").text('{{ trans('lang.products') }} (' + allProductdata.length + ')');
        if (allProductdata != undefined && allProductdata != '') {
            $('#myTab2').show();
            allProductdata.forEach((listval) => {
                count++;
                var val = listval;
                if (count == 1) {
                    html = html + '<div class="row">';
                }
                var product_id_single = val.id;
                var view_product_details = "{{ route('productDetail', ':id') }}";
                view_product_details = view_product_details.replace(':id', product_id_single);
                var rating = 0;
                var reviewsCount = 0;
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.reviewsSum != null && val.reviewsSum != '' && val.hasOwnProperty(
                        'reviewsCount') && val.reviewsCount != 0 && val.reviewsCount != null && val.reviewsCount != '') {
                    rating = (val.reviewsSum / val.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = val.reviewsCount;
                }
                html = html +
                    '<div class="col-md-3 product-list"><div class="list-card position-relative"><div class="list-card-image">';
                if (val.photo != "" && val.photo != null) {
                    photo = val.photo;
                } else {
                    photo = placeholderImage;
                }
                html = html + '<a href="' + view_product_details +
                    '"><img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="#" src="' +
                    photo +
                    '" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body position-relative"><h6 class="mb-1"><a href="' +
                    view_product_details + '" class="arv-title">' + val.name + '</a></h6>';
                let final_price = priceData[val.id];

                if (val.disPrice && val.disPrice !== '0' && !val.item_attribute) {
                    let or_price = getProductFormattedPrice(parseFloat(final_price.price));
                    let dis_price = getProductFormattedPrice(parseFloat(final_price.dis_price));
                    html = html + '<span class="text-gray mb-0 pro-price ">' + dis_price + '  <s>' + or_price +
                        '</s></span>';
                } else if (val.item_attribute && val.item_attribute.variants?.length > 0) {
                    let variantPrices = val.item_attribute.variants.map(v => v.variant_price);
                    let minPrice = Math.min(...variantPrices);
                    let maxPrice = Math.max(...variantPrices);
                    let or_price = minPrice !== maxPrice ?
                        `${getProductFormattedPrice(final_price.min)} - ${getProductFormattedPrice(final_price.max)}` :
                        getProductFormattedPrice(final_price.max);
                    html = html + '<span class="text-gray mb-0 pro-price ">' + or_price + '</span>';
                } else {
                    let or_price = getProductFormattedPrice(final_price.price);
                    html = html + '<span class="text-gray mb-0 pro-price ">' + or_price + '</span>';
                }
                html = html +
                    '<div class="star position-relative"><span class="badge badge-success "><i class="feather-star"></i>' +
                    rating + ' (' + reviewsCount + ')</span></div>';
                html = html + '</div>';
                html = html + '</div></div></div>';
                if (count == 4) {
                    html = html + '</div>';
                    count = 0;
                }
            });
            html = html + '</div>';
        }
        return html;
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

    function checkSelfDeliveryForVendor(vendorId) {
        setTimeout(function() {
            database.collection('vendors').doc(vendorId).get().then(async function(snapshots) {
                if (snapshots.exists) {
                    var data = snapshots.data();
                    if (data.hasOwnProperty('isSelfDelivery') && data.isSelfDelivery != null && data.isSelfDelivery != '') {
                        if (data.isSelfDelivery && isSelfDeliveryGlobally) {
                            console.log(vendorId)
                            $('.free-delivery-' + vendorId).html('<span><img src="{{ asset('img/free_delivery.png') }}" width="100px"> {{trans("lang.free_delivery")}}</span>');
                        }
                    }
                }
            })
        }, 3000);
    }
    
</script>

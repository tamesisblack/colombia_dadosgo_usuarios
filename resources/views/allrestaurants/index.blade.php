@include('layouts.app')
@include('layouts.header')
<div class="d-none">
    <div class="bg-primary p-3 d-flex align-items-center">
        <a class="toggle togglew toggle-2" href="#"><span></span></a>
    </div>
</div>
<div class="siddhi-popular">
    <div class="container">
        <div class="search py-5">
            <div class="input-group mb-4"></div>
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active border-0 bg-light text-dark rounded" id="home-tab" data-toggle="tab"
                        href="#home" role="tab" aria-controls="home" aria-selected="true"><i
                            class="feather-home mr-2"></i><span class="restaurant_counts">{{trans('lang.all_stores')}}</span></a>
                </li>
            </ul>
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="container mt-4 mb-4 p-0">
                        <div id="all_stores" class="res-search-list-1"></div>
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
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script type="text/javascript">
    var firestore=firebase.firestore();
    var geoFirestore=new GeoFirestore(firestore);
    var placeholderImage='';
    var placeholder=database.collection('settings').doc('placeHolderImage');
    placeholder.get().then(async function(snapshotsimage) {
        var placeholderImageData=snapshotsimage.data();
        placeholderImage=placeholderImageData.image;
    })
    var end=null;
    var endarray=[];
    var start=null;
    var vendorsref=database.collection('vendors');
    var currentDate=new Date();
    var expiresVendor=database.collection('vendors').where("subscriptionExpiryDate","<",currentDate);
    var RestaurantNearBy='';
    var DriverNearByRef=database.collection('settings').doc('RestaurantNearBy');
    var pagesize=12;
    var nearestRestauantRefnew='';
    var append_list='';
    var placeholderImageRef=database.collection('settings').doc('placeHolderImage');
    var placeholderImageSrc='';
    var all_stores=document.getElementById('all_stores');
    all_stores.innerHTML='';
    var inValidVendors = new Set();
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
    placeholderImageRef.get().then(async function(placeholderImageSnapshots) {
        var placeHolderImageData=placeholderImageSnapshots.data();
        placeholderImageSrc=placeHolderImageData.image;
    })
    var radiusUnit = 'km';
    callRestaurant();
    async function callRestaurant() {
        if(address_lat==''||address_lng==''||address_lng==NaN||address_lat==NaN||address_lat==null||address_lng==null) {
            return false;
        }
        DriverNearByRef.get().then(async function(DriverNearByRefSnapshots) {
            var DriverNearByRefData=DriverNearByRefSnapshots.data();
            RestaurantNearBy=parseInt(DriverNearByRefData.radios);
            address_lat=parseFloat(address_lat);
            address_lng=parseFloat(address_lng);
            radiusUnit=DriverNearByRefData.distanceType;
           
            if (radiusUnit == 'miles') {
                RestaurantNearBy = parseInt(RestaurantNearBy * 1.60934)
            }
            getNearestRestaurants();
        })
    }
    async function getNearestRestaurants() {
       // Retrieve all invalid vendors

        await checkVendors().then(expiredStores => {
           inValidVendors=expiredStores;
        });
        
        if(RestaurantNearBy) {
            nearestRestauantRefnew=geoFirestore.collection('vendors').near({
                center: new firebase.firestore.GeoPoint(address_lat,address_lng),
                radius: RestaurantNearBy
            }).where('zoneId','==',user_zone_id);
        } else {
            nearestRestauantRefnew=geoFirestore.collection('vendors').where('zoneId','==',user_zone_id);
        }
        <?php if (isset($_GET['dinein']) && @$_GET['dinein'] == 1) { ?>
        nearestRestauantRefnew=nearestRestauantRefnew.where('enabledDiveInFuture','==',true).get();
        <?php } else { ?>
        nearestRestauantRefnew=nearestRestauantRefnew.get();
        <?php } ?>
        nearestRestauantRefnew.then(async function(nearestRestauantSnapshot) {
            if(nearestRestauantSnapshot.docs.length>0) {
                var popularRestauranthtml=buildHTMLNearestRestaurant(nearestRestauantSnapshot);
                start=nearestRestauantSnapshot.docs[nearestRestauantSnapshot.docs.length-1];
                endarray.push(nearestRestauantSnapshot.docs[0]);
                all_stores.innerHTML=popularRestauranthtml;
            } else {
                all_stores.innerHTML="<h5 class='font-weight-bold text-center mt-3'>{{trans('lang.no_results')}}</h5>";
            }
            jQuery("#data-table_processing").hide();
        })
    }
    sortArrayOfObjects=(arr,key) => {
        return arr.sort((a,b) => {
            return a[key]-b[key];
        });
    };
    function buildHTMLNearestRestaurant(nearestRestauantSnapshot) {
        var html='';
        var alldata=[];
        nearestRestauantSnapshot.docs.forEach((listval) => {
            var datas=listval.data();
            datas.id=listval.id;
            datas.isOpen = checkIfStoreIsOpen(datas);  
            var rating=0;
            var reviewsCount=0;
            if('<?php echo @$_GET['popular'] && @$_GET['popular'] == "yes" ?>'&&!inValidVendors.has(listval.id)) {
                if(datas.hasOwnProperty('reviewsSum')&&datas.reviewsSum!=0 && datas.reviewsSum!=null && datas.reviewsSum!='' &&datas.hasOwnProperty('reviewsCount')&&datas.reviewsCount!=0  &&datas.reviewsCount!=null  &&datas.reviewsCount!='') {
                    rating=(datas.reviewsSum/datas.reviewsCount);
                    rating=Math.round(rating*10)/10;
                }
                datas.rating=rating;
                alldata.push(datas);
            } else {
                if(!inValidVendors.has(listval.id)) {
                    alldata.push(datas);
                }
            }
        });
        if('<?php echo @$_GET['popular'] && @$_GET['popular'] == "yes" ?>') {
            if(alldata.length) {
                alldata=sortArrayOfObjects(alldata,"rating");
                alldata=alldata.reverse();
            }
            $('.restaurant_counts').text('{{trans('lang.popular_restaurant_store')}}');
        }
        alldata.sort((a, b) => b.isOpen - a.isOpen);

        var count=0;
        var popularFoodCount=0;
        if(alldata.length) {
            html=html+'<div class="row">';
            alldata.forEach((listval) => {
                var val=listval;
                var checkDineinPlan=true;
                 <?php if (isset($_GET['dinein']) && @$_GET['dinein'] == 1) { ?>
                    if(val.hasOwnProperty('subscription_plan')) {
                        if(val.subscription_plan.hasOwnProperty('features')) {
                            checkDineinPlan=val.subscription_plan.features.dineIn
                        }
                    }
               <?php } ?>
                if(<?php echo isset($_GET['dinein']) && $_GET['dinein'] == 1 ? 'checkDineinPlan' : 'true'; ?>) {
                    var rating=0;
                    var reviewsCount=0;
                    if(val.hasOwnProperty('reviewsSum')&&val.reviewsSum!=0 && val.reviewsSum!=null && val.reviewsSum!='' &&val.hasOwnProperty('reviewsCount')&&val.reviewsCount!=0 && val.reviewsCount!=null && val.reviewsCount!='') {
                        rating=(val.reviewsSum/val.reviewsCount);
                        rating=Math.round(rating*10)/10;
                        reviewsCount=val.reviewsCount;
                    }
                    
                    var status = val.isOpen ? "{{ trans('lang.open') }}" : "{{ trans('lang.closed') }}";
                    var statusclass = val.isOpen ? "open" : "closed";
                    var statusclass2 = val.isOpen ? "" : "store-closed";
                    
                    var vendor_id_single=val.id;
                    <?php if (isset($_GET['dinein']) && @$_GET['dinein'] == 1) { ?>
                    var view_vendor_details="{{ route('dyiningrestaurant', ':id')}}";
                    <?php } else { ?>
                    var view_vendor_details="{{ route('restaurant', ':id')}}";
                    <?php } ?>
                    view_vendor_details=view_vendor_details.replace(':id','id='+vendor_id_single);
                    count++;
                    html=html+'<div class="col-md-3 pb-3"><div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm"><div class="list-card-image ' + statusclass2 + '">';
                    if(val.photo!=""&&val.photo!=null) {
                        photo=val.photo;
                    } else {
                        photo=placeholderImageSrc;
                    }
                    html=html+'<div class="member-plan position-absolute"><span class="badge badge-dark '+statusclass+'">'+status+'</span></div><div class="offer-icon position-absolute free-delivery-'+val.id+'"></div><a href="'+view_vendor_details+'"><img onerror="this.onerror=null;this.src=\''+placeholderImage+'\'" alt="#" src="'+photo+'" class="img-fluid item-img w-100"></a></div><div class="p-3 position-relative"><div class="list-card-body"><h6 class="mb-1"><a href="'+view_vendor_details+'" class="text-black">'+val.title+'</a></h6>';
                    html=html+'<p class="text-gray mb-1 small"><span class="fa fa-map-marker"></span> '+val.location+'</p>';

                    if (!checkIfStoreIsOpen(val)) {
                        let nextOpenTime = getStoreNextOpeningTime(val);
                        html=html+'<p class="text-gray mb-1 small text-danger"><span class="fa fa-clock-o"></span> '+nextOpenTime+'</p>';
                    }
                    
                    html=html+'<div class="star position-relative mt-3"><span class="badge badge-success"><i class="feather-star"></i>'+rating+' ('+reviewsCount+')</span></div>';
                    html=html+'</div>';
                    html=html+'</div></div></div>';
                }
                checkSelfDeliveryForVendor(val.id);
            });
            html=html+'</div>';
        } else {
            html=html+"<h5 class='font-weight-bold text-center mt-3'>{{trans('lang.no_results')}}</h5>";
        }
        return html;
    }
    async function moreload() {
        if(start!=undefined||start!=null) {
            jQuery("#data-table_processing").hide();
            listener=nearestRestauantRefnew.startAfter(start).limit(pagesize).get();
            listener.then(async (snapshots) => {
                html='';
                html=await buildHTMLNearestRestaurant(snapshots);
                jQuery("#data-table_processing").hide();
                if(html!='') {
                    all_stores.innerHTML+=html;
                    start=snapshots.docs[snapshots.docs.length-1];
                    if(endarray.indexOf(snapshots.docs[0])!=-1) {
                        endarray.splice(endarray.indexOf(snapshots.docs[0]),1);
                    }
                    endarray.push(snapshots.docs[0]);
                    if(snapshots.docs.length<pagesize) {
                        jQuery("#loadmore").hide();
                    } else {
                        jQuery("#loadmore").show();
                    }
                }
            });
        }
    }
    async function prev() {
        if(endarray.length==1) {
            return false;
        }
        end=endarray[endarray.length-2];
        if(end!=undefined||end!=null) {
            jQuery("#data-table_processing").show();
            listener=ref.startAt(end).limit(pagesize).get();
            listener.then(async (snapshots) => {
                html='';
                html=await buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if(html!='') {
                    append_list.innerHTML=html;
                    start=snapshots.docs[snapshots.docs.length-1];
                    endarray.splice(endarray.indexOf(endarray[endarray.length-1]),1);
                    if(snapshots.docs.length<pagesize) {
                        jQuery("#users_table_previous_btn").hide();
                    }
                }
            });
        }
    }
     function checkSelfDeliveryForVendor(vendorId){
        setTimeout(function() {
        database.collection('vendors').doc(vendorId).get().then(async function(snapshots){
            if(snapshots.exists){
                var data=snapshots.data();
                if(data.hasOwnProperty('isSelfDelivery') && data.isSelfDelivery!=null && data.isSelfDelivery!=''){
                    if(data.isSelfDelivery && isSelfDeliveryGlobally){
                        $('.free-delivery-'+vendorId).html('<span><img src="{{asset('img/free_delivery.png')}}" width="100px" > {{trans("lang.free_delivery")}}</span>');
                    }
                }
            }
        })
        }, 3000);
    }
    
</script>
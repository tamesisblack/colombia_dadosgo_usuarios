<div id="data-table_processing" class="page-overlay" style="display:none;">
    <div class="overlay-text">
        <img src="{{ asset('img/spinner.gif') }}">
    </div>
</div>
<button type="button" id="locationModal" data-toggle="modal" data-target="#locationModalAddress" hidden>{{trans('lang.submit')}}</button>
<div class="modal fade" id="locationModalAddress" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered location_modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title locationModalTitle">{{ trans('lang.delivery_address') }}</h5>
            </div>
            <div class="modal-body">
                <form class="">
                    <div class="form-row">
                        <div class="col-md-12 form-group">
                            <label class="form-label">{{ trans('lang.street_1') }}</label>
                            <div class="input-group">
                                <input placeholder="Delivery Area" type="text" id="address_line1" class="form-control">
                                <div class="input-group-append">
                                    <button onclick="getCurrentLocationAddress1()" type="button" class="btn btn-outline-secondary"><i class="feather-map-pin"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.landmark') }}</label><input placeholder="{{ trans('lang.footer') }}" value="" id="address_line2" type="text" class="form-control"></div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.zip_code') }}</label><input placeholder="Zip Code" id="address_zipcode" type="text" class="form-control">
                        </div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.city') }}</label><input placeholder="{{ trans('lang.city') }}" id="address_city" type="text" class="form-control"></div>
                        <div class="col-md-12 form-group"><label class="form-label">{{ trans('lang.country') }}</label><input placeholder="Country" id="address_country" type="text" class="form-control">
                        </div>
                        <input type="hidden" name="address_lat" id="address_lat">
                        <input type="hidden" name="address_lng" id="address_lng">
                    </div>
                </form>
            </div>
            <div class="modal-footer p-0 border-0">
                <div class="col-12 m-0 p-0">
                    <button type="button" id="close_button" class="close" data-dismiss="modal" aria-label="Close" hidden>
                        <button type="button" class="btn btn-primary btn-lg btn-block" onclick="saveShippingAddress()">{{ trans('lang.save_changes') }}
                        </button>
                </div>
            </div>
        </div>
    </div>
</div>
<span style="display: none;">
    <button type="button" class="btn btn-primary" id="notification_accepted_order_by_restaurant_id" data-toggle="modal" data-target="#notification_accepted_order_by_restaurant">{{ trans('lang.large_modal') }}</button>
</span>
<div class="modal fade" id="notification_accepted_order_by_restaurant" tabindex="-1" role="dialog" aria-labelledby="notification_accepted_order_by_restaurant" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered notification-main" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title order_accepted_subject" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><span id="restaurnat_name"></span></h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"><a href="{{ url('my_order') }}" id="notification_accepted_order_by_restaurant_a">{{ trans('lang.go') }}</a>
                </button>
            </div>
        </div>
    </div>
</div>
<span style="display: none;">
    <button type="button" class="btn btn-primary" id="notification_rejected_order_by_restaurant_id" data-toggle="modal" data-target="#notification_rejected_order_by_restaurant">{{ trans('lang.large_modal') }}</button>
</span>
<div class="modal fade" id="notification_rejected_order_by_restaurant" tabindex="-1" role="dialog" aria-labelledby="notification_accepted_order_by_restaurant" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered notification-main" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title restaurant_rejected_order" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><span id="restaurnat_name_1"></span></h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"><a href="{{ url('my_order') }}?activeTab=rejected" id="notification_rejected_order_by_restaurant_a">{{ trans('lang.go') }}</a>
                </button>
            </div>
        </div>
    </div>
</div>
<span style="display: none;">
    <button type="button" class="btn btn-primary" id="notification_accepted_order_id" data-toggle="modal" data-target="#notification_accepted_order">{{ trans('lang.large_modal') }}</button>
</span>
<div class="modal fade" id="notification_accepted_order" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered notification-main" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title driver_accepted_subject" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><span id="np_accept_name"></span></h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"><a href="{{ url('my_order') }}" id="notification_accepted_a">{{ trans('lang.go') }}</a>
                </button>
            </div>
        </div>
    </div>
</div>
<span style="display: none;">
    <button type="button" class="btn btn-primary" id="notification_order_complete_id" data-toggle="modal" data-target="#notification_order_complete">{{ trans('lang.large_modal') }}</button>
</span>
<div class="modal fade" id="notification_order_complete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered notification-main" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title order_completed_subject" id="exampleModalLongTitle">
                    {{ trans('lang.order_completed') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6 id="order_completed_msg"></h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"><a href="{{ url('my_order') }}?activeTab=completed" id="">{{ trans('lang.go') }}</a></button>
            </div>
        </div>
    </div>
</div>
<span style="display: none;">
    <button type="button" class="btn btn-primary" id="notification_accepted_dining_by_restaurant_id" data-toggle="modal" data-target="#notification_accepted_dining_by_restaurant">{{ trans('lang.large_modal') }}</button>
</span>
<div class="modal fade" id="notification_accepted_dining_by_restaurant" tabindex="-1" role="dialog" aria-labelledby="notification_accepted_dining_by_restaurant" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered notification-main" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title dinein_accepted" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><span id="restaurnat_name_dining"></span></h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"><a href="{{ url('my_dinein') }}" id="notification_accepted_dining_by_restaurant_a">{{ trans('lang.go') }}</a>
                </button>
            </div>
        </div>
    </div>
</div>
<span style="display: none;">
    <button type="button" class="btn btn-primary" id="notification_rejected_dining_by_restaurant_id" data-toggle="modal" data-target="#notification_rejected_dining_by_restaurant">{{ trans('lang.large_modal') }}</button>
</span>
<div class="modal fade" id="notification_rejected_dining_by_restaurant" tabindex="-1" role="dialog" aria-labelledby="notification_rejected_dining_by_restaurant" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered notification-main" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title dinein_rejected" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><span id="restaurnat_name_dining_rejected"></span>
                </h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"><a href="{{ url('my_dinein') }}" id="notification_rejected_dining_by_restaurant_a">{{ trans('lang.go') }}</a>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="error-container" id="error-container"></div>
<footer class="section-footer border-top bg-dark">
    <div class="footerTemplate"></div>
</footer>
<script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/sidebar/hc-offcanvas-nav.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/slick/slick.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/slick/slick-lightbox.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/siddhi.js') }}"></script>
<script src="{{ asset('js/jquery.resizeImg.js') }}"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<!-- jQuery UI JavaScript -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
<script type="text/javascript">
    <?php $id = null;
    if (Auth::user()) {
        $id = Auth::user()->getvendorId();
    } ?>
    var cuser_id = '<?php echo $id; ?>';
    var dine_in_enable = false;
    var place = [];
    var address_name = getCookie('address_name');
    var address_name1 = getCookie('address_name1');
    var address_name2 = getCookie('address_name2');
    var address_zip = getCookie('address_zip');
    var address_lat = getCookie('address_lat');
    var address_lng = getCookie('address_lng');
    var address_city = getCookie('address_city');
    var address_state = getCookie('address_state');
    var address_country = getCookie('address_country');
    var database = firebase.firestore();
    var googleMapKey = '';
    var mapType = '';
    var type = '';
    var mapTypeDoc = database.collection('settings').doc('DriverNearBy');
    mapTypeDoc.get().then(async function(snapshots) {
        var mapTypeData = snapshots.data();
        mapType = mapTypeData.selectedMapType;
    })
    if (cuser_id && cuser_id.trim() !== '') {
        database.collection('users').doc(cuser_id).onSnapshot(function(doc) {
            if (!doc.exists) {
                console.log("User document not found, logging out...");
                firebase.auth().signOut().then(() => {
                    event.preventDefault();
                    document.getElementById('logout-form').submit();
                }).catch((error) => {
                    console.error("Error during sign out:", error);
                });
            }
        });
    }
    async function showAlertMessage(message, color, timeInSec) {
        const errorContainer = document.getElementById('error-container');
        const alert = document.createElement('div');
        alert.classList.add('alert-custom');
        alert.style.backgroundColor = color || 'red';
        alert.innerHTML = message;
        const closeButton = document.createElement('button');
        closeButton.innerHTML = '×';
        closeButton.onclick = () => errorContainer.removeChild(alert);
        alert.appendChild(closeButton);
        errorContainer.appendChild(alert);
        setTimeout(() => {
            alert.style.animation = 'fadeOut 0.5s forwards';
            setTimeout(() => errorContainer.removeChild(alert), 500);
        }, timeInSec * 1000);
    }
    async function loadGoogleMapsScript() {
        await database.collection('settings').doc("googleMapKey").get().then(function(googleMapKeySnapshotsHeader) {
            var placeholderImageHeaderData = googleMapKeySnapshotsHeader.data();
            googleMapKey = placeholderImageHeaderData.key;
            const script = document.createElement('script');
            if (mapType == 'google') {
                script.src = "https://maps.googleapis.com/maps/api/js?key=" + googleMapKey +
                    "&libraries=places";
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            } else {
                script.src = "https://unpkg.com/leaflet/dist/leaflet.js";
                document.head.appendChild(script);
            }
            script.onload = function() {
                if (mapType == 'google') {
                    initialize();
                } else {
                    init();
                }
            }
        });
    }
    loadGoogleMapsScript();

    function setAddressCookies(place) {
        
        address_name = place.name || place.formatted_address;
        address_lat = place.geometry.location.lat();
        address_lng = place.geometry.location.lng();

        $.each(place.address_components, function(i, address_component) {
            address_name1 = '';
            if (address_component.types[0] == "premise") {
                if (address_name1 == '') {
                    address_name1 = address_component.long_name;
                } else {
                    address_name2 = address_component.long_name;
                }
            } else if (address_component.types[0] == "postal_code") {
                address_zip = address_component.long_name;
            } else if (address_component.types[0] == "locality") {
                address_city = address_component.long_name;
            } else if (address_component.types[0] == "administrative_area_level_1") {
                address_state = address_component.long_name;
            } else if (address_component.types[0] == "country") {
                address_country = address_component.long_name;
            }
        });
        
        <?php if (@Route::current()->getName() != 'checkout') { ?>
            setCookie('address_name1', address_name1, 365);
            setCookie('address_name2', address_name2, 365);
            setCookie('address_name', address_name, 365);
            setCookie('address_lat', address_lat, 365);
            setCookie('address_lng', address_lng, 365);
            setCookie('address_zip', address_zip, 365);
            setCookie('address_city', address_city, 365);
            setCookie('address_state', address_state, 365);
            setCookie('address_country', address_country, 365);
        <?php } ?>
        window.location.reload();
    }

    function initialize() {

        var inputIds = ['user_locationnew', 'user_locationnew_mobile'];
        inputIds.forEach(function(id) {
            var el = document.getElementById(id);
            if (el && typeof address_name !== 'undefined' && address_name != '') {
                el.value = address_name;
            }
        });

         // Desktop autocomplete
        var desktopInput = document.getElementById('user_locationnew');
        if (desktopInput) {
            var desktopAutocomplete = new google.maps.places.Autocomplete(desktopInput);
            google.maps.event.addListener(desktopAutocomplete, 'place_changed', function() {
                var place = desktopAutocomplete.getPlace();
                if (!place.geometry) return;
                setAddressCookies(place);
            });
        }

        // Mobile autocomplete
        var mobileInput = document.getElementById('user_locationnew_mobile');
        if (mobileInput) {
            var mobileAutocomplete = new google.maps.places.Autocomplete(mobileInput);
            google.maps.event.addListener(mobileAutocomplete, 'place_changed', function() {
                var place = mobileAutocomplete.getPlace();
                if (!place.geometry) return;
                setAddressCookies(place);
            });
        }
    }

    function init() {
      
        var inputIds = ['user_locationnew', 'user_locationnew_mobile'];
        inputIds.forEach(function(id) {
            var el = document.getElementById(id);
            if (el && typeof address_name !== 'undefined' && address_name != '') {
                el.value = address_name;
            }
        });

        function getPlaceSuggestions(query) {
            return $.ajax({
                url: `https://nominatim.openstreetmap.org/search?format=json&q=${query}`,
                dataType: 'json'
            });
        }

        // Autocomplete setup
        $('#user_locationnew, #user_locationnew_mobile').autocomplete({
            source: function(request, response) {
                getPlaceSuggestions(request.term).done(function(data) {
                    response(data.map(place => ({
                        label: place.display_name,
                        lat: place.lat,
                        lon: place.lon,
                        address: place.address || {}
                    })));
                });
            },
            select: function(event, ui) {
                window.location.reload(true);
                var address_name = ui.item.label;
                var address_lat = ui.item.lat;
                var address_lng = ui.item.lon;
                var address = ui.item.address || {}; // Default to empty object if address is undefined
                // Extract address components from the selected place
                var address_name1 = ui.item.address.road || '';
                var address_name2 = ui.item.address.neighbourhood || ui.item.address.suburb || '';
                var address_zip = ui.item.address.postcode || '';
                var address_city = ui.item.address.city || ui.item.address.town || ui.item.address
                    .village || '';
                var address_state = ui.item.address.state || '';
                var address_country = ui.item.address.country || '';
                // Set the cookies for the selected address details
                setCookie('address_name1', address_name1, 365);
                setCookie('address_name2', address_name2, 365);
                setCookie('address_name', address_name, 365);
                setCookie('address_lat', address_lat, 365);
                setCookie('address_lng', address_lng, 365);
                setCookie('address_zip', address_zip, 365);
                setCookie('address_city', address_city, 365);
                setCookie('address_state', address_state, 365);
                setCookie('address_country', address_country, 365);
            }
        });
    }

    <?php if (@Route::current()->getName() == 'checkout') { ?>

    setTimeout(() => {
        initializeCheckout();
    }, 4000);

    function initializeCheckout() {
        if (address_name != '') {
            document.getElementById('address_line1').value = address_name;
        }
        var input2 = document.getElementById('address_line1');
        if (mapType == 'google') {
            autocomplete2 = new google.maps.places.Autocomplete(input2);
            google.maps.event.addListener(autocomplete2, 'place_changed', function() {
                var place = autocomplete2.getPlace();
                address_name = place.name;
                address_lat = place.geometry.location.lat();
                address_lng = place.geometry.location.lng();
                $.each(place.address_components, function(i, address_component) {
                    address_name1 = '';
                    if (address_component.types[0] == "premise") {
                        if (address_name1 == '') {
                            address_name1 = address_component.long_name;
                        } else {
                            address_name2 = address_component.long_name;
                        }
                        if (address_name2 == null) {
                            address_name2 = '';
                        }
                    } else if (address_component.types[0] == "postal_code") {
                        address_zip = address_component.long_name;
                    } else if (address_component.types[0] == "locality") {
                        address_city = address_component.long_name;
                    } else if (address_component.types[0] == "administrative_area_level_1") {
                        address_state = address_component.long_name;
                    } else if (address_component.types[0] == "country") {
                        address_country = address_component.long_name;
                    }
                });
                $("#address_line1").val(address_name1);
                $("#address_line2").val(address_name2);
                $("#address_lat").val(address_lat);
                $("#address_lng").val(address_lng);
                $("#address_city").val(address_city);
                $("#address_country").val(address_country);
                $("#address_zipcode").val(address_zip);
            });
        }
    }
    <?php } ?>
    async function getCurrentLocation(type = '') {
        if (mapType == 'google') {
            var geocoder = new google.maps.Geocoder();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async function(position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        var geolocation = new google.maps.LatLng(position.coords.latitude, position.coords
                            .longitude);
                        var circle = new google.maps.Circle({
                            center: geolocation,
                            radius: position.coords.accuracy
                        });
                        var location = new google.maps.LatLng(pos['lat'], pos[
                            'lng']); // turn coordinates into an object
                        geocoder.geocode({
                            'latLng': location
                        }, async function(results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                if (results.length > 0) {
                                    setLocationValue("value",results[0].formatted_address);
                                    address_name1 = '';
                                    $.each(results[0].address_components, async function(i,
                                        address_component) {
                                        address_name1 = '';
                                        if (address_component.types[0] ==
                                            "premise") {
                                            if (address_name1 == '') {
                                                address_name1 = address_component
                                                    .long_name;
                                            } else {
                                                address_name2 = address_component
                                                    .long_name;
                                            }
                                        } else if (address_component.types[0] ==
                                            "postal_code") {
                                            address_zip = address_component
                                                .long_name;
                                        } else if (address_component.types[0] ==
                                            "locality") {
                                            address_city = address_component
                                                .long_name;
                                        } else if (address_component.types[0] ==
                                            "administrative_area_level_1") {
                                            var address_state = address_component
                                                .long_name;
                                        } else if (address_component.types[0] ==
                                            "country") {
                                            var address_country = address_component
                                                .long_name;
                                        }
                                    });
                                    address_name = results[0].formatted_address;
                                    address_lat = results[0].geometry.location.lat();
                                    address_lng = results[0].geometry.location.lng();
                                    setCookie('address_name1', address_name1, 365);
                                    setCookie('address_name2', address_name2, 365);
                                    setCookie('address_name', address_name, 365);
                                    setCookie('address_lat', address_lat, 365);
                                    setCookie('address_lng', address_lng, 365);
                                    setCookie('address_zip', address_zip, 365);
                                    setCookie('address_city', address_city, 365);
                                    setCookie('address_state', address_state, 365);
                                    setCookie('address_country', address_country, 365);
                                    if (type == 'reload') {
                                        window.location.reload(true);
                                    }
                                }
                            }
                        });
                        try {
                            if (autocomplete) {
                                autocomplete.setBounds(circle.getBounds());
                            }
                        } catch (err) {}
                    },
                    function() {});
            } else {}
        } else {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                setLocationValue("innerHTML","{{trans('lang.geolocation_is_not_supported_by_this_browser')}}")
            }
            if (type == 'reload') {
                window.location.reload(true);
            }
        }
    }

    function setLocationValue(prop, val) {
        ['user_locationnew', 'user_locationnew_mobile'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el[prop] = val;
        });
    }

    function showPosition(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        fetchNearbyPlaces(latitude, longitude);
    }

    function fetchNearbyPlaces(lat, lon) {
        const lat1 = lat.toFixed(4);
        const lon1 = lon.toFixed(4);
        const url = 'https://nominatim.openstreetmap.org/reverse?lat=' + lat1 + '&lon=' + lon1 +
            '&format=json&addressdetails=1';
        $.getJSON(url, function(data) {
            if (data && data.address) {
                const placeName = data.display_name;
                setLocationValue("value",placeName);
                var address_name = placeName;
                var address_lat = lat1;
                var address_lng = lon1;
                var address = placeName || {}; // Default to empty object if address is undefined
                // Extract address components from the selected place
                var address = data.address;
                var address_city = address.city || address.town || address.village || '';
                var address_state = address.state || '';
                var address_country = address.country || '';
                var address_zip = address.postcode || '';
                var address_name1 = address.road || '';
                var address_name2 = address.neighbourhood || address.suburb || '';
                // Set the cookies for the selected address details
                setCookie('address_name1', address_name1, 365);
                setCookie('address_name2', address_name2, 365);
                setCookie('address_name', address_name, 365);
                setCookie('address_lat', address_lat, 365);
                setCookie('address_lng', address_lng, 365);
                setCookie('address_zip', address_zip, 365);
                setCookie('address_city', address_city, 365);
                setCookie('address_state', address_state, 365);
                setCookie('address_country', address_country, 365);
                if (type == 'reload') {
                    window.location.reload(true);
                }
            } else {
                console.error("Place not found.");
            }
        }).fail(function() {
            console.error("Error fetching data from Nominatim.");
        });
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                setLocationValue("innerHTML","{{trans('lang.user_denied_the_request_for_geolocation')}}");
                break;
            case error.POSITION_UNAVAILABLE:
                setLocationValue("innerHTML","{{trans('lang.location_information_is_unavailable')}}");
                break;
            case error.TIMEOUT:
                setLocationValue("innerHTML","{{trans('lang.the_request_to_get_user_location_timed_out')}}");
                break;
            case error.UNKNOWN_ERROR:
                setLocationValue("innerHTML","{{trans('lang.an_unknown_error_occurred')}}");
                break;
        }
    }

    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    const BATCH_SIZE = 100;
    const MAX_PARALLEL_BATCHES = 5;
    database.collection('settings').doc("restaurant").get().then(async function(snapshots) {
        var subscriptionSetting = snapshots.data();
        localStorage.setItem('subscriptionModel', subscriptionSetting.subscription_model);
    });
    async function checkVendors() {
        let expiredStores = new Set();
        var subscriptionModel = false;

        var subscriptionBusinessModel = database.collection('settings').doc("restaurant");
        await subscriptionBusinessModel.get().then(async function(snapshots) {
            var subscriptionSetting = snapshots.data();
            if (subscriptionSetting.subscription_model == true) {
                subscriptionModel = true;
            }
        });

        if (subscriptionModel) {

            let vendorSnapshots = await database.collection('vendors').limit(BATCH_SIZE).get();

            // Keep track of the vendor batch promises
            let batchPromises = [];

            // While there are more vendors to process
            while (!vendorSnapshots.empty) {
                const vendorPromise = processBatch(vendorSnapshots, expiredStores);
                batchPromises.push(vendorPromise);

                if (batchPromises.length >= MAX_PARALLEL_BATCHES) {
                    await Promise.race(batchPromises); // Wait for the first batch to finish
                }

                // Get the next batch of vendors
                vendorSnapshots = await database.collection('vendors')
                    .startAfter(vendorSnapshots.docs[vendorSnapshots.docs.length - 1])
                    .limit(BATCH_SIZE)
                    .get();
            }

            // Wait for any remaining promises to finish
            await Promise.all(batchPromises);
        }
        return expiredStores;
    }

    async function processBatch(vendorSnapshots, expiredStores) {
        const vendorPromises = vendorSnapshots.docs.map(vendorDoc => processVendor(vendorDoc, expiredStores));
        return Promise.all(vendorPromises); // Process all vendors in the batch in parallel
    }

    async function processVendor(vendorDoc, expiredStores) {
        const vendorId = vendorDoc.id;
        const vendorData = vendorDoc.data();

        if (vendorData.hasOwnProperty('subscription_plan') && vendorData.subscription_plan != null && vendorData
            .hasOwnProperty('subscriptionExpiryDate')) {
            if (vendorData.subscriptionExpiryDate != null) {
                const subscriptionExpiryDate = vendorData.subscriptionExpiryDate;

                // Check subscription expiry
                if (subscriptionExpiryDate && new Date(subscriptionExpiryDate.seconds * 1000) < new Date()) {
                    expiredStores.add(vendorId);
                    return; // Skip processing this vendor
                }
            }


            const orderCount = vendorData.subscriptionTotalOrders
            const orderLimit = vendorData.subscription_plan ? vendorData.subscription_plan.orderLimit : 0;

            if (orderLimit != '-1') {
                if (parseInt(orderCount) == 0) {
                    expiredStores.add(vendorId);
                }
            }
        } else {
            expiredStores.add(vendorId);
        }
    }

    var currentLocale = "{{ session()->get('locale', app()->getLocale()) }}";

    function translateFooterHtml(html) {
        if (currentLocale !== 'es' || !html) {
            return html;
        }
        return html
            .replace(/>Services</g, '>Servicios<')
            .replace(/>Delivery support</g, '>Soporte de entrega<')
            .replace(/>Contact us</g, '>Contáctanos<')
            .replace(/>Terms of use</g, '>Términos de uso<')
            .replace(/>About us</g, '>Sobre nosotros<')
            .replace(/>For users</g, '>Para usuarios<')
            .replace(/>User Login\s*</g, '>Iniciar sesión<')
            .replace(/>User Register\s*</g, '>Registrarse<')
            .replace(/>Account Setting\s*</g, '>Configuración de cuenta<')
            .replace(/>Send us mails\.\s*&nbsp;</g, '>Envíanos un correo&nbsp;<')
            .replace(/>Send us mails\.</g, '>Envíanos un correo.<')
            .replace(/>Find us here</g, '>Encuéntranos aquí<')
            .replace(/All rights reserved/g, 'Todos los derechos reservados')
            .replace(
                /Lorem Ipsum is simply dummy text of the printing and typesetting industry\. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book\./g,
                'Pide comida de tus restaurantes favoritos y recíbela en la puerta de tu casa de forma rápida y confiable.'
            );
    }

    var footerRef = database.collection('settings').doc('footerTemplate');
    footerRef.get().then(async function(snapshots) {
        var footerData = snapshots.data();
        if (footerData != undefined) {
            if (footerData.footerTemplate && footerData.footerTemplate != "" && footerData.footerTemplate !=
                undefined) {
                $('.footerTemplate').html(translateFooterHtml(footerData.footerTemplate));
            }
        }
    });
    var langcount = 0;
    var languages_list = database.collection('settings').doc('languages');
    languages_list.get().then(async function(snapshotslang) {
        snapshotslang = snapshotslang.data();
        if (snapshotslang != undefined) {
            snapshotslang = snapshotslang.list;
            languages_list_main = snapshotslang;
            var hasCurrentLocale = false;
            snapshotslang.forEach((data) => {
                if (data.isActive == true) {
                    langcount++;
                    $('.language_dropdown').append($("<option></option>").attr("value", data.slug).text(data.title));
                    if (data.slug === currentLocale) {
                        hasCurrentLocale = true;
                    }
                }
            });
            if (!hasCurrentLocale && currentLocale) {
                var currentTitle = currentLocale === 'es' ? 'Español' : currentLocale;
                $('.language_dropdown').prepend($("<option></option>").attr("value", currentLocale).text(currentTitle));
                languages_list_main = [{
                    slug: currentLocale,
                    title: currentTitle,
                    isActive: true,
                    is_rtl: false
                }].concat(languages_list_main || []);
                langcount++;
            }
            if (langcount > 1) {
                $("#language_dropdown_box").css('visibility', 'visible');
            }
            $(".language_dropdown").val(currentLocale);
        }
    });
    var url = "{{ route('changeLang') }}";
    $(".changeLang").change(function() {
        var slug = $(this).val();
        languages_list_main.forEach((data) => {
            if (slug == data.slug) {
                if (data.is_rtl == undefined) {
                    setCookie('is_rtl', 'false', 365);
                } else {
                    setCookie('is_rtl', data.is_rtl.toString(), 365);
                }
                window.location.href = url + "?lang=" + slug;
            }
        });
    });
    database.collection('settings').doc("notification_setting").get().then(async function(snapshots) {
        var data = snapshots.data();
        if (data != undefined) {
            serviceJson = data.serviceJson;
            if (serviceJson != '' && serviceJson != null) {
                $.ajax({
                    type: 'POST',
                    data: {
                        serviceJson: btoa(serviceJson),
                    },
                    url: "{{ route('store-firebase-service') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {}
                });
            }
        }
    });
    //start - Get user zone id from address
    getUserZoneId();
    var user_zone_id = null;
    async function getUserZoneId() {
        var zone_list = [];
        var snapshots = await database.collection('zone').where("publish", "==", true).get();
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
                if (is_in_polygon(points_polygon, vertices_x, vertices_y, address_lng, address_lat)) {
                    user_zone_id = zone.id;
                    break;
                }
            }
        }
    }

    function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y) {
        $i = $j = $c = $point = 0;
        for ($i = 0, $j = $points_polygon; $i < $points_polygon; $j = $i++) {
            $point = $i;
            if ($point == $points_polygon)
                $point = 0;
            if ((($vertices_y[$point] > $latitude_y != ($vertices_y[$j] > $latitude_y)) && ($longitude_x < ($vertices_x[
                    $j] - $vertices_x[$point]) * ($latitude_y - $vertices_y[$point]) / ($vertices_y[$j] -
                    $vertices_y[$point]) + $vertices_x[$point])))
                $c = !$c;
        }
        return $c;
    }
    //end - Get user zone id from address
    //start - Get product price with admin commission globally
    database.collection('settings').doc("AdminCommission").get().then(async function(snapshots) {
        var adminCommissionSettings = snapshots.data();
        localStorage.setItem('adminCommissionSettings', JSON.stringify(adminCommissionSettings));
    });
    async function getPrice(price, vendorID) {
        var final_price = price;
        if (price != null && price != "") {
            var adminCommissionSettings = localStorage.getItem('adminCommissionSettings')
            if (adminCommissionSettings != "" && adminCommissionSettings != undefined) {
                adminCommissionSettings = JSON.parse(localStorage.getItem('adminCommissionSettings'));
                if (adminCommissionSettings.isEnabled) {
                    await database.collection('vendors').where('id', '==', vendorID).get().then(async function(
                        snapshot) {
                        if (snapshot.docs.length > 0) {
                            var data = snapshot.docs[0].data();
                            if (data.hasOwnProperty('adminCommission') && data.adminCommission !=
                                null &&
                                data.adminCommission != '') {
                                if (data.adminCommission.commissionType == "Percent") {
                                    final_price = price + ((parseFloat(data.adminCommission
                                        .fix_commission) * price) / 100);
                                } else {
                                    final_price = price + parseFloat(data.adminCommission
                                        .fix_commission);
                                }
                            } else {
                                if (adminCommissionSettings.commissionType == "Percent") {
                                    final_price = price + ((parseFloat(adminCommissionSettings
                                        .fix_commission) * price) / 100);
                                } else {
                                    final_price = price + parseFloat(adminCommissionSettings
                                        .fix_commission);
                                }
                            }
                        } else {
                            if (adminCommissionSettings.commissionType == "Percent") {
                                final_price = price + ((parseFloat(adminCommissionSettings
                                    .fix_commission) * price) / 100);
                            } else {
                                final_price = price + parseFloat(adminCommissionSettings
                                    .fix_commission);
                            }
                        }
                    })
                }
            }
        }
        return final_price;
    }
    async function getFormattedPrice(price, vendorID) {
        if (price != null && price !== "") {
            let final_price = price;

            let adminCommissionSettings = localStorage.getItem('adminCommissionSettings');
            // Check if admin commission settings exist
            if (adminCommissionSettings && adminCommissionSettings !== undefined) {
                adminCommissionSettings = JSON.parse(adminCommissionSettings);
                if (adminCommissionSettings.isEnabled) {
                    // Fetch vendor data from Firestore
                    const snapshot = await database.collection('vendors').where('id', '==', vendorID).get();
                    if (snapshot.docs.length > 0) {
                        const data = snapshot.docs[0].data();
                        // Check for admin commission in the vendor data
                        if (data.hasOwnProperty('adminCommission') && data.adminCommission !== null && data
                            .adminCommission !== '') {
                            const commission = data.adminCommission;
                            if (commission.commissionType === "Percent") {
                                final_price += (parseFloat(commission.fix_commission) * price) / 100;
                            } else {
                                final_price += parseFloat(commission.fix_commission);
                            }
                        }
                    } else {
                        // Handle case where vendor data is not found
                        if (adminCommissionSettings.commissionType === "Percent") {
                            final_price += (parseFloat(adminCommissionSettings.fix_commission) * price) / 100;
                        } else {
                            final_price += parseFloat(adminCommissionSettings.fix_commission);
                        }
                    }
                }
            }
            // Format the final price based on the currency settings
            let formatted_price = currencyAtRight ?
                final_price.toFixed(decimal_degits) + "" + currentCurrency :
                currentCurrency + "" + final_price.toFixed(decimal_degits);
            return formatted_price;
        } else {
            return ''; // Return an empty string or handle case where price is not valid
        }
    }
    //end - Get product price with admin commission globally
    // Process each vendor's data and calculate the price with admin commission
    async function fetchVendorPriceData() {
        let priceData = {}; // To store price data for each vendor
        let adminCommissionSettings = localStorage.getItem('adminCommissionSettings');
        // Check if admin commission settings exist
        if (adminCommissionSettings && adminCommissionSettings !== undefined) {
            adminCommissionSettings = JSON.parse(adminCommissionSettings);
            // Fetch all vendors in parallel
            const vendorSnapshot = await database.collection('vendors').get();
            const vendorCommissions = {};
            vendorSnapshot.docs.forEach(doc => {
                vendorCommissions[doc.id] = doc.data().adminCommission || adminCommissionSettings;
            });
            const productSnapshot = await database.collection('vendor_products').get();
            const promises = productSnapshot.docs.map(doc => {
                const productData = doc.data();
                const vendorID = productData.vendorID;
                // Fetch the corresponding vendor commission
                const commissionData = vendorCommissions[vendorID] || adminCommissionSettings;
                return processVendorData(productData, commissionData);
            });
            // Wait for all promises to resolve
            const results = await Promise.all(promises);
            results.forEach(result => {
                priceData[result.productId] = result.finalPrice;
            });
        }
        return priceData;
    }
    // Process each vendor's data and calculate the price with admin commission
    async function processVendorData(productData, commissionData) {
        let final_price = parseFloat(productData.price); // Default to the base price
        let adminCommissionSettings = localStorage.getItem('adminCommissionSettings');
        if (adminCommissionSettings && adminCommissionSettings !== undefined) {
            adminCommissionSettings = JSON.parse(adminCommissionSettings);
        }
        // Handle the commission logic (if any)
        if (commissionData && adminCommissionSettings.isEnabled) {
            if (commissionData.commissionType === "Percent") {
                price = parseFloat(productData.price) + (parseFloat(productData.price) * parseFloat(commissionData
                    .fix_commission) / 100);
            } else {
                price = parseFloat(productData.price) + parseFloat(commissionData.fix_commission);
            }
        } else {
            price = parseFloat(productData.price);
        }
        final_price = {
            price: price
        };
        // Check for discount price (disPrice)

        if (productData.disPrice && productData.disPrice !== '0' && productData.disPrice !== "") {
            if (commissionData && adminCommissionSettings.isEnabled) {
                if (commissionData.commissionType === "Percent") {
                    dis_price = parseFloat(productData.disPrice) + (parseFloat(productData.disPrice) * parseFloat(
                            commissionData.fix_commission) /
                        100);
                } else {
                    dis_price = parseFloat(productData.disPrice) + parseFloat(commissionData.fix_commission);
                }
                final_price = {
                    price: price,
                    dis_price: dis_price
                };
            } else {
                final_price = {
                    price: parseFloat(productData.price),
                    dis_price: parseFloat(productData.disPrice)
                };
            }
        }
        // Check for variant prices if available

        if (productData.item_attribute && productData.item_attribute.variants?.length > 0) {
            let variantPrices = productData.item_attribute.variants.map(v => v.variant_price);
            let minPrice = Math.min(...variantPrices);
            let maxPrice = Math.max(...variantPrices);
            if (commissionData && adminCommissionSettings.isEnabled) {
                if (commissionData.commissionType === "Percent") {
                    minPrice = minPrice + (minPrice * parseFloat(commissionData.fix_commission) / 100);
                    maxPrice = maxPrice + (maxPrice * parseFloat(commissionData.fix_commission) / 100);
                } else {
                    minPrice = minPrice + parseFloat(commissionData.fix_commission);
                    maxPrice = maxPrice + parseFloat(commissionData.fix_commission);
                }
                // If variants have a range, use that
                if (minPrice !== maxPrice) {

                    final_price = {
                        min: minPrice,
                        max: maxPrice
                    };
                } else {

                    final_price = {
                        max: minPrice,
                    }; // Single price if there's no range
                }
            } else {
                if (minPrice !== maxPrice) {
                    final_price = {
                        min: minPrice,
                        max: maxPrice
                    };
                } else {
                    final_price = {
                        max: minPrice,
                    };
                }
            }
        }
        return {
            productId: productData.id,
            finalPrice: final_price
        };
    }

    function getProductFormattedPrice(price) {
        if (price != null && price != '' && price != undefined) {
            if (currencyAtRight) {
                return price.toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                return currentCurrency + "" + price.toFixed(decimal_degits);
            }
        } else {
            return currentCurrency + "" + 0;
        }
    }

    function checkIfStoreIsOpen(data) {
        var currentdate = new Date();
        var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        var currentDay = days[currentdate.getDay()];
        var hour = currentdate.getHours().toString().padStart(2, '0');
        var minute = currentdate.getMinutes().toString().padStart(2, '0');
        var currentTime = hour + ':' + minute;
        if (!data.workingHours) return false;
        if (data.hasOwnProperty('workingHours')) {
            for (let dayData of data.workingHours) {
                if (dayData.day === currentDay) {
                    for (let slot of dayData.timeslot) {
                        if (currentTime >= slot.from && currentTime <= slot.to) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    function getStoreNextOpeningTime(data) {
        const now = new Date();
        const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const currentDay = days[now.getDay()];
        const currentTime = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
        if (!data.workingHours || data.workingHours.length === 0) {
            return "{{trans('lang.closed')}}";
        }
        const todayData = data.workingHours.find(d => d.day === currentDay);
        if (todayData && todayData.timeslot.length) {
            const futureToday = todayData.timeslot
                .filter(t => t.from > currentTime)
                .sort((a, b) => a.from.localeCompare(b.from));

            if (futureToday.length) {
                return `{{trans('lang.next_available_today_at')}} ${getTimeFormat(futureToday[0].from)}`;
            }
        }
        const currentIndex = days.indexOf(currentDay);
        for (let i = 1; i <= 7; i++) {
            const nextDayIndex = (currentIndex + i) % 7;
            const nextDay = days[nextDayIndex];
            const dayData = data.workingHours.find(d => d.day === nextDay);

            if (dayData && dayData.timeslot.length) {
                const nextSlot = dayData.timeslot.sort((a, b) => a.from.localeCompare(b.from))[0];
                if (i === 1) {
                    return `{{trans('lang.next_available_tomorrow_at')}} ${getTimeFormat(nextSlot.from)}`;
                } else {
                    return `{{trans('lang.next_available')}} ${nextDay} {{trans('lang.at')}} ${getTimeFormat(nextSlot.from)}`;
                }
            }
        }
        return "{{trans('lang.closed')}}";
    }

    function getTimeFormat(time) {
        let [h, m] = time.split(":");
        h = parseInt(h);
        return (h % 12 || 12) + ":" + m + (h >= 12 ? " PM" : " AM");
    }

    function formatCurrency(amount, currency = {}) {
        const symbol = currency.symbol || '';
        const decimals = currency.decimal_degits ?? 2;
        const symbolAtRight = Boolean(currency.symbolAtRight);
        const formatted = parseFloat(amount).toFixed(decimals);
        return symbolAtRight
            ? formatted + ' ' + symbol
            : symbol + formatted;
    }

    async function getCountryFromLatLng(lat, lng) {
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        return data?.address?.country || '';
    }
   
</script>
<script type="text/javascript">
    <?php
    use App\Models\user;
    use App\Models\VendorUsers;
    
    $user_email = '';
    
    $user_uuid = '';
    
    $auth_id = Auth::id();
    
    if ($auth_id) {
        $user = user::select('email')->where('id', $auth_id)->first();
    
        $user_email = $user->email;
    
        $user_uuid = VendorUsers::select('uuid')->where('email', $user_email)->first();
    
        $user_uuid = $user_uuid->uuid;
    }
    
    ?>
    var database = firebase.firestore();
    var refDineInRestaurant = database.collection('settings').doc("DineinForRestaurant");
    refDineInRestaurant.get().then(async function(snapshotsDinein) {
        var enableddineinRestaurant = snapshotsDinein.data();
        dine_in_enable = enableddineinRestaurant.isEnabledForCustomer;
        if (dine_in_enable == true) {
            $(".dine_in_menu").show();
            $(".dine_in_menu").attr('style', 'display: block !important');
        }
    });
    var placeholderImageHeader = '';
    var googleMapKey = '';
    var googleMapKeySettingHeader = database.collection('settings').doc("googleMapKey");
    googleMapKeySettingHeader.get().then(async function(googleMapKeySnapshotsHeader) {
        var placeholderImageHeaderData = googleMapKeySnapshotsHeader.data();
        placeholderImageHeader = placeholderImageHeaderData.placeHolderImage;
        googleMapKey = placeholderImageHeaderData.key;
    });
    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
    placeholder.get().then(async function(snapshotsimage) {
        var placeholderImageData = snapshotsimage.data();
        placeholderImage = placeholderImageData.image;
    });
    var user_email = "<?php echo $user_email; ?>";
    var user_ref = '';
    var referral_ref = '';

    if (user_email != '') {

        var user_uuid = "<?php echo $user_uuid; ?>";

        user_ref = database.collection('users').where("id", "==", user_uuid);

        referral_ref = database.collection('referral').doc(user_uuid);

    }
    $(document).ready(function() {
        if (user_ref != '') {
            user_ref.get().then(async function(profileSnapshots) {
                var profile_user = profileSnapshots.docs[0].data();
                var profile_name = profile_user.firstName + " " + profile_user.lastName;
                if (profile_user.profilePictureURL != '') {
                    $("#dropdownMenuButton").append('<img onerror="this.onerror=null;this.src=\'' +
                        placeholderImage + '\'" alt="#" src="' + profile_user
                        .profilePictureURL +
                        '" class="img-fluid rounded-circle header-user mr-2 header-user">Hi ' +
                        profile_user.firstName);
                } else {
                    $("#dropdownMenuButton").append('<img alt="#" src="' + placeholderImage +
                        '" class="img-fluid rounded-circle header-user mr-2 header-user">Hi ' +
                        profile_user.firstName);
                }
                if (profile_user.hasOwnProperty('shippingAddress')) {
                    $("#user_location").html(profile_user.shippingAddress.city);
                }
            });
        }
        if (referral_ref) {
            referral_ref.get().then(async function(refSnapshot) {
                var referral_data = refSnapshot.data();
                if (referral_data != undefined && referral_data.referralCode != null &&
                    referral_data.referralCode != undefined) {
                    $(".referral_code").html("<b>{{ trans('lang.your_referral_code') }} : " +
                        referral_data.referralCode + "</b>");
                }
            })
        }
    })
    $(".user-logout-btn").click(async function() {
        firebase.auth().signOut().then(function() {
            var logoutURL = "{{ route('logout') }}";
            $.ajax({
                type: 'POST',
                url: logoutURL,
                data: {},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data1) {
                    if (data1.logoutuser) {
                        window.location = "{{ route('login') }}";
                    }
                }
            })
        });
    });
    <?php if (@$_GET['update_location'] == 1) { ?>
    var vendorsRef = database.collection('vendors');
    vendorsRef.get().then(async function(vendorsSnapshots) {
        vendorsSnapshots.forEach((doc) => {
            vendorRate = doc.data();
            if (vendorRate.g != undefined) {
                if (vendorRate.g.geopoint._longitude && vendorRate.g.geopoint._latitude) {
                    coordinates = new firebase.firestore.GeoPoint(vendorRate.g.geopoint._latitude,
                        vendorRate.g.geopoint._longitude);
                    try {
                        geoFirestore.collection('vendors').doc(vendorRate.id).update({
                            'coordinates': coordinates
                        }).then(() => {
                            console.log('Provided document has been updated in Firestore');
                        }, (error) => {
                            console.log('Error: ' + error);
                        });
                    } catch (err) {}
                }
            }
        });
    });
    <?php } ?>
</script>
<script type="text/javascript" src="{{ asset('js/rocket-loader.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('https://static.cloudflareinsights.com/beacon.min.js') }}"></script>
<?php if (Auth::user()) { ?>
<script type="text/javascript">
    var orderAcceptedSubject = '';
    var orderAcceptedMsg = '';
    var orderRejectedSubject = '';
    var orderRejectedMsg = '';
    var orderCompletedSubject = '';
    var orderCompletedMsg = '';
    var takeAwayOrderCompletedSubject = '';
    var takeAwayOrderCompletedMsg = '';
    var driverAcceptedSubject = '';
    var driverAcceptedMsg = '';
    var dineInAcceptedSubject = '';
    var dineInAcceptedMsg = '';
    var dineInRejectedSubject = '';
    var dineInRejectedMsg = '';
    var database = firebase.firestore();
    database.collection('dynamic_notification').get().then(async function(snapshot) {
        if (snapshot.docs.length > 0) {
            snapshot.docs.map(async (listval) => {
                val = listval.data();
                if (val.type == "driver_accepted") {
                    driverAcceptedSubject = val.subject;
                    driverAcceptedMsg = val.message;
                } else if (val.type == "restaurant_rejected") {
                    orderRejectedSubject = val.subject;
                    orderRejectedMsg = val.message;
                } else if (val.type == "takeaway_completed") {
                    takeAwayOrderCompletedSubject = val.subject;
                    takeAwayOrderCompletedMsg = val.message;
                } else if (val.type == "driver_completed") {
                    orderCompletedSubject = val.subject;
                    orderCompletedMsg = val.message;
                } else if (val.type == "restaurant_accepted") {
                    orderAcceptedSubject = val.subject;
                    orderAcceptedMsg = val.message;
                } else if (val.type == "dinein_accepted") {
                    dineInAcceptedSubject = val.subject;
                    dineInAcceptedMsg = val.message;
                } else if (val.type == "dinein_canceled") {
                    dineInRejectedSubject = val.subject;
                    dineInRejectedMsg = val.message;
                }
            });
        }
    });
    var route1 = '<?php echo route('my_order') ?>?activeTab=rejected';
    var pageloadded = 0;
    database.collection('restaurant_orders').where('author.id', "==", cuser_id).onSnapshot(function(doc) {
        if (pageloadded) {
            doc.docChanges().forEach(function(change) {
                var val = change.doc.data();
                if (change.type == "modified") {
                    if (val.status == "Order Completed" && val.takeAway == true || val.takeAway =='true') {
                        $('.order_completed_subject').text(takeAwayOrderCompletedSubject);
                        $('#order_completed_msg').text(takeAwayOrderCompletedMsg);
                        $("#notification_order_complete_id").trigger("click");
                    } else if (val.status == "Order Completed" && val.takeAway == false || val.takeAway == 'false') {
                        $('.order_completed_subject').text(orderCompletedSubject);
                        $('#order_completed_msg').text(orderCompletedMsg);
                        $("#notification_order_complete_id").trigger("click");
                    } else if (val.status == "Order Accepted") {
                        title = '';
                        if (val.vendor.title) {
                            title = val.vendor.title;
                        }
                        $("#restaurnat_name").text(orderAcceptedMsg);
                        $('.order_accepted_subject').text(orderAcceptedSubject);
                        if (route1) {
                            $("#notification_accepted_order_by_restaurant_a").attr("href", route1);
                        }
                        $("#notification_accepted_order_by_restaurant_id").trigger("click");
                    } else if (val.status == "Driver Accepted") {
                        var driverName = '';
                        if (val.driver && val.driver.firstName) {
                            driverName = val.driver.firstName;
                        }
                        $("#np_accept_name").text(driverAcceptedMsg);
                        $('.driver_accepted_subject').text(driverAcceptedSubject);
                        if (route1) {
                            $("#notification_accepted_a").attr("href", route1.replace(':id', val.id));
                        }
                        $("#notification_accepted_order").modal('show');
                        $("#notification_accepted_order_id").trigger("click");
                    } else if (val.status == "Order Rejected") {
                        var title = '';
                        if (val.vendor.title) {
                            title = val.vendor.title;
                        }
                        $("#restaurnat_name_1").text(orderRejectedMsg);
                        $('.restaurant_rejected_order').text(orderRejectedSubject);
                        if (route1) {
                            $("#notification_rejected_order_by_restaurant_a").attr("href", route1);
                        }
                        $("#notification_rejected_order_by_restaurant_id").trigger("click");
                    }
                }
            });
        } else {
            pageloadded = 1;
        }
    });
    var route2 = '<?php echo route('my_dinein'); ?>';
    var pageloadded_dining = 0;
    database.collection('booked_table').where('author.id', "==", cuser_id).onSnapshot(function(doc) {
        if (pageloadded_dining) {
            doc.docChanges().forEach(function(change) {
                var val = change.doc.data();
                if (change.type == "modified") {
                    if (val.status == "Order Accepted") {
                        var title = '';
                        if (val.vendor.title) {
                            title = val.vendor.title;
                        }
                        $("#restaurnat_name_dining").text(dineInAcceptedMsg);
                        $('.dinein_accepted').text(dineInAcceptedSubject);
                        if (route1) {
                            $("#notification_accepted_dining_by_restaurant_a").attr("href", route2);
                        }
                        $("#notification_accepted_dining_by_restaurant_id").trigger("click");
                    } else if (val.status == "Order Rejected") {
                        var title = '';
                        if (val.vendor.title) {
                            title = val.vendor.title;
                        }
                        $("#restaurnat_name_dining_rejected").text(dineInRejectedMsg);
                        $('.dinein_rejected').text(dineInRejectedSubject);
                        if (route1) {
                            $("#notification_rejected_dining_by_restaurant_a").attr("href", route2);
                        }
                        $("#notification_rejected_dining_by_restaurant_id").trigger("click");
                    }
                }
            });
        } else {
            pageloadded_dining = 1;
        }
    });
   
    var email_templates = database.collection('email_templates').where('type', '==', 'new_order_placed');
    var emailTemplatesData = null;
    var currentCurrency = "";
    var currencyAtRight = false;
    var decimal_degits = 0;
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

    let taxBreakdownGrouped = {
        item: {},
        order: {},
        delivery: {},
        packaging: {},
        platform: {}
    };

    async function sendMailData(orderId, userId) {
        try {
            // Wrap email sending in try-catch to prevent SMTP errors from blocking order completion
            let emailTempRef = await email_templates.get();
            let emailTemplatesData = emailTempRef.docs[0].data();

        let orderRef = await database.collection('restaurant_orders').doc(orderId).get();
        if (!orderRef.exists) return; 
        let orderDetails = orderRef.data();
        
        let userRef = await database.collection('users').doc(userId).get();
        if (!userRef.exists) return; 
        let userDetails = userRef.data();

        let orderUserName = userDetails.firstName+' '+userDetails.lastName;
        let orderUserEmail = userDetails.email;

        var order_subtotal = 0;
        var total_discount = 0;
        var total_tax_amount = 0;
        var tip_amount = parseFloat(orderDetails.tip_amount || 0);
        var deliveryCharge = parseFloat(orderDetails.deliveryCharge || 0);
        var platformFee = parseFloat(orderDetails.platformFee || 0);
        var packagingCharge = parseFloat(orderDetails.vendor.packagingCharge || 0);

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
            let itemSubtotal = order_subtotal;
            let itemCombinedTax = 0;
            orderDetails.products.forEach(product => {
                let basePrice = (product.discountPrice && parseFloat(product.discountPrice) > 0) ? parseFloat(product.discountPrice) : parseFloat(product.price);
                let itemGross = (basePrice + parseFloat(product.extras_price || 0)) * parseInt(product.quantity);
                let itemDiscount = (itemSubtotal > 0) ? (itemGross / itemSubtotal) * total_discount : 0;
                let itemTaxable = Math.max(0, itemGross - itemDiscount);
                let itemTaxes = product.taxSetting || [];
                itemTaxes.forEach(tax => {
                    if (tax.enable) {
                        let taxAmount = 0;
                        if (tax.type === "percentage") {
                            taxAmount = (tax.tax / 100) * itemTaxable;
                        } else {
                            taxAmount = tax.tax;
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
        
        //Final subtotal after discounts
        var subTotalWithDisc = formatCurrency(order_subtotal, currencyData);
        order_subtotal = order_subtotal - total_discount;
        // Final total
        var order_total = order_subtotal + deliveryCharge + tip_amount + packagingCharge + platformFee + total_tax_amount;

        var subTotalText = formatCurrency(order_subtotal, currencyData);
        var discountText = formatCurrency(order_discount, currencyData);
        var deliveryChargeText = formatCurrency(deliveryCharge, currencyData);
        var packagingChargeText = formatCurrency(packagingCharge, currencyData);
        var platformFeeText = formatCurrency(platformFee, currencyData);
        var tipAmountText = formatCurrency(tip_amount, currencyData);
        var totalAmountText = formatCurrency(order_total, currencyData);
        
        var productDetailsHtml = '';
        orderDetails.products.forEach((product) => {
            productDetailsHtml += '<tr>';
            var extra_html = '';
            var extras_price = 0;
            var basePriceValue = (product.discountPrice !== undefined &&
                      product.discountPrice !== null &&
                      parseFloat(product.discountPrice) > 0)
                    ? parseFloat(product.discountPrice)
                    : parseFloat(product.price);

            var price_item = basePriceValue.toFixed(decimal_degits);

            var totalProductPrice = parseFloat(price_item) * parseInt(product.quantity);
            if (product.extras != undefined && product.extras != '' && product.extras.length > 0) {
                var extra_count = 1;
                try {
                    var extras_price_item = (parseFloat(product.extras_price) * parseInt(product.quantity)).toFixed(decimal_degits);
                    if (parseFloat(extras_price_item) != NaN && product.extras_price != undefined) {
                        extras_price = extras_price_item;
                    }
                    product.extras.forEach((extra) => {
                        if (extra_count > 1) {
                            extra_html = extra_html + ',' + extra;
                        } else {
                            extra_html = extra_html + extra;
                        }
                        extra_count++;
                    })
                } catch (error) {}
            }
            productDetailsHtml += '<td style="width: 20%; border-top: 1px solid rgb(0, 0, 0);">';
            productDetailsHtml += product.name;
            if (extra_count > 1) {
                productDetailsHtml += '<br> {{ trans('lang.extra_item') }} : ' + extra_html;
            }
            
            var price_item = formatCurrency(price_item, currencyData);
            var extras_price = formatCurrency(extras_price, currencyData);
            var totalProductPrice = formatCurrency(totalProductPrice, currencyData);

            productDetailsHtml += '</td>';
            productDetailsHtml += '<td style="width: 20%; border: 1px solid rgb(0, 0, 0);">' + product
                .quantity + '</td><td style="width: 20%; border: 1px solid rgb(0, 0, 0);">' + price_item +
                '</td><td style="width: 20%; border: 1px solid rgb(0, 0, 0);">' + extras_price +
                '</td><td style="width: 20%; border: 1px solid rgb(0, 0, 0);">  ' + totalProductPrice +
                '</td>';
            productDetailsHtml += '</tr>';
        });

        var productHtml =
        '<table style="width: 100%; border-collapse: collapse; border: 1px solid rgb(0, 0, 0);">\n' +
        '    <thead>\n' +
        '        <tr>\n' +
        '            <th style="text-align: left; border: 1px solid rgb(0, 0, 0);">{{ trans('lang.product_name') }}<br></th>\n' +
        '            <th style="text-align: left; border: 1px solid rgb(0, 0, 0);">{{ trans('lang.quantity_plural') }}<br></th>\n' +
        '            <th style="text-align: left; border: 1px solid rgb(0, 0, 0);">{{ trans('lang.price') }}<br></th>\n' +
        '            <th style="text-align: left; border: 1px solid rgb(0, 0, 0);">{{ trans('lang.extra_item') }} {{ trans('lang.price') }}<br></th>\n' +
        '            <th style="text-align: left; border: 1px solid rgb(0, 0, 0);">{{ trans('lang.total') }}<br></th>\n' +
        '        </tr>\n' +
        '    </thead>\n' +
        '    <tbody id="productDetails">' + productDetailsHtml + '</tbody>\n' +
        '</table>';

        var specialDiscountVal = '';
        var specialDiscountAmount = 0;
        var totalAmount = 0;
        if (orderDetails.specialDiscount.specialType != '') {
            specialDiscountAmount = parseFloat(orderDetails.specialDiscount.special_discount).toFixed(2);
            if (orderDetails.specialDiscount.specialType == "percentage") {
                specialDiscountVal = orderDetails.specialDiscount.special_discount_label + '%';
            } else {
                specialDiscountVal = formatCurrency(orderDetails.specialDiscount.special_discount_label, currencyData);
            }
        }
        var specialDiscountAmountText = formatCurrency(specialDiscountAmount, currencyData);

        var shippingddress = '';
        if (orderDetails.hasOwnProperty('address')) {
            shippingddress = orderDetails.address.address;
            if (orderDetails.address.hasOwnProperty('locality') && orderDetails.address.locality != '') {
                shippingddress = shippingddress + ',' + orderDetails.address.locality;
            }
            if (orderDetails.address.hasOwnProperty('landmark') && orderDetails.address.landmark != '') {
                shippingddress = shippingddress + ' ' + orderDetails.address.landmark;
            }
        }

        let formattedDate = new Date().toLocaleDateString('en-GB');

        var subject = emailTemplatesData.subject;
        subject = subject.replace(/{orderid}/g, orderDetails.id);

        emailTemplatesData.subject = subject;
        var message = emailTemplatesData.message;

        message = message.replace(/{username}/g, orderUserName);
        message = message.replace(/{orderid}/g, orderDetails.id);
        message = message.replace(/{date}/g, formattedDate);
        message = message.replace(/{address}/g, shippingddress);
        message = message.replace(/{paymentmethod}/g, orderDetails.payment_method);
        message = message.replace(/{productdetails}/g, productHtml);
        message = message.replace(/{subtotal}/g, subTotalWithDisc);

        if (orderDetails.couponCode) {
            message = message.replace(/{coupon}/g, '(' + orderDetails.couponCode + ')');
        } else {
            message = message.replace(/{coupon}/g, "");
        }
        message = message.replace(/{discountamount}/g, discountText);
        
        if (specialDiscountVal != '') {
            message = message.replace(/{specialcoupon}/g, '(' + specialDiscountVal + ')');
        } else {
            message = message.replace(/{specialcoupon}/g, "");
        }

        message = message.replace(/{specialdiscountamount}/g, specialDiscountAmountText);
        message = message.replace(/{shippingcharge}/g, deliveryChargeText);
        message = message.replace(/{packagingcharge}/g, packagingChargeText);
        message = message.replace(/{platformcharge}/g, platformFeeText);
        message = message.replace(/{tipamount}/g, tipAmountText);

        var taxDetailsHtml = renderMailTaxSection('item', 'Tax on Item Total');
        taxDetailsHtml += renderMailTaxSection('order', 'Tax on Order Total');
        taxDetailsHtml += renderMailTaxSection('delivery', 'Tax on Delivery Fee');
        taxDetailsHtml += renderMailTaxSection('packaging', 'Tax on Packaging Fee');
        taxDetailsHtml += renderMailTaxSection('platform', 'Tax on Platform Fee');
        taxDetailsHtml += `<strong>Total Tax : ${formatCurrency(total_tax_amount, currencyData)}</strong><br>`;
        if (taxDetailsHtml != '') {
            message = message.replace(/{taxdetails}/g, taxDetailsHtml);
        } else {
            message = message.replace(/{taxdetails}/g, "");
        }
        message = message.replace(/{totalAmount}/g, totalAmountText);
        
        emailTemplatesData.message = message;

        var url = "{{ url('send-email') }}";
        return await sendEmail(url, emailTemplatesData.subject, emailTemplatesData.message, [orderUserEmail]);
        } catch (error) {
            // Catch SMTP errors and log them without blocking order completion
            console.log('Email sending failed:', error);
            // Return success to prevent blocking order completion
            return true;
        }
    }

    function renderMailTaxSection(section, labelSuffix) {
        let taxHtml = "";
        if (!taxBreakdownGrouped[section]) return;
        for (let title in taxBreakdownGrouped[section]) {
            let taxlabel = title;
            let taxAmount = parseFloat(taxBreakdownGrouped[section][title]);
            taxHtml += `${taxlabel} ${labelSuffix} : ${formatCurrency(taxAmount, currencyData)}<br>`;
        }
        return taxHtml;
    }

    async function sendEmail(url, subject, message, recipients) {
        var checkFlag = false;
        await $.ajax({
            type: 'POST',
            data: {
                subject: subject,
                message: message,
                recipients: recipients
            },
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                checkFlag = true;
            },
            error: function(xhr, status, error) {
                checkFlag = true;
            }
        });
        return checkFlag;
    }
    
</script>
<?php } ?>

<?php if (isset($_COOKIE['section_color'])) { ?>
<style type="text/css">
    a,
    .list-card a:hover,
    a:hover {
        color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .hc-offcanvas-nav h2,
    .hc-offcanvas-nav:not(.touch-device) li:not(.custom-content) a:hover,
    .cat-item a.cat-link:hover {
        background-color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .homebanner-content .ban-btn a,
    .open-ticket-btn a,
    .select-sec-btn a {
        background-color:
            <?php echo $_COOKIE['section_color']; ?>;
        border-color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .homebanner-content .ban-btn a:hover,
    .open-ticket-btn a:hover,
    .select-sec-btn a:hover {
        color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .header-main .takeaway-div input[type="checkbox"]::before {
        background-color:
            <?php echo $_COOKIE['section_color']; ?>;
        opacity: 0.6;
    }

    .header-main .takeaway-div input[type="checkbox"]:checked::before {
        opacity: 1;
    }

    .list-card .member-plan .badge.open,
    .rest-basic-detail .feather_icon .fu-status a.rest-right-btn>span.open,
    .header-main .takeaway-div input[type="checkbox"]:checked::before {
        background-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .offer_coupon_code .offer_code p.badge,
    .offer_coupon_code .offer_price {
        color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .cat-item a.cat-link:hover i.fa {
        color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .rest-basic-detail .feather_icon a.rest-right-btn,
    .rest-basic-detail .feather_icon a.btn {
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .rest-basic-detail .feather_icon a.rest-right-btn .feather-star,
    .rest-basic-detail .feather_icon a.btn,
    .rest-basic-detail .feather_icon a.rest-right-btn:hover,
    ul.rating {
        color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .vendor-detail-left h4.h6::after,
    .sidebar-header h3.h6::after {
        background-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .gold-members .add-btn .menu-itembtn a.btn {
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
        color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .btn-primary,
    .transactions-list .media-body .app-off-btn a {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .btn-primary:hover,
    .btn-primary:not(:disabled):not(.disabled).active,
    .btn-primary:not(:disabled):not(.disabled):active,
    .show>.btn-primary.dropdown-toggle,
    .btn-primary.focus,
    .btn-primary:focus,
    .custom-control-input:checked~.custom-control-label::before,
    .row.fu-loadmore-btn .page-link {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .count-number-box .count-number .count-number-input,
    .count-number .count-number-input,
    .count-number-box .count-number button.count-number-input-cart:hover,
    .count-number button.btn-sm.btn:hover,
    .btn-link {
        color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .transactions-banner {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .transactions-list .media-body .app-off-btn a:hover,
    .rating-stars .feather-star.star_active,
    .rating-stars .feather-star.text-warning {
        color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .search .nav-tabs .nav-item.show .nav-link,
    .search .nav-tabs .nav-link.active {
        border-color:
            <?php echo $_COOKIE['section_color']; ?> !important;
        background-color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .text-primary,
    .card-icon>span {
        color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .checkout-left-box.siddhi-cart-item::after,
    .checkout-left-box.accordion::after,
    .dropdown-item.active,
    .dropdown-item:active,
    .restaurant-detail-left h4.h6::after,
    .sidebar-header h3.h6::after {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .page-link,
    .rest-basic-detail .feather_icon a.rest-right-btn {
        color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .page-link:hover {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .btn-outline-primary {
        color:
            <?php echo $_COOKIE['section_color']; ?>;
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .btn-outline-primary:hover {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .gendetail-row h3 {
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .dyn-menulist button.view_all_menu_btn {
        color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .daytab-cousines ul li>span {
        color:
            <?php echo $_COOKIE['section_color']; ?>;
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .daytab-cousines ul li>span:hover {
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
        background:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .feather-star.text-warning,
    .list-card .offer_coupon_code .star .badge .feather-star.star_active,
    .list-card-body .offer-btm .star .badge .feather-star.star_active {
        color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    a.restaurant_direction img {
        filter: grayscale(100%);
        -webkit-filter: grayscale(100%);
    }

    .modal-body .recepie-body .custom-control .custom-control-label>span.text-muted {
        color:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .payment-table tr th {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .slick-dots li.slick-active button::before {
        color:
            <?php echo $_COOKIE['section_color']; ?> !important;
        background:
            <?php echo $_COOKIE['section_color']; ?> !important;
    }

    .footer-top .title::after,
    .product-list .list-card .list-card-image .discount-price {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .ft-contact-box .ft-icon {
        color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .head-search .dropdown {
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .list-card .list-card-body .offer-code a {
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
        background:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .vandor-sidebar .vandorcat-list li a:hover,
    .vandor-sidebar .vandorcat-list li.active a {
        border-color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .list-card .list-card-body p.text-gray span.fa.fa-map-marker,
    .car-det-head .car-det-price span.price {
        color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .product-detail-page .addons-option .custom-control .custom-control-label.active::before {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .product-detail-page .addtocart .add-to-cart.btn.btn-primary.booknow {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    .product-detail-page .addtocart .add-to-cart.btn.btn-primary {
        border: 1px solid<?php echo $_COOKIE['section_color']; ?>;
        color:
            <?php echo $_COOKIE['section_color']; ?>;
    }

    @media (max-width: 991px) {
        .bg-primary {
            background:
                <?php echo $_COOKIE['section_color']; ?> !important;
        }
    }

    .swal2-actions .swal2-confirm.swal2-styled {
        background:
            <?php echo $_COOKIE['section_color']; ?>;
    }
</style>
<?php } ?>

@include('auth.default')
<?php
$filepath = public_path('countriesdata.json');
$countries = file_get_contents($filepath);
$countries = json_decode($countries);
$countries = (array)$countries;
$newcountries = array();
$newcountriesjs = array();
foreach ($countries as $keycountry => $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
    $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
}
?>
<style>
.google-btn-container{
    display:flex;
    justify-content:center;
    width:100%;
}
</style>

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

    .or-line span{
        color: <?php echo $_COOKIE['section_color']; ?>;
     }
</style>
<?php } ?>

<link href="{{ asset('vendor/select2/dist/css/select2.min.css')}}" rel="stylesheet">
<link href="{{ asset('/css/font-awesome.min.css')}}" rel="stylesheet">
<div class="login-page vh-100">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="col-md-6">
            <div class="col-10 mx-auto card p-3">
                <h3 class="text-dark my-0 mb-3">{{trans('lang.login')}}</h3>
                <p class="text-50">{{trans('lang.sign_in_to_continue')}}</p>
                <div class="error" id="error"></div>
                <form class="mt-3 mb-4" action="#" onsubmit="return loginClick()" id="login-box">
                    <div class="form-group">
                        <label for="email" class="text-dark">{{trans('lang.user_email')}}</label>
                        <input type="email" placeholder="{{trans('lang.user_email_help_2')}}" class="form-control"
                               id="email" aria-describedby="emailHelp" name="email">
                        <div  class="error" id="email_required"></div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="text-dark">{{trans('lang.password')}}</label>
                        <input type="password" placeholder="{{trans('lang.user_password_help_2')}}" class="form-control"
                               id="password" name="password">
                        <div class="error" id="password_required"></div>
                    </div>
                    <div class="forgot-password">
                        <p><a href="{{url('forgot-password')}}" class="standard-link"
                              target="_blank">{{trans('lang.forgot_password')}}?</a></p>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block"
                            id="login_btn">{{trans('lang.log_in')}}</button>
                    <a href="{{route('signup')}}" class="btn btn-primary btn-lg btn-block">{{trans('lang.sign_up')}}</a>

                    <div class="mt-2 text-center google-btn-container">  
                        <div id="googleBtn"></div>
                    </div>
                    <div class="or-line mb-3 mt-3">
                        <span>O</span>
                    </div>
                    <button type="button" onclick="loginWithPhoneClick()" id="loginphon_btn"
                            class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                        <i class="fa fa-phone mr-2"> </i> {{trans('lang.login')}} {{trans('lang.with_phone')}}</button>
                </form>
                <form class="form-horizontal form-material" name="loginwithphon" id="login-with-phone-box" action="#"
                      style="display:none;">
                    @csrf
                    <div class="box-title m-b-20">{{trans('lang.login')}}</div>
                    <div class="form-group " id="phone-box">
                        <div class="col-xs-12">                            
                             <select name="country" id="country_selector" class="country_code" @if(request('loginType') === 'phone') disabled @endif>
                                @foreach($countries as $country)
                                <option phoneCode="{{ $country->phoneCode }}" value="{{ $country->code }}"  >
                                    +{{ $country->phoneCode }} {{ $country->countryName }}</option>
                                @endforeach
                            </select>
                            <input class="form-control" placeholder="{{trans('lang.user_phone')}}" id="phone"
                                   type="number" class="form-control" name="phone" value="{{ old('phone') }}" required
                                   autocomplete="phone" autofocus>
                        </div>
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
                        @enderror
                    </div>
                    <div class="error" id="password_required_new1" style="display:none;"></div>
                    <div class="form-group " id="otp-box" style="display:none;">
                        <input class="form-control" placeholder="{{trans('lang.otp')}}" id="verificationcode"
                               type="text" class="form-control" name="otp" value="{{ old('otp') }}" required
                               autocomplete="otp" autofocus>
                    </div>
                    <div id="recaptcha-container" style="display:none;"></div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button type="button" style="display:none;" onclick="applicationVerifier()" id="verify_btn"
                                    class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">{{trans('lang.otp_verify')}}</button>
                            <button type="button" style="display:none;" onclick="sendOTP()" id="sendotp_btn"
                                    class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">{{trans('lang.otp_send')}}</button>
                            <div class="or-line mb-3 mt-3">
                                <span>O</span>
                            </div>
                            <button type="button" onclick="loginBackClick()"
                                    class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                <i class="fa fa-envelope mr-2"> </i> {{trans('lang.login')}} {{trans('lang.with_email')}}
                            </button>
                            <div class="error" id="password_required_new"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript">
    var database = firebase.firestore();
    function loginClick() {
        var email = $("#email").val();
        var password = $("#password").val();
        $("#email_required").css('display', 'none');
        $("#password_required").html("");
        if (email == '') {
            $("#email_required").css('display','block');
            jQuery("#email_required").html("Please enter email id").css("color", "red");
            return false;    
        }
        else if (password == '') {
            $("#email_required").css('display','none');
            jQuery("#password_required").html("Please enter valid password").css("color", "red");
            return false;
        }
                $("#email_required").css('display', 'none');
                firebase.auth().signInWithEmailAndPassword(email, password).then(function (result) {
                    var userEmail = result.user.email;
                    database.collection("users").where("email", "==", email).where('active', '==', true).get().then(async function (snapshots) {
                        if (snapshots.docs.length) {
                            var userData = snapshots.docs[0].data();
                            if (userData.role == "customer") {
                                var userToken = result.user.getIdToken();
                                var uid = result.user.uid;
                                var user = userData.id;
                                var firstName = userData.firstName;
                                var lastName = userData.lastName;
                                var imageURL = userData.profilePictureURL;
                                var url = "{{route('setToken')}}";
                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                        id: uid,
                                        userId: user,
                                        email: email,
                                        password: password,
                                        firstName: firstName,
                                        lastName: lastName,
                                        profilePicture: imageURL
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function (data) {
                                        if (data.access) {
                                            setCookie("loginType", "EmailPassword");
                                            window.location = "{{url('/')}}";
                                        }
                                    }
                                });
                            }
                        } else {
                            $("#password_required").html("");
                            $("#password_required").append("<p>{{trans('lang.waiting_for_approval')}}</p>");
                        }
                    })
                })
                .catch(function (error) {
                    $("#password_required").html("The entered password is invalid. Please check and try again.").css("color", "red");
                });
                return false;
    }
    function loginWithPhoneClick() {
        jQuery("#login-box").hide();
        jQuery("#login-with-phone-box").show();
        jQuery("#phone-box").show();
        jQuery("#recaptcha-container").show();
        jQuery("#sendotp_btn").show();
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
            'size': 'invisible',
            'callback': (response) => {
            }
        });
    }
    function loginBackClick() {
        jQuery("#login-box").show();
        jQuery("#login-with-phone-box").hide();
        jQuery("#sendotp_btn").hide();
    }
    function sendOTP() {
        if(jQuery("#phone").val() == "")
        {
            $("#password_required_new1").css('display','block');
            jQuery("#password_required_new1").html("Enter valid phone number.").css("color", "red");
        }
        else if (jQuery("#phone").val() && $(".country_code option:selected").attr('phoneCode')) {
            var countryCode = '+' + $(".country_code option:selected").attr('phoneCode');
            var phone = jQuery("#phone").val();
            var phoneNumber = '+' + $(".country_code option:selected").attr('phoneCode')+''+jQuery("#phone").val();
            database.collection("users").where("phoneNumber", "==", phone).where("role", "==", 'customer').where('active', '==', true).get().then(async function (snapshots) {
                if (snapshots.docs.length) {
                    var userData = snapshots.docs[0].data();
                    firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                        .then(function (confirmationResult) {
                            window.confirmationResult = confirmationResult;
                            if (confirmationResult.verificationId) {
                                jQuery("#phone-box").hide();
                                jQuery("#recaptcha-container").hide();
                                jQuery("#otp-box").show();
                                jQuery("#verify_btn").show();
                                $("#password_required_new1").css('display','none');
                            }
                        });
                } else {
                    jQuery("#password_required_new").html("User not found.");
                }
            });
        }
    }
    function applicationVerifier() {
        window.confirmationResult.confirm(document.getElementById("verificationcode").value)
            .then(function (result) {
                var countryCode = '+' + $(".country_code option:selected").attr('phoneCode');
                var phone = jQuery("#phone").val();
                database.collection("users").where('phoneNumber', '==', phone).get().then(async function (snapshots_login) {
                    userData = snapshots_login.docs[0].data();
                    if (userData) {
                        if (userData.role == "customer") {
                            var uid = result.user.uid;
                            var user = result.user.uid;
                            var firstName = userData.firstName;
                            var lastName = userData.lastName;
                            var imageURL = userData.profilePictureURL;
                            var url = "{{route('setToken')}}";
                            $.ajax({
                                type: 'POST',
                                url: url,
                                data: {
                                    id: uid,
                                    userId: user,
                                    email: userData.phoneNumber,
                                    password: '',
                                    firstName: firstName,
                                    lastName: lastName,
                                    profilePicture: imageURL
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (data) {
                                    if (data.access) {
                                        setCookie("loginType", "Phone");
                                        window.location = "{{url('/')}}";
                                    }
                                }
                            });
                        } else {
                            $('#email_required').text('');
                            jQuery("#password_required_new").html("User not found.");
                        }
                    }
                })
            }).catch(function (error) {
            $('#email_required').text('');
            jQuery("#password_required_new").html(error.message);
        });
    }
    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var countryCode = state.element.value.toLowerCase(); // "GB" → "gb"
        var baseUrl = "<?php echo URL::to('/'); ?>/flags/120";
        var $state = $(
            '<span><img src="' + baseUrl + '/' + countryCode + '.png' + '" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    }
    function formatState2(state) {
        if (!state.id) {
            return state.text;
        }
        var countryCode = state.element.value.toLowerCase();
        var baseUrl = "<?php echo URL::to('/'); ?>/flags/120";
        var $state = $(
            '<span><img class="img-flag" src="' + baseUrl + '/' + countryCode + '.png' + '" /> <span>' + state.text + '</span></span>'
        );
        return $state;
    }	
            
    var globalSettingsRef = database.collection('settings').doc("globalSettings");

    globalSettingsRef.get().then(function(snapshot) {

        var globalSettings = snapshot.data();

        if (!globalSettings || !globalSettings.defaultCountryCode) {
            return;
        }

        let defaultCountryCode = globalSettings.defaultCountryCode.toString().trim();
        let $option = null;

        $option = $("#country_selector option[value='" + defaultCountryCode.toUpperCase() + "']");

        if ($option.length === 0) {
            let phoneCode = defaultCountryCode.replace('+', '');
            $option = $("#country_selector option[phoneCode='" + phoneCode + "']");
        }

        if ($option.length > 0) {
            $("#country_selector").val($option.val()).trigger('change');
        } else {
            console.warn("Default country not found:", defaultCountryCode);
        }

    }).catch(function(error) {
        console.error("Error fetching global settings:", error);
    });
    jQuery(document).ready(function () {
        jQuery("#country_selector").select2({
            templateResult: formatState,
            templateSelection: formatState2,
            placeholder: "Select Country",
            allowClear: true
        });
    });     

    function saveUserData(user, event) {
        jQuery('#overlay').show();
        database.collection("users").doc(user.uid).get().then(async function (snapshots_login) {
            var userData = snapshots_login.data();
            if (userData) {
                if (userData.role == "customer") {
                    var uid = user.uid;
                    var firstName = userData.firstName || '';
                    var lastName = userData.lastName || '';
                    var imageURL = userData.profilePictureURL || '';
                    var url = "{{route('setToken')}}";
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            id: uid,
                            userId: user.uid,
                            email: userData.email || '',
                            password: '',
                            firstName: firstName,
                            lastName: lastName,
                            profilePicture: imageURL,
                            provider:'google'
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            if (data.access) {
                                setCookie("loginType", "Social");
                                window.location = "{{url('/')}}";
                            }
                        }
                    });
                } else {
                    $("#password_required").html("");
                    $("#password_required").append("<p>User not found.</p>");
                }
            } else {
                var phoneNumber = user.phoneNumber || '';
                var firstName = user.displayName ? user.displayName.split(' ')[0] : '';
                var lastName = '';
                if(user.displayName.split(' ')[1] == "" || user.displayName.split(' ')[1] == null || user.displayName.split(' ')[1] == undefined){
                    lastName = " ";
                }
                else{
                    
                    lastName = user.displayName.split(' ')[1];
                }
                var uuid = user.uid;
                var email = user.email || '';
                var photoURL = user.photoURL || '';
                var createdAtman = firebase.firestore.Timestamp.fromDate(new Date());
                var redirectUrl = `{{ url('socialsignup') }}?uuid=${encodeURIComponent(uuid)}&phoneNumber=${encodeURIComponent(phoneNumber)}&firstName=${encodeURIComponent(firstName)}&lastName=${encodeURIComponent(lastName)}&email=${encodeURIComponent(email)}&photoURL=${encodeURIComponent(photoURL)}&createdAt=${createdAtman.toDate()}`;
                window.location.href = redirectUrl;
            }
        }).catch(function (error) {
            console.log(error);
            $("#password_required").html("");
            $("#password_required").append("<p>"+ error.message +"</p>");
        });
    }
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    window.onload = function () {
        google.accounts.id.initialize({
            client_id: "{{env('GOOGLE_CLIENT_ID')}}",
            callback: handleGoogleCredential
        });

        google.accounts.id.renderButton(
            document.getElementById("googleBtn"),
            {
                theme: "outline",
                size: "large",
                // width: "100%",
                width: 600,
                text: "continue_with"
            }
        );

    };

    async function handleGoogleCredential(response) {
        try {
            const idToken = response.credential;

            const payload = JSON.parse(atob(idToken.split('.')[1]));
            const email = payload.email;

            const methods = await firebase.auth()
                .fetchSignInMethodsForEmail(email);

            if (methods.includes('password')) {
                $("#error")
                .show()
                .html("The account already exists for that email.")
                .css("color", "red");
                return;
            }

            const snapshot = await firebase.firestore()
                .collection('users')
                .where('email', '==', email)
                .limit(1)
                .get();

            if (!snapshot.empty) {
                const userData = snapshot.docs[0].data();

                if (
                    (userData.provider === 'google' || userData.provider === 'apple' || userData.provider === 'phone') &&
                    userData.role !== 'customer'
                ) {
                    $("#error")
                    .show()
                    .html("The account already exists for that email.")
                    .css("color", "red");
                    return;
                }
            }

            const credential = firebase.auth.GoogleAuthProvider.credential(idToken);
            const userCredential = await firebase.auth()
                .signInWithCredential(credential);

            saveUserData(userCredential.user);

        } catch (e) {
            console.error("Google Sign-In Error:", e);
        }
    }

</script>

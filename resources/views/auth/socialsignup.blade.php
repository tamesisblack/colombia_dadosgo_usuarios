@include('auth.default')
<?php
$countries = file_get_contents(public_path('countriesdata.json'));
$countries = json_decode($countries);
$countries = (array) $countries;
$newcountries = array();
$newcountriesjs = array();
foreach ($countries as $keycountry => $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
    $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
}
?>
<link href="{{ asset('vendor/select2/dist/css/select2.min.css')}}" rel="stylesheet">
<link href="{{ asset('/css/font-awesome.min.css')}}" rel="stylesheet">
<div class="siddhi-signup login-page vh-100">
    <div class="d-flex align-items-center justify-content-center py-3">
        <div class="col-md-6">
            <div class="col-10 mx-auto card p-3">
                <h3 class="text-dark my-0 mb-3">{{trans('lang.sign_up_with_us')}}</h3>
                <p class="text-50">{{trans('lang.sign_up_to_continue')}}</p>
                <div class="error" style="color: red" id="field_error"></div>
                <div class="error" id="field_error1" style="color:red;display:none;"></div>
                <form class="mt-3 mb-4" action="javascript:void(0)" onsubmit="return signupClick()">
                    <div class="form-group" id="firstName_div">
                        <label for="firstName" class="text-dark">{{trans('lang.first_name')}}</label>
                        <input type="text" placeholder="Ingresa tu nombre" value="{{ old('firstName', $firstName) }}" class="form-control" id="firstName" required>
                        <input type="hidden" id="hidden_fName" />
                    </div>
                    <div class="form-group" id="lastName_div">
                        <label for="lastName" class="text-dark">{{trans('lang.last_name')}}</label>
                        <input type="text" placeholder="Ingresa tu apellido" value="{{ old('lastName', $lastName) }}" class="form-control" id="lastName" required>
                        <input type="hidden" id="hidden_lName" />
                    </div>
                    <div class="form-group" id="email_div">
                        <label for="email" class="text-dark">{{trans('lang.email_address')}}</label>
                        <input type="email" placeholder="Ingresa tu correo electrónico" disabled class="form-control" value="{{ old('email', $email) }}" id="email" required
                            autocomplete="new-password" >
                    </div>
                    <div class="form-group" id="phone-box">
                        <?php
                        $countryCode = '';
                        $localNumber = $phoneNumber;
                        if (preg_match('/^\+(\d{1,3})\s?(\d+)$/', $phoneNumber, $matches)) {
                            $countryCode = $matches[1];
                            $localNumber = $matches[2];
                        }
                        ?>
                        <div class="col-xs-12">
                            {{--<select name="country" id="country_selector">
                                <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                    <?php
                                    $selected = ($countryCode == $valuecy->phoneCode) ? "selected" : "";
                                    ?>
                                    <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>"
                                        value="<?php echo $keycy; ?>">+<?php echo $valuecy->phoneCode; ?>  <?php echo $valuecy->countryName; ?></option>
                                <?php } ?>
                            </select>--}}
                            <select name="country" id="country_selector" class="country_code" >
                                @foreach($countries as $country)
                                <option phoneCode="{{ $country->phoneCode }}" value="{{ $country->code }}"  >
                                    +{{ $country->phoneCode }} {{ $country->countryName }}</option>
                                @endforeach
                            </select>
                            <input class="form-control" placeholder="{{trans('lang.user_phone')}}" id="mobileNumber"
                                type="number" name="mobileNumber" value="{{ old('phoneNumber', $localNumber) }}" required
                                autocomplete="mobileNumber">
                        </div>
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group" id="referral_div">
                        <label for="referral_code" class="text-dark">{{trans('lang.referral_code')}}
                            ({{trans('lang.optional')}})</label>
                        <input type="text" placeholder="Ingresa el código de referido" class="form-control" id="referral_code">
                        <input type="hidden" id="hidden_referral" />
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="email_valid" id="email_valid" value="1">
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block btn-sign-up" id="btn-sign-up">
                        {{trans('lang.sign_up')}}
                    </button>
                </form>
            </div>
            <div class="new-acc d-flex align-items-center justify-content-center mt-4 mb-3">
                <a href="{{url('login')}}">
                    <p class="text-center m-0"> {{trans('lang.already_an_account')}} {{trans('lang.sign_in')}}</p>
                </a>
            </div>
        </div>
    </div>
</div>

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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript">
    var createdAtman = firebase.firestore.Timestamp.fromDate(new Date());
    var database = firebase.firestore();
    async function signupClick() {
        $(".btn-sign-up").text('Please wait...');
        var email = $("#email").val();
        var password = $("#password").val();
        var countryCode = '+' + $(".country_code option:selected").attr('phoneCode');
        var mobile = jQuery("#mobileNumber").val();
        var mobileNumber = '+' + $(".country_code option:selected").attr('phoneCode') + '' + jQuery("#mobileNumber").val();
        var isoCode = $(".country_code").val();
        var firstName = $("#firstName").val();
        var lastName = $("#lastName").val();
        var referralCode = $("#referral_code").val();
        var referralBy = '';
        if (referralCode) {
            var referralByRes = getReferralUserId(referralCode);
            var referralBy = await referralByRes.then(function (refUserId) {
                return refUserId;
            });
        }
        var userReferralCode = Math.floor(Math.random() * 899999 + 100000);
        userReferralCode = userReferralCode.toString();
        var uuid = "{{$uuid}}";
                var photourl = "{{$photoURL}}";
                database.collection("referral").doc(uuid).set({
                    'id': uuid,
                    'referralBy': referralBy ? referralBy : '',
                    'referralCode': userReferralCode,
                });
                database.collection("users").doc(uuid).set({
                    'appIdentifier':"web",
                    'email': email,
                    'firstName': firstName,
                    'lastName': lastName,
                    'id': uuid,
                    'countryCode':countryCode,
                    'phoneNumber': mobile,
                    'countryISOCode': isoCode,
                    'role': "customer",
                    'profilePictureURL': photourl,
                    'provider':'google',
                    'createdAt': createdAtman,
                    'active':true
                }).then(() => {
                    var url = "{{route('newRegister')}}";
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            userId: uuid,
                            email: email,
                            password: '',
                            firstName: firstName,
                            lastName: lastName
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
                    })
                }).catch((error) => {
                        console.error("Error writing document: ", error);
                        $("#field_error").html(error);
                        window.scrollTo(0, 0);
                });
        return false;
    }
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    async function getReferralUserId(referralCode) {
        var refUserId = database.collection('referral').where('referralCode', '==', referralCode).get().then(async function (snapshots) {
            if (snapshots.docs.length > 0) {
                var referralData = snapshots.docs[0].data();
                return referralData.id;
            }
        });
        return refUserId;
    }
    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
	var newcountriesjs = JSON.parse(newcountriesjs);
	function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var countryCode = state.element.value.toLowerCase(); // "GB" → "gb"
        var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";
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
		var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";
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
            placeholder: "Selecciona un país",
            allowClear: true
        });
    });
</script>

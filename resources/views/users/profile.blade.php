@include('layouts.app')
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
@include('layouts.header')
<div class="siddhi-profile">
    <div class="container position-relative">
        <div class="py-5 siddhi-profile row">
            <div class="col-md-4 mb-3">
                <div class="bg-white rounded shadow-sm sticky_sidebar overflow-hidden">
                    <a href="{!! url('profile') !!}" class="">
                        <div class="d-flex align-items-center p-3">
                            <div class="left mr-3 user_image">
                            </div>
                            <div class="right">
                                <h6 class="mb-1 font-weight-bold user_full_name"></h6>
                                <p class="text-muted m-0 small"><span class="user_email_show"></span></p>
                            </div>
                        </div>
                    </a>
                    <div class="siddhi-credits d-flex align-items-center p-3 bg-light">
                        <p class="m-0">{{trans('lang.wallet_amount')}}</p>
                        <h5 class="m-0 ml-auto text-primary user_wallet"></h5>
                    </div>
                    <div class="bg-white password_div profile-details">
                        <a data-toggle="modal" data-target="#change_password" class="d-flex w-100 align-items-center border-bottom p-3">
                            <div class="left mr-3">
                                <h6 class="font-weight-bold mb-1 text-dark">{{trans('lang.change_password')}}</h6>
                            </div>
                            <div class="right ml-auto">
                                <h6 class="font-weight-bold m-0"><i class="feather-chevron-right"></i></h6>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-8 mb-3">
                <div class="rounded shadow-sm p-4 bg-white">
                    <h5 class="mb-4">{{trans('lang.my_account')}}</h5>
                    <div id="edit_profile">
                        <div class="error_top"></div>
                        <div>
                            <div class="form-group">
                                <label>{{trans('lang.first_name')}}</label>
                                <input type="text" class="form-control user_first_name" value="">
                            </div>
                            <div class="form-group">
                                <label>{{trans('lang.last_name')}}</label>
                                <input type="text" class="form-control user_last_name">
                            </div>
                            <div class="form-group">
                                <label>{{trans('lang.email')}}</label>
                                <input type="text" class="form-control user_email" disabled>
                            </div>
                            <div class="form-group" id="phone-box">
                            <div class="form-group form-material" >
                                            <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                                            <div class="col-12">
                                                <div class="phone-box position-relative" id="phone-box"> 
                                                    {{--<select name="country" id="country_selector" disabled>
                                                        <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                                        <?php $selected = ""; ?>
                                                        <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>"
                                                                value="<?php echo $keycy; ?>">
                                                            +<?php echo $valuecy->phoneCode; ?> {{$valuecy->countryName}}</option>
                                                        <?php } ?>
                                                    </select>--}}
                                                    <select name="country" id="country_selector" class="form-control country_code" disabled>
                                                        <option value="">{{ trans("lang.select_country") }}</option>
                                                        @foreach($countries as $country)
                                                        <option value="{{ $country->code }}"  data-phonecode="{{ $country->phoneCode }}" data-iso="{{ $country->code }}">{{$country->countryName}}
                                                            (+{{$country->phoneCode}})
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <input class="form-control user_phone" disabled  placeholder="Phone" id="phone" type="phone"
                                                            name="phone" value="{{ old('phone') }}" required
                                                        autocomplete="phone" autofocus>
                                                    <div id="error2" class="err"></div>
                                                </div>
                                            </div>
                                    </div>
                            <div class="form-group">
                                <label>{{trans('lang.restaurant_image')}}</label>
                                <input type="file" onChange="handleFileSelect(event)"  accept="image/*">
                            </div>
                            <div class="form-group">
                               <div class="placeholder_img_thumb user_image"></div>
                                <div id="uploding_image"></div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-block save_user_btn">{{ trans('lang.save')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="change_password" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{trans('lang.change_password')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-12 error_top_pass"></div>
                        <div class="col-md-12 form-group">
                            <label class="form-label">{{trans('lang.old_password')}}</label>
                            <input type="password" class="form-control user_old_password">
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="form-label">{{trans('lang.new_password')}}</label>
                            <input type="password" class="form-control user_new_password">
                        </div>
                    </div>
            </div>
            <div class="modal-footer p-0 border-0">
                <div class="col-6 m-0 p-0">
                    <button type="button" class="btn border-top btn-lg btn-block" data-dismiss="modal">{{ trans('lang.cancel')}}</button>
                </div>
                <div class="col-6 m-0 p-0">
                    <button type="button" class="btn btn-primary btn-lg btn-block change_user_password">{{ trans('lang.save')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@include('layouts.nav')
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
<script>
    var id = user_uuid;
    var database = firebase.firestore();
    var ref = database.collection('users').where("id","==",id);
    var photo ="";
    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
    placeholder.get().then( async function(snapshotsimage){
        var placeholderImageData = snapshotsimage.data();
        placeholderImage = placeholderImageData.image;
    })
    var currentCurrency = '';
    var currencyAtRight = false;
    var refCurrency = database.collection('currencies').where('isActive', '==' , true);
    var decimal=0;
    refCurrency.get().then( async function(snapshots){
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        decimal=currencyData.decimal_degits;
        currencyAtRight = currencyData.symbolAtRight;
    });
    var loginType = getCookie("loginType");
    if(loginType == "EmailPassword"){
        $(".password_div").show();
    }else{
        $(".password_div").hide();
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
    $(document).ready(function(){
        jQuery("#country_selector").select2({
            templateResult: formatState,
            templateSelection: formatState2,
            placeholder: "{{trans('lang.select_country')}}",
            allowClear: true
        });
        ref.get().then( async function(snapshots){
            var user = snapshots.docs[0].data();
            var wallet_amount_user = '';
            $(".user_email_show").html(user.email);
            $(".user_full_name").html(user.firstName+' '+user.lastName);
            if(user.hasOwnProperty('wallet_amount') && user.wallet_amount != '' && user.wallet_amount != '0' ){
                if(currencyAtRight){
                    wallet_amount_user=user.wallet_amount.toFixed(decimal)+''+currentCurrency;
                }else{
                    //  wallet_amount_user=currentCurrency+''+user.wallet_amount.toFixed(decimal);
                    let walletAmount = user.wallet_amount || 0; // Fallback to 0 if null or undefined
                    wallet_amount_user = currentCurrency + '' + walletAmount.toFixed(decimal);
                }
            }else{
                wallet_amount=0;
                if(currencyAtRight){
                    wallet_amount_user=wallet_amount.toFixed(decimal)+currentCurrency;
                }else{
                      wallet_amount_user=currentCurrency+wallet_amount.toFixed(decimal);
                }
            }
            $(".user_wallet").html(wallet_amount_user);
            $(".user_first_name").val(user.firstName);
            $(".user_last_name").val(user.lastName);
            $(".user_email").val(user.email);
           /*  if (user.countryCode != null){
                $("#country_selector").val(user.countryCode.replace('+', '')).trigger('change');
            } */
            if (user.countryISOCode) {        
                const isoCode = user.countryISOCode;

                if ($("#country_selector option[value='" + isoCode + "']").length > 0) {
                    $("#country_selector").val(isoCode).trigger('change');
                } else {
                    fallbackToGlobal();
                }

            } else if (user.countryCode) {

                let phoneCode = user.countryCode;
                if (phoneCode.startsWith('+')) phoneCode = phoneCode.substring(1);

                if ($("#country_selector option[data-phonecode='" + phoneCode + "']").length > 0) {
                    $("#country_selector")
                        .val($("#country_selector option[data-phonecode='" + phoneCode + "']").val())
                        .trigger('change');
                } else {
                    fallbackToGlobal();
                }

            } else {
                fallbackToGlobal();
            }
            function fallbackToGlobal() {
                database.collection('settings').doc("globalSettings").get()
                    .then(function(snapshot) {
                        let settings = snapshot.data();
                        if (!settings || !settings.defaultCountryCode) return;

                        let defaultCode = settings.defaultCountryCode.toString().trim();

                        // ISO first
                        if ($("#country_selector option[value='" + defaultCode.toUpperCase() + "']").length > 0) {
                            $("#country_selector").val(defaultCode.toUpperCase()).trigger('change');
                        }
                        // phone fallback
                        else {
                            let phoneCode = defaultCode.replace('+', '');
                            let $opt = $("#country_selector option[phoneCode='" + phoneCode + "']");
                            if ($opt.length > 0) {
                                $("#country_selector").val($opt.val()).trigger('change');
                            }
                        }
                    })
                    .catch(function(error) {
                        console.error("Error loading global settings:", error);
                    });
            }
            $(".user_phone").val(user.phoneNumber);
            photo = user.profilePictureURL;
            if (photo!='' && photo!=null) {
                $(".user_image").append('<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" style="width:50px" src="'+photo+'" alt="image">');
            }else{
                $(".user_image").append('<img class="rounded" style="width:50px" src="'+placeholderImage+'" alt="image">');
            }
            jQuery("#data-table_processing").hide();
        })
        $(".change_user_password").click(function(){
            var userOldPassword =  $(".user_old_password").val();
            var userNewPassword = $(".user_new_password").val();
            var userEmail = $(".user_email").val();
            if(userOldPassword == ''){
                $(".error_top_pass").show();
                $(".error_top_pass").html("");
                $(".error_top_pass").append("<p>{{trans('lang.old_password_error')}}</p>");
                window.scrollTo(0, 0);
            }else if(userNewPassword == ''){
                $(".error_top_pass").show();
                $(".error_top_pass").html("");
                $(".error_top_pass").append("<p>{{trans('lang.new_password_error')}}</p>");
                window.scrollTo(0, 0);
            }else{
                var user = firebase.auth().currentUser;
                firebase.auth().signInWithEmailAndPassword(userEmail, userOldPassword).then((userCredential) => {
                    var user = userCredential.user;
                        user.updatePassword(userNewPassword).then(() => {
                            showAlertMessage("{{trans('lang.password_updated_successfully')}}", "green", 3);  
                            window.scrollTo(0, 0);
                            $("#change_password .close").trigger( "click" );
                            }).catch((error) => {
                            $(".error_top_pass").show();
                            $(".error_top_pass").html("");
                            $(".error_top_pass").append("<p>"+error+"</p>");
                            window.scrollTo(0, 0);
                            });
                    }).catch((error) => {
                        var errorCode = error.code;
                        var errorMessage = error.message;
                        $(".error_top_pass").show();
                        $(".error_top_pass").html("");
                        $(".error_top_pass").append("<p>"+errorMessage+"</p>");
                        window.scrollTo(0, 0);
                    });
                }
          });
        $(".save_user_btn").click(function(){
            var userFirstName = $(".user_first_name").val();
            var userLastName = $(".user_last_name").val();
            var email = $(".user_email").val();
            // var countryCode = '+' + jQuery("#country_selector").val();
            var userPhone = $(".user_phone").val();
            var countryCode = '+' + $("#country_selector option:selected").data('phonecode');
            let isoCode = $("#country_selector option:selected").data('iso');
            var password = $(".user_password").val();
            var user_name = userFirstName+" "+userLastName;
            if(userFirstName == ''){
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_firstname_error')}}</p>");
                window.scrollTo(0, 0);
            }else if(email == ''){
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_email_error')}}</p>");
                window.scrollTo(0, 0);
            } else if(userPhone == '' ){
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_phone_error')}}</p>");
                window.scrollTo(0, 0);
            }
           else{
               database.collection('users').doc(id).update({'firstName':userFirstName,'lastName':userLastName,'email':email,'phoneNumber':userPhone, 'countryCode': countryCode,'countryISOCode': isoCode,'profilePictureURL':photo,'role':'customer'}).then(function(result) {
                        window.location.href = '{{ url("/")}}';
                     });
           }
        })
    })
    var storageRef = firebase.storage().ref('images');
    function handleFileSelect(evt) {
      var f = evt.target.files[0];
        // Check if the selected file is an image
        var validExtensions = ['.jpeg', '.jpg', '.png', '.gif', '.webp'];
        var ext = f.name.split('.').pop().toLowerCase();
        if (!validExtensions.includes('.' + ext)) {
            Swal.fire({text: "{{trans('lang.upload_valid_file')}}", icon: "error"});
            evt.target.value = '';
            return; // Exit the function if the file extension is not valid
        }
      var reader = new FileReader();
      reader.onload = (function(theFile) {
        return function(e) {
          var filePayload = e.target.result;
          var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
            var val =f.name;
          var ext=val.split('.')[1];
          var docName=val.split('fakepath')[1];
          var filename = (f.name).replace(/C:\\fakepath\\/i, '')
          var timestamp = Number(new Date());
          var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
          var uploadTask = storageRef.child(filename).put(theFile);
          uploadTask.on('state_changed', function(snapshot){
          var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
          console.log('Upload is ' + progress + '% done');
          jQuery("#uploding_image").text("{{trans('lang.image_is_uploading')}}");
        }, function(error) {
        }, function() {
            uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
                jQuery("#uploding_image").text("{{trans('lang.upload_is_completed')}}");
                photo = downloadURL;
                $(".user_image").empty();
                $(".user_image").append('<img onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" class="rounded" style="width:50px" src="'+photo+'" alt="image">');
          });
        });
        };
      })(f);
      reader.readAsDataURL(f);
    }
</script>

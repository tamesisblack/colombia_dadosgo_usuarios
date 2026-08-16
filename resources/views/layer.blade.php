@include('layouts.app')
<div class="home" id="home"></div>
@include('layouts.footer')
<script type="text/javascript">
    jQuery("#data-table_processing").show();
    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');
    var placeholderImageSrc = '';
    placeholderImageRef.get().then(async function (placeholderImageSnapshots) {
        var placeHolderImageData = placeholderImageSnapshots.data();
        placeholderImageSrc = placeHolderImageData.image;
    })
    var globalSettingsRef = database.collection('settings').doc('globalSettings');
    var homepageTemplateRef = database.collection('settings').doc('homepageTemplate');
    homepageTemplateRef.get().then(async function (homepageTemplateSnapshots) {
        var homepageTemplateData = homepageTemplateSnapshots.data();
        $('#home').html(homepageTemplateData.homepageTemplate);
        await globalSettingsRef.get().then(async function (globalSettingsSnapshots) {
            var globalSettingsData = globalSettingsSnapshots.data();
            var src_new = globalSettingsData.appLogo;
            $('#logo_web').html('<img onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'"alt="#" class="logo_web img-fluid" src="'+src_new+'">');
            $('.location-group .locate-me').attr("onclick","getCurrentLocation()");
            $("#logo_web").attr('src', globalSettingsData.appLogo);
            $('.hc-offcanvas-nav h2').html(globalSettingsData.applicationName);
            $("#footer_logo_web").attr('src', globalSettingsData.appLogo);
            setCookie('section_color', globalSettingsData.website_color, 365);
            setCookie('application_name', globalSettingsData.applicationName, 365);
            setCookie('meta_title', globalSettingsData.meta_title, 365);
            setCookie('favicon', globalSettingsData.favicon, 365);
            document.title = globalSettingsData.meta_title;
        });
        if(mapType == 'google'){
            initialize();
        }else{
            init();
        }
        jQuery("#data-table_processing").hide();
    });
    $(document).ready(function () {
        $(document).on("click", ".btn-continue", function (e) {
            var element = $('.cat-slider .cat-item.section-selected');
            var section_id = element.attr('data-id');
            var address = '';
            if ($('#user_locationnew').length) {
                address = $('#user_locationnew').val().trim();
            }
            if ($('#user_locationnew_mobile').length) {
                address = $('#user_locationnew_mobile').val().trim();
            }
            if (!address) {
                Swal.fire({text: "{{trans('lang.select_your_address')}}", icon: "error"});
                return false;
            }
            window.location.href = "<?php echo url('/'); ?>";
        });
    });
</script>

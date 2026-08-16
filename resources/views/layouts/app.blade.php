<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      <?php if(str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true'){ ?> dir="rtl" <?php } ?>>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="<?php echo @$_COOKIE['application_name']; ?>">
<meta name="author" content="<?php echo @$_COOKIE['application_name']; ?>">
<link rel="icon" id="favicon" type="image/png" href="{{ asset('img/favicon.png') }}">
<title><?php echo @$_COOKIE['meta_title']; ?></title>
    <link rel="stylesheet" type="text/css" href="{{asset('vendor/slick/slick.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('vendor/slick/slick-theme.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('vendor/slick/slick-lightbox.css')}}"/>
    <link href="{{asset('vendor/icons/feather.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <?php if(str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true'){ ?>
    <link href="{{asset('vendor/bootstrap/css/bootstrap-rtl.min.css')}}" rel="stylesheet">
    <?php } ?>
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <?php if(str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true'){ ?>
    <link href="{{asset('css/style_rtl.css')}}" rel="stylesheet">
    <?php } ?>
    <link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendor/sidebar/demo.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script type="text/javascript">
        var section_colorman = '<?php echo @$_COOKIE['section_color']; ?>';
        var application_name = '<?php echo @$_COOKIE['application_name']; ?>';
        var meta_title = '<?php echo @$_COOKIE['meta_title']; ?>';
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('vendor/select2/dist/css/select2.min.css')}}" rel="stylesheet">
</head>
<body class="fixed-bottom-bar">
</body>
</html>

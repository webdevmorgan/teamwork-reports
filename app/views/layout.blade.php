<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Teamwork Reports</title>

    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('css/themesorter.css') }}
    {{ HTML::style('css/font-awesome/css/font-awesome.css') }}
    {{ HTML::style('css/styles.css') }}
    {{ HTML::script('js/jquery-1.11.1.js') }}
    {{ HTML::script('js/bootstrap.min.js') }}
    {{ HTML::script('js/md5.js') }}
    {{ HTML::script('js/aes.js') }}
    <script type="text/javascript">
        var val_key = CryptoJS.AES.encrypt('<?=Config::get('teamwork.API_KEY');?>', "/");
        var name = CryptoJS.AES.encrypt('<?=Config::get('teamwork.COMPANY');?>', "/");
    </script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="content_wrapper">
                 <!-- Content -->
                 @yield('content')
        </div>
    </div>
</div>

{{ HTML::script('js/jquery.tablesorter.js') }}
{{ HTML::script('js/jquery.tablesorter.widgets.js') }}
{{ HTML::script('js/jquery.blockUI.js') }}
{{ HTML::script('js/autoNumeric.js') }}
{{ HTML::script('js/functions.js') }}
{{ Requirement::requireJS()}}
</body>
</html>
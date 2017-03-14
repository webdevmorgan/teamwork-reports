@extends('layout')
@section('content')
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
<script type="text/javascript">
    intuit.ipp.anywhere.setup({
        menuProxy: '<?php print($quickbooks_menu_url); ?>',
        grantUrl: '<?php print($quickbooks_oauth_url); ?>'
    });
</script>
<?php if ($quickbooks_is_connected){ ?>
    <div style="border: 2px solid green; text-align: center; padding: 8px; color: green;">
        CONNECTED!<br>
    </div>
<?php } else { ?>
     <div style="border: 2px solid red; text-align: center; padding: 8px; color: red;">
        <b>NOT</b> CONNECTED!<br>
        <br>
        <ipp:connectToIntuit></ipp:connectToIntuit>
        <br>
        <br>
        You must authenticate to QuickBooks <b>once</b> before you can exchange data with it. <br>
        <br>
        <strong>You only have to do this once!</strong> <br><br>

        After you've authenticated once, you never have to go
        through this connection process again. <br>
        Click the button above to
        authenticate and connect.
    </div>
<?php
}
?>
@stop
@extends('layout')
@section('content')
<div style="text-align: center; font-family: sans-serif; font-weight: bold;">
    You're connected! Please wait...
</div>
<script type="text/javascript">
    window.opener.location.reload(false);
    window.setTimeout('window.close()', 2000);
</script>
@stop
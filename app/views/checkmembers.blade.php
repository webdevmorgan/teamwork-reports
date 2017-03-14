@extends('layout')
@section('content')
<div id="content">
    <div class="row">
        <?php
		Utilities::dump($persons['content']);
		?>
    </div>
</div>
@stop
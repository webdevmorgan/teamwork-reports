@extends('layout')
@section('content')
<div id="content">
    <div class="row">
        <div class="col-md-4 col-md-offset-4" style="margin-top:12%;">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Enter your API Key</h3>
                </div>
                <div class="panel-body">
                    <form action="/projects" method="post">
                        <fieldset>
                            <div class="form-group">
                                <input name="api_key" type="text" class="form-control"/>
                            </div>
                            <div class="form-inline">
                                <input type="submit" value="Submit" class="btn btn-primary view-report" />
                                <label class="checkbox" style="margin:0 0 0 23px;"><input type="checkbox" name="remember_me"></label>
                                <span class="remember_text">Remember Me</span>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
        @if(Session::has('message'))
        <div class="col-md-4 col-md-offset-4">
            <div class="alert alert-danger">{{ Session::get('message') }}</div>
        </div>
        @endif
    </div>
</div>
@stop
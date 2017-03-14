@extends('layout')
@section('content')
<div id="content">
    <div class="row">
        <div style="margin-top:150px;">
            @if($company['logo'])
            <img style="margin:0 auto;display:block;" src="{{$company['logo']}}" alt="{{$company['companyname']}}"/>
            @endif
        </div>
        <div class="col-md-4 col-md-offset-4" style="margin-top:20px;">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Select a Project</h3>
                </div>
                <div class="panel-body">
                    <form action="/report">
                        <fieldset>
                            <div class="form-group">
                                <select id="project" class="form-control" name="project">
                                    @if($projects)
                                    @foreach ($projects as $project)
                                    <option value="{{$project['id']}}">{{$project['name']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <input type="submit" value="View Report" class="btn btn-primary view-report">
                        </fieldset>
                    </form>
                </div>
                <div class="panel-footer">
                <h6 style="text-align: right;"> Not You? <a href="/?changeaccount=true">Click here.</a> </h6>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
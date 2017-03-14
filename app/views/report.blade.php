@extends('layout')
@section('content')
<div id="content">
    <div class="row" style="padding-top:10px;">
        <div class="col-md-8">
            <h3>Project Report: <a class="with-tooltip" data-toggle="tooltip" data-placement="top" title="Back to Projects" href="/projects" style="color: #000;text-decoration: underline"><?=$project_name?></a> <span style="color:#ccc;font-size:13px;"><?=$comp_name;?></span></h3>
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-2 dl-to-pdf marginTop right">
            <button type="button" class="btn btn-success download-pdf" style="float:right">Download PDF</button>
            <a class="download-option-show-hide download-options hide-for-downloadables" href="#" style="font-size: 10px; color: #000000; display: inline-block;">Show/Hide Options</a>
            <?php
            if($project_logo){
                $logo = $project_logo;
            } else {
                $logo = $company['logo'];
            }
            ?>
            <img style="display:none;" class="right comp_logo"  src="{{Utilities::cleanImageSource($logo)}}" alt="{{$company['companyname']}}"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <p style="padding-top:10px;"><strong>Project Start Date</strong>: <?=$start?> <strong>End Date</strong>: <?=$end;?></p>
        </div>
        <div class="col-md-4">
            <div class="right" id="legend">
                <table cellspacing="5">
                    <tbody><tr>
                        <td width="15" class="indicator completed lined">&nbsp;</td><td class="legend_text"> Completed </td>
                        <td width="15" class="indicator upcoming lined">&nbsp;</td><td class="legend_text"> Upcoming (next 7 days) </td>
                        <td width="15" class="indicator late lined">&nbsp;</td><td class="legend_text"> Late </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row hide-for-downloadables download-options-container" style="display: none;">
                <form class="downloadable-form-options">
                    <div class="col-md-12">
                        <label style="width: 100%; text-align: left;">Hide in PDF:</label>
                    </div>
                    <div class="col-md-6">
                        <span class="downloadables-checkbox"><input type="checkbox" class="hide-task-description" value="desc-entry" name="sections[]"/> Description</span>
                        <span class="downloadables-checkbox"><input type="checkbox" class="hide-date-entries" value="date-entries" name="sections[]" /> Date Entries</span>
                        <span class="downloadables-checkbox"><input type="checkbox" class="hide-person-responsible" value="person-entry" name="sections[]"/> Person Responsible</span>
                        <span class="downloadables-checkbox"><input type="checkbox" class="hide-status" value="status-entry" name="sections[]" /> Status</span>
                    </div>
                    <div class="col-md-6">
                        <span class="downloadables-checkbox"><input type="checkbox" class="hide-priority" value="priority-entry" name="sections[]"/> Priority</span>
                        <span class="downloadables-checkbox"><input type="checkbox" class="hide-progress" value="progress-entry" name="sections[]"/> Progress</span>
                        <span class="downloadables-checkbox"><input type="checkbox" class="hide-time" value="time-entry" name="sections[]" /> Time Entries</span>
                        <span class="downloadables-checkbox"><input type="checkbox" class="hide-comment" value="comment-entry" name="sections[]" /> Comments</span>
                        <!--<span class="test-click">click</span>-->
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if($checking)
    @include('milestone', array('milestones'=>$milestones, 'comments'=>$comments))
    @else
    @include('tasklists', array('tasklists'=>$milestones,'comments'=>$comments))
    @endif
</div>
@stop
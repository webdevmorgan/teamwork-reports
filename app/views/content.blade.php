<div class="row">
            <div class="col-md-8" style="padding-top:10px;">
                <h3>Project Report: <?=$project_name?> <span style="color:#ccc;font-size:13px;"><?=$comp_name;?></span></h3>
            </div>
            <div class="col-md-4 dl-to-pdf marginTop right"><button type="button" class="btn btn-success download-pdf" style="float:right">Download PDF</button>
            
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <p style="padding-top:10px;"><strong>Project Start Date</strong>: <?=$start?> <strong>End Date</strong>: <?=$end;?></p>
            </div>
            <div class="col-md-6">
                <div class="right" id="legend">
                    <table cellspacing="5" style="font-size:12px">
                        <tbody><tr>
                            <td width="15" class="indicator completed lined">&nbsp;</td><td style="padding-left:4px;padding-right:4px;">Completed</td>
                            <td width="15" class="indicator upcoming lined">&nbsp;</td><td style="padding-left:4px;padding-right:4px;">Upcoming (next 7 days)</td>
                            <td width="15" class="indicator late lined">&nbsp;</td><td style="padding-left:4px;">Late</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
</div>
@if($checking)
   @include('milestone', array('milestones'=>$milestones))
@else
   @include('tasklists', array('tasklists'=>$milestones))
@endif

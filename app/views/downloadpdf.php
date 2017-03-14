<!DOCTYPE html>
<html>
<head>
<title>Diggit Project Report</title>
<link media="all" type="text/css" rel="stylesheet" href="css/download.css">
<style>
    body{
        font-family: 'sans-serif';
    }
    .sub-header {font-size:11px;}
    #legend tr td{font-size:10px; }
</style>
</head>
<body>
<div class="container">
    <div class="row">
        <div id="content">
            <div class="row">
                <div class="col-md-8" style="padding-top:10px;">
                    <h3>Project Report: <?=$total_time['name']?> <span style="color:#ccc;font-size:13px;"><?=$total_time['company']['name'];?></span></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p class="sub-header"><strong>Project Start Date</strong>: <?=$start?> <strong>End Date</strong>: <?=$end;?></p>
                </div>
                <div class="col-md-6">
                    <div class="right" id="legend">
                        <table cellspacing="5" style="font-size:12px">
                            <tbody><tr>
                                <td width="12" class="indicator completed lined">&nbsp;</td><td style="padding-left:4px;padding-right:4px;">Completed</td>
                                <td width="12" class="indicator upcoming lined">&nbsp;</td><td style="padding-left:4px;padding-right:4px;">Upcoming (next 7 days)</td>
                                <td width="12" class="indicator late lined">&nbsp;</td><td style="padding-left:4px;">Late</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h4>Milestones and associated tasks</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table cellspacing="5" style="font-size:12px;color:#808080;">
                        <tbody>
                        <tr>
                            <td><strong>Total Hours:</strong></td><td style="padding-left:4px;padding-right:15px;"><?=Utilities::getEstimated($total_time['time-totals']['total-mins-sum']);?></td>
                            <td><strong>Total Estimated Time:</strong></td><td style="padding-left:4px;padding-right:15px;"><?=Utilities::getEstimated($total_time['time-estimates']['total-mins-estimated']);?></td>
                            <td><strong>Total Billable Time:</strong></td><td style="padding-left:4px;padding-right:15px;"><?=Utilities::getEstimated($total_time['time-totals']['billable-mins-sum']);?></td>
                            <td><strong>Total NonBillable Time:<strong></strong></strong></td><td style="padding-left:4px;"><?=Utilities::getEstimated($total_time['time-totals']['non-billable-mins-sum']);?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table tablesorter table-bordered milestone" cellspacing="1" cellpadding="0" id="mainTable">
                    <thead>
                    <tr class="main_header">
                        <th class="dark-header" width="5" style="text-align: center;">&nbsp;</th>
                        <th style="text-align: center">Milestone</th>
                        <th style="text-align: center">Description</th>
                        <th style="text-align: center">Due Date</th>
                        <th style="text-align: center">Responsible</th>
                        <th style="text-align: center">Status</th>
                        <th style="text-align: center">Days Late</th>
                        <th style="text-align: center">Date Completed</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                         foreach ($milestones as $milestone) {
                             if($milestone['status'] == 'completed'){
                                 //$date = date('d M (Y)', strtotime($milestone['completed-on']));
                                 $date="";
                                 $days_late = "";
                             } else if ($milestone['status'] == 'late') {
                                 $date = "";
                                 $late = Utilities::getDaysLate($milestone);
                                 $days_late = "Due ".$late->days." days ago";
                             } else {
                                 $date = "";
                                 $days_late = "";
                             }
                    ?>
                             <tr data-id="<?=$milestone['id']?>" class="milestone_row">
                                 <td rowspan="1"  class="indicator <?=$milestone['status']?>">&nbsp;</td>
                                 <td><strong><?=$milestone['title']?></strong></td>
                                 <td><p><?=$milestone['description']?></p></td>
                                 <td><?=date('d M (Y)', strtotime($milestone['deadline']))?></td>
                                 <td><?=$milestone['responsible-party-names']?></td>
                                 <td><?=ucfirst($milestone['status']);?></td>
                                 <td><?=$days_late;?></td>
                                 <td><?=$date?></td>
                             </tr>
                    <?php
                             if(!empty($milestone['tasklists'])){
                    ?>
                            <tr class="tablesorter-childRow">
                                <td colspan="8">
                                    <div style="margin-left: 20px;">
                                        <table class="table tablesorter table-bordered tablesorter-default">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center;">&nbsp;</th>
                                                <th>Tasklists</th>
                                                <th>Description</th>
                                                <th>Start Date</th>
                                                <th>Date Due</th>
                                                <th>Assigned To</th>
                                                <th>Priority</th>
                                                <th>Progress</th>
                                                <th>Status</th>
                                                <th>Estimated</th>
                                                <th>Time</th>
                                                <th>Billable</th>
                                            </tr>
                                            </thead>
                                            <?php
                                                foreach ($milestone['tasklists'] as $tasklist) {
                                            ?>
                                                    <tbody class="tablesorter-no-sort">
                                                    <tr><th>&nbsp;</th><th colspan="12">&nbsp;<?=$tasklist['name'];?></th></tr>
                                                    </tbody>
                                            <?php
                                                }
                                            ?>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                    <?php

                             }
                    ?>

                    <?php
                         }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
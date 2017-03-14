<div class="row">
    <div class="col-md-12">
        <h4>Tasklists and tasks</h4>
    </div>
</div>
<div class="row time-entry">
    <div class="col-md-12">
        <table cellspacing="5" style="font-size:12px;color:#808080;">
            <tbody>
            <tr>
                <td><strong>Total Hours:</strong></td><td style="padding-left:4px;padding-right:15px;"><?=Utilities::getEstimated($total_time['time-totals']['total-mins-sum']);?></td>
                <td><strong>Total Estimated Time:</strong></td><td style="padding-left:4px;padding-right:15px;"><?=Utilities::getEstimated($total_time['time-estimates']['total-mins-estimated']);?></td>
                <td><strong>Total Billable Time:</strong></td><td style="padding-left:4px;padding-right:15px;"><?=Utilities::getEstimated($total_time['time-totals']['billable-mins-sum']);?></td>
                <td><strong>Total Non Billable Time:<strong></strong></strong></td><td style="padding-left:4px;"><?=Utilities::getEstimated($total_time['time-totals']['non-billable-mins-sum']);?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row image_loader" style="margin-top:10%">
    <div class="span4"></div>
    <div class="span4"><img class="center-block" src="/images/loader.gif"/></div>
    <div class="span4"></div>
</div>
<div class="table-responsive">
<table class="task sortable table-bordered tablesorter-default" role="grid" style="display: none;" id="mainTable">
<colgroup>
    <col/>
    <!--<col/>-->
    <col/>
    <col/>
    <col/>
    <col/>
    <col/>
    <col/>
    <col/>
    <col/>
    <col/>
    <col/>
    <col/>
</colgroup>
<thead class="header_mile">
<tr class="tablesorter-headerRow" role="row">
    <th width="4" style="text-align: center;" class="sorter-false filter-false"><input type="checkbox" name="c_tasklist_all" data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report"/></th>
    <th width="4" class="sorter-false filter-false tablesorter-inner-indicator"></th>
    <th colspan="2">Tasklists</th>
    <!--<th class="desc-entry">Description</th>-->
    <th class="date-entries">Start Date</th>
    <th class="date-entries">Date Due</th>
    <th class="person-entry">Assigned To</th>
    <th class="priority-entry">Priority</th>
    <th class="progress-entry">Progress</th>
    <th class="status-entry">Status</th>
    <th class="time-cell time-entry">Estimated</th>
    <th class="time-cell time-entry">Time</th>
    <th class="time-cell time-entry">Billable</th>
</tr>
</thead>
<?php
foreach($tasklists as $tasklist) {
    $tasks = $tasklist['todo-items'];
    ?>
    <tbody class="tablesorter-no-sort">
    <tr class="child-mtasklist" data-id="<?=$tasklist['id'];?>">
        <th><input type="checkbox" name="c_tasklist" data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report" /></th>
        <th class="child-mtasklist-title" colspan="12"><?php if(count($tasks) > 0) { ?><a class="collapse-task show-task"><i class="fa fa-plus-square-o fa-lg"></i></a><?php } ?><?=$tasklist['name'];?></th>
    </tr>
    </tbody>
    <tbody id="task-<?=$tasklist['id'];?>" class="mil-task-list hide-task" style="display: none;">
    <?php
    $total_estimated = 0;
    $total_time = 0;
    $total_billable = 0;
    $class_over = '';
    $total_over_under = 0;
    if(!empty($tasks)){
        foreach($tasks as $task) {
            $start =  isset($task['start-date']) ? date('d M (Y)', strtotime($task['start-date'])) : "";
            $due =  isset($task['due-date']) ? date('d M (Y)', strtotime($task['due-date'])) : "";
            $progress = $task['progress'] > 0 ? $task['progress']."%" : "";
            $completed =  isset($task['completed_on']) ? date('d M (Y)', strtotime($task['completed_on'])) : "";
            $time_val = "None";
            $billable = "None";
            $status = Utilities::createStatus($task);
            $status_class = Utilities::createStatusClass($task);
            $total_estimated += (int)$task['estimated-minutes'];

            $over_under = $task['estimated-minutes'] > 0 ?  $task['estimated-minutes']: 0;
            $color =  $task['estimated-minutes'] > 0 ?  "" : "";
            if($task['timeIsLogged'] && isset($time[$task['id']])) {
                $time_ = $time[$task['id']];
                $res_time = Utilities::sumTime($time_);
                $time_val = Utilities::getEstimated($res_time['total']);
                $billable = Utilities::getEstimated($res_time['billable']);

                $total_time += $res_time['total'];
                $total_billable += $res_time['billable'];
                $color = $task['estimated-minutes'] < $res_time['billable'] ? "red" : "";
                //$color = $task['estimated-minutes'] > $res_time['billable'] ? "" : "";

                $over_under = $res_time['billable'] > $task['estimated-minutes'] ? $res_time['billable'] - $task['estimated-minutes'] : $task['estimated-minutes'] - $res_time['billable'];
            }

            $over_under_time = $over_under > 0 ? "( ".Utilities::getEstimated($over_under)." )" : "";
            $total_logged_time = $total_billable + $total_time;
            $total_over_under = $total_estimated - $total_logged_time;
            $total_over_under = abs($total_over_under);

            $class_over = '';
            if($total_estimated < $total_logged_time){
                $class_over = 'red';
            } else if($total_estimated > $total_logged_time){
                $class_over = 'green';
            }
            ?>

            <tr data-id="<?=$task['id'];?>" class="indicator <?=$status_class;?> child-row-task tablesorter-hasChildRow">
                <td class="tablesorter-inner-indicator"></td>
                <td class="indicator <?=$status_class;?>"><input type="checkbox" name="c_task" data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report"/></td>
                <td colspan="2" class="<?php if(strlen(trim(strip_tags($task['description']))) > 0 ) { ?>border-<?=$status_class;?><?php } ?>"><b><?=$task['content']?></b>
                </td>
                <td class="date-entries"><?=$start;?></td>
                <td class="date-entries"><?=$due;?></td>
                <td class="person-entry"><?php if(isset($task['responsible-party-names'])){ echo $task['responsible-party-names']; }?></td>
                <td class="priority-entry"><?=$task['priority']?></td>
                <td class="progress-entry"><?=$progress?></td>
                <td class="status-entry"><?=$status;?> <?=$completed?></td>
                <td class="time-entry"><?=Utilities::getEstimated($task['estimated-minutes']);?></td>
                <td class="time-entry"><?=$time_val;?></td>
                <td class="time-entry <?=$color;?>"><?=$billable;?></td>
            </tr>
            <?php if(strlen(trim(strip_tags($task['description']))) > 0 ) { ?>
                <tr class="indicator <?=$status_class;?> child-row-task tablesorter-childRow desc-entry" data-id="<?=$task['id'];?>">
                    <td class="tablesorter-inner-indicator"></td>
                    <td class="indicator <?=$status_class;?>"></td>
                    <td colspan="11" class="comments-row <?php if(!empty($comments[$task['id']])) {?>desc_area<?php } ?>">
                        <p class="desc-entry <?php if(strlen(trim(strip_tags($task['description']))) > 0 ) { ?>description<?php } ?>"><?php if(strlen(trim(strip_tags($task['description']))) > 0 ) { ?><?=trim(strip_tags($task['description']));?><?php }  ?></p>
                    </td>
                </tr>
            <?php } ?>
            <?php if(!empty($comments[$task['id']])) {?>
                <tr class="indicator <?=$status_class;?> child-row-task tablesorter-childRow comment-entry" data-id="<?=$task['id'];?>">
                    <td class="tablesorter-inner-indicator"></td>
                    <td class="indicator <?=$status_class;?>"></td>
                    <td colspan="11" class="comments-row">
                        <a data-id="<?=$task['id'];?>" class="collapse-comments show-comments opened"><i class="fa fa-plus-square-o fa-lg"></i></a> <strong>Comments</strong>
                        <?php foreach($comments[$task['id']] as $key=>$comm) {
                            $num = $key + 1;
                            $dtime = date('F d Y', strtotime($comm['datetime']));
                            ?>
                            <div class="comment_wrap_<?=$task['id'];?>" style="display: none">
                                <div class="name">
                                    <span class="commentNo"><?=$num;?>.&nbsp;&nbsp;</span><span class="author"><strong><?=$comm['author-firstname'];?> <?=$comm['author-lastname'];?></strong></span>&nbsp;<span class="company">(<?=$comm['company-name'];?>)</span>&nbsp;&nbsp;<span class="date"><?=$dtime;?></span>
                                </div>
                                <div class="comments">
                                    <p><?=$comm['body'];?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>

            <?php
            if(!empty($task['subTasks'])){
                foreach($task['subTasks'] as $subTask) {
                    $start =  isset($subTask['start-date']) ? date('d M (Y)', strtotime($subTask['start-date'])) : "";
                    $due =  isset($subTask['due-date']) ? date('d M (Y)', strtotime($subTask['due-date'])) : "";
                    $progress = $subTask['progress'] > 0 ? $subTask['progress']."%" : "";
                    $completed =  isset($subTask['completed_on']) ? date('d M (Y)', strtotime($subTask['completed_on'])) : "";
                    $time_val = "None";
                    $billable = "None";
                    $estimated = Utilities::getEstimated($subTask['estimated-minutes']);
                    $total_estimated += (int)$subTask['estimated-minutes'];
                    $status = Utilities::createStatus($subTask);
                    $status_class = Utilities::createStatusClass($subTask);

                    $over_under = $subTask['estimated-minutes'] > 0 ?  $subTask['estimated-minutes']: 0;
                    $color =  $subTask['estimated-minutes'] > 0 ?  "" : "";

                    if($subTask['timeIsLogged'] && isset($time[$subTask['id']])) {
                        $time_ = $time[$subTask['id']];
                        $res_time = Utilities::sumTime($time_);
                        $time_val = Utilities::getEstimated($res_time['total']);
                        $billable = Utilities::getEstimated($res_time['billable']);

                        $total_time += $res_time['total'];
                        $total_billable += $res_time['billable'];
                        $color = $subTask['estimated-minutes'] < $res_time['billable'] ? "red" : "";
                        //$color = $subTask['estimated-minutes'] > $res_time['billable'] ? "" : "";
                        $over_under = $res_time['billable'] > $subTask['estimated-minutes'] ? $res_time['billable'] - $subTask['estimated-minutes'] : $subTask['estimated-minutes'] - $res_time['billable'];

                    }

                    $over_under_time = $over_under > 0 ? "( ".Utilities::getEstimated($over_under)." )" : "";
                    $total_logged_time = $total_billable + $total_time;
                    $total_over_under = $total_estimated - $total_logged_time;
                    $total_over_under = abs($total_over_under);

                    $class_over = '';
                    if($total_estimated < $total_logged_time){
                        $class_over = 'red';
                    } else if($total_estimated > $total_logged_time){
                        $class_over = 'green';
                    }

                    ?>
                    <tr class="indicator <?=$status_class?> child-row-task-<?=$task['id'];?> child-row-task tablesorter-hasChildRow" data-id="<?=$subTask['id'];?>" data-parent-id="<?=$task['id'];?>">
                        <td class="tablesorter-inner-indicator"></td>
                        <td class="indicator <?=$status_class;?>"><input type="checkbox" name="c_task" data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report"/></td>
                        <td style="padding-left:20px;" colspan="2"  class="<?php if(strlen(trim(strip_tags($subTask['description']))) > 0 ) { ?>border-<?=$status_class;?><?php } ?>">
                            <b>&#8226; <?=$subTask['content']?></b>
                        </td>
                        <td class="date-entries"><?=$start?></td>
                        <td class="date-entries"><?=$due?></td>
                        <td class="person-entry"><?=$subTask['responsible-party-names']?></td>
                        <td class="priority-entry"><?=$subTask['priority']?></td>
                        <td class="progress-entry"><?=$progress;?></td>
                        <td class="status-entry"><?=$status;?> <?=$completed;?></td>
                        <td class="time-entry"><?=$estimated;?></td>
                        <td class="time-entry"><?=$time_val;?></td>
                        <td class="time-entry <?=$color;?>"><?=$billable;?></td>
                    </tr>
                    <?php if(strlen(trim(strip_tags($subTask['description']))) > 0 ) { ?>
                        <tr class="indicator <?=$status_class;?> child-row-task tablesorter-childRow desc-entry" data-id="<?=$subTask['id'];?>" data-parent-id="<?=$task['id'];?>">
                            <td class="tablesorter-inner-indicator"></td>
                            <td class="indicator <?=$status_class;?>"></td>
                            <td colspan="11" style="padding-left:20px;" class="comments-row <?php if(!empty($comments[$subTask['id']])) {?>desc_area<?php } ?>">
                                <p class="desc-entry <?php if(strlen(trim(strip_tags($subTask['description']))) > 0 ) { ?>description<?php } ?>"><?php if(strlen(trim(strip_tags($subTask['description']))) > 0 ) { ?><?=trim(strip_tags($subTask['description']));?><?php }  ?></p>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if(!empty($comments[$subTask['id']])) {?>
                        <tr class="indicator <?=$status_class;?> child-row-task tablesorter-childRow comment-entry" data-id="<?=$subTask['id'];?>" data-parent-id="<?=$task['id'];?>">
                            <td class="tablesorter-inner-indicator"></td>
                            <td class="indicator <?=$status_class;?>"></td>
                            <td colspan="11" class="comments-row">
                                <a data-id="<?=$subTask['id'];?>" style="padding-left:20px;" class="collapse-comments show-comments opened"><i class="fa fa-plus-square-o fa-lg"></i></a> <strong>Comments</strong>
                                <?php foreach($comments[$subTask['id']] as $key=>$comm) {
                                    $num = $key + 1;
                                    $dtime = date('F d Y', strtotime($comm['datetime']));
                                    ?>
                                    <div class="comment_wrap_<?=$subTask['id'];?>" style="padding-left:20px;display: none">
                                        <div class="name">
                                            <span class="commentNo"><?=$num;?>.&nbsp;&nbsp;</span><span class="author"><strong><?=$comm['author-firstname'];?> <?=$comm['author-lastname'];?></strong></span>&nbsp;<span class="company">(<?=$comm['company-name'];?>)</span>&nbsp;&nbsp;<span class="date"><?=$dtime;?></span>
                                        </div>
                                        <div class="comments">
                                            <p><?=$comm['body'];?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php
                }
            }
            ?>
        <?php
        }

    }
    ?>
    <tbody class="tasklist-total tasklist-total-<?=$tasklist['id']?> tablesorter-no-sort time-entry" style="display: none;">
    <tr>
        <td class="total-over-under total-offset" colspan="10"></td>
        <td class="total-over-under"><?=Utilities::getEstimated($total_estimated);?></td>
        <td class="total-over-under"><?=Utilities::getEstimated($total_time);?></td>
        <td class="total-over-under"><?=Utilities::getEstimated($total_billable);?></td>
    </tr>
    <tr>
        <td class="total-over-under total-offset" colspan="10"></td>
        <td class="total-over-under left">Over/Under</td>
        <td colspan="2" class="total-over-under {{$class_over}} left"><?=Utilities::getEstimated($total_over_under);?></td>
    </tr>
    </tbody>
    </tbody>

<?php } ?>
</table>
</div>
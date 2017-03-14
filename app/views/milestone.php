<div class="row">
    <div class="col-md-12">
        <h4>Milestones and associated tasks</h4>
    </div>
</div>
<div class="row">
    <div class="col-md-9">
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
<div class="row image_loader" style="margin-top: 10%;">
      <div class="span4"></div>
        <div class="span4"><img class="center-block" src="/images/loader.gif"/></div>
      <div class="span4"></div>
</div>
<div class="table-responsive">
    <table class="table tablesorter table-bordered milestone" cellspacing="1" cellpadding="0" id="mainTable" data-projectid="<?=$total_time['id'];?>" style="display: none;">
        <colgroup>
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
        <tr class="main_header">
            <th class="sorter-false filter-false dark-header" width="10" style="text-align: center;"><input data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report" type="checkbox" name="all_milestone"/></th>
            <th width="4" class="sorter-false filter-false">&nbsp;</th>
            <th width="200" style="text-align: center">Milestone</th>
            <th width="220" style="width:220px;white-space:normal;text-align: center;">Description</th>
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
            <td rowspan="1"  class="indicator <?=$milestone['status']?>"><input data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report" type="checkbox" name="c_milestone"></td>
            <td style="text-align: center;"><a class="collapse-mil show-mil"><i class="fa fa-plus-square-o fa-lg"></i></a></td>
            <td><strong><?=$milestone['title']?></strong></td>
            <td class="description" style="width:200px;white-space:normal;"><p><?=$milestone['description']?></p></td>
            <td><?=date('d M (Y)', strtotime($milestone['deadline']))?></td>
            <td><?=$milestone['responsible-party-names']?></td>
            <td><?=ucfirst($milestone['status']);?></td>
            <td><?=$days_late;?></td>
            <td><?=$date?></td>
        </tr>
        <?php
        if(!empty($milestone['tasklists'])){
        ?>
        <tr class="tablesorter-childRow hide-mil" id="mil-<?=$milestone['id']?>" style="display: none;">
            <td colspan="9">
                <div style="margin-left: 20px;" class="tasklist_wrapper">
                    <table class="sortable table-bordered">
                        <thead class="tasklist_header">
                        <tr class="sub_header_task">
                            <th width="4" class="sorter-false filter-false" style="text-align: center;"><input data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report"  type="checkbox" name="c_tasklist_all" /></th>
                            <th width="4" class="sorter-false filter-false"></th>
                            <th class="tl">Tasklists</th>
                            <th class="description">Description</th>
                            <th>Start Date</th>
                            <th>Date Due</th>
                            <th>Assigned To</th>
                            <th>Priority</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Estimated</th>
                            <th>Timehere</th>
                            <th>Billable</th>
                        </tr>
                        </thead>
                        <?php
                        foreach ($milestone['tasklists'] as $tasklist) {
                                $tasks = $tasklist['todo-items'];
                        ?>
                            <tbody class="tablesorter-no-sort">
                            <tr data-id="<?=$tasklist['id'];?>" class="child-mtasklist-<?=$milestone['id']?> child-mtasklist"><th><input data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report" type="checkbox" name="c_tasklist"></th><th colspan="12"><?php if(count($tasks) > 0) { ?><a class="collapse-task show-task"><i class="fa fa-plus-square-o fa-lg"></i></a><?php } ?><?=$tasklist['name'];?></th></tr>
                            </tbody>
                            <tbody class="mil-task-list hide-task" id="task-<?=$tasklist['id'];?>" style="display: none;">
                            <?php
                            $total_estimated = 0;
                            $total_time = 0;
                            $total_billable = 0; 
                            if(count($tasks) > 0) {
                                 foreach($tasks as $task) {
                                         $start =  isset($task['start-date']) && $task['start-date'] !="" ? date('d M (Y)', strtotime($task['start-date'])) : "";
                                        $due =  isset($task['due-date']) ? date('d M (Y)', strtotime($task['due-date'])) : "";
                                        $progress = $task['progress'] > 0 ? $task['progress']."%" : "";
                                        $completed =  isset($task['completed_on']) ? date('d M (Y)', strtotime($task['completed_on'])) : "";
                                        $time_val = "None";
                                        $billable = "None";
                                        $status = Utilities::createStatus($task);
                                        $status_class = Utilities::createStatusClass($task);
                                        $total_estimated += (int)$task['estimated-minutes'];
                                        
                                        if($task['timeIsLogged'] && isset($time_entries[$task['id']])) {
                                             $time = $time_entries[$task['id']];
                                             $res_time = Utilities::sumTime($time);
                                             $time_val = Utilities::getEstimated($res_time['total']);
                                             $billable = Utilities::getEstimated($res_time['billable']);
                                             
                                             $total_time += $res_time['total'];
                                             $total_billable += $res_time['billable'];
                                        } 
                                  ?>
                                  <tr class="indicator <?=$status_class;?> child-row-<?=$milestone['id'];?> child-row-task" data-id="<?=$task['id'];?>">
                                        <td>&nbsp;</td>
                                        <td class="indicator <?=$status_class;?>"><input data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report" data-toggle="tooltip" data-placement="top" title="Toggle Visibility in Report"  type="checkbox" name="c_task"></td>
                                        <td><?=$task['content']?></td>
                                        <td class="<?php if(strlen(trim($task['description'])) > 0 ) { ?>description<?php } ?>"><p><?php if(strlen(trim($task['description'])) > 0 ) { ?><?=trim($task['description']);?><?php }  ?></p></td>
                                        <td><?=$start;?></td>
                                        <td><?=$due;?></td>
                                        <td><?php if(isset($task['responsible-party-names'])){ echo $task['responsible-party-names']; }?></td>
                                        <td><?=$task['priority']?></td>
                                        <td><?=$progress?></td>
                                        <td><?=$status;?> <?=$completed?></td>
                                        <td><?=Utilities::getEstimated($task['estimated-minutes']);?></td>
                                        <td><?=$time_val;?></td>
                                        <td><?=$billable;?></td>
                                  </tr>
                                   <?php
                                      if(!empty($task['subTasks'])) {
                                           foreach($task['subTasks'] as $subTask) {
                                                $start =  isset($subTask['start-date']) && $subTask['start-date'] !=""  ? date('d M (Y)', strtotime($subTask['start-date'])) : "";
                                                $due =  isset($subTask['due-date']) ? date('d M (Y)', strtotime($subTask['due-date'])) : "";
                                                $progress = $subTask['progress'] > 0 ? $subTask['progress']."%" : "";
                                                $completed =  isset($subTask['completed_on']) ? date('d M (Y)', strtotime($subTask['completed_on'])) : "";
                                                $time_val = "None";
                                                $billable = "None";
                                                $estimated = Utilities::getEstimated($subTask['estimated-minutes']);
                                                $total_estimated += (int)$subTask['estimated-minutes'];
                                                $status = Utilities::createStatus($task);
                                                $status_class = Utilities::createStatusClass($task);
                                                if($subTask['timeIsLogged'] && isset($time_entries[$subTask['id']])) {
                                                    $time = $time_entries[$subTask['id']];
                                                    $res_time = Utilities::sumTime($time);
                                                    $time_val = Utilities::getEstimated($res_time['total']);
                                                    $billable = Utilities::getEstimated($res_time['billable']);
                                                    
                                                    $total_time += $res_time['total'];
                                                    $total_billable += $res_time['billable'];

                                                }
                                       ?>
                                           <tr class="indicator <?=$status_class?> child-row-<?=$milestone['id'];?> child-row-task-<?=$task['id'];?> child-row-task">
                                                <td>&nbsp;</td>
                                                <td class="indicator <?=$status_class;?>"><input type="checkbox" name="c_task"></td>
                                                <td style="padding-left:20px;">&#8226; <?=$subTask['content']?></td>
                                                <td class="<?php if(strlen(trim($subTask['description'])) > 0 ) { ?>description<?php } ?>"><p><?php if(strlen(trim($subTask['description'])) > 0 ) { ?><?=trim($subTask['description']);?><?php }  ?></p></td>
                                                <td><?=$start?></td>
                                                <td><?=$due?></td>
                                                <td><?=$subTask['responsible-party-names']?></td>
                                                <td><?=$subTask['priority']?></td>
                                                <td><?=$progress;?></td>
                                                <td><?=$status;?> <?=$completed;?></td>
                                                <td><?=$estimated;?></td>
                                                <td><?=$time_val;?></td>
                                                <td><?=$billable;?></td>
                                           </tr>      
                                      <?php              
                                           }
                                      }
                                    ?>
                                  <?php           
                                 }
                           }
                        ?>
                            <tr class="tasklist-total milestone-total-<?=$milestone['id']?> tasklist-total-<?=$tasklist['id']?>" style="display: none;">
                                <td colspan="10"></td>
                                <td class="total"><?=Utilities::getEstimated($total_estimated);?></td>
                                <td class="total"><?=Utilities::getEstimated($total_time);?></td>
                                <td class="total"><?=Utilities::getEstimated($total_billable);?></td>
                            </tr>
                        <?php } ?>
                        </tbody> 
                          
                    </table>
                </div>
            </td>
        </tr>
<?php } } ?>
       </tbody>
</table>
</div>
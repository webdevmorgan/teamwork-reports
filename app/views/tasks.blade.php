<tbody class="mil-task-list hide-task" id="task-<?=$params['tasklist_id'];?>">
<?php
$total_estimated = 0;
$total_time = 0;
$total_billable = 0;

foreach ($tasks as $task) {
    $start =  isset($task['start-date']) ? date('d M (Y)', strtotime($task['start-date'])) : "";
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
    }

    ?>
    <tr class="indicator <?=$status_class;?> child-row-<?=$params['milestone_id'];?> child-row-task" data-id="<?=$task['id'];?>">
        <td>&nbsp;</td>
        <td class="indicator <?=$status_class;?>"><input type="checkbox" name="c_task"></td>
        <td><?=$task['content']?></td>
        <td width="200" valign="top" style="width:200px;white-space:normal" class="description"><p><?=$task['description']?></p></td>
        <td><?=$start;?></td>
        <td><?=$due;?></td>
        <td><?php if(isset($task['responsible-party-names'])){ echo $task['responsible-party-names']; }?></td>
        <td><?=$task['priority']?></td>
        <td><?=$progress?></td>
        <td><?=$status;?> <?=$completed?></td>
        <td><?=Utilities::getEstimated($task['estimated-minutes']);?></td>
        <td><?//=$time_val;?></td>
        <td><?//=$billable;?></td>
    </tr>
    <?php
    if (!empty($task['subTasks'])) {
        foreach($task['subTasks'] as $subTask) {
            $start =  isset($subTask['start-date']) ? date('d M (Y)', strtotime($subTask['start-date'])) : "";
            $due =  isset($subTask['due-date']) ? date('d M (Y)', strtotime($subTask['due-date'])) : "";
            $progress = $subTask['progress'] > 0 ? $subTask['progress']."%" : "";
            $completed =  isset($subTask['completed_on']) ? date('d M (Y)', strtotime($subTask['completed_on'])) : "";
            $time_val = "None";
            $billable = "None";
            $estimated = Utilities::getEstimated($subTask['estimated-minutes']);
            $total_estimated += (int)$subTask['estimated-minutes'];
            $status = Utilities::createStatus($task);
            $status_class = Utilities::createStatusClass($task);
            if($subTask['timeIsLogged']) {

            }
            ?>
            <tr class="indicator <?=$status_class?> child-milestone-<?=$mid;?> child-row-task-<?=$task['id'];?> child-row-task">
                <td>&nbsp;</td>
                <td class="indicator <?=$status_class;?>"><input type="checkbox" name="c_milestone"></td>
                <td style="padding-left:20px;">&#8226; <?=$subTask['content']?></td>
                <td class="description"><p><?=$task['description']?></p></td>
                <td><?=$start?></td>
                <td><?=$due?></td>
                <td><?=$subTask['responsible-party-names']?></td>
                <td><?=$subTask['priority']?></td>
                <td><?=$progress;?></td>
                <td><?=$status;?> <?=$completed;?></td>
                <td><?//=$estimated;?></td>
                <td><?//=$time_val;?></td>
                <td><?//=$billable;?></td>
            </tr>
        <?php } } ?>
<?php } ?>
</tbody>
<tbody>
<tr>
    <td colspan="10"></td>
    <td><?php //echo getEstimated($total_estimated);?></td>
    <td><?php //echo getEstimated($total_time);?></td>
    <td><?php //echo getEstimated($total_billable);?></td>
</tr>
</tbody>
@extends('layout')
@section('content')
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <h3>Members Rate</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12"><span class="right" style="display: block;"><?php echo $members->links(); ?></span></div>
        <div class="col-md-12">
        <div class="table-responsive">
            <table class="sortable table-bordered tablesorter-default sortabletable" role="grid">
                <thead>
                <tr class="main_header" role="row">
                    <th width="25%" >First Name</th>
                    <th width="25%">Last Name</th>
                    <th width="25%">Role</th>
                    <th width="25%">Rate</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($members as $member){?>
                    <tr>
                        <td><?=$member->first_name?></td>
                        <td><?=$member->last_name?></td>
                        <td><?=$member->role?></td>
                        <td><input class="members-rate currency" type="text" value="<?=$member->rate?>" data-memberid="<?=$member->id?>" name="rate" placeholder="0.00" /></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
       </div>
       <div class="col-md-12"><span class="right" style="display: block;"><?php echo $members->links(); ?></span></div>
    </div>
</div>
@stop
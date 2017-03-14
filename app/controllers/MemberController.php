<?php
class MemberController extends BaseController {

    public function rateAction() {
        Requirement::javascripts(
            array(
                '/js/members.js'
            ));
        $members = Member::whereNotNull('tw_id')->orderBy('created_at', 'DESC')->paginate(30);


        return View::make('members.rates')->with(array('members' => $members));
    }

    public function updateAction(){
        $formData = Input::all();
        $member = Member::find($formData['id']);
        $member->fill($formData);
        $member->save($formData);
    }
}
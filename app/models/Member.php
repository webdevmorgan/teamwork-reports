<?php
/**
 * Created by PhpStorm.
 * User: Precy
 * Date: 4/3/14
 * Time: 10:21 AM
 *
 */

class Member extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'members';
    protected $softDelete = true;
    protected $fillable = array(
        'tw_id',
        'first_name',
        'last_name',
        'role',
        'qb_id',
        'rate'
    );
}
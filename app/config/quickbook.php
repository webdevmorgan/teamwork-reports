<?php
$db_name = Config::get('database.connections.mysql.database');
$db_username = Config::get('database.connections.mysql.username');
$db_password = Config::get('database.connections.mysql.password');
return array(
    'token'=> 'ed675b43ba900b4d66bb5f6b781d7d7f4930',
    'oauth_consumer_key' => 'qyprdfmYNK05m6HrThBxeJUUj4jIf0',
    'oauth_consumer_secret'=>'eofqnzVW6JmCeMJ8yp5O4UBnrPBo7d3PTmPcHpkP',
    'quickbooks_oauth_url' => 'http://'.$_SERVER['HTTP_HOST'].'/quickbook/oauth',
    'quickbooks_success_url' =>'http://'.$_SERVER['HTTP_HOST'].'/quickbook/success',
    'quickbooks_menu_url' => 'http://'.$_SERVER['HTTP_HOST'].'/quickbook/menu',
    'encryption_key' => 'bcde1234',
    'db_quickbooks' => 'mysqli://'.$db_username.':'.$db_password.'@localhost/'.$db_name,
    'the_username'=> 'DO_NOT_CHANGE_ME',
    'the_tenant' => '1292763235'
);
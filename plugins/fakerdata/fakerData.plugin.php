<?php

/*
Plugin Name: Faker data
Plugin URI: https://github.com/atmoner
Description: This is a faker data 
Version: 1.0
Author: Atmoner
Author URI: https://github.com/atmoner
*/


//set plugin id as file name of plugin
$plugin_id = basename(__FILE__);

//some plugin data
$data['name'] = "Faker data";
$data['author'] = "Atmoner";
$data['url'] = "https://github.com/atmoner";

//register plugin data
register_plugin($plugin_id, $data);


//plugin function
function fakerdata() {
        global $hook; 
        $hook->add_admin_page('fakerdata','plugins/fakerdata/php/admin/fakerdata.php','plugins/fakerdata/html/admin/fakerdata.html');
}

function addnewadminmenu_faker() {
        global $hook; 
		$hook->add_admin_menu('addmensssu', 'Faker data', 'admincp/fakerdata',5);
}



add_hook('admin_action','addnewadminmenu_faker');
add_hook('new_admin_page','fakerdata');
 

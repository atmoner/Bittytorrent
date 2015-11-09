<?php

/*
Plugin Name: Donate paypal plugin
Plugin URI: http://bittytorrent.com/
Description: Create Donate button on all pages.
Version: 1.0
Author: Atmoner
Author URI: http://bittytorrent.com/
*/


//set plugin id as file name of plugin
$plugin_id = basename(__FILE__);

//some plugin data
$data['name'] = "Donate paypal plugin";
$data['author'] = "Atmoner";
$data['url'] = "http://bittytorrent.com/";

//register plugin data
register_plugin($plugin_id, $data);

function install_donate() {
	global $db,$plugin_id;
	$db->query("INSERT INTO `settings`(`key`, `value`) VALUES ('donate','Y6458Z2DDGX7W')");
	$db->query("UPDATE plugins SET installed_sql='true' WHERE filename='".basename(__FILE__)."'");
}

function uninstall_donate() {
	global $db,$plugin_id;
	$db->query("DELETE FROM `settings` WHERE `key` = 'donate'");
	$db->query("UPDATE plugins SET installed_sql='false' WHERE filename='".basename(__FILE__)."'");
}



function donate() {
        global $hook,$conf; 
 
        $content = '<div align="center">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="'.$conf['donate'].'">
					<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
					<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
					</form>
					</div>';
        
		$hook->add_side_block('donateP', 'Donate',$content,5); 	
}
 

add_hook('action','donate');
 


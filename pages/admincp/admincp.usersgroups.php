<?php
/*
___.   .__  __    __            __                                      __   
\_ |__ |__|/  |__/  |_ ___.__._/  |_  __________________   ____   _____/  |_ 
 | __ \|  \   __\   __<   |  |\   __\/  _ \_  __ \_  __ \_/ __ \ /    \   __\
 | \_\ \  ||  |  |  |  \___  | |  | (  <_> )  | \/|  | \/\  ___/|   |  \  |  
 |___  /__||__|  |__|  / ____| |__|  \____/|__|   |__|    \___  >___|  /__|  
     \/                \/                                     \/     \/      
     
     
Contact:  contact.atmoner@gmail.com     

This file is part of Bittytorrent.

Bittytorrent is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Bittytorrent is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Bittytorrent.  If not, see <http://www.gnu.org/licenses/>. 
          
*/

/*$test = $db->get_results("SELECT * FROM users_group");
 
foreach ( $db->get_col_info()  as $group ) {
 if ($group != 'id' && $group != 'group') {
 	$hook->admin_users_groups($group, $group, '5'); 		
 }            
}
*/

if (!defined("IN_TORRENT"))
      die("Access denied!");

$smarty->assign('getAllgroups',$startUp->getAllgroups());

if (isset($_POST['name'])) {
	$pk = $_POST['pk'];
	$name = $_POST['name'];
	$value = $_POST['value'];
	
	$db->query("UPDATE `users_group` SET $name = '$value' WHERE `id` =$pk");
exit;
}
/*
foreach ($items as $obj) {
	$array['id'] = $obj->id;
	$hook->admin_users_groups('view_torrent','View torrent', '5');
} 

$hook->admin_users_groups('view_torrent','View torrent', '5');
$hook->admin_users_groups('edit_torrent','Edit torrent', '5');
$hook->admin_users_groups('delete_torrent','Delete torrent', '5');
*/
 


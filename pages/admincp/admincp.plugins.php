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

 
if (!defined("IN_TORRENT"))
      die("Access denied!"); 
 
$sub=(isset($_GET["sub"])?$_GET["sub"]:"");

if (!empty($sub)) {

	
} else {


	function getPlugins($where=NULL,$value=NULL){
		global $db;	
		$sql = "SELECT filename, action FROM plugins";
		if($where === 'action')
		$sql .= " WHERE action = '$value' ";
		if($where === 'filename')
		$sql .= " WHERE filename = '$value' ";
		$items = $db->get_results($sql);
		
		foreach ( $items as $obj ){
        		$array[$obj->filename]['filename'] = $obj->filename;
        		$array[$obj->filename]['action'] = $obj->action;
	        }
 
		return $array;
	}

$action = (isset($_GET["action"])?$_GET["action"]:"");
$_GET['filename'] = $db->escape($_GET['filename']);
switch ($action) {

	case "deactivate" :
	
 		$ext = explode('.',$_GET['filename']);
		if(function_exists('uninstall_'.$ext[0])) { 
			$function = 'uninstall_'.$ext[0];
			$function();
		}  
		
		$db->query("UPDATE plugins SET action='0' WHERE filename= '".$_GET ['filename']."'");
		$startUp->redirect($conf['baseurl'].'/admincp/plugins/?tokenAdmin='.$_COOKIE['tokenAdmin']);
		break;
		
	case "activate" :
		$count = count (getPlugins('filename',$_GET['filename']));
		if ($count < 1) {
			$db->query("INSERT INTO plugins (filename, action) VALUES ('".$_GET['filename']."',1)");
		} else {
			$db->query("UPDATE plugins SET action='1' WHERE filename= '".$_GET['filename']."'");
		}		
		$startUp->redirect($conf['baseurl'].'/admincp/plugins/?action=installSql&filename='.$_GET['filename'].'&tokenAdmin='.$_COOKIE['tokenAdmin']);		
		break;
		
	case "installSql" :
 		$ext = explode('.',$_GET['filename']);
		if(function_exists('install_'.$ext[0])) { 
			$function = 'install_'.$ext[0];
			$function();
		}  
		$startUp->redirect($conf['baseurl'].'/admincp/plugins/?tokenAdmin='.$_COOKIE['tokenAdmin']);
		break;
}
 
 
$plugin_list = new phphooks();
$plugin_headers = $plugin_list->get_plugins_header();

$api=array();
$i=0;
 
  foreach ($plugin_headers as $tid=>$plugin_header) { 
			$action = false;		
	foreach ( getPlugins() as $result_row )  
		if ($plugin_header['filename'] == $result_row['filename'] && $result_row['action'] == 1)
			$action = true;
			

		   
		if ($action)
			$api[$i]["active"]="class='active'";
			else
			$api[$i]["active"]="";
		// Name
		$api[$i]["Name"]=$plugin_header['Name'];
		$api[$i]["Version"]=$plugin_header['Version'];
		$api[$i]["Description"]=$plugin_header['Description'];
		$api[$i]["AuthorURI"]=$plugin_header['AuthorURI'];
		$api[$i]["Author"]=$plugin_header['Author'];
		if ($action) {
			$api[$i]["linkAdd"]='<i class="icon-minus-sign"></i> <a href="?action=deactivate&filename=' . $plugin_header['filename'] . '&tokenAdmin='.$_COOKIE['tokenAdmin'].'" title="DESACTIVATE">Desactivate</a>';
			$api[$i]["Use"]='Use it !';
			} else {
			$api[$i]["linkAdd"]='<i class="icon-ok-sign"></i> <a href="?action=activate&filename=' . $plugin_header['filename'] . '&tokenAdmin='.$_COOKIE['tokenAdmin'].'" title="ACTIVATE">Activate</a>';
			$api[$i]["Use"]='';			 
   		}
   
  $i++;   
  } 
$smarty->assign("api",$api);  
 }
 
$hook->addJs('dataTables','jquery.dataTables.js','themes/asset/js/','2');
$hook->addJs('datatablesjs','datatables.js','themes/asset/js/','3'); 


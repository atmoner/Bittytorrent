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
      
$smarty->assign('returnUpdate',false);
  
if (isset($_GET['updateNow'])) {

require($path.'/libs/Update.class.php');

$update = new AutoUpdate(true);
$update->currentVersion = 2; //Must be an integer - you can't compare strings
$update->updateUrl = 'http://localhost/libs'; //Replace with your server update directory

//Check for a new update
$latest = $update->checkUpdate();

if ($latest !== false) {
// var_dump($update->currentVersion);
	if ($latest > $update->currentVersion) {
		//Install new update
		$rUpdate = "";
		$rUpdate .=  "New Version: ".$update->latestVersionName."<br />";
		$rUpdate .= "Installing Update...<br />";
		if ($update->update()) 
			$rUpdate .= "Update successful!";
		else  
			$rUpdate .= "Update failed!";		
	}
	else  
		$rUpdate .= "Current Version is up to date";
}
else 
	echo $update->getLastError(); 

$smarty->assign('returnUpdate',$rUpdate);

}  else {

 
	$version = $db->get_row("SELECT value FROM `settings` WHERE `key` LIKE 'version'"); 
	$currentVObj = unserialize($version->value);
 	$smarty->assign('currentV',$currentVObj['version']);
 	$smarty->assign('currentC',$currentVObj['commit']); 
 	
 
 	
 	 
	$lasteV = $startUp->get_web_page('https://raw.githubusercontent.com/atmoner/Bittytorrent/master/version?access_token=00d6ca368c3f725bb1ba63663debd5dc212da0c2');
	$jsonObj = json_decode($lasteV['content']);
	$smarty->assign('lasteV',$jsonObj->version);
	$smarty->assign('lasteC',$jsonObj->commit);
	$smarty->assign('updateValue',$jsonObj->updateValue);
	$smarty->assign('updateMessage',$jsonObj->updateMessage);
 

}


	

	

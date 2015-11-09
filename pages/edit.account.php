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

if (!defined("IN_TORRENT")) die("Access denied!");

$startUp->isLoggedAcount(); 

if (!isset($_COOKIE['token']) 
	|| empty($_COOKIE['token']) 
	|| !isset($_GET['token']) 
	|| empty($_GET['token']) 
	|| $_GET['token'] != $_COOKIE['token']
) 
    $startUp->redirect($conf['baseurl']);

if ($hook->hook_exist('edit_account_page'))
	$hook->execute_hook('edit_account_page');
	
$hook->add_block('defaultEditAccount', '', '',740,3); 	

if (isset($hook->addblock['defaultEditAccount'])) {
	if (isset($_POST['submit'])) {
		if (!empty($_POST['mail'])) {
			if ($startUp->checkMail($_POST['mail'])) {
				$startUp->EditUserInfo('',$_POST['mail'],$_POST['showMail'],$_POST['seeMytorrents'],$_POST['location'],$_POST['website'],$_POST['signature']);		
				$smarty->assign("errormail",false);	
				$smarty->assign("syntaxmail",false);	
			
			} else
				$smarty->assign("syntaxmail",true);	

		} else 
			$smarty->assign("errormail",true);	
	}  
	
	$getUserdata = $startUp->getUserdata();
	$smarty->assign("getUserdata",$getUserdata);
	$smarty->assign("getGravatar",$startUp->get_gravatar($getUserdata->mail));
}
$hook->set_title('title_editaccount', $lang["titleEdit"]); 
$hook->add_side_block('defaultBlock_Account','','', 3); 


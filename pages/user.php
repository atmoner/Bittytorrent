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

if ($userData->view_users != 'true')
        $startUp->setError('You do not have the required permissions to view the user detail');

if ($hook->hook_exist('user_page'))  
		$hook->execute_hook('user_page');
		

$hook->add_side_block('defaultBlock_Categories','','', 3);
$hook->add_block('infoUser', '', '','450-left',10);  
      
if (!isset($_GET['act']))
	$startUp->redirect($conf['baseurl']);

$getUserdata = $startUp->getUserdata($_GET['act']);
 
if ($getUserdata != 'false')
{

if(!isset($_GET["sortedBy"])) $_GET["sortedBy"] ='';
		$sortedBy = $_GET["sortedBy"];

if(!isset($_GET["axis"])) $_GET["axis"] ='';
		$axis = $_GET["axis"];

// required connect
SmartyPaginate::connect();
// set items per page
SmartyPaginate::setLimit(10);

if (!isset($_GET['next']))
	SmartyPaginate::reset(); // reset/init the session data all time!
 
SmartyPaginate::setUrl('user/'.$getUserdata->name);
$startUp->paginatePage = 'user/'.$getUserdata->name;

	//$hook->set_title('title_user', $getUserdata->name .' detail'); 
	if ($getUserdata->seeMytorrents === 'true') {
		$hook->add_block('getMytorrents', '', '','270-right',12);
	}	
$smarty->assign('getUserdata',$getUserdata);
$smarty->assign("getMyTorrents",$startUp->getTorrentsByUser($_GET['act'],$_GET["sortedBy"],$_GET["axis"])); 
$smarty->assign("getGravatar",$startUp->get_gravatar($getUserdata->mail));

} else {

// $hook->set_title('title_user','User not found!'); 

}

$hook->set_title('title_user','User detail'); 




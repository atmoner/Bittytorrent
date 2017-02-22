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

$smarty->assign('thisisNotYourtorrent',false);
$smarty->assign('NotrightsTodelete',false);
$smarty->assign('notFound',false);
$smarty->assign('errorHash',false);
$smarty->assign('torrentDelete',false);
$smarty->assign('notAuth',false);

if ($userData->delete_torrents != 'false') {        
	if (isset($_GET['id']) && strlen($_GET['hash']) == '40') {
		$torrentData = $db->get_row("SELECT id, userid, info_hash FROM torrents WHERE id = '".$db->escape($_GET['id'])."'");
		if ($torrentData) {
			// Check if torrent belongs to the user OR if user is admin for delete torrent
			if ($userData->delete_torrents != 'false' || $userData->admin_access === 'true') {
				if ($torrentData->userid === $startUp->uid || $userData->admin_access === 'true') {
					$db->get_row("DELETE FROM torrents WHERE id = '".$db->escape($torrentData->id)."'");	
					$smarty->assign('torrentDelete',true);				
				} else
					$smarty->assign('thisisNotYourtorrent',true);
			
			} else 
				$smarty->assign('NotrightsTodelete',true);	
	 	
		} else
			$smarty->assign('notFound',true);	
	} else
		$smarty->assign('errorHash',true); 
} else
	$smarty->assign('notAuth',true); 

$hook->add_side_block('defaultBlock_Categories','','', 3); 

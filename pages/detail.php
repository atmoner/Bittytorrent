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

if ($userData->view_torrents != 'true')
        $startUp->setError('You do not have the required permissions to view the full torrents');

if ($hook->hook_exist('detail_page'))
	$hook->execute_hook('detail_page');

$hook->add_block('defaultUpload', '', '',740,3); 
$hook->add_block('torrentInfo', '', '',740,4); 
$hook->add_block('torrentDownload', '', '',740,5); 
$hook->add_block('torrentTools', '', '',740,6); 
$hook->add_content('defaultButton', '',1); 

$smarty->assign('torrentNotfound',true);
$smarty->assign('canViewtools',false);
 
if (isset($hook->addblock['defaultUpload'])) {

	$getDetail = $startUp->getTorrent($_GET['id']);

	if ($getDetail != 'NULL') {
	
		$smarty->assign('getDetail',$getDetail);
		$smarty->assign('urlScrape',$startUp->makeUrl(array('page'=>'scrape','hash'=>$getDetail['info_hash'])));
		$smarty->assign('torrentNotfound',false); 
		if ($getDetail['userid'] === $startUp->uid || $userData->admin_access === 'true')  
			$smarty->assign('canViewtools',true);
	
	} else
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); // Set 404, no reference to anything on the search engines!

	if (file_exists($path.'/uploads/images/'.$getDetail['info_hash'].$getDetail['imgExt'])) 
		$smarty->assign('imgExist',true);
	else
		$smarty->assign('imgExist',false);

}

$hook->add_side_block('defaultBlock_Categories','','', 3); 

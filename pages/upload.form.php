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

require_once $path.'/libs/class.bdecode.php';
require_once $path.'/libs/class.bencode.php'; // To create info hash of torrent


 

$torrent = new BDECODE($path.'/uploads/torrents/'.$_GET['info_hash'].'.torrent');
$resultTorrent = $torrent->result;
$hash = sha1(BEncode($resultTorrent['info']));

// var_dump($resultTorrent['info']);

	
	// Calcul the total size of content torrent
	if (isset($resultTorrent["info"]) && $resultTorrent["info"]) $upfile=$resultTorrent["info"];
		else $upfile = 0;

	if (isset($upfile["length"]))
	{
	  $size = (float)($upfile["length"]);
	}
	else if (isset($upfile["files"]))
		 {
	// multifiles torrent
		     $size=0;
		     foreach ($upfile["files"] as $file)
		             {
		             $size+=(float)($file["length"]);
		             }
		 }
	else
		$size = "0";
		
		
          $smarty->assign('descTorrent',true);
          $smarty->assign('uploadTorrent',false);
          $smarty->assign('saveTorrent',false);
          $smarty->assign('torrentHash',$hash);
          $smarty->assign('torrentBytestoSize',$startUp->bytesToSize($size));
          $smarty->assign('torrentSize',$size);
          $smarty->assign('torrentUrlname',preg_replace("/[^a-zA-Z0-9]+/", "-", strtolower($resultTorrent['info']['name'])));          
          $smarty->assign('resultTorrent',$resultTorrent); 
          $smarty->assign('errorFileTorrent',false);
          $smarty->assign('errorUploadTorrent',false);		
          
          
		 // Categories list part
 foreach ($startUp->Categories('getlist',0) as $obj) {
 
	$array[$obj['id']]['id'] = $obj['id'];
	$array[$obj['id']]['prefix'] = $obj['prefix'];
	$array[$obj['id']]['c_name'] = $obj['c_name'];
	
	 if ($getSubCat = $startUp->Categories('getsubcat',$obj['id']))
	 {
	 	foreach ($getSubCat as $objgetSubCat) {
	 		// var_dump($objgetSubCat['c_name'] );
	 		$array[$objgetSubCat['id']]['subCat']['c_name'] = $objgetSubCat['c_name'];
	 		$array[$objgetSubCat['id']]['subCat']['is_child_of'] = $obj['id'];
	 	}
	 }
}    

		$smarty->assign('getAllCat',$array);          

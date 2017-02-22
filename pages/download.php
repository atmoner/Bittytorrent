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
 
if ($userData->can_download != 'true')
		die('You do not have the required permissions to download this torrents');

if (isset($_GET['hash'])) {

	$torrent = $db->get_row("SELECT id, title, info_hash FROM torrents WHERE info_hash = '".$db->escape($_GET['hash'])."'");
		
	if($torrent){		

		require_once $path.'/libs/class.bdecode.php';
		require_once $path.'/libs/class.bencode.php';

		$fd = fopen($path.'/uploads/torrents/'.$torrent->info_hash.'.torrent', "rb");
		$mainTorrent = fread($fd, filesize($path.'/uploads/torrents/'.$torrent->info_hash.'.torrent'));
		// $array = new BDecode($mainTorrent);
		
		$torrentDecode = new BDECODE($path.'/uploads/torrents/'.$torrent->info_hash.'.torrent');
		$array = $torrentDecode->result; 
 
		$array["announce"] = $conf['baseurl']."/announce?pid=".$userData->private_id;
 
		if (isset($array["announce-list"]) && is_array($array["announce-list"]))
		   {
		   for ($i=0;$i<count($array["announce-list"]);$i++)
		       {
		       for ($j=0;$j<count($array["announce-list"][$i]);$j++)
		           {
		           if (in_array($array["announce-list"][$i][$j],$conf['baseurl']."/announce"))
		              {
		              if (strpos($array["announce-list"][$i][$j],"announce.php")===false)
		                 $array["announce-list"][$i][$j] = trim(str_replace("/announce", "/announce/".$userData->private_id, $array["announce-list"][$i][$j]));
		              else
		                 $array["announce-list"][$i][$j] = trim(str_replace("/announce.php", "/announce.php?pid=$pid", $array["announce-list"][$i][$j]));
		            }
		         }
		     }
		 }
     		$mainTorrent=BEncode($array);
		fclose($fd);
		header("Content-Type: application/x-bittorrent");
		header('Content-Disposition: attachment; filename="['.$conf['title'].'] '.$torrent->title.'.torrent"');
		print($mainTorrent);  
 		
	} else {
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); // Set 404, no reference to anything on the search engines!
		// $smarty->assign('notFound',true);
	}
}

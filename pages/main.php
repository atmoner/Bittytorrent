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

$hook->add_side_block('defaultBlock_Categories','','', 3); 
$hook->add_block('defaultIndex', '', '',740,10); 

if ($hook->hook_exist('home_page'))
	$hook->execute_hook('home_page');
	
 

 if (isset($hook->addblock['defaultIndex'])){
 

		 // Categories list part
		 foreach ($startUp->Categories('getlist',0) as $obj) {
	
 
			$positions = explode(">",$obj['position']);

			if (!isset($positions[1]) OR empty($positions[1])) { // is root categorie

			$array[$obj['id']]['id'] = $obj['id'];
			$array[$obj['id']]['c_name'] = $obj['c_name'];
			$array[$obj['id']]['url_strip'] = $obj['url_strip'];


		$sql = "SELECT format(finished,0) as finished, c.position, c.c_name, c.c_icon, users.name, torrents.* FROM ".$startUp->prefix_db."torrents ";	
		$sql .= "INNER JOIN ".$startUp->prefix_db."categories as c ON torrents.categorie=c.id ";
		$sql .= "INNER JOIN ".$startUp->prefix_db."users ON torrents.userid=users.id ";	
		$sql .= "WHERE leechers + seeds > 0 AND categorie = '".$obj['id']."' OR c.position RLIKE '^".$obj['id'].">[0-9]+>$' ORDER BY CAST(finished AS UNSIGNED) DESC LIMIT 9 ";	
 
		// $sql .= "WHERE leechers + seeds > 0 AND categorie = '".$obj['id']."' ORDER BY CAST(finished AS UNSIGNED) DESC LIMIT 9 "; 
 
		$items = $db->get_results($sql);
 
 
	 	if ($items) { 
		foreach ($items as $obj) {
		
 		$positions = explode(">",$obj->position);
 

			 	$array[$positions[0]]['files'][$obj->id]['id'] = $obj->id;
			 	$array[$positions[0]]['files'][$obj->id]['uname'] = $startUp->Fuckxss($obj->name);
			 	$array[$positions[0]]['files'][$obj->id]['uname_url'] = $conf['baseurl'].'/'.$startUp->makeUrl(array('page'=>'user','act'=>$obj->name)).'/';
			 	$array[$positions[0]]['files'][$obj->id]['c_name'] = $startUp->Fuckxss($obj->c_name);
			 	$array[$positions[0]]['files'][$obj->id]['c_icon'] = $startUp->Fuckxss($obj->c_icon); 	
			 	$array[$positions[0]]['files'][$obj->id]['title'] = $startUp->Fuckxss($obj->title);	
         		$array[$positions[0]]['files'][$obj->id]['info_hash'] = $obj->info_hash;
				$array[$positions[0]]['files'][$obj->id]['torrent_desc'] = $obj->torrent_desc;  
           		$array[$positions[0]]['files'][$obj->id]['date'] = $obj->date;
           		$array[$positions[0]]['files'][$obj->id]['hits'] = $obj->hits;
           		$array[$positions[0]]['files'][$obj->id]['seeds'] = $obj->seeds;
           		$array[$positions[0]]['files'][$obj->id]['leechers'] = $obj->leechers;
           		$array[$positions[0]]['files'][$obj->id]['finished'] = $obj->finished;
           		$array[$positions[0]]['files'][$obj->id]['size'] = $startUp->bytesToSize($obj->size);  		
           		$array[$positions[0]]['files'][$obj->id]['torrentUrl'] = $startUp->makeUrl(array('page'=>'torrent-detail','id'=>$obj->id,'urlTitle'=>$startUp->Fuckxss($obj->url_title)));
	        }
 
 
	 	} else
	 		$arrayTop = '';			
 
			
			}
		}  

		$smarty->assign('getAllMainCat',$array);

 
 } // If hook 
 




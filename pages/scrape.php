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
 
if (isset($_GET['info_hash'])) {

	$torrent = $db->get_row("SELECT id, announce, info_hash FROM torrents WHERE info_hash = '".$db->escape($_GET['info_hash'])."'");

	if($torrent){		
		$returnError = "";
		$smarty->assign('notFound',false);
 		$annouce = unserialize($torrent->announce);  
		// First, we clear torrent stats 
		$startUp->clearScrape($torrent->info_hash);
		// Set errorScrape to false
 		$smarty->assign('returnScrape',false);
 
		if (is_array($annouce)) {
			foreach ($annouce as $key => $value) {
				$returnError .= $startUp->torrentScrape($value[0],$torrent->info_hash);		
			}	
			$smarty->assign('returnScrape',$returnError);		
		} else {
	 
				$smarty->assign('soloScrape',$startUp->torrentScrape($annouce,$torrent->info_hash));	
		}
		
 


		// Traitements
		$NewTorrentvalue = $db->get_row("SELECT id, seeds, leechers, finished, last_scrape FROM torrents WHERE info_hash = '".$db->escape($torrent->info_hash)."'");
		$retour = array(
			'chaine'    => $NewTorrentvalue,
			'lastScrape'      => date('j F Y H:i:s', $NewTorrentvalue->last_scrape),
			'errorScrape'      => $returnError
		);
 
		 
		// Envoi du retour (on renvoi le tableau $retour encodé en JSON)
		header('Content-type: application/json');
		echo json_encode($retour);
		exit;
		// var_dump($torrent);
 		// $startUp->redirect($conf['baseurl'].'/'.$startUp->makeUrl(array('page'=>'torrent-detail','id'=>$torrent->id)));
	} else {
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); // Set 404, no reference to anything on the search engines!
		$smarty->assign('notFound',true);
	}
}


 


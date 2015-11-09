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

/* if ($hook->hook_exist('torrents_page'))
	$hook->execute_hook('torrents_page'); */
	
$hook->add_block('defaultTorrents', '', '',740,10); 
$hook->add_side_block('defaultBlock_Categories','','', 3); 


 
if (isset($hook->addblock['defaultTorrents'])){
 	
	if(!isset($_GET["sortedBy"])) $_GET["sortedBy"] ='';
	if(!isset($_GET["axis"])) $_GET["axis"] ='';
 

 		$sortedList = array('title','date','size','seeds','leechers');
 
		foreach ($sortedList as $arr) {
	     		$array[$arr]['id'] = $arr;
	     		if ($cat_id!='NULL') 
	    		$array[$arr]['url'] = $startUp->makeUrl(array('page'=>'torrents','gost'=>'cat','catid'=>$cat_id,'sortedBy'=>$arr,'axis'=>($_GET["axis"] == 'asc' ? 'desc' : 'asc'))); 
		     	else
		     	$array[$arr]['url'] = $startUp->makeUrl(array('page'=>'torrents','sortedBy'=>$arr,'axis'=>($_GET["axis"] == 'asc' ? 'desc' : 'asc'))); 
		} 		 
 
 

 
	// required connect
	SmartyPaginate::connect();
	// set items per page
	SmartyPaginate::setLimit(25);

	if (!isset($_GET['next']))
		SmartyPaginate::reset(); // reset/init the session data all time!
 
	// assign your db results to the template
	$smarty->assign('results',$startUp->getTorrents($cat_id,$_GET["sortedBy"],$_GET["axis"]));
	SmartyPaginate::assign($smarty);	
	if (!empty($_GET["search"]))
		$startUp->paginatePage = 'torrents/search/'.$_GET["search"];
	else
		$startUp->paginatePage = 'torrents';
	$smarty->assign('sortedList',$array);

}


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

if(!isset($_GET["catid"])) $_GET["catid"] =0;

$ctg_id = $_GET["catid"];

if (isset($_POST['act'])) 	{
 	if ($_POST['act'] === "add") {
 		$startUp->Categories('add',$_POST);
    }
 	if ($_POST['act'] === "update") {
 		$startUp->Categories('update',$_POST); 
    }
}

if (isset($_GET['delCat'])) {

		if (isset($_GET['cat_action'])) {
				$dial = $startUp->Categories('delete',$_GET); 
		} else 
			$dial = 'Please select an action';
			
		// Envoi du retour (on renvoi le tableau $retour encodé en JSON)
		header('Content-type: application/json');
		$retour = array(
			'chaine'    => $dial
		);
		echo json_encode($retour);
		exit;	
}

if (isset($_GET['catid'])) {
 	$getOne = $startUp->Categories('getOne',$_GET['catid']);
 	$smarty->assign('getOne',$getOne);
 	$smarty->assign('getParent',$startUp->Categories('getParent',$getOne->position));
	
}
// $startUp->Categories('getlist',$_GET['id']) ;
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
$smarty->assign('outputHtmlCat',$startUp->Categories('html',$ctg_id,true));
 

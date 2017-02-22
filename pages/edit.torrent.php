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

if (!$startUp->isLogged())
        $startUp->redirect($conf['baseurl']);

if ($userData->edit_torrents != 'true')
        $startUp->setError('You do not have the required permissions to edit torrents');

        
        
$smarty->assign('errorUpdateTorrent','false');
$smarty->assign('succesUpdateTorrent','false');

$tId = (isset($_GET["id"])?$_GET["id"]:"");


if (isset($_POST['updateTorrent'])) {

	if (!empty($_POST['torrentTitle'])){
		if (!empty($_POST['torrentUrlTitle'])){
			if (!empty($_POST['torrentDesc'])){
				if (!empty($_POST['categories'])){
					// Check if image existe
  						
						if (!empty($_FILES['image']['name'])) {
						
							$detailTorrent = $db->get_row("SELECT info_hash FROM torrents WHERE id ='".$db->escape($tId)."'");
							$valid_ext = array( '.jpg' , '.jpeg' , '.gif' , '.png' );
							$ext = strrchr($_FILES['image']['name'], '.'); 
					 
							if (in_array($ext,$valid_ext)) {
								if(!move_uploaded_file($_FILES['image']['tmp_name'], $path.'/uploads/images/'.$detailTorrent->info_hash.$ext))
									$smarty->assign('errorUploadimg',true);
								else
									$smarty->assign('errorUploadimg',false);		
							} else
								 $smarty->assign('imgNotAutorised',true);
						} else
							$ext = '';	
 
					$startUp->editTorrent(
								$_POST['torrentId'],
								$_POST['torrentTitle'],
								$_POST['torrentUrlTitle'],
								$_POST['torrentDesc'],
								$_POST['categories'],
								$ext
					);	
					$smarty->assign('succesUpdateTorrent','Torrent update');
					$smarty->assign('errorUpdateTorrent','false');
					$getUpdate = $startUp->getTorrent($_POST['torrentId']); // Get new info torrent after update
					$smarty->assign('torrentUrl',$startUp->makeUrl(array('page'=>'torrent-detail','id'=>$getUpdate['id'],'urlTitle'=>$getUpdate['url_title'])));
							
				} else	
					$smarty->assign('errorUpdateTorrent','Categories can not be empty'); // $error = 'Categories can not be empty';		
			} else	
				$smarty->assign('errorUpdateTorrent','Description can not be empty'); // $error = 'Description can not be empty';				
		} else	
			$smarty->assign('errorUpdateTorrent','Url title can not be empty'); // $error = 'Url title can not be empty';		
	} else	
		$smarty->assign('errorUpdateTorrent','Torrent title can not be empty'); // $error = 'Torrent title can not be empty';	
 
}


// Categories list part
foreach ($startUp->Categories('getlist',0) as $obj) {
 
	$array[$obj['id']]['id'] = $obj['id'];
	$array[$obj['id']]['prefix'] = $obj['prefix'];
	$array[$obj['id']]['c_name'] = $obj['c_name'];
	
	 if ($getSubCat = $startUp->Categories('getsubcat',$obj['id']))
	 {
	 	foreach ($getSubCat as $objgetSubCat) {
	 		$array[$objgetSubCat['id']]['subCat']['c_name'] = $objgetSubCat['c_name'];
	 		$array[$objgetSubCat['id']]['subCat']['is_child_of'] = $obj['id'];
	 	}
	 }
}    

$smarty->assign('getAllCat',$array);   


$getDetail = $startUp->getTorrent($tId);
$smarty->assign('getDetail',$getDetail);
$smarty->assign('urlScrape',$startUp->makeUrl(array('page'=>'scrape','hash'=>$getDetail['info_hash'])));


$hook->add_side_block('defaultBlock_Categories','','', 3); 
$hook->addJs('scriptsummernote','themes/asset/js/summernote.js','','5');
$hook->addCss('csssummernote','themes/asset/css/summernote.css','','5');
$hook->addJs('jsjasny','themes/asset/js/jasny-bootstrap.min.js','','7');
$hook->addCss('cssjasny','themes/asset/css/jasny-bootstrap.min.css','','6');


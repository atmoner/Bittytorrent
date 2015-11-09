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
 
if ($userData->can_upload != 'true')
        $startUp->setError('You do not have the required permissions to upload new torrents');

if ($hook->hook_exist('upload_page'))
	$hook->execute_hook('upload_page');

$hook->add_block('defaultUpload', '', '',740,10); 
$hook->add_side_block('defaultBlock_Upload','','', 3); 
$hook->addJs('scriptUpload','themes/asset/js/script.upload.js','','2');
$hook->addJs('scriptsummernote','themes/asset/js/summernote.js','','5');
$hook->addJs('filestyle','themes/asset/js/bootstrap-filestyle.js','','6');
$hook->addJs('jsjasny','themes/asset/js/jasny-bootstrap.min.js','','7');
$hook->addCss('csssummernote','themes/asset/css/summernote.css','','5');
$hook->addCss('cssjasny','themes/asset/css/jasny-bootstrap.min.css','','6');
 

if (isset($hook->addblock['defaultUpload'])){
 
require_once $path.'/libs/class.bdecode.php';
require_once $path.'/libs/class.bencode.php'; // To create info hash of torrent

$data = array();

if(isset($_GET['files'])) {	
 
	$files = array();

	$uploaddir = $path.'/uploads/torrents/';
	foreach($_FILES as $file) {

	if(in_array(strrchr($file['name'], '.'), array('.torrent'))) {
	
	$torrent = new BDECODE($file['tmp_name']);
	$resultTorrent = $torrent->result;
	$hash = sha1(BEncode($resultTorrent['info']));
	
	if ($conf['open_tracker'] === 'false') {
		   if ($resultTorrent["announce"] === $conf['baseurl'].'/announce') {
			if (is_uploaded_file($file['tmp_name'])) {	
				if(move_uploaded_file($file['tmp_name'], $uploaddir .$hash.'.torrent')) {
					$files[] = $uploaddir.$file['name'];
					$data = array('files' => $files, 'info_hash' => $hash);
				} else
			 		$data = array('error' => $lang["errorMove"]);
		 	} else
		 		$data = array('error' => $lang["errorIsuploaded"]);
		    } else
		   	    $data = array('error' => $lang["errorPrivate"].' => <strong>'.$conf['baseurl'].'/announce</strong>');
		
	} else {

			if (is_uploaded_file($file['tmp_name'])) {	
				if(move_uploaded_file($file['tmp_name'], $uploaddir .$hash.'.torrent')) {
					$files[] = $uploaddir.$file['name'];
					$data = array('files' => $files, 'info_hash' => $hash);
				} else
			 		$data = array('error' => $lang["errorMove"]);
		 	} else
		 		$data = array('error' => $lang["errorIsuploaded"]);
 
		
	}

	} else
		 $data = array('error' => $lang["notTorrent"]);
	
	

	} 
echo json_encode($data);
exit;
 
}
if (isset($_GET['form']))
{
	$data = array('success' => $lang["welldone"], 'formData' => $_POST);
	echo json_encode($data);
	exit;
 
}

if (isset($_GET['act'])) {

$torrent = new BDECODE($path.'/uploads/torrents/'.$_POST['torrentHash'].'.torrent');
$resultTorrent = $torrent->result;
 	
 	// Check announce(s) url 
	if (isset($torrent->result['announce-list']))
		$announce = $torrent->result['announce-list'];
	else
		$announce = $torrent->result['announce'];
 
	// Check if image existe
	/* if (!empty($_FILES['image']['name']))  
		$imagesConvert = $startUp->img_base64($_FILES['image']['tmp_name']);
	else 
		$imagesConvert = ''; */

	if (!empty($_FILES['image']['name']) && $userData->can_upload === 'true') {
	
		$valid_ext = array( '.jpg' , '.jpeg' , '.gif' , '.png' );
		$ext = strrchr($_FILES['image']['name'], '.'); 
 
		if (in_array($ext,$valid_ext)) {
			if(!move_uploaded_file($_FILES['image']['tmp_name'], $path.'/uploads/images/'.$_POST['torrentHash'].$ext))
				$smarty->assign('errorUploadimg',true);
			else
				$smarty->assign('errorUploadimg',false);		
		} else
			 $smarty->assign('imgNotAutorised',true);
	} else
		$ext = '';		
 		
 		// Insert torrent in database and assign id of torrent
 
			$returnIdtorrent = $startUp->addTorrent(
					$userId,
					$_POST['torrentTitle'],
					$_POST['torrentUrlTitle'],
					$_POST['categories'],
					$_POST['torrentDesc'],
					$_POST['torrentHash'],
					$announce,
					$_POST['torrentSize'],
					$ext
				);
				
		// First, we clear torrent stats scrape
		$startUp->clearScrape($_POST['torrentHash']);
		$smarty->assign('errorScrape',false);
		$smarty->assign('torrentUrl',$startUp->makeUrl(
			array('page'=>'torrent-detail',
			      'id'=>$returnIdtorrent,
			      'urlTitle'=>$startUp->Fuckxss($_POST['torrentUrlTitle'])
			      )));
			      
		if (is_array($announce)) {
			foreach ($announce as $key => $value) {
				// echo "Clé : $key; Valeur : $value[0]<br />\n";
				$startUp->torrentScrape($value[0],$_POST['torrentHash']);
			}			
		} else
			$startUp->torrentScrape($announce,$_POST['torrentHash']);
 
}

}


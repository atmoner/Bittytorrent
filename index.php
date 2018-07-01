<?php
// This part can be removed after install
if (file_exists("install.php")) {
	header("Location: install.php");
	exit;
}

define("IN_TORRENT",true);

$path = dirname(__FILE__); 

require('libs/startup.php');
$page = isset($_GET["page"])?$_GET["page"]:"";


$hook->add_page('','pages/main.php','index.html');
$hook->add_page('upload','pages/upload.php','upload.html');
$hook->add_page('upload-form','pages/upload.form.php','upload.form.html');
$hook->add_page('torrents','pages/torrents.php','torrents.html');
$hook->add_page('torrent-detail','pages/detail.php','detail.html');
$hook->add_page('torrent-edit','pages/edit.torrent.php','edit.torrent.html');
$hook->add_page('torrent-delete','pages/delete.torrent.php','delete.torrent.html');
$hook->add_page('externalscrape','pages/scrape.php','scrape.html');
$hook->add_page('scrape','pages/internalscrape.php','scrape.html');
$hook->add_page('announce','pages/announce.php','');
$hook->add_page('download','pages/download.php','');
$hook->add_page('rss','pages/rss.php','');
$hook->add_page('login','pages/login.php','login.html');
$hook->add_page('registration','pages/registration.php','registration.html');
$hook->add_page('account','pages/account.php','account.html');
$hook->add_page('edit-account','pages/edit.account.php','edit.account.html');
$hook->add_page('user','pages/user.php','user.html');
$hook->add_page('logout','pages/logout.php','');
$hook->add_page('admincp','pages/admincp/admincp.index.php',''); 

if ($hook->hook_exist('new_page'))
	$hook->execute_hook('new_page');
	
	foreach ($hook->addnewpage as $value) {
		if ($value['name'] === $page) {
			if (!empty($value['phpFile']))
				include $value['phpFile'];
			if (!empty($value['htmlFile']))
				$smarty->display($value['htmlFile']);
		}
	}


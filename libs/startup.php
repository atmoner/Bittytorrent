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

require($path.'/libs/Smarty.class.php');
require($path.'/libs/db.php');
require($path.'/libs/database/ez_sql_core.php');
require($path.'/libs/database/ez_sql_mysql.php');
require($path."/libs/categories.class.php");
require($path.'/libs/default.class.php'); 
require($path.'/libs/SmartyPaginate.class.php');
require($path.'/libs/Hooks.class.php'); 


$db 	 = new ezSQL_mysql($dbuser,$dbpass,$dbname,$dbhost); 
$smarty  = new Smarty;
$hook    = new phphooks();
$startUp = new Bittytorrent; 

$conf 	 = $startUp->getConfigs();
$startUp->I18n();

require($path.'/libs/lang/lang_'.$_SESSION['strLangue'].'.php');
// Smarty config
$smarty->addPluginsDir($path.'/libs/plugins/');
// $smarty->template_dir = $path.'/themes/v2/';
$smarty->template_dir = $path.'/themes/'.$conf['theme'].'/';
$smarty->compile_dir = $path.'/libs/cache/compile_tpl/';
$smarty->cache_dir = $path.'/libs/cache/';
$smarty->debugging = false;
//$smarty->caching = $conf['timecache'];
$smarty->caching = false;
$smarty->force_compile = false ;
$smarty->cache_lifetime = $conf['timecache'];
$smarty->config_dir = $path.'/libs/lang/';
$smarty->assign("lang",$lang);

$userId = $startUp->isLogged();
$userData = $startUp->getMydata(); 
 
if ($userId && isset($_COOKIE['tokenAdmin']))
        $isAdmin = $startUp->checkAdmin($_COOKIE['tokenAdmin']);
else
        $isAdmin = false;
 
$smarty->assign('name',$conf['title']);
$smarty->assign("userId",$userId); 
$smarty->assign("userData",$userData); 
$smarty->assign("userName",$startUp->session_username);
$smarty->assign("Admin",$isAdmin);
$smarty->assign('userUpload',$userData->upload);
$smarty->assign('userDownload',$userData->download);



if(!isset($_GET["catid"])) $_GET["catid"] ='NULL';
$cat_id = $_GET["catid"];
$smarty->assign('getCategoriescollapsed',$startUp->Categories('getlistcollapsed',$cat_id));
$smarty->assign('setError','false');


$sql = "SELECT filename FROM plugins WHERE action = '".$db->escape(1)."'";
$items = $db->get_results($sql,ARRAY_A);
if($items){
	foreach ($items as $result_rows)   
		$plugins[] = $result_rows['filename'];
	} else
		$plugins ='';	

$hook->active_plugins = $plugins;
$hook->set_hooks(array(
			'install',
			'action',
			'home_page',
			'new_page',
			'upload_page',
			'torrents_page',
			'detail_page',
			'registration_page',
			'login_page',
			'account_page',
			'edit_account_page',
			'user_page',
			'admin_action',
			'new_admin_page',
			'admin_settings_page'
));
$hook->load_plugins();

function add_hook($tag, $function, $priority = 10) {
	global $hook;
	$hook->add_hook ( $tag, $function, $priority );
}

//same as above
function register_plugin($plugin_id, $data) {
	global $hook;
	$hook->register_plugin ( $plugin_id, $data );
}

$smarty->assign('hooks',$hook); // !! do not remove....



// Lang menu hook
$hook->addMenuLang('en',$lang["english"], '?strLangue=en', 'en.png', '3'); 
$hook->addMenuLang('fr',$lang["french"], '?strLangue=fr', 'fr.png', '4');
$hook->addMenuLang('ru',$lang["russe"], '?strLangue=ru', 'ru.png', '5');

$hook->addMenu('home','Home', '', '3');
$hook->addMenu('upload','Upload', 'upload', '4');
$hook->addMenu('torrents','Torrents', 'torrents', '5');

// Usermenu hook 
if (!$userId) {
        $hook->addUserMenu('gestcp','','','', '4');
} else {
        if (isset($_COOKIE['tokenAdmin'])) {
                if ($isAdmin) {
                        $hook->addUserMenu('admincp','','','', '4');
                }                
        }
        
        $hook->addUserMenu('usercp','','','', '8');
}
 
// Search part
if (isset($_POST['torrentSearch']))  
	$startUp->redirect($conf['baseurl'].'/torrents/search/'.$startUp->Fuckxss($_POST['torrentSearch']));

// Default javascript
$hook->addJs('Jquery','themes/asset/js/jquery-latest.min.js','','1');
// Default css
$hook->addCss('Style','style.css','themes/'.$conf['theme'].'/css/','1');
$hook->addCss('bootstrap','bootstrap.css','themes/'.$conf['theme'].'/css/','2');
$hook->addCss('bootstrapTheme','bootstrap-theme.css','themes/'.$conf['theme'].'/css/','3');
$hook->addCss('select2','select2.css','themes/asset/css/','4');
$hook->addCss('Bselect2','select2-bootstrap.css','themes/asset/css/','5');

if ($hook->hook_exist('action'))
	$hook->execute_hook('action');




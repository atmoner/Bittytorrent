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

if ($userData->admin_access != 'true')
		$startUp->redirect($conf['baseurl']);

if (!isset($_COOKIE['tokenAdmin']) 
	|| empty($_COOKIE['tokenAdmin']) 
	|| !isset($_GET['tokenAdmin']) 
	|| empty($_GET['tokenAdmin']) 
	|| $_GET['tokenAdmin'] != $_COOKIE['tokenAdmin']
) 
    $startUp->redirect($conf['baseurl']);
        
if ($startUp->checkAdmin() === false) 
        $startUp->redirect($conf['baseurl']);
 
if ($hook->hook_exist('admin_action'))  
		$hook->execute_hook('admin_action');
if ($hook->hook_exist('new_admin_page')) 
		$hook->execute_hook('new_admin_page');
			
$do = (isset($_GET["act"])?$_GET["act"]:"");

$hook->add_admin_page('','admincp.settings.php','admincp.settings.html');
$hook->add_admin_page('users','admincp.users.php','admincp.users.html');
$hook->add_admin_page('settings','admincp.settings.php','admincp.settings.html');
$hook->add_admin_page('usersgroups','admincp.usersgroups.php','admincp.usersgroups.html');
$hook->add_admin_page('categories','admincp.categories.php','admincp.categories.html');
$hook->add_admin_page('plugins','admincp.plugins.php','admincp.plugins.html');
$hook->add_admin_page('themes','admincp.themes.php','admincp.themes.html');
$hook->add_admin_page('update','admincp.update.php','admincp.update.html');
 
	foreach ($hook->addnewadminpage as $valueAdmin) {
				
		if ($valueAdmin['name'] === $do) { 
			if (!empty($valueAdmin['phpFile']))
				include $valueAdmin['phpFile'];
			if (!empty($valueAdmin['htmlFile']))
				$smarty->display($valueAdmin['htmlFile']);		        	
		}  			
	}
 

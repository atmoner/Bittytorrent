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
      

// required connect
SmartyPaginate::connect();
// set items per page
SmartyPaginate::setLimit(10);

if (!isset($_GET['next']))
	SmartyPaginate::reset(); // reset/init the session data all time!
 
 
$startUp->paginatePage = 'admincp/users';

if (!defined("IN_TORRENT"))
      die("Access denied!");
     
if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
	if ($startUp->delUser($_GET['del'])) {
		$smarty->assign('forbidden',false);		
	} else
		$smarty->assign('forbidden',true);	
} else
	$smarty->assign('forbidden',false);	

if(isset($_POST['submitadd'])) {
        if (!empty($_POST['name']) && !empty($_POST['mail']) && !empty($_POST['pass'])){    
        	if ($_POST['sendmail']) {
        		$sendMail = $_POST['sendmail'];
        	} else
        		 $sendMail = 'NULL';   
        		        
            $startUp->addUser($_POST['name'],$_POST['mail'],$_POST['pass'],'NULL',$sendMail);
        }
}

if(!empty($_GET['edit'])) {
        if (!empty($_POST['editUser'])){
                $startUp->adminEditUser($_GET['edit'],$_POST['pass'],$_POST['name'],$_POST['mail'],$_POST['level'],$_POST['location'],$_POST['website'],$_POST['signature']);
        }
        $smarty->assign("getUserdata",$startUp->getUserdata($_GET['edit']));
        $smarty->assign("getStatuts",$startUp->getStatuts());
} else 
        $smarty->assign("getUsers",$startUp->getUsers());
 
$hook->addCss('adminUser','adminUser.css','themes/'.$conf['theme'].'/css/','1');
SmartyPaginate::assign($smarty); // paginate


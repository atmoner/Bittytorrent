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
 
if ($startUp->isLogged())
        $startUp->redirect($conf['baseurl'].'/account');

if (isset($_POST['submit'])) {
		$redirect = '';
		$error = '';
        if (!empty($_POST['user']) && !empty($_POST['pass'])){        
                if ($startUp->checkCredentials($_POST['user'], $_POST['pass'])){
                                        $startUp->setSession($_POST['user'],$_POST['pass'],'on');
                                        $redirect = $conf['baseurl'].'/account';
                                        $error = '<font color="green">'.$lang["goodCredential"].'</font><br /> ';
                                } else
                                                $error = '<font color="red">'.$lang["badCredential"].'</font><br /> ';
        } else
                        $error = '<font color="red">'.$lang["errorLoginEmpty"].'</font><br /> ';


		$retour = array(
			'errorLogin'      => $error,
			'redirect'      => $redirect
		);
 
		 
		// Envoi du retour (on renvoi le tableau $retour encodé en JSON)
		header('Content-type: application/json');
		echo json_encode($retour);
		exit;
}  

$hook->add_side_block('defaultBlock_Login','','', 3); 


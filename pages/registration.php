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
 
function domain_exists($email, $record = 'MX'){
	list($user, $domain) = explode('@', $email);
	return checkdnsrr($domain, $record);
} 
 
$notAvaible = '<img src="'.$conf['baseurl'].'/themes/asset/img/not-available.png" />';
$avaible = '<img src="'.$conf['baseurl'].'/themes/asset/img/available.png" />';

if(isset($_POST["checkPass"])) {
	$pwd = $_POST['checkPass'];

	if( strlen($pwd) < 6 ) {
		$error .= $notAvaible . $lang['PtooShort'] . " <br />";
	}

	if( strlen($pwd) > 20 ) {
		$error .= $notAvaible . $lang['PtooLong'] . " <br />";
	}

	if( !preg_match("#[0-9]+#", $pwd) ) {
		$error .= $notAvaible . $lang['PleastOneNumber'] . " <br />";
	}
	
	if( !preg_match("#[a-z]+#", $pwd) ) {
		$error .= $notAvaible . $lang['PleastOneLetter'] . " <br />";
	}


	if($error){
		echo ' <br /> '.$error.'<br />';
	} else {
		echo $avaible . $lang['Pstrong'] ;
	}
	exit;
}

if(isset($_POST["checkRepass"])) {
	if($_POST["checkRepass"] === $_POST["checkpassO"]) {
	    echo $avaible . $lang["PGood"]; 
	} else  
	    echo $notAvaible . $lang['PdoesnotMatch'];	 
	exit;
}

if(isset($_POST["checkMail"])) {
	if(filter_var($_POST["checkMail"], FILTER_VALIDATE_EMAIL)){
		if(domain_exists($_POST["checkMail"])) {
		     echo $avaible . $lang["PGood"]; 
		} else  
		    echo $notAvaible . $lang["PDomainNotExist"];	
	} else
		echo $notAvaible . $lang["PBadSyntax"]; 
	exit;
}
//check we have username post var
if(isset($_POST["checkUsername"])) {
    //check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die();
    }  

	if(!empty($_POST["checkUsername"])) {
		//trim and lowercase username
		$username =  strtolower(trim($_POST["checkUsername"]));
	   
		//sanitize username
		$username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
	   
		//check username in db
		$user = $db->get_row("SELECT id FROM users WHERE name='".$db->escape($username)."'"); 
	 
		//if value is more than 0, username is not available
		if($user) {
		    echo $notAvaible . $lang["PuserAlreadyExist"]; 
		} else 
		    echo $avaible . $lang["PGood"];
    } else
    	echo $notAvaible . $lang["PuserEmpty"]; 
    exit;
}
if ($_POST['sendForm']) {

	$smarty->assign('accountCreated',false);
	$smarty->assign('error',false);
	
	if (!empty($_POST['email'])) {
		if (!empty($_POST['username'])) {
			if (!empty($_POST['password'])) {
				if (!empty($_POST['password']) && $_POST['repeatPassword'] === $_POST['password']) {
					if (isset($_POST['terms']) && $_POST['terms'] === 'on') {
						if ($startUp->addUser($_POST['username'],$_POST['email'],$_POST['password'],true,'NULL'))  
							$smarty->assign('accountCreated',true);
						else 
							$smarty->assign('error','These identifiers (<b>'.$startUp->Fuckxss($_POST['username']).'</b> or <b>'.$_POST['email'].'</b>) are already used'); 
					} else
						$smarty->assign('error',$lang["PAgree"]);					
				} else
					$smarty->assign('error',$lang["PdoesnotMatch"]);	
			} else
				$smarty->assign('error',$lang["PasswordNotEmpty"]);
		} else
			$smarty->assign('error',$lang["PuserEmpty"]);	
	} else
		$smarty->assign('error',$lang["PmailNotEmpty"]);	
}

$hook->add_side_block('defaultBlock_Categories','','', 3); 
 

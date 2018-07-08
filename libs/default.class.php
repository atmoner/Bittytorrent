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

class StartUp {

	var $prefix_db = ''; // Prefix db (for security)
	var $version = '3'; // Version of php-pastebin
	var $rev = '0'; // Revision of php-pastebin
	var $charset = 'utf-8'; // Chraset
	var $get = '';
	var $block = '';
	var $sqlvalue = '';
	var $paginatePage = '';	
	 		
	###
	function __construct() {
		if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
			if (session_status() == PHP_SESSION_NONE) session_start();
		} else
			session_start();
		header("Content-type:text/html; charset=".$this->charset."");
		$this->checkInstallFile();
	}
	###
	function checkInstallFile() {
		global $smarty,$path;
		if (file_exists($path."/install.php")) {
			if (filesize($path."/libs/db.php") != 0) {
				$smarty->assign('errorInstallFile',true);
			} 
		} else
			$smarty->assign('errorInstallFile',false);	
	}
	###
	function setError($message) {
		global $smarty;
		$smarty->assign('setError',$message);
	}
	###
	function cGet($get) {
	    $this->get = $get;
	    if(is_numeric($this->get)) {
	      $get=(int)$this->get;
	    } else {
	      $get=htmlspecialchars($this->get);
	    }
	    // return $get;
	}
	###
	function getConfigs(){
		global $db, $smarty;
			$sql = "SELECT `key`,`value` FROM ".$this->prefix_db."settings";
			$array = $db->get_results($sql,ARRAY_A);
			// $db->debug();
			foreach ($array as $key => $value) {
				$array[$value['key']] = $this->Fuckxss($value['value']);
			}		
			$smarty->assign('getConfigs',$array); 
    	return $array;
 	}	
	###
	function redirect($location='index.php'){
		header("location:".$location);
		exit;
	}		
	###
	function makeUrl($array) {
		global $conf;
		if ($conf['rewrite_url'] === 'true') {
			$post_url = '';
			foreach ($array as $key=>$value)
				$post_url .= $value.'/';
			$post_url = rtrim($post_url, '/'); 
		} else {	
			$post_url = '';	
			foreach ($array as $key=>$value)
				$post_url .= $key.'='.$value.'&';
			$post_url = rtrim('?'.$post_url, '&');
			$post_url = str_replace('&','&amp;', $post_url); // Replace & by &amp; for url not rewrite	
		}
		return $post_url;			
	} 
	### 	
	function addTorrent($userid,$title,$urlTitle,$cat,$desc,$hash,$announce,$size,$imgExt) {
		global $db,$conf;
		$date = time();
		$query = "INSERT INTO ".$this->prefix_db."torrents (userid,info_hash,title,url_title,categorie,torrent_desc,date,announce,size,imgExt) 
	          VALUES (
			  '".$db->escape($userid)."',
			  '".$db->escape($hash)."',
			  '".$db->escape($title)."',
			  '".$db->escape($urlTitle)."',
			  '".$db->escape($cat)."',
			  '".$db->escape($desc)."',
			  '$date',
			  '".$db->escape(serialize($announce))."',
			  '".$db->escape($size)."',
			  '".$db->escape($imgExt)."' 
			  )";
    		$db->query($query);
    		// $db->debug();
    		return $db->insert_id;
 
    		// $paste = $db->get_row("SELECT uniqueid FROM ".$this->prefix_db."torrents WHERE id='$id'");
     		// $this->redirect($conf['baseurl'].'/'.$paste->uniqueid);
	}
	###
	function torrentScrape($url,$infohash='')
	{
	global $path,$db,$smarty;
		if (isset($url))
		{
		    $url_c = parse_url($url);
		        
		    if(!isset($url_c["port"]) || empty($url_c["port"]))
		        $url_c["port"]=80;

		    require_once($path."/libs/scrape/".$url_c["scheme"]."tscraper.php");
		    try
		    {
		        $timeout = 5;

		        if($url_c["scheme"]=="udp")
		            $scraper = new udptscraper($timeout);
		        else
		            $scraper = new httptscraper($timeout);

		        $ret = $scraper->scrape($url_c["scheme"]."://".$url_c["host"].":".$url_c["port"].(($url_c["scheme"]=="udp")?"":"/announce"),array($infohash));
		        //var_dump($ret);
		        $query = "UPDATE torrents SET `seeds`=seeds+".$ret[$infohash]["seeders"].", `leechers`=leechers+".$ret[$infohash]["leechers"].", `finished`=finished+".$ret[$infohash]["completed"].", `last_scrape`='".time()."' WHERE `info_hash` = '".$db->escape($infohash)."'";
		        $db->query($query);
		        $smarty->assign('returnScrape',$ret);
		        // var_dump($ret);
		        // return $ret;
		    }
		    catch(ScraperException $e) {
		       		if ($e->getMessage())
		       			 return "<br />".$e->getMessage();	       		
		    }
		    return;
		}
		return;
	}
	###
	function cachingTorrent($website,$torrent){
		$files = array(
			array(
			    'name' => 'torrent',			// Don't change
			    'type' => 'application/x-bittorrent',
			    'file' => $torrent			// Full path for file to upload
			)
		);

		$http_resp = http_post_fields( $website, array(), $files );
		$tmp = explode( "\r\n", $http_resp );
		$infoHash = substr( $tmp[count( $tmp ) - 1], 0, 40 );
		unset( $tmp, $http_resp, $files );	
		
		return $infoHash;
	}
	###
	function clearScrape($infohash) {
		global $db;
		$query = "UPDATE torrents SET `seeds`='0', `leechers`='0', `finished`='0' WHERE `info_hash` = '".$db->escape($infohash)."'";
		$db->query($query);
	}	
	###
	function write_log($text,$reason='add') {
	  global $db;
 		$id = $this->uid;
		$query = "INSERT INTO ".$this->prefix_db."logs (date,txt,type,user) VALUES(UNIX_TIMESTAMP(), '".$db->escape($text)."', '".$db->escape($reason)."',".$id.")";
		$db->query($query);
	}
	###
	function editTorrent($id,$title,$url_title,$desc,$cat,$image){
		global $db;
		if (!empty($image))
			$imageUpdate = ", imgExt='".$db->escape($image)."'";
		else
			$imageUpdate = "";
			
		$query = "UPDATE ".$this->prefix_db."torrents SET 
						title='".$db->escape($title)."',
						url_title='".$db->escape($url_title)."',
						torrent_desc='".$db->escape($desc)."',
						categorie='".$db->escape($cat)."'
						$imageUpdate
			 WHERE id = '".$db->escape($id)."'";
		if ($db->query($query)) 
    		return true;
    	else
    		return false;	
	}
 
	###
	function getTorrents($cat='',$orderBy='',$axis=''){	
		global $db,$smarty;
		$query_select = "";
 
		if (!empty($cat) && $cat != 'NULL') {
			$cat_sql = "SELECT * FROM categories WHERE url_strip='$cat'";
			$cat_sql = $db->get_row($cat_sql); 
		}  
 
		$sql = "SELECT SQL_CALC_FOUND_ROWS					 t.id,t.info_hash,t.title,t.url_title,t.categorie,t.torrent_desc,t.date,t.hits,t.seeds,t.leechers,t.finished,t.size,t.announce,users.name,categories.c_name, categories.url_strip,categories.c_icon FROM ".$this->prefix_db."torrents AS t ";
		$sql .= "INNER JOIN ".$this->prefix_db."users ON t.userid=users.id ";			
		$sql .= "INNER JOIN ".$this->prefix_db."categories ON t.categorie=categories.id ";	
		if (!empty($cat) && $cat != 'NULL') {
		$sql .= "WHERE categories.url_strip='$cat' OR categories.position RLIKE '^".$cat_sql->id.">[0-9]+>$'"; 
		}
		  if (isset($_GET["search"])) {
		   $testocercato = trim($_GET["search"]);
		   $testocercato = explode(" ",$testocercato);
		   if ($_GET["search"]!="")
		      $search = "search=" . implode("+",$testocercato);
		    for ($k=0; $k < count($testocercato); $k++) {
			// $query_select .= " t.title LIKE '%" . mysql_real_escape_string($testocercato[$k]) . "%'";
			$query_select .= sprintf(" t.title LIKE '%%%s%%'", "%" . $db->escape($testocercato[$k]) . "%");
			 
			
			if ($k<count($testocercato)-1)
			   $query_select .= " AND ";
		    }
		    $sql .= " WHERE " . $query_select;
		}	 
		if (!empty($orderBy))		 
			$sql .= " ORDER BY t.".$db->escape($orderBy)." ";
		  else
			$sql .= " ORDER BY t.date ";
			
		if (!empty($orderBy))		 
			$sql .= "".strtoupper($db->escape($axis))." ";	
		  else
			$sql .= " DESC ";	
 
		$sql .= " LIMIT %d,%d";

        $_query = sprintf($sql, SmartyPaginate::getCurrentIndex(), SmartyPaginate::getLimit()); 
		$items = $db->get_results($_query); 
 
		$_row = $db->get_row("SELECT FOUND_ROWS() as total");		 
        SmartyPaginate::setTotal($_row->total);
 

	 	if ($items) { 
		foreach ($items as $obj) {
         		$array[$obj->id]['id'] = $obj->id;
         		$array[$obj->id]['info_hash'] = $obj->info_hash;
            	$array[$obj->id]['title'] = $this->Fuckxss($obj->title);
				$array[$obj->id]['torrent_desc'] = $this->Fuckxss($obj->torrent_desc);  
           		$array[$obj->id]['date'] = $this->ago($obj->date);
           		$array[$obj->id]['hits'] = $obj->hits;
           		$array[$obj->id]['seeds'] = $obj->seeds;
           		$array[$obj->id]['leechers'] = $obj->leechers;
           		$array[$obj->id]['finished'] = $obj->finished;
           		$array[$obj->id]['size'] = $this->bytesToSize($obj->size);
           		$array[$obj->id]['announce'] = unserialize($obj->announce);          		
           		$array[$obj->id]['name'] = $obj->name;
           		$array[$obj->id]['c_icon'] = $obj->c_icon;
           		$array[$obj->id]['c_name'] = $this->Fuckxss($obj->c_name);
           		$array[$obj->id]['c_url'] = $this->makeUrl(array('page'=>'torrents','gost'=>'cat','catid'=>$this->Fuckxss($obj->url_strip)));            		       		
           		$array[$obj->id]['torrentUrl'] = $this->makeUrl(array('page'=>'torrent-detail','id'=>$obj->id,'urlTitle'=>$this->Fuckxss($obj->url_title)));
	        }
			// $smarty->assign('getTorrents',$array);	
			return $array;
	 	} else
	 		return false;	   		
	}
	###
	// this returns all the categories with subs into a select
	function Categories($act='getlist',$val='',$adminPanel='NULL') {
	  global $db,$smarty,$conf;
 
 		$categories = new categories;
 		
 		switch ($act) {
 		
 			case 'getlist':
 				$output = $categories->build_list($val);
 			break;

 			case 'getlistcollapsed':
 				$output = $categories->build_list($val,"collapsed");
 			break;
 			
 			case 'getsubcat':
 				$output = $categories->browse_by_id($val);
 			break;

 			case 'getParent':
 				$output = $categories->getParent($val); 
 			break;
 			
 			case 'add':
 				$output = $categories->add_new($val['parent'] , $val["name"] , $val["desc"] , $val["icon"] , $val["strip"] ); 
 			break;

 			case 'update':
 				$output = $categories->update($val['id'], $val['parent'] , $val["name"] , $val["desc"] , $val["icon"] , $val["strip"] ); 
 			break;

 			case 'delete':
 				$output = $categories->deleteCat($val['catFrom'], $val['catTo'], $val["cat_action"]); 
 			break;
 			
 			case 'getOne':
 				$output = $categories->getOne($val); 
 			break;
 			
 			case 'html':
				if ($adminPanel === true) {
					$categories->HtmlTree = array(
					"header" => "<ul class='unstyled'>", 
					"BodyUnselected" => '<li> [prefix] <a href="'.$conf['baseurl'].'/admincp/categories/?catid=[id]&tokenAdmin='.$_COOKIE['tokenAdmin'].'"> [name] </a> </li>',
					"BodySelected" => '<li> [prefix] <a href="'.$conf['baseurl'].'/admincp/categories/?catid=[id]&tokenAdmin='.$_COOKIE['tokenAdmin'].'"><strong><font color="#000000">[name]</font></strong></a> </li>',
					"footer" => '</ul>',
					);
					
				} else {
					$categories->HtmlTree = array(
					"header" => "<ul class='nav nav-tabs nav-stacked'>", 
					"BodyUnselected" => '<li> <a href="'.$conf['baseurl'].'/'.$this->makeUrl(array('page'=>'torrents','cat'=>'categorie','catid'=>'[id]')).'"> [name] </a> </li>',
					"BodySelected" => '<li>  <a href="'.$conf['baseurl'].'/'.$this->makeUrl(array('page'=>'torrents','cat'=>'categorie','catid'=>'[id]')).'"><strong><font color="#000000">[name]</font></strong></a> </li>',
					"footer" => '</ul>',
					);				
				}
 
 				$output = $categories->html_output($val); 				
 			break;
 		}
		
		return $output;	
	}	
	###
	function getTorrent($id){ 
		global $db, $conf;
		$sql = "SELECT t.id,t.userid 	,t.info_hash,t.title,t.url_title,t.categorie,t.torrent_desc,t.date,t.hits,t.seeds,t.leechers,t.finished,t.size,t.announce,t.last_scrape,t.imgExt,users.name,categories.c_name FROM ".$this->prefix_db."torrents AS t ";
		$sql .= "INNER JOIN ".$this->prefix_db."categories ON t.categorie=categories.id ";	
		$sql .= "INNER JOIN ".$this->prefix_db."users ON t.userid=users.id ";
		$sql .= "WHERE t.id = '".$db->escape($id)."' ";
 
 		$items = $db->get_results($sql);
		if ($items)
		{
 
			foreach ($items as $obj) {
		     		$array['id'] = $obj->id;
		     		$array['userid'] = $obj->userid;
		     		$array['info_hash'] = $obj->info_hash;
		        	$array['title'] = $this->Fuckxss($obj->title);
		        	$array['url_title'] = $this->Fuckxss($obj->url_title);
		        	$array['categorie'] = $this->Fuckxss($obj->categorie);
		        	$array['c_name'] = $this->Fuckxss($obj->c_name);
					$array['torrent_desc'] = $obj->torrent_desc;  
		       		$array['date'] = $obj->date;
		       		$array['hits'] = $obj->hits;
		       		$array['seeds'] = $obj->seeds;
		       		$array['leechers'] = $obj->leechers;
		       		$array['finished'] = $obj->finished;
		       		$array['size'] = $this->bytesToSize($obj->size);
		       		$array['announce'] = unserialize($obj->announce);          		
		       		$array['name'] = $obj->name;
		       		$array['last_scrape'] = $obj->last_scrape;
		       		$array['imgExt'] = $obj->imgExt;
		       		$array['imgUrl'] = $obj->info_hash.$obj->imgExt;
		       		$array['uname'] = $obj->name;
		       		$array['uname_url'] = $conf['baseurl'].'/'.$this->makeUrl(array('page'=>'user','act'=>$obj->name)).'/';
		       		$array['cat_url'] = $conf['baseurl'].'/'.$this->makeUrl(array('page'=>'torrents','ghost'=>'cat','catid'=>$this->Fuckxss($obj->c_name)));
			    } 		 
			return $array;
		} else
			return 'NULL';
	}
	###
	function bytesToSize($bytes, $precision = 2) {  
		$kilobyte = 1024;
		$megabyte = $kilobyte * 1024;
		$gigabyte = $megabyte * 1024;
		$terabyte = $gigabyte * 1024;
	   
		if (($bytes >= 0) && ($bytes < $kilobyte)) {
		    return $bytes . ' B';
	 
		} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
		    return round($bytes / $kilobyte, $precision) . ' KB';
	 
		} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
		    return round($bytes / $megabyte, $precision) . ' MB';
	 
		} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
		    return round($bytes / $gigabyte, $precision) . ' GB';
	 
		} elseif ($bytes >= $terabyte) {
		    return round($bytes / $terabyte, $precision) . ' TB';
		} else {
		    return $bytes . ' B';
		}
	}
	###
	function updateHits($id) {
		global $db;
			$sql = "UPDATE ".$this->prefix_db."torrents SET hits=(hits + 1) WHERE uniqueid='".$db->escape($id)."'";		
	    		$db->query($sql);
		return true;
	}
	###
	function makeId($car=8) {			      
		$string = "";
		$chaine = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnpqrstuvwxy1234567890";
		srand((double)microtime()*1000000);
		for($i=0; $i<$car; $i++) {
		$string .= $chaine[rand()%strlen($chaine)];
		}
	return $string;
	}
	###
	function I18n(){
 
 		global $conf;
		if(isset($_GET['strLangue'])) {
	
			$chaine = $_SERVER['REQUEST_URI'];
			$nbr = 13;
			$url = substr($chaine, 0, -$nbr);
 
			$langAutorises = array('fr','en','ru');
			if (in_array($_GET['strLangue'],$langAutorises))
			$_SESSION['strLangue']=$_GET['strLangue'];
	 		$this->redirect($url); 
 							
		} else {
			if (empty($_SESSION['strLangue'])) {
			$_SESSION['strLangue'] = 'en';		
			}  
		}
	}
	### 	
	function addUser($name,$mail,$pass,$redirect='NULL',$sendMail='NULL',$isadmin="NULL") {
		global $db,$conf;

		$db->query("SELECT id FROM users WHERE id != '".$db->escape(0)."' AND mail='".$db->escape($mail)."' OR name='".$db->escape($name)."' ");
		$user_details = $db->get_row();
		
		if (!$user_details) {
		
		$hash = $this->makeId(15);
		if ($isadmin!="NULL")
			$level = "6";
			else
			$level = "2";
			
		$query = "INSERT INTO ".$this->prefix_db."users (name,pass,mail,level,private_id) 
	          VALUES (
			  '".$db->escape($name)."',
			  '".$this->obscure($pass)."',
			  '".$db->escape($mail)."',
			  '$level',
			  '".md5(uniqid(rand(), true))."'
			  )";
    		$db->query($query);
    		if ($sendMail!='NULL')  
    		$this->sendMail($name,$mail,$pass,$hash);
     		if ($redirect!='NULL') 
     			$this->redirect($conf['baseurl'].'/zone-login');
     			     			
     		return true;  				
			
		} else
			return false; 
	}
	### 
	function getAllgroups() {
		global $db,$smarty,$hook;
 
		$sql = "SELECT * FROM ".$this->prefix_db."users_group";
		$items = $db->get_results($sql,ARRAY_A);

	 	if ($items) {  
	 	
			foreach ($items as $obj) {
			
				/*$array[$obj['id']]['id'] = $obj['id'];
				$array[$obj['id']]['group'] = $obj['group']; */

				foreach ( $db->get_col_info()  as $group ) {
 					//var_dump($group.' : '.$obj[$group].'<br />');
 					
 
				if ($group != 'id' && $group != 'group') {
				 	// $hook->admin_users_groups($group, $group, $obj[$group]); 
				 	$array[$obj['id']]['auth'][$group] = $obj[$group];		
				 } else
				 	$array[$obj['id']][$group] = $obj[$group];    
				}
			} 
 
			return $array;
	 	} else
	 		return false;			
	}	
	####	
	function addUserAuth($name,$mail,$sendMail='NULL',$isadmin="NULL") {
		global $db,$conf;

		$db->query("SELECT id FROM users WHERE id != '".$db->escape(0)."' AND mail='".$db->escape($mail)."' OR name='".$db->escape($name)."' ");
		$user_details = $db->get_row();
		
		if (!$user_details) { 
 
			$query = "INSERT INTO users (id,name,pass,mail,level,auth_type,auth_id) 
			      VALUES (
				  'NULL',
				  '".$db->escape($user_profile["username"])."',
				  'NULL',
				  'NULL',
				  '2',
				  'facebook',
				  '".$user_profile["id"]."'
				  )";
				$db->query($query);

    		if ($sendMail!='NULL')  
    		$this->sendMail($name,$mail,$pass,$hash);
     		if ($redirect!='NULL') 
     			$this->redirect($conf['baseurl'].'/zone-login.html');    				
			
		} else
			return false; 
	}
	###
	function EditUserInfo($pass='',$mail,$seemail,$seeMytorrents,$location,$website,$sign) {
		global $db;
		if (empty($pass)) {
			$pass = '';
		} else {
			$pass = "pass='".$this->obscure($pass)."',";
		}
		$query = "UPDATE ".$this->prefix_db."users SET $pass 
					mail='".$db->escape($mail)."',
					seemail='".$db->escape($seemail)."',
					seeMytorrents='".$db->escape($seeMytorrents)."',
					location='".$db->escape($location)."',
					website='".$db->escape($website)."',
					signature ='".$db->escape($sign)."' 
					WHERE id = '".$db->escape($this->uid)."'";
    		$db->query($query);
    		$this->redirect($conf['baseurl'].'/account?token='.$_COOKIE['token']);  
	return true;
	} 
	###
	function checkMail($mail){
		# code...
		$atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
		$domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caractères autorisés après l'arobase (nom de domaine)
				               
		$regex = '/^'.$atom.'+'.'(\.'.$atom.'+)*'.'@'.'('.$domain.'{1,63}\.)+'.$domain.'{2,63}$/i';          

		// test de l'adresse e-mail
		if (preg_match($regex, $mail)) {
		    return true;
		} else {
		    return false;
		}
	}
	###
	function sendMail($user,$mail,$pass,$hash) {
    		require_once('mailling/classes/class.formatmail.php');
    		    $GLOBALS['NAME'] = $user;
		    $GLOBALS['USERNAME'] = $user;
		    $GLOBALS['PASSWORD'] = $pass;
		    //Importatnt: fill up all GLOBALS field before call this constructor
		    $FM = new FormatMail(dirname(__FILE__).'/mailling/templates/registration-'.$_SESSION['strLangue'].'.htm');
		    $FM->Mailer->FromName = $user;
		    // $FM->Mailer->From = $this->admin_mail;
		    $FM->Mailer->Subject = 'Registration';
		    $FM->Mailer->AddAddress($mail,$user);
		    //And now, send the mail...
		    if ($FM->Send()) 
	return true;	
	}	
	### 
	function getThemes($dir,$mode='folders'){	 
	 $items = array();	 
	 if( !preg_match( "/^.*\/$/", $dir ) ) $dir .= '/';	 
         $handle = opendir( $dir );
	 if( $handle != false ){
	  while($item=readdir($handle))
	  {
	   if($item != '.' && $item != '..' && $item != 'asset')
	   {
	    // selon le mode choisi
	    switch($mode)
	    {
	     case 'folders' :
	      if(is_dir($dir.$item))
	       $items[] = $item;
	      break;
	     
	     case 'files' :
	      if(!is_dir($dir.$item))
	       $items[] = $item;
	      break;
	     
	     case  'all' :
	      $items[] = $item;	    
	    }
	   }
	  }	  
	  closedir($handle);	   
	  return $items;	  
	 }
	 else return false;	  
	}
	###
	function makeTimestamp($date){
		$date = str_replace(array(' ', ':'), '-', $date);
		$c    = explode('-', $date);
		$c    = array_pad($c, 6, 0);
		array_walk($c, 'intval'); 
	return mktime($c[3], $c[4], $c[5], $c[1], $c[2], $c[0]);
	}
	###
	function img_base64($image){	 
			// Read image path, convert to base64 encoding
			$imageData = base64_encode(file_get_contents($image));
			// Format the image SRC:  data:{mime};base64,{data};
			$src = 'data: '.mime_content_type($image).';base64,'.$imageData;
		return $src;
	}

	###	
	function Fuckxss($var) {
			strip_tags($var);
			$output = htmlspecialchars($var, ENT_QUOTES);
		return $output;
	}	
	###
	function ago($time) {
	
	   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	   $lengths = array("60","60","24","7","4.35","12","10");
	   $now = time();

		   $difference     = $now - $time;
		   $tense         = "ago";

		   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			   $difference /= $lengths[$j];
		   }
		   $difference = round($difference);
		   if($difference != 1) {
			   $periods[$j].= "s";
		   }
	   return "$difference $periods[$j] ago";
	}
	###
	function get_web_page( $url ) {
	 
		$ch = curl_init(); // open curl session
		// set curl options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13');    
	 
		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );    
		curl_close($ch); // close curl session

		$header['errno']   = $err;
		$header['errmsg']  = $errmsg;
		$header['content'] = $content;
	 
		return $header;
	}	
}
class Bittytorrent extends startUp {
	
	var $session_name = '';	
	var $session_username = '';
	var $session_password = '';
	var $uid = '';
	var $authType = '';

	###
	function isLogged(){
		if($this->checkUser()){
			return $this->uid;
		} else {
			return false;
		}
	}
	###
	function isLoggedAcount(){
		if($this->checkUser()){
			return $this->uid;
		} else {
			$this->redirect();
			$this->killAll();

		}
	}
	###
	function checkUser(){
 
		global $db;
		if($this->checkCookie()){
			$uid = $this->uid;
			$username = $this->session_username;
			$password = $this->session_password;

			$query = "SELECT id FROM ".$this->prefix_db."users WHERE name = '".$db->escape($username)."' AND pass = '".$db->escape($password)."' AND id = '".$db->escape($uid)."' AND level > '0' LIMIT 1;";
			$user = $db->get_row($query); 				
			
			 
			
 			// $db->debug();
				 if ($user->id)  
					return true;					
				 else  
					return false;					 	
			} else 
				return false;		
	}
	###
	function checkAdmin(){
		global $db;
		if($this->checkCookie()){
				if ($this->checkUser()) {
					$uid = $this->uid;
					$query = "SELECT id FROM ".$this->prefix_db."users WHERE id = '".$db->escape($uid)."' AND level = '6' LIMIT 1;";
					$user = $db->get_row($query); 
		 
						 if ($user->id)  
							return true;					
						 else  
							return false;					
				} else
					return false;				 	
			} else 
				return false;			 
	}
	###
	function checkCredentials($username, $password){	
		global $db; 
		$password = $this->obscure($password);				
		$query = "SELECT id FROM ".$this->prefix_db."users WHERE name = '".$db->escape($username)."' AND pass = '".$db->escape($password)."' AND level > '0' LIMIT 1;";
    	$user = $db->get_row($query); 
    		
		if ($user) {
			if ($user->id)  
				return true;
			else  
				return false;
		
		}		
	}
        ###
        function getMydata(){
                global $db;
 
 				$query = "SELECT *
 				FROM ".$this->prefix_db."users AS u";
                $query .= " INNER JOIN users_group ON u.level=users_group.id";
                $query .= " WHERE u.id = '".$db->escape($this->uid)."' LIMIT 1";
                    
                $user = $db->get_row($query,OBJECT); // get result in objet (OBJECT)

                if ($user) {
                         $user->id = $this->Fuckxss($user->id);
                         $user->name = $this->Fuckxss($user->name);
                         $user->mail = $this->Fuckxss($user->mail);
                         $user->level = $this->Fuckxss($user->level);
                         $user->signature = $this->Fuckxss($user->signature);        
                         $user->location = $this->Fuckxss($user->location);        
                         $user->website = $this->Fuckxss($user->website);
                         $user->private_id = $this->Fuckxss($user->private_id);
                         $user->seeMytorrents = $this->Fuckxss($user->seeMytorrents);
						 if ($user->upload != NULL) 
						 $user->upload = $this->bytesToSize($user->upload);
						 else 
						 $user->upload = '0 Mb'; 
						 if ($user->download != NULL)
						 $user->download = $this->bytesToSize($user->download);
						 else
						 $user->download = '0 Mb';
                          
 
                	return $user;
                                 
                } else
                	return false;

        }
	###
	function getUserdata($uid=FALSE){
		global $db;
 
		if($uid==FALSE){
			$id = $this->uid;
			$where = "WHERE u.id = '".$db->escape($this->uid)."' ";	
		} elseif (!is_numeric($uid)) {
			$where = "WHERE u.name = '".$db->escape($uid)."' ";		
		} else {
			$where = "WHERE u.id = '".$db->escape($uid)."' ";
		}
		 
		$query = "SELECT u.id, u.name, u.mail, u.level, u.signature, u.seemail, u.seeMytorrents, u.location, u.website, ug.group FROM ".$this->prefix_db."users AS u"; 
		$query .= " INNER JOIN ".$this->prefix_db."users_group AS ug ON ug.id=u.level ";	
		$query .= " $where LIMIT 1";				
    	$user = $db->get_row($query,OBJECT); 
    		
 		if ($user) {
			$user->id = $this->Fuckxss($user->id); 
			$user->level = $this->Fuckxss($user->level); 
			$user->group = $this->Fuckxss($user->group); 
 			$user->name = $this->Fuckxss($user->name);
 			$user->mail = $this->Fuckxss($user->mail); 
 			$user->signature = $this->Fuckxss($user->signature);	
 			$user->location = $this->Fuckxss($user->location);	
 			$user->website = $this->Fuckxss($user->website);
 			$user->seeMytorrents = $this->Fuckxss($user->seeMytorrents);
 			
 
			return $user;
		} else
			return false;
	}
	###
    function getMyTorrents($orderBy,$axis) {
                global $db;
 
		$sql = "SELECT SQL_CALC_FOUND_ROWS					 t.id,t.info_hash,t.title,t.url_title,t.categorie,t.torrent_desc,t.date,t.hits,t.seeds,t.leechers,t.finished,t.size,t.announce,t.last_scrape,users.name ,categories.c_name,categories.c_icon, categories.url_strip FROM ".$this->prefix_db."torrents AS t ";
		$sql .= "INNER JOIN ".$this->prefix_db."users ON t.userid=users.id ";			
		$sql .= "INNER JOIN ".$this->prefix_db."categories ON t.categorie=categories.id ";	
		$sql .= "WHERE t.userid = '".$this->uid."' ";

		if (!empty($orderBy))		 
			$sql .= "ORDER BY t.$orderBy ";
		  else
			$sql .= "ORDER BY t.date ";
			
		if (!empty($orderBy))		 
			$sql .= "".strtoupper($axis)." ";	
		  else
			$sql .= "DESC ";	
		 		
		$sql .= "LIMIT %d,%d";

		$_query = sprintf($sql, SmartyPaginate::getCurrentIndex(), SmartyPaginate::getLimit()); 
		$items = $db->get_results($_query);
 
		$_row = $db->get_row("SELECT FOUND_ROWS() as total");		 
		SmartyPaginate::setTotal($_row->total);

 
                if ($items) {
                foreach ($items as $obj) {
                        $array[$obj->id]['id'] = $obj->id;
                        $array[$obj->id]['info_hash'] = $obj->info_hash;
                    	$array[$obj->id]['title'] = $this->Fuckxss($obj->title);
                        $array[$obj->id]['date'] = $obj->date;
                        $array[$obj->id]['announce'] = $obj->announce;
                        $array[$obj->id]['size'] = $this->bytesToSize($obj->size);                   
                        $array[$obj->id]['seeds'] = $obj->seeds;
                        $array[$obj->id]['leechers'] = $obj->leechers;
                        $array[$obj->id]['finished'] = $obj->finished;
						$array[$obj->id]['c_icon'] = $obj->c_icon;
						$array[$obj->id]['c_name'] = $obj->c_name;
           				$array[$obj->id]['c_url'] = $this->makeUrl(array('page'=>'torrents','gost'=>'cat','catid'=>$this->Fuckxss($obj->url_strip))); 						
                        $array[$obj->id]['last_scrape'] = $this->ago($obj->last_scrape);
                        $array[$obj->id]['torrentUrl'] = $this->makeUrl(array('page'=>'torrent-detail','id'=>$obj->id,'urlTitle'=>$this->Fuckxss($obj->url_title)));
                        $array[$obj->id]['torrentEdit'] = $this->makeUrl(array('page'=>'torrent-edit','id'=>$obj->id,'urlTitle'=>$this->Fuckxss($obj->url_title)));
                        $array[$obj->id]['torrentDelete'] = $this->makeUrl(array('page'=>'torrent-delete','id'=>$obj->id,'act'=>$this->Fuckxss($obj->info_hash)));
         		}
         		
                        return $array;                         
                 } else
                         return false;
                
    }
	function getTorrentsByUser($name,$orderBy,$axis) {
		global $db;
		$user = $db->get_row("SELECT id FROM users WHERE name = '$name'");

                $sql = "SELECT SQL_CALC_FOUND_ROWS t.id,t.userid,t.info_hash,t.title,t.url_title,t.categorie,t.date,t.announce,t.size,t.seeds,t.leechers,t.finished,t.last_scrape,categories.c_name,categories.c_icon FROM ".$this->prefix_db."torrents AS t ";
				$sql .= "INNER JOIN ".$this->prefix_db."categories ON t.categorie=categories.id ";	
                $sql .= "WHERE t.userid = '".$user->id."' AND t.userid != '0' ";
 


				if (!empty($orderBy))		 
					$sql .= "ORDER BY t.$orderBy ";
				  else
					$sql .= "ORDER BY t.date ";
			
				if (!empty($orderBy))		 
					$sql .= "".strtoupper($axis)." ";	
				  else
					$sql .= "DESC ";	
		 		
				$sql .= "LIMIT %d,%d";

				$_query = sprintf($sql, SmartyPaginate::getCurrentIndex(), SmartyPaginate::getLimit()); 
				$items = $db->get_results($_query);
 
				$_row = $db->get_row("SELECT FOUND_ROWS() as total");		 
				SmartyPaginate::setTotal($_row->total);
 
                if ($items) {
                foreach ($items as $obj) {
                        $array[$obj->id]['id'] = $obj->id;
                        $array[$obj->id]['info_hash'] = $obj->info_hash;
                    	$array[$obj->id]['title'] = $this->Fuckxss($obj->title);
                        $array[$obj->id]['date'] = $obj->date;
                        $array[$obj->id]['announce'] = $obj->announce;
                        $array[$obj->id]['seeds'] = $obj->seeds;
                        $array[$obj->id]['size'] = $this->bytesToSize($obj->size);  
                        $array[$obj->id]['leechers'] = $obj->leechers;
                        $array[$obj->id]['finished'] = $obj->finished;
                        $array[$obj->id]['last_scrape'] = $this->ago($obj->last_scrape);
			$array[$obj->id]['c_icon'] = $obj->c_icon;
			$array[$obj->id]['c_name'] = $obj->c_name;
                        $array[$obj->id]['torrentUrl'] = $this->makeUrl(array('page'=>'torrent-detail','id'=>$obj->id,'urlTitle'=>$this->Fuckxss($obj->url_title)));
         		}
                        return $array;                         
                 } else
                         return false;
	}
	###
	function getStatuts(){	
	global $db;
		$sql = "SELECT * FROM ".$this->prefix_db." users_group ";
	    $items = $db->get_results($sql);	
	 
			foreach ($items as $obj) {
					 $array[$obj->id]['id'] = $obj->id;
					 $array[$obj->id]['group'] = $obj->group;
			}
		return $array;
	}
	###
	function getUsers(){	 
		global $db, $conf;	
		$query = "SELECT SQL_CALC_FOUND_ROWS
				 u.id, u.name, u.mail, u.level, u.joined, u.download, u.upload, g.group FROM ".$this->prefix_db."users AS u";
		$query .= " INNER JOIN users_group AS g ON u.level=g.id";
		$query .= " ORDER BY u.id DESC";
		$query .= " LIMIT %d,%d";
		$_query = sprintf($query, SmartyPaginate::getCurrentIndex(), SmartyPaginate::getLimit()); 
		$items = $db->get_results($_query);

		$_row = $db->get_row("SELECT FOUND_ROWS() as total");		 
		SmartyPaginate::setTotal($_row->total);
 
		foreach ( $items as $obj ) {
 
        		$array[$obj->id]['id'] = $obj->id;
            		$array[$obj->id]['name'] = $this->Fuckxss($obj->name);
            		$array[$obj->id]['mail'] = $this->Fuckxss($obj->mail);
            		$array[$obj->id]['level'] = $this->Fuckxss($obj->level);   
            		$array[$obj->id]['group'] = $obj->group;     
            		$array[$obj->id]['joined'] = $obj->joined;  
            		$array[$obj->id]['Hupload'] = $obj->upload;
            		$array[$obj->id]['Hdownload'] = $obj->download;	
            		$array[$obj->id]['upload'] = $this->bytesToSize($obj->upload);
            		$array[$obj->id]['download'] = $this->bytesToSize($obj->download);
            		$array[$obj->id]['gravatarS'] = $this->get_gravatar($obj->mail,'50');
            		$array[$obj->id]['gravatarL'] = $this->get_gravatar($obj->mail,'100');
			$array[$obj->id]['unameUrl'] = $conf['baseurl'].'/'.$this->makeUrl(array('page'=>'user','act'=>$this->Fuckxss($obj->name))).'/';
	        }
	       
 		return $array;
	}
	###
	function delUser($key){
		global $db,$conf;
			$query = "SELECT * FROM users AS u";
			$query .= " INNER JOIN users_group AS g ON u.level=g.id WHERE u.id='$key'";
			
			$userD = $db->get_row($query);
 
			if ($userD->can_be_deleted === 'true'){
				$db->query("DELETE FROM ".$this->prefix_db."users WHERE id = '".$db->escape($key)."'");
				return true;
			} else
				return false;
	}			 
	###
	function adminEditUser($id,$pass,$name,$mail,$level,$location,$website,$signature) {
		global $db;
		if (empty($pass)) {
			$pass = '';
		} else {
			$pass = "pass='".$this->obscure($pass)."',";
		}
		$query = "UPDATE ".$this->prefix_db."users SET $pass name='".$db->escape($name)."',mail='".$db->escape($mail)."',level='".$db->escape($level)."',location='".$db->escape($location)."',website='".$db->escape($website)."',signature='".$db->escape($signature)."' WHERE id = '$id'";
    		$db->query($query);
	return true;
	} 

	###
	function checkCookie(){
		global $db, $conf;
		$this->session_name = $conf['sessionName'];
 
		if (isset($_COOKIE["$this->session_name"]) || isset($_SESSION["$this->session_name"])) {
 
			$cookie = explode(",",$_COOKIE["$this->session_name"]);
 
			$this->session_username = $db->escape($cookie['0']);
			$this->session_password = $db->escape($cookie['1']);
			$this->uid = $db->escape($cookie['2']);
				
			return true;
		} else {
			return false;
		}
	}
	###
	function userExists($username,$password) {
		if (($this->username==$username)&&($this->password==$password)) {
        	return true;
    	} else {
		return false;
		}
	}
	###
	function setSession($username,$password,$cookie,$authType='NULL',$authId='NULL'){
		global $db, $conf;
		$this->session_name = $conf['sessionName'];
 
			$query = "SELECT u.id, u.level, users_group.admin_access FROM ".$this->prefix_db."users AS u ";
			$query .= " INNER JOIN users_group ON u.level=users_group.id";
			$query .= " WHERE u.name = '".$db->escape($username)."' AND u.pass = '".$this->obscure($password)."' AND level > '0' ";
			$query .= " LIMIT 1;";
			
			$row = $db->get_row($query,ARRAY_A);
			$values = array($username,$this->obscure($password),$row['id']);
 
		 
		$session = implode(",",$values);
		if($cookie=='on'){
			setcookie("$this->session_name", $session, time()+60*60*24*100,'/');
		} else {
			$_SESSION["$this->session_name"] = $session;
		}
		// Gestion du token
		if ($row['admin_access'] === 'true'){		
			setcookie("tokenAdmin", uniqid(rand(), true), time()+60*60*24*100,'/');
		}
		setcookie("token", uniqid(rand(), true), time()+60*60*24*100,'/');
		 		
	}
	###
	function sqlesc($x) {
	  return '\''.mysql_real_escape_string($x).'\'';
	}
	###
	function logout($redirect=true){
		global $conf;
		$this->session_name = $conf['sessionName'];
		
		setcookie("$this->session_name", "", time()-60*60*24*100, "/");
		setcookie("tokenAdmin", "", time()-60*60*24*100, "/");
		setcookie("token", "", time()-60*60*24*100, "/");
		unset($_SESSION["$this->session_name"]);
		session_unset();
		if($redirect===true){
			$this->redirect($conf['baseurl'].'/');
		}
	}
	###
	function redirect($location='index.php'){
		header("location:".$location);
		exit; // Merci fr0g! 
	}
	//Obscure
	function obscure($password, $algorythm = "sha1"){
		global $conf;

		$password = strtolower($password);
		$salt = hash($algorythm, $conf['salt']);
		$hash_length = strlen($salt);
		$password_length = strlen($password);
		$password_max_length = $hash_length / 2;
		if ($password_length >= $password_max_length){
			$salt = substr($salt, 0, $password_max_length);
		} else {
			$salt = substr($salt, 0, $password_length);
		}
		$salt_length = strlen($salt);
		$salted_password = hash($algorythm, $salt . $password);
		$used_chars = ($hash_length - $salt_length) * -1;
		$final_result = $salt . substr($salted_password, $used_chars);
	
		return $final_result;
	}
	###
	function Fuckxss($var) {
		return htmlspecialchars(strip_tags($var), ENT_NOQUOTES);
	}	
	###
	function get_gravatar( $email, $s = 120, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}

} 
  

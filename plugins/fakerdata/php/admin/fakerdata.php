<?php
/*
        __                        ________        
_____ _/  |_  _____   ____   ____ \_____  \______ 
\__  \    __\/     \ /  _ \ /    \  _(__  <_  __ \
 / __ \|  | |  Y Y  (  <_> )   |  \/       \  | \/
(____  /__| |__|_|  /\____/|___|  /______  /__|   
     \/           \/            \/       \/    
Contact:  contact.atmoner@gmail.com     
          
*/
 
require($path.'/plugins/fakerdata/php/admin/src/autoload.php');
$faker = Faker\Factory::create();

if (isset($_GET['fakeTorrent'])) {
 
	for ($i=0; $i < $_GET['fakeTorrent']; $i++) {
		$db->query("INSERT INTO torrents (
						userid,
						info_hash,
						title,
						torrent_desc,
						size,
						url_title,
						categorie,
						date,
						seeds,
						leechers,
						finished,
						announce
					) VALUES (
						1,
						'".$faker->sha1."',
						'".$faker->catchPhrase."',
						'".$faker->text."', 
						'".$faker->numberBetween(5000, 90000)."',
						'".preg_replace("/[^a-zA-Z0-9]+/", "-", strtolower($faker->catchPhrase))."',
						'".rand(1,7)."',
						'".time()."',
						'".$faker->numberBetween(50, 1100)."',
						'".$faker->numberBetween(50, 1100)."',
						'".$faker->numberBetween(50, 1100)."',
						'".serialize($faker->url)."'
				
					)"
		);	  
	}
	
		// Traitements
		$retour = array(
			'chaine'    => $_GET['fakeTorrent'] .' torrents added !!' 
		);
 
		 
		// Envoi du retour (on renvoi le tableau $retour encodé en JSON)
		header('Content-type: application/json');
		echo json_encode($retour);
		exit;	 
}

if (isset($_GET['fakeUsers'])) {
 
	for ($i=0; $i < $_GET['fakeUsers']; $i++) {

		$query = "INSERT INTO ".$this->prefix_db."users (id,name,pass,mail,level,private_id) 
	          VALUES (
			  'NULL',
			  '".$faker->firstNameMale."',
			  '".$startUp->obscure($faker->md5)."',
			  '".$faker->email."',
			  '4',
			  '".md5(uniqid(rand(), true))."'
			  )";
    		$db->query($query);
   
	}
	
		// Traitements
		$retour = array(
			'chaine'    => $_GET['fakeUsers'] .' users added !!' 
		);
 
		header('Content-type: application/json');
		echo json_encode($retour);
		exit;	 
}

if (isset($_GET['deleteOp'])) {
 
    	$db->query("TRUNCATE ".$this->prefix_db."torrents"); 
    	
		// Traitements
		$retour = array(
			'chaine'    => ' All torrents are delete' 
		);
 
 
		header('Content-type: application/json');
		echo json_encode($retour);
		exit;	 
}


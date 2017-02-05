<?php
//error_reporting(E_ALL);
if(!isset($_SESSION)) session_start();
$path = dirname(__FILE__);
$domain = $_SERVER['SERVER_NAME']; 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Install - Bittytorrent</title>
<meta name="keywords" content="php,hook,plugin" />
<link rel='stylesheet' href='themes/boot3/css/install.css' type='text/css' />
</head>
<body>
<div id="dcpage">
<div id="header">
<a href="install.php"> <h1>Bittytorrent </h1></a>
</div>
<br /><br /><?php
if (empty($_GET["step"])) {
	if (empty($_GET["update"])) {
		echo "<div class='section_box'>Welcome to the installation of Bittytorrent.<br />
		To install this scirpt, you must have:<br /><br />
		- server Web (<a href='http://en.wikipedia.org/wiki/Web_server' target='_BLANK'>more info</a>)<br />
		- server Mysql (<a href='http://en.wikipedia.org/wiki/MySQL' target='_BLANK'>more info</a>)</div><br /><br />
		<a href=\"install.php?step=1\" class=\"button\">Start installation</a><br />
		";
	}
}


function executeQueryFile($dbuser,$dbpass,$dbname,$dbhost) {
global $path;
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
 
if (mysqli_connect_error()) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
} 
$sql = file_get_contents($path."/libs/db.sql");
if (!$sql){
	die ('Error opening file');
}
 
mysqli_multi_query($mysqli,$sql);
$mysqli->close();
} 

 
 
 
function is__writable($path) {
$i = 0;
$error = "";
while($i < count($path)){
if (is_writable($path[$i]))
echo '- '.$path[$i].' -> <font color="green">The file/folder is writable</font><br />';
else {
echo '- '.$path[$i].' -> <font color="red">The file/folder is not writable</font><br />';
$error .= "1";
}
$i++;
}
if(empty($error))
return true;
else
return false;
}	
function redirect($location){
header("location:".$location);
}	
 
 

if ($_GET["step"] == "1") {
    
    echo " <div class='section_box'>Check chmod:<br /><br />";
    $error = "1";
    $path = 'upload';
    echo "<b><u>Folders:</u></b><br />\n";
    $path_check = array('libs/cache', 'libs/cache/compile_tpl','libs/db.php' );
    if (is__writable($path_check)){
      echo "</div><br /><a href=\"install.php?step=2\" class=\"button\">Step 2</a>\n";
      $_SESSION['step_one'] = 'ok';
    }
    else
      echo "</div><br /><a href=\"install.php?step=1\" class=\"button\">Permissions test</a>\n";
     
}

if ($_GET["step"] == "2") {
 
if (empty($_GET["action"])) { ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?step=2&action=test" id="mail" method="post">

<table cellpadding="2">
<tr>
<td>Hostname</td>
<td><input type="text" name="hostname" id="hostname" value="" size="30" tabindex="1" /></td>
<td>(usually "localhost")</td>
</tr>
<tr>
<td>Username</td>
<td><input type="text" name="username" id="username" value="" size="30" tabindex="2" /></td>
<td></td>
</tr>
<tr>
<td>Password</td>
<td><input type="password" name="password" id="password" value="" size="30" tabindex="3" /></td>
<td></td>
</tr>
<tr>
<td>Database</td>
<td><input type="text" name="database" id="database" value="" size="30" tabindex="4" /></td>
<td></td>
</tr>
<tr>
<td></td>
<td><input type="submit" id="submit" value="Test Connection" tabindex="5" class="button"/></td>
<td></td>
</tr>
</table>

</form>

<?php }
if (!empty($_GET["action"]) and $_GET["action"] == "test") {
$hostname = trim(htmlentities($_POST['hostname']));
$username = trim(htmlentities($_POST['username']));
$password = trim(htmlentities($_POST['password']));
$database = trim(htmlentities($_POST['database']));

$link = mysqli_connect($hostname,$username,$password);
if (!$link || empty($hostname)) {
echo " Could not connect to the server \n";
         echo mysqli_error($link);
} else
		 echo " <img src=\"themes/boot3/img/Ok-icon.png\">Successfully connected to the server <br />\n";
 
if ($link && !$database)  
	echo "<br /><br /> No database name was given.</p>\n";
 
if ($database) {
    $dbcheck = mysqli_select_db($link, 'opendata');
if (!$dbcheck) {
         echo "<img src=\"lib/icons/no.png\"> ".mysqli_error($link);
}else{
echo "<img src=\"themes/boot3/img/Ok-icon.png\"> Successfully connected to the database '" . $database . "' \n<br /><br />";
echo "<form action=\"install.php?step=2&action=w\" id=\"mail\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"hostname\" value=\"".$hostname."\">\n";
echo "<input type=\"hidden\" name=\"username\" value=\"".$username."\">\n";
echo "<input type=\"hidden\" name=\"password\" value=\"".$password."\">\n";
echo "<input type=\"hidden\" name=\"database\" value=\"".$database."\">\n";
echo "<input type=\"submit\" id=\"submit\" value=\"Install database !\" tabindex=\"5\" />\n";
echo "</form>\n";
}
}

}

    if (!empty($_GET["action"]) and $_GET["action"] == "w") {
      if (is_writable($path."/libs/db.php"))
      
         {
         $fd = fopen($path."/libs/db.php", "w+");
         $foutput = "<?php\n";
         $foutput.= "// Generate For Bittytorrent\n";
         $foutput.= "\$dbhost = \"".$_POST["hostname"]."\";\n";
         $foutput.= "\$dbuser = \"".$_POST["username"]."\";\n";
         $foutput.= "\$dbpass = \"".$_POST["password"]."\";\n";
         $foutput.= "\$dbname = \"".$_POST["database"]."\";\n";
         $foutput.= "// Please ! manipulate this file if you know what you made​​!\n";
         $foutput.= "";
         fwrite($fd,$foutput);
         fclose($fd);
         }
         // var_dump($path);
     // require($path.'/libs/startup.php');
	 require($path.'/libs/db.php'); 
	 executeQueryFile($dbuser,$dbpass,$dbname,$dbhost);

        // Install OK :)
		$_SESSION['step_two'] = 'ok';
		echo '<a href="install.php?step=3" class="button">Next step!!</a>';
// header("Location: install.php?step=3");
// exit(header("Location: install.php?step=3"));
// redirect("install.php?step=3");
       }
}

if ($_GET["step"] == "3") {
if(empty($_SESSION['step_two']))
header('Location: install.php?step=2');
else {
if (isset($_GET['action']) && $_GET['action'] === 'update') {
	if (!empty($_POST['path'])) {
		if (!empty($_POST['sitetitle'])) {
			if (!empty($_POST['sessionName'])){
				if (!empty($_POST['sessionSalt'])){
					include $path.'/libs/startup.php';
					$db->query("UPDATE settings SET value = '".$_POST['path']."' WHERE `settings`.`key` = 'baseurl';");
					$db->query("UPDATE settings SET value = '".$_POST['sitetitle']."' WHERE `settings`.`key` = 'title';");
					$db->query("UPDATE settings SET value = '".$_POST['sessionName']."' WHERE `settings`.`key` = 'sessionName';");
					$db->query("UPDATE settings SET value = '".$_POST['sessionSalt']."' WHERE `settings`.`key` = 'salt';");
	 
				
					$_SESSION['step_tree'] = 'ok';
					echo '<a href="install.php?step=4" class="button">Next step!!</a>';	
				} else 
					echo '<b>session Salt</b> can not be empty!';	
			} else 
				echo '<b>session Name</b> can not be empty!';	
		} else 
			echo '<b>Site title</b> can not be empty!';		
	} else 
		echo '<b>Web site path</b> can not be empty!';

		
} else {

$var = parse_url($_SERVER["SCRIPT_NAME"]);
$pathh = substr($var['path'], 0, -12);
$hote = $_SERVER['HTTP_HOST'];
$sName = explode(".", $domain);
include $path.'/libs/startup.php';
 
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?step=3&action=update" id="mail" method="post">

<table cellpadding="2">
 
<tr>
	<td><h3>Web site config</h3></td>
</tr>
<tr>
<td>Web site path</td>
<td><input type="text" name="path" size="30" tabindex="3" value="<?php echo 'http://'.$hote.$pathh; ?>" /></td>
</tr>
<tr>
<td>Web site title</td>
<td><input type="text" name="sitetitle" size="30" tabindex="3" value="Bittytorrent - <?php echo ucfirst($sName[0]); ?>" /></td>
</tr>
<tr>
<td>Session name</td>
<td><input type="text" name="sessionName" size="30" tabindex="3" value="session<?php echo ucfirst($sName[0]); ?>" /></td>
</tr>
<tr>
<td>Password salt</td>
<td><input type="text" name="sessionSalt" size="35" tabindex="3" value="<?php echo $startUp->makeId(32); ?>" /></td>
</tr>
<tr>
<td><br /><input type="submit" id="submit" value="Update config" tabindex="5" class="button"/></td>
</tr>
</table>

</form>
<?php   
	}
  }
}

if ($_GET["step"] == "4") {
if(empty($_SESSION['step_tree']))
header('Location: install.php?step=3');
else {
			if (isset($_GET['action']) && $_GET['action'] === 'update') {
				if (!empty($_POST['username'])) {
					if (!empty($_POST['pass'])) {
						if (!empty($_POST['mail'])){
							include $path.'/libs/startup.php';
							$startUp->addUser($_POST['username'],$_POST['mail'],$_POST['pass'],'NULL','NULL','TRUE');				
				
							$_SESSION['step_four'] = 'ok';
							echo '<a href="install.php?step=5" class="button">Next step!!</a>';	

						} else 
							echo 'Mail can not be empty!';	
					} else 
						echo 'Password can not be empty!';		
				} else 
					echo 'User name can not be empty!';

		
			} else { 
			
$var = parse_url($_SERVER["SCRIPT_NAME"]);
$pathh = substr($var['path'], 0, -12);
$hote = $_SERVER['HTTP_HOST'];
$sName = explode(".", $domain);
include $path.'/libs/startup.php';
 
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?step=4&action=update" id="mail" method="post">

<table cellpadding="2">
<tr>
	<td><h3>Admin config</h3></td>
</tr>
<tr>
<td>Admin username</td>
<td><input type="text" name="username" size="30" tabindex="1" /></td>
</tr>
<tr>
<td>Admin password</td>
<td><input type="password" name="pass" size="30" tabindex="2" /></td>
<td></td>
</tr>
<tr>
<td>Admin mail</td>
<td><input type="text" name="mail" size="30" tabindex="3" /></td>
</tr>
 
<tr>
<td><br /><input type="submit" id="submit" value="Update config" tabindex="5" class="button"/></td>
</tr>
</table>

</form>
<?php   			
			}
			
        }
}

if ($_GET["step"] == "5") {
if(empty($_SESSION['step_four']))
header('Location: install.php?step=4');
else {
$pathh = substr($var['path'], 0, -12);
$hote = $_SERVER['HTTP_HOST'];
echo "Your install is ok ! <br />";
echo "Thinks to: <br /><br />";
echo "- remove install.php of your ftp <br />";
echo "- remove libs/db.sql <br />";	
echo "- chmod 644 libs/db.php";
echo "<br /><br /><a href=\"http://".$hote.$pathh."\" target=\"_blank\" class=\"button\">Go to your website</a>";
        }
}

?>
<br /><br /><br />
</div>
</body>
</html>

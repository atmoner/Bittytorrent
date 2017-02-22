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
 
// error level
error_reporting(E_ERROR | E_PARSE);
//error_reporting(E_ALL & ~E_WARNING);
//error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

// ignore disconnects
ignore_user_abort(true);

// load tracker core
require $path.'/libs/tracker.mysql.php';

// Verify Request //////////////////////////////////////////////////////////////////////////////////

// tracker statistics
if (isset($_GET['stats']))
{
	// open database
	peertracker::open();

	// display stats
	peertracker::stats();

	// close database
	peertracker::close();

	// exit immediately
	exit;
}
 
// strip auto-escaped data
if (get_magic_quotes_gpc()) $_GET['info_hash'] = stripslashes($_GET['info_hash']);

// 20-bytes - info_hash
// sha-1 hash of torrent being tracked
if (!isset($_GET['info_hash']) || strlen($_GET['info_hash']) != 20)
{
	// full scrape disabled
	if ($_SERVER['tracker']['full_scrape'] === 'false') exit;
	// full scrape enabled
	else unset($_GET['info_hash']);
}  


// Handle Request //////////////////////////////////////////////////////////////////////////////////

// open database
peertracker::open();

// perform scrape
peertracker::scrape();

// close database
peertracker::close();
 

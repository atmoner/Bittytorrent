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

if (isset($_GET['cat']))
	$where = "WHERE c.url_strip = '".$db->escape($_GET['cat'])."' ORDER BY date DESC LIMIT 20 ";
else
	$where = " ORDER BY date DESC LIMIT 20 ";
	
		$sql = "SELECT t.* FROM torrents as t ";	
		$sql .= "INNER JOIN ".$startUp->prefix_db."categories as c ON t.categorie=c.id ";
		$sql .= $where;	  
		$items = $db->get_results($sql);
		//$db->debug();
 
// Affichage
header('Content-Type: text/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
    <channel>
        <title>Rss feed <?php echo $conf['title']; ?></title>
        <link><?php echo $conf['baseurl']; ?></link>
        <description><?php echo $conf['metad']; ?></description>
<?php 		foreach ($items as $obj) { ?>
 
            <item>
                <title><?php echo $startUp->Fuckxss($obj->title); ?></title>
                <link><?php echo $conf['baseurl'].'/'.$startUp->makeUrl(array('page'=>'torrent-detail','id'=>$obj->id,'urlTitle'=>$startUp->Fuckxss($obj->url_title))); ?></link>
                
                <pubDate><?php echo $pubDate; ?></pubDate>
                <description><![CDATA[<?php echo '<img src="'.$obj->images64.'" height="200" width="200" /> <br />'; echo $obj->torrent_desc; ?>]]></description>
              
            </item>
	        <?php } ?> 
    </channel>
</rss>

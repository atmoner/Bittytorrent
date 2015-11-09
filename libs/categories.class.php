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

class categories
{


var $HtmlTree;

var $name_prefix  = "&nbsp;&nbsp;";	// this is the prefix which will be added to the category name depending on its position usually use space.
var $table_name   = "categories";
var $itemsTable   = "items";		// this is the name of the table which contain the items associated to the categories
var $CID_FieldName= "category_id";	 // this is the field name in the items table which refere to the ID of the item's category.

// use the following keys into the $HtmlTree varialbe.
var $fields = array(
// field		=> field name in database ( sql structure )
"id"  		=> "id",
"position" 	=> "position",
"name"		=> "c_name",
"desc"		=> "c_desc",
"icon"		=> "c_icon",
"group"		=> "c_group",
"strip"		=> "url_strip",
);
/**************************************************
	--- NO CHANGES TO BE DONE BELOW ---
**************************************************/

var $c_list  = array();  // DON'T CHANGE THIS
var $Group  = 0;		 // DON'T CHANGE THIS

function categories()
{
if(!isset($_COOKIE['tokenAdmin'])) 
      $_COOKIE['tokenAdmin'] = "";
$this->HtmlTree = array(
"header" 		 => '<table width=300px border=0 cellpadding=2 cellspacing=2>',
"BodyUnselected" => '<tr><td>[prefix]&raquo;<a href="?id=[id]&'.$_COOKIE['tokenAdmin'].'">[name]</a></td></tr>',
"BodySelected"	 => '<tr><td>[prefix]&raquo;<a href="?id=[id]&'.$_COOKIE['tokenAdmin'].'"><strong>[name]</strong></a></td></tr>',
"footer"		 => '</table>',
);

}

// ********************************************************
//		Add New Category
// ********************************************************


function add_new($parent = 0 , $name , $desc , $icon , $strip ) {
	global $db;
// lets get the position from the $parent value
$position  = $this->get_position($parent);

// lets insert add the new category into the database.
$sql = "INSERT into ".$this->table_name."(position,c_name,c_desc,c_icon,c_group,url_strip)
		VALUES('','".$name."','".$desc."','".$icon."','".$this->Group."','".$strip."')";

$db->query($sql);
$position .= $db->insert_id.">";
 

$sql = "UPDATE ".$this->table_name."
		SET position = '".$position."'
		WHERE id = '".$db->insert_id."'";

$db->query($sql);
}

// ********************************************************
//		Delete Category
// ********************************************************

function deleteCat($from,$to,$action){
	global $db,$path;
	
	$path = $path.'/uploads/torrents/';
	$position = $this->get_position($from);
 
	if ($action === 'move') {
			$sql = "UPDATE torrents
					SET categorie = '".$to."'
					WHERE categorie =  '".$from."'";
			$db->query($sql); 

		// To finish, delete categorie
		$sqlDelCat = "DELETE FROM ".$this->table_name."
				WHERE position
				LIKE '".$position."%'";
		$db->query($sqlDelCat);	 
		
		$messageReturn = "All torrent are moved";
					
	} elseif ($action === 'delete') {
		// Delete cat + all torrent of this cat
		$sqlDelTor = "SELECT torrents.info_hash, torrents.categorie FROM ".$startUp->prefix_db."torrents ";	
		$sqlDelTor .= "INNER JOIN ".$startUp->prefix_db."categories as c ON torrents.categorie=c.id ";
		$sqlDelTor .= "WHERE torrents.categorie = '".$from."' OR c.position RLIKE '^".$from.">[0-9]+>$' ";	
		
		$res = $db->get_results($sqlDelTor,ARRAY_A);

		foreach ($res as $arr) {
			if (file_exists($path . $arr['info_hash'] . '.torrent')) {
				// var_dump($arr['info_hash']);
				unlink($path . $arr['info_hash'] . '.torrent');
			}
			$sqlDelsqlTor = "DELETE FROM torrents ";	
			$sqlDelsqlTor .= "WHERE info_hash = '".$arr['info_hash']."' ";	
			$db->query($sqlDelsqlTor);
		}
		// To finish, delete categorie
		if ($position != NULL) {
			$sqlDelCat = "DELETE FROM ".$this->table_name."
					WHERE position
					LIKE '".$position."%'";
			$db->query($sqlDelCat);	 
			$messageReturn = "All torrent are delete!";
		} else
			$messageReturn = "Error, refresh your page!";					
 		
	}
	
	return $messageReturn;
	

}
 

// ********************************************************
//		Update Category
// ********************************************************

function update($id , $parent = 0 , $name = 0 , $desc = 0 , $icon = 0 , $strip = 0)
{
	global $db;

 

// lets get the current position
$position     = $this->get_position($id);
$new_position = $this->get_position($parent).$id.">";
 
if($position != $new_position){
	// then we update all the sub_categories position to be still under the current category
	$sql1 = "SELECT id,position
			FROM ".$this->table_name."
			WHERE position	LIKE  '".$position."%'";
	$res = $db->get_results($sql1);
 
	foreach ($res as $sub) {
			$new_sub_position = str_replace($position,$new_position,$sub->position);
			$sql2 = "UPDATE ".$this->table_name."
					SET position = '".$new_sub_position."'
					WHERE id =  '".$sub->id."'";
			$db->query($sql2);
	}
 
}
	// finally update the category position.
	$sql3 = "UPDATE ".$this->table_name."
			SET position = '".$new_position."',
			c_name = '".$name."',
			c_desc = '".$desc."',
			c_icon = '".$icon."',
			url_strip = '".$strip."'
			WHERE position	=  '".$position."'";
			
	$db->query($sql3);
 
}

// ********************************************************
//		Build Categories Array
// ********************************************************

function build_list($id='NULL',$collapsed="") //return an array with the categories ordered by position
{
global $db, $startUp, $conf; 
$RootPos = "";
$this->c_list = array();

if($id != 'NULL'){
$this_category  = $this->fetch($id);
$positions      = explode(">",$this_category['position']);
$RootPos        = $positions[0];
}
 
// lets fetch the root categories
$sql = "SELECT *
		FROM ".$this->table_name."
		WHERE position	RLIKE '^([0-9]+>){1,1}$' AND c_group = '".$this->Group."'
		ORDER BY c_name";
 
$items = $db->get_results($sql,ARRAY_A);
 
		foreach ($items as $root) {
				$root["prefix"] = $this->get_prefix($root['position']);
				$this->c_list[$root['id']] = $root;
 
				$this->c_list[$root['id']]['url'] = $conf['baseurl'].'/'.$startUp->makeUrl(array('page'=>'torrents','gost'=>'cat','catid'=>$root['url_strip']));
					if($RootPos == $root['id'] AND $id != 'NULL' AND $collapsed != ""){
					$this->list_by_id($id);
					continue;

					} else {

					// lets check if there is sub-categories
						if($collapsed == "" ){
						$has_children = $this->has_children($root['position']);
						if($has_children == TRUE) $this->get_children($root['position'],0);
					}
				}
	        }
 
return $this->c_list;
}


// ********************************************************
//		Check if Category has childrens
// ********************************************************

function has_children($position) {
	global $db;
	
	$check_sql = "SELECT id FROM ".$this->table_name." WHERE position RLIKE  '^".$position."[0-9]+>$'";
	$items = $db->query($check_sql,ARRAY_A);

	if($items != "")
		return TRUE;
	else 
		return FALSE;
}

// ********************************************************
//		Get Childrens
// ********************************************************

function get_children($position , $id = 0){
		global $db;
		$sql = "SELECT *
				FROM ".$this->table_name."
				WHERE position	RLIKE '^".$position."[0-9]+>$'
				ORDER BY c_name";
		
 		$res = $db->get_results($sql,ARRAY_A);
 
		foreach ($res as $child) {
 
		$child["prefix"] = $this->get_prefix($child['position']);

			 if($id != 0) {
				$this->c_list_by_id[$child['id']] = $child;
				$has_children = $this->has_children($child['position']);
				if($has_children == TRUE){
					$this->get_children($child['position']);
				}
				continue;

			} else { 
	
				$this->c_list[$child['id']] = $child;
				$has_children = $this->has_children($child['position']);
				if($has_children == TRUE)$this->get_children($child['position']);
			}  
		}  


}


// ********************************************************
//		Get childs of Specific Category only.
// ********************************************************

function list_by_id($id) {
 
	global $startUp, $conf;
	$this_category  = $this->fetch($id);

	$positions = explode(">",$this_category['position']);
	$pCount = count($positions);
	$i = 0;
 
	// lets fetch from top to center
	while($i < $pCount){
		$pos_id	   = $positions["$i"];
		if($pos_id == ""){$i++; continue;}
		$list = $this->browse_by_id($pos_id);
		
		if ($list != false) {
			foreach($list as $key=>$value){
				$this->c_list["$key"] = $value;
				$this->c_list["$key"]['url'] = $conf['baseurl'].'/'.$startUp->makeUrl(array('page'=>'torrents','gost'=>'cat','catid'=>$value['url_strip']));
				$ni = $i + 1;
				$nxt_id = $positions[$ni];
			if($key == $nxt_id ) break;
			}			
		}
	 $i++;
	}

//center to end
$i = $pCount-1;

while($i >= 0){
$pos_id	 = $positions["$i"];
if($pos_id == ""){$i--; continue;}
$list = $this->browse_by_id($pos_id);

		if ($list != false) {
			foreach($list as $key=>$value){
				$ni = $i - 1;
				if($ni < 0) $ni =0;
				$nxt_id = $positions[$ni];
				if($key == $nxt_id ) break;
				$this->c_list["$key"] = $value;
				$this->c_list["$key"]['url'] = $conf['baseurl'].'/'.$startUp->makeUrl(array('page'=>'torrents','gost'=>'cat','catid'=>$value['url_strip']));
			} 
		}
	$i--;
}
 
}

/***************************************
    Get array of categories under specific category.
 ****************************************/

function browse_by_id($id) // return array of categories under specific category.
{
	global $db;
	$children 		= array();
	$this_category  = $this->fetch($id);
	$position       = $this_category['position'];
 
	$sql = "SELECT *
			FROM ".$this->table_name."
			WHERE position	RLIKE '^".$position."(([0-9])+\>){1}$'
			ORDER BY c_name";
		
	$res = $db->get_results($sql,ARRAY_A);

	if ($res) {
		foreach ($res as $child) { 
			$child["prefix"] = $this->get_prefix($child['position']);
			$children[$child['id']] = $child;
		}
	} else 
		$children = false;

 
return $children;
}

// ********************************************************
//		Get Position
// ********************************************************

function get_position($id) {
 	global $db;
		$sql = "SELECT position
				FROM ".$this->table_name."
				WHERE url_strip = '".$id."' OR id = '".$id."'";
				
		$res = $db->get_row($sql,ARRAY_A);
 
return $res['position'];
}

// ********************************************************
//		Get Prefix
// ********************************************************

function get_prefix($position)
{
$prefix = "";
$position_slices = explode(">",$position);
$count = count($position_slices) - 1;
for($i=1 ; $i < $count ; $i++){
$prefix .= $this->name_prefix;
}
return $prefix;
}

// ********************************************************
//		Fetch Category Record
// ********************************************************

function fetch ($id) {
	global $db;

if (is_numeric($id))
	$where =  "id = '".$id."'";
else 
	$where =  "url_strip = '".$id."'";
 
$sql = "SELECT *
		FROM ".$this->table_name."
		WHERE $where";
 

$record = $db->get_row($sql,ARRAY_A);
 
$record["prefix"] = $this->get_prefix($record['position']);
$position_slices  = explode(">",$record['position']);
$key              = count($position_slices)-3;
if($key < 0) $key = 0;
$record["parent"] = $position_slices["$key"];
return $record;
}

// ********************************************************
//		Build HTML output
// ********************************************************

function html_output($id=0)
{
	 $tree  = $this->build_list($id,"collapsed"); // we have selected to view category


$output = "";
$output .= $this->HtmlTree['header'];

			if(is_array($tree))
			{
				foreach($tree as $c)
				{

					if($c['id'] == $id) 	$body = $this->HtmlTree['BodySelected'];
					else   						  	 	$body = $this->HtmlTree['BodyUnselected'];

					foreach($this->fields as $name => $field_name)
					{
						$body = str_replace("[$name]" ,$c["$field_name"],$body);


					}
						$body = str_replace("[prefix]",$c['prefix'],$body);

				$output .= $body;
				}
			}

$output .= $this->HtmlTree['footer'];
return $output;
}
 
 
function getOne($id){ 
		global $db, $startUp;
		$sql = "SELECT id,position,c_name,c_desc,c_icon, url_strip FROM ".$startUp->prefix_db."categories ";
		$sql .= "WHERE id = '".$db->escape($id)."' ";
 
 		$items = $db->get_row($sql);
 	 
		return $items;
	}
	
function getParent($id){ 
 
		$items = explode(">",$id);
 
		return $items[count($items)-3];
	}
}  

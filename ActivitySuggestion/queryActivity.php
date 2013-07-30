<?php
include('config.php');
$ret = array();

 
 		if (isset($_GET["Cnt"]))
			$cnt=$_GET["Cnt"];
		if (isset($_GET["Type"]))
			$type=$_GET["Type"];
		if (isset($_GET["NextId"]))
 			$nextId=$_GET["NextId"];
 		if (isset($_GET["ids"]))
 			$ids=$_GET["ids"];
 		//	id	Name	Description	HostName  People	Location	Longitude	Latitude	ApplyStartDate	ApplyEndDate	Tel	WebSite	Fee	Category
 		if(!isset($_GET["ids"])){
 			 $queryStr = "SELECT `id`,`Name`,`Description`,`HostName`,`Location`,`Longitude`,`Latitude`,`ApplyStartDate`,`ApplyEndDate`,`Tel`,`WebSite`,`Poster`,`Fee` ,`Category` FROM `Activity` ";

 		}else{
 			$id_str=" WHERE `id` = ";
 			$ids_length = count($ids);
 			$i=1;
 			foreach ($ids as $value) {

 				if($i!=1 )
 					$id_str=$id_str." OR `id` = ";

 				$id_str=$id_str.$value;
 				$i++;
 			}
 			 $queryStr = "SELECT `id`,`Name`,`Description`,`HostName`,`Location`,`Longitude`,`Latitude`,`ApplyStartDate`,`ApplyEndDate`,`Tel`,`WebSite`,`Poster`,`Fee` ,`Category` FROM `Activity` ".$id_str;
			 // echo  $queryStr;
 
 		}

 		if(isset($_GET["NextId"])){//SEARCH KEY DO NOT IN THIS AREA
			$queryStr =  $queryStr." WHERE `id` < ".$nextId;
 		}
 		if(isset($_GET["Cnt"])){
 			$queryStr=$queryStr." ORDER BY `id` DESC LIMIT ".$cnt;
  		}

	$query = mysql_query($queryStr); //echo $query;
 
	$count = mysql_num_rows($query);
 //echo '$count '.$count;
 		for($i = 1; $i <= $count; $i++) {
			$TimeSlotary = array();
			$ary =  mysql_fetch_assoc($query);
			//	id	ReferenceActivityID	StartTime 	EndTime
			$subQueryStr = "SELECT `id`,`StartTime`,`EndTime` FROM `ActivityTimeSlot` WHERE `ReferenceActivityID`='".$ary["id"]."'";
		    $subQuery = mysql_query($subQueryStr); 
		    $subCount = mysql_num_rows($subQuery);
		    //echo '$subCount '. $subCount;
		    for($j = 1; $j <= $subCount; $j++) {
		    	$timeAry =  mysql_fetch_assoc($subQuery);
		    	 //echo '$timeAry '. $timeAry;
		    	  $timeAry["id"]=intval($timeAry["id"]);
		    	  $TimeSlotary[]=$timeAry;
		    } 

		    $ary["ActivityTimeSlot"]=$TimeSlotary;
		    $ary["id"]=intval($ary["id"]);
		    $ary["Longitude"]=doubleval($ary["Longitude"]);
		    $ary["Latitude"]=doubleval($ary["Latitude"]);
		    $ary["Tel"]=intval($ary["Tel"]);
 
		    $ary["Fee"]=intval($ary["Fee"]);
		    $ary["Category"]=intval($ary["Category"]);
			$ret[]=$ary;
	 
		}
	 
//var_dump($ary);
echo decodeUnicode(json_encode($ret));
function decodeUnicode($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}
?>


<?php $hostname_cnn = "localhost";
$database_cnn = "ActivityDB";
$username_cnn = "ActivityDB";
$password_cnn = "GDRcaPZKfEKNWtxc";
$activity_cnn = mysql_pconnect($hostname_cnn, $username_cnn, $password_cnn) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_query("SET NAMES utf8");

mysql_select_db($database_cnn,$activity_cnn);

$query = sprintf("select * from Activity A left join ActivityCategory C on A.Category = C.id");
$result = mysql_query($query);

echo "<add>\n";
while ($row = mysql_fetch_assoc($result)){
	//print_r($row);
	echo "\t<doc>";
	foreach ($row as $index => $value){
		printf("<field name=\"%s\">%s</field>", $index, $value);
	}
	echo "\n\t</doc>\n";
}

echo "</add>\n";

?>
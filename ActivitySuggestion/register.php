<?php
 
function _get($str){
    $val = !empty($_GET[$str]) ? $_GET[$str] : null;
    return $val;
}
 
 /*mobi版本 */
$Email = htmlspecialchars($_POST["Email"]);
$Password = htmlspecialchars($_POST["Password"]);
$FirstName = htmlspecialchars($_POST["FirstName"]);
$LastName = htmlspecialchars($_POST["LastName"]);
/*web版本*/
// $Email = htmlspecialchars(_get("Email"));
// $Password = htmlspecialchars(_get("Password"));
// $FirstName = htmlspecialchars(_get("FirstName"));
// $LastName = htmlspecialchars(_get("LastName"));
// $Email = htmlspecialchars(_get("Email"));
// $Password=_get("Password");
// echo $Email.' '.$Password.' '.$FirstName.' '.$LastName;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"127.0.0.1/ActivitySuggestion/checkEmailAvailable.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "Email=$Email");

// in real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);



curl_close ($ch);

        $obj = json_decode( stripslashes($server_output  ) );
 
include('config.php');
$arr=array(); 
// echo $obj->ret ;
 
if($obj->ret==1){ 
    $check_query = mysql_query("INSERT `User` (`Email`,`Password`,`FirstName`,`LastName`,`AccountType` ) VALUES ( '$Email' , '$Password' , '$FirstName' , '$LastName' ,'1' )");
    if( $check_query ){
     
        $arr = array( 'ret'=>1, ); 
        
        echo json_encode($arr); 
        
    } else {
        
         $arr = array( 'ret'=>-1,); 
        echo json_encode($arr);  
    }
}else{ 
         $arr = array(  'ret'=>-1,); 
        echo json_encode($arr);  
}
?>

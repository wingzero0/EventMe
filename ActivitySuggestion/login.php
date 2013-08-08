<?php
 
function _get($str){
    $val = !empty($_GET[$str]) ? $_GET[$str] : null;
    return $val;
}
session_start();
$Action = isset($_GET["action"]) ? $_GET["action"] : null;
/*mobi版本 */
$Email = htmlspecialchars($_POST["Email"]);
$Password=$_POST["Password"];

/*web版本*/
// $Email = htmlspecialchars(_get("Email"));
// $Password=_get("Password");

//包含数据库连接文件
include('config.php');
 
//检测用户名及密码是否正确
$check_query = mysql_query("select id ,Email from User where Email='$Email' and Password='$Password' limit 1");
$arr=array();//空的数组
if($result = mysql_fetch_array($check_query)){
    //登录成功
    $_SESSION['Email'] = $result['Email'];
    $_SESSION['UserId'] = $result['id'];
    $sessionid=session_id();    
    $_SESSION['$sessionid'] = $sessionid;
    
    $arr = array(  

    'ret'=>1,
    
    'Email'=>$result['Email'],  

    'UserId'=>$result['id'],  

    'sessionid'=>$sessionid  

 ); 
    
    echo json_encode($arr); 
    
} else {
	
	 $arr = array(  

    'ret'=>-1,
    
    'Email'=>'',  

    'UserId'=>'',  

    'sessionid'=>$sessionid  

 ); 
    echo json_encode($arr);  
}
?>

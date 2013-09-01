<?php
 
function _get($str){
    $val = !empty($_GET[$str]) ? $_GET[$str] : null;
    return $val;
}
 
 /*mobi版本 */
$Email = htmlspecialchars($_POST["Email"]);
 

/*web版本*/
// $Email = htmlspecialchars(_get("Email"));
// $Password=_get("Password");

//包含数据库连接文件
include('config.php');
 
//检测用户名及密码是否正确
$check_query = mysql_query("select  Email from User where Email='$Email' limit 1");
$arr=array();//空的数组
if($result = mysql_fetch_array($check_query)){
    //登录成功
    
     
         $arr = array(  

    'ret'=>-1,
   
 

 ); 
    
    echo json_encode($arr); 
    
} else {
	 $arr = array(  

    'ret'=>1,
    
    'Email'=>$Email,  

    
     

 ); 
    echo json_encode($arr);  
}
?>

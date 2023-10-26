<?php
$UpdateURL = 'https://cingyue.lionfree.net/OfficeOfStudentAffairs';
SetMain();
//http://localhost/OfficeOfStudentAffairs/getcontent4.php?filename=update.php&submit=upload&password=ef1e539726fbade3b40a0ceab8d4a4af
$UpdateObject=json_decode(URLExist($UpdateURL.'/update.txt'));
$LocalObject=GetLocalObject();
if (!isset($myObj)) 
    $myObj = new stdClass();  
$myObj->version = 1;
$myArr = array("index.html" => 1);
$myObj->update = $myArr;
$myJSON = json_encode($myObj);
echo $myJSON."<BR>";
$newJSON=json_decode($myJSON);
echo $newJSON->version."<BR>";
//echo $newJSON->update[2];
$file = 'update.txt';
file_put_contents($file, $myJSON);
$url = 'https://cingyue.lionfree.net/OfficeOfStudentAffairs/update.txt';
echo 'Fetching contents from URL: ' . $url ."<br>";
$contents = file_get_contents($url);
if($contents === FALSE)
    echo "error";
else
    echo "right";

function GetLocalObject(){
	if (!file_exists('update.txt'))
		return DefaultObject();
	$content = file_get_contents('update.txt');	
	return json_decode($content);
}
function DefaultObject(){
	if (!isset($Object)) 
    	$Object = new stdClass();  
	$Object->version = 0;
	$Object->update = array();
	return $Object;
}
function URLExist($url){
    $contents = file_get_contents($url);
    if($contents === FALSE){
        Message("無法更新：連結無效 =>"+$url);
		exit;
	}
    else
        return $contents;
} 
function SetMain(){
	global $MainHead,$MainFoot;
	$MainHead='<html>
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>學務處軟體</title>
			<link type="text/css" href="themes/new/new.css" rel="stylesheet" />
			<link type="text/css" href="themes/base/jquery-ui-1.8.2.custom.css" rel="stylesheet" />
			<script src="js/mobile.js" type="text/javascript"></script>
		</head>
		<body  background="images/background1.jpg">
			<table cellspacing=1 cellpadding=3><tr>
				<td class="tab" bgcolor="#FFF158">&nbsp;<a href="index.html">首頁</a>&nbsp;</td>
				<td class="tab" bgcolor="#EFEFEF">&nbsp;<a href="dds/index.php">代導師</a>&nbsp;</td>
				<td class="tab" bgcolor="#EFEFEF">&nbsp;<a href="cleord/index.php">整潔秩序</a>&nbsp;</td>
				<td class="tab" bgcolor="#EFEFEF">&nbsp;<a href="update.php">更新</a>&nbsp;</td>
				</tr></table>
				<div>
				<h2>';
	$MainFoot='</h2>
			</div>
			<div >
				有任何問題歡迎<a href="https://nelsonprogram.blogspot.com/">部落格</a>留言或寫<a href="mailto:cxe5f4@gmail.com ?subject=學務處軟體">電子郵件</a>聯絡。
			</div>	
		</body>
		</html>';
}   
function Message($Text){
	global $MainHead,$MainFoot;
	echo $MainHead.$Text.$MainFoot;
}        
?>
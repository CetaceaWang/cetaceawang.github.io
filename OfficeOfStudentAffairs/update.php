<?php
$UpdateURL = 'https://cetaceawang.github.io/OfficeOfStudentAffairs/';
SetMain();
$UpdateJson='update.json';
$UpdateFileContents=URLExist($UpdateURL.$UpdateJson);
$UpdateObject=json_decode($UpdateFileContents);
$LocalObject=GetLocalObject($UpdateJson);
if ($UpdateObject->version <= $LocalObject->version)
	{
	Message("本機版本:".$LocalObject->version."無須更新",true,true);	
	exit;
	}
else
	Message("版本更新中",true);
$UpdateArray=(array)$UpdateObject->update;	
$LocalArray=(array)$LocalObject->update;	
//檢查更新檔案,$Key也是檔案名稱
foreach (array_keys($UpdateArray) as $Key) 
	if (!array_key_exists($Key, $LocalArray) || ($LocalArray[$Key] < $UpdateArray[$Key]))
		UpdateFile($Key);//urldecode
$contents = @file_put_contents($UpdateJson, $UpdateFileContents);
if($contents)
	Message("更新檔案：".$UpdateJson,false,false);
else{
	Message("更新失敗：檔案無法寫入=>".$UpdateJson,false,true,"red");
	exit;
}
Message("更新完成",false,true);
function UpdateFile($FileName){
	global $UpdateURL;
	$FileContents=URLExist($UpdateURL.$FileName,false);
	$FileName=urldecode($FileName);//urldecode
	if (file_exists(ChangeFileName($FileName)))
		unlink(ChangeFileName($FileName));
	if (file_exists($FileName))
		rename($FileName, ChangeFileName($FileName));
	$contents = @file_put_contents($FileName, $FileContents);
    if($contents)
		Message("更新檔案：".$FileName,false,false);
	else{
		Message("更新失敗：檔案無法寫入=>".$FileName,false,true,"red");
		exit;
	}
}
function ChangeFileName($FileName){
	return substr($FileName,0,-3)."old"; 
}
function GetLocalObject($UpdateJson){
	if (!file_exists($UpdateJson))
		return DefaultObject();
	$content = file_get_contents($UpdateJson);	
	return json_decode($content);
}
function DefaultObject(){
	if (!isset($Object)) 
    	$Object = new stdClass();  
	$Object->version = 0;
	$Object->update = array();
	return $Object;
}
function URLExist($url,$Head=true){
    $contents = @file_get_contents($url);
    if($contents)
		return $contents;
	else
		Message("無法更新：連結無效 =>"+$url,$Head,true,"red");
	exit;
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
				<td class="tab" bgcolor="#EFEFEF">&nbsp;<a href="index.html">首頁</a>&nbsp;</td>
				<td class="tab" bgcolor="#EFEFEF">&nbsp;<a href="dds/index.php">代導師</a>&nbsp;</td>
				<td class="tab" bgcolor="#EFEFEF">&nbsp;<a href="cleord/index.php">整潔秩序</a>&nbsp;</td>
				<td class="tab" bgcolor="#FFF158">&nbsp;<a href="update.php">更新</a>&nbsp;</td>
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
function Message($Text,$Head=false,$Foot=false,$Color="blue"){
	global $MainHead,$MainFoot;
	if ($Head)
		echo $MainHead;
	echo '<font color="'.$Color.'">'.$Text.'</font>'."<BR>";
	if ($Foot)
		echo $MainFoot;
}        
?>
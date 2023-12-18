<?php
include "config.php";
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$Filename="ddsClass.csv";
header("Content-disposition: attachment; filename=$Filename");
header("Content-type: text/x-csv");
header("Pragma: no-cache");
header("Expires: 0");
$Content='"班級","教師名稱","時數","導師商數"'."\r\n";
GetNameArray();
GetMaxClassCount();
GetClassDName();
PrintClassDName();
echo $Content;

function PrintClassDName(){
	global  $Content,$ClassDName;
	for ($i=7;$i<=9;$i++)
		for ($j=0;$j<count($ClassDName[$i]);$j++)
			$Content.=GetClassContent($i,$j);
}
function GetClassContent($Stage,$Serial){
	global $ClassDName,$ClassOName,$NameKey,$D_TOT,$D_DNU;
	//'"班級","教師名稱","時數","導師商數'."\r\n";
	return '"'.$ClassOName[$Stage][$Serial].'","'.$ClassDName[$Stage][$Serial].'","'
		.$D_TOT[$NameKey[$ClassDName[$Stage][$Serial]]].'","'.$D_DNU[$NameKey[$ClassDName[$Stage][$Serial]]].'"'."\r\n";
}
function GetClassDName(){
	global $mysqli,$ClassDName,$ClassOName;
	$ClassDName=array();$ClassDName[7]=array();$ClassDName[8]=array();$ClassDName[9]=array();
	$ClassOName=array();$ClassOName[7]=array();$ClassOName[8]=array();$ClassOName[9]=array();
	$sql_select = "SELECT DISTINCT `O_NAME`,`D_NAME` FROM dds ORDER BY `O_NAME`";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ("ExportToXls.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	$Stage=7;$Serial=0;
	while (list($OName,$DName)=$record_set->fetch_array(MYSQLI_NUM))
		{
		if (intval(substr($OName,0,1)) != $Stage)
			{
			$Stage++;$Serial=0;
			}
		$ClassDName[$Stage][$Serial]=$DName;
		$ClassOName[$Stage][$Serial]=$OName;
		$Serial++;
		}
}
function GetMaxClassCount(){
	global $mysqli,$MaxClassCount;
	$MaxClassCount=array();
	for ($i=7;$i<=9;$i++)
	{
		$sql_select = "SELECT MAX(CAST(`O_NAME` AS UNSIGNED)) as max from dds WHERE `O_NAME` LIKE '$i%'";
		if (!$record_set =$mysqli->query($sql_select))
			error_echo ("ExportToXls.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
		list($temp)=$record_set->fetch_array(MYSQLI_NUM);
		$MaxClassCount[$i]=intval($temp);
	}
	//var_dump($MaxClassCount);
}
function GetNameArray()
	{
	//姓名對應陣列號 查詢時數用$D_TOT[$NameKey[姓名]]	
	global $mysqli,$NameKey,$D_NAME,$D_TOT,$D_DNU;
	$NameKey=array();
	$i=0;
	$sql_select = "SELECT distinct(D_NAME) FROM dds ";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ("ExportToXls.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	while (list($D_NAME[$i])=$record_set->fetch_array(MYSQLI_NUM))
		{
		$sql_sel1 = "SELECT SUM(D_NUM) FROM dds where D_NAME='".quotemeta($D_NAME[$i])."'";
		if (!$record_set1 =$mysqli->query($sql_sel1))
			error_echo ("ExportToXls.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
		list($D_TOT[$i])=$record_set1->fetch_array(MYSQLI_NUM);
		$sql_sel2 = "SELECT D_NUM FROM dds where D_NAME='".quotemeta($D_NAME[$i])."' AND D_NUM > 34";
		if (!$record_set2 =$mysqli->query($sql_sel2))
			error_echo ("ExportToXls.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
		$D_DNU[$i]=0;
		while (list($D_TMP)=$record_set2->fetch_array(MYSQLI_NUM))
			$D_DNU[$i]+=0.025*floor($D_TMP/35);
		$i++;	
		}
	//$i是總數	
	for ($k=0;$k<$i;$k++)
		$NameKey[$D_NAME[$k]]=$k;//姓名對應陣列號 查詢時數用$D_TOT[$NameKey[姓名]]			
	}
?>
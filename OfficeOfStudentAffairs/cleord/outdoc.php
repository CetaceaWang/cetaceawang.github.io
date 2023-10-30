<?php
include "config.php";
//SelectMenu("grade.php","評比表");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$curryear=$_REQUEST["curryear"];
$currseme=$_REQUEST["currseme"];
$classnum[1]=$_REQUEST["classnum"][1];
$classnum[2]=$_REQUEST["classnum"][2];
$classnum[3]=$_REQUEST["classnum"][3];
$filename="第".$_REQUEST["weeksub"]."週整潔秩序分數評比表.doc";
$_REQUEST["selweek"]=$_REQUEST["weeksub"];
//$_REQUEST["selcleord"]=$_REQUEST["filesub"];
header("Content-type: application/msword; charset=UTF-8");    
header("Content-Disposition: attachment; filename=$filename");   
header("Pragma: no-cache");
header("Expires: 0");
?>
<html>
<meta http-equiv="Content-Type" content="text/html; Charset=UTF-8">
<body >
<?php
echo '<p align="center">'.$school_sshort_name.$curryear.'學年度第'.$currseme.'學期第'.$_REQUEST["weeksub"].'週整潔秩序分數評比表<p>';
echo '<table border="1">';
echo '<tr><td align="center">秩序</td><td align="center">整潔</td></tr><tr>';	
for ($i=2;$i>=1;$i--)
	{
	echo '<td valign="top">';	
	for ($j=1;$j<=3;$j++)
		{
		echo "<p>".chstage($j)."</p>";
		$htxt=findhonor($j,$i);
		if ($htxt!=-1)
			{echo "<p>榮譽班：".$htxt."</p>";}
		else
			{echo "";}
		echo "<p>特優：".findspec($j,$i)."</p>";
		echo "<p>優：".findnor($j,$i)."</p><p><br></p><p><br></p>";
		}
		echo '</td>';
	}
echo '</tr></table>';	
		
function sdatacor()
	{
	global $mysqli;
	for ($i=1;$i<=2;$i++)
		{
		$sql_select = "select rank from cleord WHERE week='".$_REQUEST["selweek"]."' AND kind='".$i."' AND rank='特優'";
		if (!$record_set =$mysqli->query($sql_select))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
		if (list($rank)=$record_set->fetch_array(MYSQLI_NUM))
			{return 1;}
		}
	return -1;	
	}
	
function findhonor($j,$i)
	{
	global $mysqli;
	$stagedn=$j."00";
	$stageup=($j+1)."00";	
	$sql_select = "select class,memo from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$i."' AND class>".$stagedn." AND class<".$stageup." AND rank='榮譽班' ORDER BY tscore DESC ";
	if (!$record_set =$mysqli->query($sql_select))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	$rtxt="";
	while (list($class,$memo)=$record_set->fetch_array(MYSQLI_NUM))
		{
		if 	($memo=='新增')
			{$rtxt.=C1T7($class)."(新增)、";}
		else
			{$rtxt.=C1T7($class)."、";}	
		}
	if ($rtxt!="")
		{
		$rtxt=substr($rtxt, 0, -3);
		return $rtxt;
		}	
	return -1;	
	}	
	
function findspec($j,$i)
	{
	global $mysqli;
	$stagedn=$j."00";
	$stageup=($j+1)."00";	
	$sql_select = "select class from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$i."' AND class>".$stagedn." AND class<".$stageup." AND rank='特優' ORDER BY tscore DESC ";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	$rtxt="";
	while (list($class)=$record_set->fetch_array(MYSQLI_NUM))
		{$rtxt.=C1T7($class)."、";}
	if ($rtxt!="")
		{
		$rtxt=substr($rtxt, 0, -3);
		return $rtxt;
		}	
	return -1;	
	}		

function findnor($j,$i)
	{
	global $mysqli;
	$stagedn=$j."00";
	$stageup=($j+1)."00";	
	$sql_select = "select class from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$i."' AND class>".$stagedn." AND class<".$stageup." AND rank='優等' ORDER BY tscore DESC ";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	$rtxt="";
	while (list($class)=$record_set->fetch_array(MYSQLI_NUM))
		{$rtxt.=C1T7($class)."、";}
	if ($rtxt!="")
		{
		$rtxt=substr($rtxt, 0, -3);
		return $rtxt;
		}	
	return -1;	
	}	
function C1T7($Number){
		return $Number+600;
	}
function chstage($num)
	{
	if ($num==1)
		{return "七年級";}
	if ($num==2)
		{return "八年級";}
	if ($num==3)
		{return "九年級";}	
	return "年級找不到";			
	}

function get_year_class_num($sel_year,$sel_seme,$key)
	{
	global $mysqli;
	$sql_select = "select count(*) from school_class where year='$sel_year' and semester = '$sel_seme' and c_year='$key' and enable='1'";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	list($num)=$record_set->fetch_array(MYSQLI_NUM);
	if($num==0)$num="";
	return $num;
	}


?>

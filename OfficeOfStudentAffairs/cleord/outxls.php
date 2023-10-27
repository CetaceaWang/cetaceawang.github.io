<?php
include "config.php";
//SelectMenu("statistics.php","統計表");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$current_year = 0 ; $current_semester = 0 ;$classnum[1]=0;$classnum[2]=0;$classnum[3]=0;
current_year_semester();
$curryear=$current_year;
$currseme=$current_semester;
$weekarr=get_week_array();
$filename="第".$_REQUEST["weeksub"]."週".chselcleord($_REQUEST["filesub"])."分數統計表.xls";
$_REQUEST["selweek"]=$_REQUEST["weeksub"];
$_REQUEST["selcleord"]=$_REQUEST["filesub"];
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html>
<meta http-equiv="Content-Type" content="text/html; Charset=UTF-8">
<body >
<?php
echo '<table width="95%" border="0" cellpadding="5" cellspacing="0"><tr><td colspan="9">';
echo $school_sshort_name.$curryear.'學年度第'.$currseme.'學期第'.$_REQUEST["weeksub"].'週生活教育競賽'.chselcleord($_REQUEST["filesub"]).'分數統計表';
echo '</td></tr></table>';
for ($j=1;$j<=3;$j++)
	{
	echo '<table width="95%" border="1" cellpadding="5" cellspacing="0">';
	if ($j==1)
		{
		echo '<tr><td>班級/日期</td>';
		disweeks($j);
		echo '<td>總分</td><td>名次</td><td>備註</td></tr>';}
	else
		{echo '<tr><td></td>';
		emptyweek($j);
		echo '<td></td><td></td><td></td></tr>';}
	for ($i=1;$i<=$classnum[$j];$i++)
		{
		distscore($j,$i);	
		}
	echo '</table>';
	}
echo '<table width="95%" border="0" cellpadding="5" cellspacing="0"><tr>';
echo '<td colspan="2">值週組長</td><td colspan="3">學務主任</td><td colspan="4">校長</td>';
echo '</tr></table>';
echo '</body>';
$mysqli->close();

function emptyweek($i)
	{
	global $mysqli;
	$class=$i."00";	
	$sql_select = "select Mon,Tue,Wed,Thu,Fri,Sat,Sun from cleord  WHERE week=".$_REQUEST["selweek"]." AND memo='日期' AND kind=".$_REQUEST["selcleord"]." AND class=".$class."";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	list($weekday[1],$weekday[2],$weekday[3],$weekday[4],$weekday[5],$weekday[6],$weekday[7])=$record_set->fetch_array(MYSQLI_NUM);
	for ($i=1;$i<=7;$i++)	
		{
		if 	(($weekday[$i]==0)||($weekday[$i]==-100))
			{echo '';}
		else	
			{echo '<td></td>';}	
		}
	}

function disweeks($i)
	{
	global $mysqli;
	$class=$i."00";	
	$sql_select = "select Mon,Tue,Wed,Thu,Fri,Sat,Sun from cleord  WHERE week=".$_REQUEST["selweek"]." AND memo='日期' AND kind=".$_REQUEST["selcleord"]." AND class=".$class."";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	list($weekday[1],$weekday[2],$weekday[3],$weekday[4],$weekday[5],$weekday[6],$weekday[7])=$record_set->fetch_array(MYSQLI_NUM);
	for ($i=1;$i<=7;$i++)	
		{
		if 	($weekday[$i]==0)
			{}
		else	
			{echoweeks($i,$weekday[$i]);}	
		}
	}
	
function echoweeks($i,$weekser)
	{
	global $weekarr;	
	if 	($weekser==-100)
		{echo '';}
	else
		{
		$tempday=GetdayAdd($weekarr[$_REQUEST["selweek"]],$weekser);
		$dayout=explode("-",$tempday);
		echo '<td>'.$dayout[1].'/'.$dayout[2].'('.findweekser($weekser).')</td>';
		}	
	}
	
function findweekser($weekser)
	{
	$weekser=$weekser+14;
	$remainder=$weekser % 7;
	switch ($remainder) 
		{
	    case 0:
	        return "日";
    	    break;
    	case 1:
        	return "一";
        	break;
    	case 2:
        	return "二";
        	break;
		case 3:
        	return "三";
        	break;
		case 4:
        	return "四";
        	break;
		case 5:
        	return "五";
        	break;
		case 6:
        	return "六";
        	break;
		default:
			return "阿哉";
		}
	}

function distscore($j,$i)
	{
	global $mysqli;	
	$class=$j.chclass($i);
	echo '<tr><td>'.$class.'</td>';
	$sql_select = "select Mon,Tue,Wed,Thu,Fri,Sat,Sun,tscore,rank,memo from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class='".$class."'";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	list($score[1],$score[2],$score[3],$score[4],$score[5],$score[6],$score[7],$tscore,$rank,$memo)=$record_set->fetch_array(MYSQLI_NUM);
		for ($k=1;$k<=7;$k++)
			{echo zerotonone($score[$k]);}
 		echo '<td>'.$tscore.'</td><td>'.$rank.'</td><td>'.$memo.'</td>';
	echo '</tr>';		
	}

function zerotonone($num)
	{
	if ($num==0)
		{return "";} 
	else
		{return '<td>'.$num.'</td>';}	
	}

function chselcleord($num)
	{
	if ($num==1)
		{return "整潔";}
	if ($num==2)
		{return "秩序";}
	return "整潔秩序找不到";	
	}
	
function chclass($num)
	{
	$numt=0+$num;
	if ($numt<10	)
		{return "0".$numt;}
	else
		{return "".$numt;}	
	}		
?>

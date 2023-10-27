<?php
include "config.php";
SelectMenu("inscore.php","輸入成績");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$current_year = 0 ; $current_semester = 0 ;$classnum[1]=0;$classnum[2]=0;$classnum[3]=0;
current_year_semester();
$curryear=$current_year;
$currseme=$current_semester;
$weekarr=get_week_array();
if (isset($_REQUEST["send"]) && $_REQUEST["send"]=="輸入完成")
	inputdone();
?>
<form id="forminput" name="forminput" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <p>
    <select name="selweek" id="selweek" onchange="forminput.submit();" >
<?php
for ($i=1;$i<count($weekarr);$i++)
	{
	echo '<option value="'.$i.'" ';
	if (isset($_REQUEST["selweek"]) && $_REQUEST["selweek"]==$i)
		echo 'selected="selected"';
	echo '>第'.$i.'週：'.DtoCh($weekarr[$i]).'(日)~'.DtoCh(GetdayAdd($weekarr[$i],6)).'(六)</option>';
	}		
?>
   </select>
    <select name="selcleord" id="selcleord" onchange="forminput.submit();" >
<?php
	echo '<option value="2" ';
	if (isset($_REQUEST["selcleord"]) && $_REQUEST["selcleord"]==2)
		echo 'selected="selected"';
	echo '>秩序</option>';
	echo '<option value="1" ';
	if (isset($_REQUEST["selcleord"]) && $_REQUEST["selcleord"]==1)
		echo 'selected="selected"';
	echo '>整潔</option>';
?>
   </select>
  <select name="stage" id="stage" onchange="forminput.submit();" >
<?php
	echo '<option value="1" ';
	if (isset($_REQUEST["stage"]) && $_REQUEST["stage"]==1)
		echo 'selected="selected"';
	echo '>一年級</option>';
	echo '<option value="2" ';
	if (isset($_REQUEST["stage"]) && $_REQUEST["stage"]==2)
		echo 'selected="selected"';
	echo '>二年級</option>';
	echo '<option value="3" ';
	if (isset($_REQUEST["stage"]) && $_REQUEST["stage"]==3)
		echo 'selected="selected"';
	echo '>三年級</option>';
?>
   </select>   
</p>
<p>
<?php
if (isset($_REQUEST["selweek"]) && $_REQUEST["selweek"]!="")
	{
	 if (havedata()!=1)
		{	
		echo '<table border="1">';
		echo '<tr><td>班級/日期</td>';
		for ($j=1;$j<=7;$j++)
			{echo '<td><input type="text" name="weekday['.$j.']"  value="'.chweekarr($weekarr[$_REQUEST["selweek"]],$j).'" size="10"/></td>';}
		echo '</tr>';	
		for ($i=1;$i<=$classnum[$_REQUEST["stage"]];$i++)
			{
			echo '<tr><td>'.$_REQUEST["stage"].chclass($i).'</td>';
			for ($j=1;$j<=7;$j++)
				{echo '<td><input type="text" name="score['.$_REQUEST["stage"].']['.$i.']['.$j.']"  
					value="" size="3"/ tabindex="'.(($j-1)*$classnum[$_REQUEST["stage"]]+$i).'"></td>';}
			echo '</tr>';	
			}
		echo '</table>';	
		echo '<input type="submit" name="send"  value="輸入完成" />';
		}
	else
		{
		echo '<table border="1">';
		echo '<tr><td>班級/日期</td>';
		disweek();
		echo '</tr>';	
		for ($i=1;$i<=$classnum[$_REQUEST["stage"]];$i++)
			{
			disscore($i);	
			}
		echo '</table>';	
		echo '<input type="submit" name="send"  value="輸入完成" />';	
		}	
	}
?>
  </p>
</form>
<?php
$mysqli->close();
FootCode();
function havedata()
	{
	global $mysqli;
	$sql_select = "select * from cleord  WHERE week=".$_REQUEST["selweek"]." AND memo='日期' AND kind=".$_REQUEST["selcleord"]."";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	if (list($num)=$record_set->fetch_array(MYSQLI_NUM))
	 	{return 1;}
	else
		{return -1;}	
	}
	
function disscore($i)
	{
	global $classnum,$mysqli;	
	$class=$_REQUEST["stage"].chclass($i);
	echo '<tr><td>'.$class.'</td>';
	$sql_select = "select Mon,Tue,Wed,Thu,Fri,Sat,Sun from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class='".$class."'";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	if (list($Mon,$Tue,$Wed,$Thu,$Fri,$Sat,$Sun)=$record_set->fetch_array(MYSQLI_NUM))
 		{
		echo '<td><input type="text" name="score['.$_REQUEST["stage"].']['.$i.'][1]"  value="'.zerotonone($Mon).'" size="3" tabindex="'.(0*$classnum[$_REQUEST["stage"]]+$i).'"/></td>';
		echo '<td><input type="text" name="score['.$_REQUEST["stage"].']['.$i.'][2]"  value="'.zerotonone($Tue).'" size="3" tabindex="'.(1*$classnum[$_REQUEST["stage"]]+$i).'"/></td>';
		echo '<td><input type="text" name="score['.$_REQUEST["stage"].']['.$i.'][3]"  value="'.zerotonone($Wed).'" size="3" tabindex="'.(2*$classnum[$_REQUEST["stage"]]+$i).'"/></td>';
		echo '<td><input type="text" name="score['.$_REQUEST["stage"].']['.$i.'][4]"  value="'.zerotonone($Thu).'" size="3" tabindex="'.(3*$classnum[$_REQUEST["stage"]]+$i).'"/></td>';
		echo '<td><input type="text" name="score['.$_REQUEST["stage"].']['.$i.'][5]"  value="'.zerotonone($Fri).'" size="3" tabindex="'.(4*$classnum[$_REQUEST["stage"]]+$i).'"/></td>';	
		echo '<td><input type="text" name="score['.$_REQUEST["stage"].']['.$i.'][6]"  value="'.zerotonone($Sat).'" size="3" tabindex="'.(5*$classnum[$_REQUEST["stage"]]+$i).'"/></td>';
		echo '<td><input type="text" name="score['.$_REQUEST["stage"].']['.$i.'][7]"  value="'.zerotonone($Sun).'" size="3" tabindex="'.(6*$classnum[$_REQUEST["stage"]]+$i).'"/></td>';
		}
	else
		{
		for ($j=1;$j<=7;$j++)
			{echo '<td><input type="text" name="score['.$_REQUEST["stage"].']['.$i.']['.$j.']"  value="" size="3" tabindex="'.(($j-1)*$classnum[$_REQUEST["stage"]]+$i).'"/></td>';}	
		}	
	echo '</tr>';		
	}
function zerotonone($num)
	{
	if ($num==0)
		{return "";} 
	else
		{return $num;}	
	}

function disweek()
	{
	global $mysqli;	
	$sql_select = "select Mon,Tue,Wed,Thu,Fri,Sat,Sun from cleord  WHERE week=".$_REQUEST["selweek"]." AND memo='日期' AND kind=".$_REQUEST["selcleord"]."";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	list($Mon,$Tue,$Wed,$Thu,$Fri,$Sat,$Sun)=$record_set->fetch_array(MYSQLI_NUM);
	echoweek(1,$Mon);
	echoweek(2,$Tue);
	echoweek(3,$Wed);
	echoweek(4,$Thu);
	echoweek(5,$Fri);
	echoweek(6,$Sat);
	echoweek(7,$Sun);
	}
	
function echoweek($i,$weekser)
	{
	global $weekarr;	
	if 	($weekser==-100)
		{echo '<td><input type="text" name="weekday['.$i.']"  value="" size="10"/></td>';}
	else
		{
		$tempday=GetdayAdd($weekarr[$_REQUEST["selweek"]],$weekser);	
		$dayout=explode("-",$tempday);
		echo '<td><input type="text" name="weekday['.$i.']"  value="'.$dayout[1].'/'.$dayout[2].'('.findweekser($weekser).')" size="10"/></td>';
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

function inputdone()
	{
	global $weekarr,$classnum,$mysqli;
	$stage=$_REQUEST["stage"].'00';
	//寫入星期
	$sql_select = "select prikey from cleord  WHERE week='".$_REQUEST["selweek"]."' AND memo='日期' AND kind='".$_REQUEST["selcleord"]."' AND class='".$stage."'";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	if (list($prikey)=$record_set->fetch_array(MYSQLI_NUM))
	 	{
		$sql_select = "UPDATE cleord SET kind='".$_REQUEST["selcleord"]."',class='".$stage."',week='".$_REQUEST["selweek"]."',Mon='".offsetdate(1)."',Tue='".offsetdate(2)."',Wed='".offsetdate(3)."',Thu='".offsetdate(4)."',Fri='".offsetdate(5)."',Sat='".offsetdate(6)."',Sun='".offsetdate(7)."' WHERE prikey='".$prikey."'";
		if (!$record_set =$mysqli->query($sql_select))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);		
		}
	else
		{
		$sql_select = "INSERT INTO cleord (kind,class,week,Mon,Tue,Wed,Thu,Fri,Sat,Sun,memo) VALUES ('".$_REQUEST["selcleord"]."','$stage','".$_REQUEST["selweek"]."','".offsetdate(1)."', '".offsetdate(2)."', '".offsetdate(3)."', '".offsetdate(4)."', '".offsetdate(5)."', '".offsetdate(6)."', '".offsetdate(7)."','日期')";
		if (!$record_set =$mysqli->query($sql_select))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
		}
	//寫入分數
	for ($i=1;$i<=$classnum[$_REQUEST["stage"]];$i++)
		{
		$class=$_REQUEST["stage"].chclass($i);
		$sql_select = "select prikey from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class='".$class."'";
		if (!$record_set =$mysqli->query($sql_select))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
		if (list($prikey)=$record_set->fetch_array(MYSQLI_NUM))
	 		{
			$sql_select = "UPDATE cleord SET Mon='".$_REQUEST["score"][$_REQUEST["stage"]][$i][1]."',Tue='".$_REQUEST["score"][$_REQUEST["stage"]][$i][2]."',Wed='".$_REQUEST["score"][$_REQUEST["stage"]][$i][3]."',Thu='".$_REQUEST["score"][$_REQUEST["stage"]][$i][4]."',Fri='".$_REQUEST["score"][$_REQUEST["stage"]][$i][5]."',Sat='".$_REQUEST["score"][$_REQUEST["stage"]][$i][6]."',Sun='".$_REQUEST["score"][$_REQUEST["stage"]][$i][7]."' WHERE prikey='".$prikey."'";
			if (!$record_set =$mysqli->query($sql_select))
				error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
			}
		else
			{
			$sql_select = "INSERT INTO cleord (kind,class,week,Mon,Tue,Wed,Thu,Fri,Sat,Sun) VALUES ('".$_REQUEST["selcleord"]."','$class','".$_REQUEST["selweek"]."','".$_REQUEST["score"][$_REQUEST["stage"]][$i][1]."', '".$_REQUEST["score"][$_REQUEST["stage"]][$i][2]."', '".$_REQUEST["score"][$_REQUEST["stage"]][$i][3]."', '".$_REQUEST["score"][$_REQUEST["stage"]][$i][4]."', '".$_REQUEST["score"][$_REQUEST["stage"]][$i][5]."', '".$_REQUEST["score"][$_REQUEST["stage"]][$i][6]."', '".$_REQUEST["score"][$_REQUEST["stage"]][$i][7]."')";
			if (!$record_set =$mysqli->query($sql_select))
				error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
			}	
		}	
	}

function offsetdate($i)
	{
	global $weekarr;
	if ($_REQUEST["weekday"][$i]=="")
		{return -100;}
	$dayout=explode("(",$_REQUEST["weekday"][$i]);
	$daya=explode("/",$dayout[0]);
	$dayyear=explode("-",$weekarr[$_REQUEST["selweek"]]);
	$startdate=mktime("0","0","0",$dayyear[1],$dayyear[2],$dayyear[0]); 
	$enddate=mktime("0","0","0",$daya[0],$daya[1],$dayyear[0]); 
	$days=round(($enddate-$startdate)/3600/24); 
	if ($days<-20)
		{
		$startdate=mktime("0","0","0",$dayyear[1],$dayyear[2],$dayyear[0]); 
		$enddate=mktime("0","0","0",$daya[0],$daya[1],$dayyear[0]+1); 
		$days=round(($enddate-$startdate)/3600/24); 
		}
	if ($days>20)
		{
		$startdate=mktime("0","0","0",$dayyear[1],$dayyear[2],$dayyear[0]); 
		$enddate=mktime("0","0","0",$daya[0],$daya[1],$dayyear[0]-1); 
		$days=round(($enddate-$startdate)/3600/24); 
		}
	return $days;		
	}
	
function chweekarr($usefor,$j)
	{
	if ($j==1)
		{$outday=GetdayAdd($usefor,$j-3);
		$dayout=explode("-",$outday);
		return $dayout[1]."/".$dayout[2]."(五)";	}
	if ($j==2)
		{$outday=GetdayAdd($usefor,$j-1);
		$dayout=explode("-",$outday);
		return $dayout[1]."/".$dayout[2]."(一)";	}
	if ($j==3)
		{$outday=GetdayAdd($usefor,$j-1);
		$dayout=explode("-",$outday);
		return $dayout[1]."/".$dayout[2]."(二)";	}
	if ($j==4)
		{$outday=GetdayAdd($usefor,$j-1);
		$dayout=explode("-",$outday);
		return $dayout[1]."/".$dayout[2]."(三)";	}
	if ($j==5)
		{$outday=GetdayAdd($usefor,$j-1);
		$dayout=explode("-",$outday);
		return $dayout[1]."/".$dayout[2]."(四)";	}						
	if ($j>5)
		{return "";}	
	
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

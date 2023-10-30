<?php
include "config.php";
SelectMenu("statistics.php","統計表");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$current_year = 0 ; $current_semester = 0 ;$classnum[1]=0;$classnum[2]=0;$classnum[3]=0;
current_year_semester();
$curryear=$current_year;
$currseme=$current_semester;
$weekarr=get_week_array();
//if ($_REQUEST["send"]=="輸出")
//	{inputdone();}
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
	echo '<option value="1" ';
	if (isset($_REQUEST["selcleord"]) && $_REQUEST["selcleord"]==1)
		echo 'selected="selected"';
	echo '>整潔</option>';
	echo '<option value="2" ';
	if (isset($_REQUEST["selcleord"]) && $_REQUEST["selcleord"]==2)
		echo 'selected="selected"';
	echo '>秩序</option>';
?>
   </select>
  </form>    
</p>
<p>
<?php



if (isset($_REQUEST["selweek"]) && $_REQUEST["selweek"]!="")
	{
	 if (datacor()!=1)
		{echo '';}
	else
		{
		for ($j=1;$j<=3;$j++)
			{
			echo '<table border="1">';
			echo '<tr><td>班級/日期</td>';
			disweeks($j);
			echo '<td>總分</td><td>名次</td><td>備註</td></tr>';	
			for ($i=1;$i<=$classnum[$j];$i++)
				{
				distscore($j,$i);	
				}
			echo '</table>';
			}
		echo '<form id="outxls" name="outxls" method="post" action="outxls.php"><input type="hidden" name="curryear" value="'.$curryear.'">
		<input type="hidden" name="currseme" value="'.$currseme.'"><input type="hidden" name="weeksub" value="'.$_REQUEST["selweek"].'"><input type="hidden" name="classnum[1]" value="'.$classnum[1].'"><input type="hidden" name="classnum[2]" value="'.$classnum[2].'"><input type="hidden" name="classnum[3]" value="'.$classnum[3].'"><input type="hidden" name="filesub" value="'.$_REQUEST["selcleord"].'"><input type="submit" name="send"  value="輸出xls" /></form>';	
		}
	}
$mysqli->close();	
FootCode();
/*
function get_year_class_num($sel_year,$sel_seme,$key)
	{
	global $CONN;
	$sql_select = "select count(*) from school_class where year='$sel_year' and semester = '$sel_seme' and c_year='$key' and enable='1'";
	$recordSet=$CONN->Execute($sql_select)  or user_error("查詢失敗！<br>$sql_select",256);
	list($num)=$recordSet->FetchRow();
	if($num==0)$num="";
	return $num;
	}
*/
function datacor()
	{
	global $classnum,$mysqli;
	for ($i=1;$i<=3;$i++)
		{
		$stage=$i.'00';
		$sql_select = "select Mon,Tue,Wed,Thu,Fri,Sat,Sun from cleord  WHERE week='".$_REQUEST["selweek"]."' AND memo='日期' AND kind='".$_REQUEST["selcleord"]."' AND class='".$stage."'";
		if (!$record_set =$mysqli->query($sql_select))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
		if (list($dayo[1],$dayo[2],$dayo[3],$dayo[4],$dayo[5],$dayo[6],$dayo[7])=$record_set->fetch_array(MYSQLI_NUM))
		 	{
			for ($j=1;$j<=$classnum[$i];$j++)
				{
				$class=$i.chclass($j);
				$sql_select1 = "select prikey,Mon,Tue,Wed,Thu,Fri,Sat,Sun from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class='".$class."'";
				if (!$record_set1 =$mysqli->query($sql_select1))
					error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
				if (list($prikey,$dayn[1],$dayn[2],$dayn[3],$dayn[4],$dayn[5],$dayn[6],$dayn[7])=$record_set1->fetch_array(MYSQLI_NUM))
		 			{
					$tscore=0;
					for ($k=1;$k<=7;$k++)
						{
						if (($dayo[$k]==-100)&&($dayn[$k]!=0))
							{
							echo '第'.$_REQUEST["selweek"].'週'.$class.chselcleord($_REQUEST["selcleord"]).'多輸入成績，請選擇上方"輸入成績"後再執行"統計表"。';	
							return -1;	
							}
						if (($dayo[$k]!=-100)&&($dayn[$k]==0))
							{
							echo '第'.$_REQUEST["selweek"].'週'.$class.chselcleord($_REQUEST["selcleord"]).'少輸入成績，請選擇上方"輸入成績"後再執行"統計表"。';	
							return -1;	
							}
						$tscore=$tscore+$dayn[$k];		
						}
					$sql_select2 = "UPDATE cleord SET tscore='".$tscore."' WHERE prikey='".$prikey."'";
					if (!$record_set2 =$mysqli->query($sql_select2))
						error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
					}
				else
					{
					echo '第'.$_REQUEST["selweek"].'週'.$class.chselcleord($_REQUEST["selcleord"]).'成績未輸入，請選擇上方"輸入成績"後再執行"統計表"。';	
					return -1;	
					}	
				}
			}
		else
			{
			echo '第'.$_REQUEST["selweek"].'週'.chstage($i).chselcleord($_REQUEST["selcleord"]).'成績未輸入，請選擇上方"輸入成績"後再執行"統計表"。';	
			return -1;
			}	
		}
	for ($i=1;$i<=3;$i++)
		{
		$stagedn=$i."00";
		$stageup=($i+1)."00";
		$sql_select = "update cleord set rank='' WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class>".$stagedn." AND class<".$stageup;
		if (!$record_set =$mysqli->query($sql_select))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
		$sql_select = "select prikey,class,tscore from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class>".$stagedn." AND class<".$stageup." ORDER BY tscore DESC ";
		if (!$record_set =$mysqli->query($sql_select))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
		$istop=1;
		$scoreser=1;
		$topscore=0;//新增的20200509
		while (list($prikey,$class,$tscore)=$record_set->fetch_array(MYSQLI_NUM))
			{
			if ($topscore==$tscore)
				{$istop=1;}	
			if ($istop==1)
				{
				$topscore=$tscore;
				if (newhonorclass($prikey,$class)==1)
					{$scoreser--;}
				}
			if ($scoreser==3)
				{$spescore=$tscore;
				findspecial($spescore,$stagedn,$stageup);
				}
			if ($scoreser==12)
				{$nspescore=$tscore;}	
			if (($scoreser<=3)||($spescore==$tscore))
				{
				uprank($prikey,"特優");
				}
			else if (($scoreser<=12)||($nspescore==$tscore))
				{
				uprank($prikey,"優等");	
				}			
			$istop++;
			$scoreser++;	
			}
		$sql_select = "select prikey,class,tscore from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class>".$stagedn." AND class<".$stageup." AND rank!='榮譽班' ORDER BY tscore DESC ";
		if (!$record_set =$mysqli->query($sql_select))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
		$istop=1;
		$scoreser=1;
		while (list($prikey,$class,$tscore)=$record_set->fetch_array(MYSQLI_NUM))
			{
			if ($topscore==$tscore)
				{$istop=1;}	
			if ($istop==1)
				{
				$topscore=$tscore;
				if (newhonorclass($prikey,$class)==1)
					{$scoreser--;}
				}
			if ($scoreser==3)
				{$spescore=$tscore;
				findspecial($spescore,$stagedn,$stageup);
				}
			if ($scoreser==12)
				{$nspescore=$tscore;}
			//echo "優等最低分".$nspescore."分數".$tscore."序位".$scoreser."班級".$class."<br>";		
			if (($scoreser<=3)||($spescore==$tscore))
				{
				uprank($prikey,"特優");
				}
			else if (($scoreser<=12)||($nspescore==$tscore))
				{
				uprank($prikey,"優等");	
				}			
			$istop++;
			$scoreser++;	
			}	
		}
	return 1;	
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
		
function newhonorclass($prikey,$class)
	{
	global $mysqli;	
	if ($_REQUEST["selweek"]>=4)
		{
		if ((istopspecial(($_REQUEST["selweek"]-1),$class)==1)&&(istopspecial(($_REQUEST["selweek"]-2),$class)==1))
			{
			$sql_spec = "select rank from cleord  WHERE week='".($_REQUEST["selweek"]-1)."' AND kind='".$_REQUEST["selcleord"]."' AND class=".$class;
			if (!$recordspec =$mysqli->query($sql_spec))
				error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
			list($rank)=$recordspec->fetch_array(MYSQLI_NUM);
			if ($rank=='榮譽班')
				{$memo="";}
			else
				{$memo="新增";}			
			$sql_select = "UPDATE cleord SET rank='榮譽班',memo='".$memo."' where prikey='".$prikey."'";
			if (!$record_set =$mysqli->query($sql_select))
				error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
			return 1;	
			}
		}
	return -1;	
	}

function istopspecial($week,$class)
	{
	global $mysqli;
	$i=intval($class/100);
	$stagedn=$i."00";
	$stageup=($i+1)."00";	
	$sql_score = "select tscore from cleord  WHERE week='".$week."' AND kind='".$_REQUEST["selcleord"]."' AND class>".$stagedn." AND class<".$stageup." AND rank!='榮譽班' ORDER BY tscore DESC ";
	if (!$recordscore =$mysqli->query($sql_score))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
	list($topscore)=$recordscore->fetch_array(MYSQLI_NUM);	
	//echo "$sql_score"."##".$topscore."##";
	$sql_select = "select rank from cleord  WHERE week='".$week."' AND kind='".$_REQUEST["selcleord"]."' AND class='".$class."' AND tscore>=".$topscore."";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);		
	if (list($rank)=$record_set->fetch_array(MYSQLI_NUM))
		{return 1;}
	else
		{return -1;}	
	}

function findspecial($spescore,$stagedn,$stageup)
	{
	global $mysqli,$scoreser;
	$sql_select = "select class from cleord  WHERE week='".($_REQUEST["selweek"]-1)."' AND kind='".$_REQUEST["selcleord"]."' AND class>'".$stagedn."' AND class<'".$stageup."' AND rank='榮譽班'";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
	while (list($class)=$record_set->fetch_array(MYSQLI_NUM))
		{
		$sql_txt = "select tscore from cleord  WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class=".$class;
		if (!$recordtxt =$mysqli->query($sql_txt))
			error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
		list($tscore)=$recordtxt->fetch_array(MYSQLI_NUM);	
		if 	($tscore<=($spescore-2))
			{
			//echo "bb".$prikey."aa".$spescore."vv<br>";
			$sql_select1 = "UPDATE cleord SET memo='' WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class='".$class."'";
			if (!$record_set1 =$mysqli->query($sql_select1))
				error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
			}
		else
			{
			$sql_select1 = "UPDATE cleord SET rank='榮譽班' WHERE week='".$_REQUEST["selweek"]."' AND kind='".$_REQUEST["selcleord"]."' AND class='".$class."'";
			if (!$record_set1 =$mysqli->query($sql_select1))
				error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);		
			$scoreser--;
			}	
		}
	return 1;	
	}
	
function uprank($prikey,$ranktext)
	{
	global $mysqli;
	$sql_select = "select rank from cleord  WHERE prikey='".$prikey."'";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
	list($rank)=$record_set->fetch_array(MYSQLI_NUM);
	if ($rank=='榮譽班')
		{return -1; }
	$sql_select = "UPDATE cleord SET rank='".$ranktext."' WHERE prikey='".$prikey."'";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
	return 1;	
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
	global$mysqli;	
	$class=$j.chclass($i);
	echo '<tr><td>'.($j+6).chclass($i).'</td>';
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
		
?>

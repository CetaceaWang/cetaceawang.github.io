<?php
include "config.php";
SelectMenu("grade.php","評比表");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$current_year = 0 ; $current_semester = 0 ;$classnum[1]=0;$classnum[2]=0;$classnum[3]=0;
current_year_semester();
$curryear=$current_year;
$currseme=$current_semester;
$weekarr=get_week_array();
?>
<form id="forminput" name="forminput" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <p>
    <select name="selweek" id="selweek" onchange="forminput.submit();" >
<?php
for ($i=1;$i<count($weekarr);$i++)
	{
	echo '<option value="'.$i.'" ';
	if (isset($_REQUEST["selweek"]) && $_REQUEST["selweek"]==$i)
		{echo 'selected="selected"';}
	echo '>第'.$i.'週：'.DtoCh($weekarr[$i]).'(日)~'.DtoCh(GetdayAdd($weekarr[$i],6)).'(六)</option>';
	}		
?>
   </select>
  </form>    
</p>
<p>
<?php



if (isset($_REQUEST["selweek"]) && $_REQUEST["selweek"]!="")
	{
	 if (sdatacor()!=1)
		{echo '請先執行第'.$_REQUEST["selweek"].'週統計表';}
	else
		{
		echo '<table border="1">';
		echo '<tr><td >秩序</td><td >整潔</td></tr><tr>';	
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
		$out_text='<form id="outdoc" name="outdoc" method="post" action="outdoc.php"><input type="hidden" name="curryear" value="'.$curryear.'">
		<input type="hidden" name="currseme" value="'.$currseme.'"><input type="hidden" name="weeksub" value="'.$_REQUEST["selweek"].'"><input type="hidden" name="classnum[1]" value="'.$classnum[1].'"><input type="hidden" name="classnum[2]" value="'.$classnum[2].'"><input type="hidden" name="classnum[3]" value="'.$classnum[3].'">';
		//<input type="hidden" name="filesub" value="'.$_REQUEST["selcleord"].'"> 不知道幹嘛的
		$out_text.='<input type="submit" name="send"  value="輸出doc" /></form><form id="outtxt" name="outtxt" method="post" action="outtxt.php"><input type="hidden" name="curryear" value="'.$curryear.'">
		<input type="hidden" name="currseme" value="'.$currseme.'"><input type="hidden" name="weeksub" value="'.$_REQUEST["selweek"].'"><input type="hidden" name="classnum[1]" value="'.$classnum[1].'"><input type="hidden" name="classnum[2]" value="'.$classnum[2].'"><input type="hidden" name="classnum[3]" value="'.$classnum[3].'">';
		//<input type="hidden" name="filesub" value="'.$_REQUEST["selcleord"].'">
		$out_text.='<input type="submit" name="send"  value="輸出校務公告" /></form>';
		echo $out_text;
		}
	}
FootCode();
	
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

<?php
include "config.php";
SelectMenu("allstat.php","期末統計表");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$current_year = 0 ; $current_semester = 0 ;$classnum[1]=0;$classnum[2]=0;$classnum[3]=0;
current_year_semester();
$curryear=$current_year;
$currseme=$current_semester;
$weekarr=get_week_array();
?>
</p>
<p>
<form id="forminput" name="forminput" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
 <select name="selcleord" id="selcleord" onchange="forminput.submit();" >
<?php
if (!isset($_REQUEST["selcleord"]))
	{$_REQUEST["selcleord"]=1;}
echo '<option value="1" ';
if ($_REQUEST["selcleord"]==1)
	{echo 'selected="selected"';}
echo '>整潔</option>';
echo '<option value="2" ';
if ($_REQUEST["selcleord"]==2)
	{echo 'selected="selected"';}
echo '>秩序</option>';
?>
</select></form>
<?php
echo '<table border="1">';
for ($i=1;$i<=3;$i++)
	{
	echo '<tr><td>班級/週別</td>';	
	for ($j=2;$j<count($weekarr);$j++)
		{echo '<td>'.$j.'</td>';}
	echo '</tr>';
	for ($k=1;$k<=$classnum[$i];$k++)
		{
		echo '<tr><td>'.($i+6).chclass($k).'</td>';	
		for ($j=2;$j<count($weekarr);$j++)
			{echo '<td>'.findrank($i,$j,$k).'</td>';}
		echo '</tr>';	
		}
	echo '<tr>';	
	for ($j=1;$j<count($weekarr);$j++)
		{echo '<td></td>';}
	echo '</tr>';		
	}
$form_text='</table><form id="outdoc" name="outdoc" method="post" action="outalldoc.php"><input type="hidden" name="curryear" value="'.$curryear.'">
<input type="hidden" name="currseme" value="'.$currseme.'">';
//<input type="hidden" name="weeksub" value="'.$_REQUEST["selweek"].'">
$form_text.='<input type="hidden" name="classnum[1]" value="'.$classnum[1].'"><input type="hidden" name="classnum[2]" value="'.$classnum[2].'"><input type="hidden" name="classnum[3]" value="'.$classnum[3].'"><input type="hidden" name="filesub" value="'.$_REQUEST["selcleord"].'"><input type="submit" name="send"  value="輸出doc" /></form>';	
echo $form_text;		
FootCode();
	
function findrank($i,$j,$k)
	{
	global $mysqli;
	$class=$i.chclass($k);
	$sql_select = "select rank from cleord  WHERE week='".$j."' AND kind='".$_REQUEST["selcleord"]."' AND class=".$class;
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	//echo "aa".$sql_select."aa<br>";
	if (list($rank)=$record_set->fetch_array(MYSQLI_NUM))
		{
		return $rank;	
		}
	return "";	
	}
	
function chclass($num)
	{
	$numt=0+$num;
	if ($numt<10	)
		{return "0".$numt;}
	else
		{return "".$numt;}	
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

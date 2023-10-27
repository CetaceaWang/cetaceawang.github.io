<?php
include "config.php";
SelectMenu("setone.php","期初設定");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$echo_text="";
$echo_text_end="";
if (isset($_REQUEST["Confirm"]) && $_REQUEST["Confirm"]=="設定確認")
	set_year_semester();
if (isset($_REQUEST["send"]) && $_REQUEST["send"]=="確定清空資料")
	{
	$sql_select = "TRUNCATE TABLE cleord";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	$echo_text_end="<font color='#D26900'>清空資料完成。</font>";	
	}
?>
<form id="year_semester" name="year_semester" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table >
	<tr>
		<td  align="center">學年度</td>
		<td  align="center">學期</td>
		<td  align="center">開學日期</td>
		<td  align="center">學期結束日期</td>
		<td  align="center">七年級班級數</td>
		<td  align="center">八年級班級數</td>
		<td  align="center">九年級班級數</td>
		<td  align="center">功能按鈕</td>
	</tr>
	<tr>
		<td  align="center"><?php input_set_value("Year","108",4); ?></td>
		<td  align="center"><?php input_set_value("Semester","2",2); ?></td>
		<td  align="center"><?php input_set_value("StartDay","2020-02-25",11); ?></td>
		<td  align="center"><?php input_set_value("EndDay","2020-07-14",11); ?></td>
		<td  align="center"><?php input_set_value("ClasssNumber1","18",3); ?></td>
		<td  align="center"><?php input_set_value("ClasssNumber2","18",3); ?></td>
		<td  align="center"><?php input_set_value("ClasssNumber3","19",3); ?></td>
		<td  align="center"><input type="submit" value="設定確認" name="Confirm"><input type="reset" value="清除" name="Clear"></td>
	</tr>
</table>
</form>
<br>
<?php
echo  $echo_text;
?>
<hr>
<font color="#FF00CC">★[確定清空資料]會清空所有資料，使用前請確認需要的資料均已下載，學期初必須使用一次。</font><form id="forminput" name="forminput" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="submit" name="send"  value="確定清空資料" /></form>
<br>
<?php
echo $echo_text_end;
FootCode();
//'".date( "Y-m-d", strtotime( $_REQUEST["StartDay"] ))."' 
function set_year_semester()
	{
	global $echo_text,$mysqli;
	$where_sql="where year='".$_REQUEST["Year"]."' and semester='".$_REQUEST["Semester"]."'";
	$sql="select * from year_semester_class ".$where_sql;
	if (!$record_set =$mysqli->query($sql))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	if  (list($id,$year,$semester,$start_day,$end_day,$class_number_1,$class_number_2,$class_number_3)=$record_set->fetch_array(MYSQLI_NUM))
		{
		$sql1="update year_semester_class set start_day='".date( "Y-m-d", strtotime( $_REQUEST["StartDay"] ))."' 
		,end_day='".date( "Y-m-d", strtotime( $_REQUEST["EndDay"] ))."' ,class_number_1='".$_REQUEST["ClasssNumber1"]."'
		,class_number_2='".$_REQUEST["ClasssNumber2"]."',class_number_3='".$_REQUEST["ClasssNumber3"]."' ".$where_sql;
		}
	else
		{
		$sql1="INSERT INTO year_semester_class (year, semester, start_day, end_day, class_number_1, class_number_2,class_number_3) 
		VALUES ('".$_REQUEST["Year"]."','".$_REQUEST["Semester"]."','".date( "Y-m-d", strtotime( $_REQUEST["StartDay"] ))."'
		, '".date( "Y-m-d", strtotime( $_REQUEST["EndDay"] ))."', '".$_REQUEST["ClasssNumber1"]."', '".$_REQUEST["ClasssNumber2"]."'
		, '".$_REQUEST["ClasssNumber3"]."')";
		}
	if (!$record_set =$mysqli->query($sql1))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);	
	if (!$record_set =$mysqli->query($sql))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);			
	list($id,$year,$semester,$_REQUEST["StartDay"],$_REQUEST["EndDay"],$_REQUEST["ClasssNumber1"],$_REQUEST["ClasssNumber2"]
	,$_REQUEST["ClasssNumber3"])=$record_set->fetch_array(MYSQLI_NUM);
	$echo_text="<font color='#D26900'>設定完成，請檢查每個欄位是否正確</font>";
	}
function input_set_value($request_name,$prompt_string,$size)
	{
		if (isset($_REQUEST[$request_name]))
			echo '<input type="text" style="text-align:center" name="'.$request_name.'" size="'.$size.'" value="'.$_REQUEST[$request_name].'">';
		else
			echo '<input  style="color:#E1E100;text-align:center" type="text" name="'.$request_name.'" size="'.$size.'" value="'.$prompt_string.'">';	
	}

?>

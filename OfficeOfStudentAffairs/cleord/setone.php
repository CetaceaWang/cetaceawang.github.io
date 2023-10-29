<?php
include "config.php";
SelectMenu("setone.php","期初設定");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$RemoteUrl="https://cingyue.lionfree.net/OfficeOfStudentAffairs/cleord/";
$echo_text="";
$echo_text_end="";
$BackupText="";
if (isset($_REQUEST["Confirm"]) && $_REQUEST["Confirm"]=="設定確認")
	set_year_semester();
if (isset($_REQUEST["send"]) && $_REQUEST["send"]=="清空資料")
	{
	$sql_select = "TRUNCATE TABLE cleord";
	if (!$record_set =$mysqli->query($sql_select))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	$echo_text_end="<font color='#D26900'>清空資料完成。</font>";	
	}
if (isset($_REQUEST["backup"]) && $_REQUEST["backup"]=="備份")
	BackupToRemote($RemoteUrl."RemoteBackupServer.php");
if (isset($_REQUEST["restore"]) && $_REQUEST["restore"]=="回存")
	RestoreFromRemote($RemoteUrl."cleord.txt");	
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
<font color="#FF00CC">★[清空資料]會清空所有資料，使用前務必使用[備份]並確認需要的資料均已下載，學期初必須使用一次。</font>
<form id="forminput" name="forminput" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="submit" name="send" style="color:white; background-color: #f44336;" value="清空資料" /></form>
<br>
<?php
echo $echo_text_end;
?>
<hr>
<font color="#FF00CC">★[回存]會清空所有資料，使用前務必使用[備份]。</font><form id="formbackup" name="formbackup" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="submit" name="backup"  value="備份" />
<input type="submit" name="restore" style="color:white; background-color: #f44336;" value="回存" /></form>
<br>
<?php
echo $BackupText;
FootCode();
//'".date( "Y-m-d", strtotime( $_REQUEST["StartDay"] ))."' 
function BackupToRemote($UploadUrl){
	$FileContents=BackupFile("cleord.sql");
	UploadFile($FileContents,$UploadUrl);
}
function RestoreFromRemote($DownloadUrl){
	global $BackupText,$mysqli;
	BackupFile("cleord.old");
    $contents = @file_get_contents($DownloadUrl);
    if(!$contents){
		$BackupText .= Message("無法讀取遠端檔案：".$DownloadUrl,"red");
		return;
	}
	if (!$record_set =$mysqli->multi_query($contents))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	else
		$BackupText .= Message("回存成功");	
}
function BackupFile($Name){
	global $BackupText;
	$FileContents=ExportSql("cleord").ExportSql("year_semester_class");
	$contents = @file_put_contents($Name, $FileContents);
    if($contents)
		$BackupText .= Message("檔案寫入成功 ".$Name);
	else
		$BackupText .= Message("無法寫入 ".$Name,"red");
	return 	$FileContents;
}
function UploadFile($Contents,$UploadUrl){
	global $BackupText;
	if (!CurlExist())
		{
			$BackupText .= Message("curl_version不存在","red");
			return;
		}
	$post = array('import'=>$Contents,'password4' => 'ef1e539726fbade3b40a0ceab8d4a4af','save' => 'Do');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$UploadUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = @curl_exec($ch); 	
	curl_close ($ch);
	if($result)
		$BackupText .= Message("備份上傳成功");
	else
		$BackupText .= Message("備份上傳失敗","red");
}
function Message($Text,$Color="blue"){
	return '<font color="'.$Color.'">'.$Text.'</font>'."<BR>";
}
function ExportSql($table)
{
	global $mysqli;
	$sql="show create table `$table`";
	if (!$record_set =$mysqli->query($sql))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	$q2 = $record_set->fetch_array(MYSQLI_BOTH);		
	$mysql = $q2['Create Table'] . ";\r\n";
	$q3="select * from `$table`";
	if (!$record_set3 =$mysqli->query($q3))
		error_echo ($_SERVER['PHP_SELF'].__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
	while ($data = $record_set3->fetch_assoc()) {
		$keys = array_keys($data);
		$keys = array_map('addslashes', $keys);
		$keys = join('`,`', $keys);
		$keys = "`" . $keys . "`";
		$vals = array_values($data);
		$vals = array_map('addslashes', $vals);
		$vals = join("','", $vals);
		$vals = "'" . $vals . "'";
		$mysql .= "insert into `$table`($keys) values($vals);\r\n";
	}
	return "DROP TABLE IF EXISTS $table;\r\n".$mysql;
}
function CurlExist(){
    return function_exists('curl_version');
}
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

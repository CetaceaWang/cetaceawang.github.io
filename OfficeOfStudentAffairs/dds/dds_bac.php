<?php
include "config.php";
//SelectMenu("dds6.php","備份、回存");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$filename="ddsbac.csv";
$filedata="\xEF\xBB\xBF"; // UTF-8 BOM
$filedata.="\"導師班級\",\"任課教師姓名\",\"日期\",\"時數\",\"備註\"\r\n";
dds_bac();
header("Content-disposition: attachment; filename=$filename");
header("Content-type: text/x-csv");
header("Pragma: no-cache");
header("Expires: 0");
echo $filedata;
exit; //程式無需更新畫面，就此停止
function dds_bac()
	{
	global $CONN,$filedata,$mysqli;
	$sql_select = "SELECT * FROM dds ";
	//$recordSet=$CONN->Execute($sql_select) or user_error("查詢失敗！<br>$sql_select",256);
	if (!$record_set =$mysqli->query($sql_select))
	{error_echo ("dds6.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);}
	//while (list($O_NAME,$D_NAME,$D_NUM,$D_DATE,$D_BACKUP,$D_ROWID)=$recordSet->FetchRow())
	while (list($O_NAME,$D_NAME,$D_NUM,$D_DATE,$D_BACKUP,$D_ROWID)=$record_set->fetch_array(MYSQLI_NUM))
		{
		$filedata.="\"$O_NAME\",\"$D_NAME\",\"$D_DATE\",\"$D_NUM\",\"$D_BACKUP\"\r\n";
		} 	
	}
?>
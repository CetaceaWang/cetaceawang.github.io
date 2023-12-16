<?php
include "config.php";
SelectMenu("dds5.php","所有代導師查詢");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$main=
'<table border="1">  
  <tr bgcolor="#00CCFF">
  <form method="POST" action="ddscsv.php"><td><p><input type="submit" value="下載csv" name="B1"></p></td></form>
  <form method="POST" action="ExportToXls.php"><td><p><input type="submit" value="匯出班級xls" name="B2"></p></td></form>
   </tr>
    ';
$main.=
	'</table><table border="1"><tr bgcolor="#FF99FF">
	  <td><p><font size="4"><b>任課教師姓名</b></font></p></td>
	  <td><p><b><font size="4">時數統計</font></b></p></td>
	  <td><p><b><font size="4">導師商數</font></b></p></td>
	  </tr>	';
dds_numall();
$main.="</table>";	
echo $main;
FootCode();
function dds_numall()
	{
	global $CONN,$main,$mysqli;
	$i=0;
	$sql_select = "SELECT D_NAME FROM dds ";
	if (!$record_set =$mysqli->query($sql_select))
	{error_echo ("dds5.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);}
	while (list($D_NAME[$i])=$record_set->fetch_array(MYSQLI_NUM))
		{
		for ($k=0;$k<$i;$k++)
			{
			if ($D_NAME[$k]==$D_NAME[$i])
				{
				continue 2;
				}
			}
		$sql_sel1 = "SELECT SUM(D_NUM) FROM dds where D_NAME='".quotemeta($D_NAME[$i])."'";
		if (!$record_set1 =$mysqli->query($sql_sel1))
		{error_echo ("dds5.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);}
		list($D_TOT[$i])=$record_set1->fetch_array(MYSQLI_NUM);
		$sql_sel2 = "SELECT D_NUM FROM dds where D_NAME='".quotemeta($D_NAME[$i])."' AND D_NUM > 34";
		if (!$record_set2 =$mysqli->query($sql_sel2))
		{error_echo ("dds5.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);}
		$D_DNU[$i]=0;
		while (list($D_TMP)=$record_set2->fetch_array(MYSQLI_NUM))
			{
			$D_DNU[$i]+=0.025*floor($D_TMP/35);
			}
		$i++;	
		}
	for ($j=0;$j<$i;$j++)
		{
		for ($k=$j+1;$k<$i;$k++)
			{
			if ($D_TOT[$k]>$D_TOT[$j])
				{
				list($D_NAME[$k], $D_NAME[$j]) = array($D_NAME[$j], $D_NAME[$k]);
				list($D_TOT[$k], $D_TOT[$j]) = array($D_TOT[$j], $D_TOT[$k]);
				list($D_DNU[$k], $D_DNU[$j]) = array($D_DNU[$j], $D_DNU[$k]);
				}
			}
		}
	for ($k=0;$k<$i;$k++)
		{		
		$main.=
		'<tr bgcolor="#FF99FF">
		  <td><p><font size="4"><b>'.$D_NAME[$k].'</b></font></p></td>
		  <td><p><b><font size="4">'.$D_TOT[$k].'</font></b></p></td>
		  <td><p><b><font size="4">'.$D_DNU[$k].'</font></b></p></td>
		  </tr>	';
		 } 	
	}
?>
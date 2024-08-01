<?php
include "config.php";
SelectMenu("dds6.php","備份、回存");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
// phpinfo(); 
if(isset($_REQUEST["B2"]) && $_REQUEST["B2"]=="上傳備份資料")
//if ($_REQUEST["B2"]=="上傳備份資料")
	{
	inp_bac();
	}
	
$main=
'
<table border="1"> 
  <tr >
  <td bgcolor="#00CCFF"><form method="POST" action="dds_bac.php"> <p align="center">★★★<input type="submit" value="下載備份資料" name="B1">★★★</p></form></td></tr>
  <tr><td bgcolor="#CC00FF">
  <form enctype="multipart/form-data" method="POST" action="'.$_SERVER['PHP_SELF'].'">
  <input type="file" name="import" size="20"><input type="submit" value="上傳備份資料" name="B2"></form></td> </tr></table>
    ';
echo $main;
?>
<font color="#FF00CC">★請注意[下載備份資料]鈕不會破壞原有資料，[上傳備份資料]會完全刪除原有資料</font><br />
★每學年要用[上傳備份資料]此功能，查詢時才會出現代導師姓名。<br />
★每學年要製作upnew.csv，範例檔在目錄中，流程如下：<br />
　一、先製作SPVTXT.xls，以欣河排課2007版為例。<br />
　　　列印作業->排課資料列印->[開始班級]選第一班->[結束班級]選最後一班->excel檔->列印->離開。<br />
　二、後製作upnew.csv，以office2007為例。<br />
　　　1.[SPVTXT.xls]跟[製作代導師上傳檔案.xls]在同一目錄後開啟[製作代導師上傳檔案.xls]->GO。<br />
　　　2.離開excel->否。<br />
　三、開啟 upnew.csv，刪除不要的列，如外師、技藝教師等，然後存檔。<br />
　四、使用本頁->瀏覽->選擇upnew.csv->[上傳備份資料]。<br />
<br />
<font color="#0464F9">工具程式：</font>
<a href="製作代導師上傳檔案.xlsm">製作代導師上傳檔案</a>、
<a href="代導師時數暨商數統計表.xlsm">代導師時數暨商數統計表</a>
<br />
<?php
FootCode();
function inp_bac()
	{
	global $CONN,$mysqli;//新增$mysqli
	//setlocale(LC_ALL, 'en_US.UTF-8');
	$csv_message="還沒選任何檔案";
	if ($_FILES['import']['size']>0 && $_FILES['import']['name'] != "") 
		{
		//讀出csv內容
		$sql_select = "TRUNCATE TABLE dds";
		if (!$record_set =$mysqli->query($sql_select))
		{error_echo ("dds6.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);}
//	    $CONN->Execute($sql_select) or user_error("查詢失敗！<br>$sql_select",256);
		//$items_arr=array();
		$sql="";
		$row=1;
		/*$fp=fopen($_FILES['import']['tmp_name'],"r");
		while(($data=fgets($fp))!== FALSE)
		{
			if ($row>1)
			{
				$sql_temp=explode('","',$data);
				$sql_temp[0]=substr($sql_temp[0],1,5);
				$sql_temp[4]=substr($sql_temp[4],0,-3);
				if ($sql_temp[0]=="") //空白不匯入
					continue;
				$sql="INSERT INTO dds(o_name,d_name,d_date,d_num,d_backup) 
				VALUES ('$sql_temp[0]','$sql_temp[1]','$sql_temp[2]','$sql_temp[3]','$sql_temp[4]')";
				if (!$record_set =$mysqli->query($sql))
					error_echo ("dds6.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
			}
			$row++;	
		}
		fclose($fp);*/
		if (($handle = fopen($_FILES['import']['tmp_name'],"r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
			{
				if ($row>1)
				{
					//echo $data[0].":".$data[1]."<br>";
					if ($data[0]=="") //空白不匯入
						continue;
					$sql="INSERT INTO dds(o_name,d_name,d_date,d_num,d_backup) 
					VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]')";
					if (!$record_set =$mysqli->query($sql))
						error_echo ("dds6.php:".__LINE__."-查詢失敗: (" . $mysqli->errno . ") " . $mysqli->error);
				}
			$row++;
			}
		fclose($handle);
		}
		if ($row>2)
			$csv_message='<li>'.date('Y/m/d h:i:s')." 已自[".$_FILES['import']['name']."]匯入CSV資料</li>";
		else 
			$csv_message='<li>'.date('Y/m/d h:i:s')." [".$_FILES['import']['name']."]無紀錄可供匯入！</li>";
		echo $csv_message;
		}
	}	
?>
</p>

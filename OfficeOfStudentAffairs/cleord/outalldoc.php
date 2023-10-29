<?php
include "config.php";
//SelectMenu("allstat.php","期末統計表");
$mysqli = new mysqli($DB_SERVER,$DB_LOGIN,$DB_PASSWORD,$DB_NAME);
if ($mysqli->connect_errno) {echo "連接資料庫失敗: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;exit();}
if (!$mysqli->set_charset("utf8")) {printf("資料庫設定無法設定utf8編碼: %s\r\n", $mysqli->error);exit();}
$current_year = 0 ; $current_semester = 0 ;$classnum[1]=0;$classnum[2]=0;$classnum[3]=0;
current_year_semester();
$curryear=$current_year;
$currseme=$current_semester;
$weekarr=get_week_array();
$_REQUEST["selcleord"]=$_REQUEST["filesub"];
$filename=$curryear.'學年度第'.$currseme.'學期生活教育競賽'.chselcleord($_REQUEST["selcleord"]).'評分統計總表.doc';
//$_REQUEST["selweek"]=$_REQUEST["weeksub"];
header("Content-type: application/msword; charset=UTF-8"); 
header("Content-Disposition:filename=$filename");  
header("Pragma: no-cache");
header("Expires: 0");
?>
<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"
xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 14">
<meta name=Originator content="Microsoft Word 14">
<style>
<!--
 /* Font Definitions */
 @font-face
	{font-family:新細明體;
	panose-1:2 2 5 0 0 0 0 0 0 0;
	mso-font-alt:PMingLiU;
	mso-font-charset:136;
	mso-generic-font-family:roman;
	mso-font-pitch:variable;
	mso-font-signature:-1610611969 684719354 22 0 1048577 0;}
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-parent:"";
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"新細明體","serif";
	mso-bidi-font-family:新細明體;}
p
	{mso-style-priority:99;
	mso-margin-top-alt:auto;
	margin-right:0cm;
	mso-margin-bottom-alt:auto;
	margin-left:0cm;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"新細明體","serif";
	mso-bidi-font-family:新細明體;}
span.GramE
	{mso-style-name:"";
	mso-gram-e:yes;}
.MsoChpDefault
	{mso-style-type:export-only;
	mso-default-props:yes;
	mso-fareast-font-family:新細明體;}
@page WordSection1
	{size:595.3pt 841.9pt;
	margin:1.0cm 1.0cm 1.0cm 1.0cm;
	mso-header-margin:42.55pt;
	mso-footer-margin:49.6pt;
	mso-paper-source:0;}
div.WordSection1
	{page:WordSection1;}
-->
</style>
</head>
<body lang=ZH-TW style='tab-interval:24.0pt'>
<?php
$rownum=0;
echo "<div class=WordSection1>
	<div align=center>
	<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
 style='border-collapse:collapse;border:none;mso-border-alt:solid black .75pt;
 mso-yfti-tbllook:1184;mso-padding-alt:0cm 0cm 0cm 0cm'>
 <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'>
  <td colspan=19 style='border:none;border-bottom:solid black 1.0pt;mso-border-bottom-alt:
  solid black .75pt;padding:0cm 0cm 0cm 0cm'>
  <p class=MsoNormal align=center style='text-align:center'><span lang=EN-US>".$curryear."</span>學年度第<span
  lang=EN-US>".$currseme."</span>學期生活教育競賽".chselcleord($_REQUEST["selcleord"])."評分統計總表</p>
  </td>
 </tr>";
//echo '<p align="center">'.$curryear.'學年度第'.$currseme.'學期生活教育競賽'.chselcleord($_REQUEST["selcleord"]).'評分統計總表<p>';
for ($i=1;$i<=3;$i++)
	{
	$rownum++;	
	//echo '<table border="1" cellspacing="0" cellpadding="0" >';	
	//echo '<tr><td class="cur">班級/週別</td>';
	echo "<tr style='mso-yfti-irow:".$rownum."'><td width=36 style='width:26.8pt;border:solid black 1.0pt;border-top:none;
  mso-border-top-alt:solid black .75pt;mso-border-alt:solid black .75pt;
  padding:0cm 0cm 0cm 0cm'>
  <p class=MsoNormal align=center style='text-align:center'><span class=GramE><span
  style='font-size:5.0pt'>週</span></span><span style='font-size:5.0pt'>別</span><span
  lang=EN-US style='font-size:5.0pt;font-family:Wingdings;mso-ascii-font-family:
  新細明體;mso-hansi-font-family:新細明體;mso-char-type:symbol;mso-symbol-font-family:
  Wingdings'><span style='mso-char-type:symbol;mso-symbol-font-family:Wingdings'>&agrave;</span></span><span
  lang=EN-US style='font-size:5.0pt'><br>
  </span><span style='font-size:5.0pt'>班級</span><span lang=EN-US
  style='font-size:5.0pt;font-family:Wingdings;mso-ascii-font-family:新細明體;
  mso-hansi-font-family:新細明體;mso-char-type:symbol;mso-symbol-font-family:Wingdings'><span
  style='mso-char-type:symbol;mso-symbol-font-family:Wingdings'>&acirc;</span></span><span
  lang=EN-US style='font-size:5.0pt'><o:p></o:p></span></p>
  </td>";		
	for ($j=2;$j<count($weekarr);$j++)
		{//echo '<td >'.$j.'</td>';
		echo "<td width=36 style='width:26.8pt;border-top:none;border-left:none;border-bottom:
  solid black 1.0pt;border-right:solid black 1.0pt;mso-border-top-alt:solid black .75pt;
  mso-border-left-alt:solid black .75pt;mso-border-alt:solid black .75pt;
  padding:0cm 0cm 0cm 0cm'>
  <p class=MsoNormal align=center style='text-align:center'><span
  style='font-size:7.0pt'>".$j."<span lang=EN-US><o:p></o:p></span></span></p>
  </td>";
		//echo '<td>榮譽班</td>';
		}
	echo '</tr>';	
	for ($k=1;$k<=$classnum[$i];$k++)
		{
		//echo '<tr><td >'.$i.chclass($k).'</td>';
		$rownum++;
		echo "<tr style='mso-yfti-irow:".$rownum."'><td style='border:solid black 1.0pt;border-top:none;mso-border-top-alt:solid black .75pt;
  mso-border-alt:solid black .75pt;padding:0cm 0cm 0cm 0cm'>
  <p class=MsoNormal align=center style='text-align:center'><span lang=EN-US
  style='font-size:7.0pt'>".($i+6).chclass($k)."<o:p></o:p></span></p></td>";	
		for ($j=2;$j<count($weekarr);$j++)
			{
			//echo '<td >'.findrank($i,$j,$k).'</td>';
			echo "<td style='border-top:none;border-left:none;border-bottom:solid black 1.0pt;
  border-right:solid black 1.0pt;mso-border-top-alt:solid black .75pt;
  mso-border-left-alt:solid black .75pt;mso-border-alt:solid black .75pt;
  padding:0cm 0cm 0cm 0cm'>
  <p class=MsoNormal align=center style='text-align:center'><span
  style='font-size:7.0pt'>".findrank($i,$j,$k)."<span lang=EN-US><o:p></o:p></span></span></p></td>";
			}
		echo '</tr>';	
		}
	//echo '<tr>';
		if ($i!=3)
		{
		$rownum++;	
		echo "<tr style='mso-yfti-irow:".$rownum."'>";	
		for ($j=1;$j<count($weekarr);$j++)
			{
			//echo '<td ></td>';
			echo "<td style='border:none;border-bottom:solid black 1.0pt;mso-border-top-alt:
  solid black .75pt;mso-border-top-alt:solid black .75pt;mso-border-bottom-alt:
  solid black .75pt;padding:0cm 0cm 0cm 0cm'>
  <p class=MsoNormal align=center style='text-align:center'><span lang=EN-US
  style='font-size:2.0pt'><o:p>&nbsp;</o:p></span></p>
  </td>";
			}
		echo '</tr>';
		}
	}
echo '</table></div></div></body></html>';
		
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
	

function chselcleord($num)
	{
	if ($num==1)
		{return "整潔";}
	if ($num==2)
		{return "秩序";}
	return "整潔秩序找不到";	
	}
	

?>

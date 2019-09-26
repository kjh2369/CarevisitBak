<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=SMS.xls" );

	$IsExcel = true;
	$style = 'border:0.5pt solid BLACK; background-color:#EAEAEA;';

	$year = $_POST['year'];
	$month = $_POST['month'];

?>
<div align="center" style="font-size:15pt; font-weight:bold;"><?=$year;?>년 <?=$month;?>월 SMS이용현황</div>
<table border="1">
	<tr>
		<th class="head">No</th>
		<th class="head">기관명</th>
		<th class="head">기관기호</th>
		<th class="head">CMS</th>
		<th class="head">CMS회사</th>
		<th class="head">대표자</th>
		<th class="head">사용</br>건수</th>
		<th class="head">기본</br>금액</th>
		<th class="head">추가</br>건수</th>
		<th class="head">추가</br>금액</th>
		<th class="head">합계</br>금액</th>
		<th class="head last">비고</th>
	</tr><?
	include_once('./center_SMS_search.php');?>
</table>

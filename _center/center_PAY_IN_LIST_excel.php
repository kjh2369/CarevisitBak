<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = '기관계약현황';

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr($title).".xls" );

	$IsExcel = true;
	$style = 'border:0.5pt solid BLACK; background-color:#EAEAEA;';?>

	<table border="1">
		<tr>
			<th style="<?=$style;?> width:50px;">No</th>
			<th style="<?=$style;?> width:100px;">기관기호</th>
			<th style="<?=$style;?> width:200px;">기관명</th>
			<th style="<?=$style;?> width:80px;">청구일자</th>
			<th style="<?=$style;?> width:80px;">입금일자</th>
			<th style="<?=$style;?> width:70px;">시간</th>
			<th style="<?=$style;?> width:60px;">입금구분</th>
			<th style="<?=$style;?> width:80px;">입금금액</th>
			<th style="<?=$style;?> width:70px;">출금상태</th>
			<th style="<?=$style;?> width:150px;">출금은행</th>
			<th style="<?=$style;?> width:150px;">입금은행</th>
			<th style="<?=$style;?> width:200px;">비고</th>
		</tr><?
		$style = 'border:0.5pt solid BLACK;';
		include_once('./center_PAY_IN_LIST_search.php');?>
	</table><?

	include_once('../inc/_db_close.php');
?>
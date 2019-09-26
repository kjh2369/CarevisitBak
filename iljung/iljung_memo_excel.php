<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_page_list.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$name	= $_POST['name'];
	$fromDt	= $_POST['from'];
	$toDt	= $_POST['to'];

	header( "Content-type: application/vnd.ms-excel;charset=UTF-8");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=carevisit_excel_".date('Ymd').".xls" );
?>
<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=UTF-8'>
<div style="font-weight:bold; font-size:20px; text-align:center;">메모관리</div>
<div style="font-size:13px; text-align:right;">조회기간 : <?=$myF->dateStyle($fromDt,'.');?> ~ <?=$myF->dateStyle($toDt,'.');?></div>
<table border="1">
	<tr style="height:30px;">
		<th style="width:50px; background-color:#EAEAEA;">No</th>
		<th style="width:100px; background-color:#EAEAEA;">작성일</th>
		<th style="width:100px; background-color:#EAEAEA;">수급자</th>
		<th style="width:300px; background-color:#EAEAEA;">작성내용</th>
		<th style="width:100px; background-color:#EAEAEA;">비고</th>
	</tr><?
	$sql = 'SELECT	jumin
			,		yymm
			,		seq
			,		m03_name AS name
			,		reg_dt
			,		memo
			,		svc_cd
			FROM	iljung_memo
			INNER	JOIN	m03sugupja
					ON		m03_ccode	= org_no
					AND		m03_mkind	= CASE WHEN svc_cd = \'S\' OR svc_cd = \'R\' THEN \'6\' ELSE svc_cd END
					AND		m03_jumin	= jumin
			WHERE	org_no	= \''.$orgNo.'\'';

	if ($year > 0){
		$sql .= ' AND yymm = \''.$year.$month.'\'';
	}

	if ($fromDt && $toDt){
		$sql .= ' AND reg_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'';
	}else if ($fromDt){
		$sql .= ' AND reg_dt >= \''.$fromDt.'\'';
	}else if ($toDt){
		$sql .= ' AND reg_dt <= \''.$toDt.'\'';
	}

	if ($name) $sql .= ' AND m03_name like \'%'.$name.'%\'';

	$sql .= '
			AND		del_flag= \'N\'
			ORDER	BY	reg_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td style="text-align:center;"><?=$no;?></td>
			<td style="text-align:center;"><?=$myF->dateStyle($row['reg_dt'],'.');?></td>
			<td style="text-align:left;"><?=$row['name'];?></td>
			<td style="text-align:left;"><?=$row['memo'];?></td>
			<td style="text-align:left;"></td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
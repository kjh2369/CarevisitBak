<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");


	$code	= $_REQUEST['code'];
	$kind	= $_REQUEST['kind'];
	$year	= $_REQUEST['year']  != '' ? $_REQUEST['year']  : date('Y', mktime());
	
	$sql = "select ifnull(max(cast(right(salary_yymm, 2) as unsigned)), 0)
			  from salary_basic
			 where org_no = '$code'";
	$max_month = $conn->get_data($sql);

	$month = $_REQUEST['month'] != '' ? $_REQUEST['month'] : ($max_month > 0 ? $max_month : date('m', mktime()));
	$month = ($month < 10 ? '0' : '').intval($month);

	$init_year = $myF->year();

	// 페이지 설정
	$page = (intval($_POST['page']) > 0 ? intval($_POST['page']) : 1);
	$col_cnt = 5;
	$col_width1 = 35;
	$col_width2 = 64;
	$col_width  = $col_width1 + $col_width2;

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );
	

	if($_GET['ex_gbn'] == '1'){
		include('salary_table_sub.php');
	}else if($_GET['ex_gbn'] == '2'){
		include('salary_table_sub2.php');
	}else {
		include('salary_table_sub3.php');
	}
	include('../inc/_db_close.php');
?>
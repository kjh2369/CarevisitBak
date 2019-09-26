<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');

	define('__EXCEL__', 'YES');

	//echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );

	$code = $_REQUEST['code'];
	$kind = $_REQUEST['kind'];
?>
<style>
	.head{
		background-color:#efefef;
	}
	.head_l{
		text-align:left;
		background-color:#efefef;
	}
	.head_r{
		text-align:right;
		background-color:#efefef;
	}
	.head_c{
		text-align:center;
		background-color:#efefef;
	}
</style>
<div class="title">미수금 입금처리</div>
<?
	include_once('../account/unpaid_list_sub.php');
	include_once('../inc/_db_close.php');
?>
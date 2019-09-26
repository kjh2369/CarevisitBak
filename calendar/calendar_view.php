<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code = $_POST['code'];
	$yymm = $_POST['yymm'];
	$seq  = $_POST['seq'];
	$no   = $_POST['no'];
	$mode = $_POST['mode'];
	$dt = $_POST['dt'];


	ob_start();


	@include_once('./calendar_view_'.$mode.'.php');


	$html = ob_get_contents();

	ob_end_clean();

	echo $html;


	include_once('../inc/_db_close.php');
?>
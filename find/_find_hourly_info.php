<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySalary.php');
	include_once('../inc/_ed.php');


	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);
	$svcID = $_POST['svcID'];
	$seq   = $_POST['seq'];
	$text  = $_POST['text'];

	if (empty($seq)) $seq = '0';
	if (!$text) $text = false;


	ob_start();

	//입사일자
	$sql = 'SELECT DATE_FORMAT(join_dt,\'%Y-%m\') AS dt
			  FROM mem_his
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			   AND join_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
			 ORDER BY join_dt
			 LIMIT 1';
	$joinYM = $conn->get_data($sql);

	/*********************************************************

		시급내역

	*********************************************************/
	$conn->query($mySalary->_queryNowHourly($code, $jumin, $svcID, $seq));
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$salaryHourIf = $mySalary->_setHourlyData($conn->select_row($i));
	}

	$conn->row_free();


	echo $mySalary->_getSalarySvc($svcID, $salaryHourIf, $text, $joinYM);

	$html = ob_get_contents();

	ob_clean();

	$html = $myF->_gabSplitHtml($html);

	echo $html;


	include_once('../inc/_db_close.php');
?>
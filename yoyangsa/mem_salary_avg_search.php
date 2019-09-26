<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_login.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code   = $_SESSION['userCenterCode'];
	$jumin  = $ed->de($_POST['jumin']);
	$year   = $_POST['year'];

	$sql = 'SELECT *
			  FROM salary_avg
			 WHERE org_no = \''.$code.'\'
			   AND year   = \''.$year.'\'
			   AND jumin  = \''.$jumin.'\'';

	$row = $conn->get_array($sql);

	$data = $row['h_mon'].chr(2)
		  . $row['h_pay'].chr(2)
		  . $row['h_amt'].chr(2)
		  . $row['s_mon'].chr(2)
		  . $row['s_amt'].chr(2)
		  . $row['b_cnt'].chr(2)
		  . $row['b_pay'].chr(2)
		  . $row['b_mon'];

	UnSet($row);

	echo $data;

	include_once("../inc/_db_close.php");
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	#echo $myF->getMtime()."\n";

	$sql = 'select extra500_1
			,      extra500_2
			,      extra500_3
			,      extra800_1
			,      extra800_2
			,      extra800_3
			  from mem_extra
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	$conn->fetch_type == 'assoc';
	$row = $conn->get_array($sql);

	#echo $myF->getMtime()."\n";

	#echo 'CBFD1='.$row['extra500_1']
	#	.'&CBKD1='.$row['extra500_2']
	#	.'&CBKD2='.$row['extra500_3']
	#	.'&CNWS1='.$row['extra800_1']
	#	.'&CNWS2='.$row['extra800_2']
	#	.'&CNWS3='.$row['extra800_3'];

	echo $row['extra500_1'].'/'.$row['extra500_2'].'/'.$row['extra500_3'].'/'.$row['extra800_1'].'/'.$row['extra800_2'].'/'.$row['extra800_3'];

	include_once('../inc/_db_close.php');
?>
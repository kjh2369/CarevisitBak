<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);

	$sql = 'select license_type as tp
			,      license_seq as seq
			,      license_gbn as nm
			,      license_no as no
			  from counsel_license
			 where org_no      = \''.$code.'\'
			   and license_ssn = \''.$jumin.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	$option = '<option value=\'\'>--</option>';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$option .= '<option value=\''.$row['tp'].'/'.$row['seq'].'\'>'.$row['nm'].'/'.$row['no'].'</option>';
	}

	$conn->row_free();

	if (empty($option)) $option = '<option value=\'\'>--</option>';

	echo $option;

	include_once('../inc/_db_close.php');
?>
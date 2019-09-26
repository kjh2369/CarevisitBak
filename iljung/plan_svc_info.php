<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$svcCd = $_POST['svcCd'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$sql = 'select svc_cd
			,      seq
			,      from_dt
			,      to_dt
			  from client_his_svc
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_cd = \''.$svcCd.'\'
			   and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
			   and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);?>
		<input id="svcCd_<?=$row['svc_cd'];?>" name="svcCd" type="hidden" value="<?=$row['svc_cd'];?>"><?//_planSugaLoad
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
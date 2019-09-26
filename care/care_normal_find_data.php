<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$seq	= $_POST['seq'];

	$sql = 'SELECT	jumin,name,postno,addr,addr_dtl,phone,mobile,grd_nm,grd_addr,grd_telno,marry_gbn,cohabit_gbn,edu_gbn,rel_gbn,longcare_lvl,longcare_gbn
			FROM	care_client_normal
			WHERE	org_no		= \''.$code.'\'
			AND		normal_sr	= \''.$sr.'\'
			AND		normal_seq	= \''.$seq.'\'';

	$row = $conn->get_array($sql);

	$data .= 'seq='		.$seq;
	$data .= '&jumin='		.$row['jumin'];
	$data .= '&name='		.$row['name'];
	$data .= '&postno='		.$row['postno'];
	$data .= '&addr='		.$row['addr'];
	$data .= '&addrDtl='	.$row['addr_dtl'];
	$data .= '&phone='		.$row['phone'];
	$data .= '&mobile='		.$row['mobile'];
	$data .= '&grdNm='		.$row['grd_nm'];
	$data .= '&grdAddr='	.$row['grd_addr'];
	$data .= '&grdTelno='	.$row['grd_telno'];
	$data .= '&marryGbn='	.$row['marry_gbn'];
	$data .= '&cohabitGbn='	.$row['cohabit_gbn'];
	$data .= '&eduGbn='		.$row['edu_gbn'];
	$data .= '&relGbn='		.$row['rel_gbn'];
	$data .= '&longLvl='	.$row['longcare_lvl'];
	$data .= '&longGbn='	.$row['longcare_gbn'];

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>
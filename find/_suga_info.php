<?
	include_once('../inc/_login.php');

	$fromDT = $_REQUEST['from'];
	$toDT   = $_REQUEST['to'];

	if (empty($fromDT)) $fromDT = date('Y-m-d', mktime());
	if (empty($toDT)) $toDT = date('Y-m-d', mktime());

	$sql = 'select svc_kind as kind
			,      svc_gbn_cd as code
			,      svc_gbn_nm as name
			,      svc_time as time
			,      svc_pay as pay
			  from suga_service_add
			 where left(svc_from_dt, '.strlen($fromDT).') <= \''.$fromDT.'\'
			   and left(svc_to_dt, '.strlen($toDT).')     >= \''.$toDT.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	$html = '<script language="javascript" type="text/javascript">';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$html .= 'var infoAddSvc_'.$row['kind'].'_'.$row['code'].' = {"kind":"'.$row['kind'].'","code":"'.$row['code'].'","name":"'.$row['name'].'","time":"'.$row['time'].'","pay":"'.$row['pay'].'"};';
	}

	$conn->row_free();


	$sql = 'select lvl_kind as kind
			,      lvl_gbn as gbn
			,      lvl_cd as cd
			,      lvl_id as id
			,      lvl_rate as rate
			,      lvl_pay as pay
			  from income_lvl_self_pay
			 where left(lvl_from_dt, '.strlen($fromDT).') <= \''.$fromDT.'\'
			   and left(lvl_to_dt, '.strlen($toDT).')     >= \''.$toDT.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$html .= 'var infoIncomeLvl_'.$row['kind'].'_'.$row['id'].' = {"kind":"'.$row['kind'].'","gbn":"'.$row['gbn'].'","cd":"'.$row['cd'].'","id":"'.$row['id'].'","rate":"'.$row['rate'].'","pay":"'.$row['pay'].'"};';
	}

	$conn->row_free();


	$html .= '</script>';

	echo $html;
?>
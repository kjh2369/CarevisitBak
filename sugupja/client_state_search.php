<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	
	if (!$isExcel){
		$code	= $_SESSION['userCenterCode'];
		$year	= $_POST['year'];
		$month	= IntVal($_POST['month']);	
	}else{
		$code	= $_SESSION['userCenterCode'];
		$year	= $_GET['year'];
		$month	= IntVal($_GET['month']);
	}
	
	$month	= ($month < 10 ? '0' : '').$month;
	

	$sql = 'SELECT	DISTINCT
					m03_jumin		AS jumin
			,		m03_name		AS name
			,		m03_juso1		AS addr
			,		m03_juso2		AS addr_dtl
			,		m03_tel			AS phone
			,		m03_hp			AS mobile
			,		m03_yboho_name	AS grd_nm
			,		svc.from_dt		AS from_dt
			,		svc.to_dt		AS to_dt
			,		lvl.app_no		AS app_no
			,		lvl.level		AS lvl
			,		kind.kind		AS kind
			FROM	m03sugupja
			INNER	JOIN	client_his_svc AS svc
					ON		svc.org_no	= m03_ccode
					AND		svc.svc_cd	= m03_mkind
					AND		svc.jumin	= m03_jumin
					/*AND		svc.svc_stat= \'1\'*/
					AND		DATE_FORMAT(svc.from_dt,\'%Y%m\')	<= \''.$year.$month.'\'
					AND		DATE_FORMAT(svc.to_dt,\'%Y%m\')		>= \''.$year.$month.'\'
			INNER	JOIN	client_his_lvl AS lvl
					ON		lvl.org_no	= m03_ccode
					AND		lvl.svc_cd	= m03_mkind
					AND		lvl.jumin	= m03_jumin
					AND		DATE_FORMAT(lvl.from_dt,\'%Y%m\')	<= \''.$year.$month.'\'
					AND		DATE_FORMAT(lvl.to_dt,\'%Y%m\')		>= \''.$year.$month.'\'
			INNER	JOIN	client_his_kind AS kind
					ON		kind.org_no	= m03_ccode
					AND		kind.jumin	= m03_jumin
					AND		DATE_FORMAT(kind.from_dt,\'%Y%m\')	<= \''.$year.$month.'\'
					AND		DATE_FORMAT(kind.to_dt,\'%Y%m\')	>= \''.$year.$month.'\'
			WHERE	m03_ccode = \''.$code.'\'
			AND		m03_mkind = \'0\'
			ORDER	BY name';

	$arr = $conn->_fetch_array($sql,'jumin');

	if (Is_Array($arr)){
		foreach($arr as $row){
			$data .= 'name='	.$row['name'];
			$data .= '&jumin='	.$myF->issStyle($row['jumin']);
			$data .= '&appNo='	.$row['app_no'];
			$data .= '&kind='	.$row['kind'];
			$data .= '&gender='	.(SubStr($row['jumin'],6,1) % 2);
			$data .= '&from='	.Str_Replace('-','',$row['from_dt']);
			$data .= '&to='		.Str_Replace('-','',$row['to_dt']);
			$data .= '&addr='	.$row['addr'].' '.$row['addr_dtl'];
			$data .= '&telno='	.($row['phone'] ? $row['phone'] : $row['mobile']);
			$data .= '&grdNm='	.$row['grd_nm'];
			$data .= chr(11);
		}

		if(!$isExcel){
			echo $data;
		}
	}

	include_once('../inc/_db_close.php');
?>
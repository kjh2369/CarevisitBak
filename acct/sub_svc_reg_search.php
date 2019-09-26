<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['code'];	//기관
	$svcCd	= $_POST['svcCd'];	//서비스
	$seq	= $_POST['seq'];	//순번

	//기관명
	$sql = 'SELECT	m00_store_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			ORDER	BY m00_mkind
			LIMIT	1';

	$name = $conn->get_data($sql);

	$sql = 'SELECT	acct_yn
			,		from_dt
			,		to_dt
			FROM	sub_svc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_cd	= \''.$svcCd.'\'
			AND		seq		= \''.$seq.'\'';

	$row = $conn->get_array($sql);

	if ($row){
		$acctYn	= $row['acct_yn'];
		$fromDt	= $row['from_dt'];
		$toDt	= $row['to_dt'];
		$data	= 'name='.$name.'&acctYn='.$acctYn.'&fromDt='.$fromDt.'&toDt='.$toDt;
	}

	Unset($row);
	echo $data;

	include_once('../inc/_db_close.php');
?>
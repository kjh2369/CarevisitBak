<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];

	$sr		= Trim($_POST['sr']);
	$IPIN	= Trim($_POST['IPIN']);
	$rcpt	= Trim($_POST['rcpt']);

	if ($IPIN && $rcpt && $sr){
		$hce->SR	= $sr;
		$hce->IPIN	= $IPIN;
		$hce->rcpt	= $rcpt;
	}

	//if ($hce->backRcptNo && $hce->backRcptNo != $hce->rcpt){
	if ($hce->backRcptNo && !$hce->rcpt){
		$hce->rcpt = $hce->backRcptNo;
	}

	$sql = 'SELECT	m03_name AS name
			,		m03_jumin AS jumin
			,		IFNULL(mst_jumin.jumin,m03_jumin) AS real_jumin
			FROM	m03sugupja
			INNER	JOIN	mst_jumin
					ON		mst_jumin.org_no = m03_ccode
					AND		mst_jumin.gbn = \'1\'
					AND		mst_jumin.code = m03_jumin
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$row = $conn->get_array($sql);

	$data .= 'name='.$row['name'];
	$data .= '&birthDay='.$myF->dateStyle($myF->issToBirthday($row['real_jumin']),'KOR');

	Unset($row);

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CMT\'';

	$gbn = $conn->_fetch_array($sql,'code');

	$sql = 'SELECT	*
			FROM	hce_proc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$data .= '&endYn='		.$row['end_yn'];	//종결여부
	$data .= '&rcptDt='		.$myF->dateStyle($row['rcpt_dt'],'KOR');	//접수일자
	$data .= '&rcptSeq='	.$row['hce_seq'];	//차수
	$data .= '&IVYn='		.($row['itvw_dt'] ? 'Y' : 'N');	//초기면접
	$data .= '&IsptYn='		.($row['ispt_dt'] ? 'Y' : 'N');	//사정기록
	$data .= '&choiceYn='	.($row['chic_dt'] > 0 ? 'Y' : 'N');	//선정기준
	$data .= '&meetGbn='	.$gbn[$row['meet_gbn']]['name'];	//사례회의 구분
	$data .= '&planSeq='	.($row['plan_dt'] > 0 ? 'Y' : 'N');	//서비스 계획서
	$data .= '&contYn='		.($row['cont_dt'] ? 'Y' : 'N');	//동의서
	$data .= '&cuslYn='		.($row['cusl_dt'] ? 'Y' : 'N');	//과정상담
	$data .= '&connYn='		.($row['conn_dt'] ? 'Y' : 'N');	//연계
	$data .= '&mntrYn='		.($row['mntr_dt'] ? 'Y' : 'N');	//모니터링
	$data .= '&restYn='		.($row['rest_dt'] ? 'Y' : 'N');	//재사정
	$data .= '&prvEvYn='	.($row['prvev_dt'] ? 'Y' : 'N'); //제공평가서
	$data .= '&evlnYn='		.($row['evln_dt'] ? 'Y' : 'N');	//평가

	Unset($row);

	$hce->let();

	echo $data;

	include_once('../inc/_db_close.php');
?>
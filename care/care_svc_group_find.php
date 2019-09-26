<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$sugaCd	= $_POST['sugaCd'];
	$seq	= $_POST['seq'];

	$sql = 'SELECT	group_nm
			,		res_cd
			,		target
			,		mem_cd
			,		category
			FROM	care_svc_group
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		suga_cd = \''.$sugaCd.'\'
			AND		seq		= \''.$seq.'\'';

	$R = $conn->get_array($sql);

	$grpNm	= $R['group_nm'];
	$resCd	= $R['res_cd'];
	$target	= $R['target'];
	$memCd	= $R['mem_cd'];
	$cateCd	= $R['category'];

	Unset($R);

	$cateNm = '';
	$parent = $cateCd;

	if ($parent){
		while(true){
			$sql = 'SELECT	parent
					,		name
					FROM	mst_category
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$SR.'\'
					AND		gbn		= \'01\'
					AND		code	= \''.$parent.'\'';

			$row = $conn->get_array($sql);
			$cd = $row['parent'];
			$cateNm = $row['name'].($cateNm ? ' - ' : '').$cateNm;

			if ($cd == '0') $cd = '';
			if ($cd){
				$parent = $cd;
			}else{
				break;
			}
		}
	}

	if ($resCd){
		$sql = 'SELECT	cust_nm
				FROM	care_cust
				WHERE	org_no	= \''.$orgNo.'\'
				AND		cust_cd = \''.$resCd.'\'';
		$resNm = $conn->get_data($sql);
	}

	if ($memCd){
		$sql = 'SELECT	DISTINCT m02_yname
				FROM	m02yoyangsa
				WHERE	m02_ccode	= \''.$orgNo.'\'
				AND		m02_yjumin	= \''.$memCd.'\'';
		$memNm = $conn->get_data($sql);
	}

	echo 'grpNm='.$grpNm
		.'&resCd='.$resCd
		.'&resNm='.$resNm
		.'&memCd='.$ed->en($memCd)
		.'&memNm='.$memNm
		.'&cateCd='.$cateCd
		.'&cateNm='.$cateNm
		.'&target='.$target;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$svcGbn	= $_POST['svcGbn'];
	$svcCd	= $_POST['svcCd'];

	$sql = 'SELECT	seq,from_dt,to_dt,acct_yn,acct_from,acct_to,acct_gbn,stnd_cost,over_cost,limit_cnt,use_yn
			FROM	cv_svc_fee
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_gbn	= \''.$svcGbn.'\'
			AND		svc_cd	= \''.$svcCd.'\'
			AND		del_flag= \'N\'
			ORDER	BY from_dt, to_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data .= ($data ? '?' : '')
			  .'seq='.$row['seq'].'&fromDt='.$row['from_dt'].'&toDt='.$row['to_dt'].'&acctYn='.$row['acct_yn'].'&acctFrom='.$row['acct_from'].'&acctTo='.$row['acct_to'].'&acctGbn='.$row['acct_gbn'].'&stndCost='.$row['stnd_cost'].'&overCost='.$row['over_cost'].'&limitCnt='.$row['limit_cnt'].'&usrYn='.$row['use_yn'];
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo		= $_POST['orgNo'];		//기관코드
	$svcGbn		= $_POST['svcGbn'];		//서비스구분 1:메인서비스, 2:부가서비스
	$svcCd		= $_POST['svcCd'];		//서비스코드
	$seq		= $_POST['seq'];		//순번
	$useYn		= $_POST['useYn'];		//사용여부
	$fromDt		= $_POST['fromDt'];		//적용일
	$toDt		= $_POST['toDt'];		//종료일
	$acctYn		= $_POST['acctYn'];		//과금여부
	$acctFrom	= $_POST['acctFrom'];		//과금시작일
	$acctTo		= $_POST['acctTo'];		//과금종료일
	$acctGbn	= $_POST['acctGbn'];	//과금기준 1:정율제, 2:정액제
	$stndCost	= $_POST['stndCost'];	//기본금
	$overCost	= $_POST['overCost'];	//초과단가
	$limitCnt	= $_POST['limitCnt'];	//제한수


	if (!$orgNo || !$svcCd){
		echo '!!! 기관코드 및 서비스코드오류 !!!';
		$conn->close();
		exit;
	}

	if ($seq){
		$sql = 'UPDATE	cv_svc_fee
				SET		from_dt		= \''.$fromDt.'\'
				,		to_dt		= \''.$toDt.'\'
				,		acct_yn		= \''.$acctYn.'\'
				,		acct_from	= \''.$acctFrom.'\'
				,		acct_to		= \''.$acctTo.'\'
				,		acct_gbn	= \''.$acctGbn.'\'
				,		stnd_cost	= \''.$stndCost.'\'
				,		over_cost	= \''.$overCost.'\'
				,		limit_cnt	= \''.$limitCnt.'\'
				,		use_yn		= \''.$useYn.'\'
				,		mod_gbn		= \'1\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		svc_gbn	= \''.$svcGbn.'\'
				AND		svc_cd	= \''.$svcCd.'\'
				AND		seq		= \''.$seq.'\'';
	}else{
		//중복검사
		$sql = 'SELECT	seq
				,		from_dt
				,		to_dt
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

			if (($row['from_dt'] <= $fromDt && $row['to_dt'] >= $fromDt) ||
				($row['from_dt'] <= $toDt && $row['to_dt'] >= $toDt)){
				echo '!!! 기존 적용일자 중복 !!!';
				$conn->close();
				exit;
			}
		}

		$conn->row_free();

		//다음순번
		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	cv_svc_fee
				WHERE	org_no	= \''.$orgNo.'\'
				AND		svc_gbn	= \''.$svcGbn.'\'
				AND		svc_cd	= \''.$svcCd.'\'';
		$seq = $conn->get_data($sql);

		$sql = 'INSERT INTO cv_svc_fee (
				 org_no
				,svc_gbn
				,svc_cd
				,seq
				,from_dt
				,to_dt
				,acct_yn
				,acct_from
				,acct_to
				,acct_gbn
				,stnd_cost
				,over_cost
				,limit_cnt
				,use_yn
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$svcGbn.'\'
				,\''.$svcCd.'\'
				,\''.$seq.'\'
				,\''.$fromDt.'\'
				,\''.$toDt.'\'
				,\''.$acctYn.'\'
				,\''.$acctFrom.'\'
				,\''.$acctTo.'\'
				,\''.$acctGbn.'\'
				,\''.$stndCost.'\'
				,\''.$overCost.'\'
				,\''.$limitCnt.'\'
				,\''.$useYn.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}


	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
		 exit;
	}

	$conn->commit();


	//적용
	$userCode = $orgNo;
	include_once('../inc/set_val.php');
	include_once('../inc/_db_close.php');
?>
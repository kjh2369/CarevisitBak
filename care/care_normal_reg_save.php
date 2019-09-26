<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************

		재가지원/자원연계 일반접수 등록

	 *********************************************************/

	$code	= $_SESSION['userCenterCode'];
	$userCd	= $_SESSION['userCode'];
	$sr		= $_POST['sr'];
	$seq	= $_POST['seq'];
	$jumin	= $_POST['txtJumin1'].$_POST['txtJumin2'];
	$name	= $_POST['txtName'];
	$phone	= Str_Replace('-','',$_POST['txtPhone']);
	$mobile	= Str_Replace('-','',$_POST['txtMobile']);
	$postno	= $_POST['txtPostno1'].$_POST['txtPostno2'];
	$addr	= $_POST['txtAddr'];
	$addrDtl= $_POST['txtAddrDtl'];
	$grdNm	= $_POST['txtGrdNm'];
	$grdAddr= $_POST['txtGrdAddr'];
	$grdTel	= Str_Replace('-','',$_POST['txtGrdTel']);
	$marry	= $_POST['cboMarry'];
	$cohabit= $_POST['cboCohabit'];
	$edu	= $_POST['cboEdu'];
	$rel	= $_POST['cboRel'];
	$lvl	= $_POST['cboLvl'];		//등급
	$gbn	= $_POST['cboGbn'];		//구분
	$regDt	= $_POST['txtRegDt'];
	$endDt	= $_POST['txtEndDt'];
	$reason	= $_POST['txtEndReason'];
	$kindGbn= $_POST['cboKindGbn'];

	$rstDt		= $_POST['txtRstDt'];		//처리일자
	$rstReason	= $_POST['cboRstReason'];	//처리결과
	$reasonStr	= $_POST['txtReasonStr'];	//사유

	$sql = 'SELECT	COUNT(*)
			FROM	care_client_normal
			WHERE	org_no		= \''.$code.'\'
			AND		normal_sr	= \''.$sr.'\'
			AND		normal_seq	= \''.$seq.'\'';

	$liCnt = $conn->get_data($sql);

	if ($liCnt > 0){
		$new = false;
	}else{
		$new = true;
	}

	if ($new){
		$sql = 'SELECT	IFNULL(MAX(normal_seq),0)+1
				FROM	care_client_normal
				WHERE	org_no		= \''.$code.'\'
				AND		normal_sr	= \''.$sr.'\'';

		$seq = $conn->get_data($sql);

		$sql = 'INSERT INTO care_client_normal (
				 org_no
				,normal_sr
				,normal_seq) VALUES (
				 \''.$code.'\'
				,\''.$sr.'\'
				,\''.$seq.'\'
				)';

		$query[SizeOf($query)] = $sql;
	}

	$sql = 'UPDATE	care_client_normal
			SET		jumin		= \''.$jumin.'\'
			,		name		= \''.$name.'\'
			,		postno		= \''.$postno.'\'
			,		addr		= \''.$addr.'\'
			,		addr_dtl	= \''.$addrDtl.'\'
			,		phone		= \''.$phone.'\'
			,		mobile		= \''.$mobile.'\'
			,		grd_nm		= \''.$grdNm.'\'
			,		grd_addr	= \''.$grdAddr.'\'
			,		grd_telno	= \''.$grdTel.'\'
			,		marry_gbn	= \''.$marry.'\'
			,		cohabit_gbn	= \''.$cohabit.'\'
			,		edu_gbn		= \''.$edu.'\'
			,		rel_gbn		= \''.$rel.'\'
			,		longcare_lvl= \''.$lvl.'\'
			,		longcare_gbn= \''.$gbn.'\'
			,		reg_dt		= \''.$regDt.'\'
			,		end_dt		= \''.$endDt.'\'
			,		end_reason	= \''.$reason.'\'
			,		kind_gbn	= \''.$kindGbn.'\'
			,		rst_dt		= \''.$rstDt.'\'
			,		rst_reason	= \''.$rstReason.'\'
			,		reason_str	= \''.$reasonStr.'\'
			';

	if ($new){
		$sql .= '
			,		insert_dt	= NOW()
			,		insert_id	= \''.$userCd.'\'';
	}else{
		$sql .= '
			,		update_dt	= NOW()
			,		update_id	= \''.$userCd.'\'';
	}

	$sql .= '
			WHERE	org_no		= \''.$code.'\'
			AND		normal_sr	= \''.$sr.'\'
			AND		normal_seq	= \''.$seq.'\'';

	$query[SizeOf($query)] = $sql;

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>
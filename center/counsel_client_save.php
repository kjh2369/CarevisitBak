<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	echo $myF->header_script();
	
	$mode  = $_POST['mode'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$is_pop = $_POST['is_pop'];

	$conn->begin();
	

	if($mode == 'stat' || $mode == 'state') {
		/********************************************
			김주완 2012.09.04 상태변화일지 추가
		********************************************/
		
		$code  = $_POST['code'] != '' ? $_POST['code'] : $_SESSION['userCenterCode'];
		$statDt = $_POST['statDt'];
		$statBackDt = $_POST['statBackDt'] != '' ? $_POST['statBackDt'] : $_POST['regDt'];
		$statSsn   = $ed->de($_POST['statSsn']);

		$sql = 'DELETE
				  FROM counsel_client_state
				 WHERE org_no = \''.$code.'\'
				   AND jumin  = \''.$statSsn.'\'
				   AND reg_dt = \''.$statBackDt.'\'';
		$conn->execute($sql);

		if (!Empty($statDt)){
			$statRegCd = $ed->de($_POST['statRegCd']);
			$statRegNm = $conn->member_name($code, $statRegCd);
			$statYoyCd = $ed->de($_POST['statYoyCd']);
			$statYoyNm = $conn->member_name($code, $statYoyCd);
			$statText  = AddSlashes($_POST['statText']);
			$statTake  = AddSlashes($_POST['statTake']);

			$sql = 'REPLACE INTO counsel_client_state (
					 org_no
					,jumin
					,reg_dt
					,reg_cd
					,reg_nm
					,yoy_cd
					,yoy_nm
					,stat
					,take) VALUES (
					 \''.$code.'\'
					,\''.$statSsn.'\'
					,\''.$statDt.'\'
					,\''.$statRegCd.'\'
					,\''.$statRegNm.'\'
					,\''.$statYoyCd.'\'
					,\''.$statYoyNm.'\'
					,\''.$statText.'\'
					,\''.$statTake.'\')';
			
			$conn->execute($sql);
		}

	}else {
		include_once('../counsel/client_counsel_'.$mode.'_save.php');
	}

	$ssn = $_POST[$mode.'_ssn'] != '' ? $_POST[$mode.'_ssn'] : $ed->en($statSsn);
	
	$conn->commit();
	
	include_once('../inc/_db_close.php');
	
	if($is_pop == 'Y'){
		echo '<form name="f" method="post">';
		echo '<input name="type" type="hidden" value="'.$mode.'">';
		echo '<input name="code" type="hidden" value="'.$code.'">';
		
		echo '<input name="ssn" type="hidden" value="'.$ssn.'">';
		echo '<input name="seq" type="hidden" value="'.$_POST[$mode.'_seq'].'">';
		echo '<input name="regDt" type="hidden" value="'.$statBackDt.'">';
		echo '<input name="yymm" type="hidden" value="'.substr(str_replace('-','',$_POST[$mode.'_dt']), 0, 6).'">';
		echo '</form>';
		echo '<script>';
		echo 'alert(\''.$myF->message('ok','N').'\');';
		echo 'f.action = "../sugupja/process_counseling_reg.php";'; 
		echo 'f.submit();';
		echo '</script>';
	}else {
		echo '<script>';
		echo 'alert(\''.$myF->message('ok','N').'\');';	
		echo 'location.replace(\'../sugupja/counsel_client.php?year='.$year.'&month='.$month.'&seq='.$stress_seq.'&ssn='.$ed->en($stress_ssn).'\');';
		echo '</script>';
	}
?>
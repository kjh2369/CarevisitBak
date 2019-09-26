<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$userArea = $_SESSION['userArea'];
	$chicSeq= $_POST['seq'];
	
	if($userArea == '05'){
	
		$sql = 'SELECT	count(*)
				FROM	hce_choice_cn
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		chic_seq= \''.$chicSeq.'\'';
		$cnt2 = $conn->get_data($sql);

	}

	if($cnt2 > 0){
		$sql = 'SELECT	*
				FROM	hce_choice_cn
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		chic_seq= \''.$chicSeq.'\'';

		$row = $conn->get_array($sql);

		if ($row){
			$data .= 'choiceDt='.$row['chic_dt'];
			$data .= '&lblK1='	.$row['income_gbn'];
			$data .= '&lblL1='	.$row['dwelling_gbn'];
			$data .= '&lblM1='	.$row['gross_gbn'];
			$data .= '&lblN1='	.$row['disease_gbn'];
			$data .= '&lblO1='	.$row['handicap_gbn'];
			$data .= '&lblP1='	.$row['adl_gbn'];
			$data .= '&lblQ1='	.$row['care_gbn'];
			$data .= '&lblR1='	.$row['life_gbn'];
			$data .= '&lblS1='	.$row['social_rel_gbn'];
			$data .= '&lblT1='	.$row['feel_gbn'];
			$data .= '&lblU1='	.$row['free_gbn'];
			$data .= '&comment='.$row['comment'];
		}
	}else {

		$sql = 'SELECT	*
				FROM	hce_choice
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		chic_seq= \''.$chicSeq.'\'';
		$row = $conn->get_array($sql);
		
		if ($row){
			$data .= 'choiceDt='.$row['chic_dt'];
			$data .= '&lblA1='	.$row['income_gbn'];
			$data .= '&lblA2='	.$row['nonfamily_gbn'];
			$data .= '&lblB1='	.$row['dwelling_gbn'];
			$data .= '&lblB2='	.$row['rental_gbn'];
			$data .= '&lblC1='	.$row['gross_gbn'];
			$data .= '&lblC2='	.$row['public_gbn'];
			$data .= '&lblD2='	.$row['help_gbn'];
			$data .= '&lblE1='	.$row['body_gbn'];
			$data .= '&lblE2='	.$row['body_patient_gbn'];
			$data .= '&lblF1='	.$row['feel_gbn'];
			$data .= '&lblF2='	.$row['feel_patient_gbn'];
			$data .= '&lblG1='	.$row['handicap_gbn'];
			$data .= '&lblG2='	.$row['handi_dup_gbn'];
			$data .= '&lblG3='	.$row['handi_2per_gbn'];
			$data .= '&lblH1='	.$row['adl_gbn'];
			$data .= '&lblI1='	.$row['care_gbn'];
			$data .= '&lblJ1='	.$row['free_gbn'];
			$data .= '&comment='.$row['comment'];
		}
	}

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$fromDt	= $_POST['fromDt'];
	$toDt	= $_POST['toDt'];
	$appNo	= $_POST['appNo'];

	$val = Explode('?',$_POST['para']);

	if (!is_array($val)){
		$conn->close();
		echo 9;
		exit;
	}


	//복원
	$sql = 'UPDATE	lg2cv
			SET		del_flag= \'N\'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		del_flag= \'Y\'';

	$query[] = $sql;


	//기존삭제
	$sql = 'UPDATE	lg2cv
			SET		del_flag = \'Y\'
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		reg_dt	>= \''.$fromDt.'\'
			AND		reg_dt	<= \''.$toDt.'\'';

	if ($appNo){
		$sql .= '
			AND		app_no = \''.$appNo.'\'';
	}

	$query[] = $sql;


	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->execute($sql);
			 echo 9;
			 exit;
		}
	}

	$conn->commit();


	Unset($query);


	$sql = 'DELETE
			FROM	lg2cv
			WHERE	org_no	= \''.$orgNo.'\'
			AND		del_flag= \'Y\'';

	$query[] = $sql;


	foreach($val as $idx => $R){
		parse_str($R, $row);

		$detail = Explode('<BR><BR>',$row['detail']);
		$tmp	= Explode('<BR>',$detail[1]);

		Unset($detail);


		//신체횔동 RsvcTm01
		//인지활동 RsvcTm02
		//가사,일상생활 RsvcTm03
		//정서지원 RsvcTm04

		//신체기능 RaltrBdy 1:호전, 2:유지, 3:악화
		//식사기능 RaltrEat 1:호전, 2:유지, 3:악화
		//인지기능 RaltrPct 1:호전, 2:유지, 3:악화

		//대변 실수 횟수 RaltrDfctnL
		//소변 실수 횟수 RaltrDfctnS

		//특이 사항 RpcrlComt
		$detail[1] = IntVal($row['RsvcTm01']);
		$detail[2] = IntVal($row['RsvcTm02']);
		$detail[3] = IntVal($row['RsvcTm03']);
		$detail[4] = IntVal($row['RsvcTm04']);

		$detail[5] = $row['RaltrBdy'];
		$detail[6] = $row['RaltrEat'];
		$detail[7] = $row['RaltrPct'];

		$detail[8] = IntVal($row['RaltrDfctnL']);
		$detail[9] = IntVal($row['RaltrDfctnS']);

		$detail[10] = $row['RpcrlComt'];
		/*
			foreach($tmp as $str){
				$str = Explode(':',$str);
				$detail[Trim($str[0])] = IntVal($str[1]);
			}
		*/

		if (!$seq[$row['yymm']][$row['appNo']][$row['fromDate']]){
			$sql = 'SELECT	IFNULL(MAX(seq),0)+1
					FROM	lg2cv
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$row['yymm'].'\'
					AND		app_no	= \''.$row['appNo'].'\'
					AND		reg_dt	= \''.$row['fromDate'].'\'
					AND		del_flag= \'N\'';

			$seq[$row['yymm']][$row['appNo']][$row['fromDate']] = $conn->get_data($sql);
		}else{
			$seq[$row['yymm']][$row['appNo']][$row['fromDate']] ++;
		}

		$row['seq'] = $seq[$row['yymm']][$row['appNo']][$row['fromDate']];


		$sql = 'INSERT INTO lg2cv VALUES(
				 \''.$orgNo.'\'					/*org_no*/
				,\''.$row['yymm'].'\'			/*yymm*/
				,\''.$row['appNo'].'\'			/*app_no*/
				,\''.$row['fromDate'].'\'		/*reg_dt*/
				,\''.$row['seq'].'\'			/*seq*/
				,\''.$row['name'].'\'			/*name*/
				,\''.$row['svcGbn'].'\'			/*svc_gbn*/
				,\''.$row['fromDate'].'\'		/*from_dt*/
				,\''.$row['fromTime'].'\'		/*from_tm*/
				,\''.$row['toDate'].'\'			/*to_dt*/
				,\''.$row['toTime'].'\'			/*to_tm*/
				,\''.$row['procTime'].'\'		/*proc_time*/
				,\''.$row['memNm'].'\'			/*mem_nm*/
				,\''.$row['memHP'].'\'			/*mem_hp*/
				,\''.$row['sendGbn'].'\'		/*send_gbn*/
				,\''.$row['errDate'].'\'		/*err_dt*/
				,\''.$row['errTime'].'\'		/*err_tm*/
				,\''.$row['useYn'].'\'			/*use_yn*/
				,\''.$detail[1].'\'	/*dtl_1*/
				,\''.$detail[2].'\'	/*dtl_2*/
				,\''.$detail[3].'\'	/*dtl_3*/
				,\''.$detail[4].'\'	/*dtl_4*/
				,\''.$detail[5].'\'	/*dtl_5*/
				,\''.$detail[6].'\'	/*dtl_6*/
				,\''.$detail[7].'\'	/*dtl_7*/
				,\''.$detail[8].'\'	/*dtl_8*/
				,\''.$detail[9].'\'	/*dtl_9*/
				,\''.$detail[10].'\'	/*dtl_10*/
				,\'\'	/*other*/
				,\'N\'	/*del_flag*/
				,NOW()
				,\''.$_SESSION['userCode'].'\'
				)';
			#$sql = 'INSERT INTO lg2cv VALUES(
			#		 \''.$orgNo.'\'					/*org_no*/
			#		,\''.$row['yymm'].'\'			/*yymm*/
			#		,\''.$row['appNo'].'\'			/*app_no*/
			#		,\''.$row['fromDate'].'\'		/*reg_dt*/
			#		,\''.$row['seq'].'\'			/*seq*/
			#		,\''.$row['name'].'\'			/*name*/
			#		,\''.$row['svcGbn'].'\'			/*svc_gbn*/
			#		,\''.$row['fromDate'].'\'		/*from_dt*/
			#		,\''.$row['fromTime'].'\'		/*from_tm*/
			#		,\''.$row['toDate'].'\'			/*to_dt*/
			#		,\''.$row['toTime'].'\'			/*to_tm*/
			#		,\''.$row['procTime'].'\'		/*proc_time*/
			#		,\''.$row['memNm'].'\'			/*mem_nm*/
			#		,\''.$row['memHP'].'\'			/*mem_hp*/
			#		,\''.$row['sendGbn'].'\'		/*send_gbn*/
			#		,\''.$row['errDate'].'\'		/*err_dt*/
			#		,\''.$row['errTime'].'\'		/*err_tm*/
			#		,\''.$row['useYn'].'\'			/*use_yn*/
			#		,\''.$detail['정서지원'].'\'	/*dtl_1*/
			#		,\''.$detail['신체활동'].'\'	/*dtl_2*/
			#		,\''.$detail['일상생활'].'\'	/*dtl_3*/
			#		,\''.$detail['개인활동'].'\'	/*dtl_4*/
			#		,\'\'	/*dtl_5*/
			#		,\'\'	/*dtl_6*/
			#		,\'\'	/*dtl_7*/
			#		,\'\'	/*dtl_8*/
			#		,\'\'	/*dtl_9*/
			#		,\'\'	/*dtl_10*/
			#		,\'\'	/*other*/
			#		,\'N\'	/*del_flag*/
			#		,NOW()
			#		,\''.$_SESSION['userCode'].'\'
			#		)';

		if ($row['appNo']){
			$query[] = $sql;
		}
	}

	if (is_array($query)){
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
	}
	echo 1;

	include_once('../inc/_db_close.php');
?>
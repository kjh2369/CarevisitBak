<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$mode = $_POST['mode'];

	if ($mode == '2' || $mode == '12' || $mode == '32'){
		$year  = $_POST['year'];
		$month = $_POST['month'];
		$month = (IntVal($month) < 10 ? '0' : '').IntVal($month);
		$data  = Explode(chr(1),$_POST['data']);

		$conn->begin();

		if ($mode == '2'){
			$table = 'sms_acct_'.$year.$month;
		}else if ($mode == '12'){
			$table = 'smart_acct_'.$year.$month;
		}else if ($mode == '32'){
			$table = 'center_acct_'.$year.$month;
		}

		$sql = 'SELECT COUNT(*)
				  FROM '.$table;

		@$rowCount = $conn->get_data($sql);

		if (!Is_Numeric($rowCount)){
			if ($mode == '2'){
				$sql = 'CREATE TABLE '.$table.' (
							org_no char(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
							acct_yn char(1) COLLATE utf8_unicode_ci DEFAULT \'Y\',
							cnt int(4) DEFAULT \'0\',
							basic int(9) DEFAULT \'0\',
							over int(9) DEFAULT \'0\',
							tot int(9) DEFAULT \'0\',
							insert_dt datetime DEFAULT NULL,
							PRIMARY KEY (org_no)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
			}else if ($mode == '12'){
				$sql = 'CREATE TABLE '.$table.' (
							org_no char(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
							acct_yn char(1) COLLATE utf8_unicode_ci DEFAULT \'Y\',
							admin_cnt int(4) DEFAULT \'0\',
							admin_amt int(9) DEFAULT \'0\',
							mem_cnt int(4) DEFAULT \'0\',
							mem_amt int(9) DEFAULT \'0\',
							total_amt int(9) DEFAULT \'0\',
							insert_dt datetime DEFAULT NULL,
							PRIMARY KEY (org_no)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
			}else if ($mode == '32'){
				$sql = 'CREATE TABLE '.$table.' (
							org_no char(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
							hold_yn char(1) COLLATE utf8_unicode_ci DEFAULT \'Y\',
							basic_amt int(9) DEFAULT \'0\',
							over_amt int(9) DEFAULT \'0\',
							tot_amt int(9) DEFAULT \'0\',
							client_cnt int(4) DEFAULT \'0\',
							limit_cnt int(4) DEFAULT \'0\',
							client_cost int(9) DEFAULT \'0\',
							cont_yn char(1) DEFAULT \'Y\',
							insert_dt datetime DEFAULT NULL,
							PRIMARY KEY (org_no)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
			}

			if (!$conn->execute($sql)){
				 $conn->close();
				 echo 9;
				 exit;
			}
		}

		if ($mode == '2'){
			$query = 'INSERT INTO '.$table.' (
					   org_no
					  ,acct_yn
					  ,cnt
					  ,basic
					  ,over
					  ,tot
					  ,insert_dt) VALUES (';
		}else if ($mode == '12'){
			$query = 'INSERT INTO '.$table.' (
					   org_no
					  ,acct_yn
					  ,admin_cnt
					  ,admin_amt
					  ,mem_cnt
					  ,mem_amt
					  ,total_amt
					  ,insert_dt) VALUES (';
		}else if ($mode == '32'){
			$query = 'INSERT INTO '.$table.' (
					   org_no
					  ,hold_yn
					  ,basic_amt
					  ,over_amt
					  ,tot_amt
					  ,client_cnt
					  ,limit_cnt
					  ,client_cost
					  ,cont_yn
					  ,insert_dt) VALUES (';
		}

		foreach($data as $row){
			if (!Empty($row)){
				$val = Explode(chr(2),$row);
				$sql = '';

				foreach($val as $col){
					if (Empty($sql)){
						$sql = $query.'\''.$col.'\'';
					}else{
						$sql .= ',\''.$col.'\'';
					}
				}

				$sql .= ',NOW())';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo 9;
					 exit;
				}
			}
		}

		$conn->commit();

	}else if ($mode == '3_1' || $mode == '13_1' || $mode == '41_1'){
		$code = $_POST['code'];

		if ($mode == '3_1'){
			$table = 'sms_deposit';
		}else if ($mode == '13_1'){
			$table = 'smart_deposit';
		}else if ($mode == '41_1'){
			$table = 'center_deposit_'.$gDomainID;
		}

		if (Empty($code)){
			echo 9;
			exit;
		}

		$regDt = Str_Replace('-','',$_POST['date']);

		$sql = 'SELECT COUNT(*)
				  FROM '.$table.'
				 WHERE org_no = \''.$code.'\'
				   AND reg_dt = \''.$regDt.'\'';

		$liCnt = IntVal($conn->get_data($sql));

		if ($liCnt > 0){
			echo 7;
			exit;
		}

		$conn->begin();

		$sql = 'INSERT INTO '.$table.' (
				 org_no
				,reg_dt
				,amt
				,type
				,other
				,insert_dt) VALUES (
				 \''.$code.'\'
				,\''.$regDt.'\'
				,\''.$_POST['amt'].'\'
				,\''.$_POST['type'].'\'
				,\''.$_POST['other'].'\'
				,NOW()
				)';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();

	}else if ($mode == 'CARE_SUGA_APPLY'){
		//재가지원서비스 등록 및 변경
		$type		= $_POST['type'];
		$mstCd		= $_POST['mstCd'];
		$mstNm		= Str_Replace('/','<br>',$_POST['mstNm']);
		$proCd		= $_POST['proCd'];
		$proNm		= Str_Replace('/','<br>',$_POST['proNm']);
		$svcCd		= $_POST['svcCd'];
		$svcNm		= Str_Replace('/','<br>',$_POST['svcNm']);
		$cost		= Str_Replace(',','',$_POST['cost']);
		$seq		= $_POST['seq'];
		$from		= $_POST['from'];
		$to			= $_POST['to'];
		$reYn		= $_POST['reYn'];

		if ($type == 'mst'){
			$sql = 'SELECT	COUNT(*)
					FROM	suga_care
					WHERE	cd1 = \''.$mstCd.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt > 0){
				$sql = 'UPDATE	suga_care
						SET		nm1 = \''.$mstNm.'\'
						WHERE	cd1 = \''.$mstCd.'\'';
			}else{
				$sql = 'INSERT INTO suga_care(
						 cd1
						,cd2
						,cd3
						,seq
						,nm1) VALUES(
						 \''.$mstCd.'\'
						,\'\'
						,\'\'
						,\'1\'
						,\''.$mstNm.'\'
						)';
			}
		}else if ($type == 'pro'){
			$sql = 'SELECT	COUNT(*)
					FROM	suga_care
					WHERE	cd1 = \''.$mstCd.'\'
					AND		cd2 = \''.$proCd.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt > 0){
				$sql = 'UPDATE	suga_care
						SET		nm2 = \''.$proNm.'\'
						WHERE	cd1 = \''.$mstCd.'\'
						AND		cd2 = \''.$proCd.'\'';
			}else{
				$sql = 'SELECT	COUNT(*)
						FROM	suga_care
						WHERE	cd1 = \''.$mstCd.'\'
						AND		cd2 = \'\'';

				$cnt = $conn->get_data($sql);

				if ($cnt > 0){
					$sql = 'UPDATE	suga_care
							SET		cd2 = \''.$proCd.'\'
							,		nm2 = \''.$proNm.'\'
							WHERE	cd1 = \''.$mstCd.'\'';
				}else{
					$sql = 'SELECT	nm1
							FROM	suga_care
							WHERE	cd1	= \''.$mstCd.'\'
							LIMIT	1';

					$row = $conn->get_array($sql);

					$mstNm = $row['nm1'];

					Unset($row);

					$sql = 'INSERT INTO suga_care(
							 cd1
							,cd2
							,cd3
							,nm1
							,nm2
							,nm3
							,seq) VALUES(
							 \''.$mstCd.'\'
							,\''.$proCd.'\'
							,\'\'
							,\''.$mstNm.'\'
							,\''.$proNm.'\'
							,\'\'
							,\'1\'
							)';
				}
			}
		}else if ($type == 'svc'){
			$sql = 'SELECT	COUNT(*)
					FROM	suga_care
					WHERE	cd1 = \''.$mstCd.'\'
					AND		cd2 = \''.$proCd.'\'
					AND		cd3 = \''.$svcCd.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt > 0){
				$sql = 'UPDATE	suga_care
						SET		nm3 = \''.$svcNm.'\'
						WHERE	cd1 = \''.$mstCd.'\'
						AND		cd2 = \''.$proCd.'\'
						AND		cd3 = \''.$svcCd.'\'';
			}else{
				$sql = 'SELECT	COUNT(*)
						FROM	suga_care
						WHERE	cd1 = \''.$mstCd.'\'';

				$cnt = $conn->get_data($sql);

				if ($cnt == 0){
					echo 91;
					exit;
				}

				$sql = 'SELECT	COUNT(*)
						FROM	suga_care
						WHERE	cd1 = \''.$mstCd.'\'
						AND		cd2 = \''.$proCd.'\'
						AND		cd3 = \'\'';

				$cnt = $conn->get_data($sql);

				if ($cnt > 0){
					$sql = 'UPDATE	suga_care
							SET		cd3 = \''.$svcCd.'\'
							,		nm3 = \''.$svcNm.'\'
							WHERE	cd1 = \''.$mstCd.'\'
							AND		cd2 = \''.$proCd.'\'';
				}else{
					$sql = 'SELECT	nm1,nm2
							FROM	suga_care
							WHERE	cd1	= \''.$mstCd.'\'
							AND		cd2	= \''.$proCd.'\'
							LIMIT	1';

					$row = $conn->get_array($sql);

					$mstNm = $row['nm1'];
					$proNm = $row['nm2'];

					Unset($row);

					$sql = 'INSERT INTO suga_care(
							 cd1
							,cd2
							,cd3
							,nm1
							,nm2
							,nm3
							,seq) VALUES(
							 \''.$mstCd.'\'
							,\''.$proCd.'\'
							,\''.$svcCd.'\'
							,\''.$mstNm.'\'
							,\''.$proNm.'\'
							,\''.$svcNm.'\'
							,\'1\'
							)';
				}
			}
		}else if ($type == 'other'){
			//과거내역 유무
			$sql = 'SELECT	COUNT(*)
					FROM	suga_care
					WHERE	cd1 = \''.$mstCd.'\'
					AND		cd2 = \''.$proCd.'\'
					AND		cd3 = \''.$svcCd.'\'
					AND		from_dt > \''.$from.'\'
					AND		seq != \''.$seq.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt > 0){
				echo 111;
				exit;
			}

			//기간 중복확인
			$sql = 'SELECT	COUNT(*)
					FROM	suga_care
					WHERE	cd1 = \''.$mstCd.'\'
					AND		cd2 = \''.$proCd.'\'
					AND		cd3 = \''.$svcCd.'\'
					AND		from_dt <= \''.$from.'\'
					AND		to_dt >= \''.$from.'\'
					AND		seq != \''.$seq.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt > 0){
				echo 111;
				exit;
			}

			$sql = 'SELECT	COUNT(*)
					FROM	suga_care
					WHERE	cd1 = \''.$mstCd.'\'
					AND		cd2 = \''.$proCd.'\'
					AND		cd3 = \''.$svcCd.'\'
					AND		from_dt <= \''.$to.'\'
					AND		to_dt >= \''.$to.'\'
					AND		seq != \''.$seq.'\'';

			$cnt = $conn->get_data($sql);

			if ($cnt > 0){
				echo 111;
				exit;
			}

			if ($reYn == 'Y'){
				$sql = 'SELECT	seq,nm1,nm2,nm3
						FROM	suga_care
						WHERE	cd1	= \''.$mstCd.'\'
						AND		cd2	= \''.$proCd.'\'
						AND		cd3	= \''.$svcCd.'\'
						ORDER	BY seq DESC
						LIMIT	1';

				$row = $conn->get_array($sql);

				$seq = $row['seq']+1;
				$mstNm = $row['nm1'];
				$proNm = $row['nm2'];
				$svcNm = $row['nm3'];

				Unset($row);

				$sql = 'INSERT INTO suga_care(
						 cd1
						,cd2
						,cd3
						,nm1
						,nm2
						,nm3
						,cost
						,seq
						,from_dt
						,to_dt) VALUES(
						 \''.$mstCd.'\'
						,\''.$proCd.'\'
						,\''.$svcCd.'\'
						,\''.$mstNm.'\'
						,\''.$proNm.'\'
						,\''.$svcNm.'\'
						,\''.$cost.'\'
						,\''.$seq.'\'
						,\''.$from.'\'
						,\''.$to.'\'
						)';
			}else{
				$sql = 'UPDATE	suga_care
						SET		cost		= \''.$cost.'\'
						,		from_dt		= \''.$from.'\'
						,		to_dt		= \''.$to.'\'
						WHERE	cd1	= \''.$mstCd.'\'
						AND		cd2	= \''.$proCd.'\'
						AND		cd3	= \''.$svcCd.'\'
						AND		seq	= \''.$seq.'\'';
			}

		}else{
			echo 8;
			exit;
		}

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();

	}else if ($mode == 'CARE_SUGA_DELETE'){
		//삭제
		$mstCd = SubStr($_POST['code'],0,1);
		$proCd = SubStr($_POST['code'],1,2);
		$svcCd = SubStr($_POST['code'],3,2);
		$seq = $_POST['seq'];

		$sql = 'DELETE
				FROM	suga_care
				WHERE	cd1 = \''.$mstCd.'\'
				AND		cd2 = \''.$proCd.'\'
				AND		cd3 = \''.$svcCd.'\'
				AND		seq = \''.$seq.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();

	}else{
		echo 9;
		exit;
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>
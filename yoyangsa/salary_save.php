<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];

	//대상 고형형태
	$employType[1] = false;
	$employType[2] = false;
	$employType[3] = false;
	$employType[4] = false;

	if ($_POST['chkMemGbn1'] == 'Y') $employType[1] = true; //정규직
	if ($_POST['chkMemGbn2'] == 'Y') $employType[2] = true; //계약직
	if ($_POST['chkMemGbn3'] == 'Y') $employType[3] = true; //단시간(60시간이상)
	if ($_POST['chkMemGbn4'] == 'Y') $employType[4] = true; //단시간(60시간미만)

	$salaryNot	= $_POST['memNot']; //급여 미등록 직원 포함여부

	$svcCd = $_POST['svcCd']; //선택 서비스

	if ($svcCd == '11' || $svcCd == '12'){
		$kind = '0';
	}else if ($svcCd == '21'){
		$kind = '1';
	}else if ($svcCd == '22'){
		$kind = '2';
	}else if ($svcCd == '23'){
		$kind = '3';
	}else if ($svcCd == '24'){
		$kind = '4';
	}else{
		$kind = $svcCd;
	}

	//시급
	$yymm[1]	= str_replace('-','',$_POST['txtYYMM1']); //적용년월
	$aplGbn[1]	= $_POST['optAplAmt1']; //적용구분(1:적용시급, 2:추가금액)
	$hourlyPay	= str_replace(',','',$_POST['txtAmt1_'.$aplGbn[1]]); //적용금액

	//수가별수당
	$yymm[2]	= str_replace('-','',$_POST['txtYYMM2']); //적용년월
	$aplGbn[2]	= $_POST['optAplAmt2']; //적용구분(1:적용시급, 2:추가금액)
	$extra30	= str_replace(',','',$_POST['txtAmt2_'.$aplGbn[2].'_1']); //30분
	$extra60	= str_replace(',','',$_POST['txtAmt2_'.$aplGbn[2].'_2']); //60분
	$extra90	= str_replace(',','',$_POST['txtAmt2_'.$aplGbn[2].'_3']); //90분
	$extra120	= str_replace(',','',$_POST['txtAmt2_'.$aplGbn[2].'_4']); //120분
	$extra150	= str_replace(',','',$_POST['txtAmt2_'.$aplGbn[2].'_5']); //150분
	$extra180	= str_replace(',','',$_POST['txtAmt2_'.$aplGbn[2].'_6']); //180분
	$extra210	= str_replace(',','',$_POST['txtAmt2_'.$aplGbn[2].'_7']); //210분
	$extra240	= str_replace(',','',$_POST['txtAmt2_'.$aplGbn[2].'_8']); //240분

	//총액비율
	$yymm[4]	= str_replace('-','',$_POST['txtYYMM3']); //적용년월
	$aplGbn[4]	= $_POST['optAplAmt3']; //적용구분(1:적용시급, 2:추가금액)
	$rateVal	= IntVal($_POST['txtAmt3_'.$aplGbn[4].'_1']) + (IntVal($_POST['txtAmt3_'.$aplGbn[4].'_2'].(StrLen($_POST['txtAmt3_'.$aplGbn[4].'_2']) == 1 ? '0' : '')) * 0.01);

	//일당
	$yymm[6]	= str_replace('-','',$_POST['txtYYMM4']); //적용년월
	$aplGbn[6]	= $_POST['optAplAmt4']; //적용구분(1:적용시급, 2:추가금액)
	$daliy1		= str_replace(',','',$_POST['txtAmt4_'.$aplGbn[6].'_1']); //단태아
	$daliy2		= str_replace(',','',$_POST['txtAmt4_'.$aplGbn[6].'_2']); //쌍태아
	$daliy3		= str_replace(',','',$_POST['txtAmt4_'.$aplGbn[6].'_3']); //삼태아

	//고정임금
	$yymm[3]	= str_replace('-','',$_POST['txtYYMM5']); //적용년월
	$aplGbn[3]	= $_POST['optAplAmt5']; //적용구분(1:적용시급, 2:추가금액)
	$fixedPay	= str_replace(',','',$_POST['txtAmt5_'.$aplGbn[3]]); //적용금액
	$fixedExtra	= $_POST['chkExtraPay']; //목욕간호 수당 포함여부

	//공단비율
	$yymm[7] = str_replace('-','',$_POST['txtYYMM6']); //적용년월

	if ($svcCd == '11' || $svcCd == '12' || $svcCd == '24'){
		if ($fixedExtra != 'Y') $fixedExtra = 'N';
	}else{
		$fixedExtra = 'N';
	}

	//대상직원
	foreach($yymm as $ym){
		if (!$ym) continue;

		$sql = 'SELECT	jumin
				,		seq
				,		join_dt
				,		IFNULL(quit_dt,\'9999-12-31\') AS quit_dt
				,		employ_type
				FROM	mem_his
				WHERE	org_no		= \''.$orgNo.'\'
				AND		employ_stat = \'1\'
				/*AND		LEFT(REPLACE(join_dt,\'-\',\'\'),6) <= \''.$ym.'\'
				AND		LEFT(REPlACE(IFNULL(quit_dt,\'9999-12-31\'),\'-\',\'\'),6) >= \''.$ym.'\'*/
				ORDER	BY jumin, join_dt, quit_dt';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($employType[$row['employ_type']]){
				$IsDuplicate = false;

				if (is_array($mem)){
					foreach($mem as $m){
						if ($m['jumin'] == $row['jumin']){
							$IsDuplicate = true;
							break;
						}
					}
				}

				if (!$IsDuplicate){
					$mem[] = Array(
						'jumin'	=>$row['jumin']
					,	'seq'	=>$row['seq']
					,	'joinDt'=>$row['join_dt']
					,	'quitDt'=>$row['quit_dt']
					,	'type'	=>$row['employ_type']
					);
				}
			}
		}

		$conn->row_free();
	}

	if (is_array($mem)){
		foreach($mem as $m){
			//현재 등록되 직원
			$sql = 'SELECT	mh_seq AS seq, mh_type AS gbn, mh_hourly,mh_vary_hourly_1,mh_vary_hourly_2,mh_vary_hourly_3,mh_vary_hourly_4,mh_vary_hourly_5,mh_vary_hourly_6,mh_vary_hourly_7,mh_vary_hourly_8,mh_hourly_rate,mh_fixed_pay,mh_daily_pay1,mh_daily_pay2,mh_daily_pay3,mh_from_dt,mh_to_dt
					FROM	mem_hourly
					WHERE	org_no	= \''.$orgNo.'\'
					AND		mh_svc	= \''.$svcCd.'\'
					AND		mh_jumin= \''.$m['jumin'].'\'
					AND		del_flag= \'N\'
					ORDER	BY mh_from_dt DESC, mh_to_dt DESC
					LIMIT	1';

			$hourly = $conn->get_array($sql);

			$sql = 'SELECT	MAX(mh_seq)
					FROM	mem_hourly
					WHERE	org_no	= \''.$orgNo.'\'
					AND		mh_svc	= \''.$svcCd.'\'
					AND		mh_jumin= \''.$m['jumin'].'\'';

			$hourly['maxSeq'] = $conn->get_data($sql);

			/*
				$hourly['gbn']
				1 : 시급
				2 : 수가별수당
				3 : 고정급
				4 : 총액비율
				6 : 일당
				7 : 공단비율
			 */

			foreach($yymm as $gbn => $ym){
				if (($ym && $salaryNot == 'Y') || ($ym && $gbn == $hourly['gbn'])){
					$column['mh_extra_yn'] = 'N';

					if ($gbn == 1){
						//시급
						if ($aplGbn[$gbn] == '1'){
							$column['mh_hourly'] = $hourlyPay;
						}else{
							$column['mh_hourly'] = $hourly['mh_hourly'] + $hourlyPay;
						}
					}else if ($gbn == 2){
						//수가별수당
						if ($aplGbn[$gbn] == '1'){
							$column['mh_vary_hourly_1'] = $extra30;
							$column['mh_vary_hourly_2'] = $extra60;
							$column['mh_vary_hourly_3'] = $extra90;
							$column['mh_vary_hourly_4'] = $extra120;
							$column['mh_vary_hourly_5'] = $extra150;
							$column['mh_vary_hourly_6'] = $extra180;
							$column['mh_vary_hourly_7'] = $extra210;
							$column['mh_vary_hourly_8'] = $extra240;
						}else{
							$column['mh_vary_hourly_1'] = $hourly['mh_vary_hourly_1'] + $extra30;
							$column['mh_vary_hourly_2'] = $hourly['mh_vary_hourly_2'] + $extra60;
							$column['mh_vary_hourly_3'] = $hourly['mh_vary_hourly_3'] + $extra90;
							$column['mh_vary_hourly_4'] = $hourly['mh_vary_hourly_4'] + $extra120;
							$column['mh_vary_hourly_5'] = $hourly['mh_vary_hourly_5'] + $extra150;
							$column['mh_vary_hourly_6'] = $hourly['mh_vary_hourly_6'] + $extra180;
							$column['mh_vary_hourly_7'] = $hourly['mh_vary_hourly_7'] + $extra210;
							$column['mh_vary_hourly_8'] = $hourly['mh_vary_hourly_8'] + $extra240;
						}
					}else if ($gbn == 3){
						//고정급
						if ($aplGbn[$gbn] == '1'){
							$column['mh_fixed_pay'] = $fixedPay;
						}else{
							$column['mh_fixed_pay'] = $hourly['mh_fixed_pay'] + $fixedPay;
						}
						$column['mh_extra_yn'] = $fixedExtra;
					}else if ($gbn == 4){
						//총액비율
						if ($aplGbn[$gbn] == '1'){
							$column['mh_hourly_rate'] = $rateVal;
						}else{
							$column['mh_hourly_rate'] = $hourly['mh_hourly_rate'] + $rateVal;
						}
					}else if ($gbn == 6){
						//일당
						if ($aplGbn[$gbn] == '1'){
							$column['mh_daily_pay1'] = $daliy1;
							$column['mh_daily_pay2'] = $daliy2;
							$column['mh_daily_pay3'] = $daliy3;
						}else{
							$column['mh_daily_pay1'] = $hourly['mh_daily_pay1'] + $daliy1;
							$column['mh_daily_pay2'] = $hourly['mh_daily_pay2'] + $daliy2;
							$column['mh_daily_pay3'] = $hourly['mh_daily_pay3'] + $daliy3;
						}
					}

					if ($hourly['mh_from_dt'] == $ym && $hourly['mh_to_dt'] == '999912'){
						$column['update_id'] = $_SESSION['userCode'];
						$column['update_dt'] = Date('Y-m-d');

						$sql = '';

						foreach($column as $colNm => $colVal){
							$sql .= ($sql ? ',' : '').$colNm.' = \''.$colVal.'\'';
						}

						$sql = 'UPDATE	mem_hourly
								SET		'.$sql.'
								WHERE	org_no	= \''.$orgNo.'\'
								AND		mh_jumin= \''.$m['jumin'].'\'
								AND		mh_svc	= \''.$svcCd.'\'
								AND		mh_seq	= \''.$hourly['seq'].'\'';

						$query[] = $sql;

					}else{
						$toYm = $myF->dateAdd('month',-1,SubStr($ym,0,4).'-'.SubStr($ym,4,2).'-01','Ym');

						$sql = 'UPDATE	mem_hourly
								SET		mh_to_dt	= \''.$toYm.'\'
								,		update_id	= \''.$_SESSION['userCode'].'\'
								,		update_dt	= NOW()
								WHERE	org_no	= \''.$orgNo.'\'
								AND		mh_jumin= \''.$m['jumin'].'\'
								AND		mh_svc	= \''.$svcCd.'\'
								AND		mh_seq	= \''.$hourly['seq'].'\'';

						$query[] = $sql;

						$column['org_no']	= $orgNo;
						$column['mh_kind']	= $kind;
						$column['mh_jumin'] = $m['jumin'];
						$column['mh_svc']	= $svcCd;
						$column['mh_seq']	= $hourly['maxSeq'] + 1;
						$column['mh_type']	= $gbn;

						$column['mh_from_dt']= $ym;
						$column['mh_to_dt'] = '999912';

						$column['insert_id'] = $_SESSION['userCode'];
						$column['insert_dt'] = Date('Y-m-d');

						$sl1 = '';
						$sl2 = '';

						foreach($column as $colNm => $colVal){
							$sl1 .= ($sl1 ? ',' : '').$colNm;
							$sl2 .= ($sl2 ? ',' : '').'\''.$colVal.'\'';
						}

						$query[] = 'INSERT INTO mem_hourly ('.$sl1.') VALUES ('.$sl2.')';

						Unset($column);
					}
				}
			}
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
<?
	@include_once('../inc/_db_open.php');

	class mySuga{
		var $conn;

		function mySuga($db){
			$this->conn = $db;
		}

		function findSugaCare($code, $svcKind, $date, $fromTime, $toTime, $ynFamily = 'N', $bathKind = '', $addPayYn = true, $lvl = 0){
			//return $this->findSugaCareSub($code, $svcKind, $date, $fromTime, $toTime, $ynFamily, $bathKind);
			$date = str_replace('.', '', $date);
			$date = str_replace('-', '', $date);

			if ($svcKind == '200'){
				if (SubStr($date,0,6) >= '201301'){
					$liNewType = 1;
				}else{
					$liNewType = 0;
				}
				//$liNewType = 0;

				$lsFrom   = str_replace(':', '', $fromTime);
				$tmpFrom[0] = substr($lsFrom, 0, 2);
				$tmpFrom[1] = substr($lsFrom, 2, 2);
				$liFrom = intval($tmpFrom[0]) * 60 + intval($tmpFrom[1]);

				$lsTo   = str_replace(':', '', $toTime);
				$tmpTo[0] = substr($lsTo, 0, 2);
				$tmpTo[1] = substr($lsTo, 2, 2);
				$liTo   = intval($tmpTo[0]) * 60 + intval($tmpTo[1]);

				if ($liTo < $liFrom) $liTo += 24 * 60;

				$liProcTime = $liTo - $liFrom;
				$liProc = $liProcTime;

				if ($liProc < 30) return;

				$msg .= $lvl.'/'.$liProc;

				//510분에서 540분으로 수정
				if (SubStr($date,0,6) >= '201703'){
					if ($lvl == 1 || $lvl == 2){
						$liLimitTime = 540;
					}else{
						if ($liProc >= 270 && $liProc <= 540){
							$liLimitTime = 540;
						}else{
							$liLimitTime = 180;
						}
					}
				}else if (SubStr($date,0,6) >= '201301'){
					$liLimitTime = 540;
				}else{
					$liLimitTime = 510;
				}

				if ($liProcTime > $liLimitTime){
					$liProcTime = $liLimitTime;
				}

				if ($liProcTime > 240){ //270
					$mode = 1;
				}else{
					$mode = 2;
				}
			}else{
				$mode = 2;
			}

			if ($mode == 1){
				$i = 0;

				while(true){
					if ($liProcTime < 30){
						//탈출 조건
						break;
					}

					if ($liNewType == 1){
						if ($i == 0){
							if (SubStr($date,0,6) >= '201603'){
								if ($liProc >= 480){
									$liTo = $liFrom + 240;
									$liProcTime -= 240;
								}else if ($liProc >= 270){
									$liTo = $liFrom + 270;
									$liProcTime -= 270;
								}else{
									$liTo = $liFrom + $liProc;
									$liProcTime -= $liProc;
								}
							}else{
								if ($liProc >= 270){
									$liTo = $liFrom + 270;
									$liProcTime -= 270;
								}else{
									$liTo = $liFrom + $liProc;
									$liProcTime -= $liProc;
								}
							}
						}else{
							$liFrom = $liTo;
							$liTo   = $liFrom + $liProcTime;
							$liProcTime = 0;
						}
					}else{
						if ($i == 0){
							$liTo = $liFrom + 240;
							$liProcTime -= 240;
						}else{
							$liFrom = $liTo + 30;
							$liTo   = $liFrom + $liProcTime - 30;
							$liProcTime = 0;
						}
					}

					if ($liTo > 24 * 60){
						$liTo -= (24 * 60);
					}


					$liFH = Floor($liFrom / 60);
					$liFM = $liFrom % 60;
					$liTH = Floor($liTo / 60);
					$liTM = $liTo % 60;

					$lsFrom = ($liFH < 10 ? '0' : '').$liFH.':'.($liFM < 10 ? '0' : '').$liFM;
					$lsTo   = ($liTH < 10 ? '0' : '').$liTH.':'.($liTM < 10 ? '0' : '').$liTM;

					$tmpSuga[$i] = $this->findSugaCareSub($code, $svcKind, $date, $lsFrom, $lsTo, $ynFamily, $bathKind, 1, $addPayYn, $lvl);

					//$msg .= '<br>'.$lsFrom.'/'.$lsTo.'/'.$tmpSuga[$i]['cost'];

					$i ++;
				}

				if (Is_Array($tmpSuga)){
					foreach($tmpSuga as $i => $row){
						if ($i == 0){
							if ($row['ynHoliday'] == 'Y'){
								if ($liProc >= 270){
									$lsSugaCd = 'CCHS9';
									$lsSugaNm = '요양/270분이상(30%)';
								}else{
									$lsSugaCd = 'CCHS8';
									$lsSugaNm = '요양/240분(30%)';
								}
							}else{
								if ($liProc >= 270){
									$lsSugaCd = 'CCWS9';
									$lsSugaNm = '요양/270분이상';
								}else{
									$lsSugaCd = 'CCWS8';
									$lsSugaNm = '요양/240분';
								}
							}

							$suga = array('code'	   =>$lsSugaCd
										 ,'name'	   =>$lsSugaNm
										 ,'cost'	   =>$row['cost']
										 ,'costEvening'=>$row['costEvening']
										 ,'costNight'  =>$row['costNight']
										 ,'costTotal'  =>$row['costTotal']
										 ,'sudangPay'  =>$row['sudangPay']
										 ,'timeEvening'=>$row['timeEvening']
										 ,'timeNight'  =>$row['timeNight']
										 ,'ynEvening'  =>$row['ynEvening']
										 ,'ynNight'	   =>$row['ynNight']
										 ,'ynHoliday'  =>$row['ynHoliday']
										 ,'costBipay'  =>$row['costBipay']
										 ,'costHoliday'=>$row['costHoliday']
										 ,'procTime'   =>$liProc //$row['procTime']
										 ,'msg'        =>$row['msg']);
						}else{
							$suga['cost']        += $row['cost'];
							$suga['costEvening'] += $row['costEvening'];
							$suga['costNight']   += $row['costNight'];
							$suga['costTotal']   += $row['costTotal'];
							$suga['sudangPay']   += $row['sudangPay'];
							$suga['timeEvening'] += $row['timeEvening'];
							$suga['timeNight']   += $row['timeNight'];
							$suga['costBipay']   += $row['costBipay'];
							$suga['costHoliday'] += $row['costHoliday'];
							$suga['msg']         .= $row['msg'];
						}

						//$msg .= '<br>'.$suga['timeEvening'].'/'.$row['costTotal'];
					}

					$suga['costTotal'] = Round($suga['costTotal'] / 10) * 10;
				}

				$suga['msg'] .= $msg;

				return $suga;
			}else{
				if ($svcKind == '210'){
					return $this->findSugaDmta($code, $svcKind, $date, $fromTime, $toTime);
				}else{
					return $this->findSugaCareSub($code, $svcKind, $date, $fromTime, $toTime, $ynFamily, $bathKind, 0, $addPayYn, $lvl);
				}
			}
		}

		//재가요양 수가
		function findSugaCareSub($code, $svcKind, $date, $fromTime, $toTime, $ynFamily = 'N', $bathKind = '', $roundPos = 0, $addPayYn = true, $lvl = ''){
			$lsSvcKind = $svcKind;

			if (SubStr($date,0,6) >= '201301'){
				$liNewType = 1;
			}else{
				$liNewType = 0;
			}
			//$liNewType = 0;

			// 입력시간
			$lsFrom   = str_replace(':', '', $fromTime);
			$tmpFrom[0] = substr($lsFrom, 0, 2);
			$tmpFrom[1] = substr($lsFrom, 2, 2);
			$liFrom = intval($tmpFrom[0]) * 60 + intval($tmpFrom[1]);

			$lsTo   = str_replace(':', '', $toTime);
			$tmpTo[0] = substr($lsTo, 0, 2);
			$tmpTo[1] = substr($lsTo, 2, 2);
			$liTo   = intval($tmpTo[0]) * 60 + intval($tmpTo[1]);

			if ($liTo < $liFrom) $liTo += 24 * 60;

			$liProcTime = $liTo - $liFrom;

			if ($svcKind == '200' || $svcKind == '500'){
				$liProcKind = floor($liProcTime - ($liProcTime % 30));
			}else{
				$liProcKind = $liProcTime;
			}

			// 요일
			$lsDt = $date;
			$lsDt = str_replace('.', '', $lsDt);
			$lsDt = str_replace('-', '', $lsDt);

			$ynHoliday = 'N';

			if (strlen($lsDt) == 8){
				$tmpDt[0] = substr($lsDt, 0, 4);
				$tmpDt[1] = substr($lsDt, 4, 2);
				$tmpDt[2] = substr($lsDt, 6, 2);
				$liWeekday = date('w', strtotime($tmpDt[0].'-'.$tmpDt[1].'-'.$tmpDt[2]));

				// 휴일여부
				if ($liWeekday == 0) $ynHoliday = 'Y';
				if ($ynHoliday != 'Y'){
					$sql = 'select count(*)
							  from tbl_holiday
							 where mdate = \''.$lsDt.'\'';

					if ($this->conn->get_data($sql) > 0){
						$ynHoliday = 'Y';
					}
				}
			}

			// 구분 시간값등을 확인
			$TN  = $liProcKind;
			$ETN = 0;
			$NTN = 0;
			$ETNtime = 0; //야간시간
			$NTNtime = 0; //심야시간
			$ERang1 = 18 * 60;
			$ERang2 = 21 * 60 + 59;
			$NRang1 = 22 * 60;
			$NRang2 = 24 * 60 + 3 * 60 + 59;
			$NRang3 = 3 * 60 + 59;
			$Egubun  = 'N'; //야간여부
			$Ngubun  = 'N'; //심야여부

			$EAMT  = 0;
			$NAMT  = 0;
			$TAMT  = 0;
			$EFrom = 0;
			$ETo   = 0;
			$NFrom = 0;
			$NTo   = 0;

			/*********************************************************
				종료시간을 시작시간에서 진행시간을 더한 값으로 변경한다.
			*********************************************************/
			//if ($lsSvcKind == '200') $liTo = $liFrom + $TN;

			if (intval($liTo) - intval($liFrom) > 8.5 * 60)
				$liTo = intval($liFrom) + 8.5 * 60;

			$EFrom = $liFrom - $ERang1;

			if ($lsSvcKind == '200'){
				//연장 및 심야 종료 및 시작시간
				if ($ERang1 < $liFrom){
					if ($liNewType == 1){
					}else{
						if ($liFrom % 30 != 0){
							$ERang2 = $ERang2 + $liFrom % 30;
							$NRang1 = $ERang2 + 1;

							if ($liTo > $NRang1 && ($liTo - $NRang1) % 30 != 0){
								$liTo = $liTo - ($liTo - $NRang1) % 30;
							}else if ($liTo > $ERang2 && $liTo < $ERang2 && ($liTo - $ERang2) % 30){
								$liTo = $liTo - ($liTo - $ERang2) % 30;
							}
						}
					}
				}
			}

			/*********************************************************
				근무시간이 510분이상 넘어갈 경우 510분까지만 인정한다.
			*********************************************************/
			if (intval($liTo) - intval($liFrom) >= 8.5 * 60){ //510분초과시 510분 인정
				$ETo = (intval($liFrom) + 8.5 * 60) - $ERang1;

			}else if (intval($liTo) - intval($liFrom) >= 4.5 * 60){ //270분초과시 30분 빼기
				//$ETo = (intval($liFrom) + (intval($liTo) - intval($liFrom) /*- 30*/)) - $ERang1;
				$liCutMin = 0;

				if ($liNewType == 1){
				}else{
					if ($liTo > $ERang1){
						if ($liTo - $liFrom <= 270){
							$liCutMin = 30;
						}
					}
				}

				$ETo = (intval($liFrom) + (intval($liTo) - intval($liFrom) - $liCutMin)) - $ERang1;

			}else if (intval($liTo) - intval($liFrom) >= 4 * 60 && intval($liTo) - intval($liFrom) <= 4.5 * 60){ //240분~270분시 240분적용
				if ($liNewType == 1){
					$ETo = $liTo - $ERang1;
				}else{
					$ETo = (intval($liFrom) + 4 * 60) - $ERang1;
				}

			}else{
				$ETo = $liTo - $ERang1;
			}

			if ($lsSvcKind == '200'){
				// 요양 중 동거가 아닐경우만 야간및 심야 할증을 실행한다.
				if ($ynFamily != 'Y'){
					if ($liNewType == 1){
						if ($liFrom < $NRang3){
							$NFrom   = $NRang3 - $liFrom;
							$NTo     = $NRang3 - $liTo;
							$NTNtime = $NFrom - ($NTo < 0 ? 0 : $NTo);
						}else{
							$NFrom   = $liFrom - $NRang1;
							$NTo     = $liTo - $NRang1;
							$NTNtime = $NTo - ($NFrom < 0 ? 0 : $NFrom);
						}
					}else{
						if ($liFrom < $NRang3){
							$NFrom   = $NRang3 - $liFrom;
							$NTo     = $NRang3 - $liTo;
							$NTNtime = $NFrom - ($NTo < 0 ? 0 : $NTo) + 1;
						}else{
							$NFrom   = $liFrom - $NRang1;
							$NTo     = $liTo - $NRang1 + 1;
							$NTNtime = $NTo - ($NFrom < 0 ? 0 : $NFrom);
						}
					}

					$ETNtime = $ETo - ($EFrom < 0 ? 0 : $EFrom);

					$NTNtime = $NTNtime < 0 ? 0 : $NTNtime;

					if ($liNewType == 1){
					}else{
						$NTNtime = floor($NTNtime - ($NTNtime % 30));
					}

					$ETNtime = $ETNtime < 0 ? 0 : $ETNtime - $NTNtime;

					if ($NTNtime > 480) $NTNtime = 480;
					if ($ETNtime > 480) $ETNtime = 480;

					//새벽 6시 이전에 근무한 시간을 야간으로 적용한다.
					if ($liFrom < 360){
						$tmpTT = 360 - $liTo;

						if ($tmpTT < 0) $tmpTT = 0;

						$NTNtime = 360 - $liFrom - $tmpTT;
					}

					if($lvl == 5){
						$NTNtime = 0;
					}

				}else{
					$NTNtime = 0;
					$ETNtime = 0;
				}
			}else if ($lsSvcKind == '800'){
				if ($liFrom < $NRang3){
					$NFrom   = $NRang3 - $liFrom;
					$NTo     = $NRang3 - $liTo;
					$NTNtime = $NFrom - ($NTo < 0 ? 0 : $NTo) + 1;
				}else{
					$NFrom   = $liFrom - $NRang1;
					$NTo     = $liTo - $NRang1 + 1;
					$NTNtime = $NTo - ($NFrom < 0 ? 0 : $NFrom);
				}

				//1분제거
				$NTNtime = $NTNtime - 1;

				$ETNtime = $ETo - ($EFrom < 0 ? 0 : $EFrom);

				$NTNtime = $NTNtime < 0 ? 0 : $NTNtime;
				$ETNtime = $ETNtime < 0 ? 0 : $ETNtime - $NTNtime;

				if ($NTNtime > 480) $NTNtime = 480;
				if ($ETNtime > 480) $ETNtime = 480;

				//새벽 6시 이전에 근무한 시간을 야간으로 적용한다.
				if ($liFrom < 360){
					$tmpTT = 360 - $liTo;

					if ($tmpTT < 0) $tmpTT = 0;

					$NTNtime = 360 - $liFrom - $tmpTT;
				}

				#//심야시 연장 심야를 무시한다.
				#if ($NTNtime > 1){
				#	$NTNtime = 0;
				#	$ETNtime = 0;
				#}

				//60분이상은 60분까지만 인정한다.
				if ($NTNtime > 60){
					$NTNtime = 60;
				}
			}else{
				// 목욕은 할증을 실행하자 않는다.
				$NTNtime = 0;
				$ETNtime = 0;
			}

			if ($lsSvcKind == '200'){
				if ($liNewType == 1){
					$ETN_time = $ETNtime;
					$NTN_time = $NTNtime;
				}

				//방문요양은 30분단위로 절사를 실행한다.
				$NTNtime = floor($NTNtime - ($NTNtime % 30));
				$ETNtime = floor($ETNtime - ($ETNtime % 30));
			}

			if ($lsSvcKind == '200'){
				$TN = floor($TN - ($TN % 30));

				if ($liNewType == 1){
					$PRC_time = $liProcTime; //총근무시간
					//$PRC_time = $TN; //수가시간
				}

				switch($TN){
					case 30 : $TN = 1; break;
					case 60 : $TN = 2; break;
					case 90 : $TN = 3; break;
					case 120: $TN = 4; break;
					case 150: $TN = 5; break;
					case 180: $TN = 6; break;
					case 210: $TN = 7; break;
					case 240: $TN = 8; break;
					default : $TN = 9; break;
				}
			}else if ($lsSvcKind == '800'){
				if ($TN < 30){
					$TN = 1;
				}else if ($TN < 60){
					$TN = 2;
				}else{
					$TN = 3;
				}
			}else{
				if ($bathKind == '1' || $bathKind == '2')
					$TN = 'K';
				else
					$TN = 'F';
			}

			switch($ETNtime){
				case 30 : $ETN = 1; break;
				case 60 : $ETN = 2; break;
				case 90 : $ETN = 3; break;
				case 120: $ETN = 4; break;
				case 150: $ETN = 5; break;
				case 180: $ETN = 6; break;
				case 210: $ETN = 7; break;
				case 240: $ETN = 8; break;
				default : $ETN = 0;
			}

			switch($NTNtime){
				case 30 : $NTN = 1; break;
				case 60 : $NTN = 2; break;
				case 90 : $NTN = 3; break;
				case 120: $NTN = 4; break;
				case 150: $NTN = 5; break;
				case 180: $NTN = 6; break;
				case 210: $NTN = 7; break;
				case 240: $NTN = 8; break;
				default : $NTN = 0;
			}

			$cutProcTime = Floor($liProcTime / 10) * 10;

			$sugaKey = '';
			$sugaGubun = '';

			if ($lsSvcKind == '200'){
				// 요양
				if ($ynFamily == 'Y'){
					$sugaGubun = 'CCWC';
				}else if ($ynHoliday == 'Y'){
					$sugaGubun = 'CCHS';
				}else{
					$sugaGubun = 'CCWS';
				}
			}else if ($lsSvcKind == '500'){
				// 목욕
				$sugaGubun = 'CB';
			}else{
				// 간호
				if ($ynHoliday != 'Y'){
					$sugaGubun = 'CNW';
				}else{
					$sugaGubun = 'CNH';
				}

				$sugaGubun .= 'S';
			}

			$sugaKey = $sugaGubun.$TN;

			if ($lsSvcKind == '500'){
				$sugaKey .= 'D';

				if (intval($bathKind) % 2 == 1)
					$sugaKey .= '1';
				else
					$sugaKey .= '2';
			}

			if ($code == '1234'){
				if (SubStr($lsDt, 0, 6) >= '201801'){
					$sql = 'select m01_suga_cont as name
							,      m01_suga_value as cost
							  from m01suga
							 where m01_mcode  = \'goodeos\'
							   and m01_mcode2 = \''.$sugaKey.'\'
							   and left(m01_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
							   and left(m01_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'
							 union all
							select m11_suga_cont as name
							,      m11_suga_value as cost
							  from m11suga
							 where m11_mcode  = \'goodeos\'
							   and m11_mcode2 = \''.$sugaKey.'\'
							   and left(m11_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
							   and left(m11_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'';
				}else{
					$sql = 'select m01_suga_cont as name
							,      m01_suga_value as cost
							  from m01suga
							 where m01_mcode  = \'goodeos\'
							   and m01_mcode2 = \''.$sugaKey.'\'
							   and left(m01_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
							   and left(m01_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'
							 union all
							select m11_suga_cont as name
							,      m11_suga_value as cost
							  from m11suga
							 where m11_mcode  = \'goodeos\'
							   and m11_mcode2 = \''.$sugaKey.'\'
							   and left(m11_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
							   and left(m11_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'';
				}
			}else{
				$sql = 'select m01_suga_cont as name
						,      m01_suga_value as cost
						  from m01suga
						 where m01_mcode  = \'goodeos\'
						   and m01_mcode2 = \''.$sugaKey.'\'
						   and left(m01_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
						   and left(m01_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'
						 union all
						select m11_suga_cont as name
						,      m11_suga_value as cost
						  from m11suga
						 where m11_mcode  = \'goodeos\'
						   and m11_mcode2 = \''.$sugaKey.'\'
						   and left(m11_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
						   and left(m11_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'';
			}

			$tmp = $this->conn->get_array($sql);

			$sugaName  = $tmp['name']; //명칭
			$sugaPrice = $tmp['cost']; //단가

			if ($ynHoliday == 'Y'){
				$sugaHoliday = $sugaPrice;
			}else{
				$sugaHoliday = $sugaPrice + $sugaPrice * 0.3;
			}

			if ($liNewType == 1){
				if ($lsSvcKind == '200'){
					if ($TN > 8) $TN = 8;
				}
			}

			if ($lsSvcKind != '200'){
				$sql = 'select m21_svalue
						  from m21sudang
						 where m21_mcode  = \''.$code.'\'
						   and m21_mcode2 = \''.$sugaKey.'\'';

				$sudangPrice = $this->conn->get_data($sql);
			}else{
				$sudangPrice = 0;
			}

			// 2011년 7월 1일부터 목욕 적용수가를 시간기준으로 변경한다.(2011.07.11 적용)
			if (substr($lsDt,0,6) >= '201107'){
				if ($lsSvcKind == '500'){
					$tmpTime = $liTo - $liFrom;

					if ($tmpTime < 40){
						$sugaPrice = 0;
						$sugaHoliday = 0;
					}else if ($tmpTime >= 40 && $tmpTime < 60){
						$sugaPrice = $sugaPrice * 80 / 100;
						//$sugaPrice = floor($sugaPrice - ($sugaPrice % 10));
						$sugaPrice = Round($sugaPrice,-1);
						$sugaHoliday = $sugaPrice * 1.3;
					}
				}
			}

			#var tempValue = new Array();
			#var tempTime  = new Array();
			$tempIndex = 0;

			if ($TN == 9){
				// 270분 이상일 경우 수가를 계산
				$tempFmH = intval($tmpFrom[0]);
				$tempFmM = intval($tmpFrom[1]);
				$tempToH = intval($tmpTo[0]);
				$tempToM = intval($tmpTo[1]);

				if ($tempFmH > $tempToH) $tempToH = $tempToH + 24;
				$tempFmH = $tempFmH * 60 + $tempFmM;
				$tempToH = $tempToH * 60 + $tempToM - $tempFmH;

				/*********************************************************
					최대 8시간 30분까지만 허용한다.
				*********************************************************/
				if ($tempToH > 8.5 * 60) $tempToH = 8.5 * 60;

				$tempL = floor($tempToH - ($tempToH % 30)) / 30;
				$tempK = 0;
				$temp_first = false;

				$sugaPrice = 0;
				$sugaHoliday = 0;

				while(1){
					if ($tempL >= 8){
						$tempK = 8;
					}else if ($tempL == 0 || $tempK == 0){
						break;
					}else{
						$tempK = $tempL % 8;
					}
					$tempL = $tempL - $tempK;

					if (!$temp_first){
						$tempL = $tempL - 1; // 4시간후 30분을 뺀다.
						$temp_first = true;

						if ($tempFmH + ($tempK * 30) >= 1320 ||
							$tempFmH + ($tempK * 30) <  360){

							//심야
							if ($NTNtime > 0) $NTNtime -= 30;
						}else if ($tempFmH + ($tempK * 30) >= 1080){
							//야간
							if ($ETNtime > 0) $ETNtime -= 30;
						}else{
							//주간
						}
					}

					$sql = 'select m01_suga_value as cost
							  from m01suga
							 where m01_mcode  = \'goodeos\'
							   and m01_mcode2 = \''.$sugaGubun.$tempK.'\'
							   and left(m01_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
							   and left(m01_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'
							 union all
							select m11_suga_value as cost
							  from m11suga
							 where m11_mcode  = \'goodeos\'
							   and m11_mcode2 = \''.$sugaGubun.$tempK.'\'
							   and left(m11_sdate,'.strlen($lsDt).') <= \''.$lsDt.'\'
							   and left(m11_edate,'.strlen($lsDt).') >= \''.$lsDt.'\'';

					$tempValue[$tempIndex] = $this->conn->get_data($sql);
					$tempTime[$tempIndex]  = $tempK;

					$sugaPrice += $tempValue[$tempIndex]; //단가
					$sugaHoliday += ($tempValue[$tempIndex] + $tempValue[$tempIndex] * 0.3);
					$tempIndex ++;
				}
			}

			$temp_e = 0;
			$i = 0;

			if ($ynHoliday != 'Y'){
				if ($NTNtime > 0 || ($liNewType == 1 && $NTN_time > 0)){
					if ($sugaGubun != 'HS' && $sugaGubun != 'HD'){
						if ($liNewType == 0 && $TN == 9){
							$temp_e = $NTNtime / 30;
							$liMax = sizeof($tempValue) - 1;
							$i = 0;

							$NAMT = 0;

							while(1){
								if ($i > $liMax) break;
								if ($temp_e <= 0) break;

								if ($tempTime[$i] >= $temp_e){
									$NAMT += round(($tempValue[$i] / $tempTime[$i] * $temp_e * 0.3),1);
									break;
								}else{
									$NAMT += round($tempValue[$i] * 0.3,1);
									$temp_e -= $tempTime[$i];
								}

								$i ++;
							}
						}else{
							if ($lsSvcKind == '200'){
								if ($liNewType == 1){
									//$NAMT = round(($sugaPrice * ($NTN_time / $PRC_time)) * 0.3,1);
									$NAMT = Round($sugaPrice * 0.3 * $NTN_time / $PRC_time, 2);
								}else{
									$NAMT = round(($sugaPrice * ($NTN / $TN)) * 0.3,1);
								}
							}else if ($lsSvcKind == '800'){
								//방문간호 연장 할증 금액
								$NAMT = Round($sugaPrice / $liProcTime * 0.3 * $NTNtime, 1);
							}
						}

						#$NAMT = round($NAMT);
					}
					$Ngubun = 'Y';
				}

				//$msg .= '<br>1 : '.$ETNtime.'/'.$sugaPrice.'/'.$ETN_time.'/'.$PRC_time;

				if ($ETNtime > 0 || ($liNewType == 1 && $ETN_time > 0)){
					if ($sugaGubun != 'HS' && $sugaGubun != 'HD'){
						#if ($lsSvcKind == '200'){
							if ($liNewType == 0 && $TN == 9){
								if ($liNewType == 1){
									//$EAMT = round(($sugaPrice * ($ETN_time / $PRC_time)) * 0.2,1);
									$EAMT = Round($sugaPrice * 0.2 * $ETN_time / $PRC_time,1);
								}else{
									$temp_e = $ETNtime / 30;

									if ($i == 0){
										$i = sizeof($tempValue) - 1;
									}else{
										if ($tempTime[$i] <= $temp_e){
											$i --;
										}

										if ($i < 0) $i = 0;
									}

									$EAMT = 0;

									while(1){
										if ($i < 0) break;
										if ($temp_e <= 0) break;

										if ($tempTime[$i] >= $temp_e){
											$EAMT += round(($tempValue[$i] / $tempTime[$i] * $temp_e * 0.2),1);
											break;
										}else{
											$EAMT += round($tempValue[$i] * 0.2,1);
											$temp_e -= $tempTime[$i];
										}

										$i--;
									}
								}
							}else{
								if ($lsSvcKind == '200'){
									//방문요양 연장 할증 금액
									if ($liNewType == 1){
										//$EAMT = round(($sugaPrice * ($ETN_time / $PRC_time)) * 0.2,1);
										$EAMT = Round($sugaPrice * 0.2 * $ETN_time / $PRC_time,2);
									}else{
										$EAMT = round(($sugaPrice * ($ETN / $TN)) * 0.2,1);
									}
								}else if ($lsSvcKind == '800'){
									//방문간호 연장 할증 금액
									$EAMT = Round($sugaPrice / $liProcTime * 0.2 * $ETNtime, 1);
								}
							}
						#}else{
						#	if ($code == '1234'){
						#		$EAMT = floor(($sugaPrice * ($ETNtime / 60)) * 0.2);
						#		$msg .= $EAMT;
						#	}
						#}

						//$EAMT = round($EAMT / 10) * 10; //반올림
						#$EAMT = round($EAMT);
					}
					$Egubun = 'Y';
				}
			}

			//$msg .= '<br>'.$sugaPrice.'/'.$ETN_time.'/'.$PRC_time;
			//	 .  '<br>'.$ETNtime.'/'.$NTNtime
			//	 .  '<br>'.$ETN.'/'.$NTN
			//	 .  '<br>'.$EAMT.'/'.$NAMT;

			if ($lsSvcKind == '800'){
				//간호 심야는 야간에 포함한다.
				$EAMT += $NAMT;
				$NAMT  = 0;
			}

			if (!$addPayYn){
				$EAMT = 0;
				$NAMT = 0;
			}

			//$TAMT = round((intval($sugaPrice) + intval($EAMT) + intval($NAMT)) / 10) * 10;
			$TAMT = intval($sugaPrice) + FloatVal($EAMT) + FloatVal($NAMT);

			if ($roundPos == 1){
				//$TAMT = Round($TAMT, 1);
				//원단위 사사오입을 실행한다.
				$TAMT = Round($TAMT, -1);
			}else{
				$TAMT = Round($TAMT / 10) * 10;
			}

			if ($svcKind == '500' || $ynFamily == 'Y'){
				$sugaHoliday = $sugaPrice;
			}else{
				$sugaHoliday = round(intval($sugaHoliday) / 10) * 10;
			}

			$liCostBipay = $this->findSugaCenter($code, $svcCd, $svcKind, $sugaKey, $date);

			if ($lsSvcKind != '500'){
				if ($sugaPrice <= 0){
					$sugaKey  = '';
					$sugaName = '';
				}
			}

			$suga = array('code'	   =>$sugaKey
						 ,'name'	   =>$sugaName
						 ,'cost'	   =>$sugaPrice
						 ,'costEvening'=>$EAMT
						 ,'costNight'  =>$NAMT
						 ,'costTotal'  =>$TAMT
						 ,'sudangPay'  =>$sudangPrice
						 ,'timeEvening'=>$ETNtime
						 ,'timeNight'  =>$NTNtime
						 ,'ynEvening'  =>$Egubun
						 ,'ynNight'	   =>$Ngubun
						 ,'ynHoliday'  =>$ynHoliday
						 ,'costBipay'  =>$liCostBipay
						 ,'costHoliday'=>$sugaHoliday
						 ,'procTime'   =>$liProcTime
						 ,'msg'        =>$msg);

			return $suga;
		}

		//치매가족 수가
		function findSugaDmta($orgNo, $svcKind, $date, $fromTime, $toTime){
			$lsFrom = str_replace(':', '', $fromTime);
			$tmpFrom[0] = substr($lsFrom, 0, 2);
			$tmpFrom[1] = substr($lsFrom, 2, 2);
			$liFrom = intval($tmpFrom[0]) * 60 + intval($tmpFrom[1]);

			$lsTo = str_replace(':', '', $toTime);
			$tmpTo[0] = substr($lsTo, 0, 2);
			$tmpTo[1] = substr($lsTo, 2, 2);
			$liTo = intval($tmpTo[0]) * 60 + intval($tmpTo[1]);

			if ($liTo <= $liFrom) $liTo += 24 * 60;

			$liProcTime = $liTo - $liFrom;
			$liProcIdx = $liProcTime / 30;
			
			if($date < '20190101'){
				if ($liProcIdx == 48){
					$sugaCode = 'DF001';
				}else if ($liProcIdx >= 32){
					$sugaCode = 'DF002';
				}else{
					return;
				}
			}else {
				$sugaCode = 'DF002';
			}

			$sql = 'SELECT	m01_suga_cont AS suga_name
					,		m01_suga_value AS suga_cost
					,		m01_suga_value15 AS add_cost
					FROM	m01suga
					WHERE	m01_mcode  = \'goodeos\'
					AND		m01_mcode2 = \''.$sugaCode.'\'
					AND		LEFT(m01_sdate, '.StrLen($date).') <= \''.$date.'\'
					AND		LEFT(m01_edate, '.StrLen($date).') >= \''.$date.'\'
					UNION	ALL
					SELECT	m11_suga_cont AS suga_name
					,		m11_suga_value AS suga_cost
					,		m11_suga_value15 AS add_cost
					FROM	m11suga
					WHERE	m11_mcode  = \'goodeos\'
					AND		m11_mcode2 = \''.$sugaCode.'\'
					AND		LEFT(m11_sdate, '.StrLen($date).') <= \''.$date.'\'
					AND		LEFT(m11_edate, '.StrLen($date).') >= \''.$date.'\'';

			//$msg .= nl2br($sql);

			$row = $this->conn->get_array($sql);
			
			if (is_array($row)){
				$sugaName = $row['suga_name'];
				$sugaPrice = $row['suga_cost'];
				$sugaAddPay = $row['add_cost'];
			}

			Unset($row);

			return array('code'=>$sugaCode, 'name'=>$sugaName, 'cost'=>$sugaPrice, 'costTotal'=>$sugaPrice+$sugaAddPay, 'addPay'=>$sugaAddPay, 'procTime'=>$liProcTime, 'msg'=>$msg);
		}

		//가사간병 수가
		function findSugaVoucher($code, $svcCd, $date, $fromTime, $toTime, $svcVal, $svcLvl){
			if ($svcCd == '1'){
				$lsSugaCd = 'VH001';
			}else if ($svcCd == '2'){
				$lsSugaCd = 'VO'.$svcVal.'01';
			}else if ($svcCd == '3'){
				$lsSugaCd = 'VM'.$svcVal.'01';
			}else if ($svcCd == '4'){
				$lsSugaCd = 'VA'.($svcVal == '1' ? 'A' : 'C').$svcLvl.'0';
			}

			$lsDt = $date;

			$lsFrom     = str_replace(':', '', $fromTime);
			$tmpFrom[0] = substr($lsFrom, 0, 2);
			$tmpFrom[1] = substr($lsFrom, 2, 2);
			$liFrom     = intval($tmpFrom[0]) * 60 + intval($tmpFrom[1]);

			$lsTo     = str_replace(':', '', $toTime);
			$tmpTo[0] = substr($lsTo, 0, 2);
			$tmpTo[1] = substr($lsTo, 2, 2);
			$liTo     = intval($tmpTo[0]) * 60 + intval($tmpTo[1]);

			if ($liTo < $liFrom) $liTo += 24 * 60;

			if ($svcCd == '1' || $svcCd == '2'){
				//$liProcTime = Round(($liTo - $liFrom) / 60 * 10) / 10;

				$tmpYm = '201402';

				if ($svcCd == '2') $tmpYm = '201502';

				if (SubStr($date,0,6) >= $tmpYm){
					/*
					 *	2014년 2월 수가계산법이 변경됨
					 */
					$liProcTime = $liTo - $liFrom;
					
					if ($lsSugaCd == 'VOD01'){
						
					}else{
						$tmpTime = $liProcTime % 60;
						$liProcTime = $liProcTime - $tmpTime;
					
						if ($tmpTime >=15 && $tmpTime <=44){
							//15분 ~ 44분까지 30분 인정
							$liProcTime += 30;
						}else if ($tmpTime >= 45){
							//45분 이상 60분 인정
							$liProcTime += 60;
						}else{
							//15분이하는 인정하지 않음
						}
					}

					$liProcTime = $liProcTime - ($liProcTime % 30);
					$liProcTime = Round($liProcTime / 60 * 10) / 10;
					$lsProcTime = $liProcTime;
					
				}else{
					$liProcTime = Round(($liTo - $liFrom) / 60);
					$lsProcTime = $liProcTime;
				}
			}else if ($svcCd == '3'){
				$liProcTime = 1;
				$lsProcTime = Round(($liTo - $liFrom) / 60);
			}else{
				$liProcTime = Round(($liTo - $liFrom) / 60);
				$lsProcTime = $liProcTime;
			}

			//서비스단가
			$sql = 'select service_code as cd
					,      service_gbn as nm
					,      service_cost as cost
					,      service_cost_night as cost_night
					,      service_cost_holiday as cost_holiday
					,      service_bipay as cost_bipay
					,      DATE_FORMAT(service_from_dt,\'%Y%m%d\') as from_dt
					,      DATE_FORMAT(service_to_dt,\'%Y%m%d\') as to_dt
					  from suga_service
					 where org_no       = \'goodeos\'
					   and service_kind = \''.$svcCd.'\'';

			$laSvcSuga = $this->conn->_fetch_array($sql);

			if (is_array($laSvcSuga)){
				/*
				foreach($laSvcSuga as $svc){
					if ($svc['cd'] == $lsSugaCd && $svc['from_dt'] <= $lsDt && $svc['to_dt'] >= $lsDt){
						$lsSugaNm      = $svc['nm'];
						$liSugaCost    = $svc['cost'];
						$liCostNight   = $svc['cost_night'];
						$liCostHoliday = $svc['cost_holiday'];
						$liCostBipay   = $svc['cost_bipay'];
						$msg .= '<br>'.$lsSugaCd.'/'.$lsDt.'/'.$svc['from_dt'].'/'.$svc['to_dt'];
						break;
					}
				}
				*/
				$liCnt = SizeOf($laSvcSuga);

				for($i=0; $i<$liCnt; $i++){
					if ($laSvcSuga[$i]['cd'] == $lsSugaCd &&
						SubStr($laSvcSuga[$i]['from_dt'],0,StrLen($lsDt)) <= $lsDt &&
						SubStr($laSvcSuga[$i]['to_dt'],0,StrLen($lsDt)) >= $lsDt){
						if ($svcCd == '2'){
							if ($lsSugaCd == 'VOD01'){
								$laSvcSuga[$i]['cost'] = $laSvcSuga[$i]['cost'] / 3;
							}
						}

						$lsSugaNm      = $laSvcSuga[$i]['nm'];
						$liSugaCost    = $laSvcSuga[$i]['cost'];
						$liCostNight   = $laSvcSuga[$i]['cost_night'];
						$liCostHoliday = $laSvcSuga[$i]['cost_holiday'];
						$liCostBipay   = $laSvcSuga[$i]['cost_bipay'];
						break;
					}
				}
			}

			if ($liCostHoliday == 0) $liCostHoliday = $liSugaCost;

			$liSugaTot    = $liSugaCost * $liProcTime;
			$liHolidayTot = $liCostHoliday * $liProcTime;

			if ($svcCd == '2'){
				//노인돌봄
				if ($lsSugaCd == 'VOD01'){
					//주간보호
					if ($liProcTime >= 7){
						$liSugaTot = $liSugaCost * 3;
						$liHolidayTot = $liCostHoliday * 3;
					}else if ($liProcTime >= 3 && $liProcTime < 7){
						$liSugaTot = $liSugaCost * 2;
						$liHolidayTot = $liCostHoliday * 2;
					}else{
						$liSugaTot = $liSugaCost;
						$liHolidayTot = $liCostHoliday;
					}
				}
			}

			$suga = array('code'	   =>$lsSugaCd
						 ,'name'	   =>$lsSugaNm
						 ,'cost'	   =>$liSugaCost
						 ,'costEvening'=>0
						 ,'costNight'  =>0
						 ,'costTotal'  =>$liSugaTot
						 ,'sudangPay'  =>0
						 ,'timeEvening'=>0
						 ,'timeNight'  =>0
						 ,'ynEvening'  =>'N'
						 ,'ynNight'	   =>'N'
						 ,'ynHoliday'  =>'N'
						 ,'costBipay'  =>$liCostBipay
						 ,'costHoliday'=>$liHolidayTot
						 ,'procTime'   =>$lsProcTime
						 ,'msg'        =>$msg);

			return $suga;
		}

		//장애인활동지원
		function findSugaDis($code, $svcKind, $date, $fromTime, $toTime, $svcVal, $svcLvl, $bathKind, $memCnt){
			$lsMsg = '';
			$lsDt = $date;

			if (SubStr($lsDt,0,6) >= '201402'){
				$limitTimes = 8;
			}else{
				$limitTimes = 4;
			}

			$lsFrom     = str_replace(':', '', $fromTime);
			$tmpFrom[0] = substr($lsFrom, 0, 2);
			$tmpFrom[1] = substr($lsFrom, 2, 2);
			$liFrom     = intval($tmpFrom[0]) * 60 + intval($tmpFrom[1]);

			$lsTo     = str_replace(':', '', $toTime);
			$tmpTo[0] = substr($lsTo, 0, 2);
			$tmpTo[1] = substr($lsTo, 2, 2);
			$liTo     = intval($tmpTo[0]) * 60 + intval($tmpTo[1]);

			if ($liTo < $liFrom) $liTo += 24 * 60;

			//진행시간
			$liProcTime = $liTo - $liFrom;

			if ($svcKind == '200'){
				if (SubStr($lsDt,0,6) >= '201701'){
					//2017년 1월 수가계산법이 변경
					//$liProcTime = Round($this->cutoff($liProcTime, 30) / 60, 1) * 60;

					$tmpTime = $liProcTime % 60;
					$liProcTime = $liProcTime - $tmpTime;

					if ($tmpTime >=15 && $tmpTime <=44){
						//15분 ~ 44분까지 30분 인정
						$liProcTime += 30;
					}else if ($tmpTime >= 45){
						//45분 이상 60분 인정
						$liProcTime += 60;
					}else{
						//15분이하는 인정하지 않음
					}
				}else{
					$liProcTime = Round($liProcTime / 60) * 60;
				}
				$liTo = $liFrom + $liProcTime;

				//제한 시간
				if ($memCnt > 1){
					$liLimitHour = 3 * 60;
				}else{
					$liLimitHour = 8 * 60;
				}

				//제한시간 체크
				if ($liProcTime > $liLimitHour){
					$liProcTime = $liLimitHour;
					$liTo       = $liFrom + $liProcTime;
				}
			}

			if ($svcKind == '500'){
				$lsSugaCd = 'VAB'.$bathKind.'0';
			}else if ($svcKind == '800'){
				$lsSugaCd = 'VAN';

				if ($liProcTime < 30){
					$lsSugaCd .= '1';
				}else if ($liProcTime >= 30 && $liProcTime < 60){
					$lsSugaCd .= '2';
				}else{
					$lsSugaCd .= '3';
				}

				$lsSugaCd .= '0';
			}else{
				if($svcVal == 'D'){
					$lsSugaCd = 'VA'.$svcVal.($svcLvl < 10 ? '0'.$svcLvl : $svcLvl);;
				}else {
					$lsSugaCd = 'VA'.$svcVal.$svcLvl.'0';;
				}
			}

			//서비스단가
			$sql = 'select service_code as cd
					,      service_gbn as nm
					,      service_lvl as gbn
					,      service_cost as cost
					,      service_cost_night as cost_night
					,      service_cost_holiday as cost_holiday
					,      service_bipay as cost_bipay
					,      DATE_FORMAT(service_from_dt,\'%Y%m%d\') as from_dt
					,      DATE_FORMAT(service_to_dt,\'%Y%m%d\') as to_dt
					  from suga_service
					 where org_no       = \'goodeos\'
					   and service_kind = \'4\'';

			$laSvcSuga = $this->conn->_fetch_array($sql);

			if (is_array($laSvcSuga)){
				$liCnt = SizeOf($laSvcSuga);

				for($i=0; $i<$liCnt; $i++){
					if ($laSvcSuga[$i]['cd'] == $lsSugaCd &&
						SubStr($laSvcSuga[$i]['from_dt'],0,StrLen($lsDt)) <= $lsDt &&
						SubStr($laSvcSuga[$i]['to_dt'],0,StrLen($lsDt)) >= $lsDt){
						if ($svcKind == '200'){
							$lsSugaNm = $laSvcSuga[$i]['nm'].'/'.$liProcTime.'분';
						}else{
							$lsSugaNm = $laSvcSuga[$i]['nm'].'/'.$laSvcSuga[$i]['gbn'];
						}
						$liCost        = $laSvcSuga[$i]['cost'];
						$liCostNight   = $laSvcSuga[$i]['cost_night'];
						$liCostHoliday = $laSvcSuga[$i]['cost_holiday'];
						$liCostBipay   = $laSvcSuga[$i]['cost_bipay'];

						break;
					}
				}
			}

			if ($liCostHoliday == 0) $liCostHoliday = $liCost;

			if ($svcKind == '200'){
				//연장시간
				$laNightList = array(
					0 => array(6 * 60, 22 * 60)
				,	1 => array(22 * 60, 6 * 60 + 24 * 60)
				,	2 => array(0, 6 * 60)
				);

				$liHour      = 0; //기준시간
				$liHourNight = 0; //연장시간

				if (($liFrom >= $laNightList[1][0] && $liFrom < $laNightList[1][1]) ||
					($liFrom >= $laNightList[2][0] && $liFrom < $laNightList[2][1])){
					//시작이 22시 이후의 연장수가 적용
					if ($liFrom >= $laNightList[1][0] && $liFrom < $laNightList[1][1]){
						$liIdx = 1;
					}else{
						$liIdx = 2;
					}

					if ($liFrom + $liProcTime > $laNightList[$liIdx][1]){
						$liHourNight = $laNightList[$liIdx][1] - $liFrom;
					}else{
						$liHourNight = $liProcTime;
					}
				}else if (($liTo >= $laNightList[1][0] && $liTo < $laNightList[1][1]) ||
						  ($liTo >= $laNightList[2][0] && $liTo < $laNightList[2][1])){
					//종료가 22시를 넘어가면 연장수가를 적용한다.
					if ($liTo >= $laNightList[1][0]){
						$liTmtHour = $liTo;
					}else{
						$liTmtHour = $liTo + 24 * 60;
					}

					$liHourNight = $liTmtHour - $laNightList[1][0];
				}else{
					$lsMsg .= 'flag : 3';
					$liHour = $liProcTime;
				}

				//연장은 최대 4시간으로 제한한다.
				if ($liHourNight > $limitTimes * 60) $liHourNight = $limitTimes * 60;

				$liHour = $liProcTime - $liHourNight;

				//직원 2명인 경우 단가를 150% 적용한다.
				if ($memCnt > 1){
					$liCost *= 1.5;
					$liCostNight *= 1.5;
					$liCostHoliday *= 1.5;
				}

				//휴일 기준 및 연장시간
				$liHolidayHourNight = $liProcTime;

				if ($liHolidayHourNight > $limitTimes * 60) $liHolidayHourNight = $limitTimes * 60;

				$liHolidayHour = $liProcTime - $liHolidayHourNight;

				//$liHour = round($liHour / 60, 1);
				//$liHourNight = round($liHourNight / 60, 1);
				//$liHolidayHour = round($liHolidayHour / 60, 1);
				//$liHolidayHourNight = round($liHolidayHourNight / 60, 1);

				//시간단위로 계산함
				if (SubStr($lsDt,0,6) >= '201701'){
					$liHour = round($this->cutoff($liHour, 30) / 60, 1);
					$liHourNight = round($this->cutoff($liHourNight, 30) / 60, 1);
					$liHolidayHour = round($this->cutoff($liHolidayHour, 30) / 60, 1);
					$liHolidayHourNight = round($this->cutoff($liHolidayHourNight, 30) / 60, 1);
				}else{
					$liHour = round($liHour / 60);
					$liHourNight = round($liHourNight / 60);
					$liHolidayHour = round($liHolidayHour / 60);
					$liHolidayHourNight = round($liHolidayHourNight / 60);
				}

				$lsToHour = floor($liTo / 60);
				$lsToMin  = $liTo % 60;

				if ($lsToHour > 24) $lsToHour -= 24;

				$lsToHour = ($lsToHour < 10 ? '0' : '').$lsToHour;
				$lsToMin = ($lsToMin < 10 ? '0' : '').$lsToMin;
			}

			if ($svcKind == '200'){
				$liSugaTot = ($liCost * $liHour) + ($liCostNight * $liHourNight);
				//$liCostHoliday = ($liCost * $liHolidayHour) + ($liCostNight * $liHolidayHourNight);
				$liHolidayTot = ($liCostHoliday * $liHour) + ($liCostNight * $liHourNight);
			}else{
				$liSugaTot = $liCost;
				$liHolidayTot = $liCostHoliday;
			}

			$suga = array('code'	   =>$lsSugaCd
						 ,'name'	   =>$lsSugaNm

						 ,'cost'	   =>$liCost
						 ,'hour'       =>$liHour
						 ,'costNight'  =>$liCostNight
						 ,'hourNight'  =>$liHourNight
						 ,'toHour'	   =>$lsToHour
						 ,'toMin'	   =>$lsToMin
						 ,'holidayCost'=>$liCostHoliday

						 ,'holidayHour'     =>$liHolidayHour
						 ,'holidayHourNight'=>$liHolidayHourNight

						 ,'costEvening'=>0
						 ,'costTotal'  =>$liSugaTot
						 ,'sudangPay'  =>0
						 ,'timeEvening'=>0
						 ,'timeNight'  =>$liHourNight
						 ,'ynEvening'  =>'N'
						 ,'ynNight'	   =>'N'
						 ,'ynHoliday'  =>'N'
						 ,'costBipay'  =>$liCostBipay
						 ,'costHoliday'=>$liHolidayTot
						 ,'procTime'   =>$liProcTime
						 ,'msg'        =>$msg);

			return $suga;
		}

		//기타유료
		function findSugaOther($code, $svcCd, $date, $fromTime, $toTime){
			$lsDt = $date;

			$lsFrom     = str_replace(':', '', $fromTime);
			$tmpFrom[0] = substr($lsFrom, 0, 2);
			$tmpFrom[1] = substr($lsFrom, 2, 2);
			$liFrom     = intval($tmpFrom[0]) * 60 + intval($tmpFrom[1]);

			$lsTo     = str_replace(':', '', $toTime);
			$tmpTo[0] = substr($lsTo, 0, 2);
			$tmpTo[1] = substr($lsTo, 2, 2);
			$liTo     = intval($tmpTo[0]) * 60 + intval($tmpTo[1]);

			if ($liTo < $liFrom) $liTo += 24 * 60;

			//진행시간
			if ($svcCd == 'A'){
				$liProcTime = 1;
			}else{
				$liProcTime = round(($liTo - $liFrom) / 60,1);
			}

			//수가정보
			$sql = 'select service_code as cd
					,      service_gbn as nm
					,      date_format(service_from_dt,\'%Y%m%d\') as from_dt
					,      date_format(service_to_dt,\'%Y%m%d\') as to_dt
					  from suga_service
					 where org_no       = \'goodeos\'
					   and service_kind = \''.$svcCd.'\'';

			$laSvcSuga = $this->conn->_fetch_array($sql);

			if (is_array($laSvcSuga)){
				foreach($laSvcSuga as $svc){
					if (substr($svc['from_dt'],0,strlen($lsDt)) <= $lsDt && substr($svc['to_dt'],0,strlen($lsDt)) >= $lsDt){
						$lsSugaCd = $svc['cd'];
						$lsSugaNm = $svc['nm'];
						break;
					}
				}
			}

			$suga = array('code'	   =>$lsSugaCd
						 ,'name'	   =>$lsSugaNm
						 ,'cost'	   =>0
						 ,'costEvening'=>0
						 ,'costNight'  =>0
						 ,'costTotal'  =>0
						 ,'sudangPay'  =>0
						 ,'timeEvening'=>0
						 ,'timeNight'  =>0
						 ,'ynEvening'  =>'N'
						 ,'ynNight'	   =>'N'
						 ,'ynHoliday'  =>'N'
						 ,'costBipay'  =>0
						 ,'costHoliday'=>0
						 ,'procTime'   =>$liProcTime);

			return $suga;
		}

		//기관 비급여 수가
		function findSugaCenter($code, $svcCd, $svcKind, $sugaCd, $date = ''){
			if (!empty($date))
				$lsDt = $date;
			else
				$lsDt = date('Ymd');

			$lsDt = str_replace('.', '', $lsDt);
			$lsDt = str_replace('-', '', $lsDt);

			$sql = 'select val
					  from (
						   select m01_suga_cvalue1 as val
						   ,      m01_sdate as from_dt
						   ,      m01_edate as to_dt
							 from m01suga
							where m01_mcode  = \''.$code.'\'
							  and m01_mcode2 = \''.$sugaCd.'\'
							  and left(m01_sdate,'.strlen(date).') <= \''.$date.'\'
							  and left(m01_edate,'.strlen(date).') >= \''.$date.'\'
							union all
						   select m11_suga_cvalue1
						   ,      m11_sdate
						   ,      m11_edate
							 from m11suga
							where m11_mcode  = \''.$code.'\'
							  and m11_mcode2 = \''.$sugaCd.'\'
							  and left(m11_sdate,'.strlen(date).') <= \''.$date.'\'
							  and left(m11_edate,'.strlen(date).') >= \''.$date.'\'
						   ) as t
					 order by from_dt desc
					 limit 1';

			$val = $this->conn->get_data($sql);

			return $val;
		}

		//주야간보호 수가
		function findSugaDayNight($date, $fromTime, $toTime, $svcLvl){
			$date	= str_replace('-','',$date);
			$date	= str_replace('.','',$date);
			$from	= $this->getTime2Min($fromTime);
			$to		= $this->getTime2Min($toTime);
			$ynHoli	= $this->ynHoliday($date);
			$weekly	= date('w',StrToTime($date)); //요일

			//7시 이전 및 21시 이후에는 일정등록할 수 없다.
			if ($from > 1260 || $from < 420){
				$from = 0;
				$to = 0;
			}

			if (StrLen($date) == 6){
				$dateFormat = '%Y%m';
			}else{
				$dateFormat = '%Y%m%d';
			}

			if ($from > $to) $to += (24 * 60);

			$time = $to - $from;
			$hour = Floor($time / 60);

			if ($hour >= 3 && $hour < 6){
				$hour = 3;
			}else if ($hour >= 6 && $hour < 8){
				$hour = 6;
			}else if ($hour >= 8 && $hour < 10){
				$hour = 8;
			}else if ($hour >= 10 && $hour < 12){
				$hour = 10;
			}else if ($hour >= 12){
				$hour = 12;
			}

			$hour = ($hour < 10 ? '0' : '').$hour;

			$sql = 'SELECT	code
					,		cost
					,		name
					,		other AS fullname
					FROM	suga_dan
					WHERE	code	= \'B43'.$hour.'\'
					AND		lv_gbn	= \''.$svcLvl.'\'
					AND		DATE_FORMAT(from_dt,\''.$dateFormat.'\') <= \''.$date.'\'
					AND		DATE_FORMAT(to_dt,	\''.$dateFormat.'\') >= \''.$date.'\'';

			$row = $this->conn->get_array($sql);

			$suga['code']		= $row['code'];
			$suga['cost']		= $row['cost'];
			$suga['procTime']	= $time; //제공시간
			$suga['name']		= $row['name'];
			$suga['fullname']	= $row['fullname'];
			$suga['costTotal']	= 0; //총금액
			$suga['timeEvening']= 0; //연장시간
			$suga['costEvening']= 0; //연장할증금액
			$suga['timeNight']	= 0; //야간시간
			$suga['costNight']	= 0; //야간할증금액

			if (SubStr($date, 0, 6) >= '201701'){
				$suga['costSat'] = Round($suga['cost'] * 1.3 / 10) * 10; //토요일수가
			}else{
				$suga['costSat'] = Round($suga['cost'] * 1.2 / 10) * 10; //토요일수가
			}
			//$suga['costHoliday']= $suga['cost'] * 1.3; //휴일할증수가
			$suga['costHoliday']= Round($suga['cost'] * 1.3 / 10) * 10; //휴일할증수가
			//$suga['costHoliday']= $suga['costHoliday'] - ($suga['costHoliday'] % 10);
			$suga['ynHoliday']	= $ynHoli;

			Unset($row);

			//18시(1080) ~ 22시(1320)까지 20%
			//22시 ~ 익일 06시까지 30%

			//야간시간
			$nightFrom	= 22 * 60;
			$nightTo	= 24 * 60 + 6 * 60;

			if ($from >= $nightFrom){
				$suga['timeNight'] = $from - $nightFrom;

				if ($to > $nightTo){
					$suga['timeNight'] += ($nightTo - $from);
				}else{
					$suga['timeNight'] += ($to - $from);
				}
			}else if ($to >= $nightFrom){
				$suga['timeNight'] = $to - $nightFrom;
			}

			//일반은 야간을 하지 않음.
			if ($svcLvl == '9') $suga['timeNight'] = 0;

			//주야간보호 야간 인정하지 않는거 같음.
			$suga['costNight'] = 0; //Floor($suga['cost'] / $suga['procTime'] * $suga['timeNight'] * 0.2);

			//연장시간
			$extendFrom = 18 * 60;
			$extendTo	= 22 * 60;

			if ($to >= $extendFrom){
				if ($to > $extendTo){
					if ($from > $extendFrom){
						$suga['timeEvening']	= $extendTo - $from;
					}else{
						$suga['timeEvening']	= $extendTo - $extendFrom;
					}
				}else{
					$suga['timeEvening']	= $to - $extendFrom;
				}
			}

			//일반은 연장을 하지 않음.
			if ($svcLvl == '9') $suga['timeEvening'] = 0;

			if ($ynHoli == 'Y'){//휴일
				$suga['costEvening']= 0;
				$suga['costTotal']	= $suga['costHoliday'];
			}else if ($weekly == 6){//토요일
				$suga['costEvening']= 0;
				$suga['costTotal']	= $suga['costSat'];
			}else{
				$suga['costEvening']= Floor($suga['cost'] / $suga['procTime'] * $suga['timeEvening'] * 0.2);
				$suga['costTotal']	= $suga['cost'] + Round($suga['costEvening']/10)*10 + Round($suga['costNight']/10)*10;
			}

			return $suga;
		}

		//시간을 분으로 환산
		function getTime2Min($ltTime){
			$lsTime  = str_replace(':','',$ltTime); //시작시간
			$liTimeH = intval(substr($lsTime,0,2));
			$liTimeM = intval(substr($lsTime,2,2));
			$lsTime  = $liTimeH * 60 + $liTimeM;

			return $lsTime;
		}

		//휴일여부
		function ynHoliday($date){
			$lsDtY = substr($date, 0, 4);
			$lsDtM = substr($date, 4, 2);
			$lsDtD = substr($date, 6, 2);

			//요일
			$liWeekday = date('w', strtotime($lsDtY.'-'.$lsDtM.'-'.$lsDtD));

			//휴일여부
			$ynHoliday = 'N';
			if ($liWeekday == 0) $ynHoliday = 'Y';
			if ($ynHoliday != 'Y'){
				$sql = 'select count(*)
						  from tbl_holiday
						 where mdate = \''.$date.'\'';

				if ($this->conn->get_data($sql) > 0){
					$ynHoliday = 'Y';
				}
			}

			return $ynHoliday;
		}

		//수가리스트
		function listSugaCare($date, $sugaCd){
			$lsDt = $date;
			$lsDt = str_replace('.', '', $lsDt);
			$lsDt = str_replace('-', '', $lsDt);

			$sql = 'select m01_suga_value as cost
					,      m01_calc_time as time
					  from m01suga
					 where m01_mcode  = \'goodeos\'
					   and left(m01_sdate, '.strlen($lsDt).')  <= \''.$lsDt.'\'
					   and left(m01_edate, '.strlen($lsDt).')  >= \''.$lsDt.'\'
					   and left(m01_mcode2,'.strlen($sugaCd).') = \''.$sugaCd.'\'
					 union all
					select m11_suga_cont as name
					,      m11_suga_value as cost
					  from m11suga
					 where m11_mcode  = \'goodeos\'
					   and left(m11_sdate, '.strlen($lsDt).')  <= \''.$lsDt.'\'
					   and left(m11_edate, '.strlen($lsDt).')  >= \''.$lsDt.'\'
					   and left(m11_mcode2,'.strlen($sugaCd).') = \''.$sugaCd.'\'
					 order by cost';

			$laSugaList = $this->conn->_fetch_array($sql);

			return $laSugaList;
		}

		//수가 시간
		function getSugaTime($date, $sugaCd, $sugaVal){
			$sql = 'select m01_mcode2 as code
					,      m01_suga_value as val
					,      m01_calc_time as time
					  from m01suga
					 where m01_mcode       = \'goodeos\'
					   and m01_suga_value <= \''.$sugaVal.'\'
					   and m01_calc_time  <  \'9\'
					   and left(m01_mcode2,'.strlen($sugaCd).') = \''.$sugaCd.'\'
					   and left(m01_sdate,'.strlen($date).')   <= \''.$date.'\'
					   and left(m01_edate,'.strlen($date).')   >= \''.$date.'\'
					 union all
					select m11_mcode2 as code
					,      m11_suga_value
					,      m11_calc_time
					  from m11suga
					 where m11_mcode       = \'goodeos\'
					   and m11_suga_value <= \''.$sugaVal.'\'
					   and m11_calc_time  <  \'9\'
					   and left(m11_mcode2,'.strlen($sugaCd).') = \''.$sugaCd.'\'
					   and left(m11_sdate,'.strlen($date).')   <= \''.$date.'\'
					   and left(m11_edate,'.strlen($date).')   >= \''.$date.'\'
					 order by val desc
					 limit 1';

			$row = $this->conn->get_array($sql);

			return $row['code'].chr(1).$row['val'].chr(1).$row['time'];
		}

		function cutoff($val, $cut = 10){
			return floor($val - ($val % $cut));
		}
	}

	$mySuga = new mySuga($conn);
?>
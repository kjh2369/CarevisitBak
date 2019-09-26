<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$suga	= $_POST['suga'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$para	= $_POST['para'];

	$arrPara = Explode(';',$para);

	$conn->begin();

	if($lsIljungSave=='1'){
		/*
		$sql = 'UPDATE t01iljung
				SET   t01_del_yn	  = \'Y\' 
				WHERE t01_ccode		  = \''.$code.'\'
				AND	  t01_mkind		  = \''.$sr.'\'
				AND	  t01_suga_code1  = \''.$suga.'\'
				AND	  t01_sugup_date >= \''.$year.$month.'01\'
				AND	  t01_sugup_date <= \''.$year.$month.'31\'';
		
		if ($sr == 'S'){
		}else{
			$sql .= '
				AND		t01_status_gbn != \'1\'
				AND		t01_status_gbn != \'5\'';
		}
		
		$sql .= '
				AND		t01_del_yn		= \'N\'
				AND		IFNULL(t01_request,\'SERVICE\') = \'SERVICE\'';
		
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
		*/
		//순번
		$sql = 'SELECT	CONCAT(t01_jumin, \'_\', t01_sugup_date) AS idx
				,		MAX(t01_sugup_seq) AS seq
				FROM	t01iljung
				WHERE	t01_ccode = \''.$code.'\'
				AND		t01_mkind = \''.$sr.'\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				GROUP	BY t01_jumin, t01_sugup_date';

		$arrSeq = $conn->_fetch_array($sql,'idx');

		$sl	 = 'INSERT INTO t01iljung (
				 t01_ccode			/*기관코드*/
				,t01_mkind			/*서비스구분*/
				,t01_jumin			/*주민번호*/
				,t01_sugup_date		/*일자*/
				,t01_sugup_fmtime	/*시작시간*/
				,t01_sugup_seq		/*순번*/
				,t01_sugup_yoil		/*요일*/
				,t01_svc_subcode	/*서비스종류*/
				,t01_status_gbn		/*상태*/

				,t01_yoyangsa_id1	/*실행 주요양보호사*/
				,t01_yoyangsa_id2	/*실행 부요양보호사*/
				,t01_yname1			/*요양보호사명*/
				,t01_yname2			/*요양보호사명*/

				,t01_mem_cd1		/*계획 주요양보호사*/
				,t01_mem_cd2		/*계획 부요양보호사*/
				,t01_mem_nm1		/*요양보호사명*/
				,t01_mem_nm2		/*요양보호사명*/

				,t01_suga_code1		/*수가코드*/
				,t01_suga			/*수가*/
				,t01_suga_tot		/*수가총액*/

				,t01_svc_subcd		/*S:재가지원 R:자원연계*/
				,t01_request

				) values (';


		if ($sr == 'S'){
			$statGbn = '1';
		}else{
			$statGbn = '9';
		}


		foreach($arrPara as $row){
			parse_str($row,$col);

			//$client	= Explode(chr(11), $col['client']);
			$client	= Explode('_TAB_', $col['client']);
			$stat	= Explode(chr(11), $col['stat']);
			$seq	= Explode(chr(11), $col['seq']);
			$date	= $year.$month.($col['day'] < 10 ? '0' : '').$col['day'];
			$weekly	= Date('w', StrToTime($date));
			$time	= str_replace(':', '', $col['from']);
			$cost	= str_replace(',', '', $col['cost']);
			$memCd	= $ed->de($col['memCd']);
			$resourceCd = $col['resourceCd'];
			

			foreach($seq as $c => $no){

				if($col['day'] != ''){
					$sql = 'SELECT count(*)
							FROM  t01iljung
							WHERE t01_ccode		  = \''.$code.'\'
							AND	  t01_mkind		  = \''.$sr.'\'
							AND	  t01_suga_code1  = \''.$suga.'\'  
							AND   t01_sugup_date  = \''.$date.'\'
							AND   t01_sugup_fmtime = \''.$time.'\'
							AND   t01_sugup_seq   = \''.$no.'\'
							AND   t01_mem_cd1     = \''.$resourceCd.'\'
							AND   t01_mem_cd2     = \''.$memCd.'\'
							AND   t01_svc_subcode  = \''.$col['svcKind'].'\'';
					
					$cnt = $conn -> get_data($sql);
				
					if($cnt>0){
						
						$sql = 'UPDATE t01iljung
								SET   t01_del_yn	  = \'N\' 
								WHERE t01_ccode		  = \''.$code.'\'
								AND	  t01_mkind		  = \''.$sr.'\'
								AND	  t01_suga_code1  = \''.$suga.'\'  
								AND   t01_sugup_date  = \''.$date.'\'
								AND   t01_sugup_fmtime = \''.$time.'\'
								AND   t01_sugup_seq   = \''.$no.'\'
								AND   t01_mem_cd1     = \''.$resourceCd.'\'
								AND   t01_mem_cd2     = \''.$memCd.'\'
								AND   t01_svc_subcode  = \''.$col['svcKind'].'\'';
					
						if ($sr == 'S'){
						}else{
							$sql .= '
								AND		t01_status_gbn != \'1\'
								AND		t01_status_gbn != \'5\'';
						}
						
						$sql .= '
								AND		IFNULL(t01_request,\'SERVICE\') = \'SERVICE\'';
						
						
						if (!$conn->execute($sql)){
							 $conn->rollback();
							 $conn->close();
							 echo 9;
							 exit;
						}
						
					}else {
						foreach($client as $i => $jumin){
							if (!$jumin) continue;

							$tmp = $jumin;
							$jumin = $ed->de($jumin);
							
							if (!$jumin) continue;

							$arrSeq[$jumin.'_'.$date]['seq'] ++;

							if ($sr == 'S'){
							}else{
								if ($stat[$i] == '1') continue;
							}

							if (!$col['resourceCd']) continue;
								
							$sql = $sl.'
									 \''.$code.'\'
									,\''.$sr.'\'
									,\''.$jumin.'\'
									,\''.$date.'\'
									,\''.$time.'\'
									,\''.$arrSeq[$jumin.'_'.$date]['seq'].'\'
									,\''.$weekly.'\'
									,\''.$col['svcKind'].'\'
									,\''.$statGbn.'\'

									,\''.$col['resourceCd'].'\'
									,\''.$memCd.'\'
									,\''.$col['resourceNm'].'\'
									,\''.$col['memNm'].'\'

									,\''.$col['resourceCd'].'\'
									,\''.$memCd.'\'
									,\''.$col['resourceNm'].'\'
									,\''.$col['memNm'].'\'

									,\''.$suga.'\'
									,\''.$cost.'\'
									,\''.$cost.'\'

									,\''.$sr.'\'
									,\'SERVICE\'
									)';
							
							if (!$conn->execute($sql)){
								 $conn->rollback();
								 $conn->close();
								 echo 9;
								 exit;
							}	
							
						}
					}
				}
			}
		}
		
		
		$conn->commit();
		
	}else {
	
		//기존일정삭제
		$sql = 'DELETE
				FROM	t01iljung
				WHERE	t01_ccode		= \''.$code.'\'
				AND		t01_mkind		= \''.$sr.'\'
				AND		t01_suga_code1	= \''.$suga.'\'
				AND		t01_sugup_date >= \''.$year.$month.'01\'
				AND		t01_sugup_date <= \''.$year.$month.'31\'';

		if ($sr == 'S'){
		}else{
			$sql .= '
				AND		t01_status_gbn != \'1\'
				AND		t01_status_gbn != \'5\'';
		}

		$sql .= '
				AND		t01_del_yn		= \'N\'
				AND		IFNULL(t01_request,\'SERVICE\') = \'SERVICE\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		

		//순번
		$sql = 'SELECT	CONCAT(t01_jumin, \'_\', t01_sugup_date) AS idx
				,		MAX(t01_sugup_seq) AS seq
				FROM	t01iljung
				WHERE	t01_ccode = \''.$code.'\'
				AND		t01_mkind = \''.$sr.'\'
				AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
				GROUP	BY t01_jumin, t01_sugup_date';

		$arrSeq = $conn->_fetch_array($sql,'idx');

		$sl	 = 'INSERT INTO t01iljung (
				 t01_ccode			/*기관코드*/
				,t01_mkind			/*서비스구분*/
				,t01_jumin			/*주민번호*/
				,t01_sugup_date		/*일자*/
				,t01_sugup_fmtime	/*시작시간*/
				,t01_sugup_seq		/*순번*/
				,t01_sugup_yoil		/*요일*/
				,t01_svc_subcode	/*서비스종류*/
				,t01_status_gbn		/*상태*/

				,t01_yoyangsa_id1	/*실행 주요양보호사*/
				,t01_yoyangsa_id2	/*실행 부요양보호사*/
				,t01_yname1			/*요양보호사명*/
				,t01_yname2			/*요양보호사명*/

				,t01_mem_cd1		/*계획 주요양보호사*/
				,t01_mem_cd2		/*계획 부요양보호사*/
				,t01_mem_nm1		/*요양보호사명*/
				,t01_mem_nm2		/*요양보호사명*/

				,t01_suga_code1		/*수가코드*/
				,t01_suga			/*수가*/
				,t01_suga_tot		/*수가총액*/

				,t01_svc_subcd		/*S:재가지원 R:자원연계*/
				,t01_request

				) values (';


		if ($sr == 'S'){
			$statGbn = '1';
		}else{
			$statGbn = '9';
		}


		foreach($arrPara as $row){
			parse_str($row,$col);

			//$client	= Explode(chr(11), $col['client']);
			$client	= Explode('_TAB_', $col['client']);
			$stat	= Explode(chr(11), $col['stat']);
			$seq	= Explode(chr(11), $col['seq']);
			$date	= $year.$month.($col['day'] < 10 ? '0' : '').$col['day'];
			$weekly	= Date('w', StrToTime($date));
			$time	= str_replace(':', '', $col['from']);
			$cost	= str_replace(',', '', $col['cost']);
			$memCd	= $ed->de($col['memCd']);

			foreach($client as $i => $jumin){
				if (!$jumin) continue;

				$tmp = $jumin;
				$jumin = $ed->de($jumin);

				if (!$jumin) continue;

				$arrSeq[$jumin.'_'.$date]['seq'] ++;

				if ($sr == 'S'){
				}else{
					if ($stat[$i] == '1') continue;
				}

				if (!$col['resourceCd']) continue;

				$sql = $sl.'
						 \''.$code.'\'
						,\''.$sr.'\'
						,\''.$jumin.'\'
						,\''.$date.'\'
						,\''.$time.'\'
						,\''.$arrSeq[$jumin.'_'.$date]['seq'].'\'
						,\''.$weekly.'\'
						,\''.$col['svcKind'].'\'
						,\''.$statGbn.'\'

						,\''.$col['resourceCd'].'\'
						,\''.$memCd.'\'
						,\''.$col['resourceNm'].'\'
						,\''.$col['memNm'].'\'

						,\''.$col['resourceCd'].'\'
						,\''.$memCd.'\'
						,\''.$col['resourceNm'].'\'
						,\''.$col['memNm'].'\'

						,\''.$suga.'\'
						,\''.$cost.'\'
						,\''.$cost.'\'

						,\''.$sr.'\'
						,\'SERVICE\'
						)';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo 9;
					 exit;
				}
			}
		}

		$conn->commit();
	}


	echo 1;

	include_once('../inc/_db_close.php');
?>
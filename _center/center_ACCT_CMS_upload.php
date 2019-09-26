<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$IsOverlap = ($_POST['chkOverlapYN'] == 'Y' ? true : false);
	$cms = $_FILES['cmsfile'];
	$file = '../tempFile/CMS_'.mktime();

	$IsCenter = false;

	if (move_uploaded_file($cms['tmp_name'], $file)){
		//기관 미수내역
		/*
		$sql = 'SELECT	org_no, SUM(acct_amt) AS acct_amt, SUM(in_amt) AS in_amt
				FROM	(
						SELECT	a.org_no, SUM(IFNULL((SELECT amt FROM cv_svc_acct_amt WHERE org_no = a.org_no AND yymm <= a.yymm ORDER BY yymm LIMIT 1),0)) AS acct_amt, 0 AS in_amt
						FROM	(
								SELECT	org_no, yymm, SUM(acct_amt) AS acct_amt
								FROM	cv_svc_acct_list
								GROUP	BY org_no, yymm
								) AS a
						GROUP	BY a.org_no
						UNION	ALL
						SELECT	org_no, 0, SUM(in_amt)
						FROM	cv_cms_reg
						WHERE	del_flag = \'N\'
						GROUP	BY org_no
						) AS a
				GROUP	BY org_no
				HAVING	acct_amt - in_amt > 0';

		$orgList = $conn->_fetch_array($sql, 'org_no');
		*/

		//청구금액 및 적용금액
		/*
		$sql = 'SELECT	org_no, yymm, SUM(acct_amt) AS acct_amt, SUM(link_amt) AS link_amt
				FROM	(
						SELECT	a.org_no, a.yymm, CASE WHEN a.yymm > \'201508\' THEN a.acct_amt ELSE SUM(IFNULL((SELECT amt FROM cv_svc_acct_amt WHERE org_no = a.org_no AND yymm <= a.yymm AND amt > 0 ORDER BY yymm LIMIT 1),0)) END AS acct_amt, 0 AS link_amt
						FROM	(
								SELECT	org_no, yymm, SUM(acct_amt) AS acct_amt
								FROM	cv_svc_acct_list
								GROUP	BY org_no, yymm
								) AS a
						GROUP	BY a.org_no, a.yymm
						UNION	ALL
						SELECT	org_no, yymm, 0, SUM(link_amt)
						FROM	cv_cms_link
						WHERE	del_flag = \'N\'
						GROUP	BY org_no, yymm
						) AS a
				GROUP	BY org_no, yymm';
		*/
		$sql = 'SELECT	UPPER(org_no) AS org_no, yymm, SUM(acct_amt) AS acct_amt, SUM(link_amt) AS link_amt
				FROM	(
						SELECT	a.org_no, a.yymm, a.acct_amt, 0 AS link_amt
						FROM	(
								SELECT	org_no, yymm, SUM(acct_amt) AS acct_amt
								FROM	cv_svc_acct_list
								GROUP	BY org_no, yymm
								) AS a
						GROUP	BY a.org_no, a.yymm
						UNION	ALL
						SELECT	org_no, yymm, 0, SUM(link_amt)
						FROM	cv_cms_link
						WHERE	del_flag = \'N\'
						GROUP	BY org_no, yymm
						) AS a
				GROUP	BY org_no, yymm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$orgList[$row['org_no']][$row['yymm']] = Array('acctAmt'=>$row['acct_amt'], 'linkAmt'=>$row['link_amt']);
		}

		$conn->row_free();


		error_reporting(E_ALL ^ E_NOTICE);

		include_once('../excel/PHPExcel.php');           //라이브러리 업로드 경로
		include_once('../excel/PHPExcel/IOFactory.php'); //라이브러리 업로드 경로

		@$lo_reader = PHPExcel_IOFactory::createReaderForFile($file);//읽기객체생성
		@$lo_reader->setReadDataOnly(true);							//읽기전용설정
		@$lo_excel = $lo_reader->load($file);						//읽기
		@$lo_excel->setActiveSheetIndex(0);							//시트선택
		@$lo_sheet = $lo_excel->getActiveSheet();					//시트활성화
		@$lo_rowIterator = $lo_sheet->getRowIterator();				//모든 행

		$rowId = 0;  //데이타 인덱스
		$yymm  = ''; //년월
		$cellCnt = 0;

		foreach($lo_rowIterator as $row){
			@$lo_cell = $row->getCellIterator();
			@$lo_cell->setIterateOnlyExistingCells(false); //해당 행의 모든 열

			/*********************************************************
				A : No
				B : 결제구분
				C : 회원번호
				D : 회원명
				E : 약정일
				F : 입금일
				G : 예정일
				H : 출금일
				I : 출금액
				J : 비고
				K : 회원구분
			 *********************************************************/
			if ($IsCenter){
				$defCell = Array('A'=>'org_no', 'C'=>'amt', 'E'=>'cms_no');
			}else{
				/*
				if ($cellCnt == 0){
					foreach($lo_cell as $cell){
						$cellCnt ++;
					}
				}

				if ($cellCnt == 11){
					//지케어
					$defCell = Array('B'=>'ACCT_GBN', 'C'=>'CMS_NO', 'D'=>'ORG_NM', 'H'=>'CMS_DT', 'I'=>'IN_AMT', 'J'=>'STAT');
				}else if ($cellCnt == 17){
					//굿이오스, 케어비지트
					$defCell = Array('B'=>'ACCT_GBN', 'E'=>'CMS_NO', 'F'=>'ORG_NM', 'C'=>'CMS_DT', 'N'=>'IN_AMT', 'P'=>'STAT');
				}else{
					exit;
				}

				$tmpData = Array('ACCT_GBN'=>'', 'CMS_NO'=>'', 'ORG_NM'=>'', 'CMS_DT'=>'', 'IN_AMT'=>'', 'STAT'=>'');
				*/
				$defCell = Array(
					'A'=>'NO'
				,	'B'=>'IN_GBN'		//CMS, 무통장
				,	'C'=>'CMS_COM'		//효성, 이지스
				,	'D'=>'CMS_DT'		//청구일
				,	'E'=>'IN_DT'		//출금일
				,	'F'=>'CMS_MEM_NO'	//CMS 회원번호
				,	'G'=>'ORG_NO'		//기관기호
				,	'H'=>'CMS_NO'		//CMS 코드
				,	'I'=>'ORG_NM'		//기관명
				,	'K'=>'IN_AMT'		//출금금액
				);

				$tmpData = Array(
					'NO'=>''
				,	'ORG_NO'=>''	//기관기호
				,	'CMS_NO'=>''	//CMS 코드
				,	'CMS_DT'=>''	//청구일
				,	'ORG_NM'=>''	//기관명
				,	'IN_GBN'=>''	//CMS, 무통장
				,	'IN_AMT'=>''	//출금금액
				,	'IN_STAT'=>''	//출금상태 Y, N
				,	'IN_DT'=>''		//출금일
				,	'CMS_MEM_NO'=>''//CMS 회원번호
				,	'CMS_COM'=>''	//효성, 이지스
				);
			}

			foreach($lo_cell as $cell){
				//echo $cell->getRow().'/'.$cell->getColumn().':'.$cell->getValue().' | ';

				$tmpCell = $defCell[$cell->getColumn()];

				if ($tmpCell){
					$tmpData[$tmpCell] = $cell->getValue();

					if ($tmpCell == 'CMS_DT' || $tmpCell == 'IN_DT'){
						$tmpData[$tmpCell] = str_replace('/','',$tmpData[$tmpCell]);
						$tmpData[$tmpCell] = str_replace('-','',$tmpData[$tmpCell]);
						$tmpData[$tmpCell] = str_replace('.','',$tmpData[$tmpCell]);
					}else if ($tmpCell == 'IN_AMT'){
						$tmpData[$tmpCell] = str_replace(',','',$tmpData[$tmpCell]);
					}else if ($tmpCell == 'ORG_NO'){
						$tmpData[$tmpCell] = strToUpper($tmpData[$tmpCell]);
					}else if ($tmpCell == 'IN_GBN'){
						if ($tmpData[$tmpCell] == 'CMS'){
							$tmpData[$tmpCell] = '1';
						}else if ($tmpData[$tmpCell] == '무통장'){
							$tmpData[$tmpCell] = '2';
						}
					}
				}
			}

			$data[] = $tmpData;
		}

		@unlink($file);

		Unset($tmpData);

		if ($IsCenter){
			foreach($data as $idx => $R){
				if (!is_numeric($R['cms_no'])){
					unset($data[$idx]);
					continue;
				}

				$sql = 'SELECT	*
						FROM	b02center
						WHERE	b02_center = \''.$R['org_no'].'\'';
				$row = $conn->get_array($sql);

				$data[$idx]['link_company'] = $row['b02_branch'];
				$data[$idx]['link_branch'] = $row['b02_branch'];
				$data[$idx]['link_person'] = $row['b02_person'];

				$sql = 'select m00_start_date, m00_cont_date
						from   m00center
						where  m00_mcode = \''.$R['org_no'].'\'
						order by m00_mkind
						limit 1';
				$r1 = $conn->get_array($sql);
				$data[$idx]['start_dt'] = $r1['m00_start_date'];
				$data[$idx]['cont_dt'] = $r1['m00_cont_date'];

				$data[$idx]['from_dt'] = str_replace('-','',$row['from_dt']);
				$data[$idx]['to_dt'] = str_replace('-','',$row['to_dt']);

				$data[$idx]['acct_gbn'] = '1';

				$data[$idx]['area_cd'] = $row['care_area'];
				$data[$idx]['group_cd'] = $row['care_group'];

				if (!$data[$idx]['area_cd'] || !$data[$idx]['group_cd']){
					$data[$idx]['area_cd'] = '99';
					$data[$idx]['group_cd'] = '99';
				}
			}

			unset($query);

			foreach($data as $idx => $r){
				if ($tmpOrgNo[$r['org_no']] == 'Y'){
					$cnt = 1;
				}else{
					$tmpOrgNo[$r['org_no']] = 'Y';
					$cnt = 0;
				}

				if ($cnt == 0){
					$sql = 'update	b02center
							set		cms_cd = \''.$r['cms_no'].'\'
							where	b02_center = \''.$r['org_no'].'\'';
					$query[] = $sql;

					$sql = 'insert into cv_reg_info (';

					$first = true;
					foreach($data[$idx] as $c => $v){
						if ($c == 'amt') continue;
						if ($first){
							$first = false;
						}else{
							$sql .= ',';
						}
						$sql .= $c;
					}

					$sql .= ') values (';

					$first = true;
					foreach($data[$idx] as $c => $v){
						if ($c == 'amt') continue;
						if ($first){
							$first = false;
						}else{
							$sql .= ',';
						}
						$sql .= '\''.$v.'\'';
					}

					$sql .= ')';
					$query[] = $sql;

					$sql = 'insert into cv_svc_fee (org_no,svc_gbn,svc_cd,seq,from_dt,to_dt,acct_yn,acct_gbn,stnd_cost,over_cost,limit_cnt,use_yn) values (
							\''.$r['org_no'].'\',\'1\',\'11\',\'1\',\''.$r['from_dt'].'\',\''.$r['to_dt'].'\',\'Y\',\'1\',\''.$r['amt'].'\',\'500\',\'30\',\'Y\')';
					$query[] = $sql;
				}

				$sql = 'insert into cv_cms_list (org_no, cms_no) values (\''.$r['org_no'].'\',\''.$r['cms_no'].'\')';
				$query[] = $sql;
			}
		}
	}

	if ($IsCenter){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 'ERROR'.chr(13).$conn->error_msg.chr(13).$conn->error_query;
				 exit;
			}
		}

		$conn->commit();

		unset($query);
	}


	if ($IsCenter) exit;


	$regCnt = 0;
	$regAmt = 0;


	if (is_array($data)){
		/*
		foreach($data as $tmpIdx => $R){
			if (is_numeric(StrPos($R['STAT'],'출금실패')) || $R['IN_AMT'] <= 0) continue;

			$CMSNo = IntVal($R['CMS_NO']);

			//기관기호
			$sql = 'SELECT	org_no
					FROM	cv_cms_list
					WHERE	cms_no = \''.$CMSNo.'\'';
			$orgNo = $conn->get_data($sql);


			$sql = 'SELECT	link_stat
					FROM	cv_cms_reg
					WHERE	org_no	= \''.$orgNo.'\'
					AND		cms_no	= \''.$R['CMS_NO'].'\'
					AND		cms_dt	= \''.$R['CMS_DT'].'\'
					AND		del_flag= \'N\'';
			$linkStat = $conn->get_data($sql);


			//입금내역이 연결된 후에는 수정할 수 없도록...
			if (!$linkStat) $linkStat = '9';
			if ($linkStat != '9') continue;

			if ($IsOverlap){
				//중복시 이전의 데이타 삭제처리
				$sql = 'UPDATE	cv_cms_reg
						SET		del_flag	= \'Y\'
						,		update_id	= \''.$_SESSION['userCode'].'\'
						,		update_dt	= NOW()
						WHERE	org_no	= \''.$orgNo.'\'
						AND		cms_no	= \''.$R['CMS_NO'].'\'
						AND		cms_dt	= \''.$R['CMS_DT'].'\'';
				$query[] = $sql;

				//다음순번
				$sql = 'SELECT	IFNULL(MAX(seq),0)+1
						FROM	cv_cms_reg
						WHERE	org_no	= \''.$orgNo.'\'
						AND		cms_no	= \''.$R['CMS_NO'].'\'
						AND		cms_dt	= \''.$R['CMS_DT'].'\'';
				$seq = $conn->get_data($sql);
				$IsNew = true;
			}else{
				$sql = 'SELECT	seq
						FROM	cv_cms_reg
						WHERE	org_no	= \''.$orgNo.'\'
						AND		cms_no	= \''.$R['CMS_NO'].'\'
						AND		cms_dt	= \''.$R['CMS_DT'].'\'
						AND		del_flag= \'N\'';
				$seq = $conn->get_data($sql);

				if ($seq){
					$IsNew = false;
				}else{
					$sql = 'SELECT	IFNULL(MAX(seq),0)+1
							FROM	cv_cms_reg
							WHERE	org_no	= \''.$orgNo.'\'
							AND		cms_no	= \''.$R['CMS_NO'].'\'
							AND		cms_dt	= \''.$R['CMS_DT'].'\'';
					$seq = $conn->get_data($sql);
					$IsNew = true;
				}
			}

			//등록
			if ($IsNew){
				$sql = 'INSERT INTO cv_cms_reg (org_no,cms_no,cms_dt,seq,org_nm,in_amt,in_stat,link_stat,insert_id,insert_dt) VALUES (
						 \''.$orgNo.'\'
						,\''.$R['CMS_NO'].'\'
						,\''.$R['CMS_DT'].'\'
						,\''.$seq.'\'
						,\''.$R['ORG_NM'].'\'
						,\''.$R['IN_AMT'].'\'
						,\''.$R['STAT'].'\'
						,\'9\'
						,\''.$_SESSION['userCode'].'\'
						,NOW()
						)';
			}else{
				$sql = 'UPDATE	cv_cms_reg
						SET		org_nm		= \''.$R['ORG_NM'].'\'
						,		in_amt		= \''.$R['IN_AMT'].'\'
						,		in_stat		= \''.$R['STAT'].'\'
						,		update_id	= \''.$_SESSION['userCode'].'\'
						,		update_dt	= NOW()
						WHERE	org_no	= \''.$orgNo.'\'
						AND		cms_no	= \''.$R['CMS_NO'].'\'
						AND		cms_dt	= \''.$R['CMS_DT'].'\'
						AND		seq		= \''.$seq.'\'';
			}
			$query[] = $sql;

			$regAmt += $R['IN_AMT'];
			$regCnt ++;
		}
		*/


		$conn->begin();

		foreach($data as $tmpIdx => $R){
			/*
				'ORG_NO'=>''	//기관기호
			,	'CMS_NO'=>''	//CMS 코드
			,	'CMS_DT'=>''	//청구일
			,	'ORG_NM'=>''	//기관명
			,	'IN_GBN'=>''	//CMS, 무통장
			,	'IN_AMT'=>''	//출금금액
			,	'IN_STAT'=>''	//출금상태 Y, N
			,	'IN_DT'=>''		//출금일
			,	'CMS_MEM_NO'=>''//CMS 회원번호
			,	'CMS_COM'=>''	//효성, 이지스
			*/

			if ($R['IN_AMT'] <= 0) continue;

			if ($R['IN_GBN'] == '2'){ //무통장
				$R['CMS_NO'] = 'BANK'.SubStr($R['CMS_DT'],0,6);
			}

			//연결상태
			$sql = 'SELECT	seq, link_stat
					FROM	cv_cms_reg
					WHERE	org_no		= \''.$R['ORG_NO'].'\'
					AND		cms_mem_no	= \''.$R['CMS_MEM_NO'].'\'
					AND		cms_dt		= \''.$R['CMS_DT'].'\'
					AND		in_gbn		= \''.$R['IN_GBN'].'\'
					AND		in_dt		= \''.$R['IN_DT'].'\'';

			$row = $conn->get_array($sql);

			$linkStat = $row['link_stat'];

			if ($linkStat){
				$IsNew = false;
				$seq[$R['ORG_NO']] = $row['seq'];
			}else{
				$IsNew = true;
				$linkStat = '9';
			}

			Unset($row);


			//연결된 내역이 있으면 건너뛴다.
			if ($linkStat != '9') continue;


			/** 입금적용 ****************************************/
			if ($IsNew){
				//순번
				$sql = 'SELECT	IFNULL(MAX(seq),0)+1
						FROM	cv_cms_reg
						WHERE	org_no	= \''.$R['ORG_NO'].'\'
						AND		cms_no	= \''.$R['CMS_NO'].'\'
						AND		cms_dt	= \''.$R['CMS_DT'].'\'';

				$seq[$R['ORG_NO']] = $conn->get_data($sql);

				$sql = 'INSERT INTO cv_cms_reg (org_no,cms_no,cms_dt,seq,org_nm,in_gbn,in_amt,in_stat,in_dt,cms_com,cms_mem_no,link_stat,excel_yn,insert_id,insert_dt) VALUES (
						 \''.$R['ORG_NO'].'\'
						,\''.$R['CMS_NO'].'\'
						,\''.$R['CMS_DT'].'\'
						,\''.$seq[$R['ORG_NO']].'\'
						,\''.$R['ORG_NM'].'\'
						,\''.$R['IN_GBN'].'\'
						,\''.$R['IN_AMT'].'\'
						,\''.$R['IN_STAT'].'\'
						,\''.$R['IN_DT'].'\'
						,\''.$R['CMS_COM'].'\'
						,\''.$R['CMS_MEM_NO'].'\'
						,\''.$linkStat.'\'
						,\'Y\'
						,\''.$_SESSION['userCode'].'\'
						,NOW()
						)';
			}else{
				$sql = 'UPDATE	cv_cms_reg
						SET		in_gbn		= \''.$R['IN_GBN'].'\'
						,		in_amt		= \''.$R['IN_AMT'].'\'
						,		in_stat		= \''.$R['IN_STAT'].'\'
						,		in_dt		= \''.$R['IN_DT'].'\'
						,		cms_com		= \''.$R['CMS_COM'].'\'
						,		cms_mem_no	= \''.$R['CMS_MEM_NO'].'\'
						,		update_id	= \''.$_SESSION['userCode'].'\'
						,		update_dt	= NOW()
						WHERE	org_no	= \''.$R['ORG_NO'].'\'
						AND		cms_no	= \''.$R['CMS_NO'].'\'
						AND		cms_dt	= \''.$R['CMS_DT'].'\'
						AND		seq		= \''.$seq[$R['ORG_NO']].'\'';
			}

			//$query[] = $sql;
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo $conn->error_msg.'<br>'.$conn->error_query;
				 exit;
			}


			//월별 미입금액 찾기
			if (is_array($orgList[$R['ORG_NO']])){
				//print_r($orgList[$R['ORG_NO']]);
				//foreach($orgList[$R['ORG_NO']] as $tmpYm => $R1){
				foreach($orgList[$R['ORG_NO']] as $tmpYm => $R1){
					if ($data[$tmpIdx]['IN_AMT'] < 1) break;
					if ($tmpYm != $myF->dateAdd('month',-1,$R['CMS_DT'],'Ym')) continue;

					$misuAmt = $R1['acctAmt'] - $orgList[$R['ORG_NO']][$tmpYm]['linkAmt']; //미수금액

					if ($misuAmt > 0){
						//if ($R['ORG_NO'] == 'KN73C001') echo $R['ORG_NO'].' / '.$tmpYm.' / '.$R1['acctAmt'].' / '.$orgList[$R['ORG_NO']][$tmpYm]['linkAmt'].' / '.$misuAmt.' / '.$data[$tmpIdx]['IN_AMT'].'<br>';

						if ($data[$tmpIdx]['IN_AMT'] >= $misuAmt){
							$linkAmt = $misuAmt; //연결금액
						}else{
							$linkAmt = $data[$tmpIdx]['IN_AMT']; //연결금액
						}

						$orgList[$R['ORG_NO']][$tmpYm]['linkAmt'] = $linkAmt;
						$data[$tmpIdx]['IN_AMT'] -= $linkAmt;

						if ($data[$tmpIdx]['IN_AMT'] > 0){
							$linkStat = '3'; //일부연결
						}else{
							$linkStat = '1'; //연결완료
						}

						//순번
						$sql = 'SELECT	IFNULL(MAX(seq),0)+1
								FROM	cv_cms_link
								WHERE	org_no	= \''.$R['ORG_NO'].'\'
								AND		yymm	= \''.$tmpYm.'\'';

						$tmpSeq = $conn->get_data($sql);
						$tmpAcctYm = $myF->dateAdd('month', 1, $tmpYm.'01', 'Ym'); //청구년월

						//입금연결내역
						$sql = 'INSERT INTO cv_cms_link (org_no,yymm,seq,acct_ym,cms_no,cms_dt,cms_seq,link_amt,link_stat,org_amt,insert_id,insert_dt) VALUES (
								 \''.$R['ORG_NO'].'\'
								,\''.$tmpYm.'\'
								,\''.$tmpSeq.'\'
								,\''.$tmpAcctYm.'\'
								,\''.$R['CMS_NO'].'\'
								,\''.$R['CMS_DT'].'\'
								,\''.$seq[$R['ORG_NO']].'\'
								,\''.$linkAmt.'\'
								,\'1\'
								,\''.$R['IN_AMT'].'\'
								,\''.$_SESSION['userCode'].'\'
								,NOW()
								)';

						#if ($R['ORG_NO'] == 'KN73C001') echo nl2br($sql);

						//$query[] = $sql;
						if (!$conn->execute($sql)){
							 $conn->rollback();
							 $conn->close();
							 echo $conn->error_msg.'<br>'.$conn->error_query;
							 exit;
						}


						//CMS 등록내역 상태 변경
						$sql = 'UPDATE	cv_cms_reg
								SET		link_stat = \''.$linkStat.'\'
								WHERE	org_no	= \''.$R['ORG_NO'].'\'
								AND		cms_no	= \''.$R['CMS_NO'].'\'
								AND		cms_dt	= \''.$R['CMS_DT'].'\'
								AND		seq		= \''.$seq[$R['ORG_NO']].'\'';

						//$query[] = $sql;
						if (!$conn->execute($sql)){
							 $conn->rollback();
							 $conn->close();
							 echo $conn->error_msg.'<br>'.$conn->error_query;
							 exit;
						}
					}
				}
			}


			$regAmt += $R['IN_AMT'];
			$regCnt ++;
		}
		Unset($data);

		$conn->commit();
		//$conn->rollback();


		/*
		if (is_array($query)){
			$conn->begin();

			foreach($query as $sql){
				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo $conn->error_msg.'<br>'.$conn->error_query;
					 exit;
				}
			}

			$conn->commit();

			Unset($query);
		}
		*/
	}

	include_once('../inc/_db_close.php');
?>
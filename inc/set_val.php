<?
	//중지설정일자
	$sql = 'SELECT	DATE_FORMAT(ADDDATE(DATE_FORMAT(close_dt, \'%Y-%m-%d\'), interval -1 day), \'%Y%m%d\')
			FROM	stop_set
			WHERE	org_no	 = \''.$userCode.'\'
			AND		stop_gbn = \'1\'
			AND		cls_yn	 = \'N\'
			ORDER	BY close_dt
			LIMIT	1
			';
	$close_dt = $conn->get_data($sql);

	if ($close_dt <= $today){
		$sql = 'SELECT	COUNT(*)
				FROM	cv_reg_info
				WHERE	org_no	= \''.$userCode.'\'
				AND		to_dt	= \''.$close_dt.'\'
				AND		rs_cd	= \'2\'
				';
		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	cv_reg_info
					SET		rs_cd		= \'4\'
					,		rs_dtl_cd	= \'03\'
					WHERE	org_no	= \''.$userCode.'\'
					AND		to_dt	= \''.$close_dt.'\'
					AND		rs_cd	= \'2\'
					';
			$conn->begin();

			if ($conn->execute($sql)){
				$conn->commit();
			}else{
				$conn->rollback();
			}
		}
	}


	/** 계약기간 ****************************************/
		$today = Date('Ymd');
		$sql = 'SELECT	from_dt, to_dt
				FROM	cv_reg_info
				WHERE	org_no	 = \''.$userCode.'\'
				AND		from_dt <= \''.$today.'\'
				AND		to_dt	>= \''.$today.'\'';
		$R = $conn->get_array($sql);
		$contFrom = $R['from_dt'];
		$contTo = $R['to_dt'];

		$sql = 'UPDATE	b02center
				SET		from_dt		= \''.$contFrom.'\'
				,		to_dt		= \''.$contTo.'\'
				WHERE	b02_center	= \''.$userCode.'\'';
		$valQuery[] = $sql;


	/** 변경서비스 적용 *******************************************************/
	$sql = 'SELECT	COUNT(*)
			FROM	cv_svc_fee
			WHERE	org_no	= \''.$userCode.'\'';

	$tmpCnt = $conn->get_data($sql);

	if ($tmpCnt > 0){
		$today = Date('Ymd');

		$sql = 'SELECT	b02_homecare AS homecare
				,		b02_voucher AS voucher
				,		b02_caresvc AS sr_yn
				,		care_support AS care_s
				,		care_resource AS care_r
				FROM	b02center
				WHERE	b02_center = \''.$userCode.'\'';
		$R = $conn->get_array($sql);
		$homecare = $R['homecare']; //재가요양
		$nurse = $R['voucher'][0]; //가사간병
		$old = $R['voucher'][1]; //노인돌봄
		$baby = $R['voucher'][2]; //산모신생아
		$dis = $R['voucher'][4]; //장애인활동지원
		$care_s = $R['care_s']; //재가지원
		$care_r = $R['care_r']; //자원연계
		Unset($R);


		//주야간보호
		$sql = 'SELECT	seq, from_dt, to_dt, \'Y\' AS yn
				FROM	sub_svc
				WHERE	org_no	 = \''.$userCode.'\'
				AND		svc_cd	 = \'5\'
				AND		from_dt <= NOW()
				AND		to_dt	>= NOW()
				AND		del_flag = \'N\'';
		$R = $conn->get_array($sql);
		$dan['new'] = 'N';
		$dan['yn'] = $R['yn'];
		$dan['seq'] = $R['seq'];
		$dan['from_dt'] = $R['from_dt'];
		$dan['to_dt'] = $R['to_dt'];
		Unset($R);


		//복지용구
		$sql = 'SELECT	seq, from_dt, to_dt, \'Y\' AS yn
				FROM	sub_svc
				WHERE	org_no	 = \''.$userCode.'\'
				AND		svc_cd	 = \'7\'
				AND		from_dt <= NOW()
				AND		to_dt	>= NOW()
				AND		del_flag = \'N\'';
		$R = $conn->get_array($sql);
		$wmd['new'] = 'N';
		$wmd['yn'] = $R['yn'];
		$wmd['seq'] = $R['seq'];
		$wmd['from_dt'] = $R['from_dt'];
		$wmd['to_dt'] = $R['to_dt'];
		Unset($R);


		//스마트폰
		$sql = 'SELECT	seq, from_dt, to_dt, \'Y\' AS yn
				FROM	smart_acct
				WHERE	org_no	 = \''.$userCode.'\'
				AND		from_dt <= NOW()
				AND		to_dt	>= NOW()';
		$R = $conn->get_array($sql);
		$smart['new'] = 'N';
		$smart['yn'] = $R['yn'];
		$smart['seq'] = $R['seq'];
		$smart['from_dt'] = $R['from_dt'];
		$smart['to_dt'] = $R['to_dt'];
		Unset($R);


		//SMS
		$sql = 'SELECT	seq, from_dt, to_dt, \'Y\' AS yn
				FROM	sms_acct
				WHERE	org_no	 = \''.$userCode.'\'
				AND		from_dt <= NOW()
				AND		to_dt	>= NOW()';
		$R = $conn->get_array($sql);
		$sms['new'] = 'N';
		$sms['yn'] = $R['yn'];
		$sms['seq'] = $R['seq'];
		$sms['from_dt'] = $R['from_dt'];
		$sms['to_dt'] = $R['to_dt'];
		Unset($R);


		/** 변경내용 **********************************************************************************/
		function GetSvcYn($conn, $orgNo, $svcGbn, $svcCd, $today){
			$sql = 'SELECT	from_dt, to_dt
					FROM	cv_svc_fee
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		svc_gbn	 = \''.$svcGbn.'\'
					AND		svc_cd	 = \''.$svcCd.'\'
					AND		use_yn	 = \'Y\'
					AND		del_flag = \'N\'';
			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			Unset($arr);

			if ($rowCnt > 0){
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);

					$fromDt = str_replace('-','',$row['from_dt']);
					$toDt	= str_replace('-','',$row['to_dt']);

					if ($today >= $fromDt && $today <= $toDt){
						$arr['yn'] = 'Y';
						$arr['from_dt'] = $fromDt;
						$arr['to_dt'] = $toDt;
						break;
					}else{
						$arr['yn'] = 'N';
						$arr['from_dt'] = $fromDt;
						$arr['to_dt'] = $toDt;
					}
				}
			}else{
				$arr['yn'] = 'N';
			}

			$conn->row_free();

			return $arr;
		}

		//재가요양
		$new = GetSvcYn($conn, $userCode, '1', '11', $today);
		if ($new) $homecare = $new['yn']; //설정변경

		//가사간병
		$new = GetSvcYn($conn, $userCode, '1', '21', $today);
		if ($new) $nurse = $new['yn']; //설정변경

		//노인돌봄
		$new = GetSvcYn($conn, $userCode, '1', '22', $today);
		if ($new) $old = $new['yn']; //설정변경

		//산모신생아
		$new = GetSvcYn($conn, $userCode, '1', '23', $today);
		if ($new) $baby = $new['yn']; //설정변경

		//장애인활동지원
		$new = GetSvcYn($conn, $userCode, '1', '24', $today);
		if ($new) $dis = $new['yn']; //설정변경

		//재가지원
		$new = GetSvcYn($conn, $userCode, '1', '41', $today);
		if ($new) $care_s = $new['yn']; //설정변경

		//자원연계
		$new = GetSvcYn($conn, $userCode, '1', '42', $today);
		if ($new) $care_r = $new['yn']; //설정변경

		//주야간보호
		$new = GetSvcYn($conn, $userCode, '1', '14', $today);
		if ($new){
			//$dan['seq'] = $R['seq'];
			$dan['new'] = 'Y';
			$dan['yn'] = $new['yn'];
			$dan['from_dt'] = $new['from_dt'];
			$dan['to_dt'] = $new['to_dt'];
		}


		//복지용구
		$new = GetSvcYn($conn, $userCode, '1', '15', $today);
		if ($new){
			$wmd['new'] = 'Y';
			$wmd['yn'] = $new['yn'];
			$wmd['from_dt'] = $new['from_dt'];
			$wmd['to_dt'] = $new['to_dt'];
		}


		//스마트폰
		$new1 = GetSvcYn($conn, $userCode, '2', '11', $today); //관리자
		$new2 = GetSvcYn($conn, $userCode, '2', '12', $today); //사회복지사
		$new3 = GetSvcYn($conn, $userCode, '2', '13', $today); //요양보호사
		if ($new1['yn'] == 'Y'){
			$smart['new'] = 'Y';
			$smart['yn'] = $new1['yn'];
			$smart['from_dt'] = $new1['from_dt'];
			$smart['to_dt'] = $new1['to_dt'];
		}else if ($new2['yn'] == 'Y'){
			$smart['new'] = 'Y';
			$smart['yn'] = $new2['yn'];
			$smart['from_dt'] = $new2['from_dt'];
			$smart['to_dt'] = $new2['to_dt'];
		}else if ($new3['yn'] == 'Y'){
			$smart['new'] = 'Y';
			$smart['yn'] = $new3['yn'];
			$smart['from_dt'] = $new3['from_dt'];
			$smart['to_dt'] = $new3['to_dt'];
		}


		//SMS
		$new = GetSvcYn($conn, $userCode, '2', '21', $today);
		if ($new){
			$sms['new'] = 'Y';
			$sms['yn'] = $new['yn'];
			$sms['from_dt'] = $new['from_dt'];
			$sms['to_dt'] = $new['to_dt'];
		}


		//echo $homecare.'/'.$nurse.'/'.$old.'/'.$baby.'/'.$dis.'/'.$care_s.'/'.$care_r.chr(13);
		$sql = 'UPDATE	b02center
				SET		b02_homecare	= \''.$homecare.'\'
				,		b02_voucher		= \''.$nurse.$old.$baby.$dis.'\'
				,		b02_caresvc		= \''.($care_s == 'Y' || $care_r == 'Y' ? 'Y' : 'N').'\'
				,		care_support	= \''.$care_s.'\'
				,		care_resource	= \''.$care_r.'\'
				WHERE	b02_center		= \''.$userCode.'\'';
		$valQuery[] = $sql;


		if ($dan['new'] == 'Y'){
			$sql = 'SELECT	COUNT(*)
					FROM	sub_svc
					WHERE	org_no	= \''.$userCode.'\'
					AND		svc_cd	= \'5\'';
			$cnt = $conn->get_data($sql);

			if ($dan['yn'] == 'Y'){
				if ($cnt > 0){
					$sql = 'UPDATE	sub_svc
							SET		from_dt = \''.$dan['from_dt'].'\'
							,		to_dt	= \''.$dan['to_dt'].'\'
							WHERE	org_no	= \''.$userCode.'\'
							AND		svc_cd	= \'5\'
							AND		seq		= \'1\'';
				}else{
					$sql = 'INSERT INTO sub_svc (org_no,svc_cd,seq,from_dt,to_dt) VALUES (\''.$userCode.'\',\'5\',\'1\',\''.$dan['from_dt'].'\',\''.$dan['to_dt'].'\')';
				}
			}else{
				$sql = 'DELETE
						FROM	sub_svc
						WHERE	org_no	= \''.$userCode.'\'
						AND		svc_cd	= \'5\'
						AND		seq		= \'1\'';
			}
			$valQuery[] = $sql;
		}


		if ($wmd['new'] == 'Y'){
			$sql = 'SELECT	COUNT(*)
					FROM	sub_svc
					WHERE	org_no	= \''.$userCode.'\'
					AND		svc_cd	= \'7\'';
			$cnt = $conn->get_data($sql);

			if ($wmd['yn'] == 'Y'){
				if ($cnt > 0){
					$sql = 'UPDATE	sub_svc
							SET		from_dt = \''.$wmd['from_dt'].'\'
							,		to_dt	= \''.$wmd['to_dt'].'\'
							WHERE	org_no	= \''.$userCode.'\'
							AND		svc_cd	= \'7\'
							AND		seq		= \'1\'';
				}else{
					$sql = 'INSERT INTO sub_svc (org_no,svc_cd,seq,from_dt,to_dt) VALUES (\''.$userCode.'\',\'7\',\'1\',\''.$wmd['from_dt'].'\',\''.$wmd['to_dt'].'\')';
				}
			}else{
				$sql = 'DELETE
						FROM	sub_svc
						WHERE	org_no	= \''.$userCode.'\'
						AND		svc_cd	= \'7\'
						AND		seq		= \'1\'';
			}

			$valQuery[] = $sql;
		}


		if ($smart['new'] == 'Y'){
			$sql = 'SELECT	COUNT(*)
					FROM	smart_acct
					WHERE	org_no	= \''.$userCode.'\'';
			$cnt = $conn->get_data($sql);

			if ($smart['yn'] == 'Y'){
				if ($cnt > 0){
					$sql = 'UPDATE	smart_acct
							SET		from_dt = \''.$smart['from_dt'].'\'
							,		to_dt	= \''.$smart['to_dt'].'\'
							WHERE	org_no	= \''.$userCode.'\'
							AND		seq		= \'1\'';
				}else{
					$sql = 'INSERT INTO smart_acct (org_no,seq,from_dt,to_dt) VALUES (\''.$userCode.'\',\'1\',\''.$smart['from_dt'].'\',\''.$smart['to_dt'].'\')';
				}
			}else{
				$sql = 'DELETE
						FROM	smart_acct
						WHERE	org_no	= \''.$userCode.'\'
						AND		seq		= \'1\'';
			}

			$valQuery[] = $sql;
		}


		if ($sms['new'] == 'Y'){
			$sql = 'SELECT	COUNT(*)
					FROM	sms_acct
					WHERE	org_no	= \''.$userCode.'\'';
			$cnt = $conn->get_data($sql);

			if ($sms['yn'] == 'Y'){
				if ($cnt > 0){
					$sql = 'UPDATE	sms_acct
							SET		from_dt = \''.$sms['from_dt'].'\'
							,		to_dt	= \''.$sms['to_dt'].'\'
							WHERE	org_no	= \''.$userCode.'\'
							AND		seq		= \'1\'';
				}else{
					$sql = 'INSERT INTO sms_acct (org_no,seq,from_dt,to_dt) VALUES (\''.$userCode.'\',\'1\',\''.$sms['from_dt'].'\',\''.$sms['to_dt'].'\')';
				}
			}else{
				$sql = 'DELETE
						FROM	sms_acct
						WHERE	org_no	= \''.$userCode.'\'
						AND		seq		= \'1\'';
			}

			$valQuery[] = $sql;
		}
	}

	if (is_array($valQuery)){
		$conn->begin();

		foreach($valQuery as $sql){
			if ($conn->execute($sql)){
				$conn->commit();
			}else{
				$conn->rollback();
			}
		}
	}
?>
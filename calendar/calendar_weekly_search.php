<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code	= $_POST['code']; //$_SESSION['userCenterCode'];
	$from	= $_POST['from'];
	$to		= $_POST['to'];

	$fromYm	= SubStr($from,0,6);
	$toYm	= SubStr($to,0,6);
	$fromDt	= $myF->dateStyle($from);
	$toDt	= $myF->dateStyle($to);

	$sql = 'SELECT	cld_seq
			,		cld_yymm
			,		cld_no
			,		cld_dt
			,		cld_from
			,		cld_to
			,		cld_fulltime
			,		cld_subject
			,		cld_contents
			,		cld_reg_nm
			FROM	calendar
			WHERE	org_no	= \''.$code.'\'
			AND		cld_yymm= \''.$fromYm.'\'
			AND		cld_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
			AND		del_flag= \'N\'';

	if ($fromYm != $toYm){
		$sql .= '
			UNION	ALL
			SELECT	cld_seq
			,		cld_yymm
			,		cld_no
			,		cld_dt
			,		cld_from
			,		cld_to
			,		cld_fulltime
			,		cld_subject
			,		cld_contents
			,		cld_reg_nm
			FROM	calendar
			WHERE	org_no	= \''.$code.'\'
			AND		cld_yymm= \''.$toYm.'\'
			AND		cld_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
			AND		del_flag= \'N\'';
	}

	$sql .=	'
			ORDER	BY cld_dt,cld_from,cld_seq,cld_no';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$weekly	= Date('w',StrToTime($row['cld_dt']));
		$from	= $myF->time2min($row['cld_from']);
		$to		= $myF->time2min($row['cld_to']);

		/*
		if (Is_Array($arr)){
			#foreach($arr as $arr1){
				if (Is_Array($arr[$weekly])){
					foreach($arr[$weekly] as $tmpSeq => $arr2){
						$useYn = 'N';

						foreach($arr2 as $tmp){
							$tmpArr = $tmp;
							$lbAdd = true;

							if ($tmp['full'] != 'Y'){
								if ($tmp['dt'] == $row['cld_dt']){
									if ($useYn == 'N' && $tmp['from'] <= $from && $tmp['to'] > $from){
										$useYn = 'Y';
									}

									$tmpH1 = SubStr($myF->min2time($tmp['from']),0,2);
									$tmpH2 = SubStr($row['cld_from'],0,2);

									if ($tmpH1 == $tmpH2){
										$liSeq = $tmpSeq;
										$lbAdd = false;
										break;
									}
								}
							}
						}
						#if ($liSeq > 0) break;
						if (!$lbAdd) break;
					}
				}
			#	if ($liSeq > 0) break;
			#}

			if ($lbAdd){
				$liSeq = SizeOf($arr[$weekly]);

				if ($useYn == 'Y'){
					$idx = SizeOf($arr[$weekly][$liSeq]);

					$arr[$weekly][$liSeq][$idx]	= Array(
						'id'	=>'TMP_'.$tmpArr['id']
					,	'seq'	=>$tmpArr['seq']
					,	'yymm'	=>$tmpArr['yymm']
					,	'no'	=>$tmpArr['no']
					,	'dt'	=>$tmpArr['dt']
					,	'from'	=>$tmpArr['from']
					,	'to'	=>$tmpArr['to']
					,	'full'	=>$tmpArr['full']
					,	'title'	=>$tmpArr['title']
					,	'use'	=>'N'
					);
				}
			}

			Unset($tmpArr);

			#if ($tmpDt == $row['cld_dt']){
			#}else{
			#	$tmpDt	= $row['cld_dt'];
			#	if ($liSeq == $seq){
			#		echo $tmpDt.'/'.$row['cld_dt'].'/'.$row['cld_from'].chr(13);
			#		$liSeq ++;
			#	}
			#}

			$seq = $liSeq;

			Unset($liSeq);
		}else{
			$seq = 0;
		}
		*/

		if ($tmpDt == $row['cld_dt']){
			if (Is_Array($arr)){
				if (Is_Array($arr[$weekly])){
					foreach($arr[$weekly] as $tmpSeq => $arr2){
						$useYn = 'N';

						foreach($arr2 as $tmp){
							$lbAdd = true;

							if ($tmp['full'] != 'Y'){
								if ($tmp['dt'] == $row['cld_dt']){
									if ($useYn == 'N' && $tmp['from'] <= $from && $tmp['to'] > $from){
										$useYn = 'Y';
									}

									$tmpH1 = SubStr($myF->min2time($tmp['from']),0,2);
									$tmpH2 = SubStr($row['cld_from'],0,2);

									if ($useYn == 'Y' || $tmpH1 == $tmpH2){
										$seq = $tmpSeq;
										$lbAdd = false;
										break;
									}
								}
							}
						}
						if (!$lbAdd) break;
					}
				}
			}

			if ($lbAdd) $seq ++;
		}else{
			$tmpDt	= $row['cld_dt'];
			$seq ++;
		}

		$idx = SizeOf($arr[$weekly][$seq]);

		$arr[$weekly][$seq][$idx]	= Array(
			'id'	=>$row['cld_yymm'].'_'.$row['cld_seq'].'_'.$row['cld_no']
		,	'seq'	=>$row['cld_seq']
		,	'yymm'	=>$row['cld_yymm']
		,	'no'	=>$row['cld_no']
		,	'dt'	=>$row['cld_dt']
		,	'from'	=>$from
		,	'to'	=>$to
		,	'full'	=>$row['cld_fulltime']
		,	'title'	=>$row['cld_subject']
		,	'use'	=>'Y'
		);
	}

	$conn->row_free();

	if (Is_Array($arr)){
		foreach($arr as $weekly => $arrWeek){
			foreach($arrWeek as $seq => $arrSeq){
				$cnt = SizeOf($arrSeq);
				foreach($arrSeq as $idx => $row){
					$data .= 'key='		.$seq;
					$data .= '&cnt='	.$cnt;
					$data .= '&week='	.$weekly;
					$data .= '&id='		.$row['id'];
					$data .= '&dt='		.$row['dt'];
					$data .= '&from='	.$row['from'];
					$data .= '&to='		.$row['to'];
					$data .= '&seq='	.$row['seq'];
					$data .= '&yymm='	.$row['yymm'];
					$data .= '&no='		.$row['no'];
					$data .= '&full='	.$row['full'];
					$data .= '&title='	.$row['title'];
					$data .= '&use='	.$row['use'];
					$data .= chr(11);
				}
			}
		}

		echo $data;
	}

	include_once('../inc/_db_close.php');
?>
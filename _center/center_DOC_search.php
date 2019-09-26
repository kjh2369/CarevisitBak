<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$orgNm	= $_POST['orgNm'];
	$mgNm	= $_POST['mgNm'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);

	$sql = 'SELECT	a.org_no, a.org_nm, a.mg_nm, b.cont_dt, b.doc_type, b.file_path, b.cancel_yn, b.chk_yn, b.insert_dt
			FROM	(
					SELECT	m00_mcode AS org_no, m00_store_nm AS org_nm, m00_mname AS mg_nm
					FROM	m00center
					INNER	JOIN	cv_doc AS a
							ON		a.org_no = m00_mcode
					WHERE	m00_mcode != \'\'';

	if ($orgNo) $sql .= ' AND m00_mcode LIKE \''.$orgNo.'%\'';
	if ($orgNm) $sql .= ' AND m00_store_nm LIKE \''.$orgNm.'%\'';
	if ($mgNm) $sql .= ' AND m00_mname LIKE \''.$mgNm.'%\'';

	$sql .= '		GROUP	BY	m00_mcode
					ORDER	BY	org_nm
					) AS a
			INNER	JOIN	cv_doc AS b
					ON		b.org_no = a.org_no
			WHERE	a.org_no != \'\'';

	if ($fromDt && $toDt){
		$sql .= ' AND b.insert_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'';
	}else if ($fromDt){
		$sql .= ' AND b.insert_dt <= \''.$fromDt.'\'';
	}else if ($toDt){
		$sql .= ' AND b.insert_dt >= \''.$toDt.'\'';
	}

	$sql .= '
			ORDER	BY	insert_dt desc, org_nm, org_no';

	//echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>'; 31153000200

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$orgNo = $row['org_no'];
		$insertDt= $row['insert_dt'];
		$docGbn= IntVal($row['doc_type']);

		if (!$data[$orgNo]['DOC'][$insertDt]) $data[$orgNo]['DOC'][$insertDt] = Array('1'=>Array(), '2'=>Array(), '3'=>Array());

		$data[$orgNo]['name']= $row['org_nm'];
		$data[$orgNo]['mg']	= $row['mg_nm'];
		$data[$orgNo]['DOC'][$insertDt][$docGbn]['file'] = $row['file_path'];
		$data[$orgNo]['DOC'][$insertDt][$docGbn]['cancel'] = $row['cancel_yn'];
		$data[$orgNo]['DOC'][$insertDt][$docGbn]['check'] = $row['chk_yn'];
		$data[$orgNo]['DOC'][$insertDt][$docGbn]['contDt'] = $row['cont_dt'];

		if ($orgNo.'_'.$insertDt != $tmKey){
			$tmKey = $orgNo.'_'.$insertDt;
			$data[$orgNo]['cnt'] ++;
		}

		//$data[$orgNo]['cnt'] ++;
	}

	$conn->row_free();

	if (is_array($data)){
		$no = 1;

		foreach($data as $orgNo => $R){
			if (!$R['cnt']) $R['cnt'] = 1;?>
			<tr>
			<td class="center" rowspan="<?=$R['cnt'];?>"><?=$no;?></td>
			<td class="center" rowspan="<?=$R['cnt'];?>"><div class="left nowrap" style="width:90px;"><?=$orgNo;?></div></td>
			<td class="center" rowspan="<?=$R['cnt'];?>"><div class="left"><a href="#" onclick="lfSelOrg('<?=$orgNo;?>');"><?=$R['name'];?></a></div></td>
			<td class="center" rowspan="<?=$R['cnt'];?>"><div class="left"><?=$R['mg'];?></div></td><?
			$IsFirst = true;
			foreach($R['DOC'] as $insertDt => $D){
				if ($IsFirst){
					$IsFirst = false;
				}else{?>
					<tr><?
				}?>
				<td class="center"><?=$myF->dateStyle($insertDt,'.');?></td><?
				$loopCnt = 0;
				foreach($D as $gbn => $row){?>
					<td class="center"><?
						if ($row['file']){?>
							<a href="<?=$row['file'];?>" target="_blank">●</a><?
						}?>
					</td><?
					$loopCnt ++;
				}

				for($i=3; $i>$loopCnt; $i--){?>
					<td class="center">&nbsp;</td><?
				}?>
				<td class="center last">
					<div class="left"><?
						foreach($D as $gbn => $row){
							if ($gbn == '1'){
								$gbnStr = '계약서';
							}else if ($gbn == '2'){
								$gbnStr = '등록증';
							}else if ($gbn == '3'){
								$gbnStr = '동의서';
							}else{
								$gbnStr = $gbn;
							}?>
							<span class="btn_pack small"><button onclick="lfDelete('<?=$orgNo;?>','<?=$row['contDt'];?>','<?=$gbn;?>');" <?=!$row['file'] ? 'disabled' : '';?>><?=$gbnStr;?>삭제</button></span><?
						}?>
					</div>
				</td>
				</tr><?
			}

			$no ++;
		}
	}else{?>
		<tr>
			<td class="center last" colspan="8">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>
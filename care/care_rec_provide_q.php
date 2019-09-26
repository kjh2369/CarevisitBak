<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$type	= $_POST['type'];

	if ($type == 'PROVIDE_ITEM'){
		//설정항목
		$sql = 'SELECT	suga_cd
				,		seq
				,		use_yn
				FROM	care_rec_provide_item
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$id = $row['suga_cd'].'_'.$row['seq'];
			$item[$id] = $row['use_yn'];
		}

		$conn->row_free();

		//항목관리
		$sql = 'SELECT	suga.cd1 AS mst_cd
				,		suga.nm1 AS mst_nm
				,		suga.cd2 AS pro_cd
				,		suga.nm2 AS pro_nm
				,		suga.cd3 AS svc_cd
				,		suga.nm3 AS svc_nm
				,		care.suga_cd AS suga_cd
				,		care.suga_sub AS sub_cd
				,		care.suga_seq AS sub_seq
				,		care.suga_nm AS sub_nm
				,		care.suga_cost AS sub_cost
				,		care.from_dt
				,		care.to_dt
				FROM	care_suga AS care
				INNER	JOIN	suga_care AS suga
						ON		cd1 = SUBSTR(care.suga_cd,1,1)
						AND		cd2 = SUBSTR(care.suga_cd,2,2)
						AND		cd3 = SUBSTR(care.suga_cd,4,2)
				WHERE	care.org_no = \''.$orgNo.'\'
				AND		care.suga_sr= \''.$SR.'\'
				ORDER	BY mst_cd, pro_cd, svc_cd, sub_nm,sub_cd,sub_seq DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if (!$data[$row['mst_cd']]){
				 $data[$row['mst_cd']]['nm'] = $row['mst_nm'];
			}
			$data[$row['mst_cd']]['cnt'] ++;

			if (!$data[$row['mst_cd']]['sub'][$row['pro_cd']]){
				 $data[$row['mst_cd']]['sub'][$row['pro_cd']]['nm'] = $row['pro_nm'];
			}
			$data[$row['mst_cd']]['sub'][$row['pro_cd']]['cnt'] ++;

			if (!$data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]){
				 $data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['nm'] = $row['svc_nm'];
			}
			$data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['cnt'] ++;

			if (!$data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['sub'][$row['sub_cd'].'_'.$row['sub_seq']]){
				 $data[$row['mst_cd']]['sub'][$row['pro_cd']]['sub'][$row['svc_cd']]['sub'][$row['sub_cd'].'_'.$row['sub_seq']] = Array(
					 'nm'=>$row['sub_nm']
					,'suga'=>$row['mst_cd'].$row['pro_cd'].$row['svc_cd'].$row['sub_cd']
					,'seq'=>$row['sub_seq']
					,'cost'=>$row['sub_cost']
					,'from'=>$myF->dateStyle($row['from_dt'],'.')
					,'to'=>$myF->dateStyle($row['to_dt'],'.')
				);
			}
		}

		$conn->row_free();

		if (!is_array($data)) exit;

		foreach($data as $mstCd => $mst){?>
			<tr style="cursor:default;">
			<td class="top" rowspan="<?=$mst['cnt'];?>"><div class="left"><?=$mst['nm'];?></div></td><?
			$IsFirst[0] = true;
			foreach($mst['sub'] as $proCd => $pro){
				if (!$IsFirst[0]){?>
					<tr><?
				}?>
				<td class="top" rowspan="<?=$pro['cnt'];?>"><div class="left"><?=$pro['nm'];?></div></td><?
				$IsFirst[0] = false;
				$IsFirst[1] = true;
				foreach($pro['sub'] as $svcCd => $svc){
					if (!$IsFirst[1]){?>
						<tr><?
					}?>
					<td class="top" rowspan="<?=$svc['cnt'];?>"><div class="left"><?=$svc['nm'];?></div></td><?
					$IsFirst[1] = false;
					$IsFirst[2] = true;
					foreach($svc['sub'] as $subCd => $sub){
						if (!$IsFirst[2]){?>
							<tr><?
						}

						$cd = $sub['suga'];
						$seq = $sub['seq'];
						$id = $cd.'_'.$seq;?>
						<td class="clsTd top"><div class="left"><?=$sub['nm'];?></div></td>
						<td class="clsTd center" style="line-height:1.5em;"><div class="left"><?=$sub['from'];?><br>~<?=$sub['to'];?></div></td>
						<td class="clsTd center" id="itemId_<?=$id;?>" onclick="lfSetUse('<?=$cd;?>','<?=$seq;?>');"><a href="#" onclick="return false;"><?=$item[$id] == 'Y' ? '<span style="color:BLUE;">Y</span>' : '<span style="color:RED;">N</span>';?></a></td>
						<td class="clsTd last"></td>
						</tr><?
						$IsFirst[2] = false;
					}
				}
			}
		}


	}else if ($type == 'PROVIDE_ITEM_SET'){
		//항목설정 변경
		$cd	= $_POST['cd'];
		$seq= $_POST['seq'];

		$sql = 'SELECT	use_yn
				FROM	care_rec_provide_item
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		suga_cd	= \''.$cd.'\'
				AND		seq		= \''.$seq.'\'';

		$yn = $conn->get_data($sql);

		if ($yn){
			if ($yn == 'Y'){
				$yn = 'N';
			}else{
				$yn = 'Y';
			}

			$sql = 'UPDATE	care_rec_provide_item
					SET		use_yn		= \''.$yn.'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$orgNo.'\'
					AND		org_type	= \''.$SR.'\'
					AND		suga_cd		= \''.$cd.'\'
					AND		seq			= \''.$seq.'\'';
		}else{
			$yn = 'Y';
			$sql = 'INSERT INTO care_rec_provide_item (
					 org_no
					,org_type
					,suga_cd
					,seq
					,use_yn
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$SR.'\'
					,\''.$cd.'\'
					,\''.$seq.'\'
					,\''.$yn.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		echo $yn;


	}else if ($type == 'SEARCH_CLIENT'){
		//기록지리스트
		$sql = 'SELECT	CONCAT(jumin,\'_\',CAST(RIGHT(yymm,2) AS unsigned)) AS cd
				,		COUNT(seq) AS cnt
				FROM	care_rec_provide_list
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		LEFT(yymm,4)= \''.$year.'\'
				AND		del_flag	= \'N\'
				GROUP	BY jumin, yymm';

		$provideList = $conn->_fetch_array($sql,'cd');

		//대상자리스트
		$sql = 'SELECT	a.jumin
				,		b.jumin AS real_jumin
				,		m03_name AS name
				,		m03_key AS cd
				,		a.mp_gbn
				,		a.use_gbn
				FROM	(
						SELECT	jumin
						,		SUM(mp_gbn) AS mp_gbn
						,		SUM(use_gbn) AS use_gbn
						FROM	(
								SELECT	DISTINCT jumin
								,		CASE WHEN mp_gbn = \'Y\' AND NOW() >= from_dt AND NOW() <= to_dt THEN 1 ELSE 0 END AS mp_gbn
								,		CASE WHEN NOW() >= from_dt AND NOW() <= to_dt THEN 1 ELSE 0 END AS use_gbn
								FROM	client_his_svc
								WHERE	org_no	 = \''.$orgNo.'\'
								AND		svc_cd	 = \''.$SR.'\'
								AND		from_dt	<= \''.$year.'-01-01\'
								AND		to_dt	>= \''.$year.'-12-31\'
								) AS a
						GROUP	BY jumin
						) AS a
				INNER	JOIN	m03sugupja
						ON		m03_ccode = \''.$orgNo.'\'
						AND		m03_mkind = \'6\'
						AND		m03_jumin = a.jumin
				INNER	JOIN	mst_jumin AS b
						ON		b.org_no= m03_ccode
						AND		b.gbn	= \'1\'
						AND		b.code	= a.jumin
				ORDER	BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		if ($rowCnt > 0){
			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);
				$jumin = SubStr($row['real_jumin'].'0000000','0',13);
				$gender = $myF->issToGender($jumin);
				$birth = $myF->issToBirthDay($jumin,'.');

				if ($gender == '남'){
					$gender = '<span style="color:BLUE;">남</span>';
				}else if ($gender == '여'){
					$gender = '<span style="color:RED;">여</span>';
				}?>
				<tr>
					<td class="td center"><?=$no;?></td>
					<td class="td center"><div class="left nowrap" style="width:70px;"><?=$row['name'];?></div></td>
					<td class="td center"><?=$gender;?></td>
					<td class="td center"><?=$birth;?></td>
					<td class="td center"><?=$row['mp_gbn'] > 0 ? 'Y' : '';?></td>
					<td class="center"></td><?
					for($j=1; $j<=12; $j++){?>
						<td id="cellId_<?=$row['cd'];?>_<?=$j;?>" jumin="<?=$ed->en($row['jumin']);?>" month="<?=$j;?>" class="center <?=$j == 12 ? 'last' : '';?>" onmouseover="this.style.backgroundColor='#EFEFEF';" onmouseout="this.style.backgroundColor='#FFFFFF';" onclick="lfReg(this);"><a href="#" onclick="return false;"><?=$provideList[$row['jumin'].'_'.$j]['cnt'];?></a></td><?
					}?>
				</tr><?

				$no ++;
			}
		}else{?>
			<tr>
				<td class="center last" colspan="16">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}

		$conn->row_free();


	}else if ($type == 'SVC_LIST'){
		//등록내용
		$jumin	= $ed->de($_POST['jumin']);
		$seq	= $_POST['seq'];

		if ($jumin && $seq){
			$sql = 'SELECT	suga_cd
					,		contents
					FROM	care_rec_provide_svc
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$SR.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		yymm	= \''.$year.$month.'\'
					AND		seq		= \''.$seq.'\'';

			$provideSvc = $conn->_fetch_array($sql,'suga_cd');
		}

		//서비스 리스트
		$sql = 'SELECT	a.suga_cd
				,		a.seq
				,		b.suga_nm
				FROM	care_rec_provide_item AS a
				INNER	JOIN	care_suga AS b
						ON		b.org_no	= a.org_no
						AND		b.suga_sr	= a.org_type
						AND		b.suga_cd	= SUBSTR(a.suga_cd,1,5)
						AND		b.suga_sub	= SUBSTR(a.suga_cd,6)
						AND		b.suga_seq	= a.seq
						AND		DATE_FORMAT(b.from_dt,	\'%Y%m\') <= \''.$year.$month.'\'
						AND		DATE_FORMAT(b.to_dt,	\'%Y%m\') >= \''.$year.$month.'\'
				WHERE	a.org_no	= \''.$orgNo.'\'
				AND		a.org_type	= \''.$SR.'\'
				AND		a.use_yn	= \'Y\'
				ORDER	BY SUBSTR(a.suga_cd,1,5), suga_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr id="trSvc_<?=$row['suga_cd'];?>" style="display:none;">
				<th><?=$row['suga_nm'];?></th>
				<td><textarea id="<?=$row['suga_cd'];?>_<?=$row['seq'];?>" style="width:100%; height:35px;"><?=StripSlashes($provideSvc[$row['suga_cd']]['contents']);?></textarea></td>
			</tr><?
		}

		$conn->row_free();


	}else if ($type == 'SVC_INFO'){
		//조회
		$jumin	= $ed->de($_POST['jumin']);
		$seq	= $_POST['seq'];

		$sql = 'SELECT	reg_dt
				,		from_time
				,		to_time
				,		mem_cd
				,		other
				FROM	care_rec_provide_list
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$year.$month.'\'
				AND		seq		= \''.$seq.'\'
				AND		del_flag= \'N\'';

		$row = $conn->get_array($sql);

		$sql = 'SELECT	DISTINCT m02_yname
				FROM	m02yoyangsa
				WHERE	m02_ccode	= \''.$orgNo.'\'
				AND		m02_yjumin	= \''.$row['mem_cd'].'\'';

		$name = $conn->get_data($sql);

		$data = 'date='	.$row['reg_dt']
			  . '&from='.$myF->timeStyle($row['from_time'])
			  . '&to='	.$myF->timeStyle($row['to_time'])
			  . '&mem='	.$ed->en($row['mem_cd'])
			  . '&other='.StripSlashes($row['other'])
			  . '&name='.$name
		;

		Unset($row);

		echo $data;


	}else if ($type == 'REC_LIST'){
		//제공기록지 리스트
		$jumin		= $ed->de($_POST['jumin']);
		$year		= $_POST['year'];
		$month		= $_POST['month'];

		$sql = 'SELECT	a.seq
				,		a.reg_dt
				,		a.from_time
				,		a.to_time
				,		m02_yname AS name
				FROM	care_rec_provide_list AS a
				INNER	JOIN	m02yoyangsa
						ON		m02_ccode	= a.org_no
						AND		m02_mkind	= \'0\'
						AND		m02_yjumin	= a.mem_cd
				WHERE	a.org_no	= \''.$orgNo.'\'
				AND		a.org_type	= \''.$SR.'\'
				AND		a.jumin		= \''.$jumin.'\'
				AND		a.yymm		= \''.$year.$month.'\'
				AND		a.del_flag	= \'N\'
				ORDER	BY reg_dt, from_time, to_time';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($i % 2 == 0){
				$bgclr = 'FFFFFF';
			}else{
				$bgclr = 'F3F3F3';
			}?>
			<tbody style="background-color:#<?=$bgclr;?>;" seq="<?=$row['seq'];?>" bgclr="<?=$bgclr;?>">
				<tr>
					<td class="center" rowspan="2"><?=$no;?>회</td>
					<td class="left"><?=$myF->dateStyle($row['reg_dt'],'.');?></td>
					<td class="left last" rowspan="2"><?=$row['name'];?></td>
				</tr>
				<tr>
					<td class="left"><?=$myF->timeStyle($row['from_time']);?> ~ <?=$myF->timeStyle($row['to_time']);?></td>
				</tr>
			</tbody><?

			$no ++;
		}

		$conn->row_free();


	}else if ($type == 'SAVE'){
		//저장
		$jumin		= $ed->de($_POST['jumin']);
		$year		= $_POST['year'];
		$month		= $_POST['month'];
		$seq		= $_POST['seq'];
		$date		= $_POST['date'];
		$fromTime	= str_replace(':','',$_POST['fromTime']);
		$toTime		= str_replace(':','',$_POST['toTime']);
		$memCd		= $ed->de($_POST['memCd']);
		$other		= AddSlashes($_POST['other']);
		$data		= $_POST['data'];

		$sql = 'SELECT	COUNT(*)
				FROM	care_rec_provide_list
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$year.$month.'\'
				AND		seq		= \''.$seq.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	care_rec_provide_list
					SET		reg_dt		= \''.$date.'\'
					,		from_time	= \''.$fromTime.'\'
					,		to_time		= \''.$toTime.'\'
					,		mem_cd		= \''.$memCd.'\'
					,		other		= \''.$other.'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$orgNo.'\'
					AND		org_type	= \''.$SR.'\'
					AND		jumin		= \''.$jumin.'\'
					AND		yymm		= \''.$year.$month.'\'
					AND		seq			= \''.$seq.'\'';

			$query[] = $sql;

			$sql = 'DELETE
					FROM	care_rec_provide_svc
					WHERE	org_no		= \''.$orgNo.'\'
					AND		org_type	= \''.$SR.'\'
					AND		jumin		= \''.$jumin.'\'
					AND		yymm		= \''.$year.$month.'\'
					AND		seq			= \''.$seq.'\'';

			$query[] = $sql;
		}else{
			$sql = 'SELECT	IFNULL(MAX(seq), 0) + 1
					FROM	care_rec_provide_svc
					WHERE	org_no		= \''.$orgNo.'\'
					AND		org_type	= \''.$SR.'\'
					AND		jumin		= \''.$jumin.'\'
					AND		yymm		= \''.$year.$month.'\'';

			$seq = $conn->get_data($sql);

			$sql = 'INSERT INTO care_rec_provide_list (
					 org_no
					,org_type
					,jumin
					,yymm
					,seq
					,reg_dt
					,from_time
					,to_time
					,mem_cd
					,other
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$SR.'\'
					,\''.$jumin.'\'
					,\''.$year.$month.'\'
					,\''.$seq.'\'
					,\''.$date.'\'
					,\''.$fromTime.'\'
					,\''.$toTime.'\'
					,\''.$memCd.'\'
					,\''.$other.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';

			$query[] = $sql;
		}

		parse_str($data, $svc);

		if (is_array($svc)){
			$no = 1;

			foreach($svc as $tmp => $val){
				if ($val){
					$suga = Explode('_',$tmp);
					$sql = 'INSERT INTO care_rec_provide_svc (
							 org_no
							,org_type
							,jumin
							,yymm
							,seq
							,no
							,suga_cd
							,contents) VALUES (
							 \''.$orgNo.'\'
							,\''.$SR.'\'
							,\''.$jumin.'\'
							,\''.$year.$month.'\'
							,\''.$seq.'\'
							,\''.$no.'\'
							,\''.$suga[0].'\'
							,\''.AddSlashes($val).'\'
							)';
					$query[] = $sql;
					$no ++;
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
			echo 1;
		}


	}else if ($type == 'DELETE'){
		//삭제
		$jumin	= $ed->de($_POST['jumin']);
		$seq	= $_POST['seq'];

		$sql = 'UPDATE	care_rec_provide_list
				SET		del_flag	= \'Y\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		jumin		= \''.$jumin.'\'
				AND		yymm		= \''.$year.$month.'\'
				AND		seq			= \''.$seq.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		echo 1;

	}else if ($type == 'SVC_DAY'){
		//일정 등록 서비스
		$jumin	= $ed->de($_POST['jumin']);
		$date	= str_replace('-','',$_POST['date']);

		$sql = 'SELECT	DISTINCT
						t01_suga_code1 AS cd
				FROM	t01iljung
				WHERE	t01_ccode		= \''.$orgNo.'\'
				AND		t01_mkind		= \''.$SR.'\'
				AND		t01_jumin		= \''.$jumin.'\'
				AND		t01_sugup_date	= \''.$date.'\'
				AND		t01_del_yn		= \'N\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$data .= ($data ? '/' : '').$row['cd'];
		}

		$conn->row_free();

		echo $data;


	}else{
	}

	include_once('../inc/_db_close.php');
?>
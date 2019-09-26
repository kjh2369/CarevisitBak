<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$kind = $_POST['kind'];
	$seq  = $_POST['seq'];
	$mode = $_POST['mode'];

	if (Empty($code)){
		exit;
	}

	$sql = 'SELECT	from_dt
			,		to_dt
			FROM	insu_center
			WHERE	org_no   = \''.$code.'\'';

	if (Is_Numeric($seq)){
		$sql .= '
			AND		seq = \''.$seq.'\'';
	}else{
		$sql .= '
			AND		from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
			AND		to_dt   >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
	}

	$sql .= '
			ORDER	BY seq DESC
			LIMIT 1';

	$laDt = $conn->get_array($sql);

	//직원리스트
	if ($mode == '1'){
		$sql = 'SELECT	g01_name AS insu_nm
				,		mst.jumin
				,		mst.name
				,		his.join_dt
				,		his.quit_dt
				,		insu.start_dt
				,		insu.end_dt
				,		IFNULL(insu.stat,\'0\') AS stat
				FROM	mem_his AS his
				INNER	JOIN (
						SELECT	DISTINCT
								m02_yjumin AS jumin
						,		m02_yname AS name
						FROM	m02yoyangsa
						WHERE	m02_ccode = \''.$code.'\'
						) AS mst
						ON		mst.jumin = his.jumin
				LEFT	JOIN (
						SELECT	insu_cd
						,		jumin
						,		join_dt
						,		start_dt
						,		end_dt
						,		stat
						FROM	insu
						WHERE	org_no = \''.$code.'\'
						) AS insu
						ON		mst.jumin   = insu.jumin
						AND		his.join_dt = insu.join_dt
				INNER	JOIN g01ins
						ON		g01_code = insu.insu_cd
				WHERE	org_no = \''.$code.'\'
				AND		his.join_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
				AND		his.insu_yn = \'Y\'
				AND		IFNULL(insu.stat,\'0\') = \'0\'
				ORDER	BY start_dt, end_dt, name';
	}else if ($mode == '3'){
		$sql = 'SELECT	insu.insu_cd
				,		g01_name AS insu_nm
				,		mst.jumin
				,		mst.name
				,		his.join_dt
				,		his.quit_dt
				,		insu.start_dt
				,		insu.end_dt
				,		IFNULL(insu.stat,\'0\') AS stat
				,		insu.seq
				FROM	mem_his AS his
				INNER	JOIN (
							SELECT	DISTINCT
									m02_yjumin AS jumin
							,		m02_yname AS name
							FROM	m02yoyangsa
							WHERE	m02_ccode = \''.$code.'\'
						) AS mst
						ON	mst.jumin = his.jumin
				INNER	JOIN (
							SELECT	insu_cd
							,		jumin
							,		join_dt
							,		start_dt
							,		end_dt
							,		stat
							,		seq
							FROM	insu
							WHERE	org_no = \''.$code.'\'
							AND		stat   = \'1\'
							UNION	ALL
							SELECT	insu_cd
							,		jumin
							,		join_dt
							,		start_dt
							,		end_dt
							,		stat
							,		seq
							FROM	insu
							WHERE	org_no = \''.$code.'\'
							AND		stat   = \'7\'
					   ) AS insu
						ON	mst.jumin	= insu.jumin
						AND	his.join_dt = insu.join_dt
				INNER	JOIN g01ins
						ON	g01_code = insu.insu_cd
				WHERE	org_no = \''.$code.'\'
				ORDER	BY start_dt, end_dt, name';
	}else if ($mode == '5'){
		$sql = 'SELECT	g01_name AS insu_nm
				,		mst.jumin
				,		mst.name
				,		his.join_dt
				,		his.quit_dt
				,		insu.start_dt
				,		insu.end_dt
				,		IFNULL(insu.stat,\'0\') AS stat
				FROM	mem_his AS his
				INNER	JOIN (
							SELECT	DISTINCT
									m02_yjumin AS jumin
							,		m02_yname AS name
							FROM	m02yoyangsa
							WHERE	m02_ccode = \''.$code.'\'
						) AS mst
					ON	mst.jumin = his.jumin
				INNER	JOIN (
						SELECT	insu_cd
						,		jumin
						,		join_dt
						,		start_dt
						,		end_dt
						,		stat
						FROM	insu
						WHERE	org_no = \''.$code.'\'
						AND		stat   > \'1\'';

		if (Is_Numeric($seq)){
			$sql .= '	AND		start_dt >= \''.$laDt['from_dt'].'\'
						AND		start_dt <  \''.$laDt['to_dt'].'\'
						AND		IFNULL(end_dt,\''.$laDt['to_dt'].'\') <= \''.$laDt['to_dt'].'\'';
		}else{
			$sql .= '
						AND		DATE_FORMAT(start_dt,\'%Y%m\') = DATE_FORMAT(NOW(),\'%Y%m\')
						UNION	ALL
						SELECT	insu_cd
						,		jumin
						,		join_dt
						,		start_dt
						,		end_dt
						,		stat
						FROM	insu
						WHERE	org_no = \''.$code.'\'
						AND		stat   = \'9\'
						AND		DATE_FORMAT(start_dt,\'%Y%m\') = DATE_FORMAT(NOW(),\'%Y%m\')';
		}

		$sql .= '		) AS insu
						ON		mst.jumin   = insu.jumin
						AND		his.join_dt = insu.join_dt
				INNER	JOIN g01ins
						ON		g01_code = insu.insu_cd
				WHERE	org_no = \''.$code.'\'
				ORDER	BY start_dt, end_dt, name';
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$lsStartDt = $row['join_dt']; //입사일
		//$lsEndDt   = $row['quit_dt']; //퇴사일

		if ($mode == '1'){
			if ($laDt['from_dt'] > $row['join_dt']){
				$lsStartDt = $laDt['from_dt'];
			}

		//	if ($laDt['to_dt'] < $row['quit_dt']){
		//		$lsEndDt = $laDt['to_dt'];
		//	}
		}else{
			$lsStartDt = $row['start_dt'];
		}

		if ($row['stat'] == '0' && $row['stat'] != ''){
			//미가입 퇴사
			$stat = ' ';
		}else if ($row['stat'] == '0' && $row['stat'] == ''){
			//가입가능
			$stat = ' ';
			//link = '<span class="btn_pack m"><button type="button" onclick="lfRegInsu(\''+idx+'\',\''+val[7]+'\',\''+val[2]+'\',\''+val[4]+'\');">배상책임보험 가입</button></span>';
		}else if ($row['stat'] == '1'){
			$stat = '가입신청';
		}else if ($row['stat'] == '3'){
			$stat = '가입완료';
		}else if ($row['stat'] == '7'){
			$stat = '해지신청';
		}else if ($row['stat'] == '9'){
			$stat = '해지완료';
		}?>
		<tr>
			<td class="center"><?=$i+1;?></td>
			<td class="center"><div class="left"><?=$row['insu_nm'];?></div></td>
			<td class="center"><?=$row['name'];?></td>
			<td class="center"><?=$myF->issStyle($row['jumin']);?></td>
			<td class="center"><?=$myF->dateStyle($row['join_dt'],'.');?></td>
			<td class="center"><?=$myF->dateStyle($row['quit_dt'],'.');?></td>
			<td class="center"><?=$myF->dateStyle($lsStartDt,'.');?></td>
			<td class="center"><?=$myF->dateStyle($row['end_dt'],'.');?></td>
			<td class="center"><?=$stat;?></td>
			<td class="center last"><?
				if ($mode == '3'){?>
					<div class="left"><span class="btn_pack m"><button type="button" onclick="lfCancel('<?=$row['insu_cd'];?>','<?=$ed->en($row['jumin']);?>','<?=$row['seq'];?>');">신청취소</button></span></div><?
				}?>
			</td>
		</tr><?

		/*
		$data .= $myF->issStyle($row['jumin']).chr(2)
			  .  $row['name'].chr(2)
			  .  $row['join_dt'].chr(2)
			  .  $row['quit_dt'].chr(2)
			  .  $lsStartDt.chr(2)
			  .  $row['end_dt'].chr(2)
			  .  $row['stat'].chr(2)
			  .  $ed->en($row['jumin']).chr(1);
		*/
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>
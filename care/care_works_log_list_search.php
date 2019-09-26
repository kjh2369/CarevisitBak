<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	$order	= $_POST['order'];
	$gbn	= $_POST['gbn'];
	$weekly	= Array(0=>'<span style="color:RED;">일</span>',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'<span style="color:BLUE;">토</span>');
	$gender	= Array('남'=>'<span style="color:BLUE;">남</span>', '여'=>'<span style="color:RED;">여</span>');
	

	$sql = 'SELECT	a.date
			,		a.time
			,		a.seq
			,		a.jumin AS jumin_cd
			,		m03_name AS name
			,		a.suga_cd
			,		c.suga_nm
			,		a.res_cd
			,		a.res_nm
			,		a.mem_cd
			,		a.mem_nm
			,		a.week
			FROM	(
					SELECT	t01_sugup_date AS date
					,		t01_sugup_fmtime AS time
					,		t01_sugup_seq AS seq
					,		t01_jumin AS jumin
					,		t01_suga_code1 AS suga_cd
					,		t01_yoyangsa_id1 AS res_cd
					,		t01_yname1 AS res_nm
					,		t01_yoyangsa_id2 AS mem_cd
					,		t01_yname2 AS mem_nm
					,		t01_sugup_yoil AS week
					FROM	t01iljung
					WHERE	t01_ccode	= \''.$orgNo.'\'
					AND		t01_mkind	= \''.$SR.'\'
					AND		t01_del_yn	= \'N\'
					AND		t01_sugup_date BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
					) AS a
			INNER	JOIN	m03sugupja AS b
					ON		m03_ccode = \''.$orgNo.'\'
					AND		m03_mkind = \'6\'
					AND		m03_jumin = a.jumin
			INNER	JOIN	care_suga AS c
					ON		c.org_no	= \''.$orgNo.'\'
					AND		c.suga_sr	= \''.$SR.'\'
					AND		CONCAT(c.suga_cd, c.suga_sub)	 = a.suga_cd
					AND		REPLACE(c.from_dt,\'-\',\'\')	<= a.date
					AND		REPLACE(c.to_dt,\'-\',\'\')		>= a.date
			ORDER	BY ';


	switch($order){
		case '1':
			$sql .= 'date, time, name';
			break;

		case '2':
			$sql .= 'mem_nm, date, time';
			break;

		case '3':
			$sql .= 'suga_nm, date, time';
			break;

		case '4':
			$sql .= 'name, date, time';
			break;
	}

	$sql .= ', suga_cd';
	
	$rows = $conn->_fetch_array($sql);
	$no = 1;
	
	//if($debug) echo '<tr><td colspan="8">'.nl2br($sql).'</td></tr>';
	
	for($i=0; $i<count($rows); $i++){
		$row = $rows[$i];
		
		//대상자 주민번호조회
		$sql = 'SELECT	LEFT(CONCAT(IFNULL(jumin, '.$row['jumin_cd'].'), \'0000000000000\'), 13) AS jumin
				FROM    mst_jumin AS d
				WHERE	org_no	= \''.$orgNo.'\'
				AND		gbn		= \'1\'
				AND		code	= \''.$row['jumin_cd'].'\'';
		$cd   = $conn->get_data($sql); 
		
		$row['jumin'] = $cd;

		
		$sql = 'SELECT	contents AS cont_new, pic_nm AS pic_new, file_path AS path_new
				FROM	care_works_log
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		jumin		= \''.$row['jumin_cd'].'\'
				AND		suga_cd		= \''.$row['suga_cd'].'\'
				AND		resource_cd	= \''.$row['res_cd'].'\'
				AND		mem_cd		= \''.$row['mem_cd'].'\'
				AND		date		= \''.$row['date'].'\'
				';
		
		$R = $conn->get_array($sql);

		
		if ($R['cont_new']){
			$cont = StripSlashes($R['cont_new']);
			$pic = $R['pic_new'];
			$path = $R['path_new'];
		}else{
			$sql = 'SELECT	content AS cont_old
				,		picture AS pic_old
				FROM    care_result
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		jumin		= \''.$row['jumin_cd'].'\'
				AND		date		= \''.$row['date'].'\'
				AND		time		= \''.$row['time'].'\'
				AND		seq			= \''.$row['seq'].'\'
				AND		no			= \'1\'';
			$rst = $conn->get_array($sql);
			
			$cont = StripSlashes($rst['cont_old']);
			$pic = $rst['pic_old'];
			$path = '../care/pic/'.$pic;
		}

		unset($R);

		if ($pic){
			$pic = '<img src="../image/f_list.gif" border="0">';
		}

		//$obj = '{\'date\':\''.$row['date'].'\', \'time\':\''.$row['time'].'\', \'seq\':\''.$row['seq'].'\', \'jumin\':\''.$ed->en($row['jumin_cd']).'\', \'suga\':\''.$row['suga_cd'].'\', \'res\':\''.$row['res_cd'].'\', \'mem\':\''.$ed->en($row['mem_cd']).'\'}';

		$para = 'date='.$row['date'].'&time='.$row['time'].'&seq='.$row['seq'].'&jumin='.$ed->en($row['jumin_cd']).'&suga='.$row['suga_cd'].'&res='.$row['res_cd'].'&mem='.$ed->en($row['mem_cd']);
		
		if ($gbn == 'Y' && $cont){
			if ($IsExcelClass){
				$rowNo ++;
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>$no) );
				$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>$myF->dateStyle($row['date'],'.')."(".$weekly[$row['week']].")\n ".$myF->timeStyle($row['time'])) );
				$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>$row['name'].'('.$myF->issToGender($row['jumin']).')') );
				$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>$myF->issToBirthday($row['jumin'],'.')) );
				$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$row['suga_nm'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$row['res_nm'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>$row['mem_nm'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$cont, 'H'=>'L') );
				$sheet->SetData( Array('F'=>'I'.$rowNo, 'H'=>'L') );
				$sheet->getStyle('H'.$rowNo)->getAlignment()->setWrapText(true);
				$sheet->getRowDimension($rowNo)->setRowHeight(-1);
			}else{?>
				<tr id="ID_ROW_<?=$i;?>" onclick="lfWorkLogReg('<?=$para;?>',this);" style="cursor:default;" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';">
					<td class="center"><?=$no;?></td>
					<td class="center"><?=$myF->dateStyle($row['date'],'.');?>(<?=$weekly[$row['week']];?>) <?=$myF->timeStyle($row['time']);?></td>
					<td class="center"><?=$row['name'];?>(<?=$gender[$myF->issToGender($row['jumin'])];?>)</td>
					<td class="center"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
					<td class="center"><div class="nowrap left" style="width:100px;"><?=$row['suga_nm'];?></div></td>
					<td class="center"><div class="nowrap left" style="width:100px;"><?=$row['res_nm'];?></div></td>
					<td class="center"><?=$row['mem_nm'];?></td>
					<td class="center"><div id="ID_CONTENTS" class="nowrap left" style="width:150px;"><?=$cont;?></div></td><?
					if (!$IsExcel){?>
						<td class="center"><div id="ID_PICTURE"><?=$pic;?></div></td><?
					}?>
					<td class="center last"></td>
				</tr><?
			}

			$no ++;

		}else if ($gbn == 'N' && !$cont){
			if ($IsExcelClass){
				$rowNo ++;
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>$no) );
				$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>$myF->dateStyle($row['date'],'.')."(".$weekly[$row['week']].")\n ".$myF->timeStyle($row['time'])) );
				$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>$row['name'].'('.$myF->issToGender($row['jumin']).')') );
				$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>$myF->issToBirthday($row['jumin'],'.')) );
				$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$row['suga_nm'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$row['res_nm'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>$row['mem_nm'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$cont, 'H'=>'L') );
				$sheet->SetData( Array('F'=>'I'.$rowNo, 'H'=>'L') );
				$sheet->getStyle('H'.$rowNo)->getAlignment()->setWrapText(true);
				$sheet->getRowDimension($rowNo)->setRowHeight(-1);
			}else{?>
				<tr id="ID_ROW_<?=$i;?>" onclick="lfWorkLogReg('<?=$para;?>',this);" style="cursor:default;" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';">
					<td class="center"><?=$no;?></td>
					<td class="center"><?=$myF->dateStyle($row['date'],'.');?>(<?=$weekly[$row['week']];?>) <?=$myF->timeStyle($row['time']);?></td>
					<td class="center"><?=$row['name'];?>(<?=$gender[$myF->issToGender($row['jumin'])];?>)</td>
					<td class="center"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
					<td class="center"><div class="nowrap left" style="width:100px;"><?=$row['suga_nm'];?></div></td>
					<td class="center"><div class="nowrap left" style="width:100px;"><?=$row['res_nm'];?></div></td>
					<td class="center"><?=$row['mem_nm'];?></td>
					<td class="center"><div id="ID_CONTENTS" class="nowrap left" style="width:150px;"><?=$cont;?></div></td><?
					if (!$IsExcel){?>
						<td class="center"><div id="ID_PICTURE"><?=$pic;?></div></td><?
					}?>
					<td class="center last"></td>
				</tr><?
			}

			$no ++;

		}else if ($gbn == ''){
			if ($IsExcelClass){
				$rowNo ++;
				$sheet->SetData( Array('F'=>'A'.$rowNo, 'val'=>$no) );
				$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>$myF->dateStyle($row['date'],'.')."(".$weekly[$row['week']].")\n ".$myF->timeStyle($row['time'])) );
				$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>$row['name'].'('.$myF->issToGender($row['jumin']).')') );
				$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>$myF->issToBirthday($row['jumin'],'.')) );
				$sheet->SetData( Array('F'=>'E'.$rowNo, 'val'=>$row['suga_nm'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'F'.$rowNo, 'val'=>$row['res_nm'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'G'.$rowNo, 'val'=>$row['mem_nm'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'H'.$rowNo, 'val'=>$cont, 'H'=>'L') );
				$sheet->SetData( Array('F'=>'I'.$rowNo, 'H'=>'L') );
				$sheet->getStyle('H'.$rowNo)->getAlignment()->setWrapText(true);
				$sheet->getRowDimension($rowNo)->setRowHeight(-1);
			}else{?>
				<tr id="ID_ROW_<?=$i;?>" onclick="lfWorkLogReg('<?=$para;?>',this);" style="cursor:default;" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';">
					<td class="center"><?=$no;?></td>
					<td class="center"><?=$myF->dateStyle($row['date'],'.');?>(<?=$weekly[$row['week']];?>) <?=$myF->timeStyle($row['time']);?></td>
					<td class="center"><?=$row['name'];?>(<?=$gender[$myF->issToGender($row['jumin'])];?>)</td>
					<td class="center"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
					<td class="center"><div class="nowrap left" style="width:100px;"><?=$row['suga_nm'];?></div></td>
					<td class="center"><div class="nowrap left" style="width:100px;"><?=$row['res_nm'];?></div></td>
					<td class="center"><?=$row['mem_nm'];?></td>
					<td class="center"><div id="ID_CONTENTS" class="nowrap left" style="width:150px;"><?=$cont;?></div></td><?
					if (!$IsExcel){?>
						<td class="center"><div id="ID_PICTURE"><?=$pic;?></div></td><?
					}?>
					<td class="center last"></td>
				</tr><?
			}

			$no ++;

		}
	}

	unset($rows);

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$seq	= $_POST['seq'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	$mode	= $_POST['mode'];

	if ($mode == '1'){
		//기간중복여부
		$sql = 'SELECT	from_dt
				,		to_dt
				FROM	insure_dc
				WHERE	org_no		 = \''.$orgNo.'\'
				AND		jumin		 = \''.$jumin.'\'
				AND		seq			!= \''.$seq.'\'
				AND		insure_gbn	 = \''.$_POST['gbn'].'\'
				AND		del_flag	 = \'N\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$errorCode = 0;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if (($fromDt >= $row['from_dt'] && $fromDt <= $row['to_dt']) || ($toDt >= $row['from_dt'] && $toDt <= $row['to_dt'])){
				$errorCode = 7;
				break;
			}
		}

		$conn->row_free();

		if ($errorCode > 0){
			$conn->close();
			echo $errorCode;
			exit;
		}


		//등록 및 수정
		$sql = 'SELECT	COUNT(*)
				FROM	insure_dc
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		seq		= \''.$seq.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	insure_dc
					SET		insure_gbn	= \''.$_POST['gbn'].'\'
					,		dc_val		= \''.$_POST['val'].'\'
					,		from_dt		= \''.$fromDt.'\'
					,		to_dt		= \''.$toDt.'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		seq		= \''.$seq.'\'';
		}else{
			$sql = 'SELECT	IFNULL(MAX(seq),0)+1
					FROM	insure_dc
					WHERE	org_no	= \''.$orgNo.'\'
					AND		jumin	= \''.$jumin.'\'';

			$seq = $conn->get_data($sql);

			$sql = 'INSERT INTO insure_dc (
					 org_no
					,jumin
					,seq
					,insure_gbn
					,dc_val
					,from_dt
					,to_dt
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$jumin.'\'
					,\''.$seq.'\'
					,\''.$_POST['gbn'].'\'
					,\''.$_POST['val'].'\'
					,\''.$fromDt.'\'
					,\''.$toDt.'\'
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
		echo 1;


	}else if ($mode == '2' || $mode == '4'){
		//조회
		$sql = 'SELECT	seq
				,		insure_gbn AS gbn
				,		dc_val
				,		from_dt
				,		to_dt
				FROM	insure_dc
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'';

		if ($mode == '4'){
			$sql .= '
				AND		from_dt <= DATE_FORMAT(NOW(),\'%Y%m\')';
		}

		$sql .= '
				AND		del_flag= \'N\'';

		if ($mode == '4'){
			$sql .= '
				ORDER	BY from_dt, to_dt';
		}else{
			$sql .= '
				ORDER	BY from_dt DESC, to_dt DESC';
		}

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		if ($rowCnt > 0){
			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($row['gbn'] == '01'){
					$gbn = '국민연금';
				}else if ($row['gbn'] == '02'){
					$gbn = '건강보험';
				}else if ($row['gbn'] == '03'){
					$gbn = '장기요양보험';
				}else if ($row['gbn'] == '04'){
					$gbn = '고용보험';
				}else{
					$gbn = $row['gbn'];
				}

				if ($mode == '2'){?>
					<tr id="rowId_<?=$i;?>" seq="<?=$row['seq'];?>" gbn="<?=$row['gbn'];?>" val="<?=IntVal($row['dc_val']);?>" from="<?=$myF->_styleYYMM($row['from_dt']);?>" to="<?=$myF->_styleYYMM($row['to_dt']);?>">
					<td class="center"><?=$no;?></td><?
				}else{?>
					<tr><?
				}?>
				<td class="center"><div class="left"><?=$gbn;?></div></td>
				<td class="center"><div class="right"><?=$row['dc_val'];?></div></td>
				<td class="center"><?=$myF->_styleYYMM($row['from_dt'],'.');?> ~ <?=$myF->_styleYYMM($row['to_dt'],'.');?></td>
				<td class="center"><?
					if ($mode == '2'){?>
						<div class="left"><span class="btn_pack small"><button onclick="lfDelete('<?=$row['seq'];?>');">삭제</button></span></div><?
					}?>
				</td>
				</tr><?
			}
		}else{
			if ($mode == '4'){?>
				<tr>
					<td class="center" colspan="4">::검색된 데이타가 없습니다.::</td>
				</tr><?
			}
		}

		$conn->row_free();


	}else if ($mode == '3'){
		//삭제
		$sql = 'UPDATE	insure_dc
				SET		del_flag	= \'Y\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		seq		= \''.$seq.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		echo 1;


	}else{
	}

	include_once('../inc/_db_close.php');
?>
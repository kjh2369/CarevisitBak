<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$gwan_cd = $_POST['gwan_cd'];
	$hang_cd = $_POST['hang_cd'];
	$mog_cd = $_POST['mog_cd'];
	$tgt_dt = $_POST['tgt_dt'];
	$rowno = $_POST['rowno'] - 1;

	if ($rowno < 0) $rowno = 0;

	$sql = 'SELECT	a.ent_dt, a.ent_seq, a.wrt_dt, b.gbn_name AS ar_type, a.ar_amt, a.exp_name, a.cause, a.sign_cd
			FROM	fa_apprq AS a
			INNER	JOIN	ltcf_gbn AS b
					ON		b.gbn_type	= \'T2\'
					AND		b.gbn_cd	= a.ar_type
					AND		b.del_flag	= \'N\'
			WHERE	a.org_no	 = \''.$orgNo.'\'
			AND		a.wrt_dt	<= \''.$tgt_dt.'\'
			AND		a.gwan_cd	 = \''.$gwan_cd.'\'
			AND		a.hang_cd	 = \''.$hang_cd.'\'
			AND		a.mog_cd	 = \''.$mog_cd.'\'
			AND		a.del_flag	 = \'N\'
			ORDER	BY wrt_dt DESC, a.ent_dt DESC, a.ent_seq DESC
			LIMIT	'.$rowno.', 10
			';
	$rows = $conn->fetch_array($sql);

	for($i=0; $i<count($rows); $i++){
		$row = $rows[$i];

		$sql = 'SELECT	a.title, a.ipin, b.name, a.sign_flag
				FROM	sign_log AS a
				INNER	JOIN ltcf_mem AS b
						ON		b.ipin		= a.ipin
						AND		b.del_flag	= \'N\'
				WHERE	a.org_no	= \''.$orgNo.'\'
				AND		a.link_cd	= \''.$row['sign_cd'].'\'
				AND		a.del_flag	= \'N\'
				ORDER	BY a.seq
				';
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$stat = $myF->Button(Array('id'=>'BTN_SEL', 'name'=>'선택', 'gab'=>''));

		for($j=0; $j<$rowCnt; $j++){
			$r = $conn->select_row($j);

			if ($j == 0) $per_name = $r['name'];

			if ($r['sign_flag'] != 'Y'){
				$stat = $r['title'].'('.$r['name'].') 대기중';
				break;
			}
		}

		$conn->row_free();?>
		<tr ent_dt="<?=$row['ent_dt'];?>" ent_seq="<?=$row['ent_seq'];?>" sign_cd="<?=$row['sign_cd'];?>" wrt_dt="<?=$row['wrt_dt'];?>">
		<td class="txt_center"><?=$i+1;?></td>
		<td class="txt_center"><?=$myF->dateStyle($row['wrt_dt'],'/');?></td>
		<td class="txt_center"><?=$row['ar_type'];?></td>
		<td class="txt_right5"><?=number_format($row['ar_amt']);?></td>
		<td><?=$row['exp_name'];?></td>
		<td class="nowrap"><?=stripslashes($row['cause']);?></td>
		<td><?=$per_name;?></td>
		<td><?=$stat;?></td>
		<td>&nbsp;</td>
		</tr><?
	}

	unset($rows);
?>
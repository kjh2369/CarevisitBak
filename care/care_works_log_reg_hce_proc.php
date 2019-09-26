<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$IPIN	= $_POST['IPIN'];
	$seq	= $_POST['seq'];
	$date	= $_POST['date'];

	$sql = 'SELECT	a.proc_seq AS seq
			,		a.counsel_gbn
			,		b.name AS gbn
			,		a.counsel_nm AS mem_nm
			FROM	hce_proc_counsel AS a
			LEFT	JOIN	hce_gbn AS b
					ON		b.type	= \'CT\'
					AND		b.use_yn= \'Y\'
					AND		b.code = a.counsel_gbn
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.org_type	= \''.$SR.'\'
			AND		a.IPIN		= \''.$IPIN.'\'
			AND		a.rcpt_seq	= \''.$seq.'\'
			AND		a.counsel_dt= \''.$date.'\'
			AND		a.del_flag	= \'N\'
			ORDER	BY a.proc_seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	if ($rowCnt > 0){
		$no = 1;
		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<div style="border-top:<?=$i > 0 ? '1px solid #CCCCCC;' : '';?>" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';">
				<span><?=$no;?>회차</span> /
				<span><?=$row['gbn'];?></span> /
				<span><?=$row['mem_nm'];?></span>
				<span>
					[<a href="#" onclick="lfHCEProcReg('<?=$IPIN;?>','<?=$seq;?>','<?=$row['seq'];?>',$('#cboGbn').val()); return false;">변경</a> |
					<a href="#" onclick="lfProcRemove('<?=$IPIN;?>','<?=$seq;?>','<?=$row['seq'];?>'); return false;">삭제</a>]
				</span>
			</div><?

			$no ++;
		}
	}else{?>
		<div>::적성이력이 없습니다.::</div><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
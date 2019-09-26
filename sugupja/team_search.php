<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$jumin	= $ed->de($_POST['jumin']);

	$sql = 'SELECT	a.seq
			,		a.team_cd
			,		m02_yname AS team_nm
			,		a.from_ym
			,		a.to_ym
			,		a.deduct_amt
			,		a.deduct_rate
			FROM	client_his_team AS a
			INNER	JOIN	m02yoyangsa
					ON		m02_ccode	= a.org_no
					/*AND		m02_mkind	= a.svc_cd*/
					AND		m02_mkind	= \'0\'
					AND		m02_yjumin	= a.team_cd
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.jumin		= \''.$jumin.'\'
			AND		a.svc_cd	= \''.$svcCd.'\'
			AND		a.del_flag	= \'N\'
			ORDER	BY from_ym DESC, to_ym DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr seq="<?=$row['seq'];?>" teamCd="<?=$ed->en($row['team_cd']);?>">
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$row['team_nm'];?></td>
			<td class="center"><?=$myF->_styleYYMM($row['from_ym'],'.');?></td>
			<td class="center"><?=$myF->_styleYYMM($row['to_ym'],'.');?></td>
			<td class="center"><div class="right"><?=number_format($row['deduct_amt']);?></div></td>
			<td class="center"><div class="right"><?=$row['deduct_rate'];?>%</div></td>
			<td class="center">
				<div class="left"><span class="btn_pack small"><button onclick="lfTeamDelete('<?=$row['seq'];?>');">삭제</button></span></div>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once("../inc/_db_close.php");
?>
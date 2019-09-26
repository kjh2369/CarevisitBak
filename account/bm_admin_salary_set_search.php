<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$close	= $_POST['close'];

	$sql = 'SELECT	a.jumin
			,		b.name
			,		a.job
			,		a.salary
			,		a.nps_amt, a.nhic_amt, a.ei_amt, a.insu_amt
			,		a.retire_amt
			FROM	ie_bm_salary AS a
			INNER	JOIN (
					SELECT	DISTINCT
							m02_yjumin AS jumin
					,		m02_yname AS name
					FROM	m02yoyangsa
					WHERE	m02_ccode = \''.$orgNo.'\'
					) AS b
					ON	b.jumin = a.jumin
			WHERE	a.org_no= \''.$orgNo.'\'
			AND		a.yymm	= \''.$year.$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		switch($row['job']){
			case '01':
				$row['job'] = '센터장';
				break;

			case '02':
				$row['job'] = '정직원';
				break;
		}?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['job'];?></div></td>
			<td class="center"><div class="left"><?=$row['name'];?></div></td>
			<td class="center"><div class="right"><?=number_format($row['salary']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['nps_amt']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['nhic_amt']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['ei_amt']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['insu_amt']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['retire_amt']);?></div></td>
			<td class="center">
				<div class="left"><?
					if ($close != 'Y'){?>
						<span class="btn_pack small"><button onclick="lfRemove('<?=$ed->en($row['jumin']);?>');" style="color:RED;">삭제</button></span><?
					}?>
				</div>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
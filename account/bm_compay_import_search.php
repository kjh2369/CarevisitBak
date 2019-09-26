<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$sql = 'SELECT	seq
			,		subject
			,		amt
			FROM	ie_bm_compay_item
			WHERE	domain_id	= \''.$gDomainID.'\'
			AND		ie_gbn		= \'I\'
			AND		yymm		= \''.$year.($month < 10 ? '0' : '').$month.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['subject'];?></div></td>
			<td class="center"><div class="right"><?=number_format($row['amt']);?></div></td>
			<td class="center"><div class="left"><span class="btn_pack small"><button onclick="lfRemove('<?=$row['seq'];?>');" style="color:RED;">삭제</button></span></div></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
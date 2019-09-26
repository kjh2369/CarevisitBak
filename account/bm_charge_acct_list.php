<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	cd,name
			FROM	ie_bm_acct_cd
			ORDER	BY seq,cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';">
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$row['cd'];?></td>
			<td class="center"><div class="left"><a href="#" onclick="lfSel('<?=$row['cd'];?>','<?=$row['name'];?>');"><?=$row['name'];?></a></div></td>
			<td class="center"></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
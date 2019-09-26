<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	DISTINCT
					a.org_no AS old_cd
			,		b.m00_store_nm AS old_nm
			,		b.m00_mname AS old_mg
			,		a.val AS new_cd
			,		c.m00_store_nm AS new_nm
			,		c.m00_mname AS new_mg
			,		a.dt AS chng_dt
			FROM	center_his AS a
			INNER	JOIN	m00center AS b
					ON		b.m00_mcode = a.org_no
			INNER	JOIN	m00center AS c
					ON		c.m00_mcode = a.val
			WHERE	a.gbn	= \'01\'
			ORDER	BY old_nm, new_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td><div class="left"><?=$row['old_cd'];?></div></td>
			<td><div class="left"><?=$row['old_nm'];?></div></td>
			<td><div class="left"><?=$row['old_mg'];?></div></td>
			<td><div class="left"><?=$row['new_cd'];?></div></td>
			<td><div class="left"><?=$row['new_nm'];?></div></td>
			<td><div class="left"><?=$row['new_mg'];?></div></td>
			<td class="last"><div class="left"><?=$myF->dateStyle($row['chng_dt'],'.');?></div></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
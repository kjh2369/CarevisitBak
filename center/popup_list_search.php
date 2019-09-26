<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	pop_id
			,		COUNT(org_no) AS cnt
			,		insert_dt
			,		from_dt
			,		to_dt
			,		contents
			FROM	center_popup
			WHERE	from_dt <= NOW()
			AND		to_dt >= NOW()
			GROUP	BY pop_id
			ORDER	BY insert_dt, from_dt, to_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	if ($rowCnt > 0){
		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td class="left"><?=$myF->dateStyle($row['insert_dt'],'.');?></td>
				<td class="center"><?=$row['cnt'];?></td>
				<td class="left"><?=$myF->dateStyle($row['from_dt'],'.');?><br>~ <?=$myF->dateStyle($row['to_dt'],'.');?></td>
				<td class="left"><?=$row['contents'];?></td>
				<td class="left last">
					<span class="btn_pack m"><button onclick="lfModify('<?=$row['pop_id'];?>');">수정</button></span>
					<span class="btn_pack m"><button onclick="lfDelete('<?=$row['pop_id'];?>');">삭제</button></span>
				</td>
			</tr><?

			$no ++;
		}
	}else{?>
		<tr>
			<td class="center last" colspan="6">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');

	$orgNo = $_SESSION['userCenterCode'];
?>
<div class="title title_border">세미나 신청내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="200px">
		<col width="70px">
		<col width="100px">
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">신청일시</th>
			<th class="head">기관명</th>
			<th class="head">직위</th>
			<th class="head">이름</th>
			<th class="head">참가지역</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		$sql = 'SELECT	org_nm
				,		position
				,		name
				,		take_part_area
				,		deposit_yn
				,		insert_dt
				FROM	seminar_request
				WHERE	org_no = \''.$orgNo.'\'
				ORDER	BY insert_dt DESC';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$i+1;?></td>
				<td class="left"><?=Date('Y.m.d H:i',StrToTime($row['insert_dt']));?></td>
				<td class="left"><?=$row['org_nm'];?></td>
				<td class="left"><?=$row['position'];?></td>
				<td class="left"><?=$row['name'];?></td>
				<td class="left"><?=$row['take_part_area'];?></td>
				<td class="center last"></td>
			</tr><?
		}

		$conn->row_free();?>
	</tbody>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
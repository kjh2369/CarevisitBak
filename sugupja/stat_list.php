<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];

	if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="90px">
		<col width="90px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">일자</th>
			<th class="head">기록자</th>
			<th class="head">담당요양보호사</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
	$sql = 'SELECT reg_dt
			,      reg_nm
			,      yoy_nm
			  FROM counsel_client_state
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			 ORDER BY reg_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$i+1;?></td>
			<td class="center"><?=$myF->dateStyle($row['reg_dt'],'.');?></td>
			<td class="left"><?=$row['reg_nm'];?></td>
			<td class="left"><?=$row['yoy_nm'];?></td>
			<td class="left last">
				<span class="btn_pack m"><button type="button" onclick="go_stat_reg('<?=$row['reg_dt'];?>');">수정</button></span>
				<span class="btn_pack m"><button type="button" onclick="go_stat_show('<?=$row['reg_dt'];?>');">출력</button></span>
				<span class="btn_pack m"><button type="button" onclick="go_stat_del('<?=$row['reg_dt'];?>');">삭제</button></span>
			</td>
		</tr><?
	}

	$conn->row_free();

	if ($rowCount == 0){?>
		<tr>
			<td class="center last" colspan="5">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}?>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
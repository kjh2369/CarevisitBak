<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$mode  = $_POST['mode'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
?>
<table class="my_table my_border_blue" style="width:auto;">
	<colgroup>
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="100px">
		<col width="160px">
	</colgroup>
	<tbody>
		<tr>
			<th class="center">입사일자</th>
			<th class="center">퇴사일자</th>
			<th class="center">고용상태</th>
			<th class="center">휴직기간</th>
			<th class="center">비고</th>
		</tr>
		<tr>
			<td class="center top" colspan="5">
				<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:150px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="70px">
							<col width="70px">
							<col width="60px">
							<col width="100px">
							<col>
						</colgroup>
						<tbody>
						<?
							$sql = 'select seq
									,      join_dt
									,      quit_dt
									,      employ_stat
									,      case employ_stat when \'1\' then \'재직\' when \'2\' then \'휴직\' else \'퇴직\' end as stat
									,      case employ_stat when \'2\' then concat(replace(leave_from, \'-\', \'.\'), \'~\', replace(leave_to, \'-\', \'.\')) else \'\' end leave_dt
									  from mem_his
									 where org_no = \''.$code.'\'
									   and jumin  = \''.$jumin.'\'
									 order by seq desc';

							$conn->query($sql);
							$conn->fetch();

							$rowCount = $conn->row_count();

							for($i=0; $i<$rowCount; $i++){
								$row = $conn->select_row($i);?>
								<tr>
									<td class="center"><?=$myF->dateStyle($row['join_dt'],'.');?></td>
									<td class="center"><?=$myF->dateStyle($row['quit_dt'],'.');?></td>
									<td class="center"><?=$row['stat'];?></td>
									<td class="center"><?=$myF->dateStyle($row['leave_dt']);?></td>
									<td class="center"><?
									if ($i == 0 && $mode == 0){?>
										<div class="left">
											<span class="btn_pack small"><button type="button" onclick="_memFind('<?=$ed->en($jumin);?>','<?=$row['seq'];?>','N');">변경</button></span><?
											if ($row['employ_stat'] != '9'){
											}else{?>
												<span class="btn_pack small"><button type="button" onclick="_memFind('<?=$ed->en($jumin);?>','<?=$row['seq']+1;?>','Y');">채용</button></span><?
											}?>
										</div><?
									}?>
									</td>
								</tr><?
							}

							$conn->row_free();
						?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<?
	include_once('../inc/_db_close.php');
?>
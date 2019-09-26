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
		<col width="130px">
		<col width="100px">
		<col width="130px">
	</colgroup>
	<tbody>
		<tr>
			<th class="center">서비스</th>
			<th class="center">계약일자</th>
			<th class="center">상태</th>
			<th class="center">비고</th>
		</tr>
		<tr>
			<td class="center top" colspan="5">
				<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:150px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="70px">
							<col width="130px">
							<col width="100px">
							<col>
						</colgroup>
						<tbody>
						<?
							$sql = 'select seq
									,      svc_cd
									,      from_dt
									,      to_dt
									,      svc_stat
									,      svc_reason
									  from client_his_svc
									 where org_no = \''.$code.'\'
									   and jumin  = \''.$jumin.'\'
									 order by from_dt desc, to_dt desc';

							$conn->query($sql);
							$conn->fetch();

							$rowCount = $conn->row_count();

							if ($rowCount > 0){
								for($i=0; $i<$rowCount; $i++){
									$row = $conn->select_row($i);?>
									<tr>
										<td class="center"><?=$conn->kind_name_svc($row['svc_cd']);?></td>
										<td class="center"><?=$myF->dateStyle($row['from_dt'],'.').'~'.$myF->dateStyle($row['to_dt'],'.');?></td>
										<td class="center"><?=$definition->SugupjaStatusGbn($row['svc_stat']);?></td>
										<td class="center">
										<?
											if ($i == 0 && $mode == 0){?>
												<div class="left">
													<span class="btn_pack small"><button type="button" onclick="_clientFind('<?=$ed->en($jumin);?>','<?=$row['seq'];?>','N');">변경</button></span>
													<!--span class="btn_pack small"><button type="button" onclick="_clientFind('<?=$ed->en($jumin);?>','<?=$row['seq']+1;?>','Y');">계약</button></span-->
												</div><?
											}
										?>
										</td>
									</tr><?
								}
							}else{?>
								<tr>
									<td class="center">미등록</td>
									<td class="center">&nbsp;</td>
									<td class="center">미등록</td>
									<td class="center">
										<div class="left">
											<span class="btn_pack small"><button type="button" onclick="_clientFind('<?=$ed->en($jumin);?>','<?=$row['seq'];?>','N');">변경</button></span>
										</div>
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
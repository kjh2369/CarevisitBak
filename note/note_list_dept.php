<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");

	$code      = $_POST[code];
	$send_type = $_POST[send_type];
	$val       = explode('//',$_POST[val]);
	$wsl       = '';

	for($i=0; $i<sizeof($val); $i++){
		if (!empty($val[$i])){
			$wsl .= (!empty($wsl) ? ',' : '').'\''.$val[$i].'\'';
		}
	}
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="130px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold">부서코드</th>
			<th class="head bold">부서명</th>
			<th class="head bold">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="6" style="height:100px;">
				<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;">
					<table id="body_list" class="my_table" style="width:100%;">
						<colgroup>
							<col width="80px">
							<col width="130px">
							<col>
						</colgroup>
						<tbody>
						<?
							if ($wsl != ''){
								$sql = "select dept_cd, dept_nm
										  from dept
										 where org_no   = '$code'
										   and dept_cd in ($wsl)
										   and del_flag = 'N'";

								$conn->fetch_type = 'assoc';
								$conn->query($sql);
								$conn->fetch();

								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row = $conn->select_row($i);

									echo '<tr id=\'body_list_tr_'.$row['dept_cd'].'\'>
											<td class=\'center\'><div class=\'center\'>'.$row['dept_cd'].'</div></td>
											<td class=\'center\'><div class=\'left\'>'.$row['dept_nm'].'</div></td>
											<td class=\'center\'><div class=\'left\'>
												<a href=\'#\' onclick=\'delete_list("body_list","body_list_tr_'.$row['dept_cd'].'");\'>삭제</a>
											</div></td>
										  </tr>';

									echo '<input name=\''.$send_type.'_cd[]\' type=\'hidden\' value=\''.$row['dept_cd'].'\'>';
								}

								$conn->row_free();
							}
						?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once("../inc/_footer.php");
?>
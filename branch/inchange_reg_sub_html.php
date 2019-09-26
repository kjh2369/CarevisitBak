<?
	$branch = $_POST['branch']; //지사코드
	$person = $_POST['person']; //지사코드
	$type   = $_POST['type']; //등록, 리스트
	$mode   = $_POST['mode'];
	$domain = $myF->_get_domain();

	switch($mode){
		case _COM_:
			if ($domain == _DWCARE_)
				$mark_val = 'ON';
			else if ($domain == _KLCF_)
				$mark_val = 'KL';
			else if ($domain == _KDOLBOM_)
				$mark_val = 'KD';
			else
				$mark_val = 'GE';
			break;
		case _BRAN_:
			if ($_SESSION['userLevel'] == 'A'){
				$mark_val = 'G';
			}else{
				$mark_val = $_SESSION['userBranchCode']; //지사코드
			}
			break;
		case _STORE_:
			$mark_val = 'S';
			break;
	}

	if ($branch != '' && $person != ''){
		$wrt_mode = 2;

		// 지점 정보
		$sql = "select b00_code, b00_name
				  from b00branch
				 where b00_code = '$branch'";
		$row = $conn->get_array($sql);
		$branch_code = $row[0];
		$branch_name = $row[1];

		if ($person != 'new'){
			// 담당자 정보
			$sql = "select *
					  from b01person
					 where b01_branch = '$branch'
					   and b01_code   = '$person'";
			$mst = $conn->get_array($sql);
		}else{
			$sql = "select ifnull(right(concat('00', cast(cast(max(b01_code) as unsigned) + 1 as char)), 3), '001')
				  from b01person
				 where b01_branch = '$branch'";
			$newPerson = $conn->get_data($sql);
			$mst['b01_branch'] = $branch;
			$mst['b01_code'] = $newPerson;
		}
	}else{
		$wrt_mode = 1;

		$mst['b01_branch'] = '';
		$mst['b01_code']   = '';
	}
?>

<div style="width:48%; position:relative; margin-left:10px; margin-top:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="20%">
			<col>
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">지사</th>
		</thead>
		<tbody>
			<tr>
				<th>지사선택</th>
				<td>
				<?
					if ($wrt_mode == 1){
						echo '<select name=\'branch\' style=\'width:auto;\' onChange=\'_getPersonCode(this.value);\'>';

						if ($mode == _COM_){
						}else if ($_SESSION['userLevel'] == 'B'){
						}else{
							echo '<option value=\'\'>-선택하여 주십시오.-</option>';
						}

						$sql = "select b00_code, b00_name
								  from b00branch
								  where b00_domain = '$domain'
								    and b00_code like '$mark_val%'
								 order by b00_code";
						$conn->query($sql);
						$conn->fetch();
						$rowCount = $conn->row_count();

						for($i=0; $i<$rowCount; $i++){
							$row = $conn->select_row($i);
							echo '<option value=\''.$row[0].'\' '.($mst['b01_branch'] == $row[0] ? 'selected' : '').'>'.$row[1].'</option>';
						}
						$conn->row_free();

						echo '</select>';
					}else{
						echo '<span class=\'left bold\'>'.$branch_name.'</span>';
						echo '<input name=\'branch\' type=\'hidden\' value=\''.$mst['b01_branch'].'\'>';
					}
				?>

				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:48%; position:relative; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="20%">
			<col width="30%">
			<col width="20%">
			<col width="30%">
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">담당자</th>
		</thead>
		<tbody>
			<tr>
				<th>아이디</th>
				<td>
				<?
					if ($wrt_mode == 1){
						echo '<input name=\'id\' type=\'text\' value=\''.$mst['b01_id'].'\' style=\'width:100%;\' onfocus=\'_chk_branch("'.$mode.'");\' onchange=\'return _chk_id("'.$mode.'");\'>';
					}else{
						echo '<span class=\'left bold\'>'.$mst['b01_id'].'</span>';
						echo '<input name=\'id\' type=\'hidden\' value=\''.$ed->en($mst['b01_id']).'\'>';
					}

					echo '<input name=\'personCode\' type=\'hidden\' value=\''.$mst['b01_code'].'\'>';
				?>
				</td>
				<th>비밀번호</th>
				<td><input name="pwd" type="text" value="<?=$mst['b01_pass'];?>" style="width:100%;">
				</td>
			</tr>
			<tr>
				<th>담당자명</th>
				<td><input name="personName" type="text" value="<?=$mst['b01_name'];?>" style="width:100%;"></td>
				<th>연락처</th>
				<td><input name="phone" type="text" value="<?=$myF->phoneStyle($mst['b01_phone']);?>" class="phone"></td>
			</tr>
			<tr>
				<th>직위</th>
				<td>
					<select name="position" style="width:auto;">
					<?
						$sql = "select m81_code, m81_name
								  from m81gubun
								 where m81_gbn = 'POS'
								 order by m81_code";
						$conn->query($sql);
						$conn->fetch();
						$rowCount = $conn->row_count();

						for($i=0; $i<$rowCount; $i++){
							$row = $conn->select_row($i);
							?>
								<option value="<?=$row[0];?>" <? if ($mst['b01_position'] == $row[0]){echo('selected');} ?>><?=$row[1];?></option>
							<?
						}
						$conn->row_free();
					?>
					</select>
				</td>
				<th>상태</th>
				<td>
					<select name="stat" style="width:auto;">
					<option value="1" <? if ($mst['b01_stat'] == '1'){echo('selected');} ?>>활동</option>
					<?
						if ($person != ''){
						?>
							<option value="9" <? if ($mst['b01_stat'] == '9'){echo('selected');} ?>>퇴사</option>
						<?
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<th>입사일자</th>
				<td>
					<input name="joinDate" type="text" value="<?=$myF->dateStyle($mst['b01_join_date']);?>" class="date">
				</td>
				<th>퇴사일자</th>
				<td>
					<input name="quitDate" type="text" value="<?=$myF->dateStyle($mst['b01_quit_date']);?>" class="date" readOnly>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:48%; position:relative; top:-66; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="20%">
			<col>
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">소재</th>
		</thead>
		<tbody>
			<tr>
				<th>우편번호</th>
				<td>
					<input name="postno1" type="text" value="<?=subStr($mst['b01_postno'],0,3);?>" class="date" maxlength="3" style="width:30px;" onKeyDown="__onlyNumber(this);"> -
					<input name="postno2" type="text" value="<?=subStr($mst['b01_postno'],3,3);?>" class="date" maxlength="3" style="width:30px;" onKeyDown="__onlyNumber(this);">
					<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="__helpAddress('postno1', 'postno2', 'addr1', 'addr2');">찾기</button></span>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input name="addr1" type="text" value="<?=$mst['b01_addr1'];?>" style="width:100%;">
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input name="addr2" type="text" value="<?=$mst['b01_addr2'];?>" style="width:100%;">
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:48%; position:relative; top:-66; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col>
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">비고</th>
		</thead>
		<tbody>
			<tr>
				<td><textarea name="other" style="width:99%; height:40px;"><?=stripSlashes($mst['b01_other']);?></textarea></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="clear:both; position:relative; top:-66; margin:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col>
		</colGroup>
		<tbody>
			<tr>
				<th class="right">
				<?
					if ($type == 'reg'){
						echo '<a href=\'#\' onclick=\'_inchangeRegOk();\'>등록</a>';
					}else{
						echo '<a href=\'#\' onclick=\'_inchangeRegOk();\'>수정</a> | ';
						echo '<a href=\'#\' onclick=\'_inchangeList("'.$mode.'");\'>리스트</a>';
					}
				?>
				</th>
			</tr>
		</tbody>
	</table>
</div>

<input name="type" type="hidden" value="<?=$type;?>">
<input name="wrt_mode" type="hidden" value="<?=$wrt_mode;?>">
<input name="mode" type="hidden" value="<?=$mode;?>">
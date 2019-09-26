<?
	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	$current_svc_nm = $conn->kind_name($k_list, $__CURRENT_SVC_ID__, 'id');
?>
<table class="my_table my_border_blue" style="width:100%; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>이용서비스</th>
			<td>
				<input name="" type="checkbox" class="checkbox" value="Y">가사간병
				<input name="" type="checkbox" class="checkbox" value="Y">노인돌봄
				<input name="" type="checkbox" class="checkbox" value="Y">산모신생아
				<input name="" type="checkbox" class="checkbox" value="Y">장애활동보조
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%; border-top:none; border-bottom:none;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>수급상태</th>
			<td>
			<?
				$statusList = $definition->SugupjaStatusList();

				for($ii=0; $ii<sizeOf($statusList); $ii++){
					echo '<input name=\'sugupStatus\' type=\'radio\' class=\'radio\' value=\''.$statusList[$ii]['code'].'\' tag=\'\' onclick=\'\'>'.$statusList[$ii]['name'];
				}
			?>
			</td>
		</tr>
		<tr>
			<th>계약기간</th>
			<td>
				<input name="gaeYakFm" type="text" value="" tag="" class="date"> ~
				<input name="gaeYakTo" type="text" value="" tag="" class="date" alt="not">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="width:100%; border-top:none;">
	<colgroup>
		<col width="80px">
		<col width="80px">
		<col width="240px">
		<col width="70px">
		<col>
		<col width="40px">
		<col width="110px">
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="2">질병</th>
			<th>병명</th>
			<td>
			<?
				$sql = "select m81_code as cd
						,      m81_name as nm
						  from m81gubun
						 where m81_gbn = 'DAS'";

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				for($ii=0; $ii<$row_count; $ii++){
					$row =$conn->select_row($ii);

					echo '<input name=\'byungMung\' type=\'radio\' class=\'radio\'>'.$row['nm'];
				}

				$conn->row_free();
			?>
			</td>
			<th rowspan="2">요양보호사</th>
			<td>
				<input name="yoyangsa1" type="hidden" value="<?=$ed->en($mst[0]["m03_yoyangsa1"]);?>" tag="<?=$ed->en($mst[0]["m03_yoyangsa1"]);?>">
				<input name="yoyangsa1Nm" type="text" value="<?=$mst[0]["m03_yoyangsa1_nm"];?>" style="background-color:#eeeeee; margin-top:3px;" readOnly>
				<span class="btn_pack find" style="margin-top:1px; margin-left:-5px;" onclick="__helpYoy('<?=$code;?>','<?=$kind;?>',document.getElementById('yoyangsa1'),document.getElementById('yoyangsa1Nm'));"></span>
				<span class="btn_pack m" style="margin-top:2px;"><button type="button" onclick="__notYoy(1);">삭제</button></span>
			</td>
			<th>배우자</th>
			<td>
				<input name="" type="radio" class="radio" value="Y">예
				<input name="" type="radio" class="radio" value="N">아니오
			</td>
		</tr>
		<tr>
			<th>기타병명</th>
			<td></td>
			<td>
				<input name="yoyangsa1" type="hidden" value="<?=$ed->en($mst[0]["m03_yoyangsa1"]);?>" tag="<?=$ed->en($mst[0]["m03_yoyangsa1"]);?>">
				<input name="yoyangsa1Nm" type="text" value="<?=$mst[0]["m03_yoyangsa1_nm"];?>" style="background-color:#eeeeee; margin-top:3px;" readOnly>
				<span class="btn_pack find" style="margin-top:1px; margin-left:-5px;" onclick="__helpYoy('<?=$code;?>','<?=$kind;?>',document.getElementById('yoyangsa1'),document.getElementById('yoyangsa1Nm'));"></span>
				<span class="btn_pack m" style="margin-top:2px;"><button type="button" onclick="__notYoy(1);">삭제</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border_blue" style="margin-top:10px;">
	<colgroup>
		<col width="80px">
		<col width="350px">
	</colgroup>
	<tbody>
		<tr>
			<th>인증번호</th>
			<td></td>
		</tr>
		<tr>
			<th>유효기간</th>
			<td>
				<input name="injungFrom" type="text" value="" tag="" class="date"> ~
				<input name="injungTo" type="text" value="" tag="" class="date">
			</td>
		</tr>
		<tr>
			<th>장기요양등급</th>
			<td>
			<?
				$sql = "select m81_code as cd
						,      m81_name as nm
						  from m81gubun
						 where m81_gbn = 'LVL'";

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				for($ii=0; $ii<$row_count; $ii++){
					$row =$conn->select_row($ii);

					echo '<input name=\'yLvl\' type=\'radio\' class=\'radio\'>'.$row['nm'];
				}

				$conn->row_free();
			?>
			</td>
		</tr>
		<tr>
			<th>급여한도액</th>
			<td></td>
		</tr>
		<tr>
			<th>수급자구분</th>
			<td>
			<?
				$sql = "select m81_code as cd
						,      m81_name as nm
						  from m81gubun
						 where m81_gbn = 'STP'";

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				for($ii=0; $ii<$row_count; $ii++){
					$row =$conn->select_row($ii);

					echo '<input name=\'sKind\' type=\'radio\' class=\'radio\'>'.$row['nm'];
				}

				$conn->row_free();
			?>
			</td>
		</tr>
		<tr>
			<th>청구한도금액</th>
			<td></td>
		</tr>
		<tr>
			<th>본인부담금</th>
			<td></td>
		</tr>
	</tbody>
</table>
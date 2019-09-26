<?
	include_once('../inc/_header.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');


	$sql = "select m00_mcode as code
			,      m00_mkind as kind
			,      m00_store_nm as name
			,      m00_mname as manager
			,      m00_cont_date as workDate
			,      m00_start_date as startDate
			,      m00_cont_date as contDate
			,      case m00_close_cond when '0' then '운영중'
									   when '1' then '휴업중'
									   when '8' then '파업중'
									   when '9' then '폐업' else '-' end as stat
			,      concat('[', substring(m00_cpostno, 1, 3), '-', substring(m00_cpostno, 4, 3), ']', m00_caddr1, ' ', m00_caddr2) as addr
			  from m00center
			 where m00_mcode = '".$_GET['center']."'
			   and m00_mkind = '".$_GET['kind']."'";
	$center = $conn->get_array($sql);

	if (!is_array($center)){
		$center['code'] = '-';
		$center['kind'] = '-';
		$center['name'] = '-';
		$center['manager'] = '-';
		$center['workDate']  = '-';
		$center['startDate'] = date('Ymd', mktime());
		$center['contDate']  = ''; //date('Ymd', mktime());
		$center['stat'] = '-';
		$center['addr'] = '-';
	}
	
	$sql = "select b02_branch as branch
			,      b02_person as person
			,      b02_date as date
			,      b02_other as other
			,      b02_homecare as homecare
			,      b02_voucher as voucher
			  from b02center
			 where b02_center = '".$_GET['center']."'
			   and b02_kind   = '".$_GET['kind']."'";
	$branch = $conn->get_array($sql);

	$homecare = $branch['homecare'] != '' ? $branch['homecare'] : 'Y';
	

	//시작일자
	$startDate = $center['startDate'] != '' ? $center['startDate'] : date('Ymd', mktime());

?>
<style>
body{
	margin:0;
	padding:0;
}
</style>
<script type="text/javascript" src="../js/branch.js"></script>
<form name="f" method="post">
<div class="title title_border">기관연결</div>
<table class="my_table" style="width:100%;">
<colgroup>
	<col width="13%">
	<col width="21%">
	<col width="13%">
	<col width="20%">
	<col width="13%">
	<col width="20%">
</colgroup>
<thead>
	<tr>
		<th class="head" colspan="6">기관정보</th>
	</tr>
</thead>
<tbody>
	<tr>
		<th class="head left">기관코드</th>
		<td class="left" >
			<span id="idCenterCode" style="width:80%; text-align:left;"><?=$center['code'];?></span>
			<?
				if ($center['code'] == '-'){
				?>
					<span class="btn_pack find" onClick="__showB2CenterList();"></span>
				<?
				}
			?>
		</td>
		<th class="head left" >기관명</th>
		<td class="left" colspan="3"><span id="idCenterName" style="text-align:left;"><?=$center['name'];?></span></td>
	</tr>
	<tr>
		<th class="head left" >대표자명</th>
		<td class="left" ><span id="idCenterManager" style="text-align:left;"><?=$center['manager'];?></span></td>
		<th class="head left" >업무시작일자</th>
		<td class="left" ><span id="idCenterWorkDate" style="text-align:left;"><?=$myF->dateStyle($center['workDate']);?></span></td>
		<th class="head left" >상태</th>
		<td class="left" ><span id="idCenterStat" style="text-align:left;"><?=$center['stat'];?></span></td>
	</tr>
	<tr>
		<th class="head left" >주소</th>
		<td class="left" colspan="5"><span id="idCenterAddr" style="text-align:left;"><?=$center['addr'];?></span></td>
	</tr>
	<tr>
		<th class="head"  colspan="6">지사연결</th>
	</tr>
	<tr>
		<th class="head left" >지사명</th>
		<td colspan="3">
			<select name="branchCode" style="width:auto;" onChange="_b2cPersonList('idPersonCode', this.value);">
				<option value="">--</option>
			<?
				$sql = "select b00_code, b00_name, b00_manager
						  from b00branch
						 where b00_domain = '".$gDomain."'
						 order by b00_name";
				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);
					?>
						<option value="<?=$row[0];?>" <? if ($branch['branch'] == $row[0]){echo 'selected';} ?>><?=$row[1].'['.$row[2].']';?></option>
					<?
				}
				$conn->row_free();
			?>
			</select>
		</td>
		<th class="head left" >담당자명</th>
		<td >
			<div id="idPersonCode">
				<select name="personCode" style="width:auto;">
				<?
					if (isSet($branch['branch'])){
						$sql = "select concat(b01_branch, b01_code) as code
								,      b01_name as name
								  from b01person
								 where b01_branch = '".$branch['branch']."'
								 order by b01_name";
						$conn->query($sql);
						$conn->fetch();
						$rowCount = $conn->row_count();

						echo '<option value="">--</option>';

						for($i=0; $i<$rowCount; $i++){
							$row = $conn->select_row($i);
							echo '<option value="'.$row['code'].'" '.($branch['branch'].$branch['person'] == $row['code'] ? 'selected' : '').'>'.$row['name'].'</option>';
						}

						$conn->row_free();
					}
				?>
				</select>
			</div>
		</td>
	</tr>
	<tr>
		<th class="head left" >이용서비스</th>
		<td colspan='5'>
			<input id='homeCareYN' name='homeCareYN' type='checkbox' value='Y' class='checkbox' <?=$homecare == 'Y' ? 'checked' : '';?>><label for="homeCareYN">장기요양</label>
			<input id='vouNurseYN' name='vouNurseYN' type='checkbox' value='Y' class='checkbox' <?=$branch['voucher'][0] == 'Y' ? 'checked' : '';?>><label for="vouNurseYN">가산간병</label>
			<input id='vouOldYN' name='vouOldYN' type='checkbox' value='Y' class='checkbox' <?=$branch['voucher'][1] == 'Y' ? 'checked' : '';?>><label for="vouOldYN">노인돌봄</label>
			<input id='vouBabyYN' name='vouBabyYN' type='checkbox' value='Y' class='checkbox' <?=$branch['voucher'][2] == 'Y' ? 'checked' : '';?>><label for="vouBabyYN">산모신생아</label>
			<input id='vouDisYN' name='vouDisYN' type='checkbox' value='Y' class='checkbox' <?=$branch['voucher'][3] == 'Y' ? 'checked' : '';?>><label for="vouDisYN">장애인활동지원</label>
		</td>
	</tr>
	<tr>
		<th class="head left" >시작일자</th>
		<td ><input name="startDate" type="text" value="<?=$myF->dateStyle($startDate);?>" class="date" maxlength="8" onkeydown="__onlyNumber(this);" onfocus="__replace(this, '-', '');" onblur="__getDate(this);"></td>
		<th class="head left" >계약일자</th>
		<td colspan='3'><input name="contDate" type="text" value="<?=$myF->dateStyle($center['contDate']);?>" class="date" maxlength="8" onkeydown="__onlyNumber(this);" onfocus="__replace(this, '-', '');" onblur="__getDate(this);"></td>
	</tr>
	<tr>
		<th class="head left" >비고</th>
		<td colspan="5">
			<textarea name="other" style="width:100%; height:45px;"><?=stripSlashes($branch['other']);?></textarea>
		</td>
	</tr>
</tbody>
</table>

<input name="centerCode" type="hidden" value="<?=$center['code'];?>">
<input name="centerKind" type="hidden" value="<?=$center['kind'];?>">

<div style="margin-top:3px; margin-right:3px; text-align:right;">
	<span class="btn_pack m icon"><span class="save"></span><button onClick="submitF();">저장</button></span>
	<span class="btn_pack m icon"><span class="delete"></span><button onClick="self.close();">닫기</button></span>
</div>

</form>
<?
	include_once('../inc/_footer.php');
?>
<div id="centerListLayer" style="position:absolute; z-index:1001; left:0; top:0; width:100%; height:100%; background-color:#fff; display:none;">
	<div style="position:absolute; z-index:1002; right:3px; top:3px; width:20px;">X</div>
	<div id="centerListDiv">

	</div>
</div>
<script language="javascript">
function submitF(){
	if (document.f.centerCode.value == ''){
		alert('기관을 선택하여 주십시오.');
		__showB2CenterList();
		return;
	}

	if (document.f.branchCode.value == ''){
		alert('연결할 지사를 선택하여 주십시오.');
		document.f.branchCode.focus();
		return;
	}

	if (document.f.personCode.value == ''){
		alert('연결할 지사의 담당자를 선택하여 주십시오.');
		document.f.personCode.focus();
		return;
	}

	document.f.action = 'b2c_center_add_ok.php';
	document.f.submit();
}
self.focus();
</script>
<?
	if ($_SESSION["userLevel"] == "A"){
		$findCode = $_POST["mCode"];
		$findKind = $_POST["mKind"];
	}else{
		$findCode = $_SESSION["userCenterCode"];

		if ($target == "yoyReg" or $target == "suReg"){
			$findKind = $_POST["currentMkind"] != "" ? $_POST["currentMkind"] : $_SESSION["userCenterKind"][0];
		}else{
			$findKind = $_POST["currentMkind"];
		}
	}

	$sql = $conn->get_query("00", $findCode, $findKind);
	$conn->query($sql);
	$row2 = $conn->fetch();

	if ($conn->row_count() > 0){
		$curMcode = $row2["m00_mcode"];
		$curMkind = $row2["m00_mkind"];
		$curCode1 = $row2["m00_code1"];
		$curCname = $row2["m00_cname"];
		$curMkindText = getKindName($curMkind);

		$notBody = "none";
		$yoyBody = "";
	}else{
		if ($_SESSION["userLevel"] == "A"){
			$curMcode = "";
			$curMkind = "";
			$curCode1 = "";
			$curCname = "";
			$curMkindText = "";
		}else{
			$curMcode = $findCode;
			$curMkind = $findKind;
			$curCode1 = "";
			$curCname = "";
			$curMkindText = getKindName($curMkind);
		}

		$notBody = "";
		$yoyBody = "none";
	}

	$conn->row_free();
?>

<tr>
	<td class="subject">기관정보</td>
	<td class="button" style="widht:500px; vertical-align:bottom;">
	<?
		if ($_POST["gubun"] == "search"){
			if ($target == "yoy"){
				echo "<span class='btn_pack m icon'><span class='refresh'></span><button type='button' onFocus='this.blur();' onClick='_centerYoyList(1);'>검색</button></span>";
			}else if($target == "su"){
				echo "<span class='btn_pack m icon'><span class='refresh'></span><button type='button' onFocus='this.blur();' onClick='_sugupjaList(1);'>검색</button></span>";
			}
		}
	?>
	</td>
</tr>
<tr>
	<td class="noborder" colspan="2">
		<table class="view_type1">
			<tr>
				<th style="width:15%;" scope="row">기관기호</th>
				<td style="width:20%;">
					<table>
						<tr>
							<td style="width:170px; border-top:0px; border-bottom:0px; padding-left:0px; padding-right:0px;"><div id="currentMcode"><?=$curMcode;?></div></td>
							<td style="border-top:0px; border-bottom:0px; text-align:left; padding-left:0px; padding-right:0px;">
							<?
								if ($_SESSION["userLevel"] == "A" and $_POST["gubun"] != "yoySearch"){
								?>
									<div id="currentButton" onClick="__helpMCenter(currentMcode,currentMkind,currentCode1,currentCname);" style="cursor:pointer;"><img src="../image/btn_find.png"></div>
								<?
								}
							?>
							</td>
						</tr>
					</table>
				</td>
				<th style="width:15%;" scope="row">요양기관 구분</th>
				<td style="width:50%;" colspan="3">
				<?
					if ($_SESSION["userLevel"] == "A"){
					?>
						<div id="currentMkind"><?=$curMkindText;?></div>
					<?
					}else{
					?>
						<select name="currentMkind" style="width:100%;" onChange="_setCenterName('<?=$target;?>', document.center.curMcode, document.center.currentMkind, currentCode1, currentCname);">
						<?
							if ($target == "yoyReg" or $target == "suReg"){
							}else{
							?>
								<option value="all">전체</option>
							<?
							}

							for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){

							?>
								<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $findKind){echo "selected";}?>><?=getKindName($_SESSION["userCenterKind"][$r]);?></option>
							<?
							}
						?>
						</select>
					<?
					}
				?>
				</td>
			</tr>
			<tr>
				<th scope="row">승인번호</th>
				<td><div id="currentCode1"><?=$curCode1;?></div></td>
				<th scope="row">기관명</th>
				<td colspan="3"><div id="currentCname"><?=$curCname;?></div></td>
			</tr>

			<?
				if ($_POST["gubun"] == "search"){
				?>
					<tr>
						<th scope="row"><? if (subStr($target, 0, 3) == "yoy"){echo '직원명';}else{echo '수급자명';}?></th>
						<td><input name="searchJname" type="text" value="<?=$_POST["jName"];?>" maxlength="6" style="width:100%;"></td>
						<th scope="row">연락처</th>
						<td style="width:20%;"><input name="searchTel" type="text" value="<?=$_POST["cTel"];?>" maxlength="11" style="width:100%;" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);"></td>
						<?
							if ($target == "yoy"){
								$temp_stat = $_POST["stat"] != '' ? $_POST["stat"] : '1';
							?>
								<th style="width:15%;" scope="row">고용상태</th>
								<td style="width:15%;">
									<select name="searchStat" style="width:100%;">
									<option value="all">전체</option>
									<option value="1" <?=$temp_stat == "1" ? "selected" : "";?>>활동</option>
									<option value="2" <?=$temp_stat == "2" ? "selected" : "";?>>휴직</option>
									<option value="9" <?=$temp_stat == "9" ? "selected" : "";?>>퇴사</option>
									</select>
								</td>
							<?
							}else{
							?>
								<th style="width:15%;" scope="row">수급현황</th>
								<td style="width:15%;">
									<select name="searchStat" style="width:100%;">
									<option value="">전체</option>
									<?
										$statusList = $definition->SugupjaStatusList();

										for($i=0; $i<sizeOf($statusList); $i++){
											echo '<option value="'.$statusList[$i]['code'].'" '.($_POST['stat'] == $statusList[$i]['code'] ? 'selected' : '').'>'.$statusList[$i]['name'].'</option>';
										}
									?>
									</select>
								</td>
							<?
							}
						?>
					</tr>
				<?
				}else{
				?>
					<input name="searchJname" type="hidden" value="<?=$_POST["jName"];?>">
					<input name="searchTel"   type="hidden" value="<?=$_POST["cTel"];?>">
					<input name="searchStat"  type="hidden" value="<?=$_POST["stat"];?>">
				<?
				}
			?>
		</table>
	</td>
</tr>
<input name="gubun" type="hidden" value="<?=$_POST["gubun"];?>">
<input name="page" type="hidden" value="<?=$_POST["page"] ? $_POST["page"] : "1";?>">
<input name="searchMcode" type="hidden" value="<?=$_POST["searchMcode"];?>">
<input name="searchMkind" type="hidden" value="<?=$_POST["searchMkind"];?>">
<input name="searchCode1" type="hidden" value="<?=$_POST["searchCode1"];?>">
<input name="searchCname" type="hidden" value="<?=$_POST["searchCname"];?>">
<input name="curMcode" type="hidden" value="<?=$curMcode;?>">
<input name="curMkind" type="hidden" value="<?=$curMkind;?>">
<input name="curCode1" type="hidden" value="<?=$curCode1;?>">
<input name="curCname" type="hidden" value="<?=$curCname;?>">

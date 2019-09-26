<?
	$sql = $conn->get_query("00", $_POST["mCode"], $_POST["mKind"]);
	$conn->query($sql);
	$row2 = $conn->fetch();

	if ($conn->row_count() > 0){
		$curMcode = $row2["m00_mcode"];
		$curMkind = $row2["m00_mkind"];
		$curCode1 = $row2["m00_code1"];
		$curCname = $row2["m00_cname"];
		
		switch($curMkind){
			case "0":
				$curMkindText = "재가요양기관";
				break;
			case "1":
				$curMkindText = "가사간병(바우처)";
				break;
			case "2":
				$curMkindText = "노인돌봄(바우처)";
				break;
			case "3":
				$curMkindText = "산모신생아(바우처)";
				break;
			case "4":
				$curMkindText = "장애인 활동보조(바우처)";
				break;
		}

		$notBody = "none";
		$yoyBody = "";
	}else{
		$curMcode = "";
		$curMkind = "";
		$curCode1 = "";
		$curCname = "";
		$curMkindText = "";

		$notBody = "";
		$yoyBody = "none";
	}

	$conn->row_free();
?>
<tr>
	<td class="subject">기관정보</td>
	<td class="button" style="widht:500px; vertical-align:bottom;">
		<a onClick="_centerYoyList(1);"><img src="../image/btn8.gif"></a>
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
							<td style="border-top:0px; border-bottom:0px; text-align:left; padding-left:0px; padding-right:0px;"><div class="find" onClick="__helpMCenter(currentMcode,currentMkind,currentCode1,currentCname);"></div></td>
						</tr>
					</table>
				</td>
				<th style="width:15%;" scope="row">요양기관 구분</th>
				<td style="width:50%;"><div id="currentMkind"><?=$curMkindText;?></div></td>
			</tr>
			<tr>
				<th scope="row">승인번호</th>
				<td><div id="currentCode1"><?=$curCode1;?></div></td>
				<th scope="row">기관명</th>
				<td><div id="currentCname"><?=$curCname;?></div></td>
			</tr>
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
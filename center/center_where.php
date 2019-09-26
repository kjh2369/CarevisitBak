<table style="width:100%;">
	<?
		if (!$yoy){
		?>
			<tr>
				<td class="title" colspan="2">센터 마스터 조회</td>
			</tr>
			<tr>
				<td class="subject">검색조건</td>
				<td class="button" style="text-align:right; vertical-align:bottom;">
					<a onClick="_centerList();"><img src="../image/btn8.gif"></a>
				</td>
			</tr>
		<?
		}else{
		?>
			<tr>
				<td class="subject">검색조건</td>
				<td class="button" style="padding-right:10px; text-align:right; vertical-align:bottom;">
					<a onClick="_submit();"><img src="../image/btn8.gif"></a>
				</td>
			</tr>
		<?
		}
	?>
	<tr>
		<td class="noborder" colspan="2">
			<form name="center" method="post" action="center.php">
				<table class="my_table my_border">
					<tr>
						<th scope="row">기관명</th>
						<td>
						<?
							if ($_SESSION["userLevel"] == "A"){
							?>
								<div id="currentCname" style="display:none;"></div>
								<input name="searchCname" type="text" value="<?=$_POST["cName"] != "" ? $_POST["cName"] : $_POST["searchCname"];?>" onkeypress="if(event.keyCode==13){_submit();}" maxlength="20" style="width:100%;">
							<?
							}else{
							?>
								<div id="currentCname"><?=$_SESSION["userCenterName"];?></div>
								<input name="searchCname" type="hidden" value="">
							<?
							}
						?>
						</td>
						<th style="width:20%;" scope="row">승인번호</th>
						<td>
						<?
							if ($_SESSION["userLevel"] == "A"){
							?>
								<div id="currentCode1" style="display:none;"></div>
								<input name="searchCode1" type="text" value="<?=$_POST["code1"] != "" ? $_POST["code1"] : $_POST["searchCode1"];?>" maxlength="15" style="width:100%;">
							<?
							}else{
							?>
								<div id="currentCode1"><?=$_SESSION["userCenterGiho"];?></div>
								<input name="searchCode1" type="hidden" value="<?=$_SESSION["userCenterGiho"];?>">
							<?
							}
						?>
						</td>
						<!--th style="width:20%;"scope="row">요양기관 구분</th>
						<td style="width:30%;">
							<select name="searchMkind" style="width:100%;" onChange="_setCenterName(document.center.searchMcode, document.center.searchMkind, currentCode1, currentCname, document.center.searchCode1, document.center.searchCname);">
								<option value="all">전체</option>
								<option value="0"<? if(($_POST["mKind"] != "" ? $_POST["mKind"] : $_POST["searchMkind"]) == "0"){ echo "selected"; }?>>재가요양기관</option>
								<option value="1"<? if(($_POST["mKind"] != "" ? $_POST["mKind"] : $_POST["searchMkind"]) == "1"){ echo "selected"; }?>>가사간병(바우처)</option>
								<option value="2"<? if(($_POST["mKind"] != "" ? $_POST["mKind"] : $_POST["searchMkind"]) == "2"){ echo "selected"; }?>>노인돌봄(바우처)</option>
								<option value="3"<? if(($_POST["mKind"] != "" ? $_POST["mKind"] : $_POST["searchMkind"]) == "3"){ echo "selected"; }?>>산모신생아(바우처)</option>
								<option value="4"<? if(($_POST["mKind"] != "" ? $_POST["mKind"] : $_POST["searchMkind"]) == "4"){ echo "selected"; }?>>장애인 활동보조(바우처)</option>
							</select>						
						</td-->
						<input name="searchMkind" type="hidden" value=''>
					</tr>
					<tr>
						<th style="width:20%;" scope="row">기관기호</th>
						<td style="width:30%;">
						<?
							if ($_SESSION["userLevel"] == "A"){
							?>
								<input name="searchMcode" type="text" value="<?=$_POST["mCode"] != "" ? $_POST["mCode"] : $_POST["searchMcode"];?>" maxlength="15" style="width:100%;">
							<?
							}else{
							?>
								<div><?=$_SESSION["userCenterCode"];?></div>
								<input name="searchMcode" type="hidden" value="<?=$_SESSION["userCenterCode"];?>">
							<?
							}
						?>
						</td>	
					</tr>
				</table>
				<input name="r_gubun" type="hidden" value="<?=$_REQUEST["r_gubun"];?>">
				<input name="gubun" type="hidden" value="<?=$_POST["gubun"];?>">
				<input name="page" type="hidden" value="<?=$_POST["page"] ? $_POST["page"] : "1";?>">
			</form>
		</td>
	</tr>
	<tr>
		<td class="subject" colspan="2">센터 목록</td>
	</tr>
</table>
<?
	$backEditMode = $editMode;

	if ($isDemo) $editMode = false;

?>
<table class="my_table tb_t" style="width:100%;">
	<colgroup>
		<col width="130px">
		<col width="180px">
		<col width="130px">
		<col width="180px">
		<col width="130px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td><?
				if ($editMode){?>
					<input id="mCode" name="mCode" type="text" value="<?=$mCode;?>" maxlength="15" style="ime-mode:disabled;" onFocus="this.select();" onChange="return _exist('mCode');" tag="승인번호를 입력하여 주십시오."><?
				}else{?>
					<span style="height:25px; line-height:25px; margin-left:5px; font-weight:bold;"><?=$mCode;?></span><input id="mCode" name="mCode" type="hidden" value="<?=$mCode;?>"><?
				}?>
			</td>
			<th>기관명</th>
			<td><input name="storeNm" type="text" value='<?=$storeNm;?>' maxlength="30" tag="가맹점명을 입력하여 주십시오."></td>
			<!--th>사용일자</th>
			<td class="left last"><?=$startDate.' / '.$contDate?></td-->
			<th>&nbsp;</th>
			<td class="last">&nbsp;</td>
		</tr>
		<tr>
			<th>전화번호</th>
			<td><input name="cTel" type="text" value="<?=$cTel;?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" tag="가맹점전화번호를 입력하여 주십시오."></td>
			<th>FAX번호</th>
			<td><input name="faxNo" type="text" value="<?=$faxNo;?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" tag="FAX번호를 입력하여 주십시오."></td>
			<th>&nbsp;</th>
			<td class="last">&nbsp;</td>
		</tr>
		<tr>
			<th>대표자명</th>
			<td><input name="mName" type="text" value="<?=$mName;?>" maxlength="10" tag="대표자명을 입력하여 주십시오."></td>
			<th>휴대폰</th>
			<td class=""><input name="txtMobile" type="text" value="<?=$myF->phoneStyle($mobile);?>" class="phone"></td>
			<th>홈페이지</th>
			<td class="left last">http:// <input name="homepage" type="text" value="<?=$homepage;?>" maxlength="30" class="iw200"></td>
		</tr>
		<tr>
			<th>주소</th>
			<td class="" colspan="3">
				<div style="float:left; width:auto">
					<input name="cPostNo" type="text" value="<?=$cPostNo;?>" maxlength="5" class="no_string" style="width:40px; text-align:center; margin-right:3px;">
					<!--input name="cPostNo1" type="text" value="<?=$cPostNo1;?>" maxlength="3" class="phone" style="width:30px; text-align:center;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"> -
					<input name="cPostNo2" type="text" value="<?=$cPostNo2;?>" maxlength="3" class="phone" style="width:30px; text-align:center; margin-right:0;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"-->
				</div>
				<!--div style="float:left; width:auto; margin-left:5px; padding-top:1px;">
					<span class="btn_pack small"><button type="button" onClick="__helpAddress(document.f.cPostNo1, document.f.cPostNo2, document.f.cAddr1, document.f.cAddr2);">찾기</button></span>
				</div-->
				<div style="float:left; width:auto">
					<input name="cAddr1" type="text" value="<?=$cAddr1;?>" maxlength="20" style="width:150px;">
					<input name="cAddr2" type="text" value="<?=$cAddr2;?>" maxlength="30" style="width:200px;">
				</div>
			</td>
			<th>이메일</th>
			<td class="last"><input name="email" type="text" value="<?=$email;?>" maxlength="30" class="iw200"></td>
		</tr>
		<tr>
			<th>ICON</th>
			<td class="left" colspan="3">
				<div style="float:left; width:auto;"><div id="icon_view" style="position:absolute; width:auto; margin-top:2px; width:35px; height:20px;"><? if(!empty($icon)){echo '<img id=\'icon_img\' src=\'../mem_picture/'.$icon.'\' onload=\'load_icon(this);\'>';} ?></div></div>
				<div style="float:left; width:50px; margin-left:40px; margin-top:2px; background:url(../image/find_file.gif) no-repeat left 50%;"><input type="file" name="icon" id="icon" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-42px;" onchange="show_icon(this, 'icon');"></div>
				<div style="float:left; width:auto; margin-left:0;">(jpg, gif파일만 업로드 가능합니다.)</div>
				<div style="float:left; width:auto; margin-left:5px; margin-top:3px;">
					<span class="btn_pack small"><button onclick="lfRemove('ICON','<?=$icon;?>');">ICON 삭제</button></span>
				</div>
				<input name="icon_back" type="hidden" value="<?=$icon;?>">
			</td><?
			if ($_SESSION['userLevel'] == 'A'){
				$sql = 'select m97_pass
						  from m97user
						 where m97_user = \''.$mCode.'\'';
				$ctPass = $conn->get_data($sql);?>
				<th>비밀번호</th>
				<td class="last"><input id="centerPw" name="centerPw" type="password" value="" style="width:100%;"></td><?
			}else{?>
				<th>사업자등록번호</th>
				<td class="last"><input name="cCode" type="text" value="<?=$cCode;?>" maxlength="10" class="phone iw200" alt="biz"></td><?
			}?>
		</tr>
		<tr>
			<th>직인등록</th>
			<td class="left" colspan="3">
				<div style="float:left; width:auto;"><div id="jikin_view" style="position:absolute; width:auto; margin-top:2px; width:35px; height:20px;"><? if(!empty($jikin)){echo '<img id=\'jikin_img\' src=\'../mem_picture/'.$jikin.'\' onload=\'load_icon(this);\'>';} ?></div></div>
				<div style="float:left; width:50px; margin-left:40px; margin-top:2px; background:url(../image/find_file.gif) no-repeat left 50%;"><input type="file" name="jikin" id="jikin" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-42px;" onchange="show_icon(this, 'jikin');"></div>
				<div style="float:left; width:auto; margin-left:0;">(jpg, gif파일만 업로드 가능합니다.)</div>
				<div style="float:left; width:auto; margin-left:5px; margin-top:3px;">
					<span class="btn_pack small"><button onclick="lfRemove('JIKIN','<?=$jikin;?>');">직인 삭제</button></span>
				</div>
				<input name="jikin_back" type="hidden" value="<?=$jikin;?>">
			</td>
			<td class="left last" colspan="2">세금계산서 발행시 메일, 사업등록번호 필수입력</td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
	function lfRemove(gbn,file){
		$.ajax({
			type :'POST'
		,	url  :'./pic_remove.php'
		,	data :{
				'gbn':gbn
			,	'file':file
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					if (gbn == 'ICON'){
						$('#icon_view').html('');
					}else{
						$('#jikin_view').html('');
					}
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<?
	$editMode = $backEditMode;
?>
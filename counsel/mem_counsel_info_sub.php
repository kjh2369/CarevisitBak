<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="40px">
		<col width="60px">
		<col width="140px">
		<col width="40px">
		<col width="40px">
		<col width="70px">
		<col width="40px">
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th rowspan="3">소재</th>
			<th>우편번호</th>
			<td>
				<input name="counsel_postno1" type="text" value="<?=substr($mem['mem_postno'],0,3);?>" tabindex="21" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"> -
				<input name="counsel_postno2" type="text" value="<?=substr($mem['mem_postno'],3);?>" tabindex="21" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();">
				<span class="btn_pack small"><button type="button" onClick="__helpAddress(__getObject('counsel_postno1'),__getObject('counsel_postno2'),__getObject('counsel_addr'),__getObject('counsel_addr_dtl'));">찾기</button></span>
			</td>
			<th rowspan="3">연락처</th>
			<th>유선</th>
			<td class="">
				<input name="counsel_phone" type="text" tabindex="11" value="<?=$myF->phoneStyle($mem['mem_phone']);?>" maxlength="11" class="phone" onkeydown="__onlyNumber(this);">
			</td>
			<th rowspan="3">기타</th>
			<th>학력</th>
			<td class="last" colspan="2">
				<input name="counsel_edu_level" type="radio" value="1" tabindex="31" class="radio" <? if($mem['mem_edu_lvl'] == '1'){?>checked<?} ?>>중졸이하
				<input name="counsel_edu_level" type="radio" value="3" tabindex="31" class="radio" <? if($mem['mem_edu_lvl'] == '3'){?>checked<?} ?>>고졸
				<input name="counsel_edu_level" type="radio" value="5" tabindex="31" class="radio" <? if($mem['mem_edu_lvl'] == '5'){?>checked<?} ?>>대학중퇴
				<input name="counsel_edu_level" type="radio" value="7" tabindex="31" class="radio" <? if($mem['mem_edu_lvl'] == '7'){?>checked<?} ?>>대졸이상
			</td>
		</tr>
		<tr>
			<td colspan="2"><input name="counsel_addr" type="text" value="<?=$mem['mem_addr'];?>" tabindex="21" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();"></td>
			<th>무선</th>
			<td class=""><input name="counsel_mobile" type="text" tabindex="11" value="<?=$myF->phoneStyle($mem['mem_mobile']);?>" maxlength="11" class="phone" onkeydown="__onlyNumber(this);"></td>
			<th>구분</th>
			<td class="last" colspan="2">
				<input name="counsel_gubun" type="radio" value="1" tabindex="31" class="radio" <? if($mem['mem_gbn'] == '1'){?>checked<?} ?>>일반
				<input name="counsel_gubun" type="radio" value="3" tabindex="31" class="radio" <? if($mem['mem_gbn'] == '3'){?>checked<?} ?>>차상위
				<input name="counsel_gubun" type="radio" value="5" tabindex="31" class="radio" <? if($mem['mem_gbn'] == 'A'){?>checked<?} ?>>기초수급자
			</td>
		</tr>
		<tr>
			<td colspan="2"><input name="counsel_addr_dtl" type="text" value="<?=$mem['mem_addr_dtl'];?>" tabindex="21" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();">
			<th>e-mail</th>
			<td class=""><input name="counsel_email" type="text" tabindex="11" value="<?=$mem['mem_email'];?>" style="width:100%;"></td>
			<th>주거</th>
			<td class="last" colspan="2">
				<input name="counsel_abode" type="radio" value="1" tabindex="31" class="radio" <? if($mem['mem_abode'] == '1'){?>checked<?} ?>>전세
				<input name="counsel_abode" type="radio" value="3" tabindex="31" class="radio" <? if($mem['mem_abode'] == '3'){?>checked<?} ?>>월세
				<input name="counsel_abode" type="radio" value="5" tabindex="31" class="radio" <? if($mem['mem_abode'] == '5'){?>checked<?} ?>>자가
			</td>
		</tr>
	</tbody>
</table>
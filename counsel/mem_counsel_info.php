<?
	if ($is_path != 'counsel'){
		include_once('mem_counsel_head.php');
	}
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="105px">
		<col width="80px">
		<col width="80px">
		<col width="200px">
		<col width="70px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center top bottom" style="padding-top:5px;" rowspan="5">
				<div id="pictureView" style="width:90px; height:120px;"><img id="img_picture" src="<?=$mem['mem_picture'];?>" style="border:1px solid #000;" width="90" height="120">
			</td>
			<th rowspan="3">개인정보</th>
			<th>주민번호</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->issStyle($jumin).'</div>';
				}else{
					if ($counsel_mode == 1){?>
						<input name="ssn1" type="text" tabindex="1" value="" maxlength="6" class="no_string" style="width:50px;" onkeydown="__onlyNumber(this);" onkeyup="check_ssn('ssn1','ssn2');"> -
						<input name="ssn2" type="text" tabindex="1" value="" maxlength="7" class="no_string" style="width:55px;" onkeydown="__onlyNumber(this);" onkeyup="check_ssn('ssn1','ssn2');"><?

						if ($is_path != 'counsel'){
							echo '<span class="btn_pack small"><button type="button" onClick="find_counsel();">찾기</button></span>';
						}
					}else{?>
						<div class="left"><?=$myF->issStyle($ssn);?></div>
						<input name="ssn" type="hidden" value="<?=$ed->en($ssn);?>"><?
					}
				}
			?>
			</td>
			<th rowspan="3">연락처</th>
			<th>유선</th>
			<td class="last">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->phoneStyle($mem['mem_phone']).'</div>';
				}else{?>
					<input name="counsel_phone" type="text" tabindex="11" value="<?=$myF->phoneStyle($mem['mem_phone']);?>" maxlength="11"class="iw200" onkeydown="__onlyNumber(this);"><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>성명</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$mem['mem_nm'].'</div>';
				}else{?>
					<input name="counsel_name" type="text" tabindex="1" value="<?=$mem['mem_nm'];?>" maxlength="7" onkeydown="__enterFocus();"><?
				}
			?>
			</td>
			<th>무선</th>
			<td class="last">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$myF->phoneStyle($mem['mem_mobile']).'</div>';
				}else{?>
					<input name="counsel_mobile" type="text" tabindex="11" value="<?=$myF->phoneStyle($mem['mem_mobile']);?>" maxlength="11"class="iw200" onkeydown="__onlyNumber(this);" style="margin-right:0;">
					<!--input name="counsel_mobile_modelno" type="text" tabindex="11" value="<?=$myF->phoneStyle($mem['mem_mobile_modelno']);?>" style="margin-left:0;"--><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th>결혼여부</th>
			<td>
			<?
				if ($view_type == 'read'){
					switch($mem['mem_marry']){
						case 'Y':
							$tmp = '결혼';
							break;
						case 'N':
							$tmp = '미혼';
							break;
						default:
							$tmp = '기타';
					}
					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{?>
					<input name="counsel_marry" type="radio" value="Y" tabindex="1" class="radio" onkeydown="__enterFocus();" <? if($mem['mem_marry'] == 'Y'){?>checked<?} ?>>결혼
					<input name="counsel_marry" type="radio" value="N" tabindex="1" class="radio" onkeydown="__enterFocus();" <? if($mem['mem_marry'] == 'N'){?>checked<?} ?>>미혼
					<input name="counsel_marry" type="radio" value="X" tabindex="1" class="radio" onkeydown="__enterFocus();" <? if($mem['mem_marry'] == 'X'){?>checked<?} ?>>기타<?
				}
			?>
			</td>
			<th>e-mail</th>
			<td class="last">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$mem['mem_email'].'</div>';
				}else{?>
					<input name="counsel_email" type="text" tabindex="11" value="<?=$mem['mem_email'];?>"class="iw200"><?
				}
			?>
			</td>
		</tr>
		<tr>
			<th rowspan="3">소재</th>
			<th>우편번호</th>
			<td>
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$mem['mem_postno'].'</div>';
				}else{?>
					<input id="counsel_postno" name="counsel_postno" type="text" value="<?=$client['postno'];?>" tabindex="21" maxlength="6" class="no_string" style="width:50px; margin-right:0;">
					<span class="btn_pack m"><button onclick="lfPostno();">찾기</button></span>
					
					<!--span class="btn_pack small"><button type="button" onClick="__helpAddress(__getObject('counsel_postno1'),__getObject('counsel_postno2'),__getObject('counsel_addr'),__getObject('counsel_addr_dtl'));">찾기</button></span--><?
				}
			?>
			</td>
			<th>학력</th>
			<td class="last" colspan="2">
			<?
				if ($view_type == 'read'){
					switch($mem['mem_edu_lvl']){
						case '1':
							$tmp = '중졸이하';
							break;
						case '3':
							$tmp = '고졸';
							break;
						case '5':
							$tmp = '대학중퇴';
							break;
						case '7':
							$tmp = '대졸이상';
							break;
					}
					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{?>
					<input name="counsel_edu_level" type="radio" value="1" tabindex="31" class="radio" <? if($mem['mem_edu_lvl'] == '1'){?>checked<?} ?>>중졸이하
					<input name="counsel_edu_level" type="radio" value="3" tabindex="31" class="radio" <? if($mem['mem_edu_lvl'] == '3'){?>checked<?} ?>>고졸
					<input name="counsel_edu_level" type="radio" value="5" tabindex="31" class="radio" <? if($mem['mem_edu_lvl'] == '5'){?>checked<?} ?>>대학중퇴
					<input name="counsel_edu_level" type="radio" value="7" tabindex="31" class="radio" <? if($mem['mem_edu_lvl'] == '7'){?>checked<?} ?>>대졸이상<?
				}
			?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$mem['mem_addr'].'</div>';
				}else{?>
					<input id="counsel_addr" name="counsel_addr" type="text" value="<?=$mem['mem_addr'];?>" tabindex="21" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();"><?
				}
			?>
			</td>
			<th>구분</th>
			<td class="last" colspan="2">
			<?
				if ($view_type == 'read'){
					switch($mem['mem_gbn']){
						case '1':
							$tmp = '일반';
							break;
						case '3':
							$tmp = '차상위';
							break;
						case 'A':
							$tmp = '기초수급자';
							break;
					}
					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{?>
					<input name="counsel_gubun" type="radio" value="1" tabindex="31" class="radio" <? if($mem['mem_gbn'] == '1'){?>checked<?} ?>>일반
					<input name="counsel_gubun" type="radio" value="3" tabindex="31" class="radio" <? if($mem['mem_gbn'] == '3'){?>checked<?} ?>>차상위
					<input name="counsel_gubun" type="radio" value="A" tabindex="31" class="radio" <? if($mem['mem_gbn'] == 'A'){?>checked<?} ?>>기초수급자<?
				}
			?>
			</td>
		</tr>
		<tr>
			<td class="center top" rowspan="1">
			<?
				if ($view_type == 'read'){
				}else{?>
					<div style="width:50px; height:18px; background:url(../image/find_file.gif) no-repeat left 50%;"><input type="file" name="counsel_mem_picture" id="file" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin:0;" onchange="__showLocalImage(this,'pictureView');"></div><?
				}
			?>
			</td>
			<td colspan="2">
			<?
				if ($view_type == 'read'){
					echo '<div class=\'left\'>'.$mem['mem_addr_dtl'].'</div>';
				}else{?>
					<input id="counsel_addr_dtl" name="counsel_addr_dtl" type="text" value="<?=$mem['mem_addr_dtl'];?>" tabindex="21" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();"><?
				}
			?>
			</td>
			<th>주거</th>
			<td class="last" colspan="2">
			<?
				if ($view_type == 'read'){
					switch($mem['mem_abode']){
						case '1':
							$tmp = '전세';
							break;
						case '3':
							$tmp = '월세';
							break;
						case '5':
							$tmp = '자가';
							break;
					}
					echo '<div class=\'left\'>'.$tmp.'</div>';
				}else{?>
					<input name="counsel_abode" type="radio" value="1" tabindex="31" class="radio" <? if($mem['mem_abode'] == '1'){?>checked<?} ?>>전세
					<input name="counsel_abode" type="radio" value="3" tabindex="31" class="radio" <? if($mem['mem_abode'] == '3'){?>checked<?} ?>>월세
					<input name="counsel_abode" type="radio" value="5" tabindex="31" class="radio" <? if($mem['mem_abode'] == '5'){?>checked<?} ?>>자가<?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
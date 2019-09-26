<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$seq = $_POST['seq'];

	$sql = 'SELECT	*
			FROM	care_client_normal
			WHERE	org_no		= \''.$code.'\'
			AND		normal_sr	= \''.$sr.'\'
			AND		normal_seq	= \''.$seq.'\'';

	$basic = $conn->get_array($sql);
?>
<script type="text/javascript" src="../sugupja/client.js"></script>
<script type="text/javascript">
	$(document).ready(function(){

	});

	function lfNew(){
		$('#seq').val('');

		$('input').each(function(){
			$(this).val('');
		});

		$('select').each(function(){
			$(this).val('');
		});
	}

	function lfList(){
		var f = document.f;

		f.action = '../care/care.php?sr=<?=$sr;?>&type=83';
		f.submit();
	}

	function lfSave(){
		if (!$('#txtName').val()){
			alert('성명을 입력하여 주십시오.');
			$('#txtName').focus();
			return;
		}

		var data = {};

		$('input').each(function(){
			data[$(this).attr('id')] = $(this).val();
		});

		$('select').each(function(){
			data[$(this).attr('id')] = $(this).val();
		});

		$.ajax({
			type: 'POST'
		,	url : './care_normal_reg_save.php'
		,	data: data
		,	beforeSend: function (){
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					location.replace('./care.php?sr=<?=$sr;?>&type=83');
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfDelete(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시곘습니까?')) return;

		var data = {};

		data['seq']	= $('#seq').val();
		data['sr']	= '<?=$sr;?>';

		$.ajax({
			type: 'POST'
		,	url : './care_normal_delete.php'
		,	data: data
		,	beforeSend: function (){
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					location.replace('./care.php?sr=<?=$sr;?>&type=83');
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">일반접수등록(<?=$title;?>)</div>

<div class="my_border_blue" style="margin-top:10px; margin-left:10px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px" span="2">
			<col width="150px">
			<col width="70px">
			<col width="50px">
			<col width="130px">
			<col width="70px">
			<col width="40px">
			<col width="40px">
			<col width="35px">
			<col width="35px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="bold last" colspan="20">1.기본정보</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="head">주민번호</th>
				<td colspan="2">
					<input id="txtJumin1" name="txtJumin" type="text" value="<?=SubStr($basic['jumin'],0,6);?>" maxlength="6" style="width:50px;"> -
					<input id="txtJumin2" name="txtJumin" type="text" value="<?=SubStr($basic['jumin'],6,7);?>" maxlength="7" style="width:55px;">
				</td>
				<th class="head" rowspan="2">연락처</th>
				<th class="head">유선</th>
				<td>
					<input id="txtPhone" name="txt" type="text" value="<?=$myF->phoneStyle($basic['phone']);?>" class="phone">
				</td>
				<th class="head">접수번호</th>
				<td class="left" colspan="3"><span id="txtFixNo" class="bold"><?=$basic['normal_seq'];?></span></td>
				<th class="head">유형</th>
				<td class="last">
					<select id="cboKindGbn" style="width:auto;">
						<option value=""></option>
						<option value="1" <?=$basic['kind_gbn'] == '1' ? 'selected' : '';?>>수급자</option>
						<option value="2" <?=$basic['kind_gbn'] == '2' ? 'selected' : '';?>>차상위</option>
						<option value="3" <?=$basic['kind_gbn'] == '3' ? 'selected' : '';?>>150%</option>
					</select>
				</td>
			</tr>
			<tr>
				<th class="head">성명</th>
				<td colspan="2">
					<input id="txtName" name="txt" type="text" value="<?=$basic['name'];?>">
				</td>
				<th class="head">무선</th>
				<td>
					<input id="txtMobile" name="txt" type="text" value="<?=$myF->phoneStyle($basic['mobile']);?>" class="phone">
				</td>
				<th class="head" rowspan="2">결혼정보</th>
				<th class="head">결혼</th>
				<td class="">
					<select id="cboMarry" name="cbo" style="width:auto;">
						<option value="">-</option><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'MR\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($row['code'] == $basic['marry_gbn'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
				<th class="head" rowspan="2">건보<br>공단</th>
				<th class="head">등급</th>
				<td class="last">
					<select id="cboLvl" name="cbo" style="width:auto;">
						<option value="">-</option>
						<option value="1" <?=($basic['longcare_lvl'] == '1' ? 'selected' : '');?>>1등급</option>
						<option value="2" <?=($basic['longcare_lvl'] == '2' ? 'selected' : '');?>>2등급</option>
						<option value="3" <?=($basic['longcare_lvl'] == '3' ? 'selected' : '');?>>3등급</option>
						<option value="4" <?=($basic['longcare_lvl'] == '4' ? 'selected' : '');?>>4등급</option>
						<option value="5" <?=($basic['longcare_lvl'] == '5' ? 'selected' : '');?>>5등급</option>
						<option value="7" <?=($basic['longcare_lvl'] == '7' ? 'selected' : '');?>>등급 외 A,B</option>
						<option value="9" <?=($basic['longcare_lvl'] == '9' ? 'selected' : '');?>>일반</option>
					</select>
				</td>
			</tr>
			<tr>
				<th class="head" rowspan="3">주소</th>
				<th class="head">우편번호</th>
				<td>
					<input id="txtPostno1" name="txt" type="text" value="<?=SubStr($basic['postno'],0,3);?>" class="no_string" maxlength="3" style="width:30px;"> -
					<input id="txtPostno2" name="txt" type="text" value="<?=SubStr($basic['postno'],3);?>" class="no_string" maxlength="3" style="width:30px;">
					<!--span class="btn_pack small"><a href="#" onclick="__helpAddress(document.f.txtPostno1, document.f.txtPostno2, document.f.txtAddr, document.f.txtAddrDtl);">찾기</a></span-->
				</td>
				<th class="head" rowspan="3">보호자</th>
				<th class="head">성명</th>
				<td>
					<input id="txtGrdNm" name="txt" type="text" value="<?=$basic['grd_nm'];?>">
				</td>
				<th class="head">동거</th>
				<td class="">
					<select id="cboCohabit" name="cbo" style="width:auto;">
						<option value="">-</option><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'CB\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($basic['cohabit_gbn'] == $row['code'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
				<th class="head">구분</th>
				<td class="last">
					<select id="cboGbn" name="cbo" style="width:auto;">
						<option value="">-</option>
						<option value="3" <?=($basic['longcare_gbn'] == '3' ? 'selected' : '');?>>기초</option>
						<option value="2" <?=($basic['longcare_gbn'] == '2' ? 'selected' : '');?>>의료</option>
						<option value="4" <?=($basic['longcare_gbn'] == '4' ? 'selected' : '');?>>경감</option>
						<option value="1" <?=($basic['longcare_gbn'] == '1' ? 'selected' : '');?>>일반</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input id="txtAddr" name="txt" type="text" value="<?=$basic['addr'];?>" style="width:90%;">
				</td>
				<th class="head">주소</th>
				<td>
					<input id="txtGrdAddr" name="txt" type="text" value="<?=$basic['grd_addr'];?>" style="width:100%;">
				</td>
				<th class="head" rowspan="2">기타</th>
				<th class="head">학력</th>
				<td class="" colspan="3">
					<select id="cboEdu" name="cbo" style="width:auto;">
						<option value="">-</option><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'EL\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($basic['edu_gbn'] == $row['code'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
				<td class="bottom last"></td>
			</tr>
			<tr>
				<td class="" colspan="2">
					<input id="txtAddrDtl" name="txt" type="text" value="<?=$basic['addr_dtl'];?>" style="width:90%;">
				</td>
				<th class="head">연락처</th>
				<td class="">
					<input id="txtGrdTel" name="txt" type="text" value="<?=$myF->phoneStyle($basic['grd_telno']);?>" class="phone">
				</td>
				<th class="head">종교</th>
				<td class="" colspan="3">
					<select id="cboRel" name="cbo" style="width:auto;">
						<option value="">-</option><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'RG\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($basic['rel_gbn'] == $row['code'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
				<td class="last"></td>
			</tr>
			<!--
				<tr>
					<th class="center bottom">등록일</th>
					<td class="bottom" colspan="2"><input id="txtRegDt" type="text" value="<?=$myF->dateStyle($basic['reg_dt']);?>" class="date"></td>
					<th class="center bottom">종결일</th>
					<td class="bottom" colspan="2"><input id="txtEndDt" type="text" value="<?=$myF->dateStyle($basic['end_dt']);?>" class="date"></td>
					<th class="center bottom">종결사유</th>
					<td class="bottom last" colspan="5"><input id="txtEndReason" type="text" value="<?=$basic['end_reason'];?>" style="width:100%;"></td>
				</tr>
			-->
			<script type="text/javascript">
				function lfSetRst(obj){
					$('#trEnd').hide();
					$('#trRst').hide();

					if ($(obj).val() == '03'){
						$('#trEnd').show();
					}else if ($(obj).val() == '99'){
						$('#trRst').show();
					}
				}
			</script>
			<tr>
				<th class="center ">등록일</th>
				<td class="" colspan="2"><input id="txtRegDt" type="text" value="<?=$myF->dateStyle($basic['reg_dt']);?>" class="date"></td>
				<th class="center ">처리일자</th>
				<td class="" colspan="2"><input id="txtRstDt" type="text" value="<?=$myF->dateStyle($basic['rst_dt']);?>" class="date"></td>
				<th class="center ">처리결과</th>
				<td class="last" colspan="5">
					<select id="cboRstReason" style="width:auto;" onchange="lfSetRst(this);">
						<option value="">-선택하여 주십시오.-</option>
						<option value="01" <?=$basic['rst_reason'] == '01' ? 'selected' : '';?>>서비스대상등록</option>
						<option value="02" <?=$basic['rst_reason'] == '02' ? 'selected' : '';?>>타기관이전</option>
						<option value="03" <?=$basic['rst_reason'] == '03' ? 'selected' : '';?>>종결</option>
						<option value="99" <?=$basic['rst_reason'] == '99' ? 'selected' : '';?>>기타</option>
					</select>
				</td>
			</tr>
			<tr id="trEnd" style="display:<?=$basic['rst_reason'] != '03' ? 'none' : '';?>;">
				<th class="center bottom">종결일</th>
				<td class="bottom" colspan="2"><input id="txtEndDt" type="text" value="<?=$myF->dateStyle($basic['end_dt']);?>" class="date"></td>
				<th class="center bottom">종결사유</th>
				<td class="bottom last" colspan="8"><input id="txtEndReason" type="text" value="<?=$basic['end_reason'];?>" style="width:100%;"></td>
			</tr>
			<tr id="trRst" style="display:<?=$basic['rst_reason'] != '99' ? 'none' : '';?>;">
				<th class="center bottom"></th>
				<td class="bottom" colspan="2"></td>
				<th class="center bottom">사유내용</th>
				<td class="bottom last" colspan="8"><input id="txtReasonStr" type="text" value="<?=$basic['reason_str'];?>" style="width:100%;"></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="text-align:right; margin-top:5px;">
	<span class="btn_pack m"><span class="add"></span><a href="#" onclick="lfNew(); return false;">신규</a></span>
	<span class="btn_pack m"><span class="save"></span><a href="#" onclick="lfSave(); return false;">저장</a></span><?
	if ($seq){?>
		<span class="btn_pack m"><span class="delete"></span><a href="#" onclick="lfDelete(); return false;">삭제</a></span><?
	}?>
	<span class="btn_pack m"><span class="list"></span><a href="#" onclick="lfList(); return false;">리스트</a></span>
</div>

<input id="page" type="hidden" value="<?=$page;?>">
<input id="seq" type="hidden" value="<?=$seq;?>">
<?
	Unset($basic);
	Unset($cont);
	Unset($other);

	include_once('../inc/_db_close.php');
?>
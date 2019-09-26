<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_nhcs_db.php');
	
	$medical_org_no = $ed->de($_POST['orgNo']);

	$sql = 'SELECT *
			  FROM medical_org
			 WHERE medical_org_no = \''.$medical_org_no.'\'';
	$row = $conn -> get_array($sql);

?>
<script type="text/javascript">
	
	$(document).ready(function(){
		
		$('input:text').each(function(){
			__init_object(this);
		});

		$('textarea').each(function(){
			__init_object(this);
		});

	});

	function lfResizeSub(){
		var h = $('#divBody').height() - $('#divBtn').height() - $('#divCont').offset().top + $('#divBody').offset().top;
		$('#divCont').height(h);
	}

	function lfSave(){
		if (!$('#txtOrgNo').val()){
			alert('의료기관기호를 입력하여 주십시오.');
			$('#txtOrgNo').focus();
			return;
		}

		if (!$('#txtOrgNm').val()){
			alert('의료기관명을 입력하여 주십시오.');
			$('#txtOrgNm').focus();
			return;
		}
		
		var data = {};

		$('input:hidden').each(function(){
			var id = $(this).attr('id');
			var val = $(this).val();

			data[id] = val;
		});

		$('input:text').each(function(){
			var id = $(this).attr('id');
			var val = $(this).val();

			data[id] = val;
		});

		
		$('select').each(function(){
			var id = $(this).attr('id');
			var val = $(this).val();

			data[id] = val;
		});

		$('textarea').each(function(){
			var id = $(this).attr('id');
			var val = $(this).val();

			data[id] = val;
		});

		$('input:radio').each(function(){
			var id = $(this).attr('name');
			var val = $('input:radio[name="'+id+'"]:checked').val();

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'./medical_reg_save.php'
		,	data:data
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				$('#tempLodingBar').remove();

				if (!result){
					alert('정상적으로 처리되었습니다.');
					opener.lfSearch();
				}else{
					alert(result);
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
	
	function CheckNo(obj){
		
		$.ajax({
			type:'POST'
		,	url:'./medical_check_no.php'
		,	data:{
				'medical_org_no':$(obj).val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result != 'Y'){
					alert('입력하신 기관기호는 이미 사용중인 기관기호입니다.\n다른 기관기호를 입력하여 주십시오.');
					$(obj).val('').focus();
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
	
</script>
<div class="title title_border">의료기관 등록</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="150px">
		<col width="70px">
		<col width="150px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">의료기관기호</th>
			<td class="left"><?
				if($row['medical_org_no'] != ''){ ?>
					<span><?=$row['medical_org_no'];?></span>
					<input id="txtOrgNo" type="hidden" value="<?=$row['medical_org_no'];?>"><?
				}else { ?> 	
					<input id="txtOrgNo" type="text" style="width:100%; ime-mode:disabled;" onchange="CheckNo(this);"><?
				} ?>	
			</td>
			<th class="center">의료기관명</th>
			<td><input id="txtOrgNm" type="text" style="width:100%;" value="<?=$row['medical_org_name'];?>"></td>
			<th class="center">대표자명</th>
			<td class="last"><input id="txtManager" type="text" style="width:36%;" value="<?=$row['ceo_name'];?>"></td>
		</tr>
		<tr>
			<th class="center">전화번호</th>
			<td><input id="txtPhone" type="text" class="phone" value="<?=$myF->phoneStyle($row['telno_loc_org']);?>"></td>
			<th class="center">FAX번호</th>
			<td><input id="txtFAX" type="text" class="phone" value="<?=$myF->phoneStyle($row['faxno_org']);?>"></td>
			<th class="center">사업자번호</th>
			<td class="last"><input id="txtBizNo" type="text" class="phone" alt="biz" value="<?=$myF->bizStyle($row['taxid']);?>"></td>
		</tr>
		<tr>
			<th class="center">대표자(무선)</th>
			<td><input id="txtCeoMobile" type="text" class="phone" value="<?=$myF->phoneStyle($row['telno_mob_ceo']);?>"></td>	
			<th class="center">대표자(유선)</th>
			<td><input id="txtCeoPhone" type="text" class="phone" value="<?=$myF->phoneStyle($row['telno_loc_ceo']);?>"></td>
			<th class="center">이메일</th>
			<td class="last"><input id="txtEmail" type="text" style="width:100%;" value="<?=$row['e_mail'];?>"></td>
		</tr>
		<tr>
			<th class="center">SMS(무선)</th>
			<td><input id="txtSmsMobile" type="text" class="phone" value="<?=$myF->phoneStyle($row['telno_mob_sms']);?>"></td>	
			<th class="center">SMS(유선)</th>
			<td><input id="txtSmsPhone" type="text" class="phone" value="<?=$myF->phoneStyle($row['telno_mob_sms']);?>"></td>
			<th class="center">홈페이지</th>
			<td class="last"><input id="txtHomepage" type="text" style="width:100%;" value="<?=$row['homepage'];?>"></td>
		</tr>
		<tr>
			<th class="center" rowspan="2">주소</th>
			<td class="last" colspan="5">
				<input id="txtAddr" type="text" style="width:60%;" value="<?=$row['addr1'];?>">
			</td>
		</tr>
		<tr>
			<td class="last" colspan="5"><input id="txtAddrDtl" type="text" style="width:60%;" value="<?=$row['addr2'];?>"></td>
		</tr>
	</tbody>
</table>

<div id="divBtn">
	<table class="my_table" style="width:100%; margin-top:10px; margin-bottom:10px;">
		<colgroup>
			<col>
		</colgroup>
		<tbody>
			<tr>
				<td class="center bottom last">
					<span class="btn_pack m"><span class="save"></span><button onclick="lfSave();">저장</button></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
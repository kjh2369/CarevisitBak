<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_nhcs_db.php');
	
	$doctor_licence_no = $ed->de($_POST['doctor_licence_no']);

	$sql = 'SELECT *
			  FROM doctor
			 WHERE doctor_licence_no = \''.$doctor_licence_no.'\'';
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
		if (!$('#txtLicenceNo').val()){
			alert('의사면허번호를 입력하여 주십시오.');
			$('#txtLicenceNo').focus();
			return;
		}

		if (!$('#txtDoctorNm').val()){
			alert('의사명을 입력하여 주십시오.');
			$('#txtDoctorNm').focus();
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
		,	url:'./doctor_reg_save.php'
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
		,	url:'./doctor_check_no.php'
		,	data:{
				'doctor_licence_no':$(obj).val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result != 'Y'){
					alert('입력하신 면허번호는 이미 사용중인 면허번호입니다.\n다른 면허번호를 입력하여 주십시오.');
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
<div class="title title_border">의사 등록</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="130px">
		<col width="90px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">의사면허번호</th>
			<?
				if($row['doctor_licence_no'] != ''){ ?>
					<td class="left">
						<span><?=$row['doctor_licence_no'];?></span>
						<input id="txtLicenceNo" type="hidden" style="width:100%;" value="<?=$row['doctor_licence_no'];?>">
					</td><?
				}else { ?> 
					<td >
						<input id="txtLicenceNo" type="text" style="width:100%; ime-mode:disabled;" onchange="CheckNo(this);">
					</td><?
				} ?>	
			</td>
			<th class="center">의사명</th>
			<td ><input id="txtDoctorNm" type="text" style="width:90px;" value="<?=$row['doctor_name'];?>"></td>	
		</tr>
		<tr>
			<th class="center" rowspan="2">전문과목</th>
			<td rowspan="2">
				<select name="cboSpc" id="cboSpc">
				<?
					

					$sql = ' select medical_off_cd as cd
							 ,		medical_off_name as name
							   from medical_office_cd
							  order by medical_off_name';
					$conn -> query($sql); 
					$conn -> fetch();
					$rowCnt = $conn -> row_count();

					for($i=0; $i<$rowCnt; $i++){
						$R = $conn -> select_row($i); 
						echo '<option value=\''.$R['cd'].'\' '.($row['spc_subject'] == $R['cd'] ? 'selected' : '').'>'.$R['name'].'</option>';
					}

					$conn -> row_free();
				?>
				</select>
			</td>
			<th class="center">유선번호</th>
			<td><input id="txtPhone" type="text" class="phone" value="<?=$myF->phoneStyle($row['telno_loc']);?>"></td>
			
		</tr>
		<tr>
			<th class="center">무선번호</th>
			<td><input id="txtMobile" type="text" class="phone" value="<?=$myF->phoneStyle($row['telno_mob']);?>"></td>
		</tr>
		<tr>
			<th class="center" rowspan="2">주소</th>
			<td colspan="3">
				<input id="txtPostno" type="text" class="no_string" maxlength="5" style="width:50px;">
				<input id="txtAddr" type="text" style="width:100%;" value="<?=$row['addr1'];?>">
			</td>
		</tr>
		<tr>
			<td colspan="3"><input id="txtAddrDtl" type="text" style="width:100%;" value="<?=$row['addr2'];?>"></td>
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
<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	/*
	 *	기관등록
	 */

	$today = Date('Y-m-d');

	$sql = 'SELECT	area_cd, group_cd, group_nm
			FROM	care_group
			ORDER	BY area_cd, show_seq, group_cd
			';
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$care_group[$row['area_cd']][$row['group_cd']] = $row['group_nm'];
	}

	$conn->row_free();

	$care_group = json_encode($care_group, true);
?>
<script type="text/javascript">
	var care_group = JSON.parse('<?=$care_group;?>');
	var winPos = {};

	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		$('textarea').each(function(){
			__init_object(this);
		});

		$('#area_cd').unbind('change').bind('change', function(){
			$('#group_cd option').remove();

			var str = '<option value="">-</option>';

			for(var i in care_group[$(this).val()]){
				str += '<option value="'+i+'">'+care_group[$(this).val()][i]+'</option>';
			}

			$('#group_cd').html(str);
		});

		lfSetToDt();
	});

	function lfResizeSub(){
		return;
		var h = $('#divBody').height() - $('#divBtn').height() - $('#divCont').offset().top + $('#divBody').offset().top;
		$('#divCont').height(h);
	}

	function lfSave(){
		if (!$('#txtOrgNo').val()){
			alert('기관기호를 입력하여 주십시오.');
			$('#txtOrgNo').focus();
			return;
		}

		if (!$('#txtOrgNm').val()){
			alert('기관명을 입력하여 주십시오.');
			$('#txtOrgNm').focus();
			return;
		}

		if (!$('#txtLogId').val()){
			alert('로그인ID를 입력하여 주십시오.');
			$('#txtLogId').focus();
			return;
		}

		if (!$('#txtLogPw').val()){
			alert('비밀번호를 입력하여 주십시오.');
			$('#txtLogPw').focus();
			return;
		}

		if (!$('#area_cd').val()){
			alert('지역을 입력하여 주십시오.');
			$('#area_cd').focus();
			return;
		}

		if (!$('#group_cd').val()){
			alert('지역을 입력하여 주십시오.');
			$('#group_cd').focus();
			return;
		}

		/*
		//사용기간
		if (!$('#txtFromDt').val() || !$('#txtToDt').val()){
			alert('사용기간을 입력하여 주십시오.');

			if (!$('#txtFromDt').val()){
				$('#txtFromDt').focus();
				return;
			}

			if (!$('#txtToDt').val()){
				$('#txtToDt').focus();
				return;
			}
		}

		//회사
		if (!$('#cboCompany').val()){
			alert('회사를 선택하여 주십시오.');
			$('#cboCompany').focus();
			return;
		}

		//지사
		if (!$('#cboBranch').val()){
			alert('지사를 선택하여 주십시오.');
			$('#cboBranch').focus();
			return;
		}

		//담당자
		if (!$('#cboPerson').val()){
			alert('담당자를 선택하여 주십시오.');
			$('#cboPerson').focus();
			return;
		}
		*/

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

		data['svcSet'] = '';
		$('input:checkbox[id^="chkSvcSet_"]').each(function(){
			var id = $(this).attr('id').replace('chkSvcSet_','');

			data['svcSet'] += (data['svcSet'] ? '?' : '');

			if (id == '2_11'){
				data['svcSet'] += 'id='+id+'&yn=Y';
			}else{
				data['svcSet'] += 'id='+id+'&yn='+($(this).attr('checked') ? 'Y' : 'N');
			}
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

		data['txtLogPw'] = $('#txtLogPw').val();
		data['companyCd']= $('#cboCompany option:selected').attr('cd');

		$.ajax({
			type:'POST'
		,	url:'./center_CENTER_REG_save.php'
		,	data:data
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				$('#tempLodingBar').remove();

				if (!result){
					alert('정상적으로 처리되었습니다.');

					$(':text').val('');
					$('select').val('');
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

	function Selection(){
		var orgNo = $('#txtOrgNo').val();

		if (!orgNo) return;

		var width = 800;
		var height = 600;
		//var left = window.screenLeft + ($(window).width() - width) / 2;
		//var top = window.screenTop + ($(window).height() - height) / 2;
		var left = window.screenLeft + $('#left_box').width();
		var top = window.screenTop + $('#divTitle').offset().top;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var url = './center_connect_info.php';
		var win = window.open('about:blank', 'CONNECT_INFO', option);
			win.opener = self;
			win.focus();

		winPos['X'] = left;
		winPos['Y'] = top;

		var parm = new Array();
			parm = {
				'orgNo':orgNo
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', 'CONNECT_INFO');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfSetToDt(){
		//var today= getToday();
		var today= '<?=$today;?>';
		var fromDt = $('#txtFromDt').val();
		var toDt = __addDate('d', 13, fromDt);

		if (toDt < today){
			alert('시작일자를 오늘보다 과거 일자로 등록할 수 없스비다. 확인 후 다시 입력하여 주십시오.');
			fromDt = __addDate('d', -13, $('#txtToDt').val());
			$('#txtFromDt').val(fromDt);
			return;
		}

		$('#txtToDt').val(toDt);
	}
</script>
<!--div id="divCont" style="overflow-x:hidden; overflow-y:auto; height:100px; border-bottom:1px solid #CCCCCC;"-->
<div>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="150px">
			<col width="70px">
			<col width="150px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">기관기호</th>
				<td><input id="txtOrgNo" type="text" style="width:100%; ime-mode:disabled;" onchange="CheckCode('orgNo',this,$('#txtLogId'));"></td>
				<th class="center">기관명</th>
				<td><input id="txtOrgNm" type="text" style="width:100%;"></td>
				<th class="center">홈페이지</th>
				<td class="last"><input id="txtHomepage" type="text" style="width:100%;"></td>
			</tr>
			<tr>
				<th class="center">로그인ID</th>
				<td><input id="txtLogId" type="text" style="width:100%;" onchange="CheckCode('logId',this);"></td>
				<th class="center">전화번호</th>
				<td><input id="txtPhone" type="text" class="phone"></td>
				<th class="center">이메일</th>
				<td class="last"><input id="txtEmail" type="text" style="width:100%;"></td>
			</tr>
			<tr>
				<th class="center">비밀번호</th>
				<td><input id="txtLogPw" type="password" style="width:100%;"></td>
				<th class="center">FAX번호</th>
				<td><input id="txtFAX" type="text" class="phone"></td>
				<th class="center">사업자번호</th>
				<td class="last"><input id="txtBizNo" type="text" class="phone" alt="biz"></td>
			</tr>
			<tr>
				<th class="center">대표자명</th>
				<td><input id="txtManager" type="text" style="width:100%;"></td>
				<th class="center">휴대폰</th>
				<td><input id="txtMobile" type="text" class="phone"></td>
				<th class="center">법인번호</th>
				<td class="last"><input id="txtRegNo" type="text" style="width:100%;"></td>
			</tr>
			<tr>
				<th class="center">우편번호</th>
				<td colspan="3">
					<input id="txtPostno" type="text" class="no_string" maxlength="5" style="width:50px;">
				</td>
				<th class="center">지역</th>
				<td class="last">
					<select id="area_cd" style="width:auto;">
						<option value="">-</option><?
						$sql = 'SELECT	area_cd, area_nm
								FROM	care_area
								ORDER	BY show_seq, area_cd
								';
						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['area_cd'];?>"><?=$row['area_nm'];?></option><?
						}

						$conn->row_free();?>
					</select>
					<select id="group_cd" style="width:auto;">
						<option value="">-</option>
					</select>
				</td>
			</tr>
			<tr>
				<th class="center" rowspan="2">주소</th>
				<td colspan="3"><input id="txtAddr" type="text" style="width:100%;"></td>
				<th class="center" rowspan="2">&nbsp;</th>
				<td class="last" rowspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3"><input id="txtAddrDtl" type="text" style="width:100%;"></td>
			</tr>
		</tbody>
	</table>
</div>
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
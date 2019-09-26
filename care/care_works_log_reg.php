<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.worklog = false; //업무일지 작성여부

		$('input:hidden[name="svcCd"]').val(opener.svcCd);
		$('input:hidden[name="date"]').val(opener.date);
		$('input:hidden[name="time"]').val(opener.time);
		$('input:hidden[name="seq"]').val(opener.seq);
		$('input:hidden[name="jumin"]').val(opener.jumin);
		$('input:hidden[name="suga"]').val(opener.suga);
		$('input:hidden[name="resource"]').val(opener.resource);
		$('input:hidden[name="mem"]').val(opener.mem);

		$('textarea').each(function(){
			__init_object(this);
		});

		__fileUploadInit($('#frmFile'), 'fileUploadCallback');
		
		lfLoad();
		lfHCE();
		lfSearch();
		
	});

	function lfLoad(){
		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_load.php'
		,	data:{
				'svcCd'	:opener.svcCd
			,	'date'	:opener.date
			,	'jumin'	:opener.jumin
			,	'suga'	:opener.suga
			,	'res'	:opener.resource
			,	'mem'	:opener.mem
			}
		,	success: function(data){
				var obj = __parseVal(data);
				
				
				$('#ID_NAME').text(obj['name']);
				$('#ID_GENDER').text(obj['gender']);
				$('#ID_BIRTHDAY').text(obj['birthday']);
				$('#ID_DATE').text(__getDate(opener.date,'.')+' '+__styleTime(opener.time));
				$('#ID_SERVICE').text(obj['service']);
				$('#ID_RESOURCE').text(obj['resource']);
				$('#ID_MEM').text(obj['mem']);
				$('#ID_TEL').text(obj['tel']);
			}
		});
	}

	function lfHCE(){
		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_hce.php'
		,	data:{
				'svcCd'	:opener.svcCd
			,	'jumin'	:opener.jumin
			}
		,	success: function(html){
				$('#ID_HCE').html(html);
				$('#cboHCESeq').change();
			}
		});
	}

	function lfHCEProcList(IPIN, seq){
		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_hce_proc.php'
		,	data:{
				'svcCd'	:opener.svcCd
			,	'IPIN'	:IPIN
			,	'seq'	:seq
			,	'date'	:opener.date
			}
		,	success: function(html){
				$('#ID_PROC_COUNSEL').html(html);
			}
		});
	}

	function lfHCEProcReg(IPIN, seq, no, gbn){
		if (!opener.worklog){
			alert('저장 후 과정상담이력을 등록하여 주십시오.');
			return;
		}

		if (!no) no = 0;
		if (no < 1){
			if (!$('#ID_PROC_MEM').attr('jumin')){
				alert('상담자를 선택을 선행하여 주십시오.');
				return;
			}
			if (!confirm('과정상담이력을 추가등록하시겠습니까?')) return;
		}else{
			if (!confirm('과정상담이력 변경은 업무내용만 변경되고 상담자, 상담방법은 변경되지 않습니다.\n\n변경하시겠습니까?')) return;
		}

		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_hce_proc_set.php'
		,	data:{
				'svcCd'	:opener.svcCd
			,	'IPIN'	:IPIN
			,	'seq'	:seq
			,	'no'	:no
			,	'date'	:opener.date
			,	'gbn'	:gbn
			,	'memCd'	:$('#ID_PROC_MEM').attr('jumin')
			,	'memNm'	:$('#ID_PROC_MEM').text()
			,	'text'	:$('textarea[name="txtContents"]').val()
			,	'mode'	:'SAVE'
			}
		,	success: function(result){
				if (result == 1){
					if (no < 1){
						lfHCEProcList(IPIN, seq);
					}else{
						alert('정상적으로 처리되었습니다.');
					}
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}

	function lfProcRemove(IPIN, seq, no){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_hce_proc_set.php'
		,	data:{
				'svcCd'	:opener.svcCd
			,	'IPIN'	:IPIN
			,	'seq'	:seq
			,	'no'	:no
			,	'mode'	:'REMOVE'
			}
		,	success: function(result){
				if (result == 1){
					lfHCEProcList(IPIN, seq);
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}

	function lfMemFind(rtn){
		var jumin = $('#txtClient').attr('jumin');
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open(url, 'FIND_CLIENT', option);
			win.opener = self;
			win.focus();

		if (!rtn) rtn = 'lfMemFindResult';

		var parm = new Array();
			parm = {
				'type':'member'
			,	'jumin':jumin
			,	'kind':opener.svcCd
			,	'year':opener.date.substr(0,4)
			,	'month':opener.date.substr(4,2)
			,	'return':rtn
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type','hidden');
			objs.setAttribute('name',key);
			objs.setAttribute('value',parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target','FIND_CLIENT');
		form.setAttribute('method','post');
		form.setAttribute('action',url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfMemFindResult(obj){
		var obj = __parseStr(obj);
		$('#ID_PROC_MEM').attr('jumin',obj['jumin']).text(obj['name']);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_search.php'
		,	data:{
				'svcCd':opener.svcCd
			,	'date':opener.date
			,	'time':opener.time
			,	'seq':opener.seq
			,	'jumin':opener.jumin
			,	'suga':opener.suga
			,	'resource':opener.resource
			,	'mem':opener.mem
			}
		,	success: function(data){
				//if ('<?=$debug;?>' == '1'){
				//	alert(data);
				//}
				if (!data) return;
				var val = __parseVal(data);
				
				$('textarea[name="txtContents"]').val(__replace(val['contents'], '@', '&'));
				

				$('#downLoad').show();

				if (val['picNm']){
					$('#ID_BODY_PIC').html('<img src="'+val['path']+'" style="width:'+val['width']+'px; height:'+val['height']+';" border="0">');
					var img = $('img:first',$('#ID_BODY_PIC'));
					$(img).attr('orgW',$(img).width()).attr('orgH',$(img).height());
					lfPicSet();				
				}

				$('input:hidden[name="origin"]').val(val['origin']);
				$('#ID_BTN_REMOVE').show();

				opener.worklog = true;

				lfSetBtn();
			}
		});
	}
	
	function lfFileDown(){
		var frm = $('#frmFile');
			frm.attr('action', './care_works_log_img_download.php');
			frm.submit();
	}

	function lfSave(){
		var frm = $('#frmFile');
			frm.attr('action', './care_works_log_reg_save.php');
			frm.submit();
	}

	function lfPicRemove(){
		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_pic_remove.php'
		,	data:{
				'svcCd':opener.svcCd
			,	'date':opener.date
			,	'jumin':opener.jumin
			,	'suga':opener.suga
			,	'resource':opener.resource
			,	'mem':opener.mem
			}
		,	success: function(result){
				if (result == 1){
					$('#ID_BODY_PIC').html('');
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}

	function fileUploadCallback(data, state){
		if (data == 'ATTACH_ERROR' || data == 'DATA_ERROR'){
			alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		}else if(data == 'IMG'){
		}else{
			if (data){
				data = data.split('amp;').join('');
				var val = __parseVal(data);
				$('#ID_BODY_PIC').html('<img src="'+val['src']+'" style="width:'+val['width']+'px; height:'+val['height']+';" border="0">');
				var img = $('img:first',$('#ID_BODY_PIC'));
				$(img).attr('orgW',$(img).width()).attr('orgH',$(img).height());
				lfPicSet();
			}

			opener.contents = $('textarea[name="txtContents"]').val();
			opener.pic = $('img:first',$('#ID_BODY_PIC')).length > 0 ? 'Y' : 'N';
			opener.result = true;
			opener.worklog = true;
			alert('정상적으로 처리되었습니다.');
			opener.worklog = true;
			lfSetBtn();
		}
	}

	function lfPicSet(){
		var img = $('img:first',$('#ID_BODY_PIC'));

		if (!$(img).attr('src')) return;

		if ($(img).width() != $(img).attr('orgW')){
			$(img).width($(img).attr('orgW')).height($(img).attr('orgH'));
		}else{
			var maxW = $('#ID_BODY_PIC').width();
			var maxH = $('#ID_BODY_PIC').height();
			var picW = $(img).width();
			var picH = $(img).height();

			if (picW > maxW){
				picH = picH * (maxW / picW);
				picW = maxW;
			}

			if (picH > maxH){
				picW = picW * (maxH / picH);
				picH = maxH;
			}

			$(img).width(picW).height(picH);
		}
	}

	function lfRemove(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./care_works_log_reg_remove.php'
		,	data:{
				'svcCd'	:$('input:hidden[name="svcCd"]').val()
			,	'date'	:$('input:hidden[name="date"]').val()
			,	'time'	:$('input:hidden[name="time"]').val()
			,	'seq'	:$('input:hidden[name="seq"]').val()
			,	'jumin'	:$('input:hidden[name="jumin"]').val()
			,	'suga'	:$('input:hidden[name="suga"]').val()
			,	'res'	:$('input:hidden[name="resource"]').val()
			,	'mem'	:$('input:hidden[name="mem"]').val()
			,	'origin':$('input:hidden[name="origin"]').val()
			}
		,	success: function(result){
				if (result == 1){
					opener.result = true;
					opener.worklog = false;
					self.close();
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}

	function lfSetBtn(){
		$('#ID_HCE').show();
	}
</script>
<div class="title title_border">업무일지 등록 및 수정</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="50px">
		<col width="50px">
		<col width="70px">
		<col width="70px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">대상자명</th>
			<td class="left" id="ID_NAME"></td>
			<th class="center">성별</th>
			<td class="left" id="ID_GENDER"></td>
			<th class="center">생년월일</th>
			<td class="left" id="ID_BIRTHDAY"></td>
			<th class="center">연락처(대상자/보호자)</th>
			<td class="left" id="ID_TEL"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="50px">
		<col width="150px">
		<col width="50px">
		<col width="150px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">일시</th>
			<td class="left" id="ID_DATE"></td>
			<th class="center">서비스</th>
			<td class="center"><div id="ID_SERVICE" class="left nowrap" style="width:150px;"></div></td>
			<th class="center">자원</th>
			<td class="center"><div id="ID_RESOURCE" class="left nowrap" style="width:150px;"></div></td>
			<th class="center">담당자</th>
			<td class="left" id="ID_MEM"></td>
		</tr>
	</tbody>
</table>

<form id="frmFile" name="frmFile" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="250px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">업무내용</th>
			<th class="head" colspan="2">사진</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top" rowspan="3">
				<div style="border-bottom:1px solid #CCCCCC;"><textarea name="txtContents" style="width:100%; height:200px;"></textarea></div>
				<div id="ID_HCE" style="display:none;"></div>
			</td>
			<th class="center">사진찾기</th>
			<td>
				<input name="pic" type="file" style="width:200px;">
				<span class="btn_pack m"><button onclick="lfPicRemove();">사진파일 삭제</button></span>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="ID_BODY_PIC" onclick="lfPicSet();" style="width:100%; height:373px; overflow-x:auto; overflow-y:auto;"></div>
			</td>
		</tr>
		<tr>
			<td class="center" style="padding:5px;" colspan="2">
				<div style="float:right; width:auto;">
					<span id="ID_BTN_REMOVE" class="btn_pack m" style="display:none;"><button onclick="lfRemove();" style="color:RED;">삭제</button></span>
				</div>
				<div style="float:center; width:auto;">
					<span id="downLoad" class="btn_pack m" style="display:none;"><button onclick="lfFileDown();">사진다운로드</button></span>
					<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
					<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<input name="svcCd" type="hidden" value="">
<input name="date" type="hidden" value="">
<input name="time" type="hidden" value="">
<input name="seq" type="hidden" value="">
<input name="jumin" type="hidden" value="">
<input name="suga" type="hidden" value="">
<input name="resource" type="hidden" value="">
<input name="mem" type="hidden" value="">
<input name="origin" type="hidden" value="NEW">

</form>
<?
	include_once('../inc/_footer.php');
?>
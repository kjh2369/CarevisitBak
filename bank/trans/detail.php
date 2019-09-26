<?
	include_once('../inc/_header.php');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$colgroup = '<col width="40px"><col width="120px"><col width="60px"><col width="170px"><col width="60px"><col width="70px"><col width="55px"><col>';
?>
<base target="_self">
<table style="width:100%;" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="65px">
		<col width="200px">
		<col width="50px">
		<col width="70px">
		<col width="50px">
		<col width="90px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr style="height:30px; background-color:#efefef;">
			<th style="" colspan="8">이체요청 리스트</th>
		</tr>
	</thead>
	<tbody>
		<tr style="height:25px;">
			<th style="border-bottom:1px solid #cccccc; border-top:1px solid #cccccc; background-color:#efefef;">기관명</th>
			<td style="border-bottom:1px solid #cccccc; border-top:1px solid #cccccc; border-left:1px solid #cccccc; padding:0 5px 0 5px;"><span id="lblCenter">&nbsp;</span></td>
			<th style="border-bottom:1px solid #cccccc; border-top:1px solid #cccccc; border-left:1px solid #cccccc; background-color:#efefef;">대표자</th>
			<td style="border-bottom:1px solid #cccccc; border-top:1px solid #cccccc; border-left:1px solid #cccccc; padding:0 5px 0 5px;"><span id="lblManager">&nbsp;</span></td>
			<th style="border-bottom:1px solid #cccccc; border-top:1px solid #cccccc; border-left:1px solid #cccccc; background-color:#efefef;">연락처</th>
			<td style="border-bottom:1px solid #cccccc; border-top:1px solid #cccccc; border-left:1px solid #cccccc; padding:0 5px 0 5px;"><span id="lblPhone">&nbsp;</span></td>
			<th style="border-bottom:1px solid #cccccc; border-top:1px solid #cccccc; border-left:1px solid #cccccc; background-color:#efefef;">주소</th>
			<td style="border-bottom:1px solid #cccccc; border-top:1px solid #cccccc; border-left:1px solid #cccccc; padding:0 5px 0 5px;"><span id="lblAddr">&nbsp;</span></td>
		</tr>
		<tr style="height:25px;">
			<th style="border-bottom:1px solid #cccccc; background-color:#efefef;">계좌번호</th>
			<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; padding:0 5px 0 5px;"><span id="lblBankNo">&nbsp;</span></td>
			<th style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; background-color:#efefef;">예금주</th>
			<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; padding:0 5px 0 5px;" colspan="3"><span id="lblBankAcct">&nbsp;</span></td>
			<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; padding:0 5px 0 5px;" colspan="2">[<span style="font-weight:bold; color:blue;" onclick="lfExcel();">엑셀출력</span>]</td>
		</tr>
	</tbody>
</table>

<table style="width:100%; margin-top:1px;" cellpadding="0" cellspacing="0">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr style="height:25px;">
			<th style="background-color:#efefef; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;">No</th>
			<th style="background-color:#efefef; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-left:1px solid #cccccc;">직원명</th>
			<th style="background-color:#efefef; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-left:1px solid #cccccc;">은행명</th>
			<th style="background-color:#efefef; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-left:1px solid #cccccc;">계좌번호</th>
			<th style="background-color:#efefef; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-left:1px solid #cccccc;">예금주</th>
			<th style="background-color:#efefef; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-left:1px solid #cccccc;">금액</th>
			<th style="background-color:#efefef; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-left:1px solid #cccccc;">결과</th>
			<th style="background-color:#efefef; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-left:1px solid #cccccc;">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="vertical-align:top; border-bottom:1px solid #cccccc;" colspan="9">
				<div id="body" style="cursor:default; overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;" onclick=""></div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr style="height:35px;">
			<td style="text-align:center;" colspan="9">
				<button id="" onclick="lfSave();" style="padding-top:3px;">저장</button>
				<button id="" onclick="lfClose();" style="padding-top:3px;">닫기</button>
			</td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_footer.php');
?>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<iframe id="frmTemp" name="frmTemp" src="" width="0" height="0" frameborder="0"></iframe>
<script type="text/javascript">
	var opener = null;
	var objW = 0;

	$(document).ready(function(){
		opener = window.dialogArguments;

		lfResize();

		setTimeout('lfCenterInfo()',1);
		setTimeout('lfRequestList()',1);
	});

	$(window).unload(function(){
		opener.parent.modal = null;
		opener.parent.lfSearchCenter();
	});

	function lfResize(){
		var h = $(this).height();
		var t = $('#body').offset().top;

		h = h - t - 35;

		$('#body').height(h);

		var w = $(this).width();

		objW = w - (40 + 120 + 60+ 170 + 60 + 70 + 55) - 30;
	}

	function lfCenterInfo(){
		$.ajax({
			type: 'POST'
		,	url : '../trans/center_info.php'
		,	data: {
				'code':opener.orgNo
			}
		,	beforeSend: function (){
			}
		,	success: function (data){
				var val = data.split(String.fromCharCode(2));

				$('#lblCenter').text(val[0]);
				$('#lblManager').text(val[1]);
				$('#lblPhone').text(val[2]);
				$('#lblAddr').text(val[3]);
				$('#lblBankNo').text(val[4] ? val[4] : ' ');
				$('#lblBankAcct').text(val[5] ? val[5] : ' ');
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfRequestList(){
		$.ajax({
			type: 'POST'
		,	url : '../trans/request_list.php'
		,	data: {
				'code':opener.orgNo
			}
		,	beforeSend: function (){
			}
		,	success: function (data){
				var list = data.split(String.fromCharCode(1));
				var html = '<table style="width:100%;" cellpadding="0" cellspacing="0">'
						 + '<colgroup><?=$colgroup;?></colgroup>'
						 + '<tbody>';

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));
						var bgcolor = 'ffffff';

						if (i % 2 != 0){
							bgcolor = 'ffffff';
						}

						html += '<tr style="height:25px;">'
							 +  '<td style="text-align:center; border-bottom:1px solid #cccccc;">'+(i+1)+'</td>'
							 +  '<td style="text-align:center; border-left:1px solid #cccccc; border-bottom:1px solid #cccccc;">'+val[9].substring(0,10).split('-').join('.')+' '+val[9].substring(11,19)+'</td>'
							 +  '<td style="text-align:center; border-left:1px solid #cccccc; border-bottom:1px solid #cccccc;"><div style="width:auto; padding-left:3px; text-align:left;">'+val[5]+'</div></td>'
							 +  '<td style="text-align:center; border-left:1px solid #cccccc; border-bottom:1px solid #cccccc;"><div style="width:auto; padding-left:3px; text-align:left;">'+val[6]+'</div></td>'
							 +  '<td style="text-align:center; border-left:1px solid #cccccc; border-bottom:1px solid #cccccc;"><div style="width:auto; padding-left:3px; text-align:left;">'+val[7]+'</div></td>'
							 +  '<td style="text-align:center; border-left:1px solid #cccccc; border-bottom:1px solid #cccccc;"><div style="width:auto; padding-right:3px; text-align:right;">'+__num2str(val[8])+'</div></td>'
							 +  '<td style="text-align:center; border-left:1px solid #cccccc; border-bottom:1px solid #cccccc;"><input id="chkN_'+i+'" name="chkYN_'+i+'" type="checkbox" value="N" onclick="lfChk(this);"><label or="chkN_'+i+'">실패</label></td>'
							 +  '<td style="text-align:center; border-left:1px solid #cccccc; border-bottom:1px solid #cccccc; background-color:#f9f9f9;">'
							 +  '<input id="txtOther_'+i+'" name="txt" type="text" value="" style="ime-mode:active; width:'+objW+'px; border:none; background-color:#f9f9f9;" disabled="true" write="N">'
							 +  '<input id="key_'+i+'" type="hidden" yymm="'+val[0]+'" jumin="'+val[1]+'" seq="'+val[2]+'" result="">'
							 +  '</td>'
							 +  '</tr>';
					}
				}

				html += '</tbody>'
					 +  '</table>';

				$('#body').html(html);
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfChk(obj){
		var idx = $(obj).attr('id').split('_')[1];
		var yn  = $(obj).val();

		if (!$(obj).attr('checked')){
			yn = 'Y';
		}else{
			yn = 'N';
		}

		if (yn == 'Y'){
			$('#txtOther_'+idx).css('background-color','#f9f9f9').attr('disabled',true).attr('write','N').parent().css('background-color','#f9f9f9');
		}else{
			$('#txtOther_'+idx).css('background-color','#ffffff').attr('disabled',false).attr('write','Y').parent().css('background-color','#ffffff');
		}
	}

	function lfSave(){
		var lbExit = false;

		$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

		$('input:hidden[id^="key_"]').each(function(){
			var idx = $(this).attr('id').split('key_').join('');

			if ($('#chkN_'+idx).attr('checked')){
				if (!$('#txtOther_'+idx).val()){
					alert('실패 사유를 입력하여 주십시오.');
					$('#txtOther_'+idx).focus();
					lbExit = true;
					return false;
				}
			}
		});

		if (lbExit){
			$('#tempLodingBar').remove();
			return;
		}

		$('input:hidden[id^="key_"]').each(function(){
			var idx  = $(this).attr('id').split('key_').join('');
			var stat = '';

			if (!$('#chkN_'+idx).attr('checked')){
				stat = '3';
			}else{
				stat = '5';
			}

			$.ajax({
				type: 'POST'
			,	async:false
			,	url : '../trans/result_ok.php'
			,	data: {
					'code':opener.orgNo
				,	'yymm':$(this).attr('yymm')
				,	'jumin':$(this).attr('jumin')
				,	'seq':$(this).attr('seq')
				,	'stat':stat
				,	'other':$('#txtOther_'+idx).val()
				}
			,	beforeSend: function (){
				}
			,	success: function (result){
					$(this).attr('result',result);
				}
			,	error: function (){
				}
			}).responseXML;
		});

		$('#tempLodingBar').remove();
		setTimeout('lfRequestList()',1);
	}

	function lfExcel(){
		var url = '../trans/excel.php';

		var parm = new Array();
			parm = {
				'code':opener.orgNo
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

		form.setAttribute('target', 'frmTemp');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfClose(){
		self.close();
	}
</script>
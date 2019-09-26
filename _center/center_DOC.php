<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		__fileUploadInit($('#docF'), 'fileUploadCallback');

		$('input:text').each(function(){
			__init_object(this);
		});

		//lfSearch();
	});
	
	function lfResizeSub(){
		return;
		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		var h = document.body.offsetHeight - $(obj).offset().top - $('#copyright').height();
		$(obj).height(h);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'orgNo'	:$('#txtOrgNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'mgNm'	:$('#txtMgNm').val()
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfReg(){
		var html = '';

		html = '<div style="text-align:left; margin-bottom:2px;">'
			 + '	<div style="float:left; width:70px; text-align:center;">기관명</div>'
			 + '	<div style="float:left; width:auto;">'
			 + '		<span class="btn_pack find" style="height:25px;" onclick="lfFindCenter();"></span>'
			 + '		<span id="ID_CELL_ORG" style="width:70%; height:24px; border:1px solid #CCCCCC;" code=""></span>'
			 + '	</div>'
			 + '</div>'
			 + '<div>'
			 + '	<div style="float:left; width:70px;">계약서일자</div>'
			 + '	<div style="float:left; width:70%; text-align:left; margin-left:24px; border:1px solid #CCCCCC;">'
			 + '		<div style="padding-left:5px;">계약서일자 : <input id="txtContDt" name="txtContDt" type="text" value="" class="date"></div>'
			 + '		<div id="ID_CELL_CONTDT"></div>'
			 + '	</div>'
			 + '</div>'
			 + '<div style="margin-top:1px;">'
			 + '	<div style="float:left; width:70px;">계약서</div>'
			 + '	<div style="float:left; width:auto; ">'
			 + '		<input type="file" name="docFile1" id="docFile1" style="width:90%;">'
			 + '		<input type="hidden" id="docType">'
			 + '		<input type="hidden" id="docContDt">'
			 + '	</div>'
			 + '</div>'
			 + '<div>'
			 + '	<div style="float:left; width:70px;">등록증</div>'
			 + '	<div style="float:left; width:auto;">'
			 + '		<input type="file" name="docFile2" id="docFile2" style="width:90%;">'
			 + '		<input type="hidden" id="docType">'
			 + '		<input type="hidden" id="docContDt">'
			 + '	</div>'
			 + '</div>'
			 + '<div>'
			 + '	<div style="float:left; width:70px;">CMS 동의서</div>'
			 + '	<div style="float:left; width:auto;">'
			 + '		<input type="file" name="docFile3" id="docFile3" style="width:90%;">'
			 + '		<input type="hidden" id="docType">'
			 + '		<input type="hidden" id="docContDt">'
			 + '	</div>'
			 + '</div>'
			 + '<div style="font-size:13px; text-align:center;"><img src="../popup/kacold_popup/btn_register.png" onclick="fileUpload();"></div>';

		$('#ID_POP_BODY_DOC').html(html);
		$('input:text[id="txtContDt"]').each(function(){
			__init_object(this);
		});
		$('#ID_POP_BODY').show();
	}

	function fileUpload(){
		if (!$('#ID_CELL_ORG').attr('code')){
			alert('기관을 선택하여 주십시오.');
			return;
		}

		var frm = $('#docF');
			frm.attr('action', './center_<?=$menuId?>_upload.php?orgNo='+$('#ID_CELL_ORG').attr('code'));
			frm.submit();
	}

	function fileUploadCallback(data, state){
		if (!data){
			$('#ID_POP_BODY').hide();
			alert('정상적으로 처리되었습니다.');
			lfSearch();
		}else{
			alert(data);
		}
	}

	function lfDelete(orgNo, contDt, gbn){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_delete.php'
		,	data:{
				'orgNo':orgNo
			,	'contDt':contDt
			,	'gbn':gbn
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				$('#tempLodingBar').remove();
				lfSearch();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfFindCenter(){
		var objModal = new Object();
		var url      = '../find/_find_center.php';
		var style    = 'dialogWidth:800px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.mode = '97';
		objModal.code = '';
		objModal.name = '';

		window.showModalDialog(url, objModal, style);

		if (!objModal.code) return;

		$('#ID_CELL_ORG').attr('code',objModal.code).text(objModal.name);

		$.ajax({
			type:'POST'
		,	url:'./center_contdtlist.php'
		,	data:{
				'orgNo':$('#ID_CELL_ORG').attr('code')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_CELL_CONTDT').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
	
	var winPos = {};

	function lfSelOrg(orgNo){
		var width = 900;
		var height = 700;
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
	
	function GetScreenInfo(){
		return winPos;
	}

</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col width="150px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td>
				<input id="txtOrgNo" type="text" value="" style="width:100%;">
			</td>
			<th class="center">기관명</th>
			<td>
				<input id="txtOrgNm" type="text" value="" style="width:100%;">
			</td>
			<th class="center">대표자명</th>
			<td>
				<input id="txtMgNm" type="text" value="" style="width:100%;">
			</td>
			<td class="left last" rowspan="2">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">등록일자</th>
			<td colspan="5">
				<input id="txtFromDt" type="text" value="" class="date"> ~ <input id="txtToDt" type="text" value="" class="date">
			</td>
		</tr>
	</tbody>
</table><?
$colgroup = '
	<col width="40px">
	<col width="90px">
	<col width="150px">
	<col width="80px">
	<col width="70px">
	<col width="50px" span="3">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">등록일</th>
			<th class="head">계약서</th>
			<th class="head">등록증</th>
			<th class="head">동의서</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>

<div id="ID_BODY_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:auto;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="ID_LIST"></tbody>
		<tfoot>
			<tr>
				<td class="bottom last"></td>
			</tr>
		</tfoot>
	</table>
</div>

<div id="ID_POP_BODY" style="position:absolute; left:0; top:0; width:100%; height:100%; padding:50px 100px 0 100px; display:none; z-index:11; background:url('../image/tmp_bg.png');">
	<form id="docF" name="docF" method="post" enctype="multipart/form-data">
		<div style="position:absolute; z-index:100; left:100px; top:50px; width:100%; height:130px; border:2px solid #363dcb; background-color:WHITE; display:;">
			<div style="width:100%; text-align:right;">
				<div style="float:right; width:auto; margin-top:5px; margin-right:5px; cursor:pointer;"><img src="../popup/kacold_popup/btn_close.png" onclick="$('#ID_POP_BODY').hide();"></div>
			</div>
			<table style="width:100%; border:none;">
				<tr>
					<td id="ID_POP_BODY_DOC" style="background-color:WHITE; padding:30px; border:none;"></td>
				</tr>
			</table>
		</div>
	</form>
</div>
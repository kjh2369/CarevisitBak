<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	var gWin = new Array();

	window.onunload = function(){
		for(var i in gWin) gWin[i].close();
	}

	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		__fileUploadInit($('#f'), 'fileUploadCallback');
	});

	function lfResizeSub(){
		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		var h = document.body.offsetHeight - $(obj).offset().top - $('#copyright').height();
		$(obj).height(h);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_ACCT_CMS_search.php'
		,	data:{
				'CMSNo'	:$('#txtCMSNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			,	'link'	:$('#cboLinkStat').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				//$('#ID_LIST').html('<tr><td colspan="10">'+html+'</td></tr>');
				$('#ID_LIST').html(html);

				var amt = 0;

				$('tr',$('#ID_LIST')).each(function(){
					amt += __str2num($('td',this).eq(5).text());
				});

				$('#ID_CELL_CNT').text(__num2str($('tr',$('#ID_LIST')).length));
				$('#ID_CELL_AMT').text(__num2str(amt));

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

	function lfInReg(){
		var left = (screen.availWidth - (width = 800)) / 2, top = (screen.availHeight - (height = 400)) / 2;
		var i = gWin.length;

		gWin[i] = window.open('./center_cms_reg.php', 'CMS_REG', 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no');
		gWin[i].focus();
	}

	function lfUpload(){
		if (!$('#cmsfile').val()){
			alert('엑셀파일을 선택하여 주십시오.');
			$('#cmsfile').focus();
			return;
		}

		var exp = $('#cmsfile').val().split('.');

		if (exp[exp.length-1].toLowerCase() != 'xls' && exp[exp.length-1].toLowerCase() != 'xlsx'){
			alert('EXCEL 파일을 선택하여 주십시오.');
			return;
		}

		var frm = $('#f');
			frm.attr('action', './center_ACCT_CMS_upload.php');
			frm.submit();

		$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
	}

	function fileUploadCallback(data, state){
		if (!data){
			alert('정상적으로 처리되었습니다.');
		}else{
			alert(data);
		}
		//$('#ID_TEST').html(state+'<br>'+data).show();
		$('#tempLodingBar').remove();
	}

	function lfOrgNoLink(CMSNo, CMSDt, CMSSeq){
		$.ajax({
			type:'POST'
		,	url:'./center_ACCT_CMS_org_link.php'
		,	data:{
				'CMSNo'	:CMSNo
			,	'CMSDt'	:CMSDt
			,	'CMSSeq':CMSSeq
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
				}else{
					alert(result);
				}

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
</script>

<form id="f" name="f" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">CMS 파일</th>
			<td class="left last">
				<div style="float:left; width:auto;">
					<input type="file" name="cmsfile" id="cmsfile" style="width:250px; margin:0;">
					<span class="btn_pack m"><button type="button" onclick="lfUpload();">업로드</button></span>
					<span class="btn_pack m"><button type="button" onclick="lfInReg();">입력저장</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
</form>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="70px">
		<col width="50px">
		<col width="70px">
		<col width="90px">
		<col width="180px">
		<col width="60px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">CMS 번호</th>
			<td>
				<input id="txtCMSNo" type="text" value="" class="no_string">
			</td>
			<th class="center">기관명</th>
			<td>
				<input id="txtOrgNm" type="text" value="">
			</td>
			<th class="center">CMS 등록일자</th>
			<td class="">
				<input id="txtFromDt" type="text" value="" class="date"> ~
				<input id="txtToDt" type="text" value="" class="date">
			</td>
			<th class="center">연결상태</th>
			<td>
				<select id="cboLinkStat" style="width:auto;">
					<option value="">전체</option>
					<option value="1">연결</option>
					<option value="5">일부 및 미연결</option>
					<option value="3">일부연결</option>
					<option value="9">미연결</option>
				</select>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div style="padding-left:5px; border-bottom:1px solid #CCCCCC;">
	건수 : <span id="ID_CELL_CNT">0</span> /
	금액 : <span id="ID_CELL_AMT">0</span>
</div><?
$colgroup = '
	<col width="40px">
	<col width="100px">
	<col width="70px">
	<col width="150px">
	<col width="80px">
	<col width="70px">
	<col width="100px">
	<col width="100px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관기호</th>
			<th class="head">CMS 번호</th>
			<th class="head">기관명</th>
			<th class="head">등록일자</th>
			<th class="head">금액</th>
			<th class="head">상태</th>
			<th class="head">연결</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>
<div id="ID_TEST" style="display:none;"></div>
<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
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
<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$fromDt	= $myF->dateAdd('month', -1, Date('Y-m-01'), 'Y-m-d');
	$toDt	= $myF->dateAdd('day', -1, $myF->dateAdd('month', 1, $fromDt, 'Y-m-d'), 'Y-m-d');
?>
<script type="text/javascript">
	var IsLoad = false;

	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
			if (!IsLoad){
				lfSearch();
				IsLoad = true;
			}
		});
	});

	function lfResizeSub(){
		$('#ID_BODY').height($('#ID_BODY').height() - $('#PAGE_LIST').height());
	}

	function lfSearch(page){
		if (!page) page = 1;

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId;?>_search.php'
		,	data:{
				'orgNo':$('#txtOrgNo').val()
			,	'orgName':$('#txtOrgName').val()
			,	'inGbn':$('#cboInGbn').val()
			,	'inBank':$('#cboInBank').val() ? $('#cboInBank option:selected').text() : ''
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
			,	'outStat':$('#cboOutStat').val()
			,	'contCom':$('#cboContCom').val() ? $('#cboContCom option:selected').text() : ''
			,	'page':page
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_BODY tbody').html(html);
				$('#ID_BODY tbody td').unbind('dblclick').bind('dblclick', function(){
					var index = $('td', $(this).parent()).index(this);

					if (index == 1 || index == 2){
						$('#txtOrgNo').val('');
						$('#txtOrgName').val('');
						$('#cboInGbn').val('');
						$('#cboInBank').val('');
						$('#txtFromDt').val('');
						$('#txtToDt').val('');
						$('#cboOutStat').val('');
						$('#cboContCom').val('');

						if (index == 1){
							$('#txtOrgNo').val($(this).text());
						}else if (index == 2){
							$('#txtOrgName').val($(this).text());
						}

						lfSearch();
					}
				});
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

	function lfExcel(){
		var parm = new Array();
			parm = {
				'orgNo':$('#txtOrgNo').val()
			,	'orgName':$('#txtOrgName').val()
			,	'inGbn':$('#cboInGbn').val()
			,	'inBank':$('#cboInBank').val() ? $('#cboInBank option:selected').text() : ''
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
			,	'outStat':$('#cboOutStat').val()
			,	'contCom':$('#cboContCom').val() ? $('#cboContCom option:selected').text() : ''
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

		form.setAttribute('method', 'post');
		form.setAttribute('action', './center_<?=$menuId?>_excel.php');

		document.body.appendChild(form);

		form.submit();
	}

	function lfReg(orgNo, issueDt, issueSeq){
		if (!orgNo) orgNo = '';
		if (!issueDt) issueDt = '';
		if (!issueSeq) issueSeq = '';

		var width = 600;
		var height = 600;
		var left = window.screenLeft + $('#left_box').width();
		var top = window.screenTop + $('#divTitle').offset().top;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var url = './center_pay_direct_in.php';
		var win = window.open('about:blank', 'PAY_IN_REG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'orgNo':orgNo
			,	'issueDt':issueDt
			,	'issueSeq':issueSeq
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

		form.setAttribute('target', 'PAY_IN_REG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfModify(obj, orgNo, date, seq){
		var objModal = new Object();

		objModal.result = false;

		showModalDialog('./pop_payin_remark.php?orgNo='+orgNo+'&date='+date+'&seq='+seq, objModal, 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:yes');

		if (objModal.result){
			$(obj).text(objModal.remark);
		}
	}

	function lfDtl(orgNo, issueDt, issueSeq){
		if (!orgNo || !issueDt || !issueSeq) return;

		var objModal = new Object();

		showModalDialog('./pop_payin_dtl.php?orgNo='+orgNo+'&issueDt='+issueDt+'&issueSeq='+issueSeq, objModal, 'dialogWidth:500px; dialogHeight:400px; dialogHide:yes; scroll:no; status:yes');
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col width="60px">
		<col width="100px">
		<col width="60px">
		<col width="50px">
		<col width="60px">
		<col width="50px">
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
				<input id="txtOrgName" type="text" value="" style="width:100%;">
			</td>
			<th class="center">입금구분</th>
			<td>
				<select id="cboInGbn" style="width:auto;">
					<option value="">전체</option>
					<option value="1">CMS</option>
					<option value="2">무통장</option>
				</select>
			</td>
			<th class="center">입금은행</th>
			<td>
				<select id="cboInBank" style="width:auto;">
					<option value="">전체</option>
					<option value="1">개인</option>
					<option value="2">농협</option>
					<option value="3">법인(케어비지트)</option>
					<option value="4">법인(지케어)</option>
				</select>
			</td>
			<td class="left last" rowspan="2">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
				<span class="btn_pack m"><button onclick="lfExcel();">Excel</button></span>
				<span class="btn_pack m"><button onclick="lfReg();">등록</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">조회기간</th>
			<td colspan="3">
				<input id="txtFromDt" type="text" value="" class="date"> ~
				<input id="txtToDt" type="text" value="" class="date">
			</td>
			<th class="center">출금상태</th>
			<td>
				<select id="cboOutStat" style="width:auto;">
					<option value="">전체</option>
					<option value="1">출금성공</option>
					<option value="2">출금실패</option>
					<option value="3">상태없음</option>
				</select>
			</td>
			<th class="center">계약회사</th>
			<td>
				<select id="cboContCom" style="width:auto;">
					<option value="">전체</option>
					<option value="1">케어비지트</option>
					<option value="2">굿이오스</option>
					<option value="3">지케어</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="90px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="60px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">청구일자</th>
			<th class="head bold">입금일자<br>입금수정</th>
			<th class="head">시간</th>
			<th class="head">입금구분</th>
			<th class="head bold">입금금액<br>상세조회</th>
			<th class="head">출금상태</th>
			<th class="head">출금은행</th>
			<th class="head">입금은행</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top last" colspan="12">
				<div id="ID_BODY" style="overflow-x:hidden; overflow-y:scroll; height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="75px">
							<col width="85px">
							<col width="70px">
							<col width="70px">
							<col width="60px">
							<col width="60px">
							<col width="65px">
							<col width="65px">
							<col width="65px">
							<col width="65px">
							<col>
						</colgroup>
						<tbody></tbody>
					</table>
				<div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="11" id="PAGE_LIST"></td>
		</tr>
	</tfoot>
</table>
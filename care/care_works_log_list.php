<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$today	= Date('Y-m-d');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		if (!$('#txtFrom').val()){
			alert('조회기간을 입력하여 주십시오.');
			$('#txtFrom').focus();
			return;
		}

		if (!$('#txtTo').val()){
			alert('조회기간을 입력하여 주십시오.');
			$('#txtTo').focus();
			return;
		}

		if ($('#txtFrom').val() > $('#txtTo').val()){
			alert('조회기간 입력오류입니다. 확인 후 다시 입력하여 주십시오.');
			$('#txtTo').focus();
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./care_works_log_list_search.php'
		,	data:{
				'SR'	:$('#sr').val()
			,	'fromDt':$('#txtFrom').val()
			,	'toDt'	:$('#txtTo').val()
			,	'order'	:$('input:radio[name="optOrder"]:checked').val()
			,	'gbn'	:$('#cboResultGbn').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			}
		,	complete:function(){
			}
		,	error:function(request, status, error){
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
				'SR'	:$('#sr').val()
			,	'fromDt':$('#txtFrom').val()
			,	'toDt'	:$('#txtTo').val()
			,	'order'	:$('input:radio[name="optOrder"]:checked').val()
			,	'gbn'	:$('#cboResultGbn').val()
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
		
		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './care_works_log_list_excel.php');
		

		document.body.appendChild(form);

		form.submit();
	}

	function lfWorksLogPrt(){
		var parm = new Array();
			parm = {
				'SR'	:$('#sr').val()
			,	'fromDt':$('#txtFrom').val()
			,	'toDt'	:$('#txtTo').val()
			,	'order'	:$('input:radio[name="optOrder"]:checked').val()
			,	'gbn'	:$('#cboResultGbn').val()
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

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './care_works_log_excel_test.php');
		
		//	form.setAttribute('action', './care_works_log_excel.php');
		

		document.body.appendChild(form);

		form.submit();
	}

	function lfChkDt(obj){
		//var lastday = __getLastDay($('#txtFrom').val().split('-')[0], $('#txtFrom').val().split('-')[1]);
		var days = __DateDiff($('#txtFrom').val(), $('#txtTo').val()) + 1;

		if (days > 31){
			if ($(obj).attr('id') == 'txtFrom'){
				var dt = __addDate('d', 31, $('#txtFrom').val(), '-');
				$('#txtTo').val(dt);
			}else{
				var dt = __addDate('d', -31, $('#txtTo').val(), '-');
				$('#txtFrom').val(dt);
			}
		}
	}

	//재가지원 업무내용등록
	function lfWorkLogReg(para,obj){
		if (!para) return;

		para = __parseVal(para);

		var objModal = new Object();
		var url = '../care/care_works_log_reg.php';
		var style = 'dialogWidth:800px; dialogHeight:550px; dialogHide:yes; scroll:no; status:no';

		objModal.result = false;
		objModal.svcCd = $('#sr').val();
		objModal.date = para['date'];
		objModal.time = para['time'];
		objModal.seq = para['seq'];
		objModal.jumin = para['jumin'];
		objModal.suga = para['suga'];
		objModal.resource = para['res'];
		objModal.mem = para['mem'];

		window.showModalDialog(url, objModal, style);

		if (!obj) return;
		if (objModal.result){
			$('#ID_CONTENTS',obj).text(objModal.contents);

			if (objModal.pic == 'Y'){
				$('#ID_PICTURE',obj).html('<img src="../image/f_list.gif" border="0">');
			}else{
				$('#ID_PICTURE',obj).html('');
			}
		}
	}
</script>
<div class="title title_border">실적등록(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="560px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기간</th>
			<td class="">
				<input id="txtFrom" type="text" value="<?=$today;?>" class="date" onchange="lfChkDt(this);"> ~
				<input id="txtTo" type="text" value="<?=$today;?>" class="date" onchange="lfChkDt(this);">
				<span>*검색기간을 한달이내로 설정하여 주십시오.</span>
			</td>
			<!--
				<th class="center">년월</th>
				<td class="left">
					<div style="float:left; width:auto;">
						<div style="width:auto; margin-top:1px;">
							<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
							<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="yymm" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
							<div style="float:left; width:auto; padding-top:2px; margin-right:5px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
						</div>
						<div style="width:auto;"><?=$myF->_btn_month($month,'lfMonth(');?></div>
					</div>
				</td>
			-->
			<td class="left last" rowspan="3">
				<span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel();">Excel</button></span>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfWorksLogPrt();">업무일지</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">선택</th>
			<td class="">
				<select id="cboResultGbn" style="width:auto;">
					<option value="">전체</option>
					<option value="Y">업무내용 등록 일정만 조회</option>
					<option value="N">업무내용 미등록 일정만 조회</option>
				</select>
			</td>
		</tr>
		<tr>
			<th class="center">정렬</th>
			<td class="">
				<input id="optOrder1" name="optOrder" type="radio" class="radio" value="1" checked><label for="optOrder1">일자/시간순</label>
				<input id="optOrder2" name="optOrder" type="radio" class="radio" value="2"><label for="optOrder2">직원명순</label>
				<input id="optOrder3" name="optOrder" type="radio" class="radio" value="3"><label for="optOrder3">서비스순</label>
				<input id="optOrder4" name="optOrder" type="radio" class="radio" value="4"><label for="optOrder4">대상자순</label>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="120px">
		<col width="80px">
		<col width="70px">
		<col width="100px">
		<col width="100px">
		<col width="70px">
		<col width="150px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">일시</th>
			<th class="head">대상자</th>
			<th class="head">생년월일</th>
			<th class="head">서비스</th>
			<th class="head">자원</th>
			<th class="head">담당</th>
			<th class="head">업무내용</th>
			<th class="head">사진</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST">
		<tr>
			<td class="center last" colspan="8">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>
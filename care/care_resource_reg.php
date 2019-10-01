<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$code = $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
	var opener = null;
	var wins = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = 9;

		__init_form(document.f);

		//setTimeout('lfLoad()',150);
		//setTimeout('lfLoadResource()',200);
		lfInit();
		lfGetCust(opener.cust);
	});

	//서비스 로드
	function lfInit(){
		$.ajax({
			type :'POST'
		,	url  :'./care_resource_reg_search.php'
		,	data :{
				'type':opener.type
			,	'sr':opener.sr
			,	'svc':opener.svc
			,	'cd':opener.cd
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				lfCareSvcFindResult(col['cd'],col['nm1'],col['nm2'],col['nm3']);

				$('#txtCost').val(__num2str(col['cost']));
				$('#txtFromDt').val(col['from']);
				$('#txtToDt').val(col['to']);
				$('#btnSvcCd').css('display','none');
			}
		,	error:function(){
			}
		}).responseXML;
	}
	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':opener.type
			,	'sr':opener.sr
			,	'svc':opener.svc
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				$('#cboSvc').html(data);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//서비스 로드
	function lfLoadResource(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':opener.type+'_LOAD'
			,	'sr':opener.sr
			,	'svc':opener.svc
			,	'cd':opener.cd
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseStr(data);

				$('#txtCost').val(__num2str(col['cost']));
				$('#txtFromDt').val(col['from']);
				$('#txtToDt').val(col['to']);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//거래처조회
	function lfFindCust(){
		var h = 550;
		var w = 800;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../care/care_cust_find.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';

		if (wins != null){
			wins.close();
			wins = null;
		}

		wins = window.open('about:blank', 'FIND_CARECUST', option);
		wins.opener = self;
		wins.focus();

		var parm = new Array();
			parm = {
				'SR':opener.sr
			,	'return':'lfFindCustResult'
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

		form.setAttribute('target', 'FIND_CARECUST');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
		/*
			var objModal = new Object();
			var url = './care_cust_find.php';
			var style = 'dialogWidth:800px; dialogHeight:550px; dialogHide:yes; scroll:no; status:no';

			objModal.win = window;
			objModal.sr	= opener.sr;
			window.showModalDialog(url, objModal, style);

			if (objModal.result == 1){
				$('#lblGbn').text(objModal.gbn);
				$('#lblCustCd').text(objModal.cd);
				$('#lblCustNm').text(objModal.nm);
				$('#lblBizno').text(objModal.bizno);
				$('#lblManager').text(objModal.manager);
				$('#lblStat').text(objModal.stat);
				$('#lblItem').text(objModal.item);
				$('#lblPhone').text(objModal.phone);
				$('#lblFAX').text(objModal.fax);
				$('#lblAddr').text(objModal.addr);
				$('#lblPernm').text(objModal.pernm);
				$('#lblPertel').text(objModal.pertel);
			}
		*/
	}

	function lfFindCustResult(obj){
		$('#lblGbn').text(obj['gbn']);
		$('#lblCustCd').text(obj['cd']);
		$('#lblCustNm').text(obj['nm']);
		$('#lblBizno').text(obj['bizno']);
		$('#lblManager').text(obj['manager']);
		$('#lblStat').text(obj['stat']);
		$('#lblItem').text(obj['item']);
		$('#lblPhone').text(__getPhoneNo(obj['phone']).split('-').join('.'));
		$('#lblFAX').text(__getPhoneNo(obj['fax']).split('-').join('.'));
		$('#lblAddr').text(obj['addr']);
		$('#lblPernm').text(obj['pernm']);
		$('#lblPertel').text(__getPhoneNo(obj['pertel']).split('-').join('.'));
	}

	function lfGetCust(cust){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'71_FIND'
			,	'cd':cust
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseStr(data);

				if (col['gbn'] == '1'){
					col['gbn'] = '공공';
				}else if (col['gbn'] == '2'){
					col['gbn'] = '기업';
				}else if (col['gbn'] == '3'){
					col['gbn'] = '단체';
				}else{
					col['gbn'] = '개인';
				}

				$('#lblGbn').text(col['gbn']);
				$('#lblCustCd').text(cust);
				$('#lblCustNm').text(col['nm']);
				$('#lblBizno').text(col['bizno']);
				$('#lblManager').text(col['manager']);
				$('#lblStat').text(col['stat']);
				$('#lblItem').text(col['item']);
				$('#lblPhone').text(__getPhoneNo(col['phone']).split('-').join('.'));
				$('#lblFAX').text(__getPhoneNo(col['fax']).split('-').join('.'));
				$('#lblAddr').text(col['addr']);
				$('#lblPernm').text(col['pernm']);
				$('#lblPertel').text(__getPhoneNo(col['pertel']).split('-').join('.'));
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfApply(){
		/*
			if (!$('#cboSvc option:selected').val()){
				alert('서비스를 선택하여 주십시오.');
				$('#cboSvc option:selected').focus();
				return;
			}
		*/
		if (!$('#lblSvcCd').attr('code')){
			alert('서비스를 선택하여 주십시오.');
			lfCareSvcFind();
			return;
		}

		if (!$('#lblCustCd').text()){
			lfFindCust();
			return;
		}

		if (!$('#txtFromDt').val()){
			alert('적용일을 입력하여 주십시오.');
			$('#txtFromDt').focus();
			return;
		}

		if (!$('#txtToDt').val()){
			alert('종료일을 입력하여 주십시오.');
			$('#txtToDt').focus();
			return;
		}

		if ($('#txtFromDt').val() > $('#txtToDt').val()){
			alert('적용일이 종료일보가 큽니다. 확인 후 다시 입력하여 주십시오.');
			$('#txtToDt').focus();
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type'	:opener.type
			,	'sr'	:opener.sr
			,	'svc'	:$('#lblSvcCd').attr('code') //$('#cboSvc option:selected').val())
			,	'cd'	:opener.cd
			,	'cust'	:$('#lblCustCd').text()
			,	'cost'	:$('#txtCost').val()
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					opener.result = 1;
					alert('정상적으로 처리되었습니다.');
					self.close();
				}else if (result == 8){
					alert('적용기간이 중복됩니다. 확인 후 다시 입력하여 주십시오.');
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//재가관리 서비스 조회
	function lfCareSvcFind(){
		var h = 600;
		var w = 800;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../care/care_suga_find.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';

		if (wins != null){
			wins.close();
			wins = null;
		}

		wins = window.open('about:blank', 'FIND_CARESVC', option);
		wins.opener = self;
		wins.focus();

		var parm = new Array();
			parm = {
				'sr':opener.sr
			,	'type':'CUST_SEL'
			,	'return':'lfCareSvcFindResult'
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

		form.setAttribute('target', 'FIND_CARESVC');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfCareSvcFindResult(cd,mstNm,proNm,svcNm){
		$('#lblSvcCd').attr('code',cd).text(mstNm+'/'+proNm+'/'+svcNm);
	}
</script>

<form id="f" name="f" method="post">
<div class="title">
	<div>서비스</div>
</div>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>서비스</th>
			<td>
				<!--select id="cboSvc" name="cboSvc" style="width:auto;"></select-->
				<button onclick="lfCareSvcFind();">찾기</button>
				<span id="lblSvcCd" code="" style="height:25px; line-height:25px; margin-left:5px;"></span>
			</td>
		</tr>
		<!--tr>
			<th>단가</th>
			<td>
				<input id="txtCost" name="txt" type="text" class="number" value="0" style="width:70px;">
			</td>
		</tr-->
		<tr>
			<th>기간</th>
			<td>
				<input id="txtFromDt" name="txt" type="text" class="date" value="" orgVal=""> ~
				<input id="txtToDt" name="txt" type="text" class="date" value="" orgVal="">
			</td>
		</tr>
	</tbody>
</table>
<div id="lsTitle" class="title"><div>자원연결</div></div>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="150px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>거래처코드</th>
			<td>
				<button onclick="lfFindCust();">찾기</button>
				<span id="lblCustCd" class="bold"></span>
			</td>
			<th>구분</th>
			<td class="left" id="lblGbn"></td>
		</tr>
		<tr>
			<th>거래처명</th>
			<td class="left" colspan="3" id="lblCustNm"></td>
		</tr>
		<tr>
			<th>사업자번호</th>
			<td class="left" id="lblBizno"></td>
			<th>대표자명</th>
			<td class="left" id="lblManager"></td>
		</tr>
		<tr>
			<th>업태</th>
			<td class="left" id="lblStat"></td>
			<th>업종</th>
			<td class="left" id="lblItem"></td>
		</tr>
		<tr>
			<th>연락처</th>
			<td class="left" id="lblPhone"></td>
			<th>FAX</th>
			<td class="left" id="lblFAX"></td>
		</tr>
		<tr>
			<th>주소</th>
			<td class="left" colspan="3" id="lblAddr"></td>
		</tr>
		<tr>
			<th>담당자</th>
			<td class="left" id="lblPernm"></td>
			<th>연락처</th>
			<td class="left" id="lblPertel"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center bottom">
				<span class="btn_pack m"><button type="button" onclick="lfApply();">적용</button></span>
				<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span><?
				if ($debug){?>
					<!--<a href="javascript:document.f.submit();">reload</a>--><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
</form>
<?
	include_once('../inc/_footer.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	$year = date('Y');
	$month = date('m');

?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
	});

	function lfSearch(){
		if (!$('#lblCenter').attr('code')){
			alert('기관을 선택하여 주십시오.');
			lfFindCenter($("#lblCenter"));
			return;
		}
		
		if (!$('#lblCltObj').text()){
			alert('수급자를 선택하여 주십시오.');
			lfFindClient($('#lblCltObj'),$('#lblCenter'),1);
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./report2014_copy_search.php'
		,	data :{
				'code':$('#lblCenter').attr('code')
			,	'ssn':$('#txtSsn1').attr('value')
			,	'seq':$('#txtSeq1').attr('value')
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
			
				var vals = result.split('//');

				$('#ddtCnt').text(vals[0]);
				$('#pstCnt').text(vals[1]);
				$('#bsrCnt').text(vals[2]);
				$('#planCnt').text(vals[3]);
				$('#agrCnt').text(vals[4]);
			}
		,	error:function(){
			}
		}).responseXML;
	}
	
	function lfFindCenter(obj){
		var objModal = new Object();
		var url      = '../find/_find_center.php';
		var style    = 'dialogWidth:800px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.mode = '99';
		objModal.code = '';
		objModal.name = '';

		window.showModalDialog(url, objModal, style);

		if (!objModal.code) return;

		$(obj).attr('code',objModal.code).text(objModal.name+'('+objModal.code+')');		
	}
	
	
	/*********************************************************

		수급자 찾기

	*********************************************************/
	function lfFindClient(obj, obj2, mode){
		var objModal = new Object();
		var url      = '../find/_find_client.php';
		var style    = 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';
		
		objModal.code  = $(obj2).attr('code');
		objModal.kind  = $('#kind').attr('value');
		objModal.year  = $('#year').attr('value');
		objModal.month  = $('#month').attr('value');
		
		window.showModalDialog(url, objModal, style);

		var result = objModal.para;	

		if (!result) return;

		var arr = result.split('&'); 
		var val = new Array();

		for(var i=0; i<arr.length; i++){
			var tmp = arr[i].split('=');

			val[tmp[0]] = tmp[1];
		}
		
		$(obj).text(val['name']);
		if(mode == '1'){
			$('#txtSsn1').attr('value', val['jumin']);
		}else {
			$('#txtSsn2').attr('value', val['jumin']);	
		}
		
	}
	

	/*********************************************************

		직원 찾기

	*********************************************************/
	function findMember(){
		
		var val = __findMember($('#lblTarget').attr('code'));
		
		if (!val) return;

		$('#lblMemName').text(val['name']);
		$('#txtMemCd').attr('value', val['jumin']);	
		
	}

	function lfCopy(){
		if (!$('#lblCenter').attr('code')){
			alert('기관을 선택하여 주십시오.');
			lfFindCenter($("#lblCenter"));
			return;
		}

		if (!$('#lblCltObj').text()){
			alert('수급자를 선택하여 주십시오.');
			lfFindClient($('#lblCltObj'),$('#lblCenter'),1);
			return;
		}
		
		if (!$('#txtSeq1').val()){
			alert('순번을 입력해 주십시오.');
			$('#txtSeq1').focus();
			return;
		}
		
		if (!$('#lblTarget').attr('code')){
			alert('대상기관을 선택하여 주십시오.');
			lfFindCenter($("#lblTarget"));
			return;
		}
		
		if (!$('#lblCltTarget').text()){
			alert('대상수급자를 선택하여 주십시오.');
			lfFindClient($('#lblCltObj'),$('#lblCenter'),2);
			return;
		}
		
		if (!$('#lblMemName').text()){
			alert('작성자를 선택하여 주십시오.');
			findMember();
			return;
		}
		
		if (!$('#txtSeq2').val()){
			alert('대상 순번을 입력해 주십시오.');
			$('#txtSeq2').focus();
			return;
		}

		if (!$('#txtRegDt').val()){
			$('#txtRegDt').focus();
			alert('작성일자를 입력하여 주십시오.');
			return;
		}

		$.ajax({
			type :'POST'
		,	async:false
		,	url  :'./report2014_copy_save.php'
		,	data :{
				'code1':$('#lblCenter').attr('code')
			,	'code2':$('#lblTarget').attr('code')
			,	'ssn1':$('#txtSsn1').val()
			,	'ssn2':$('#txtSsn2').val()
			,	'memCd':$('#txtMemCd').val()
			,	'seq1':$('#txtSeq1').val()
			,	'seq2':$('#txtSeq2').val()
			,	'regDt':$('#txtRegDt').val()
			,	'ddtCnt':$('#ddtCnt').text()
			,	'pstCnt':$('#pstCnt').text()
			,	'bsrCnt':$('#bsrCnt').text()
			,	'planCnt':$('#planCnt').text()
			,	'agrCnt':$('#agrCnt').text()
			}
		,	beforeSend: function(){
			}
		,	success: function(result){
				alert(result);
			}
		});
	}

</script>
<form name="f" method="post">
<div class="title title_border">기관평가자료(복사)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="250px">
		<col width="250px">
		<col width="*">
	</colgroup>
	<thead>
		<tr>
			<th class="head"></th>
			<th class="head">현재기관</th>
			<th class="head">대상기관</th>
			<th class="head last"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>기관명</th>
			<td class="left" style="padding-top:1px;" >
				<div style="float:left; width:auto; height:25px;"><span class="btn_pack m find" onclick="lfFindCenter($('#lblCenter'));"></span></div>
				<div style="float:left; width:auto; line-height:25px;"><span id="lblCenter" code=""></span></div>
			</td>
			<td class="left" style="padding-top:1px;" >
				<div style="float:left; width:auto; height:25px;"><span class="btn_pack m find" onclick="lfFindCenter($('#lblTarget'));"></span></div>
				<div style="float:left; width:auto; line-height:25px;"><span id="lblTarget" code=""></span></div>	
			</td>
			<td class="last"></td>
		</tr>
		<tr>
			<th >수급자</th>
			<td class="left" style="padding-top:1px;" >
				<div style="float:left; width:auto; height:25px;"><span class="btn_pack m find" onclick="lfFindClient($('#lblCltObj'),$('#lblCenter'),1);"></span></div>
				<div style="float:left; width:auto; line-height:25px;"><span id="lblCltObj" ></span></div>
				<input type="hidden" id="txtSsn1" name="txtSsn1" value="">
			</td>
			<td class="left" style="padding-top:1px;" >
				<div style="float:left; width:auto; height:25px;"><span class="btn_pack m find" onclick="lfFindClient($('#lblCltTarget'),$('#lblTarget'),2);"></span></div>
				<div style="float:left; width:auto; line-height:25px;"><span id="lblCltTarget" ></span></div>
				<input type="hidden" id="txtSsn2" name="txtSsn2" value="">
			</td>
			<td class="last"></td>
		</tr>
		<tr>
			<th >작성자</th>
			<td ></td>
			<td class="left" style="padding-top:1px;" >
				<div style="float:left; width:auto; height:25px;"><span class="btn_pack m find" onclick="findMember();"></span></div>
				<div style="float:left; width:auto; line-height:25px;"><span id="lblMemName" ></span></div>
				<input type="hidden" id="txtMemCd" name="txtMemCd" value="">
			</td>
			<td class="last"></td>
		</tr>
		<tr>
			<th >순번</th>
			<td><input id="txtSeq1" name="txtSeq1" type="text" value="" onchange="lfSearch();"></td>
			<td><input id="txtSeq2" name="txtSeq2" type="text" value="" onchange="lfSearch();"></td>
			<td class="last"></td>
		</tr>
		<tr>
			<th >작성일자</th>
			<td ></td>
			<td><input id="txtRegDt" name="txtRegDt" type="text" value="" class="date"></td>
			<td class="last"></td>
		</tr>
	</tbody>
</table>
<div class="title title_border">자료 조회</div>
<table class="my_table" style="width:100%; margin-bottom:10px;">
<colgroup>
	<col width="50px">
	<col width="50px">
	<col width="50px">
	<col width="50px">
	<col width="50px">
	<col width="50px">
	<col width="50px">
	<col width="50px">
	<col width="50px">
	<col width="50px">
</colgroup>
<tbody>
	<tr>
		<th>낙상</th>
		<td class="center"><span id="ddtCnt"></span></td>
		<th>욕창</th>
		<td class="center"><span id="pstCnt"></span></td>
		<th>욕구</th>
		<td class="center"><span id="bsrCnt"></span></td>
		<th>급여계획</th>
		<td class="center"><span id="planCnt"></span></td>
		<th>이용계획서</th>
		<td class="center last"><span id="agrCnt"></span></td>
	</tr>
</tbody>
</table>
<div align="center"><span class="btn_pack m"><button type="button" onclick="lfCopy();">복사하기</button></span></div>
<?
	include_once('../inc/_db_close.php');
?>
<input type="hidden" id="kind" name="kind" value="0">
<input type="hidden" id="year" name="year" value="<?=$year;?>">
<input type="hidden" id="month" name="month" value="<?=$month;?>">
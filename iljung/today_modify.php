<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}
?>
<script type="text/javascript" src="../longcare/longcare.js"></script>
<script type="text/javascript" src="./iljung.longcare.js"></script>
<script type="text/javascript" src="./iljung.longcare.result.js"></script>
<script type="text/javascript">
	var timer = null;
	var cnt = 1;

	$(document).ready(function(){
		setTimeout('lfSearch()',200);

		//2014.02.03 공단 소스변경으로 인해 임시막음.
		//$('#btnAutoRun').attr('disabled',true);
		//$('#btnSearch').attr('disabled',true);
		//$('#btnReceipt').attr('disabled',true);
		//$('#btnUpload').attr('disabled',true);
		//$('#btnComplete').attr('disabled',true);
	});

	function lfInitTimer(){
		if (timer == null){
			$('#btnAutoRun').css('font-weight','bold').text('자동실행중');
			$('#btnSearch').attr('disabled',true);
			$('#btnReceipt').attr('disabled',true);
			$('#btnUpload').attr('disabled',true);
			$('#btnComplete').attr('disabled',true);

			cnt = 1;
			timer = setInterval('lfChkTimer()',1000);
		}else{
			$('#btnAutoRun').css('font-weight','normal').text('자동실행');
			$('#btnSearch').attr('disabled',false);
			$('#btnReceipt').attr('disabled',false);
			$('#btnUpload').attr('disabled',false);
			$('#btnComplete').attr('disabled',false);

			clearInterval(timer);
			timer = null;
		}
	}

	function lfChkTimer(){
		//2.5분마다 공단조회
		//if (cnt % 150 == 0){
		//	lfLongtermLoad();
		//}

		//1분마다 접수처리
		if (cnt % 60 == 0){
			//접수처리 후 조회
			lfReceipt();
		}

		//5분마다 공단업로드
		if (cnt % 300 == 0){
			lfUpload();
		}

		//3분마다 공단업로드 확인
		if (cnt % 180 == 0){
			lfConfirm();
		}

		$('#lblCnt').text(cnt);

		cnt ++;
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./today_modify_search.php'
		,	data:{
				'reason':$('#cboReason').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfReceipt(){
		$.ajax({
			type:'POST'
		,	url:'./today_modify_receipt.php'
		,	data:{
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				lfSearch();
			}
		,	error:function(request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfUpload(){
		if (!lfLoginIs()){
			alert('공단로그인을 선행하여 주십시오.');
			return;
		}

		var width = 500;
		var height = 200;
		var left = window.screenLeft + ($(window).width() - width) / 2;
		var top = window.screenTop + ($(window).height() - height) / 2;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=yes';
		var url = './today_modify_upload.php';
		var win = window.open('about:blank', 'UPLOAD_LIST', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'dpDelYn':($('#chkDpDelYn').attr('checked') ? 'Y' : 'N')
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

		form.setAttribute('target', 'UPLOAD_LIST');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfConfirm(){
		if (!lfLoginIs()){
			alert('공단로그인을 선행하여 주십시오.');
			return;
		}

		var width = 800;
		var height = 200;
		var left = window.screenLeft + ($(window).width() - width) / 2;
		var top = window.screenTop + ($(window).height() - height) / 2;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=yes';
		var url = './today_modify_confirm.php';
		var win = window.open('about:blank', 'CONFIRM_LIST', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
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

		form.setAttribute('target', 'CONFIRM_LIST');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfLongtermLoad(){
		if (!lfLoginIs()){
			alert('공단로그인을 선행하여 주십시오.');
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./today_modify_longterm.php'
		,	data:{
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfLoginIs(){
		var YYMM = getToday().split('-').join('').substring(0,6);
		var rst = false;

		try{
			if ('<?=$debug;?>' == '1'){
				rst = true;
			}else{
				$.ajax({
					type: 'GET',
					async:false,
					url : 'http://www.longtermcare.or.kr/portal/site/nydev/',
					success: function (data){
						var str = $('.welcome',data).html();

						if (str){
							if (YYMM >= '201108'){
								rst = true;
							}else{
								rst = false;
							}
						}else{
							rst = false;
						}

						return rst;
					},
					error:function(request, status, error){
						alert('[ERROR No.02]'
							 +'\nCODE : ' + request.status
							 +'\nSTAT : ' + status
							 +'\nMESSAGE : ' + request.responseText);
						rst = false;
					}
				});
			}
		}catch(e){
			rst = false;
		}

		return rst;
	}
</script>
<div class="title title_border">당일일정 변경요청</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="50px">
		<col width="*" span="2">
	</colgroup>
	<tbody>
		<tr>
			<th class="center">사유</th>
			<td>
				<select id="cboReason" style="width:auto;">
					<option value="">전체</option>
					<option value="01">천재지변</option>
					<option value="02">응급상황</option>
					<option value="03">자격변동 처리 지연</option>
					<option value="04">기타사유</option>
				</select>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button id="btnAutoRun" onclick="lfInitTimer();">자동실행</button></span>
				<span class="btn_pack m"><button id="btnSearch" onclick="lfSearch();">변경내역 조회</button></span>
				<span class="btn_pack m"><button id="btnReceipt" onclick="lfReceipt();">변경내역 적용</button></span>
				<span class="btn_pack m"><button id="btnUpload" onclick="lfUpload();">공단업로드</button></span>
				<span class="btn_pack m"><button id="btnComplete" onclick="lfConfirm();">업로드확인</button></span>
			</td>
			<td class="right last">
				<span id="lblCnt"></span>
			</td>
		</tr>
		<tr>
			<th class="center">선택</th>
			<td class="last" colspan="3">
				<label><input id="chkDpDelYn" type="checkbox" class="checkbox" value="Y" checked>공단과 불일치한 계획발견시 삭제 후 업로드를 실행함.</label>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="50px">
		<col width="40px" span="2">
		<col width="100px">
		<col width="50px">
		<col width="40px" span="2">
		<col width="100px">
		<col width="120px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">수급자</th>
			<th class="head" rowspan="2">서비스</th>
			<th class="head" colspan="3">변경전</th>
			<th class="head" rowspan="2">요청<br>구분</th>
			<th class="head" colspan="3">변경후</th>
			<th class="head" rowspan="2">사유</th>
			<th class="head" rowspan="2">상태</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">요양보호사</th>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">요양보호사</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
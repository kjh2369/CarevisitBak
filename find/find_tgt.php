<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcNm	= Array('homecare'=>'재가요양','nurse'=>'가사간병', 'old'=>'노인돌봄', 'baby'=>'산모신생아', 'dis'=>'장애인활동지원', 'careSupport'=>'재가지원', 'careResource'=>'자원연계');
	$rstFun = $_GET['rstFun'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
		$('#ID_LIST').height(__GetHeight($('#ID_LIST')));
	});

	function lfSearch(){
		if (!$('#txtRcptDt',opener.document).val()){
			alert('접수일자를 입력 후 조회하여 주십시오.');
			$('#txtRcptDt',opener.document).focus();
			return;
		}

		$.ajax({
			type:'POST',
			url:'./find_tgt_search.php',
			data:{
				'name':$('#txtName').val()
			,	'svcCd':$('#cboSvcCd').val()
			,	'useGbn':$('#cboUseGbn').val()
			,	'date':$('#txtVisitDt',opener.document).val()
			},
			beforeSend: function (){
			},
			success:function(html){
				$('tbody',$('#ID_LIST')).html(html);
			},
			error: function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfSelTgt(obj){
		var obj = __GetTagObject($(obj),'TR');
		var bnfCd = '';

		if ($(obj).attr('svcCd') == '0') bnfCd = '03';
		else if ($(obj).attr('svcCd') == '1') bnfCd = '02';
		else if ($(obj).attr('svcCd') == '2') bnfCd = '04';
		else if ($(obj).attr('svcCd') == '3') bnfCd = '01';
		else if ($(obj).attr('svcCd') == '4') bnfCd = '';
		else if ($(obj).attr('svcCd') == 'S') bnfCd = '05';
		else if ($(obj).attr('svcCd') == 'R') bnfCd = '';

		eval('<?=$rstFun;?>({"jumin":"'+$(obj).attr('jumin')+'","name":"'+$(obj).attr('name')+'","svcCd":"'+$(obj).attr('svcCd')+'","phone":"'+$(obj).attr('phone')+'","mobile":"'+$(obj).attr('mobile')+'","postno":"'+$(obj).attr('postno')+'","addr1":"'+$(obj).attr('addr1')+'","addr2":"'+$(obj).attr('addr2')+'","addrDtl":"'+$(obj).attr('addrDtl')+'","bnfCd":"'+bnfCd+'"});');

		self.close();
	}
</script>
<div class="title title_border">고객찾기</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="70px">
		<col width="50px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">고객명</th>
			<td>
				<input id="txtName" type="text" style="width:100%;">
			</td>
			<th class="center">서비스</th>
			<td>
				<select id="cboSvcCd" style="width:auto;">
					<option value="">전체</option><?
					foreach($gHostSvc as $svcCd => $val){
						if (!$val || !$svcNm[$svcCd]) continue;?>
						<option value="<?=$svcCd;?>"><?=$svcNm[$svcCd];?></option><?
					}?>
				</select>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table><?

$colgrp = '
	<col width="40px">
	<col width="70px">
	<col width="70px">
	<col width="300px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgrp;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">생년월일</th>
			<th class="head">이용서비스</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>
<div id="ID_LIST" style="overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgrp;?></colgroup>
		<tbody></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>
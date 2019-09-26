<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$jumin	= $_POST['jumin'];
	$mode   = $_POST['mode'];

?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
			lfSearch();
		});

		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		$(obj).height(__GetHeight($(obj)));
		
	});
	
	window.onunload = function(){
		
		if('<?=$mode;?>'=='teamSet'){
			opener.lfSearch();
		}else {
			$.ajax({
			type: 'POST'
			,	async:false
			,	url : './team_fine_nm.php'
			,	beforeSend: function(){
				}
			,	data: {
					'svcCd'	:'<?=$svcCd;?>'
				,	'jumin'	:'<?=$jumin;?>'
				}
			,	success: function(result){
					opener.lfTeamResult(result);
				}
			,	complete: function(html){
				}
			,	error: function (){
				}
			}).responseXML;
			
		}
	}

	


	function lfApply(){
		if (!$('#teamCd').val()){
			alert('관리자를 선택하여 주십시오.');
			return;
		}

		if (!$('#fromYm').val()){
			alert('적용 시작년월을 입력하여 주십시오.');
			$('#fromYm').focus();
			return;
		}

		if (!$('#toYm').val()){
			alert('적용 종료년월을 입력하여 주십시오.');
			$('#toYm').focus();
			return;
		}

		if ($('#fromYm').val() > $('#toYm').val()){
			alert('적용기간 입력오류입니다. 확인하여 주십시오.');
			$('#toYm').focus();
			return;
		}

		$.ajax({
			type: 'POST'
		,	url : './team_save.php'
		,	beforeSend: function(){
			}
		,	data: {
				'svcCd'	:'<?=$svcCd;?>'
			,	'jumin'	:'<?=$jumin;?>'
			,	'seq'	:$('#seq').val()
			,	'teamCd':$('#teamCd').val()
			,	'fromYm':$('#fromYm').val().replace('-','')
			,	'toYm'	:$('#toYm').val().replace('-','')
			,	'deAmt'	:$('#deductAmt').val()
			,	'deRate':$('#deductRate').val()
			}
		,	success: function(result){
				if (!result){
					self.close();
					if('<?=$mode;?>'=='teamSet'){
						opener.lfSearch();
						//$('#strTeam<?=$val;?>', opener.document).val($('#teamNm').val());
					}
				}else{
					alert(result);
				}
			}
		,	complete: function(html){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfSearch(){
		$.ajax({
			type: 'POST'
		,	url : './team_search.php'
		,	beforeSend: function(){
			}
		,	data: {
				'svcCd'	:'<?=$svcCd;?>'
			,	'jumin'	:'<?=$jumin;?>'
			}
		,	success: function(html){
				$('#ID_LIST').html(html);

				/*
				var obj = $('td',$('tr:first',$('#ID_LIST')));


				$('#teamNm').val($(obj).eq(1).text());
				$('#fromYm').val($(obj).eq(2).text().replace('.','-'));
				$('#toYm').val($(obj).eq(3).text().replace('.','-'));

				var obj = $('tr:first',$('#ID_LIST'));

				$('#teamCd').val($(obj).attr('teamCd'));
				$('#seq').val($(obj).attr('seq'));
				*/
			}
		,	complete: function(html){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfTeamDelete(seq){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시곗습니까?')) return;

		$.ajax({
			type: 'POST'
		,	url : './team_delete.php'
		,	beforeSend: function(){
			}
		,	data: {
				'svcCd'	:'<?=$svcCd;?>'
			,	'jumin'	:'<?=$jumin;?>'
			,	'seq'	:seq
			}
		,	success: function(result){
				if (!result){
					lfSearch();
				}else{
					alert(result);
				}
			}
		,	complete: function(html){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfNew(){
		$('input').each(function(){
			$(this).val('');
		});
	}
</script>
<div class="title title_border">담당관리자</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="150px">
		<col width="80px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">관리자</th>
			<td>
				<input id="seq" type="hidden">
				<input id="teamCd" name="teamCd" type="hidden" class="clsData" value="">
				<input id="teamNm" name="teamNm" type="" class="clsData" value="" style="background-color:#eeeeee; margin-top:3px;" readOnly> <span class="btn_pack find" style="margin-top:1px; margin-left:-5px;" onclick="__find_yoyangsa('<?=$orgNo;?>','<?=$svcCd;?>',document.getElementById('teamCd'),document.getElementById('teamNm'));"></span>
			</td>
			<th class="center">기본공제금액</th>
			<td>
				<input id="deductAmt" type="text"class="number clsData" value="" style="width:100%;">
			</td>
			<td class="left" rowspan="2">
				<span class="btn_pack m"><button onclick="lfApply();">적용</button></span>
				<!--<span class="btn_pack m"><button onclick="lfNew();">신규</button></span>-->
			</td>
		</tr>
		<tr>
			<th class="center">적용기간</th>
			<td>
				<input id="fromYm" type="text" class="yymm"> ~ <input id="toYm" type="text" class="yymm">
			</td>
			<th class="center">공제율(%)</th>
			<td>
				<input id="deductRate" type="text" class="number clsData" value="" style="width:50px;" onkeydown="__onlyNumber(this,'.');">%
			</td>
		</tr>
	</tbody>
</table>

<div class="title title_border">적용내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="60px" span="2">
		<col width="70px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">관리자명</th>
			<th class="head">적용년월</th>
			<th class="head">종료년월</th>
			<th class="head">공제금액</th>
			<th class="head">공제율</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="70px">
			<col width="60px" span="2">
			<col width="70px" span="2">
			<col>
		</colgroup>
		<tbody id="ID_LIST"></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>
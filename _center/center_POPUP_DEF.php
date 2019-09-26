<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">

	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		$('input:radio').unbind('click').bind('click',function(){
			if ($(this).attr('name') == 'optPop'){
				var obj = $('#txtDay');
			}else if ($(this).attr('name') == 'optStop'){
				var obj = $('#txtStopDt');
			}else{
				return;
			}

			if ($(this).val() == 'Y'){
				$(obj).attr('disabled',false);
			}else{
				$(obj).attr('disabled',true);
			}
		});

		lfSearch();
	});

	/*
	function lfResizeSub(){
		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		var h = document.body.offsetHeight - $(obj).offset().top - $('#copyright').height();
		$(obj).height(h);
	}
	*/

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(val){
				if (!val) return;

				var val = __parseVal(val);

				if (val['popYn'] != 'Y') val['popYn'] = 'N';
				if (val['stopYn'] != 'Y') val['stopYn'] = 'N';

				$('input:radio[name="optPop"][value="'+val['popYn']+'"]').attr('checked',true);
				$('input:radio[name="optStop"][value="'+val['stopYn']+'"]').attr('checked',true);

				if (val['popYn'] == 'Y'){
					$('#txtDay').attr('disabled',false).val(val['popDay']);
				}else{
					$('#txtDay').attr('disabled',true).val('');
				}

				if (val['stopYn'] == 'Y'){
					$('#txtStopDt').attr('disabled',false).val(__getDt(val['stopDt']));
				}else{
					$('#txtStopDt').attr('disabled',true).val('');
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

	function lfSave(){
		if ($('input:radio[name="optPop"]:checked').val() == 'Y'){
			if (!$('#txtDay').val()){
				alert('팝업실행 일자를 입력하여 주십시오.');
				$('#txtDay').focus();
				return;
			}
		}

		if ($('input:radio[name="optStop"]:checked').val() == 'Y'){
			if (!$('#txtStopDt').val()){
				alert('로그인중지 실행일자를 입력하여 주십시오.');
				$('#txtStopDt').focus();
				return;
			}
		}

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_save.php'
		,	data:{
				'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			,	'popYn'	:$('input:radio[name="optPop"]:checked').val()
			,	'popDay':$('#txtDay').val()
			,	'stopYn':$('input:radio[name="optStop"]:checked').val()
			,	'stopDt':$('#txtStopDt').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result) alert(result);
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
<div style="padding:5px;">
	<div>
		<div style="float:left; width:auto;">- 해당 청구년월의 미납기간 팝업설정을 하시곘습니까?</div>
		<div style="float:left; width:auto;">
			<label><input id="optPopY" name="optPop" type="radio" class="radio" value="Y">예</label>
			<label><input id="optPopN" name="optPop" type="radio" class="radio" value="N" checked>아니오</label>
		</div>
	</div>
	<div>
		<div style="float:left; width:auto;">- 청구년월</div>
		<div style="float:left; width:auto;"><input id="txtDay" type="text" class="number" style="width:30px; margin-left:3px; margin-right:3px;" maxlength="2"></div>
		<div style="float:left; width:auto;"> 일 이후 부터 팝업을 실행합니다.</div>
	</div>
	<div style="margin-top:10px;">
		<div style="float:left; width:auto;">- 미납시 로그인 중지를 하시겠습니까?</div>
		<div style="float:left; width:auto;">
			<label><input id="optStopY" name="optStop" type="radio" class="radio" value="Y">예</label>
			<label><input id="optStopN" name="optStop" type="radio" class="radio" value="N" checked>아니오</label>
		</div>
	</div>
	<div>
		<div style="float:left; width:auto;">- 미납이</div>
		<div style="float:left; width:auto;"><input id="txtStopDt" type="text" class="date" style="margin-left:3px; margin-right:3px;"></div>
		<div style="float:left; width:auto;">이후가 되면 기관의 로그인을 중지합니다.</div>
	</div>
	<div style="margin-top:20px;">
		※ 팝업설정 시 미납이 완료되기 전까지 팝업이 실행됩니다.
	</div>
	<div style="margin-top:20px;">
		<span class="btn_pack m"><span class="save"></span><button onclick="lfSave();">저장</button></span>
	</div>
</div>


<?
$colgroup = '
	<col width="30px">
	<col width="90px">
	<col width="150px">
	<col width="80px">
	<col width="70px" span="5">
	<col>';?>
<!--
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head"><input id="chkAll" type="checkbox" class="checkbox"></th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">청구총액</th>
			<th class="head">입금총액</th>
			<th class="head">연결총액</th>
			<th class="head">미납총액</th>
			<th class="head">미연결</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>
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
-->
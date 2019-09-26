<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		//lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_BODY_LIST')).html(html);
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

	function lfTaxReg(orgNo){
		if (!orgNo) orgNo = '';

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_reg.php'
		,	data:{
				'orgNo'	:orgNo
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LOCAL_POP_DATA').html(html);
				$('#ID_LOCAL_POP')
					.css('left','250px')
					.css('top','250px')
					.css('width','700px')
					.css('height','300px')
					.show();
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

	function lfTaxDel(orgNo){
		if (!orgNo) return;
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시곗습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_del.php'
		,	data:{
				'orgNo'	:orgNo
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
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
</script><?

$colgroup = '
	<col width="40px">
	<col width="150px">
	<col width="90px">
	<col width="70px">
	<col width="70px">
	<col width="50px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">기관기호</th>
			<th class="head">대표자</th>
			<th class="head">발급일자</th>
			<th class="head">구분</th>
			<th class="head last">
				<div style="float:right; width:auto;">
					<span class="btn_pack small" style="margin-left:5px;"><button>Excel(일괄등록)</button></span>
					<span class="btn_pack small"><button onclick="lfTaxReg();">기관개별등록</button></span>
				</div>
				<div style="float:center; width:auto;">비고</div>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top bottom last" colspan="7">
				<div id="ID_BODY_LIST" style="overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
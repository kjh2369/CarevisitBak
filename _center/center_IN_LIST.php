<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	var win = null;

	window.onunload = function(){

		if (win != null) win.close();
	}

	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		lfSearch();

		return;
		var page = 1;
		var loading = false; //to prevents multipal ajax loads

		$('#ID_BODY_LIST').scroll(function() { //detect page scroll
			//alert($('#ID_BODY_LIST').scrollTop()+'/'+$('#ID_BODY_LIST').height()+'/'+$('#ID_BODY_LIST').attr('scrollHeight'));
			if($('#ID_BODY_LIST').scrollTop() + $('#ID_BODY_LIST').height() == $('#ID_BODY_LIST').attr('scrollHeight')){  //user scrolled to bottom of the page?
				if (loading == false){ //there's more data to load
					loading = true; //prevent further ajax loading

					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

					//load data from the server using a HTTP POST request
					$.post('./center_<?=$menuId?>_search.php',{'orgNo':$('#txtOrgNo').val(),'orgNm':$('#txtOrgNm').val(),'mgNm':$('#txtMgNm').val(),'page':(page+1)}, function(html){
						if (html){
							//$("#results").append(data); //append received data into the element
							$('tbody',$('#ID_BODY_LIST')).append(html);

							//hide loading image
							$('#tempLodingBar').remove(); //hide loading image once data is received

							page ++;
							loading = false;
						}else{
							$('#tempLodingBar').remove();
						}
					}).fail(function(xhr, ajaxOptions, thrownError) { //any errors?

						alert(thrownError); //alert with HTTP error
						$('#tempLodingBar').remove(); //hide loading image
						loading = false;

					});
				}
			}
		});

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			,	'orgNo'	:$('#txtOrgNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'mgNm'	:$('#txtMgNm').val()
			,	'page':1
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

	function lfDtl(orgNo, gbn){
		if (!orgNo || !gbn) return;

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_dtl.php'
		,	data:{
				'orgNo'	:orgNo
			,	'gbn'	:gbn
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LOCAL_POP_DATA').html(html);
				$('#ID_LOCAL_POP')
					.css('left','250px')
					.css('top','205px')
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

	function lfModify(no, orgNo, cmsNo, cmsDt, cmsSeq){
		var left = (screen.availWidth - (width = 700)) / 2, top = (screen.availHeight - (height = 300)) / 2;

		win = window.open('./center_<?=$menuId?>_modify.php?no='+no+'&orgNo='+orgNo+'&cmsNo='+cmsNo+'&cmsDt='+cmsDt+'&cmsSeq='+cmsSeq,'<?=$menuId?>_MODIFY','left='+left+',top='+top+', width='+width+', height='+height+', scrollbars=no, status=no, resizable=no');
		win.focus();
	}

	function lfDelete(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./center_cms_delete.php'
		,	data:{
				'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			,	'modYn'	:$('#chkYn').attr('checked') ? 'Y' : 'N'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
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

	function lfInReg(){
		var left = (screen.availWidth - (width = 800)) / 2, top = (screen.availHeight - (height = 400)) / 2;
		var win = window.open('./center_cms_reg.php', 'CMS_REG', 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no');
			win.focus();
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="130px">
		<col width="70px">
		<col width="90px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관명</th>
			<td><input id="txtOrgNm" type="text" style="width:100%;"></td>
			<th class="center">기관기호</th>
			<td><input id="txtOrgNo" type="text" style="width:100%;"></td>
			<th class="center">대표자명</th>
			<td><input id="txtMgNm" type="text" style="width:100%;"></td>
			<td class="left last">
				<div style="float:right; width:auto;">
					<label><input id="chkYn" type="checkbox" class="checkbox">변경된 내역도 삭제함.</label>
					<span class="btn_pack small"><button onclick="lfDelete();">삭제</button></span>
				</div>
				<div style="float:left; width:auto;">
					<span class="btn_pack small"><button onclick="lfSearch();">조회</button></span>
					<span class="btn_pack small"><button onclick="lfInReg();">입금</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<!--
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="150px">
		<col width="90px">
		<col width="80px">
		<col width="80px">
		<col width="80px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">CMS번호</th>
			<th class="head">등록일자</th>
			<th class="head">연결여부</th>
			<th class="head">입금액</th>
			<th class="head">연결금액</th>
			<th class="head">미연결금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>
--><?
$colgroup = '
	<col width="40px">
	<col width="100px">
	<col width="70px">
	<col width="70px">
	<col width="80px">
	<col width="70px">
	<col width="80px">
	<col width="80px">
	<col width="80px">
	<col width="80px">
	<col>';
?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">기관기호</th>
			<th class="head">청구일자</th>
			<th class="head">청구금액</th>
			<th class="head">입금일자</th>
			<th class="head">입금금액</th>
			<th class="head">적용금액</th>
			<th class="head">미납금액</th>
			<th class="head">미납누계</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="sum center" colspan="3"><div class="right">합계</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_3"></div></td>
			<td class="sum center"><div id="ID_CELL_SUM_4" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_5" class="right"></div></td>
			<td class="sum center"><div id="ID_CELL_SUM_6" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_7" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_8" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_9" class="right">0</div></td>
			<td class="sum center last"></td>
		</tr>
	</tbody>
</table>
<div id="ID_BODY_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody></tbody>
	</table>
</div>
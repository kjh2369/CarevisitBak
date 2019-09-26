<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId;?>_search.php'
		,	data:{
				'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			,	'orgNo'	:$('#txtOrgNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'mgNm'	:$('#txtMgNm').val()
			,	'allYn'	:$('#chkAllYn').attr('checked') ? 'Y' : 'N'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_BODY')).html(html);
				$('tbody tr',$('#ID_BODY')).css('cursor','default').attr('selYn','N').unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','');
				}).unbind('click').bind('click',function(){
					return;
					$('tbody tr',$('#ID_BODY')).css('background-color','').attr('selYn','N');
					$(this).attr('selYn','Y');
					$(this).css('background-color','#FAF4C0');

					lfRowSel($('td',this).eq(1).text());
				});
				$('#tempLodingBar').remove();

				var acAmt = 0, dcAmt = 0;

				$('tbody tr',$('#ID_BODY')).each(function(){
					acAmt += __str2num($('td',this).eq(4).text());
					dcAmt += __str2num($('td',this).eq(5).text());
				});

				$('#ID_CELL_SUM_4').text(__num2str(acAmt));
				$('#ID_CELL_SUM_5').text(__num2str(dcAmt));
				$('#ID_CELL_SUM_6').text(__num2str(acAmt - dcAmt));
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfRowSel(obj, orgNo){
		var obj = __GetTagObject($(obj),'TR');

		$('tbody tr',$('#ID_BODY')).css('background-color','').attr('selYn','N');
		$(obj).attr('selYn','Y');
		$(obj).css('background-color','#FAF4C0');

		var left = (screen.availWidth - (width = 800)) / 2, top = (screen.availHeight - (height = 600)) / 2;

		win = window.open('./center_<?=$menuId?>_modify.php?orgNo='+orgNo+'&year='+$('#lblYYMM').attr('year')+'&month='+$('#lblYYMM').attr('month'),'<?=$menuId?>_MODIFY','left='+left+',top='+top+', width='+width+', height='+height+', scrollbars=no, status=no, resizable=no');
		win.focus();

		return false;
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">청구년월</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM")); lfSearch();');?></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="50px">
		<col width="150px">
		<col width="70px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td><input id="txtOrgNo" type="text" style="width:100%;"></td>
			<th class="center">기관명</th>
			<td><input id="txtOrgNm" type="text" style="width:100%;"></td>
			<th class="center">대표자명</th>
			<td><input id="txtMgNm" type="text" style="width:100%;"></td>
			<td class="left last"><span class="btn_pack m"><button onclick="lfSearch();">조회</button></span></td>
		</tr>
		<tr>
			<td class="last" colspan="7">
				<label><input id="chkAllYn" type="checkbox" class="checkbox" checked>과금금액이 있는 기관만 출력.</label>
			</td>
		</tr>
	</tbody>
</table><?

$colgroup = '
	<col width="40px">
	<col width="100px">
	<col width="170px">
	<col width="90px">
	<col width="80px">
	<col width="80px">
	<col width="80px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">과금금액</th>
			<th class="head">할인금액</th>
			<th class="head">청구금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="sum center" colspan="4"><div class="right">합계</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_4" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_5" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_6" class="right">0</div></td>
			<td class="sum center last"></td>
		</tr>
	</tbody>
</table>
<div id="ID_BODY" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody></tbody>
		<tfoot>
			<tr>
				<td class="bottom last"></td>
			</tr>
		</tfoot>
	</table>
</div>
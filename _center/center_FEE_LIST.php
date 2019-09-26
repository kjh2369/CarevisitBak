<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	function lfResizeSub(){
		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		var h = document.body.offsetHeight - $(obj).offset().top - $('#copyright').height();
		$(obj).height(h);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_FEE_LIST_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html('<tr><td colspan="10">'+html+'</td></tr>');
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
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="50px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">회사</th>
			<td>
				<select id="cboCompany" style="width:auto;" onchange="GetValue('BRANCH',this.value);">
					<option value="">-회사선택-</option><?
					$sql = 'SELECT	b00_code AS cd
							,		b00_name AS nm
							,		b00_manager AS manager
							,		b00_domain AS domain
							FROM	b00branch
							WHERE	b00_com_yn = \'Y\'
							ORDER	BY nm';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['domain'];?>" <?=$row['domain'] == 'carevisit.net' ? 'selected' : '';?>><?=$row['nm'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
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
</table><?
$colgroup = '
	<col width="40px">
	<col width="150px">
	<col width="90px">
	<col width="100px">
	<col width="80px" span="5">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">서비스</th>
			<th class="head">기본금</th>
			<th class="head">초과구분</th>
			<th class="head">초과수</th>
			<th class="head">초과금액</th>
			<th class="head">합계</th>
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
<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");

	$year  = Date('Y');
	$month = Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYear').text());

		year += pos;

		$('#lblYear').text(year);

		lfSearch();
	}

	function lfMoveMonth(month){
		setTimeout('lfMoveMonthSub(\''+month+'\')',10);
	}

	function lfMoveMonthSub(month){
		$('#lblMonth').text(month);
		$('div[id^="btnMonth_"]').removeClass('my_month_y').addClass('my_month_1');
		$('#btnMonth_'+month).removeClass('my_month_1').addClass('my_month_y');

		lfSearch();
	}

	function lfSearch(){
		setTimeout('lfSearchSub()',200);
	}

	function lfSearchSub(){
		$.ajax({
			type:'POST'
		,	url:'./mem_4insu_his_list.php'
		,	beforeSend: function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	data:{
				'year':$('#lblYear').text()
			,	'month':$('#lblMonth').text()
			,	'stat':$('#cboStat option:selected').val()
			}
		,	success: function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						var stat = '<span style="color:red;">미처리</span>';

						if (col['stat'] == '1'){
							stat = '<span style="color:blue;">취득</span>';
						}else if (col['stat'] == '3'){
							stat = '<span style="color:black;">상실</span>';
						}

						html += '<tr>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="left">'+col['name']+'</td>';
						html += '<td class="center">'+col['a']+'</td>';
						html += '<td class="center">'+col['h']+'</td>';
						html += '<td class="center">'+col['e']+'</td>';
						html += '<td class="center">'+col['s']+'</td>';
						html += '<td class="center">'+__getDate(col['f'],'.')+'</td>';
						html += '<td class="center">'+__getDate(col['t'],'.')+'</td>';
						html += '<td class="center">'+stat+'</td>';
						html += '<td class="left last"></td>';
						html += '</tr>';

						no ++;
					}
				}

				if (!html){
					 html = '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">4대보험 취득/상실내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="85px">
		<col width="500px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td>
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
					<div id="lblMonth" style="display:none;"><?=$month;?></div>
				</div>
			</td>
			<td class="left"><? echo $myF->_btn_month($month,'lfMoveMonth(',');',null,true);?></td>
			<th class="center">구분</th>
			<td class="last">
				<select id="cboStat" style="width:auto;" onchange="lfSearch();">
					<option value="ALL">전체</option>
					<option value="1">취득</option>
					<option value="3">상실</option>
					<option value="9">미처리</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="40px" span="4">
		<col width="70px" span="2">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">국민</th>
			<th class="head">건강</th>
			<th class="head">고용</th>
			<th class="head">산재</th>
			<th class="head">취득일</th>
			<th class="head">상실일</th>
			<th class="head">상태</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
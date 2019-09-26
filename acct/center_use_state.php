<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year  = Date('Y');
	$month = IntVal(Date('m'));
?>
<style>
	html,body{overflow:hidden;}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		var top = $('#divBody').offset().top;
		var height = $(window).height();

		height = height - top - 10;

		$('#divBody').height(height);

		setTimeout('lfSearch()',200);
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_use_state_search.php'
		,	data:{
				'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = 1;
				var first = true;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var clr = '';

						if (!first){
							 clr = 'border-top:1px solid #cccccc;';
						}else{
							 first = false;
						}

						html += '<tr style="cursor:default;" onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">';
						html += '<td class="center bottom" style="'+clr+'">'+no+'</td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="nowrap" style="width:75px; margin-left:5px; text-align:left;">'+col['code']+'</div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="nowrap" style="width:125px; margin-left:5px; text-align:left;" title="'+col['name']+'">'+col['name']+'</div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="nowrap" style="width:55px; margin-left:5px; text-align:left;">'+col['manager']+'</div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="nowrap" style="width:85px; margin-left:5px; text-align:left;">'+__getPhoneNo(col['telno'],'.')+'</div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="" id="lblSvcCnt0" style="width:auto; margin-right:5px; text-align:right;"></div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="" id="lblSvcCnt1" style="width:auto; margin-right:5px; text-align:right;"></div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="" id="lblSvcCnt2" style="width:auto; margin-right:5px; text-align:right;"></div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="" id="lblSvcCnt3" style="width:auto; margin-right:5px; text-align:right;"></div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="" id="lblSvcCnt4" style="width:auto; margin-right:5px; text-align:right;"></div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="" id="lblSvcCntS" style="width:auto; margin-right:5px; text-align:right;"></div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="" id="lblSvcCntR" style="width:auto; margin-right:5px; text-align:right;"></div></td>';
						html += '<td class="center bottom" style="'+clr+'"><div class="" id="lblSvcCntT" style="width:auto; margin-right:5px; text-align:right;"></div></td>';
						html += '<td class="center bottom last" style="'+clr+'"><div class="" id="lblSvcLoad" style="width:auto; margin-left:5px; text-align:left;" onclick="lfAddRow($(this).parent().parent());">조회</div></td>';
						html += '</tr>';

						no ++;
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadAll(){
		var fn = new lfTimer();
		var no = 1;

		$('tr',$('#tbodyList')).each(function(){
			fn.setTimeout(this,no,no*100);
			no ++;
		});
	}

	function lfTimer(){
		this.setTimeout = function(obj,no,speed,val){
			setTimeout(function(speed){lfAddRow(obj)},speed);
		}
	}

	function lfAddRow(obj){
		var code = $('td',obj).eq(1).text();	//기관기호

		$.ajax({
			type:'POST'
		,	url:'./center_use_state_client.php'
		,	data:{
				'code':code
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#lblSvcLoad',obj).text('Loading...');
			}
		,	success:function(data){
				var col = __parseVal(data);
				var tot = 0;

				for(var i in col){
					$('#lblSvcCnt'+i,obj).text(__num2str(col[i]));

					tot += __str2num(col[i]);
				}

				$('#lblSvcCntT',obj).text(__num2str(tot));
				$('#lblSvcLoad',obj).text('OK');
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<div class="title title_border">기관이용현황</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="center">
				<div class="left" style="padding-top:2px;">
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); setTimeout('lfSearch()',200);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
				<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); setTimeout('lfSearch()',200);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="left last"><? echo $myF->_btn_month($month, '__moveMonth(', '); setTimeout("lfSearch()",200);', null, false);?></td>
		</tr>
	</tbody>
</table><?

$colgroup = '	<col width="40px">
				<col width="80px">
				<col width="130px">
				<col width="60px">
				<col width="90px">
				<col width="40px" span="7">
				<col width="60px">
				<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">기관코드</th>
			<th class="head" rowspan="2">기관명</th>
			<th class="head" rowspan="2">대표자</th>
			<th class="head" rowspan="2">연락처</th>
			<th class="head" colspan="7">사용서비스유형</th>
			<th class="head" rowspan="2">총인원</th>
			<th class="head last" rowspan="2"><span class="btn_pack small"><button type="button" onclick="lfLoadAll();">전체조회</button></span></th>
		</tr>
		<tr>
			<th class="head">요양</th>
			<th class="head">가사</th>
			<th class="head">돌봄</th>
			<th class="head">산모</th>
			<th class="head">활동</th>
			<th class="head">재가</th>
			<th class="head">자원</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top last" colspan="20">
				<div id="divBody" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>
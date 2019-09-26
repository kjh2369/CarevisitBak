<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$month = Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoad()',200);
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYYMM').attr('year'));

		year += pos;

		$('#lblYYMM').attr('year',year).text(year);

		lfLoad();
	}

	function lfMoveMonth(month){
		var obj = $('div[id^="btnMonth_"]');

		$(obj).each(function(){
			if ($(obj).hasClass('my_month_y')){
				$(obj).removeClass('my_month_y').addClass('my_month_1');
				return false;
			}
		});

		obj = $('#btnMonth_'+month);

		$(obj).removeClass('my_month_1').addClass('my_month_y');
		$('#lblYYMM').attr('month',month);

		lfLoad();
	}

	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var first = true;
				var rowspan = 1;
				var total = 0;
				var tot = {};
				var html = '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						if (first){
							html += '<tr>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;" colspan="3"><div class="right">총&nbsp;&nbsp;&nbsp;계</div></td>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;"><div id="lblTotal" class="right">0</div></td>';
							html += '<td class="center sum last" style="background-color:#DDEEF3;"></td>';
							html += '</tr>';
							first = false;
						}

						if (col['mstCnt'] > 0){
							rowspan = __str2num(col['mstCnt'])+1;
							html += '<tr>';
							html += '<td class="center" rowspan="'+rowspan+'" style="line-height:1.3em;">'+col['mstNm']+'</td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;" colspan="2"><div class="right">합&nbsp;&nbsp;&nbsp;계</div></td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;"><div id="lblTot_'+col['suga'].substring(0,1)+'" class="right">0</div></td>';
							html += '<td class="center sum last" style="background-color:#FDE9D9;"></td>';
							html += '</tr>';
						}

						if (col['proCnt'] > 0){
							rowspan = __str2num(col['proCnt'])+1;
							html += '<tr>';
							html += '<td class="center" rowspan="'+rowspan+'" style="line-height:1.3em;">'+col['proNm']+'</td>';
							html += '<td class="center sum bold" style="background-color:#FFFF00;"><div class="right">소&nbsp;&nbsp;&nbsp;계</div></td>';
							html += '<td class="center sum bold" style="background-color:#FFFF00;"><div id="lblTot_'+col['suga'].substring(0,3)+'" class="right">0</div></td>';
							html += '<td class="center sum last" style="background-color:#FFFF00;"></td>';
							html += '</tr>';
						}

						html += '<tr>';

						var cnt = (__str2num(col['cnt']) > 0 ? col['cnt'] : '');

						//총계
						total += __str2num(cnt);

						//합계
						tot[col['suga'].substring(0,1)] = __str2num(tot[col['suga'].substring(0,1)]) + __str2num(cnt);

						//소계
						tot[col['suga'].substring(0,3)] = __str2num(tot[col['suga'].substring(0,3)]) + __str2num(cnt);

						if (cnt){
							if (col['gbn'] == '1'){
								cnt += ' 명';
							}else{
								cnt += ' 회';
							}
						}

						html += '<td class="center"><div class="left">'+col['svcNm']+'</div></td>';
						html += '<td class="center"><div class="right">'+cnt+'</div></td>';
						html += '<td class="center last"></td>';
						html += '</tr>';
					}
				}

				if (!html){
					html = '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);
				$('#lblTotal').text(__num2str(total)); //총계

				for(var i in tot){
					$('#lblTot_'+i).text(__num2str(tot[i]));
				}

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfExcel(type){
		var url = './care_excel.php';
		var parm = new Array();
			parm = {
				'type':'<?=$type;?>_'+type
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'prtGbn':$('#cboPrtGbn option:selected').val()
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

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">보고서(기관)</div>
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
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="left last"><? echo $myF->_btn_month($month,'lfMoveMonth(');?></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">보고서</th>
			<td class="last">
				<select id="cboQuarter" style="width:auto; margin-right:0;">
					<option value="1">1분기</option>
					<option value="2">2분기</option>
					<option value="3">3분기</option>
					<option value="4">4분기</option>
				</select>
				<select id="cboPrtGbn" style="width:auto; margin-left:0; margin-right:0;">
					<option value="1">기본</option>
					<option value="2">상세</option>
				</select>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel('MIDDLE'+$('#cboQuarter option:selected').val());">분기(중분류)</button></span>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel('QUARTER'+$('#cboQuarter option:selected').val());">분기(세부사업)</button></span>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel('MONTH');">월보고서</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="120px">
		<col width="170px">
		<col width="90px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">서비스</th>
			<th class="head">명/횟수</th>
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
	include_once('../inc/_db_close.php');
?>
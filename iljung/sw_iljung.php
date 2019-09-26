<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$month = IntVal(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSetCalendar();
	});

	function lfSetCalendar(){
		$.ajax({
			type:'POST'
		,	url:'./sw_fun.php'
		,	data:{
				'type':'CALENDAR'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var holiday = {};
				var row = data.split('?');

				holiday['0'] = '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						holiday[col['day']] = col['holiday'];
					}
				}

				var y = __str2num($('#lblYYMM').attr('year'));
				var m = __str2num($('#lblYYMM').attr('month')) - 1;

				var theDate = new Date(y,m,1);
				var theDay = theDate.getDay();
				var last = [31,28,31,30,31,30,31,31,30,31,30,31];

				if ((y % 400 == 0) || (y % 4 == 0 && y % 100 != 0)){
					last[1] = 29;
				}

				var lastDate = last[m];
				var row = Math.ceil((theDay+lastDate)/7);

				$('div[id^="ID_CELL_DAY"]').css('color','').html('');
				$('div[id^="ID_CELL_NM"]').css('color','').attr('day','').html('');
				$('div[id^="ID_CELL_STR"]').attr('day','').html('');

				var dNum = 1;
				for (var i=1; i<=row; i++) { // 행 만들기
					for (var j=0; j<7; j++) { // 열 만들기
						// 월1일 이전 + 월마지막일 이후 = 빈 칸으로!
						if(i==1 && (j+1)<=theDay || dNum>lastDate) {
						}else{
							$('#ID_CELL_DAY_'+i+'_'+j).attr('day',dNum).text(dNum);
							$('#ID_CELL_STR_'+i+'_'+j).attr('day',dNum);

							if (holiday[dNum] || j == 0){
								$('#ID_CELL_DAY_'+i+'_'+j).css('color','RED');
								$('#ID_CELL_NM_'+i+'_'+j).css('color','RED').text(holiday[dNum]);
							}else if (j == 6){
								$('#ID_CELL_DAY_'+i+'_'+j).css('color','BLUE');
								$('#ID_CELL_NM_'+i+'_'+j).css('color','BLUE');
							}

							dNum++;
						}
					}
				}

				$('#tempLodingBar').remove();

				lfLoadSwList();
			}
		});
	}

	function lfLoadSwList(){
		$.ajax({
			type:'POST'
		,	url:'./sw_fun.php'
		,	data:{
				'type':'SW_LIST'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_SW_LIST').html(html);
				$('div',$('#ID_SW_LIST')).css('cursor','default').unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','');
				}).unbind('click').bind('click',function(){
					$('div',$('#ID_SW_LIST')).css('background-color','').attr('selYn','N');
					$(this).css('background-color','#FAF4C0').attr('selYn','Y');

					lfLoadSwIljung($(this).attr('memCd'));
				}).attr('selYn','N');
				$('#tempLodingBar').remove();
			}
		});
	}

	function lfLoadSwIljung(memCd){
		var prtYn = $('#chkWrkGbnPrtYn').attr('checked') ? 'Y' : 'N';

		if (!prtYn) prtYn = 'Y';

		$.ajax({
			type:'POST'
		,	url:'./sw_fun.php'
		,	data:{
				'type':'SW_ILJUNG'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'memCd':memCd
			,	'prtYn':prtYn
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				$('div[id^="ID_CELL_NM_"], div[id^="ID_CELL_STR_"]').html('');
				$('#ID_TARGET_CNT, #ID_VISIT_CNT').text('0');

				var y = __str2num($('#lblYYMM').attr('year'));
				var m = __str2num($('#lblYYMM').attr('month')) - 1;

				var theDate = new Date(y,m,1);
				var theDay = theDate.getDay();
				var last = [31,28,31,30,31,30,31,31,30,31,30,31];

				if ((y % 400 == 0) || (y % 4 == 0 && y % 100 != 0)){
					last[1] = 29;
				}

				var row = __parseVal(data);

				for(var i=1; i<=last[m]; i++){
					if (!row[i]) continue;

					var obj = $('div[id^="ID_CELL_STR"][day="'+i+'"]');
					var R = (row[i].split('|').join('?').split('+').join('=').split('$').join('&')).split('?');
					var htm = '';

					for(var j=0; j<R.length; j++){
						var v = __parseVal(R[j]);
						htm += '<div>'+__min2time(v['from'],'HHMM')+'~'+__min2time(v['to'],'HHMM')+' <span class="'+(v['bold'] == 'Y' ? 'bold' : '')+'">'+v['work']+'</span></div>';

						$('#ID_TARGET_CNT').text(__str2num($('#ID_TARGET_CNT').text()) + __str2num(v['tgCnt']));
						$('#ID_VISIT_CNT').text(__str2num($('#ID_VISIT_CNT').text()) + (v['bold'] == 'Y' ? 1 : 0));
					}

					$(obj).html(htm);
				}

				$('#tempLodingBar').remove();
			}
		});
	}

	function lfExcel(gbn){
		var memCd = '';

		if (gbn == 'PERSON'){
			memCd = $('div[selYn="Y"]',$('#ID_SW_LIST')).attr('memCd');
			if (!memCd){
				alert('사회복지사를 선택하여 주십시오.');
				return;
			}
		}

		var prtYn = $('#chkWrkGbnPrtYn').attr('checked') ? 'Y' : 'N';

		if (!prtYn) prtYn = 'Y';

		var parm = new Array();
			parm = {
				'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'lvlYn':$('#chkPrtLevelYn').attr('checked') ? 'Y' : 'N'
			,	'memCd':memCd
			,	'prtYn':prtYn
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

		//form.setAttribute('target', '');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './sw_iljung_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">사회복지사 일정표 조회</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="last">
				<div class="left" style="padding-top:2px; float:left;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1,'lfSetCalendar()');" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1,'lfSetCalendar()');" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
					<div style="width:auto; float:left; padding-left:5px; padding-bottom:1px;"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM"),"lfSetCalendar()")')?></div>
					<div style="width:auto; float:right;">
						<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel('PERSON');">개별출력</button></span>
						<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel('ALL');">전체출력</button></span>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="120px">
		<col width="1px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="top" id="ID_SW_LIST"></td>
			<td>
				<img src="" style="width:1px; height:1px;">
			</td>
			<td class="top last">
				<table class="my_table" style="width:100%;">
					<tbody>
						<tr>
							<td class="bold last">
								<div>
									<label><input id="chkWrkGbnPrtYn" type="checkbox" class="checkbox" onclick="lfLoadSwIljung($('#ID_SW_LIST div[selYn=\'Y\']').attr('memCd'));" checked>내근, 점심 출력</label>
									<label><input id="chkPrtLevelYn" type="checkbox" class="checkbox">엑셀 출력시 등급 출력</label>
								</div>
								<div class="left">월 방문대상자수 : <span id="ID_TARGET_CNT">0</span>명 / 방문건수 : <span id="ID_VISIT_CNT">0</span>건</div>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="4%">
						<col width="16%" span="6">
					</colgroup>
					<thead>
						<tr>
							<th class="head" style="color:RED;">일</th>
							<th class="head">월</th>
							<th class="head">화</th>
							<th class="head">수</th>
							<th class="head">목</th>
							<th class="head">금</th>
							<th class="head last" style="color:BLUE;">토</th>
						</tr>
					</thead>
					<tbody><?
						for($i=1; $i<=6; $i++){
							$cls = '';
							if ($i == 6) $cls .= ' bottom ';?>
							<tr><?
							for($j=0; $j<=6; $j++){
								if ($j == 0){
									$clr = 'RED';
								}else if ($j == 6){
									$clr = 'BLUE';
								}else{
									$clr = '';
								}
								if ($j == 6) $cls .= ' last ';?>
								<td class="top <?=$cls;?>">
									<div style="">
										<div id="ID_CELL_DAY_<?=$i;?>_<?=$j;?>" day="" style="float:left; width:auto; height:20px; padding-left:3px; color:<?=$clr;?>;"></div>
										<div id="ID_CELL_NM_<?=$i;?>_<?=$j;?>" style="float:right; width:auto; height:20px; padding-right:3px;"></div>
									</div>
									<div id="ID_CELL_STR_<?=$i;?>_<?=$j;?>" day="" style="padding:3px;"></div>
								</td><?
							}?>
							</tr><?
						}?>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
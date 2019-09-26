<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code	= $_POST['code'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
?>
<script type="text/javascript">
	var evtMouse = 1;

	function lfWeeklyDraw(){
		var year	= __str2num('<?=$year;?>');
		var month	= __str2num('<?=$month;?>')-1;
		var startDt	= new Date(year,month,1);

		$('span[id^="lblWeekly"]').hide();

		if (startDt.getDay() <= 6){
			startDt.setDate(startDt.getDate()-startDt.getDay());
		}

		var startDay = new Date(year, month, 1);
		var monthsDay = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
		var lastDay  = monthsDay[parseInt(month+1, 10)];
		var startWeek = startDay.getDay(); //1일의 요일
		var totalWeek = Math.ceil((lastDay + startWeek) / 7); //총 몇 주인지 구하기
		var weekIdx	= 1;
		var today	= getToday();

		for(var i=0; i<6; i++){
			var weekly	= i+1;
			var tmpDt	= new Date(startDt.valueOf());

			startDt.setDate(startDt.getDate()+6);

			//alert(tmpDt.toLocaleDateString()+'~'+startDt.toLocaleDateString());

			var y1 = tmpDt.getFullYear();
			var m1 = tmpDt.getMonth()+1;
				m1 = (m1 < 10 ? '0' : '')+m1;
			var d1 = tmpDt.getDate();
				d1 = (d1 < 10 ? '0' : '')+d1;

			var y2 = startDt.getFullYear();
			var m2 = startDt.getMonth()+1;
				m2 = (m2 < 10 ? '0' : '')+m2;
			var d2 = startDt.getDate();
				d2 = (d2 < 10 ? '0' : '')+d2;

			if (today >= y1+'-'+m1+'-'+d1 && today <= y2+'-'+m2+'-'+d2){
				weekIdx = weekly;
			}

			if (weekly <= totalWeek){
				$('#lblWeekly'+weekly).attr('week',weekly).attr('fromDt',y1+m1+d1).attr('toDt',y2+m2+d2).text(weekly+'주차').show();
			}

			if (startDt.getMonth() != month) break;

			startDt.setDate(startDt.getDate()+1);
		}

		lfSetWeeklyDt(weekIdx);
	}

	function lfSetWeeklyDt(w){
		var weekly	= new Array('일','월','화','수','목','금','토');
		var color	= new Array('red','black','black','black','black','black','blue');
		var year	= __str2num('<?=$year;?>');
		var month	= __str2num('<?=$month;?>');

		$('span[id^="lblWeekly"]').css('font-weight','');
		$('#lblWeekly'+w).css('font-weight','bold');

		var today	= getToday();
		var date	= __getDate($('#lblWeekly'+w).attr('fromDt'));

		$('div[id^="lblWeek"]').html('');

		for(var i=0; i<7; i++){
			var dt	= addDate('d', i, date);
			var day	= getDay(dt);
			var week= getWeekDay(dt);
			var ym1 = year+'.'+month;
			var ym2	= getYear(dt)+'.'+getMonth(dt);
			var bgClr	= 'ffffff';

			if (today == dt){
				bgClr = 'ffffd9';
			}

			if (ym1 == ym2){
				$('#lblWeek'+i).attr('date',dt).html('<span class="bold">'+day+'</span> (<span style="color:'+color[i]+';">'+weekly[i]+'</span>)');
			}else{
				$('#lblWeek'+i).attr('date',dt).html('<span class="bold" style="color:#a9b3ef;">'+day+'</span> (<span style="color:'+color[i]+';">'+weekly[i]+'</span>)');
			}

			$('td[id^="'+i+'_"]').unbind('mousedown').bind('mousedown',function(){
				var tmp = $(this).attr('id').split('_');
				var week= tmp[0];
				var time= tmp[1];
				var obj	= $('#lblWeek'+week);
				var dt	= $(obj).attr('date');

				if (time == 'fullTime'){
					if (evtMouse == 1){
						_regCalendar(this,'<?=$code;?>',dt,'0','0','popup');
					}
				}else{
					_regCalendar(this,'<?=$code;?>',dt,'0','0','popup',time);
				}
			}).css('background-color',bgClr);
		}

		setTimeout('lfSearch(\''+w+'\')',200);
	}

	function lfSearch(w){
		$.ajax({
			type : 'POST'
		,	url  : './calendar_weekly_search.php'
		,	data : {
				'code'	:'<?=$code;?>'
			,	'from'	:$('#lblWeekly'+w).attr('fromDt')
			,	'to'	:$('#lblWeekly'+w).attr('toDt')
			}
		,	beforeSend: function(){
				$('.FULL').remove();
				$('.CALN').remove();
			}
		,	success: function(data){
				var row = data.split(String.fromCharCode(11));
				var fullCnt = 0;
				var tmpDt	= '';

				for(var i=0; i<row.length; i++){
					var html = '';

					if (row[i]){
						var col = __parseVal(row[i]);

						if (tmpDt != col['dt']){
							tmpDt = col['dt'];
							fullCnt = 0;
						}

						if (col['full'] == 'Y'){
							if (col['use'] == 'Y'){
								var border = '';

								if (fullCnt > 0){
									border = 'border-top:1px solid #efefef;';
								}
								html += '<div class="FULL" style="width:auto; cursor:default; text-align:left;">';
								html += '<div class="bold wraphide" style="width:105; text-align:left; '+border+'"><span onclick="return false;" onmouseup="evtMouse=1; return false;" onmousedown="evtMouse=2; _viewCalendar(this,\'<?=$code;?>\',\''+col['yymm']+'\',\''+col['seq']+'\',\''+col['no']+'\',\'popup\',\''+col['dt']+'\'); return false;">'+col['title']+'</span></div>';
								html += '</div>';

								var tmp = $('#'+col['week']+'_fullTime').html();

								$('#'+col['week']+'_fullTime').html(tmp+html);

								fullCnt ++;
							}
						}else{
							var str	= __str2num(col['from']);
							var h	= Math.floor(str / 60);
							var m	= str % 60;

							if (h > 12){
								str	= '오후 '+(h-12);
							}else{
								str	= '오전 '+h;
							}

							str += '시';

							if (m > 0) str = str + ' ' + m + '분';

							var id	= col['week']+'_'+col['from'];
							html += '<div class="CALN" objId="'+col['id']+'" value="'+col['key']+'" yymm="'+col['yymm']+'" seq="'+col['seq']+'" no="'+col['no']+'" key="'+col['key']+'" cnt="'+col['cnt']+'" dt="'+col['dt']+'" from="'+col['from']+'" to="'+col['to']+'" week="'+col['week']+'" use="'+col['use']+'" tmpW="0" style="position:absolute; z-index:10; left:0px; top:0px; width:auto; height:0px; border:1px solid #467fd0; background-color:#ffffff; display:none; cursor:default;">';
							html += '<div class="wraphide" style="width:auto; height:17px; line-height:1.5em; padding-left:5px; font-size:11px; font-weight:bold; color:white; background-color:#4984d9;">'+str+'</div>';
							html += '<div class="wrapnon" style="line-height:1em; color:white; background-color:6d9de1;">'+col['title']+'</div>';
							html += '</div>';

							$('#tmpDiv').before(html);
						}
					}
				}

				setTimeout('lfSetClan()',100);
			}
		});
	}

	function lfSetClan(){
		$('.CALN').each(function(){
			var cnt		= $(this).attr('cnt');
			var from	= $(this).attr('from');
			var to		= $(this).attr('to');
			var id		= $(this).attr('week')+'_'+from;
			var obj		= $('#'+id);
			var top		= $(obj).offset().top+1;
			var left	= $(obj).offset().left+2;
			var width	= parseInt($(obj).width() * 0.9 / cnt,10);
			var height	= 17;
			var bottom	= $('#'+$(this).attr('week')+'_'+to).offset().top - top - 2;

			var liLeft	= 0;
			var liWidth	= 0;

			$('.CALN[value="'+$(this).attr('key')+'"]').each(function(){
				if ($(this).css('display') != 'none'){
					liLeft += width;
				}else if ($(this).attr('use') == 'N'){
					var tmpId	= $(this).attr('objId').split('TMP_').join('');
					var tmpW	= parseInt($('.CALN[objId="'+tmpId+'"]').attr('tmpW'),10);
					if (!isNaN(tmpW)) liWidth += tmpW;

					var tmpW	= parseInt($('.CALN[objId="'+tmpId+'"]').css('width'),10);
					if (!isNaN(tmpW)) liWidth += tmpW;

					liWidth	+= (cnt % 2 == 1 ? 2 : 1);
				}
			});

			left += __str2num(liLeft + liWidth);

			width -= (cnt % 2 == 1 ? 2 : 1);

			var tmpId = $('#'+$(this).attr('week')+'_'+$(this).attr('from'));

			if (left + width > $(tmpId).offset().left + $(tmpId).width()){
				var tmpW = (left + width) - ($(tmpId).offset().left + $(tmpId).width() * 0.9);

				if (width - tmpW > 0){
					width -= tmpW;
				}

				if (width < 0) width = 0;
			}

			if ($(this).attr('use') == 'Y'){
				$(this)
					.css('top',top)
					.css('left',left)
					.css('width',width)
					.css('height',bottom)
					.attr('tmpW',liWidth).unbind('click').bind('click',function(){
						_viewCalendar(this,'<?=$code;?>',$(this).attr('yymm'),$(this).attr('seq'),$(this).attr('no'),'popup');
					}).show();
				$('div',this).css('width',width-2);
				$('div:last',this).css('height',bottom-19);
			}
		});
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="42px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">주차</th>
			<td class="left last">
				<span id="lblWeekly1" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(1);" style="cursor:default;"></span>
				<span id="lblWeekly2" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(2);" style="padding-left:10px; cursor:default;"></span>
				<span id="lblWeekly3" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(3);" style="padding-left:10px; cursor:default;"></span>
				<span id="lblWeekly4" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(4);" style="padding-left:10px; cursor:default;"></span>
				<span id="lblWeekly5" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(5);" style="padding-left:10px; cursor:default;"></span>
				<span id="lblWeekly6" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(6);" style="padding-left:10px; cursor:default;"></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="110px" span="7">
	</colgroup>
	<thead>
		<tr>
			<th class="head">시간</th>
			<th class="head"><div id="lblWeek0" date=""></div></th>
			<th class="head"><div id="lblWeek1" date=""></div></th>
			<th class="head"><div id="lblWeek2" date=""></div></th>
			<th class="head"><div id="lblWeek3" date=""></div></th>
			<th class="head"><div id="lblWeek4" date=""></div></th>
			<th class="head"><div id="lblWeek5" date=""></div></th>
			<th class="head last"><div id="lblWeek6" date=""></div></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center">종일</th>
			<td id="0_fullTime" class="center top"></td>
			<td id="1_fullTime" class="center top"></td>
			<td id="2_fullTime" class="center top"></td>
			<td id="3_fullTime" class="center top"></td>
			<td id="4_fullTime" class="center top"></td>
			<td id="5_fullTime" class="center top"></td>
			<td id="6_fullTime" class="center top last"></td>
		</tr><?
		for($t=0;$t<24;$t++){
			if ($t == 0){
				$timeStr = '오전<br>12시';
			}else if ($t == 12){
				$timeStr = '오후<br>12시';
			}else{
				$timeStr = ($t > 12 ? $t - 12 : $t).'시';
			}

			for($l=0; $l<2; $l++){
				$hm = $t * 60 + $l * 30;?>
				<tr><?
					if ($l == 0){?>
						<th class="right" rowspan="2"><?=$timeStr;?></th><?
						$border = 'border-bottom:1px dashed #cccccc;';
					}else{
						$border = 'border-bottom:1px solid #cccccc;';
					}?>
					<td id="0_<?=$hm;?>" class="center" style="border-top:none; <?=$border;?>"></td>
					<td id="1_<?=$hm;?>" class="center" style="border-top:none; <?=$border;?>"></td>
					<td id="2_<?=$hm;?>" class="center" style="border-top:none; <?=$border;?>"></td>
					<td id="3_<?=$hm;?>" class="center" style="border-top:none; <?=$border;?>"></td>
					<td id="4_<?=$hm;?>" class="center" style="border-top:none; <?=$border;?>"></td>
					<td id="5_<?=$hm;?>" class="center" style="border-top:none; <?=$border;?>"></td>
					<td id="6_<?=$hm;?>" class="center last" style="border-top:none; <?=$border;?>"></td>
				</tr><?
			}
		}?>
	</tbody>
</table>
<div id="tmpDiv"></div>
<?
	include_once('../inc/_db_close.php');
?>
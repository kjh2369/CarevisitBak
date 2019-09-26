<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= Date('m');

?>
<script type="text/javascript">
	$(document).ready(function(){
		lfWeeklyDraw();

		$('input:checkbox[name="chkSvc"]').unbind('click').bind('click',function(){
			var key = $(this).attr('id');
			var chk = $(this).attr('checked');

			$('input:checkbox[id^="'+key+'"]').attr('checked',chk);
		});

		$('#chkSvc').attr('checked',true).click().attr('checked',true);
	});

	function lfWeeklyDraw(){
		var year	= __str2num($('#lblYYMM').attr('year'));
		var month	= __str2num($('#lblYYMM').attr('month'))-1;
		var startDt	= new Date(year,month,1);
		
		
		$('span[id^="lblWeekly"]').hide();

		if (startDt.getDay() <= 6){
			startDt.setDate(startDt.getDate()-startDt.getDay());
		}

		var startDay = new Date(year, month, 1);
		var monthsDay = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
		var lastDay  = monthsDay[parseInt(month, 10)];
		var startWeek = startDay.getDay(); //1일의 요일
		var totalWeek = Math.ceil((lastDay + startWeek) / 7); //총 몇 주인지 구하기
		
		for(var i=0; i<6; i++){
			var weekly	= i+1;
			var tmpDt	= new Date(startDt.valueOf());

			startDt.setDate(startDt.getDate()+6);

			//if ('<?=$debug;?>' == '1') alert(tmpDt.toLocaleDateString()+'~'+startDt.toLocaleDateString());

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

			if (i == 0 && y1+'-'+m1 < y2+'-'+m2){
				y1 = y2;
				m1 = m2;
				d1 = '01';
			}else if (i < 6 && y1+'-'+m1 < y2+'-'+m2){
				y2 = y1;
				m2 = m1;
				d2 = __getLastDay(y1,m1);
				d2 = (d2 < 10 ? '0' : '')+d2;
			}
			
			
			if(weekly <= totalWeek){
				$('#lblWeekly'+weekly).attr('week',weekly).attr('fromDt',y1+m1+d1).attr('toDt',y2+m2+d2).html('<a href="#" onclick="return false;">'+weekly+'주차</a>').show();
			}

			if (startDt.getMonth() != month) break;

			startDt.setDate(startDt.getDate()+1);
		}

		lfSetWeeklyDt(1);
	}

	function lfSetWeeklyDt(w){
		var weekly	= new Array('일','월','화','수','목','금','토');
		var color	= new Array('red','black','black','black','black','black','blue');
		var year	= __str2num($('#lblYYMM').attr('year'));
		var month	= __str2num($('#lblYYMM').attr('month')) - 1;

		$('span[id^="lblWeekly"]').css('color','black');
		$('#lblWeekly'+w).css('color','blue');
		$('#lblFromDt').text(__getDate($('#lblWeekly'+w).attr('fromDt'),'.'));
		$('#lblDtGbn').text('~');
		$('#lblToDt').text(__getDate($('#lblWeekly'+w).attr('toDt'),'.'));

		var fromDay = __str2num($('#lblWeekly'+w).attr('fromDt').substring(6,8));
		var toDay	= __str2num($('#lblWeekly'+w).attr('toDt').substring(6,8));

		$('div[id^="lblWeek"]').html('');

		for(var i=fromDay; i<=toDay; i++){
			var tmpDt	= new Date(year,month,i);
			var week	= tmpDt.getDay();

			$('#lblWeek'+week).html('<span class="bold">'+i+'</span> (<span style="color:'+color[week]+';">'+weekly[week]+'</span>)');
		}

		$('#lblIljung0').html('');
		$('#lblIljung1').html('');
		$('#lblIljung2').html('');
		$('#lblIljung3').html('');
		$('#lblIljung4').html('');
		$('#lblIljung5').html('');
		$('#lblIljung6').html('');

		setTimeout('lfSearch()',200);
	}

	function lfGetSvc(){
		var chkSvc = '';

		$('input:checkbox[name="chkSvc"]').each(function(){
			if ($(this).attr('checked') && $(this).val() != 'on'){
				var key  = $(this).val().split('H_').join('').split('V_').join('').split('C_').join('').split('O_').join('');
					key += String.fromCharCode(1);

				chkSvc += key;
			}
		});

		return chkSvc;
	}

	//출력옵션(서비스명출력여부)
	function chkSvc(){
		var svcChk = document.getElementById('svcChk').checked;
		var fontSize1 = 	document.getElementById('fontSize1');
		var fontSize2 = 	document.getElementById('fontSize2');

		if(svcChk == true){
			fontSize1.style.display = '';
			fontSize2.style.display = 'none';
		}else {
			fontSize1.style.display = 'none';
			fontSize2.style.display = '';
		}

	}

	function lfSearch(){
		$.ajax({
			type : 'POST'
		,	url  : './iljung_weekly_search.php'
		,	data : {
				'from':$('#lblFromDt').text()
			,	'to':$('#lblToDt').text()
			,	'svc':lfGetSvc()
			}
		,	beforeSend: function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success: function(data){
				var row = data.split(String.fromCharCode(11));
				var html = {};

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var brd = '';


						if (!html[col['week']]){
							html[col['week']] = '';
						}else{
							brd = 'border-top:1px dotted #999999;';
						}

						html[col['week']] += '<div class="clean" style="line-height:1.5em; '+brd+'">';
						html[col['week']] += '<div class="left">'+col['svcNm']+'</div>';
						html[col['week']] += '<div>';
						html[col['week']] += '<div class="left" style="float:left; width:auto;">'+__styleTime(col['from'])+'~'+__styleTime(col['to'])+'</div>';
						html[col['week']] += '<div class="right nowrap" style="float:right; width:50px;">'+col['name']+'</div>';
						html[col['week']] += '</div>';
						html[col['week']] += '<div class="left">'+col['mem']+'</div>';
						html[col['week']] += '</div>';
					}
				}

				$('#lblIljung0').html(html['0']);
				$('#lblIljung1').html(html['1']);
				$('#lblIljung2').html(html['2']);
				$('#lblIljung3').html(html['3']);
				$('#lblIljung4').html(html['4']);
				$('#lblIljung5').html(html['5']);
				$('#lblIljung6').html(html['6']);
				$('#tempLodingBar').remove();
			}
		});
	}


	function lfShowWeekly(){

		var fromDt  = $('#lblFromDt').text();
		var toDt    = $('#lblToDt').text();
		var svc  = lfGetSvc();
		var svcChk = document.getElementById('svcChk').checked;

		if(svcChk == true){
			var fontSize = 	document.getElementById('fontSize1').value;
		}else {
			var fontSize = 	document.getElementById('fontSize2').value;
		}
		var para = 'root=iljung'
				 + '&fileName=iljung_weekly'
				 + '&fileType=pdf'
				 + '&target=show.php'
				 + '&showForm=ILJUNG_WEEKLY'
				 + '&from='+fromDt
				 + '&to='+toDt
				 + '&svc='+svc
				 + '&svcChk='+svcChk
				 + '&fontSize='+fontSize
				 + '&param=';

		__printPDF(para);
	}

</script>
<div class="title title_border">주간별일정조회/출력</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="600px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">년월</th>
			<td class="left">
				<div style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="left"><? echo $myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM")); lfWeeklyDraw();');?></td>
			<td class="center last" rowspan="3">
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfShowWeekly();">출력</button></span><br>
				<!--span class="btn_pack m"><span class="excel"></span><button type="button" onclick="">출력</button></span-->
			</td>
		</tr>
		<tr>
			<th class="head">주차</th>
			<td class="left" colspan="2">
				<span id="lblWeekly1" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(1);"></span>
				<span id="lblWeekly2" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(2);" style="padding-left:10px;"></span>
				<span id="lblWeekly3" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(3);" style="padding-left:10px;"></span>
				<span id="lblWeekly4" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(4);" style="padding-left:10px;"></span>
				<span id="lblWeekly5" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(5);" style="padding-left:10px;"></span>
				<span id="lblWeekly6" week="" fromDt="" toDt="" onclick="lfSetWeeklyDt(6);" style="padding-left:10px;"></span>
			</td>
		</tr>
		<tr>
			<th class="head">기간</th>
			<td class="left bold" colspan="2">
				<span id="lblFromDt"></span>
				<span id="lblDtGbn" style="padding:0 3px 0 3px;"></span>
				<span id="lblToDt"></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<tr>
		<th>전체</th>
		<td class="last" colspan="5">
			<input id="chkSvc" name="chkSvc" type="checkbox" class="checkbox"><label for="chkSvc">전체</label>
		</td>
	</tr><?
	$laKind = $conn->svcKindSort($code, $gHostSvc['voucher']);

	foreach($laKind as $svcIdx => $svcKind){
		if ($svcIdx == 'H'){
			if ($type == 105) continue;
			$lsSvcName = '재가요양';
		}else if ($svcIdx == 'V'){
			if ($type == 105) continue;
			$lsSvcName = '바우처';
		}else if ($svcIdx == 'C'){
			if ($type != 105) continue;
			if ($sr == 'S')
				$lsSvcName = '재가지원';
			else
				$lsSvcName = '자원연계';
		}else{
			if ($type == 105) continue;
			$lsSvcName = '기타유료';
		}

		$key = 'chkSvc_'.$svcIdx;?>
		<tr>
			<th><input id="<?=$key;?>" name="chkSvc" type="checkbox" class="checkbox" style="margin-left:0;"><label for="<?=$key;?>"><?=$lsSvcName;?></label></th>
			<td class="last" colspan="5"><?
			foreach($svcKind as $subIdx => $subKind){
				$key1 = $key.'_'.$subKind['code'];

				if (Is_Array($subKind['sub'])){
					$lbFirst = true;

					foreach($subKind['sub'] as $svcSubKindCd => $svcSubKindNm){
						$key2 = $key1.'_'.$svcSubKindCd;

						if ($svcIdx == 'V' && $lbFirst){?>
							<div style="float:left; width:auto; height:100%; margin-left:5px; padding-right:5px; background-color:#f7faff; border-left:1px solid #a6c0f3; border-right:1px solid #a6c0f3;">
								<label><input id="<?=$key1;?>" name="chkSvc" type="checkbox" class="checkbox"><?=$subKind['name'];?></label>
							</div><?
						}?>
						<div style="float:left; width:auto;"><label><input id="<?=$key2;?>" name="chkSvc" type="checkbox" value="<?=$svcIdx.'_'.$subKind['code'].'_'.$svcSubKindCd;?>" class="checkbox"><?=$svcSubKindNm;?></label></div><?

						$lbFirst = false;
					}
				}else{?>
					<div style="float:left; width:auto;"><label><input id="<?=$key1;?>" name="chkSvc" type="checkbox" value="<?=$svcIdx.'_'.$subKind['code'];?>" class="checkbox"><?=$subKind['name'];?></label></div><?
				}
			}?>
			</td>
		</tr><?
	}?>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="92px">
		<col width="150px">
		<col width="85px">
		<col width="*">
	</colgroup>
	<tr>
		<th>출력옵션</th>
		<td >
			<input id="svcChk"  type="checkbox" class="checkbox" onclick="chkSvc();" checked><label for="svcChk">서비스명출력여부 </label></input>
		</td>
		<th class="head">기본폰트크기</th>
		<td class="last">
			<!--서비스명 출력일 때-->
			<select name="fontSize1" id="fontSize1" style="width:auto;">
				<option value="5">5pt
				<option value="7">7pt
				<option value="9" selected>9pt
				<option value="11">11pt
			</select>

			<!--서비스명 출력 안할 때-->
			<select name="fontSize2" id="fontSize2" style="width:auto; display:none;">
				<option value="5">5pt
				<option value="7" selected>7pt
			</select>

		</td>
	</tr>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="<?=100/7;?>%" span="7">
	</colgroup>
	<thead>
		<tr>
			<th class="head"><div id="lblWeek0"></div></th>
			<th class="head"><div id="lblWeek1"></div></th>
			<th class="head"><div id="lblWeek2"></div></th>
			<th class="head"><div id="lblWeek3"></div></th>
			<th class="head"><div id="lblWeek4"></div></th>
			<th class="head"><div id="lblWeek5"></div></th>
			<th class="head last"><div id="lblWeek6"></div></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top"><div id="lblIljung0"></div></td>
			<td class="center top"><div id="lblIljung1"></div></td>
			<td class="center top"><div id="lblIljung2"></div></td>
			<td class="center top"><div id="lblIljung3"></div></td>
			<td class="center top"><div id="lblIljung4"></div></td>
			<td class="center top"><div id="lblIljung5"></div></td>
			<td class="center top last"><div id="lblIljung6"></div></td>
		</tr>
	</tbody>
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
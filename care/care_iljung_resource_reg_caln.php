<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$suga	= $_POST['suga'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$from	= $_POST['from'];
	$to		= $_POST['to'];

	//휴일리스트
	$sql = 'select cast(substring(mdate,7) as unsigned) as day
			,      holiday_name as nm
			  from tbl_holiday
			 where left(mdate,6) = \''.$year.$month.'\'';
	$loHolidayList = $conn->_fetch_array($sql,'day');
?>
<style>
	.divCalCont{
		width:100%;
		height:17px;
		line-height:17px;
	}
</style>
<script type="text/javascript">
	function lfDaySel(obj){
		var isYn   = $(obj).attr('value');
		var weekly = $(obj).attr('weekly');

		if (isYn == 'Y')
			isYn = 'N';
		else
			isYn = 'Y';

		var bold  = 'normal';
		var bgClr = '#ffffff';

		if (isYn == 'Y'){
			bold  = 'bold';
			bgClr = '#fffabb';
		}

		$(obj).attr('value', isYn).css('font-weight',bold).css('background-color',bgClr);
	}

	function lfWeekSel(obj){
		var isYn   = $(obj).attr('value');
		var weekly = $(obj).attr('weekly');

		if (isYn == 'Y')
			isYn = 'N';
		else
			isYn = 'Y';

		var bold  = 'normal';
		var bgClr = '#ffffff';

		if (isYn == 'Y'){
			bold  = 'bold';
			bgClr = '#fffabb';
		}

		$(obj).attr('value', isYn);
		$('.clsWeek_'+weekly, $('#tblAssignCal')).attr('value', isYn).css('font-weight',bold).css('background-color',bgClr);
	}

	function lfAllSel(asSel){
		var clr  = '';
		var lsChk = asSel;
		var ynChk = 'N';

		if (lsChk == 'A' || lsChk == 'X'){
			for(var i=0; i<7; i++){
				if (lsChk == 'A')
					ynChk = 'Y';
				else
					ynChk = 'N';

				$('#weekday_'+i, $('#tblAssignCal')).attr('value', (ynChk == 'Y' ? 'Y' : 'N'));
				$('.clsWeek_'+i, $('#tblAssignCal')).attr('value', (ynChk == 'Y' ? 'Y' : 'N')).css('font-weight', (ynChk == 'Y' ? 'bold' : 'normal')).css('background-color', (ynChk == 'Y' ? '#fffabb' : '#ffffff'));
			}
		}else{
			$('div[id^="txtDay_"]').attr('value', 'N').css('font-weight','normal').css('background-color', '#ffffff');
			if (lsChk == 'W'){
				$('div[id^="txtDay_"][weekly!="0"]').attr('value', 'Y').css('font-weight','bold').css('background-color', '#fffabb');
			}else{
				$('div[id^="txtDay_"][weekly="0"]').attr('value', 'Y').css('font-weight','bold').css('background-color', '#fffabb');
			}
		}
	}

	function lfAssign(){
		if (!$('#lblResource').attr('code')){
			alert('자원을 선택하여 주십시오.');
			lfResourceFind();
			return;
		}
		
		if (!$('#lblMemName').attr('jumin')){
			alert('담당직원을 선택하여 주십시오.');
			lfMemberFind();
			return;
		}

		
		if (!$('#txtFromH').val() || !$('#txtFromM').val()){
			alert('시작시간을 입력하여 주심시오.');
			if (!$('#txtFromH').val()){
				$('#txtFromH').focus();
				return;
			}
			if (!$('#txtFromM').val()){
				$('#txtFromM').focus();
				return;
			}
		}

		var liDayCnt = $('div[id^="txtDay_"][value="Y"]').length;

		if (liDayCnt == 0){
			alert('배정할 일자를 선택하여 주십시오.');
			return;
		}

		var liClientCnt = $('input:checkbox[name="chk"]:checked').length;

		if (liClientCnt == 0){
			alert('대상자를 선택하여 주십시오.');
			return;
		}

		var lsClient = '';

		liClientCnt = 0;

		$('input:checkbox[name="chk"]:checked').each(function(){
			var jumin = $(this).val();

			if (jumin){
				//lsClient += ((lsClient ? String.fromCharCode(11) : '') + jumin);
				lsClient += ((lsClient ? '_TAB_' : '') + jumin);
				liClientCnt ++;
			}
		});

		$('#loLoading').html(__get_loading());

		var l = ($(window).width() - $('#loLoading').width()) / 2;
		var t = ($(window).height() - $('#loLoading').height()) / 2;

		$('#loLoading').css('left', l).css('top',t).show();

		//배정
		loMstObj = $('div[id^="txtDay_"][value="Y"]');

		var sugaCd      = $('#lblResource').attr('code')
		,	sugaNm      = $('#lblResource').text()
		,	sugaCost    = __str2num($('#lblResource').attr('cost'))

		$(loMstObj).each(function(){
			var day = __str2num($(this).text());
			var obj = $('#loCal_'+day);
			var cnt = $('.clsCal', $(obj)).length + $('.clsGrp', $(obj)).length;
			var week = $(this).attr('week');

			var lsStyle  = 'clear:both; text-align:left; padding-left:3px;';
				lsStyle += 'border-top:1px dotted #666666;';

			//시간
			var lsTime = $('#txtFromH').val()+':'+$('#txtFromM').val();
			var lsResourceNm = $('#lblResource').text();
			var lsResourceCd = $('#lblResource').attr('code');
			var lsMemNm = $('#lblMemName').text();
			var lsMemCd = $('#lblMemName').attr('jumin');
			var lsDt = $('#year').val()+'-'+$('#month').val()+'-'+(day < 10 ? '0' : '')+day;
			var lsSvcKind = $('#sr').val();
			var lsSugaNm = sugaNm;
			var lsSugaCd = sugaCd;
			var liDuplicate = 1;

			/*
			$('div[id^="loCal_'+day+'"]').each(function(){
				alert(day+'\n'+$(this).attr('from')+'/'+lsTime+'\n'+$(this).attr('resourceCd')+'/'+lsResourceCd);
				if ($(this).attr('from') == lsTime &&
					$(this).attr('resourceCd') == lsResourceCd){
					liDuplicate = 9;
					return false;
				}
			});
			*/

			var html = '<div id="loCal_'+day+'_'+cnt+'" class="clsCal" style="'+lsStyle+'" onclick="" onmouseover="lfMouseOver(this);" onmouseout="lfMouseOut(this);"'
					 + ' day		="'+day+'"'
					 + ' cnt		="'+cnt+'"'
					 + ' week		="'+week+'"'
					 + ' svcKind	="'+lsSvcKind+'"'
					 + ' from		="'+lsTime+'"'
					 + ' resourceCd	="'+lsResourceCd+'"'
					 + ' resourceNm	="'+lsResourceNm+'"'
					 + ' memCd		="'+lsMemCd+'"'
					 + ' memNm		="'+lsMemNm+'"'
					 + ' sugaName	="'+sugaNm+'"'
					 + ' sugaCd		="'+lsSugaCd+'"'
					 + ' sugaNm		="'+lsSugaNm+'"'
					 + ' cost		="'+sugaCost+'"'
					 + ' client		="'+lsClient+'"'
					 + ' duplicate	="'+liDuplicate+'"'
					 + ' ynAddRow	="N"'
					 + ' ynSave		="N"'
					 + ' stat		="9"'
					 + ' seq		="'+cnt+'"'
					 + ' svcSeq		=""'
					 + '>'
					 + '<div class="divCalCont" style="font-weight:bold; cursor:default;">'
					 + '	<div id="btnRemove" style="float:right; width:auto; margin-right:3px;"><img src="../image/btn_close.gif" onclick="return lfCalRemove(\''+day+'\',\''+cnt+'\');" style="margin-top:3px;"></div>'
					 + '	<div id="lblTimeStr" style="float:left; width:auto; cursor:default;">'+lsTime+'</div>'
					 + '</div>';

			if (lsMemNm){
				html += '<div id="lblMemStr" class="divCalCont" style="cursor:default;">'+lsMemNm+'</div>';
			}

			html	+= '<div class="divCalCont" style="cursor:default;">대상자 : '+liClientCnt+'명</div>';
			html	+= '<div id="lblSugaStr" class="divCalCont" style="cursor:default;">'
					+  '<div style="float:left; width:auto; margin-right:5px; margin-top:-1px;">'+lsSugaNm+'</div>';
			html	+= '</div>';
			html	+= '<div class="divCalCont" style="display:'+(liDuplicate == 1 ? 'none' : '')+';"><span id="divErrorMsg" style="color:#ff0000; font-size:11px; font-weight:bold; cursor:default;">일정중복</span></div>';
			html	+= '</div>';

			if (cnt > 0){
				if ($('.clsGrp:last', $(obj)).length > 0){
					$('.clsGrp:last', $(obj)).after(html);
				}else if ($('.clsCal:last', $(obj)).length > 0){
					$('.clsCal:last', $(obj)).after(html);
				}else{
					$(obj).html(html);
				}
			}else{
				$(obj).html(html);
			}

			$('.clsCal:first', $(obj)).css('border-top','none');
		});

		$('#loLoading').hide();
	}
	

	//일자 더블클릭 시 배정
	function lfPlanAssign(obj){
		lfAllSel('X');
		lfDaySel(obj);
		lfAssign();
	}



	//일정삭제
	function lfCalRemove(aiDay,aiCnt){
		
		var loObj = $('#loCal_'+aiDay+'_'+aiCnt);
		var loParent = $(loObj).parent();
		
		if('<?=$lsIljungSave;?>'=='1'){
			if (!confirm('삭제후 복구가 불가능합니다.\n삭제를 진행하시겠습니까?')){
				$('#loCal_'+asDay+'_'+asCnt).attr('flag1','1');
				return false;
			}

			//삭제진행
			$.ajax({
				type : 'POST'
			,	url  : './care_iljung_resource_reg_each_delete.php'
			,	data : {
					'sr'	:$('#sr').val()
				,	'suga'	:$('#suga').val()
				,	'year'	:$('#year').val()
				,	'month'	:$('#month').val()
				,	'day'   :$(loObj).attr('day')
				,	'time'  :$(loObj).attr('from')
				,	'resourceCd':$(loObj).attr('resourceCd')
				,	'memCd' :$(loObj).attr('memCd')
				,   'svcKind':$(loObj).attr('svcKind')
				}
			,	success: function(result){
					if (result == 1){
					}else if (result == 9){
						alert('일정삭제중 오류가 발생하였습니다.\n잠시후 다시 시도하여 주십시오.');
					}else{
						alert(result);
					}
				}
			});
		}
		
		
		$(loObj).remove();
		$('.clsCal:first', $(loParent)).css('border-top','none');

		return false;
		

		//return lfCalRemove(asDay,asCnt);
		
	}

</script>
<table id="tblAssignCal" class="my_table" style="width:auto;">
	<colgroup>
		<col width="36px" span="7">
	</colgroup>
	<tbody>
		<tr>
			<td class="center last" colspan="7">
				<div class="left" style="float:left; width:auto;">
					<img src="../iljung/img/btn_calen1.gif" onclick="lfAllSel('A');" alt="전체선택">
					<img src="../iljung/img/btn_calen2.gif" onclick="lfAllSel('W');" alt="평일선택">
					<img src="../iljung/img/btn_calen3.gif" onclick="lfAllSel('H');" alt="휴일선택">
					<img src="../iljung/img/btn_calen4.gif" onclick="lfAllSel('X');" alt="전택해제">
				</div>
				<div class="right" style="float:right; width:auto;">
					<img src="../iljung/img/btn_calen6.gif" onclick="lfAssign();" alt="배정">
				</div>
			</td>
		</tr>
		<tr>
			<th class="center"><div id="weekday_0" onclick="lfWeekSel(this);" value="N" weekly="0" style="cursor:default; color:ff0000;">일</div></th>
			<th class="center"><div id="weekday_1" onclick="lfWeekSel(this);" value="N" weekly="1" style="cursor:default; color:000000;">월</div></th>
			<th class="center"><div id="weekday_2" onclick="lfWeekSel(this);" value="N" weekly="2" style="cursor:default; color:000000;">화</div></th>
			<th class="center"><div id="weekday_3" onclick="lfWeekSel(this);" value="N" weekly="3" style="cursor:default; color:000000;">수</div></th>
			<th class="center"><div id="weekday_4" onclick="lfWeekSel(this);" value="N" weekly="4" style="cursor:default; color:000000;">목</div></th>
			<th class="center"><div id="weekday_5" onclick="lfWeekSel(this);" value="N" weekly="5" style="cursor:default; color:000000;">금</div></th>
			<th class="center last"><div id="weekday_6" onclick="lfWeekSel(this);" value="N" weekly="6" style="cursor:default; color:0000ff;">토</div></th>
		</tr><?
		$liFirstWeekly = date('w', strtotime($year.$month.'01'));
		$liLastDay = intval($myF->dateAdd('day', -1, $myF->dateAdd('month', 1, $year.$month.'01', 'Y-m-d'), 'd'));
		$liChkWeek = ceil(($liLastDay + $liFirstWeekly) / 7);
		$liWeekday = 0;
		$lbLastWeek= false;

		for($i=0; $i<$liFirstWeekly; $i++){
			if ($liWeekday % 7 == 0){?>
				<tr><?
			}?>
			<td class="center">&nbsp;</td><?
			$liWeekday ++;
		}

		if ($liWeekday > 0)
			$liWeekly = 1;
		else
			$liWeekly = 0;

		$liWeekGbn	= 0;

		for($i=1; $i<=$liLastDay; $i++){
			if ($liWeekday % 7 == 0){
				$liWeekday = 0;
				$liWeekly ++;

				if ($liChkWeek <= $liWeekly) $lbLastWeek = true;

				if ($liFirstWeekly != 0){?>
					</tr><?
				}?>
				<tr><?
			}

			if (!empty($loHolidayList[$i]['nm'])){
				$liWeekIdx = 0;
				$lsFontClr = '#ff0000';
			}else{
				$liWeekIdx = $liWeekday;

				switch($liWeekday){
					case 0: $lsFontClr = '#ff0000'; break;
					case 6: $lsFontClr = '#0000ff'; break;
					default: $lsFontClr = '#000000';
				}
			}

			$lsDt  = $year.$month.($i < 10 ? '0' : '').$i;
			$lbAdd = false;

			if (Date('w',StrToTime($lsDt)) == 1){
				$liWeekGbn ++;
			}

			if ($from <= $lsDt && $to >= $lsDt){
				$lbAdd = true;
			}
			
			
			?>
			<td class="center <?=$liWeekday == 6 ? 'last' : '';?> <?=$lbLastWeek ? 'bottom' : '';?>"><?
			if ($lbAdd){?>
				<div id="txtDay_<?=$i;?>" value="N" week="<?=$liWeekGbn;?>" weekly="<?=$liWeekIdx;?>" class="clsWeek_<?=$liWeekday;?>" style="cursor:default; font-weight:normal; background-color:#ffffff; color:<?=$lsFontClr;?>;" onclick="lfDaySel(this);" ondblclick="lfPlanAssign(this);" ><?=$i;?></div><?
			}else{?>
				<div id="tmpDay_0" value="N" week="<?=$liWeekGbn;?>" weekly="<?=$liWeekIdx;?>" class="" style="cursor:default; background-color:#efefef; color:#cccccc;"><?=$i;?></div><?
			}?>
			</td><?
			$liWeekday ++;
		}

		if ($liWeekday % 7 == 0){?>
			</tr><?
		}else{
			for($i=$liWeekday+1; $i<=7; $i++){?>
				<td class="center <?=$liWeekday == 6 ? 'last' : '';?> <?=$lbLastWeek ? 'bottom' : '';?>">&nbsp;</td><?
				$liWeekday ++;
			}?>
			</tr><?
		}?>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
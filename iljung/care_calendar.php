<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$year = $_POST['year'];
	$month = $_POST['month'];

	//직원입사일자
	$sql = 'SELECT	DATE_FORMAT(MIN(mem_his.join_dt),\'%Y%m%d\') AS from_dt
			,		DATE_FORMAT(MAX(IFNULL(mem_his.quit_dt,\'9999-12-31\')),\'%Y%m%d\') AS to_dt
			FROM	mem_his
			WHERE	org_no = \''.$code.'\'
			AND		jumin = \''.$jumin.'\'
			AND		DATE_FORMAT(join_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(IFNULL(quit_dt,\'9999-12-31\'),\'%Y%m\') >= \''.$year.$month.'\'';

	$period = $conn->get_array($sql);
?>
<script type='text/javascript' src='./plan.js'></script>
<script type="text/javascript">
	function lfAssign(){
		if (!$('#txtClient').attr('jumin')){
			alert('고객을 선택하여 주십시오.');
			lfFindClient();
			return false;
		}

		if (!checkDate($('#txtFromH').val()+':'+$('#txtFromM').val())){
			alert('방문시간 입력오류 입니다.');
			$('#txtFromH').focus();
			return false;
		}

		if (!checkDate($('#txtToH').val()+':'+$('#txtToM').val())){
			alert('방문시간 입력오류 입니다.');
			$('#txtFromH').focus();
			return false;
		}

		$('div[id^="txtDay_"][value="Y"]').each(function(){
			var day = $(this).text();
			var obj = $('#loCal_'+day);
			var from1 = $('#txtFromH').val()+':'+$('#txtFromM').val();
			var to1 = $('#txtToH').val()+':'+$('#txtToM').val();
			var proc = $('#lblProc').text();
			var gbn = lfDuplicate(day,'',__time2min(from1),__time2min(to1));

			var name = $('#txtClient').val();
			var jumin = $('#txtClient').attr('jumin');

			var cnt = $('div[id^="loCal_'+day+'_"]').length + 1;
			var lsStyle  = 'clear:both; text-align:left; padding-left:3px;';
				lsStyle += 'border-top:1px dotted #666666;';
			var lsTime = from1+'~'+to1;
			var lsMem = name;
			var lsSugaNm = '';

			var html = '<div id="loCal_'+day+'_'+cnt+'" class="clsCal" style="'+lsStyle+'" onmouseover="_planMouseOver(this);" onmouseout="_planMouseOut(this);"'
					 + ' day="'+day+'"'
					 + ' cnt="'+cnt+'"'
					 + ' jumin="'+jumin+'"'
					 + ' from="'+from1+'"'
					 + ' to="'+to1+'"'
					 + ' proc="'+proc+'"'
					 + ' duplicate="'+gbn+'"'
					 + ' seq="'+cnt+'"'
					 + ' stat="9"'
					 + '>'
					 + '<div class="divCalCont" style="font-weight:bold; cursor:default; line-height:1.2em;">'
					 + '	<div id="btnRemove" style="float:right; width:auto; margin-right:3px;"><img src="../image/btn_close.gif" onclick="return lfCalRemove($(this).parent().parent().parent());" style="margin-top:3px;"></div>'
					 + '	<div id="lblTimeStr" style="float:left; width:auto; cursor:default;">'+lsTime+'</div>'
					 + '</div>'
					 + '<div id="lblMemStr" class="divCalCont" style="cursor:default; line-height:1.2em;">'+lsMem+'</div>'
					 + '<div id="lblSugaStr" class="divCalCont" style="cursor:default; line-height:1.2em;"><div style="float:left; width:auto; margin-right:5px; margin-top:-1px;">상담지원</div></div>';
			html += '<div class="divCalCont" style=""><span id="divErrorMsg" style="color:#ff0000; font-size:11px; font-weight:bold; cursor:default; display:'+(gbn == 1 ? 'none' : '')+';">일정중복</span></div>';
			html += '</div>';

			if (cnt > 1){
				$('.clsCal:last', $(obj)).after(html);
			}else{
				$(obj).html(html);
			}

			$('.clsCal:first', $(obj)).css('border-top','none');
		});
	}

	function lfDuplicate(day,id1,from1,to1){
		var gbn = 1;

		$('div[id^="loCal_'+day+'_"]').each(function(){
			var id2 = $(this).attr('id');
			var from2 = __time2min($(this).attr('from'));
			var to2 = __time2min($(this).attr('to'));

			if (id1 == id2){
			}else{
				if ((from1 <= from2 && to1 > from2) ||
					(from1 < to2 && to1 >= to2) ||
					(from1 > from2 && to1 < to2)){
					gbn = 91; //시간중복
					return false;
				}
			}
		});

		return gbn;
	}

	function lfCalRemove(obj){
		var parent = $(obj).parent();
		var day = $(obj).attr('day');

		$(obj).remove();

		$('div[id^="loCal_'+day+'_"]').each(function(){
			var id = $(this).attr('id');
			var from = __time2min($(this).attr('from'));
			var to = __time2min($(this).attr('to'));
			var gbn = lfDuplicate(day,id,from,to);

			if (gbn == 1){
				$('#divErrorMsg',this).hide();
			}else{
				$('#divErrorMsg',this).show();
			}

			$(this).attr('duplicate',gbn);
			$('.clsCal:first',$(this).parent()).css('border-top','none');
		});
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
					<img src="./img/btn_calen1.gif" onclick="_planAssingAll('A');" alt="전체선택">
					<img src="./img/btn_calen2.gif" onclick="_planAssingAll('W');" alt="평일선택">
					<img src="./img/btn_calen3.gif" onclick="_planAssingAll('H');" alt="휴일선택">
					<img src="./img/btn_calen4.gif" onclick="_planAssingAll('X');" alt="전택해제">
				</div>
				<div class="right" style="float:right; width:auto;">
					<div id="btnAssign1">
						<img src="./img/btn_calen5.gif" onclick="lfGetPattern(this);" alt="패턴리스트">
						<img src="./img/btn_calen6.gif" onclick="lfAssign();" alt="배정">
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<th class="center"><div id="weekday_0" onclick="_planAssignWeekSel(this);" value="N" weekly="0" style="cursor:default; color:ff0000;">일</div></th>
			<th class="center"><div id="weekday_1" onclick="_planAssignWeekSel(this);" value="N" weekly="1" style="cursor:default; color:000000;">월</div></th>
			<th class="center"><div id="weekday_2" onclick="_planAssignWeekSel(this);" value="N" weekly="2" style="cursor:default; color:000000;">화</div></th>
			<th class="center"><div id="weekday_3" onclick="_planAssignWeekSel(this);" value="N" weekly="3" style="cursor:default; color:000000;">수</div></th>
			<th class="center"><div id="weekday_4" onclick="_planAssignWeekSel(this);" value="N" weekly="4" style="cursor:default; color:000000;">목</div></th>
			<th class="center"><div id="weekday_5" onclick="_planAssignWeekSel(this);" value="N" weekly="5" style="cursor:default; color:000000;">금</div></th>
			<th class="center last"><div id="weekday_6" onclick="_planAssignWeekSel(this);" value="N" weekly="6" style="cursor:default; color:0000ff;">토</div></th>
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

			if ($period['from_dt'] <= $lsDt && $period['to_dt'] >= $lsDt){
				$lbAdd = true;
			}?>
			<td class="center <?=$liWeekday == 6 ? 'last' : '';?> <?=$lbLastWeek ? 'bottom' : '';?>"><?
			if ($lbAdd){?>
				<div id="txtDay_<?=$i;?>" value="N" week="<?=$liWeekGbn;?>" weekly="<?=$liWeekIdx;?>" class="clsWeek_<?=$liWeekday;?>" style="cursor:default; font-weight:normal; background-color:#ffffff; color:<?=$lsFontClr;?>;" onclick="_planAssignDaySel(this);"><?=$i;?></div><?
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
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= Date('m');
	$today	= Date('Y-m-d');
	$sr		= $_GET['sr'];
	$title	= lfGetSPName($sr);
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:radio[name="optOrder"]').unbind('click').bind('click',function(){
			lfSearch();
		});

		//lfMoveDate();
		//lfMoveDay(0);

		lfSearch();
	});

	function lfChkAll(){
		var chk = $('#chkAll').attr('checked');

		$('input:checkbox[id^="chk_"]').attr('checked',chk);
	}

	function lfMoveYear(pos){
		var year = __str2num($('#lblYYMM').attr('year'));

		year += pos;

		$('#lblYYMM').attr('year',year).text(year);
		lfMoveDate();
		lfSearch();
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
		lfMoveDate();
		lfSearch();
	}

	function lfMoveDate(){
		var year = $('#lblYYMM').attr('year');
		var month = $('#lblYYMM').attr('month');

		$('#btnDay').html(__btnDay(year,month));
	}

	function lfMoveDay(day){
		var from = $('#lblYYMM').attr('from');
		var to = $('#lblYYMM').attr('to');

		day = __str2num(day);

		if (day == 0){
			from = 1;
			to = $('div[id^="btnDay_"]:last').text();
		}else{
			if (from > 0 && to > 0){
				from = day;
				to = '';
			}else if (from > 0){
				to = day;
			}else{
				from = day;
			}
		}

		if (from > to){
			$('#lblYYMM').attr('from',to).attr('to',from);
		}else{
			$('#lblYYMM').attr('from',from).attr('to',to);
		}

		from = __str2num($('#lblYYMM').attr('from'));
		to = __str2num($('#lblYYMM').attr('to'));

		if (from == 0) from = day;
		if (to == 0) to = day;

		$('div[id^="btnDay_"]').removeClass('my_box_2').addClass('my_box_1');
		$('div[id^="btnDay_"][id!="btnDay_A"]').each(function(){
			if ($(this).text() >= from && $(this).text() <= to){
				$(this).removeClass('my_box_1').addClass('my_box_2');
			}
		});
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'fromDay':$('#lblYYMM').attr('from')
			,	'toDay':$('#lblYYMM').attr('to')
			,	'from':$('#txtFrom').val()
			,	'to':$('#txtTo').val()
			,	'order':$('input:radio[name="optOrder"]:checked').val()
			,	'service':$('#lblService').attr('code')
			,	'resource':$('#lblResource').attr('code')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);
						var clr = '000000';

						if (col['week'] == '6'){
							clr = '0000ff';
						}else if (col['week'] == '0'){
							clr = 'ff0000';
						}

						var time = __styleTime(col['from']);
						var cost = __num2str(col['cost']);

						html += '<tr class="rowData" onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';"';
						html += ' jumin="'+col['jumin']+'"';
						html += ' dt="'+col['dt']+'"';
						html += ' date="'+col['date']+'"';
						html += ' from="'+col['from']+'"';
						html += ' seq="'+col['seq']+'"';
						html += '>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center" style="color:'+clr+';">'+col['date']+'</td>';
						html += '<td class="center">'+time+'</td>';
						html += '<td class="left">'+col['name']+'</td>';

						if ('<?=$type;?>' == '41'){
							if (col['gender'] == '1'){
								col['gender'] = '<span style="color:BLUE;">남</span>';
							}else if (col['gender'] == '0'){
								col['gender'] = '<span style="color:RED;">여</span>';
							}

							html += '<td class="center">'+col['birth']+'</td>';
							html += '<td class="center">'+col['gender']+'</td>';
						}

						html += '<td class="left">'+col['sugaNm']+'</td>';
						html += '<td class="left">'+col['resNm']+'</td>';
						//html += '<td class="right">'+cost+'</td>';
						html += '<td class="center"><input id="chk_'+i+'" name="chk" type="checkbox" class="checkbox" '+(col['stat'] == '1' ? 'checked' : '')+'></td>';
						html += '<td class="last"></td>';
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

	function lfSave(){
		var cnt = $('tr.rowData',$('#tbodyList')).length;

		if (cnt == 0){
			alert('일정데이타가 없습니다.');
			return;
		}

		var data = '';

		$('tr.rowData',$('#tbodyList')).each(function(){
			var stat = $('input:checkbox[id^="chk_"]',this).attr('checked') ? '1' : '9';

			data += 'jumin='+$(this).attr('jumin');
			data += '&dt='+$(this).attr('dt');
			data += '&date='+$(this).attr('date');
			data += '&from='+$(this).attr('from');
			data += '&seq='+$(this).attr('seq');
			data += '&stat='+stat;
			data += String.fromCharCode(11);
		});

		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'data':data
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//재가관리 서비스 조회
	function lfCareSvcFind(){
		var h = 600;
		var w = 800;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../care/care_suga_find.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_CARESVC', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'sr':'<?=$sr;?>'
			,	'type':'CUST_SEL'
			,	'return':'lfCareSvcFindResult'
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

		form.setAttribute('target', 'FIND_CARESVC');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfCareSvcFindResult(cd,mstNm,proNm,svcNm,subNm){
		$('#lblService').attr('tag','서비스선택').attr('code',cd).html(subNm+'<span style="margin-left:5px;"><a href="#" onclick="lfClear($(\'#lblService\')); return false;">X</a></span>');

		lfSearch();
	}

	function lfClear(obj){
		$(obj).attr('code','').html($(obj).attr('tag'));

		lfSearch();
	}

	function lfResourceFind(){
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../care/care_resource_find.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_RESOURCE', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'sugaCd':$('#lblService').attr('code')
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			,	'sr'	:'<?=$sr;?>'
			,	'return':'lfResourceFindResult'
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

		form.setAttribute('target', 'FIND_RESOURCE');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfResourceFindResult(cd,cost,nm,per){
		if (!cd){
			alert('자원코드가 없습니다.\n관리자에게 문의하여 주십시오.');
			return;
		}
		$('#lblResource').attr('tag','자원선택').attr('code',cd).html(nm+'<span style="margin-left:5px;"><a href="#" onclick="lfClear($(\'#lblResource\')); return false;">X</a></span>');

		lfSearch();
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'fromDay':$('#lblYYMM').attr('from')
			,	'toDay':$('#lblYYMM').attr('to')
			,	'from':$('#txtFrom').val()
			,	'to':$('#txtTo').val()
			,	'order':$('input:radio[name="optOrder"]:checked').val()
			,	'service':$('#lblService').attr('code')
			,	'resource':$('#lblResource').attr('code')
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

		form.setAttribute('method', 'post');
		form.setAttribute('action', './care_conf_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">실적관리(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="350px">
		<col>
		<!--
			<col width="40px">
			<col width="85px">
			<col width="550px">
			<col>
		-->
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기간</th>
			<td class="">
				<input id="txtFrom" type="text" value="<?=$today;?>" class="date"> ~
				<input id="txtTo" type="text" value="<?=$today;?>" class="date">
			</td>
			<td class="left last" rowspan="3">
				<span class="btn_pack m"><button type="button" onclick="lfSearch(); return false;">조회</button></span>
				<span class="btn_pack m"><button type="button" onclick="lfSave(); return false;">저장</button></span>
				<span class="btn_pack m"><button type="button" onclick="lfExcel(); return false;">엑셀</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">검색</th>
			<td class="left">
				<span class="btn_pack find" style="margin-left:2px; margin-top:1px;" onclick="lfCareSvcFind();"></span>
				<span id="lblService" code="" style="height:25px; line-height:25px; margin-left:5px;">서비스선택</span>
				<span class="btn_pack find" style="margin-left:2px; margin-top:1px; margin-left:20px;" onclick="lfResourceFind();"></span>
				<span id="lblResource" code="" style="height:25px; line-height:25px; margin-left:5px;">자원선택</span>
			</td>
		</tr>
		<tr>
			<th class="center">정렬</th>
			<td class="">
				<input id="optOrder1" name="optOrder" type="radio" class="radio" value="1" checked><label for="optOrder1">일자/시간순</label>
				<input id="optOrder2" name="optOrder" type="radio" class="radio" value="2"><label for="optOrder2"><?=($type == 41 ? '대상자순' : '직원명순');?></label>
				<input id="optOrder3" name="optOrder" type="radio" class="radio" value="3"><label for="optOrder3">서비스순</label>
				<input id="optOrder4" name="optOrder" type="radio" class="radio" value="4"><label for="optOrder4"><?=($type == 41 ? '자원순' : '대상자순');?></label>
			</td>
		</tr>
		<!--
			<tr>
				<th class="center">년도</th>
				<td class="center">
					<div class="left" style="padding-top:2px;">
						<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
						<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>" from="" to=""><?=$year;?></div>
						<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
					</div>
				</td>
				<td class="left"><? echo $myF->_btn_month($month, 'lfMoveMonth(');?></td>
				<td class="left last">
					<span class="btn_pack m"><button type="button" onclick="lfSearch(); return false;">조회</button></span>
					<span class="btn_pack m"><button type="button" onclick="lfSave(); return false;">저장</button></span>
					<span class="btn_pack m"><button type="button" onclick="lfExcel(); return false;">엑셀</button></span>
				</td>
			</tr>
			<tr>
				<th class="center">일자</th>
				<td class="left last" style="padding-top:1px;" colspan="3" id="btnDay"></td>
			</tr>
			<tr>
				<th class="center">검색</th>
				<td class="left last" colspan="3">
					<span class="btn_pack find" style="margin-left:2px; margin-top:1px;" onclick="lfCareSvcFind();"></span>
					<span id="lblService" code="" style="height:25px; line-height:25px; margin-left:5px;">서비스선택</span>
					<span class="btn_pack find" style="margin-left:2px; margin-top:1px; margin-left:20px;" onclick="lfResourceFind();"></span>
					<span id="lblResource" code="" style="height:25px; line-height:25px; margin-left:5px;">자원선택</span>
				</td>
			</tr>
			<tr>
				<th class="center">정렬</th>
				<td class="last" colspan="3">
					<input id="optOrder1" name="optOrder" type="radio" class="radio" value="1" checked><label for="optOrder1">일자/시간순</label>
					<input id="optOrder2" name="optOrder" type="radio" class="radio" value="2"><label for="optOrder2"><?=($type == 41 ? '고객명순' : '직원명순');?></label>
					<input id="optOrder3" name="optOrder" type="radio" class="radio" value="3"><label for="optOrder3">서비스순</label>
					<input id="optOrder4" name="optOrder" type="radio" class="radio" value="4"><label for="optOrder4"><?=($type == 41 ? '자원순' : '고객명순');?></label>
				</td>
			</tr>
		-->
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="40px">
		<col width="50px">
		<col width="70px"><?
		if ($type == 41){?>
			<col width="70px">
			<col width="30px"><?
		}?>
		<col width="150px"><?
		if ($type == 41){?>
			<col width="130px"><?
		}else if ($type == 42){?>
			<col width="70px"><?
		}?>
		<!--col width="70px"-->
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">일자</th>
			<th class="head">시간</th><?
			if ($type == 41){?>
				<th class="head">대상자</th>
				<th class="head">생년월일</th>
				<th class="head">성별</th><?
			}else if ($type == 42){?>
				<th class="head">직원명</th><?
			}?>
			<th class="head">서비스</th><?
			if ($type == 41){?>
				<th class="head">자원</th><?
			}else if ($type == 42){?>
				<th class="head">대상자</th><?
			}?>
			<!--th class="head">단가</th-->
			<th class="head"><label><input id="chkAll" name="chk" type="checkbox" class="checkbox" onclick="lfChkAll();">실적여부</label></th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
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
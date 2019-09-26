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

	$code = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$month = Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoadYear()',10);
		$('div[id^="btnMonth"]').unbind('click').bind('click',function(){
			if ($(this).hasClass('my_month_2')){
				return false;
			}

			lfLoadList(this);
		});

		$('input:text').each(function(){
			__init_object(this);
		});
	});

	function lfMoveYear(pos){
		var year = __str2num($('#year').text());

		year += pos;

		$('#year').text(year);

		setTimeout('lfLoadYear()',10);
	}

	function lfLoadYear(){
		$.ajax({
			type:'POST'
		,	url:'./expense_search.php'
		,	data:{
				'type':'YEAR'
			,	'year':$('#year').text()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){

				var month = __parseStr(data);

				for(var i=1; i<=12; i++){
					if (month[i] == '1'){
						$('#btnMonth'+i).removeClass('my_month_2').addClass('my_month_y').css('color','#000000');
					}else if (month[i] == '0'){
						$('#btnMonth'+i).removeClass('my_month_2').addClass('my_month_1').css('color','#000000');
					}else{
						$('#btnMonth'+i).removeClass('my_month_y').removeClass('my_month_1').addClass('my_month_2').css('color','#999999');
					}
				}

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadList(obj){
		var year = $('#year').text();
		var month = $(obj).attr('value');
		var closeYn = 'N';

		$('#yymm').attr('year',year).attr('month',month).text(year+'년 '+__str2num(month)+'월');

		$.ajax({
			type:'POST'
		,	url:'./expense_search.php'
		,	data:{
				'type':'LIST'
			,	'year':$('#year').text()
			,	'month':month
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

				closeYn = lfCloseYn(month);

				if (closeYn == 'Y'){
					$('#btnSave').attr('disabled',true);
				}else{
					$('#btnSave').attr('disabled',false);
				}

				$('div[id^="btnMonth"]').each(function(){
					if ($(this).attr('tmp')){
						$(this).removeClass('my_month_r').addClass($(this).attr('tmp'));
					}
				});

				if ($(obj).hasClass('my_month_y')){
					$(obj).attr('tmp','my_month_y');
				}else if ($(obj).hasClass('my_month_1')){
					$(obj).attr('tmp','my_month_1');
				}else{
				}

				$(obj).removeClass('my_month_y').removeClass('my_month_1').addClass('my_month_r');
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(13));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);
						var chk = '';

						if (__str2num(col['amt']) > 0){
							chk = 'checked';
						}

						html	+= '<tr onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">';
						html	+= '<td class="center">'+no+'</td>';
						html	+= '<td class="center">'+col['memNm']+'</td>';
						html	+= '<td class="center">'+col['cltNm']+'</td>';
						html	+= '<td class="center"><div class="right">'+__num2str(col['expensePay'])+'</div></td>';
						html	+= '<td class="center"><div class="right">'+__num2str(col['overPay'])+'</div></td>';
						html	+= '<td class="center"><div class="right">'+__num2str(col['bipay'])+'</div></td>';
						html	+= '<td class="center"><div class="right">'+__num2str(col['totPay'])+'</div></td>';

						if ($('#btnSave').attr('disabled')){
							html	+= '<td class="center bold"><input id="chkIn'+no+'" name="chkIn" type="hidden">'+(chk ? 'Y' : '')+'</td>';
						}else{
							html	+= '<td class="center"><input id="chkIn'+no+'" name="chkIn" type="checkbox" class="checkbox" onclick="lfListChk(this);" memCd="'+col['memCd']+'" cltCd="'+col['cltCd']+'" payYn="'+col['payYn']+'" '+chk+'></td>';
						}

						html += '<td class="center"><div class="right">'+__num2str(col['amt'])+'</div></td>';
						html += '<td class="center">'
							 +	'<input id="entDt'+no+'" name="entDt" type="text" class="date" value="'+col['entDt']+'" '+(col['entDt'] && col['entSeq'] ? 'readonly' : '')+'>'
							 +	'<input id="entSeq'+no+'" name="entSeq" type="hidden" value="'+col['entSeq']+'">'
							 +	'</td>';
						html += '<td class="center last"></td>'
						html += '</tr>';

						no ++;
					}
				}

				if (!html){
					html = '<tr><td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#bodyList').html(html);
				$('#tempLodingBar').remove();
				$('input:text',$('#bodyList')).each(function(){
					__init_object(this);
				});
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfChkAll(){
		var chk = $('#chkAll').attr('checked');

		$('input:checkbox[name="chkIn"]').each(function(){
			$(this).attr('checked',chk);
			lfListChk(this);
		});
	}

	function lfListChk(chk){
		var obj = $(chk).parent().parent();
		var expense = __str2num($('td',obj).eq(6).text());
		var inAmt = 0;

		if ($(chk).attr('checked')){
			inAmt = expense;
		}

		$('div',$('td',obj).eq(8)).text(__num2str(inAmt));
	}

	function lfCloseYn(month){
		var val = '';

		$.ajax({
			type:'POST'
		,	url:'./expense_search.php'
		,	async:false
		,	data:{
				'type':'CLOSE'
			,	'year':$('#year').text()
			,	'month':month
			}
		,	beforeSend:function(){

			}
		,	success:function(data){
				val = data;
			}
		,	error:function(){
			}
		}).responseXML;

		return val;
	}

	function lfSave(){
		var data = '';
		var error = false;

		$('input:checkbox[name="chkIn"]').each(function(){
			var obj = $(this).parent().parent();

			data += (data ? '?' : '');

			if ($(this).attr('checked')){
				if (!$('input:text[name="entDt"]',obj).val()){
					error = true;
					alert('입금일자를 입력하여 주십시오.');
					$('input:text[name="entDt"]',obj).focus();
					return false;
				}

				data += 'memCd='+$(this).attr('memCd');
				data += '&memNm='+$('td',obj).eq(1).text();
				data += '&payYn='+$(this).attr('payYn');
				data += '&cltCd='+$(this).attr('cltCd');
				data += '&cltNm='+$('td',obj).eq(2).text();
				data += '&expense='+__str2num($('td',obj).eq(3).text());
				data += '&over='+__str2num($('td',obj).eq(4).text());
				data += '&bipay='+__str2num($('td',obj).eq(5).text());
				data += '&tot='+__str2num($('td',obj).eq(6).text());
				data += '&amt='+__str2num($('td',obj).eq(8).text());
			}

			data += '&entDt='+$('input:text[name="entDt"]',obj).val().split('-').join('');
			data += '&entSeq='+$('input:hidden[name="entSeq"]',obj).val();
		});

		if (error) return;

		$.ajax({
			type:'POST'
		,	url:'./expense_reg.php'
		,	data:{
				'year':$('#yymm').attr('year')
			,	'month':$('#yymm').attr('month')
			,	'data':data
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				//document.write(result);
				if (!result){
					alert('정상적으로 처리되었습니다.');
					var m = __str2num($('#yymm').attr('month'));
					if (m > 0) lfLoadList($('#btnMonth'+m));
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetDt(){

		$('input:text[name="entDt"]').each(function(){
			if (!$(this).attr('readonly')){
				$(this).val($('#txtEntDt').val());
			}
		});
	}
</script>
<form name="f" method="post">
<div class="title title_border">본인부담금공제</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="year"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
		</tr>
		<tr>
			<th class="center">월별</th>
			<td class="last"><?
				for($i=1; $i<=12; $i++){?>
					<div id="btnMonth<?=$i;?>" value="<?=($i<10?'0':'').$i;?>" tmp="" class="my_month my_month_2" style="float:left; cursor:default; margin-left:3px; margin-top:2px; padding-bottom:1px; color:#999999;"><?=$i;?>월</div><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="200px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="left last" id="yymm" year="" month=""></td>
			<td class="right last">
				<span class="btn_pack m"><button id="btnSave" type="button" onclick="lfSave(); return false;" disabled="true">저장</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="30px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">요양보호사</th>
			<th class="head">수급자</th>
			<th class="head">본인부담금</th>
			<th class="head">초과금액</th>
			<th class="head">비급여금액</th>
			<th class="head">합계금액</th>
			<th class="head"><input id="chkAll" name="chk" type="checkbox" class="checkbox" onclick="lfChkAll();"></th>
			<th class="head">입금금액</th>
			<th class="head"><input id="txtEntDt" type="text" value="" class="date" onchange="lfSetDt();"></th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="bodyList">
		<tr>
			<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center bottom last"></td>
		</tr>
	</tfoot>
</table>
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$svcCd = $_POST['svcCd'];
	$svcNm = $conn->_svcNm($svcCd);
	$jumin = $_POST['jumin'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$type  = $_POST['type'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	switch($type){
		case 'make':
			$lsTitle = '바우처 생성내역 등록';
			break;
	}

	//노인돌봄 서비스구분
	if ($svcCd == '2'){
		$sql = 'select svc_val
				  from client_his_old
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
				 order by seq desc
				 limit 1';
		$lsVal = $conn->get_data($sql);
	}


	if ($svcCd == '4'){
		$sql = 'select svc_val
				  from client_his_dis
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
				 order by seq desc
				 limit 1';
		$disVal = $conn->get_data($sql);
	}


	include_once('../find/_suga_info.php');
?>
<script type="text/javascript" src="./iljung.js"></script>
<form id="f" name="f" method="post">
<div class="title title_border"><?=$lsTitle;?></div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="150px">
		<col width="50px">
		<col width="30px">
		<col width="130px">
		<col width="50px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관명</th>
			<td class="left" colspan="7"><?=$_SESSION['userCenterName'];?>(<?=$_SESSION['userCenterGiho'];?>)</td>
		</tr>
		<tr>
			<th>고객명</th>
			<td class="left"><div id="cNm"></div></td>
			<th rowspan="2">연락처</th>
			<th>유선</th>
			<td class="left"><div id="cPhone"></div></td>
			<th rowspan="2">보호자</th>
			<th>성명/관계</th>
			<td class="left"><div id="cParent"></div></td>
		</tr>
		<tr>
			<th>주민번호</th>
			<td class="left"><div id="cJumin"></div></td>
			<th>무선</th>
			<td class="left"><div id="cMobile"></div></td>
			<th>연락처</th>
			<td class="left"><div id="cParentTel"></div></td>
		</tr>
		<tr>
			<th>주소</th>
			<td class="left" colspan="7"><div id="cAddr"></div></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="205px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>소득등급</th>
			<td class="left"><div id="disLvl" value=""></div></td>
			<th>본인부담금</th>
			<td class="left"><div id="cExpense"></div></td>
		</tr>
	</tbody>
</table>

<div class="title title_border">서비스 내역</div>
<table class="my_table title_border" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>서비스</th>
			<td class="left"><?=$svcNm;?></td>
		</tr>
		<tr>
			<th>월별</th>
			<td class="left">
				<div id="loMon1" value="1" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">1월</div>
				<div id="loMon2" value="2" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">2월</div>
				<div id="loMon3" value="3" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">3월</div>
				<div id="loMon4" value="4" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">4월</div>
				<div id="loMon5" value="5" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">5월</div>
				<div id="loMon6" value="6" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">6월</div>
				<div id="loMon7" value="7" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">7월</div>
				<div id="loMon8" value="8" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">8월</div>
				<div id="loMon9" value="9" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">9월</div>
				<div id="loMon10" value="10" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">10월</div>
				<div id="loMon11" value="11" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">11월</div>
				<div id="loMon12" value="12" value1="N" class="my_month my_month_1" style="float:left; margin-top:1px;margin-right:2px; color:#cccccc; cursor:default;">12월</div>
			</td>
		</tr>
	</tbody>
</table>
<table id="loSvcGbnTbl" class="my_table title_border" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr id="loSvcGbn" value="<?=$lsVal;?>" style="display:none;">
			<th>서비스구분</th>
			<td><div id="loSvcGbnHtml"></div></td>
		</tr>
		<tr id="loSvcTime" style="display:none;">
			<th>서비스시간</th>
			<td><div id="loSvcTimeHtml"></div></td>
		</tr>
		<tr id="loSvcOver" style="display:none;">
			<th>이월시간</th>
			<td><div id="loSvcOverHtml"></div></td>
		</tr>
		<tr id="loSvcStnd" style="display:none;">
			<th>기본급여</th>
			<td><div id="loSvcStndHtml" value=""></div></td>
		</tr>
		<tr id="loSvcAdd" style="display:none;">
			<th>추가급여</th>
			<td><div id="loSvcAddHtml" value=""></div></td>
		</tr>
	</tbody>
</table>
<input id="code" name="code" type="hidden" value="<?=$code;?>">
<input id="svcCd" name="svcCd" type="hidden" value="<?=$svcCd;?>">
<input id="jumin" name="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
<input id="year" name="year" type="hidden" value="<?=$year;?>">
<input id="month" name="month" type="hidden" value="<?=$month;?>">
<input id="seq" name="seq" type="hidden" value="0">
<input id="disVals" name="disVals" type="hidden" value="<?=$disVal;?>">

<div id="svcDiv_1" > 
<?
	include_once('../common/voucher_addpay_tbl.php');
?>
</div>
<div id="svcDiv_2" style="display:none;"> 
<?
	include_once('../common/voucher_addpay_tbl_new.php');
?>
</div>
<script type="text/javascript">
	var liAddTot     = 0;
	var liAddTime    = 0;
	var liAddSupport = 0;
	var liAddExpense = 0;
	var liCost       = lfGetCost();

	//단가저장
	function lfGetCost(){
		var year  = $('#year').val();
		var month = $('#month').val();

		if (!year || !month){
			var date = new Date();

			year  = date.getFullYear();
			month = date.getMonth()+1;
			month = (month < 10 ? '0' : '')+month;
		}
		
		if (year+month >= '201901'){
			return 12960;
		}else if (year+month >= '201801'){
			return 10760;
		}else if (year+month >= '201701'){
			return 9240;
		}else if (year+month >= '201601'){
			return 9000;
		}else if (year+month >= '201502'){
			return 8810;
		}else if (year+month >= '201302'){
			return 8550;
		}else{
			return 8300;
		}
	}
</script>
<?
	if ($lsRoot == 'sugupja'){?>
		<script type="text/javascript">
		$('.clsAddPay').unbind('click').click(function(){
			lfInitAddPay();
		});
		</script><?
	}else{?>
		<script type="text/javascript">
		$('#overSupport').unbind('change').change(function(){
			var cost = lfGetCost();
			var time = __round(__str2num($(this).val()) / cost, 1);

			$('#overTot').attr('value',$(this).val()).text(__num2str($(this).val()));
			$('#overTime').attr('value',time).text(__num2str(time));

			lfTotAddPay();
		});
		</script><?
	}
?>
<script type="text/javascript">
	$('#sidoTime').change(function(){
		lfInitSJPay(this,'sido');
	});

	$('#jachTime').change(function(){
		lfInitSJPay(this,'jach');
	});
	
	
	divViewType();
	

	function lfAddPayLoad(){
		lfInitAddPay();
		lfInitSJPay($('#sidoTime'),'sido');
		lfInitSJPay($('#jachTime'),'jach');
	}

	function lfInitAddPay(){
		liAddTot     = 0;
		liAddTime    = 0;
		liAddSupport = 0;
		liAddExpense = 0;

		lfSetAddPay('add');
		lfSetAddPayNew('add');
		
		if($('#disVals').val() == '1' || $('#disVals').val() == '2'){
			$('input:radio[name="addPay1"]:checked').each(function(){
				lfCalAddPay(this,'add');
			});

			$('input:checkbox[names="addPay2"]:checked').each(function(){
				lfCalAddPay(this,'add');
			});
		}else {
			$('input:checkbox[names="addPay2New"]:checked').each(function(){
				lfCalAddPay(this,'add');		
			});
		}
		
		lfSetAddPay('add');
		lfTotAddPay();
		
		lfSetAddPayNew('add');
		lfTotAddPayNew();
	
	}

	function lfInitSJPay(obj, gbn){
		var cost = lfGetCost();

		liAddTot     = __str2num($(obj).attr('value')) * cost;
		liAddSupport = liAddTot;

		$('#'+gbn+'Tot').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#'+gbn+'Support').attr('value',liAddSupport).text(__num2str(liAddSupport));

		lfTotAddPay();
	}

	function lfSetAddPay(gbn){
		$('#'+gbn+'Tot').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#'+gbn+'Time').attr('value',liAddTime).text(__num2str(liAddTime));
		$('#'+gbn+'Support').attr('value',liAddSupport).text(__num2str(liAddSupport));
		$('#'+gbn+'Expense').attr('value',liAddExpense).text(__num2str(liAddExpense));
	}

	function lfSetAddPayNew(gbn){
		$('#'+gbn+'Tot2').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#'+gbn+'Time2').attr('value',liAddTime).text(__num2str(liAddTime));
		$('#'+gbn+'Support2').attr('value',liAddSupport).text(__num2str(liAddSupport));
		$('#'+gbn+'Expense2').attr('value',liAddExpense).text(__num2str(liAddExpense));
	}

	function lfCalAddPay(obj,gbn){
		try{
			
			var pay  = __str2num($(obj).attr('value1'));
			var time = __str2num($(obj).attr('value2'));
			var lvl  = __str2num($('#disLvl').attr('value'));
			
		
			var expenseAmt = 0;
			var supportAmt = 0;

			var result = 1;
			var tday = new Date();
			var date  = tday.getFullYear()+'-'+(((tday.getMonth()+1) < 10 ? '0' : '')+(tday.getMonth()+1))+'-'+((tday.getDate() < 10 ? '0' : '')+tday.getDate());
			var today  = date;
			//var today  = getToday();
			
			try{
				for(var i=0; i<laExpense[lvl].length; i++){
					if (laExpense[lvl][i]['from'] <= today && laExpense[lvl][i]['to'] >= today){
						if (laExpense[lvl][i]['pay'] > 0){
							result = laExpense[lvl][i]['pay'];
						}else{
							result = laExpense[lvl][i]['rate'];
						}
						break;
					}
				}
			}catch(e){
			}
			
			if (result > 1){
				expenseAmt = result;
			}else{
				expenseAmt = cut(pay * result, 100);
			}

			supportAmt = pay - expenseAmt;

			liAddTot     += pay;
			liAddTime    += time;
			liAddSupport += supportAmt;
			liAddExpense += expenseAmt;

		}catch(e){
		}
	}

	function lfTotAddPay(){
		var liAddTot     = 0;
		var liAddTime    = 0;
		var liAddSupport = 0;
		var liAddExpense = 0;

		$('.clsTot').each(function(){
			liAddTot += __str2num($(this).attr('value'));
		});
		$('.clsTime').each(function(){
			liAddTime += __str2num($(this).attr('value'));
		});
		$('.clsSupport').each(function(){
			liAddSupport += __str2num($(this).attr('value'));
		});
		$('.clsExpense').each(function(){
			liAddExpense += __str2num($(this).attr('value'));
		});

		$('#totalTot').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#totalTime').attr('value',liAddTime).text(__num2str(liAddTime));
		$('#totalSupport').attr('value',liAddSupport).text(__num2str(liAddSupport));
		$('#totalExpense').attr('value',liAddExpense).text(__num2str(liAddExpense));

		try{
			var liOverTime = __str2num($('#overTime').text());
			var liButTime  = __str2num($('#totalTime').text());
			var liMakeTime = liButTime - liOverTime;

			$('#txtOverTime').text(liOverTime);
			$('#txtMakeTime').text(liMakeTime);
			$('#txtBuyTime').text(liButTime);
		}catch(e){
		}
		
	}

	function lfTotAddPayNew(){
		var liAddTot     = 0;
		var liAddTime    = 0;
		var liAddSupport = 0;
		var liAddExpense = 0;
		
		$('.clsTotNew').each(function(){
			liAddTot += __str2num($(this).attr('value'));
		});
		$('.clsTimeNew').each(function(){
			liAddTime += __str2num($(this).attr('value'));
		});
		$('.clsSupportNew').each(function(){
			liAddSupport += __str2num($(this).attr('value'));
		});
		$('.clsExpenseNew').each(function(){
			liAddExpense += __str2num($(this).attr('value'));
		});
		
		$('#totalTot2').attr('value',liAddTot).text(__num2str(liAddTot));
		$('#totalTime2').attr('value',liAddTime).text(__num2str(liAddTime));
		$('#totalSupport2').attr('value',liAddSupport).text(__num2str(liAddSupport));
		$('#totalExpense2').attr('value',liAddExpense).text(__num2str(liAddExpense));

		try{
			var liOverTime = __str2num($('#overTime2').text());
			var liButTime  = __str2num($('#totalTime2').text());
			var liMakeTime = liButTime - liOverTime;

			$('#txtOverTime2').text(liOverTime);
			$('#txtMakeTime2').text(liMakeTime);
			$('#txtBuyTime2').text(liButTime);
		}catch(e){
		}
		
	}

	function divViewType(obj){
	
		if(!obj){
			if('<?=$disVal;?>' == 1){
				$('#svcDiv_2').hide();
				$('#svcDiv_1').show();
			}else {
				$('#svcDiv_1').hide();
				$('#svcDiv_2').show();
			}
		}else {
			if(obj.value == '1'){
				$('#svcDiv_2').hide();
				$('#svcDiv_1').show();
				$('#disVals').val('1');
			}else {
				$('#svcDiv_1').hide();
				$('#svcDiv_2').show();
				$('#disVals').val('3');
			}
		}

		lfInitAddPay();
	}
</script>
<div class="title title_border">구매 내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col width="60px">
		<col width="80px">
		<col width="70px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>이월시간</th>
			<td class="left"><div id="txtOverTime" value="0" class="right">0</div></td>
			<th>생성시간</th>
			<td class="left"><div id="txtMakeTime" value="0" class="right">0</div></td>
			<th>총구매시간</th>
			<td class="left"><div id="txtBuyTime" value="0" class="right">0</div></td>
			<td class="center">
				<div id="btnClose" style="float:right; width:auto; margin-right:3px;"><span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span></div>
				<div id="btnSave" style="float:right; width:auto; margin-right:3px;"><span class="btn_pack m"><button type="button" onclick="lfSaveData();">저장</button></span></div>
				<div id="btnDelete" style="float:left; width:auto; margin-left:3px;"><span class="btn_pack m"><button type="button" onclick="lfDelData();" style="color:#ff0000;">삭제</button></span></div>
			</td>
		</tr>
	</tbody>
</table>
</form>
<div id="lastObj"></div>
<?
	include_once('../inc/_footer.php');
?>
<script type="text/javascript">
var loTimer      = null;

$(document).ready(function(){
	var svcCd = $('#svcCd').val();

	lfSetSvcGbn();

	if (svcCd == '1'){
		$('#loSvcTime').show();
		if ($('#year').val()+$('#month').val() >= '201309'){
			$('#loSvcOver').show();
		}
	}else if (svcCd == '2'){
		$('#loSvcGbn').show();
		$('#loSvcTime').show();
		$('#loSvcOver').show();
	}else if (svcCd == '3'){
		$('#loSvcGbn').show();
	}else if (svcCd == '4'){
		$('#loSvcStnd').show();
		$('#loSvcAdd').show();
		if($('#disVals').val() == '1' || $('#disVals').val() == '2'){
			$('#loSvcDisTbl').show();
		}else {
			$('#loSvcDisTbl2').show();
		}
	}
	
	if (svcCd == '1' || svcCd == '2' || svcCd == '3')
		$('#loSvcGbnTbl').removeClass('title_border');

	_iljungLoadInfo(1);
	_iljungLoadInfo(2);
	_iljungLoadInfo(3);

	__init_form(document.f);

	loTimer = setInterval('lfWinResize()',10);

});

function lfMoveMonth(month){
	var svcCd = $('#svcCd').val();

	month = (parseInt(month,10) < 10 ? '0' : '')+parseInt(month,10);

	$('#month').val(month);
	_iljungLoadInfo(3);

	if (svcCd == '4'){
		_iljungLoadInfo(42);
	}

	lfLoadData();
	lfSetSvcGbn();
}

function lfSetSvcGbn(){
	var svcCd = $('#svcCd').val();
	var html  = '';

	switch(svcCd){
		case '1':
			if ($('#year').val()+$('#month').val() >= '201302' && $('#year').val()+$('#month').val() <= '201812'){
				html = '<input id="time1" name="time" type="radio" value="1" value1="24" class="radio"><label for="time1">24시간</label>'
					 + '<input id="time2" name="time" type="radio" value="2" value1="27" class="radio"><label for="time2">27시간</label>';
			}else if ($('#year').val()+$('#month').val() >= '201901'){
				html = '<input id="time1" name="time" type="radio" value="1" value1="24" class="radio"><label for="time1">24시간</label>'
					 + '<input id="time2" name="time" type="radio" value="2" value1="27" class="radio"><label for="time2">27시간</label>'
					 + '<input id="time3" name="time" type="radio" value="3" value1="40" class="radio"><label for="time3">40시간</label>';
			}else{
				html = '<input id="time1" name="time" type="radio" value="1" value1="18" class="radio"><label for="time1">18시간</label>'
					 + '<input id="time2" name="time" type="radio" value="2" value1="24" class="radio"><label for="time2">24시간</label>';
			}

			$('#loSvcTimeHtml').html(html);

			if ($('#year').val()+$('#month').val() >= '201309'){

				html = '<input id="overTime" name="overTime" type="text" value="0" class="number" style="width:50px;" onkeydown="__onlyNumber(this,\'.\');"  >시간(일)';
				$('#loSvcOverHtml').html(html);
				$('#loSvcOver').show();
				$('input:radio[name="time"]').unbind('click').click(function(){
					var over = __str2num($('#overTime').val());
					var make = __str2num($(this).attr('value1'));
					var tot  = over + make;

					$('#txtOverTime').attr('value',over).text(over+'시간(일)');
					$('#txtMakeTime').attr('value',make).text(make+'시간(일)');
					$('#txtBuyTime').attr('value',tot).text(tot+'시간(일)');
				});

				$('#overTime').unbind('change').change(function(){
					$('input:radio[name="time"]:checked').click();
				});
			}else {
				$('#loSvcOver').hide();
				$('input:radio[name="time"]').unbind('click').click(function(){

					$('#txtOverTime').text('0시간(일)');
					$('#txtMakeTime').text($(this).attr('value1')+'시간(일)');
					$('#txtBuyTime').text($(this).attr('value1')+'시간(일)');
				});
			}

			break;

		case '2':
			var lsSvcGbn = $('#loSvcGbn').attr('value');

			html = '<input id="gbn9" name="gbn" type="radio" value="9" value1="24" value2="'+lsSvcGbn+'" class="radio"><label for="gbn9">해당없음</label>';

			if (lsSvcGbn == '1'){
				html += '<input id="gbn1" name="gbn" type="radio" value="1" value1="18" value2="'+lsSvcGbn+'" class="radio"><label for="gbn1">방문</label>';
			}else if (lsSvcGbn == '2'){
				html += '<input id="gbn2" name="gbn" type="radio" value="2" value1="24" value2="'+lsSvcGbn+'" class="radio"><label for="gbn2">주간보호</label>';
			}else if (lsSvcGbn == '3'){
				html += '<input id="gbn3" name="gbn" type="radio" value="3" value1="24" value2="'+lsSvcGbn+'" class="radio"><label for="gbn3">단기가사</label>';
			}

			$('#loSvcGbnHtml').html(html);

			html = '<div id="divTime" style="display:none;">'
				 + '<input id="time1" name="time" type="radio" value="1" value1="0" class="radio"><label for="time1"></label>'
				 + '<input id="time2" name="time" type="radio" value="2" value1="0" class="radio"><label for="time2"></label>'
				 + '</div>'
				 + '<div id="divText" class="left" style="display:none;">서비스구분을 선택하여 주십시오.</div>';

			$('#loSvcTimeHtml').html(html);

			html = '<input id="overTime" name="overTime" type="text" value="0" class="number" style="width:50px;" onkeydown="__onlyNumber(this,\'.\');">시간(일)';

			$('#loSvcOverHtml').html(html);

			$('input:radio[name="gbn"]').unbind('click').click(function(){
				if ($(this).val() == '1'){
					$('#divTime').show();
					$('#divText').hide();
					$('label[for="time1"]').text('27시간').show();
					$('label[for="time2"]').text('36시간').show();
					$('#time1').attr('value1','27').show();
					$('#time2').attr('value1','36').show();
				}else if ($(this).val() == '2'){
					$('#divTime').show();
					$('#divText').hide();
					$('label[for="time1"]').text('9일').show();
					$('label[for="time2"]').text('12일').show();
					$('#time1').attr('value1','9').show();
					$('#time2').attr('value1','12').show();
				}else if ($(this).val() == '3'){
					$('#divTime').show();
					$('#divText').hide();
					$('label[for="time1"]').text('24시간(1개월)').show();
					$('#time1').attr('value1','24').show();
					//$('label[for="time2"]').hide();
					//$('#time2').hide();
					$('label[for="time2"]').text('48시간(2개월)').show();
					$('#time2').attr('value1','48').show();
				}else{
					$('#time1').attr('value1','0');
					$('#time2').attr('value1','0');
					$('input:radio[name="time"]:checked').click();
					$('#divTime').hide();
					$('#divText').show();
				}
			});

			$('input:radio[name="time"]').unbind('click').click(function(){
				var over = __str2num($('#overTime').val());
				var make = __str2num($(this).attr('value1'));
				var tot  = over + make;

				$('#txtOverTime').attr('value',over).text(over+'시간(일)');
				$('#txtMakeTime').attr('value',make).text(make+'시간(일)');
				$('#txtBuyTime').attr('value',tot).text(tot+'시간(일)');
			});

			$('#overTime').unbind('change').change(function(){
				$('input:radio[name="time"]:checked').click();
			});

			$('input:radio[name="gbn"]:input[value="'+lsSvcGbn+'"]').click();
			$('input:radio[name="time"]:checked').click();

			break;

		case '3':
			html = '<input id="gbn1" name="gbn" type="radio" value="1" value1="12" class="radio"><label for="gbn1">단태아(12일)</label>'
				 + '<input id="gbn2" name="gbn" type="radio" value="2" value1="18" class="radio"><label for="gbn2">쌍태아(18일)</label>'
				 + '<input id="gbn3" name="gbn" type="radio" value="3" value1="24" class="radio"><label for="gbn3">삼태아(24일)</label>';

			$('#loSvcGbnHtml').html(html);

			$('input:radio[name="gbn"]').unbind('click').click(function(){
				$('#txtOverTime').text('0일');
				$('#txtMakeTime').text($(this).attr('value1')+'일');
				$('#txtBuyTime').text($(this).attr('value1')+'일');
			});

			break;

		case '4':
			_iljungLoadInfo(42);	
			_iljungLoadInfo(41);

			break;
	}
}

function lfLoadData(){	
	var html = '';

	$.ajax({
		type: 'POST'
	,	url : './iljung_fun.php'
	,	data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()
		,	svcCd  : $('#svcCd').val()
		,	year   : $('#year').val()
		,	month  : $('#month').val()
		,	mode   : 201
		}
	,	success: function (result){
			var val = __parseStr(result);

			switch($('#svcCd').val()){
				case '1':
					$('#txtOverTime').text('0시간(일)');
					$('#txtMakeTime').text('0시간(일)');
					$('#txtBuyTime').text('0시간(일)');
					$('input:radio[name="time"]').attr('checked',false);
					$('input:radio[name="time"]:input[value="'+val['lvl']+'"]').attr('checked',true);
					$('#overTime').val(__num2str(val['overtime']));
					$('input:radio[name="time"]:checked').click();
					break;

				case '2':
					$('#txtOverTime').text('0시간(일)');
					$('#txtMakeTime').text('0시간(일)');
					$('#txtBuyTime').text('0시간(일)');
					$('input:radio[name="gbn"]').attr('checked',false);
					$('input:radio[name="gbn"]:input[value="'+val['gbn']+'"]').attr('checked',true);
					$('input:radio[name="time"]:input[value="'+val['lvl']+'"]').attr('checked',true);
					$('#overTime').val(__num2str(val['overtime']));

					if ($('input:radio[name="gbn"]:checked').length > 0){
						$('#divTime').show();
						$('#divText').hide();
						$('input:radio[name="gbn"]:checked').click();
					}else{
						$('#divTime').hide();
						$('#divText').show();
					}

					$('input:radio[name="time"]:checked').click();
					break;

				case '3':
					$('#txtOverTime').text('0일');
					$('#txtMakeTime').text('0일');
					$('#txtBuyTime').text('0일');
					$('input:radio[name="gbn"]').attr('checked',false);
					$('input:radio[name="gbn"]:input[value="'+val['gbn']+'"]').attr('checked',true);
					$('input:radio[name="gbn"]:checked').click();
					break;

				case '4':
					
					if(val['gbn']==3){
						$('#svcDiv_2').show();
						$('#svcDiv_1').hide();
						$('#loSvcDisTbl2').show();
					}else {
						$('#svcDiv_2').hide();
						$('#svcDiv_1').show();
						$('#loSvcDisTbl').show();
					}

					$('#txtOverTime').text('0시간');
					$('#txtMakeTime').text('0시간');
					$('#txtBuyTime').text('0시간');
					if(val['gbn']==3){
						$('input:radio[name="stnd"]:input[value=""]').attr('checked',true);
						$('input:radio[name="addPay1"]:input[value="0"]').attr('checked',true);
						$('input:checkbox[name="addPay2"]').attr('checked',false);
						$('input:radio[name="stnd"]:input[value="'+val['gbn']+'"]').attr('checked',true);
						$('input:radio[name="addPay1"]:input[value="'+val['addPay1']+'"]').attr('checked',true);
						$('#sidoTime').val(__num2str(val['addTime1']));
						$('#jachTime').val(__num2str(val['addTime2']));
						$('#overSupport').val(__num2str(val['overPay']));

						$('input:radio[name="stnd"]:checked').click();
						$('#sidoTime').change();
						$('#jachTime').change();
						$('#overSupport').change();

						var add2 = val['addPay2'].split('/');
						
						for(var i=1; i<add2.length; i++)
							$('input:checkbox[name="addPay2"]:input[value="'+add2[i]+'"]').attr('checked',true);
					}else {
						$('input:radio[name="stnd"]:input[value=""]').attr('checked',true);
						$('input:radio[name="addPay1"]:input[value="0"]').attr('checked',true);
						$('input:checkbox[name="addPay2"]').attr('checked',false);
						$('input:radio[name="stnd"]:input[value="'+val['gbn']+'"]').attr('checked',true);
						$('input:radio[name="addPay1"]:input[value="'+val['addPay1']+'"]').attr('checked',true);
						$('#sidoTime').val(__num2str(val['addTime1']));
						$('#jachTime').val(__num2str(val['addTime2']));
						$('#overSupport').val(__num2str(val['overPay']));

						$('input:radio[name="stnd"]:checked').click();
						$('#sidoTime').change();
						$('#jachTime').change();
						$('#overSupport').change();

						var add2 = val['addPay2'].split('/');
						
						for(var i=1; i<add2.length; i++)
							$('input:checkbox[name="addPay2"]:input[value="'+add2[i]+'"]').attr('checked',true);
					}

					lfInitAddPay();
					break;

				default:

			}

			$('#btnDelete').css('display',val['calendarCnt'] > 0 || val['ynLoad'] == 'N' ? 'none' : '');
			$('#seq').val(__str2num(val['seq']));
		}
	}).responseXML;
}

function lfSaveData(){
	var para = '';

	switch($('#svcCd').val()){
		case '1':
			if ($('input:radio[name="time"]:checked').length == 0){
				alert('서비스 시간을 선택하여 주십시오.');
				return;
			}

			para = 'lvl='+$('input:radio[name="time"]:checked').val()
				 + '&time='+$('input:radio[name="time"]:checked').attr('value1')
				 + '&over='+$('#overTime').val();

			break;

		case '2':
			if ($('input:radio[name="gbn"]:checked').length == 0){
				alert('서비스구분을 선택하여 주십시오.');
				return;
			}

			if ($('input:radio[name="time"]:checked').length == 0){
				alert('서비스 시간을 선택하여 주십시오.');
				return;
			}

			para = 'gbn='+$('input:radio[name="gbn"]:checked').val()
				 + '&val='+$('input:radio[name="gbn"]:checked').attr('value2')
				 + '&lvl='+$('input:radio[name="time"]:checked').val()
				 + '&time='+$('input:radio[name="time"]:checked').attr('value1')
				 + '&over='+$('#overTime').val();

			break;

		case '3':
			if ($('input:radio[name="gbn"]:checked').length == 0){
				alert('서비스 구분을 선택하여 주십시오.');
				return;
			}

			para = 'gbn='+$('input:radio[name="gbn"]:checked').val()
				 + '&days='+$('input:radio[name="gbn"]:checked').attr('value1');

			break;

		case '4':
			if ($('input:radio[name="stnd"]:checked').length == 0){
				alert('기본급여을 선택하여 주십시오.');
				return;
			}

			var add2 = '';

			$('input:checkbox[name="addPay2"]:checked').each(function(){
				add2 += ('/'+$(this).val());
			});

			para = 'gbn='+$('input:radio[name="stnd"]:checked').val()
				 + '&val='+$('input:radio[name="stnd"]:checked').attr('value2')
				 + '&lvl='+$('input:radio[name="stnd"]:checked').attr('value1')
				 + '&addPay1='+$('input:radio[name="addPay1"]:checked').attr('value')
				 + '&addPay2='+add2
				 + '&addPay='+__str2num($('#addTot').attr('value'))
				 + '&addTime='+__str2num($('#addTime').attr('value'))
				 + '&sidoTime='+__str2num($('#sidoTime').val())
				 + '&jachTime='+__str2num($('#jachTime').val())
				 + '&makePay='+__str2num($('#stndTot').attr('value'))
				 + '&makeTime='+__str2num($('#stndTime').attr('value'))
				 + '&overPay='+__str2num($('#overTot').attr('value'))
				 + '&overTime='+__str2num($('#overTime').attr('value'))
				 + '&totalPay='+__str2num($('#totalTot').attr('value'))
				 + '&totalTime='+__str2num($('#totalTime').attr('value'));
			break;
	}

	//if ('<?=$debug;?>' == '1'){
	//	alert(para);
	//}

	$.ajax({
		type: 'POST'
	,	url : './iljung_fun.php'
	,	data: {
			code  : $('#code').val()
		,	jumin : $('#jumin').val()
		,	svcCd : $('#svcCd').val()
		,	year  : $('#year').val()
		,	month : $('#month').val()
		,	seq   : $('#seq').val()
		,	mode  : 301
		,	para  : para
		}
	,	success: function (result){
			switch(result){
				case '1':
					alert('정삭적으로 처리되었습니다.');
					break;

				case '9':
					alert('바우처 새성중 오류가 발생하였습니다.\n\n잠시 후 다시 시도하여 주십시오.');
					break;

				default:
					alert(result);
			}
		}
	}).responseXML;
}

function lfDelData(){
	$.ajax({
		type: 'POST'
	,	url : './iljung_fun.php'
	,	data: {
			code  : $('#code').val()
		,	jumin : $('#jumin').val()
		,	svcCd : $('#svcCd').val()
		,	year  : $('#year').val()
		,	month : $('#month').val()
		,	seq   : $('#seq').val()
		,	mode  : 901
		}
	,	success: function (result){
			switch(result){
				case '1':
					alert('정삭적으로 처리되었습니다.');
					lfLoadData();
					break;

				case '9':
					alert('바우처 새성중 오류가 발생하였습니다.\n\n잠시 후 다시 시도하여 주십시오.');
					break;

				default:
					alert(result);
			}
		}
	}).responseXML;
}

function lfWinResize(){
	var lbLoadYn = false;
	var svcCd = $('#svcCd').val();

	switch(svcCd){
		case '1':
			if($('#loSvcOverHtml').text() != ''){
				if ($('#loSvcTimeHtml').text() != '' && $('#loSvcOverHtml').text() != '') lbLoadYn = true;
			}else {
				if ($('#loSvcTimeHtml').text() != '') lbLoadYn = true;
			}
			break;

		case '2':
			if ($('#loSvcGbnHtml').text() != '' && $('#loSvcTimeHtml').text() != '' && $('#loSvcOverHtml').text() != '') lbLoadYn = true;
			break;

		case '3':
			if ($('#loSvcGbnHtml').text() != '') lbLoadYn = true;
			break;

		case '4':
			if ($('#loSvcStndHtml').text() != '' /*&& $('#loSvcAddHtml').text() != '' */) lbLoadYn = true;
			break;
	}
	
	
	if (lbLoadYn){
		lfLoadData();
		__window_resize(800, $('#lastObj').offset().top+78);

		clearInterval(loTimer);
		loTimer = null;
	}
}
</script>
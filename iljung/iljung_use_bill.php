<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}


	/*********************************************************

		사용 테이블
		 - master : svc_use_bill
		 - detail : svc_use_bill_itme



		PDF 출력시

		var arguments = 'root=iljung'
					  + '&dir=P'
					  + '&fileName=use_bill'
					  + '&fileType=pdf'
					  + '&target=show.php'
					  + '&code='org_no
					  + '&jumin='bill_jumin
					  + '&type='bill_svc_nm
					  + '&seq='bill_seq
		__printPDF(arguments);

	*********************************************************/


	/*********************************************************

		변수

	*********************************************************/
	$code  = $_SESSION['userCenterCode'];
	$kind  = '0';
	$k_cd  = $conn->center_code($code, '0');
	$k_nm  = $conn->center_name($code);
	$today = date('Y-m-d', mktime());


	echo '<div class=\'title title_border\'>재가서비스 제공계획서</div>';

	echo '<form id=\'f\' name=\'f\' method=\'post\'>
		  <table class=\'my_table\' style=\'width:100%;\'>
			<colgroup>
				<col width=\'90\'>
				<col width=\'150\'>
				<col width=\'90\'>
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class=\'head bold last\' colspan=\'4\'>기관정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class=\'center bottom\'>기관기호</th>
					<td class=\'left bottom\'>'.$k_cd.'</td>
					<th class=\'center bottom\'>기관명</th>
					<td class=\'left last bottom\'>'.$k_nm.'</td>
				</tr>
			</tbody>
		  </table>';



	echo '<table class=\'my_table\' style=\'width:100%; border-top:2px solid #0e69b0;\'>
			<colgroup>
				<col width=\'70\'>
				<col width=\'20\'>
				<col width=\'100\'>
				<col width=\'70\'>
				<col width=\'120\'>
				<col width=\'70\'>
				<col width=\'100\'>
				<col width=\'70\'>
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class=\'head bold last\' colspan=\'9\'>수급자정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class=\'center\'>수급자명</th>
					<td class=\'left last\' style=\'padding-top:1px;\'><span class=\'btn_pack find\' onclick=\'_findClientInfo(__findClient("'.$code.'","'.$kind.'"));\'></span></td>
					<td class=\'left\'><span id=\'strName\' class=\'bold\'></span></td>
					<th class=\'center\'>주민번호</th>
					<td class=\'left\'><span id=\'strJumin\' class=\'bold\'></span></td>
					<th class=\'center\'>등급</th>
					<td class=\'left\'><span id=\'strLevel\' class=\'bold\'></span></td>
					<th class=\'center\'>인정번호</th>
					<td class=\'left last\'>
						<span id=\'strInjungNo\' class=\'bold\'></span>
						<span id=\'strRate\' style=\'display:none;\'></span>
					</td>
				</tr>
			</tbody>
		  </table>';

	echo '<div class=\'my_border_blue\' style=\'margin:10px;\'>
			<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'70\'>
					<col width=\'100\'>
					<col width=\'70\'>
					<col width=\'70\'>
					<col>
				</colgroup>
				<thead>
					<tr>
						<th class=\'head bold last\' colspan=\'7\'>급여이용 신청내용</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class=\'center\'>급여종류</th>
						<td class=\'center\'><input id=\'svcType\' name=\'svcType\' type=\'text\' value=\'방문재가\' style=\'width:100%;\'></td>
						<th class=\'center\'>이용기간</th>
						<td class=\'center\'><input class=\'yymm\' id=\'strDate\' ></td>
						<td class=\'right last\'>
							<span class=\'btn_pack m\'><button type=\'button\' onclick=\'showPDF();\'>출력</button></span>
						</td>
					</tr>
				</tbody>
			  </table>

			  <table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'100\'>
					<col width=\'20\'>
					<col width=\'200\'>
					<col width=\'90\'>
					<col width=\'90\'>
					<col width=\'90\'>
					<col width=\'90\'>
					<col>
				</colgroup>
				<thead>
					<tr>
						<th class=\'head\'>서비스 종류</th>
						<th class=\'head\' colspan=\'2\'>서비스 내용</th>
						<th class=\'head\'>수가</th>
						<th class=\'head\'>횟수/월</th>
						<th class=\'head\'>금액/월</th>
						<th class=\'head\'>본인부담금/월</th>
						<th class=\'head last\'>비고</th>
					</tr>
				</thead>
				<tbody>';
	
	


	$svcTitle[0] = '방문요양';
	$svcTitle[1] = '방문목욕';
	$svcTitle[2] = '방문간호';

	$i = 0;
	$j = 0;
	$svcCd = '';
	$svcID = 1;
	$limit_rows = 18;
	while(true){
		if ($i % 6 == 0){
			if ($i > 0){
				echo summly($svcCd);
			}

			switch($j){
				case 0:
					$svcCd = '200';
					$svcID = 1;
					break;

				case 1:
					$svcCd = '500';
					$svcID = 1;
					break;

				case 2:
					$svcCd = '800';
					$svcID = 1;
					break;
			}

			echo '<tr>
				  <th class=\'center\' rowspan=\'7\'>'.$svcTitle[$j].'</th>';

			$j ++;
		}else{
			echo '<tr>';
		}

		echo '<td class=\'left last\' style=\'padding-top:1px;\'><span class=\'btn_pack find\' onclick=\'_chkSugaInfo("'.$code.'","'.$svcCd.'","'.$svcID.'")\'></span></td>
			  <td class=\'left\'>
				<span id=\'strSugaCode'.$svcCd.'_'.$svcID.'\' class=\'sugaCode'.$svcCd.'\' style=\'display:none\'></span>
				<span id=\'strSugaName'.$svcCd.'_'.$svcID.'\' class=\'sugaName'.$svcCd.'\'></span>
			  </td>
			  <td class=\'right\'><span id=\'strSugaCost'.$svcCd.'_'.$svcID.'\' class=\'sugaCost'.$svcCd.'\'></span></td>
			  <td class=\'center\'><input id=\'objSugaCnt'.$svcCd.'_'.$svcID.'\' name=\'objSugaCnt'.$svcCd.'_'.$svcID.'\' type=\'text\' value=\'0\' class=\'number sugaCnt'.$svcCd.'\' style=\'width:100%;\' onchange=\'sumSvcSuga("'.$svcCd.'","'.$svcID.'");\'></td>
			  <td class=\'right\'><span id=\'strSugaAmt'.$svcCd.'_'.$svcID.'\' class=\'sugaAmt'.$svcCd.'\'></span></td>
			  <td class=\'right\'>
				<span id=\'strSugaMy'.$svcCd.'_'.$svcID.'\' class=\'sugaMy'.$svcCd.'\'></span>
				<span id=\'myPay'.$svcCd.'_'.$svcID.'\' style=\'display:none;\'></span>
			  </td>
			  <td class=\'left\'></td>
			  </tr>';

		$i ++;
		$svcID ++;

		if ($i >= $limit_rows) break;
	}

	echo summly($svcCd);
	echo summly('',2);

	echo '		</tbody>
			  </table>
		  </div>';

	echo '<input id=\'code\' name=\'code\' type=\'hidden\' value=\''.$code.'\'>
		  <input id=\'kind\' name=\'kind\' type=\'hidden\' value=\''.$kind.'\'>
		  <input id=\'jumin\' name=\'jumin\' type=\'hidden\' value=\'\'>
		  <input id=\'lbTestMode\' name=\'lbTestMode\' type=\'hidden\' value=\''.$lbTestMode.'\'>

		  </form>';


	/*********************************************************

		소계

	*********************************************************/
	function summly($svcCD, $sumType = 1){
		if ($sumType == 1){
			$sumCols  = 2;
			$sumTitle = '소계';
			$sumClass = ' sum_sub';
		}else{
			$sumCols  = 3;
			$sumTitle = '합계';
			$sumClass = ' sum';
		}

		if ($sumType != 1){
			$class = ' bottom';
		}

		$html = '<tr>
					<th class=\'right bold'.$class.'\' colspan=\''.$sumCols.'\'>'.$sumTitle.'</th>
					<td class=\'right'.$sumClass.$class.'\'></td>
					<td class=\'right'.$sumClass.$class.'\'><span id=\'strSumCnt'.$svcCD.'\'>0</span></td>
					<td class=\'right'.$sumClass.$class.'\'><span id=\'strSumAmt'.$svcCD.'\'>0</span></td>
					<td class=\'right'.$sumClass.$class.'\'><span id=\'strSumMy'.$svcCD.'\'>0</span></td>
					<td class=\'left'.$sumClass.$class.'\'></td>
				 </tr>';

		return $html;
	}

	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<script type='text/javascript'>
$(document).ready(function(){
	__init_form(document.f);
});


/*********************************************************

	서비스 합계

*********************************************************/
function sumSvcSuga(svcCD, svcID){
	$('#strSugaAmt'+svcCD+'_'+svcID).text( __num2str(__str2num($('#strSugaCost'+svcCD+'_'+svcID).text()) * __str2num($('#objSugaCnt'+svcCD+'_'+svcID).attr('value'))) );
	$('#strSugaMy'+svcCD+'_'+svcID).text( __num2str(cutOff(__str2num($('#strSugaAmt'+svcCD+'_'+svcID).text()) * __str2num($('#strRate').text()) / 100)) );
	$('#myPay'+svcCD+'_'+svcID).text( __num2str(cutOff(__str2num($('#strSugaCost'+svcCD+'_'+svcID).text()) * __str2num($('#strRate').text()) / 100)) );

	var cnt = 0;
	var amt = 0;
	var my  = 0;

	$('.sugaCnt'+svcCD).each(function(){
		cnt += __str2num($(this).attr('value'));
	});

	$('.sugaAmt'+svcCD).each(function(){
		amt += __str2num($(this).text());
	});

	$('.sugaMy'+svcCD).each(function(){
		my += __str2num($(this).text());
	});

	$('#strSumCnt'+svcCD).text( __num2str(cnt) );
	$('#strSumAmt'+svcCD).text( __num2str(amt) );
	$('#strSumMy'+svcCD).text( __num2str(my) );

	sumTotalSuga();
}

function sumTotalSuga(){
	var cnt = 0;
	var amt = 0;
	var my  = 0;

	$('.sugaCnt200').each(function(){ cnt += __str2num($(this).attr('value')); });
	$('.sugaCnt500').each(function(){ cnt += __str2num($(this).attr('value')); });
	$('.sugaCnt800').each(function(){ cnt += __str2num($(this).attr('value')); });
	$('.sugaAmt200').each(function(){ amt += __str2num($(this).text()); });
	$('.sugaAmt500').each(function(){ amt += __str2num($(this).text()); });
	$('.sugaAmt800').each(function(){ amt += __str2num($(this).text()); });
	$('.sugaMy200').each(function(){ my += __str2num($(this).text()); });
	$('.sugaMy500').each(function(){ my += __str2num($(this).text()); });
	$('.sugaMy800').each(function(){ my += __str2num($(this).text()); });

	$('#strSumCnt').text( __num2str(cnt) );
	$('#strSumAmt').text( __num2str(amt) );
	$('#strSumMy').text( __num2str(my) );
}


/*********************************************************

	수가기준일자의 변경

*********************************************************/
function chkStndSugaDate(){
	//stndSugaDt
	var svcCD, sugaIf;

	for(var i=1; i<=3; i++){
		switch(i){
			case 1: svcCD = '200'; break;
			case 2: svcCD = '500'; break;
			case 3: svcCD = '800'; break;
		}

		for(var j=1; j<=6; j++){
			if ($('#strSugaCode'+svcCD+'_'+j).text() != ''){
				sugaIf = __getSugaInfo($('#code').attr('value'), $('#strSugaCode'+svcCD+'_'+j).text(), $('#stndSugaDt').attr('value'));

				var arr = sugaIf.split('&');
				var val = new Array();

				for(var k=0; k<arr.length; k++){
					var tmp = arr[k].split('=');

					val[tmp[0]] = tmp[1];
				}

				$('#strSugaName'+svcCD+'_'+j).text( val['name'] );
				$('#strSugaCost'+svcCD+'_'+j).text( __num2str(val['cost']) );

				sumSvcSuga(svcCD, j);
			}
		}
	}

	sumTotalSuga();
}


/*********************************************************

	출력

*********************************************************/
function showPDF(){
	var svcCD, arguments;

	arguments  = 'code='+$('#code').attr('value');
	arguments += '&kind='+$('#kind').attr('value');
	arguments += '&jumin='+$('#jumin').attr('value');
	//arguments += '&date='+$('#stndSugaDt').attr('value');
	arguments += '&type='+$('#svcType').attr('value');

	for(var i=1; i<=3; i++){
		switch(i){
			case 1: svcCD = '200'; break;
			case 2: svcCD = '500'; break;
			case 3: svcCD = '800'; break;
		}

		for(var j=1; j<=6; j++){
			if ($('#strSugaCode'+svcCD+'_'+j).text() != ''){
				arguments += '&svcCD[]='+svcCD;
				arguments += '&sugaCode[]='+$('#strSugaCode'+svcCD+'_'+j).text();
				arguments += '&sugaName[]='+$('#strSugaName'+svcCD+'_'+j).text();
				arguments += '&sugaCost[]='+$('#strSugaCost'+svcCD+'_'+j).text();
				arguments += '&sugaCnt[]='+$('#objSugaCnt'+svcCD+'_'+j).attr('value');
				arguments += '&sugaMy[]='+$('#strSugaMy'+svcCD+'_'+j).text();
				arguments += '&myPay[]='+$('#myPay'+svcCD+'_'+j).text();
			}
		}
	}

	try{
		$.ajax({
			type: 'POST',
			url : './iljung_use_bill_save.php',
			data: {
				'arguments':arguments
			},
			beforeSend: function (){
			},
			success: function (xmlHttp){
				var seq = xmlHttp;

				switch(seq){
					case 'error_1':
						alert('출력중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
						return;
						break;

					case 'error_2':
						alert('출력중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
						return;
						break;
				}

				var arguments = 'root=iljung'
					  + '&dir=P'
					  + '&fileName=iljung_use_bill'
					  + '&fileType=pdf'
					  + '&target=show.php'
					  + '&code='+$('#code').attr('value')
					  + '&jumin='+$('#jumin').attr('value')
					  + '&type='+$('#svcType').attr('value')
					  + '&date='+$('#strDate').attr('value')
					  + '&seq='+seq
				__printPDF(arguments);
			}
			,
			error: function (){
			}
		}).responseXML;
	}catch(e){
		alert('출력중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
	}
}

</script>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$svcCd = $_POST['svcCd'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($svcCd == '0' || $svcCd == '4'){
		//기관 제공서비스 및 목욕요율 정보
		$sql = 'select m00_kupyeo_1 as care_yn
				,      m00_kupyeo_2 as bath_yn
				,      m00_kupyeo_3 as nurs_yn
				,      m00_muksu_yul1 as rate1
				,      m00_muksu_yul2 as rate2
				  from m00center
				 where m00_mcode = \''.$code.'\'
				   and m00_mkind = \''.$svcCd.'\'';
		$row = $conn->get_array($sql);

		$ynCare  = ($row['care_yn'] == 'Y' ? 'Y' : 'N'); //방문요양 여부
		$ynBath  = ($row['bath_yn'] == 'Y' ? 'Y' : 'N'); //방문목욕 여부
		$ynNurs  = ($row['nurs_yn'] == 'Y' ? 'Y' : 'N'); //방문간호 여부
		$liRate1 = floatval($row['rate1']); //수당요율
		$liRate2 = floatval($row['rate2']); //수당요율

		unset($row);
	}

	//담당요양보호사
	$sql = 'select m03_yoyangsa1 as mem_cd1
			,      m03_yoyangsa1_nm as mem_nm1
			,      m03_yoyangsa2 as mem_cd2
			,      m03_yoyangsa2_nm as mem_nm2
			,      m03_partner as partner_yn
			,      m03_stat_nogood as stat_yn
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			   and m03_mkind = \''.$svcCd.'\'
			   and m03_jumin = \''.$jumin.'\'';
	$row = $conn->get_array($sql);

	$lsMemCd1 = $row['mem_cd1'];
	$lsMemNm1 = (!empty($lsMemCd1) ? $row['mem_nm1'] : '');
	$lsMemCd2 = $row['mem_cd2'];
	$lsMemNm2 = (!empty($lsMemCd1) ? $row['mem_nm2'] : '');
	$liMemAge = $myF->issToAge($lsMemCd1);

	if ($svcCd == '0'){
		$ynPartner = $row['partner_yn']; //주요양보호사 배우자 여부
		$ynStatNot = $row['stat_yn'];    //상태이상여부
	}else{
		$ynPartner = 'N';
		$ynStatNot = 'N';
	}

	unset($row);

	//요양보호사 일정리스트
	for($m=1; $m<=2; $m++){
		if ($m == 1)
			$lsMemCd = $lsMemCd1;
		else
			$lsMemCd = $lsMemCd2;

		if (!empty($lsMemCd)){
			$sql = 'select cast(substring(t01_sugup_date,7) as unsigned) as dt
					,      t01_sugup_fmtime as f_time
					,      t01_sugup_totime as t_time
					  from t01iljung
					 where t01_ccode   = \''.$code.'\'
					   and t01_jumin  != \''.$jumin.'\'
					   and t01_mem_cd1 = \''.$lsMemCd.'\'
					   and t01_del_yn  = \'N\'
					   and left(t01_sugup_date,6) = \''.$year.$month.'\'
					 union all
					select cast(substring(t01_sugup_date,7) as unsigned)
					,      t01_sugup_fmtime
					,      t01_sugup_totime
					  from t01iljung
					 where t01_ccode   = \''.$code.'\'
					   and t01_jumin  != \''.$jumin.'\'
					   and t01_mem_cd2 = \''.$lsMemCd.'\'
					   and t01_del_yn  = \'N\'
					   and left(t01_sugup_date,6) = \''.$year.$month.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				$loArr[$row['dt']] .= $row['f_time'].'/'.$row['t_time'].';';
			}

			$conn->row_free();

			if (is_array($loArr)){
				foreach($loArr as $i => $row){
					$lsMemCalendar[$m] .= (!empty($lsMemCalendar[$m]) ? '&' : '').$i.'='.$row;
				}
			}
		}
	}

	if ($svcCd == '0'){
		//동거가족 제한
		if ($year.$month >= '201108'){
			if (($ynPartner == 'Y' && $liMemAge >=65) || $ynStatNot == 'Y'){
				$ynFamily90 = 'Y';
				$liFamilyLimitCnt = $myF->lastDay($year,$month);
			}else{
				$ynFamily90 = 'N';
				$liFamilyLimitCnt = 20;
			}
			$liCareLimitCnt = 1;
		}else{
			$ynFamily90 = 'Y';
			$liFamilyLimitCnt = 31;
			$liCareLimitCnt = 99;
		}

		//가족 요양보호사
		$ynMemFamily = 'N';
		if ($svcCd == '0'){
			$sql = 'select cf_mem_cd as cd
					,      cf_mem_nm as nm
					,      cf_kind as kind
					  from client_family
					 where org_no   = \''.$code.'\'
					   and cf_jumin = \''.$jumin.'\'';
			$row = $conn->_fetch_array($sql,'cd');

			if (!empty($row[$lsMemCd1]['kind'])){
				$ynMemFamily = 'Y';
			}
		}

		unset($row);

		$ynMakeSvc = 'Y';
	}else if ($svcCd >= '1' && $svcCd <= '4'){//바우처
		$sql = 'select count(*)
				  from voucher_make
				 where org_no        = \''.$code.'\'
				   and voucher_kind  = \''.$svcCd.'\'
				   and voucher_jumin = \''.$jumin.'\'
				   and voucher_yymm  = \''.$year.$month.'\'
				   and del_flag      = \'N\'';
		$liMakeVouCnt = $conn->get_data($sql);

		if ($liMakeVouCnt > 0){
			$ynMakeSvc = 'Y';
		}else{
			$ynMakeSvc = 'N';
		}
	}else{
		$ynMakeSvc = 'Y';
	}

	//개별 수당금액
	$sql = 'select jumin
			,      extra500_1
			,      extra500_2
			,      extra500_3
			,      extra800_1
			,      extra800_2
			,      extra800_3
			  from mem_extra
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$lsMemCd1.'\'';

	if (!empty($lsMemCd1)){
		$sl = $sql.' and jumin  = \''.$lsMemCd1.'\'';
		$laExtraMem1 = $conn->get_array($sl);
	}

	if (!empty($lsMemCd2)){
		$sl = $sql.' and jumin  = \''.$lsMemCd2.'\'';
		$laExtraMem2 = $conn->get_array($sl);
	}
?>
	<div id="loExtraMem1" B3="<?=$laExtraMem1['extra500_1'];?>" B2="<?=$laExtraMem1['extra500_3'];?>" B1="<?=$laExtraMem1['extra500_2'];?>" N1="<?=$laExtraMem1['extra800_1'];?>" N2="<?=$laExtraMem1['extra800_2'];?>" N3="<?=$laExtraMem1['extra800_3'];?>" style="display:none;"></div>
	<div id="loExtraMem2" B3="<?=$laExtraMem2['extra500_1'];?>" B2="<?=$laExtraMem2['extra500_3'];?>" B1="<?=$laExtraMem2['extra500_2'];?>" N1="<?=$laExtraMem2['extra800_1'];?>" N2="<?=$laExtraMem2['extra800_2'];?>" N3="<?=$laExtraMem2['extra800_3'];?>" style="display:none;"></div>
	<div id="tblSuga"><?
		if ($svcCd == '0' || $svcCd == '4')
			$liIdx = '0';
		else if ($svcCd == '1' || $svcCd == '2' || $svcCd == '3' || $svcCd == 'A' || $svcCd == 'B' || $svcCd == 'C')
			$liIdx = '1';
		else
			$liIdx = '';

		$lsFileNm = './plan_suga_info_'.$liIdx.'.php';

		if (is_file($lsFileNm)){
			include_once($lsFileNm);
		}else{
			include_once('./plan_suga_info_error.php');
		}?>
	</div>
	<?
	include('./plan_suga_obj.php');
	include_once('../inc/_db_close.php');?>

	<script type="text/javascript">
		lbWinLoad = false;

		//케어구분 설정
		function lfSetCareGbn(){
			var lsPayKihd = $('#txtPayKind').val();
			var lsSvcKind = $('#txtSvcKind').val();
			var ynFamily  = $('#txtMemCd1').attr('ynFamily'); //가족여부

			var liMode = 1;

			if (lsSvcKind == '200'){
				//if (ynFamily == 'Y'){
				liMode = 2;
				//}
			}

			var lsOption = '<option value="1">일반</option>';

			if (liMode == 2){
				lsOption += '<option value="2">가족</option>';
			}

			lsOption += '<option value="3">비급여</option>';

			$('#txtPayKind').html(lsOption);

			if (ynFamily == 'Y'){
				$('#txtPayKind').val('2');
			}
		}

		//제공시간 설정 인텍스
		function lfGetKindGbn(){
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $('#txtSvcKind').val();
			var lsPayKind = $('#txtPayKind').val();
			var liKindGbn = 0;

			//방문요양
			if (lsSvcCd == '0'){
				if (lsSvcKind == '200'){
					if (lsPayKind == '1'){
						liKindGbn = 11; //일반케어
					}else if (lsPayKind == '2'){
						liKindGbn = 12; //동거케어
					}else{
						liKindGbn = 13; //비급여
					}
				}else if (lsSvcKind == '500'){
					liKindGbn = 21; //목욕
				}else{
					liKindGbn = 31; //간호
				}
			}else if (lsSvcCd == '4'){
				if (lsSvcKind == '500'){
					liKindGbn = 22;
				}else if (lsSvcKind == '800'){
					liKindGbn = 31;
				}
			}

			return liKindGbn;
		}

		//제공시간설정
		function lfSetSvcTime(aiKindGbn){
			var liKindGbn  = aiKindGbn;
			var ynFamily   = $('#txtMemCd1').attr('ynFamily');   //가족여부
			var ynFamily90 = $('#loClientIfno').attr('ynFamily90'); //90분가능여부

			if (ynFamily != 'Y'){
				ynFamily90 = 'N';
			}

			if (liKindGbn == 0){
				if (lbWinLoad) lfSetEndTime();
				return;
			}

			var option = '';

			switch(liKindGbn){
				case 11:
					if (ynFamily != 'Y'){
						option = '<option value="1">30분</option>'
							   + '<option value="2">60분</option>';
					}

					if (ynFamily90 != 'Y'){
						option += '<option value="3">90분</option>';
					}

					option += '<option value="4">120분</option>'
						   +  '<option value="5">150분</option>'
						   +  '<option value="6">180분</option>'
						   +  '<option value="7">210분</option>'
						   +  '<option value="8">240분</option>'
						   +  '<option value="9">270분이상</option>';
					break;

				case 12:
					option = '<option value="1">30분</option>'
						   + '<option value="2">60분</option>';
					if (ynFamily90 == 'Y'){
						option += '<option value="3">90분</option>';
					}
					break;

				case 13:
					option = '<option value="1">30분</option>'
						   + '<option value="2">60분</option>'
						   + '<option value="3">90분</option>'
						   + '<option value="4">120분</option>'
						   + '<option value="5">150분</option>'
						   + '<option value="6">180분</option>'
						   + '<option value="7">210분</option>'
						   + '<option value="8">240분</option>'
						   + '<option value="9">270분이상</option>';
					break;

				case 21:
					option = '<option value="3">미차량</option>'
						   + '<option value="2">가정내입욕</option>'
						   + '<option value="1">차량입욕</option>';
					break;

				case 22:
					option = '<option value="2">가정내입욕</option>'
						   + '<option value="1">차량입욕</option>';
					break;

				case 31:
					option = '<option value="1">30분미만</option>'
						   + '<option value="2">60분미만</option>'
						   + '<option value="3">60분이상</option>';
					break;
			}

			$('#txtSvcTime').html(option);

			if (lbWinLoad) lfSetEndTime(true);
		}

		//요양보호사 찾기
		function lfMemFind(aiIdx){
			var code     = $('#centerInfo').attr('value');
			var jumin    = $('#clientInfo').attr('value');
			var svcCd    = $('#planInfo').attr('svcCd');
			var memCd    = $('#txtMemCd1').attr('code')+','+$('#txtMemCd2').attr('code');
			var ynFamily = 'N';

			if ($('#txtPayKind').val() == '2') ynFamily = 'Y';

			var h = 400;
			var w = 600;
			var t = (screen.availHeight - h) / 2;
			var l = (screen.availWidth - w) / 2;

			var url    = '../inc/_find_person.php';
			var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
			var win    = window.open('about:blank', 'FIND_MEMBER', option);
				win.opener = self;
				win.focus();

			var parm = new Array();
				parm = {
					'type'     : 'member'
				,	'code'     : code
				,	'kind'     : svcCd
				,	'jumin'    : jumin
				,	'yoy'      : memCd
				,	'idx'	   : aiIdx
				,	'ynFamily' : ynFamily
				,	'year'     : $('#planInfo').attr('year')
				,	'month'    : $('#planInfo').attr('month')
				,	'return'   : 'lfMemFindResult'
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

			form.setAttribute('target', 'FIND_MEMBER');
			form.setAttribute('method', 'post');
			form.setAttribute('action', url);

			document.body.appendChild(form);

			form.submit();
		}

		function lfMemFindResult(asObj){
			var val = __parseStr(asObj);
			var lsSvcCd = $('#planInfo').attr('svcCd');
			var lsSvcKind = $('#txtSvcKind').val();

			$.ajax({
				type : 'POST'
			,	url  : './plan_family_yn.php'
			,	data : {
					'code'  : $('#centerInfo').attr('value')
				,	'jumin'	: $('#clientInfo').attr('value')
				,	'svcCd' : lsSvcCd
				,	'memCd' : val['jumin']
				}
			,	success: function(result){
					$('#txtMemCd'+val['idx']).val(val['name'])
						.attr('code',val['jumin'])
						.attr('ynFamily',result);

					//일정리스트
					lfMemCalendar(val['idx'],val['jumin']);

					//수당조회
					setTimeout('lfFindExtraMemPay('+val['idx']+')',10);

					//제공시간 설정
					if (lsSvcCd == '0'){
						//lfSetCareGbn();
						lfSetSvcTime(lfGetKindGbn());
					}else if (lsSvcCd == '4'){
						lfFindSuga();
					}
				}
			});
		}

		//요양보호사 일정리스트
		function lfMemCalendar(aiIdx,asMemCd){
			$.ajax({
				type : 'POST'
			,	url  : './plan_mem_calendar.php'
			,	data : {
					'code'  : $('#centerInfo').attr('value')
				,	'jumin'	: $('#clientInfo').attr('value')
				,	'svcCd' : $('#planInfo').attr('svcCd')
				,	'memCd' : asMemCd
				,	'year'  : $('#planInfo').attr('year')
				,	'month' : $('#planInfo').attr('month')
				}
			,	success: function(result){
					$('#txtMemCd'+aiIdx).attr('calendar',result);
				}
			});
		}

		//요양보호사 삭제
		function lfMemClear(aiIdx,abReturn){
			var lsSvcCd = $('#planInfo').attr('svcCd');

			$('#txtMemCd'+aiIdx)
				.attr('value','')
				.attr('code','')
				.attr('calendar','')
				.attr('ynFamily','N')
				.attr('ynPatner','N');

			$('#loExtraMem'+aiIdx)
				.attr('B1','0')
				.attr('B2','0')
				.attr('B3','0')
				.attr('N1','0')
				.attr('N2','0')
				.attr('N3','0');

			if (abReturn) return;

			//제공시간 설정
			if (lsSvcCd == '0'){
				lfSetCareGbn();
				lfSetSvcTime(lfGetKindGbn());
			}else if (lsSvcCd == '4'){
				lfFindSuga();
			}
		}

		//시간 자리수 수정
		function lfSetTimePos(aoObj){
			time = __str2num($(aoObj).val());

			if (time < 10){
				time = '0'+time;
			}

			$(aoObj).val(time);
		}

		//제공시간 수정
		function lfSetProcTime(abVal){
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $('#txtSvcKind').val();
			var lsPayKind = $('#txtPayKind').val();

			if (lsSvcKind == '500') return;

			var to = __str2num($('#txtToH').val()) * 60
				   + __str2num($('#txtToM').val());

			var from = __str2num($('#txtFromH').val()) * 60
				     + __str2num($('#txtFromM').val());

			if (from > to) to += (24 * 60);

			var time = to - from;

			var ynSetEndTime = 'N';

			if ((lsSvcCd == '0' && lsSvcKind == '200' && time % 30 != 0) ||
				(lsSvcCd == '4' && lsSvcKind == '200')){
				ynSetEndTime = 'Y';
			}

			if (lsSvcCd == '0' && lsSvcKind == '200'){
				if ($('#txtPayKind').val() == '2'){
					ynSetEndTime = 'Y';
				}
			}

			var svcTime = 0;
			var liTime  = 0;

			if (lsSvcKind == '200'){
				time = to - from;

				if (lsSvcCd == '0' && lsPayKind != '3'){
					//510분에서 540분으로 수정
					if ($('#planInfo').attr('year')+$('#planInfo').attr('month') >= '201301'){
						var liLimitTime = 540;
					}else{
						var liLimitTime = 510;
					}

					if (time > liLimitTime){
						time = liLimitTime;
						to = from + time;

						var hour = Math.floor(to / 60);
						var min  = to % 60;

						if (hour >= 24) hour = hour - 24;
						if (hour < 10) hour = '0'+hour;
						if (min < 10) min = '0'+min;

						$('#txtToH').val(hour);
						$('#txtToM').val(min);
					}
				}else{
					liTime = time % 60;

					if (liTime > 30){
						time = cut(time,30)+30;
					}else{
						time = cut(time,60);
					}
				}

				svcTime = Math.floor(time / 30);
			}else{
				time = to - from;

				if (time < 30)
					svcTime = 1;
				else if (time < 60)
					svcTime = 2;
				else
					svcTime = 3;
			}

			if (lsSvcCd == '0'){
				//최대 270분이상
				if (svcTime > 9) svcTime = 9;
			}

			if (lsSvcCd == '4' && lsSvcKind == '200'){
				$('#txtSvcTimeStr').attr('value', svcTime).text((svcTime * 30)+'분');
			}else{
				$('#txtSvcTime option[value="'+svcTime+'"]').attr('selected','selected');
			}

			if (ynSetEndTime == 'Y' && svcTime < 9){
				lfSetEndTime();
			}else{
				lfFindSuga();
			}
		}

		//서비스 종료시간 수정
		function lfSetEndTime(abSetTime){
			var lsSvcCd   = $('#planInfo').attr('svcCd');
			var lsSvcKind = $('#txtSvcKind').val();
			var lsPayKind = $('#txtPayKind').val();

			//제공시간
			var svcTime = 0;

			if (lsSvcCd == '0'){
				if (lsSvcKind == '200'){
					svcTime = __str2num($('#txtSvcTime').val()) * 30;
				}else if (lsSvcKind == '500'){
					svcTime = 60;
				}else{
					svcTime = __str2num($('#txtSvcTime').val()) * 30 - 1;
				}
			}else if (lsSvcCd == '3'){
				svcTime = 60 * 8;
			}else if (lsSvcCd == '4'){
				if (lsSvcKind == '200'){
					if (__str2num($('#txtSvcTimeStr').attr('value')) == 0){
						svcTime = 60;
					}else{
						svcTime = __str2num($('#txtSvcTimeStr').attr('value')) * 30;
					}
				}else if (lsSvcKind == '500'){
					svcTime = 60;
				}else{
					svcTime = __str2num($('#txtSvcTime').val()) * 30 - 1;
				}
			}else{
				svcTime = 60;
			}

			if ($('#txtFromH').val() == '') $('#txtFromH').val('00');
			if ($('#txtFromM').val() == '') $('#txtFromM').val('00');

			//시작시간
			var from = __str2num($('#txtFromH').val()) * 60
				     + __str2num($('#txtFromM').val());

			//종료시간
			//기존에 30분단위 계산을 분단위로 전환
			//var to = from + svcTime;
			var to = __str2num($('#txtToH').val()) * 60
				   + __str2num($('#txtToM').val());

			//종료시간이 없는 경우, 가족케어, 서비스 및 케어구분 변경시 종료시간 강제 설정
			if (to == 0 || lsPayKind == '2' || abSetTime){
				to = from + svcTime;
			}

			if (to == 0) return;

			if ($('#txtFromH').val() != '' && $('#txtFromM').val() != ''){
				var hour = Math.floor(to / 60);
				var min  = to % 60;
			}else{
				var hour = 0;
				var min  = 0;
			}

			if (hour >= 24) hour = hour - 24;
			if (hour < 10) hour = '0'+hour;
			if (min < 10) min = '0'+min;

			$('#txtToH').val(hour);
			$('#txtToM').val(min);

			if ($('#txtFromH').val() != '' &&
				$('#txtFromM').val() != '' &&
				$('#txtToH').val() != '' &&
				$('#txtToM').val() != ''){
				lfFindSuga();
			}
		}

		//수가조회
		function lfFindSuga(){
			var lsSvcCd    = $('#planInfo').attr('svcCd');
			var lsSvcKind  = $('#txtSvcKind').val();
			var lsPayKind  = '';
			var ynFamily   = 'N';
			var lsBathKind = '';
			var liMemCnt   = 1;

			if (lsSvcCd == '0'){
				lsPayKind  = $('#txtPayKind').val();
				ynFamily   = (lsPayKind == '2' ? 'Y' : 'N');
				lsBathKind = $('#txtSvcTime').val();
			}else if (lsSvcCd == '4'){
				lsPayKind  = $('#txtPayKind').val();
				ynFamily   = 'N';
				lsBathKind = $('#txtSvcTime').val();

				if (lsSvcKind == '200'){
					liMemCnt = 0;

					if ($('#txtMemCd1').attr('code') != '') liMemCnt ++;
					if ($('#txtMemCd2').attr('code') != '') liMemCnt ++;
				}
			}else{
				if ($('#txtPayKind').attr('checked')){
					lsPayKind = 'Y';
				}else{
					lsPayKind = 'N';
				}
			}

			$.ajax({
				type : 'POST'
			,	async: false
			,	url  : '../find/find_suga.php'
			,	data : {
					'code'     : $('#centerInfo').attr('value')
				,	'svcCd'    : lsSvcCd
				,	'svcKind'  : $('#txtSvcKind').val()
				,	'date'     : $('#planInfo').attr('year')+$('#planInfo').attr('month')
				,	'fromTime' : $('#txtFromH').val()+$('#txtFromM').val()
				,	'toTime'   : $('#txtToH').val()+$('#txtToM').val()
				,	'ynFamily' : ynFamily
				,	'bathKind' : lsBathKind
				,	'svcVal'   : $('#infoClient').attr('svcVal')
				,	'svcLvl'   : $('#infoClient').attr('svcLvl')
				,	'memCnt'   : liMemCnt
				}
			,	success: function(result){
					//if ('<?=$debug;?>' == '1'){
					//	$('#loSuga').html(result).show();
					//}

					var val = __parseStr(result);

					if (!result) return;

					if (lsSvcCd == 'A' || lsSvcCd == 'B' || lsSvcCd == 'C'){
						val['cost'] = __str2num($('#infoClient').attr('svcCost'));
						val['costHoliday'] = val['cost'];
						val['costTotal']   = val['cost'] * val['procTime'];
					}

					$('#loSuga')
						.attr('code',val['code']) //수가코드
						.attr('name',val['name']) //수가명
						.attr('cost',val['cost']) //수가
						.attr('costEvening',val['costEvening']) //연장할증금액
						.attr('costNight',val['costNight']) //야간할증금액
						.attr('costTotal',val['costTotal']) //총금액
						.attr('sudangPay',val['sudangPay']) //수당
						.attr('timeEvening',val['timeEvening']) //연장시간
						.attr('timeNight',val['timeNight']) //야간시간
						.attr('ynEvening',val['ynEvening']) //연장여부
						.attr('ynNight',val['ynNight']) //야간여부
						.attr('ynHoliday',val['ynHoliday']) //휴일여부
						.attr('costBipay',val['costBipay']) //비급여수가
						.attr('costHoliday',val['costHoliday']) //휴일할증수가
						.attr('procTime',val['procTime']) //제공시간
						.attr('hour',val['hour'] ? val['hour'] : 0) //기준시간
						.attr('hourNight',val['hourNight'] ? val['hourNight'] : 0) //연장시간
						.attr('holidayHour',val['holidayHour'] ? val['holidayHour'] : 0) //휴일기준시간
						.attr('holidayHourNight',val['holidayHourNight'] ? val['holidayHourNight'] : 0); //휴일연장시간

					if (lsSvcCd == '0' || lsSvcCd == '4'){
						var obj = null;

						if (lsSvcKind == '200'){
							obj = $('#loSuga1');
						}else{
							obj = $('#loSuga2');
						}

						$('#lblSugaNm', $(obj)).text(val['name']); //수가명
						$('#lblSugaCost', $(obj)).text(__num2str(val['cost'])); //수가

						if (lsSvcKind != '200'){
							$('#txtExtraPay', $(obj)).val(__num2str(val['sudangPay']));

							//수당입력
							if (lsSvcKind == '500'){
								$('#txtBathPay').val(__num2str(val['sudangPay']));
							}else if (lsSvcKind == '800'){
								$('#txtNursePay').val(__num2str(val['sudangPay']));
							}
						}

						if (lsPayKind == '1' || lsPayKind == '2'){
							if (lsSvcCd == '0'){
								$('#lblSugaEveing', $(obj)).text(__num2str(val['costEvening']));
								$('#lblSugaNight', $(obj)).text(__num2str(val['costNight']));
								$('#lblSugaTot', $(obj)).text(__num2str(val['costTotal']));
							}else if (lsSvcCd == '4'){
								$('#lblSugaCost').text(__num2str(val['cost']));
								$('#lblSugaHour').text(__num2str(val['hour']));
								$('#lblNightCost').text(__num2str(val['costNight']));
								$('#lblNightHour').text(__num2str(val['hourNight']));
								$('#lblSugaTot').text(__num2str(val['costTotal']));

								if (lsSvcKind == '200'){
									if (val['toHour'] + ':' + val['toMin'] != $('#txtToH').val() + ':' + $('#txtToM').val()){
										$('#txtToH').val(val['toHour']).select();
										$('#txtToM').val(val['toMin']).select();

										self.focus();
									}
								}
							}
						}else{
							$('#txtBipayCost1').attr('value',val['cost']).text(__num2str(val['cost']));
							$('#txtBipayCost2').attr('value',val['costBipay']).text(__num2str(val['costBipay']));
							$('#txtBipayCost3').attr('value',$('#infoClient').attr('bipay'+lsSvcKind)).val(__num2str($('#infoClient').attr('bipay'+lsSvcKind)));
							$('input:radio[name="txtExtraKind"]:checked').click();
						}

						setTimeout('lfSetExtraMemPay()',10);

					}else if (lsSvcCd == '1' || lsSvcCd == '2' || lsSvcCd == '3'){
						$('#lblSugaNm').text(val['name']); //수가명
						$('#lblProcTime').text(val['procTime']); //제공시간
						$('#lblSugaCost').text(__num2str(val['cost'])); //수가
						$('#lblSugaCnt').text(val['procTime']); //시간
						$('#lblSugaTot').text(__num2str(val['costTotal'])); //수가계

						if (lsPayKind == 'Y'){
							$('#txtBipayCost1').attr('value',val['cost']).text(__num2str(val['cost']));
							$('#txtBipayCost2').attr('value',val['costBipay']).text(__num2str(val['costBipay']));
							$('#txtBipayCost3').attr('value',$('#infoClient').attr('bipay')).val(__num2str($('#infoClient').attr('bipay')));
							$('input:radio[name="txtExtraKind"]:checked').click();
						}
					}else{
						$('#lblSugaNm').text(val['name']); //수가명
						$('#lblProcTime').text(val['procTime']); //제공시간
						$('#lblSugaCost').text(__num2str(val['cost'])); //수가
						$('#lblSugaCnt').text(val['procTime']); //시간
						$('#lblSugaTot').text(__num2str(val['costTotal'])); //수가계
					}
				}
			});
		}

		//비급여 실비처리구분 불러오기
		function lfExtraShow(obj){
			var obj = $(obj).parent().parent().parent();
			var l = $(obj).offset().left - 2;
			var t = $(obj).offset().top + $(obj).height() - 2;

			$('#extraCont').css('left',l).css('top',t).show(300);
		}

		//비급여 적용수가 숨기기
		function lbExtraHide(){
			try{
				$('#extraCont').hide(300);
			}catch(e){
			}
		}

		//산모 추가 수당
		function lfBabyAddShow(obj){
			var obj = $(obj).parent().parent().parent();
			var l = $(document).innerWidth() - $('#babyAddCont').width();
			var t = $(obj).offset().top + $(obj).height() - 2;

			$('#babyAddCont').css('left',l).css('top',t).show(300);
		}
		function lfBabyAddHide(){
			try{
				$('#babyAddCont').hide(300);
			}catch(e){
			}
		}

		//수당항목
		function lfExtraPayShow(obj){
			var obj = $(obj).parent().parent();
			var l = $(obj).offset().left - 2;
			var t = $(obj).offset().top + $(obj).height() - 2;

			var lsSvcKind = $('#txtSvcKind').val();
			var lsSvcTime = $('#txtSvcTime').val();

			$('#tblExtraBath').hide();
			$('#tblExtraNurse').hide();

			if (lsSvcKind == '500'){
				$('#tblExtraBath').show();
			}else if (lsSvcKind == '800'){
				$('#tblExtraNurse').show();
			}

			$('#extraPayCont').css('left',l).css('top',t).show(300,function(){
				//수당입력
				setTimeout('lfSetExtraMemPay()',10);
			});
		}
		function lfExtraPayHide(){
			try{
				$('#extraPayCont').hide(300);
			}catch(e){
			}
		}

		function lfTest(){
			alert('test');
		}

		//요양보호사 수당조회
		function lfFindExtraMemPay(aiIdx){
			try{
				var lsSvcKind = $('#txtSvcKind').val();
				var lsSvcTime = $('#txtSvcTime').val();

				if ($('#txtMemCd'+aiIdx).attr('code') == ''){
					return;
				}

				if (lsSvcKind == '500' || lsSvcKind == '800'){
					//목욕 및 간호는 직원별 수당을 가져온다.
					$.ajax({
						type : 'POST'
					,	url  : '../common/find_extra_pay.php'
					,	data : {
							'code'  : $('#centerInfo').attr('value')
						,	'jumin'	: $('#txtMemCd'+aiIdx).attr('code')
						}
					,	success: function(result){
							var val = result.split('/');

							$('#loExtraMem'+aiIdx)
								.attr('B3',val[0])
								.attr('B2',val[2])
								.attr('B1',val[1])
								.attr('N1',val[3])
								.attr('N2',val[4])
								.attr('N3',val[5]);

							//수당입력
							if (lfChkExtraMemPay()){
								setTimeout('lfSetExtraMemPay()',10);
							}
						}
					});
				}
			}catch(e){
			}
		}

		//수당여부
		function lfChkExtraMemPay(){
			try{
				var lsSvcKind = $('#txtSvcKind').val();
				var lsSvcTime = $('#txtSvcTime').val();
				var lsSugaVal = '';

				if (lsSvcKind == '500'){
					lsSugaVal = 'B';
				}else if (lsSvcKind == '800'){
					lsSugaVal = 'N';
				}else{
					return true;
				}

				lsSugaVal += lsSvcTime;

				//직원별수당
				var liPay1 = $('#loExtraMem1').attr(lsSugaVal)
				,	liPay2 = $('#loExtraMem2').attr(lsSugaVal);

				if ($('#loExtraPay').attr('gbn') == 'PERSON'){
					var lsMemNm1 = $('#txtMemCd1').val()
					,	lsMemNm2 = $('#txtMemCd2').val();
					var lsSvcKindNm = $('#txtSvcKind option:selected').text();
					var liIdx = 0;

					if (lsMemNm1 != '' && liPay1 == 0) liIdx = 1;
					if (lsSvcKind == '500' && liIdx == 0){
						if (lsMemNm2 != '' && liPay2 == 0) liIdx = 2;
					}

					// - 직원의 수당 입력여부를 판단한다.
					//if (liIdx > 0){
					//	alert('"'+(liIdx == 1 ? lsMemNm1 : lsMemNm2)+'"의 "'+lsSvcKindNm+'" 수당을 직원정보에서 입력한 후 선택하여 주십시오.');
					//	lfMemClear(liIdx,true);
					//	return false;
					//}
				}

				return true;
			}catch(e){
				alert(e);
				return false;
			}
		}

		//수당입력
		function lfSetExtraMemPay(){
			try{
				var lsSugaCd  = $('#loSuga').attr('code');
				var lsSvcKind = $('#txtSvcKind').val();
				var lsSvcTime = $('#txtSvcTime').val();
				var lsSugaVal = '';

				if (lsSvcKind == '500'){
					lsSugaVal = 'B';
				}else if (lsSvcKind == '800'){
					lsSugaVal = 'N';
				}else{
					return false;
				}

				lsSugaVal += lsSvcTime;

				//직원별수당
				var liPay1 = $('#loExtraMem1').attr(lsSugaVal)
				,	liPay2 = $('#loExtraMem2').attr(lsSugaVal);

				if (lsSvcKind == '500'){
					var liExtraPay   = __str2num($('#txtBathPay').val());
					var liExtraRate1 = __str2num($('#loClientIfno').attr('bathRate1'));
					var liExtraRate2 = __str2num($('#loClientIfno').attr('bathRate2'));
					var liExtraPay1  = liExtraPay * liExtraRate1 / 100;
					var liExtraPay2  = liExtraPay * liExtraRate2 / 100;

					$('#lblBathPay1').text(__num2str(liPay1));
					$('#lblBathPay2').text(__num2str(liPay2));

					$('#txtBathRate1').val(liExtraRate1);
					$('#txtBathRate2').val(liExtraRate2);

					$('#txtBathPay1').val(__num2str(liExtraPay1));
					$('#txtBathPay2').val(__num2str(liExtraPay2));
				}else if (lsSvcKind == '800'){
					var liExtraPay = __str2num($('#txtNursePay').val());
					$('#lblNursePay').text(__num2str(liPay1));
				}

				switch($('#loExtraPay').attr('gbn')){
					case 'PERSON':
						switch(lsSvcKind){
							case '500':
								$('#lblApplyBathPay1').attr('value',liPay1).text(__num2str(liPay1));
								$('#lblApplyBathPay2').attr('value',liPay2).text(__num2str(liPay2));
								break;

							case '800':
								$('#lblApplyNursePay').attr('value',liPay1).text(__num2str(liPay1));
								break;
						}
						break;

					case 'AMT':
						switch(lsSvcKind){
							case '500':
								$('#lblApplyBathPay1').attr('value',liPay1).text(__num2str(liExtraPay1));
								$('#lblApplyBathPay2').attr('value',liPay2).text(__num2str(liExtraPay2));
								break;

							case '800':
								$('#lblApplyNursePay').attr('value',liPay1).text(__num2str(liExtraPay));
								break;
						}
						break;
				}

				lfApplyExtraPay($('#loExtraPay').attr('kind'),$('#loExtraPay').attr('gbn'));
			}catch(e){
				alert(e);
			}
		}

		$(document).ready(function(){
			__init_form(document.f);
		});
	</script>
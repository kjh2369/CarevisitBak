<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$today = Date('Ymd');

	if ($_SESSION['userLevel'] == 'C' || $_SESSION['userSmart'] == 'M'){
		$lbEdit	= true;
	}else{
		$lbEdit	= false;
	}
	$lbEdit	= true;

	$sql = 'SELECT	COUNT(*)
			FROM	cv_reg_info
			WHERE	org_no = \''.$orgNo.'\'
			AND		rs_cd IN (\'1\', \'2\', \'3\')
			AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
			AND		to_dt >= DATE_FORMAT(NOW(), \'%Y%m%d\')';

	$regInfoCnt = $conn->get_data($sql);

	if ($regInfoCnt < 1) $lbEdit = false;

	$svcCd	= $_POST['svcCd'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$type	= $_POST['type'];
	$jumin	= $ed->de($_POST['jumin']);
	$limitDt= $myF->dateAdd('month',1,Date('Y-m-d'),'Y-m-d');
	$limitY	= '9999'; //Date('Y',StrToTime($limitDt));
	$limitM	= '12'; //Date('m',StrToTime($limitDt));
	$sr     = $_POST['svcCd'];

	parse_str($_POST['para'],$para);

	//주야간보호
	if ($para['DayAndNight'] == 'Y'){
		$svcCd = '5';
	}?>

	<script type="text/javascript">
		$(document).ready(function(){
			setTimeout('lfGetSvcPeriod()',10);
		});

		function lfMoveYear(pos){
			$('#lblYear').text(parseInt($('#lblYear').text()) + pos);
			lfGetSvcPeriod();
		}

		function lfGetSvcPeriod(){
			$.ajax({
				type	:'POST'
			,	url		:'../iljung/plan_svc_period.php'
			,	data	:{
					'jumin'	:$('#clientInfo').attr('value') //'<?=$jumin;?>'
				,	'svcCd'	:$('#planInfo').attr('svcCd') //'<?=$svcCd;?>'
				,	'year'	:$('#lblYear').text()
				}
			,	beforeSend	:function(){
				}
			,	success	:function(data){
					for(var i=1; i<=12; i++){
						$('#lblMon'+i).html('');
					}

					if (!data){
						return false;
					}

					var month	= __parseStr(data);

					for(var i=1; i<=12; i++){
						var link	= '';

						if (month[i]){
							link	= '<a href="#" onclick="opener.gPlanWin = null; opener._planReg(\'\',\''+$('#lblYear').text()+'\',\''+(i<10?'0':'')+i+'\',\''+$('#clientInfo').attr('value')+'\',\''+$('#planInfo').attr('svcCd')+'\',\'\',\'<?=$sr;?>\');">'+month[i]+'</a>';
							$('#lblMon'+i).html(link);
						}
					}
				}
			,	error:function(request, status, error){
					alert(error);
				}
			});
		}

		function lfSvcPeriodShow(){
			if ($('#divSvcPeriod').css('display') != 'none'){
				$('#divSvcPeriod').hide(300);
				return;
			}

			var l	= $('#tdYYMM').offset().left;
			var t	= $('#tdYYMM').offset().top + $('#tdYYMM').height();

			$('#divSvcPeriod').css('left',l).css('top',t).show(300);
		}

		function lfMemo(){
			var width = 800;
			var height = 600;
			var left = (screen.availWidth - width) / 2;
			var top = (screen.availHeight - height) / 2;
			var target = 'ILJUNG_MEMO';
			var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
			var url = './iljung_memo_reg.php';
				gPlanWin = window.open('about:blank', target, option);
				gPlanWin.opener = self;
				gPlanWin.focus();

			var parm = new Array();
				parm = {
					'jumin'	:$('#clientInfo').attr('value')
				,	'year'	:$('#planInfo').attr('year')
				,	'month'	:$('#planInfo').attr('month')
				,	'svcCd'	:$('#planInfo').attr('svcCd')
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

			form.setAttribute('target', target);
			form.setAttribute('method', 'post');
			form.setAttribute('action', url);

			document.body.appendChild(form);

			form.submit();
		}
	</script><?

	if ($svcCd == '5'){
		//등급판단
		$sql = 'SELECT	COUNT(*)
				FROM	client_his_lvl
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		svc_cd	= \'0\'
				AND		level	< \'9\'
				AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$year.$month.'\'
				AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$year.$month.'\'';

		$IsLvlNormal = $conn->get_data($sql);

		if ($IsLvlNormal){
			//주야간보호 할증여부
			$sql = 'SELECT	yn
					FROM	dan_extra_charge
					WHERE	org_no	= \''.$orgNo.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		yymm	= \''.$year.$month.'\'';

			$danExtraChargeYn = $conn->get_data($sql);

			if (!$danExtraChargeYn) $danExtraChargeYn = 'N';?>
			<script type="text/javascript">
				function lfSetDanExtraCharge(obj){
					$.ajax({
						type:'POST'
					,	url:'../iljung/plan_extra_charge.php'
					,	data:{
							'jumin':$('#clientInfo').attr('value')
						,	'yymm':$('#planInfo').attr('year')+''+$('#planInfo').attr('month')
						,	'yn':($(obj).attr('checked') ? 'Y' : 'N')
						}
					,	beforeSend:function(){
						}
					,	success:function(result){
							if (result == 1){
								if ($(obj).attr('checked')){
									var claimAmt = Math.floor(__str2num($('#infoClient').attr('limitAmt')) * 1.5);

									claimAmt = Math.round(claimAmt / 10) * 10;

									$('#infoClient').attr('claimAmt',claimAmt);
								}else{
									$('#infoClient').attr('claimAmt',$('#infoClient').attr('limitAmt'));
								}

								$('#txtClaimPay').text(__num2str($('#infoClient').attr('claimAmt')));

								_planSetLimitAmt();
								setTimeout('lfCalendarData(1)',100);
							}else{
								$(obj).attr('checked',!$(obj).attr('checked'));
							}
						}
					,	error:function(){
						}
					}).responseXML;
				}
			</script>
			<table class="my_table" style="width:100%; border-bottom:1px solid #cccccc;">
				<colgroup>
					<col width="100px">
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th class="center bold">예상처우개선비</th>
						<td class="left bold last" id="lblDealPay">0</td>
					</tr>
					<tr>
						<th class="center bold bottom">150% 할증여부</th>
						<td class="bottom last">
							<label><input id="chkDanExtraChargeYn" type="checkbox" class="checkbox" value="Y" onclick="lfSetDanExtraCharge(this);" <?=$danExtraChargeYn == 'Y' ? 'checked' : '';?>>하루 8시간이상 20일 초과시 및 기타 150% 할증이 가능한 경우 선택하여 주십시오.</label>
						</td>
					</tr>
				</tbody>
			</table><?
		}
	}?>

	<div id="divSvcPeriod" class="my_border_blue" style="position:absolute; z-index:10; width:auto; background-color:#ffffff; display:none;">
		<div style="position:absolute; width:auto; top:-20px; right:1px;" onclick="$('#divSvcPeriod').hide(300);">
			<img src="../image/btn_close.gif">
		</div>
		<table class="my_table" style="width:auto;">
			<colgroup>
				<col width="80px">
				<col width="30px" span="12">
			</colgroup>
			<thead>
				<tr>
					<th class="head bold">년도</th>
					<th class="head bold">1월</th>
					<th class="head bold">2월</th>
					<th class="head bold">3월</th>
					<th class="head bold">4월</th>
					<th class="head bold">5월</th>
					<th class="head bold">6월</th>
					<th class="head bold">7월</th>
					<th class="head bold">8월</th>
					<th class="head bold">9월</th>
					<th class="head bold">10월</th>
					<th class="head bold">11월</th>
					<th class="head bold last">12월</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class="center bottom">
						<div class="left" style="">
						<div style="float:left; width:auto;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
						<div style="float:left; width:auto; padding-left:3px; padding-right:3px; font-weight:bold;" id="lblYear"><?=$year;?></div>
						<div style="float:left; width:auto;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
						</div>
					</th>
					<td class="center bottom" id="lblMon1"></td>
					<td class="center bottom" id="lblMon2"></td>
					<td class="center bottom" id="lblMon3"></td>
					<td class="center bottom" id="lblMon4"></td>
					<td class="center bottom" id="lblMon5"></td>
					<td class="center bottom" id="lblMon6"></td>
					<td class="center bottom" id="lblMon7"></td>
					<td class="center bottom" id="lblMon8"></td>
					<td class="center bottom" id="lblMon9"></td>
					<td class="center bottom" id="lblMon10"></td>
					<td class="center bottom" id="lblMon11"></td>
					<td class="center bottom last" id="lblMon12"></td>
				</tr>
			</tbody>
		</table>
	</div>

	<table class="my_table" style="width:100%; <?=($type == 'CONF' ? 'border-top:2px solid #0e69b0; margin-top:-2px;' : '');?>">
		<colgroup><?
			if ($type == 'PLAN' && !$IsLongtermCng2016){?>
				<col width="110px">
				<col width="200px"><?
			}?>
			<col>
			<col width="500px">
		</colgroup>
		<tbody>
			<tr><?
				if ($type == 'PLAN'){
					if (!$IsLongtermCng2016){
						if (!$isDemo && $lbEdit && $svcCd == '0'){?>
							<th class="center" style="font-weight:bold; color:#000000;">건보계획 업로드</th>
							<td class="left last">
								<!--img id="btnUpload1" src="../image/btn_lcm_1.gif" alt="방문요양 건보 업로드" onclick="lfReason('<?=$year.$month;?>','001','Y','<?=$debug;?>');"-->
								<!--img id="btnUpload2" src="../image/btn_lcm_2.gif" alt="방문목욕 건보 업로드" onclick="lfReason('<?=$year.$month;?>','002','Y','<?=$debug;?>');"-->
								<div id="divUploadBtn" style="width:auto; display:none;">
									<img id="btnUpload1" src="../image/btn_lcm_1.gif" alt="방문요양 건보 업로드" onclick="lfLongcareUpload('<?=$year.$month;?>','001','Y','<?=$debug;?>');">
									<img id="btnUpload2" src="../image/btn_lcm_2.gif" alt="방문목욕 건보 업로드" onclick="lfLongcareUpload('<?=$year.$month;?>','002','Y','<?=$debug;?>');">
									<img id="btnUpload3" src="../image/btn_lcm_3.gif" alt="방문간호 건보 업로드" onclick="lfLongcareUpload('<?=$year.$month;?>','003','Y','<?=$debug;?>');">
								</div>
								<div id="divReloadBtn" style="width:auto; display:none;">
									<span class="btn_pack m" style="color:BLUE;"><button onclick="lfLoadOption();">공단정보 새로고침</button></span>
								</div>
							</td><?
						}else if (!$isDemo && $lbEdit && $svcCd == '5'){?>
							<th class="center" style="font-weight:bold; color:#000000;">건보계획 업로드</th>
							<td class="left last"><?
								if ($year.$month <= $limitY.$limitM){?>
									<div id="divUploadBtn" style="width:auto; display:none;">
										<img id="btnUpload1" src="../image/btn_lcm_4.png" alt="주야간보호 건보 업로드" onclick="lfLongcareUpload('<?=$year.$month;?>','004','Y','<?=$debug;?>');">
									</div>
									<div id="divReloadBtn" style="width:auto; display:none;">
										<span class="btn_pack m" style="color:BLUE;"><button onclick="lfLoadOption();">공단정보 새로고침</button></span>
									</div><?
								}else{?>
									<div><?=$limitY;?>년 <?=IntVal($limitM);?>월까지 업로드가 가능함.</div><?
								}?>
							</td><?
						}else{?>
							<td class="last">&nbsp;</td>
							<td class="last">&nbsp;</td><?
						}
					}?>
					<td id="tdYYMM" class="center last"><a href="#" onclick="lfSvcPeriodShow(); return false;"><span class="bold"><?=intval($year);?>년 <?=intval($month);?>월</span></a></td><?
				}else{?>
					<td class="left bold last"><div class="title">실적내역(<?=intval($year);?>년 <?=intval($month);?>월)</div></td><?
				}?>
				<td class="right last"><?
					if ($type == 'PLAN'){
						if ($lbEdit){?>
							<img src="./img/btn_calen7.gif" onclick="lfCalClean('1');" alt="중복일정지우기">
							<img src="./img/btn_calen8.gif" onclick="lfCalClean('2');" alt="미저장일정지우기">
							<img src="../image/btn_save_2.png" onclick="lfSave();" alt="일정저장">
							<img src="../image/btn11.gif" onclick="lfDelete();" alt="일적삭제"><?
						}?>
						<img src="../image/btn_print_1.png" onclick="lfShowCaln('Y');" alt="금액표시된 출력물입니다.">
						<img src="../image/btn_print_2.png" onclick="lfShowCaln('N');" alt="금액 미표시된 출력물입니다."><?
					}else{?>
						<!--
						<img src="../image/btn/btn_plan_print.gif" onclick="_iljungPDFShow('1'
																							  ,$('#centerInfo').attr('value')
																							  ,$('#planInfo').attr('svcCd')
																							  ,$('#planInfo').attr('year')
																							  ,$('#planInfo').attr('month')
																							  ,$('#clientInfo').attr('value'));" alt="일정출력">
						-->
						<img src="../image/btn_print_1.png" onclick="lfShowCaln('Y','conf');" alt="일정출력"><?
					}?>
					<span class="btn_pack m"><button onclick="lfMemo();">메모관리</button></span>
				</td>
			</tr>
		</tbody>
	</table><?
	include_once('../inc/_db_close.php');?>
	<script type="text/javascript">
	//출력
	function lfShowCaln(asUseType,showGbn){
		var chkSvc = '';

		if (!showGbn) showGbn = 'all';

		if ($('#planInfo').attr('svcCd') == '0' ||
			$('#planInfo').attr('svcCd') == '4'){
			if ('<?=$svcCd;?>' == '5'){
				chkSvc = '5'+String.fromCharCode(1);
			}else{
				chkSvc = $('#planInfo').attr('svcCd')+'_200'+String.fromCharCode(1)
					   + $('#planInfo').attr('svcCd')+'_210'+String.fromCharCode(1)
					   + $('#planInfo').attr('svcCd')+'_500'+String.fromCharCode(1)
					   + $('#planInfo').attr('svcCd')+'_800'+String.fromCharCode(1);
			}
		}else{
			chkSvc = $('#planInfo').attr('svcCd')+String.fromCharCode(1);
		}

		var para = 'root=iljung'
				 + '&dir=p'
				 + '&fileName=iljung_print'
				 + '&fileType=pdf'
				 + '&target=show.php'
				 + '&showForm=ILJUNG_CALN'
				 + '&code=<?=$code;?>'
				 + '&year='+$('#planInfo').attr('year')
				 + '&month='+$('#planInfo').attr('month')
				 + '&jumin='+$('#clientInfo').attr('value')
				 + '&showGbn='+showGbn
				 + '&mode=101'
				 + '&name='
				 + '&chkSvc='+chkSvc
				 + '&printDT='
				 + '&useType='+asUseType
				 + '&calnYn=Y'
				 + '&dtlYn=Y'
				 + '&sr='+$('#planInfo').attr('sr')
				 + '&param=';

		__printPDF(para);
	}

	//업로드 사유
	function lfReason(asYYMM, asKind, asUpYn, abDebug){
		var today = getDay(getToday());
		var upload = true;
		var subCd = '';

		if ($('div[id^="divCalCngYn_"][yn="Y"]').length > 0){
			alert('저장 후 업로드를 실행하여 주십시오.');
			return;
		}

		if ($('div[id^="loCal_"][ynSave="N"]').length > 0){
			alert('저장 후 업로드를 실행하여 주십시오.');
			return;
		}

		if ($('div[id^="loCal_"][longtermYn="N"]').length > 0){
			alert('과거의 일정이 공단과 다른 경우 업로드 할 수 없습니다.');
			return;
		}

		switch(asKind){
			case '001': subCd = '200'; break;
			case '002': subCd = '500'; break;
			case '003': subCd = '800'; break;
		}

		for(var i=1; i<today; i++){
			if ($('#lblPlan_'+i).attr(subCd+'Yn') == 'Y'){
				//업로드 불가능
				upload = false;
				break;
			}
		}

		if (!upload){
			alert('과거의 일정이 공단과 다른 경우 업로드 할 수 없습니다.');
			return;
		}

		var tbl = $('#tblLongterm');
		var td = $('td[id^="lblUpload_"]',tbl);

		upload = true;

		$(td).each(function(){
			if ($(this).text() != 'Y'){
				upload = false;
				return false;
			}
		});

		if (!upload){
			alert('건보와 등급 및 구분이 불일치하여 업로드가 불가능합니다.\n확인 후 다시 시도하여 주십시오.');
			return;
		}

		alert('TEST');

		return;

		var width = 500;
		var height = 200;
		//var left = (screen.availWidth - width) / 2;
		//var top = (screen.availHeight - height) / 2;
		var left = window.screenLeft + ($(window).width() - width) / 2;
		var top = window.screenTop + ($(window).height() - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=yes,status=no,resizable=yes';
		var url = './plan_reason.php';
		var win = window.open('', 'PLAN_REASON', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'yymm':asYYMM
			,	'kind':asKind
			,	'upYn':asUpYn
			,	'test':abDebug
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

		form.setAttribute('target', 'PLAN_REASON');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	//건보업로드
	function lfLongcareUpload(asYYMM, asKind, asUpYn, abDebug, chgSayu, chgSayuEtc){
		/*
		var date = new Date();
		var year = date.getFullYear();
		var month= date.getMonth()+1;

		month = (month < 10 ? '0' : '')+month;

		if (asYYMM <= year+month){
			alert('현재년월과 과거의 일정은 공단으로 업로드 할 수 없습니다.');
			return;
		}
		 */

		if (asKind == '004' && $('#infoClient').attr('svcLvl') == '5'){
			alert('현재 주야간보호 5등급의 업로드를 작업중입니다.\n공단에서 등록 후 가져오기로 입력하여 주시기 바랍니다.\n감사합니다.');
			return;
		}

		if ('<?=$lbTodayPlanReg;?>' == '1'){
			if ($('#divChkDataChange').text() == 'Y'){
				alert('변경된 데이타가 있습니다. 저장 후 업로드를 실행하여 주십시오.');
				return;
			}

			if ($('#divLongtermUploadYn').text() != 'Y'){
				alert('공단정보를 조회한지 5분이 지났습니다.\n공단정보를 다시 조회 후 업로드를 실행하여 주십시오.');
				//lfLoadOption();
				return;
			}
		}

		var today = getToday();
		var y = getYear(today);
		var m = getMonth(today);
		var yymm = y+''+(m < 10 ? '0' : '')+m;
		var day = getDay(today);
		var upload = true;
		var subCd = '';
		var lgPara = '';

		if ('<?=$debug;?>' == '1' && '<?=$orgNo;?>' == '34273000017'){
			$('.clsCal').each(function(){
				if ($('#lblLGSaveYn',this).attr('lgSaveYn') == 'Y'){
					lgPara += (lgPara ? '?' : '');
					lgPara += 'day='+$(this).attr('day');
					lgPara += '&subCd='+$(this).attr('svcKind');
					lgPara += '&from='+$(this).attr('from').split(':').join('');
				}
			});
		}else{
			//공단계획 확정여부
			var IsFix = false;

			for(var i=1; i<day; i++){
				if ($('#lblPlan_'+i).attr('fix') == 'Y'){
					IsFix = true;
					break;
				}
			}

			if (IsFix){
				alert('공단에 계획확정이 있습니다.\n\n공단에서 직접 수정하여 주십시오.');
				return;
			}
		}

		//변경된 일정이 있을 경우(삭제 후 저장되지 않은 경우)
		if ($('div[id^="divCalCngYn_"][yn="Y"]').length > 0){
			alert('저장 후 업로드를 실행하여 주십시오.');
			return;
		}

		//저장되지 않은 일정이 있을 경우
		if ($('div[id^="loCal_"][ynSave="N"]').length > 0){
			alert('저장 후 업로드를 실행하여 주십시오.');
			return;
		}

		if ($('div[id^="loCal_"][longtermYn="N"]').length > 0){
			alert('과거의 일정이 공단과 다른 경우 업로드 할 수 없습니다.');
			return;
		}

		if (asYYMM < yymm){
			alert('과거년월의 일정은 공단으로 업로드 할 수 없습니다.');
			return;
		}

		switch(asKind){
			case '001': subCd = '200'; break;
			case '002': subCd = '500'; break;
			case '003': subCd = '800'; break;
		}

		for(var i=1; i<day; i++){
			if ($('#lblPlan_'+i).attr(subCd+'Yn') == 'Y'){
				//업로드 불가능
				upload = false;
				break;
			}
		}

		if (!upload){
			alert('과거의 일정이 공단과 다른 경우 업로드 할 수 없습니다.');
			return;
		}

		var tbl = $('#tblLongterm');
		var td = $('td[id^="lblUpload_"]',tbl);
		var upload = true;

		$(td).each(function(){
			var yn = $(this).text();

			if (!yn) yn = 'Y';

			if (yn != 'Y'){
				upload = false;
				return false;
			}
		});

		if (!upload){
			alert('건보공단과 등급 및 구분이 불일치하거나 월 중간에 계약이 갱신된 경우 업로드가 불가능합니다.\n확인 후 다시 시도하여 주십시오.');
			return;
		}

		/*
		if (asYYMM >= year+month){
		}else{
			alert('과거일정은 업로드가 불가능합니다.');
			return false;
		}
		*/

		var rowCnt = __str2num($('#tblLvlInfo').attr('rowCnt'));

		if (rowCnt > 1){
			alert('월중간에 등급이 바뀐 경우의 건보업로드는 아직 지원하지 않습니다.');
			return false;
		}

		if (!chgSayu) chgSayu = '';
		if (!chgSayuEtc) chgSayuEtc = '';
		_longcareUpload(asYYMM,asKind,asUpYn,abDebug,chgSayu,chgSayuEtc,lgPara);
	}

	/*********************************************************
	 * 저장
	 *********************************************************/
	function lfSave(){
		var para = '';
		var svcCd = $('#planInfo').attr('svcCd');
		if (svcCd != '<?=$svcCd;?>') svcCd = '<?=$svcCd;?>';

		if (svcCd == 'S'){
			var obj = $('.clsCal[duplicate="N"]');
		}else{
			var obj = $('.clsCal[duplicate="N"][stat!="1"]');
		}

		$(obj).each(function(){
			if (($('#planInfo').attr('svcCd') == 'S' || $('#planInfo').attr('svcCd') == 'R') && ($(this).attr('request').length == 10 || $(this).attr('request') == 'SERVICE')){
				//재가지원,자원연계 저장시 묶음등록된 일정은 저장하지 않는다.
			}else{
				if (para) para += '&';

				var liFrom = __time2min($(this).attr('from'))
				,	liTo   = __time2min($(this).attr('to'));

				if (liFrom >= liTo){
					liTo = liTo + (24 * 60);
				}

				var liTime = liTo - liFrom;
				var addFlag = false;

				if (svcCd == '5'){
					$(this).attr('memCd1','DAY_AND_NIGHT');
					$(this).attr('memNm1','');
					//$(this).attr('memCd2','');
					//$(this).attr('memNm2','');
					addFlag = true;
				}else{
					if ($(this).attr('memCd1')) addFlag = true;
				}

				if (addFlag){
					para += $(this).attr('day')+'_'+$(this).attr('cnt')+'='
						 +  $(this).attr('day')+';'			//0
						 +  $(this).attr('week')+';'		//1
						 +  $(this).attr('svcKind')+';'		//2
						 +  $(this).attr('from')+';'		//3
						 +  $(this).attr('to')+';'			//4
						 +  liTime+';'						//5
						 +  $(this).attr('memCd1')+';'		//6
						 +  $(this).attr('memNm1')+';'		//7
						 +  $(this).attr('memCd2')+';'		//8
						 +  $(this).attr('memNm2')+';'		//9
						 +  $(this).attr('sugaCd')+';'		//10
						 +  ';' //수가명					//11
						 +  $(this).attr('cost')+';'		//12
						 +  $(this).attr('costEvening')+';'	//13
						 +  $(this).attr('costNight')+';'	//14
						 +  $(this).attr('costTotal')+';'	//15
						 +  $(this).attr('sudangPay')+';'	//16
						 +  $(this).attr('sudangKind')+';'	//17
						 +  $(this).attr('sudangVal1')+';'	//18
						 +  $(this).attr('sudangVal2')+';'	//19
						 +  $(this).attr('timeEvening')+';'	//20
						 +  $(this).attr('timeNight')+';'	//21
						 +  $(this).attr('ynNight')+';'		//22
						 +  $(this).attr('ynEvening')+';'	//23
						 +  $(this).attr('ynHoliday')+';'	//24
						 +  $(this).attr('ynBipay')+';'		//25
						 +  $(this).attr('ynFamily')+';'	//26
						 +  $(this).attr('extraKind')+';'	//27
						 +  $(this).attr('bipayCost')+';'	//28
						 +  $(this).attr('ynRealPay')+';'	//29
						 +  $(this).attr('realPay')+';'		//30
						 +  $(this).attr('stat')+';'		//31
						 +  $(this).attr('seq')+';'			//32
						 +  $(this).attr('babyAddPay')+';'	//33
						 +  $(this).attr('togetherYn')+';'	//34
						 +  $(this).attr('bipayInfo')+';'	//35 주야간 비급여
						 +  $(this).attr('ynDementia')+';'	//36
						 ;
				}
			}
		});

		$.ajax({
			type : 'POST'
		,	url  : './plan_save.php'
		,	data : {
				'code'	:$('#centerInfo').attr('value')
			,	'jumin'	:$('#clientInfo').attr('value')
			,	'svcCd'	:svcCd
			,	'year'	:$('#planInfo').attr('year')
			,	'month'	:$('#planInfo').attr('month')
			,	'sr'	:$('#planInfo').attr('sr')
			,	'para'	:para
			}
		,	beforeSend: function(){
				//$('#clientInfo').after(_planLoading($('#clientInfo')));
			}
		,	success: function(result){
				//$('#loMsg').text(result).show();
				if (result == 1){
					alert('정상적으로 처리되었습니다.');

					$('#lblDealCare').text('0');
					$('#lblDealBath').text('0');

					/*
					if ('<?=$lbTodayPlanReg;?>' == '1'){
						if ($('#divChkDataChange').text() == 'Y'){
							lfLoadOption();
						}
					}
					*/

					_planCalContLoad();
					lfLoadOption();
					try{
						lfGetMemPlantime();
					}catch(e){
					}
				}else if (result == 9){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}

	/*********************************************************
	 * 삭제
	 *********************************************************/
	function lfDelete(){
		var svcCd = $('#planInfo').attr('svcCd');
		if (svcCd != '<?=$svcCd;?>') svcCd = '<?=$svcCd;?>';

		if (svcCd == 'S'){
			var liCnt = $('.clsCal[duplicate="N"]').length;
		}else{
			var liCnt = $('.clsCal[duplicate="N"][stat!="1"]').length;
		}

		if (liCnt == 0){
			alert('삭제할 일정이 없습니다.');
			return;
		}

		if (!confirm('삭제된 일정은 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type : 'POST'
		,	url  : './plan_delete.php'
		,	data : {
				'code'	:$('#centerInfo').attr('value')
			,	'jumin'	:$('#clientInfo').attr('value')
			,	'svcCd'	:svcCd
			,	'year'	:$('#planInfo').attr('year')
			,	'month'	:$('#planInfo').attr('month')
			}
		,	success: function(result){
				//$('#loMsg').text(result).show();
				if (result == 1){
					alert('정상적으로 처리되었습니다.');

					$('#lblDealCare').text('0');
					$('#lblDealBath').text('0');

					_planCalContLoad();

					try{
						lfGetMemPlantime();
					}catch(e){
					}
				}else if (result == 9){
					alert('삭제중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}
	</script>
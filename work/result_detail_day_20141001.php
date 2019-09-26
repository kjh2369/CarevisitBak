<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_body_header.php');

	$code	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$day	= $_POST['day'];
	$ymd	= $year.'-'.$month.'-'.$day;
	$lastDay= $myF->lastDay($year, $month);
	$order	= $_POST['order'] != '' ? $_POST['order'] : '1';


	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	//본인부담율 변경내역
	$sql = 'SELECT	jumin
			,		from_dt
			,		to_dt
			,		rate
			FROM	client_his_kind
			WHERE	org_no	 = \''.$code.'\'
			AND		from_dt <= \''.$ymd.'\'
			AND		to_dt	>= \''.$ymd.'\'
			ORDER	BY seq';
	$tmpRate = $conn->_fetch_array($sql);

	for($i=1; $i<=$lastDay; $i++){
		if (Is_Array($tmpRate)){
			foreach($tmpRate as $row){
				$tmpDt = $year.'-'.$month.'-'.($i < 10 ? '0' : '').$i;
				$jumin = $row['jumin'];

				if ($row['from_dt'] <= $tmpDt && $row['to_dt'] >= $tmpDt){
					$laRate[$jumin][$i] = $row['rate'];
					break;
				}
			}
		}
	}

	UnSet($tmpRate);

	//가사간병 등록내역
	$sql = 'SELECT	jumin
			,		svc_val AS val
			FROM	client_his_nurse
			WHERE	org_no	 = \''.$code.'\'
			AND		from_dt <= \''.$ymd.'\'
			AND		to_dt	>= \''.$ymd.'\'
			ORDER	BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$jumin = $row['jumin'];
		$laVoucher[$jumin]['1'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//노인돌봄
	$sql = 'SELECT	jumin
			,		svc_val AS val
			FROM	client_his_old
			WHERE	org_no	 = \''.$code.'\'
			AND		from_dt <= \''.$ymd.'\'
			AND		to_dt	>= \''.$ymd.'\'
			ORDER	BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$jumin = $row['jumin'];

		if ($row['val'] == '1'){
			$row['val'] = 'V';
		}else if ($row['val'] == '2'){
			$row['val'] = 'D';
		}

		$laVoucher[$jumin]['2'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//산모신생아
	$sql = 'SELECT	jumin
			,		svc_val AS val
			FROM	client_his_baby
			WHERE	org_no	 = \''.$code.'\'
			AND		from_dt <= \''.$ymd.'\'
			AND		to_dt	>= \''.$ymd.'\'
			ORDER	BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$jumin = $row['jumin'];
		$laVoucher[$jumin]['3'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//장애인활동지원
	$sql = 'SELECT	jumin
			,		svc_val AS val
			,		svc_lvl AS lvl
			FROM	client_his_dis
			WHERE	org_no	 = \''.$code.'\'
			AND		from_dt	<= \''.$ymd.'\'
			AND		to_dt	>= \''.$ymd.'\'
			ORDER	BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$jumin = $row['jumin'];

		if ($row['val'] == '1'){
			$row['val'] = 'A';
		}else if ($row['val'] == '2'){
			$row['val'] = 'C';
		}

		$laVoucher[$jumin]['4'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//바우처 새성내역
	$sql = 'SELECT	voucher_jumin AS jumin
			,		voucher_kind AS kind
			,		voucher_gbn AS val
			,		voucher_lvl AS lvl
			FROM	voucher_make
			WHERE	org_no		= \''.$code.'\'
			AND		voucher_yymm= \''.$year.$month.'\'
			AND		del_flag	= \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$jumin = $row['jumin'];

		if ($row['kind'] == '2'){
			if ($row['val'] == '1'){
				$row['val'] = 'V';
			}else if ($row['val'] == '2'){
				$row['val'] = 'D';
			}
		}else if ($row['kind'] == '4'){
			$row['val'] = $laVoucher[$jumin]['4']['val'];
		}

		$laVoucher[$jumin][$row['kind']] = Array('val'=>$row['val'],'lvl'=>$row['lvl']);
	}

	$conn->row_free();

	//마감처리여부
	$ynClose = $conn->_isCloseResult($code, $year.$month);?>

	<form id="f" name="f" method="post">
	<div class="title_border"><?
		if ($ynClose == 'Y'){?>
			<div class="bold" style="float:right; width:auto; color:#ff0000; margin-top:15px;">※ <?=$year;?>년 <?=intval($month);?>월은 실적등록이 완료되어 수정이 불가능합니다.</div><?
		}?>
		<div class="title" style="float:left; width:auto;">월실적등록(수급자)</div>
	</div>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="">일자</th>
				<td class="left last"><?=$year;?>.<?=$month;?>.<?=$day;?></td>
				<td class="right last">
					<span class="btn_pack m icon"><span class="save"></span><button type="button" onclick="lfSave();">저장</button></span>
					<span class="btn_pack m icon"><span class="before"></span><button type="button" onclick="lfBefore();">이전</button></span>
					<span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="IfExcel();">엑셀</button></span>
				</td>
			</tr>
			<tr>
				<th>정렬</th>
				<td class="last" colspan="2">
					<input id='order1' name='order' type='radio' value='1' class='radio' onclick='lfSearch();' <? if($order == '1'){ echo 'checked'; } ?> ><label for='order1'>수급자순별</label>
					<input id='order2' name='order' type='radio' value='2' class='radio' onclick='lfSearch();' <? if($order == '2'){ echo 'checked'; } ?> ><label for='order2'>계획시간순별</label>
					<input id='order3' name='order' type='radio' value='3' class='radio' onclick='lfSearch();' <? if($order == '3'){ echo 'checked'; } ?> ><label for='order3'>실적시간순별</label>
					<input id='order4' name='order' type='radio' value='4' class='radio' onclick='lfSearch();' <? if($order == '4'){ echo 'checked'; } ?> ><label for='order4'>담당자순별</label>
					<input id='order5' name='order' type='radio' value='5' class='radio' onclick='lfSearch();' <? if($order == '5'){ echo 'checked'; } ?> ><label for='order5'>제공서비스별</label>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- 엑셀출력 계획/실적금액 환경변수-->
	<input name="TotConfAmt" id="TotConfAmt" type="hidden" value="">
	<input name="TotPlanAmt" id="TotPlanAmt" type="hidden" value="">

	<script type="text/javascript" src="../iljung/plan.js"></script>
	<div class="title title_border">서비스 내역</div>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="45px">
			<col width="70px">
			<col width="120px">
			<col width="140px">
			<col width="110px">
			<col width="70px">
			<col width="70px">
			<col width="130px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">일자</th>
				<th class="head">고객명</th>
				<th class="head">계획</th>
				<th class="head">실적</th>
				<th class="head">제공서비스</th>
				<th class="head">실적급여</th>
				<th class="head">계획급여</th>
				<th class="head">담당자</th>
				<th class="head last">
					<img src="../image/btn_copy_2.png" onclick="lfSvcCopy('ALL');">
					<img src="../image/btn_cancel_2.png" onclick="lfSvcCancel('ALL');">
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center sum"></td>
				<td class="center sum"></td>
				<td class="center sum"><span id="lblTotPlanTime"></span></td>
				<td class="center sum"><span id="lblTotConfTime"></span></td>
				<td class="center sum"></td>
				<td class="right sum"><span id="lblTotConfAmt"></span></td>
				<td class="right sum"><span id="lblTotPlanAmt"></span></td>
				<td class="center sum"></td>
				<td class="center sum last"></td>
			</tr><?
			//오늘
			$today = date('Ymd');

			//요일
			$laWeekly = array(1=>'일',2=>'월',3=>'화',4=>'수',5=>'목',6=>'금',7=>'토');

			//수가정보
			$sql = 'select m01_mcode2 as suga
					,      m01_suga_cont as name
					,      m01_suga_value as cost
					  from m01suga
					 where m01_mcode  = \'goodeos\'
					   and left(m01_sdate,'.strlen($year.$month).') <= \''.$year.$month.'\'
					   and left(m01_edate,'.strlen($year.$month).') >= \''.$year.$month.'\'
					 union all
					select m11_mcode2 as suga
					,      m11_suga_cont as name
					,      m11_suga_value as cost
					  from m11suga
					 where m11_mcode  = \'goodeos\'
					   and left(m11_sdate,'.strlen($year.$month).') <= \''.$year.$month.'\'
					   and left(m11_edate,'.strlen($year.$month).') >= \''.$year.$month.'\'
					 union all
					select service_code as suga
					,      service_gbn as nm
					,      service_cost as cost
					  from suga_service
					 where org_no = \'goodeos\'
					   and left(service_from_dt,'.strlen($year.$month).') <= \''.$year.$month.'\'
					   and left(service_to_dt,  '.strlen($year.$month).') >= \''.$year.$month.'\'';
			$laSuga = $conn->_fetch_array($sql, 'suga');

			$sql = 'select	t01_jumin AS jumin
					,		m03_name AS name
					,		t01_mkind as kind
					,		cast(substring(t01_sugup_date,7) as unsigned) as date
					,		dayofweek(date_format(t01_sugup_date,\'%Y-%m-%d\')) as weekly
					,		t01_sugup_fmtime as plan_from
					,		t01_sugup_totime as plan_to
					,		t01_sugup_soyotime as plan_time
					,		t01_sugup_seq as seq
					,		t01_suga_code1 as plan_suga_cd
					,		t01_suga_tot as plan_suga
					,		t01_conf_fmtime as conf_from
					,		t01_conf_totime as conf_to
					,		t01_conf_soyotime as conf_time
					,		t01_conf_suga_code as conf_suga_cd
					,		t01_conf_suga_value as conf_suga
					,		t01_status_gbn as stat
					,		t01_svc_subcode as kind_cd
					,		t01_yoyangsa_id1 as conf_cd1
					,		t01_yoyangsa_id2 as conf_cd2
					,		t01_yname1 as conf_nm1
					,		t01_yname2 as conf_nm2
					,		t01_mem_cd1 as plan_cd1
					,		t01_mem_nm1 as plan_nm1
					,		t01_mem_cd2 as plan_cd2
					,		t01_mem_nm2 as plan_nm2
					,		t01_holiday as holiday_yn
					,		t01_toge_umu as family_yn
					,		t01_bipay_umu as bipay_yn
					,		case when date_format(now(), \'%Y%m%d\') >= t01_sugup_date then \'Y\' else \'N\' end as day_yn
					,		t01_bipay_kind as bipay_kind
					,		t01_bipay1 as bipay1
					,		t01_bipay2 as bipay2
					,		t01_bipay3 as bipay3
					,		t01_request as request
					,		t01_yoyangsa_id5 AS cut_gbn
					from	t01iljung
					INNER	JOIN	m03sugupja
							ON		m03_ccode = t01_ccode
							AND		m03_mkind = t01_mkind
							AND		m03_jumin = t01_jumin
					where	t01_ccode  = \''.$code.'\'
					AND		t01_mkind != \'6\'
					and		t01_del_yn = \'N\'
					and		t01_sugup_date = \''.$year.$month.$day.'\'';

			if ($order == '1'){
				$sql .=' order by name, kind, plan_from, plan_to';
			}else if ($order == '2'){
				$sql .=' order by plan_time, plan_to, kind, name';
			}else if ($order == '3'){
				$sql .=' order by conf_from, conf_to, kind, name';
			}else if ($order == '4'){
				$sql .=' order by conf_nm1, conf_nm2, conf_from, conf_to, plan_from, plan_to, kind';
			}else if ($order == '5'){
				$sql .=' order by kind, name, plan_from, plan_to';
			}

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);
				$jumin = $row['jumin'];

				if ($row['weekly'] == 1){
					$color = '#ff0000';
				}else if ($row['weekly'] == 7){
					$color = '#0000ff';
				}else{
					$color = '#000000';
				}

				$liPlanAmt = $row['plan_suga'];

				if ($row['stat'] == '1'){
					$lsSugaCd  = $row['conf_suga_cd'];
					$lsSugaNm  = $laSuga[$lsSugaCd]['name'];
					$liConfAmt = $row['conf_suga'];
					$lsMemCd1  = $ed->en($row['conf_cd1']);
					$lsMemNm1  = $row['conf_nm1'];
					$lsMemCd2  = $ed->en($row['conf_cd2']);
					$lsMemNm2  = $row['conf_nm2'];
				}else{
					$lsSugaCd  = $row['plan_suga_cd'];
					$lsSugaNm  = $laSuga[$lsSugaCd]['name'];
					$liConfAmt = 0;
					$lsMemCd1  = $ed->en($row['plan_cd1']);
					$lsMemNm1  = $row['plan_nm1'];
					$lsMemCd2  = $ed->en($row['plan_cd2']);
					$lsMemNm2  = $row['plan_nm2'];
				}

				$orgAmt  = $liConfAmt;
				$rate    = FloatVal($laRate[$jumin][IntVal($row['date'])]);
				$expense = $orgAmt * ($rate / 100);

				if ($year.$month >= '201401'){
					$expense = 0;
				}

				$sugaVal = $orgAmt - $expense;
				$downVal = $sugaVal * (IntVal($row['cut_gbn']) * 0.01);

				$sugaVal = $orgAmt - $downVal;
				$orgAmt  = Round($sugaVal / 10) * 10;

				//$liConfPay = Round($liConfAmt - ($liConfAmt * (IntVal($row['cut_gbn']) * 0.01)),-1);
				$liConfPay = $orgAmt;

				if ($lsSugaCd == 'CBKD1' || $lsSugaCd == 'VAB10'){
					$lsBathKind = '1';
				}else if ($lsSugaCd == 'CBKD2' || $lsSugaCd == 'VAB20'){
					$lsBathKind = '2';
				}else{
					$lsBathKind = '3';
				}

				if ($row['request'] != 'LOG'){
					$lsPlan = '<span id="lblPlanFrom_'.$i.'">'.$myF->timeStyle($row['plan_from']).'</span>~<span id="lblPlanTo_'.$i.'">'.$myF->timeStyle($row['plan_to']).'</span> (<span id="lblPlanTime_'.$i.'">'.$row['plan_time'].'</span>분)';
				}else{
					$lsPlan    = '<span style="font-weight:bold; color:#0000ff;">RFID실적만 있음</span>';
					$liPlanAmt = 0;
				}

				if ($ynClose != 'Y' && $today >= $year.$month.($row['date'] < 10 ? '0' : '').$row['date']){
					$lbEdit = true;
				}else{
					$lbEdit = false;
				}

				$lbEdit = true;?>
					<tr id="trVal_<?=$i?>"
						jumin		="<?=$ed->en($jumin);?>"
						svcCd		="<?=$row['kind'];?>"
						svcKind		="<?=$row['kind_cd'];?>"
						date		="<?=$row['date'];?>"
						planFrom	="<?=$row['plan_from'];?>"
						planSeq		="<?=$row['seq'];?>"
						ynFamily	="<?=($row['family_yn'] == 'Y' ? 'Y' : 'N');?>"
						bathKind	="<?=$lsBathKind;?>"
						svcVal		="<?=$laVoucher[$jumin][$row['kind']]['val'];?>"
						svcLvl		="<?=$laVoucher[$jumin][$row['kind']]['lvl'];?>"
						sugaCd		="<?=$lsSugaCd;?>"
						sugaNm		="<?=$lsSugaNm;?>"
						sugaVal		="<?=$liConfAmt;?>"
						saveVal		="0"
						from		="<?=$myF->timeStyle($row['conf_from']);?>"
						to			="<?=$myF->timeStyle($row['conf_to']);?>"
						time		="<?=$row['conf_time'];?>"
						memCd1		="<?=$lsMemCd1;?>"
						memNm1		="<?=$lsMemNm1;?>"
						memCd2		="<?=$lsMemCd2;?>"
						memNm2		="<?=$lsMemNm2;?>"
						stat		="<?=$row['stat'];?>"
						bipayYn		="<?=$row['bipay_yn'];?>"
						holidayYn	="<?=$row['holiday_yn'];?>"
						cutGbn		="<?=IntVal($row['cut_gbn']);?>"
						rate		="<?=FloatVal($laRate[$jumin][IntVal($row['date'])]);?>"
						bipay		="<?=IntVal($row['plan_suga']);?>"
						addRow		="N"
						flag		="N"
						overYn		="N">
					<td class="center" style="color:<?=$color;?>;"><?=$row['date'];?>(<?=$laWeekly[$row['weekly']];?>)</td>
					<td class="center"><?=$row['name'];?></td>
					<td class="center"><?=$lsPlan;?></td>
					<td class="left"><?
						if ($lbEdit){?>
							<input id="txtConfFrom_<?=$i?>" name="txtConf_<?=$i?>" type="text" value="<?=$myF->timeStyle($row['conf_from']);?>" class="no_string" style="width:35px; margin:0;" alt="time">
							<input id="txtConfTo_<?=$i?>" name="txtConf_<?=$i?>" type="text" value="<?=$myF->timeStyle($row['conf_to']);?>" class="no_string" style="width:35px; margin:0;" max="0" alt="time">
							<span id="lblConfTime_<?=$i?>"><?=number_format($row['conf_time']);?></span>
							<span id="lblTimeStr_<?=$i?>">분</span><?
						}else{?>
							<span><?=$myF->timeStyle($row['conf_from']);?></span>
							<span><?=$myF->timeStyle($row['conf_to']);?></span>
							<span id="lblConfTime_<?=$i?>"><?=$row['conf_time'].(!empty($row['conf_time']) ? '분' : '');?></span><?
						}?>
					</td>
					<td class="left" style="color:#000000;"><?=$lsSugaNm.($row['bipay_yn'] == 'Y' ? '(<span style=\'color:#ff0000;\'>비</span>)' : '');?></td><?
						if ($row['kind'] == '0' && $row['bipay_yn'] != 'Y'){?>
							<td class="right" style="color:#000000; background-color:#efefef; cursor:pointer;" onclick="lfConfCut(this,<?=$i;?>);"><?=Number_Format($liConfPay);?></td><?
						}else{?>
							<td class="right" style="color:#000000; background-color:#efefef;"><?=Number_Format($liConfPay);?></td><?
						}?>
					<td class="right" style="color:#000000;"><?=number_format($liPlanAmt);?></td>
					<td class="left"><?
						if ($lbEdit){?>
							<input id="txtMem1_<?=$i?>" name="txtMem_<?=$i?>" type="text" value="<?=$lsMemNm1;?>" jumin="<?=$lsMemCd1;?>" style="width:60px; margin:0; cursor:default;" alt="not" readonly><?
							if (($row['kind'] == '0' && $row['kind_cd'] == '500') ||
								($row['kind'] == '4' && ($row['kind_cd'] == '200' || $row['kind_cd'] == '500'))){?>
								<input id="txtMem2_<?=$i?>" name="txtMem_<?=$i?>" type="text" value="<?=$lsMemNm2;?>" jumin="<?=$lsMemCd2;?>" style="width:60px; margin:0; cursor:default;" alt="not" readonly><?
							}
						}else{?>
							<span><?=$lsMemNm1;?></span><?
							if (($row['kind'] == '0' && $row['kind_cd'] == '500') ||
								($row['kind'] == '4' && ($row['kind_cd'] == '200' || $row['kind_cd'] == '500'))){?>
								/ <span><?=$lsMemNm2;?></span><?
							}
						}?>

					</td>
					<td class="left last"><?
						if ($lbEdit){
							if ($row['request'] != 'LOG'){?>
								<img src="../image/btn_copy_2.png" onclick="lfSvcCopy(<?=$i?>);"><?
							}?>
							<img src="../image/btn_cancel_2.png" onclick="lfSvcCancel(<?=$i?>);" style="<?=$row['request'] == 'LOG' ? 'margin-left:32px;' : '';?>"><?
						}?>
					</td>
				</tr>
				<div id="lblCutGbn_<?=$i;?>" style="position:absolute; width:auto; font-size:9px; color:red; display:none;"><?=$row['cut_gbn'];?>%</div><?
			}

			$conn->row_free();?>
		</tbody>
		<tfoot>
			<tr>
				<td id="lblMsg" class="bottom last" colspan="8"></td>
			</tr>
		</tfoot>
	</table>
	<input id="code" name="code" type="hidden" value="<?=$code;?>">
	<input id="kind" name="kind" type="hidden" value="<?=$kind;?>">
	<input id="year" name="year" type="hidden" value="<?=$year;?>">
	<input id="month" name="month" type="hidden" value="<?=$month;?>">
	<input id="day" name="day" type="hidden" value="<?=$day;?>">
	<input id="jumin" name="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
	<input id="ynClose" name="ynClose" type="hidden" value="<?=$ynClose;?>">

	<input id="find_year" name="find_year" type="hidden" value="<?=$_POST['find_year'];?>">
	<input id="find_kind" name="find_kind" type="hidden" value="<?=$_POST['find_kind'];?>">
	<input id="find_name" name="find_name" type="hidden" value="<?=$_POST['find_name'];?>">
	</form>
	<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
		//setTimeout('lfSetTot()',10);
		setTimeout('lfSetCutLabel()',10);
	});

	//수가
	var li270Cnt = 0;

	$('input[name^="txtConf_"]').unbind('change').change(function(){
		var lsIdx = $(this).attr('name').split('txtConf_').join('');
		var liCnt = 1;
		var obj   = $('#trVal_'+lsIdx);

		$(this).css('color','#0000ff');

		if ($(obj).attr('bipayYn') == 'Y'){
			var liVal  = __str2num($(obj).attr('bipay'));

			$(obj).attr('sugaVal',liVal);
			//$('td', $(obj)).eq(4).css('color','#0000ff').text(__num2str(liVal));
			$('td', $(obj)).eq(4).css('color','#0000ff').text(__num2str(liVal));
			$(obj).attr('flag','Y');
		}else{
			if ($('#txtMem2_'+lsIdx).attr('jumin')) liCnt ++;
			if ($('#txtConfFrom_'+lsIdx).val() && $('#txtConfTo_'+lsIdx).val()){
				//var lsFrom = $('#txtConfFrom_'+lsIdx).val();
				//var lsTo   = $('#txtConfTo_'+lsIdx).val();
				var lsFrom = $('#txtConfFrom_'+lsIdx).val().split(':').join('');
				var lsTo   = $('#txtConfTo_'+lsIdx).val().split(':').join('');
				var liFH   = parseInt(lsFrom.substring(0,2),10);
				var liFM   = parseInt(lsFrom.substring(2,4),10);
				var liTH   = parseInt(lsTo.substring(0,2),10);
				var liTM   = parseInt(lsTo.substring(2,4),10);
				var liFrom = liFH * 60 + liFM;
				var liTo   = liTH * 60 + liTM;

				if (liFrom > liTo){
					liTo += 24 * 60;
				}

				if ($(obj).attr('svcCd') == '0'){
					if (liTo - liFrom >= 300){
						li270Cnt ++;
					}
				}

				if (li270Cnt > 4){
					liTo = liFrom + 4 * 60;

					lsFrom = $('#txtConfFrom_'+lsIdx).val();
					liTH   = Math.floor(liTo / 60);
					liTM   = liTo % 60;
					lsTo   = (liTH < 10 ? '0' : '')+liTH+':'+(liTM < 10 ? '0' : '')+liTM;

					$('#txtConfTo_'+lsIdx).val(lsTo);
				}else{
					lsFrom = $('#txtConfFrom_'+lsIdx).val();
					lsTo   = $('#txtConfTo_'+lsIdx).val();
				}

				$.ajax({
					type  : 'POST'
				,	async : false
				,	url   : '../find/find_suga.php'
				,	data  : {
						'code'     : $('#code').val()
					,	'svcCd'    : $(obj).attr('svcCd')
					,	'svcKind'  : $(obj).attr('svcKind')
					,	'date'     : $('#year').val()+$('#month').val()+($(obj).attr('date') < 10 ? '0' : '')+$(obj).attr('date')
					,	'fromTime' : lsFrom //$('#txtConfFrom_'+lsIdx).val()
					,	'toTime'   : lsTo //$('#txtConfTo_'+lsIdx).val()
					,	'ynFamily' : $(obj).attr('ynFamily')
					,	'bathKind' : $(obj).attr('bathKind')
					,	'svcVal'   : $(obj).attr('svcVal')
					,	'svcLvl'   : $(obj).attr('svcLvl')
					,	'memCnt'   : liCnt
					}
				,	success: function(result){
						var val = __parseStr(result);

						if ($(obj).attr('holidayYn') == 'Y'){
							var liVal = val['costHoliday'];
						}else{
							var liVal = val['costTotal'];
						}

						$(obj).attr('sugaCd',val['code'])
							  .attr('sugaNm',val['name'])
							  .attr('sugaVal',liVal);
						$('td', $(obj)).eq(4).css('color','#0000ff').text(val['name']);

						var orgAmt  = __str2num(liVal);
						var rate    = __str2num($(obj).attr('rate'));
						var expense = orgAmt * (rate / 100);
						var sugaVal = orgAmt - expense;
						var downVal = sugaVal * (__str2num($(obj).attr('cutGbn')) * 0.01);

						sugaVal = orgAmt - downVal;
						orgAmt  = Math.round(sugaVal);

						$('td', $(obj)).eq(5).css('color','#0000ff').text(__num2str(orgAmt));

						if ($(obj).attr('svcCd') == '0' ||
							$(obj).attr('svcCd') == '4'){
						}else{
							val['procTime'] = val['procTime'] * 60;
						}

						$('#lblConfTime_'+lsIdx).css('color','#0000ff').text(__num2str(val['procTime']));
					}
				});

				$(obj).attr('flag','Y');
			}else{
				$('td', $(obj)).eq(5).text('0');
			}
		}

		setTimeout('lfSetTot()',10);
	});

	//담당자
	$('input[name^="txtMem_"]').unbind('click').click(function(){
		var lsIdx = $(this).attr('name').split('txtMem_').join('');
		var obj   = $('#trVal_'+lsIdx);

		if ($(obj).attr('svcCd') == '0' && $(obj).attr('svcKind') == '500'){
		}else{
			if ($(this).attr('id') == 'txtMem2_'+lsIdx){
				return false;
			}
		}

		var liIdx = 1;

		if ($(this).attr('id') == 'txtMem2_'+lsIdx){
			liIdx = 2;
		}

		_planMemFind(liIdx+'_'+lsIdx
					,$('#code').val()
					,$('#jumin').val()
					,$(obj).attr('svcCd')
					,($('#txtMem1_'+lsIdx).attr('jumin')+($('#txtMem2_'+lsIdx).attr('jumin') ? ','+$('#txtMem2_'+lsIdx).attr('jumin') : ''))
					,$(obj).attr('ynFamily')
					,'lfMemFindResult');

		$(obj).attr('flag','Y');
	});

	//담당자 입력
	function lfMemFindResult(asObj){
		var val = __parseStr(asObj);

		$('#txtMem'+val['idx']).css('color','#0000ff').attr('jumin',val['jumin']).val(val['name']);
	}

	//복사
	function lfSvcCopy(asIdx){
		if (asIdx == 'ALL'){
			var result = showModalDialog('../inc/_msg.php?gubun=100', window, 'dialogWidth:400px; dialogHeight:150px; dialogHide:yes; scroll:no; status:no');

			if (result != 1 && result != 2) return;

			$('[id^="trVal_"]').each(function(){
				var lsIdx = $(this).attr('id').split('trVal_').join('');

				if ((result == 1 && (!$('#txtConfFrom_'+lsIdx).val() || !$('#txtConfTo_'+lsIdx).val())) || result == 2){
					lfSvcCopySub(lsIdx);
				}
			});
		}else{
			lfSvcCopySub(asIdx);
		}

		setTimeout('lfSetTot()',10);
	}

	//취소
	function lfSvcCancel(asIdx){
		if (asIdx == 'ALL'){
			if (!confirm('전체취소를 실행하시면 입력된 데이타는 삭제되며 일정의 상태가 완료에서 미수행으로 변경됩니다.\n전체취소를 실행하시겠습니까?')) return;
			$('[id^="trVal_"]').each(function(){
				var lsIdx = $(this).attr('id').split('trVal_').join('');
				lfSvcCancelSub(lsIdx);
			});
		}else{
			lfSvcCancelSub(asIdx);
		}

		setTimeout('lfSetTot()',10);
	}

	function lfSvcCopySub(asIdx){
		var obj    = $('#trVal_'+asIdx);
		var lsFrom = $('#lblPlanFrom_'+asIdx).text();
		var lsTo   = $('#lblPlanTo_'+asIdx).text();
		var lsTime = $('#lblPlanTime_'+asIdx).text();

		$('#txtConfFrom_'+asIdx).css('color','#0000ff').val(lsFrom);
		$('#txtConfTo_'+asIdx).css('color','#0000ff').val(lsTo);
		$('#lblConfTime_'+asIdx).css('color','#0000ff').text(lsTime);
		$('#txtConfFrom_'+asIdx).change();
	}

	function lfSvcCancelSub(asIdx){
		var obj = $('#trVal_'+asIdx);

		$('#txtConfFrom_'+asIdx).css('color','#000000').val('');
		$('#txtConfTo_'+asIdx).css('color','#000000').val('');
		$('#lblConfTime_'+asIdx).css('color','#000000').text('');
		$('#txtConfFrom_'+asIdx).change();
		$(obj).attr('flag','N');
	}

	//합계금액
	function lfSetTot(){
		var liPlanTime = 0;
		var liConfTime = 0;
		var liLimitPay = __str2num($('#lblLimitPay').text()); //한도금액
		var liPlanAmt  = 0 //계획급여
		,	liConfAmt  = 0; //실적급여

		$('tr[id^="trVal_"][svcCd="0"][bipayYn!="Y"][svcKind!="200"]').each(function(){
			var lsIdx = $(this).attr('id').split('trVal_').join('');

			liPlanTime += __str2num($('#lblPlanTime_'+lsIdx).text());
			liConfTime += __str2num($('#lblConfTime_'+lsIdx).text());
			liPlanAmt += __str2num($('td', $(this)).eq(6).text());
			liConfAmt += __str2num($('td', $(this)).eq(5).text());
		});

		$('tr[id^="trVal_"][svcCd="0"][bipayYn!="Y"][svcKind="200"]').each(function(){
			var lsIdx = $(this).attr('id').split('trVal_').join('');

			liPlanTime += __str2num($('#lblPlanTime_'+lsIdx).text());
			liConfTime += __str2num($('#lblConfTime_'+lsIdx).text());
			liPlanAmt += __str2num($('td', $(this)).eq(6).text());
			liConfAmt += __str2num($('td', $(this)).eq(5).text());
		});

		$('tr[id^="trVal_"][svcCd!="0"][bipayYn!="Y"]').each(function(){
			var lsIdx = $(this).attr('id').split('trVal_').join('');

			liPlanTime += __str2num($('#lblPlanTime_'+lsIdx).text());
			liConfTime += __str2num($('#lblConfTime_'+lsIdx).text());
			liPlanAmt += __str2num($('td', $(this)).eq(6).text());
			liConfAmt += __str2num($('td', $(this)).eq(5).text());
		});


		$('#lblTotPlanTime').text(__num2str(liPlanTime)+'분');
		$('#lblTotConfTime').text(__num2str(liConfTime)+'분');
		$('#lblTotPlanAmt').text(__num2str(liPlanAmt));
		$('#lblTotConfAmt').text(__num2str(liConfAmt));
		$('#TotPlanAmt').val(__num2str(liPlanAmt));
		$('#TotConfAmt').val(__num2str(liConfAmt));
	}

	function lfBefore(){
		var f = document.f;

		//f.action = './result_month.php?mode=2';
		f.action = './result_day.php?menuTopId=D';
		f.submit();
	}

	function lfSave(){
		$('[id^="trVal_"]').each(function(){
			var liAdd = 1;
			var lsIdx = $(this).attr('id').split('trVal_').join('');

			if ($('#txtConfFrom_'+lsIdx).val() && $('#txtConfTo_'+lsIdx).val()){
			}else if ($(this).attr('saveVal') == '0' && !$('#txtConfFrom_'+lsIdx).val() && !$('#txtConfTo_'+lsIdx).val()){
				liAdd = 2;
			}else if ($(this).attr('saveVal') == '0' && !$('#txtConfFrom_'+lsIdx).val()){
				liAdd = 3;
			}else if ($(this).attr('saveVal') == '0' && !$('#txtConfTo_'+lsIdx).val()){
				liAdd = 4;
			}

			if ($(this).attr('saveVal') == '0' && !$(this).attr('sugaNm')){
				liAdd = 5;
			}

			if (!$('#txtMem1_'+lsIdx).val()){
				liAdd = 6;
			}

			if ($(this).attr('svcKind') == '500'){
				if (!$('#txtMem2_'+lsIdx).val()){
					liAdd = 7;
				}
			}

			if (liAdd == 1){
				if ($(this).attr('flag') == 'Y'){
					$(this).attr('addRow','Y').attr('stat','1').css('background-color','#dcdeff');
				}else{
					$(this).attr('addRow','N').css('background-color','#ffffff');
				}
			}else{
				if ($(this).attr('stat') == '1'){
					$(this).attr('addRow','Y').attr('stat','9').css('background-color','#dcdeff');
				}else{
					$(this).attr('addRow','N').css('background-color','#ffffff');
				}
			}
		});

		if (!confirm('선택된 리스트만 저장됩니다.\n저장을 실행하시겠습니까?')){
			$('[id^="trVal_"]').attr('addRow','N').css('background-color','#ffffff');
			return;
		}

		var para = '';

		$('[id^="trVal_"][addRow="Y"]').each(function(){
			var lsIdx = $(this).attr('id').split('trVal_').join('');
			var liVal = __str2num($(this).attr('saveVal'));

			if (liVal == 0){
				liVal = __str2num($(this).attr('sugaVal'));
			}

			if (para) para += '/';

			para = para
				 + 'svcCd='+$(this).attr('svcCd')
				 + '&jumin='+$(this).attr('jumin')
				 + '&date='+$('#year').val()+$('#month').val()+($(this).attr('date') < 10 ? '0' : '')+$(this).attr('date')
				 + '&planFrom='+$(this).attr('planFrom')
				 + '&planSeq='+$(this).attr('planSeq')
				 + '&from='+$('#txtConfFrom_'+lsIdx).val().split(':').join('')
				 + '&to='+$('#txtConfTo_'+lsIdx).val().split(':').join('')
				 + '&time='+$('#lblConfTime_'+lsIdx).text()
				 + '&sugaCd='+$(this).attr('sugaCd')
				 + '&sugaVal='+liVal
				 + '&stat='+$(this).attr('stat')
				 + '&memCd1='+$('#txtMem1_'+lsIdx).attr('jumin')
				 + '&memNm1='+$('#txtMem1_'+lsIdx).val()
				 + '&memCd2='+($('#txtMem2_'+lsIdx).attr('jumin') ? $('#txtMem2_'+lsIdx).attr('jumin') : '')
				 + '&memNm2='+($('#txtMem2_'+lsIdx).val() ? $('#txtMem2_'+lsIdx).val() : '')
				 + '&cutGbn='+$(this).attr('cutGbn');
		});

		if (para){
			$.ajax({
				type  : 'POST'
			,	async : false
			,	url   : './result_detail_save_day.php'
			,	data  : {
					'code':$('#code').val()
				,	'para':para
				}
			,	success: function(result){
					if (result == 1){
						alert('정상적으로 처리되었습니다.');
						$('[id^="trVal_"]').attr('addRow','N').css('color','#000000').css('background-color','#ffffff');
					}else if (result == 9){
						alert('처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
						$('[id^="trVal_"]').attr('addRow','N').css('background-color','#ffffff');
					}else{
						alert(result);
					}

					if (result == 1){
						lfSetCutLabel();
					}
				}
			});
		}
	}

	function lfConfCut(obj, i){
		var gbn  = $('#trVal_'+i).attr('cutGbn');
		var html = '<div style="position:absolute; z-index:100; width:auto; line-height:1em; border:2px solid #0e69b0;">'
				 + '<div style="margin-left:'+($(obj).width()+5)+'; border-left:1px solid #0e69b0; width:220px; height:25px; background-color:#ffffff;">'
				 + '<input id="optCut0" name="opt" type="radio" class="radio" value="0" onclick="lfConfCutSet(this,'+i+');" '+(gbn == '0' ? 'checked' : '')+'><label for="optCut0">감액없음</label>&nbsp;'
				 + '<input id="optCut5" name="opt" type="radio" class="radio" value="5" onclick="lfConfCutSet(this,'+i+');" '+(gbn == '5' ? 'checked' : '')+'><label for="optCut5">감액5%</label>&nbsp;'
				 + '<input id="optCut10" name="opt" type="radio" class="radio" value="10" onclick="lfConfCutSet(this,'+i+');" '+(gbn == '10' ? 'checked' : '')+'><label for="optCut10">감액10%</label>&nbsp;'
				 + '</div></div>';

		$('#divTemp')
			.css('left',$(obj).offset().left - 1)
			.css('top',$(obj).offset().top - 2)
			.html(html).show();
	}

	function lfConfCutSet(gbn,i){
		var obj = $('#trVal_'+i);
		var val = __str2num($(gbn).val());
		var orgAmt  = __str2num($(obj).attr('sugaVal'));
		var rate    = __str2num($(obj).attr('rate'));
		var expense = orgAmt * (rate / 100);

		if ($('#year').val()+$('#month').val() >= '201401'){
			expense = 0;
		}

		var sugaVal = orgAmt - expense;
		var downVal = sugaVal * (val * 0.01);

		sugaVal = orgAmt - downVal;
		//orgAmt  = Math.round(sugaVal);
		orgAmt = Math.round(sugaVal / 10) * 10;

		if (val == $(obj).attr('cutGbn')){
			$('#divTemp').html('');
			lfSetCutLabel();
			return;
		}

		$(obj).attr('cutGbn',val).attr('flag','Y').attr('addRow','Y');

		$('td', $(obj)).eq(5).css('color','red').text(__num2str(orgAmt));
		$('#divTemp').html('');
		lfSetCutLabel();
	}

	function lfSetCutLabel(){
		$('tr[id^="trVal_"]').each(function(){
			var i = $(this).attr('id').split('trVal_').join('');

			if ($(this).attr('cutGbn') == '0'){
				$('#lblCutGbn_'+i).hide();
			}else{
				$('#lblCutGbn_'+i)
					.css('left',($('td',this).eq(5).offset().left+2)+'px')
					.css('top',($('td',this).eq(5).offset().top-7)+'px')
					.text($(this).attr('cutGbn')+'%')
					.show();
			}
		});

		lfSetTot();
	}

	function IfExcel(){
		var f = document.f;

		f.action = './result_detail_excel.php?mode=DAY_EXCEL';
		f.submit();
	}

	function lfSearch(){
		var f = document.f;

		f.action = 'result_detail_day.php';
		f.submit();
	}

	</script><?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
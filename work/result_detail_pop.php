<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$kind  = $_POST['kind'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$jumin = $_POST['jumin'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	//가사간병 등록내역
	$sql = 'SELECT svc_val AS val
			  FROM client_his_nurse
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			   AND date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   AND date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
			 ORDER BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$laVoucher['1'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//노인돌봄
	$sql = 'SELECT svc_val AS val
			  FROM client_his_old
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			   AND date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   AND date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
			 ORDER BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($row['val'] == '1'){
			$row['val'] = 'V';
		}else if ($row['val'] == '2'){
			$row['val'] = 'D';
		}

		$laVoucher['2'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//산모신생아
	$sql = 'SELECT svc_val AS val
			  FROM client_his_baby
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			   AND date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   AND date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
			 ORDER BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$laVoucher['3'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//장애인활동지원
	$sql = 'SELECT svc_val AS val
			,      svc_lvl AS lvl
			  FROM client_his_dis
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			   AND date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   AND date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
			 ORDER BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($row['val'] == '1'){
			$row['val'] = 'A';
		}else if ($row['val'] == '2'){
			$row['val'] = 'C';
		}

		$laVoucher['4'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//바우처 새성내역
	$sql = 'SELECT voucher_kind AS kind
			,      voucher_gbn AS val
			,      voucher_lvl AS lvl
			  FROM voucher_make
			 WHERE org_no        = \''.$code.'\'
			   AND voucher_jumin = \''.$jumin.'\'
			   AND voucher_yymm  = \''.$year.$month.'\'
			   AND del_flag      = \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($row['kind'] == '2'){
			if ($row['val'] == '1'){
				$row['val'] = 'V';
			}else if ($row['val'] == '2'){
				$row['val'] = 'D';
			}
		}else if ($row['kind'] == '4'){
			#if ($row['val'] == '1'){
			#	$row['val'] = 'A';
			#}else if ($row['val'] == '2'){
			#	$row['val'] = 'C';
			#}

			//장애 구분 및 등급
			$sql = 'SELECT svc_val AS val
					,      svc_lvl AS lvl
					  FROM client_his_dis
					 WHERE org_no = \''.$code.'\'
					   AND jumin  = \''.$jumin.'\'
					   AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					   AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					 ORDER BY seq DESC
					 LIMIT 1';
			$tmp = $conn->get_array($sql);

			if ($tmp['val'] == '1'){
				$row['val'] = 'A';
			}else if ($tmp['val'] == '2'){
				$row['val'] = 'A';
			}

			UnSet($tmp);
		}

		$laVoucher[$row['kind']] = Array('val'=>$row['val'],'lvl'=>$row['lvl']);
	}

	$conn->row_free();

	//마감처리여부
	$ynClose = $conn->_isCloseResult($code, $year.$month);
	$lsCName = $conn->client_name($code, $jumin);?>

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
			<col width="60px">
			<col width="100px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th>년월</th>
				<td class="left"><?=$year;?>.<?=$month;?></td>
				<th>수급자명</th>
				<td class="left"><?=$lsCName;?></td>
				<td class="right last">
					<span class="btn_pack m icon"><span class="save"></span><button type="button" onclick="lfSave();">저장</button></span>
					<!--span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="">엑셀</button></span-->
					<span class="btn_pack m icon"><span class="before"></span><button type="button" onclick="self.close();">닫기</button></span>
				</td>
			</tr>
		</tbody>
	</table><?
	/*********************************************************
	 *	재가요양
	 *********************************************************/
	include_once('./result_detail_care_new.php');

	/*********************************************************
	 *	장애인활동지원
	 *********************************************************/
	include_once('./result_detail_dis.php');

	/*********************************************************
	 *	바우처 및 기타유료
	 *********************************************************/
	include_once('./result_detail_other.php');?>
	<script type="text/javascript" src="../iljung/plan.js"></script>
	<div class="title title_border">서비스 내역</div>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="45px">
			<col width="120px">
			<col width="140px">
			<col width="150px">
			<col width="70px">
			<col width="70px">
			<col width="130px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">일자</th>
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
				<td class="center sum"><span id="lblTotPlanTime"></span></td>
				<td class="center sum"><span id="lblTotConfTime"></span></td>
				<td class="center sum"><span style="font-weight:normal;"><? if($liLimitAmt > 0){?>재가한도금액 : </span><span id="lblLimitPay" style="font-weight:bold;"><?=number_format($liLimitAmt);?></span><?} ?></td>
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

			$sql = 'select t01_mkind as kind
					,      cast(substring(t01_sugup_date,7) as unsigned) as date
					,      dayofweek(date_format(t01_sugup_date,\'%Y-%m-%d\')) as weekly
					,      t01_sugup_fmtime as plan_from
					,      t01_sugup_totime as plan_to
					,      t01_sugup_soyotime as plan_time
					,      t01_sugup_seq as seq
					,      t01_suga_code1 as plan_suga_cd
					,      t01_suga_tot as plan_suga
					,      t01_conf_fmtime as conf_from
					,      t01_conf_totime as conf_to
					,      t01_conf_soyotime as conf_time
					,      t01_conf_suga_code as conf_suga_cd
					,      t01_conf_suga_value as conf_suga
					,      t01_status_gbn as stat
					,      t01_svc_subcode as kind_cd
					,      t01_yoyangsa_id1 as conf_cd1
					,      t01_yoyangsa_id2 as conf_cd2
					,      t01_yname1 as conf_nm1
					,      t01_yname2 as conf_nm2
					,      t01_mem_cd1 as plan_cd1
					,      t01_mem_nm1 as plan_nm1
					,      t01_mem_cd2 as plan_cd2
					,      t01_mem_nm2 as plan_nm2
					,      t01_holiday as holiday_yn
					,      t01_toge_umu as family_yn
					,      t01_bipay_umu as bipay_yn
					,      case when date_format(now(), \'%Y%m%d\') >= t01_sugup_date then \'Y\' else \'N\' end as day_yn
					,      t01_bipay_kind as bipay_kind
					,      t01_bipay1 as bipay1
					,      t01_bipay2 as bipay2
					,      t01_bipay3 as bipay3
					,      t01_request as request
					  from t01iljung
					 where t01_ccode  = \''.$code.'\'
					   and t01_jumin  = \''.$jumin.'\'
					   and t01_del_yn = \'N\'
					   AND	t01_mkind != \'6\'
					   and left(t01_sugup_date,6) = \''.$year.$month.'\'
					 order by date, kind';

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

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

				if ($lsSugaCd == 'CBKD1'){
					$lsBathKind = '1';
				}else if ($lsSugaCd == 'CBKD2'){
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
						svcCd="<?=$row['kind'];?>"
						svcKind="<?=$row['kind_cd'];?>"
						date="<?=$row['date'];?>"
						planFrom="<?=$row['plan_from'];?>"
						planSeq="<?=$row['seq'];?>"
						ynFamily="<?=($row['family_yn'] == 'Y' ? 'Y' : 'N');?>"
						bathKind="<?=$lsBathKind;?>"
						svcVal="<?=$laVoucher[$row['kind']]['val'];?>"
						svcLvl="<?=$laVoucher[$row['kind']]['lvl'];?>"
						sugaCd="<?=$lsSugaCd;?>"
						sugaNm="<?=$lsSugaNm;?>"
						sugaVal="<?=$liConfAmt;?>"
						saveVal="0"
						from="<?=$myF->timeStyle($row['conf_from']);?>"
						to="<?=$myF->timeStyle($row['conf_to']);?>"
						time="<?=$row['conf_time'];?>"
						memCd1="<?=$lsMemCd1;?>"
						memNm1="<?=$lsMemNm1;?>"
						memCd2="<?=$lsMemCd2;?>"
						memNm2="<?=$lsMemNm2;?>"
						stat="<?=$row['stat'];?>"
						bipayYn="<?=$row['bipay_yn'];?>"
						bipay="<?=IntVal($row['plan_suga']);?>"
						holidayYn="<?=$row['holiday_yn'];?>"
						addRow="N"
						flag="N"
						overYn="N">
					<td class="center" style="color:<?=$color;?>;"><?=$row['date'];?>(<?=$laWeekly[$row['weekly']];?>)</td>
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
					<td class="left" style="color:#000000;"><?=$lsSugaNm.($row['bipay_yn'] == 'Y' ? '(<span style=\'color:#ff0000;\'>비</span>)' : '');?></td>
					<td class="right" style="color:#000000; background-color:#efefef;"><?=number_format($liConfAmt);?></td>
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
				</tr><?
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
	<input id="year" name="year" type="hidden" value="<?=$year;?>">
	<input id="month" name="month" type="hidden" value="<?=$month;?>">
	<input id="jumin" name="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
	<input id="ynClose" name="ynClose" type="hidden" value="<?=$ynClose;?>">

	<input id="find_year" name="find_year" type="hidden" value="<?=$_POST['find_year'];?>">
	<input id="find_kind" name="find_kind" type="hidden" value="<?=$_POST['find_kind'];?>">
	<input id="find_name" name="find_name" type="hidden" value="<?=$_POST['find_name'];?>">
	</form>
	<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
		setTimeout('lfSetTot()',10);
	});

	//수가
	var li270Cnt = 0;

	$('input[name^="txtConf_"]').unbind('change').change(function(){
		var lsIdx = $(this).attr('name').split('txtConf_').join('');
		var liCnt = 1;
		var obj   = $('#trVal_'+lsIdx);

		$(this).css('color','#0000ff');

		if ($(obj).attr('bipayYn') == 'Y'){
			//비급여처리
			var liVal  = __str2num($(obj).attr('bipay'));

			$(obj).attr('sugaVal',liVal);
			$('td', $(obj)).eq(4).css('color','#0000ff').text(__num2str(liVal));
			$(obj).attr('flag','Y');
		}else{
			if ($('#txtMem2_'+lsIdx).attr('jumin')) liCnt ++;
			if ($('#txtConfFrom_'+lsIdx).val() && $('#txtConfTo_'+lsIdx).val()){
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

				if (liTo - liFrom >= 300){
					li270Cnt ++;
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
						$('td', $(obj)).eq(3).css('color','#0000ff').text(val['name']);
						$('td', $(obj)).eq(4).css('color','#0000ff').text(__num2str(liVal));

						if ($(obj).attr('svcCd') == '0' ||
							$(obj).attr('svcCd') == '4'){
						}else{
							val['procTime'] = val['procTime'] * 60;
						}

						$('#lblConfTime_'+lsIdx).css('color','#0000ff').text(__num2str(val['procTime']));

						//if ('<?=$debug;?>' == '1'){
						//	$('td', $(obj)).eq(5).text(result);
						//}
					}
				});

				$(obj).attr('flag','Y');
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
		var liLimitPay = __str2num($('#lblLimitPay').text()); //한도금액
		var liPlanAmt  = 0 //계획급여
		,	liConfAmt  = 0; //실적급여

		$('tr[id^="trVal_"][svcCd="0"][bipayYn!="Y"][svcKind!="200"]').each(function(){
			var lsIdx = $(this).attr('id').split('trVal_').join('');

			liPlanAmt += __str2num($('td', $(this)).eq(5).text());
			liConfAmt += __str2num($('td', $(this)).eq(4).text());
		});

		$('tr[id^="trVal_"][svcCd="0"][bipayYn!="Y"][svcKind="200"]').each(function(){
			var lsIdx = $(this).attr('id').split('trVal_').join('');

			liPlanAmt += __str2num($('td', $(this)).eq(5).text());
			liConfAmt += __str2num($('td', $(this)).eq(4).text());

			if (liConfAmt > liLimitPay){
				$('td', $(this)).eq(4).css('color','#ff0000');
			}
		});

		$('tr[id^="trVal_"][svcCd!="0"][bipayYn!="Y"]').each(function(){
			var lsIdx = $(this).attr('id').split('trVal_').join('');

			liPlanAmt += __str2num($('td', $(this)).eq(5).text());
			liConfAmt += __str2num($('td', $(this)).eq(4).text());
		});

		$('#lblTotPlanAmt').text(__num2str(liPlanAmt));
		$('#lblTotConfAmt').text(__num2str(liConfAmt));
	}

	function lfBefore(){
		var f = document.f;

		f.action = './result_month.php?mode=2';
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
				 + '&memNm2='+($('#txtMem2_'+lsIdx).val() ? $('#txtMem2_'+lsIdx).val() : '');
		});

		if (para){
			$.ajax({
				type  : 'POST'
			,	async : false
			,	url   : './result_detail_save_new.php'
			,	data  : {
					'code':$('#code').val()
				,	'jumin':$('#jumin').val()
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
				}
			});
		}
	}
	</script><?
	include_once('../inc/_footer.php');
?>
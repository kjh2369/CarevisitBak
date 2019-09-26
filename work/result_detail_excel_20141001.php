<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );

	$code  = $_POST['code'];
	$kind  = $_POST['kind'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$jumin = $_POST['jumin'];
	$TotPlanAmt = $_POST['TotPlanAmt'];
	$TotConfAmt = $_POST['TotConfAmt'];
	$mode  = $_GET['mode'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$dt = $year.$month.$day;
	$len = StrLen($dt);

	$conn->set_name('utf8');

	//가사간병 등록내역
	$sql = 'SELECT	jumin
			,		svc_val AS val
			FROM	client_his_nurse
			WHERE	org_no = \''.$code.'\' '.($mode != 'DAY_EXCEL' ? 'AND jumin  = \''.$jumin.'\'' : '').'
			AND		LEFT(REPlACE(from_dt,\'-\',\'\'),'.$len.') <= \''.$dt.'\'
			AND		LEFT(REPlACE(to_dt,  \'-\',\'\'),'.$len.') >= \''.$dt.'\'
			ORDER	BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$laVoucher[$row['jumin']]['1'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//노인돌봄
	$sql = 'SELECT	jumin
			,		svc_val AS val
			FROM	client_his_old
			WHERE	org_no = \''.$code.'\' '.($mode != 'DAY_EXCEL' ? 'AND jumin  = \''.$jumin.'\'' : '').'
			AND		LEFT(REPlACE(from_dt,\'-\',\'\'),'.$len.') <= \''.$dt.'\'
			AND		LEFT(REPlACE(to_dt,  \'-\',\'\'),'.$len.') >= \''.$dt.'\'
			ORDER	BY seq';

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

		$laVoucher[$row['jumin']]['2'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//산모신생아
	$sql = 'SELECT	jumin
			,		svc_val AS val
			FROM	client_his_baby
			WHERE	org_no = \''.$code.'\' '.($mode != 'DAY_EXCEL' ? 'AND jumin  = \''.$jumin.'\'' : '').'
			AND		LEFT(REPlACE(from_dt,\'-\',\'\'),'.$len.') <= \''.$dt.'\'
			AND		LEFT(REPlACE(to_dt,  \'-\',\'\'),'.$len.') >= \''.$dt.'\'
			ORDER BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		$laVoucher[$row['jumin']]['3'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//장애인활동지원
	$sql = 'SELECT	jumin
			,		svc_val AS val
			,		svc_lvl AS lvl
			FROM	client_his_dis
			WHERE	org_no = \''.$code.'\' '.($mode != 'DAY_EXCEL' ? 'AND jumin  = \''.$jumin.'\'' : '').'
			AND		LEFT(REPlACE(from_dt,\'-\',\'\'),'.$len.') <= \''.$dt.'\'
			AND		LEFT(REPlACE(to_dt,  \'-\',\'\'),'.$len.') >= \''.$dt.'\'
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

		$laVoucher[$row['jumin']]['4'] = Array('val'=>$row['val'],'lvl'=>'');
	}

	$conn->row_free();

	//바우처 새성내역
	$sql = 'SELECT	voucher_jumin AS jumin
			,		voucher_kind AS kind
			,		voucher_gbn AS val
			,		voucher_lvl AS lvl
			FROM	voucher_make
			WHERE	org_no			= \''.$code.'\' '.($mode != 'DAY_EXCEL' ? 'AND voucher_jumin  = \''.$jumin.'\'' : '').'
			AND		voucher_jumin	= \''.$jumin.'\'
			AND		del_flag		= \'N\'';

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
			$row['val'] = $laVoucher['4']['val'];
		}

		$laVoucher[$row['jumin']][$row['kind']] = Array('val'=>$row['val'],'lvl'=>$row['lvl']);
	}

	$conn->row_free();

	//마감처리여부
	$ynClose = $conn->_isCloseResult($code, $year.$month);
	$lsCName = $conn->client_name($code, $jumin);?>


	<style>
		.head { text-align:center; background-color:#eeeeee; };
		.left { text-align:left; };
		.center { text-align:center; };
		.right { text-align:right; };
		.title { font-size:10pt; font-weight:bold; }
	</style>

	<?
	$sql = "select m00_cname
			  from m00center
			 where m00_mcode = '$code'";
	$c_name = $conn -> get_data($sql);

	$r_dt = date('Y.m.d',mktime());

	if ($mode != 'DAY_EXCEL'){?>
		<div align="center" style="font-size:15pt; font-weight:bold;"><?=$year?>년<?=$month?>월 월실적등록(수급자)</div><?
	}else{?>
		<div align="center" style="font-size:15pt; font-weight:bold;"><?=$year?>년<?=$month?>월<?=$day;?>일 일실적내역</div><?
	}?>
	<div >
		<table>
			<tr>
				<td colspan="5" style="text-align:left; font-size:12pt; font-weight:bold;">센터명 : <?=$c_name?></td>
				<td colspan="5" style="text-align:right; font-size:12pt; font-weight:bold;">일자 : <?=$r_dt?></td>
			</tr>
		</table>
	</div><?
	if ($mode != 'DAY_EXCEL'){
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
		include_once('./result_detail_other.php');
	}?>

	<div class="title">서비스 내역</div>
	<table class="my_table" border="1">
		<colgroup>
			<col width="45px">
			<col width="120px">
			<col width="140px">
			<col width="150px">
			<col width="70px">
			<col width="70px">
			<col width="130px">
		</colgroup>
		<thead>
			<tr>
				<th class="head" style="background-color:#eeeeee;">일자</th>
				<th class="head" colspan="2" style="background-color:#eeeeee;">계획</th>
				<th class="head" colspan="2" style="background-color:#eeeeee;">실적</th>
				<th class="head" colspan="2" style="background-color:#eeeeee;">제공서비스</th>
				<th class="head" style="background-color:#eeeeee;">실적급여</th>
				<th class="head" style="background-color:#eeeeee;">계획급여</th>
				<th class="head" style="background-color:#eeeeee;">담당자</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center" ></td>
				<td class="center" colspan="2" ><span ></span></td>
				<td class="center" colspan="2" ><span ></span></td>
				<td class="center" colspan="2" ><span style="font-weight:normal;"><? if($liLimitAmt > 0){?>재가한도금액 : </span><span id="lblLimitPay" style="font-weight:bold;"><?=number_format($liLimitAmt);?></span><?} ?></td>
				<td class="right" ><span style="font-weight:bold;"><?=$TotConfAmt?></span></td>
				<td class="right" ><span style="font-weight:bold;"><?=$TotPlanAmt?></span></td>
				<td class="center" ></td>
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
					   and left(m01_sdate,'.$len.') <= \''.$dt.'\'
					   and left(m01_edate,'.$len.') >= \''.$dt.'\'
					 union all
					select m11_mcode2 as suga
					,      m11_suga_cont as name
					,      m11_suga_value as cost
					  from m11suga
					 where m11_mcode  = \'goodeos\'
					   and left(m11_sdate,'.$len.') <= \''.$dt.'\'
					   and left(m11_edate,'.$len.') >= \''.$dt.'\'
					 union all
					select service_code as suga
					,      service_gbn as nm
					,      service_cost as cost
					  from suga_service
					 where org_no = \'goodeos\'
					   and left(service_from_dt,'.$len.') <= \''.$dt.'\'
					   and left(service_to_dt,  '.$len.') >= \''.$dt.'\'';
			$laSuga = $conn->_fetch_array($sql, 'suga');

			$sql = 'select	t01_mkind as kind
					,		t01_jumin AS jumin
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
					 where t01_ccode  = \''.$code.'\' '.($mode != 'DAY_EXCEL' ? 'AND t01_jumin  = \''.$jumin.'\'' : '').'
					   and t01_del_yn = \'N\'
					   and left(t01_sugup_date,'.$len.') = \''.$dt.'\'';

			if ($mode != 'DAY_EXCEL'){
				$sql .= ' order by date, kind';
			}else{
				$sql .= ' order by date, plan_from, plan_to';
			}

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
					$lsConf = '<span id="lblPlanFrom_'.$i.'">'.$myF->timeStyle($row['conf_from']).'</span>~<span id="lblPlanTo_'.$i.'">'.$myF->timeStyle($row['conf_to']).'</span> (<span id="lblPlanTime_'.$i.'">'.$row['plan_time'].'</span>분)';
				}else{
					$lsPlan    = '<span style="font-weight:bold; color:#0000ff;">RFID실적만 있음</span>';
					$liPlanAmt = 0;
				}



				if ($ynClose != 'Y' && $today >= $year.$month.($row['date'] < 10 ? '0' : '').$row['date']){
					$lbEdit = true;
				}else{
					$lbEdit = false;
				}?>
					<tr id="trVal_<?=$i?>"
						svcCd="<?=$row['kind'];?>"
						svcKind="<?=$row['kind_cd'];?>"
						date="<?=$row['date'];?>"
						planFrom="<?=$row['plan_from'];?>"
						planSeq="<?=$row['seq'];?>"
						ynFamily="<?=($row['family_yn'] == 'Y' ? 'Y' : 'N');?>"
						bathKind="<?=$lsBathKind;?>"
						svcVal="<?=$laVoucher[$row['jumin']][$row['kind']]['val'];?>"
						svcLvl="<?=$laVoucher[$row['jumin']][$row['kind']]['lvl'];?>"
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
						holidayYn="<?=$row['holiday_yn'];?>"
						addRow="N"
						flag="N"
						overYn="N">
					<td class="center" style="color:<?=$color;?>;"><?=$row['date'];?>(<?=$laWeekly[$row['weekly']];?>)</td>
					<td class="center" colspan="2"><?=$lsPlan;?></td>
					<td class="center" colspan="2"><?=$lsConf;?></td>
					<td class="left" style="color:#000000;" colspan="2"><?=$lsSugaNm.($row['bipay_yn'] == 'Y' ? '(<span style=\'color:#ff0000;\'>비</span>)' : '');?></td>
					<td class="right" style="color:#000000;"><?=number_format($liConfAmt);?></td>
					<td class="right" style="color:#000000;"><?=number_format($liPlanAmt);?></td>
					<td class="left">
						<span><?=$lsMemNm1;?></span><?
						if (($row['kind'] == '0' && $row['kind_cd'] == '500') ||
							($row['kind'] == '4' && ($row['kind_cd'] == '200' || $row['kind_cd'] == '500'))){?>
							/ <span><?=$lsMemNm2;?></span><?
						}
						?>
					</td>
				</tr><?
			}

			$conn->row_free();?>
		</tbody>
		<tfoot>
			<tr>
				<td id="lblMsg" class="bottom last" colspan="7"></td>
			</tr>
		</tfoot>
	</table>

<?
	include_once("../inc/_db_close.php");
?>
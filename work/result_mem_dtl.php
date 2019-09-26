<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$mode = $_POST['mode'];


	if($mode == 'excel'){
		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-type: charset=utf-8" );
		header( "Content-Disposition: attachment; filename=test.xls" );
		header( "Content-Description: test" );
		header( "Pragma: no-cache" );
		header( "Expires: 0" );
	}else {
		include_once('../inc/_http_uri.php');
		include_once('../inc/_login.php');
	}

	$code  = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];
	$name  = $_POST['name'];
	$svcCd = $_POST['svcCd'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$order = $_POST['order'];


	if ($svcCd == 'ALL'){
		$svcNm = '전체';
	}else{
		$svcNm = $conn->_svcNm($svcCd);
	}

	if (!$order) $order = '1';

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$sql = "select m00_cname
			  from m00center
			 where m00_mcode = '$code'";
	$c_name = $conn->get_data($sql);

	if($mode == 'excel'){ ?>
		<div style="width:auto; font-size:15pt; font-weight:bold; text-align:center;"><?=$year?>년 <?=$month?>월 요양보호사 실적내역</div>
		<table>
		<tr>
			<td colspan="4">
				<div style="font-size:11pt;">기관명 : <?=$c_name?></div>
			</td>
			<td colspan="2" style="text-align:right;">
				<div style="font-size:11pt;">직원명 : <?=$name?></div>
			</td>
			<td colspan="2" style="text-align:right;">
				<div style="font-size:11pt;">서비스 : <?=$svcNm?></div>
			</td>
		</tr><?
	}else {	?>
		<script>
			function excel(){

				var f = document.f;
			   
				f.name.value = '<?=$name?>';
				f.jumin.value = '<?=$_POST[jumin];?>';
				f.svcCd.value = '<?=$svcCd?>';
				f.year.value = '<?=$year?>';
				f.month.value = '<?=$month?>';
				f.order.value = '<?=$order?>';
				f.mode.value = "excel";

				f.action = "./result_mem_dtl.php";
				f.submit();
			}
		</script>
		<div id="loDtl">
			<table class="my_table" style="width:100%;">
				<colgroup>
					<col width="40px">
					<col width="50px">
					<col width="50px">
					<col width="70px">
					<col width="50px">
					<col width="100px">
					<col span="2">
				</colgroup>
				<tbody>
					<tr>
						<th class="center">년월</th>
						<td class="center"><?=$year;?>.<?=$month;?></td>
						<th class="center">직원명</th>
						<td class="left"><?=$name;?></td>
						<th class="center">서비스</th>
						<td class="left"><?=$svcNm;?></td>
						<td class="left last">
							<input id="rdoOrder1" name="rdoOrder" type="radio" class="radio" value="1" onclick="lfReload();" <?if($order == '1'){?>checked<?}?>><label for="rdoOrder1">고객명순</label>
							<input id="rdoOrder2" name="rdoOrder" type="radio" class="radio" value="2" onclick="lfReload();" <?if($order != '1'){?>checked<?}?>><label for="rdoOrder2">일자순</label>
						</td>
						<td class="right last">
							<span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="excel();">엑셀</button></span>
							<span class="btn_pack m icon"><span class="before"></span><button type="button" onclick="lfDetailHide();">이전</button></span>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="title title_border">서비스 내역</div><?
	} ?>


	<?
	if($mode == 'excel'){ ?>
		<table border="1"><?
	}else { ?>
		<table class="my_table" style="width:100%; margin-bottom:20px;">
			<colgroup><?
				if ($order == '1'){?>
					<col width="70px">
					<col width="50px"><?
				}else{?>
					<col width="50px">
					<col width="70px"><?
				}?>
				<col width="120px">
				<col width="120px">
				<col width="150px">
				<col width="80px">
				<col width="80px">
				<col>
			</colgroup><?
	} ?>
			<thead>
				<tr><?
					if ($order == '1'){?>
						<th class="head">고객명</th>
						<th class="head">일자</th><?
					}else{?>
						<th class="head">일자</th>
						<th class="head">고객명</th><?
					}?>
					<th class="head">계획</th>
					<th class="head">실적</th>
					<th class="head">제공서비스</th>
					<th class="head">실적급여액</th>
					<th class="head">계획급여액</th>
					<th class="head last"></th>
				</tr>
			</thead>
			<tbody><?
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

				$sql = 'select iljung.kind
						,      iljung.jumin
						,      mst.name
						,      substring(iljung.date,7,2) as date
						,      dayofweek(date_format(iljung.date,\'%Y-%m-%d\')) as weekly
						,      iljung.plan_from
						,      iljung.plan_to
						,      iljung.plan_time
						,      iljung.conf_from
						,      iljung.conf_to
						,      iljung.conf_time
						,      iljung.plan_val
						,      iljung.conf_val
						, iljung.suga_cd
						  from (
							   select t01_mkind as kind
							   ,      t01_jumin as jumin
							   ,      t01_sugup_date as date
							   ,      t01_sugup_fmtime as plan_from
							   ,      t01_sugup_totime as plan_to
							   ,      t01_sugup_soyotime as plan_time
							   ,      t01_conf_fmtime as conf_from
							   ,      t01_conf_totime as conf_to
							   ,      t01_conf_soyotime as conf_time
							   ,      t01_suga_tot as plan_val
							   ,      t01_conf_suga_value as conf_val
							   ,      case t01_status_gbn when \'1\' then t01_conf_suga_code else t01_suga_code1 end as suga_cd
								 from t01iljung
								where t01_ccode  = \''.$code.'\'';

						if ($svcCd != 'ALL'){
							$sql .= ' and t01_mkind  = \''.$svcCd.'\'';
						}

						$sql .= ' and t01_del_yn = \'N\'
								  and t01_yoyangsa_id1       = \''.$jumin.'\'
								  and left(t01_sugup_date,6) = \''.$year.$month.'\'
								union all
							   select t01_mkind
							   ,      t01_jumin
							   ,      t01_sugup_date
							   ,      t01_sugup_fmtime
							   ,      t01_sugup_totime
							   ,      t01_sugup_soyotime
							   ,      t01_conf_fmtime
							   ,      t01_conf_totime
							   ,      t01_conf_soyotime
							   ,      t01_suga_tot
							   ,      t01_conf_suga_value
							   ,      case t01_status_gbn when \'1\' then t01_conf_suga_code else t01_suga_code1 end
								 from t01iljung
								where t01_ccode  = \''.$code.'\'';

						if ($svcCd != 'ALL'){
							$sql .= ' and t01_mkind  = \''.$svcCd.'\'';
						}

						$sql .= ' and t01_del_yn = \'N\'
								  and t01_yoyangsa_id2       = \''.$jumin.'\'
								  and left(t01_sugup_date,6) = \''.$year.$month.'\'
							   ) as iljung
						  left join (
							   select min(m03_mkind) as kind
							   ,      m03_jumin as jumin
							   ,      m03_name as name
								 from m03sugupja
								where m03_ccode = \''.$code.'\'
								group by m03_jumin
							   ) as mst
							on mst.jumin = iljung.jumin';

				if ($order == '1'){
					$sql .= ' order by name, jumin, date';
				}else{
					$sql .= ' order by date, jumin';
				}
				
				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					if ($order == '1'){
						$lsVal = $row['jumin'];
					}else{
						$lsVal = $row['date'];
					}

					if ($lsTmp != $lsVal){
						$lsTmp  = $lsVal;

						if ($order == '1'){
							$laData[$lsVal]['name'] = $row['name'];
						}else{
							$laData[$lsVal]['date'] = $row['date'];
						}
						$laData[$lsVal]['rows'] = 0;
					}

					$liPlanMin = $myF->time2min($row['plan_to']) - $myF->time2min($row['plan_from']);
					$liConfMin = $myF->time2min($row['conf_to']) - $myF->time2min($row['conf_from']);

					$lsPlanTime = $myF->timeStyle($row['plan_from']).'~'.$myF->timeStyle($row['plan_to']).'('.$liPlanMin.'분)';

					if ($liConfMin > 0){
						$lsConfTime = $myF->timeStyle($row['conf_from']).'~'.$myF->timeStyle($row['conf_to']).'('.$liConfMin.'분)';
						$liConfSuga = $row['conf_val'];
					}else{
						$lsConfTime = '';
						$liConfSuga = 0;
					}

					$laData[$lsVal]['data'][$laData[$lsVal]['rows']] = array(
						'name'=>($row['name'] ? $row['name'] : '<span style="color:#ff0000;">미등록</span>')
					,	'date'=>intval($row['date'])
					,	'week'=>$row['weekly']
					,	'plan'=>$lsPlanTime
					,	'conf'=>$lsConfTime
					,	'suga'=>$laSuga[$row['suga_cd']]['name']
					,	'planAmt'=>$row['plan_val']
					,	'confAmt'=>$liConfSuga
					);
					$laData[$lsVal]['rows'] ++;


					$tot_planTime += $liPlanMin;
					$tot_confTime += $liConfMin;
					$tot_planAmt  += $row['plan_val'];
					$tot_confAmt  += $liConfSuga;
				}

				$conn->row_free();

				echo '<tr>
						<td class=\'center sum\' '.$tot_css_c.' colspan="2">합 계</td>
						<td class=\'right sum\' '.$tot_css_r.' >'.$myF->_min2timeKor($tot_planTime).'</td>
						<td class=\'right sum\' '.$tot_css_r.' >'.$myF->_min2timeKor($tot_confTime).'</td>
						<td class=\'right sum\' '.$tot_css_r.' ></td>
						<td class=\'right sum\' '.$tot_css_r.' >'.number_format($tot_confAmt).'</td>
						<td class=\'right sum\' '.$tot_css_r.' >'.number_format($tot_planAmt).'</td>
						<td class="left last sum"></td>
					 </tr>';

				if (is_array($laData)){
					foreach($laData as $cd => $laM){
						foreach($laM['data'] as $laS){?>
							<tr><?
								if ($laS['week'] == 1){
									$color = '#ff0000';
								}else if ($laS['week'] == 7){
									$color = '#0000ff';
								}else{
									$color = '#000000';
								}
								if ($lsCd != $cd){
									$lsCd  = $cd;
									if ($order == '1'){?>
										<td class="left" rowspan="<?=$laM['rows'];?>"><?=$laS['name'];?></td><?
									}else{?>
										<td class="center" rowspan="<?=$laM['rows'];?>" style="color:<?=$color;?>;"><?=$laS['date'];?>(<?=$laWeekly[$laS['week']];?>)</td><?
									}
								}

								if ($order == '1'){?>
									<td class="center" style="color:<?=$color;?>;"><?=$laS['date'];?>(<?=$laWeekly[$laS['week']];?>)</td><?
								}else{
									?>
									<td class="left"><?=$laS['name'];?></td><?
								}?>
								<td class="center"><?=$laS['plan'];?></td>
								<td class="center"><?=$laS['conf'];?></td>
								<td class="left"><?=$laS['suga'];?></td>
								<td class="right"><?=number_format($laS['confAmt']);?></td>
								<td class="right"><?=number_format($laS['planAmt']);?></td>
								<td class="left last"></td>
							</tr><?
						}
					}
				}else{?>
					<tr>
						<td class="center last" colspan="8">::검색된 데이타가 없습니다.::</td>
					</tr><?
				}?>
			</tbody>
		</table><?
	if($mode == 'excel'){
	}else { ?>
		</div><?
	}?>

	<script type="text/javascript">
		function lfDetailHide(){
			$('#loMst').show();
			$('#loDtl').remove();
		}

		function lfReload(){
			$.ajax({
				type: 'POST',
				url : './result_mem_dtl.php',
				data: {
					jumin : '<?=$ed->en($jumin);?>'
				,	name  : '<?=$name;?>'
				,	svcCd : '<?=$svcCd;?>'
				,	year  : '<?=$year;?>'
				,	month : '<?=$month;?>'
				,	order : $('input:radio[name="rdoOrder"]:checked').val()
				},
				beforeSend: function (){
					$('#loadingBody').before('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'><div style=\'width:250px; height:100px; padding-top:30px; border:2px solid #cccccc; background-color:#ffffff;\'>'+__get_loading()+'</div></div></center></div>');
				},
				success: function (result){
					$('#tempLodingBar').remove();
					$('#loDtl').remove();
					$('#loMst').after(result).hide();
				},
				error: function (){
				}
			}).responseXML;
		}
	</script><?

	include_once('../inc/_db_close.php');
?>
<?

	$year     = $_POST['year'];
	$month    = $_POST['month'];
	$mode     = $_REQUEST['mode'];
	$wrt_mode = $_POST['wrt_mode'];
	$svcCode  = $_POST['svcCode'] != '' ? $_POST['svcCode'] : $_GET['svcCode'];

	if ($wrt_mode != 2){
		include_once("../inc/_header.php");
		include_once("../inc/_login.php");
		include_once("../inc/_page_list.php");
		include_once("../inc/_ed.php");

		$code     = $_SESSION['userCenterCode'];

		/********************************
			2014평가자료에서 링크 시
		*********************************/
		if($_GET['reportYN'] == 'Y'){
			include_once('../inc/_report2014.php');

			$IPIN = $report->IPIN;

			$sql = 'select m02_yjumin
					  from m02yoyangsa
					 where m02_ccode = \''.$code.'\'
					   and m02_key   = \''.$IPIN.'\'';

			$ssn = $conn -> get_data($sql);

			$svcCode = '0';

			/*********************************
			#
			#	2014평가자료 사용유무 카운트
			#
			**********************************/
			$sql = 'select count(*)
					  from report2014_request
					 where org_no = \''.$_SESSION['userCenterCode'].'\'
					   and use_yn = \'Y\'';
			$r_cnt = $conn -> get_data($sql);

			//버튼 숨김여부
			if($r_cnt == 0){
				$display = 'style="display:none;';
			}else {
				$display = '';
			}
		}
	}else{
		include_once("../inc/_db_open.php");
		include_once("../inc/_ed.php");

		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-type: charset=utf-8" );
		header( "Content-Disposition: attachment; filename=test.xls" );
		header( "Content-Description: test" );
		header( "Content-Transfer-Encoding: binary" );
		header( "Content-Description: PHP4 Generated Data" );
		header( "Pragma: no-cache" );
		header( "Expires: 0" );

		$ssn = $ed->de($_POST['ssn']);

	}

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	include_once("../inc/_myFun.php");

	/***************************************************

		- mode 구분
		  - 1 : 요양보호사
		  - 2 : 노동부 제출용

		- wrt_mode 구분
		  - 1 : 리스트 출력
		  - 2 : 엑셀 출력

	***************************************************/

	if ($svcCode == ''){
		$svcCode = 'ALL';
	}

	$init_year = $myF->year();

	if (empty($code))     $code  = $_SESSION['userCenterCode'];
	if (empty($year))     $year  = date('Y',mktime());
	if (empty($month))    $month = date('m',mktime());
	if (empty($mode))     $mode  = 1;
	if (empty($wrt_mode)) $wrt_mode = 1;

	$month = (intval($month) < 10 ? '0' : '').intval($month);

	if($mode == 1){
		$title = '(요양보호사)';
		$col   = 22;
	}else {
		$title = '(노동부제출용)';
		$col   = 19;
	}


	if ($wrt_mode == 1){?>
		<script src="../js/account.js" type="text/javascript"></script>
		<script language='javascript'>
		<!--

		var f = null;

		function set_month(month){
			f.month.value = month;
			f.submit();
		}

		function excel(){
			f.wrt_mode.value = 2;
			f.submit();
			f.wrt_mode.value = 1;
		}

		function pdf(){
			var f = document.f;

			var w = 900;
			var h = 700;
			var l = (window.screen.width  - w) / 2;
			var t = (window.screen.height - h) / 2;

			var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');

			f.target = 'SHOW_PDF';
			f.action = '../work/work_pdf_show.php';
			f.submit();
			f.target = '_self';
			f.action = '../work/work_stat.php';
		}

		function resize(){
			var tbl = document.getElementById('tbl');
			var div = document.getElementById('div');

			var t = __getObjectTop(tbl);
			var h = document.body.offsetHeight - t - tbl.offsetHeight - 5;
			var w = document.body.offsetWidth;

			if (w < 1850)
				w = 1850;
			else
				w = w - 22;

			var sub1 = document.getElementById('sub_tbl_1');
			var sub2 = document.getElementById('sub_tbl_2');

			sub1.style.width  = w;
			sub2.style.width  = w;
			div.style.height = h;

			tbl.onscroll = function(){
				div.scrollLeft = tbl.scrollLeft;
			}

			div.onscroll = function(){
				tbl.scrollLeft = div.scrollLeft;
			}
		}

		function lfMoveYear(aiPos){
			$('#lblYear').text(parseInt($('#lblYear').text()) + aiPos);
			$('#year').val($('#lblYear').text());

			var month = 1;

			$('.my_month').each(function(){
				if ($(this).hasClass('my_month_y')){
					return false;
				}

				month ++;
			});

			set_month(month);
		}

		window.onload = function(){
			f = document.f;

			resize();

			__init_form(f);

			self.focus();
		}

		window.onresize = resize;

		-->
		</script>

		<form name="f" method="post"><?
	}
?>



<?
	if ($wrt_mode == 1){?>
	<div class="title title_border">근무현황</div>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="50px">
				<col width="50px">
				<col width="40px">
				<col width="85px">
				<col width="450px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="center">서비스</th>
					<td class="center"><?
						$svcList = $conn->kind_list($code, $gHostSvc['voucher']);?>
						<select id="svcCode" name="svcCode" style="width:auto;">
						<option value="ALL" <?=($svcCode == 'ALL' ? 'selected' : '');?>>전체</option><?
						foreach($svcList as $svc){?>
							<option value="<?=$svc['code'];?>" <?=($svcCode == $svc['code'] ? 'selected' : '');?>><?=$svc['name'];?></option><?
						}?>
						</select>
					</td>
					<th class="center">년도</th>
					<td class="last">
						<div class="left" style="padding-top:2px;">
						<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
						<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
						<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
						</div>
						<input id="year" name="year" type="hidden" value="<?=$year;?>">
					</td>
					<td class="last" style="padding-top:1px;"><?
					for($i=1; $i<=12; $i++){
						$class = 'my_month ';

						if ($i == intval($month)){
							$class .= 'my_month_y ';
							$color  = 'color:#000000;';
						}else{
							$class .= 'my_month_1 ';
							$color  = 'color:#666666;';
						}

						$link = '<a href="#" onclick="set_month('.$i.');">'.$i.'월</a>';

						if ($i == 12){
							$style = 'float:left;';
						}else{
							$style = 'float:left; margin-right:2px;';
						}?>
						<div class="<?=$class;?>" style="<?=$style;?>"><?=$link;?></div><?
					}?>
					</td>
					<td class="right last">
						<span class="btn_pack m icon" <?=$display;?>><span class="excel" <?=$display;?>></span><button type="button" onFocus="this.blur();" onClick="excel();">엑셀</button></span><?
						if($mode == '1'){ ?>
							<span class="btn_pack m icon" <?=$display;?>><span class="pdf" <?=$display;?>></span><button type="button" onFocus="this.blur();" onClick="pdf();">PDF</button></span><?
						}?>
						<span class="btn_pack m"><button type="button" onFocus="this.blur();" onclick="self.close();">닫기</button></span>
					</td>
				</tr>
			</tbody>
		</table><?
	}else {
		$sql = "select m00_cname
				  from m00center
				 where m00_mcode = '$code'";
		$c_name = $conn -> get_data($sql);

		$r_dt = date('Y.m.d',mktime());

		?>
		<div align="center" style="font-size:15pt; font-weight:bold;"><?=$year?>년<?=$month?>월 근무현황</div>
		<div width="1850px;">
			<table>
				<tr>
					<td colspan="19" style="text-align:left; font-size:12pt; font-weight:bold;">센터명 : <?=$c_name?></td>
					<td colspan="19" style="text-align:right; font-size:12pt; font-weight:bold;">일자 : <?=$r_dt?></td>
				</tr>
			</table>
		</div>
		<?
	}

	$colgrp = '<col width="40px">';


	if ($mode == 1){
		$colgrp .= '<col width="70px"><col width="70px">';
		$colgrp .= '<col width="100px">';
	}else{
		$colgrp .= '<col width="90px">';
		$colgrp .= '<col width="100px">';
		$colgrp .= '<col width="110px">';
	}

	if ($mode == 1){
		$colgrp .= '<col width="50px"><col width="40px"><col width="90px">';
		$colgrp .= '<col width="60px">
					<col width="60px">
					<col width="60px">
					<col width="35px" span="31">
					<col>';
	}

	if ($mode == 2){
		$colgrp .= '<col width="60px">
					<col width="60px">
					<col width="35px" span="31">
					<col>';
	}

?>

<div id="tbl" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100%;">
	<table <?=($wrt_mode == 1 ? 'id=\'sub_tbl_1\' class=\'my_table\' style=\'width:1850px;\'' : 'border=\'1\'');?>>
		<colgroup><?=$colgrp;?></colgroup>
		<thead>
			<tr>
			<th class="head">No</th>
			<th class="head">요양사</th>
			<?
				if($mode != 1){?>
					<th class="head">주민번호</th><?
				}
				if ($mode == 1){?>
					<th class="head">수급자</th><?
				}
			?>
			<th class="head">서비스</th>
			<?
				if ($mode == 1){?>
					<th class="head">구분</th>
					<th class="head"></th>
					<th class="head">시간</th><?
				}
			?>
			<?
			if($mode == 1){ ?>
				<th class="head">일수</th><?
			} ?>
			<th class="head">횟수</th>
			<th class="head">총시간</th>
			<?
				for($i=1; $i<=31; $i++){
					echo '<th class=\'head\' style=\'width:35px;\' >'.$i.'</th>';
				}
			?>
			<th class="head">비고</th>
		</tr>
		</thead>
<?
if($wrt_mode == 1){
?>
	</table>
</div>
<div id="div" style="overflow-x:scroll; overflow-y:scroll; width:100%; height:100px;">
	<table id="sub_tbl_2" class="my_table" style="width:1850px;">
		<colgroup><?=$colgrp;?></colgroup><?
}?>
		<tbody>
		<?
			$sql = "select t01_mkind as kind
					,      t01_svc_subcode as svc_cd
					,      t01_yoyangsa_id1 as mem_cd1
					,      t01_yname1 as mem_nm1
					,      t01_yname2 as mem_nm2
					,      t01_jumin as client_cd
					,      cast(date_format(t01_sugup_date, '%d') as unsigned) as dt
					,      TIMEDIFF(DATE_FORMAT(CONCAT(CASE WHEN t01_sugup_totime > t01_sugup_fmtime THEN t01_sugup_date ELSE REPLACE(ADDDATE(DATE_FORMAT(t01_sugup_date,'%Y-%m-%d'),interval 1 day),'-','') END, t01_sugup_totime,'00'),'%Y-%m-%d %H:%i:%s')
								   ,DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_fmtime,'00'),'%Y-%m-%d %H:%i:%s')) as plan_time
					,      t01_sugup_fmtime as plan_from_time
					,      t01_sugup_totime as plan_to_time
					,      TIMEDIFF(DATE_FORMAT(CONCAT(CASE WHEN t01_conf_totime > t01_conf_fmtime THEN t01_sugup_date ELSE REPLACE(ADDDATE(DATE_FORMAT(t01_sugup_date,'%Y-%m-%d'),interval 1 day),'-','') END, t01_conf_totime,'00'),'%Y-%m-%d %H:%i:%s')
								   ,DATE_FORMAT(CONCAT(t01_sugup_date, t01_conf_fmtime,'00'),'%Y-%m-%d %H:%i:%s')) as conf_time
					,	   CASE WHEN t01_mkind = '0' AND t01_svc_subcode = '200' AND t01_bipay_umu != 'Y' THEN
									 t01_conf_soyotime - CASE WHEN t01_conf_soyotime >= 270 THEN 30 ELSE 0 END
								ELSE t01_conf_soyotime END as conf_soyotime
					,      t01_conf_fmtime as conf_from_time
					,      t01_conf_totime as conf_to_time
					,      t01_status_gbn as stat
					,      'm' as ms_gbn
					,      case when t01_bipay_umu = 'Y' then 'Y' else 'N' end as bipay_yn
					  from t01iljung
					 where t01_ccode  = '$code'
					   and t01_del_yn = 'N'
					   and t01_yoyangsa_id1 != ''
					   and left(t01_sugup_date,6) = '$year$month'";

			if ($svcCode != 'ALL'){
				$sql .= " AND t01_mkind = '".$svcCode."'";
			}

			$sql .= " union all
					select t01_mkind as kind
					,      t01_svc_subcode as svc_cd
					,      t01_yoyangsa_id2 as mem_cd1
					,      t01_yname2 as mem_nm1
					,      t01_yname1 as mem_nm2
					,      t01_jumin as client_cd
					,      cast(date_format(t01_sugup_date, '%d') as unsigned) as dt
					,      TIMEDIFF(DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_totime,'00'),'%Y-%m-%d %H:%i:%s')
								   ,DATE_FORMAT(CONCAT(t01_sugup_date, t01_sugup_fmtime,'00'),'%Y-%m-%d %H:%i:%s')) as plan_time
					,      t01_sugup_fmtime as plan_from_time
					,      t01_sugup_totime as plan_to_time
					,      TIMEDIFF(DATE_FORMAT(CONCAT(t01_sugup_date, t01_conf_totime,'00'),'%Y-%m-%d %H:%i:%s')
								   ,DATE_FORMAT(CONCAT(t01_sugup_date, t01_conf_fmtime,'00'),'%Y-%m-%d %H:%i:%s')) as conf_time
					,	   CASE WHEN t01_mkind = '0' AND t01_svc_subcode = '200' AND t01_bipay_umu != 'Y' THEN
									 t01_conf_soyotime - CASE WHEN t01_conf_soyotime >= 270 THEN 30 ELSE 0 END
								ELSE t01_conf_soyotime END as conf_soyotime
					,      t01_conf_fmtime as conf_from_time
					,      t01_conf_totime as conf_to_time
					,      t01_status_gbn as stat
					,      's' as ms_gbn
					,      case when t01_bipay_umu = 'Y' then 'Y' else 'N' end as bipay_yn
					  from t01iljung
					 where t01_ccode         = '$code'
					   and t01_del_yn        = 'N'
					   and t01_yoyangsa_id2 != ''
					   and t01_svc_subcode = '500'
					   and left(t01_sugup_date,6) = '$year$month'";

			if ($svcCode != 'ALL'){
				$sql .= " AND t01_mkind = '".$svcCode."'";
			}

			if($ssn != ''){
				$sql = "SELECT kind
						,      svc_cd
						,      mem_cd1
						,      mem_nm1
						,      mem_nm2
						,      client_cd
						,      client_nm
						,      dt
						,      CASE WHEN kind = '0' AND svc_cd = '200' AND bipay_yn != 'Y' THEN
									plan_time - CASE WHEN plan_time >= 540 THEN 60
													 WHEN plan_time >= 270 THEN 30 ELSE 0 END
									ELSE plan_time END AS plan_time
						,      plan_from_time
						,      plan_to_time
						,      CASE WHEN kind = '0' AND svc_cd = '200' AND bipay_yn != 'Y' THEN
										 conf_time - CASE WHEN conf_time >= 540 THEN 60
														  WHEN conf_time >= 270 THEN 30 ELSE 0 END
									ELSE conf_time END AS conf_time
						,	   CASE WHEN kind = '0' THEN conf_soyotime ELSE conf_time END AS conf_soyotime
						,      conf_from_time
						,      conf_to_time
						,      stat
						,      ms_gbn
						,      bipay_yn
						  FROM (
							   SELECT kind
							   ,      svc_cd
							   ,      mem_cd1
							   ,      mem_nm1
							   ,      mem_nm2
							   ,      client_cd
							   ,      m03_name AS client_nm
							   ,      dt
							   ,      HOUR(plan_time) * 60 + MINUTE(plan_time) as plan_time
							   ,      plan_from_time
							   ,      plan_to_time
							   ,      CASE WHEN kind = '0' AND svc_cd = '200' THEN HOUR(conf_time) * 60 + MINUTE(conf_time) - ((HOUR(conf_time) * 60 + MINUTE(conf_time)) % 30) ELSE HOUR(conf_time) * 60 + MINUTE(conf_time) END as conf_time
							   ,	  conf_soyotime
							   ,      conf_from_time
							   ,      conf_to_time
							   ,      stat
							   ,      ms_gbn
							   ,      bipay_yn
								 FROM (".$sql.") as t
								INNER JOIN m03sugupja
								   ON m03_ccode = '$code'
								  AND m03_mkind = kind
								  AND m03_jumin = client_cd
								WHERE mem_cd1 = '".$ssn."'
							   ) AS t";

			}else {
				$sql = "SELECT kind
						,      svc_cd
						,      mem_cd1
						,      mem_nm1
						,      mem_nm2
						,      client_cd
						,      client_nm
						,      dt

						,      CASE WHEN kind = '0' AND svc_cd = '200' AND bipay_yn != 'Y' THEN
									plan_time - CASE WHEN '$year$month' >= '201603' THEN
														CASE WHEN plan_time >= 480 THEN 0
															 WHEN plan_time >= 270 THEN 30 ELSE 0 END
													 ELSE
														CASE WHEN plan_time >= 540 THEN 60
															 WHEN plan_time >= 270 THEN 30 ELSE 0 END
													 END
												/*CASE WHEN plan_time >= 540 THEN 60 WHEN plan_time >= 270 THEN 30 ELSE 0 END*/
									ELSE plan_time END AS plan_time
						,      plan_from_time
						,      plan_to_time
						,      CASE WHEN kind = '0' AND svc_cd = '200' AND bipay_yn != 'Y' THEN
										 conf_time - CASE WHEN '$year$month' >= '201603' THEN
														CASE WHEN conf_time >= 480 THEN 0
															 WHEN conf_time >= 270 THEN 30 ELSE 0 END
													 ELSE
														CASE WHEN conf_time >= 540 THEN 60
															 WHEN conf_time >= 270 THEN 30 ELSE 0 END
													 END
													 /*CASE WHEN conf_time >= 540 THEN 60 WHEN conf_time >= 270 THEN 30 ELSE 0 END*/
									ELSE conf_time END AS conf_time
						,	   CASE WHEN kind = '0' THEN conf_soyotime ELSE conf_time END AS conf_soyotime
						,	   conf_soyotime as soyotime
						,      conf_from_time
						,      conf_to_time
						,      stat
						,      ms_gbn
						,      bipay_yn
						  FROM (
							   SELECT kind
							   ,      svc_cd
							   ,      mem_cd1
							   ,      mem_nm1
							   ,      mem_nm2
							   ,      client_cd
							   ,      m03_name AS client_nm
							   ,      dt
							   ,      HOUR(plan_time) * 60 + MINUTE(plan_time) as plan_time
							   ,      plan_from_time
							   ,      plan_to_time
							   ,      CASE WHEN kind = '0' AND svc_cd = '200' THEN HOUR(conf_time) * 60 + MINUTE(conf_time) - ((HOUR(conf_time) * 60 + MINUTE(conf_time)) % 30) ELSE HOUR(conf_time) * 60 + MINUTE(conf_time) END as conf_time
							   ,	  conf_soyotime
							   ,      conf_from_time
							   ,      conf_to_time
							   ,      stat
							   ,      ms_gbn
							   ,      bipay_yn
								 FROM (".$sql.") as t
								INNER JOIN m03sugupja
								   ON m03_ccode = '$code'
								  AND m03_mkind = kind
								  AND m03_jumin = client_cd
								WHERE mem_cd1 != ''
							   ) AS t";
			}


			if ($mode == 1){
				$sql .= " order by mem_nm1, mem_cd1, kind,svc_cd,client_nm, soyotime, dt";
			}else{
				$sql .= " order by mem_nm1, mem_cd1, kind, svc_cd, case when bipay_yn != 'Y' then 1 else 2 end, dt, plan_from_time";
			}

			/*
			if($debug){
				echo nl2br($sql);
				exit;
			}
			*/

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$strDt = $year.$month.(IntVal($row['dt']) < 10 ? '0' : '').IntVal($row['dt']);

				$new_m = false;
				$new_c = false;
				$new_w = false;

				if(empty($row['plan_time'])){
					$plan_fromTime = mktime(substr($row['plan_from_time'],0,2),substr($row['plan_from_time'],2,2),00,$month,$year,$row['dt']);
					$plan_toTime =mktime(substr($row['plan_to_time'],0,2),substr($row['plan_to_time'],2,2),00,$month,$year,$row['dt']);

					$row['plan_time'] = (($plan_toTime-$plan_fromTime)/60);
				}

				if(empty($row['conf_time'])){
					@$conf_fromTime = mktime(substr($row['conf_from_time'],0,2),substr($row['conf_from_time'],2,2),00,$month,$year,$row['dt']);
					@$conf_toTime = mktime(substr($row['conf_to_time'],0,2),substr($row['conf_to_time'],2,2),00,$month,$year,$row['dt']);

					$row['conf_time']	  = (($conf_toTime-$conf_fromTime)/60);
					$row['conf_soyotime'] = (($conf_toTime-$conf_fromTime)/60);


				}

				if ($row['kind'] == '0'){
					if ($row['svc_cd'] == '200'){
						$row['conf_soyotime']	= $myF->cutOff($row['conf_soyotime'],30);
					}else if ($row['svc_cd'] == '500'){
						if ($row['conf_soyotime'] >= 60){
							$row['conf_soyotime']	= 60;
						}else if ($row['conf_soyotime'] >= 40){
							$row['conf_soyotime']	= 40;
						}
					}
				}else if ($row['kind'] == '4'){
					if($row['conf_soyotime'] > 480){
						$row['conf_soyotime'] = 480;
					}
				}
				
				$soyoTime = $mst[$m][$c]['iljung']['conf'][$w]['soyotime'] != '' ? $mst[$m][$c]['iljung']['conf'][$w]['soyotime'] : $mst[$m][$c]['iljung']['plan'][$w]['soyotime'];

				if (!isset($m)){
					$m = 0;
					$c = 0;
					$w = 0;
					$new_m = true;
					$new_c = true;
					$new_w = true;
				}else{
					if ($mst['member'][$m]['cd'] != $row['mem_cd1']){
						$m ++;
						$c = 0;
						$w = 0;
						$new_m = true;
						$new_c = true;
						$new_w = true;
					}else if ($mode == 1 && $mst[$m]['client'][$c]['cd'] != $row['client_cd']){
						$c ++;
						$w = 0;
						$new_c = true;
						$new_w = true;
					}else if (($mode == 1 && $mst[$m][$c]['iljung']['plan'][$w]['from_time'] != $row['plan_from_time']) ||
							  ($mode == 1 && $mst[$m][$c]['iljung']['plan'][$w]['to_time'] != $row['plan_to_time']) ||
							  ($mode == 1 && $mst[$m][$c]['iljung']['svc_cd'][$w] != $row['svc_cd'])){
						$w ++;
						$new_w = true;
					}else if (($mode == 1 && $mst[$m][$c]['iljung']['conf'][$w]['soyotime'] != $row['soyotime']) ||
							  ($mode == 1 && $mst[$m][$c]['iljung']['svc_cd'][$w] != $row['svc_cd'])){
						$w ++;
						$new_w = true;
					}else if (($mode == 2 && $mst[$m][$c]['iljung']['kind_cd'][$w] != $row['kind']) ||
							  ($mode == 2 && $mst[$m][$c]['iljung']['svc_cd'][$w] != $row['svc_cd']) ||
							  ($mode == 2 && $mst[$m][$c]['iljung']['bipay'][$w] != $row['bipay_yn']) ){
						$w ++;
						$new_w = true;
					}
				}

				if ($mode != 1) $new_c = false;

				if ($new_m){
					$mst['member'][$m] = array('cd'=>$row['mem_cd1'], 'nm'=>$row['mem_nm1'], 'row'=>0);
				}

				if ($new_c){
					$mst[$m]['client'][$c] = array('cd'=>$row['client_cd'], 'nm'=>$row['client_nm'], 'row'=>0);
				}

				if ($new_w){
					$mst[$m][$c]['iljung']['kind_cd'][$w] = $row['kind'];
					$mst[$m][$c]['iljung']['svc_cd'][$w]  = $row['svc_cd'];
					$mst[$m][$c]['iljung']['bipay'][$w]   = $row['bipay_yn'];

					if($wrt_mode == 1){
						if ($row['kind'] == '0'){
							$mst[$m][$c]['iljung']['kind'][$w] = $conn->kind_name_sub($conn->kind_name_svc($row['kind'])).'['.$conn->kind_name_sub($conn->kind_name_svc($row['svc_cd'])).']';
						}else if ($row['kind'] >= '1' && $row['kind'] <= '4'){
							if ($row['kind'] == '4'){
								$mst[$m][$c]['iljung']['kind'][$w] = '장애['.$conn->kind_name_sub($conn->kind_name_svc($row['svc_cd'])).']';
							}else{
								$mst[$m][$c]['iljung']['kind'][$w] = '바우처['.$conn->kind_name_sub($conn->kind_name_svc($row['kind'])).']';
							}
						}else{
							$mst[$m][$c]['iljung']['kind'][$w] = '기타['.$conn->kind_name_sub($conn->kind_name_svc($row['kind'])).']';
						}

						if ($mst[$m][$c]['iljung']['bipay'][$w] == 'Y')
							$mst[$m][$c]['iljung']['kind'][$w] .= '<span style=\'color:#ff0000;\'>[비]</span>';
					}else {
						if ($row['kind'] == '0'){
							$mst[$m][$c]['iljung']['kind'][$w] = $conn->kind_name_sub($conn->kind_name_svc($row['kind'])).'['.$conn->kind_name_sub($conn->kind_name_svc($row['svc_cd'])).']';
						}else if ($row['kind'] >= '1' && $row['kind'] <= '4'){
							if ($row['kind'] == '4'){
								$mst[$m][$c]['iljung']['kind'][$w] = '장애['.$conn->kind_name_sub($conn->kind_name_svc($row['svc_cd'])).']';
							}else{
								$mst[$m][$c]['iljung']['kind'][$w] = '바우처['.$conn->kind_name_sub($conn->kind_name_svc($row['kind'])).']';
							}
						}else{
							$mst[$m][$c]['iljung']['kind'][$w] = '기타['.$conn->kind_name_sub($conn->kind_name_svc($row['kind'])).']';
						}

						if ($mst[$m][$c]['iljung']['bipay'][$w] == 'Y')
							$mst[$m][$c]['iljung']['kind'][$w] .= '<span style=\'color:#ff0000;\'>[비]</span>';
					}


					/*********************************************************
						2명이 서비스한 경우 정/부를 표시한다.
					*********************************************************/
					if ($mode == 1 && !empty($row['mem_nm2'])){
						$mst[$m][$c]['iljung']['kind'][$w] .= '<br>'.($row['ms_gbn'] == 'm' ? '부' : '정').' : '.$row['mem_nm2'];
					}

					$mst[$m][$c]['iljung']['plan'][$w] = array('proc_time'=>$row['plan_time'], 'from_time'=>$row['plan_from_time'], 'to_time'=>$row['plan_to_time'], 'soyotime'=>$row['soyotime'], 'work_dt'=>'', 'work_cnt'=>0, 'work_time'=>0);
					$mst[$m][$c]['iljung']['conf'][$w] = array('proc_time'=>$row['conf_time'], 'from_time'=>$row['conf_from_time'], 'to_time'=>$row['conf_to_time'], 'soyotime'=>$row['soyotime'], 'work_dt'=>'', 'work_cnt'=>0, 'work_time'=>0);

					$mst['member'][$m]['row']     += ($mode == 1 ? 2 : 1);
					$mst[$m]['client'][$c]['row'] += ($mode == 1 ? 2 : 1);

					for($j=1; $j<=31; $j++){
						$mst[$m][$c]['iljung']['plan'][$w][$j] = 0;
						$mst[$m][$c]['iljung']['conf'][$w][$j] = 0;
					}
				}

				if($row['kind'] == 0){
					$mst[$m][$c]['iljung']['plan'][$w][$row['dt']] = $row['plan_time'];
				}else {
					if (($row['kind'] == 1 && $strDt >= '20140201') || ($row['kind'] == 2 && $strDt >= '20150201')){
						$tmpTime = $row['plan_time'] % 60;

						if ($tmpTime >= 45){
							$planTime = $myF->cutOff($row['plan_time'],60)+60;
						}else if ($tmpTime >= 15 && $tmpTime < 45){
							$planTime = $myF->cutOff($row['plan_time'],60)+30;
						}else{
							$planTime = $myF->cutOff($row['plan_time'],60);
						}
					}else if(($row['kind'] == 4 && $strDt >= '20170101')){
						$tmpTime = $row['plan_time'] % 60;

						if ($tmpTime >= 45){
							$planTime = $myF->cutOff($row['plan_time'],60)+60;
						}else if ($tmpTime >= 15 && $tmpTime < 45){
							$planTime = $myF->cutOff($row['plan_time'],60)+30;
						}else{
							$planTime = $myF->cutOff($row['plan_time'],60);
						}
					}else{
						$planTime = round(round($row['plan_time'] / 60));
						$planTime = ($planTime*60);
					}

					$mst[$m][$c]['iljung']['plan'][$w][$row['dt']] = $planTime;
				}

				if ($row['kind'] == 0){
					if ($mode == 1)
						$mst[$m][$c]['iljung']['conf'][$w][$row['dt']] = $row['conf_time'];
					else
						$mst[$m][$c]['iljung']['conf'][$w][$row['dt']] += $row['conf_time'];
				}else{
					if (($row['kind'] == 1 && $strDt >= '20140201') || ($row['kind'] == 2 && $strDt >= '20150201')){
						$tmpTime = $row['conf_soyotime'] % 60;

						if ($tmpTime >= 45){
							$confTime = $myF->cutOff($row['conf_soyotime'],60)+60;
						}else if ($tmpTime >= 15 && $tmpTime < 45){
							$confTime = $myF->cutOff($row['conf_soyotime'],60)+30;
						}else{
							$confTime = $myF->cutOff($row['conf_soyotime'],60);
						}
					}else if(($row['kind'] == 4 && $strDt >= '20170101')){
						$tmpTime = $row['conf_soyotime'] % 60;

						if ($tmpTime >= 45){
							$confTime = $myF->cutOff($row['conf_soyotime'],60)+60;
						}else if ($tmpTime >= 15 && $tmpTime < 45){
							$confTime = $myF->cutOff($row['conf_soyotime'],60)+30;
						}else{
							$confTime = $myF->cutOff($row['conf_soyotime'],60);
						}
					}else{
						$confTime = round(round($row['conf_soyotime'] / 60));
						$confTime = ($confTime*60);
					}

					if ($mode == 1){
						$mst[$m][$c]['iljung']['conf'][$w][$row['dt']] = $confTime;
					}else{
						$mst[$m][$c]['iljung']['conf'][$w][$row['dt']] += $confTime;
					}
				}

				//if ($debug && $i >= 2098 && $i <= 2114 /*$row['client_cd'] == '6808082A00001'*/){
				//	$tmpstr .= $i.'/'.$row['dt'].'/'.$row['client_cd'].'/'.$confTime.'/'.$mst[$m][$c]['iljung']['conf'][$w][$row['dt']].'<br>';
				//}

				if (!is_numeric(strpos($mst[$m][$c]['iljung']['plan'][$w]['work_dt'], '/'.$row['dt']))){
					$mst[$m][$c]['iljung']['plan'][$w]['work_cnt'] ++;
					$mst[$m][$c]['iljung']['plan'][$w]['work_dt'] .= '/'.$row['dt'];
				}

				if ($row['kind'] == 0){
					$mst[$m][$c]['iljung']['plan'][$w]['work_time'] += $row['plan_time'];
				}else{
					if (($row['kind'] == 1 && $strDt >= '20140201') || ($row['kind'] == 2 && $strDt >= '20150201')){
						//$planTime = $myF->cutOff($row['plan_time'],30);
						$tmpTime = $row['plan_time'] % 60;

						if ($tmpTime >= 45){
							$planTime = $myF->cutOff($row['plan_time'],60)+60;
						}else if ($tmpTime >= 15 && $tmpTime < 45){
							$planTime = $myF->cutOff($row['plan_time'],60)+30;
						}else{
							$planTime = $myF->cutOff($row['plan_time'],60);
						}
					}else if(($row['kind'] == 4 && $strDt >= '20170101')){
						$tmpTime = $row['plan_time'] % 60;

						if ($tmpTime >= 45){
							$planTime = $myF->cutOff($row['plan_time'],60)+60;
						}else if ($tmpTime >= 15 && $tmpTime < 45){
							$planTime = $myF->cutOff($row['plan_time'],60)+30;
						}else{
							$planTime = $myF->cutOff($row['plan_time'],60);
						}
					}else{
						$planTime = round(round($row['plan_time'] / 60));
						$planTime = ($planTime*60);
					}

					$mst[$m][$c]['iljung']['plan'][$w]['work_time'] += $planTime;
				}

				if ($row['stat'] == '1'){
					if (!is_numeric(strpos($mst[$m][$c]['iljung']['conf'][$w]['work_dt'], '/'.$row['dt']))){
						$mst[$m][$c]['iljung']['conf'][$w]['work_cnt'] ++;
						$mst[$m][$c]['iljung']['conf'][$w]['work_dt'] .= '/'.$row['dt'];
					}


					if ($row['kind'] == 0){
						$mst[$m][$c]['iljung']['conf'][$w]['work_time'] += $row['conf_time'];
					}else{
						if ($year.$month >= '201301'){
							if (($row['kind'] == 1 && $strDt >= '20140201') || ($row['kind'] == 2 && $strDt >= '20150201')){
								//$confTime = $myF->cutOff($row['conf_soyotime'],30);
								$tmpTime = $row['conf_soyotime'] % 60;

								if ($tmpTime >= 45){
									$confTime = $myF->cutOff($row['conf_soyotime'],60)+60;
								}else if ($tmpTime >= 15 && $tmpTime < 45){
									$confTime = $myF->cutOff($row['conf_soyotime'],60)+30;
								}else{
									$confTime = $myF->cutOff($row['conf_soyotime'],60);
								}
							}else if(($row['kind'] == 4 && $strDt >= '20170101')){
								$tmpTime = $row['conf_soyotime'] % 60;

								if ($tmpTime >= 45){
									$confTime = $myF->cutOff($row['conf_soyotime'],60)+60;
								}else if ($tmpTime >= 15 && $tmpTime < 45){
									$confTime = $myF->cutOff($row['conf_soyotime'],60)+30;
								}else{
									$confTime = $myF->cutOff($row['conf_soyotime'],60);
								}
								//echo $row['dt'].'/'.$row['client_nm'].'/'.$tmpTime.'//';
							}else{
								$confTime = round(round($row['conf_soyotime'] / 60));
								$confTime = ($confTime*60);
							}
							$mst[$m][$c]['iljung']['conf'][$w]['work_time'] += $confTime;
						}else{
							$confTime = round(round($row['conf_time'] / 60));
							$confTime = ($confTime*60);
							$mst[$m][$c]['iljung']['conf'][$w]['work_time'] += $confTime;
						}
					}
				}

			}

			$conn->row_free();

			$cnt_m   = sizeof($mst['member']);

			for($m=0; $m<$cnt_m; $m++){
				$cnt_c = sizeof($mst[$m]['client']);
				$row_m = '<td rowspan=\''.$mst['member'][$m]['row'].'\'><div class=\'center\'>'.($m+1).'</div></td>';

				if($mode == 1){
					$row_m .= '<td rowspan=\''.$mst['member'][$m]['row'].'\'><div class=\'left\'>'.($mst['member'][$m]['nm'].('<br>').substr($myF->issStyle($mst['member'][$m]['cd']),0,8)).('').'</div></td>';
				}else {
					$row_m .= '<td rowspan=\''.$mst['member'][$m]['row'].'\'><div class=\'center\'>'.($mst['member'][$m]['nm']).'</div></td>';
					$row_m .= '<td rowspan=\''.$mst['member'][$m]['row'].'\'><div class=\'center\' style="text-align:center;">'.(substr($myF->issStyle($mst['member'][$m]['cd']),0,8)).('').'</div></td>';
				}
				for($c=0; $c<$cnt_c; $c++){
					$cnt_w = sizeof($mst[$m][$c]['iljung']['plan']);

					if ($mode == 1)
						$row_c = '<td rowspan=\''.$mst[$m]['client'][$c]['row'].'\'><div class=\'left\'>'.($mst[$m]['client'][$c]['nm']).'</div></td>';
					else
						$row_c = '';

					for($w=0; $w<$cnt_w; $w++){
						#######################################
						#
						# 계획
						#
						#######################################

						if ($mode == 1){
							echo '<tr>';

							if (!empty($row_m)){
								echo $row_m;
								unset($row_m);
							}

							if (!empty($row_c)){
								echo $row_c;
								unset($row_c);
							}

							echo make_td($mode, $mst[$m][$c]['iljung']['kind'][$w], '계획', $mst[$m][$c]['iljung']['plan'][$w]['proc_time'], $mst[$m][$c]['iljung']['plan'][$w]['from_time'], $mst[$m][$c]['iljung']['plan'][$w]['to_time'], $mst[$m][$c]['iljung']['plan'][$w]['work_cnt'], '', $mst[$m][$c]['iljung']['plan'][$w]['work_time'], '');
							echo make_day_td($mst[$m][$c]['iljung']['plan'][$w], '', $debug);
							echo '</tr>';
						}

						#######################################
						#
						# 실적
						#
						#######################################
						echo '<tr>';

						if (!empty($row_m)){
							echo $row_m;
							unset($row_m);
						}

						if (!empty($row_c)){
							echo $row_c;
							unset($row_c);
						}

						echo make_td($mode, ($mode == 1 ? '' : $mst[$m][$c]['iljung']['kind'][$w]), '실적', $mst[$m][$c]['iljung']['conf'][$w]['proc_time'], $mst[$m][$c]['iljung']['conf'][$w]['from_time'], $mst[$m][$c]['iljung']['conf'][$w]['to_time'], $mst[$m][$c]['iljung']['conf'][$w]['work_cnt'],'', $mst[$m][$c]['iljung']['conf'][$w]['work_time'], '');
						echo make_day_td($mst[$m][$c]['iljung']['conf'][$w], '', $debug);
						echo '</tr>';

						/***********************************************
						 소계 데이타
						***********************************************/
						if ($mode == 1){
							//if ($debug){
								make_subsum($subsum['plan'], $mst[$m][$c]['iljung']['plan'][$w]['work_dt'], $mst[$m][$c]['iljung']['plan'][$w]['work_time'], $mst[$m][$c]['iljung']['plan'][$w]);
								make_subsum($subsum['conf'], $mst[$m][$c]['iljung']['conf'][$w]['work_dt'], $mst[$m][$c]['iljung']['conf'][$w]['work_time'], $mst[$m][$c]['iljung']['conf'][$w]);
							//}else{
							//	make_subsum($subsum['plan'], $mst[$m][$c]['iljung']['plan'][$w]['work_cnt'], $mst[$m][$c]['iljung']['plan'][$w]['work_time'], $mst[$m][$c]['iljung']['plan'][$w]);
							//	make_subsum($subsum['conf'], $mst[$m][$c]['iljung']['conf'][$w]['work_cnt'], $mst[$m][$c]['iljung']['conf'][$w]['work_time'], $mst[$m][$c]['iljung']['conf'][$w]);
							//}
						}
					}
				}

				/***********************************************
				 소계 계획
				***********************************************/
				if ($mode == 1){
					for($i=1; $i<=31; $i++){
						if(!empty($subsum['plan']['iljung'][$i])){
							$subsum['plan']['work_day_cnt'] ++;
						}

						if(!empty($subsum['conf']['iljung'][$i])){
							$subsum['conf']['work_day_cnt'] ++;
						}
					}

					echo '<tr>';
					echo '<td class=\'center border_g\' colspan=\'4\' rowspan=\'2\'><div class=\'right bold\'>소계</div></td>';
					echo make_td($mode, '', '계획', '', '', '', $subsum['plan']['work_cnt'], $subsum['plan']['work_day_cnt'], $subsum['plan']['work_time'], 'border_g bold');
					echo make_day_td($subsum['plan']['iljung'], 'border_g bold', $debug);
					echo '</tr>';

					/***********************************************
					 소계 실적
					***********************************************/
					echo '<tr>';

					if ($mode != 1)
						echo '<td class=\'center border_g\' colspan=\'3\'><div class=\'right bold\'>소계</div></td>';

					echo make_td($mode, '', '실적', '', '', '', $subsum['conf']['work_cnt'], $subsum['conf']['work_day_cnt'], $subsum['conf']['work_time'], 'border_g bold');
					echo make_day_td($subsum['conf']['iljung'], 'border_g bold', $debug);
					echo '</tr>';

					unset($subsum);
				}
			}

			//if ($debug) echo '<tr><td colspan="10">'.$tmpstr.'</td></tr>';
		?>
		</tbody>
	</table>
</div>
<?
	if ($wrt_mode == 1){?>
		<input name="code" type="hidden" value="<?=$code;?>">
		<input name="month" type="hidden" value="<?=$month;?>">
		<input name="wrt_mode" type="hidden" value="<?=$wrt_mode;?>">
		<input name="mode" type="hidden" value="<?=$mode;?>">
		<input name="ssn" type="hidden" value="<?=$ed->en($ssn);?>">

		</form><?

		include_once("../inc/_footer.php");
	}else{
		include_once("../inc/_db_close.php");
	}

	function make_td($mode, $svc_nm, $gbn, $proc_time, $from_time, $to_time, $work_cnt, $work_day_cnt, $work_time, $class){
		if (!empty($proc_time)) $proc_time = number_format($proc_time / 60, 1); else $proc_time = '';
		if (!empty($work_time)) $work_time = number_format($work_time / 60, 1); else $work_time = '';
		if (!empty($from_time)) $from_time = $from_time = substr($from_time, 0, 2).':'.substr($from_time, 2); else $from_time = '';
		if (!empty($to_time))   $to_time   = substr($to_time, 0, 2).':'.substr($to_time, 2); else $to_time = '';
		if (!empty($from_time)) $from_to_time = $from_time.' ~ '.$to_time; else $from_to_time = '';
		if (empty($work_cnt))   $work_cnt = '';
		if (empty($work_day_cnt))   $work_day_cnt = '';

		if (!empty($svc_nm)) $rowspan = 'rowspan=\''.($mode == 1 ? 2 : 1).'\'';

		$html  = '';

		if (!empty($rowspan)) $html .= '<td class=\'center\' '.$rowspan.'><div class=\'left\'>'.$svc_nm.'</div></td>';

		if ($mode == 1){
			$html .= '<td class=\'center '.$class.'\' style=\'text-align:center;\'><div class=\'center\'>'.$gbn.'</div></td>';
			$html .= '<td class=\'center '.$class.'\' style=\'text-align:center;\'><div class=\'center\' style="mso-number-format:\@;">'.$proc_time.'</div></td>';
			$html .= '<td class=\'center '.$class.'\' style=\'text-align:center;\'><div class=\'center\'>'.$from_to_time.'</div></td>';
		}
		if($mode==1) $html .= '<td class=\'center '.$class.'\' style=\'text-align:center;\'><div>'.$work_day_cnt.'</div></td>';
		$html .= '<td class=\'center '.$class.'\' style=\'text-align:center;\'><div>'.$work_cnt.'</div></td>';
		$html .= '<td class=\'center '.$class.'\' style=\'text-align:center;\'><div style="mso-number-format:\@;">'.$work_time.'</div></td>';

		return $html;
	}

	function make_day_td($iljung, $class, $debug){
		$html = '';

		for($i=1; $i<=31; $i++){
			$time  = $iljung[$i];
			$time  = !empty($time) ? number_format(round($time / 60, 1), 1) : '';
			$html .= '<td class=\'center '.$class.'\'><div class=\'center\' style=\'text-align:center; mso-number-format:\@;\'>'.$time.'</div></td>';
		}

		$html .= '<td class=\'center '.$class.'\'><div class=\'center\'></div></td>';

		return $html;
	}

	function make_subsum(&$subsum, $work_dt, $work_time, $iljung){
		if (!is_numeric($work_dt)){
			$arrDT = explode('/',$work_dt);

			foreach($arrDT as $i => $dt){
				if (!empty($dt)){

					$subsum['work_dt'] .= '/'.$dt;
					$subsum['work_cnt'] ++;

				}
			}

			$subsum['work_time'] += $work_time;
		}else{
			$subsum['work_cnt']  += $work_cnt;
			$subsum['work_time'] += $work_time;
		}

		for($i=1; $i<=31; $i++){
			$subsum['iljung'][$i] += $iljung[$i];
		}

	}
?>
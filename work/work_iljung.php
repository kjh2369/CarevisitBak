<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	/************************************

		type 설정

		s : 수급자
		y : 요양보호사
		c : 욕구상담
		service : 서비스


	************************************/

	// 페이지 리스트를 위한 설정
	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	// 년도범위
	$init_year = $myF->year();

	$code	   = $_SESSION['userCenterCode'];
	$kind	   = $_SESSION["userCenterKind"][0];
	$year	   = $_POST['year'] != '' ? $_POST['year'] : date('Y', mktime());
	$month	   = $_POST['month'] != '' ? $_POST['month'] : date('m', mktime());
	$type	   = $_REQUEST['type'];
	$family	   = $_REQUEST['family'] != '' ? $_REQUEST['family'] : 'N';
	$find_kind = $_REQUEST['find_kind'];
	$cNm       = $_POST['cNm'];

	if (!isset($find_kind)) $find_kind = 'all';
	if (!isset($find_dept)) $find_dept = 'all';

	if ($_POST['svcCD_all'] != 'Y'){
		$svcList = $_POST['svcCD'];
	}

	if ($family == 'Y'){
		$family_sql = " and t01_toge_umu = 'Y'
						and t01_svc_subcode = '200'";

	}else if ($family == 'W'){
		$family_sql = " and t01_status_gbn = '1'";

	}else{
		$family_sql = "";
	}

	switch($type){
		case 's':
			$title = '일정표출력(수급자)';
			break;
		case 'y':
			$title = '일정표출력(요양보호사)';
			break;
		case 'c':
			$title = '일정표출력(욕구상담)';
			break;
		case 'service':
			$title = '일정표출력(서비스별)';
			break;
		default:
			include('../inc/_http_home.php');
	}
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function list(page){
	var f = document.f;

	f.page.value = page;
	f.submit();
}

-->
</script>

<form name="f" method="post">

<div class="title title_border"><?=$title;?></div>

<?
	if ($type == 'c'){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="40px">
				<?
					if ($type == 's' || $type == 'y'){?>
						<col width="197px"><?
					}else{?>
						<col width="80px"><?
					}
				?>
				<col width="50px">
				<col width="40px">
				<col>
				<col width="40px">
				<col width="115px">
			</colgroup>
			<tbody>
				<tr>
					<th class="center">년도</th>
					<td class="">
						<select name="year" style="width:auto;">
						<?
							for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
								<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option><?
							}
						?>
						</select>년
						<?
							if ($type == 's' || $type == 'y'){
								echo '<select name=\'family\' style=\'width:auto; margin:0;\'>
										<option value=\'N\' '.($family == 'N' ? 'selected' : '').'>전체수급자표시</option>
										<option value=\'Y\' '.($family == 'Y' ? 'selected' : '').'>동거케어만표시</option>
										<option value=\'W\' '.($family == 'W' ? 'selected' : '').'>실적만표시</option>
									  </select>';
							}else{
								echo '<input name=\'family\' type=\'hidden\' value=\'N\'>';
							}
						?>
					</td>
					<td class="center">
						<span class="btn_pack m"><button name="btnSearch" type="button" onFocus="this.blur();" onClick="list(<?=$page;?>);">조회</button></span>
					</td>
					<?
						if ($type == 's' || $type == 'y'){?>
							<th class="center">선택</th>
							<td class="left">
								<select name="printType" style="width:auto; margin:0;">
									<option value="pdf">PDF</option>
									<option value="html">HTML</option>
								</select>
								<select name="useType" style="width:auto; margin:0;">
									<option value="y">서비스금액표시</option>
									<option value="n">서비스금액미표시</option>
								</select>
								<select name="detail_yn" style="width:auto; margin:0;">
									<option value="y">달력표시</option>
									<option value="n">달력미표시</option>
								</select>
								<select name="page_pl" style="width:auto; margin:0;">
									<option value="p">세로</option>
									<option value="l">가로</option>
								</select>
								<select name="svcDtlYN" style="width:auto; margin:0;">
									<option value="N">기본</option>
									<option value="Y">상세</option>
								</select>
							</td><?
						}else{?>
							<input name="printType" type="hidden" value="pdf">
							<input name="useType" type="hidden" value="n">
							<input name="detail_yn" type="hidden" value="y">
							<input name="page_pl" type="hidden" value="p">
							<input name="svcDtlYN" type="hidden" value="N"><?
						}

						if ($type == 's' || $type == 'y' || $type == 'c'){?>
							<th class="center">월별</th>
							<td class="left last">
								<select name="print_month" style="width:auto; margin:0;">
								<?
									$sql = "select sum(case substring(t01_sugup_date, 5, 2) when '01' then 1 else 0 end) as mon01
											,      sum(case substring(t01_sugup_date, 5, 2) when '02' then 1 else 0 end) as mon02
											,      sum(case substring(t01_sugup_date, 5, 2) when '03' then 1 else 0 end) as mon03
											,      sum(case substring(t01_sugup_date, 5, 2) when '04' then 1 else 0 end) as mon04
											,      sum(case substring(t01_sugup_date, 5, 2) when '05' then 1 else 0 end) as mon05
											,      sum(case substring(t01_sugup_date, 5, 2) when '06' then 1 else 0 end) as mon06
											,      sum(case substring(t01_sugup_date, 5, 2) when '07' then 1 else 0 end) as mon07
											,      sum(case substring(t01_sugup_date, 5, 2) when '08' then 1 else 0 end) as mon08
											,      sum(case substring(t01_sugup_date, 5, 2) when '09' then 1 else 0 end) as mon09
											,      sum(case substring(t01_sugup_date, 5, 2) when '10' then 1 else 0 end) as mon10
											,      sum(case substring(t01_sugup_date, 5, 2) when '11' then 1 else 0 end) as mon11
											,      sum(case substring(t01_sugup_date, 5, 2) when '12' then 1 else 0 end) as mon12
											  from t01iljung
											 where t01_ccode = '$code'
											   /*and t01_mkind = '$kind'*/
											   and t01_sugup_date like '$year%'
											   and t01_del_yn = 'N'";

									$mon = $conn->get_array($sql);

									for($i=0; $i<12; $i++){
										if ($mon[$i] > 0){
											$cur_i = (($i+1)<10?'0':'').($i+1);?>
											<option value="<?=$cur_i;?>" <? if($cur_i == $month){?>selected<?} ?>><?=($i+1);?>월</option><?
										}
									}
								?>
								</select>
								<span class="btn_pack m icon"><span class="pdf"></span><button name="btnSearch" type="button" onFocus="this.blur();" onClick="serviceCalendarShow('<?=$code;?>', '<?=$kind;?>', '<?=$year;?>', document.getElementById('print_month').value, 'all', '<?=$type;?>', document.getElementById('useType').value, 'pdf', document.getElementById('detail_yn').value, document.getElementById('page_pl').value,document.getElementById('family_yn').value);">출력</button></span>
								<input name="service_kind" type="hidden">
							</td><?
						}else{?>
							<td class="center" style="border-right:1px solid #ffffff;"></td>
							<td class="center last"></td><?
						}
					?>
				</tr>
			</tbody>
		</table><?
	}


	if ($type == 's' || $type == 'y'){
		function chkSvcValue($svcList, $val){
			$chk = false;

			if (is_array($svcList)){
				foreach($svcList as $i => $svc){
					if ($svc == $val){
						$chk = true;
						break;
					}
				}
			}

			return $chk;
		}

		$kindList = $myF->sortArray($conn->kind_list_detail($code, $gHostSvc['voucher']), 'id', 1);

		$html .= '<div>
					<div class="divTblTd" style="clear: both; width:auto; border-right:none;">
					<div class="divTblTh" style="float:left; width:70px; border-bottom:none;">출력선택</div>
					<div class="divTblTd" style="float:left; width:auto; padding-top:2px; padding-left:5px; border-bottom:none; border-right:none;">
						<select name="printType" style="width:auto; margin:0;">
							<option value="pdf">PDF</option>
							<option value="html">HTML</option>
						</select>&nbsp;
						<select name="useType" style="width:auto; margin:0;">
							<option value="y">서비스금액표시</option>
							<option value="n" '.($type == 'y' ? 'selected' : '').'>서비스금액미표시</option>
						</select>&nbsp;
						<select name="detail_yn" style="width:auto; margin:0;">
							<option value="y">달력표시</option>
							<option value="n">달력미표시</option>
						</select>&nbsp;
						<select name="page_pl" style="width:auto; margin:0;">
							<option value="p">세로</option>
							<option value="l">가로</option>
						</select>&nbsp;
						<select id="svcDtlYn" name="svcDtlYn" style="width:auto; margin:0;">
							<option value="Y">기본</option>
							<option value="N">상세</option>
						</select>
					</div>
					<div class="divTblTh" style="float:left; width:auto; margin-left:5px; border-left:1px solid #a6c0f3; padding-left:5px; padding-right:5px; border-bottom:none;">월별출력</div>
					<div class="divTblTd" style="float:left; width:auto; padding-top:2px; padding-left:5px; border-bottom:none; border-right:none;">
						<select name="print_month" style="width:auto; margin:0;">';

						$sql = "select sum(case substring(t01_sugup_date, 5, 2) when '01' then 1 else 0 end) as mon01
								,      sum(case substring(t01_sugup_date, 5, 2) when '02' then 1 else 0 end) as mon02
								,      sum(case substring(t01_sugup_date, 5, 2) when '03' then 1 else 0 end) as mon03
								,      sum(case substring(t01_sugup_date, 5, 2) when '04' then 1 else 0 end) as mon04
								,      sum(case substring(t01_sugup_date, 5, 2) when '05' then 1 else 0 end) as mon05
								,      sum(case substring(t01_sugup_date, 5, 2) when '06' then 1 else 0 end) as mon06
								,      sum(case substring(t01_sugup_date, 5, 2) when '07' then 1 else 0 end) as mon07
								,      sum(case substring(t01_sugup_date, 5, 2) when '08' then 1 else 0 end) as mon08
								,      sum(case substring(t01_sugup_date, 5, 2) when '09' then 1 else 0 end) as mon09
								,      sum(case substring(t01_sugup_date, 5, 2) when '10' then 1 else 0 end) as mon10
								,      sum(case substring(t01_sugup_date, 5, 2) when '11' then 1 else 0 end) as mon11
								,      sum(case substring(t01_sugup_date, 5, 2) when '12' then 1 else 0 end) as mon12
								  from t01iljung
								 where t01_ccode = '$code'
								   and t01_sugup_date like '$year%'
								   and t01_del_yn = 'N'";

						$mon = $conn->get_array($sql);

						for($i=0; $i<12; $i++){
							if ($mon[$i] > 0){
								$cur_i = (($i+1)<10?'0':'').($i+1);
								$html .= '<option value="'.$cur_i.'" '.($cur_i == $month ? 'selected' : '') .'>'.($i+1).'월</option>';
							}
						}

		$html .= '		</select>&nbsp;
						<span class="btn_pack m icon"><span class="pdf"></span><button name="btnSearch" type="button" onFocus="this.blur();" onClick="serviceCalendarShow(\''.$code.'\',\''.$kind.'\',\''.$year.'\',document.getElementById(\'print_month\').value,\'all\',\''.$type.'\',document.getElementById(\'useType\').value,\'pdf\',document.getElementById(\'detail_yn\').value, document.getElementById(\'page_pl\').value,document.getElementById(\'family_yn\').value);">출력</button></span>
						<input name="service_kind" type="hidden">
					</div>
				  </div>';


		$html .= '<div style="margin:10px; border:2px solid #0e69b0;">
					<div class="divTblTd" style="clear: both; width:auto; border-right:none;">
					<div class="divTblTh" style="float:left; width:90px; border-bottom:none;">년도</div>
					<div class="divTblTd" style="float:left; width:auto; padding-top:2px; padding-left:5px; border-bottom:none; border-right:none;">
						<select name="year" style="width:auto;">';

						for($i=$init_year[0]; $i<=$init_year[1]; $i++){
							$html .= '<option value="'.$i.'" '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
						}

		$html .= '		</select>&nbsp;년&nbsp;&nbsp;';

						if ($type == 's' || $type == 'y'){
							$html .= '<select name="family" style="width:auto; margin:0;">
										<option value="N" '.($family == 'N' ? 'selected' : '').'>전체수급자표시</option>
										<option value="Y" '.($family == 'Y' ? 'selected' : '').'>동거케어만표시</option>
										<option value="W" '.($family == 'W' ? 'selected' : '').'>실적만표시</option>
									  </select>';
						}else{
							$html .= '<input name="family" type="hidden" value="N">';
						}

		$html .= '	</div>';

		if ($type == 's' || $type == 'y'){
			$html .= '<div class="divTblTh" style="float:left; width:auto; margin-left:5px; padding-left:5px; border-bottom:none; padding-right:5px; border-left:1px solid #a6c0f3;">'.($type == 's' ? '수급자명' : '요양보호사명').'</div>
					  <div class="divTblTd" style="float:left; width:auto; padding-top:2px; padding-left:5px; border-bottom:none; border-right:none;">
						<input id="cNm" name="cNm" type="text" value="'.$cNm.'">
					  </div>';
		}

		$html .= '	<div style="float:right; width:auto; padding-top:2px; padding-right:5px; text-align:left;">
						<div id="divBtnBody" style="position:absolute; width:40px; height:80px; margin-left:-37px; background-color:#ffffff;">
							<button name="btnSearch" type="button" style="width:100%; height:100%;" onFocus="this.blur();" onClick="list('.$page.');">조회</button>
						</div>
					</div>
				  </div>';


		$html .= '<div>';
		$html .= '<div class="divTblTd" style="clear: both; width:auto; border-right:none;">
					<div class="divTblTh" style="float:left; width:90px; border-bottom:none;">전체</div>
					<div class="divTblTd" style="float:left; width:auto; padding-top:2px; border-bottom:none; border-right:none;">
					  <input id="svcCD_all" name="svcCD_all" type="checkbox" value="Y" class="checkbox" onclick="chkSvcList(\'all\');"><label for="svcCD_all">전체</label></div>
					</div>
				  </div>';

		$first  = 0;
		$allSet = true;

		foreach($kindList as $i => $svc){
			if ($svc['id'] > 10 && $svc['id'] < 20){
				$html .= '<div class="divTblTd" style="clear: both; width:auto; border-right:none;">
							<div class="divTblTh" style="float:left; width:90px; padding-top:2px; padding-left:0; border-bottom:none;">
								<input id="svcCD_all_care" name="svcCDAll[]" type="checkbox" value="Y" class="checkbox" onclick="chkSvcList(\'care\');"><label for="svcCD_all_care">'.$svc['name'].'</label>
							</div>';

				foreach($svc['sub'] as $j => $sub){
					if (chkSvcValue($svcList,$i.'_'.$j)){
						$chk    = true;
						$allSet = false;
					}else{
						$chk = false;
					}

					$html .= '<div class="divTblTd" style="float:left; width:auto; padding-top:2px; border-bottom:none; border-right:none;">
							  <input id="svcCD_'.$i.'_'.$j.'" name="svcCD[]" type="checkbox" value="'.$i.'_'.$j.'" class="checkbox svcType_care" '.($chk ? 'checked' : '').' onclick="$(\'#svcCD_all\').attr(\'checked\',\'\'); $(\'#svcCD_all_care\').attr(\'checked\',\'\');"><label for="svcCD_'.$i.'_'.$j.'">'.$sub.'</label></div>';
				}

				$html .= '</div>';

			}else if ($svc['id'] > 20 && $svc['id'] < 30){
				if ($first == 0){
					$first = 1;
					$html .= '<div class="divTblTd" style="clear: both; width:auto; border-right:none;">
								<div class="divTblTh" style="float:left; width:90px; padding-top:2px; padding-left:0; border-bottom:none;">
									<input id="svcCD_all_vou" name="svcCDAll[]" type="checkbox" value="Y" class="checkbox" onclick="chkSvcList(\'vou\');"><label for="svcCD_all_vou">바우처</label>
								</div>';
				}

				if (is_array($svc['sub'])){
					$html .= '<div class="divTblTh" style="float:left; width:auto; margin-left:10px; padding-right:5px; padding-top:2px; padding-left:0; border-left:1px solid #a6c0f3; border-bottom:none;">
								<input id="svcCD_all_dis" name="svcCDAll[]" type="checkbox" value="Y" class="checkbox" onclick="chkSvcList(\'dis\');"><label for="svcCD_all_dis">'.$svc['name'].'</label>
							  </div>';
					foreach($svc['sub'] as $j => $sub){
						if (chkSvcValue($svcList,$i.'_'.$j)){
							$chk    = true;
							$allSet = false;
						}else{
							$chk = false;
						}

						$html .= '<div class="divTblTd" style="float:left; width:auto; padding-top:2px; border-bottom:none; border-right:none;">
								  <input id="svcCD_'.$i.'_'.$j.'" name="svcCD[]" type="checkbox" value="'.$i.'_'.$j.'" class="checkbox svcType_dis" '.($chk ? 'checked' : '').' onclick="$(\'#svcCD_all\').attr(\'checked\',\'\'); $(\'#svcCD_all_dis\').attr(\'checked\',\'\');"><label for="svcCD_'.$i.'_'.$j.'">'.$sub.'</label></div>';
					}
				}else{
					if (chkSvcValue($svcList,$i)){
						$chk    = true;
						$allSet = false;
					}else{
						$chk = false;
					}

					$html .= '<div class="divTblTd" style="float:left; width:auto; padding-top:2px; border-bottom:none; border-right:none;">
							  <input id="svcCD_'.$i.'" name="svcCD[]" type="checkbox" value="'.$i.'" class="checkbox svcType_vou" '.($chk ? 'checked' : '').' onclick="$(\'#svcCD_all\').attr(\'checked\',\'\'); $(\'#svcCD_all_vou\').attr(\'checked\',\'\');"><label for="svcCD_'.$i.'">'.$svc['name'].'</label></div>';
				}
			}else{
				if ($first == 1) $html .= '</div>';
				if ($first != 2){
					$first = 2;
					$html .= '<div class="divTblTd" style="clear: both; width:auto; border-right:none;">
								<div class="divTblTh" style="float:left; width:90px; padding-top:2px; padding-left:0; border-bottom:none;">
									<input id="svcCD_all_other" name="svcCDAll[]" type="checkbox" value="Y" class="checkbox" onclick="chkSvcList(\'other\');"><label for="svcCD_all_other">기타유료</label>
								</div>';
				}

				if (chkSvcValue($svcList,$i)){
					$chk    = true;
					$allSet = false;
				}else{
					$chk = false;
				}

				$html .= '<div class="divTblTd" style="float:left; width:auto; padding-top:2px; border-bottom:none; border-right:none;">
						  <input id="svcCD_'.$i.'" name="svcCD[]" type="checkbox" value="'.$i.'" class="checkbox svcType_other" '.($chk ? 'checked' : '').' onclick="$(\'#svcCD_all\').attr(\'checked\',\'\'); $(\'#svcCD_all_other\').attr(\'checked\',\'\');"><label for="svcCD_'.$i.'">'.$svc['name'].'</label></div>';
			}
		}

		if ($first != 0)
			$html .= '</div>';


		$html .= '<div class="divTblTd" style="clear: both; width:auto; border-right:none; border-bottom:none;">
					<div class="divTblTh" style="float:left; width:90px; border-bottom:none;">출력일자</div>
					<div class="divTblTd" style="float:left; width:auto; border-bottom:none; border-right:none;">
						<input id="printDT" name="printDT" type="text" value="'.date('Y-m-d', mktime()).'" class="date" style="margin-left:5px; margin-top:2px;">
					</div>
				  </div>';


		$svcParam = '';

		if (is_array($svcList)){
			foreach($svcList as $i => $svc){
				$svcParam .= (!empty($svcParam) ? '/' : '').$svc;
			}
		}

		$html .= '<input id="svcParam" name="svcParam" type="hidden" value="'.$svcParam.'">';


		$html .= '</div>';
		$html .= '<script language="javascript" type="text/javascript">
					function chkSvcList(svc){
						if (svc == "all"){
							$(":input[name=\'svcCD[]\']").each(function(){
								$chk = $("#svcCD_all").attr("checked");
								$(this).attr("checked", $chk);
								$("#svcCD_all_care").attr("checked", $chk);
								$("#svcCD_all_vou").attr("checked", $chk);
								$("#svcCD_all_dis").attr("checked", $chk);
								$("#svcCD_all_other").attr("checked", $chk);
							});
						}else{
							$(".svcType_"+svc).each(function(){
								$(this).attr("checked", $("#svcCD_all_"+svc).attr("checked"));
							});
						}
					}


					$(document).ready(function(){';

					if ($allSet){
						$html .= '$("#svcCD_all").attr("checked", "checked");
								  chkSvcList("all");';
					}
		$html .= '		$chk = "checked";
						$(".svcType_care").each(function(){
							if (!$(this).attr("checked") && $chk != ""){
								$chk = "";
							}
						});

						$("#svcCD_all_care").attr("checked", $chk);

						$chk = "checked";
						$(".svcType_dis").each(function(){
							if (!$(this).attr("checked") && $chk != ""){
								$chk = "";
							}
						});

						$("#svcCD_all_dis").attr("checked", $chk);

						$chk = "checked";
						$(".svcType_vou").each(function(){
							if (!$(this).attr("checked") && $chk != ""){
								$chk = "";
							}
						});

						$("#svcCD_all_vou").attr("checked", $chk);

						$chk = "checked";
						$(".svcType_other").each(function(){
							if (!$(this).attr("checked") && $chk != ""){
								$chk = "";
							}
						});

						$("#svcCD_all_other").attr("checked", $chk);

						__init_form(document.f);';
		$html .= '	});
				  </script>';

		$html = $myF->_gabSplitHtml($html);

		echo $html;
	}

	if ($type == 's' || $type == 'y'){
		echo '<table class="my_table my_border">';
	}else{
		echo '<table class="my_table" style="width:100%;">';
	}
?>


	<colgroup>
	<?
		if ($type == 's' || $type == 'c'){?>
			<col width="40px">
			<col width="80px">
			<col width="100px">
			<col width="60px">
			<col width="80px">
			<col><?
		}else{?>
			<col width="40px">
			<col width="100px">
			<col width="50px">
			<col><?
		}
	?>
	</colgroup>
	<thead>
	<?
		if ($type == 's' || $type == 'c'){?>
			<tr>
				<th class="head">No</th>
				<th class="head">수급자</th>
				<th class="head">
				<?
					if ($type == 's'){
						echo '서비스';
					}else{
						$kind_list = $conn->kind_list($code);

						echo '<select name=\'find_kind\' style=\'width:auto;\' onchange=\'list('.$page.');\'>';
						echo '<option value=\'all\'>전체</option>';

						foreach($kind_list as $i => $k){
							if (($mode != 3) || ($mode == 3 && $k['code'] != '0'))
								echo '<option value=\''.$k['code'].'\' '.($find_kind == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
						}

						echo '</select>';
					}
				?>
				</th>
				<th class="head">등급</th>
				<th class="head">구분</th>
				<th class="head last">월별일정</th>
			</tr><?
		}else{?>
			<tr>
				<th class="head">No</th>
				<th class="head">요양보호사</th>
				<th class="head">
				<?
					echo '<select name=\'find_dept\' style=\'width:auto;\' onchange=\'list('.$page.');\'>';
					echo '<option value=\'all\' '.($find_dept == 'all' ? 'selected' : '').'>전체</option>';

					$sql = "select dept_cd, dept_nm
							  from dept
							 where org_no   = '$code'
							   and del_flag = 'N'
							 order by order_seq";

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					for($i=0; $i<$row_count; $i++){
						$row = $conn->select_row($i);

						echo '<option value=\''.$row['dept_cd'].'\' '.($find_dept == $row['dept_cd'] ? 'selected' : '').'>'.$row['dept_nm'].'</option>';
					}

					$conn->row_free();

					//echo '<option value=\'-\' '.($find_dept == '-' ? 'selected' : '').'>미등록</option>';
					echo '</select>';
				?>
				</th>
				<th class="head last">월별일정</th>
			</tr><?
		}
	?>
	</thead>
	<tbody>
	<?
		/**************************************************



		**************************************************/
			if ($type == 's'){
				if (is_array($svcList)){
					$sql = '';

					foreach($svcList as $i => $svc){
						$svc = explode('_', $svc);

						$sql .= (!empty($sql) ? ' union all ' : '');
						$sql .= 'select t01_jumin as cd
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'01\' '.$family_sql.' then 1 else 0 end) as mon01
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'02\' '.$family_sql.' then 1 else 0 end) as mon02
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'03\' '.$family_sql.' then 1 else 0 end) as mon03
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'04\' '.$family_sql.' then 1 else 0 end) as mon04
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'05\' '.$family_sql.' then 1 else 0 end) as mon05
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'06\' '.$family_sql.' then 1 else 0 end) as mon06
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'07\' '.$family_sql.' then 1 else 0 end) as mon07
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'08\' '.$family_sql.' then 1 else 0 end) as mon08
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'09\' '.$family_sql.' then 1 else 0 end) as mon09
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'10\' '.$family_sql.' then 1 else 0 end) as mon10
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'11\' '.$family_sql.' then 1 else 0 end) as mon11
								 ,      sum(case when substring(t01_sugup_date, 5, 2) = \'12\' '.$family_sql.' then 1 else 0 end) as mon12
								   from t01iljung
								  where t01_ccode               = \''.$code.'\'
									and t01_mkind               = \''.$svc[0].'\'
									and left(t01_sugup_date, 4) = \''.$year.'\'
									and t01_del_yn              = \'N\'';

						if (!empty($svc[1])){
							$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
						}

						$sql .= ' group by t01_jumin';
					}

					$sql = 'select cd
							,      sum(mon01) as mon01
							,      sum(mon02) as mon02
							,      sum(mon03) as mon03
							,      sum(mon04) as mon04
							,      sum(mon05) as mon05
							,      sum(mon06) as mon06
							,      sum(mon07) as mon07
							,      sum(mon08) as mon08
							,      sum(mon09) as mon09
							,      sum(mon10) as mon10
							,      sum(mon11) as mon11
							,      sum(mon12) as mon12
							  from ('.$sql.') as t
							 group by cd';

					unset($tmpSvc);
				}else{
					$sql = "select t01_jumin as cd
							,      sum(case when substring(t01_sugup_date, 5, 2) = '01' $family_sql then 1 else 0 end) as mon01
							,      sum(case when substring(t01_sugup_date, 5, 2) = '02' $family_sql then 1 else 0 end) as mon02
							,      sum(case when substring(t01_sugup_date, 5, 2) = '03' $family_sql then 1 else 0 end) as mon03
							,      sum(case when substring(t01_sugup_date, 5, 2) = '04' $family_sql then 1 else 0 end) as mon04
							,      sum(case when substring(t01_sugup_date, 5, 2) = '05' $family_sql then 1 else 0 end) as mon05
							,      sum(case when substring(t01_sugup_date, 5, 2) = '06' $family_sql then 1 else 0 end) as mon06
							,      sum(case when substring(t01_sugup_date, 5, 2) = '07' $family_sql then 1 else 0 end) as mon07
							,      sum(case when substring(t01_sugup_date, 5, 2) = '08' $family_sql then 1 else 0 end) as mon08
							,      sum(case when substring(t01_sugup_date, 5, 2) = '09' $family_sql then 1 else 0 end) as mon09
							,      sum(case when substring(t01_sugup_date, 5, 2) = '10' $family_sql then 1 else 0 end) as mon10
							,      sum(case when substring(t01_sugup_date, 5, 2) = '11' $family_sql then 1 else 0 end) as mon11
							,      sum(case when substring(t01_sugup_date, 5, 2) = '12' $family_sql then 1 else 0 end) as mon12
							  from t01iljung
							 where t01_ccode               = '$code'
							   and left(t01_sugup_date, 4) = '$year'
							   and t01_del_yn              = 'N'
							 group by t01_jumin";
				}
			}else if ($type == 'c'){
				$sql = "select desire_ssn as cd
						,      sum(case when substring(desire_yymm, 5, 2) = '01' then 1 else 0 end) as mon01
						,      sum(case when substring(desire_yymm, 5, 2) = '02' then 1 else 0 end) as mon02
						,      sum(case when substring(desire_yymm, 5, 2) = '03' then 1 else 0 end) as mon03
						,      sum(case when substring(desire_yymm, 5, 2) = '04' then 1 else 0 end) as mon04
						,      sum(case when substring(desire_yymm, 5, 2) = '05' then 1 else 0 end) as mon05
						,      sum(case when substring(desire_yymm, 5, 2) = '06' then 1 else 0 end) as mon06
						,      sum(case when substring(desire_yymm, 5, 2) = '07' then 1 else 0 end) as mon07
						,      sum(case when substring(desire_yymm, 5, 2) = '08' then 1 else 0 end) as mon08
						,      sum(case when substring(desire_yymm, 5, 2) = '09' then 1 else 0 end) as mon09
						,      sum(case when substring(desire_yymm, 5, 2) = '10' then 1 else 0 end) as mon10
						,      sum(case when substring(desire_yymm, 5, 2) = '11' then 1 else 0 end) as mon11
						,      sum(case when substring(desire_yymm, 5, 2) = '12' then 1 else 0 end) as mon12
						  from counsel_client_desire
						 where org_no               = '$code'
						   and left(desire_yymm, 4) = '$year'
						 group by desire_ssn";
			}else{
				if (is_array($svcList)){
					$sql = '';

					foreach($svcList as $i => $svc){
						$svc = explode('_', $svc);

						$sql .= (!empty($sql) ? ' union all ' : '');
						$sql .= '  select t01_mem_cd1 as cd
								   ,      case when substring(t01_sugup_date, 5, 2) = \'01\' '.$family_sql.' then 1 else 0 end as mon01
								   ,      case when substring(t01_sugup_date, 5, 2) = \'02\' '.$family_sql.' then 1 else 0 end as mon02
								   ,      case when substring(t01_sugup_date, 5, 2) = \'03\' '.$family_sql.' then 1 else 0 end as mon03
								   ,      case when substring(t01_sugup_date, 5, 2) = \'04\' '.$family_sql.' then 1 else 0 end as mon04
								   ,      case when substring(t01_sugup_date, 5, 2) = \'05\' '.$family_sql.' then 1 else 0 end as mon05
								   ,      case when substring(t01_sugup_date, 5, 2) = \'06\' '.$family_sql.' then 1 else 0 end as mon06
								   ,      case when substring(t01_sugup_date, 5, 2) = \'07\' '.$family_sql.' then 1 else 0 end as mon07
								   ,      case when substring(t01_sugup_date, 5, 2) = \'08\' '.$family_sql.' then 1 else 0 end as mon08
								   ,      case when substring(t01_sugup_date, 5, 2) = \'09\' '.$family_sql.' then 1 else 0 end as mon09
								   ,      case when substring(t01_sugup_date, 5, 2) = \'10\' '.$family_sql.' then 1 else 0 end as mon10
								   ,      case when substring(t01_sugup_date, 5, 2) = \'11\' '.$family_sql.' then 1 else 0 end as mon11
								   ,      case when substring(t01_sugup_date, 5, 2) = \'12\' '.$family_sql.' then 1 else 0 end as mon12
									 from t01iljung
									where t01_ccode               = \''.$code.'\'
									  and t01_mkind               = \''.$svc[0].'\'
									  and left(t01_sugup_date, 4) = \''.$year.'\'
									  and t01_del_yn              = \'N\'';

									if (!empty($svc[1])){
										$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
									}

						$sql .= '	union all
								   select t01_mem_cd2 as cd
								   ,      case when substring(t01_sugup_date, 5, 2) = \'01\' '.$family_sql.' then 1 else 0 end as mon01
								   ,      case when substring(t01_sugup_date, 5, 2) = \'02\' '.$family_sql.' then 1 else 0 end as mon02
								   ,      case when substring(t01_sugup_date, 5, 2) = \'03\' '.$family_sql.' then 1 else 0 end as mon03
								   ,      case when substring(t01_sugup_date, 5, 2) = \'04\' '.$family_sql.' then 1 else 0 end as mon04
								   ,      case when substring(t01_sugup_date, 5, 2) = \'05\' '.$family_sql.' then 1 else 0 end as mon05
								   ,      case when substring(t01_sugup_date, 5, 2) = \'06\' '.$family_sql.' then 1 else 0 end as mon06
								   ,      case when substring(t01_sugup_date, 5, 2) = \'07\' '.$family_sql.' then 1 else 0 end as mon07
								   ,      case when substring(t01_sugup_date, 5, 2) = \'08\' '.$family_sql.' then 1 else 0 end as mon08
								   ,      case when substring(t01_sugup_date, 5, 2) = \'09\' '.$family_sql.' then 1 else 0 end as mon09
								   ,      case when substring(t01_sugup_date, 5, 2) = \'10\' '.$family_sql.' then 1 else 0 end as mon10
								   ,      case when substring(t01_sugup_date, 5, 2) = \'11\' '.$family_sql.' then 1 else 0 end as mon11
								   ,      case when substring(t01_sugup_date, 5, 2) = \'12\' '.$family_sql.' then 1 else 0 end as mon12
									 from t01iljung
									where t01_ccode               = \''.$code.'\'
									  and t01_mkind               = \''.$svc[0].'\'
									  and left(t01_sugup_date, 4) = \''.$year.'\'
									  and t01_del_yn              = \'N\'';

									if (!empty($svc[1])){
										$sql .= ' and t01_svc_subcode = \''.$svc[1].'\'';
									}
					}

					$sql = 'select cd
							,      sum(mon01) as mon01
							,      sum(mon02) as mon02
							,      sum(mon03) as mon03
							,      sum(mon04) as mon04
							,      sum(mon05) as mon05
							,      sum(mon06) as mon06
							,      sum(mon07) as mon07
							,      sum(mon08) as mon08
							,      sum(mon09) as mon09
							,      sum(mon10) as mon10
							,      sum(mon11) as mon11
							,      sum(mon12) as mon12
							  from ('.$sql.') as t
							 group by cd';

					unset($tmpSvc);
				}else{
					$sql = "select cd
							,      sum(mon01) as mon01
							,      sum(mon02) as mon02
							,      sum(mon03) as mon03
							,      sum(mon04) as mon04
							,      sum(mon05) as mon05
							,      sum(mon06) as mon06
							,      sum(mon07) as mon07
							,      sum(mon08) as mon08
							,      sum(mon09) as mon09
							,      sum(mon10) as mon10
							,      sum(mon11) as mon11
							,      sum(mon12) as mon12
							  from (
								   select t01_mem_cd1 as cd
								   ,      case when substring(t01_sugup_date, 5, 2) = '01' $family_sql then 1 else 0 end as mon01
								   ,      case when substring(t01_sugup_date, 5, 2) = '02' $family_sql then 1 else 0 end as mon02
								   ,      case when substring(t01_sugup_date, 5, 2) = '03' $family_sql then 1 else 0 end as mon03
								   ,      case when substring(t01_sugup_date, 5, 2) = '04' $family_sql then 1 else 0 end as mon04
								   ,      case when substring(t01_sugup_date, 5, 2) = '05' $family_sql then 1 else 0 end as mon05
								   ,      case when substring(t01_sugup_date, 5, 2) = '06' $family_sql then 1 else 0 end as mon06
								   ,      case when substring(t01_sugup_date, 5, 2) = '07' $family_sql then 1 else 0 end as mon07
								   ,      case when substring(t01_sugup_date, 5, 2) = '08' $family_sql then 1 else 0 end as mon08
								   ,      case when substring(t01_sugup_date, 5, 2) = '09' $family_sql then 1 else 0 end as mon09
								   ,      case when substring(t01_sugup_date, 5, 2) = '10' $family_sql then 1 else 0 end as mon10
								   ,      case when substring(t01_sugup_date, 5, 2) = '11' $family_sql then 1 else 0 end as mon11
								   ,      case when substring(t01_sugup_date, 5, 2) = '12' $family_sql then 1 else 0 end as mon12
									 from t01iljung
									where t01_ccode               = '$code'
									  and left(t01_sugup_date, 4) = '$year'
									  and t01_del_yn              = 'N'
									union all
								   select t01_mem_cd2 as cd
								   ,      case when substring(t01_sugup_date, 5, 2) = '01' $family_sql then 1 else 0 end as mon01
								   ,      case when substring(t01_sugup_date, 5, 2) = '02' $family_sql then 1 else 0 end as mon02
								   ,      case when substring(t01_sugup_date, 5, 2) = '03' $family_sql then 1 else 0 end as mon03
								   ,      case when substring(t01_sugup_date, 5, 2) = '04' $family_sql then 1 else 0 end as mon04
								   ,      case when substring(t01_sugup_date, 5, 2) = '05' $family_sql then 1 else 0 end as mon05
								   ,      case when substring(t01_sugup_date, 5, 2) = '06' $family_sql then 1 else 0 end as mon06
								   ,      case when substring(t01_sugup_date, 5, 2) = '07' $family_sql then 1 else 0 end as mon07
								   ,      case when substring(t01_sugup_date, 5, 2) = '08' $family_sql then 1 else 0 end as mon08
								   ,      case when substring(t01_sugup_date, 5, 2) = '09' $family_sql then 1 else 0 end as mon09
								   ,      case when substring(t01_sugup_date, 5, 2) = '10' $family_sql then 1 else 0 end as mon10
								   ,      case when substring(t01_sugup_date, 5, 2) = '11' $family_sql then 1 else 0 end as mon11
								   ,      case when substring(t01_sugup_date, 5, 2) = '12' $family_sql then 1 else 0 end as mon12
									 from t01iljung
									where t01_ccode               = '$code'
									  and left(t01_sugup_date, 4) = '$year'
									  and t01_del_yn              = 'N'
								   ) as t
							 group by cd";
				}
			}

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$iljung[$row['cd']] = $row;
			}

			$conn->row_free();
		/*************************************************/




		// 총 카운트
		if ($type == 's' || $type == 'c'){
			if ($find_kind == 'all'){
				$find_kind = '';
			}else{
				$find_kind = ' and m03_mkind = \''.$find_kind.'\'';
			}


			// 수급자 조건문
			$wsl = " where m03_ccode  = '$code' $find_kind
					   and m03_del_yn = 'N'
					   and '$year' between left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y') end, 4) and left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '9999' end, 4)
					   and (select count(*)
							  from t01iljung
							 where t01_ccode = m03_ccode
							   and t01_mkind = m03_mkind
							   and t01_jumin = m03_jumin
							   and t01_sugup_date like '$year%' $family_sql
							   and t01_del_yn = 'N') > 0";

			if (!empty($cNm))
				$wsl .= " and m03_name like '".$cNm."%'";

			$sql = "select count(*)
					  from m03sugupja $wsl";
		}else{
			if ($find_dept == 'all'){
				$find_dept = '';
			}else if ($find_dept == 'not'){
				$find_dept = ' and m02_dept_cd = \'0\'';
			}else{
				$find_dept = ' and m02_dept_cd = \''.$find_dept.'\'';
			}

			$wsl = " where m02_ccode  = '$code' $find_dept
					   and m02_del_yn = 'N'
					   and '$year' between left(case when ifnull(m02_yipsail, '') != '' then m02_yipsail else date_format(now(), '%Y') end, 4) and left(case when ifnull(m02_ytoisail, '') != '' then m02_ytoisail else '9999' end, 4)";

			if (!empty($cNm))
				$wsl .= " and m02_yname like '".$cNm."%'";

			$sql = "select count(*)
					  from m02yoyangsa $wsl";
		}

		if ($type == 's' || $type == 'c'){
			// 수급자 쿼리
			if ($type == 's'){
				if (is_array($svcList)){
					$sql = 'select min(m03_mkind) as kind
								,      m03_name as name
								,      m03_jumin as jumin
								,      case when m03_mkind = \'0\' then LVL.m81_name
											when m03_mkind = \'1\' then concat(m03_ylvl, \'등급\') else \'\' end as lvl_name
								,      case when m03_mkind = \'0\' then STP.m81_name else \'\' end as stp_name
								,      m03_key as key_no
								,      m03_injung_no as injung_no
								,      left(case when ifnull(m03_gaeyak_fm, \'\') != \'\' then ifnull(m03_gaeyak_fm, \'\') else date_format(now(), \'%Y%m\') end, 6) as gaeyak_fm
								,      left(case when ifnull(m03_gaeyak_to, \'\') != \'\' then ifnull(m03_gaeyak_to, \'\') else \'999912\' end, 6) as gaeyak_to
								  from m03sugupja
								  left join m81gubun as LVL
									on LVL.m81_gbn  = \'LVL\'
								   and LVL.m81_code = m03_ylvl
								  left join m81gubun as STP
									on STP.m81_gbn  = \'STP\'
								   and STP.m81_code = m03_skind '.$wsl;

					$sql .= ' group by m03_jumin
							  order by name';
				}else{
					$sql = "select min(m03_mkind) as kind
							,      m03_name as name
							,      m03_jumin as jumin
							,      case when m03_mkind = '0' then LVL.m81_name
										when m03_mkind = '1' then concat(m03_ylvl, '등급') else '' end as lvl_name
							,      case when m03_mkind = '0' then STP.m81_name else '' end as stp_name
							,      m03_key as key_no
							,      m03_injung_no as injung_no
							,      left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y%m') end, 6) as gaeyak_fm
							,      left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '999912' end, 6) as gaeyak_to
							  from m03sugupja
							  left join m81gubun as LVL
								on LVL.m81_gbn  = 'LVL'
							   and LVL.m81_code = m03_ylvl
							  left join m81gubun as STP
								on STP.m81_gbn  = 'STP'
							   and STP.m81_code = m03_skind $wsl
							 group by m03_jumin
							 order by m03_name
							 /*limit $pageCount, $item_count*/";
				}
			}else{
				$sql = "select min(m03_mkind) as kind
						,      m03_name as name
						,      m03_jumin as jumin
						,      case when m03_mkind = '0' then LVL.m81_name
									when m03_mkind = '1' then concat(m03_ylvl, '등급') else '' end as lvl_name
						,      case when m03_mkind = '0' then STP.m81_name else '' end as stp_name
						,      m03_key as key_no
						,      m03_injung_no as injung_no
						,      left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y%m') end, 6) as gaeyak_fm
						,      left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '999912' end, 6) as gaeyak_to
						  from m03sugupja
						  left join m81gubun as LVL
							on LVL.m81_gbn  = 'LVL'
						   and LVL.m81_code = m03_ylvl
						  left join m81gubun as STP
							on STP.m81_gbn  = 'STP'
						   and STP.m81_code = m03_skind $wsl
						 group by m03_jumin
						 order by m03_name
						 /*limit $pageCount, $item_count*/";
			}

		}else{
			// 요양보호사 쿼리
			$sql = "select min(m02_mkind) as kind
					,      m02_yname as name
					,      m02_yjumin as jumin
					,      m02_ycode as ycode
					,      dept.dept_nm
					,      left(case when ifnull(m02_yipsail, '')  != '' then ifnull(m02_yipsail, '')  else date_format(now(), '%Y%m') end, 6) as gaeyak_fm
					,      left(case when ifnull(m02_ytoisail, '') != '' then ifnull(m02_ytoisail, '') else '999912' end, 6) as gaeyak_to
					  from m02yoyangsa
					  left join dept
					    on dept.org_no  = m02_ccode
					   and dept.dept_cd = m02_dept_cd $wsl
					 group by m02_yjumin
					 order by m02_yname
					 /*limit $pageCount, $item_count*/";
		}

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$data[$i] = $conn->select_row($i);
		}

		$conn->row_free();
		$data_count = $row_count;
		$seq = 0;

		for($i=0; $i<$data_count; $i++){
			$addRow = false;
			for($j=1; $j<=12; $j++){
				if ($iljung[$data[$i]['jumin']]['mon'.($j < 10 ? '0' : '').$j] > 0){
					$addRow = true;
					break;
				}
			}

			if ($addRow){
				$seq ++;?>
				<tr>
					<td class="center"><?=$pageCount + $seq;?></td>
					<td class="left"><?=$data[$i]['name'];?></td>
					<?
						if ($type == 's' || $type == 'c'){?>
							<td class="left"><?=$conn->kind_name_svc($data[$i]['kind']);?></td>
							<td class="center"><?=$data[$i]["lvl_name"];?></td>
							<td class="left"><?=$data[$i]["stp_name"];?></td><?
						}else{?>
							<td class="left"><?=$data[$i]["dept_nm"];?></td><?
						}
					?>
					<td class="left last" style="padding-top:2px;">
					<?
						for($j=1; $j<=12; $j++){
							$class = 'my_month ';
							$cur_i = ($j < 10 ? '0' : '').$j;

							if (ceil($data[$i]['gaeyak_fm']) > ceil($year.$cur_i)){
								$text = '';
							}else{
								if (ceil($data[$i]['gaeyak_to']) < ceil($year.$cur_i)){
									$text = '';
								}else{
									if ($iljung[$data[$i]['jumin']]['mon'.$cur_i] > 0){
										$class .= 'my_month_y ';
										$color  = 'color:#000000;';

										if ($type == 'service'){
											$text = '<a href=\'#\' onclick=\'_iljung_print("'.$code.'","'.$kind.'","'.$year.'","'.$cur_i.'","'.$ed->en($data[$i]["jumin"]).'",document.f.service_kind.options[document.f.service_kind.selectedIndex].text); return false;\'>'.$j.'월</a>';
										}else{
											$text = '<a href="#" onclick="serviceCalendarShow(\''.$code.'\',\''.$kind.'\',\''.$year.'\',\''.$cur_i.'\',\''.$ed->en($data[$i]["jumin"]).'\',\''.$type.'\', document.f.useType.value, document.f.printType.value, document.getElementById(\'detail_yn\').value, document.getElementById(\'page_pl\').value, document.getElementById(\'family\').value); return false;">'.$j.'월</a>';
										}
									}else{
										$class .= 'my_month_1 ';
										$color  = 'color:#cccccc;';
										$text   = '<a style=\'cursor:default;\'>'.$j.'월</a>';
									}
								}
							}

							if ($j == 12){
								$style = 'float:left;';
							}else{
								$style = 'float:left; margin-right:2px;';
							}?>
							<div class="<?=$class;?>" style="<?=$style;?> <?=$color;?>"><?=$text;?></div><?
						}
					?>
					</td>
				</tr><?
			}
		}
		unset($data);
		unset($iljung);
	?>
	</tbody>
</table>

<div style="text-align:left;">
	<div style="/*position:absolute; width:auto;*/ padding-left:10px;">검색된 전체 갯수 : <?=number_format($seq);?></div>
	<!--div style="width:100%; text-align:center;">
	<?
		$paging = new YsPaging($params);
		$paging->printPaging();

		unset($paging);
	?>
	</div-->
</div>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">
<input type="hidden" name="type" value="<?=$type;?>">
<input type="hidden" name="page" value="<?=$page;?>">
<input type="hidden" name="month" value="">
<input type="hidden" name="jumin" value="">
<input type="hidden" name="family_yn" value="<?=$family;?>">

</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
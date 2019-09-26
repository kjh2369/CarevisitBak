<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	/*
	 * 기능		: 기관등록 / 수정
	 * 작성자	: 김재용
	 * 일자		: 2011.03.21
	 */

	$find_yoy_name  = $_POST['find_yoy_name'];
	$find_yoy_phone = $_POST['find_yoy_phone'];
	$find_yoy_stat  = $_POST['find_yoy_stat'];

	$page  = $_REQUEST['page'];			//페이지
	$code  = $_REQUEST['code'] != '' ? $_REQUEST['code'] : $_SESSION["userCenterCode"];			//기관번호
	$kind  = $conn->get_data("select min(m00_mkind) from m00center where m00_mcode = '$code'");//기관분류
	$jumin = $ed->de($_REQUEST['jumin']);	//주민번호

	// 기관분류 리스트
	$k_list = $conn->kind_list($code);
	$center_code = $conn->center_code($code, $kind);
	$center_name = $conn->center_name($code, $kind);

	// 기관의 배상책임보험 가입여부
	$sql = "select g02_ins_code
			  from g02inscenter
			 where g02_ccode = '$code'
			   and g02_mkind = '0'";
	$center_ins_code = $conn->get_data($sql);

	// 기본기관구분
	$basic_kind = $k_list[0]['code'];

	$sql = "select *
			  from m02yoyangsa
			 where m02_ccode  = '$code'
			   and m02_yjumin = '$jumin'
			   and m02_del_yn = 'N'
			 order by m02_mkind";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$mst[$row['m02_mkind']] = $row;

	}

	$conn->row_free();

	// 급여액
	$hourly_1 = 0;

	$hourly_2[1] = 0;
	$hourly_2[2] = 0;
	$hourly_2[3] = 0;
	$hourly_2[9] = 0;

	$hourly_3 = 0;
	$hourly_4 = 0;

	if ($mst[0]["m02_ygupyeo_kind"] == '1' || $mst[0]["m02_ygupyeo_kind"] == '2'){
		if ($mst[0]['m02_pay_type'] == 'Y'){
			$pay_type = '1'; //시급(고정급)
		}else{
			$pay_type = '2'; //시급(변동급)
		}
	}else if ($mst[0]["m02_ygupyeo_kind"] == '3'){
		$pay_type = '3'; //월급

		if ($mst[0]['m02_pay_type'] == 'Y'){
			$pay_com_type = 'Y';
		}
	}else if ($mst[0]["m02_ygupyeo_kind"] == '4'){
		$pay_type = '4'; //총액비율
	}else{
		$pay_type = '0';
	}

	switch($pay_type){
	case '1':
		$hourly_1 = $mst[0]["m02_ygibonkup"];
		break;
	case '2':
		$sql = "select m02_gubun
				,      m02_pay
				  from m02pay
				 where m02_ccode = '$code'
				   and m02_mkind = '$kind'
				   and m02_jumin = '$jumin'";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$hourly_2[$row['m02_gubun']] = $row['m02_pay'];
		}

		$conn->row_free();
		break;
	case '3':
		$hourly_3 = $mst[0]["m02_ygibonkup"];
		break;
	case '4':
		$hourly_4 = $mst[0]["m02_ysuga_yoyul"];
		break;
	}


	//동거가족케어
	if($mst[0]['m02_yfamcare_type'] == '1'){
			$famcare_type = 1; //고정급
		if($mst[0]['m02_yfamcare_umu'] == 'N'){
			$famcare_type = '0';  //무
		}
	}else if($mst[0]['m02_yfamcare_type'] == '2'){
			$famcare_type = 2; //수가총액
	}else if($mst[0]['m02_yfamcare_type'] == '3'){
			$famcare_type = 3; //고정급
	}else {
		$famcare_type = '0';
	}


	switch($famcare_type){
	case '1':
		$famcare_pay1 = $mst[0]['m02_yfamcare_pay'];
		break;
	case '2':
		$famcare_pay2 = $mst[0]['m02_yfamcare_pay'];
		break;
	case '3':
		$famcare_pay3 = $mst[0]['m02_yfamcare_pay'];
		break;
	}

	if (!is_array($mst)){
		$mode = 1; //등록
	}else{
		$mode = 2; //수정
	}

	// 등록시 기본설정
	if ($mode == 1){
		$mst[$basic_kind]['m02_mkind'] = $basic_kind;	//기관구분 기본 설정

		$mst[0]['m02_ygoyong_kind']	= '1';	//고용형태
		$mst[0]['m02_ygoyong_stat']	= '1';	//고용상태
		$mst[0]['m02_ygupyeo_kind']	= '1';	//급여산정방식
		$mst[0]['m02_yfamcare_umu']	= 'N';	//동거가족케어유무
		$mst[0]['m02_yipsail']		= date('Y-m-d', mkTime());	//입사일자
		$mst[0]['m02_bnpay_yn']     = 'Y'; //월급제 목욕,간호수당 포함여부
	}

	// 스마트폰 업무 구분
	$smart_gbn['Y'] = 'N';
	$smart_gbn['M'] = 'N';

	if ($mst[0]['m02_jikwon_gbn'] == 'A'){
		$smart_gbn['Y'] = 'Y';
		$smart_gbn['M'] = 'Y';
	}else{
		if ($mst[0]['m02_jikwon_gbn'] != ''){
			$smart_gbn[$mst[0]['m02_jikwon_gbn']] = 'Y';
		}
	}

	// 타이틀
	if ($mode == 1){
		$title = '직원등록';
	}else{
		$title = '직원정보수정';
	}

	// 기관의 보험가입정보
	$sql = "select g02_ins_code as code
			,      g02_ins_from_date as fromDate
			,      g02_ins_to_date as toDate
			  from g02inscenter
			 where g02_ccode = '$code'
			   and g02_mkind = '0'";
	$ins = $conn->get_array($sql);

	// 보험가입여부
	if ($mst[$kind]['m02_ins_yn'] == 'Y'){
		$insYN = 'Y';
	}else{
		$insYN = 'N';
	}

	// 보험정보
	if ($insYN == 'Y'){
		$insCode     = $mst[$kind]['m02_ins_code'];
		$insFromDate = $mst[$kind]['m02_ins_from_date'];
		$insToDate   = $mst[$kind]['m02_ins_to_date'];
	}else{
		$insCode = $ins['code'];

		if ($mode == 1){
			$insFromDate = $ins['fromDate'];
		}else{
			//$insFromDate = ($ins['fromDate'] > $mst[0]['m02_yipsail'] ? $ins['fromDate'] : $mst[0]['m02_yipsail']);
			$sql = "select g03_ins_to_date
					  from g03insapply
					 where g03_jumin          = '".$mst[$kind]["m02_yjumin"]."'
					   and g03_ins_from_date >= '".$ins['fromDate']."'
					 order by g03_ins_to_date desc
					 limit 1";
			$tempDate = $conn->get_data($sql);

			if (strLen($tempDate) == 8){
				$tempDate = $myF->dateStyle($tempDate);
				$tempDate = $myF->dateAdd('day', 1, $tempDate, 'Ymd');
				$insFromDate = ($ins['fromDate'] > $tempDate ? $ins['fromDate'] : $tempDate);
			}else{
				$insFromDate = ($ins['fromDate'] > $mst[0]['m02_yipsail'] ? $ins['fromDate'] : $mst[0]['m02_yipsail']);
			}
		}

		$ins[1]    = $insFromDate;
		$insToDate = $ins['toDate'];

	}

	$insData = $conn->get_array("select g01_name, g01_use from g01ins where g01_code = '$insCode'");
	$insName = $insData[0];
	$insUse  = $insData[1];

	// 급여 항목 고정급
	if ($mode > 1){
		$sql = "select concat(t25_pay_kind1, '_', t25_pay_kind2, '_', t25_pay_code) as code
				,      t25_pay_amount as amount
				  from t25payfix
				 where t25_ccode    = '$code'
				   and t25_mkind    = '$kind'
				   and t25_yoy_code = '$jumin'";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$my_fix_pay[$i] = $conn->select_row($i);
		}

		$conn->row_free();
	}

	// 급여 항목 리스트
	$sql = "select t20_kind1
			,      t20_kind2
			,      t20_code
			,      t20_name
			  from t20subject
			 where t20_ccode = '$code'
			   and concat(t20_kind1,'_',t20_kind2,'_',t20_code) != '1_1_01'
			   and t20_fix   = 'Y'
			   and t20_use   = 'Y'
			   and t20_name != ''
			 order by t20_kind1, t20_kind2, t20_code";
	$conn->query($sql);
	$conn->fetch();
	$subject_count = $conn->row_count();
	$subject_count = 0;

	for($i=0; $i<$subject_count; $i++){
		$my_subject[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	// 병합행카운트
	$row_span_count = 15 + ceil($subject_count / 2);

	$button  = '';
	$button .= "<span class='btn_pack m icon'><span class='list'></span><button type='button' onFocus='this.blur();' onClick='_list_member($page);'>리스트</button></span> ";

	if ($mode == 1){
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_save_member();'>등록</button></span> ";
	}else{
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_save_member();'>수정</button></span> ";

		if ($_SESSION["userLevel"] == 'A'){
			$button .= "<span class='btn_pack m icon'><span class='delete'></span><button type='button' onFocus='this.blur();' onClick=''>삭제</button></span>";
		}
	}

	/*
	// 직원고용상태
	$member_stat = $mst[$kind]['m02_ygoyong_stat'];

	// 익월서비스 일정표 유무
	$next_ym = date("Ym",strtotime("+1 month"));
	$sql = "select count(*)
			  from t01iljung
			 where t01_ccode = '$code'
			   and t01_mkind = '$kind'
			   and t01_sugup_date like '$next_ym%'
			   and '$jumin' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
			   and t01_del_yn = 'N'";
	$work_count = $conn->get_data($sql);

	// 상담일지(격월주기)
	$sql = "select datediff(date_add(date_format(concat(ifnull(max(r260_date),''),'000000'), '%Y-%m-%d'), interval 2 month), date_format(now(), '%Y-%m-%d'))
			  from r260talk
			 where r260_ccode    = '$code'
			   and r260_mkind    = '$kind'
			   and r260_yoyangsa = '$jumin'";
	$talk_days = $conn->get_data($sql);

	// 직무평가 및 만족도 조사(격월주기)
	$sql = "select datediff(date_add(date_format(concat(ifnull(max(r270_date),''),'000000'), '%Y-%m-%d'), interval 2 month), date_format(now(), '%Y-%m-%d'))
			  from r270test
			 where r270_ccode    = '$code'
			   and r270_mkind    = '$kind'
			   and r270_yoy_code = '$jumin'";
	$test_days = $conn->get_data($sql);
	*/

	if ($mst[$basic_kind]["m02_yjakuk_kind"] == '') $mst[$basic_kind]["m02_yjakuk_kind"] = '12';
	if ($mst[$basic_kind]["m02_yjikjong"] == '') $mst[$basic_kind]["m02_yjikjong"] = '11';
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script language='javascript' src='../js/report.js'></script>
<script language='javascript' src='../js/work.js'></script>
<script language='javascript'>
<!--
window.onload = function(){
	_show_pay_layer(__get_value(document.getElementsByName('yGupyeoKind')));
	_famcare_pay_layer(__get_value(document.getElementsByName('yFamCareType')));
	set_out_date(__get_value(document.getElementsByName('yGoyongStat')));
	_ins_join_yn(__get_value(document.getElementsByName('insYN')), '<?=$insYN;?>', 'insFromDate', 'insToDate');
	__init_form(document.f);
	set_4ins_yn('<?=$mst[$basic_kind]['m02_y4bohum_umu'];?>');
	//_family_care_yn();
}

function set_out_date(gubun){
	var object = document.getElementById('yToisail');

	if (gubun == '1' || gubun == '2'){
		object.disabled = true;
		object.style.backgroundColor = '#eeeeee';
	}else{
		object.disabled = false;
		object.style.backgroundColor = '#ffffff';
	}
}

function set_4ins_yn(yn){
	var yKuksinMpay = document.getElementById('yKuksinMpay');
	var yHealthMpay = document.getElementById('yHealthMpay');
	var yEmployMpay = document.getElementById('yEmployMpay');
	var ySanjeMpay  = document.getElementById('ySanjeMpay');

	if (yn == 'Y'){
		var disabled = false;
		var bgcolor  = '#ffffff';
	}else{
		var disabled = true;
		var bgcolor  = '#efefef';
	}

	yKuksinMpay.disabled = disabled;
	yHealthMpay.disabled = disabled;
	yEmployMpay.disabled = disabled;
	ySanjeMpay.disabled  = disabled;

	yKuksinMpay.style.backgroundColor = bgcolor;
	yHealthMpay.style.backgroundColor = bgcolor;
	yEmployMpay.style.backgroundColor = bgcolor;
	ySanjeMpay.style.backgroundColor  = bgcolor;
}
//-->
</script>
<form name="f" method="post">
<div class="title"><?=$title;?></div>
<?
	if ($mode > 1){?>
		<!--<table class="my_table my_border">
			<colgroup>
				<col width="10%;">
				<col width="10%;">
				<col width="10%;">
				<col width="10%;">
				<col width="10%;">
				<col width="10%;">
				<col width="10%;">
				<col width="10%;">
				<col width="10%;">
				<col width="10%;">
			</colgroup>
			<thead>
				<tr>
					<th class="head" rowspan="2">근로<br>계약서</th>
					<th class="head" rowspan="2">개인정보<br>보호동의서</th>
					<th class="head" rowspan="2">익월서비스<br>일정표</th>
					<th class="head" rowspan="2">상담일지<br>(격월주기)</th>
					<th class="head" rowspan="2">직무평가및<br>만족도조사<br>(격월주기)</th>
					<th class="head" colspan="4">교육</th>
					<th class="head last" rowspan="2">건강검진<br>(년주기)</th>
				</tr>
				<tr>
					<th class="head">신규</th>
					<th class="head">급여제공</th>
					<th class="head">업무범위</th>
					<th class="head">개인정보보호</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="center"><a href="#" onclick="showReport(91, '<?=$code;?>', '<?=$kind;?>', '', '<?=$mst[$kind]['m02_key'];?>', '')">출력</a></td>
					<td class="center">-</td>
					<td class="center">
					<?
						if ($work_count > 0){?>
							<a href="#" onclick="serviceCalendarShow('<?=$code;?>', '<?=$kind;?>', '<?=subStr($next_ym, 0, 4);?>', '<?=subStr($next_ym, 4, 2);?>', '<?=$ed->en($jumin);?>', 'y', 'n', 'pdf', 'y', 'y');">보기</a><?
						}else{
							if ($member_stat == '1'){?>
								<a href="#" onclick="window.open('../iljung/iljung.php?menuIndex=work&menuSeq=1&manager=true&gubun=reg', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');">등록</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center" id="talk_body">
					<?
						if ($talk_days > 0){?>
							<a href="#" onclick="_member_report_layer('talk_body', '47', '<?=$code;?>', '<?=$kind;?>', '<?=$ed->en($jumin);?>');">작성(完)</a><?
						}else{
							if ($member_stat == '1'){?>
								<a href="#" onclick="__my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','47','php','1','2'), 'talk_body', '47', 'code', 'kind', 'jumin');">미작성</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center" id="test_body">
					<?
						if ($test_days > 0){?>
							<a href="#" onclick="_member_report_layer('test_body', '33', '<?=$code;?>', '<?=$kind;?>', '<?=$ed->en($jumin);?>');">작성(完)</a><?
						}else{
							if ($member_stat == '1'){?>
								<a href="#" onclick="__my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','33','php','1','2'), 'test_body', '33', 'code', 'kind', 'jumin');">미작성</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center">-</td>
					<td class="center">-</td>
					<td class="center">-</td>
					<td class="center">-</td>
					<td class="center last">-</td>
				</tr>
			</tbody>
		</table>--><?
	}
?>

<table class="my_table my_border" style="<? if($mode > 1){?>margin-top:-1px;<?} ?>">
	<colgroup>
		<col width="100px">
		<col width="130px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$code;?></td>
			<th>기관명</th>
			<td class="left last"><?=$center_name;?></td>
		</tr>
		<tr>
			<th>기관구분</th>
			<td class="last" colspan="3">
			<?
				for($i=0; $i<sizeof($k_list); $i++){
					if ($mst[$i]['m02_mkind'] == $k_list[$i]['code']){
						$checked = 'checked';
					}else{
						$checked = '';
					}?>
					<input name="kind_temp[]" type="hidden" value="<?=$k_list[$i]['code'];?>">
					<input name="kind_list[]" type="checkbox" class="checkbox" value="<?=$k_list[$i]['code'];?>" onclick="_set_kind(this);" <?=$checked;?>><?=$k_list[$i]['name'];
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('reg_sub.php');
?>
<input name="page"	type="hidden" value="<?=$page;?>">
<input name="code"	type="hidden" value="<?=$code;?>">
<input name="kind"	type="hidden" value="<?=$kind;?>">
<input name="mode"	type="hidden" value="<?=$mode;?>">

<input name="ins_no"	type="hidden" value="<?=$mst[$kind]['m02_ins_no'];?>">
<input name="ins_code"	type="hidden" value="<?=$insCode;?>">

<input name="centerInsFromDate" type="hidden" value="<?=$myF->dateStyle($ins[1]);?>">
<input name="centerInsToDate" type="hidden" value="<?=$myF->dateStyle($ins[2]);?>">

<input name="find_yoy_name"	type="hidden" value="<?=$find_yoy_name;?>">
<input name="find_yoy_phone"type="hidden" value="<?=$find_yoy_phone;?>">
<input name="find_yoy_stat"	type="hidden" value="<?=$find_yoy_stat;?>">
</form>

<div style="width:100%; margin:0; padding:0; text-align:right; margin:5px;"><?=$button;?></div>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
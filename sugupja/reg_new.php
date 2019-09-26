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
	}else if ($mst[0]["m02_ygupyeo_kind"] == '4'){
		$pay_type = '4'; //총액비율
	}else{
		$pay_type = '1';
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
		$hourly_3 = 0;
		break;
	case '4':
		$hourly_4 = 0;
		break;
	}

	$conn->row_free();

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
		$sql = "select g03_ins_to_date
				  from g03insapply
				 where g03_ins_code       = '".$ins['code']."'
				   and g03_jumin          = '".$mst[$kind]["m02_yjumin"]."'
				   and g03_ins_from_date >= '".$ins['fromDate']."'
				 order by g03_ins_to_date desc
				 limit 1";
		$tempDate = $conn->get_data($sql);

		$insCode = $ins['code'];

		if (strLen($tempDate) == 8){
			$tempDate = $myF->dateStyle($tempDate);
			$tempDate = $myF->dateAdd('day', 1, $tempDate, 'Ymd');
			$insFromDate = ($ins['fromDate'] > $tempDate ? $ins['fromDate'] : $tempDate);
		}else{
			$insFromDate = ($ins['fromDate'] > $mst[0]['m02_yipsail'] ? $ins['fromDate'] : $mst[0]['m02_yipsail']);
		}
		$ins[1] = $insFromDate;
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
			   and t20_fix   = 'Y'
			   and t20_use   = 'Y'
			   and t20_name != ''
			 order by t20_kind1, t20_kind2, t20_code";
	$conn->query($sql);
	$conn->fetch();
	$subject_count = $conn->row_count();

	for($i=0; $i<$subject_count; $i++){
		$my_subject[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	// 병합행카운트
	$row_span_count = 12 + ceil($subject_count / 2);

	$button = '';
	if ($mode == 1){
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_save_member();'>등록</button></span> ";
	}else{
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_save_member();'>수정</button></span> ";

		if ($_SESSION["userLevel"] == 'A'){
			$button .= "<span class='btn_pack m icon'><span class='delete'></span><button type='button' onFocus='this.blur();' onClick=''>삭제</button></span>";
		}
	}

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
	show_pay_layer(__get_value(document.getElementsByName('yGupyeoKind')));
	set_out_date(__get_value(document.getElementsByName('yGoyongStat')));
	_ins_join_yn(__get_value(document.getElementsByName('insYN')), '<?=$insYN;?>', 'insFromDate', 'insToDate');
	__init_form(document.f);
	_family_care_yn();
}

function show_pay_layer(gubun){
	var value = new Array();

	for(var i=1; i<=4; i++){
		value[i] = new Array();
		value[i][1] = false;
		value[i][2] = false;
	}

	if (gubun == '1Y'){
		value[1][1] = true;
		value[1][2] = true;
	}else if (gubun == '1N'){
		value[2][1] = true;
		value[2][2] = true;
	}else if (gubun == '3'){
		value[3][1] = true;
		value[3][2] = true;
	}else if (gubun == '4'){
		value[4][1] = true;
		value[4][2] = true;
	}

	_show_tbody_layer('tbody_1_1', 'layer_1_1', value[1][1]);
	_show_tbody_layer('tbody_1_2', 'layer_1_2', value[1][2]);

	_show_tbody_layer('tbody_2_1', 'layer_2_1', value[2][1]);
	_show_tbody_layer('tbody_2_2', 'layer_2_2', value[2][2]);

	_show_tbody_layer('tbody_3_1', 'layer_3_1', value[3][1]);
	_show_tbody_layer('tbody_3_2', 'layer_3_2', value[3][2]);

	_show_tbody_layer('tbody_4_1', 'layer_4_1', value[4][1]);
	_show_tbody_layer('tbody_4_2', 'layer_4_2', value[4][2]);
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

function set_kind(kind){
	var object = document.getElementById('kind_'+kind.value);

	if (kind.checked){
		object.style.display = '';

		if (kind.value == '0'){
			show_pay_layer(__get_value(document.getElementsByName('yGupyeoKind')));
		}
	}else{
		if (kind.value == '0'){
			for(var i=1; i<=4; i++){
				document.getElementById('layer_'+i+'_1').style.display = 'none';
				document.getElementById('layer_'+i+'_2').style.display = 'none';
			}
		}

		object.style.display = 'none';
	}
}
//-->
</script>
<form name="f" method="post">
<div class="title"><?=$title;?></div>
<?
	if ($mode > 1){?>
		<table class="my_table my_border">
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
							<a href="#" onclick="serviceCalendarShow('<?=$code;?>', '<?=$kind;?>', '<?=subStr($next_ym, 0, 4);?>', '<?=subStr($next_ym, 4, 2);?>', '<?=$ed->en($jumin);?>', 'y', 'n', 'pdf');">보기</a><?
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
					<td class="center">-</td>
				</tr>
			</tbody>
		</table><?
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
					<input name="kind_list[]" type="checkbox" class="checkbox" value="<?=$k_list[$i]['code'];?>" onclick="set_kind(this);" <?=$checked;?>><?=$k_list[$i]['name'];
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="margin-top:-1px;">
	<colgroup>
		<col width="400px">
		<col width="430px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="left top bottom" style="padding:0;">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="50px">
						<col width="130px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th colspan="2">주민번호</th>
							<td class="last">
							<?
								if ($mode == 1){
							?>		<input name="yJumin1" type="text" value="" maxlength="6" class="phone" style="width:50px;" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.f.yJumin2.focus();}" onChange="_check_ssn('yoy', document.f.yJumin1, document.f.yJumin2, document.f.code);" onFocus="this.select();"> -
									<input name="yJumin2" type="text" value="" maxlength="7" class="phone" style="width:55px;" onKeyDown="__onlyNumber(this);" onkeyUp="if(this.value.length == 7){document.f.yName.focus();}"   onChange="_check_ssn('yoy', document.f.yJumin1, document.f.yJumin2, document.f.code);" onFocus="this.select();">
									<input name="jumin" type="hidden" value=""><?
								}else{?>
									<div class="left"><?=$myF->issStyle($jumin);?></div>
									<input name="jumin" type="hidden" value="<?=$ed->en($jumin);?>"><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">성명</th>
							<td class="last">
								<input name="yName" type="text" value="<?=$mst[$basic_kind]["m02_yname"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();" tag="성명을 입력하여 주십시오.">
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="2">전화<br>번호</th>
							<th>핸드폰</th>
							<td class="last">
								<input name="yTel" type="text" value="<?=getPhoneStyle($mst[$basic_kind]["m02_ytel"]);?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" onChange="checkHPno(this);" tag="핸드폰번호를 입력하여 주십시오.">
							</td>
						</tr>
						<tr>
							<th>유선</th>
							<td class="last">
								<input name="yTel2" type="text" value="<?=$mst[$basic_kind]["m02_ytel2"];?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);">
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">소재</th>
							<th>우편번호</th>
							<td class="last">
								<input name="yPostNo1" type="text" value="<?=substr($mst[$basic_kind]["m02_ypostno"],0,3);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"> -
								<input name="yPostNo2" type="text" value="<?=substr($mst[$basic_kind]["m02_ypostno"],3,6);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();">
								<span class="btn_pack small"><button type="button" onClick="__helpAddress(document.f.yPostNo1, document.f.yPostNo2, document.f.yJuso1, document.f.yJuso2);">찾기</button></span>
							</td>
						</tr>
						<tr>
							<th rowspan="2">주소</th>
							<td class="last">
								<input name="yJuso1" type="text" value="<?=$mst[$basic_kind]["m02_yjuso1"];?>" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();">
							</td>
						</tr>
						<tr>
							<td class="last">
								<input name="yJuso2" type="text" value="<?=$mst[$basic_kind]["m02_yjuso2"];?>" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();">
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">자격증</th>
							<th>자격종류</th>
							<td class="last">
								<select name="yJakukKind" style="width:auto;" onKeyDown="__enterFocus();">
								<?
									$sql = $conn->get_query("99");
									$conn->query($sql);
									$row2 = $conn->fetch();
									$row_count = $conn->row_count();

									for($i=0; $i<$row_count; $i++){
										$row2 = $conn->select_row($i);
									?>
										<option value="<?=$row2[0];?>"<? if($mst[$basic_kind]["m02_yjakuk_kind"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
									<?
									}

									$conn->row_free();
								?>
								</select>
							</td>
						</tr>
						<tr>
							<th>자격증번호</th>
							<td class="last">
								<input name="yJagyukNo" type="text" value="<?=$mst[$basic_kind]["m02_yjagyuk_no"];?>" maxlength="11" class="no_string" onKeyDown="__onlyNumber(this);" onFocus="this.select();">
							</td>
						</tr>
						<tr>
							<th>발급일자</th>
							<td class="last">
								<input name="yJakukDate" type="text" value="<?=$mst[$basic_kind]["m02_yjakuk_date"];?>" maxlength="11" class="date" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
							</td>
						</tr>
						<tr>
							<th colspan="2">직책</th>
							<td class="last">
								<select name="yJikJong" style="width:auto;" onKeyDown="__enterFocus();">
								<?
									$sql = $conn->get_query("98");
									$conn->query($sql);
									$row2 = $conn->fetch();
									$row_count = $conn->row_count();

									for($i=0; $i<$row_count; $i++){
										$row2 = $conn->select_row($i);
									?>
										<option value="<?=$row2[0];?>"<? if($mst[$basic_kind]["m02_yjikjong"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
									<?
									}

									$conn->row_free();
								?>
								</select>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="6">급여<br>공통<br>항목</th>
							<th>급여지급은행</th>
							<td class="last">
								<select name="yBankName" style="width:auto;">
								<?
									$bankList= $definition->GetBankList();
									$bankListCount = sizeOf($bankList);

									for($i=0; $i<$bankListCount; $i++){
									?>
										<option value="<?=$bankList[$i]['code'];?>" title="<?=$bankList[$i]['name'];?>" <? if($mst[$basic_kind]['m02_ybank_name'] == $bankList[$i]['code']){?>selected<?} ?>><?=$bankList[$i]['name'];?></option>
									<?
									}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<th>계좌번호</th>
							<td class="last">
								<input name="yGyeojaNo" type="text" value="<?=$mst[$basic_kind]["m02_ygyeoja_no"];?>" class="no_string" style="width:100%;" onKeyDown="__onlyNumber(this, '189 109');" onFocus="this.select();">
							</td>
						</tr>
						<tr>
							<th>4대보험 가입유무</th>
							<td class="last">
								<input name="y4BohumUmu" type="radio" class="radio" value="Y" checked>유
								<input name="y4BohumUmu" type="radio" class="radio" value="N">무
							</td>
						</tr>
						<tr>
							<th>공제대상 가족수</th>
							<td class="last">
								<input name="yGongJeJaNo" type="text" value="<?=$mst[$basic_kind]["m02_ygongjeja_no"];?>" maxlength="2" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == ''){this.value = '0';}">
							</td>
						</tr>
						<tr>
							<th>20세이하 자녀수</th>
							<td class="last">
								<input name="yGongJeJayeNo" type="text" value="<?=$mst[$basic_kind]["m02_ygongjejaye_no"];?>" maxlength="2" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == ''){this.value = '0';}">
							</td>
						</tr>
						<tr>
							<th>국민연금 신고 월급여액</th>
							<td class="last">
								<input name="yKuksinMpay" type="text" value="<?=number_format($mst[$basic_kind]["m02_ykuksin_mpay"]);?>" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);">
							</td>
						</tr>
						<tr>
							<th colspan="2">스마트폰 업무 구분</th>
							<td class="last">
								<input name="jikwonGbnM" type="checkbox" class="checkbox" value="Y" <? if($smart_gbn['M'] == 'Y'){?>checked<?} ?>>관리자
								<input name="jikwonGbnY" type="checkbox" class="checkbox" value="Y" <? if($smart_gbn['Y'] == 'Y'){?>checked<?} ?>>요양보호사
							</td>
						</tr>
					</tbody>

				</table>
			</td>

			<td class="left top bottom" style="padding:0;">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="20px">
						<col width="100px">
						<col width="110px">
						<col width="70px">
						<col>
					</colgroup>
					<tbody id="kind_0" style="display:<? if($mst[0]['m02_mkind'] != '0'){echo 'none';} ?>;">
						<tr>
							<th class="center" rowspan="<?=$row_span_count;?>">재<br>가</th>
							<th>고용형태</th>
							<td class="last" colspan="3">
								<input name="yGoyongKind" type="radio" class="radio" value="1" <? if($mst[0]["m02_ygoyong_kind"] == "1"){echo "checked";}?>><span style="margin-left:-5px;">정규직</span>
								<input name="yGoyongKind" type="radio" class="radio" value="2" style="margin-left:3px;" <? if($mst[0]["m02_ygoyong_kind"] == "2"){echo "checked";}?>><span style="margin-left:-5px;">계약직</span>
								<input name="yGoyongKind" type="radio" class="radio" value="3" style="margin-left:3px;" <? if($mst[0]["m02_ygoyong_kind"] == "3"){echo "checked";}?>><span style="margin-left:-5px;">시간직</span>
								<input name="yGoyongKind" type="radio" class="radio" value="4" style="margin-left:3px;" <? if($mst[0]["m02_ygoyong_kind"] == "4"){echo "checked";}?>><span style="margin-left:-5px;">기타</span>
								<input name="yGoyongKind" type="radio" class="radio" value="5" style="margin-left:3px;" <? if($mst[0]["m02_ygoyong_kind"] == "5"){echo "checked";}?>><span style="margin-left:-5px;">특수근로</span>
							</td>
						</tr>
						<tr>
							<th>고용상태</th>
							<td class="last" colspan="3">
								<input name="yGoyongStat" type="radio" class="radio" value="1" onclick="set_out_date(this.value);" <? if($mst[0]["m02_ygoyong_stat"] == "1"){echo "checked";}?>><span style="margin-left:-5px;">활동</span>
								<input name="yGoyongStat" type="radio" class="radio" value="2" onclick="set_out_date(this.value);" <? if($mst[0]["m02_ygoyong_stat"] == "2"){echo "checked";}?>><span style="margin-left:-5px;">휴직</span>
								<input name="yGoyongStat" type="radio" class="radio" value="9" onclick="set_out_date(this.value);" <? if($mst[0]["m02_ygoyong_stat"] == "9"){echo "checked";}?>><span style="margin-left:-5px;">퇴사</span>
							</td>
						</tr>
						<tr>
							<th>입사일자</th>
							<td>
								<input name="yIpsail" type="text" value="<?=$myF->dateStyle($mst[0]["m02_yipsail"]);?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" tag="입사일자를 입력하여 주십시오.">
							</td>
							<th>퇴사일자</th>
							<td class="last">
								<input name="yToisail" type="text" value="<?=$myF->dateStyle($mst[0]["m02_ytoisail"]);?>" maxlength="8" class="date" tag="<?=$myF->dateStyle($mst[0]["m02_ytoisail"]);?>" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
							</td>
						</tr>
						<tr>
							<th>근무가능요일</th>
							<td class="last" colspan="3">
								<input name="yGunmuMon" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_mon"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">월</font>
								<input name="yGunmuTue" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_tue"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">화</font>
								<input name="yGunmuWed" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_wed"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">수</font>
								<input name="yGunmuThu" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_thu"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">목</font>
								<input name="yGunmuFri" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_fri"] == "Y"){echo "checked";}?>><font style="font-weight:bold;">금</font>
								<input name="yGunmuSat" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_sat"] == "Y"){echo "checked";}?>><font style="font-weight:bold; color:#0000ff;">토</font>
								<input name="yGunmuSun" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_sun"] == "Y"){echo "checked";}?>><font style="font-weight:bold; color:#ff0000;">일</font>
							</td>
						</tr>
						<tr>
							<th>급여산정방식</th>
							<td class="last" colspan="3">
								<input name="yGupyeoKind" type="radio" class="radio" value="1Y" onclick="show_pay_layer(this.value);" <? if($pay_type == "1"){echo "checked";}?>><span style="margin-left:-5px;">시급(고정)</span>
								<input name="yGupyeoKind" type="radio" class="radio" value="1N" onclick="show_pay_layer(this.value);" <? if($pay_type == "2"){echo "checked";}?>><span style="margin-left:-5px;">시급(변동)</span>
								<input name="yGupyeoKind" type="radio" class="radio" value="3"  onclick="show_pay_layer(this.value);" <? if($pay_type == "3"){echo "checked";}?>><span style="margin-left:-5px;">총액비율</span>
								<input name="yGupyeoKind" type="radio" class="radio" value="4"  onclick="show_pay_layer(this.value);" <? if($pay_type == "4"){echo "checked";}?>><span style="margin-left:-5px;">월급</span>
							</td>
						</tr>
						<tr>
							<th id="tbody_1_1">시급(고정급)</th>
							<td id="tbody_1_2" class="last" colspan="3">
								<input name="yGibonKup1" type="text" value="<?=number_format($hourly_1);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="고정시급을 입력하여 주십시오.">
							</td>
						</tr>
						<tr>
							<th id="tbody_2_1">시급(변동급)</th>
							<td id="tbody_2_2" class="last" colspan="3">
								<table class="my_table" style="width:100%;">
									<colgroup>
										<col width="25%" span="4">
									</colgroup>
									<tbody>
										<tr>
											<th>1등급</th>
											<td>
												<input name="yGibonKup[]" type="text" value="<?=number_format($hourly_2[1]);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(1등급)을 입력하여 주십시오.">
												<input name="yGibonKupCode[]" type="hidden" value="1">
											</td>
											<th>2등급</th>
											<td class="last">
												<input name="yGibonKup[]" type="text" value="<?=number_format($hourly_2[2]);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(2등급)을 입력하여 주십시오.">
												<input name="yGibonKupCode[]" type="hidden" value="2">
											</td>
										</tr>
										<tr>
											<th class="bottom">3등급</th>
											<td class="bottom">
												<input name="yGibonKup[]" type="text" value="<?=number_format($hourly_2[3]);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(3등급)을 입력하여 주십시오.">
												<input name="yGibonKupCode[]" type="hidden" value="3">
											</td>
											<th class="bottom">일반</th>
											<td class="bottom last">
												<input name="yGibonKup[]" type="text" value="<?=number_format($hourly_2[9]);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="시급(일반)을 입력하여 주십시오.">
												<input name="yGibonKupCode[]" type="hidden" value="9">
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<th id="tbody_3_1">수가총액비율</th>
							<td id="tbody_3_2" class="last" colspan="3">
								<input name="ySugaYoyul" type="text" value="<?=$hourly_4;?>" maxlength="3" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == ''){this.value = '0';}" tag="수가총액비율을 입력하여 주십시오.">%
							</td>
						</tr>
						<tr>
							<th id="tbody_4_1">기본급</th>
							<td id="tbody_4_2" class="last" colspan="3">
								<input name="yGibonKup3" type="text" value="<?=number_format($hourly_3);?>" maxlength="8" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" tag="기본급을 입력하여 주십시오.">
							</td>
						</tr>
						<tr>
							<th>동거가족케어유무</th>
							<td>
								<input name="yFamCareUmu" type="radio" class="radio" value="Y" onclick="_family_care_yn();" <? if($mst[0]["m02_yfamcare_umu"] == "Y"){echo "checked";}?>><span style="margin-left:-5px;">유</span>
								<input name="yFamCareUmu" type="radio" class="radio" value="N" onclick="_family_care_yn();" <? if($mst[0]["m02_yfamcare_umu"] == "N"){echo "checked";}?>><span style="margin-left:-5px;">무</span>
							</td>
							<th>시급</th>
							<td class="last">
								<input name="yFamCarePay" type="text" value="<?=number_format($mst[0]['m02_yfamcare_pay'])?>" tag="<?=number_format($mst[0]['m02_yfamcare_pay'])?>" maxlength="8" class="number" style="<?=$mst[0]["m02_yfamcare_umu"] != 'Y' ? 'background-color:#eee;' : '';?>" onKeyDown="__onlyNumber(this);" onFocus="<?=$mst[0]["m02_yfamcare_umu"] == 'Y' ? '__commaUnset(this);' : 'this.blur();';?>" onBlur="if(this.value==''){this.value='0';}else{__commaSet(this);}" <? if($mst[0]["m02_yfamcare_umu"] != 'Y'){?>readOnly<?} ?> tag="시급(동거가족)을 입력하여 주십시오.">
							</td>
						</tr>
						<?
							if ($subject_count > 0){?>
								<tr>
									<th rowspan="">각종수당<br>/공제처리</th>
									<td class="last" colspan="3">
										<table class="my_table" style="width:100%;">
											<colgroup>
												<col width="25%" span="4">
											</colgroup>
											<tbody>
											<?
												$tr = false;

												if ($subject_count > 0){
													for($i=0; $i<$subject_count; $i++){
														$pay = $my_subject[$i];

														if ($i == $subject_count - 1){
															$class_bottom = 'bottom';
														}else{
															$class_bottom = '';
														}

														if ($i % 2 == 0){
															if ($tr == true){
																echo '</tr>';
															}
															echo '<tr>';
															$tr = true;
															$class_last = '';
														}else{
															$class_last = 'last';
														}

														/*
														$payString = "";

														if ($pay["t20_kind1"] == "1"){
															if ($pay["t20_kind2"] == "1"){
																$payString = "(과세)";
															}else if ($pay["t20_kind2"] == "2"){
																$payString = "(비과세)";
															}
														}else{
															$payString = "(공제)";
														}
														*/

														$my_fix_amount = 0;
														for($j=0; $j<sizeOf($my_fix_pay); $j++){
															if ($my_fix_pay[$j]['code'] == $pay["t20_kind1"].'_'.$pay["t20_kind2"].'_'.$pay["t20_code"]){
																$my_fix_amount = number_format($my_fix_pay[$j]['amount']);
																break;
															}
														}

														echo '
															<th class="'.$class_bottom.'">'.$pay["t20_name"].$payString.'</th>
															<td class="'.$class_bottom.' '.$class_last.'"><input name="pay_'.$pay["t20_kind1"].'_'.$pay["t20_kind2"].'_'.$pay["t20_code"].'" type="text" value="'.$my_fix_amount.'" maxlength="8" class="number" style="width:100%;" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == \'\'){this.value = \'0\';}"></td>
															 ';
													}
													if ($subject_count % 2 != 0){
														echo '
															<th class="bottom"></th>
															<td class="bottom last"></td>
															 ';
													}
													echo '</tr>';
												}
											?>
											</tbody>
										</table>
									</td>
								</tr><?
							}
						?>

						<tr>
							<th rowspan="2">배상책임보험</th>
							<th>가입여부</th>
							<td class="last" colspan="2">
								<input name="insYN" type="radio" class="radio" value="Y" onclick="_ins_join_yn(this.value, '<?=$insYN;?>', 'insFromDate', 'insToDate');" <? if($insYN == "Y"){echo "checked";}?>><span style="margin-left:-5px;">유</span>
								<input name="insYN" type="radio" class="radio" value="N" onclick="_ins_join_yn(this.value, '<?=$insYN;?>', 'insFromDate', 'insToDate');" <? if($insYN == "N"){echo "checked";}?>><span style="margin-left:-5px;">무</span>
							</td>
						</tr>
						<tr>
							<th>가입기간</th>
							<td class="last" colspan="2">
								<input name="insFromDate" type="text" value="<? if($insYN == 'Y'){echo $myF->dateStyle($insFromDate);} ?>" tag="<?=$myF->dateStyle($insFromDate);?>" alt="_checkInsLimitDate" maxlength="8" class="date" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" onChange="_checkInsLimitDate();"> ~
								<input name="insToDate"	  type="text" value="<? if($insYN == 'Y'){echo $myF->dateStyle($insToDate);}   ?>" tag="<?=$myF->dateStyle($insToDate);?>"   alt="_checkInsLimitDate" maxlength="8" class="date" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" onChange="_checkInsLimitDate();">
							</td>
						</tr>
					</tbody>

					<tbody id="kind_1" style="display:<? if($mst[1]['m02_mkind'] != '1'){echo 'none';} ?>;">
						<tr>
							<th colspan="2">가사간병</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>

					<tbody id="kind_2" style="display:<? if($mst[2]['m02_mkind'] != '2'){echo 'none';} ?>;">
						<tr>
							<th colspan="2">노인돌봄</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>

					<tbody id="kind_3" style="display:<? if($mst[3]['m02_mkind'] != '3'){echo 'none';} ?>;">
						<tr>
							<th colspan="2">산모신생아</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>

					<tbody id="kind_4" style="display:<? if($mst[4]['m02_mkind'] != '4'){echo 'none';} ?>;">
						<tr>
							<th colspan="2">장애인보조</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>

					<tbody id="kind_5" style="display:<? if($mst[5]['m02_mkind'] != '5'){echo 'none';} ?>;">
						<tr>
							<th colspan="2">시설</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>
				</table>
			</td>

			<td class="other"></td>
		</tr>
	</tbody>
</table>
<input name="page"	type="hidden" value="<?=$page;?>">
<input name="code"	type="hidden" value="<?=$code;?>">
<input name="kind"	type="hidden" value="<?=$kind;?>">
<input name="mode"	type="hidden" value="<?=$mode;?>">

<input name="ins_no"	type="hidden" value="<?=$mst[$kind]['m02_ins_no'];?>">
<input name="ins_code"	type="hidden" value="<?=$insCode;?>">

<input name="centerInsFromDate" type="hidden" value="<?=$myF->dateStyle($ins[1]);?>">
<input name="centerInsToDate" type="hidden" value="<?=$myF->dateStyle($ins[2]);?>">
</form>

<div style="width:100%; margin:0; padding:0; text-align:right; margin:5px;"><?=$button;?></div>

<div id="layer_1_1" style="position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>
<div id="layer_1_2" style="position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>
<div id="layer_2_1" style="position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>
<div id="layer_2_2" style="position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>
<div id="layer_3_1" style="position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>
<div id="layer_3_2" style="position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>
<div id="layer_4_1" style="position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>
<div id="layer_4_2" style="position:absolute; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; display:none;"></div>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
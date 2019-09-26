<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	/*
	 * 기능		: 수급자 등록 / 수정
	 * 작성자	: 김재용
	 * 일자		: 2011.03.21
	 *
	 * 관련파일
	 * - inc/_su_check_ssn.php
	 * - js/center.js
	 */

	$find_center_code   = $_POST['find_center_code'];
	$find_center_name   = $_POST['find_center_name'];
	$find_su_name		= $_POST['find_su_name'];
	$find_su_phone		= $_POST['find_su_phone'];
	$find_su_stat       = $_POST['find_su_stat'];

	$page	= $_REQUEST['page'];

	$code	= $_REQUEST['code'] != '' ? $_REQUEST['code'] : $_SESSION["userCenterCode"];			//기관번호
	$kind	= $conn->get_data("select min(m00_mkind) from m00center where m00_mcode = '$code'");//기관분류
	$jumin	= $ed->de($_REQUEST['jumin']);

	// 기관분류 리스트
	$k_list		 = $conn->kind_list($code);
	$center_code = $conn->center_code($code, $kind);
	$center_name = $conn->center_name($code, $kind);

	// 최초 일정등록일
	$sql = "select min(t01_sugup_date)
			  from t01iljung
			 where t01_ccode  = '$code'
			   and t01_mkind  = '$kind'
			   and t01_jumin  = '$jumin'
			   and t01_del_yn = 'N'";
	$first_iljung_date = $conn->get_data($sql);

	if ($first_iljung_date == '') $first_iljung_date = '99999999';

	$sql = "select *
			  from m03sugupja
			 where m03_ccode = '$code'
			   and m03_mkind = '$kind'
			   and m03_jumin = '$jumin'";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$mst[$row['m03_mkind']] = $row;
	}

	$conn->row_free();

	if (!is_array($mst)){
		$mode = 1;
	}else{
		$mode = 2;
	}

	switch($mode){
	case 1:
		$title								= '수급자등록';
		$mst[$kind]['m03_mkind']			= $kind;
		$mst[$kind]["m03_gaeyak_fm"]		= date('Ym', mktime()).'01';
		$mst[$kind]["m03_gaeyak_to"]		= '99999999';
		$mst[0]['m03_sugup_status']			= '1';
		$mst[0]['m03_ylvl']					= '1';
		$mst[0]['m03_skind']				= '1';
		$mst[0]['m03_bonin_yul']			= '15.0';
		$sDate								= $mst[$kind]["m03_gaeyak_fm"];
		$eDate								= '99999999';
		break;
	case 2:
		$title		 = '수급자정보수정';
		$sDate		 = $mst[$kind]["m03_sdate"];
		$eDate		 = $mst[$kind]["m03_edate"];
		$client_stat = $mst[$kind]['m03_sugup_status'];
		break;
	}

	$button  = '';
	$button .= "<span class='btn_pack m icon'><span class='list'></span><button type='button' onFocus='this.blur();' onClick='_list_client($page);'>리스트</button></span> ";

	if ($mode == 1){
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_save_client();'>등록</button></span> ";
	}else{
		$button .= "<span class='btn_pack m icon'><span class='download'></span><button type='button' onFocus='this.blur();' onClick='_save_client();'>수정</button></span> ";

		if ($_SESSION["userLevel"] == 'A'){
			$button .= "<span class='btn_pack m icon'><span class='delete'></span><button type='button' onFocus='this.blur();' onClick=''>삭제</button></span>";
		}
	}
	/*
	if ($mode > 1){
		// 초기상담 기록지
		$sql = "select count(*)
				  from r200fsttalk
				 where r200_ccode = '$code'
				   and r200_mkind = '$kind'
				   and r200_jumin = '$jumin'";
		$first_talk_count = $conn->get_data($sql);

		// 욕구평가 기록지
		$sql = "select count(*)
				  from r210nar
				 where r210_ccode = '$code'
				   and r210_mkind = '$kind'
				   and r210_sugup_code = '$jumin'";
		$nar_count = $conn->get_data($sql);

		// 욕창위험도
		$sql = "select count(*)
				  from r220purat
				 where r220_ccode = '$code'
				   and r220_mkind = '$kind'
				   and r220_sugupCode = '$jumin'";
		$purat_count = $conn->get_data($sql);


		// 낙상위험도
		$sql = "select count(*)
				  from r250risktoll
				 where r250_ccode = '$code'
				   and r250_mkind = '$kind'
				   and r250_sugupja_jumin = '$jumin'";
		$risktoll_count = $conn->get_data($sql);

		// 익월 서비스 일정표
		$next_ym = date("Ym",strtotime("+1 month"));
		$sql = "select count(*)
				  from t01iljung
				 where t01_ccode  = '$code'
				   and t01_mkind  = '$kind'
				   and t01_sugup_date like '$next_ym%'
				   and t01_jumin  = '$jumin'
				   and t01_del_yn = 'N'";
		$iljung_count = $conn->get_data($sql);

		// 전월 본인부담 영수증 발행
		$before_ym = date("Ym",strtotime("-1 month"));
		$sql = "select count(*)
				  from t13sugupja
		   	     where t13_ccode    = '$code'
				   and t13_mkind    = '$kind'
				   and t13_pay_date = '$before_ym'
				   and t13_jumin    = '$jumin'
				   and t13_type     = '2'";
		$bill_count = $conn->get_data($sql);

		// 당월 실적 확정처리
		$now_ym = date("Ym",mktime());
		$sql = "select count(*)
				  from t13sugupja
				 where t13_ccode    = '$code'
				   and t13_mkind    = '$kind'
				   and t13_pay_date = '$now_ym%'
				   and t13_jumin    = '$jumin'
				   and t13_type     = '2'";
		$conf_count = $conn->get_data($sql);

		// 미수금현황
		$sql = "select sum(t13_misu_amt - t13_misu_inamt) as misu_pay
				  from t13sugupja
				 where t13_ccode = '$code'
				   and t13_mkind = '$kind'
			       and t13_jumin = '$jumin'
			       and t13_type  = '2'";
		$misu_count = $conn->get_data($sql);

		// 만족도조사분기별
		$sql = "select sum(case when r360_service_gbn = '200' then 1 else 0 end)
				,      sum(case when r360_service_gbn = '500' then 1 else 0 end)
				,      sum(case when r360_service_gbn = '800' then 1 else 0 end)
				  from r360quest
				 where r360_ccode = '$code'
				   and r360_mkind = '$kind'
				   and r360_sugupja = '$jumin'";
		$quest_count = $conn->get_array($sql);
	}
	*/
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
	var mode = document.f.mode.value;

	if (mode > 1){
		modify_my_rate();
	}

	__init_form(document.f);
	_client_cont_date(__get_value(document.f.sugupStatus),'gaeYakFm','gaeYakTo');

	_set_max_pay(document.f.kupyeoMax, __get_value(document.f.yLvl));

	if (document.f.boninYul.value == ''){
		_set_my_yul(__get_value(document.f.sKind), __get_tag(document.f.sKind));
	}

	if (mode > 1){
		modify_sugup_note();
	}

	/*
	var byungMung = null;
	var tmpByungMung = document.getElementsByName('byungMung');

	for(var i=0; i<tmpByungMung.length; i++){
		if (tmpByungMung[i].checked){
			byungMung = tmpByungMung[i];
			break;
		}
	}

	if (byungMung != null){
		_setDisabled(byungMung, document.getElementById('diseaseNm'));
	}
	*/
	set_dis_nm();
}

function set_dis_nm(){
	var f = document.f;
	var gbn = __object_get_value('byungMung');

	if (gbn == '1'){
		f.diseaseNm.style.display = 'none';
		f.stat_nogood.style.display = '';
		lvl_stat_nogood.style.display = '';
	}else{
		f.diseaseNm.style.display = '';
		f.stat_nogood.style.display = 'none';
		lvl_stat_nogood.style.display = 'none';

		if (gbn == '9'){
			__setEnabled('diseaseNm', true);
		}else{
			__setEnabled('diseaseNm', false);
		}
	}
}

function modify_sugup_note_check(){
	var check = document.getElementById('edit_mode_select');

	check.checked = !check.checked;
	modify_sugup_note();
}

function modify_sugup_note(){
	var body_layer_0 = document.getElementById('layer_1');
	var check        = document.getElementById('edit_mode_select');

	if (!check.checked){
		if (_client_check_sugup_data(document.f)){
			if (confirm('수급내용중 변경된 내용있습니다. 수급내용 변경을 취소하시면 변경된 내용이 이전 상태로 되돌아갑니다.\n변경을 취소하시겠습니까?')){
				_client_sugup_data_resotre(document.f);
			}else{
				check.checked = true;
				return;
			}
		}
	}

	_show_tbody_layer('kind_0', 'layer_1', check.checked);
}

function modify_my_rate(){
	var my_kind = __get_value(document.getElementsByName('sKind'));
	var my_rate = document.getElementById('boninYul');

	if (my_kind == 2 || my_kind == 4){
		my_rate.readOnly = false;
	}
}

function restore(){
	if (!confirm('데이타를 복원하시겠습니까?')) return;

	var f = document.f;

	f.action = 'his_restore.php';
	f.submit();
}
//-->
</script>
<form name="f" method="post">
<div class="title"><?=$title;?></div>

<?
	//if ($mode > 1){?>
		<!--table class="my_table my_border">
			<colgroup>
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
			</colgroup>
			<thead>
				<tr>
					<th class="head" rowspan="2">급여<br>계약서</th>
					<th class="head" rowspan="2">초기상담<br>기록지</th>
					<th class="head" colspan="3">평가</th>
					<th class="head" rowspan="2">익월<br>서비스<br>일정표</th>
					<th class="head" rowspan="2">전월<br>본인부담<br>영수증</th>
					<th class="head" rowspan="2">당월실적<br>확정처리</th>
					<th class="head" rowspan="2">미수금<br>현황</th>
					<th class="head last" colspan="3">만족도조사(분기별)</th>
				</tr>
				<tr>
					<th class="head">욕구</th>
					<th class="head">욕창위험도</th>
					<th class="head">낙상위험도</th>
					<th class="head">방문요양</th>
					<th class="head">방문목욕</th>
					<th class="head last">방문간호</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="center"><a href="#" onclick="showReport(70, '<?=$code;?>', '<?=$kind;?>', '', '<?=$mst[$kind]['m03_key'];?>', '')">출력</a></td>
					<td class="center" id="talk_body">
					<?
						if ($first_talk_count > 0){?>
							<a href="#" onclick="_member_report_layer('talk_body', '31', '<?=$code;?>', '<?=$kind;?>', '<?=$ed->en($jumin);?>');">작성(完)</a><?
						}else{
							if ($client_stat == '1'){?>
								<a href="#" onclick="__my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','31','php','1','2'), 'talk_body', '31', 'code', 'kind', 'jumin');">미작성</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center" id="nar_body">
					<?
						if ($nar_count > 0){?>
							<a href="#" onclick="_member_report_layer('nar_body', '37', '<?=$code;?>', '<?=$kind;?>', '<?=$ed->en($jumin);?>');">작성(完)</a><?
						}else{
							if ($client_stat == '1'){?>
								<a href="#" onclick="__my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','37','php','1','2'), 'nar_body', '37', 'code', 'kind', 'jumin');">미작성</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center" id="purat_body">
					<?
						if ($purat_count > 0){?>
							<a href="#" onclick="_member_report_layer('purat_body', '41', '<?=$code;?>', '<?=$kind;?>', '<?=$ed->en($jumin);?>');">작성(完)</a><?
						}else{
							if ($client_stat == '1'){?>
								<a href="#" onclick="__my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','41','php','1','2'), 'purat_body', '41', 'code', 'kind', 'jumin');">미작성</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center" id="risktoll_body">
					<?
						if ($risktoll_count > 0){?>
							<a href="#" onclick="_member_report_layer('risktoll_body', '81', '<?=$code;?>', '<?=$kind;?>', '<?=$ed->en($jumin);?>');">작성(完)</a><?
						}else{
							if ($client_stat == '1'){?>
								<a href="#" onclick="__my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','81','php','1','2'), 'risktoll_body', '81', 'code', 'kind', 'jumin');">미작성</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center" id="iljung_body">
					<?
						if ($iljung_count > 0){?>
							<a href="#" onclick="serviceCalendarShow('<?=$code;?>', '<?=$kind;?>', '<?=subStr($next_ym, 0, 4);?>', '<?=subStr($next_ym, 4, 2);?>', '<?=$ed->en($jumin);?>', 's', 'n', 'pdf', 'y', 'y');">보기</a><?
						}else{
							if ($client_stat == '1'){?>
								<a href="#" onclick="window.open('../iljung/iljung.php?menuIndex=work&menuSeq=1&manager=true&gubun=reg', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');">등록</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center">-</td>
					<td class="center">-</td>
					<td class="center">-</td>
					<td class="center" id="quest_200_body">
					<?
						if ($quest_count[0] > 0){?>
							<a href="#" onclick="_member_report_layer('quest_200_body', '40', '<?=$code;?>', '<?=$kind;?>', '<?=$ed->en($jumin);?>', '200');">작성(完)</a><?
						}else{
							if ($client_stat == '1'){?>
								<a href="#" onclick="__my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','40','php','1','2'), 'quest_200_body', '40', 'code', 'kind', 'jumin','200');">미작성</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center" id="quest_500_body">
					<?
						if ($quest_count[1] > 0){?>
							<a href="#" onclick="_member_report_layer('quest_500_body', '74', '<?=$code;?>', '<?=$kind;?>', '<?=$ed->en($jumin);?>', '500');">작성(完)</a><?
						}else{
							if ($client_stat == '1'){?>
								<a href="#" onclick="__my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','74','php','1','2'), 'quest_500_body', '74', 'code', 'kind', 'jumin','500');">미작성</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
					<td class="center last" id="quest_800_body">
					<?
						if ($quest_count[2] > 0){?>
							<a href="#" onclick="_member_report_layer('quest_800_body', '75', '<?=$code;?>', '<?=$kind;?>', '<?=$ed->en($jumin);?>', '800');">작성(完)</a><?
						}else{
							if ($client_stat == '1'){?>
								<a href="#" onclick="__my_modal(Array('<?=$kind?>','','<?=$ed->en($jumin);?>','','report','input','75','php','1','2'), 'quest_800_body', '75', 'code', 'kind', 'jumin','800');">미작성</a><?
							}else{?>
								<span>-</span><?
							}
						}
					?>
					</td>
				</tr>
			</tbody>
		</table--><?
	//}
?>

<table class="my_table my_border" style="<? if($mode > 1){?>margin-top:-1px;<?} ?>">
	<colgroup>
		<col width="100px">
		<col width="130px">
		<col width="100px">
		<col>
		<col width="100px">
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$code;?></td>
			<th>기관명</th>
			<td class="left last" colspan="2"><?=$center_name;?></td>
		</tr>
		<tr>
			<th>기관구분</th>
			<td class="left" colspan="3">
			<?
				for($i=0; $i<sizeof($k_list); $i++){
					if ($mst[$i]['m03_mkind'] == $k_list[$i]['code']){
						$checked = 'checked';
					}else{
						$checked = '';
					}?>
					<input name="kind_temp[]" type="hidden" value="<?=$k_list[$i]['code'];?>">
					<input name="kind_list[]" type="checkbox" class="checkbox" value="<?=$k_list[$i]['code'];?>" onclick="_set_kind(this);" <?=$checked;?>><?=$k_list[$i]['name'];
				}
			?>
			</td>
			<td class="right last">
			<?
				if ($mode > 1){?>
					<input name="edit_mode_select" type="checkbox" class="checkbox" onclick="modify_sugup_note();"><a href="#" onclick="modify_sugup_note_check();">수급내용수정<a><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('reg_sub.php');
?>
<input name="find_center_code"	type="hidden" value="<?=$find_center_code;?>">
<input name="find_center_name"	type="hidden" value="<?=$find_center_name;?>">
<input name="find_su_name"		type="hidden" value="<?=$find_su_name;?>">
<input name="find_su_phone"		type="hidden" value="<?=$find_su_phone;?>">
<input name="find_su_stat"		type="hidden" value="<?=$find_su_stat;?>">
<input name="code"				type="hidden" value="<?=$code;?>">
<input name="kind"				type="hidden" value="<?=$kind;?>">
<input name="page"				type="hidden" value="<?=$page;?>">
<input name="mode"				type="hidden" value="<?=$mode;?>">
<input name="history_yn"		type="hidden" value="Y">
<input name="sDate"				type="hidden" value="<?=$sDate;?>" tag="<?=$sDate;?>">
<input name="startDate"			type="hidden" value="<?=$sDate;?>" tag="<?=$sDate;?>">
<input name="first_iljung_date"	type="hidden" value="<?=$first_iljung_date;?>">
<input name="mem_change_dt"		type="hidden" value="<?=$sDate;?>">

</form>

<div style="width:100%; margin:0; padding:0; text-align:right; margin:5px;"><?=$button;?></div>

<div class="title">수급자 변동내역</div>

<table class="my_table my_border" style="margin-top:-1px; margin-bottom:10px;">
	<colgroup>
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="200px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head">등급</th>
			<th class="head">수급자구분</th>
			<th class="head">수급현황</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select m31_sdate
				,      m31_edate
				,      LVL.m81_name as levelName
				,      STP.m81_name as kindName
				,      m31_bonin_yul
				,      m31_status
				,      m31_gaeyak_fm
				,      m31_gaeyak_to
				  from m31sugupja
				 inner join m81gubun as LVL
					on LVL.m81_gbn = 'LVL'
				   and LVL.m81_code = m31_level
				 inner join m81gubun as STP
					on STP.m81_gbn = 'STP'
				   and STP.m81_code = m31_kind
				 where m31_ccode = '$code'
				   and m31_mkind = '$kind'
				   and m31_jumin = '$jumin'
				 order by m31_sdate desc, m31_edate desc";
		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		if ($rowCount > 0){
			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);?>
				<tr>
					<td class="center"><?=$myF->dateStyle($row['m31_sdate'],'.');?></td>
					<td class="center"><?=$myF->dateStyle($row['m31_edate'],'.');?></td>
					<td class="center"><?=$row['levelName'];?></td>
					<td class="left"><?=$row['kindName'].'('.$row['m31_bonin_yul'].')';?></td>
					<td class="center"><?=$definition->SugupjaStatusGbn($row['m31_status']);?></td>
					<td class="other left top" rowspan="<?=$rowCOunt;?>" style="padding-top:5px;"><?
						if ($i == 0){?>
							<img src="../image/btn_restore.png" style="cursor:pointer;" onclick="restore();"><?
						}?>
					</td>
				</tr><?
			}
		}else{
			echo '<tr><td class="center last" colspan="7">::검색된 데이타가 없습니다.::</td></tr>';
		}
		$conn->row_free();
	?>
	</tbody>
</table>

<div id="sugupLayer1" style="z-index:1000000; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; position:absolute; top:0px; left:0px; height:0px; width:0px;"></div>
<div id="sugupLayer2" style="z-index:1000001; left:0; top:0; position:absolute; color:#000000;">
	<table id="sugupTable" style="width:400px; height:60px; background-color:#ffffff; display:none;">
	<tr>
	<td class="noborder" style="width:10px;"></td>
	<td class="title" style="width:400px;">적용일자 안내</td>
	</tr>
	<tr>
	<td class="noborder" style="width:10px;"></td>
	<td class="title" style="width:400px; padding-top:5px; padding-bottom:5px; line-height:1.2em;">
		장기요양등급, 수급자구분, 계약시작일 및 종료일, 수급현황, 담당요양보호사가 변경된 경우 적용기준일자를 입력하여 주십시오.
	</td>
	</tr>
	<tr>
	<td class="noborder" colspan="2">
		<form name="sugupCheck" method="post">
		<table style="width:100%;">
		<tr>
		<td style="width:20%; border:0px; border-bottom:1px dotted #cccccc; text-align:center; padding-left:15px;">적용 기준일</td>
		<td style="width:30%; border:0px; border-bottom:1px dotted #cccccc; text-align:center;"><?=$myF->dateStyle($sDate,'.');?></td>
		<td style="width:20%; border:0px; border-bottom:1px dotted #cccccc; text-align:center; padding-left:15px;">적용 시작일</td>
		<td style="width:30%; border:0px; border-bottom:1px dotted #cccccc; text-align:center;"><input name="startDate" type="text" value="<?=subStr($sDate, 0, 4).'.'.subStr($sDate, 4, 2).'.'.subStr($sDate, 6, 2);?>" tag="<?=$sDate;?>" maxlength="8" class="phone" onFocus="__toNumber(this); this.select();" onBlur="__setDate(this);" onKeyDown="__onlyNumber(this);"></td>
		<!--td style="width:30%; border:0px; border-bottom:1px dotted #cccccc; text-align:center; dispaly:none;" id="endDate">9999.99.99</td-->
		</tr>
		<tr>
		<td style="border:0px; padding-left:5px;" colspan="4">
			<a href="#" onClick="_show_save_client();"><img src="../image/btn9.gif"></a>
			<a href="#" onClick="_show_cancel_client();"><img src="../image/btn_cancel.png"></a>
		</td>
		</tr>
		</table>
		</form>
	</td>
	</tr>
	</table>
</div>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
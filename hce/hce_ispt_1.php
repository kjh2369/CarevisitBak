<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	/*********************************************************
	 *	사정기록지 - 기본
	 *********************************************************/
	//관계
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'HR\'
			AND		use_yn	= \'Y\'';
	$arrRel = $conn->_fetch_array($sql,'code');
	//사례접수 및 초기면접 내용
	$sql = 'SELECT	mst.m03_name AS name
			,		mst.m03_jumin AS jumin
			,		EL.name AS edu_gbn
			,		rcpt.addr
			,		rcpt.addr_dtl
			,		rcpt.phone
			,		rcpt.mobile
			,		rcpt.grd_tel
			,		rcpt.grd_rel
			,		rcpt.marry_gbn
			,		rcpt.cohabit_gbn
			,		iv.income_gbn
			,		iv.income_other
			,		iv.dwelling_gbn
			,		iv.dwelling_other
			,		iv.deposit_amt
			,		iv.rental_amt
			,		iv.house_gbn
			,		iv.house_other
			,		iv.health_gbn
			,		iv.health_other
			,		iv.disease_gbn
			,		iv.handicap_gbn
			,		iv.handicap_other
			,		iv.device_gbn
			,		iv.device_other
			,		iv.longlvl_gbn
			,		iv.longlvl_other
			FROM	hce_receipt AS rcpt
			INNER	JOIN	m03sugupja AS mst
					ON		mst.m03_ccode	= rcpt.org_no
					AND		mst.m03_mkind	= \'6\'
					AND		mst.m03_key		= rcpt.IPIN
			LEFT	JOIN	hce_interview AS iv
					ON		iv.org_no	= rcpt.org_no
					AND		iv.org_type = rcpt.org_type
					AND		iv.IPIN		= rcpt.IPIN
					AND		iv.rcpt_seq = rcpt.rcpt_seq
			LEFT	JOIN	hce_gbn AS EL
					ON		EL.type = \'EL\'
					AND		EL.code = rcpt.edu_gbn
			WHERE	rcpt.org_no		= \''.$orgNo.'\'
			AND		rcpt.org_type	= \''.$hce->SR.'\'
			AND		rcpt.IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt.rcpt_seq	= \''.$hce->rcpt.'\'';
	$row = $conn->get_array($sql);
	//주민번호
	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$row['jumin'].'\'';
	$row['jumin'] = $conn->get_data($sql);
	$row['jumin'] = SubStr($row['jumin'].'0000000',0,13);
	$name	= $row['name'];	//성명
	$gender = $myF->issToGender($row['jumin']);	//성별
	$age	= $myF->issToAge($row['jumin']);	//연령
	$jumin	= $myF->issStyle($row['jumin']);	//주민번호
	$eduGbn	= $row['edu_gbn'];	//학력
	$addr	= $row['addr'].' '.$row['addr_dtl'];	//주소
	$phone	= $myF->phoneStyle($row['phone'],'.');	//연락처
	$mobile	= $myF->phoneStyle($row['mobile'],'.');	//핸드폰
	$grdTel	= $myF->phoneStyle($row['grd_tel'],'.');	//비상연락처
	$grdRel	= $arrRel[$row['grd_rel']]['name'];	//관계
	$marry	= $row['marry_gbn'];	//결혼
	$cohabit= $row['cohabit_gbn'];	//동거
	$income		= $row['income_gbn'];	//보호형태
	$incomeOther= $row['income_other'];
	$dwelling		= $row['dwelling_gbn'];	//주거형태
	$dwellingOther	= $row['dwelling_other'];
	$depositAmt		= $row['deposit_amt'];	//보증금
	$rentalAmt		= $row['rental_amt'];	//월세
	$house		= $row['house_gbn'];	//주택형태
	$houseOther	= $row['house_other'];
	$health		= $row['health_gbn'];	//건강상태
	$healthOther= $row['health_other'];
	//만성질환
	$tmp= $row['disease_gbn'];
	$tmp= Explode('/',$tmp);
	foreach($tmp as $var){
		$var = Explode(':',$var);
		$disease[$var[0]] = $var[1];
	}
	$handicap		= $row['handicap_gbn'];	//장애여부
	$handicapOther	= $row['handicap_other'];
	//보장구
	$tmp= $row['device_gbn'];
	$tmp= Explode('/',$tmp);
	foreach($tmp as $var){
		$var = Explode(':',$var);
		$device[$var[0]] = $var[1];
	}
	$deviceOther= $row['device_other'];
	$longLvl		= $row['longlvl_gbn'];	//장기요양등급
	$longLvlOther	= $row['longlvl_other'];
	Unset($row);
	if ($_POST['hcptSeq']) $tmpHcptSeq = $_POST['hcptSeq'];
	if (!$tmpHcptSeq) $tmpHcptSeq = $hce->rcpt;
	//사정기록
	$sql = 'SELECT	ispt_seq
			,		work_amt
			,		live_aid_amt
			,		basic_old_amt
			,		ext_aid_amt
			,		support_amt
			,		support_aid_amt
			,		dwelling_env
			,		dwelling_env_other
			,		elv_yn
			,		house_stat
			,		house_stat_fault
			,		clean_stat
			,		clean_stat_fault
			,		heat_gbn
			,		heat_material
			,		heat_other
			,		toilet_gbn
			,		toilet_type
			,		moving_stat
			,		physical_problem_gbn
			,		physical_problem_other
			,		mental_problem_gbn
			,		mental_problem_other
			,		past_medi_his
			,		curr_medi_his
			,		per_family_cnt
			,		per_cost_gbn
			,		per_medical_gbn
			,		remark
			,		hsp_nm,dis_nm,hsp_go,hsp_fre,hsp_med,hsp_tel
			,		hsp_nm_2,dis_nm_2,hsp_go_2,hsp_fre_2,hsp_med_2,hsp_tel_2
			,		hsp_nm_3,dis_nm_3,hsp_go_3,hsp_fre_3,hsp_med_3,hsp_tel_3
			,		hsp_nm_4,dis_nm_4,hsp_go_4,hsp_fre_4,hsp_med_4,hsp_tel_4
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			ORDER	BY ispt_seq
			LIMIT	1';
	$row = $conn->get_array($sql);
	$workAmt			= Number_Format($row['work_amt']);			//근로소득
	$liveAidAmt			= Number_Format($row['live_aid_amt']);		//생계.주거비
	$basicOldAmt		= Number_Format($row['basic_old_amt']);		//기초노령연금
	$extAidAmt			= Number_Format($row['ext_aid_amt']);		//기타
	$supportAmt			= Number_Format($row['support_amt']);		//후원금
	$supportAidAmt		= Number_Format($row['support_aid_amt']);	//부양자지원
	$dwellingEnv		= ($row['dwelling_env'] ? $row['dwelling_env'] : '1');	//주거환경
	$dwellingEnvOther	= $row['dwelling_env_other'];	//주거환경기타
	$elvYn				= ($row['elv_yn'] ? $row['elv_yn'] : 'N');//엘리베이터 유무
	$houseStat			= ($row['house_stat'] ? $row['house_stat'] : 'Y');//주택상태
	$houseStatFault		= $row['house_stat_fault'];		//주택불량상태
	$cleanStat			= ($row['clean_stat'] ? $row['clean_stat'] : 'Y');//위생상태
	$cleanStatFault		= $row['clean_stat_fault'];		//위생불량상태
	$heatGbn			= ($row['heat_gbn'] ? $row['heat_gbn'] : '1');//난방형태
	$heatMaterial		= ($row['heat_material'] ? $row['heat_material'] : '1');		//난방재료
	$heatOther			= $row['heat_other'];			//난방기타
	$toiletGbn			= ($row['toilet_gbn'] ? $row['toilet_gbn'] : '1');//화장실구분
	$toiletType			= ($row['toilet_type'] ? $row['toilet_type'] : '1');//화장실형태
	$movingStat			= ($row['moving_stat'] ? $row['moving_stat'] : '1');//거동구분
	$physicalOther		= $row['physical_problem_other'];//신체적문제
	$mentalOther		= $row['mental_problem_other'];	//신적문제
	$pastMediHis		= StripSlashes($row['past_medi_his']);		//과거병력
	$currMediHis		= StripSlashes($row['curr_medi_his']);		//현재병력
	$familyCnt			= IntVal($row['per_family_cnt']);	//수입 의존 가족수
	$costGbn			= $row['per_cost_gbn'];		//최저생계비 구분
	$medicalGbn			= $row['per_medical_gbn'];	//의료구분
	$hspNm = stripslashes($row['hsp_nm']);
	$disNm = stripslashes($row['dis_nm']);
	$hspGo = stripslashes($row['hsp_go']);
	$hspFre = stripslashes($row['hsp_fre']);
	$hspMed = stripslashes($row['hsp_med']);
	$hspTel = stripslashes($row['hsp_tel']);
	$hspNm2 = stripslashes($row['hsp_nm_2']);
	$disNm2 = stripslashes($row['dis_nm_2']);
	$hspGo2 = stripslashes($row['hsp_go_2']);
	$hspFre2 = stripslashes($row['hsp_fre_2']);
	$hspMed2 = stripslashes($row['hsp_med_2']);
	$hspTel2 = stripslashes($row['hsp_tel_2']);
	$hspNm3 = stripslashes($row['hsp_nm_3']);
	$disNm3 = stripslashes($row['dis_nm_3']);
	$hspGo3 = stripslashes($row['hsp_go_3']);
	$hspFre3 = stripslashes($row['hsp_fre_3']);
	$hspMed3 = stripslashes($row['hsp_med_3']);
	$hspTel3 = stripslashes($row['hsp_tel_3']);
	$hspNm4 = stripslashes($row['hsp_nm_4']);
	$disNm4 = stripslashes($row['dis_nm_4']);
	$hspGo4 = stripslashes($row['hsp_go_4']);
	$hspFre4 = stripslashes($row['hsp_fre_4']);
	$hspMed4 = stripslashes($row['hsp_med_4']);
	$hspTel4 = stripslashes($row['hsp_tel_4']);
	$remark	 = StripSlashes($row['remark']);				//비고
	$isptSeq = $row['ispt_seq'];				//순번
	//신체적문제
	$tmp = Explode('/',$row['physical_problem_gbn']);
	foreach($tmp as $var){
		$var = Explode(':',$var);
		$physicalGbn[$var[0]] = $var[1];
	}
	//정신적문제
	$tmp = Explode('/',$row['mental_problem_gbn']);
	foreach($tmp as $var){
		$var = Explode(':',$var);
		$mentalGbn[$var[0]] = $var[1];
	}
	//순번
	if (Empty($isptSeq)) $isptSeq = 0;
	Unset($row);
	//사진
	$pic = '../sugupja/picture/'.$orgNo.'_'.$hce->IPIN.'.jpg';
	if (!is_file($pic)) $pic = '';
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfGetCostOfLiving(true);
		$('input:text[name="txtIcAmt"]').unbind('change').bind('change',function(){
			var amt = __str2num($('#txtIcWorkAmt').val())
					+ __str2num($('#txtIcAidAmt').val())
					+ __str2num($('#txtIcOldAmt').val())
					+ __str2num($('#txtIcExtAmt').val())
					+ __str2num($('#txtIcSupportAmt').val())
					+ __str2num($('#txtIcSpAidAmt').val());
			$('#lblIcAmt').text(__num2str(amt));
			lfChkCostOfLiving();
		});
		__init_form(document.f);
		__fileUploadInit($('#frmFile'), 'fileUploadCallback');
		$('#txtIcWorkAmt').change();
	});
	//저장
	function lfSaveSub(){
		var data = {};
		data['txtIVerJumin']= $('#txtIVer').attr('jumin');
		$('input:text').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();
			var dis = $(this).attr('disabled');
			if (!val || dis) val = '';
			data[id] = val;
		});
		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();
			if (!val) val = '';
			data[id] = val;
		});
		$('input:radio').each(function(){
			var name= $(this).attr('name');
			var val	= $('input:radio[name="'+name+'"]:checked').val();
			var dis = $(this).attr('disabled');
			if (!val || dis) val = '';
			data[name] = val;
		});
		$('input:checkbox').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).attr('checked') ? 'Y' : 'N';
			var dis = $(this).attr('disabled');
			if (dis) val = '';
			data[id] = val;
		});
		$('select').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();
			if (id.substring(0,12) == 'cboFamilyGbn' ||
				id.substring(0,15) == 'cboFamilyCohabit'){
			}else{
				data[id] = val;
			}
		});
		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();
			var dis = $(this).attr('disabled');
		});
		$.ajax({
			type:'POST'
		,	url:'./hce_apply.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();
					fileUpload();
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}
	//파일업로드
	function fileUpload(){
		if (!$('#filePicture').val()){
			return;
		}
		
		var exp = $('#filePicture').val().split('.');
		//if (exp[exp.length-1].toLowerCase() != 'xls'){
		//	alert('EXCEL 파일을 선택하여 주십시오.');
		//	return;
		//}
		var frm = $('#frmFile');
			frm.attr('action', './hce_client_picture_reg.php?fileCd=<?=$hce->IPIN;?>');
			frm.submit();
	}
	function fileUploadCallback(data, state){
		/*
		if (__fileUploadCallback(data, state)){
			alert('정상적으로 처리되었습니다.');
		}else{
			alert('저장중 오류가 발생하였습니다.\n관리자에게 문의하여 주십시오.');
		}
		*/
		//$('#msgBody').html(data).show();
	}
	//최저생계비
	function lfGetCostOfLiving(isLoad){
		if (!$('#txtIsptDt').val() || !$('#txtPerCnt').val()) return;
		var perCnt = __str2num($('#txtPerCnt').val());
		if (perCnt >= 1 && perCnt <= 6){
		}else{
			if (!isLoad) alert('수입 의존 가족수를 1명에서 6명사이로 입력하여 주십시오.');
			$('#txtPerCnt').focus();
			return;
		}
		$.ajax({
			type:'POST'
		,	url:'./hce_find.php'
		,	data:{
				'type'	:'GET_COST_OF_LIVING'
			,	'year'	:$('#txtIsptDt').val().substring(0,4)
			,	'gbn'	:$('#txtPerCnt').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var cost = __num2str(data);
				$('#lblLivingCost').text(cost);
				lfChkCostOfLiving();
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}
	//최저생계비 구분 체크
	function lfChkCostOfLiving(){
		var cost= __str2num($('#lblLivingCost').text());
		var pay	= __str2num($('#lblIcAmt').text());
		var gbn	= '9';
		if (cost > 0){
			if (cost > pay){
				gbn = '1';
			}else if (cost * 2 <= pay){
				gbn = '2';
			}
		}
		$('#optCostOfLiving_'+gbn).attr('checked',true);
	}
</script>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="80px">
		<col width="40px">
		<col width="50px">
		<col width="40px">
		<col width="50px">
		<col width="60px">
		<col width="95px">
		<col width="40px">
		<col width="135px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold" colspan="10">
				<div style="float:left; width:auto; padding-top:3px;">- 기본사항</div>
				<div class="right" style="width:auto;">
					<script type="text/javascrit">
						function lfPicShow(obj){
							if (!__checkImageExp3(obj)) return;
							var path = __get_file_path(obj);
							$('#divImgView').hide();
							$('#divImgView').css('filter','progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'file://'+path+'\', width=\'100%\', height=\'100%\', sizingMethod=\'image\'');
							var picW = $('#divImgView').width();
							var picH = $('#divImgView').height();
							var prtW = $('#divImgView').parent().width();
							var prtH = $('#divImgView').parent().height();
							if (picW > prtW || picH > prtH){
								var picR = 1;
								if (picW > picH){
									picR = picH / picW;
									picW = prtW;
									picH = prtH * picR;
								}else{
									picR = picW / picH;
									picH = prtH;
									picW = prtW * picR;
								}
								$('#divImgView').css('filter','progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'file://'+path+'\', width=\''+picW+'px\', height=\''+picH+'px\', sizingMethod=\'scale\'');
							}
							$('#divImgView').css('width',picW+'px').css('height',picH+'px');
							$('#divImgView').show();
						}
						function lfPicDel(){
							if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;
							$.ajax({
								type:'POST'
							,	url:'./hce_apply.php'
							,	data:{
									'type':'PICTURE_DEL'
								,	'sr':'<?=$hce->SR;?>'
								,	'cd':'<?=$hce->IPIN;?>'
								}
							,	beforeSend:function(){
								}
							,	success:function(result){
									if (result == 1){
										alert('사진이 삭제되었습니다.');
									}else if (result == 7){
										alert('삭제할 파일이 없습니다.');
									}else if (result == 9){
										alert('파일 삭제 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
									}else{
										alert(returl);
									}
								}
							,	error:function(){
								}
							}).responseXML;
							$('#divImgView').hide();
						}
					</script>
					<form id="frmFile" name="frmFile" method="post" enctype="multipart/form-data">
						<div style="float:right; width:auto; margin-top:1px;"><span class="btn_pack small"><button onclick="lfPicDel();">삭제</button></span></div>
						<div style="float:right; width:50px; margin-left:40px; background:url(../image/find_file.gif) no-repeat left 50%;"><input type="file" name="filePicture" id="filePicture" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-42px;" onchange="lfPicShow(this);"></div>
					</form>
				</div>
			</th>
			<td class="center top last" rowspan="5">
				<div id="divImgView" style="height:100%; <?=!$pic ? 'display:none;' : 'background:url('.$pic.') no-repeat center;';?>"></div>
			</td>
		</tr>
		<tr>
			<th class="center">성명</th>
			<td class="center"><?=$name;?></td>
			<th class="center">성별</th>
			<td class="center"><?=$gender;?></td>
			<th class="center">연령</th>
			<td class="center"><?=$age;?></td>
			<th class="center">주민번호</th>
			<td class="center"><?=$jumin;?></td>
			<th class="center">학력</th>
			<td class="left"><?=$eduGbn;?></td>
		</tr>
		<tr>
			<th class="center">주소</th>
			<td class="left" ><?=$addr;?></td>
		</tr>
		<tr>
			<th class="center">연락처</th>
			<td class="center" colspan="9">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="40px">
						<col width="90px">
						<col width="50px">
						<col width="90px">
						<col width="70px">
						<col width="90px">
						<col width="40px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center bottom">자택</th>
							<td class="center bottom"><?=$phone;?></td>
							<th class="center bottom">핸드폰</th>
							<td class="center bottom"><?=$mobile;?></td>
							<th class="center bottom">비상연락처</th>
							<td class="center bottom"><?=$grdTel;?></td>
							<th class="center bottom">관계</th>
							<td class="left bottom last"><?=$grdRel;?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="center">결혼/동거</th>
			<td class="" colspan="9"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'MR\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<span style="margin-left:5px;"><?=($marry == $row['code'] ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?
				}
				$conn->row_free();?>
				<span style="margin-left:5px;">/</span><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'CB\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<span style="margin-left:5px;"><?=($cohabit == $row['code'] ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?
				}
				$conn->row_free();?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="71px">
		<col width="80px">
		<col width="150px">
		<col width="50px">
		<col width="100px">
		<col width="72px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="8">- 가족사항</th>
		</tr>
		<tr>
			<th class="head">관계</th>
			<th class="head">성명</th>
			<th class="head">주소</th>
			<th class="head">연령</th>
			<th class="head">직업</th>
			<th class="head">동거여부</th>
			<th class="head">월소득액</th>
			<th class="head last">비고</th>
		</tr>
	</tbody>
	<tbody><?
		$sql = 'SELECT	family_rel AS rel
				,		family_nm AS name
				,		family_addr AS addr
				,		family_age AS age
				,		family_job AS job
				,		family_cohabit AS cohabit
				,		family_monthly AS monthly
				,		family_remark AS remark
				FROM	hce_family
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'';
		$conn->query($sql);
		$conn->fetch();
		$rowCnt = $conn->row_count();
		if ($rowCnt > 0){
			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);?>
				<tr>
					<td class="center"><?=$arrRel[$row['rel']]['name'];?></td>
					<td class="center"><?=$row['name'];?></td>
					<td class="center"><?=StripSlashes($row['addr']);?></td>
					<td class="center"><?=$row['age'];?></td>
					<td class="center"><?=$row['job'];?></td>
					<td class="center"><?=$row['cohabit'];?></td>
					<td class="center"><?=StripSlashes($row['monthly']);?></td>
					<td class="center last"><?=StripSlashes($row['remark']);?></td>
				</tr><?
			}
		}else{?>
			<tr>
				<td class="center last" colspan="20">::등록된 가족이 없습니다.::</td>
			</tr><?
		}
		$conn->row_free();?>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="550px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="3">- 경제상항</th>
		</tr>
		<tr>
			<th class="head">보호형태</th>
			<td class="last" colspan="2"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'IG\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<span style="margin-left:5px;"><?=($income == $row['code'] ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?
					if ($income == '9' && $income == $row['code'] && $incomeOther){?>
						<span style="margin-left:1px;">(<?=$incomeOther;?>)</span><?
					}
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="head">소득상황</th>
			<td class="">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="80px">
						<col width="95px">
						<col width="70px">
						<col width="90px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="">근로소득</th>
							<td class="last" colspan="5">
								<input id="txtIcWorkAmt" name="txtIcAmt" type="text" value="<?=$workAmt	;?>" class="number" style="width:70px;">
							</td>
						</tr>
						<tr>
							<th class="" rowspan="3">정부지원금</th>
							<th class="">생계.주거비</th>
							<td class="last" colspan="4">
								<input id="txtIcAidAmt" name="txtIcAmt" type="text" value="<?=$liveAidAmt;?>" class="number" style="width:70px;">
							</td>
						</tr>
						<tr>
							<th class="">기초노령연금</th>
							<td class="last" colspan="4">
								<input id="txtIcOldAmt" name="txtIcAmt" type="text" value="<?=$basicOldAmt;?>" class="number" style="width:70px;">
							</td>
						</tr>
						<tr>
							<th class="">기타</th>
							<td class="last" colspan="4">
								<input id="txtIcExtAmt" name="txtIcAmt" type="text" value="<?=$extAidAmt;?>" class="number" style="width:70px;">
							</td>
						</tr>
						<tr>
							<th class="">후원금</th>
							<td class="last" colspan="5">
								<input id="txtIcSupportAmt" name="txtIcAmt" type="text" value="<?=$supportAmt;?>" class="number" style="width:70px;">
							</td>
						</tr>
						<tr>
							<th class="">부양자지원</th>
							<td class="last" colspan="5">
								<input id="txtIcSpAidAmt" name="txtIcAmt" type="text" value="<?=$supportAidAmt;?>" class="number" style="width:70px;">
							</td>
						</tr>
						<tr>
							<th class="" colspan="3">이 수입에 의존하는 가족수</th>
							<td class="">
								<input id="txtPerCnt" name="txt" type="text" value="<?=$familyCnt;?>" class="number" style="width:70px;" onchange="lfGetCostOfLiving();">
							</td>
							<th class="">최저생계비</th>
							<td class="left last"><span id="lblLivingCost"></span></td>
						</tr>
						<tr>
							<td class="last" colspan="6"><?
								$sql = 'SELECT	code,name
										FROM	hce_gbn
										WHERE	type	= \'MCL\'
										AND		use_yn	= \'Y\'';
								$conn->query($sql);
								$conn->fetch();
								$rowCnt = $conn->row_count();
								for($i=0; $i<$rowCnt; $i++){
									$row = $conn->select_row($i);?>
									<label><input id="optCostOfLiving_<?=$row['code'];?>" name="optCostOfLiving" type="radio" class="radio" value="<?=$row['code'];?>" <?=($costGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?
								}
								$conn->row_free();?>
							</td>
						</tr>
						<tr>
							<th class="bottom">의료보장</th>
							<td class="bottom last" colspan="5"><?
								$sql = 'SELECT	code,name
										FROM	hce_gbn
										WHERE	type	= \'MDC\'
										AND		use_yn	= \'Y\'';
								$conn->query($sql);
								$conn->fetch();
								$rowCnt = $conn->row_count();
								for($i=0; $i<$rowCnt; $i++){
									$row = $conn->select_row($i);?>
									<label><input id="optMedical_<?=$row['code'];?>" name="optMedical" type="radio" class="radio" value="<?=$row['code'];?>" <?=($medicalGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?
								}
								$conn->row_free();?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="bold last">
				<div style="padding-left:20px;">월 총수입</div>
				<div class="right" style="width:100px;"><span id="lblIcAmt">0</span>원</div>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="2">- 주거상항</th>
		</tr>
		<tr>
			<th class="head">주택<br>소유상태</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'DL\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<span style="margin-left:5px;"><?=($dwelling == $row['code'] ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?
					if ($row['code'] == '2'){
						if ($dwelling == $row['code']){
							$amt = Number_Format($depositAmt);
						}else{
							$amt = 0;
						}?>
						(보증금 : <?=$amt;?>만원)<br><?
					}else if ($row['code'] == '3'){
						if ($dwelling == $row['code']){
							$amt1 = Number_Format($depositAmt);
							$amt2 = Number_Format($rentalAmt);
						}else{
							$amt1 = 0;
							$amt2 = 0;
						}?>
						(보증금 : <?=$amt1;?>만원 / 월세 : <?=$amt2;?>만원)<br><?
					}else if ($row['code'] == '9'){
						if ($dwelling == $row['code']){?>
							<span style="margin-left:1px;">(<?=$dwellingOther;?>)</span><?
						}
					}
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="head">주택형태</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'HT\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<span style="margin-left:5px;"><?=($house == $row['code'] ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?
					if ($row['code'] == '9'){
						if ($house == $row['code']){?>
							<span style="margin-left:1px;">(<?=$houseOther;?>)</span><?
						}
					}
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="head" rowspan="2">주거환경</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'DLE\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optDwellingEnv_<?=$row['code'];?>" name="optDwellingEnv" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtDwellingEnvOther" <?=($dwellingEnv == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?
					if ($row['code'] == '9'){?>
						(<input id="txtDwellingEnvOther" name="txt" type="text" value="<?=$dwellingEnvOther;?>" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<td class="last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="">승강기유무</th>
							<td class="last">
								<label><input id="optElvY" name="optElv" type="radio" class="radio" value="Y" <?=($elvYn == 'Y' ? 'checked' : '');?>>유</label>
								<label><input id="optElvN" name="optElv" type="radio" class="radio" value="N" <?=($elvYn != 'Y' ? 'checked' : '');?>>무</label>
							</td>
						</tr>
						<tr>
							<th class="">주택상태</th>
							<td class="last">
								<label><input id="optHouseStat1" name="optHouseStat" type="radio" class="radio" value="1" otherVal="2" otherObj="txtHouseStatOther" <?=($houseStat == '1' ? 'checked' : '');?>>양호</label>
								<label><input id="optHouseStat2" name="optHouseStat" type="radio" class="radio" value="2" otherVal="2" otherObj="txtHouseStatOther" <?=($houseStat == '2' ? 'checked' : '');?>>불량</label>
								(<input id="txtHouseStatOther" name="txt" type="text" value="<?=$houseStatFault;?>" style="width:150px; background-color:#efefef;" disabled="true">)
							</td>
						</tr>
						<tr>
							<th class="bottom">위생상태</th>
							<td class="bottom last">
								<label><input id="optCleanStat1" name="optCleanStat" type="radio" class="radio" value="1" otherVal="2" otherObj="txtCleanStatOther" <?=($cleanStat == '1' ? 'checked' : '');?>>양호</label>
								<label><input id="optCleanStat2" name="optCleanStat" type="radio" class="radio" value="2" otherVal="2" otherObj="txtCleanStatOther" <?=($cleanStat == '2' ? 'checked' : '');?>>불량</label>
								(<input id="txtCleanStatOther" name="txt" type="text" value="<?=$cleanStatFault;?>" style="width:150px; background-color:#efefef;" disabled="true">)
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="head" rowspan="2">난방형태</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'HTG\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optHeat_<?=$row['code'];?>" name="optHeat" type="radio" class="radio" value="<?=$row['code'];?>" otherVal="9" otherObj="txtHeatOther" <?=($heatGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?
					if ($row['code'] == '9'){?>
						(<input id="txtHeatOther" name="txt" type="text" value="<?=$heatOther;?>" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<td class="last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="bottom">난방재료</th>
							<td class="bottom last">
								<label><input id="optHeatMaterial1" name="optHeatMaterial" type="radio" class="radio" value="1" style=" background-color:#efefef;" disabled="true" <?=($heatMaterial == '1' ? 'checked' : '');?>>연탄</label>
								<label><input id="optHeatMaterial2" name="optHeatMaterial" type="radio" class="radio" value="2" style=" background-color:#efefef;" disabled="true" <?=($heatMaterial == '2' ? 'checked' : '');?>>장작</label>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="head">화장실형태</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'TLG\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optToilet_<?=$row['code'];?>" name="optToilet" type="radio" class="radio" value="<?=$row['code'];?>" <?=($toiletGbn == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?
				}
				$conn->row_free();?>
				<span>/</span><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'TLT\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="optToiletType_<?=$row['code'];?>" name="optToiletType" type="radio" class="radio" value="<?=$row['code'];?>" <?=($toiletType == $row['code'] ? 'checked' : '');?>><?=$row['name'];?></label><?
				}
				$conn->row_free();?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="2">- 건강상태</th>
		</tr>
		<tr>
			<th class="head">전체적<br>건강상태</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'HS\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<div style="float:left; width:47%;">
						<span style="margin-left:5px;"><?=($health == $row['code'] ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?
						if ($row['code'] == '9'){
							if ($health == $row['code']){?>
								<span style="margin-left:2px;"><?=$healthOther;?></span><?
							}
						}?>
					</div><?
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="head">만성질환</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type	= \'DT\'
						AND		use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<div style="float:left; width:23%;">
						<span style="margin-left:5px;"><?=($disease[$row['code']] == 'Y' ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span>
					</div><?
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="head">거동상태</th>
			<td class="last">
				<label><input id="optMovingStat1" name="optMovingStat" type="radio" class="radio" value="1" <?=($movingStat == '1' ? 'checked' : '');?>>자립가능</label>
				<label><input id="optMovingStat2" name="optMovingStat" type="radio" class="radio" value="2" <?=($movingStat == '2' ? 'checked' : '');?>>도움필요</label>
				<label><input id="optMovingStat3" name="optMovingStat" type="radio" class="radio" value="3" <?=($movingStat == '3' ? 'checked' : '');?>>완전도움필요</label>
			</td>
		</tr>
		<tr>
			<th class="center" rowspan="2">장애여부</th>
			<td class="last">
				<span style="margin-left:5px;"><?=($handicap == 'Y' ? '■' : '□');?></span><span style="margin-left:2px;">유</span>
				<span style="margin-left:5px;"><?=($handicap != 'Y' ? '■' : '□');?></span><span style="margin-left:2px;">무</span>
			</td>
		</tr>
		<tr>
			<td class="last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="50px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="bottom">장애유형</th>
							<td class="bottom last"><?
								if ($handicap == 'Y'){?>
									<span style="margin-left:5px;"><?=$handicapOther;?></span><?
								}?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="head">장기요양등급</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type= \'LLV\'
						AND	use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<span style="margin-left:5px;"><?=($longLvl == $row['code'] ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?
					if ($row['code'] == '9'){
						if ($longLvl == $row['code']){?>
							<span style="margin-left:5px;"><?=$longLvlOther;?></span><?
						}
					}
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="head">보장구</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type= \'DV\'
						AND	use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<span style="margin-left:5px;"><?=($device[$row['code']] == 'Y' ? '■' : '□');?></span><span style="margin-left:2px;"><?=$row['name'];?></span><?
					if ($row['code'] == '99'){
						if ($device[$row['code']] == 'Y'){?>
							<span style="margin-left:5px;"><?=$deviceOther;?></span><?
						}
					}
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="head">신체적문제</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type= \'PP\'
						AND	use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="chkPhysical_<?=$row['code'];?>" name="chkDevice" type="checkbox" class="checkbox" value="<?=$row['code'];?>" <?=($physicalGbn[$row['code']] == 'Y' ? 'checked' : '');?> otherVal="9" otherObj="txtPhysicalOther"><?=$row['name'];?></label><?
					if ($row['code'] == '9'){?>
						(<input id="txtPhysicalOther" name="txt" type="text" value="<?=$physicalOther;?>" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="head">정신적문제</th>
			<td class="last"><?
				$sql = 'SELECT	code,name
						FROM	hce_gbn
						WHERE	type= \'MP\'
						AND	use_yn	= \'Y\'';
				$conn->query($sql);
				$conn->fetch();
				$rowCnt = $conn->row_count();
				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<label><input id="chkMental_<?=$row['code'];?>" name="chkMental" type="checkbox" class="checkbox" value="<?=$row['code'];?>" <?=($mentalGbn[$row['code']] == 'Y' ? 'checked' : '');?> otherVal="9" otherObj="txtMentalOther"><?=$row['name'];?></label><?
					if ($row['code'] == '9'){?>
						(<input id="txtMentalOther" name="txt" type="text" value="<?=$mentalOther;?>" style="width:150px; background-color:#efefef;" disabled="true">)<?
					}
				}
				$conn->row_free();?>
			</td>
		</tr>
		<tr>
			<th class="head">과거병력</th>
			<td class="last">
				<input id="txtPastMediHis" name="txt" type="text" value="<?=$pastMediHis;?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="head">현재병력</th>
			<td class="last">
				<input id="txCurrMediHis" name="txt" type="text" value="<?=$currMediHis;?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="head">비고</th>
			<td class="last">
				<input id="txtRemark" name="txt" type="text" value="<?=$remark;?>" style="width:100%;">
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="16%" span="6">
	</colgroup>
	<tbody>
		<tr>
			<th class="head">의료기관명</th>
			<th class="head">질병</th>
			<th class="head">통원상황</th>
			<th class="head">빈도</th>
			<th class="head">복약(치료상황)</th>
			<th class="head">연락처</th>
		</tr>
		<tr>
			<td><input id="txtHspNm" name="txt" value="<?=$hspNm;?>" style="width:100%;"></td>
			<td><input id="txtDisNm" name="txt" value="<?=$disNm;?>" style="width:100%;"></td>
			<td><input id="txtHspGo" name="txt" value="<?=$hspGo;?>" style="width:100%;"></td>
			<td><input id="txtHspFre" name="txt" value="<?=$hspFre;?>" style="width:100%;"></td>
			<td><input id="txtHspMed" name="txt" value="<?=$hspMed;?>" style="width:100%;"></td>
			<td><input id="txtHspTel" name="txt" value="<?=$myF->phoneStyle($hspTel);?>" class="phone"></td>
		</tr>
		<tr>
			<td><input id="txtHspNm_2" name="txt" value="<?=$hspNm2;?>" style="width:100%;"></td>
			<td><input id="txtDisNm_2" name="txt" value="<?=$disNm2;?>" style="width:100%;"></td>
			<td><input id="txtHspGo_2" name="txt" value="<?=$hspGo2;?>" style="width:100%;"></td>
			<td><input id="txtHspFre_2" name="txt" value="<?=$hspFre2;?>" style="width:100%;"></td>
			<td><input id="txtHspMed_2" name="txt" value="<?=$hspMed2;?>" style="width:100%;"></td>
			<td><input id="txtHspTel_2" name="txt" value="<?=$myF->phoneStyle($hspTel2);?>" class="phone"></td>
		</tr>
		<tr>
			<td><input id="txtHspNm_3" name="txt" value="<?=$hspNm3;?>" style="width:100%;"></td>
			<td><input id="txtDisNm_3" name="txt" value="<?=$disNm3;?>" style="width:100%;"></td>
			<td><input id="txtHspGo_3" name="txt" value="<?=$hspGo3;?>" style="width:100%;"></td>
			<td><input id="txtHspFre_3" name="txt" value="<?=$hspFre3;?>" style="width:100%;"></td>
			<td><input id="txtHspMed_3" name="txt" value="<?=$hspMed3;?>" style="width:100%;"></td>
			<td><input id="txtHspTel_3" name="txt" value="<?=$myF->phoneStyle($hspTel3);?>" class="phone"></td>
		</tr>
		<tr>
			<td><input id="txtHspNm_4" name="txt" value="<?=$hspNm4;?>" style="width:100%;"></td>
			<td><input id="txtDisNm_4" name="txt" value="<?=$disNm4;?>" style="width:100%;"></td>
			<td><input id="txtHspGo_4" name="txt" value="<?=$hspGo4;?>" style="width:100%;"></td>
			<td><input id="txtHspFre_4" name="txt" value="<?=$hspFre4;?>" style="width:100%;"></td>
			<td><input id="txtHspMed_4" name="txt" value="<?=$hspMed4;?>" style="width:100%;"></td>
			<td><input id="txtHspTel_4" name="txt" value="<?=$myF->phoneStyle($hspTel4);?>" class="phone"></td>
		</tr>
	</tbody>
</table>
<input id="bodyIdx" type="hidden" value="1">
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>
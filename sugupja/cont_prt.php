<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_myImage.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$conSeq	= $_POST['conSeq'];
	$svcSeq	= $_POST['svcSeq'];
	$subCd	= $_POST['subCd'];
	$svcCd	= '0';


	//기관정보
	$sql = 'SELECT	m00_store_nm AS org_nm, m00_ctel AS phone, m00_mname AS mg_nm, m00_caddr1 AS addr, m00_caddr2 AS addr_dtl
			,		m00_bank_no AS bank_no, m00_bank_name AS bank_cd
			,		m00_jikin AS jikin
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			AND		m00_mkind = \'0\'';

	$tmpR = $conn->get_array($sql);

	$orgNm	= $tmpR['org_nm'];
	$orgTel	= $myF->phoneStyle($tmpR['phone'],'.');
	$orgMg	= $tmpR['mg_nm'];
	$orgAddr= $tmpR['addr'].' '.$tmpR['addr_dtl'];
	$orgBank= $tmpR['bank_no'] ? $definition->GetBankName($tmpR['bank_cd']).'('.$tmpR['bank_no'].')' : '';
	$orgJikin='../mem_picture/'.$tmpR['jikin'];

	Unset($tmpR);

	if (!is_file($orgJikin)) $orgJikin = '';


	//고객정보
	$sql = 'SELECT	m03_name AS name, m03_juso1 AS addr, m03_juso2 AS addr_dtl, m03_tel AS phone, m03_hp AS mobile, m03_key AS cd_key
			,		m03_yboho_name AS grd_nm, m03_yboho_gwange AS grd_rel, m03_yboho_juminno AS grd_jumin, m03_yboho_phone AS grd_tel, m03_yboho_addr AS grd_addr
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \''.$svcCd.'\'
			AND		m03_jumin = \''.$jumin.'\'';

	$tmpR = $conn->get_array($sql);

	$name	= $tmpR['name'];
	$addr	= $tmpR['addr'].' '.$tmpR['addr_dtl'];
	$phone	= $myF->phoneStyle($tmpR['phone'] ? $tmpR['phone'] : $tmpR['mobile'],'.');
	$key	= $tmpR['cd_key'];

	$grdNm	= $tmpR['grd_nm'];
	$grdRel	= $tmpR['grd_rel'];
	$grdJumin = $tmpR['grd_jumin'];
	$grdTel	= $tmpR['grd_tel'];
	$grdAddr= $tmpR['grd_addr'];

	Unset($tmpR);


	//이용자 서명
	$sql = 'SELECT	sign_data
			FROM	sign_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		log_key = \'CT_TG_'.$key.'_0_'.$conSeq.'\'';

	$signTG = $conn->get_data($sql);


	//대리인 서명
	$sql = 'SELECT	sign_data
			FROM	sign_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		log_key = \'CT_PT_'.$key.'_0_'.$conSeq.'\'';

	$signPT = $conn->get_data($sql);



	//약관내용
	$sql = 'SELECT	*
			FROM	client_contract
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_cd	= \''.$svcCd.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		seq		= \''.$conSeq.'\'';

	$R = $conn->get_array($sql);


	//등급, 인정번호
	$sql = 'SELECT	app_no, level
			FROM	client_his_lvl
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		svc_cd	 = \''.$svcCd.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		from_dt	<= \''.$R['from_dt'].'\'
			AND		to_dt	>= \''.$R['from_dt'].'\'';

	$tmpR = $conn->get_array($sql);

	$appNo = $tmpR['app_no'];
	//$level = $tmpR['level'].' '.$tmpR['addr_dtl'];
	
	$level = $myF->_lvlNm($tmpR['level']);

	Unset($tmpR);


	//수급자구분
	$sql = 'SELECT	kind
			FROM	client_his_kind
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		from_dt <= \''.$R['from_dt'].'\'
			AND		to_dt	>= \''.$R['from_dt'].'\'';

	$kind = $conn->get_data($sql);


	$style = 'line-height:1.3em; border:1px solid BLACK;';
	$bold = 'font-weight:bold;';
	

	if($orgNo == '32817000163'){ //천사방문
		$str = '별지 제24호서식의 장기요양급여비용명세서';
	}else {
		$str = '별지 제3호서식의 장기요양급여 이용료 세부내역서';
	}

	//다음페이지 <p style="page-break-before:always">
?>
<style type="text/css">
	td,body {font:10pt/1.5 바탕체; letter-spacing:-0.8px;}
	td {border:1px solid BLACK;}
</style>

<div id="ID_BTN_PRT" style="position:absolute; left:20px; top:20px; width:50px; height:30px; text-align:center; padding-top:3px; background-color:WHITE; border:2px solid RED;"><a href="javascript:printPage();" style="font-weight:bold; color:BLUE;">출력</a></div>
<div style="font-size:15pt; font-weight:bold; text-align:center; border:1px solid BLACK;">장기요양급여 이용 표준약관<br>(방문요양)</div>
<p style="margin-top:50px; text-align:right;"><img src="../image/standard_mark.jpg"></p>
<p style="<?=$bold;?>text-align:justify; margin-top:50px;">&nbsp;이용자, 제공자 및 대리인(보호자)은 장기요양급여 이용에 대하여<br>다음과 같은 조건으로 계약을 체결한다.</p>
<div><?if ($orgJikin){?><div style="position:absolute; z-index:1; left:280px; top:590px; width:auto;"><img src="<?=$orgJikin;?>" style="width:80px;"></div><?}?>
	<div id="ID_DIV_TBL1" style="position:; z-index:2;">
		<table style="width:100%; margin-top:50px;">
			<colgroup>
				<col width="60px">
				<col width="90px">
				<col width="120px">
				<col width="70px">
				<col width="90px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<td style="text-align:left; " colspan="6">&nbsp;계약당사자</td>
				</tr>
				<tr>
					<td style="" rowspan="4">이용자<br>(갑)</td>
					<td style=" height:70px;">성&nbsp;&nbsp;&nbsp;&nbsp;명</td>
					<td style="border-right:none; text-align:left;">&nbsp;<?=$name;?></td>
					<td style="border-left:none; text-align:right;">(인)&nbsp;<?if ($signTG){?><div style="position:absolute; margin-left:-75px; margin-top:-15px;"><img src="<?=$signTG;?>" style="width:110px;"></div><?}?></td>
					<td style="">등&nbsp;&nbsp;&nbsp;급/<br>인정번호</td>
					<td style="text-align:left;">&nbsp;<?=$level;?>/<br>&nbsp;<?=$appNo;?></td>
				</tr>
				<tr>
					<td style="">주민번호</td>
					<td style="text-align:left;" colspan="2">&nbsp;<?=$myF->issStyle($jumin);?></td>
					<td style="">연&nbsp;락&nbsp;처</td>
					<td style="text-align:left;">&nbsp;<?=$phone;?></td>
				</tr>
				<tr>
					<td style="">주&nbsp;&nbsp;&nbsp;&nbsp;소</td>
					<td style="text-align:left;" colspan="4">&nbsp;<?=$addr;?></td>
				</tr>
				<tr>
					<td style="">구&nbsp;&nbsp;&nbsp;&nbsp;분</td>
					<td style="text-align:left;" colspan="4">
						&nbsp;<?=$kind == '1' ? '▣' : '□';?>일반
						&nbsp;<?=$kind == '4' ? '▣' : '□';?>경감대상자
						&nbsp;<?=$kind == '2' ? '▣' : '□';?>의료수급자
						&nbsp;<?=$kind == '3' ? '▣' : '□';?>기초수급권자
					</td>
				</tr>
				<tr>
					<td style="" rowspan="3">제공자<br>(을)</td>
					<td style="">기&nbsp;관&nbsp;명</td>
					<td style="text-align:left;" colspan="2">&nbsp;<?=$orgNm;?></td>
					<td style="">기관기호</td>
					<td style="text-align:left;">&nbsp;<?=$_SESSION['userCenterGiho'];?></td>
				</tr>
				<tr>
					<td style="">기관장 성명</td>
					<td style="border-right:none; text-align:left;">&nbsp;<?=$orgMg;?></td>
					<td style="border-left:none; text-align:right;">(인)&nbsp;</td>
					<td style="">연&nbsp;락&nbsp;처</td>
					<td style="text-align:left;">&nbsp;<?=$orgTel;?></td>
				</tr>
				<tr>
					<td style="">주&nbsp;&nbsp;&nbsp;&nbsp;소</td>
					<td style="text-align:left;" colspan="4">&nbsp;<?=$orgAddr;?></td>
				</tr>
				<tr>
					<td style="" rowspan="3">대리인<br>또는<br>보호자<br>(병)</td>
					<td style="">성&nbsp;&nbsp;&nbsp;&nbsp;명</td>
					<td style="border-right:none; text-align:left;">&nbsp;<?=$grdNm;?></td>
					<td style="border-left:none; text-align:right;">(인)&nbsp;<?if ($signPT){?><div style="position:absolute; margin-left:-75px; margin-top:-15px;"><img src="<?=$signPT;?>" style="width:110px;"></div><?}?></td>
					<td style="">관&nbsp;&nbsp;&nbsp;&nbsp;계</td>
					<td style="text-align:left;">&nbsp;<?=$grdRel;?></td>
				</tr>
				<tr>
					<td style="">생년월일</td>
					<td style="text-align:left;" colspan="2">&nbsp;<?=$myF->dateStyle($myF->issToBirthday($grdJumin),'KOR');?></td>
					<td style="">연&nbsp;락&nbsp;처</td>
					<td style="text-align:left;">&nbsp;<?=$myF->phoneStyle($grdTel,'.');?></td>
				</tr>
				<tr>
					<td style="">주&nbsp;&nbsp;&nbsp;&nbsp;소</td>
					<td style="text-align:left;" colspan="4">&nbsp;<?=$grdAddr;?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<p style="page-break-before:always;">
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제1조(목적)</p>
	<p style="text-align:justify; padding-left:15px;">
		고령이나 노인성질병 등으로 인하여 혼자서 일상생활을 수행하기 어려운 노인들 중 장기요양등급을 받은 분들에게 방문요양급여를 제공하여 노후의 건강증진 및 생활안정을 도모하고 그 가족의 부담을 덜어줌으로써 삶의 질을 향상시키고자 한다.
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제2조(계약기간)</p>
	<p style="text-align:justify; padding-left:15px;">
		① 계약기간은 <?=$myF->dateStyle($R['from_dt'],'KOR');?>부터 <?=$myF->dateStyle($R['to_dt'],'KOR');?>까지로 한다.<br>
		② 제1항의 계약기간은 당사자 간의 협의에 따라 변경할 수 있다.
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제3조(급여범위)</p>
	<p style="text-align:justify; padding-left:15px;">
		방문요양급여는 장기요양요원이 '갑' 의 가정 등을 방문하여 신체활동 및 가사활동 등을 지원하는 장기요양급여로 한다.
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제4조(급여이용 및 제공)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; margin-left:15px;">
			① 방문요양급여 이용 및 제공은 장기요양급여 이용(제공)계획서에 의한다.<br>
			② '갑'과 '을'의 방문요양급여 이용시간은 아래와 같이 한다. 다만, 1일 2회 방문요양급여를 제
공하는 경우에는 2시간 이상의 간격으로 제공한다.
		</p>
		<div style="margin-left:15px; padding-top:10px;">
			<table style="width:100%;">
				<colgroup>
					<col width="16%">
					<col width="42%">
					<col width="42%">
				</colgroup>
				<tbody>
					<tr>
						<td style="background-color:#EAEAEA;">구분</td>
						<td style="background-color:#EAEAEA;">이용일</td>
						<td style="background-color:#EAEAEA;">이용시간</td>
					</tr>
					<tr>
						<td style="background-color:#EAEAEA;">이용시간(1)</td>
						<td>
							<?=$R['use_yoil1'][0] == 'Y' ? '▣' : '□';?>월
							<?=$R['use_yoil1'][1] == 'Y' ? '▣' : '□';?>화
							<?=$R['use_yoil1'][2] == 'Y' ? '▣' : '□';?>수
							<?=$R['use_yoil1'][3] == 'Y' ? '▣' : '□';?>목
							<?=$R['use_yoil1'][4] == 'Y' ? '▣' : '□';?>금
							<?=$R['use_yoil1'][5] == 'Y' ? '▣' : '□';?>토
							<?=$R['use_yoil1'][6] == 'Y' ? '▣' : '□';?>일
						</td>
						<td><?=SubStr($R['from_time1'],0,2) ? SubStr($R['from_time1'],0,2) : '&nbsp;&nbsp;';?>시 <?=SubStr($R['from_time1'],2,2) ? SubStr($R['from_time1'],2,2) : '&nbsp;&nbsp;';?>분 ~ <?=SubStr($R['to_time1'],0,2) ? SubStr($R['to_time1'],0,2) : '&nbsp;&nbsp;';?>시 <?=SubStr($R['to_time1'],2,2) ? SubStr($R['to_time1'],2,2) : '&nbsp;&nbsp;';?>분</td>
					</tr>
					<tr>
						<td style="background-color:#EAEAEA;">이용시간(2)</td>
						<td>
							<?=$R['use_yoil2'][0] == 'Y' ? '▣' : '□';?>월
							<?=$R['use_yoil2'][1] == 'Y' ? '▣' : '□';?>화
							<?=$R['use_yoil2'][2] == 'Y' ? '▣' : '□';?>수
							<?=$R['use_yoil2'][3] == 'Y' ? '▣' : '□';?>목
							<?=$R['use_yoil2'][4] == 'Y' ? '▣' : '□';?>금
							<?=$R['use_yoil2'][5] == 'Y' ? '▣' : '□';?>토
							<?=$R['use_yoil2'][6] == 'Y' ? '▣' : '□';?>일
						</td>
						<td><?=SubStr($R['from_time2'],0,2) ? SubStr($R['from_time2'],0,2) : '&nbsp;&nbsp;';?>시 <?=SubStr($R['from_time2'],2,2) ? SubStr($R['from_time2'],2,2) : '&nbsp;&nbsp;';;?>분 ~ <?=SubStr($R['to_time2'],0,2) ? SubStr($R['to_time2'],0,2) : '&nbsp;&nbsp;';?>시 <?=SubStr($R['to_time2'],2,2) ? SubStr($R['to_time2'],2,2) : '&nbsp;&nbsp;';?>분</td>
					</tr>
				</tbody>
			</table>
		</div>
		<p style="text-align:justify; margin-left:15px; padding-top:10px;">
			※ 요일에 따라 이용시간이 다른 경우 이용시간 기재란을 늘려서 기록함<br>
			※ '갑' 또는 '병' 은 사정에 의해 일시적으로 이용시간을 지키기 어려운 경우 서비스 이용시작 최소 1시간 전에 '을' 에게 연락을 취해야 함.<br>
		</p>
		<p style="text-align:justify; margin-left:15px; padding-top:10px;">
			③ 관공서의 공휴일에 관한 규정’에 의한 공휴일에 급여를 제공하는 경우에는 '을' 은 30%의 할증비용을 청구할 수 있다.<br>
			④ 야간(18:00~22:00), 심야(22:00~06:00)에 급여를 제공하는 경우에는 '을' 은 야간 20%, 심야 30%의 할증 비용을 청구할 수 있다.<br>
			⑤ 야간심야휴일가산이 동시에 적용되는 경우에는 중복하여 가산하지 않는다.<br>
			⑥ '을' 은 익월 장기요양급여 제공을 하고자하는 경우에는'갑' (또는 '병' )과 협의하여 급여개시일로부터 14일이내 급여계획서를 작성하고 수급자(보호자)확인받아 급여 서비스를 실시한다.<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제5조(계약자 의무)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			① '갑' 은 다음 각 호를 성실하게 이행하여야 한다.
		</p>
		<p style="text-align:justify; padding-left:30px;">
			1. 월 이용료 납부<br>
			2. 방문요양급여 범위내 급여이용<br>
			3. 장기요양급여 이용수칙 준수<br>
			4. 기타 '을' 과 협의한 규칙 이행<br>
		</p>
		<p style="text-align:justify; padding-left:15px;">
			② '을' 은 다음 각 호를 성실하게 이행하여야 한다.
		</p>
		<p style="text-align:justify; padding-left:30px;">
			1. 방문요양급여 제공 계약내용 준수<br>
			2. 급여제공 중 '갑' 에게 신병 이상이 생기는 경우 즉시 '병' 에게 통보<br>
			3. 급여제공시간에 '갑' 의 주변 및 집기류의 청결 및 유지관리<br>
			4. 급여제공 중 알게 된 '갑' 의 신상 및 질환 증에 관한 비밀유지 (단, 치료 등의 조치가 필요한 경우는 예외)<br>
			5. 이용상담, 지역사회 다른 서비스 이용 정보제공<br>
			6. 노인학대 예방 및 노인인권 보호 준수<br>
			7. 기타 '갑' (또는 '병' )의 요청에 협조<br>
		</p>
		<p style="text-align:justify; padding-left:15px;">
			③ '병' 은 다음 각 호를 성실하게 이행하여야 한다.
		</p>
		<p style="text-align:justify; padding-left:30px;">
			1. '갑' 에 관한 건강 및 필요한 자료제공<br>
			2. '갑' 의 월 이용료 등 비용 부담<br>
			3. 인적 사항 및 장기요양보험 등급 변경 시 즉시 '을' 에게 통보<br>
			4. '갑' 에 대한 의무이행이 어려울시 대리인 선정 및 '을' 에게 통보<br>
			5. 기타 '을' 의 협조요청 이행<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제6조(계약해지 요건)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			① '갑' (또는 '병' )은 다음 각호에 해당되는 경우에는 '을' 과 협의하여 계약을 해지 할 수 있다.
		</p>
		<p style="text-align:justify; padding-left:30px;">
			1. 제2조의 계약기간이 만료된 경우<br>
			2. 제3조의 방문요양급여 범위에 해당하는 서비스를 이행하지 아니한 경우<br>
			3. 제4조제2항의 방문요양급여 제공시간을'갑' (또는'병' )의 동의 없이 '을' 이 임의로 변경하거나 배치된 장기요양요원을 임의로 변경 했을 경우<br>
			4. 기타 '갑' 의 계약해지 사유가 발생한 경우<br>
		</p>
		<p style="text-align:justify; padding-left:15px;">
			② '을' 은 다음 각호에 해당되는 경우에는 '갑' (또는 '병' )과 협의하여 계약을 해지 할 수 있다.
		</p>
		<p style="text-align:justify; padding-left:30px;">
			1. 제2조의 계약기간이 만료되거나 사망한 경우<br>
			2. '갑' 이 장기요양보험 등급외자로 등급변경이 발생한 경우<br>
			3. '갑' 의 건강진단 결과「감염병의예방및관리에대한법률」에 따른 감염병 환자로서 감염의 위험성이 있는 경우로 판정될 때<br>
			4. '갑' 의 건강상의 이유로 서비스 이용이 어려울 때<br>
			5. 이용계약시 제시된 이용안내를 '갑' 이 정당한 이유 없이 따르지 않는 등 서비스 제공에 심각한 지장을 줄 때<br>
			6. '갑' 이 월 5회 이상 무단으로 방문요양급여 이용시간과 장소를 지키지 아니하였을 때<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제7조(계약의 해지)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			① '갑' (또는'병' )은 제6조제1항의 계약해지 요건이 발생한 경우에는 해당일 또는 계약기간 만료일전에 별지 제2호서식의 장기요양급여 종결 신청서를 제출하여야 한다. 다만, 기타 부득이한 경우에는 우선 유선으로 할 수 있다.<br>
			② '을' 은 제6조제2항에 의한 계약해지 요건이 발생한 경우에는 계약해지 의사를 별지 제2호 서식의 장기요양급여 종결안내서 및 관련 증빙서류와 함께 '갑' 과 '병' 에게 통보하고 충분히 설명해야 한다.<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제8조(이용료 납부)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			① '을' 은 전월 1일부터 말일까지의 이용료를 매월 <?=$R['pay_day1'] ? $R['pay_day1'] : '말';?>일에 정산하고 '갑' (또는 '병')에게 <?=$R['pay_day2'] ? $R['pay_day2'] : '5';?>일까지 <?=$str?>를 통보한다.<br>
			② '갑' 은 매월 <?=$R['pay_day3'] ? $R['pay_day3'] : '15';?>일까지 <?=$orgBank ? $orgBank.'로 ' : '';?>본인부담금을 납부 한다. 다만, 납부일이 공휴일인 경우에는 그 익일로 한다.<br>
			③ '을' 은 '갑' 이 납부한 비용에 대해서는 별지 제4호서식의 장기요양급여 납부확인서를 발급한다.<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제9조(재계약)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			다음 각호에 해당하는 경우에는 이를 반영한 계약서를 재작성한다.
		</p>
		<p style="text-align:justify; padding-left:30px;">
			1. 제2조의 계약기간이 만료된 경우<br>
			2. 장기요양 인정등급이 변경된 경우<br>
			3. 방문요양 급여비용 및 본인부담 비용이 변경된 경우<br>
			4. 기타 '갑' 과 '을' 이 필요한 경우<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제10조(건강관리)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			①'을' 은'갑' 의 건강 및 감염병 예방을 위하여 종사자들에게 연 1회 이상 건강진단을 실시하여야 한다.<br>
			②'을' 은 장기요양요원이 방문요양급여 제공도중 '갑' 에게 상해를 입혔을 경우 적절한 조치를 취해야 한다.<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제11조(위급 시 조치)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			① '을' 은 방문요양급여 제공시간에 '갑' 의 생명이 위급한 상태라고 판단된 때에는 '갑' (또는 '병')이 지정한 병원 또는 관련 의료기관으로 즉시 후송하고 '병' 에게 즉시 통보하여야 한다.<br>
			② '병' 은 제1항의 규정에 의한 통보를 받았을 때에는 신속하게 대처하여야 한다. 다만, 대처가 어려울 경우에는 우선 진료를 받을 수 있도록 조치하여야 한다.<br>
			③ '갑' 이 서비스 이용도중 사망하였을 경우'을' 은 즉시'병' 에게 통보한다.<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제12조(개인정보 보호의무)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			① '갑' 은 본인의 개인정보에 대해 알 권리가 있다.<br>
			② '을' 은 '갑' 의 개인정보를 관계규정에 따라 보호하여야 한다.<br>
			③ '을' 은 장기요양서비스 제공에 필요한 '갑' 의 개인 정보 자료를 수집하고 활용하며 동자료를 노인장기요양보험 운영주체 등에게 관계규정에 따라 제출할 수 있다.<br>
			④ '을' 은 개인정보수집 및 활용을 하고자 하는 경우에는 '갑' 에게 별지 제5호서식의 개인정보제공 및 활용 동의서를 받아야 한다.<br>
			⑤ '을' 은 '갑' 의 사생활을 존중하고, 업무상 알게 된 개인정보는 철저히 비밀을 보장한다.<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제13조(기록 및 공개)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			'을' 은 '갑' 의 생활과 장기요양서비스에 관한 모든 내용을 상세히 관찰하여 정확히 기록하고, '갑' (또는 '병' )이 요구할 경우에는 표준양식에 의거한 기록을 공개하여야 한다.
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제14조(배상책임)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			① '을' 은 다음 각호의 경우에는'갑' (또는'병' )에게 배상의무가 있으며 배상책임은 관계규정에 따른다.
		</p>
		<p style="text-align:justify; padding-left:30px;">
			1. 장기요양요원(또는 '을' )의 고의나 과실로 인하여 '갑' 을 부상케 하는 등 건강을 상하게 하거나 사망에 이르게 하였을 때<br>
			2. 장기요양요원(또는 '을' )의 학대(노인복지법 제1조의2 제4호의 노인학대 및 같은 법 제39조의9의 금지행위를 말한다)로 인하여 '갑' 의 건강을 상하게 하거나, 사망에 이르게하였을 때<br>
		</p>
		<p style="text-align:justify; padding-left:15px;">
			② 다음 각 호에 해당되는 경우에는 '갑' (또는 '병' )은 '을' 에게 배상을 요구할 수 없다.
		</p>
		<p style="text-align:justify; padding-left:30px;">
			1. 자연사 또는 질환에 의하여 사망 하였을 때<br>
			2. '을' 이 선량한 주의의무를 다했음에도 임의로 외출하여 상해를 당했거나 사망 하였을 때<br>
			3. 천재지변으로 인하여 상해를 당했거나 사망 하였을 때<br>
			4. '갑' 의 고의 또는 중과실로 인하여 상해를 당했거나 사망하였을 때<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제15조(기타)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			① 이 계약서에서 규정하지 않은 사항은 민법이나 사회상규에 따른다.<br>
			② 부득이한 사정으로 소송이 제기될 경우 '갑' (또는 '병' ) 또는 시설이 속한 소재지역의 관할법원으로 한다.<br>
		</p>
	</p>
	<p style="<?=$bold;?> padding-left:5px; margin-top:10px;">제16조(별첨사항)</p>
	<p style="text-align:justify;">
		<p style="text-align:justify; padding-left:15px;">
			위에 기술되지 않은 특이사항은 #별첨1 에 정의 되어 있다.
		</p>
	</p>

	<p style="<?=$bold;?> text-align:center; padding-top:20px; font-size:15pt;"><?=$myF->dateStyle($R['reg_dt'],'KOR');?></p>
	<p style="<?=$bold;?> padding-top:20px; font-size:11pt;">상기 내용에 대한 충분한 설명을 '갑' 과 '병' 에게 제공하였습니다.</p>
	<p style="<?=$bold;?> padding-top:20px; font-size:11pt;"><?
		if ($orgJikin){?><div style="position:absolute; z-index:1; text-align:right; margin-top:-30px; padding-right:10px;"><img src="<?=$orgJikin;?>" style="width:80px;"></div><?}?>
		<div style="position:absolute; z-index:2;">
			<div style="<?=$bold;?> float:right; width:auto; padding-right:20px;">(인)</div>
			<div style="<?=$bold;?> float:right; width:auto; padding-right:30px;"><?=$orgMg;?></div>
			<div style="<?=$bold;?> float:center; width:auto; text-align:center;">'을' 기관장</div>
		</div>
	</p>
	<p style="<?=$bold;?> padding-top:20px; font-size:11pt;">상기 내용을 읽고 그 내용에 동의합니다.</p>
	<p style="<?=$bold;?> padding-top:20px; font-size:11pt;"><?
		if ($signTG){?><div style="position:absolute; z-index:2; text-align:right; margin-top:-15px;"><img src="<?=$signTG;?>" style="width:110px;"></div><?}?>
		<div style="position:absolute; z-index:1;">
			<div style="<?=$bold;?> float:right; width:auto; padding-right:20px;">(인)</div>
			<div style="<?=$bold;?> float:right; width:auto; padding-right:30px;"><?=$name;?></div>
			<div style="<?=$bold;?> float:center; width:auto; text-align:center;">'갑' 이용자</div>
		</div>
	</p>
	<p style="<?=$bold;?> padding-top:40px; font-size:11pt;"><?
		if ($signPT){?><div style="position:absolute; z-index:2; text-align:right; margin-top:-15px;"><img src="<?=$signPT;?>" style="width:110px;"></div><?}?>
		<div style="position:absolute; z-index:1;">
			<div style="<?=$bold;?> float:right; width:auto; padding-right:20px;">(인)</div>
			<div style="<?=$bold;?> float:right; width:auto; padding-right:30px;"><?=$grdNm;?></div>
			<div style="<?=$bold;?> float:center; width:auto; text-align:center;">'병' 대리인</div>
		</div>
	</p>
</p><?

if ($contract['other_text1']){?>
	<p style="page-break-before:always;">
		<p style="<?=$bold;?> padding-left:5px;">#별첨1</p>
		<p style="padding-left:5px;"><?=nl2br($contract['other_text1']);?></p>
	</p><?
}?>

<p style="page-break-before:always;">
	<p style="padding-left:5px; font-size:9pt;">[별지 제5호서식]</p>
	<div style="border:2px solid BLACK;">
		<div style="font-size:15pt; font-weight:bold; text-align:center; padding:70px;">개인정보 제공 및 활용 동의서</div>
		<div style="padding-left:20px;">성명 : <?=$name;?> (생년월일 : <?=$myF->dateStyle($myF->issToBirthday($jumin),'KOR');?>)</div>
		<div style="padding-left:20px; padding-top:10px;">주소 : <?=$addr;?></div>
		<div style="padding:30px 0 0 40px; font-weight:bold;">1. 수집 및 이용목적</div>
		<div style="padding-left:60px;">
			○ 장기요양급여 관련 정보<br>
			○ 이용자의 지역연계 관련 정보<br>
			○ 관련기관 정보제공 요청시 필요한 정보<br>
			○ 기타 목적사업 수행에 필요한 정보<br>
			○ 대상자 급여 관련에 필요한 정보의 활용<br>
			○ 제공기관 간의 서비스 연계와 관련사항에 관한 대상자 정보 제공<br>
			○ 관련기관 정보제공 요청시 제공<br>
			○ 장기요양계획, 욕구조사, 정기요양서비스 질 수준 향상 등에 활용<br>
		</div>
		<div style="padding:30px 0 0 40px; font-weight:bold;">2. 이용기간 및 보유기간</div>
		<div style="padding-left:60px;">
			○ 이용기간 : 급여개시일부터 급여계약기간 만료(해지)일까지로 함<br>
			○ 보유기간 : 급여개시일부터 급여계약기간 만료(해지) 후 5년까지로 함<br>
		</div>
		<div style="padding:30px 0 0 40px; font-weight:bold;">3. 수집항목</div>
		<div style="padding-left:60px;">
			○ 개인식별정보(성명, 주민등록번호, 외국인등록번호)<br>
			○ 개인정보(주소, 연락처, 가족사항)<br>
			○ 사진<br>
		</div>
		<div style="padding:40px 0 0 40px;">상기 본인은 개인정보를 제공하고 활용하는 것에 동의합니다.</div>
		<div style="padding:40px 0 0 40px; text-align:center;"><?=$myF->dateStyle($R['reg_dt'],'KOR');?></div>
		<div style="padding:40px 20px 100px 40px; text-align:right;"><?
			if ($signTG){?><div style="position:absolute; text-align:right; margin-top:-15px;"><img src="<?=$signTG;?>" style="width:110px;"></div><?}?>
			<div style="width:100%;">
				<div style="float:right; width:100px; text-align:left; padding-left:30px;">(인)</div>
				<div style="float:right; width:150px;"><?=$name;?></div>
				<div style="float:right; width:150px;">이용자 :</div>
			</div><?
			if ($signPT){?><div style="position:absolute; text-align:right; margin-top:15px;"><img src="<?=$signPT;?>" style="width:110px;"></div><?}?>
			<div style="width:100%; padding-top:30px;">
				<div style="float:right; width:100px; text-align:left; padding-left:30px;">(인)</div>
				<div style="float:right; width:150px;"><?=$grdNm;?></div>
				<div style="float:right; width:150px;">보호자 :</div>
			</div>
		</div>
	</div>
</p>
<!--</div-->

<object id="factory" style="display:none"
	classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"
	codebase="../activex/smsx.cab#Version=7,5,0,20">
</object>
<script type="text/javascript">
	$(window).scroll(function() {
		$('#ID_BTN_PRT').css('top',($(window).scrollTop()+20)+'px');
	});

	function printPage(){
		$('#ID_BTN_PRT').hide();
		$('#ID_DIV_TBL1').css('position','absolute');

		factory.printing.header = ""; //머릿말 설정
		factory.printing.footer = "<?=$orgNm;?>(<?=$orgTel;?>)&b &p of &P"; //꼬릿말 설정
		factory.printing.portrait = true; //출력방향 설정: true-세로, false-가로
		factory.printing.leftMargin = 20.0; //왼쪽 여백 설정
		factory.printing.topMargin = 20.0; //위쪽 여백 설정
		factory.printing.rightMargin = 20.0; //오른쪽 여백 설정
		factory.printing.bottomMargin = 15.0; //아래쪽 여백 설정
		//factory.printing.printBackground = true; //배경이미지 출력 설정:라이센스 필요
		//factory.printing.Print(false); //출력하기
		factory.printing.Preview();
		//factory.printing.Print(true, document.all.getElementById('ID_DIV_BODY'));

		$('#ID_DIV_TBL1').css('position','');
		$('#ID_BTN_PRT').show();
	}

	//printPage();
</script>
<?
	Unset($R);
	include_once('../inc/_footer.php');
?>
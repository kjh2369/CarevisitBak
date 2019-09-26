<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	/*
	 *	기관연결정보
	 */

	$userId = $_SESSION["userCode"];
	$orgNo	= $_POST['orgNo'];
	$year	= Date('Y');
	$today	= Date('Ymd');

	//계약이력
	$sql = 'SELECT	start_dt, cont_dt, from_dt, to_dt, cont_com, rs_cd, rs_dtl_cd, rs_str
			FROM	cv_reg_info
			WHERE	org_no = \''.$orgNo.'\'
			ORDER	BY cont_dt DESC';

	$contR = $conn->_fetch_array($sql);
	$contRCnt = SizeOf($contR);

	for($i=0; $i<$contRCnt; $i++){
		if ($contR[$i]['from_dt'] <= $today && ($contR[$i]['to_dt'] ? $contR[$i]['to_dt'] : '99991231') >= $today){
			$contNowR = $contR[$i];
			break;
		}
	}

	if (!$contNowR){
		$sql = 'SELECT	start_dt, cont_dt, from_dt, to_dt, cont_com, rs_cd, rs_dtl_cd, rs_str
				FROM	cv_reg_info
				WHERE	org_no   = \''.$orgNo.'\'
				AND		from_dt <= \''.$today.'\'
				ORDER	BY from_dt DESC
				LIMIT	1';

		$contNowR = $conn->get_array($sql);
	}

	if ($contNowR['cont_dt']){
		$contGbn = '계약일자';
		$contTerm= '계약기간';
		$contDt = $myF->dateStyle($contNowR['cont_dt'],'.');
	}

	//휴대폰
	$sql = 'SELECT	mobile
			FROM	mst_manager
			WHERE	org_no = \''.$orgNo.'\'';

	$mobile = $conn->get_data($sql);

	//기관정보
	$sql = 'SELECT	DISTINCT
					m00_store_nm AS org_nm
			,		m00_cont_date AS cont_dt1
			,		m00_start_date AS cont_dt2
			,		m00_mname AS mg_nm
			,		m00_ctel AS phone
			,		m00_fax_no AS fax
			,		m00_email AS email
			,		m00_caddr1 AS addr
			,		m00_caddr2 AS addr_dtl
			,		m00_ccode AS biz_no
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			AND		m00_del_yn= \'N\'
			ORDER	BY m00_mkind';

	$row = $conn->get_array($sql);

	$orgNm = $row['org_nm'];

	/*
	if ($row['cont_dt1']){
		$contGbn = '계약일자';
		$contTerm= '계약기간';
		$contDt = $myF->dateStyle($row['cont_dt1'],'.');
	}else{
		$contGbn = '<span style="color:BLUE;">계약예정</span>';
		$contTerm= '<span style="color:BLUE;">연결기간</span>';
		$contDt = $myF->dateStyle($row['cont_dt2'],'.');
	}
	*/

	if (!$contNowR['cont_dt']){
		$contGbn = '계약일자';
		$contTerm= '<span style="color:BLUE;">연결기간</span>';
		$contDt = '';
	}

	if ($contNowR['rs_cd'] == '4'){
		$contGbn = '<span style="color:RED;">해지일자</span>';
	}

	$mgNm	= $row['mg_nm'];
	$phone	= $myF->phoneStyle($row['phone'],'.');
	$fax	= $myF->phoneStyle($row['fax'],'.');
	$email	= $row['email'];
	$addr	= $row['addr'].' '.$row['addr_dtl'];
	$bizNo	= $row['biz_no'];

	Unset($row);


	//계약정보
	/*$sql = 'SELECT	*
			FROM	cv_reg_info
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		from_dt <= \''.$today.'\'
			AND		CASE WHEN to_dt = \'\' THEN \'99991231\' ELSE to_dt END >= \''.$today.'\'';*/
	$sql = 'SELECT	*
			FROM	cv_reg_info
			WHERE	org_no	= \''.$orgNo.'\'
			AND		from_dt = \''.$contNowR['from_dt'].'\'';

	$row = $conn->get_array($sql);

	if (!$row){
		$sql = 'SELECT	from_dt
				FROM	cv_reg_info
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		from_dt <= \''.$today.'\'
				ORDER	BY to_dt DESC
				LIMIT	1';
		$tmpDt = $conn->get_data($sql);
		if ($tmpDt) $curDt = $tmpDt;

		$sql = 'SELECT	from_dt
				FROM	cv_reg_info
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		from_dt	>= \''.$today.'\'
				ORDER	BY from_dt
				LIMIT	1';
		$tmpDt = $conn->get_data($sql);
		if ($tmpDt) $curDt = $tmpDt;

		$sql = 'SELECT	*
				FROM	cv_reg_info
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		from_dt <= \''.$curDt.'\'
				AND		to_dt	>= \''.$curDt.'\'';

		$row = $conn->get_array($sql);
	}

	$tmpContCom = Array('1'=>'굿이오스','2'=>'지케어','3'=>'케어비지트');

	/*
	$tmpRs = Array('3'=>'신규','1'=>'서비스','2'=>'일시중지','4'=>'해지','9'=>'기타');
	$tmpDtlRs['3'] = Array('01'=>'신규연결','02'=>'기간연장','03'=>'재연결','04'=>'본사');
	$tmpDtlRs['1'] = Array('01'=>'신규계약','02'=>'재계약','03'=>'기간연장','04'=>'중지해제');
	$tmpDtlRs['2'] = Array('01'=>'사용료미납','02'=>'기관요청','03'=>'장기미사용');
	$tmpDtlRs['4'] = Array('01'=>'계약기간만기','02'=>'기관요청','03'=>'사용료미납','04'=>'미계약','05'=>'장기미사용','06'=>'무료기간연장');
	$tmpDtlRs['9'] = Array('99'=>'기타');
	*/

	include_once('./center_rs_set.php');
	$tmpRs = $setRs;
	$tmpDtlRs = $setDtlRs;

	$rsCd = $tmpRs[$row['rs_cd']].'-'.$tmpDtlRs[$row['rs_cd']][$row['rs_dtl_cd']].($row['taxbill_yn'] == 'Y' ? ' / 세금계산서 발행기관' : '');

	/*
	$acctGbn = $row['acct_gbn'];

	if ($acctGbn == '1'){
		$acctGbn = 'CMS';

		$sql = 'SELECT	GROUP_CONCAT(cms_no)
				FROM	cv_cms_list
				WHERE	org_no = \''.$orgNo.'\'';

		$acctCms = $conn->get_data($sql);

		if ($acctCms) $acctGbn .= '('.$acctCms .')';
	}else if ($acctGbn == '2'){
		$acctGbn = '무통장';
	}else if ($acctGbn == '2'){
		$acctGbn = '가상계좌';
	}
	*/

	$sql = 'SELECT	bill_gbn, cms_no
			FROM	cv_bill_info
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		del_flag = \'N\'
			AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
			AND		to_dt	>= DATE_FORMAT(NOW(), \'%Y%m%d\')';

	$R = $conn->get_array($sql);

	if ($R['bill_gbn'] == '1'){
		//CMS
		$acctGbn = 'CMS('.$R['cms_no'].')';
	}else{
		//무통장
		$acctGbn = '무통장';
	}


	$fromDt	= $myF->dateStyle($row['from_dt'],'.');
	$toDt	= $myF->dateStyle($row['to_dt'],'.');

	Unset($row);


	/*
	//연결정보
	$sql = 'SELECT	b02_date
			,		b02_homecare
			,		b02_voucher
			,		care_support
			,		care_resource
			,		care_area
			,		care_group
			,		from_dt
			,		to_dt
			,		conn_gbn
			,		b02_other
			,		cms_cd
			,		CASE WHEN from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\') AND to_dt >= DATE_FORMAT(NOW(),\'%Y-%m-%d\') THEN \'Y\' ELSE \'N\' END AS use_yn
			FROM	b02center
			WHERE	b02_center = \''.$orgNo.'\'';

	$row = $conn->get_array($sql);

	$connDt = $myF->dateStyle($row['b02_date'],'.');
	$svcHomecare = $row['b02_homecare'];
	$svcNurse = SubStr($row['b02_voucher'],0,1);
	$svcOld = SubStr($row['b02_voucher'],1,1);
	$svcBaby = SubStr($row['b02_voucher'],2,1);
	$svcDis = SubStr($row['b02_voucher'],3,1);
	$svcSupport = $row['care_support'];
	$svcResource = $row['care_resource'];
	$area = $row['care_area'];
	$group = $row['care_group'];
	$connGbn = $myF->orgConnectGbn($row['conn_gbn']);
	//$memo = nl2br(StripSlashes($row['b02_other']));
	$CMSNo = $row['cms_cd'];
	$useYn = $row['use_yn']; //사용여부

	if (!$fromDt && !$toDt){
		$fromDt = $myF->dateStyle($row['from_dt'],'.');
		$toDt = $myF->dateStyle($row['to_dt'],'.');
	}

	Unset($row);
	*/
	$connDt = $myF->dateStyle($contNowR['start_dt'],'.');


	//최근 메모
	$sql = 'SELECT	reg_nm, subject, contents, insert_dt, update_dt
			FROM	cv_memo
			WHERE	memo_type=\'1\'
			AND		org_no	= \''.$orgNo.'\'
			AND		del_flag= \'N\'
			ORDER	BY insert_dt DESC
			LIMIT	1';

	$row = $conn->get_array($sql);

	if ($row) $memo = '작성일시 : '.str_replace('-','.',$row['insert_dt']).' / 최종수정일시 : '.str_replace('-','.',$row['update_dt']).' / 제목 : '.stripslashes($row['subject']).' / 작성자 : '.$row['reg_nm'].'<br>'.nl2br(stripslashes($row['contents']));

	Unset($row);


	/*
	if ($useYn == 'N'){
		$rsCd = '<span style="color:BLUE;">'.$tmpRs['4'].'-'.$tmpDtlRs['4']['01'].'</span>';
	}
	*/

	/*
	$sql = 'SELECT	svc_cd
			FROM	sub_svc
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		from_dt <= NOW()
			AND		to_dt	>= NOW()
			AND		del_flag = \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	$svcDAN = '<span style="margin-right:20px; color:#BDBDBD;">주야간보호</span>';
	$svcWMD = '<span style="margin-right:20px; color:#BDBDBD;">복지용구</span>';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['svc_cd'] == '5'){
			$svcDAN = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">주야간보호</span>';
		}else if ($row['svc_cd'] == '7'){
			$svcWMD = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">복지용구</span>';
		}
	}

	$conn->row_free();

	if ($svcHomecare == 'Y'){
		$svcHomecare = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">장기요양</span>';
	}else{
		$svcHomecare = '<span style="color:#BDBDBD; margin-right:20px;">장기요양</span>';
	}

	if ($svcNurse == 'Y'){
		$svcNurse = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">가사간병</span>';
	}else{
		$svcNurse = '<span style="margin-right:20px; color:#BDBDBD;">가사간병</span>';
	}

	if ($svcOld == 'Y'){
		$svcOld = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">노인돌봄</span>';
	}else{
		$svcOld = '<span style="margin-right:20px; color:#BDBDBD;">노인돌봄</span>';
	}

	if ($svcBaby == 'Y'){
		$svcBaby = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">산모신생아</span>';
	}else{
		$svcBaby = '<span style="margin-right:20px; color:#BDBDBD;">산모신생아</span>';
	}

	if ($svcDis == 'Y'){
		$svcDis = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">장애인활동지원</span>';
	}else{
		$svcDis = '<span style="margin-right:20px; color:#BDBDBD;">장애인활동지원</span>';
	}

	$svcVoucher = $svcNurse.$svcOld.$svcBaby.$svcDis;

	if ($svcSupport == 'Y'){
		$svcSupport = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">재가지원</span>';
	}else{
		$svcSupport = '<span style="margin-right:20px; color:#BDBDBD;">재가지원</span>';
	}

	if ($svcResource == 'Y'){
		$svcResource = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">자원연계</span>';
	}else{
		$svcResource = '<span style="margin-right:20px; color:#BDBDBD;">자원연계</span>';
	}

	$svcHomeold = $svcSupport.$svcResource;

	if ($svcSupport == 'Y' || $svcResource == 'Y'){
		if ($area && $group){
			$sql = 'SELECT	area_nm
					FROM	care_area
					WHERE	area_cd = \''.$area.'\'';

			$areaStr = $conn->get_data($sql);

			$sql = 'SELECT	group_nm
					FROM	care_group
					WHERE	area_cd	= \''.$area.'\'
					AND		group_cd= \''.$group.'\'';

			$groupStr = $conn->get_data($sql);

			$svcHomeold .= '<span style="margin-right:20px;">광역 : '.$areaStr.'</span>';
			$svcHomeold .= '<span style="margin-right:20px;">시.군.구 : '.$groupStr.'</span>';
		}
	}
	*/

	$svcDAN = '<span style="margin-right:20px; color:#BDBDBD;">주야간보호</span>';
	$svcWMD = '<span style="margin-right:20px; color:#BDBDBD;">복지용구</span>';
	$svcHomecare = '<span style="color:#BDBDBD; margin-right:20px;">장기요양</span>';
	$svcNurse = '<span style="margin-right:20px; color:#BDBDBD;">가사간병</span>';
	$svcOld = '<span style="margin-right:20px; color:#BDBDBD;">노인돌봄</span>';
	$svcBaby = '<span style="margin-right:20px; color:#BDBDBD;">산모신생아</span>';
	$svcDis = '<span style="margin-right:20px; color:#BDBDBD;">장애인활동지원</span>';
	$svcSupport = '<span style="margin-right:20px; color:#BDBDBD;">재가지원</span>';
	$svcResource = '<span style="margin-right:20px; color:#BDBDBD;">자원연계</span>';

	$sql = 'SELECT	svc_cd
			FROM	cv_svc_fee
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_gbn = \'1\'
			AND		del_flag= \'N\'
			AND		\''.$today.'\' BETWEEN from_dt AND to_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['svc_cd'] == '11'){ $svcHomecare = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">장기요양</span>';
		}else if ($row['svc_cd'] == '14'){ $svcDAN = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">주야간보호</span>';
		}else if ($row['svc_cd'] == '15'){ $svcWMD = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">복지용구</span>';
		}else if ($row['svc_cd'] == '21'){ $svcNurse = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">가사간병</span>';
		}else if ($row['svc_cd'] == '22'){ $svcOld = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">노인돌봄</span>';
		}else if ($row['svc_cd'] == '23'){ $svcBaby = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">산모신생아</span>';
		}else if ($row['svc_cd'] == '24'){ $svcDis = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">장애인활동지원</span>';
		}else if ($row['svc_cd'] == '41'){ $svcSupport = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">재가지원</span>';
		}else if ($row['svc_cd'] == '42'){ $svcResource = '<span class="bold" style="color:RED;">V</span><span class="bold" style="margin-right:20px;">자원연계</span>';
		}
	}

	$svcVoucher = $svcNurse.$svcOld.$svcBaby.$svcDis;
	$svcHomeold = $svcSupport.$svcResource;

	$conn->row_free();


	//계약서 및 등록증 요구내역
	$sql = 'SELECT	doc_type, cancel_yn
			FROM	cv_doc
			WHERE	org_no = \''.$orgNo.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$arrDoc[$row['doc_type']] = ($row['cancel_yn'] ? $row['cancel_yn'] : 'Y');
	}

	$conn->row_free();


	//메모 리스트 콜
	$cgMemo = '<col width="40px"><col width="120px"><col width="150px"><col width="70px"><col>';
?>
<script type="text/javascript">
	var screenX = 0, screenY = 0;
	var gWin = new Array();

	$(document).ready(function(){
		var h = $(this).height() - $('#divMemo').offset().top - 3;
		$('#divMemo').height(h);

		var tmp = opener.GetScreenInfo();

		screenX = tmp['X'];
		screenY = tmp['Y'];

		setTimeout('lfLoasSvcCnt()', 100);

		self.focus();
	});

	window.onunload = function(){
		for(var i=0; i<gWin.length; i++) if (gWin[i]) gWin[i].close();
	}

	function lfMoveYear(pos){
		$('#lblYear').text(parseInt($('#lblYear').text()) + pos);

		lfLoasSvcCnt();
	}

	function lfLoasSvcCnt(){
		for(var i=1; i<=12; i++){
			$('#svcTCnt_'+i).html('0&nbsp;');
			$('#inAmt_'+i).html('0&nbsp;');
			$('#dftAmt_'+i).html('0&nbsp;');
			$('#nowAmt_'+i).html('0');
			$('#calAmt_'+i).html('0');
			$('#difAmt_'+i).html('0&nbsp;');
			$('#smsCnt_'+i).html('0&nbsp;');
		}

		$.ajax({
			type :'POST'
		,	url  :'./center_iljung.php'
		,	data :{
				'year':$('#lblYear').text()
			,	'orgNo':'<?=$orgNo;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var row = data.split('?');

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var difAmt = __str2num(col['nowAmt']) - __str2num(col['calAmt']);

						$('#svcTCnt_'+i).html(__num2str(__str2num(col['careCnt'])+__str2num(col['bathNurseCnt']))+'&nbsp;');
						$('#svcCCnt_'+i).html(__num2str(col['careCnt'])+'&nbsp;');
						$('#svcBNCnt_'+i).html(__num2str(col['bathNurseCnt'])+'&nbsp;');
						//$('#inAmt_'+i).html(__num2str(col['amt'])+'&nbsp;');
						$('#inAmt_'+i).html(__num2str(col['inAmt'])+'&nbsp;');
						$('#nowAmt_'+i).html(__num2str(col['nowAmt']));
						$('#calAmt_'+i).html(__num2str(col['calAmt']));
						$('#difAmt_'+i).css('color', difAmt == 0 ? '' : difAmt > 0 ? '' : 'RED').html(__num2str(difAmt)+'&nbsp;');
						$('#clsYn_'+i).html(col['clsYn'] == 'Y' ? 'Y' : 'N');
						$('#smsCnt_'+i).html(__num2str(col['smsCnt'])+'/'+__num2str(col['lmsCnt'])+'&nbsp;');
						$('#dftAmt_'+i).html(__num2str(col['dftAmt'])+'&nbsp;').css('color',col['dftAmt'] > 0 ? 'RED' : '');
					}
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfReg(type){
		var url = './center_connect_reg.php';

		switch(type){
			case 'Contract':
				if (__str2num($('#ID_CELL_CONT_CNT').text()) > 1){
					var width = 600;
					var height = 500;

					url = './center_cont_list.php';
				}else{
					var width = 1000;
					var height = 700;
				}

				break;

			case 'Branch':
				var width = 250;
				var height = 300;
				break;

			case 'Account':
				var width = 250;
				var height = 330;
				break;

			case 'Service':
				var width = 1100;
				var height = 500;
				break;

			case 'Deposit':
				var width = 800;
				var height = 650;
				break;

			case 'Tax':
				var width = 600;
				var height = 450;
				break;

			case 'StopSet':
				var width = 600;
				var height = 400;
				break;

			default:
				return;
		}

		var left = screenX;
		var top = screenY;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'CONNECT_REG_'+type, option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'orgNo':'<?=$orgNo;?>'
			,	'type':type
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', 'CONNECT_REG_'+type);
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfResult(type){
		//location.reload();
	}

	function lfReMakeFee(month){
		if ($('#clsYn_'+month).text() == 'Y'){
			alert('마감되어 계산 할 수 없습니다.');
			return;
		}

		if (!confirm('청구금액을 다시 계산하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./center_FEE_MAKE_save.php'
		,	data:{
				'orgNo'	:'<?=$orgNo;?>'
			,	'company':''
			,	'year'	:$('#lblYear').text()
			,	'month'	:month
			,	'loc'	:'INFO'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				var v = __parseVal(result);

				$('#calAmt_'+month).html(__num2str(v['acctAmt']));

				var difAmt = __str2num($('#nowAmt_'+month).text()) - __str2num(v['acctAmt']);
				$('#difAmt_'+month).css('color',difAmt >= 0 ? '' : 'RED').html(__num2str(difAmt)+'&nbsp;');
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfSetAmt(gbn, month, amt){
		var amt = prompt('현재 청구금액을 입력하여 주십시오.',amt);

		if (amt == null) return;

		$.ajax({
			type:'POST'
		,	url:'./center_set_nowamt.php'
		,	data:{
				'orgNo'	:'<?=$orgNo;?>'
			,	'year'	:$('#lblYear').text()
			,	'month'	:month
			,	'gbn':gbn
			,	'amt'	:amt.split(',').join('')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var v = __parseVal(data);

				for(var i=1; i<=12; i++){
					if (gbn == 'NOW'){
						$('#nowAmt_'+i).html(__num2str(v[i]));

						var difAmt = __str2num(v[i]) - __str2num($('#calAmt_'+i).text());
						$('#difAmt_'+i).css('color',difAmt >= 0 ? '' : 'RED').html(__num2str(difAmt)+'&nbsp;');
					}else if (gbn == 'DFT'){
						$('#dftAmt_'+i).html(__num2str(v[i])+'&nbsp;');
					}
				}

				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfMemo(gbn,seq){
		var left = (screen.availWidth - (width = 800)) / 2, top = (screen.availHeight - (height = 600)) / 2;
		var winIdx = gWin.length;

		if (!seq) seq = '';

		gWin[winIdx] = window.open('./center_memo_pop.php?orgNo=<?=$orgNo;?>','ORGMEMO','left='+left+',top='+top+', width='+width+', height='+height+', scrollbars=no, status=no, resizable=no');
		gWin[winIdx].focus();
	}

	function ShowPayIn(orgNo){
		window.open('./pop_payin_list.php?orgNo='+orgNo, 'POP_PAYIN_LIST', 'left='+((screen.availWidth - 1024) / 2)+', top='+((screen.availHeight - 600) / 2)+', width=1024, height=600, status=no, menubar=no, toolbar=no, resizeable=no').focus();
	}

	function lfResetPw(orgNo){
		if (!orgNo) return;
		if (!confirm('비밀번호를 초기화 하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./center_pw_reset.php'
		,	data:{
				'orgNo':orgNo
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
				}else{
					alert(result);
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>
<div class="title title_border">기관연결정보</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
		<col width="90px">
	</colgroup>
	<tbody>
		<tr>
			<td class="bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col width="130px">
						<col width="50px">
						<col width="200px">
						<col width="50px">
						<col width="80px">
						<col width="50px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">기관코드</th>
							<td class="left"><?=$orgNo;?></td>
							<th class="center">기관명</th>
							<td class="left"><?=$orgNm;?></td>
							<th class="center">대표자</th>
							<td class="left"><?=$mgNm;?></td>
							<th class="center">휴대폰</th>
							<td class="left last"><?=$myF->phoneStyle($mobile, '.');?></td>
						</tr>
						<tr>
							<th class="center">전화번호</th>
							<td class="left"><?=$phone;?></td>
							<th class="center">FAX</th>
							<td class="left"><?=$fax;?></td>
							<th class="center">이메일</th>
							<td class="left last" colspan="3"><?=$email;?></td>
						</tr>
						<tr>
							<th class="center">주소</th>
							<td class="left" colspan="3"><?=$addr;?></td>
							<th class="center">사업자</th>
							<td class="left last" colspan="3"><?=$myF->bizStyle($bizNo);?></td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="50px">
						<col width="70px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center" rowspan="3">이용<br>서비스</th>
							<th class="center">장기요양</th>
							<td class="left last"><?=$svcHomecare.$svcDAN.$svcWMD;?></td>
						</tr>
						<tr>
							<th class="center">바우처</th>
							<td class="left last"><?=$svcVoucher;?></td>
						</tr>
						<tr>
							<th class="center">재가노인</th>
							<td class="left last"><?=$svcHomeold;?></td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col width="90px">
						<col width="70px">
						<col width="150px">
						<col width="70px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">연결일자</th>
							<td class="left last"><?=$connDt;r?></td>
							<td class="left" style="color:BLUE;" colspan="2"><span id="ID_CELL_CONT_CNT"><?=$contRCnt;?></span>건의 계약이 있습니다.</td>
							<th class="center">연결코드</th>
							<td class="left last"><?=$rsCd;?></td>
						</tr>
						<tr>
							<th class="center"><?=$contGbn;?></th>
							<td class="left"><?=$contDt;?></td>
							<th class="center"><?=$contTerm;?></th>
							<td class="left"><?=$fromDt.($toDt ? ' ~ '.$toDt : '');?></td>
							<th class="center">청구구분</th>
							<td class="left last"><?=$acctGbn;?></td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="90px">
						<col width="60px" span="12">
					</colgroup>
					<tbody>
						<tr>
							<td class="last" colspan="13">
								<div class="left bold">※<span style="color:RED;">사용기준</span>의 <span style="color:RED;">년월</span>로 작성된 데이타입니다.</div>
							</td>
						</tr>
						<tr>
							<th class="center">
								<div style="float:left; width:auto; padding-left:3px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
								<div style="float:left; width:auto; padding-left:2px; padding-right:2px; font-weight:bold;" id="lblYear"><?=$year?></div>
								<div style="float:left; width:auto;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
							</th><?
							for($i=1; $i<=12; $i++){?>
								<th class="center <?=$i == 12 ? 'last' : '';?>"><?=$i;?>월</th><?
							}?>
						</tr>
						<tr>
							<th class="center">고객수</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="text-align:right;" id="svcTCnt_<?=$i;?>">0&nbsp;</td><?
							}?>
						</tr>
						<tr>
							<th class="center">방문요양</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="text-align:right;" id="svcCCnt_<?=$i;?>">0&nbsp;</td><?
							}?>
						</tr>
						<tr>
							<th class="center">목욕/간호</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="text-align:right;" id="svcBNCnt_<?=$i;?>">0&nbsp;</td><?
							}?>
						</tr>
						<tr>
							<td class="last" style="height:2px;" colspan="13"></td>
						</tr>
						<tr>
							<th class="center">미납금액</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="text-align:right; background-color:#E0FFDB;"><a href="#" onclick="lfSetAmt('DFT', '<?=$i;?>', $(this).text());" id="dftAmt_<?=$i;?>">0</a></td><?
							}?>
						</tr>
						<tr>
							<td class="last" style="height:2px;" colspan="13"></td>
						</tr>
						<tr>
							<th class="center">현재청구</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="text-align:right; background-color:#FFF2E6;"><?
								if ($gHostNm == 'admin' && $userId != 'geecare'){?>
									<a href="#" onclick="lfSetAmt('NOW', '<?=$i;?>', $(this).text()); return false;" id="nowAmt_<?=$i;?>">0</a>&nbsp;</td><?
								}else{?>
									<span id="nowAmt_<?=$i;?>">0</span>&nbsp;<?
								}
							}?>
						</tr>
						<tr>
							<th class="center">계산청구</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="text-align:right; background-color:#FFF2E6;"><?
								if ($gHostNm == 'admin' && $userId != 'geecare'){?>
									<a href="#" onclick="lfReMakeFee('<?=$i;?>'); return false;" id="calAmt_<?=$i;?>">0</a>&nbsp;</td><?
								}else{?>
									<span id="calAmt_<?=$i;?>">0</span>&nbsp;<?
								}
							}?>
						</tr>
						<tr>
							<th class="center">차액</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="text-align:right; background-color:#FFF2E6;" id="difAmt_<?=$i;?>">0&nbsp;</td><?
							}?>
						</tr>
						<tr>
							<td class="last" style="height:2px;" colspan="13"></td>
						</tr>
						<tr>
							<th class="center">입금금액</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="text-align:right; background-color:#E0FFDB;" id="inAmt_<?=$i;?>">0&nbsp;</td><?
							}?>
						</tr>
						<tr>
							<td class="last" style="height:2px;" colspan="13"></td>
						</tr>
						<tr>
							<th class="center">SMS건수</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" style="text-align:right;" id="smsCnt_<?=$i;?>">0&nbsp;</td><?
							}?>
						</tr>
						<tr>
							<td class="last" style="height:2px;" colspan="13"></td>
						</tr>
						<tr>
							<th class="center">마감여부</th><?
							for($i=1; $i<=12; $i++){?>
								<td class="center <?=$i == 12 ? 'last' : '';?>" id="clsYn_<?=$i;?>"></td><?
							}?>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head last">메모</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="last">
								<div id="divMemo" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll; padding:5px;"><?=$memo;?></div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="center top last">
				<div class="left" style="margin-top:5px;">
					<div style="">
						<span class="btn_pack m"><button onclick="lfReg('Contract');">계약이력</button></span><br>
						<!--<span class="btn_pack m"><button onclick="lfReg('Branch');">지사등록</button></span><br>
						<span class="btn_pack m"><button onclick="lfReg('Account');">출금계좌</button></span><br>-->
						<span class="btn_pack m"><button onclick="lfReg('Service');">계약서비스</button></span><br>
					</div>
					<div style="margin-top:10px;">
						<span class="btn_pack m"><button onclick="lfReg('Deposit');">무통장입금</button></span><br>
						<span class="btn_pack m"><button onclick="lfReg('Tax');">세금계산서</button></span><br>
					</div>
					<div style="margin-top:10px;">
						<span class="btn_pack m"><button onclick="lfReg('StopSet');">미납,중지</button></span><br>
						<!--span class="btn_pack m"><button onclick="$('#ID_BODY_CONTHIS').show();">계약이력</button></span><br-->
						<span class="btn_pack m"><button onclick="lfMemo('MemoList');">메모작성</button></span><br>
					</div>
					<div style="margin-top:10px;">
						<span class="btn_pack m"><button onclick="ShowPayIn('<?=$orgNo;?>');">입금내역</button></span><br>
					</div>
					<div style="margin-top:10px;">
						<span class="btn_pack m"><button onclick="lfResetPw('<?=$orgNo;?>');">비밀번호 초기화</button></span><br>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<input id="orgNo" type="hidden" value="<?=$orgNo;?>">
<div id="ID_BODY_CONTHIS" style="position:absolute; left:0; top:250px; width:681px; height:350px; background-color:WHITE; border:2px solid #4374D9; display:none;">
	<div style="position:absolute; text-align:right; left:0; top:-20px;">
		<a href="#" onclick="$('#ID_BODY_CONTHIS').hide();"><img src="../image/btn_exit.png"></a>
	</div>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="70px">
			<col width="150px">
			<col width="150px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">계약일자</th>
				<th class="head">계약기간</th>
				<th class="head">계약회사</th>
				<th class="head last">사유코드</th>
			</tr>
		</thead>
		<tbody><?
			if (is_array($contR)){
				$no = 1;

				for($i=0; $i<$contRCnt; $i++){?>
					<tr>
						<td class="center"><?=$no;?></td>
						<td class="center"><?=$myF->dateStyle($contR[$i]['cont_dt'],'.');?></td>
						<td class="center"><?=$myF->dateStyle($contR[$i]['from_dt'],'.');?> ~ <?=$myF->dateStyle($contR[$i]['to_dt'],'.');?></td>
						<td class="">&nbsp;<?=$tmpContCom[$contR[$i]['cont_com']];?></td>
						<td class="last">&nbsp;<?=$tmpRs[$contR[$i]['rs_cd']];?>-<?=$tmpDtlRs[$contR[$i]['rs_cd']][$contR[$i]['rs_dtl_cd']];?></td>
					</tr><?

					$no ++;
				}
			}?>
		</tbody>
	</table>
</div>
<div id="ID_BODY_MEMO" style="position:absolute; left:180px; top:42px; width:600px; height:555px; background-color:WHITE; border:2px solid #4374D9; display:none;">
	<div style="position:absolute; text-align:right; left:0; top:-20px;">
		<a href="#" onclick="$('#ID_BODY_MEMO').hide();"><img src="../image/btn_exit.png"></a>
	</div>
	<table class="my_table" style="width:100%;">
		<colgroup><?=$cgMemo;?></colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">작성일시</th>
				<th class="head">제목</th>
				<th class="head">작성자</th>
				<th class="head last">
					<div style="float:right; width:auto; margin-right:5px;"><span class="btn_pack small"><button onclick="lfMemo('MemoReg');">작성</button></span></div>
					<div style="float:center; width:auto; padding-top:1px;">비고</div>
				</th>
			</tr>
		</thead>
	</table>
	<div id="ID_TBL_MEMO_LIST" style="overflow-x:hidden; overflow-y:scroll; height:526px;"></div>
</div>
<?
	Unset($contR);
	include_once('../inc/_footer.php');
?>
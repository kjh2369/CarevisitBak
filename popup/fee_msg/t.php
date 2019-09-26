<?
	include_once("../../inc/_db_open.php");
	include_once("../../inc/_login.php");
	include_once("../../inc/_myFun.php");

	$orgNo = $_SESSION["userCenterCode"];
	$type = $_GET['type'];
	//$date = StrToTime($myF->dateAdd('month', -1, Date('Ymd'), 'Ymd'));

	$year = $_GET['year'];
	$month= $_GET['month'];

	if ($year && $month){
		$tmpYm = $year.($month < 10 ? '0' : '').$month;
	}else{
		$sql = 'SELECT	yymm
				FROM	cv_claim_yymm
				LIMIT	1';

		$tmpYm = $conn->get_data($sql);
	}

	$date = StrToTime($myF->dateAdd('month', -1, $tmpYm.'01', 'Ymd'));


	$year = Date("Y",$date);
	$month = IntVal(Date("m",$date));
	$yymm = $year.($month < 10 ? "0" : "").$month;
	$lastday= $myF->dateAdd('day', -1, $yymm.'01', 'd');

	$claimDt = $myF->dateAdd('month', 1, $yymm.'01', 'Ymd');
	$claimY  = SubStr($claimDt, 0, 4);
	$claimM  = IntVal(SubStr($claimDt, 4, 2));
	$claimYm = $claimY.($claimM < 10 ? '0' : '').$claimM;
	
	$sql = 'SELECT	bill_kind
			FROM	cv_bill_info
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		del_flag = \'N\'
			AND		LEFT(from_dt, 6) <= \''.$tmpYm.'\'
			AND		LEFT(to_dt, 6)	 >= \''.$tmpYm.'\'
			';
	$bill_kind = $conn->get_data($sql);
	
	if($tmpYm>=201804){
		$bast15 = '24,200';
		$bast30 = '36,300';
	}else {
		$bast15 = '22,000';
		$bast30 = '33,000';
	}


	//청구구분
	$sql = 'SELECT	bill_gbn
			FROM	cv_bill_info
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		LEFT(from_dt, 6) <= \''.Date('Ym', $date).'\'
			AND		LEFT(to_dt, 6) >= \''.Date('Ym', $date).'\'';

	$billGbn = $conn->get_data($sql); //1:CMS, 2:무통장

	if ($type == 'CLAIM'){
		if ($billGbn == '1'){
			$tmpdt = $claimY.($claimM < 10 ? '0' : '').$claimM.'19';

			while(true){
				$w = Date('w', StrToTime($tmpdt));
				$loopflag = false;

				if ($w == 6 || $w == 0){
					$tmpdt = $myF->dateAdd('day', 1, $tmpdt, 'Ymd');
					$loopflag = true;
				}else{
					$sql = 'SELECT	holiday_name
							FROM	tbl_holiday
							WHERE	mdate = \''.$claimY.$claimM.'19\'
							';
					$tmphd = $conn->get_data($sql);

					if ($tmphd){
						$tmpdt = $myF->dateAdd('day', 1, $tmpdt, 'Ymd');
						$loopflag = true;
					}
				}

				if (!$loopflag) break;
			}

			$tmpday = Date('j', StrToTime($tmpdt));
			$infoMsg = '('.$claimY.'년 '.$claimM.'월 '.$tmpday.'일 CMS 출금예정)';
		}else{
			$infoMsg = '('.$claimY.'년 '.$claimM.'월 25일까지 무통장 입금부탁드립니다.<br>농협 301-0164-4623-31 (주)케어비지트)';
		}
	}


	if ($type == 'CLAIM'){
		$typeStr = '청구요금';
	}else{
		$typeStr = '사용요금';
	}


	//청구요금, 미납요금
	$sql = 'SELECT	amt, dft_amt
			FROM	cv_svc_acct_amt
			WHERE	org_no = \''.$orgNo.'\'
			AND		yymm <= \''.$yymm.'\'
			ORDER	BY yymm DESC
			LIMIT	1';

	$row = $conn->get_array($sql);

	$claimAmt = $row['amt'];
	$dftAmt = $row['dft_amt'];

	Unset($row);

	if ($claimYm < '201612') $dftAmt = 0;


	//서비스 항목
	$sql = 'SELECT	CONCAT(1, \'_\', svc_cd) AS cd
			,		svc_nm
			,		pro_cd
			,		unit_gbn
			,		day_cal
			FROM	cv_svc_main
			WHERE	parent_cd IS NOT NULL
			UNION	ALL
			SELECT	CONCAT(2, \'_\', svc_cd)
			,		svc_nm
			,		NULL
			,		unit_gbn
			,		day_cal
			FROM	cv_svc_sub
			WHERE	parent_cd IS NOT NULL';

	$fee = $conn->_fetch_array($sql, 'cd');


	$sql = "SELECT	cnt1amt, cnt2amt, cnt3amt, cnt4amt
			FROM	cv_stnd_fee_set
			WHERE	'$yymm' BETWEEN from_ym AND to_ym";

	$row = $conn->get_array($sql);

	$careFee15 = $row['cnt1amt']; //15인이하 기본금
	$careFee30 = $row['cnt2amt']; //30인이하 기본금
	$danFee9 = $row['cnt3amt']; //주야간보호 9명이하
	$danFee10 = $row['cnt4amt']; //주야간보호 10명이상

	$sql = "SELECT	t01_mkind AS svc_cd, COUNT(DISTINCT t01_jumin) AS cnt
			FROM	t01iljung
			WHERE	t01_ccode	= '$orgNo'
			AND		t01_del_yn	= 'N'
			AND		LEFT(t01_sugup_date, 6) = '$yymm'
			AND		CASE WHEN t01_mkind = '0' AND t01_svc_subcode = '200' THEN 1
						 WHEN t01_mkind = '5' THEN 1 ELSE 0 END = 1
			GROUP	BY t01_mkind";

	$tgtRow = $conn->_fetch_array($sql, 'svc_cd');

	if ($tgtRow['0']['cnt'] > 15){
		$careFee = $careFee30;
	}else{
		$careFee = $careFee15;
	}

	if ($tgtRow['5']['cnt'] >= 10){
		$danFee = $danFee10;
	}else{
		$danFee = $danFee9;
	}

	$sql = 'SELECT	SUM(CASE WHEN IFNULL(sms_type,\'SMS\') = \'LMS\' AND gbn != \'L\' THEN 2 ELSE 1 END)
			FROM	sms_his as his
			LEFT    JOIN sms_send_fail_log as log
			ON      log.call_seq = his.call_seq
			WHERE	org_no = \''.$orgNo.'\'
			AND		his.gbn		!= \'L\'
			AND     log.call_rst_cd is null
			AND		DATE_FORMAT(his.insert_dt,\'%Y%m\') = \''.$yymm.'\'';
	$smsCnt = $conn->get_data($sql);

	$sql = 'SELECT	COUNT(*)
			FROM	sms_his as his
			LEFT    JOIN sms_send_fail_log as log
			ON      log.call_seq = his.call_seq
			WHERE	his.org_no	= \''.$orgNo.'\'
			AND		his.gbn		= \'L\'
			AND     log.call_rst_cd is null
			AND		DATE_FORMAT(his.insert_dt,\'%Y%m\') = \''.$yymm.'\'
			';
	$lmsCnt = $conn->get_data($sql);

	$lmsPay = 0;

	if($lmsCnt>0){
		$lmsPay = 110 * $lmsCnt;
	}

	$sql = 'SELECT	COUNT(*)
			FROM	cv_svc_fee
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		svc_gbn	 = \'1\'
			AND		svc_cd	 = \'11\'
			AND		del_flag = \'N\'
			AND		LEFT(from_dt, 6) <= \''.$yymm.'\'
			AND		LEFT(to_dt, 6)   >= \''.$yymm.'\'';

	$careCnt = $conn->get_data($sql);


	$sql = "SELECT	svc_gbn, svc_cd, acct_gbn, stnd_cost, over_cost, limit_cnt, from_dt, to_dt, acct_yn, acct_from, acct_to
			FROM	cv_svc_fee
			WHERE	org_no = '$orgNo'
			AND		acct_yn = 'Y'
			AND		del_flag = 'N'
			AND		'$yymm' BETWEEN LEFT(from_dt,6) AND LEFT(to_dt,6)
			AND		'$yymm' BETWEEN LEFT(acct_from,6) AND LEFT(acct_to,6)";

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (/*$yymm >= '201512' && */$row['svc_gbn'] == '2' && $row['svc_cd'] == '11') continue;
		if ($row['svc_gbn'] == '1' && $row['svc_cd'] == '15') continue;

		if (!$tmpOrgCd['1_01']) $tmpOrgCd['1_01'] = $row['svc_gbn'].'_'.$row['svc_cd'];

		if ($row['acct_gbn'] == '1' && $row['svc_gbn'] == '1' && $row['svc_cd'] == '11'){
			//재가요양
			$row['stnd_cost'] = $careFee;
		}else if ($row['acct_gbn'] == '1' && $row['svc_gbn'] == '1' && $row['svc_cd'] == '14'){
			//주야간보호
			$row['stnd_cost'] = $danFee;
		}

		$cd = $row['svc_gbn'].'_'.$row['svc_cd'];

		if ($cd == '1_21' || $cd == '1_22' || $cd == '1_23' || $cd == '1_24'){
			$cd = '1_21';

			if ($data[$cd]['stndCost'] > 0) continue;
		}

		$data[$cd]['stndCost'] = $row['stnd_cost'];
		$data[$cd]['limitCnt'] = $row['limit_cnt'];
		$data[$cd]['overCost'] = $row['over_cost'];
		$data[$cd]['overCnt'] = 0;
		$data[$cd]['days'] = 0;

		if ($cd == '1_11' && $tgtRow['0']['cnt'] > $data['1_11']['limitCnt']) $data[$cd]['overCnt'] = $tgtRow['0']['cnt'] - $data[$cd]['limitCnt'];
		if ($cd == '2_21' && $smsCnt > $data['2_21']['limitCnt']) $data[$cd]['overCnt'] = $smsCnt - $data[$cd]['limitCnt'];

		//월중간 시작과 종료시 일수로 계산한다.
		if ($fee[$cd]['dayCal'] == 'Y'){
			if ($yymm.'01' < $row['acct_from']){
				$diffDay = $lastday - $myF->dateDiff('d', $yymm.'01', $row['acct_from']);
				$data[$cd]['days'] = $diffDay;
			}else if ($yymm.$lastday > $row['to_dt']){
				$diffDay = $myF->dateDiff('d', $yymm.'01', $row['to_dt']);
				$data[$cd]['days'] = $diffDay;
			}else{
				$data[$cd]['days'] = 0;
			}
		}else{
			$data[$cd]['days'] = 0;
		}

		//일수계산(원단위절사)
		if ($data[$cd]['days'] > 0){
			$data[$cd]['stndCost'] = Floor($data[$cd]['stndCost'] / $lastday * $data[$cd]['days'] / 10) * 10;
		}

		//$totAmt += $data[$cd]['stndCost'] + $data[$cd]['overCost'] * $data[$cd]['overCnt'];
	}

	$conn->row_free();

	if ($data['1_11']['stndCost'] > 0 && $data['1_21']['stndCost'] > 0){
		$data['1_21']['stndCost'] = 0;
	}

	if (is_array($data)){
		foreach($data as $cd => $R){
			$totAmt += ($R['stndCost'] + $R['overCost'] * $R['overCnt']);
		}
	}

	//lms 문자요금 합산
	$totAmt += $lmsPay;

	$dcAmt = ($totAmt - $claimAmt) * -1; //할인금액

	if ($careCnt < 1){
		if ($data['1_14']['stndCost'] > 0) $data['1_14']['stndCost'] += $data['1_01']['stndCost'];
		if ($data['1_41']['stndCost'] + $data['1_42']['stndCost'] > 0) $data['1_41']['stndCost'] += $data['1_42']['stndCost'] + $data['1_01']['stndCost'];
	}

	//if ($data['1_11']['stndCost'] > 0) $data['1_11']['stndCost'] += $data['1_01']['stndCost'];
	if ($careCnt) $data['1_11']['stndCost'] += $data['1_01']['stndCost'];

	$claimAmt += $dftAmt;
?>
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title><?=$_SESSION['userCenterName'].' '.$typeStr;?> 안내</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv='imagetoolbar' content='no'>
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />

<style type="text/css">
body,a, abbr, acronym, address, applet, article, aside, audio,b, blockquote, big,center, canvas, caption, cite, code, command,datalist,del, details, dfn, div,
em, embed,fieldset, figcaption, figure, font, footer, form, h1, h2, h3, h4, h5, h6, header, hgroup, html,i, iframe, img, ins,kbd, keygen,label, legend, li,meter,nav, menu,
object, ol, output,p, pre, progress,q,s, samp, section, small, span, source, strike, strong, sub, sup,table, tbody, tfoot, thead, th, tr, tdvideo, tt,u, ul, var
{margin:0; padding:0; border:0; font-size:9pt;}

dl,dt,dd {margin:0; padding:0; border:0;}
 ul { list-style:none; overflow:hidden;}
 ul li{line-height:2em;}
table { border-collapse:collapse; border-spacing:0; table-layout:fixed; }
button { margin:0; padding:0; border:0; font:inherit; color:inherit; background:transparent; overflow:visible; cursor:pointer; line-height:0; vertical-align:middle; }
legend, caption ,hr { position:absolute; top:0; left:0; width:1px; height:1px; visibility:hidden; font-size:0; line-height:0; }
input { font:inherit; line-height:1em; font-size:9pt;}
select { height:20px; font:inherit; color:inherit; vertical-align:middle; font-size:9pt;}
textarea { font:inherit; color:inherit;}
img { vertical-align:top; border:none;}

legend, hr { overflow:hidden; position:absolute; top:0; left:0}
legend, hr, caption { visibility:hidden; font-size:0; width:0; height:0; line-height:0}

/* hidden */
.hidden, #contents .hidden { visibility:hidden; position:absolute; font-size:0; width:0; height:0; line-height:0; margin:0; padding:0; background:none;}

body { font-family: Dotum,Gulim,AppleGothic,Sans-serif; color:#2a2a2a;line-height:1.5em; font-size:9pt;}

/*table_box*/
.tbl_box,.tbl_box th,.tbl_box td{border:0}
.tbl_box{font-family:'돋움',dotum;font-size:12px; border-collapse:collapse; border-top:2px solid #828282;}
.tbl_box caption{display:none}
.tbl_box tfoot{background-color:#f5f7f9;font-weight:bold}
.tbl_box th{padding:8px 10px 8px;border-top:1px solid #cccccc;border-right:1px solid #cccccc;border-left:1px solid #cccccc; border-bottom:1px solid #cccccc; font-family:'돋움',dotum;font-size:9pt;font-weight:bold}
.tbl_box th{ background-color:#f0f0f0; line-height:1.5em;}
.tbl_box th.fw, .tbl_box th.fw{font-weight:normal;}
.tbl_box th.bg_blue, .tbl_box td.bg_blue{background-color:#f2f2f2;}
.tbl_box th.bg_blue2, .tbl_box td.bg_blue2{background-color:#f5f1fe;}
.tbl_box td{padding:6px 10px 6px;border:1px solid #cccccc; font-size:9pt; line-height:1.5em;}
.tbl_box td.ranking{font-weight:bold}
.tbl_box tbody.txt_left{text-align:left;}
.tbl_box tbody.txt_center {text-align:center;}
.tbl_box tbody.txt_right {text-align:right;}
.tbl_box td.ls1 {letter-spacing:1px;}
.tbl_box th.txt_center, .tbl_box td.txt_center, .tbl_box td ul.txt_center{text-align:center;}
.tbl_box th.txt_left, .tbl_box td.txt_left, .tbl_box td ul.txt_left{text-align:left;}
.tbl_box th.txt_right, .tbl_box td.txt_right, .tbl_box td ul.txt_right{text-align:right;}
.tbl_box td.bord_none{border-left:none; border-right:none;}
.tbl_width{width:600px;}
.tbl_width600{width:650px;}
.tbl_bg_none{background-image:none !important; background-color:#f7f7f7 !important;}
.tbl_bg_none2{background-image:none !important; background-color:#ffffff !important; border:none !important; *height:10px; _height:10px;}
.col_f6f6f6{ background:#f6f6f6}
.tbl_box td .input_txt{ border:1px solid #7f9db9; padding:5px 2px;}

/*table_box2*/
.tbl_box2,.tbl_box2 th,.tbl_box2 td{border:0}
.tbl_box2{font-family:'돋움',dotum;font-size:12px; border-collapse:collapse;}
.tbl_box2 caption{display:none}
.tbl_box2 tfoot{background-color:#f5f7f9;font-weight:bold}
.tbl_box2 th{padding:10px 10px 8px;border-top:1px solid #b4b4b4;border-right:1px solid #b4b4b4;border-left:1px solid #b4b4b4; border-bottom:1px solid #b4b4b4; font-family:'돋움',dotum;font-size:9pt;font-weight:bold}
.tbl_box2 th{ background-color:#fbf153; line-height:1.5em;}
.tbl_box2 th.bg_col{ background-color:#e3defd; line-height:1.5em;}
.tbl_box2 th.fw, .tbl_box th.fw{font-weight:normal;}
.tbl_box2 td{padding:14px 10px 10px;border:1px solid #b4b4b4; font-size:9pt; line-height:1.5em; background-color:#f9f9f9; }
.tbl_box2 td.f_size {font-size:18px;}
.tbl_box2 td.f_size span.col_red{color:#d50909; font-weight:bold;}
td.f_size span{font-size:18px;}
.tbl_box2 tbody.txt_cent {text-align:center;}
td.bold1, td span.bold1, p span.bold1{font-weight:bold; color:#d50909;}
td.bold2, td span.bold2{font-weight:bold;}
ul.f_wb li{font-weight:bold;}

p a{font-size:9pt; color:#777;}
p strong{font-size:9pt; color:#c21b06;}
p a.path_home{position:relative; top:4px;}
p a:hover, #right #location p a:focus, #right #loaction p a:active {text-decoration:none; }
h2{font-size:30px;}
h3{color:#2a589f; letter-spacing:-1px; word-spacing:-1px; font-size:17px; background:url(./img/icon_title.png) left 30% no-repeat; padding-left:18px;}
strong{color:#333;}
h4{margin-top:15px; color:#333; background:url(./img/icon_title2.gif) left 40% no-repeat; padding-left:10px;  font-size:14px; margin-left:2px;}
p { display:block; margin-top:10px; font-size:12px; }

h5{letter-spacing:-1px; word-spacing:-1px; font-size:12px; margin-left:16px; margin-top:15px; font-weight:normal;}
dl dt{background:url(/images/sub/body_title/icon_title2.gif) left 45% no-repeat; padding:1px 0 1px 8px; color:#333; letter-spacing:-1px; word-spacing:-1px; font-size:13px; margin-top:10px;}
ol.style_dec li{list-style-type:decimal;}
th.title1{background:url(./img/icon_title3.png) 15px 43% no-repeat; padding-left:28px;  background-color:#f0f0f0; line-height:1.5em;}

.s_month{margin-top:5px; margin-bottom:5px;}

.mg_top5{margin-top:5px;}
.mg_top10{margin-top:10px;}
.mg_top15{margin-top:15px;}
.mg_top20{margin-top:20px;}
.mg_top26{margin-top:26px;}
.mg_top25{margin-top:25px;}
.mg_top35{margin-top:35px;}

.mg_left0{margin-left:0;}
.mg_left8{margin-left:8px;}
.mg_left16{margin-left:16px;}
.mg_left20{margin-left:20px;}
.mg_btm15{margin-bottom:15px;}

td.pd_left50{padding-left:50px;}
td.pd_left20{padding-left:20px;}
th.pd_left25, td.pd_left25{padding-left:25px;}
td.pd_right20, tbody.pd_right20 td{padding-right:20px;}
th.pd_right25, td.pd_right25 td{padding-right:25px;}

.col_red, span.col_red{color:#d50909; font-weight:bold;}
.col_green{color:#800d7f;}
.col_green2{color:#07680e;}
.bg_ye{background-color:#eeffe5;}
.bg_ye2{background-color:#fdfbea;}
.col2{color:#0b6e93;}
.col3{color:#2a085d;}

.border0{border:0px;}
span.pd_right2{padding-right:2px;}

.div_sty{ width:600px; border-top:2px solid #0e69b0; border-bottom:1px solid #0e69b0; padding:10px;}
.div_sty span{color:#2a589f; font-size:17px; font-weight:bold}

	#print-button{
	 text-decoration:none;
	 float:right; position:absolute; top:115px; right:17px;
	 border:1px solid #046bb4; background-color:#0572bf; color:#fff; padding:5px; padding:3px 20px 2px;  min-width:120px; font-weight:bold
	}

@media print {
	@page {size:auto;}
		#print-button, #chkClose{display:none;}
}

#print-button:hover{ text-decoration:underline;}
</style>
<script type="text/javascript" src="../../js/script.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(window).focus();
	});

	function winClose(){
		if ($('input:checkbox[name="chkClose"]').attr('checked')){
			__setCookie("CLAIM_POP","DONE",1);
		}

		self.close();
	}

	function ShowDftList(){
		var objModal = new Object();
		showModalDialog('../../claim/claim_dftamt.php', objModal, 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:yes');
	}

	function cPrint() {
	  var origin = $("#wrap").html();
	  var pcontent = $("#print_out").html();
	  $("#wrap").html(pcontent);
	  window.print();
	  $("#wrap").html(origin);
	}

</script>
</head>

<body class="mg_left20 mg_top15 " style="margin-bottom:30px; ">

<h2  style="width:650px; height:70px; line-height:70px; color:#fff; letter-spacing:-1px; word-spacing:-1px; font-size:25px; background:url(./img/title.gif) left top no-repeat; padding-left:15px;">
	<span style="font-size:25px; color:#84e0fd;"><?=$_SESSION['userCenterName'];?></span> <?=$typeStr;?> 안내
</h2>
<!-- 사용 요금 계산 -->
<h3 class="mg_top35">사용요금 <?=$type == 'CLAIM' ? '청구' : '예정';?> 내역</h3><a href="javascript:window.print()" id="print-button">인쇄하기</a>
<?
if ($type != 'CLAIM'){?>
	<p>※ 예정금액이므로 실제 청구될 금액과 차이가 있을 수 있습니다.</p><?
}

if ($bill_kind == '1'){
	$yymmstr = $claimY.'년 '.$claimM.'월 ';
}else{
	$yymmstr = $year.'년 '.$month.'월 ';
}?>
<p style="margin-top:10px; width:650px; height:20px; padding:15px; background:#e4e6fa; border:1px solid #a0a4d0; border-bottom:none; font-size:16px; font-weight:bold; border-top:2px solid #3d58a8;">
	<?=$claimY;?>년 <?=$claimM;?>월 <?=$typeStr;?> : <span style="color:#d50909;font-size:24px;"><?=number_format($claimAmt);?></span>원 (<?=$yymmstr;?> 사용분)
</p><?
if ($type == 'CLAIM'){?>
	<p style="width:650px;  height:20px; padding:15px; padding-top:0; background:#e4e6fa; margin-top:0; border:1px solid #a0a4d0; border-top:none; font-size:14px; font-weight:bold; text-align:right;"><?=$infoMsg;?></p><?
}?>
</div>
<table class="tbl_box  tbl_width600 mg_top10" summary=" 서비스요금계산">
	<colgroup>
		<col width="16%">
		<col width="14%">
		<col width="21%">
		<col width="27%">
		<col width="*">
	</colgroup>
	<thead>
		<tr>
		<th scope="col" colspan="4" style="height:30px;">서비스</th>
		<th scope="col">금액(VAT포함)</th>
		</tr>
	</thead>
	<tbody class="txt_right pd_right20">
		<tr>
		<th colspan="4" class="" style="background-color:#fff; border-right:1px solid #fff; color:#403385;">
			<div style="float:right; width:auto;"><!--사용기본요금 :--></div><?
			if ($type == 'CLAIM'){?>
				<div style="float:left; width:auto;"><span  class=" bold1" style="margin-left:10px; color:#403385;">방문요양일정(목욕,간호 제외) 등록된 수급자수 :</span> <span class="col_red"><?=number_format($tgtRow['0']['cnt']);?> 명</span></div><?
			}?>
		</th>

		<td style="background-color:#fff;"><!--span style="font-weight:bold; color:#403385;" class="pd_right2"><?=number_format($data['1_01']['stndCost']);?></span>원--></td>
		</tr><?
		if ($type == 'CLAIM'){?>
			<tr>
			<th class="txt_right pd_right25" scope="rowgroup" rowspan="5" style="background-color:#f8f8f8">장기요양</th>
			<td rowspan="2">기본요금</td>
			<td>15명 까지</td>
			<td> <?//=number_format($careFee15 + $data['1_01']['stndCost']);?><?=$bast15;?>원</td>
			<td rowspan="2"><span class="pd_right2" style="color:red;"><?=number_format($data['1_11']['stndCost']);?></span>원</td>
			</tr><?
		}else{?>
			<tr>
				<th class="txt_right pd_right25" scope="rowgroup" rowspan="7" style="background-color:#f8f8f8">장기요양</th>
				<td  colspan="3"  class="txt_left pd_left20">재가요양(요양,목욕,간호)</td>
				<td rowspan="5"><span class="pd_right2"><?=number_format($data['1_11']['stndCost'] + $data['1_11']['overCost'] * $data['1_11']['overCnt']);?></span>원</td>
			</tr>
			<tr>
				<td  colspan="3" ><span  class=" bold1" style="margin-left:10px; color:#403385;">방문요양일정(목욕,간호 제외) 등록된 수급자수 :</span> <span class="col_red"><?=number_format($tgtRow['0']['cnt']);?> 명</span></td>
			</tr>
			<tr>
			<td rowspan="2">기본요금</td>
			<td>15명 까지</td>
			<td> <?=number_format($careFee15);?>원</td>
			</tr><?
		}?>
		<tr>
		<td>16명~30명 까지</td>
		<td> <?//=number_format($careFee30 + $data['1_01']['stndCost']);?><?=$bast30;?>원</td>
		</tr>
		<tr>
		<td>추가요금</td>
		<td><?//=number_format($data['1_11']['limitCnt'])?>30명 초과<br />(1인당/ <?//=number_format($data['1_11']['overCost'])?>660원)</td>
		<td><?=number_format($data['1_11']['overCnt'])?>명 초과</td>
		<td><?=number_format($data['1_11']['overCost'] * $data['1_11']['overCnt']);?>원</td>
		</tr>
		<tr>
		<td  colspan="3" class="txt_left pd_left20">
		<label for="id_oc_check_01_1">주‧야간보호<span class="bold1" style="font-weight:normal;" >(9명 이하)</span></label>
		</td>
		<td><span class="pd_right2"><?=$tgtRow['5']['cnt'] <= 9 ? number_format($data['1_14']['stndCost']) : 0;?></span>원</td>
		</tr>
		<tr>
		<td colspan="3" class="txt_left pd_left20">
		<label for="id_oc_check_01_2">주‧야간보호<span class="bold1" style="font-weight:normal;" >(10명 이상)</span></label>
		</td>
		<td><span class="pd_right2"><?=$tgtRow['5']['cnt'] >= 10 ? number_format($data['1_14']['stndCost']) : 0;?></span>원</td>
		</tr>
		<tr>
		<th class="txt_right pd_right25"scope="rowgroup" rowspan="2" style="background-color:#f8f8f8">바우처</th>
		<td colspan="3" class="txt_left pd_left20"><label for="id_check_02">장기요양 사용시 무료 / 바우처만 사용시 <?=number_format($data['1_01']['stndCost']);?> 원</label></td>
		<td rowspan="2"><span class="pd_right2"><?=number_format($data['1_21']['stndCost']);?></span>원</td>
		</tr>
		<tr>
		<td colspan="3" class="txt_left pd_left20">(가사간병, 노인돌봄, 산모신생아, 장애인활동지원)</td>
		</tr>
		<tr>
		<th class="txt_right pd_right25" scope="rowgroup" <?//rowspan="2"?> style="background-color:#f8f8f8">재가노인</th>
		<td colspan="3" class="txt_left pd_left20"><label for="id_check_06">재가지원, 자원연계</label></td>
		<td><span class="pd_right2"><?=number_format($data['1_41']['stndCost']);?></span>원</td>
		</tr>
		<!--<tr>
		<td colspan="3" class="txt_left pd_left20"><label for="id_check_07">자원연계</label></td>
		<td><span class="pd_right2"><?=number_format($data['1_42']['stndCost']);?></span>원</td>
		</tr>--><?
		if ($type != 'CLAIM'){?>
			<tr>
			<th class="txt_right pd_right25" scope="rowgroup" rowspan="6" style="background-color:#f8f8f8">부가서비스</th>
			<td colspan="3" class="txt_left pd_left20">스마트폰 업무관리 <span  class=" bold1" style="margin-left:10px;">무료(2015년 12월 청구요금부터)</span></td>
			<td><span class="pd_right2"><?=number_format($data['2_11']['stndCost']);?></span>원</td>
			</tr>
			<tr>
			<td colspan="3" class="txt_left pd_left20">
				<label for="id_check_09">SMS 문자서비스
			<td rowspan="4"><span class="pd_right2"><?=number_format($data['2_21']['stndCost'] + $data['2_21']['overCost'] * $data['2_21']['overCnt']);?></span>원</td>
			</tr><?
		}else{?>
			<tr>
			<th class="txt_right pd_right25" scope="rowgroup" rowspan="6" style="background-color:#f8f8f8">부가서비스</th>
			<td colspan="3" class="txt_left pd_left20">
				<label for="id_check_09">SMS 문자서비스
			<td rowspan="4"><span class="pd_right2"><?=number_format($data['2_21']['stndCost'] + $data['2_21']['overCost'] * $data['2_21']['overCnt']);?></span>원</td>
			</tr><?
		}?>
		<tr>
		<td colspan="3"><span  class=" bold1" style="margin-left:20px; text-align:center; color:#403385;">사용한 문자 건수 :</span> <span class=" bold1" style="color:red;"><?=number_format($smsCnt);?> 건</span></td>
		</tr>
		<tr>
		<td class="txt_center pd_left20">기본요금</td>
		<td><?//=number_format($data['2_21']['limitCnt']);?>300건/<?//=number_format($data['2_21']['stndCost']);?>5,500원</td>
		<td rowspan="2"><?=number_format($data['2_21']['overCnt']);?>건 초과<br><span class="bold1" style="font-weight:normal;" ><?=number_format($data['2_21']['overCost'] * $data['2_21']['overCnt']);?>원</span></td>
		</tr>
		<tr>
		<td class="txt_center pd_left20">추가요금</td>
		<td>1건당/<?//=number_format($data['2_21']['overCost']);?>22원</td>
		</tr>

		<tr>
		<td colspan="3" class="txt_left pd_left20">
			<label for="id_check_09">급여비용청구안내</label>
		</td>
		<td rowspan="2"><span class="pd_right2"><?=number_format($lmsPay);?></span>원</td>
		</tr>
		<tr>
			<td class="txt_center pd_left20">추가요금</td>
			<td>1건당/<?//=number_format($data['2_21']['overCost']);?>110원</td>
			<td ><span  class=" bold1" style=" text-align:center; color:#403385;">사용한 문자 건수 :</span> <span class=" bold1" style="color:red;"><?=number_format($lmsCnt);?> 건</span></td>
		</tr><?

		if ($type == 'CLAIM'){?>
			<tr>
			<th rowspan="4" colspan="3" class="txt_center pd_left25"  style="background-color:#eaedf3; height:30px; font-size:14px;"><?=$claimY;?>년 <?=$claimM;?>월</th>
			<th class="txt_center pd_left25"  style="background-color:#eaedf3; height:30px; font-size:14px; height:30px; line-height:1em;">사용요금</th>
			<td class="txt_right pd_left20" style="line-height:2em; background-color:#eaedf3; line-height:1em;"><span id="usePay" class=" bold1 pd_right2" style="font-size:16px;"><?=number_format($totAmt);?></span>원</td>
			</tr>
			<tr>
			<th class="txt_center pd_left25"  style="background-color:#eaedf3; height:30px; font-size:14px; line-height:1em;">할인요금</th>
			<td class="txt_right pd_left20" style="line-height:2em; background-color:#eaedf3; line-height:1em;"><span id="usePay" class=" bold1 pd_right2" style="font-size:16px;"><?=number_format($dcAmt);?></span>원</td>
			</tr><?
			if ($claimYm >= '201612'){?>
				<tr>
				<th class="txt_center pd_left25"  style="background-color:#eaedf3; height:30px; font-size:14px; line-height:1em;">미납합계<!--[<a href="#" onclick="ShowDftList();" style="font-size:14px; color:blue;">내역보기</a>]--></th>
				<td class="txt_right pd_left20" style="line-height:2em; background-color:#eaedf3; line-height:1em;"><span id="dftPay" class=" bold1 pd_right2" style="font-size:16px;"><?=number_format($dftAmt);?></span>원</td>
				</tr><?
			}?>
			<tr>
			<th class="txt_center pd_left25"  style="background-color:#eaedf3; height:30px; font-size:14px; line-height:1em;">청구요금</th>
			<td class="txt_right pd_left20" style="line-height:2em; background-color:#eaedf3; line-height:1em;"><span id="usePay" class=" bold1 pd_right2" style="font-size:16px;"><?=number_format($claimAmt);?></span>원</td>
			</tr><?
		}else{?>
			<tr>
			<th colspan="4" class="txt_center pd_left25"  style="background-color:#eaedf3; height:40px; font-size:14px;"><?=$year;?>년 <?=$month;?>월 사용요금</th>
			<td class="txt_right pd_left20" style="line-height:2em; background-color:#eaedf3;"><span id="usePay" class=" bold1 pd_right2" style="font-size:16px;"><?=number_format($totAmt);?></span>원</td>
			</tr><?
		}?>
	</tbody>
</table>
<div id="chkClose" style="text-align:right; padding-top:10px; padding-right:20px;">
	<label><input name="chkClose" type="checkbox" onclick="winClose();">하루동안 열지않기</label>
</div>
</body>
</html>
<?
	include_once("../../inc/_db_close.php");
?>
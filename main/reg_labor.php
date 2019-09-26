<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title>인사노무서비스 안내문</title>
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
.tbl_box{font-family:'돋움',dotum;font-size:12px; border-collapse:collapse; border-top:2px solid #0e69b0;}
.tbl_box caption{display:none}
.tbl_box tfoot{background-color:#f5f7f9;font-weight:bold}
.tbl_box th{padding:7px 10px 4px;border-top:1px solid #dcdcdc;border-right:1px solid #dcdcdc;border-left:1px solid #dcdcdc; border-bottom:1px solid #e5e5e5; font-family:'돋움',dotum;font-size:9pt;font-weight:bold}
.tbl_box th{ background-color:#f7faff}
.tbl_box td{padding:6px 10px 4px;border:1px solid #e5e5e5; font-size:9pt;}
.tbl_box td.ranking{font-weight:bold}
.tbl_box tbody.txt_left{text-align:left;}
.tbl_box th.txt_center, .tbl_box td.txt_center, .tbl_box td ul.txt_center{text-align:center;}
.tbl_box td.bord_none{border-left:none; border-right:none;}
.tbl_width{width:600px;}
.tbl_width600{width:600px;}
.tbl_bg_none{background-image:none !important; background-color:#f7f7f7 !important;}
.tbl_bg_none2{background-image:none !important; background-color:#ffffff !important; border:none !important; *height:10px; _height:10px;}
.col_f6f6f6{ background:#f6f6f6}
.tbl_box td .input_txt{ border:1px solid #7f9db9; padding:5px 2px;}

p a{font-size:9pt; color:#777;}
p strong{font-size:9pt; color:#c21b06;}
p a.path_home{position:relative; top:4px;}
p a:hover, #right #location p a:focus, #right #loaction p a:active {text-decoration:none; }
h2{font-size:30px;}
h3{color:#2a589f; letter-spacing:-1px; word-spacing:-1px; font-size:17px; margin-top:20px;}
strong{color:#333;}
 h4{margin-top:5px; margin-left:16px; color:#333;}
p { display:block; margin-top:10px; font-size:12px;}

h5{letter-spacing:-1px; word-spacing:-1px; font-size:12px; margin-left:16px; margin-top:15px; font-weight:normal;}
dl dt{background:url(/images/sub/body_title/icon_title2.gif) left 45% no-repeat; padding:1px 0 1px 8px; color:#333; letter-spacing:-1px; word-spacing:-1px; font-size:13px; margin-top:10px;}
ol.style_dec li{list-style-type:decimal;}

.mg_top5{margin-top:5px;}
.mg_top10{margin-top:10px;}
.mg_top15{margin-top:15px;}
.mg_top26{margin-top:26px;}

.mg_left0{margin-left:0;}
.mg_left8{margin-left:8px;}
.mg_left16{margin-left:16px;}
.mg_left20{margin-left:20px;}
.mg_btm15{margin-bottom:15px;}

.border0{border:0px;}

.div_sty{ width:600px; border-top:2px solid #0e69b0; border-bottom:1px solid #0e69b0; padding:10px;}
.div_sty span{color:#2a589f; font-size:17px; font-weight:bold}
</style>

</head>

<script type="text/javascript" src="../js/script.js"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		lfResize();

		$('input:radio[name="optGbn1"]').unbind('click').bind('click',function(){
			lfSetPay();
		});

		$('input:checkbox[name^="chkGbn"]').unbind('click').bind('click',function(){
			var obj = $(this).attr('name');
			var chk = $(this).attr('checked');

			$('input:checkbox[name="'+obj+'"]').attr('checked',false);
			$(this).attr('checked',chk);

			lfSetPay();
		});

		self.focus();
	});

	function lfResize(){
		var t = $('#divCal').offset().top;
		var h = $(document).height() - t - 20;

		$('#divTop').height(h);
	}

	function lfSetPay(){
		var gbn1 = $('input:radio[name="optGbn1"]:checked').val();
		var gbn2 = $('input:checkbox[name="chkGbn2"]:checked').val();
		var gbn3 = $('input:checkbox[name="chkGbn3"]:checked').val();
		var pay = 0;

		if (gbn1 == '2') pay = 10000;
		if (gbn2 == '1') pay = 30000;
		if (gbn2 == '2') pay = 50000;
		if (gbn2 == '3') pay = 70000;
		if (gbn3 == '1') pay += 50000;
		if (gbn3 == '2') pay += 90000;
		if (gbn3 == '3') pay += 120000;

		$('#usePay').text(__num2str(pay));
	}

	function lfContract(){
		if (!$('#bankNm').val()){
			alert('은형명을 입력하여 주십시오.');
			$('#bankNm').focus();
			return;
		}

		if (!$('#bankNo').val()){
			alert('계좌번호를 입력하여 주십시오.');
			$('#bankNo').focus();
			return;
		}

		if (!$('#bankAcct').val()){
			alert('예금주를 입력하여 주십시오.');
			$('#bankAcct').focus();
			return;
		}

		if (!$('#bankSSN').val()){
			alert('개인통장인 경우는 주민번호를 법인통장인 경우는 사업자번호를 입력하여 주십시오.');
			$('#bankSSN').focus();
			return;
		}

		var dt = $('input[name="bankDt"]:checked').val();

		if (dt == '1'){
			dt = '10일';
		}else{
			dt = '25일';
		}

		var msg = '';

		msg += '월 이용료 '+$('#usePay').text()+'원이';
		msg += '\n\n은행명    : '+$('#bankNm').val();
		msg += '\n계좌번호 : '+$('#bankNo').val();
		msg += '\n예금주    : '+$('#bankAcct').val();
		msg += '\n에서 매월 '+dt+'에 자동출금됩니다.';
		msg += '\n\n이에 동의 하십니까?';
		msg += '\n\n동의하시면 확인을 클릭하여 주십시오.';

		if (!confirm(msg)) return;
	}
</script>

<body class="mg_left20 " style="margin-bottom:30px;">

<div id="divTop" style="height:100px; overflow-x:hidden;overflow-y:scroll;">
<p class="mg_top26 mg_btm15 ">
	<img src="../img/txt1.png" alt="㈜굿이오스는 재가기관의 어려운 부분인 인사노무와 관련 '노무법인 정' 을 파트너로 하여 기관은 작은 비용으로 최선의 서비스를 받게끔 하오니 많은 이용 바랍니다." />
</p>
<p class="mg_top26 mg_btm15 ">
	<img src="../img/top_tel.png" alt="대표 공인노무사 이상규  016-712-9397 " />
</p>
<h2><img src="../img/title1.png" alt="인사,노무 사용서비스 계약표" class="mg_top15" /></h2>

<!-- 1.재가기관기본-->
<h3>1.재가기관 기본</h3>
<ul class="mg_left16 mg_top5 ">
	<li>- 4대보험 취득, 상실 신고업무 대행</li>
	<li>- 케어비지트(carevisit)프로그램 내 커뮤니티의 노무상담 사용(답변)<br />&nbsp;&nbsp;(문서작성 요청, 컨설팅에 관계되는 부분은 제외)</li>
</ul>

<table class="tbl_box  tbl_width600 mg_top5" summary=" 재가기관기본">
	<colgroup>
		<col width="30%">
		<col width="30%">
		<col width="10%">
		<col width="20%">
	</colgroup>
	<thead>
		<tr>
		<th>구분</th>
		<th>요금</th>
		<th>확인</th>
		<th>비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
		<td>30인 이하</td>
		<td>무료</td>
		<td class="txt_center"><input type="radio" id="optGbn11" name="optGbn1"  value="1" checked/></td>
		<td rowspan="2" class="txt_center">VAT별도</td>
		</tr>
		<tr>
		<td>30인 초과</td>
		<td>월 10,000원</td>
		<td class="txt_center"><input type="radio" id="optGbn12" name="optGbn1"  value="2"/></td>
		</tr>
	</tbody>
</table>
<!-- //1.재가기관기본-->

<!-- 2.	재가기관 전문서비스제공-->
<h3>2.	재가기관 전문서비스제공(1항의 내용은 포함됨)</h3>
<ul class="mg_left16 mg_top5 ">
	<li>- 각종 노동법 및 근로기준법에 관련한 문서작성 도움.</li>
	<li>- 정 노무법인에서 발행하는 인사노무관련 소식지제공</li>
	<li>- 기관의 요청에 의한 사업장 방문상담 업무</li>
</ul>

<table class="tbl_box  tbl_width600 mg_top5" summary=" 재가기관 전문서비스제공">
	<colgroup>
		<col width="30%">
		<col width="30%">
		<col width="10%">
		<col width="20%">
	</colgroup>
	<thead>
		<tr>
		<th>구분</th>
		<th>요금</th>
		<th>확인</th>
		<th>비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
		<td>30인 이하</td>
		<td>월 30,000원</td>
		<td class="txt_center"><input type="checkbox" id="chkGbn21" name="chkGbn2"  value="1"/></td>
		<td rowspan="3" class="txt_center">VAT별도</td>
		</tr>
		<tr>
		<td>31인 ~ 50인 까지</td>
		<td>월 50,000원</td>
		<td class="txt_center"><input type="checkbox" id="chkGbn22" name="chkGbn2"  value="2"/></td>
		</tr>
		<tr>
		<td>50인 초과</td>
		<td>월 70,000원</td>
		<td class="txt_center"><input type="checkbox" id="chkGbn23" name="chkGbn2"  value="3"/></td>
		</tr>
	</tbody>
</table>
<!-- //2.	재가기관 전문서비스제공-->

<!-- 3.	요양시설 전문서비스 제공-->
<h3>3.	요양시설 전문서비스 제공(1항, 2항의 내용은 포함됨)</h3>
<table class="tbl_box  tbl_width600 mg_top5" summary=" 요양시설 전문서비스 제공">
	<colgroup>
		<col width="30%">
		<col width="30%">
		<col width="10%">
		<col width="20%">
	</colgroup>
	<thead>
		<tr>
		<th>구분</th>
		<th>요금</th>
		<th>확인</th>
		<th>비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
		<td>9인 이하</td>
		<td>월 50,000원</td>
		<td class="txt_center"><input type="checkbox" id="chkGbn31" name="chkGbn3"  value="1"/></td>
		<td rowspan="3" class="txt_center">VAT별도</td>
		</tr>
		<tr>
		<td>10인 ~ 30인 까지</td>
		<td>월 90,000원</td>
		<td class="txt_center"><input type="checkbox" id="check32" name="chkGbn3"  value="2"/></td>
		</tr>
		<tr>
		<td>30인 초과</td>
		<td>월 120,000원</td>
		<td class="txt_center"><input type="checkbox" id="check33" name="chkGbn3"  value="3"/></td>
		</tr>
	</tbody>
</table>
<!--// 3.	요양시설 전문서비스 제공-->

<!-- 4.	CMS(자동출금 요청) 작성-->
<h3>4.	CMS(자동출금 요청) 작성</h3>
<table class="tbl_box  tbl_width600 mg_top5" summary=" 요양시설 전문서비스 제공">
	<colgroup>
		<col width="20%">
		<col width="30%">
		<col width="15%">
		<col width="35%">
	</colgroup>
	<thead>
		<tr>
		<th>은행명</th>
		<th>계좌번호</th>
		<th>예금주</th>
		<th>주민번호 또는 사업자번호</th>
		</tr>
	</thead>
	<tbody>
		<tr>
		<td><input type="text" id="bankNm" name="bank" style="width:100px;"  title="은행명 입력"  class="input_txt"/></td>
		<td><input type="text" id="bankNo" name="account" style="width:160px;"  title="계좌번호 입력" class="input_txt"/></td>
		<td><input type="text" id="bankAcct" name="name" style="width:70px;"  title="성명 입력" class="input_txt"/></td>
		<td><input type="password" id="bankSSN" name="jumin" style="width:190px;"  title="주민번호 또는 사업자번호 입력" class="input_txt" maxlength="13"/></td>
		</tr>
		<tr>
		<td>인출일자(매월)</td>
		<td colspan="3">
			<label>10일 <input type="radio" id="bankDt1" name="bankDt"  value="1"/></label>
			<label>25일 <input type="radio" id="bankDt2" name="bankDt"  value="2" checked/></label>
			<span style="padding-left:20px; color:red;">출금은 계약 익월부터 출금입니다.</span>
		</td>
		</tr>
	</tbody>
</table>
</div>
<!-- //4.CMS(자동출금 요청) 작성-->
<div id="divCal">
	<div class="mg_top26 div_sty" style="background-color:#f6f6f6;">
		<span> 월이용료:</span><span id="usePay" style="width:150px; height:12px; text-align:right; padding-right:5px;">0</span><span>원(VAT별도)</span>
	</div>
	<div style="text-align:center; width:600px; padding:10px; margin-top:10px">
		<a href="#" style="" onclick="lfContract();"><img src="../img/btn1.png" alt="계약진행"/></a>
		<a href="#" style="display:none;"><img src="../img/btn2.png" alt="계약서출력"/></a>
	</div>
</div>

</body>
</html>

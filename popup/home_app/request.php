<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_myFun.php');
	include_once('../../inc/_login.php');
	
	/*
	$sql = 'select max(seq)
			,	   rank
			,	   pay
	          from tablet_request
			 where org_no = \''.$_SESSION['userCenterCode'].'\'';
	$request = $conn -> get_array($sql);
	*/

?>
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title>신청하기</title>
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

body { font-family: Dotum,Gulim,AppleGothic,Sans-serif; color:#fff;line-height:1em; font-size:14px; font-weight:bold;}

#wrap{width:460px; height:380px;  background:#23374a;}
.bank{width:460px; height:104px; background:#e55151;}

/*write
============================================================*/
#WirteForm{margin-top:10px;}
#WirteForm p.check{ margin:0; padding-right:5px; text-align:right;}
#WirteForm p.check img{vertical-align:middle;}
.write_type,.write_type th,.write_type td{border:0; color:#fff;}  
.write_type{width:460px;border-top:2px solid #d1d4d6; table-layout:fixed;}  
.write_type caption{display:none}
.write_type th{padding:6px 0 6px 25px; border-bottom:1px solid #727f8b; border-right:1px solid #727f8b; background-color:#2e4255; font-weight:bold;text-align:left;}
.write_type th label	{display:block;}
.write_type th img{vertical-align:middle;}
.write_type td{padding:6px 0 6px 20px; border-bottom:1px solid #727f8b; text-align:left;}  
.write_type td select{border:1px solid #cccccc; vertical-align:middle; height:20px; line-height:18px; padding:2px;}
.write_type td input{border:1px solid #4a4470; vertical-align:middle; height:20px; padding-left:5px;}
.write_type td textarea{ border:1px solid #cccccc; width:90%; height:100px; padding:5px; color:#565960;}

p a{font-size:9pt; color:#777;} 
p strong{font-size:9pt; color:#c21b06;}
p a.path_home{position:relative; top:4px;}
p a:hover, #right #location p a:focus, #right #loaction p a:active {text-decoration:none; }
h2{font-size:30px;}	
strong{color:#fff;}
 h4{margin-top:5px; color:#2a589f; font-size:14px;}
p { display:block; margin-top:13px; font-size:12px; line-height:1.4em;}

ul.s_ctn_list {list-style-type:circle; overflow:hidden; padding-left:20px; width:600px; margin-top:15px;}
ul.s_ctn_list li{ margin-bottom:10px; }
ul.s_ctn_list li strong{color:#155597; padding-top:5px; display:block; height:12px;}

.mg_top5{margin-top:5px;}
.mg_top10{margin-top:10px;}
.mg_top15{margin-top:15px;}
.mg_top26{margin-top:26px;}
.mg_top18{margin-top:17px;}
.mg_top35{margin-top:35px;}

.mg_left0{margin-left:0;}
.mg_left8{margin-left:8px;}
.mg_left16{margin-left:16px;}
.mg_left28{margin-left:28px;}
.mg_left20{margin-left:20px;}
.mg_btm15{margin-bottom:15px;}
</style>
<script type="text/javascript" src="../../js/script.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function(){

	});

	function lfRequest(){
		
		var msg = '';
		
		msg += '\n금액 '+$('#BoardPay').text()+'원 입니다.';
		msg += '\n\n신청 하시겠습니까?';
		msg += '\n\n신청하시면 확인을 클릭하여 주십시오.';

		if (!confirm(msg)) return false;

		$('#txtBoardPay').val($('#BoardPay').text());
		
	
		document.WirteForm.action = './request_ok.php';
		
		return true;
	}

</script>
</head>

<body>
	<div id="wrap">
	<!-- 참가신청-->
	<h2><img src="./img/re_top_title.png" alt="기관홈페이지개발 신청하기"/></h2>
	<div class="bank">
	 <h3><img src="./img/re_title1.png" alt="이체 금액 및 계좌안내" class="mg_top18 mg_left28"/></h3>
	 <p class="mg_left28">
	  <strong style="display:block; color:#fff; font-size:14px;">- 금액 : 330,000&nbsp;원</strong>
	  <strong style="display:block; color:#fff;  font-size:14px; margin-top:5px;">- 농협 : 302-0164-4623-31&nbsp;&nbsp;&nbsp;&nbsp;예금주 : (주)케어비지트 </strong>
	 <p>
	</div>

  <!--h3><img src="./img/re_title2.png" alt="신청서작성" class="mg_top18 mg_left28"/></h3-->
	<form id="WirteForm" name="WirteForm" method="post" action="#" onsubmit="return lfRequest();">
	<input type="hidden" id="txtBoardSeq" name="txtBoardSeq" value=""/>
	<input type="hidden" id="txtBoardPay" name="txtBoardPay" value=""/>
		<table  class="write_type mg_top10" border="1" cellspacing="0" summary="">
			<caption>기관홈페이지개발 신청하기</caption>  
			<colgroup>  
				<col width="120" />
				<col width="*"/>    
			</colgroup> 
			<thead>
				<tr>
					<th scope="row"><label for="BoardCenter" style="font-size:14px; font-weight:bold">기관명</label></th>
					<td style="font-size:14px; font-weight:bold"><?=$_SESSION['userCenterName'];?></td>
				</tr>
			<tbody>
				<tr>
					<th scope="row"><label for="BoardPay" style="font-size:14px; font-weight:bold">금액</label></th>
					<td style="font-size:14px; font-weight:bold"><span id="BoardPay" style="width:50px; padding-right:5px; font-size:14px; font-weight:bold; color:#faf717;">330,000</span>원</td>
				</tr>
			</tbody>
		</table>
		<div style="text-align:center; width:460px;">
			<input type="image" alt="신청하기" src="img/btn_request2.png"  class="mg_top15" />
		</div>
	</form>
</div>
</body>
</html>

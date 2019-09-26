<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_myFun.php');
	include_once('../../inc/_login.php');
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

body { font-family: Dotum,Gulim,AppleGothic,Sans-serif; color:#2a2a2a;line-height:1.5em; font-size:9pt;}

/*write
============================================================*/
#WirteForm{margin-top:10px;}
#WirteForm p.check{ margin:0; padding-right:5px; text-align:right;}
#WirteForm p.check img{vertical-align:middle;}
.write_type,.write_type th,.write_type td{border:0;}  
.write_type{width:600px;border-top:2px solid #0e69b0; font-size:12px;table-layout:fixed;}  
.write_type caption{display:none}
.write_type th{padding:5px 0 5px 25px; border-bottom:1px solid #dcdcdc; border-right:1px solid #dcdcdc; background-color:#f7faff; color:#5877a3;font-weight:bold;text-align:left;}
.write_type th label	{display:block;}
.write_type th img{vertical-align:middle;}
.write_type td{padding:5px 0 4px 20px; border-bottom:1px solid #e0e4e7; text-align:left;}  
.write_type td select{border:1px solid #cccccc; vertical-align:middle; height:20px; line-height:18px; padding:2px;}
.write_type td input{border:1px solid #cccccc; vertical-align:middle; height:26px; line-height:22px; padding-left:5px;}
.write_type td textarea{ border:1px solid #cccccc; width:90%; height:100px; padding:5px; color:#565960;}

p a{font-size:9pt; color:#777;} 
p strong{font-size:9pt; color:#c21b06;}
p a.path_home{position:relative; top:4px;}
p a:hover, #right #location p a:focus, #right #loaction p a:active {text-decoration:none; }
h2{font-size:30px;}	
h3{color:#2a589f; letter-spacing:-1px; word-spacing:-1px; font-size:17px; margin-top:20px;}
strong{color:#333;}
 h4{margin-top:5px; color:#2a589f; font-size:14px;}
p { display:block; margin-top:15px; font-size:12px;}

ul.s_ctn_list {list-style-type:circle; overflow:hidden; padding-left:20px; width:600px; margin-top:15px;}
ul.s_ctn_list li{ margin-bottom:10px; }
ul.s_ctn_list li strong{color:#155597; padding-top:5px; display:block; height:12px;}

.mg_top5{margin-top:5px;}
.mg_top10{margin-top:10px;}
.mg_top15{margin-top:15px;}
.mg_top26{margin-top:26px;}
.mg_top35{margin-top:35px;}

.mg_left0{margin-left:0;}
.mg_left8{margin-left:8px;}
.mg_left16{margin-left:16px;}
.mg_left20{margin-left:20px;}
.mg_btm15{margin-bottom:15px;}
</style>
<script type="text/javascript" src="../../js/script.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript">

	function lfRequest(){	
		$.ajax({
			type :'POST'
		,	url  :'./request_ok.php'
		,	data :{
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('신청완료 되었습니다. \n\n 입금확인 후 사용가능합니다. \n\n 아래의 전화번호로 연락주십시오. \n\n 연락처 : 070-4893-6990');
					self.close();
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert('신청을 하였으나 \n\n 입금확인이 되지않았습니다. \n\n 아래의 전화번호로 확인하여주십시오. \n\n 연락처 : 070-4893-6990');
				}
			}
		,	error:function(){
			}
		}).responseXML;

	}

</script>
</head>

<body class="mg_left20 " style="margin-bottom:30px;">

<!-- 2014평가자료 신청-->
<h2><img src="./img/title1.png" alt="이체계좌 안내" class="mg_top26" /></h2>

<p style="margin-left:20px">
<strong style="display:block;  font-size:14px;">- 금액 : 110,000 (부가세포함)</strong>
<strong style="display:block; color:#4f4f4f;  font-size:14px; margin-top:5px;">- 농협 : 302-0983-1868-41&nbsp;&nbsp;&nbsp;&nbsp;예금주 : 이양수</strong>
<p>
<h4><img src="./img/title2.png" alt="신청서작성"/></h4>
	<table  class="write_type mg_top10" border="1" cellspacing="0" summary="">
		<caption>2014평가자료 신청서</caption>  
		<colgroup>  
			<col width="120" />
			<col width="*"/>    
		</colgroup> 
		<thead>
			<tr>
				<th scope="row"><label for="BoardCenter">기관명</label></th>
				<td><?=$_SESSION['userCenterName'];?></td>
			</tr>
		<tbody>
			<tr>
				<th scope="row"><label for="BoardName">금액</label></th>
				<td><span style="width:50px; height:12px; padding-right:5px;">110,000</span>원</td>
			</tr>
		</tbody>
	</table>
	<div style="text-align:center; width:600px; padding:10px; margin-top:5px">
		<a href="#" onclick="lfRequest();" ><img src="img/btn_re.png" alt="2014평가자료 신청서 작성하기"/></a>
	</div>
<!-- //참가신청-->
</body>
<p style="font-size:14px; font-weight:bold; letter-spacing:-1px;">
1. 위의 신청하기 버튼을 클릭하시기 바랍니다.</br>
2. 송금 완료 후<strong style="font-size:14px;"> (070-4893-6990)로 전화</strong>를 주시면 사용가능합니다.
</p>
</html>
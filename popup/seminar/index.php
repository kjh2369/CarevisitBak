<?
	include_once('../../inc/_db_open.php');
?>
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title>2016~2017년 기관평가교육</title>
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
.write_type{width:400px;border-top:2px solid #0e69b0; font-size:12px;table-layout:fixed;}
.write_type caption{display:none}
.write_type th{padding:8px 0 8px 10px; border-bottom:1px solid #dcdcdc; border-right:1px solid #dcdcdc; background-color:#f7faff; color:#5877a3;font-weight:bold;text-align:left;}
.write_type th label	{display:block;}
.write_type th img{vertical-align:middle;}
.write_type td{padding:8px 0 8px 20px; border-bottom:1px solid #e0e4e7; text-align:left;  line-height:1.5em; }
.write_type td select{border:1px solid #cccccc; vertical-align:middle; height:20px; line-height:18px; padding:2px;}
.write_type td input{border:1px solid #cccccc; vertical-align:middle; height:26px; line-height:22px; padding-left:5px;}
.write_type td textarea{ border:1px solid #cccccc; width:90%; height:100px; padding:5px; color:#565960;}

p a{font-size:9pt; color:#777;}
p strong{font-size:9pt; color:#c21b06;}
p a.path_home{position:relative; top:4px;}
p a:hover, #right #location p a:focus, #right #loaction p a:active {text-decoration:none; }
h2{font-size:30px;}
h3{color:#2a589f; letter-spacing:-1px; word-spacing:-1px; font-size:17px; margin-top:20px; background:transparent url( img/icon_place.png) left 50% no-repeat; padding-left:10px;}
.ctn{width:356px; color:#2a589f;  font-size:17px; margin-top:25px; margin-left:22px; line-height:1.3em; font-weight:bold;}
strong{color:#333;}
 h4{margin-top:5px; color:#2a589f; font-size:14px;}
p { display:block;  font-size:12px;}

.mg_top5{margin-top:5px;}
.mg_top10{margin-top:10px;}
.mg_top15{margin-top:15px;}
.mg_top25{margin-top:25px;}
.mg_top35{margin-top:35px;}

.mg_left0{margin-left:0;}
.mg_left8{margin-left:8px;}
.mg_left16{margin-left:16px;}
.mg_left20{margin-left:20px;}
.mg_left22{margin-left:22px;}
.mg_btm15{margin-bottom:15px;}
</style>
<script type="text/javascript">
	function Request_pop(cnt,allcnt){
		if(allcnt >= 40){
			alert('모집인원이 다찼습니다.');
		}else {
			
			//if(cnt>0){
			//	alert('이미 신청한 기관입니다.');
			//}else {
				var Top = (window.screen.height-320) /2;
				var Left = (window.screen.width-635) /2;
				window.open('request.php','SEMINAR_REG','resizable=no scrollbars=no width=456 height=600 Top='+Top+' left='+Left+'');
			//}
		}
	}
	function setCookie(name, value, expiredays ){
		var todayDate = new Date();
			todayDate.setDate( todayDate.getDate() + expiredays );

		document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
	}

	function end(){
		var f = document.f;

		if (f.check.checked){
			setCookie('SEMINAR','DONE',1);
		}

		self.close();
	}
</script>
</head>
<?

$sql = 'select count(*)
		  from seminar_request';
$allCnt = $conn -> get_data($sql);

$sql = 'select count(*)
		  from seminar_request
		 where org_no = \''.$_SESSION['userCenterCode'].'\'';
$cnt = $conn -> get_data($sql);

?>
<body  style="margin-bottom:30px;">
<p style="width:440px;">
	<img src="img/semi_title.png" alt="2016~2017년 기관평가교육" />
</p>
<div style="width:440px; height:340px;">
<img  class="ctn" src="img/img_txt.png" alt="2016~2017년 공단평가에 케어비지트를 사용하시는 기관의 기관장 및 복지사님들께
	교육을 실시하고자 합니다." />
		<table  class="write_type mg_top25 mg_left22" border="1" cellspacing="0" summary="세미나 장소,시간,주소">
			<caption>2014년 평가관련 세미나를 개최 공고</caption>
			<colgroup>
				<col width="70" />
				<col width="*"/>
			</colgroup>
			<thead>
			  <tr>
					<th scope="row"><label for="evalPlace">장소</label></th>
					<td>
					  <span style="font-weight:bold">서울시 강남구 선릉로 514 (삼성동)<br />성원빌딩 11층 유니에스 교육장</span>
						<ul style="margin-left:16px; margin-top:5px;">
						<li>지하철 : 선릉역 8번출구와 연결</li>
						<li>버스 : 선릉역 한국학원 앞/ 진선여고 앞 정류장 하차</li>
						</ul>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th scope="row"><label for="evalDate">일자</label></th>
					<td style="font-weight:bold">2016년 02월 26일 (금요일)</td>
				</tr>
				<tr>
					<th scope="row"><label for="evalTime">시간</label></th>
					<td style="font-weight:bold">10시~ 18시</td>
				</tr>
				<tr>
					<th scope="row"><label for="evalNumber ">모집인원</label></th>
					<td style="color:red; font-weight:bold;">40명 (선착순)</td>
				</tr>
				<tr>
					<th scope="row"><label for="evalTeacher">강사</label></th>
					<td>
						<ul style="margin-left:16px;">
						<li>이양수 (케어비지트 대표)</li>
						<li>조호철 (10,12,14년 최우수기관 대표)</li>
						<li>김대식 (목포 국화센터장)</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="evalMoney">참가비</label></th>
					<td style="font-size:18px; color:red; font-weight:bold;">1인당 100,000원</td>
				</tr>
				<tr>
			</tbody>
		</table>
	<div style="height:70px; text-align:center; width:100%; margin-top:15px;">
		<a href="#" onclick="Request_pop('<?=$cnt;?>','<?=$allCnt;?>');" ><img alt="참가신청하기" src="img/btn1.png"  style="text-align:center;" /></a>
	</div>
</div>
<div style="background-color:#000; width:100%; height:35px; padding-top:5px;">
<form name="f" method="post" action="">
<label><img src="img/p_img2.gif" style="border:0; vertical-align:middle;"></label><input type="checkbox" name="check" style="border:0; vertical-align:middle;" onClick="end();"/>
<span style="width:43px; height:28px; margin-left:200px;">
 <img src="img/p_img3.gif" onClick="window.close();" style="cursor:pointer; vertical-align:middle;">
</span>
</form>
</div>
</body>
</html>
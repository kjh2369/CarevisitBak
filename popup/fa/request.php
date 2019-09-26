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
	
	$sql = 'select count(*)
			  from seminar_request';
	$allCnt = $conn -> get_data($sql);
   
	$sql = 'select *
	          from seminar_request
			 where org_no = \''.$_SESSION['userCenterCode'].'\'
			   and gbn    = \'3\'
			   and del_flag = \'N\'';
	$mst = $conn -> get_array($sql);
	
	$rank = $mst['rank'] != '' ? $mst['rank'] : '';

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
.write_type{width:410px;border-top:2px solid #0e69b0; font-size:12px;table-layout:fixed;}
.write_type caption{display:none}
.write_type th{padding:5px 0 5px 20px; border-bottom:1px solid #dcdcdc; border-right:1px solid #dcdcdc; background-color:#f7faff; color:#5877a3;font-weight:bold;text-align:left;}
.write_type th label	{display:block;}
.write_type th img{vertical-align:middle;}
.write_type td{padding:5px 0 4px 20px; border-bottom:1px solid #e0e4e7; text-align:left;  line-height:1.5em; }
.write_type td select{border:1px solid #cccccc; vertical-align:middle; height:20px; line-height:18px; padding:2px;}
.write_type td input{border:1px solid #cccccc; vertical-align:middle; height:26px; line-height:22px; padding-left:5px;}
.write_type td textarea{ border:1px solid #cccccc; width:90%; height:100px; padding:5px; color:#565960;}

p a{font-size:9pt; color:#777;}
p strong{font-size:9pt; color:#c21b06;}
p a.path_home{position:relative; top:4px;}
p a:hover, #right #location p a:focus, #right #loaction p a:active {text-decoration:none; }
h2{font-size:30px;}
h3{color:#2a589f; letter-spacing:-1px; word-spacing:-1px; font-size:17px; margin-top:20px; background:transparent url( img/icon_place.png) left 50% no-repeat; padding-left:10px;}
strong{color:#333;}
 h4{margin-top:5px; color:#2a589f; font-size:14px;}
p { display:block;  font-size:12px;}
.radio{border:none;}
.mg_top5{margin-top:5px;}
.mg_top10{margin-top:10px;}
.mg_top15{margin-top:15px;}
.mg_top26{margin-top:26px;}
.mg_top35{margin-top:35px;}

.mg_left0{margin-left:0;}
.mg_left8{margin-left:8px;}
.mg_left16{margin-left:16px;}
.mg_left20{margin-left:20px;}
.mg_left25{margin-left:27px;}
.mg_btm15{margin-bottom:15px;}
</style>
<script type="text/javascript" src="../../js/script.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		//lfResize();
		lfSetPay();
		//self.focus();
		//setTimeout('lfSetPay()',50);
	});

	function lfResize(){
		var t = $('#divCal').offset().top;
		var h = $(document).height() - t - 20;

		$('#divTop').height(h);
	}

	function lfRequest(){
		
		var msg = '';
		

		msg += '신청 하시겠습니까?';
		msg += '\n\n신청하시면 확인을 클릭하여 주십시오.';

		if (!confirm(msg)) return false;

		
		$.ajax({
			type  : 'POST'
		,	async : false
		,	url   : './request_ok.php'
		,	data  : {
			}
		,	success: function(result){
				
				if (result == 1){
					alert('정상적으로 신청하였습니다.');
					self.close();
				}else if (result == 9){
					alert('처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
				
			}
		});
		
	}


	function show_report(){

		var w = 700;
		var h = 900;
		var l = (window.screen.width  - w) / 2;
		var t = (window.screen.height - h) / 2;
		
		
		
		var filename = './2018_seminar.pdf';
		
		
		var win = window.open(filename,'SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');	
	}

</script>
</head>
<body>
<form id="WirteForm" name="WirteForm" method="post" >
  <img usemap="#famap" src="./img/fa_request.jpg" alt="신청서작성" />
  <map name="famap">
  <area shape="rect" coords="27,498,466,544" href="javascript:lfRequest();" title="신청하기">
  </map>
</form>
</body>
</html>

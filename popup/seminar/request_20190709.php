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

	function lfSetPay(){
		var rank = $('#BoardRank').val();
		var rank2 = $('#BoardRank2').val();
		var pay = 0;
		
		if(rank != ''){
			for(var i=0; i<rank; i++){
				pay += 150000;
			}
		}

		if(rank2 != ''){
			for(var i=0; i<rank2; i++){
				pay += 10000;
			}
		}
		
		$('#txtPay').text(__num2str(pay));
		$('#BoardPay').text(__num2str(pay));

	}

	function lfRequest(){
		
		//var totRank = (parseInt('<?=$allCnt?>')+parseInt($('#BoardRank').val())); 
		
		/*
		if(totRank >= 40){
			alert('죄송합니다. 신청인원이 꽉 찼습니다.');
			return false;
		}else {
		*/	
			
			if($('input:radio[name=BoardGbn]').is(':checked')==false){
				alert('타입을 선택해주십시오.');
				$('#BoardGbn').focus();
				return false;
			}

			var msg = '';
			

			msg += '티입 '+$('input[name=BoardGbn]:radio:checked').val()+'를 선택하였습니다.';
			msg += '\n\n신청 하시겠습니까?';
			msg += '\n\n신청하시면 확인을 클릭하여 주십시오.';

			if (!confirm(msg)) return false;

			//$('#txtBoardPay').val($('#BoardPay').text());
		
		//}

		document.WirteForm.action = './request_ok.php';
		
		return true;
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
  <img src="../finance_title.png" alt="재무회계" />	
  <div style="overflow-x:hidden; overflow-y:scroll; width:500px; height:420px;"><img src="../finance_ctn.png" alt="신청서작성" /></div>
  <div style="border-top:1px solid #ccc;"></div>
	<img style="margin-top:15px; margin-left:27px;" src="./img/title.png" alt="신청서작성" />
	<form id="WirteForm" name="WirteForm" method="post" action="#" onsubmit="return lfRequest();">
	<input type="hidden" id="txtBoardSeq" name="txtBoardSeq" value="<?=$mst['seq'];?>"/>
	<input type="hidden" id="txtBoardPay" name="txtBoardPay" value=""/>
		<table  class="write_type mg_top10 mg_left25" style="width:450px;" border="1" cellspacing="0" summary="">
			<caption>재무회계 신청하기</caption>  
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
					<th scope="row"><label for="BoardName" style="font-size:14px; font-weight:bold">타입선택</label></th>
					<td style="font-size:14px; font-weight:bold">
						<input type="radio" class="radio" id="BoardGbn1" name="BoardGbn" value="A" style="border:none;" /> 타입A
						<input type="radio" class="radio" id="BoardGbn2" name="BoardGbn" value="B" style="border:none;" /> 타입B
					</td>
				</tr>
				<!--tr>
					<td style="font-size:14px; font-weight:bold; color:red;" colspan="2">
						※신청한 기관에 한하여 개별연락드립니다.
					</td>
				</tr-->
			</tbody>
		</table>
		<div style="text-align:center; width:460px; color:red; font-weight:bold; font-size:15pt;">
			<input type="image" alt="신청하기" src="img/btn_request2.png" class="mg_top15" />
		</div>
	</form>

	<!--div style="position:absolute; top:180px; left:22px; cursor:pointer;" onclick="show_report();"><img src="./img/btn_semi.png" alt="세미나 내용 상세보기" /></div-->

</body>
</html>

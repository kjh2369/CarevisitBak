<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_myFun.php');
	
	$code = $_SESSION["userCenterCode"];
	
	$sql = 'SELECT m00_store_nm as cname
			,	   m00_ctel as ctel
			  FROM m00center
			 WHERE m00_mcode = \''.$code.'\'
			 ORDER BY m00_mkind
			 LIMIT 1';
	$center = $conn->get_array($sql);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="description" content="케어비지트 재가관리프로그램 소개" />
<meta name="keywords" content="재가관리 프로그램,재가방문,재가급여,방문서비스,사회서비스,방문요양,방문목욕,방문간호 관리" />
<title>케어비지트</title>
<style type="text/css">
<!--
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

#c_title { position:relative; width:100%; height:250px; margin-top:34px;}
#c_title h2{height:28px; background: url( img/icon_b_title.png) left 40% no-repeat; padding-left:36px; margin-bottom:35px;}
#c_title p{margin-left:0; margin-top:20px; display:block;}


.tbl_btn_box2{padding:0; padding-top:10px; text-align:center;}

/*counsel
============================================================*/
#c_title { position:relative; width:100%; height:250px; margin-top:34px;}
#c_title h2{height:28px; background: url( img/icon_b_title.png) left 40% no-repeat; padding-left:36px; margin-bottom:35px;}
#c_title p{margin-left:0; margin-top:20px; display:block;}

#CounForm {padding-top:15px;}
#CounForm p.check{ margin:0; padding-right:5px; text-align:right;}
#CounForm p.check img{vertical-align:middle;}
.Coun_type,.Coun_type th,.Coun_type td{border:0; font-family:"굴림","돋움", Dotum,Gulim,AppleGothic,Sans-serif;}  
.Coun_type{width:100%; min-width:680px; border-top:2px solid #11678f; color:#333;font-size:12px;table-layout:fixed;}  
.Coun_type caption{display:none}
.Coun_type th{padding:5px 0 5px 10px; border-bottom:1px solid #b1c2c5; background-color:#b8eaf5;color:#2e3c74;font-weight:bold;text-align:left;} line-height:1.8em;
.Coun_type th label	{display:block;}
.Coun_type th img{vertical-align:middle;}
.Coun_type td{padding:5px 0 4px 10px; border-bottom:1px solid #b1c2c5; text-align:left; line-height:1.8em;}  
.Coun_type td select{border:1px solid #11678f; vertical-align:middle; height:20px; line-height:18px; font-size:12px; padding:2px;}
.Coun_type td input{border:1px solid #11678f; vertical-align:middle;  height:16px; line-height:16px; margin-left:1px; margin-right:5px; padding:2px 3px 2px;border:1px solid #b5b5b5;font-size:12px;}
.Coun_type td textarea{ border:1px solid #c5c5c5; width:90%; height:200px;}
.Coun_type td input.submit{width:60px;border:solid 1px #c5c5c5;background-color:#fffcdf;}

-->
</style>
<script type="text/javascript" src="../../js/prototype.js"	></script>
<script type="text/javascript" src="../../js/xmlHTTP.js"	></script>
<script type="text/javascript" src="../../js/script.js"	></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript">
	$(window).load(function(){
		$(this).focus();
	});

	function lfReg(){
	
		if (!$('#txtCenterNm').val()){
			alert('기관명을 입력하여 주십시오.');
			$('#txtCenterNm').focus();
			return;
		}
	
		if (!$('#txtTel').val()){
			alert('연락처를 입력하여 주십시오.');
			$('#txtTel').focus();
			return;
		}

		
		/*
		if (!$('#txtCont').val()){
			alert('상담내용을 입력하여 주십시오.');
			$('#txtCont').focus();
			return;
		}
		*/
		
		$.ajax({
			type: 'POST'
		,	url : './labor_request_ok.php'
		,	data: {
				'center'	:$('#txtCenterNm').val()
			,	'phone'		:$('#txtTel').val()
			,	'content'	:$('#txtCont').val()
			}
		,	success: function (result){
			
				alert('확인 후 연락드리겠습니다.\n감사합니다.');
				self.close();
			}
		,	error: function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
		
	}
</script>
</head>

<body>
	<div style="width:680px; margin:0 auto;">
		<!--ctn_title-->
		<div id="c_title"  style="height:40px;">
			<h2><img src="../../total/img/title_total2.png" alt="문의신청" /></h2>
		</div>
		<!--//ctn_title-->
		<!--CounForm-->
		<form id="CounForm" name="CounForm" method="post" action="#" onsubmit="">
		<p class="check"><img src="http://www.carevisit.net/home/bbs/img/board_check_1.gif" alt="*" /> 표시 필수 입력사항</p>
		<table  class="Coun_type" border="0"  cellspacing="0" summary=" 문의신청 작성폼">
			<caption>문의신청</caption>
			<colgroup>
				<col width="100" />
				<col width="*"/>
			</colgroup>
			<thead>
					<th scope="row"><label for="txtCenterNm"><img src="http://www.carevisit.net/home/bbs/img/board_check_2.gif" alt="*" /> 기관명</label></th>
					<td colspan="3"><?=$center['cname'];?><input type="hidden" id="txtCenterNm" name="txt" style="width:100px;" value="<?=$center['cname'];?>" /></td>
				</tr>
			</thead>
			<tbody>
				<tr>
				<th><label for="txtTel"><img src="http://www.carevisit.net/home/bbs/img/board_check_2.gif" alt="*" /> 연락처</label> </th>
				<td colspan="3">
					<input type="text" id="txtTel" name="txt" style="width:90px; ime-mode:disabled;" maxlength="11" onkeydown="__onlyNumber(this);" onblur="__getPhoneNo(this);" title="전화번호" value="<?=$center['ctel']?>"/>
				</td>
			</tr>
				<tr>
					<th scope="row"><label for="CounCont"> 문의내용</label></th>
					<td style="width:100%; height:100%;" colspan="3">
						<textarea name="txts" id="txtCont" cols="50" rows="20" style="width:560px; height:200px; padding:2px 3px 2px;" ></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<!--//CounForm-->
		<div class="tbl_btn_box2">
			<a href="#" onclick="lfReg();"><img alt="등록"  src="http://www.carevisit.net/home/bbs/img/btn_regi.png" /></a>
			<a href="#" onclick="self.close();"><img alt="취소" src="http://www.carevisit.net/home/bbs/img/btn_cancel.png" /></a>
		</div>
		</form>
	</div>
</body>
</html>
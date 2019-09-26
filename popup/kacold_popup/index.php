<?
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_db_open.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_function.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_myFun.php");

	$orgNo = $_SESSION['userCenterCode'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
	<title>::재가지원서비스::</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='imagetoolbar' content='no'>
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta name="keywords" content="방문서비스 관리 시스템, 케어,돌봄, 재가, 요양보호사, 방문요양, 방문목욕, 방문간호" />
	<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript" src="http://carevisit.net/js/prototype.js"	></script>
<script type="text/javascript" src="http://carevisit.net/js/xmlHTTP.js"	></script>
<script type="text/javascript" src="http://carevisit.net/js/script.js"	></script>
<script type="text/javascript" src="http://carevisit.net/js/jquery.js"></script>
<script type="text/javascript" src="http://carevisit.net/js/jquery.form.js"></script>
<style>
	.nowrap{
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
</style>
<script language="vbscript">
	Function checkDate(p_date)
		checkDate = IsDate(p_date)
	End Function

	Function getToday()
		Dim y, m, d

		y = Year(Now)
		m = Month(Now)
		d = Day(Now)

		If (m < 10) Then m = "0" & m
		If (d < 10) Then d = "0" & d

		getToday = y & "-" & m & "-" & d
	End Function

	Function getTime()
		Dim time

		getTime = Right("0" & Hour(Now),2) & ":" & Right("0" & Minute(Now), 2)
	End Function

	Function diffDate(p_datepart, p_startDate, p_endDate)
		diffDate = DateDiff(p_datepart, p_startDate, p_endDate)
	End Function

	Function addDate(p_interval, p_number, p_date)
		Dim ls_date

		ls_date = DateAdd(p_interval, p_number, p_date)

		addDate = Year(ls_date) & "-" & Right("0" & Month(ls_date), 2) & "-" & Right("0" & Day(ls_date), 2)
	End Function

	Function addTime(p_interval, p_number, p_time)
		Dim ls_time

		ls_time = DateAdd(p_interval, p_number, p_time)

		addTime = Hour(ls_time) & ":" & Minute(ls_time) & ":" & Second(ls_time)
	End Function

	Function getYear(p_date)
		If (checkDate(p_date)) Then
			getYear = Year(p_date)
		Else
			getYear = 0
		End If
	End Function

	Function getMonth(p_date)
		If (checkDate(p_date)) Then
			getMonth = Month(p_date)
		Else
			getMonth = 0
		End If
	End Function

	Function getDay(p_date)
		If (checkDate(p_date)) Then
			getDay = Day(p_date)
		Else
			getDay = 0
		End If
	End Function

	Function getWeekDay(p_date)
		If (checkDate(p_date)) Then
			getWeekDay = Weekday(p_date)
		Else
			getWeekDay = 0
		End If
	End Function

	Function getLastDay(p_date)
		If (Not checkDate(p_date)) Then
			getLastDay = 0
			Exit Function
		End If

		getLastDay = Day(DateAdd("d", -1, DateAdd("m", 1, p_date)))
	End Function
</script>

<script language='javascript'>
	$(document).ready(function(){
		__fileUploadInit($('#f'), 'fileUploadCallback');
	});

	function fileUpload(path){
		var frm = $('#f');
			frm.attr('action', './upload.php?path='+path);
			frm.submit();
	}

	function fileUploadCallback(data, state){
		if (data == 1){
			alert('정상적으로 처리되었습니다.');
			$('#pop').hide();
		}else if (data == 9){
			alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		}else{
			alert(data);
		}
	}

	function checkFile(obj,exp){
		/*
		var s = $(obj).val().split('.');

		s = s[s.length-1].toLowerCase();

		if (exp.indexOf(s) < 0){
			alert('스캔 받은 이미지 파일을 선택하여 여주십시오.');
			return;
		}
		*/

		$('#filename').val($(obj).val());

		//$('#filestr').text(path);

		/*
		alert($('#contUpload').val());

		var p = '';

		if ($(obj).attr('id') == 'contUpload'){
			p = 'contract';
		}else{
			p = 'registration';
		}

		fileUpload(p);
		*/
	}

	function popShow(obj,gbn){
		var left = $(obj).offset().left;
		var top = $(obj).offset().top - $('#pop').height() - 20;
		var msg = '';

		if (gbn == 'contract'){
			msg = '계약서';
		}else{
			msg = '등록증';
		}

		$.ajax({
			type:'POST'
		,	url:'./get_filen.php'
		,	data:{
				'dir':gbn
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (data){
					$('#regFile').css('color','BLUE').text('등록된 '+msg+'이 있습니다.');
				}else{
					$('#regFile').css('color','RED').text('등록된 '+msg+'이 없습니다.');
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});

		$('#pop').css('left',left).css('top',top).show();
		$('#path').val(gbn);
	}
</script>
<body style="margin:0;">
<table style="width:100%;">
	<tr>
		<td>
			<div><img src="./bg_ctn.jpg"></div>
			<div style="position:relative; top:-58px; text-align:center;">
				<img id="bg" src="./btn1.png" style="margin-right:10px;" onclick="location.href='./04.재가지원사용계약서(경재협).xlsx';">
				<img id="bg" src="./btn2.png" style="margin-right:10px;" onclick="popShow(this,'contract');">
				<img id="bg" src="./btn3.png" onclick="popShow(this,'registration');">
			</div>
		</td>
	</tr>
</table>

<form id="f" name="f" method="post" enctype="multipart/form-data">
<div id="pop" style="position:absolute; z-index:100; left:0; top:0; width:200px; height:130px; border:2px solid #363dcb; background-color:WHITE; display:none;">
	<div style="width:100%; text-align:right;">
		<div style="float:right; width:auto; margin-top:5px; margin-right:5px; cursor:pointer;"><img src="./btn_close.png" onclick="$('#pop').hide();"></div>
	</div>
	<table style="width:100%;">
		<tr>
			<td>
				<div style="padding-top:10px;">
					<div style="float:left; width:190px; font-size:13px; margin-left:5px;" class="nowrap" id="regFile"></div>
				</div>
				<div style="width:100%; padding-top:5px;">
					<div style="float:right; width:100px; background:url(../../image/find_file.gif) no-repeat left 50%;">
						<input type="file" name="upfile" id="upfile" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand;" onchange="checkFile(this,'jpg,png,bmp');">
						<input type="hidden" name="path" id="path">
					</div>
					<div style="float:left; width:auto; padding-left:5px;">
						<input type="text" id="filename" style="width:80px; border:1px solid #bcbcbc; height:14px;" readonly>
					</div>
				</div>
				<div style="font-size:13px; text-align:center; padding-top:10px;">
					<img src="./btn_register.png" onclick="fileUpload();">
				</div>
			</td>
		</tr>
	</table>
</div>
</form>
</body>
<?
	include_once($_SERVER['DOCUMENT_ROOT']."/inc/_db_close.php");

	/*
				<form id="f" name="f" method="post" enctype="multipart/form-data">
					<div style="float:left; width:auto; margin-right:10px;"><img id="bg" src="./btn1.png" onclick="location.href='./04.재가지원사용계약서(경재협).xlsx';"></div>
					<div style="float:left; width:187px; height:43px; margin-right:10px; background:url(./btn2.png) no-repeat;" onclick="$('#contUpload').click(); $('#bg').focus();">
						<input type="file" name="contUpload" id="contUpload" style="width:10px; height:43px; filter:alpha(opacity=0);" onchange="checkFile(this,'jpg,png,bmp');">
					</div>
					<div style="float:left; width:187px; height:43px; background:url(./btn3.png) no-repeat;" onclick="$('#bizUpload').click(); $('#bg').focus();">
						<input type="file" name="bizUpload" id="bizUpload" style="width:10px; height:43px; filter:alpha(opacity=0);" onchange="checkFile(this,'jpg,png,bmp');">
					</div>
				</form>
	*/
?>
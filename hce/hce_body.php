<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once("../inc/_body_header.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgType= $_GET['sr'];
	$type	= $_GET['type'];
	$sr		= $_GET['sr'];
	
	/*
	$sql = 'SELECT	*
			FROM	hce_proc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$infoDt = $conn->get_array($sql);
	*/
?>
<script language="vbscript">
	Function GetImageSize(ByVal f, ByRef x, ByRef y)
		Set p = LoadPicture(f)
			x = CLng(CDbl(p.Width) * 24 / 635)
			y = CLng(CDbl(p.Height) * 24 / 635)
		Set p = Nothing

		GetImageSize = x & "_" & y
	End Function
</script>
<script type="text/javascript">
	$(document).ready(function(){
		console.log("test");
		__init_form(document.f);
		lfResize();
	});

	function lfResize(){
		try{
			var top = $('#divBody').offset().top;
			var height = $(document).height();
			var head = 0;
			var foot = 0;

			if ($('#divFoot').css('display') != 'none'){
				foot = __str2num($('#divFoot').height());
			}

			if ($('#divHead').css('display') != 'none'){
				head = __str2num($('#divHead').height());
			}

			height = height - head - foot;

			var h = height - top - 10;

			$('#divBody').height(h);
		}catch(e){
		}
	}

	function lfMemFind(rtn){
		var jumin = $('#txtClient').attr('jumin');
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_CLIENT', option);
			win.opener = self;
			win.focus();

		if (!rtn) rtn = 'lfMemFindResult';

		var parm = new Array();
			parm = {
				'type':'member'
			,	'jumin':jumin
			,	'kind':'<?=$sr;?>'
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			,	'return':rtn
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type','hidden');
			objs.setAttribute('name',key);
			objs.setAttribute('value',parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target','FIND_CLIENT');
		form.setAttribute('method','post');
		form.setAttribute('action',url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfPDF(type,subId,idx,def){
		var dir = 'P';
		var file = 'hce_print';

		if (type == '1'){
			dir = 'L';
		}

		if (!def) def = '';
		if (!subId) subId = '';
		if (!idx) idx = '';

		var arguments	= 'root=hce'
						+ '&dir='+dir
						+ '&fileName='+file
						+ '&fileType=pdf'
						+ '&target=show.php'
						+ '&mode='+type
						+ '&sr=<?=$sr;?>'
						+ '&subId='+subId
						+ '&idx='+idx
						+ '&def='+def
						+ '&from='+$('#txtFrom').val()
						+ '&to='+$('#txtTo').val()
						+ '&endYn='+$('#cboEndYn').val()
						+ '&showForm=HCE';

		if (type == '81'){
			arguments += ('&order='+$('input:radio[name="optOrder"]:checked').val());
		}

		__printPDF(arguments);
	}
</script>
<form name="f" method="post" enctype="multipart/form-data">
<div style="margin-right:10px;"><?
	if ($type == '1'){
		//사례접수일지
		include_once('./hce_receipt.php');
	}else if ($type == '11' || $type == '12'){
		//사례접수등록
		include_once('./hce_receipt.php');
	}else if ($type == '21'){
		//초기면접기록지
		include_once('./hce_interview.php');
	}else if ($type == '31'){
		//사정기록지
		include_once('./hce_inspection.php');
	}else if ($type == '41'){
		//선정기준표
		include_once('./hce_choice.php');
	}else if ($type == '51'){
		//사례회의
		include_once('./hce_case_meeting.php');
	}else if ($type == '52'){
		//사례회의록 작성
		include_once('./hce_case_meeting_reg.php');
	}else if ($type == '61'){
		//서비스계획서
		include_once('./hce_svc_plan.php');
	}else if ($type == '62'){
		//서비스계획서 작성
		include_once('./hce_svc_plan_reg.php');
	}else if ($type == '71'){
		//서비스 이용안내 및 동의서
		include_once('./hce_consent_form.php');
	}else if ($type == '81'){
		//과정상담일지
		include_once('./hce_proc_counsel.php');
	}else if ($type == '82'){
		//과정상담일지 등록
		include_once('./hce_proc_counsel_reg.php');
	}else if ($type == '91'){
		//서비스 연계 및 의뢰서
		include_once('./hce_svc_connection.php');
	}else if ($type == '92'){
		//서비스 연계 및 의뢰서
		include_once('./hce_svc_connection_reg.php');
	}else if ($type == '101'){
		//모니터링 기록지
		include_once('./hce_monitor.php');
	}else if ($type == '102'){
		//모니터링 기록지
		include_once('./hce_monitor_reg.php');
	}else if ($type == '111'){
		//재사정기록지
		include_once('./hce_re_ispt.php');
	}else if ($type == '112'){
		//재사정기록지 작성
		include_once('./hce_re_ispt_reg.php');
	}else if ($type == '121'){
		//서비스 종결 안내서
		include_once('./hce_end.php');
	}else if ($type == '141'){
		//제공평가서
		include_once('./hce_provide_eval.php');
	}else if ($type == '142'){
		//제공평가서
		include_once('./hce_provide_eval_reg.php');
	}else if ($type == '131'){
		//사례평가서
		include_once('./hce_evaluation.php');
	}?>
</div>
<input id="type" name="type" type="hidden" value="<?=$type;?>">
<input id="sr" name="sr" type="hidden" value="<?=$sr;?>">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once('../inc/_footer.php');
?>
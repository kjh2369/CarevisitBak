<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$year = $_POST['year'];
	$month = $_POST['month'];
?>
<script type='text/javascript' src='./care.js'></script>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);

		$('input:text[name="txtTime"]').unbind('keyup').bind('keyup',function(){
			if ($(this).attr('maxlength') == $(this).val().length){
				if ($(this).attr('id') == 'txtFromH'){
					$('#txtFromM').focus();
				}else if ($(this).attr('id') == 'txtFromM'){
					$('#txtToH').focus();
				}else if ($(this).attr('id') == 'txtToH'){
					$('#txtToM').focus();
				}else if ($(this).attr('id') == 'txtToM'){
					lfSetProcTime();
				}
			}
		}).unbind('blur').bind('blur',function(){
			lfSetProcTime();
		});
	});

	function lfSetProcTime(){
		var from = __time2min($('#txtFromH').val()+':'+$('#txtFromM').val());
		var to = __time2min($('#txtToH').val()+':'+$('#txtToM').val());
		var proc = __str2num(to - from);

		$('#lblProc').text(proc);
	}

	function lfFindClient(){
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

		var parm = new Array();
			parm = {
				'type':'sugupja'
			,	'jumin':jumin
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			,	'svcCd':'6'
			,	'return':'lfMemFindResult'
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

	function lfMemFindResult(obj){
		$('#txtClient').attr('jumin',obj[0]).val(obj[1]);
	}
</script>
<div class="title title_border">제공서비스</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="20px">
		<col width="30px">
		<col width="64px">
		<col width="30px">
		<col width="64px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" colspan="2">고객</th>
			<th class="head" colspan="4">방문시간</th>
			<th class="head">진행시간</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center bottom last">
				<input id="txtClient" name="txtClient" type="text" style="width:100%; margin-right:0;" jumin="" readonly alt="not" onmousedown="lfFindClient();">
			</td>
			<td class="center bottom"><img src="../image/btn_close.gif"></td>
			<th class="center bottom">시작</th>
			<td class="center bottom">
				<input id="txtFromH" name="txtTime" type="text" class="no_string" style="text-align:center; width:20px;" maxlength="2">:<input id="txtFromM" name="txtTime" type="text" class="no_string" style="text-align:center; width:20px;" maxlength="2">
			</td>
			<th class="center bottom">종료</th>
			<td class="center bottom">
				<input id="txtToH" name="txtTime" type="text" class="no_string" style="text-align:center; width:20px;" maxlength="2">:<input id="txtToM" name="txtTime" type="text" class="no_string" style="text-align:center; width:20px;" maxlength="2">
			</td>
			<td class="center bottom"><div id="lblProc"></div></td>
			<td class="center bottom last"></td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
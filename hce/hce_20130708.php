<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_hce.php');

	$type = $_GET['type'];
	$sr = $_GET['sr'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfResize();

		setTimeout('lfSetTarget()',50);

		__init_form(document.f);
	});

	function lfResize(){
		var width = $(document).width();
		var height = $(document).height();
		var top = $('#frmMenu').offset().top;
		var left = $('#frmBody').offset().left;

		width = width - left - 12;
		height = height - top - 10;

		$('#frmMenu').height(height);
		$('#frmBody').width(width).height(height);
	}

	function lfSetTarget(jumin,seq){
		if (!jumin){
			jumin = '';
		}

		if (!seq){
			seq = 0;
		}

		$.ajax({
			type :'POST'
		,	url  :'./hce_find.php'
		,	data :{
				'type':'TARGET'
			,	'jumin':jumin
			,	'seq':seq
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseStr(data);

				$('#lblTGName').text(col['name']);

				if (col['endYn'] == 'Y'){
					$('#lblTGEndYn').css('color','red').text('종결');
				}else if (col['endYn'] == 'N'){
					$('#lblTGEndYn').css('color','black').text('미결');
				}

				$('#lblTGBirth').text(col['birthDay']);
				$('#lblTGRctDt').text(col['rcptDt']);
				$('#lblTGRctSeq').text(col['rcptSeq']);
				$('#lblTGIVYn').text(col['IVYn'] == 'Y' ? '작성' : '');
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfMemFind(){
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
				'type':'member'
			,	'jumin':jumin
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
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
</script>
<form id="f" name="f" method="post" enctype="multipart/form-data">
<div style="padding:10px 10px 0 10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="40px">
			<col width="80px">
			<col width="80px">
			<col width="35px">
			<col width="60px">
			<col>
		</colgroup>
		<thead>
			<tr style="color:#064271;">
				<th class="head bold">대상자</th>
				<th class="head bold" style="line-height:1.3em;">종결<br>여부</th>
				<th class="head bold">생년월일</th>
				<th class="head bold">접수일자</th>
				<th class="head bold">차수</th>
				<th class="head bold">초기면접</th>
				<th class="head bold"></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center bold" id="lblTGName"></td>
				<td class="center bold" id="lblTGEndYn"></td>
				<td class="center bold" id="lblTGBirth"></td>
				<td class="center bold" id="lblTGRctDt"></td>
				<td class="center bold" id="lblTGRctSeq"></td>
				<td class="center bold" id="lblTGIVYn"></td>
				<td class="center"></td>
			</tr>
		</tbody>
	</table>
</div>
<iframe id="frmMenu" src="./hce_left.php" width="200" height="100" frameborder="0" style="margin-top:10px; margin-left:10px;"></iframe>
<iframe id="frmBody" width="200" height="100" frameborder="1" style="margin-top:10px; margin-left:10px;"></iframe>
<?
	/*
	if ($type == '1'){
		//사례접수일지
		include_once('./hce_case.php');
	}else if ($type == '11' || $type == '2'){
		//사례접수등록
		include_once('./hce_receipt.php');
	}else if ($type == '21'){
		//초기면접기록지
		include_once('./hce_interview.php');
	}else{
		include('../inc/_http_home.php');
		exit;
	}
	*/
?>
</form>
<?
	include_once('../inc/_footer.php');
?>
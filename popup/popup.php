<?
	include_once('../inc/_header.php');

	if (!isset($_SESSION['userCode']) || $_SESSION['userCode'] == ''){
		echo '<script language=\'javascript\'>
				self.close();
			  </script>';
	}
?>

<script type='text/javascript'>
function showNotice(id){
	$.ajax({
		type: 'POST',
		url : '../goodeos/notice_text.php',
		data: {
			'id':id
		},
		beforeSend: function (){
		},
		success: function (html){
			$('#divCont').html(html).show();
			$('#divList').hide();
			$('#readYn'+id).text('Y');
			$('#btnGbn').text('리스트');
		},
		error: function (){
		}
	}).responseXML;
}

function showList(){
	$('#divList').show();
	$('#divCont').html('').hide();
	$('#btnGbn').text('닫기');
}

function setGbn(){
	switch ($('#btnGbn').text()){
		case '리스트':
			showList();
			break;

		case '닫기':
			self.close();
			break;
	}
}

function setList(){
	$.ajax({
		type: 'POST',
		url : '../goodeos/notice_data.php',
		data: {
			'orderBy':$('#opt1').val()
		,	'descBy':$('#opt2').val()
		},
		beforeSend: function (){
		},
		success: function (html){
			$('#divList').html(html).show();
		},
		error: function (){
		}
	}).responseXML;
}

function resize(){
	var height = $(window).height() - $('#tblList thead').offset().top - $('#tblList thead').height() - $('#tblList tfoot').height();

	$('#divList').height(height);
	$('#divCont').height(height);
}

function setCookie(){
	__setCookie('notice','done',1);
}

function lfAttachDownload(id,seq){
	var parm = new Array();
		parm = {
			'type'	:'NOTICE'
		,	'mode'	:'ATTACH_DOWNLOAD'
		,	'id'	:id
		,	'seq'	:seq
		};

	var form = document.createElement('form');
	var objs;
	for(var key in parm){
		objs = document.createElement('input');
		objs.setAttribute('type', 'hidden');
		objs.setAttribute('name', key);
		objs.setAttribute('value', parm[key]);

		form.appendChild(objs);
	}

	form.setAttribute('target', '_self');
	form.setAttribute('method', 'post');
	form.setAttribute('action', '../goodeos/notice_fun.php');

	document.body.appendChild(form);

	form.submit();
}

$(document).ready(function(){
	var w = 550;
	var h = 450;

	window.resizeTo(w,h);

	resize();
	setList();

	$('#divList').show();
	$('#divCont').hide();

	self.focus();
});

$(window).resize(function(){
	resize();
});
</script>

<div class="title title_border">
	<div style="float:right; width:auto; margin-top:10px; padding-right:5px; font-weight:normal; color:#000000;">
		읽지 않은 공지사항
		<select id="opt1" name="opt1" style="width:auto;" onchange="setList();">
			<option value="1">기본</option>
			<option value="2">위로</option>
			<option value="3">아래</option>
		</select>
		<select id="opt2" name="opt2" style="width:auto;" onchange="setList();">
			<option value="1">최근일자순</option>
			<option value="2">작성일자순</option>
		</select>
	</div>
	<div style="float:left; width:auto;">공지사항</div>
</div>

<table id="tblList" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="270px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">작성일</th>
			<th class="head">제목</th>
			<th class="head">읽음</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top" colspan="5">
				<div id="divList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:150px;"></div>
				<div id="divCont" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:150px; text-align:left; padding:5px;"></div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5" style="text-align:right;">
				<div style="float:right; width:auto; padding-right:5px;"><a id="btnGbn" href="#" onclick="setGbn();">닫기</a></div>
				<div style="float:left; width:auto;"><input id="notYn" name="notYn" type="checkbox" class="checkbox" onclick="setCookie();"><label for="notYn">오늘 더이상 표시하지 않음</label></div>
			</td>
		</tr>
	</tfoot>
</table>

<div id='divLayer' style='position:absolute; left:0; top:0; z-index:11; display:none;'></div>

<?
	include_once('../inc/_footer.php');
?>
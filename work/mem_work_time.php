<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$year = date("Y");
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text, textarea').each(function(){
			__init_object(this);
		});

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST',
			url:'./mem_work_time_search.php',
			data:{
				'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfExcel(mode){
		var parm = new Array();
			parm = {
				'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
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
		

		form.setAttribute('method', 'post');
		if(mode == 'm'){
			form.setAttribute('action', './mem_work_time_excel_month.php');
		}else {
			form.setAttribute('action', './mem_work_time_excel.php');
		}
		document.body.appendChild(form);

		form.submit();
	}
</script>
<script type="text/javascript" src="../js/script.js"	></script>
<div class="title title_border">요양보호사 근무시간</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="550px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월조회</th>
			<td class="last">
				<input id="txtFromDt" type="text" class="yymm" value="<?=$year?>01"> ~ <input id="txtToDt" type="text" class="yymm" style="margin-right:0;" value="<?=$year?>12">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
			</td>
			<td class="right last">
				<span class="btn_pack small"><button onclick="lfExcel('m');">년월단위출력</button></span>
				<span class="btn_pack small"><button onclick="lfExcel();">엑셀출력</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="50px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">생년월일</th>
			<th class="head">입사일</th>
			<th class="head">근무개월</th>
			<th class="head">요양</th>
			<th class="head">목욕</th>
			<th class="head">간호</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"></tbody>
</table>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
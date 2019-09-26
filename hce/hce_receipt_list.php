<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사례접수 리스트
	 *********************************************************/

	$page = $_GET['page'];

	if (!$page) $page = '1';
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfResize();
		lfSearch();
	});

	function lfResize(){
		var top = $('#frmList').offset().top;
		var height = $(document).height();

		var h = height - top - 38;

		$('#frmList').height(h);
	}

	function lfPage(page){
		var f = document.f;

		f.action = './hce_body.php?sr=<?=$sr;?>&type=1&page='+page;
		f.submit();
	}

	function lfSearch(page){
		if (!page) page = 1;

		var parm = new Array();
			parm = {
				'txtName'	: $('#txtName').val()
			,	'txtFrom'	: $('#txtFrom').val()
			,	'txtTo'		: $('#txtTo').val()
			,	'cboEndYn'	: $('#cboEndYn').val()
			,	'page'		: page
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

		form.setAttribute('target', 'frmList');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './hce_receipt_list_sub.php?sr=<?=$sr;?>');

		document.body.appendChild(form);

		form.submit();
	}

	function lfExcels(){
		var f = document.f;
		

		f.action = './hce_receipt_excel.php';
		f.submit();
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="bottom last">
				<div style="float:left; width:auto;">
					<span class="btn_pack m"><span class="pdf"></span><button onclick="lfPDF('21','','','Y');">초기면접기록지(빈양식)</button></span>
					<span class="btn_pack m"><span class="pdf"></span><button onclick="lfPDF('31','ALL','','Y');">사정기록지(빈양식)</button></span>
				</div>
				<div style="float:right; width:auto;">
					<span class="btn_pack m"><span class="add"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=12" target="frmBody">신규</a></span>
					<span class="btn_pack m"><span class="pdf"></span><a href="#" onclick="lfPDF('<?=$type;?>');">출력</a></span>
					<span class="btn_pack m"><span class="excel"></span><a href="#" onclick="lfExcels();">출력</a></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<div class="my_border_blue">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col width="90px">
			<col width="60px">
			<col width="180px">
			<col width="60px">
			<col width="30px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">대상자명</th>
				<td><input id="txtName" name="txtName" type="text" value="" style="width:100%;"></td>
				<th class="center">접수기간</th>
				<td>
					<input id="txtFrom" name="txtFrom" type="text" value="" class="date"> ~
					<input id="txtTo" name="txtTo" type="text" value="" class="date">
				</td>
				<th class="center">종결여부</th>
				<td class="last">
					<select name="cboEndYn" id="cboEndYn" style="width:auto;">
						<option value="">전체</option>
						<option value="Y">종결</option>
						<option value="N" <?=$_SESSION['userArea'] == '05' && $sr == 'S' ? 'selected' : '';?>>미결</option>
					</select>
				</td>
				<td>
					<span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="60px">
			<col width="90px">
			<col width="40px">
			<col width="50px">
			<col width="70px">
			<col width="130px">
			<col width="90px">
			<col width="60px">
			<col width="40px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head" rowspan="2">No</th>
				<th class="head" colspan="3">대상자</th>
				<th class="head" rowspan="2">접수<br>방법</th>
				<th class="head" rowspan="2">접수<br>일자</th>
				<th class="head" colspan="2">의뢰인</th>
				<th class="head" rowspan="2">접수자</th>
				<th class="head" rowspan="2">종결<br>여부</th>
				<th class="head last" rowspan="2">비고</th>
			</tr>
			<tr>
				<th class="head">성명</th>
				<th class="head">연락처</th>
				<th class="head">차수</th>
				<th class="head">성명</th>
				<th class="head">연락처</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="top last" colspan="20">
					<iframe id="frmList" name="frmList" src="" width="100%" height="100px" scrolling="yes" frameborder="0"></iframe>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td id="pageList" class="center bottom last" colspan="20"></td>
			</tr>
		</tfoot>
	</table>
</div>
<?
	include_once('../inc/_db_close.php');
?>
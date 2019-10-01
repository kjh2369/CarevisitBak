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
		lfSearch();
	});
	

	function lfSearch(){
		$.ajax({
			type: 'POST',
			url : './hce_receipt_list_sub.php',
			data: {
				'txtName'	: $('#txtName').val()
			,	'txtFrom'	: $('#txtFrom').val()
			,	'txtTo'		: $('#txtTo').val()
			,	'cboEndYn'	: $('#cboEndYn').val()
			,	'sr'        : '<?=$sr;?>'
			},
			beforeSend: function (){
				// $('#listBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'>'+__get_loading()+'</div></center></div>');
			},
			success: function (xmlHttp){
				$('#listBody').html(xmlHttp);
				$('#tempLodingBar').remove();
			},
			error: function (e){
				console.log(e);
			}
		}).responseXML;
	
	}

	function lfDelete(IPIN,seq){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./hce_receipt_list_sub_delete.php'
		,	data:{
				'SR'	:'<?=$sr;?>'
			,	'IPIN'	:IPIN
			,	'seq'	:seq
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget('','');
					top.frames['frmLeft'].lfHideMenu();
					parent.lfPage('<?=$page;?>');
				}else if (result == 9){
					alert('처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}


	function lfExcels(){
		var f = document.f;
		

		f.action = './hce_receipt_excel.php';
		f.submit();
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">사례접수일지</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m wa"><button onclick="lfPDF('21','','','Y');">초기면접기록지(빈양식)</button></span>
		<span class="btn_pack m wa"><button onclick="lfPDF('31','ALL','','Y');">사정기록지(빈양식)</button></span>
		<span class="btn_pack m"><button type="button" class="bold" onclick="location.href='../hce/hce_body.php?sr=<?=$sr;?>&type=12'">신규</button></span>
		<span class="btn_pack m"><button type="button" class="bold" onclick="lfPDF('<?=$type;?>');">PDF</button></span>
		<span class="btn_pack m"><button type="button" class="bold" onclick="lfExcels();">엑셀</button></span>
	</div>
</div>

<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="100px">
		<col width="80px">
		<col width="202px">
		<col width="80px">
		<col width="86px">
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
			<td>
				<select name="cboEndYn" id="cboEndYn" style="width:auto;">
					<option value="">전체</option>
					<option value="Y">종결</option>
					<option value="N" <?=$_SESSION['userArea'] == '05' && $sr == 'S' ? 'selected' : '';?>>미결</option>
				</select>
			</td>
			<td class="left other">
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
		<col width="60px">
		<col width="70px">
		<col width="70px">
		<col width="130px">
		<col width="90px">
		<col width="80px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
	</thead>
	<tbody>
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
		<tr>
			<td class="pd0 last" colspan="11">
				<div id='listBody' style='overflow-x:hidden; overflow-y:scroll; width:100%; height:500px;'></div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$SR = $_POST['SR'];
	$return = $_POST['return'];
	$year = $_POST['year'];
	$month = $_POST['month'];
?>
<script type="text/javascript">
	//var opener = null;

	$(document).ready(function(){
		//opener = window.dialogArguments;

		var t = $('#frmList').offset().top;
		var h = $(this).height() - t - 1;

		$('#frmList').height(h);

		setTimeout('lfLoad()',200);
	});

	function lfLoad(){
		var gbn = $('#cboGbn').val();
		var cust = $('#txtCust').val();

		$('#frmList').attr('src','./care_cust_find_frame.php?sr=<?=$SR;?>&gbn='+gbn+'&cust='+cust);
		//$('#frmList').attr('src','./care_cust_find_frame.php?sr='+opener.sr+'&gbn='+gbn+'&cust='+cust);
	}

	function lfSetCust(obj){
		opener.lfFindCustResult(obj);
		self.close();
		/*
			opener.result = 1;
			opener.gbn = obj['gbn'];
			opener.cd = obj['cd'];
			opener.nm = obj['nm'];
			opener.bizno = obj['bizno'];
			opener.manager = obj['manager'];
			opener.stat = obj['stat'];
			opener.item = obj['item'];
			opener.phone = __getPhoneNo(obj['phone']).split('-').join('.');
			opener.fax = __getPhoneNo(obj['fax']).split('-').join('.');
			opener.addr= obj['addr'];
			opener.pernm = obj['pernm'];
			opener.pertel = __getPhoneNo(obj['pertel']).split('-').join('.');

			self.close();
		*/
	}
</script>
<div class="title title_border">거래처조회</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="50px">
		<col width="40px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>구분</th>
			<td>
				<select id="cboGbn" style="width:auto;">
					<option value="ALL">전체</option>
					<option value="1">공공</option>
					<option value="2">기업</option>
					<option value="3">단체</option>
					<option value="4">개인</option>
				</select>
			</td>
			<th>명칭</th>
			<td>
				<input id="txtCust" type="txt" type="text" style="width:100%;">
			</td>
			<td class="left">
				<span class="btn_pack m"><button type="button" onclick="lfLoad();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="120px">
		<col width="50px">
		<col width="70px">
		<col width="90px" span="2">
		<col width="150px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">명칭</th>
			<th class="head">구분</th>
			<th class="head">대표자명</th>
			<th class="head">연락처</th>
			<th class="head">FAX</th>
			<th class="head">주소</th>
			<th class="head">담당자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>
<iframe id="frmList" src="about:blank" width="100%" height="100" frameborder="0" scrolling="yes" target="FRM_LIST"></iframe>
<?
	include_once("../inc/_footer.php");
?>
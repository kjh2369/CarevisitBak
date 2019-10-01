<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$sr = $_POST['sr'];
	$type = $_POST['type'];

	if (!$type) $type = 'ILJUNG';
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfReSize();
		lfSearch();
	});

	function lfReSize(){
		var t = $('#divList').offset().top;
		var h = $(document).height();
		var height = h - t -3;

		$('#divList').height(height);
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_suga_search.php'
		,	data :{
				'SR':'<?=$sr;?>'
			,	'str':$('#txtCategory').val()
			,	'type':'CUST_SEL'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetSuga(cd,mstNm,proNm,svcNm){
		opener.lfCareSvcFindResult(cd,mstNm,proNm,svcNm);
		self.close();
	}
</script>
<div class="title"><div>재가관리 서비스 조회</div></div>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="90px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">검색</th>
			<td><input id="txtCategory" type="text" value="" style="width:70px;"></td>
			<td><span class="btn_pack m"><button onclick="lfSearch();">조회</button></span></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="100px">
		<col width="200px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">관</th>
			<th class="head">항</th>
			<th class="head">목</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="" colspan="4" style="padding:0;">
				<div id="divList" style="overflow-y:scroll;width:100%;height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="100px">
							<col width="100px">
							<col width="200px">
							<col>
						</colgroup>
						<tbody id="tbodyList">
							<tr>
								<td class="center last" colspan="4">::검색된 데이타가 없습니다.::</td>
							</tr>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_footer.php");
?>
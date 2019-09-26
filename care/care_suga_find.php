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
	
	if($debug){
		echo $type;
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfReSize();
		//setTimeout('lfLoad()',200);
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
				'sr':'<?=$sr;?>'
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			,	'type':'<?=$type;?>'
			,	'str':$('#txtCategory').val()
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

	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'ILJUNG_REG'
			,	'sr':'<?=$sr;?>'
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(13));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr style="cursor:default;">';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center">'+col['cd']+'</td>';
						html += '<td class="center"><div class="left"><a href="#" onclick="lfSetSuga(\''+col['cd']+'\',\''+col['mstNm']+'\',\''+col['proNm']+'\',\''+col['svcNm']+'\',\''+col['subNm']+'\'); return false;">'+col['subNm']+'</a></div></td>';
						html += '<td class="center last"></td>';
						html += '</tr>';

						no ++;
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetSuga(cd,mstNm,proNm,svcNm,subNm){
		opener.lfCareSvcFindResult(cd,mstNm,proNm,svcNm,subNm);
		self.close();
	}
</script>
<div class="title title_border">재가관리 서비스 조회</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">검색</th>
			<td><input id="txtCategory" type="text" value=""></td>
			<td class="left"><span class="btn_pack m"><button onclick="lfSearch();">조회</button></span></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="100px">
		<col width="200px">
		<col width="200px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">소분류</th>
			<th class="head">상세서비스</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top last" colspan="10">
				<div id="divList" style="overflow-y:auto;width:100%;height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="100px">
							<col width="100px">
							<col width="200px">
							<col width="200px">
							<col>
						</colgroup>
						<tbody id="tbodyList">
							<tr>
								<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
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
<!--
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="200px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수가코드</th>
			<th class="head">수가명</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top last" colspan="10">
				<div id="divList" style="overflow-y:auto;width:100%;height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="70px">
							<col width="200px">
							<col>
						</colgroup>
						<tbody id="tbodyList">
							<tr>
								<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
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
-->
<?
	include_once("../inc/_footer.php");
?>
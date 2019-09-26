<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$today = Date('Y-m-d');
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="177px">
		<col width="160px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">
				<select id="type" name="cbo" style="width:auto;">
					<option value="1">요청일자</option>
					<option value="2">이체일자</option>
				</select>
			</th>
			<td class="left">
				<input id="txtFrom" name="txt" type="text" value="<?=$today;?>" class="date"> ~
				<input id="txtTo" name="txt" type="text" value="<?=$today;?>" class="date">
			</td>
			<td class="left">
				<a href="#" onclick="lfSetDate(0);">오늘</a> |
				<a href="#" onclick="lfSetDate(1);">1개월</a> |
				<a href="#" onclick="lfSetDate(3);">3개월</a> |
				<a href="#" onclick="lfSetDate(6);">6개월</a>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="40px">
		<col width="90px">
		<col width="130px">
		<col width="60px">
		<col width="70px">
		<col width="50px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">요청일시</th>
			<th class="head">구분</th>
			<th class="head">은행명</th>
			<th class="head">계좌번호</th>
			<th class="head">예금주</th>
			<th class="head">이체금액</th>
			<th class="head">상태</th>
			<th class="head">처리일시</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="list"></tbody>
	<tfoot>
		<tr>
			<td id="pageStr" class="center bottom last" colspan="10"></td>
		</tr>
	</tfoot>
</table>

<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(aiPage){
		var page = __str2num(aiPage);
		var max  = 0;

		if (page < 1) page = 1;

		$.ajax({
			type: 'POST'
		,	async:false
		,	url : './trans_result_search.php'
		,	data: {
				'type':$('#type').val()
			,	'from':$('#txtFrom').val().split('-').join('')
			,	'to':$('#txtTo').val().split('-').join('')
			,	'page':'0'
			}
		,	beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success: function (result){
				max = result;
			}
		,	error: function (){
			}
		}).responseXML;

		$.ajax({
			type: 'POST'
		,	url : './trans_result_search.php'
		,	data: {
				'type':$('#type').val()
			,	'from':$('#txtFrom').val().split('-').join('')
			,	'to':$('#txtTo').val().split('-').join('')
			,	'page':page
			,	'max':max
			}
		,	beforeSend: function (){
			}
		,	success: function (data){
				//var pageStr = data.split(String.fromCharCode(3))[1];
				//data = data.split(String.fromCharCode(3))[0];

				var list = data.split(String.fromCharCode(1));
				var html = '';

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						if (val[2] == '1'){
							val[2] = '급여';
						}else if (val[2] == '3'){
							val[2] = '보험';
						}else if (val[2] == '9'){
							val[2] = '기타';
						}

						if (val[7] == '1'){
							val[7] = '요청';
						}else if (val[7] == '3'){
							val[7] = '<span style="color:blue;">성공</span>';
						}else if (val[7] == '5'){
							val[7] = '<span style="color:red;">실패</span>';
						}else if (val[7] == '9'){
							val[7] = '에러';
						}

						html += '<tr>'
							 +  '<td class="center">'+val[0]+'</td>'
							 +  '<td class="center" style="line-height:1.5em;">'+val[1].substring(0,10).split('-').join('.')+'<br>'+val[1].substring(11,19)+'</td>'
							 +  '<td class="center">'+val[2]+'</td>'
							 +  '<td class="left">'+val[3]+'</td>'
							 +  '<td class="left">'+val[4]+'</td>'
							 +  '<td class="left">'+val[5]+'</td>'
							 +  '<td class="right">'+__num2str(val[6])+'</td>'
							 +  '<td class="center">'+val[7]+'</td>'
							 +  '<td class="center" style="line-height:1.5em;">'+val[8].substring(0,10).split('-').join('.')+'<br>'+val[8].substring(11,19)+'</td>'
							 +  '<td class="left last">'+val[9]+'</td>'
							 +  '</tr>';
					}
				}

				if (!html){
					 html = '<tr>'
						  + '<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>'
						  + '</tr>';

				}

				$('#list').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfSetDate(aiMonth){
		var today = getToday();
		var date  = addDate('m', aiMonth*-1, today);

		$('#txtFrom').val(date);
		$('#txtTo').val(today);
	}
</script>
<?
	include_once('../inc/_db_close.php');
?>
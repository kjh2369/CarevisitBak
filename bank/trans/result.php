<?
	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	include_once('../inc/_menu_top.php');

	$today = Date('Y-m-d');
	$colgroup = '<col width="40px">
				 <col width="70px">
				 <col width="150px">
				 <col width="40px">
				 <col width="90px">
				 <col width="130px">
				 <col width="60px">
				 <col width="70px">
				 <col width="50px">
				 <col>';
?>
<table style="width:100%; min-width:1024px;" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="70px">
		<col width="175px">
		<col width="175px">
		<col>
	</colgroup>
	<tbody>
		<tr style="height:25px;">
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">이체일자</th>
			<td style="border-right:1px solid #cccccc; border-bottom:1px solid #cccccc; padding-left:5px;">
				<input id="txtFrom" name="txt" type="text" value="<?=$today;?>" style="width:70px; ime-mode:disabled;"> ~
				<input id="txtTo" name="txt" type="text" value="<?=$today;?>" style="width:70px; ime-mode:disabled;">
			</td>
			<td style="border-right:1px solid #cccccc; border-bottom:1px solid #cccccc; padding-left:5px;">
				<a href="#" onclick="lfSetDate(0);">오늘</a> |
				<a href="#" onclick="lfSetDate(1);">1개월</a> |
				<a href="#" onclick="lfSetDate(3);">3개월</a> |
				<a href="#" onclick="lfSetDate(6);">6개월</a>
			</td>
			<td style="border-right:1px solid #cccccc; border-bottom:1px solid #cccccc; padding-left:5px;">
				<button type="button" onclick="lfSearch();" style="padding-top:3px;">조회</button>
			</td>
		</tr>
	</tbody>
</table>

<table style="width:100%; min-width:1024px;" cellpadding="0" cellspacing="0">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr style="height:35px;">
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">No</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">이체일시</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">기관명</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">구분</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">은행명</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">계좌번호</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">예금주</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">이체금액</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">상태</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-bottom:1px solid #cccccc;">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="" colspan="20">
				<div id="listDiv" style="cursor:default; overflow-x:hidden; overflow-y:auto; width:100%; height:100px;" onclick="">
					<table style="width:100%; min-width:1024px;" cellpadding="0" cellspacing="0">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="list"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_menu_foot.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfResize();
		lfSearch();
	});

	function lfResize(){
	var h = $(this).height() - $('#top').height() - 40 - 63;

	$('#listDiv').height(h);
}

	function lfSetDate(aiMonth){
		var today = getToday();
		var date  = addDate('m', aiMonth*-1, today);

		$('#txtFrom').val(date);
		$('#txtTo').val(today);
	}

	function lfSearch(){
		$.ajax({
			type: 'POST'
		,	url : '../trans/result_search.php'
		,	data: {
				'from':$('#txtFrom').val().split('-').join('')
			,	'to':$('#txtTo').val().split('-').join('')
			}
		,	beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success: function (data){
				var list = data.split(String.fromCharCode(1));
				var html = '';

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						if (val[3] == '1'){
							val[3] = '급여';
						}else if (val[3] == '3'){
							val[3] = '보험';
						}else if (val[3] == '9'){
							val[3] = '기타';
						}

						if (val[8] == '1'){
							val[8] = '요청';
						}else if (val[8] == '3'){
							val[8] = '<span style="color:blue;">성공</span>';
						}else if (val[8] == '5'){
							val[8] = '<span style="color:red;">실패</span>';
						}else if (val[8] == '9'){
							val[8] = '에러';
						}

						html += '<tr style="height:25px;">'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:center;">'+val[0]+'</td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:center;">'+val[1].substring(0,10).split('-').join('.')+'<br>'+val[1].substring(11,19)+'</td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:left;"><div style="width:auto; padding:0 3px 0 3px;">'+val[2]+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:center;">'+val[3]+'</td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:left;"><div style="width:auto; padding:0 3px 0 3px;">'+val[4]+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:left;"><div style="width:auto; padding:0 3px 0 3px;">'+val[5]+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:left;"><div style="width:auto; padding:0 3px 0 3px;">'+val[6]+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:right;"><div style="width:auto; padding:0 3px 0 3px;">'+__num2str(val[7])+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:center;">'+val[8]+'</td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; text-align:left;"><div style="width:auto; padding:0 3px 0 3px;">'+(val[9] ? val[9] : '&nbsp;')+'</div></td>'
							 +  '</tr>';
					}
				}

				if (!html){
					 html = '<tr style="height:25px;">'
						  + '<td style="text-align:center;" colspan="20">::검색된 데이타가 없습니다.::</td>'
						  + '</tr>';

				}

				$('#list').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>
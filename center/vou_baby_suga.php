<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = 1;

		__init_form(document.f);

		var gbn = '';
		var max = 0;

		if (opener.type == '1'){
			gbn = '단태아';
			max = 792000;
		}else if (opener.type == '2'){
			gbn = '쌍태아';
			max = 1457000;
		}else if (opener.type == '3'){
			gbn = '삼태아';
			max = 2158000;
		}else{
			self.close();
		}

		$('#lblGbn').attr('value',opener.type).text(gbn);
		$('#lblMax').text(__num2str(max));
		$('#txtAmt').attr('max',max);

		$('#txtAmt').unbind('change').bind('change',function(){
			var amt = __str2num($(this).val());
			var max = __str2num($(this).attr('max'));

			if (amt > max){
				$(this).val(max);
			}
		});

		lfLoad();
	});

	function lfLoad(){
		$.ajax({
			type:'POST'
		,	url:'./vou_baby_suga_find.php'
		,	data:{
				'type':opener.type
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center"><div class="right">'+__num2str(col['cost'])+'</div></td>';
						html += '<td class="center">'+col['from']+'</td>';
						html += '<td class="center">'+col['to']+'</td>';
						html += '<td class="center"><div class="left"><span class="btn_pack small"><button id="btnDel" type="button" onclick="" disabled="true">삭제</button></span></div></td>';
						html += '</tr>';

						no ++;
					}
				}

				if (!html){
					html = '<tr><td class="center" colspan="10">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);

				if ($('tr',$('#tbodyList')).length > 1){
					$('#btnDel').attr('disabled',false);
				}

				var amt = __str2num($('td',$('#tbodyList')).eq(1).text());
				var from = $('td',$('#tbodyList')).eq(2).text().split('.').join('-');
				var to = $('td',$('#tbodyList')).eq(3).text().split('.').join('-');

				$('#txtAmt').val(amt);
				$('#txtFrom').val(from);
				$('#txtTo').val(to);
			}
		,	complite:function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfApply(){
		$.ajax({
			type:'POST'
		,	url:'./vou_baby_suga_apply.php'
		,	data:{
				'type':opener.type
			,	'amt':__str2num($('#txtAmt').val())
			,	'from':$('#txtFrom').val()
			,	'to':$('#txtTo').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				alert(result);
			}
		,	complite:function(result){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}
</script>
<form id="f" name="f" method="post">
<div class="title title_border">산모신생아 가격 설정</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="70px" span="2">
		<col width="180px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">구분</th>
			<th class="head">최대가격</th>
			<th class="head">적용가격</th>
			<th class="head">적용기간</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center"><div id="lblGbn" class="left" value=""></div></th>
			<td class="center"><div id="lblMax" class="right"></div></td>
			<td class="center"><input id="txtAmt" name="txt" value="0" max="0" class="number" style="width:100%;"></td>
			<td class="center">
				<input id="txtFrom" name="txt" value="" class="date"> ~
				<input id="txtTo" name="txt" value="" class="date">
			</td>
			<td class="center">
				<div class="left"><span class="btn_pack m"><button type="button" onclick="lfApply();">적용</button></span></div>
			</td>
		</tr>
	</tbody>
</table>
<div class="title title_border">변경내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">적용가격</th>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="5">
				<div id="divBody" style="width:100%; height:150px; overflow-x:hidden; overflow-y:auto;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="70px">
							<col width="70px" span="2">
							<col>
						</colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom bold" style="line-height:1.5em; padding-top:20px;" colspan="5">
				<div class="left bold">※ 서비스 가격 자율화안내</div>
				<div style="padding-left:20px;">- 2013년 2월 1일부터 시행</div>
				<div style="padding-left:20px;">- 서비스의 가격을 일정 범위 내에서 제공기관이 자율적으로 책정하도록 허용</div>
				<div style="padding-left:20px;">- 제공인력의 전문성 및 부가서비스에 따른 자율성을 20%범위에서 허용</div>
			</td>
		</tr>
	</tfoot>
</table>
</form>
<?
	include_once('../inc/_footer.php');
?>
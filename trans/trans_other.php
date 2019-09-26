<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="">은행명</th>
			<td class="">
				<select id="bankNm" name="cbo" style="width:auto;">
				<option value="">--</option><?
				$sql = 'SELECT code
						,      name
						  FROM bank
						 ORDER BY name';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);?>
					<option value="<?=$row['code'];?>" <? if ($memHis['bank_nm'] == $row['code']){?>selected<?} ?>><?=$row['name'];?></option><?
				}

				$conn->row_free();?>
				</select>
			</td>
			<td class="last"><span class="btn_pack find" style="margin-left:3px; margin-top:1px;" onclick="lfFindAcct();"></span></td>
		</tr>
		<tr>
			<th class="">계좌번호</th>
			<td class="last" colspan="2"><input id="bankNo" name="txt" type="text" style="width:150px;"></td>
		</tr>
		<tr>
			<th class="">예금주</th>
			<td class="last" colspan="2"><input id="bankAcct" name="txt" type="text" style="width:70px;"></td>
		</tr>
		<tr>
			<th class="">이체금액</th>
			<td class="last" colspan="2"><input id="amt" name="txt" type="text" value="0" style="width:70px;" class="number"></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
			<td class="left bottom last" colspan="2">
				<span class="btn_pack m"><button type="button" onclick="lfTrans();">이체요청</button></span>
				<span class="btn_pack m"><button type="button" onclick="lfReset();">다시쓰기</button></span>
			</td>
		</tr>
	</tfoot>
</table>

<div class="title title_border">금일 이체요청내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="150px">
		<col width="70px">
		<col width="70px">
		<col width="50px">
		<col width="60px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">은행명</th>
			<th class="head">계좌번호</th>
			<th class="head">예금주</th>
			<th class="head">이체금액</th>
			<th class="head">상태</th>
			<th class="head">요청시간</th>
			<th class="head">처리시간</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="list"></tbody>
</table>

<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
		lfTodayTrans();
	});

	function lfTodayTrans(){
		$.ajax({
			type: 'POST'
		,	url : './trans_other_search.php'
		,	data: {
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

						if (val[4] == '1'){
							val[4] = '요청';
						}else if (val[4] == '3'){
							val[4] = '<span style="color:blue;">성공</span>';
						}else if (val[4] == '5'){
							val[4] = '<span style="color:red;">실패</span>';
						}else if (val[4] == '9'){
							val[4] = '에러';
						}else{
						}

						html += '<tr>'
							 +  '<td class="center">'+(i+1)+'</td>'
							 +  '<td class="left">'+val[0]+'</td>'
							 +  '<td class="left">'+val[1]+'</td>'
							 +  '<td class="left">'+val[2]+'</td>'
							 +  '<td class="right">'+__num2str(val[3])+'</td>'
							 +  '<td class="center">'+val[4]+'</td>'
							 +  '<td class="center">'+val[5]+'</td>'
							 +  '<td class="center">'+val[6]+'</td>'
							 +  '<td class="left last">'+val[7]+'</td>'
							 +  '</tr>';
					}
				}

				if (!html){
					 html = '<tr>'
						  + '<td class="center last" colspan="8">::검색된 데이타가 없습니다.::</td>'
						  + '</tr>';
				}

				$('#list').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfFindAcct(){
		var objModal = new Object();
		var url      = './trans_acctno_find.php';
		var style    = 'dialogWidth:800px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		//objModal.code   = $(':input[name="code"]').attr('value');

		window.showModalDialog(url, objModal, style);

		if (!objModal.acctNm) return;

		$('#bankNm').val(objModal.bankNm);
		$('#bankNo').val(objModal.bankNo);
		$('#bankAcct').val(objModal.bankAcct);
		$('#amt').focus();
	}

	function lfTrans(){
		if (!$('#bankNm').val()){
			alert('은행명을 입력하여 주십시오.');
			$('#bankNm').focus();
			return;
		}

		if (!$('#bankNo').val()){
			alert('계좌번호를 입력하여 주십시오.');
			$('#bankNo').focus();
			return;
		}

		if (!$('#bankAcct').val()){
			alert('예금주명을 입력하여 주십시오.');
			$('#bankAcct').focus();
			return;
		}

		if (!$('#amt').val()){
			alert('이체금액을 입력하여 주십시오.');
			$('#amt').focus();
			return;
		}

		$.ajax({
			type: 'POST'
		,	url : './trans_other_request.php'
		,	data: {
				'bankNm':$('#bankNm').val()
			,	'bankNo':$('#bankNo').val()
			,	'bankAcct':$('#bankAcct').val()
			,	'amt':__str2num($('#amt').val())
			}
		,	beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success: function (result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}

				$('#tempLodingBar').remove();

				lfReset();
				lfTodayTrans();
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfReset(){
		$('input:text[name="txt"]').val('');
		$('#bankNm').val('');
		$('#amt').val('0');
	}
</script>
<?
	include_once('../inc/_db_close.php');
?>
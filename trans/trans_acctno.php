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
		<col width="30px">
		<col width="100px">
		<col width="100px">
		<col width="200px">
		<col width="100px">
		<col>
		<col width="50px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">관리명</th>
			<th class="head">은행명</th>
			<th class="head">계좌번호</th>
			<th class="head">예금주</th>
			<th class="head last" colspan="2">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">-</td>
			<td class="center"><input id="acctNm" name="txt" type="text" style="width:100%;"></td>
			<td class="center">
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
			<td class="center"><input id="bankNo" name="txt" type="text" style="width:100%;"></td>
			<td class="center"><input id="bankAcct" name="txt" type="text" style="width:100%;"></td>
			<td class="center"><input id="other" name="txt" type="text" style="width:100%;"></td>
			<td class="center last"><span class="btn_pack m"><button type="button" onclick="lfAdd();">추가</button></span></td>
		</tr>
	</tbody>
	<tbody id="list"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);

		lfSearch();
	});

	function lfSearchCall(){
		setTimeout('lfSearch()',10);
	}

	function lfSearch(){
		$.ajax({
			type: 'POST'
		,	url : './trans_acctno_search.php'
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

						html += '<tr onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">'
							 +  '<td class="center">'+(i+1)+'</td>'
							 +  '<td class="left">'+val[1]+'</td>'
							 +  '<td class="left">'+val[2]+'</td>'
							 +  '<td class="left">'+val[3]+'</td>'
							 +  '<td class="left">'+val[4]+'</td>'
							 +  '<td class="left">'+val[5]+'</td>'
							 +  '<td class="center last"><span class="btn_pack m"><button type="button" onclick="lfDelete(\''+val[0]+'\');">삭제</button></span></td>'
							 +  '</tr>';
					}
				}

				if (!html){
					 html = '<tr>'
						  + '<td class="center last" colspan="7">::검색된 데이타가 없습니다.::</td>'
						  + '</tr>';
				}

				$('#list').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfAdd(){
		if (!$('#acctNm').val()){
			 $('#acctNm').focus();
			 alert('관리명을 입력하여 주십시오.');
			 return;
		}

		if (!$('#bankNm').val()){
			 $('#bankNm').focus();
			 alert('은행명을 입력하여 주십시오.');
			 return;
		}

		if (!$('#bankNo').val()){
			 $('#bankNo').focus();
			 alert('계좌번호를 입력하여 주십시오.');
			 return;
		}

		if (!$('#bankAcct').val()){
			 $('#bankAcct').focus();
			 alert('예금주명을 입력하여 주십시오.');
			 return;
		}

		$.ajax({
			type: 'POST'
		,	url : './trans_acctno_add.php'
		,	data: {
				'acctNm':$('#acctNm').val()
			,	'bankNm':$('#bankNm').val()
			,	'bankNo':$('#bankNo').val()
			,	'bankAcct':$('#bankAcct').val()
			,	'other':$('#other').val()
			}
		,	beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success: function (result){
				$('input:text[name="txt"]').val('');
				$('#bankNm').val('');
				$('#tempLodingBar').remove();
				lfSearchCall();
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfDelete(aiSeq){
		if (!confirm('삭제후 데이타 복구가 불가능합니다.\n선택하신 계좌정보를 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type: 'POST'
		,	url : './trans_acctno_delete.php'
		,	data: {
				'seq':aiSeq
			}
		,	beforeSend: function (){
			}
		,	success: function (result){
				lfSearchCall();
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>
<?
	include_once('../inc/_db_close.php');
?>
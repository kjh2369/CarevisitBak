<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];

	$colgroup = '<col width="40px">
				 <col width="70px">
				 <col width="100px">
				 <col width="150px">
				 <col width="100px">
				 <col width="250px">
				 <col>';
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		lfAcctNo(1);
	});

	function lfSelect(aiSeq){
		opener.acctNm   = $('#acctNm_'+aiSeq).text();
		opener.bankNm   = $('#bankNm_'+aiSeq).text();
		opener.bankNo   = $('#bankNo_'+aiSeq).text();
		opener.bankAcct = $('#bankAcct_'+aiSeq).text();

		self.close();
	}

	function lfAcctNo(aiIdx){
		if (aiIdx == 1){
			var url = './trans_acctno_regno.php';
		}else{
			var url = './trans_acctno_member.php';
		}

		$.ajax({
			type: 'POST'
		,	url : url
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

						html += '<tr>'
							 +  '<td class="center">'+(i+1)+'</td>'
							 +  '<td class="center"><div id="acctNm_'+i+'" class="left">'+val[0]+'</div></td>'
							 +  '<td class="center"><div id="bankNm_'+i+'" class="left">'+val[1]+'</div></td>'
							 +  '<td class="center"><div id="bankNo_'+i+'" class="left">'+val[2]+'</div></td>'
							 +  '<td class="center"><div id="bankAcct_'+i+'" class="left">'+val[3]+'</div></td>'
							 +  '<td class="center"><div class="left">'+val[4]+'</div></td>'
							 +  '<td class="center"><a href="#" onclick="lfSelect(\''+i+'\');">선택</a></td>'
							 +  '</tr>';
					}
				}

				if (!html){
					 html = '<tr><td class="center" colspan="7">::검색된 데이타가 없습니다.::</td></tr>'
				}

				$('#list').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">이체계좌관리</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>구분</th>
			<td class="left">
				<a href="#" onclick="lfAcctNo(1);">등록계좌</a> |
				<a href="#" onclick="lfAcctNo(2);">직원계좌</a>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">관리명</th>
			<th class="head">은행명</th>
			<th class="head">계좌번호</th>
			<th class="head">예금주</th>
			<th class="head">비고</th>
			<th class="head">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top" colspan="7">
				<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:405px;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="list"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>
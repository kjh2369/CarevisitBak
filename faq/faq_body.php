<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$mode = $_GET['mode'];

	if ($mode == '101'){?>
		<script type="text/javascript">
			function lfView(obj){
				$.ajax({
					type: 'POST'
				,	url : './faq_view.php'
				,	data: {
						'id':$(obj).attr('id')
					}
				,	success: function (data){
						var html = '';

						html += '<tr class="removeTr">'
							 +  '<td class="center">-</td>'
							 +  '<td class="center" colspan="6">'
							 +  '<div class="left">'+data+'</div>'
							 +  '</td>'
							 +  '</tr>';

						$('.removeTr').remove();
						$(obj).after(html);
					}
				,	error: function (request, status, error){
						alert('[ERROR]'
							 +'\nCODE : ' + request.status
							 +'\nSTAT : ' + status
							 +'\nMESSAGE : ' + request.responseText);
					}
				});
			}

			function lfWrite(liId){
				var id = liId;

				if (!id) id = 0;

				var objModal = new Object();
				var url      = './faq_write.php';
				var style    = 'dialogWidth:600px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

				objModal.id		= id;
				objModal.mode	= '1';

				window.showModalDialog(url, objModal, style);
			}

			function lfDelete(obj){
			}
		</script>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="40px">
				<col width="150px">
				<col width="250px">
				<col width="70px">
				<col width="120px">
				<col width="50px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">No</th>
					<th class="head">구분</th>
					<th class="head">제목</th>
					<th class="head">작성자</th>
					<th class="head">작성일</th>
					<th class="head">조회</th>
					<th class="head last">비고</th>
				</tr>
			</thead>
			<tbody id="listBody"></tbody>
			<tfoot>
				<tr>
					<td class="center bottom last" colspan="10">
						<div style="float:right; width:auto;"><span class="btn_pack m"><button type="button" onclick="lfWrite(); return false;">작성</button></span></div>
						<div style="flato:center; width:auto;"><?
							include('../inc/_page_script.php');?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table><?
	}

	include_once('../inc/_db_close.php');
?>
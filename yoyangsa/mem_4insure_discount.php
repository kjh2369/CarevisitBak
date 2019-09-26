<?
	include_once('../inc/_login.php');
?>
<div style="clear:both; width:100%; margin-left:10px; margin-top:10px;">
	<script type="text/javascript">
		$(document).ready(function(){
			lfInsureDcSearch();
		});

		function lfInsureDcShow(){
			var objModal= new Object();
			var url		= './mem_4insure_dc_pop.php';
			var style	= 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

			objModal.jumin = $(':input[name="ssn"]').attr('value');

			window.showModalDialog(url, objModal, style);

			lfInsureDcSearch();
		}

		function lfInsureDcSearch(){
			$.ajax({
				type:'POST'
			,	url:'./mem_4insure_dc_q.php'
			,	data:{
					'jumin'	:$(':input[name="ssn"]').attr('value')
				,	'mode'	:'4'
				}
			,	beforeSend:function(){
				}
			,	success:function(html){
					$('#tbodyList').html(html);
				}
			,	error:function(){
				}
			}).responseXML;
		}
	</script>
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col width="150px">
			<col width="100px">
			<col width="200px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold" colspan="4">
					<div style="float:right; width:auto; padding-right:3px;"><span class="btn_pack m"><button onclick="lfInsureDcShow();">변경</button></span></div>
					<div style="float:center; width:auto;">장기요양보험료 경감 적용</div>
				</th>
			</tr>
			<tr>
				<th class="head">구분</th>
				<th class="head">감액률</th>
				<th class="head">적용기간</th>
				<th class="head">비고</th>
			</tr>
		</thead>
		<tbody id="tbodyList"></tbody>
	</table>
</div>
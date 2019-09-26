<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$jumin = $_POST['jumin'];
?>
<script type='text/javascript' src='./care.js'></script>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoadCenterName()',10);
		setTimeout('lfLoadMemberName()',10);
	});

	function lfLoadCenterName(){
		$.ajax({
			type :'POST'
		,	url  :'./care_fun.php'
		,	data :{
				'type':'1'
			}
		,	beforeSend: function(){
			}
		,	success: function(data){
				$('#lblCenterName').text(data);
			}
		});
	}

	function lfLoadMemberName(){
		$.ajax({
			type :'POST'
		,	url  :'./care_fun.php'
		,	data :{
				'type':'11'
			,	'jumin':'<?=$jumin;?>'
			}
		,	beforeSend: function(){
			}
		,	success: function(data){
				$('#lblMemberName').text(data);
			}
		});
	}
</script>
<div class="title title_border">기관 및 직원정보</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관명</th>
			<td class="center last"><div id="lblCenterName" class="nowrap left" style="width:180px;">&nbsp;</div></td>
		</tr>
		<tr>
			<th class="center bottom">직원명</th>
			<td class="center bottom last"><div id="lblMemberName" class="nowrap left" style="width:180px;">&nbsp;</div></td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
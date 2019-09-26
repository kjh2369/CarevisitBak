<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$suga	= $_POST['suga'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$from	= $_POST['from'];
	$to		= $_POST['to'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfLoadClient();
	});


	function lfLoadClient(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_resource_reg_client_search.php'
		,	data :{
				'sr':'<?=$sr;?>'
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			//,	'findClient':$('#findClient').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);

				$('input:checkbox[name="chk"]').unbind('click').bind('click',function(){
					var id = $(this).attr('id');
					var gbn = $(this).attr('gbn');

					if (gbn == 'A'){
						$('input:checkbox[id^="'+id+'"]').attr('checked',$(this).attr('checked'));
					}else{
						$('input:checkbox[id="'+id+'"]').attr('checked',$(this).attr('checked'));
					}
				});
			}
		,	complete:function(){

			}
		,	error:function(){
			}
		}).responseXML;
	}

</script>

<!--table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody >
		<th>대상자</th>
		<td><input id="findClient" name="findClient" value="" onkeypress="if(event.keyCode==13){lfLoadClient();}" ><span class="btn_pack m"><button onclick="lfLoadClient();">조회</button></span></td>
	</tbody>
</table-->
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody id="tbodyList"></tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
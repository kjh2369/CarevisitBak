<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];

	$sql = 'SELECT	seq
			FROM	stop_est
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cls_yn	= \'N\'
			AND		stop_yn	= \'N\'';
?>
<script type="text/javascript">
	function lfStopSet(IsYn){
		$.ajax({
			type:'POST'
		,	url:'./center_connect_reg_stopset_save.php'
		,	data:{
				'orgNo':'<?=$orgNo;?>'
			,	'seq':
			,	'stopDt':
			,	'defTxt':
			,	'defAmt':
			,	'clsYn':
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_TYPE_BODY').html(html);

				var obj = __GetTagObject($('#ID_CMS_LIST'),'DIV');
				$(obj).height(__GetHeight($(obj)));

				$('input:text',$('#ID_TYPE_BODY')).each(function(){
					__init_object(this);
				});
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">중지일자</th>
			<td>
				<input id="txtDate" type="text" class="date">
			</td>
		</tr>
		<tr>
			<th class="center">미납내역</th>
			<td>
				<input id="txtCont" type="text" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="center">미납금액</th>
			<td>
				<input id="txtAmt" type="text" class="number" style="width:70px;">
			</td>
		</tr>
	</tbody>
</table>
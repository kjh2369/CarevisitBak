<?
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$.ajax({
			type:'POST'
		,	url:'./popup_set_data.php'
		,	data:{
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				$('#ID_POPSET_BODY').html(data);
			}
		,	error: function (request, status, error){
			}
		});
	});
</script>
<div id="ID_POPSET_BODY" style="display:<?=!$debug ? 'none' : '';?>;"></div>
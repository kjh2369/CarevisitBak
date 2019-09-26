<?
	##################################################################
	#
	# 전화상담 기록지
	#
	##################################################################
?>
<script language='javascript'>
<!--

function show_counsel_phone(){
	go_phone_list();
	show_svc_layer('phone');
}

-->
</script>

<div id="svc_phone" style="display:none;">
<?
	include_once('../counsel/client_counsel_phone.php');
?>
</div>
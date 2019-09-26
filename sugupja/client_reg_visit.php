<?
	##################################################################
	#
	# 방문상담 기록지
	#
	##################################################################
?>
<script language='javascript'>
<!--

function show_counsel_visit(){
	go_visit_list();
	show_svc_layer('visit');
}

-->
</script>

<div id="svc_visit" style="display:none;">
<?
	include_once('../counsel/client_counsel_visit.php');
?>
</div>
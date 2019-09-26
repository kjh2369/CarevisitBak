<?
	##################################################################
	#
	# 사례관리
	#
	##################################################################
?>
<script language='javascript'>
<!--

function show_counsel_case(){
	go_case_list();
	show_svc_layer('case');
}

-->
</script>

<div id="svc_case" style="display:none;">
<?
	include_once('../counsel/client_counsel_case.php');
?>
</div>
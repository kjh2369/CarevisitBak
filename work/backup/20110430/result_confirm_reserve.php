<script language='javascript'>
	var requet1 = getHttpRequest('../work/result_check.php');
	var requet2 = 0; //getHttpRequest('../work/result_show_check.php');
	/*
	var w = 400;
	var h = 300;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;
	*/

	var l = window.screen.width;
	var t = window.screen.height;

	if (requet1 > 0 || requet2 > 0){
		var win = window.open('../work/result_proc.php', 'WORK_CONFIRM', 'left='+l+', top='+t+', width=0, height=0, toolbar=no, location=no, status=yes, menubar=no, scrollbars=no, resizable=no');
	}
</script>
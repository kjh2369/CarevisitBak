<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");
	
?>
<script>
	function lfView(cd,ssn){
		var f = document.f;
		f.code.value = cd;
		f.jumin.value = ssn;
		f.action = "../sub1/index.php?mtype=1_1&workerGbn=G&mode=2";
		f.submit();	
	}
</script>
<form method="post" name="f">
<?
	if($_GET['mode'] == 1){
		include_once("./hp_member_list.php");
	}else {
		include_once("./hp_member_write.php");
	}
?>

</form>

<?	

	include("../inc/_db_open.php");
	include("../inc/_http_referer.php");
	include("../inc/_ed.php");
	include("../inc/_myFun.php");

	$checkDept = $_POST['checkDept'];
	$dept_cd = $_POST['dept_cd'];
	$org_no = $_POST['org_no'];
	
	//üũ�迭ũ�⸸ŭ ������������ del_flag�� "Y"�� ������Ʈ�Ѵ�.
	for($i=0; $i<sizeof($checkDept); $i++){
		$sql = "update dept"
			. "    set del_flag = 'Y'"
			. "  where dept_cd = '".$dept_cd[$checkDept[$i]]
			. "'   and org_no = '".$org_no 
			. "'";

		$conn->execute($sql);	
	}
	
	include("../inc/_db_close.php");
?>
<script>
	location.replace('dept_list.php');
</script>

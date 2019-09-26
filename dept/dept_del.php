<?	

	include("../inc/_db_open.php");
	include("../inc/_http_referer.php");
	include("../inc/_ed.php");
	include("../inc/_myFun.php");

	$checkDept = $_POST['checkDept'];
	$dept_cd = $_POST['dept_cd'];
	$org_no = $_POST['org_no'];
	
	//체크배열크기만큼 루프를돌려서 del_flag를 "Y"로 업데이트한다.
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

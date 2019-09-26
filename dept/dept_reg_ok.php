<?
	/*
	1. 체크배열을 포스트로 받는다.
	2. 체크박스배열크기만큼 루틴을 돌린다.
	*/
	include("../inc/_db_open.php");
	include("../inc/_http_referer.php");
	include("../inc/_ed.php");
	include("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$checkDept = $_POST["checkDept"];
	$org_no = $_POST['org_no'];
	$dept_cd = $_POST['dept_cd'];
	$dept_nm = $_POST['dept_nm'];
	$order_seq = $_POST['order_seq'];


	$sql = "select ifnull(max(dept_cd), 0) + 1
			  from dept
			 where org_no = '$org_no'";
		$max_dept_code = $conn -> get_data($sql);


	//체크배열크기만큼 루프를 돌린다.
	for($i=0; $i<sizeof($checkDept); $i++){

		//체크배열에 값이 널이 아니고 부서코드가 값이 있다면 수정(업데이트)를 한다.
		if($checkDept[$i] != '' and $dept_cd[$checkDept[$i]] == true){
			$sql = "update dept
					   set dept_nm = '".$dept_nm[$checkDept[$i]]."'
						 , order_seq = '".$order_seq[$checkDept[$i]]."'
					 where org_no = '$org_no'
					   and del_flag = 'N'
					   and dept_cd = '".$dept_cd[$checkDept[$i]]."'";
			$conn -> execute($sql);

		}
		//체크배열에 값이 널이 아니고 부서코드 값이 0이라면.. insert를 한다.
		if($checkDept[$i] != '' and $dept_cd[$checkDept[$i]] == 0){
			$sql = " insert into dept (
					 org_no
					 ,dept_cd
					 ,dept_nm
					 ,del_flag
					 ,order_seq
					 ) values (
					 '".$org_no."'
					,'".$max_dept_code."'
					,'".$dept_nm[$checkDept[$i]]."'
					,'N'
					,'".$order_seq[$checkDept[$i]]."')";

			$conn -> execute($sql);

		$max_dept_code++;
		}

	}


	include("../inc/_db_close.php");
?>
<script>
	location.replace("dept_list.php");
</script>
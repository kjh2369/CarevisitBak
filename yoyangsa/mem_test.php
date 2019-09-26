<?
	include_once("../inc/_header.php");
	//include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
	
	$code = $_SESSION["userCenterCode"];

	$sql = "select *
			  from m02yoyangsa
			 where m02_ccode  = '$code'
			   and m02_mkind  = (select min(m02_mkind) from m02yoyangsa where m02_ccode = '$code' and m02_del_yn = 'N')
			   and m02_del_yn = 'N'";

	$conn->fetch_type = 'array';
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		for($j=0; $j<sizeof($row); $j++){
			$mst[$j][$i] = $row[$j];
		}
	}

	$conn->row_free();

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
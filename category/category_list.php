<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");
	include_once("../inc/_page_list.php");

	$sql = "select gubun, code, name, note
			  from tbl_category
			 where parent = '0'
			 order by seq";
	$conn->query($sql);
	$conn->fetch();
	$rCount1 = $conn->row_count();

	if ($rCount1 > 0){
		echo "<div style='text-align:left;'>";
		for($i=0; $i<$rCount1; $i++){
			$r1 = $conn->select_row($i);

			echo $r1['gubun'].' / '.$r1['name'].' / '.$r1['note'].'<br>';
		}
		echo "</div>";
	}else{
		echo "등록된 카테고리가 없습니다.";
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
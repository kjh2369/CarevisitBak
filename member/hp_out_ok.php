
<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	
	$code   = $_GET['code'];
	$id     = $_GET['id'];
	
	$sql = "update member
			   set del_yn = 'Y'
			 where org_no = '".$code."'
			   and code = '".$id."'
			   and del_yn = 'N'";
	if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script type="text/javascript">alert("데이타 삭제중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오."); history.back();</script>';
			exit;
	}

include_once("../inc/_db_close.php");
?>

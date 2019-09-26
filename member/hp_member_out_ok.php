
<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");
	
	/********************************************************************

	늘푸른돌봄센터,성동돌봄센터,어르신편안하게돌보는사람들(회원탈퇴,삭제)
	
	**********************************************************************/
	
	
	$code   = $_GET['code'];	//기관코드	
	$m_id   = $_GET['id'];		//회원아이디
	
	$conn->begin();

	$sql = "update member
			   set del_yn = 'Y'
			 where org_no = '".$code."'
			   and code = '".$m_id."'
			   and del_yn = 'N'";
	if (!$conn->execute($sql)){
			$conn->rollback();
			echo '<script type="text/javascript">alert("데이타 삭제중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오."); history.back();</script>';
			exit;
	}
	
	$conn->commit();

include_once("../inc/_db_close.php");

?>
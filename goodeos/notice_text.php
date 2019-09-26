<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_login.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");

	$id   = $_REQUEST['id'];
	$code = $_SESSION['userCode'];

	if (!empty($id) && !empty($code)){
		$sql = 'update popup_notice
				   set read_yn   = \'Y\'
				 where notice_id = \''.$id.'\'
				   and org_no    = \''.$code.'\'';

		$conn->execute($sql);

		$sql = 'select content
				  from tbl_goodeos_notice
				 where id = \''.$id.'\'';

		$str = $conn->get_data($sql);

		echo $str;?>

		<div class="clear left bold" style="margin-top:20px;">※첨부파일</div><?
		$sql = 'SELECT	seq
				,		file_path
				,		file_name
				,		file_type
				,		file_size
				FROM	attach_file
				WHERE	id = \'NOTICE_'.$gDomainID.'\'
				AND		attach_key = \''.$id.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$size = $row['file_size'];

			$depth = 0;
			while(true){
				if ($size > 1000){
					$size /= 1000;
					$depth ++;
				}else{
					break;
				}
			}

			$size = Round($size,2);

			if ($depth == 1){
				$gbn = 'KB';
			}else if ($depth == 2){
				$gbn = 'MB';
			}else if ($depth == 3){
				$gbn = 'GB';
			}else if ($depth == 4){
				$gbn = 'TB';
			}else{
				$gbn = 'Byte';
			}?>
			<div class="clear left">
				<div style="float:left; width:auto;"><a href="#" onclick="lfAttachDownload('<?=$id;?>','<?=$row['seq'];?>'); return false;"><?=$row['file_name'];?></a>[<?=$size.$gbn;?>]</div>
			</div><?
		}

		$conn->row_free();
	}

	include_once("../inc/_db_close.php");
?>
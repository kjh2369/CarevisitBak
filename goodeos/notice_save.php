<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$id			= $_POST['id'];
	$subject	= addslashes($_POST['subject']);
	$content	= $_POST['back_content'];
	$datetime	= mkTime();
	$ip_addr	= $_SERVER['REMOTE_ADDR'];
	$page		= $_POST['page'];

	if (!is_numeric($id)){
		$sql = 'select ifnull(max(id),0)+1
				  from tbl_goodeos_notice';

		$id = $conn->get_data($sql);

		$sql = "insert into tbl_goodeos_notice values (
				 '$id'
				,'$subject'
				,'$content'
				,'$datetime'
				,'$ip_addr'
				,'$gDomainID'
				,'N')";
	}else{
		$sql = "update tbl_goodeos_notice
				   set subject = '$subject'
				,      content = '$content'
				,      reg_ip  = '$ip_addr'
				 where id      = '$id'";
	}

	if (!$conn->execute($sql)){
		 echo $conn->error_msg;
	}


	$useYn  = $_POST['useYn']; //팝업사용여부
	$fromDt = $_POST['fDt'];   //적용기간
	$toDt   = $_POST['tDt'];   //적용기간
	$popCd  = $_POST['popCd'];

	$sql = 'delete
			  from popup_notice
			 where notice_id = \''.$id.'\'';

	$conn->execute($sql);

	if ($popCd == 'all'){
		$popCd = '';

		$sql = 'select b02_center as cd
				,      min(b02_kind) as kind
				  from b02center
				 inner join b00branch
					on b00_code   = b02_branch
				   and b00_domain = \''.$gDomain.'\'
				 group by b02_center';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$popCd .= '/'.$row['cd'];
		}

		$conn->row_free();
	}

	$popCd = explode('/', $popCd);

	if (is_array($popCd)){
		foreach($popCd as $cd){
			if (!empty($cd)){
				$sql = 'insert into popup_notice (
						 notice_id
						,org_no
						,from_dt
						,to_dt
						,read_yn) values (
						 \''.$id.'\'
						,\''.$cd.'\'
						,\''.$fromDt.'\'
						,\''.$toDt.'\'
						,\'N\'
						)';
				$conn->execute($sql);
			}
		}
	}

	$fileCnt = SizeOf($_FILES['attachFile']['tmp_name']);

	if ($fileCnt > 0){
		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	attach_file
				WHERE	id = \'NOTICE_'.$gDomainID.'\'
				AND		attach_key = \''.$id.'\'';
		$seq = $conn->get_data($sql);

		for($i=0; $i<$fileCnt; $i++){
			$files = $_FILES['attachFile'];
			$tmpName = $files['tmp_name'][$i];

			if ($tmpName){
				$tmpInfo	= pathInfo($files['name'][$i]);
				$exp		= StrToLower($tmpInfo['extension']);
				$fileIdx	= 1;
				$fileStr	= mktime();
				$filePath	= $fileStr.'_'.$fileIdx;
				$fileName	= $files['name'][$i];
				$fileType	= $files['type'][$i];
				$fileSize	= $files['size'][$i];

				while(true){
					if (is_file('./files/'.$filePath)){
						$fileIdx ++;
						$filePath = $fileStr.'_'.$fileIdx;
					}else{
						break;
					}
				}

				if (move_uploaded_file($tmpName,'./files/'.$filePath)){
					$sql = 'INSERT INTO attach_file VALUES (
							 \'NOTICE_'.$gDomainID.'\'
							,\''.$id.'\'
							,\''.$seq.'\'
							,\''.$filePath.'\'
							,\''.$fileName.'\'
							,\''.$fileType.'\'
							,\''.$fileSize.'\'
							,\''.$_SESSION['userCode'].'\'
							,NOW()
							)';

					$attachQuery[] = $sql;

					$seq ++;
				}
			}
		}

		if (is_array($attachQuery)){
			foreach($attachQuery as $sql){
				$conn->execute($sql);
			}
			$conn->commit();
		}
	}

	$conn->close();
?>
<script>
	location.replace('notice_list.php?page=<?=page;?>');
</script>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$type	= $_POST['type'];
	$cd		= $_POST['cd'];
	$id		= $_POST['seq'];

	if ($debug){
		print_r($_POST);
		print_r($_FILES);
		exit;
	}

	if (is_array($_FILES['file'])){
		$f = $_FILES['file'];

		//파일 순번
		$sql = 'SELECT	IFNULL(MAX(file_id),0)+1
				FROM	board_file
				WHERE	org_no	= \''.$orgNo.'\'
				AND		brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		brd_cd	= \''.$cd.'\'
				AND		brd_id	= \''.$id.'\'';

		$seq = $conn->get_data($sql);

		foreach($f['name'] as $i => $t){
			if ($f['tmp_name'][$i]){
				$path = './files/'.$type;
				if (!is_dir($path)) mkdir($path);

				$path .= '/'.$orgNo;
				if (!is_dir($path)) mkdir($path);

				$path .= '/'.$gDomainID.'_'.$cd.'_'.$id.'_'.$seq;

				if (move_uploaded_file($f['tmp_name'][$i], $path)){
					$sql = 'INSERT INTO board_file (
							 org_no
							,brd_type
							,dom_id
							,brd_cd
							,brd_id
							,file_id
							,file_name
							,file_size
							,file_type) VALUES (
							 \''.$orgNo.'\'
							,\''.$type.'\'
							,\''.$gDomainID.'\'
							,\''.$cd.'\'
							,\''.$id.'\'
							,\''.$seq.'\'
							,\''.$f['name'][$i].'\'
							,\''.$f['size'][$i].'\'
							,\''.$f['type'][$i].'\'
							)';
					$seq ++;

					$query[] = $sql;
				}
			}
		}
	}

	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 'ERROR';
				 exit;
			}
		}

		$conn->commit();
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>
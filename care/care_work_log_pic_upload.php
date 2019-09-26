<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$jumin	= $ed->de($_POST['jumin']);
	$sugaCd	= $_POST['sugaCd'];
	$target	= $ed->de($_POST['target']);
	$date	= $_POST['date'];

	$sql = 'SELECT	sub_key
			FROM	care_work_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		suga_cd	= \''.$sugaCd.'\'
			AND		mem_cd	= \''.$jumin.'\'
			AND		date	= \''.$date.'\'
			AND		jumin	= \''.$target.'\'';

	$key = $conn->get_data($sql);

	//기관폴더 생성
	$path = './work_log/'.$orgNo;
	if (!is_dir($path)) mkdir($path);

	//서비스폴더 생성
	$path .= '/'.$SR;
	if (!is_dir($path)) mkdir($path);

	//일자폴더 생성
	$path .= '/'.$date;
	if (!is_dir($path)) mkdir($path);

	foreach($_FILES as $name => $pic){
		$seq = Explode('_',$name);
		$seq = $seq[1];

		if ($pic['tmp_name']){
			$name	= $pic['name'];
			$exp	= Explode('.',$name);
			$exp	= $exp[SizeOf($exp)-1];
			$type	= $pic['type'];
			$size	= $pic['size'];

			$saveFile = $path.'/'.$key.'_'.$seq.'.'.$exp;

			if (move_uploaded_file($pic['tmp_name'], $saveFile)){
				$sql = 'UPDATE	care_work_log_sub
						SET		pic_path	= \''.$saveFile.'\'
						,		pic_name	= \''.$name.'\'
						,		pic_size	= \''.$size.'\'
						,		pic_type	= \''.$type.'\'
						WHERE	org_no		= \''.$orgNo.'\'
						AND		org_type	= \''.$SR.'\'
						AND		sub_key		= \''.$key.'\'
						AND		seq			= \''.$seq.'\'';

				$query[] = $sql;
			}
		}
	}

	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 1;
				 exit;
			}
		}

		$conn->commit();
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>
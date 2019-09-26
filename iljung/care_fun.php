<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$type = $_POST['type'];

	if ($type == '1'){
		//기관정보
		$name = $conn->_storeName($code).'('.$code.')';
		echo $name;

	}else if ($type == '11'){
		//직원정보
		$jumin = $ed->de($_POST['jumin']);

		$sql = 'SELECT	m02_yname
				FROM	m02yoyangsa
				WHERE	m02_ccode = \''.$code.'\'
				AND		m02_yjumin = \''.$jumin.'\'
				ORDER	BY m02_mkind
				LIMIT	1';

		$name = $conn->get_data($sql).'('.$myF->issStyle($jumin).')';
		echo $name;

	}else{
	}

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	/*********************************************************

	가족요양보호사 조회

	*****************************************************/
	$sql = 'select cf_mem_cd as cd
			,      cf_mem_nm as nm
			,      cf_kind as kind
			  from client_family
			 where org_no   = \''.$code.'\'
			   and cf_jumin = \''.$jumin.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$lsStr = '';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$lsStr .= 'nm='.$row['nm'].'&cd='.$ed->en($row['cd']).'&kind='.$row['kind'].';';
	}

	$conn->row_free();

	echo $lsStr;

	include_once('../inc/_db_close.php');
?>
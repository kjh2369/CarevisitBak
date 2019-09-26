<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 * 게시판 카테고리 생성
	 */

	//$gDomainID
	$type	= $_POST['type'];
	$cd		= $_POST['cd'];
	$name	= $_POST['name'];
	$parent	= $_POST['parent'];
	$seq	= $_POST['seq'];

	if (!$seq) $seq = 1;

	if ($cd > 0){
		//수정
	}else{
		//신규
		$sql = 'SELECT	IFNULL(MAX(cd),0)+1
				FROM	board_category
				WHERE	brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'';

		$cd = $conn->get_data($sql);

		$sql = 'INSERT INTO board_category (
				 brd_type
				,dom_id
				,cd
				,name
				,parent
				,seq
				,insert_id
				,insert_dt) VALUES (
				 \''.$type.'\'
				,\''.$gDomainID.'\'
				,\''.$cd.'\'
				,\''.$name.'\'
				,\''.$parent.'\'
				,\''.$seq.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>
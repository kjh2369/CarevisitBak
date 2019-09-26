<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	DISTINCT t01_jumin AS jumin
			FROM	t01iljung
			LEFT	JOIN	t13sugupja
					ON		t13_ccode	= t01_ccode
					AND		t13_mkind	= t01_mkind
					AND		t13_jumin	= t01_jumin
					AND		t13_pay_date= \''.$yymm.'\'
					AND		t13_type	= \'2\'
			WHERE	t01_ccode	= \''.$orgNo.'\'
			AND		t01_mkind	= \'0\'
			AND		t01_del_yn	= \'N\'
			AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
			AND		t13_jumin IS NULL';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$sql = 'DELETE
				FROM	t01iljung
				WHERE	t01_ccode	= \''.$orgNo.'\'
				AND		t01_mkind	= \'0\'
				AND		t01_jumin	= \''.$row['jumin'].'\'
				AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'';
		$query[] = $sql;
	}

	$conn->row_free();

	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo '본인부담금 누락 대상자의 일정 삭제 중 오류가 발생하였습니다. 관리자에게 문의하여 주십시오.';
				 exit;
			}
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');
?>
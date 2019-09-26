<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$sr		= $_POST['sr'];
	$data	= Explode(chr(13),$_POST['data']);

	$sql = 'SELECT	iljung_dt AS dt
			,		iljung_seq AS seq
			FROM	care_counsel_iljung
			WHERE	org_no = \''.$code.'\'
			AND		jumin = \''.$jumin.'\'
			AND		iljung_sr = \''.$sr.'\'
			AND		LEFT(iljung_dt,6) = \''.$year.$month.'\'';

	$seq = $conn->_fetch_array($sql,'dt');

	$conn->begin();

	//기존일정삭제
	$sql = 'DELETE
			FROM	care_counsel_iljung
			WHERE	org_no = \''.$code.'\'
			AND		jumin = \''.$jumin.'\'
			AND		iljung_sr = \''.$sr.'\'
			AND		LEFT(iljung_dt,6) = \''.$year.$month.'\'
			AND		iljung_stat = \'9\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	if (Is_Array($data)){
		foreach($data as $row){
			if ($row){
				parse_str($row,$col);

				$day = IntVal($col['day']);
				$day = ($day < 10 ? '0' : '').$day;
				$day = $year.$month.$day;
				$cd = $ed->de($col['jumin']);
				$from = $col['from'];
				$to = $col['to'];
				$proc = $col['proc'];

				$seq[$day] ++;

				$sql = 'INSERT INTO care_counsel_iljung(
						 org_no
						,jumin
						,iljung_sr
						,iljung_dt
						,iljung_seq
						,iljung_jumin
						,iljung_from
						,iljung_to
						,iljung_proc) VALUES (
						 \''.$code.'\'
						,\''.$jumin.'\'
						,\''.$sr.'\'
						,\''.$day.'\'
						,\''.$seq[$day].'\'
						,\''.$cd.'\'
						,\''.$from.'\'
						,\''.$to.'\'
						,\''.$proc.'\'
						)';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo 9;
					 exit;
				}
			}
		}
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>
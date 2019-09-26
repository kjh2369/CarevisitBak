<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$lsDt = $_POST['date'];
	$lsYM = substr($lsDt,0,6);
	$lsFamilyYn = $_POST['familyYn'];
	$lsSugaCd = $_POST['sugaCd'];
	$liSugaVal = $_POST['sugaVal'];

	if ($lsFamilyYn == 'Y'){
		$lsSugaCd .= 'WC';
	}else{
		if (date('w',strtotime($lsDt)) == 0){
			$lsSugaCd .= 'H';
		}else{
			$sql = 'select count(*)
					  from tbl_holiday
					 where mdate = \''.$lsDt.'\'';

			$liCnt = $conn->get_data($sql);

			if ($liCnt > 0){
				$lsSugaCd .= 'H';
			}else{
				$lsSugaCd .= 'W';
			}
		}

		$lsSugaCd .= 'S';
	}

	echo $mySuga->getSugaTime($lsYM, $lsSugaCd, $liSugaVal);

	include_once('../inc/_db_close.php');
?>
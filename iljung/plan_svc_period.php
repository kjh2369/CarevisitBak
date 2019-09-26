<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$conn->fetch_type = 'assoc';

	$code	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$jumin	= $_POST['jumin'];
	$year	= $_POST['year'];

	if (!$year){
		$year	= Date('Y');
	}

	if (!Is_Numeric($jumin)){
		$jumin	= $ed->de($jumin);
	}

	$sql = 'SELECT	from_dt
			,		to_dt
			FROM	client_his_svc
			WHERE	org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		svc_cd	= \''.$svcCd.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt	= $conn->row_count();
	$lbEnd	= false;

	for($i=0; $i<$rowCnt; $i++){
		$row	= $conn->select_row($i);
		$tmpDt	= Explode('-',$row['from_dt']);
		$fromY	= IntVal($tmpDt[0]);
		$fromM	= IntVal($tmpDt[1]);

		$tmpDt	= Explode('-',$row['to_dt']);
		$toY	= IntVal($tmpDt[0]);
		$toM	= IntVal($tmpDt[1]);

		if ($fromY <= $year && $toY >= $year){
			for($y=$fromY; $y<=$toY; $y++){
				if ($y == $year){
					if (!$arrYM[$y]){
						$arrYM[$y]	= Array(
								1	=>'N'
							,	2	=>'N'
							,	3	=>'N'
							,	4	=>'N'
							,	5	=>'N'
							,	6	=>'N'
							,	7	=>'N'
							,	8	=>'N'
							,	9	=>'N'
							,	10	=>'N'
							,	11	=>'N'
							,	12	=>'N'
						);
					}

					if ($y == $fromY){
						$startM	= $fromM;
					}else{
						$startM	= 1;
					}

					if ($y == $toY){
						$endM	= $toM;
					}else{
						$endM	= 12;
					}

					for($m=$startM; $m<=$endM; $m++){
						$arrYM[$y][$m]	= 'Y';
					}
				}else if ($y > $year){
					$lbEnd	= true;
					break;
				}
			}

			if ($lbEnd){
				break;
			}
		}
	}

	$conn->row_free();

	if (Is_Array($arrYM)){
		foreach($arrYM as $year => $arrM){
			foreach($arrM as $month => $val){
				if (!Empty($tmp)){
					$tmp	.= '&';
				}

				$tmp	.= $month.'='.($val == 'Y' ? 'Y' : '');
			}

			//$data	.= $year.chr(2).$tmp.chr(1);
			$data	= $tmp.'&year='.$year;

			Unset($tmp);
		}
	}

	echo $data;

	include_once('../inc/_db_close.php');
?>
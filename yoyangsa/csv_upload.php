<?
	include("../inc/_db_open.php");
	include_once('../inc/_myFun.php');

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mDuplicate = $_POST['duplicateYN'];
	$mFile = $mCode.'_'.$mKind.'_'.date('Ymd', mkTime()).'_'.date('His', mkTime()).'.csv';

	//echo $mCode.'<br>'.$mKind.'<br>'.$mFile;

	$f = $_FILES['excel'];

	if ($f['tmp_name'] != ''){
		if (move_uploaded_file($f['tmp_name'], './excel/'.$mFile)){
			// 업로드 성공
			$upload = true;
		}else{
			// 업로드 실패
			$upload = false;
		}
	}else{
		// 업로드 실패
		$upload = false;
	}

	if ($upload != true){
		echo '
			<script language="javascript">
				alert("파일업로드중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.");
				history.back();
			</script>
			 ';
		exit;
	}

	header('Content-type: text/html; charset=utf-8'); // 문자셋

	error_reporting(E_ALL ^ E_NOTICE);

	$file = './excel/'.$mFile;
	$row_no = 1;
	
	if (($handle = fopen($file, "r")) !== FALSE) {
		while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($row_no > 1){
				/*
				$cnt = count($row);

				for ($c=0; $c < $cnt; $c++) {
					echo $myF->utf($row[$c]) . "<br />\n";
				}
				*/
				$name  = $myF->utf($row[0]); //성명
				$jumin = str_replace('-', '', $myF->utf($row[1])); //주민번호

				if (strlen($jumin) == 13){
					
					$total = 0; // 변수초기화
					$id = $jumin; // 두개로 나눠져잇는 값을 하나로 합쳐 id변수에 저장

					for($ii=0; $ii<13; $ii++){
						$id[$ii] = intval($id[$ii]); //정수화함
					}

					$total = $id[0]*2 + $id[1]*3 + $id[2]*4 + $id[3]*5 + $id[4]*6 + $id[5]*7 + $id[6]*8 +
							 $id[7]*9 + $id[8]*2 + $id[9]*3 + $id[10]*4 + $id[11]*5; //주민등록번호 식에 맞게 계산

					$total = $total%11; //계산
					$total2 = 11-$total; // 계산 (위에 원리참조)

					if($total2>9) $total2 = $total2 % 10; //체크숫자는 무조건 1의자리니 10이상의 나머지가나온다면 1의자리로 변환함

					$total = $id[12]; // 체크숫자를 total함수에 저장

					// 체크숫자와 나머지가 값이 같은가확인
					if ($total != $total2){
						$error .= ($error != '' ? '\n' : '').$row_no.'라인 '.$name;
					}
					
				}else{
					if($jumin != '') $error .= ($error != '' ? '\n' : '').$row_no.'라인 '.$name;
				}
			}
			$row_no ++;
		}
		fclose($handle);
		
		if ($error != ''){
			$error .= '의 주민번호가 올바르지 않습니다. 확인 후 다시 시도하여 주십시오.';
			$error  = str_replace(chr(13).chr(10), '', $error);
			$error  = str_replace(chr(13), '', $error);
			$error  = str_replace(chr(10), '', $error);

			echo $myF->message($error, 'Y', 'Y');
			exit;
		}
		
	}else{
		echo $myF->message('업로드하신 파일을 찾을 수 없습니다. 잠시후 다시 시도하여 주십시오.', 'Y', 'Y');
		exit;
	}


	echo '
		<script language="javascript">
			location.replace("csv_insert.php?p_code='.$mCode.'&p_kind='.$mKind.'&p_file='.$mFile.'&duplicateYN='.$mDuplicate.'");
		</script>
		 ';
?>
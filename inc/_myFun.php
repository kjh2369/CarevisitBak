<?
	class myFun{
		// 분을 시분으로 표시
		function getMinToHM($pMin){
			if (abs(intVal($pMin / 60)) > 0){
				$result = intVal($pMin / 60).'시간';
			}else{
				$result = '';
			}

			if (abs(intVal($pMin % 60)) > 0){
				if ($result == ''){
					$result .= ($pMin % 60).'분';
				}else{
					$result .= abs($pMin % 60).'분';
				}
			}

			if ($result == '') $result = '0분';
			return $result;
		}

		// 분을 시분으로 표시
		function minToHM($pMin){
			if (abs(intVal($pMin / 60)) > 0){
				$result = intVal($pMin / 60).'H';
			}else{
				$result = '';
			}

			if (abs(intVal($pMin % 60)) > 0){
				if ($result == ''){
					$result .= ($pMin % 60).'M';
				}else{
					$result .= abs($pMin % 60).'M';
				}
			}

			if ($result == '') $result = '0M';
			return $result;
		}

		//
		function numberFormat($pVal, $pText = ''){
			if ($pVal != 0){
				$result = number_format($pVal).$pText;
			}else{
				if ($pText != ''){
					$result = '0'.$pText;
				}else{
					$result = '';
				}
			}
			return $result;
		}

		// 전화번호 스타일
		function phoneStyle($phone, $split = '-'){
			$phone = Trim($phone);
			$phone = str_replace("-","",$phone);
			$phone = str_replace(")","",$phone);
			$phone = str_replace(".","",$phone);

			if (substr($phone, 0, 2) == "02"){
				$phone_1 = substr($phone,0,2);
				$phone   = substr($phone,2,strLen($phone));
				$phone_3 = substr($phone,strLen($phone)-4,4);
				$phone   = substr($phone,0,strLen($phone)-4);
				$phone_2 = $phone;
			}else{
				$phone_1 = substr($phone,0,3);
				$phone   = substr($phone,3,strLen($phone));
				$phone_3 = substr($phone,strLen($phone)-4,4);
				$phone   = substr($phone,0,strLen($phone)-4);
				$phone_2 = $phone;
			}

			if ($phone_1 == "02" or
				$phone_1 == "051" or
				$phone_1 == "053" or
				$phone_1 == "032" or
				$phone_1 == "062" or
				$phone_1 == "042" or
				$phone_1 == "052" or
				$phone_1 == "031" or
				$phone_1 == "033" or
				$phone_1 == "043" or
				$phone_1 == "041" or
				$phone_1 == "063" or
				$phone_1 == "061" or
				$phone_1 == "054" or
				$phone_1 == "055" or
				$phone_1 == "064"){
				$temp_phone_no = $phone_1.$split.$phone_2.$split.$phone_3;
			}else{
				$temp_phone_no = $phone_1.$split.$phone_2.$split.$phone_3;
			}

			$temp_phone_no = str_replace('--','',$temp_phone_no);
			$temp_phone_no = str_replace('..','',$temp_phone_no);

			return $temp_phone_no;
		}

		// 사업자번호
		function bizStyle($biz){
			if (strLen($biz) == 10){
				$biz = substr($biz,0,3)."-".substr($biz,3,2)."-".substr($biz,5,5);
			}
			return $biz;
		}

		function yymmddStyle($date, $gbn = '/'){
			if (!$date) return;

			$date = Explode('-', $this->dateStyle($date));
			$y = SubStr($date[0], 2, 2);
			$m = $date[1];
			$d = $date[2];

			$ymd = $y.$gbn.$m.$gbn.$d;

			return $ymd;
		}

		// 일자 스타일
		function dateStyle($date, $val = '-', $format = 'YMD'){
			$date = str_replace('/','',$date);
			$date = str_replace('-','',$date);
			$date = str_replace('.','',$date);

			if (intval($date) == 0) return '';

			if (strLen($date) == 8){
				$year = substr($date,0,4);
				$month = substr($date,4,2);
				$day = substr($date,6,2);

				if (strtoupper($val) == 'KOR'){
					$date = '';

					if (is_numeric(StrPos($format, 'Y'))) $date .= IntVal($year).'년 ';
					if (is_numeric(StrPos($format, 'M'))) $date .= IntVal($month).'월 ';
					if (is_numeric(StrPos($format, 'D'))) $date .= IntVal($day).'일';

					//$date = substr($date,0,4).'년 '.intval(substr($date,4,2)).'월 '.intval(substr($date,6,2)).'일';
				}else{
					$date = '';

					if (is_numeric(StrPos($format, 'Y'))) $date .= $year;
					if (is_numeric(StrPos($format, 'M'))) $date .= ($date ? $val : '').$month;
					if (is_numeric(StrPos($format, 'D'))) $date .= ($date ? $val : '').$day;

					//$date = substr($date,0,4).$val.substr($date,4,2).$val.substr($date,6,2);
				}
			}else{
				$date = "";
			}
			return $date;
		}


		// 시간 스타일
		function timeStyle($time){
			$time  = str_replace(':','',$time);
			$value = '';
			for($i=0; $i<strLen($time); $i=$i+2){
				$value .= ($value != '' ? ':' : '').subStr($time, $i, 2);
			}
			return $value;
		}

		// 절사
		function cutOff($val, $cut = 10){
			//$val = floor($val);
			//return $val - ($val % $cut);
			return floor($val - ($val % $cut));
		}

		// 주민번호 출력
		function issStyle($iss){
			if (strLen($iss) != 13) return '';
			return subStr($iss, 0, 6).'-'.subStr($iss, 6, 1).'******';
		}

		function issNo($iss){
			if (strLen($iss) != 13) return '';
			return subStr($iss, 0, 6).'-'.subStr($iss, 6, 7);
		}

		//주민번호 7자리까지 포멧 출력
		function issSsn7($iss){
			if (strLen($iss) != 7) return '';
			return subStr($iss, 0, 6).'-'.subStr($iss, 6, 1);
		}

		// 주민번호로 나이를 구한다
		function issToAge($iss, $year = 0){
			if ($year > 0)
				$year1 = $year;
			else
				$year1 = date('Y');

			$year2 = $this->issToYear(subStr($iss, 6, 1)) + intVal(subStr($iss, 0, 2));
			$age = $year1 - $year2;

			return $age;
		}

		// 주민번호 7번째 구분자로 연도를 산출한다.
		function issToYear($gubun){
			switch($gubun){
				case "1":
					$value = 1900;
					break;
				case "2":
					$value = 1900;
					break;
				case "9":
					$value = 1800;
					break;
				case "0":
					$value = 1800;
					break;
				default:
					$value = 2000;
			}

			return $value;
		}

		// 주민번호로 생년월일을 판단한다.
		function issToBirthday($juminNo, $gubunja = '-'){
			$juminNo = str_replace('-','',$juminNo);
			if (strLen($juminNo) < 6) return '';

			$value = Trim($juminNo);
			$gubun = substr($value, 6, 1);
			$value = substr($value, 0, 2).$gubunja.substr($value, 2, 2).$gubunja.substr($value, 4, 2);

			switch($gubun){
				case "1":
					$value = "19".$value;
					break;
				case "2":
					$value = "19".$value;
					break;
				case "9":
					$value = "18".$value;
					break;
				case "0":
					$value = "18".$value;
					break;
				case "5":
					$value = "19".$value;
					break;
				case "6":
					$value = "19".$value;
					break;
				default:
					$value = "20".$value;
			}
			return $value;
		}

		// 주민번호로 성별을 판단한다.
		function issToGender($juminNo){
			if (strLen($juminNo) != 13) return '';

			$gender = intval(substr($juminNo,6,1));

			if ($gender % 2 == 1){
				$gender = '남';
			}else{
				$gender = '여';
			}
			return $gender;
		}

		function splits($text, $length){
			if (mb_strlen($text,"UTF-8") > $length){
				$value = mb_substr($text, 0, $length,"UTF-8")."...";
			}else{
				$value = $text;
			}
			return $value;
		}

		function left($text, $length){
			if (mb_strlen($text,"UTF-8") > $length){
				$value = mb_substr($text, 0, $length,"UTF-8");
			}else{
				$value = $text;
			}
			return $value;
		}

		function mid($text, $start, $end){
			return mb_substr($text, $start, $end,"UTF-8");
		}

		function len($text){
			return mb_strlen($text,"UTF-8");
		}

		// 날짜계산
		function dateAdd($interval, $number, $date, $format = ""){
			/*
			 * year : 년도
			 * month : 월
			 * day(date) : 일자
			 */
			if ($format == ""){
				$date = explode("-", date("Y", strToTime("$number $interval $date")));

				return mkTime(0, 0, 1, $date[1], $date[2], $date[0]);
			}else{
				return date($format, strToTime("$number $interval $date"));
			}
		}

		// 날짜차이
		function dateDiff($interval, $datefrom, $dateto, $using_timestamps = false) {
			/*
			$interval can be:
			yyyy - Number of full years
			q - Number of full quarters
			m - Number of full months
			y - Difference between day numbers
				(eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
			d - Number of full days
			w - Number of full weekdays
			ww - Number of full weeks
			h - Number of full hours
			n - Number of full minutes
			s - Number of full seconds (default)
			*/

			if (!$using_timestamps) {
				$datefrom = strtotime($datefrom, 0);
				$dateto = strtotime($dateto, 0);
			}
			$difference = $dateto - $datefrom; // Difference in seconds

			switch($interval) {

			case 'yyyy': // Number of full years

				$years_difference = floor($difference / 31536000);
				if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
					$years_difference--;
				}
				if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
					$years_difference++;
				}
				$datediff = $years_difference;
				break;

			case "q": // Number of full quarters

				$quarters_difference = floor($difference / 8035200);
				while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
					$months_difference++;
				}
				$quarters_difference--;
				$datediff = $quarters_difference;
				break;

			case "m": // Number of full months

				$months_difference = floor($difference / 2678400);
				while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
					$months_difference++;
				}
				$months_difference--;
				$datediff = $months_difference;
				break;

			case 'y': // Difference between day numbers

				$datediff = date("z", $dateto) - date("z", $datefrom);
				break;

			case "d": // Number of full days

				$datediff = floor($difference / 86400);
				break;

			case "w": // Number of full weekdays

				$days_difference = floor($difference / 86400);
				$weeks_difference = floor($days_difference / 7); // Complete weeks
				$first_day = date("w", $datefrom);
				$days_remainder = floor($days_difference % 7);
				$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
				if ($odd_days > 7) { // Sunday
					$days_remainder--;
				}
				if ($odd_days > 6) { // Saturday
					$days_remainder--;
				}
				$datediff = ($weeks_difference * 5) + $days_remainder;
				break;

			case "ww": // Number of full weeks

				$datediff = floor($difference / 604800);
				break;

			case "h": // Number of full hours

				$datediff = floor($difference / 3600);
				break;

			case "n": // Number of full minutes

				$datediff = floor($difference / 60);
				break;

			default: // Number of full seconds (default)

				$datediff = $difference;
				break;
			}

			return $datediff;
		}

		// 말일
		function lastDay($year, $month){
			$day = date("t", mkTime(0, 0, 1, $month, 1, $year));
			$day = ($day < 10 ? "0" : "").$day;

			return $day;
		}

		// 주횟수
		function weekCount($year, $month){
			$calTime = mkTime(0, 0, 1, $month, 1, $year);
			$lastDay = date("t", $calTime); //총일수 구하기
			$startWeek = date("w", strtotime(date("Y-m", $calTime)."-01")); //시작요일 구하기
			$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
			$lastWeek  = date('w', strtotime(date("Y-m", $calTime)."-".$lastDay));
			$weekCount = 0;
			$day = 1;
			for($i=1; $i<=$totalWeek; $i++){
				for ($j=0; $j<7; $j++){
					if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
						if ($j == 0){
							$weekCount ++;
							break;
						}
					}
				}
			}
			return $weekCount;
		}

		// 일요일 리스트
		function sunday_list($year, $month, $weekday = 0){
			$calTime   = mkTime(0, 0, 1, $month, 1, $year);
			$lastDay   = date("t", $calTime); //총일수 구하기
			$startWeek = date("w", strtotime(date("Y-m", $calTime)."-01")); //시작요일 구하기
			$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
			$lastWeek  = date('w', strtotime(date("Y-m", $calTime)."-".$lastDay));
			$weekCount = 0;
			$day       = 1;
			$index     = 0;
			for($i=1; $i<=$totalWeek; $i++){
				for ($j=0; $j<7; $j++){
					if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
						if ($j == $weekday){
							$list[$index] = $year.'-'.$month.'-'.($day<10?'0':'').$day;
							$index ++;
						}
						$day ++;
					}
				}
			}
			return $list;
		}

		// 근무가능일수
		function workCount($year, $month, $days = 5){
			$calTime = mkTime(0, 0, 1, $month, 1, $year);
			$lastDay = date("t", $calTime); //총일수 구하기
			$startWeek = date("w", strtotime(date("Y-m", $calTime)."-01")); //시작요일 구하기
			$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
			$lastWeek  = date('w', strtotime(date("Y-m", $calTime)."-".$lastDay));
			$workCount = 0;
			$day = 1;
			for($i=1; $i<=$totalWeek; $i++){
				for ($j=0; $j<7; $j++){
					if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
						if ($days == 5){
							if ($j != 0 && $j != 6){
								$workCount ++;
							}
						}else{
							if ($j != 0){
								$workCount ++;
							}
						}
					}
				}
			}
			return $workCount;
		}


		// 근무가능일자
		function _workDate($year, $month, $days = 5){
			$calTime = mkTime(0, 0, 1, $month, 1, $year);
			$lastDay = date("t", $calTime); //총일수 구하기
			$startWeek = date("w", strtotime(date("Y-m", $calTime)."-01")); //시작요일 구하기
			$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
			$lastWeek  = date('w', strtotime(date("Y-m", $calTime)."-".$lastDay));
			$workDate  = '';
			$day = 1;
			for($i=1; $i<=$totalWeek; $i++){
				for ($j=0; $j<7; $j++){
					if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
						if ($days == 5){
							if ($j != 0 && $j != 6){
								$workDate .= '/'.$year.$month.($day<10?'0':'').$day;
							}
						}else{
							if ($j != 0){
								$workDate .= '/'.$year.$month.($day<10?'0':'').$day;
							}
						}
						$day ++;
					}
				}
			}
			return $workDate;
		}

		// 호스트
		function host(){
			$host = explode('.', $_SERVER['HTTP_HOST']);

			return $host[0];
		}

		// 배열정렬
		function sortArray($array, $column, $pos = 0){
			for($i=0; $i<sizeOf($array); $i++){
				$sortarray[] = $array[$i][$column];
			}
			$op = array(SORT_DESC, SORT_ASC); //배열 정렬
			@array_multisort($sortarray, $op[$pos], $array);

			return $array;
		}

		// 문자포맷
		function formatString($p_target, $p_format){
			if (str_replace(' ', '', $p_target) == ''){
				return '';
			}

			$len = 0;
			$l = 0;
			$sLen = 0;
			$s = '';
			$t = '';
			for($i=0; $i<strLen($p_format); $i++){
				if (subStr($p_format, $i, 1) == '#'){
					$len ++;
				}else{
					$t = subStr($p_format, $len+$l, 1);
					$s .= (subStr($p_target, $sLen, $len).$t);
					$sLen += $len;
					$len = 0;
					$l ++;
				}
			}
			$s .= subStr($p_target, $sLen, $len);

			$s = str_replace('#', '-', $s);

			return $s;
		}

		// 마이크로시간
		function getMtime(){
			list($usec, $sec) = explode(" ",microtime());
			return ((float)$usec + (float)$sec);
		}

		// 이미지 업로드
		function uploadFileName($f, $index){
			if ($f['tmp_name'] != ''){
				$fileInfo = explode('.', $f['name']);
				$expName  = $fileInfo[sizeOf($fileInfo) - 1];
				$fileName = $index.'_'.mkTime().'.'.$expName;

				// 첨부파일 업로드
				if (move_uploaded_file($f['tmp_name'], '../upFile/'.$fileName)){
					// 업로드 성공
					$expFile = $fileName;
				}else{
					// 업로드 실패
					$expFile = '';
				}
			}else{
				$expFile = '';
			}
			return $expFile;
		}

		// UTF-8 변경
		function utf($value){
			return iconv("EUC-KR","UTF-8",$value);
		}

		// EUCKR
		function euckr($value){
			return iconv("UTF-8","EUC-KR",$value);
		}

		function addSpace($_val){
			return $_val != '' ? $_val : ' ';
		}

		// 게시판명
		function board_name($board_type){
			switch($board_type){
			case '1':
				$title = '케어비지트';
				break;
			case '2':
				$title = '세무회계';
				break;
			case '3':
				$title = '노무자문';
				break;
			case '4':
				$title = '법률자문';
				break;
			case 'free':
				$title = '자유게시판';
				break;
			case 'noti':
				$title = '공지사항';
				break;
			case 'pds':
				$title = '자료실';
				break;
			}

			return $title;
		}

		// 년도
		function year(){
			$year[0] = 2010;
			$year[1] = date('Y')+(date('m') >= 10 ? 1 : 0);

			return $year;
		}

		// 메세지선택
		function message($p_gubun, $p_script = 'Y', $p_history = 'N', $p_close = 'N'){
			$gubun = strtolower($p_gubun);
			$value = '';

			if ($p_script == 'Y') $value .= '<script>alert("';
			if ($gubun == 'ok'){
				$value .= '정상적으로 처리되었습니다.';
			}else if ($gubun == 'nodata'){
				$value .= ':: 검색된 데이타가 없습니다. ::';
			}else if ($gubun == 'error'){
				$value .= '데이타 처리중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.';
			}else if ($gubun == 'err1'){
				$value .= '오류가 발생하였습니다. 관리자에게 문의하여 주십시오.';
			}else if (is_numeric($gubun)){
				$value .= '검색된 전체 갯수 : '.number_format($gubun);
			}else{
				$value .= $p_gubun;
			}

			if ($p_script  == 'Y') $value .= '");';
			if ($p_history == 'Y') $value .= 'history.go(-1);';
			if ($p_close   == 'Y') $value .= 'window.self.close();';
			if ($p_script  == 'Y') $value .= '</script>';

			return $value;
		}

		// 요일
		function weekday($p_date){
			if (strlen($p_date) == 8){
				$p_date = $this->dateStyle($p_date);
			}
			$week_array = array('일', '월', '화', '수', '목', '금', '토');
			return $week_array[date('w', strtotime($p_date))];
		}

		// 주간일자
		function weekdate($dt, $gbn = '-'){
			$tmp_dt = explode('-', $dt);
			$tmp_format   = 'Y'.$gbn.'m'.$gbn.'d';
			$tmp_time     = mktime(0, 0, 0, $tmp_dt[1], $tmp_dt[2], $tmp_dt[0]);
			$tmp_end_week = date('w', $tmp_time);
			$tmp_end_dt   = strtotime(($tmp_end_week == 0 ? 'this Sunday' : 'next Sunday'), $tmp_time);
			$tmp_start_dt = date($tmp_format, strtotime('-6 day', $tmp_end_dt));
			$tmp_end_dt   = date($tmp_format, $tmp_end_dt);

			return array('from' => $tmp_start_dt, 'to' => $tmp_end_dt);
		}

		// 주간인덱스
		function weekindex($dt){
			$tmp_dt    = explode('-', $dt);
			$tmp_now   = mktime(0, 0, 0, $tmp_dt[1], $tmp_dt[2], $tmp_dt[0]);
			$tmp_mon   = mktime(0, 0, 0, $tmp_dt[1], 1, $tmp_dt[0]);
			$tmp_now_w = date('W', $tmp_now);
			$tmp_mon_w = date('W', $tmp_mon);
			//$tmp_idx   = $tmp_now_w - $tmp_mon_w + 1;

			$tmp_idx = $tmp_now_w - $tmp_mon_w;

			$lsWeekly = date('w', strtotime(substr($dt,0,8).'01'));

			if ($lsWeekly != 0)
				$tmp_idx ++;


			if (date('w', $tmp_now) == 0)
				$tmp_idx ++;

			return $tmp_idx;
		}

		//
		function zero_str($value, $count = 0){
			$zero = '';

			for($i=0; $i<($count - strlen($value)); $i++){
				$zero .= '0';
			}

			$value = $zero.$value;

			return $value;
		}


		function zero_code($code, $length = 2){
			$zero = "";

			for($i=0; $i<$length; $i++){
				$zero .= "0";
			}

			$cd = $zero.$code;
			$cd = SubStr($cd, StrLen($cd) - $length, StrLen($cd));

			return $cd;
		}

		// 헤더
		function header_script(){
			return '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		}

		// 링크
		function make_link($str, $link, $return = false){
			return '<a href=\'#\' onclick=\''.$link.'; return '.$return.';\'>'.$str.'</a>';
		}

		// 바우처 수가코드
		function voucher_suga($svc_id, $svc_gbn = '', $svc_cd = '', $svc_val = ''){
			if ($svc_id > 20 && $svc_id < 30) $rst = 'V';

			switch($svc_id){
				case 21:
					$rst .= 'H';
					break;
				case 22:
					$rst .= 'O';
					break;
				case 23:
					$rst .= 'M';
					break;
				case 24:
					$rst .= 'A';
					break;
			}

			if ($svc_gbn != '') $rst .= $svc_gbn;
			if ($svc_cd  != '') $rst .= $this->zero_str($svc_cd, 2 - strlen($svc_val));
			if ($svc_val != '') $rst .= $svc_val;

			return $rst;
		}

		// 시간산정
		function com_time($time, $pos = 0){
			return round($time / 60, $pos);
		}

		//
		function time2min($time){
			$time = str_replace(':', '', $time);

			$time_from = intval(substr($time,0,2)) * 60 + intval(substr($time,2,2));
			$time_to   = intval(substr($time,0,2)) * 60 + intval(substr($time,2,2));

			return intval($time_from) /*+ intval($time_to)*/;
		}

		//
		function min2time($val){
			$hour = floor($val / 60);
			$min  = $val % 60;

			$hour = ($hour < 10 ? '0' : '').intval($hour);
			$min  = ($min  < 10 ? '0' : '').intval($min);

			return $hour.':'.$min;
		}

		// 일정등록 모드
		function get_iljung_mode(){
			if (strpos($_SERVER['HTTP_REFERER'], 'iljung_reg.php') > 0){
				$mode = 1; //등록
			}else if (strpos($_SERVER['HTTP_REFERER'], 'iljung_add.php') > 0){
				$mode = 2; //수정
			}else if (strpos($_SERVER['HTTP_REFERER'], 'iljung_add_conf.php') > 0){
				$mode = 901;
			}else if (strpos($_SERVER['HTTP_REFERER'], 'iljung_delete_ok.php') > 0){
				$mode = 101;
			}else if (strpos($_SERVER['HTTP_REFERER'], 'iljung_voucher_make.php') > 0){
				$mode = 51;
			}else if (strpos($_SERVER['HTTP_REFERER'], 'iljung_voucher_overtime.php') > 0){
				$mode = 52;
			}else{
				$mode = 0;
			}

			return $mode;
		}

		// 경로
		function _path(){
			$path = explode('/', $_SERVER['HTTP_REFERER']);
			$path = explode('.', $path[sizeof($path)-1]);

			return $path[0];
		}

		function _self(){
			$self = explode('/', $_SERVER['PHP_SELF']);
			$self = explode('.', $self[sizeof($self)-1]);

			return $self[0];
		}



		/*********************************************************

			년도

		*********************************************************/
		function _btn_year($year, $name = 'year', $str = '년', $type = 'select', $fun_nm = '__moveYear'){
			if ($type == 'select'){
				$init = $this->year();

				$html = '<select id=\''.$name.'\' name=\''.$name.'\' style=\'width:auto;\'>';

				for($i=$init[0]; $i<=$init[1]; $i++){
					$html .= '<option value=\''.$i.'\' '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
				}

				$html .= '</select>'.$str;
			}else{
				$html .= '<div class=\'left\' style=\'padding-top:2px;\'>
							<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_pre_out.gif\' style=\'cursor:pointer;\' onclick=\''.$fun_nm.'(-1);\' onmouseover=\'this.src="../image/btn/btn_pre_over.gif";\' onmouseout=\'this.src="../image/btn/btn_pre_out.gif";\'></div>
							<div style=\'float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;\' id=\''.$name.'\'>'.$year.'</div>
							<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_next_out.gif\' style=\'cursor:pointer;\' onclick=\''.$fun_nm.'(1);\' onmouseover=\'this.src="../image/btn/btn_next_over.gif";\' onmouseout=\'this.src="../image/btn/btn_next_out.gif";\'></div>
						  </div>';
			}

			return $html;
		}

		function yymm($year = '', $month = '', $fun = 'lfMoveYear'){
			if (!$year) $year = Date('Y');
			if (!$month) $month = Date('m');

			$month = IntVal($month);

			$html = '
				<div class=\'left\' style=\'padding-top:2px;\'>
					<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_pre_out.gif\' style=\'cursor:pointer;\' onclick=\''.$fun.'(-1);\' onmouseover=\'this.src="../image/btn/btn_pre_over.gif";\' onmouseout=\'this.src="../image/btn/btn_pre_out.gif";\'></div>
					<div style=\'float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;\' id=\'yymm\' year=\''.$year.'\' month=\''.$month.'\'>'.$year.'</div>
					<div style=\'float:left; width:auto; padding-top:2px;\'><img src=\'../image/btn/btn_next_out.gif\' style=\'cursor:pointer;\' onclick=\''.$fun.'(1);\' onmouseover=\'this.src="../image/btn/btn_next_over.gif";\' onmouseout=\'this.src="../image/btn/btn_next_out.gif";\'></div>
				</div>';

			return $html;
		}


		/*********************************************************

			일자

		*********************************************************/
		function _btn_day($year, $month, $day = 'A', $fun = 'alert(\'test\');'){
			$lastday = $this->lastDay($year, $month);

			$html = '<div style=\'clear:both;\'>
					 <div id=\'btnDay_A\' class=\'my_box '.($day == 'A' ? 'my_box_2' : 'my_box_1').'\' style=\'float:left; margin-left:2px;\' style=\'cursor:pointer;\' onclick=\''.str_replace('"D"', '"A"', $fun).'\'>A</div>';

			for($i=1; $i<=$lastday; $i++){
				$weekday = date('w', strtotime($year.'-'.$month.'-'.($i < 10 ? '0' : '').$i));

				switch($weekday){
					case 6:
						$color = 'color:#0000ff;';
						break;
					case 0:
						$color = 'color:#ff0000;';
						break;
					default:
						$color = '';
				}
				$f     = str_replace('"D"', $i, $fun);
				$html .= '<div id=\'btnDay_'.$i.'\' class=\'my_box '.($i == $day ? 'my_box_2' : 'my_box_1').'\' style=\'float:left; margin-left:2px;\' style=\'cursor:pointer;'.$color.'\' onclick=\''.$f.'\' tag=\''.($i == $day ? 'Y' : '').'\'>'.$i.'</div>';
				$html .= '<input id=\'objDay_'.$i.'\' name=\'objDay_'.$i.'\' type=\'hidden\' value=\''.($i == $day || $day == 'A' ? 'Y' : 'N').'\' tag=\'\'>';
			}

			$html .= '</div>';

			return $html;
		}



		/**************************************************

			월별

		**************************************************/
		function _btn_month($month, $fun1 = 'alert(', $fun2 = ');', $mon_cnt = null, $all_yn = false){
			/*
			$html = '';

			for($i=1; $i<=12; $i++){
				$class = 'my_month ';

				if ($i == intval($month)){
					$class .= 'my_month_y ';
					$color  = 'color:#000000;';
				}else{
					$class .= 'my_month_1 ';
					$color  = 'color:#666666;';
				}

				if (is_array($mon_cnt)){
					if (intval($mon_cnt[$i]) < 1) $color = 'color:#cccccc';
				}

				$text = '<a href=\'#\' onclick=\''.$fun1.$i.$fun2.'\'><span style=\''.$color.'\'>'.$i.'월</span></a>';

				if ($i == 12){
					$style = 'float:left;';
				}else{
					$style = 'float:left; margin-right:3px;';
				}
				$html .= '<div id=\'obj_month_'.$i.'\' class=\''.$class.'\' style=\''.$style.'\'>'.$text.'</div>';
			}
			*/

			$html = '';

			if ($all_yn){

				if ($month == 'A'){
					$class = 'my_month my_month_y';
					$color  = 'color:#000000;';
				}else{
					$class = 'my_month my_month_1';
					$color  = 'color:#666666;';
				}

				$text  = '<a href=\'#\' onclick=\''.$fun1.'"A"'.$fun2.'\'><span style=\''.$color.'\'>전체</span></a>';
				$html .= '<div id=\'btnMonth_A\' class=\''.$class.'\' style=\'float:left; margin-right:3px;\'>'.$text.'</div>';
			}

			for($i=1; $i<=12; $i++){
				$class = 'my_month ';

				if ($i == intval($month)){
					$class .= 'my_month_y ';
					$color  = 'color:#000000;';
				}else{
					$class .= 'my_month_1 ';
					$color  = 'color:#000000;';
				}

				if (is_array($mon_cnt)){
					if (intval($mon_cnt[$i]) < 1) $color = 'color:#cccccc';
				}

				$text = '<a href=\'#\' onclick=\''.$fun1.$i.$fun2.'\'><span style=\''.$color.'\'>'.$i.'월</span></a>';

				if ($i == 12){
					$style = 'float:left;';
				}else{
					$style = 'float:left; margin-right:3px;';
				}
				$html .= '<div id=\'btnMonth_'.$i.'\' class=\''.$class.'\' style=\''.$style.'\'>'.$text.'</div>';
			}

			return $html;
		}


		/*********************************************************

			만나이를 구한다.

		*********************************************************/
		function man_age($jumin, $month = '', $year = 0){
			if (empty($month)) $month = date('m');

			$m_age = intval($this->issToAge($jumin, $year));
			if ($m_age > 60){
				if (intval(substr($jumin,2,2)) >= intval($month)){
					$m_age ++;
				}
			}
			return $m_age;
		}

		function ManAge($birthDt, $ymd = ''){
			$y = IntVal(SubStr($birthDt, 0, 4));
			$m = IntVal(SubStr($birthDt, 4, 2));
			$d = IntVal(SubStr($birthDt, 6, 2));

			//기준일(시스템)
			if ($ymd){
				$year = IntVal(SubStr($ymd, 0, 4));
				$month = IntVal(SubStr($ymd, 4, 2));
				$day = IntVal(SubStr($ymd, 6, 2));
			}else{
				$year = IntVal(date("Y"));
				$month = IntVal(date("m"));
				$day = IntVal(date("d"));
			}

			if ($m > $month){
				$age = $year - $y - 1;
			}else if ($m == $month){
				if ($d >= $day){
					$age = $year - $y;
				}else{
					$age = $year - $y - 1;
				}
			}else{
				$age = $year - $y;
			}

			return $age;
		}


		/*********************************************************

			요일색상

		*********************************************************/
		function _weekColor($weekday, $type = 'norma'){
			switch($weekday){
				case 0:
					if ($type == 'soft'){
						$color = '#efa9c7';
					}else{
						$color = '#ff0000';
					}
					break;

				case 6:
					if ($type == 'soft'){
						$color = '#a9b3ef';
					}else{
						$color = '#0000ff';
					}
					break;

				default:
					if ($type == 'soft'){
						$color = '#c0b3c0';
					}else{
						$color = '#000000';
					}
			}

			return $color;
		}


		/*********************************************************

			호스트 이미지

		*********************************************************/
		function _get_host_image_path(){
			$tmpDomain = explode('.', $_SERVER['HTTP_HOST']);
			//if($_SERVER['HTTP_HOST'] == 'pr.carevisit.co.kr'){
			$strDomain = $tmpDomain[sizeof($tmpDomain) - 3];
			//}else {
			//	$strDomain = $tmpDomain[sizeof($tmpDomain) - 2];
			//}
			
			if (Is_Numeric($strDomain)){
				$strDomain = 'goodeos';
			}

			$imgPath   = '../admin_img/'.$strDomain;

			return $imgPath;
		}



		/*********************************************************

			호스트별 구분

		*********************************************************/
		function _get_host_svc(){
			$tmpDomain = explode('.', $_SERVER['HTTP_HOST']);
			$strDomain = $tmpDomain[sizeof($tmpDomain)-2].'.'.$tmpDomain[sizeof($tmpDomain)-1];

			$host['homecare'] = false;
			$host['voucher']  = false;
			$host['center']   = false;

			switch($strDomain){
				case 'dwcare.com':
					$host['homecare'] = true;
					$host['voucher']  = true;
					break;

				default:
					$host['homecare'] = true;

			}

			return $host;
		}


		/*********************************************************

			도메인

		*********************************************************/
		function _get_domain(){
			$tmpStr = $_SERVER['HTTP_HOST'];
			$tmpDomain = explode('.', $tmpStr);
			$strDomain = $tmpDomain[sizeof($tmpDomain)-2].'.'.$tmpDomain[sizeof($tmpDomain)-1];

			if ($tmpStr == 'g-care.co.kr' || $tmpStr == 'www.g-care.co.kr' ||
				$tmpStr == 'geecare.co.kr' || $tmpStr == 'www.geecare.co.kr' ||
				$tmpStr == 'geecare.kr' || $tmpStr == 'www.geecare.kr'){

				$strDomain = 'carevisit.net';
			}

			return $strDomain;
		}

		function _domain(){
			return $this->_get_domain();
		}

		function _domain_name(){
			$tmpDomain = explode('.', $_SERVER['HTTP_HOST']);
			//$strDomain = $tmpDomain[1];
			$strDomain = $tmpDomain[sizeof($tmpDomain)-2];

			return $strDomain;
		}

		function _domain_id(){
			$domain = $this->_domain();

			switch($domain){
				case 'carevisit.net':
					return 1;
					break;

				case 'dwcare.com':
					return 2;
					break;

				case 'klcf.kr':
					return 3;
					break;

				case 'kdolbom.net':
					return 4;
					break;

				case 'thegoodjob.net':
					return 5;
					break;

				case 'kacold.net':
					return 6;
					break;

				case 'dasomi-m.net':
					return 7;
					break;

				case 'vaerp.com':
					return 8;
					break;

				case 'dolvoin.net':
					return 9;
					break;

				case 'forweak.net':
					return 10;
					break;
			}
		}



		/*********************************************************

			분을 시간으로 표현

		*********************************************************/
		function _min2timeKor($time){
			$hour = floor($time / 60);
			$min  = $time % 60;
			$str  = '';

			if (!empty($hour)) $str .= $hour.'시간';
			if (!empty($min)) $str .= $min.'분';

			return $str;
		}


		/*********************************************************

			년월

		*********************************************************/
		function _styleYYMM($yymm, $gbn = '-'){
			$str  = substr($yymm, 0, 4).($gbn == 'KOR' ? '년 ' : $gbn);
			$str .= ($gbn == 'KOR' ? intval(substr($yymm, 4, 2)) : substr($yymm, 4, 2)).($gbn == 'KOR' ? '월' : '');

			return $str;
		}

		function yymmStyle($yymm, $gbn = '-'){
			$str  = substr($yymm, 0, 4).($gbn == 'KOR' ? '년 ' : $gbn);
			$str .= ($gbn == 'KOR' ? intval(substr($yymm, 4, 2)) : substr($yymm, 4, 2)).($gbn == 'KOR' ? '월' : '');

			return $str;
		}


		/*********************************************************

			콤보박스

		*********************************************************/
		function _makeSelectBox($ao_list, $as_name, $as_id = '', $ab_all = true, $as_cd = ''){
			if (empty($as_id)) $as_id = $as_name;

			$html = '<select id=\''.$as_id.'\' name=\''.$as_name.'\' style=\'width:auto;\'>';

			if ($ab_all)
				$html .= '<option value=\'\' selected>전체</option>';

			if (is_array($ao_list)){
				foreach($ao_list as $i => $list){
					$html .= '<option value=\''.$i.'\' '.($as_cd == $i ? 'selected' : '').'>'.$list.'</option>';
				}
			}

			$html .= '</select>';

			return $html;
		}


		/*********************************************************

			한글여부

		*********************************************************/
		function _isKor($str){
			for($i=0; $i<strlen($str); $i++){
				$char = ord($str[$i]);
				if ($char >= 0xa1 && $char <= 0xfe){
					return true;
				}
			}
			return false;
		}


		/*********************************************************

			기간 중 일자

		*********************************************************/
		function _getDt($fromDt, $toDt){
			$today = date('Y-m-d');
			$dt    = $today;

			$fromDt = $this->dateStyle($fromDt);
			$toDt = $this->dateStyle($toDt);

			if (empty($fromDt)) $fromDt = $today;
			if (empty($toDt)) $toDt = $today;

			if ($today > $fromDt && $today < $toDt){
			}else if ($today < $fromDt){
				$dt = $fromDt;
			}else if ($today > $toDt){
				$dt = $toDt;
			}

			return $dt;
		}


		/*********************************************************

			등급명칭

		*********************************************************/
		function _lvlNm($lvlCd, $svcCd = '0', $svcVal = ''){
			$lvlNm = '';

			switch($svcCd){
				case '0':
					switch($lvlCd){
						case '1': $lvlNm = '1등급'; break;
						case '2': $lvlNm = '2등급'; break;
						case '3': $lvlNm = '3등급'; break;
						case '4': $lvlNm = '4등급'; break;
						case '5': $lvlNm = '5등급'; break;
						case 'A': $lvlNm = '인지지원'; break;
						default: $lvlNm = '일반';
					}
					break;

				case '1':
					switch($lvlCd){
						case '1': $lvlNm = '기초생활수급자'; break;
						case '2': $lvlNm = '차상위계층'; break;
						default: $lvlNm = '일반';
					}
					break;

				case '2':
					switch($lvlCd){
						case '1': $lvlNm = '기초생활수급자'; break;
						case '2': $lvlNm = '차상위계층'; break;
						case '3': $lvlNm = '차상위초과'; break;
						default: $lvlNm = '일반';
					}
					break;

				case '3':
					switch($lvlCd){
						case '1': $lvlNm = '40%이하'; break;
						case '2': $lvlNm = '40%초과~50%이하'; break;
						case '3': $lvlNm = '차상위초과'; break;
						default: $lvlNm = '일반';
					}
					break;

				case '4':
					
					if($svcVal == '3'){
						switch($lvlCd){
							case '1': $lvlNm = '기초생활수급자'; break;
							case '2': $lvlNm = '차상위계층'; break;
							case '3': $lvlNm = '50%이하'; break;
							case '4': $lvlNm = '50%초과~100%이하'; break;
							case '5': $lvlNm = '100%초과~150%이하'; break;
							case '6': $lvlNm = '150%초과'; break;
							default: $lvlNm = '일반';
						}
					}else {
						switch($lvlCd){
							case '1': $lvlNm = '생계의료급여수급자'; break;
							case '2': $lvlNm = '차상위계층'; break;
							case '3': $lvlNm = '70%이하'; break;
							case '4': $lvlNm = '70%초과~120%이하'; break;
							case '5': $lvlNm = '120%초과~180%이하'; break;
							case '6': $lvlNm = '180%초과'; break;
							default: $lvlNm = '일반';
						}
					}
					break;
				case '40':
					switch($lvlCd){
						case '1': $lvlNm = '1구간'; break;
						case '2': $lvlNm = '2구간'; break;
						case '3': $lvlNm = '3구간'; break;
						case '4': $lvlNm = '4구간'; break;
						case '5': $lvlNm = '5구간'; break;
						case '6': $lvlNm = '6구간'; break;
						case '7': $lvlNm = '7구간'; break;
						case '8': $lvlNm = '8구간'; break;
						case '9': $lvlNm = '9구간'; break;
						case '10': $lvlNm = '10구간'; break;
						case '11': $lvlNm = '11구간'; break;
						case '12': $lvlNm = '12구간'; break;
						case '13': $lvlNm = '13구간'; break;
						case '14': $lvlNm = '14구간'; break;
						case '15': $lvlNm = '15구간'; break;
						default: $lvlNm = '특례';
					}
					break;
			}

			return $lvlNm;
		}


		/*********************************************************

			수급자구분 명칭

		*********************************************************/
		function _kindNm($kindCd){
			$kindNm = '';

			switch($kindCd){
				case '3': $kindNm = '기초수급권자'; break;
				case '2': $kindNm = '의료수급권자'; break;
				case '4': $kindNm = '경감대상자'; break;
				default: $kindNm = '일반';
			}

			return $kindNm;
		}

		function _kindSub($kindCd){
			$kindNm = '';

			switch($kindCd){
				case '3': $kindNm = '기초'; break;
				case '2': $kindNm = '의료'; break;
				case '4': $kindNm = '경감'; break;
				default: $kindNm = '일반';
			}

			return $kindNm;
		}


		/*********************************************************

			스크립트

		*********************************************************/
		function _gabSplitHtml($html){
			$html = str_replace(chr(13).chr(10), '', $html);
			$html = str_replace(chr(9), '', $html);
			$html = str_replace('  ', '', $html);

			return $html;
		}



		/*********************************************************

			숫자를 한글로 표기

		 *********************************************************/
		function no2Kor($aiNum){
			$lsVal = "";

			if (!Is_Numeric($aiNum)){
				return 9;
			}

			$laNum = StrRev($aiNum);

			for($i=StrLen($laNum)-1; $i>=0; $i--){
				//현재 자리를 구함
				$digit = SubStr($laNum, $i, 1);

				// 각 자리 명칭
				switch($digit){
					case '-' : $lsVal .= "(-) ";
						break;
					case '0' : $lsVal .= "";
						break;
					case '1' : $lsVal .= "일";
						break;
					case '2' : $lsVal .= "이";
						break;
					case '3' : $lsVal .= "삼";
						break;
					case '4' : $lsVal .= "사";
						break;
					case '5' : $lsVal .= "오";
						break;
					case '6' : $lsVal .= "육";
						break;
					case '7' : $lsVal .= "칠";
						break;
					case '8' : $lsVal .= "팔";
						break;
					case '9' : $lsVal .= "구";
						break;
				}
				if($digit=="-")continue;

				// 4자리 표기법 공통부분
				if ($digit != 0){
					if($i % 4 == 1)$lsVal .= "십";
					else if($i % 4 == 2)$lsVal .= "백";
					else if($i % 4 == 3)$lsVal .= "천";
				}

				// 4자리 한자 표기법 단위
				if ($i % 4 == 0){
					if (floor($i/ 4) ==0)$lsVal .= "";
					else if (floor($i / 4)==1)$lsVal .= "만";
					else if (floor($i / 4)==2)$lsVal .= "억";
					else if (floor($i / 4)==3)$lsVal .= "조";
					else if (floor($i / 4)==4)$lsVal .= "경";
				}
			}
			return $lsVal;
		}


		/*********************************************************

			가족구분

		 *********************************************************/
		function familyKind($kind){
			switch($kind){
				case 'S031': return '처'; break;
				case 'S032': return '남편'; break;
				case 'S033': return '자'; break;
				case 'S034': return '자부'; break;
				case 'S035': return '사위'; break;
				case 'S036': return '형제자매'; break;
				case 'S037': return '손'; break;
				case 'S038': return '배우자의형제자매'; break;
				case 'S039': return '외손'; break;
				case 'S040': return '부모'; break;
				case 'S041': return '기타'; break;
			}
		}


		//초성
		function toCho($cho){
			$toCho	= '';

			if ($cho == '가'){$toCho	= '나';
			}else if ($cho == '나'){$toCho	= '다';
			}else if ($cho == '다'){$toCho	= '라';
			}else if ($cho == '라'){$toCho	= '마';
			}else if ($cho == '마'){$toCho	= '바';
			}else if ($cho == '바'){$toCho	= '사';
			}else if ($cho == '사'){$toCho	= '아';
			}else if ($cho == '아'){$toCho	= '자';
			}else if ($cho == '자'){$toCho	= '차';
			}else if ($cho == '차'){$toCho	= '카';
			}else if ($cho == '카'){$toCho	= '타';
			}else if ($cho == '타'){$toCho	= '파';
			}else if ($cho == '파'){$toCho	= '하';
			}else if ($cho == '하'){$toCho	= '';
			}

			return $toCho;
		}


		//월
		function monthStr($month){
			$month = IntVal($month);
			$month = ($month < 10 ? '0' : '').$month;

			return $month;
		}

		//적용기간 확인
		function chkPeriod($newFrom,$newTo,$orgFrom,$orgTo){
			if ($newFrom < $orgFrom){
				return 11;
			}else if ($newFrom >= $orgFrom && $newFrom <= $orgTo){
				return 12;
			}else if ($newTo < $orgTo){
				return 21;
			}else if ($newTo >= $orgFrom && $newTo <= $orgTo){
				return 22;
			}else{
				return 1;
			}
		}


		//노무/회계
		function getAcctTable(){
			$host = Trim($this->host());

			if ($host == 'hy-acc' || $host == 'hanlim'){
				$tbl = 'tax_acct';
			}else if ($host == 'acc'){
				$tbl = 'fa_acct';
			}else{
				$tbl = 'labor_acct';
			}

			return $tbl;
		}


		//고객 고유번호
		function fixNo($no){
			$no = '0000000000'.$no;
			$no = SubStr($no,StrLen($no)-10,StrLen($no));

			return $no;
		}


		//파일크기
		function getFileSize($size){
			$size = $size / 1024;

			if ($size < 1000){
				return Round($size,2).' KB';
			}

			$size = $size / 1024;

			if ($size < 1000){
				return Round($size,2).' MB';
			}

			return $size;
		}


		//육십갑자, 띠
		function getGapJaDdi($gbn,$year){
			$arrGap = Array(4=>"갑",5=>"을",6=>"병",7=>"정",8=>"무",9=>"기",0=>"경",1=>"신",2=>"임",3=>"계");
			$arrJa  = Array(4=>"자",5=>"축",6=>"인",7=>"묘",8=>"진",9=>"사",10=>"오",11=>"미",0=>"신",1=>"유",2=>"술",3=>"해");
			$arrDdi = Array(4=>"쥐",5=>"소",6=>"범",7=>"토끼",8=>"용",9=>"뱀",10=>"말",11=>"양",0=>"원숭이",1=>"닭",2=>"개",3=>"돼지");

			//육십갑자, 띠 계산
			//십간에서는 연도 마지막 숫자
			$num = substr($year,-1);

			//십이지지와 띠는 연도와 12의 나눠서 나오는 나머지값
			$num2 = $year % 12;

			$GapJa = $arrGap[$num].$arrJa[$DDI];
			$Ddi   = $arrDdi[$num2];

			switch($gbn){
				case "ALL":
					$str = $GapJa."년 ".$Ddi."의 해";
					return;

				case "GAPJA":
					$str = $GapJa;
					return;

				default:
					$str = $Ddi;
			}

			return $str;
		}

		//UTF8 코드
		function utf8_ord($ch) {
			$len = strlen($ch);

			if($len <= 0) return false;

			$h = ord($ch{0});

			if ($h <= 0x7F) return $h;
			if ($h < 0xC2) return false;
			if ($h <= 0xDF && $len>1) return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
			if ($h <= 0xEF && $len>2) return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);
			if ($h <= 0xF4 && $len>3) return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);

			return false;
		}

		//초성구하기
		function GetCho($str) {
			$cho = array("ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");

			$result = "";

			for ($i=0; $i<$this->len($str); $i++) {
				$code = $this->utf8_ord($this->mid($str, $i, 1)) - 44032;

				if ($code > -1 && $code < 11172) {
					$cho_idx = $code / 588;
					$result .= $cho[$cho_idx];
				}
			}

			return $result;
		}

		function GetChoCode($str) {
			$result = "";

			for ($i=0; $i<$this->len($str); $i++) {
				$code = $this->utf8_ord($this->mid($str, $i, 1)) - 44032;

				$result .= ($result ? '/' : '').$code;
			}

			return $result;
		}


		//리포트 타이틀
		function StatsTitle($SR, $type, $kind){
			if ($SR == 'S'){
				$title = '재가지원 > ';
			}else if ($SR == 'R'){
				$title = '자원연계 > ';
			}else{
				return '';
			}

			if ($kind[0] == 'CENTER'){
			}else if ($kind[0] == 'CLIENT'){
				if ($kind[1] == 'STATE'){
					$title .= '대상자현황(등록기준)';
					return $title;
				}else{
					$title .= '대상자현황 > ';
				}
			}else if ($kind[0] == 'SERVICE'){
				$title .= '서비스현황 > ';
			}else{
				return '';
			}

			if ($type == 'M'){
				$title .= '월별 > ';
			}else if ($type == 'Q'){
				$title .= '분기별 > ';
			}

			if ($kind[0] == 'CENTER'){
				if ($kind[1] == 'STATE' || $kind[1] == 'LIST') $title .= '기관현황';
				else if ($kind[1] == 'USE') $title .= '기관별 사용현황';
			}else if ($kind[0] == 'CLIENT'){
				if ($kind[1] == 'STATUS') $title .= '지역별현황';
				else if ($kind[1] == 'GENDER') $title .= '성별현황';
				else if ($kind[1] == 'AGE') $title .= '연령별현황';
				else if ($kind[1] == 'SERVICE') $title .= '서비스별 지역현황';
			}else if ($kind[0] == 'SERVICE'){
				if ($kind[1] == 'CLIENT') $title .= '대상자현황';
				else if ($kind[1] == 'SVC') $title .= '서비스현황';
				else if ($kind[1] == 'CUST') $title .= '자원현황';
			}else{
				return '';
			}

			return $title;
		}


		//기관연결구분
		function orgConnectGbn($gbn){
			if ($gbn == '01'){
				$gbn = '신규연결';
			}else if ($gbn == '02'){
				$gbn = '재연결';
			}else if ($gbn == '11'){
				$gbn = '무료기간 종료';
			}else if ($gbn == '12'){
				$gbn = '계약 해지';
			}else if ($gbn == '31'){
				$gbn = '사용료 미납';
			}else if ($gbn == '32'){
				$gbn = '기관 이전';
			}else if ($gbn == '90'){
				$gbn = '기관폐쇄';
			}else if ($gbn == '99'){
				$gbn = '기타';
			}

			return $gbn;
		}


		function parseGet($str){
			parse_str(str_replace('/|/','=',str_replace('/*/','&',$str)),$val);
			return $val;
		}


		function ClientRate($clientGbn){
			switch($clientGbn){
				case '3':
					$rate = 0;
					break;

				case '2':
				case '4':
					$rate = 7.5;
					break;

				case '41':
					$rate = 6;
					break;

				case '42':
					$rate = 9;
					break;

				case '1':
					$rate = 15;
					break;

				default:
					$rate = 100;
			}

			return $rate;
		}


		//내구연한 및 연장기간 비율
		function DurExtRate($durext, $product_code, $barcode, $date){
			if (is_array($durext[$product_code][$barcode])){
				$rate = 0;

				foreach($durext[$product_code][$barcode] as $tmpI => $R){
					if ($R['from_dt'] <= $date && $R['to_dt'] >= $date){
						$rate = $R['rate'];
						break;
					}
				}
			}else{
				$rate = 0;
			}

			return $rate;
		}


		//복지용구 본인부담금
		function CalExpense($fromDt, $toDt, $rentalPay, $clientGbn, $cutoff = false, $rstGbn = 'EXPENSE'){
			$rate = $this->ClientRate($clientGbn);

			$fromYm = SubStr($fromDt, 0, 6);
			$toYm = SubStr($toDt, 0, 6);
			$loopYm = $fromYm;
			$expense = 0;
			$rentalAmt = 0;

			$i = 0;

			while(true){
				if ($loopYm == $fromYm || $loopYm == $toYm){
					$lastday = $this->lastday(SubStr($loopYm, 0, 4), SubStr($loopYm, 4, 2));

					if ($loopYm >= '201604'){
						$dayCnt = $lastday;
					}else{
						$dayCnt = 30;
					}

					if ($loopYm == $fromYm){
						if ($fromYm == $toYm){
							$days = $this->dateDiff('d', $fromDt, $toDt) + 1;
						}else{
							$days = $lastday - IntVal(Date('d', StrToTime($fromDt))) + 1;
						}
					}else{
						$days = IntVal(Date('d', StrToTime($toDt)));
					}

					if ($days > $dayCnt) $days = $dayCnt;

					$pay = Floor($rentalPay / $dayCnt * $days);
				}else{
					$pay = $rentalPay;
				}

				$rentalAmt += $pay;

				if ($cutoff){
					$pay = Floor($pay * $rate / 1000) * 10;
				}else{
					$pay = Floor($pay * $rate / 100);
				}

				$expense += $pay;

				if ($loopYm >= $toYm) break;

				$loopYm = $this->dateAdd('month', 1, $loopYm.'01', 'Ym');

				$i ++;

				if ($i > 20) break;
			}

			if ($rstGbn == 'EXPENSE'){
				return $expense;
			}else if ($rstGbn == 'RENTAL'){
				return $rentalAmt;
			}else{
				return;
			}
		}


		function CalRentalPay($date, $rentalPay, $fromDt, $toDt){
			$lastday = $this->lastday(SubStr($date, 0, 4), SubStr($date, 4, 2));

			if (SubStr($date, 0, 6) >= '201604'){
				$dayCnt = $lastday;
			}else{
				$dayCnt = 30;
			}

			if (SubStr($date, 0, 6) == SubStr($fromDt, 0, 6) || SubStr($date, 0, 6) == SubStr($toDt, 0, 6)){
				if (SubStr($date, 0, 6) == SubStr($fromDt, 0, 6)){
					if (SubStr($fromDt, 0, 6) == SubStr($toDt, 0, 6)){
						$days = $this->dateDiff('d', $fromDt, $toDt) + 1;
					}else{
						$days = $lastday - IntVal(Date('d', StrToTime($date))) + 1;
					}
				}else{
					$days = IntVal(Date('d', StrToTime($date)));
				}

				if ($days > $dayCnt) $days = $dayCnt;

				$pay = Floor($rentalPay / $dayCnt * $days);
			}else{
				$pay = $rentalPay;
			}

			return $pay;
		}


		function CalWftPayInfo($objVal, $yymm = ''){
			if (!is_array($objVal)) return;

			foreach($objVal as $tmpIdx => $R){
				$rate = $this->ClientRate($R['clientGbn']);

				if ($R['rentalFlag'] == 'Y'){
					$fromYm = SubStr($R['fromDt'], 0, 6);
					$toYm = SubStr($R['toDt'], 0, 6);
					$loopYm = $fromYm;

					$R['rentalPay'] = $R['rentalPay'] * $R['calRate'];
					$R['rentalPay'] = Round($R['rentalPay'] / 100) * 100;

					while(true){
						if (is_array($R['gbnHisDt'][$loopYm])){
							$loopCnt = count($R['gbnHisDt'][$loopYm]);
							$changDt = true;
						}else{
							$loopCnt = 1;
							$changDt = false;
						}

						for($i=0; $i<$loopCnt; $i++){
							$loopFromDt = $R['fromDt'];
							$loopToDt = $R['toDt'];
							$lastday = $this->lastday(SubStr($loopYm, 0, 4), SubStr($loopYm, 4, 2));

							//echo '1 : '.$loopYm.' / '.$loopFromDt.' /'.$loopToDt.' / '.IntVal(SubStr($loopToDt, 6, 2)).' / '.IntVal(SubStr($loopFromDt, 6, 2)).' / '.$days.' / '.$stopDays.chr(13).chr(10);
							//echo '1 : '.$loopFromDt.' / '.$loopToDt.chr(13);

							if ($loopFromDt < $loopYm.'01') $loopFromDt = $loopYm.'01';
							if ($loopToDt > $loopYm.$lastday) $loopToDt = $loopYm.$lastday;
							//echo '2 : '.$loopFromDt.' / '.$loopToDt.chr(13);

							if ($changDt){
								if ($loopFromDt < $R['gbnHisDt'][$loopYm][$i]['fromDt']) $loopFromDt = $R['gbnHisDt'][$loopYm][$i]['fromDt'];
								if ($loopToDt > $R['gbnHisDt'][$loopYm][$i]['toDt']) $loopToDt = $R['gbnHisDt'][$loopYm][$i]['toDt'];
								if ($loopFromDt > $loopToDt) continue;

								//echo '3 : '.$loopFromDt.' / '.$loopToDt.chr(13);
								//print_r($R['gbnHisDt'][$loopYm]);

								$loopRate = $R['gbnHisDt'][$loopYm][$i]['rate'];
							}else{
								$loopRate = $rate;
							}

							if ($loopYm < '201604'){
								if ($loopToDt == $loopYm.$lastday){
									$loopToDt = $loopYm.'30';
									//echo '4 : '.$loopFromDt.' / '.$loopToDt.chr(13);
								}
							}

							$stopDays = 0;

							if (is_array($R['stopHisDt'][$loopYm])){
								foreach($R['stopHisDt'][$loopYm] as $tmpIdx => $stopDt){
									if ($stopDt['fromDt'] < $loopFromDt) $stopDt['fromDt'] = $loopFromDt;
									if ($stopDt['toDt'] > $loopToDt) $stopDt['toDt'] = $loopToDt;
									$stopDays += $this->dateDiff('d', $stopDt['fromDt'], $stopDt['toDt']) + 1;
								}
							}

							if ($loopYm == $fromYm || $loopYm == $toYm){
								if ($loopYm >= '201604'){
									$dayCnt = $lastday;
								}else{
									$dayCnt = 30;
								}

								if ($loopYm == $fromYm){
									if ($fromYm == $toYm){
										//$days = $this->dateDiff('d', $loopFromDt, $loopToDt) + 1;
										$days = IntVal(SubStr($loopToDt, 6, 2)) - IntVal(SubStr($loopFromDt, 6, 2)) + 1;
									}else{
										$days = $lastday - IntVal(Date('d', StrToTime($loopFromDt))) + 1;
										//$days = $dayCnt - IntVal(Date('d', StrToTime($loopFromDt))) + 1;
									}
								}else{
									//$days = IntVal(Date('d', StrToTime($loopToDt)));

									if (SubStr($loopFromDt, 6, 2) == '01'){
										$days = IntVal(SubStr($loopToDt, 6, 2));
									}else{
										$days = IntVal(SubStr($loopToDt, 6, 2)) - IntVal(SubStr($loopFromDt, 6, 2)) + 1;
									}
								}

								$days -= $stopDays;

								if ($days > $dayCnt) $days = $dayCnt;

								//echo '3 : '.$loopYm.' / '.$loopFromDt.' /'.$loopToDt.' / '.IntVal(SubStr($loopToDt, 6, 2)).' / '.IntVal(SubStr($loopFromDt, 6, 2)).' / '.$days.' / '.$stopDays.chr(13).chr(10);
								//echo $R['rentalPay'].'/'.$dayCnt.'/'.$days.chr(13);

								$pay = Floor($R['rentalPay'] / $dayCnt * $days);

								//echo $loopFromDt.' /'.$loopToDt.'/'.$pay.chr(13);
							}else{
								if ($stopDays > 0){
									if ($loopYm >= '201604'){
										$dayCnt = $lastday;
									}else{
										$dayCnt = 30;
									}

									//$days = $lastday - $stopDays;
									if ($lastday > $dayCnt){
										$days = $dayCnt - $stopDays;
									}else{
										$days = $lastday - $stopDays;
									}

									$pay = Floor($R['rentalPay'] / $dayCnt * $days);
								}else{
									if ($changDt){
										if ($loopYm >= '201604'){
											$dayCnt = $lastday;
										}else{
											$dayCnt = 30;
										}

										//$days = $this->dateDiff('d', $loopFromDt, $loopToDt) + 1;
										$days = IntVal(SubStr($loopToDt, 6, 2)) - IntVal(SubStr($loopFromDt, 6, 2)) + 1;
										$pay = Floor($R['rentalPay'] / $dayCnt * $days);

										//echo $loopYm.'/'.$days.'/'.$pay.'/'.$R['rentalPay'].'<br>';
									}else{
										$pay = $R['rentalPay'];
									}
								}
							}

							//if ($loopYm == '201707') echo $pay.'/'.($pay * $loopRate / 100).'<br>';

							if ($loopYm >= '201707'){
								$pay = Round($pay / 10) * 10;
							}else{
								$pay = Floor($pay / 10) * 10;
							}

							$expense = $pay * $loopRate / 100;
							$tmpData = Array('pay'=>$pay, 'expense'=>$expense, 'rate'=>$loopRate, 'changeGbn'=>$changDt);

							if ($yymm == $loopYm){
								if ($R['rootKey']){
									$data['RENTAL'][$R['productCode']][$R['rootKey']] = $tmpData;
								}else{
									$data['RENTAL'][$R['productCode']][] = $tmpData;
								}
							}else if (!$yymm){
								if ($R['rootKey']){
									$data[$loopYm]['RENTAL'][$R['productCode']][$R['rootKey']] = $tmpData;
								}else{
									$data[$loopYm]['RENTAL'][$R['productCode']][] = $tmpData;
								}
							}
						}

						if ($loopYm >= $toYm) break;

						$loopYm = $this->dateAdd('month', 1, $loopYm.'01', 'Ym');
					}
				}else{
					$loopYm = SubStr($R['buyDt'], 0, 6);
					$loopRate = $rate;

					if (is_array($R['gbnHisDt'][$loopYm])){
						$changDt = true;
					}else{
						$changDt = false;
					}

					if ($changDt){
						for($i=0; $i<count($R['gbnHisDt'][$loopYm]); $i++){
							if ($R['buyDt'] >= $R['gbnHisDt'][$loopYm][$i]['fromDt'] && $R['buyDt'] <= $R['gbnHisDt'][$loopYm][$i]['toDt']){
								$loopRate = $R['gbnHisDt'][$loopYm][$i]['rate'];
								break;
							}
						}
					}

					$expense = $R['buyPay'] * $loopRate / 100;
					$tmpData = Array('pay'=>$R['buyPay'], 'expense'=>$expense, 'rate'=>$loopRate, 'date'=>$R['buyDt']);

					if ($yymm == $loopYm){
						if ($R['rootKey']){
							$data['BUY'][$R['productCode']][$R['rootKey']] = $tmpData;
						}else{
							$data['BUY'][$R['productCode']][] = $tmpData;
						}
					}else if (!$yymm){
						if ($R['rootKey']){
							$data[$loopYm]['BUY'][$R['productCode']][$R['rootKey']] = $tmpData;
						}else{
							$data[$loopYm]['BUY'][$R['productCode']][] = $tmpData;
						}
					}
				}
			}

			return $data;
		}


		//이미지 축소
		function ImgResize($width, $height, $maxwidth, $maxheight){
			if ($width > $maxwidth || $height > $maxheight) {
				// 가로길이가 가로limit값보다 크거나 세로길이가 세로limit보다 클경우
				$sumw = (100*$maxheight)/$height;
				$sumh = (100*$maxwidth)/$width;

				if($sumw < $sumh) {
					// 가로가 세로보다 클경우
					$img_width = ceil(($width*$sumw)/100);
					$img_height = $maxheight;
				}else{
					// 세로가 가로보다 클경우
					$img_height = ceil(($height*$sumh)/100);
					$img_width = $maxwidth;
				}
			}else{
				// limit보다 크지 않는 경우는 원본 사이즈 그대로.....
				$img_width = $width;
				$img_height = $height;
			}

			$imgsize[0] = $img_width;
			$imgsize[1] = $img_height;

			return $imgsize;
		}

		//요일 인덱스
		function dowidx($date){
			return Date('w', StrToTime($date));
		}

		function Weekly($fullname = false, $color = true){
			return Array(
				0=>($color ? '<span style="color:red;">' : '').'일'.($fullname ? '요일' : '').($color ? '</span>' : '')
			,	1=>'월'.($fullname ? '요일' : '')
			,	2=>'화'.($fullname ? '요일' : '')
			,	3=>'수'.($fullname ? '요일' : '')
			,	4=>'목'.($fullname ? '요일' : '')
			,	5=>'금'.($fullname ? '요일' : '')
			,	6=>($color ? '<span style="color:blue;">' : '').'토'.($fullname ? '요일' : '').($color ? '</span>' : '')
			);
		}

		function dayofweek($date, $fullname = false){
			if (!$date) return;

			$dayofweek = $this->Weekly($fullname);
			return $dayofweek[$this->dowidx($date)];
		}

		function dowidx2name($dowidx, $fullname = false, $color = true){
			$dayofweek = $this->Weekly($fullname, $color);
			return $dayofweek[$dowidx];
		}


		// 급여배열초기화
		function initPaymentArray(){
			$payment = array();

			$payment['1_1_01']					= array('name' => '기본급',						'value' => 0);
			$payment['2_1_total']				= array('name' => '소득세합계',					'value' => 0);
			$payment['2_2_total']				= array('name' => '보험합계',					'value' => 0);
			$payment['2_3_total']				= array('name' => '기타합계',					'value' => 0);
			$payment['annuityAnnuity']			= array('name' => '국민연금(국민연금)',			'value' => 0);
			$payment['annuityCenter']			= array('name' => '국민연금(센터부담)',			'value' => 0);
			$payment['annuityCenterAnnuity']	= array('name' => '국민연금(센터국민연금)',		'value' => 0);
			$payment['annuityPay']				= array('name' => '국민연금신고급여',			'value' => 0);
			$payment['annuityYN']				= array('name' => '국민연금가입여부',			'value' => 0);
			$payment['bathSudang']				= array('name' => '목욕수당',					'value' => 0);
			$payment['bojeonSudang']			= array('name' => '보전수당',					'value' => 0);
			$payment['deductPay']				= array('name' => '공제전금액',					'value' => 0);
			$payment['diffPay']					= array('name' => '차인지급액',					'value' => 0);
			$payment['employAnnuity']			= array('name' => '고용보험(국민연금)',			'value' => 0);
			$payment['employCenter']			= array('name' => '고용보험(센터부담)',			'value' => 0);
			$payment['employCenterAnnuity']		= array('name' => '고용보험(센터국민연금)',		'value' => 0);
			$payment['employYN']				= array('name' => '고용보험가입여부',			'value' => 0);
			$payment['gongjaye']				= array('name' => '20세이하자녀수',				'value' => 0);
			$payment['gongjeja']				= array('name' => '공제자수',					'value' => 0);
			$payment['healthAnnuity']			= array('name' => '건강보험(국민연금)',			'value' => 0);
			$payment['healthCenter']			= array('name' => '건강보험(센터부담)',			'value' => 0);
			$payment['healthCenterAnnuity']		= array('name' => '건강보험(센터국민연금)',		'value' => 0);
			$payment['healthYN']				= array('name' => '건강보험가입여부',			'value' => 0);
			$payment['holidayHour']				= array('name' => '휴일가산근로시간',			'value' => 0);
			$payment['holidayRate']				= array('name' => '휴일근무가산비율',			'value' => 0);
			$payment['holidaySudang']			= array('name' => '휴일근로수당',				'value' => 0);
			$payment['holidayTime']				= array('name' => '월휴일근로시간',				'value' => 0);
			$payment['hourly']					= array('name' => '방문요양기준시급',			'value' => 0);
			$payment['minPay']					= array('name' => '계약시급',					'value' => 0);
			$payment['minusPay']				= array('name' => '차감액',						'value' => 0);
			$payment['nightHour']				= array('name' => '야간가산근로시간',			'value' => 0);
			$payment['nightRate']				= array('name' => '야간근무가산비율',			'value' => 0);
			$payment['nightSudang']				= array('name' => '야간근로수당',				'value' => 0);
			$payment['nightTime']				= array('name' => '월야간근로시간',				'value' => 0);
			$payment['nursingSudang']			= array('name' => '간호수당',					'value' => 0);
			$payment['oldcareAnnuity']			= array('name' => '노인장기요양(국민연금)',		'value' => 0);
			$payment['oldcareCenter']			= array('name' => '노인장기요양(센터부담)',		'value' => 0);
			$payment['oldcareCenterAnnuity']	= array('name' => '노인장기요양(센터국민연금)', 'value' => 0);
			$payment['planTime']				= array('name' => '계획시간',					'value' => 0);
			$payment['prolongHour']				= array('name' => '연장가산근로시간',			'value' => 0);
			$payment['prolongRate']				= array('name' => '연장근무가산비율',			'value' => 0);
			$payment['prolongSudang']			= array('name' => '연장근로수당',				'value' => 0);
			$payment['prolongTime']				= array('name' => '월연장근로시간',				'value' => 0);
			$payment['sanjeCenter']				= array('name' => '산재보험(센터부담)',			'value' => 0);
			$payment['sanjeCenterAnnuity']		= array('name' => '산재보험(센터국민연금)',		'value' => 0);
			$payment['sanjeYN']					= array('name' => '산재보험가입여부',			'value' => 0);
			$payment['totalHour']				= array('name' => '월산출총시간',				'value' => 0);
			$payment['totalPay']				= array('name' => '총급여',						'value' => 0);
			$payment['totalTax']				= array('name' => '과세총액',					'value' => 0);
			$payment['totalTaxfree']			= array('name' => '비과세총액',					'value' => 0);
			$payment['weekAppTime']				= array('name' => '주소정근로시간',				'value' => 0);
			$payment['weekCount']				= array('name' => '주휴일수',					'value' => 0);
			$payment['workCount']				= array('name' => '근무가능일수',				'value' => 0);
			$payment['workHour']				= array('name' => '월소정근로시간',				'value' => 0);
			$payment['workTime']				= array('name' => '월주간근무시간',				'value' => 0);
			$payment['workWeekTime']			= array('name' => '주휴시간',					'value' => 0);
			$payment['payType']					= array('name' => '시급고정여부',				'value' => 'Y');
			$payment['familyPay']				= array('name' => '가족케어시급',				'value' => 0);
			$payment['workDayCount']			= array('name' => '근무일수',					'value' => 0);
			$payment['bathCount']				= array('name' => '목욕횟수',					'value' => 0);
			$payment['nursingCount']			= array('name' => '간호횟수',					'value' => 0);
			$payment['yoyul']					= array('name' => '총액비율',					'value' => 0);
			$payment['suga']					= array('name' => '수가',						'value' => 0);

			return $payment;
		}


		function DrawCheckbox($objid, $obj = null){
			if ($obj['dftVal']) $obj['defVal'] = $obj['dftVal'];
			if (!$obj['defVal']) $obj['defVal'] = 'Y';
			if ($obj['linkObj'] && SubStr($obj['linkObj'], 0, 1) != '#') $obj['linkObj'] = '#'.$obj['linkObj'];

			$html = '<input id="'.$objid.'" type="checkbox" class="checkbox" value="'.$obj['defVal'].'" linkObj="'.$obj['linkObj'].'" paraFlag="'.$obj['paraFlag'].'" '.($obj['dis'] == 'Y' ? 'disabled="true"' : '').' '.($obj['defVal'] == $obj['value'] ? 'checked' : '').'><label for="'.$objid.'"></label>';

			if ($obj['name']) $html .= '<label for="'.$objid.'" style="'.$obj['style'].'">'.$obj['name'].'</label>';

			return $html;
		}


		function HSurl(){
			return "https://api.efnc.co.kr:1443";
		}

		function HSport(){
			return 1443;
		}

		function HSkey(){
			$swKey = '4LjFflzr6z4YSknp';
			$custKey = 'BT2z4D5DUm7cE5tl';

			return $swKey.':'.$custKey;
		}

		function HSSocketIP(){
			return "121.134.74.90";
		}

		function HSSocketPort(){
			return 16000;
		}
	}

	$myF = new myFun();

	$gHostImgPath = $myF->_get_host_image_path();

	//$gHostSvc     = $myF->_get_host_svc();
	$gHostNm	= $myF->host();
	$gDomainID  = $myF->_domain_id();
	$gDomain    = $myF->_domain();
	$gDomainNM  = $myF->_domain_name();
	$gCompanyCD = $conn->_company_code($gDomain);
	$gHostSvc   = $conn->_is_service($_SESSION['userCenterCode']);
	$gDayAndNight = $conn->_isDayAndNight($_SESSION['userCenterCode']);
	$gWMD = $conn->_isWMD($_SESSION['userCenterCode']);
	$gHSurl = $myF->HSurl();
	$gHSport = $myF->HSport();
	$gHSkey = $myF->HSkey();

	switch($gDomain){
		case _CAREVISIT_:
			$conn->m_center = 'GE01001';
			break;

		case _DWCARE_:
			$conn->m_center = 'ON01001';
			break;

		case _KLCF_:
			$conn->m_center = 'KL01001';
			break;

		case _KDOLBOM_:
			$conn->m_center = 'KD01001';
			break;

		case _DACARE_:
			$conn->m_center = 'DA01001';
			break;

		case _KACOLD_:
			$conn->m_center = 'KC01001';
			break;

		case _VAERP_:
			$conn->m_center = 'VA01001';
			break;

		case _DOLVOIN_:
			$conn->m_center = 'UD01001';
			break;

		case _FORWEAK_:
			$conn->m_center = 'FW01001';
			break;
	}
?>
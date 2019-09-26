<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code = $_POST['code'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$seq  = $_POST['seq'];
	$no   = $_POST['no'];
	$mode = $_POST['mode'];
	$yymm = substr($date, 0, 6);


	ob_start();


	if (empty($seq)) $seq = 0;
	if (empty($date)) $date = date('Y-m-d', mktime());

	echo '<input name=\'yymm\' type=\'hidden\' value=\''.$yymm.'\'>';
	echo '<input name=\'seq\'  type=\'hidden\' value=\''.$seq.'\'>';
	echo '<input name=\'no\'   type=\'hidden\' value=\''.$no.'\'>';
	echo '<input name=\'time\'   type=\'hidden\' value=\''.$time.'\'>';


	@include_once('./calendar_reg_'.$mode.'.php');


	$html = ob_get_contents();

	ob_end_clean();

	echo $html;


	include_once('../inc/_db_close.php');


	/*********************************************************

		시간인덱스

	*********************************************************/
	function getTimeList($index){
		$str = '';
		$i   = 0;

		while(1){
			$time = $i * 30;

			$hour = floor($time / 60);
			$min  = $time % 60;

			$hour = ($hour < 10 ? '0' : '').intval($hour);
			$min  = ($min  < 10 ? '0' : '').intval($min);

			$strTime = $hour.':'.$min;

			$str  .= '<option value=\''.($i).'\' '.($i == $index ? 'selected' : '').'>'.$strTime.'</option>';
			$i ++;

			if ($i > 47) break;
		}

		return $str;
	}
?>
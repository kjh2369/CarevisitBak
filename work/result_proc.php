<?
	include_once('../inc/_header.php');

	/*
	 * 실적일괄확정 처리
	 */
?>
<script src="../js/result.conf.js" type="text/javascript"></script>
<?
	$code = $_SESSION['userCenterCode'];
	$date = date('Y-m-d', mktime());

	$sql = "select closing_yymm
			  from closing_progress
			 where org_no            = '$code'
			   and del_flag          = 'N'
			   and act_bat_conf_flag = 'N'
			   and act_bat_conf_dt  <= '$date'
			 order by act_bat_conf_dt";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	echo '1.수급자 실적을 마감하는 중...<br>';

	$code = $_SESSION['userCenterCode'];

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$conf_date = $row[0];

		$year	= substr($conf_date, 0, 4);
		$month	= substr($conf_date, 4, 2);
		$gubun	= '1';?>
		<script language='javascript'>
			result_conf.code	= '<?=$code;?>';
			result_conf.year	= '<?=$year;?>';
			result_conf.month	= '<?=$month;?>';
			result_conf.gubun	= '<?=$gubun;?>';
			result_conf.conf();
		</script><?
	}

	$conn->row_free();

	$sql = "select closing_yymm
			  from closing_progress
			 where org_no               = '$code'
			   and del_flag             = 'N'
			   and salary_bat_calc_flag = 'N'
			   and salary_bat_calc_dt  <= '$date'
			 order by salary_bat_calc_dt";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	echo '2.요양보호사 급여를 계산하는 중...<br>';

	$code = $_SESSION['userCenterCode'];

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$conf_date = $row[0];

		$year	= substr($conf_date, 0, 4);
		$month	= substr($conf_date, 4, 2);
		$gubun	= '2';?>
		<script language='javascript'>
			result_conf.code	= '<?=$code;?>';
			result_conf.year	= '<?=$year;?>';
			result_conf.month	= '<?=$month;?>';
			result_conf.gubun	= '<?=$gubun;?>';
			result_conf.conf();
		</script><?
	}?>
	<script language='javascript'>
		result_conf.win = window;
		result_conf.code = '<?=$code;?>';
		result_conf.close();
		result_conf.show_result();
	</script><?

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
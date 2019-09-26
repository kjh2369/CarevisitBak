<?
	include_once('../inc/_db_open.php');

	$code	= $_SESSION['userCenterCode'];
	$date	= date('Y-m-d', mktime());

	$sql = "select count(*)
			  from closing_progress
			 where org_no             = '$code'
			   and del_flag           = 'N'
			   and ((act_bat_conf_flag    = 'N' and act_bat_conf_dt    <= '$date')
			    or  (salary_bat_calc_flag = 'N' and salary_bat_calc_dt <= '$date'))";

	$count = $conn->get_data($sql);
	$count = intval($count);

	echo $count;

	include_once('../inc/_db_close.php');
?>
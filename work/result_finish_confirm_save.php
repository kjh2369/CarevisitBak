<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code	= $_SESSION['userCenterCode'];
	$user	= $_SESSION['userCode'];
	$mode	= $_REQUEST['mode'];
	$gubun	= $_REQUEST['gubun'];
	$date	= date('Y-m-d', mktime());

	$year	= $_REQUEST['year'];
	$month	= ($_REQUEST['month'] < 10 ? '0' : '').$_REQUEST['month'];

	/*
	$hist	= $conn->get_data("select max(closing_tmstmp)
								 from closing_progress_hist
								where org_no         = '$code'
								  and closing_yymm   = '$year$month'");
	*/

	$conf_dt	= $_REQUEST['conf_y'].'-'.$_REQUEST['conf_m'].'-'.$_REQUEST['conf_d'];
	$calc_dt	= $_REQUEST['calc_y'].'-'.$_REQUEST['calc_m'].'-'.$_REQUEST['calc_d'];

	$conn->begin();

	if ($gubun == 1){
		$sql = "update closing_progress
				   set update_id        = '$user'
				,      update_dt        = '$date'
				,      act_bat_conf_dt  = '$conf_dt'
				 where org_no           = '$code'
				   and closing_yymm     = '$year$month'";
	}else{
		$sql = "update closing_progress
				   set update_id          = '$user'
				,      update_dt          = '$date'
				,      salary_bat_calc_dt = '$calc_dt'
				 where org_no             = '$code'
				   and closing_yymm       = '$year$month'";

	}

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$sql = "insert into closing_progress_hist (
			 org_no
			,closing_yymm
			,closing_tmstmp
			,create_id
			,create_dt
			,update_id
			,update_dt
			,del_flag
			,act_cls_dt_from
			,nhic_bill_cls_dt_from
			,salary_cls_dt_from
			,act_bat_conf_flag
			,act_bat_conf_dt
			,act_bat_can_flag
			,act_bat_can_dt
			,salary_bat_calc_flag
			,salary_bat_calc_dt
			,salary_bat_can_flag
			,salary_bat_can_dt)
			select org_no
			,      closing_yymm
			,      now()
			,      create_id
			,      create_dt
			,      update_id
			,      update_dt
			,      del_flag
			,      act_cls_dt_from
			,      nhic_bill_cls_dt_from
			,      salary_cls_dt_from
			,      act_bat_conf_flag
			,      act_bat_conf_dt
			,      act_bat_can_flag
			,      act_bat_can_dt
			,      salary_bat_calc_flag
			,      salary_bat_calc_dt
			,      salary_bat_can_flag
			,      salary_bat_can_dt
			  from closing_progress
			 where org_no			 = '$code'
					and closing_yymm = '$year$month'";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $myF->message('error', 'Y', 'Y');
		exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>
<script language='javascript'>
	alert("<?=$myF->message('ok','N');?>");
	location.replace("result_finish_confirm.php?mode=<?=$mode;?>&year=<?=$year;?>&month=<?=$intval($month);?>");
</script>
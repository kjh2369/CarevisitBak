<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$code	= $_SESSION['userCenterCode'];
	$user	= $_SESSION['userCode'];
	$gubun	= $_GET['gubun'];
	$t_dt	= date('Y-m-d', mktime());
	$s_dt	= $_POST['year'].($_POST['month'] < 10 ? '0' : '').$_POST['month'];
	//$w_dt	= $_POST['w_y'].'-'.$_POST['w_m'].'-'.$_POST['w_d'];
	//$p_dt	= $_POST['p_y'].'-'.$_POST['p_m'].'-'.$_POST['p_d'];
	$w_dt	= $t_dt;
	$p_dt	= $t_dt;

	$flag_yn = $_POST['flag_yn'];

	if ($flag_yn == 'Y'){
		$flag_yn = 'N';
	}else{
		$flag_yn = 'Y';
	}

	$conn->begin();

	$sql = "select count(*)
			  from closing_progress
			 where org_no       = '$code'
			   and closing_yymm = '$s_dt'";
	$i_count = $conn->get_data($sql);

	if ($i_count == 0){
		$sql = "insert into closing_progress (org_no, closing_yymm) values ('$code','$s_dt')";

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $myF->message('error', 'Y', 'Y');
			exit;
		}

		$sql = "update closing_progress
				   set create_id = '$user'
				,      create_dt = '$t_dt'";
	}else{
		$sql = "update closing_progress
				   set update_id = '$user'
				,      update_dt = '$t_dt'";
	}

	if ($gubun == 1){
		$sql .= ",      act_cls_flag        = '$flag_yn'
				 ,      act_cls_dt_from		= '$w_dt'
				 ,      act_cls_ent_dt		= '$t_dt'
				  where org_no				= '$code'
					and closing_yymm		= '$s_dt'";
	}else{
		$sql .= ",      salary_cls_flag 	= '$flag_yn'
				 ,      salary_cls_dt_from	= '$p_dt'
				 ,      salary_cls_ent_dt	= '$t_dt'
				  where org_no				= '$code'
					and closing_yymm		= '$s_dt'";
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
					and closing_yymm = '$s_dt'";

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
	location.replace("result_finish.php?mode=<?=$_POST['mode'];?>&year=<?=$_POST['year'];?>&month=<?=$_POST['month'];?>");
</script>
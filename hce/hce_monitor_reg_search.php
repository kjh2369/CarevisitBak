<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');
	include('./hce_var.php');

	$seq = $_POST['seq'];

	$sql = 'SELECT	mntr_dt
			,		mntr_gbn
			,		mntr_type
			,		per_nm
			,		per_jumin
			,		inspector_nm
			,		inspector_jumin
			,		schedule_sat
			,		schedule_svc
			,		fullness_sat
			,		fullness_svc
			,		perincharge_sat
			,		perincharge_svc
			,		ability_change
			,		life_env_change
			,		ext_discomfort
			,		monitor_rst
			,		ext_detail
			FROM	hce_monitor
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		mntr_seq= \''.$seq.'\'
			AND		del_flag= \'N\'';

	$row = $conn->get_array($sql);

	if ($row){
		$data .= 'date='				.$row['mntr_dt'];
		$data .= '&gbn='				.$row['mntr_gbn'];
		$data .= '&type='				.$row['mntr_type'];
		$data .= '&perNm='				.$row['per_nm'];
		$data .= '&perJumin='			.$ed->en($row['per_jumin']);
		$data .= '&ispNm='				.$row['inspector_nm'];
		$data .= '&ispJumin='			.$ed->en($row['inspector_jumin']);
		$data .= '&scheduleGbn='		.$row['schedule_sat'];
		$data .= '&scheduleStr='		.StripSlashes($row['schedule_svc']);
		$data .= '&fullnessGbn='		.$row['fullness_sat'];
		$data .= '&fullnessStr='		.StripSlashes($row['fullness_svc']);
		$data .= '&perinchargeGbn='		.$row['perincharge_sat'];
		$data .= '&perinchargeStr='		.StripSlashes($row['perincharge_svc']);
		$data .= '&abilityStr='			.StripSlashes($row['ability_change']);
		$data .= '&lifeEnvStr='			.StripSlashes($row['life_env_change']);
		$data .= '&extDiscomfortStr='	.StripSlashes($row['ext_discomfort']);
		$data .= '&monitorRst='			.$row['monitor_rst'];
		$data .= '&extDetailStr='		.StripSlashes($row['ext_detail']);
	}

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$date  = $_POST['date'];
	$from  = $_POST['from'];
	$seq   = $_POST['seq'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$sql = 'select t01_conf_date as dt
			,      t01_conf_fmtime as f_time
			,      t01_conf_totime as t_time
			,      t01_conf_soyotime as proctime
			,      t01_yoyangsa_id1 as mem_cd1
			,      t01_yoyangsa_id2 as mem_cd2
			,      t01_yname1 as mem_nm1
			,      t01_yname2 as mem_nm2
			,      t01_conf_suga_code as suga_cd
			,      t01_conf_suga_value as suga_cost
			  from t01iljung
			 where t01_ccode        = \''.$code.'\'
			   and t01_mkind        = \''.$svcCd.'\'
			   and t01_jumin        = \''.$jumin.'\'
			   and t01_sugup_date   = \''.$date.'\'
			   and t01_sugup_fmtime = \''.$from.'\'
			   and t01_sugup_seq    = \''.$seq.'\'
			   and t01_status_gbn   = \'1\'';

	$row = $conn->get_array($sql);

	if ($svcCd == '0'){
		$sql = 'select m01_suga_cont as name
				  from m01suga
				 where m01_mcode  = \'goodeos\'
				   and m01_mcode2 = \''.$row['suga_cd'].'\'
				   and m01_sdate <= \''.$row['dt'].'\'
				   and m01_edate >= \''.$row['dt'].'\'
				 union all
				select m11_suga_cont as name
				  from m11suga
				 where m11_mcode  = \'goodeos\'
				   and m11_mcode2 = \''.$row['suga_cd'].'\'
				   and m11_sdate <= \''.$row['dt'].'\'
				   and m11_edate >= \''.$row['dt'].'\'';
	}else{
		$sql = 'select case service_kind when \'4\' then
		                                 case left(service_code,3) when \'VAA\' then concat(service_gbn,\'/\',\''.$row['proctime'].'분\')
										      else concat(service_gbn,\'/\',service_lvl) end
		                    else service_gbn end
				  from suga_service
				 where org_no       = \'goodeos\'
				   and service_code = \''.$row['suga_cd'].'\'
				   and date_format(service_from_dt,\'%Y%m%d\') <= \''.$row['dt'].'\'
				   and date_format(service_to_dt,  \'%Y%m%d\') >= \''.$row['dt'].'\'';
	}

	$lsSugaNm = $conn->get_data($sql);

	echo 'dt='.$myF->dateStyle(trim($row['dt']))
		.'&from='.trim($row['f_time'])
		.'&to='.trim($row['t_time'])
		.'&memCd1='.$ed->en(trim($row['mem_cd1']))
		.'&memNm1='.trim($row['mem_nm1'])
		.'&memCd2='.$ed->en(trim($row['mem_cd2']))
		.'&memNm2='.trim($row['mem_nm2'])
		.'&sugaCd='.trim($row['suga_cd'])
		.'&sugaNm='.$lsSugaNm
		.'&sugaCost='.trim($row['suga_cost']);

	unset($row);

	include_once('../inc/_db_close.php');
?>
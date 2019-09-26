<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');

	$gbn = $_REQUEST['gbn'];

	switch($gbn){
		######################################################################
		#
		# 수가의 단가
		#
		case 'suga_cost':
			$code    = $_REQUEST['code']; //기관코드
			$svc_id  = $_REQUEST['svc_id']; //수가코드
			$svc_gbn = $_REQUEST['svc_gbn']; //
			$svc_cd  = $_REQUEST['svc_cd']; //
			$svc_val = $_REQUEST['svc_val']; //
			$date    = $_REQUEST['date']; //적용일자

			if ($svc_id == '24')
				$svc_var = '0';
			else
				$svc_var = $svc_val;

			if (empty($date)) $date = date('Y-m-d', mktime());

			$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);

			$sql = "select service_cost
					  from suga_service
					 where org_no           = '$code'
					   and service_code     = '$suga'
					   and service_from_dt <= '$date'
					   and service_to_dt   >= '$date'";

			$val = $conn->get_data($sql);

			echo $val; //$suga.'/'.$svc_gbn.'/'.$svc_cd.'/'.$svc_val;

			break;
		#
		######################################################################

		######################################################################
		#
		# 본인 부담금
		#
		case 'suga_pay1':
			$code    = $_REQUEST['code'];  //기관코드
			$svc_id  = $_REQUEST['svc_id'];  //수가코드
			$svc_gbn = $_REQUEST['svc_gbn'];  //
			$svc_cd  = $_REQUEST['svc_cd']; //
			$svc_val = $_REQUEST['svc_val']; //
			$level   = $_REQUEST['level']; //소득등급

			if ($svc_id == '24')
				$svc_var = '0';
			else
				$svc_var = $svc_val;

			$date    = str_replace('-', '', $_REQUEST['date']); //적용일자
			$suga    = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);
			$time    = $myF->zero_str($_REQUEST['time'],5-strlen($suga)); //서비스시간

			if (strlen($date) != 8) $date = date('Ymd', mktime());

			$sql = "select person_amt$level
					  from suga_person
					 where org_no          = '$code'
					   and person_code     = '$suga$time'
					   and person_from_dt <= '$date'
					   and person_to_dt   >= '$date'";

			$var = $conn->get_data($sql);

			echo $var;

			break;
		#
		######################################################################

		######################################################################
		#
		# 노인돌봄 서비스 시간
		#
		case 'svc_time':
			$code    = $_REQUEST['code'];
			$var     = $_REQUEST['val'];
			$value   = $_REQUEST['value'];
			$svc_id  = $_REQUEST['svc_id'];  //수가코드
			$svc_gbn = $_REQUEST['svc_gbn'];  //
			$suga    = $myF->voucher_suga($svc_id, $svc_gbn);
			$date    = str_replace('-', '', $_REQUEST['date']); //적용일자

			if (strlen($date) != 8) $date = date('Ymd', mktime());

			$sql = "select person_code
					,      person_id
					,      person_conf_time
					  from suga_person
					 where org_no          = '$code'
					   and person_from_dt <= '$date'
					   and person_to_dt   >= '$date'";

			if ($svc_id == '23' || $svc_id == '31'){
				$sql .= "
					   and person_code  like '$suga%'
					   and person_id       = '$var'";
			}else{
				$sql .= "
					   and person_code  like '$suga$var%'";
			}


			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			$val = '';

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$val .= $var.'/'.$row['person_code'].'/'.$row['person_id'].'/'.$row['person_conf_time'].'/'.$value.'/'.$var.';';
			}

			$conn->row_free();

			echo $val;

			break;
		#
		######################################################################

		######################################################################
		#
		# 장애인보조 서비스 시간
		#
			case 'dis_time':
				$code    = $_REQUEST['code']; //기관코드
				$svc_id  = $_REQUEST['svc_id']; //수가코드
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$svc_cd  = $_REQUEST['svc_cd']; //
				$svc_val = $_REQUEST['svc_val']; //
				$date    = $_REQUEST['date']; //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				//$suga = $myF->voucher_suga($svc_id, $svc_val, $svc_cd, $svc_gbn);
				$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);

				$sql = "select service_conf_time
							  from suga_service
							 where org_no           = '$code'
							   and service_code     = '$suga'
							   and service_from_dt <= '$date'
							   and service_to_dt   >= '$date'";

				$val = $conn->get_data($sql);

				$sql = 'select svc_time
						  from suga_service_add
						 where svc_kind     = \'4\'
						   and svc_gbn_cd   = \''.$svc_val.'\'
						   and svc_from_dt <= \''.$date.'\'
						   and svc_to_dt   >= \''.$date.'\'';

				$val += $conn->get_data($sql);

				echo $val;

				break;

			case 'dis_stnd_time':
				$code    = $_REQUEST['code']; //기관코드
				$svc_id  = $_REQUEST['svc_id']; //수가코드
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$svc_cd  = $_REQUEST['svc_cd']; //
				$svc_val = $_REQUEST['svc_val']; //
				$date    = $_REQUEST['date']; //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				//$suga = $myF->voucher_suga($svc_id, $svc_val, $svc_cd, $svc_gbn);
				$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);

				$sql = "select service_conf_time
							  from suga_service
							 where org_no           = '$code'
							   and service_code     = '$suga'
							   and service_from_dt <= '$date'
							   and service_to_dt   >= '$date'";

				$val = $conn->get_data($sql);

				echo $val;

				break;

			case 'dis_add_time':
				$code    = $_REQUEST['code']; //기관코드
				$svc_id  = $_REQUEST['svc_id']; //수가코드
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$svc_cd  = $_REQUEST['svc_cd']; //
				$svc_val = $_REQUEST['svc_val']; //
				$date    = $_REQUEST['date']; //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				//$suga = $myF->voucher_suga($svc_id, $svc_val, $svc_cd, $svc_gbn);
				$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);

				$sql = 'select svc_time
						  from suga_service_add
						 where svc_kind     = \'4\'
						   and svc_gbn_cd   = \''.$svc_val.'\'
						   and svc_from_dt <= \''.$date.'\'
						   and svc_to_dt   >= \''.$date.'\'';

				$val = $conn->get_data($sql);

				echo intval($val);

				break;
		#
		######################################################################




		/**************************************************

			수가 금액

		**************************************************/

			case 'svc_max_pay':
				$code    = $_REQUEST['code'];    //기관코드
				$kind    = $_REQUEST['kind'];
				$svc_cd  = $_REQUEST['svc_cd'];  //
				$svc_id  = $_REQUEST['svc_id'];  //
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$svc_val = $_REQUEST['svc_val']; //
				$date    = $_REQUEST['date'];    //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);

				/**************************************************
					수가
				**************************************************/
				$sql = 'select service_conf_amt
							  from suga_service
							 where org_no           = \''.$code.'\'
							   and service_code     = \''.$suga.'\'
							   and service_from_dt <= \''.$date.'\'
							   and service_to_dt   >= \''.$date.'\'';

				$val = $conn->get_data($sql);



				/**************************************************
					추가급여
				**************************************************/
				$sql = 'select svc_pay
						  from suga_service_add
						 where svc_kind     = \''.$kind.'\'
						   and svc_gbn_cd   = \''.$svc_val.'\'
						   and svc_from_dt <= \''.$date.'\'
						   and svc_to_dt   >= \''.$date.'\'';

				$val += $conn->get_data($sql);

				echo $val;

				break;


			/*********************************************************
				기본급여
			*********************************************************/
			case 'svc_stnd_max_pay':
				$code    = $_REQUEST['code'];    //기관코드
				$kind    = $_REQUEST['kind'];
				$svc_cd  = $_REQUEST['svc_cd'];  //
				$svc_id  = $_REQUEST['svc_id'];  //
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$svc_val = $_REQUEST['svc_val']; //
				$date    = $_REQUEST['date'];    //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);

				/**************************************************
					수가
				**************************************************/
				$sql = 'select service_conf_amt
							  from suga_service
							 where org_no           = \''.$code.'\'
							   and service_code     = \''.$suga.'\'
							   and service_from_dt <= \''.$date.'\'
							   and service_to_dt   >= \''.$date.'\'';

				$val = $conn->get_data($sql);

				echo $val;

				break;


			/*********************************************************
				추가급여
			*********************************************************/
			case 'svc_add_max_pay':
				$code    = $_REQUEST['code'];    //기관코드
				$kind    = $_REQUEST['kind'];
				$svc_cd  = $_REQUEST['svc_cd'];  //
				$svc_id  = $_REQUEST['svc_id'];  //
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$svc_val = $_REQUEST['svc_val']; //
				$date    = $_REQUEST['date'];    //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);

				/**************************************************
					추가급여
				**************************************************/
				$sql = 'select svc_pay
						  from suga_service_add
						 where svc_kind     = \''.$kind.'\'
						   and svc_gbn_cd   = \''.$svc_val.'\'
						   and svc_from_dt <= \''.$date.'\'
						   and svc_to_dt   >= \''.$date.'\'';

				$val = $conn->get_data($sql);

				echo intval($val);

				break;

		/*************************************************/




		/**************************************************

			본인부담액

		**************************************************/

			case 'svc_self_pay':
				$code    = $_REQUEST['code'];    //기관코드
				$kind    = $_REQUEST['kind'];
				$svc_cd  = $_REQUEST['svc_cd'];  //
				$svc_id  = $_REQUEST['svc_id'];  //
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$svc_val = $_REQUEST['svc_val']; //
				$svc_kind= $_REQUEST['svc_kind']; //
				$date    = $_REQUEST['date'];    //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);



				/**************************************************
					상한금액
				**************************************************/
				$sql = 'select lvl_limit_pay as pay
						  from income_lvl_self_limit
						 where lvl_kind     = \''.$kind.'\'
						   and lvl_from_dt <= \''.$date.'\'
						   and lvl_to_dt   >= \''.$date.'\'';

				$limit_pay = $conn->get_data($sql);



				/**************************************************
					수가
				**************************************************/
				$sql = 'select service_conf_amt
							  from suga_service
							 where org_no           = \''.$code.'\'
							   and service_code     = \''.$suga.'\'
							   and service_from_dt <= \''.$date.'\'
							   and service_to_dt   >= \''.$date.'\'';

				$suga_pay = $conn->get_data($sql);

				$sql = 'select lvl_rate as rate
						,      lvl_pay as pay
						  from income_lvl_self_pay
						 where lvl_kind     = \''.$kind.'\'
						   and lvl_id       = \''.$svc_kind.'\'
						   and lvl_gbn      = \'1\'
						   and lvl_from_dt <= \''.$date.'\'
						   and lvl_to_dt   >= \''.$date.'\'';

				$self_if = $conn->get_array($sql);

				if ($self_if['pay'] > 0)
					$self_pay = $self_if['pay'];
				else
					$self_pay = $suga_pay * $self_if['rate'];

				if ($self_pay > $limit_pay) $self_pay = $limit_pay;




				/**************************************************
					추가급여
				**************************************************/
				$sql = 'select svc_pay
						  from suga_service_add
						 where svc_kind     = \''.$kind.'\'
						   and svc_gbn_cd   = \''.$svc_val.'\'
						   and svc_from_dt <= \''.$date.'\'
						   and svc_to_dt   >= \''.$date.'\'';

				$suga_pay = $conn->get_data($sql);



				/**************************************************
					추가급여 본인부담비율
				**************************************************/
				$sql = 'select lvl_rate as rate
						,      lvl_pay as pay
						  from income_lvl_self_pay
						 where lvl_kind     = \''.$kind.'\'
						   and lvl_id       = \''.$svc_kind.'\'
						   and lvl_gbn      = \'2\'
						   and lvl_from_dt <= \''.$date.'\'
						   and lvl_to_dt   >= \''.$date.'\'';

				$self_if = $conn->get_array($sql);

				if ($self_if['pay'] > 0)
					$self_add_pay = $self_if['pay'];
				else
					$self_add_pay = $suga_pay * $self_if['rate'];


				/**************************************************
					본인부담금액
				**************************************************/
				$self_pay += $self_add_pay;

				echo $self_pay;

				break;

		/*************************************************/





		/*********************************************************
			기본급여 본인부담금
		*********************************************************/
			case 'svc_stnd_self_pay':
				$code    = $_REQUEST['code'];    //기관코드
				$kind    = $_REQUEST['kind'];
				$svc_cd  = $_REQUEST['svc_cd'];  //
				$svc_id  = $_REQUEST['svc_id'];  //
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$svc_val = $_REQUEST['svc_val']; //
				$svc_kind= $_REQUEST['svc_kind']; //
				$date    = $_REQUEST['date'];    //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);



				/**************************************************
					상한금액
				**************************************************/
				$sql = 'select lvl_limit_pay as pay
						  from income_lvl_self_limit
						 where lvl_kind     = \''.$kind.'\'
						   and lvl_from_dt <= \''.$date.'\'
						   and lvl_to_dt   >= \''.$date.'\'';

				$limit_pay = $conn->get_data($sql);



				/**************************************************
					수가
				**************************************************/
				$sql = 'select service_conf_amt
							  from suga_service
							 where org_no           = \''.$code.'\'
							   and service_code     = \''.$suga.'\'
							   and service_from_dt <= \''.$date.'\'
							   and service_to_dt   >= \''.$date.'\'';

				$suga_pay = $conn->get_data($sql);

				$sql = 'select lvl_rate as rate
						,      lvl_pay as pay
						  from income_lvl_self_pay
						 where lvl_kind     = \''.$kind.'\'
						   and lvl_id       = \''.$svc_kind.'\'
						   and lvl_gbn      = \'1\'
						   and lvl_from_dt <= \''.$date.'\'
						   and lvl_to_dt   >= \''.$date.'\'';

				$self_if = $conn->get_array($sql);

				if ($self_if['pay'] > 0)
					$self_pay = $self_if['pay'];
				else
					$self_pay = $suga_pay * $self_if['rate'];

				if ($self_pay > $limit_pay) $self_pay = $limit_pay;

				echo $self_pay;

				break;
		/********************************************************/



		/*********************************************************
			추가급여 본인부담금
		*********************************************************/
			case 'svc_add_self_pay':
				$code    = $_REQUEST['code'];    //기관코드
				$kind    = $_REQUEST['kind'];
				$svc_cd  = $_REQUEST['svc_cd'];  //
				$svc_id  = $_REQUEST['svc_id'];  //
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$svc_val = $_REQUEST['svc_val']; //
				$svc_kind= $_REQUEST['svc_kind']; //
				$date    = $_REQUEST['date'];    //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				$suga = $myF->voucher_suga($svc_id, $svc_gbn, $svc_cd, $svc_var);



				/**************************************************
					추가급여
				**************************************************/
				$sql = 'select svc_pay
						  from suga_service_add
						 where svc_kind     = \''.$kind.'\'
						   and svc_gbn_cd   = \''.$svc_val.'\'
						   and svc_from_dt <= \''.$date.'\'
						   and svc_to_dt   >= \''.$date.'\'';

				$suga_pay = $conn->get_data($sql);



				/**************************************************
					추가급여 본인부담비율
				**************************************************/
				$sql = 'select lvl_rate as rate
						,      lvl_pay as pay
						  from income_lvl_self_pay
						 where lvl_kind     = \''.$kind.'\'
						   and lvl_id       = \''.$svc_kind.'\'
						   and lvl_gbn      = \'2\'
						   and lvl_from_dt <= \''.$date.'\'
						   and lvl_to_dt   >= \''.$date.'\'';

				$self_if = $conn->get_array($sql);

				if ($self_if['pay'] > 0)
					$self_add_pay = $self_if['pay'];
				else
					$self_add_pay = $suga_pay * $self_if['rate'];

				echo $self_add_pay;

				break;
		/********************************************************/




		/*********************************************************

			장애인활동지원 기본수가

		*********************************************************/
			case 'svc_basic_cost':
				$code    = $_REQUEST['code'];    //기관코드
				$kind    = $_REQUEST['kind'];
				$svc_id  = $_REQUEST['svc_id'];  //
				$svc_gbn = $_REQUEST['svc_gbn']; //
				$date    = $_REQUEST['date'];    //적용일자

				if ($svc_id == '24')
					$svc_var = '0';
				else
					$svc_var = $svc_val;

				if (empty($date)) $date = date('Y-m-d', mktime());

				$suga = $myF->voucher_suga($svc_id, $svc_gbn);

				$sql = 'select service_cost
						  from suga_service
						 where org_no           = \''.$code.'\'
						   and service_from_dt <= \''.$date.'\'
						   and service_to_dt   >= \''.$date.'\'
						   and left(service_code, '.strlen($suga).') = \''.$suga.'\'
						 limit 1';

				$val = $conn->get_data($sql);

				echo $val;

				break;



		/*********************************************************

			산모 기본 시간과 금액

		*********************************************************/
			case 'svc_baby_default':
				$svc  = $_REQUEST['svc'];
				$date = $_REQUEST['date'];
				$code = 'VM'.$svc.'01';

				if (empty($date)) $date = date('Ymd', mktime());

				$sql = 'select service_conf_time as days
						,      service_cost as cost
						  from suga_service
						 where org_no           = \'goodeos\'
						   and service_kind     = \'3\'
						   and service_code     = \''.$code.'\'
						   and service_from_dt <= \''.$date.'\'
						   and service_to_dt   >= \''.$date.'\'';

				$tmp = $conn->get_array($sql);

				echo $tmp['days'].'/'.$tmp['cost'];

				unset($tmp);

				break;
	}

	include_once('../inc/_db_close.php');
?>
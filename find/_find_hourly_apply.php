<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code			= $_POST['code'];
	$jumin			= $ed->de($_POST['jumin']);
	$svcID			= $_POST['svcID'];
	$seq			= $_POST['seq'];
	$type			= $_POST['type'];
	$hourly			= IntVal(str_replace(',', '', $_POST['hourly']));
	$varyHourly[1]	= IntVal(str_replace(',', '', $_POST['varyHourly_1']));
	$varyHourly[2]	= IntVal(str_replace(',', '', $_POST['varyHourly_2']));
	$varyHourly[3]	= IntVal(str_replace(',', '', $_POST['varyHourly_3']));
	$varyHourly[4]	= IntVal(str_replace(',', '', $_POST['varyHourly_4']));
	$varyHourly[5]	= IntVal(str_replace(',', '', $_POST['varyHourly_5']));
	$varyHourly[6]	= IntVal(str_replace(',', '', $_POST['varyHourly_6']));
	$varyHourly[7]	= IntVal(str_replace(',', '', $_POST['varyHourly_7']));
	$varyHourly[8]	= IntVal(str_replace(',', '', $_POST['varyHourly_8']));
	$varyHourly[9]	= IntVal(str_replace(',', '', $_POST['varyHourly_9']));
	$hourlyRate		= str_replace(',', '', $_POST['hourlyRate']);
	$hourlyRateSub	= IntVal(str_replace(',', '', $_POST['hourlyRateSub']));
	$hourlyRateSub	= ($hourlyRateSub < 10 ? '0' : '').$hourlyRateSub;
	$fixedPay		= str_replace(',', '', $_POST['fixedPay']);
	$dailyPay[1]	= IntVal(str_replace(',', '', $_POST['dailyPay_1']));
	$dailyPay[2]	= IntVal(str_replace(',', '', $_POST['dailyPay_2']));
	$dailyPay[3]	= IntVal(str_replace(',', '', $_POST['dailyPay_3']));
	$extraYN		= $_POST['extraYN'];
	$fromDT			= str_replace('-', '', $_POST['fromDT']);
	$toDT			= str_replace('-', '', $_POST['toDT']);
	$lastDT			= $myF->dateAdd('month', -1, $fromDT.'01', 'Ym');

	if (empty($code)){
		$conn->close();
		echo 'ok';
		return;
	}

	if (empty($toDT)) $toDT = '999912';

	switch($svcID){
		case '11': $kind = '0'; break; //재가요양
		case '12': $kind = '0'; break; //동거가족
		case '21': $kind = '1'; break; //가사간병
		case '22': $kind = '2'; break; //노인돌봄
		case '23': $kind = '3'; break; //산모신생아
		case '24': $kind = '4'; break; //장애인활동보조
	}


	/*********************************************************
		처음 생성일
	*********************************************************/
	$sql = 'select min(mh_from_dt)
			  from mem_hourly
			 where org_no      = \''.$code.'\'
			   and mh_jumin    = \''.$jumin.'\'
			   and mh_svc      = \''.$svcID.'\'
			   and del_flag    = \'N\'';

	$minDT = $conn->get_data($sql);


	/*********************************************************
		일자 중복 체크
	*********************************************************/
	$sql = 'select mh_from_dt
			  from mem_hourly
			 where org_no      = \''.$code.'\'
			   and mh_jumin    = \''.$jumin.'\'
			   and mh_svc      = \''.$svcID.'\'
			   and mh_from_dt >= \''.$fromDT.'\'
			   and del_flag    = \'N\'
			 order by mh_from_dt, mh_to_dt
			 limit 1';

	$chkDT = $conn->get_data($sql);

	if (empty($chkDT)){
		$conn->begin();

		/*********************************************************
			신규
		*********************************************************/
		$sql = 'select mh_seq
				  from mem_hourly
				 where org_no      = \''.$code.'\'
				   and mh_jumin    = \''.$jumin.'\'
				   and mh_svc      = \''.$svcID.'\'
				   and mh_from_dt <= \''.$fromDT.'\'
				   and mh_to_dt   >= \''.$fromDT.'\'
				   and del_flag    = \'N\'
				 order by mh_from_dt desc, mh_to_dt desc
				 limit 1';

		$chkSeq = $conn->get_data($sql);


		//전 일정의 종료일을 변경한다.
		$sql = 'update mem_hourly
				   set mh_to_dt  = \''.$lastDT.'\'
				,      update_id = \''.$_SESSION['userCode'].'\'
				,      update_dt = now()
				 where org_no    = \''.$code.'\'
				   and mh_jumin  = \''.$jumin.'\'
				   and mh_svc    = \''.$svcID.'\'
				   and mh_seq    = \''.$chkSeq.'\'
				   and del_flag  = \'N\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->close();
			echo 'error';
			exit;
		}


		//새 순번
		$sql = 'select ifnull(max(mh_seq), 0) + 1
				  from mem_hourly
				 where org_no   = \''.$code.'\'
				   and mh_jumin = \''.$jumin.'\'
				   and mh_svc   = \''.$svcID.'\'';

		$seq = $conn->get_data($sql);


		//새 일정 등록
		$sql = 'insert into mem_hourly (
				 org_no
				,mh_jumin
				,mh_svc
				,mh_seq
				,mh_kind
				,mh_type
				,mh_hourly
				,mh_vary_hourly_1
				,mh_vary_hourly_2
				,mh_vary_hourly_3
				,mh_vary_hourly_4
				,mh_vary_hourly_5
				,mh_vary_hourly_6
				,mh_vary_hourly_7
				,mh_vary_hourly_8
				,mh_vary_hourly_9
				,mh_hourly_rate
				,mh_fixed_pay
				,mh_daily_pay1
				,mh_daily_pay2
				,mh_daily_pay3
				,mh_extra_yn
				,mh_from_dt
				,mh_to_dt
				,insert_id
				,insert_dt) values (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$svcID.'\'
				,\''.$seq .'\'
				,\''.$kind.'\'
				,\''.$type.'\'
				,\''.$hourly.'\'
				,\''.$varyHourly[1].'\'
				,\''.$varyHourly[2].'\'
				,\''.$varyHourly[3].'\'
				,\''.$varyHourly[4].'\'
				,\''.$varyHourly[5].'\'
				,\''.$varyHourly[6].'\'
				,\''.$varyHourly[7].'\'
				,\''.$varyHourly[8].'\'
				,\''.$varyHourly[9].'\'
				,\''.$hourlyRate.'.'.$hourlyRateSub.'\'
				,\''.$fixedPay.'\'
				,\''.$dailyPay[1].'\'
				,\''.$dailyPay[2].'\'
				,\''.$dailyPay[3].'\'
				,\''.$extraYN.'\'
				,\''.$fromDT.'\'
				,\''.$toDT.'\'
				,\''.$_SESSION['userCode'].'\'
				,now())';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->close();
			echo 'error';
			exit;
		}

		$conn->commit();

		echo $seq;
	}else{
		/*********************************************************
			일자중복
		*********************************************************/
		if ($fromDT == $chkDT){
			$conn->begin();

			//새 순번
			$sql = 'select mh_seq
					  from mem_hourly
					 where org_no     = \''.$code.'\'
					   and mh_jumin   = \''.$jumin.'\'
					   and mh_svc     = \''.$svcID.'\'
					   and mh_from_dt = \''.$fromDT.'\'
					   and del_flag   = \'N\'';

			$seq = $conn->get_data($sql);

			$sql = 'update	mem_hourly
					set		mh_type			= \''.$type.'\'
					,		mh_hourly		= \''.$hourly.'\'
					,		mh_vary_hourly_1= \''.$varyHourly[1].'\'
					,		mh_vary_hourly_2= \''.$varyHourly[2].'\'
					,		mh_vary_hourly_3= \''.$varyHourly[3].'\'
					,		mh_vary_hourly_4= \''.$varyHourly[4].'\'
					,		mh_vary_hourly_5= \''.$varyHourly[5].'\'
					,		mh_vary_hourly_6= \''.$varyHourly[6].'\'
					,		mh_vary_hourly_7= \''.$varyHourly[7].'\'
					,		mh_vary_hourly_8= \''.$varyHourly[8].'\'
					,		mh_vary_hourly_9= \''.$varyHourly[9].'\'
					,		mh_hourly_rate  = \''.$hourlyRate.'.'.$hourlyRateSub.'\'
					,		mh_fixed_pay    = \''.$fixedPay.'\'
					,		mh_daily_pay1	= \''.$dailyPay[1].'\'
					,		mh_daily_pay2	= \''.$dailyPay[2].'\'
					,		mh_daily_pay3	= \''.$dailyPay[3].'\'
					,		mh_extra_yn		= \''.$extraYN.'\'
					,		update_id		= \''.$_SESSION['userCode'].'\'
					,		update_dt		= now()
					where	org_no			= \''.$code.'\'
					and		mh_jumin		= \''.$jumin.'\'
					and		mh_svc			= \''.$svcID.'\'
					and		mh_seq			= \''.$seq.'\'
					and		del_flag		= \'N\'';

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->close();
				echo 'error';
				exit;
			}

			$conn->commit();

			echo $seq;
		}else if ($fromDT < $minDT){
			$toDT = $myF->dateAdd('month', -1, $minDT.'01', 'Ym');

			$conn->begin();

			//새 순번
			$sql = 'select ifnull(max(mh_seq), 0) + 1
					  from mem_hourly
					 where org_no   = \''.$code.'\'
					   and mh_jumin = \''.$jumin.'\'
					   and mh_svc   = \''.$svcID.'\'';

			$seq = $conn->get_data($sql);


			//새 일정 등록
			$sql = 'insert into mem_hourly (
					 org_no
					,mh_jumin
					,mh_svc
					,mh_seq
					,mh_kind
					,mh_type
					,mh_hourly
					,mh_vary_hourly_1
					,mh_vary_hourly_2
					,mh_vary_hourly_3
					,mh_vary_hourly_4
					,mh_vary_hourly_5
					,mh_vary_hourly_6
					,mh_vary_hourly_7
					,mh_vary_hourly_8
					,mh_vary_hourly_9
					,mh_hourly_rate
					,mh_fixed_pay
					,mh_daily_pay1
					,mh_daily_pay2
					,mh_daily_pay3
					,mh_extra_yn
					,mh_from_dt
					,mh_to_dt
					,insert_id
					,insert_dt) values (
					 \''.$code.'\'
					,\''.$jumin.'\'
					,\''.$svcID.'\'
					,\''.$seq .'\'
					,\''.$kind.'\'
					,\''.$type.'\'
					,\''.$hourly.'\'
					,\''.$varyHourly[1].'\'
					,\''.$varyHourly[2].'\'
					,\''.$varyHourly[3].'\'
					,\''.$varyHourly[4].'\'
					,\''.$varyHourly[5].'\'
					,\''.$varyHourly[6].'\'
					,\''.$varyHourly[7].'\'
					,\''.$varyHourly[8].'\'
					,\''.$varyHourly[9].'\'
					,\''.$hourlyRate.'.'.$hourlyRateSub.'\'
					,\''.$fixedPay.'\'
					,\''.$dailyPay[1].'\'
					,\''.$dailyPay[2].'\'
					,\''.$dailyPay[3].'\'
					,\''.$extraYN.'\'
					,\''.$fromDT.'\'
					,\''.$toDT.'\'
					,\''.$_SESSION['userCode'].'\'
					,now())';

			if (!$conn->execute($sql)){
				$conn->rollback();
				$conn->close();
				echo 'error';
				exit;
			}

			$conn->commit();

			echo $seq;
		}else{
			echo 'date';
		}
	}


	include_once('../inc/_db_close.php');
?>
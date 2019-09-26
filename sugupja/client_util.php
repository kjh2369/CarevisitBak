<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	//include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo $myF->header_script();


	/*********************************************************
		직원이력
	*********************************************************/
	/*
	$sql = 'insert into mem_his
			select org_no
			,      jumin
			,      seq
			,      join_dt
			,      case when replace(replace(ifnull(quit_dt,\'\'),\'-\',\'\'), \'0\', \'\') != \'\' then quit_dt else null end
			,      leave_from
			,      leave_to
			,      com_no
			,      mem_id
			,      employ_type
			,      employ_stat
			,      weekly
			,      bank_acct
			,      bank_no
			,      bank_nm
			,      prolong_rate
			,      holiday_rate_gbn
			,      holiday_rate
			,      ins_yn
			,      annuity_yn
			,      health_yn
			,      sanje_yn
			,      employ_yn
			,      paye_yn
			,      annuity_amt
			  from (
				   select m02_ccode as org_no
				   ,      min(m02_mkind) as kind
				   ,      m02_yjumin as jumin
				   ,      1 as seq
				   ,      m02_yipsail as join_dt
				   ,      m02_ytoisail as quit_dt
				   ,      null as leave_from
				   ,      null as leave_to
				   ,      m02_mem_no as com_no
				   ,      null as mem_id
				   ,      m02_ygoyong_kind as employ_type
				   ,      m02_ygoyong_stat as employ_stat
				   ,      m02_weekly_holiday as weekly
				   ,      m02_ybank_holder as bank_acct
				   ,      m02_ygyeoja_no as bank_no
				   ,      m02_ybank_name as bank_nm
				   ,      m02_add_payrate as prolong_rate
				   ,      m02_holiday_payrate_yn as holiday_rate_gbn
				   ,      m02_holiday_payrate as holiday_rate
				   ,      m02_y4bohum_umu as ins_yn
				   ,      m02_ykmbohum_umu as annuity_yn
				   ,      m02_ygnbohum_umu as health_yn
				   ,      m02_ysnbohum_umu as sanje_yn
				   ,      m02_ygobohum_umu as employ_yn
				   ,      m02_paye_yn as paye_yn
				   ,      m02_ykuksin_mpay as annuity_amt
					 from m02yoyangsa
					group by m02_ccode, m02_yjumin
				   ) as t
			 where org_no = \'1234\'';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	*/


	/*********************************************************
		고객계약이력
	*********************************************************/
	/*
	echo '고객계약이력 시작<br>';
	$sql = 'insert into client_his_svc (org_no,jumin,svc_cd,seq,from_dt,to_dt,svc_stat,svc_reason)
			select m03_ccode
			,      m03_jumin
			,      m03_mkind
			,      1
			,      m03_gaeyak_fm
			,      m03_gaeyak_to
			,      m03_sugup_status
			,      m03_stop_reason
			  from m03sugupja
			 where m03_ccode != \'1234\'
			   and m03_del_yn = \'N\'
			 order by m03_jumin, m03_mkind ';
	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 'error<br>';
		 exit;
	}

	$conn->commit();
	echo '고객계약이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/


	/*********************************************************
		고객장기요양보험이력
	*********************************************************/
	/*
	echo '고객장기요양보험이력 시작<br>';
	$sql = "insert into client_his_lvl (org_no, jumin, svc_cd, seq, from_dt, to_dt, app_no, level)
			select m03_ccode
			,      m03_jumin
			,      '0'
			,      '1'
			,      case when m03_injung_from != '' then m03_injung_from else m03_sdate end
			,      case when m03_injung_to != '' then m03_injung_to else case when m03_edate = '99999999' then '99991231' else m03_edate end end
			,      m03_injung_no
			,      m03_ylvl
			  from m03sugupja
			 where m03_mkind  = '0'
			   and m03_del_yn = 'N'
			   and m03_ylvl  != ''
			   and m03_ccode != '1234'";
	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 'error<br>';
		 exit;
	}

	$conn->commit();
	echo '고객장기요양보험이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/


	/*********************************************************
		고객구분이력
	*********************************************************/
	/*
	$sql = 'select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      kind
			,      rate
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = \'99999999\' then \'99991231\' else m03_edate end as t_dt
				   ,      m03_skind as kind
				   ,      m03_bonin_yul as rate
					 from m03sugupja
					where m03_mkind  = \'0\'
					  and m03_del_yn = \'N\'
					  and m03_ccode != \'1234\'
					union all
				   select m31_ccode
				   ,      m31_jumin
				   ,      1
				   ,      m31_sdate
				   ,      case when m31_edate = \'99999999\' then \'99991231\' else m31_edate end
				   ,      m31_kind
				   ,      m31_bonin_yul
					 from m31sugupja
					where m31_mkind  = \'0\'
					  and m31_ccode != \'1234\'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, kind, rate
			 order by cd, jumin, f_dt, t_dt';

	echo '고객구분이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_kind (org_no,jumin,seq,from_dt,to_dt,kind,rate) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\''.$seq.'\'
				,\''.$row['f_dt'].'\'
				,\''.$row['t_dt'].'\'
				,\''.$row['kind'].'\'
				,\''.$row['rate'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 echo $conn->error_msg.'<br>';
			 echo nl2br($conn->error_query);
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '고객구분이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/


	/*********************************************************
		가사간병이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      val
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      case m03_ylvl when '2' then '2' else '1' end as val
					 from m03sugupja
					where m03_mkind  = '1'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					union all
				   select m31_ccode
				   ,      m31_jumin
				   ,      1
				   ,      m31_sdate
				   ,      case when m31_edate = '99999999' then '99991231' else m31_edate end
				   ,      case m31_level when '2' then '2' else '1' end
					 from m31sugupja
					where m31_mkind  = '1'
					  and m31_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, val
			 order by cd, jumin, f_dt, t_dt";

	echo '가사간병이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_nurse (org_no,jumin,seq,from_dt,to_dt,svc_val) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\''.$seq.'\'
				,\''.$row['f_dt'].'\'
				,\''.$row['t_dt'].'\'
				,\''.$row['val'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '가사간병이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/

	/*********************************************************
		가사간병 소득등급 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      lvl
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_skind as lvl
					 from m03sugupja
					where m03_mkind  = '1'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					union all
				   select m31_ccode
				   ,      m31_jumin
				   ,      1
				   ,      m31_sdate
				   ,      case when m31_edate = '99999999' then '99991231' else m31_edate end
				   ,      m31_kind
					 from m31sugupja
					where m31_mkind  = '1'
					  and m31_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, lvl
			 order by cd, jumin, f_dt, t_dt";
	echo '가사간병 소득등급이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_lvl (org_no,jumin,svc_cd,seq,from_dt,to_dt,level) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\'1\'
				,\''.$seq.'\'
				,\''.$row['f_dt'].'\'
				,\''.$row['t_dt'].'\'
				,\''.$row['lvl'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '가사간병 소득등급이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/



	/*********************************************************
		노인돌봄 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      case val when 'V' then '1' else '2' end as val
			,      tm
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_vlvl as val
				   ,      m03_ylvl as tm
					 from m03sugupja
					where m03_mkind  = '2'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					union all
				   select m31_ccode
				   ,      m31_jumin
				   ,      1
				   ,      m31_sdate
				   ,      case when m31_edate = '99999999' then '99991231' else m31_edate end
				   ,      m31_vlvl
				   ,      m31_level
					 from m31sugupja
					where m31_mkind  = '2'
					  and m31_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, val, tm
			 order by cd, jumin, f_dt, t_dt";

	echo '노인돌봄 이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$tmpKey = '';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_old (org_no,jumin,seq,from_dt,to_dt,svc_val,svc_tm) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\''.$seq.'\'
				,\''.$row['f_dt'].'\'
				,\''.$row['t_dt'].'\'
				,\''.$row['val'].'\'
				,\''.$row['tm'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '노인돌봄 이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/

	/*********************************************************
		노인돌봄 소득등급 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      lvl
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_skind as lvl
					 from m03sugupja
					where m03_mkind  = '2'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					union all
				   select m31_ccode
				   ,      m31_jumin
				   ,      1
				   ,      m31_sdate
				   ,      case when m31_edate = '99999999' then '99991231' else m31_edate end
				   ,      m31_kind
					 from m31sugupja
					where m31_mkind  = '2'
					  and m31_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, lvl
			 order by cd, jumin, f_dt, t_dt";
	echo '노인돌봄 소득등급이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$tmpKey = '';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_lvl (org_no,jumin,svc_cd,seq,from_dt,to_dt,level) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\'2\'
				,\''.$seq.'\'
				,\''.$row['f_dt'].'\'
				,\''.$row['t_dt'].'\'
				,\''.$row['lvl'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '노인돌봄 소득등급이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/






	/*********************************************************
		산모신생아 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      val
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_vlvl as val
					 from m03sugupja
					where m03_mkind  = '3'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					union all
				   select m31_ccode
				   ,      m31_jumin
				   ,      1
				   ,      m31_sdate
				   ,      case when m31_edate = '99999999' then '99991231' else m31_edate end
				   ,      m31_vlvl
					 from m31sugupja
					where m31_mkind  = '3'
					  and m31_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, val
			 order by cd, jumin, f_dt, t_dt";

	echo '산모신생아 이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$tmpKey = '';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_baby (org_no,jumin,seq,from_dt,to_dt,svc_val) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\''.$seq.'\'
				,\''.$row['f_dt'].'\'
				,\''.$row['t_dt'].'\'
				,\''.$row['val'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '산모신생아 이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/


	/*********************************************************
		산모신생아 소득등급 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      lvl
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_skind as lvl
					 from m03sugupja
					where m03_mkind  = '3'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					union all
				   select m31_ccode
				   ,      m31_jumin
				   ,      1
				   ,      m31_sdate
				   ,      case when m31_edate = '99999999' then '99991231' else m31_edate end
				   ,      m31_kind
					 from m31sugupja
					where m31_mkind  = '3'
					  and m31_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, lvl
			 order by cd, jumin, f_dt, t_dt";
	echo '산모신생아 소득등급이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$tmpKey = '';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_lvl (org_no,jumin,svc_cd,seq,from_dt,to_dt,level) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\'3\'
				,\''.$seq.'\'
				,\''.$row['f_dt'].'\'
				,\''.$row['t_dt'].'\'
				,\''.$row['lvl'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '산모신생아 소득등급이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/






	/*********************************************************
		장애인활동지원 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      case val when 'A' then '1'
			                when 'C' then '2' else '' end as val
			,      lvl
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_vlvl as val
				   ,      m03_ylvl as lvl
					 from m03sugupja
					where m03_mkind  = '4'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					union all
				   select m31_ccode
				   ,      m31_jumin
				   ,      1
				   ,      m31_sdate
				   ,      case when m31_edate = '99999999' then '99991231' else m31_edate end
				   ,      m31_vlvl
				   ,      m31_level
					 from m31sugupja
					where m31_mkind  = '4'
					  and m31_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, val, lvl
			 order by cd, jumin, f_dt, t_dt";

	echo '장애인활동지원 이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$tmpKey = '';
	$seq = 0;

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_dis (org_no,jumin,seq,from_dt,to_dt,svc_val,svc_lvl) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\''.$seq.'\'
				,\''.$row['f_dt'].'\'
				,\''.$row['t_dt'].'\'
				,\''.$row['val'].'\'
				,\''.$row['lvl'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 echo $conn->error_msg.'<br>';
			 echo nl2br($conn->error_query);
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '장애인활동지원 이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/

	/*********************************************************
		장애인활동지원 소득등급 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      lvl
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_skind as lvl
					 from m03sugupja
					where m03_mkind  = '4'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					union all
				   select m31_ccode
				   ,      m31_jumin
				   ,      1
				   ,      m31_sdate
				   ,      case when m31_edate = '99999999' then '99991231' else m31_edate end
				   ,      m31_kind
					 from m31sugupja
					where m31_mkind  = '4'
					  and m31_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, lvl
			 order by cd, jumin, f_dt, t_dt";
	echo '장애인활동지원 소득등급이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$tmpKey = '';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_lvl (org_no,jumin,svc_cd,seq,from_dt,to_dt,level) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\'4\'
				,\''.$seq.'\'
				,\''.$row['f_dt'].'\'
				,\''.$row['t_dt'].'\'
				,\''.$row['lvl'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 echo $conn->error_msg.'<br>';
			 echo nl2br($conn->error_query);
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '장애인활동지원 소득등급이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/



	/*********************************************************
		산모유료 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      val
			,      amt
			,      cnt
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_vlvl as val
				   ,      m03_kupyeo_1 as amt
				   ,      m03_baby_svc_cnt as cnt
				     from m03sugupja
					where m03_mkind  = 'A'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, val
			 order by cd, jumin, f_dt, t_dt";

	echo '산모유료 이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$tmpKey = '';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_other (org_no,jumin,svc_cd,seq,svc_val,svc_cost,svc_cnt) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\'A\'
				,\'1\'
				,\''.$row['val'].'\'
				,\''.$row['amt'].'\'
				,\''.$row['cnt'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 echo $conn->error_msg.'<br>';
			 echo nl2br($conn->error_query);
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '산모유료 이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/



	/*********************************************************
		 병원간병 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      amt
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_kupyeo_1 as amt
				     from m03sugupja
					where m03_mkind  = 'B'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, val
			 order by cd, jumin, f_dt, t_dt";

	echo '병원간병 이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$tmpKey = '';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_other (org_no,jumin,svc_cd,seq,svc_cost) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\'B\'
				,\'1\'
				,\''.$row['amt'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 echo $conn->error_msg.'<br>';
			 echo nl2br($conn->error_query);
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '병원간병 이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	exit;
	*/



	/*********************************************************
		 기타유료 이력
	*********************************************************/
	/*
	$sql = "select cd
			,      jumin
			,      seq
			,      min(f_dt) as f_dt
			,      max(t_dt) as t_dt
			,      amt
			  from (
				   select m03_ccode as cd
				   ,      m03_jumin as jumin
				   ,      1 as seq
				   ,      m03_sdate as f_dt
				   ,      case when m03_edate = '99999999' then '99991231' else m03_edate end as t_dt
				   ,      m03_kupyeo_1 as amt
				     from m03sugupja
					where m03_mkind  = 'C'
					  and m03_del_yn = 'N'
					  and m03_ccode != '1234'
					order by cd, jumin, f_dt
				   ) as t
			 group by cd, jumin, val
			 order by cd, jumin, f_dt, t_dt";

	echo '기타유료 이력 시작<br>';

	$conn->begin();
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$tmpKey = '';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['cd'].'_'.$row['jumin']){
			$tmpKey  = $row['cd'].'_'.$row['jumin'];
			$seq = 0;
		}

		$seq ++;

		$sql = 'insert into client_his_other (org_no,jumin,svc_cd,seq,svc_cost) values (
				 \''.$row['cd'].'\'
				,\''.$row['jumin'].'\'
				,\'C\'
				,\'1\'
				,\''.$row['amt'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'error<br>';
			 echo $conn->error_msg.'<br>';
			 echo nl2br($conn->error_query);
			 exit;
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '기타유료 이력 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	*/



	/*
	echo '장기요양보험 이력갱신 시작<br>';
	$conn->begin();
	$todday = date('Y-m-d');

	$sql = 'select org_no
			,      jumin
			,      seq
			,      from_dt
			,      to_dt
			,      app_no
			,      level
			  from client_his_lvl
			 where svc_cd = \'0\'
			   and to_dt < \''.$todday.'\'
			   and org_no != \'1234\'
			 order by org_no, jumin, seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$code   = $row['org_no'];
		$jumin  = $row['jumin'];
		$seq    = $row['seq'];
		$fromDt = $row['from_dt'];
		$toDt   = $row['to_dt'];
		$appNo  = $row['app_no'];
		$lvl    = $row['level'];

		while(1){
			$seq ++;
			$sDt = $myF->dateAdd('day', 1, $toDt, 'Y-m-d');
			$eDt = $myF->dateAdd('day', -1, $myF->dateAdd('year', 1, $sDt, 'Y-m-d'), 'Y-m-d');

			$sql = 'insert into client_his_lvl (org_no,jumin,svc_cd,seq,from_dt,to_dt,app_no,level) values (
					 \''.$code.'\'
					,\''.$jumin.'\'
					,\'0\'
					,\''.$seq.'\'
					,\''.$sDt.'\'
					,\''.$eDt.'\'
					,\''.$appNo.'\'
					,\''.$lvl.'\')';
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 'error<br>';
				 echo $conn->error_msg.'<br>';
				 echo nl2br($conn->error_query);
				 exit;
			}

			echo $code.'/'.$jumin.'/'.$seq.'/'.$sDt.'/'.$eDt.'<br>';

			$toDt = $eDt;

			if ($eDt > $todday){
				break;
			}
		}
	}

	$conn->row_free();
	$conn->commit();

	echo '장기요양보험 이력갱신 종료<br>';
	echo '<br>---------------------------------------------------------------------------------------<br>';
	*/

	include_once('../inc/_db_close.php');
?>
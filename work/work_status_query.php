<?
	if ($_SESSION['userStmar'] == 'Y'){
		$member = $_SESSION['userSSN'];
	}else{
		$member = 'all';
	}

	if (Empty($lsVal1)) $lsVal1 = '일반';
	if (Empty($lsVal2)) $lsVal2 = '등급';

	$sql = 'select mst.jumin as code
			,      mst.name
			,      case mst.svc_cd when \'0\' then case mst.lvl1 when \'9\' then \''.$lsVal1.'\' else concat(mst.lvl1,\''.$lsVal2.'\') end
								   when \'4\' then concat(mst.lvl2,\''.$lsVal2.'\') else \'\' end as lvl
			,      mst.addr
			,      mst.tel
			,      iljung.svc_cd as svc_code
			,      iljung.svc_cd as svc_type
			,      max(iljung.weekly) as weeks
			,      sum(iljung.weekly) as cnt
			,      iljung.soyotime
			,      iljung.mem_m
			,      iljung.mem_s
			,	   iljung.mem_tel
			,      iljung.from_time
			,      iljung.to_time
			  from ( ';

	if ($_SESSION['userLevel'] == 'P' &&
		$_SESSION['userSmart'] == 'Y'){
		$sql .= 'select t01_mkind as kind
				 ,      t01_jumin as jumin
				 ,      t01_svc_subcode as svc_cd
				 ,      count(week(date_format(t01_sugup_date, \'%Y-%m-%d\'))) as weekly
				 ,      t01_sugup_soyotime as soyotime
				 ,      t01_yname1 as mem_m
				 ,      t01_yname2 as mem_s
				 ,      t01_sugup_fmtime as from_time
				 ,      t01_sugup_totime as to_time
				   from t01iljung
				  where t01_ccode  = \''.$code.'\'
					and t01_del_yn = \'N\'
					AND t01_yoyangsa_id1       = \''.$_SESSION['userSSN'].'\'
					and left(t01_sugup_date,6) = \''.$year.$month.'\'
				  group by t01_mkind, t01_jumin, t01_svc_subcode, t01_sugup_soyotime, t01_yname1, t01_yname2, t01_sugup_fmtime, t01_sugup_totime
				  UNION ALL
				 select t01_mkind as kind
				 ,      t01_jumin as jumin
				 ,      t01_svc_subcode as svc_cd
				 ,      count(week(date_format(t01_sugup_date, \'%Y-%m-%d\'))) as weekly
				 ,      t01_sugup_soyotime as soyotime
				 ,      t01_yname1 as mem_m
				 ,      t01_yname2 as mem_s
				 ,      t01_sugup_fmtime as from_time
				 ,      t01_sugup_totime as to_time
				   from t01iljung
				  where t01_ccode  = \''.$code.'\'
					AND t01_mkind  = \'0\'
					and t01_del_yn = \'N\'
					AND t01_yoyangsa_id2       = \''.$_SESSION['userSSN'].'\'
					and left(t01_sugup_date,6) = \''.$year.$month.'\'
				  group by t01_mkind, t01_jumin, t01_svc_subcode, t01_sugup_soyotime, t01_yname1, t01_yname2, t01_sugup_fmtime, t01_sugup_totime
				  UNION ALL
				 select t01_mkind as kind
				 ,      t01_jumin as jumin
				 ,      t01_svc_subcode as svc_cd
				 ,      count(week(date_format(t01_sugup_date, \'%Y-%m-%d\'))) as weekly
				 ,      t01_sugup_soyotime as soyotime
				 ,      t01_yname1 as mem_m
				 ,      t01_yname2 as mem_s
				 ,      t01_sugup_fmtime as from_time
				 ,      t01_sugup_totime as to_time
				   from t01iljung
				  where t01_ccode  = \''.$code.'\'
					AND t01_mkind  = \'4\'
					and t01_del_yn = \'N\'
					AND t01_yoyangsa_id2       = \''.$_SESSION['userSSN'].'\'
					and left(t01_sugup_date,6) = \''.$year.$month.'\'
				  group by t01_mkind, t01_jumin, t01_svc_subcode, t01_sugup_soyotime, t01_yname1, t01_yname2, t01_sugup_fmtime, t01_sugup_totime';
	}else{
		$sql .= 'select t01_mkind as kind
				 ,      t01_jumin as jumin
				 ,      t01_svc_subcode as svc_cd
				 ,      count(week(date_format(t01_sugup_date, \'%Y-%m-%d\'))) as weekly
				 ,      t01_sugup_soyotime as soyotime
				 ,      t01_yname1 as mem_m
				 ,      t01_yname2 as mem_s
				 ,	    yoy.m02_ytel as mem_tel
				 ,      t01_sugup_fmtime as from_time
				 ,      t01_sugup_totime as to_time
				   from t01iljung
				  inner join ( select distinct m02_yjumin, m02_ytel
							     from m02yoyangsa
								where m02_ccode = \''.$code.'\' ) as yoy
				     on m02_yjumin = t01_yoyangsa_id1
				  where t01_ccode  = \''.$code.'\'
					and t01_del_yn = \'N\'
					and left(t01_sugup_date,6) = \''.$year.$month.'\'
				  group by t01_mkind, t01_jumin, t01_svc_subcode, t01_sugup_soyotime, t01_yname1, t01_yname2, t01_sugup_fmtime, t01_sugup_totime';
	}

	$sql .= '	   ) as iljung
			 inner join (
				   select mst.jumin as jumin
				   ,      mst.name as name
				   ,      mst.addr as addr
				   ,      mst.tel as tel
				   ,      svc.svc_cd as svc_cd
				   ,      lvl.level as lvl1
				   ,      dis.svc_lvl as lvl2
					 from (
						  select min(m03_mkind) as kind
						  ,      m03_jumin as jumin
						  ,      m03_name as name
						  ,      concat(m03_juso1,\' \',m03_juso2) as addr
						  ,      case when m03_tel != \'\' then m03_tel else m03_hp end as tel
							from m03sugupja
						   where m03_ccode = \''.$code.'\'
						   group by m03_jumin
						  ) as mst
					inner join (
						  select jumin
						  ,      svc_cd
						  ,      seq
							from client_his_svc
						   where org_no = \''.$code.'\'
							 and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							 and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
						  ) as svc
					   on svc.jumin = mst.jumin
					 left join (
						  select jumin
						  ,      svc_cd
						  ,      seq
						  ,      app_no
						  ,      level
							from client_his_lvl
						   where org_no = \''.$code.'\'
							 and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							 and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
						  ) as lvl
					   on lvl.jumin = svc.jumin
					 left join (
						  select jumin
						  ,      seq
						  ,      svc_val
						  ,      svc_lvl
							from client_his_dis
						   where org_no = \''.$code.'\'
							 and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
							 and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
						  ) as dis
					   on dis.jumin = svc.jumin
					order by mst.name, mst.jumin, svc.svc_cd
				   ) as mst
				on mst.jumin = iljung.jumin
			   and mst.svc_cd = iljung.kind
			 group by mst.jumin, mst.name, mst.svc_cd, mst.addr, mst.tel, iljung.svc_cd, iljung.soyotime, iljung.mem_m, iljung.mem_s, iljung.from_time, iljung.to_time
			 order by name, svc_code, weeks';

	#if($debug) echo nl2br($sql); exit;

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if ($tmp_code != $row['code']){
			$tmp_code  = $row['code'];

			$i1 = sizeof($data);
			$i2 = 0;
			$i3 = 0;

			$data[$i1]['client'] = array('cd'=>$row['code'], 'nm'=>$row['name'], 'cnt'=>0);
		}

		if ($tmp_level != $tmp_code.$row['lvl']){
			$tmp_level  = $tmp_code.$row['lvl'];

			$data[$i1]['dtl'][$i2] = array('lvl'=>$row['lvl'], 'addr'=>$row['addr'], 'tel'=>$myF->phoneStyle($row['tel']), 'cnt'=>0);

			$i2 ++;
			$i3 = 0;
		}

		if ($tmp_svc != $tmp_code.$tmp_level.$row['svc_code']){
			$tmp_svc  = $tmp_code.$tmp_level.$row['svc_code'];

			$data[$i1]['dtl'][$i2-1]['svc'][$i3] = array('cd'=>$row['svc_code'], 'nm'=>$conn->kind_name_svc($row['svc_type']), 'cnt'=>0);

			$i3 ++;
			$i4 = 0;
		}

		if ($tmp_work != $tmp_code.$tmp_level.$tmp_svc.$row['weeks'].$row['soyotime'].$row['mem_m'].$row['mem_s']){
			$tmp_work  = $tmp_code.$tmp_level.$tmp_svc.$row['weeks'].$row['soyotime'].$row['mem_m'].$row['mem_s'];

			$data[$i1]['dtl'][$i2-1]['svc'][$i3-1][$i4] = array('weeks'=>$row['weeks'],'soyotime'=>$row['soyotime'],'mem_m'=>$row['mem_m'],'mem_s'=>$row['mem_s'],'mem_tel'=>$myF->phoneStyle($row['mem_tel']),'cnt'=>0);

			$i4 ++;
			$i5 = 0;
		}else{
			$i5 ++;
		}

		$data[$i1]['dtl'][$i2-1]['svc'][$i3-1][$i4-1][$i5] = array('from_time'=>$myF->timeStyle($row['from_time']),'to_time'=>$myF->timeStyle($row['to_time']),'cnt'=>0);
		$data[$i1]['dtl'][$i2-1]['svc'][$i3-1][$i4-1][$i5]['cnt'] += $row['cnt'];

		$data[$i1]['client']['cnt'] ++;
		$data[$i1]['dtl'][$i2-1]['cnt'] ++;
		$data[$i1]['dtl'][$i2-1]['svc'][$i3-1]['cnt'] ++;
		$data[$i1]['dtl'][$i2-1]['svc'][$i3-1][$i4-1]['cnt'] ++;

		if ($data[$i1]['dtl'][$i2-1]['svc'][$i3-1][$i4-1]['cnt'] > 1){
			$data[$i1]['client']['cnt'] = $data[$i1]['client']['cnt'] - 1;
			$data[$i1]['dtl'][$i2-1]['cnt'] = $data[$i1]['dtl'][$i2-1]['cnt'] - 1;
			$data[$i1]['dtl'][$i2-1]['svc'][$i3-1]['cnt'] = $data[$i1]['dtl'][$i2-1]['svc'][$i3-1]['cnt'] - 1;
		}
	}

	$conn->row_free();
?>
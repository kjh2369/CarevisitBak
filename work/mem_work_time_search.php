<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);

	
	$sql = 'select m02_yjumin
			,	   m02_yipsail
			  from m02yoyangsa
			 where m02_ccode = \''.$orgNo.'\'';
	$startDt = $conn->_fetch_array($sql, 'm02_yjumin');
		

	$sql = 'select name
			,      jumin
			,      work_mon
			,      sum(200_conf_time) as conf_200
			,      sum(500_conf_time) as conf_500
			,      sum(800_conf_time) as conf_800
			from (
			select t01_mem_nm1 as name
			,      t01_mem_cd1 as jumin
			,      COUNT(DISTINCT left(t01_conf_date, 6)) as work_mon
			,      SUM(floor((case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' then t01_conf_soyotime else 0 end -
							case when left(t01_conf_date,6) >= \'201603\' then
									  case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 480 then 0
										   when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end
								 else case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end end) - (case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' then t01_conf_soyotime else 0 end -
							case when left(t01_conf_date,6) >= \'201603\' then
									  case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 480 then 0
										   when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end
								 else case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end end) % 30)) as 200_conf_time
			,      sum(floor(case when t01_svc_subcode = \'500\' then t01_conf_soyotime else 0 end - (case when t01_svc_subcode = \'500\' then t01_conf_soyotime else 0 end % 30))) as 500_conf_time
			,      sum(case when t01_svc_subcode = \'800\' then t01_conf_soyotime else 0 end) as 800_conf_time
			from t01iljung
			where t01_ccode = \''.$orgNo.'\'
			and t01_del_yn = \'N\'
			and t01_mem_cd1 != \'\'
			and left(t01_conf_date,6) BETWEEN  \''.$fromDt.'\' and \''.$toDt.'\'
			group by t01_mem_cd1 
			UNION all
			select t01_mem_nm2 as name
			,      t01_mem_cd2 as jumin
			,      COUNT(DISTINCT left(t01_conf_date, 6)) as work_mon
			,      SUM(floor((case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' then t01_conf_soyotime else 0 end -
							case when left(t01_conf_date,6) >= \'201603\' then
									  case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 480 then 0
										   when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end
								 else case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end end) - (case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' then t01_conf_soyotime else 0 end -
							case when left(t01_conf_date,6) >= \'201603\' then
									  case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 480 then 0
										   when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end
								 else case when t01_mkind = \'0\' and t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then 30 else 0 end end) % 30)) as 200_conf_time
			,      sum(floor(case when t01_mkind = \'0\' and t01_svc_subcode = \'500\' then t01_conf_soyotime else 0 end - (case when t01_svc_subcode = \'500\' then t01_conf_soyotime else 0 end % 30))) as 500_conf_time
			,      sum(case when t01_svc_subcode = \'800\' then t01_conf_soyotime else 0 end) as 800_conf_time
			from t01iljung
			where t01_ccode = \''.$orgNo.'\'
			and t01_del_yn = \'N\'
			and t01_mem_cd2 != \'\'
			and left(t01_conf_date,6) BETWEEN  \''.$fromDt.'\' and \''.$toDt.'\'
			and t01_svc_subcode = 500
			group by t01_mem_cd2
			) as t
			group by jumin
			order by name';
	
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		if($row['conf_200'] == 0 && $row['conf_500'] == 0 && $row['conf_800'] == 0){
		}else { ?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td class="left"><?=$row['name'];?></td>
				<td class="center"><?=$myF->issToBirthday($row['jumin'], '.');?></td>
				<td class="center"><?=$myF->dateStyle($startDt[$row['jumin']]['m02_yipsail'], '.');?></td>
				<td class="right"><?=$row['work_mon'];?> 개월</td>
				<td class="right"><?=$row['conf_200'] != '0' ? number_format($row['conf_200']/ 60, 1) : '';?></td>
				<td class="right"><?=$row['conf_500'] != '0' ? number_format($row['conf_500']/ 60, 1) : '';?></td>
				<td class="right"><?=$row['conf_800'] != '0' ? number_format($row['conf_800']/ 60, 1) : '';?></td>
				<td class="center last"></td>
			</tr><?
		}

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
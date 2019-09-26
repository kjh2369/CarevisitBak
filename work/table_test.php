<?
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$sql = "select distinct idx, serviceCode, serviceName, yoyangsa, sugupja";

	for($i=1; $i<=31; $i++){
		$sql .= ", sum(dt".($i<10?'0':'').$i.") as dt".($i<10?'0':'').$i;
	}

	$sql .= ", sum(";

	for($i=1; $i<=31; $i++){
		if ($i > 1) $sql .= " + ";
		$sql .= " dt".($i<10?'0':'').$i;
	}

	$sql .= ") as tot_dt";

	$sql .="
			,      fromTime, toTime, soyoTime
			  from (";

	for($i=1; $i<=31; $i++){
		if ($i > 1) $sql .= " union all <br>";
		$sql .= "select '1' as idx <br>
				,      t01_svc_subcode as serviceCode <br>
				,      case t01_svc_subcode when '200' then '요양' when '500' then '목욕' when '800' then '간호' else '-' end as serviceName <br>
				,      concat(t01_yname1, case when t01_yname2 != '' then concat('/', t01_yname2) else '' end) as yoyangsa <br>
				,      m03_name as sugupja <br>";

		for($j=1; $j<$i; $j++){
			$sql .= "
					,      0 as dt".($j<10?'0':'').$j." <br>";
		}


		$sql .= "
				,      1 as dt".($i<10?'0':'').$i." <br>";

		for($j=$i+1; $j<=31; $j++){
			$sql .= "
					,      0 as dt".($j<10?'0':'').$j." <br>";
		}



		$sql .= "
				,      t01_sugup_fmtime as fromTime <br>
				,      t01_sugup_totime as toTime <br>
				,      round(t01_sugup_soyotime / 60, 1) as soyoTime <br>
				  from t01iljung <br>
				 inner join m03sugupja <br>
					on m03_ccode = t01_ccode <br>
				   and m03_mkind = t01_mkind <br>
				   and m03_jumin = t01_jumin <br>
				 where t01_ccode = '1234' <br>
				   and t01_mkind = '0' <br>
				   and t01_sugup_date = '201103".($i<10?'0':'').$i."' <br>
				   and t01_del_yn = 'N' <br>";
	}

	$sql .= "
			  ) as t
			 group by idx, serviceCode, serviceName, yoyangsa, sugupja, fromTime, toTime, soyoTime";


	$sql .= " union all ";
	$sql .= "select idx, serviceCode, serviceName, yoyangsa, sugupja";

	for($i=1; $i<=31; $i++){
		$sql .= ", sum(dt".($i<10?'0':'').$i.") as dt".($i<10?'0':'').$i;
	}

	$sql .= ", sum(";

	for($i=1; $i<=31; $i++){
		if ($i > 1) $sql .= " + ";
		$sql .= " dt".($i<10?'0':'').$i;
	}

	$sql .= ") as tot_dt";

	$sql .="
			,      fromTime, toTime, soyoTime
			  from (";

	for($i=1; $i<=31; $i++){
		if ($i > 1) $sql .= " union all <br>";
		$sql .= "select '2' as idx <br>
				,      t01_svc_subcode as serviceCode <br>
				,      case t01_svc_subcode when '200' then '요양' when '500' then '목욕' when '800' then '간호' else '-' end as serviceName <br>
				,      concat(t01_yname1, case when t01_yname2 != '' then concat('/', t01_yname2) else '' end) as yoyangsa <br>
				,      m03_name as sugupja <br>";

		for($j=1; $j<$i; $j++){
			$sql .= "
					,      0 as dt".($j<10?'0':'').$j." <br>";
		}


		$sql .= "
				,      case when t01_status_gbn = '1' then 1 else 0 end as dt".($i<10?'0':'').$i." <br>";

		for($j=$i+1; $j<=31; $j++){
			$sql .= "
					,      0 as dt".($j<10?'0':'').$j." <br>";
		}



		$sql .= "
				,      t01_sugup_fmtime as fromTime <br>
				,      t01_sugup_totime as toTime <br>
				,      round(t01_sugup_soyotime / 60, 1) as soyoTime <br>
				  from t01iljung <br>
				 inner join m03sugupja <br>
					on m03_ccode = t01_ccode <br>
				   and m03_mkind = t01_mkind <br>
				   and m03_jumin = t01_jumin <br>
				 where t01_ccode = '1234' <br>
				   and t01_mkind = '0' <br>
				   and t01_sugup_date = '201103".($i<10?'0':'').$i."' <br>
				   and t01_del_yn = 'N' <br>";
	}

	$sql .= "
			  ) as t
			 group by idx, serviceCode, serviceName, yoyangsa, sugupja, fromTime, toTime, soyoTime";



	$sql .= "
			 order by serviceCode, yoyangsa, sugupja, fromTime, toTime, idx, soyoTime";

	echo $sql;
?>
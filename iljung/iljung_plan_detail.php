<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	
	echo   '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'40px\'>
					<col width=\'80px\'>
					<col width=\'80px\'>
					<col width=\'110px\'>
					<col width=\'80px\'>
					<col width=\'50px\'>
					<col width=\'50px\'>
					<col>
				</colgroup>
				<thead>
					<tr>
						<th class=\'head\' >No</th>
						<th class=\'head\' >수급자</th>
						<th class=\'head\' >서비스</th>
						<th class=\'head\' >주민번호</th>
						<th class=\'head\' >연락처</th>
						<th class=\'head\' >등급</th>
						<th class=\'head\' >방문일자</th>
						<th class=\'head last\' >비고</th>
					</tr>
				</thead>
				<tbody>';




	$sql = 'select mst.name as nm
			,	   mst.tel  as tel
			,	   mst.hp   as hp
			,      iljung.kind as center_kind
			,      iljung.jumin as ssn
			,      case iljung.kind when \'0\' then case lvl.level when \'9\' then \'일반\' else concat(lvl.level,\'등급\') end
									when \'4\' then concat(dis.svc_lvl,\'등급\') else \'\' end as lvl_nm
			,      iljung.svc_cd
			  from (
				   select t01_mkind as kind
				   ,      t01_jumin as jumin
				   ,      t01_svc_subcode as svc_cd
					 from t01iljung
					where t01_ccode       = \''.$code.'\'
					  and left(t01_sugup_date, 6) >= \''.$year.$month.'\'
					  and left(t01_sugup_date, 6) <= \''.$year.$month.'\'
					  and t01_del_yn      = \'N\'';


	$sql .= '		group by t01_jumin, t01_svc_subcode
				   ) as iljung
			 inner join (
				   select min(m03_mkind) as kind
				   ,      m03_jumin as jumin
				   ,      m03_name as name
				   ,	  m03_tel  as tel
				   ,	  m03_hp   as hp
					 from m03sugupja
					where m03_ccode = \''.$code.'\'';

	$sql .= '		group by m03_jumin, m03_name
				   ) as mst
				on mst.jumin = iljung.jumin

			  left join (
				   select jumin
				   ,      level
				   ,      svc_cd
					 from client_his_lvl
					where org_no   = \''.$code.'\'
					  and left(from_dt, 6) <= \''.$year.$month.'\'
					  and left(to_dt, 6)   >= \''.$year.$month.'\'
				   ) as lvl
				on lvl.jumin  = iljung.jumin
			   and lvl.svc_cd = iljung.kind

			  left join (
				   select jumin
				   ,      svc_val
				   ,      svc_lvl
					 from client_his_dis
					where org_no   = \''.$code.'\'
					  and left(from_dt, 6) <= \''.$year.$month.'\'
					  and left(to_dt, 6)   >= \''.$year.$month.'\'
				   ) as dis
				on dis.jumin = iljung.jumin
			 order by mst.name, iljung.kind, iljung.svc_cd';
	
	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();
	
	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		
		$sql = 'select plan
				  from iljung_plan
				 where org_no = \''.$code.'\'
				   and yymm   = \''.$year.$month.'\'
				   and jumin  = \''.$row['ssn'].'\'';
		$plan = $conn -> get_data($sql);


		echo '<tr id=\'row_cnt'.$i.'\'>';
		echo '<td class=\'center\'>'.($i+1).'</td>';
		echo '<td class=\'left\'>'.$row['nm'].'</td>';
		echo '<td class=\'left\'>'.$conn->kind_name_svc($row['svc_cd']).'</td>';
		echo '<td class=\'center\'><input type=\'hidden\' id=\'jumin'.$i.'\'  name=\'jumin'.$i.'\' value=\''.$row['ssn'].'\' style="width:40px;" maxlength=\'2\'/>'.$myF->issStyle($row['ssn']).'</td>';
		echo '<td>'.($row['tel'] != '' ? $myF->phoneStyle($row['tel']) : $myF->phoneStyle($row['hp'])).'</td>';
		echo '<td class=\'center\'>'.$row['lvl_nm'].'</td>';
		echo '<td>
				<input type=\'text\' id=\'visit_dt'.$i.'\'  name=\'visit_dt'.$i.'\' value=\''.$plan.'\' style="width:40px;" maxlength=\'2\'/>		
			  </td>';
		echo '<td class=\'last\'></td>';
		echo '</tr>';	
	}
	
	echo '</tbody>';
	echo '</table>';
	

?>
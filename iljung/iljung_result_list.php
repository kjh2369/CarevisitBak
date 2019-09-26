<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$day   = $_POST['day'];
	$limit = $_POST['limit'];
	$gubun = $_POST['gubun'];
	$today = date('d', mktime());
	$find_name		= $_REQUEST['find_name'];
	$find_type      = $_REQUEST['find_type'];


	switch($gubun){
		case 'client':
			$title = '수급자';
			break;
		case 'member':
			$title = '요양보호사';
			break;
		default:
			exit;
	}

	if ($limit == 1) $day = null;

	if (empty($year))  $year  = date('Y', mktime());
	if (empty($month)) $month = date('m', mktime());

	$lastday = $myF->lastDay($year, $month);

	if ($year.$month == date('Ym', mktime())){
		$from_time = '01';
		$limit_day = true;
	}else if ($year.$month > date('Ym', mktime())){
		$from_time = '01';
		$limit_day = true;
	}else{
		$from_time = '01';
		$limit_day = false;
	}

	if (empty($day)){
		if ($limit_day)
			$day = date('d', mktime());
		else
			$day = $lastday;
	}else{
	}

	$day = (intval($day) < 10 ? '0' : '').intval($day);

	if ($year.$month == date('Ym', mktime())){
		$to_time = $day;
	}else if ($year.$month > date('Ym', mktime())){
		$to_time = '31';
	}else{
		$to_time = $day;
	}

	if ($_SESSION['userStmar'] == 'Y'){
		$member = $_SESSION['userSSN'];
	}else{
		$member = 'all';
	}

	$init_year = $myF->year();
?>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="45px">
		<col width="70px">
		<?
			if ($gubun == 'client'){?>
				<col width="50px"><?
			}
		?>
		<col width="150px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head"><u title="<?=$title;?>명을 클릭하시면 상세내역을 보실수 있습니다."><?=$title;?></u></th>
			<?
				if ($gubun == 'client'){?>
					<th class="head">등급</th><?
				}
			?>
			<th class="head">서비스</th>
			<th class="head">계획</th>
			<th class="head">실적</th>
			<th class="head">차이</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?

		if ($gubun == 'client'){
			if ($lbTestMode){
				$sql = 'select mst.name as nm
						,      iljung.kind as center_kind
						,      iljung.jumin as ssn
						,      case iljung.kind when \'0\' then case lvl.level when \'9\' then \'일반\' else concat(lvl.level,\'등급\') end
												when \'4\' then concat(dis.svc_lvl,\'등급\') else \'\' end as lvl_name
						,      iljung.svc_cd
						,      iljung.plan_time
						,      iljung.conf_time
						  from (
							   select t01_mkind as kind
							   ,      t01_jumin as jumin
							   ,      t01_svc_subcode as svc_cd
							   ,      sum(case when t01_svc_subcode = \'500\' or t01_svc_subcode = \'800\' then 1 else t01_sugup_soyotime -
											case when t01_svc_subcode = \'200\' and t01_sugup_soyotime >= 270 then
												 case when t01_sugup_soyotime >= 480 and \''.$year.$month.'\' >= \'201603\' then 0 else 30 end
											else 0 end end) as plan_time
							   ,      sum(case when t01_status_gbn = \'1\' then case when t01_svc_subcode = \'500\' or t01_svc_subcode = \'800\' then 1 else t01_conf_soyotime -
											case when t01_svc_subcode = \'200\' and t01_conf_soyotime >= 270 then
												 case when t01_sugup_soyotime >= 480 and \''.$year.$month.'\' >= \'201603\' then 0 else 30 end
												 else 0 end end else 0 end) as conf_time
								 from t01iljung
								where t01_ccode       = \''.$code.'\'
								  and t01_sugup_date >= \''.$year.$month.$from_time.'\'
								  and t01_sugup_date <= \''.$year.$month.$to_time.'\'
								  and t01_del_yn      = \'N\'';

				if($find_type != '') $sql .= ' and t01_mkind = \''.$find_type.'\'';

				$sql .= '		group by t01_jumin, t01_svc_subcode
							   ) as iljung
						 inner join (
							   select min(m03_mkind) as kind
							   ,      m03_jumin as jumin
							   ,      m03_name as name
								 from m03sugupja
								where m03_ccode = \''.$code.'\'';

				if(!empty($find_name)) $sql .= ' and m03_name >= \''.$find_name.'\'';

				$sql .= '		group by m03_jumin, m03_name
							   ) as mst
							on mst.jumin = iljung.jumin

						  left join (
							   select jumin
							   ,      level
							   ,      svc_cd
								 from client_his_lvl
								where org_no   = \''.$code.'\'
								  and from_dt <= \''.$year.$month.$day.'\'
								  and to_dt   >= \''.$year.$month.$day.'\'
							   ) as lvl
							on lvl.jumin  = iljung.jumin
						   and lvl.svc_cd = iljung.kind

						  left join (
							   select jumin
							   ,      svc_val
							   ,      svc_lvl
							     from client_his_dis
								where org_no   = \''.$code.'\'
								  and from_dt <= \''.$year.$month.$day.'\'
								  and to_dt   >= \''.$year.$month.$day.'\'
							   ) as dis
						    on dis.jumin = iljung.jumin
						 order by mst.name, iljung.kind, iljung.svc_cd';

			}else{
				$sql = "select m03_name as nm
						,	   m03_mkind as center_kind
						,      t01_jumin as ssn
						,      LVL.m81_name as lvl_name
						,      t01_svc_subcode as svc_cd
						,      sum(case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_sugup_soyotime -
										case when t01_svc_subcode = '200' and t01_sugup_soyotime >= 270 then
											 case when t01_sugup_soyotime >= 480 and '".$year.$month."' >= \'201603\' then 0 else 30 end
											 else 0 end end) as plan_time
						,      sum(case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime -
										case when t01_svc_subcode = '200' and t01_conf_soyotime >= 270 then
											 case when t01_sugup_soyotime >= 480 and '".$year.$month."' >= \'201603\' then 0 else 30 end
											 else 0 end end else 0 end) as conf_time
						  from t01iljung
						 inner join m03sugupja
							on m03_ccode = t01_ccode
						   and m03_mkind = t01_mkind
						   and m03_jumin = t01_jumin
						  left join m81gubun as LVL
							on LVL.m81_gbn  = 'LVL'
						   and LVL.m81_code = case when m03_mkind = '0' or m03_mkind = '4' then m03_ylvl else '' end
						 where t01_ccode    = '$code'
						   and t01_sugup_date between '$year$month$from_time' and '$year$month$to_time'
						   and t01_del_yn   = 'N'";
				if($find_name != '') $sql .= " and m03_name like '%$find_name%'";
				if($find_type != '') $sql .= " and m03_mkind = '$find_type'";

				$sql .=	 "group by m03_name, t01_jumin, LVL.m81_name, t01_svc_subcode
						  order by m03_name, t01_mkind, t01_svc_subcode";
			}
		}else{
			$sql = "select center_code
					,      center_kind
					,      svc_cd
					,      member_code as ssn
					,      m02_yname as nm
					,      sum(plan_time) as plan_time
					,      sum(conf_time) as conf_time
					  from (
						   select t01_ccode as center_code
						   ,      t01_mkind as center_kind
						   ,      t01_svc_subcode as svc_cd
						   ,      t01_yoyangsa_id1 as member_code
						   ,      case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_sugup_soyotime - case when t01_svc_subcode = '200' and t01_sugup_soyotime >= 270 then case when t01_sugup_soyotime >= 480 and '".$year.$month."' >= \'201603\' then 0 else 30 end else 0 end end as plan_time
						   ,      case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime - case when t01_svc_subcode = '200' and t01_conf_soyotime >= 270 then case when t01_sugup_soyotime >= 480 and '".$year.$month."' >= \'201603\' then 0 else 30 end else 0 end end else 0 end as conf_time
							 from t01iljung
							where t01_ccode  = '$code'
							  and t01_del_yn = 'N'
							  and t01_sugup_date between '$year$month$from_time' and '$year$month$to_time'";

			if ($member != 'all') $sql .= " and t01_yoyangsa_id1 = '$member'";

			$sql .= "		union all
						   select t01_ccode as center_code
						   ,      t01_mkind as center_kind
						   ,      t01_svc_subcode as svc_cd
						   ,      t01_yoyangsa_id2 as member_code
						   ,      case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_sugup_soyotime end as plan_time
						   ,      case when t01_status_gbn = '1' then case when t01_svc_subcode = '500' or t01_svc_subcode = '800' then 1 else t01_conf_soyotime end else 0 end as conf_time
							 from t01iljung
							where t01_ccode  = '$code'
							  and t01_del_yn = 'N'
							  and t01_sugup_date between '$year$month$from_time' and '$year$month$to_time'";

			if ($member != 'all') $sql .= " and t01_yoyangsa_id2 = '$member'";

			$sql .= "	   ) as t
					 inner join m02yoyangsa
						on m02_ccode  = center_code
					   and m02_mkind  = '0'
					   and m02_yjumin = member_code";

			if($find_name != '')	 $sql .= " and m02_yname like '%$find_name%'";
			if($find_type != '')	 $sql .= " and m02_dept_cd = '".str_replace('-','',$find_type)."'";

			$sql .=	" group by center_code, center_kind, svc_cd, member_code, m02_yname
					  order by nm, center_kind, svc_cd";
		}

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if($debug){
				//echo $row['center_kind'].'///'.$row['svc_cd'].'</br>';
			}

			if ($tmp_ssn != $row['ssn']){
				if (!empty($tmp_ssn)){
					$tmp_seq ++;

					$html[$tmp_i][0] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'center\'>'.($tmp_seq).'</div></td>';
					$html[$tmp_i][1] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'left\'><a href=\'#\' onclick=\'set_detail("'.$ed->en($tmp_ssn).'",document.getElementById("day").value);\'>'.$tmp_nm.'</a></div></td>';
					$html[$tmp_i][2] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'center\'>'.$tmp_lvl.'</div></td>';
				}

				$tmp_i    = $i;
				$tmp_ssn  = $row['ssn'];
				$tmp_nm   = $row['nm'];
				$tmp_lvl  = $row['lvl_name'];
				$tmp_rows = 1;
			}else{
				$tmp_rows ++;

				$html[$i][0] = '';
				$html[$i][1] = '';
				$html[$i][2] = '';
			}

			/*********************************************************/
			/*
			 서비스에 장애활동지원 조회 시 활동지원 찍히지않아서
			 db_open.php 에  kind_name_svc2(); 추가
			 $conn->kind_name_svc2($row['center_kind'],$row['svc_cd']) 변경

			 2012.3.8 김주완 수정
			*/
			/**********************************************************/


			$html[$i][3] = '<td><div class=\'left\'>'.$conn->kind_name_svc2($row['center_kind'],$row['svc_cd']).'</div></td>';

			if ($row['svc_cd'] == '500' || $row['svc_cd'] == '800'){
				$html[$i][4] = '<td><div class=\'right\'>'.$myF->numberFormat($row['plan_time'],'회').'</div></td>';
				$html[$i][5] = '<td><div class=\'right\'>'.$myF->numberFormat($row['conf_time'],'회').'</div></td>';
				$html[$i][6] = '<td><div class=\'right\' style=\''.($row['conf_time']-$row['plan_time'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->numberFormat($row['conf_time']-$row['plan_time'],'회').'</div></td>';
			}else{
				$html[$i][4] = '<td><div class=\'right\'>'.$myF->getMinToHM($row['plan_time']).'</div></td>';
				$html[$i][5] = '<td><div class=\'right\'>'.$myF->getMinToHM($row['conf_time']).'</div></td>';
				$html[$i][6] = '<td><div class=\'right\' style=\''.($row['conf_time']-$row['plan_time'] < 0 ? 'color:#ff0000;' : '').'\'>'.$myF->getMinToHM($row['conf_time']-$row['plan_time']).'</div></td>';
			}

			$html[$i][7] = '<td class=\'last\'><div class=\'center\'></div></td>';
		}

		$tmp_seq ++;

		$html[$tmp_i][0] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'center\'>'.($tmp_seq).'</div></td>';
		$html[$tmp_i][1] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'left\'><a href=\'#\' onclick=\'set_detail("'.$ed->en($tmp_ssn).'",document.getElementById("day").value);\'>'.$tmp_nm.'</a></div></td>';
		$html[$tmp_i][2] = '<td rowspan=\''.$tmp_rows.'\'><div class=\'center\'>'.$row['lvl_name'].'</div></td>';

		$conn->row_free();

		$html_cnt = sizeof($html);

		for($i=0; $i<$html_cnt; $i++){
			echo '<tr>';

			if (!empty($html[$i][0])) echo $html[$i][0];
			if (!empty($html[$i][1])) echo $html[$i][1];

			if ($gubun == 'client'){
				if (!empty($html[$i][2])) echo $html[$i][2];
			}

			echo $html[$i][3];
			echo $html[$i][4];
			echo $html[$i][5];
			echo $html[$i][6];
			echo $html[$i][7];

			echo '</tr>';
		}
	?>
	</tbody>
	<tfoot>
		<tr>
		<?
			if (empty($row_count)){
				echo '<td class=\'center last\' colspan=\'8\'>'.$myF->message('nodata','N').'</td>';
			}else{
				echo '<td class=\'left bottom last\' colspan=\'8\'>수급자 : '.$tmp_seq.'명 / 서비스 : '.$html_cnt.'건</td>';
			}
		?>
		</tr>
	</tfoot>
</table>

<?
	include_once('../inc/_db_close.php');
?>
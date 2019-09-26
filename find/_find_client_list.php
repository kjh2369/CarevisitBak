<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = ($_POST['code'] != '' ? $_POST['code'] : $_SESSION['userCenterCode']);
	$kind  = $_POST['kind'];
	$name  = $_POST['name'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$SR = $_POST['SR'];

	if ($SR){
		//대상자 주민번호
		$sql = 'SELECT	code
				,		jumin
				,		name
				,		cd_key
				FROM	mst_jumin
				WHERE	org_no	= \''.$code.'\'
				AND		gbn		= \'1\'';

		$tgJumin = $conn->_fetch_array($sql,'code');
	}

	if (empty($year) && empty($month)){
		$year  = date('Y');
		$month = date('m');
	}

	$month = (intval($month) < 10 ? '0' : '').intval($month);

	$html = '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'40px\'>
					<col width=\'70px\'>
					<col width=\'90px\'>
					<col width=\'90px\'>
					<col width=\'90px\'>
					<col>
				</colgroup>
				<tbody>';

	$sql = 'select mst.jumin as cd
			,      mst.name as nm
			,      case svc.svc_cd when \'0\' then lvl.level
								   when \'4\' then dis.svc_lvl else \'\' end lvl_cd
			,      case svc.svc_cd when \'0\' then case lvl.level when \'9\' then \'일반\' else concat(lvl.level,\'등급\') end
								   when \'4\' then concat(dis.svc_lvl,\'등급\') else \'\' end as lvl_nm
			,      kind.rate
			,      mst.tel
			,      mst.hp
			,      mst.addr
			,      lvl.app_no
			,      mst.client_no
			  from (
				   select min(m03_mkind) as kind
				   ,      m03_jumin as jumin
				   ,      m03_name as name
				   ,      m03_tel as tel
				   ,      m03_hp as hp
				   ,      concat(m03_juso1,\' \', m03_juso2) as addr
				   ,      m03_client_no AS client_no
					 from m03sugupja
					where m03_ccode = \''.$code.'\'';

	if (!empty($name)) $sql .= ' and m03_name >= \''.$name.'\'';

	$sql .= '		group by m03_jumin, m03_name
				   ) as mst ';

	if ($_SESSION['userLevel'] == 'P' &&
		$_SESSION['userSmart'] == 'Y'){
		$sql .= 'INNER JOIN (
					   SELECT DISTINCT t01_jumin AS jumin
						 FROM t01iljung
						WHERE t01_ccode = \''.$code.'\'
						  AND t01_mkind = \'0\'
						  AND t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'
						  AND LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
						  AND t01_del_yn = \'N\'
					   ) AS iljung
					ON iljung.jumin = mst.jumin ';
	}

	$sql .= 'left join (
				   select jumin
				   ,      MIN(svc_cd) AS svc_cd
				   ,      seq
					 from client_his_svc
					where org_no = \''.$code.'\'
					  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

	if ($SR){
		$sql .= ' and svc_cd NOT IN (\'S\',\'R\')';
	}

	$sql .= '		  and seq = (SELECT MAX(tmp.seq)
								   FROM client_his_svc AS tmp
								  WHERE tmp.org_no = \''.$code.'\'
								    AND tmp.jumin  = client_his_svc.jumin
								    AND DATE_FORMAT(tmp.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
									AND DATE_FORMAT(tmp.to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

	if ($SR){
		$sql .= ' and svc_cd NOT IN (\'S\',\'R\')';
	}

	$sql .= ')
					 GROUP BY jumin, seq
				   ) as svc
				on svc.jumin  = mst.jumin';

	if ($kind != ''){
		$sql .= ' and svc.svc_cd = \''.$kind.'\'';
	}

	$sql .= ' inner join (
				   select jumin
				   ,      svc_cd
				   ,      seq
				   ,      level
				   ,      app_no
					 from client_his_lvl
					where org_no = \''.$code.'\'
					  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					  and seq = (SELECT MAX(tmp.seq)
								   FROM client_his_lvl AS tmp
								  WHERE tmp.org_no = \''.$code.'\'
								    AND tmp.jumin  = client_his_lvl.jumin
								    AND DATE_FORMAT(tmp.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
									AND DATE_FORMAT(tmp.to_dt,  \'%Y%m\') >= \''.$year.$month.'\')
				   ) as lvl
				on lvl.jumin = mst.jumin
			   and lvl.svc_cd = svc.svc_cd
			  left join (
				   select jumin
				   ,      seq
				   ,      svc_val
				   ,      svc_lvl
					 from client_his_dis
					where org_no = \''.$code.'\'
					  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					  and seq = (SELECT MAX(tmp.seq)
								   FROM client_his_dis AS tmp
								  WHERE tmp.org_no = \''.$code.'\'
								    AND tmp.jumin  = client_his_dis.jumin
								    AND DATE_FORMAT(tmp.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
									AND DATE_FORMAT(tmp.to_dt,  \'%Y%m\') >= \''.$year.$month.'\')
				   ) as dis
				on dis.jumin = mst.jumin
			  left join (
				   select jumin
				   ,      seq
				   ,      kind
				   ,      rate
					 from client_his_kind
					where org_no = \''.$code.'\'
					  and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					  and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					  and seq = (SELECT MAX(tmp.seq)
								   FROM client_his_kind AS tmp
								  WHERE tmp.org_no = \''.$code.'\'
								    AND tmp.jumin  = client_his_kind.jumin
								    AND DATE_FORMAT(tmp.from_dt,\'%Y%m\') <= \''.$year.$month.'\'
									AND DATE_FORMAT(tmp.to_dt,  \'%Y%m\') >= \''.$year.$month.'\')
				   ) as kind
				on kind.jumin = mst.jumin
			 order by nm';
	//if($debug) $html .= '<tr><td>'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tgJumin[$row['cd']]['jumin']){
			$jumin = $tgJumin[$row['cd']]['jumin'];
		}else{
			$jumin = $row['cd'];
		}

		$jumin = $ed->en($jumin);

		$html .= '<tr>
					<td class=\'center\'>'.($i+1).'</td>
					<td class=\'center\'><div class=\'left\'><a href=\'#\' onclick=\'setItem("name='.$row['nm'].'&jumin='.$jumin.'&app_no='.$row['app_no'].'&lvl_cd='.$row['lvl_cd'].'&lvl_nm='.$row['lvl_nm'].'&rate='.$row['rate'].'&no='.$row['client_no'].'&strJumin='.$myF->issStyle($row['cd']).'");\'>'.$row['nm'].'</a></div></td>
					<td class=\'center\'><div class=\'left\'>'.$row['app_no'].'</div></td>
					<td class=\'center\'>'.$myF->issToBirthDay($row['cd'],'.').'</td>
					<td class=\'center\'><div class=\'left\'>'.$myF->phoneStyle($row['tel'],'.').'</div></td>
					<td class=\'center last\'><div class=\'left nowrap\' style=\'width:95px;\'>'.$row['addr'].'</div></td>
				  </tr>';
	}

	$conn->row_free();

	$html .= '	</tbody>
			  </table>';

	echo $html;

	include_once('../inc/_db_close.php');
?>
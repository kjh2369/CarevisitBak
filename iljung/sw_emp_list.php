<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code		= $_POST['code'];
	$mode		= $_POST['mode'];
	$year       = $_POST['year'];

	parse_str($_POST['param'], $para);

	$target = $mode;

	if (!$year) $year = Date('Y');

	echo  '<table class=\'my_table\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'40px\'>
					<col width=\'70px\'>
					<col width=\'70px\'>
					<col width=\'70px\'>
					<col >
				</colgroup>';



	echo '	<thead>
					<tr>
						<th class=\'head\' ><input id="chkAll" name="chk" type="checkbox" class="checkbox" onclick="lfChkAll();" checked></th>
						<th class=\'head\' >성명</th>
					    <th class=\'head\' >생년월일</th>
						<th class=\'head\' >등급</th>
					    <th class=\'head last\' >
							<div  style=\'text-align:left; padding-left:5px;\'>
								<span class=\'btn_pack small\'><button type=\'button\' style=\'width:52px;\' onclick=\'EmpPrint();\'>빈양식출력</button></span>
							</div>
						</th>
					</tr>';

	echo  '</thead>';

	echo '<tbody>
				<tr>
					<td class="top last" colspan="5">
						<div style="width:100%; height:380px; overflow-x:hidden; overflow-y:scroll;">
							<table class=\'my_table_blue\' style=\'width:100%;\'>
							<colgroup>
								<col width=\'40px\'>
								<col width=\'70px\'>
								<col width=\'70px\'>
								<col width=\'70px\'>
								<col >
							</colgroup>';
									/*
									$sql = 'SELECT	distinct m03_jumin
											,		m03_name
											,       level
											,      case lvl.svc_cd when \'0\' then case lvl.level when \'9\' then \'일반\' else concat(lvl.level,\'등급\') end
											       when \'4\' then concat(lvl.level,\'등급\') else \'\' end as lvl_nm
											FROM	m03sugupja
											LEFT JOIN (SELECT	org_no
														,		jumin
														FROM	client_his_svc
														WHERE   svc_cd = \'0\'
														  AND date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
														  AND date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
														) as svc
											ON      m03_ccode = svc.org_no
											AND     m03_jumin = svc.jumin
											LEFT JOIN (   select org_no
													   ,	  jumin
													   ,      level
													   ,	  svc_cd
													    from client_his_lvl
														where org_no = \''.$code.'\'
														  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
														  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
													   ) as lvl
											ON      m03_ccode = lvl.org_no
											AND     m03_jumin = lvl.jumin
											WHERE	m03_ccode	= \''.$code.'\'
											AND		m03_mkind	= \'0\'
											AND		m03_del_yn	= \'N\'
											ORDER	BY m03_name
											';
									*/

									$sql = 'SELECT	m03_jumin, m03_name
											,		CASE WHEN m03_mkind = \'0\' THEN
														 CASE WHEN b.level = \'9\' THEN \'일반\'
															  WHEN b.level = \'A\' THEN \'인지등급\' ELSE CONCAT(b.level,\'등급\') END
														 WHEN \'4\' THEN CONCAT(b.level,\'등급\') ELSE \'\' END AS lvl_nm
											FROM	m03sugupja
											INNER	JOIN	client_his_svc AS a
													ON		a.org_no = m03_ccode
													AND		a.svc_cd = m03_mkind
													AND		a.jumin	 = m03_jumin
													AND		LEFT(a.from_dt, 4)	<= \''.$year.'\'
													AND		LEFT(a.to_dt, 4)	>= \''.$year.'\'
											LEFT	JOIN	client_his_lvl AS b
													ON		b.org_no = m03_ccode
													AND		b.svc_cd = m03_mkind
													AND		b.jumin	 = m03_jumin
													AND		LEFT(b.from_dt, 4)	<= \''.$year.'\'
													AND		LEFT(b.to_dt, 4)	>= \''.$year.'\'
											WHERE	m03_ccode	= \''.$code.'\'
											AND		m03_mkind	= \'0\'
											AND		m03_del_yn	= \'N\'
											GROUP	BY m03_jumin
											ORDER	BY m03_name
											';

									$conn->query($sql);
									$conn->fetch();

									$rowCount = $conn->row_count();

									for($i=0; $i<$rowCount; $i++){
										$row = $conn->select_row($i);

										echo  '<tr>
													<td class=\'center\'><input id="chkIn'.$i.'" name="chkIn" type="checkbox" class="checkbox" cltCd="'.$ed->en64($row['m03_jumin']).'" checked></td>
													<td class=\'center\'><div align="left" class="nowrap" style="width:70px; padding-left:5px;">'.$row['m03_name'].'</div></td>
													<td class=\'center\'>'.$myF->issToBirthday($row['m03_jumin'],'.').'</td>
													<td class=\'center\'>'.$row['lvl_nm'].'</td>
													<td class=\'center last\'>&nbsp;</td>
												  </tr>';


									}

									$conn->row_free();

		echo '				</tbody>
						</table>';


	include_once('../inc/_db_close.php');
?>
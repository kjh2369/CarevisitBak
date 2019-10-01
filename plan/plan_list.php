<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$org_no = $_SESSION['userCenterCode'];
	$SR = $_POST['SR'];
	$year = $_POST['year'];

	$sql = 'SELECT	m03_jumin AS jumin, m03_name AS name, m03_in_charge AS in_charge_cd, a.m02_yname AS in_charge_name, a.m02_team_manager AS team_manager_cd, b.m02_yname AS team_manager_name
			FROM	m03sugupja
			INNER	JOIN	client_his_svc AS c
					ON		c.org_no= m03_ccode
					AND		c.jumin	= m03_jumin
					AND		c.svc_cd= \''.$SR.'\'
					AND		LEFT(c.from_dt, 4)	<= \''.$year.'\'
					AND		LEFT(c.to_dt, 4)	>= \''.$year.'\'
			LEFT	JOIN	m02yoyangsa AS a
					ON		a.m02_ccode	= m03_ccode
					AND		a.m02_yjumin= m03_in_charge
					AND		a.m02_del_yn= \'N\'
			LEFT	JOIN	m02yoyangsa AS b
					ON		b.m02_ccode	= m03_ccode
					AND		b.m02_yjumin= a.m02_team_manager
					AND		b.m02_del_yn= \'N\'
			WHERE	m03_ccode	= \''.$org_no.'\'
			AND		m03_del_yn	= \'N\'
			ORDER	BY CASE WHEN IFNULL(in_charge_cd, \'\') != \'\' THEN 1 ELSE 2 END, in_charge_name, name
			';
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!$row['in_charge_cd']) $row['in_charge_cd'] = 'NOT';
		if (!$data[$row['in_charge_cd']]) $data[$row['in_charge_cd']] = Array('name'=>$row['in_charge_name'], 'manager'=>$row['team_manager_name']);

		$data[$row['in_charge_cd']]['LIST'][$row['jumin']] = Array('name'=>$row['name'], 'gender'=>$myF->issToGender($row['jumin']), 'age'=>$myF->issToAge($row['jumin']));
		$data[$row['in_charge_cd']]['rows'] ++;
	}

	$conn->row_free();

	if (is_array($data)){
		foreach($data as $in_charge => $R1){
			foreach($R1['LIST'] as $jumin => $R){?>
				<tr onmouseover="this.style.backgroundColor='#EEEEEE';" onmouseout="this.style.backgroundColor='';"><?
				if ($R1['rows'] > 0){?>
					<td class="center" rowspan="<?=$R1['rows'];?>" style="background-color:white;"><?
					$no = 1;
					if ($in_charge != 'NOT'){
						echo $R1['name'].'<br>담당<br>복지사<br>('.$R1['manager'].')';
					}else{
						echo '-';
					}?>
					</td><?
				}?>
				<td class="center"><?=$no;?></td>
				<td><?=$R['name'];?></td>
				<td class="center"><?=$R['gender'];?></td>
				<td class="right"><?=$R['age'];?>세</td><?
				for($i=1; $i<=12; $i++){?>
					<td onclick="lfPlanReg('<?=$i;?>');">&nbsp;</td><?
				}?>
				<td>&nbsp;</td>
				</tr><?

				$no ++;
				$R1['rows'] = 0;
			}
		}
	}else{?>
		<tr>
			<td class="center" colspan="18">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>
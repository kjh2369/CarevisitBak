<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = $_POST['year'];
	$IsWrk = $_POST['IsWrk'];
	$findName = $_POST['findName'];


	$sql = 'SELECT	CONCAT(jumin,\'_\',RIGHT(yymm,2)) AS id
			,		COUNT(seq) AS cnt
			FROM	sw_log
			WHERE	org_no = \''.$orgNo.'\'
			AND		LEFT(yymm,4) = \''.$year.'\'
			AND		del_flag = \'N\'
			GROUP	BY jumin, yymm';

	$log = $conn->_fetch_array($sql,'id');

	//계약기간
	$sql = 'SELECT	jumin
			,		DATE_FORMAT(from_dt,\'%Y%m\') AS from_ym
			,		DATE_FORMAT(to_dt,\'%Y%m\') AS to_ym
			FROM	client_his_svc
			WHERE	org_no = \''.$orgNo.'\'
			AND		svc_cd = \'0\'
			AND		LEFT(from_dt,4) <= \''.$year.'\'
			AND		LEFT(to_dt,4)	>= \''.$year.'\'
			ORDER	BY jumin, from_dt, to_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		for($j=1; $j<=12; $j++){
			$tmpYM = $year.($j < 10 ? '0' : '').$j;

			if ($tmpYM >= $row['from_ym'] && $tmpYM <= $row['to_ym']){
				$dtCnt[$row['jumin']][$j] ++;
			}
		}
	}

	$conn->row_free();

	$sql = 'SELECT	m03_jumin AS jumin
			,		m03_name AS name
			,		lvl.level AS lvl
			FROM	m03sugupja
			INNER	JOIN	client_his_svc AS svc
					ON		svc.org_no	= m03_ccode
					AND		svc.svc_cd	= m03_mkind
					AND		svc.jumin	= m03_jumin
					AND		LEFT(svc.from_dt,4) <= \''.$year.'\'
					AND		LEFT(svc.to_dt,4)	>= \''.$year.'\'
			INNER	JOIN	client_his_lvl AS lvl
					ON		lvl.org_no	= m03_ccode
					AND		lvl.svc_cd	= m03_mkind
					AND		lvl.jumin	= m03_jumin
					AND		LEFT(lvl.from_dt,4) <= \''.$year.'\'
					AND		LEFT(lvl.to_dt,4)	>= \''.$year.'\'';

	if ($IsWrk == 'DAN'){
		$sql .= '
			INNER	JOIN	client_option AS c
					ON		c.org_no = m03_ccode
					AND		c.jumin = m03_jumin
					AND		c.day_night_yn = \'Y\'
					AND		lvl.level = \'5\'';
	}
	
	$sql .= '
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'0\'
			AND		m03_del_yn	= \'N\'';
	
	if($findName) $sql .= ' AND m03_name >= \''.$findName.'\''; 

	$sql .=	'ORDER	BY name';
	


	#if ($debug) echo '<tr><td colspan="20">'.nl2br($sql).'</td></tr>';

	$data = $conn->_fetch_array($sql,'jumin');

	$no = 1;

	foreach($data as $jumin => $row){
		$gender = $myF->issToGender($jumin);

		if ($gender == '남'){
			$gender = '<span style="color:BLUE;">남</span>';
		}else{
			$gender = '<span style="color:RED;">여</span>';
		}?>
		<tr onmouseover="this.style.backgroundColor='#EFEFEF';" onmouseout="this.style.backgroundColor='#FFFFFF';">
			<td class="center" style=""><?=$no;?></td>
			<td class="center" style=""><div align="left" class="nowrap" style="width:80px"><?=$row['name'];?>(<?=$gender;?>)</div></td>
			<td class="center" style=""><?=$myF->issToBirthday($jumin,'.');?></td>
			<td class="center" style=""><?=($row['lvl'] ? $row['lvl'].'등급' : '');?></td><?
			for($j=1; $j<=12; $j++){
				if ($dtCnt[$jumin][$j] > 0){?>
					<td id="obj<?=$no;?>_<?=$j;?>" class="center" style="cursor:pointer; background-color:#FFFFA2;" onclick="lfWokrLogReg(this,'<?=$ed->en($jumin);?>','<?=($j < 10 ? '0' : '').$j;?>');"><a href="#"><?=$log[$jumin.'_'.($j < 10 ? '0' : '').$j]['cnt'];?></a></td><?
				}else{?>
					<td id="obj<?=$no;?>_<?=$j;?>" class="center" style=""></td><?
				}
			}?>
			<td class="center last" style="">&nbsp;</td>
		</tr><?
		$no ++;
	}

	Unset($log);
	Unset($row);
	Unset($data);
	Unset($dtCnt);

	include_once('../inc/_db_close.php');
?>
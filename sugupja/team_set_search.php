<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$teamCd  = $ed->de($_POST['teamCd']);
	$cltNm = $_POST['cltNm'];
	$kind  = $_POST['kind'];


	$sql = 'SELECT DISTINCT m03_jumin, m03_jumin as jumin, svc.svc_cd, team.seq, team_cd, from_ym, to_ym, m02_yname as yname, m03_name as name
			,      concat(m03_juso1,\' \', m03_juso2) as addr
			/*,      case lvl.svc_cd when \'0\' then case lvl.level when \'9\' then \'일반\' else concat(lvl.level,\'등급\') end when \'4\' then concat(lvl.level,\'등급\') else \'\' end as ylvl*/
			FROM   m03sugupja 
			INNER JOIN client_his_svc as svc
			ON    svc.org_no = m03_ccode
			AND   svc.jumin  = m03_jumin
			AND   date_format(now(),\'%Y%m%d\') >= date_format(svc.from_dt,\'%Y%m%d\')
			AND   date_format(now(),\'%Y%m%d\') <= date_format(svc.to_dt,  \'%Y%m%d\')
			LEFT  JOIN client_his_team as team
			ON	   team.org_no = m03_ccode
			AND    team.jumin = m03_jumin
			AND    date_format(now(),\'%Y%m\') >= from_ym
			AND    date_format(now(),\'%Y%m\') <= to_ym
			AND    del_flag = \'N\'
			LEFT  JOIN m02yoyangsa
			ON	   m02_ccode = m03_ccode
			AND    m02_yjumin = team_cd
			AND    m02_mkind  = \'0\'
			
			/*
			LEFT   JOIN (
						  select jumin
						  ,      svc_cd
						  ,      level
							from client_his_lvl
						   where org_no = \''.$code.'\'
						   and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						   and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')) as lvl
			ON	   lvl.svc_cd = m03_mkind
			AND	   lvl.jumin = m03_jumin
			*/
			WHERE  m03_ccode = \''.$code.'\'
			AND    m03_mkind = \'0\'';

	if($teamCd)
		$sql .= ' AND team_cd = \''.$teamCd.'\'';

	if($cltNm)
		$sql .= ' AND m03_name = \''.$cltNm.'\'';
	
	if($kind)
		$sql .= 'AND svc.svc_cd = \''.$kind.'\'';


	$sql .= '	ORDER  BY yname, name';
	
	//echo '<tr><td colspan="9">'.nl2br($sql).'</td></tr>'; 
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	?>
	<?
	
	if($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn -> select_row($i); ?>
			
			<tr>
				<td class="center" ><div style="left nowrap" style="width:40px;"><?=($i+1);?></div></td>
				<td class='center'><div class="left nowrap" style="width:120px;">
					<div style='float:left;  width:auto; height:100%; padding-top:1px;'><input id="strTeam<?=($i+1);?>" name="strTeam<?=($i+1);?>" type="text" style="width:60px; padding:0; background-color:#eeeeee;" value="<?=$row['yname'];?>" readonly />
					<span class='btn_pack find' onclick="lfTeamPop('<?=$row['svc_cd'];?>', '<?=$ed->en($row['jumin']);?>');"></span></div><div style='width:auto; height:100%; padding-top:2px;'>
					<!--span class="btn_pack m"><button type="button" onclick="lfSave(<?=($i+1);?>);">적용</button></span--></div>
					</div>
					<input id="strTeamCd<?=($i+1);?>" name="strTeamCd<?=($i+1);?>" type="hidden" />
					<input id="cltCd<?=($i+1);?>" name="cltCd<?=($i+1);?>" type="hidden" value="<?=$ed->en($row['jumin']);?>" />
					<input id="svcCd<?=($i+1);?>" name="svcCd<?=($i+1);?>" type="hidden" value="<?=$row['svc_cd'];?>" />
					<input id="seq<?=($i+1);?>" name="seq<?=($i+1);?>" type="hidden" value="<?=$row['seq'];?>" />
				</td>
				<td class="center"><div class="center nowrap" style="width:110px;"><?=$myF->_styleYYMM($row['from_ym'],'.');?>~<?=$myF->_styleYYMM($row['to_ym'],'.');?></div></td>
				<td class="center"><div class="left nowrap" style="width:80px;"><?=$row['name'];?></div></td>
				<td class="center"><div class="center nowrap" style="width:80px;"><?=$myF->issToBirthday($row['jumin'],'.');?></div></td>
				<td class="center"><div class="center nowrap" style="width:40px;"><?=$myF->issToGender($row['jumin'],'.');?></div></td>
				<td class="center"><div class="center nowrap" style="width:40px;"><?=$myF->issToAge($row['jumin'],'.');?></div></td>
				<td class="center"><div class="left nowrap" style="width:305px;"><?=$row['addr'];?></div></td>
				<td class="last"><div class="left nowrap" style="width:15px;"></div></td>
			</tr>
			<?	
		}
	}else {
		echo '<tr><td class="center" colspan="9">::검색된 데이터가 없습니다::</td></tr>';
	}

	$conn -> row_free();

	?>	

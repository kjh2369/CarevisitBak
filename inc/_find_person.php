<?
	//header('P3P: CP="NOI CURa ADMa DEVa TAIa OUR DELa BUS IND PHY ONL UNI COM NAV INT DEM PRE"');
	
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$findStat = $_REQUEST['findStat'] != '' ? $_REQUEST['findStat'] : '1';
	$findName = $_REQUEST['findName'];
	$find_dept = $_REQUEST['find_dept'];
	$find_job = $_REQUEST['find_job'];
	$hourly_yn = $_REQUEST['hourly_yn'] != '' ? $_REQUEST['hourly_yn'] : 'Y';

	if ($_SESSION['userLevel'] == 'A'){
		$code = $_REQUEST['code'];
	}else{
		$code = $_SESSION['userCenterCode'];
	}
	

	$kind = $_REQUEST['kind'];
	$type = $_REQUEST['type'];
	$returnType = $_REQUEST['return'];
	$yymm = $_POST['year'].$_POST['month'];
	$rtnType = $_POST['rtnType'];
	$wrkType = $_POST['wrkType'];
	$openerId = $_POST['openerId'];
	$subCd = $_POST['subCd'];

	$svc_gbn = $_POST['svc_gbn'];

	$svcCd = $_POST['svcCd']; //서비스
	$regDt = $_GET['regDt'];
	

	if (!$svcCd){
		$sql = 'SELECT	m00_mkind
				FROM	m00center
				WHERE	m00_mcode = \''.$code.'\'
				ORDER	BY m00_mkind
				LIMIT	1';
		$svcCd = $conn->get_data($sql);
	}

	$svcCd = 'S';


	//수급자
	$jumin = $_POST['jumin'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	//가족여부
	$ynFamily = $_POST['ynFamily'];

	if ($ynFamily == 'Y' || $ynFamily == '200'){
		$sql = 'select cf_mem_cd as cd
				,      cf_mem_nm as nm
				  from client_family
				 where org_no   = \''.$code.'\'
				   and cf_jumin = \''.$jumin.'\'';

		$loFamilyMem = $conn->_fetch_array($sql,'cd');
	}

	$temp_yoy = explode(',', $_REQUEST['yoy']);
	if($yoy[0] != '' && $yoy[1] != ''){
		$yoys = "'".$ed->de($temp_yoy[0])."','".$ed->de($temp_yoy[1])."'";
	}else {
		$yoys = "'".$ed->de($temp_yoy[0])."'";
	}

	if (empty($find_dept)) $find_dept = 'all';
	if (empty($find_job)) $find_job = 'all';

	switch($type){
	case 'sugupja':
		$title = '고객';
		break;
	case 'yoyangsa':
		$title = '직원';
		break;
	case 'member':
		$title = '직원';
		break;
	case 'team':
		$title = '팀장';
		break;
	case 'manager':
		$title = '담당자';
		break;
	}
?>
<style>
body{
	margin-top:0px;
	margin-left:0px;
}
</style>
<script language='javascript'>
<!--
var retVal = 'cancel';

function _currnetRow(value1, value2, value3, value4, value5, value6, value7, value8, value9, value10, value11, value12, value13, value14, value15, value16, value17, value18, value19, value20, value21, value22, value23, value24, value25){
	var currentItem = new Array();

	currentItem[0] = value1;
	currentItem[1] = value2;
	currentItem[2] = value3;
	currentItem[3] = value4;
	currentItem[4] = value5;
	currentItem[5] = value6;
	currentItem[6] = value7;
	currentItem[7] = value8;
	currentItem[8] = value9;
	currentItem[9] = value10;
	currentItem[10] = value11;
	currentItem[11] = value12;
	currentItem[12] = value13;
	currentItem[13] = value14;
	currentItem[14] = value15;
	currentItem[15] = value16;
	currentItem[16] = value17;
	currentItem[17] = value18;
	currentItem[18] = value19;
	currentItem[19] = value20;
	currentItem[20] = value21;
	currentItem[21] = value22;
	currentItem[22] = value23;
	currentItem[23] = value24;
	currentItem[24] = value25;
	
	if ('<?=$returnType;?>' == 'lfTargetFindResult'){
		try{
			opener.lfTargetFindResult(currentItem);
		}catch(e){
		}
	}else{
		try{
			opener.lfMemFindResult(currentItem);
		}catch(e){
			var returnType = $('#return').val();

			if (returnType){
				opener.returnObj = currentItem;
				eval('opener.'+returnType+'()');
			}
		}
	}

	window.returnValue = currentItem;
	window.close();
}

function current_row(current_if){
	var returnType = $('#return').val();//$('#option').attr('returnType');

	if (returnType != ''){
		//opener.eval(returnType+'('+current_if+')');
		//eval('opener.'+returnType+'(\''+current_if+'\')');
		try{
			opener.lfMemFindResult(current_if);
		}catch(e){
			opener.returnObj = current_if;
			eval('opener.'+returnType+'()');
		}
	}else{
		window.returnValue = current_if;
	}
	window.close();
}

function _submit(){
	var returnType = $('#return').val();//$('#option').attr('returnType');

	if (returnType != ''){
		document.f.action = '_find_person.php?type=<?=$type;?>&code=<?=$code;?>&kind=<?=$kind;?>';
	}
	document.f.submit();
}

window.onload = function(){
	if('<?=$type?>' == 'svc_date'){
	}else {
		document.f.findName.focus();
	}
}

//-->
</script>
<style>
.view_type1{
	margin:0;
	padding:0;
}

.view_type1 thead th{
	margin:0;
	padding:0;
	text-align:center;
}

.view_type1 tbody td{
	margin:0;
	padding:0;
}

view_type2{
	margin:0;
	padding:0;
}
</style>
<form name="f" method="post">
<table class="my_table my_brder" width="100%">
	<tr>
		<th style="border:1px solid #a6c0f3;" class="title"><?=$title;?> 조회</th>
	</tr>
</table><?
if ($type == 'svc_date'){
	//이용계약서(계약기간리스트)
}else{?>
	<table class="my_table my_border" style="margin-top:-1px;"><?
		if ($type == 'sugupja'){?>
			<colgroup><?
				if ($wrkType != 'CARE_CLIENT_NORMAL'){?>
					<col width="15%">
					<col width="10%">
					<col width="20%"><?
				}else{?>
					<col width="10%"><?
				}?>
				<col >
				<col width="5%">
			</colgroup><?
		}else if($type == 'yoyangsa' ||
				 $type == 'member'){?>
			<colgroup>
				<col width="5%">
				<col width="10%">
				<col width="5%">
				<col width="10%">
				<col width="5%">
				<col width="5%">
				<col width="10%">
				<col width="10%">
				<col >
			</colgroup><?
		}else if($type == 'team'){?>
			<colgroup>
				<col width="8%">
				<col width="15%">
				<col >
			</colgroup><?
		}?>
		<tbody>
		<tr><?
			if ($type == 'sugupja'){
				if ($wrkType != 'CARE_CLIENT_NORMAL'){?>
					<th class="center">이용상태</th>
					<td class="last">
						<select name="findStat" style="width:auto;">
							<option value="all">전체</option>
							<option value="1" <?=$findStat == "1" ? "selected" : "";?>>이용</option>
							<option value="9" <?=$findStat == "9" ? "selected" : "";?>>중지</option>
						</select>
					</td><?
				}?>
				<th class="center"><?=$title;?>명</th>
				<td ><input name="findName" type="text" value="<?=$findName;?>" style="width:100%;" onKeyPress="if(event.keyCode==13){_submit();}"></td>
				<td class="center"><span class="btn_pack m" ><button type="button" onclick="_submit('1');" >조회</button></span></td><?
			}else if($type == 'yoyangsa' ||
					 $type == 'member'){ ?>

					<th class="center">부서</th>
					<td>
					<?
						echo '<select name=\'find_dept\' style=\'width:90px;;\'>';
						echo '<option value=\'all\' '.($find_dept == 'all' ? 'selected' : '').'>전체</option>';

						$sql = "select dept_cd, dept_nm
								  from dept
								 where org_no   = '$code'
								   and del_flag = 'N'
								 order by order_seq";

						$conn->query($sql);
						$conn->fetch();

						$row_count = $conn->row_count();

						for($i=0; $i<$row_count; $i++){
							$row = $conn->select_row($i);

							echo '<option value=\''.$row['dept_cd'].'\' '.($find_dept == $row['dept_cd'] ? 'selected' : '').'>'.$row['dept_nm'].'</option>';
						}

						$conn->row_free();

						echo '<option value=\'-\' '.($find_dept == '-' ? 'selected' : '').'>미등록</option>';
						echo '</select>';
					?>
					</td>
					<th class="center">직무</th>
					<td >
					<?
						echo '<select name=\'find_job\' style=\'width:80px;\'>';
						echo '<option value=\'all\' '.($find_job == 'all' ? 'selected' : '').'>전체</option>';

						$sql = "select job_cd, job_nm
								  from job_kind
								 where org_no   = '$code'
								   and del_flag = 'N'
								 order by job_seq";

						$conn->query($sql);
						$conn->fetch();

						$row_count = $conn->row_count();

						for($i=0; $i<$row_count; $i++){
							$row = $conn->select_row($i);

							echo '<option value=\''.$row['job_cd'].'\' '.($find_job == $row['job_cd'] ? 'selected' : '').'>'.$row['job_nm'].'</option>';
						}

						$conn->row_free();

						echo '<option value=\'-\' '.($find_job == '-' ? 'selected' : '').'>미등록</option>';
						echo '</select>';
					?>
					</td>
					<th class="center">상태</th>
					<td style="text-align:left;" >
						<select name="findStat" style="width:auto;">
							<option value="all" <? if($findStat == "all"){echo "selected";}?>>전체</option>
							<option value="1"<? if($findStat == "1"){echo "selected";}?>>재직</option>
							<option value="2"<? if($findStat == "2"){echo "selected";}?>>휴직</option>
							<option value="9"<? if($findStat == "9"){echo "selected";}?>>퇴사</option>
						</select>
					</td>
					<td class="center" rowspan="2"><span class="btn_pack m" ><button type="button" onclick="_submit('1');" >조회</button></span></td>
				<?
			}else if($type == 'team'){ ?>
				<tr>
					<th class="head">팀장명</th>
					<td><input name="findName" type="text" value="<?=$findName;?>" style="width:100%;" onKeyPress="if(event.keyCode==13){_submit();}"></td>
					<td class="left last"><span class="btn_pack m" ><button type="button" onclick="_submit('1');" >조회</button></span></td><?
			} ?>

		</tr>
		</tbody>
	</table><?
}?>
		<table class="my_table my_border" style="width:100%; height:100%;">
			<colgroup>
				<col width="50px">
				<col width="90px">
				<col width="90px">
				<col width="40px">
				<col width="90px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head" >No</th>
					<th class="head" >성명</th>
					<th class="head" >생년월일</th>
					<th class="head" >성별</th>
					<th class="head" >연락처</th>
					<th class="head last" >주소</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?
					if($type == 'sugupja'){
						$cols = 6;
						$height = '305';
					}else {
						$cols = 6;
						$height = '275';
					}
					?>
					<td style="height:325px; vertical-align:top; margin:0; padding:0;" colspan="<?=$cols?>">
						<div style="overflow-x:hidden; overflow-y:scroll; height:<?=$height?>">
							<table class="my_table" style=" width:100%;">
							<colgroup>
								<col width="50px">
								<col width="90px">
								<col width="90px">
								<col width="40px">
								<col width="90px">
								<col>
							</colgroup>
							<tbody><?
							
								if($type == 'sugupja'){
									if ($wrkType != 'CARE_CLIENT_NORMAL'){
										$sql = 'select ';

										if ($rtnType == 'key'){
											$sql .= 'm03_key as jumin';
										}else{
											$sql .= 'm03_jumin as jumin';
										}

										$sql .= '
												,      m03_name as name
												,      m03_tel as tel
												,	   m03_hp as hp
												,	   m03_post_no as postno
												,      m03_juso1 as addr
												,      m03_juso2 as addr2
												,	   m03_yboho_name as boho_name
												,	   m03_yboho_gwange as boho_gwange
												,	   m03_yboho_phone as boho_phone
												,	   m03_yoyangsa4_nm as boho_addr

												,		SUBSTR(m03_yoyangsa5_nm,1,1) AS marry_gbn
												,		SUBSTR(m03_yoyangsa5_nm,2,1) AS cohabit_gbn
												,		SUBSTR(m03_yoyangsa5_nm,3,2) AS edu_gbn
												,		SUBSTR(m03_yoyangsa5_nm,5,1) AS rel_gbn

												,		IFNULL(mst_jumin.jumin, m03_jumin) AS real_jumin

												,      lvl.app_no as no
												,      case lvl.svc_cd when \'0\' then case lvl.level when \'9\' then \'일반\' when \'A\' then \'인지지원\' else concat(lvl.level,\'등급\') end
												when \'4\' then concat(dis.svc_lvl,\'등급\') else \'\' end as level
												  from m03sugupja
												   left join (
															   select jumin
															   ,      svc_lvl
																 from client_his_dis
																where org_no = \''.$code.'\'
																  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
																  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
															   ) as dis
															on dis.jumin = m03_jumin
												  left join (
															select jumin
															, svc_cd
															, level
															, app_no
															, from_dt
															, MAX(to_dt) AS to_dt
															from client_his_lvl
															where org_no = \''.$code.'\'';
												if($regDt){
													$sql .= '		and date_format(\''.$regDt.'\',\'%Y%m%d\') >=date_format(from_dt,\'%Y%m%d\')
																	and date_format(\''.$regDt.'\',\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')';
												}else {						  
													$sql .= '		and date_format(now(),\'%Y%m%d\') >=date_format(from_dt,\'%Y%m%d\')
																	and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')';
												}

												$sql .= '	GROUP BY jumin, svc_cd
															) as lvl
															on lvl.jumin = m03_jumin
														   and lvl.svc_cd = m03_mkind
												  '.($svcCd == 'S' ? 'inner' : 'left').' join (select jumin
															 ,      svc_cd
															 ,		svc_stat
															   from client_his_svc
															  where org_no = \''.$code.'\'';

												if($findStat == '1'){
													$sql .= '  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
															   and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')';
												}

												if ($svcCd){
													$sql .= ' AND svc_cd = \''.$svcCd.'\'';
												}

												$sql .= '	  group by jumin, svc_cd
															  order by case when date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
															  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt, \'%Y%m%d\') then 1 else 2 end, seq desc
															) as svc
															on svc.jumin  = m03_jumin
															';

										$sql .= '
												LEFT	JOIN	mst_jumin
														ON		mst_jumin.org_no = m03_ccode
														AND		mst_jumin.gbn = \'1\'
														AND		mst_jumin.code = m03_jumin';

										$sql .=	' where m03_ccode = \''.$code.'\'
												   and m03_mkind = \''.($svcCd != 'S' && $svcCd != 'R' ? $svcCd : '6').'\'';

										if ($svcCd == 'S' || $svcCd == 'R'){
											if ($wrkType == 'INTERVIEW_REG'){
												$sql .= ' and m03_jumin NOT IN (SELECT	mst_jumin.code
																				FROM	hce_interview
																				INNER	JOIN	mst_jumin
																						ON		mst_jumin.org_no = hce_interview.org_no
																						AND		mst_jumin.gbn = \'1\'
																						AND		mst_jumin.cd_key = hce_interview.IPIN
																				WHERE	hce_interview.org_no = \''.$code.'\'
																				AND		hce_interview.org_type = \''.$svcCd.'\'
																				AND		hce_interview.rcpt_seq = \'0\')';
											}
										}

										if ($findStat != 'all'){
											if ($findStat == '1')
												$sql .= ' and svc_stat = \'1\'';
											else
												$sql .= ' and svc_stat != \'1\'';
										}

										if ($findName != ''){
											$sql .= ' and m03_name >= \''.$findName.'\'';
										}

										$sql .= ' group by jumin
												  order by m03_name';
									}else{
										$sql = 'SELECT	normal_seq,jumin,name,addr,addr_dtl,phone,mobile
												FROM	care_client_normal
												WHERE	org_no = \''.$code.'\'
												AND		normal_sr = \''.$svcCd.'\'';

										if ($findName != ''){
											$sql .= ' and name >= \''.$findName.'\'';
										}

										$sql .= '
												ORDER	BY name';
									}

								}else if($type == 'svc_date'){
									//이용계약서(계약기간리스트)

									$sql = 'select from_dt
									        ,	   to_dt
											,	   seq
											  from client_his_svc
											 where org_no = \''.$code.'\'
											   and svc_cd = \'0\'
											   and jumin  = \''.$ed->de($_GET['ssn']).'\'
											 order by from_dt, to_dt';


									$conn -> get_array($sql);
									$conn -> fetch();
									$row_count = $conn -> row_count();

								}else if($type == 'team'){
									//팀장조회
									$sql = 'SELECT	m02_yname as name
											,	    m02_yjumin as jumin
											,       m02_ytel as tel
											,       m02_yjuso1 as addr
											FROM    client_his_team
											INNER   JOIN m02yoyangsa
											ON      m02_ccode = org_no
											AND     m02_yjumin = team_cd
											WHERE	org_no	= \''.$code.'\'
											and    date_format(now(),\'%Y%m\') >= from_ym
											and    date_format(now(),\'%Y%m\') <= to_ym 
											and    del_flag = \'N\'';
									
									if ($findName != ''){
										$sql .= ' and name >= \''.$findName.'\'';
									}

									$sql .= ' group by team_cd';
									
								}else {
									$family_yn = $_REQUEST['family_yn'];
									$svc_cd = $_REQUEST['svcSubCode'];

									if ($_REQUEST["mKey"] != ""){

										$sql = "select m03_yoyangsa1"
											 . ",      m03_yoyangsa2"
											 . ",      m03_ylvl
												,      m03_partner
												,      m03_stat_nogood"
											 . "  from m03sugupja"
											 . " where m03_ccode = '".$code
											 . "'  and m03_key   = '".$_REQUEST["mKey"]
											 . "'";
										$conn->query($sql);
										$row = $conn->fetch();
										$yoy[1] = $row["m03_yoyangsa1"];
										$yoy[2] = $row["m03_yoyangsa2"];
										$partner_yn = $row['m03_partner'];
										$stat_nogood = $row['m03_stat_nogood'];
										$sugupjaLevel = $row['m03_ylvl'];
										$conn->row_free();

										$caseSql = "";
										$yoyIndex = 1;

										for($i=1; $i<=2; $i++){
											if ($yoy[$i] != ""){
												$caseSql .= " when m02_yjumin = '".$yoy[$i]."' then ".$yoyIndex;
												$yoyIndex++;
											}
										}

										if ($caseSql != ""){
											$orderSql = " order by case ".$caseSql." else 6 end, ";
										}else{
											$orderSql = " order by ";
										}
									}else{
										$orderSql = " order by ";
									}

									if ($subCd == '800'){
										$orderSql .= " CASE WHEN mem_h.jumin IS NOT NULL THEN 1 ELSE 2 END, m02_yname";
									}else{
										$orderSql .= " m02_yname";
									}

									if($svc_gbn == '11'){
										$kind = 0;
									}else if($svc_gbn == '21'){
										$kind = 1;
									}else if($svc_gbn == '22'){
										$kind = 2;
									}else if($svc_gbn == '23'){
										$kind = 3;
									}else if($svc_gbn == '24'){
										$kind = 4;
									}else if($svc_gbn == '51'){
										$kind = 5;
									}else{
										$kind = '';
									}

									$sql = "select distinct m02_ycode
											,	   m02_yjumin as jumin
											,      m02_yname as name
											,      m02_ytel as tel
											,      m02_ygupyeo_kind
											,      case when m02_ygupyeo_kind in ('1','2') then m02_ygibonkup else 0 end
											,      m02_yjuso1 as addr
											,	   m02_yipsail as yipsail
											,	   m02_ytoisail as ytoisail
											,	   m02_yipsail as join_dt
											,      job_nm as level
											,	   m02_email as email
											  from m02yoyangsa
											  left join job_kind
												on job_kind.org_no = m02_ccode
											   and job_kind.job_cd = m02_yjikjong
											 ";


									if ($subCd == '800'){
										$sql .= " LEFT JOIN (SELECT DISTINCT jumin FROM mem_his WHERE org_no = '$code' AND nurse_yn = 'Y') AS mem_h
													   ON mem_h.jumin = m02_yjumin";
									}



									$sql .= " where m02_ccode = '$code'
												and m02_del_yn = 'N'";

									if (!empty($kind)) $sql .= " and m02_mkind = '$kind'";

									if ($findStat != 'all'){
										$sql .= " and m02_ygoyong_stat = '$findStat'";
									}
									if ($svc_cd == '200'){
										if ($family_yn == 'Y'){
											#$sql .= " and m02_yfamcare_umu = 'Y'";
										}else if ($family_yn == 'N'){
											#$sql .= " and m02_ygupyeo_kind != '0'";
										}
									}else{
										#$sql .= " and m02_ygupyeo_kind != '0'";
									}


									if ($yoys != ""){
										$sql .= " and m02_yjumin not in(".$yoys.")";
									}

									if ($findName != ''){
										$sql .= " and m02_yname >= '$findName'";
									}

									if ($_SESSION['userLevel'] == 'A'){
										$sql .= " AND CASE WHEN m02_jikwon_gbn = 'Y' THEN '' ELSE m02_jikwon_gbn END != '' ";
									}

									if ($find_dept      != 'all') $sql .= " and m02_dept_cd = '".str_replace('-','',$find_dept)."'";
									if ($find_dept      != 'all') $sql .= " and m02_dept_cd = '$find_dept'";

									if ($find_job      != 'all') $sql .= " and m02_yjikjong = '".str_replace('-','',$find_job)."'";
									if ($find_job      != 'all') $sql .= " and m02_yjikjong = '$find_job'";

									$sql .= " group by m02_yjumin, m02_yname";
									$sql .= $orderSql;
								}

								//if ($debug) echo nl2br($sql);

								$conn->query($sql);
								$conn->fetch();
								$rowCount = $conn->row_count();

								if ($rowCount > 0){

									$seq = 1;

									if ($type == 'member'){
										for($i=0; $i<$rowCount; $i++){
											$row = $conn->select_row($i);

											$lbAdd = false;

											if ($ynFamily == 'Y'){
												if (!empty($loFamilyMem[$row['jumin']]['nm'])){
													$lbAdd = true;
												}
											}else{
												if (empty($loFamilyMem[$row['jumin']]['nm'])){
													$lbAdd = true;
												}
											}

											if ($lbAdd){
												$age    = $myF->issToAge($row['jumin']).'세';
												$gender = $myF->issToGender($row['jumin']);

												if($row['hourly'] != 0){
													$HourlyYn = 'Y';
												}else {
													$HourlyYn = 'N';
												}

												if ($returnType != ''){
													$lsResult = '"name='.$row['name'].
																'&jumin='.$ed->en($row['jumin']).
																'&age='.$age.
																'&gender='.$gender.
																'&tel='.$row['tel'].
																'&email='.$row['email'].
																'&idx='.$_REQUEST['idx'].
																'&jobKind='.$row['level'].
																'&openerId='.$openerId.'"';
												}else{
													$lsResult = '{"name":"'.$row['name'].'",
																  "jumin":"'.$ed->en($row['jumin']).'",
																  "age":"'.$age.'",
																  "gender":"'.$gender.'",
																  "tel":"'.$row['tel'].'",
																  "email":"'.$row['email'].'",
																  "jobKind":"'.$row['level'].'",
																  "openerId":"'.$openerId.'"}';
												}

												echo '<tr onmouseover=\'this.style.backgroundColor="#f2f5ff";\' onmouseout=\'this.style.backgroundColor="#ffffff";\'>';
												echo '<td class=\'center\'>'.$seq.'</td>';
												echo '<td class=\'center\'><div class=\'left\'><a href=\'#\' onclick=\'current_row('.$lsResult.');\'>'.$row['name'].'</a></div></td>';
												echo '<td class=\'center\'><div>'.$myF->issToBirthDay($row['jumin'],'.').'</div></td>';
												echo '<td class=\'center\'><div>'.$gender.'</div></td>';
												echo '<td class=\'center\'><div>'.$myF->phoneStyle($row['tel'],'.').'</div></td>';
												echo '<td class=\'center last\'><div class="left nowrap" style="width:170px;">'.$row['addr'].'</div></td>';
												echo '</tr>';

												$seq ++;
											}
										}
									}else if($type == 'yoyangsa'){
										
										for($i=0; $i<$rowCount; $i++){
											$row = $conn->select_row($i);

											$gender = getGender($row['jumin']);
											$birth  = str_replace('-', '.', getBirthDay($row['jumin']));

											if ($row['m02_ygupyeo_kind'] == '1' or $row['m02_ygupyeo_kind'] == '2'){
												$timePay = $conn->get_time_pay($_REQUEST["code"], $_REQUEST["kind"], $row['jumin'], $sugupjaLevel);

												if ($timePay == ''){
													$timePay = $row[4];
												}
											}else{
												$timePay = $row[4];
											}

											if ($yoy[1] == $row[1]){
												$p_yn = $partner_yn;
											}else{
												$p_yn = $stat_nogood;
											}

											?>
											<tr>
												<td class="center"><?=$seq;?></td>
												<td class="left"><a href="#" onClick="_currnetRow('<?=$ed->en($row['jumin']);?>','<?=$row['name'];?>','<?=$gender;?>','<?=$birth;?>','<?=$row['no']?>','<?=$row['level'];?>','<?=$myF->issToAge($row['jumin']);?>세','<?=$myF->dateStyle($row['yipsail']).'~'.$myF->dateStyle($row['ytoisail']);?>','<?=$timePay;?>', '<?=$p_yn;?>');">&nbsp;<?=$row['name'];?></a></td>
												<td class="center"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
												<td class="center"	><?=$gender;?></td>
												<td class="left">&nbsp;<?=$myF->phoneStyle($row['tel']);?></td>
												<td class="left last"	><div class="nowrap" style="width:170px;">&nbsp;<?=$row['addr'];?></div></td>
											</tr><?
											$seq ++;


										}
									}else if($type == 'team'){
										for($i=0; $i<$rowCount; $i++){
											$row = $conn->select_row($i);

											$age    = $myF->issToAge($row['jumin']).'세';
											$gender = $myF->issToGender($row['jumin']);
											
											$lsResult = '{"name":"'.$row['name'].'",
														  "jumin":"'.$ed->en($row['jumin']).'"}';
										

											echo '<tr onmouseover=\'this.style.backgroundColor="#f2f5ff";\' onmouseout=\'this.style.backgroundColor="#ffffff";\'>';
											echo '<td class=\'center\'>'.$seq.'</td>';
											echo '<td class=\'center\'><div class=\'left\'><a href=\'#\' onclick=\'current_row('.$lsResult.');\'>'.$row['name'].'</a></div></td>';
											echo '<td class=\'center\'><div>'.$myF->issToBirthDay($row['jumin'],'.').'</div></td>';
											echo '<td class=\'center\'><div>'.$gender.'</div></td>';
											echo '<td class=\'center\'><div>'.$myF->phoneStyle($row['tel'],'.').'</div></td>';
											echo '<td class=\'center\'><div class="left nowrap" style="width:170px;">'.$row['addr'].'</div></td>';
											echo '</tr>';

											$seq ++;
										
										}
									}else if($type == 'svc_date'){
										//이용계약서(계약기간리스트)
										for($i=0; $i<$rowCount; $i++){
											$row = $conn->select_row($i);

											?>
											<tr>
												<td style="height:25px; text-align:center;"	><?=$seq;?></td>
												<td style="height:25px; text-align:left;"	><a href="#" onClick="_currnetRow('<?=$myF->dateStyle($row['from_dt'],'.').'~'.$myF->dateStyle($row['to_dt'],'.');?>','<?=$row['seq']?>');">&nbsp&nbsp<?=$myF->dateStyle($row['from_dt'],'.').'~'.$myF->dateStyle($row['to_dt'],'.');?></a></td>
											</tr><?
											$seq ++;

										}
									}else if ($wrkType == 'CARE_CLIENT_NORMAL'){
										for($i=0; $i<$rowCount; $i++){
											$row = $conn->select_row($i);

											$gender = $myF->issToGender($row['jumin']);
											$age = $myF->issToAge($row['jumin']);
											$jumin = $myF->issStyle($row['jumin']);
											$addr = $row['addr'].' '.$row['addr_dtl'];
											$telno = $myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile']);?>
											<tr>
												<td style="height:25px; text-align:center;"><?=$i+1;?></td>
												<td style="height:25px; text-align:left;"><div class="left"><a href="#" onclick="_currnetRow('<?=$row['normal_seq']?>','<?=$row['name']?>');"><?=$row['name'];?></a></div></td>
												<td style="height:25px; text-align:center;"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
												<td style="height:25px; text-align:center;"><?=$gender;?></td>
												<td style="height:25px; text-align:center;"><?=$telno;?></td>
												<td style="height:25px; text-align:left;"><div class="left"><?=$addr;?></div></td>
											</tr><?
										}
									}else{
										for($i=0; $i<$rowCount; $i++){
											$row = $conn->select_row($i);

											$gender = getGender($row['real_jumin']);
											$birth  = str_replace('-', '.', getBirthDay($row['real_jumin']));
											$phone  = $row['tel'] != '' ? $row['tel'] : $row['hp'];
											$tmp_addr = explode('<br />', nl2br($row['addr']));
											$addr   = str_replace('"', '', $tmp_addr[0].' '.$row['addr2']);

											if ($rtnType == 'key'){
												$strJumin = $row['jumin'];
											}else{
												$strJumin = $ed->en($row['jumin']);
											}?>
											<tr>
												<td style="height:25px; text-align:center;"	><?=$i+1;?></td>
												<td style="height:25px; text-align:left;"	><a href="#" onClick="_currnetRow('<?=$strJumin;?>','<?=$row['name'];?>','<?=$gender;?>','<?=$birth;?>','<?=$row['no']?>','<?=$row['level'];?>','<?=$myF->issToAge($row['real_jumin']);?>세','<?=$myF->dateStyle($row['yipsail']).'~'.$myF->dateStyle($row['ytoisail']);?>','<?=$myF->issNo($row['real_jumin'])?>','<?=$addr;?>', '<?=substr($row['postno'],0,3);?>', '<?=substr($row['postno'],3,3);?>', '<?=$tmp_addr[0];?>', '<?=str_replace('"', '', $row['addr2']);?>', '<?=$phone;?>', '<?=$row['boho_name'];?>', '<?=$row['boho_gwange'];?>', '<?=$row['boho_phone'];?>','<?=$row['tel']?>','<?=$row['hp']?>','<?=$row['marry_gbn'];?>','<?=$row['cohabit_gbn'];?>','<?=$row['edu_gbn'];?>','<?=$row['rel_gbn'];?>','<?=$row['boho_addr'];?>');">&nbsp;<?=$row['name'];?></a></td>
												<td style="height:25px; text-align:center;"	><?=$myF->issToBirthday($row['real_jumin'],'.');?></td>
												<td style="height:25px; text-align:center;"	><?=$gender;?></td>
												<td style="height:25px; text-align:left;"	>&nbsp;<?=$myF->phoneStyle($row['tel']);?></td>
												<td style="height:25px; text-align:left;"	>&nbsp;<?=$row['addr'];?></td>
											</tr><?
										}
									}
								}else{?>
									<tr>
										<td style="height:25px; text-align:center;" colspan="6">::검색된 데이타가 없습니다..::</td>
									</tr><?
								}

								$conn->row_free();
							?>
							</tbody>
							</table>
						</div>
					</td>
				</tr>
			</tbody>
			</table>
		</td>
	</tr>
</tbody>
</table>

<div id="option"
	returnType="<?=$returnType;?>">
</div>
<input id="svcCd" name="svcCd" type="hidden" value="<?=$svcCd;?>">
<input id="rtnType" name="rtnType" type="hidden" value="<?=$rtnType;?>">
<input id="wrkType" name="wrkType" type="hidden" value="<?=$wrkType;?>">
<input id="wrkType" name="wrkType" type="hidden" value="<?=$wrkType;?>">
<input id="return" name="return" type="hidden" value="<?=$returnType;?>">
<input id="openerId" name="openerId" type="hidden" value="<?=$openerId;?>">

</form>
<?
	if ($rowCount == 0){
	?>
		<script>
			document.getElementById('findName').value = '';
		</script>
	<?
	}

	include_once("../inc/_footer.php");
?>
<script>
	self.focus();
</script>
<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($_SESSION["userLevel"] == "A"){
		$code = $_REQUEST["mCode"];
	}else{
		$code = $_SESSION["userCenterCode"];
	}


	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;
	if ($page < 1) $page = 1;

	$find_center_code   = $_SESSION["userLevel"] == 'A' ? $_REQUEST['find_center_code'] : $_SESSION["userCenterCode"];	//기관기호
	$find_center_kind	= $_REQUEST['find_center_kind'];																//서비스구분
	$find_center_name   = $_REQUEST['find_center_name'];																//기관명
	$find_su_name		= $_REQUEST['find_su_name'];																	//성명
	$find_su_ssn		= str_replace('-', '', $_REQUEST['find_su_ssn']);												//주민번호
	$find_su_phone		= $_REQUEST['find_su_phone'];																	//연락처
	$find_su_stat       = $_REQUEST['find_su_stat'] != '' ? $_REQUEST['find_su_stat'] : '1';							//수급상태
	$find_lvl           = $_REQUEST['find_lvl'];																		//등급
	$sst				= $_REQUEST['sst'];																				//db필드명 비교 변수
	$sod				= $_REQUEST['sod'];																				//오름(asc),내림(desc) 정렬 플래그
	$sfl				= $_REQUEST['sfl'];																				//DB(필드명)
	$strTeam			= $_REQUEST['strTeam'];																			//팀장으로 조회

	$kind = $conn->center_kind($find_center_code);

	
	#성명,주민번호,연락처 있을 시 이용상태 전체 조회
	//if($find_su_name != '' or $find_su_phone != '' or $find_su_ssn != '') $find_su_stat = 'all';


	if($find_su_ssn != ''){
		if(strlen($find_su_ssn) > 6){
			$find_ssn = subStr($find_su_ssn, 0, 6).'-'.subStr($find_su_ssn, 6, 7);
		}else {
			$find_ssn = $find_su_ssn;
		}
	}

	if (!isset($find_center_kind) || $find_center_kind == '') $find_center_kind = 'all';
	//if (empty($find_center_kind)) $find_center_kind = 'all';

	//검색변수들을 $find변수의 배열에 담는다.
	$find = array(
					'1'=>$sst
				,	'2'=>$sod
				,	'3'=>$sfl
				,	'4'=>$find_center_kind
				,	'5'=>$find_center_name
				,	'6'=>$find_su_name
				,	'7'=>$find_su_ssn
				,	'8'=>$find_su_phone
				,	'9'=>$find_su_stat
				,	'10'=>$page
			);



	#####################################################################
	#
	# 가사간병 소득등급
		$voucher_income[1] = get_voucher_income_list($conn, "'21', '22', '99'");

	# 노인돌봄 소득등급
		$voucher_income[2] = get_voucher_income_list($conn, "'21', '22', '23', '99'");

	# 산모신생아 소득등급
		$voucher_income[3] = get_voucher_income_list($conn, "'24', '25', '99'");

	# 장애인보조 소득등급
		$voucher_income[4] = get_voucher_income_list($conn, "'21', '22', '26', '27', '28', '29', '99'");

		function get_voucher_income_list($conn, $lvl_cd){
			$sql = "select lvl_cd, lvl_id, lvl_nm
				  from income_lvl
				 where lvl_cd in ($lvl_cd)";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$list[$i] = array('cd'=>$row['lvl_id'], 'nm'=>$row['lvl_nm']);
			}

			$conn->row_free();

			return $list;
		}
	#
	#####################################################################

	//고객 인정번호 만료일자
	$sql = 'SELECT jumin
			,      MAX(to_dt) AS dt
			  FROM client_his_lvl
			 WHERE org_no = \''.$code.'\'
			   AND svc_cd = \'0\'
			 GROUP BY jumin';

	$loInjungLastDt = $conn->_fetch_array($sql, 'jumin');


?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>

<script language='javascript'>
<!--

function reg_client(code, kind, jumin, modfify_type){
	var f = document.f;

	f.code.value		= code;
	f.kind.value		= kind;
	f.jumin.value		= jumin;
	f.modify_type.value = modfify_type;

	if ('<?=$lbTestMode;?>' == '1'){
		f.action = 'client_new.php';
	}else{
		f.action = 'client_reg.php';
	}

	f.submit();
}

/*********************************************************

	팀장 찾기

*********************************************************/
function findTeam(){
	
	var result = __findTeam('<?=$code;?>');
	
	if (!result) return;

	$('#strTeam').val(result['name']);
	$('#param').attr('value', 'jumin='+result['jumin']);
	
	_clientList('<?=$page;?>');
}

-->
</script>

<form name="f" method="post">
<div class="title">고객 조회</div>
<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="90px">
		<col width="60px">
		<col width="140px">
		<col width="60px">
		<col width="100px">
		<col width="60px">
		<col width="120px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td>
			<?
				if ($_SESSION["userLevel"] == "A"){
				?>	<input name="find_center_code" type="text" value="<?=$find_center_code;?>" maxlength="15" class="no_string" style="width:120px;" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_clientList('<?=$page;?>');}" onkeyup="if(event.keyCode==13){_clientList('<?=$page;?>');}" onFocus="this.select();"><?
				}else{
				?>	<span style="padding-left:5px;"><?=$_SESSION["userCenterCode"];?></span><?
				}
			?>
			</td>
			<th class="center">기관명</th>
			<td >
			<?
				if ($_SESSION["userLevel"] == "A"){
				?>	<input name="find_center_name" type="text" value="<?=$find_center_name;?>" maxlength="20" onkeypress="if(event.keyCode==13){_clientList('<?=$page;?>');}" style="width:100%;" onFocus="this.select();"><?
				}else{
				?>	<span style="padding-left:5px;"><?=$_SESSION["userCenterName"];?></span><?
				}
			?>
			</td>
			<th class="center">이용상태</th>
			<td class="last">
				<select id="find_su_stat" name="find_su_stat" style="width:auto;">
					<option value="all">전체</option>
					<option value="1" <?=$find_su_stat == "1" ? "selected" : "";?>>이용</option>
					<option value="9" <?=$find_su_stat == "9" ? "selected" : "";?>>중지</option>
				</select>
			</td>
			
			<th class="center">서비스</th>
			<td >
			<?
				$kind_list = $conn->kind_list($find_center_code, $gHostSvc['voucher']);

				echo '<select name=\'find_center_kind\' style=\'width:auto;\'>';
				echo '<option value=\'all\'>전체</option>';

				foreach($kind_list as $i => $k){
					echo '<option value=\''.$k['code'].'\' '.($find_center_kind == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
				}

				echo '</select>';
			?>
			</td>

			<td class="right other" style="vertical-align:top; padding-top:2px;">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="_list_client('1');">조회</button></span>
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="reg_client('<?=$find_center_code;?>','<?=$kind;?>','','');">등록</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">고객명</th>
			<td>
				<input name="find_su_name" type="text" value="<?=$find_su_name;?>" style="width:80px;" onkeyup="if(event.keyCode==13){_clientList('<?=$page;?>');}" onFocus="this.select();">
			</td>
			<th class="center">주민번호</th>
			<td >
				<input name="find_su_ssn" type="text" value="<?=$find_ssn;?>" maxlength="13" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_list_client('<?=$page;?>');}" class="phone" style="ime-mode:disabled;" onfocus="__replace(this, '-', '');" onblur="__getSsn(this);" >
			</td>
			<th class="center">연락처</th>
			<td >
				<input name="find_su_phone" type="text" value="<?=$myF->phoneStyle($find_su_phone);?>" maxlength="11" class="phone" style="ime-mode:disabled;" onKeyDown="if(event.keyCode!=13){__onlyNumber(this);}else{_list_client('<?=$page;?>');}" onkeyup="if(event.keyCode==13){_clientList('<?=$page;?>');}" onfocus="__replace(this, '-', '');" onblur="__getPhoneNo(this);">
			</td>
			<th class='center bottom'>팀장명</th>
			<td class='left bottom last'><div style='float:left;  width:auto; height:100%; padding-top:1px;'><span class='btn_pack find' onclick='findTeam();'></span></div><div style='width:auto; height:100%; padding-top:2px;'><!--span id='strTeam' name="strTeam" class='bold'><?=$strTeam;?></span--><input id="strTeam" name="strTeam" type="text" style="width:75px; padding:0; background-color:#eeeeee;" value="<?=$strTeam;?>" readonly /></div></td>
			
			<td class="right other"><span class="btn_pack m icon">
				<span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="_sugup_chk_popup('<?=$find_center_code?>','<?=$find_center_name?>','<?=$find_center_kind?>','<?=$find_su_name?>','<?=$find_su_phone?>','<?=$find_su_stat?>','<?=$find_ssn?>','<?=$sst?>','<?=$sod?>','<?=$sfl?>','<?=$strTeam?>');">엑셀</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="margin-top:-1px;">
	<colgroup>
		<col width="35px">
		<?
			if ($_SESSION["userLevel"] == "A"){?>
				<col width="110px"><?
			}
		?>
		<col width="90px">
		<col width="60px">
		<col width="80px">
		<col width="60px">
		<col width="45px">
		<col width="50px">
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="85px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<?
				if ($_SESSION["userLevel"] == "A"){?>
					<th class="head">기관명</th><?
				}
			?>
			<th class="head" title="수급자명을 클릭하면 상세한 수급자 정보를 조회할 수 있습니다."><?=align('name','','',$find);?><span style="font-weight:bold;">고객명</span></a></th>
			<th class="head">서비스</th>
			<th class="head">연락처</th>
			<th class="head"><?=align('mem_nm1','','',$find)?><span style="font-weight:bold;">담당직원</span></a></th>
			<th class="head">등급
				<!--select name="Lvl" style="width:auto;">
					<option value="1" <? if($find_lvl == '1'){ echo 'selected'; } ?>>1등급</option>
					<option value="2" <? if($find_lvl == '2'){ echo 'selected'; } ?>>2등급</option>
					<option value="3" <? if($find_lvl == '3'){ echo 'selected'; } ?>>3등급</option>
					<option value="9" <? if($find_lvl == '9'){ echo 'selected'; } ?>>일반</option>
				</select-->
			</th>
			<th class="head">고객구분</th>
			<th class="head">동거</th>
			<th class="head"><span title="계약시작일자"><?=align('from_dt','','',$find)?><span style="font-weight:bold;">계약시작</span></a></span></th>
			<th class="head"><span title="계약종료일자"><?=align('to_dt','','',$find)?><span style="font-weight:bold;">계약종료</span></a></span></th>
			<th class="head"><span title="인정만료일자"><?=align('lvl_to_dt','','',$find)?><span style="font-weight:bold;">인정만료</span></a></span></th>
			<th class="head">보호자연락처</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?

		if($find_su_stat == 'all'){
			if($find_center_kind != 'all'){
				$join = 'inner';
			}else {
				$join = 'left';
			}

			if (!Empty($find_su_name) ||
				!Empty($find_su_ssn) ||
				!Empty($find_su_phone)){
				$join = 'inner';
			}
		}
	
		
		$query='select mst.jumin
				,      mst.name
				,      svc.svc_cd
				,      svc.svc_stat
				,      svc.svc_reason
				,      mst.mobile
				,      mst.mem_cd1
				,      mst.mem_nm1
				,      mst.mem_nm2
				,      mst.phone
				,      mst.partner
				,	   mst.nogood
				,      svc.from_dt
				,	   svc.to_dt
				,	   lvl.level as level
				,      lvl.to_dt as lvl_to_dt
				,      case lvl.svc_cd when \'0\' then case lvl.level when \'9\' then \'일반\' else concat(lvl.level,\'등급\') end
									   when \'4\' then case dis.svc_val when \'3\' then concat(dis.svc_lvl,\'구간\') else  concat(dis.svc_lvl,\'등급\') end else \'\' end as lvl_nm
				,      case kind.kind when \'3\' then \'기초\'
									  when \'2\' then \'의료\'
									  when \'4\' then \'경감\' else \'일반\' end as kind_nm
				,      case lvl.svc_cd when \'0\' then lvl.to_dt else \'\' end as no_dt
				,      case lvl.svc_cd when \'0\' then datediff(date_add(date_format(lvl.to_dt,\'%Y-%m-%d\'), interval -3 month), date_format(now(),\'%Y-%m-%d\')) else 0 end as day_cnt
				,	   opt.bill_phone';

		$query .= ' , CAST(CASE WHEN T1.income_gbn = \'1\' OR T1.income_gbn = \'2\' OR T1.income_gbn = \'3\' OR T1.income_gbn = \'4\' THEN \'바우처\'
											WHEN T1.income_gbn = \'7\' THEN \'지자체\'
											WHEN T1.income_gbn = \'9\' THEN \'일발\' ELSE T1.income_gbn END AS char) AS income_gbn ';

		$query .= '
				  from (
					   select org_no
					   ,	  min(svc_cd) as svc_cd
					   ,      jumin
					   , seq
					   , case when date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						and date_format(now(),\'%Y%m%d\') <= date_format(to_dt, \'%Y%m%d\') then from_dt else max(from_dt) end as from_dt
						, case when date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						and date_format(now(),\'%Y%m%d\') <= date_format(to_dt, \'%Y%m%d\') then to_dt else max(to_dt) end as to_dt
					   ,      svc_stat
					   ,      svc_reason
						 from client_his_svc
						where org_no = \''.$code.'\'';

						if($find_su_stat == '1'){
							$query .= ' and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
										and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')';
						}

						if ($find_center_kind != 'all')
							$query .= ' and svc_cd = \''.$find_center_kind.'\'';

						if (!empty($find_su_ssn))
							$query .= ' and left(jumin,'.strlen($find_su_ssn).') = \''.$find_su_ssn.'\'';

						if ($find_su_stat != 'all'){
							if ($find_su_stat == '1')
								$query .= ' and svc_stat = \'1\'';
							else
								$query .= ' and svc_stat != \'1\'';
						}

		$query .= '	    group by jumin
						order by case when date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
									   and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\') then 1 else 2 end, seq desc) as svc ';

			$query .= ' LEFT JOIN vuc_baby_due AS T1
						ON T1.org_no = svc.org_no
						AND T1.jumin = svc.jumin
						AND	T1.svc_seq = svc.seq ';

		$query .= $join.' join (
					   select m03_mkind as kind
					   ,      m03_jumin as jumin
					   ,      m03_name as name
					   ,	  case when m03_hp != \'\' then m03_hp ELSE m03_tel end as mobile
					   ,      m03_yoyangsa1 AS mem_cd1
					   ,      m03_yoyangsa1_nm as mem_nm1
					   ,      m03_yoyangsa2_nm as mem_nm2
					   ,      m03_yboho_phone as phone
					   ,	  m03_stat_nogood as nogood
					   ,	  m03_partner     as partner
						 from m03sugupja
						where m03_ccode = \''.$code.'\'';

						if (!empty($find_su_phone))
							$query .= ' and left(case when m03_hp != \'\' then m03_hp ELSE m03_tel end ,'.strlen($find_su_phone).') = \''.$find_su_phone.'\'';

						if (!empty($find_su_name))
							$query .= ' and m03_name >= \''.$find_su_name.'\'';
		$query .= '
					   ) as mst
					on svc.jumin = mst.jumin
				   and svc.svc_cd = mst.kind
				  left join (
					   select jumin
					   ,      svc_cd
					   ,      level
					   ,      from_dt
					   ,      MAX(to_dt) AS to_dt
						 from client_his_lvl
						where org_no = \''.$code.'\'
						  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
						GROUP BY jumin, svc_cd
					   ) as lvl
					on svc.jumin  = lvl.jumin
				   and svc.svc_cd = lvl.svc_cd';

		$query .= ' left join (
					   select jumin
					   ,      kind
					   ,      from_dt
					   ,      to_dt
						 from client_his_kind
						where org_no = \''.$code.'\'
						  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
					   ) as kind
					on svc.jumin = kind.jumin
				  left join (
					   select jumin
					   ,	  svc_val
					   ,      svc_lvl
					   ,      from_dt
					   ,      to_dt
						 from client_his_dis
						where org_no = \''.$code.'\'
						  and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						  and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
					   ) as dis
					on svc.jumin = dis.jumin
				left join client_option as opt
				  on opt.org_no = \''.$code.'\'
				 and opt.jumin = mst.jumin
				left join ( select jumin, yname
							from client_his_team as team
							left join ( select min(m02_mkind) as kind, m02_yjumin, m02_yname as yname
										from   m02yoyangsa            
										where  m02_ccode = \''.$code.'\'
										group by m02_yjumin) as mem             
							on    mem.m02_yjumin = team.team_cd 
							where  team.org_no = \''.$code.'\'
							and    date_format(now(),\'%Y%m\') >= team.from_ym
							and    date_format(now(),\'%Y%m\') <= team.to_ym 
							and    team.del_flag = \'N\'
							group  by jumin) as yoy
				on   yoy.jumin  = mst.jumin  
				where mst.jumin is not null';
		
		if($strTeam){		
			$query .= ' and yname = \''.$strTeam.'\'';
		}

		$sql = 'select count(*)
				  from ('.$query.') as t';
		
		
		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:_clientList',
			'curPageNum'	=> $page,
			'pageVar'		=> 'page',
			'extraVar'		=> '',
			'totalItem'		=> $total_count,
			'perPage'		=> $page_count,
			'perItem'		=> $item_count,
			'prevPage'		=> '[이전]',
			'nextPage'		=> '[다음]',
			'prevPerPage'	=> '[이전'.$page_count.'페이지]',
			'nextPerPage'	=> '[다음'.$page_count.'페이지]',
			'firstPage'		=> '[처음]',
			'lastPage'		=> '[끝]',
			'pageCss'		=> 'page_list_1',
			'curPageCss'	=> 'page_list_2'
		);

		$pageCount = $page;

		if ($pageCount == "") $pageCount = 1;

		$pageCount = (intVal($pageCount) - 1) * $item_count;


		//동거가족
		$sql = 'SELECT cf_jumin AS jumin
				,      COUNT(cf_jumin) AS cnt
				  FROM client_family
				 WHERE org_no = \''.$code.'\'
				 GROUP BY cf_jumin';
		$arrFamily = $conn->_fetch_array($sql,'jumin');


		$sql = '';

		$sql .= 'select jumin
				,      svc_cd as kind
				,	   level
				,      name
				,      mobile
				,      mem_cd1
				,      mem_nm1
				,      mem_nm2
				,      lvl_nm';

		//$sql .= ', kind_nm';
		$sql .= ', CASE WHEN svc_cd = \'3\' AND income_gbn IS NOT NULL THEN income_gbn ELSE kind_nm END kind_nm';

		$sql .= '
				,      no_dt
				,      day_cnt
				,      from_dt
				,	   to_dt
				,      phone
				,	   nogood
				,      partner
				,      svc_stat as stat
				,      svc_reason as reason
				,      lvl_to_dt
				,	   bill_phone
				  from ('.$query.') as t';

		if (!$sst) {
			$sql .=	 ' order by name, jumin,svc_cd';
		}else {
			$sql .= " order by $sst $sod ";
		}

		$sql .= ' limit '.$pageCount.','.$item_count;
		
		//if($debug) echo nl2br($sql);

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();
		$lsChkDt = $myF->dateAdd('month', 3, date('Y-m-d'), 'Y-m-d');
		$t_no_dt = '';
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			
			/*
			$year = date('Y', mktime());
			$month = date('m', mktime());
			*/
			
			//요양사의 나이
			$yoyAge = $myF->ManAge(str_replace('-','', $myF->issToBirthday($row['mem_cd1'])));
			
			$liFamilyCnt = $arrFamily[$row['jumin']]['cnt'];
			
			
			if ($liFamilyCnt > 0){
				if($row['nogood'] == 'Y'){
					$family_gbn = '90분';
				}else if ($row['partner'] == 'Y' && $yoyAge >= 65){
					$family_gbn = '90분';
				}else{
					$family_gbn = '60분';
				}
			}else{
				$family_gbn = '';
			}


			if ($row['stat'] == '1'){
				$stat = '이용';
			}else{
				if ($row['kind'] == '0'){
					$reason = array(
							'01'=>'계약해지'
						,	'02'=>'보류'
						,	'03'=>'사망'
						,	'04'=>'타기관이전'
						,	'05'=>'등외판정'
						,	'06'=>'입원'
						,	'99'=>'기타'
					);
				}else{
					$reason = array(
							'01'=>'본인포기'
						,	'02'=>'사망'
						,	'03'=>'말소'
						,	'04'=>'전출'
						,	'05'=>'미사용'
						,	'06'=>'본인부담금미납'
						,	'07'=>'사업종료'
						,	'08'=>'자격종료'
						,	'09'=>'판정결과반영'
						,	'10'=>'자격정지'
						,	'99'=>'기타'
					);
				}

				//$stat = $reason[$row['reason']];
				$stat = '중지';
			}


			#if ($row['day_cnt'] > 0)
			#	$noDt = $myF->dateStyle($loInjungLastDt[$row['jumin']]['dt']/*$row['no_dt']*/,'.');
			#else
			#	$noDt = '<span style=\'color:red;\'>'.$myF->dateStyle($loInjungLastDt[$row['jumin']]['dt']/*$row['no_dt']*/,'.').'</span>';

			$no_dt[$i] = $loInjungLastDt[$row['jumin']]['dt'];

			if ($loInjungLastDt[$row['jumin']]['dt'] > $lsChkDt)
				$noDt = $myF->dateStyle($row['lvl_to_dt'],'.');
			else
				$noDt = '<span style=\'color:red;\'>'.$myF->dateStyle($loInjungLastDt[$row['jumin']]['dt'],'.').'</span>';

			#현금영수증발행연락처
			if ( $gDomain == 'dolvoin.net' )
				$bill_phone = $myF->phoneStyle($row['bill_phone']);
			else
				$bill_phone = '';
			

			if($row['lvl_nm'] == '99구간'){
				$row['lvl_nm'] = '특례';
			}


			?>
			<tr>
				<td class="center"><?=$pageCount + ($i + 1);?></td>
				<td class="left" id="tbody_<?=$i?>"><div class="nowrap" style="width:90px; text-align:left;"><a href="#" onclick="reg_client('<?=$code;?>','<?=$row['kind'];?>','<?=$ed->en($row['jumin']);?>');"><?=(!Empty($row['name']) ? $row['name'] : '이름없음');?></a></div></td>
				<td class="center"><div class="nowrap" style="width:40px; text-align:left;"><?=$conn->kind_name_svc($row['kind']);?></div></td>
				<td class="center"><?=$myF->phoneStyle($row['mobile'],'.');?></td>
				<td class="left"><div class="nowrap" style="width:50px; text-align:left;"><?=$row['mem_nm1'];?></div></td>
				<td class="center"><?=$row['lvl_nm'];?></td>
				<td class="center"><div class="nowrap" style="width:60px; text-align:center;"><?=$row['kind_nm'];?></div></td>
				<td class="center"><?=$family_gbn;?></td>
				<td class="center"><?=$myF->dateStyle($row['from_dt'],'.');?></td>
				<td class="center"><?=$myF->dateStyle($row['to_dt'],'.');?></td>
				<td class="center"><?=$noDt;?></td>
				<td class="center"><?=$myF->phoneStyle($row['phone'],'.');?></td>
				<td class="center last"><?=$bill_phone;?></td>
			</tr><?
		}

		/*
		if($debug){
			for($i=0; $i<$rowCount; $i++){
				$Dt = array(
					$i => $no_dt[$i],
				);
			}

			echo rsort($Dt);
		}
		*/


		$conn->row_free();

		if ($rowCount == 0){?>
			<tr>
				<td class="center last" colspan="11"><?=$myF->message('nodata','N');?></td>
			</tr><?
		}
	?>
</table>
<div style="text-align:left;">
	<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
	<div style="width:100%; text-align:center;">
	<?
		$paging = new YsPaging($params);
		$paging->printPaging();
	?>
	</div>
</div>
<input name="code"			type="hidden" value="">
<input name="kind"			type="hidden" value="">
<input name="jumin"			type="hidden" value="">
<input name="page"			type="hidden" value="<?=$page;?>">
<input name="modify_type"	type="hidden" value="">
<!-- 오름,내림차순정렬 변수 담기-->
<input name="sst"			type="hidden" value="<?=$sst?>">
<input name="sod"			type="hidden" value="<?=$sod?>">
<input name="sfl"			type="hidden" value="<?=$sfl?>">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");


	//오름차순,내림차순 정렬
	function align($col, $query_string='', $flag='asc', $find){

		$q1 = "sst=$col";
		if ($flag == 'asc'){	//오름차순
			$q2 = 'sod=asc';
			if ($find[1] == $col){
				if ($find[2] == 'asc'){
					$q2 = 'sod=desc';
				}
			}
		}else {					//내림차순
			$q2 = 'sod=desc';
			if ($find[1] == $col){
				if ($find[2] == 'desc'){
					$q2 = 'sod=asc';
				}
			}
		}

		return "<a href='$_SERVER[PHP_SELF]?$query_string&$q1&$q2&sfl=$find[1]&find_center_kind=$find[4]&find_su_name=$find[6]&find_su_ssn=$find[7]&find_su_phone=$find[8]&find_su_stat=$find[9]&page=$find[10]'>";
	}

?>

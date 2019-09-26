<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("iljung_config.php");

	ob_start();

	$wrt_mode = $myF->get_iljung_mode();

	##########################################################
	#
	# 일정등록을 위한 정보를 출력한다.
	#
	##########################################################

	$code		= $_POST['code'];
	$key		= $_POST['key'];
	$svc_id		= $_POST['svc_id'];
	$center_nm	= $conn->center_name($code);
	$year		= $_POST['year'];
	$month		= $_POST['month'];
	$month		= (intval($month) < 10 ? '0' : '').intval($month);
	$day		= $_POST['day'];
	$day		= (intval($day) < 10 ? '0' : '').intval($day);


	######################################################
	#
	# 모드

	$http = $_SERVER['HTTP_REFERER'];
	$http = substr($http, strpos($http,'.php?') + strlen('.php?'));

	parse_str($http, $method);

	$mode = $method['mMode'];

	#
	######################################################

	if ($mode == 'ADD_CONF'){
		$jumin = $ed->de($key);
		$sql = 'select m03_key
				  from m03sugupja
				 where m03_ccode = \''.$code.'\'
				   and m03_jumin = \''.$jumin.'\'
				   and m03_mkind =   '.$conn->_client_kind();

		$key = $conn->get_data($sql);
	}

	if (!empty($svc_id)){
		$kind     = $conn->kind_code($conn->kind_list($code, true), $svc_id);
		$tmp_kind = $conn->get_data("select min(m03_mkind) from m03sugupja where m03_ccode = '$code' and m03_key = '$key' and m03_del_yn = 'N'");
	}else{
		$kind = '0';
	}

	if ($kind == '0'){
		$kind   = $conn->get_data("select min(m03_mkind) from m03sugupja where m03_ccode = '$code' and m03_key = '$key'");
		#$kind   = $conn->get_data("select min(m03_mkind) from m03sugupja where m03_ccode = '$code' and m03_key = '$key' and '$year$month' between left(m03_sdate,6) and left(m03_edate,6)");
		$svc_id = $conn->kind_code($conn->kind_list($code, true), $kind, 'id');
	}

	$client = $conn->get_array("select m03_name, m03_jumin from m03sugupja where m03_ccode = '$code' and m03_mkind = '$kind' and m03_key = '$key'");
	$name   = $client[0];
	$jumin  = $client[1];

	//if (empty($svc_id)) $svc_id = $_POST['svc_id'];

	unset($client);

	##########################################################
	#
	# 기관정보 및 고객명
	#
	##########################################################

	echo '<table id=\'tblCenterInfo\' class=\'my_table my_border_blue\' style=\'width:100%;'.($wrt_mode == 1 || ($wrt_mode > 50 && $wrt_mode < 60) ? 'border-bottom:none;' : '').($wrt_mode > 50 && $wrt_mode < 60 ? 'margin-top:'.__GAB__.';' : '').'\'>
			<colgroup>
				<col width=\'70px\'>
				<col>
				<col width=\'70px\'>
				<col width=\'100px\'>
				<col width=\'70px\'>
				<col width=\'100px\'>
			</colgroup>
			<tbody>
				<tr>
					<th>기관명</th>';
		if ($wrt_mode > 50 && $wrt_mode < 60){
			echo '	<td class=\'left last\'><span id=\'strCenterName\'>'.$center_nm.'</span>('.$code.')'.'</td>
					<td class=\'last\'></td>
					<td class=\'last\'></td>
					<td class=\'last\'></td>
					<td class=\'last\'></td>';
		}else{
			echo '
					<td class=\'left\'><span id=\'strCenterName\'>'.$center_nm.'</span>('.$code.')'.'</td><th>고객명</th>
					<td class=\'left\'>'.$name.'</td>
					<th>생년월일</th>
					<td class=\'left\'>'.$myF->issToBirthday($jumin,'.').'</td>';
		}

		echo '	</tr>
			</tbody>
		  </table>';

	##########################################################
	#
	# 고객정보
	#
	##########################################################

	if ($wrt_mode > 50 && $wrt_mode < 60){
		//바우처용
		$sql = "select m03_tel
				,      m03_hp
				,      m03_yboho_name
				,      m03_yboho_gwange
				,      m03_yboho_phone
				,      concat('[',substring(m03_post_no,1,3),'-',substring(m03_post_no,4,3),'] ', m03_juso1, ' ', m03_juso2)
				,      m03_skind
				,      m03_kupyeo_1
				  from m03sugupja
				 where m03_ccode  = '$code'
				   and m03_mkind  = '$kind'
				   and m03_jumin  = '$jumin'
				   and m03_del_yn = 'N'";

		$client = $conn->get_array($sql);
		$lvl_nm = $conn->income_nm($svc_id, $client[6]);

		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; border-top:none;\'>
				<colgroup>
					<col width=\'70px\'>
					<col width=\'120px\'>
					<col width=\'70px\'>
					<col width=\'70px\'>
					<col width=\'120px\'>
					<col width=\'70px\'>
					<col width=\'70px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th>고객명</th>
						<td class=\'left\'>'.$name.'</td>
						<th rowspan=\'2\'>연락처</th>
						<th>유선</th>
						<td class=\'left\'>'.$myF->phoneStyle($client[0]).'</td>
						<th rowspan=\'2\'>보호자</th>
						<th>성명/관계</th>
						<td class=\'left\'>'.$client[2].($client[3] != '' ? '/'.$client[3] : '').'</td>
					</tr>
					<tr>
						<th>주민번호</th>
						<td class=\'left\'>'.$myF->issStyle($jumin).'</td>
						<th>무선</th>
						<td class=\'left\'>'.$myF->phoneStyle($client[1]).'</td>
						<th>연락처</th>
						<td class=\'left\'>'.$myF->phoneStyle($client[4]).'</td>
					</tr>
					<tr>
						<th>주소</th>
						<td class=\'left\' colspan=\'7\'>'.$client[5].'</td>
					</tr>
					<tr>
						<th>소득등급</th>
						<td class=\'left\' colspan=\'2\'>'.$lvl_nm.'</td>
						<th>본인부담금</th>
						<td class=\'right\'>'.number_format($client[7]).'</td>
						<td class=\'left\' colspan=\'3\'></td>
					</tr>
				</tbody>
			  </table>';
	}

	##########################################################
	#
	# 제공 서비스 리스트 작성
	#
	##########################################################

	$tmp_list = $conn->kind_list($code, true);


	if ($lbTestMode){
		$sql = 'select svc_cd
				,      from_dt
				,      to_dt
				  from client_his_svc
				 /*
				 inner join m03sugupja
				    on m03_ccode  = org_no
				   and m03_mkind  = svc_cd
				   and m03_jumin  = jumin
				   and m03_del_yn = \'N\'
				 */
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'';

		if ($mode == 'ADD_CONF'){
			$sql .= ' and from_dt <= \''.$year.$month.$day.'\'
					  and to_dt   >= \''.$year.$month.$day.'\'';
		}else{
			$sql .= ' and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			          and date_format(to_dt,\'%Y%m\')   >= \''.$year.$month.'\'';
		}

		if ($gHostSvc['homecare']){
			$strKind .= '\'0\'';
		}

		if ($gHostSvc['voucher']){
			$strKind .= (!empty($strKind) ? ',' : '').'\'1\',\'2\',\'3\',\'4\'';
		}

		$strKind .= (!empty($strKind) ? ',' : '').'\'A\',\'B\',\'C\'';

		$sql .= ' and svc_cd in ('.$strKind.')
				order by svc_cd';
	}else{
		$sql = 'select m03_mkind
				  from m03sugupja
				 where m03_ccode  = \''.$code.'\'
				   and m03_jumin  = \''.$jumin.'\'
				   and left(m03_gaeyak_fm,6) <= \''.$year.$month.'\'
				   and left(m03_gaeyak_to,6) >= \''.$year.$month.'\'';

		if ($mode == 'ADD_CONF'){
			$sql .= ' and \''.$year.$month.$day.'\' between m03_sdate and m03_edate';
		}

		if ($gHostSvc['homecare']){
			$strKind .= '\'0\'';
		}

		if ($gHostSvc['voucher']){
			$strKind .= (!empty($strKind) ? ',' : '').'\'1\',\'2\',\'3\',\'4\'';
		}

		$strKind .= (!empty($strKind) ? ',' : '').'\'A\',\'B\',\'C\'';

		$sql .= ' and m03_mkind in ('.$strKind.')
				  /*and m03_del_yn = \'N\'*/
				order by m03_mkind';
	}

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	//이용서비스 리스트 작성
	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		foreach($tmp_list as $num => $k_list){
			if ($k_list['code'] == $row[0]){
				if ($lbTestMode){
				}else{
					if ($i == 0) $svc_id = $k_list['id'];
				}
				$kind_list[sizeof($kind_list)] = $k_list;
			}
		}
	}

	$conn->row_free();

	if ($row_count == 0){
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; border-top:none;\'>
				<colgroup>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<td class=\'center bold\'>이용서비스가 지정되지 않았습니다.<br>이용서비스를 먼저 지정하여 주십시오.</td>
					</tr>
				</tbody>
			  </table>';
		exit;
	}

	if ($wrt_mode == 1 || $mode == 'ADD' || $mode == 'ADD_CONF'){
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; border-top:none;'.($mode != 'ADD' && $mode != 'ADD_CONF' ? 'margin-bottom:'.__GAB__.'px;' : '').'\'>
				<colgroup>
					<col width=\'70px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th>제공서비스</th>
						<td class=\'left\'>';

		if (is_array($kind_list)){
			foreach($kind_list as $i => $k_list){
				if (!empty($svc_id)){
					if ($svc_id == $k_list['id'])
						$chk = 'checked';
					else
						$chk = '';
				}else{
					if ($i == 0)
						$chk = 'checked';
					else
						$chk = '';
				}

				$disabled = '';

				if ($mode == 'ADD'){
					if ($chk == '') $disabled = 'disabled=true';
				}

				echo '<input name=\'svc_id[]\' type=\'radio\' class=\'radio\' value=\''.$k_list['id'].'\' '.$disabled.' onclick=\'_set_current_svc(this.value);\' '.$chk.'>';

				if ($mode == 'ADD'){
					echo $k_list['name'];
				}else{
					echo '<a href=\'#\' onclick=\'_set_current_svc("'.$k_list['id'].'");\'>'.$k_list['name'].'</a>';
				}

				echo '<input name=\'svc_cd[]\' type=\'hidden\' value=\''.$k_list['code'].'\'>';
				echo '<input name=\'svc_nm[]\' type=\'hidden\' value=\''.$k_list['name'].'\'>';
			}
		}else{
			echo '이용서비스가 지정되지 않았습니다.<br>이용서비스를 먼저 지정하여 주십시오.';
		}

		echo '			</td>
					</tr>
				</tbody>
			  </table>';
	}else{
		foreach($kind_list as $i => $k_list){
			if ($k_list['id'] == $svc_id){
				echo '<input name=\'svc_id[]\' type=\'hidden\' value=\''.$k_list['id'].'\'>';
				echo '<input name=\'svc_cd[]\' type=\'hidden\' value=\''.$k_list['code'].'\'>';
				echo '<input name=\'svc_nm[]\' type=\'hidden\' value=\''.$k_list['name'].'\'>';
			}
		}
	}

	##########################################################
	#
	# 환경변수를 설정한다.
	#
	##########################################################

	echo '<input id=\'code\'  name=\'code\'  type=\'hidden\' value=\''.$code.'\'>';
	echo '<input id=\'kind\'  name=\'kind\'  type=\'hidden\' value=\''.$kind.'\'>';
	echo '<input id=\'jumin\' name=\'jumin\' type=\'hidden\' value=\''.$ed->en($jumin).'\'>';
	echo '<input id=\'key\'   name=\'key\'   type=\'hidden\' value=\''.$key.'\'>';
	echo '<input id=\'year\'  name=\'year\'  type=\'hidden\' value=\''.$year.'\'>';
	echo '<input id=\'month\' name=\'month\' type=\'hidden\' value=\''.$month.'\'>';
	echo '<input id=\'day\'   name=\'day\'   type=\'hidden\' value=\''.$day.'\'>';

	include_once("../inc/_db_close.php");

	$value = ob_get_contents();
	ob_end_clean();

	echo $value;

	unset($tmp_list);
	unset($k_list);
	unset($kind_list);
	unset($value);
?>
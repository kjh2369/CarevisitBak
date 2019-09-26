<?
	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	######################################################
	# 변경레이어
		#if ($view_type == 'read'){
		#}else{
		#	include('./client_reg_sub_layer.php');
		#}
	#
	######################################################

	if (isset($client)) unset($client);


	$arrStat = array('1'=>'이용','9'=>'중지');
	$useYn = 'Y';
	$today = date('Ymd');

	//계약유형
	if ($__CURRENT_SVC_ID__ == 11){
		$sql = 'SELECT	cont_type
				FROM	client_option
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'';
		$contType = $conn->get_data($sql);
	}

	$sql = 'select count(*)
			  from client_his_svc
			 where org_no   = \''.$code.'\'
			   and jumin    = \''.$jumin.'\'
			   and svc_cd   = \''.$__CURRENT_SVC_CD__.'\'
			   and date_format(from_dt,\'%Y%m%d\') <= date_format(now(),\'%Y%m%d\')
			   and date_format(to_dt,  \'%Y%m%d\') >= date_format(now(),\'%Y%m%d\')';
	$liSvcCnt = $conn->get_data($sql);

	$sql = 'select seq as seq
			,      svc_stat as stat
			,      svc_reason as reason
			,      from_dt as from_dt
			,      to_dt as to_dt
			  from client_his_svc
			 where org_no   = \''.$code.'\'
			   and jumin    = \''.$jumin.'\'
			   and svc_cd   = \''.$__CURRENT_SVC_CD__.'\'';

	if ($liSvcCnt > 0){
		$sql .= ' and date_format(from_dt,\'%Y%m%d\') <= date_format(now(),\'%Y%m%d\')
				  and date_format(to_dt,  \'%Y%m%d\') >= date_format(now(),\'%Y%m%d\')';
	}

	$sql .= ' order by seq desc
			  limit 1';

	$client = $conn->get_array($sql);

	if (!$client){
		$client['stat']    = null;
		$client['reason']  = null;
		$client['from_dt'] = null; //date('Y-m-d', mktime());
		$client['to_dt']   = null; //$myF->dateAdd('day', -1, $myF->dateAdd('year', 1, $client['from_dt'], 'Y-m-d'), 'Y-m-d');
		$client['seq']     = 0;

		$useYn = 'N';
	}else{
		if ($client['stat'] != '1'){
			$client['stat'] = '9';

			if (empty($client['reason']))
				$client['reason'] = '99';
		}
	}

	if ($lbPop){
		$html = '<div class="title title_border">'.$current_svc_nm.'</div>
				 <div id="loPopBody" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;">
					<table class=\'my_table my_border_blue\' style=\'width:'.$body_w.'; border-top:none; border-bottom:none;\'>';
	}else{
		$html = '<table class=\'my_table my_border_blue\' style=\'width:'.$body_w.'; border-bottom:none;\'>';
	}

	$html .= '<colgroup>
				<col width=\'80px\'>
				<col>
			  </colgroup>
			  <tbody>';

	if (!$lbPop){
		$html .= '<tr>
					<th class=\'head bold\' colspan=\'2\'>'.$current_svc_nm.'</th>
				  </tr>';
	}

	if ($__CURRENT_SVC_ID__ == 11){
		/*
		$arrReason = array(
				'01'=>'계약해지'
			,	'02'=>'보류'
			,	'03'=>'사망'
			,	'04'=>'타기관이전'
			,	'05'=>'등외판정'
			,	'06'=>'입원'
			,	'99'=>'기타'
		);
		*/
		$arrReason = array(
				'01'=>'계약해지'
			,	'02'=>'보류'
			,	'03'=>'사망'
			,	'04'=>'타업체이동'
			,	'05'=>'등외판정'
			,	'06'=>'입원'
			,	'07'=>'무리한서비스요구'
			,	'08'=>'단순서비스종료'
			,	'09'=>'근무자미투입'
			,	'10'=>'거주지이전'
			,	'11'=>'건강호전'
			,	'12'=>'부담금미납'
			,	'13'=>'지점이동'
			,	'14'=>'요양입소'
			,	'15'=>'주야간보호이용'
			,	'16'=>'서비스거부'
			,	'99'=>'기타'
		);
	}else if ($__CURRENT_SVC_ID__ >= 21 && $__CURRENT_SVC_ID__ <= 24){
		$arrReason = array(
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

	if ($__CURRENT_SVC_ID__ == 11){
		$html .=
			'<tr>
				<th>계약유형</th>
				<td>
					<label><input name="optContType" type="radio" class="radio clsData clsObjData" value="01" '.($contType == '01' ? 'checked' : '').'>전화,인터넷</label>
					<label><input name="optContType" type="radio" class="radio clsData clsObjData" value="02" '.($contType == '02' ? 'checked' : '').'>직접발굴</label>
					<label><input name="optContType" type="radio" class="radio clsData clsObjData" value="03" '.($contType == '03' ? 'checked' : '').'>지인소개</label><br>
					<label><input name="optContType" type="radio" class="radio clsData clsObjData" value="04" '.($contType == '04' ? 'checked' : '').'>근무자를 통한소개</label>
					<label><input name="optContType" type="radio" class="radio clsData clsObjData" value="05" '.($contType == '05' ? 'checked' : '').'>공단자료</label>
					<label><input name="optContType" type="radio" class="radio clsData clsObjData" value="06" '.($contType == '06' ? 'checked' : '').'>외부인수</label>
					<label><input name="optContType" type="radio" class="radio clsData clsObjData" value="07" '.($contType == '07' ? 'checked' : '').'>간병연계</label>
					<label><input name="optContType" type="radio" class="radio clsData clsObjData" value="08" '.($contType == '08' ? 'checked' : '').'>지점연계</label>
				</td>
			</tr>';
	}

	$html .= '<tr>
				<th>계약기간</th>
				<td>';

	if ($view_type == 'read'){
		$html .= '<div class=\'left\'>'.$myF->dateStyle($client['from_dt'],'.').' ~ '.$myF->dateStyle($client['to_dt'],'.').'</div>';
	}else{
		$html .= '<div class=\'left\' style=\'float:left; width:auto;\'>
					<span id=\'txtFrom_'.$__CURRENT_SVC_ID__.'\' value=\''.$client['from_dt'].'\' class=\'clsData\'>'.$myF->dateStyle($client['from_dt'],'.').'</span> ~ <span id=\'txtTo_'.$__CURRENT_SVC_ID__.'\' value=\''.$client['to_dt'].'\' class=\'clsData\'>'.$myF->dateStyle($client['to_dt'],'.').'</span>
				  </div>';

		if ($view_type != 'read'){
			$html .= '<input id=\''.$__CURRENT_SVC_ID__.'_gaeYakFm\' name=\''.$__CURRENT_SVC_ID__.'_gaeYakFm\' type=\'hidden\' value=\''.$client['from_dt'].'\' tag=\''.$client['from_dt'].'\'>';
			$html .= '<input id=\''.$__CURRENT_SVC_ID__.'_gaeYakTo\' name=\''.$__CURRENT_SVC_ID__.'_gaeYakTo\' type=\'hidden\' value=\''.$client['to_dt'].'\' tag=\''.$client['to_dt'].'\'>';
			$html .= '<div class=\'left\' style=\'float:left; width:auto; margin-left:35px;\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'_clientPeriodShow("'.$__CURRENT_SVC_ID__.'","'.$__CURRENT_SVC_CD__.'");\'>변경</button></span></div>';
		}
	}

	$html .= '	</td>
			  </tr>';


	$html .= '<tr>
				<th>이용상태</th>
				<td><div id=\'txtStat_'.$__CURRENT_SVC_ID__.'\' value=\''.$client['stat'].'\' class=\'left clsData\'>'.$arrStat[$client['stat']].'</div></td>
			  </tr>';

	if (($__CURRENT_SVC_ID__ > 30 && $__CURRENT_SVC_ID__ < 40) ||
		($__CURRENT_SVC_ID__ == 26)){
	}else{
		$html .= '<tr id=\'reasonTr_'.$__CURRENT_SVC_ID__.'\' style=\'display:'.($client['stat'] == '1' ? 'none' : '').';\'>
					<th>중지사유</th>
					<td><div id=\'txtReason_'.$__CURRENT_SVC_ID__.'\' value=\''.$client['reason'].'\' class=\'left clsData\'>'.$arrReason[$client['reason']].'</div></td>
				  </tr>';
	}
	/*
	$html .= '<tr>
				<th>계약기간</th>
				<td>';

	if ($view_type == 'read'){
		$html .= '<div class=\'left\'>'.$myF->dateStyle($client['from_dt'],'.').' ~ '.$myF->dateStyle($client['to_dt'],'.').'</div>';
	}else{
		$html .= '<div class=\'left\' style=\'float:left; width:auto;\'>
					<span id=\'txtFrom_'.$__CURRENT_SVC_ID__.'\' value=\''.$client['from_dt'].'\' class=\'clsData\'>'.$myF->dateStyle($client['from_dt'],'.').'</span> ~ <span id=\'txtTo_'.$__CURRENT_SVC_ID__.'\' value=\''.$client['to_dt'].'\' class=\'clsData\'>'.$myF->dateStyle($client['to_dt'],'.').'</span>
				  </div>';

		if ($view_type != 'read'){
			$html .= '<input id=\''.$__CURRENT_SVC_ID__.'_gaeYakFm\' name=\''.$__CURRENT_SVC_ID__.'_gaeYakFm\' type=\'hidden\' value=\''.$client['from_dt'].'\' tag=\''.$client['from_dt'].'\'>';
			$html .= '<input id=\''.$__CURRENT_SVC_ID__.'_gaeYakTo\' name=\''.$__CURRENT_SVC_ID__.'_gaeYakTo\' type=\'hidden\' value=\''.$client['to_dt'].'\' tag=\''.$client['to_dt'].'\'>';
			$html .= '<div class=\'left\' style=\'float:left; width:auto;\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'_clientPeriodShow("'.$__CURRENT_SVC_ID__.'","'.$__CURRENT_SVC_CD__.'");\'>변경</button></span></div>';
		}
	}

	$html .= '	</td>
			  </tr>';
	*/
	if ($view_type != 'read'){
		$html .= '<input id=\''.$__CURRENT_SVC_ID__.'_sugupStatus\' name=\''.$__CURRENT_SVC_ID__.'_sugupStatus\' type=\'hidden\' value=\''.$client['stat'].'\' tag=\''.$client['stat'].'\'>
				  <input id=\''.$__CURRENT_SVC_ID__.'_stopReason\' name=\''.$__CURRENT_SVC_ID__.'_stopReason\' type=\'hidden\' value=\''.$client['reason'].'\' tag=\''.$client['reason'].'\'>';
	}

	$html .= '	</tbody>
			  </table>
			  <input id=\'svcNm_'.$__CURRENT_SVC_ID__.'\' name=\''.$__CURRENT_SVC_ID__.'_svcNm\' type=\'hidden\' value=\''.$__CURRENT_SVC_NM__.'\'>
			  <input id=\'writeMode_'.$__CURRENT_SVC_ID__.'\' name=\''.$__CURRENT_SVC_ID__.'_writeMode\' type=\'hidden\' value=\''.($useYn == 'N' ? 1 : 2).'\'>';

	$html .= '<div id="svcSeq_'.$__CURRENT_SVC_ID__.'" value="'.$client['seq'].'" class="clsData" style="display:none;"></div>';

	echo $html;

	unset($client);

	if ($__CURRENT_SVC_ID__ >= 21 && $__CURRENT_SVC_ID__ <= 24){
		include('./client_reg_sub_staff.php');
	}
?>
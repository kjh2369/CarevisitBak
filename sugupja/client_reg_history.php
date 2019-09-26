<?
	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	##################################################################
	#
	# 이용서비시 변경내역리스트를 작성한다.
	#
	##################################################################

	//삭제여부
	$sql = "select ifnull(m03_bipay1, 0) as bipay1
			,      ifnull(m03_bipay2, 0) as bipay2
			,      ifnull(m03_bipay3, 0) as bipay3
			,      m03_expense_yn as exp_yn
			,      m03_expense_pay as exp_pay
			,      m03_del_yn as del_yn
			  from m03sugupja
			 where m03_ccode = '$code'
			   and m03_mkind = '$__CURRENT_SVC_CD__'
			   and m03_jumin = '$jumin'";

	$tmp_array = $conn->get_array($sql);

	$bipay1  = $tmp_array['bipay1'];
	$bipay2  = $tmp_array['bipay2'];
	$bipay3  = $tmp_array['bipay3'];
	$exp_yn  = $tmp_array['exp_yn'];
	$exp_pay = $tmp_array['exp_pay'];
	$svc_del_flag = $tmp_array['del_yn'];

	unset($tmp_array);
?>
<script language='javascript'>
<!--

// 복원
function restore(svc_id, svc_cd){
	if (!confirm('데이타를 복원하시겠습니까?')) return;

	var f = document.f;

	f.action = 'client_reg_restore.php?svc_id='+svc_id+'&svc_cd='+svc_cd;
	f.submit();
}

-->
</script>


<?
	/******************************

		비급여단가

	******************************/
	if ($__CURRENT_SVC_ID__ == 11 ||
		$__CURRENT_SVC_ID__ == 24){
		echo '<table class=\'my_table my_border_blue\' style=\'width:'.$body_w.'; border-top:none;'.($view_type == 'read' ? '' : 'border-bottom:none;').'\'>
				<colgroup>
					<col width=\'80px\'>
					<col width=\'80px\'>
					<col width=\'80px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th class=\'\' rowspan=\'3\'>비급여단가</th>';

		switch($__CURRENT_SVC_ID__){
			case 11:
				echo '<th>방문요양</th>';
				break;
			case 24:
				echo '<th>활동지원</th>';
				break;
		}

		if ($view_type != 'read'){
			echo '<td class=\'last\'><input id=\''.$__CURRENT_SVC_ID__.'_bipay1\' name=\''.$__CURRENT_SVC_ID__.'_bipay1\' type=\'text\' value=\''.number_format($bipay1).'\' class=\'number clsObjData\' style=\'width:100%;\'></td>';
		}else{
			echo '<td class=\'right last\'>'.number_format($bipay1).'</td>';
		}

		echo '			<td>/ 시간</td>
					</tr>
					<tr>
						<th>방문목욕</th>';

		if ($view_type != 'read'){
			echo '<td class=\'last\'><input id=\''.$__CURRENT_SVC_ID__.'_bipay2\' name=\''.$__CURRENT_SVC_ID__.'_bipay2\' type=\'text\' value=\''.number_format($bipay2).'\' class=\'number clsObjData\' style=\'width:100%;\'></td>';
		}else{
			echo '<td class=\'right last\'>'.number_format($bipay2).'</td>';
		}

		echo '			<td>/ 횟수</td>
					</tr>
					<tr>
						<th>방문간호</th>';

		if ($view_type != 'read'){
			echo '<td class=\'last\'><input id=\''.$__CURRENT_SVC_ID__.'_bipay3\' name=\''.$__CURRENT_SVC_ID__.'_bipay3\' type=\'text\' value=\''.number_format($bipay3).'\' class=\'number clsObjData\' style=\'width:100%;\'></td>';
		}else{
			echo '<td class=\'right last\'>'.number_format($bipay3).'</td>';
		}

		echo '			<td>/ 횟수</td>
					</tr>
				</tbody>
			  </table>';
	}else if ($__CURRENT_SVC_ID__ > 20 && $__CURRENT_SVC_ID__ < 30){
		echo '<table class=\'my_table my_border_blue\' style=\'width:'.$body_w.'; border-top:none;'.($view_type == 'read' ? '' : 'border-bottom:none;').'\'>
				<colgroup>
					<col width=\'80px\'>
					<col width=\'80px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th rowspan=\'3\'>비급여단가</th>';

		if ($view_type != 'read'){
			echo '<td class=\'last\'><input id=\''.$__CURRENT_SVC_ID__.'_bipay1\' name=\''.$__CURRENT_SVC_ID__.'_bipay1\' type=\'text\' value=\''.number_format($bipay1).'\' class=\'number clsObjData\' style=\'width:100%;\'></td>';
		}else{
			echo '<td class=\'right last\'>'.number_format($bipay1).'</td>';
		}

		echo '<td class=\'left\'>/ '.($__CURRENT_SVC_ID__ == 23 ? '일' : '시간').'</td>';

		if ($view_type != 'read'){
			echo '<input id=\''.$__CURRENT_SVC_ID__.'_bipay2\' name=\''.$__CURRENT_SVC_ID__.'_bipay2\' type=\'hidden\' class=\'clsObjData\' value=\'0\'>
				  <input id=\''.$__CURRENT_SVC_ID__.'_bipay3\' name=\''.$__CURRENT_SVC_ID__.'_bipay3\' type=\'hidden\' class=\'clsObjData\' value=\'0\'>';
		}

		echo '		</tr>
				</tbody>
			  </table>';
	}else{
		echo '<input id=\''.$__CURRENT_SVC_ID__.'_bipay1\' name=\''.$__CURRENT_SVC_ID__.'_bipay2\' type=\'hidden\' class=\'clsObjData\' value=\'0\'>
			  <input id=\''.$__CURRENT_SVC_ID__.'_bipay2\' name=\''.$__CURRENT_SVC_ID__.'_bipay2\' type=\'hidden\' class=\'clsObjData\' value=\'0\'>
			  <input id=\''.$__CURRENT_SVC_ID__.'_bipay3\' name=\''.$__CURRENT_SVC_ID__.'_bipay3\' type=\'hidden\' class=\'clsObjData\' value=\'0\'>';
	}

	/*************************

		비급여 실비지급여부

	*************************/
	if ($__CURRENT_SVC_ID__ > 10 && $__CURRENT_SVC_ID__ < 30){
		echo '<table class=\'my_table my_border_blue\' style=\'width:'.$body_w.'; border-top:none;'.($lbTestMode || $view_type == 'read' ? '' : 'border-bottom:none;').'\'>
				<colgroup>
					<col width=\'80px\'>
					<col width=\'85px\'>
					<col width=\'80px\'>
					<col>
				</colgroup>
				<tbody>';

		if ($view_type != 'read'){
			echo '	<tr>
						<th class=\'bottom\' rowspan=\'2\'>비급여<br>실비지급여부</th>
						<td class=\'\'><input id=\''.$__CURRENT_SVC_ID__.'_expense_y\' name=\''.$__CURRENT_SVC_ID__.'_expense_yn\' type=\'radio\' value=\'Y\' class=\'radio clsObjData\' onclick=\'set_expense_pay("'.$__CURRENT_SVC_ID__.'");\' '.($exp_yn == 'Y' ? 'checked' : '').'>예</td>
						<th class=\'\'>실비지급금액</th>
						<td class=\'\'><input id=\''.$__CURRENT_SVC_ID__.'_expense_pay\' name=\''.$__CURRENT_SVC_ID__.'_expense_pay\' type=\'text\' value=\''.number_format($exp_pay).'\' class=\'number clsObjData\' style=\'width:70px;\'></td>
					</tr>
					<tr>
						<td class=\'bottom last\'><input id=\''.$__CURRENT_SVC_ID__.'_expense_n\' name=\''.$__CURRENT_SVC_ID__.'_expense_yn\' type=\'radio\' value=\'N\' class=\'radio clsObjData\' onclick=\'set_expense_pay("'.$__CURRENT_SVC_ID__.'");\' '.($exp_yn != 'Y' ? 'checked' : '').'>아니오</td>
						<td class=\'bottom\' colspan=\'2\'></td>
					</tr>';
		}else{
			echo '	<tr>
						<th class=\'bottom\'>실비지급여부</th>
						<td class=\'center\'>'.($exp_yn == 'Y' ? '예' : '아니오').'</td>
						<th class=\'\'>실비지급금액</th>
						<td class=\'left\'>'.number_format($exp_pay).'</td>
					</tr>';
		}

		echo '	</tbody>
			  </table>';
	}else{
		echo '<input id=\''.$__CURRENT_SVC_ID__.'_expense_yn\' name=\''.$__CURRENT_SVC_ID__.'_expense_yn\'  type=\'hidden\' class=\'clsObjData\' value=\'N\'>
			  <input id=\''.$__CURRENT_SVC_ID__.'_expense_pay\' name=\''.$__CURRENT_SVC_ID__.'_expense_pay\' type=\'hidden\' class=\'clsObjData\' value=\'0\'>';
	}

	if ($lbTestMode){?>
		<span id="loPopLast" style=""></span><?
	}else{?>
		<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:1px solid #0e69b0;">
			<colgroup>
				<col width="80px">
				<col width="80px">
				<?
					if ($__CURRENT_SVC_ID__ == 24){
						echo '<col width=\'90px\'>';
						echo '<col width=\'90px\'>';
						$col_cnt = 2;
					}else{
						echo '<col width=\'120px\'>';
						$col_cnt = 1;
					}
				?>
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head bold" colspan="6">변경내역</th>
				</tr>
				<tr>
					<th class="head">적용일</th>
					<th class="head">종료일</th>
					<?
						if ($__CURRENT_SVC_ID__ == 11){
							echo '<th class=\'head\'>수급현황</th>';
						}else if ($__CURRENT_SVC_ID__ == 24){
							echo '<th class=\'head\' colspan=\'2\'>소득등급</th>';
						}else{
							echo '<th class=\'head\'>이용상태</th>';
						}
					?>
					<th class="head last" rowspan="2">비고</th>
				</tr>
				<tr>
				<?
					if ($__CURRENT_SVC_ID__ == 11){
						echo '<th class=\'head\'>등급</th>';
						echo '<th class=\'head\' colspan=\'2\'>수급자구분</th>';
					}else if ($__CURRENT_SVC_ID__ == 21){
						echo '<th class=\'head\'>서비스시간</th>';
						echo '<th class=\'head\' colspan=\'2\'>소득등급</th>';
					}else if ($__CURRENT_SVC_ID__ == 22 || $__CURRENT_SVC_ID__ == 23){
						echo '<th class=\'head\'>서비스구분</th>';
						echo '<th class=\'head\'>서비스시간</th>';
						echo '<th class=\'head\'>소득등급</th>';
					}else if ($__CURRENT_SVC_ID__ == 24){
						echo '<th class=\'head\'>이용상태</th>';
						echo '<th class=\'head\'>나이등급</th>';
						echo '<th class=\'head\'>장애인정등급</th>';
						echo '<th class=\'head\'>특례구분</th>';
					}else{
						echo '<th class=\'head\' colspan=\'2\'>서비스단가</th>';
						echo '<th class=\'head\'>서비스시간(일)</th>';
					}
				?>
				</tr>
			</thead>
			<tbody>
			<?
				if ($__CURRENT_SVC_ID__ == 11){
					$sql = "select m31_sdate
							,      m31_edate
							,      LVL.m81_name as level_nm
							,      STP.m81_name as kind_nm
							,      m31_bonin_yul
							,      m31_status
							,      m31_gaeyak_fm
							,      m31_gaeyak_to
							  from m31sugupja
							 inner join m81gubun as LVL
								on LVL.m81_gbn = 'LVL'
							   and LVL.m81_code = m31_level
							 inner join m81gubun as STP
								on STP.m81_gbn = 'STP'
							   and STP.m81_code = m31_kind
							 where m31_ccode = '$code'
							   and m31_mkind = '$__CURRENT_SVC_CD__'
							   and m31_jumin = '$jumin'
							 order by m31_sdate desc, m31_edate desc";
				}else if ($__CURRENT_SVC_ID__ > 10 && $__CURRENT_SVC_ID__ < 30){
					$sql = "select m31_sdate
							,      m31_edate";

					if ($__CURRENT_SVC_ID__ == 21){
						$sql .= ", concat(person_conf_time,'시간') as level_nm";
					}else if ($__CURRENT_SVC_ID__ == 22){
						$sql .= ", case m31_vlvl when 'V' then '방문'
												 else '주간보호' end as vlvl_nm
								 , concat(person_conf_time, case m31_vlvl when 'V' then '시간' else '일' end) as level_nm";
					}else if ($__CURRENT_SVC_ID__ == 23){
						$sql .= ", case m31_vlvl when '1' then '단태아'
												 when '2' then '쌍태아'
												 else '삼태아' end as vlvl_nm
								 , concat(person_conf_time, '일') as level_nm";
					}else if ($__CURRENT_SVC_ID__ == 24){
						$sql .= ", case m31_vlvl when 'A' then '성인'
												 when 'C' then '아동'
												 else '65세도래자' end as age_kind
								 , case m31_level when '1' then '1등급'
												  when '2' then '2등급'
												  when '3' then '3등급'
												  else '4등급' end as level_nm";
					}else{
						$sql .= ", m31_level as level_nm";
					}

					$sql .= "
							,      lvl_nm as kind_nm
							,      m31_bonin_yul
							,      m31_status
							,      m31_gaeyak_fm
							,      m31_gaeyak_to
							  from m31sugupja";

					if ($__CURRENT_SVC_ID__ == 21){
						$sql .= "
							 inner join suga_person
								on org_no    = m31_ccode
							   and person_id = m31_level
							   and person_code like 'VH0%'";
					}else if ($__CURRENT_SVC_ID__ == 22){
						$sql .= "
							 inner join suga_person
								on org_no               = m31_ccode
							   and person_id            = m31_level
							   and left(person_code, 3) = concat('VO', m31_vlvl)";
					}else if ($__CURRENT_SVC_ID__ == 23){
						$sql .= "
							 inner join suga_person
								on org_no               = m31_ccode
							   and person_id            = m31_level
							   and left(person_code, 2) = 'VM'";
					}

					$sql .= "
							 inner join income_lvl
								on lvl_id = m31_kind";

					if ($__CURRENT_SVC_ID__ == 21)
						$sql .= " and lvl_cd in ('21', '22', '99')";
					else if ($__CURRENT_SVC_ID__ == 22)
						$sql .= " and lvl_cd in ('21', '22', '23', '99')";
					else if ($__CURRENT_SVC_ID__ == 23)
						$sql .= " and lvl_cd in ('24', '25', '99')";
					else if ($__CURRENT_SVC_ID__ == 24)
						$sql .= " and lvl_cd in ('21', '22', '26', '27', '28', '29', '99')";

					$sql .= "
							 where m31_ccode = '$code'
							   and m31_mkind = '$__CURRENT_SVC_CD__'
							   and m31_jumin = '$jumin'
							 order by m31_sdate desc, m31_edate desc";
				}else{
					$sql = "select m31_sdate
							,      m31_edate
							,      m31_status
							,      m31_gaeyak_fm
							,      m31_gaeyak_to
							,      m31_kupyeo_1
							,      m31_kupyeo_2
							  from m31sugupja
							 where m31_ccode = '$code'
							   and m31_mkind = '$__CURRENT_SVC_CD__'
							   and m31_jumin = '$jumin'
							 order by m31_sdate desc, m31_edate desc";
				}

				$conn->query($sql);
				$conn->fetch();
				$row_count = $conn->row_count();

				for($ii=0; $ii<$row_count; $ii++){
					$row = $conn->select_row($ii);

					if ($ii % 2 == 1){
						$bg_color = 'background-color:#eeeeee;';
					}else{
						$bg_color = '';
					}

					echo '<tr>';
					echo '<td class=\'center\' style=\''.$bg_color.'\'>'.$myF->dateStyle($row['m31_sdate'],'.').'</td>';
					echo '<td class=\'center\' style=\''.$bg_color.'\'>'.$myF->dateStyle($row['m31_edate'],'.').'</td>';

					if ($__CURRENT_SVC_ID__ == 11){
						echo '<td class=\'left\' style=\''.$bg_color.'\'>'.$definition->SugupjaStatusGbn($row['m31_status']).'</td>';
					}else if ($__CURRENT_SVC_ID__ == 24){
						echo '<td class=\'left\' style=\''.$bg_color.'\' colspan=\''.$col_cnt.'\'>'.$row['kind_nm'].'</td>';
					}else{
						echo '<td class=\'left\' style=\''.$bg_color.'\' colspan=\''.$col_cnt.'\'>'.($row['m31_status'] == 1 ? '이용' : '중지').'</td>';
					}

					echo '<td class=\'other left top\' rowspan=\'2\' style=\'padding-top:5px;\'>';

					if ($ii == 0){
						echo '<img src=\'../image/btn_restore.png\' style=\'cursor:pointer;\' onclick=\'restore("'.$__CURRENT_SVC_ID__.'","'.$__CURRENT_SVC_CD__.'");\'>';
					}

					echo '</td>';
					echo '</tr>';
					echo '<tr>';

					if ($__CURRENT_SVC_ID__ == 22 || $__CURRENT_SVC_ID__ == 23){
						echo '<td class=\''.($__CURRENT_SVC_ID__ == 22 ? 'left' : 'center').'\' style=\''.$bg_color.'\'>'.$row['vlvl_nm'].'</td>';
						echo '<td class=\''.($__CURRENT_SVC_ID__ == 22 ? 'left' : 'center').'\' style=\''.$bg_color.'\'>'.$row['level_nm'].'</td>';
					}else if ($__CURRENT_SVC_ID__ == 24){
						echo '<td class=\''.($__CURRENT_SVC_ID__ == 22 ? 'left' : 'center').'\' style=\''.$bg_color.'\'>'.($row['m31_status'] == 1 ? '이용' : '중지').'</td>';
						echo '<td class=\''.($__CURRENT_SVC_ID__ == 22 ? 'left' : 'center').'\' style=\''.$bg_color.'\'>'.$row['age_kind'].'</td>';
						echo '<td class=\''.($__CURRENT_SVC_ID__ == 22 ? 'left' : 'center').'\' style=\''.$bg_color.'\'>'.$row['level_nm'].'</td>';
					}else if ($__CURRENT_SVC_ID__ > 30 && $__CURRENT_SVC_ID__ < 40){
						echo '<td class=\'right\' style=\''.$bg_color.'\' colspan=\'2\'>'.number_format($row['m31_kupyeo_1']).'</td>';
					}else{
						echo '<td class=\'center\' style=\''.$bg_color.'\'>'.$row['level_nm'].'</td>';
					}

					if ($__CURRENT_SVC_ID__ == 24){
						echo '<td class=\'left\' style=\''.$bg_color.'\'>'.$row['kind_nm'].'</td>';
					}else if ($__CURRENT_SVC_ID__ > 30 && $__CURRENT_SVC_ID__ < 40){
						echo '<td class=\'right\' style=\''.$bg_color.'\'>'.$row['m31_kupyeo_2'].'시간</td>';
					}else{
						echo '<td class=\'left\' colspan=\'2\' style=\''.$bg_color.'\'>'.$row['kind_nm'].($__CURRENT_SVC_ID__ == 11 ? '('.$row['m31_bonin_yul'].')' : '').'</td>';
					}

					echo '</tr>';
				}

				$conn->row_free();
			?>
			</tbody>
		</table><?
	}
?>
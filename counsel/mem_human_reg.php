<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$self	= $myF->_self();

	if ($self == 'mem_human_reg')
		$human_mode = 1;
	else
		$human_mode = 2;

	$code	= $_POST['code'];
	$kind	= $conn->center_kind($code);
	$ssn	= $ed->de($_POST['ssn']);
	$m_nm	= $conn->member_name($code, $ssn);
	$type   = 'M_HUMAN';


	/**************************************************

		교육이수

	**************************************************/
	$sql = 'select *
			  from counsel_edu
			 where org_no   = \''.$code.'\'
			   and edu_ssn  = \''.$ssn.'\'
			   and edu_type = \''.$type.'\'
			 union all
			select *
			  from counsel_edu
			 where org_no   = \''.$code.'\'
			   and edu_ssn  = \''.$ssn.'\'
			   and edu_type = \'1\'
			 order by edu_type';
	
	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$edu[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	$edu_cnt = sizeof($edu); #+ 1;

	
	/**************************************************

		2012.08.30 입사면담기록 데이터조회 
	
	**************************************************/
	
	$sql = 'select *
			  from counsel_record
			 where org_no       = \''.$code.'\'
			   and record_ssn  = \''.$ssn.'\'
			   and record_type = \''.$type.'\'
			 union all
			select *
			  from counsel_record
			 where org_no       = \''.$code.'\'
			   and record_ssn  = \''.$ssn.'\'
			   and record_type = \'1\'
			 order by record_type';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$rec[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	$rec_cnt = sizeof($rec); # + 1;


	/**************************************************

		자격사항

	**************************************************/
	$sql = 'select *
			  from counsel_license
			 where org_no       = \''.$code.'\'
			   and license_ssn  = \''.$ssn.'\'
			   and license_type = \''.$type.'\'
			 union all
			select *
			  from counsel_license
			 where org_no       = \''.$code.'\'
			   and license_ssn  = \''.$ssn.'\'
			   and license_type = \'1\'
			 order by license_type';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$lcs[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	$lcs_cnt = sizeof($lcs); # + 1;



	/**************************************************

		상벌사항

	**************************************************/
	$sql = 'select *
			  from counsel_rnp
			 where org_no   = \''.$code.'\'
			   and rnp_ssn  = \''.$ssn.'\'
			   and rnp_type = \''.$type.'\'';

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$rnp[$i] = $conn->select_row($i);
	}

	$conn->row_free();

	$rnp_cnt = sizeof($rnp);

	#if ($rnp_cnt == 0) $rnp_cnt = 1;
?>

<!-- 2012.08.30 입사면담기록 추가 -->
<table id="human_rec_tbl" class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="80px">
		<col width="80px">
		<col width="120px">
		<col width="120px">
		<col width="120px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody id="rec_human_my">
		<tr>
			<th id="rec_human_span" rowspan="<?=$rec_cnt + 1;?>">입사전기록</th>
			<th class="head">근무시작일</th>
			<th class="head">근무종료일</th>
			<th class="head">직장명</th>
			<th class="head">직 위</th>
			<th class="head">담당업무</th>
			<th class="head">급 여</th>
			<th class="head last">
				<div class="center" style="position:absolute;">비고</div>
				<div class="right" style="position:relative;"><span class="btn_pack m"><button type="button" onclick="human_rec.t_add_row();">추가</button></span></div>
			</th>
		</tr>
		<?
			$id = 1;
			
			for($i=0; $i<$rec_cnt; $i++){
				$id = $i + 1;

				echo '<tr id=\'rec_human_row_'.$id.'\'>';
				echo '<td class=\'center\'><input name=\'rec_human_fm_dt[]\' type=\'text\' value=\''.$rec[$i]['record_fm_dt'].'\' class=\'date\' onclick=\'_carlendar(this);\' ></td>';
				echo '<td class=\'center\'><input name=\'rec_human_to_dt[]\' type=\'text\' value=\''.$rec[$i]['record_to_dt'].'\' class=\'date\' onclick=\'_carlendar(this);\' ></td>';
				echo '<td class=\'center\'><input name=\'rec_human_job_nm[]\' type=\'text\' value=\''.$rec[$i]['record_job_nm'].'\' style=\'width:100%;\' ></td>';
				echo '<td class=\'center\'><input name=\'rec_human_position[]\' type=\'text\' value=\''.$rec[$i]['record_position'].'\' style=\'width:100%;\' ></td>';
				echo '<td class=\'center\'><input name=\'rec_human_task[]\' type=\'text\' value=\''.$rec[$i]['record_task'].'\' style=\'width:100%;\' ></td>';
				echo '<td class=\'center\'><input name=\'rec_human_salary[]\' type=\'text\' value=\''.$rec[$i]['record_salary'].'\' style=\'width:100%;\' ></td>';


				echo '<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'human_rec.t_delete_row("rec_human_row_'.$id.'", 0);\'>삭제</button></span></td>';
				echo '</tr>';

			}

		?>
	</tbody>
</table>

<table id="human_edu_tbl" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="100px">
		<col width="100px">
		<col width="130px">
		<col width="150px">
		<col width="80px">
		<col width="80px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody id="edu_human_my">
		<tr>
			<th id="edu_human_span" rowspan="<?=$edu_cnt + 1;?>">교육이수</th>
			<th class="head">교육구분</th>
			<th class="head">교육기관</th>
			<th class="head">교육명</th>
			<th class="head">교육시작일</th>
			<th class="head">교육종료일</th>
			<th class="head">교육시간</th>
			<th class="head last">
				<div class="left" style="position:absolute;">비고</div>
				<div class="right" style="position:relative;"><span class="btn_pack m"><button type="button" onclick="human_edu.t_add_row();">추가</button></span></div>
			</th>
		</tr>
		<?
			$add_row = true;
			$id = 1;
			$edu_head_cnt = 1;

			for($i=0; $i<$edu_cnt; $i++){
				if ($edu[$i]['edu_type'] == '1'){
					echo '<tr>';
					echo '<td class=\'left\'>';

					switch($edu[$i]['edu_gbn']){
						case '1':
							echo '돌봄관련교육';
							break;

						default:
							echo '기타교육';
							break;
					}

					echo '</td>';

					echo '<td class=\'left\'>'.$edu[$i]['edu_center'].'</td>';
					echo '<td class=\'left\'>'.$edu[$i]['edu_nm'].'</td>';
					echo '<td class=\'left\'>'.$edu[$i]['edu_from_dt'].'</td>';
					echo '<td class=\'left\'>'.$edu[$i]['edu_to_dt'].'</td>';
					echo '<td class=\'left\'>'.$edu[$i]['edu_time'].'</td>';
					echo '<td class=\'left\'>&nbsp;</td>';
					echo '</tr>';

					$edu_head_cnt ++;
				}else{
					echo '<tr id=\'edu_human_row_'.$id.'\'>';
					echo '<td class=\'center\'>';
					echo '<select name=\'edu_human_gbn[]\' style=\'width:auto;\'>';
					echo '<option value=\'1\''.($edu[$i]['edu_gbn'] == '1' ? 'selected' : '').'>돌봄관련교육</option>';
					echo '<option value=\'9\''.($edu[$i]['edu_gbn'] == '9' ? 'selected' : '').'>기타교육</option>';
					echo '</select>';
					echo '</td>';
					echo '<td class=\'center\'><input name=\'edu_human_center[]\' type=\'text\' value=\''.$edu[$i]['edu_center'].'\' style=\'width:100%;\'></td>';
					echo '<td class=\'center\'><input name=\'edu_human_name[]\' type=\'text\' value=\''.$edu[$i]['edu_nm'].'\' style=\'width:100%;\'></td>';
					echo '<td class=\'center\'><input name=\'edu_human_from_date[]\' type=\'text\' value=\''.$edu[$i]['edu_from_dt'].'\' style=\'width:100%;\' class=\'date\' onclick=\'_carlendar(this);\'></td>';
					echo '<td class=\'center\'><input name=\'edu_human_to_date[]\' type=\'text\' value=\''.$edu[$i]['edu_to_dt'].'\' style=\'width:100%;\' class=\'date\' onclick=\'_carlendar(this);\'></td>';
					echo '<td class=\'center\'><input name=\'edu_human_date[]\' type=\'text\' value=\''.$edu[$i]['edu_time'].'\' style=\'width:100%;\'></td>';

					/*
					if ($add_row){
						echo '<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'human_edu.t_add_row();\'>추가</button></span></td>';
					}else{
						echo '<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'human_edu.t_delete_row("edu_human_row_'.$id.'", 0);\'>삭제</button></span></td>';
					}
					*/
					echo '<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'human_edu.t_delete_row("edu_human_row_'.$id.'", 0);\'>삭제</button></span></td>';
					echo '</tr>';

					$add_row = false;
					$id ++;
				}
			}

			/*
			if ($add_row){
				echo '<tr id=\'edu_human_row_'.$id.'\'>';
				echo '<td class=\'center\'>';
				echo '<select name=\'edu_human_gbn[]\' style=\'width:auto;\'>';
				echo '<option value=\'1\''.($edu[$i]['edu_gbn'] == '1' ? 'selected' : '').'>돌봄관련교육</option>';
				echo '<option value=\'9\''.($edu[$i]['edu_gbn'] == '9' ? 'selected' : '').'>기타교육</option>';
				echo '</select>';
				echo '</td>';
				echo '<td class=\'center\'><input name=\'edu_human_center[]\' type=\'text\' value=\''.$edu[$i]['edu_center'].'\' style=\'width:100%;\'></td>';
				echo '<td class=\'center\'><input name=\'edu_human_name[]\' type=\'text\' value=\''.$edu[$i]['edu_nm'].'\' style=\'width:100%;\'></td>';
				echo '<td class=\'center\'><input name=\'edu_human_date[]\' type=\'text\' value=\''.$edu[$i]['edu_time'].'\' style=\'width:100%;\'></td>';
				echo '<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'human_edu.t_add_row();\'>추가</button></span></td>';
				echo '</tr>';
			}
			*/
		?>
	</tbody>
</table>

<!-- 자격 -->
<table id="human_lcs_tbl" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="100px">
		<col width="150px">
		<col width="150px">
		<col width="150px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody id="lcs_human_my">
		<tr>
			<th id="lcs_human_span" rowspan="<?=$lcs_cnt + 1;?>">자격</th>
			<th class="head">자격증종류</th>
			<th class="head">자격증번호</th>
			<th class="head">발급기관</th>
			<th class="head">발급일자</th>
			<th class="head last">
				<div class="center" style="position:absolute;">비고</div>
				<div class="right" style="position:relative;"><span class="btn_pack m"><button type="button" onclick="human_lcs.t_add_row();">추가</button></span></div>
			</th>
		</tr>
		<?
			$add_row = true;
			$id = 1;
			$lcs_head_cnt = 1;

			for($i=0; $i<$lcs_cnt; $i++){
				$id = $i + 1;

				if ($lcs[$i]['license_type'] == '1'){
					echo '<tr>';
					echo '<td class=\'left\'>'.$lcs[$i]['license_gbn'].'</td>';
					echo '<td class=\'left\'>'.$lcs[$i]['license_no'].'</td>';
					echo '<td class=\'left\'>'.$lcs[$i]['license_center'].'</td>';
					echo '<td class=\'center\'>'.$lcs[$i]['license_dt'].'</td>';
					echo '<td class=\'left\'>&nbsp;</td>';
					echo '</tr>';

					$lcs_head_cnt ++;
				}else{
					echo '<tr id=\'lcs_human_row_'.$id.'\'>';
					echo '<td class=\'center\'><input name=\'lcs_human_type[]\' type=\'text\' value=\''.$lcs[$i]['license_gbn'].'\' style=\'width:100%;\' onKeyDown=\'__enterFocus();\'></td>';
					echo '<td class=\'center\'><input name=\'lcs_human_no[]\' type=\'text\' value=\''.$lcs[$i]['license_no'].'\' style=\'width:100%;\' onKeyDown=\'__enterFocus();\'></td>';
					echo '<td class=\'center\'><input name=\'lcs_human_center[]\' type=\'text\' value=\''.$lcs[$i]['license_center'].'\' style=\'width:100%;\' onKeyDown=\'__enterFocus();\'></td>';
					echo '<td class=\'center\'><input name=\'lcs_human_date[]\' type=\'text\' value=\''.$lcs[$i]['license_dt'].'\' class=\'date\' onclick=\'_carlendar(this);\' onKeyDown=\'__enterFocus();\'></td>';

					/*
					if ($add_row){
						echo '<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'human_lcs.t_add_row();\'>추가</button></span></td>';
					}else{
						echo '<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'human_lcs.t_delete_row("lcs_human_row_'.$id.'", 0);\'>삭제</button></span></td>';
					}
					*/
					echo '<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'human_lcs.t_delete_row("lcs_human_row_'.$id.'", 0);\'>삭제</button></span></td>';
					echo '</tr>';

					$add_row = false;
					$id ++;
				}
			}

			/*
			if ($add_row){
				echo '<tr id=\'lcs_human_row_'.$id.'\'>';
					echo '<td class=\'center\'><input name=\'lcs_human_type[]\' type=\'text\' value=\''.$lcs[$i]['license_gbn'].'\' style=\'width:100%;\' onKeyDown=\'__enterFocus();\'></td>';
					echo '<td class=\'center\'><input name=\'lcs_human_no[]\' type=\'text\' value=\''.$lcs[$i]['license_no'].'\' style=\'width:100%;\' onKeyDown=\'__enterFocus();\'></td>';
					echo '<td class=\'center\'><input name=\'lcs_human_center[]\' type=\'text\' value=\''.$lcs[$i]['license_center'].'\' style=\'width:100%;\' onKeyDown=\'__enterFocus();\'></td>';
					echo '<td class=\'center\'><input name=\'lcs_human_date[]\' type=\'text\' value=\''.$lcs[$i]['license_dt'].'\' class=\'date\' onclick=\'_carlendar(this);\' onKeyDown=\'if(event.keyCode == 13){li_tbl.t_add_row();}\'></td>';
					echo '<td class=\'left last\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'human_lcs.t_add_row();\'>추가</button></span></td>';
					echo '</tr>';
			}
			*/
		?>
	</tbody>
</table>



<!-- 상벌 -->
<table id="human_rnp_tbl" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<colgroup>
		<col width="100px">
		<col width="80px">
		<col width="120px">
		<col width="400px">
		<col>
	</colgroup>
	<tbody id="rnp_human_my">
		<tr>
			<th id="rnp_human_span" rowspan="<?=$rnp_cnt + 1;?>">상벌</th>
			<th class="head">일자</th>
			<th class="head">구분</th>
			<th class="head">내용</th>
			<th class="head last">
				<div class="center" style="position:absolute;">비고</div>
				<div class="right" style="position:relative;"><span class="btn_pack m"><button type="button" onclick="human_rnp.t_add_row();">추가</button></span></div>
			</th>
		</tr>
		<?
			for($i=0; $i<$rnp_cnt; $i++){
				$id = $i + 1;

				echo '<tr id="rnp_human_row_'.$id.'">
						<td class="center"><input name="rnp_human_date[]" type="text" value="'.$rnp[$i]['rnp_date'].'" style="width:100%;" class="date" onKeyDown="__enterFocus();"></td>
						<td class="center">
							<input name="rnp_human_kind_'.$id.'" type="radio" class="radio" value="R" '.($rnp[$i]['rnp_gbn'] != 'P' ? 'checked' : '').'>포상
							<input name="rnp_human_kind_'.$id.'" type="radio" class="radio" value="P" '.($rnp[$i]['rnp_gbn'] == 'P' ? 'checked' : '').'>징계
						</td>
						<td class="center"><input name="rnp_human_cont[]" type="text" value="'.$rnp[$i]['rnp_comment'].'" style="width:100%;" onKeyDown="__enterFocus();"></td>';

				/*
				if ($i == 0){
					echo '<td class="left last"><span class="btn_pack m"><button type="button" onclick="human_rnp.t_add_row();">추가</button></span></td>';
				}else{
					echo '<td class="left last"><span class="btn_pack m"><button type="button" onclick="human_rnp.t_delete_row(\'rnp_human_row_'.$id.'\', 0);">삭제</button></span></td>';
				}
				*/
				echo '<td class="left last"><span class="btn_pack m"><button type="button" onclick="human_rnp.t_delete_row(\'rnp_human_row_'.$id.'\', 0);">삭제</button></span></td>';

				echo '</tr>';
			}
		?>
	</tbody>


<!-- 특이사항 -->
<!--table id="human_rnp_tbl" class="my_table my_border_blue" style="width:100%; margin-top:10px;">
	<tr>
		<td>
			<textarea name="special_stat" style="width:100%; height:100%;" onKeyDown="__checkMaxLength(this, 100);"></textarea>
		</td>
	</tr>
</table-->


<?
	###########################################################
	# 환경변수

	echo '<input name=\'human_code\'    type=\'hidden\' value=\''.$code.'\'>';
	echo '<input name=\'human_ssn\'     type=\'hidden\' value=\''.$ed->en($ssn).'\'>';
	echo '<input name=\'human_edu_cnt\' type=\'hidden\' value=\''.$edu_cnt.'\'>';
	echo '<input name=\'human_rec_cnt\' type=\'hidden\' value=\''.$rec_cnt.'\'>';
	echo '<input name=\'human_lcs_cnt\' type=\'hidden\' value=\''.$lcs_cnt.'\'>';
	echo '<input name=\'human_rnp_cnt\' type=\'hidden\' value=\''.$rnp_cnt.'\'>';

	echo '<input name=\'human_head_edu_cnt\' type=\'hidden\' value=\''.$edu_head_cnt.'\'>';
	echo '<input name=\'human_head_lcs_cnt\' type=\'hidden\' value=\''.$lcs_head_cnt.'\'>';

	###########################################################

	include_once("../inc/_db_close.php");
?>
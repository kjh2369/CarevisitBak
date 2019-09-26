<?
	/***********************************************

		서비스 고객여부확인

	***********************************************/
		$sql = 'select m03_mkind as kind, count(m03_mkind) as cnt
				  from m03sugupja
				 where m03_ccode        = \''.$mCode.'\'
				   and m03_sugup_status = \'1\'
				   and m03_del_yn       = \'N\'
				 group by m03_mkind';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$c_kind_cnt[$row['kind']] = $row['cnt'];
		}

		$conn->row_free();
	/**********************************************/
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<th class="head bold" colspan="2">기관구분</th>
	</thead>
	<tbody>
	<?
		/***********************************************

			방문용양

		***********************************************/
		if ($gHostSvc['homecare']){
			echo '<tr>';
			echo '<th>재가요양</th>';
			echo '<td>';

			if (empty($c_kind_cnt[0])){
				echo '<div class=\'left\' style=\'float:left; width:auto;\'><input id=\'kind_1\' name=\'kind_1\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("0", this.checked);\' '.($centerGubun[0] == 'Y'?'checked':'').'><label for=\'kind_1\'>재가요양</label></div>';
			}else{
				echo '<input id=\'kind_1\' name=\'kind_1\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 재가요양</div>';
			}

			echo '</td>';
			echo '</tr>';
		}else{
			echo '<input name=\'kind_1\' type=\'hidden\' value=\'N\'>';
		}



		/***********************************************

			바우처

		***********************************************/
		if ($gHostSvc['voucher']){
			echo '<tr>';
			echo '<th>바우처</th>';
			echo '<td>';

			/*
				if (empty($c_kind_cnt[1])){
					echo '<div class=\'left\' style=\'float:left; width:auto;\'><input id=\'kind_2_1\' name=\'kind_2_1\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("1", this.checked);\' '.($gubun_1[1] == 'Y'?'checked':'').'><label for=\'kind_2_1\'>가사간병</label></div>';
				}else{
					echo '<input id=\'kind_2_1\' name=\'kind_2_1\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 가사간병</div>';
				}

				if (empty($c_kind_cnt[2])){
					echo '<div class=\'left\' style=\'float:left; width:auto;\'><input id=\'kind_2_2\' name=\'kind_2_2\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("2", this.checked);\' '.($gubun_1[2] == 'Y'?'checked':'').'><label for=\'kind_2_2\'>노인돌봄</label></div>';
				}else{
					echo '<input id=\'kind_2_2\' name=\'kind_2_2\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 노인돌봄</div>';
				}

				if (empty($c_kind_cnt[3])){
					echo '<div class=\'left\' style=\'float:left; width:auto;\'><input id=\'kind_2_3\' name=\'kind_2_3\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("3", this.checked);\' '.($gubun_1[3] == 'Y'?'checked':'').'><label for=\'kind_2_3\'>산모신생아</label></div>';
				}else{
					echo '<input id=\'kind_2_3\' name=\'kind_2_3\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 산모신생아</div>';
				}

				if (empty($c_kind_cnt[4])){
					echo '<div class=\'left\' style=\'float:left; width:auto;\'><input id=\'kind_2_4\' name=\'kind_2_4\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("4", this.checked);\' '.($gubun_1[4] == 'Y'?'checked':'').'><label for=\'kind_2_4\'>장애인활동보조</label></div>';
				}else{
					echo '<input id=\'kind_2_4\' name=\'kind_2_4\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 장애인활동보조</div>';
				}
			*/

			//가사산병
			if ($gHostSvc['nurse']){
				if (empty($c_kind_cnt[1])){
					echo '<div class=\'left\' style=\'float:left; width:auto;\'><input id=\'kind_2_1\' name=\'kind_2_1\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("1", this.checked);\' '.($gubun_1[1] == 'Y'?'checked':'').'><label for=\'kind_2_1\'>가사간병</label></div>';
				}else{
					echo '<input id=\'kind_2_1\' name=\'kind_2_1\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 가사간병</div>';
				}
			}else{
				echo '<input id=\'kind_2_1\' name=\'kind_2_1\' type=\'hidden\' value=\'N\'>';
			}

			//노인돌봄
			if ($gHostSvc['old']){
				if (empty($c_kind_cnt[2])){
					echo '<div class=\'left\' style=\'float:left; width:auto;\'><input id=\'kind_2_2\' name=\'kind_2_2\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("2", this.checked);\' '.($gubun_1[2] == 'Y'?'checked':'').'><label for=\'kind_2_2\'>노인돌봄</label></div>';
				}else{
					echo '<input id=\'kind_2_2\' name=\'kind_2_2\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 노인돌봄</div>';
				}
			}else{
				echo '<input id=\'kind_2_2\' name=\'kind_2_2\' type=\'hidden\' value=\'N\'>';
			}

			//산모신생아
			if ($gHostSvc['baby']){
				if (empty($c_kind_cnt[3])){
					echo '<div class=\'left\' style=\'float:left; width:auto;\'><input id=\'kind_2_3\' name=\'kind_2_3\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("3", this.checked);\' '.($gubun_1[3] == 'Y'?'checked':'').'><label for=\'kind_2_3\'>산모신생아</label></div>';
				}else{
					echo '<input id=\'kind_2_3\' name=\'kind_2_3\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 산모신생아</div>';
				}
			}else{
				echo '<input id=\'kind_2_3\' name=\'kind_2_3\' type=\'hidden\' value=\'N\'>';
			}

			//장애인활동지원
			if ($gHostSvc['dis']){
				if (empty($c_kind_cnt[4])){
					echo '<div class=\'left\' style=\'float:left; width:auto;\'><input id=\'kind_2_4\' name=\'kind_2_4\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("4", this.checked);\' '.($gubun_1[4] == 'Y'?'checked':'').'><label for=\'kind_2_4\'>장애인활동보조</label></div>';
				}else{
					echo '<input id=\'kind_2_4\' name=\'kind_2_4\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 장애인활동보조</div>';
				}
			}else{
				echo '<input id=\'kind_2_4\' name=\'kind_2_4\' type=\'hidden\' value=\'N\'>';
			}
			
			echo '</td>';
			echo '</tr>';
		}else{
			echo '<input id=\'kind_2_1\' name=\'kind_2_1\' type=\'hidden\' value=\'N\'>';
			echo '<input id=\'kind_2_2\' name=\'kind_2_2\' type=\'hidden\' value=\'N\'>';
			echo '<input id=\'kind_2_3\' name=\'kind_2_3\' type=\'hidden\' value=\'N\'>';
			echo '<input id=\'kind_2_4\' name=\'kind_2_4\' type=\'hidden\' value=\'N\'>';
		}



		/***********************************************

			시설

		***********************************************/
		if ($gHostSvc['center']){
			echo '<tr>';
			echo '<th>시설</th>';
			echo '<td>';

			if (empty($c_kind_cnt[5])){
				echo '<input id=\'kind_3\' name=\'kind_3\' type=\'checkbox\' value=\'Y\' class=\'checkbox\' style=\'margin-right:0;\' onclick=\'set_kind("5", this.checked);\' '.($centerGubun[2] == 'Y'?'checked':'').'><label for=\'kind_3\'>시설</label>';
			}else{
				echo '<input id=\'kind_3\' name=\'kind_3\' type=\'hidden\' value=\'Y\'><div class=\'left\' style=\'float:left; width:auto;\'><span class=\'bold\' style=\'color:#ff0000;\'>√</span> 시설</div>';
			}

			echo '</td>';
			echo '</tr>';
		}else{
			echo '<input name=\'kind_3\' type=\'hidden\' value=\'N\'>';
		}


		/***********************************************

			서비스별 이용 고객수

		***********************************************/
		echo '<input name=\'kind_cnt_0\' type=\'hidden\' value=\''.(!empty($c_kind_cnt[0]) ? $c_kind_cnt[0] : 0).'\'>';
		echo '<input name=\'kind_cnt_1\' type=\'hidden\' value=\''.(!empty($c_kind_cnt[1]) ? $c_kind_cnt[1] : 0).'\'>';
		echo '<input name=\'kind_cnt_2\' type=\'hidden\' value=\''.(!empty($c_kind_cnt[2]) ? $c_kind_cnt[2] : 0).'\'>';
		echo '<input name=\'kind_cnt_3\' type=\'hidden\' value=\''.(!empty($c_kind_cnt[3]) ? $c_kind_cnt[3] : 0).'\'>';
		echo '<input name=\'kind_cnt_4\' type=\'hidden\' value=\''.(!empty($c_kind_cnt[4]) ? $c_kind_cnt[4] : 0).'\'>';
		echo '<input name=\'kind_cnt_5\' type=\'hidden\' value=\''.(!empty($c_kind_cnt[5]) ? $c_kind_cnt[5] : 0).'\'>';

		unset($c_kind_cnt);
	?>
	</tbody>
</table>
<? 
if($IsNursingOrder){ 
	//지시서요청 완료 조회
	$sql= 'SELECT count(*)
			 FROM medical_request
			WHERE org_no = \''.$_SESSION['userCenterCode'].'\'
			  AND complete_yn = \'Y\'
			  AND cancel_yn = \'N\'
			  AND del_flag = \'N\'';
	$rqCnt = $conn -> get_data($sql);

	//if($kupyeo3 && $rqCnt==0){
	if($rqCnt==0){ ?>
		<div style="position:absolute; top:322px; left:854px; cursor:pointer; width:100px;" onclick="_nursingPop();">
			<img src="../popup/nursing_request/img/btn_medical.png" alt="방문간호지시서 의료기관신청" title="방문간호지시서 의료기관신청">
		</div><?
	}
} ?>
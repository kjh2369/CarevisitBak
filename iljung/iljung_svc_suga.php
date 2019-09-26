<?
	########################################################
	#
	# 적용수가
	#
	########################################################

	//실적마감여부
	$ls_closerYn = $conn->_isCloseResult($code, $year.$month);

	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.($wrt_mode == 1 ? __GAB__ : __GAB_MINUS__).'px;\'>';
		echo '<colgroup>';
			echo '<col width=\'60px\'>';
			echo '<col width=\'150px\'>';
			echo '<col width=\'60px\'>';
			echo '<col>';
			echo '<col width=\'128px\'>';
		echo '</colgroup>';

		if ($wrt_mode == 1){
			echo '<thead>';
				echo '<tr>';
					echo '<th class=\'head bold\' colspan=\'6\'>적용수가</th>';
				echo '</tr>';
			echo '</thead>';
		}
		echo '<tbody>';
			echo '<tr>';
				echo '<th class=\'head\'>수가명</th>';
				echo '<td class=\'left\' id=\'sugaCont\'></td>';
				echo '<th class=\'head\'>적용수가</th>';
				echo '<td class=\'left\'>';

				if ($svc_id > 10 && $svc_id < 20){
					echo '기준수가 <input id=\'sPrice\' name=\'sPrice\' type=\'text\' value=\'\' class=\'number\' style=\'width:55px; background-color:#eeeeee;\' readOnly>원, (';
					echo '야간     <input id=\'ePrice\' name=\'ePrice\' type=\'text\' value=\'\' class=\'number\' style=\'width:55px; background-color:#eeeeee;\' readOnly>원 + ';
					echo '심야     <input id=\'nPrice\' name=\'nPrice\' type=\'text\' value=\'\' class=\'number\' style=\'width:55px; background-color:#eeeeee;\' readOnly>원) = ';
					echo '수가계   <input id=\'tPrice\' name=\'tPrice\' type=\'text\' value=\'\' class=\'number\' style=\'width:55px; background-color:#eeeeee;\' readOnly>원';
				}else if ($svc_id > 20 && $svc_id < 30){
					if ($svc_id == 24){
						/**************************************************

							장애인활동지원

						**************************************************/
						echo '<div style=\'float:left; width:auto; padding-top:3px; padding-bottom:3px;\'>
								<div id=\'frame_suga_cost\' style=\'width:auto; padding-bottom:1px;\'>
									기준수가
									<input id=\'sugaCost\' name=\'sugaCost\'  type=\'text\'   value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>원 X
									<input id=\'sugaTime\' name=\'sugaTime\'  type=\'text\'   value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>시간(일)
								</div>

								<div id=\'frame_suga_night\' style=\'width:auto; padding-top:1px;\'>
									연장수가
									<input id=\'sugaCostNight\' name=\'sugaCostNight\'  type=\'text\'   value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>원 X
									<input id=\'sugaTimeNight\' name=\'sugaTimeNight\'  type=\'text\'   value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>시간(일)
								</div>
							  </div>

							  <div id=\'frame_suga_tot\' style=\'float:left; width:auto; padding-left:10px; padding-top:15px;\'>
								= 수가계
								<input id=\'sugaTot\' name=\'sugaTot\' type=\'text\' value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>원
							  </div>';
					}else{
						echo '기준수가 <input id=\'sugaCost\' name=\'sugaCost\'  type=\'text\'   value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>원 X ';
						echo '시간(일) <input id=\'sugaTime\' name=\'sugaTime\'  type=\'text\'   value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>시간(일) = ';
						echo '수가계   <input id=\'sugaTot\'  name=\'sugaTot\'   type=\'text\'   value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>원';
					}
				}else{
					echo '기준수가 <input id=\'sugaCost\' name=\'sugaCost\'  type=\'text\' value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>원 X ';
					echo '제공시간 <input id=\'sugaTime\' name=\'sugaTime\'  type=\'text\' value=\'\' class=\'number\' style=\'width:40px; background-color:#eeeeee;\' readOnly>시간 = ';;
					echo '수가계   <input id=\'sugaTot\'  name=\'sugaTot\'   type=\'text\' value=\'\' class=\'number\' style=\'width:60px; background-color:#eeeeee;\' readOnly>원';
				}

				echo '</td>';
				echo '<td class=\'right\' style=\'width:auto;\'>';

				if ($ls_closerYn != 'Y'){
					if ($wrt_mode == 1){
						echo '	<a href=\'#\' onclick=\'_setIljungAss();\'><img src="../image/btn_pattern_list.png"  style="margin-left:4px; margin-right:3px;"></a>';
					}

					/*********************************************

						배정

					*********************************************/
					echo '<a href=\'#\' onclick=\'_setAss();\'><img src="../image/btn_diss.png"></a>';
				}else{
					echo '<span style=\'width:100px; text-align:center; font-weight:bold; color:#ff0000;\'>실 적 마 감</span>';
				}

				echo '</td>';
			echo '</tr>';
		echo '</tbody>';
	echo '</table>';

	echo '<input name=\'sugaCode\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'sugaName\' type=\'hidden\' value=\'\'>';
?>
<?
	include_once('../inc/_db_open.php');
	include_once('iljung_config.php');

	$msg   = $_POST['msg'];
	$code  = $_POST['code'];
	$kind  = $_POST['kind'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$key   = $_POST['key'];
	$vm_yn = $_POST['vm_yn']; //바우처 생성여부

	$sql = 'select m03_mkind
			  from m03sugupja
			 where m03_ccode = \''.$code.'\'
			   and m03_key   = \''.$key.'\'
			   and left(m03_gaeyak_fm,6) <= \''.$year.$month.'\'
			   and left(m03_gaeyak_to,6) >= \''.$year.$month.'\'
			 order by m03_mkind
			 limit 1';

	$kind = $conn->get_data($sql);

	ob_start();

	echo '<input name=\'_SAVE_CARE_\'    type=\'hidden\' value=\''.__PATH_CARE_SAVE__.'\'>';
	echo '<input name=\'_SAVE_VOUCHER_\' type=\'hidden\' value=\''.__PATH_VOUCHER_SAVE__.'\'>';

	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'px;\'>';
	echo '	<colgroup>
				<col>
				<col width=\'250px;\'>
			</colgroup>';
	echo '	<tbody>';
	echo '		<tr>';
	echo '			<th class=\'left bold last\' style=\'font-size:13px; padding-top:7px;\'>'.$msg.'</th>';
	echo '			<th class=\'center\' style="width:auto;">';


	/*
	echo '<div style=\'float:left; width:auto; margin-top:2px;\'>
			<img src=\'../image/btn_lcm_1.gif\' style=\'cursor:pointer;\' onclick=\'_iljungGetLongTermMgmtNo("001",'.$debug.');\'>
			<img src=\'../image/btn_lcm_2.gif\' style=\'cursor:pointer;\' onclick=\'_iljungGetLongTermMgmtNo("002");\'>
			<img src=\'../image/btn_lcm_3.gif\' style=\'cursor:pointer;\' onclick=\'_iljungGetLongTermMgmtNo("003");\'>
		  </div>';
	*/

	/*********************************************************
		건보 일정등록 버튼
		-간호는 임시로 제외한다.
		<img src=\'../image/btn_lcm_3.gif\' style=\'cursor:pointer;\' onclick=\'_iljungPlanList("003");\'>
	*********************************************************/
	if ($kind == 0){
		echo '<div style=\'float:left; width:auto; margin-top:2px;\'>
				<img src=\'../image/btn_lcm_1.gif\' style=\'cursor:pointer;\' onclick=\'_longcareUpload("'.$year.$month.'","001","Y");\'>
				<img src=\'../image/btn_lcm_2.gif\' style=\'cursor:pointer;\' onclick=\'_longcareUpload("'.$year.$month.'","002","Y");\'>
				<img src=\'../image/btn_lcm_3.gif\' style=\'cursor:pointer;\' onclick=\'_longcareUpload("'.$year.$month.'","003","Y");\'>
			  </div>

			  <div style=\'float:left; width:auto; line-height:26px; margin-left:20px; cursor:pointer;\' onclick=\'_familyCareRegInfo(this);\'>
				<span style=\'font-weight:bold;\'>[<span style=\'color:#0000ff;\'>가족요양보호사</span> <span style=\'color:#ff0000\'>등록안내</span>]</span>
			  </div>';

		if ($debug){
			echo '<div style=\'float:left; width:auto; margin-top:2px;\' onclick=\'_longcareUpload("'.$year.$month.'","001","N");\'>
					[test]
				  </div>';
		}
	}

	echo '				<div style=\'float:right; width:auto; margin-top:2px; margin-right:10px;\'>
							<a href=\'#\' onClick=\'_iljungSubmit();\'><img src="../image/btn_save_2.png"></a>
							<a href=\'#\' onClick=\'_delete_iljung();\'><img src="../image/btn11.gif"></a>
							<a href=\'#\' onClick=\'serviceCalendarShow("'.$code.'","'.$kind.'","'.$year.'","'.$month.'","'.$key.'","s","y","pdf","y")\' ><img src="../image/btn_print_1.png" title="금액표시된 출력물입니다."></a>
							<a href=\'#\' onClick=\'serviceCalendarShow("'.$code.'","'.$kind.'","'.$year.'","'.$month.'","'.$key.'","s","n","pdf","y")\' ><img src="../image/btn_print_2.png" title="금액 미표시된 출력물입니다."></a>
						</div>';

	echo '			</th>';
	echo '		</tr>';
	echo '	</tbody>';
	echo '</table>';

	$html = ob_get_contents();

	ob_end_clean();

	echo $html;

	include_once('../inc/_db_close.php');
?>
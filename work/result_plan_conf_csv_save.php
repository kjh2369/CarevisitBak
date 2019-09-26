<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_html.php');

	/**************************************************

		파라메타

	**************************************************/
	$code = $_POST['code']; //'31147000087'
	$file = $_POST['file'];

	/**************************************************

		CSV 파일읽기

		[0] =>						--> 사용안함
		[1] => 방문요양				--> 서비스 구분
		[2] => 조찬수				--> 수급자명
		[3] => 410308-1******		--> 수급자 주민번호
		[4] => 양순란				--> 요양보호사
		[5] => 480412-2******		--> 요양보호사 주민번호
		[6] => Y					--> 자동/수동 구분
		[7] => (자동)				--> 구분 명칭
		[8] => 2011.08.01 05:16		--> 시작일시
		[9] => 2011.08.01 07:27		--> 종료일시
		[10] => 131					--> 진행시간
		[11] =>						--> 사용안함

	**************************************************/
		if (($handle = fopen($file, "r")) !== FALSE) {
			$row_id = 0;
			$row_no = 0;

			while(true){
				$str = fgets($handle);

				if ($row_no > 2){
					$data = explode(chr(9), $str);

					for($i=0; $i<sizeof($data); $i++){
						$row[$row_id][$i] = $myF->utf($data[$i]);
					}

					$row_id ++;
				}

				$row_no ++;

				if (feof($handle)) break;
			}
			fclose($handle);
		}else{
			echo $myF->message('업로드하신 파일을 찾을 수 없습니다. 잠시후 다시 시도하여 주십시오.', 'Y', 'Y');
			exit;
		}

		$r_cnt = sizeof($row);
	/*************************************************/




	/**************************************************

		데이타 가공

	**************************************************/
		$index = 0;
		$yymm  = '';

		for($i=0; $i<$r_cnt; $i++){
			$m[$index] = init_array($row[$i]);

			if (!empty($m[$index]['svc_cd'])){
				//수급자 주민번호
				$sql = 'select m03_jumin
						  from m03sugupja
						 where m03_ccode    = \''.$code.'\'
						   and m03_name  like \''.$m[$index]['c_nm'].'%\'
						   and m03_jumin like \''.$m[$index]['c_cd'].'%\'
						   and m03_del_yn   = \'N\'
						 limit 1';

				$m[$index]['c_cd'] = $conn->get_data($sql);


				//요양사 주민번호
				$sql = 'select m02_yjumin
						  from m02yoyangsa
						 where m02_ccode     = \''.$code.'\'
						   and m02_yname  like \''.$m[$index]['m_nm1'].'%\'
						   and m02_yjumin like \''.$m[$index]['m_cd1'].'%\'
						   and m02_del_yn    = \'N\'
						 limit 1';

				$m[$index]['m_cd1'] = $conn->get_data($sql);


				$m[$index]['conf_from'] = $m[$index]['plan_from'];
				$m[$index]['conf_time'] = $myF->cutOff($m[$index]['plan_time'], 30);

				if ($m[$index]['svc_cd'] == '800'){
					if ($m[$index]['conf_time'] > 60) $m[$index]['conf_time'] = 60;
				}

				$tmp_time = explode(':', $m[$index]['conf_from']);
				$int_time = intval($tmp_time[0]) * 60 + intval($tmp_time[1]) + intval($m[$index]['conf_time']);
				$tmp_hour = floor($int_time / 60);
				$tmp_min  = ($int_time % 60);
				$tmp_hour = ($tmp_hour < 10 ? '0' : '').$tmp_hour;
				$tmp_min  = ($tmp_min < 10 ? '0' : '').$tmp_min;
				$m[$index]['conf_to']   = $tmp_hour.':'.$tmp_min;

				unset($tmp_time);
				unset($int_time);
				unset($tmp_hour);
				unset($tmp_min);

				if (!empty($m[$index]['c_cd']) && !empty($m[$index]['m_cd1'])){
					if ($m[$index]['svc_cd']	== '500'					 &&
						$m[$index]['c_cd']		== $m[$index-1]['c_cd']		 &&
						$m[$index]['date']		== $m[$index-1]['date']		 ){

						/*
						$m[$index]['plan_from'] == $m[$index-1]['plan_from']
						$m[$index]['plan_to']	== $m[$index-1]['plan_to']
						$m[$index]['plan_time'] == $m[$index-1]['plan_time']
						*/

						$m[$index-1]['m_cd2'] = $m[$index]['m_cd1'];
						$m[$index-1]['m_nm2'] = $m[$index]['m_nm1'];

						unset($m[$index]);
					}else{
						$str_yymm = substr($m[$index]['date'],0, 7);

						if (!is_numeric(strpos($yymm, $str_yymm)) && !empty($str_yymm)){
							$yymm .= $str_yymm.' / ';
						}

						$index ++;
					}
				}else{
					unset($m[$index]);
				}
			}else{
				unset($m[$index]);
			}
		}

		//메모리 해제
		unset($row);
	/*************************************************/


	/*************************************************

		년월 배열

	*************************************************/
		$yymm = explode(' / ', $yymm);

		//년월 구분의 마지막 배열은 해제한다.
		unset($yymm[sizeof($yymm)-1]);

		$yymm_cnt = sizeof($yymm);

		if ($yymm_cnt == 0){
			unset($yymm);
			unset($m);

			echo '<script>
					alert(\'TEXT 파일에서 일치하는 수급자와 요양보호사를 찾을 수 없습니다.\');
					self.close();
				  </script>';
			exit;
		}

		for($i=0; $i<$yymm_cnt; $i++){
			if ($year != substr($yymm[$i],0,4)){
				$year  = substr($yymm[$i],0,4);
			}

			for($j=1; $j<=12; $j++){
				$month_list[$year][$j] = ($j == intval(substr($yymm[$i],5)) ? 'Y' : 'N');
			}

			/**************************************************

				마감정보

			**************************************************/

			$sql = 'select substring(closing_yymm, 5) as mm
					,      act_cls_flag as flag
					  from closing_progress
					 where org_no                = \''.$code.'\'
					   and left(closing_yymm, 4) = \''.$year.'\'';

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($j=0; $j<$row_count; $j++){
				$row = $conn->select_row($j);

				if ($row['flag'] == 'Y'){
					$month_list[$year][intval($row['mm'])] = 'X';
				}
			}

			$conn->row_free();
		}

		unset($year);
	/************************************************/

	echo $html_script->title('건보 계획,실적 일괄등록(TEXT)', true);
	echo $html_script->form_start('f','post',false,' action=\'result_plan_conf_csv_save_ok.php\' ');
	echo $html_script->table_start();
	echo $html_script->table_colgroup(array(50,50));
	echo $html_script->table_body_start();
	echo $html_script->table_row_start();

	$html = '';

	for($i=0; $i<$yymm_cnt; $i++){
		if ($year != substr($yymm[$i],0,4)){
			$year  = substr($yymm[$i],0,4);

			echo $html_script->table_row('년도','center','th');
			echo $html_script->table_row($year.'년','left');
			echo $html_script->input('year[]','hidden',$year);

			if (!empty($html)) echo $html_script->table_row($html,'left last','td','padding-top:3px; padding-bottom:3px;');

			$html = '';
		}

		for($j=1; $j<=12; $j++){
			$id = $year.($j<10?'0':'').$j;

			if ($month_list[$year][$j] == 'Y'){
				$class = 'my_month my_month_y ';
				$text  = '<a href=\'#\' onclick=\'set_yymm("'.$id.'");\'>'.$j.'월</a>';
			}else if ($month_list[$year][$j] == 'X'){
				$class = 'my_month my_month_n ';
				$text  = '<span style=\'color:#cccccc; cursor:default;\'>'.$j.'월</span>';
			}else{
				$class = 'my_month my_month_1 ';
				$text  = '<span style=\'color:#cccccc; cursor:default;\'>'.$j.'월</span>';
			}

			$style = 'float:left; margin-right:3px;';
			$html .= '<div id=\'id_'.$id.'\' class=\''.$class.'\' style=\''.$style.'\'>'.$text.'</div>';
			$html .= '<input name=\'obj_'.$id.'\' type=\'hidden\' value=\''.$month_list[$year][$j].'\'>';
		}
	}

	echo $html_script->table_row($html,'left last','td','padding-top:3px; padding-bottom:3px;');
	echo $html_script->table_row_end();
	echo $html_script->table_body_end();
	echo $html_script->table_end();



	echo $html_script->input('code','hidden',$code);
	echo $html_script->input('file','hidden',$file);

	$m_cnt = sizeof($m);

	for($i=0; $i<$m_cnt; $i++){
		echo $html_script->input('svc_cd[]','hidden',$m[$i]['svc_cd']);
		echo $html_script->input('c_cd[]','hidden',$m[$i]['c_cd']);
		echo $html_script->input('m_cd1[]','hidden',$m[$i]['m_cd1']);
		echo $html_script->input('m_cd2[]','hidden',$m[$i]['m_cd2']);
		echo $html_script->input('m_nm1[]','hidden',$m[$i]['m_nm1']);
		echo $html_script->input('m_nm2[]','hidden',$m[$i]['m_nm2']);
		echo $html_script->input('date[]','hidden',str_replace('.','',$m[$i]['date']));
		echo $html_script->input('work_from[]','hidden',str_replace(':','',$m[$i]['plan_from']));
		echo $html_script->input('work_to[]','hidden',str_replace(':','',$m[$i]['plan_to']));
		echo $html_script->input('work_time[]','hidden',$m[$i]['plan_time']);
		echo $html_script->input('conf_from[]','hidden',str_replace(':','',$m[$i]['conf_from']));
		echo $html_script->input('conf_to[]','hidden',str_replace(':','',$m[$i]['conf_to']));
		echo $html_script->input('conf_time[]','hidden',$m[$i]['conf_time']);

		$suga = $conn->_find_suga_($code, $m[$i]['svc_cd'], $m[$i]['date'], $m[$i]['conf_from'], $m[$i]['conf_to'], $m[$i]['conf_time']);

		echo $html_script->input('suga_code[]','hidden',$suga['code']);
		echo $html_script->input('suga_name[]','hidden',$suga['name']);
		echo $html_script->input('suga_cost[]','hidden',$suga['cost']);
		echo $html_script->input('suga_evening_cost[]','hidden',$suga['evening_cost']);
		echo $html_script->input('suga_night_cost[]','hidden',$suga['night_cost']);
		echo $html_script->input('suga_total_cost[]','hidden',$suga['total_cost']);
		echo $html_script->input('suga_sudang_pay[]','hidden',$suga['sudang_pay']);
		echo $html_script->input('suga_evening_time[]','hidden',$suga['evening_time']);
		echo $html_script->input('suga_night_time[]','hidden',$suga['night_time']);
		echo $html_script->input('suga_evening_yn[]','hidden',$suga['evening_yn']);
		echo $html_script->input('suga_night_yn[]','hidden',$suga['night_yn']);
		echo $html_script->input('holiday_yn[]','hidden',$suga['holiday_yn']);

		unset($suga);
	}

	//메모리 해제
	unset($m);


	$html  = '<div>'.$html_script->input('flag','radio','N','radio').'동일 데이타가 존재시 수정합니다.</div>';
	$html .= '<div>'.$html_script->input('flag','radio','Y','radio','checked').'기존의 데이타를 삭제 후 저장합니다.</div>';


	echo '<div style=\'padding:10px;\'>';
	echo $html_script->table_start('my_border_blue');
	echo $html_script->table_colgroup();
	echo $html_script->table_body_start();
	echo $html_script->table_row_start();
	echo $html_script->table_row($html,'left','td','padding:10px;');
	echo $html_script->table_row_end();
	echo $html_script->table_body_end();
	echo $html_script->table_end();
	echo '</div>';



	$html  = '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'save();\'>저장</button></span> ';
	$html .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'self.close();\'>닫기</button></span>';

	echo '<div style=\'padding:10px;\'>';
	echo $html_script->table_start('my_border_blue');
	echo $html_script->table_colgroup();
	echo $html_script->table_body_start();
	echo $html_script->table_row_start();
	echo $html_script->table_row($html,'center','td','padding:10px;');
	echo $html_script->table_row_end();
	echo $html_script->table_body_end();
	echo $html_script->table_end();
	echo '</div>';


	echo $html_script->form_end();

	include_once('../inc/_footer.php');



	// 배열초기화
	function init_array($tmp_arr){
		switch(trim($tmp_arr[1])){
			case '방문요양': $svc_cd = '200'; break;
			case '방문목욕': $svc_cd = '500'; break;
			case '방문간호': $svc_cd = '800'; break;
		}

		$arr = array('svc_cd'	=>$svc_cd
					,'svc_nm'	=>trim($tmp_arr[1])
					,'c_cd'		=>substr(str_replace('-', '', $tmp_arr[3]), 0, 7)
					,'c_nm'		=>$tmp_arr[2]
					,'m_cd1'	=>substr(str_replace('-', '', $tmp_arr[5]), 0, 7)
					,'m_nm1'	=>$tmp_arr[4]
					,'m_cd2'	=>''
					,'m_nm2'	=>''
					,'date'		=>substr($tmp_arr[8], 0, 10)
					,'plan_from'=>trim(substr($tmp_arr[8], 10))
					,'plan_to'	=>trim(substr($tmp_arr[9], 10))
					,'plan_time'=>$tmp_arr[10]
					,'conf_from'=>''
					,'conf_to'	=>''
					,'conf_time'=>0
					,'use_yn'	=>'Y');

		return $arr;
	}
?>
<script language='javascript'>
<!--

function set_yymm(yymm){
	var obj1 = document.getElementById('id_'+yymm);
	var obj2 = document.getElementById('obj_'+yymm);

	if (obj2.value != 'Y'){
		obj1.className = 'my_month my_month_y';
		obj2.value = 'Y';
	}else{
		obj1.className = 'my_month my_month_1';
		obj2.value = 'N';
	}
}

function save(){
	var f    = document.f;
	var year = document.getElementsByName('year[]');
	var flag = false;

	for(var i=0; i<year.length; i++){
		for(var j=1; j<=12; j++){
			var obj = document.getElementById('obj_'+year[i].value+(j<10?'0':'')+j);

			if (obj.value == 'Y'){
				flag = true;
				break;
			}
		}

		if (flag) break;
	}

	if (flag){
		f.submit();
	}else{
		alert('데이타를 저장할 월을 선택하여 주십시오.');
	}
}

-->
</script>
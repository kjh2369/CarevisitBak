<?
	if ($output != 'excel'){
		include_once('../inc/_header.php');
	}else{
		include_once('../inc/_db_open.php');
	}

	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if ($output == 'excel'){
		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-type: charset=utf-8" );
		header( "Content-Disposition: attachment; filename=test.xls" );
		header( "Content-Description: test" );
		header( "Pragma: no-cache" );
		header( "Expires: 0" );
	}

	$code  = $_POST['code'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$day   = $_POST['day'];

	$file = $_POST['file'];

	$onload = $_POST['onload'];

	$output = $_POST['output'];

	$flag = $_POST['flag'];

	if (empty($onload)){
		if (empty($flag)) $flag = '1';
	}

	#######################
	# 버튼그룹
		if ($output != 'excel'){
			$btn = '<div style=\'text-align:right; padding-right:5px; padding-top:5px;\'>
					<input name=\'flag\' type=\'radio\' class=\'radio\' value=\'1\' onclick=\'set_reload(this.value);\' '.($flag == '1' ? 'checked' : '').'><a href=\'#\' onclick=\'set_reload("1");\'>전체</a>
					<input name=\'flag\' type=\'radio\' class=\'radio\' value=\'6\' onclick=\'set_reload(this.value);\' '.($flag == '6' ? 'checked' : '').'><a href=\'#\' onclick=\'set_reload("6");\'>정상데이타</a>
					<input name=\'flag\' type=\'radio\' class=\'radio\' value=\'2\' onclick=\'set_reload(this.value);\' '.($flag == '2' ? 'checked' : '').'><a href=\'#\' onclick=\'set_reload("2");\'>수급자에러</a>
					<input name=\'flag\' type=\'radio\' class=\'radio\' value=\'3\' onclick=\'set_reload(this.value);\' '.($flag == '3' ? 'checked' : '').'><a href=\'#\' onclick=\'set_reload("3");\'>요양사에러</a>
					<input name=\'flag\' type=\'radio\' class=\'radio\' value=\'4\' onclick=\'set_reload(this.value);\' '.($flag == '4' ? 'checked' : '').'><a href=\'#\' onclick=\'set_reload("4");\'>계획에러</a>
					<input name=\'flag\' type=\'radio\' class=\'radio\' value=\'5\' onclick=\'set_reload(this.value);\' '.($flag == '5' ? 'checked' : '').'><a href=\'#\' onclick=\'set_reload("5");\'>실적에러</a>
					<input name=\'flag\' type=\'radio\' class=\'radio\' value=\'9\' onclick=\'set_reload(this.value);\' '.($flag == '9' ? 'checked' : '').'><a href=\'#\' onclick=\'set_reload("9");\'>미등록</a>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<span class=\'btn_pack m\'><button type=\'button\' onclick=\'set_excel();\'>엑셀</button></span>
					<span class=\'btn_pack m\'><button type=\'button\' onclick=\'set_save();\'>등록</button></span>
					<span class=\'btn_pack m\'><button type=\'button\' onclick=\'set_close();\'>닫기</button></span>
					</div>';
		}
	#
	#######################

	#######################
	# 임시테이블 생성 쿼리
		$sql = "select count(*)
				  from tmp_request";

		if ($conn->execute($sql)){
			$conn->execute("drop table tmp_request");
		}

		$nql = "create temporary table tmp_request (
				 row_id integer unsigned not null auto_increment
				,id int(11) not null
				,client_nm varchar(20)
				,client_cd char(7)
				,client_ssn char(13) not null

				,member_nm varchar(20)
				,member_cd char(7)
				,member_ssn char(13) not null

				,from_dt char(10) not null
				,from_tt char(5) not null
				,to_dt char(10)
				,to_tt char(5)
				,proc_min char(4)

				,work_from_dt char(10)
				,work_from_tt char(5)
				,work_to_dt char(10)
				,work_to_tt char(5)
				,work_proc_min char(4)

				,conf_from_dt char(10)
				,conf_from_tt char(5)
				,conf_to_dt char(10)
				,conf_to_tt char(5)
				,conf_proc_min char(4)

				,weekday char(1)
				,holiday char(1)
				,suga_cd char(45)
				,family char(1)

				,svc_nm varchar(10)
				,svc_cd char(3)
				,seq int(11) not null
				,stat char(1)
				,stat_gbn varchar(20)
				,ms_gbn char(1)

				,sub_nm varchar(20)
				,sub_cd char(7)
				,sub_ssn char(13)
				,sub_gbn char(1)
				,sub_set char(1) default 'N'

				,primary key (row_id));";
	#
	#######################

	if (empty($onload)){
		if (empty($flag)) $flag = '1';
	}

	if (empty($file)){
		$f = $_FILES['csv'];

		###################################################
		# CSV 파일업로드
			if ($f['tmp_name'] != ''){
				$file_nm = $_SESSION['userCenterCode'];
				$file    = '../file/csv/'.$file_nm;

				if (is_file($file)){
					@unlink($file);
				}

				if (move_uploaded_file($f['tmp_name'], $file)){
					// 업로드 성공
					$upload = true;
				}else{
					// 업로드 실패
					$upload = false;
				}
			}else{
				// 업로드 실패
				$upload = false;
			}

			if (!$upload){
				echo '<script language="javascript">
						alert(\'파일업로드중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.\');
						history.back();
					  </script>';
				exit;
			}
		#
		###################################################
	}



	###################################################
	# CSV 파일읽기
		if (($handle = fopen($file, "r")) !== FALSE) {
			$row_id = 0;

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
	#
	###################################################

	$r_cnt = sizeof($row);

	for($i=0; $i<$r_cnt; $i++){
		//서비스구분
		$svc_nm = $row[$i][1];

		switch($svc_nm){
			case '방문요양':
				$svc_cd = '200';
				break;
			case '방문목욕':
				$svc_cd = '500';
				break;
			case '방문간호':
				$svc_cd = '800';
				break;
			default:
				$svc_cd = '';
		}

		if (!empty($row[$i][0])) $svc_cd = '';

		if (!empty($svc_cd)){
			if (!empty($row[$i][3])){
				$client_nm = $row[$i][2];  //수급자명
				$client_cd = $row[$i][3];  //수급자주민번호
				$client_cd = substr(str_replace('-', '', $client_cd),0,7);
			}

			if (!empty($row[$i][5])){
				$member_nm = $row[$i][4]; //요양사명
				$member_cd = $row[$i][5]; //요양사주민번호
				$member_cd = substr(str_replace('-', '', $member_cd),0,7);
			}

			if (empty($row[$i][8]) && empty($row[$i][9]) && empty($row[$i][10])){
			}else{
				$from_dt  = substr($row[$i][8], 0, 10); //서비스시작일시
				$from_dt  = str_replace('.','',$from_dt);
				$from_dt  = str_replace(' ','',$from_dt);
				$from_tt  = substr($row[$i][8], 11);
				$from_tt  = str_replace(':','',$from_tt);
				$from_tt  = str_replace(' ','',$from_tt);
				$to_dt    = substr($row[$i][9], 0, 10); //서비스종료일시
				$to_dt    = str_replace('.','',$to_dt);
				$to_dt    = str_replace(' ','',$to_dt);
				$to_tt    = substr($row[$i][9], 11);
				$to_tt    = str_replace(':','',$to_tt);
				$to_tt    = str_replace(' ','',$to_tt);
				$proc_min = $row[$i][10]; //제공시간(분)

				$svc_cnt = sizeof($svc);
				$svc[$svc_cnt]['id']        = $svc_cnt + 1;
				$svc[$svc_cnt]['data_id']   = 0;
				$svc[$svc_cnt]['svc_cd']    = $svc_cd;
				$svc[$svc_cnt]['svc_nm']    = $svc_nm;
				$svc[$svc_cnt]['client_nm'] = $client_nm;
				$svc[$svc_cnt]['client_cd'] = $client_cd;
				$svc[$svc_cnt]['member_nm'] = $member_nm;
				$svc[$svc_cnt]['member_cd'] = $member_cd;
				$svc[$svc_cnt]['from_dt']   = $from_dt;
				$svc[$svc_cnt]['from_tt']   = $from_tt;
				$svc[$svc_cnt]['to_dt']     = $to_dt;
				$svc[$svc_cnt]['to_tt']     = $to_tt;
				$svc[$svc_cnt]['proc_min']  = $proc_min;
				$svc[$svc_cnt]['key']       = $svc_cd.'_'.$client_nm.'_'.$member_nm.'_'.$from_dt.'_'.$from_tt.'_'.$to_tt;
			}
		}
	}

	###################################################
	# 메모리헤제
		unset($row);
	#
	###################################################

	$svc = $myF->sortArray($svc, 'key', 1);

	if ($output != 'excel'){?>
		<script src="../js/work.js" type="text/javascript"></script>
		<script language='javascript'>
		<!--

		var proc_val = 0;
		var proc_max = 100;
		var proc_timer = null;

		function set_reload(val){
			var f = document.f;
			var flag = document.getElementsByName('flag');

			for(var i=0; i<flag.length; i++){
				if (flag[i].value == val){
					flag[i].checked = true;
					break;
				}
			}

			f.output.value = '';
			f.action = 'result_csv_upload.php';
			f.submit();
		}

		function set_close(){
			var f = document.f;

			if (!confirm('현재창을 닫으시겠습니까?')) return;

			f.action = 'result_csv_close.php';
			f.submit();
		}

		function set_save(){
			var f = document.f;
			var cnt = __checkMyCount('check[]');

			if (cnt < 1){
				alert('등록할 일정을 선택하여 주십시오.');
				return;
			}

			f.action = 'result_csv_save.php';
			f.submit();
		}

		function set_check_all(val){
			__checkMyValue('check[]', val);

			if (val){
				var obj = document.getElementsByName('check[]');
				var win = document.getElementById('layer_win');
				var bdy = document.getElementById('body_div');

				win.style.top    = __getObjectTop(bdy);
				win.style.left   = __getObjectLeft(bdy);
				win.style.width  = bdy.offsetWidth;
				win.style.height = bdy.offsetHeight;

				for(var i=0; i<obj.length; i++){
					if (obj[i].type != 'hidden'){
						obj[i].tag   = 'N';
					}
				}

				timer_init();
			}else{
				var obj  = document.getElementsByName('check[]');
				var suga = document.getElementsByName('csv_suga[]');

				for(var i=0; i<obj.length; i++){
					if (obj[i].type != 'hidden'){
						obj[i].tag   = 'N';
					}
				}

				for(var i=0; i<suga.length; i++){
					suga[i].innerHTML = '';
				}
			}
		}

		function set_suga(val){
			var index = __object_index('check[]', val);

			_set_conf_proc_time(index);
		}

		function timer_init(){
			document.getElementById('check_all').disabled = true;
			proc_timer = setInterval("timer()",10);
		}

		function timer_clear(){
			clearInterval(proc_timer);
			proc_timer = null;
			document.getElementById('check_all').disabled = false;
		}

		function timer(){
			var obj  = document.getElementsByName('check[]');
			var win  = document.getElementById('layer_win');
			var body = document.getElementById('layer_div');
			var rate = document.getElementById('layer_rat');
			var exec = false;

			proc_max = obj.length;

			for(var i=0; i<obj.length; i++){
				if (obj[i].type != 'hidden'){
					if (obj[i].tag != 'Y'){
						obj[i].tag  = 'Y';
						proc_val = i+1;
						set_suga(obj[i].value);
						exec = true;
						break;
					}
				}
			}

			if (!exec){
				timer_clear();

				var win = document.getElementById('layer_win');

				win.style.top    = 0;
				win.style.left   = 0;
				win.style.width  = 0;
				win.style.height = 0;

				body.style.display = 'none';
				rate.style.display = 'none';
			}else{
				var val  = proc_val / proc_max * 100;

				body.style.display = '';
				rate.style.display = '';

				var top  = win.offsetTop + (win.offsetHeight - body.offsetHeight) / 2;
				var left = (win.offsetWidth - body.offsetWidth) / 2;

				body.style.top  = top;
				rate.style.top  = top;
				body.style.left = left;
				rate.style.left = left;

				body.innerHTML = '<div style=\'filter:progid:DXImageTransform.Microsoft.Gradient(startColorStr="#FA8A8B", endColorStr="#CC2A2C", gradientType="1"); width:'+val+'%; height:100%;\'></div>';
				rate.innerHTML = '<div style=\'text-align:center; height:100%;\'>'+Math.round(val)+'%</div>';
			}
		}

		function set_excel(){
			var f = document.f;

			f.output.value = 'excel';
			f.action = 'result_csv_upload.php';
			f.submit();
		}

		function toresize(){
			var tbl = document.getElementById('body_tbl');
			var div = document.getElementById('body_div');

			tbl.style.height = document.body.offsetHeight - 80;

			var height = parseInt(tbl.style.height, 10) - 52;

			div.style.height = height;
		}

		window.onload = function(){
			onresize();

			//document.getElementById('check_all').checked = true;

			//set_check_all(true);
		}

		window.onresize = function(){
			toresize();
		}

		-->
		</script>

		<form name="f" method="post"><?
	}?>

<div class="title title_border">
<div style="width:auto; float:left;">건보실적등록(TEXT)</div>
<div style="width:auto; float:right; font-weight:normal;"><?=$btn;?></div>
</div>

<?
	$colgrp = '<col width="40px">
			   <col width="60px">
			   <col width="70px">
			   <col width="150px">
			   <col width="70px">
			   <col width="60px">
			   <col width="60px">
			   <col width="60px">
			   <col width="60px">
			   <col width="70px">
			   <col width="60px">
			   <col width="60px">
			   <col width="60px">
			   <col width="30px">
			   <col>';
?>
<table id="body_tbl" class="my_table" style="width:100%;" <? if($output == 'excel'){echo 'border=\'1\'';} ?>>
	<colgroup><?=$colgrp;?></colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">서비스</th>
			<th class="head" rowspan="2">수급자</th>
			<th class="head" rowspan="2">요양사</th>
			<th class="head" colspan="5">계획일정</th>
			<th class="head" colspan="4">실적일정</th>
			<th class="head bottom"></th>
			<th class="head" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">일자</th>
			<th class="head">시작시간</th>
			<th class="head">종료시간</th>
			<th class="head">제공시간</th>
			<th class="head">상태</th>
			<th class="head">일자</th>
			<th class="head">시작시간</th>
			<th class="head">종료시간</th>
			<th class="head">제공시간</th>
			<?
				if ($output != 'excel'){?>
					<th class="head"><input name="check_all" type="checkbox" class="checkbox" onclick="set_check_all(this.checked);"></th><?
				}else{?>
					<th class="head"></th><?
				}
			?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top" colspan="15">
				<div id="body_div" class="top" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:80px;">
					<table class="my_table" style="width:100%;" <? if($output == 'excel'){echo 'border=\'1\'';} ?>>
						<colgroup><?=$colgrp;?></colgroup>
						<tbody>
						<?
							$svc_cnt = sizeof($svc);

							for($i=0; $i<$svc_cnt; $i++){
								if ($tmp_client != $svc[$i]['client_cd'] ||
									$tmp_member != $svc[$i]['member_cd'] ||
									$tmp_date   != $svc[$i]['from_dt']){

									$tmp_svc    = $svc[$i]['svc_cd'];
									$tmp_client = $svc[$i]['client_cd'];
									$tmp_member = $svc[$i]['member_cd'];
									$tmp_date   = $svc[$i]['from_dt'];

									//수급자 주민번호
									$sql = "select m03_jumin
											  from m03sugupja
											 where m03_ccode    = '$code'
											   and m03_name  like '".$svc[$i]['client_nm']."%'
											   and m03_jumin like '".$svc[$i]['client_cd']."%'
											   and m03_del_yn   = 'N'
											 limit 1";

									$svc[$i]['client_ssn'] = $conn->get_data($sql);

									$svc_not[$svc[$i]['from_dt']] .= ' and m03_jumin != \''.$svc[$i]['client_ssn'].'\' ';


									//요양사 주민번호
									$sql = "select m02_yjumin
											  from m02yoyangsa
											 where m02_ccode     = '$code'
											   and m02_yname  like '".$svc[$i]['member_nm']."%'
											   and m02_yjumin like '".$svc[$i]['member_cd']."%'
											   and m02_del_yn    = 'N'
											 limit 1";

									$svc[$i]['member_ssn'] = $conn->get_data($sql);

									$column = "t01_sugup_date
											  ,t01_sugup_fmtime
											  ,t01_sugup_totime
											  ,t01_sugup_soyotime
											  ,t01_sugup_yoil

											  ,t01_conf_date
											  ,t01_conf_fmtime
											  ,t01_conf_totime
											  ,t01_conf_soyotime

											  ,t01_svc_subcode
											  ,t01_sugup_seq
											  ,t01_status_gbn

											  ,t01_suga_code1
											  ,t01_holiday

											  ,t01_toge_umu";

									//계획조회
									$sql = "select $column
											,      'm' as ms_gbn
											,      t01_yoyangsa_id2 as sub_ssn
											,      t01_yname2 as sub_nm
											,      's' as sub_gbn
											  from t01iljung
											 where t01_ccode        = '$code'
											   and t01_jumin        = '".$svc[$i]['client_ssn']."'
											   and t01_yoyangsa_id1 = '".$svc[$i]['member_ssn']."'
											   and t01_sugup_date   = '".$svc[$i]['from_dt']."'
											   and t01_bipay_umu   != 'Y'
											   and t01_del_yn       = 'N'";

									if ($tmp_svc == '500'){
										$sql .= " union all
												 select $column
												 ,      's' as ms_gbn
												 ,      t01_yoyangsa_id1 as sub_ssn
												 ,      t01_yname1 as sub_nm
												 ,      'm' as sub_gbn
											       from t01iljung
												  where t01_ccode        = '$code'
												    and t01_jumin        = '".$svc[$i]['client_ssn']."'
												    and t01_yoyangsa_id2 = '".$svc[$i]['member_ssn']."'
												    and t01_sugup_date   = '".$svc[$i]['from_dt']."'
													and t01_bipay_umu   != 'Y'
												    and t01_del_yn       = 'N'";
									}

									$sql .= " order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";

									$conn->query($sql);
									$conn->fetch();

									$row_count = $conn->row_count();

									for($ii=0; $ii<$row_count; $ii++){
										$row = $conn->select_row($ii);

										$dta_cnt = sizeof($dta);
										$dta[$dta_cnt]['id'] = 0;

										$dta[$dta_cnt]['client_nm']  = $svc[$i]['client_nm'];
										$dta[$dta_cnt]['client_cd']  = $svc[$i]['client_cd'];
										$dta[$dta_cnt]['client_ssn'] = $svc[$i]['client_ssn'];

										$dta[$dta_cnt]['member_nm']  = $svc[$i]['member_nm'];
										$dta[$dta_cnt]['member_cd']  = $svc[$i]['member_cd'];
										$dta[$dta_cnt]['member_ssn'] = $svc[$i]['member_ssn'];

										$dta[$dta_cnt]['from_dt']  = $row['t01_sugup_date'];
										$dta[$dta_cnt]['from_tt']  = $row['t01_sugup_fmtime'];
										$dta[$dta_cnt]['to_dt']    = $row['t01_sugup_date'];
										$dta[$dta_cnt]['to_tt']    = $row['t01_sugup_totime'];
										$dta[$dta_cnt]['proc_min'] = $row['t01_sugup_soyotime'];

										//실근무정보
										$dta[$dta_cnt]['work_from_dt']  = '';
										$dta[$dta_cnt]['work_from_tt']  = '';
										$dta[$dta_cnt]['work_to_dt']    = '';
										$dta[$dta_cnt]['work_to_tt']    = '';
										$dta[$dta_cnt]['work_proc_min'] = '';

										$dta[$dta_cnt]['conf_from_dt']  = $row['t01_conf_date'];
										$dta[$dta_cnt]['conf_from_tt']  = $row['t01_conf_fmtime'];
										$dta[$dta_cnt]['conf_to_dt']    = $row['t01_conf_date'];
										$dta[$dta_cnt]['conf_to_tt']    = $row['t01_conf_totime'];
										$dta[$dta_cnt]['conf_proc_min'] = $row['t01_conf_soyotime'];

										$dta[$dta_cnt]['weekday'] = $row['t01_sugup_yoil'];
										$dta[$dta_cnt]['holiday'] = $row['t01_holiday'];
										$dta[$dta_cnt]['suga_cd'] = $row['t01_suga_code1'];
										$dta[$dta_cnt]['family']  = $row['t01_toge_umu'];

										switch($row['t01_svc_subcode']){
											case '200':
												$dta[$dta_cnt]['svc_nm'] = '방문요양';
												break;
											case '500':
												$dta[$dta_cnt]['svc_nm'] = '방문목욕';
												break;
											case '800':
												$dta[$dta_cnt]['svc_nm'] = '방문간호';
												break;
										}

										$dta[$dta_cnt]['svc_cd'] = $row['t01_svc_subcode'];
										$dta[$dta_cnt]['seq']    = $row['t01_sugup_seq'];
										$dta[$dta_cnt]['stat']   = $row['t01_status_gbn'];

										$dta[$dta_cnt]['draw'] = false;

										switch($row['t01_status_gbn']){
											case '1':
												$dta[$dta_cnt]['stat_gbn'] = '완료';
												break;
											case '5':
												$dta[$dta_cnt]['stat_gbn'] = '수행중';
												break;
											case '9':
												$dta[$dta_cnt]['stat_gbn'] = '대기';
												break;
											case '0':
												$dta[$dta_cnt]['stat_gbn'] = '대기';
												break;
											case 'C':
												$dta[$dta_cnt]['stat_gbn'] = '에러';
												break;
											default:
												$dta[$dta_cnt]['stat_gbn'] = '대기';
										}

										$dta[$dta_cnt]['ms_gbn'] = $row['ms_gbn'];

										$dta[$dta_cnt]['sub_nm']  = $row['sub_nm'];
										$dta[$dta_cnt]['sub_cd']  = substr($row['sub_ssn'],0,7);
										$dta[$dta_cnt]['sub_ssn'] = $row['sub_ssn'];
										$dta[$dta_cnt]['sub_gbn'] = $row['sub_gbn'];
									}

									$conn->row_free();
								}

								if (empty($svc[$i]['client_ssn'])) $svc[$i]['client_ssn'] = $svc[$i-1]['client_ssn'];
								if (empty($svc[$i]['member_ssn'])) $svc[$i]['member_ssn'] = $svc[$i-1]['member_ssn'];
							}

							$sql = $nql;

							if (!$conn->execute($sql)){
								echo '1 :'.$conn->error_msg;
							}

							$dta_cnt = sizeof($dta);

							$sql = "insert into tmp_request values";

							for($i=0; $i<$dta_cnt; $i++){
								$sql .= ($i > 0 ? ',' : '');

								$sql .= "(null"
									 .	" ,'".$dta[$i]['id']

									 .  "','".$dta[$i]['client_nm']
									 .  "','".$dta[$i]['client_cd']
									 .  "','".$dta[$i]['client_ssn']

									 .  "','".$dta[$i]['member_nm']
									 .  "','".$dta[$i]['member_cd']
									 .  "','".$dta[$i]['member_ssn']

									 .  "','".$dta[$i]['from_dt']
									 .  "','".$dta[$i]['from_tt']
									 .  "','".$dta[$i]['to_dt']
									 .  "','".$dta[$i]['to_tt']
									 .  "','".$dta[$i]['proc_min']

									 //실근무정보
									 .  "','".$dta[$i]['work_from_dt']
									 .  "','".$dta[$i]['work_from_tt']
									 .  "','".$dta[$i]['work_to_dt']
									 .  "','".$dta[$i]['work_to_tt']
									 .  "','".$dta[$i]['work_proc_min']

									 .  "','".$dta[$i]['conf_from_dt']
									 .  "','".$dta[$i]['conf_from_tt']
									 .  "','".$dta[$i]['conf_to_dt']
									 .  "','".$dta[$i]['conf_to_tt']
									 .  "','".$dta[$i]['conf_proc_min']

									 .  "','".$dta[$i]['weekday']
									 .  "','".$dta[$i]['holiday']
									 .  "','".$dta[$i]['suga_cd']
									 .  "','".$dta[$i]['family']

									 .  "','".$dta[$i]['svc_nm']
									 .  "','".$dta[$i]['svc_cd']
									 .  "','".$dta[$i]['seq']
									 .  "','".$dta[$i]['stat']
									 .  "','".$dta[$i]['stat_gbn']
									 .  "','".$dta[$i]['ms_gbn']

									 .  "','".$dta[$i]['sub_nm']
									 .  "','".$dta[$i]['sub_cd']
									 .  "','".$dta[$i]['sub_ssn']
									 .  "','".$dta[$i]['sub_gbn']
									 .  "','N')";
							}

							if ($dta_cnt > 0){
								if (!$conn->execute($sql)){
									echo '2 : '.$conn->error_msg;
								}
							}

							unset($dta);

							$sql = "select *
									  from tmp_request
									 order by svc_cd, client_nm, member_nm, from_dt, from_tt, to_tt";

							$conn->query($sql);
							$conn->fetch();
							$row_count = $conn->row_count();

							$no = 0;

							for($i=0; $i<$row_count; $i++){
								$row = $conn->select_row($i);

								if ($i == 0){
									$dta[$no] = $row;
									$no ++;
								}else{
									if ($dta[$no-1]['svc_cd']     == $row['svc_cd'] &&
										$dta[$no-1]['client_ssn'] == $row['client_ssn'] &&
										$dta[$no-1]['member_ssn'] == $row['member_ssn'] &&
										$dta[$no-1]['from_dt']    == $row['from_dt'] &&
										$dta[$no-1]['from_tt']    == $row['from_tt'] &&
										$dta[$no-1]['seq']        == $row['seq']){
									}else{
										$dta[$no] = $row;
										$no ++;
									}
								}
							}

							$conn->row_free();

							$sql = "drop table tmp_request";

							if (!$conn->execute($sql)){
								echo '3 : '.$conn->error_msg;
							}

							##################################################################
							#
							# 목욕 정부연결
								$dta_cnt = sizeof($dta);

							# 현재데이타 삭제

								for($i=0; $i<$dta_cnt; $i++){
									$arr_i = sizeof($arr);

									if ($dta[$i]['svc_cd'] == '500'){
										if ($arr_i == 0){
											$arr[$arr_i] = $dta[$i];
										}else{
											if ($arr[$arr_i-1]['svc_cd']     == $dta[$i]['svc_cd'] &&
												$arr[$arr_i-1]['client_ssn'] == $dta[$i]['client_ssn'] &&
												$arr[$arr_i-1]['from_dt']    == $dta[$i]['from_dt'] &&
												$arr[$arr_i-1]['from_tt']    == $dta[$i]['from_tt'] &&
												$arr[$arr_i-1]['seq']        == $dta[$i]['seq']){

												$arr[$arr_i-1]['sub_nm']  = $dta[$i]['member_nm'];
												$arr[$arr_i-1]['sub_cd']  = $dta[$i]['member_cd'];
												$arr[$arr_i-1]['sub_ssn'] = $dta[$i]['member_ssn'];
												$arr[$arr_i-1]['sub_gbn'] = $dta[$i]['ms_gbn'];
												$arr[$arr_i-1]['sub_set'] = 'Y';

												if ($dta[$i]['ms_gbn'] == 'm'){
													$arr[$arr_i-1]['work_from_dt']  = $dta[$i]['work_from_dt'];
													$arr[$arr_i-1]['work_from_tt']  = $dta[$i]['work_from_tt'];
													$arr[$arr_i-1]['work_to_dt']    = $dta[$i]['work_to_dt'];
													$arr[$arr_i-1]['work_to_tt']    = $dta[$i]['work_to_tt'];
													$arr[$arr_i-1]['work_proc_min'] = $dta[$i]['work_proc_min'];
												}
											}else{
												$arr[$arr_i] = $dta[$i];
											}
										}
									}else{
										$arr[$arr_i] = $dta[$i];
									}
								}

								unset($dta);

								$dta = $arr;
							#
							##################################################################

							//계획과 실적연결
							$dta_cnt = sizeof($dta);

							for($i=0; $i<$svc_cnt; $i++){
								for($j=0; $j<$dta_cnt; $j++){
									if ($svc[$i]['svc_cd']     == $dta[$j]['svc_cd'] &&
										$svc[$i]['client_ssn'] == $dta[$j]['client_ssn'] &&
										$svc[$i]['member_ssn'] == $dta[$j]['member_ssn'] &&
										$svc[$i]['from_dt']    == $dta[$j]['from_dt']){

										if ($dta[$j]['id'] == 0 && $svc[$i]['data_id'] == 0){
											$dta[$j]['id']       = $svc[$i]['id'];
											$svc[$i]['data_id']  = $svc[$i]['id'];

											$dta[$j]['work_from_dt']  = $svc[$i]['from_dt'];
											$dta[$j]['work_from_tt']  = $svc[$i]['from_tt'];
											$dta[$j]['work_to_dt']    = $svc[$i]['to_dt'];
											$dta[$j]['work_to_tt']    = $svc[$i]['to_tt'];
											$dta[$j]['work_proc_min'] = $svc[$i]['proc_min'];

											break;
										}
									}
								}

								if ($svc[$i]['data_id'] == 0){
									$dta_cnt = sizeof($dta);
									$dta[$dta_cnt]['id'] = 0;

									$dta[$dta_cnt]['client_nm']  = $svc[$i]['client_nm'];
									$dta[$dta_cnt]['client_cd']  = $svc[$i]['client_cd'];
									$dta[$dta_cnt]['client_ssn'] = $svc[$i]['client_ssn'];
									$dta[$dta_cnt]['member_nm']  = $svc[$i]['member_nm'];
									$dta[$dta_cnt]['member_cd']  = $svc[$i]['member_cd'];
									$dta[$dta_cnt]['member_ssn'] = $svc[$i]['member_ssn'];

									$dta[$dta_cnt]['from_dt']  = '';
									$dta[$dta_cnt]['from_tt']  = '';
									$dta[$dta_cnt]['to_dt']    = '';
									$dta[$dta_cnt]['to_tt']    = '';
									$dta[$dta_cnt]['proc_min'] = '';

									//실근무정보
									$dta[$dta_cnt]['work_from_dt']  = $svc[$i]['from_dt'];
									$dta[$dta_cnt]['work_from_tt']  = $svc[$i]['from_tt'];
									$dta[$dta_cnt]['work_to_dt']    = $svc[$i]['to_dt'];
									$dta[$dta_cnt]['work_to_tt']    = $svc[$i]['to_tt'];
									$dta[$dta_cnt]['work_proc_min'] = $svc[$i]['proc_min'];

									$dta[$dta_cnt]['conf_from_dt']  = '';
									$dta[$dta_cnt]['conf_from_tt']  = '';
									$dta[$dta_cnt]['conf_to_dt']    = '';
									$dta[$dta_cnt]['conf_to_tt']    = '';
									$dta[$dta_cnt]['conf_proc_min'] = '';

									$dta[$dta_cnt]['weekday'] = '';
									$dta[$dta_cnt]['holiday'] = 'N';
									$dta[$dta_cnt]['suga_cd'] = '';
									$dta[$dta_cnt]['family']  = 'N';

									$dta[$dta_cnt]['svc_nm'] = $svc[$i]['svc_nm'];
									$dta[$dta_cnt]['svc_cd'] = $svc[$i]['svc_cd'];
									$dta[$dta_cnt]['seq']    = 0;
									$dta[$dta_cnt]['stat']   = '9';
									$dta[$dta_cnt]['stat_gbn'] = '';

									$dta[$dta_cnt]['sub_nm']  = '';
									$dta[$dta_cnt]['sub_cd']  = '';
									$dta[$dta_cnt]['sub_ssn'] = '';
									$dta[$dta_cnt]['sub_gbn'] = '';
									$dta[$dta_cnt]['sub_set'] = 'N';

									$dta[$dta_cnt]['draw'] = false;
								}
							}

							##############################################
							# 임시테이블 생성
								$sql = $nql;
								if (!$conn->execute($sql)){
									echo '4 : '.$conn->error_msg;
								}

								$dta_cnt = sizeof($dta);

								$sql = "insert into tmp_request values";

								for($i=0; $i<$dta_cnt; $i++){
									$sql .= ($i > 0 ? ',' : '');

									$sql .= "(null"
										 .	" ,'".$dta[$i]['id']

										 .  "','".$dta[$i]['client_nm']
										 .  "','".$dta[$i]['client_cd']
										 .  "','".$dta[$i]['client_ssn']

										 .  "','".$dta[$i]['member_nm']
										 .  "','".$dta[$i]['member_cd']
										 .  "','".$dta[$i]['member_ssn']

										 .  "','".$dta[$i]['from_dt']
										 .  "','".$dta[$i]['from_tt']
										 .  "','".$dta[$i]['to_dt']
										 .  "','".$dta[$i]['to_tt']
										 .  "','".$dta[$i]['proc_min']

										 //실근무정보
										 .  "','".$dta[$i]['work_from_dt']
										 .  "','".$dta[$i]['work_from_tt']
										 .  "','".$dta[$i]['work_to_dt']
										 .  "','".$dta[$i]['work_to_tt']
										 .  "','".$dta[$i]['work_proc_min']

										 .  "','".$dta[$i]['conf_from_dt']
										 .  "','".$dta[$i]['conf_from_tt']
										 .  "','".$dta[$i]['conf_to_dt']
										 .  "','".$dta[$i]['conf_to_tt']
										 .  "','".$dta[$i]['conf_proc_min']

										 .  "','".$dta[$i]['weekday']
										 .  "','".$dta[$i]['holiday']
										 .  "','".$dta[$i]['suga_cd']
										 .  "','".$dta[$i]['family']

										 .  "','".$dta[$i]['svc_nm']
										 .  "','".$dta[$i]['svc_cd']
										 .  "','".$dta[$i]['seq']
										 .  "','".$dta[$i]['stat']
										 .  "','".$dta[$i]['stat_gbn']
										 .  "','".$dta[$i]['ms_gbn']

										 .  "','".$dta[$i]['sub_nm']
										 .  "','".$dta[$i]['sub_cd']
										 .  "','".$dta[$i]['sub_ssn']
										 .  "','".$dta[$i]['sub_gbn']
										 .  "','".$dta[$i]['sub_set']
									     .  "')";
								}

								if ($dta_cnt > 0){
									if (!$conn->execute($sql)){
										echo '5 : '.$conn->error_msg;
									}
								}

								unset($dta);

								$sql = "select *
										  from tmp_request
										 order by case when from_dt != '' then from_dt else '99999999' end, svc_cd, client_nm, member_nm, case when from_dt != '' then from_dt else '99999999' end, from_tt, to_tt";

								$conn->query($sql);
								$conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row = $conn->select_row($i);

									$dta[$i] = $row;
								}

								$conn->row_free();

								$sql = "drop table tmp_request";

								if (!$conn->execute($sql)){
									echo '6 : '.$conn->error_msg;
								}
							##############################################

							$cnt     = sizeof($dta);
							$no      = 0;
							$draw_no = 0;

							unset($tmp_date);

							$index = 0;

							for($i=0; $i<$cnt; $i++){
								$msg  = '';
								$draw = false;

								if ($flag == '1' || $flag == '6' || $flag == '2'){
									if (empty($dta[$i]['client_ssn'])){
										$msg .= '<span style=\'color:#ff0000;\'>'.(!empty($msg) ? ', ' : '').'수급자없음</span>';
										$draw = true;
									}
								}

								if ($flag == '1' || $flag == '6' || $flag == '3'){
									if (empty($dta[$i]['member_ssn'])){
										$msg .= '<span style=\'color:#ff0000;\'>'.(!empty($msg) ? ', ' : '').'요양사없음</span>';
										$draw = true;
									}
								}

								if ($flag == '1' || $flag == '6' || $flag == '4'){
									if (empty($dta[$i]['from_dt'])){
										$msg .= '<span style=\'color:#ff0000;\'>'.(!empty($msg) ? ', ' : '').'계획없음</span>';
										$draw = true;
									}
								}

								if ($flag == '1' || $flag == '6' || $flag == '5'){
									if (empty($dta[$i]['work_from_dt']) && empty($dta[$i]['work_from_tt']) && empty($dta[$i]['work_to_tt'])){
										$msg .= '<span style=\'color:#ff0000;\'>'.(!empty($msg) ? ', ' : '').'실적없음</span>';
										$draw = true;
									}else if (empty($dta[$i]['work_from_dt']) || empty($dta[$i]['work_from_tt']) || empty($dta[$i]['work_to_tt'])){
										$msg .= '<span style=\'color:#ff0000;\'>'.(!empty($msg) ? ', ' : '').'실적에러</span>';
										$draw = true;
									}
								}

								if ($flag == '1' || $flag == '9') $draw = true;
								if ($flag == '6') $draw = !$draw;

								if (empty($msg)){
									if ($draw_no % 2 == 0)
										$bgcolor = '#ffffff';
									else
										$bgcolor = '#fafaff';
								}else{
									$str_no = '-';

									if ($draw_no % 2 == 0)
										$bgcolor = '#fff0f0';
									else
										$bgcolor = '#ffe0e0';
								}

								if (!empty($dta[$i]['client_ssn'])){
									$client_nm = $dta[$i]['client_nm'];
								}else{
									$client_nm = '<span style=\'color:#0000ff;\'>'.$dta[$i]['client_nm'].'</span>';
								}

								if ($dta[$i]['svc_cd'] == '500'){
									$member_nm = $dta[$i]['member_nm'];

									if (!empty($msg)){
										if ($dta[$i]['ms_gbn'] == 'm'){
											$member_nm .= '[정]';
											$member_nm .= '/'.$dta[$i]['sub_nm'].'['.($dta[$i]['sub_gbn'] == 'm' ? '정' : '부').']';
										}else{
											$member_nm .= '[부]';
											$member_nm  = $dta[$i]['sub_nm'].'['.($dta[$i]['sub_gbn'] == 'm' ? '정' : '부').']/'.$member_nm;
											$str_no     = '-';
										}

										if ($dta[$i]['sub_set'] != 'Y'){
											$msg .= '<span style=\'color:#0000ff;\'>'.(!empty($msg) ? ', ' : '').$dta[$i]['sub_nm'].'['.($dta[$i]['sub_gbn'] == 'm' ? '정' : '부').']누락</span>';
										}
									}else{
										$member_nm .= '[-]';
									}
								}else{
									$member_nm = $dta[$i]['member_nm'];
								}

								if (empty($dta[$i]['member_ssn'])){
									$member_nm = '<span style=\'color:#0000ff;\'>'.$member_nm.'</span>';
								}

								if ($draw){
									$draw_no ++;

									if ($tmp_date != $dta[$i]['from_dt']){
										if (!empty($tmp_date)){
											if ($flag == '1' || $flag == '9'){
												echo reg_not_data($conn, $myF, $code, $svc_not[$tmp_date], $myF->dateStyle($tmp_date,'.'), $flag);
											}

											$no = 0;
											$str_no = '';
										}

										$tmp_date  = $dta[$i]['from_dt'];

										echo '<tr>';

										if (!empty($dta[$i]['from_dt'])){
											if ($flag != '9'){
												echo '<td class=\'left bold my_bg_gray\' colspan=\'15\'> - '.$myF->dateStyle($dta[$i]['from_dt']).'</td>';
											}else{
												echo '<td class=\'left bold my_bg_gray\' colspan=\'15\'> - '.$myF->dateStyle($dta[$i]['from_dt']).' 미등록</td>';
											}
										}else{
											if ($flag != '9'){
												echo '<td class=\'left bold my_bg_gray\' colspan=\'15\'> - 일자없음</td>';
											}
											$str_no = '-';
										}

										echo '</tr>';
									}

									if ($str_no != '-'){
										$no ++;
										$str_no = $no;
									}

									if ($flag != '9'){
										echo '<tr>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$str_no.'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$dta[$i]['svc_nm'].'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'left\'>'.$client_nm.'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'left\'>'.$member_nm.'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$myF->dateStyle($dta[$i]['from_dt'],'.').'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$myF->timeStyle($dta[$i]['from_tt']).'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$myF->timeStyle($dta[$i]['to_tt']).'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$dta[$i]['proc_min'].(!empty($dta[$i]['proc_min']) ? '분' : '').'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$dta[$i]['stat_gbn'].'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$myF->dateStyle($dta[$i]['work_from_dt'],'.').'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$myF->timeStyle($dta[$i]['work_from_tt']).'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$myF->timeStyle($dta[$i]['work_to_tt']).'</div></td>';
										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$dta[$i]['work_proc_min'].(!empty($dta[$i]['work_proc_min']) ? '분' : '').'</div></td>';

										if ($output != 'excel'){
											echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.($str_no != '-' ? '<input name=\'check[]\' type=\'checkbox\' class=\'checkbox\' value=\''.$dta[$i]['id'].'\' tag=\'N\' onclick=\'set_suga(this.value);\'>' : '-').'</div></td>';
										}else{
											echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>-</div></td>';
										}

										echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'left\' '.($str_no != '-' ? 'id=\'csv_suga[]\'' : '').'>'.$msg.'</div></td>';
										echo '</tr>';

										if ($str_no != '-' && $output != 'excel'){
											echo '<input name=\'client[]\' type=\'hidden\' value=\''.$ed->en($dta[$i]['client_ssn']).'\'>';
											echo '<input name=\'member[]\' type=\'hidden\' value=\''.$ed->en($dta[$i]['member_ssn']).'\'>';

											echo '<input name=\'svc_code[]\' type=\'hidden\' value=\''.$dta[$i]['svc_cd'].'\'>';

											echo '<input name=\'plan_date[]\' type=\'hidden\' value=\''.$dta[$i]['from_dt'].'\'>';
											echo '<input name=\'plan_from[]\' type=\'hidden\' value=\''.$dta[$i]['from_tt'].'\'>';
											echo '<input name=\'plan_to[]\'   type=\'hidden\' value=\''.$dta[$i]['to_tt'].'\'>';
											echo '<input name=\'plan_seq[]\'  type=\'hidden\' value=\''.$dta[$i]['seq'].'\'>';
											echo '<input name=\'plan_time[]\' type=\'hidden\' value=\''.$dta[$i]['proc_min'].'\'>';

											echo '<input name=\'work_date[]\' type=\'hidden\' value=\''.$dta[$i]['work_from_dt'].'\'>';
											echo '<input name=\'work_from[]\' type=\'hidden\' value=\''.$dta[$i]['work_from_tt'].'\'>';
											echo '<input name=\'work_to[]\'   type=\'hidden\' value=\''.$dta[$i]['work_to_tt'].'\'>';
											echo '<input name=\'work_time[]\' type=\'hidden\' value=\''.$dta[$i]['work_proc_min'].'\'>';

											echo '<input name=\'conf_date[]\' type=\'hidden\' value=\''.$dta[$i]['work_from_dt'].'\'>';
											echo '<input name=\'conf_from[]\' type=\'hidden\' value=\''.$dta[$i]['work_from_tt'].'\'>';
											echo '<input name=\'conf_to[]\'   type=\'hidden\' value=\''.$dta[$i]['work_to_tt'].'\'>';
											echo '<input name=\'conf_time[]\' type=\'hidden\' value=\''.$dta[$i]['work_proc_min'].'\'>';

											echo '<input name=\'weekday[]\' type=\'hidden\' value=\''.$dta[$i]['weekday'].'\'>';
											echo '<input name=\'holiday[]\' type=\'hidden\' value=\''.$dta[$i]['holiday'].'\'>';
											echo '<input name=\'family[]\'  type=\'hidden\' value=\''.$dta[$i]['family'].'\'>';
											echo '<input name=\'bipay[]\'   type=\'hidden\' value=\'N\'>';

											echo '<input name=\'suga_code[]\'  type=\'hidden\' value=\''.$dta[$i]['suga_cd'].'\'>';
											echo '<input name=\'suga_name[]\'  type=\'hidden\' value=\'\'>';
											echo '<input name=\'suga_value[]\' type=\'hidden\' value=\'0\'>';
											echo '<input name=\'suga_price[]\' type=\'hidden\' value=\'0\'>';

											echo '<input name=\'change_flag[]\'  type=\'hidden\' value=\'N\'>';
											echo '<input name=\'index_'.$dta[$i]['id'].'\' type=\'hidden\' value=\''.$index.'\'>';

											$index ++;
										}
									}
								}
							}

							if (!empty($tmp_date)){
								if ($flag == '1' || $flag == '9'){
									echo reg_not_data($conn, $myF, $code, $svc_not[$tmp_date], $myF->dateStyle($tmp_date,'.'), $flag);
								}
							}
						?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<?
	if ($output != 'excel'){
		echo $btn;?>
		<div id="layer_win" style="z-index:100; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:#ffffff; position:absolute; top:0px; left:0px; height:0px; width:0px;"></div>
		<div id="layer_div" style="z-index:101; left:0; top:0; width:70%; height:50px; position:absolute; background-color:#ffffff; color:#000000; text-align:left; border:3px solid #cccccc; display:none;"></div>
		<div id="layer_rat" style="z-index:102; left:0; top:0; width:70%; height:50px; position:absolute; color:#000000; text-align:center; padding-top:12px; font-weight:bold; font-size:22px; display:none;"></div>

		<input name="code" type="hidden" value="<?=$code;?>">
		<input name="file" type="hidden" value="<?=$file;?>">
		<input name="output" type="hidden" value="true">
		<input name="onload" type="hidden" value="true">

		</form><?
	}
	//@unlink($file);

	if ($output != 'excel'){
		include_once('../inc/_footer.php');
	}else{
		include_once('../inc/_db_close.php');
	}

	function reg_not_data($conn, $fun, $code, $not_client, $date, $flag){
		$sql = 'select m03_jumin as ssn
				,      m03_name as name
				,      t01_sugup_date as plan_date
				,      t01_sugup_fmtime as from_time
				,      t01_sugup_totime as to_time
				,      t01_sugup_soyotime as proc_time
				,      t01_yname1 as mem_nm1
				,      t01_yname2 as mem_nm2
				,      case t01_status_gbn when \'0\' then \'미수행\'
										   when \'9\' then \'대기\'
										   when \'5\' then \'수행중\'
										   when \'1\' then \'완료\' else \'대기\' end as stat
				,      case t01_svc_subcode when \'200\' then \'방문요양\'
											when \'500\' then \'방문목욕\'
											when \'800\' then \'방문간호\' else \'-\' end as svc_name
				  from m03sugupja
				 inner join t01iljung
					on t01_ccode      = m03_ccode
				   and t01_mkind      = m03_mkind
				   and t01_jumin      = m03_jumin
				   and t01_sugup_date = \''.str_replace('.','',$date).'\'
				   and t01_del_yn     = \'N\'
				 where m03_ccode      = \''.$code.'\'
				   and m03_mkind      = \'0\'
				   and \''.str_replace('.','',$date).'\' between m03_gaeyak_fm and m03_gaeyak_to
				   and m03_del_yn     = \'N\' '.$not_client.'
				 order by m03_name';
		#echo $date.'<br>';
		#echo $not_client.'<br>';
		#echo $sql.'<br><br><br>';

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			if ($i % 2 == 0)
				$bgcolor = '#f8ecda';
			else
				$bgcolor = '#fbe6c6';

			if ($flag != '9'){
				$no = '-';
			}else{
				$no = $i + 1;
			}

			echo '<tr>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$no.'</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$row['svc_name'].'</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'left\'>'.$row['name'].'</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'left\'>'.$row['mem_nm1'].'</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$fun->dateStyle($row['plan_date'],'.').'</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$fun->timeStyle($row['from_time']).'</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$fun->timeStyle($row['to_time']).'</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$row['proc_time'].'분</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>'.$row['stat'].'</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'></div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'></div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'></div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'></div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'center\'>-</div></td>';
			echo '<td style=\'background-color:'.$bgcolor.';\'><div class=\'left\'><span style=\'color:#ff0000;\'>미등록</span></div></td>';
			echo '</tr>';
		}

		$conn->row_free();
	}
?>
<?
	header( "Content-type: application/vnd.ms-excel" ); 
	header( "Content-Disposition: attachment; filename=test.xls" ); 
	header( "Content-Transfer-Encoding: binary" ); 
	header( "Content-Description: PHP4 Generated Data" ); 
	
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");
	

	$code	= $_SESSION["userCenterCode"];
	$mode   = $_POST['mode'] != '' ? $_POST['mode'] : 1;
	$jumin  = $_POST['jumin'];
	$member = $_POST['member'];
	$year	= $_POST['year']  != '' ? $_POST['year']  : date('Y', mktime());
	$month	= $_POST['month'] != '' ? $_POST['month'] : date('m', mktime());

	$init_year = $myF->year();


	$today = '['.date('Y.m.d', mktime()).']';
	

	$sql = "select m00_cname
			  from m00center
			 where m00_mcode = '$code'";
	$c_name = $conn -> get_data($sql);
	

if($mode != 1){ ?>
<div align="center" style="font-size:15pt; font-weight:bold;"><?=$year?>년 <?=$month?>월 월별진행일정(요양보호사)</div>
<div>
	<table>
		<tr>
			<td colspan="3" style="text-align:left; font-size:12pt; font-weight:bold;">센터명 : <?=$c_name?></td>
			<td colspan="6" style="text-align:left; font-size:12pt; font-weight:bold;">요양보호사 : <?=$member?></td>
			<td colspan="3" style="text-align:right; font-size:12pt; font-weight:bold;">출력일자 : <?=$today?></td>
		</tr>
	</table>
</div><?
}else {?>
<div align="center" style="font-size:15pt; font-weight:bold;">당일일정(요양보호사)</div>
<div>
	<table>
		<tr>
			<td colspan="5" style="text-align:left; font-size:12pt; font-weight:bold;">센터명 : <?=$c_name?></td>
			<td colspan="5" style="text-align:left; font-size:12pt; font-weight:bold;"></td>
			<td colspan="3" style="text-align:right; font-size:12pt; font-weight:bold;">출력일자 : <?=$today?></td>
		</tr>
	</table>
</div><?
}?>
<table border="1">
	<colgroup>
		<col width="40px;">
		<?
			if ($mode == 1){?>
				<col width="90px;"><?
			}
		?>
		<col width="90px;">
		<col width="130px;">
		<col width="50px;" span="6">
		<col width="75px;">
		<col width="55px;">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th rowspan="2"><? if($mode == 1){?>No<?}else{?>일자<?}?></th>
			<?
				if ($mode == 1){?>
					<th rowspan="2">
						<div class="help_left">요양보호사</div>
						<div class="help" onmouseover="_show_help(this, '요양보호사 성명을 클릭하면 월별 진행일정을 볼 수 있습니다.');" onmouseout="_hidden_help();"></div>
					</th><?
				}
			?>
			<th rowspan="2">수급자</th>
			<th rowspan="2">서비스</th>
			<th colspan="2">계획시간</th>
			<th colspan="2">수행시간</th>
			<th rowspan="2">실소요<br>시간</th>
			<th rowspan="2">인정<br>시간</th>
			<th rowspan="2" title='녹색완료는 스마트폰으로 정상완료를 한 경우이며, 클릭 시 상세서비스항목이 표시됩니다. 적색완료는 수동으로 입력하여 완료한 경우입니다.'><u>진행상태</u></th>
			<th rowspan="2">위치<br>정보</th>
			<th rowspan="2"><? if($mode == 1){?>비고<?}else{?>주소<?} ?></th>
		</tr>
		<tr>
			<th >시작</th>
			<th >종료</th>
			<th >시작</th>
			<th >종료</th>
		</tr>
	</thead>
	<tbody>
	<?
		if ($mode == 1){
			$date = date('Ymd', mktime());
			$temp_query = "";
			$temp_filed = "";
		}else{
			$date = $year.$month;
			$temp_query = " and m02_yjumin = '".$ed->de($jumin)."'";
			$temp_filed = ", concat(m03_juso1, ' ', m03_juso2) as addr";
		}

		$sql = "select m02_ccode
				,      m02_mkind
				,      m02_yjumin
				,      m02_yname
				,      m03_jumin
				,      m03_name
				,      m03_key
				,      t01_suga_code1
				,      t01_sugup_date
				,      t01_sugup_fmtime
				,      t01_sugup_totime
				,      t01_sugup_seq
				,      t01_conf_fmtime
				,      t01_conf_totime
				,      round((case when time_to_sec(timediff(date_format(concat(t01_wrk_date, t01_conf_totime,'00'), '%H:%i:%s'), date_format(concat(t01_wrk_date, t01_conf_fmtime,'00'), '%H:%i:%s'))) < 0 then 24 * 60 * 60 else 0 end + time_to_sec(timediff(date_format(concat(t01_wrk_date, t01_conf_totime,'00'), '%H:%i:%s'), date_format(concat(t01_wrk_date, t01_conf_fmtime,'00'), '%H:%i:%s')))) / 60 / 60, 1) as work_time
				,      round(t01_conf_soyotime / 60, 1) as conf_time
				,      case when concat(t01_sugup_date, t01_sugup_fmtime) <= date_format(now(), '%Y%m%d%H%i') and t01_status_gbn = '9' then '0' else t01_status_gbn end as status_gbn
				,      t01_modify_pos
				,      t01_modify_yn
				,      date_format(t01_sugup_date, '%d') as plan_day
				,      1 as ms_gbn $temp_filed
				  from t01iljung
				 inner join m02yoyangsa
					on m02_ccode  = t01_ccode
				   and m02_mkind  = ".$conn->_mem_kind()."
				   and m02_yjumin = t01_mem_cd1
				 inner join m03sugupja
					on m03_ccode = t01_ccode
				   and m03_mkind = ".$conn->_client_kind()."
				   and m03_jumin = t01_jumin
				 where t01_ccode      = '$code'
				   and t01_del_yn     = 'N'
				   and t01_sugup_date like '$date%' $temp_query
				 union all
				select m02_ccode
				,      m02_mkind
				,      m02_yjumin
				,      m02_yname
				,      m03_jumin
				,      m03_name
				,      m03_key
				,      t01_suga_code1
				,      t01_sugup_date
				,      t01_sugup_fmtime
				,      t01_sugup_totime
				,      t01_sugup_seq
				,      t01_conf_fmtime
				,      t01_conf_totime
				,      round((case when time_to_sec(timediff(date_format(concat(t01_wrk_date, t01_conf_totime,'00'), '%H:%i:%s'), date_format(concat(t01_wrk_date, t01_conf_fmtime,'00'), '%H:%i:%s'))) < 0 then 24 * 60 * 60 else 0 end + time_to_sec(timediff(date_format(concat(t01_wrk_date, t01_conf_totime,'00'), '%H:%i:%s'), date_format(concat(t01_wrk_date, t01_conf_fmtime,'00'), '%H:%i:%s')))) / 60 / 60, 1) as work_time
				,      round(t01_conf_soyotime / 60, 1) as conf_time
				,      case when concat(t01_sugup_date, t01_sugup_fmtime) <= date_format(now(), '%Y%m%d%H%i') and t01_status_gbn != '1' then '0' else t01_status_gbn end as status_gbn
				,      t01_modify_pos
				,      t01_modify_yn
				,      date_format(t01_sugup_date, '%d') as plan_day
				,      2 as ms_gbn $temp_filed
				  from t01iljung
				  left join m02yoyangsa
					on m02_ccode  = t01_ccode
				   and m02_mkind  = ".$conn->_mem_kind()."
				   and m02_yjumin = t01_mem_cd2
				 inner join m03sugupja
					on m03_ccode = t01_ccode
				   and m03_mkind = ".$conn->_client_kind()."
				   and m03_jumin = t01_jumin
				 where t01_ccode      = '$code'
				   and t01_del_yn     = 'N'
				   and t01_sugup_date like '$date%'
				   and m02_yjumin is not null $temp_query";

		if ($mode == 1){
			$sql .= " order by m02_yname, t01_sugup_fmtime";
		}else{
			$sql .= " order by plan_day, t01_sugup_fmtime";
		}

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$seq = 0;
		$seq_sub = 0;

		if ($row_count > 0){
			$in_count = $row_count;

			$data = array();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$yoy_jumin  .= ($yoy_jumin != "" ? ",'" : "'").$row['m02_yjumin']."'";
				$temp_member = $ed->en($row['m02_yjumin']);
				$temp_client = $ed->en($row['m03_jumin']);

				switch($row['status_gbn']){
				case '0': //미수행
					$status = '미수행';
					break;
				case '1': //완료
					$status = '완료';
					break;
				case '5': //수행중
					$status = '수행중';
					break;
				case '9': //준비중
					$status = '준비중';
					break;
				case 'C': //에러
					$status = '에러';
					break;
				default: //대기
					$status = '대기';
				}
				
				if ($row['status_gbn'] == '1' && $row['t01_modify_yn'] == 'N'){
					$location_img = '<a href="#" onclick="_locationFind(\''.$row['m02_ccode'].'\',\''.$row['m02_mkind'].'\',\''.$temp_client.'\',\''.$row['t01_sugup_date'].'\',\''.$row['t01_sugup_fmtime'].'\',\''.$row['t01_sugup_seq'].'\',\''.$temp_member.'\');"><img src="../image/btn_location_find.gif"></a>';
				}else{
					$location_img = '';
				}

				if ($temp_jumin != $row['m02_yjumin']){
					$temp_jumin = $row['m02_yjumin'];
					$seq ++;
					$seq_sub = 0;
				}

				if ($seq_sub == 0){
					$data[$seq-1][$seq_sub]['no']		= $seq;
					$data[$seq-1][$seq_sub]['member']	= $row['m02_yname'];
					$data[$seq-1][$seq_sub]['code']		= $row['m02_ccode'];
					$data[$seq-1][$seq_sub]['kind']		= $row['m02_mkind'];
					$data[$seq-1][$seq_sub]['jumin']	= $temp_member;
				}
				$data[$seq-1][$seq_sub]['day']				= $row['plan_day'];
				$data[$seq-1][$seq_sub]['client']			= $row['m03_name'];
				$data[$seq-1][$seq_sub]['suga']				= $conn->get_suga($code, $row['t01_suga_code1'], $row['t01_sugup_date']);
				$data[$seq-1][$seq_sub]['plan_from_time']	= $myF->timeStyle($row['t01_sugup_fmtime']);
				$data[$seq-1][$seq_sub]['plan_to_time']		= $myF->timeStyle($row['t01_sugup_totime']);
				$data[$seq-1][$seq_sub]['work_from_time']	= $myF->timeStyle($row['t01_conf_fmtime']);
				$data[$seq-1][$seq_sub]['work_to_time']		= $myF->timeStyle($row['t01_conf_totime']);
				$data[$seq-1][$seq_sub]['work_time']		= $row['work_time'];
				$data[$seq-1][$seq_sub]['conf_time']		= $row['conf_time'];
				$data[$seq-1][$seq_sub]['status']			= $status;
				$data[$seq-1][$seq_sub]['location']			= $location_img;

				if ($mode == 2){
					$data[$seq-1][$seq_sub]['addr']	= $row['addr'];
				}

				$seq_sub ++;
			}
		}

		$conn->row_free();

		for($i=0; $i<sizeof($data); $i++){
			$row_count = sizeof($data[$i]);

			for($j=0; $j<$row_count; $j++){
				?>
				<tr><?
					if ($mode == 1){
						if ($j == 0){?>
							<td style="text-align:center;"	rowspan="<?=$row_count;?>"><?=$data[$i][0]['no'];?></td>
							<td style="text-align:left;"	rowspan="<?=$row_count;?>"><?=$data[$i][0]['member'];?></td><?
						}
					}else{?>
						<td class="center"><?=$data[$i][$j]['day'];?></td><?
					}?>
					<td style="text-align:left;"><?=$data[$i][$j]['client'];?></td>
					<td style="text-align:left;"><?=$data[$i][$j]['suga'];?></td>
					<td style="text-align:center;"><?=$data[$i][$j]['plan_from_time'];?></td>
					<td style="text-align:center;"><?=$data[$i][$j]['plan_to_time'];?></td>
					<td style="text-align:center;"><?=$data[$i][$j]['work_from_time'];?></td>
					<td style="text-align:center;"><?=$data[$i][$j]['work_to_time'];?></td>
					<td style="text-align:center;"><?=$data[$i][$j]['work_time'];?></td>
					<td style="text-align:center;"><?=$data[$i][$j]['conf_time'];?></td>
					<td style="text-align:center;"><?=$data[$i][$j]['status'];?></td>
					<td style="text-align:center;"><?=$data[$i][$j]['location'];?></td>
					<?
						if ($mode == 1){?>
							<td style="text-align:center;" >&nbsp;</td><?
						}else{?>
							<td style="text-align:left;" title="<?=$data[$i][$j]['addr'];?>"><div class='nowrap' style=''><?=$myF->splits($data[$i][$j]['addr'], 12);?></div></td><?
						}
					?>
				</tr><?
			}
		}

		if ($mode == 1 && $yoy_jumin != ''){
			$sql = "select m02_ccode
					,      m02_mkind
					,      m02_yname
					,      m02_yjumin
					 from m02yoyangsa
				    where m02_ccode  = '$code'
					  and m02_mkind  = ".$conn->_mem_kind()."
					  and m02_del_yn = 'N'
					  and m02_yjumin not in ($yoy_jumin)";
			$conn->query($sql);
			$conn->fetch();
			$row_count = $conn->row_count();

			$out_count = $row_count;

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$seq++;?>
				<tr>
					<td style="text-align:center;"><?=$seq;?></td>
					<td style="text-align:left;"><?=$row['m02_yname'];?></td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">-</td>
					<td style="text-align:center;">&nbsp;</td>
				</tr><?
			}

			$conn->row_free();
		}
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left bottom <? if($in_count == 0 || $mode == 2){?>last<?} ?>" colspan="12">
			<?
				if ($mode == 1){?>
					<span>검색된 전체 갯수 : <?=$in_count+$out_count;?> / 당일일정 갯수 : <?=$in_count;?></span><?
				}else{
					if ($in_count > 0){?>
						<span>검색된 갯수 : <?=$in_count;
					}else{?>
						<div style="text-align:center;">::검색된 데이타가 없습니다.::</div><?
					}
				}?>
			</td>
		</tr>
	</tbody>
</table>
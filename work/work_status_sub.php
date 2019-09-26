<?
	if ($_GET['excel'] == true){

		$sql = "select m00_cname
				  from m00center
				 where m00_mcode = '$code'";
		$c_name = $conn -> get_data($sql);

		$r_dt = date('Y.m.d',mktime());

		?>
		<div align="center" style="font-size:15pt; font-weight:bold;"><?=$year?>년<?=$month?>월 근무현황표</div>
		<div>
			<table>
				<tr>
					<td colspan="6" style="text-align:left; font-size:12pt; font-weight:bold;">센터명 : <?=$c_name?></td>
					<td colspan="6" style="text-align:right; font-size:12pt; font-weight:bold;">일자 : <?=$r_dt?></td>
				</tr>
			</table>
		</div>
		<table border="1"><?
	}else{?>
		<table class="my_table" style="width:100%;"><?
	}
?>	<colgroup>
		<col width="40px">
		<col width="50px">
		<col width="90px"><?
		if ($_GET['excel'] == true){?>
			<col width="150px"><?
		}?>
		<col width="50px">
		<col width="90px">
		<col width="50px">
		<col width="70px">
		<col width="90px">
		<col width="130px">
		<col width="90px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자</th>
			<th class="head">연락처</th><?
			if ($_GET['excel'] == true){?>
				<th class="head">주소</th><?
			}?>
			<th class="head">등급</th>
			<th class="head">서비스</th>
			<th class="head">횟수/주</th>
			<th class="head">시간/회</th>
			<th class="head">요양보호사</th>
			<th class="head">근무시간</th>
			<th class="head">연락처</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		include_once('./work_status_query.php');

		ob_start();

		$data_cnt = sizeof($data);

		for($i=0; $i<$data_cnt; $i++){
			$client_seq = $i+1;
			$client_nm  = $data[$i]['client']['nm'];
			$client_row = $data[$i]['client']['cnt'];
			$client_tr  = true;

			$dtl_cnt = sizeof($data[$i]['dtl']);

			for($j=0; $j<$dtl_cnt; $j++){
				$lvl_nm  = $data[$i]['dtl'][$j]['lvl'];
				$tel_no  = $data[$i]['dtl'][$j]['tel'];
				$addr	 = $data[$i]['dtl'][$j]['addr'];
				$dtl_row = $data[$i]['dtl'][$j]['cnt'];
				$dtl_tr	 = true;

				$svc_cnt = sizeof($data[$i]['dtl'][$j]['svc']);

				for($k=0; $k<$svc_cnt; $k++){
					$svc_cd = $data[$i]['dtl'][$j]['svc'][$k]['cd'];
					$svc_nm = $data[$i]['dtl'][$j]['svc'][$k]['nm'];
					$svc_tr = true;

					$list_cnt = $data[$i]['dtl'][$j]['svc'][$k]['cnt'];

					for($l=0; $l<$list_cnt; $l++){
						$weeks		= $data[$i]['dtl'][$j]['svc'][$k][$l]['weeks'];
						$soyotime	= $data[$i]['dtl'][$j]['svc'][$k][$l]['soyotime'];
						$mem_m		= $data[$i]['dtl'][$j]['svc'][$k][$l]['mem_m'];
						$mem_s		= $data[$i]['dtl'][$j]['svc'][$k][$l]['mem_s'];
						$mem_tel	= $data[$i]['dtl'][$j]['svc'][$k][$l]['mem_tel'];

						if ($svc_cd != '500'){
							$week_cnt = $weeks.'일/주';
						}else{
							$week_cnt = $weeks.'회/주';
						}

						$time_cnt = $soyotime.'분/회';
						$mem_list = $mem_m.($mem_s != '' ? ',' : '').$mem_s;

						$list_row	= $data[$i]['dtl'][$j]['svc'][$k][$l]['cnt'];
						$list_tr	= true;

						$works_cnt = $list_row;

						$from_to  = '';

						for($w=0; $w<$works_cnt; $w++){
							$from_time	= $data[$i]['dtl'][$j]['svc'][$k][$l][$w]['from_time'];
							$to_time	= $data[$i]['dtl'][$j]['svc'][$k][$l][$w]['to_time'];
							$work_cnt	= $data[$i]['dtl'][$j]['svc'][$k][$l][$w]['cnt'];
							$from_to   .= ($from_to != '' ? ', ' : '').$from_time.'~'.$to_time.'('.$work_cnt.'회)';
						}

						echo "<tr>";

						if ($client_tr){
							echo "<td class='center' rowspan='$client_row'>$client_seq</td>";
							echo "<td class='left'   rowspan='$client_row'>$client_nm</td>";
							echo "<td class='left'   rowspan='$client_row'>$tel_no</td>";
						}

						if ($dtl_tr){
							if ($_GET['excel'] == true){
								echo "<td class='left'   rowspan='$dtl_row'>$addr</td>";
							}

							echo "<td class='center' rowspan='$dtl_row'>$lvl_nm</td>";
						}

						if ($svc_tr){
							echo "<td class='left' rowspan='$list_cnt'>$svc_nm</td>";
						}

						echo "<td class='left'>$week_cnt</td>";
						echo "<td class='left'>$time_cnt</td>";
						echo "<td class='left'>$mem_list</td>";

						echo "<td class='left'>$from_to</td>";

						echo "<td class='left'>$mem_tel</td>";
						echo "<td class='left last'>&nbsp;</td>";

						echo "</tr>";

						$client_tr	= false;
						$dtl_tr		= false;
						$svc_tr		= false;
						$list_tr	= false;
					}
				}
			}
		}

		$html = ob_get_contents();

		ob_end_clean();

		echo $html;
	?>
	</tbody>
	<tbody>
		<tr><?
			if ($row_count > 0){?>
				<td class="left last bottom" colspan="11"><?=$myF->message($row_count, 'N');?></td><?
			}else{?>
				<td class="center last bottom" colspan="11"><?=$myF->message('nodata', 'N');?></td><?
			}?>
		</tr>
	</tbody>
</table>
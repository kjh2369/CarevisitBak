<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once('../pdf/work_status.php');

	define(__COUNT__, 50);

	$conn->set_name('euckr');

	$code	= $_GET['code'];
	$kind	= $_GET['kind'];
	$year	= $_GET['year'];
	$month	= $_GET['month'];

	$lsVal1 = '일반';
	$lsVal2 = '등급';

	require_once('./work_status_query.php');

	$pdf = new MYPDF();
	$pdf->year = $year;
	$pdf->month = intval($month);
	$pdf->Open();
	$pdf->_setDefaultFont();
	$pdf->AliasNbPages();
	$pdf->SetFillColor(220,220,220);
	$pdf->MY_ADDPAGE();
	$pdf->SetAutoPageBreak(true, $pdf->row_height);

	$col = $pdf->default_col();

	$pdf->set_font(7);

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

				// 근무시간의 문자길이가 긴경우를 찾아서 행의 높이를 계산할 값을 구한다.
				$tmp_list_cnt   = 0;
				$tmp_row_height = 0;

				for($l=0; $l<$list_cnt; $l++){
					if ($tmp_list_cnt < $data[$i]['dtl'][$j]['svc'][$k][$l]['cnt']){
						$tmp_list_cnt = $data[$i]['dtl'][$j]['svc'][$k][$l]['cnt'];
					}
				}

				$tmp_list_cnt = ceil($tmp_list_cnt / 2) - 1;

				$tmp_list_height = $pdf->row_height * $tmp_list_cnt;

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
					$from_to   = '';

					$tmp_row_height = 0;

					for($w=0; $w<$works_cnt; $w++){
						$from_time	= $data[$i]['dtl'][$j]['svc'][$k][$l][$w]['from_time'];
						$to_time	= $data[$i]['dtl'][$j]['svc'][$k][$l][$w]['to_time'];
						$work_cnt	= $data[$i]['dtl'][$j]['svc'][$k][$l][$w]['cnt'];
						$from_to   .= ($from_to != '' ? ', ' : '').$from_time.'~'.$to_time.'('.$work_cnt.'회)';

						if ($w > 0 && $w % 2 == 0){
							 //$tmp_row_height += $pdf->row_height;
							 $tmp_row_height = $tmp_list_height;
						}
					}

					if ($client_tr){
						$x = $pdf->left;

						$pdf->SetX($x);

						$pdf->Cell($col[0],  $pdf->row_height * $client_row + $tmp_list_height, number_format($client_seq), 1, 0, 'C');
						$pdf->Cell($col[1],  $pdf->row_height * $client_row + $tmp_list_height, $client_nm, 1, 0, 'C');
					}

					if ($dtl_tr){
						$x = $pdf->left + $col[0] + $col[1];

						$pdf->SetX($x);

						if ($pdf->GetStringWidth($addr) > $col[3] * 0.93){
							$tmp_arr  = explode(' ', $addr);
							$tmp_cnt  = sizeof($tmp_arr);
							$tmp_addr = '';

							for($a=$tmp_cnt; $a>=0; $a--){

								if ($pdf->GetStringWidth($tmp_arr[$a].' '.$tmp_addr) > $col[3] * 0.95){
									break;
								}else{
									$tmp_addr = $tmp_arr[$a].' '.$tmp_addr;
								}
							}
							$addr = $tmp_addr;
						}

						$pdf->Cell($col[2],  $pdf->row_height * $dtl_row + $tmp_list_height, $lvl_nm, 1, 0, 'C');
						$pdf->Cell($col[3],  $pdf->row_height * $dtl_row + $tmp_list_height, $addr.$tmp_text, 1, 0, 'L');
						$pdf->Cell($col[4],  $pdf->row_height * $dtl_row + $tmp_list_height, $tel_no, 1, 0, 'L');
					}

					if ($svc_tr){
						$x = $pdf->left + $col[0] + $col[1] + $col[2] + $col[3] + $col[4];

						$pdf->SetX($x);

						$pdf->Cell($col[5],  $pdf->row_height * $list_cnt + $tmp_list_height, $myF->euckr($svc_nm), 1, 0, 'C');

						$x = $pdf->left + $col[0] + $col[1] + $col[2] + $col[3] + $col[4] + $col[5];
					}

					$pdf->SetX($x);

					$pdf->Cell($col[6],  $pdf->row_height + $tmp_row_height, $week_cnt, 1, 0, 'C');
					$pdf->Cell($col[7],  $pdf->row_height + $tmp_row_height, $time_cnt, 1, 0, 'C');
					$pdf->Cell($col[8],  $pdf->row_height + $tmp_row_height, $mem_list, 1, 0, 'L');

					if ($tmp_row_height > 0){
						$tmp_x = $pdf->GetX();
						$tmp_y = $pdf->GetY();

						$pdf->SetXY($tmp_x, $tmp_y+1);
						$pdf->MultiCell($col[9], 5, $from_to, 0, 'L');
						$pdf->SetXY($tmp_x, $tmp_y);
						$pdf->Cell($col[9], $pdf->row_height + $tmp_row_height, '', 1, 0, 'L');
					}else{
						$pdf->Cell($col[9], $pdf->row_height + $tmp_row_height, $from_to, 1, 0, 'L');
					}

					$pdf->Cell($col[10],  $pdf->row_height + $tmp_row_height, $mem_tel, 1, 0, 'L');
					$pdf->Cell($col[11],  $pdf->row_height + $tmp_row_height, '', 1, 1, 'C');

					$client_tr	= false;
					$dtl_tr		= false;
					$svc_tr		= false;
					$list_tr	= false;
				}
			}
		}
	}

	$pdf->Output();

	include_once('../inc/_db_close.php');
?>
<script>self.focus();</script>
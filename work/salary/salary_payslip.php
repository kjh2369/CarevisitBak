<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once('../pdf/pdf_payslip.php');

	$conn->set_name('euckr');

	$code	= $_GET['code'];			//����ڵ�
	$kind	= '0';			//����з�
	$year	= $_GET['year'];			//��
	$month	= $_GET['month'];			//��
	$member	= $ed->de($_GET['member']);	//��纸ȣ�� �ֹι�ȣ

	$pdf = new MYPDF();
	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);
	$pdf->Open();
	$pdf->SetFillColor(220,220,220);

	$pdf->year	= $year;
	$pdf->month	= $month;
	$pdf->center_name	= $myF->euckr($_SESSION['userCenterName']);

	$sql = "select m00_cname, m00_ctel
			  from m00center
			 where m00_mcode = '$code'
			   and m00_mkind = '$kind'";

	$temp_center = $conn->get_array($sql);
	$center_name = $temp_center['m00_cname'];
	$center_tel  = $myF->phoneStyle($temp_center['m00_ctel']);
	unset($temp_center);

	if ($member == 'all'){
		$sql = "select salary_jumin
				  from salary_basic
				 inner join m02yoyangsa
				    on m02_ccode   = org_no
				   and m02_yjumin  = salary_jumin
				 where org_no      = '$code'
				   and salary_yymm = '$year$month'
				 order by m02_yname";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$member_list[$i] = $row['salary_jumin'];
		}

		$conn->row_free();

		$member_count = $row_count;
	}else{
		$member_list[0] = $member;
		$member_count	= 1;
	}

	for($m_index=0; $m_index<$member_count; $m_index++){
		$pdf->pos = $m_index % 2;
		$jumin    = $member_list[$m_index];
		$sql = "select m02_yname as name
				,      m02_rank_pay as pay
				  from m02yoyangsa
				 where m02_ccode  = '$code'
				   and m02_mkind  = (select min(m00_mkind) from m00center where m00_mcode = '$code')
				   and m02_yjumin = '$jumin'";

		$member = $conn->get_array($sql);

		$pdf->member_name	= $member['name'];
		$rank_pay			= $member['pay'];

		// �޿�����Ÿ
		$sql = "select salary_basic.work_cnt
				,      salary_basic.work_time
				,      salary_basic.weekly_cnt
				,      salary_basic.paid_cnt
				,      salary_basic.bath_cnt
				,      salary_basic.nursing_cnt

				,      salary_basic.prolong_hour
				,      salary_basic.night_hour
				,      salary_basic.holiday_hour
				,      salary_basic.holiday_prolong_hour
				,      salary_basic.holiday_night_hour
				,      salary_basic.prolong_hour + salary_basic.night_hour + salary_basic.holiday_hour + salary_basic.holiday_prolong_hour + salary_basic.holiday_night_hour as tot_sudang_hour

				,      salary_basic.base_pay
				,      salary_basic.weekly_pay
				,      salary_basic.paid_pay
				,      salary_basic.bath_pay
				,      salary_basic.nursing_pay
				,      salary_basic.meal_pay
				,      salary_basic.car_keep_pay
				,      salary_basic.bojeon_pay
				,      salary_basic.base_pay + salary_basic.weekly_pay + salary_basic.paid_pay + salary_basic.bath_pay + salary_basic.nursing_pay + salary_basic.meal_pay + salary_basic.car_keep_pay + salary_basic.bojeon_pay as tot_basic_pay

				,      salary_basic.prolong_pay
				,      salary_basic.night_pay
				,      salary_basic.holiday_pay
				,      salary_basic.holiday_prolong_pay
				,      salary_basic.holiday_night_pay
				,      salary_basic.rank_pay
				,      salary_basic.prolong_pay + salary_basic.night_pay + salary_basic.holiday_pay + salary_basic.holiday_prolong_pay + salary_basic.holiday_night_pay as tot_sudang_pay

				,      salary_basic.pension_amt
				,      salary_basic.health_amt
				,      salary_basic.care_amt
				,      salary_basic.employ_amt
				,      salary_basic.pension_amt + salary_basic.health_amt + salary_basic.care_amt + salary_basic.employ_amt as tot_ins_pay

				,      salary_basic.tax_amt_1
				,      salary_basic.tax_amt_2
				,      salary_basic.tax_amt_1 + salary_basic.tax_amt_2 as tot_tax_pay

				,      salary_amt.basic_total_amt
				,      salary_amt.addon_total_amt
				,      salary_amt.total_amt
				,      salary_amt.basic_deduct_amt
				,      salary_amt.addon_deduct_amt
				,      salary_amt.deduct_amt
				,      salary_amt.diff_amt

				  from salary_basic
				  left join salary_amt
					on salary_amt.org_no       = salary_basic.org_no
				   and salary_amt.salary_yymm  = salary_basic.salary_yymm
				   and salary_amt.salary_jumin = salary_basic.salary_jumin
				 where salary_basic.org_no       = '$code'
				   and salary_basic.salary_yymm  = '$year$month'
				   and salary_basic.salary_jumin = '$jumin'";

		$salary = $conn->get_array($sql);

		if (!$salary['rank_pay']) $salary['rank_pay'] = $rank_pay;

		$sql = "select salary_type
				,      salary_index
				,      salary_subject
				,      salary_pay
				  from salary_addon
				 where org_no = '$code'";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$index[1] = -1;
		$index[2] = -1;

		$addon_pay[1] = $salary['rank_pay'];
		$addon_pay[2] = 0;

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$index[intval($row['salary_type'])] ++;

			$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['index']	= $row['salary_index'];
			$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['subject']	= $row['salary_subject'];
			$salary_addon[$row['salary_type']][$index[intval($row['salary_type'])]]['pay']		= $row['salary_pay'];
		}

		$conn->row_free();

		$sql = "select salary_type
				,      salary_index
				,      salary_pay
				  from salary_addon_pay
				 where org_no       = '$code'
				   and salary_yymm  = '$year$month'
				   and salary_jumin = '$jumin'";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		$temp_addon_count[1] = sizeof($salary_addon[1]);
		$temp_addon_count[2] = sizeof($salary_addon[2]);

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			for($j=0; $j<$temp_addon_count[$row['salary_type']]; $j++){
				if ($salary_addon[$row['salary_type']][$j]['index'] == $row['salary_index']){
					$salary_addon[$row['salary_type']][$j]['pay'] = $row['salary_pay'];
					break;
				}
			}
		}

		for($i=1; $i<=2; $i++){
			for($j=0; $j<$temp_addon_count[$i]; $j++){
				$addon_pay[$i] += $salary_addon[$i][$j]['pay'];
			}
		}

		$conn->row_free();

		if ($pdf->pos == 0) $pdf->AddPage($pdf->direction, $pdf->paper_size);

		$pdf->SetFont($pdf->font_name_kor,'B',17);

		$pdf->set_default_xy();
		$pdf->Cell($pdf->w, $pdf->row_height, intval($pdf->month).'�� �޿�����', 0, 1, 'C');

		$pdf->set_default_xy();
		$pdf->set_font(11);
		$pdf->Cell($pdf->w / 2, $pdf->text_height, $center_name.'('.$center_tel.')', 0, 0, 'L');
		$pdf->Cell($pdf->w / 2, $pdf->text_height, '���� : '.$pdf->member_name, 0, 1, 'R');

		$pdf->set_default_xy();
		$pdf->set_caption_font('B');
		$pdf->Cell($pdf->w * 0.33, $pdf->row_height, '�޿��Ѿ�(A + B + E)', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.33, $pdf->row_height, '�����ݾ�(C + D + F)', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.34, $pdf->row_height, '�������޾�', 1, 1, 'C', true);

		$pdf->set_default_xy();
		$pdf->set_text_font();
		$pdf->Cell($pdf->w * 0.33, $pdf->text_height, number_format($salary['tot_basic_pay']+$salary['tot_sudang_pay']+$addon_pay[1]), 1, 0, 'C');
		$pdf->Cell($pdf->w * 0.33, $pdf->text_height, number_format($salary['tot_ins_pay']+$salary['tot_tax_pay']+$addon_pay[2]), 1, 0, 'C');
		$pdf->Cell($pdf->w * 0.34, $pdf->text_height, number_format(($salary['tot_basic_pay']+$salary['tot_sudang_pay']+$addon_pay[1])-($salary['tot_ins_pay']+$salary['tot_tax_pay']+$addon_pay[2])), 1, 1, 'C');

		$pdf->set_default_xy(0, 5);
		$pdf->set_caption_font('B');
		$pdf->Cell($pdf->w, $pdf->row_height, '�⺻�ٹ�(�⺻�޿�)', 1, 1, 'C', true);

		$pdf->set_default_xy();
		$pdf->set_caption_font();
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '�ٹ��ϼ�', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '�ٹ��ð�', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '�����ϼ�', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '�����ϼ�', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.18, $pdf->row_height, '���Ƚ��', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.18, $pdf->row_height, '��ȣȽ��', 1, 1, 'C', true);

		$pdf->set_default_xy();
		$pdf->set_text_font();
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height * 3 + $pdf->row_height, number_format($salary['work_cnt']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['work_time']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['weekly_cnt']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['paid_cnt']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['bath_cnt']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['nursing_cnt']), 1, 1, 'R');

		$pdf->set_default_xy($pdf->w * 0.16);
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['base_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['weekly_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['paid_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['bath_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['nursing_pay']), 1, 1, 'R');

		$pdf->set_default_xy($pdf->w * 0.16);
		$pdf->set_caption_font();
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '�Ĵ뺸��', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '����������', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '��������', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.36, $pdf->row_height, '�հ�(A)', 1, 1, 'C', true);

		$pdf->set_default_xy($pdf->w * 0.16);
		$pdf->set_text_font();
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['meal_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['car_keep_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['bojeon_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.36, $pdf->text_height, number_format($salary['tot_basic_pay']), 1, 1, 'R');

		$pdf->set_default_xy(0, 5);
		$pdf->set_caption_font('B');
		$pdf->Cell($pdf->w, $pdf->row_height, '�ʰ��ٹ�', 1, 1, 'C', true);

		$pdf->set_default_xy();
		$pdf->set_caption_font();
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '����', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '�߰�', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '����', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '���Ͽ���', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.18, $pdf->row_height, '���Ͼ߰�', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.18, $pdf->row_height, '�հ�(B)', 1, 1, 'C', true);

		$pdf->set_default_xy();
		$pdf->set_text_font();
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['prolong_hour'], 1), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['night_hour'], 1), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['holiday_hour'], 1), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['holiday_prolong_hour'], 1), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['holiday_night_hour'], 1), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['tot_sudang_hour'], 1), 1, 1, 'R');

		$pdf->set_default_xy();
		$pdf->set_text_font();
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['prolong_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['night_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['holiday_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['holiday_prolong_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['holiday_night_pay']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['tot_sudang_pay']), 1, 1, 'R');

		$pdf->set_default_xy(0, 5);
		$pdf->set_caption_font('B');
		$pdf->Cell($pdf->w * 0.18, $pdf->row_height + $pdf->text_height, '�����׸�', 1, 0, 'C', true);
		$pdf->set_caption_font('');
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '���ο���', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '�ǰ�����', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '�����', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '��뺸��', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.18, $pdf->row_height, '�հ�(C)', 1, 1, 'C', true);

		$pdf->set_default_xy($pdf->w * 0.18);
		$pdf->set_text_font();
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['pension_amt']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['health_amt']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['care_amt']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['employ_amt']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['tot_ins_pay']), 1, 1, 'R');

		$pdf->set_default_xy(0, 5);
		$pdf->set_caption_font('B');
		$pdf->Cell($pdf->w * 0.18, $pdf->row_height + $pdf->text_height, '�ҵ漼', 1, 0, 'C', true);
		$pdf->set_caption_font('');
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '���ټ�', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.16, $pdf->row_height, '�ֹμ�', 1, 0, 'C', true);
		$pdf->Cell($pdf->w * 0.18, $pdf->row_height, '�հ�(D)', 1, 1, 'C', true);

		$pdf->set_default_xy($pdf->w * 0.18);
		$pdf->set_text_font();
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['tax_amt_1']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.16, $pdf->text_height, number_format($salary['tax_amt_2']), 1, 0, 'R');
		$pdf->Cell($pdf->w * 0.18, $pdf->text_height, number_format($salary['tot_tax_pay']), 1, 1, 'R');

		$pdf->set_default_xy(0, 5);
		$temp_y = $pdf->GetY();
		$pdf->set_caption_font('B');
		$pdf->Cell($pdf->w * 0.48, $pdf->row_height, '�� �� �� ��(+)', 1, 1, 'C', true);

		$pdf->set_text_font();
		$pdf->set_default_xy();
		$pdf->Cell($pdf->w * 0.48 * 0.6, $pdf->text_height, '�հ�(E)', 1, 0, 'L', true);
		$pdf->Cell($pdf->w * 0.48 * 0.4, $pdf->text_height, number_format($addon_pay[1]), 1, 1, 'R');

		$pdf->set_default_xy();
		$pdf->Cell($pdf->w * 0.48 * 0.6, $pdf->text_height, '���޼���', 1, 0, 'L');
		$pdf->Cell($pdf->w * 0.48 * 0.4, $pdf->text_height, number_format($salary['rank_pay']), 1, 1, 'R');

		$count = sizeof($salary_addon[1]);

		for($i=0; $i<$count; $i++){
			if ($salary_addon[1][$i]['pay'] > 0){
				$pdf->set_default_xy();
				$pdf->Cell($pdf->w * 0.48 * 0.6, $pdf->text_height, $salary_addon[1][$i]['subject'], 1, 0, 'L');
				$pdf->Cell($pdf->w * 0.48 * 0.4, $pdf->text_height, number_format($salary_addon[1][$i]['pay']), 1, 1, 'R');
			}
		}

		$pdf->set_caption_font('B');
		$pdf->set_default_xy($pdf->w * 0.52, $temp_y - $pdf->GetY());
		$pdf->Cell($pdf->w * 0.48, $pdf->row_height, '�� �� �� ��(-)', 1, 1, 'C', true);

		$pdf->set_text_font();
		$pdf->set_default_xy($pdf->w * 0.52);
		$pdf->Cell($pdf->w * 0.48 * 0.6, $pdf->text_height, '�հ�(F)', 1, 0, 'L', true);
		$pdf->Cell($pdf->w * 0.48 * 0.4, $pdf->text_height, number_format($addon_pay[2]), 1, 1, 'R');

		$count = sizeof($salary_addon[2]);

		for($i=0; $i<$count; $i++){
			if ($salary_addon[2][$i]['pay'] > 0){
				$pdf->set_default_xy($pdf->w * 0.52);
				$pdf->Cell($pdf->w * 0.48 * 0.6, $pdf->text_height, $salary_addon[2][$i]['subject'], 1, 0, 'L');
				$pdf->Cell($pdf->w * 0.48 * 0.4, $pdf->text_height, number_format($salary_addon[2][$i]['pay']), 1, 1, 'R');
			}
		}

		$pdf->SetY($pdf->top);

		$pdf->draw_border();
	}
	$pdf->Output();

	include_once('../inc/_db_close.php');
?>
<script>
	self.focus();
</script>
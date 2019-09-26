<?
	include('../inc/_db_open.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');
	require('../pdf/pdf_center_amt_table.php');

	define('__ROW_LIMIT__',30);

	$conn->set_name('euckr');

	$code		= $_GET['code'];
	$kind		= $_GET['kind'];
	$year		= $_GET['year'];
	$month		= $_GET['month'];
	$type		= $_GET['type'];



	if($month == ''){
		$pdf = new MYPDF('L');
		$pdf->AliasNbPages();
		$pdf->AddUHCFont('굴림','Gulim');
		$pdf->Open();
		$pdf->SetFillColor(220,220,220);

		// 헤더값 설정
		$pdf->year			= $year;		//년
		//$pdf->month			= $month;		//월

		$height = 5;

		$pdf->AddPage('L', 'A4');


		$sql = "select right(salary_basic.salary_yymm, 2) as mm
			,      sum(salary_basic.pension_amt) as person_pension
			,      sum(salary_basic.health_amt) as person_health
			,      sum(salary_basic.care_amt) as person_care
			,      sum(salary_basic.employ_amt) as person_employ
			,      sum(salary_basic.pension_amt) +
				   sum(salary_basic.health_amt) +
				   sum(salary_basic.care_amt) +
				   sum(salary_basic.employ_amt) as person_tot
			,      sum(salary_center_amt.pension_amt) as center_pension
			,      sum(salary_center_amt.health_amt) as center_health
			,      sum(salary_center_amt.care_amt) as center_care
			,      sum(salary_center_amt.employ_amt) as center_employ
			,      sum(salary_center_amt.sanje_amt) as center_sanje
			,      sum(salary_center_amt.pension_amt) +
				   sum(salary_center_amt.health_amt) +
				   sum(salary_center_amt.care_amt) +
				   sum(salary_center_amt.employ_amt) +
				   sum(salary_center_amt.sanje_amt) as center_tot
			,      sum(salary_center_amt.pension_amt) +
				   sum(salary_center_amt.health_amt) +
				   sum(salary_center_amt.care_amt) +
				   sum(salary_center_amt.employ_amt) +
				   sum(salary_center_amt.sanje_amt) as sanje_tot
			  from salary_basic
			 inner join salary_center_amt
				on salary_center_amt.org_no       = salary_basic.org_no
			   and salary_center_amt.salary_yymm  = salary_basic.salary_yymm
			   and salary_center_amt.salary_jumin = salary_basic.salary_jumin";
		/*
		$sql .= " INNER JOIN (
						SELECT jumin
						,      employ_stat
						,      DATE_FORMAT(join_dt, '%Y%m') AS join_ym
						,      DATE_FORMAT(ifnull(quit_dt,'9999-12-31'), '%Y%m') AS quit_ym
						  FROM mem_his
						 WHERE org_no = '".$code."'
						) AS his
					 ON his.jumin = salary_basic.salary_jumin
					AND his.join_ym <= salary_basic.salary_yymm
					AND his.quit_ym >= salary_basic.salary_yymm";
		*/

		$sql .= "
			 where salary_basic.org_no = '$code'
			   and salary_basic.salary_yymm like '$year%'
			 group by right(salary_basic.salary_yymm, 2)
			 order by mm";

		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		$pdf->SetFont('굴림','B',9);

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$pdf->rowCount = $rowCount;
			$total = $row['person_tot'] + $row['center_tot'];

			$pdf->SetX(14);

			$pdf->SetLineWidth(0.6);
			$pdf->Line(116.5,33,157.5,33);
			$pdf->Line(263,33,284,33);
			$pdf->SetLineWidth(0.2);

			$pdf->Cell(18,	$height,	$row['mm'],	1,	0, 'C');
			$pdf->Cell(21,	$height,	number_format($row['person_pension']),	1,	0, 'R');
			$pdf->Cell(21,	$height,	number_format($row['person_health']),	1,	0, 'R');
			$pdf->Cell(21,	$height,	number_format($row['person_care']),	1,	0, 'R');
			$pdf->Cell(21,	$height,	number_format($row['person_employ']),	1,	0, 'R');
			$pdf->Cell(21,	$height,	number_format($row['person_tot']),	1,	0, 'R', true);
			$pdf->Cell(21,	$height,	number_format($row['center_pension']),	1,	0, 'R');
			$pdf->Cell(21,	$height,	number_format($row['center_health']),	1,	0, 'R');
			$pdf->Cell(21,	$height,	number_format($row['center_care']),	1,	0, 'R');
			$pdf->Cell(21,	$height,	number_format($row['center_employ']),	1,	0, 'R');
			$pdf->Cell(21,	$height,	number_format($row['center_sanje']),	1,	0, 'R');
			$pdf->Cell(21,	$height,	number_format($row['center_tot']),	1,	0, 'R', true);
			$pdf->Cell(21,	$height,	number_format($total),	1,	1, 'R', true);

				$tot_person_pension	+= $row['person_pension'];
				$tot_person_health	+= $row['person_health'];
				$tot_person_care	+= $row['person_care'];
				$tot_person_employ	+= $row['person_employ'];
				$tot_person_tot	+= $row['person_tot'];
				$tot_center_pension	+= $row['center_pension'];
				$tot_center_health	+= $row['center_health'];
				$tot_center_care	+= $row['center_care'];
				$tot_center_employ	+= $row['center_employ'];
				$tot_center_sanje	+= $row['center_sanje'];
				$tot_center_tot	+= $row['center_tot'];
				$tot_total	+= $total;
		}

		$pdf->SetX(14);

		$pdf->Cell(18,	$height,	'합계',	1,	0, 'C', true);
		$pdf->Cell(21,	$height,	number_format($tot_person_pension),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_person_health),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_person_care),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_person_employ),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_person_tot),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_center_pension),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_center_health),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_center_care),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_center_employ),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_center_sanje),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_center_tot),	1,	0, 'R', true);
		$pdf->Cell(21,	$height,	number_format($tot_total),	1,	1, 'R', true);

		$conn->row_free();

	}else {
		$pdf = new MYPDF('L');
		$pdf->AliasNbPages();
		$pdf->AddUHCFont('굴림','Gulim');
		$pdf->Open();
		$pdf->SetFillColor(220,220,220);

		// 헤더값 설정
		$pdf->year			= $year;		//년
		$pdf->month			= $month;		//월

		$height = 5;

		$pdf->AddPage('L', 'A4');

			$sql = "select m02_yname as name
			,      salary_basic.salary_jumin as jumin
			,      salary_basic.pension_amt as person_pension
			,      salary_basic.health_amt as person_health
			,      salary_basic.care_amt as person_care
			,      salary_basic.employ_amt as person_employ
			,      salary_basic.pension_amt +
				   salary_basic.health_amt +
				   salary_basic.care_amt +
				   salary_basic.employ_amt as person_tot
			,      salary_center_amt.pension_amt as center_pension
			,      salary_center_amt.health_amt as center_health
			,      salary_center_amt.care_amt as center_care
			,      salary_center_amt.employ_amt as center_employ
			,      salary_center_amt.sanje_amt as center_sanje
			,      salary_center_amt.pension_amt +
				   salary_center_amt.health_amt +
				   salary_center_amt.care_amt +
				   salary_center_amt.employ_amt +
				   salary_center_amt.sanje_amt as center_tot
			  from salary_basic
			 inner join salary_center_amt
				on salary_center_amt.org_no       = salary_basic.org_no
			   and salary_center_amt.salary_yymm  = salary_basic.salary_yymm
			   and salary_center_amt.salary_jumin = salary_basic.salary_jumin
			 inner join m02yoyangsa
				on m02_ccode = salary_basic.org_no
			   and m02_mkind = '0'
			   and m02_yjumin = salary_basic.salary_jumin
			   and m02_del_yn = 'N'";

			//퇴사자 걸러내기
			$sql .= " INNER JOIN (
							SELECT jumin
							,      employ_stat
							  FROM mem_his
							 WHERE org_no = '".$code."'
							   AND DATE_FORMAT(join_dt, '%Y%m') <= '".$year.($month < 10 ? '0'.$month : $month)."'
							   AND DATE_FORMAT(ifnull(quit_dt,'9999-12-31'), '%Y%m') >= '".$year.($month < 10 ? '0'.$month : $month)."'
							) AS his
						 ON his.jumin = salary_basic.salary_jumin";

			$sql .= " where salary_basic.org_no = '$code'
						and salary_basic.salary_yymm = '".$year.($month < 10 ? '0'.$month : $month)."'";

			if($type == 'Y'){
				$sql .= "and salary_basic.pension_amt
						   + salary_basic.health_amt
						   + salary_basic.care_amt
						   + salary_basic.employ_amt
						   + salary_center_amt.pension_amt
						   + salary_center_amt.health_amt
						   + salary_center_amt.care_amt
						   + salary_center_amt.employ_amt
						   + salary_center_amt.sanje_amt > 0";
			}
			$sql .= " group by jumin
			          order by name  ";
			
			//if($debug) echo nl2br($sql);
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			$pdf->SetFont('굴림','B',9);

			$row_no = 0;
			$pdf->rowCount = $rowCount;

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				$total = $row['person_tot'] + $row['center_tot'];

				$pdf->SetX(14);

				$pdf->SetLineWidth(0.6);
				$pdf->Line(124,33,164,33);
				$pdf->Line(244,33,264,33);
				$pdf->Line(264,33,284,33);
				$pdf->SetLineWidth(0.2);

				$pdf->Cell(30,	$height,	$row['name'],	1,	0, 'L');
				$pdf->Cell(20,	$height,	number_format($row['person_pension']),	1,	0, 'R');
				$pdf->Cell(20,	$height,	number_format($row['person_health']),	1,	0, 'R');
				$pdf->Cell(20,	$height,	number_format($row['person_care']),	1,	0, 'R');
				$pdf->Cell(20,	$height,	number_format($row['person_employ']),	1,	0, 'R');
				$pdf->Cell(20,	$height,	number_format($row['person_tot']),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($row['center_pension']),	1,	0, 'R');
				$pdf->Cell(20,	$height,	number_format($row['center_health']),	1,	0, 'R');
				$pdf->Cell(20,	$height,	number_format($row['center_care']),	1,	0, 'R');
				$pdf->Cell(20,	$height,	number_format($row['center_employ']),	1,	0, 'R');
				$pdf->Cell(20,	$height,	number_format($row['center_sanje']),	1,	0, 'R');
				$pdf->Cell(20,	$height,	number_format($row['center_tot']),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($total),	1,	1, 'R', true);

				$tot_person_pension	+= $row['person_pension'];
				$tot_person_health	+= $row['person_health'];
				$tot_person_care	+= $row['person_care'];
				$tot_person_employ	+= $row['person_employ'];
				$tot_person_tot		+= $row['person_tot'];
				$tot_center_pension	+= $row['center_pension'];
				$tot_center_health	+= $row['center_health'];
				$tot_center_care	+= $row['center_care'];
				$tot_center_employ	+= $row['center_employ'];
				$tot_center_sanje	+= $row['center_sanje'];
				$tot_center_tot	+= $row['center_tot'];
				$tot_total	+= $total;

				$row_no ++;


				if ($row_no % __ROW_LIMIT__ == 0){


				$pdf->SetX(14);

				$pdf->Cell(30,	$height,	'합계',	1,	0, 'C', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_pension),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_health),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_care),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_employ),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_tot),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_pension),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_health),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_care),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_employ),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_sanje),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_tot),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_total),	1,	1, 'R', true);

				}
			}
				$pdf->SetX(14);
				$pdf->Cell(30,	$height,	'합계',	1,	0, 'C', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_pension),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_health),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_care),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_employ),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_person_tot),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_pension),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_health),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_care),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_employ),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_sanje),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_center_tot),	1,	0, 'R', true);
				$pdf->Cell(20,	$height,	number_format($tot_total),	1,	1, 'R', true);

			$conn->row_free();


	}



	$pdf->Output();

	include('../inc/_db_close.php');
?>
<script>self.focus();</script>
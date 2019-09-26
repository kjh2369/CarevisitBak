<?
	/*********************************************************

		PDF ��� �ڵ常 �ۼ�

	*********************************************************/

	//print_r($var);

	$code  = $var['code'];
	$jumin = $var['jumin'];
	$seq   = $var['seq'];
	$date = str_replace('-','',$var['date']);
	
	#�̿�Ⱓ ��������
	$sql = 'select min(t01_sugup_date) as min_dt
		    ,      max(t01_sugup_date) as max_dt
			  from t01iljung
			 where t01_ccode               = \''.$code.'\'
			   and t01_mkind               = \'0\'
			   and t01_jumin               = \''.$jumin.'\'
			   and t01_del_yn              = \'N\'
			   and left(t01_sugup_date, 6) = \''.$date.'\'';

	$use_dt = $conn->get_array($sql);
	
	#���������� ��������
	$sql = "select m03_name as nm
			,	   app_no as injungNo
			,	   case lvl.svc_cd when '0' then case lvl.level when '9' then '�Ϲ�' else concat(lvl.level,'���') end
				   when '4' then concat(dis.svc_lvl,'���') else '' end as lvl
			  from m03sugupja
		 left join (
					select jumin
						 , svc_cd
						 , level
						 , from_dt
						 , to_dt
						 , app_no
					  from client_his_lvl
					 where org_no = '$code'
					   and date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
					   and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d')
					) as lvl
				 on m03_jumin = lvl.jumin
		 left join (
					select jumin
						 , svc_lvl
						 , from_dt
						 , to_dt
					  from client_his_dis
					 where org_no = '$code'
					   and date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
					   and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d')
					) as dis
				on m03_jumin = dis.jumin
			 where m03_ccode = '".$code."'
			   and m03_jumin = '".$jumin."'
			 group by m03_jumin";
	
	$su = $conn->get_array($sql);
	
	/*
	$sql = "select m03_name as nm
			,	   m03_injung_no as injungNo
			,	   m03_ylvl
			,	   m81_name as lvl
			  from m03sugupja
		inner join m81gubun
			    on m81_gbn   = 'LVL'
			   and m81_code  = m03_ylvl
			 where m03_jumin = '$jumin'";
	$su = $conn->get_array($sql);
	*/

	#�Ұ�, �հ谪 ��������
	$sql = 'select bill_jumin
			,	   bill_seq
			,	   bill_dt
			,      bill_svc_nm
			,	   bill_suga_dt
			,	   bill_care_amt
			,	   bill_bath_amt
			,	   bill_nurs_amt
			,      bill_bonin_amt
			,	   bill_total_amt
		      from svc_use_bill
			 where org_no      = \''.$code.'\'
			   and bill_jumin  = \''.$jumin.'\'
			   and bill_seq = \''.$seq.'\'';
	$use_bill = $conn->get_array($sql);

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetLineWidth(0.6);
	$pdf->Rect($pdf->left, $pdf->top, $pdf->width, $pdf->height - $pdf->top);
	$pdf->SetLineWidth(0.2);

	$pdf->SetFont($pdf->font_kor_name, 'B', 18);
	$pdf->Cell($pdf->width, 19, '�簡���� ������ȹ��',1,1,'C');

	$pdf->SetFont($pdf->font_kor_name,'',11);

	$k_cd  = $conn->center_code($code, '0');
	$k_nm  = $conn->center_name($code);
	$min_dt = $myF->dateStyle($use_dt['min_dt'],'.');	//�̿��������
	$max_dt = $myF->dateStyle($use_dt['max_dt'],'.');	//�̿���������

	$pdf->SetX($pdf->left);
	$pdf->Cell(20, 10, '�������',1,0,'C');
	$pdf->Cell(20, 10, '�����ȣ',1,0,'C');
	$pdf->Cell(60, 10, $k_cd,1,0,'C');
	$pdf->Cell(20, 10, '�����',1,0,'C');
	$pdf->Cell(62, 10, $k_nm,1,1,'C');
	
	$pdf->SetX($pdf->left);
	$pdf->Cell(20, 20, '',1,0,'C');	//����������
	$pdf->Cell(20, 10, '�����ڸ�',1,0,'C');
	$pdf->Cell(60, 10, $su['nm'],1,0,'C');
	$pdf->Cell(20, 10, '�ֹι�ȣ',1,0,'C');
	$pdf->Cell(62, 10, $myF->issStyle($jumin),1,1,'C');

	$arr[sizeof($arr)] = array('x'=>$pdf->left, 'y'=>$pdf->GetY() - $pdf->GetStringWidth('�� '), 'type'=>"multi_text", 'width'=>20, 'height'=>5, 'font_size'=>$pdf->font_size, 'align'=>'C', 'border'=>0, 'text'=>"������\n����");

	$pdf->SetX($pdf->left);
	$pdf->Cell(20, 0, '',0,0,'C');
	$pdf->Cell(20, 10, '���',1,0,'C');
	$pdf->Cell(60, 10, $su['lvl'],1,0,'C');
	$pdf->Cell(20, 10, '������ȣ',1,0,'C');
	$pdf->Cell(62, 10, $su['injungNo'],1,1,'C');

	$pdf->SetFont($pdf->font_kor_name,'',13);

	$pdf->SetX($pdf->left);
	$pdf->Cell(182, 8, '�޿��̿� ��û����',1,1,'C');

	$pdf->SetFont($pdf->font_kor_name,'',11);

	$pdf->SetX($pdf->left);
	$pdf->Cell(30, 8, '�޿�����',1,0,'C');
	$pdf->Cell(50, 8, $use_bill['bill_svc_nm'],1,0,'C');
	$pdf->Cell(34, 8, '�̿�Ⱓ',1,0,'C');
	$pdf->Cell(68, 8, $min_dt.'~'.$max_dt,1,1,'C');

	$pdf->SetX(14);
	$pdf->Cell(30, 8, '��������',1,0,'C');
	$pdf->Cell(50, 8, '���񽺳���',1,0,'C');
	$pdf->Cell(25, 8, '����',1,0,'C');
	$pdf->Cell(25, 8, 'Ƚ��',1,0,'C');
	$pdf->Cell(26, 8, '�ݾ�',1,0,'C');
	$pdf->Cell(26, 8, '���κδ��',1,1,'C');

	for($k=0; $k<3; $k++){

		if($k==0){
			$svc_kind = '�湮���';
			$svc      = 200;
			$bill_amt = $use_bill['bill_care_amt'] != 0 ? number_format($use_bill['bill_care_amt']) : '';	//�湮��� �Ұ�
		}else if($k==1){
			$svc_kind = '�湮���';
			$svc      = 500;
			$bill_amt = $use_bill['bill_bath_amt'] != 0 ? number_format($use_bill['bill_bath_amt']) : '';	//�湮��� �Ұ�
		}else {
			$svc_kind = '�湮��ȣ';
			$svc      = 800;
			$bill_amt = $use_bill['bill_nurs_amt'] != 0 ? number_format($use_bill['bill_nurs_amt']) : '';	//�湮��ȣ �Ұ�
		}

		$bill_tot_amt = $use_bill['bill_total_amt'] != '' ? number_format($use_bill['bill_total_amt']) : '';		//���հ�


		$sql = "select org_no
			,	   bill_jumin
			,	   bill_seq
			,	   item_seq
			,	   item_svc_cd
			,	   item_suga_cd
			,	   item_suga_nm
			,	   item_suga
			,      item_suga_my
			,	   item_suga_cnt
			,	   item_suga_amt
			  from svc_use_bill_item
			 where org_no      = '".$code."'
			   and bill_jumin  = '".$jumin."'
			   and item_svc_cd = '".$svc."'
			   and bill_seq    = '".$seq."'";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row			   = $conn->select_row($i);
			$suga_nm[$svc][$i]       = $row['item_suga_nm'] != '' ? $row['item_suga_nm'] : '';		//���񽺳���
			$item_suga[$svc][$i]     = $row['item_suga'] != '' ? number_format($row['item_suga']) : '';				//����
			$item_suga_cnt[$svc][$i] = $row['item_suga_cnt'] != '' ? $row['item_suga_cnt'] : '';		//Ƚ��(��)
			$item_suga_amt[$svc][$i] = $row['item_suga_amt'] != '' ? number_format($row['item_suga_amt']) : '';		//�ݾ�
			$item_suga_my[$svc][$i]  = $row['item_suga_my'] != '' ? number_format($row['item_suga_my']) : '';

			if (!isset($bill_tot_my[$svc])) $bill_tot_my[$svc] = 0;
			$bill_tot_my[$svc]  += intval($row['item_suga_my']);
			$bill_tot_my['tot'] += intval($row['item_suga_my']);
		}

		for($j=0; $j<6; $j++){
			$pdf->SetX($pdf->left);
			if($j==0){
				$pdf->Cell(30, 48, $svc_kind ,1,0,'C');
			}else {
				$pdf->Cell(30, 0, '' ,0,0,'C');
			}
			$pdf->Cell(50, 8, $suga_nm[$svc][$j],1,0,'L');
			$pdf->Cell(25, 8, $item_suga[$svc][$j],1,0,'R');
			$pdf->Cell(25, 8, $item_suga_cnt[$svc][$j],1,0,'C');
			$pdf->Cell(26, 8, $item_suga_amt[$svc][$j],1,0,'R');
			$pdf->Cell(26, 8, $item_suga_my[$svc][$j],1,1,'R');

		}

		$pdf->SetFont($pdf->font_kor_name,'B',12);

		$pdf->SetX($pdf->left);
		$pdf->Cell(80, 8, '�� ��',1,0,'R');
		$pdf->Cell(76, 8, $bill_amt,1,0,'R');
		$pdf->Cell(26, 8, !empty($bill_tot_my[$svc]) ? number_format($bill_tot_my[$svc]) : '',1,1,'R');

		$pdf->SetFont($pdf->font_kor_name,'',11);
	}

	$pdf->SetFont($pdf->font_kor_name,'B',12);

	$pdf->SetX($pdf->left);
	$pdf->Cell(80, 8, '�� ��',1,0,'R');
	$pdf->Cell(76, 8, $bill_tot_amt,1,0,'R');
	$pdf->Cell(26, 8, !empty($bill_tot_my['tot']) ? number_format($bill_tot_my['tot']) : '',1,1,'R');


	setArrayText($pdf, $arr);

	unset($arr);
?>
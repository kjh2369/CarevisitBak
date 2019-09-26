<?

	/**************************************************

		data

	**************************************************/
	$sql = 'select *
			  from counsel_client_'.strtolower($type).'
			 where org_no                     = \''.$code.'\'
			   and '.strtolower($type).'_yymm = \''.$yymm.'\'
			   and '.strtolower($type).'_seq  = \''.$seq.'\'';

	$data = $conn->get_array($sql);


	$sql = 'select m00_mcode as cd
			,      m00_store_nm as nm
			  from m00center
			 where m00_mcode  = \''.$code.'\'
			   and m00_del_yn = \'N\'
			 order by m00_mkind
			 limit 1';

	$tmp = $conn->get_array($sql);

	$pdf->k_cd = $tmp['cd'];
	$pdf->k_nm = $tmp['nm'];

	unset($tmp);

	if ($pdf->root == 'MEMBER'){
		$sql = 'SELECT m02_yname AS nm
				,      m02_ytel AS mobile
				,      m02_ytel2 AS phone
				,      CONCAT(SUBSTRING(m02_ypostno,1,3),\'-\',SUBSTRING(m02_ypostno,4)) AS postno
				,      m02_yjuso1 AS addr
				,      m02_yjuso2 AS addr_dtl
				  FROM m02yoyangsa
				 WHERE m02_ccode  = \''.$code.'\'
				   AND m02_yjumin = \''.$data[strtolower($type).'_c_cd'].'\'
				 ORDER BY m02_mkind
				 LIMIT 1';

		
		$tmp = $conn->get_array($sql);
	}else{
		$sql = 'select m03_name as nm
				,      m03_tel as phone
				,      m03_hp as mobile
				,      concat(substring(m03_post_no, 1, 3), \'-\', substring(m03_post_no, 4)) as postno
				,      m03_juso1 as addr
				,      m03_juso2 as addr_dtl
				,      m03_yboho_name as parent_nm
				,      m03_yboho_gwange as parent_rel
				,      m03_yboho_phone as parent_phone
				  from m03sugupja
				 where m03_ccode  = \''.$code.'\'
				   and m03_jumin  = \''.$data[strtolower($type).'_c_cd'].'\'
				   and m03_del_yn = \'N\'
				   and m03_mkind  =  '.$conn->_client_kind();

		$tmp = $conn->get_array($sql);

	}

	$pdf->c_cd     = $myF->issStyle($data[strtolower($type).'_c_cd']);
	$pdf->c_nm     = $tmp['nm'];
	$pdf->c_phone  = $myF->phoneStyle($tmp['phone'],'.');
	$pdf->c_mobile = $myF->phoneStyle($tmp['mobile'],'.');
	$pdf->c_postno			= $tmp['postno'];
	$pdf->c_addr			= $tmp['addr'];
	$pdf->c_addr_dtl		= $tmp['addr_dtl'];
	$pdf->c_parent_nm		= $tmp['parent_nm'];
	$pdf->c_parent_rel		= $tmp['parent_rel'];
	$pdf->c_parent_phone	= $myF->phoneStyle($tmp['parent_phone']);


	/**************************************************

		PDF ���

	**************************************************/
	$pdf->SetAutoPageBreak(false);
	$pdf->MY_ADDPAGE();




	/**************************************************
		�⺻ ��Ʈ ����
	**************************************************/
	$pdf->SetFont($pdf->font_name_kor, '', 10);




	/**************************************************
		������
	**************************************************/
	/*
	$pos[sizeof($pos)] = array('x'=>$pdf->left, 'y'=>$pdf->GetY() + $pdf->row_height * 3 / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.15, 'height'=>1, 'align'=>'C', 'text'=>'�ּ�');
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.15, 'y'=>$pdf->GetY() + $pdf->row_height * 0.3, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.25, 'height'=>5, 'align'=>'L', 'text'=>$tmp['postno']."\n".$tmp['addr']."\n".$tmp['addr_dtl']);
	$pos[sizeof($pos)] = array('x'=>$pdf->left + $pdf->width * 0.40, 'y'=>$pdf->GetY() + $pdf->row_height * 3 / 2, 'font_size'=>10, 'type'=>'multi_text', 'width'=>$pdf->width * 0.15, 'height'=>1, 'align'=>'C', 'text'=>'��ȣ��');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 3, '', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.25, $pdf->row_height * 3, '', 1, 0, 'C');
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height * 3, '', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, '����', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.30, $pdf->row_height, $tmp['parent_nm'], 1, 1, 'L');

	$pdf->SetX($pdf->left + $pdf->width * 0.55);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, '����', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.30, $pdf->row_height, $tmp['parent_rel'], 1, 1, 'L');

	$pdf->SetX($pdf->left + $pdf->width * 0.55);
	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, '����ó', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.30, $pdf->row_height, $myF->phoneStyle($tmp['parent_parent_phone']), 1, 1, 'L');

	$pdf->SetLineWidth(0.6);
	$pdf->Rect($pdf->left, $pdf->GetY() - $pdf->row_height * 6, $pdf->width, $pdf->row_height * 6);
	$pdf->SetLineWidth(0.2);
	*/
	unset($tmp);




	/**************************************************
		����̷�
	**************************************************/
	$pdf->SetX($pdf->left);

	switch($type){
		case 'VISIT':
			$pdf->Cell($pdf->width, $pdf->row_height, '���湮 ����̷�', 1, 1, 'C', 1);
			break;

		case 'PHONE':
			$pdf->Cell($pdf->width, $pdf->row_height, '��ȭ ����̷�', 1, 1, 'C', 1);
			break;

		case 'STRESS':
			$pdf->Cell($pdf->width, $pdf->row_height, '�Ҹ� �� ����ó�� ����̷�', 1, 1, 'C', 1);
			break;

		case 'CASE':
			$pdf->Cell($pdf->width, $pdf->row_height, '��ʰ��� ȸ�� �̷�', 1, 1, 'C', 1);
			break;
	}

	switch($type){
		case 'STRESS':
			$caption = '����';
			break;

		case 'CASE':
			$caption = 'ȸ��';
			break;

		default:
			$caption = '���';
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.07, $pdf->row_height, 'No', 1, 0, 'C', 1);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, $caption.'����', 1, 0, 'C', 1);

	switch($type){
		case 'CASE':
			$pdf->Cell($pdf->width * 0.15, $pdf->row_height, '�ְ���', 1, 0, 'C', 1);
			break;

		default:
			$pdf->Cell($pdf->width * 0.15, $pdf->row_height, $caption.'��', 1, 0, 'C', 1);
	}

	switch($type){
		case 'VISIT':
			$pdf->Cell($pdf->width * 0.51, $pdf->row_height, '��㳻��', 1, 0, 'C', 1);
			break;

		case 'PHONE':
			$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '�������', 1, 0, 'C', 1);
			$pdf->Cell($pdf->width * 0.39, $pdf->row_height, '��㳻��', 1, 0, 'C', 1);
			break;

		case 'STRESS':
			$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '�������', 1, 0, 'C', 1);
			$pdf->Cell($pdf->width * 0.39, $pdf->row_height, '�Ҹ� �� ���泻�� / ó�����', 1, 0, 'C', 1);
			break;

		case 'CASE':
			$pdf->Cell($pdf->width * 0.20, $pdf->row_height, '���񽺸�', 1, 0, 'C', 1);
			$pdf->Cell($pdf->width * 0.31, $pdf->row_height, '�ֿ乮����', 1, 0, 'C', 1);
			break;
	}

	$pdf->Cell($pdf->width * 0.15, $pdf->row_height, '���', 1, 1, 'C', 1);

	$sql = 'select '.strtolower($type).'_dt as dt
			,      '.strtolower($type).'_m_nm as nm';

	switch($type){
		case 'VISIT':
			$sql .= ', visit_other AS cont';
			break;
			
		case 'PHONE':
			$sql .= ', case phone_kind when \'1\' then \'��\'
									   when \'2\' then \'����\' else \'������\' end as kind
					 , phone_contents as cont';
			break;

		case 'STRESS':
			$sql .= ', case stress_rct_path when \'1\' then \'����\'
											when \'2\' then \'�湮\'
											when \'3\' then \'��ȭ\'
											when \'4\' then \'Ȩ������\'
											when \'5\' then \'����\' else \'��Ÿ\' end as kind
					 , concat(case stress_cont_kind when \'1\' then \'������ģ��\'
											        when \'2\' then \'���񽺳���\'
											        when \'3\' then \'���񽺺��\' else \'��Ÿ\' end, \' / \'
					 ,        case stress_proc_kind when \'1\' then \'��û �� ȿ���� ��ȭ\'
											        when \'2\' then \'�̿��ڹ湮\'
											        when \'3\' then \'����\' else \'��Ÿ\' end) as cont';
			break;

		case 'CASE':
			$sql .= ', case_run_nm as nm
					 , case_svc_kind as kind
					 , case_main_quest as quest';
			break;
	}

	$sql .= ' from counsel_client_'.strtolower($type).'
			 where org_no = \''.$code.'\'
			   and '.strtolower($type).'_c_cd = \''.$data[strtolower($type).'_c_cd'].'\'
			   and '.strtolower($type).'_dt   = \''.$data[strtolower($type).'_dt'].'\'
			   and del_flag = \'N\'
			 order by '.strtolower($type).'_dt desc
			 limit 3';
	
	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		
		
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.07, $pdf->row_height, number_format($i+1), 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.12, $pdf->row_height, $myF->dateStyle($row['dt'],'.'), 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, $row['nm'], 1, 0, 'L');

		switch($type){
			case 'VISIT':
				$pdf->Cell($pdf->width * 0.51, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($row['cont']),$pdf->width * 0.51), 1, 0, 'L');
				break;

			case 'CASE':
				$k_list = explode('/', $row['kind']);

				if (is_array($k_list)){
					$svc_list = '';
					$svc_str  = '';
					$svc_cnt  = sizeof($k_list);

					for($j=0; $j<$svc_cnt; $j++){
						if (!empty($svc_list) && $j < $svc_cnt - 1)
							$svc_list .= ', ';

						$svc_list .= $conn->kind_name_svc($k_list[$j]);
					}

					$str_svc = $pdf->_splitTextWidth($svc_list, $pdf->width * 0.20);
				}

				unset($k_list);

				$str_quest = $pdf->_splitTextWidth($myF->utf($row['quest']), $pdf->width * 0.31);
				$pdf->Cell($pdf->width * 0.20, $pdf->row_height, $str_svc, 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.31, $pdf->row_height, $str_quest, 1, 0, 'L');
				break;

			default:
				$pdf->Cell($pdf->width * 0.12, $pdf->row_height, $row['kind'], 1, 0, 'C');
				//$pdf->Cell($pdf->width * 0.39, $pdf->row_height, $row['cont'], 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.39, $pdf->row_height, $pdf->_splitTextWidth($myF->utf($row['cont']),$pdf->width * 0.39), 1, 0, 'L');
		}

		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, '', 1, 1, 'L');
	}

	$conn->row_free();

	for($i=$row_count; $i<3; $i++){
		$pdf->SetX($pdf->left);
		$pdf->Cell($pdf->width * 0.07, $pdf->row_height, '', 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '', 1, 0, 'C');
		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, '', 1, 0, 'L');

		switch($type){
			case 'VISIT':
				$pdf->Cell($pdf->width * 0.51, $pdf->row_height, '', 1, 0, 'L');
				break;

			case 'CASE':
				$pdf->Cell($pdf->width * 0.20, $pdf->row_height, '', 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.31, $pdf->row_height, '', 1, 0, 'L');
				break;

			default:
				$pdf->Cell($pdf->width * 0.12, $pdf->row_height, '', 1, 0, 'L');
				$pdf->Cell($pdf->width * 0.39, $pdf->row_height, '', 1, 0, 'L');
		}

		$pdf->Cell($pdf->width * 0.15, $pdf->row_height, '', 1, 1, 'L');
	}

	$pdf->SetLineWidth(0.6);
	$pdf->Rect($pdf->left, $pdf->GetY() - $pdf->row_height * 5, $pdf->width, $pdf->row_height * 5);
	$pdf->SetLineWidth(0.2);
?>
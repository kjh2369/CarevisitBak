<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	���������-ADL,IADL
	 *********************************************************/

	$conn->fetch_type = 'assoc';
	$pdf->SetFont($pdf->font_name_kor,'B',13);

	$orgNo = $_SESSION['userCenterCode'];


	//ADL
	$sql = 'SELECT	*
			FROM	hce_inspection_adl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$ADL = $conn->get_array($sql);

	//IADL
	$sql = 'SELECT	*
			FROM	hce_inspection_iadl
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$IADL = $conn->get_array($sql);


	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.25;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.15;
	$col[] = $pdf->width * 0.15;

	$pdf->SetXY($pdf->left,$pdf->top);
	$pdf->Cell($pdf->width,$pdf->row_height * 1.5,"�� �ϻ��Ȱ ��������(ADL)",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"�ϻ��Ȱ ����",1,0,'C',1);
	$pdf->Cell($col[2],$pdf->row_height,"�ڸ�����",1,0,'C',1);
	$pdf->Cell($col[3],$pdf->row_height,"�ణ����",1,0,'C',1);
	$pdf->Cell($col[4],$pdf->row_height,"�����ָ� ����",1,0,'C',1);
	$pdf->Cell($col[5],$pdf->row_height,"���������ʿ�",1,1,'C',1);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 4,"�⺻����",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"�� ���� �ݱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['base_door']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"ȥ�ڼ� �Ź� ����",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['base_shoes']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"�Ź��� ���忡 �ֱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['base_shoes_put']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"���ڸ� ���� �ְ� ����",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['base_chair']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 5,"�ź�ó��",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"������ �� ����ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_bath']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"�����ϰ� ��ġ���ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_wash']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"�⺻���� ������ �ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_groom']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"ȥ�ڼ� �� �Ա�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_in_dress']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"ȥ�ڼ� �� ����",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['per_out_dress']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 4,"�뺯ó��",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"ȥ�ڼ� ���⿡ �ɱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['wc_bedpan']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"�뺯 �� ��ó�� �ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['wc_after']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"�뺯 �����ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['wc_feces']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"�Һ� �����ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['wc_urine']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 5,"�Ļ�",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"������ �Ա�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_spoon']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"������ ����ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_stick']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"��ũ ����ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_poke']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"������ �� ���ñ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_cup']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"������ �ִ� �� ����ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['eat_grip_cup']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 3,"����",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"ȥ�ڼ� 100m�̻� �ȱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['walk_100m']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"������� ��� ����������",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['walk_hand']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"�������� ��� ����������",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['walk_stair']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 5,"ħ��Ȱ��",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"�� ���¿��� �ɱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_sitdown']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"���� ���¿��� �Ͼ��",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_standup']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"ȥ�ڼ� ���� �Ͼ��",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_lie']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"ȥ�ڼ� ��ô�̱�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_turn']);

	$pdf->SetX($pdf->left+$col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"��� �� ħ���� �����ϱ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$ADL['bed_tidy']);



	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$pdf->row_height * 1.5,"�� ������ �ϻ��Ȱ ����(IADL)",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"�׸�",1,0,'C',1);
	$pdf->Cell($col[2],$pdf->row_height,"�ڸ�����",1,0,'C',1);
	$pdf->Cell($col[3],$pdf->row_height,"�ణ����",1,0,'C',1);
	$pdf->Cell($col[4],$pdf->row_height,"�����ָ� ����",1,0,'C',1);
	$pdf->Cell($col[5],$pdf->row_height,"���������ʿ�",1,1,'C',1);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"��ȭ���",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['phone']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"���� �Ǵ� ����",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['outdoor']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"���Ǳ���",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['buying']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"�Ļ��غ�",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['eating']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"������(û�ҳ� ��������)",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['homework']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"����",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['cleaning']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"���ð��� ��Ȯ�� �뷮�� �� ����",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['medicine']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"��������",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['money']);



	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0]+$col[1],$pdf->row_height,"�� ������(�ٴ����̳� ����)",1,0,'L',1);
	lfDrawCell34($pdf,$col,$IADL['repair']);

	Unset($col);



	function lfDrawCell34($pdf,$col,$val){
		$pdf->SetFont($pdf->font_name_kor,'B',9);
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell($col[2],$pdf->row_height,($val == '1' ? '��' : ''),1,0,'C');
		$pdf->Cell($col[3],$pdf->row_height,($val == '2' ? '��' : ''),1,0,'C');
		$pdf->Cell($col[4],$pdf->row_height,($val == '3' ? '��' : ''),1,0,'C');
		$pdf->Cell($col[5],$pdf->row_height,($val == '4' ? '��' : ''),1,1,'C');
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont($pdf->font_name_kor,'',9);
	}
?>
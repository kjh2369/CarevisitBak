<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	���������-����������, ��ȸ������
	 *********************************************************/

	$conn->fetch_type = 'assoc';

	$orgNo = $_SESSION['userCenterCode'];


	//����������
	$sql = 'SELECT	*
			FROM	hce_inspection_feel
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$row = $conn->get_array($sql);

	$feel1Yn	= $row['feel1_yn'];
	$feel2Yn	= $row['feel2_yn'];
	$feel2Rsn	= StripSlashes($row['feel2_rsn']);
	$feel3Yn	= $row['feel3_yn'];
	$feel4Yn	= $row['feel4_yn'];
	$feel4Rsn	= StripSlashes($row['feel4_rsn']);
	$feel5Yn	= $row['feel5_yn'];
	$feel6Yn	= $row['feel6_yn'];
	$feel6Eft	= StripSlashes($row['feel6_eft']);
	$feel7Yn	= $row['feel7_yn'];
	$feel7Cnt	= StripSlashes($row['feel7_cnt']);
	$feel7Whn	= StripSlashes($row['feel7_whn']);
	$feel7Rsn	= StripSlashes($row['feel7_rsn']);

	Unset($row);


	//��ȭ������
	$sql = 'SELECT	*
			FROM	hce_inspection_social
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$row = $conn->get_array($sql);

	$social1	= $row['social1'];
	$social2	= $row['social2'];
	$social2Rsn	= StripSlashes($row['social2_rsn']);
	$social3	= $row['social3'];
	$social4	= $row['social4'];
	$social4Rsn	= StripSlashes($row['social4_rsn']);
	$social5	= $row['social5'];
	$social6	= $row['social6'];
	$social6Rsn	= StripSlashes($row['social6_rsn']);
	$social7	= $row['social7'];
	$social7Nm	= StripSlashes($row['social7_nm']);
	$social7Tel	= $row['social7_tel'];
	$social8	= $row['social8'];
	$social8Str	= StripSlashes($row['social8_other']);
	$social9	= $row['social9'];
	$social9Str	= StripSlashes($row['social9_other']);

	Unset($row);



	$col[] = $pdf->width * 0.86;
	$col[] = $pdf->width * 0.07;
	$col[] = $pdf->width * 0.07;

	$rowHeight = $pdf->row_height * 0.9;


	$pdf->SetXY($pdf->left,$pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$rowHeight * 1.5,"�� ������ ����",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"����",1,0,'C',1);
	$pdf->Cell($col[1],$rowHeight,"��",1,0,'C',1);
	$pdf->Cell($col[2],$rowHeight,"�ƴϿ�",1,1,'C',1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"������ ���� ���� �� �� �ؾ�����ʴϱ�?[����]",1);
	lfDrawCell56($pdf,$col,$rowHeight,$feel1Yn);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"�������� �ʰų� ȭ���� ���� ���� �ֽ��ϱ�?[��������](����� ������ �ۼ��Ͽ� �ֽʽÿ�.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$feel2Yn);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       �����, ���� : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$feel2Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"����ǰ ���� ���� �Ҿ�����ʴϱ�?",1);
	lfDrawCell56($pdf,$col,$rowHeight,$feel3Yn);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"������ ������ ���� ���� �����ʴϱ�?[��������](����� ������ �ۼ��Ͽ� �ֽʽÿ�.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$feel4Yn);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       �����, ����  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$feel4Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"���� ����� �� �ֹ��ʴϱ�?",1);
	lfDrawCell56($pdf,$col,$rowHeight,$feel5Yn);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"�۳� �� �� ���� Ȥ�� �ٷ��� ģ���� ģô�� ���� ���� �ֽ��ϱ�? �ִٸ�, � ������ ���ƽ��ϱ�?","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$feel6Yn);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       �����, ����  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$feel6Eft,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"���� ��ȯ���� �Կ��Ͻ� ���� �ֽ��ϱ�?(����� ������ �ۼ��Ͽ� �ֽʽÿ�.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$feel7Yn);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.11,$rowHeight,"       Ƚ��","BL",0,'R');
	$pdf->Cell($col[0] * 0.20,$rowHeight,$feel7Cnt,"B",0);
	$pdf->Cell($col[0] * 0.11,$rowHeight,"       ����","B",0,'R');
	$pdf->Cell($col[0] * 0.20,$rowHeight,$feel7Whn,"B",0);
	$pdf->Cell($col[0] * 0.11,$rowHeight,"       ����","B",0,'R');
	$pdf->Cell($col[0] * 0.27,$rowHeight,$feel7Rsn,"RB",1);




	//-------------------------------------------//


	$pdf->SetX($pdf->left);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$rowHeight * 1.5,"�� ��ȸ�� ����",1,1,'L',1);
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"����",1,0,'C',1);
	$pdf->Cell($col[1],$rowHeight,"��",1,0,'C',1);
	$pdf->Cell($col[2],$rowHeight,"�ƴϿ�",1,1,'C',1);


	/*
	$social1	= $row['social1'];
	$social2	= $row['social2'];
	$social2Rsn	= StripSlashes($row['social2_rsn']);
	$social3	= $row['social3'];
	$social4	= $row['social4'];
	$social4Rsn	= StripSlashes($row['social4_rsn']);
	$social5	= $row['social5'];
	$social6	= $row['social6'];
	$social6Rsn	= StripSlashes($row['social6_rsn']);
	$social7	= $row['social7'];
	$social7Nm	= StripSlashes($row['social7_nm']);
	$social7Tel	= $row['social7_tel'];
	$social8	= $row['social8'];
	$social8Str	= StripSlashes($row['social8_other']);
	$social9	= $row['social9'];
	$social9Str	= StripSlashes($row['social9_other']);
	 */


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"�ֱ� 1�� ���̿� ���ϴ� �Ⱑ �Ǵ� �а��� �ڳ��� ������ ����ϱ�?","LTR");
	$pdf->Cell($col[1],$rowHeight * 4,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 4,"","TRB",2,'C');


	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 3);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '1' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"����� �������� ������.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '2' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�����, ���� � ������ �����ų� �����Ѵ�.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '3' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"��2��~3�޿� �� �� �� ���� �����Ѵ�.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '4' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"��1�޿� �� �� �� ���� �����Ѵ�.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '5' ? ' ��' : '','LB');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"��1�� ��ȸ �̻� �����Ѵ�.","B",0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social1 == '6' ? ' ��' : '','B');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"���Ⱑ�� �ڳడ ����.","BR",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"�������� ���迡 ������ �ֽ��ϱ�?(����� ������ �ۼ��Ͽ� �ֽʽÿ�.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$social2);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       �����, ����  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$social2Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"���ϴ� ��ü�� �̿��� ��� ���� ģ�ϰ� ������ ��ʴϱ�?","LTR");
	$pdf->Cell($col[1],$rowHeight * 4,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 4,"","TRB",2,'C');

	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 3);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '1' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"����� �𸣰� ������.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '2' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"���λ��ϴ� �����̴�.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '3' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�鸻���ϴ� �����̴�.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '4' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�긻���� ���� ������ �ְ�޴´�.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social3 == '5' ? ' ��' : '','LB');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"������������ �ϰ� ����ó�� ������.","B",0);
	$pdf->Cell($col[0] * 0.5,$rowHeight,"","BR",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"�̿��� ��︮�µ� ������� �����ʴϱ�?(����� ������ �ۼ��Ͽ� �ֽʽÿ�.)","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$social4);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       �����, ����  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$social4Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"ģ������ �־ ���������� �����ʴϱ�?",1);
	lfDrawCell56($pdf,$col,$rowHeight,$social5);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"��ȸ, ������ȸȰ�� �� ���� ��ȸȰ���� �����Ͻʴϱ�? ","LTR");
	lfDrawCell56($pdf,$col,$rowHeight * 2,$social6);
	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight);
	$pdf->Cell($col[0] * 0.19,$rowHeight,"       �����, ����  : ","BL",0);
	$pdf->Cell($col[0] * 0.81,$rowHeight,$social6Rsn,"RB",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"������Ȱ�� �ϽŴٸ� ��� �Ͻʴϱ�?","LTR");
	$pdf->Cell($col[1],$rowHeight * 5,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 5,"","TRB",2,'C');

	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 4);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '1' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"������ ���� �ʴ´�.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '2' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�谡���� �Ѵ�.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '3' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"������ �ϴ� ���̴�(��1~2ȸ).",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '4' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"���׻� �����Ѵ�.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social7 == '5' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�������� ����.",0,0);
	$pdf->Cell($col[0] * 0.5,$rowHeight,"","R",1);

	$pdf->SetX($pdf->left);
	//$pdf->Cell($col[0],$rowHeight,"  ���������                             ��ȭ��ȣ","RBL",1);
	$pdf->Cell($col[0] * 0.20,$rowHeight,"  ���������","LB",0,'R');
	$pdf->Cell($col[0] * 0.30,$rowHeight,$social7Nm,"B",0,'R');
	$pdf->Cell($col[0] * 0.20,$rowHeight,"��ȭ��ȣ","B",0,'R');
	$pdf->Cell($col[0] * 0.30,$rowHeight,$social7Tel,"RB",1,'R');


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"�����ϴ� ���ϰŸ�(�����ð�)��?","LTR");
	$pdf->Cell($col[1],$rowHeight * 5,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 5,"","TRB",2,'C');

	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 4);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '1' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�������, ��ο����� �ð��� ������.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '2' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�������, ����, ��å�ο��� ������.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '3' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�������� TV�� ���ų� �׳� ������.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '4' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"��ģ���� ���̳� �� ������ ģ���� ������.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '5' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"����δ��п� ���ų� ���Ȱ���� �Ѵ�.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '6' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"����λ���̳� ��, �ξ��� �Ѵ�.","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '7' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"������Ȱ���̳� ����Ȱ���� �Ѵ�.",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social8 == '8' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"���Ÿ(".$social8Str.")","BR",1);


	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$rowHeight,"���ϴ� ������� ����ų� ���� �� �����κ��� ������ ���� ���� �޽��ϱ�?","LTR");
	$pdf->Cell($col[1],$rowHeight * 6,"","TBL",0,'C');
	$pdf->Cell($col[2],$rowHeight * 6,"","TRB",2,'C');

	$pdf->SetXY($pdf->left,$pdf->GetY() - $rowHeight * 5);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '1' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"������",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '2' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"���ڳ�","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '3' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�������ڸ�,�θ�",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '4' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"���̿�","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '5' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"���ȸ������",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '6' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�쵿�繫�� �������","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '7' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"��������ü",0,0);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '8' ? ' ��' : '');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"�����","R",1);

	$pdf->SetX($pdf->left);
	lfDrawEx($pdf,$col[0] * 0.5 * 0.05,$rowHeight,$social9 == '9' ? ' ��' : '','L');
	$pdf->Cell($col[0] * 0.5 * 0.95,$rowHeight,"���Ÿ(".$social9Str.")","B",0);
	$pdf->Cell($col[0] * 0.5,$rowHeight,"","BR",1);

	Unset($col);


	function lfDrawCell56($pdf,$col,$rowHeight,$val){
		$pdf->SetFont($pdf->font_name_kor,'B',9);
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell($col[1],$rowHeight,($val == 'Y' ? '��' : ''),1,0,'C');
		$pdf->Cell($col[2],$rowHeight,($val == 'N' ? '��' : ''),1,1,'C');
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont($pdf->font_name_kor,'',9);
	}

	function lfDrawEx($pdf,$width,$height,$val,$border='0'){
		$pdf->SetFont($pdf->font_name_kor,'B',9);
		$pdf->SetTextColor(0,0,255);
		$pdf->Cell($width,$height,$val,$border,0,'L');
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont($pdf->font_name_kor,'',9);
	}
?>
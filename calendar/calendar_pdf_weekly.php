<?
	include('../inc/_db_open.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');

	$code   = $_GET['code'];		//����ڵ�
	$year   = $_GET['year'];		//��
	$month  = $_GET['month'];		//��
	$week   = $_GET['week'];		//���ϱ���
	$fromDt = $_GET['fromDt'];		//�ְ� ������
	$toDt   = $_GET['toDt'];		//�ְ� ������
	$mode   = $_GET['mode'];		//����

	require("../pdf/pdf_calendar_table.php");


	$pdf = new MYPDF(strtoupper('l'));

	/**************************************************

		�⺻����

	**************************************************/
	#�ɸ��ͺ���
	$conn->set_name('euckr');
	#�����
	$center_nm   = $conn->center_name($code);

	//$pdf->cpIcon   = '../ci/ci_'.$gDomainNM.'.jpg';
	//$pdf->cpName   = null;
	$pdf->ctIcon   = $conn->center_icon($code);			//���������
	$pdf->ctName   = $conn->center_name($code);			//�����

	$pdf->AddUHCFont($pdf->font_name_kor, $pdf->font_name_eng);

	$pdf->font_name_kor = '����';
	$pdf->font_name_eng = 'Gulim';
	$pdf->AddUHCFont('����','Gulim');

	$pdf->Open();

	$pdf->SetFillColor(220,220,220);

	$pdf->mode			= $mode;
	$pdf->year			= $year;
	$pdf->month			= $month;
	$pdf->week			= $week;
	$pdf->fromDt		= $fromDt;
	$pdf->toDt			= $toDt;
	$pdf->center_nm     = $center_nm;

	$pdf->AddPage(strtoupper('l'), 'A4');
	$pdf->SetFont('����','',11);

	$conn->set_name('euckr');


	// ���� ���� ����
	//$pdf->Cell($col['w'][$j], $height, $col['t'][$j], 1, $j < 6 ? 0 : 1, 'C', true);
	$calTime	= mktime(0, 0, 1, $month-1, 1, $year);
	$lastDay	= date('t', $calTime);			//���ϼ� ���ϱ�
	//$weekly = array("��","��","ȭ","��","��","��","��");
	//$color = array("red","black","black","black","black","black","blue");



	$pdf->Output();

	include('../inc/_db_close.php');


	// �޷��� ���Ϻ� ������ �׸���.
	function drawLine($pdf, $col, $top){
		$pdf->SetLineWidth(0.2);

		$left = $pdf->left;

		for($i=0; $i<7; $i++){
			$left += $col['w'][$i];
			$pdf->Line($left, $pdf->top+2, $left, $top);
		}
	}

	// �޷��� ��ü �׵θ��� �׸���.
	function drawBorder($pdf, $height){
		$pdf->SetLineWidth(0.6);
		$pdf->Rect($pdf->left, $pdf->top+2, $pdf->width, $height);
		$pdf->SetLineWidth(0.2);
	}
?>

<script>self.focus();</script>

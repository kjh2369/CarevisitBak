<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	���������-���������ɷ���(MMES-DS)
	 *********************************************************/

	$conn->fetch_type = 'assoc';

	$orgNo = $_SESSION['userCenterCode'];


	//������� �� �ʱ���� ����
	$sql = 'SELECT	mst.m03_name AS name
			,		mst.m03_jumin AS jumin
			,		rcpt.phone
			,		rcpt.mobile
			,		rcpt.addr
			,		rcpt.addr_dtl
			,		rcpt.marry_gbn
			,		rcpt.cohabit_gbn
			FROM	hce_receipt AS rcpt
			INNER	JOIN	m03sugupja AS mst
					ON		mst.m03_ccode	= rcpt.org_no
					AND		mst.m03_mkind	= \'6\'
					AND		mst.m03_key		= rcpt.IPIN
			WHERE	rcpt.org_no		= \''.$orgNo.'\'
			AND		rcpt.org_type	= \''.$hce->SR.'\'
			AND		rcpt.IPIN		= \''.$hce->IPIN.'\'
			AND		rcpt.rcpt_seq	= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$jumin = $row['jumin'];

	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$jumin.'\'';

	$jumin = $conn->get_data($sql);
	$tmpJumin = $jumin;
	$jumin = SubStr($jumin.'0000000',0,13);

	$year	= $myF->issToYear($row['jumin']);	//����
	$name	= $row['name'];	//����
	$gender = $myF->euckr($myF->issToGender($jumin));	//����
	$age	= $tmpJumin != '' ? $myF->issToAge($jumin) : '       ';//����
	$jumin	= $tmpJumin != '' ? $myF->issStyle($jumin) : '';	//�ֹι�ȣ
	$addr	= $row['addr'].' '.$row['addr_dtl'];	//�ּ�
	$phone	= $myF->phoneStyle($row['phone'],'.');	//����ó
	$mobile	= $myF->phoneStyle($row['mobile'],'.');	//�ڵ���
	$ddi	= $tmpJumin != '' ? $myF->euckr($myF->getGapJaDdi('DDI',$year)) : '      ';

	$birthDay	= $myF->dateStyle($myF->issToBirthday($jumin),'KOR');

	$marry	= $row['marry_gbn'];	//��ȥ
	$cohabit= $row['cohabit_gbn'];	//����

	Unset($row);


	$sql = 'SELECT	per_family_cnt
			,		per_cost_gbn
			,		per_medical_gbn
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$row = $conn->get_array($sql);

	$costGbn	= $row['per_cost_gbn'];	//�������豸��
	$familyCnt	= $row['per_family_cnt'];	//������
	$medicalGbn	= $row['per_medical_gbn'];

	Unset($row);


	$sql = 'SELECT	iver_nm
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';
	$iverNm = $conn->get_data($sql);


	$sql = 'SELECT	*
			FROM	hce_inspection_mmseds
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$mmse = $conn->get_array($sql);


	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetFont($pdf->font_name_kor,'B',18);
	$pdf->Cell($pdf->width,$pdf->row_height*2,"�������������(MMSE-DS)�˻�",0,1,'C');
	$pdf->SetFont($pdf->font_name_kor,'B',9);

	$pdf->SetXY($pdf->left, $pdf->GetY() + 2);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$pdf->row_height,"�� �α����� ����",0,1,'L');
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"��           ��",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.13,$pdf->row_height,$name,1,0,'L');
	$pdf->Cell($pdf->width * 0.08,$pdf->row_height,"����ó",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.23,$pdf->row_height,"��) ".$phone,1,0,'L');
	$pdf->Cell($pdf->width * 0.23,$pdf->row_height,"H.P) ".$mobile,1,0,'L');
	$pdf->Cell($pdf->width * 0.08,$pdf->row_height,"�����",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.13,$pdf->row_height,$iverNm,1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"��           ��",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.21,$pdf->row_height,$gender,1,0,'L');
	$pdf->Cell($pdf->width * 0.13,$pdf->row_height,"�ֹε�Ϲ�ȣ",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.54,$pdf->row_height,$jumin,1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"��  ��  ��  ��",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.34,$pdf->row_height,$birthDay,1,0,'L');
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"���� / ��",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.42,$pdf->row_height,$age."�� / ".$ddi."��",1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"���Ա�������",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.21,$pdf->row_height,StripSlashes($mmse['edu_training']),1,0,'L');
	$pdf->Cell($pdf->width * 0.13,$pdf->row_height,"�ѱ��ص��ɷ�",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.54,$pdf->row_height,StripSlashes($mmse['kor_decode']),1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"��    ��    ��",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$addr,1,1,'L');

	//��ȥ����
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'MR\'';

	$marryStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$marry);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"��  ȥ  ��  ��",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$marryStr,1,1,'L');

	//���ű���
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CB\'';

	$cohabitStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$cohabit);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"��           ��",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$cohabitStr,1,1,'L');

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'MCL\'
			AND		use_yn	= \'Y\'';

	$costStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$costGbn);

	$conn->row_free();

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height * 2,"��    ��    ��",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$costStr,1,2,'L');
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,"�� �� ���Կ� �����ϴ� ��������? (     ".$familyCnt."  )",1,1,'L');

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	= \'MDC\'
			AND		use_yn	= \'Y\'';

	$medicalStr = lfGetGbnStr($pdf,$myF,$conn,$sql,$medicalGbn);

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"��  ��  ��  ��",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.88,$pdf->row_height,$medicalStr,1,1,'L');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.12,$pdf->row_height,"�ֵ��������",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.21,$pdf->row_height,StripSlashes($mmse['life_job']),1,0,'L');
	$pdf->Cell($pdf->width * 0.08,$pdf->row_height,"������",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.18,$pdf->row_height,$myF->euckr($myF->dateStyle($mmse['check_dt'],'KOR')),1,0,'L');
	$pdf->Cell($pdf->width * 0.10,$pdf->row_height,"�����",1,0,'C',1);
	$pdf->Cell($pdf->width * 0.31,$pdf->row_height,StripSlashes($mmse['evl_place']),1,1,'L');



	$quest[]	= Array('id'=>'Q1','no'=>'1.','quest'=>'���ش� �� �⵵ �Դϱ�?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q2','no'=>'2.','quest'=>'������ ���� �����Դϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q3','no'=>'3.','quest'=>'������ ��ĥ�Դϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q4','no'=>'4.','quest'=>'������ ���� �����Դϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q5','no'=>'5.','quest'=>'������ �� ���Դϱ�?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q6','no'=>'6.','quest'=>'�츮�� �ִ� �̰��� ���� ��/Ư����/������ �Դϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q7','no'=>'7.','quest'=>'����� ���� ��/��/�� �Դϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q8','no'=>'8.','quest'=>'����� ���� ��/��/�� �Դϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q9','no'=>'9.','quest'=>'�츮�� ���� �� �ǹ��� �� ���� �ֽ��ϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q10','no'=>'10.','quest'=>'�� ����� �̸��� �����Դϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'T','no'=>'11.','quest'=>" ���� �� ���� ������ �̸��� �����帮�ڽ��ϴ�. ������ �� ������ ������ �� ���� ������ �̸��� ��� ������ ���ʽÿ�. �׸��� �� �� �Ŀ��� �� ������ ������ �̸����� �ٽ� ��� ���̴� ������ ������ �̸��� �� ����ϰ� ��ʽÿ�.\n\n",'rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'T','no'=>'','quest'=>"����          �ڵ���          ����",'rst'=>'N','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'T','no'=>'','quest'=>"���� �ۡۡ۴Բ��� ��� ������ 3���� ���� �̸��� ��� ������ ������.",'rst'=>'N','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q11','no'=>'','quest'=>'����','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q12','no'=>'','quest'=>'�ڵ���','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q13','no'=>'','quest'=>'����','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q14','no'=>'12.','quest'=>'100���� 7�� ���� �󸶰� �˴ϱ�?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q15','no'=>'','quest'=>'�ű⿡�� 7�� ���� �󸶰� �˴ϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q16','no'=>'','quest'=>'�ű⿡�� 7�� ���� �󸶰� �˴ϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q17','no'=>'','quest'=>'�ű⿡�� 7�� ���� �󸶰� �˴ϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q18','no'=>'','quest'=>'�ű⿡�� 7�� ���� �󸶰� �˴ϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'T','no'=>'13.','quest'=>'���� ���� ���� ����϶�� ������ȴ� �� ���� ������ �̸��� �������� �����Ͽ� �ֽʽÿ�?','rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q19','no'=>'','quest'=>'����','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q20','no'=>'','quest'=>'�ڵ���','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q21','no'=>'','quest'=>'����','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q22','no'=>'14.','quest'=>'(���� �ð踦 �����ָ�) �̰��� �����̶�� �մϱ�?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q23','no'=>'','quest'=>'(���� ������ �����ָ�) �̰��� �����̶�� �մϱ�?','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'T','no'=>'15.','quest'=>'���� �ϴ� ���� ������ ��� ������ ���ʽÿ�. �� ���� �����帱 ���̴� �� ��� ���� �Ͻʽÿ�.','rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q24','no'=>'','quest'=>'������������','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'T','no'=>'16.','quest'=>"���ݺ��� ���� �����帮�� ��� �� ���ʽÿ�. �� ���� �����帱 ���̴� �� �����ð� �״�� �� ���ʽÿ�.\n���� ���̸� �� �� �帱 ���Դϴ�. �׷��� �� ���̸� ���������� �޾�, ������ ���� ����, ���� ���� �÷������ʽÿ�.\n\n",'rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q25','no'=>'','quest'=>'���������� �޴´�.','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q26','no'=>'','quest'=>'������ ���´�.','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'Q27','no'=>'','quest'=>'���� ���� ���´�.','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1','align'=>'C');
	$quest[]	= Array('id'=>'T','no'=>'17.','quest'=>"(��ģ ������ �׸��� ����Ű��) ���⿡ �������� ������ �ִ� �׸��� �ֽ��ϴ�. �� �׸��� �Ʒ� �� ���� �״�� �׷����ʽÿ�.\n",'rst'=>'N','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q28','no'=>'','quest'=>"\n\n\n\n\n\n",'path'=>'../hce/img/test_17.GIF','rst'=>'Y','border'=>'','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q29','no'=>'18.','quest'=>'���� �� ���Ƽ� �Խ��ϱ�?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');
	$quest[]	= Array('id'=>'Q30','no'=>'19.','quest'=>'"Ƽ�� ��� �»�"�� ���� �� �Դϱ�?','rst'=>'Y','border'=>'Y','Y'=>'0','N'=>'1');



	$col[] = $pdf->width * 0.04;
	$col[] = $pdf->width * 0.76;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.10;

	$pdf->SetXY($pdf->left, $pdf->GetY() + 10);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$pdf->row_height,"�� MMSE-DS","B",1,'L');
	$pdf->SetFont($pdf->font_name_kor,'',9);
	$pdf->SetFillColor(234,234,234);

	$pnt = 0;

	foreach($quest as $row){
		if ($row['no'] == '13.'){
			$pdf->Line($pdf->left,$pdf->GetY(),$pdf->left+$pdf->width,$pdf->GetY());

			lfDrawPos($pdf,$pos);

			$pdf->MY_ADDPAGE();
			$pdf->SetFont($pdf->font_name_kor,'',9);
			$pdf->SetXY($pdf->left, $pdf->top);

			Unset($pos);

			$border = "T";
		}else{
			$border = "";
		}

		if ($row['border']) $border = "T";

		$tmp = Explode("\n",$row['quest']);
		$cnt = SizeOf($tmp);

		if ($row['path']){
			$path = $row['path'];

			if (is_file($path)){
				$pdf->Image($path, $pdf->left+$col[0], $pdf->GetY());
			}
		}else{
			$pos[] = Array('X'=>$pdf->left+$col[0],'Y'=>$pdf->GetY() + 0.3,'width'=>$col[1],'align'=>$row['align'],'text'=>$row['quest']);
		}

		for($i=0; $i<$cnt; $i++){
			$Y = $pdf->GetY();

			$pdf->SetX($pdf->left);
			$pdf->Cell($col[0],$pdf->row_height,($i == 0 ? $row['no'] : ""),($i == 0 ? $border : "")."L",0,'R');
			$pdf->Cell($col[1],$pdf->row_height,"",($i == 0 ? $border : ""),0,'L');

			if ($i == 0){
				$pdf->Cell($col[2],$pdf->row_height * $cnt,($mmse[$row['id']] == 'Y' ? $row['Y'] : ''),($i == 0 ? $border : "")."L",0,'C',($row['rst'] == 'Y' ? '1' : '0'));
				$pdf->Cell($col[3],$pdf->row_height * $cnt,($mmse[$row['id']] == 'N' ? $row['N'] : ''),($i == 0 ? $border : "")."LR",1,'C',($row['rst'] == 'Y' ? '1' : '0'));
				$pdf->SetY($Y + $pdf->row_height);
			}else{
				$pdf->Cell($col[2],$pdf->row_height,"",($i == 0 ? $border : "")."L",0);
				$pdf->Cell($col[3],$pdf->row_height,"",($i == 0 ? $border : "")."LR",1);
			}
		}

		if ($mmse[$row['id']] == 'Y') $pnt += IntVal($row['Y']);
		if ($mmse[$row['id']] == 'N') $pnt += IntVal($row['N']);
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width * 0.15,$pdf->row_height,"��   ��",1,0,'C');
	$pdf->Cell($pdf->width * 0.85,$pdf->row_height,"(".$pnt.") / 30��",1,1,'L');

	$Y = $pdf->GetY();

	lfDrawPos($pdf,$pos);

	Unset($pos);
	Unset($col);



	$col[] = $pdf->width * 0.120;
	$col[] = $pdf->width * 0.120;
	$col[] = $pdf->width * 0.003;
	$col[] = $pdf->width * 0.190;
	$col[] = $pdf->width * 0.190;
	$col[] = $pdf->width * 0.190;
	$col[] = $pdf->width * 0.190;

	$pdf->SetXY($pdf->left, $Y + 10);
	$pdf->SetFont($pdf->font_name_kor,'B',13);
	$pdf->Cell($pdf->width,$pdf->row_height,"�� MMSE-DS ���ܰ˻� �Ƿ� ����","B",1,'L');
	$pdf->SetFont($pdf->font_name_kor,'',11);

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"��   ��",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height * 2,"��   ��",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height * 2,"","T",0,'C');
	$pdf->Cell($col[3] + $col[4] + $col[5] + $col[6],$pdf->row_height,"��  ��  ��  ��",1,2,'C');
	$pdf->Cell($col[3],$pdf->row_height,"0~3��",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"4~6��",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"7~12��",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"��13��",1,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$col[2],"",0,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"60~69��",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"��",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"20",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"24",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,2,'C');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"��",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"19",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"23",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,2,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"70~74��",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"��",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"21",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"23",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,2,'C');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"��",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"18",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"21",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"75~79��",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"��",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"20",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"22",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"25",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"25",1,2,'C');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"��",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"17",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"21",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"24",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"26",1,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0],$pdf->row_height * 2,"��80��",1,0,'C');
	$pdf->Cell($col[1],$pdf->row_height,"��",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"",0,0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"18",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"22",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"24",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"25",1,2,'C');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1],$pdf->row_height,"��",1,0,'C');
	$pdf->Cell($col[2],$pdf->row_height,"","B",0,'C');
	$pdf->Cell($col[3],$pdf->row_height,"16",1,0,'C');
	$pdf->Cell($col[4],$pdf->row_height,"20",1,0,'C');
	$pdf->Cell($col[5],$pdf->row_height,"24",1,0,'C');
	$pdf->Cell($col[6],$pdf->row_height,"27",1,1,'C');

	$pdf->SetX($pdf->left);
	$pdf->Cell($pdf->width,$pdf->row_height,"�� �� ǥ�� ���õ� ���� ������ ��� ���ܰ˻�� �Ƿ���.",0,1,'L');

	Unset($mmse);
	Unset($col);
	Unset($quest);


	function lfDrawPos($pdf,$pos){
		foreach($pos as $row){
			$pdf->SetXY($row['X'],$row['Y']);
			$pdf->MultiCell($row['width'], 5, $row['text'], 0, $row['align']);
		}
	}
?>
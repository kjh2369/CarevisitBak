<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	���ȸ�Ƿ�
	 *********************************************************/
	$pdf->SetFont($pdf->font_name_kor,'',9);

	$orgNo = $_SESSION['userCenterCode'];

	//�����
	$sql = 'SELECT	m03_name AS name
			,		jumin AS jumin
			FROM	m03sugupja
			INNER   JOIN mst_jumin
			ON		org_no = m03_ccode
			AND     code   = m03_jumin
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$row = $conn->get_array($sql);

	$name	= $row['name'];
	$jumin	= $myF->issStyle($row['jumin']);

	Unset($row);

	

	//���ȸ�Ƿ�
	$sql = 'SELECT	*
			FROM	hce_meeting
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		meet_seq= \''.$var['idx'].'\'
			AND     del_flag= \'N\'';
	$meet = $conn->get_array($sql);
	
	

	//���� ���� ����
	$decisionGbn = $meet['decision_gbn'];

	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type = \'CMT\'';

	$str = lfGetGbnStr($pdf,$myF,$conn,$sql,$meet['meet_gbn']);

	//�������� meet_gbn
	$pdf->SetFont($pdf->font_name_kor,'B',11);
	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($pdf->width,$pdf->row_height,"����(".$str.") ����ȸ��",0,1,'C');
	$pdf->SetFont($pdf->font_name_kor,'',9);


	$col[] = $pdf->width * 0.12;
	$col[] = $pdf->width * 0.10;
	$col[] = $pdf->width * 0.34;
	$col[] = $pdf->width * 0.20;
	$col[] = $pdf->width * 0.24;

	$rowH = $pdf->row_height * 1.5;

	//ȸ������, ������
	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($col[0], $rowH, "ȸ������", 1, 0, 'C', 1);
	$pdf->Cell($col[1] + $col[2], $rowH, $myF->euckr($myF->dateStyle($meet['meet_dt'],'KOR')), 1, 0, 'C');
	$pdf->Cell($col[3], $rowH, "��  ��  ��", 1, 0, 'C', 1);
	$pdf->Cell($col[4], $rowH, $meet['examiner'], 1, 1, 'C');

	
	//������
	$str = '';
	$arr = Explode('&',$meet['attendee']);
	$cnt = 0;

	
	//��������
	$sql = 'SELECT	DISTINCT
					m02_yjumin AS jumin
			,       m02_key   AS code
			,		m02_yname AS name
			FROM	m02yoyangsa
			INNER	JOIN	mem_option
					ON		mem_option.org_no		= m02_ccode
					AND		mem_option.mo_jumin		= m02_yjumin';

	if ($hce->SR == 'S'){
		$sql .= '	AND		mem_option.support_yn	= \'Y\'';
	}else{
		$sql .= '	AND		mem_option.response_yn	= \'Y\'';
	}

	$sql .= '
			INNER	JOIN	mem_his
					ON		mem_his.org_no	 = m02_ccode
					AND		mem_his.jumin	 = m02_yjumin
					AND		mem_his.join_dt	<= \''.$meet['meet_dt'].'\'
					AND		IFNULL(mem_his.quit_dt,\'9999-12-31\') >= \''.$meet['meet_dt'].'\'
			WHERE	m02_ccode = \''.$orgNo.'\'
			ORDER	BY name';
	
	
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	
	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
		$mem[$row['jumin']][$row['code']]['name'] = $row['name'];

		if (is_array($arr)){
			foreach($arr as $key){
				if($key==$row['code']){
					if($mem[$row['jumin']][$key]['name'] != $tmpName){
						$tmpName = $mem[$row['jumin']][$key]['name'];
						$str .= ($str ? ', ' : '').$mem[$row['jumin']][$key]['name'];
						$cnt ++;
					}
				}
			}
		}
		
	}
	
	
	

	
	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $rowH, "��  ��  ��", 1, 0, 'C', 1);
	$pdf->Cell($col[1] + $col[2], $rowH, $str, "LTB", 0, 'L');
	$pdf->Cell($col[3] + $col[4], $rowH, "(�� ".$cnt."��)", "RTB", 1, 'L');

	//����� ������
	$txtW[0] = $pdf->GetStringWidth("��  ��  ��");
	$txtH[0] = lfGetStringHeight($pdf, $col[1] + $col[2] + $col[3] + $col[4], "��  ��  ��\n��  ��  ��");
	$txtH[1] = lfGetStringHeight($pdf, $col[1] + $col[2] + $col[3] + $col[4], StripSlashes($meet['attendee_other'])) + 1;

	if ($txtH[1] < $rowH) $txtH[1] = $rowH;

	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left,'Y'=>$Y + ($txtH[1]-$txtH[0])/2,'width'=>$col[0],'align'=>'C','text'=>"��  ��  ��\n��  ��  ��");
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y + 1,'width'=>$col[1] + $col[2] + $col[3] + $col[4],'text'=>StripSlashes($meet['attendee_other']));

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $txtH[1], "", 1, 0, 'C', 1);
	$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4], $txtH[1], "", 1, 1, 'L');

	$pdf->SetXY($pdf->left, $pdf->GetY() + 5);
	$pdf->Cell($col[0], $rowH, "��  ��  ��", 1, 0, 'C', 1);
	$pdf->Cell($col[1], $rowH, "��  ��", 1, 0, 'C', 1);
	$pdf->Cell($col[2], $rowH, $name, 1, 0, 'C');
	$pdf->Cell($col[3], $rowH, "�ֹε�Ϲ�ȣ", 1, 0, 'C',1);
	$pdf->Cell($col[4], $rowH, $jumin, 1, 1, 'C');

	$W = $col[1] + $col[2] + $col[3] + $col[4];

	//������ ���� ����
	$tmp = str_replace('/','&',$meet['decision_svc']);
	$tmp = str_replace(':','=',$tmp);

	parse_str($tmp,$arr);

	$str = '';
	$sql = 'SELECT	DISTINCT
					care.suga_cd AS cd
			,		suga.nm1 AS mst_nm
			,		suga.nm2 AS pro_nm
			,		suga.nm3 AS svc_nm
			FROM	care_suga AS care
			INNER	JOIN	suga_care AS suga
					ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
					AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
					AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
			WHERE	care.org_no	= \''.$orgNo.'\'
			AND		care.suga_sr= \''.$hce->SR.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($arr[$row['cd']] == 'Y'){
			$str .= ($str ? ', ' : '').$row['svc_nm'];
		}
	}

	$conn->row_free();

	$txtH[0] = lfGetStringHeight($pdf, $W, "�� ��Ȱ���� �� �ǰ�����\n".StripSlashes($meet['life_lvl'])) + 1;
	$txtH[1] = lfGetStringHeight($pdf, $W, "�� ���� ".($decisionGbn == '1' ? "����" : "����")." ��û ����\n".StripSlashes($meet['req_rsn'])) + 1;
	$txtH[2] = lfGetStringHeight($pdf, $W, "�� ���� ".($decisionGbn == '1' ? "����" : "����")." ���� ����\n".StripSlashes($meet['decision_rsn'])) + 1;
	$txtH[3] = lfGetStringHeight($pdf, $W, "�� ������ ���� ����\n".$str) + 1;

	$txtCnt = SizeOf($txtH);

	for($i=0; $i<$txtCnt; $i++){
		if ($txtH[$i] < $rowH * 3) $txtH[$i] = $rowH * 3;
	}


	//��Ȱ���� �� �ǰ�����
	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y + 1,'width'=>$W,'text'=>"�� ��Ȱ���� �� �ǰ�����\n".StripSlashes($meet['life_lvl']));

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $txtH[0] + $txtH[1], "ȸ�ǳ���", 1, 0, 'C', 1);
	$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4], $txtH[0], "", 1, 1, 'L');

	//���� ����(����) ��û ����
	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y + 1,'width'=>$W,'text'=>"�� ���� ".($decisionGbn == '1' ? "����" : "����")." ��û ����\n".StripSlashes($meet['req_rsn']));
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4], $txtH[1], "", 1, 1, 'L');


	$Y = $pdf->GetY();
	$H = $pdf->height - $Y;

	if ($Y + $rowH + $rowH + $txtH[2] + $txtH[3] > $pdf->height){
		foreach($pos as $row){
			$pdf->SetXY($row['X'],$row['Y']);
			$pdf->MultiCell($row['width'], 4, $row['text'], 0, $row['align']);
		}
		Unset($pos);
		$pdf->MY_ADDPAGE();
		$pdf->SetFont($pdf->font_name_kor,'',9);
		$Y = $pdf->GetY();
		$H = $pdf->height - $Y;
	}

	$pdf->SetX($pdf->left);
	$pdf->Cell($col[0], $H, "�������", 1, 0, 'C', 1);
	$pdf->Cell($col[1] + $col[2], $rowH, "������������", 1, 0, 'C', 1);
	$pdf->Cell($col[3] + $col[4], $rowH, ($decisionGbn == '1' ? "��" : "��")."����     ".($decisionGbn == '2' ? "��" : "��")."����", 1, 1, 'C');

	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1] + $col[2], $rowH, ($decisionGbn == '1' ? "��   ��   " : "��   ��   ")."��   ��", 1, 0, 'C', 1);
	$pdf->Cell($col[3] + $col[4], $rowH, $myF->euckr($myF->dateStyle($meet['decision_dt'],'KOR')), 1, 1, 'C');


	//���� ���� �Ǵ� ���� ��������
	$Y = $pdf->GetY();
	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y + 1,'width'=>$W,'text'=>"�� ���� ".($decisionGbn == '1' ? "����" : "����")." ���� ����\n".StripSlashes($meet['decision_rsn']));
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4], $txtH[2], "", 1, 1, 'L');

	//������ ���� ����
	$Y = $pdf->GetY();
	$H = $pdf->height - $Y;

	$pos[] = Array('X'=>$pdf->left + $col[0],'Y'=>$Y + 1,'width'=>$W,'text'=>"�� ������ ���� ����\n".$str);
	$pdf->SetX($pdf->left + $col[0]);
	$pdf->Cell($col[1] + $col[2] + $col[3] + $col[4], $H, "", 1, 1, 'L');

	Unset($meet);

	foreach($pos as $row){
		$pdf->SetXY($row['X'],$row['Y']);
		$pdf->MultiCell($row['width'], 4, $row['text'], 0, $row['align']);
	}

	Unset($pos);
	Unset($col);

	include('../hce/hce_meeting_pic_pdf.php');

?>
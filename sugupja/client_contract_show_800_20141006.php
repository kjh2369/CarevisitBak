<?
	include_once("../inc/_definition.php");

	$conn->set_name('euckr');

	$code = $_SESSION['userCenterCode'];	//�����ȣ
	$kind = $_POST['kind'];				//���񽺱���
	$ssn = $ed->de($_POST['jumin']);	//�������ֹι�ȣ
	$svc_seq   = $_POST['seq'];			//���򰡰���(���Ű)	
	$seq   = $_POST['seq'];			//���򰡰���(���Ű)	
	
	$report_id = $_POST['report_id'];	//���򰡰���(�̿��༭)
	
	//$ctIcon   = $conn->center_icon($mCode);
	if(($report_id != '') or ($seq != '')){
		
		$svc_seq = $ct['svc_seq'] != '' ? $ct['svc_seq'] : $svc_seq;
		
		$sql = 'select svc_cd
				,	   seq
				,	   reg_dt
				,	   svc_seq
				,      use_yoil1
				,      from_time1
				,      to_time1
				,      use_yoil2
				,      from_time2
				,      to_time2
				,	   pay_day1
				,	   pay_day2
				  from client_contract
				 where org_no   = \''.$code.'\'
				   and svc_cd   = \''.$kind.'\'
				   and jumin    = \''.$ssn.'\'
				   and seq      = \''.$seq.'\'
				   and del_flag = \'N\'';
		$ct = $conn->get_array($sql);

		
		$sql =  ' select from_dt 
				  ,		 to_dt 
					from client_his_svc
				   where org_no = \''.$code.'\'
					 and jumin  = \''.$ssn.'\'
					 and seq    = \''.$svc_seq.'\'';
		
		$svc = $conn->get_array($sql);


		$sql = "select m03_jumin as jumin
				,	   m03_key
				,	   m03_name as name
				,	   m03_tel as tel
				,	   m03_yboho_name as bohoName
				,	   m03_yboho_juminno as bohoJumin
				,	   m03_yboho_gwange as gwange
				,	   m03_yboho_phone as bohoPhone
				,	   m03_yboho_addr as bohoAddr
				,	   lvl.level as level
				,	   lvl.app_no as injungNo
				,	   case lvl.level when '9' then '�Ϲ�' else concat(lvl.level,'���') end as level
				,	   case kind.kind when '3' then '���ʼ��ޱ���' when '2' then '�Ƿ���ޱ���' when '4' then '�氨�����' else '�Ϲ�' end as m92_cont
				,	   kind.kind
				,	   concat(m03_juso1, ' ', m03_juso2) as juso
				,	   max(lvl.from_dt)
				,	   max(lvl.to_dt)
				,	   max(kind.from_dt)
				,	   max(kind.to_dt)
				  from m03sugupja
				  left join ( select jumin
							  ,		 from_dt 
							  ,		 to_dt 
							  ,		 app_no
							  ,		 level
								from client_his_lvl
							   where org_no = '".$code."'
								 and from_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
								  or to_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
							   order by from_dt desc
								 ) as lvl
						 on lvl.jumin = m03_jumin
				  left join ( select jumin
							  ,		 from_dt 
							  ,		 to_dt 
							  ,		 kind
								from client_his_kind
							   where org_no = '".$code."'
								 and from_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
								  or to_dt between '".$svc['from_dt']."' and '".$svc['to_dt']."'
							   order by from_dt desc 
							   ) as kind
						 on kind.jumin = m03_jumin
				 where m03_ccode = '".$code."'
				   and m03_mkind = '".$kind."'
				   and m03_jumin = '".$ssn."'
				   and m03_del_yn = 'N'";
		
		//echo nl2br($sql); exit;
		$su = $conn->get_array($sql);

		
	}
	
	$sql = "select m00_mname as manager"
			 . ",      concat(m00_caddr1, ' ', m00_caddr2) as address"
			 . ",      m00_cname as centerName"
			 . ",      m00_code1 as centerCode"
			 . ",      m00_ctel as centerTel"
			 . ",      m00_fax_no as centerFax"
			 . ",      m00_bank_no as bankNo"
			 . ",      m00_bank_name as bankCode"
			 . ",      m00_jikin as jikin"
			 . "  from m00center"
			 . " where m00_mcode = '".$code
			 . "'  and m00_mkind = '".$kind."'";
			
	$center = $conn->get_array($sql);

	$bank = $center['bankNo'] != '' ? iconv('utf-8','euc-kr', $definition->GetBankName($center['bankCode']))."(".$center['bankNo'].")��" : " ";
		
	$jikin = $center['jikin'];
	$file = '../mm/sign/client/'.$code.'/'.$su['m03_key'].'_r.jpg'; //���� 

	$from_year = $svc['from_dt'] != '' ? substr($svc['from_dt'],0,4) : '           ';	//�����۱Ⱓ(��)
	$from_month = $svc['from_dt'] != '' ? substr($svc['from_dt'],5,2) : '     ';		//�����۱Ⱓ(��)
	$from_day = $svc['from_dt'] != '' ? substr($svc['from_dt'],8,2) : '     ';		//�����۱Ⱓ(��)
	
	$to_year = $svc['to_dt'] != '' ? substr($svc['to_dt'],0,4) : '           ';		//�������Ⱓ(��)
	$to_month = $svc['to_dt'] != '' ? substr($svc['to_dt'],5,2) : '     ';			//�������Ⱓ(��)
	$to_day = $svc['to_dt'] != '' ? substr($svc['to_dt'],8,2) : '     ';			//�������Ⱓ(��)
	
	$yoil = '';
	
	//�̿����
	for($i=0; $i<7; $i++){
		if($ct['bath_weekly'][$i] == 'Y' and ($i == 0)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 1)){
			$yoil .= 'ȭ';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 2)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 3)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 4)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 5)){
			$yoil .= '��';
		}else if($ct['bath_weekly'][$i] == 'Y'  and ($i == 6)){
			$yoil .= '��';
		}

	}
	

	$pdf->MY_ADDPAGE();

	$pdf->SetFont($pdf->font_name_kor, "B", $pdf->font_size + 7);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "�����޿� �̿� ��༭(�湮��ȣ)", 0,"C");
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size + 1);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	if($su['name']!=''){
		$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, $su['name']." (���� [�̿���]��� �մϴ�)�� ".$center['centerName']."(���� [�����]��� �մϴ�)�� ����ڰ� �̿��ڿ� ���ؼ� �ǽ��ϴ� �湮 ��ȣ�� ���� ������ ���� ����մϴ�.");
	}else {
		$pdf->MultiCell($pdf->width*0.9, $pdf->row_height, "_____________________ (���� [�̿���]��� �մϴ�)�� ".$center['centerName']."(���� [�����]��� �մϴ�)�� ����ڰ� �̿��ڿ� ���ؼ� �ǽ��ϴ� �湮 ��ȣ�� ���� ������ ���� ����մϴ�.");
	}
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"��1��(����)\n  ����ڴ� �̿��ڿ� ���� ��纸�� ������ ������ ���� �̿��ڰ� ������ �� �� ���ÿ� ���Ͽ� �� ������ �ɷ¿� ���� �ڸ��� �ϻ��Ȱ�� ������ ���� �ֵ��� �̿����� ��� ��Ȱ�� �����Ͽ� �ɽ��� ��� ���� ȸ���� ��ǥ�� �ϴ� ���� �������� �湮 ��ȣ ���񽺸� �����ϸ� �̿��ڴ� ����ڿ� ���� �� ���񽺿� ���� ����� �����մϴ�.
	
	��2�� (���Ⱓ)
	  1. �� ����� ���Ⱓ��  ".$from_year." ��  ".$from_month." �� ".$from_day." �Ϻ��� �̿����� ��� ��ȣ ������ ��ȿ          �Ⱓ �����ϱ����� �մϴ�.
	  2. ��� ������ 2�������� �̿��ڷκ��� ����ڿ� ���ؼ�, ������ ���� ��� ������ ��û��          ���� ��� ����� �ڵ� ���ŵǴ� ������ �մϴ�.
	  
	��3�� (�湮��ȣ ��ȹ�� �ۼ�������)
	  1. ����ڴ� �̿��ڿ� ���õǴ� ǥ������̿��ȹ�� �ۼ��Ǿ� �ִ� ��쿡�� �ű⿡ ����           �̿����� �湮 ��ȣ ��ȹ�� �ۼ��ϴ� ������ �մϴ�.
	  2. ����ڴ� ��ġ���� ���� �̿����� �ϻ��Ȱ ������ ��Ȳ �� ����� �ٰŷ� �Ͽ� ���湮           ��ȣ ��ȹ���� �ۼ��մϴ�. ����ڴ� �̡��湮 ��ȣ ��ȹ���� ������ �̿��� �� �� ����         ���� �����Ͽ� �� ���Ǹ� ��� ������ �մϴ�.
	  3. ����ڴ� ������ ��� ���ΰ��� �ش��ϴ� ��쿡�� ��1���� ���� �ϴ� �湮 ��ȣ ����         �� ������ ���� �湮 ��ȣ ��ȹ�� ������ �ǽ��մϴ�.
	   ���̿����� �ɽ��� ��Ȳ, �� ó���� �ִ� ȯ�� ���� ��ȭ�� ���� �ش� �湮 ��ȣ ��ȹ�� ��          ���� �ʿ䰡 �ִ� ���.
	   ���̿��ڰ� �湮 ��ȣ ������ �����̳� ���� ��� ���� ������ ����ϴ� ���.
	  4. ����ڴ� �湮 ��ȣ ��ȹ�� �������� ��쿡�� �̿��ڿ� ���ؼ� �������� �����Ͽ� �� ��        ���� Ȯ���ϴ� ������ �մϴ�.

   ��4�� (��ġ�ǿ��� ����)
	  1. ����ڴ� �湮 ��ȣ ������ ������ �����Ϸ��� ��ġ���� ���ø� ������ �޽��ϴ�.
	  2. ����ڴ� ��ġ�ǿ� �湮 ��ȣ ��ȹ�� �� �湮 ��ȣ ������ �����Ͽ� ��ġ�ǿ��� ������         ���޸� ���մϴ�.

	��5�� (�湮��ȣ ������ ����)
	  1. �̿��ڰ� ������ �޴� �湮 ��ȣ ������ ����������༭ �������� ���ߴ� �ٿ� ������         ��. ����ڴ�,������� �������� ���� ���뿡 ���Ͽ� �̿��� �� �� �������� �����մϴ�.
	  2. ����ڴ� ���� �������� �̿����� ���ÿ� �İ��� �湮 ��ȣ ��ȹ�� ���󡼰�༭ ����         ���� ���� ������ �湮 ��ȣ ���񽺸� �����մϴ�.
	  3. �湮 ��ȣ ��ȹ�� �̿��ڿ��� ���Ǹ� ������ ����Ǿ� ����ڰ� �����ϴ� ������ ����         �Ǵ� ��纸�� ������ ������ ������ �Ǵ�  ���� �̿����� �³��� ��� ���ο� ������        ����༭ �������� �ۼ��Ͽ� �װ��� ������ �湮 ��ȣ ������ �������� �մϴ�.");
	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"��6�� (���� ������ ���)
  1. ����ڴ� �湮 ��ȣ ������ �ǽø��� ���� ���� ���� ���񽺽ǽ� ��Ϻο� �����ϰ�        ������ ����ÿ� �̿����� Ȯ���� �޴°����� �մϴ�. �� ����� �̿����� ����� ����       �� ������ �̿�  �ڿ��� �����մϴ�.
  2. ����ڴ� ���� �ǽ� ��Ϻθ� �ۼ��ϴ� ������ �ϸ�, �� ����� ���� �� 2�Ⱓ ������        �ϴ�.
  3. �̿��ڴ� ������� ���� �ð����� �� ����ҿ��� �ش� �̿��ڿ� ���� ��2���� ���� ��       �� ��Ϻθ� ������ �� �ֽ��ϴ�.
  4. �̿��ڴ� ����� ������ ������ �ش� �̿��ڿ� ���� ��2���� ���� �ǽ� ��Ϻ��� ��       �繰������ ���� ���� �ֽ��ϴ�.

��7�� (�湮��ȣ���� ��ü ��)
  1. �̿��ڴ� ���ӵ� �湮 ��ȣ���� ��ü�� ����ϴ� ��쿡�� �ش�  �湮 ��ȣ�簡 ������         �������̶�� �����Ǵ� ���� �� �� ��ü�� ����ϴ� ������ �и��� �Ͽ� ����ڿ� ���ؼ�        �湮 ��ȣ���� ��  ü�� ��û�� ���� �ֽ��ϴ�.
  2. ����ڴ� �湮 ��ȣ���� ��ü�� ���� �̿��� �� �� ���� � ���ؼ� ���� �̿���� ��        ������ ������ �ʰ� ����� ����ϴ� ������ �մϴ�.
  3. �湮 ��ȣ�簡 ����� �ҷ� ���� ���� �湮 �� �� ���� �Ǿ��� ������ ��ü �ο��� �μ�       �ϰ� �μ� �� ���� �̿��� �� �� �������� �����ϰڽ��ϴ�.

��8�� (���)
  1. �̿��ڴ� ������ �밡�� �ؼ�����༭ �������� ���ϴ� �̿� ���������� ����� �⺻         ���� ���� �ſ��� �հ� �ݾ��� �����մϴ�.
  2. ����ڴ� ��� ����� �հ���� û������ ���� �����Ͽ� ���� �� 10�ϱ��� �̿��ڿ���       �ۺ��մϴ�.
  3. �̿��ڴ� ��� ����� �հ���� ������ 15�ϱ��� ������� �����ϴ� ������� �����մ�        ��.
  4. �̿��ڴ� ���ÿ� ���� ���� �������� ���� �ǽø� ���ؼ� ����ϴ� ����, ����, ����,       ��ȭ�� ����� �δ��մϴ�.

��9�� (������ ����)
  1. �̿��ڴ� ����ڿ� ���ؼ� ���� �ǽ����� �������� ���� 6�ñ��� ������ �ϴ� ������        �� ����� �δ��ϴ� �� ���� ���� �̿��� ������ ���� �ֽ��ϴ�.
  2. �̿��ڰ� ���� �ǽ����� ���������� ���� 5�ñ��� �����ϴ� �� ���� ������ ������        ������� ��쿡�� ����ڴ� �̿��ڿ� ���ؼ�,����༭ �������� ���ϴ� �������\n     ����, ����� ���� �Ǵ� �Ϻθ� ĵ����� �ؼ� û���� ���� �ֽ��ϴ�. �� ����� �����\n     ��6���� ���ϴ� �ٸ� ����� ���Ұ� ���Ͽ� û���մϴ�.

��10�� (����� ����)
   1. ����ڴ� �̿��ڿ� ���ؼ� 1���������� ������ �����ϴ� �����ν� �̿� ���������� ��         ���� ����(���� �Ǵ� ����)�� ��û�� ���� �ֽ��ϴ�.
   2. �̿��ڰ� ����� ������ �³��ϴ� ���, ���ο� ��ݿ� �ٰ��ϴ¡���༭ �������� �ۼ�        ��, ���� �ְ� �޽��ϴ�.
   3. �̿��ڰ� ����� ������ �³����� �ʴ� ���, ����ڿ� ����, ������ �����ϴ� �����ν�,        �� ����� �ؾ��� ���� �ֽ��ϴ�.");

	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"��11�� (����� ����)
	 1. �̿��ڴ� ����ڿ� ���ؼ� 1�ְ��� ���� �Ⱓ�� �ξ� ������ ������ �ϴ� �����ν�, ��\n     ����� �ؾ��� ���� �ֽ��ϴ�. 
     �ٸ�, �̿����� ����, ���۽����� �Կ� �� ��¿ �� ���� ������ �ִ� ���� ���� �Ⱓ��\n     1�ְ� �̳��� ���������� �� ����� �ؾ��� ���� �ֽ��ϴ�.
  2. ����ڴ� �ε����� ������ �ִ� ��� �̿��ڿ� ���ؼ� 1�������� ���� �Ⱓ�� �ξ� ����       �� ��Ÿ�� ������ �����ϴ� �����ν� �� ����� �ؾ��� ���� �ֽ��ϴ�.
  3. ������ �� 1���� ������ �ش����� ���� �̿��ڴ� ������ �����ϴ� �����ν� ��� ��\n     ����� �ؾ��� ���� �ֽ��ϴ�.
    �����ڰ� ������ ���� ���� ���񽺸� �������� �ʴ� ���.
    �����ڰ� ����� ��ų �ǹ��� ������ ���
    �����ڰ� �̿��ڳ� �� ���� ��� ���� ��ȸ ����� ��Ż�ϴ� ������ �ǽ����� ���
    �����ڰ� �Ļ����� ���
  4. ������ ��1���� ������ �ش����� ���� ����ڴ� ������ �����ϴ� �����ν� ��� �� ��       ���� �ؾ��� ���� �ֽ��ϴ�.
    ���̿����� ���� �̿����� ������ 3���� �̻� ���� ��, ����� �����ϵ��� �ְ� ����         ���� �ұ��ϰ� 10�� �̳��� ���ҵ��� �ʴ� ���
    ���̿��� �Ǵ� �� ������ ����ڳ� ���� �������� ���ؼ�, �� ����� ��� �ϱ� �����          ��ŭ�� ��������� �ǽ����� ���.
  5. ������ ��1���� ������ �ش����� ���� �� ����� �ڵ������� �����մϴ�.
    ���̿��ڰ� ��纸�� �ü��� �Լ� ���� ���
    ���̿����� ��� ��ȣ ���� ������ ��޿ܶ�� �����Ǿ��� ���.
    ���̿��ڰ� ������� ���.
	
��12�� (��� ���� ����)
  1. ����� �� ������� ����ϴ� ����� ���� ������ �ϴµ� �־ �ľ��� �̿��� �� ��        ������ ���� ����� ������ ���� ���� �����ڿ��� �긮�� �ʽ��ϴ�. �� ����� ��ų �ǹ�        �� ��� ���� �ĵ�  �����ϴ�.
  2. ����ڴ� �̿��� �� �� ������ ������ ������ �ذ��ؾ� �� ���� � ���� ���� �����        ȸ�ǿ� ���� ������ �����ϱ� ���ؼ� �̿��� �� ������ ���������� ���� ����� ȸ�ǿ�      �� �̿��ϴ� ���� �� ����� ������ ���Ƿ� �����մϴ�.

��13�� (���å��)
  ����ڴ� ������ ������ ���� ������� ������ ������ ���� �̿����� ������ü����꿡 ���ظ� ������ ���� �̿��ڿ� ���ؼ� �� ���ظ� ����մϴ�.

��14�� (��޽��� ����)
  ����ڴ� ������ �湮��ȣ�� ������ �ǽ��ϰ� ���� �� �̿����� ������ �޺��� ������ ��� �� �� �ʿ��� ���� �ż��ϰ� ��ġ�� �ǻ� �Ǵ� ġ�� �ǻ翡�� ������ �ϴ� �� �ʿ��� ��ġ�� �����մϴ�.

��15�� (�ź��� �޴� �ǹ�)
  ���� �������� �׻� �ź����� �޴� �� ù ȸ �湮�� �� �̿��� �Ǵ� �̿����� �������κ��� ���ð� �䱸�Ǿ��� ���� ������ �ź����� �����մϴ�.");
	
	$pdf->MY_ADDPAGE();
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"��16�� (����)
  ����ڴ� �湮��ȣ�� ������ ������������� �� �����Ƿ� ���� �Ǵ� �������񽺸� �����ϴ� ������� ������ ���޿� ��� �մϴ�.

��17�� (��㡤���� ����)
  ����ڴ� �̿��ڷκ����� ���, ���� � �����ϴ� â���� ��ġ�� �湮 ��ȣ�� ���� �̿����� ���, ���� ���� �ż��� �����մϴ�.

��18�� (�� ��࿡ ������ ���� ����)
  1. �̿��� �� ����ڴ� ���Ǽ����� ������ �� ����� �����ϴ� ������ �մϴ�.
  2. �� ��࿡ ������ ���� ���׿� ���ؼ��� ��纸�� ���� �� �� �� ������ ���ϴ� ���� ������ �ֹ��� ���Ǹ� ���� ���� �� ���մϴ�.

��19�� (���ǰ���)
  �� ��࿡ ���ؼ� ��¿�� ���� �Ҽ��� �Ǵ� ��쿡 �̿��ڿ� ����ڴ� ������� �ּ����� �����ϴ� ���ǼҸ� ������ �������Ǽҷ� �ϴ� �Ϳ� �̸� �����մϴ�.

  ����� ����� �����ϱ� ���Ͽ� ���� 2���� �ۼ��ϰ� �̿��� �� ����ڰ� ���� ������ ��  ���� 1�뾿 �����ϴ� ������ �մϴ�.");

	$pdf->SetXY($pdf->width*0.38, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, '���ü����' , 0, 0, "R");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($ct['reg_dt'],0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($ct['reg_dt'],5,2) < 10 ? substr($ct['reg_dt'],6,1) : substr($ct['reg_dt'],5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($ct['reg_dt'],8,2) < 10 ? substr($ct['reg_dt'],9,1) : substr($ct['reg_dt'],8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 1, "L");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "[�̿���]", 0, 1, "R");
	
	
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "�� ��:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.8, $pdf->row_height, '  '.$su['juso'], 0, 1, "L");
	
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 80, $pdf->getY(), '20', '20');	//�� ����
	}

	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "�� ��:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, '  '.$su['name'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "(��)", 0, 1, "L");

	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"���� ������
      ���� ������ ��� �ǻ縦 Ȯ���� ���� �����Ͽ����ϴ�.");
	
	$pdf->SetFont($pdf->font_name_kor, "", $pdf->font_size);
	$pdf->SetXY($pdf->left+10, $pdf->GetY()+5);
	$pdf->MultiCell($pdf->width*0.9, $pdf->row_height-0.5,"�̿��ڿ��� ����      
*���ǣ���Ģ���μ� �ξ��ڷ� �մϴ�.");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "[�����]", 0, 1, "R");
	
	
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "�����:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, '  '.$center['centerName'], 0, 1, "L");
	
	if($jikin != ''){
		$exp = explode('.',$jikin);
		$exp = strtolower($exp[sizeof($exp)-1]);
		if($exp != 'bmp'){
			$pdf->Image('../mem_picture/'.$jikin, 77, 248, '20', '20');	//��� ����
		}
	}


	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "������:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.8, $pdf->row_height, '  '.$center['address'], 0, 1, "L");

	$pdf->SetXY($pdf->left+7, $pdf->GetY()+5);
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, "�� ǥ:", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, '  '.$center['manager'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, "(��)", 0, 1, "L");
	
	
	# �̿��༭
	
	$pdf->MY_ADDPAGE();
	
	if(file_exists($file) and is_file($file)){
		$pdf->Image($file, 175, $pdf->getY()+200, '20', '20');	//�� ����
	}

	$pdf->SetXY($pdf->left+5, $pdf->top+9);
	$pdf->SetFont('����','',11);
	$pdf->Cell(150,5,'[���� ��5ȣ����]',0,1,'L');

	$pdf->SetXY($pdf->left, $pdf->top);
	$pdf->SetLineWidth(0.6);
	//$pdf->SetFillColor('255');
	$pdf->Rect($pdf->left+5, $pdf->top+15, $pdf->width-10, $pdf->height-45);
	$pdf->SetLineWidth(0.2);
	
	$pdf->SetFont($pdf->font_name_kor, "B", 18);
	$pdf->SetXY($pdf->left, $pdf->top+25);
	$pdf->Cell($pdf->width, $pdf->row_height * 20 / $pdf->font_size, "�������� ���� �� Ȱ�� ���Ǽ�", 0, 1, "C");
	
	$pdf->SetXY($pdf->left+5, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height, "�� ��:", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.13, $pdf->row_height, $su['name'], 0, 0, "L");
	$pdf->Cell($pdf->width * 0.45, $pdf->row_height, "(������� :    ".$myF->issToBirthday($su['jumin'],'.'), 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, ")", 0, 1, "C");
	
	$pdf->SetX($pdf->left+5);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.12, $pdf->row_height+3, "�� ��:", 0, 0, "C");
	$pdf->Cell($pdf->width * 0.88, $pdf->row_height+3, $su['juso'], 0, 0, "L");
	
	$pdf->SetXY($pdf->left+15, $pdf->GetY()+15);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "1. �����������", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "�� �����޿� ���� ����
�� �̿����� �������� ���� ����
�� ���ñ�� �������� ��û�� �ʿ��� ����
�� ��Ÿ ������� ���࿡ �ʿ��� ����", 0, "L");
	
	$pdf->SetXY($pdf->left+15, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 12);
	$pdf->MultiCell($pdf->width, 6, "2. ���������� Ȱ�����", 0, "L");

	$pdf->SetXY($pdf->left+20, $pdf->GetY());
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "�� ����� �޿� ���ÿ� �ʿ��� ������ Ȱ��
�� ������� ���� ���� ����� ���û��׿� ���� ����� ���� ����
�� ���ñ�� �������� ��û�� ����
�� ������ȹ, �屸����, �����缭�� �� ���� ��� � Ȱ��", 0, "L");

	$pdf->SetXY($pdf->left+10, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->MultiCell($pdf->width, 6, "��� ������ ���������� �����ϰ� Ȱ���ϴ� �Ϳ� �����մϴ�.", 0, "L");
	
	$pdf->SetXY($pdf->width*0.43, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "", 12);
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, substr($ct['reg_dt'],0,4), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, substr($ct['reg_dt'],5,2) < 10 ? substr($ct['reg_dt'],6,1) : substr($ct['reg_dt'],5,2), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 0, "L");
	$pdf->Cell($pdf->width * 0.06, $pdf->row_height, (substr($ct['reg_dt'],8,2) < 10 ? substr($ct['reg_dt'],9,1) : substr($ct['reg_dt'],8,2)), 0, 0, "R");
	$pdf->Cell($pdf->width * 0.03, $pdf->row_height, "��", 0, 1, "L");
	
	$pdf->SetXY($pdf->left, $pdf->GetY()+30);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, "�� �� �� :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $su['name'], 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, " (��)", 0, 0, "L");

	$pdf->SetXY($pdf->left, $pdf->GetY()+10);
	$pdf->SetFont($pdf->font_name_kor, "B", 13);
	$pdf->Cell($pdf->width * 0.7, $pdf->row_height, "�� ȣ �� :", 0, 0, "R");
	$pdf->Cell($pdf->width * 0.2, $pdf->row_height, $su['bohoName'], 0, 0, "C");
	$pdf->Cell($pdf->width * 0.1, $pdf->row_height, " (��)", 0, 0, "L");


	$pdf->Output();

	include('../inc/_db_close.php');
	
?>
<script>self.focus();</script>
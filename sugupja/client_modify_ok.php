<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = '';
	$change_jumin = '';
	$conn->begin();
	
	//����������
	$sql = 'update m03sugupja
			   set m03_jumin = \''.$change_jumin.'\'
			 where m03_ccode = \''.$code.'\'
			   and m03_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '1';
		 exit;
	}

	//���񽺰�೻��
	$sql = 'update client_his_svc
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '2';
		 exit;
	}

	//����޳���
	$sql = 'update client_his_lvl
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '3';
		 exit;
	}

	//�����ڱ��г���
	$sql = 'update client_his_kind
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '4';
		 exit;
	}

	//û���ѵ�����
	$sql = 'update client_his_limit
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '5';
		 exit;
	}

	//���簣��
	$sql = 'update client_his_nurse
		       set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '6';
		 exit;
	}

	//���ε���
	$sql = 'update client_his_old
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '7';
		 exit;
	}

	//���Ż���
	$sql = 'update client_his_baby
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '8';
		 exit;
	}

	//�����Ȱ������
	$sql = 'update client_his_dis
			   set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '9';
		 exit;
	}

	//��Ÿ����
	$sql = 'update client_his_other
	           set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '10';
		 exit;
	}

	//��õ��
	$sql = 'update client_recom
			   set cr_jumin    = \''.$change_jumin.'\'
			 where org_no   = \''.$code.'\'
			   and cr_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '11';
		 exit;
	}

	//����߰��޿�
	$sql = 'update client_svc_addpay
			   set jumin  = \''.$change_jumin.'\'
			 where org_no  = \''.$code.'\'
			   and svc_ssn = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '12';
		 exit;
	}

	//����
	$sql = 'update pattern
	           set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '13';
		 exit;
	}

	//������纸ȣ��
	$sql = 'update client_family
			   set jumin  = \''.$change_jumin.'\'
			 where org_no   = \''.$code.'\'
			   and cf_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '14';
		 exit;
	}

	//�ɼ�
	$sql = 'update client_option
	           set jumin  = \''.$change_jumin.'\'
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '15';
		 exit;
	}
	
	//����
	$sql = 'update t01iljung
			   set t01_jumin = \''.$change_jumin.'\'
			 where t01_ccode = \''.$code.'\'
			   and t01_jumin = \''.$jumin.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo '1';
		 exit;
	}

	$conn->commit();

	echo 'Y';

	include_once('../inc/_db_close.php');
?>
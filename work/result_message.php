<?
	include_once('../inc/_db_open.php');

	$code	= $_REQUEST['code'];
	$year	= $_REQUEST['year'];
	$month	= ($_REQUEST['month'] < 10 ? '0' : '').intval($_REQUEST['month']);
	$gubun	= $_REQUEST['gubun'];

	$sql = "select closing_dt_f
			,      closing_dt_t
			,      closing_rst
			,      closing_msg
			  from closing_result
			 where org_no          = '$code'
			   and closing_yymm    = '$year$month'
			   and closing_gbn     = '$gubun'
			   and closing_read_yn = 'N'";

	$conf = $conn->get_array($sql);

	if (is_array($conf)){
		$result  = '������ ���� �ϰ�Ȯ�� ó������\n';
		$result .= '�����Ͻ� : '.$conf['closing_dt_f'];
		$result .= '�����Ͻ� : '.$conf['closing_dt_t'];
		$result .= '������� : '.$conf['closing_msg'];
		$result .= '';

		$conn->begin();
		$sql = "update closing_result
				   set closing_read_yn = 'Y'
				 where org_no          = '$code'
				   and closing_yymm    = '$year$month'
				   and closing_gbn     = '$gubun'
				   and closing_read_yn = 'N'";
		$conn->execute($sql);
		$conn->commit();
	}else{
		$result = 'no data';
	}

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$result;

	include_once('../inc/_db_close.php');
?>
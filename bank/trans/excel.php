<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->set_name('euckr');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$code = $_REQUEST['code'];

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=".$code."_".Date("YmdHis").".xls" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );
?>
<table cellpadding="1" cellspacing="1" border="1">
	<tr style="height:35px;">
		<td style="text-align:center;">No</td>
		<td style="text-align:center;">은행코드</td>
		<td style="text-align:center;">계좌번호</td>
		<td style="text-align:center;">금액</td>
		<td style="text-align:center;">예금주</td>
	</tr><?
	$sql = 'SELECT yymm
			,      jumin
			,      seq
			,      type
			,      bank_nm
			,      bank_no
			,      bank_acct
			,      amt
			,      request_dt AS dt
			  FROM trans
			 WHERE org_no = \''.$code.'\'
			   AND stat   = \'1\'
			 ORDER BY request_dt DESC, bank_acct';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td style="width:40px; text-align:center;"><?=$i+1;?></td>
				<td style="width:70px; text-align:center; mso-number-format:'\@';"><?=$row['bank_nm'];?></td>
				<td style="width:150px; text-align:left;"><?=$row['bank_no'];?></td>
				<td style="width:100px; text-align:right;"><?=Number_Format($row['amt']);?></td>
				<td style="width:70px; text-align:left;"><?=$row['bank_acct'];?></td>
			</tr><?
		}
	}

	$conn->row_free();
	?>
</table>
<?
	include_once('../inc/_db_close.php');
?>
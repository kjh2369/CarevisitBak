<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	$taxCrGbn = Array('C'=>'청구', 'R'=>'영수');

	$sql = 'SELECT	DISTINCT a.org_no, m00_store_nm AS org_nm, m00_mname AS mg_nm, a.acct_ym, a.iss_dt, a.cr_gbn
			FROM	cv_tax_his AS a
			INNER	JOIN	m00center
					ON		m00_mcode	= a.org_no
					AND		m00_domain	= \''.$company.'\'
			WHERE	a.acct_ym = \''.$yymm.'\'
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	if ($rowCnt > 0){
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td><div class="left"><?=$row['org_nm'];?></div></td>
				<td><div class="left"><?=$row['org_no'];?></div></td>
				<td><div class="left"><?=$row['mg_nm'];?></div></td>
				<td class="center"><?=$myF->dateStyle($row['iss_dt'],'.');?></td>
				<td class="center"><?=$taxCrGbn[$row['cr_gbn']];?></td>
				<td class="last">
					<div class="left">
						<span class="btn_pack small"><button onclick="lfTaxReg('<?=$row['org_no'];?>');" style="color:BLUE;">수정</button></span>
						<span class="btn_pack small"><button onclick="lfTaxDel('<?=$row['org_no'];?>');" style="color:RED;">삭제</button></span>
					</div>
				</td>
			</tr><?
		}
	}else{?>
		<tr>
			<td class="center last" colspan="7">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
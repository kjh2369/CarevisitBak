<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	

	$code = $_SESSION['userCenterCode'];
	$name = $conn->_storeName($code);

	if ($_GET['gubun'] == 'I'){
		$field = 'income';
		$title = '수입';
	}else{
		$field = 'outgo';
		$title = '지출';
	}
	
	if (!Empty($_GET['docNo'])){
		$sql = 'SELECT '.$field.'_acct_dt
				,      '.$field.'_item_cd
				,      '.$field.'_item
				,      '.$field.'_amt
				,      '.$field.'_vat
				,      proof_year
				,      proof_no
				  FROM center_'.$field.'
				 WHERE org_no = \''.$code.'\'
				   AND CONCAT(DATE_FORMAT('.$field.'_acct_dt,\'%Y%m%d\'),proof_no) = \''.$_GET['docNo'].'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if (Empty($tmp[''.$field.'_acct_dt'])){
				$tmp[''.$field.'_acct_dt'] = $row[''.$field.'_acct_dt'];
			}

			if (Empty($tmp[''.$field.'_item_cd'])){
				$tmp[''.$field.'_item_cd'] = $row[''.$field.'_item_cd'];
			}

			if (Empty($tmp['proof_year'])){
				$tmp['proof_year'] = $row['proof_year'];
			}

			if (Empty($tmp['proof_no'])){
				$tmp['proof_no'] = $row['proof_no'];
			}

			$tmp[''.$field.'_amt'] += $row[''.$field.'_amt'];
			$tmp[''.$field.'_vat'] += $row[''.$field.'_vat'];
			$tmp[''.$field.'_item'] .= (!Empty($tmp[''.$field.'_item']) ? ',' : '').$row[''.$field.'_item'];
		}

		$conn->row_free();

		$row = $tmp;
		$row['cnt'] = $rowCount;
		UnSet($tmp);
	}else{
		$sql = 'SELECT *
				  FROM center_'.$field.'
				 WHERE org_no = \''.$code.'\'
				   AND '.$field.'_ent_dt = \''.$_GET['entDt'].'\'
				   AND '.$field.'_seq    = \''.$_GET['seq'].'\'';
		
		$row = $conn->get_array($sql);
	}
	
	$sql = 'SELECT cd1 AS cd
			,      nm1 AS nm
			  FROM ie_category
			 WHERE gbn = \''.$_GET['gubun'].'\'
			   AND cd1 = \''.SubStr($row[$field.'_item_cd'],0,2).'\'
			 LIMIT 1';
	$cate1 = $conn->get_array($sql);

	$sql = 'SELECT cd2 AS cd
			,      nm2 AS nm
			  FROM ie_category
			 WHERE gbn = \''.$_GET['gubun'].'\'
			   AND cd2 = \''.SubStr($row[$field.'_item_cd'],2,2).'\'
			 LIMIT 1';
	$cate2 = $conn->get_array($sql);

	$sql = 'SELECT cd3 AS cd
			,      nm3 AS nm
			  FROM ie_category
			 WHERE gbn = \''.$_GET['gubun'].'\'
			   AND cd3 = \''.SubStr($row[$field.'_item_cd'],4,3).'\'';
	$cate3 = $conn->get_array($sql);
	


	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Description: test" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );
	
?>	

	<table>
	
		<tr>
			<th colspan="8" rowspan="2" style="font-size:20pt; font-weight:bold;"><?=$title?>결의서</th>
			<td rowspan="2" style="width:45px; border:0.5pt solid #000; text-align:center;">결<br/>제</td>
			<td style="height:25px; border:0.5pt solid #000; text-align:center;">담당</td>
			<td style="height:25px; border:0.5pt solid #000; text-align:center;">부장</td>
			<td style="height:25px; border:0.5pt solid #000; text-align:center;">센터장</td>
		</tr>
		<tr>
			<td style="width:60px; height:55px; border:0.5pt solid #000;"></td>
			<td style="width:60px; height:55px; border:0.5pt solid #000;"></td>
			<td style="width:60px; height:55px; border:0.5pt solid #000;"></td>
		</tr>
		<tr><td colspan="12"></td></tr>
		<tr>
			<td colspan="5" style="height:35px; border:0.5pt solid #000;">결의서 번호: <?=str_replace('-','', $row[$field.'_acct_dt']).'-'.$row['proof_no']?></td>
			<td colspan="7" style="text-align:right;">년도 (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) 회계</td>
		</tr>
		<tr>
			<td colspan="4" style="height:35px; border:0.5pt solid #000; text-align:center;">세입과목</td>
			<td style="border:0.5pt solid #000;"></td>
			<td colspan="3" style="width:150px; border:0.5pt solid #000;"></td>
			<td style="border:0.5pt solid #000; text-align:center;">발의</td>
			<td colspan="3" style="border:0.5pt solid #000; text-align:center;"><?=$row[$field.'_acct_dt']?></td>
		</tr>
		<tr>
			<td style="height:35px; width:35px; border:0.5pt solid #000; text-align:center;">관</td>
			<td colspan="3" style="width:140px; border:0.5pt solid #000;  text-align:center;"><?=$cate1['nm']?></td>
			<td style="border:0.5pt solid #000; text-align:center;">지 출 원</td>
			<td colspan="3" style="width:150px; border:0.5pt solid #000; text-align:center;"></td>
			<td style="border:0.5pt solid #000; text-align:center;">결재</td>
			<td colspan="3" style="border:0.5pt solid #000; text-align:center;"><?=$row[$field.'_acct_dt']?></td>
		</tr>
		<tr>
			<td style="height:35px; border:0.5pt solid #000; text-align:center;">항</td>
			<td colspan="3" style="border:0.5pt solid #000;  text-align:center;"><?=$cate2['nm']?></td>
			<td style="border:0.5pt solid #000; text-align:center;">사&nbsp;&nbsp;&nbsp; 업</td>
			<td colspan="3" style="border:0.5pt solid #000; text-align:center;"><?=$cate3['nm']?></td>
			<td style="border:0.5pt solid #000; text-align:center;">출납</td>
			<td colspan="3" style="border:0.5pt solid #000; text-align:center;"><?=$row[$field.'_acct_dt']?></td>
		</tr>
		<tr>
			<td style="height:35px; border:0.5pt solid #000; text-align:center;">목</td>
			<td colspan="3" style="border:0.5pt solid #000;  text-align:center;"><?=$cate3['nm']?></td>
			<td style="border:0.5pt solid #000; text-align:center;">작 성 자</td>
			<td colspan="3" style="border:0.5pt solid #000; text-align:center;"></td>
			<td style="border:0.5pt solid #000; text-align:center;">등기</td>
			<td colspan="3" style="border:0.5pt solid #000; text-align:center;"><?=$row[$field.'_acct_dt']?></td>
		</tr>
		<tr>
			<td style="height:35px; border:0.5pt solid #000; text-align:center;">No</td>
			<td colspan="2" style="width:110px; border:0.5pt solid #000; text-align:center;">계정과목(세목)</td>
			<td colspan="3" style="width:165px; border:0.5pt solid #000; text-align:center;">적요(거래처)</td>
			<td colspan="2" style="border:0.5pt solid #000; text-align:center;">금액</td>
			<td colspan="2" style="border:0.5pt solid #000; text-align:center;">자금원천</td>
			<td colspan="2" style="border:0.5pt solid #000; text-align:center;">상대계정</td>
		</tr>
		<tr>
			<td style="height:35px; border:0.5pt solid #000; text-align:center;">1</td>
			<td colspan="2" style="width:110px; border:0.5pt solid #000;"><?=$cate3['nm'];?></td>
			<td colspan="3" style="border:0.5pt solid #000;"><?=nl2br($row[$field.'_item']);?></td>
			<td colspan="2" style="border:0.5pt solid #000; text-align:right;"><?=Number_Format($row[$field.'_amt']+$row[$field.'_vat'])?></td>
			<td colspan="2" style="border:0.5pt solid #000;"></td>
			<td colspan="2" style="border:0.5pt solid #000;"></td>
		</tr><?
		for($i=0; $i<9; $i++){ ?>
			<tr>
				<td style="height:35px; border:0.5pt solid #000; text-align:center;"></td>
				<td colspan="2" style="width:110px; border:0.5pt solid #000;"></td>
				<td colspan="3" style="border:0.5pt solid #000;"></td>
				<td colspan="2" style="border:0.5pt solid #000; text-align:right;"></td>
				<td colspan="2" style="border:0.5pt solid #000;"></td>
				<td colspan="2" style="border:0.5pt solid #000;"></td>
			</tr><?
		} ?>		
		<tr>
			<td colspan="2" style="height:35px; border:0.5pt solid #000; text-align:center;">금액</td>
			<td colspan="10" style="border:0.5pt solid #000; text-align:center;">금<?=Number_Format($row[$field.'_amt']+$row[$field.'_vat'])?>원 (<?=$myF->no2Kor($row[$field.'_amt']+$row[$field.'_vat'])?>원)</td>
		</tr>
		<tr>
			<td colspan="2" style="border:0.5pt solid #000; text-align:center;">비고</td>
			<td colspan="10" style="height:210px; border:0.5pt solid #000;"><?=nl2br($row['other']);?></td>
		</tr>
	</table>
	
	
<?
	include_once('../inc/_db_close.php');
?>
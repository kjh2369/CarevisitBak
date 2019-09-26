<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$company= $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	$orgNo	= $_POST['orgNo'];
	$orgNm	= $_POST['orgNm'];
	$orgMg	= $_POST['orgMg'];

	$ContCom = Array('1'=>'굿이오스','2'=>'지케어','3'=>'케어비지트');

	if ($yymm >= '201207'){

		$sql = 'SELECT	org_nm, org_no, mg_nm, cnt, acct_gbn, cont_com, cms_no
				,		CASE WHEN cnt > 300 THEN cnt - 300 ELSE 0 END AS add_cnt
				FROM	(
						SELECT	DISTINCT m00_store_nm AS org_nm, a.org_no, m00_mname AS mg_nm, cnt, cms.acct_gbn, cms.cont_com, cms.cms_no
						FROM	(';

		if ($yymm >= '201506'){
			$sql .= '			SELECT	org_no, SUM(CASE WHEN IFNULL(sms_type, \'SMS\') = \'LSM\' THEN 2 ElSE 1 END) AS cnt
								FROM	sms_his
								WHERE	DATE_FORMAT(insert_dt, \'%Y%m\') = \''.$yymm.'\'
								GROUP	BY org_no';
		}else{
			$sql .= '			SELECT	org_no, COUNT(sms_seq) AS cnt
								FROM	sms_'.$yymm.'
								GROUP	BY org_no';
		}

		$sql .= '				) AS a
						INNER	JOIN m00center
								ON   m00_mcode = a.org_no';

		if($company){
			$sql .= '   AND		m00_domain = \''.$company.'\'';
		}

		$sql .= '		LEFT    JOIN cv_reg_info as cms
						        ON cms.org_no = a.org_no
						AND left(cms.from_dt, 6) <= \''.$yymm.'\'
						AND left(cms.to_dt, 6) >= \''.$yymm.'\'
						) AS a
				WHERE	org_no != \'\'';

		if ($orgNo){
			$sql .= '
				AND		org_no LIKE \''.$orgNo.'%\'';
		}

		if ($orgNm){
			$sql .= '
				AND		org_nm LIKE \'%'.$orgNm.'%\'';
		}

		if ($orgMg){
			$sql .= '
				AND		mg_nm LIKE \'%'.$orgMg.'%\'';
		}

		$sql .= '
				ORDER	BY org_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		if($rowCnt > 0){
			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				//청구구분 CMS 여부
				if($row['acct_gbn'] == 1){
					$cmsNo = $row['cms_no'];
					$cmsCom = $ContCom[$row['cont_com']];
				}else {
					$cmsNo = '';
					$cmsCom = '';
				}

				$tot_cnt += $row['cnt'];				//총사용건수
				$tot_basic += 5500;						//총기본금액
				$tot_add_cnt += $row['add_cnt'];		//총추가건수
				$tot_add_pay += ($row['add_cnt']*22);	//총추가금액
				$tot_pay += ($row['add_cnt']*22+5500);  //총합계금액

				$html .= '<tr>
							<td class="center" style="text-align:center;">'.$no.'</td>
							<td class="">&nbsp;'.$row['org_nm'].'</td>
							<td class="" style="mso-number-format:\'\@\'">&nbsp;'.$row['org_no'].'</td>
							<td class="" style="mso-number-format:\'\@\'">&nbsp;'.$cmsNo.'</td>
							<td class="">&nbsp;'.$cmsCom.'</td>
							<td class=""><div class="nowrap" style="width:60px;">&nbsp;'.$row['mg_nm'].'</div></td>
							<td style="text-align:right;">'.number_format($row['cnt']).'&nbsp;</td>
							<td style="text-align:right;">5,500&nbsp;</td>
							<td style="text-align:right;">'.number_format($row['add_cnt']).'&nbsp;</td>
							<td style="text-align:right;">'.number_format($row['add_cnt'] * 22).'&nbsp;</td>
							<td style="text-align:right;">'.number_format($row['add_cnt'] * 22 + 5500).'&nbsp;</td>
							<td class="last"></td>
						</tr>';

				$no ++;
			}

			$conn->row_free();

			$tot_html = '<tr>
							<td class="right" colspan="6">합 계&nbsp;&nbsp;</td>
							<td style="text-align:right;">'.number_format($tot_cnt).'&nbsp;</td>
							<td style="text-align:right;">'.number_format($tot_basic).'&nbsp;</td>
							<td style="text-align:right;">'.number_format($tot_add_cnt).'&nbsp;</td>
							<td style="text-align:right;">'.number_format($tot_add_pay).'&nbsp;</td>
							<td style="text-align:right;">'.number_format($tot_pay).'&nbsp;</td>
							<td class="last"></td>
						</tr>';

			if ($IsExcel){
				$html = str_replace('&nbsp;','',$html);
				$tot_html = str_replace('&nbsp;','',$tot_html);
			}

			echo $tot_html;
			echo $html;

		}else {
			echo '<tr>
					<td class="center last" colspan="9">::검색된 데이타가 없습니다.::</td>
				</tr>';
		}

	}else{?>
		<tr>
			<td class="center last" colspan="9">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	//기관리스트
		$sql = 'SELECT	DISTINCT
						m00_mcode AS org_no
				,		m00_store_nm AS org_nm
				,		m00_mname AS mg_nm
				FROM	m00center
				ORDER	BY org_nm';
		$data = $conn->_fetch_array($sql, 'org_no');


	//청구총액
		$sql = 'SELECT	org_no, COUNT(DISTINCT yymm) AS cnt, SUM(acct_amt) AS acct_amt
				FROM	cv_svc_acct_list
				GROUP	BY org_no';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$orgNo = $row['org_no'];
			$data[$orgNo]['AMT']['acct'] = $row['acct_amt'];
			$data[$orgNo]['AMT']['months'] = $row['cnt'];
		}

		$conn->row_free();


	//CMS 입금총액
		$sql = 'SELECT	org_no, SUM(in_amt) AS in_amt
				FROM	cv_cms_reg
				WHERE	org_no	!= \'\'
				AND		del_flag = \'N\'
				GROUP	BY org_no';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$orgNo = $row['org_no'];
			$data[$orgNo]['AMT']['in'] = $row['in_amt'];
		}

		$conn->row_free();


	//무통장 입금총액
		$sql = 'SELECT	org_no, SUM(link_amt) AS in_amt
				FROM	cv_cms_link
				WHERE	link_stat IS NOT NULL
				AND		del_flag = \'N\'
				GROUP	BY org_no';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$orgNo = $row['org_no'];
			$data[$orgNo]['AMT']['in'] += $row['in_amt'];
		}

		$conn->row_free();


	//연결총액
		$sql = 'SELECT	org_no, SUM(link_amt) AS link_amt
				FROM	cv_cms_link
				WHERE	del_flag = \'N\'
				AND		IFNULL(link_stat,\'1\') = \'1\'
				GROUP	BY org_no';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$orgNo = $row['org_no'];
			$data[$orgNo]['AMT']['link'] = $row['link_amt'];
		}

		$conn->row_free();


	if (is_array($data)){
		foreach($data as $orgNo => $R){
			if (!$R['AMT'] || !$orgNo){
				Unset($data[$orgNo]);
				continue;
			}

			$data[$orgNo]['AMT']['nonpay'] = $R['AMT']['acct'] - $R['AMT']['link'];
			$data[$orgNo]['AMT']['unlink']= $R['AMT']['in'] - $R['AMT']['link'];
		}


		foreach($data as $orgNo => $R){?>
			<tr id="ID_ROW_<?=$orgNo;?>">
				<td class="center"><input id="chkOrg" type="checkbox" class="checkbox"></td>
				<td><div class="left"><?=$R['org_no'];?></div></td>
				<td><div class="left nowrap" style="width:150px;" title="<?=$R['org_nm'];?>"><?=$R['org_nm'];?></div></td>
				<td><div class="left"><?=$R['mg_nm'];?></div></td>
				<td><div class="right"><?=number_format($R['AMT']['acct']);?></div></td>
				<td><div class="right"><?=number_format($R['AMT']['in']);?></div></td>
				<td><div class="right"><?=number_format($R['AMT']['link']);?></div></td>
				<td><div class="right"><?=number_format($R['AMT']['nonpay']);?></div></td>
				<td><div class="right"><?=number_format($R['AMT']['unlink']);?></div></td>
				<td class="last">
					<div class="left">
						<span class="btn_pack small"><button>설정</button></span>
					</div>
				</td>
			</tr><?
		}
	}else{?>
		<tr>
			<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>
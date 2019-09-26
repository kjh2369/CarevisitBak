<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");

	$code      = $_POST['code'];
	$send_type = $_POST['send_type'];
	$val       = explode('//',$_POST['val']);
	$wsl       = '';

	for($i=0; $i<sizeof($val); $i++){
		if (!empty($val[$i])){
			$var = explode('/',$val[$i]);

			$str = '';
			for($j=0; $j<sizeof($var); $j++){
				$str .= (!empty($str) ? '_' : '').$var[$j];
			}

			$wsl .= (!empty($wsl) ? ',' : '').'\''.$str.'\'';
		}
	}

	switch($send_type){
		case 'all':
			$colgroup = '<col>';
			$thead    = '<th class=\'bold\'>전체</th>';
			break;

		case 'branch':
			$colgroup = '<col width=\'80px\'>
						 <col width=\'130px\'>
						 <col width=\'80px\'>
						 <col>';
			$thead = '<th class=\'head bold\'>지사기호</th>
					  <th class=\'head bold\'>지사명</th>
					  <th class=\'head bold\'>대표자명</th>
					  <th class=\'head bold\'>비고</th>';
			break;

		case 'center':
			$colgroup = '<col width=\'80px\'>
						 <col width=\'130px\'>
						 <col width=\'80px\'>
						 <col>';
			$thead = '<th class=\'head bold\'>가맹점기호</th>
					  <th class=\'head bold\'>가맹점명</th>
					  <th class=\'head bold\'>대표자명</th>
					  <th class=\'head bold\'>비고</th>';
			break;

		case 'dept':
			$colgroup = '<col width=\'80px\'>
						 <col width=\'130px\'>
						 <col>';
			$thead = '<th class=\'head bold\'>부서코드</th>
					  <th class=\'head bold\'>부서명</th>
					  <th class=\'head bold\'>비고</th>';
			break;

		case 'person':
			$colgroup = '<col width=\'60px\'>
						 <col width=\'100px\'>
						 <col width=\'80px\'>
						 <col width=\'100px\'>
						 <col width=\'100px\'>
						 <col width=\'80px\'>
						 <col>';
			$thead = '<th class=\'head bold\'>No</th>
					  <th class=\'head bold\'>부서</th>
					  <th class=\'head bold\'>사번</th>
					  <th class=\'head bold\'>직원명</th>
					  <th class=\'head bold\'>연락처</th>
					  <th class=\'head bold\'>입사일</th>
					  <th class=\'head bold\'>비고</th>';
			break;
	}
?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead><?=$thead;?></thead>
	<?
		if ($send_type != 'all'){?>
			<tbody>
				<tr>
					<td colspan="10" style="height:100px;">
						<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;">
							<table id="body_list" class="my_table" style="width:100%;">
								<colgroup><?=$colgroup;?></colgroup>
								<tbody>
								<?
									if ($wsl != ''){
										switch($send_type){
											case 'branch':
												$sql = "select b00_code as cd, b00_name as nm, b00_manager as man
														  from b00branch
														 where b00_code  in ($wsl)
														   and b00_domain = '$gDomain'
														   and b00_com_yn = 'N'";
												break;

											case 'center':
												$sql = "select m00_mcode as cd, m00_mkind as kind, m00_cname as nm, m00_mname as man
														  from m00center
														 where m00_mcode in ($wsl)
														   and m00_domain = '$gDomain'
														   and m00_del_yn = 'N'
														 group by m00_mcode";
												break;

											case 'dept':
												$sql = "select org_no as no, dept_cd as cd, dept_nm as nm
														  from dept
														 where concat(org_no, '_', dept_cd) in ($wsl)
														   and del_flag = 'N'";
												break;

											case 'person':
												$sql = "select m02_ccode, min(m02_mkind), dept_nm, m02_mem_no, m02_yname, m02_ytel, m02_yipsail
														  from m02yoyangsa
														  left join dept
															on dept.org_no  = m02_ccode
														   and dept.dept_cd = m02_dept_cd
														 where concat(m02_ccode, '_', m02_key) in ($wsl)
														   and m02_del_yn  = 'N'
														 group by m02_ccode, m02_yjumin";
												break;
										}

										$conn->fetch_type = 'assoc';
										$conn->query($sql);
										$conn->fetch();

										$row_count = $conn->row_count();

										for($i=0; $i<$row_count; $i++){
											$row = $conn->select_row($i);

											if ($send_type == 'branch' || $send_type == 'center'){
												echo '<tr id=\'body_list_tr_'.$row['cd'].'\'>
														<td class=\'center\'><div class=\'center\'>'.$row['cd'].'</div></td>
														<td class=\'center\'><div class=\'left\'>'.$row['nm'].'</div></td>
														<td class=\'center\'><div class=\'left\'>'.$row['man'].'</div></td>
														<td class=\'center\'><div class=\'left\'>
															<a href=\'#\' onclick=\'delete_list("body_list","body_list_tr_'.$row['cd'].'");\'>삭제</a>
														</div></td>
													  </tr>';

												echo '<input name=\''.$send_type.'_cd[]\' type=\'hidden\' value=\''.$row['cd'].'\'>';
												echo '<input name=\''.$send_type.'_nm[]\' type=\'hidden\' value=\''.$row['nm'].'\'>';

											}else if ($send_type == 'dept'){
												echo '<tr id=\'body_list_tr_'.$row['cd'].'\'>
														<td class=\'center\'><div class=\'center\'>'.$row['cd'].'</div></td>
														<td class=\'center\'><div class=\'left\'>'.$row['nm'].'</div></td>
														<td class=\'center\'><div class=\'left\'>
															<a href=\'#\' onclick=\'delete_list("body_list","body_list_tr_'.$row['cd'].'");\'>삭제</a>
														</div></td>
													  </tr>';

												echo '<input name=\''.$send_type.'_no[]\' type=\'hidden\' value=\''.$row['no'].'\'>';
												echo '<input name=\''.$send_type.'_cd[]\' type=\'hidden\' value=\''.$row['cd'].'\'>';
												echo '<input name=\''.$send_type.'_nm[]\' type=\'hidden\' value=\''.$row['nm'].'\'>';

											}else if ($send_type == 'person'){
												echo '<tr id=\'body_list_tr_'.$row['dept_cd'].'\'>
														<td class=\'center\'><div class=\'center\'>'.($i+1).'</div></td>
														<td class=\'center\'><div class=\'center\'>'.$row['dept_nm'].'</div></td>
														<td class=\'center\'><div class=\'center\'>'.$myF->formatString($row['m02_mem_no'],'####-####').'</div></td>
														<td class=\'center\'><div class=\'left\'>'.$row['m02_yname'].'</div></td>
														<td class=\'center\'><div class=\'left\'>'.$myF->phoneStyle($row['m02_ytel'],'.').'</div></td>
														<td class=\'center\'><div class=\'center\'>'.$myF->dateStyle($row['m02_yipsail'],'.').'</div></td>
														<td class=\'center\'><div class=\'left\'>
															<a href=\'#\' onclick=\'delete_list("body_list","body_list_tr_'.$row['dept_cd'].'");\'>삭제</a>
														</div></td>
													  </tr>';

												echo '<input name=\''.$send_type.'_no[]\' type=\'hidden\' value=\''.$row['m02_ccode'].'\'>';
												echo '<input name=\''.$send_type.'_cd[]\' type=\'hidden\' value=\''.$row['m02_mem_no'].'\'>';
												echo '<input name=\''.$send_type.'_nm[]\' type=\'hidden\' value=\''.$row['m02_yname'].'\'>';

											}else{
												echo '<tr id=\'body_list_tr_'.$row['cd'].'\'>
														<td class=\'center\'><div class=\'center\'>'.$row['cd'].'</div></td>
														<td class=\'center\'><div class=\'left\'>'.$row['nm'].'</div></td>
														<td class=\'center\'><div class=\'left\'>
															<a href=\'#\' onclick=\'delete_list("body_list","body_list_tr_'.$row['cd'].'");\'>삭제</a>
														</div></td>
													  </tr>';

												echo '<input name=\''.$send_type.'_cd[]\' type=\'hidden\' value=\''.$row['cd'].'\'>';
												echo '<input name=\''.$send_type.'_nm[]\' type=\'hidden\' value=\''.$row['nm'].'\'>';
											}
										}

										$conn->row_free();
									}
								?>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</tbody><?
		}
	?>
</table>
<?
	include_once("../inc/_footer.php");
?>
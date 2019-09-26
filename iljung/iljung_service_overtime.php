<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("iljung_config.php");

	ob_start();

	##########################################################
	#
	# 바우처 생성
	#
	##########################################################

	$code      = $_POST['code'];
	$svc_id    = $_POST['svc_id'];
	$jumin     = $ed->de($_POST['jumin']);
	$year      = $_POST['year'];
	$month     = $_POST['month'];
	$month     = (intval($month) < 10 ? '0' : '').intval($month);
	$kind_list = $conn->kind_list($code, true);
	$svc_cd    = $conn->kind_code($kind_list, $svc_id);
	$seq       = $_POST['seq'];
	$onload	   = $_POST['onload'];

	##########################################################
	#
	# 생성된 바우처가 있다면 이월시간 등록을 막는다.
	#
		$sql = "select count(*)
				  from voucher_make
				 where org_no        = '$code'
				   and voucher_kind  = '$svc_cd'
				   and voucher_jumin = '$jumin'
				   and voucher_yymm  > '$year$month'
				   and del_flag      = 'N'";

		$chk_cnt = $conn->get_data($sql);
	#
	##########################################################

	if (empty($onload)) $onload = 2;

	if (empty($seq)){
		$sql = "select voucher_seq
				  from voucher_make
				 where org_no        = '$code'
				   and voucher_kind  = '$svc_cd'
				   and voucher_jumin = '$jumin'
				   and voucher_yymm  = '$year$month'
				   and del_flag      = 'N'";

		$seq = $conn->get_data($sql);

		if (empty($seq)) $seq = 0;
	}

	if ($seq == 0)
		$mode = 1;
	else
		$mode = 2;

	if ($mode == 2){
		$sql = "select voucher_month_time as overtime
				  from voucher_make
				 where org_no        = '$code'
				   and voucher_kind  = '$svc_cd'
				   and voucher_jumin = '$jumin'
				   and voucher_yymm  = '$year$month'
				   and voucher_seq   = '$seq'
				   and del_flag      = 'N'";

		$overtime = $conn->get_data($sql);
	}

	if (empty($overtime)) $overtime = 0;

	$sql = "select kind, from_dt, to_dt, lvl, gbn, gbn2, 0 as overtime, addtime1, addtime2
			  from (
			       select m03_mkind as kind, m03_sdate as from_dt, m03_edate as to_dt, m03_ylvl as lvl, m03_vlvl as gbn, m03_sgbn as gbn2, m03_add_time1 as addtime1, m03_add_time2  as addtime2
			         from m03sugupja
			        where m03_ccode  = '$code'
					  and m03_mkind in ('2', '4')
			          and m03_jumin  = '$jumin'
					  and m03_del_yn = 'N'
			        union all
			       select m31_mkind as kind, m31_sdate as from_dt, m31_edate as to_dt, m31_level as lvl, m31_vlvl as gbn, m31_sgbn as gbn2, m31_add_time1 as addtime1, m31_add_time2  as addtime2
       			     from m31sugupja
			        where m31_ccode  = '$code'
					  and m31_mkind in ('2', '4')
			          and m31_jumin  = '$jumin'
					  and (select m03_del_yn from m03sugupja where m03_ccode = m31_ccode and m03_mkind = m31_mkind and m03_jumin = m31_jumin) = 'N'
				   ) as t
			 order by from_dt";

	$conn->query($sql);
	$conn->fetch();

	$row_count = $conn->row_count();
	$kind_name = '';

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		$kind_name .= '<input name=\'use_kind\' type=\'radio\' class=\'radio\' value=\''.$row['kind'].'\' checked>';
		$kind_name .= $conn->kind_name($kind_list, $row['kind']);

		if ($row['kind'] == $svc_cd)
			$client = $row;
	}

	$conn->row_free();

	if ($row_count == 0){
		##########################################################
		#
		# 이용할 서비스가 없다.
		#
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'\'>
				<colgroup>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<td class=\'center bold\'>이월할 수 있는 서비스가 없습니다.</td>
					</tr>
				</tbody>
			  </table>';
	}else{
		##########################################################
		#
		# 이용서비스명
		#
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'\'>
				<colgroup>
					<col width=\'70px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th>이용서비스</th>
						<td class=\'left\'>'.$kind_name.'</td>
					</tr>
				</tbody>
			  </table>';

		##########################################################
		#
		# 이월시간
		#
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'\'>
				<colgroup>
					<col width=\'70px\'>
					<col width=\'200px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th>이월시간</th>';

		if ($chk_cnt == 0){
			echo '<td class=\'last\'><input name=\'overtime\' type=\'text\' value=\''.$overtime.'\' maxlength=\'3\' class=\'number\' onkeydown=\'__onlyNumber(this);\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\' style=\'width:70px;\'>시간</td>';
		}else{
			echo '<td class=\'left last\'>'.$overtime.' 시간(일)</td>';
		}

		echo '			<td class=\'right\'>';

		if ($chk_cnt == 0){
			echo '<a href=\'#\' onclick=\'_voucher_run("iljung_service_overtime_ok.php");\'>저장</a> |
				  <a href=\'#\' onclick=\'self.close();\'>취소</a>';
		}

		echo '				<a href=\'#\' onclick=\'self.close();\'>닫기</a>
						</td>
					</tr>
				</tbody>
			  </table>';

		##########################################################
		#
		#
		#
		echo '<input name=\'seq\'       type=\'hidden\' value=\''.$seq.'\'>';
		echo '<input name=\'mode\'      type=\'hidden\' value=\''.$mode.'\'>';
		echo '<input name=\'onload\'    type=\'hidden\' value=\''.$onload.'\'>';
	}

	include_once("../inc/_db_close.php");

	$value = ob_get_contents();
	ob_end_clean();

	echo $value;
?>
<?
	if (!isset($code)) include_once('../inc/_http_home.php');

	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('iljung_config.php');

	$wrt_mode = $myF->get_iljung_mode();



	/*********************************************************

		담당직원

	*********************************************************/
		$sql = "select m03_yoyangsa1"
			 . ",     (select m02_yname from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1 and m02_ygoyong_stat = '1')"
			 . ",     (select m02_ygibonkup from m02yoyangsa where m02_ccode = m03_ccode and m02_mkind = m03_mkind and m02_yjumin = m03_yoyangsa1 and m02_ygupyeo_kind in ('1','2') and m02_ygoyong_stat = '1')"
			 . "  from m03sugupja"
			 . " where m03_ccode = '".$code
			 . "'  and m03_mkind = '".$svc_cd
			 . "'  and m03_jumin = '".$jumin
			 . "'";

	$conn->query($sql);
	$row = $conn->fetch();
	$yoy1   = $row[0];
	$yoyNm1 = $row[1];
	$yoyTA1 = $row[2];
	$yoy2   = '';
	$yoyNm2 = '';
	$yoyTA2 = '';
	/********************************************************/


	if ($lbTestMode){
		$sql = 'select seq
				  from client_his_svc
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svc_cd.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
				 order by seq desc
				 limit 1';

		$liSeq = $conn->get_data($sql);

		$sql = 'select svc_cost
				,      svc_cnt
				  from client_his_other
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svc_cd.'\'
				   and seq    = \''.$liSeq.'\'';
	}else{
		$sql = "select svc_cost
				,      svc_max_time
				  from (
					   select m03_kupyeo_1 as svc_cost
					   ,      m03_kupyeo_2 as svc_max_time
					   ,      m03_sdate as from_dt
					   ,      m03_edate as to_dt
						 from m03sugupja
						where m03_ccode = '$code'
						  and m03_mkind = '$svc_cd'
						  and m03_jumin = '$jumin'
						union all
					   select m31_kupyeo_1
					   ,      m31_kupyeo_2
					   ,      m31_sdate
					   ,      m31_edate
						 from m31sugupja
						where m31_ccode = '$code'
						  and m31_mkind = '$svc_cd'
						  and m31_jumin = '$jumin'
					   ) as t
				 where left(from_dt, 6) <= '$year$month'
				   and left(to_dt, 6)   >= '$year$month'";
	}

	$mst = $conn->get_array($sql);



	ob_start();

	echo '<table class=\'my_table my_border_blue\' style=\'width:100%;'.($wrt_mode == 1 ? '' : 'margin-top:-2px;').'\'>';
	echo '	<colgroup>
				<col width=\'100px\'>
				<col width=\'80px\'>
				<col width=\'95px\'>
				<col width=\'30px\'>
				<col width=\'110px\'>
				<col width=\'30px\'>
				<col width=\'110px\'>
				<col width=\'80px\'>
				<col width=\'80px\'>
				<col width=\'80px\'>
				<col>
			</colgroup>';
		echo '<thead>';

			if ($wrt_mode == 1){
				echo '<tr><th class=\'head bold\' colspan=\'11\'>'.$kind_nm.'</th></tr>';
			}

			echo '<tr>';
				echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>제공서비스</th>';
				echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>비용구분</th>';
				echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>제공자</th>';
				echo '<th class=\'head\' style=\''.__BORDER_T__.'\' colspan=\'4\'>방문시간</th>';
				echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>소요시간</th>';
				echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>서비스단가</th>';
				echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>서비스시간</th>';
				echo '<th class=\'head\' style=\''.__BORDER_T__.'\'>비고</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			echo '<tr>';
				echo '<td class=\'left\'>'.$kind_nm.'</td>';
				echo '<td class=\'left\'>본인부담</td>';
				echo '<td>';
					echo '<input name=\'yoyNm1\' type=\'text\'   value=\''.$yoyNm1.'\' style=\'width:70px; background-color:#eeeeee;\' onClick=\'_helpSuYoyPA("'.$code.'","","'.$key.'",document.f.yoy1,document.f.yoyNm1,document.f.yoyTA1)\' readOnly><a onClick=\'_yoyNot("1");\'><span class=\'bold\'>X</span></a>';
					echo '<input name=\'yoy1\'   type=\'hidden\' value=\''.$ed->en($yoy1).'\'>';
					echo '<input name=\'yoyTA1\' type=\'hidden\' value=\''.$yoyTA1.'\'>';
				echo '</td>';
				echo '<th>시작</th>';
				echo '<td>';
					echo '<input name=\'ftHour\' type=\'text\' value=\'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\'>시';
					echo '<input name=\'ftMin\'  type=\'text\' value=\'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\'>분';
				echo '</td>';
				echo '<th>종료</th>';
				echo '<td>';
					echo '<input name=\'ttHour\' type=\'text\' value=\'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\'>시';
					echo '<input name=\'ttMin\'  type=\'text\' value=\'\' maxlength=\'2\' class=\'number\' style=\'text-align:center; width:30px;\' onKeyDown=\'__onlyNumber(this);\' onfocus=\'this.select();\' onkeyup=\'_iljung_check_time(this);\' onchange=\'__object_set_format(this, "number", 2);\'>분';
				echo '</td>';
				echo '<td><input name=\'procTime\' type=\'text\' class=\'number\' style=\'width:100%; cursor:default; background-color:#eeeeee;\' onfocus=\'this.blur();\' readonly></td>';
				echo '<td class=\'right\'>'.number_format($mst['svc_cost']).'</td>';
				echo '<td class=\'right\'>'.number_format($mst['svc_max_time']).'</td>';
				echo '<td></td>';
			echo '</tr>';
		echo '</tbody>';
	echo '</table>';

	/**************************************************

		산모 추가 요금 등록

	**************************************************/
	if ($svc_id == 31) include('iljung_reg_addpay.php');
	/*************************************************/

	########################################################
	#
	# 제공요일 및 일자
	#
	include_once('iljung_svc_date.php');
	########################################################

	########################################################
	#
	# 적용수가
	#
	include_once('iljung_svc_suga.php');
	########################################################

	echo '<input name=\'svcSubCode\'      type=\'hidden\' value=\'Y\'>';
	echo '<input name=\'bipayUmu\'        type=\'hidden\' value=\'Y\'>';
	echo '<input name=\'svcStnd\'         type=\'hidden\' value=\'0\'>';
	echo '<input name=\'svcCnt\'          type=\'hidden\' value=\'0\'>';
	echo '<input name=\'svcCost\'         type=\'hidden\' value=\''.$mst['svc_cost'].'\'>';
	echo '<input name=\'svcMaxTime\'      type=\'hidden\' value=\''.$mst['svc_max_time'].'\'>';
	echo '<input name=\'voucher_make_yn\' type=\'hidden\' value=\'Y\'>';




	/**************************************************

		시간입력제한

	**************************************************/
	echo '<input name=\'svcLimitTime\' type=\'hidden\' value=\'0\'>'; //제한업음





	$html = ob_get_contents();

	ob_end_clean();

	echo $html;

	unset($mst);

	include_once('../inc/_db_close.php');
?>
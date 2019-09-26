<?
	#######################################################################
	#
	#

	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	$current_svc_nm = $conn->kind_name($k_list, $__CURRENT_SVC_ID__, 'id');

	$body_w = '100%';

	#
	#######################################################################

	include('./client_reg_sub_reason.php');

	$sql = "select m03_vlvl as gbn
			,      m03_kupyeo_1 as amt1
			,      m03_kupyeo_2 as amt2
			,      m03_baby_svc_cnt as svc_cnt
			  from m03sugupja
			 where m03_ccode  = '$code'
			   and m03_mkind  = '$__CURRENT_SVC_CD__'
			   and m03_jumin  = '$jumin'
			   and m03_del_yn = 'N'";

	$client = $conn->get_array($sql);

	if (!$client){
		$client['amt1'] = 0;
		$client['amt2'] = 0;
	}

	if (empty($client['gbn'])) $client['gbn'] = '1';

	if ($__CURRENT_SVC_ID__ == 31){
		$class = '';
	}else{
		$class = 'bottom';
	}
?>
<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-top:none; <?=$view_type == 'read' ? '' : 'border-bottom:none;';?>">
	<colgroup>
		<col width="80px">
		<!--col width="110px">
		<col width="90px"-->
		<col>
	</colgroup>
	<tbody>
	<?
		if ($__CURRENT_SVC_ID__ == 31){
			echo '<tr>
					<th>서비스 구분</th>
					<td>';
					if ($view_type == 'read'){
						switch($client['gbn']){
							case '1':
								$tmp = '단태아';
								break;
							case '2':
								$tmp = '쌍태아';
								break;
							case '3':
								$tmp = '삼태아';
								break;
						}
						echo '<div class=\'left\'>'.$tmp.'</div>';
					}else{
						echo '<input id=\''.$__CURRENT_SVC_ID__.'_gbn\' name=\''.$__CURRENT_SVC_ID__.'_gbn\' type=\'radio\' class=\'radio\' value=\'1\' tag=\''.$client['gbn'].'\' '.($client['gbn'] == '1' ? 'checked' : '').'><a href=\'#\' onclick=\'$(":radio[name=\"'.$__CURRENT_SVC_ID__.'_gbn\"]:input[value=\"1\"]").attr("checked","checked");\'>단태아</a>
							  <input id=\''.$__CURRENT_SVC_ID__.'_gbn\' name=\''.$__CURRENT_SVC_ID__.'_gbn\' type=\'radio\' class=\'radio\' value=\'2\' tag=\''.$client['gbn'].'\' '.($client['gbn'] == '2' ? 'checked' : '').'><a href=\'#\' onclick=\'$(":radio[name=\"'.$__CURRENT_SVC_ID__.'_gbn\"]:input[value=\"2\"]").attr("checked","checked");\'>쌍태아</a>
							  <input id=\''.$__CURRENT_SVC_ID__.'_gbn\' name=\''.$__CURRENT_SVC_ID__.'_gbn\' type=\'radio\' class=\'radio\' value=\'3\' tag=\''.$client['gbn'].'\' '.($client['gbn'] == '3' ? 'checked' : '').'><a href=\'#\' onclick=\'$(":radio[name=\"'.$__CURRENT_SVC_ID__.'_gbn\"]:input[value=\"3\"]").attr("checked","checked");\'>삼태아</a>';
					}
			echo '	</td>
				  </tr>';
		}

		echo '<tr>
				<th class=\'\'>'.($__CURRENT_SVC_ID__ == 31 ? '서비스금액' : '서비스단가').'</th>';

		if ($view_type == 'read'){
			echo '<td class=\'\'><div class=\'left\'>'.number_format($client['amt1']).$str_svc_type.'</div></td>';
		}else{
			echo '<td class=\'\'>';

			if ($__CURRENT_SVC_ID__ == 31){
				$setSvcPay = '$("#'.$__CURRENT_SVC_ID__.'_svcpay").text(
								__num2str(__str2num($("#'.$__CURRENT_SVC_ID__.'_kupyeo1").attr("value")) *
								          __str2num($("#'.$__CURRENT_SVC_ID__.'_svcdays").attr("value"))) );';


				echo '<input id=\''.$__CURRENT_SVC_ID__.'_kupyeo1\' name=\''.$__CURRENT_SVC_ID__.'_kupyeo1\' type=\'text\' value=\''.number_format($client['amt1']).'\' tag=\''.$client['amt1'].'\' maxlength=\'10\' class=\'number\' style=\'width:70px;\' onchange=\''.$setSvcPay.'\'>원 X';
				echo '<input id=\''.$__CURRENT_SVC_ID__.'_svcdays\' name=\''.$__CURRENT_SVC_ID__.'_svcdays\' type=\'text\' value=\''.number_format($client['svc_cnt']).'\' tag=\''.$client['svc_cnt'].'\' maxlength=\'10\' class=\'number\' style=\'width:50px;\' onchange=\''.$setSvcPay.'\'>일 =';
				echo '<span id=\''.$__CURRENT_SVC_ID__.'_svcpay\' class=\'left bold\' style=\'color:#0000ff;\'>'.number_format($client['amt1'] * $client['svc_cnt']).'</span>원';
			}else{
				echo '<input id=\''.$__CURRENT_SVC_ID__.'_kupyeo1\' name=\''.$__CURRENT_SVC_ID__.'_kupyeo1\' type=\'text\' value=\''.number_format($client['amt1']).'\' tag=\''.$client['amt1'].'\' maxlength=\'10\' class=\'number\'>';
			}

			echo '</td>';
			echo '<input id=\''.$__CURRENT_SVC_ID__.'_kupyeo2\' name=\''.$__CURRENT_SVC_ID__.'_kupyeo2\' type=\'hidden\' value=\'0\' tag=\'0\'>';
		}
		echo '</tr>';


	?>
	</tbody>
</table>

<?
	if ($__CURRENT_SVC_ID__ == 31){
		include('client_reg_sub_staff.php');
		include('client_reg_sub_baby_addpay.php');
	}


	/*********************************************************

		추천인

	*********************************************************/
	if ($view_type == 'read'){
	}else{
		if ($debug){
		echo '<table class=\'my_table my_border_blue\' style=\'width:100%; border-top:none;'.($lbTestMode ? '' : 'border-bottom:none;').'\'>
				<colgroup>
					<col width=\'80px\'>
					<col width=\'80px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th class=\' bottom\' rowspan=\'3\'>추천인</th>
						<th class=\'\'>성명</th>
						<td class=\'\'><input id=\'recomNm_'.$__CURRENT_SVC_CD__.'\' name=\'recomNm_'.$__CURRENT_SVC_CD__.'\' type=\'text\' value=\''.$arrRecomList[$__CURRENT_SVC_CD__]['nm'].'\'></td>
					</tr>
					<tr>
						<th class=\'\'>연락처</th>
						<td class=\'\'><input id=\'recomTel_'.$__CURRENT_SVC_CD__.'\' name=\'recomTel_'.$__CURRENT_SVC_CD__.'\' type=\'text\' value=\''.$myF->phoneStyle($arrRecomList[$__CURRENT_SVC_CD__]['tel']).'\' class=\'phone\'></td>
					</tr>
					<tr>
						<th class=\'bottom\'>금액</th>
						<td class=\'bottom\'><input id=\'recomAmt_'.$__CURRENT_SVC_CD__.'\' name=\'recomAmt_'.$__CURRENT_SVC_CD__.'\' type=\'text\' value=\''.number_format($arrRecomList[$__CURRENT_SVC_CD__]['amt']).'\' class=\'number\' style=\'width:70px;\'></td>
					</tr>
				</tbody>
			  </table>';
		}
	}
?>
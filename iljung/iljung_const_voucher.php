<?
	/**************************************************

		바우처/장애인 활동지원

		서비스내역

	**************************************************/



	/*********************************************************
		한도금액
	*********************************************************/
	$sql = 'select voucher_maketime + (voucher_overpay / voucher_suga_cost) + voucher_addtime as time0, voucher_makepay + voucher_overpay + voucher_addpay as pay0
			,      voucher_addtime1 + voucher_addtime2 as time1, (voucher_addtime1 + voucher_addtime2) * voucher_suga_cost as pay1
			,      voucher_suga_cost as suga_cost
			  from voucher_make
			 where org_no        = \''.$code.'\'
			   and voucher_kind  = \''.$svc_cd.'\'
			   and voucher_jumin = \''.$jumin.'\'
			   and voucher_yymm  = \''.$year.$month.'\'
			   and del_flag      = \'N\'';

	$limitPayInfo = $conn->get_array($sql);

	$sub_list[0] = array('id'=>24, 'code'=>200, 'name'=>'활동지원');
	$sub_list[1] = array('id'=>24, 'code'=>500, 'name'=>'방문목욕');
	$sub_list[2] = array('id'=>24, 'code'=>800, 'name'=>'방문간호');

	/**************************************************
		추가시간
	**************************************************/
	$add_list[0] = array('id'=>24, 'text'=>'기본/추가');
	$add_list[1] = array('id'=>24, 'text'=>'시도/자치');
	$add_list[2] = array('id'=>24, 'text'=>'');

	echo '<table class=\'my_table my_border_blue\' style=\'width:100%; margin-top:'.__GAB__.'px;\'>';
		echo '<colgroup>';
			echo '<col width=\'70px\'>';
			echo '<col width=\'50px\'>';
			echo '<col width=\'90px\'>';
			echo '<col width=\'50px\'>';
			echo '<col width=\'90px\'>';
			echo '<col width=\'50px\'>';
			echo '<col width=\'90px\'>';
			echo '<col width=\'2px\'>';
			echo '<col>';
			echo '<col width=\'50px\'>';
			echo '<col width=\'90px\'>';
			echo '<col width=\'50px\'>';
			echo '<col width=\'90px\'>';
		echo '</colgroup>';
		echo '<tbody>';

			echo '<tr>
					<th class=\'head bold\' colspan=\'14\'>'.$kind_nm.' 서비스 내역</th>
				  </tr>';

			echo '<tr>
					<th class=\'head bold\' rowspan=\'2\'>구분</th>
					<th class=\'head bold\' colspan=\'2\'>수급(급여)계</th>
					<th class=\'head bold\' colspan=\'2\'>비급여</th>
					<th class=\'head bold\' colspan=\'2\'>이용</th>
					<td class=\'head bold bottom\'></td>
					<th class=\'head bold\' rowspan=\'2\'>구분</th>
					<th class=\'head bold\' colspan=\'2\'>잔여</th>
					<th class=\'head bold\' colspan=\'2\'>한도</th>
				  </tr>';

			echo '<tr>
					<th class=\'head bold\'>시간</th>
					<th class=\'head bold\'>금액</th>
					<th class=\'head bold\'>시간</th>
					<th class=\'head bold\'>금액</th>
					<th class=\'head bold\'>시간</th>
					<th class=\'head bold\'>금액</th>
					<td class=\'head bold\' bottom></td>
					<th class=\'head bold\'>시간</th>
					<th class=\'head bold\'>금액</th>
					<th class=\'head bold\'>시간</th>
					<th class=\'head bold\'>금액</th>
				  </tr>';

			$limitTotInfo = array('pay'=>0, 'time'=>0);

			if (is_array($sub_list)){
				foreach($sub_list as $i => $list){
					echo '<tr>
							<th class=\'head\'>'.$list['name'].'</th>
							<td class=\'right strConstClass\' id=\'strTotTime'.$list['code'].'\'></td>
							<td class=\'right strConstClass\' id=\'strTotPay'.$list['code'].'\'></td>
							<td class=\'right strConstClass\' id=\'strBipayTime'.$list['code'].'\'></td>
							<td class=\'right strConstClass\' id=\'strBipayPay'.$list['code'].'\'></td>
							<td class=\'right strConstClass\' id=\'strUseTime'.$list['code'].'\'></td>
							<td class=\'right strConstClass\' id=\'strUsePay'.$list['code'].'\'></td>
							<td class=\'bottom\'></td>
							<th class=\'head\'>'.$add_list[$i]['text'].'</th>
							<td class=\'right strConstClass '.(is_numeric($limitPayInfo['time'.$i]) ? 'strConstLeft' : '').'\' id=\'strLeftTime'.$i.'\'>'.(is_numeric($limitPayInfo['time'.$i]) ? number_format($limitPayInfo['time'.$i],1) : '').'</td>
							<td class=\'right strConstClass\' id=\'strLeftPay'.$i.'\'>'.(is_numeric($limitPayInfo['pay'.$i]) ? number_format($limitPayInfo['pay'.$i]) : '').'</td>
							<td class=\'right\' id=\'strLimitTime'.$i.'\'>'.(is_numeric($limitPayInfo['time'.$i]) ? number_format($limitPayInfo['time'.$i],1) : '').'</td>
							<td class=\'right\' id=\'strLimitPay'.$i.'\'>'.(is_numeric($limitPayInfo['pay'.$i]) ? number_format($limitPayInfo['pay'.$i]) : '').'</td>
						  </tr>';

					$limitTotInfo['pay']  += $limitPayInfo['pay'.$i];
					$limitTotInfo['time'] += $limitPayInfo['time'.$i];
				}
			}



			/**************************************************

				소계

			**************************************************/
			echo '<tr>
					<th class=\'head bold\'>계</th>
					<td class=\'right bold\' id=\'strTotTimeSum\'></td>
					<td class=\'right bold\' id=\'strTotPaySum\'></td>
					<td class=\'right bold\' id=\'strBipayTimeSum\'></td>
					<td class=\'right bold\' id=\'strBipayPaySum\'></td>
					<td class=\'right bold\' id=\'strUseTimeSum\'></td>
					<td class=\'right bold\' id=\'strUsePaySum\'></td>
					<td></td>
					<th class=\'head bold\'>계</th>
					<td class=\'right bold\' id=\'strLeftTotTime\'>'.number_format($limitTotInfo['time'],1).'</td>
					<td class=\'right bold\' id=\'strLeftTotPay\'>'.number_format($limitTotInfo['pay']).'</td>
					<td class=\'right bold\' id=\'strLimitTotTime\'>'.number_format($limitTotInfo['time'],1).'</td>
					<td class=\'right bold\' id=\'strLimitTotPay\'>'.number_format($limitTotInfo['pay']).'</td>
				  </tr>';

		echo '</tbody>';
	echo '</table>
		  <span id=\'strSugaCost\' style=\'display:none;\'>'.$limitPayInfo['suga_cost'].'</span>';

	unset($limitPayInfo);
	unset($limitTotInfo);
?>
<?
	$sql = 'select t13_bonin_yul as lvl
			,      t13_max_amt as max_amt
			,      t13_result_amt as limit_amt
			,      t13_save_pay as save_amt
			,      t13_chung_amt1 as sugapay1, t13_bipay1 as bipay1, t13_suga_tot1 as totpay1
			,      t13_chung_amt2 as sugapay2, t13_bipay2 as bipay2, t13_suga_tot2 as totpay2
			,      t13_chung_amt3 as sugapay3, t13_bipay3 as bipay3, t13_suga_tot3 as totpay3
			,      t13_chung_amt4 as sugapay4, t13_bipay4 as bipay4, t13_suga_tot4 as totpay4
			,      t13_other as pay_if
			  from t13sugupja
			 where t13_ccode    = \''.$code.'\'
			   and t13_pay_date = \''.$year.$month.'\'
			   and t13_jumin    = \''.$jumin.'\'
			   and t13_mkind    = \'4\'';

	$row = $conn->get_array($sql);

	ob_start();

	if (is_array($row) > 0){
		parse_str($row['pay_if'], $pay_if);

		
		if($mode == 'excel'){ 
			//엑셀출력일 경우
			echo '<div class=\'title\'>장애인활동지원 확정내역</div>';
			echo '<table class="my_table" border="1">';
			$css  = 'head';
		}else {
			echo '<div class=\'title title_border\'>장애인활동지원 확정내역</div>';
			echo '<table class="my_table" style="width:100%;">';
			$css  = 'head last';
		}
		echo '<colgroup>';
		echo '<col width=\'50px\'>';
		echo '<col width=\'60px\'>';
		echo '<col width=\'70px\'>';
		echo '<col width=\'100px\' span=\'3\'>';
		echo '<col width=\'1px\'>';
		echo '<col width=\'70px\' span=\'4\'>';
		echo '<col>';
		echo '</colgroup>';
		echo '<thead>';
		echo '<tr>';
		echo '<th class=\'head\'>등급</th>';
		echo '<th class=\'head\'>총이용금액</th>';
		echo '<th class=\'head\'>서비스</th>';
		echo '<th class=\'head\'>수급(급여)계</th>';
		echo '<th class=\'head\'>급여</th>';
		echo '<th class=\'head\'>비급여</th>';
		if($mode != 'excel'){ 
			echo '<td class=\'center\'></td>';
		}
		echo '<th class=\''.$css.'\' colspan=\'4\'>급여정보</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';

		echo '<tr>';
		echo '<td class=\'center\' rowspan=\'3\'>'.$row['lvl'].'등급</td>';
		echo '<td class=\'right\' rowspan=\'3\'>'.number_format($row['max_amt']).'</td>';
		echo '<td class=\'center\'>활동지원</td>';
		echo '<td class=\'right\'>'.number_format($row['totpay1']).'</td>';
		echo '<td class=\'right\'>'.number_format($row['sugapay1']).'</td>';
		echo '<td class=\'right\'>'.number_format($row['bipay1']).'</td>';
		if($mode != 'excel'){ 
			echo '<td class=\'center bottom\'></td>';
		}
		echo '<th class=\'center\'>기본급여</th>';
		echo '<td class=\'right\'>'.@number_format($pay_if['makepay']).'</td>';
		echo '<th class=\'center\'>이월급여</th>';
		#echo '<td class=\'right last\'>'.number_format($pay_if['overpay']).'</td>';
		echo '<td class=\'right last\'>'.number_format($row['save_amt']).'</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class=\'center\'>방문목욕</td>';
		echo '<td class=\'right\'>'.number_format($row['totpay2']).'</td>';
		echo '<td class=\'right\'>'.number_format($row['sugapay2']).'</td>';
		echo '<td class=\'right\'>'.number_format($row['bipay2']).'</td>';
		if($mode != 'excel'){ 
			echo '<td class=\'center bottom\'></td>';
		}
		echo '<th class=\'center\'>추가급여</th>';
		echo '<td class=\'right\'>'.@number_format($pay_if['addpay']).'</td>';
		if($mode != 'excel'){ 
			echo '<th class=\'center\'>&nbsp;</th>';
		}
		echo '<td class=\'left last\'>&nbsp;</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class=\'center\'>방문간호</td>';
		echo '<td class=\'right\'>'.number_format($row['totpay3']).'</td>';
		echo '<td class=\'right\'>'.number_format($row['sugapay3']).'</td>';
		echo '<td class=\'right\'>'.number_format($row['bipay3']).'</td>';
		if($mode != 'excel'){ 
			echo '<td class=\'center bottom\'></td>';
		}
		echo '<th class=\'center\'>시도비급여</th>';
		echo '<td class=\'right\'>'.@number_format($pay_if['sidopay']).'</td>';
		echo '<th class=\'center\'>&nbsp;</th>';
		echo '<td class=\'left last\'>&nbsp;</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th class=\'right\' colspan=\'3\'>합계</th>';
		echo '<td class=\'right\'>'.number_format($row['totpay4']).'</td>';
		echo '<td class=\'right\'>'.number_format($row['sugapay4']).'</td>';
		echo '<td class=\'right\'>'.number_format($row['bipay4']).'</td>';
		if($mode != 'excel'){ 
			echo '<td class=\'center bottom\'></td>';
		}
		echo '<th class=\'center\'>자치비급여</th>';
		echo '<td class=\'right\'>'.@number_format($pay_if['jachpay']).'</td>';
		echo '<th class=\'center\'>&nbsp;</th>';
		echo '<td class=\'left last\'>&nbsp;</td>';
		echo '</tr>';

		echo '</tbody>';

		echo '</table>';
	}

	$html = ob_get_contents();

	ob_end_clean();

	echo $html;
?>
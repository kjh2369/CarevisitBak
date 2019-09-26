<div class="title title_border">바우처 및 기타유료 확정내역</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="130px">
		<col width="70px">
		<col width="100px" span="5">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">서비스</th>
			<th class="head">단가</th>
			<th class="head">구매시간(일)</th>
			<th class="head">총사용시간(일)</th>
			<th class="head">초+비시간(일)</th>
			<th class="head">사용시간(일)</th>
			<th class="head">잔여시간(일)</th>
			<th class="head last"><u title="총본인부담금 = 본인부담금 + 초과금액 + 비급여">총본인부담금</u></th>
		</tr>
	</thead>
	<?
		$sql = "select t13_mkind as kind
				,      t13_result_amt as cost
				,      t13_suga_tot1 as tot_time
				,      t13_suga_tot2 as tot_amt
				,      t13_bonin_amt1 as use_time
				,      t13_bonin_amt2 as use_amt
				,      t13_over_amt1 as over_time
				,      t13_over_amt2 as over_amt
				,      t13_bipay1 as bi_time
				,      t13_bipay2 as bi_amt
				,      t13_save_time as save_time
				,      t13_time_gbn as time_gbn
				,      t13_misu_amt as tot_my_amt
				  from t13sugupja
				 where t13_ccode    = '$code'
				   and t13_mkind    > '0'
				   and t13_pay_date = '$year$month'
				   and t13_jumin    = '$jumin'";

		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		$tot_time   = 0;
		$use_time   = 0;
		$over_time  = 0;
		$bi_time    = 0;
		$save_time  = 0;
		$tot_my_amt = 0;

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$gbn = $row['time_gbn'] == 'time' ? '시간' : '일';

			echo '<tr>';
			echo '<td class=\'left\'>'.$conn->kind_name_svc($row['kind']).'</td>';
			echo '<td class=\'right\'>'.number_format($row['cost']).'</td>';
			echo '<td class=\'right\'>'.$row['tot_time'].' '.$gbn.'</td>';
			echo '<td class=\'right\'>'.($row['use_time']+$row['over_time']+$row['bi_time']).' '.$gbn.'</td>';
			echo '<td class=\'right\'>'.($row['over_time']+$row['bi_time']).' '.$gbn.'</td>';
			echo '<td class=\'right\'>'.$row['use_time'].' '.$gbn.'</td>';
			echo '<td class=\'right\'>'.$row['save_time'].' '.$gbn.'</td>';
			echo '<td class=\'right last\'>'.number_format($row['tot_my_amt']).'</td>';
			echo '</tr>';

			$tot_time   += $row['tot_time'];
			$use_time   += $row['use_time'];
			$over_time  += $row['over_time'];
			$bi_time    += $row['bi_time'];
			$save_time  += $row['save_time'];
			$tot_my_amt += $row['tot_my_amt'];
		}

		$conn->row_free();

		echo '<tr>';
		echo '<th class=\'right\' colspan=\'2\'>합계</th>';
		echo '<td class=\'right\'>'.$tot_time.' '.$gbn.'</td>';
		echo '<td class=\'right\'>'.($use_time+$over_time+$bi_time).' '.$gbn.'</td>';
		echo '<td class=\'right\'>'.($over_time+$bi_time).' '.$gbn.'</td>';
		echo '<td class=\'right\'>'.$use_time.' '.$gbn.'</td>';
		echo '<td class=\'right\'>'.$save_time.' '.$gbn.'</td>';
		echo '<td class=\'right last\'>'.number_format($tot_my_amt).'</td>';
		echo '</tr>';
	?>
</table>
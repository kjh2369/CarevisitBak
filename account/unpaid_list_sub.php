<?
	include_once("../inc/_ed.php");

	if (__EXCEL__ != 'YES'){?>
		<table class="my_table" style="width:100%;"><?
	}else{?>
		<table border="1"><?
	}?>
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="80px">
		<col width="60px">
		<col width="120px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자명</th>
			<th class="head">생년월일</th>
			<th class="head">등급</th>
			<th class="head">구분</th>
			<th class="head">미수금액</th>
			<th class="head<?=__EXCEL__ != 'YES' ? ' last' : '';?>">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select (select ifnull(sum(t13_bonbu_tot4 /*t13_misu_amt*/), 0)
						  from t13sugupja
					     where t13_ccode = '$code'
						   and t13_mkind = '$kind'
						   and t13_type  = '2') -
					   (select ifnull(sum(deposit_amt), 0)
						  from unpaid_deposit
					     where org_no   = '$code'
						   and del_flag = 'N') as deposit_amt";
		$deposit_amt = $conn->get_data($sql);

		$sql = "select client_name
				,      client_jumin
				,      lvl_name
				,      stp_name
				,      bonin_yul
				,      unpaid_amt
				,      deposit_amt
				,      unpaid_amt - deposit_amt as real_unpaid_amt
				  from (
					   select m03_jumin as client_jumin
					   ,      m03_name as client_name
					   ,      case when m03_mkind = '0' then lvl.m81_name else '' end as lvl_name
					   ,      case when m03_mkind = '0' then stp.m81_name else '' end as stp_name
					   ,      m03_bonin_yul as bonin_yul
					   ,     (select ifnull(sum(t13_bonbu_tot4 /*t13_misu_amt*/), 0)
						   	    from t13sugupja
							   where t13_ccode = m03_ccode
							     and t13_jumin = m03_jumin
								 and t13_type  = '2') as unpaid_amt
					   ,     (select ifnull(sum(deposit_amt), 0)
							    from unpaid_deposit
							   where org_no        = m03_ccode
							     and deposit_jumin = m03_jumin
								 and del_flag      = 'N') as deposit_amt
					     from m03sugupja
					     left join m81gubun as lvl
						   on lvl.m81_gbn  = 'LVL'
					      and lvl.m81_code = m03_ylvl
					     left join m81gubun as stp
						   on stp.m81_gbn  = 'STP'
					      and stp.m81_code = m03_skind
					    where m03_ccode = '$code'
					      and m03_mkind = ".$conn->_client_kind();

		if ($find_name != ''){
			$sql .= " and m03_name >= '$find_name'";
		}

		$sql .= "
					   ) as t";

		if ($find_unpaid == '2'){
			$sql .= "
					where unpaid_amt - deposit_amt > 0";
		}

		$sql .= "
				 order by client_name";

		$conn->fetch_type = 'accos';
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();?>

		<tr>
			<td class="sum right" colspan="5">계</td>
			<td class="sum right"><?=number_format($deposit_amt);?></td>
			<td class="sum left last"></td>
		</tr><?

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$tot_amt += $row['real_unpaid_amt']; ?>
			<tr>
				<td class="center"		><?=$i + 1;?></td>
				<td class="left"		><?=$row['client_name'];?></td>
				<td class="center"		><?=$myF->issToBirthday($row['client_jumin'],'.');?></td>
				<td class="center"		><?=$row['lvl_name'];?></td>
				<td class="left"		><?=$row['stp_name'].(!empty($row['stp_name']) ? '('.$row['bonin_yul'].')' : '');?></td>
				<td class="right"		><?=number_format($row['real_unpaid_amt']);?></td><?
				if (__EXCEL__ != 'YES'){?>
					<td class="left last"	>
						<img src="../image/btn_in.png" style="cursor:pointer;" onclick="reg('<?=$ed->en($row['client_jumin']);?>');">
						<img src="../image/btn_dtl.png" style="cursor:pointer;" onclick="detail('<?=$ed->en($row['client_jumin']);?>');">
					</td><?
				}else{?>
					<td class="left last" style="width:150px;">&nbsp;</td><?
				}?>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left last bottom" colspan="7"><?=$myF->message($row_count, 'N');?></td>
		</tr>
	</tbody>
</table>
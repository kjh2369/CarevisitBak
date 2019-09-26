<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orderBy = $_POST['orderBy'];
	$dipoYn  = $_POST['dipoYn'];
	
	$sql = 'SELECT sum(deposit_pay)
			  FROM seminar_request
			 WHERE deposit_yn = \'Y\'
			   AND del_flag = \'N\'';
	
	$sql .= ' AND left(insert_dt, 4) = \''.date('Y').'\''; 

	$DIPO_TOT = $conn -> get_data($sql);
	

	$sql = 'SELECT	sr.seq
			,		sr.org_no
			,		sr.org_nm
			,		sr.name
			,	    sr.rank+sr.rank2 as all_rank
			,		sr.deposit_pay
			,	    sr.deposit_yn
			,		sr.in_pay
			,		left(sr.insert_dt, 10) as dt
			,		mst.addr
			FROM	seminar_request AS sr
			INNER	JOIN (
						SELECT	DISTINCT
								m00_mcode AS org_no
						,		m00_caddr1 AS addr
						FROM	m00center
					) AS mst
					ON		mst.org_no = sr.org_no';
	
	$sql .= ' AND del_flag = \'N\'
	          AND gbn = \'2\'';

	//입금유무
	if($dipoYn != 'all') $sql .= ' AND deposit_yn = \''.$dipoYn.'\'';
	
	$sql .= ' AND left(insert_dt, 4) = \''.date('Y').'\''; 
	
	if ($orderBy == '1'){
		$sql .= ' ORDER	BY insert_dt DESC';
	}else if ($orderBy == '2'){
		$sql .= ' ORDER	BY org_nm';
	}
	
	//if($debug) echo '<tr><td colspan="5">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	
	$org_cnt = 0;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		
	
		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		} 
		
		
		if($row['deposit_pay']=='') $row['deposit_pay'] = 0;

		
		?>
		<tr style="background-color:#<?=$bgcolor;?>;">
			<td class="center"><?=$i+1;?></td>
			<td class="center"><?=str_replace('-','.',$row['dt']);?></td>
			<td class="left"><?=$row['org_no'];?></td>
			<td class="left"><div class="nowrap" style="width:100px;"><?=$row['org_nm'];?></div></td>
			<td class="left"><div class="nowrap" style="width:100px;"><?=$row['name'];?></div></td>
			<td class="right"><div class="nowrap" style="width:30px;"><?=$row['all_rank'];?>명</div></td>
			<td class="left"><input id="txtDipopay_<?=$i?>" name="txtDipopay_<?=$i?>"  type="text" onFocus="__replace(this,',','');" onchange="deposit_tot(this);" value="<?=number_format($row['deposit_pay']);?>" style="width:100%; text-align:right;"></td>
			<td class="left"><input id="txtInpay_<?=$i?>" name="txtInpay_<?=$i?>"  type="text" onFocus="__replace(this,',','');" onchange="deposit_tot(this);" value="<?=number_format($row['in_pay']);?>" style="width:100%; text-align:right;"></td>
			<td class="center">
				<input id="optDipoyn_<?=$i?>" name="optDipoyn_<?=$i?>" type="radio" class="radio" value="Y" <? if($row['deposit_yn'] == 'Y'){ ?> checked <? } ?> onclick="deposit_chk(this,'Y','<?=$i?>');">입금
				<input id="optDipoyn_<?=$i?>" name="optDipoyn_<?=$i?>" type="radio"  class="radio" value="N" onclick="deposit_chk(this,'N','<?=$i?>');" <? if($row['deposit_yn'] == 'N'){ ?> checked <? } ?> >미입금
			</td>
			<td class="left "><div class="nowrap" style="width:60px;"><?=$row['addr'];?></div></td>
			<td class="left last"><span id="btnSave" class="btn_pack m" ><button onclick="lfDel('<?=$row['org_no']?>','<?=$row['seq']?>');">삭제</button></span></td>
			<input id="code_<?=$i?>" name="code" type="hidden" value="<?=$row['org_no'];?>">
			<input id="seq_<?=$i?>" name="seq" type="hidden"   value="<?=$row['seq'];?>">
		</tr><?

		if($row['deposit_yn'] == 'Y'){
			$org_cnt += 1;
			$DIPO_CNT += $row['all_rank'];
			$tot_in_pay += $row['in_pay'];
		}

		$tot_rank += $row['all_rank'];
		$tot_pay += $row['deposit_pay'];
		
	}

	$conn->row_free();

	$DIPO_CNT = $tot_in_pay / 30000;


	?>

	<tr style="background-color:#EFEFEF;">
		<td class="center" colspan="5" style="font-size:9pt; font-weight:bold;">총 합계</td>
		<td class="center" ><?=number_format($tot_rank);?> 명</td>
		<td class="right">
			<span id="lblTotalPay" ><?=number_format($tot_pay);?></span>
			<input id="txtTotalPay" name="txtTotalPay" type="hidden" value="<?=number_format($tot_pay);?>">
		</td>
		<td class="right">
			<span id="lblTotalPay" ><?=number_format($tot_in_pay);?></span>
		</td>
		<td class="right last" colspan="3" >
			입금기관수: <?=number_format($org_cnt);?>&nbsp;&nbsp; 입금자수: <?=number_format($DIPO_CNT);?> 명&nbsp; <!--입금액 : <?=number_format($DIPO_TOT);?> 원-->
		</td>
	</tr>
	
<?
	unset($tot_pay);

	include_once('../inc/_db_close.php');
?>

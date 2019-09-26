<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orderBy = $_POST['orderBy'];
	$dipoYn  = $_POST['dipoYn'];
	$useYn   = $_POST['useYn'];
	$orgNo   = $_POST['orgNo'];
	$orgNm   = $_POST['orgNm'];

	$sql = 'SELECT	mst.addr
			,		mst.org_nm
			,		mst.org_no
			,		rp.seq
			,		rp.dipo_pay
			,		rp.dipo_yn
			,		rp.use_yn
			,		rp.insert_dt
			FROM	report2014_request AS rp
			INNER	JOIN (		
						SELECT	DISTINCT
								m00_mcode AS org_no
						,	    m00_store_nm AS org_nm
						,		m00_caddr1 as addr
						FROM	m00center
					) AS mst
					ON		mst.org_no = rp.org_no
			WHERE seq = (select max(seq) from report2014_request as tmp where tmp.org_no = rp.org_no)';
	
	//기관코드조회
	if($orgNo != ''){
		$sql .= ' AND org_no like \'%'.$orgNo.'%\'';
	}	
	
	//기관명조회
	if($orgNm != ''){
		$sql .= ' AND org_nm like \'%'.$orgNm.'%\'';
	}

	//입금유무
	if($dipoYn == 'Y'){
		$sql .= ' AND dipo_yn = \'Y\'';
	}else if($dipoYn == 'N'){
		$sql .= ' AND dipo_yn = \'N\'';
	}
	
	//사용유무
	if($useYn == 'Y'){
		$sql .= ' AND use_yn   = \'Y\'';
	}else if($useYn == 'N'){
		$sql .= ' AND use_yn   = \'N\'';
	}
	
	//정렬
	if ($orderBy == '1'){
		$sql .= ' ORDER	BY rp.insert_dt DESC';
	}else if ($orderBy == '2'){
		$sql .= ' ORDER	BY mst.org_nm';
	}else {
		$sql .= ' ORDER	BY case rp.use_yn when \'N\' then 1 else 2 end, rp.insert_dt DESC';
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}?>
		<tr style="background-color:#<?=$bgcolor;?>;">
			<td class="center"><?=$i+1;?></td>
			<td class="center"><?=str_replace('-','.',$row['insert_dt']);?></td>
			<td class="left"><?=$row['org_nm'];?></td>
			<td class="left"><?=$row['addr'];?></td>
			<td class="left"><input id="txtDipopay_<?=$i?>" name="txtDipopay_<?=$i?>" type="text" onchange="deposit_tot(this);" value="<?=number_format($row['dipo_pay']);?>" style="width:100%; text-align:right;"></td>
			<td class="center">
				<input id="optDipoyn_<?=$i?>" name="optDipoyn_<?=$i?>" type="radio" class="radio" value="Y" <? if($row['dipo_yn'] == 'Y'){ ?> checked <? } ?> onclick="deposit_chk(this,'Y','<?=$i?>');">입금
				<input id="optDipoyn_<?=$i?>" name="optDipoyn_<?=$i?>" type="radio"  class="radio" value="N" <? if($row['dipo_yn'] == 'N'){ ?> checked <? } ?> onclick="deposit_chk(this,'N','<?=$i?>');">미입금
			</td>
			<td class="center last">
				<input id="optUseyn_Y_<?=$i?>" name="optUseyn_<?=$i?>" type="radio" class="radio" value="Y" <? if($row['use_yn'] == 'Y'){ ?> checked <? } ?>>사용
				<input id="optUseyn_N_<?=$i?>" name="optUseyn_<?=$i?>" type="radio"  class="radio" value="N" <? if($row['use_yn'] == 'N'){ ?> checked <? } ?>>미사용
			</td>
			<input id="code_<?=$i?>" name="code" type="hidden" value="<?=$row['org_no'];?>">
			<input id="seq_<?=$i?>" name="seq" type="hidden"   value="<?=$row['seq'];?>">
		</tr><?

		$tot_pay += $row['dipo_pay'];

	} ?>
	
	<tr style="background-color:#<?=$bgcolor;?>;">
		<td class="center" colspan="4" style="font-size:9pt; font-weight:bold;">총 합계</td>
		<td class="right ">
			<span id="lblTotalPay" style="font-size:9pt; font-weight:bold;"><?=number_format($tot_pay);?></span>
			<input id="txtTotalPay" name="txtTotalPay" type="hidden" value="<?=number_format($tot_pay);?>">
		</td>
		<td class="center ">&nbsp;</td>
		<td class="center last">&nbsp;</td>
	</tr>
<?
	$conn->row_free();
	
	unset($tot_pay);

	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orderBy = $_POST['orderBy'];
	$dipoYn  = $_POST['dipoYn'];
	//$cnt	 = $_POST['cnt'];
	$orgNo   = $_POST['orgNo'];
	$orgNm   = $_POST['orgNm'];

	$sql = 'SELECT sum(pay)
			  FROM homepage_request
			 WHERE dipo_yn = \'Y\'
			 AND   hp_gbn = \'new\'';
	
	$DIPO_TOT = $conn -> get_data($sql); 

	$sql = 'SELECT	mst.addr
			,		mst.org_nm
			,		tb.org_no
			,		tb.seq
			,		tb.pay
			,		tb.dipo_yn
			,		left(tb.insert_dt, 10) as dt
			FROM	homepage_request AS tb
			INNER	JOIN (		
						SELECT	DISTINCT
								m00_mcode AS org_no
						,	    m00_store_nm AS org_nm
						,		concat(m00_caddr1,\' \',m00_caddr2) as addr
						FROM	m00center
					) AS mst
					ON		mst.org_no = tb.org_no
			WHERE seq = (select max(seq) from homepage_request as tmp where tmp.org_no = tb.org_no)
			AND hp_gbn = \'new\'
			AND del_flag = \'N\'';
	
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

	//정렬
	if ($orderBy == '1'){
		$sql .= ' ORDER	BY tb.insert_dt DESC';
	}else if ($orderBy == '2'){
		$sql .= ' ORDER	BY mst.org_nm';
	}			

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$DIPO_CNT = 0;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($i % 2 == 0){
			$bgcolor = 'FFFFFF';
		}else{
			$bgcolor = 'EFEFEF';
		}?>
		<tr style="background-color:#<?=$bgcolor;?>;">
			<td class="center"><?=$i+1;?></td>
			<td class="center"><?=str_replace('-','.',$row['dt']);?></td>
			<td class="left"><?=$row['org_no'];?></td>
			<td class="left"><div class="nowrap" style="width:110px;"><?=$row['org_nm'];?></div></td>
			<td class="left"><input id="txtDipopay_<?=$i?>" name="txtDipopay_<?=$i?>" type="text" onchange="deposit_tot(this);" value="<?=number_format($row['pay']);?>" style="width:100%; text-align:right;"></td>
			<td class="center">
				<input id="optDipoyn_<?=$i?>" name="optDipoyn_<?=$i?>" type="radio" class="radio" value="Y" <? if($row['dipo_yn'] == 'Y'){ ?> checked <? } ?> onclick="deposit_chk(this,'Y','<?=$i?>');">입금
				<input id="optDipoyn_<?=$i?>" name="optDipoyn_<?=$i?>" type="radio"  class="radio" value="N" <? if($row['dipo_yn'] == 'N'){ ?> checked <? } ?> >미입금
			</td>
			<td class="left "><div class="nowrap" style="width:185px;"><?=$row['addr'];?></div></td>
			<td class="left last"><span id="btnSave" class="btn_pack m" ><button onclick="lfDel('<?=$row['org_no']?>','<?=$row['seq']?>');">삭제</button></span></td>
			<input id="code_<?=$i?>" name="code" type="hidden" value="<?=$row['org_no'];?>">
			<input id="seq_<?=$i?>" name="seq" type="hidden"   value="<?=$row['seq'];?>">
		</tr><?
		
		
		if($row['dipo_yn'] == 'Y'){
			$DIPO_CNT ++;	
		}
	}
	
	$conn->row_free();
?>
	<tr style="background-color:#<?=$bgcolor;?>;">
		<td class="center" colspan="5" style="font-size:9pt; font-weight:bold;">총 합계</td>
		<td class="right ">
			<span id="lblTotalPay" ><?=number_format($tot_pay);?></span>
			<input id="txtTotalPay" name="txtTotalPay" type="hidden" value="<?=number_format($tot_pay);?>">
		</td>
		<td colspan="3" class="right last" style="font-size:9pt; font-weight:bold;">입금건수: <?=number_format($DIPO_CNT);?> 대&nbsp; 입금액 : <?=number_format($DIPO_TOT);?> 원</td>
	</tr>
<?
	include_once('../inc/_db_close.php');
?>
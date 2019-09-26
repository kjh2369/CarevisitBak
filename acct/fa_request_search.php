<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orderBy = $_POST['orderBy'];
	$orgNo  = $_POST['orgNo'];
	$orgNm  = $_POST['orgNm'];
	
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
			,		sr.use_yn
			,	    sr.type
			,		left(sr.insert_dt, 10) as dt
			,		mst.addr

			FROM	seminar_request AS sr
			INNER	JOIN (
						SELECT	DISTINCT
								m00_mcode AS org_no
						,		concat (m00_caddr1, \' \', m00_caddr2) AS addr
						FROM	m00center
					) AS mst
					ON		mst.org_no = sr.org_no';
	
	$sql .= ' AND del_flag = \'N\'
	          AND gbn = \'9\'';
	
	
	//기관코드
	if($orgNo != '') $sql .= ' AND sr.org_no >= \''.$orgNo.'\'';

	//기관명
	if($orgNm != '') $sql .= ' AND sr.org_nm like \'%'.$orgNm.'%\'';

	
	if($orgNo == '' && $orgNm == ''){
		if ($orderBy == '1'){
			$sql .= ' ORDER	BY insert_dt DESC';
		}else if ($orderBy == '2'){
			$sql .= ' ORDER	BY org_nm';
		}
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
			<td class="left"><div class="nowrap" style="width:150px;"><?=$row['org_nm'];?></div></td>
			<td class="left "><div class="nowrap" style="width:250px;"><?=$row['addr'];?></div></td>
			<td class="center">
				<input id="optUseYn_<?=$i?>" name="optUseYn_<?=$i?>" type="radio" class="radio" value="Y" <? if($row['use_yn'] == 'Y'){ ?> checked <? } ?> onclick="deposit_chk(this,'Y','<?=$i?>');">이용
				<input id="optUseYn_<?=$i?>" name="optUseYn_<?=$i?>" type="radio"  class="radio" value="N" <? if($row['use_yn'] == 'N'){ ?> checked <? } ?> >미이용
			</td>
			<td class="left last"><span id="btnSave" class="btn_pack m" ><button onclick="lfDel('<?=$row['org_no']?>','<?=$row['seq']?>');">삭제</button></span></td>
			<input id="code_<?=$i?>" name="code" type="hidden" value="<?=$row['org_no'];?>">
			<input id="seq_<?=$i?>" name="seq" type="hidden"   value="<?=$row['seq'];?>">
		</tr><?

		$org_cnt += 1;
		
	}

	$conn->row_free();

	
	?>

	<tr style="background-color:#EFEFEF;">
		<td class="center" colspan="6" style="font-size:9pt; font-weight:bold;">총 합계</td>
		<td class="right" ><?=number_format($org_cnt);?> 건</td>
	</tr>
	
<?
	unset($tot_pay);

	include_once('../inc/_db_close.php');
?>

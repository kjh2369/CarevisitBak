<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$orgNo	= $_SESSION['userCenterCode'];
	$IPIN	= $_POST['IPIN'];
?>
<div class="title title_border">
	<div style="float:left; width:auto;">이용신청서(<?=$title;?>)</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="">저장</button></span>
		<span class="btn_pack m"><span class="pdf"></span><button type="button" class="bold" onclick="">출력</button></span>
	</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">신청일</th>
			<td class="last"><input id="txtDate" type="text" class="date" value=""></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="100px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last" colspan="4">신청인</th>
		</tr>
		<tr>
			<th class="center">성명</th>
			<td></td>
			<th class="center">주민번호</th>
			<td class="last"></td>
		</tr>
		<tr>
			<th class="center">이용자와의 관계</th>
			<td></td>
			<th class="center">연락처</th>
			<td class="last"></td>
		</tr>
		<tr>
			<th class="center">주소</th>
			<td class="last" colspan="3"></td>
		</tr>

		<tr>
			<th class="bold last" colspan="4">서비스 이용대상자</th>
		</tr>
		<tr>
			<th class="center">성명</th>
			<td></td>
			<th class="center">주민번호</th>
			<td class="last"></td>
		</tr>
		<tr>
			<th class="center">주소</th>
			<td class="last" colspan="3"></td>
		</tr>
		<tr>
			<th class="center">이용희망기간</th>
			<td class="last" colspan="3"></td>
		</tr>
		<tr>
			<th class="center">희망이용<br>서비스</th>
			<td class="bottom last" colspan="3">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
					</colgroup>
					<tbody><?
						if ($r['svc_req']){
							$tmp = Explode('/',$r['svc_req']);
							foreach($tmp as $t){
								$s = Explode(':',$t);
								$arr[$s[0]] = $s[1];
							}
						}

						$sql = 'SELECT	DISTINCT
										care.suga_cd AS cd
								,		suga.nm1 AS mst_nm
								,		suga.nm2 AS pro_nm
								,		suga.nm3 AS svc_nm
								FROM	care_suga AS care
								INNER	JOIN	suga_care AS suga
										ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
										AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
										AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
								WHERE	care.org_no	= \''.$orgNo.'\'
								AND		care.suga_sr= \''.$sr.'\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();
						$idx = 1;

						$tmpStr1 = '';
						$tmpStr2 = '';

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);

							if ($tmpStr1 != SubStr($row['cd'],0,1)){
								$tmpStr1  = SubStr($row['cd'],0,1);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:20px;"><?=SubStr($row['cd'],0,1).'. '.Str_Replace('<br>','',$row['mst_nm']);?></th>
								</tr><?
							}

							if ($tmpStr2 != SubStr($row['cd'],0,3)){
								$tmpStr2  = SubStr($row['cd'],0,3);
								$idx = 1;?>
								<tr>
									<th class="last" style="padding-left:35px;"><?=SubStr($row['cd'],1,2).'. '.Str_Replace('<br>','',$row['pro_nm']);?></th>
								</tr><?
							}

							if ($idx % 3 == 1){?>
								<tr><td class="last" style="padding-left:50px;"><?
							}?>
							<div style="float:left; width:30%;"><label><input id="chkSvcReq_<?=$row['cd'];?>" name="chkSvcReq" type="checkbox" class="checkbox" value="<?=$row['cd'];?>" <?=$arr[$row['cd']] == 'Y' ? 'checked' : '';?>><?=$row['svc_nm'];?></label></div><?

							if ($idx == 3){
								$idx = 1;?>
								</td></tr><?
							}else{
								$idx ++;
							}
						}

						$conn->row_free();
						Unset($arr);?>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="center">이용사유</th>
			<td class="last" colspan="3"></td>
		</tr>

		<tr>
			<th class="bold last" colspan="4">구비서류</th>
		</tr>
		<tr>
			<td class="left last" colspan="4">
				1.주민등록증 또는 주민등록증(복사본) 1부.<br>
				2.건강보험료 납입확인서 1부(기초새활 수급대상자의 경우 의료보호증 또는 수급자 확인서).<br>
				3.장기요양등급 판정표(해당자에 한함).<br>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
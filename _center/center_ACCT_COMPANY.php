<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	a.svc_gbn
			,		a.svc_cd AS main_cd
			,		a.svc_nm AS main_nm
			,		b.svc_cd AS sub_cd
			,		b.svc_nm AS sub_nm
			FROM	(
					SELECT	\'1\' AS svc_gbn
					,		svc_cd
					,		svc_nm
					FROM	cv_svc_main
					WHERE	parent_cd IS NULL
					UNION	ALL
					SELECT	\'2\'
					,		svc_cd
					,		svc_nm
					FROM	cv_svc_sub
					WHERE	parent_cd IS NULL
					) AS a
			INNER	JOIN (
					SELECT	\'1\' AS svc_gbn
					,		svc_cd
					,		svc_nm
					,		parent_cd
					FROM	cv_svc_main
					WHERE	parent_cd IS NOT NULL
					UNION	ALL
					SELECT	\'2\'
					,		svc_cd
					,		svc_nm
					,		parent_cd
					FROM	cv_svc_sub
					WHERE	parent_cd IS NOT NULL
					) AS b
					ON		b.svc_gbn	= a.svc_gbn
					AND		b.parent_cd = a.svc_cd';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$width = 360;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!$svc[$row['svc_gbn'].'_'.$row['main_cd']]){
			$svc[$row['svc_gbn'].'_'.$row['main_cd']]['name'] = $row['main_nm'];
		}
		$svc[$row['svc_gbn'].'_'.$row['main_cd']]['list'][] = Array('gbn'=>$row['svc_gbn'], 'cd'=>$row['sub_cd'], 'nm'=>$row['sub_nm']);
		$svc[$row['svc_gbn'].'_'.$row['main_cd']]['cnt'] ++;

		$width += 90;
	}

	$conn->row_free();
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();

		$('#ID_FOOT_SCROLL').scroll(function(){
			$('#ID_HEAD_CAPTION').scrollLeft($(this).scrollLeft());
			$('#ID_BODY_LIST').scrollLeft($(this).scrollLeft());
		});
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_ACCT_COMPANY_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'year':$('#yymm').attr('year')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				if (!data){
					$('#tempLodingBar').remove();
					return;
				}

				var row = data.split('?');

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);

						for(var j in col){
							$('#ID_CELL_'+j).text(col[j]);
						}
					}
				}

				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="40px">
		<col width="80px">
		<col>
		<col width="80px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">월별</th>
			<th class="head">기관수</th>
			<th class="head">청구금액</th>
			<th class="head" style="text-align:left;">
				<div id="ID_HEAD_CAPTION" style="width:620px; overflow-x:hidden;">
					<table class="my_table" style="width:<?=$width;?>;">
						<colgroup>
							<col width="90px" span="4"><?
							foreach($svc as $tmpCD => $R){?>
								<col width="90px" span="<?=$R['cnt'];?>"><?
							}?>
						</colgroup>
						<thead>
							<tr>
								<th class="head" colspan="2">청구내역</th>
								<th class="head" colspan="2">입금내역</th><?
								foreach($svc as $tmpCD => $R){?>
									<th class="head last" style="border-left:1px solid #a6c0f3;" colspan="<?=$R['cnt'];?>"><?=$R['name'];?></th><?
								}?>
							</tr>
							<tr>
								<th class="head bottom">당월분</th>
								<th class="head bottom">미납분</th>
								<th class="head bottom">입금</th>
								<th class="head bottom">미납</th><?
								foreach($svc as $tmpCD => $R1){
									foreach($R1['list'] as $tmpIdx => $R){?>
										<th class="head bottom last" style="border-left:1px solid #a6c0f3;"><?=$R['nm'];?></th><?
									}
								}?>
							</tr>
						</thead>
					</table>
				</div>
			</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		for($i=1; $i<=12; $i++){?>
			<tr>
				<td class="center"><?=$i;?>월</td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_1" class="right"></div></td>
				<td class="center"><div id="ID_CELL_<?=$i;?>_2" class="right"></div></td><?
				if ($i == 1){?>
					<td class="center top" rowspan="12">
						<div id="ID_BODY_LIST" style="width:620px; margin-left:-1px; margin-top:-1px; overflow-x:hidden;">
							<table class="my_table" style="width:<?=$width;?>;">
								<colgroup>
									<col width="90px" span="4"><?
									foreach($svc as $tmpCD => $R){?>
										<col width="90px" span="<?=$R['cnt'];?>"><?
									}?>
								</colgroup>
								<tbody><?
									for($j=1; $j<=12; $j++){?>
										<tr>
											<td class="center <?=$j == 12 ? 'bottom' : '';?>"><div id="ID_CELL_<?=$j;?>_3" class="right">0</div></td>
											<td class="center <?=$j == 12 ? 'bottom' : '';?>"><div id="ID_CELL_<?=$j;?>_4" class="right">0</div></td>
											<td class="center <?=$j == 12 ? 'bottom' : '';?>"><div id="ID_CELL_<?=$j;?>_5" class="right">0</div></td>
											<td class="center <?=$j == 12 ? 'bottom' : '';?>"><div id="ID_CELL_<?=$j;?>_6" class="right">0</div></td><?
											foreach($svc as $tmpCD => $R1){
												foreach($R1['list'] as $tmpIdx => $R){?>
													<td class="center last <?=$j == 12 ? 'bottom' : '';?>" style="border-left:1px solid #cccccc;">
														<div id="ID_CELL_<?=$j;?>_<?=$R['gbn'];?>_<?=$R['cd'];?>" class="right">0</div>
													</td><?
												}
											}?>
										</tr><?
									}?>
								</tbody>
							</table>
						</div>
					</td><?
				}?>
				<td class="center last"></td>
			</tr><?
		}?>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last" colspan="3">&nbsp;</td>
			<td class="top bottom last">
				<div id="ID_FOOT_SCROLL" style="width:620px; overflow-x:scroll;">
					<img style="width:<?=$width;?>px; height:1px;"><br>
				</div>
			</td>
		</tr>
	</tfoot>
</table>
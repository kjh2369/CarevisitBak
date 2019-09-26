<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$orgNo = $_SESSION['userCenterCode'];
	$re_gbn = $_GET['re_gbn'];
	$idx = $_GET['idx'];
	$year = $_GET['year'];
	$gwan = $_GET['gwan'];
	$hang = $_GET['hang'];
	$mog = $_GET['mog'];
	$flag = $_GET['flag'];
	$reFun = $_GET['reFun'];
	
	if($re_gbn == 'E'){
		$title = '세촐';
	}else {
		$title = '세입';
	}


	if (!$year) $year = Date('Y');
	//if (SubStr($bodyid, 0, 1) != '#') $bodyid = '#'.$bodyid;
	
	if ($reFun == 'lfSetTgtAcctCd'){
		$drawGbn = 1;

		$sql = 'SELECT	gwan_cd, hang_cd, mog_cd, amt
				FROM	fa_budget
				WHERE	org_no	= \''.$orgNo.'\'
				AND		year	= \''.$year.'\'
				AND		re_gbn	= \''.$re_gbn.'\'
				';
		
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$budget[$row['gwan_cd']][$row['hang_cd']][$row['mog_cd']] = $row['amt'];
		}

		$conn->row_free();

		$sql = 'SELECT	gwan_cd, hang_cd, mog_cd, SUM(ab_amt) AS amt
				FROM	fa_acctbk
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		re_gbn	 = \''.$re_gbn.'\'
				AND		del_flag = \'N\'
				AND		LEFT(ab_dt, 4) = \''.$year.'\'
				GROUP	BY gwan_cd, hang_cd, mog_cd
				';
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$acctbk[$row['gwan_cd']][$row['hang_cd']][$row['mog_cd']] = $row['amt'];
		}

		$conn->row_free();
	}else{
		$drawGbn = 2;
	}
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		__init_form(document.f);
		
		
		$('#lblTitle').text('<?=$title;?>'+'계정 코드');

	});

	function lfResize(){
		var h = $(this).height();
		var t = $('#list').offset().top;

		h = h - t;

		$('#list').height(h);
	}

	function lfSelect(obj){
		opener.result = true;
	
		if('<?=$reFun;?>'=='lfSetAcctCd' || '<?=$reFun;?>'=='lfSetNotAcctCd' || '<?=$reFun;?>'=='lfSetTgtAcctCd'){
			
			opener.gwan = $(obj).attr('gwan_cd');
			opener.hang = $(obj).attr('hang_cd');
			opener.mog  = $(obj).attr('mog_cd');
			opener.gwan_name  = $(obj).attr('gwan_name');
			opener.hang_name  = $(obj).attr('hang_name');
			opener.mog_name  = $(obj).attr('mog_name');
			opener.acct_name  = $(obj).attr('mog_name');
			opener.stnd_flag  = $(obj).attr('stnd_flag');
			opener.tgt_gwan = $(obj).attr('tgt_gwan_cd');
			opener.tgt_hang = $(obj).attr('tgt_hang_cd');
			opener.tgt_mog  = $(obj).attr('tgt_mog_cd');
			opener.tgt_stnd_flag  = $(obj).attr('tgt_stnd_flag');
			opener.tgt_name  = $(obj).attr('tgt_name');
			opener.obj =  obj;
			
			self.close();
		}else {
			if (!confirm('상대계정을 적용하시겠습니까?')) return;
				opener.nm1 = $(obj).attr('mog_name');
			var data = {
				'gwan_cd':'<?=$gwan;?>'
			,	'hang_cd':'<?=$hang;?>'
			,	'mog_cd':'<?=$mog;?>'
			,	'stnd_flag':'<?=$flag;?>'
			,	'tgt_gwan_cd':$(obj).attr('gwan_cd')
			,	'tgt_hang_cd':$(obj).attr('hang_cd')
			,	'tgt_mog_cd':$(obj).attr('mog_cd')
			,	'tgt_stnd_flag':$(obj).attr('stnd_flag')
			};
			
			
			$.ajax({
				type:'POST'
			,	url:'../fa/item_tgt_save.php'
			,	data:data
			,	beforeSend:function(){
				}
			,	success:function(rst){
					
					alert('정상적으로 처리되었습니다');
					opener.nm1 = $(obj).attr('mog_name');
					
					self.close();
					
				}
			,	error:function(e){
					alert('ERROR\n'+e);
				}
			}).responseXML;
		}

	}


</script>

<base target="_self">
<form name="f">
<div id="lblTitle" class="title title_border">&nbsp;</div>
<table class="my_table" style="width:100%;"><?
	$colgrp = '
	<col width="50px">
	<col width="150px">
	<col width="50px">
	<col width="150px">
	<col width="50px">
	<col width="150px">';

	if ($drawGbn == 1){
		$colgrp .= '<col width="100px"><col width="100px"><col width="120px">';
	}else{
		$colgrp .= '<col width="320px">';
	}
	$colgrp .= '<col >';

	?>
	<colgroup><?=$colgrp;?></colgroup>
	<tbody>
		<tr>
			<th class="head" colspan="6">과목</th><?
			if ($drawGbn == 1){?>
				<th class="head" rowspan="3">예산</th>
				<th class="head" rowspan="3">세입</th>
				<th class="head" rowspan="3">차액</th><?
			}else{?>
				<th class="head" rowspan="3">내역</th><?
			}?>
			<th class="head last" rowspan="3">비고</th>
		</tr>
		<tr>
			<th class="head" colspan="2">관</th>
			<th class="head" colspan="2">항</th>
			<th class="head" colspan="2">목</th>
		</tr>
		<tr>
			<th class="head" >코드</th>
			<th class="head" >항목</th>
			<th class="head" >코드</th>
			<th class="head" >항목</th>
			<th class="head" >코드</th>
			<th class="head" >항목</th>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="top center last" colspan="<?=($drawGbn==1?'10':'8');?>">
				<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:380px;">
					<table id="TBL_ACCTCD" class="my_table" style="width:100%;">
						<colgroup><?=$colgrp;?></colgroup>
						<tbody>
						
						<?
							
							$sql = 'SELECT	gwan_cd, hang_cd, mog_cd, gwan_name, hang_name, mog_name, dtl_txt, \'Y\' AS stnd_flag, tgt_gwan_cd, tgt_hang_cd, tgt_mog_cd, tgt_stnd_flag
									FROM	fa_item
									WHERE	re_gbn	 = \''.$re_gbn.'\'
									AND		use_flag = \'Y\'
									AND		LEFT(apply_dt, 4) <= \''.$year.'\'
									UNION	ALL
									SELECT	a.gwan_cd, a.hang_cd, a.org_mog_cd, b.gwan_name, b.hang_name, a.mog_name, a.dtl_txt, \'N\' AS stnd_flag, a.tgt_gwan_cd, a.tgt_hang_cd, a.tgt_mog_cd, a.tgt_stnd_flag
									FROM	fa_item_org AS a
									INNER	JOIN	fa_item AS b
											ON		b.gwan_cd	= a.gwan_cd
											AND		b.hang_cd	= a.hang_cd
											AND		b.mog_cd	= a.mog_cd
											AND		b.re_gbn	= a.re_gbn
									WHERE	a.org_no = \''.$orgNo.'\'
									AND		a.re_gbn = \''.$re_gbn.'\'
									AND		LEFT(a.from_dt, 4) <= \''.$year.'\'
									AND		LEFT(a.to_dt, 4) >= \''.$year.'\'
									AND     a.del_flag = \'N\'
									ORDER	BY gwan_cd, hang_cd, mog_cd
									';
							$conn->query($sql);
							$conn->fetch();

							$rowCnt = $conn->row_count();

							for($i=0; $i<$rowCnt; $i++){
								$row = $conn->select_row($i);

								if ($row['tgt_gwan_cd'] && $row['tgt_hang_cd'] && $row['tgt_mog_cd']){
									if (!$tgt_name[$re_gbn][$row['tgt_gwan_cd']][$row['tgt_hang_cd']][$row['tgt_mog_cd']]){
										if ($row['tgt_stnd_flag'] == 'Y'){
											$sql = 'SELECT	mog_name
													FROM	fa_item
													WHERE	gwan_cd	= \''.$row['tgt_gwan_cd'].'\'
													AND		hang_cd	= \''.$row['tgt_hang_cd'].'\'
													AND		mog_cd	= \''.$row['tgt_mog_cd'].'\'
													AND		re_gbn	= \'R\'
													';
										}else{
											$sql = 'SELECT	mog_name
													FROM	fa_item_org
													WHERE	org_no		= \''.$orgNo.'\'
													AND		gwan_cd		= \''.$row['tgt_gwan_cd'].'\'
													AND		hang_cd		= \''.$row['tgt_hang_cd'].'\'
													AND		org_mog_cd	= \''.$row['tgt_mog_cd'].'\'
													AND		re_gbn		= \'R\'
													AND     del_flag = \'N\'
													';
										}
										$tgt_name[$re_gbn][$row['tgt_gwan_cd']][$row['tgt_hang_cd']][$row['tgt_mog_cd']] = $conn->get_data($sql);
									}
								}

								if (!$data[$row['gwan_cd']])
									 $data[$row['gwan_cd']] = Array('name'=>$row['gwan_name'], 'rows'=>0, 'LIST'=>Array());
								if (!$data[$row['gwan_cd']]['LIST'][$row['hang_cd']])
									 $data[$row['gwan_cd']]['LIST'][$row['hang_cd']] = Array('name'=>$row['hang_name'], 'rows'=>0, 'LIST'=>Array());

								$data[$row['gwan_cd']]['rows'] ++;
								$data[$row['gwan_cd']]['LIST'][$row['hang_cd']]['rows'] ++;
								$data[$row['gwan_cd']]['LIST'][$row['hang_cd']]['LIST'][$row['mog_cd']] = Array(
									'name'=>$row['mog_name']
								,	'dtl_txt'=>$row['dtl_txt']
								,	'stnd_flag'=>$row['stnd_flag']
								,	'tgt_gwan_cd'=>$row['tgt_gwan_cd']
								,	'tgt_hang_cd'=>$row['tgt_hang_cd']
								,	'tgt_mog_cd'=>$row['tgt_mog_cd']
								,	'tgt_stnd_flag'=>$row['tgt_stnd_flag']
								,	'tgt_name'=>$tgt_name[$re_gbn][$row['tgt_gwan_cd']][$row['tgt_hang_cd']][$row['tgt_mog_cd']]
								);
							}

							$conn->row_free();

							foreach($data as $gwan_cd => $gwan){
								foreach($gwan['LIST'] as $hang_cd => $hang){
									foreach($hang['LIST'] as $mog_cd => $mog){?>
										<tr gwan_cd="<?=$gwan_cd;?>" hang_cd="<?=$hang_cd;?>" mog_cd="<?=$mog_cd;?>" gwan_name="<?=$gwan['name'];?>" hang_name="<?=$hang['name'];?>" mog_name="<?=$mog['name'];?>" stnd_flag="<?=$mog['stnd_flag'];?>" tgt_gwan_cd="<?=$mog['tgt_gwan_cd'];?>" tgt_hang_cd="<?=$mog['tgt_hang_cd'];?>" tgt_mog_cd="<?=$mog['tgt_mog_cd'];?>" tgt_stnd_flag="<?=$mog['tgt_stnd_flag'];?>" tgt_name="<?=$mog['tgt_name'];?>" style="cursor:pointer;"><?
										if ($gwan['rows'] > 0){?>
											<td style="vertical-align:top;" rowspan="<?=$gwan['rows'];?>"><div class="left"><?=$gwan_cd;?></div></td>
											<td style="vertical-align:top;" rowspan="<?=$gwan['rows'];?>"><div class="left"><?=$gwan['name'];?></div></td><?
										}
										if ($hang['rows'] > 0){?>
											<td style="vertical-align:top;" rowspan="<?=$hang['rows'];?>"><?=$hang_cd;?></td>
											<td style="vertical-align:top;" rowspan="<?=$hang['rows'];?>"><div class="left"><?=$hang['name'];?></div></td><?
										}?>
										<td class="center" style="vertical-align:top;"><?=$mog_cd;?></td>
										<td class="center" id="acct_name" style="vertical-align:top;"><div class="left"><?=$mog['name'];?></div></td><?
										if ($drawGbn == 1){?>
											<td class="center" ><div class="left"><?=number_format($budget[$gwan_cd][$hang_cd][$mog_cd]);?></div></td>
											<td class="center" ><div class="left"><?=number_format($acctbk[$gwan_cd][$hang_cd][$mog_cd]);?></div></td>
											<td class="center" ><div class="left"><?=number_format($budget[$gwan_cd][$hang_cd][$mog_cd] - $acctbk[$gwan_cd][$hang_cd][$mog_cd]);?></div></td><?
										}else{?>
											<td class="center" style="white-space: pre-wrap;"><div class="left"><?=$mog['dtl_txt'];?></div></td><?
										}?>
										<td class="last" ><div class="left"><a href="#" onclick="lfSelect($(this).parent().parent().parent());">선택</a></div></td>
										</tr><?

										$gwan['rows'] = 0;
										$hang['rows'] = 0;
									}
								}
							}?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
</form>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<?
	include_once('../inc/_footer.php');
?>
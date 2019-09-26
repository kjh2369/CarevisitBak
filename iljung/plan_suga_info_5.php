<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	//등급판단
	$sql = 'SELECT	COUNT(*)
			FROM	client_his_lvl
			WHERE	org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		svc_cd	= \'0\'
			AND		level	= \'9\'
			AND		LEFT(REPLACE(from_dt,\'-\',\'\'),6) <= \''.$year.$month.'\'
			AND		LEFT(REPLACE(to_dt,\'-\',\'\'),6)	>= \''.$year.$month.'\'';

	$IsLvlNormal = $conn->get_data($sql);

	//주야간보호?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col width="83px">
			<col width="60px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th>시작시간</th>
				<td>
					<input id="txtFromH" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;"> :
					<input id="txtFromM" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">
				</td>
				<th rowspan="2">
					<div>비급여</div>
					<div id="lblNpmtAmt" class="right" style="color:RED;">0</div>
				</th>
				<td class="last" rowspan="2">
					<div style="height:50px; overflow-x:hidden; overflow-y:scroll;"><?
						$sql = 'SELECT	a.code
								,		a.name
								,		CASE WHEN b.cost IS NOT NULL THEN b.cost ElSE a.cost END AS cost
								,		CASE WHEN b.cost IS NOT NULL THEN \'Y\' ElSE \'N\' END AS set_yn
								,		a.suga_yn
								FROM	dan_nonpayment AS a
								LEFT	JOIN	dan_nonpayment_client AS b
										ON		b.org_no	= a.org_no
										AND		b.jumin		= \''.$jumin.'\'
										AND		b.code		= a.code
										AND		b.base_seq	= a.seq
										AND		b.del_flag	= \'N\'
										AND		DATE_FORMAT(b.from_dt,	\'%Y%m\') <= \''.$year.$month.'\'
										AND		DATE_FORMAT(b.to_dt,	\'%Y%m\') >= \''.$year.$month.'\'
								WHERE	a.org_no = \''.$code.'\'
								AND		a.use_yn = \'Y\'
								AND		DATE_FORMAT(a.from_dt,	\'%Y%m\') <= \''.$year.$month.'\'
								AND		DATE_FORMAT(a.to_dt,	\'%Y%m\') >= \''.$year.$month.'\'
								ORDER	BY name';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<div style="float:left; width:auto;">
								<table>
									<tr><td style="height:20px; border:1px solid WHITE;"><?
									if ($row['suga_yn'] == 'Y'){?>
										<label style="margin-right:10px;"><input id="chkNpmtSuga_<?=$row['code'];?>" type="checkbox" class="checkbox" value="<?=$row['cost'];?>" <?=$row['set_yn'] == 'Y' ? 'checked' : '';?> onclick="lfSetBipaySuga(this);"><?=$row['name'];?> : <?=number_format($row['cost']);?></label><?
									}else{?>
										<label style="margin-right:10px;"><input id="chkNpmt_<?=$row['code'];?>" type="checkbox" class="checkbox" value="<?=$row['cost'];?>" <?=$row['set_yn'] == 'Y' ? 'checked' : '';?>><?=$row['name'];?> : <?=number_format($row['cost']);?></label><?
									}?>
									</td></tr>
								</table>
							</div><?
						}

						$conn->row_free();?>
						<script type="text/javascript">
							function lfSetBipaySuga(obj){
								var cost = __str2num($('#loSuga').attr('cost'));

								if ($(obj).attr('checked')){
									cost += __str2num($(obj).val());
								}else{
									cost -= __str2num($(obj).val());
								}

								$('#loSuga').attr('cost',cost);
								$('#lblCost').text(__num2str(cost));

								var amt = 0;

								amt += __str2num($('#lblCost').text());
								amt += __str2num($('#lblEvening').text());

								$('#lblTotal').text(__num2str(amt));
								$('#loSuga').attr('costTotal',amt);
							}

							function lfSetNormalSuga(obj){
								var cost = __str2num($(obj).val());

								$('#lblCost').text(__num2str(cost));
								$('#lblTotal').text(__num2str(cost));

								$('.clsLvlList').each(function(){
									$('#loSugaLvl'+$(this).attr('lvl')).attr('cost',cost).attr('costTotal',cost).attr('costSat',cost).attr('costHoliday',cost);
								});
							}
						</script>
					</div>
				</td>
			</tr>
			<tr>
				<th>종료시간</th>
				<td>
					<input id="txtToH" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;"> :
					<input id="txtToM" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">
				</td>
			</tr>
			<tr>
				<th class="bottom">적용수가</th>
				<td class="left bottom last" colspan="3"><?
					if ($IsLvlNormal > 0){?>
						<span>수가</span>[<span id="lblCost" style="display:none;">0</span><input id="txtTotal" type="text" value="0" class="number" style="width:60px" onchange="lfSetNormalSuga(this);">] +
						<span>연장</span>[<span id="lblEvening" class="bold" style="color:BLUE;">0</span>] =
						<span>합계</span>[<span id="lblTotal" class="bold" style="color:RED;">0</span>]<?
					}else{?>
						<span>수가</span>[<span id="lblCost" class="bold">0</span>] +
						<span>연장</span>[<span id="lblEvening" class="bold" style="color:BLUE;">0</span>] =
						<span>합계</span>[<span id="lblTotal" class="bold" style="color:RED;">0</span>]<?
					}?>&nbsp;/
					<span id="lblFullname"></span>
					<span id="lblCostSat" style="display:none;">0</span>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="loSvcInfo" style="display:none;"></div>
	<div id="ID_LVL_NORMAL" style="display:none;"><?=$IsLvlNormal > 0 ? 'Y' : 'N';?></div>
	<div id="loSugaLvl1" style="display:none;"></div>
	<div id="loSugaLvl2" style="display:none;"></div>
	<div id="loSugaLvl3" style="display:none;"></div>
	<div id="loSugaLvl4" style="display:none;"></div>
	<div id="loSugaLvl5" style="display:none;"></div>
	<div id="loSugaLvl9" style="display:none;"></div>
	<div id="loSugaLvlA" style="display:none;"></div>

	<script type="text/javascript">
		//방문시간 변경 이벤트
		$('input:text[name="txtTime"]').unbind('keyup').keyup(function(){
			var lsPayKind = $('#txtPayKind').val();

			if ($(this).val().length == $(this).attr('maxlength')){
				//시간 초과시 변경
				if ($(this).attr('id') == 'txtFromH' || $(this).attr('id') == 'txtToH'){
					var liVal = __str2num($(this).val());

					if (liVal >= 24){
						liVal = liVal % 24;
					}

					liVal = (liVal < 10 ? '0' : '')+liVal;

					$(this).val(liVal);
				}

				//분 초과시 변경
				if ($(this).attr('id') == 'txtFromM' || $(this).attr('id') == 'txtToM'){
					var liVal = __str2num($(this).val());

					if (liVal >= 60){
						liVal = 0;
					}

					liVal = (liVal < 10 ? '0' : '')+liVal;

					$(this).val(liVal);
				}

				if ($(this).attr('id') == 'txtFromH'){
					$('#txtFromM').focus();
					return;
				}else if ($(this).attr('id') == 'txtFromM'){
					$('#txtToH').focus();
					return;
				}else if ($(this).attr('id') == 'txtToH'){
					$('#txtToM').focus();
					return;
				}else if ($(this).attr('id') == 'txtToM'){
					lfSugaRow();
					return;
				}
			}
		}).unbind('change').change(function(){
		}).unbind('focus').focus(function(){
		}).unbind('blur').blur(function(){
			var lsSvcCd		= $('#planInfo').attr('svcCd');

			lfSetTimePos(this);

			if ($(this).attr('id') == 'txtToM'){
				lfSugaRow();
			}
		});

		$('input:checkbox[id^="chkNpmt_"]').unbind('click').bind('click',function(){
			lfSetNonpayment();
		});

		$(document).ready(function(){
			lfSetNonpayment();
			lbWinLoad = true;
		});

		function lfSetNonpayment(){
			var amt = 0;

			$('input:checkbox[id^="chkNpmt_"]:checked').each(function(){
				if ($(this).attr('checked')){
					amt += __str2num($(this).val());
				}
			});

			$('#lblNpmtAmt').text(__num2str(amt));
		}

		function lfSugaRow(){
			$('.clsLvlList').each(function(){
				lfFindSuga($(this).attr('lvl'));
			});
		}
	</script>
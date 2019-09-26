<?
	if ($head_load){
		$income_pos = '2';
		$income_border = '';
		$income_top    = 'margin-top:-1px;';
		$income_bottom = 'border-bottom:none;';
	}else{
		$income_pos = '1';
		$income_border = '';
		$income_top    = '';
		$income_bottom = '';
	}

	if ($mode == '1' || $mode == '3' || $mode == '4'){
		$colspan = '';
	}else{
		$colspan = 'colspan=3';
	}
?>
<table class="<? if(!$head_load){?>my_table my_border<?}else{?>my_table<?} ?>" style="<? if($head_load){?>width:100%;<?} ?>">
	<colgroup>
		<col width="40px">
		<col width="75px">
		<col span="2">
	</colgroup>
	<tbody>
		<tr>
		<?	if (!$head_load){?>
				<th class="head">년도</th>
				<td class="left last">
					<select name="year" style="width:auto; margin-left:0;"><?
						for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
							<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option><?
						}?>
					</select>년
				</td>
				<td class="left last">
				<?
					if ($mode != '2'){
						for($i=1; $i<=12; $i++){
							$class = 'my_month ';

							if ($i == intval($month)){
								$class .= 'my_month_y ';
								$text   = $i.'월';
							}else{
								$class .= 'my_month_1 ';
								$text   = '<span style="color:#7c7c7c;">'.$i.'월</span>';
							}

							if ($mode == '1'){
								$link = "<a href='#' onclick=\"_income_search('$i','$income_pos');\">$text</a>";
							}else if ($mode == '2'){
								$link = "<a href='#' onclick=\"_income_search('$i','');\">$text</a>";
							}else if ($mode == '3'){
								$link = "<a href='#' onclick=\"_income_modify('$i','');\">$text</a>";
							}else if ($mode == '4'){
								$link = "<a href='#' onclick=\"_income_delete('$i','');\">$text</a>";
							}

							$margin_right = '2px';

							if ($i == 12){
								$margin_right = '0';
							}?>
							<div class="<?=$class;?>" style="float:left; margin-right:<?=$margin_right;?>;"><?=$link;?></div><?
						}
					}
				?>
				</td><?
			}?>
			<td class="right last <?=$head_load ? 'bottom' : ''?>">
			<?
				if ($mode == '1'){?>
					<span class="btn_pack m"><button type="button" onclick="location.href='income_modify.php?io_type=<?=$io_type;?>&mode=3&find_center_code=<?=$code;?>&year=<?=$year;?>&month=<?=$month;?>';">수정</button></span>
					<span class="btn_pack m"><button type="button" onclick="location.href='income_delete.php?io_type=<?=$io_type;?>&mode=4&find_center_code=<?=$code;?>&year=<?=$year;?>&month=<?=$month;?>';">삭제</button></span><?
				}else if ($mode == '2'){?>
					<span class="btn_pack m"><button type="button" onclick="_income_reg_check();">등록</button></span><?
				}else if ($mode == '3'){?>
					<span class="btn_pack m"><button type="button" onclick="_income_reg_check();">수정</button></span><?
				}else if ($mode == '4'){?>
					<span class="btn_pack m"><button type="button" onclick="_income_reg_check();">삭제</button></span><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>

<!--
<table class="my_table my_border" style="<?=$income_border;?><?=$income_top;?><?=$income_bottom;?>">
	<colgroup>
		<?
			if (!$head_load){
			?>	<col width="60px"><?
			}
		?>
		<col>
		<col width="60px">
		<col width="177px">
		<col width="<?=$mode == '2' ? '220px' : '200px';?>">
	</colgroup>
	<tbody>
		<tr>
			<?
				if (!$head_load){
				?>	<th>기관명</th>
					<td <?=$colspan;?>>
					<?
						if ($find_admin){
						?>	<input name="find_center_name" type="text" value="<?=$find_center_name;?>" maxlength="20" onkeypress="if(event.keyCode==13){_list_center('<?=$page;?>');}" style="width:100%;" onFocus="this.select();"><?
						}else{	?>
							<span class="left"><?=$find_center_name;?></span>	<?
						}
					?>
					</td><?
				}else{
					if ($mode == '2'){	?>
						<td class="left bottom last" style="<?=$income_border;?>">&nbsp;</td>	<?
					}else{	?>
						<td class="left bottom <?=$mode != '1' ? 'last' : '';?>" style="<?=$income_border;?>">검색된 전체 갯수 : <?=$row_count;?></td>	<?
					}
				}

				if ($mode == '1' || (($mode == '3' || $mode == '4') && !$head_load)){	?>
					<th class="<?=$head_load?'bottom':'';?>" style="<?=$income_border;?>">조회기간</th>
					<td class="<?=$head_load?'bottom':'';?>" style="<?=$income_border;?>">
						<input name="find_from_date<?=$income_pos;?>" type="text" value="<?=$find_from_date;?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"> ~
						<input name="find_to_date<?=$income_pos;?>" type="text" value="<?=$find_to_date;?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);">
					</td>	<?
				}
			?>
			<td class="center last <?=$head_load?'bottom':'';?>" style="line-height:26px; vertical-align:top; padding-top:2px;">
			<?
				if ($mode == '1'){ ?>
					<span class="btn_pack m"><button type="button" onclick="_income_search('<?=$income_pos;?>');">조회</button></span>
					<span class="btn_pack m"><button type="button" onclick="_income_modify('<?=$income_pos;?>');">수정모드</button></span>
					<span class="btn_pack m"><button type="button" onclick="">삭제모드</button></span>	<?
				}else if ($mode == '2'){
					if (!$head_load){ ?>
						<span class="btn_pack m"><button type="button" onclick="_income_search('');">조회모드</button></span>
						<span class="btn_pack m"><button type="button" onclick="_income_modify('');">수정모드</button></span>
						<span class="btn_pack m"><button type="button" onclick="_income_delete('');">삭제모드</button></span>	<?
					}else{
						if ($account_firm_code != ''){?>
							<span class="btn_pack m"><button type="button" onclick="_income_reg_check();">등록</button></span>	<?
						}
					}
				}else if ($mode == '3'){
					if (!$head_load){	?>
						<span class="btn_pack m"><button type="button" onclick="_income_modify('1');">조회</button></span>
						<span class="btn_pack m"><button type="button" onclick="_income_reg();">등록모드</button></span>
						<span class="btn_pack m"><button type="button" onclick="_income_delete('1');">삭제모드</button></span>	<?
					}else{
						if ($account_firm_code){?>
							<span class="btn_pack m"><button type="button" onclick="_income_reg_check();">수정</button></span>	<?
						}
					}
				}else if ($mode == '4'){
					if (!$head_load){?>
						<span class="btn_pack m"><button type="button" onclick="_income_delete('1');">조회</button></span>
						<span class="btn_pack m"><button type="button" onclick="_income_reg();">등록모드</button></span>
						<span class="btn_pack m"><button type="button" onclick="_income_modify('1');">수정모드</button></span><?
					}else{
						if ($account_firm_code != ''){ ?>
							<span class="btn_pack m"><button type="button" onclick="_income_reg_check();">삭제</button></span><?
						}
					}
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
-->
<?
	$head_load = true;
?>
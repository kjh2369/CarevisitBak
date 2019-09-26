<?
	$temp_name = explode('/',$_SERVER['PHP_SELF']);
	$temp_name = $temp_name[sizeof($temp_name)-1];

	// 등급별 조회 조건 추가 여부
	if ($temp_name == 'supply_stat_month.php'){
		$level_flag = true;
		$return_uri = 'supply_stat.php';
	}else{
		$level_flag = false;
	}

	// 수급자 조회 조건 추가 여부
	if ($temp_name == 'supply_stat_day.php'){
		$client_flag = true;
		$client_name = $conn->client_name($_SESSION['userCenterCode'], $jumin);
		$return_uri  = 'supply_stat_month.php';
	}else{
		$client_flag = false;
	}
?>
<script language='javascript'>
<!--
function go_return(uri){
	var f = document.f;

	f.action = uri;
	f.submit();
}
-->
</script>
<table class="my_table my_border">
	<colgroup>
		<?
			if ($level_flag){?>
				<col width="60px">
				<col width="50px"><?
			}else if ($client_flag){?>
				<col width="60px">
				<col width="120px"><?
			}
		?>
		<col width="60px">
		<col width="85px">
		<col>
		<col width="60px">
	</colgroup>
	<tbody>
		<tr>
			<?
				if ($level_flag){?>
					<th class="center">등급</th>
					<td class="" style="padding-top:1px;">
						<select name="level" style="width:auto;" onchange="set_month('<?=$month;?>');">
							<option value="" <? if($level == ''){?>selected<?} ?>>전체</option><?
							$sql = "select m81_code, m81_name
									  from m81gubun
									 where m81_gbn = 'LVL'
									 order by m81_code";

							$conn->query($sql);
							$conn->fetch();
							$row_count = $conn->row_count();

							for($i=0; $i<$row_count; $i++){
								$row = $conn->select_row($i);?>
								<option value="<?=$row['m81_code'];?>" <? if($level == $row['m81_code']){?>selected<?} ?>><?=$row['m81_name'];?></option><?
							}

							$conn->row_free();
						?>
						</select>
					</td><?
				}else if ($client_flag){?>
					<th class="center">수급자</th>
					<td class="left" style="padding-top:1px;">
						<span class="btn_pack find" onClick="if(__find_sugupja3('<?=$_SESSION['userCenterCode'];?>','0','client','client_name')){set_month('<?=$month;?>');}"></span>
						<span id="client_name" style="height:100%; margin-left:5px; font-weight:bold;"><?=$client_name;?></span>
						<input name="client" type="hidden" value="<?=$ed->en($jumin);?>">
					</td><?
				}
			?>
			<th class="center">년도</th>
			<td>
				<select name="year" style="width:auto;">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
						<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>년
			</td>
			<td class="left last">
			<?
				for($i=1; $i<=12; $i++){
					$class = 'my_month ';

					if ($i == intval($month)){
						$class .= 'my_month_y ';
						$color  = 'color:#000000;';
					}else{
						$class .= 'my_month_1 ';
						$color  = 'color:#666666;';
					}

					$text   = '<a href="#" onclick="set_month('.$i.');">'.$i.'월</a>';

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:2px;';
					}?>
					<div class="<?=$class;?>" style="<?=$style;?>"><?=$text;?></div><?
				}
			?>
			</td>
			<td class="right last" style="padding-top:1px;">
			<?
				if ($return_uri != ''){?>
					<a href="#" onclick="go_return('<?=$return_uri;?>');"><img src="../image/btn_prev.png"></a><?
				}
			?>
			</td>
		</tr>
	</tbody>
</table>
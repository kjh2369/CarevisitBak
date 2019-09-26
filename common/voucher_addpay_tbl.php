<?
	include_once('../inc/_http_uri.php');

	$laRoot = explode('/',$_SERVER['PHP_SELF']);
	$lsRoot = $laRoot[1];

	if ($lsRoot == 'sugupja'){
		$lbOver = false;
		$liSido = intval($laDisOption['sido_time']);
		$liJach = intval($laDisOption['jach_time']);
	}else{
		$lbOver = true;
		$liSido = 0;
		$liJach = 0;
	}

	if ($lsRoot == 'sugupja'){?>
		<table class="my_table my_border_blue" style="width:<?=$body_w;?>; border-bottom:2px solid #0e69b0;"><?
	}else{?>
		<table id="loSvcDisTbl" class="my_table" style="width:100%; display:none;"><?
	}?>
	<colgroup>
		<col width='75px'>
		<col width='80px'>
		<col width='80px'>
		<col width='80px'><?
		if ($lsRoot != 'sugupja'){?>
			<col width='80px'><?
		}?>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class='head'>급여구분</th>
			<th class='head'>합계</th>
			<th class='head'>시간</th>
			<th class='head'>지원금액</th>
			<th class='head'>본인부담금</th><?
			if ($lsRoot != 'sugupja'){?>
				<th class='head'>비고</th><?
			}?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class='center'><div class="left">기본급여</div></th>
			<td class='center'><div id="stndTot" class="right clsTot" value="0">0</div></td>
			<td class='center'><div id="stndTime" class="right clsTime" value="0">0</div></td>
			<td class='center'><div id="stndSupport" class="right clsSupport" value="0">0</div></td>
			<td class='center'><div id="stndExpense" class="right clsExpense" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
		<tr>
			<th class='center'><div class="left">추가급여</div></th>
			<td class='center'><div id="addTot" class="right clsTot" value="0">0</div></td>
			<td class='center'><div id="addTime" class="right clsTime" value="0">0</div></td>
			<td class='center'><div id="addSupport" class="right clsSupport" value="0">0</div></td>
			<td class='center'><div id="addExpense" class="right clsExpense" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
		<tr>
			<th class='center'><div class="left">시도비추가</div></th>
			<td class='center'><div id="sidoTot" class="right clsTot" value="0">0</div></td>
			<td class='center'><input id="sidoTime" name='sidoTime' type='text' value='<?=number_format($liSido);?>' class='number readonly clsTime' style='width:100%; background-color:#f6f4d3;'></td>
			<td class='center'><div id="sidoSupport" class="right clsSupport" value="0">0</div></td>
			<td class='center'><div id="sidoExpense" class="right clsExpense" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
		<tr>
			<th class='center'><div class="left">자치비추가</div></th>
			<td class='center'><div id="jachTot" class="right clsTot" value="0">0</div></td>
			<td class='center'><input id="jachTime" name='jachTime' type='text' value='<?=number_format($liJach);?>' class='number readonly clsTime' style='width:100%; background-color:#f6f4d3;'></td>
			<td class='center'><div id="jachSupport" class="right clsSupport" value="0">0</div></td>
			<td class='center'><div id="jachExpense" class="right clsExpense" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
		<?
			if ($lbOver){?>
				<tr>
					<th class='center'><div class="left">이월급여</div></th>
					<td class='center'><div id="overTot" class="right clsTot" value="0">0</div></td>
					<td class='center'><div id="overTime" class="right clsTime" value="0">0</div></td>
					<td class='center'><input id="overSupport" name="overSupport" type="text" value="0" class="number readonly clsSupport" style="width:100%;background-color:#f6f4d3;"> </td>
					<td class='center'><div id="overExpense" class="right clsExpense" value="0">0</div></td>
					<td class='center'></td>
				</tr><?
			}
		?>
		<tr>
			<th class='center'><div class="left">총이용합계</div></th>
			<td class='center'><div id="totalTot" class="right" value="0">0</div></td>
			<td class='center'><div id="totalTime" class="right" value="0">0</div></td>
			<td class='center'><div id="totalSupport" class="right" value="0">0</div></td>
			<td class='center'><div id="totalExpense" class="right" value="0">0</div></td><?
			if ($lsRoot != 'sugupja'){?>
				<td class='center'></td><?
			}?>
		</tr>
	</tbody>
</table>
<?
	$sql = 'select lvl_id as id
			,      lvl_rate as rate
			,      lvl_pay as pay
			,      lvl_from_dt as from_dt
			,      lvl_to_dt as to_dt
			  from income_lvl_self_pay
			 where lvl_kind = \'4\'
			   and lvl_gbn  = \'2\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	echo '<script type="text/javascript">
			var laExpense = new Array();';

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($id != $row['id']){
			$id  = $row['id'];
			$idx = 0;

			echo 'laExpense['.$id.'] = new Array();';
		}

		echo 'laExpense['.$id.']['.$idx.'] = {"rate":'.$row['rate'].',"pay":'.$row['pay'].',"from":"'.$row['from_dt'].'","to":"'.$row['to_dt'].'"};';

		$idx ++;
	}

	echo '</script>';

	$conn->row_free();
?>


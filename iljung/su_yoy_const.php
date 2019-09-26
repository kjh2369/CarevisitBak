<?
	include("../inc/_header.php");

	$type = $_GET['mType'];

	if ($type == 'search'){
		$title = '요양보호사 수당현황(실적기준)';
	}else{
		$title = '요양보호사 수당현황';
	}
?>
<table style="width:900px;">
	<tr>
	<?
		if ($type == 'search'){?>
			<td style="background-color:#eeeeee; font-weight:bold;" colspan="12"><?=$title;?></td><?
		}else{?>
			<td style="background-color:#eeeeee; font-weight:bold;" colspan="10"><?=$title;?></td>
			<td style="background-color:#eeeeee; font-weight:bold;" colspan="2"><a onClick="_iljungCal();"><img src="../image/btn_calc.png"></a></td><?
		}
	?>
	</tr>
	<tr>
		<td style="width:100px; height:30px; line-height:1.1em; background-color:#eeeeee; font-weight:bold;">요양<br>보호사</td>
		<td style="width:130px; height:30px; line-height:30px; background-color:#eeeeee; font-weight:bold;">제공서비스</td>
		<td style="width:75px; height:30px; line-height:30px; background-color:#eeeeee; font-weight:bold;">시간</td>
		<td style="width:75px; height:30px; line-height:30px; background-color:#eeeeee; font-weight:bold;">수가</td>
		<td style="width:50px; height:30px; line-height:30px; background-color:#eeeeee; font-weight:bold;">횟수</td>
		<td style="width:75px; height:30px; line-height:30px; background-color:#fffbe2; font-weight:bold;">수가 계</td>
		<td style="width:65px; height:30px; line-height:30px; background-color:#eeeeee; font-weight:bold;">시급</td>
		<td style="width:65px; height:30px; line-height:30px; background-color:#eeeeee; font-weight:bold;">시간</td>
		<td style="width:65px; height:30px; line-height:1.1em; background-color:#eeeeee; font-weight:bold;">건별<br>수당</td>
		<td style="width:75px; height:30px; line-height:30px; background-color:#fffbe2; font-weight:bold;">수당 계</td>
		<td style="width:75px; height:30px; line-height:1.1em; background-color:#eeeeee; font-weight:bold;">차익<br>(수가-수당)</td>
		<td style="width:50px; height:30px; line-height:30px; background-color:#eeeeee; font-weight:bold;">비고</td>
	</tr>
	<tbody id="yoyConstBody">
	</tbody>
</table>
<?
	include("../inc/_footer.php");
?>
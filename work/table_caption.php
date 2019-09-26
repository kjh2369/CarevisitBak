<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");

	$lastDay = $myF->lastDay($mYear, $mMonth);

?>
<table class="my_table">
	<colGroup>
		<col width="30px">
		<col width="100px">
		<col width="80px">
		<col width="30px">
		<col width="50px">
		<col width="35px">
		<col width="60px">
		<col width="60px">
		<col>
	</colGroup>
	<thead>
		<tr>
			<th class="head">No.</th>
			<th class="head">요양보호사</th>
			<th class="head">수급자</th>
			<th class="head" colspan="2">시간</th>
			<th class="head">구분</th>
			<th class="head">근무일수</th>
			<th class="head">총시간</th>
			<td>
				<div id="scroll_caption" style="overflow-x:scroll; overflow-y:scroll; width:100px; height:40px;">
					<table style="width:<?=intval($lastDay)*25;?>;">
						<tr>
				<?
					for($i=1; $i<=$lastDay; $i++){
						if ($i < $lastDay){
							$float = 'float:left;';
						}else{
							$float = '';
						} ?>
						<td style="width:25px; height:100%; text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc;"><?=$i;?></td>
					<?
					}
				?>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</thead>
</table>
<script language='javascript'>
	var p = parent.document.getElementById('tbl_caption');
	var w = p.offsetWidth - 399;
	var scroll = document.getElementById('scroll_caption');
	//alert(scroll.style.width);
	scroll.style.width = w;
	//alert(scroll.style.width);
</script>
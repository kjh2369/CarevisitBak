<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result	= false;
	});

	function lfSetReason(reason){
		opener.reason	= reason;
		self.close();
	}
</script>

<form id="f" name="f" method="post">

<div class="title title_border">겸직직종</div>

<div style="float:left; width:auto;">
	<table class="my_table" style="width:50%;">
		<colgroup>
			<col width="30px">
			<col>
		</colgroup>
		<tbody>
			<tr onclick="lfSetReason('01');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">1</th>
				<td class="left">시설장(관리책임자)</td>
			</tr>
			<tr onclick="lfSetReason('02');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">2</th>
				<td class="left">사무국장</td>
			</tr>
			<tr onclick="lfSetReason('03');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">3</th>
				<td class="left">사회복지사</td>
			</tr>
			<tr onclick="lfSetReason('04');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">4</th>
				<td class="left">의사</td>
			</tr>
			<tr onclick="lfSetReason('05');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">5</th>
				<td class="left">촉탁의사</td>
			</tr>
			<tr onclick="lfSetReason('06');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">6</th>
				<td class="left">간호사</td>
			</tr>
			<tr onclick="lfSetReason('07');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">7</th>
				<td class="left">간호조무사</td>
			</tr>
			<tr onclick="lfSetReason('08');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">8</th>
				<td class="left">치과위생사</td>
			</tr>
			<tr onclick="lfSetReason('09');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">9</th>
				<td class="left">물리치료사</td>
			</tr>
			<tr onclick="lfSetReason('10');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">10</th>
				<td class="left">작업치료사</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="float:left; width:auto;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="30px">
			<col>
		</colgroup>
		<tbody>
			<tr onclick="lfSetReason('11');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">11</th>
				<td class="left">요양보호사 1급</td>
			</tr>
			<tr onclick="lfSetReason('12');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">12</th>
				<td class="left">요양보호사 2급</td>
			</tr>
			<tr onclick="lfSetReason('13');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">13</th>
				<td class="left">요양보호사 기존유예자</td>
			</tr>
			<tr onclick="lfSetReason('14');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">14</th>
				<td class="left">영양사</td>
			</tr>
			<tr onclick="lfSetReason('15');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">15</th>
				<td class="left">사무원</td>
			</tr>
			<tr onclick="lfSetReason('16');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">16</th>
				<td class="left">조리원</td>
			</tr>
			<tr onclick="lfSetReason('17');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">17</th>
				<td class="left">위생원</td>
			</tr>
			<tr onclick="lfSetReason('18');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">18</th>
				<td class="left">관리인</td>
			</tr>
			<tr onclick="lfSetReason('19');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">19</th>
				<td class="left">보조원 운전사</td>
			</tr>
			<tr onclick="lfSetReason('20');" onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
				<th class="center">20</th>
				<td class="left">기타</td>
			</tr>
		</tbody>
	</table>
</div>

</form>

<?
	include_once('../inc/_footer.php');
?>
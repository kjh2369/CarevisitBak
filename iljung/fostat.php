<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= IntVal(Date('m'));
?>
<script type="text/javascript">
	function lfSearch(){
		alert('test');
	}
</script>

<div class="title title_border">가족요양보호사 타수급현황</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM"),"lfSearch()")');?></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="70px" span="3">
		<col width="70px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">요양보호사</th>
			<th class="head" colspan="3">계획시간</th>
			<th class="head" colspan="3">실적시간</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">가족</th>
			<th class="head">타수급</th>
			<th class="head">대상자</th>
			<th class="head">가족</th>
			<th class="head">타수급</th>
			<th class="head">대상자</th>
		</tr>
	</thead>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
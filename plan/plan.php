<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$org_no = $_SESSION['userCenterCode'];
	$SR = $_SESSION['userTypeSR'];
	$year = Date('Y');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfMoveYear(pos){
		var year = __str2num($('#year').text());

		year += pos;

		$('#year').text(year);

		lfSearch();
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./plan_list.php'
		,	data :{
				'SR':'<?=$SR;?>'
			,	'year':$('#year').text()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#TBL_LIST tbody').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfPlanReg(month){
		var data = {};

		data['bodyid'] = 'DIV_LAYER';
		data['SR'] = '<?=$SR;?>';
		data['year'] = $('#year').text();
		data['month'] = month;

		$.ajax({
			type:'POST'
		,	url:'./plan_reg.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#'+data['bodyid']).html(html).parent().show();
				__initLayerPosition(data['bodyid'], Math.floor($(window).width() / 10) * 10 - 100, Math.floor($(window).height() / 10) * 10 - 30);
			}
		,	error:function(e){
				alert(e);
			}
		}).responseXML;
	}
</script>
<div class="title"><div>계획등록 및 수정</div></div>
<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td>
				<div style="float:left; width:auto;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
				<div style="float:left; width:auto; margin-top:2px; padding-left:5px; padding-right:5px; font-weight:bold;" id="year"><?=$year;?></div>
				<div style="float:left; width:auto;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
			</td>
		</tr>
	</tbody>
</table>
<table id="TBL_LIST" class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="40px">
		<col width="70px">
		<col width="50px">
		<col width="50px">
		<col width="70px" span="12">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th>생활<br>관리사</th>
			<th>No</th>
			<th>대상자</th>
			<th>성별</th>
			<th>나이</th><?
			for($i=1; $i<=12; $i++){?>
				<th><?=$i;?>월</th><?
			}?>
			<th>비고</th>
		</tr>
	</thead>
	<tbody></tbody>
	<tfoot>
		<tr>
			<td style="border:none; background-color:white;">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<div class="layer_body"><div id="DIV_LAYER" class="layer_pop b_rad2"></div></div>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
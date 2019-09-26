<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_hce.php');

	$type = $_GET['type'];
	$sr   = $_GET['sr'];

	$imgpath = $gHostImgPath.'/top/ci_'.$_SESSION['userArea'].'.png';
	if (!is_file($imgpath)) $imgpath = $gHostImgPath.'/top/ci.png';
	if (!is_file($imgpath)) $imgpath = '';
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfTarget()',100);
	});

	function lfTarget(IPIN,rcpt){
		if (!IPIN) IPIN = '';
		if (!rcpt) rcpt = '';

		$.ajax({
			type :'POST'
		,	url  :'./hce_target.php'
		,	data :{
				'IPIN':IPIN
			,	'rcpt':rcpt
			,	'sr':'<?=$sr;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseStr(data);

				$('#lblTGName').text(col['name']);

				if (col['endYn'] == 'Y'){
					$('#lblTGEndYn').css('color','red').text('종결');
				}else if (col['endYn'] == 'N'){
					$('#lblTGEndYn').css('color','black').text('미결');
				}

				$('#lblTGBirth').text(col['birthDay']);
				$('#lblTGRctDt').text(col['rcptDt']);
				$('#lblTGRctSeq').text(col['rcptSeq']+(col['rcptSeq'] ? '차' : ''));
				$('#lblTGIVYn').text(col['IVYn'] == 'Y' ? '작성' : '');
				$('#lblTGInstYn').text(col['IsptYn'] == 'Y' ? '작성' : '');
				$('#lblTGChoiceYn').text(col['choiceYn'] == 'Y' ? '작성' : '');
				$('#lblTGMeegGbn').text(col['meetGbn']);
				$('#lblTGPlanSeq').text(col['planSeq'] == 'Y' ? '작성' : '');
				$('#lblTGContYn').text(col['contYn'] == 'Y' ? '작성' : '');
				$('#lblTGCuslYn').text(col['cuslYn'] == 'Y' ? '작성' : '');

				$('#lblTGConnYn').text(col['connYn'] == 'Y' ? '작성' : '');
				$('#lblTGMntrYn').text(col['mntrYn'] == 'Y' ? '작성' : '');
				$('#lblTGRestYn').text(col['restYn'] == 'Y' ? '작성' : '');
				$('#lblTGPrvEvYn').text(col['prvEvYn'] == 'Y' ? '작성' : '');
				$('#lblTGEvlnYn').text(col['evlnYn'] == 'Y' ? '작성' : '');

				try{
					top.frames['frmLeft'].lfShowMenu(IPIN);
				}catch(e){
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfInit(){
		return;
		$.ajax({
			type :'POST'
		,	url  :'./hce_target.php'
		,	data :{
				'IPIN':''
			,	'rcpt':''
			,	'sr':'<?=$sr;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				$('#lblTGName').text('');
				$('#lblTGEndYn').text('');
				$('#lblTGBirth').text('');
				$('#lblTGRctDt').text('');
				$('#lblTGRctSeq').text('');
				$('#lblTGIVYn').text('');
				$('#lblTGInstYn').text('');
				$('#lblTGChoiceYn').text('');
				$('#lblTGMeegGbn').text('');
				$('#lblTGPlanSeq').text('');
				$('#lblTGContYn').text('');
				$('#lblTGCuslYn').text('');

				$('#lblTGConnYn').text('');
				$('#lblTGMntrYn').text('');
				$('#lblTGRestYn').text('');
				$('#lblTGPrvEvYn').text('');
				$('#lblTGEvlnYn').text('');

				try{
					top.frames['frmLeft'].lfShowMenu('');
				}catch(e){
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<form id="f" name="f" method="post">
<div style="padding:10px 10px 0 10px;">
	<img src="../image/bg_case_top.jpg" border="0"><?

	if ($imgpath){?>
		<div style="position:absolute; width:auto; left:30px; top:40px;"><img src="<?=$imgpath;?>"></div><?
	}?>
</div>
<div style="padding:0 10px 0 10px; margin-top:-4px;">
	<table class="my_table my_border_blue" style="width:100%; border-top:none;">
		<colgroup>
			<col width="70px">
			<col width="40px">
			<col width="110px">
			<col width="110px">
			<col width="35px">
			<col width="40px" span="8">
			<col width="60px">
			<col width="50px">
			<col width="40px">
			<col width="40px">
			<col>
		</colgroup>
		<thead>
			<tr style="color:#064271;">
				<th class="head bold">대상자</th>
				<th class="head bold" style="line-height:1.3em;">종결<br>여부</th>
				<th class="head bold">생년월일</th>
				<th class="head bold">접수일자</th>
				<th class="head bold">차수</th>
				<th class="head bold">면접</th>
				<th class="head bold">사정</th>
				<th class="head bold">선정</th>
				<th class="head bold">사례</th>
				<th class="head bold">계획</th>
				<th class="head bold">동의</th>
				<th class="head bold">과정</th>
				<th class="head bold">연계</th>
				<th class="head bold">모니터링</th>
				<th class="head bold">재사정</th>
				<th class="head bold">제평</th>
				<th class="head bold">평가</th>
				<th class="head bold"></th>
			</tr>
		</thead>
		<tbody>
			<tr style="cursor:default;">
				<td class="center bold" id="lblTGName"></td>
				<td class="center bold" id="lblTGEndYn"></td>
				<td class="center bold" id="lblTGBirth"></td>
				<td class="center bold" id="lblTGRctDt"></td>
				<td class="center bold" id="lblTGRctSeq"></td>
				<td class="center bold" id="lblTGIVYn"></td>
				<td class="center bold" id="lblTGInstYn"></td>
				<td class="center bold" id="lblTGChoiceYn"></td>
				<td class="center bold" id="lblTGMeegGbn"></td>
				<td class="center bold" id="lblTGPlanSeq"></td>
				<td class="center bold" id="lblTGContYn"></td>
				<td class="center bold" id="lblTGCuslYn"></td>
				<td class="center bold" id="lblTGConnYn"></td>
				<td class="center bold" id="lblTGMntrYn"></td>
				<td class="center bold" id="lblTGRestYn"></td>
				<td class="center bold" id="lblTGPrvEvYn"></td>
				<td class="center bold" id="lblTGEvlnYn"></td>
			</tr>
		</tbody>
	</table>
</div>
</form>
<?
	include_once('../inc/_footer.php');
?>
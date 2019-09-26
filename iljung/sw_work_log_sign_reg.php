<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$type	= $_POST['type'];
	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$yymm	= $year.$month;
	$seq	= $_POST['seq'];

	if($type == 'SIGN'){
		$disabled = 'disabled="true";"';
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfLoad();
	});

	function lfLoad(){
		lfLogBody();
		lfSignBody();
	}

	function lfLogBody(){
		$.ajax({
			type:'POST'
		,	url:'../iljung/sw_work_log_reg_body.php'
		,	data:{
				'type'	:'SIGN'
			,	'jumin'	:$('#jumin').val()
			,	'yymm'	:$('#yymm').val()
			,	'seq'	:$('#seq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divLogBody').html(html);
			}
		});
	}

	function lfSignBody(){
		$.ajax({
			type:'POST'
		,	url:'../iljung/sw_work_log_sign_reg_body.php'
		,	data:{
				'type':'SIGN'
			,	'jumin':$('#jumin').val()
			,	'yymm':$('#yymm').val()
			,	'seq':$('#seq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divSignBody').html(html);
				$('input[init="Y"]').each(function(){
					__init_object(this);
				});

				$('textarea').each(function(){
					__init_object(this);
				});
			}
		});
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">사회복지사</th>
			<td class="last">
				<div id="lblSW" class="left" style="float:left; width:auto; line-height:25px;"></div>
			</td>
		</tr>
		<tr>
			<th class="center">업무수행일시</th>
			<td class="left last">
				<span id="lblDate" style="margin-right:5px;"></span>(<span id="lblTime" style="margin-left:5px; margin-right:5px;"></span> ~ <span id="lblToTime" style="margin-left:5px; margin-right:5px;"></span>)
			</td>
		</tr>
	</tbody>
</table>

<div id="divLogBody" style="height:500px; overflow-x:hidden; overflow-y:scroll;"></div>
<div id="divSignBody" style="height:200px;"></div>

<input id="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
<input id="from" type="hidden" value="<?=$from;?>">
<input id="to" type="hidden" value="<?=$to;?>">
<input id="year" type="hidden" value="<?=$year;?>">
<input id="month" type="hidden" value="<?=$month;?>">
<input id="yymm" type="hidden" value="<?=$yymm;?>">
<input id="seq" type="hidden" value="<?=$seq;?>">
<?
	include_once('../inc/_footer.php');
?>
<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$suga	= $_POST['code'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$from	= $_POST['from'];
	$to		= $_POST['to'];
?>
<script type="text/javascript">
	$(window).bind('resize', function(e){
		window.resizeEvt;
		$(window).resize(function(){
			clearTimeout(window.resizeEvt);
			window.resizeEvt = setTimeout(function(){
				lfResizeClient();
				lfResizeIljung();
			}, 250);
		});
	});

	$(document).ready(function(){
		setTimeout('lfLoadCtSvc()', 100);
		setTimeout('lfLoadCaln()',200);
		setTimeout('lfLoadRsTg()',300);
		setTimeout('lfLoadClient()',400);
		setTimeout('lfLoadBtn()',500);
		setTimeout('lfLoadIljung()',600);
	});

	function lfResizeClient(){
		var top = $('#divClient').offset().top;
		var body = document.body;
		var height = body.offsetHeight;
		var h = height - top - 2;

		$('#divClient').height(h);
	}

	function lfResizeIljung(){
		var top = $('#divIljung').offset().top;
		var body = document.body;
		var height = body.offsetHeight;
		var h = height - top - 2;

		$('#divIljung').height(h);
	}

	function lfLoadCtSvc(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_resource_reg_ctsvc.php'
		,	data :{
				'sr'	:$('#sr').val()
			,	'suga'	:$('#suga').val()
			,	'year'	:$('#year').val()
			,	'month'	:$('#month').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divCtSvc').html(html);
			}
		,	complete:function(){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadCaln(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_resource_reg_caln.php'
		,	data :{
				'sr'	:$('#sr').val()
			,	'suga'	:$('#suga').val()
			,	'year'	:$('#year').val()
			,	'month'	:$('#month').val()
			,	'from'	:$('#from').val()
			,	'to'	:$('#to').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divCaln').html(html);
			}
		,	complete:function(){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadRsTg(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_resource_reg_rstg.php'
		,	data :{
				'sr'	:$('#sr').val()
			,	'suga'	:$('#suga').val()
			,	'year'	:$('#year').val()
			,	'month'	:$('#month').val()
			,	'from'	:$('#from').val()
			,	'to'	:$('#to').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divRsTg').html(html);
			}
		,	complete:function(){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadClient(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_resource_reg_client.php'
		,	data :{
				'sr'	:$('#sr').val()
			,	'suga'	:$('#suga').val()
			,	'year'	:$('#year').val()
			,	'month'	:$('#month').val()
			,	'from'	:$('#from').val()
			,	'to'	:$('#to').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divClient').html(html);
			}
		,	complete:function(){
				lfResizeClient();
			}
		,	error:function(){
			}
		}).responseXML;
	}


	function lfLoadBtn(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_resource_reg_btn.php'
		,	data :{
				'sr'	:$('#sr').val()
			,	'suga'	:$('#suga').val()
			,	'year'	:$('#year').val()
			,	'month'	:$('#month').val()
			,	'from'	:$('#from').val()
			,	'to'	:$('#to').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divBtn').html(html);
			}
		,	complete:function(){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadIljung(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_resource_reg_iljung.php'
		,	data :{
				'sr'	:$('#sr').val()
			,	'suga'	:$('#suga').val()
			,	'year'	:$('#year').val()
			,	'month'	:$('#month').val()
			,	'from'	:$('#from').val()
			,	'to'	:$('#to').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divIljung').html(html);
			}
		,	complete:function(){
				lfResizeIljung();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfFindClient(name){
		if (name){
			$('#divClient #tbodyList :checkbox[id^="chk"][gbn="A"]').parent().parent().hide();
			$('#divClient #tbodyList :checkbox[id^="chk"][gbn="M"]').parent().parent().hide();
		}else{
			$('#divClient #tbodyList :checkbox[id^="chk"][gbn="A"]').parent().parent().show();
			$('#divClient #tbodyList :checkbox[id^="chk"][gbn="M"]').parent().parent().show();
		}

		$('#divClient #tbodyList span[id="client_name"]').each(function(){
			if ($(this).text().indexOf(name) >= 0){
				$(this).parent().parent().show();
			}else{
				$(this).parent().parent().hide();
			}
		});
	}
</script>
<form id="f" name="f" method="post">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="260px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="top bottom last" style="border-right:1px solid #0e69b0;">
				<div class="title title_border">기관 및 서비스</div>
				<div id="divCtSvc"></div>
				<div id="divCaln" style="border-top:1px solid #0e69b0;"></div>
				<div class="title" style="border-top:1px solid #0e69b0;">대상자</div>
				<table class="my_table" style="width:100%; border-top:1px solid #0e69b0; border-bottom:none;">
					<colgroup>
						<col width="70px">
						<col>
					</colgroup>
					<tbody >
						<th style="border-bottom:none;">대상자명</th>
						<td style="border-bottom:none;"><input type="text" value="" onkeyup="lfFindClient($(this).val());"></td>
					</tbody>
				</table>
				<div id="divClient" style="overflow-x:hidden; overflow-y:auto; height:100px; border-top:1px solid #0e69b0;"></div>
			</td>
			<td class="top bottom last" style="">
				<div class="title title_border">자원</div>
				<div id="divRsTg"></div>
				<div id="divBtn" style="overflow-x:hidden; overflow-y:scroll; border-top:1px solid #0e69b0;"></div>
				<div id="divIljung" style="overflow-x:hidden; overflow-y:scroll; height:100px;"></div>
			</td>
		</tr>
	</tbody>
</table>
<input id="sr" type="hidden" value="<?=$sr;?>">
<input id="suga" type="hidden" value="<?=$suga;?>">
<input id="year" type="hidden" value="<?=$year;?>">
<input id="month" type="hidden" value="<?=$month;?>">
<input id="from" type="hidden" value="<?=$from;?>">
<input id="to" type="hidden" value="<?=$to;?>">
</form>
<div id="loLoading" style="position:absolute; width:auto; background-color:#ffffff; border:2px solid #cccccc; top:400px; padding:20px; display:none;"></div>
<div id="CalInfoShow" style="position:absolute; width:auto; display:none;"></div>
<?
	include_once('../inc/_footer.php');
?>
<?
	include_once('../inc/_http_uri.php');

	$year  = date('Y', mktime());
	$month = date('m', mktime());
	$init_year = $myF->year();
?>
<table style="width:1024px;">
	<colgroup>
		<col width="32%">
		<col width="32%">
		<col width="36%">
	</colgroup>
	<tr>
		<td class="tmp_0 tmp_3"><? include_once('body_board_notice.php');?></td>
		<td class="tmp_0 tmp_3"><? include_once('body_board_free.php');?></td>
		<td class="tmp_2 tmp_3"><? include_once('body_board_mananul.php');?></td>
	</tr>
	<tr>
		<td class="tmp_4" colspan="3">
			<!--div style="height:38px; border-bottom:1px solid #414548; background:url('../image/caption_bg.gif');">
				<div style="width:52px; height:16px; margin-top:10px; margin-left:20px; float:left; background:url('../image/caption_10.gif') no-repeat;"></div>
				<div style="width:9px; height:17px; margin-top:9px; margin-left:20px; float:left; background:url('../image/arrow_b.gif') no-repeat; cursor:pointer;" onclick="lfSetIljung(-1);"></div>
				<div id="str_ym" style="width:auto; height:17px; margin-top:5px; margin-left:15px; margin-right:15px; float:left; font-weight:bold; font-size:16px;"><?=$year;?>.<?=$month;?></div>
				<div style="width:9px; height:17px; margin-top:9px; float:left; background:url('../image/arrow_n.gif') no-repeat; cursor:pointer;" onclick="lfSetIljung(1);"></div>
			</div>
			<div id="tmp_iljung"></div-->
		</td>
	</tr>
</table>

<input id="year" name="year" type="hidden" value="<?=$year;?>">
<input id="month" name="month" type="hidden" value="<?=$month;?>">

<script type="text/javascript">
	$(document).ready(function(){
		//setTimeout('lfSetIljung()',1);
	});

	function lfSetIljung(pos){
		var year = $('#year').val();
		var month = parseInt($('#month').val(),10);

		switch (pos){
			case 1:
				month = parseInt(month, 10) + 1;

				if (month > 12){
					year = parseInt(year, 10) + 1;
					month = 1;
				}
				break;
			case -1:
				month = parseInt(month, 10) - 1;

				if (month < 1){
					year  = parseInt(year, 10) - 1;
					month = 12;
				}
				break;
		}

		month = (month < 10 ? '0' : '')+month;

		$('#year').val(year);
		$('#month').val(month);

		$.ajax({
			type: 'POST'
		,	url : './body_person_iljung.php'
		,	data: {
				'year':year
			,	'month':month
			}
		,	beforeSend: function (){
			}
		,	success: function (html){
				$('#str_ym').text(year+'.'+month);
				$('#tmp_iljung').html(html);
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>
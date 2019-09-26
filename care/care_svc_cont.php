<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_GET['SR'];
	$year	= Date('Y');
	$month	= IntVal(Date('m'));
	$cols	= 30;
?>
<script type="text/javascript">
	var clientIdx = {};

	$(document).ready(function(){
		//lfResize();
		lfLoadClient();
		//lfSearch();

		$('#divHead').scroll(function(){
			$('#divData').scrollLeft($(this).scrollLeft());
		});

		$('#divNote').scroll(function(){
			$('#divSuga').scrollTop($(this).scrollTop());
			$('#divData').scrollTop($(this).scrollTop());
		});
	});

	function lfResize(){
		var top = $('#divSuga').offset().top;
		var h = $(document).height();
		var height = h - top - 31;

		$('#divSuga').height(height);
		$('#divData').height(height);
		$('#divNote').height(height);
	}

	function lfMoveMonth(month){
		$('#lblYYMM').attr('month',month);
		$('div[id^="btnMonth_"]').each(function(){
			var mon = $(this).attr('id').replace('btnMonth_','');

			if (mon == month){
				$(this).removeClass('my_month_1').addClass('my_month_y');
			}else{
				$(this).removeClass('my_month_y').addClass('my_month_1');
			}
		});

		//lfSearch('SERVICE');

		lfLoadClient();
	}

	function lfLoadClient(){
		$.ajax({
			type:'POST',
			url:'./care_svc_cont_client.php',
			data:{
				'SR':'<?=$SR;?>',
				'year':$('#lblYYMM').attr('year'),
				'month':$('#lblYYMM').attr('month')
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(data){
				var row = data.split('?');
				var html = '';
				var idx = 0;

				clientIdx['cols'] = row.length;

				html += '<table class="my_talbe" style="width:'+(row.length * 25)+'px;">'
					 +	'<colgroup>'
					 +	'<col width="25px" span="'+row.length+'">'
					 +	'</colgroup>'
					 +	'<thead>'
					 +	'<tr>';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var cls = '';

						if (i == row.length - 1) cls = ' last';

						var name = '';

						for(var j=0; j<col['nm'].length; j++){
							name += col['nm'].substr(j,1)+'<br>';
						}

						clientIdx[idx] = col['cd'];
						html += '<th class="head bottom'+cls+'" style="vertical-align:top;">'+name+'</th>';
						idx ++;
					}
				}

				html += '</tr>'
					 +	'</thead>'
					 +	'</table>';

				$('#divHead').html(html);

				lfResize();
				lfLoadData();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfLoadData(){
		$.ajax({
			type:'POST',
			url:'./care_svc_cont_data.php',
			data:{
				'SR':'<?=$SR;?>',
				'year':$('#lblYYMM').attr('year'),
				'month':$('#lblYYMM').attr('month')
			},
			beforeSend: function (){
			},
			success:function(data){
				var row = data.split('?');
				var suga = {};
				var html = {};
				var val = {};
				var idx = 0;

				html['data']  = '';
				html['data'] += '<table id="tblData" class="my_talbe" style="width:'+(clientIdx['cols'] * 25)+'px;">'
							 +	'<colgroup>'
							 +	'<col width="25px" span="'+clientIdx['cols']+'">'
							 +	'</colgroup>'
							 +	'<tbody>';

				html['suga']  = '';
				html['suga'] += '<table class="my_talbe">'
							 +	'<colgroup>'
							 +	'<col width="*">'
							 +	'</colgroup>'
							 +	'<tbody>';

				html['note']  = '';
				html['note'] += '<table class="my_talbe" style="width:100%;">'
							 +	'<colgroup>'
							 +	'<col width="*">'
							 +	'</colgroup>'
							 +	'<tbody>';

				clientIdx['rows'] = row.length;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var cls = '';

						if (suga[col['sugaCd']] != col['sugaCd']){
							suga[col['sugaCd']]  = col['sugaCd'];

							html['suga'] += '<tr>'
										 +	'<td class="center last"><div class="left nowrap" style="width:150px;">'+col['sugaNm']+'</div></td>'
										 +	'</tr>';

							html['data'] += '<tr>';

							for(var j=0; j<clientIdx['cols']; j++){
								var cls = '';

								if (j == clientIdx['cols'] - 1){
									cls = ' last';
								}
								html['data'] += '<td id="tdId_'+col['sugaCd']+'_'+clientIdx[j]+'" class="center bold'+cls+'" style="color:BLUE;"></td>';
							}

							html['data'] += '</tr>';
							html['note'] += '<tr><td>&nbsp;</td></tr>';
						}

						val[idx] = {};
						val[idx]['suga'] = col['sugaCd'];
						val[idx]['code'] = col['code'];

						idx ++;
					}
				}

				html['data'] += '</tbody>'
							 +	'</table>';

				html['suga'] += '</tbody>'
							 +	'</table>';

				html['note'] += '</tbody>'
							 +	'</table>';

				$('#divSuga').html(html['suga']);
				$('#divData').html(html['data']);
				$('#divNote').html(html['note']);

				//var obj = $('#tblData');

				for(var i=0; i<idx; i++){
					//$('#tdId_'+val[i]['suga']+'_'+val[i]['code']).text('●');
					var obj = $('#tdId_'+val[i]['suga']+'_'+val[i]['code']);
					var v = __str2num($(obj).text())+1;

					$(obj).text(v);
				}

				$('#tempLodingBar').remove();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'SR':'<?=$SR;?>',
				'year':$('#lblYYMM').attr('year'),
				'month':$('#lblYYMM').attr('month')
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './care_svc_cont_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">서비스내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col width="500px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="last">
				<div>
					<div style="float:left; width:auto; margin-left:5px; margin-top:3px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; margin-top:3px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last" style="padding-top:1px;"><?echo $myF->_btn_month($month,'lfMoveMonth(');?></td>
			<td class="right last">
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">EXCEL</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="150px">
		<col width="750px">
		<col width="*">
	</colgroup>
	<thead>
		<tr>
			<th class="head">서비스</th>
			<th class="head">
				<div id="divHead" style="width:750px; height:auto; text-align:left; overflow-x:scroll; overflow-y:hidden;"></div>
			</th>
			<th class="head last">비고</th>
		</tr>
	</head>
	<tbody>
		<tr>
			<td class="top">
				<div id="divSuga" style="width:100%; height:100px; overflow-x:hidden; overflow-y:hidden;"></div>
			</td>
			<td class="top">
				<div id="divData" style="width:100%; height:100px; overflow-x:hidden; overflow-y:hidden;"></div>
			</td>
			<td class="top last">
				<div id="divNote" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;"></div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last" colspan="32"></td>
		</tr>
	</tfoot>
</table>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<?
	include_once('../inc/_footer.php');
?>
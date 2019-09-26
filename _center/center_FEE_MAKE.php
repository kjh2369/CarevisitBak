<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_FEE_MAKE_search.php'
		,	data:{
				//'company':$('#cboCompany').val()
				'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

				var clsYn = getHttpRequest('./center_close_yn.php?type=1&year='+$('#lblYYMM').attr('year')+'&month='+$('#lblYYMM').attr('month'));

				if (clsYn == 'Y'){
					$('button',$('.CLS_BTN')).attr('disabled',true);
				}else{
					$('button',$('.CLS_BTN')).attr('disabled',false);
				}
			}
		,	success:function(html){
				//$('#ID_LIST').html('<tr><td colspan="10">'+html+'</td></tr>');
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();

				var amt1 = 0, amt2 = 0;

				$('tr',$('#ID_LIST')).each(function(){
					amt1 += __str2num($('td',this).eq(4).text());
					amt2 += __str2num($('td',this).eq(5).text());
				});

				$('#ID_CELL_SUM_4').text(__num2str(amt1));
				$('#ID_CELL_SUM_5').text(__num2str(amt2));
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfMake(orgNo){
		if (!orgNo) orgNo = '';

		$.ajax({
			type:'POST'
		,	url:'./center_FEE_MAKE_save.php'
		,	data:{
				'orgNo'	:orgNo
			//,	'company':$('#cboCompany').val()
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					lfSearch();
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfRemove(orgNo){
		if (!orgNo) orgNo = '';

		$.ajax({
			type:'POST'
		,	url:'./center_FEE_MAKE_remove.php'
		,	data:{
				'orgNo'	:orgNo
			//,	'company':$('#cboCompany').val()
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				//$('#ID_LIST').html('<tr><td colspan="10">'+result+'</td></tr>');
				if (!result){
					lfSearch();
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfContSvc(orgNo){
		var width = 950;
		var height = 500;
		var left = (screen.width - width) / 2;
		var top = (screen.height - height) / 2;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var url = './center_connect_reg.php';
		var win = window.open('about:blank', 'CONNECT_REG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'orgNo':orgNo
			,	'type':'Service'
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

		form.setAttribute('target', 'CONNECT_REG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function Selection(orgNo){
		var width = 900;
		var height = 750;
		//var left = window.screenLeft + ($(window).width() - width) / 2;
		//var top = window.screenTop + ($(window).height() - height) / 2;
		var left = window.screenLeft + $('#left_box').width();
		var top = window.screenTop + $('#divTitle').offset().top;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var url = './center_connect_info.php';
		var win = window.open('about:blank', 'CONNECT_INFO', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'orgNo':orgNo
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

		form.setAttribute('target', 'CONNECT_INFO');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">청구년월</th>
			<!--td>
				<select id="cboCompany" style="width:auto;" onchange="GetValue('BRANCH',this.value);">
					<option value="">-회사선택-</option><?
					$sql = 'SELECT	b00_code AS cd
							,		b00_name AS nm
							,		b00_manager AS manager
							,		b00_domain AS domain
							FROM	b00branch
							WHERE	b00_com_yn = \'Y\'
							ORDER	BY nm';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['domain'];?>" <?=$row['domain'] == 'carevisit.net' ? 'selected' : '';?>><?=$row['nm'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td-->
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM")); lfSearch();');?></td>
		</tr>
	</tbody>
</table><?
$colgroup = '
	<col width="40px">
	<col width="100px">
	<col width="170px">
	<col width="90px">
	<col width="80px">
	<col width="80px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">현재요금</th>
			<th class="head">계산요금</th>
			<th class="head last">
				<div class="CLS_BTN" style="float:right; width:auto;">
					<span class="btn_pack small"><button onclick="lfMake();">전체생성</button></span>
					<span class="btn_pack small"><button onclick="lfRemove();" style="color:RED;">전체삭제</button></span>
				</div>
				<div style="float:center; width:auto;">비고</div>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="sum center" colspan="4"><div class="right">합계</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_4" class="right">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_5" class="right">0</div></td>
			<td class="sum center last"></td>
		</tr>
	</tbody>
</table>
<div id="ID_BODY" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="ID_LIST"></tbody>
		<tfoot>
			<tr>
				<td class="bottom last"></td>
			</tr>
		</tfoot>
	</table>
</div>
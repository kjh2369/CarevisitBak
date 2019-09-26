<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	var winPos = {};

	$(document).ready(function(){
		/*
		var page = 1;
		var loading = false; //to prevents multipal ajax loads

		$('#ID_BODY_LIST').scroll(function() { //detect page scroll
			if($('#ID_BODY_LIST').scrollTop() + $('#ID_BODY_LIST').height() == $('#ID_BODY_LIST').attr('scrollHeight')){  //user scrolled to bottom of the page?
				if (loading == false){ //there's more data to load
					loading = true; //prevent further ajax loading

					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

					//load data from the server using a HTTP POST request
					$.post('./center_<?=$menuId?>_search.php',{'orgNo':$('#txtOrgNo').val(),'orgNm':$('#txtOrgNm').val(),'popFrom':$('#txtPopFrom').val(),'popTo':$('#txtPopTo').val(),'stat':$('#cboStat').val(),'page':(page+1)}, function(html){
						if (html){
							//$("#results").append(data); //append received data into the element
							$('tbody',$('#ID_BODY_LIST')).append(html);

							//hide loading image
							$('#tempLodingBar').remove(); //hide loading image once data is received

							page ++;
							loading = false;
						}else{
							$('#tempLodingBar').remove();
						}
					}).fail(function(xhr, ajaxOptions, thrownError) { //any errors?

						alert(thrownError); //alert with HTTP error
						$('#tempLodingBar').remove(); //hide loading image
						loading = false;

					});
				}
			}
		});
		*/

		lfSearch();
	});

	//기관연결정보
	function Selection(orgNo){
		var width = 900;
		var height = 600;
		//var left = window.screenLeft + ($(window).width() - width) / 2;
		//var top = window.screenTop + ($(window).height() - height) / 2;
		var left = window.screenLeft + $('#left_box').width();
		var top = window.screenTop + $('#divTitle').offset().top;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var url = './center_connect_info.php';
		var win = window.open('about:blank', 'CONNECT_INFO', option);
			win.opener = self;
			win.focus();

		winPos['X'] = left;
		winPos['Y'] = top;

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

	function GetScreenInfo(){
		return winPos;
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'orgNo'	:$('#txtOrgNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'popFrom':$('#txtPopFrom').val()
			,	'popTo'	:$('#txtPopTo').val()
			,	'stat'	:$('#cboStat').val()
			,	'page'	:1
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_BODY_LIST')).html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				$('#tempLodingBar').remove();
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col width="150px">
		<col width="70px">
		<col width="177px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td>
				<input id="txtOrgNo" type="text" style="width:100%;">
			</td>
			<th class="center">기관명</th>
			<td>
				<input id="txtOrgNm" type="text" style="width:100%;">
			</td>
			<th class="center">팝업일자</th>
			<td>
				<input id="txtPopFrom" type="text" class="date"> ~ <input id="txtPopTo" type="text" class="date">
			</td>
			<td>
				<select id="cboStat" style="width:auto;">
					<option value="">전체</option>
					<option value="1">중지</option>
					<option value="2">미납</option>
				</select>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table><?

$colgroup = '
	<col width="40px">
	<col width="150px">
	<col width="100px">
	<col width="70px">
	<col width="150px">
	<col width="70px">
	<col width="60px">
	<col>';?>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">기관기호</th>
			<th class="head">팝업일자</th>
			<th class="head">미납내역</th>
			<th class="head">미납금액</th>
			<th class="head">상태</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>

<div id="ID_BODY_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody></tbody>
	</table>
</div>
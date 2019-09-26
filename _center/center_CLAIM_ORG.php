<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		/*
		var page = 1;
		var loading = false; //to prevents multipal ajax loads

		$('#ID_BODY_LIST').scroll(function() { //detect page scroll
			//alert($('#ID_BODY_LIST').scrollTop()+'/'+$('#ID_BODY_LIST').height()+'/'+$('#ID_BODY_LIST').attr('scrollHeight'));
			if($('#ID_BODY_LIST').scrollTop() + $('#ID_BODY_LIST').height() == $('#ID_BODY_LIST').attr('scrollHeight')){  //user scrolled to bottom of the page?
				if (loading == false){ //there's more data to load
					loading = true; //prevent further ajax loading

					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

					//load data from the server using a HTTP POST request
					$.post('./center_<?=$menuId?>_search.php',{'company':$('#cboCompany').val(),'year':$('#yymm').attr('year'),'month':$('#yymm').attr('month'),'page':(page+1)}, function(html){
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

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			,	'orgNo'	:$('#txtOrgNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'mgNm'	:$('#txtMgNm').val()
			,	'popYn'	:'<?=$_GET["popYn"];?>'
			,	'page'	:1
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_BODY_LIST')).html(html);
				$('#tempLodingBar').remove();

				var amt4 = 0, amt5 = 0, amt7 = 0;

				$('tr',$('tbody',$('#ID_BODY_LIST'))).each(function(){
					amt4 += __str2num($('td',this).eq(4).text());
					amt5 += __str2num($('td',this).eq(5).text());
					amt7 += __str2num($('td',this).eq(7).text());
				});

				$('#ID_CELL_SUM_3').text(__num2str(amt4 + amt5));
				$('#ID_CELL_SUM_4').text(__num2str(amt4));
				$('#ID_CELL_SUM_5').text(__num2str(amt5));
				$('#ID_CELL_SUM_7').text(__num2str(amt7));
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfPopDtl(orgNo){
		$.ajax({
			type:'POST',
			url:'../center/bill_rec_dtl.php',
			data:{
				'orgNo'	:orgNo
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('#ID_POP_DTL').html(html).show();
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

	function lfBillClose(){
		$('#ID_POP_DTL').hide();
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'company':$('#cboCompany').val()
			,	'year':$('#yymm').attr('year')
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

		form.setAttribute('method', 'post');
		form.setAttribute('action', './center_<?=$menuId?>_excel.php');

		document.body.appendChild(form);

		form.submit();

		__TempLoading();
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="130px">
		<col width="70px">
		<col width="90px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관명</th>
			<td><input id="txtOrgNm" type="text" style="width:100%;"></td>
			<th class="center">기관기호</th>
			<td><input id="txtOrgNo" type="text" style="width:100%;"></td>
			<th class="center">대표자명</th>
			<td><input id="txtMgNm" type="text" style="width:100%;"></td>
			<td class="left last">
				<span class="btn_pack small"><button onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table><?

$colgroup = '
	<col width="40px">
	<col width="150px">
	<col width="90px">
	<col width="70px" span="3">
	<col width="70px">
	<col width="70px">
	<col width="70px">
	<col width="50px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">기관명</th>
			<th class="head" rowspan="2">기관기호</th>
			<th class="head" colspan="3">청구내역</th>
			<th class="head" colspan="2">입금내역</th>
			<th class="head" colspan="2">세금계산서</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">합계</th>
			<th class="head">당월분</th>
			<th class="head">미납분</th>
			<th class="head">일자</th>
			<th class="head">금액</th>
			<th class="head">발행일자</th>
			<th class="head">구분</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="sum center" colspan="3"><div class="right">합계</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_3" class="right" style="font-size:11px;">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_4" class="right" style="font-size:11px;">0</div></td>
			<td class="sum center"><div id="ID_CELL_SUM_5" class="right" style="font-size:11px;">0</div></td>
			<td class="sum center" colspan="2"><div id="ID_CELL_SUM_7" class="right">0</div></td>
			<td class="sum center last" colspan="3"></td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<td class="center top bottom last" colspan="12">
				<div id="ID_BODY_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
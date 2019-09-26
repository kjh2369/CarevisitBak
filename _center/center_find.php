<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		var page = 1;
		var loading = false; //to prevents multipal ajax loads

		$('#ID_REG_LIST').scroll(function() { //detect page scroll
			if($('#ID_REG_LIST').scrollTop() + $('#ID_REG_LIST').height() == $('#ID_REG_LIST').attr('scrollHeight')){  //user scrolled to bottom of the page?
				if (loading == false){ //there's more data to load
					loading = true; //prevent further ajax loading

					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

					//load data from the server using a HTTP POST request
					$.post('./center_find_search.php',{'orgNo':$('#txtOrgNo').val(),'orgNm':$('#txtOrgNm').val(),'mgNm':$('#txtMgNm').val(),'addr':$('#txtAddr').val(),'page':(page+1)}, function(html){
						if (html){
							//$("#results").append(data); //append received data into the element
							$('tbody',$('#ID_REG_LIST')).append(html);

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

		lfRegSearch();
	});

	function lfRegSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_find_search.php'
		,	data:{
				'orgNo'	:$('#txtOrgNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'mgNm'	:$('#txtMgNm').val()
			,	'addr'	:$('#txtAddr').val()
			,	'page'	:1
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_REG_LIST')).html(html);
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

	function lfRegSelOrg(obj){
		//$('tbody tr',$('#ID_REG_LIST')).css('font-weight','normal').css('color','');
		//$(obj).css('font-weight','bold').css('color','BLUE');
		//$('#ID_CELL_REG_ORGNO').text($(obj).attr('orgNo'));

		lfFindOrgSet(obj);
	}
</script>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup>
		<col width="70px" span="8">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관명</th>
			<td><input id="txtOrgNm" type="text" style="width:100%;"></td>
			<th class="center">기관기호</th>
			<td><input id="txtOrgNo" type="text" style="width:100%;"></td>
			<th class="center">대표자</th>
			<td><input id="txtMgNm" type="text" style="width:100%;"></td>
			<th class="center">주소</th>
			<td><input id="txtAddr" type="text" style="width:100%;"></td>
			<td class="left last">
				<span class="btn_pack small"><button onclick="lfRegSearch();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table><?

$colgroup = '
	<col width="40px">
	<col width="150px">
	<col width="90px">
	<col width="70px">
	<col>';?>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">기관기호</th>
			<th class="head">대표자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top bottom last" colspan="5">
				<div id="ID_REG_LIST" style="overflow-x:hidden; overflow-y:scroll; height:243px;">
					<table class="my_table" style="width:100%; background-color:WHITE;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
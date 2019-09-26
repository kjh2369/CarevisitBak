<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$gbn	= $_POST['gbn'];

	$sql = 'SELECT	DISTINCT m00_store_nm AS org_nm, m00_mname AS mg_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'';

	$row = $conn->get_array($sql);

	$orgNm	= $row['org_nm'];
	$mgNm	= $row['mg_nm'];

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		var page = 1;
		var loading = false; //to prevents multipal ajax loads

		$('#ID_DTL_LIST').scroll(function() { //detect page scroll
			if($('#ID_DTL_LIST').scrollTop() + $('#ID_DTL_LIST').height() == $('#ID_DTL_LIST').attr('scrollHeight')){  //user scrolled to bottom of the page?
				if (loading == false){ //there's more data to load
					loading = true; //prevent further ajax loading

					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

					//load data from the server using a HTTP POST request
					$.post('./center_IN_LIST_dtl_search.php',{'orgNo':'<?=$orgNo;?>','gbn':'<?=$gbn;?>','fromDt':$('#txtFromDt').val(),'toDt':$('#txtToDt').val(),'page':(page+1)}, function(html){
						if (html){
							//$("#results").append(data); //append received data into the element
							$('tbody',$('#ID_DTL_LIST')).append(html);

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

		lfDtlSearch();
	});

	function lfDtlSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_IN_LIST_dtl_search.php'
		,	data:{
				'orgNo'	:'<?=$orgNo;?>'
			,	'gbn'	:'<?=$gbn;?>'
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			,	'page'	:1
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_DTL_LIST')).html(html);
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
</script>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup>
		<col width="70px">
		<col width="200px">
		<col width="70px">
		<col width="130px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관명</th>
			<td><div class="left nowrap" style="width:200px;"><?=$orgNm;?></div></td>
			<th class="center">기관기호</th>
			<td><div class="left nowrap" style="width:130px;"><?=$orgNo;?></div></td>
			<th class="center">대표자명</th>
			<td><div class="left"><?=$mgNm;?></div></td>
		</tr>
		<tr>
			<th class="center">입금일자</th>
			<td colspan="5">
				<div style="float:left; width:auto;">
					<input id="txtFromDt" type="text" class="date"> ~
					<input id="txtToDt" type="text" class="date">
				</div>
				<div style="float:left; width:auto; padding-top:2px;">
					<span class="btn_pack small"><button>조회</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table><?

$colgroup = '
	<col width="40px">
	<col width="70px">
	<col width="100px">
	<col width="70px">
	<col width="70px">
	<col width="70px">
	<col width="80px">
	<col>';?>
<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">입금일자</th>
			<th class="head">CMS번호</th>
			<th class="head">입금금액</th>
			<th class="head">적용년월</th>
			<th class="head">적용금액</th>
			<th class="head">미적용금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<div id="ID_DTL_LIST" style="overflow-x:hidden; overflow-y:scroll; height:218px; background-color:WHITE;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody></tbody>
	</table>
</div>
<?
	include_once('../inc/_db_close.php');
?>
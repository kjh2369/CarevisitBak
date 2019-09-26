<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	/*
	 *	기관조회
	 */
?>
<script type="text/javascript">
	$(document).ready(function(){
		var page = 1;
		var loading = false; //to prevents multipal ajax loads

		$('#ID_BODY_LIST').scroll(function() { //detect page scroll
			//alert($('#ID_BODY_LIST').scrollTop()+'/'+$('#ID_BODY_LIST').height()+'/'+$('#ID_BODY_LIST').attr('scrollHeight'));
			if($('#ID_BODY_LIST').scrollTop() + $('#ID_BODY_LIST').height() >= $('#ID_BODY_LIST').attr('scrollHeight')){  //user scrolled to bottom of the page?
				if (loading == false){ //there's more data to load
					loading = true; //prevent further ajax loading

					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

					//load data from the server using a HTTP POST request
					$.post('./center_<?=$menuId;?>_search.php',{'year':$('#lblYYMM').attr('year'),'month':$('#lblYYMM').attr('month'),'page':(page+1)}, function(html){
						if (html){
							//$("#results").append(data); //append received data into the element
							$('tbody',$('#ID_BODY_LIST')).append(html);

							//hide loading image
							$('#tempLodingBar').remove(); //hide loading image once data is received

							page ++;
							loading = false;
						}else{
							alert(html);
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

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST',
			url:'./center_<?=$menuId;?>_search.php',
			data:{
				'year':	$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'page':	1
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('tbody',$('#ID_BODY_LIST')).html(html);
				$('#tempLodingBar').remove();
				$('#ID_BODY_LIST').scroll();
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
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
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

$sql = 'SELECT	\'1\' AS svc_gbn, svc_cd, svc_nm
		FROM	cv_svc_main
		WHERE	parent_cd IS NOT NULL
		UNION	ALL
		SELECT	\'2\' AS svc_gbn, svc_cd, svc_nm
		FROM	cv_svc_sub
		WHERE	parent_cd IS NOT NULL';

$conn->query($sql);
$conn->fetch();

$rowCnt = $conn->row_count();

$colgroup = '
	<col width="40px">
	<col width="90px">
	<col width="150px">
	<col width="70px">
	<col width="30px" span="'.$rowCnt.'"
	<col>';?>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관코드</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th><?
			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);?>
				<th class="head"><?=$myF->mid($row['svc_nm'],0,1);?></th><?
			}
			$conn->row_free();?>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>

<div id="ID_BODY_LIST" style="overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody></tbody>
	</table>
</div>
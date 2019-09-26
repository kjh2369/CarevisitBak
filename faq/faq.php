<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$mode = $_GET['mode'];

	if ($mode == '101'){
	}else{
		$conn->close();
		exit;
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',1);
	});

	function lfSearch(aiPage){
		var html   = '';
		var page   = parseInt(aiPage,10);
		var maxCnt = 0;

		if (!page) page = 1;

		$.ajax({
			type: 'POST'
		,	url : './faq_list.php'
		,	data: {
				'page':0
			}
		,	beforeSend: function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success: function (result){
				maxCnt = parseInt(result,10);

				if (!maxCnt) maxCnt = 0;

				if (maxCnt > 0){
					$.ajax({
						type: 'POST'
					,	url : './faq_list.php'
					,	data: {
							'page':page
						,	'max' :maxCnt
						}
					,	success: function (data){
							var list = data.split(String.fromCharCode(1));

							for(var i=0; i<list.length; i++){
								if (list[i]){
									var val = list[i].split(String.fromCharCode(2));
									var tmp = val[0].split(String.fromCharCode(3));
									var key = tmp[0];
									var no  = tmp[1];

									var btn = '';

									if (val[6] == '<?=$gDomainID;?>'){
										btn = '<span class="btn_pack m"><button type="button" onclick="lfWrite('+key+');">수정</button></span> '
											+ '<span class="btn_pack m"><button type="button" onclick="">삭제</button></span>';
									}

									html += '<tr id="'+key+'">'
										 +  '<td class="center">'+(i+1)+'</td>'
										 +  '<td class="left">'+val[1]+'</td>'
										 +  '<td class="left"><a href="#" onclick="lfView($(this).parent().parent()); return false;">'+val[2]+'</a></td>'
										 +  '<td class="left">'+val[3]+'</td>'
										 +  '<td class="left">'+val[4]+'</td>'
										 +  '<td class="center">'+val[5]+'</td>'
										 +  '<td class="left last">'+btn+'</td>'
										 +  '</tr>';
								}
							}

							$('#listBody').html(html);
							$('#tempLodingBar').remove();
							_lfSetPageList(maxCnt,page);
						}
					,	error: function (request, status, error){
							alert('[ERROR]'
								 +'\nCODE : ' + request.status
								 +'\nSTAT : ' + status
								 +'\nMESSAGE : ' + request.responseText);
						}
					});
				}else{
					html = '<tr><td class="align_center" colspan="7">::등록된 데이타가 없습니다.::</td></tr>';
					$('#listBody').html(html);
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>

<div class="title title_border">FAQ</div>

<form id="f" name="f" method="post" enctype="multipart/form-data">
<?
	if ($mode == '101'){
		include_once('./faq_body.php');
	}else{
		include('../inc/_http_home.php');
		exit;
	}
?>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
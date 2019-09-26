<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	$arrCho	= Array('가','나','다','라','마','바','사','아','자','차','카','타','파','하');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#lblCNm').unbind('click').bind('click',function(){
			var obj	= $(this).parent().parent().parent().parent();

			var l	= $(obj).offset().left;
			var t	= $(obj).offset().top + $(obj).height() + 3;

			setTimeout('lfGetClientInfoList()',10);

			$('#divClientInfoList').css('left',l).css('top',t).show(300);

			return false;
		});
	});

	function lfSetClientInfoSvc(asSvcCd){
		$('span[id^="ciSvc"]').removeClass('bold');
		$('#ciSvc'+asSvcCd).addClass('bold');

		lfGetClientInfoList();
	}

	function lfSetClientInfoCho(asCho){
		$('span[id^="ciCho"]').removeClass('bold');
		$('#ciCho'+asCho).addClass('bold');

		lfGetClientInfoList();
	}

	function lfGetClientInfoList(){
		var svcCd	= $('span[id^="ciSvc"][class="bold"]').attr('value');
		var cho		= $('span[id^="ciCho"][class="bold"]').text();

		$.ajax({
			type	:'POST'
		,	url		:'../iljung/plan_client_info_list_get.php'
		,	data	:{
				'year'	:$('#planInfo').attr('year')
			,	'month'	:$('#planInfo').attr('month')
			,	'svcCd'	:svcCd
			,	'cho'	:cho
			}
		,	beforeSend	:function(){
			}
		,	success	:function(data){
				var row		= data.split(String.fromCharCode(1));
				var html	= '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col		= row[i].split(String.fromCharCode(2));
						var jumin	= col[0];
						var svcCd	= col[2];
						var svcNm	= __svcNm(svcCd);

						var name	= '<a href="#" onclick="opener.gPlanWin = null; opener._planReg(\'\',\''+$('#planInfo').attr('year')+'\',\''+$('#planInfo').attr('month')+'\',\''+jumin+'\',\''+svcCd+'\'); return false;">'+col[1]+'</a>';

						html	+= '<div style="height:25px; border-bottom:1px solid #cccccc;">';
						html	+= '<div class="nowrap" style="float:left; width:70px; padding-left:5px; border-right:1px solid #cccccc;">'+name+'</div>';
						html	+= '<div class="nowrap" style="float:left; width:70px; padding-left:5px; border-right:1px solid #cccccc;">'+svcNm+'</div>';
						html	+= '</div>';
					}
				}

				$('#bodyClientInfoList').html(html);
			}
		,	error	:function(request, status, error){
				alert(error);
			}
		});
	}
</script>
<div id="divClientInfoList" class="my_border_blue" style="position:absolute; z-index:10; width:auto; background-color:#ffffff; display:none;">
	<table class="my_table" style="width:600px;">
		<tbody>
			<tr>
				<th class="center" style="width:50px;">서비스</th>
				<td class="left last">
					<!--a href="#" onclick="lfSetClientInfoSvc('ALL'); return false;"><span id="ciSvcALL" value="ALL" class="bold">전체</span></a--><?
					$svcList	= $conn->svcKindSort($code,$gHostSvc['voucher']);

					foreach($svcList as $svcGbn => $arrSvc){
						foreach($arrSvc as $idx => $svc){
							if ($svcCd == $svc['code']){
								$clsStyle	= 'bold';
							}else{
								$clsStyle	= '';
							}?>
							<a href="#" onclick="lfSetClientInfoSvc('<?=$svc['code'];?>'); return false;"><span id="ciSvc<?=$svc['code'];?>" value="<?=$svc['code'];?>" class="<?=$clsStyle;?>"><?=$svc['name'];?></span></a><?
						}
					}?>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:600px;">
		<tbody>
			<tr>
				<th class="center bottom" style="width:30px; padding-top:5px; padding-bottom:5px; line-height:1.3em;">
					<div><a href="#" onclick="lfSetClientInfoCho('ALL'); return false;"><span id="ciChoALL" value="ALL" class="bold">전체</span></a></div><?
					foreach($arrCho as $i => $cho){?>
						<div><a href="#" onclick="lfSetClientInfoCho('<?=$i;?>'); return false;"><span id="ciCho<?=$i;?>" value="<?=$i;?>" class=""><?=$cho;?></span></a></div><?
					}?>
					<div><a href="#" onclick="lfSetClientInfoCho('Other'); return false;"><span id="ciChoOther" value="Other" class="">그외</span></a></div>
				</th>
				<td class="bottom last"><div id="bodyClientInfoList" style="overflow-y:scroll; width:100%; height:100%;"></div></td>
			</tr>
		</tbody>
	</table>
</div>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./longcare/mem_search.php'
		,	data :{
				'mode':'<?=$mode;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var list = data.split(String.fromCharCode(1));
				var html = '';

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						html += '<tr seq="'+val[0]+'" onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">'
							 +  '<td class="center">'+(i+1)+'</td>'
							 +  '<td class="center">'+val[1].substring(0,6)+'-'+val[1].substring(6,13)+'</td>'
							 +  '<td class="center"><div class="left nowrap" style="width:65px;">'+val[2]+'</div></td>'
							 +  '<td class="center"><div class="left nowrap" style="width:65px;"></div></td>'
							 +  '<td class="center">'+val[3]+'</td>'
							 +  '<td class="center"></td>'
							 +  '<td class="center last"></td>'
							 +  '</tr>'
					}
				}

				$('#body').html(html);
				$('#tempLodingBar').remove();

				lfLongcare();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLongcare(){
		var loginYn = __longcareLoginYn();

		if (!loginYn){
			alert('건보로그인을 하여 주십시오.');
			return false;
		}

		var date  = new Date();
		var year  = date.getFullYear();
		var month = date.getMonth()+1;
			month = (month < 10 ? '0' : '')+month;

		var laSvcKind = {'200':'001','500':'002','800':'003'};

		for(var i in laSvcKind){
			$.ajax({
				type : 'POST',
				url  : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=YR',
				data : {
					'serviceKind' : laSvcKind[i]
				,	'payMm'		  : year+month
				,	'fnc'		  : 'care'
				},
				beforeSend:function(){
					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
				}
			,	success: function (data){
					$('tr',$('#body')).each(function(){
						var row   = this;
						var jumin = $('td',row).eq(1).text();
						var tmpNo = '';
						var tmpNm = '';
						var wrkDt = '';

						if ($('td:contains("'+jumin+'")', data).length > 0){
							$('td:contains("'+jumin+'")', data).each(function(){
								tmpNo = $(this).attr('id').replace('careJuminNo','').replace('qlfNo','');
								tmpNm = $('#careNm'+tmpNo, data).text();
								wrkDt = $('#jobFrDt'+tmpNo, data).text();

								if ($('div',$('td',row).eq(2)).text() != tmpNm){
									tmpNm = '<span style="color:red;">'+tmpNm+'</span>';
								}

								if ($('td',row).eq(4).text() != wrkDt){
									wrkDt = '<span style="color:red;">'+wrkDt+'</span>';
								}

								$('div',$('td',row).eq(3)).html(tmpNm);
								$('td',row).eq(5).html(wrkDt);

								return false;
							});
						}
					});

					$('#bntWrokDtReg').attr('disabled',false);
					$('#tempLodingBar').remove();

					return false;
				},
				error: function (request, status, error){
					alert('[ERROR No.03]'
						 +'\nCODE : ' + request.status
						 +'\nSTAT : ' + status
						 +'\nMESSAGE : ' + request.responseText);
				}
			});
		}
	}

	function lfWorkStartDtReg(){
		var data = '';

		$('tr',$('#body')).each(function(){
			if ($('td',this).eq(5).text() && $('td',this).eq(4).text() != $('td',this).eq(5).text()){
				data += $(this).attr('seq')+String.fromCharCode(2)
					 +  $('td',this).eq(1).text()+String.fromCharCode(2)
					 +  $('td',this).eq(5).text()+String.fromCharCode(1);
			}
		});

		if (!data){
			 alert('등록할 데이타가 없습니다.');
			 return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./longcare/mem_reg.php'
		,	data :{
				'mode':'<?=$mode;?>'
			,	'data':data
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				alert(result);

				$('#tempLodingBar').remove();

				lfSearch();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<div class="title title_border">
	<div style="float:left; width:auto;">건보등록비교</div>
	<div style="float:right; width:auto;">
		<span class="btn_pack m" style="margin-top:8px;"><button id="bntWrokDtReg" type="button" disabled="true" onclick="lfWorkStartDtReg();">근무시작일자 등록</button></span>
	</div>
</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="95px">
		<col width="70px" span="2">
		<col width="70px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">주민번호</th>
			<th class="head" colspan="2">성명</th>
			<th class="head" colspan="2">근무시작일자</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">케어</th>
			<th class="head">건보</th>
			<th class="head">케어</th>
			<th class="head">건보</th>
		</tr>
	</thead>
	<tbody id="body"></tbody>
	<tfoot>
		<tr>
			<td class="center bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');?>

	<script type="text/javascript">
		$(document).ready(function(){
			$('input:text[name="txtTime"]').unbind('keyup').keyup(function(){
				if ($(this).val().length == $(this).attr('maxlength')){
					if ($(this).attr('id') == 'txtFromH'){
						$('#txtFromM').focus();
					}else if ($(this).attr('id') == 'txtFromM'){
						$('#txtToH').focus();
					}else if ($(this).attr('id') == 'txtToH'){
						$('#txtToM').focus();
					}else if ($(this).attr('id') == 'txtToM'){
					}
				}
			});

			$('input:radio[name="optTime"]').unbind('click').bind('click',function(){
				var time = $(this).val();

				$('#txtFromH').val(time.substr(0,2));
				$('#txtFromM').val(time.substr(2,2));
			});
		});

		//재가관리 서비스 조회
		function lfCareSvcFind(){
			var h = 600;
			var w = 800;
			var t = (screen.availHeight - h) / 2;
			var l = (screen.availWidth - w) / 2;

			var url = '../care/care_suga_find.php';
			var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
			var win = window.open('about:blank', 'FIND_CARESVC', option);
				win.opener = self;
				win.focus();

			var parm = new Array();
				parm = {
					'year':$('#planInfo').attr('year')
				,	'month':$('#planInfo').attr('month')
				,	'sr':$('#planInfo').attr('sr')
				,	'return':'lfCareSvcFindResult'
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

			form.setAttribute('target', 'FIND_CARESVC');
			form.setAttribute('method', 'post');
			form.setAttribute('action', url);

			document.body.appendChild(form);

			form.submit();
		}

		function lfCareSvcFindResult(cd,mstNm,proNm,svcNm,subNm){
			$('#lblSugaNm').text(subNm);
			$('#loSuga').attr('code',cd).attr('name',subNm);
			$('#lblResourceNm').text('');
			$('#lblSugaCost').text('0');
			$('#loResource')
				.attr('code','')
				.attr('name','')
				.attr('cost','0');
		}

		function lfResourceFind(){
			var h = 400;
			var w = 600;
			var t = (screen.availHeight - h) / 2;
			var l = (screen.availWidth - w) / 2;

			var url = '../care/care_resource_find.php';
			var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
			var win = window.open('about:blank', 'FIND_RESOURCE', option);
				win.opener = self;
				win.focus();

			var parm = new Array();
				parm = {
					'sugaCd':$('#loSuga').attr('code')
				,	'year':$('#planInfo').attr('year')
				,	'month':$('#planInfo').attr('month')
				,	'sr':$('#planInfo').attr('sr')
				,	'return':'lfResourceFindResult'
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

			form.setAttribute('target', 'FIND_RESOURCE');
			form.setAttribute('method', 'post');
			form.setAttribute('action', url);

			document.body.appendChild(form);

			form.submit();
		}

		function lfResourceFindResult(cd,cost,nm,per){
			if (!cd){
				alert('자원코드가 없습니다.\n관리자에게 문의하여 주십시오.');
				return;
			}
			$('#lblResourceNm').text(nm);
			$('#lblSugaCost').text(__num2str(cost));
			$('#loResource')
				.attr('code',cd)
				.attr('name',nm)
				.attr('cost',cost);
			$('#lblResName').text(per);

			if (per){
				$('#lblResYn').text('Y');
				$('#lblMemYn').text('');
			}

			$('#txtFromH').focus();
		}

		function lfMemberFind(){
			_planMemFind('', $('#centerInfo').attr('value'), $('#clientInfo').attr('value'), '<?=$svcCd;?>', '', '', 'lfMemFindResult');
		}

		function lfMemFindResult(obj){
			var col = __parseStr(obj);

			$('#lblMemName').attr('jumin',col['jumin']).text(col['name']);
		}
	</script>

	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="30px">
			<col width="200px">
			<col width="30px">
			<col width="200px">
			<col width="120px"><?
			if ($svcCd != 'S'){?>
				<col width="70px"><?
			}?>
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">제공서비스</th>
				<th class="head" colspan="2">서비스</th>
				<th class="head" colspan="2">자원</th>
				<th class="head">시작시간</th><?
				if ($svcCd != 'S'){?>
					<th class="head">수가</th><?
				}?>
				<th class="head last">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center"><?=($svcCd == 'S' ? '재가지원' : '자원연계');?></td>
				<td class="last"><span class="btn_pack find" style="margin-left:2px; margin-top:1px;" onclick="lfCareSvcFind(); return false;"></span></td>
				<td><span id="lblSugaNm"></span></td>
				<td class="last"><span class="btn_pack find" style="margin-left:2px; margin-top:1px;" onclick="lfResourceFind(); return false;"></span></td>
				<td><span id="lblResourceNm"></span></td>
				<td class="center"><? 
					
					//시간으로 표시 여푸
					$sql = 'select kacold_time_yn
							from   center_comm
							where  org_no = \''.$code.'\'';
					$kacoldTimeYn = $conn->get_data($sql);
					
					if($kacoldTimeYn!='Y'){ ?>
						<label><input id="optTimeFrom" name="optTime" type="radio" class="radio" value="1000" checked>오전</label>
						<label><input id="optTimeTo" name="optTime" type="radio" class="radio" value="1300">오후</label>
						<input id="txtFromH" name="txtTime" type="hidden" value="10" maxlength="2">
						<input id="txtFromM" name="txtTime" type="hidden" value="00" maxlength="2">
						<input id="txtToH" name="txtToH" type="hidden" value="" maxlength="0">
						<input id="txtToM" name="txtToH" type="hidden" value="" maxlength="0"><?
					}else { ?>
						<input id="txtFromH" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;"> :
						<input id="txtFromM" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;">
						<input id="txtToH" name="txtToH" type="hidden" value="" maxlength="0">
						<input id="txtToM" name="txtToH" type="hidden" value="" maxlength="0"><?
					} ?>
				</td><?
				if ($svcCd != 'S'){?>
					<td class="right"><span id="lblSugaCost">0</span></td><?
				}?>
				<td class="left last">&nbsp;</td>
			</tr>
		</tbody>
	</table>

	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="20px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center bottom">담당직원</th>
				<td class="bottom"><span class="btn_pack find" style="margin-left:2px; margin-top:1px;" onclick="lfMemberFind(); return false;"></span></td>
				<td class="bottom last"><span id="lblMemName" jumin="" class="left bold"></span></td>
			</tr>
		</tbody>
	</table>
	<div id="loSvcInfo" ynMakeSvc="<?=$ynMakeSvc;?>" style="display:none;"></div>
<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	/*
	 *	기관조회
	 */
?>
<script type="text/javascript">
	var winPos = {};

	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		lfSearch();
		GetValue('BRANCH','carevisit.net');
	});

	function lfResizeSub(){
		var h = $('#divBody').height() - $('#tfootList').height() - $('#divList').offset().top + $('#divBody').offset().top - 1;
		$('#divList').height(h);
	}

	function lfSearch(page){
		if (!page) page= 1;

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuFile;?>_search.php'
		,	data:{
				'menuId':'<?=$menuId;?>'
			,	'orgNo':$('#txtOrgNo').val()
			,	'orgNm':$('#txtOrgNm').val()
			,	'manager':$('#txtManager').val()
			,	'CMSno':$('#txtCMSno').val()
			,	'CMSgbn':$('#cboCMSGbn').val()
			,	'connFrom':$('#txtConnFrom').val()
			,	'connTo':$('#txtConnTo').val()
			,	'contFrom':$('#txtContFrom').val()
			,	'contTo':$('#txtContTo').val()
			,	'company':$('#cboCompany').val()
			,	'branch':$('#cboBranch').val()
			,	'person':$('#cboPerson').val()
			,	'useCenter':$('#cboUseCenter').val()
			,	'connDt':$('#chkConnDt').attr('checked') ? 'Y' : 'N'
			,	'taxbillYn':$('#chkTaxbillYn').attr('checked') ? 'Y' : 'N'
			,	'page':page
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
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

	function ShowCenterScreen(did, url, id, pw){
		var tgt = 'WINDOW_CENTER_'+did;
		var win = window.open('about:blank',tgt);
		var frm = document.createElement('form');

		frm.appendChild(__create_input('loc', 'admin'));
		frm.appendChild(__create_input('uCode', id));
		frm.appendChild(__create_input('uPass', pw));
		frm.setAttribute('method', 'post');

		document.body.appendChild(frm);

		frm.target = tgt;
		frm.action = url;
		frm.submit();
	}

	function lfExcel(gbn){
		var parm = new Array();
			parm = {
				'gbn':gbn
			,	'orgNo':$('#txtOrgNo').val()
			,	'orgNm':$('#txtOrgNm').val()
			,	'manager':$('#txtManager').val()
			,	'CMSno':$('#txtCMSno').val()
			,	'CMSgbn':$('#cboCMSGbn').val()
			,	'connFrom':$('#txtConnFrom').val()
			,	'connTo':$('#txtConnTo').val()
			,	'contFrom':$('#txtContFrom').val()
			,	'contTo':$('#txtContTo').val()
			,	'company':$('#cboCompany').val()
			,	'branch':$('#cboBranch').val()
			,	'person':$('#cboPerson').val()
			,	'useCenter':$('#cboUseCenter').val()
			,	'connDt':$('#chkConnDt').attr('checked') ? 'Y' : 'N'
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
		form.setAttribute('action', './center_CENTER_LIST_excel.php');

		document.body.appendChild(form);

		form.submit();
	}

	function ShowPayIn(orgNo){
		window.open('./pop_payin_list.php?orgNo='+orgNo, 'POP_PAYIN_LIST', 'left='+((screen.availWidth - 1024) / 2)+', top='+((screen.availHeight - 600) / 2)+', width=1024, height=600, status=no, menubar=no, toolbar=no, resizeable=no').focus();
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
		<col width="250px">
	</colgroup>
	<tbody>
		<tr>
			<td class="bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col width="50px">
						<col width="50px">
						<col width="50px">
						<col width="50px">
						<col width="50px">
						<col width="60px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">기관코드</th>
							<td><input id="txtOrgNo" type="text"></td>
							<th class="center">기관명</th>
							<td><input id="txtOrgNm" type="text"></td>
							<th class="center">대표자</th>
							<td><input id="txtManager" type="text" style="width:70px;"></td>
							<th class="center">CMS번호</th>
							<td class="last"><input id="txtCMSno" type="text" style="width:50px;"></td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col width="170px">
						<col width="60px">
						<col width="172px">
						<col width="60px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">연결기간</th>
							<td><input id="txtConnFrom" type="text" class="date">~<input id="txtConnTo" type="text" class="date"></td>
							<th class="center">계약기간</th>
							<td><input id="txtContFrom" type="text" class="date">~<input id="txtContTo" type="text" class="date"></td>
							<th class="center">CMS구분</th>
							<td class="last">
								<select id="cboCMSGbn" style="width:auto;">
									<option value="" selected>전체</option>
									<option value="Y">등록</option>
									<option value="N">미등록</option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">연결지사</th>
							<td class="last">
								<div class="nowrap" style="float:left; width:auto;">
									<select id="cboCompany" style="width:auto; margin-right:0;" onchange="GetValue('BRANCH',this.value);">
										<option value="">전체</option><?
										$sql = 'SELECT	b00_code AS cd
												,		b00_name AS nm
												,		b00_manager AS manager
												,		b00_domain AS domain
												FROM	b00branch
												WHERE	b00_com_yn	= \'Y\'
												AND		b00_stat	= \'1\'
												ORDER	BY nm';

										$conn->query($sql);
										$conn->fetch();

										$rowCnt = $conn->row_count();

										for($i=0; $i<$rowCnt; $i++){
											$row = $conn->select_row($i);?>
											<option value="<?=$row['domain'];?>" <?=$row['domain'] == 'carevisit.net' ? '' : '';?>><?=$row['nm'];?></option><?
										}

										$conn->row_free();?>
									</select>
									<select id="cboBranch" style="width:auto; margin:0;" onchange="GetValue('PERSON',this.value);">
										<option value="">-지사선택-</option>
									</select>
									<select id="cboPerson" style="width:auto; margin-left:0;">
										<option value="">-담당자-</option>
									</select>
								</div>
								<div style="float:right; width:auto;">
									<select id="cboUseCenter" style="width:auto; margin-left:0;">
										<option value="">전체기관</option>
										<option value="Y" selected>사용기관</option>
										<option value="N">미사용기관</option>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<th class="center">정렬</th>
							<td class="last">
								<div style="float:left; width:auto;"><label><input id="chkConnDt" type="checkbox" class="checkbox" value="Y">연결일자순</label></div>
								<div style="float:right; width:auto; margin-right:5px;"><label><input id="chkTaxbillYn" type="checkbox" class="checkbox" value="Y">세금계산서 발행기관</label></div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
				<span class="btn_pack m"><button onclick="lfMenu('CENTER_REG');">등록</button></span><br>
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel('1');">미계약</button></span>
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel('2');">CMS계약</button></span>
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel('3');">무통장계약</button></span>
			</td>
		</tr>
	</tbody>
</table><?
$colgroup = '
		<col width="40px">
		<col width="90px">
		<col width="150px">
		<col width="90px">
		<col width="50px">
		<col width="70px">
		<col width="50px">
		<col width="70px">
		<col width="50px">
		<col width="50px">
		<col>';?>
<table id="tblList" class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" colspan="4">기관정보</th>
			<th class="head" rowspan="2">연결일자</th>
			<th class="head" rowspan="2">팝업<br>설정</th>
			<th class="head" colspan="2">계약정보</th>
			<th class="head" rowspan="2">계산서<br>발행</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">코드</th>
			<th class="head">명칭</th>
			<th class="head">CMS</th>
			<th class="head">대표자</th>
			<th class="head">일자</th>
			<th class="head">담당자</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top last" colspan="20">
				<div id="divList" style="overflow-x:hidden; overflow-y:scroll; height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td id="tfootList" class="center bottom last" colspan="20"></td>
		</tr>
	</tfoot>
</table>
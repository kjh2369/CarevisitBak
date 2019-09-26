<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$iljungYm = $myF->dateAdd('month', -1, Date('Y-m-d'), 'Y-m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
		lfSearch();
	});

	function lfResizeSub(){
		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		var h = document.body.offsetHeight - $(obj).offset().top - $('#copyright').height() - 26;
		$(obj).height(h);
	}

	function lfSearch(page){
		if (!page) page = 1;

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'domain':$('#cboDomain').val()
			,	'orgNo':$('#txtOrgNo').val()
			,	'orgNm':$('#txtOrgNm').val()
			,	'mgNm':$('#txtMgNm').val()
			,	'yymm':$('#txtYYMM').val()
			,	'CMSno':$('#txtCMSNo').val()
			//,	'CMS2':$('#chkCMS2').attr('checked') ? 'Y' : 'N'
			//,	'CMS1':$('#chkCMS1').attr('checked') ? 'Y' : 'N'
			//,	'CMS3':$('#chkCMS3').attr('checked') ? 'Y' : 'N'
			//,	'CMSX':$('#chkCMSX').attr('checked') ? 'Y' : 'N'
			,	'connGbn':$('input:radio[name="optConnGbn"]:checked').val()
			,	'use':$('#cboUseYn').val()
			,	'contCom':$('#cboContCom').val()
			,	'taxbill':$('#cboTaxbill').val()
			,	'contDt':$('#txtContDt').val().replace(/-/g, '')
			,	'page':page
			}
		,	beforeSend:function(){
				$('#ID_LIST').html('');
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();

				var obj = __GetTagObject($('#ID_LIST'),'DIV');
				$(obj).scrollTop(0);
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'domain':$('#cboDomain').val()
			,	'orgNo':$('#txtOrgNo').val()
			,	'orgNm':$('#txtOrgNm').val()
			,	'mgNm':$('#txtMgNm').val()
			,	'yymm':$('#txtYYMM').val()
			,	'CMSno':$('#txtCMSNo').val()
			//,	'CMS2':$('#chkCMS2').attr('checked') ? 'Y' : 'N'
			//,	'CMS1':$('#chkCMS1').attr('checked') ? 'Y' : 'N'
			//,	'CMS3':$('#chkCMS3').attr('checked') ? 'Y' : 'N'
			//,	'CMSX':$('#chkCMSX').attr('checked') ? 'Y' : 'N'
			,	'connGbn':$('input:radio[name="optConnGbn"]:checked').val()
			,	'use':$('#cboUseYn').val()
			,	'taxbill':$('#cboTaxbill').val()
			,	'contCom':$('#cboContCom').val()
			,	'contDt':$('#txtContDt').val().replace(/-/g, '')
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
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="50px">
		<col width="90px">
		<col width="100px">
		<col width="50px">
		<col width="100px">
		<col width="70px">
		<col width="130px">
		<col width="70px">
		<col width="80px">
		<col width="80px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">도메인</th>
			<td>
				<select id="cboDomain" style="width:auto;" onchange="GetValue('BRANCH',this.value);">
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
						<option value="<?=$row['domain'];?>" <?=$row['domain'] == 'carevisit.net' ? 'selected' : '';?>><?=$row['nm'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
			<th class="center">일정등록년월</th>
			<td><input id="txtYYMM" type="text" value="<?=$iljungYm;?>" class="yymm"></td>
			<th class="center">계약사</th>
			<td>
				<select id="cboContCom" style="width:auto;">
					<option value="">전체</option>
					<option value="1">굿이오스</option>
					<option value="2">지케어</option>
					<option value="3">케어비지트</option>
					<option value="X">미등록</option>
				</select>
			</td>
			<th class="center">CMS번호</th>
			<td><input id="txtCMSNo" type="text"></td>
			<th class="center">사용여부</th>
			<td>
				<select id="cboUseYn" style="width:auto;">
					<option value="ALL">전체</option>
					<option value="USE" selected>사용기관</option>
				</select>
			</td>
			<th class="center">세금계산서</th>
			<td>
				<select id="cboTaxbill" style="width:auto;">
					<option value="">전체</option>
					<option value="Y">발행기관</option>
					<option value="N">미발행기관</option>
				</select>
			</td>
			<td class="left" rowspan="2">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
				<span class="btn_pack m"><button onclick="lfExcel();">Excel</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">기관명</th>
			<td><input id="txtOrgNm" type="text" style="width:100%;"></td>
			<th class="center">기관기호</th>
			<td><input id="txtOrgNo" type="text" style="width:100%;"></td>
			<th class="center">대표자</th>
			<td><input id="txtMgNm" type="text" style="width:100%;"></td>
			<!--th class="center">CMS구분</th>
			<td colspan="5">
				<label><input id="chkCMS2" type="checkbox" class="checkbox" value="Y">지케어</label>
				<label><input id="chkCMS1" type="checkbox" class="checkbox" value="Y">굿이오스</label>
				<label><input id="chkCMS3" type="checkbox" class="checkbox" value="Y">케어비지트</label>
				<label><input id="chkCMSX" type="checkbox" class="checkbox" value="Y">무통장</label>
			</td-->
			<th class="center">계약구분</th>
			<td colspan="3">
				<label><input name="optConnGbn" type="radio" value="" class="radio" checked>전체</label>
				<label><input name="optConnGbn" type="radio" value="N" class="radio">신규</label>
				<label><input name="optConnGbn" type="radio" value="S" class="radio">서비스</label>
			</td>
			<th class="center">계약일자</th>
			<td>
				<input id="txtContDt" type="text" class="date">
			</td>
		</tr>
	</tbody>
</table><?
$colgroup = '
	<col width="40px">
	<col width="90px">
	<col width="150px">
	<col width="100px">
	<col width="70px">
	<col width="80px">
	<col width="80px" span="3">
	<col width="70px" span="3">
	<col width="50px">
	<col width="70px" span="3">
	<col width="40px" span="2">
	<col width="30px">
	<col width="60px">
	<col width="150px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">기관기호</th>
			<th class="head" rowspan="2">기관명</th>
			<th class="head" rowspan="2">도메인</th>
			<th class="head" rowspan="2">대표자</th>
			<th class="head" rowspan="2">연락처</th>
			<th class="head" rowspan="2">연결일</th>
			<th class="head" rowspan="2">계약일</th>
			<th class="head" rowspan="2">적용기간</th>
			<th class="head" colspan="4">CMS / 무통장</th>
			<th class="head" rowspan="2">계산요금</th>
			<th class="head" rowspan="2">현재요금</th>
			<th class="head" rowspan="2">요금차액</th>
			<th class="head" colspan="2">수급자</th>
			<th class="head" rowspan="2">과금<br>구분</th>
			<th class="head" rowspan="2">조정요금<br>관리기관</th>
			<th class="head" rowspan="2">이메일</th>
			<th class="head last" rowspan="2">
				<img src="" style="width:50px; height:1px;">
			</th>
		</tr>
		<tr>
			<th class="head">지케어</th>
			<th class="head">굿이오스</th>
			<th class="head">케어비지트</th>
			<th class="head">무통장</th>
			<th class="head">명수</th>
			<th class="head">일정</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top" colspan="23">
				<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
				<table class="my_table" style="width:100%;">
					<colgroup><?=$colgroup;?></colgroup>
					<tbody id="ID_LIST"></tbody>
				</table>
				</div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td id="ID_ROW_PAGELIST" class="center bottom" colspan="20">PAGE LIST</td>
		</tr>
	</tfoot>
</table>
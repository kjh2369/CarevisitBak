<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year = Date('Y');
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoad()',200);
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYear').text());

		year += pos;

		$('#lblYear').text(year);

		lfLoad();
	}

	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYear').text()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">';

						if (col['mstCd']){
							html += '<td class="center" rowspan="'+col['mstCnt']+'" style="line-height:1.3em;">'+col['mstNm']+'</td>';
						}

						if (col['proCd']){
							html += '<td class="center" rowspan="'+col['proCnt']+'" style="line-height:1.3em;">'+col['proNm']+'</td>';
						}

						var target = '';
						var budget = '';

						if (__str2num(col['target']) > 0){
							target = __num2str(col['target'])+(col['gbn'] == '1' ? '명' : '회');
						}

						if (__str2num(col['budget']) > 0){
							budget = __num2str(col['budget']);
						}
						
						html += '<td class="left" style="line-height:1.3em;"><div class="nowrap" title="'+col['svcNm']+'" style="width:110px;">'+col['svcNm']+'</div></td>';
						html += '<td class="center">'+target+'</td>';
						html += '<td class="right">'+budget+'</td>';
						html += '<td class="center">'+col['cnt']+'</td>';
						html += '<td class="left" style="line-height:1.3em;"><div class="nowrap" title="'+col['cont']+'" style="width:140px;">'+col['cont']+'</div></td>';
						html += '<td class="left" style="line-height:1.3em;"><div class="nowrap" title="'+col['effect']+'" style="width:130px;">'+col['effect']+'</div></td>';
						html += '<td class="left" style="line-height:1.3em;"><div class="nowrap" title="'+col['eval']+'" style="width:110px;">'+col['eval']+'</div></td>';
						html += '<td class="left last">';
						html += '<span class="btn_pack m"><button type="button" onclick="lfPlanReg($(this).parent().parent().parent(),\''+col['suga']+'\');">수정</button></span> ';
						html += '<span class="btn_pack m"><button type="button" onclick="lfPlanDel($(this).parent().parent().parent(),\''+col['suga']+'\')">삭제</button></span>';
						html += '</td>';
						html += '</tr>';
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfPlanReg(obj,code){
		var objModal = new Object();
		var url = './care_plan_reg.php';
		var style = 'dialogWidth:600px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.type	= '<?=$type;?>_POP';
		objModal.year	= $('#lblYear').text();
		objModal.sr		= '<?=$sr;?>';
		objModal.code	= code;

		window.showModalDialog(url, objModal, style);

		if (objModal.result != 1) return;

		lfLoad();
		return;

		$('td',obj).eq(3).text(__num2str(objModal.target)+(objModal.gbn == '1' ? '명' : '회')); //목표
		$('td',obj).eq(4).text(__num2str(objModal.budget)); //예산
		$('td',obj).eq(5).text(objModal.cnt); //횟수
		$('div',$('td',obj).eq(6)).attr('title',objModal.cont).text(objModal.cont); //사업내용
		$('div',$('td',obj).eq(7)).attr('title',objModal.effect).text(objModal.effect); //기대효과
		$('div',$('td',obj).eq(8)).attr('title',objModal.eval).text(objModal.eval); //평가도구
	}

	function lfPlanDel(obj,code){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_plan_delete.php'
		,	data :{
				'sr':'<?=$sr;?>'
			,	'year':$('#lblYear').text()
			,	'code':code
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
					lfLoad();
				}else{
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfPlanCopy(){
		if (!confirm('전년도 사업계획을 복사하시겠습니까?\n선택 년도의 작성된 사업계획은 삭제됩니다.')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_plan_copy.php'
		,	data :{
				'sr':'<?=$sr;?>'
			,	'year':$('#lblYear').text()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리 되었습니다.');
					lfLoad();
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYear').text()
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

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './care_plan_excel.php');

		document.body.appendChild(form);

		form.submit();
	}

</script>
<div class="title title_border">사업계획(<?=lfGetSPName($sr);?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="center last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="right last">
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">엑셀</button></span>
				<span class="btn_pack m"><button onclick="lfPlanCopy();">전년 사업계획 복사</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="20px" span="2">
		<col width="110px">
		<col width="60px">
		<col width="70px">
		<col width="60px">
		<col width="140px">
		<col width="130px">
		<col width="110px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2" colspan="2">사업<br>분류</th>
			<th class="head" rowspan="2">세부사업명</th>
			<th class="head" colspan="3">계획</th>
			<th class="head" rowspan="2">사업내용</th>
			<th class="head" rowspan="2">기대효과</th>
			<th class="head" rowspan="2">수행 및<br>평가도구</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">목표</th>
			<th class="head">예산</th>
			<th class="head">횟수</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>
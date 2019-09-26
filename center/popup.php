<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$popId	= $_POST['popId'];
	$popup	= $_POST['popup'];

	if ($popup){
		$sql = 'SELECT	*
				FROM	center_popup
				WHERE	pop_id = \''.$popId.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$sql = 'SELECT	m00_store_nm
					FROM	m00center
					WHERE	m00_mcode = \''.$row['org_no'].'\'
					ORDER	BY m00_mkind
					LIMIT	1';

			$name = $conn->get_data($sql);

			$selCode .= '/'.$row['org_no'];
			$div .= '<div id="divId_'.$row['org_no'].'" class="nowrap" style="float:left; width:32%;">'.$name.'('.$row['org_no'].')</div>';

			if ($i == 0){
				$fromDt = $row['from_dt'];
				$toDt	= $row['to_dt'];
				$contents = $row['contents'];
			}
		}

		$conn->row_free();
	}else{
		include_once('../inc/_body_header.php');
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
		$('#divCenterList').height(__GetHeight($('#divCenterList')) - $('#divCenterPageList').height() - $('#copyright').height());
		$('#divSelList').height(__GetHeight($('#divSelList')) - $('#copyright').height());
		lfSearch();
	});

	function lfSearch(page){
		var maxCnt = 0;

		$.ajax({
			type :'POST'
		,	async:false
		,	url  :'../find/_find_center_search.php'
		,	data :{
				'mode':'LIST'
			,	'page':'0'
			,	'pCnt':'5'
			,	'code':$('#txtCode').val()
			,	'name':$('#txtName').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				maxCnt = __str2num(data);
			}
		,	error:function(){
			}
		}).responseXML;

		if (!page) page = 1;

		$.ajax({
			type :'POST'
		,	url  :'../find/_find_center_search.php'
		,	data :{
				'mode':'LIST'
			,	'pCnt':'5'
			,	'page':page
			,	'max':maxCnt
			,	'code':$('#txtCode').val()
			,	'name':$('#txtName').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var html = '';
				var row = data.split(String.fromCharCode(1));

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = row[i].split(String.fromCharCode(2));

						html += '<tr code="'+col[1]+'">'
							 +	'<td class="center"><div class="left">'+col[1]+'</div></td>'
							 +	'<td class="center last"><div class="left">'+col[2]+'</div></td>'
							 +	'</tr>';
					}
				}

				$('#tbodyCenterList').html(html);
				$('tr',$('#tbodyCenterList')).css('cursor','default').unbind('mouseover').bind('mouseover',function(){
					var code = '/'+$(this).attr('code');
					if ($('#selCode').val().indexOf(code) >= 0) return;
					$(this).css('background-color','#FAF4C0');
				}).unbind('mouseout').bind('mouseout',function(){
					var code = '/'+$(this).attr('code');
					if ($('#selCode').val().indexOf(code) >= 0) return;
					$(this).css('background-color','#FFFFFF');
				}).unbind('click').bind('click',function(){
					var id = $(this).attr('code');
					var code = '/'+id;

					if ($('#selCode').val().indexOf(code) >= 0){
						$('#selCode').val($('#selCode').val().split(code).join(''));
						$(this).css('background-color','#FFFFFF');

						$('#divId_'+id).remove();
					}else{
						$('#selCode').val($('#selCode').val()+code);
						$(this).css('background-color','#D9E5FF');

						var div = '';

						div = '<div id="divId_'+id+'" class="nowrap" style="float:left; width:32%;">'+$('td',this).eq(1).text()+'('+$('td',this).eq(0).text()+')</div>';

						if ($('div:last',$('#divSelList')).length > 0){
							$('div:last',$('#divSelList')).after(div);
						}else{
							$('#divSelList').html(div);
						}
					}
				});

				$('tr',$('#tbodyCenterList')).each(function(){
					var code = '/'+$(this).attr('code');
					if ($('#selCode').val().indexOf(code) >= 0){
						$(this).css('background-color','#D9E5FF');
					}
				});

				_lfSetPageList(maxCnt,page,5);

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">팝업등록</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="250px">
		<col width="1px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">기관리스트</th>
			<th class="head"></th>
			<th class="head last">팝업내용</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top center bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="90px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">기관코드</th>
							<td class="last"><input id="txtCode" type="text" value="" style="width:100%;"></td>
						</tr>
						<tr>
							<th class="center">기관명</th>
							<td class="last"><input id="txtName" type="text" value="" style="width:100%;"></td>
						</tr>
						<tr>
							<td class="center last" colspan="2">
								<div class="right"><span class="btn_pack m"><button onclick="lfSearch();">조회</button></span></div>
							</td>
						</tr>
						<tr>
							<th class="center">기관코드</th>
							<th class="center last">기관명</th>
						</tr>
					</tbody>
					<tbody>
						<td class="top center bottom last" colspan="2">
							<div id="divCenterList" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
								<table class="my_table" style="width:100%;">
									<colgroup>
										<col width="90px">
										<col>
									</colgroup>
									<tbody id="tbodyCenterList"></tbody>
								</table>
							</div>
							<div id="divCenterPageList" style="border-top:1px solid #CCCCCC;">&nbsp;<? include_once('../inc/_page_script.php');?>&nbsp;</div>
						</td>
					</tbody>
				</table>
			</td>
			<td class="top center bottom"></td>
			<td class="top center bottom last">
				<div class="left" style="padding:5px;">
					팝업기간 : <input id="txtFrom" type="text" class="date" value="<?=$fromDt;?>"> ~ <input id="txtTo" type="text" class="date" value="<?=$toDt;?>">
				</div>
				<div id="divContents" style="padding:5px;">
					<link href="../editor/css/default.css" rel="stylesheet" type="text/css" />
					<script type="text/javascript" src="../editor/js/HuskyEZCreator.js" charset="utf-8"></script>

					<textarea name="ir1" id="ir1" style="width:100%; height:300px;" tag="내용을 입력하여 주십시오."><?=$contents;?></textarea>
					<textarea id="back_content" name="back_content" style="display:none;"></textarea>

					<script>
						var form = document.f;

						var oEditors = [];

						nhn.husky.EZCreator.createInIFrame(oEditors, "ir1", "../editor/SEditorSkin.html", "createSEditorInIFrame", null, false);

						function insertIMG(fname){
							var filepath = form.filepath.value;
							var sHTML = "<img src='" + filepath + "/" + fname + "' style='cursor:hand;' border='0'>";
							oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]);
						}

						function pasteHTMLDemo(){
							sHTML = "<span style='color:#FF0000'>이미지 등도 이렇게 삽입하면 됩니다.</span>";
							oEditors.getById["ir1"].exec("PASTE_HTML", [sHTML]);
						}

						function showHTML(){
							alert(oEditors.getById["ir1"].getIR());
						}

						function lfShow(){
							var width	= 330;
							var height	= 400;
							var left	= (screen.availWidth - width) / 2;
							var top		= (screen.availHeight - height) / 2;

							oEditors.getById["ir1"].exec("UPDATE_IR_FIELD", []);
							$('#back_content').val(document.getElementById("ir1").value);

							var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
							var win = window.open('', 'POPUP_SHOW', option);
								win.opener = self;
								win.focus();

							var parm = new Array();
								parm = {
									'contents':$('#back_content').val()
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

							form.setAttribute('target', 'POPUP_SHOW');
							form.setAttribute('method', 'post');
							form.setAttribute('action', './popup_show.php');

							document.body.appendChild(form);

							form.submit();
						}

						function lfSave(){
							if (!$('#selCode').val()){
								alert('선택된 기관이 없습니다. 확인하여 주십시오.');
								return;
							}

							if (!$('#txtFrom').val() || !$('#txtTo').val()){
								alert('팝업기간을 입력하여 주십시오.');
								if (!$('#txtFrom').val()) $('#txtFrom').focus(); else $('#txtTo').focus();
								return;
							}

							oEditors.getById["ir1"].exec("UPDATE_IR_FIELD", []);
							$('#back_content').val(document.getElementById("ir1").value);

							if (!$('#back_content').val()){
								alert('내용을 입력하여 주십시오.');
								return;
							}

							$.ajax({
								type :'POST'
							,	url  :'./popup_save.php'
							,	data :{
									'popId':$('#popId').val()
								,	'selCode':$('#selCode').val()
								,	'fromDt':$('#txtFrom').val()
								,	'toDt':$('#txtTo').val()
								,	'contents':$('#back_content').val()
								}
							,	beforeSend:function(){
									$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
								}
							,	success:function(result){
									if (__resultMsg(result)){
									}

									$('#tempLodingBar').remove();
								}
							,	error:function(){
								}
							}).responseXML;
						}
					</script>
				</div>
				<div class="center" style="padding:5px;">
					<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
					<span class="btn_pack m"><button onclick="lfShow();">미리보기</button></span>
				</div>
				<div id="divSelList" class="left" style="padding:5px; width:100%; height:100px; overflow-x:hidden; overflow-y:scroll; border-top:1px solid #CCCCCC;"><?=$div;?></div>
			</td>
		</tr>
	</tbody>
</table>
<input id="popId" type="hidden" value="<?=$popId;?>">
<input id="selCode" type="hidden" value="<?=$selCode;?>">
<?
	if (!$popup){
		include_once('../inc/_body_footer.php');
	}

	include_once('../inc/_footer.php');
?>
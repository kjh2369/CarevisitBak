<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$jumin	= $ed->de($_POST['jumin']);
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$month	= ($month < 10 ? '0' : '').$month;
	$lastday= $myF->lastDay($year, $month);
	$objId	= $_POST['objId'];

	if ($SR == 'S'){
		$title = '재가지원';
	}else if ($SR == 'R'){
		$title = '자원연계';
	}else{
		exit;
	}

	//대상자 정보
	$sql = 'SELECT	m03_name AS name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'6\'
			AND		m03_jumin = \''.$jumin.'\'';

	$row = $conn->get_array($sql);

	$name = $row['name'];

	Unset($row);

	//이용정보
	$sql = 'SELECT	mp_gbn
			,		from_dt
			,		to_dt
			FROM	client_his_svc
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		svc_cd	 = \''.$SR.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		from_dt <= NOW()
			AND		to_dt	>= NOW()';

	$row = $conn->get_array($sql);

	if (!$row){
		$sql = 'SELECT	mp_gbn
				,		from_dt
				,		to_dt
				FROM	client_his_svc
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		svc_cd	 = \''.$SR.'\'
				AND		jumin	 = \''.$jumin.'\'
				ORDER	BY from_dt DESC, to_dt DESC
				LIMIT	1';
		$row = $conn->get_array($sql);
	}

	$mpGbn	= $row['mp_gbn'] == 'Y' ? '중점' : '일반';
	$fromDt = $myF->dateStyle($row['from_dt'],'.');
	$toDt	= $myF->dateStyle($row['to_dt'],'.');

	Unset($row);
?>
<script type="text/javascript">
	IsCalBtnShow = false;
	defaultCalDate = '<?=$year;?>-<?=$month;?>-01';

	window.onunload = function(){
		$('#<?=$objId;?>',opener.document).text($('tbody',$('#tbodyRecList')).length);
	}

	$(document).ready(function(){
		//$('#divBody').height(__GetHeight($('#divBody')) - $('#txtOther').parent().height() - $('#divFoot').height());
		$('#divBody').height(570);
		$('input:text').each(function(){
			__init_object(this);
		});

		lfLoadRecList();
		lfLoadSvcList();
	});

	function lfNew(){
		$('#lblMem').attr('jumin','').text('');
		$('#txtDate').val('');
		$('#txtFrom').val('');
		$('#txtTo').val('');
		$('#txtOther').val('');
		$('textarea').each(function(){
			$(this).val('');
		});
		$('tbody[selYn="Y"]',$('#tbodyRecList')).each(function(){
			$(this).attr('selYn','N').css('background-color',$(this).attr('bgclr'));
		});
		$('tr[id^="trSvc_"]',$('#tbodySugaList')).hide();
	}

	function lfLoadRecList(){
		$.ajax({
			type :'POST'
		,	url  :'./care_rec_provide_q.php'
		,	data :{
				'type'	:'REC_LIST'
			,	'SR'	:'<?=$SR;?>'
			,	'jumin'	:'<?=$ed->en($jumin);?>'
			,	'year'	:'<?=$year;?>'
			,	'month'	:'<?=$month;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyRecList').html(html);
				$('tbody',$('#tbodyRecList')).attr('selYn','N').css('cursor','default').unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','D9E5FF');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color',$(this).attr('bgclr'));
				}).unbind('click').bind('click',function(){
					$('tbody[selYn="Y"]',$('#tbodyRecList')).each(function(){
						$(this).attr('selYn','N').css('background-color',$(this).attr('bgclr'));
					});

					$(this).attr('selYn','Y').css('background-color','FAF4C0');

					lfLoadInfo($(this).attr('seq'));
					lfLoadSvcList($(this).attr('seq'));
				});

				$('#tempLodingBar').remove();
				$('tbody:last',$('#tbodyRecList')).click();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadInfo(seq){
		if (!seq) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_rec_provide_q.php'
		,	data :{
				'type'	:'SVC_INFO'
			,	'SR'	:'<?=$SR;?>'
			,	'year'	:'<?=$year;?>'
			,	'month'	:'<?=$month;?>'
			,	'jumin'	:'<?=$ed->en($jumin);?>'
			,	'seq'	:seq
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				$('#lblMem').attr('jumin',col['mem']).text(col['name']);
				$('#txtDate').val(col['date']);
				$('#txtFrom').val(col['from']);
				$('#txtTo').val(col['to']);
				$('#txtOther').val(col['other']);

				lfDaySvc(col['date']);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadSvcList(seq){
		if (!seq) seq = '';

		$.ajax({
			type :'POST'
		,	url  :'./care_rec_provide_q.php'
		,	data :{
				'type'	:'SVC_LIST'
			,	'SR'	:'<?=$SR;?>'
			,	'year'	:'<?=$year;?>'
			,	'month'	:'<?=$month;?>'
			,	'jumin'	:'<?=$ed->en($jumin);?>'
			,	'seq'	:seq
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodySugaList').html(html);
				$('#tempLodingBar').remove();

				$('textarea').each(function(){
					__init_object(this);
				});
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfMemFind(){
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url    = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win    = window.open('about:blank', 'FIND_MEMBER', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type'	:'member'
			,	'kind'	:'<?=$SR;?>'
			,	'return':'lfMemFindResult'
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

		form.setAttribute('target', 'FIND_MEMBER');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfMemFindResult(obj){
		var col = __parseStr(obj);

		$('#lblMem').attr('jumin',col['jumin']).text(col['name']);
	}

	function lfChkDt(obj){
		var minDt = __getDate($(obj).attr('minDt'));
		var maxDt = __getDate($(obj).attr('maxDt'));
		var nowDt = __getDate($(obj).val());

		if (nowDt >= minDt && nowDt <= maxDt){
			lfDaySvc(nowDt);
		}else{
			alert('일자 입력오유입니다. 확인하여 주십시오.');
			$(obj).val('').focus();
			return false;
		}
	}

	function lfSave(){
		if (!$('#lblMem').attr('jumin')){
			alert('담당자를 선택하여 주십시오.');
			return;
		}

		if (!$('#txtDate').val()){
			alert('제공일자를 입력하여 주십시오.');
			$('#txtDate').focus();
			return;
		}

		if (!$('#txtFrom').val() || !$('#txtTo').val()){
			alert('제공시간을 입력하여 주십시오.');

			if (!$('#txtFrom').val()) $('#txtFrom').focus();
			if (!$('#txtTo').val()) $('#txtTo').focus();

			return;
		}

		var data = {};

		data['type']	= 'SAVE';
		data['SR']		= '<?=$SR;?>'
		data['jumin']	= '<?=$ed->en($jumin);?>';
		data['year']	= '<?=$year;?>';
		data['month']	= '<?=$month;?>';
		data['seq']		= $('tbody[selYn="Y"]',$('#tbodyRecList')).attr('seq') ? $('tbody[selYn="Y"]',$('#tbodyRecList')).attr('seq') : ''
		data['date']	= $('#txtDate').val();
		data['fromTime']= $('#txtFrom').val();
		data['toTime']	= $('#txtTo').val();
		data['memCd']	= $('#lblMem').attr('jumin');

		var text = '';

		$('textarea').each(function(){
			var id = $(this).attr('id');

			if (id == 'txtOther') return;

			var val = $(this).val();

			if (text) text += '&';

			text += (id+'='+val);
		});

		data['data']	= text;
		data['other']	= $('#txtOther').val();

		$.ajax({
			type :'POST'
		,	url  :'./care_rec_provide_q.php'
		,	data :data
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
					lfLoadRecList();
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		var data = {};

		data['type']	= 'DELETE';
		data['SR']		= '<?=$SR;?>'
		data['jumin']	= '<?=$ed->en($jumin);?>';
		data['year']	= '<?=$year;?>';
		data['month']	= '<?=$month;?>';
		data['seq']		= $('tbody[selYn="Y"]',$('#tbodyRecList')).attr('seq') ? $('tbody[selYn="Y"]',$('#tbodyRecList')).attr('seq') : ''

		$.ajax({
			type :'POST'
		,	url  :'./care_rec_provide_q.php'
		,	data :data
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
					lfLoadRecList();
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDaySvc(date){
		if (!date) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_rec_provide_q.php'
		,	data :{
				'type'	:'SVC_DAY'
			,	'SR'	:'<?=$SR;?>'
			,	'jumin'	:'<?=$ed->en($jumin);?>'
			,	'date'	:date
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = data.split('/');

				$('tr[id^="trSvc_"]',$('#tbodySugaList')).hide();

				for(var i=0; i<col.length; i++){
					$('#trSvc_'+col[i],$('#tbodySugaList')).show();
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div id="lsTitle" class="title title_border">서비스 제공기로기지(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="210px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center top">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col width="*">
					</colgroup>
					<thead>
						<tr>
							<th class="head last" colspan="2">대상자정보</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>대상자명</th>
							<td class="left last"><?=$name;?></td>
						</tr>
						<tr>
							<th>중점여부</th>
							<td class="left last"><?=$mpGbn;?></td>
						</tr>
						<tr>
							<th class="bottom">이용기간</th>
							<td class="left bottom last"><?=$fromDt;?> ~ <?=$toDt;?></td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%; border-top:1px solid #0e69b0;">
					<colgroup>
						<col width="30px">
						<col width="90px">
						<col width="*">
					</colgroup>
					<thead>
						<tr>
							<th class="head last" colspan="4">
								<div style="float:left; width:auto;"><?=$year;?>년 <?=IntVal($month);?>월 제공기록지</div>
								<div style="float:right; width:auto; margin-right:5px;">
									<span class="btn_pack small"><button onclick="lfNew();">신규</button></span>
								</div>
							</th>
						</tr>
					</thead>
					<tbody id="tbodyRecList">
						<!--
						<tr>
							<td class="center" rowspan="2">1회</td>
							<td class="left">2014.01.01</td>
							<td class="left last" rowspan="2">홍길동</td>
						</tr>
						<tr>
							<td class="left">09:00 ~ 10:00</td>
						</tr>
						-->
					</tbody>
				</table>
			</td>
			<td class="center top last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col width="50px">
						<col width="60px">
						<col width="117px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="">담당자</th>
							<td class="left" colspan="3">
								<div style="float:left; width:auto; height:25px;"><span class="btn_pack find" onclick="lfMemFind();"></span></div>
								<div id="lblMem" class="bold" jumin=""></div>
							</td>
							<td class="last" rowspan="2">
								<span class="btn_pack m" style="margin-left:5px;"><span class="excel"></span><button onclick="">엑셀</button></span>
							</td>
						</tr>
						<tr>
							<th class="">제공일자</th>
							<td><input id="txtDate" type="text" value="" class="date" onchange="lfChkDt(this);" minDt="<?=$year;?>-<?=$month;?>-01" maxDt="<?=$year;?>-<?=$month;?>-<?=$lastday;?>"></td>
							<th class="">제공일시</th>
							<td class="">
								<input id="txtFrom" type="text" value="" class="no_string" alt="time"> ~
								<input id="txtTo" type="text" value="" class="no_string" alt="time">
							</td>
						</tr>
					</tbody>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head bold">서비스</th>
							<th class="head bold last">제공내용</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="center top last" colspan="2">
								<div id="divBody" style="width;100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
									<table class="my_table" style="width:100%;">
										<colgroup>
											<col width="70px">
											<col>
										</colgroup>
										<tbody id="tbodySugaList"></tbody>
									</table>
								</div>
							</td>
						</tr>
						<tr>
							<th>비고</th>
							<td class="last"><textarea id="txtOther" style="width:100%; height:35px;"></textarea></td>
						</tr>
						<tr>
							<td class="center bottom top last" colspan="2">
								<div id="divFoot" style="padding:5px;">
									<span class="btn_pack m"><button onclick="lfNew();">신규</button></span>
									<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
									<span class="btn_pack m"><button onclick="lfDelete();">삭제</button></span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>
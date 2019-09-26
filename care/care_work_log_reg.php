<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$sugaCd	= $_POST['sugaCd'];
	$jumin	= $ed->de($_POST['jumin']);
	$target	= $ed->de($_POST['target']);
	$date	= $_POST['date'];
	$key	= $_POST['key'];
	$objId	= $_POST['objId'];

	if ($SR == 'S'){
		$title = '재가지원';
	}else if ($SR == 'R'){
		$title = '자원연계';
	}else{
		exit;
	}

	//직원명
	$sql = 'SELECT	DISTINCT m02_yname
			FROM	m02yoyangsa
			WHERE	m02_ccode = \''.$orgNo.'\'
			AND		m02_yjumin= \''.$jumin.'\'';

	$memNm = $conn->get_data($sql);

	//생년월일
	$birthday = $myF->issToBirthday($jumin,'.');

	//대상자
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'6\'
			AND		m03_jumin = \''.$target.'\'';

	$tgNm = $conn->get_data($sql);

	$sql = 'SELECT	from_time
			,		to_time
			FROM	care_work_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		suga_cd	= \''.$sugaCd.'\'
			AND		mem_cd	= \''.$jumin.'\'
			AND		date	= \''.$date.'\'
			AND		jumin	= \''.$target.'\'';

	$row = $conn->get_array($sql);

	$fromTime	= $myF->timeStyle($row['from_time']);
	$toTime		= $myF->timeStyle($row['to_time']);

	Unset($row);
?>
<script type="text/javascript">
	var IsSave = false;

	window.onunload = function(){
		if (IsSave){
			opener.lfRegResult('<?=$objId;?>');
		}
	}

	$(document).ready(function(){
		__fileUploadInit($('#f'), 'fileUploadCallback');

		$('input:text').each(function(){
			__init_object(this);
		});

		var obj = __GetTagObject($('#tbodyItem'),'DIV');
		$(obj).height(__GetHeight($(obj)));

		lfLoadItem();
	});

	function lfLoadItem(){
		$.ajax({
			type :'POST'
		,	url  :'./care_work_log_load_item.php'
		,	data :{
				'SR'	:'<?=$SR;?>'
			,	'jumin'	:'<?=$ed->en($jumin);?>'
			,	'sugaCd':'<?=$sugaCd;?>'
			,	'target':'<?=$ed->en($target);?>'
			,	'date'	:'<?=$date;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyItem').html(html);
				$('textarea').each(function(){
					__init_object(this);
				});
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCheckImg(obj){
		if (!__checkImageExp2(obj)){
			return;
		}
		var path = __get_file_path(obj);
		var obj = __GetTagObject(obj,'TR');

		$('#lblPicImg',obj).html(path);
	}

	function lfSave(){
		if (!$('#txtFromTime').val()){
			alert('제공시간을 입력하여 주십시오.');
			$('#txtFromTime').focus();
			return;
		}

		if (!$('#txtToTime').val()){
			alert('제공시간을 입력하여 주십시오.');
			$('#txtToTime').focus();
			return;
		}

		var data = '';

		$('textarea',$('#tbodyItem')).each(function(){
			data += (data ? '?' : '');
			data += 'seq='+$(this).attr('seq')+'&contents='+$(this).val();
		});

		$.ajax({
			type :'POST'
		,	url  :'./care_work_log_reg_save.php'
		,	data :{
				'SR'	:'<?=$SR;?>'
			,	'sugaCd':'<?=$sugaCd;?>'
			,	'jumin'	:'<?=$ed->en($jumin);?>'
			,	'target':'<?=$ed->en($target);?>'
			,	'date'	:'<?=$date;?>'
			,	'from'	:$('#txtFromTime').val()
			,	'to'	:$('#txtToTime').val()
			,	'data'	:data
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 'ERROR'){
					alert('저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					IsSave = true;
					fileUpload();
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function fileUpload(){
		var frm = $('#f');

		var parm = new Array();
			parm = {
				'SR'	:'<?=$SR;?>'
			,	'sugaCd':'<?=$sugaCd;?>'
			,	'jumin'	:'<?=$ed->en($jumin);?>'
			,	'target':'<?=$ed->en($target);?>'
			,	'date'	:'<?=$date;?>'
			};

		//var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			document.f.appendChild(objs);
		}

		frm.attr('action', './care_work_log_pic_upload.php');
		frm.submit();
	}

	function fileUploadCallback(data, state){
		if (__fileUploadCallback(data, state)){
			alert('정상적으로 처리되었습니다.');
		}else{
			alert('저장중 오류가 발생하였습니다.\n관리자에게 문의하여 주십시오.');
		}
	}

	function lfExcel(){
		opener.lfExcel('<?=$ed->en($jumin);?>','<?=$ed->en($target);?>','<?=$date;?>',document);
	}
</script>
<div class="title title_border">업무일지 작성(<?=$title;?>)</div>
<form id="f" name="f" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="55px">
		<col width="80px">
		<col width="60px">
		<col width="120px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>담당자</th>
			<td colspan="3">&nbsp;<?=$memNm;?></td>
			<td class="last" rowspan="3">
				<div class="left">
					<span class="btn_pack m"><span class="save"></span><button onclick="lfSave();">저장</button></span>
					<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">엑셀</button></span>
				</div>
			</td>
		</tr>
		<tr>
			<th>대상자</th>
			<td colspan="3">&nbsp;<?=$tgNm?></td>
		</tr>
		<tr>
			<th>제공일자</th>
			<td>&nbsp;<?=$myF->dateStyle($date,'.');?></td>
			<th>제공시간</th>
			<td>
				<input id="txtFromTime" type="text" value="<?=$fromTime;?>" class="no_string" alt="time"> ~
				<input id="txtToTime" type="text" value="<?=$toTime;?>" class="no_string" alt="time">
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="140px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center bold">서비스</th>
			<th class="center bold last">제공내용</th>
		</tr>
		<tr>
			<td class="top last" colspan="2">
				<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="140px">
							<col>
						</colgroup>
						<tbody id="tbodyItem"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
</form>
<?
	include_once('../inc/_footer.php');
?>
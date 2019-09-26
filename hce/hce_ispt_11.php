<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	사정기록지 - 약도
	 *********************************************************/

	if ($_POST['hcptSeq']) $tmpHcptSeq = $_POST['hcptSeq'];
	if (!$tmpHcptSeq) $tmpHcptSeq = $hce->rcpt;

	//약도 파일
	$userMap = '../hce/user_map/'.$orgNo.'/'.$hce->SR.'/'.$hce->IPIN.'_'.$tmpHcptSeq.'.jpg';

	//주소
	$sql = 'SELECT	addr
			,		addr_dtl
			FROM	hce_receipt
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'';

	$row = $conn->get_array($sql);

	$addr = $row['addr'].' '.$row['addr_dtl'];

	Unset($row);

	$isptSeq = '1';


	$sql = 'SELECT	rough_text
			,		rough_file
			FROM	hce_inspection_needs
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$row = $conn->get_array($sql);

	$roughText = StripSlashes($row['rough_text']);
	$roughFile = $row['rough_file'];

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		__fileUploadInit($('form[name="f"]'), 'fileUploadCallback');
	});

	function fileUploadCallback(data, state){
		if (state == 'success'){
			$('#imgMapView').attr('src','<?=$userMap;?>?timestamp=' + new Date().getTime()).show();
			alert('정상적으로 처리되었습니다.');
		}else{
			alert('약도 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		}
	}

	//네이버 지도
	function lfLoadMap(){
		var w = 800;
		var h = 600;
		var l = (screen.availWidth - w) / 2;
		var t = (screen.availHeight - h) / 2;


		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=yes';
		//var url = 'http://map.naver.com/?query=<?=urlencode($addr);?>';
		var url = 'http://map.naver.com?query=<?=$addr;?>';
		//var url = 'http://map.naver.com/';
		var win = window.open(url, 'MAP', option);
			win.opener = self;
			win.focus();
		/*
		var parm = new Array();
			parm = {
				'query':'<?=$addr;?>'
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

		form.setAttribute('target', 'MAP');
		form.setAttribute('method', 'get');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
		*/
	}

	//맵확인
	function lfShowImg(obj){
		
		if (!__checkImageExp3(obj)){
			return;
		}


		//윈도우9이상 버전에서 생기는 문제로 아래의 함수를 건너뜀.
		return;

		var path;
		
	
		try{
			path = __get_file_path(obj);
		}catch(e){
			alert('ERROR\n'+e);
			return;
		}
		
		$('#imgMapView').hide();
		$('#divMapView').css('filter',"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='file://"+path+"', sizingMethod='scale')").show();
	}

	function lfSave(){
		var frm = $('form[name="f"]');
			frm.attr('action', './hce_ispt_7_map_upload.php');
			frm.submit();
	}

	function lfRemove(file){
		$.ajax({
			type :'POST'
		,	url  :'./map_remove.php'
		,	data :{
				'file':file
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					$('#divMapView').html('');	
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last">- 약도</th>
		</tr>
		<tr>
			<td class="left last">
				<div style="float:left; width:auto;"><span class="btn_pack small"><button type="button" onclick="lfLoadMap();" style="color:#666666;">지도보기</button></span></span></div>
				<div style="float:left; width:50px; margin-left:3px; margin-top:-1px; background:url(../image/find_file.gif) no-repeat left 50%;">
					<input type="file" name="imgMap" id="imgMap" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-5px;" onchange="lfShowImg(this);">
				</div>
				<div style="float:left; width:auto;"><span class="btn_pack small"><button type="button" onclick="lfRemove('<?=$userMap;?>');" style="color:#666666;">삭제</button></span></span></div>
			</td>
		</tr>
		<tr>
			<td class="last">
				<div id="divMapView" style="height:300px;">
					<img id="imgMapView" src="<?=$userMap;?>?timestamp=<?=Date();?>" border="0" >
				</div>
				<!--iframe src="./hce_naver_map.php" width="100%" height="290" frameborder="0"></iframe-->
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">설명</th>
			<td><textarea id="txtRough" name="txtRough" style="width:100%; height:35px;"><?=$roughText;?></textarea></td>
		</tr>
	</tbody>
</table>
<input id="bodyIdx" type="hidden" value="11">
<input id="isptSeq" name="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>
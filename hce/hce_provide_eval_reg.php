<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_hce.php');
	include_once('../inc/_ed.php');

	/******************************************
	 *	제공평가서
	 ******************************************/

	$orgNo	= $_SESSION['userCenterCode'];
	$evlSeq	= $_GET['evlSeq'] != '' ? $_GET['evlSeq'] : $_GET['seq'];
	$copyYn = $_GET['copyYn'];

	$sql = 'SELECT	m03_name AS name
			,		m03_jumin AS jumin
			,		IFNULL(jumin.jumin, m03_jumin) AS real_jumin
			,		m03_tel AS phone, m03_hp AS mobile
			,		m03_juso1 AS addr, m03_juso2 AS addr_dtl
			FROM	m03sugupja
			LEFT	JOIN	mst_jumin AS jumin
					ON		jumin.org_no= m03_ccode
					AND		jumin.gbn	= \'1\'
					AND		jumin.code	= m03_jumin
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';

	$row = $conn->get_array($sql);

	$name	= $row['name'];
	$jumin	= SubStr($row['real_jumin'].'0000000',0,13);
	$gender	= $myF->issToGender($jumin);
	$jumin	= $myF->issStyle($jumin);
	$phone	= $myF->phoneStyle($row['phone'] ? $row['phone'] : $row['mobile'],'.');
	$addr	= $row['addr'].' '.$row['addr_dtl'];

	Unset($row);

	if ($evlSeq){
		$sql = 'SELECT	*
				FROM	hce_provide_evl
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		evl_seq	= \''.$evlSeq.'\'';
		
		$R = $conn->get_array($sql);

		if ($R['evl_cd']){
			$sql = 'SELECT	DISTINCT m02_yname
					FROM	m02yoyangsa
					WHERE	m02_ccode = \''.$orgNo.'\'
					AND		m02_yjumin= \''.$R['evl_cd'].'\'';

			$evlNm = $conn->get_data($sql);
		}
	}

	if($copyYn == 'Y'){
		$evlSeq = '';
	}
	
?>
<script type="text/javascript">
	function lfMemFindResult(obj){
		var obj = __parseStr(obj);
		$('#txtMger').attr('jumin',obj['jumin']).val(obj['name']);
	}

	function lfSave(){
		if (!$('#txtPEDt').val()){
			alert('평가일자를 입력하여 주십시오.');
			$('#txtPEDt').focus();
			return;
		}

		if (!$('#txtMger').val()){
			alert('사례관리자를 입력하여 주십시오.');
			lfMemFind();
			return;
		}

		var data = {};

		data['evlSeq'] = $('#evlSeq').val();
		data['mgerJumin'] = $('#txtMger').attr('jumin');

		$('input:text, textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'./hce_provide_eval_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./hce_provide_eval_delete.php'
		,	data:{
				'evlSeq':$('#evlSeq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					location.href="../hce/hce_body.php?sr=<?=$sr;?>&type=141";
					top.frames['frmTop'].lfTarget();
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}


	function lfCopy(){
		var objModal = new Object();
		var url      = './hce_copy.php?type=142';
		var style    = 'dialogWidth:500px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		//objModal.code  = $('#code').val();
		objModal.type  = '142';


		window.showModalDialog(url, objModal, style);

		var result = objModal.para;
		
		if (!result) return;

		var arr = result.split('&');
		var val = new Array();

		for(var i=0; i<arr.length; i++){
			var tmp = arr[i].split('=');
			
			val[tmp[0]] = tmp[1];
		}


		location.href = '../hce/hce_body.php?sr=S&type=142&seq='+val['seq']+'&r_seq='+val['r_seq']+'&copyYn=Y';

		//$('#strCname').text(val['name']);
		//$('#param').attr('value', 'jumin='+val['jumin']);
	}

</script>

<div class="my_border_blue" style="margin-top:10px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col width="90px">
			<col width="50px">
			<col width="30px">
			<col width="70px">
			<col width="90px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">대상자명</th>
				<td class="left"><?=$name;?></td>
				<th class="center">주민번호</th>
				<td class="left"><?=$jumin;?></td>
				<th class="center">성별</th>
				<td class="center"><?=$gender;?></td>
				<th class="center">전화번호</th>
				<td class="left"><?=$phone;?></td>
				<td class="left bottom last" rowspan="3">
					<?  if (!$evlSeq && $copyYn != 'Y'){?>
							<span class="btn_pack m"><button type="button" onclick="lfCopy();">복사</button></span><?
						}
					?>
					<span class="btn_pack m"><span class="add"></span><button onclick="lfSave();">저장</button></span>
					<span class="btn_pack m"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=141" target="frmBody">리스트</a></span><br><?
					if ($evlSeq){?>
						<span class="btn_pack m"><span class="delete"></span><button onclick="lfDelete();">삭제</button></span>
						<span class="btn_pack m"><span class="pdf"></span><button onclick="lfPDF('<?=$type;?>','<?=$evlSeq;?>');">출력</button></span><?
					}?>
				</td>
			</tr>
			<tr>
				<th class="center">주소</th>
				<td class="left" colspan="7"><?=$addr;?></td>
			</tr>
			<tr>
				<th class="center bottom">평가일자</th>
				<td class="bottom">
					<input id="txtPEDt" type="text" value="<?=$myF->dateStyle($R['evl_dt']);?>" class="date">
				</td>
				<th class="center bottom">사례관리자</th>
				<td class="bottom" colspan="5">
					<div style="float:left; width:auto; height:25px; padding:1px 0 0 5px;"><span class="btn_pack find" onclick="lfMemFind();"></span></div>
					<div style="float:left; width:auto; padding-top:2px;"><input id="txtMger" type="text" value="<?=$evlNm;?>" jumin="<?=$ed->en($R['evl_cd']);?>" style="margin-left:0;" alt="not" readonly></div>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="my_border_blue" style="margin-top:10px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">제공<br>서비스<br>내용</th>
				<td class="last">
					<textarea id="txtSvcCont" style="width:100%; height:75px;"><?=StripSlashes($R['svc_cont']);?></textarea>
				</td>
			</tr>
			<tr>
				<th class="center">사례<br>평가<br>내용</th>
				<td class="last">
					<textarea id="txtEvlCont" style="width:100%; height:150px;"><?=StripSlashes($R['evl_cont']);?></textarea>
				</td>
			</tr>
			<tr>
				<th class="center">향후<br>계획</th>
				<td class="last">
					<textarea id="txtAfterPlan" style="width:100%; height:75px;"><?=StripSlashes($R['after_plan']);?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<input id="evlSeq" type="hidden" value="<?=$evlSeq;?>">
<?
	Unset($R);
	include_once('../inc/_db_close.php');
?>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$id		= $_POST['id'];
	$orgNo	= $_POST['orgNo'];
	$contDt	= $_POST['contDt'];
	$gbn	= $_POST['gbn'];

	//기관명
	$sql = 'SELECT	DISTINCT m00_store_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'';
	$orgNm = $conn->get_data($sql);

	//기존입력정보
	$sql = 'SELECT	doc_type, file_path, stop_dt
			FROM	cv_doc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cont_dt	= \''.$contDt.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		if (!$stopDt) $stopDt = $row['stop_dt'];
		$doc[$row['doc_type']] = $row['file_path'];
	}

	$conn->row_free();
?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('input:text').each(function(){
				__init_object(this);
			});
		});

		function lfDocSave(){
			if (!$('#txtStopDt').val()){
				alert('프로그램 중지일자를 입력하여 주십시오.');
				$('#txtStopDt').focus();
				return;
			}

			$.ajax({
				type :'POST'
			,	url  :'./center_connect_info_ask.php'
			,	data :{
					'orgNo':'<?=$orgNo;?>'
				,	'contDt':$('#txtFromDt').attr('orgDt')
				,	'stopDt':$('#txtStopDt').val()
				,	'type':'01,02'
				,	'gbn':'<?=$gbn;?>'
				}
			,	beforeSend:function(){
				}
			,	success:function(result){
					if (!result){
						alert('정상적으로 처리되었습니다.');
					}else{
						alert(result);
					}
				}
			,	error:function(){
				}
			}).responseXML;
		}
	</script>
	<div style="padding:100px;">
		<div style="position:relative; text-align:right;">
			<a href="#" onclick="$('#<?=$id;?>').hide();"><img src="../image/btn_exit.png"></a>
		</div><?
	if ($gbn == '1'){?>
		<table class="my_table" style="width:100%; border:2px solid #4374D9; background-color:WHITE;">
			<tbody>
				<tr>
					<td class="bold left">※ 문서요청</td>
				</tr>
				<tr>
					<td style="padding:5px;"><p style="text-align:justify;">
						알려드립니다!<br>
						현재 <?=$orgNm;?>는(은) 케어비지트와 계약이 되어있지 않습니다.<br>
						<input id="txtStopDt" type="text" value="<?=$myF->dateStyle($stopDt);?>" class="date" style="margin-left:0;">까지 아래의 "계약서다운"을 받아 날인 및 계약서 하단의 CMS동의 란 작성 하시고, 그 계약서를 스캔 받아 "계약서등록"에 올려주시고 사어자등록증(고유번호증)도 스캔 받아 "사업자등록"에 올려 주십시오.<br><br>
						지정일까지 계약서가 등록되지 않으면 프로그램이 중단 됩니다.</p>
					</td>
				</tr>
				<tr>
					<td class="center" style="padding:10px;"><?
						if (is_array($doc)){?>
							<div class="left"><?
							foreach($doc as $type => $path){
								if ($type == '01'){
									$str = '계약서';
								}else if ($type == '02'){
									$str = '등록증';
								}

								if ($path){?>
									[<a href="<?=$path;?>" target="_blank"><?
								}else{?>
									[<span style="color:RED;"><?
								}?>

								등록된 <?=$str;?><?=$path ? ' 보기' : '가(이) 없습니다.';

								if ($path){?>
									</a>]<?
								}else{?>
									</span>]<?
								}
							}?>
							</div><?
						}?>
						<div><span class="btn_pack m"><button onclick="lfDocSave();">저장</button></span></div>
					</td>
				</tr>
			</tbody>
		</table><?
	}?>
	</div><?
	include_once('../inc/_db_close.php');
?>
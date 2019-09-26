<?
	include_once('../../inc/_db_open.php');


	$sql = 'select *
			  from medical_request
			 where org_no = \''.$_SESSION['userCenterCode'].'\'
			   and complete_yn = \'N\'
			   and cancel_yn = \'N\'
			   and del_flag = \'N\'
			 order by seq asc
			 limit 1 ';
	
	$row = $conn -> get_array($sql);
	
	$completeYn = $row['complete_yn'];
	


?>
<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title>방문간호지시서 의료기관 신청하기</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv='imagetoolbar' content='no'>
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />

<style type="text/css">
body,a, abbr, acronym, address, applet, article, aside, audio,b, blockquote, big,center, canvas, caption, cite, code, command,datalist,del, details, dfn, div,
em, embed,fieldset, figcaption, figure, font, footer, form, h1, h2, h3, h4, h5, h6, header, hgroup, html,i, iframe, img, ins,kbd, keygen,label, legend, li,meter,nav, menu,
object, ol, output,p, pre, progress,q,s, samp, section, small, span, source, strike, strong, sub, sup,table, tbody, tfoot, thead, th, tr, tdvideo, tt,u, ul, var
{margin:0; padding:0; border:0; font-size:9pt;}
/*write
============================================================*/
#WirteForm{margin-top:10px;}
#WirteForm p.check{ margin:0; padding-right:5px; text-align:right;}
#WirteForm p.check img{vertical-align:middle;}
.write_type,.write_type th,.write_type td{border:0;}
.write_type{width:330px;border-top:2px solid #0e69b0; font-size:12px;table-layout:fixed; margin-left:34px;}
.write_type caption{display:none}
.write_type th{padding:8px 0 8px 15px; border-bottom:1px solid #dcdcdc; border-right:1px solid #dcdcdc; background-color:#f7faff; color:#5877a3;font-weight:bold;text-align:left;}
.write_type th label	{display:block;}
.write_type th img{vertical-align:middle;}
.write_type td{padding:8px 0 8px 8px; border-bottom:1px solid #e0e4e7; text-align:left;  line-height:1.5em; }
.write_type td select{border:1px solid #cccccc; vertical-align:middle; height:20px; line-height:18px; padding:2px;}
.write_type td input{border:1px solid #cccccc; vertical-align:middle; height:26px; line-height:22px; padding-left:5px;}
.write_type td textarea{ border:1px solid #cccccc;  padding:5px; color:#565960;}
</style>
<script type="text/javascript" src="../../js/script.js"></script>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript">
	
	$(document).ready(function(){
		
		if('<?=$row[insert_id]?>' == '' || '<?=$completeYn;?>' == 'Y'){
			$('#Reg').show();
			$('#Moid').hide();
		}else {
			$('#Reg').hide();
			$('#Moid').show();
		}
		
		resize();	
		
	});

	//저장
	function lfSave(mode){
		
		if($('#optArea').val() == ''){
			alert('지역을선택하여주십시오.');
			$('#optArea').focus();
			return;
		}

		var data = {};
		
		$('input:text').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';
			data[id] = val;
		});
				
		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';
			
			data[id] = val;
		});
	
		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();
			
			data[id] = val;
		});

		$('select').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;			
		});
		
		data['mode'] = mode;
		data['seq'] = '<?=$row[seq]?>';

		$.ajax({
			type:'POST'
		,	url:'./request_ok.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				var result = result.split('//');
		
				if (result[0] == 1){
					alert('정상적으로 신청되었습니다.');
					if(mode == 'reg'){
						$('#Reg').hide();
						$('#Modi').show();
					}else if(mode == 'del'){
						$('#Reg').show();
						$('#Modi').hide();
					}
				}else if (result[0] == 9){
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
</script>
</head>

<body>
<div style="width:400px; height:546px; background:#e5e6ed  url( img/img_bg.png) left top no-repeat;">
	<table  class="write_type mg_top25 mg_left22" style="position:absolute; top:280px;" border="1" cellspacing="0" summary="세미나 장소,시간,주소">
		<caption>방문간호지시서 의료기관 신청</caption>
		<colgroup>
			<col width="90" />
			<col width="*"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="row"><label for="optArea">지역선택</label></th>
				<td>
					<select id="optArea" name="optArea" style="width:150px;">
					<option value="">- 선택 -</option>
					<option value="01" <?=($row['request_area'] == '01' ? 'selected' : '');?>>서울-서대문</option>
					<option value="02" <?=($row['request_area'] == '02' ? 'selected' : '');?>>서울-은평,강동</option>
					<option value="03" <?=($row['request_area'] == '03' ? 'selected' : '');?>>부산-동래</option>
					<option value="04" <?=($row['request_area'] == '04' ? 'selected' : '');?>>대구-달서</option>
					<option value="05" <?=($row['request_area'] == '05' ? 'selected' : '');?>>인천-부평</option>
					<option value="06" <?=($row['request_area'] == '06' ? 'selected' : '');?>>광주-서구</option>
					<option value="07" <?=($row['request_area'] == '07' ? 'selected' : '');?>>경기-일산</option>
					<option value="08" <?=($row['request_area'] == '08' ? 'selected' : '');?>>경기-광명</option>
					<option value="09" <?=($row['request_area'] == '09' ? 'selected' : '');?>>경남-창원</option>
					<option value="10" <?=($row['request_area'] == '10' ? 'selected' : '');?>>세종시-조치원읍</option>
					<option value="11" <?=($row['request_area'] == '11' ? 'selected' : '');?>>충북-청주</option>
					<option value="12" <?=($row['request_area'] == '12' ? 'selected' : '');?>>경남-함양</option>
					<option value="13" <?=($row['request_area'] == '13' ? 'selected' : '');?>>경북-김천,상주</option>
					<option value="14" <?=($row['request_area'] == '14' ? 'selected' : '');?>>강원-삼척</option>
					<option value="15" <?=($row['request_area'] == '14' ? 'selected' : '');?>>강원-원주,횡성</option>
					<option value="16" <?=($row['request_area'] == '15' ? 'selected' : '');?>>전남-목포,영암</option>
					</select>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th scope="row"><label for="txtCont">문의내용</label></th>
				<td>
				 <textarea rows="6" cols="25" id="txtCont"><?=stripslashes($row['request_cont'])?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="Reg" style="height:70px; text-align:center; width:100%; position:relative; top:440px;">
		<a href="#" onclick="lfSave('reg');" ><img alt="참가신청하기" src="img/btn_request.png" /></a>
	</div>
	<div id="Modi" style="height:70px; text-align:center; width:100%; position:relative; top:440px;">
		<a href="#" onclick="lfSave('modi');" ><img alt="수정하기" src="img/btn_modify.png" /></a>
		<a href="#" onclick="lfSave('del');" ><img alt="취소하기" src="img/btn_del.png" /></a>
	</div>
</div>
<div>
</body>
</html>
<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	*
			FROM	cv_tax_his
			WHERE	org_no	= \''.$orgNo.'\'
			AND		acct_ym	= \''.$yymm.'\'';

	$R = $conn->get_array($sql);
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		var page = 1;
		var loading = false; //to prevents multipal ajax loads

		$('#ID_REG_LIST').scroll(function() { //detect page scroll
			if($('#ID_REG_LIST').scrollTop() + $('#ID_REG_LIST').height() == $('#ID_REG_LIST').attr('scrollHeight')){  //user scrolled to bottom of the page?
				if (loading == false){ //there's more data to load
					loading = true; //prevent further ajax loading

					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();

					//load data from the server using a HTTP POST request
					$.post('./center_TAX_reg_search.php',{'company':$('#cboCompany').val(),'orgNo':'<?=$orgNo;?>' ? '<?=$orgNo;?>' : $('#txtOrgNo').val(),'orgNm':$('#txtOrgNm').val(),'mgNm':$('#txtMgNm').val(),'addr':$('#txtAddr').val(),'page':(page+1)}, function(html){
						if (html){
							//$("#results").append(data); //append received data into the element
							$('tbody',$('#ID_REG_LIST')).append(html);

							//hide loading image
							$('#tempLodingBar').remove(); //hide loading image once data is received

							page ++;
							loading = false;
						}else{
							$('#tempLodingBar').remove();
						}
					}).fail(function(xhr, ajaxOptions, thrownError) { //any errors?

						alert(thrownError); //alert with HTTP error
						$('#tempLodingBar').remove(); //hide loading image
						loading = false;

					});
				}
			}
		});

		lfRegSearch();
	});

	function lfRegSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_TAX_reg_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'orgNo'	:'<?=$orgNo;?>' ? '<?=$orgNo;?>' : $('#txtOrgNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'mgNm'	:$('#txtMgNm').val()
			,	'addr'	:$('#txtAddr').val()
			,	'page'	:1
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_REG_LIST')).html(html);
				$('#tempLodingBar').remove();

				if ('<?=$orgNo;?>'){
					$('tr:first',$('tbody',$('#ID_REG_LIST'))).click();
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfRegSelOrg(obj){
		$('tbody tr',$('#ID_REG_LIST')).css('font-weight','normal').css('color','');
		$(obj).css('font-weight','bold').css('color','BLUE');
		$('#ID_CELL_REG_ORGNO').text($(obj).attr('orgNo'));
	}

	function lfRegSave(){
		if (!$('#txtIssYm').val()){
			alert('적용년월을 입력하여 주십시오.');
			$('#txtIssYm').focus();
			return;
		}

		if (!$('#txtIssDt').val()){
			alert('발급일자을 입력하여 주십시오.');
			$('#txtIssDt').focus();
			return;
		}

		if (!$('#ID_CELL_REG_ORGNO').text()){
			alert('기관을 선택하여 주십시오.');
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./center_TAX_reg_save.php'
		,	data:{
				'orgNo'	:$('#ID_CELL_REG_ORGNO').text()
			,	'issYm'	:$('#txtIssYm').val()
			,	'issDt'	:$('#txtIssDt').val()
			,	'crGbn'	:$('input:radio[name="optCrGbn"]:checked').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');

					$('#ID_LOCAL_POP_DATA').html('');
					$('#ID_LOCAL_POP').hide();

					lfSearch();
				}else{
					alert(result);
				}
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
</script>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup>
		<col width="70px">
		<col width="50px">
		<col width="70px">
		<col width="50px">
		<col width="50px">
		<col width="120px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">적용년월</th>
			<td>
				<input id="txtIssYm" type="text" class="yymm" value="<?=$yymm;?>" <?=$yymm ? 'readonly' : '';?>>
			</td>
			<th class="center">발급일자</th>
			<td>
				<input id="txtIssDt" type="text" class="date" value="<?=$myF->dateStyle($R['iss_dt']);?>">
			</td>
			<th class="center">구분</th>
			<td>
				<label><input name="optCrGbn" type="radio" class="radio" value="C" checked>청구</label>
				<label><input name="optCrGbn" type="radio" class="radio" value="R" <?=$R['cr_gbn'] == 'R' ? 'checked' : '';?>>영수</label>
			</td>
			<td class="left last">
				<span class="btn_pack small"><button onclick="lfRegSave();">저장</button></span>
				<div id="ID_CELL_REG_ORGNO" style="display:none;"></div>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup>
		<col width="70px" span="8">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관명</th>
			<td><input id="txtOrgNm" type="text" style="width:100%;"></td>
			<th class="center">기관기호</th>
			<td><input id="txtOrgNo" type="text" style="width:100%;"></td>
			<th class="center">대표자</th>
			<td><input id="txtMgNm" type="text" style="width:100%;"></td>
			<th class="center">주소</th>
			<td><input id="txtAddr" type="text" style="width:100%;"></td>
			<td class="left last">
				<span class="btn_pack small"><button onclick="lfRegSearch();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table><?

$colgroup = '
	<col width="40px">
	<col width="150px">
	<col width="90px">
	<col width="70px">
	<col>';?>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">기관기호</th>
			<th class="head">대표자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top bottom last" colspan="5">
				<div id="ID_REG_LIST" style="overflow-x:hidden; overflow-y:scroll; height:217px;">
					<table class="my_table" style="width:100%; background-color:WHITE;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
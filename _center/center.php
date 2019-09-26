<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	if ($_POST['popYn'] != 'Y') include_once('../inc/_body_header.php');

	//메뉴
	$menuId = $_REQUEST['menuId'];
	$menuFile = $menuId;

	$menuPath = './center_'.$menuFile.'.php';

	if (!is_file($menuPath)){
		 include('../inc/_http_home.php');
		 exit;
	}

	$showCompany = false;
	$showY = false;
	$showM = false;
	$showFromDt = false;
	$showToDt = false;
	$showNew = false;
	$showExcel = false;
	$showOrgNm = false;
	$showSearch = false;

	switch($menuId){
		case 'CENTER_LIST';
			$title = '기관조회';
			break;

		case 'CENTER_REG':
			$title = '기관등록';
			break;

		case 'FEE_MAKE':
			$title = '청구요금생성';
			break;

		case 'FEE_LIST':
			$title = '요금내역';
			break;

		case 'BANKBOOK_COMPANY':
			$title = '본사계좌관리';
			//$showNew = true;
			break;

		case 'CLAIM_LIST':
			$title = '청구내역(월별)';
			$showCompany = true;
			$showY = true;
			$showExcel = true;
			break;

		case 'CLAIM_ORG':
			$title = '청구내역(기관별)';
			$showCompany = true;
			$showY = true;
			$showM = true;
			$showExcel = true;
			break;

		case 'CLAIM_ORG_DTL':
			$title = '청구내역(기관별 상세)';
			$showY = true;
			$showOrgNm = true;
			break;

		case 'ACCT_MONTH':
			$title = '월별 청구 내역';
			$showCompany = true;
			$showY = true;
			break;

		case 'ACCT_DEPOSIT':
			$title = '입금관리';
			$showCompany = true;
			$showY = true;
			$showM = true;
			break;

		case 'ACCT_CMS':
			$title = '입금등록(CMS,무통장)';
			break;

		case 'ACCT_LINK':
			$title = '입금적용';
			$showCompany = true;
			$showY = true;
			$showM = true;
			break;

		case 'CLAIM_ACCT':
			$title = '청구 및 입금내역(월별)';
			$showCompany = true;
			$showY = true;
			break;

		case 'CLAIM_ACCT_ORG':
			$title = '청구 및 입금내역(기관별)';
			$showCompany = true;
			$showY = true;
			$showM = true;
			break;

		case 'DEFAULT':
			$title = '미납기관내역(기관별)';
			$showCompany = true;
			$showY = true;
			$showM = true;
			break;

		case 'IN_LIST':
			$title = '입금내역조회';
			$showY = true;
			$showM = true;
			break;

		case 'POPUP_DEF':
			$title = '미납기관 팝업설정';
			$showY = true;
			$showM = true;
			break;

		case 'DOC';
			$title = '기관계약서/등록증 관리';
			$showNew = true;
			break;

		case 'TRANS':
			$title = '기관이전내역';
			break;

		case 'CONT':
			$title = '기관계약현황';
			break;

		case 'TAX':
			$title = '세금계산서발행이력';
			$showCompany = true;
			$showY = true;
			$showM = true;
			break;

		case 'STOPSET':
			$title = '중지팝업설정조회';
			break;

		case 'SVCCONT':
			$title = '서비스계약현황';
			break;

		case 'SMS':
			$title = 'SMS 이용현황';
			$showCompany = true;
			$showY = true;
			$showM = true;
			$showSearch = true;
			break;

		case 'NEWCON':
			$title = '신규연결기관';
			break;

		case 'CLSMG':
			$title = '마감관리';
			break;

		case 'FEE_EDIT':
			$title = '청구요금조정';
			break;

		case 'IN_OVER':
			$title = '과입금내역조회';
			break;

		case 'CLAIM_YYMM':
			$title = '청구년월설정';
			break;

		case 'PAY_IN_REG':
			$title = '입금등록';
			break;

		case 'PAY_IN_LIST':
			$title = '입금내역조회';
			break;

		case 'FEE_EXCEL':
			$title = '청구요금 엑셀등록';
			break;
	}?>

	<script type="text/javascript">
		$(document).ready(function(){
			$('input:text').each(function(){
				__init_object(this);
			});

			if ('<?=$_POST["popYn"];?>' == 'Y'){
				$('body').css('overflow','hidden');
			}else{
				if ($('#divBody').length > 0){
					$('#divBody').unbind('mouseover').bind('mouseover',function(){
						$('body').css('overflow','hidden');
					}).unbind('mouseout').bind('mouseout',function(){
						$('body').css('overflow','');
					});
				}
			}

			lfResize();
		});

		$(window).bind('resize', function(e){
			window.resizeEvt;
			$(window).resize(function(){
				clearTimeout(window.resizeEvt);
				window.resizeEvt = setTimeout(function(){
					lfResize();
				}, 250);
			});
		}).scroll(function(){
			if ('<?=$menuId;?>' != 'CONT'){
				lfResize();
			}
		});

		function lfResize(){
			return;
			if ('<?=$menuId;?>' == 'CONT' || '<?=$_POST["popYn"];?>' == 'Y'){
				if ($('div[id^="ID_BODY"]').length > 0){
					lfResize2();
				}else{
					lfResize1();
				}
			}else{
				lfResize2();
			}
		}

		function lfResize1(){
			var top = $('#divBody').offset().top;
			var height = document.body.offsetHeight;
			var menu = $('#left_box').height();
			var bottom = $('#copyright').height();

			if (!bottom) bottom = 0;
			if (menu > height + bottom){
				var h = height - top;
			}else{
				var h = height - top - bottom;
			}

			$('#divBody').height(h + 2);

			lfResizeSub();
		}

		function lfResize2(){
			try{
				var top = $('#divBody').offset().top;
				var height = document.body.offsetHeight;
				var menu = 0;
				var bottom = $('#copyright').height();
				var foot = $('#ID_FOOT').height();

				if ($('#left_box').length > 0) menu = $('#left_box').offset().top + $('#left_box').height();
				if (!foot) foot = 0;

				if (menu + bottom > height){
					var h = height - top - foot;

					if ($('body').scrollTop() > 0) h = menu - top - foot;
				}else{
					var h = height - top - bottom - foot;
				}

				$('#divBody').height(h);
			}catch(e){
			}

			$('div[id^="ID_BODY"]').each(function(){
				try{
					var top = $(this).offset().top;
					var height = document.body.offsetHeight;
					var menu = 0; //$('#left_box').offset().top + $('#left_box').height();
					var bottom = $('#copyright').height();
					var foot = $('#ID_FOOT').height();

					if ($('#left_box').length > 0) menu = $('#left_box').offset().top + $('#left_box').height();
					if (!foot) foot = 0;

					if (menu + bottom > height){
						var h = height - top - foot;

						if ($('body').scrollTop() > 0) h = menu - top - foot;
					}else{
						var h = height - top - bottom - foot;
					}

					$(this).height(h - 2);
				}catch(e){
				}
			});

			lfResizeSub();
		}

		function MoveYear(pos){
			var year = __str2num($('#yymm').attr('year'));

			year += pos;

			$('#yymm').attr('year',year).text(year);

			if ('<?=$menuId;?>' == 'IN_LIST') return;

			lfSearch();
		}

		function MoveMonth(month){
			$('#yymm').attr('month',month);
			$('div[id^="btnMonth_"]').each(function(){
				var mon = $(this).attr('id').replace('btnMonth_','');

				if (mon == month){
					$(this).removeClass('my_month_1').addClass('my_month_y');
				}else{
					$(this).removeClass('my_month_y').addClass('my_month_1');
				}
			});

			lfSearch();
		}

		function CheckCode(type,obj,cpyO){
			var cpy = cpyO;

			$.ajax({
				type:'POST'
			,	url:'./center_check_code.php'
			,	data:{
					'type':type
				,	'code':$(obj).val()
				}
			,	beforeSend:function(){
				}
			,	success:function(result){
					if (result != 'Y'){
						alert('입력하신 코드는 이미 사용중인 코드입니다.\n다른 코드를 입력하여 주십시오.');
						$(obj).val('').focus();
					}else{
						if (cpy){
							$(cpy).val($(obj).val());
							CheckCode('logId',$(cpy));
						}
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

		function GetValue(type,cd){
			$.ajax({
				type:'POST'
			,	url:'./center_get_value.php'
			,	data:{
					'type':type
				,	'code':cd
				}
			,	beforeSend:function(){
				}
			,	success:function(html){
					if (type == 'GROUP'){
						$('#cboGroup').html(html);
					}else if (type == 'BRANCH'){
						$('#cboBranch').html(html);
					}else if (type == 'PERSON'){
						$('#cboPerson').html(html);
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

		function lfFindOrg(){
			$.ajax({
				type:'POST'
			,	url:'./center_find.php'
			,	data:{
				}
			,	beforeSend:function(){
					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
				}
			,	success:function(html){
					$('#ID_LOCAL_POP_DATA').html(html);
					$('#ID_LOCAL_POP')
						.css('left','250px')
						.css('top','250px')
						.css('width','700px')
						.css('height','300px')
						.show();
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

		function lfFindOrgSet(obj){
			$('#ID_CELL_ORG').attr('orgNo', $(obj).attr('orgNo')).text($(obj).attr('orgNm')+'('+$(obj).attr('orgNo')+')');
			$('#ID_LOCAL_POP_DATA').html(html);
			$('#ID_LOCAL_POP').hide();

			lfSearch();
		}

		function lfSearch(){}
		function lfResizeSub(){}
		function lfExcel(){}
	</script>
	<div id="divTitle" class="title title_border" style="width:100%;">
		<div style=""><?=$title;?></div><?
		if ($showNew){?>
			<div style="float:right; width:auto; margin-top:9px;"><span class="btn_pack m"><span class="add"></span><button onclick="lfReg();">등록</button></span></div><?
		}?>
	</div><?

	if ($_REQUEST['year']){
		$year = $_REQUEST['year'];
	}else{
		$year = Date('Y');
	}

	if ($_REQUEST['month']){
		$month = $_REQUEST['month'];
	}else{
		$month	= Date('m');
	}

	$company = $_GET['company'];
	$orgNo = $_GET['orgNo'];

	if ($showY || $showM){
		if ($showY && $showM){
			if ($menuId == 'IN_LIST'){
				$title = '청구년월';
			}else{
				$title = '년월';
			}
		}else if ($showY){
			$title = '년도';
		}else{
			$title = '&nbsp;';
		}?>
		<table class="my_table" style="width:100%;">
			<colgroup><?
				if ($menuId == 'IN_LIST'){?>
					<col width="60px"><?
				}else{?>
					<col width="40px"><?
				}
				if ($showCompany){?>
					<col width="50px"><?
				}
				if ($showY){?>
					<col width="83px"><?
				}
				if ($showM){?>
					<col width="500px"><?
				}?>
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="center"><?=$title;?></th><?
					if ($showCompany){?>
						<td>
							<select id="cboCompany" style="width:auto;" onchange="lfSearch();">
								<option value="">-회사선택-</option><?
								$sql = 'SELECT	b00_code AS cd
										,		b00_name AS nm
										,		b00_manager AS manager
										,		b00_domain AS domain
										FROM	b00branch
										WHERE	b00_com_yn = \'Y\'
										ORDER	BY nm';

								$conn->query($sql);
								$conn->fetch();

								$rowCnt = $conn->row_count();

								for($i=0; $i<$rowCnt; $i++){
									$row = $conn->select_row($i);?>
									<option value="<?=$row['domain'];?>" <?=$row['domain'] == ($company ? $company : 'carevisit.net') ? 'selected' : '';?>><?=$row['nm'];?></option><?
								}

								$conn->row_free();?>
							</select>
						</td><?
					}
					if ($showY){?>
						<td class="<?=(!$showM && !$showOrgNm ? 'last' : '');?>">
							<div>
								<div style="float:left; width:auto; margin-left:5px; margin-top:3px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="MoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
								<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="yymm" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
								<div style="float:left; width:auto; margin-top:3px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="MoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
							</div>
						</td><?
					}

					if ($showM){?>
						<td class="last">
							<div style="margin-left:5px; margin-top:1px;"><?=$myF->_btn_month($month,'MoveMonth(');?></div>
						</td><?
						if($menuId == 'SMS'){ ?>
							<td class="right last">
								<span class="btn_pack m icon"><span class="excel"></span><button id="btnDealPrt" type="button" onclick="lfExcel();">출력</button></span>
							</td>
						<?
						}
					}

					if ($showOrgNm){
						if ($orgNo){
							$sql = 'SELECT	DISTINCT m00_store_nm
									FROM	m00center
									WHERE	m00_mcode = \''.$orgNo.'\'';
							$orgNm	= $conn->get_data($sql);
						}?>
						<td class="last">
							<div id="ID_CELL_ORG" class="left" style="background-color:#FFFFD2;" orgNo="<?=$orgNo;?>" onclick="lfFindOrg();"><?
								if ($orgNm){
									echo $orgNm.'('.$orgNo.')';
								}else{?>
									<span style="color:#CC723D;">-기관을 선택하여 주십시오.</span><?
								}?>
							</div>
						</td><?
					}else{
						if ($showExcel){?>
							<td class="last">
								<div class="right">
									<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">Excel</button></span>
								</div>
							</td><?
						}else{?>
							<td class="last">&nbsp;</td><?
						}
					}?>
				</tr>
			</tbody>
		</table><?
	}

	if ($showSearch){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="70px">
				<col width="100px">
				<col width="70px">
				<col width="150px">
				<col width="70px">
				<col width="70px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="center">기관기호</th>
					<td><input id="txtOrgNo" type="text" style="width:100%;"></td>
					<th class="center">기관명</th>
					<td><input id="txtOrgNm" type="text" style="width:100%;"></td>
					<th class="center">대표자명</th>
					<td><input id="txtOrgMg" type="text" style="width:100%;"></td>
					<td class="last">&nbsp;<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span></td>
				</tr>
			</tbody>
		</table><?
	}?>

	<!--div id="divBody" style="overflow-x:hidden; overflow-y:auto; width:100%; height:100px;"--><?
		include_once($menuPath);?>
	<!--/div-->
	<div id="ID_POP_DTL" style="position:absolute; left:200px; top:200px; width:800px; height:600px; border:3px solid #4374D9; background-color:WHITE; display:none;"></div>
	<div id="ID_LOCAL_POP" style="position:absolute; left:0; top:0; width:0; height:0; display:none; z-index:11; background:url('../image/tmp_bg.png'); border:2px solid #4374D9;">
		<div style="position:absolute; text-align:right; width:100%; top:-20px; left:-5px;">
			<a href="#" onclick="$('#ID_LOCAL_POP').hide();"><img src="../image/btn_exit.png"></a>
		</div>
		<div id="ID_LOCAL_POP_DATA" style="position:absolute; width:100%;"></div>
	</div><?

	if ($_POST['popYn'] != 'Y') include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
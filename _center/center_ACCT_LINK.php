<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfResizeSub();
	});

	function lfResizeSub(){
		//var h1 = document.body.offsetHeight - $('#ID_ACCT_LIST').offset().top - $('#copyright').height() - 1;
		//var h2 = Math.floor(h1 * 0.3);
		//var h3 = h1 - h2 - $('#ID_ACCT_TAB').height();


		var top = $('#ID_ACCT_LIST').offset().top;
		var height = document.body.offsetHeight;
		var menu = $('#left_box').offset().top + $('#left_box').height();
		var bottom = $('#copyright').height();
		var foot = 0; //$('#ID_FOOT').height();

		if (!foot) foot = 0;

		if (menu + bottom > height){
			var h = height - top - foot;

			if ($('body').scrollTop() > 0) h = menu - top - foot;
		}else{
			var h = height - top - bottom - foot;
		}

		var h1 = h - 2;
		//var h2 = Math.floor(h1 * 0.4);
		//var h3 = h1 - h2 - $('#ID_ACCT_TAB').height();
		var h3 = Math.floor((h1 - $('#ID_ACCT_TAB').height()) * 0.5);

		$('#ID_ACCT_LIST').height(h1);
		$('#ID_ACCT_INFO').height(h3);
		$('#ID_ACCT_IN').height(h3);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_acct_org_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_ACCT_LIST').html(html);
				$('div[id^="ID_CENTER_"]',$('#ID_ACCT_LIST')).unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#FFFFFF');
				});

				var obj = $('div[id^="ID_CENTER_"]:first',$('#ID_ACCT_LIST'));

				lfSetOrg(obj);

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

	function lfSetOrg(obj){
		if (!obj){
			$('#ID_ACCT_INFO').html('');
			lfAcctInfo();
			return;
		}

		var orgNo = $('#ID_CELL_NO',obj).text();
		var CMS = $('#ID_CELL_CMS',obj).text();

		$('div[id^="ID_CENTER_"]',$('#ID_ACCT_LIST')).css('background-color','#FFFFFF').attr('selYn','N');
		$(obj).css('background-color','#FAF4C0').attr('selYn','Y');

		$.ajax({
			type:'POST'
		,	url:'./center_acct_org_info.php'
		,	data:{
				'orgNo'	:orgNo
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_ACCT_INFO').html(html);

				var o = __GetTagObject($('#ID_ACCT_SVC_LIST',$('#ID_ACCT_INFO')), 'DIV');
				var h = $('#ID_ACCT_INFO').height() - $('#ID_ACCT_SVC_LIST_CAPTION',$('#ID_ACCT_INFO')).height() - $('#ID_ACCT_SVC_LIST_SUM',$('#ID_ACCT_INFO')).height();

				$(o).height(h);

				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});

		//청구 및 입금정보
		lfAcctInfo(obj);

		//미연결 및 입금내역
		lfAcctNonLinkCMSList();
	}

	function lfAcctInfo(obj){
		//청구금액
		$('#ID_CELL_ACCTAMT').text('0');

		//CMS미연결금액
		$('#ID_CELL_NONLINKAMT').text('0');

		//CMS연결금액
		$('#ID_CELL_LINKAMT').text('0');

		//입금등록금액
		$('#ID_CELL_BANKAMT').text('0');

		//미납금액
		$('#ID_CELL_NONPAY').text('0');

		if (!obj){
			lfAcctNonLinkCMSList();
			return;
		}

		var orgNo = $('#ID_CELL_NO',obj).text();
		var CMS = $('#ID_CELL_CMS',obj).text();

		$.ajax({
			type:'POST'
		,	url:'./center_acct_in_info.php'
		,	data:{
				'orgNo'	:orgNo
			,	'CMS'	:CMS
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var val = __parseVal(data);

				//청구금액
				$('#ID_CELL_ACCTAMT').text(__num2str(val['acctAmt']));

				//CMS미연결금액
				$('#ID_CELL_NONLINKAMT').text(__num2str(val['nooLinkAmt']));

				//CMS연결금액
				$('#ID_CELL_LINKAMT').text(__num2str(val['linkAmt']));

				//입금등록금액
				$('#ID_CELL_BANKAMT').text(__num2str(val['bankAmt']));

				//미납금액
				$('#ID_CELL_NONPAY').text(__num2str(val['nonpay']));
				$('#ID_CELL_NONAMT', $('div[id^="ID_CENTER_"][selYn="Y"]')).text(__num2str(val['nonpay']));
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	//CMS 미연결내역
	function lfAcctNonLinkCMSList(){
		var obj = $('div[id^="ID_CENTER_"][selYn="Y"]');
		var orgNo = $('#ID_CELL_NO',obj).text();
		var CMS = $('#ID_CELL_CMS',obj).text();

		$.ajax({
			type:'POST'
		,	url:'./center_acct_cms_notlink.php'
		,	data:{
				'orgNo'	:orgNo
			,	'CMS'	:CMS
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_ACCT_IN').html(html);
				$('tr',$('#ID_ACCT_IN')).unbind('mouseover').bind('mouseover',function(){
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					$(this).css('background-color','#FFFFFF');
				});

				var o = __GetTagObject($('#ID_CMS_LIST',$('#ID_ACCT_IN')), 'DIV');
				var h = $('#ID_ACCT_IN').height() - $('#ID_CMS_LIST_CAPTION',$('#ID_ACCT_IN')).height() - $('#ID_CMS_LIST_SUM',$('#ID_ACCT_IN')).height();

				$(o).height(h);

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

	//CMS 연결내역
	function lfAcctLinkCMSList(){
		var obj = $('div[id^="ID_CENTER_"][selYn="Y"]');
		var orgNo = $('#ID_CELL_NO',obj).text();
		var CMS = $('#ID_CELL_CMS',obj).text();

		$.ajax({
			type:'POST'
		,	url:'./center_acct_cms_link_list.php'
		,	data:{
				'orgNo'	:orgNo
			,	'CMS'	:CMS
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_ACCT_IN').html(html);
				$('tr',$('#ID_ACCT_IN')).unbind('mouseover').bind('mouseover',function(){
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					$(this).css('background-color','#FFFFFF');
				});

				var o = __GetTagObject($('#ID_CMS_LIST',$('#ID_ACCT_IN')), 'DIV');
				var h = $('#ID_ACCT_IN').height() - $('#ID_CMS_LIST_CAPTION',$('#ID_ACCT_IN')).height() - $('#ID_CMS_LIST_SUM',$('#ID_ACCT_IN')).height();

				$(o).height(h);

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

	//입금등록
	function lfAcctIn(){
		var obj = $('div[id^="ID_CENTER_"][selYn="Y"]');
		var orgNo = $('#ID_CELL_NO',obj).text();
		var CMS = $('#ID_CELL_CMS',obj).text();

		$.ajax({
			type:'POST'
		,	url:'./center_acct_in_reg.php'
		,	data:{
				'orgNo'	:orgNo
			,	'CMS'	:CMS
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_ACCT_IN').html(html);
				$('input',$('#ID_ACCT_IN')).each(function(){
					__init_object(this);
				});

				$('tr',$('#ID_ACCT_IN')).unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('class')){
						$('.'+$(this).attr('class'),$(this).parent()).css('background-color','#EAEAEA');
					}else{
						$(this).css('background-color','#EAEAEA');
					}
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('class')){
						$('.'+$(this).attr('class'),$(this).parent()).css('background-color','#FFFFFF');
					}else{
						$(this).css('background-color','#FFFFFF');
					}
				});

				var o = __GetTagObject($('#ID_CMS_LIST',$('#ID_ACCT_IN')), 'DIV');
				var h = $('#ID_ACCT_IN').height() - $('#ID_CMS_LIST_CAPTION',$('#ID_ACCT_IN')).height() - $('#ID_CMS_LIST_SUM',$('#ID_ACCT_IN')).height();

				$(o).height(h);

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

	//CMS 연결
	function lfAcctCMSLink(obj){
		var obj		= __GetTagObject($(obj),'TR');
		var date	= $(obj).attr('dt'); //CMS 등록일자
		var seq		= $(obj).attr('seq'); //등록순번
		var amt		= $(obj).attr('amt'); //연결 가능금액
		var CMS		= $(obj).attr('no'); //

		var obj		= $('div[id^="ID_CENTER_"][selYn="Y"]');
		var orgNo	= $('#ID_CELL_NO',obj).text();
		//var CMS		= $('#ID_CELL_CMS',obj).text();

		$.ajax({
			type:'POST'
		,	url:'./center_acct_cms_link_set.php'
		,	data:{
				'orgNo'	:orgNo
			,	'CMS'	:CMS
			,	'date'	:date
			,	'seq'	:seq
			,	'amt'	:amt
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					//청구 및 입금정보
					lfAcctInfo(obj);

					//미연결 및 입금내역
					lfAcctNonLinkCMSList();
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

	//은행입금연결
	function lfAcctBankLink(obj){
		var obj		= __GetTagObject($(obj),'TR');
		var yymm	= $(obj).attr('yymm'); //
		var seq		= $(obj).attr('seq'); //등록순번

		var obj		= $('div[id^="ID_CENTER_"][selYn="Y"]');
		var orgNo	= $('#ID_CELL_NO',obj).text();

		$.ajax({
			type:'POST'
		,	url:'./center_acct_bank_link_set.php'
		,	data:{
				'orgNo'	:orgNo
			,	'seq'	:seq
			,	'yymm'	:yymm
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					//청구 및 입금정보
					lfAcctInfo(obj);

					//미연결 및 입금내역
					lfAcctNonLinkCMSList();
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

	//연결해제
	function lfAcctCMSUnlink(obj){
		var obj		= __GetTagObject($(obj),'TR');
		var CMSNo	= $(obj).attr('CMSNo'); //
		var CMSDate	= $(obj).attr('CMSDate'); //CMS 등록일자
		var CMSSeq	= $(obj).attr('CMSSeq'); //등록순번
		var seq		= $(obj).attr('seq'); //연결순번
		var prepaySeq = $(obj).attr('prepaySeq'); //

		var obj		= $('div[id^="ID_CENTER_"][selYn="Y"]');
		var orgNo	= $('#ID_CELL_NO',obj).text();
		//var CMS		= $('#ID_CELL_CMS',obj).text();

		$.ajax({
			type:'POST'
		,	url:'./center_acct_cms_link_unset.php'
		,	data:{
				'orgNo'	:orgNo
			,	'seq'	:seq
			,	'CMSNo'	:CMSNo
			,	'CMSDate':CMSDate
			,	'CMSSeq':CMSSeq
			,	'prepaySeq':prepaySeq
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					//청구 및 입금정보
					lfAcctInfo(obj);

					//미연결 및 입금내역
					lfAcctNonLinkCMSList();
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

	/*
	//입금등록
	function lfAcctInReg(obj){
		var obj		= __GetTagObject($(obj),'TR');
		var date	= $('#txtDate',$('#ID_ACCT_IN')).val();
		var bankNm	= $('#txtBankNm',$('#ID_ACCT_IN')).val();
		var acctNm	= $('#txtAcctNm',$('#ID_ACCT_IN')).val();
		var inAmt	= $('#txtAmt',$('#ID_ACCT_IN')).val();
		var stat	= $('#cboStat',$('#ID_ACCT_IN')).val();

		if (__str2num(inAmt) == 0){
			alert('입금금액을 입력하여 주십시오.');
			$('#txtAmt',$('#ID_ACCT_IN')).focus();
			return;
		}

		var obj		= $('div[id^="ID_CENTER_"][selYn="Y"]');
		var orgNo	= $('#ID_CELL_NO',obj).text();
		var CMS		= $('#ID_CELL_CMS',obj).text();

		$.ajax({
			type:'POST'
		,	url:'./center_acct_in_set.php'
		,	data:{
				'orgNo'	:orgNo
			,	'CMS'	:CMS
			,	'date'	:date
			,	'bankNm':bankNm
			,	'acctNm':acctNm
			,	'inAmt'	:inAmt
			,	'stat'	:stat
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					//청구 및 입금정보
					lfAcctInfo(obj);

					//입금등록내역
					lfAcctIn();
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
	*/

	function lfLinkAll(){
		if ($('div[id^="ID_CENTER_"]').length < 1){
			alert('연결할 기관리스트가 없습니다.');
			return;
		}

		$('div[id^="ID_CENTER_"]').each(function(){
			var orgNo = $('#ID_CELL_NO',this).text();

			$.ajax({
				type:'POST'
			,	async:false
			,	url:'./center_acct_link_all.php'
			,	data:{
					'orgNo'	:orgNo
				,	'year'	:$('#yymm').attr('year')
				,	'month'	:$('#yymm').attr('month')
				}
			,	beforeSend:function(){
					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
				}
			,	success:function(result){
					$('#tempLodingBar').remove();
				}
			,	error: function (request, status, error){
					$('#tempLodingBar').remove();
					alert('[ERROR No.02]'
						 +'\nCODE : ' + request.status
						 +'\nSTAT : ' + status
						 +'\nMESSAGE : ' + request.responseText);
				}
			});
		});

		lfSearch();
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="220px">
		<col width="1px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">
				<div style="float:right; width:auto; margin-right:5px;"><span class="btn_pack small"><button onclick="lfLinkAll();">전체적용</button></span></div>
				<div style="float:center; width:auto; margin-left:5px;">기관리스트</div>
			</th>
			<th class="head"><img style="width:1px; height:1px;"></th>
			<th class="head last">서비스 청구내역</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="bottom"><div id="ID_ACCT_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;"></div></td>
			<td class="bottom"></td>
			<td class="top bottom last">
				<div id="ID_ACCT_INFO" style="width:100%; height:100px; margin-bottom:1px;"></div>
				<div id="ID_ACCT_TAB" class="left" style=" border-top:1px solid #CCCCCC;">
					<div style="border-bottom:1px dashed #CCCCCC;">
						청구금액 : <span id="ID_CELL_ACCTAMT" class="bold" style="color:BLACK;">0</span> /
						미적용 : <span id="ID_CELL_NONLINKAMT" class="bold" style="color:BLUE;">0</span> /
						CMS적용 : <span id="ID_CELL_LINKAMT" class="bold" style="color:#4374D9;">0</span> /
						입금등록 : <span id="ID_CELL_BANKAMT" class="bold" style="color:#4641D9;">0</span> /
						미납금액 : <span id="ID_CELL_NONPAY" class="bold" style="color:RED;">0</span>
					</div>
					<div>
						<a href="#" onclick="lfAcctNonLinkCMSList();">미적용내역</a> |
						<a href="#" onclick="lfAcctLinkCMSList();">적용내역</a> |
						<a href="#" onclick="lfAcctIn();">입급등록</a>
					</div>
				</div>
				<div id="ID_ACCT_IN" style="width:100%; height:100px; border-top:1px solid #CCCCCC;"></div>
			</td>
		</tr>
	</tbody>
</table>
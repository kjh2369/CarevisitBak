<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year  = Date('Y');
	$month = IntVal(Date('m'));

	if ($type == '3'){
		$lsTitle = 'SMS 입금관리';
	}else if ($type == '13'){
		$lsTitle = '스마트폰 입금관리';
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		var html = '<table id="tbl" class="my_table" style="width:100%;">'
				 + '<colgroup><col width="50px"><col></colgroup>'
				 + '<tbody>'
				 + '<tr>'
				 + '<td class="center bottom"><div class="center">합계</div></td>'
				 + '<td class="left bottom">'
					+ '<div style="float:left; width:auto;">기본 : </div><div id="lblCenter" style="float:left; width:auto;">0</div>'
					+ '<div style="float:left; width:auto;">/ 교육 : </div><div id="lblEdu" style="float:left; width:auto;">0</div>'
					+ '<div style="float:left; width:auto;">/ SMS : </div><div id="lblSMS" style="float:left; width:auto;">0</div>'
					+ '<div style="float:left; width:auto;">/ 스마트폰 : </div><div id="lblSmart" style="float:left; width:auto;">0</div>'
					+ '<div style="float:left; width:auto;">/ 총금액 : </div><div id="lblTot" style="float:left; width:auto;">0</div>'
					+ '<div style="float:left; width:auto;">/ 입금액 : </div><div id="lblDeposit" style="float:left; width:auto; color:blue;">0</div>'
					+ '<div style="float:left; width:auto;">/ 미납총액 : </div><div id="lblUnpaid" style="float:left; width:auto; color:red;">0</div>'
				 + '</td>'
				 + '</tr>'
				 + '</tbody>'
				 + '</table>';

		//gQuickTop = $('#tbl').offset().top + 25;
		//gQuickTop = $(document).height() - 30;

		gBodyHeight = $(document).height();

		if ($(document).height() > $('#tbl').height()){
			gQuickTop = $('#tbl').offset().top + 25;
		}else{
			gQuickTop = $(document).height() - 30
		}

		$('#quickMenu').html(html);
		$('#quickMenu')
			.css('top',gQuickTop)
			.css('left',$('#tbl').offset().left)
			.css('width',$('#tbl').width())
			.css('height','30px');
		$('#quickMenu').show();
		$('#quickMenu').animate( { "top": $(document).scrollTop() + gQuickTop +"px" }, 500);
		$(window).scroll(function(){
			var liH = gQuickTop;

			if ($('#tbl').offset().top + $('#tbl').height() < $(document).scrollTop() + gBodyHeight - 30){
				liH = ($('#tbl').offset().top + $('#tbl').height()) - $(document).scrollTop() - 27;
			}else{
				liH = gBodyHeight - 30
			}

			$('#quickMenu').stop();
			$('#quickMenu').animate( { "top": $(document).scrollTop() + liH + "px" }, 1000);
		});

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode':'<?=$type;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var list = data.split(String.fromCharCode(1));
				var html = '';
				var liTot = 0, liCenter = 0, liSMS = 0, liSmart = 0, liUnpaid = 0, liDeposit = 0, liEdu = 0;

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						html += '<tr id="rowId_'+i+'" onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">'
							 +  '<td class="center">'+(i+1)+'</td>'
							 +  '<td class="center"><div class="left">'+val[0]+'</div></td>'
							 +  '<td class="center"><div class="left nowrap" style="width:125px;">'+val[1]+'</div></td>'
							 +  '<td class="center"><div class="right">'+__num2str(val[3])+'</div></td>'
							 +  '<td class="center"><div class="right">'+__num2str(val[8])+'</div></td>'
							 +  '<td class="center"><div class="right">'+__num2str(val[4])+'</div></td>'
							 +  '<td class="center"><div class="right">'+__num2str(val[5])+'</div></td>'
							 +  '<td class="center"><div class="right">'+__num2str(val[2])+'</div></td>'
							 +  '<td class="center"><div class="right" style="color:blue;">'+__num2str(val[7])+'</div></td>'
							 +  '<td class="center"><div class="right" style="color:red;">'+__num2str(val[6])+'</div></td>'
							 +  '<td class="center last">'
							 +  '<div class="left nowrap"><span class="btn_pack m"><button type="button" onclick="lfDeposit($(this).parent().parent().parent().parent());">입금등록</button></span></div>'
							 +  '</td>'
							 +  '</tr>';

						liTot     += __str2num(val[2]);
						liCenter  += __str2num(val[3]);
						liEdu     += __str2num(val[8]);
						liSMS     += __str2num(val[4]);
						liSmart   += __str2num(val[5]);
						liUnpaid  += __str2num(val[6]);
						liDeposit += __str2num(val[7]);
					}
				}

				if (!html){
					html = '<tr><td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('div[id="lblTot"]').text(__num2str(liTot));
				$('div[id="lblCenter"]').text(__num2str(liCenter));
				$('div[id="lblEdu"]').text(__num2str(liEdu));
				$('div[id="lblSMS"]').text(__num2str(liSMS));
				$('div[id="lblSmart"]').text(__num2str(liSmart));
				$('div[id="lblUnpaid"]').text(__num2str(liUnpaid));
				$('div[id="lblDeposit"]').text(__num2str(liDeposit));

				$('#list').html(html);
				$('#tempLodingBar').remove();

				if ($('#tbl').offset().top + $('#tbl').height() < $(document).scrollTop() + gBodyHeight - 30){
					var liH = ($('#tbl').offset().top + $('#tbl').height()) - $(document).scrollTop() - 27;
				}else{
					var liH = gBodyHeight - 30
				}

				$('#quickMenu').stop();
				$('#quickMenu').animate( { "top": $(document).scrollTop() + liH + "px" }, 1000);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDeposit(obj){
		var objModal = new Object();
		var url      = './deposit_reg.php';
		var style    = 'dialogWidth:600px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.mode = '<?=$type;?>';
		objModal.code = $('td',$(obj)).eq(1).text();
		objModal.name = $('td',obj).eq(2).text();
		objModal.charge = __str2num($('td',obj).eq(7).text());
		objModal.unpaid = __str2num($('td',obj).eq(9).text());
		objModal.win  = window;

		window.showModalDialog(url, objModal, style);

		if (!objModal.code) return;
	}
</script>

<div class="title title_border">입금관리</div>

<table id="tbl" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="130px">
		<col width="67px" span="7">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관코드</th>
			<th class="head">기관명</th>
			<th class="head">기본</th>
			<th class="head">교육비</th>
			<th class="head">SMS</th>
			<th class="head">스마트폰</th>
			<th class="head">총금액</th>
			<th class="head">입금액</th>
			<th class="head">현미납금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="list"></tbody>
	<tbody>
		<tr>
			<th class="center bottom bold" colspan="10"></th>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>
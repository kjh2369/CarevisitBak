<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$IsWrk = $_GET['IsWrk'];
?>
<style>
	body{
		/*overflow:hidden;*/
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		lfResize();
		lfSearch();
	});

	$(window).bind('resize', function(e){
		window.resizeEvt;
		$(window).resize(function(){
			clearTimeout(window.resizeEvt);
			window.resizeEvt = setTimeout(function(){
				lfResize();
			}, 250);
		});
	});

	function lfResize(){
		return;
		var obj = __GetTagObject($('#tbodyList'),'DIV');
		var top = $(obj).offset().top;
		var height = document.body.offsetHeight;
		var h = height - top;

		$(obj).height(h);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_client.php'
		,	data:{
				'year':$('#lblYYMM').attr('year')
			,	'IsWrk':'<?=$IsWrk;?>'
			,   'findName':$('#findName').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		});
	}

	function lfWokrLogReg(obj, jumin, month){
		var width = 1024;
		var height = 768;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './sw_work_log_reg.php';
		var win = window.open('about:blank', 'WORK_LOG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'jumin':jumin
			,	'year':$('#lblYYMM').attr('year')
			,	'month':month
			,	'IsWrk':'<?=$IsWrk;?>'
			,	'objId':$(obj).attr('id')
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

		form.setAttribute('target', 'WORK_LOG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfEmpPrint(){
		//if ('<?=$debug;?>' == '1'){
			/*
			var left = (screen.availWidth - (width = 300)) / 2, top = (screen.availHeight - (height = 100)) / 2;
			var win = window.open('../showWeb?path=../iljung/sw_work_log_print.php&data='+__parseSet('type/=/blank&jumin/=/<?=$ed->en64("1");?>||<?=$ed->en64("2");?>||<?=$ed->en64("3");?>'), 'WEB_PRINT', 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no');
			*/
			
			var year = $('#lblYYMM').attr('year')
			
			var width = 380;
			var height = 450;
			var left = (screen.availWidth - width) / 2;
			var top = (screen.availHeight - height) / 2;

			window.open("../iljung/sw_emp_pop.php?year="+year,"EMP_POP","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
		/*
		}else{
			var para = 'root=iljung'
					 + '&dir=N'
					 + '&fileName=sw_work_log'
					 + '&fileType=pdf'
					 + '&target=show.php'
					 + '&showForm=SW_WORK_LOG'
					 + '&param=';

			__printPDF(para);
		}
		*/
	}

</script>
<div class="title title_border">사회복지사 업무일지</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col width="75px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="last">
				<div class="left" style="padding-top:2px; float:left;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); setTimeout('lfSearch()',200);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); setTimeout('lfSearch()',200);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
					
				</div>
			</td>
			<th class="head">수급자</th>
			<td>
				<div style="float:left; width:auto; padding-top:2px;">
					<input id='findName' name='findName' type='text' value='' onkeydown="if(event.keyCode==13){lfSearch();}">
					<span class='btn_pack m'><button type='button' onclick="setTimeout('lfSearch()',200);">조회</button></span>
				</div>
				<div style="width:auto; float:right;"><span class="btn_pack m"><button type="button" onclick="lfEmpPrint();">빈양식출력</button></span></div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;"><?
	$colgroup = '
		<col width="40px">
		<col width="90px">
		<col width="75px">
		<col width="50px">
		<col width="45px" span="12">
		<col>';

	?>
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자</th>
			<th class="head">생년월일</th>
			<th class="head">등급</th><?
			for($i=1; $i<=12; $i++){?>
				<th class="head "><?=$i;?>월</th><?
			}?>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
<table>
<table class="my_table" style="width:100%;">
	<tbody>
		<tr>
			<td class="top last" colspan="17">
				<div style="width:100%; height:530px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<!--table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table-->
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$yymm  = date('Y-m', mktime());

	if (empty($year))  $year  = date('Y', mktime());
	if (empty($month)) $month = date('m', mktime());
	$init_year = $myF->year();

?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();

		$('input[init="Y"]').each(function(){
			__init_object(this);
		});

	});
	
	function set_month(month){
		
		if (month > 0){
			$('#year').val((parseInt(month, 10) < 10 ? '0' : '')+parseInt(month, 10));
		}
		$('#month').val((parseInt(month, 10) < 10 ? '0' : '')+parseInt(month, 10));

		lfSearch();
	}
	
	function lfSearch(){

		$.ajax({
			type:'POST'
		,	url:'./sw_work_search_sub.php'
		,	data:{
			'year':$('#year').val()
		,	'month':$('#month').val()
		,	'memCd'	:$('#divSW').attr('jumin')
		,	'orderByGbn':$('input:radio[name="orderByGbn"]:checked').val()
		}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				for(var i=1; i<=12; i++){
					var mon = (i < 10 ? '0' : '')+i;
					var obj = document.getElementById('m_'+mon);

					if (mon == $('#month').val()){
						obj.className = 'my_month my_month_y';
					}else{
						obj.className = 'my_month my_month_1';
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		});
	}
	
	function lfFindWorker(){
		var width = 500;
		var height = 500;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './sw_mem_list.php';
		var win = window.open('about:blank', 'SW_MEM', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'year':$('#year').val()
			/* ,	'month':$('#month').val() */
			,	'result':'lfFindWorkerResult'
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

		form.setAttribute('target', 'SW_MEM');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfFindWorkerResult(jumin,name,from,to){
		$('#divSW').attr('jumin',jumin).attr('from',from).attr('to',to).text(name);
		lfSearch();
	}

</script>
<input id="month" name="month" type="hidden" value="<?=$month;?>">
<div class="title title_border">사회복지사 실적조회</div>
<table id="my_table" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col width="445px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td class="last">
				<select id="year" name="year" style="width:auto;"><?
				for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
					<option value="<?=$i;?>" <? if($i == $year){echo 'selected';} ?>><?=$i;?></option><?
				}?>
				</select>년
			</td>
			<td class="last" style="padding-top:1px;">
			<?
				for($i=1; $i<=12; $i++){
					$class = 'my_month ';

					if ($i == intval($month)){
						$class .= 'my_month_y ';
						$color  = 'color:#000000;';
					}else{
						$class .= 'my_month_1 ';
						$color  = 'color:#000000;';
					}

					$link = '<a href="#" style="'.$color.'" onclick="set_month('.$i.', 1);">'.$i.'월</a>';

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:2px;';
					}?>
					<div id="m_<?=($i < 10 ? '0' : '').$i;?>" class="<?=$class;?>" style="<?=$style;?>"><?=$link;?></div><?
				}
			?>
			</td>
			<td>
				<div id="divNo" seq="" class="left" style="float:left; width:auto; line-height:25px; margin-left:50px; margin-right:5px; font-weight:bold;">사회복지사 조회 : </div>
				<div style="float:left; width:auto; height:25px; margin-left:2px; margin-top:1px;"><span class="btn_pack find" onclick="lfFindWorker();"></span></div>
				<div id="divSW" jumin="" from="" to="" style="float:left; width:auto; line-height:25px;"></div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%; ">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="80px">
		<col width="120px">
		<col width="120px">
		<col width="60px">
		<col width="*">
	</colgroup>
	<thead>
		<tr>
			<th class="head">일자</th>
			<th class="head">대상자</th>
			<th class="head">요양사</th>
			<th class="head">요양사시간</th>
			<th class="head">복지사시간</th>
			<th class="head">차이</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
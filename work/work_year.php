<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$orgNo = $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfMoveYear(pos){
		var year = __str2num($('#yymm').attr('year'));

		year += pos;

		$('#yymm').attr('year',year).text(year);

		lfSearch();
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./work_year_search.php'
		,	data:{
				'year':$('#yymm').attr('year')
			}
		,	beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function (html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function (){
			}
		}).responseXML;
	}

	function lfWorkSearch(gbn,jumin){
		var width = 800;
		var height = 600; //screen.availHeight;
		var left = window.screenLeft;
		var top = window.screenTop;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=yes,status=no,resizable=yes';
		var url = './work_year_list.php';
		var win = window.open('about:blank', 'WORK_YEAR_LIST', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'jumin':jumin
			,	'gbn':gbn
			,	'year':$('#yymm').attr('year')
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

		form.setAttribute('target', 'WORK_YEAR_LIST');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">근무현황표(연간)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last"><? echo $myF->yymm();?></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">직원명</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code  = $_SESSION['userCenterCode'];
	$year  = date('Y');
	$month = date('m');

	
	$sql = 'SELECT team.jumin, team.svc_cd, team_cd, from_ym, to_ym, m02_yname as yname, m03_name as name
			,      concat(m03_juso1,\' \', m03_juso2) as addr
			/*,      case lvl.svc_cd when \'0\' then case lvl.level when \'9\' then \'일반\' else concat(lvl.level,\'등급\') end when \'4\' then concat(lvl.level,\'등급\') else \'\' end as ylvl*/
			FROM   client_his_team as team
			INNER  JOIN m02yoyangsa
			ON	   m02_ccode = org_no
			AND    m02_yjumin = team_cd
			AND    m02_mkind  = \'0\'
			LEFT   JOIN m03sugupja
			ON	   m03_ccode = org_no
			AND    m03_jumin = jumin
			AND    m03_mkind  = \'0\'
			/*
			LEFT   JOIN (
						  select jumin
						  ,      svc_cd
						  ,      level
							from client_his_lvl
						   where org_no = \''.$code.'\'
						   and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
						   and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')) as lvl
			ON	   lvl.svc_cd = m03_mkind
			AND	   lvl.jumin = m03_jumin
			*/
			WHERE  org_no = \''.$code.'\'
			ORDER  BY m02_yname';
	
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

?>
<script type="text/javascript">
$(document).ready(function(){
	lfSearch();
});
/*********************************************************

	팀장명 조회

*********************************************************/
function findTeam(val){
	if(!val) val = '';
	
	var result = __findTeam('<?=$code;?>');
	
	if (!result) return;

	$('#strTeam'+val).val(result['name']);
	$('#strTeamCd'+val).val(result['jumin']);
	
	if(!val){
		lfSearch();
	}
}


function lfSearch(){
	$.ajax({
		type:'POST'
	,	url:'../sugupja/team_set_search.php'
	,	data:{
			'teamCd':$('#strTeamCd').val()
		,	'cltNm':$('#cltNm').val()
		,	'kind':$('#kind').val()
		}
	,	beforeSend:function(){
			$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
		}
	,	success:function(html){
			$('#tbodyList').html(html);
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

function lfSave(val){
	
	$.ajax({
		type:'POST'
	,	url:'../sugupja/team_set_save.php'
	,	data:{
			'teamCd':$('#strTeamCd'+val).val()
		,	'svcCd':$('#svcCd'+val).val()
		,	'seq':$('#seq'+val).val()
		,	'cltCd':$('#cltCd'+val).val()
		}
	,	beforeSend:function(){
			//$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
		}
	,	success:function(html){
			alert('정상적으로 처리되었습니다');
			//$('#tempLodingBar').remove();
		}
	,	error: function (request, status, error){
			alert('[ERROR No.02]'
				 +'\nCODE : ' + request.status
				 +'\nSTAT : ' + status
				 +'\nMESSAGE : ' + request.responseText);
		}
	});
}


function lfTeamPop(svcCd, jumin){
	
	
	var width = 500;
	var height = 400;
	var left = (screen.availWidth - width) / 2;
	var top = (screen.availHeight - height) / 2;

	var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
	var win = window.open('', 'TEAM_POP', option);
		win.opener = self;
		win.focus();

	var parm = new Array();
		parm = {
			'svcCd'	:svcCd
		,	'jumin'	:jumin
		,	'mode'  :'teamSet'
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

	form.setAttribute('target', 'TEAM_POP');
	form.setAttribute('method', 'post');
	form.setAttribute('action', 'team.php');

	document.body.appendChild(form);

	form.submit();
	
}

</script>
<form name="f" method="post">

<div class="title title_border">담당자조회 및 설정</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="120px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="120px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">팀장명</th>
			<td class='left bottom'><div style='float:left;  width:auto; height:100%; padding-top:1px;'><span class='btn_pack find' onclick='findTeam();'></span></div><div style='width:auto; height:100%; padding-top:2px;'><!--span id='strTeam' name="strTeam" class='bold'><?=$strTeam;?></span--><input id="strTeam" name="strTeam" type="text" style="width:75px; padding:0; background-color:#eeeeee;" value="<?=$strTeam;?>" readonly /><input id="strTeamCd" name="strTeamCd" type="hidden" /></div></td>
			<th class="center">대상자명</th>
			<td class="center"><input id="cltNm" name="cltNm" type="text" value="" style="width:100%;"></td>
			<th class="center">서비스</th>
			<td >
			<?
				$kind_list = $conn->kind_list($code, $gHostSvc['voucher']);

				echo '<select name=\'kind\' id=\'kind\' style=\'width:auto;\'>';
				echo '<option value=\'\'>전체</option>';

				foreach($kind_list as $i => $k){
					echo '<option value=\''.$k['code'].'\' '.($kind == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
				}

				echo '</select>';
			?>
			</td>
			<td class="left last">
				<div style="float:left; width:auto;"><span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span></div>
			</td>
		</tr>
	</tbody>
</table>

<table id="tblList" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="120px">
		<col width="110px">
		<col width="80px">
		<col width="80px">
		<col width="40px" span="2">
		<col width="305px">
		<col >
	</colgroup>
	<tbody>
		<tr>
			<th class="center" >순번</th>
			<th class="center" >팀장명</th>
			<th class="center" >적용기간</th>
			<th class="center" >대상자명</th>
			<th class="center" >생년월일</th>
			<th class="center" >성별</th>
			<th class="center" >나이</th>
			<th class="center" >주소</th>
			<th class="center last" >&nbsp;</th>
		</tr>
		<tr>
			<td class="center top last" colspan="9">
				<div id="divList" style="overflow-x:hidden; overflow-y:scroll; height:605px;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<input id="code" name="code" type="hidden" value="<?=$code;?>">

</form>

<div id="loadingBody" style="position:absolute; left:0; top:0; widht:auto; height:auto;"></div>

<?
	include_once('../inc/_body_footer.php');
?>
<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	//고객정보
	$sql = 'select min(m03_mkind) as kind
			,      m03_jumin as jumin
			,      m03_name as name
			  from m03sugupja
			 where m03_ccode  = \''.$code.'\'
			   and m03_jumin  = \''.$jumin.'\'
			   and m03_del_yn = \'N\'';

	$row   = $conn->get_array($sql);
	$lsCNm = $row['name'];
	$lsCCd = $row['jumin'];
	$svcNm = $conn->_svcNm($svcCd);

	unset($row);

	//서비스 순번
	$sql = 'select seq
			  from client_his_svc
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_cd = \''.$svcCd.'\'
			   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
			 order by seq desc
			 limit 1';

	$liSeq = $conn->get_data($sql);

	//서비스정보
	$sql = 'select svc_val
			,      svc_cost
			,      svc_cnt
			  from client_his_other
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and svc_cd = \''.$svcCd.'\'
			   and seq    = \''.$liSeq.'\'';
	$row = $conn->get_array($sql);

	$lsSvcVal  = $row['svc_val'];  //서비스구분
	$liSvcCost = $row['svc_cost']; //서비스단가
	$liSvcCnt  = $row['svc_cnt'];  //서비스횟수

	unset($row);?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">고객명</th>
				<td class="center last"><div id="lblCNm" class="left"><a href="#" onclick="retrun false;"><span class="bold"><?=$lsCNm;?></span></a></div></td>
			</tr>
			<tr>
				<th class="center <?=($type == 'PLAN' ? 'bottom' : '');?>">생년월일</th>
				<td class="center <?=($type == 'PLAN' ? 'bottom' : '');?> last"><div class="left"><?=$myF->issToBirthday($lsCCd,'.');?></div></td>
			</tr>
		</tbody>
	</table>
	<div id="infoClient" style="display:none;"
		svcVal="<?=$lsSvcVal;?>"
		svcCost="<?=$liSvcCost;?>"
		svcCnt="<?=$liSvcCnt;?>"
		svcSeq="<?=$liSeq;?>"
		ynLoad="N"></div>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#infoClient').attr('ynLoad','Y');
		});
	</script>
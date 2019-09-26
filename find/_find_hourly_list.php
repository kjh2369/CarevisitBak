<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);
	$svcID = $_POST['svcID'];
	$svcname = Array('200'=>'요양', '500'=>'목욕', '800'=>'간호');


	$sql = 'SELECT	svc_cd, from_ym, to_ym, rate
			FROM	corp_rate
			ORDER	BY from_ym, svc_cd';

	$corp_rate = $conn->_fetch_array($sql);


	ob_start();


	echo '<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="60px">
				<col width="60px">
				<col width="70px">
				<col width="200px">
				<col width="70px">
				<col>
			</colgroup>
			<tbody>';


	$sql = 'select mh_kind as kind
			,      mh_svc as svc
			,      mh_seq as seq
			,      mh_type as type
			,      mh_hourly as hourly
			,      mh_vary_hourly_1 as vary_hourly_1
			,      mh_vary_hourly_2 as vary_hourly_2
			,      mh_vary_hourly_3 as vary_hourly_3
			,      mh_vary_hourly_4 as vary_hourly_4
			,      mh_vary_hourly_5 as vary_hourly_5
			,      mh_vary_hourly_6 as vary_hourly_6
			,      mh_vary_hourly_7 as vary_hourly_7
			,      mh_vary_hourly_8 as vary_hourly_8
			,      mh_vary_hourly_9 as vary_hourly_9
			,      mh_hourly_rate as hourly_rate
			,      mh_fixed_pay as fixed_pay
			,		mh_daily_pay1 AS daily_pay1
			,		mh_daily_pay2 AS daily_pay2
			,		mh_daily_pay3 AS daily_pay3
			,      mh_extra_yn as extra_yn
			,      mh_from_dt as from_dt
			,      mh_to_dt as to_dt
			,      case when mh_from_dt <= date_format(now(), \'%Y%m\') and mh_to_dt >= date_format(now(), \'%Y%m\') then \'setY\' else \'setN\' end as isSet
			  from mem_hourly
			 where org_no   = \''.$code.'\'
			   and mh_jumin = \''.$jumin.'\'
			   and mh_svc   = \''.$svcID.'\'
			   and del_flag = \'N\'
			 order by from_dt desc, to_dt desc';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		switch($row['type']){
			case '1':
				$type   = '시급';
				$hourly = number_format($row['hourly']);
				break;

			case '2':
				if ($lbSalarySet){
					$type   = '수가별';
					$hourly = '30분 : '.number_format($row['vary_hourly_1']).'/'
							. '60분 : '.number_format($row['vary_hourly_2']).'/'
							. '90분 : '.number_format($row['vary_hourly_3']).'/'
							. '120분 : '.number_format($row['vary_hourly_4']).'/'
							. '150분 : '.number_format($row['vary_hourly_5']).'/'
							. '180분 : '.number_format($row['vary_hourly_6']).'/'
							. '210분 : '.number_format($row['vary_hourly_7']).'/'
							. '240분 : '.number_format($row['vary_hourly_8']);
				}else{
					$type   = '변동시급';
					$hourly = number_format($row['vary_hourly_1']).'/'.number_format($row['vary_hourly_2']).'/'.number_format($row['vary_hourly_3']).'/'.number_format($row['vary_hourly_9']);
				}
				break;

			case '3':
				$type   = '고정급';
				$hourly = number_format($row['fixed_pay']);
				break;

			case '4':
				$type   = '총액비율';
				$hourly = number_format($row['hourly_rate']).'%';
				break;

			case '5':
				$type   = '수가비율';
				$hourly = number_format($row['hourly_rate']).'%';
				break;

			case '6':
				$type   = '일당';
				$hourly = number_format($row['daily_pay1']).' / '.number_format($row['daily_pay2']).' / '.number_format($row['daily_pay3']);
				break;

			case '7':
				$type = '공단비율';
				$yymm = SubStr(str_replace('-', '', $myF->_getDt($row['from_dt'].'01', $row['to_dt'].'01')), 0, 6);

				$hourly = '';

				for($j=0; $j<count($corp_rate); $j++){
					if ($corp_rate[$j]['from_ym'] <= $yymm && $corp_rate[$j]['to_ym'] >= $yymm){
						$hourly .= ($hourly ? '/' : '').$svcname[$corp_rate[$j]['svc_cd']].':'.$corp_rate[$j]['rate'];
					}
				}

				break;

			default:
				$type   = '무';
				$hourly = 0;
		}

		echo '<tr>
				<td class="center '.$row['isSet'].'_fromDt">'.$myF->_styleYYMM($row['from_dt'],'.').'</td>
				<td class="center '.$row['isSet'].'_toDt">'.$myF->_styleYYMM($row['to_dt'],'.').'</td>
				<td class="center">'.$type.'<div class="'.$row['isSet'].'_type" style="display:none;">'.$row['type'].'</div></td>
				<td class="center"><div class="right '.$row['isSet'].'_hourly"><div class="nowrap" style="width:190px;" title="'.$hourly.'">'.$hourly.'</div></div></td>
				<td class="center">'.($row['extra_yn'] == 'Y' ? '예' : '아니오').'<div class="'.$row['isSet'].'_extraYN" style="display:none;">'.($row['extra_yn'] == 'Y' ? 'checked' : '').'</div></td>
				<td class="center"><div class="left">'.($i == 0 ? '<span class="btn_pack m"><button type="button" onclick="rowDelete(\''.$row['seq'].'\');">삭제</button></span>' : '').'</div></td>
			  </tr>
			  <div id="'.$row['isSet'].'_seq"" style="display:none;">'.$row['seq'].'</div>';
	}

	$conn->row_free();

	echo '	</tbody>
		  </table>';


	$html = ob_get_contents();

	ob_clean();

	$html = $myF->_gabSplitHtml($html);

	echo $html;

	unset($corp_rate);


	include_once('../inc/_db_close.php');
?>
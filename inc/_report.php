<?
	class Report{
		var $conn = null;

		function report($conn){
			$this->conn = $conn;
			$this->conn->fetch_type = 'assoc';
		}

		function btn_group($body, $menu, $tab, $index, $code, $kind, $client_cd, $date, $seq, $member_cd){
			$btn = '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'modifyReport('.$body.',"'.$menu.'","'.$tab.'","'.$index.'","'.$code.'","'.$kind.'","'.$client_cd.'","'.$date.'","'.$seq.'","'.$member_cd.'");\'>수정</button></span>
				    <span class=\'btn_pack m\'><button type=\'button\' onclick=\'showMyReport("'.$index.'","'.$code.'","'.$kind.'","'.$date.'","'.$client_cd.'","'.$member_cd.'","'.$seq.'");\'>출력</button></span>
					<span class=\'btn_pack m\'><button type=\'button\' onclick=\'deleteReport('.$body.',"'.$menu.'","'.$tab.'","'.$index.'","'.$code.'","'.$kind.'","'.$client_cd.'","'.$date.'","'.$seq.'","'.$member_cd.'");\'>삭제</button></span>';

			return $btn;
		}

		function msg_foot($f, $cnt, $cols){
			if ($cnt == 0){
				echo '<td class=\'center last\' colspan=\''.$cols.'\'>'.$f->message('nodata','N').'</td>';
			}else{
				echo '<td class=\'left bottom last\' colspan=\''.$cols.'\'>'.$f->message($cnt,'N').'</td>';
			}
		}




		/********************************

			리포트 아이디

		********************************/
		function get_report_id($report_index){
			$report_id = explode('_', $report_index);
			$report_id = $report_id[sizeof($report_id) - 1];

			return $report_id;
		}


		/**************************************************

			리포트 메뉴 인덱스

		**************************************************/
		function get_report_menu($report){
			$report = explode('_', $report);
			$report = $report[0];

			return $report;
		}


		/********************************

			월별 데이타 카운트

		********************************/
		function get_month_cnt($report_id, $code, $year, $ssn = ''){

			switch($report_id){

				case 'MCR':
					$sql = 'select sum(case substring(m32_a_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(m32_a_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(m32_a_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(m32_a_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(m32_a_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(m32_a_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(m32_a_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(m32_a_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(m32_a_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(m32_a_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(m32_a_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(m32_a_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from m32jikwon
							 where m32_ccode           = \''.$code.'\'
							   and left(m32_a_date, 4) = \''.$year.'\'';
					break;

				case 'MEMTR':
					$sql = 'select sum(case date_format(stress_dt,\'%m\') when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case date_format(stress_dt,\'%m\') when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case date_format(stress_dt,\'%m\') when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case date_format(stress_dt,\'%m\') when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case date_format(stress_dt,\'%m\') when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case date_format(stress_dt,\'%m\') when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case date_format(stress_dt,\'%m\') when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case date_format(stress_dt,\'%m\') when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case date_format(stress_dt,\'%m\') when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case date_format(stress_dt,\'%m\') when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case date_format(stress_dt,\'%m\') when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case date_format(stress_dt,\'%m\') when \'12\' then 1 else 0 end) as \'12\'
							  from counsel_stress
							 where org_no             = \''.$code.'\'
							   and left(stress_dt, 4) = \''.$year.'\'';
					break;

				case 'CLTLCC':
					$sql = 'select sum(case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'0\'
							   and t01_svc_subcode         = \'200\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					break;

				case 'CLTLCC12':
					$sql = 'select sum(case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'0\'
							   AND t01_status_gbn		   = \'1\'
							   and t01_svc_subcode         = \'200\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					break;

				case 'CLTLCCNEW':
					$sql = 'select sum(case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'0\'
							   and t01_svc_subcode         = \'200\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.$year.'\'
							   and left(t01_sugup_date, 6) >= \'201407\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					break;

				case 'CLTLCCNEW2':
					$sql = 'select sum(case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'0\'
							   and t01_svc_subcode         = \'200\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.$year.'\'
							   and left(t01_sugup_date, 6) >= \'201407\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					break;

				case 'CLTLCB':

					$sql = 'select sum(case substring(date,5,2) when \'01\' then 1 else 0 end) as \'1\'
								,      sum(case substring(date,5,2) when \'02\' then 1 else 0 end) as \'2\'
								,      sum(case substring(date,5,2) when \'03\' then 1 else 0 end) as \'3\'
								,      sum(case substring(date,5,2) when \'04\' then 1 else 0 end) as \'4\'
								,      sum(case substring(date,5,2) when \'05\' then 1 else 0 end) as \'5\'
								,      sum(case substring(date,5,2) when \'06\' then 1 else 0 end) as \'6\'
								,      sum(case substring(date,5,2) when \'07\' then 1 else 0 end) as \'7\'
								,      sum(case substring(date,5,2) when \'08\' then 1 else 0 end) as \'8\'
								,      sum(case substring(date,5,2) when \'09\' then 1 else 0 end) as \'9\'
								,      sum(case substring(date,5,2) when \'10\' then 1 else 0 end) as \'10\'
								,      sum(case substring(date,5,2) when \'11\' then 1 else 0 end) as \'11\'
								,      sum(case substring(date,5,2) when \'12\' then 1 else 0 end) as \'12\'
									FROM (
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'    union all
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'		) AS iljung';


					break;

				case 'CLTLCBNEW':

					$sql = 'select sum(case substring(date,5,2) when \'01\' then 1 else 0 end) as \'1\'
								,      sum(case substring(date,5,2) when \'02\' then 1 else 0 end) as \'2\'
								,      sum(case substring(date,5,2) when \'03\' then 1 else 0 end) as \'3\'
								,      sum(case substring(date,5,2) when \'04\' then 1 else 0 end) as \'4\'
								,      sum(case substring(date,5,2) when \'05\' then 1 else 0 end) as \'5\'
								,      sum(case substring(date,5,2) when \'06\' then 1 else 0 end) as \'6\'
								,      sum(case substring(date,5,2) when \'07\' then 1 else 0 end) as \'7\'
								,      sum(case substring(date,5,2) when \'08\' then 1 else 0 end) as \'8\'
								,      sum(case substring(date,5,2) when \'09\' then 1 else 0 end) as \'9\'
								,      sum(case substring(date,5,2) when \'10\' then 1 else 0 end) as \'10\'
								,      sum(case substring(date,5,2) when \'11\' then 1 else 0 end) as \'11\'
								,      sum(case substring(date,5,2) when \'12\' then 1 else 0 end) as \'12\'
									FROM (
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'    union all
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'		) AS iljung';


					break;


				case 'CLTLCBB':

					$sql = 'select sum(case substring(date,5,2) when \'01\' then 1 else 0 end) as \'1\'
								,      sum(case substring(date,5,2) when \'02\' then 1 else 0 end) as \'2\'
								,      sum(case substring(date,5,2) when \'03\' then 1 else 0 end) as \'3\'
								,      sum(case substring(date,5,2) when \'04\' then 1 else 0 end) as \'4\'
								,      sum(case substring(date,5,2) when \'05\' then 1 else 0 end) as \'5\'
								,      sum(case substring(date,5,2) when \'06\' then 1 else 0 end) as \'6\'
								,      sum(case substring(date,5,2) when \'07\' then 1 else 0 end) as \'7\'
								,      sum(case substring(date,5,2) when \'08\' then 1 else 0 end) as \'8\'
								,      sum(case substring(date,5,2) when \'09\' then 1 else 0 end) as \'9\'
								,      sum(case substring(date,5,2) when \'10\' then 1 else 0 end) as \'10\'
								,      sum(case substring(date,5,2) when \'11\' then 1 else 0 end) as \'11\'
								,      sum(case substring(date,5,2) when \'12\' then 1 else 0 end) as \'12\'
									FROM (
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'    union all
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'		) AS iljung';


					break;

				case 'CLTLCBBB':

					$sql = 'select sum(case substring(date,5,2) when \'01\' then 1 else 0 end) as \'1\'
								,      sum(case substring(date,5,2) when \'02\' then 1 else 0 end) as \'2\'
								,      sum(case substring(date,5,2) when \'03\' then 1 else 0 end) as \'3\'
								,      sum(case substring(date,5,2) when \'04\' then 1 else 0 end) as \'4\'
								,      sum(case substring(date,5,2) when \'05\' then 1 else 0 end) as \'5\'
								,      sum(case substring(date,5,2) when \'06\' then 1 else 0 end) as \'6\'
								,      sum(case substring(date,5,2) when \'07\' then 1 else 0 end) as \'7\'
								,      sum(case substring(date,5,2) when \'08\' then 1 else 0 end) as \'8\'
								,      sum(case substring(date,5,2) when \'09\' then 1 else 0 end) as \'9\'
								,      sum(case substring(date,5,2) when \'10\' then 1 else 0 end) as \'10\'
								,      sum(case substring(date,5,2) when \'11\' then 1 else 0 end) as \'11\'
								,      sum(case substring(date,5,2) when \'12\' then 1 else 0 end) as \'12\'
									FROM (
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'    union all
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'		) AS iljung';


					break;

				case 'CLTLCBREC':

					$sql = 'select sum(case substring(date,5,2) when \'01\' then 1 else 0 end) as \'1\'
								,      sum(case substring(date,5,2) when \'02\' then 1 else 0 end) as \'2\'
								,      sum(case substring(date,5,2) when \'03\' then 1 else 0 end) as \'3\'
								,      sum(case substring(date,5,2) when \'04\' then 1 else 0 end) as \'4\'
								,      sum(case substring(date,5,2) when \'05\' then 1 else 0 end) as \'5\'
								,      sum(case substring(date,5,2) when \'06\' then 1 else 0 end) as \'6\'
								,      sum(case substring(date,5,2) when \'07\' then 1 else 0 end) as \'7\'
								,      sum(case substring(date,5,2) when \'08\' then 1 else 0 end) as \'8\'
								,      sum(case substring(date,5,2) when \'09\' then 1 else 0 end) as \'9\'
								,      sum(case substring(date,5,2) when \'10\' then 1 else 0 end) as \'10\'
								,      sum(case substring(date,5,2) when \'11\' then 1 else 0 end) as \'11\'
								,      sum(case substring(date,5,2) when \'12\' then 1 else 0 end) as \'12\'
									FROM (
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'    union all
								SELECT t01_sugup_date as date
								  FROM t01iljung
								 WHERE t01_ccode = \''.$code.'\'
								   AND t01_mkind = \'0\'
								   AND t01_svc_subcode =\'500\'
								   AND t01_del_yn = \'N\'
								   AND LEFT(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';

					$sql .=	'		) AS iljung';


					break;

				case 'CLTLCN':
					$sql = 'select sum(case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'0\'
							   and t01_svc_subcode         = \'800\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					break;

				case 'CLTLCNN':
					$sql = 'select sum(case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'0\'
							   and t01_svc_subcode         = \'800\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.$year.'\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					break;

				case 'CLTLCNNEW':
					$sql = 'select sum(case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'0\'
							   and t01_svc_subcode         = \'800\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.$year.'\'
							   and left(t01_sugup_date, 6) >= \'201407\'';

					if($_SESSION['userLevel'] == 'P') $sql .= ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					break;

				case 'CLTLCBILL':
					$sql = 'select sum(case substring(t13_pay_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t13_pay_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t13_pay_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t13_pay_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t13_pay_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t13_pay_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t13_pay_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t13_pay_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t13_pay_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t13_pay_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t13_pay_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t13_pay_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t13sugupja
							 where t13_ccode             = \''.$code.'\'
							   and t13_type              = \'2\'
							   and left(t13_pay_date, 4) = \''.$year.'\'';
					break;

				case 'SLT':
					$sql = 'select sum(case substring(salary_yymm,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(salary_yymm,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(salary_yymm,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(salary_yymm,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(salary_yymm,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(salary_yymm,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(salary_yymm,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(salary_yymm,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(salary_yymm,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(salary_yymm,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(salary_yymm,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(salary_yymm,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from salary_basic
							 where org_no               = \''.$code.'\'
							   and left(salary_yymm, 4) = \''.$year.'\'';
					break;

				case 'CLTLCVB':
					$sql = 'select sum(case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'3\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.$year.'\'';
					break;

				case 'CLTLCVC':
					$sql = 'select sum(case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'4\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.$year.'\'';
					break;

				case 'CLTLCVN':
					$sql = 'select sum(m01) as \'1\'
							,      sum(m02) as \'2\'
							,      sum(m03) as \'3\'
							,      sum(m04) as \'4\'
							,      sum(m05) as \'5\'
							,      sum(m06) as \'6\'
							,      sum(m07) as \'7\'
							,      sum(m08) as \'8\'
							,      sum(m09) as \'9\'
							,      sum(m10) as \'10\'
							,      sum(m11) as \'11\'
							,      sum(m12) as \'12\'
							  from (
							       select case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end as \'m01\'
							       ,      case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end as \'m02\'
							       ,      case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end as \'m03\'
							       ,      case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end as \'m04\'
							       ,      case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end as \'m05\'
							       ,      case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end as \'m06\'
							       ,      case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end as \'m07\'
							       ,      case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end as \'m08\'
							       ,      case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end as \'m09\'
							       ,      case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end as \'m10\'
							       ,      case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end as \'m11\'
							       ,      case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end as \'m12\'
							         from t01iljung
							        where t01_ccode               = \''.$code.'\'
							          and t01_mkind               = \'1\'
							          and t01_del_yn              = \'N\'
							          and left(t01_sugup_date, 4) = \''.$year.'\'
							        union all
							       select case substring(t01_sugup_date,5,2) when \'01\' then 1 else 0 end as \'m01\'
							       ,      case substring(t01_sugup_date,5,2) when \'02\' then 1 else 0 end as \'m02\'
							       ,      case substring(t01_sugup_date,5,2) when \'03\' then 1 else 0 end as \'m03\'
							       ,      case substring(t01_sugup_date,5,2) when \'04\' then 1 else 0 end as \'m04\'
							       ,      case substring(t01_sugup_date,5,2) when \'05\' then 1 else 0 end as \'m05\'
							       ,      case substring(t01_sugup_date,5,2) when \'06\' then 1 else 0 end as \'m06\'
							       ,      case substring(t01_sugup_date,5,2) when \'07\' then 1 else 0 end as \'m07\'
							       ,      case substring(t01_sugup_date,5,2) when \'08\' then 1 else 0 end as \'m08\'
							       ,      case substring(t01_sugup_date,5,2) when \'09\' then 1 else 0 end as \'m09\'
							       ,      case substring(t01_sugup_date,5,2) when \'10\' then 1 else 0 end as \'m10\'
							       ,      case substring(t01_sugup_date,5,2) when \'11\' then 1 else 0 end as \'m11\'
							       ,      case substring(t01_sugup_date,5,2) when \'12\' then 1 else 0 end as \'m12\'
							         from t01iljung
							        where t01_ccode               = \''.$code.'\'
							          and t01_mkind               = \'2\'
							          and t01_del_yn              = \'N\'
							          and left(t01_sugup_date, 4) = \''.$year.'\'
								   ) as t';

					break;

				case 'CLTCRSRH':
					$sql = 'select sum(case substring(r_yymm,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(r_yymm,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(r_yymm,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(r_yymm,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(r_yymm,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(r_yymm,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(r_yymm,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(r_yymm,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(r_yymm,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(r_yymm,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(r_yymm,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(r_yymm,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from r_quest
							 where org_no          = \''.$code.'\'
							   and left(r_yymm, 4) = \''.$year.'\'
							   and r_service_gbn   = \'200\'';
					break;

				case 'CLTBRSRH':
					$sql = 'select sum(case substring(r_yymm,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(r_yymm,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(r_yymm,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(r_yymm,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(r_yymm,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(r_yymm,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(r_yymm,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(r_yymm,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(r_yymm,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(r_yymm,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(r_yymm,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(r_yymm,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from r_quest
							 where org_no          = \''.$code.'\'
							   and left(r_yymm, 4) = \''.$year.'\'
							   and r_service_gbn   = \'500\'';
					break;

				case 'CLTNRSRH':
					$sql = 'select sum(case substring(r_yymm,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(r_yymm,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(r_yymm,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(r_yymm,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(r_yymm,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(r_yymm,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(r_yymm,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(r_yymm,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(r_yymm,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(r_yymm,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(r_yymm,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(r_yymm,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from r_quest
							 where org_no          = \''.$code.'\'
							   and left(r_yymm, 4) = \''.$year.'\'
							   and r_service_gbn   = \'800\'';
					break;

				default:
					$sql = 'select sum(case substring(r_yymm,5,2) when \'01\' then 1 else 0 end) as \'1\'
							,      sum(case substring(r_yymm,5,2) when \'02\' then 1 else 0 end) as \'2\'
							,      sum(case substring(r_yymm,5,2) when \'03\' then 1 else 0 end) as \'3\'
							,      sum(case substring(r_yymm,5,2) when \'04\' then 1 else 0 end) as \'4\'
							,      sum(case substring(r_yymm,5,2) when \'05\' then 1 else 0 end) as \'5\'
							,      sum(case substring(r_yymm,5,2) when \'06\' then 1 else 0 end) as \'6\'
							,      sum(case substring(r_yymm,5,2) when \'07\' then 1 else 0 end) as \'7\'
							,      sum(case substring(r_yymm,5,2) when \'08\' then 1 else 0 end) as \'8\'
							,      sum(case substring(r_yymm,5,2) when \'09\' then 1 else 0 end) as \'9\'
							,      sum(case substring(r_yymm,5,2) when \'10\' then 1 else 0 end) as \'10\'
							,      sum(case substring(r_yymm,5,2) when \'11\' then 1 else 0 end) as \'11\'
							,      sum(case substring(r_yymm,5,2) when \'12\' then 1 else 0 end) as \'12\'
							  from r_'.strtolower($report_id).'
							 where org_no          = \''.$code.'\'
							   and left(r_yymm, 4) = \''.$year.'\'';

					if (!empty($ssn)){
						$sql .= ' and r_m_id = \''.$ssn.'\'';
					}

			}

			$tmp = @$this->conn->get_array($sql);

			return $tmp;
		}




		/********************************

			리포트 내용

		********************************/
		function get_report_cont($report_id, $code, $yymm, $seq, $ssn){

			//서비스만족도조사(방문요양,목욕,간호)
			if($report_id == 'CLTCRSRH' ||
			   $report_id == 'CLTBRSRH' ||
			   $report_id == 'CLTNRSRH' ){

				$report_id = 'QUEST';
			}

			$sql = 'select *
					  from r_'.strtolower($report_id).'
					 where org_no = \''.$code.'\'
					   and r_yymm = \''.$yymm.'\'
					   and r_seq  = \''.$seq.'\'';

			if(!empty($ssn)) $sql .= 'and r_c_id = \''.$ssn.'\'';

			return $this->conn->get_array($sql);
		}




		/********************************

			리포트 헤더 넓이

		********************************/
		function col_group($index){
			switch($index){
				case 'HUREC'		: $str = $this->col_group_sub(array(40,80,80,90)); break;
				case 'MCR'			: $str = $this->col_group_sub(array(40,70,70,70,50,150,80)); break;
				case 'QARR'			: $str = $this->col_group_sub(array(40,70,150,70,150)); break;
				case 'QI'			: $str = $this->col_group_sub(array(40,70,150,70,150)); break;
				case 'WR60U'		: $str = $this->col_group_sub(array(40,80,80,100)); break;
				case 'WR60D'		: $str = $this->col_group_sub(array(40,80,80,100)); break;
				case 'WR60M'		: $str = $this->col_group_sub(array(40,80,80,100)); break;
				case 'WRT'			: $str = $this->col_group_sub(array(40,70,70,100,50)); break;
				case 'SLT'			: $str = $this->col_group_sub(array(40,70,70,80,70,70)); break;
				case 'WTOTO'		: $str = $this->col_group_sub(array(40,70,70,70,70)); break;
				case 'MONMR'		: $str = $this->col_group_sub(array(40,70,70,150)); break;
				case 'MEMTR'		: $str = $this->col_group_sub(array(40,70,70,70,70,330)); break;
				case 'MEMJAS'		: $str = $this->col_group_sub(array(40,70,70,150,150)); break;
				case 'MEMTAKE'		: $str = $this->col_group_sub(array(40,70,70,70,70)); break;
				case 'MEMEDU'		: $str = $this->col_group_sub(array(50,110,150,70,150)); break;
				case 'CLTBR'		: $str = $this->col_group_sub(array(40,70,100,230,100)); break;
				case 'CLTBSR'		: $str = $this->col_group_sub(array(40,70,70,70,70,80,70)); break;
				case 'CLTBSR2'		: $str = $this->col_group_sub(array(40,70,70,70,70,80,70)); break;
				case 'CLTPST'		: $str = $this->col_group_sub(array(40,70,70,70,70,70)); break;
				case 'CLTDDT'		: $str = $this->col_group_sub(array(40,70,70,70,70,50)); break;
				case 'CLTPICMAN'	: $str = $this->col_group_sub(array(40,70,70,70,70)); break;
				case 'CLTMRRC'		: $str = $this->col_group_sub(array(40,70,70,70,70)); break;
				case 'CLTMRRB'		: $str = $this->col_group_sub(array(40,70,70,70,70)); break;
				case 'CLTLCC'		: $str = $this->col_group_sub(array(40,70,70,50,90,60,80)); break;
				case 'CLTLCCNEW'	: $str = $this->col_group_sub(array(40,70,70,50,90,60,80)); break;
				case 'CLTLCCNEW2'	: $str = $this->col_group_sub(array(40,70,70,50,90,60,80)); break;
				case 'CLTLCB'		: $str = $this->col_group_sub(array(40,70,70,50,60,80)); break;
				case 'CLTLCNEW'		: $str = $this->col_group_sub(array(40,70,70,50,60,80)); break;
				case 'CLTLCBB'		: $str = $this->col_group_sub(array(40,70,70,50,60,80)); break;
				case 'CLTLCBBB'		: $str = $this->col_group_sub(array(40,70,70,50,60,80)); break;
				case 'CLTLCBNEW'	: $str = $this->col_group_sub(array(40,70,70,50,60,80)); break;
				case 'CLTLCBNEW2'	: $str = $this->col_group_sub(array(40,70,70,50,60,80)); break;
				case 'CLTLCBREC'	: $str = $this->col_group_sub(array(40,70,70,50,60,80)); break;
				case 'CLTLCN'		: $str = $this->col_group_sub(array(40,70,70,50,90,60,80)); break;
				case 'CLTLCNN'		: $str = $this->col_group_sub(array(40,70,70,50,90,60,80)); break;
				case 'CLTLCNNEW'	: $str = $this->col_group_sub(array(40,70,70,50,90,60,80)); break;
				case 'CLTPSR'		: $str = $this->col_group_sub(array(40,70,70,70,70)); break;
				case 'CLTLCMOR'		: $str = $this->col_group_sub(array(40,70,70,100,50,100,70,70,70)); break;
				case 'CLTLCBILL'	: $str = $this->col_group_sub(array(40,70,70,100,50,100,70,70,70)); break;
				case 'CTLRECEIPT'	: $str = $this->col_group_sub(array(40,100,100,100)); break;
				case 'CLTLCPLAN'	: $str = $this->col_group_sub(array(40,70,70,50,90)); break;
				case 'CLTLCVB'		: $str = $this->col_group_sub(array(40,70,70,90)); break;
				case 'CLTLCVC'		: $str = $this->col_group_sub(array(40,70,70,90)); break;
				case 'CLTLCVN'		: $str = $this->col_group_sub(array(40,70,90,70,90)); break;
				case 'CLTSVCCTC'	: $str = $this->col_group_sub(array(40,70,70,50,70,75)); break;
				case 'CLTSVCCTCC'	: $str = $this->col_group_sub(array(40,70,70,70,150)); break;
				case 'CLTSVCCTBB'	: $str = $this->col_group_sub(array(40,70,70,70,150)); break;
				case 'CLTPLAN'		: $str = $this->col_group_sub(array(40,70,70,70,80)); break;
				case 'CLTREC'		: $str = $this->col_group_sub(array(40,70,70,100,80,140,70)); break;
				case 'CLTOLD'		: $str = $this->col_group_sub(array(40,80,100,100,180,100)); break;
				case 'CLTCRSRH'		: $str = $this->col_group_sub(array(40,70,70,70,110)); break;
				case 'CLTBRSRH'		: $str = $this->col_group_sub(array(40,70,70,70,110)); break;
				case 'CLTNRSRH'		: $str = $this->col_group_sub(array(40,70,70,70,110)); break;
				case 'CLTPLAGC'		: $str = $this->col_group_sub(array(40,70,110,70,60)); break;
				case 'CLTPLAGB'		: $str = $this->col_group_sub(array(40,70,110,70,60)); break;
				case 'CLTPLAGN'		: $str = $this->col_group_sub(array(40,70,110,70,60)); break;
				case 'CLTSUCHC'		: $str = $this->col_group_sub(array(40,70,110,50,90)); break;
				case 'NURRSH'		: $str = $this->col_group_sub(array(40,70,70,70,70,50)); break;
				case 'CLTLCC12'		: $str = $this->col_group_sub(array(40,70,70,50,60,80)); break;
				case 'SW10'			: $str = $this->col_group_sub(array(40,70,70,50,60,80)); break;
				case 'CLTSTATRCD'	: $str = $this->col_group_sub(array(40,70,70,50,50)); break;
				case 'CLTPLANCHN'	: $str = $this->col_group_sub(array(40,70,70,50,50)); break;

				default:
					$str = 'col group : '.$index;
			}

			return $str;
		}

		function col_group_sub($col){
			$col_cnt = sizeof($col);

			for($i=0; $i<$col_cnt; $i++){
				$str .= '<col width=\''.$col[$i].'px\'>';
			}

			$str .= '<col>';

			return $str;
		}

		/********************************

			리포트 헤더명

		********************************/

		function col_header($index){

			$year = $_POST['year'] != '' ? $_POST['year'] : date('Y');
			$month = $_POST['month'] != '' ? $_POST['month']<10?'0'.$_POST['month'] : $_POST['month'] : date('m');

			$para  = ' "yymm":"'.$year.$month.'"';
			$para .= ',"mode":"all"';
			$para  = '{'.$para.'}';
			$para  = (!empty($para)?','.$para:'');

			switch($index){
				case 'HUREC'		: $str = $this->col_header_sub(array('No','근로자','생년월일','연락처','비고')); break;
				case 'MCR'			: $str = $this->col_header_sub(array('No','변경일자','고객명','생년월일','등급','구분','요양보호사','비고')); break;
				case 'QARR'			: $str = $this->col_header_sub(array('No','작성일자','담당부서','작성자','사업명','비고')); break;
				case 'QI'			: $str = $this->col_header_sub(array('No','작성일자','담당부서','작성자','사업명','비고')); break;
				case 'WR60U'		: $str = $this->col_header_sub(array('No','근로자','생년월일','연락처','비고')); break;
				case 'WR60D'		: $str = $this->col_header_sub(array('No','근로자','생년월일','연락처','비고')); break;
				case 'WR60M'		: $str = $this->col_header_sub(array('No','근로자','생년월일','연락처','비고')); break;
				case 'WRT'			: $str = $this->col_header_sub(array('No','담당자','고객명','제공서비스','등급','')); break;
				case 'SLT'			: $str = $this->col_header_sub(array('No','직원명','생년월일','연락처','입사일','퇴사일','비고')); break;
				case 'WTOTO'		: $str = $this->col_header_sub(array('No','작성일','인계자','인수자','고객명','비고')); break;
				case 'MONMR'		: $str = $this->col_header_sub(array('No','회의일','진행자','장소','비고')); break;
				case 'MEMTR'		: $str = $this->col_header_sub(array('No','상담일자','직원명','상담자','상담유형','처리결과','비고')); break;
				case 'MEMJAS'		: $str = $this->col_header_sub(array('No','작성일','직원명','소속','직무','비고')); break;
				case 'MEMTAKE'		: $str = $this->col_header_sub(array('No','인수인계일','인계자','수급자','인수자','비고')); break;
				case 'MEMEDU'		: $str = $this->col_header_sub(array('No','일시','장소','강사','주제','비고')); break;
				case 'CLTBR'		: $str = $this->col_header_sub(array('No','고객명','연락처','상담구분','서비스','비고')); break;
				case 'CLTBSR'		: $str = $this->col_header_sub(array('No','작성일','작성자','고객명','생년월일','연락처','보호자','비고')); break;
				case 'CLTBSR2'		: $str = $this->col_header_sub(array('No','작성일','작성자','고객명','생년월일','연락처','보호자','비고')); break;
				case 'CLTPST'		: $str = $this->col_header_sub(array('No','평가일','평가자','고객명','생년월일','평가점수','비고')); break;
				case 'CLTDDT'		: $str = $this->col_header_sub(array('No','작성일','작성자','고객명','생년월일','점수','비고')); break;
				case 'CLTPICMAN'	: $str = $this->col_header_sub(array('No','작성일','작성자','고객명','생년월일','비고')); break;
				case 'CLTMRRC'		: $str = $this->col_header_sub(array('No','작성일','작성자','고객명','생년월일','비고')); break;
				case 'CLTMRRB'		: $str = $this->col_header_sub(array('No','작성일','작성자','고객명','생년월일','비고')); break;
				case 'CLTLCC'		: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','병명','연락처','<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>전체</button></span>')); break;
				case 'CLTLCCNEW'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','병명','연락처','<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>전체</button></span>')); break;
				case 'CLTLCCNEW2'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','병명','연락처','<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>전체</button></span>')); break;
				case 'CLTLCB'		: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','연락처','비고')); break;
				case 'CLTLCBNEW'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','연락처','비고')); break;
				case 'CLTLCBNEW2'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','연락처','비고')); break;
				case 'CLTLCBB'		: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','연락처','비고')); break;
				case 'CLTLCBBB'		: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','연락처','비고')); break;
				case 'CLTLCBREC'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','연락처','비고')); break;
				case 'CLTLCN'		: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','병명','연락처','비고')); break;
				case 'CLTLCNN'		: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','병명','연락처','비고')); break;
				case 'CLTLCNNEW'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','병명','연락처','비고')); break;
				case 'CLTPSR'		: $str = $this->col_header_sub(array('No','작성일','작성자','고객명','생년월일','비고')); break;
				case 'CLTLCMOR'		: $str = $this->col_header_sub(array('No','고객명','생년월일','서비스','등급','구분','총금액','공단청구액','고객부담액','비고')); break;
				case 'CLTLCBILL'	: $str = $this->col_header_sub(array('No','고객명','생년월일','서비스','등급','구분','총금액','공단청구액','고객부담액','비고')); break;
				case 'CTLRECEIPT'	: $str = $this->col_header_sub(array('월','총금액','공단청구액','고객부담액','비고')); break;
				case 'CLTLCPLAN'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','비고')); break;
				case 'CLTLCVB'		: $str = $this->col_header_sub(array('No','고객명','생년월일','연락처','비고')); break;
				case 'CLTLCVC'		: $str = $this->col_header_sub(array('No','고객명','생년월일','연락처','비고')); break;
				case 'CLTLCVN'		: $str = $this->col_header_sub(array('No','고객명','서비스','생년월일','연락처','비고')); break;
				case 'CLTSVCCTC'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','본인부담율','인정번호','비고')); break;
				case 'CLTSVCCTCC'	: $str = $this->col_header_sub(array('No','작성일','고객명','생년월일','계약기간','비고')); break;
				case 'CLTSVCCTBB'	: $str = $this->col_header_sub(array('No','작성일','고객명','생년월일','계약기간','비고')); break;
				case 'CLTPLAN'		: $str = $this->col_header_sub(array('No','작성일','담당자','고객명','생년월일','비고')); break;
				case 'CLTREC'		: $str = $this->col_header_sub(array('No','작성일','수급자','주민번호','인정번호','유효기간','담당자','비고')); break;
				case 'CLTOLD'		: $str = $this->col_header_sub(array('No','작성일','신청인','대리인','대리인유형','신청인과의관계','비고')); break;
				case 'CLTCRSRH'		: $str = $this->col_header_sub(array('No','작성일','작성자','수급자','주민번호','비고')); break;
				case 'CLTBRSRH'		: $str = $this->col_header_sub(array('No','작성일','작성자','수급자','주민번호','비고')); break;
				case 'CLTNRSRH'		: $str = $this->col_header_sub(array('No','작성일','작성자','수급자','주민번호','비고')); break;
				case 'CLTPLAGC'		: $str = $this->col_header_sub(array('No','성명','주민등록번호','담당자','등급','비고')); break;
				case 'CLTPLAGB'		: $str = $this->col_header_sub(array('No','성명','주민등록번호','담당자','등급','비고')); break;
				case 'CLTPLAGN'		: $str = $this->col_header_sub(array('No','성명','주민등록번호','담당자','등급','비고')); break;
				case 'CLTSUCHC'		: $str = $this->col_header_sub(array('No','성명','주민등록번호','등급','전화번호','비고')); break;
				case 'NURRSH'		: $str = $this->col_header_sub(array('No','작성일','작성자','고객명','전화번호','등급','비고')); break;
				case 'CLTLCC12'		: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','연락처','비고')); break;
				case 'SW10'			: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','연락처','비고')); break;
				case 'CLTSTATRCD'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>전체</button></span>')); break;
				case 'CLTPLANCHN'	: $str = $this->col_header_sub(array('No','고객명','생년월일','등급','구분','비고')); break;

				default:
					$str = 'col header : '.$index;
			}

			return $str;
		}

		function col_header_sub($col){
			$col_cnt = sizeof($col);

			$str = '<tr>';

			for($i=0; $i<$col_cnt; $i++){

				if($col[$i] == '비고'){
					$cs = 'head ';
				}else {
					$cs = '';
				}

				$str .= '<th class=\''.$cs.' '.($i == $col_cnt - 1 ? 'last' : '').'\'>'.$col[$i].'</th>';
			}

			$str .= '</tr>';

			return $str;
		}

		/********************************

			리포트 푸터

		********************************/
		function col_footer($f, $cnt, $cols){
			$str = '<tr>';

			if ($cnt == 0){
				$str .= '<td class=\'center last\' colspan=\''.$cols.'\'>'.$f->message('nodata','N').'</td>';
			}else{
				$str .= '<td class=\'left bottom last\' colspan=\''.$cols.'\'>'.$f->message($cnt,'N').'</td>';
			}

			$str .= '</tr>';

			return $str;
		}


		/********************************

			버튼 그룹

		********************************/
		function col_button($code, $report_menu, $report_index, $yymm, $seq, $year, $month, $event, $para = '', $ssn = ''){
			$report_id = $this->get_report_id($report_index);
			$btn  = '';
			$para  = (!empty($para)?','.$para:'');

			if ($report_id == 'WR60U'	||
				$report_id == 'WR60D'	||
				$report_id == 'WR60M'	){

				if ($event['word']  == 'Y') $btn .= '<span id=\'contract_report\''.$seq.'\' class=\'btn_pack m\'><span class=\'word\'></span><button type=\'button\' onclick=\'_contract_dt_input_layer(this'.$para.');\'>출력</button></span>';
			}else if ($report_id == 'CLTSVCCTC'){
				$params = explode(":", $para);
				$params = str_replace('"','', $params);
				$params = explode(',', $params[3]);
				$params = str_replace('}','', $params[0]); //현재계약일자불러오기

				if($params != "~"){
					$btn .= '<span id=\'contract_report\''.$seq.'\' name=\'contract_report\''.$seq.'\' type="text" onmouseover=\'_svc_contract_dt_get_layer(this'.$para.');\'  style=\'font-weight:bold;\'>'.$params.'</span>';
				}else {
					$btn .= '<span id=\'contract_report\''.$seq.'\' name=\'contract_report\''.$seq.'\' type="text" style=\'font-weight:bold;\'>계약기간없음</span>';
				}
			}else if ($report_id == 'CLTPLAGC' ){ //서비스제공계획서및동의서(방문요양)
				if ($event['pdf']  == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'pdf\'></span><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>출력</button></span> ';
				if ($event['word']  == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'word\'></span><button type=\'button\' onclick=\'_report_show_word("CLTWPLAGC"'.$para.');\'>출력</button></span>';
			}else if ($report_id == 'CLTPLAGB' ){ //서비스제공계획서및동의서(방문목욕)
				if ($event['pdf']  == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'pdf\'></span><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>출력</button></span> ';
				if ($event['word']  == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'word\'></span><button type=\'button\' onclick=\'_report_show_word("CLTWPLAGB"'.$para.');\'>출력</button></span>';
			}else if ($report_id == 'CLTPLAGN' ){ //서비스제공계획서및동의서(방문간호)
				if ($event['pdf']  == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'pdf\'></span><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>출력</button></span> ';
				if ($event['word']  == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'word\'></span><button type=\'button\' onclick=\'_report_show_word("CLTWPLAGN"'.$para.');\'>출력</button></span>';
			}else if ($report_id == 'CLTSUCHC' ){ //수급자 교부서류 확인서
				if ($event['pdf']  == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'pdf\'></span><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>출력</button></span> ';
				if ($event['word']  == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'word\'></span><button type=\'button\' onclick=\'_report_show_word("CLTWSUCHC"'.$para.');\'>출력</button></span>';
			}else if ($report_id == 'SLT'){
				$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>개별</button></span>';
			}else if ($report_id == 'CLTLCC' ||
					  $report_id == 'CLTLCC12'||
					  $report_id == 'CLTLCCNEW'||
					  $report_id == 'CLTLCCNEW2'||
					  $report_id == 'CLTLCB' ||
					  $report_id == 'CLTLCBNEW' ||
					  $report_id == 'CLTLCBNEW2' ||
					  $report_id == 'CLTLCBB'||
					  $report_id == 'CLTLCNN' ||
					  $report_id == 'CLTLCVN' ||
					  $report_id == 'CLTLCNNEW' ||
					  $report_id == 'CLTLCVC'	){

				$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>양식</button></span> ';
				if ($report_id == 'CLTLCCNEW2' ){
					$tm_arr = explode('"cvCnt":',$para);
					$arr = explode('"',$tm_arr[1]);
					if(!empty($arr[1])){
						$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.',"","cv");\'>실적</button></span> ';
					}
				}
			}else if (
					  $report_id == 'CLTLCN'	||
					  $report_id == 'CLTLCVB'	){
				$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>계획</button></span> ';
				$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>실적</button></span> ';
			}else if ($report_id == 'CLTLCMOR'	||
					  $report_id == 'CLTLCBILL'	||
					  /*$report_id == 'CTLRECEIPT'||*/
					  $report_id == 'MCR'  ){
				$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.',"'.$report_id.'");\'>출력</button></span>';
			}else if ($report_id == 'HUREC'  ){
				$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.',"HUREC");\'>출력</button></span>';
			}else if ($report_id == 'CTLRECEIPT'){
				$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'__printPDF("root=expenses&dir=P&fileName=expenses_show_receive&fileType=pdf&target=show.php&showForm=ReceiveBook&code='.$code.'&byGbn=1&year='.substr($yymm,0,4).'&month='.substr($yymm,4,2).'");\'>출력</button></span>';
			}else if($report_id == 'CLTREC'){
				$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_reg("'.$code.'","'.$report_menu.'","'.$report_index.'","'.$yymm.'","'.$seq.'","","'.$year.'","'.$month.'");\'>수정</button></span> ';
				$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(0'.$para.');\'>출력</button></span>';
			}else if ($report_id == 'CLTLCBBB' ){
				if ($event['hwp']   == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'hwp\'></span><button type=\'button\' onclick=\'_report_show_hwp("'.$report_id.'"'.$para.');\'>출력</button></span> ';
			}else if ($report_id == 'CLTLCBREC' ){
				if ($event['hwp']   == 'Y') $btn .= '<span class=\'btn_pack m\'><span class=\'hwp\'></span><button type=\'button\' onclick=\'_report_show_hwp("'.$report_id.'"'.$para.');\'>출력</button></span> ';
			}else{
				if (is_null($event)){
				}else{
					if ($report_id == 'MEMTR'  ||
						$report_id == 'CLTBSR' ||
						$report_id == 'CLTBSR2' ||
						$report_id == 'CLTPST' ||
						$report_id == 'CLTDDT' ){
						$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_reg("'.$code.'","'.$report_menu.'","'.$report_index.'","'.$yymm.'","'.$seq.'","'.$ssn.'","'.$year.'","'.$month.'");\'>수정</button></span> ';
					}else{
						$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_reg("'.$code.'","'.$report_menu.'","'.$report_index.'","'.$yymm.'","'.$seq.'","","'.$year.'","'.$month.'");\'>수정</button></span> ';
					}

					if ($event['pdf'] == 'N' && $event['word'] == 'N' && $event['excel'] == 'N' && $event['hwp'] == 'N'){
						$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>출력</button></span> ';
					}else{
						if ($event['pdf']   == 'Y') $btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>출력</button></span> ';
						if ($event['word']  == 'Y') $btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_word("'.$report_id.'");\'>출력</button></span> ';
						if ($event['excel'] == 'Y') $btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_excel(1);\'>출력</button></span> ';
						if ($event['hwp']   == 'Y') $btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_hwp("'.$report_id.'");\'>출력</button></span> ';
					}

					//2012평가출력
					if(empty($report_menu)) $btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_show_pdf(1'.$para.');\'>출력</button></span> ';
					//$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'alert("준비중입니다.");\'>삭제</button></span>';
					//$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_del("'.$report_id.'"'.$para.');\'>삭제</button></span>';


					$btn .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_report_del("'.$code.'","'.$report_menu.'","'.$report_index.'","'.$yymm.'","'.$seq.'","'.$ssn.'","'.$year.'","'.$month.'");\'>삭제</button></span>';


				}
			}

			return $btn;
		}


		/********************************

			쿼리

		********************************/

		/**********************
		검색 시 변수
		$find_name
		$find_counsel_name
		$find_type
		***********************/

		function query_list($index, $code, $find_name, $find_counsel_name, $find_type, $yymm = '', $ssn = '', $limit = 0, $lbTestMode){


			if ($index == 'CLTLCC'  ||
				$index == 'CLTLCB'  ||
				$index == 'CLTLCBNEW'  ||
				$index == 'CLTLCBNEW2'  ||
				$index == 'CLTLCBB' ||
				$index == 'CLTLCBBB' ||
				$index == 'CLTLCBREC' ||
				$index == 'CLTLCN'  ||
				$index == 'CLTLCNN' ||
				$index == 'CLTLCPLAN' ||
				$index == 'CLTSTATRCD'){

				/******************************************************
				변수 호출 레포트
				#CLTSVCCTC : 장기요양급여제공계약서(방문요양,목욕,간호)
				#CLTLCC    : 장기요양급여제공기록지(방문요양)
				#CLTLCCNEW : 장기요양급여제공기록지(방문요양)
				#CLTLCCNEW2: 장기요양급여제공기록지(방문요양)
				#CLTLCB    : 장기요양급여제공기록지(방문목욕)
				#CLTLCBB   : 장기요양급여제공기록지(방문목욕)
				#CLTLCBBB  : 장기요양급여제공기록지(방문목욕)
				#CLTLCBNEW : 장기요양급여제공기록지(방문목욕)
				#CLTLCBREC : 목욕관찰기록지
				#CLTLCN    : 장기요양급여제공기록지(방문간호)
				#CLTLCNN   : 장기요양급여제공기록지(방문간호)
				#CLTLCNNEW : 장기요양급여제공기록지(방문간호)
				#CLTLCPLAN : 장기요양서비스계획서
				CLTSTATRCD : 상태변화 기록지
				*******************************************************/

				if($lbTestMode){
					$wsl = '  left join client_his_lvl as h_lvl
								on h_lvl.org_no = t01_ccode
							   and h_lvl.svc_cd = t01_mkind
							   and h_lvl.jumin  = t01_jumin
							   and h_lvl.seq = (select max(seq) from client_his_lvl as tmp where tmp.org_no = h_lvl.org_no and tmp.jumin = h_lvl.jumin)
							  left join client_his_kind as kind
								on kind.org_no = t01_ccode
							   and kind.jumin  = t01_jumin
							   and kind.seq = (select max(seq) from client_his_kind as tmp where tmp.org_no = kind.org_no and tmp.jumin = kind.jumin)';

					$lvl = 'h_lvl.level';
					$skind = 'kind.kind';
				}else {
					$wsl = '';
					$lvl = 'm03_ylvl';
					$skind = 'm03_skind';
				}
			}else if($index == 'CLTLCMOR' ||
				     $index == 'CLTLCBILL'){

				/**************************************************
				변수 호출 레포트
				#CLTLCMOR  : 장기요양급여비납부확인서
				#CLTLCBILL : 장기요양급여비용명세서
				***************************************************/

				if($lbTestMode){
					$wsl = '  left join client_his_lvl as h_lvl
								on h_lvl.org_no = t13_ccode
							   and h_lvl.svc_cd = t13_mkind
							   and h_lvl.jumin  = t13_jumin
							   and h_lvl.seq = (select max(seq) from client_his_lvl as tmp where tmp.org_no = h_lvl.org_no and tmp.jumin = h_lvl.jumin)
							  left join client_his_kind as kind
								on kind.org_no = t13_ccode
							   and kind.jumin  = t13_jumin
							   and kind.seq = (select max(seq) from client_his_kind as tmp where tmp.org_no = kind.org_no and tmp.jumin = kind.jumin)';

					$lvl = 'h_lvl.level';
					$skind = 'kind.kind';
					$rate = 'rate';
				}else {
					$wsl = '';
					$lvl = 'm03_ylvl';
					$skind = 'm03_skind';
					$rate = 'm03_bonin_yul';
				}
			}

			switch($index){

				case 'HUREC': //인사기록카드 2012.08.31 추가
					$sql = 'select min(m02_mkind) as kind
							,      m02_yname as m_nm
							,      m02_yjumin as m_cd
							,      m02_ytel as m_tel
							,	   m02_yipsail as yipsail
							  from m02yoyangsa
							 where m02_ccode        = \''.$code.'\'
							   and m02_ygoyong_stat = \'1\'
							   and m02_del_yn       = \'N\'';

					if(!empty($find_name)) $sql .= '  and m02_yname like \'%'.$find_name.'%\'';
					if(!empty($find_type)) $sql .= '  and m02_dept_cd = \''.$find_type.'\'';

					$sql .= 'group by m02_yjumin
							 order by m02_yname';


					break;

				case 'MCR': //요양보호사 변경신고서
					$sql = 'select m32_mkind as kind
							,      m32_a_date as dt
							,      m32_jumin as c_cd
							,      m03_name as c_nm
							,      LVL.m81_name as lvl_nm
							,      YUL.m92_cont as gbn
							,      YUL.m92_bonin_yul as rate
							,      m32_a_jumin as m_cd
							,      m32_a_name as m_nm
							  from m32jikwon
							 inner join m03sugupja
								on m03_ccode = m32_ccode
							   and m03_mkind = m32_mkind
							   and m03_jumin = m32_jumin
							 inner join m81gubun as LVL
								on m81_gbn  = \'LVL\'
							   and m81_code = m03_ylvl
							 inner join m92boninyul as YUL
								on m92_code         = m03_skind
							   and m32_a_date between m92_sdate and m92_edate
							 where m32_ccode     = \''.$code.'\'
							   and m32_a_date like \''.$yymm.'%\'
							 order by m32_a_date, m03_name';
					break;

				case 'QARR': //질향샹 활동 결과 보고서
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      dept.dept_nm as dept_nm
							,      r_reg_id as m_cd
							,      r_reg_nm as m_nm
							,      r_business as b_nm
							  from r_qarr
							 inner join m02yoyangsa
							    on m02_ccode  = org_no
							   and m02_mkind  = '.$this->_member_kind().'
							   and m02_yjumin = r_reg_id
							 inner join dept
							    on dept.org_no  = r_qarr.org_no
							   and dept.dept_cd = r_qarr.r_dept_cd
							 where r_qarr.org_no   = \''.$code.'\'
							   and r_qarr.r_yymm   = \''.$yymm.'\'
							   and r_qarr.del_flag = \'N\'
							 order by r_qarr.r_reg_dt';
					break;

				case 'QI': //질향샹 활동 결과 보고서
					/*
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      dept.dept_nm as dept_nm
							,      r_reg_id as m_cd
							,      r_reg_nm as m_nm
							,      r_business as b_nm
							  from r_qarr
							 inner join m02yoyangsa
							    on m02_ccode  = org_no
							   and m02_mkind  = '.$this->_member_kind().'
							   and m02_yjumin = r_reg_id
							 inner join dept
							    on dept.org_no  = r_qarr.org_no
							   and dept.dept_cd = r_qarr.r_dept_cd
							 where r_qarr.org_no   = \''.$code.'\'
							   and r_qarr.r_yymm   = \''.$yymm.'\'
							   and r_qarr.del_flag = \'N\'
							 order by r_qarr.r_reg_dt';
					*/

					break;

				case 'WR60U': //근로계약서(월60시간이상근로 대상)
					$sql = 'select min(m02_mkind) as kind
							,      m02_yname as m_nm
							,      m02_yjumin as m_cd
							,      m02_ytel as m_tel
							,	   m02_yipsail as yipsail
							  from m02yoyangsa
							 where m02_ccode        = \''.$code.'\'
							   and m02_ygoyong_stat = \'1\'
							   and m02_ygoyong_kind in (\'2\', \'3\')
							   and m02_del_yn       = \'N\'';

					if(!empty($find_name)) $sql .= '  and m02_yname like \'%'.$find_name.'%\'';
					if(!empty($find_type)) $sql .= '  and m02_dept_cd = \''.$find_type.'\'';

					$sql .= 'group by m02_yjumin
							 order by m02_yname';


					break;

				case 'WR60D': //근로계약서(월60시간이상근로 대상)
					$sql = 'select min(m02_mkind) as kind
							,      m02_yname as m_nm
							,      m02_yjumin as m_cd
							,      m02_ytel as m_tel
							,	   m02_yipsail as yipsail
							  from m02yoyangsa
							 where m02_ccode        = \''.$code.'\'
							   and m02_ygoyong_stat = \'1\'
							   and m02_ygoyong_kind = \'4\'
							   and m02_del_yn       = \'N\'';

					if(!empty($find_name)) $sql .= '  and m02_yname like \'%'.$find_name.'%\'';
					if(!empty($find_type)) $sql .= '  and m02_dept_cd = \''.$find_type.'\'';

					$sql .= 'group by m02_yjumin
							 order by m02_yname';
					break;

				case 'WR60M': //근로계약서(월급제근로자 대상)
					$sql = 'select min(m02_mkind) as kind
							,      m02_yname as m_nm
							,      m02_yjumin as m_cd
							,      m02_ytel as m_tel
							,	   m02_yipsail as yipsail
							  from m02yoyangsa
							 where m02_ccode        = \''.$code.'\'
							   and m02_ygoyong_stat = \'1\'
							   and m02_ygoyong_kind = \'1\'
							   and m02_del_yn       = \'N\'';

					if(!empty($find_name)) $sql .= '  and m02_yname like \'%'.$find_name.'%\'';
					if(!empty($find_type)) $sql .= '  and m02_dept_cd = \''.$find_type.'\'';

					$sql .= 'group by m02_yjumin
							 order by m02_yname';
					break;

				case 'WRT': //근무현황표
					$sql = 'select t.kind
							,      t.m_cd
							,      t.m_nm
							,      t.c_cd
							,	   m03_name as c_nm
							,      CASE WHEN lvl.svc_cd = \'0\' OR lvl.svc_cd = \'4\' THEN
								   CASE WHEN lvl.lvl = \'9\' THEN \'일반\' ELSE CONCAT(lvl.lvl,\'등급\') END
									ElSE \'\' END AS l_nm
							,      t.m01 as \'1\'
							,      t.m02 as \'2\'
							,      t.m03 as \'3\'
							,      t.m04 as \'4\'
							,      t.m05 as \'5\'
							,      t.m06 as \'6\'
							,      t.m07 as \'7\'
							,      t.m08 as \'8\'
							,      t.m09 as \'9\'
							,      t.m10 as \'10\'
							,      t.m11 as \'11\'
							,      t.m12 as \'12\'
							from (
								select kind
									,	   m_cd
									,	   m_nm
									,	   c_cd
									,      sum(case substring(yymm, 5, 2) when \'01\' then 1 else 0 end) as m01
									,      sum(case substring(yymm, 5, 2) when \'02\' then 1 else 0 end) as m02
									,      sum(case substring(yymm, 5, 2) when \'03\' then 1 else 0 end) as m03
									,      sum(case substring(yymm, 5, 2) when \'04\' then 1 else 0 end) as m04
									,      sum(case substring(yymm, 5, 2) when \'05\' then 1 else 0 end) as m05
									,      sum(case substring(yymm, 5, 2) when \'06\' then 1 else 0 end) as m06
									,      sum(case substring(yymm, 5, 2) when \'07\' then 1 else 0 end) as m07
									,      sum(case substring(yymm, 5, 2) when \'08\' then 1 else 0 end) as m08
									,      sum(case substring(yymm, 5, 2) when \'09\' then 1 else 0 end) as m09
									,      sum(case substring(yymm, 5, 2) when \'10\' then 1 else 0 end) as m10
									,      sum(case substring(yymm, 5, 2) when \'11\' then 1 else 0 end) as m11
									,      sum(case substring(yymm, 5, 2) when \'12\' then 1 else 0 end) as m12
									from ( SELECT t01_mem_cd1 AS m_cd, t01_mem_nm1 AS m_nm, t01_jumin AS c_cd, t01_mkind AS kind
											, LEFT(t01_sugup_date,6) AS yymm
											FROM t01iljung
											WHERE t01_ccode = \''.$code.'\'
											AND t01_mkind = \'0\'
											AND LEFT(t01_sugup_date,4) = \''.SubStr($yymm,0,4).'\'
											AND t01_del_yn = \'N\' AND t01_svc_subcode = \'200\' ) AS caln
									GROUP BY m_cd, c_cd, kind
								UNION ALL
								SELECT	   kind
									,	   m_cd
									,	   m_nm
									,	   c_cd
									,      sum(case substring(yymm, 5, 2) when \'01\' then 1 else 0 end) as m01
									,      sum(case substring(yymm, 5, 2) when \'02\' then 1 else 0 end) as m02
									,      sum(case substring(yymm, 5, 2) when \'03\' then 1 else 0 end) as m03
									,      sum(case substring(yymm, 5, 2) when \'04\' then 1 else 0 end) as m04
									,      sum(case substring(yymm, 5, 2) when \'05\' then 1 else 0 end) as m05
									,      sum(case substring(yymm, 5, 2) when \'06\' then 1 else 0 end) as m06
									,      sum(case substring(yymm, 5, 2) when \'07\' then 1 else 0 end) as m07
									,      sum(case substring(yymm, 5, 2) when \'08\' then 1 else 0 end) as m08
									,      sum(case substring(yymm, 5, 2) when \'09\' then 1 else 0 end) as m09
									,      sum(case substring(yymm, 5, 2) when \'10\' then 1 else 0 end) as m10
									,      sum(case substring(yymm, 5, 2) when \'11\' then 1 else 0 end) as m11
									,      sum(case substring(yymm, 5, 2) when \'12\' then 1 else 0 end) as m12
									from ( SELECT t01_mem_cd1 AS m_cd, t01_mem_nm1 AS m_nm, t01_jumin AS c_cd, t01_mkind AS kind
											, LEFT(t01_sugup_date,6) AS yymm
											FROM t01iljung
											WHERE t01_ccode = \''.$code.'\'
											AND t01_mkind = \'0\'
											AND LEFT(t01_sugup_date,4) = \''.SubStr($yymm,0,4).'\'
											AND t01_del_yn = \'N\' AND t01_svc_subcode = \'500\'
											UNION ALL
											SELECT t01_mem_cd2 AS m_cd, t01_mem_nm2 AS m_nm, t01_jumin AS c_cd, t01_mkind AS kind
											, LEFT(t01_sugup_date,6) AS yymm
											FROM t01iljung
											WHERE t01_ccode = \''.$code.'\'
											AND t01_mkind = \'0\'
											AND LEFT(t01_sugup_date,4) = \''.SubStr($yymm,0,4).'\'
											AND t01_del_yn = \'N\' AND t01_svc_subcode = \'500\' ) AS caln
									GROUP BY m_cd, c_cd, kind
								UNION ALL
								SELECT	   kind
									,	   m_cd
									,	   m_nm
									,	   c_cd
									,      sum(case substring(yymm, 5, 2) when \'01\' then 1 else 0 end) as m01
									,      sum(case substring(yymm, 5, 2) when \'02\' then 1 else 0 end) as m02
									,      sum(case substring(yymm, 5, 2) when \'03\' then 1 else 0 end) as m03
									,      sum(case substring(yymm, 5, 2) when \'04\' then 1 else 0 end) as m04
									,      sum(case substring(yymm, 5, 2) when \'05\' then 1 else 0 end) as m05
									,      sum(case substring(yymm, 5, 2) when \'06\' then 1 else 0 end) as m06
									,      sum(case substring(yymm, 5, 2) when \'07\' then 1 else 0 end) as m07
									,      sum(case substring(yymm, 5, 2) when \'08\' then 1 else 0 end) as m08
									,      sum(case substring(yymm, 5, 2) when \'09\' then 1 else 0 end) as m09
									,      sum(case substring(yymm, 5, 2) when \'10\' then 1 else 0 end) as m10
									,      sum(case substring(yymm, 5, 2) when \'11\' then 1 else 0 end) as m11
									,      sum(case substring(yymm, 5, 2) when \'12\' then 1 else 0 end) as m12
									from ( SELECT t01_mem_cd1 AS m_cd, t01_mem_nm1 AS m_nm, t01_jumin AS c_cd, t01_mkind AS kind
											, LEFT(t01_sugup_date,6) AS yymm
											FROM t01iljung
											WHERE t01_ccode = \''.$code.'\'
											AND t01_mkind = \'0\'
											AND LEFT(t01_sugup_date,4) = \''.SubStr($yymm,0,4).'\'
											AND t01_del_yn = \'N\' AND t01_svc_subcode = \'800\' ) AS caln
									GROUP BY m_cd, c_cd, kind
								) AS t
								INNER JOIN m03sugupja
								        ON m03_ccode = \''.$code.'\'
									   AND m03_jumin = t.c_cd
									   AND m03_mkind = t.kind
								LEFT JOIN (
								   SELECT jumin
								   ,      svc_cd
								   ,      MIN(level) AS lvl
									 FROM client_his_lvl
									WHERE org_no           = \''.$code.'\'
									  AND LEFT(from_dt,4) <= \''.SubStr($yymm,0,4).'\'
									  AND LEFT(to_dt,4)   >= \''.SubStr($yymm,0,4).'\'
									GROUP BY jumin, svc_cd
								   ) AS lvl
								ON lvl.jumin = t.c_cd';

					break;

				case 'SLT': //급여대장
					$sql = 'select distinct
								   salary_jumin as m_cd
							,      m02_yname as m_nm
							,      m02_ytel as m_tel
							,      m02_yipsail as join_dt
							,      m02_ytoisail as quit_dt
							  from salary_basic
							 inner join m02yoyangsa
								on m02_ccode  = salary_basic.org_no
							   and m02_yjumin = salary_jumin
							   and m02_mkind  = '.$this->conn->_member_kind().'
							 where org_no               = \''.$code.'\'
							   and left(salary_yymm, 4) = \''.substr($yymm,0,4).'\'
							 order by m_nm';
					break;

				case 'WTOTO': //업무인계.인수서
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_turn_nm as turn_nm
							,      r_take_nm as take_nm
							,      r_c_nm as c_nm
							  from r_wtoto
							 where org_no   = \''.$code.'\'';
					if(substr($yymm,4,2) == 00){
						$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
					}else {
						$sql .= 'and r_yymm   = \''.$yymm.'\'';
					}
						$sql .=	'and del_flag = \'N\'
							  order by dt';
					break;

				case 'MONMR': //월례회의록
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_mc_nm as m_nm
							,      r_place as place
							  from r_monmr
							 where org_no   = \''.$code.'\'';
					if(substr($yymm,4,2) == 00){
						$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
					}else {
						$sql .= 'and r_yymm   = \''.$yymm.'\'';
					}
						$sql .=	'and del_flag = \'N\'
							  order by dt';

					break;

				case 'MEMTR':
					$sql = 'select stress_dt as dt
							,      stress_ssn as m_cd
							,      stress_seq as seq
							,      m02_yname as m_nm
							,      stress_talker_nm as r_nm
							,      case stress_type when \'1\' then \'내방\'
													when \'2\' then \'방문\'
													when \'3\' then \'전화\' else \'\' end as r_type
							,      stress_result as r_str
							  from counsel_stress
							  left join m02yoyangsa
								on m02_ccode  = org_no
							   and m02_yjumin = stress_ssn
							   and m02_mkind  = '.$this->_member_kind().'
							 where org_no     = \''.$code.'\'';

					if (!empty($find_name)) $sql .= ' and m02_yname like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and stress_talker_nm like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and stress_type  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .= ' and date_format(stress_dt,\'%Y\') = \''.substr($yymm,0,4).'\'';
						}else {
							$sql .= ' and date_format(stress_dt,\'%Y%m\') = \''.$yymm.'\'';
						}


					if (!empty($ssn))
						$sql .= ' and stress_ssn = \''.$ssn.'\'';

					if ($limit > 0)
						$sql .= ' limit '.$limit;


					break;

				case 'MEMJAS':
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_m_nm as m_nm
							,      r_part as part
							,      r_m_job as j_nm
							,      r_from_dt as form_dt
							,      r_to_dt as to_dt
							  from r_memjas
							  left join m02yoyangsa
								on m02_ccode  = org_no
							   and m02_mkind  = '.$this->_member_kind().'
							   and m02_yjumin = r_m_id
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_m_nm like \'%'.$find_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m02_dept_cd = \''.$find_type.'\'';

					if(substr($yymm,4,2) == 00){
						$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
					}else {
						$sql .= 'and r_yymm   = \''.$yymm.'\'';
					}

					if (!empty($ssn))
						$sql .= ' and r_m_id = \''.$ssn.'\'';

					$sql .= ' order by dt';

					if ($limit > 0)
						$sql .= ' limit '.$limit;

					break;

				case 'MEMTAKE':	//인수인계서
					if (SubStr($yymm, 4, 2) == '00'){
						$yymm = SubStr($yymm, 0 ,4);
					}
					$sql = 'SELECT report.yymm
							,      report.seq
							,      report.dt
							,      report.m_nm
							,      report.s_nm
							,      report.t_nm
							,      report.jumin
							  FROM (
								   SELECT r_yymm AS yymm
								   ,      r_seq AS seq
								   ,      r_t_dt AS dt
								   ,      r_h_nm AS m_nm
								   ,      r_s_nm AS s_nm
								   ,      r_t_nm AS t_nm
								   ,      r_h_ssn AS jumin
									 FROM r_memtake
									WHERE org_no = \''.$code.'\'
									  AND LEFT(r_yymm,'.StrLen($yymm).') = \''.$yymm.'\'
									  AND del_flag = \'N\'
								   ) AS report
							  LEFT JOIN (
								   SELECT m02_yjumin AS jumin
								   ,      m02_dept_cd AS dept
									 FROM m02yoyangsa
									WHERE m02_ccode = \''.$code.'\'
									GROUP BY m02_ccode, m02_yjumin, m02_dept_cd
								   ) AS mst
								ON mst.jumin = report.jumin
							 WHERE report.jumin != \'\'';

					if (!Empty($find_name)){
						$sql .= ' AND report.m_nm >= \''.$find_name.'\'';
					}
					if (!Empty($find_type)){
						$sql .= ' AND mst.dept = \''.$find_type.'\'';
					}
					if (!Empty($ssn)){
						$sql .= ' AND report.jumin = \''.$ssn.'\'';
					}

					$sql .= ' ORDER BY dt';

					if ($limit > 0)
						$sql .= ' limit '.$limit;

					break;

				case 'MEMEDU': //교육결과보고

					$sql = 'select r_yymm as yymm
							,	   r_date as dt
							,      r_time as time
							,      r_edu as edu
							,      r_place as place
							,      r_teacher as teacher
							,      r_subject as subject
							,	   r_seq as seq
							  from r_memedu
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if(substr($yymm,4,2) == 00){
						$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
					}else {
						$sql .= 'and r_yymm   = \''.$yymm.'\'';
					}

					$sql .= ' order by r_date, r_time';

					if ($limit > 0)
						$sql .= ' limit '.$limit;

					break;

				case 'CLTBSR':
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_m_nm as m_nm
							,      r_c_id as c_cd
							,      r_c_nm as c_nm
							,      r_c_phone as c_phone
							,      r_p_name as p_nm
							  from r_cltbsr
							  left join m03sugupja
							    on m03_ccode = org_no
							   and m03_jumin = r_c_id
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_m_nm like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_c_nm like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= 'and r_yymm   = \''.$yymm.'\'';
						}

					if (!empty($ssn))
						$sql .= ' and r_c_id = \''.$ssn.'\'';

					$sql .= ' group by r_c_nm, r_c_id, r_reg_dt
							  order by dt';

					if ($limit > 0)
						$sql .= ' limit '.$limit;

					break;

				case 'CLTBSR2':
					$sql = 'select left(replace(reg_dt, \'-\',\'\'), 6) as yymm
							,      seq as seq
							,      reg_dt as dt
							  from report_na
							  left join m03sugupja
							    on m03_ccode = org_no
							   and m03_jumin = jumin
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and m03_name like \'%'.$find_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	'and  left(replace(reg_dt, \'-\',\'\'), 6)  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= 'and  left(replace(reg_dt, \'-\',\'\'), 6)   = \''.$yymm.'\'';
						}

					if (!empty($ssn))
						$sql .= ' and jumin = \''.$ssn.'\'';

					$sql .= ' group by m03_name, reg_dt
							  order by dt';

					if ($limit > 0)
						$sql .= ' limit '.$limit;

					break;

				case 'CLTPST':
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_c_id as c_cd
							,      r_c_nm as c_nm
							,      r_m_nm as m_nm
							,      r_point as point
							  from r_cltpst
							  left join m03sugupja
							    on m03_ccode = org_no
							   and m03_mkind = '.$this->conn->_client_kind().'
							   and m03_jumin = r_c_id
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_m_nm like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_c_nm like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= 'and r_yymm   = \''.$yymm.'\'';
						}

					if (!empty($ssn))
						$sql .= ' and r_c_id = \''.$ssn.'\'';

					$sql .= ' order by dt';

					if ($limit > 0)
						$sql .= ' limit '.$limit;

					break;

				case 'CLTDDT':
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_c_id as c_cd
							,      r_c_nm as c_nm
							,      r_m_nm as m_nm
							,      r_point as point
							  from r_cltddt
							  left join m03sugupja
							    on m03_ccode = org_no
							   and m03_mkind = '.$this->conn->_client_kind().'
							   and m03_jumin = r_c_id
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_m_nm like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_c_nm like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= 'and r_yymm   = \''.$yymm.'\'';
						}

					if (!empty($ssn))
						$sql .= ' and r_c_id = \''.$ssn.'\'';

					$sql .= ' order by dt';

					if ($limit > 0)
						$sql .= ' limit '.$limit;

					break;

				case 'CLTMRRC':
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_c_id as c_cd
							,      r_c_nm as c_nm
							,      r_m_nm as m_nm
							  from r_cltmrrc
							  left join ( select m03_jumin, m03_mkind
										  from m03sugupja
										 where m03_ccode = \''.$code.'\'
										 group by m03_jumin
									 ) as mst
							    on mst.m03_jumin = r_c_id
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_m_nm like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_c_nm like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and mst.m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= 'and r_yymm   = \''.$yymm.'\'';
						}

						$sql .= ' order by dt';

					break;

				case 'CLTMRRB':
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_c_id as c_cd
							,      r_c_nm as c_nm
							,      r_m_nm as m_nm
							  from r_cltmrrc
							  left join ( select m03_jumin, m03_mkind
										  from m03sugupja
										 where m03_ccode = \''.$code.'\'
										 group by m03_jumin
									 ) as mst
							    on mst.m03_jumin = r_c_id
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_m_nm like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_c_nm like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and mst.m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= 'and r_yymm   = \''.$yymm.'\'';
						}

						$sql .= ' order by dt';

					break;

				case 'CLTLCC':

					//if($_SESSION['userLevel'] == 'P') $wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,	   iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_jumin AS jumin
							,	   t01_mkind as kind
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'200\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name';

					break;

				case 'CLTLCCNEW':

					//if($_SESSION['userLevel'] == 'P') $wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,	   iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_jumin AS jumin
							,	   t01_mkind as kind
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'200\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   AND LEFT(t01_sugup_date, 6) >= \'201407\'
							   '.$wsl2.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name ';

					break;

				case 'CLTLCCNEW2':

					//if($_SESSION['userLevel'] == 'P') $wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,	   iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,	   lvl.app_no
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_jumin AS jumin
							,	   t01_mkind as kind
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'200\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   AND LEFT(t01_sugup_date, 6) >= \'201407\'
							   '.$wsl2.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
								   ,	  app_no
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name ';

					break;

				case 'CLTLCB':

					/*
					if($_SESSION['userLevel'] == 'P'){
						$wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';
						$wsl3 = ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';
					}
					*/

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,      iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
							 union all
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl3.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name';

					break;

				case 'CLTLCBNEW':

					/*
					if($_SESSION['userLevel'] == 'P'){
						$wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';
						$wsl3 = ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';
					}
					*/

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,      iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
							 union all
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl3.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name';

					break;
				
				case 'CLTLCBNEW2':

					/*
					if($_SESSION['userLevel'] == 'P'){
						$wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';
						$wsl3 = ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';
					}
					*/

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,      iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
							 union all
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl3.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name';

					break;

				case 'CLTLCBB':

					/*
					if($_SESSION['userLevel'] == 'P'){
						$wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';
						$wsl3 = ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';
					}
					*/


					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,      iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
							 union all
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl3.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name';

					break;

				case 'CLTLCBBB':

					/*
					if($_SESSION['userLevel'] == 'P'){
						$wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';
						$wsl3 = ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';
					}
					*/

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,      iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
							 union all
							SELECT t01_mkind AS kind
							,	   t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl3.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name';

					break;

				case 'CLTLCBREC':

					/*
					if($_SESSION['userLevel'] == 'P'){
						$wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';
						$wsl3 = ' and t01_yoyangsa_id2 = \''.$_SESSION['userSSN'].'\'';
					}
					*/

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,	   iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_jumin AS jumin
							,	   t01_mkind as kind
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
							 union all
							SELECT t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'500\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl3.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name';

					break;

				case 'CLTLCN':

					//if($_SESSION['userLevel'] == 'P') $wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,	   iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_jumin AS jumin
							,	   t01_mkind as kind
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'800\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
							 union all
							SELECT t01_jumin AS jumin
							,	   t01_mkind as kind
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'800\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl3.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name';

					break;

				case 'CLTLCNN':

					//if($_SESSION['userLevel'] == 'P') $wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,	   iljung.kind
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							,	   left(t01_sugup_date, 6) as dt
							  FROM (
							SELECT t01_jumin AS jumin
							,	   t01_mkind as kind
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'800\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
							 union all
							SELECT t01_jumin AS jumin
							,	   t01_sugup_date
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_svc_subcode = \'800\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl3.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
									ORDER BY seq desc
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 GROUP BY iljung.jumin
							 ORDER BY name';

					break;

				case 'CLTLCNNEW':

				//if($_SESSION['userLevel'] == 'P') $wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

				$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
						,	   iljung.kind
						,      mst.name AS c_nm
						,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
						,      CASE kind.kind WHEN \'1\' THEN \'일반\'
											  WHEN \'2\' THEN \'의료\'
											  WHEN \'3\' THEN \'기초\'
											  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
						,      mst.tel AS c_tel
						,	   left(t01_sugup_date, 6) as dt
						  FROM (
						SELECT t01_jumin AS jumin
						,	   t01_mkind as kind
						,	   t01_sugup_date
						  FROM t01iljung
						 WHERE t01_ccode = \''.$code.'\'
						   AND t01_mkind = \'0\'
						   AND t01_svc_subcode = \'800\'
						   AND t01_del_yn = \'N\'
						   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
						   '.$wsl2.'
						 union all
						SELECT t01_jumin AS jumin
						,	   t01_mkind as kind
						,	   t01_sugup_date
						  FROM t01iljung
						 WHERE t01_ccode = \''.$code.'\'
						   AND t01_mkind = \'0\'
						   AND t01_svc_subcode = \'800\'
						   AND t01_del_yn = \'N\'
						   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
						   AND LEFT(t01_sugup_date, 6) >= \'201407\'
						   '.$wsl3.'
							   ) AS iljung
						 INNER JOIN (
							   SELECT m03_jumin AS jumin
							   ,      m03_name AS name
							   ,      m03_tel AS tel
								 FROM m03sugupja
								WHERE m03_ccode = \''.$code.'\'
								  AND m03_mkind = \'0\'
							   ) AS mst
							ON mst.jumin = iljung.jumin
						  LEFT JOIN (
							   SELECT jumin
							   ,      level
								 FROM client_his_lvl
								WHERE org_no = \''.$code.'\'
								  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
								  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
								ORDER BY seq desc
							   ) AS lvl
							ON lvl.jumin = iljung.jumin
						  LEFT JOIN (
							   SELECT jumin
							   ,      kind
								 FROM client_his_kind
								WHERE org_no = \''.$code.'\'
								  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
								  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
								ORDER BY seq desc
							   ) AS kind
							ON kind.jumin = iljung.jumin
						 GROUP BY iljung.jumin
						 ORDER BY name';

				break;

				case 'CLTLCMOR': //장기요양급여 납부확인서

					if($lbTestMode){
						$sql = 'select k_cd, c_cd, c_nm, l_nm, s_nm, r_nm
							,      sum(t_amt) as t_amt
							,      sum(p_amt) as p_amt
							,      sum(m_amt) as m_amt
							  from (
								   select t13_mkind as k_cd
								   ,      t13_jumin as c_cd
								   ,      m03_name as c_nm
								   ,	  case h_lvl.level when \'9\' then \'일반\' else concat(h_lvl.level,\'등급\') end as l_nm
								   ,      case m03_mkind when \'0\' then stp.m81_name else \'\' end as s_nm
								   ,      case m03_mkind when \'0\' then '.$rate.' else \'\' end as r_nm
								   ,      t13_suga_tot4 as t_amt
								   ,      t13_chung_amt4 as p_amt
								   ,      t13_bonbu_tot4 as m_amt
									 from t13sugupja
									inner join m03sugupja
									   on m03_ccode = t13_ccode
									  and m03_mkind = t13_mkind
									  and m03_jumin = t13_jumin
									  '.$wsl.'
									 left join m81gubun as lvl
									   on lvl.m81_gbn  = \'LVL\'
									  and lvl.m81_code = '.$lvl.'
									 left join m81gubun as stp
									   on stp.m81_gbn  = \'STP\'
									  and stp.m81_code = '.$skind.'
									where t13_ccode             = \''.$code.'\'
									  and t13_type              = \'2\'
									  and left(t13_pay_date, 4) = \''.substr($yymm,0,4).'\'
								   ) as t
							 group by k_cd, c_cd, c_nm, l_nm, s_nm, r_nm
							 order by c_nm, k_cd';
					}else {
						$sql = 'select k_cd, c_cd, c_nm, l_nm, s_nm, r_nm
								,      sum(t_amt) as t_amt
								,      sum(p_amt) as p_amt
								,      sum(m_amt) as m_amt
								  from (
									   select t13_mkind as k_cd
									   ,      t13_jumin as c_cd
									   ,      m03_name as c_nm
									   ,      case m03_mkind when \'0\' then lvl.m81_name
															 when \'4\' then concat(m03_ylvl, \'등급\') else \'\' end as l_nm
									   ,      case m03_mkind when \'0\' then stp.m81_name else \'\' end as s_nm
									   ,      case m03_mkind when \'0\' then '.$rate.' else \'\' end as r_nm
									   ,      t13_suga_tot4 as t_amt
									   ,      t13_chung_amt4 as p_amt
									   ,      t13_bonbu_tot4 as m_amt
										 from t13sugupja
										inner join m03sugupja
										   on m03_ccode = t13_ccode
										  and m03_mkind = t13_mkind
										  and m03_jumin = t13_jumin
										  '.$wsl.'
										 left join m81gubun as lvl
										   on lvl.m81_gbn  = \'LVL\'
										  and lvl.m81_code = '.$lvl.'
										 left join m81gubun as stp
										   on stp.m81_gbn  = \'STP\'
										  and stp.m81_code = '.$skind.'
										where t13_ccode             = \''.$code.'\'
										  and t13_type              = \'2\'
										  and left(t13_pay_date, 4) = \''.substr($yymm,0,4).'\'
									   ) as t
								 group by k_cd, c_cd, c_nm, l_nm, s_nm, r_nm
								 order by c_nm, k_cd';
					}

					break;

				case 'CLTLCBILL':	//장기요양급여비용명세서
					if($lbTestMode){

						$sql = 'select k_cd, c_cd, c_key, c_nm, l_nm, s_nm, r_nm
								,      sum(t_amt) as t_amt
								,      sum(p_amt) as p_amt
								,      sum(m_amt) as m_amt
								  from (
									   select t13_mkind as k_cd
									   ,      t13_jumin as c_cd
									   ,      m03_key as c_key
									   ,      m03_name as c_nm
									   ,	  case h_lvl.level when \'9\' then \'일반\' else concat(h_lvl.level,\'등급\') end as l_nm
									   ,      case m03_mkind when \'0\' then stp.m81_name else \'\' end as s_nm
									   ,      case m03_mkind when \'0\' then m03_bonin_yul else \'\' end as r_nm
									   ,      t13_suga_tot4 as t_amt
									   ,      t13_chung_amt4 as p_amt
									   ,      t13_bonbu_tot4 as m_amt
										 from t13sugupja
										inner join m03sugupja
										   on m03_ccode = t13_ccode
										  and m03_mkind = t13_mkind
										  and m03_jumin = t13_jumin
										  '.$wsl.'
										 left join m81gubun as lvl
										   on lvl.m81_gbn  = \'LVL\'
										  and lvl.m81_code = '.$lvl.'
										 left join m81gubun as stp
										   on stp.m81_gbn  = \'STP\'
										  and stp.m81_code = '.$skind.'
										where t13_ccode    = \''.$code.'\'
										  and t13_type     = \'2\'
										  and t13_pay_date = \''.$yymm.'\'';

					}else {
						$sql = 'select k_cd, c_cd, c_key, c_nm, l_nm, s_nm, r_nm
								,      sum(t_amt) as t_amt
								,      sum(p_amt) as p_amt
								,      sum(m_amt) as m_amt
								  from (
									   select t13_mkind as k_cd
									   ,      t13_jumin as c_cd
									   ,      m03_key as c_key
									   ,      m03_name as c_nm
									   ,      case m03_mkind when \'0\' then lvl.m81_name
															 when \'4\' then concat(m03_ylvl, \'등급\') else \'\' end as l_nm
									   ,      case m03_mkind when \'0\' then stp.m81_name else \'\' end as s_nm
									   ,      case m03_mkind when \'0\' then m03_bonin_yul else \'\' end as r_nm
									   ,      t13_suga_tot4 as t_amt
									   ,      t13_chung_amt4 as p_amt
									   ,      t13_bonbu_tot4 as m_amt
										 from t13sugupja
										inner join m03sugupja
										   on m03_ccode = t13_ccode
										  and m03_mkind = t13_mkind
										  and m03_jumin = t13_jumin
										  '.$wsl.'
										 left join m81gubun as lvl
										   on lvl.m81_gbn  = \'LVL\'
										  and lvl.m81_code = '.$lvl.'
										 left join m81gubun as stp
										   on stp.m81_gbn  = \'STP\'
										  and stp.m81_code = '.$skind.'
										where t13_ccode    = \''.$code.'\'
										  and t13_type     = \'2\'
										  and t13_pay_date = \''.$yymm.'\'';
					}

					if (!empty($find_counsel_name)) $sql .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					$sql .=	'	) as t
							 group by k_cd, c_cd, c_key, c_nm, l_nm, s_nm, r_nm
							 order by c_nm, k_cd';
					break;

				case 'CTLRECEIPT':
					$sql = 'select substring(t13_pay_date, 5, 2) as mm
							,      sum(t13_suga_tot4) as t_amt
							,      sum(t13_chung_amt4) as p_amt
							,      sum(t13_bonbu_tot4) as m_amt
							  from t13sugupja
							 where t13_ccode             = \''.$code.'\'
							   and t13_type              = \'2\'
							   and left(t13_pay_date, 4) = \''.substr($yymm,0,4).'\'
							 group by substring(t13_pay_date, 5, 2)
							 order by mm';
					break;

				case 'CLTLCPLAN':	//장기요양서비스계획서

					$sql = 'select t01_jumin as c_cd
							,      m03_name as c_nm
							,      lvl.m81_name as l_nm
							,      stp.m81_name as s_nm
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'01\' then 1 else 0 end) as \'1\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'02\' then 1 else 0 end) as \'2\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'03\' then 1 else 0 end) as \'3\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'04\' then 1 else 0 end) as \'4\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'05\' then 1 else 0 end) as \'5\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'06\' then 1 else 0 end) as \'6\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'07\' then 1 else 0 end) as \'7\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'08\' then 1 else 0 end) as \'8\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'09\' then 1 else 0 end) as \'9\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'10\' then 1 else 0 end) as \'10\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'11\' then 1 else 0 end) as \'11\'
							,      sum(case when substring(t01_sugup_date, 5, 2) = \'12\' then 1 else 0 end) as \'12\'
							  from t01iljung
							 inner join m03sugupja
								on m03_ccode = t01_ccode
							   and m03_mkind = t01_mkind
							   and m03_jumin = t01_jumin
							   '.$wsl.'
							  left join m81gubun as lvl
								on lvl.m81_gbn			   = \'LVL\'
							   and lvl.m81_code			   = '.$lvl.'
							  left join m81gubun as stp
								on stp.m81_gbn			   = \'STP\'
							   and stp.m81_code			   = '.$skind.'
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'0\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 4) = \''.substr($yymm,0,4).'\'
							 group by t01_jumin
							 order by c_nm';

					break;

				case 'CLTLCVB':
					$sql = 'select t01_mkind as kind
							,      t01_jumin as c_cd
							,      m03_name as c_nm
							,      case when m03_hp then m03_hp else m03_tel end as c_tel
							  from t01iljung
							 inner join m03sugupja
								on m03_ccode = t01_ccode
							   and m03_mkind = t01_mkind
							   and m03_jumin = t01_jumin
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'3\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 6) = \''.$yymm.'\'
							 group by t01_jumin, m03_name, m03_tel
							 order by c_nm';
					break;

				case 'CLTLCVC':
					$sql = 'select t01_mkind as kind
							,      t01_jumin as c_cd
							,      m03_name as c_nm
							,      case when m03_hp then m03_hp else m03_tel end as c_tel
							,	   left(t01_sugup_date, 6) as dt
							  from t01iljung
							 inner join m03sugupja
								on m03_ccode = t01_ccode
							   and m03_mkind = t01_mkind
							   and m03_jumin = t01_jumin
							 where t01_ccode               = \''.$code.'\'
							   and t01_mkind               = \'4\'
							   and t01_del_yn              = \'N\'
							   and left(t01_sugup_date, 6) = \''.$yymm.'\'
							 group by t01_jumin, m03_name, m03_tel
							 order by c_nm';
					break;

				case 'CLTLCVN':
					$sql = 'select kind, c_cd, m03_name as c_nm, m03_tel as c_tel
							  from (
								   select distinct
								          t01_ccode as k_cd
								   ,      t01_mkind as kind
								   ,      t01_jumin as c_cd
									 from t01iljung
									where t01_ccode               = \''.$code.'\'
									  and t01_mkind               = \'1\'
									  and t01_del_yn              = \'N\'
									  and left(t01_sugup_date, 6) = \''.$yymm.'\'
									union all
								   select distinct
								          t01_ccode as k_cd
								   ,      t01_mkind as kind
								   ,      t01_jumin as c_cd
									 from t01iljung
									where t01_ccode               = \''.$code.'\'
									  and t01_mkind               = \'2\'
									  and t01_del_yn              = \'N\'
									  and left(t01_sugup_date, 6) = \''.$yymm.'\'
								   ) as t
							 inner join m03sugupja
								on m03_ccode = k_cd
							   and m03_mkind = kind
							   and m03_jumin = c_cd';

					if (!empty($find_counsel_name)) $sql .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					$sql .=	' order by c_nm, kind';

					break;

				case 'CLTREC': //장기요양서비스종료 및 연계기록표

					if($lbTestMode){
						$sql = "select r_yymm as yymm
								,	   r_date as dt
								,      r_sugupja as c_cd
								,      m03_name as c_nm
								,      r_injung_no as no
								,      r_injung_from as dateFrom
								,      r_injung_to as dateTo
								,      m03_yoyangsa1_nm as m_nm
								,      r_seq as seq
								  from r_cltrec
								  left join m03sugupja
								    on org_no = m03_ccode
								   and r_sugupja = m03_jumin
								 where org_no = '$code'
								   and del_flag = 'N'
								 group by r_sugupja";


					}else {
						$sql = "select r_yymm as yymm
								,	   r_date as dt
								,      r_sugupja as c_cd
								,      sugupja.name as c_nm
								,      LVL.m81_name as level
								,      r_injung_no as no
								,      r_injung_from as dateFrom
								,      r_injung_to as dateTo
								,      sugupja.yoyangsa as m_nm
								,      r_seq as seq
								  from r_cltrec
								 inner join (
												select m03_jumin as jumin
												,      m03_name as name
												,      m03_ylvl as level
												,      m03_skind as kind
												,      m03_yoyangsa1_nm as yoyangsa
												,      m03_sdate as sdate
												,      m03_edate as edate
												  from m03sugupja
												 where m03_ccode = '$code'
												   and m03_mkind = ".$this->conn->_client_kind()."
												 union all
												select m31_jumin as jumin
												,      m03_name as name
												,      m31_level as level
												,      m31_kind as kind
												,      case when ifnull(m32_a_name, '') != '' then m32_a_name else m03_yoyangsa1_nm  end as yoyangsa
												,      m31_sdate as sdate
												,      m31_edate as edate
												  from m31sugupja
												 inner join m03sugupja
													on m03_ccode = m31_ccode
												   and m03_mkind = m31_mkind
												   and m03_jumin = m31_jumin
												  left join m32jikwon
													on m32_ccode = m31_ccode
												   and m32_mkind = m31_mkind
												   and m32_jumin = m31_jumin
												   and m32_a_date between m31_sdate and m31_edate
												 where m31_ccode = '$code'
												   and m31_mkind = ".$this->conn->_client_kind()."
											 ) as sugupja
									on r_sugupja = sugupja.jumin
								   and r_date between sugupja.sdate and sugupja.edate
								  left join m81gubun as LVL
									on LVL.m81_gbn  = 'LVL'
								   and LVL.m81_code = sugupja.level
								 where org_no = '$code'
								   and del_flag = 'N'";
				    }
				    if (!empty($find_counsel_name)) $sql .= ' and name like \'%'.$find_counsel_name.'%\'';
				    if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

				    $sql .= ' order by dt';


					break;

				case 'CLTSVCCTC': //장기요양급여제공 계약서(방문요양,방문목욕,방문간호)

					if($lbTestMode){

						if (!empty($find_counsel_name)) $wsl .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
						if (!empty($find_type)) $wsl .= ' and m03_mkind  = \''.$find_type.'\'';



						/*
						$sql = "select m03_name as c_nm
								,	   m03_mkind as kind
								,	   m03_jumin as c_cd
								,	   m03_tel as c_tel
								,      rate as c_bonin_yul
								,	   app_no as c_injungNo
								,      case lvl.svc_cd when '0' then case lvl.level when '9' then '일반' else concat(lvl.level,'등급') end
									   when '4' then concat(dis.svc_lvl,'등급') else '' end as c_level
								  from m03sugupja as mst
								  left join client_his_lvl as lvl
									on lvl.org_no = m03_ccode
								   and lvl.jumin = m03_jumin
								   and lvl.from_dt <= date_format(now(), '%Y-%m-%d')
								   and lvl.to_dt >= date_format(now(), '%Y-%m-%d')
							      left join client_his_kind as kind
								    on kind.org_no = m03_ccode
								   and kind.jumin = m03_jumin
								   and kind.from_dt <= date_format(now(), '%Y-%m-%d')
								   and kind.to_dt >= date_format(now(), '%Y-%m-%d')
								  left join client_his_dis as dis
								    on dis.org_no = m03_ccode
								   and dis.jumin = m03_jumin
								   and dis.from_dt <= date_format(now(), '%Y-%m-%d')
								   and dis.to_dt >= date_format(now(), '%Y-%m-%d')
								 where m03_ccode = '".$_SESSION["userCenterCode"]."'
								  $wsl
								   and m03_del_yn = 'N'
								 group by m03_jumin
								 order by m03_name, m03_jumin, m03_mkind";
						*/

					    $sql = "select jumin as c_cd
									 , svc_cd as kind
									 , name as c_nm
									 , mobile as c_tel
									 , lvl_nm as c_level
									 , rate  as c_bonin_yul
									 , app_no as c_injungNo
								  from (select mst.jumin
											 , mst.name
											 , svc.svc_cd
											 , mst.mobile
											 , lvl.level as level
											 , case lvl.svc_cd when '0' then case lvl.level when '9' then '일반' else concat(lvl.level,'등급') end
											   when '4' then concat(dis.svc_lvl,'등급') else '' end as lvl_nm
											 , kind.rate
											 , lvl.app_no
										  from (
										select min(m03_mkind) as kind
											 , m03_jumin as jumin
											 , m03_name as name
											 , m03_hp as mobile
											 , m03_yoyangsa1_nm as mem_nm1
											 , m03_yoyangsa2_nm as mem_nm2
											 , m03_yboho_phone as phone
										  from m03sugupja
										 where m03_ccode = '$code'
										  $wsl
										 group by m03_jumin
										) as mst
								  inner join (
										select min(svc_cd) as svc_cd
											 , jumin
											 , from_dt
											 , to_dt
										  from client_his_svc
										 where org_no = '$code'
										 group by jumin
										 order by case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
										   and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc
										) as svc
									 on svc.jumin = mst.jumin
								   left join (
										select jumin
											 , svc_cd
											 , level
											 , from_dt
											 , to_dt
											 , app_no
										  from client_his_lvl
									     where org_no = '$code'
										   and date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
										   and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d')
										) as lvl
									 on svc.jumin = lvl.jumin
										and svc.svc_cd = lvl.svc_cd left join (
										select jumin
											 , kind
											 , from_dt
											 , to_dt
											 , rate
										  from client_his_kind
										 where org_no = '$code'
										   and date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
										   and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d')
										) as kind
									 on svc.jumin = kind.jumin
										left join (
										select jumin
											 , svc_lvl
											 , from_dt
											 , to_dt
										  from client_his_dis
										 where org_no = '$code'
										   and date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
										   and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d')
										) as dis
									on svc.jumin = dis.jumin) as t

								 order by name, jumin,svc_cd";



					}else {
						$sql = 'select m03_name as c_nm
								,	   m03_mkind as kind
								,      m03_jumin as c_cd
								,      m03_tel as c_tel
								,	   m03_bonin_yul as c_bonin_yul
								,      m03_injung_no as c_injungNo
								,	   m81gubun.m81_name as c_level
								  from m03sugupja
								 inner join m81gubun
									on m81gubun.m81_code = m03_ylvl
								   and m81gubun.m81_gbn = \'LVL\'
								 where m03_ccode        = \''.$code.'\'
								   and m03_del_yn       = \'N\'';

						if (!empty($find_counsel_name)) $sql .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
						if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

						$sql .=	'order by m03_name';
					}
					break;

				case 'CLTPSR':
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_c_id as c_cd
							,      r_c_nm as c_nm
							,      r_m_nm as m_nm
							  from r_cltpsr
							  left join m03sugupja
							    on m03_ccode   = org_no
							   and m03_jumin = r_c_id
							   and m03_mkind = '.$this->conn->_client_kind().'
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_m_nm like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_c_nm like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= 'and r_yymm   = \''.$yymm.'\'';
						}

					$sql .=	'order by dt';

					break;

				case 'CLTCRSRH':	//서비스만족도조사설문지(방문요양)
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_date as dt
							,      r_sugupja as c_cd
							,      r_sugupja_name as c_nm
							,      r_yoyangsa_name as m_nm
							  from r_quest
							  left join m03sugupja
							    on m03_ccode = org_no
							   and m03_mkind = '.$this->conn->_client_kind().'
							   and m03_jumin = r_sugupja
							 where org_no   = \''.$code.'\'
							   and r_service_gbn = \'200\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_sugupja_name like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_yoyangsa_name like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	' and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= ' and r_yymm   = \''.$yymm.'\'';
						}

					$sql .=	' order by dt, r_yoyangsa_name';

					break;

				case 'CLTBRSRH':	//서비스만족도조사설문지(방문목욕)
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_date as dt
							,      r_sugupja as c_cd
							,      r_sugupja_name as c_nm
							,      r_yoyangsa_name as m_nm
							  from r_quest
							  left join m03sugupja
							    on m03_ccode = org_no
							   and m03_mkind = '.$this->conn->_client_kind().'
							   and m03_jumin = r_sugupja
							 where org_no   = \''.$code.'\'
							   and r_service_gbn = \'500\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_sugupja_name like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_yoyangsa_name like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	' and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= ' and r_yymm   = \''.$yymm.'\'';
						}

					$sql .=	' order by dt, r_yoyangsa_name';

					break;

				case 'CLTNRSRH':	//서비스만족도조사설문지(방문간호)
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_date as dt
							,      r_sugupja as c_cd
							,      r_sugupja_name as c_nm
							,      r_yoyangsa_name as m_nm
							  from r_quest
							  left join m03sugupja
							    on m03_ccode = org_no
							   and m03_mkind = '.$this->conn->_client_kind().'
							   and m03_jumin = r_sugupja
							 where org_no   = \''.$code.'\'
							   and r_service_gbn = \'800\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_sugupja_name like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_yoyangsa_name like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	' and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= ' and r_yymm   = \''.$yymm.'\'';
						}

					$sql .=	' order by dt, r_yoyangsa_name';

					break;

				case 'CLTPLAGC':  //서비스제공계획서및동의서(방문요양)

					if($lbTestMode){

						if (!empty($find_counsel_name)) $wsl .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
						if (!empty($find_type)) $wsl .= ' and m03_mkind  = \''.$find_type.'\'';

						$sql = "select m03_name as c_nm
								,	   m03_mkind as kind
								,	   m03_jumin as c_cd
								,	   m03_yoyangsa1_nm as m_nm
								,	   case lvl.level when '9' then '일반' else concat(lvl.level,'등급') end as level
								  from m03sugupja
								  left join client_his_lvl as lvl
									on lvl.org_no = m03_ccode
								   and lvl.jumin = m03_jumin
								   and lvl.from_dt <= date_format(now(), '%Y-%m-%d')
								   and lvl.to_dt >= date_format(now(), '%Y-%m-%d')
							      left join client_his_kind as kind
								    on kind.org_no = m03_ccode
								   and kind.jumin = m03_jumin
								   and kind.from_dt <= date_format(now(), '%Y-%m-%d')
								   and kind.to_dt >= date_format(now(), '%Y-%m-%d')
								 where m03_ccode = '".$_SESSION["userCenterCode"]."'
								  $wsl
								   and m03_del_yn = 'N'
								 group by m03_jumin
								 order by m03_name, m03_jumin, m03_mkind";

					}else{
						$sql = 'select m03_mkind as kind
								,	   m03_name as c_nm
								,	   m03_jumin as c_cd
								,	   m03_yoyangsa1_nm as m_nm
								,	   LVL.m81_name as level
								  from m03sugupja
								 inner join m81gubun as LVL
									on LVL.m81_gbn  = \'LVL\'
								   and LVL.m81_code = m03_ylvl
								 where m03_ccode    = \''.$code.'\'
								   and m03_mkind    = '.$this->conn->_client_kind().'';

						if (!empty($find_counsel_name)) $sql .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
						if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

						$sql .=	'order by m03_name';

					}

					break;

				case 'CLTPLAGB':  //서비스제공계획서및동의서(방문목욕)

					if($lbTestMode){

						if (!empty($find_counsel_name)) $wsl .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
						if (!empty($find_type)) $wsl .= ' and m03_mkind  = \''.$find_type.'\'';

						$sql = "select m03_name as c_nm
								,	   m03_mkind as kind
								,	   m03_jumin as c_cd
								,	   m03_yoyangsa1_nm as m_nm
								,	   case lvl.level when '9' then '일반' else concat(lvl.level,'등급') end as level
								  from m03sugupja
								  left join client_his_lvl as lvl
									on lvl.org_no = m03_ccode
								   and lvl.jumin = m03_jumin
								   and lvl.from_dt <= date_format(now(), '%Y-%m-%d')
								   and lvl.to_dt >= date_format(now(), '%Y-%m-%d')
							      left join client_his_kind as kind
								    on kind.org_no = m03_ccode
								   and kind.jumin = m03_jumin
								   and kind.from_dt <= date_format(now(), '%Y-%m-%d')
								   and kind.to_dt >= date_format(now(), '%Y-%m-%d')
								 where m03_ccode = '".$_SESSION["userCenterCode"]."'
								  $wsl
								   and m03_del_yn = 'N'
								 group by m03_jumin
								 order by m03_name, m03_jumin, m03_mkind";

					}else{
						$sql = 'select m03_mkind as kind
								,	   m03_name as c_nm
								,	   m03_jumin as c_cd
								,	   m03_yoyangsa1_nm as m_nm
								,	   LVL.m81_name as level
								  from m03sugupja
								 inner join m81gubun as LVL
									on LVL.m81_gbn  = \'LVL\'
								   and LVL.m81_code = m03_ylvl
								 where m03_ccode    = \''.$code.'\'
								   and m03_mkind    = '.$this->conn->_client_kind().'';

						if (!empty($find_counsel_name)) $sql .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
						if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

						$sql .=	'order by m03_name';
					}

					break;

				case 'CLTPLAGN':  //서비스제공계획서및동의서(방문간호)


					if (!empty($find_counsel_name)) $wsl .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $wsl .= ' and m03_mkind  = \''.$find_type.'\'';

					$sql = "select m03_name as c_nm
							,	   m03_mkind as kind
							,	   m03_jumin as c_cd
							,	   m03_yoyangsa1_nm as m_nm
							,	   case lvl.level when '9' then '일반' else concat(lvl.level,'등급') end as level
							  from m03sugupja
							  left join client_his_lvl as lvl
								on lvl.org_no = m03_ccode
							   and lvl.jumin = m03_jumin
							   and lvl.from_dt <= date_format(now(), '%Y-%m-%d')
							   and lvl.to_dt >= date_format(now(), '%Y-%m-%d')
							  left join client_his_kind as kind
								on kind.org_no = m03_ccode
							   and kind.jumin = m03_jumin
							   and kind.from_dt <= date_format(now(), '%Y-%m-%d')
							   and kind.to_dt >= date_format(now(), '%Y-%m-%d')
							 where m03_ccode = '".$_SESSION["userCenterCode"]."'
							  $wsl
							   and m03_del_yn = 'N'
							 group by m03_jumin
							 order by m03_name, m03_jumin, m03_mkind";


					break;


				case 'CLTSUCHC':  //수급자교부서류

					if($lbTestMode){
						if (!empty($find_counsel_name)) $wsl .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
						if (!empty($find_type)) $wsl .= ' and m03_mkind  = \''.$find_type.'\'';

						$sql = "select m03_name as c_nm
								,	   m03_mkind as kind
								,	   m03_jumin as c_cd
								,	   m03_tel as c_tel
								,	   case lvl.level when '9' then '일반' else concat(lvl.level,'등급') end as level
								  from m03sugupja
								  left join client_his_lvl as lvl
									on lvl.org_no = m03_ccode
								   and lvl.jumin = m03_jumin
								   and lvl.from_dt <= date_format(now(), '%Y-%m-%d')
								   and lvl.to_dt >= date_format(now(), '%Y-%m-%d')
							      left join client_his_kind as kind
								    on kind.org_no = m03_ccode
								   and kind.jumin = m03_jumin
								   and kind.from_dt <= date_format(now(), '%Y-%m-%d')
								   and kind.to_dt >= date_format(now(), '%Y-%m-%d')
								 where m03_ccode = '".$_SESSION["userCenterCode"]."'
								  $wsl
								   and m03_del_yn = 'N'
								 group by m03_jumin
								 order by m03_name, m03_jumin, m03_mkind";

					}else{
						$sql = 'select m03_mkind as kind
								,	   m03_name as c_nm
								,	   m03_jumin as c_cd
								,	   m03_tel as c_tel
								,	   LVL.m81_name as level
								  from m03sugupja
								 inner join m81gubun as LVL
									on LVL.m81_gbn  = \'LVL\'
								   and LVL.m81_code = m03_ylvl
								 where m03_ccode    = \''.$code.'\'
								   and m03_mkind    = '.$this->conn->_client_kind().'
								   and m03_del_yn = \'N\'';

						if (!empty($find_counsel_name)) $sql .= ' and m03_name like \'%'.$find_counsel_name.'%\'';
						if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

						$sql .=	'group by m03_jumin, m03_name
								 order by m03_name';

					}

					break;

				case 'NURRSH':
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_m_nm as m_nm
							,      r_c_id as c_cd
							,      r_c_nm as c_nm
							,	   r_c_lvl as c_lvl
							,      r_c_tel as c_tel
							  from r_nurrsh
							  left join m03sugupja
							    on m03_ccode = org_no
							   and m03_jumin = r_c_id
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_name)) $sql .= ' and r_m_nm like \'%'.$find_name.'%\'';
					if (!empty($find_counsel_name)) $sql .= ' and r_c_nm like \'%'.$find_counsel_name.'%\'';
					if (!empty($find_type)) $sql .= ' and m03_mkind  = \''.$find_type.'\'';

					if (!empty($yymm))
						if(substr($yymm,4,2) == 00){
							$sql .=	'and r_yymm  like \''.substr($yymm,0,4).'%\'';
						}else {
							$sql .= 'and r_yymm   = \''.$yymm.'\'';
						}

					if (!empty($ssn))
						$sql .= ' and r_c_id = \''.$ssn.'\'';

					$sql .= ' group by r_c_nm, r_c_id
							  order by dt';

					if ($limit > 0)
						$sql .= ' limit '.$limit;

					break;

				case 'CLTLCC12':

					if($_SESSION['userLevel'] == 'P') $wsl2 = ' and t01_yoyangsa_id1 = \''.$_SESSION['userSSN'].'\'';

					$sql = 'SELECT DISTINCT iljung.jumin AS c_cd
							,      mst.name AS c_nm
							,      CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END AS l_nm
							,      CASE kind.kind WHEN \'1\' THEN \'일반\'
												  WHEN \'2\' THEN \'의료\'
												  WHEN \'3\' THEN \'기초\'
												  WHEN \'4\' THEN \'경감\' ELSE \'-\' END AS s_nm
							,      mst.tel AS c_tel
							  FROM (
							SELECT t01_jumin AS jumin
							  FROM t01iljung
							 WHERE t01_ccode = \''.$code.'\'
							   AND t01_mkind = \'0\'
							   AND t01_status_gbn = \'1\'
							   AND t01_del_yn = \'N\'
							   AND LEFT(t01_sugup_date, 6) = \''.$yymm.'\'
							   '.$wsl2.'
								   ) AS iljung
							 INNER JOIN (
								   SELECT m03_jumin AS jumin
								   ,      m03_name AS name
								   ,      m03_tel AS tel
									 FROM m03sugupja
									WHERE m03_ccode = \''.$code.'\'
									  AND m03_mkind = \'0\'
								   ) AS mst
								ON mst.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      level
									 FROM client_his_lvl
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
								   ) AS lvl
								ON lvl.jumin = iljung.jumin
							  LEFT JOIN (
								   SELECT jumin
								   ,      kind
									 FROM client_his_kind
									WHERE org_no = \''.$code.'\'
									  AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$yymm.'\'
									  AND DATE_FORMAT(to_dt,\'%Y%m\') >= \''.$yymm.'\'
								   ) AS kind
								ON kind.jumin = iljung.jumin
							 ORDER BY name';


					break;

				case 'CLTSVCCTCC':
					$sql = 'select r_yymm as yymm
							,      r_seq as seq
							,      r_reg_dt as dt
							,      r_c_id as c_cd
							,      r_c_nm as c_nm
							,      r_c_fm_dt as from_dt
							,      r_c_to_dt as to_dt
							  from r_cltsvcctcc
							 where org_no   = \''.$code.'\'
							   and del_flag = \'N\'';

					if (!empty($find_counsel_name)) $sql .= ' and r_c_nm like \'%'.$find_counsel_name.'%\'';

					if (!empty($ssn))
						$sql .= ' and r_c_id = \''.$ssn.'\'';

					$sql .= ' order by dt';

					if ($limit > 0)
						$sql .= ' limit '.$limit;


					break;

				default:
					if ($index == 'CLTSTATRCD' ||
						$index == 'CLTPLANCHN'){
						$sql = 'SELECT rpt.r_yymm AS yymm
								,      rpt.r_seq AS seq
								,      rpt.r_reg_dt AS dt
								,      rpt.r_c_id AS c_cd
								,      rpt.r_c_nm AS c_nm
								,      CASE lvl.svc_cd WHEN \'0\' THEN CASE lvl.level WHEN \'9\' THEN \'일반\' ELSE CONCAT(lvl.level,\'등급\') END
													   WHEN \'4\' THEN CONCAT(lvl.level,\'등급\') ELSE \'\' END AS lvl
								,      CASE kind.kind WHEN \'3\' THEN \'기초\'
													  WHEN \'2\' THEN \'의료\'
													  WHEN \'4\' THEN \'경감\' ELSE \'일반\' END AS kind
								  FROM r_'.strtolower($index).' AS rpt
								  LEFT JOIN (
										   select jumin
										   ,      level
										   ,      svc_cd
											 from client_his_lvl AS lvl
											where org_no = \''.$code.'\'
											  and date_format(from_dt,\'%Y%m\') <= \''.$yymm.'\'
											  and date_format(to_dt,  \'%Y%m\') >= \''.$yymm.'\'
											  group by jumin
										   ) as lvl
										ON rpt.r_c_id  = lvl.jumin
								  LEFT JOIN (
										   select jumin
										   ,      kind
											 from client_his_kind
											where org_no = \''.$code.'\'
											  and date_format(from_dt,\'%Y%m\') <= \''.$yymm.'\'
											  and date_format(to_dt,  \'%Y%m\') >= \''.$yymm.'\'
											  group by jumin
										   ) as kind
										 ON rpt.r_c_id = kind.jumin';

						if($index == 'CLTPLANCHN' and $_SESSION['userLevel'] == 'P'){
							/******************************
							2013.01.02
							급여계획변경일지 추가수정
							직원개인아이디 접속 시
							******************************/
							$sql .= ' INNER JOIN ( select org_no, r_seq
													 from r_'.strtolower($index).'_sub
													where r_m_id = \''.$_SESSION['userSSN'].'\'
													group by r_m_id ) as rpt_sub
										 ON rpt_sub.org_no = rpt.org_no';
						}

						$sql .=	' WHERE rpt.org_no   = \''.$code.'\'
								    AND rpt.r_yymm   = \''.$yymm.'\'
								    AND rpt.del_flag = \'N\'';
						if($index == 'CLTSTATRCD' and $_SESSION['userLevel'] == 'P'){
							/******************************
							2013.01.02
							상태변화기록지 추가수정
							직원개인아이디 접속 시
							******************************/
							$sql .= ' AND rpt.r_m_id_1 = \''.$_SESSION['userSSN'].'\'
									  OR rpt.r_m_id_2 = \''.$_SESSION['userSSN'].'\'
									  OR rpt.r_m_id_3 = \''.$_SESSION['userSSN'].'\'
									  OR rpt.r_m_id_4 = \''.$_SESSION['userSSN'].'\'
									  OR rpt.r_m_id_5 = \''.$_SESSION['userSSN'].'\'';
						}
						if($index == 'CLTSTATRCD'){
							$sql .= ' ORDER BY  c_nm, dt';
						}else {
							$sql .= ' ORDER BY dt ';
						}
					}else{
						$sql = 'select r_yymm as yymm
								,      r_seq as seq
								,      r_reg_dt as dt
								,      r_c_id as c_cd
								,      r_c_nm as c_nm
								,      r_m_nm as m_nm
								  from r_'.strtolower($index).'
								 where org_no   = \''.$code.'\'
								   and r_yymm   = \''.$yymm.'\'
								   and del_flag = \'N\'
								 order by dt';
					}
					break;
			}

			return $sql;
		}

		function _member_kind(){
			//return '(select min(m02_mkind) from m02yoyangsa as tmp where tmp.m02_ccode = m02yoyangsa.m02_ccode and tmp.m02_yjumin = m02yoyangsa.m02_yjumin and tmp.m02_del_yn = \'N\')';
			return '0';
		}

		function _client_kind(){
			//return '(select min(m03_mkind) from m03sugupja as tmp where tmp.m03_ccode = m03sugupja.m03_ccode and tmp.m03_jumin = m03sugupja.m03_jumin and tmp.m03_del_yn = \'N\')';
			return '0';
		}

		function _default_query($report_id, $code, $yymm, $seq){
			$sql = 'select *
					  from r_'.strtolower($report_id).'
					 where org_no   = \''.$code.'\'
					   and r_yymm   = \''.$yymm.'\'
					   and r_seq    = \''.$seq.'\'
					   and del_flag = \'N\'';

			return $sql;
		}
	}

	$report = new Report($conn);
?>
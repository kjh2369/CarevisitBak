<?
	@session_start();

	class connection{
		var $conn;
		var $server		= "localhost";
		var $user		= "care";
		var $pass		= "care";
		var $db_name	= "care";
		var $m_center	= 'ON01001';
		var $mst_code   = 'goodeos';
		var $result		= 0;
		var $row		= 0;
		var $currentRow = 0;
		var $mode		= 1;
		var $error_msg	= '';
		var $error_no	= 0;
		var $error_query= '';
		var $fetch_type	= 'array';
		var $debug		= false;


		function connection($server = '', $user = '', $pass = '', $dbName = ''){
			if (!empty($server)) $this->server = $server;
			if (!empty($user)) $this->user = $user;
			if (!empty($pass)) $this->pass = $pass;
			if (!empty($dbName)) $this->db_name = $dbName;

			$this->conn = @mysql_connect($this->server,$this->user,$this->pass) or die ("SQL server ERROR");

			mysql_query("set names utf8");
			mysql_query("set autocommit=0");
			mysql_select_db($this->db_name);
		}

		function auto_commit_set(){
			mysql_query("set autocommit=0");
		}

		function auto_commit_unset(){
			mysql_query("set autocommit=1");
		}

		function set_name($name){
			mysql_query('set names '.$name);
		}

		function close(){
			mysql_close($this->conn);
		}

		function begin(){
			mysql_query("begin");
		}

		function rollback(){
			mysql_query("rollback");
		}

		function commit(){
			if (!$this->debug){
				mysql_query("commit");
			}else{
				$this->rollback();
			}
		}

		function query($query){
			$this->error_query = $query;
			$this->result = mysql_query($query);

			return $this->result;
		}

		function execute($query){
			$this->error_query = $query;

			if ($this->debug) echo nl2br($query).'<br><br>';
			if ($this->mode == 1){
				$result = mysql_query($query);

				if (!$result){
					$this->error_msg = mysql_error();
					$this->error_no	 = mysql_errno();
				}

				return $result;
			}else{
				$this->temp_query = $query;
				return false;
			}
		}

		function fetch(){
			$this->row = @mysql_fetch_array($this->result);
			$this->currentRow = 0;

			return $this->row;
		}

		function fetch_assoc(){
			$this->row = mysql_fetch_assoc($this->result);
			$this->currentRow = 0;

			return $this->row;
		}

		function fetch_object(){
			$this->row = mysql_fetch_object($this->result);
			$this->currentRow = 0;

			return $this->row;
		}

		function fetch_row(){
			return mysql_fetch_row($this->result);
		}

		function row_affect(){
			return mysql_affected_rows();
		}

		function row_count(){
			return @mysql_num_rows($this->result);
		}

		function select_row($index){
			mysql_data_seek($this->result, $index);

			$this->currentRow = $index;

			if ($this->fetch_type == 'assoc'){
				return mysql_fetch_assoc($this->result);
			}else if ($this->fetch_type == 'row'){
				return mysql_fetch_row($this->result);
			}else if ($this->fetch_type == 'object'){
				return mysql_fetch_object($this->result);
			}else{
				return mysql_fetch_array($this->result);
			}
		}

		function p_row(){
			if ($this->row_count() <= $this->currentRow){
				$this->currentRow = $this->row_count() - 1;
			}

			mysql_data_seek($this->result, $this->currentRow);

			if ($this->currentRow > 0){
				$this->currentRow--;
			}

			return  mysql_fetch_array($this->result);
		}

		function n_row(){
			if ($this->row_count() <= $this->currentRow){
				$this->currentRow = $this->row_count() - 1;
			}

			mysql_data_seek($this->result, $this->currentRow);

			if ($this->row_count() > $this->currentRow){
				$this->currentRow++;
			}

			return  mysql_fetch_array($this->result);
		}

		function row_free(){
			if ($this->result !=0){
				mysql_free_result($this->result);
			}
			$this->result=0;
		}

		function get_query($gubun, $mCode = "", $mKind = "", $yKey = ""){
			if ($gubun == ""){
				$sql = "";
			}else if($gubun == "00"){
				$sql = "select *"
					 . "  from m00center"
					 . " where m00_mcode = '".$mCode
					 . "'  and m00_mkind = '".$mKind
					 . "'";
			}else if($gubun == "02"){
				$sql = "select *"
					 . "  from m02yoyangsa"
					 . " where m02_ccode = '".$mCode
					 . "'  and m02_mkind = '".$mKind
					 . "'  and m02_key   = '".$yKey
					 . "'";
			}else if($gubun == "98"){
				$sql = "select m98_code"
					 . ",      m98_name"
					 . ",      m98_salary_yn"
					 . "  from m98job"
					 . " order by m98_code";
			}else if($gubun == "99"){
				$sql = "select m99_code"
					 . ",      m99_name"
					 . "  from m99license"
					 . " order by m99_code";
			}else{
				$sql = "";
			}
			return $sql;
		}

		function get_login($userCode, $userPass){
			$sql = "select *"
				 . "  from han_member"
				 . " where id   = '".$userCode
				 . "'  and pswd = '".$userPass
				 . "'";
			return $sql;
		}

		function get_gubun($gbn, $code = "", $name = ""){
			$sql = "select m81_code"
				 . ",      m81_name"
				 . "  from m81gubun"
				 . " where m81_gbn = '".$gbn
				 . "'";
			if ($code != ""){
				$sql .= " and m81_code = '".$code."'";
			}
			if ($name != ""){
				$sql .= " and m81_name like '%".$name."%'";
			}

			$sql .= " order by m81_seq";

			return $sql;
		}

		// 수급자의 급여 최대한도를 리턴한다.
		function get_kupyeo_max($mCode, $mKind, $mJumin){
			$sql = 'select m03_kupyeo_max'
				 . '  from m03sugupja'
				 . ' where m03_ccode = \''.$mCode
				 . '\' and m03_mkind = \''.$mKind
				 . '\' and m03_jumin = \''.$mJumin
				 . '\'';
			$p_result = mysql_query($sql);
			$p_row = mysql_fetch_array($p_result);
			$kupyeoMax = $p_row[0];
			mysql_free_result($p_result);

			return $kupyeoMax;
		}

		// 수급자의 본인부담율을 리턴한다.
		function get_bonin_yul($mCode, $mKind, $mJumin){
			$sql = 'select m03_bonin_yul'
				 . '  from m03sugupja'
				 . ' where m03_ccode = \''.$mCode
				 . '\' and m03_mkind = \''.$mKind
				 . '\' and m03_jumin = \''.$mJumin
				 . '\'';
			$p_result = mysql_query($sql);
			$p_row = mysql_fetch_array($p_result);
			$boninYul = $p_row[0];
			mysql_free_result($p_result);

			return $boninYul;
		}

		// 일정의 년 찾기
		function get_iljung_year($mCode){
			$sql = "select left(ifnull(min(t01_sugup_date), date_format(now(), '%Y%m%d')), 4)"
				 . ",      left(ifnull(max(t01_sugup_date), date_format(now(), '%Y%m%d')), 4)"
				 . "  from t01iljung"
				 . " where t01_ccode = '".$mCode
				 . "'";
			$p_result = mysql_query($sql);
			$p_row = mysql_fetch_array($p_result);
			$iljungYear[0] = $p_row[0];
			$iljungYear[1] = $p_row[1];
			mysql_free_result($p_result);

			return $iljungYear;
		}

		// 최소, 최대 년도
		function get_min_max_year($table, $column){
			$sql = "select left(ifnull(min(".$column."), date_format(now(), '%Y%m%d')), 4)"
				 . ",      left(ifnull(max(".$column."), date_format(now(), '%Y%m%d')), 4)"
				 . "  from ".$table;
			$p_result = mysql_query($sql);
			$p_row = mysql_fetch_array($p_result);
			$year[0] = $p_row[0];
			$year[1] = $p_row[1];
			mysql_free_result($p_result);

			return $year;
		}

		// 수가찾기
		function get_suga($pCode, $pSuga, $pDate = ''){
			/*
			$sql = "select m01_suga_cont"
				 . "  from m01suga"
				 . " where m01_mcode  = '".$pCode
				 . "'  and m01_mcode2 = '".$pSuga."'";

			if ($pDate == ''){
				$sql .= "  and date_format(now(), '%Y%m%d') between m01_sdate and m01_edate";
			}else{
				$sql .= "  and ".str_replace('-', '', $pDate)." between m01_sdate and m01_edate";
			}

			$p_result = mysql_query($sql);
			$p_row = mysql_fetch_array($p_result);
			$suga = $p_row[0];
			mysql_free_result($p_result);

			if ($suga == ''){
				$sql = "select m11_suga_cont"
					 . "  from m11suga"
					 . " where m11_mcode  = '".$pCode
					 . "'  and m11_mcode2 = '".$pSuga."'";

				if ($pDate == ''){
					$sql .= "  and date_format(now(), '%Y%m%d') between m11_sdate and m11_edate";
				}else{
					$sql .= "  and ".str_replace('-', '', $pDate)." between m11_sdate and m11_edate";
				}

				$p_result = mysql_query($sql);
				$p_row = mysql_fetch_array($p_result);
				$suga = $p_row[0];
				mysql_free_result($p_result);
			}
			*/

			$sql ="select m01_suga_cont
			         from (
				          select m01_suga_cont, m01_sdate, m01_edate
					        from m01suga
					       where m01_mcode  = '$pCode'
					         and m01_mcode2 = '$pSuga'
					       union all
				          select m11_suga_cont, m11_sdate, m11_edate
					        from m11suga
					       where m11_mcode  = '$pCode'
					         and m11_mcode2 = '$pSuga'
					       union all
				          select concat(service_gbn, case when service_gbn = '방문목욕' or service_gbn = '방문간호' then concat('(',service_lvl,')') else '' end), replace(service_from_dt, '-', ''), replace(service_to_dt, '-', '')
				            from suga_service
					       where org_no       = '$pCode'
					         and service_code = '$pSuga'
				          ) as t";

			if ($pDate == ''){
				$sql .= " where date_format(now(), '%Y%m%d') between m01_sdate and m01_edate";
			}else{
				$sql .= " where '".str_replace('-', '', $pDate)."' between m01_sdate and m01_edate";
			}

			$suga = $this->get_data($sql);

			return $suga;
		}

		function get_sugupja_jumin($pCode, $pKind, $pKey){
			$sql = "select m03_jumin"
			     . "  from m03sugupja"
				 . " where m03_ccode = '".$pCode
				 . "'  and m03_mkind = '".$pKind
				 . "'  and m03_key   = '".$pKey
				 . "'";
			return $this->get_data($sql);
		}

		// 수급자의 등급
		function get_sugupja_level($pCode, $pKind, $pJumin){
			$sql = "select m03_ylvl"
				 . "  from m03sugupja"
				 . " where m03_ccode = '".$pCode
				 . "'  and m03_mkind = '".$pKind
				 . "'  and m03_jumin = '".$pJumin
				 . "'";
			return $this->get_data($sql);
		}

		// 요양사의 시급
		function get_time_pay($pCode, $pKind, $pJumin, $pGubun){
			$sql = "select m02_pay"
				 . "  from m02pay"
				 . " where m02_ccode = '".$pCode
				 . "'  and m02_mkind = '".$pKind
				 . "'  and m02_jumin = '".$pJumin
				 . "'  and m02_gubun = '".$pGubun
				 . "'";
			return $this->get_data($sql);
		}

		// 데이타조회
		function get_data($sql){
			$p_result = mysql_query($sql);
			@$p_row = mysql_fetch_array($p_result);
			$value = $p_row[0];
			@mysql_free_result($p_result);

			return $value;
		}

		// 데이타조회
		function get_array($sql){
			$p_result = mysql_query($sql);

			if ($this->fetch_type == 'assoc'){
				$p_row = mysql_fetch_assoc($p_result);
			}else if ($this->fetch_type == 'row'){
				$p_row = mysql_fetch_row($p_result);
			}else if ($this->fetch_type == 'object'){
				$p_row = mysql_fetch_object($p_result);
			}else{
				@$p_row = mysql_fetch_array($p_result);
			}

			@mysql_free_result($p_result);

			return $p_row;
		}

		// 다음 키
		function next_key($table, $filed){
			$sql = "select ifnull(max($filed), 0) + 1
					  from $table";
			$key = $this->get_data($sql);

			return $key;
		}

		// 월별 리스트 카운트 쿼리
		function month_list_count_query($table, $index, $code, $kind, $year){
			$sql = "select date_format(".$index."_date, '%m'), count(".$index."_date)
					  from ".$table."
					 where ".$index."_ccode = '".$code."'
					   and ".$index."_mkind = '".$kind."'
					   and ".$index."_date like '".$year."%'
					 group by date_format(".$index."_date, '%m')";
			return $sql;
		}

		// 에러문
		function error(){
			return mysql_error();
		}

		// 에러
		function err_back($index = ''){
			if ($this->mode == 1){
				$error = $this->error();
				$str = "<script>alert('".$index."데이타 처리중 오류가 발생하였습니다.'); history.back();</script>";
			}else{
				if ($this->error_no != 0){
					$str  = '[ERROR NO] '.$this->error_no.'<br>';
					$str .= '[ERROR MESSAGE] '.$this->error_msg.'<br>';
					$str .= '[ERROR QUERY]<br>'.$this->temp_query.'<br><br>';
				}else{
					$str = '[EXECUTE QUERY]<br>'.$this->temp_query.'<br><br>';
				}
			}
			return $str;
		}

		// 수급자 본인부담율 조인쿼리
		function joinBoninYulQuerty($p_code, $p_kind, $p_jumin){
			$sql = "select m03_ccode as mCode
					,      m03_mkind as mKind
					,      m03_jumin as mJumin
					,      m03_name as mName
					,      m03_ylvl as mYlvl
					,      m03_skind as mSkind
					,      m03_bonin_yul as mBoninYul
					,      m03_kupyeo_max as mPayMax
					,      m03_sdate as mSdate
					,      m03_edate as mEdate
					,      m03_key   as mKey
					  from m03sugupja
					 where m03_ccode = '$p_code'
					   and m03_mkind = '$p_kind'
					   and m03_jumin = '$p_jumin'
					 union all
					select m31_ccode as mCode
					,      m31_mkind as mKind
					,      m31_jumin as mJumin
					,      m03_name as mName
					,      m31_level as mYlvl
					,      m31_kind as mSkind
					,      m31_bonin_yul as mBoninYul
					,      m31_kupyeo_max as mPayMax
					,      m31_sdate as mSdate
					,      m31_edate as mEdate
					,      m03_key   as mKey
					  from m31sugupja
					 inner join m03sugupja
						on m03_ccode = m31_ccode
					   and m03_mkind = m31_mkind
					   and m03_jumin = m31_jumin
					 where m31_ccode = '$p_code'
					   and m31_mkind = '$p_kind'
					   and m31_jumin = '$p_jumin'";
			return $sql;
		}

		// 센터명
		function get_centerName($p_code, $p_kind){
			$sql = "select m00_cname
					  from m00center
					 where m00_mcode = '$p_code'
					   and m00_mkind = '$p_kind'";
			$centerName = $this->get_data($sql);

			return $centerName;
		}

		function _storeName($asCode){
			$sql = 'SELECT m00_store_nm
					  FROM m00center
					 WHERE m00_mcode = \''.$asCode.'\'
					 ORDER BY m00_mkind
					 LIMIT 1';
			$lsName = $this->get_data($sql);

			return $lsName;
		}

		// 센터승인번호
		function center_code($p_code, $p_kind){
			$sql = "select m00_code1
					  from m00center
					 where m00_mcode = '$p_code'
					   and m00_mkind = '$p_kind'";
			$center_code = $this->get_data($sql);

			return $center_code;
		}

		// 센터명
		function center_name($p_code, $p_kind = ''){
			if ($p_code == $this->m_center){
				$center_name = $_SESSION["userCenterName"];
			}else{
				/*
				$sql = "select m00_cname
						  from m00center
						 where m00_mcode = '$p_code'";

				if ($p_kind != ''){
					$sql .= " and m00_mkind = '$p_kind'";
				}else{
					$sql .= " and m00_mkind = (select min(m00_mkind) from m00center where m00_mcode = '$p_code')";
				}

				$center_name = $this->get_data($sql);
				*/
				$sql = 'SELECT	DISTINCT m00_store_nm
						FROM	m00center
						WHERE	m00_mcode = \''.$p_code.'\'';

				$center_name = $this->get_data($sql);
			}

			return $center_name;
		}

		function center_icon($code, $kind = ''){
			$sql = "select m00_icon
					  from m00center
					 where m00_mcode = '$code'";

			if ($kind != ''){
				$sql .= " and m00_mkind = '$kind'";
			}else{
				$sql .= " and m00_mkind = (select min(m00_mkind) from m00center where m00_mcode = '$code')";
			}

			$center_icon = $this->get_data($sql);

			return $center_icon;
		}

		// 수급자명
		function client_name($p_code, $p_jumin, $p_kind = ''){
			$sql = "select m03_name
					  from m03sugupja
					 where m03_ccode = '$p_code'
					   and m03_jumin = '$p_jumin'";

			if ($p_kind != ''){
				$sql .= " and m03_mkind = '$p_kind'";
			}else{
				$sql .= " and m03_mkind = ".$this->_client_kind();
			}

			$client_name = $this->get_data($sql);

			return $client_name;
		}

		// 요양보호사명
		function member_name($p_code, $p_jumin, $p_kind = ''){
			$sql = "select m02_yname
					  from m02yoyangsa
					 where m02_ccode  = '$p_code'
					   and m02_yjumin = '$p_jumin'";

			if ($p_kind != ''){
				$sql .= " and m02_mkind = '$p_kind'";
			}else{
				$sql .= " and m02_mkind = ".$this->_mem_kind();
			}

			$member_name = $this->get_data($sql);

			return $member_name;
		}

		function MemberName($orgNo, $jumin){
			$sql = 'SELECT	m02_yname
					FROM	m02yoyangsa
					WHERE	m02_ccode  = \''.$orgNo.'\'
					AND		m02_yjumin = \''.$jumin.'\'
					LIMIT	1';

			$name = $this->get_data($sql);

			return $name;
		}

		// 기관분류
		function center_kind($p_code){
			return $this->get_data("select min(m00_mkind) from m00center where m00_mcode = '$p_code'");
		}

		// 기관구분리스트
		function kind_list($code, $voucher = false){

			//노인맞춤돌봄서비스
			$i = sizeof($list);
			$list[$i]['id']   = 34;
			$list[$i]['code'] = 'S';
			$list[$i]['name'] = '노인맞춤돌봄서비스';

			$this->row_free();

			return $list;
		}

		// 기관구분리스트
		function kind_list_detail($code, $voucher = false){
			$list = $this->kind_list($code, $voucher);

			if (is_array($list)){
				foreach($list as $i => $l){
					if ($l['id'] == 11){
						$list[$i]['sub'] = array('200'=>'방문요양', '500'=>'방문목욕', '800'=>'방문간호', '210'=>'치매가족');
					}else if($l['id'] == 24){
						$list[$i]['sub'] = array('200'=>'활동지원', '500'=>'방문목욕', '800'=>'방문간호');
					}
				}
			}

			return $list;
		}

		//서비스별 정렬
		function svcKindSort($code, $voucher = false){
			$tmpKind = $this->kind_list_detail($code, $voucher);

			foreach($tmpKind as $svcIdx => $svcKind){
				if ($svcKind['code'] == '0'){
					$lsGbn = 'H';
				}else if ($svcKind['code'] >= '1' && $svcKind['code'] <= '4'){
					$lsGbn = 'V';
				}else if ($svcKind['code'] == '6'){
					$lsGbn = 'C';
				}else{
					$lsGbn = 'O';
				}

				$idx = SizeOf($laKind[$lsGbn]);

				$laKind[$lsGbn][$idx] = Array(
					'code'=>$svcKind['code']
				,	'name'=>$svcKind['name']
				,	'sub'=>$svcKind['sub']
				);
			}

			UnSet($tmpKind);

			return $laKind;
		}

		/*********************************************************

			서비스별

		*********************************************************/
		function kind_list_service($list){
			foreach($list as $i => $l){
				if ($l['id'] == 11){
					$svc[0]['title'] = '재가요양';
					$svc[0]['list']  = $l['sub'];
				}else if ($l['id'] > 20 && $l['id'] < 30){
					$svc[1]['title'] = '바우처';

					if ($l['id'] == 24){
						$svc[1]['list'][1]['title'] = $l['name'];
						$svc[1]['list'][1]['list']  = $l['sub'];
					}else{
						$svc[1]['list'][0] .= (!empty($svc[1]['list'][0]) ? '&' : '').$l['id'].'='.$l['name'];
					}
				}else{
					$svc[2]['title'] = '기타유료';
					$svc[2]['list'] .= (!empty($svc[2]['list']) ? '&' : '').$l['id'].'='.$l['name'];
				}
			}

			return $svc;
		}

		// 기관서비스 리스트
		function _service_list($code, $voucher = false){
			$sql = 'select m00_mkind as kind, m00_kupyeo_1 as care, m00_kupyeo_2 as bath, m00_kupyeo_3 as nurs
					  from m00center
					 where m00_mcode  = \''.$code.'\'
					   and m00_del_yn = \'N\'';

			$this->query($sql);
			$this->fetch();
			$row_count = $this->row_count();
			$id = 0;

			for($i=0; $i<$row_count; $i++){
				$row = $this->select_row($i);

				if ($row['kind'] == '0'){
					$list[$id] = array('kind'=>$row['kind'],'code'=>'200','name'=>$this->kind_name_svc('200'), 'use'=>$row['care']); $id++;
					$list[$id] = array('kind'=>$row['kind'],'code'=>'500','name'=>$this->kind_name_svc('500'), 'use'=>$row['bath']); $id++;
					$list[$id] = array('kind'=>$row['kind'],'code'=>'800','name'=>$this->kind_name_svc('800'), 'use'=>$row['nurs']); $id++;
				}else{
					$list[$id] = array('kind'=>$row['kind'],'code'=>(20+$row['kind']),'name'=>$this->kind_name_svc($row['kind']), 'use'=>'Y'); $id++;
				}
			}

			$this->row_free();

			if ($voucher){
				$list[$id] = array('kind'=>'A','code'=>'31','name'=>$this->kind_name_svc('31'), 'use'=>'Y'); $id++;
				$list[$id] = array('kind'=>'B','code'=>'32','name'=>$this->kind_name_svc('32'), 'use'=>'Y'); $id++;
				$list[$id] = array('kind'=>'C','code'=>'33','name'=>$this->kind_name_svc('33'), 'use'=>'Y'); $id++;
			}

			return $list;
		}

		// 기관명
		function kind_name($list, $cd, $type = 'code'){
			for($i=0; $i<sizeof($list); $i++){
				if ($cd == $list[$i][$type]){
					$result = $list[$i]['name'];
					break;
				}
			}

			return $result;
		}

		function kind_name_svc($cd){
			switch($cd){
				case '0':
					return '재가요양';
					break;
				case '1':
					return '가사간병';
					break;
				case '2':
					return '노인돌봄';
					break;
				case '3':
					return '산모신생아';
					break;
				case '4':
					return '장애인활동지원';
					break;
				case '6':
					return '재가지원';
					break;
				case 'S':
					return '재가지원';
					break;
				case 'R':
					return '자원연계';
					break;
				case 'A':
					return '산모유료';
					break;
				case 'B':
					return '병원간병';
					break;
				case 'C':
					return '기타비급여';
					break;

				case '200':
					return '방문요양';
					break;
				case '500':
					return '방문목욕';
					break;
				case '800':
					return '방문간호';
					break;
				case '21':
					return '가사간병';
					break;
				case '22':
					return '노인돌봄';
					break;
				case '23':
					return '산모신생아';
					break;
				case '24':
					return '장애인활동지원';
					break;
				case '31':
					return '산모유료';
					break;
				case '32':
					return '병원간병';
					break;
				case '33':
					return '기타비급여';
					break;
				default:
					return '기타';
			}
		}

		function _svcNm($cd){
			return $this->kind_name_svc($cd);
		}

		function _svcSubNm($cd){
			return $this->kind_name_sub($this->_svcNm($cd));
		}


		# 실적관리 수급내역(수급자, 요양사)에서 호출
		# 2012.3.8 추가

		function kind_name_svc2($cd, $svc){

			switch($svc){
				case '0':
					return '재가요양';
					break;
				case '1':
					return '가사간병';
					break;
				case '2':
					return '노인돌봄';
					break;
				case '3':
					return '산모신생아';
					break;
				case '4':
					return '장애인활동지원';
					break;
				case '6':
					return '재가지원';
					break;
				case 'A':
					return '산모유료';
					break;
				case 'B':
					return '병원간병';
					break;
				case 'C':
					return '기타비급여';
					break;

				case '200':
					if($cd == '0'){
						# m03_mkind 0일 경우
						return '방문요양';
					}else {
						# m03_mkind 4일 경우
						return '활동지원';
					}
					break;
				case '500':
					return '방문목욕';
					break;
				case '800':
					return '방문간호';
					break;
				case '21':
					return '가사간병';
					break;
				case '22':
					return '노인돌봄';
					break;
				case '23':
					return '산모신생아';
					break;
				case '24':
					return '장애인활동지원';
					break;
				case '31':
					return '산모유료';
					break;
				case '32':
					return '병원간병';
					break;
				case '33':
					return '기타비급여';
					break;
			}
		}

		function kind_name_sub($name){
			switch($name){
				case '재가요양':
					return '재가';
					break;
				case '방문요양':
					return '요양';
					break;
				case '방문목욕':
					return '목욕';
					break;
				case '방문간호':
					return '간호';
					break;
				case '가사간병':
					return '간병';
					break;
				case '노인돌봄':
					return '돌봄';
					break;
				case '산모신생아':
					return '산모';
					break;
				case '장애인활동지원':
					return '장애';
					break;
				case '산모유료':
					return '산모';
					break;
				case '병원간병':
					return '병간';
					break;
				case '기타비급여':
					return '기타';
					break;
				case '재가지원':
					return '지원';
					break;
				case '자원연계':
					return '자원';
					break;
				default:
					return $name;
			}
		}

		//기관분류코드
		function kind_code($list, $cd, $type = 'code'){
			$cnt = sizeof($list);

			for($i=0; $i<$cnt; $i++){
				if ($type == 'code'){
					if ($cd == $list[$i]['id']){
						$result = $list[$i]['code'];
						break;
					}
				}else{
					if ($cd == $list[$i]['code']){
						$result = $list[$i]['id'];
						break;
					}
				}
			}

			return $result;
		}

		// 기관구분리스트
		function kind_list_name($code){
			$sql = "select m00_mkind
					,      m00_cname
					  from m00center
					 where m00_mcode = '$code'";
			$this->query($sql);
			$this->fetch();
			$row_count = $this->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $this->select_row($i);

				$list[$i]['code'] = $row[0];
				$list[$i]['name'] = $row[1];
			}

			$this->row_free();

			return $list;
		}

		// 요양보호사 입,퇴사일
		function member_date($code, $kind, $jumin){
			$sql = "select m02_yipsail
					,      case when m02_ytoisail != '' then m02_ytoisail else '99999999' end
					  from m02yoyangsa
					 where m02_ccode  = '$code'
					   and m02_mkind  = '$kind'
					   and m02_yjumin = '$jumin'";
			$date = $this->get_array($sql);

			return $date;
		}

		// 수급자 계약일,종료일
		function client_date($code, $kind, $jumin, $date = ''){
			/*
			$sql = "select min(m03_gaeyak_fm) as dt_form
					,      max(m03_gaeyak_to) as dt_to
					  from m03sugupja
					 where m03_ccode = '$code'
					   and m03_jumin = '$jumin'";

			if ($kind != ''){
				$sql .= " and m03_mkind = '$kind'";
			}

			$date = $this->get_array($sql);
			*/

			if (empty($date)) $date = date('Ymd');

			$sql = 'select date_format(min(from_dt),\'%Y%m%d\') as dt_form
					,      date_format(max(to_dt),  \'%Y%m%d\') as dt_to
					  from client_his_svc
					where org_no = \''.$code.'\'
					  and jumin  = \''.$jumin.'\'';

			if ($kind != ''){
				$sql .= ' and svc_cd = \''.$kind.'\'';
			}

			$sql .= ' and left(date_format(from_dt,\'%Y%m%d\'),'.strlen($date).') <= \''.$date.'\'
					  and left(date_format(to_dt,  \'%Y%m%d\'),'.strlen($date).') >= \''.$date.'\'';

			$date = $this->get_array($sql);

			return $date;
		}

		// 근무일수
		function work_count($code, $kind, $yoynagsa, $year, $month){
			$io_date = $this->member_date($code, $kind, $yoynagsa);
			$i_date  = $io_date[0];
			$o_date  = $io_date[1];

			unset($io_date);

			$sql = "select count(distinct t01_sugup_date)
					  from t01iljung
					 inner join t13sugupja
						on t13_ccode    = t01_ccode
					   and t13_mkind    = t01_mkind
					   and t13_jumin    = t01_jumin
					   and t13_pay_date = left(t01_sugup_date, 6)
					   and t13_type     = '2'
					 where t01_ccode = '$code'
					   and t01_mkind = '$kind'
					   and t01_sugup_date like '$year$month%'
					   and t01_sugup_date between '$i_date' and '$o_date'
					   and '$yoynagsa' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
					   and t01_del_yn = 'N'
					   and t01_svc_subcode = '200'
					   and t01_status_gbn = '1'";

			$value = $this->get_data($sql);

			return $value;
		}

		// 배치로그를 저장한다.
		function batch_log($type, $stnd_yymm, $run_time, $start_dt, $start_tm, $job_msg){
			$uri_array = explode('/',$_SERVER["REQUEST_URI"]);
			$uri_array = explode('?',$uri_array[sizeOf($uri_array)-1]);

			list($usec, $sec) = explode(" ",microtime());

			$code		= $_SESSION['userCenterCode'];
			$pgm_id		= $uri_array[0];
			$user		= $_SESSION['userCode'];
			$err_cd		= $this->error_no;
			$err_msg	= addslashes($this->error_msg);
			$err_query	= addslashes($this->error_query);
			$end_sec	= (float)$usec + (float)$sec;
			$run_time	= $end_sec - $run_time;
			$end_dt		= date('Y-m-d', mktime());
			$end_tm		= date('H:i:s', mktime());

			if ($type == '') $type = $this->get_program_type($pgm_id);

			if ($query != ''){
				$err_query = addslashes($query);
			}

			if ($err_cd == 0){
				$err_msg   = '';
				$err_query = '';
			}

			$sql = "insert into batch_job_log (
					 id
					,org_no
					,stnd_yymm
					,pgm_id
					,logging_tmstmp
					,create_id
					,create_dt
					,job_type
					,job_run_tm
					,job_start_dt
					,job_start_tm
					,job_end_dt
					,job_end_tm
					,job_msg
					,err_cd
					,err_msg
					,err_query) values (
					 NULL
					,'$code'
					,'$stnd_yymm'
					,'$pgm_id'
					,now()
					,'$user'
					,date_format(now(), '%Y-%m-%d')
					,'$type'
					,'$run_time'
					,'$start_dt'
					,'$start_tm'
					,'$end_dt'
					,'$end_tm'
					,'$job_msg'
					,'$err_cd'
					,'$err_msg'
					,'$err_query')";

			unset($uri_array);

			return $this->execute($sql);
		}

		function get_program_type($pgm_id){
			switch($pgm_id){
			case 'result_confirm.php':
				$type = 1;
				break;
			case 'result_salary.php':
				$type = 2;
				break;
			case 'result_finish_confirm_cancel_ok.php':
				$type = 3;
				break;
			default:
				$type = 0;
			}

			return $type;
		}

		function get_job_name($job_id){
			switch($job_id){
			case '1':
				$name = '실적일괄확정';
				break;
			case '2':
				$name = '급여일괄계산';
				break;
			case '3':
				$name = '실적일괄확정취소';
				break;
			case '4':
				$name = '급여일괄계산취소';
				break;
			default:
				$name = '기타';
			}

			return $name;
		}

		// 실적마감여부
		function get_closing_act($code, $yymm){
			$today = Date('Ymd');

			if ($code == '1234' || $today >= '20120816'){
				return 'N';
			}

			$sql = "select act_cls_flag
					,      act_cls_dt_from
					,      act_bat_conf_flag
					  from closing_progress
					 where org_no       = '$code'
					   and closing_yymm = '$yymm'";

			$closing = $this->get_array($sql);

			if (is_array($closing)){
				if ($closing[2] != 'Y') $closing[2] = 'N';

				if ($closing[0] == 'N' && $closing[2] == 'N'){
					$close_yn = 'N';
				}else{
					$close_yn = 'Y';
				}
			}else{
				$close_yn = 'N';
			}

			if ($close_yn != 'Y') $close_yn = 'N';

			return $close_yn;
		}
		function _isCloseResult($code, $yymm){
			return $this->get_closing_act($code, $yymm);
		}

		// 급여마감여부
		function get_closing_salary($code, $yymm){
			$sql = "select salary_cls_flag
					  from closing_progress
					 where org_no       = '$code'
					   and closing_yymm = '$yymm'";

			$close_yn = $this->get_data($sql);

			if ($close_yn != 'Y') $close_yn = 'N';

			return $close_yn;
		}
		function _isCloseSalary($code, $yymm){
			return $this->get_closing_salary($code, $yymm);
		}

		// 기관 기준시간 및 시급
		function get_standard($code, $kind = ''){
			if ($kind == '') $kind = $this->center_kind($code);

			$sql = "select m00_day_work_hour as time, m00_day_hourly as pay
					  from m00center
					 where m00_mcode = '$code'
					   and m00_mkind = '$kind'";

			$fetch_type = $this->fetch_type;
			$this->fetch_type = 'assoc';
			$data = $this->get_array($sql);
			$this->fetch_type = $fetch_type;

			return $data;
		}

		#############################################
		#
		# 소득등급 출력
		#
		function income_lvl($svc_id, $cur_cd, $lvl_cd, $dt = ''){
			if (empty($dt)) $dt = date('Y-m-d', mktime());

			switch($svc_id){
				case 21:
					$svc_gbn = '0';
					$svc_cd  = '1';
					$svc_val = '""';
					break;
				case 24:
					$svc_gbn = '__object_get_value("'.$svc_id.'_gbn")';
					$svc_cd  = '__object_get_value("'.$svc_id.'_lvl")';
					$svc_val = '__object_get_value("'.$svc_id.'_gbn2")';
					break;
				default:
					$svc_gbn = '__object_get_value("'.$svc_id.'_gbn")';
					$svc_cd  = '1';
					$svc_val = '""';
			}

			$sql = "select lvl_cd, lvl_id, lvl_nm
					  from income_lvl
					 where lvl_cd in ($lvl_cd)
					   and lvl_from_dt <= '$dt'
					   and lvl_to_dt   >= '$dt'";

			$this->query($sql);
			$this->fetch();

			$row_count = $this->row_count();

			$tbl = '<table style=\'width:100%;;\'>';

			$tr = false;

			for($i=0; $i<$row_count; $i++){
				$row = $this->select_row($i);

				if ($i % 2 == 0){
					if ($tr) $tbl .= '</tr>';

					$tr = true;
					$tbl .= '<tr>';
				}

				$tbl .= '<td class=\'bottom last\' style=\'height:22px; line-height:1em;\'>';
				$tbl .= '<input name=\''.$svc_id.'_kind\' type=\'radio\' class=\'radio\' value=\''.$row['lvl_id'].'\' tag=\''.$cur_cd.'\' code=\''.$row['lvl_cd'].'\' onclick=\'check_time("'.$svc_id.'",'.$svc_gbn.','.$svc_cd.','.$svc_val.');\' '.($row['lvl_id'] == $cur_cd ? 'checked' : '').'>';
				$tbl .= '<a href=\'#\' onclick=\'check_obj("'.$svc_id.'_kind", '.$i.', "'.$svc_id.'",'.$svc_gbn.','.$svc_cd.','.$svc_val.'); return false;\' >'.$row['lvl_nm'].'</a>';
				$tbl .= '</td>';
			}

			$tbl .= '</tr>';
			$tbl .= '</table>';

			$this->row_free();

			return $tbl;
		}

		#############################################
		#
		# 소득등급 명칭
		#
		function income_nm($svc_id, $lvl_cd){
			$lvl_list = $this->income_lvl_cd($svc_id);

			if ($lvl_list != ''){
				$sql = "select lvl_nm
						  from income_lvl
						 where lvl_cd in ($lvl_list)
						   and lvl_id  = '$lvl_cd'";

				$lvl_nm = $this->get_data($sql);
			}else{
				$lvl_nm = '';
			}

			return $lvl_nm;
		}

		function income_lvl_cd($svc_id){
			switch($svc_id){
				case 21:
					$lvl = "'21', '22', '99'";
					break;
				case 22:
					$lvl = "'21', '22', '23', '99'";
					break;
				case 23:
					$lvl = "'24', '25', '99'";
					break;
				case 24:
					$lvl = "'21', '22', '26', '27', '28', '29', '99'";
					break;
				default:
					$lvl = "";
			}

			return $lvl;
		}

		#############################################
		#
		# 수가정보
		#
		function _suga_info($param){
			$sql = "select service_gbn, service_lvl, service_cost
					  from suga_service
					 where org_no           = '".$param['code']."'
					   and service_kind     = '".$param['kind']."'
					   and service_code     = '".$param['suga']."'
					   and service_from_dt <= '".$param['date']."'
					   and service_to_dt   >= '".$param['date']."'";

			return $this->get_array($sql);
		}

		#
		#############################################

		function _table_kind($table, $col1, $col2, $col3 = ''){
			return '(select min('.$col2.') from '.$table.' as tmp where tmp.'.$col1.' = '.$table.'.'.$col1.(!empty($col3) ? ' and tmp.'.$col3.' = \'N\'' : '').')';
		}

		function _center_kind(){
			return '(select min(m00_mkind) from m00center as tmp where tmp.m00_ccode = m00center.m00_ccode and tmp.m00_del_yn = \'N\')';
		}

		function _cneter_no($code, $kind = ''){
			$sql = 'select m00_code1
					  from m00center
					 where m00_mcode = \''.$code.'\'
					   and m00_del_yn = \'N\'';

			if (!empty($kind)){
				$sql .= ' and m00_mkind = \''.$kind.'\'';
			}else{
				$sql .= ' and m00_mkind = '.$this->_center_kind();
			}

			$center_no = $this->get_data($sql);

			return $center_no;
		}

		function _center_manager($code){
			$sql = 'select m00_mname
					  from m00center
					 where m00_mcode = \''.$code.'\'
					   and m00_del_yn = \'N\'
					   and m00_mkind = '.$this->_center_kind();

			$center_manager = $this->get_data($sql);

			return $center_manager;
		}

		function _mem_kind(){
			//return '(select min(m02_mkind) from m02yoyangsa as tmp where tmp.m02_ccode = m02yoyangsa.m02_ccode and tmp.m02_yjumin = m02yoyangsa.m02_yjumin and tmp.m02_del_yn = \'N\')';
			return '0';
		}

		function _member_kind($del_flag = ''){
			//return '(select min(m02_mkind) from m02yoyangsa as tmp where tmp.m02_ccode = m02yoyangsa.m02_ccode and tmp.m02_yjumin = m02yoyangsa.m02_yjumin '.($del_flag == '' ? 'and tmp.m02_del_yn = \'N\'' : '').')';
			return '0';
		}

		function _client_kind($del_flag = ''){
			//return '(select min(m03_mkind) from m03sugupja as tmp where tmp.m03_ccode = m03sugupja.m03_ccode and tmp.m03_jumin = m03sugupja.m03_jumin '.($del_flag == '' ? 'and tmp.m03_del_yn = \'N\'' : '').')';
			return '0';
		}

		function _center_kind_cd($code){
			$sql = 'select min(m00_mkind)
					  from m00center
					 where m00_ccode  = \''.$code.'\'
					   and m00_del_yn = \'N\'';

			return $this->get_data($sql);
		}

		function _mem_kind_cd($code, $ssn){
			$sql = "select min(m02_mkind)
					  from m02yoyangsa
					 where m02_ccode  = '$code'
					   and m02_yjumin = '$ssn'
					   and m02_del_yn = 'N'";

			return $this->get_data($sql);
		}

		function _member_kind_cd($code, $ssn){
			$sql = "select min(m02_mkind)
					  from m02yoyangsa
					 where m02_ccode  = '$code'
					   and m02_yjumin = '$ssn'
					   and m02_del_yn = 'N'";

			return $this->get_data($sql);
		}

		function _client_kind_cd($code, $ssn){
			$sql = "select min(m03_mkind)
					  from m03sugupja
					 where m03_ccode  = '$code'
					   and m03_jumin  = '$ssn'
					   and m03_del_yn = 'N'";

			return $this->get_data($sql);
		}





		/**************************************************

			고객 등록 서비스 리스트

		**************************************************/
			// 기관구분리스트
			function client_kind_list($code, $jumin){
				$sql = "select m03_mkind
						,      case m03_mkind when '0' then '재가요양'
											  when '1' then '가사간병'
											  when '2' then '노인돌봄'
											  when '3' then '산모신생아'
											  when '4' then '장애인활동지원'
											  when '5' then '시설'
											  when 'A' then '산모유료'
											  when 'B' then '병원간병'
											  when 'C' then '기타비급여' else '--' end
						  from m03sugupja
						 where m03_ccode  = '$code'
						   and m03_jumin  = '$jumin'
						   and m03_del_yn = 'N'";

				$this->query($sql);
				$this->fetch();
				$row_count = $this->row_count();

				for($i=0; $i<$row_count; $i++){
					$row = $this->select_row($i);

					$list[$i]['code'] = $row[0];
					$list[$i]['name'] = $row[1];
				}

				$this->row_free();

				return $list;
			}
		/*************************************************/



		/*********************************************************
			수가정보
			[code]			=> 수가코드
			[name]			=> 수가명
			[cost]			=> 단가
			[evening_cost]	=> 연장단가
			[night_cost]	=> 야간단가
			[total_cost]	=> 하볘
			[sudang_pay]	=> 수당
			[evening_time]	=> 연장시간
			[night_time]	=> 야간시간
			[evening_yn]	=> 연장여부
			[night_yn]		=> 야간여부
			[holiday_yn]	=> 휴일여부
		*********************************************************/
		function _find_suga_($code, $svc_cd, $date, $from_time, $to_time, $proctime, $family_yn = 'N', $bath_kind = ''){
			// 입력시간
			$from_time   = str_replace(':', '', $from_time);
			$tmp_from[0] = substr($from_time, 0, 2);
			$tmp_from[1] = substr($from_time, 2, 2);
			$from_min = intval($tmp_from[0]) * 60 + intval($tmp_from[1]);

			$to_time   = str_replace(':', '', $to_time);
			$tmp_to[0] = substr($to_time, 0, 2);
			$tmp_to[1] = substr($to_time, 2, 2);
			$to_min   = intval($tmp_to[0]) * 60 + intval($tmp_to[1]);

			if ($to_min < $from_min) $to_min += 24 * 60;

			// 요일
			$date  = str_replace('.', '', $date);
			$date  = str_replace('-', '', $date);
			$tmp_dt[0] = substr($date, 0, 4);
			$tmp_dt[1] = substr($date, 4, 2);
			$tmp_dt[2] = substr($date, 6, 2);
			$weekday = date('w', strtotime($tmp_dt[0].'-'.$tmp_dt[1].'-'.$tmp_dt[2]));

			// 휴일여부
			$holiday_yn = 'N';
			if ($weekday == 0) $holiday_yn = 'Y';
			if ($holiday_yn != 'Y'){
				$sql = 'select count(*)
						  from tbl_holiday
						 where mdate = \''.$date.'\'';

				if ($this->get_data($sql) > 0){
					$holiday_yn = 'Y';
				}
			}



			// 구분 시간값등을 확인
			$TN  = $proctime;
			$ETN = 0;
			$NTN = 0;
			$ETNtime = 0; //야간시간
			$NTNtime = 0; //심야시간
			$ERang1 = 18 * 60;
			$ERang2 = 21 * 60 + 59;
			$NRang1 = 22 * 60;
			$NRang2 = 24 * 60 + 3 * 60 + 59;
			$NRang3 = 3 * 60 + 59;
			$Egubun  = 'N'; //야간여부
			$Ngubun  = 'N'; //심야여부

			$EAMT  = 0;
			$NAMT  = 0;
			$TAMT  = 0;
			$EFrom = 0;
			$ETo   = 0;
			$NFrom = 0;
			$NTo   = 0;

			/*********************************************************
				종료시간을 시작시간에서 진행시간을 더한 값으로 변경한다.
			*********************************************************/
			if ($svc_cd == '200') $to_min = $from_min + $TN;

			if (intval($to_min) - intval($from_min) > 8.5 * 60)
				$to_min = intval($from_min) + 8.5 * 60;

			$EFrom = $from_min - $ERang1;

			# 2012.02.02 아래와 같이 수정함.
			#$ETo   = $to_min - $ERang1;

			/*********************************************************
				근무시간이 510분이상 넘어갈 경우 510분까지만 인정한다.
			*********************************************************/
			if (intval($to_min) - intval($from_min) > 8.5 * 60){
				$ETo = (intval($from_min) + 8.5 * 60) - $ERang1;

			}else if (intval($to_min) - intval($from_min) > 4.5 * 60){ //270분초과시 30분 빼기
				//$ETo = (intval($from_min) + (intval($to_min) - intval($from_min) /*- 30*/)) - $ERang1;

				$liCutMin = 0;

				if ($to_min > $ERang1){
					if ($to_min - $from_min <= 270){
						$liCutMin = 30;
					}
				}

				$ETo = (intval($from_min) + (intval($to_min) - intval($from_min) - $liCutMin)) - $ERang1;

			}else if (intval($to_min) - intval($from_min) >= 4 * 60 && intval($to_min) - intval($from_min) <= 4.5 * 60){ //240분~270분시 240분적용
				$ETo = (intval($from_min) + 4 * 60) - $ERang1;

			}else{
				$ETo = $to_min - $ERang1;
			}

			if ($svc_cd == '200'){
				// 요양 중 동거가 아닐경우만 야간및 심야 할증을 실행한다.
				if ($family_yn != 'Y'){
					if ($from_min < $NRang3){
						$NFrom   = $NRang3 - $from_min;
						$NTo     = $NRang3 - $to_min;
						$NTNtime = $NFrom - ($NTo < 0 ? 0 : $NTo) + 1;
					}else{
						$NFrom   = $from_min - $NRang1;
						$NTo     = $to_min - $NRang1 + 1;
						$NTNtime = $NTo - ($NFrom < 0 ? 0 : $NFrom);
					}

					$ETNtime = $ETo - ($EFrom < 0 ? 0 : $EFrom);

					$NTNtime = $NTNtime < 0 ? 0 : $NTNtime;
					$NTNtime = floor($NTNtime - ($NTNtime % 30));
					$ETNtime = $ETNtime < 0 ? 0 : $ETNtime - $NTNtime;

					if ($NTNtime > 480) $NTNtime = 480;
					if ($ETNtime > 480) $ETNtime = 480;

					//새벽 6시 이전에 근무한 시간을 야간으로 적용한다.
					if ($from_min < 360){
						$tmpTT = 360 - $to_min;

						if ($tmpTT < 0) $tmpTT = 0;

						$NTNtime = 360 - $from_min - $tmpTT;
					}
				}else{
					$NTNtime = 0;
					$ETNtime = 0;
				}
			}else{
				// 목욕 및 간호는 할증을 실행하자 않는다.
				$NTNtime = 0;
				$ETNtime = 0;
			}

			// 야간과 심야의 계산시간을 30분 단위로한다.
			$NTNtime = floor($NTNtime - ($NTNtime % 30));
			$ETNtime = floor($ETNtime - ($ETNtime % 30));

			$TN = floor($TN - ($TN % 30));

			if ($svc_cd == '200'){
				switch($TN){
					case '30' : $TN = 1; break;
					case '60' : $TN = 2; break;
					case '90' : $TN = 3; break;
					case '120': $TN = 4; break;
					case '150': $TN = 5; break;
					case '180': $TN = 6; break;
					case '210': $TN = 7; break;
					case '240': $TN = 8; break;
					default   : $TN = 9; break;
				}
			}else if ($svc_cd == '800'){
				if ($TN < 30){
					$TN = 1;
				}else if ($TN < 60){
					$TN = 2;
				}else{
					$TN = 3;
				}
			}else{
				if ($bath_kind == '2' || $bath_kind == '3')
					$TN = 'K';
				else
					$TN = 'F';
			}

			switch($ETNtime){
				case 30 : $ETN = 1; break;
				case 60 : $ETN = 2; break;
				case 90 : $ETN = 3; break;
				case 120: $ETN = 4; break;
				case 150: $ETN = 5; break;
				case 180: $ETN = 6; break;
				case 210: $ETN = 7; break;
				case 240: $ETN = 8; break;
				default : $ETN = 0;
			}

			switch($NTNtime){
				case 30 : $NTN = 1; break;
				case 60 : $NTN = 2; break;
				case 90 : $NTN = 3; break;
				case 120: $NTN = 4; break;
				case 150: $NTN = 5; break;
				case 180: $NTN = 6; break;
				case 210: $NTN = 7; break;
				case 240: $NTN = 8; break;
				default : $NTN = 0;
			}

			$sugaKey = '';
			$sugaGubun = '';

			if ($svc_cd == '200'){
				// 요양
				if ($family_yn == 'Y'){
					$sugaGubun = 'CCWC';
				}else if ($holiday_yn == 'Y'){
					$sugaGubun = 'CCHS';
				}else{
					$sugaGubun = 'CCWS';
				}
			}else if ($svc_cd == '500'){
				// 목욕
				$sugaGubun = 'CB';
			}else{
				// 간호
				if ($holiday_yn != 'Y'){
					$sugaGubun = 'CNW';
				}else{
					$sugaGubun = 'CNH';
				}

				$sugaGubun .= 'S';
			}

			$sugaKey = $sugaGubun.$TN;

			if ($svc_cd == '500'){
				if ($bath_kind == '3')
					$sugaKey .= 'D2';
				else
					$sugaKey .= 'D1';
			}

			$dt = $tmp_dt[0].$tmp_dt[1].$tmp_dt[2];

			$sql = 'select m01_suga_cont as name
					,      m01_suga_value as cost
					  from m01suga
					 where m01_mcode  = \''.$code.'\'
					   and m01_mcode2 = \''.$sugaKey.'\'
					   and m01_sdate <= \''.$dt.'\'
					   and m01_edate >= \''.$dt.'\'
					 union all
					select m11_suga_cont as name
					,      m11_suga_value as cost
					  from m11suga
					 where m11_mcode  = \''.$code.'\'
					   and m11_mcode2 = \''.$sugaKey.'\'
					   and m11_sdate <= \''.$dt.'\'
					   and m11_edate >= \''.$dt.'\'';

			$tmp = $this->get_array($sql);

			$sugaName  = $tmp['name']; //명칭
			$sugaPrice = $tmp['cost']; //단가

			if ($svc_cd != '200'){
				$sql = 'select m21_svalue
						  from m21sudang
						 where m21_mcode  = \''.$code.'\'
						   and m21_mcode2 = \''.$sugaKey.'\'';

				$sudangPrice = $this->get_data($sql);
			}else{
				$sudangPrice = 0;
			}

			// 2011년 7월 1일부터 목욕 적용수가를 시간기준으로 변경한다.(2011.07.11 적용)
			if ($dt >= '201107'){
				if ($svc_cd == '500'){
					$tmp_time = $to_min - $from_min;

					if ($tmp_time < 40){
						$sugaPrice = 0;
					}else if ($tmp_time >= 40 && $tmp_time < 60){
						$sugaPrice = $sugaPrice * 80 / 100;
						$sugaPrice = floor($sugaPrice - ($sugaPrice % 10));
					}
				}
			}

			#var tempValue = new Array();
			#var tempTime  = new Array();
			$tempIndex = 0;

			if ($TN == 9){
				// 270분 이상일 경우 수가를 계산
				$tempFmH = intval($tmp_from[0]);
				$tempFmM = intval($tmp_from[1]);
				$tempToH = intval($tmp_to[0]);
				$tempToM = intval($tmp_to[1]);

				if ($tempFmH > $tempToH) $tempToH = $tempToH + 24;
				$tempFmH = $tempFmH * 60 + $tempFmM;
				$tempToH = $tempToH * 60 + $tempToM - $tempFmH;

				/*********************************************************
					최대 8시간 30분까지만 허용한다.
				*********************************************************/
				if ($tempToH > 8.5 * 60) $tempToH = 8.5 * 60;

				$tempL = floor($tempToH - ($tempToH % 30)) / 30;
				$tempK = 0;
				$temp_first = false;

				$sugaPrice = 0;

				while(1){
					if ($tempL >= 8){
						$tempK = 8;
					}else if ($tempL == 0 || $tempK == 0){
						break;
					}else{
						$tempK = $tempL % 8;
					}
					$tempL = $tempL - $tempK;

					if (!$temp_first){
						$tempL = $tempL - 1; // 4시간후 30분을 뺀다.
						$temp_first = true;

						if ($tempFmH + ($tempK * 30) >= 1320 ||
							$tempFmH + ($tempK * 30) <  360){
							//심야
							if ($NTNtime > 0) $NTNtime -= 30;
						}else if ($tempFmH + ($tempK * 30) >= 1080){
							//야간
							if ($ETNtime > 0) $ETNtime -= 30;
						}else{
							//주간
						}
					}

					$sql = 'select m01_suga_value as cost
							  from m01suga
							 where m01_mcode  = \''.$code.'\'
							   and m01_mcode2 = \''.$sugaGubun.$tempK.'\'
							   and m01_sdate <= \''.$dt.'\'
							   and m01_edate >= \''.$dt.'\'
							 union all
							select m11_suga_value as cost
							  from m11suga
							 where m11_mcode  = \''.$code.'\'
							   and m11_mcode2 = \''.$sugaGubun.$tempK.'\'
							   and m11_sdate <= \''.$dt.'\'
							   and m11_edate >= \''.$dt.'\'';

					$tempValue[$tempIndex] = $this->get_data($sql);
					$tempTime[$tempIndex]  = $tempK;

					$sugaPrice += $tempValue[$tempIndex]; //단가
					$tempIndex ++;
				}
			}

			$temp_e = 0;
			$i = 0;

			if ($holiday_yn != 'Y'){
				if ($NTNtime > 0){
					if ($sugaGubun != 'HS' && $sugaGubun != 'HD'){
						if ($TN == 9){
							$temp_e = $NTNtime / 30;
							#$i = sizeof($tempValue) - 1;
							$liMax = sizeof($tempValue) - 1;
							$i = 0;

							$NAMT = 0;

							while(1){
								#if ($i < 0) break;
								if ($i > $liMax) break;
								if ($temp_e <= 0) break;

								if ($tempTime[$i] >= $temp_e){
									$NAMT += floor(($tempValue[$i] / $tempTime[$i] * $temp_e * 0.3));
									break;
								}else{
									$NAMT += floor($tempValue[$i] * 0.3);
									$temp_e -= $tempTime[$i];
								}

								#$i--;
								$i++;
							}
						}else{
							$NAMT = floor(($sugaPrice * ($NTN / $TN)) * 0.3);
						}

						//$NAMT = round($NAMT / 10) * 10; //반올림
						$NAMT = round($NAMT);
					}
					$Ngubun = 'Y';
				}

				if ($ETNtime > 0){
					if ($sugaGubun != 'HS' && $sugaGubun != 'HD'){
						if ($TN == 9){
							$temp_e = $ETNtime / 30;

							//if ($i == 0) $i = sizeof($tempValue) - 1;
							if ($i == 0){
								$i = sizeof($tempValue) - 1;
							}else{
								if ($tempTime[$i] <= $temp_e){
									$i --;
								}

								if ($i < 0) $i = 0;
							}

							$EAMT = 0;

							while(1){
								if ($i < 0) break;
								if ($temp_e <= 0) break;

								if ($tempTime[$i] >= $temp_e){
									$EAMT += floor(($tempValue[$i] / $tempTime[$i] * $temp_e * 0.2));
									break;
								}else{
									$EAMT += floor($tempValue[$i] * 0.2);
									$temp_e -= $tempTime[$i];
								}

								$i--;
							}
						}else{
							$EAMT = floor(($sugaPrice * ($ETN / $TN)) * 0.2);
						}

						//$EAMT = round($EAMT / 10) * 10; //반올림
						$EAMT = round($EAMT);
					}
					$Egubun = 'Y';
				}
			}

			if ($TN == 9){
				//$TAMT = intval($sugaPrice) + intval($EAMT) + intval($NAMT);
				//$TAMT = floor($TAMT - ($TAMT % 10));
				$TAMT = round((intval($sugaPrice) + intval($EAMT) + intval($NAMT)) / 10) * 10;
			}else{
				$TAMT = round((intval($sugaPrice) + intval($EAMT) + intval($NAMT)) / 10) * 10;
			}

			$suga = array('code'		=>$sugaKey
						 ,'name'		=>$sugaName
						 ,'cost'		=>$sugaPrice
						 ,'evening_cost'=>$EAMT
						 ,'night_cost'	=>$NAMT
						 ,'total_cost'	=>$TAMT
						 ,'sudang_pay'	=>$sudangPrice
						 ,'evening_time'=>$ETNtime
						 ,'night_time'	=>$NTNtime
						 ,'evening_yn'	=>$Egubun
						 ,'night_yn'	=>$Ngubun
						 ,'holiday_yn'	=>$holiday_yn);

			return $suga;
		}


		/*********************************************************

			테이블의 필드 리스트

		*********************************************************/
		function _field($tbl_nm){
			$sql = 'desc '.$tbl_nm;

			$this->query($sql);
			$this->fetch();

			$row_count = $this->row_count();
			$field = '';

			for($i=0; $i<$row_count; $i++){
				$row = $this->select_row($i);
				$field .= (!empty($field) ? ',' : '').$row['Field'];
			}

			$this->row_free();

			return $field;
		}



		/*********************************************************

			선입금 리스트

		*********************************************************/
		function _ahead_list($code, $jumin){
			$sql = 'select unpaid_deposit.deposit_ent_dt as ent_dt
					,      unpaid_deposit.deposit_seq as ent_seq
					,      unpaid_deposit.deposit_amt - sum(unpaid_deposit_list.deposit_amt) as ahead_amt
					  from unpaid_deposit
					  left join unpaid_deposit_list
						on unpaid_deposit_list.org_no = unpaid_deposit.org_no
					   and unpaid_deposit_list.deposit_ent_dt = unpaid_deposit.deposit_ent_dt
					   and unpaid_deposit_list.deposit_seq = unpaid_deposit.deposit_seq
					 where unpaid_deposit.org_no        = \''.$code.'\'
					   and unpaid_deposit.deposit_jumin = \''.$jumin.'\'
					   and unpaid_deposit.del_flag      = \'N\'
					 group by unpaid_deposit.deposit_ent_dt, unpaid_deposit.deposit_seq
					having ahead_amt > 0';

			$this->query($sql);
			$this->fetch();

			$rowCount = $this->row_count();

			for($r=0; $r<$rowCount; $r++){
				$row = $this->select_row($r);
				$ahead[sizeof($ahead)] = array('entDt'	=>$row['ent_dt']
											  ,'entSeq'	=>$row['ent_seq']
											  ,'amt'	=>$row['ahead_amt']);
			}

			$this->row_free();

			return $ahead;
		}


		/*********************************************************

			금여한도금액

		*********************************************************/
		function _limit_pay($lvl, $date){
			$sql = 'select m91_kupyeo as pay
					  from m91maxkupyeo
					 where m91_code                            = \''.$lvl.'\'
					   and left(m91_sdate, '.strlen($date).') <= \''.$date.'\'
					   and left(m91_edate, '.strlen($date).') >= \''.$date.'\'';

			$pay = $this->get_data($sql);

			return $pay;
		}

		function _limitPay($lvl, $date){
			$sql = 'select m91_kupyeo as pay
					  from m91maxkupyeo
					 where m91_code                            = \''.$lvl.'\'
					   and left(m91_sdate, '.strlen($date).') <= \''.$date.'\'
					   and left(m91_edate, '.strlen($date).') >= \''.$date.'\'';

			$pay = $this->get_data($sql);

			return $pay;
		}


		/*********************************************************

			본사코드

		*********************************************************/
		function _company_code($domain){
			$sql = 'select b00_code
					  from b00branch
					 where b00_com_yn = \'Y\'
					   and b00_domain = \''.$domain.'\'
					 limit 1';

			$cd = $this->get_data($sql);

			return $cd;
		}


		/*********************************************************

			바우처 사용가능여부

		*********************************************************/
		function _is_service($code){
			$sql = 'SELECT	b02_homecare as homecare
					,		b02_voucher as voucher
					,		b02_caresvc AS caresvc
					,		care_support
					,		care_resource
					FROM	b02center
					WHERE	b02_center = \''.$code.'\'';

			$tmp = $this->get_array($sql);

			if (is_numeric(strpos($tmp['voucher'],'Y'))){
				$lbVoucher = true;
			}else{
				$lbVoucher = false;
			}

			$data = array(
					'homecare'=>($tmp['homecare'] == 'Y' ? true : false)
				,	'voucher'=>$lbVoucher /*stristr($tmp['voucher'],'Y')*/
				,	'nurse'=>($tmp['voucher'][0] == 'Y' ? true : false)
				,	'old'=>($tmp['voucher'][1] == 'Y' ? true : false)
				,	'baby'=>($tmp['voucher'][2] == 'Y' ? true : false)
				,	'dis'=>($tmp['voucher'][3] == 'Y' ? true : false)
				,	'careSvc'=>($tmp['caresvc'] == 'Y' ? true : false)
				,	'careSupport'=>($tmp['care_support'] == 'Y' ? true : false)
				,	'careResource'=>($tmp['care_resource'] == 'Y' ? true : false)
			);

			unset($tmp);

			/*
			if ($this->debug){
				$sql = 'SELECT	SUM(CASE WHEN svc_cd = \'11\' THEN 1 ElSE 0 END) AS homecare
						,		SUM(CASE WHEN svc_cd = \'21\' THEN 1 ElSE 0 END) AS nurse
						,		SUM(CASE WHEN svc_cd = \'22\' THEN 1 ElSE 0 END) AS old
						,		SUM(CASE WHEN svc_cd = \'23\' THEN 1 ElSE 0 END) AS baby
						,		SUM(CASE WHEN svc_cd = \'24\' THEN 1 ElSE 0 END) AS dis
						,		SUM(CASE WHEN svc_cd = \'41\' THEN 1 ElSE 0 END) AS care_s
						,		SUM(CASE WHEN svc_cd = \'42\' THEN 1 ElSE 0 END) AS care_r
						FROM	cv_svc_fee
						WHERE	org_no	 = \''.$code.'\'
						AND		svc_gbn	 = \'1\'
						AND		use_yn	 = \'Y\'
						AND		del_flag = \'N\'
						AND		from_dt <= NOW()
						AND		to_dt	>= NOW()';

				$row = $conn->get_array($sql);

				if ($row){
					$data['homecare'] = ($row['homecare'] > 0 ? true : false);

					$data['nurse']	= ($row['nurse'] > 0 ? true : false);
					$data['old']	= ($row['old'] > 0 ? true : false);
					$data['baby']	= ($row['baby'] > 0 ? true : false);
					$data['dis']	= ($row['dis'] > 0 ? true : false);

					if ($data['nurse'] || $data['old'] || $data['baby'] || $data['dis']){
						$data['voucher'] = true;
					}else{
						$data['voucher'] = false;
					}

					$data['careSupport'] = ($row['care_s'] > 0 ? true : false);
					$data['careResource'] = ($row['care_r'] > 0 ? true : false);

					if ($data['careSupport'] || $data['careResource']){
						$data['careSvc'] = true;
					}else{
						$data['careSvc'] = false;
					}

					print_r($data);
				}
			}
			*/

			return $data;
		}


		/*********************************************************

			휴일여부

		*********************************************************/
		function _is_holiday($date){
			$sql = 'select count(*)
					  from tbl_holiday
					 where mdate = \''.$date.'\'';

			if ($this->get_data($sql) > 0)
				return true;
			else
				return false;
		}


		/*********************************************************

			쿼리 실행 후 배열로 리턴

		*********************************************************/
		function _fetch_array($query, $key = ''){
			$tmpType = $this->fetch_type;

			$this->fetch_type = 'assoc';
			$this->query($query);
			$this->fetch();

			$rowCount = $this->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $this->select_row($i);

				if ($key != '')
					$idx = $row[$key];
				else
					$idx = $i;

				$data[$idx] = $row;
			}

			$this->row_free();
			$this->fetch_type = $tmpType;

			if (is_array($data))
				return $data;
			else
				return null;
		}


		/*********************************************************

			카테고리 풀명

		*********************************************************/
		function _category_fullname($id, $code, $gbn = 'name'){
			$sql = 'select code
					,      name
					,      parent
					  from category
					 where mem_cd = \''.$id.'\'
					   and code = \''.$code.'\'
					 order by seq, od_no';

			$category = $this->_fetch_array($sql);

			if (is_null($category)) return;

			foreach($category as $cateIdx => $row){
				$str = $this->_category_fullname($id, $row['parent']);
				$cate .= $str.($str != '' ? ' / ' : '').$row[$gbn];
			}

			return $cate;
		}


		/*********************************************************

			부서리스트

		*********************************************************/
		function _fetch_dept($code){
			$sql = 'select dept_cd as cd
					,      dept_nm as nm
					  from dept
					 where org_no   = \''.$code.'\'
					   and del_flag = \'N\'
					 order by order_seq';

			$this->query($sql);
			$this->fetch();

			$rowCount = $this->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $this->select_row($i);

				$list[$row['cd']] = $row['nm'];
			}

			$this->row_free();

			return $list;
		}


		/*********************************************************

			증빙서번호 찾기

		 *********************************************************/
		function _proofNo($code, $date, $itemCd, $field){
			$date = Str_Replace('-','',$date);

			$sql = 'SELECT proof_no
					  FROM center_'.$field.'
					 WHERE org_no             = \''.$code.'\'
					   AND '.$field.'_item_cd = \''.$itemCd.'\'
					   AND DATE_FORMAT('.$field.'_acct_dt,\'%Y%m%d\') = \''.$date.'\'
					   AND proof_no IS NOT NULL
					 LIMIT 1';
			$proofNo = $this->get_data($sql);

			$sql = 'SELECT COUNT(*)
					  FROM center_'.$field.'
					 WHERE org_no   = \''.$code.'\'
					   AND proof_no = \''.$proofNo.'\'
					   AND DATE_FORMAT('.$field.'_acct_dt,\'%Y%m%d\') = \''.$date.'\'';
			$proofCnt = $this->get_data($sql);

			if ($proofCnt == 0){
				$proofNo = '';
			}

			if (Empty($proofNo)){
				$sql = 'SELECT IFNULL(MAX(proof_no),0)+1
						  FROM center_'.$field.'
						 WHERE org_no     = \''.$code.'\'
						   AND proof_year = \''.SubStr($date,0,4).'\'';

				$proofNo = $this->get_data($sql);
			}

			for($i=StrLen($proofNo)+1; $i<=5; $i++){
				$proofNo = '0'.$proofNo;
			}

			return $proofNo;
		}


		/*********************************************************

			공통로그 작성

		*********************************************************/
		function _logW(){
			$arrUri = explode('/',$_SERVER["REQUEST_URI"]);
			$arrUri = explode('?',$arrUri[sizeOf($arrUri)-1]);

			$code     = $_SESSION['userCenterCode'];
			$usrCd    = $_SESSION['userCode'];
			$remoteIp = $_SERVER['REMOTE_ADDR'];
			$pgmId    = $arrUri[0];

			$sql = 'insert into log_his (
					 org_no
					,pgm_id
					,log_dt
					,user_cd
					,remote_ip) values (
					 \''.$code.'\'
					,\''.$pgmId.'\'
					,now()
					,\''.$usrCd.'\'
					,\''.$remoteIp.'\'
					)';

			$this->execute($sql);
		}


		/*********************************************************
			주야간보호 가능여부
		 *********************************************************/
		function _isDayAndNight($orgNo, $date = ''){
			if (!$date) $date = Date('Ymd');
			$date = str_replace('-','',$date);
			$date = str_replace('.','',$date);

			$sql = 'SELECT	COUNT(*)
					FROM	sub_svc
					WHERE	org_no	= \''.$orgNo.'\'
					AND		svc_cd	= \'5\'
					AND		DATE_FORMAT(from_dt,\'%Y%m%d\') <= \''.$date.'\'
					AND		DATE_FORMAT(to_dt,	\'%Y%m%d\') >= \''.$date.'\'';

			$cnt = $this->get_data($sql);

			if ($cnt > 0){
				return true;
			}else{
				return false;
			}
		}


		/*********************************************************
			복지용구 가능여부
		 *********************************************************/
		function _isWMD($orgNo, $date = ''){
			if (!$date) $date = Date('Ymd');
			$date = str_replace('-','',$date);
			$date = str_replace('.','',$date);

			$sql = 'SELECT	COUNT(*)
					FROM	sub_svc
					WHERE	org_no	= \''.$orgNo.'\'
					AND		svc_cd	= \'7\'
					AND		DATE_FORMAT(from_dt,\'%Y%m%d\') <= \''.$date.'\'
					AND		DATE_FORMAT(to_dt,	\'%Y%m%d\') >= \''.$date.'\'';

			$cnt = $this->get_data($sql);

			if ($cnt > 0){
				return true;
			}else{
				return false;
			}
		}


		/*
		*/
		function GetLivingJuminCd($orgNo, $jumin){
			$jumin = SubStr($jumin,0,6).'2'.SubStr(Date('Y'),2,2).Date('m');

			$nextSeq = 0;

			while(true){
				$tmpJuCd = $jumin.($nextSeq < 10 ? '0' : '').$nextSeq;

				$sql = 'SELECT	COUNT(*)
						FROM	(
								SELECT	m03_jumin AS jumin
								FROM	m03sugupja
								WHERE	m03_ccode = \''.$orgNo.'\'
								UNION	ALL
								SELECT	jumin
								FROM	vuc_baby_due
								WHERE	org_no = \''.$orgNo.'\'
								AND		del_flag = \'N\'
								) AS a
						WHERE	jumin = \''.$tmpJuCd.'\'';

				$tmpCnt = $this->get_data($sql);

				if ($tmpCnt < 1) break;

				$nextSeq ++;
			}

			return $tmpJuCd;
		}


		//내구연한 및 연장기간
		function DurExt($date, $product_code = '', $barcode = ''){
			//내구연한 및 연장기한
			$sql = 'SELECT	product_code, barcode, reg_dt AS from_dt, durout_dt AS to_dt, 1 AS rate
					FROM	wft_product_durability_set
					WHERE	org_no	 = \''.$_SESSION['userCenterCode'].'\'
					AND		del_flag = \'N\'
					AND		LEFT(reg_dt,	'.StrLen($date).') <= \''.$date.'\'
					AND		LEFT(durout_dt, '.StrLen($date).') >= \''.$date.'\'';

			if ($product_code) $sql .= ' AND product_code = \''.$product_code.'\' AND barcode = \''.$barcode.'\'';

			$sql .= '
					UNION	ALL
					SELECT	product_code, barcode, ext_from_dt, ext_to_dt, 0.5
					FROM	wft_product_durability_set
					WHERE	org_no	 = \''.$_SESSION['userCenterCode'].'\'
					AND		del_flag = \'N\'
					AND		LEFT(ext_from_dt, '.StrLen($date).') <= \''.$date.'\'
					AND		LEFT(ext_to_dt,	  '.StrLen($date).') >= \''.$date.'\'';

			if ($product_code) $sql .= ' AND product_code = \''.$product_code.'\' AND barcode = \''.$barcode.'\'';

			$rows = $this->_fetch_array($sql);

			for($i=0; $i<count($rows); $i++){
				$row = $rows[$i];
				$durext[$row['product_code']][$row['barcode']][] = Array('from_dt'=>$row['from_dt'], 'to_dt'=>$row['to_dt'], 'rate'=>$row['rate']);
			}

			unset($rows);
			return $durext;
		}
	}

	$conn = new connection();

	$gHome = $_SERVER['DOCUMENT_ROOT'];

	//$_SERVER['REMOTE_ADDR'] == '115.90.90.147'
	//테스트기관
	if ($_SESSION['userCenterCode'] == '1234' ||
		//관리자
		$_SESSION['userCode'] == 'carevisit'){
		$debug = true;
	}else{
		$debug = false;
	}

	if ($_SERVER['REMOTE_ADDR'] == '106.248.42.71' || $_SERVER['REMOTE_ADDR'] == '112.146.68.15' || $_SERVER['REMOTE_ADDR'] == '49.164.47.19' || $_SERVER['REMOTE_ADDR'] == '49.164.47.178' || $_SERVER['REMOTE_ADDR'] == '121.169.195.197' || $_SERVER['REMOTE_ADDR'] == '125.132.10.148') $debug = true;

	//고객정보 이력관리 적용
	$lbTestMode = true;

	//일정등록 테스트
	$lbPlanMode = true;

	//설적등록 테스트
	if ($debug && ($_SESSION['userCenterCode'] == '1234' || $_SESSION['userCenterCode'] == '1058721994')){
		$lbConfMode = true;
	}else{
		$lbConfMode = false;
	}

	//바우처 구분등록 테스트
	if ($_SESSION['userCenterCode'] == '1234'){
		$lbTestModeVou = true;
	}else{
		$lbTestModeVou = false;
	}

	//급여계산 과세항목 테스트
	$lbTestTax = true;

	//은행코드
	$gBankCode = '/003';

	//주거래 은행
	$sql = 'SELECT bank_cd
			  FROM bank_center
			 WHERE org_no = \''.$_SESSION['userCenterCode'].'\'
			   AND from_dt <= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
			   AND IFNULL(to_dt,\'9999-12-31\') >= DATE_FORMAT(NOW(),\'%Y-%m-%d\')
			 LIMIT 1';
	$gCenterBankCD = $conn->get_data($sql);

	//은행이체 가능여부
	if ($debug){
		if (!Empty($gCenterBankCD) && Is_Numeric(StrPos($gBankCode,'/'.$gCenterBankCD))){
			$isBankTrans = true;
		}else{
			$isBankTrans = false;
		}
	}else{
		$isBankTrans = false;
	}

	//청구한도 서비스별 설정여부
	/*
	if ($_SESSION['userCenterCode'] == '24476000002' ||
		$_SESSION['userCenterCode'] == '32726000176' ||//다솜요양복지센터 ||
		$_SESSION['userCenterCode'] == '33017000140' ||//주사랑노인복지센터 ||
		$_SESSION['userCenterCode'] == '34713000124' ||//다정재가노인복지센터 ||
		$_SESSION['userCenterCode'] == '34713000124' ||//다정재가노인복지센터 ||
		$_SESSION['userCenterCode'] == '31138000044' ||//엔젤노인복지센터 ||
		$_SESSION['userCenterCode'] == '32635000054' ||//사랑드림실버케어센터 ||
		$_SESSION['userCenterCode'] == '34211000064' ||//연구복지센타 ||
		$_SESSION['userCenterCode'] == '32817000067' ||//부모사랑노인복지){
		$lbLimitSet = true;
	}else{
		$lbLimitSet = $debug;
	}
	*/
	$lbLimitSet = true;

	//주식회사해피
	if ($_SESSION['userCenterCode'] == '32811000079' ||
		$_SESSION['userCenterCode'] == '34119000603'){
		$lbLimitSet = true;
	}

	//삼성요양센터
	if ($_SESSION['userCenterCode'] == '34682000040'){
		$lbLimitSet = true;
	}

	//데모여부
	if ($_SESSION['userCenterCode'] == '12345'){
		$isDemo = true;
	}else{
		$isDemo = false;
	}

	//직원급여 수가별 수당 급여제
	$lbSalarySet = true;

	//2인요양보호사 일정등록 가능여부 //31147000129
	$lbTogetherSet	= true;

	//오늘일정등록 가능여부
	$lbTodayPlanReg = true;

	//과거일정 등록 가능여부
	$lbPastPlanReg = $debug;

	//주야간 가능여부
	$lbDAN = $debug;

	//치매수당 가능여부
	$IsDementiaPay = true;

	//재가지원, 자원연계 요양보호사 항목추가가여부
	$IsCareYoyAddon = true;


	//방문간호지시서 가능여부
	$IsNursingOrder = true;


	/*
	 *	본인부담금 계신시 인정번호 기간별 계산여부
	 *	- ../sugupja/client_expense_exec.php
	 *	- ../work/result_detail_care_new.php
	 */
	$IsExpensePeriod = true;


	/*
	 *	수급자별 요양보호사 추가급여 적용여부
	 *	- ../salaryNew/salary_detail.php
	 *	- ../salaryNew/salary_pay_list.php
	 */
	$IsCMAddPay = true;

	//공단프로그램 변경 적용일자
	if (Date('Ymd') >= '20160328'){
		$IsLongtermCng2016 = true;
	}else{
		$IsLongtermCng2016 = false;
	}


	//재무회계 수입지출(2018/06/19)
	if ($_SESSION['userLevel'] != 'A'){
		$IsPrgRead	 = true; //읽기권한
		$IsPrgWrite	 = true; //쓰기권한
		$IsPrgModify = true; //수정권한
		$IsPrgDelete = true; //삭제권한
		$IsChkRight	 = false;
	}

	//급여조정및명세 메뉴조정
	$IsSalaryMenu = true;

	//고객정보 계약,장기요양보험,본인부담금 등록 수정
	$IsClientInfo = true;

	$lsIljungSave = false;

	//재가지원 서비스별 일정 저장 수정
	//if ($_SESSION['userCenterCode'] == '1234' || $_SESSION['userCenterCode'] == 'KN88C003'){
		$lsIljungSave = true;
	//}

	$lsAnnualChange = false;

	//급여 연차수당 계산 변경
	if ($_SESSION['userCenterCode'] == '1234' || $_SESSION['userCenterCode'] == '31138000044'){
		$lsAnnualChange = true;
	}

	$lsDisMenu = false;

	//if ($debug == '1' || $_SESSION['userCenterCode'] == '31121500010'){
		$lsDisMenu = true;
	//}


	/*********************************************************

		바우처

	*********************************************************/
	@include_once('../inc/_voucher.php');


	# 구분리스트
	@include_once("../inc/_definition.php");

	#상수
	@include_once('../inc/_const.php');
?>
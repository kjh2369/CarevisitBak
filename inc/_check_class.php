<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_ed.php");

	class checkClass extends connection {
		var $ed;

		function checkClass($_ed){
			$this->ed = $_ed;
		}

		// 보험사 상품리스트
		function insItemList($p_code, $p_returnType = "option"){
			$result = "";
			$sql = "select distinct g02_item as code, g02_name as name
					  from g02insitem
					 where g02_code = '$p_code'";
			$this->query($sql);
			$this->fetch();
			$rowCount = $this->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $this->select_row($i);
				$code = $row["code"];
				$name = $row["name"];

				if ($p_returnType == "option"){
					$result .= "<option value='$code'>$name</option>";
				}else{
					$result .= $code."//".$name.";;";
				}
			}

			$this->row_free();

			return $result;
		}

		// 보험료 단가
		function insItemPrice($p_code, $p_item, $p_memberCount){
			$date = date("Ymd", mkTime());
			$sql = "select g02_price
					  from g02insitem
					 where g02_code = '$p_code'
					   and g02_item = '$p_item'
					   and '$p_memberCount' between g02_from_person and g02_to_person
					   and '$date' between g02_from_date and g02_to_date";
			$result = $this->get_data($sql);

			return $result;
		}

		// 갑근세
		function gapgeunse($p_year, $p_pay, $p_deductCnt, $p_childrenCnt){
			$payAmount = $p_pay / 1000;
			$gongjeCount = $p_deductCnt + $p_childrenCnt;

			$sql = 'select max(g00_year)
					  from g00income
					 where g00_year <= \''.$p_year.'\'';

			$year = $this->get_data($sql);


			if ($gongjeCount < 1) $gongjeCount = 1;
			if ($gongjeCount > 2){
				if ($p_childrenCnt > 2){
					$gongjeName = "_children";
				}else{
					$gongjeName = "_normal";
				}
			}else{
				$gongjeName = "";
			}

			$field = "g00_pay_".$gongjeCount.$gongjeName;

			$sql = "select $field as fieldName
					  from g00income
					 where g00_year      = '$year'
					   and g00_pay_more <= '$payAmount'
					   and g00_pay_under > '$payAmount'";
			$gapgeunse = $this->get_data($sql);

			if ($gapgeunse == "") $gapgeunse = "0";

			return $gapgeunse;
		}

		// 지사장등록여부
		function isManager($p_code){
			$sql = "select count(*)
					  from m95manager
					 where m95_code = '$p_code'";
			$count = $this->get_data($sql);

			if ($count > 0){
				return 'Y';
			}else{
				return 'N';
			}
		}

		// 지사장등록
		function addManager($p_code, $p_pass, $p_name){
			if ($this->isManager($p_code) == 'N'){
				$sql = "insert into m95manager values (
						 '$p_code'
						,'$p_pass'
						,'$p_name')";
			}else{
				$sql = "update m95manager
						   set m95_pass = '$p_pass'
						,      m95_name = '$p_name'
						 where m95_code = '$p_code'";
			}
			if ($this->execute($sql)){
				return 'Y';
			}else{
				return 'N';
			}
		}

		// 센터지사장지정
		function appointManager($p_code, $p_kind, $p_manager){
			/*
			$sql = "select count(*)
					  from m96manager
					 where m96_ccode = '$p_code'
					   and m96_mkind = '$p_kind'";
			if ($this->get_data($sql) > 0){
				$sql = "update m96manager
						   set m96_manager = '$p_manager'
						 where m96_ccode   = '$p_code'
						   and m96_mkind   = '$p_kind'";
			}else{
				$sql = "insert into m96manager values (
						 '$p_code'
						,'$p_kind'
						,'$p_manager')";
			}
			$this->execute($sql);
			*/
			$sql = "update m00center
					   set m00_writer = '$p_manager'
					 where m00_mcode  = '$p_code'
					   and m00_mkind  = '$p_kind'";
			$this->execute($sql);

			return '';
		}

		// 요양보호사 이름, 핸드폰번호
		function getYoyNameAndMobile($p_jumin){
			$sql = "select m02_yname, m02_ytel, m02_ygoyong_stat
					  from m02yoyangsa
					 where m02_yjumin = '$p_jumin'
					 limit 1";
			$row = $this->get_array($sql);

			if (is_array($row)){
				if ($row[2] == '1'){
					return $row[0].'//'.$row[1];
				}else{
					return $row[0].'//';
				}
			}else{
				return '';
			}
		}

		// 급여가 있는 월
		function getFindPayMonth($p_code, $p_kind, $p_year){
			$sql = "select t22_ym
					  from t22payconf
					 where t22_ccode = '$p_code'
					   and t22_mkind = '$p_kind'
					   and t22_ym like '$p_year%'
					 limit 1";
			$this->query($sql);
			$this->fetch();
			$rowCount = $this->row_count();

			$request = '';

			for($i=0; $i<$rowCount; $i++){
				$row = $this->select_row($i);

				$request .= $row[0].'//';
			}
			$this->row_free();

			return $request;
		}

		// 욕창관리 기록지 기존 데이타 유무 확인
		function getBedsoreYN($p_code, $p_kind, $p_jumin, $p_date){
			$p_jumin = $this->ed->de($p_jumin);
			$p_date = str_replace('-', '', $p_date);

			$sql = "select count(*)
					  from r380bedsore
					 where r380_ccode   = '$p_code'
					   and r380_mkind   = '$p_kind'
					   and r380_sugupja = '$p_jumin'
					   and r380_date    = '$p_date'";
			$count = $this->get_data($sql);

			if ($count > 0){
				return 'Y';
			}else{
				return 'N';
			}
		}

		// 기관코드 존재여부
		function exist_center($p_code){
			$sql = "select count(*)
					  from m00center
					 where m00_mcode = '$p_code'";
			$value = $this->get_data($sql);

			if ($value > 0){
				return true;
			}else{
				return false;
			}
		}

		// 기관에 없는 수가만 전체복사
		function exist_suga_to_copy(){
			$result = true;
			$this->begin();

			$sql = "select distinct m01_mcode
					  from m01suga
					 where m01_mcode != 'goodeos'";
			$this->query($sql);
			$this->fetch();
			$row_count = $this->row_count();

			for($i=0; $i<$row_count; $i++){
				$row  = $this->select_row($i);
				$code = $row[0];

				$sql = "insert into m01suga (m01_mcode, m01_mcode2, m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, m01_sdate, m01_edate, m01_rate)
						select '$code', m01_mcode2, m01_scode, m01_suga_cont, m01_suga_value, m01_suga_value15, m01_suga_value75, m01_suga_cvalue1, m01_suga_cvalue2, m01_suga_cvalue3, m01_calc_time, m01_sdate, '99999999', m01_rate
						  from m01suga
						 where m01_mcode = 'goodeos'
						   and m01_mcode2 not in (select m01_mcode2 from m01suga where m01_mcode = '$code')";

				if (!$this->execute($sql)){
					$result = false;
					break;
				}
			}

			$this->row_free();

			if ($result){
				$this->commit();
			}else{
				$this->rollback();
			}

			return $result;
		}

		function exist_ed($cd){
			return $this->ed->en($cd);
		}

		/******************************************************

			중복된 일정리스트를 작성한다.

		******************************************************/
		function iljung_duplicate($code, $member, $date, $from_time, $to_time){
			$from_time .= '00';
			$to_time   .= '00';
			$sql = "select t01_ccode as code
					,      t01_mkind as kind
					,      m03_name as name
					,      concat(substring(t01_sugup_fmtime,1,2), ':', substring(t01_sugup_fmtime,3,2)) as from_time
					,      concat(substring(t01_sugup_totime,1,2), ':', substring(t01_sugup_totime,3,2)) as to_time
					  from (
						   select t01_ccode, t01_mkind, t01_jumin, t01_sugup_fmtime, t01_sugup_totime
							 from t01iljung
							where t01_sugup_date   = '$date'
							  and t01_yoyangsa_id1 = '$member'
							  and (date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '$from_time'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '$to_time'), '%Y-%m-%d %H:%i')
							   or  date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '$from_time'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '$to_time'), '%Y-%m-%d %H:%i'))
							  and t01_del_yn = 'N'
							union
						   select t01_ccode, t01_mkind, t01_jumin, t01_sugup_fmtime, t01_sugup_totime
							 from t01iljung
							where t01_sugup_date   = '$date'
							  and t01_yoyangsa_id2 = '$member'
							  and (date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '$from_time'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '$to_time'), '%Y-%m-%d %H:%i')
							   or  date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '$from_time'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '$to_time'), '%Y-%m-%d %H:%i'))
							  and t01_del_yn = 'N'
						   ) as t
					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin";

			$this->query($sql);
			$this->fetch();

			$row_count = $this->row_count();

			$result = "************************ 중복된 일정이 있습니다. ************************\n";

			for($i=0; $i<$row_count; $i++){
				$row = $this->select_row($i);

				if ($row['code'] != $code){
					$result = '타기관 수급자와 일정이 중복됩니다.';
					break;
				}else{
					$result .= (!empty($result) ? "\n" : '').'수급자 : "'.$row['name'].'"님의 "'.$row['from_time'].' ~ '.$row['to_time'].'" 일정이 중복됩니다.';
				}
			}

			$this->row_free();

			return $result;
		}
	}

	$check = new checkClass($ed);

	// 보험상품 리스트
	if ($_GET["check"] == "insItemList"){
		echo $check->insItemList($_GET["code"], $_GET["type"]);
	}

	// 보험상품 단가
	if ($_GET["check"] == "insItemPrice"){
		echo $check->insItemPrice($_GET["code"], $_GET["item"], $_GET["memberCount"]);
	}

	// 갑근세
	if ($_GET["check"] == "gapgeunse"){
		echo $check->gapgeunse($_GET["year"], $_GET["pay"], $_GET["deCnt"], $_GET["chCnt"]);
	}

	// 지사장등록여부
	if ($_GET['check'] == 'isManager'){
		echo $check->isManager($_GET['code']);
	}

	// 지사장등록
	if ($_POST['check'] == 'addManager'){
		echo $check->addManager($_POST['code'], $_POST['pass'], $_POST['name']);
	}

	// 지사장센터지정
	if ($_POST['check'] == 'appointManager'){
		echo $check->appointManager($_POST['code'], $_POST['kind'], $_POST['manager']);
	}

	// 요양보호사 이름, 핸드폰번호
	if ($_GET['check'] == 'getYoyNameAndMobile'){
		echo $check->getYoyNameAndMobile($_GET['jumin']);
	}

	// 급여가 있는 월 조회
	if ($_POST['check'] == 'getFindPayMonth'){
		echo $check->getFindPayMonth($_POST['code'], $_POST['kind'], $_POST['year']);
	}

	// 욕창관리 기록지 기존 데이타 유무 확인
	if ($_POST['check'] == 'getBedsoreYN'){
		echo $check->getBedsoreYN($_POST['code'], $_POST['kind'], $_POST['jumin'], $_POST['date']);
	}

	// 기관존재여부
	if ($_GET['check'] == 'exist_center'){
		echo $check->exist_center($_GET['code']);
	}

	// 기관수가복사
	if ($_GET['check'] == 'exist_suga_to_copy'){
		echo $check->exist_suga_to_copy();
	}

	// 암호화
	if ($_GET['check'] == 'ed'){
		echo $check->exist_ed($_GET['cd']);
	}

	/******************************************************

		중복된 일정리스트를 작성한다.

	******************************************************/
	if ($_GET['check'] == 'iljung_duplicate'){
		echo $check->iljung_duplicate($_GET['code'], $_GET['member'], $_GET['date'], $_GET['from_time'], $_GET['to_time']);
	}
?>
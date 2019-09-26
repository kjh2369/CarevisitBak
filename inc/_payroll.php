<?
	/*
	 * 급여
	 */
	class payroll{
		var $conn = null;
		var $myF = null;

		var $payAmt = 0; // 월급여
		var $deductCnt = 0; // 공제자수
		var $childrenCnt = 0; // 20세이하 자녀수
		var $positionAmt = 0; //직급수당

		var $npYN = "Y"; //국민연금 여부
		var $hiYN = "Y"; //건강보험 여부
		var $eiYN = "Y"; //고용보험 여부

		var $gapgeunse = 0; // 갑근세
		var $juminse = 0; // 주민세
		var $healthInsurance = 0; // 건강보험료
		var $careInsurance = 0; // 장기요양보험료
		var $nationPension = 0; // 국민연금
		var $employInsurance = 0; // 고용보험

		function payroll($p_conn, $p_myF){
			$this->conn = $p_conn;
			$this->myF = $p_myF;
		}

		function nothing(){
			$this->conn = null;
		}

		/*
		 * 기본 항목을 저장한다.
		 */
		function setDefaultSubject(){
			$mCode = $_SESSION["userCenterCode"];
			$this->conn->begin();
			$sql = "insert into t20subject (t20_ccode, t20_kind1, t20_kind2, t20_code, t20_name, t20_fix, t20_amount, t20_use, t20_required) values
					('$mCode', '1', '1', '01', '기본금', 'N', 0, 'Y', 'Y'),
					('$mCode', '1', '2', '01', '식대비', 'N', 0, 'Y', 'Y'),
					('$mCode', '1', '2', '02', '차량유지비', 'N', 0, 'Y', 'Y'),
					('$mCode', '2', '1', '01', '갑근세', 'N', 0, 'Y', 'Y'),
					('$mCode', '2', '1', '02', '주민세', 'N', 0, 'Y', 'Y'),
					('$mCode', '2', '2', '01', '고용보험', 'N', 0, 'Y', 'Y'),
					('$mCode', '2', '2', '02', '건강보험', 'N', 0, 'Y', 'Y'),
					('$mCode', '2', '2', '03', '장기요양', 'N', 0, 'Y', 'Y'),
					('$mCode', '2', '2', '04', '국민연금', 'N', 0, 'Y', 'Y')";
			if (!$this->conn->execute($sql)){
				$this->conn->rollback();
			}else{
				$this->conn->commit();
			}
		}

		function setValue(){
			$this->gapgeunse = $this->gapgeunse();
			$this->juminse = $this->juminse();
			$this->healthInsurance = $this->healthInsurance();
			$this->careInsurance = $this->careInsurance();
			$this->nationPension = $this->nationPension();
			$this->employInsurance = $this->employInsurance();
		}

		// 갑근세
		/* - 일반근로자
		 * 1.과세대상급여(비과세제외) * 12
		 *   비과세를 제외한 월급여액에 12다을 곱하여 연 소득금액을 환산한다.
		 * 2.근로소득공제
		 *   1에서 계산된 총급여액이 
		 *   500만원 이하 이면 총급여액의 80%
		 *   500만원 초과 1,500만원 이하이면 400만원 + 500만원 초과금액의 50%
		 *   1,500만원 초과 3,000만원 이하이면 900만원 + 1,500만원 초과금액의 15%
		 *   3,000만원 초과 4,500만원 이하이면 1,125만원 + 3,000만원 초과금액의 10%
		 *   4,500만원 초과 이면 1,275만원 + 4,500만원 초과금액의 5%
		 * 3.기본공제
		 *   공제대상 인원 * 150만원
		 * 4.다자녀 추가공제
		 *   20세 이하인 자녀가 2인 경우 50만원 
		 *   20세 이하인 자녀가 2인을 초과하는 경우 50만원 + 2인을 초과하는 1인당 100만원
		 * 5.특별공제
		 *   공제대상 가족수가 2인이면 - 100만원 + 연간 급여액의 2.5%
		 *   공제대상 가족수가 3인 이상이면 - 240만원 + 연간 급여액의 5%
		 * 6.연금공제
		 *   월급여약에서 공제하는 본인기여금(국민연금 표준소득월액) * 12
		 * 7.과세표준
		 *   과세표준 = 1 - (2 + 3 + 4 + 5 + 6)
		 * 8.산출세액
		 *   과세표준이 1천200만원 이하 이면 - 과세표준의 6/100
		 *   과세표준이 1천200만원 초과 4천600만원 이하 이면 - 72만원 + 1천200만원을 초과하는 과세표준금액의 16/100
		 *   과세표준이 4천600만원 초과 8천800만원 이하 이면 - 616만원 + 4천600만원을 초과하는 과세표준금액의 25/100
		 *   과세표준이 8천800만원 초과이면 - 1,666만원 + 8천800만원을 초과하는 과세표준금액의 35/100
		 * 9.근로소득세액공제
		 *   산출세액이 50만원 이하이면 산출세액의 55%
		 *   산출세액이 50만원을 초과하면 275,000 + 50만원을 초과하는 금액의 30%(50만원 한도)
		 * 10.결정세액
		 *    산출세액(8) - 근로소득세액공제(9)
		 * 11.간이세액 = 결정세액(10) / 12
		 * 12.주민세 간이세액 / 10%
		 *
		 * - 일용근로자
		 * 1.(일급여액 - 80,000원) * 3.6% * 근무일수
		 */
		function gapgeunse(){
			/*
			// 1.과세대상급여
			$totalPay = $p_pay * 12;

			echo "1.과세대상급여 : ".number_format($totalPay)."<br>";

			// 2.근로소득공제
			if ($totalPay <= 5000000){
				$gongjeAmt[0] = $totalPay * 0.8;
			}else if ($totalPay > 5000000 && $totalPay <= 15000000){
				$gongjeAmt[0] = 4000000 + ($totalPay - 5000000) * 0.5;
			}else if ($totalPay > 15000000 && $totalPay <= 30000000){
				$gongjeAmt[0] = 9000000 + ($totalPay - 15000000) * 0.15;
			}else if ($totalPay > 30000000 && $totalPay <= 45000000){
				$gongjeAmt[0] = 11250000 + ($totalPay - 30000000) * 0.1;
			}else if ($totalPay > 4500){
				$gongjeAmt[0] = 12750000 + ($totalPay - 45000000) * 0.05;
			}
			
			echo "2.근로소득공제 : ".number_format($gongjeAmt[0])."<br>";

			// 3.기본공제
			$gongjeAmt[1] = $p_deduct * 1500000;

			echo "3.기본공제 : ".number_format($gongjeAmt[1])."<br>";

			// 4.다자녀 추가공제
			if ($p_children == 2){
				$gongjeAmt[2] = 500000;
			}else if ($p_children > 3){
				$gongjeAmt[2] = 500000 + ($p_children - 2) * 1000000;
			}else{
				$gongjeAmt[2] = 0;
			}

			echo "4.다자녀 추가공제 : ".number_format($gongjeAmt[2])."<br>";

			// 5.특별공제
			//if ($p_deduct + $p_children == 2){
			if ($p_deduct == 2){
				$gongjeAmt[3] = 1000000 + $totalPay * 0.025;
			}else if ($p_deduct + $p_children > 3){
				$gongjeAmt[3] = 2400000 + $totalPay * 0.05;
			}else{
				$gongjeAmt[3] = 0;
			}

			echo "5.특별공제 : ".number_format($gongjeAmt[3])."<br>";

			// 6.연금공제
			$gongjeAmt[4] = $totalPay * 0.45;

			echo "6.연금공제 : ".number_format($gongjeAmt[4])."<br>";

			// 7.과세표준
			$taxBase = $totalPay - ($gongjeAmt[0] + $gongjeAmt[1] + $gongjeAmt[2] + $gongjeAmt[3] + $gongjeAmt[4]);

			// 8.산출세액
			if ($taxBase <= 12000000){
				$taxAmount = $taxBase * 0.06;
			}else if ($taxBase > 12000000 && $taxBase <= 46000000){
				$taxAmount = 720000 + ($taxBase - 12000000) * 0.16;
			}else if ($taxBase > 46000000 && $taxBase <= 88000000){
				$taxAmount = 6160000 + ($taxBase - 46000000) * 0.25;
			}else if ($taxBase > 88000000){
				$taxAmount = 16660000 + ($taxBase - 88000000) * 0.35;
			}else{
				$taxAmount = 0;
			}

			// 9.근로소득세액공제
			if ($taxAmount <= 500000){
				$workTaxAmt = $taxAmount * 0.55;
			}else{
				$workTaxAmt = 275000 + ($taxAmount - 500000) * 0.3;
			}

			// 10.결정세액
			$fixTaxAmt = $taxAmount - $workTaxAmt;

			// 11.간이세액
			$gapgeunse = $fixTaxAmt / 12;

			return $gapgeunse;
			*/
			$payAmount = $this->payAmt / 1000;
			$year = date("Y", mkTime());
			$gongjeCount = $this->deductCnt + $this->childrenCnt;

			if ($gongjeCount < 1) $gongjeCount = 1;

			if ($gongjeCount > 2){
				if ($this->childrenCnt > 2){
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
					 where g00_year = '$year'
					   and g00_pay_more <= '$payAmount'
					   and g00_pay_under > '$payAmount'";
			$gapgeunse = $this->conn->get_data($sql);

			return $gapgeunse;
		}

		/*
		 * 주민세
		 * - 갑근세의 10%
		 */
		function juminse(){
			return $this->myF->cutOff($this->gapgeunse * 0.1);
		}

		/*
		 * 건강보험료
		 * - 급여의 2.54%
		 */
		function healthInsurance(){
			if ($this->hiYN == "Y"){
				return $this->myF->cutOff(ceil($this->payAmt * 0.0254));
			}else{
				return 0;
			}
		}

		/*
		 * 장기요양 보험료
		 * - 건강보험료의 4.78%
		 */
		function careInsurance(){
			if ($this->hiYN == "Y"){
				return $this->myF->cutOff(ceil($this->healthInsurance * 0.0478));
			}else{
				return 0;
			}
		}

		/*
		 * 국민연금
		 * - 급여의 4.5%
		 */
		function nationPension(){
			if ($this->npYN == "Y"){
				return $this->myF->cutOff(ceil($this->payAmt * 0.045));
			}else{
				return 0;
			}
		}

		/*
		 * 고용보험료
		 * - 급여의 0.45%
		 */
		function employInsurance(){
			if ($this->eiYN == "Y"){
				return $this->myF->cutOff(ceil($this->payAmt * 0.0045));
			}else{
				return 0;
			}
		}
	}

	$payroll = new payroll($conn, $myF);
?>
<?
	class insurance{
		// 보험사리스트
		function getInsList(){
			$list[0] = "천지라이프";
			
			return $list;
		}

		// 보험료
		function getPriceList(){
			$list[0][0]["start"] = 1;
			$list[0][0]["end"] = 2;
			$list[0][0]["price"] = 41500;

			$list[0][1]["start"] = 3;
			$list[0][1]["end"] = 9;
			$list[0][1]["price"] = 39400;

			$list[0][2]["start"] = 10;
			$list[0][2]["end"] = 999;
			$list[0][2]["price"] = 37300;
		}

		// 1인당 보험료
		function getPersonPrice($p_insCode, $p_personCount = 1){
			$list = $this->getPriceList();

			for($i=0; $i<sizeOf($list); $i++){
				if ($list[$p_insCode][$i]["start"] >= $p_personCount &&
					$list[$p_insCode][$i]["end"] <= $p_personCount){
					$price = $list[$p_insCode][$i]["price"];
					break;
				}
			}
		}
	}

	$ins = new ins();
?>
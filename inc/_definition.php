<?
	class definition{
		var $f = null;

		function definition(){
		}

		// 입급구분
		function DepositList(){
			$deposit[0]['cd'] = '01';			$deposit[0]['nm'] = '현금';			$deposit[0]['use'] = true;			$deposit[0]['cal'] = 1;			$deposit[0]['income'] = 1;		$deposit[0]['seq'] = 1;
			$deposit[1]['cd'] = '02';			$deposit[1]['nm'] = '계좌이체';		$deposit[1]['use'] = true;			$deposit[1]['cal'] = 1;			$deposit[1]['income'] = 1;		$deposit[1]['seq'] = 3;
			$deposit[2]['cd'] = '03';			$deposit[2]['nm'] = '지로';			$deposit[2]['use'] = true;			$deposit[2]['cal'] = 1;			$deposit[2]['income'] = 1;		$deposit[2]['seq'] = 4;
			$deposit[3]['cd'] = '04';			$deposit[3]['nm'] = '카드';			$deposit[3]['use'] = true;			$deposit[3]['cal'] = 1;			$deposit[3]['income'] = 1;		$deposit[3]['seq'] = 5;
			$deposit[4]['cd'] = '81';			$deposit[4]['nm'] = '선납';			$deposit[4]['use'] = false;			$deposit[4]['cal'] = 1;			$deposit[4]['income'] = 1;		$deposit[4]['seq'] = 0;
			$deposit[5]['cd'] = '89';			$deposit[5]['nm'] = '선납입금';		$deposit[5]['use'] = false;			$deposit[5]['cal'] = 1;			$deposit[5]['income'] = 0;		$deposit[5]['seq'] = 0;
			$deposit[6]['cd'] = '99';			$deposit[6]['nm'] = '결손';			$deposit[6]['use'] = true;			$deposit[6]['cal'] = -1;		$deposit[6]['income'] = 0;		$deposit[6]['seq'] = 7;
			$deposit[7]['cd'] = '05';			$deposit[7]['nm'] = '현금(-)';		$deposit[7]['use'] = true;			$deposit[7]['cal'] = -1;		$deposit[7]['income'] = 1;		$deposit[7]['seq'] = 6;
			$deposit[8]['cd'] = '06';			$deposit[8]['nm'] = '현금영수증';	$deposit[8]['use'] = true;			$deposit[8]['cal'] = 1;			$deposit[8]['income'] = 1;		$deposit[8]['seq'] = 2;

			$deposit = $this->sortArray($deposit, 'seq', 1);

			return $deposit;
		}

		function DepositName($cd){
			$list = $this->DepositList();
			$list_cnt = sizeof($list);
			$nm = '';

			for($i=0; $i<$list_cnt; $i++){
				if ($list[$i]['cd'] == $cd && $list[$i]['use']){
					$nm = $list[$i]['nm'];
					break;
				}
			}

			return $nm;
		}

		// 입금구분
		function DepositGbn($gbn){
			switch($gbn){
				case '01':
					return '현금';
					break;
				case '02':
					return '계좌이체';
					break;
				case '03':
					return '지로';
					break;
				case '04':
					return '카드';
					break;
				case '05':
					return '현금(-)';
					break;
				case '81':
					return '선납';
					break;
				case '89':
					return '선납입금';
					break;
				case '99':
					return '결손';
					break;
			}
		}

		// 수급자 현황 리스트
		function SugupjaStatusList(){
			$list[0] = array('code'=>'1','name'=>'수급중',	  'end'=>'N');
			$list[1] = array('code'=>'2','name'=>'계약해지',  'end'=>'Y');
			$list[2] = array('code'=>'3','name'=>'보류',	  'end'=>'N');
			$list[3] = array('code'=>'4','name'=>'사망',	  'end'=>'Y');
			$list[4] = array('code'=>'5','name'=>'타기관이전','end'=>'Y');
			$list[5] = array('code'=>'6','name'=>'등외판정',  'end'=>'N');
			$list[6] = array('code'=>'7','name'=>'입원',	  'end'=>'N');

			return $list;
		}

		// 숙ㅂ자 현화 구분
		function SugupjaStatusGbn($gbn){
			switch($gbn){
				case '1': return '수급중';     break;
				case '2': return '계약해지';   break;
				case '3': return '보류';       break;
				case '4': return '사망';       break;
				case '5': return '타기관이전'; break;
				case '6': return '등외판정';   break;
				case '7': return '입원';       break;
			}
		}

		// 은행코드 리스트
		function BankList(){
			$list[0]['code'] = '001'; $list[0]['use'] = 'Y'; $list[0]['name'] = '한국은행';
			$list[1]['code'] = '002'; $list[1]['use'] = 'Y'; $list[1]['name'] = '산업은행';
			$list[2]['code'] = '003'; $list[2]['use'] = 'Y'; $list[2]['name'] = '기업은행';
			$list[3]['code'] = '004'; $list[3]['use'] = 'Y'; $list[3]['name'] = '국민은행';
			$list[4]['code'] = '005'; $list[4]['use'] = 'Y'; $list[4]['name'] = '외환은행';
			$list[5]['code'] = '007'; $list[5]['use'] = 'Y'; $list[5]['name'] = '수협중앙회';
			$list[6]['code'] = '008'; $list[6]['use'] = 'Y'; $list[6]['name'] = '수출입은행';
			$list[7]['code'] = '011'; $list[7]['use'] = 'Y'; $list[7]['name'] = '농협중앙회';
			$list[8]['code'] = '012'; $list[8]['use'] = 'Y'; $list[8]['name'] = '농협회원조합';
			$list[9]['code'] = '020'; $list[9]['use'] = 'Y'; $list[9]['name'] = '우리은행';

			$list[10]['code'] = '023'; $list[10]['use'] = 'Y'; $list[10]['name'] = 'SC제일은행';
			$list[11]['code'] = '027'; $list[11]['use'] = 'Y'; $list[11]['name'] = '한국씨티은행';
			$list[12]['code'] = '031'; $list[12]['use'] = 'Y'; $list[12]['name'] = '대구은행';
			$list[13]['code'] = '032'; $list[13]['use'] = 'Y'; $list[13]['name'] = '부산은행';
			$list[14]['code'] = '034'; $list[14]['use'] = 'Y'; $list[14]['name'] = '광주은행';
			$list[15]['code'] = '035'; $list[15]['use'] = 'Y'; $list[15]['name'] = '제주은행';
			$list[16]['code'] = '037'; $list[16]['use'] = 'Y'; $list[16]['name'] = '전북은행';
			$list[17]['code'] = '039'; $list[17]['use'] = 'Y'; $list[17]['name'] = '경남은행';
			$list[18]['code'] = '045'; $list[18]['use'] = 'Y'; $list[18]['name'] = '새마을금고연합회';
			$list[19]['code'] = '048'; $list[19]['use'] = 'Y'; $list[19]['name'] = '신협중앙회';

			$list[20]['code'] = '050'; $list[20]['use'] = 'Y'; $list[20]['name'] = '상호저축은행';
			$list[21]['code'] = '071'; $list[21]['use'] = 'Y'; $list[21]['name'] = '우체국';
			$list[22]['code'] = '081'; $list[22]['use'] = 'Y'; $list[22]['name'] = '하나은행';
			$list[23]['code'] = '088'; $list[23]['use'] = 'Y'; $list[23]['name'] = '신한은행';

			return $list;
		}

		function GetBankList(){
			$list = $this->BankList();
			$listCount = sizeOf($list);
			$j = 0;

			for($i=0; $i<$listCount; $i++){
				if ($list[$i]['use'] == 'Y'){
					$bankList[$j]['code'] = $list[$i]['code'];
					$bankList[$j]['name'] = $list[$i]['name'];
					$j ++;
				}
			}

			return $bankList;
		}

		// 은행명
		function GetBankName($code){
			$list = $this->GetBankList();
			$listCount = sizeOf($list);

			for($i=0; $i<$listCount; $i++){
				if ($list[$i]['code'] == $code){
					$bankName = $list[$i]['name'];
					break;
				}
			}

			return $bankName;
		}

		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 중지관련
		//
		// 중지사유
		function GetStopReason(){
			$list[0]  = array('cd'=>'01', 'nm'=>'본인포기');
			$list[1]  = array('cd'=>'02', 'nm'=>'사망');
			$list[2]  = array('cd'=>'03', 'nm'=>'말소');
			$list[3]  = array('cd'=>'04', 'nm'=>'전출');
			$list[4]  = array('cd'=>'05', 'nm'=>'미사용');
			$list[5]  = array('cd'=>'06', 'nm'=>'본인부담금미납');
			$list[6]  = array('cd'=>'07', 'nm'=>'사업종료');
			$list[7]  = array('cd'=>'08', 'nm'=>'자격종료');
			$list[8]  = array('cd'=>'09', 'nm'=>'판정결과반영');
			$list[9]  = array('cd'=>'10', 'nm'=>'자격정지');
			$list[10] = array('cd'=>'99', 'nm'=>'기타');

			return $list;
		}

		// 중지사유명
		function GetStopReasonName($cd){
			$list = $this->GetStopReason();

			for($i=0; $i<sizeof($list); $i++){
				if ($cd == $list[$i]['cd']){
					$result = $list[$i]['nm'];
					break;
				}
			}

			unset($list);

			return $result;
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////

		// 배열정렬
		function sortArray($array, $column, $pos = 0){
			for($i=0; $i<sizeOf($array); $i++){
				$sortarray[] = $array[$i][$column];
			}
			$op = array(SORT_DESC, SORT_ASC); //배열 정렬
			@array_multisort($sortarray, $op[$pos], $array);

			return $array;
		}
	}

	$definition = new definition();
?>
<?
	class Report{
		var $conn = null;

		function report($conn){
			$this->conn = $conn;
			$this->conn->fetch_type = 'assoc';
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
				case 'CLTBR'		: $str = $this->col_group_sub(array(40,70,70,70,80)); break;	//초기상담기록지
				case 'CLTBSR'		: $str = $this->col_group_sub(array(40,70,70)); break;			//욕구평가기록지
				case 'CLTPST'		: $str = $this->col_group_sub(array(40,70,70,60)); break;		//욕창위험도평가도구
				case 'CLTDDT'		: $str = $this->col_group_sub(array(40,70,70,60)); break;		//낙상위험도평가도구
				
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
			switch($index){
				case 'CLTBR'		: $str = $this->col_header_sub(array('No','상담일자','상담자','상담구분','서비스','비고')); break;
				case 'CLTBSR'		: $str = $this->col_header_sub(array('No','작성일','작성자','비고')); break;
				case 'CLTPST'		: $str = $this->col_header_sub(array('No','작성일','작성자','평가점수','비고')); break;
				case 'CLTDDT'		: $str = $this->col_header_sub(array('No','작성일','작성자','평가점수','비고')); break;

				default:
					$str = 'col header : '.$index;
			}

			return $str;
		}

		function col_header_sub($col){
			$col_cnt = sizeof($col);

			$str = '<tr>';

			for($i=0; $i<$col_cnt; $i++){
				$str .= '<th class=\'head '.($i == $col_cnt - 1 ? 'last' : '').'\'>'.$col[$i].'</th>';
			}

			$str .= '</tr>';

			return $str;
		}

		
	}

	$report = new Report($conn);
?>
<?
	class hce{
		var $conn;
		var $SR;
		var $IPIN;
		var $rcpt;
		var $meet;
		var $meetType;	//1:선정 2:제공 3:재사정 4:종결
		var $backRcptNo;

		function hce($conn){
			$this->conn = $conn;
		}

		function let($clear = false){
			$_SESSION['HCE_SR']			= $this->SR;
			$_SESSION['HCE_IPIN']		= $this->IPIN;
			$_SESSION['HCE_RCPT_SEQ']	= $this->rcpt;
			$_SESSION['HCE_MEET_SEQ']	= $this->meet;
			$_SESSION['HCE_MEETTYPE']	= $this->meetType;

			if ($clear){
				$_SESSION['HCE_RCPT_BACK'] = $_SESSION['HCE_RCPT_SEQ'];
			}

			if ($_SESSION['HCE_RCPT_BACK'] != $_SESSION['HCE_RCPT_SEQ'] && $_SESSION['HCE_RCPT_SEQ']){
				$_SESSION['HCE_RCPT_BACK']	= $_SESSION['HCE_RCPT_SEQ'];
			}
		}

		function get(){
			$this->SR		= $_SESSION['HCE_SR'];
			$this->IPIN		= $_SESSION['HCE_IPIN'];
			$this->rcpt		= $_SESSION['HCE_RCPT_SEQ'];
			$this->meet		= $_SESSION['HCE_MEET_SEQ'];
			$this->meetType	= $_SESSION['HCE_MEETTYPE'];
			$this->backRcptNo = $_SESSION['HCE_RCPT_BACK'];

			if (!$this->rcpt && $this->backRcptNo) $this->rcpt = $this->backRcptNo;
		}

		function set(){

		}

		function getMeetSeq($orgNo){
			$sql = 'SELECT	MAX(meet_seq)
					FROM	hce_meeting
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$this->SR.'\'
					AND		IPIN	= \''.$this->IPIN.'\'
					AND		rcpt_seq= \''.$this->rcpt.'\'
					AND		meet_gbn= \''.$this->meetType.'\'
					AND		del_flag= \'N\'';

			$seq = $this->conn->get_data($sql);

			return $seq;
		}

		//담당자
		function getPersonIn($orgNo){
			//담당자
			$sql = 'SELECT	iver_nm
					,		iver_jumin
					FROM	hce_interview
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$this->SR.'\'
					AND		IPIN	= \''.$this->IPIN.'\'
					AND		rcpt_seq= \''.$this->rcpt.'\'';

			$row = $this->conn->get_array($sql);

			$arr['name']	= $row['iver_nm'];
			$arr['jumin']	= $row['iver_jumin'];

			Unset($row);

			return $arr;
		}

		function init(){
			$this->SR		= '';
			$this->IPIN		= '';
			$this->rcpt		= '';
			$this->meet		= '';
			$this->meetType	= '';
			$this->let();
		}
	}

	$hce = new hce($conn);
	$hce->get();
?>
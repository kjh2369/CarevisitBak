<?
	//session_start();

	class dbconn{
		var $db;
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


		function dbconn($host, $id, $pw, $db){
			$this->db = @mysql_connect($host,$id,$pw, true) or die ("SQL server ERROR");

			mysql_query("set names utf8", $this->db);
			mysql_query("set autocommit=0", $this->db);
			mysql_select_db($db, $this->db);
		}

		function set_name($name){
			mysql_query('set names '.$name, $this->db);
		}

		function close(){
			mysql_close($this->db);
		}

		function query($query){
			$this->error_query = $query;
			$this->result = mysql_query($query, $this->db);

			return $this->result;
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

		function row_free(){
			if ($this->result !=0){
				mysql_free_result($this->result);
			}
			$this->result=0;
		}

		// 데이타조회
		function get_data($sql){
			$p_result = mysql_query($sql, $this->db);
			$p_row = mysql_fetch_array($p_result);
			$value = $p_row[0];
			mysql_free_result($p_result);

			return $value;
		}

		// 데이타조회
		function get_array($sql){
			$p_result = mysql_query($sql, $this->db);

			if ($this->fetch_type == 'assoc'){
				$p_row = mysql_fetch_assoc($p_result);
			}else if ($this->fetch_type == 'row'){
				$p_row = mysql_fetch_row($p_result);
			}else if ($this->fetch_type == 'object'){
				$p_row = mysql_fetch_object($p_result);
			}else{
				$p_row = mysql_fetch_array($p_result);
			}

			mysql_free_result($p_result);

			return $p_row;
		}

		/*********************************************************

			쿼리 실행 후 배열로 리턴

		*********************************************************/
		function fetch_array($query, $key = ''){
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


		function execute($query){
			$this->error_query = $query;

			if ($this->test) echo nl2br($query).'<br><br>';
			if ($this->mode == 1){
				$result = mysql_query($query, $this->db);

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


		function query_exec($query){
			//$this->begin();
			mysql_query("BEGIN", $this->db);

			foreach($query as $sql){
				if (!$this->execute($sql)){
					 //$this->rollback();
					 mysql_query("ROLLBACK", $this->db);

					 if ($this->debug){
						return $this->error_msg.chr(13).chr(10).$this->error_query;
					 }else{
						return 'result=ERROR&msg='.$this->ERRMSG;
					 }
				}
			}

			//$this->commit();
			mysql_query("COMMIT", $this->db);

			return 'result=SUCCESS';
		}
	}

	$dbcare = new dbconn('localhost','care','care9482', 'newcare');
	$dbnhcs = new dbconn('localhost','nhcs','nhcs9482', 'nhcs');
	$debug = false;

	//if ($_SERVER['REMOTE_ADDR'] == '106.248.42.71' || $_SERVER['REMOTE_ADDR'] == '112.146.68.15' || $_SERVER['REMOTE_ADDR'] == '49.164.47.19') $debug = true;
	//if ($_SERVER['REMOTE_ADDR'] == '221.140.54.150') $debug = true;

	$dbcare->debug = $debug;
	$dbnhcs->debug = $debug;
?>
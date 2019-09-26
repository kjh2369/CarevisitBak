<?
	class html_script{
		function title($text, $border = false){
			$html = '<div class=\'title '.($border ? 'title_border' : '').'\'>'.$text.'</div>';

			return $html;
		}

		function form_start($name, $method = 'post', $enctype = false, $other = ''){
			if ($enctype){
				$enctype = 'enctype=\'multipart/form-data\'';
			}else{
				$enctype = '';
			}

			$html = '<form name=\''.$name.'\' method=\''.$method.'\' '.$enctype.' '.$other.'>';

			return $html;
		}

		function form_end(){
			$html = '</form>';

			return $html;
		}

		function table_start($class = '', $style = ''){
			$html = '<table class=\'my_table '.$class.'\' style=\'width:100%; '.$style.'\'>';

			return $html;
		}

		function table_end(){
			$html = '</table>';

			return $html;
		}

		function table_colgroup($colgroup = null){
			$html = '<colgroup>';

			if ($colgroup == null){
				$html .= '<col>';
			}else{
				$cnt  = sizeof($colgroup);

				for($i=0; $i<$cnt; $i++){
					$html .= '<col width=\''.$colgroup[$i].'\'>';
				}

				$html .= '<col>';
			}
			$html .= '</colgroup>';

			return $html;
		}

		function table_header($header){
			$cnt  = sizeof($header);
			$html = '<thead>';

			for($i=0; $i<$cnt; $i++){
				$html .= '<th class=\'head\'>'.$header[$i].'</th>';
			}

			$html = '</thead>';

			return $html;
		}

		function table_body_start(){
			$html = '<body>';

			return $html;
		}

		function table_body_end(){
			$html = '</body>';

			return $html;
		}

		function table_row_start(){
			$html = '<tr>';

			return $html;
		}

		function table_row_end(){
			$html = '</tr>';

			return $html;
		}

		function table_row($text = '&nbsp;', $class = '', $tag = 'td', $style = '', $cols = 1){
			$html = '<'.$tag.' class=\''.$class.'\' style=\''.$style.'\' colspan=\''.$cols.'\'>'.$text.'</'.$tag.'>';

			return $html;
		}

		function input($name, $type, $value, $class = '', $other = ''){
			$html = '<input name=\''.$name.'\' type=\''.$type.'\' class=\''.$class.'\' value=\''.$value.'\' '.$other.'>';

			return $html;
		}
	}

	$html_script = new html_script();
?>
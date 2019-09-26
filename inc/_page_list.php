<?php
/**
 * Paging Class using PHP4/5
 *
 * @author		Kang YongSeok <wyseburn@gmail.com>
 * @version		1.0
 */

class YsPaging {
	/* 파라메타용 변수 */
	var $mcurMethod;
	var $mcurPage;
	var $mCurPageNum;		//현제 페이지 번호
	var $mPageVar;			//페이지에 사용되는 변수명
	var $mExtraVar;			//추가 변수
	var $mTotalItem;		//글갯수
	var $mPerPage;			//출력 페이지수
	var $mPerItem;			//출력 글 수
	var $mPrevPage;			//[이전 페이지] text 또는 img tag
	var $mNextPage;			//[다음 페이지] text 또는 img tag
	var $mPrevPerPage;	//[이전 $mPerPage 페이지] text 또는 img tag
	var $mNextPerPage;	//[다음 $mPerPage 페이지] text 또는 img tag
	var $mFirstPage;		//[처음] 페이지 text 또는 img tag
	var $mLastPage;			//[마지막] 페이지 text 또는 img tag
	var $mPageCss;		//페이지 목록에 사용할 css
	var $mCurPageCss;	//현재 페이지에 사용할 css
	var $mParamGubun; //파라메타연결자

	/* 내부사용 변수 */
	var $mPageCount;		//전체 페이지수
	var $mTotalBlock;		//전체 블럭수
	var $mBlock;				//현재 블럭수
	var $mFirstPerPage;	//한블럭의 첫 페이지번호
	var $mLastPerPage;	//한블럭의 마지막 페이지 번호

	/**
	* 생성자 - 온션을 성정하고 기본적인 페이지,블럭수 등을 계산
	* @param array $params
	*/
	function YsPaging($params) {
		if(!count($params)) {
			echo "[YsPaging Error : 파라메터가 없습니다.]";
			return;
		}

		$this->mcurMethod	= $params['curMethod'] ? $params['curMethod'] : "get";
		$this->mcurPage		= $params['curPage'] ? $params['curPage'] : $_SERVER['PHP_SELF'];
		$this->mCurPageNum	= $params['curPageNum'] ? $params['curPageNum'] : 1;
		$this->mPageVar		= $params['pageVar'] ? $params['pageVar'] : 'pagenum';
		$this->mExtraVar	= $params['extraVar'] ? $params['extraVar'] : '';
		$this->mTotalItem	= $params['totalItem'] ? $params['totalItem'] : 0;
		$this->mPerPage		= $params['perPage'] ? $params['perPage'] : 10;
		$this->mPerItem		= $params['perItem'] ? $params['perItem'] : 15;
		$this->mPrevPage	= $params['prevPage'] ? $params['prevPage'] : '이전';
		$this->mNextPage	= $params['nextPage'] ? $params['nextPage'] : '다음';
		$this->mPrevPerPage = $params['prevPerPage'];
		$this->mNextPerPage = $params['nextPerPage'];
		$this->mFirstPage	= $params['firstPage'];
		$this->mLastPage	= $params['lastPage'];
		$this->mPageCss		= $params['pageCss'];
		$this->mCurPageCss	= $params['curPageCss'];
		$this->mParamGubun	= $params['paramGubun'] ? $params['paramGubun'] : '?';

		if ($this->mcurMethod == 'post'){
			$this->mExtraVar = '';
		}

		$this->mPageCount = ceil($this->mTotalItem/$this->mPerItem);
		$this->mTotalBlock = ceil($this->mPageCount/$this->mPerPage);
		$this->mBlock = ceil($this->mCurPageNum/$this->mPerPage);
		$this->mFirstPerPage = ($this->mBlock-1)*$this->mPerPage;
		$this->mLastPerPage = $this->mTotalBlock<=$this->mBlock ? $this->mPageCount : $this->mBlock*$this->mPerPage;
	}

	/**
	* 현재 글번호를 리턴
	* @return integer
	*/
	function getItemNum() {
		return $this->mTotalItem-($this->mCurPageNum-1)*$this->mPerItem; // 현재 아이템 번호 계산
	}

	/**
	* 첫페이지 번호 링크를 리턴
	* @return string
	*/
	function getFirstPage() {
		if(empty($this->mFirstPage) || $this->mCurPageNum == 1) return NULL;

		if ($this->mcurMethod == 'get'){
			return '<a href="'.$this->mcurPage.$this->mParamGubun.$this->mPageVar.'=1'.$this->mExtraVar.'">'.$this->mFirstPage.'</a>';
		}else{
			return '<a href="'.$this->mcurPage.'(1);">'.$this->mFirstPage.'</a>';
		}
	}

	/**
	* 끝페이지 번호 링크를 리턴
	* @return string
	*/
	function getLastPage() {
		if(empty($this->mLastPage) || $this->mCurPageNum == $this->mPageCount) return NULL;

		if ($this->mcurMethod == 'get'){
			return '<a href="'.$this->mcurPage.$this->mParamGubun.$this->mPageVar.'='.$this->mPageCount.$this->mExtraVar.'">'.$this->mLastPage.'</a>';
		}else{
			return '<a href="'.$this->mcurPage.'('.$this->mPageCount.');">'.$this->mLastPage.'</a>';
		}
	}

	/**
	* 이전블럭 링크를 리턴
	* @return string
	*/
	function getPrevPerPage() {
		if(empty($this->mPrevPerPage) || $this->mBlock <= 1) return NULL;

		if ($this->mcurMethod == 'get'){
			return '<a href="'.$this->mcurPage.$this->mParamGubun.$this->mPageVar.'='.$this->mFirstPerPage.$this->mExtraVar.'">'.$this->mPrevPerPage.'</a>';
		}else{
			return '<a href="'.$this->mcurPage.'('.$this->mFirstPerPage.');">'.$this->mPrevPerPage.'</a>';
		}
	}

	/**
	* 다음블럭 링크를 리턴
	* @return string
	*/
	function getNextPerPage() {
		if(empty($this->mNextPerPage) || $this->mBlock >= $this->mTotalBlock) return NULL;

		if ($this->mcurMethod == 'get'){
			return '<a href="'.$this->mcurPage.$this->mParamGubun.$this->mPageVar.'='.($this->mLastPerPage+1).$this->mExtraVar.'">'.$this->mNextPerPage.'</a>';
		}else{
			return '<a href="'.$this->mcurPage.'('.($this->mLastPerPage+1).');">'.$this->mNextPerPage.'</a>';
		}
	}

	/**
	* 이전 페이지 링크를 리턴
	* @return string
	*/
	function getPrevPage() {
		if($this->mCurPageNum > 1)
			if ($this->mcurMethod == 'get'){
				return '<a href="'.$this->mcurPage.$this->mParamGubun.$this->mPageVar.'='.($this->mCurPageNum-1).$this->mExtraVar.'">'.$this->mPrevPage.'</a>';
			}else{
				return '<a href="'.$this->mcurPage.'('.($this->mCurPageNum-1).');">'.$this->mPrevPage.'</a>';
			}
		else
			return $this->mPrevPage;
	}

	/**
	* 다음 페이지 링크를 리턴
	* @return string
	*/
	function getNextPage() {
		if($this->mCurPageNum != $this->mPageCount && $this->mPageCount)
			if ($this->mcurMethod == 'get'){
				return '<a href="'.$this->mcurPage.$this->mParamGubun.$this->mPageVar.'='.($this->mCurPageNum+1).$this->mExtraVar.'">'.$this->mNextPage.'</a>';
			}else{
				return '<a href="'.$this->mcurPage.'('.($this->mCurPageNum+1).');">'.$this->mNextPage.'</a>';
			}
		else
			return $this->mNextPage;
	}

	/**
	* 페이지 목록 링크를 리턴
	* @return string
	*/
	function getPageList() {
		$rtn = '';
		for($i=$this->mFirstPerPage+1;$i<=$this->mLastPerPage;$i++) {
			if($this->mCurPageNum == $i)
				if(empty($this->mCurPageCss))
					$rtn .= $i;
				else
					$rtn .= '&nbsp;<span id="pageClass_'.$i.'" class="'.$this->mCurPageCss.'">'.$i.'</span>&nbsp;';
			else {
				if ($this->mcurMethod == 'get'){
					$rtn .= '&nbsp;<a href="'.$this->mcurPage.$this->mParamGubun.$this->mPageVar.'='.$i.$this->mExtraVar.'">';
					if (empty($this->mPageCss)){
						$rtn .= $i.'</a>&nbsp;';
					}else{
						$rtn .= '<span id="pageClass_'.$i.'" class="'.$this->mPageCss.'">'.$i.'</span></a>&nbsp;';
					}
				}else{
					$rtn .= '&nbsp;<a href="'.$this->mcurPage.'('.$i.');">';
					if (empty($this->mPageCss)){
						$rtn .= $i.'</a>&nbsp;';
					}else{
						$rtn .= '<span id="pageClass_'.$i.'" class="'.$this->mPageCss.'">'.$i.'</span></a>&nbsp;';
					}
				}
			}
		}
		return $rtn;
	}

	/**
	* 기본 페이지를 프린트, 상속후 변경 가능
	*/
	function printPaging() {
		echo $this->getFirstPage();
		echo '&nbsp;&nbsp;';
		echo $this->getPrevPerPage();
		echo '&nbsp;&nbsp;';
		echo $this->getPrevPage();
		echo '&nbsp;&nbsp;';
		echo $this->getPageList();
		echo '&nbsp;&nbsp;';
		echo $this->getNextPage();
		echo '&nbsp;&nbsp;';
		echo $this->getNextPerPage();
		echo '&nbsp;&nbsp;';
		echo $this->getLastPage();
	}

	function returnPaging(){
		$str  = $this->getFirstPage();
		$str .= '&nbsp;&nbsp;';
		$str .= $this->getPrevPerPage();
		$str .= '&nbsp;&nbsp;';
		$str .= $this->getPrevPage();
		$str .= '&nbsp;&nbsp;';
		$str .= $this->getPageList();
		$str .= '&nbsp;&nbsp;';
		$str .= $this->getNextPage();
		$str .= '&nbsp;&nbsp;';
		$str .= $this->getNextPerPage();
		$str .= '&nbsp;&nbsp;';
		$str .= $this->getLastPage();

		return $str;
	}
}

//$paging = new YsPaging($params);
?>
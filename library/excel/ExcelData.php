<?php

require_once(dirname(__FILE__) . '/PHPExcel/IOFactory.php');

class xnReadFilter implements PHPExcel_Reader_IReadFilter{
    public function readCell($column, $row, $worksheetName = '') {
        // 只读去1-7行&A－E列中的单元格  
        if ($row == 1) {
            // if (in_array($column,range('A','E'))) {
                return true;
            // }
        }
        return false;  
    }
}

class offsetReadFilter implements PHPExcel_Reader_IReadFilter{
	private $_startRow = 0;     // 开始行  
	private $_endRow = 0;       // 结束行  
	public function __construct($startRow, $chunkSize) {    // 我们需要传递：开始行号&行跨度(来计算结束行号)  
		$this->_startRow = $startRow;  
		$this->_endRow   = $startRow + $chunkSize;  
	}  
	public function readCell($column, $row, $worksheetName = '') {  
	    if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {  
	        return true;  
	    }  
	    return false;  
	}
}


class ExcelData{
	public function __construct(){
		include_once(dirname(__FILE__) . '/PHPExcel.php');
	}

	private function getStyle($styleType='title'){
		$style = array();
		$style['font']['size'] = 9;
		$style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
		
		//表头
		if($styleType == 'header'){
			$style['font']['size'] = 16;
			$style['borders']['outline']['style'] = PHPExcel_Style_Border::BORDER_THIN;
		}
		//副表头
		else if($styleType == 'subheader'){
			$style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
			$style['borders']['outline']['style'] = PHPExcel_Style_Border::BORDER_THIN;
		}
		//标题
		else if($styleType == 'title'){
			$style['borders']['allborders']['style'] = PHPExcel_Style_Border::BORDER_THIN;
		}
		//内容
		else if($styleType == 'content'){
			$style['borders']['allborders']['style'] = PHPExcel_Style_Border::BORDER_THIN;
		}

		return $style;
	}

	/**
	 * 读取Excel文件数据
	 * @param  string  $file      文件路径
	 * @param  array   $attrIndex 数组下标配置数组 例如：array('id', 'bank', 'school', 'openid', 'team')
	 * @param  integer $startRow  有效数据起始行
	 * @return array              二维数组
     *      $maxRow = $perCompMaxRow;
			$offset = 2000;
			$nums = ceil($maxRow / $offset);
			$startRow = 2;
			for ($i=1; $i <= $nums ; $i++) { 
				$start = ($i-1)*$offset + $startRow;
				$objORF = new offsetReadFilter($start, $offset);
				$result = $excel->readBigData($dataFile, array('startRow'=>$start), false, $objORF);
				$dataResult = array_merge($dataResult, $result);
			}
	 */
	public function readBigData($file, $sheetCfg = array(), $first=false, $objReadFilter=''){
		$result = array();

		$start = time();
		$sheetIndex = 0;
		$startRow   = 2;
		if(isset($sheetCfg['sheetIndex'])){
			$sheetIndex = $sheetCfg['sheetIndex'];
		}
		if(isset($sheetCfg['startRow'])){
			$startRow = $sheetCfg['startRow'];
		}

		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

		// 默认用excel2007读取excel，若格式不对，则用之前的版本进行读取
		$objReader = PHPExcel_IOFactory::createReader("Excel2007");

		if(!$objReader->canRead($file)){
			$objReader = PHPExcel_IOFactory::createReader("Excel5");
			if(!$objReader->canRead($file)){
				return $result;
			}
		}
		//读取过滤器
		if(is_object($objReadFilter)){
			$objReader->setReadFilter($objReadFilter);
		}

		$objReader->setReadDataOnly(true);
		$PHPExcel = $objReader->load($file);

		//读取Excel文件第一个工作表
		$currentSheet = $PHPExcel->getSheet($sheetIndex);
		//获取Excel文件中最大行数
		$allRow = $currentSheet->getHighestRow();
		if($first == true){
			return $allRow;
		}
		// $aaa = $currentSheet->toArray(null,true,true,true);
		// print_r($aaa);die;
		//获取Excel文件中最大列数
		$allColumn = $currentSheet->getHighestColumn();
		$allColumn = PHPExcel_Cell::columnIndexFromString($allColumn);
		// echo $allRow;
		// echo "<br/>";
		// echo $allColumn;die;

		for ($row = $startRow; $row <= $allRow ; $row++) { 
			for ($column = 0; $column < $allColumn ; $column++) { 
				$tempIndex = $column;
				$result[$row][$tempIndex] = $currentSheet->getCellByColumnAndRow($column, $row)->getValue();
			}
		}

		$result = array_values($result);
		return $result;
	}

	/**
	 * 读取Excel文件数据
	 * @param  string  $file      文件路径
	 * @param  array   $attrIndex 数组下标配置数组 例如：array('id', 'bank', 'school', 'openid', 'team')
	 * @param  integer $startRow  有效数据起始行
	 * @return array              二维数组
	 */
	public function read($file, $attrIndex = array(), $sheetCfg = array()){
		$result = array();

		$start = time();
		$sheetIndex = 0;
		$startRow   = 2;
		if(isset($sheetCfg['sheetIndex'])){
			$sheetIndex = $sheetCfg['sheetIndex'];
		}
		if(isset($sheetCfg['startRow'])){
			$startRow = $sheetCfg['startRow'];
		}

		//设定缓存模式为经gzip压缩后存入cache
		// $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;  
		// $cacheSettings = array();  
		// PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings); 

		$type = strtolower( pathinfo($file, PATHINFO_EXTENSION) );  
		if($type=='xlsx'||$type=='xls'){
			// 默认用excel2007读取excel，若格式不对，则用之前的版本进行读取
			$objReader = PHPExcel_IOFactory::createReader("Excel2007");

			if(!$objReader->canRead($file)){
				$objReader = PHPExcel_IOFactory::createReader("Excel5");
				if(!$objReader->canRead($file)){
					return $result;
				}
			}

			$objReader->setReadDataOnly(true);
			$PHPExcel = $objReader->load($file);

			//读取Excel文件第一个工作表
			$currentSheet = $PHPExcel->getSheet($sheetIndex);
		}else if($type == 'csv'){
			$objReader = PHPExcel_IOFactory::createReader('CSV')  
			                    ->setDelimiter(',')  
			                    ->setInputEncoding('GBK')  
			                    ->setEnclosure('"')  
			                    ->setLineEnding("\r\n");
			                    // ->setSheetIndex($sheetIndex);  
			$PHPExcel = $objReader->load($file);  
			$currentSheet = $PHPExcel->getSheet($sheetIndex);
		}           
		//获取Excel文件中最大行数
		$allRow = $currentSheet->getHighestRow();

		//获取Excel文件中最大列数
		$allColumn = $currentSheet->getHighestColumn();
		$allColumn = PHPExcel_Cell::columnIndexFromString($allColumn);
		// echo $allRow;die;

		$customIndex = false;
		if($allColumn == count($attrIndex)){
			$customIndex = true;
		}

		for ($row = $startRow; $row <= $allRow ; $row++) { 
			for ($column = 0; $column < $allColumn ; $column++) { 
				$tempIndex = $column;
				if($customIndex){
					$tempIndex = $attrIndex[$column];
				}
				$result[$row][$tempIndex] = $currentSheet->getCellByColumnAndRow($column, $row)->getValue();
			}
		}

		$result = array_values($result);
		return $result;
	}

	public function export($data, $headerColumn, $excelConfig = array(), $widthCfg=array(), $rtColumn = array())
	{
		if(!isset($excelConfig['filename']) || empty($excelConfig['filename'])){
			$excelConfig['filename'] = date('Y年m月d日');
		}
		if(!isset($excelConfig['sheetname']) || empty($excelConfig['sheetname'])){
			$excelConfig['sheetname'] = "Worksheet";
		}
		if(!isset($excelConfig['format']) || empty($excelConfig['format'])){
			$excelConfig['format'] = "xlsx";
		}
		
		// $allColumn = PHPExcel_Cell::stringFromColumnIndex(--$allColumn); //总列数 K
		
		//实例化对象PHPExcel
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objActiveSheet = $objPHPExcel->getActiveSheet();

		$excelLineNum = 1; //Excel行号

		//循环赋值标题
		$columnHeaderName = array_values($headerColumn);

		for ($i=0; $i < count($headerColumn); $i++) { 
			$column = PHPExcel_Cell::stringFromColumnIndex($i);
			$objActiveSheet->setCellValue($column . $excelLineNum, $columnHeaderName[$i]);
		}

		if($widthCfg){
			$widthCfg = array_values($widthCfg);
			foreach ($widthCfg as $kk => $vwidth) {
				$column = PHPExcel_Cell::stringFromColumnIndex($kk);
				$objActiveSheet->getColumnDimension($column)->setWidth($vwidth);
			}
		}

		$allColumn = PHPExcel_Cell::stringFromColumnIndex(count($headerColumn)-1);

		//标题字体加粗
		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->getFont()->setBold(true);
		$objActiveSheet->getRowDimension($excelLineNum)->setRowHeight(20);
		//标题默认样式
		$style = $this->getStyle('title');
		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);
		$excelLineNum++;

		//把数据二次循环填充到Excel表格里
		$fieldNames = array_keys($headerColumn);

		$autoLineId = 1; //编号从1开始
		foreach ($data as $k => $row) {
			for ($i=0; $i < count($headerColumn); $i++) { 
				$column = PHPExcel_Cell::stringFromColumnIndex($i);
				$cellValue = $row[$fieldNames[$i]];
				if(in_array($fieldNames[$i], $rtColumn)){
					$objActiveSheet->getStyle($column . $excelLineNum)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					// $objActiveSheet->setCellValue($column . $excelLineNum, $cellValue, PHPExcel_Cell_DataType::TYPE_STRING);
					$objRichText = new PHPExcel_RichText( $objPHPExcel->getActiveSheet()->getCell($column . $excelLineNum) );  
					$objRichText->createText($cellValue);
				}else{
					$objActiveSheet->setCellValue($column . $excelLineNum, $cellValue);
				}
			}
			
			//设置单元格边框
			$style = $this->getStyle('content');
			$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);
			$autoLineId++;
			$excelLineNum++;

			/*if($arrIndex == 'idcard'){
				$objActiveSheet->getStyle($column.($index+1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				// $objActiveSheet->setCellValue($column . $excelLineNum, $cellValue, PHPExcel_Cell_DataType::TYPE_STRING);
				$objRichText = new PHPExcel_RichText( $objPHPExcel->getActiveSheet()->getCell($column . $excelLineNum) );  
				$objRichText->createText($cellValue);
			}*/
		}
		
		//设置工作表名称
		$objActiveSheet->setTitle($excelConfig['sheetname']);

		$writerType = $excelConfig['format'] == 'xls' ? 'Excel5' : 'Excel2007';
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $writerType);
		
		//输出内容到浏览器  
		header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        
        //多浏览器下兼容中文标题
        $filename = $excelConfig['filename'];
        $filename .= $excelConfig['format'] == 'xls' ? '.xls' : '.xlsx';
        $encoded_filename = urlencode($filename);
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
	}

	/**
	 * 数据表导出到Excel表
	 * @param  array  $data         数据数组
	 * @param  array  $headerColumn 表头标题配置数组
	 * @param  array  $excelConfig  生成Excel文件名及格式等配置 <数组可为空>
	 *                              array(
	 *                              'filename'=>'9月份报表',
	 *                              'sheetname'=>'中行鼓励支行9月份销售业绩一览表',
	 *                              'format'=>'xlsx',
	 *                              'title'=>'',
	 *                              'subtitle'=>'',
	 *                              )
	 * @return void               
	 */
	public function exportTail($data, $headerColumn, $excelConfig = array(), $widthCfg=array(), $rtColumn = array())
	{
		if(!isset($excelConfig['filename']) || empty($excelConfig['filename'])){
			$excelConfig['filename'] = date('Y年m月d日');
		}
		if(!isset($excelConfig['sheetname']) || empty($excelConfig['sheetname'])){
			$excelConfig['sheetname'] = "Worksheet";
		}
		if(!isset($excelConfig['format']) || empty($excelConfig['format'])){
			$excelConfig['format'] = "xlsx";
		}
		
		// $allColumn = PHPExcel_Cell::stringFromColumnIndex(--$allColumn); //总列数 K
		
		//实例化对象PHPExcel
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objActiveSheet = $objPHPExcel->getActiveSheet();

		$excelLineNum = 1; //Excel行号

		//循环赋值标题
		$columnHeaderName = array_values($headerColumn);

		for ($i=0; $i < count($headerColumn); $i++) { 
			$column = PHPExcel_Cell::stringFromColumnIndex($i);

			if(is_numeric($columnHeaderName[$i]) && $columnHeaderName[$i] < 10){
				$objActiveSheet->getStyle($column . $excelLineNum)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$objRichText = new PHPExcel_RichText( $objPHPExcel->getActiveSheet()->getCell($column . $excelLineNum) );  
				$objRichText->createText($columnHeaderName[$i]);
			}else{
				$objActiveSheet->setCellValue($column . $excelLineNum, $columnHeaderName[$i]);
			}
		}

		if($widthCfg){
			$widthCfg = array_values($widthCfg);
			foreach ($widthCfg as $kk => $vwidth) {
				$column = PHPExcel_Cell::stringFromColumnIndex($kk);
				$objActiveSheet->getColumnDimension($column)->setWidth($vwidth);
			}
		}

		$allColumn = PHPExcel_Cell::stringFromColumnIndex(count($headerColumn)-1);


		$objActiveSheet->getDefaultStyle()->getFont()->setName( 'Arial');

		//标题字体加粗
		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->getFont()->setBold(true);
		$objActiveSheet->getRowDimension($excelLineNum)->setRowHeight(20);
		//标题默认样式
		$style = $this->getStyle('title');
		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);

		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    
		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$excelLineNum++;

		//把数据二次循环填充到Excel表格里
		$fieldNames = array_keys($headerColumn);

		$autoLineId = 1; //编号从1开始
		foreach ($data as $k => $row) {
			for ($i=0; $i < count($headerColumn); $i++) { 
				$column = PHPExcel_Cell::stringFromColumnIndex($i);
				$cellValue = $row[$fieldNames[$i]];

				if(false != strpos($cellValue, '#')){
					$tmp = explode('#', $cellValue);
					if($tmp[1] == 1){
						$objActiveSheet->getStyle($column . $excelLineNum)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objActiveSheet->getStyle($column . $excelLineNum)->getFill()->getStartColor()->setARGB('FF3030');
					}
					$cellValue = $tmp[0];

					if($tmp[1] == 1 && $cellValue < 10){
						$objActiveSheet->getStyle($column . $excelLineNum)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
						$objRichText = new PHPExcel_RichText( $objPHPExcel->getActiveSheet()->getCell($column . $excelLineNum) );  
						$objRichText->createText($cellValue);
					}else{
						$objActiveSheet->setCellValue($column . $excelLineNum, $cellValue);
					}

					if($tmp[1] == 1){
						$objActiveSheet->getStyle($column . $excelLineNum)->getFont()->setBold(true);
						$objActiveSheet->getStyle($column . $excelLineNum)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
					}
				}else{
					if($cellValue < 10){
						$objActiveSheet->getStyle($column . $excelLineNum)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
						$objRichText = new PHPExcel_RichText( $objPHPExcel->getActiveSheet()->getCell($column . $excelLineNum) );  
						$objRichText->createText($cellValue);
					}else{
						$objActiveSheet->setCellValue($column . $excelLineNum, $cellValue);
					}
				}

				$objActiveSheet->getRowDimension($excelLineNum)->setRowHeight(20);
				$objActiveSheet->getColumnDimension($excelLineNum)->setWidth(20);
				$objActiveSheet->getStyle($column . $excelLineNum)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    
				$objActiveSheet->getStyle($column . $excelLineNum)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

				

				

				/*if(in_array($fieldNames[$i], $rtColumn)){
					$objActiveSheet->getStyle($column . $excelLineNum)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					// $objActiveSheet->setCellValue($column . $excelLineNum, $cellValue, PHPExcel_Cell_DataType::TYPE_STRING);
					$objRichText = new PHPExcel_RichText( $objPHPExcel->getActiveSheet()->getCell($column . $excelLineNum) );  
					$objRichText->createText($cellValue);
				}else{
					$objActiveSheet->setCellValue($column . $excelLineNum, $cellValue);
				}*/
			}
			
			//设置单元格边框
			$style = $this->getStyle('content');
			$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);
			$autoLineId++;
			$excelLineNum++;

			/*if($arrIndex == 'idcard'){
				$objActiveSheet->getStyle($column.($index+1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				// $objActiveSheet->setCellValue($column . $excelLineNum, $cellValue, PHPExcel_Cell_DataType::TYPE_STRING);
				$objRichText = new PHPExcel_RichText( $objPHPExcel->getActiveSheet()->getCell($column . $excelLineNum) );  
				$objRichText->createText($cellValue);
			}*/
		}
		
		//设置工作表名称
		$objActiveSheet->setTitle($excelConfig['sheetname']);

		$writerType = $excelConfig['format'] == 'xls' ? 'Excel5' : 'Excel2007';
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $writerType);
		
		//输出内容到浏览器  
		header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        
        //多浏览器下兼容中文标题
        $filename = $excelConfig['filename'];
        $filename .= $excelConfig['format'] == 'xls' ? '.xls' : '.xlsx';
        $encoded_filename = urlencode($filename);
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
	}

	/**
	 * 读取CVS数据
	 * @param  [type]  $file      [description]
	 * @param  integer $startLine [description]
	 * @param  integer $page      [description]
	 * @param  integer $offset    [description]
	 * @return [type]             [description]
	 */
	public function readCVS($file, $startLine = 2, $page = 1, $offset = 5000, $columIndex = 0){
		$file = fopen($file, 'r'); 
		$data = array();

		$okcnt = 0;
		$cnt = 0;
		while($row = fgetcsv($file, 1000)) { //每次读取CSV里面的一行内容
			$cnt++;
			//读取数据跳过表头
			if($page == 1 && $cnt < $startLine) continue;

			//分页即是矢量读取数据
			$start = ($page - 1) * $offset + $startLine - 1;
			$end = $page * $offset + $startLine - 1;
			if($cnt <= $start || $cnt > $end){
				continue;
			}
			foreach ($row as $i => $v) {
				if($i == $columIndex){
					// $v = iconv('gbk', 'utf-8', $v);
					if($v == '大于0元小于1元' || $v == '大于等于100元小于1万元' || $v == '大于等于1元小于100元'){
						continue;
					}
				}
				$row[$i] = $v;
				// if(is_numeric($v)){
				// 	$row[$i] = $v;
				// }else{
				// 	$row[$i] = iconv('gbk', 'utf-8', $v);
				// }
			}
			
			$data[] = array_values($row);
			$okcnt++;
		}

		return array('data'=>$data,'cnt'=>$okcnt);
		// return $data;
	}

	/**
	 * 数据表导出到Excel表
	 * @param  array  $data         数据数组
	 * @param  array  $headerColumn 表头标题配置数组
	 * @param  array  $excelConfig  生成Excel文件名及格式等配置 <数组可为空>
	 *                              array(
	 *                              'filename'=>'9月份报表',
	 *                              'sheetname'=>'中行鼓励支行9月份销售业绩一览表',
	 *                              'format'=>'xlsx',
	 *                              'title'=>'',
	 *                              'subtitle'=>'',
	 *                              )
	 * @return void               
	 */
	public function export222($data, $headerColumn, $excelConfig = array(), $widthCfg=array())
	{
		if(!isset($excelConfig['filename']) || empty($excelConfig['filename'])){
			$excelConfig['filename'] = date('Y年m月d日');
		}
		if(!isset($excelConfig['sheetname']) || empty($excelConfig['sheetname'])){
			$excelConfig['sheetname'] = "Worksheet";
		}
		if(!isset($excelConfig['format']) || empty($excelConfig['format'])){
			$excelConfig['format'] = "xlsx";
		}
		
		$allColumn = count($headerColumn); //总列数
		$allColumn = PHPExcel_Cell::stringFromColumnIndex(--$allColumn); //总列数 K
		
		//实例化对象PHPExcel
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objActiveSheet = $objPHPExcel->getActiveSheet();

		$excelLineNum = 1; //Excel行号

		//循环赋值标题
		$columnHeaderName = array_values($headerColumn);
		// echo $allColumn;
		// print_r($columnHeaderName);die;
		for($column='A', $index=0; $column<=$allColumn; $column++, $index++){
			$objActiveSheet->setCellValue($column . $excelLineNum, $columnHeaderName[$index]);
		}

		if($widthCfg){
			$widthCfg = array_values($widthCfg);
			foreach ($widthCfg as $kk => $vwidth) {
				$tempCol = PHPExcel_Cell::stringFromColumnIndex($kk);
				$objActiveSheet->getColumnDimension($tempCol)->setWidth($vwidth);
			}
		}

		//标题字体加粗
		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->getFont()->setBold(true);
		$objActiveSheet->getRowDimension($excelLineNum)->setRowHeight(20);
		//标题默认样式
		$style = $this->getStyle('title');
		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);
		$excelLineNum++;

		//把数据二次循环填充到Excel表格里
		$fieldNames = array_keys($headerColumn);
		// print_r($fieldNames);die;
		$autoLineId = 1; //编号从1开始
		foreach ($data as $k => $v) {
			for($column='A', $index=0; $column<=$allColumn; $column++, $index++){
				$arrIndex  = $fieldNames[$index];
				$cellValue = $v[$arrIndex];
				// if($column == 'A'){
				// 	$cellValue = $autoLineId;
				// }
				
				/*if($arrIndex == 'idcard'){
					$objActiveSheet->getStyle($column.($index+1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					// $objActiveSheet->setCellValue($column . $excelLineNum, $cellValue, PHPExcel_Cell_DataType::TYPE_STRING);
					$objRichText = new PHPExcel_RichText( $objPHPExcel->getActiveSheet()->getCell($column . $excelLineNum) );  
					$objRichText->createText($cellValue);
				}else{
					$objActiveSheet->setCellValue($column . $excelLineNum, $cellValue);
				}*/
				$objActiveSheet->setCellValue($column . $excelLineNum, $cellValue);
			}
			//设置单元格边框
			$style = $this->getStyle('content');
			$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);
			$autoLineId++;
			$excelLineNum++;
		}
		
		//设置工作表名称
		$objActiveSheet->setTitle($excelConfig['sheetname']);

		$writerType = $excelConfig['format'] == 'xls' ? 'Excel5' : 'Excel2007';
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $writerType);
		
		//输出内容到浏览器  
		header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        
        //多浏览器下兼容中文标题
        $filename = $excelConfig['filename'];
        $filename .= $excelConfig['format'] == 'xls' ? '.xls' : '.xlsx';
        $encoded_filename = urlencode($filename);
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
	}

	/**
	 * 数据表导出到Excel表
	 * @param  array  $data         数据数组
	 * @param  array  $headerColumn 表头标题配置数组
	 * @param  array  $excelConfig  生成Excel文件名及格式等配置 <数组可为空>
	 *                              array(
	 *                              'filename'=>'9月份报表',
	 *                              'sheetname'=>'中行鼓励支行9月份销售业绩一览表',
	 *                              'format'=>'xlsx',
	 *                              'title'=>'',
	 *                              'subtitle'=>'',
	 *                              )
	 * @return void               
	 */
	public function export22223($data, $headerColumn, $excelConfig = array())
	{
		if(!isset($excelConfig['filename']) || empty($excelConfig['filename'])){
			$excelConfig['filename'] = date('Y年m月d日');
		}
		if(!isset($excelConfig['sheetname']) || empty($excelConfig['sheetname'])){
			$excelConfig['sheetname'] = "Worksheet";
		}
		if(!isset($excelConfig['format']) || empty($excelConfig['format'])){
			$excelConfig['format'] = "xlsx";
		}

		$temp = 0;
		$newheaderColumn = array();
		foreach ($headerColumn as $k => $val) {
			if($temp == 0){
				$newheaderColumn['__id'] = array('title'=>'编号', 'extra'=>array('width'=>10));
			}
			$newheaderColumn[$k] = $val;
		}
		$headerColumn = $newheaderColumn;
		$allColumn = count($headerColumn); //总列数
		$allColumn = PHPExcel_Cell::stringFromColumnIndex(--$allColumn); //总列数 K
		
		//实例化对象PHPExcel
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objActiveSheet = $objPHPExcel->getActiveSheet();

		$excelLineNum = 1; //Excel行号
		//有表头，设置表头
		if(isset($excelConfig['title']) && !empty($excelConfig['title'])){
			$objActiveSheet->setCellValue('A'.$excelLineNum, $excelConfig['title']);
			$objActiveSheet->mergeCells('A'.$excelLineNum.':'.$allColumn.$excelLineNum);
			$objActiveSheet->getRowDimension($excelLineNum)->setRowHeight(30);
			//表头默认样式
			$style = $this->getStyle('header');
			$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);
			$excelLineNum++;
		}

		//有副表头，设置副表头
		if(isset($excelConfig['subtitle']) && !empty($excelConfig['subtitle'])){
			$objActiveSheet->setCellValue('A'.$excelLineNum, $excelConfig['subtitle']);
			$objActiveSheet->mergeCells('A'.$excelLineNum.':'.$allColumn.$excelLineNum);
			$objActiveSheet->getRowDimension($excelLineNum)->setRowHeight(20);
			//表头默认样式
			$style = $this->getStyle('subheader');
			$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);
			$excelLineNum++;
		}

		//循环赋值标题
		// $columnHeaderName = array_values($headerColumn);
		// print_r($columnHeaderName);die;
		// for($column='A', $index=0; $column<=$allColumn; $column++, $index++){
		// 	$objActiveSheet->setCellValue($column . $excelLineNum, $columnHeaderName[$index]['title']);
		// 	if(isset($columnHeaderName[$index]['extra']['width'])){
		// 		$objActiveSheet->getColumnDimension($column)->setWidth($columnHeaderName[$index]['extra']['width']);
		// 	}
		// }
		//标题字体加粗
		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->getFont()->setBold(true);
		$objActiveSheet->getRowDimension($excelLineNum)->setRowHeight(20);
		//标题默认样式
		$style = $this->getStyle('title');
		$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);
		$excelLineNum++;

		//把数据二次循环填充到Excel表格里
		$fieldNames = array_keys($headerColumn);
		$autoLineId = 1; //编号从1开始
		foreach ($data as $k => $v) {
			for($column='A'; $column<=$allColumn; $column++){
				if($column == 'A'){
					$objActiveSheet->setCellValue($column . $excelLineNum, $autoLineId);
				}
				else{
					$columnNum = PHPExcel_Cell::columnIndexFromString($column);
					$arrIndex = $fieldNames[$columnNum-1];
					$cellValue = $v[$arrIndex];
					$objActiveSheet->setCellValue($column . $excelLineNum, $cellValue);
				}
			}
			//设置单元格边框
			$style = $this->getStyle('content');
			$objActiveSheet->getStyle('A'.$excelLineNum.':'.$allColumn.$excelLineNum)->applyFromArray($style);
			$autoLineId++;
			$excelLineNum++;
		}
		
		//设置工作表名称
		$objActiveSheet->setTitle($excelConfig['sheetname']);

		$writerType = $excelConfig['format'] == 'xls' ? 'Excel5' : 'Excel2007';
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $writerType);
		
		//输出内容到浏览器  
		header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        
        //多浏览器下兼容中文标题
        $filename = $excelConfig['filename'];
        $filename .= $excelConfig['format'] == 'xls' ? '.xls' : '.xlsx';
        $encoded_filename = urlencode($filename);
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
	}

	/**
	 * 下载网页内容保存Excel文件
	 * @param  string $filename 文件名
	 * @return void           
	 */
	public static function htmlToExcel($filename = ''){
		if(empty($filename)){
			$filename = date('Y年m月d日');
		}
		$filename = $filename . '.xls';

		$encoded_filename = urlencode($filename);
		$ua = $_SERVER["HTTP_USER_AGENT"];
		if (preg_match("/MSIE/", $ua)) {
		    header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
		} else if (preg_match("/Firefox/", $ua)) {
		    header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
		} else {
		    header('Content-Disposition: attachment; filename="' . $filename . '"');
		}

		header("Content-type:application/vnd.ms-excel;charset=utf-8"); 
		// header("Content-Type: application/force-download");
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	}

	/**
	 * 数据表导出到Excel表CVS格式《数据导出大数据时效率相对较高》
	 * @param  array  $data         数据数组
	 * @param  array  $headerColumn 表头标题配置数组
	 * @param  string $filename     文件名
	 * @return void                 
	 */
	public function dataToCVS($data, $headerColumn = array(), $filename = ''){
		if(!isset($filename) || empty($filename)){
			$filename = date('Y年m月d日His');
		}

	    $filename = iconv('utf-8', 'gbk', $filename);
		$fp = fopen($filename.'.csv', 'a');
		 
		// 输出Excel列头信息
		$headerColumn = array_values($headerColumn);
		foreach ($headerColumn as $i => $v) {
		    // CSV的Excel支持GBK编码，一定要转换，否则乱码
		    $head[$i] = iconv('utf-8', 'gbk', $v);
		}
		 
		// 写入列头
		fputcsv($fp, $head);
		 
		// 计数器
		$cnt = 0;
		// 每隔$limit行，刷新一下输出buffer，节约资源
		$limit = 50000;
		 
		// 逐行取出数据，节约内存
		foreach ($data as $k => $row) {
		 
		    $cnt ++;
		    if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
		        ob_flush();
		        flush();
		        $cnt = 0;
		    }
		 
		    foreach ($row as $i => $v) {
		        $row[$i] = iconv('utf-8', 'gbk', $v);
		    }
		    fputcsv($fp, $row);
		}
	}

	public function readCSV($file = '', $startLine = 2, $getfirstrow=false){
		$file = fopen($file, 'r'); 
		$data = array();

		$cnt = 0;
		while($row = fgetcsv($file)) { //每次读取CSV里面的一行内容
			$cnt++;
			if(!$getfirstrow && $cnt < $startLine) continue;
			foreach ($row as $i => $v) {
				if($getfirstrow == true){
					if(is_numeric($v)){
						$row[$i] = $v;
					}else if(mb_detect_encoding($v, 'GBK')){
						$row[$i] = iconv('gbk', 'utf-8', $v);
					}
				}else{
					if(is_numeric($v)){
						$row[$i] = $v;
					}else if(mb_detect_encoding($v, 'GBK')){
						$row[$i] = iconv('gbk', 'utf-8', $v);
					}
				}
			}
			$data[] = $row;
			if($getfirstrow == true){
				break;
			}
		}

		return $data;
	}
}
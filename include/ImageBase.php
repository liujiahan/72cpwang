<?php
/**
 * 图片处理类
 * @author houyongwei
 * @desc 
 * @example
 * // 进行图片处理： cut 裁剪，thumb 缩放， waterMarsk 添加水印
 * // 如果不配置相应的操作参数，则不进行相应的处理。
 * $r = Image::getInstance()->set(array(
 * 		'srcFile'=>'srcimg/brophp.jpg',
 * 		'path'=>'image',
 * 		'prefix'=>'zhangsan',
 * 		'cut'=>array('x'=>'800','y'=>'500','width'=>'100','height'=>'100'),
 * 		'thumb'=>array('width'=>'500','height'=>'500'),
 * 		'waterMarsk'=>array('waterName'=>'waterimg/logo.jpg','waterPos'=>9),
 * ))->save();
 * 
 * // 如果没有处理成功，得到失败的原因
 * if(!$r) {
 * 	var_dump(Image::$errorMsg);
 * }
 * 
 */
class ImageBase {
	private static $instance = null;

	public $srcFile;// 原图
	public $path; // 文件存放目录
	public $newImgName;
	
	public $src_im; // 原图资源	
	public $srcImgInfo; // 原图信息
	
	static $errorMsg = '';// 错误信息
	
	public $prefix=''; // 前缀
	public $cut = array(); // 裁剪参数
	public $thumb = array(); // 缩略图参数
	public $waterMarsk = array(); // 水印参数
	
	// 私有化的构造函数，不可外调
	private function __construct(){}
	
	// 得到本类的实例
	public static function getInstance(){
		if(self::$instance === null) {
			self::$instance = new self;
		} else {
			self::$errorMsg = '';
		}
		return self::$instance;
	}

	/**
	 * 设置图片处理参数
	 * @param array $info 配置数组
	 * 		array(
	 * 			'srcFile' => 'String', // 原图片文件地址 【必选】
	 * 			'path' => 'String',// 保存文件的目录 【必选】
	 * 			'prefix' => 'String',// 新图片名的前缀，默认为空字串[可选]
	 * 
	 * 			'cut' => array( // 裁剪处理 ，如果不设置，就不对图片进行裁剪处理[可选]
	 * 				'x' => Integer, // 剪切图片左边开始的位置【必选】
	 * 				'y' => Integer, // 剪切图片顶部开始的位置【必选】
	 * 				'width' => Iteger, // 图片剪裁的宽度【必选】
	 * 				'height' => Iteger, // 图片剪裁的高度【必选】
	 * 			),
	 * 			
	 * 			'thumb' =>  array( // 缩放处理 ，如果不设置，就不对图片进行缩放处理[可选]
	 * 				'width' => Iteger, // 缩放后的宽度 【必选】
	 * 				'height' => Iteger, // 缩放后的高度 【必选】
	 * 			),
	 * 
	 * 			'waterMarsk'=>array( //背景图片，即需要加水印的图片，暂只支持GIF,JPG,PNG格式 ，如果不设置，就不对图片进行添加水印处理[可选]
	 * 				'waterName'=>'String', // 作为水印的图片，暂只支持GIF,JPG,PNG格式  【必选】
	 * 				'waterPos'=>Integer // 水印位置，有10种状态，0为随机位置  【必选】
	 * 									// 1为顶端居左，2为顶端居中，3为顶端居右；
	 * 									// 4为中部居左，5为中部居中，6为中部居右；
	 * 									// 7为底端居左，8为底端居中，9为底端居右；
	 * 			)
	 * 		)
	 */
	public function set($info = array()){
		foreach($info as $key => $val) {
			if(array_key_exists($key,get_class_vars(get_class($this)))){
				$this->$key = $val;
			}
		}
		return $this;
	}
	
	/**
	 * 处理图片操作，根据配置参数进行处理
	 */ 
	public function save(){
		ini_set('gd.jpeg_ignore_warning', true);
		// 检查参数是否有效
		if(!$this->checkParams()) {
			
			return false;
		}
		
		//检查完所有参数符合规定  开始获取原始 ｛宽 高 类型｝
		$this->srcImgInfo = $this->getInfo($this->srcFile);
		
		//根据类型 获取相应图片的资源
		$this->src_im = $this->getImg($this->srcFile,$this->srcImgInfo);
		
		
		
		
		//检查图片类型是否正确  不正确终止
		if(!$this->src_im) {
			exit("ImageBase->save->2014-2-17->下午1:32:06");
			self::$errorMsg = '原图片文件类型不对';
			return false;
		}
		
		// 裁剪处理
		if(!empty($this->cut)) {
			
			if(!$this->cut()) {
				return false;
			}
		}
		
		
		
		
		// 缩放处理
		if(!empty($this->thumb)) {
			
			$this->thumb();
		}
		
		// 添加水印
		if(!empty($this->waterMarsk) ) {
			if(!$this->waterMark()) {
				return false;
			}
		}
		
		// 如果进行了缩放处理，就添加白底儿补白
		if(!empty($this->thumb) && !$this->addBackGround()) {
			return false;
		}
		$this->waterMarsk = array();
		$this->thumb = array();
		return $this->createNewImage($this->src_im, $this->newImgName, $this->srcImgInfo);
	}
	/**
	 * 裁剪处理
	 */
	private function cut(){
		/* 裁剪的位置不能超出背景图片范围 */
		
		if( (($this->cut['x']+$this->cut['width']) > $this->srcImgInfo['width']) || (($this->cut['y']+$this->cut['height']) > $this->srcImgInfo['height'])){
			self::$errorMsg =  "裁剪的位置超出了背景图片范围!";
			return false;
		}
		
		/* 创建一个可以保存裁剪后图片的资源 */
		$cutimg = imagecreatetruecolor($this->cut['width'], $this->cut['height']);
		$background = imagecolorallocate($cutimg, 255, 255, 255);
		imagefill($cutimg, 0, 0, $background);
		/* 使用imagecopyresampled()函数对图片进行裁剪 */
		//imagecopyresampled($cutimg, $this->src_im, 0, 0, $this->cut['x'], $this->cut['y'], $this->cut['width'], $this->cut['height'], $this->cut['width'], $this->cut['height']);
		
		$a = imagecopy($cutimg,$this->src_im,0,0,$this->cut['x'],$this->cut['y'], $this->cut['width'],  $this->cut['height']);
		//exit("ImageBase.php--cut方法 第行---2013-12-20  下午5:55:02");
		
		imagedestroy($this->src_im);
		
		//imageinterlace($cutimg, 0);
		
		$this->srcImgInfo['width'] = $this->cut['width'];
		$this->srcImgInfo['height'] = $this->cut['height'];
		
		
		$this->src_im = $cutimg;
		return true;
	}
	/**
	 * 缩放处理
	 */
	private function thumb() {
		//$size = $this->getNewSize();//李杰注释
	/* 	$size['width'] = $this->srcImgInfo['width'];
		$size['height'] = $this->srcImgInfo['height']; */
		$size["width"]=$this->thumb['width'];
		$size["height"]=$this->thumb['height'];
		$this->kidOfImage($size);
		
		
		$this->srcImgInfo['width'] = $size['width'];
		$this->srcImgInfo['height'] = $size['height'];
		return true;
	}
	
	/**
	 * 水印处理
	 */
	private function waterMark(){
		
		/*水印图片和背景图片必须都要存在*/
		if(is_array($this->waterMarsk['waterName']) && !empty($this->waterMarsk['waterName'])){
				
			foreach ($this->waterMarsk['waterName'] as $key=>$val){
				if(file_exists($val)){
					$waterInfo = $this->getInfo($val);    		 //获取水印图片信息
					if(is_array($this->waterMarsk['waterPos'])){
		
						if(!$pos = $this->position($this->srcImgInfo, $waterInfo, $this->waterMarsk['waterPos'][$key])){
							self::$errorMsg = '水印不应该比背景图片大！';
							return false;
						}
							
							
						$waterImg = $this->getImg($val, $waterInfo); //获取水印图片资源
						if(!$waterImg) {
							self::$errorMsg = '水印图片文件类型不对';
							return false;
						}
							
						//imagejpeg($this->src_im,'wcms/index.jpg');exit;
						/* 调用私有方法将水印图像按指定位置复制到背景图片中 */
						$this->src_im = $this->copyImage($this->src_im, $waterImg, $pos, $waterInfo);
		
							
					}else{
						self::$errorMsg = '不可能实现一个数组水印放在一个位置！';
						return false;
					}
				}else{
					self::$errorMsg = '水印图片不存在！';
					return false;
				}
			}
				
		}else{
			if(file_exists($this->waterMarsk['waterName'])){
				$waterInfo = $this->getInfo($this->waterMarsk['waterName']);    		 //获取水印图片信息
				/*如果背景比水印图片还小，就会被水印全部盖住*/
				//var_dump($this->srcImgInfo, $waterInfo);die();
				if(is_array($this->waterMarsk['waterPos'])){
					foreach ($this->waterMarsk['waterPos'] as $v){
						if(!$pos = $this->position($this->srcImgInfo, $waterInfo, $v)){
							self::$errorMsg = '水印不应该比背景图片大！';
							return false;
						}
							
							
						$waterImg = $this->getImg($this->waterMarsk['waterName'], $waterInfo); //获取水印图片资源
						if(!$waterImg) {
							self::$errorMsg = '水印图片文件类型不对';
							return false;
						}
						/* 调用私有方法将水印图像按指定位置复制到背景图片中 */
						$this->src_im = $this->copyImage($this->src_im, $waterImg, $pos, $waterInfo);
					}
						
				}else{
					if(!$pos = $this->position($this->srcImgInfo, $waterInfo, $this->waterMarsk['waterPos'])){
						self::$errorMsg = '水印不应该比背景图片大！';
						return false;
					}
						
						
					$waterImg = $this->getImg($this->waterMarsk['waterName'], $waterInfo); //获取水印图片资源
					if(!$waterImg) {
						self::$errorMsg = '水印图片文件类型不对';
						return false;
					}
					/* 调用私有方法将水印图像按指定位置复制到背景图片中 */
					$this->src_im = $this->copyImage($this->src_im, $waterImg, $pos, $waterInfo);
				}
			}else{
				self::$errorMsg = '水印图片不存在！';
				return false;
			}
		}
		
		return true;
	}
	
	/* 内部使用的私有方法，用来确定水印图片的位置 */
	private function position($groundInfo, $waterInfo, $waterPos){
		//var_dump($waterInfo);die();
		/* 需要加水印的图片的长度或宽度比水印还小，无法生成水印 */
		if( ($groundInfo["width"] < $waterInfo["width"]) || ($groundInfo["height"]<$waterInfo["height"]) ) {
			return false;
		}
		switch($waterPos) {
			case 1:			//1为顶端居左
				$posX = 0;
				$posY = 0;
				break;
			case 2:			//2为顶端居中
				$posX = ($groundInfo["width"] - $waterInfo["width"]) / 2;
				$posY = 0;
				break;
			case 3:			//3为顶端居右
				$posX = $groundInfo["width"] - $waterInfo["width"];
				$posY = 0;
				break;
			case 4:			//4为中部居左
				$posX = 0;
				$posY = ($groundInfo["height"] - $waterInfo["height"]) / 2;
				break;
			case 5:			//5为中部居中
				$posX = ($groundInfo["width"] - $waterInfo["width"]) / 2;
				$posY = ($groundInfo["height"] - $waterInfo["height"]) / 2;
				break;
			case 6:			//6为中部居右
				$posX = $groundInfo["width"] - $waterInfo["width"];
				$posY = ($groundInfo["height"] - $waterInfo["height"]) / 2;
				break;
			case 7:			//7为底端居左
				$posX = 0;
				$posY = $groundInfo["height"] - $waterInfo["height"];
				break;
			case 8:			//8为底端居中
				$posX = ($groundInfo["width"] - $waterInfo["width"]) / 2;
				$posY = $groundInfo["height"] - $waterInfo["height"];
				break;
			case 9:			//9为底端居右
				$posX = $groundInfo["width"] - $waterInfo["width"];
				$posY = $groundInfo["height"] - $waterInfo["height"];
				break;
			case 0:
			default:		//随机
				$posX = rand(0,($groundInfo["width"] - $waterInfo["width"]));
				$posY = rand(0,($groundInfo["height"] - $waterInfo["height"]));
				break;
		}
		return array("posX"=>$posX, "posY"=>$posY);
	}
	
	
	
	/* 内部使用的私有方法，处理带有透明度的图片保持原样 */
	private function kidOfImage($size){
		$newImg = imagecreatetruecolor($size["width"], $size["height"]);
		$otsc = imagecolortransparent($this->src_im);
		
		if( $otsc >= 0 && $otsc < imagecolorstotal($this->src_im)) {
			$transparentcolor = imagecolorsforindex( $this->src_im, $otsc );
			$newtransparentcolor = imagecolorallocate(
					$newImg,
					$transparentcolor['red'],
					$transparentcolor['green'],
					$transparentcolor['blue']
			);
			//填充白底
			imagefill( $newImg, 0, 0, $newtransparentcolor );
			
			imagecolortransparent( $newImg, $newtransparentcolor );
		}
		//imagecopyresized( $newImg, $this->src_im, 0, 0, 0, 0, $size["width"], $size["height"], $this->srcImgInfo["width"], $this->srcImgInfo["height"] );
		imagecopyresampled($newImg,  $this->src_im, 0, 0, 0, 0,$size["width"], $size["height"],$this->srcImgInfo["width"], $this->srcImgInfo["height"]);
		
		imagedestroy($this->src_im);
		
		$this->src_im = $newImg;
		//return $newImg;
	}
	
	/* 内部使用的私有方法，返回等比例缩放的图片宽度和高度，如果原图比缩放后的还小保持不变 */
	private function getNewSize(){	
		$size["width"] = $this->srcImgInfo["width"];          //原图片的宽度
		$size["height"] = $this->srcImgInfo["height"];        //原图片的高度
		
		if($this->thumb['width'] < $this->srcImgInfo["width"]){
			$size["width"]=$this->thumb['width'];             		 //缩放的宽度如果比原图小才重新设置宽度
		}

		if($this->thumb['height'] < $this->srcImgInfo["height"]){
			$size["height"] = $this->thumb['height'];            	 //缩放的高度如果比原图小才重新设置高度
		}
		/* 等比例缩放的算法 */
		if($this->srcImgInfo["width"]*$size["width"] > $this->srcImgInfo["height"] * $size["height"]){
			$size["height"] = round($this->srcImgInfo["height"]*$size["width"]/$this->srcImgInfo["width"]);
		}else{
			$size["width"] = round($this->srcImgInfo["width"]*$size["height"]/$this->srcImgInfo["height"]);
		}
		
		return $size;
	}
		
	/* 内部使用的私有方法，用于获取图片的属性信息（宽度、高度和类型） */
	private function getInfo($name) {
		$data = getimagesize($name);
		$imgInfo["width"]	= $data[0];
		$imgInfo["height"]  = $data[1];
		$imgInfo["type"]	= $data[2];
		return $imgInfo;
	}
	
	/*内部使用的私有方法， 用于创建支持各种图片格式（jpg,gif,png三种）资源  */
	private function getImg($name, $imgInfo){
			
		switch ($imgInfo["type"]) {
			case 1:					//gif
				$img = imagecreatefromgif($name);
				break;
			case 2:					//jpg
				$img = @imagecreatefromjpeg($name);
				break;
			case 3:					//png
				$img = imagecreatefrompng($name);
				break;
			default:
				return false;
				break;
		}
		return $img;
	}
	
	/* 内部使用的私有方法，用于加水印时复制图像 */
	private function copyImage($groundImg, $waterImg, $pos, $waterInfo){
		imagecopy($groundImg, $waterImg, $pos["posX"], $pos["posY"], 0, 0, $waterInfo["width"],$waterInfo["height"]);
		imagedestroy($waterImg);
		return $groundImg;
	}
	
	/* 内部使用的私有方法，用于保存图像，并保留原有图片格式 */
	private function createNewImage($newImg, $newName, $imgInfo){
	
		//$extensions = array('1'=>'.gif','2'=>'.jpg','3'=>'.png');
		
		
		//$newName = pathinfo($newName,PATHINFO_FILENAME).$extensions[$imgInfo['type']];
		
		$this->path = rtrim($this->path,"/")."/";
		
		$newImgpath =  $this->path.$newName;
		
		switch ($imgInfo["type"]) {
			case 1:				//gif
				$result = imageGIF($newImg, $newImgpath,100);
				break;
			case 2:				//jpg
				//header('Content-type: image/jpeg');
				
				$result = imageJPEG($newImg,$newImgpath,100);
				break;
			case 3:				//png
					
				//header('Content-type: image/png');
				imagePng($newImg, $newImgpath);
				/* if(pathinfo($this->srcFile,PATHINFO_EXTENSION) == 'jpg'){
					$old = imagecreatefrompng($newImgpath);
					imagejpeg($old,$this->path.pathinfo($newName,PATHINFO_FILENAME).'.jpg',100);
					unlink($newImgpath);
				} */
				
				break;
		}
		//echo $newImgpath;
		//exit("ImageBase.php--createNewImage方法 第行---2013-12-12  下午12:35:08");
		imagedestroy($newImg);
		//header('Content-type: image/png');
		
		rename($newImgpath, $this->path.$newName);
		//exit("ImageBase.php--createNewImage方法 第行---2013-12-12  下午12:33:31");
		return  $this->path.$newName;
	}
	
	// 添加白底
	private function addBackGround(){
		$background = imagecreatetruecolor($this->thumb['width'], $this->thumb['height']);
		$whitecolor = imagecolorallocate($background,255,255,255);
		imagefill( $background, 0, 0, $whitecolor );
		
		$pos = $this->position($this->thumb, $this->srcImgInfo, 5);
		if(!$pos) {
			self::$errorMsg = '图片不应比背景大';
			return false;
		}
		return $this->src_im = $this->copyImage($background, $this->src_im, $pos, $this->srcImgInfo); 
	}
	
	// 检查参数是否可用
	private function checkParams(){
		// 检查源图像
		if(!(file_exists($this->srcFile) && is_readable($this->srcFile))) {
			self::$errorMsg = '原文件不存在或不可读';
			return false;
		}
		
		// 检查存放路径
		if(!(file_exists($this->path) && is_writable($this->path))) {
			if(!@mkdir($this->path, 0755,true)) {
				self::$errorMsg = '存放新图片的目录不存在或不可写';
				return false;
			}
		}
		
		// 检查截取图像的配置参数是否有效
		if(!empty($this->cut)) {
			if(!($this->cut['x'] >= 0 && 
				$this->cut['y'] >= 0 && 
				$this->cut['width'] >0 && 
				$this->cut['height'] >0
			)) {
				self::$errorMsg = '截取图片的配置参数必须大于0';
				return false;
			}
		}
		
		// 检测缩放图像的配置参数是否有效
		if(!empty($this->thumb)) {
			if(!($this->thumb['width'] > 0 && $this->thumb['height'] >0)) {
				self::$errorMsg = '缩放功能的宽高必须大于0';
				return false;
			}
		}
		
		// 检测 水印功能的配置参数是否有效
		if(!empty($this->waterMarsk)) {
			if(is_array($this->waterMarsk['waterName']) && !empty($this->waterMarsk['waterName'])){
				foreach ($this->waterMarsk['waterName'] as $img){
					if(!(file_exists($img) && is_readable($img))) {
						self::$errorMsg = '水印图片不存在或不可读';
						
						return false;
					}
				}
			}else{
				if(!(file_exists($this->waterMarsk['waterName']) && is_readable($this->waterMarsk['waterName']))) {
					self::$errorMsg = '水印图片不存在或不可读';
					return false;
				}
			}
			
			if(is_array($this->waterMarsk['waterPos']) && !empty($this->waterMarsk['waterPos'])){
				foreach ($this->waterMarsk['waterPos'] as $imgPos){
					if(!($imgPos >= 0 && $imgPos <= 10 )) {
						self::$errorMsg = '水印图片位置在0-10之间';
						return false;
					}
				}
			}else{
				if(!( isset($this->waterMarsk['waterPos']) && $this->waterMarsk['waterPos'] >= 0 && $this->waterMarsk['waterPos'] <= 10 )) {
					self::$errorMsg = '水印图片位置在0-9之间';
					return false;
				} 
			}
			
		}
		return true;
	}
}


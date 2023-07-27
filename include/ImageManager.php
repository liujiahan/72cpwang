<?php
require_once 'ImageBase.php';
class ImageManager extends ImageBase{
	
	//默认的水印位置
	public static  $_waterArr = array(
			'waterName'=>array("images/water.png","images/topwater.png","images/bottomwater.png"),
			'waterPos'=>array(10,1,9)
	);

	//根据最大比例 得出裁切的开始位置 和裁切宽高
	private static function cutMainPic($srcfile,$dstfile,$dst_x=430,$dst_y=270){
		$imgInfo = getimagesize($srcfile) ;
	
		$src_x = $imgInfo[0] ;
		$src_y = $imgInfo[1] ;
		$img_type = $imgInfo[2] ;
	
		//判断要裁剪图片的x ，y 先裁剪出一个最大的比例，再压缩成目标尺寸
		$start_x = 0 ; //开始x位置
		$start_y = 0 ; //开始Y位置
		$cx = $src_x ; //要裁剪的宽度
		$cy = $src_y ; //要裁剪的高度
	
		if($dst_x * $src_y > $dst_y * $src_x){
			//裁 y  高度
			$cy = intval($dst_y * $src_x / $dst_x) ;
			$start_y = intval(($src_y - $cy)/2);
	
		}else if($dst_x * $src_y < $dst_y * $src_x){
			//裁 x  宽度
			$cx = intval($dst_x * $src_y / $dst_y) ;
			$start_x = intval(($src_x - $cx)/2);
		}else{
			//不用裁剪，直接压缩
		}
		return array($start_x,$start_y,$cx,$cy);
	
	}
	
	
	
	/**
	 * @param 源文件 $srcFile
	 * @param 目标文件路径 $dstpath
	 * @param 目标图片宽度 $width  
	 * @param 目标图片高度 $height
	 * @param 类型 1裁切（默认） 2 不裁切  $type
	 * @param 水印 $water
	 * @return Ambigous <boolean, string>
	 */
	static public function imageUpload($srcFile,$dstpath,$width='',$height='',$type=1,$water=array())
	{
		$image = array();
		
		//源文件
		$image['srcFile'] = $srcFile;
		//保存文件路径
		$image['path'] = dirname($dstpath);
		$image['newImgName'] = basename($dstpath);
		
		
		if($type==1){
				//按照等比先裁切出最大的图片
				list($image['cut']['x'],$image['cut']['y'],$image['cut']['width'],$image['cut']['height']) = self::cutMainPic($srcFile, $dstpath,$width,$height);
				$image['thumb']['width'] = $width;
				$image['thumb']['height'] = $height;
				if(empty($width) && empty($height))
				{
					list($image['thumb']['width'],$image['thumb']['height']) = getimagesize($srcFile);
				}
		}elseif($type==2){
				list($imgInfo["width"],$imgInfo["height"]) = getimagesize($srcFile);
				$image['thumb']['height'] = $imgInfo["height"];
				$image['thumb']['width'] = $imgInfo["width"] ;
				if((!empty($width) && empty($height)) || (empty($width) && !empty($height)) || (!empty($width) && !empty($height))){
			
					if(empty($width) && !empty($height))
					{
						$image['thumb']['height'] = $height;
						$image['thumb']['width'] = round(($imgInfo["width"]*$height)/$imgInfo["height"]) ;
					
					}else if(empty($height) && !empty($width)){
						$image['thumb']['width'] = $width;
						$image['thumb']['height'] =round(($imgInfo["height"]*$width)/$imgInfo["width"]);
					}else{
						$image['thumb']['height'] = $height;
						$image['thumb']['width'] = $width ;
					}
				}
		}
		
		 if(!empty($water)){
		 	$image['waterMarsk']['waterName'] = $water['waterName']?$water['waterName']:'';
		 	$image['waterMarsk']['waterPos'] = $water['waterPos']?$water['waterPos']:'';
		 }
	
		
		$return = ImageBase::getInstance()->set($image)->save();
		
		 if(ImageBase::$errorMsg){
			echo "<pre>";
					print_r(ImageBase::$errorMsg);
			echo "</pre>";
		} 
		return $return;
		
		
	}
	
	//根据传入的图片开始位置 长宽进行裁切
	public static function cutPhoto($srcFile, $dstpath, $cutx, $cuty, $cutw, $cuth){
		//源文件
		$image['srcFile'] = $srcFile;
		//保存文件路径
		$image['path'] = dirname($dstpath);
		$image['newImgName'] = basename($dstpath);
		//新名字
		$image['cut']['x'] = $cutx;
		$image['cut']['y'] = $cuty;
		$image['cut']['width'] = $cutw;
		$image['cut']['height'] = $cuth;
		return ImageBase::getInstance()->set($image)->save();
	}
	
}









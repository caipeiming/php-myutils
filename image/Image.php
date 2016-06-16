<?php

namespace com\jdk5\blog\Image;

class Image {
	/**
	 * @var int Default output image quality
	 *
	 */
	public $quality = 75;
	
	private $image, $filename, $original_info, $imagestring;
	private $width, $height, $scale, $fixed_given_size, $keep_ratio, $given_width, $given_height, $bgcolor;
	
	/**
	 * Load an image
	 *
	 * @param string $filename
	 *        	Path to image file
	 * @return \com\jdk5\blog\Image\Image
	 * @throws Exception
	 */
	function load($filename) {
		// Require GD library
		if (! extension_loaded ( 'gd' )) {
			throw new \Exception ( 'Required extension GD is not loaded.' );
		}
		$this->filename = $filename;
		return $this->get_meta_data ();
	}
	
	/**
	 * Get meta data of image or base64 string
	 *
	 * @param string|null $imagestring
	 *        	If omitted treat as a normal image
	 * @return \com\jdk5\blog\Image\Image
	 * @throws Exception
	 *
	 */
	protected function get_meta_data() {
		// gather meta data
		if (empty ( $this->imagestring )) {
			$info = getimagesize ( $this->filename );
			
			switch ($info ['mime']) {
				case 'image/gif' :
					$this->image = imagecreatefromgif ( $this->filename );
					break;
				case 'image/jpeg' :
					$this->image = imagecreatefromjpeg ( $this->filename );
					break;
				case 'image/png' :
					$this->image = imagecreatefrompng ( $this->filename );
					break;
				default :
					throw new \Exception ( 'Invalid image: ' . $this->filename );
					break;
			}
		} elseif (function_exists ( 'getimagesizefromstring' )) {
			$info = getimagesizefromstring ( $this->imagestring );
		} else {
			throw new \Exception ( 'PHP 5.4 is required to use method getimagesizefromstring' );
		}
		
		$this->original_info = array (
				'width' => $info [0],
				'height' => $info [1],
				'orientation' => $this->get_orientation (),
				'exif' => function_exists ( 'exif_read_data' ) && $info ['mime'] === 'image/jpeg' && $this->imagestring === null ? $this->exif = @exif_read_data ( $this->filename ) : null,
				'format' => preg_replace ( '/^image\//', '', $info ['mime'] ),
				'mime' => $info ['mime'] 
		);
		$this->width = 0;
		$this->height = 0;
		$this->scale = 0;
		$this->fixed_given_size = false;
		$this->keep_ratio = false;
		$this->given_width = 0;
		$this->given_height=0;
		
		imagesavealpha ( $this->image, true );
		imagealphablending ( $this->image, true );
		
		return $this;
	}
	
	/**
	 * Get the current orientation
	 * @return string portrait|landscape|square
	 */
	function get_orientation() {
		if (imagesx ( $this->image ) > imagesy ( $this->image )) {
			return 'landscape';
		}
		if (imagesx ( $this->image ) < imagesy ( $this->image )) {
			return 'portrait';
		}
		return 'square';
	}
	
	/**
	 * set generete image is fixed given size
	 * @param boolean $fixed_given_size
	 * @return \com\jdk5\blog\Image\Image
	 */
	function fixed_given_size($fixed_given_size) {
		$this->fixed_given_size = $fixed_given_size;
		return $this;
	}
	
	/**
	 * set background color
	 * @param array $bgcolor
	 * @return \com\jdk5\blog\Image\Image
	 */
	function bgcolor($bgcolor) {
		$this->bgcolor = $bgcolor;
		return $this;
	}

	/**
	 * set generete image is fixed_given_size
	 * @param boolean $fixed_given_size
	 * @return \com\jdk5\blog\Image\Image
	 */
	function keep_ratio($keep_ratio) {
		$this->keep_ratio = $keep_ratio;
		return $this;
	}
	
	/**
	 * set image width
	 * @param int $width
	 * @return \com\jdk5\blog\Image\Image
	 */
	function width($width) {
		$this->width = $width;
		return $this;
	}
	
	/**
	 * set image height
	 * @param int $height
	 * @return \com\jdk5\blog\Image\Image
	 */
	function height($height) {
		$this->height = $height;
		return $this;
	}
	
	/**
	 * set image width and height
	 * @param int $width
	 * @param int $height
	 * @return \com\jdk5\blog\Image\Image
	 */
	function size($width, $height){
		$this->width = $width;
		$this->height = $height;
		return $this;
	}
	
	/**
	 * set image scale
	 * @param double $scale
	 * @return \com\jdk5\blog\Image\Image
	 */
	function scale($scale){
		$this->width = $this->original_info['width'] * $scale;
		$this->height = $this->original_info['height'] * $scale;
		return $this;
	}
	
	/**
	 * Resize an image to the specified dimensions
	 *
	 * @param int $width        	
	 * @param int $height        	
	 *
	 * @return \com\jdk5\blog\Image\Image
	 *
	 */
	private function resize() {
		$width = $this->original_info['width'];
		$height = $this->original_info['height'];
		if ($this->width > 0 && $this->height > 0) {
			if ($this->fixed_given_size) {
				$this->given_width = $this->width;
				$this->given_height = $this->height;
				if (!$this->keep_ratio){
					$width = $this->width;
					$height = $this->height;
				}
			}
			if ($this->keep_ratio){
				$drawWidth = $this->width;
				$drawHeight = $this->height;
				$sourceRatio = doubleval( $width / $height);
				$targetRatio = doubleval( $this->width / $this->height);
				
				if ($sourceRatio != $targetRatio) {
					if ($sourceRatio > $targetRatio) {
						$drawHeight = intval(round($this->width / $sourceRatio));
					} else {
						$drawWidth = intval(round($this->height * $sourceRatio));
					}
				}
				if (!$this->fixed_given_size) {
					$this->given_width = $drawWidth;
					$this->given_height = $drawHeight;
				}
				$width = $drawWidth;
				$height = $drawHeight;
			}
		} else if ($this->scale > 0) {
			$width = intval($width * $this->scale);
			$height = intval($height * $this->scale);
		} else if ($this->width > 0 && $this->height == 0) {
			$height = intval($this->width * $height / $width);
			$width = intval($this->width);
		} else if ($this->width == 0 && $this->height > 0) {
			$width = intval($this->height * $width / $height);
			$height = intval($this->height);
		}
		if ($width <= 1 || $height <= 1) {
			throw new \Exception("width or height value error!");
		}
		$this->width = $width;
		$this->height = $height;
		
		$this->given_width = ($this->given_width == 0 ? $width : $this->given_width);
		$this->given_height = ($this->given_height == 0 ? $height : $this->given_height);
		
		$this->copy($width, $height, 0, 0, 0, 0, $width, $height);
		
		if ($this->keep_ratio) {
			$this->resize_image_and_keep_ratio();
		}
	}
	
	/**
	 * 等比例压缩图片,支持图片格式jpg,jpeg,png
	 * @param string $dst_dir	上传的文件夹
	 * @param string $dst_name	上传后的名称，不包括扩展名
	 * @param int $maxWidth	如果需要等比例压缩图片，指定压缩后的最大宽度，默认为200
	 * @param int $maxHeight	如果需要等比例压缩图片，指定压缩后的最大高度，默认为200
	 * @return boolean	成功返回true，否则返回false
	 */
	private function resize_image_and_keep_ratio() {
		//设置描绘的x、y坐标，高度、宽度
		$dst_x = $dst_y = $src_x = $src_y = 0;
		$ratio = min ( $this->given_height / $this->original_info['height'], $this->given_width / $this->original_info['width'] );
		$dst_h = ceil ( $this->original_info['height'] * $ratio );
		$dst_w = ceil ( $this->original_info['width'] * $ratio );
		$dst_x = ($this->given_width - $dst_w)/2;
		$dst_y = ($this->given_height - $dst_h)/2;
		return $this->copy($this->given_width, $this->given_height, $dst_x, $dst_y, $src_x, $src_y, 
				$dst_w, $dst_h);
	}
	
	/**
	 * copy original image to new size
	 * @param int $dst_w
	 * @param int $dst_h
	 * @param int $dst_x
	 * @param int $dst_y
	 * @param int $src_x
	 * @param int $src_y
	 * @param int $draw_w
	 * @param int $draw_h
	 * @return boolean
	 */
	private function copy($dst_w, $dst_h, $dst_x=0, $dst_y=0, $src_x=0, $src_y=0, $draw_w=0, $draw_h=0){
		// Generate new GD image
		$new = imagecreatetruecolor($dst_w, $dst_h);
		$draw_w = $draw_w == 0 ? $this->original_info['width'] : $draw_w;
		$draw_h = $draw_h == 0 ? $this->original_info['height'] : $draw_h;
		if( $this->original_info['format'] === 'gif' ) {
			// Preserve transparency in GIFs
			$transparent_index = imagecolortransparent($this->image);
			$palletsize = imagecolorstotal($this->image);
			if ($transparent_index >= 0 && $transparent_index < $palletsize) {
				$transparent_color = imagecolorsforindex($this->image, $transparent_index);
				$transparent_index = imagecolorallocate($new, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
				imagefill($new, 0, 0, $transparent_index);
				imagecolortransparent($new, $transparent_index);
	
				if (!empty($this->bgcolor)) {
					$bg = imagecolorallocate($new, $this->bgcolor[0], $this->bgcolor[1], $this->bgcolor[2]);
					imagefill($new, 0, 0, $bg);
				}
			}
		} else {
			// Preserve transparency in PNGs (benign for JPEGs)
			imagealphablending($new, false);
			imagesavealpha($new, true);
			$color = imagecolorallocatealpha($new, 0, 0, 0, 127);
			imagefill($new, 0, 0, $color);
			if (!empty($this->bgcolor)) {
				$bg = imagecolorallocate($new, $this->bgcolor[0], $this->bgcolor[1], $this->bgcolor[2]);
					imagefill($new, 0, 0, $bg);
			}
		}
		
		// Resize
		$flag = imagecopyresampled ( $new, $this->image, $dst_x, $dst_y, $src_x, $src_y, $draw_w, $draw_h, 
				$this->original_info['width'], $this->original_info['height'] );
		if ($flag) {
			$this->image = $new;
			$this->original_info['width'] = $dst_w;
			$this->original_info['height'] = $dst_h;
		} else {
			throw new \Exception ( 'copy image error' );
		}
		return $flag;
	}
	
	/**
	 * Save an image
	 *
	 * The resulting format will be determined by the file extension.
	 *
	 * @param null|string $filename
	 *        	If omitted - original file will be overwritten
	 * @param null|int $quality
	 *        	Output image quality in percents 0-100
	 * @param null|string $format
	 *        	The format to use; determined by file extension if null
	 *        	
	 * @return \com\jdk5\blog\Image\Image
	 * @throws Exception
	 *
	 */
	function save($filename = null, $quality = null, $format = null) {
		$this->resize();
		
		// Determine quality, filename, and format
		$quality = $quality ?  : $this->quality;
		$filename = $filename ?  : $this->filename;
		if (! $format) {
			$format = $this->file_ext ( $filename ) ?  : $this->original_info ['format'];
		}
		
		// Create the image
		switch (strtolower ( $format )) {
			case 'gif' :
				$result = imagegif ( $this->image, $filename );
				break;
			case 'jpg' :
				imageinterlace ( $this->image, true );
				$result = imagejpeg ( $this->image, $filename, round ( $quality ) );
				break;
			case 'jpeg' :
				imageinterlace ( $this->image, true );
				$result = imagejpeg ( $this->image, $filename, round ( $quality ) );
				break;
			case 'png' :
				$result = imagepng ( $this->image, $filename, round ( 9 * $quality / 100 ) );
				break;
			default :
				throw new \Exception ( 'Unsupported format' );
		}
		
		if (! $result) {
			throw new \Exception( 'Unable to save image: ' . $filename );
		}
		
		return $this;
	}
	
	/**
	 * Returns the file extension of the specified file
	 *
	 * @param string $filename        	
	 *
	 * @return string
	 *
	 */
	protected function file_ext($filename) {
		if (! preg_match ( '/\./', $filename )) {
			return '';
		}
		
		return preg_replace ( '/^.*\./', '', $filename );
	}
	
	/**
	 * Changes the quality of the image
	 * @param float|int $opacity 0-100
	 * @return \com\jdk5\blog\Image\Image
	 */
	function quality($quality) {
		$this->quality = $quality;
		return $this;
	}
	
	/**
	 * Ensures $value is always within $min and $max range.
	 *
	 * If lower, $min is returned. If higher, $max is returned.
	 *
	 * @param int|float $value        	
	 * @param int|float $min        	
	 * @param int|float $max        	
	 *
	 * @return int|float
	 *
	 */
	protected function keep_within($value, $min, $max) {
		if ($value < $min) {
			return $min;
		}
		
		if ($value > $max) {
			return $max;
		}
		
		return $value;
	}
}
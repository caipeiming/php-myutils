# php-myutils
## IDValidator 身份证验证

```php
header("Content-type: text/html; charset=utf-8");

include 'IDValidator.php';
$v = com\jdk5\blog\IDValidator\IDValidator::getInstance();

//生成一个18位身份证号
$id = $v->makeID();
//获取身份证信息
$info = $v->getInfo($id);
var_dump($info);
//生成一个15位身份证号
$id = $v->makeID(true);
$info = $v->getInfo($id);
var_dump($info);

//验证身份证号是否正确
var_dump($v->isValid("123456789012345678"));
```

## image 图片处理工具


```php
use com\jdk5\blog\Image\Image;
require '../Image.php';

$img = new Image();
$watermark = array(
	"watermarkater.png",	//水印文件
	self::CENTER,	//水印的位置，分别为:center|top|left|bottom|right|top left|top right|bottom left|bottom right
	1,	//水印的透明度，可以为0-1的任意数值，默认为1
	0,	//加水印的x轴偏移量，默认为0
	0,	//加水印的y轴偏移量，默认为0
	self::WATERMARK_DIAGONAL_NEG	//水印的旋转角度，可以为-360-360，如果为WATERMARK_DIAGONAL_POS或WATERMARK_DIAGONAL_NEG，则沿着生成图片的对角线旋转，默认为0
);
$img->load('org.jpg')
	//->width(200)	//设置生成图片的宽度，高度将按照宽度等比例缩放
	//->height(200)	//设置生成图片的高度，宽度将按照高度等比例缩放
	->size(300, 300)	//设置生成图片的宽度和高度
	->fixed_given_size(true)	//生成的图片是否以给定的宽度和高度为准
	->keep_ratio(true)		//是否保持原图片的原比例
	->bgcolor("#ffffff")	//设置背景颜色，按照rgb格式
	->rotate(20)	//指定旋转的角度
	->quality(50)	//设置生成图片的质量 0-100，如果生成的图片格式为png格式，数字越大，压缩越大，如果是其他格式，如jpg，gif，数组越小，压缩越大
	->set_watermark($watermark)		//添加水印
	->save('processed/org-width-resize.jpg');	//保存生成图片的路径
```
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
$img->load('org.jpg')
	//->width(200)	//设置生成图片的宽度，高度将按照宽度等比例缩放
	//->height(200)	//设置生成图片的高度，宽度将按照高度等比例缩放
	->size(300, 300)	//设置生成图片的宽度和高度
	->fixed_given_size(true)	//生成的图片是否以给定的宽度和高度为准
	->keep_ratio(true)		//是否保持原图片的原比例
	->bgcolor(array(100, 0, 0))	//设置背景颜色，按照rgb格式
	->quality(50)	//设置生成图片的质量 0-100
	->save('processed/org-width-resize.jpg');	//保存生成图片的路径
```
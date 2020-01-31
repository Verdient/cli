# PHP CLI Tools
PHP 命令行工具

## 安装
```
composer require verdient/cli
```
## 打印内容到控制台
```php
Console::stdout('Hello World');
```
## 打印内容到控制台并自动换行
```php
Console::output('Hello World');
```
## 提示并获取用户输入
```php
$message = 'Hello World'; //提示信息
$default = null; //默认答案 默认为空

Console::prompt($message, $default);
```
## 询问用户是否继续
```php
$message = 'Hello World'; //提示信息
$default = false; //默认动作 默认为拒绝

Console::confirm($message, $default);
```
## 进度条
```php
$down = 0; //必须从0开始
$count = 100; //总数，当$down == $count时，进度条结束
$prefix = '进度条'; // 提示信息 可选
$width = 50; //进度条宽度 默认为50
while($down <= $count){
	sleep(1);
	Console::progress($down, $count, $prefix, $width);
	$down += 10;
}
```
## 输出彩色文本
所有含有输出行为的方法（例如stdout, output, prompt, confirm, progress等）均支持对文本进行着色后输出

### 所有文本都进行着色
通过在方法的最后追加格式化参数（可以追加多个参数），可以使其对文本进行着色后输出
例如
```php
Console::output('Hello World', Console::FG_RED, Console::BG_GREY);
```
可以使输出的文字变为白底红字

### 对部分文字进行着色
如果只想对部分文字进行着色，可将字符串通过colour着色后传入相应函数
```php
$message = Console::colour('Hello', Console::FG_RED);
$message .= ' ' . Console::colour('World', Console::FG_YELLOW);
Console::output($message);
```
这样输出的结果就是`Hello`为`红色`, `World`为`黄色`
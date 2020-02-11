# PHP CLI Tools
PHP 命令行工具

## 安装
```
composer require verdient/cli
```
## 引入文件
```php
use Verdient\cli\Console;
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
$down = 0;
$count = 100; //总数，当$down == $count时，进度条结束
$prefix = '进度条'; // 提示信息 可选
$width = 50; //进度条宽度 默认为50
while($down <= $count){
	sleep(1);
	Console::progress($down, $count, $prefix, $width);
	$down += 10;
}
```
## 打印列表
```php
/**
 * 要打印的数据
 * 格式为二维数组
 * 数组内每一个数组代表一行
 */
$data = [
	[
		Console::colour('php', Console::FG_CYAN, Console::BOLD),
		0,
		'N/A',
		Console::colour('fork', Console::FG_BLACK, Console::BG_GREY, Console::BOLD),
		'64990',
		Console::colour('online', Console::FG_GREEN, Console::BOLD),
		0,
		'65m',
		'0%',
		'10.1 MB',
		Console::colour('zhufagui', Console::BOLD),
		'disabled'
	]
];

/**
 * 以一维数组表示的表头
 * 默认为空数组
 */
 $headers = [
	'App name',
	'id',
	'version',
	'mode',
	'pid',
	'status',
	'restart',
	'uptime',
	'cpu',
	'mem',
	'user',
	'watching'
];
Console::table($data, $headers);
```
## 输出彩色文本
所有含有输出行为的方法（例如stdout, output, prompt, confirm, progress, table等）均支持对文本进行着色后输出

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
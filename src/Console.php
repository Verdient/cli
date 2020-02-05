<?php
namespace cli;

/**
 * Console
 * 打印
 * -------
 * @author Verdient。
 */
class Console
{
	// 字体颜色
	const FG_BLACK = 30;
	const FG_RED = 31;
	const FG_GREEN = 32;
	const FG_YELLOW = 33;
	const FG_BLUE = 34;
	const FG_PURPLE = 35;
	const FG_CYAN = 36;
	const FG_GREY = 37;
	// 背景颜色
	const BG_BLACK = 40;
	const BG_RED = 41;
	const BG_GREEN = 42;
	const BG_YELLOW = 43;
	const BG_BLUE = 44;
	const BG_PURPLE = 45;
	const BG_CYAN = 46;
	const BG_GREY = 47;
	// 字体样式
	const RESET = 0;
	const NORMAL = 0;
	const BOLD = 1;
	const ITALIC = 3;
	const UNDERLINE = 4;
	const BLINK = 5;
	const NEGATIVE = 7;
	const CONCEALED = 8;
	const CROSSED_OUT = 9;
	const FRAMED = 51;
	const ENCIRCLED = 52;
	const OVERLINED = 53;

	/**
	 * @var Boolean $isColorSupported
	 * 是否支持颜色
	 * ------------------------------
	 * @author Verdient。
	 */
	public static $isColorEnabled = null;

	/**
	 * stdin([Boolean $trim = true])
	 * 标准输入
	 * -----------------------------
	 * @param Boolean $trim 是否移除尾部空格
	 * -----------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function stdin($trim = true){
		return $trim ? rtrim(fgets(\STDIN), PHP_EOL) : fgets(\STDIN);
	}

	/**
	 * stdout(String $string[, Integer ...$formats])
	 * 标准输出
	 * ---------------------------------------------
	 * @param String $string 文本内容
	 * @param Integer ...$formats 格式化参数
	 * ------------------------------------
	 * @return Integer|False
	 * @author Verdient。
	 */
	public static function stdout($string, ...$formats){
		return fwrite(\STDOUT, static::colour($string, ...$formats));
	}

	/**
	 * stderr(String $string[, Integer ...$formats])
	 * 标准错误输出
	 * ---------------------------------------------
	 * @param String $string 文本内容
	 * @param Integer ...$formats 格式化参数
	 * -----------------------------------
	 * @return Integer|False
	 * @author Verdient。
	 */
	public static function stderr($string, ...$formats){
		return fwrite(\STDERR, static::colour($string, ...$formats));
	}

	/**
	 * input([String $prompt = null, Integer ...$formats])
	 * 输入
	 * ---------------------------------------------------
	 * @param String $prompt 提示
	 * @param Integer ...$formats 格式化参数
	 * ------------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function input($prompt = null, ...$formats){
		if(isset($prompt)){
			static::stdout($prompt, ...$formats);
		}
		return static::stdin();
	}

	/**
	 * output(String $string[, Integer ...$formats])
	 * 输出
	 * ---------------------------------------------
	 * @param String $string 字符串
	 * @param Integer ...$formats 格式化参数
	 * -----------------------------------
	 * @return Integer|False
	 * @author Verdient。
	 */
	public static function output($string, ...$formats){
		return static::stdout($string . PHP_EOL, ...$formats);
	}

	/**
	 * error(String $string[, Integer ...$formats])
	 * 输出错误信息
	 * --------------------------------------------
	 * @param String $string 字符串
	 * @param Integer ...$formats 格式化参数
	 * -----------------------------------
	 * @return Integer|False
	 * @author Verdient。
	 */
	public static function error($string, ...$formats){
		return static::stderr($string . PHP_EOL, ...$formats);
	}

	/**
	 * scrollUp([Integer $lines = 1])
	 * 向上滑动
	 * ------------------------------
	 * @param Integer $lines 滑动行数
	 * -----------------------------
	 * @author Verdient。
	 */
	public static function scrollUp($lines = 1){
		echo "\033[" . (int) $lines . 'S';
	}

	/**
	 * scrollDown([Integer $lines = 1])
	 * 向下滑动
	 * --------------------------------
	 * @param Integer $lines 滑动行数
	 * -----------------------------
	 * @author Verdient。
	 */
	public static function scrollDown($lines = 1){
		echo "\033[" . (int) $lines . 'T';
	}

	/**
	 * clearScreenBeforeCursor()
	 * 光标前清屏
	 * ------------------------
	 * @author Verdient。
	 */
	public static function clearScreenBeforeCursor(){
		echo "\033[1J";
	}

	/**
	 * clearScreenAfterCursor()
	 * 光标后清屏
	 * ------------------------
	 * @author Verdient。
	 */
	public static function clearScreenAfterCursor(){
		echo "\033[0J";
	}

	/**
	 * clearScreen()
	 * 清屏
	 * -------------
	 * @author Verdient。
	 */
	public static function clearScreen(){
		echo "\033[2J";
	}

	/**
	 * moveCursorTo(Integer $column[, Integer $row = null])
	 * 将光标移动到
	 * ----------------------------------------------------
	 * @param Integer $column 列
	 * @param Integer $row = null 行
	 * -----------------------------
	 * @author Verdient。
	 */
	public static function moveCursorTo($column, $row = null){
		if($row === null){
			echo "\033[" . (int) $column . 'G';
		}else{
			echo "\033[" . (int) $row . ';' . (int) $column . 'H';
		}
	}

	/**
	 * confirm(String $message[, Boolean $default = false, Integer ...$formats])
	 * 确认
	 * -------------------------------------------------------------------------
	 * @param String $message 提示信息
	 * @param Boolean $default 默认值
	 * @param Integer ...$formats 格式化参数
	 * ------------------------------------
	 * @author Verdient。
	 */
	public static function confirm($message, $default = false, ...$formats){
		$first = true;
		while(true){
			if(!$first){
				static::scrollDown(1);
				static::clearScreenAfterCursor();
			}
			$first = false;
			$input = static::input($message . ' [' . ($default ? 'y' : 'n') . ']: ', ...$formats);
			if(empty($input)){
				return $default;
			}
			if(!strcasecmp($input, 'y') || !strcasecmp($input, 'yes')){
				return true;
			}
			if(!strcasecmp($input, 'n') || !strcasecmp($input, 'no')){
				return false;
			}
		}
	}

	/**
	 * prompt(String $message[, String $default = null, Integer ...$formats])
	 * 提问
	 * ----------------------------------------------------------------------
	 * @param String $message 提示信息
	 * @param String $default 默认答案
	 * @param Integer ...$formats 格式化参数
	 * -----------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function prompt($message, $default = null, ...$formats){
		$first = true;
		while(true){
			if(!$first){
				static::scrollDown(1);
				static::clearScreenAfterCursor();
			}
			$first = false;
			$input = static::input($message . ($default ? ' [' . $default . ']' : '') . ': ', ...$formats);
			if(empty($input)){
				if($default !== null){
					return $default;
				}
			}else{
				return $input;
			}
		}
	}

	/**
	 * progress(Integer $down, $total[, String $prefix = null, Integer $width = 50, Integer ...$formats])
	 * 进度条
	 * --------------------------------------------------------------------------------------------------
	 * @param Integer $down 已完成的，第一次调用必须为0
	 * @param Integer $total 总数
	 * @param String $prefix 提示信息
	 * @param Integer $width 宽度
	 * @param Integer ...$formats 格式化参数
	 * --------------------------------------------
	 * @author Verdient。
	 */
	public static function progress($down, $total, $prefix = null, $width = 50, ...$formats){
		if($down > $total){
			$down = $total;
		}
		$ratio = $down / $total;
		$num = ($ratio * $width);
		$completed = str_repeat('=', $num);
		$undone = str_repeat(' ', $width - $num);
		$percent = sprintf("%.2f", ($ratio * 100));
		$str = ($prefix ? ($prefix . ' ') : '') . '[' . $completed . $undone . '] ' . $percent . '%';
		static::moveCursorTo(1);
		if($down !== $total){
			static::stdout($str, ...$formats);
		}else{
			static::output($str, ...$formats);
		}
	}

	/**
	 * streamSupportsAnsiColors([Mixed $stream = \STDOUT])
	 * 是否支持ANSI颜色
	 * ---------------------------------------------------
	 * @param Mixed $stream 输出流
	 * --------------------------
	 * @author Verdient。
	 */
	public static function streamSupportsAnsiColors($stream = null){
		return true;
		if($stream === null && defined('STDOUT')){
			$stream = \STDOUT;
		}
		if(DIRECTORY_SEPARATOR === '\\'){
			return getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON';
		}
		return function_exists('posix_isatty') && @posix_isatty($stream);
	}

	/**
	 * isColorEnabled()
	 * 是否支持颜色
	 * ----------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public static function isColorEnabled(){
		if(static::$isColorEnabled === null){
			static::$isColorEnabled = static::streamSupportsAnsiColors();
		}
		return static::$isColorEnabled;
	}

	/**
	 * colour(String $string, Integer ...$formats)
	 * 着色
	 * -------------------------------------------
	 * @param String $string 字符串
	 * @param Integer $formats 格式化参数
	 * ---------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function colour($string, ...$formats){
		if(!empty($formats) && static::isColorEnabled()){
			return static::ansiFormat($string, $formats);
		}
		return $string;
	}

	/**
	 * fade(String $string)
	 * 着色
	 * --------------------
	 * @param String $string 字符串
	 * ---------------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function fade($string){
		return preg_replace('/\033\[(\d|\d\d)(;(\d|\d\d)){0,}m/', '', $string);
	}

	/**
	 * ansiFormat(String $string, Array $format = [])
	 * ANSI格式化
	 * ----------------------------------------------
	 * @param String $string 字符串
	 * @param Array $format 格式化的选项
	 * -------------------------------
	 * @return String
	 * @author Verdient。
	 */
	public static function ansiFormat($string, $format = []){
		$code = implode(';', $format);
		return "\033[0m" . ($code !== '' ? "\033[" . $code . 'm' : '') . $string . "\033[0m";
	}

	/**
	 * stringWidth(String $string)
	 * 计算字符宽度
	 * ---------------------------
	 * @param String $string 字符串
	 * ---------------------------
	 * @return Integer
	 * @author Verdient。
	 */
	public static function stringWidth($string){
		return iconv_strlen(static::fade($string));
	}

	/**
	 * calculeteColumnsWidth(Array $data, Array $headers)
	 * 计算列宽度
	 * --------------------------------------------------
	 * @param Array $data 数据
	 * @param Array $headers 头部
	 * --------------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected static function calculeteColumnsWidth($data, $headers){
		$columnsWidth = [];
		array_unshift($data, $headers);
		foreach($data as $row){
			foreach($row as $index => $element){
				$length = static::stringWidth($element);
				if(!isset($columnsWidth[$index])){
					$columnsWidth[$index] = 0;
				}
				if($length > $columnsWidth[$index]){
					$columnsWidth[$index] = $length;
				}
			}
		}
		return $columnsWidth;
	}

	/**
	 * table(Array $data[, Array $headers = [], Integer ...$formats])
	 * 列表展示数据
	 * --------------------------------------------------------------
	 * @param Array $data 要展示的数据
	 * @param Array $headers 表头
	 * @param Integer $formats 格式化参数
	 * --------------------------------
	 * @author Verdient。
	 */
	public static function table($data, $headers = [], ...$formats){
		$columnsWidth = static::calculeteColumnsWidth($data, $headers);
		$leftTop = '┌';
		$leftBottom = '└';
		$rightTop = '┐';
		$rightBottom = '┘';
		$horizontal = '─';
		$upright = '│';
		$topMiddle = '┬';
		$leftMiddle = '├';
		$middle = '┼';
		$rightMiddle = '┤';
		$bottomMiddle = '┴';
		$topString = $leftTop;
		$middleString = $leftMiddle;
		$bottomString = $leftBottom;
		$horizontals = [];
		foreach($columnsWidth as $width){
			$horizontals[] = str_repeat($horizontal, $width + 1);
		}
		$topString .= implode($topMiddle, $horizontals) . $rightTop;
		$middleString .= implode($middle, $horizontals) . $rightMiddle;
		$bottomString .= implode($bottomMiddle, $horizontals) . $rightBottom;
		$columnCount = count($columnsWidth);
		static::output($topString);
		$noHeaders = empty($headers);
		if(!$noHeaders){
			array_unshift($data, $headers);
		}
		foreach($data as $rowIndex => &$row){
			if($rowIndex !== 0){
				static::output($middleString);
			}
			$columnDiff = $columnCount - count($row);
			for($i = 0; $i < $columnDiff; $i++){
				$row[] = '';
			}
			foreach($row as $colIndex => &$element){
				$diff = $columnsWidth[$colIndex] - static::stringWidth($element);
				$element = ' ' . $element;
				if($diff > 0){
					$element .= str_repeat(' ', $diff);
				}
				if(!$noHeaders && $rowIndex === 0){
					$element = static::colour($element, static::FG_CYAN, static::BOLD);
				}else{
					$element = static::colour($element, ...$formats);
				}
			}
			$dataString = $upright . implode($upright, $row) . $upright;
			static::output($dataString);
		}
		static::output($bottomString);
	}
}
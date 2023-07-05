<?php

namespace Verdient\cli;

/**
 * 打印
 * @author Verdient。
 */
class Console
{
    /**
     * 字体颜色
     * @author Verdient。
     */
    const FG_BLACK = 30;
    const FG_RED = 31;
    const FG_GREEN = 32;
    const FG_YELLOW = 33;
    const FG_BLUE = 34;
    const FG_PURPLE = 35;
    const FG_CYAN = 36;
    const FG_GREY = 37;

    /**
     * 背景颜色
     * @author Verdient。
     */
    const BG_BLACK = 40;
    const BG_RED = 41;
    const BG_GREEN = 42;
    const BG_YELLOW = 43;
    const BG_BLUE = 44;
    const BG_PURPLE = 45;
    const BG_CYAN = 46;
    const BG_GREY = 47;

    /**
     * 字体样式
     * @author Verdient。
     */
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
     * @var bool 是否支持颜色
     * @author Verdient。
     */
    public static $isColorEnabled = null;

    /**
     * 标准输入
     * @param bool $trim 是否移除尾部空格
     * @return string
     * @author Verdient。
     */
    public static function stdin($trim = true)
    {
        return $trim ? rtrim(fgets(\STDIN), PHP_EOL) : fgets(\STDIN);
    }

    /**
     * 标准输出
     * @param string $string 文本内容
     * @param int ...$formats 格式化参数
     * @return int|false
     * @author Verdient。
     */
    public static function stdout($string, ...$formats)
    {
        return fwrite(\STDOUT, static::colour($string, ...$formats));
    }

    /**
     * 标准错误输出
     * @param string $string 文本内容
     * @param int ...$formats 格式化参数
     * @return int|false
     * @author Verdient。
     */
    public static function stderr($string, ...$formats)
    {
        return fwrite(\STDERR, static::colour($string, ...$formats));
    }

    /**
     * 输入
     * @param string $prompt 提示
     * @param int ...$formats 格式化参数
     * @return string
     * @author Verdient。
     */
    public static function input($prompt = null, ...$formats)
    {
        if (isset($prompt)) {
            static::stdout($prompt, ...$formats);
        }
        return static::stdin();
    }

    /**
     * 输出
     * @param string $string 字符串
     * @param int ...$formats 格式化参数
     * @return int|false
     * @author Verdient。
     */
    public static function output($string, ...$formats)
    {
        return static::stdout($string . PHP_EOL, ...$formats);
    }

    /**
     * 输出错误信息
     * @param string $string 字符串
     * @param int ...$formats 格式化参数
     * @return int|false
     * @author Verdient。
     */
    public static function error($string, ...$formats)
    {
        return static::stderr($string . PHP_EOL, ...$formats);
    }

    /**
     * 向上滑动
     * @param int $lines 滑动行数
     * @author Verdient。
     */
    public static function scrollUp($lines = 1)
    {
        echo "\033[" . (int) $lines . 'S';
    }

    /**
     * 向下滑动
     * @param int $lines 滑动行数
     * @author Verdient。
     */
    public static function scrollDown($lines = 1)
    {
        echo "\033[" . (int) $lines . 'T';
    }

    /**
     * 光标前清屏
     * @author Verdient。
     */
    public static function clearScreenBeforeCursor()
    {
        echo "\033[1J";
    }

    /**
     * 光标后清屏
     * @author Verdient。
     */
    public static function clearScreenAfterCursor()
    {
        echo "\033[0J";
    }

    /**
     * 清屏
     * @author Verdient。
     */
    public static function clearScreen()
    {
        echo "\033[2J";
    }

    /**
     * 将光标移动到
     * @param int $column 列
     * @param int $row = null 行
     * @author Verdient。
     */
    public static function moveCursorTo($column, $row = null)
    {
        if ($row === null) {
            echo "\033[" . (int) $column . 'G';
        } else {
            echo "\033[" . (int) $row . ';' . (int) $column . 'H';
        }
    }

    /**
     * 确认
     * @param string $message 提示信息
     * @param bool|null $default 默认值
     * @param int ...$formats 格式化参数
     * @return bool
     * @author Verdient。
     */
    public static function confirm($message, $default = false, ...$formats)
    {
        $first = true;
        if (is_bool($default)) {
            $defaultAnswer = $default ? 'y' : 'n';
        } else {
            $defaultAnswer = null;
        }
        while (true) {
            if (!$first) {
                static::scrollDown(1);
                static::clearScreenAfterCursor();
            }
            $first = false;
            $input = static::prompt($message, $defaultAnswer, ...$formats);
            if (empty($input)) {
                return $default;
            }
            if (!strcasecmp($input, 'y') || !strcasecmp($input, 'yes')) {
                return true;
            }
            if (!strcasecmp($input, 'n') || !strcasecmp($input, 'no')) {
                return false;
            }
        }
    }

    /**
     * 提问
     * @param string $message 提示信息
     * @param string|null $default 默认答案
     * @param int ...$formats 格式化参数
     * @return string
     * @author Verdient。
     */
    public static function prompt($message, $default = null, ...$formats)
    {
        $first = true;
        while (true) {
            if (!$first) {
                static::scrollDown(1);
                static::clearScreenAfterCursor();
            }
            $first = false;
            $input = static::input($message . ($default ? ' [' . $default . ']' : '') . ': ', ...$formats);
            if (empty($input)) {
                if ($default !== null) {
                    return $default;
                }
            } else {
                return $input;
            }
        }
    }

    /**
     * 进度条
     * @param int $down 已完成的
     * @param int $total 总数
     * @param string $prefix 提示信息
     * @param int $width 宽度
     * @param int ...$formats 格式化参数
     * @author Verdient。
     */
    public static function progress($down, $total, $prefix = null, $width = 50, ...$formats)
    {
        if ($down > $total) {
            $down = $total;
        }
        $ratio = $down / $total;
        $num = ($ratio * $width);
        $completed = str_repeat('=', $num);
        $undone = str_repeat(' ', $width - $num);
        $percent = sprintf("%.2f", ($ratio * 100));
        $str = ($prefix ? ($prefix . ' ') : '') . '[' . $completed . $undone . '] ' . $percent . '%';
        static::moveCursorTo(1);
        if ($down !== $total) {
            static::stdout($str, ...$formats);
        } else {
            static::output($str, ...$formats);
        }
    }

    /**
     * 是否支持ANSI颜色
     * @param mixed $stream 输出流
     * @author Verdient。
     */
    public static function streamSupportsAnsiColors($stream = null)
    {
        return true;
        if ($stream === null && defined('STDOUT')) {
            $stream = \STDOUT;
        }
        if (DIRECTORY_SEPARATOR === '\\') {
            return getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON';
        }
        return function_exists('posix_isatty') && @posix_isatty($stream);
    }

    /**
     * 是否支持颜色
     * @return bool
     * @author Verdient。
     */
    public static function isColorEnabled()
    {
        if (static::$isColorEnabled === null) {
            static::$isColorEnabled = static::streamSupportsAnsiColors();
        }
        return static::$isColorEnabled;
    }

    /**
     * 着色
     * @param string $string 字符串
     * @param int $formats 格式化参数
     * @return string
     * @author Verdient。
     */
    public static function colour($string, ...$formats)
    {
        if (!empty($formats) && static::isColorEnabled()) {
            return static::ansiFormat($string, $formats);
        }
        return $string;
    }

    /**
     * 去除颜色
     * @param string $string 字符串
     * @return string
     * @author Verdient。
     */
    public static function fade($string)
    {
        return preg_replace('/\033\[(\d|\d\d)(;(\d|\d\d)){0,}m/', '', $string);
    }

    /**
     * ANSI格式化
     * @param string $string 字符串
     * @param array $format 格式化的选项
     * @return string
     * @author Verdient。
     */
    public static function ansiFormat($string, $format = [])
    {
        $code = implode(';', $format);
        return "\033[0m" . ($code !== '' ? "\033[" . $code . 'm' : '') . $string . "\033[0m";
    }

    /**
     * 计算字符宽度
     * @param string $string 字符串
     * @return int
     * @author Verdient。
     */
    public static function stringWidth($string)
    {
        $string = static::fade($string);
        $length = iconv_strlen($string);
        $length += (strlen($string) - $length) / 2;
        return $length;
    }

    /**
     * 计算列宽度
     * @param array $data 数据
     * @param array $headers 头部
     * @return array
     * @author Verdient。
     */
    protected static function calculateColumnsWidth($data, $headers)
    {
        $columnsWidth = [];
        array_unshift($data, $headers);
        foreach ($data as $row) {
            foreach ($row as $index => $element) {
                $length = static::stringWidth($element) + 1;
                if (!isset($columnsWidth[$index])) {
                    $columnsWidth[$index] = 0;
                }
                if ($length > $columnsWidth[$index]) {
                    $columnsWidth[$index] = $length;
                }
            }
        }
        return $columnsWidth;
    }

    /**
     * 列表展示数据
     * @param array $data 要展示的数据
     * @param array $headers 表头
     * @param int $formats 格式化参数
     * @author Verdient。
     */
    public static function table($data, $headers = [], ...$formats)
    {
        $columnsWidth = static::calculateColumnsWidth($data, $headers);
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
        $topstring = $leftTop;
        $middlestring = $leftMiddle;
        $bottomstring = $leftBottom;
        $horizontals = [];
        foreach ($columnsWidth as $width) {
            $horizontals[] = str_repeat($horizontal, $width + 1);
        }
        $topstring .= implode($topMiddle, $horizontals) . $rightTop;
        $middlestring .= implode($middle, $horizontals) . $rightMiddle;
        $bottomstring .= implode($bottomMiddle, $horizontals) . $rightBottom;
        $columnCount = count($columnsWidth);
        static::output($topstring);
        $noHeaders = empty($headers);
        if (!$noHeaders) {
            array_unshift($data, $headers);
        }
        foreach ($data as $rowIndex => &$row) {
            if ($rowIndex !== 0) {
                static::output($middlestring);
            }
            $columnDiff = $columnCount - count($row);
            for ($i = 0; $i < $columnDiff; $i++) {
                $row[] = '';
            }
            foreach ($row as $colIndex => &$element) {
                $diff = $columnsWidth[$colIndex] - static::stringWidth($element);
                $element = ' ' . $element;
                if ($diff > 0) {
                    $element .= str_repeat(' ', $diff);
                }
                if (!$noHeaders && $rowIndex === 0) {
                    $element = static::colour($element, static::FG_CYAN, static::BOLD);
                } else {
                    $element = static::colour($element, ...$formats);
                }
            }
            $datastring = $upright . implode($upright, $row) . $upright;
            static::output($datastring);
        }
        static::output($bottomstring);
    }
}

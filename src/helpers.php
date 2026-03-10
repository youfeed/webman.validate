<?php
// +----------------------------------------------------------------------
// | Tips: https://img.youloge.com/wallet/micateam!0 
// +----------------------------------------------------------------------
// | Website: www.youloge.com
// +----------------------------------------------------------------------
// | Author:  <11247005@qq.com>  Date: 2025/03/16
// +----------------------------------------------------------------------
/**
 * 扩展方法 
 * 用于兼容 (PHP 8 <= 8.1.0) 7.2+
 * 判断是否可循环数组
 */
if (!function_exists('array_is_list')) {
    function array_is_list($arg)
    {
        return $arg === [] || (array_keys($arg) === range(0, count($arg) - 1));
    }
}

/**
 * 验证和处理表单数据
 *
 * 可以通过多个调用方式 实现复杂处理
 *
 * @param object $params 表单数据
 * @return object $rules 验证规则
 * @return bool $intersect 是否只返回验证通过的数据
 * @return array $result 验证结果
 * @throws Exception 验证失败抛出异常 ['err'=>400,'msg'=>'错误提示']
 * @example
 */

if (!function_exists('useValidate')) {
    function useValidate($params, $rules, $intersect = true)
    {
        $presets = [
            // 基本处理
            'require' => function ($field, $param, $args, $message = '%s 字段不能为空') {
                // 1. 检查参数是否存在（未提交或值为 null 则视为缺失）
                if (!isset($param) || $param === null) {
                    throw new Exception(sprintf($message, $field));
                }
                // 2. 处理字符串类型：排除纯空格（如 '   ' 应视为空）
                if (is_string($param) && trim($param) === '') {
                    throw new Exception(sprintf($message, $field));
                }
                // 3. 其他情况（如 0、'0'、false、数组等）视为有效
                return $param;
            },
            'required' => function ($field, $param, $args, $message = '%s 字段不能为空') {
                // 1. 检查参数是否存在（未提交或值为 null 则视为缺失）
                if (!isset($param) || $param === null) {
                    throw new Exception(sprintf($message, $field));
                }
                // 2. 处理字符串类型：排除纯空格（如 '   ' 应视为空）
                if (is_string($param) && trim($param) === '') {
                    throw new Exception(sprintf($message, $field));
                }
                // 3. 其他情况（如 0、'0'、false、数组等）视为有效
                return $param;
            },
            'int' => function ($field, $param, $args, $msg = '') {
                return (int) ($param ?? $args);
            },
            'bool' => function ($field, $param, $args, $msg = '') {
                return (bool) ($param ?? $args);
            },
            'float' => function ($field, $param, $args, $msg = '') {
                return (float) ($param ?? $args);
            },
            'string' => function ($field, $param, $args, $msg = '') {
                return (string) ($param ?? $args);
            },
            'array' => function ($field, $param, $args, $msg = '') {
                return (array) ($param ?? $args);
            },
            'object' => function ($field, $param, $args, $msg = '') {
                return (object) ($param ?? $args);
            },
            'sprintf' => function ($field, $param, $args = '', $msg = '') {
                return sprintf($args, $param);
            },
            'format' => function ($field, $param, $args = '', $msg = '') {
                return sprintf($args, $param);
            },
            // 常用处理
            'xss' => function ($field, $param, $args, $msg = '') {
                $replace = str_replace(["'", '"', ';', '--', '%', '_', '(', ')'], '', $param);
                return strip_tags($replace, $args);
            },
            'html' => function ($field, $param, $args, $msg = '') {
                return htmlspecialchars($param, $args ?? (ENT_COMPAT | ENT_HTML401));
            },
            'join' => function ($field, $param, $args, $msg = '') {
                return implode($args ?? ',', $param);
            },
            'trim' => function ($field, $param, $args, $msg = '') {
                return trim((string) $param) ?? $args;
            },
            'upper' => function ($field, $param, $args, $msg = '') {
                return strtoupper($param) ?? $args;
            },
            'lower' => function ($field, $param, $args, $msg = '') {
                return strtolower($param) ?? $args;
            },
            // 常用验证
            'email' => function ($field, $param, $args, $msg = '%s 字段值必须是邮箱') {
                if (filter_var($param, FILTER_VALIDATE_EMAIL)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field));
            },
            'mobile' => function ($field, $param, $args, $msg = '%s 字段值必须是手机号') {
                $options = [
                    'options' => [
                        'regexp' => "/^1[3456789]\d{9}$/"
                    ]
                ];
                if (filter_var($param, FILTER_VALIDATE_REGEXP, $options)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field));
            },
            'url' => function ($field, $param, $args, $msg = '%s 字段值必须是网址') {
                if (filter_var($param, FILTER_VALIDATE_URL)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field));
            },
            'ip' => function ($field, $param, $args, $msg = '%s 字段值必须是IP地址') {
                if (filter_var($param, FILTER_VALIDATE_IP)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field));
            },
            'date' => function ($field, $param, $args, $msg = '%s 字段值必须是%s日期格式') {
                $format = $args ?? 'Y-m-d H:i:s';
                $dateTime = DateTime::createFromFormat($format, $param);
                if ($dateTime && !$dateTime->getLastErrors()['warning_count']) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $format));
            },
            'time' => function ($field, $param, $args, $msg = '%s 字段值必须是时间戳') {
                if (!is_numeric($param) || intval($param) != $param) {
                    throw new Exception(sprintf($msg, $field));
                }
                $minTimestamp = strtotime('1970-01-01'); // 0
                $maxTimestamp = strtotime('2099-01-01'); // 4070908800000 32位是2038-01-19
                if ($param <= $minTimestamp || $param >= $maxTimestamp) {
                    throw new Exception(sprintf($msg, $field));
                }
                return $param;
            },
            'idcard' => function ($field, $param, $args, $msg = '%s 字段值必须是身份证号 %s') {
                if (strlen($param) !== 18) {
                    throw new Exception(sprintf($msg, $field, '长度不足'));
                }
                if (preg_match('/^\d{17}[\dXx]$/', $param) == false) {
                    throw new Exception(sprintf($msg, $field, '格式错误'));
                }
                // 加权因子
                $weightFactors = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                // 校验码映射
                $checkCodes = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
                // 计算校验码
                $sum = 0;
                for ($i = 0; $i < 17; $i++) {
                    $sum += intval($param[$i]) * $weightFactors[$i];
                }
                $mod = $sum % 11;
                $checkCode = $checkCodes[$mod];
                if (strtoupper($param[17]) !== $checkCode) {
                    throw new Exception(sprintf($msg, $field, '校验错误'));
                }
                return $param;
            },
            'regex' => function ($field, $param, $args, $msg = '%s 字段值格式错误') {
                if (preg_match($args, $param) === false) {
                    throw new Exception(sprintf($msg, $field));
                }
                return $param;
            },
            'test' => function ($field, $param, $args, $msg = '%s 字段值格式错误') {
                if (preg_match($args, $param) === false) {
                    throw new Exception(sprintf($msg, $field));
                }
                return $param;
            },
            //数字相关
            'min' => function ($field, $param, $args, $msg = '%s 字段数字不能小与%s') {
                $min = min(explode(',', $args));
                if (is_numeric($param) && $param >= $min) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $min));
            },
            'max' => function ($field, $param, $args, $msg = '%s 字段数字不能大于%s') {
                $max = max(explode(',', $args));
                if (is_numeric($param) && ($param <= $max)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $max));
            },
            'between' => function ($field, $param, $args, $msg = '%s 字段数字必须在%s和%s之间') {
                $conf = explode(',', $args);
                $min = min($conf);
                $max = max($conf);
                if (is_numeric($param) && $param >= $min && $param <= $max) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $min, $max));
            },
            // 字符串相关
            'start' => function ($field, $param, $args, $msg = '%s 字段值必须以%s开头') {
                // throw new Exception((string)$param);
                if (str_starts_with($param, $args)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $args));
            },
            'end' => function ($field, $param, $args, $msg = '%s 字段值必须以%s结尾') {
                if (str_ends_with($param, $args)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $args));
            },
            'digit' => function ($field, $param, $args, $msg = '%s 字段值必须是数字') {
                if (ctype_digit($param)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field));
            },
            'alpha' => function ($field, $param, $args, $msg = '%s 字段值必须是字母') {
                if (ctype_alpha($param)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field));
            },
            'alphanum' => function ($field, $param, $args, $msg = '%s 字段值必须是字母和数字') {
                if (ctype_alnum($param)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field));
            },
            'length' => function ($field, $param, $args, $msg = '%s 字段长度必须%s~%s个字符') {
                $conf = explode(',', $args);
                $min = min($conf);
                $max = max($conf);
                $len = mb_strlen($param);
                if ($len >= $min && $len <= $max) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $min, $max));
            },
            'len' => function ($field, $param, $args, $msg = '%s 字段长度必须%s~%s个字符') {
                $conf = explode(',', $args);
                $min = min($conf);
                $max = max($conf);
                $len = mb_strlen($param);
                if ($len >= $min && $len <= $max) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $min, $max));
            },
            'in' => function ($field, $param, $args, $msg = '%s 字段值必须在%s范围中') {
                $conf = explode(',', $args);
                if (in_array($param, $conf)) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $args));
            },
            'not' => function ($field, $param, $args, $msg = '%s 字段值不能在%s范围中') {
                $conf = explode(',', $args);
                if (in_array($param, $conf) == false) {
                    return $param;
                }
                throw new Exception(sprintf($msg, $field, $args));
            },
            'count' => function ($field, $param, $args, $message = '%s 字段值数量必须%s~%s个') {
                $conf = explode(',', $args);
                $min = min($conf);
                $max = max($conf);

                if (is_array($param) && count($param) >= $min && count($param) <= $max) {
                    return $param;
                }
                throw new Exception(sprintf($message, $field, $min, $max));
            }
        ];
        // 核心：递归处理规则（支持数据流转）
        $processRule = function ($field, $value, $rule) use (&$processRule, $presets) {
            // 1. 字符串规则（单规则/多规则组合，支持数据流转）
            if (is_string($rule)) {
                @[$lambda, $customMsg] = explode('#', $rule . '#', 2);
                $lambdas = explode('|', $lambda);

                // 关键：遍历多规则时，前一个规则的处理结果作为后一个的输入
                foreach ($lambdas as $_lambda) {
                    @[$ruleName, $ruleArgs] = explode(':', $_lambda, 2);
                    $method = $presets[$ruleName] ?? null;

                    if (!$method)
                        continue;

                    try {
                        $value = $method($field, $value, $ruleArgs);
                    } catch (Exception $e) {
                        // 自定义错误信息优先
                        throw new Exception($e->getMessage());
                    }
                }
                return $value;
            }

            // 2. 闭包规则（自定义预处理，结果流转）
            if (is_callable($rule)) {
                try {
                    // 闭包返回的预处理结果，作为后续规则的输入
                    return $rule($field, $value);
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }

            // 3. 索引数组 = 流水线规则（多步骤流转）
            if (array_is_list($rule)) {
                // 3.1 一维数组规则：单元素索引数组 ['rule'] → 遍历每个元素校验
                if (count($rule) === 1) {
                    if (!is_array($value)) {
                        throw new Exception("{$field}：必须是一维数组（需遍历每个元素校验）");
                    }
                    $result = [];
                    foreach ($value as $index => $item) {
                        $itemPath = "{$field}[{$index}]";
                        $result[$index] = $processRule($itemPath, $item, $rule[0]);
                    }
                    return $result;
                }
                // 3.2 流水线规则：多元素索引数组 [rule1, rule2] → 步骤流转处理
                if (count($rule) > 1) {
                    $currentValue = $value;
                    foreach ($rule as $stepIdx => $step) {
                        $stepPath = "{$field}";
                        $currentValue = $processRule($stepPath, $currentValue, $step);
                    }
                    return $currentValue;
                }
            }

            // 4. 关联数组 = 单个对象规则（子字段数据流转）
            if (is_array($rule) && !array_is_list($rule)) {
                if (!is_array($value))
                    $value = [];
                $result = [];
                foreach ($rule as $key => $subRule) {
                    $subPath = "{$field}.{$key}";
                    $result[$key] = $processRule($subPath, $value[$key] ?? null, $subRule);
                }
                return $result;
            }

            throw new Exception("{$field} 不支持的规则类型");
        };

        // try-catch 统一处理异常
        try {
            foreach ($rules as $field => $rule) {
                // 核心：当前字段的处理结果
                $params[$field] = $processRule(
                    $field,
                    $params[$field] ?? null,
                    $rule,
                );
            }
            // 验证成功：返回交集/全部数据（基于最终处理后的参数）
            return $intersect
                ? array_intersect_key($params, $rules)
                : $params;
        } catch (Exception $e) {
            // 统一返回错误格式
            return ['err' => 400, 'msg' => $e->getMessage()];
        }
    }
}
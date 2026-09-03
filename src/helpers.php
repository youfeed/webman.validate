<?php
// +----------------------------------------------------------------------
// | Tips: https://img.youloge.com/wallet/micateam!0 
// +----------------------------------------------------------------------
// | Website: www.youloge.com
// +----------------------------------------------------------------------
// | Author:  <11247005@qq.com>  Date: 2025/03/16
// +----------------------------------------------------------------------
if (!function_exists('array_is_list')) {
    /**
     * 扩展方法 
     * 用于兼容 (PHP 8 <= 8.1.0) 7.2+
     * 判断是否可循环数组
     */
    function array_is_list($arg)
    {
        return $arg === [] || (array_keys($arg) === range(0, count($arg) - 1));
    }
}


if (!function_exists('useValidate')) {
    /**
     * 验证和处理表单数据
     *
     * 可以通过多个调用方式 实现复杂处理
     *
     * @param array $params 表单数据
     * @param array $rules 验证规则
     * @param bool $intersect=true 是否只返回验证通过的数据
     * @return array $result 验证结果
     * @throws Exception 验证失败抛出异常 ['err'=>400,'msg'=>'错误提示']
     * @example
     */
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
                return (int)(($param === null || $param === '') ? $args : $param);
            },
            'bool' => function ($field, $param, $args, $msg = '') {
                return (bool)(($param === null || $param === '') ? $args : $param);
            },
            'float' => function ($field, $param, $args, $msg = '') {
                return (float)(($param === null || $param === '') ? $args : $param);
            },
            'string' => function ($field, $param, $args, $msg = '') {
                return (string)(($param === null || $param === '') ? $args : $param);
            },
            'array' => function ($field, $param, $args, $msg = '') {
                return (array)(($param === null || $param === '' || empty($param)) ? json_decode("[$args]",true) : $param);
            },
            'object' => function ($field, $param, $args, $msg = '') {
                return (object)(($param === null || $param === '' || empty($param)) ? json_decode("{{$args}}",false) : $param);
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
        // 递归处理
        try {
            foreach ($rules as $field => $rule) {
                // 初始化当前字段值
                @[$field=>$param] = $params;
                // 1. 处理可迭代规则（数组类型规则）
                if(is_iterable($rule)){
                    // 1.1 索引数组：流水线/数组元素遍历
                    if(array_is_list($rule)){
                        if(count($rule) == 1){
                            if (!is_array($param)) {
                                throw new Exception("{$field}：必须是一维数组");
                            }
                            // 值遍历
                            foreach ($params[$field] as $index => $paraming) {
                                @['err'=>$err,'msg'=>$msg,$index=>$callback] = $back = useValidate([$index => $paraming],[$index => $rule[0]],$intersect);
                                if($err === 400){ throw new Exception("$msg"); }
                                $params[$field][$index] = $callback;
                            }
                        // 流水线规则遍历
                        }else{
                            foreach($rule as $_rule){
                                @[$field=>$current] = $params;
                                @['err'=>$err,'msg'=>$msg,$field=>$callback] = $back = useValidate([$field=>$current],[$field=>$_rule],$intersect);
                                if($err === 400){ throw new Exception("$field.$msg"); }
                                $params[$field] = $callback;
                            }
                        }

                    // 1.2 关联数组：单个对象规则（子字段数据流转）
                    }else{
                        @['err'=>$err,'msg'=>$msg] = $callback = useValidate($param, $rule, $intersect);
                        if($err === 400){ throw new Exception("$field.$msg"); }
                        $params[$field] = $callback;
                    }
                    continue;
                }
                // 闭包回调规则
                if(is_callable($rule)){
                    try {
                    // 闭包返回的预处理结果，作为后续规则的输入
                        $params[$field] = $rule($field, $param);
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage(),400);
                    }
                    continue;
                }
                // 2. 处理字符串规则（单规则/多规则组合） expression
                if(is_string($rule)){
                    @[$expression, $customMsg] = explode('#', $rule);
                    $pipeline = explode('|', $expression);
                    $required = str_contains($expression,'required') || str_contains($expression,'require');
                    foreach ($pipeline as $step) {
                        @[$ruleName, $ruleParam] = explode(':', $step, 2);
                        @[$ruleName=>$method] = $presets;
                        // 非必填且值为null，或规则不存在时跳过（支持默认值）
                        if(($param === null && $required === false) || $method === null){
                            // 类型转换规则的默认值处理
                            if(in_array($ruleName,['int','bool','float','string','array','object']) && !is_null($ruleParam)){
                                $param = $method($field,$param,$ruleParam); // 更新param值，确保后续规则能使用
                                $params[$field] = $param;
                            }
                            continue; 
                        }
                        // 执行验证规则
                        $args = [$field, $param, $ruleParam];
                        if($customMsg){
                            array_push($args, $customMsg);
                        }
                        $param = $method(...$args);
                        $params[$field] = $param;
                        // 更新param为最新值，支持规则流转（前一个规则结果作为后一个输入）
                    }
                }
            }
            return $intersect ? array_intersect_key($params,$rules) : $params;
        }catch (Exception $e) {
            return ['err' => 400, 'msg' => $e->getMessage()];
        }
    }
}

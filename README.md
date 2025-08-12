# Youloge.validate Webman 表单验证器(PHP7.2+)

![Brightgreen](https://img.shields.io/badge/@-micateam-brightgreen.svg) ![Packagist](https://img.shields.io/packagist/v/youloge/webman.validate) ![Languages](https://img.shields.io/github/languages/top/youfeed/webman.validate.svg) ![Packagist Downloads](https://img.shields.io/packagist/dt/youloge/webman.validate) ![License ](https://img.shields.io/packagist/l/youloge/webman.validate)

> Webman 表单验证器是：[Github Youloge.Tool](https://github.com/youfeed/webman.tool) 的子函数之一，如果安装了`webman.tool`就不需要再安装`webman.validate`了。经过`webman.validate` 过滤处理后的表单，基本可以达到入库要求，可以大大简化逻辑处理。

- 支持数据预处理链式处理
- 支持`对象`和`数组`数据验证
- 支持过滤，仅保留指定字段
- 支持自定义错误提示
- 支持`字段过滤`，仅返回指定验证字段
- 对上次验证器处理结果进行再次过滤，可以实现复杂表单处理

### 项目地址

[Youloge.Validate](https://github.com/youfeed/webman.validate) Star 我 `如果对你的项目有帮助` 欢迎打赏~

- 1.3.0 [2025-08-12] 默认数据类型与默认值：数据类型修复为一致
- 1.3.0 [2025-04-23] 添加`array_is_list`函数，兼容到 php7.2+
- 1.2.8 [2025-03-20] 基本数据类型`int:100`,`float:1.02`,`bool:false`,`string:默认值` 提供默认值支持

### 安装使用

> `composer require youloge/webman.validate`

> 函数名称 `useValidate()`

### 使用说明

验证规则: `|`,`:`,`,`,`#`

- `|` 分割多个规则
- `:` 规则参数 `,`多个参数用逗号分隔
- `#` 自定义错误提示
- $return = useValidate($params,$rules,$filter=true)
- `$params` 验证数据
- `$rules` 验证规则
- `$filter` 是否过滤数据,剔除规则之外的数据在返回 默认 true
- 验证失败返回 `['err'=>400,'msg'=>'错误提示']`
- 验证成功返回 处理过后的数据`(filter)$params`

### 参数说明

| 验证规则 |                   说明                   |        示例        | 规则类型 |
| :------- | :--------------------------------------: | :----------------: | -------: |
| required |                 字段必填                 |      required      | 基本处理 |
| int      |              转为 int 类型               |       int:10       | 基本处理 |
| bool     |              转为 bool 类型              |     bool:false     | 基本处理 |
| float    |             转为 float 类型              |     float:1.2      | 基本处理 |
| string   |             转为 string 类型             |     int:string     | 基本处理 |
| join     | 数组转字符串：可以指定连接符号，默认逗号 |       join:@       | 基本处理 |
| trim     |            去除参数两端空白符            |        trim        | 基本处理 |
| xss      |   过滤特殊符号:支持不过滤(<b></b>标签)   |      xss:<b>       | 基本处理 |
| html     |              转义 HTML 标签              |        html        | 基本处理 |
| ---      |                   ---                    |        ---         |      --- |
| email    |              验证是否为邮箱              |       email        | 常用验证 |
| mobile   |           验证是否手机号(国内)           |       mobile       | 常用验证 |
| url      |          验证是否 URL 协议网址           |        url         | 常用验证 |
| ip       |             验证是否 IP 地址             |         ip         | 常用验证 |
| date     |                 验证日期                 |  date:Y-m-D H:i:s  | 常用验证 |
| idcard   |           验证是否身份证(国内)           |       idcard       | 常用验证 |
| regex    |                 正则验证                 | regex:[a-zA-Z]{16} | 常用验证 |
| test     |                 正则验证                 | regex:[a-zA-Z]{16} | 常用验证 |
| ---      |                   ---                    |        ---         |      --- |
| min      |                参数最小值                |       min:1        | 数字相关 |
| max      |                参数最大值                |       max:20       | 数字相关 |
| between  |               参数取值范围               |   between:1,100    | 数字相关 |
| ---      |                   ---                    |        ---         |      --- |
| start    |             参数必须以%开头              |      start:@       | 字符相关 |
| end      |             参数必须以%结尾              |    end:@qq.com     | 字符相关 |
| digit    |             参数必须为纯数字             |       digit        | 字符相关 |
| alpha    |             参数必须为纯字母             |       alpha        | 字符相关 |
| alphanum |           参数必须为数字和数字           |      alphanum      | 字符相关 |
| length   |     参数字符长度要在 min ～ max 之间     |    length:12,14    | 字符相关 |
| len      |     参数字符长度要在 min ～ max 之间     |    length:12,14    | 字符相关 |
| ---      |                   ---                    |        ---         |      --- |
| in       |              参数取值在之内              |   between:a,b,c    | 取值相关 |
| not      |              参数取值在之外              |   between:x,y,z    | 取值相关 |
| count    |       数组个数要在 min ～ max 之间       |     count:1,6      | 取值相关 |

> xss 过滤不仅会过滤 html 标签，还会过滤 `'`,`"`,`;`,`--`,`%`,`_`,`(`,`)`, 可以防止`Mysql 注入`

---

- =============================
- = 过滤规则分为`预处理`和`验证规则`
- =============================
- = `基本处理:` required int bool float string join trim upper lower xss html
- = `常用验证:` email mobile url ip date time idcard regex test
- = `数字相关:` min max between
- = `字符相关:` start end digit alpha alphanum length
- = `取值相关:` in not count
- =============================

---

### 简单示例：

```php
$params = [
     'username' => 'admin',
     'password' => '123456',
];
$rules =[
     'username' => 'required|trim|lower|email', // 必填，去除空格，转小写，邮箱格式
     'password' => 'required|length:6,20', // 必填，长度6-20
];

@['err'=>$err,'msg'=>$msg] = $data = useValidator($params,$rules,$filter=true);
if($err === 400){
     return json(['err'=>400,'msg'=>$msg]);
}

```

### 复杂示例：

##### 请求数据：

```json
{
  "title": "文章标题",
  "content": "文章内容",
  "type": 3,
  "status": "00000001",
  "share": 1,
  "price": "a12.34",
  "created": "2022-01-01 12:00:00",
  "tags": ["标签1", "标签2"],
  "info": {
    "origin": "https://www.youloge.com",
    "ip": "127.0.0.1"
  },
  "list": [
    {
      "name": "张三",
      "age": 18,
      "mail": "EMAIL100@qq.com"
    },
    {
      "name": "李四",
      "age": 20
    },
    {
      "name": "李四",
      "age": 20
    }
  ]
}
```

#### 验证规则：

```php
$rules =[
     // length:64,6,30 (不是书写错误 不区分变量数量 不区分变量前后)
     'title' => 'required|xss|length:64,6,30', // 必填，长度6-64 去除html去除 HTML 和 PHP 标签
     'content' => 'required|html', // 必填，转义html标签
     'type' =>'required|in:1,2,3', // 必填，只能是1,2,3,不能是其他值
     'status' => 'required|int|not:0,99,100', // 必填，并转换成整数，不能是0,99,100
     'share' => 'required|bool', // 必填，并转换成布尔值
     'price' => 'required|floot|max:100', // 必填，并转换成浮点数 最大100
     'created' => 'required|date:Y-m-d H:i', // 必填，日期格式(默认为：Y-m-d H:i:s)
     'tags' => 'required|count:1,6', // 必填，需要1~6个标签 [`相同键 只生效最后一个哦`]
     // 'tags' =>['required|length:2,20'], //  数组的每个值需要2-20个字符 [`相同键 只生效最后一个哦`]
     // 'tags' =>'required|join:-', // 使用-符号连接 [`相同键 只生效最后一个哦`]
     // 注意这个规则是数组-对象，所以下面的规则会循环验证数组内的对象
     'list' => [
          [
               'name'=>'required|string', // 必填，并转换成字符串
               'age'=>'required|int', // 必填，并转换成整数
               'mail'=>'required|end:@qq.com', // 必填，必须以@qq.com结尾
          ]
     ],
     // 这个规则是对象，所以下面的规则会验证对象
     'info' => [
          'origin'=>'required|url', // 必填，需要为url格式
          'ip'=>'required|ip', // 必填，需要为ip格式
     ]
];
$params = $request->all();
@['err'=>$err,'msg'=>$msg] = $data = useValidator($params,$rules,$filter=true);
if($err === 400){
     return json(['err'=>400,'msg'=>$msg]);
}

```

### 特殊规则

> 表单数组验证：要实现比如一个`label`数组字段, 要求

- 数组需要`1~6个标签`
- 且每个标签需要`至少2-20个字符`
- 最终使用`-`符号连接，你需要这么写

```php
$rule_one = ['label'=>'required|count:1,6']; // 必填，需要1~6个标签
$rule_two = ['label'=>['length:2,20']]; // 数组的每个值需要2-20个字符
$rule_three = ['label'=>'join:-']; // 使用-符号连接

@['err'=>$err,'msg'=>$msg] = $data_one = useValidator($params,$rule_one,true);
if($err === 400){ return json(['err'=>400,'msg'=>$msg]); }
// 用第一步的结果 作为第二步的验证规则 第三个参数为false 不剔除规则外字段
@['err'=>$err,'msg'=>$msg] = $data_two = useValidator($data_one,$rule_two,false);
if($err === 400){ return json(['err'=>400,'msg'=>$msg]); }
// 用第二步的结果 作为第三步的验证规则 第三个参数为false 不剔除规则外字段
@['err'=>$err,'msg'=>$msg] = $data_three = useValidator($data_two,$rule_three,false);
if($err === 400){ return json(['err'=>400,'msg'=>$msg]); }

```

---

#### 规则的配置是和输入表单 是对应的关系，验证器会`预处理数据`并交给`后续过滤规则`，所以碰到需要处理`同一个键`同时进行`修改+验证的情况`,进行多次拆分即可。

---

![wallet.micateam](https://img.youloge.com/wallet/micateam!0)

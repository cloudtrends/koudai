<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2014/12/25
 * Time: 14:16
 */

namespace common\exceptions;


class PayException extends UserExceptionExt
{
    public static $ERROR_MSG = [
        /* 支付异常错误码 */
        "2000" => "绑卡跳转SDK",       // 连连支付 - 支付0.01绑卡
        "2001" => "支付需要验证码",    // 联动支付
        "2002" => "充值支付跳转SDK",       // 连连支付 - 支付金额投资
        "2003" => "预留支付",  // 连连支付 - 只能使用余额支付
        "2004" => "预留支付",
        "2005" => "预留支付",
        "2006" => "预留支付",
        "2007" => "预留支付",
        "2008" => "预留支付",
        "2009" => "预留支付",

        "2100" => "暂时不支持该银行",
        "2101" => "不支持的第三方支付",
        "2102" => "用户参数不合法",
        "2103" => "请提供订单号",
        "2104" => "返回参数无法解析",

        // 连连支付相关
        "2200" => "订单时间格式错误",
        "2201" => "商户号不正确",
        "2202" => "支付成功，但找不到对应记录",
        "2203" => "支付失败",
        "2204" => "支付成功，但找不到对应用户",
        "2205" => "您还没有绑定银行卡",
        "2206" => "您银行卡暂不支持充值",
        "2207" => "申请充值失败，请稍后重试",
        "2208" => "只能使用余额支付，请先充值",
        "2220" => "不存在的充值订单号",
        "2221" => "请输入正确的金额",
        "2222" => "金额过大",
        "2223" => "充值成功，但更新用户账户失败",
        "2224" => "充值尚未成功，请耐心等待",
        "2225" => "证书配置错误",

        // 联动支付相关
        "2321" => "请输入正确的金额",
        "2322" => "金额过大",
        "2323" => "请求支付失败，请稍后重试",
        "2324" => "订单ID必须4-16位",

    ];
} 
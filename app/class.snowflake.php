<?php

/**
 *  分布式 id 生成类     组成: <毫秒级时间戳+机器id+序列号>
 *  默认情况下41bit的时间戳可以支持该算法使用到2082年，10bit的工作机器id可以支持1023台机器，序列号支持1毫秒产生4095个自增序列id
 *  @author zhangqi
 *  @link https://www.cnblogs.com/njccqx/p/13402169.html
 */
class SnowFlake
{
    const EPOCH = 1479533469598;    //开始时间,固定一个小于当前时间的毫秒数
    const max12bit = 4095;
    const max41bit = 1099511627775;

    static $machineId = 1;      // 机器id

    public static function machineId($mId = 0)
    {
        self::$machineId = $mId;
    }

    public static function createOnlyId()
    {
        // 时间戳 42字节
        $time = floor(microtime(true) * 1000);
        // 当前时间 与 开始时间 差值
        $time -= self::EPOCH;
        // 二进制的 毫秒级时间戳
        $base = decbin(self::max41bit + $time);
        // 机器id  10 字节
        if (!self::$machineId) {
            $machineid = self::$machineId;
        } else {
            $machineid = str_pad(decbin(self::$machineId), 10, "0", STR_PAD_LEFT);
        }
        // 序列数 12字节
        $random = str_pad(decbin(mt_rand(0, self::max12bit)), 12, "0", STR_PAD_LEFT);
        // 拼接
        $base = $base . $machineid . $random;
        // 转化为 十进制 返回
        return bindec($base);
    }
}
// SnowFlake::machineId("1"); //机器编号
// echo SnowFlake::createOnlyId();//分布式id

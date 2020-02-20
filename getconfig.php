<?php

/**
 * @class getconfig
 *
 * 这是一个静态类
 * 用来获取配置文件
 *
 * */
class getconfig{

    /**
     * @private static (string)$filename
     *
     * 这个字符串存放用户列表文件路径
     * */
    private static $filename = "./config.json";
    private static $userList = [];

    /**
     * @param (string)$url
     * @return bool|mixed
     *
     * 这个函数用来读取json文件
     * 并将json转为数组
     */
    private static function readJson($url){

        // 文件读取
        $json = file_get_contents($url);
        if (!$json) return false;

        // json解析
        $arr = json_decode($json, true);
        if (!$arr) return false;
        return $arr;
    }

    /**
     * @param (dxyx|bjmf)$type
     * @return array|bool
     *
     * 获取指定平台的用户列表信息
     */
    public static function user($type){

        // 读取全部用户列表
        self::$userList = self::readJson(self::$filename);
        if (!self::$userList || count(self::$userList) == 0) return false;

        $res = [];
        for ($i = 0; $i < count(self::$userList); $i++){

            if (!self::$userList[$i]["open"]) continue;

            if (self::$userList[$i]["mod"] != $type) continue;

            if (!self::$userList[$i]["url"]) continue;

            $userConfig = self::readJson(self::$userList[$i]["url"]);
            if (!$userConfig) continue;

            array_push($res, $userConfig);
        }

        return $res;
    }
}
<?php
    /**
     * 该代码仅供学习交流，请合理使用，出现任何纠纷与作者无关
     * 疫情期间：请如实报告自己的身体情况
     */
    ini_set('date.timezone','Asia/Shanghai');
    require("email.php");
    require("getconfig.php");

    /**
     * @desc 获取班级魔方用户列表
    **/
    $data = getconfig::user("bjmf")[0];
    for ($i = 0; $i < count($data); $i++) {

        $e = $data[$i];

        $phone = $e["phone"];       //登录手机号
        $pwd = $e["pwd"];           //登录密码
        $suburl = $e["suburl"];     //重点：点击自己班级->健康汇报->每日健康情况，然后复制当前页的浏览器地址url

        $temp = getTemp(36.3, 37.1);//体温 36.3~37.2
        $sig = $e["sig"];           //表现症状
        $isTri = $e["isTri"];       //就医情况
        $isSpl = $e["isSpl"];       //隔离情况
        $reTou = $e["reTou"];       //最新接触
        $site = $e["site"];         //地址


        //是否需要邮件提示
        $isEmail = $e["isEmail"];      //开启邮件提示
        $smtpServer = $e["smtpServer"];//发送者：smtp服务器地址
        $smtpPort = $e["smtpPort"];    //发送者：端口号
        $email = $e["email"];          //发送者：email账号
        $password = $e["password"];    //发送者：email密码(qq邮箱需要授权码)
        $name = $e["name"];            //发送者：名称
        $reName = $e["reName"];        //接收者：名称
        $reEmail = $e["reEmail"];      //接收者：email 可以填发送者email，相当于自己给自己发邮件
        $title = "健康日报自动填写完成(" . date('m-d') . ")";     //邮件标题

        //开始自动提交
        auto($phone, $pwd, $suburl, $temp, $sig, $isTri, $isSpl, $reTou, $site, $isEmail, $smtpServer, $smtpPort, $email, $password, $name, $reName, $reEmail, $title);
    }
    exit();



    /**
     * 获取随机正常体温
     */
    function getTemp($min, $max){
        return sprintf("%.1f", $min + mt_rand() / mt_getrandmax() * ($max - $min));
    }

    /**
     * 自动提交脚本
     */
    function auto($phone, $pwd, $suburl, $temp, $sig, $isTri, $isSpl, $reTou, $site, $isEmail, $smtpServer, $smtpPort, $email, $password, $name, $reName, $reEmail, $title){
        //获取cookie和token
        $curl =curl_init("http://banjimofang.com/student/login");
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl,CURLOPT_HEADER,1);
        $result = curl_exec($curl);
        preg_match('/^Set-Cookie: (.*?);/m', $result, $m);
        preg_match_all('/name="_token".*?>/', $result, $getValue);
        $token = explode('"', $getValue[0][0])[3];
        $cookie_file = $m[1];
        curl_close($curl);

        //开始登录
        $url = "http://banjimofang.com/student/login";
        $post = "_token=".$token."&username=".$phone."&password=".$pwd;
        $headers = array(
            "Accept-Encoding: gzip, deflate",
            "Accept-Language: zh-CN,zh;q=0.8",
            "Cache-Control: no-cache",
            "Content-Length: ".strlen($post),
            "Content-Type: application/x-www-form-urlencoded",
            "Host: banjimofang.com"
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
        $result = curl_exec($curl);
        //获取新cookie
        preg_match('/^Set-Cookie: (.*?);/m', $result, $m);
        $cookie_file = $m[1];

        //填写数据
        $post = "formdata[fn_1]=".$temp
                ."&formdata[fn_2]=".$sig
                ."&formdata[fn_3]=".$isTri
                ."&formdata[fn_4]=".$isSpl
                ."&formdata[fn_5]=".$reTou
                ."&formdata[fn_6]=".$site;
        curl_setopt($curl, CURLOPT_URL, $suburl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Content-Length: ' . strlen($post)));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_ACCEPT_ENCODING, "gzip,deflate");
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
        $result = curl_exec($curl);

        //修改数据
        // $post = "formdata[fn_1]=".$temp
        //         ."&formdata[fn_2]=".$sig
        //         ."&formdata[fn_3]=".$isTri
        //         ."&formdata[fn_4]=".$isSpl
        //         ."&formdata[fn_5]=".$reTou
        //         ."&formdata[fn_6]=".$site;
        // curl_setopt($curl, CURLOPT_URL, 'http://banjimofang.com/student/course/4914/profiles/29?id=106666');
        // curl_setopt($curl, CURLOPT_POST, true);
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Content-Length: ' . strlen($post)));
        // curl_setopt($curl, CURLOPT_ACCEPT_ENCODING, "gzip,deflate");
        // curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
        // $result = curl_exec($curl);

        //邮件发送
        if($isEmail && $result){
            $body = "<h2>今日提交记录</h2>"."<ol>"
                    ."<li>当天温度：".$temp."</li>"
                    ."<li>表现症状：".$sig."</li>"
                    ."<li>就医情况：".$isTri."</li>"
                    ."<li>隔离情况：".$isSpl."</li>"
                    ."<li>最新接触：".$reTou."</li>"
                    ."<li>当前位置：".$site."</li>"
                    ."</ol>"
                    ."<i>提交时间：".date('Y-m-d H:i:s')."</i>";
            sendEmail($isEmail, $smtpServer, $smtpPort, $email, $password, $name, $reName, $reEmail, $title, $body); 
        }

        curl_close($curl);
        return $result;
    }
    
?>

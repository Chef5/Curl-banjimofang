# 【班级魔方&大学印象】自动提交每日健康情况

## 一、声明

**开源项目！该代码仅供学习交流，请合理使用，出现任何纠纷与作者无关**

其他语言实现：

- node实现：[https://github.com/xinmos/checkDay](https://github.com/xinmos/checkDay)
- python实现：[https://github.com/xinmos/checkDay/blob/master/check.py](https://github.com/xinmos/checkDay/blob/master/check.py)
- iOS实现(快捷指令+自动化)：

## 二、使用方法

本项目集成了班级魔方和大学印象自动提交脚本，部署相同，按需使用即可

- **大学印象**(2020.02.19新增)：脚本为`dxever.php`

- **班级魔方**：脚本为`auto.php` ，使用前，你需要注册好班级魔方账号，并加入指定班级。

> 其中对班级魔方中“疫情基本情况”的表单没有实现，需要自己网站上手动填写，只需要提交一次即可。

**每天请如实填写自己的健康状况，共战疫情！**


### 2.1 运行环境

- PHP，需要开启curl扩展
- Linux  其他操作系统自己尝试，这里主要是设置自动任务

### 2.2 使用步骤

#### 2.2.1 获取脚本

clone或者上传到服务器（clone前请先安装git）

目录：/home/test/  (可以自己决定，这里仅演示说明)

```shell
cd /home && mkdir test && cd test
git clone git@github.com:Patrick-Jun/Curl-banjimofang.git
```

#### 2.2.2 配置参数

参数均为json文件，可配置多个用户，大学印象和班级魔方配置文件均在`user`目录下

- 大学印象：`user/dxever.json`
- 班级魔方：`user/bjmf.json`

**大学印象**
```shell
vim ./Curl-banjimofang/user/dxever.json
```
参数说明：
- `sno`:  登录学号
- `pwd`:  登录密码 初始密码为身份证后6位
- `curlocation`:  当前位置
- `goout`:  3日内是否有出行计划  1有 0无
- `hp`:  健康状况  0正常  1异常
- `ncp`:  当前是否有新冠肺炎症状  0否  1是
- `isncp`:  当前是否为疑似或确诊病例  0否  1确诊  2疑似
- `touchncp`:  15日内是否接触过ncp患者  0否  1是
- `hubei`:  15日内是否去过湖北  0否  1是
- `ps`:  备注
- `isEmail`:  是否启用邮件通知（启用需要配置邮箱，见最后）

**班级魔方**
```shell
vim ./Curl-banjimofang/user/bjmf.json
```
参数说明：
- `phone`:  登录手机号
- `pwd`:  登录密码
- `suburl`:  依次点击 自己班级->健康汇报->每日健康情况，然后复制当前页的浏览器地址url
- `sig`:  表现症状
- `isTri`:  就医情况
- `isSpl`:  隔离情况
- `reTou`:  最新接触
- `site`:  地址
- `isEmail`:  是否启用邮件通知（启用需要配置邮箱，见最后）

关于体温：会随机取36.3~37.2

> 有同学反馈：一开始使用的微信登录，找不到手机号和密码。 
> **解决** ：手机号是自己微信手机号，密码可以在 http://banjimofang.com/resetpwd/student 进行设置。


#### 2.2.3 创建自动任务

编辑任务，第一次使用会让选择编辑器，选择3，vim-basic

```shell
crontab -e
```

找的一行空白处，添加如下代码
> 其中 00 08 表示每天早上8点进行自动提交，前一个数表示分钟，后一个数表示时钟，可自己调整

**大学印象**
```c
00 08 * * * php -f /home/test/Curl-banjimofang/dxever.php
```

**班级魔方**
```c
00 08 * * * php -f /home/test/Curl-banjimofang/auto.php
```

### 2.3 开启邮件提醒

邮件提醒可以自行决定是否使用，默认不使用`isEmail=false`，那么以下参数设置无效

<img src="./imgs/email.png" alt="email" style="zoom:50%;" />

打开之前`user`目录下相应的配置文件，开启邮箱后，以下参数均需要填，否则会报错
参数说明：
- `smtpServer`:  发送者：smtp服务器地址
- `smtpPort`:  发送者：端口号
- `email`:  发送者：email账号
- `password`:  发送者：email密码(qq邮箱需要授权码)
- `name`:  发送者：名称
- `reName`:  接收者：名称
- `reEmail`:  接收者：email 可以填发送者email，相当于自己给自己发邮件

> 邮箱默认使用SSL，如果不需要，请修改email.php文件

## 三、感谢

📢特别感谢以下成员的贡献：

[@Mr-k-bear]( https://github.com/Mr-k-bear)

[@xinmos]( https://github.com/xinmos)

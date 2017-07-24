# custom-uptimebot
custom uptime-bot public page with php-proxy

uptimebot自定义域名使用https并自定义样式  

# 来源
本项目灵感和大部分代码改编自[lwl的自由天空 - 建立自己的服务状态页](https://blog.lwl12.com/read/create-own-services-status-page.html)一文。  
但是部分虚拟空间不支持使用反代（**纠正：现在说可以自动签发let's encrypt**，那就是可以增加自己的样式咯233），所以产生了直接使用php获取页面的想法。  

# 特性
- 可以随意自定义页面样式
- 页面样式和信息放在了 `Config` 文件夹下面，方便修改和部署
- 全部CSS/JS均针对国内环境进行优化
- 目前可以缓存数据（间隔为自用户访问起1分钟），防止反代多次刷新出现fail（502）的情况  
- 已经防止代理被滥用的情况（只允许访问你设定的PageId监控页)
- 隐藏 PageID （不知道算不算好处）

# 当前版本手册（V2.0)
- `Config/config.php` 请填写你的`dir`(缓存目录，必须可读写，必填),`pageId`(页面ID，必填),`clean_key`(缓存清理key，必填),`expire`(缓存过期时间，选填，默认60秒)
- 前端页面随意定制，部分信息可以在`Config/info.php`中修改，但是列表样式使用请查看上方lwl博客
- 缓存地址请记得要手动创建好，可读写，支持相对路径
- `public`目录下的`css`,`js`内`lib`文件夹是完整的库支持，如果需要本地离线运行请直接把所有文件放置根目录并更改`index.php`相对应的路径即可
- 如果频繁出现`500`的情况，建议清理缓存试试，API地址一般是`/api?clean={{your clean_key}}`


# V1.0手册【！过期，建议不要使用旧版！】
- 本项目已经优化前端样式（前端部分默认不需要按照lwl博客内更改PageID，直接在`api/index.php`内填写就好）
- 其他样式（例如title）请自行修改
- 具体样式布局请自行查看上方lwl博客
- api相关部署请查阅[这里](https://never.pet/2017/03/23/uptimebot%E8%87%AA%E5%AE%9A%E4%B9%89%E9%A1%B5%E9%9D%A2/)



# 测试
本项目已经在[Hostker](https://www.hostker.com)上通过测试，[NoticeBoard - demo](https://status.hfi.me)

# @TODO
- [x] 重构API
- [x] 清理js （打算使用bootcdn）
- [ ] 清理缓存
- [ ] 合并模板
- [ ] 全restful实现（.htaccess)【不是很会放一会】
- [x] 缓存数据并在即时抓取数据不可用时返回用户数据避免出错
- [ ] 提示缓存数据(前端不如就放过我？)


# License
MIT

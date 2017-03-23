# custom-uptimebot
custom uptime-bot public page with php-proxy

uptimebot自定义域名使用https并自定义样式  

# 来源
本项目灵感和大部分代码改编自[lwl的自由天空 - 建立自己的服务状态页](https://blog.lwl12.com/read/create-own-services-status-page.html)一文。  
但是部分虚拟空间不支持使用反代（**纠正：现在说可以自动签发let's encrypt**，那就是可以增加自己的样式咯233），所以产生了直接使用php获取页面的想法。  

# 特性
- 可以随意自定义页面样式
- 目前可以缓存数据（间隔为自用户访问起1分钟），防止反代多次刷新出现fail（502）的情况  

# 手册
- 具体样式布局请自行查看上方lwl博客
- api相关部署请查阅[这里](https://never.pet/2017/03/23/uptimebot%E8%87%AA%E5%AE%9A%E4%B9%89%E9%A1%B5%E9%9D%A2/)

# 测试
本项目已经在hostker上通过测试，[NoticeBoard - demo](https://status.hfi.me)

# @TODO
- 清理js （打算使用bootcdn）
- 合并模板
- 全restful实现（.htaccess)
- 缓存数据并在即时抓取数据不可用时返回用户数据避免出错（会有提示为缓存数据） 


# License
MIT

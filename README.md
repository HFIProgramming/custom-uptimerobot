# custom-uptimebot
custom uptime-bot public page with php proxy

uptimebot自定义域名使用https

# 来源
本项目灵感和大部分代码改编自[lwl的自由天空 - 建立自己的服务状态页](https://blog.lwl12.com/read/create-own-services-status-page.html)一文。
但是部分虚拟空间不支持使用反代（纠正：现在说可以自动签发let's encrypt，那就是可以增加自己的样式咯233），所以产生了直接使用php获取页面的想法

# 手册
具体布局请自行查看lwl博客

# 测试
本项目已经在hostker上通过测试，[NoticeBoard - demo](https://status.hfi.me)

# @TODO
- 清理js （打算使用bootcdn）
- 合并模板
- 全restful实现（.htaccess)

# License
MIT

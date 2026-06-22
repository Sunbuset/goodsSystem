# 基于微信小程序的网上商城系统

这是一个适合作为课程设计提交的完整商城示例，包含微信小程序端、PHP 接口、PHP 后台管理端和 MySQL 数据库脚本。

## 目录结构

- `miniprogram/`：微信小程序端源码
- `backend/`：PHP 接口与后台管理页面
- `database.sql`：数据库建表与初始化数据脚本

## 功能概览

- 小程序端：首页、商品列表、商品详情、购物车、订单列表、个人中心、登录页
- 后台端：管理员登录、商品管理、订单管理、用户管理
- 接口：登录、商品列表、商品详情、创建订单、订单列表、用户列表、订单状态更新

## 本地运行

### 1. 导入数据库
通过phpstudy_pro打开phpAdamin 登录数据库账号
将 `database.sql` 导入 MySQL，默认数据库名为 `goods_system`。

### 2. 配置 PHP 环境
在phpStudy_pro里左侧网站里的localhost管理里面打开根目录
<img width="664" height="511" alt="image" src="https://github.com/user-attachments/assets/48e9a711-1f7d-47c9-99b0-a505c40eb142" />


把 `backend/` 放到 `phpStudy` 或 `XAMPP` 的站点目录下(phpstudy_pro\WWW\backemd)，确保可以通过 `http://localhost/goodsSystem/backend/` 访问。 localhost换成ipv4

如果数据库账号密码不同，请修改 [backend/config/db.php](backend/config/db.php) 中的配置。

### 3. 配置小程序

使用微信开发者工具导入 `miniprogram/` 目录。

将 [miniprogram/app.js](miniprogram/app.js) 和 [miniprogram/utils/config.js](miniprogram/utils/config.js) 中的 `apiBase` 修改为你的本地 PHP 地址(也就是ipv4)。
如果不知道是多少 在命令提示符里输入ipconfig 查看ipv4

### 4. 后台登录

- 账号：admin
- 密码：123456

## 说明

该项目重点覆盖了课程设计常见知识点：全局配置、tabBar、列表渲染、条件渲染、事件绑定、网络请求、本地存储、授权登录、下拉刷新、上拉加载以及 PHP + MySQL 的 CRUD 交互。

## 结束语

本次课程设计围绕“基于微信小程序的网上商城系统”展开，完成了从需求分析、系统设计到功能实现与调试维护的完整开发流程。项目实现了小程序端与 PHP+MySQL 后端的联动，基本覆盖了商品浏览、购物车管理、订单处理、用户登录与后台管理等核心业务，达到了课程设计预期目标。

在开发过程中，我对前后端分离开发模式有了更清晰的理解，掌握了微信小程序页面开发、接口调用与数据绑定方法，也进一步熟悉了 PHP 接口编写和 MySQL 数据库设计思路。通过多轮联调与问题排查，我的工程化意识和独立解决问题能力也得到了提升。

同时，系统仍存在一定改进空间，例如安全性还可进一步增强（如更完善的权限控制、输入校验和密码加密）、后台功能还可继续细化（如更完整的筛选统计与日志管理）、用户体验也可持续优化（如更丰富的交互反馈与异常提示）。后续若继续迭代，可引入更规范的接口鉴权、自动化测试与运维监控机制，使系统在稳定性、安全性和可维护性方面进一步提升。

总体而言，本次设计不仅完成了一个可运行的商城系统原型，也让我在综合运用课程知识、组织项目结构和撰写技术文档方面获得了实质性的收获，对后续毕业设计和实际项目开发具有积极的参考价值。

## 参考文献

[1] 腾讯云开发者平台. 微信小程序官方文档[EB/OL]. https://developers.weixin.qq.com/miniprogram/dev/framework/, 2026-06-16.

[2] 腾讯云开发者平台. 微信开放能力 API 文档（登录、网络请求、存储等）[EB/OL]. https://developers.weixin.qq.com/miniprogram/dev/api/, 2026-06-16.

[3] PHP Documentation Group. PHP Manual[EB/OL]. https://www.php.net/docs.php, 2026-06-16.

[4] Oracle. MySQL 8.0 Reference Manual[EB/OL]. https://dev.mysql.com/doc/refman/8.0/en/, 2026-06-16.

[5] Flanagan D. JavaScript: The Definitive Guide[M]. 7th ed. Sebastopol: O'Reilly Media, 2020.

[6] Duckett J. HTML and CSS: Design and Build Websites[M]. Indianapolis: John Wiley & Sons, 2011.

[7] 廖雪峰. SQL教程[EB/OL]. https://www.liaoxuefeng.com/wiki/1177760294764384, 2026-06-16.

[8] 阮一峰. JavaScript 教程[EB/OL]. https://wangdoc.com/javascript/, 2026-06-16.
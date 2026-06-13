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

该项目重点覆盖了课程设计常见知识点：全局配置、tabBar、列表渲染、条件渲染、事件绑定、网络请求、本地存储、授权登录、下拉刷新、上拉加载以及 PHP + MySQL 的 CRUD 交互。# goodsSystem
none

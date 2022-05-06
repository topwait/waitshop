~~~
www  WEB部署目录(或者子目录)
├─app                   应用目录
│  ├─admin              后台应用
│  │  ├─controller      控制器目录
│  │  ├─logic           逻辑层目录
│  │  ├─validate        验证器目录
│  │  └─view            视图的目录
|  |
|  ├─api                接口应用
|  │  ├─controller      控制器目录
|  │  ├─logic           逻辑层目录
|  │  ├─service         服务层目录
|  │  └─validate        验证器目录
|  |
|  ├─common             公共层
|  │  ├─basics          基础类库
|  │  ├─command         定时任务
|  │  ├─enum            枚举类型
|  │  ├─http            中间件
|  │  ├─listener        钩子事件
|  │  ├─model           数据模型
|  │  ├─service         服务功能
|  │  └─utils           常用工具
│  │
│  ├─AppService.php      应用服务文件
│  ├─event.php           行为钩子文件
│  ├─provider.php        容器Provider
│  ├─Request.php         应用请求对象
│  ├─service.php         系统服务定义
│  └─ExceptionHandle.php 全局异常文件
│
├─config                应用配置目录
├─extend                扩展函数目录
├─route                 路由定义目录
├─runtime               运行时的目录
├─vendor                第三方库目录
├─public                WEB目录(对外访问目录)
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
|─.env                  环境变量配置(安装后自动生成)
|─.example.env          环境变量模板(仅用于参考)
|─.gitignore            Git配置文件
|─composer.json         包管理文件
|─composer.lock         包管理锁定
|─install.lock          安装锁定
├─LICENSE.txt           授权说明文件
|─think                 命令行入口文件
~~~
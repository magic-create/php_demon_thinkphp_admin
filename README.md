# PHP Demon Admin Library For ThinkPHP

## 服务说明

本服务仅支持ThinkPHP6+

正常来说是内部开发使用的，外部使用也可以（水平有限，请慎用，可能会有漏洞或者性能问题）

使用的Bootstrap4作为基础前端框架，jQuery版本为3.5

## 前置说明
1. 入口文件手动修改
   ```
    需要在入口文件(一般为index.php)的
    require __DIR__ . '/../vendor/autoload.php';
    上面加入
    require __DIR__ . '/../vendor/topthink/framework/src/helper.php';
    require __DIR__ . '/../vendor/illuminate/support/helpers.php';
    以此保证thinkphp自带的助手函数的优先级（因为加载了laravel的illuminate也会存在部分相同函数。
    另外需要注意，本库使用的collect为laravel版本而非thinkphp版本，使用方式类似，但是laravel方法更多，并且blade模板底层使用了部分thinkphp的collect不具备的方法）。
    ！！！加载后将直接影响全局collect函数！！！
   ```
2. 命令行入口文件手动修改
   ```
   需要在入口文件(一般为think)的
   require __DIR__ . '/vendor/autoload.php';
   上面加入
   require __DIR__ . '/vendor/topthink/framework/src/helper.php';
   require __DIR__ . '/vendor/illuminate/support/helpers.php';
   理由同上
   ```
3. 需要保持路由状态开启
   ```
   需要在配置文件(一般为config/app.php)的中调整with_route为true
   ```
4. 多应用模式下参数调整（指安装了topthink/think-multi-app）
   ```
   需要在配置文件(一般为config/app.php)的中为app_map增加内容如下
   'app_express' => true, // 如果不需要开启应用快速访问请设置为false
   'app_map' => [
       env('ADMIN.ADMIN_PATH', 'admin') => 'admin'
   ],
   ```

## 安装说明

> 1. 使用composer安装服务
> 2. 设置admin的数据库连接（默认是admin，单库则设置为mysql即可）以及相关database配置
> 3. 执行命令生成建表迁移文件
> 4. 执行迁移动作生成对应表和初始数据
> 5. 设置静态资源文件目录（不用写public）
> 6. 发布资源到对应目录，设置访问路径（默认是admin）
> 7. 设置静态资源CDN（默认没有，只读本地）
> 8. 大功告成
> 9. 可以在vendor/comingdemon/admin-thinkphp目录中查看源码用于参考或调试

1. composer require comingdemon/admin-thinkphp
2. edit.env ([ADMIN]ADMIN_CONNECTION) or add config/admin.php (edit connection, default : admin)
3. edit config/database.php : connections add {connection}
4. php think admin:table
5. edit.env ([ADMIN]ADMIN_STATIC) or add config/admin.php (edit static, default : /static/admin)
6. php think admin:publish --tag=admin-all (If only the asset is updated later, --tag=admin-asset)
7. edit.env ([ADMIN]ADMIN_PATH) or add config/admin.php (edit path, default : admin)
8. edit.env ([ADMIN]ADMIN_CDN) or add config/admin.php (edit cdn, default : )
9. browser url {address}/admin or {path}

## 进阶操作

1. config('admin.access') = env('ADMIN.ADMIN_ACCESS', false) //开启权限校验（需要数据库支持，默认账号：admin，默认密码：demon）
2. config('admin.authentication') = env('ADMIN.ADMIN_AUTHENTICATION') //自定义授权控制器
3. config('admin.badge') = env('ADMIN.ADMIN_BADGE') //自定义菜单标记统计的类
4. config('admin.session.\*') = env('ADMIN.ADMIN_SESSION_\*') //设置session配置（和laravel自带的session一致）
5. config('admin.element.\*') = env('ADMIN.ADMIN_ELEMENT_\*') //设置页面元素对应的视图

## 特殊申明

本库已发布至Composer，理论上只内部使用，如有问题请自行解决，概不提供服务

最终解释权归魔网天创信息科技:尘兵所属
# Environment

## Main

- php 7.2.1
- fpm
- cphalcon 3
- Nginx
- mysql/postgres

## Ext
- composer
- git
- redis/ssdb

# phalcon

## namespace
- 命名空间与路由(Router)的绑定
- 通过解析路由,映射对应目录,命名空间(namespace),控制器(controller),方法(action)

## dispatcher 
- 循环调度事件执行顺序
- dispatch:beforeDispatchLoop(event) &gt; dispatch:beforeExecuteRoute(event) &gt; beforeExecuteRoute(method);  

# Environment

## Main

1.php 7.2.1
2.fpm
3.cphalcon 3
4.Nginx
5.mysql/postgres

## Ext
1.composer
2.git
3.redis/ssdb

# phalcon

## namespace
1.命名空间与路由(Router)的绑定
2.通过解析路由,映射对应目录,命名空间(namespace),控制器(controller),方法(action)

## dispatcher 
1.循环调度事件执行顺序
2.dispatch:beforeDispatchLoop(event) &gt; dispatch:beforeExecuteRoute(event) &gt; beforeExecuteRoute(method);  

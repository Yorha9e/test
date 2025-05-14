# 使用官方的 PHP + Apache 镜像
FROM php:8.1-apache

# 启用 Apache 的 URL 重写模块
RUN a2enmod rewrite

# 安装 MySQL 扩展
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 设置工作目录
WORKDIR /var/www/html

# 复制 PHP 文件到容器的工作目录
COPY src/ /var/www/html/

# 设置 Apache 配置文件
COPY ./apache/default.conf /etc/apache2/sites-available/000-default.conf

# 曝露端口 80
EXPOSE 8080

# 启动 Apache
CMD ["apache2-foreground"]

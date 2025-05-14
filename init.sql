-- 创建数据库
CREATE DATABASE IF NOT EXISTS social_network;

-- 使用数据库
USE social_network;

-- 创建用户表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'default-avatar.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 为用户表中的 username 字段创建索引，提升用户查找效率
CREATE INDEX idx_username ON users(username);
 
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    friend_id INT NOT NULL,  -- 目标好友的ID
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES users(id) ON DELETE CASCADE
);


-- 创建好友表
CREATE TABLE IF NOT EXISTS friends (
    user_id INT NOT NULL,
    friend_id INT NOT NULL,
    status ENUM('pending', 'accepted') DEFAULT 'pending',  -- 好友关系状态：待确认 / 已确认
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, friend_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,  -- 删除用户时，删除与该用户的好友关系
    FOREIGN KEY (friend_id) REFERENCES users(id) ON DELETE CASCADE  -- 删除好友时，删除与该好友的好友关系
);

-- 创建好友请求表
CREATE TABLE IF NOT EXISTS friend_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',  -- 请求状态
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,  -- 删除用户时，删除该用户发出的请求
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE  -- 删除用户时，删除该用户接收到的请求
);
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    message_type ENUM('text', 'image', 'file') DEFAULT 'text',
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
    -- 修改 messages 表，添加 image_path 字段
    ALTER TABLE messages
    ADD COLUMN image_path VARCHAR(255) DEFAULT NULL;

);

-- 为 chat_messages 表中的 sender_id 和 receiver_id 创建复合索引
CREATE INDEX idx_sender_receiver ON chat_messages (sender_id, receiver_id);

-- 为 chat_messages 表中的 created_at 字段创建索引，提升按时间查询聊天记录的效率
CREATE INDEX idx_created_at ON chat_messages (created_at);
-- 创建消息表（聊天室消息）
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,  -- 是否已读
    message_type ENUM('text', 'image', 'file') DEFAULT 'text',  -- 消息类型：文本、图片、文件等
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,  -- 删除用户时，删除该用户的聊天记录
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE  -- 删除用户时，删除该用户的聊天记录
);

-- 为 chat_messages 表中的 sender_id 和 receiver_id 创建复合索引
CREATE INDEX idx_sender_receiver ON chat_messages (sender_id, receiver_id);

-- 为 chat_messages 表中的 created_at 字段创建索引，提升按时间查询聊天记录的效率
CREATE INDEX idx_created_at ON chat_messages (created_at);

-- 为 friends 表中的 user_id 和 friend_id 创建索引，提高查询好友关系的效率
CREATE INDEX idx_user_friend ON friends (user_id, friend_id);
CREATE INDEX idx_friend_user ON friends (friend_id, user_id);


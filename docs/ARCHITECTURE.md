# iPet 宠物管理系统 — 架构规划文档 v1.0

> **文档状态**: 最终版 | **日期**: 2026-05-23 | **基于**: Laravel 13 / Filament 5  
> **前置项目**: iCore（Filament 管理后台脚手架）→ 升级为 iPet

---

## 目录

1. [系统总览与架构图](#1-系统总览与架构图)
2. [核心功能模块清单与优先级](#2-核心功能模块清单与优先级)
3. [数据库设计最终版](#3-数据库设计最终版)
4. [技术栈确认](#4-技术栈确认)
5. [Filament 管理后台资源规划](#5-filament-管理后台资源规划)
6. [API 规划](#6-api-规划)
7. [OSS 云存储策略](#7-oss-云存储策略)
8. [视频上传与处理策略](#8-视频上传与处理策略)
9. [标签系统设计](#9-标签系统设计)
10. [开发里程碑与实施顺序](#10-开发里程碑与实施顺序)
11. [测试策略](#11-测试策略)
12. [安全与权限设计](#12-安全与权限设计)

---

## 1. 系统总览与架构图

### 1.1 系统架构

```
┌─────────────────────────────────────────────────────────────────────┐
│                          客户端层                                    │
│  ┌──────────┐  ┌──────────────┐  ┌───────────┐  ┌──────────────┐  │
│  │ iOS App  │  │ Android App  │  │ 微信小程序  │  │  Web (管理端) │  │
│  └────┬─────┘  └──────┬───────┘  └─────┬─────┘  └──────┬───────┘  │
│       │               │               │               │           │
│       └───────────────┴───────┬───────┴───────────────┘           │
│                               │                                    │
│                    HTTPS / RESTful API                              │
│                               │                                    │
└───────────────────────────────┼────────────────────────────────────┘
                                │
┌───────────────────────────────┼────────────────────────────────────┐
│                          接入层                                     │
│  ┌───────────────────────────┴──────────────────────────────────┐  │
│  │              Nginx / API Gateway (路由 & SSL)                  │  │
│  └───────────────────────────┬──────────────────────────────────┘  │
│                               │                                    │
└───────────────────────────────┼────────────────────────────────────┘
                                │
┌───────────────────────────────┼────────────────────────────────────┐
│                        应用层 (Laravel 13)                          │
│  ┌─────────────────────────────────────────────────────────────┐  │
│  │                     路由分发层                                │  │
│  │  ┌──────────┐  ┌──────────┐  ┌───────────┐                 │  │
│  │  │ /api/v1  │  │ /admin   │  │  webhooks │                 │  │
│  │  │ Sanctum  │  │ Session  │  │  (支付回调) │                 │  │
│  │  └────┬─────┘  └────┬─────┘  └─────┬─────┘                 │  │
│  └───────┼─────────────┼──────────────┼───────────────────────┘  │
│          │             │              │                           │
│  ┌───────┼─────────────┼──────────────┼───────────────────────┐  │
│  │       │        控制器/中间件层       │                       │  │
│  │  ┌────┴────┐ ┌─────┴─────┐ ┌──────┴──────┐               │  │
│  │  │ API     │ │ Filament  │ │ WebhookCtrl │               │  │
│  │  │ Controllers│ │ Resources │ │           │               │  │
│  │  └────┬────┘ └─────┬─────┘ └──────┬──────┘               │  │
│  └───────┼─────────────┼──────────────┼───────────────────────┘  │
│          │             │              │                           │
│  ┌───────┼─────────────┼──────────────┼───────────────────────┐  │
│  │       │         业务服务层           │                       │  │
│  │  ┌────┴────┐ ┌─────┴─────┐ ┌──────┴──────┐               │  │
│  │  │ Services│ │ Policies  │ │   Actions   │               │  │
│  │  │ (核心逻辑)│ │ (权限控制) │ │ (业务动作)   │               │  │
│  │  └────┬────┘ └─────┬─────┘ └──────┬──────┘               │  │
│  └───────┼─────────────┼──────────────┼───────────────────────┘  │
│          │             │              │                           │
│  ┌───────┼─────────────┼──────────────┼───────────────────────┐  │
│  │       │          领域模型层          │                       │  │
│  │  ┌────┴────┐ ┌─────┴─────┐ ┌──────┴──────┐               │  │
│  │  │  Models │ │  Events   │ │  Listeners  │               │  │
│  │  │  (Eloquent)│ │        │ │  (Queued)   │               │  │
│  │  └────┬────┘ └───────────┘ └─────────────┘               │  │
│  └───────┼────────────────────────────────────────────────────┘  │
└──────────┼────────────────────────────────────────────────────────┘
           │
┌──────────┼────────────────────────────────────────────────────────┐
│       数据/存储层                                                  │
│  ┌───────┴────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │  MySQL 8.0     │  │  Redis 7.x    │  │  阿里云 OSS           │  │
│  │  (主数据库)     │  │  (缓存/队列/   │  │  (图片/视频/CDN)      │  │
│  │               │  │   会话/锁)    │  │                      │  │
│  └────────────────┘  └──────────────┘  └──────────────────────┘  │
└───────────────────────────────────────────────────────────────────┘
```

### 1.2 数据流概要

| 流向 | 说明 |
|------|------|
| App → Laravel API | 用户操作（发帖、上传、支付等）通过 Sanctum Token 认证 |
| 管理端 → Filament | 管理员操作通过 Session 认证 + RBAC |
| API → 业务层 | Controller → Service → Model，写操作走队列异步化 |
| 文件上传 | 客户端直传 OSS（签名方式）或服务端中转 → OSS disk |
| 视频处理 | 上传完成 → 队列 Job → FFmpeg 转码/生成封面 → 回调更新 |
| 支付回调 | 支付宝/微信 → webhook → 验签 → 更新订单状态 |

### 1.3 多语言支持

| 语言 | 代码 | 覆盖范围 |
|------|------|----------|
| 简体中文 | `zh_CN` | 全量（主语言） |
| 繁體中文 | `zh_TW` | 后台 + API |
| English | `en` | 后台 + API |

---

## 2. 核心功能模块清单与优先级

### 2.1 模块分级

| 阶段 | 定义 | 时间预算 |
|------|------|----------|
| 🟢 **MVP** | 最小可用产品，上线即用 | Sprint 1-4 |
| 🟡 **V1 扩展** | MVP 后第一批功能补充 | Sprint 5-7 |
| 🔵 **V2 进阶** | 社区/商业化/高级功能 | Sprint 8-11 |
| ⚪ **远期** | 规划中，待需求确认 | TBD |

### 2.2 功能矩阵

#### 🟢 MVP (Sprint 1-4)

| 模块 | 子功能 | 说明 | 依赖 |
|------|--------|------|------|
| **用户系统** | 注册/登录/找回密码 | 手机号+邮箱，Sanctum Token | 已有基础 |
| | 个人资料管理 | 昵称、头像、简介 | Mediable |
| | 账号安全 | 密码修改、2FA（可选） | 已有基础 |
| **宠物管理** | 创建宠物档案 | 名称、品种、生日、性别、照片 | 新增模型 |
| | 编辑/删除宠物 | 档案维护 | - |
| | 宠物列表 | 用户下多宠物 | - |
| **动态发布** | 文字动态 | 纯文本发布 | 新增模型 |
| | 图片动态 | 文字+多图 | OSS + Mediable |
| | 视频动态 | 文字+视频 | OSS + Mediable + 队列 |
| | 动态列表/详情 | 时间线、详情页 | - |
| | 动态删除 | 软删除 | SoftDeletes |
| **标签系统** | 宠物标签 | 品种、性格、特征 | spatie/laravel-tags ✅ |
| | 动态标签 | 话题标签 | 同上 |
| **管理后台** | 用户管理 | 列表、详情、封禁 | 已有 |
| | 宠物管理 | CRUD | 新增 Resource |
| | 动态管理 | 列表、审核、删除 | 新增 Resource |
| | 标签管理 | 标签维护 | 新增 Resource |

#### 🟡 V1 扩展 (Sprint 5-7)

| 模块 | 子功能 | 说明 | 依赖 |
|------|--------|------|------|
| **医疗记录** | 疫苗记录 | 疫苗名称、日期、下次日期 | 新增模型 |
| | 体检记录 | 体重、体温、备注、附件 | 新增模型 + Mediable |
| | 病历记录 | 疾病、诊断、用药、附件 | 新增模型 + Mediable |
| **互动功能** | 点赞 | 动态点赞 | 新增多态关联 |
| | 评论 | 动态评论（支持图片） | 新增模型 |
| | 收藏 | 动态收藏 | 新增模型 |
| **关系链** | 关注/取关 | 用户间关注 | 新增关联 |
| | 宠物关注 | 关注其他宠物 | 新增关联 |
| **通知系统** | 点赞/评论/关注通知 | DB Notification | Laravel Notifications |
| | 系统通知 | 平台公告 | - |

#### 🔵 V2 进阶 (Sprint 8-11)

| 模块 | 子功能 | 说明 | 依赖 |
|------|--------|------|------|
| **社交功能** | 宠物相册 | 独立相册管理 | Mediable |
| | 宠物日记 | 成长记录时间线 | 新增模型 |
| | 分享/转发 | 动态转发 | 新增模型 |
| **精品内容** | 话题/活动 | 平台话题挑战 | 新增模型 |
| | 热门推荐 | 算法推荐 | Redis Sorted Set |
| **商业化** | 宠物商城 | 商品管理+订单 | 新增模型 + 支付 |
| | 会员体系 | VIP/订阅 | 钱包 + 支付 |
| | 广告系统 | 信息流广告 | 新增模型 |

---

## 3. 数据库设计最终版

### 3.1 已有表（iCore 脚手架，37 个迁移）

以下表已存在，**不需要重新创建**：

| 表名 | 说明 | 关键字段 |
|------|------|----------|
| `users` | 用户 | id, name, email, mobile, password, avatar, status, parent_id, banned_at, deleted_at |
| `admins` | 管理员 | id, name, email, password, avatar, mobile, status, banned_at, deleted_at |
| `roles` / `permissions` / `model_has_*` | RBAC 权限 | spatie/laravel-permission 标准表 |
| `wallets` / `transactions` / `transfers` / `wallet_purchases` | 钱包系统 | bavix/laravel-wallet 标准表 |
| `wallet_types` | 钱包类型 | id, name, slug |
| `user_wallet_logs` | 用户钱包日志 | id, user_id, wallet_id, amount, type, description |
| `tags` / `taggables` | 标签系统 | spatie/laravel-tags 标准表（JSON name/slug） |
| `media` / `mediables` | 媒体库 | plank/laravel-mediable 标准表 |
| `activity_log` | 操作日志 | spatie/laravel-activitylog 标准表 |
| `bans` | 封禁记录 | id, bannable_type, bannable_id, comment, expired_at |
| `passkeys` | 通行密钥 | spatie/laravel-passkeys 标准表 |
| `personal_access_tokens` | API Token | Laravel Sanctum 标准表 |
| `settings` | 系统设置 | id, key, value(JSON) |
| `password_reset_tokens` | 密码重置 | Laravel 标准表 |
| `sessions` / `cache` / `jobs` | 框架标准表 | Laravel 标准表 |

### 3.2 新增表 — 宠物相关

#### `pet_species` — 宠物品种字典

```sql
CREATE TABLE pet_species (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NULL COMMENT '父级分类ID',
    name VARCHAR(100) NOT NULL COMMENT '品种名称',
    type ENUM('dog','cat','bird','fish','rabbit','hamster','turtle','other') NOT NULL COMMENT '宠物类型',
    sort INT UNSIGNED DEFAULT 0 COMMENT '排序',
    is_active TINYINT(1) DEFAULT 1 COMMENT '是否启用',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_type (type),
    INDEX idx_parent (parent_id),
    FOREIGN KEY (parent_id) REFERENCES pet_species(id) ON DELETE SET NULL
) COMMENT='宠物品种字典';
```

#### `pets` — 宠物档案

```sql
CREATE TABLE pets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT '所属用户ID',
    species_id BIGINT UNSIGNED NULL COMMENT '品种ID',
    name VARCHAR(50) NOT NULL COMMENT '宠物名称',
    gender ENUM('male','female','unknown') DEFAULT 'unknown' COMMENT '性别',
    birthday DATE NULL COMMENT '生日',
    arrival_date DATE NULL COMMENT '到家日期',
    weight DECIMAL(8,2) NULL COMMENT '体重(kg)',
    bio TEXT NULL COMMENT '简介',
    is_public TINYINT(1) DEFAULT 1 COMMENT '是否公开',
    status TINYINT(1) DEFAULT 1 COMMENT '状态 0:隐藏 1:正常',
    sort INT UNSIGNED DEFAULT 0 COMMENT '排序',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_user (user_id),
    INDEX idx_species (species_id),
    INDEX idx_status_public (status, is_public),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (species_id) REFERENCES pet_species(id) ON DELETE SET NULL
) COMMENT='宠物档案';
```

### 3.3 新增表 — 动态相关

#### `posts` — 动态（文字+图片+视频）

```sql
CREATE TABLE posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT '发布者ID',
    pet_id BIGINT UNSIGNED NULL COMMENT '关联宠物ID(可选)',
    content TEXT NULL COMMENT '文字内容',
    post_type ENUM('text','image','video') DEFAULT 'text' COMMENT '动态类型',
    location VARCHAR(255) NULL COMMENT '发布位置',
    latitude DECIMAL(10,7) NULL COMMENT '纬度',
    longitude DECIMAL(10,7) NULL COMMENT '经度',
    view_count INT UNSIGNED DEFAULT 0 COMMENT '浏览数',
    like_count INT UNSIGNED DEFAULT 0 COMMENT '点赞数(冗余)',
    comment_count INT UNSIGNED DEFAULT 0 COMMENT '评论数(冗余)',
    share_count INT UNSIGNED DEFAULT 0 COMMENT '分享数(冗余)',
    is_pinned TINYINT(1) DEFAULT 0 COMMENT '是否置顶',
    is_reviewed TINYINT(1) DEFAULT 1 COMMENT '审核状态 0:待审 1:通过 2:驳回',
    status TINYINT(1) DEFAULT 1 COMMENT '状态 0:隐藏 1:正常',
    published_at TIMESTAMP NULL COMMENT '发布时间',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_user (user_id),
    INDEX idx_pet (pet_id),
    INDEX idx_type_reviewed (post_type, is_reviewed),
    INDEX idx_published (published_at DESC),
    INDEX idx_user_published (user_id, published_at DESC),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE SET NULL
) COMMENT='动态发布';
```

> **说明**: 动态中的图片/视频通过 `plank/laravel-mediable` 的 `mediables` 表关联，`tag` 字段用于区分：
> - `post-image` — 动态图片
> - `post-video` — 动态视频
> - `post-video-cover` — 视频封面

### 3.4 新增表 — 互动相关 (V1)

#### `comments` — 评论

```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT '评论者ID',
    post_id BIGINT UNSIGNED NOT NULL COMMENT '动态ID',
    parent_id BIGINT UNSIGNED NULL COMMENT '父评论ID(楼中楼)',
    content TEXT NOT NULL COMMENT '评论内容',
    like_count INT UNSIGNED DEFAULT 0 COMMENT '点赞数',
    status TINYINT(1) DEFAULT 1 COMMENT '状态 0:隐藏 1:正常',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_post (post_id),
    INDEX idx_user (user_id),
    INDEX idx_parent (parent_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
) COMMENT='评论';
```

#### `likes` — 点赞（多态）

```sql
CREATE TABLE likes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT '点赞者ID',
    likeable_type VARCHAR(255) NOT NULL COMMENT '多态关联类型',
    likeable_id BIGINT UNSIGNED NOT NULL COMMENT '多态关联ID',
    created_at TIMESTAMP NULL,
    UNIQUE KEY uk_user_likeable (user_id, likeable_type, likeable_id),
    INDEX idx_likeable (likeable_type, likeable_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) COMMENT='点赞';
```

#### `favorites` — 收藏（多态）

```sql
CREATE TABLE favorites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT '收藏者ID',
    favorable_type VARCHAR(255) NOT NULL COMMENT '多态关联类型',
    favorable_id BIGINT UNSIGNED NOT NULL COMMENT '多态关联ID',
    created_at TIMESTAMP NULL,
    UNIQUE KEY uk_user_favorable (user_id, favorable_type, favorable_id),
    INDEX idx_favorable (favorable_type, favorable_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) COMMENT='收藏';
```

#### `follows` — 关注（多态）

```sql
CREATE TABLE follows (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    follower_id BIGINT UNSIGNED NOT NULL COMMENT '关注者ID',
    followable_type VARCHAR(255) NOT NULL COMMENT '被关注对象类型 (App\\Models\\User / App\\Models\\Pet)',
    followable_id BIGINT UNSIGNED NOT NULL COMMENT '被关注对象ID',
    created_at TIMESTAMP NULL,
    UNIQUE KEY uk_follower_followable (follower_id, followable_type, followable_id),
    INDEX idx_followable (followable_type, followable_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE
) COMMENT='关注';
```

### 3.5 新增表 — 医疗相关 (V1)

#### `medical_records` — 医疗记录

```sql
CREATE TABLE medical_records (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pet_id BIGINT UNSIGNED NOT NULL COMMENT '宠物ID',
    user_id BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    record_type ENUM('vaccine','checkup','illness','surgery','other') NOT NULL COMMENT '记录类型',
    title VARCHAR(255) NOT NULL COMMENT '标题',
    description TEXT NULL COMMENT '描述',
    record_date DATE NOT NULL COMMENT '记录日期',
    next_date DATE NULL COMMENT '下次预约日期',
    hospital VARCHAR(255) NULL COMMENT '医院名称',
    doctor VARCHAR(100) NULL COMMENT '医生姓名',
    weight DECIMAL(8,2) NULL COMMENT '体重(kg)',
    temperature DECIMAL(4,1) NULL COMMENT '体温(°C)',
    cost DECIMAL(10,2) NULL COMMENT '费用',
    notes TEXT NULL COMMENT '备注',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_pet (pet_id),
    INDEX idx_user (user_id),
    INDEX idx_type (record_type),
    INDEX idx_date (record_date DESC),
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) COMMENT='医疗记录';
```

> **说明**: 医疗记录中的附件（化验单、处方等）通过 Mediable 关联，tag 为 `medical-attachment`。

### 3.6 新增表 — 通知相关 (V1)

#### `notifications` — Laravel 标准通知表

```sql
-- 使用 Laravel 自带迁移:
-- php artisan notifications:table

CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_notifiable (notifiable_type, notifiable_id)
);
```

### 3.7 ER 关系总览

```
users ──1:N──> pets ──1:N──> medical_records
  │              │
  │              └──M:N── tags (taggables)
  │
  ├──1:N──> posts ──1:N──> comments ──M:1──> users
  │   │         │
  │   │         └──M:N── tags (taggables)
  │   │         └──M:N── media (mediables: post-image, post-video, post-video-cover)
  │   │
  │   └──M:N── likes (多态)
  │   └──M:N── favorites (多态)
  │
  ├──M:N── follows (多态: User, Pet)
  │
  ├──M:N── tags (taggables: 用户标签)
  ├──M:N── media (mediables: avatar)
  ├──1:1── wallet (bavix/laravel-wallet)
  └──M:N── bans (bannable)

pets ──M:N──> tags (taggables)
pets ──M:N──> media (mediables: pet-avatar, pet-photo)
pets ──M:N──> follows (多态)
```

---

## 4. 技术栈确认

### 4.1 核心框架

| 组件 | 版本 | 状态 | 说明 |
|------|------|------|------|
| PHP | 8.5.5 | ✅ 已安装 | 运行环境 |
| Laravel | 13.0 | ✅ 已安装 | 核心框架 |
| Filament | 5.0 | ✅ 已安装 | 管理后台 |
| Livewire | 4.0 | ✅ 已安装 | 前端交互 |
| Tailwind CSS | 4 (via Vite) | ✅ 已安装 | 样式 |

### 4.2 数据层

| 组件 | 版本 | 状态 | 说明 |
|------|------|------|------|
| MySQL | 8.0+ | ✅ 已配置 | 主数据库 |
| Redis | 7.x | ✅ 已配置 | 缓存/队列/会话/锁 |

### 4.3 已安装的关键包

| 包名 | 版本 | 用途 | 状态 |
|------|------|------|------|
| bezhansalleh/filament-shield | ^4.0 | RBAC 权限管理 | ✅ 已集成 |
| spatie/laravel-activitylog | ^5.0 | 操作审计日志 | ✅ 已集成 |
| spatie/laravel-tags | (via filament plugin) | 标签系统 | ✅ 已集成 |
| plank/laravel-mediable | ^6.3 | 媒体管理 | ✅ 已集成 |
| imnpc/laravel-flysystem-oss | ^3.0 | 阿里云 OSS | ✅ 已集成 |
| bavix/laravel-wallet | ^12.0 | 虚拟钱包 | ✅ 已集成 |
| imnpc/filament-wallet | ^5.0 | 钱包后台 | ✅ 已集成 |
| yansongda/laravel-pay | ~3.7.0 | 支付 SDK | ✅ 已集成 |
| laravel/sanctum | ^4.0 | API Token 认证 | ✅ 已集成 |
| dedoc/scramble | ^0.13.12 | API 文档生成 | ✅ 已集成 |
| spatie/laravel-query-builder | ^7.0 | API 查询构建 | ✅ 已集成 |
| spatie/laravel-route-attributes | ^1.25 | 路由注解 | ✅ 已集成 |
| laravel/horizon | ^5.33 | 队列监控 | ✅ 已集成 |
| maatwebsite/excel | ^3.1 | Excel 导入导出 | ✅ 已集成 |
| pxlrbt/filament-excel | ^3.0 | Filament Excel 导出 | ✅ 已集成 |
| overtrue/laravel-wechat | ^8.0 | 微信 SDK（含小程序） | ✅ 已集成 |
| resend/resend-laravel | ^1.3 | 邮件服务 | ✅ 已集成 |
| leonis/easysms-notification-channel | ^3.0 | 短信通知 | ✅ 已集成 |
| vinkla/hashids | ^14.0 | ID 混淆 | ✅ 已集成 |
| simplesoftwareio/simple-qrcode | ^4.2 | 二维码生成 | ✅ 已集成 |
| spatie/laravel-passkeys | (via filament-passkeys) | WebAuthn 密钥 | ✅ 已集成 |
| filast/spatie-laravel-tags-plugin | ^5.0 | Filament 标签 UI | ✅ 已集成 |

### 4.4 需要新增的包

| 包名 | 版本 | 用途 | 优先级 |
|------|------|------|--------|
| pbmedia/laravel-ffmpeg | ^8.0 | 视频转码/封面提取 | 🟢 MVP |
| spatie/laravel-medialibrary | ^11.0 | 备选媒体库（如需更高级图片处理） | ⚪ 远期 |

> **结论**: 现有包已覆盖 95% 需求，**仅需新增 `pbmedia/laravel-ffmpeg`** 用于视频处理。

### 4.5 不引入的包（决策记录）

| 候选包 | 不引入理由 |
|--------|------------|
| laravel/reverb | 初期不需要 WebSocket 实时推送，使用轮询 + 通知即可 |
| meilisearch/meilisearch-laravel | MVP 阶段用 MySQL 全文索引 + 缓存，V2 再考虑 |
| laravel-json-api/laravel | 使用 Scramble 已满足 API 文档需求 |

---

## 5. Filament 管理后台资源规划

### 5.1 Cluster 集群规划

| Cluster | 图标 | 导航组 | 说明 | 优先级 |
|---------|------|--------|------|--------|
| `UserCluster` | heroicon-o-users | 用户管理 | 已存在 ✅ | MVP |
| `PetCluster` | heroicon-o-heart | 宠物管理 | **新增** | MVP |
| `PostCluster` | heroicon-o-newspaper | 内容管理 | **新增** | MVP |
| `MedicalCluster` | heroicon-o-clipboard-document-check | 医疗管理 | **新增** | V1 |
| `TagCluster` | heroicon-o-tag | 标签管理 | **新增** | MVP |
| `SystemCluster` | heroicon-o-cog-6-tooth | 系统设置 | **新增** | MVP |
| `FinanceCluster` | heroicon-o-banknotes | 财务管理 | 已存在 ✅ | — |
| `PermissionCluster` | heroicon-o-shield-check | 权限管理 | 已存在 ✅ | — |

> **注意**: 现有的 `Settings` Cluster 重命名为 `SystemCluster`（更语义化）。

### 5.2 Resource 资源规划

#### MVP 阶段 — 全部新增

| Resource | 所属 Cluster | Model | 页面 | 说明 |
|----------|-------------|-------|------|------|
| `PetSpeciesResource` | PetCluster | `PetSpecies` | List/Create/Edit | 品种字典管理 |
| `PetResource` | PetCluster | `Pet` | List/Create/Edit/View | 宠物档案管理 |
| `PostResource` | PostCluster | `Post` | List/Create/Edit/View | 动态管理+审核 |
| `TagResource` | TagCluster | `Tag` | List/Create/Edit | 标签管理 |
| `SystemConfigResource` | SystemCluster | `Setting` | Page | 系统配置页 |

#### V1 阶段

| Resource | 所属 Cluster | Model | 说明 |
|----------|-------------|-------|------|
| `MedicalRecordResource` | MedicalCluster | `MedicalRecord` | 医疗记录管理 |
| `CommentResource` | PostCluster | `Comment` | 评论管理 |

#### 保留的已有 Resource

| Resource | Cluster | 说明 |
|----------|---------|------|
| `UserResource` | UserCluster | ✅ 已存在，需扩展 Pet RelationManager |
| `AdminResource` | PermissionCluster | ✅ 已存在 |
| `RoleResource` | PermissionCluster | ✅ 已存在 |
| `WalletResource` / `TransactionResource` / `WalletTypeResource` | FinanceCluster | ✅ 已存在 |

### 5.3 RelationManager 关联管理器

| 父 Resource | RelationManager | 关联 | 说明 |
|------------|-----------------|------|------|
| UserResource | `PetsRelationManager` | hasMany Pet | **新增** — 用户下宠物列表 |
| PetResource | `MedicalRecordsRelationManager` | hasMany MedicalRecord | **新增** — 宠物医疗记录 |
| PetResource | `PostsRelationManager` | hasMany Post | **新增** — 宠物相关动态 |
| PetResource | `MediaRelationManager` | Mediable | **新增** — 宠物照片管理 |
| PostResource | `CommentsRelationManager` | hasMany Comment | **新增** — 动态评论管理 |

### 5.4 导航配置

```php
// AdminPanelProvider.php 中配置导航组排序
->navigationGroups([
    __('filament-model.navigation_group.user.name'),        // 用户管理
    __('filament-model.navigation_group.pet.name'),         // 宠物管理
    __('filament-model.navigation_group.post.name'),        // 内容管理
    __('filament-model.navigation_group.medical.name'),     // 医疗管理
    __('filament-model.navigation_group.tag.name'),         // 标签管理
    __('filament-model.navigation_group.role.name'),        // 权限管理
    __('filament-model.navigation_group.wallet.name'),      // 财务管理
    __('filament-model.navigation_group.setting.name'),     // 系统设置
])
```

### 5.5 Widget 规划

| Widget | 位置 | 说明 |
|--------|------|------|
| `StatsOverview` | Dashboard 顶部 | 用户数、宠物数、动态数、今日新增 |
| `PostChart` | Dashboard | 动态发布趋势图（近30天） |
| `PetTypeChart` | Dashboard | 宠物类型分布饼图 |
| `LatestPosts` | Dashboard | 最新动态列表（审核队列） |

---

## 6. API 规划

### 6.1 设计原则

- RESTful 风格，统一前缀 `/api/v1`
- 响应格式遵循 JSON:API 简化规范
- 使用 `spatie/laravel-query-builder` 实现筛选、排序、字段选择
- API 文档由 `dedoc/scramble` 自动生成
- 认证: Laravel Sanctum (Bearer Token)
- 频率限制: `throttle:api` 中间件（60 req/min 默认）

### 6.2 通用响应格式

```json
// 成功
{
  "data": { ... },
  "meta": { "current_page": 1, "last_page": 10, "per_page": 20, "total": 192 }
}

// 失败
{
  "message": "Validation failed.",
  "errors": { "field": ["错误信息"] }
}
```

### 6.3 端点设计

#### 📌 认证 (Auth)

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| POST | `/auth/register` | 注册（手机号/邮箱） | No |
| POST | `/auth/login` | 登录 | No |
| POST | `/auth/logout` | 退出 | Yes |
| POST | `/auth/refresh` | 刷新 Token | Yes |
| POST | `/auth/send-code` | 发送验证码 | No |
| POST | `/auth/reset-password` | 重置密码 | No |
| GET | `/auth/me` | 当前用户信息 | Yes |
| PUT | `/auth/me` | 更新个人资料 | Yes |
| PUT | `/auth/me/password` | 修改密码 | Yes |

#### 📌 用户 (Users)

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| GET | `/users/{id}` | 用户公开信息 | Optional |
| GET | `/users/{id}/pets` | 用户的宠物列表 | Optional |
| GET | `/users/{id}/posts` | 用户的动态列表 | Optional |

#### 📌 宠物品种 (Species)

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| GET | `/species` | 品种列表（支持 `?filter[type]=dog`） | No |
| GET | `/species/{id}` | 品种详情 | No |

#### 📌 宠物档案 (Pets) 🔒

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| GET | `/pets` | 我的宠物列表 | Yes |
| POST | `/pets` | 创建宠物档案 | Yes |
| GET | `/pets/{id}` | 宠物详情 | Owner |
| PUT | `/pets/{id}` | 更新宠物信息 | Owner |
| DELETE | `/pets/{id}` | 删除宠物 | Owner |
| POST | `/pets/{id}/avatar` | 上传宠物头像 | Owner |
| POST | `/pets/{id}/photos` | 上传宠物照片 | Owner |
| DELETE | `/pets/{id}/photos/{mediaId}` | 删除宠物照片 | Owner |

#### 📌 动态 (Posts)

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| GET | `/posts` | 动态列表（首页时间线） | Optional |
| GET | `/posts/{id}` | 动态详情 | Optional |
| POST | `/posts` | 发布动态 | Yes |
| PUT | `/posts/{id}` | 编辑动态 | Owner |
| DELETE | `/posts/{id}` | 删除动态 | Owner |
| POST | `/posts/upload` | 上传动态图片/视频 | Yes |
| GET | `/posts/{id}/comments` | 动态的评论列表 | Optional |

#### 📌 评论 (Comments) 🔒

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| POST | `/posts/{id}/comments` | 发表评论 | Yes |
| DELETE | `/comments/{id}` | 删除评论 | Owner |

#### 📌 点赞 (Likes) 🔒

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| POST | `/posts/{id}/like` | 点赞/取消点赞 | Yes |

#### 📌 收藏 (Favorites) 🔒

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| POST | `/posts/{id}/favorite` | 收藏/取消收藏 | Yes |
| GET | `/me/favorites` | 我的收藏列表 | Yes |

#### 📌 关注 (Follows) 🔒

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| POST | `/users/{id}/follow` | 关注/取关用户 | Yes |
| POST | `/pets/{id}/follow` | 关注/取关宠物 | Yes |
| GET | `/me/following` | 我的关注列表 | Yes |
| GET | `/me/followers` | 我的粉丝列表 | Yes |

#### 📌 医疗记录 (Medical Records) 🔒

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| GET | `/pets/{petId}/records` | 宠物的医疗记录列表 | Owner |
| POST | `/pets/{petId}/records` | 添加医疗记录 | Owner |
| GET | `/pets/{petId}/records/{id}` | 医疗记录详情 | Owner |
| PUT | `/pets/{petId}/records/{id}` | 更新医疗记录 | Owner |
| DELETE | `/pets/{petId}/records/{id}` | 删除医疗记录 | Owner |

#### 📌 标签 (Tags)

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| GET | `/tags` | 标签列表（支持搜索） | No |
| GET | `/tags/{id}/posts` | 标签下的动态列表 | No |

#### 📌 通知 (Notifications) 🔒

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| GET | `/me/notifications` | 通知列表 | Yes |
| GET | `/me/notifications/unread-count` | 未读通知数 | Yes |
| POST | `/me/notifications/{id}/read` | 标记已读 | Yes |
| POST | `/me/notifications/read-all` | 全部已读 | Yes |

#### 📌 上传 (Upload) 🔒

| 方法 | 端点 | 说明 | 认证 |
|------|------|------|------|
| POST | `/upload/image` | 上传图片 | Yes |
| POST | `/upload/video` | 上传视频 | Yes |
| GET | `/upload/video/{id}/status` | 查询视频转码状态 | Yes |

### 6.4 API Route 文件结构

```
routes/
├── api.php              # API 入口（路由分组）
│   ├── auth.php         # 认证路由
│   ├── users.php        # 用户路由
│   ├── pets.php         # 宠物路由
│   ├── posts.php        # 动态路由
│   ├── comments.php     # 评论路由
│   ├── species.php      # 品种路由
│   ├── tags.php         # 标签路由
│   ├── upload.php       # 文件上传路由
│   └── medical.php      # 医疗记录路由 (V1)
```

---

## 7. OSS 云存储策略

### 7.1 架构

```
┌─────────┐    直传(PUT)     ┌──────────────┐
│  客户端   │ ──────────────→ │  阿里云 OSS   │
│ (App/Web)│                  │              │
│          │ ←── 返回URL ─── │  Bucket      │
│          │                  │  └─ pets/    │
│          │  POST 签名请求    │  └─ posts/   │
│          │ ──────────────→ │  └─ avatars/ │
│  ┌───────┴──────┐          └──────────────┘
│  │ Laravel API   │                  │
│  │ /upload/sign  │          ┌───────┴──────┐
│  │ (生成STS签名) │          │  CDN 加速     │
│  └───────────────┘          │ (自定义域名)  │
│                              └──────────────┘
│  服务端中转(可选):
│  ┌──────────────────────────────────────┐
│  │ UploadedFile → MediaUploader         │
│  │ → 'oss' disk → Mediable::attach()    │
│  └──────────────────────────────────────┘
```

### 7.2 存储策略

| 内容类型 | 存储路径 | 磁盘 | 访问策略 | 说明 |
|---------|---------|------|---------|------|
| 用户头像 | `avatars/{user_id}/{filename}` | oss | CDN 公开 | 原图 + 缩略图 |
| 宠物头像 | `pets/{pet_id}/avatar/{filename}` | oss | CDN 公开 | — |
| 宠物照片 | `pets/{pet_id}/photos/{filename}` | oss | CDN 公开 | — |
| 动态图片 | `posts/images/{date}/{filename}` | oss | CDN 公开 | 按日期分目录 |
| 动态视频 | `posts/videos/{date}/{filename}` | oss | CDN 公开 | 原始+转码后 |
| 医疗附件 | `medical/{record_id}/{filename}` | oss | 私有（签名URL） | 敏感信息 |
| 系统文件 | `system/{filename}` | oss | 私有/公开 | 按需 |

### 7.3 当前 OSS 配置

`config/filesystems.php` 已有完整 OSS 配置：

```php
'oss' => [
    'driver' => 'oss',
    'access_id' => env('OSS_ACCESS_KEY_ID'),
    'access_key' => env('OSS_ACCESS_KEY_SECRET'),
    'bucket' => env('OSS_BUCKET'),
    'endpoint' => env('OSS_ENDPOINT'),
    'url' => env('OSS_DOMAIN'),  // CDN CNAME 域名
    'root' => env('OSS_ROOT', ''), // 可选子目录前缀
    'security_token' => null,
    'proxy' => null,
    'timeout' => 3600,
    'ssl' => env('OSS_SSL', false),
],
```

`config/mediable.php` 已配置：

```php
'allowed_disks' => ['public', 'oss'],
'url_generators' => [
    'oss' => Plank\Mediable\UrlGenerators\S3UrlGenerator::class,
],
```

### 7.4 图片处理

- **上传时**: `spatie/image-optimizer` 自动优化（mediable 配置已启用）
- **缩略图**: 通过 OSS 的图片处理（`?x-oss-process=image/resize,w_200`）生成 CDN URL
- **格式转换**: 支持 WebP 转换参数

### 7.5 安全

- 敏感文件（医疗附件）使用 `private` 存储 + 签名 URL 访问
- OSS Bucket 设置 CORS 白名单
- 防盗链 Referer 白名单

---

## 8. 视频上传与处理策略

### 8.1 处理流程

```
用户上传视频
    │
    ▼
┌─────────────────┐
│ 1. 客户端直传 OSS │ ← 预签名 PUT URL (STS Token)
│    或服务端中转   │
└────────┬────────┘
         │ 上传完成回调 / 数据库记录
         ▼
┌─────────────────┐
│ 2. 创建 Media 记录 │ ← Mediable, status=processing
│    + Post 记录    │ ← post_type=video
└────────┬────────┘
         │ 触发队列 Job
         ▼
┌─────────────────┐     ┌──────────────┐
│ 3. Horizon Queue │ ──→ │ FFmpeg Worker │
│   ProcessVideoJob │     │  转码 720p    │
│                   │     │  转码 480p    │
│                   │     │  生成封面      │
│                   │     │  提取时长      │
└────────┬────────┘     └──────┬───────┘
         │                     │
         │ ◄───────────────────┘
         ▼
┌─────────────────┐
│ 4. 更新 Media     │ ← 转码后 URL, duration, cover
│    更新 Post      │ ← status=completed
│    清理原文件(可选) │
└─────────────────┘
```

### 8.2 技术方案

| 环节 | 方案 |
|------|------|
| 转码工具 | FFmpeg（服务器安装） |
| PHP 封装 | `pbmedia/laravel-ffmpeg` |
| 队列 | Horizon + Redis，独立 `video` 队列 |
| 转码参数 | H.264, 720p (max 1280px), 30fps, 2M bitrate |
| 封面提取 | 取第 1 秒帧，JPEG 800px 宽 |
| 格式支持 | 输入: MP4/MOV/M4V/WebM, 输出: MP4 (H.264 + AAC) |
| 大小限制 | 最大 100MB（mediable 已配置）, 建议客户端限制 500MB |
| 超时 | Job 超时 600s, 重试 2 次 |

### 8.3 实现细节

```php
// app/Jobs/ProcessVideo.php (伪代码)

class ProcessVideo implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    public $tries = 3;
    public $timeout = 600;

    public function handle(): void
    {
        $media = $this->post->firstMedia('post-video');

        // 1. 生成封面
        FFMpeg::fromDisk('oss')
            ->open($media->getDiskPath())
            ->getFrameFromSeconds(1)
            ->export()
            ->toDisk('oss')
            ->save("posts/videos/{$this->post->id}/cover.jpg");

        // 2. 转码 720p
        FFMpeg::fromDisk('oss')
            ->open($media->getDiskPath())
            ->export()
            ->inFormat(new X264('aac'))
            ->resize(1280, 720)
            ->toDisk('oss')
            ->save("posts/videos/{$this->post->id}/720p.mp4");

        // 3. 更新 media 记录
        // 4. 更新 post status
        // 5. 触发 Notification
    }
}
```

---

## 9. 标签系统设计

### 9.1 技术选型

使用已安装的 `spatie/laravel-tags` + `filament/spatie-laravel-tags-plugin`。

**表结构已存在** (`tags` 表，JSON 格式的 name/slug，支持多语言)。

### 9.2 标签类型定义

| 类型 (`type` 字段) | 用途 | 多态关联目标 | 管理方式 |
|-------------------|------|------------|---------|
| `pet-breed` | 宠物品种标签 | Pet | 管理员预设 + 审核 |
| `pet-character` | 宠物性格标签 | Pet | 用户自由创建 |
| `pet-feature` | 宠物特征标签 | Pet | 用户自由创建 |
| `post-topic` | 动态话题标签 | Post | 管理员预设 + 热门话题 |
| `post-tag` | 动态自由标签 | Post | 用户自由创建 |
| `user-interest` | 用户兴趣标签 | User | 用户自选 |

### 9.3 Model 配置

```php
// app/Models/Pet.php
use Spatie\Tags\HasTags;

class Pet extends Model
{
    use HasTags;

    // 限定宠物使用的标签类型
    public static function getTagClassName(): string
    {
        return Tag::class;
    }
}

// app/Models/Post.php
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasTags;
}
```

### 9.4 标签管理策略

| 策略 | 说明 |
|------|------|
| **预设标签** | `pet-breed`, `post-topic` 由管理后台创建，用户选择使用 |
| **自由标签** | `pet-character`, `pet-feature`, `post-tag` 用户自由创建 |
| **标签审核** | 自由标签创建后默认隐藏，需审核通过才公开展示 |
| **热门标签** | 按使用频次排序，Redis Sorted Set 缓存 Top 100 |
| **标签合并** | 管理员可合并相似标签（管理后台功能） |

### 9.5 Filament TagsInput 配置

```php
// PetResource form
SpatieTagsInput::make('tags')
    ->type('pet-character')
    ->suggestions(PetCharacterTag::pluck('name', 'id')->toArray())
```

---

## 10. 开发里程碑与实施顺序

### 10.1 Sprint 总览

| Sprint | 周期 | 目标 | 交付物 |
|--------|------|------|--------|
| Sprint 0 | 1 周 | 项目初始化 | 环境搭建、代码规范、CI/CD |
| Sprint 1 | 2 周 | 宠物档案 MVP | Pet/Species 模型+API+后台 |
| Sprint 2 | 2 周 | 动态发布 MVP | Post 模型+API+后台+媒体上传 |
| Sprint 3 | 2 周 | 标签+互动基础 | Tags+点赞+评论 API |
| Sprint 4 | 2 周 | MVP 收尾+测试 | 联调、测试、文档、部署 |
| Sprint 5 | 2 周 | 医疗记录 | MedicalRecord 模型+API+后台 |
| Sprint 6 | 2 周 | 社交互动 | 关注+收藏+通知 |
| Sprint 7 | 2 周 | V1 收尾+优化 | 性能优化、CDN、监控 |
| Sprint 8-11 | 8 周 | V2 进阶 | 商城、会员、推荐 |

### 10.2 Sprint 0 — 项目初始化 (1周)

- [ ] 确认开发环境（PHP 8.5 + MySQL 8.0 + Redis 7 + FFmpeg）
- [ ] `composer require pbmedia/laravel-ffmpeg`
- [ ] 配置 FFmpeg 路径
- [ ] 创建 `develop` 分支，建立分支策略
- [ ] 配置 Laravel Pint 代码风格（PSR-12）
- [ ] 建立 `.env` 模板（含 OSS/支付/视频相关环境变量）
- [ ] 确认 Horizon 队列配置（video 队列）
- [ ] 初始化 `docs/` 目录
- [ ] 运行 `php artisan shield:generate --all`

### 10.3 Sprint 1 — 宠物档案 (2周)

**Week 1: 数据层 + API**

- [ ] 创建 `PetSpecies` 模型 + 迁移 + Factory + Seeder
- [ ] 创建 `Pet` 模型 + 迁移 + Factory + Seeder
- [ ] 编写 `PetPolicy` + `PetSpeciesPolicy`
- [ ] 实现 `SpeciesController` (list, show)
- [ ] 实现 `PetController` (CRUD + avatar + photos)
- [ ] API 测试

**Week 2: Filament + 联调**

- [ ] 创建 `PetCluster`
- [ ] 创建 `PetSpeciesResource` (List/Create/Edit)
- [ ] 创建 `PetResource` (List/Create/Edit/View)
- [ ] 在 `UserResource` 添加 `PetsRelationManager`
- [ ] 运行 `shield:generate`
- [ ] 管理后台联调测试

### 10.4 Sprint 2 — 动态发布 (2周)

**Week 1: 数据层 + API**

- [ ] 创建 `Post` 模型 + 迁移 + Factory
- [ ] 编写 `PostPolicy`
- [ ] 实现文件上传 API (`/upload/image`, `/upload/video`)
- [ ] 实现 `PostController` (list, show, store, update, destroy)
- [ ] 实现 `ProcessVideo` Job (FFmpeg)
- [ ] API 测试

**Week 2: Filament + 联调**

- [ ] 创建 `PostCluster`
- [ ] 创建 `PostResource` (List/Create/Edit/View, 审核)
- [ ] 视频处理联调
- [ ] OSS 存储验证

### 10.5 Sprint 3 — 标签 + 互动 (2周)

**Week 1: 标签**

- [ ] 配置 Model 的 HasTags trait
- [ ] 创建 `TagCluster` + `TagResource`
- [ ] 实现标签 API (list, search)
- [ ] 种子数据（宠物品种标签）

**Week 2: 互动**

- [ ] 创建 `Comment` 模型 + API
- [ ] 创建 `Like` 模型 + API（多态）
- [ ] 动态计数更新（监听 Event）
- [ ] 管理后台评论管理

### 10.6 Sprint 4 — MVP 收尾 (2周)

- [ ] 完整端到端测试
- [ ] API 文档生成 (`php artisan scramble:export`)
- [ ] 性能基准测试 + 优化（N+1 查询、缓存）
- [ ] 部署文档 + 运维手册
- [ ] 种子数据完善
- [ ] 安全审查 (Shield 权限、CORS、Rate Limit)

### 10.7 Sprint 5-7 — V1 扩展

| Sprint | 重点 |
|--------|------|
| Sprint 5 | 医疗记录（Model + API + Filament + 附件上传） |
| Sprint 6 | 社交互动（Follow + Favorite + Notification） |
| Sprint 7 | V1 优化（性能、CDN、监控、异常处理） |

---

## 11. 测试策略

### 11.1 测试金字塔

```
         ┌──────┐
         │ E2E  │  5%  — 核心用户流程（Playwright / Dusk）
         ├──────┤
         │ 集成  │ 20%  — API 端点 + Service 层
         ├──────┤
         │ 单元  │ 75%  — Models / Policies / Services / Jobs
         └──────┘
```

### 11.2 测试技术栈

| 层级 | 工具 | 配置 |
|------|------|------|
| 单元测试 | PHPUnit 12 (Laravel Tests) | SQLite `:memory:` |
| 集成测试 | PHPUnit + RefreshDatabase | SQLite |
| 特性测试 | PHPUnit Feature Tests | SQLite |
| API 测试 | PHPUnit + Sanctum actingAs | SQLite |
| E2E 测试 | Laravel Dusk / Playwright | 测试环境 |
| 静态分析 | Laravel Pint + IDE Inspections | — |
| 队列测试 | `Queue::fake()` | SQLite |

### 11.3 当前已有测试

| 文件 | 类型 | 说明 |
|------|------|------|
| `tests/Feature/Api/UserMeTest.php` | API Feature | ✅ 用户信息 API 测试 |
| `tests/Feature/Filament/UserResourceTest.php` | Filament Feature | ✅ 用户管理页测试 |
| `tests/Feature/Services/LogServiceTest.php` | Service | ✅ 日志服务测试 |
| `tests/Feature/Services/UserWalletServiceTest.php` | Service | ✅ 钱包服务测试 |
| `tests/Unit/ExampleTest.php` | Unit | ✅ 示例测试 |
| `tests/Feature/ExampleTest.php` | Feature | ✅ 示例测试 |

### 11.4 测试覆盖要求

| 模块 | 单元测试 | API 测试 | Filament 测试 |
|------|---------|---------|---------------|
| 认证 | ✅ | ✅ | — |
| 宠物档案 | PetPolicy, PetService | PetController CRUD | PetResource CRUD |
| 动态发布 | PostPolicy, PostService | PostController CRUD | PostResource CRUD |
| 文件上传 | MediaUploadService | Upload endpoints | — |
| 视频处理 | ProcessVideoJob | 队列测试 | — |
| 标签 | TagService | Tag endpoints | TagResource CRUD |
| 互动 | Like/Comment Service | Like/Comment endpoints | CommentResource |
| 医疗记录 | MedicalRecordService | MedicalRecord endpoints | MedicalRecordResource |

### 11.5 CI/CD 测试流程

```yaml
# .github/workflows/tests.yml (示例)
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      redis:
        image: redis:7
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.5'
      - run: composer install
      - run: php artisan test --parallel
      - run: php artisan pint --test
```

### 11.6 关键测试场景

- ✅ 用户注册 → 登录 → 创建宠物 → 发布动态 → 上传图片 → 查看时间线
- ✅ 视频上传 → 队列处理 → 转码完成 → 封面生成
- ✅ 并发点赞 → 计数一致性
- ✅ 用户封禁 → API 拒绝访问
- ✅ 大数据量分页 → 性能不退化
- ✅ OSS 签名 URL → 过期后拒绝访问

---

## 12. 安全与权限设计

### 12.1 认证体系

| 端 | Guard | 驱动 | Token/认证方式 |
|----|-------|------|---------------|
| 管理后台 | `admin` | Session | Cookie + CSRF + 2FA |
| API | `sanctum` | Sanctum Token | Bearer Token |
| Webhooks | `web` | — | 签名验证 |

### 12.2 RBAC 权限模型

使用 `bezhansalleh/filament-shield` + `spatie/laravel-permission`。

#### 管理员角色

| 角色 | 权限范围 | 说明 |
|------|---------|------|
| `super_admin` | 全部 | 超级管理员（user_id=1 自动赋予） |
| `admin` | 管理级 | 日常管理（用户/宠物/动态/标签） |
| `content_moderator` | 内容审核 | 动态审核、评论管理 |
| `finance` | 财务 | 钱包、支付、退款 |
| `viewer` | 只读 | 查看权限，不可操作 |

#### 权限命名规范

```
资源级:
  view_user, view_any_user, create_user, update_user, delete_user
  view_post, view_any_post, create_post, update_post, delete_post, review_post
  view_pet, view_any_pet, create_pet, update_pet, delete_pet
  view_medical_record, view_any_medical_record, ...

页面级:
  page_dashboard
  page_system_config

动作级:
  export_users, export_posts
  ban_user, unban_user
  review_post
```

### 12.3 API 权限策略

| 端点 | 权限要求 |
|------|---------|
| 公开端点 (`/species`, `/tags`) | 无需认证 |
| 认证用户端点 (`/me/*`) | Sanctum Token |
| 资源所有者端点 (`PUT /pets/{id}`) | Token + Policy（只能操作自己的宠物） |
| 管理员高点 (`/admin/*`) | Session + RBAC |

### 12.4 Policy 注册

所有模型对应 Policy 需注册到 `AuthServiceProvider`：

```php
// app/Providers/AuthServiceProvider.php
protected $policies = [
    Pet::class => PetPolicy::class,
    Post::class => PostPolicy::class,
    Comment::class => CommentPolicy::class,
    MedicalRecord::class => MedicalRecordPolicy::class,
    PetSpecies::class => PetSpeciesPolicy::class,
    // User, Admin, Role 已在 iCore 中注册
];
```

### 12.5 安全措施清单

| 措施 | 实现方式 | 状态 |
|------|---------|------|
| SQL 注入防护 | Eloquent ORM + 参数绑定 | ✅ |
| XSS 防护 | Blade 自动转义 / API 输出净化 | ✅ |
| CSRF 防护 | Laravel CSRF Token（Web） + Sanctum SPA Auth | ✅ |
| CORS | `config/cors.php` 白名单 | ✅ |
| Rate Limiting | `throttle:api` 中间件（60/min） | ✅ |
| 文件上传限制 | Mediable: 100MB, MIME 白名单 | ✅ |
| 密码哈希 | Bcrypt (rounds=12) | ✅ |
| 2FA | Filament App Authentication (TOTP) | ✅ 已启用 |
| Passkeys | WebAuthn (spatie/laravel-passkeys) | ✅ 已启用 |
| 封禁系统 | Bannable trait + middleware | ✅ 已启用 |
| 审计日志 | spatie/laravel-activitylog | ✅ 已启用 |
| API 文档 | Scramble（仅开发/测试环境开放） | ✅ |
| 敏感数据加密 | `encrypted` casting (2FA secret) | ✅ |
| 私有文件签名 URL | OSS STS Token 临时授权 | 待实现 |

### 12.6 数据隐私

| 数据类别 | 保护策略 |
|---------|---------|
| 用户手机号 | API 返回脱敏（`138****1234`），管理员可见 |
| 用户邮箱 | API 返回脱敏（`u***@example.com`），管理员可见 |
| 医疗记录 | 仅宠物主人 + 管理员可查看 |
| 医疗附件 | OSS 私有存储 + 签名URL 访问 |
| 动态内容 | 公开可查看，可设置宠物级隐私 |

### 12.7 API Token 生命周期

| 属性 | 值 |
|------|-----|
| Token 类型 | Sanctum Personal Access Token |
| 默认有效期 | 永不过期（可手动撤销） |
| 可选有效期 | `abilities` 控制（如 `post:create`） |
| Refresh 策略 | 重新登录获取新 Token |
| 多设备 | 每个设备独立 Token |
| 撤销策略 | 修改密码时撤销所有 Token |

---

## 附录

### A. 文件结构 — 新增文件清单

```
app/
├── Enums/
│   ├── PetGender.php              # 宠物性别枚举
│   ├── PostType.php               # 动态类型枚举
│   ├── RecordType.php             # 医疗记录类型枚举
│   └── ReviewStatus.php           # 审核状态枚举
├── Filament/
│   ├── Clusters/
│   │   ├── PetCluster/            # 宠物管理集群
│   │   │   ├── PetCluster.php
│   │   │   └── Resources/
│   │   │       ├── PetSpeciesResource.php
│   │   │       └── PetResource/
│   │   │           ├── Pages/
│   │   │           ├── RelationManagers/
│   │   │           └── PetResource.php
│   │   ├── PostCluster/           # 内容管理集群
│   │   │   ├── PostCluster.php
│   │   │   └── Resources/
│   │   │       └── PostResource/
│   │   ├── MedicalCluster/        # 医疗管理集群 (V1)
│   │   │   ├── MedicalCluster.php
│   │   │   └── Resources/
│   │   │       └── MedicalRecordResource/
│   │   └── TagCluster/            # 标签管理集群
│   │       ├── TagCluster.php
│   │       └── Resources/
│   │           └── TagResource/
│   └── Widgets/
│       ├── StatsOverview.php
│       └── LatestPosts.php
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── PetController.php
│   │   ├── PostController.php
│   │   ├── CommentController.php
│   │   ├── LikeController.php
│   │   ├── FollowController.php
│   │   ├── FavoriteController.php
│   │   ├── MedicalRecordController.php
│   │   ├── SpeciesController.php
│   │   ├── TagController.php
│   │   ├── UploadController.php
│   │   └── NotificationController.php
│   └── Resources/
│       ├── PetResource.php
│       ├── PostResource.php
│       └── MedicalRecordResource.php
├── Jobs/
│   └── ProcessVideo.php           # 视频转码 Job
├── Models/
│   ├── Pet.php
│   ├── PetSpecies.php
│   ├── Post.php
│   ├── Comment.php
│   ├── Like.php
│   ├── Follow.php
│   ├── Favorite.php
│   └── MedicalRecord.php
├── Policies/
│   ├── PetPolicy.php
│   ├── PetSpeciesPolicy.php
│   ├── PostPolicy.php
│   ├── CommentPolicy.php
│   └── MedicalRecordPolicy.php
└── Services/
    ├── PetService.php
    ├── PostService.php
    ├── MediaUploadService.php
    └── VideoProcessService.php

database/
├── factories/
│   ├── PetFactory.php
│   ├── PostFactory.php
│   └── MedicalRecordFactory.php
├── migrations/
│   ├── 2026_xx_xx_create_pet_species_table.php
│   ├── 2026_xx_xx_create_pets_table.php
│   ├── 2026_xx_xx_create_posts_table.php
│   ├── 2026_xx_xx_create_comments_table.php
│   ├── 2026_xx_xx_create_likes_table.php
│   ├── 2026_xx_xx_create_favorites_table.php
│   ├── 2026_xx_xx_create_follows_table.php
│   ├── 2026_xx_xx_create_medical_records_table.php
│   └── 2026_xx_xx_create_notifications_table.php
└── seeders/
    ├── PetSpeciesSeeder.php
    └── PetDemoDataSeeder.php

tests/
├── Feature/
│   ├── Api/
│   │   ├── AuthTest.php
│   │   ├── PetTest.php
│   │   ├── PostTest.php
│   │   ├── CommentTest.php
│   │   ├── UploadTest.php
│   │   └── MedicalRecordTest.php
│   ├── Filament/
│   │   ├── PetResourceTest.php
│   │   └── PostResourceTest.php
│   └── Jobs/
│       └── ProcessVideoTest.php
└── Unit/
    ├── Models/
    │   ├── PetTest.php
    │   └── PostTest.php
    └── Services/
        └── VideoProcessServiceTest.php

routes/
├── api.php (改)
├── api/
│   ├── auth.php
│   ├── pets.php
│   ├── posts.php
│   └── ...
```

### B. 环境变量需要新增的项

```env
# .env 新增

# FFmpeg
FFMPEG_BINARIES=/usr/bin/ffmpeg
FFPROBE_BINARIES=/usr/bin/ffprobe

# 视频转码
VIDEO_MAX_SIZE_MB=500
VIDEO_QUEUE=video
VIDEO_TRANSCODE_TIMEOUT=600

# OSS 私有 Bucket（医疗附件等）
OSS_PRIVATE_ACCESS_KEY_ID=
OSS_PRIVATE_ACCESS_KEY_SECRET=
OSS_PRIVATE_BUCKET=
OSS_PRIVATE_ENDPOINT=
OSS_PRIVATE_DOMAIN=
```

### C. 版本历史

| 版本 | 日期 | 变更 |
|------|------|------|
| v1.0 | 2026-05-23 | 初始版本，基于 iCore 脚手架设计 |

---

> **本文档为 iPet 项目的唯一架构参考源。任何架构变更需更新本文档并经过代码审查。**

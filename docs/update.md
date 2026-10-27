* 2026-10-27 v2.8.7

> 安全与稳定性集中修复版本。`2.8.6..2.8.7` 共 19 次提交（含 3 次合并提交）、67 个文件变更（+3601 / -1894）。本轮代码审计确认 2 个严重级、11 个高危级、21 个中危级、11 个低危级问题，已完成修复或加固；口令存储升级为加盐 bcrypt，与全部安全修复一致地**不做向后兼容**。**升级前请先阅读文末「升级注意」**。

#### 安全修复

- 修复安装器可被任意访客重装并接管站点：仅在「无 `config/install.lock` 且管理员口令仍为出厂默认」时允许安装，并禁止再以出厂默认口令完成安装；新增 `EASYIMAGE_DEFAULT_PASSWORD` 常量与由其派生的 `EASYIMAGE_DEFAULT_PASSWORD_HASH`（不再硬编码摘要字面量）、第二步来源校验、账号/域名清洗与密码 8~64 位限制；「是否仍为出厂默认」的判定由字符串比较统一改为 `password_verify(默认摘要, 存储值)`（`app/check.php`、`install/index.php`、`install/install.php`、`install/contorl.php`、`admin/inc.check.php`） `9f8abdb` `941512d`
- 修复文件管理器越权写 WebShell（[#285](https://github.com/icret/EasyImages2.0/issues/285)）：`admin/manager.php`、`admin/filer.php` 根目录收敛到图片存储目录，创建/上传扩展名对齐主配置图片白名单，关闭在线编辑、打包下载与远程拉取并补鉴权闸 `b12cb67` `0e6838d`
- 修复认证凭据可重放（#285）：登录态由下发 `auth` Cookie 改为服务端 Session + `session_regenerate_id()`，主动失效历史凭据 Cookie，并提供 `_logout()` 统一登出 `b12cb67`
- 修复登录日志泄露可重放凭据（#285）：不再记录口令或其 SHA256 摘要，并清理换行防止日志伪造 `b12cb67` `9f8abdb`
- 修复未授权路径穿越导致任意文件读取（CWE-22，[#286](https://github.com/icret/EasyImages2.0/issues/286)）：新增统一收敛函数 `resolve_image_path()` / `is_within_storage()`，`thumb/down/info/hide/del/compressing/list/admin.inc` 全部复用；站点根放行按读写分面逐调用点显式 opt-in，写与取回端点锁死存储目录 `e337a2b`
- 修复任意文件删除（[#283](https://github.com/icret/EasyImages2.0/issues/283)）：能力令牌 `urlHash()` 由 `crc32 + 固定 IV + AES-128-XTS`（无认证）重做为 `HKDF-SHA256` 派生密钥 + 随机 IV + `AES-256-GCM` 认证加密 + base64url，`del`/`hide` 域隔离，解密失败一律视为无效；`del_version_file` 改为硬编码固定目标 `e337a2b`
- 修复 SVG 存储型 XSS：扩展名统一转小写（`.SVG` 不再绕过），新增 `is_safe_svg()` 拒绝 `script`/`foreignObject`/`iframe`/`use`/事件属性/`javascript:`/实体膨胀等载荷，Web、API、两个文件管理器上传均执行校验；直接访问 SVG 时由 `i/.htaccess` 与 `app/thumb.php` 追加 `Content-Security-Policy: sandbox` `9f8abdb`
- 修复上传日志存储型 XSS：`User-Agent` 入库前去除控制字符，日志页输出统一 HTML 转义并在 JS 上下文使用实体 `9f8abdb`
- 修复后台日志查看任意文件包含：`app/viewlog.php`、`app/info.php` 的 `logDate` 仅允许 `YYYY-MM` 白名单 `9f8abdb`
- 修复统计页任意目录删除：`admin/chart.php`、`admin/zui.chart.php` 只允许清空固定的 `admin/logs/counts` 目录 `9f8abdb`
- 新增服务端上传扩展名白名单：`app/upload.php`、`api/index.php` 必须命中 `config.extensions`，不再只信任 MIME 类型 `9f8abdb`
- 修复 TinyFileManager 远程 URL 上传导致的 SSRF：关闭该能力并移除 `shell_exec` MIME 回退 `9f8abdb`
- 修复 `X-Forwarded-For`/`Client-IP` 伪造绕过 IP 黑白名单与游客每日限额：`real_ip()` 仅在直连对端为内网/回环（可信代理）时采信转发头，并用 `filter_var()` 校验；新增 `trusted_proxy` 配置项，公网反代/CDN 部署可填写代理出口 IP（支持 `*` 通配段） `9f8abdb` `941512d`
- 修复审查发现的防护短板：文件管理器 SVG/图片内容校验改为在分片合并后的完整文件上执行（合法首片+恶意后续片不再能绕过，>10MB 合法大图不再被首片截断误拒）；首页→安装页跳转增加“密码仍为出厂默认”前置条件，与安装门闸不再构成重定向死环；`zui.chart.php` 补齐 CSRF 校验；批量删除改走 `resolve_image_path()` 收敛并按实际结果返回状态；后台改密补 8 位最小长度；`path_rtoa()` 越界改为拒绝并校验新建目标的最近存在祖先；`real_ip()` 采信转发头支持可信代理名单；缩略图生成失败回落原图；日志输出侧补转义 `941512d`
- 新增后台 CSRF 防护：`csrf_token()`/`csrf_verify()` 令牌保存在服务端 Session，表单由页面 JS 自动补隐藏字段，危险 GET 操作由服务端拼接 `&csrf=`，`app/del.php` 管理员 POST 与统计页 POST 统一校验；公开能力（hash 删除链接）不受影响 `9f8abdb`
- 修复源图保护默认 Key 可预测：开启 `hide` 而 `hide_key` 为空或仍为公开默认值时，自动生成 16 字节随机 Key `9f8abdb`
- 口令存储升级为加盐 bcrypt（cost 12），**不向后兼容**：落盘由无盐单轮 `SHA256(明文)` 改为 `bcrypt(SHA256(明文))`，即使 `config.php` 泄露也不再直接得到可离线暴破的凭据（登录线上仍传前端 `SHA256(明文)` 摘要作 pre-hash，重放防护由 TLS/会话承担）；安装器、后台改密、上传者增改均走同一算法，登录端以提交摘要做 `password_verify`（修复 [#268](https://github.com/icret/EasyImages2.0/pull/268) 早期直接存 bcrypt 导致“安装完即无法登录”的问题，改为与登录校验配对的正确实现）。**不再接受遗留无盐 SHA256 摘要作为存储值**（无透明迁移），升级后存量以 SHA256 存储的管理员/上传者口令均失效，需重新走安装引导或按下方恢复路径重置 `9f8abdb` `941512d`
- 收紧认证写入与校验路径（本轮复核加固）：安装器/口令恢复页等“入参恒为明文”的写入路径显式按 `bcrypt(SHA256(明文))` 计算，不再猜测“64 位十六进制即摘要”（避免默认摘要串被当摘要绕过禁默认判定、以及十六进制明文口令被锁死）；安装禁默认改为按有效摘要比对（与输入形态无关）；登录失败统一为「账号或密码错误」并对不存在账号也跑一次 bcrypt 拉平时序，抑制账号枚举；后台改密对明文回退补 8~64 位长度校验；`app/reset_password.php` 的在线口令转换改为仅登录管理员可用（防匿名触发 bcrypt 的 CPU 放大），被锁在外者走静态恢复指引；出厂示例上传者账号（遗留 SHA256、已失效）移除 `941512d`
- 新增分片上传参数校验：`chunk`/`chunks` 非数字、数组入参或直接缺失分片文件时拒绝请求 `5472245` `90ab630` `4e62145`
- 新增部署侧防护：新增 `config/.htaccess`（配置目录整体拒绝 Web 访问）、重写 `i/.htaccess`（关闭 PHP 引擎 + `AcceptPathInfo Off` + 可执行扩展名拒绝 + 隐藏文件拒绝 + SVG sandbox）、新增根目录 `.htaccess`（拒绝读取审计报告与安装锁）；`docs/安全配置.md` 同步 Nginx/Caddy 等价规则 `b12cb67` `9f8abdb`
- 新增基础安全响应头：`X-Content-Type-Options: nosniff`、`X-Frame-Options: SAMEORIGIN`、`Referrer-Policy: same-origin`（`app/base.php`，CLI 下跳过） `9f8abdb`
- 其他加固：后台配置保存改为只接受已存在的配置键（`array_intersect_key`），并对存储路径/管理员路径/账号/密码/Token 入参做收敛校验；验证码随机源 `rand()` → `random_int()`；`get_whole_url()` 过滤 Host 头非法字符；`class.version.php` 恢复 TLS 校验并移除全局 `error_reporting(0)`；Bing 背景抓取重写为 HTTPS + curl 超时 + TLS 校验 + 图片内容校验 + 固定 cache 目录 `9f8abdb`
- 加固输出与响应头：`app/hide.php` 补扩展名白名单并返回正确 `Content-Type`；路径收敛后越界请求统一返回 403、文件不存在返回 404（`thumb/down/info`）；`app/info.php` 与 `app/compressing.php` 结果字段转义，下载文件名头引号化；`app/reset_password.php` 的 `md5` 表单入参补 `is_string` 类型校验 `e337a2b` `b12cb67` `9f8abdb` `941512d`

#### 功能新增

- 新增 API 上传文件名 Token ID 后缀开关 `token_suffix_ID`（[#241](https://github.com/icret/EasyImages2.0/issues/241)） `e0665f0`
- 后台 Token API 管理界面重构：调用地址与现有 Token 列表合并展示，增加/修改 Token 独立分区 `e0665f0`
- 鉴黄判定在 `Sexy`、`Porn` 之外增加 `Hentai` 维度（[#271](https://github.com/icret/EasyImages2.0/pull/271)） `bf528f8`

#### Bug 修复

- 修复 IP 黑/白名单形同虚设：`checkIP()` 中 `isset($ipNow) ?: real_ip()` 为裸 Elvis 表达式（结果为布尔值）、正则定界符冲突触发 `No ending delimiter`、空名单生成 `/^()$/` 匹配空串、白名单模式不支持通配段、数组入参被当错误报错，`preg_quote` 处理名单中的 `+ ( ) [ ]` 元字符 `3fe184d` `9f8abdb`
- 修复 API Token 校验依赖松散比较：`in_array($tokenList[$token], $tokenList)` 改为 `array_key_exists()` `3fe184d`
- 修复 `privateToken()` 使用非 CSPRNG 的 `mt_rand`（`chr(65..122)` 还会产出方括号、反斜杠、反引号等非字母符号）且 `$length` 参数实际无效：改用 `random_bytes()` `3fe184d`
- 修复 `getdirnum()` 递归不回传计数，任何情况下目录数与文件数恒为 0 `3fe184d`
- 修复大文件分片上传：合并目标由相对路径（落到当前工作目录）改为存储目录下的 `cache/`，补齐 `cache/` 目录创建与分片缺失校验，避免先建同名目录导致 `fopen` 必败、合并出空文件 `3fe184d` `4e62145`
- 修复 `GLOB_BRACE` 在 Alpine/Solaris 等非 GNU 系统未定义导致后台致命错误（[#258](https://github.com/icret/EasyImages2.0/pull/258)） `edb5f9f`
- 修复会话有效期与告警：Cookie 与 `session.gc_maxlifetime` 对齐 14 天（原默认 1440s 使管理员约 24 分钟即掉线），写 Cookie 前判断 `headers_sent()` 避免 Warning `4e62145`
- 修复删除结果谎报成功：哈希删除与版本文件删除按 `unlink`/迁移的真实结果返回，`del_version_file` 不再在成功分支无条件抛错 `4e62145`
- 修复缩略图：`creat_thumbnail_by_list()` 返回服务器绝对路径而非可访问 URL；tif/tga 等不支持格式触发致命错误（改为占位图 + 格式白名单 + GD 失败早返回） `3fe184d` `9f8abdb`
- 修复后端压缩：`imagecreatefromtiff/tga` 函数不存在、损坏图片 `getimagesize()` 返回 false 导致致命错误，现补格式/尺寸/函数存在性校验并如实返回失败 `9f8abdb`
- 修复统计与缓存：`date('YmdH')` 相减跨月导致缓存时长错误（改按 `total_time` 计算）、数组未初始化、缓存损坏时返回空结构、目录/句柄判空、累计值未重置、`mkdir` 权限 `9f8abdb`
- 修复 `config_path()` 使用相对路径 `mkdir`，CLI/部分 FCGI 下会创建到错误目录导致上传失败：落盘与 `chmod` 锚定 `APP_ROOT` `9f8abdb`
- 修复 `imgName()` 内嵌 helper 在同一请求多次调用时函数重复声明、`source` 模式未取 basename：helper 提升为全局函数并做清洗 `9f8abdb`
- 修复 `easyimage_delete()` 的 `clearstatcache()` 写在 `return` 之后从未执行 `3fe184d`
- 修复 `IP_URL_Ping()` 句柄未关闭、浮点超时不生效（`fsockopen` → `stream_socket_client`） `3fe184d`
- 修复 `arrayeval()` 对 bool/null/float 的隐式类型转换告警 `3fe184d` `9f8abdb`
- 修复公开查询 API：`public` 开关用 `=== 0` 严格比较导致字符串 `'0'` 判断失效；Windows 下 `disk_free_space('/')` 取不到磁盘空间（改 `__DIR__`）；`show` 入参类型校验 `9f8abdb`
- 修复 API 端 CORS/预检与入参：移除与 `Allow-Origin: *` 冲突的 `Allow-Credentials`、放行 OPTIONS 预检、token/name 数组入参不再触发 TypeError `9f8abdb`
- 修复广场与详情页：`?date=999999/99/99` 等任意值可递归创建大量目录（DoS），现严格校验真实日期并限制数量上限；文件名字段/URL 未转义、IP 参数未校验、JS 标题未 JSON 编码 `9f8abdb`
- 修复版本检查器：文件不存在仍调用 `filemtime()`、原始 JSON 被二次编码落盘、请求内重复读盘、全局 `error_reporting(0)` 副作用 `9f8abdb`
- 修复后台环境自检强制开启 `display_errors` 泄露绝对路径，以及 `parse_url()` 返回 false 时取下标的告警 `9f8abdb`
- 修复文件管理器（`admin/filer.php`）存储型 XSS 与符号链接越界：文件名/路径按 HTML 与 inline JS 分别转义，对已存在路径做 `realpath` 二次边界校验，禁止 SVG 在线新建/编辑 `9f8abdb` `b12cb67`
- 修复后台登出与文件管理登录逻辑：`admin/index.php` 注销改为登出图床会话，移除重复的 host 解析与未使用变量 `4e62145`
- 修复下载与输入类型：下载扩展名白名单统一转小写并去空格，路径/日期/验证码入参显式转字符串 `4e62145`

#### 性能优化

- 页面头部「广场」文件计数不再每次请求递归扫描整个存储目录，改为缓存化读取 `easyimage_cached_file_count()` `9f8abdb`
- `getFileNumber()` 改为目录迭代器实现，`get_file_by_glob()` 与 `app/total_files.php` 增加请求内缓存/记忆化 `3fe184d` `9f8abdb`
- 版本检查增加请求内缓存，关闭环境自检时不再发起网络请求 `9f8abdb`
- 「是否仍为出厂默认口令」的 bcrypt 判定结果按会话缓存（存储口令变化时才重算），避免每次进入后台设置页都跑一次 cost 12 的 bcrypt `941512d`

#### 兼容性调整

- 最低 PHP 版本由 5.6/7.0 提升至 7.4，官方支持范围 7.4 ~ 8.4；安装检查项、环境自检门槛与 README/docs 徽章及文案同步 `d2ffef2`
- PHP 8.5 兼容：`finfo_close()`/`curl_close()`/`imagedestroy()` 弃用统一加 `PHP_VERSION_ID < 80500` 判定，`exit(header())` 改为 `header(); exit;`，TimThumb 反引号改 `shell_exec()` 并判断函数可用性；涉及 `class.upload.php`、`class.version.php`、`bing.php`、`TimThumb.php`、`WaterMask.php`、`Zebra_Image.php`、`captcha.php`、`admin/manager.php`、`install/*` `9f8abdb`
- PHP 8.1+ 传 null 参数的 Deprecated 全面收口，并消除未定义变量/数组键、`pathinfo` 缺键、`foreach(false)` 等告警 `3fe184d` `4e62145`
- 口令存储格式变更：`config/config.php` 的 `password` 由 64 位十六进制无盐 SHA256 变为 `$2y$12$...` bcrypt 串，读取方（`urlHash()` 主密钥、默认口令检测）已同步适配；`config/config.guest.php` 移除出厂示例上传者账号（其遗留 SHA256 值升级后无效） `941512d`

#### 依赖更新

- ip2region 升级至 v3.17.0：`XdbSearcher.php` 替换为 `Searcher.class.php`，`ip2region.xdb` 数据切至 v3 结构（数据截至 2026-07-09），`Ip2Region.php` 兼容层适配新 API，后台与提示中的下载链接改指向 `ip2region_v4.xdb` `dcb9776`
- ECharts 5.4.1 → 6.1.0 `dcb9776`
- TinyFileManager 内置静态资源版本对齐：`bootstrap.min.css`（5.2.2）与 `bootstrap.min.js`（4.5.2）混用纠正为统一 Bootstrap 4.6.2，`jquery.slim` 3.4.1 → 3.5.1，补齐 `.map` 文件 `28b61de`
- 文档站静态资源 CDN 由 `cdn.bootcdn.net`/`unpkg.com` 切换为 `fastly.jsdelivr.net`，全部锁定具体版本并添加 SRI `integrity` + `crossorigin` 校验；`docs/sw.js` 缓存白名单同步新增 `fastly.jsdelivr.net` `3fe184d`

#### 代码结构

- `app/function.php` 严格等价重构（约 1360 行变更）：`??` 替代 isset 三元、`preg_grep` 改写目录遍历、比较符与 `htmlentities`/`real_ip` 保持原语义 `3fe184d`
- 移除 `public/static/EasyImage.js` 中无引用的 `isUrl()` 死代码 `3fe184d`
- 删除无引用死文件 `app/check_admin.inc.php`（早期前台环境自检提示块，全库无任何 `include`/`require` 引用点，且其「是否仍为出厂默认口令」判定用字串比较、bcrypt 化后永不命中；环境自检提示统一由 `admin/inc.check.php` 承担）（`941512d` 删除死函数 `checkEnv()` 后同批复核发现）
- 版本标识统一为 2.8.7：`APP_VERSION`（`app/base.php`）、`admin/version.php`、`config/config.php` 的 `update` 时间戳 `e0665f0` `3fe184d`
- `.gitignore` 忽略运行时产物与部署文件：`i/cache/`、`i/recycle/`、`i/2*/`、`config/install.lock`、`config/EasyIamge.lock`、本地审计报告，以及后台运行时日志目录 `admin/logs/`（登录日志、上传日志、IP 计数、统计缓存，均按需自动创建） `e337a2b` `dcb9776` `9f8abdb` `941512d`
- 口令散列逻辑收敛为 `easyimage_password_digest()` / `easyimage_password_hash()` / `easyimage_password_hash_plaintext()` / `easyimage_password_verify()` 四个辅助函数，安装器、后台改密、上传者增改、恢复页与 `_login()` 全部复用，避免多处实现不一致；同时删除无调用点的死函数 `checkEnv()`（其配置开关 `checkEnv` 仍由 `admin/inc.check.php` 使用） `941512d`

#### 升级注意

1. **旧的加密删除链接与源图保护链接全部失效**：`urlHash()` 不做向后兼容，解密失败即视为无效令牌。
2. 曾使用默认 `hide_key`（`EasyImage2.0`）的源图保护直链，会因 Key 被自动随机化而失效，需重新获取链接。
3. 登录态由 Cookie 迁移到服务端 Session，升级后所有已登录会话需重新登录，`auth` Cookie 不再下发。
4. 最低 PHP 版本提升至 7.4，PHP 5.6 ~ 7.3 站点须先升级 PHP，否则安装引导与环境检查不通过。
5. `config/config.php` 新增 `token_suffix_ID` 键：沿用 2.8.6 旧配置时该键缺失会触发 `Undefined array key`，且后台保存只接受已存在的配置键，需手动在 `config.php` 补 `'token_suffix_ID'=>1`。同为新增键的 `trusted_proxy` 以 `?? ''` 读取，旧配置缺失不影响运行，仅在需信任公网代理时补填。
6. ip2region 数据文件已切换为 v3 结构：若曾自行下载旧版 `app/ip2region/ip2region.xdb`，需按后台提示重新下载，否则 IP 定位查询失败。
7. 文件管理器能力收敛：仅能管理图片存储目录，在线编辑、打包下载、远程 URL 拉取均已关闭；`config/config.manager.php` 中的安全项会被 `admin/manager.php` 的强制覆盖块降级，手动改回无效。
8. `i/.htaccess`、`config/.htaccess` 与根目录 `.htaccess` 仅对 Apache 生效，Nginx/Caddy 需按 `docs/安全配置.md` 配置等价规则（目录脚本执行拒绝、SVG `CSP sandbox`、隐藏文件拒绝）。
9. 安装器仅在「无 `config/install.lock` 且存储口令仍为出厂默认」时可用，且不再允许以出厂默认口令 `admin@123` 完成安装；需要重装时先删除该锁文件并将口令恢复为默认，发行包不应携带它。
10. 出厂默认凭据仍为 `admin@123`（`config/config.php` 中的存储值已替换为其加盐 bcrypt 串），部署后必须立即改密；因 `urlHash()` 的 HKDF 主密钥取自管理员口令，改密会使旧的加密删除链接与源图保护链接同步失效（页面重新渲染的新链接不受影响）。
11. ECharts 升级至 6.x，统计页图表的默认主题色与图例样式可能与旧版存在视觉差异。
12. 鉴黄新增 `Hentai` 判定维度，同一 `checkImg_value` 阈值下被标记为可疑的图片比例会上升，请按实际效果调整阈值。
13. 分片上传依赖存储目录下的 `cache/` 子目录（缺失时自动创建），目录权限不足会导致分片合并失败。
14. 开放查询接口（`api/public.php`）开关判定改为严格 truthy：旧配置中 `public` 为字符串 `'0'` 的站点升级后接口会返回 403，需在后台重新开启一次。
15. 站点前方有公网反向代理/CDN（如 Cloudflare）时，需在后台 IP 名单区域填写 `trusted_proxy` 代理出口 IP，否则上传限额与 IP 名单将作用于代理集群而非真实访客。
16. **口令存储升级为加盐 bcrypt，不向后兼容**：升级后旧版以无盐 `SHA256` 存储的管理员与上传者口令全部失效（登录端不再接受遗留摘要），必须重置。推荐恢复路径：用发行包内的 `config/config.php` 覆盖当前文件（口令回到出厂默认）→ 用出厂默认口令 `admin@123` 登录后台 → 立即在「管理员设置」改为自定义强口令（8~64 位）；`app/reset_password.php` 的在线口令转换已收敛为仅登录管理员可用（防匿名滥用），重新走安装引导亦可。因删除令牌主密钥取自管理员口令，口令变更会使旧删除链接失效（页面重新渲染的新链接不受影响，与本文「加密链接不做向后兼容」一致）。

* 2025-03-25 v2.8.6
* feat: 添加svg矢量图，主要是广场预览 by @zhendery in https://github.com/icret/EasyImages2.0/pull/224
* 修改captcha.php中的一处笔误，会导致Nginx报错 by @HC3560 in https://github.com/icret/EasyImages2.0/pull/234


* 2024-06-14 v2.8.5
- 优化API示例
- 优化二级目录使用
- 修复对PHP8.2支持
- 上传返回状态统一
- 增加返回状态文档解释
- 增加通过文件md5禁止上传
- Fix [#114](https://github.com/icret/EasyImages2.0/issues/184)

* 2024-01-20 v2.8.4
- Fix [#163](https://github.com/icret/EasyImages2.0/pull/163)
- Fix [#159](https://github.com/icret/EasyImages2.0/pull/159)
- 增加上传至FTP
- 增加自定义时区
- 增加大文件分片上传
- 增加重置OPcache缓存
- 增加自定义NProgress颜色
- 优化-IP数据基于内存的查询
- 优化-删除类操作统一
- 优化-修改配置统一
- 优化-页面
- 更新组件

* 2023-05-30 v2.8.3
- FIX [#118](https://github.com/icret/EasyImages2.0/issues/118)

* 2023-05-14 v2.8.2
- 增加uPic教程
- Fix [#115](https://github.com/icret/EasyImages2.0/issues/115)
- Fix [#114](https://github.com/icret/EasyImages2.0/issues/114)

* 2023-04-12 v2.8.1
- 修改上传签名生成方式
- 修复一处绕过代码
- 修复部分会曝露网站路径的代码
- 增加检测cookie|Local storage

* 2023-04-05 v2.8.0
- 修复定时删除最小值不能为0
- 修复上传完毕后多次点击复制失效
- 修复两处会导致PHP5.6不兼容的代码
- 有条件的建议开启OPcache 增速明显

* 2023-03-11 v2.7.9
- 修复粘贴上传
- 修复安装检测

* 2023-03-11 v2.7.8
- 默认不再开启`环境自检`
- 增加前端上传签名
- 增加关闭后台设置
- 修改目录命名 `applicaton->app`
- 修改版本位置
- 修复漏洞

* 2023-03-05 v2.7.7
- 增加登录日志
- 修复备用文件管理登录失效

* 2023-03-04 v2.7.6
- 增加限定删除文件目录
- 增加对SVG文件格式过滤
- 修复开启验证码后绕过验证
- 修复弱类型验证导致的异常登录
- 修复上传历史中文件详细信息链接错误
- 更换加密删除链接算法 - 链接更短
- 更换存储密码算法为SHA256
- 默认不支持SVG文件上传
- 完全兼容 PHP5.6-8.0
- 对picgo/ShareX/盘络等第三方API增强兼容性
- 本次漏洞修复和代码增强感谢 [xulei1112](https://github.com/xulei1112)

* 2023-02-24 v2.7.5
- 增加定时删除文件
- 增加API上传返回Token ID
- 修复一处bug
- 修复局域网无法获取新版本时导致的页面混乱[#91](https://github.com/icret/EasyImages2.0/issues/91#issue-1585360193)
- 兼容非安全域名下的复制[#92](https://github.com/icret/EasyImages2.0/issues/92)
- 更换检测新版本方式
- 优化显示

* 2023-02-06 v2.7.4
- 优化和修复bugs
- 使用[docsify](https://docsify.js.org/#/zh-cn/)自建文档 地址 [Gitee](https://icret.gitee.io/easyimages2.0/#/) | [Gihub](https://icret.github.io/EasyImages2.0/#/) 或下载源码后直接访问`/docs/`

* 2023-02-05 v2.7.3
- 修复设置中手动压缩失败!
- 优化排版

* 2023-02-05 v2.7.2
- 增加删除单条历史上传记录
- 增加 [web-indexr](https://github.com/rehiy/web-indexr) 文件管理, 正在实验中
- - 作为 [Tinyfilemanager](https://github.com/prasathmani/tinyfilemanager) 的替代品 更轻量化 简单化
- 修复开启登录上传后,上传者账号上传失败 [#87](https://github.com/icret/EasyImages2.0/issues/87#issue-1569794639)
- 修复检测登录状态错误
- 优化排版和移动端显示

* 2023-02-01 v2.7.1
- 更新版本号

* 2023-02-01 v2.7.0
- 增加限制游客上传
- 增加上传历史记录
- 增加粘贴上传状态
- 增加广场非图片图标
- 增加前端显示缩略图链接
- 增加每日获取Bing图片背景
- 增加图片详细信息管理登录后显示更多信息
- 增加解析上传IP地址 (使用方法参考提示信息)
- 修复图片详细信息中随机图片排版混乱
- 替换访问生成缩略图代码
- 更新一些组件
- 调整前端显示
- 优化代码

* 2023-01-21 v2.6.9 Happy New Year
- 修复BMP格式无法增加水印(不建议开启,非常耗时)
- 修复上传用户组会串联变成管理员权限 [#61](https://github.com/icret/EasyImages2.0/issues/61)
- 增加了上传提示和登录提示
- 增加自定义存储分类路径 设置方法参考 [PHP date() 函数](https://www.runoob.com/php/php-date.html)
- 更改后台部分管理设置的位置
- 优化操作逻辑和信息提示


* 2022-12-30 v2.6.8
- 上传组件支持到 PHP 8.2
- 修复下载任意文件漏洞 [#75](https://github.com/icret/EasyImages2.0/issues/75)
- 增加自定义底部信息
- 增加自定义管理提示
- 增加图床模式
  - 危险：除图片外不验证文件是否正常，也代表可以上传任意指定格式!
- 增加上传时生成缩略图(仅设置直链缩略图时生效)，低配vps负载较大,影响前端上传速度!
- 其他一些优化

* 2022-09-20 v2.6.7
- 优化后台显示
- 增加控制缩略图尺寸
- 修复启用Opcache带来的问题  [#57](https://github.com/icret/EasyImages2.0/issues/57)

* 2022-07-10 v2.6.6
- 增加文件uuid命名
- 界面美化

* 2022-07-10 v2.6.6
- 增加图片信息显示随机图片
- 增加文件雪花命名
- 增加举报入口
- 更新组件

* 2022-05-27 v2.6.5
- 更改文件位置

* 2022-05-26 v2.6.4
- 更改版本显示方式

* 2022-05-26 v2.6.3
- 增加图片下载
- 增加简单暗黑模式
- 增加读取上传日志
- 增加广场浏览往日限制
  - 有助于防爬虫抓取
- 增加登陆验证码开关(默认关闭)
- 删除图片详情页Exif信息
- 文件管理中图片使用缩略图显示
- 更改广场->信息中图片长宽获取方式

- 优化图片详情页/广场UI/日志/用户/api 列表

* 2022-05-04 v2.6.2
- 增加API/用户文件浏览和删除
- 增加转换webp后也会添加水印 
  - 转换成webp后不建议开启水印,会增大图片体积
  - webp水印消耗更多PHP内存,PHP8以上有很大概率失败
- 更改广场页面返回顶部
- 优化了管理界面UI
- 修复一些bug

* 2022-05-03 v2.6.1
- 增加登陆用户有效期
- 增加管理员/登陆用户/Token专用目录
- 增加转换图片格式后压缩图片(不建议同时开启后端压缩)
- 增加隐藏存储路径(网站域名与图片域名不同且图片域名需绑定到图片目录)
- 直链缩略图支持bmp,webp
- 本次更新较大,建议重新安装!

* 2022-04-29 v2.6.0
- 修复源图缺陷
- 修复API回收不能还原问题
- 布局修改
- 登录页美化
- 屏蔽登陆页面chrome类浏览器自动填充

* 2022-04-02 v2.5.9
- 增加安装提示
- 增加忘记密码提示
- 增加检测水印图片/水印字体是否存在
- 修复登陆逻辑
- 修复广场重复显示图片
- 修改广场删除/回收样式
- 调整了广告的位置
- 调整后台设置分表

* 2022-04-02 v2.5.8
- 修复在PHP8环境下的bugs
- 修复删除token产生的bug
- 更改顶部广告位置
- 更改广场样式

* 2022-3-30 v2.5.7
- 增加弹窗公告
- 恢复文件管理
- 微调了广场样式
- 微调了删除文件
- 删除了base.php
- 日志记录上传IP端口
- 日志记录通过API上传的ID
- 修复管理页面短标签bug [#30](https://github.com/icret/EasyImages2.0/issues/30#issue-1185821542)
- 修复图片回收中批量删除失败
- 修复广场预览ico格式文件失败
- 修复个别浏览器显示二维遮住网页 [#28](https://github.com/icret/EasyImages2.0/issues/28#issue-1180675728)
- 缩略图最大生成与用户设置最大上传关联
- 安全检测中检测本地域名改为检测局域网

* 2022-3-13 v2.5.6
- 修复加密删除后不能正确提示
- 修复webp转换其他格式失败
- 修复文字水印透明度不生效
- 修复jscolor显示不正确
- 增加原图保护
- 增加检测版本

* 2022-3-4 v2.5.5
- 增加设置页面检测是否开启登录上传
- 将footer固定在底部
- 移除function_API.php
- 修复TimThumb不支持bmp格式的bug
- 修复TimThumb不支持webp动态图片bug

* 2022-2-29 v2.5.4
- 增加Token有效期
- 增加回收图片按钮
- 增加加密删除回收站
- 修复广场标题

* 2022-2-21 v2.5.3
- 增加图床数据开放
- 增加自定义服务条款
- 升级 Viewer.js 到 v1.10.4
- 将页面选择记录从cookie改为本地存储
- 修复实时生成缩略图导致的页面布局异常
- 优化显示代码
- 不出意外今年将只修复bug和兼容问题

* 2022-2-19 v2.5.2 
- 增加简繁体转换
- 增加管理页面记录当前操作页
- 修复一处暴露路径bug

* 2022-2-13 v2.5.1
- 增加异步执行鉴黄
- 取消检测imagick扩展
- 修复可能导致检测弹窗弹出失败

* 2022-2-7 v2.5.0
- 修复静态文件调用失败

* 2022-2-6 v2.4.9
- 修复静态文件引用

* 2022-2-6 v2.4.9
- 修复flash和silverlight路径引用(>IE9不影响)
- markdown html alt值改为源文件名
- 升级jquery-3.4.1至3.6.0
- 调整了静态文件位置

* 2022-2-5 v2.4.8
- 调整缩略图内存至128M
- 修复无可疑图片时显示错误
- 修复转换为webp时会复制一份bug
- 修复开启登录上传后无法上传的bug
- 插件检测的敏感信息转移到管理目录
- 增加安装时检测.user.ini
- 增加检测鉴黄接口是否可以正确访问
- 增加异步处理文件,上传完毕后处理速度变快了
- 增加 [nsfwjs](https://github.com/infinitered/nsfwjs) 接口方式检测违规图片
  - 作者测试时用的`docker`搭建 `docker`地址:[zengdawei/nsfw_restful_api
](https://hub.docker.com/r/zengdawei/nsfw_restful_api)
  - 使用注意 程序期望nsfwjs返回json 并且如下格式：
```json
[
    {
        "className": "Drawing",
        "probability": 0.824431836605072
    },
    {
        "className": "Hentai",
        "probability": 0.16360442340373993
    },
    {
        "className": "Neutral",
        "probability": 0.007620695047080517
    },
    {
        "className": "Porn",
        "probability": 0.004154415801167488
    },
    {
        "className": "Sexy",
        "probability": 0.00018858206749428064
    }
]
```
- 增加WordPress上大名鼎鼎的实时缩略图生成TimThumb
  - TimeThumb为本图床修改版,会缓存到缓存文件夹方便下次调用

* 2022-1-27 v2.4.7
- 优化页面排版
- 更改部分命名
- 增加后端压缩率
- 增加可以显示多条公告
- 增加上传后是否显示删除
- 增加可以关闭广场/统计导航|页面
- 调整登录和退出文件位置
- 调整二维码内容为每个页面
- 更换验证码库并不再区分大小写
- 修复一处有概率暴露图片绝对路径的bug

* 2022-1-22 v2.4.6
- 视图优化
- 删除重复内容
- 增加图片信息页面
- 增加上传黑/白名单
- 修复因关闭上传日志而导致的无法鉴黄和后端压缩图片
- 修复安装时更改管理员账号失败
- 修复更改管理员账户后无法退出

* 2022-1-13 v2.4.5
- 修复一处权限问题
- 修复恢复可疑图片
- 增加复制提示
- 增加默认上传后首选显示链接

* 2022-1-3 v2.4.5 beta
- 增加复制提示
- 更新安装代码
- 更改前端样式
- 更新上传格式
- 重构了密码验证
- 使用md5存储密码
- 增加后台设置提示
- 增加更改网站配色
- 增加缩略图索引格式
- 调整后台分类及位置
- 增加在线修改账号密码
- 增加以源文件名称命名
- 增加缩略图两种生成方式和开关
- 修复开启前端压缩导致的上传图片异常
- 屏蔽因缺少PHP扩展而不能生成缩略图的格式

* 2021-12-25 v2.4.4
- 更改favicon.ico
- 修复缩略图数量统计
- 增加缩略图生成开关
- 日志增加更多文件信息
- 前端增加裁剪和压缩质量
- 上传失败将会输出更多信息
- 修复前端压缩图片不能关闭问题
- 修复上传设置中错误和页面显示
- 调整网站设置->上传设置的排序
- 将快捷操作中心转移到网站设置中
- 修复因生成缩略图导致的前端数据返回失败
- 增加简单图床chrome浏览器插件，可自行配置网站->[EasyImage-Browser-Extension](https://github.com/icret/EasyImage-Browser-Extension)

* 2021-11-17 v2.4.3
- 增加登录验证码
- 二级目录安装
- 一些优化

* 2021-11-14 v2.4.2
- 增加上传日志

* 2021-11-12 v2.4.1
- 增加缓存周期配置
- 增加上传统计
- 增加viewjs
- 更新依赖件
- 修复统计错误

* 2021-11-9 v2.4.0
- 增加统计缓存
- 增加最近30天上传统计与占用空间图表
- 增加初始化安装（可能会不支持二级目录安装，可删除install文件夹初始化)
- 增加在线编辑配置(之前是需要修改config.php文件，现在可以直接网站端修改了)
- 删除广场会导致浏览速度变慢的代码
- 删除快捷配置会导致浏览速度变慢的代码

* 2021-11-3 v2.3.2
- 增加广场图片缓存
- 重构广场样式

* 2021-11-3 v2.3.1
- 增加监黄接口
- 增加审核违规图片
- 修复对php5.6的支持
- 修复二级目录的安装

* 2021-10-24 v2.3.0
- 将服务器环境监测改为第一次打开时自动检测（如需再次展示需删除config目录下的EasyImage.lock）
- 增加快捷操作中心显示服务信息
- 增加对上传文件的命名方式（详见config.php文件里的注释）
- 增加隐私政策、服务条款、DMCA
- 增加自定义静态文件CDN源
- 增加dns-prefetch
- 删除了tinyfilemanager文件管理（感觉没什么用）
- 一些bug得以修复

* 2021-5-22 v2.2.0
- 增加根目录静态属性
- 增加浏览页面懒加载
- 增加浏览页面启用选定日期查看图片
- 增加版本检测 ***每月10日06点和25日01点检测Github是否更新***
- 增加上传压缩 ***此压缩有可能使图片变大！特别是小图片 也有一定概率改变图片方向***
- 增加批量压缩目录 ***TinyPng或本机压缩，本机压缩出现的问题***
- 修复title
- 修复二级目录安装
- 修复对PHP5.6的兼容 ***建议使用php7.0及以上！***

* 2021-5-8 v2.1.1
- 修复上传界面上传失败提示信息bug
- 浏览页面重构
- 删除页面添加登录删除
- 调整首页显示
- 将调整图片长宽放置前端，减小资源开销
- 其他小调整

* 2021-5-2 v2.1
- 将tinyfilemanager配置文件简单翻译并集成到config.php
- 增加底部自定义信息
- 增加检测PHP环境，给与提示
- 增加删除图片url（服务器不会保存删除链接）
- 恢复随机浏览20张上传图片 可以设定浏览数量和关闭浏览
- - 随机浏览图片可以在线删除
- 可以使用 https://png.cm/libs/list.php?num=100 定义浏览数量
- 修复一些调用
- 更改二维码显示方式
- 开启api 需要token验证上传
- 重构并修复check.php相关文件
- 重构部分代码
- 更改目录结构
- 增加安全性配置
- * Apache配置文件默认设置上传目录不可运行 

```Apache
RewriteEngine on RewriteCond % !^$
RewriteRule i/(.*).(php)$ – [F]
RewriteRule public/(.*).(php)$ – [F]
RewriteRule config/(.*).(php)$ – [F]
```

- * Nginx请在Nginx配置：

```Nginx
 # 禁止运行php的目录
    location ~* ^/(i|public|config)/.*\.(php|php5)$
    {
     deny all;
    }
```
- - 或者参考：https://blog.png.cm/939.html
- 一些精简

* 2021-4-14 v2.0.2.1 Dev1
- 更新静态文件版本
- 请所有更新过2.0.2.1版本升级到此版本
- 更改一些描述
- md5提交登录验证
- 登录上传也显示公告

* 2021-03-28 v2.0.2.1
- 更新管理程序，修复部分漏洞
- 修复不能等比例缩小图片 
- 支持php8

* 2019-6-26 v2.0.2.0
- 精简压缩代码，使得不再压缩后反而变大
- 删除异域上传功能，不再支持异域上传
- 修复开启登录后无法粘贴密码
- 后台控制上传数量,上传格式
- 其他一些优化

* 2019-6-14 v2.0.1.9
- 增加复制链接按钮
- 增加暂停上传按钮
- 增加QQ截图，剪切板上传
- 增加文字/图片水印透明度
- 恢复开启/关闭api上传
- 恢复支持水印文字颜色
- 恢复支持远程上传图片
- 修复安装时候的权限
- 修复管理无法多选的问题
- 修复上传透明png背景变为纯黑的问题
- 修复成功上传图片但前端无法获取链接
- 修复在centos64 lnmp1.6 php7.1环境下的图片信息读取问题
- 修改图片压缩方式，速度更快，相比之前提高5倍以上
- 更改管理路径
- 更改上传路径，文件名更短
- 更改上传显示方式为缩略图
- 关闭添加图片后自动上传
- 纪念一下2019年，将版本号改为2.0.1.9

* 2019-5-23 v2.0
- 在继承上个版本（1.6.4）的基础上进行了全新优化
- 修复上传经常失败的问题
- 删除一些不常用但会增加功耗的过程
- 全新的压缩 将文件继续缩小
- 全新的目录系统，精简代码
- 设置仅允许在config.php修改，注释更加明了，即使没有代码基础也可以操作
- 增加新的文件管理系统，感谢 tinyfilemanager
- ~~支持文字/图片水印 可自定义文字颜色~~
- ~~支持文字水印背景颜色~~
- ~~支持文字水印透明度~~
- ~~支持删除远程上传文件~~ -> 不再支持删除远程文件
- ~~(支持开启/关闭api自定义文字水印)~~
- ~~支持删除自定义删除图片(仅管理员)~~

<details><summary>与1.6.4版本差别</summary>

##### 不建议再使用 [EasyImage 1.6.4版本](https://github.com/icret/easyImages)

- 在继承上个版本（[1.6.4](https://github.com/icret/easyImages "1.6.4")）的基础上进行了全新优化
- 修复上传经常失败的问题
- 删除一些不常用但会增加功耗的过程 （删除的在下边会有标记）
- 全新的压缩 将文件继续缩小
- 全新的目录系统，精简代码
- 设置仅允许在config.php修改，注释更加明了，即使没有代码基础也可以操作
- 增加新的文件管理系统，感谢 tinyfilemanager
- ~~支持文字/图片水印 可自定义文字颜色~~
- ~~支持文字水印背景颜色~~
- ~~支持文字水印透明度~~
- ~~支持删除远程上传文件~~ -> 不再支持删除远程文件
- ~~(支持开启/关闭api自定义文字水印)~~
- ~~支持删除自定义删除图片(仅管理员)~~

</details>

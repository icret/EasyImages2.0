#!/bin/bash
#
# easyimages-uploader.sh - A shell script to compress and upload images to an EasyImages instance.
#
# This script takes a local image file, compresses it into WebP format,
# and then uploads it to a specified EasyImages server. The resulting
# image URL (in Markdown format) is copied to the clipboard.
#
# Author:      Micraow Peng
# Email:       peng@pengs.top
# Homepage:    https://pengs.top
# License:     GPL-2.0
#

# --- User Configuration ---
# 请在使用前修改以下配置
# --------------------------

# [必须修改] 你的 EasyImages API 地址
API_URL="https://your.domain.com/api/index.php"

# [必须修改] 你的 EasyImages API Token
TOKEN="YOUR_API_TOKEN"

# [可选] 存放压缩后图片的本地目录 (脚本会自动创建)
# 使用 '~' 代表你的用户主目录
COMPRESSED_DIR="~/Pictures/Compressed_for_EasyImages"

# [可选] 图片处理参数
MAX_DIMENSION="2100" # 图片最长边的最大尺寸 (宽度或高度)
WEBP_QUALITY="80"    # WebP 格式的压缩质量 (1-100)

# --- Configuration End ---


# --- 辅助函数，用于格式化文件大小 ---
format_size() {
    local bytes=$1
    if (( bytes < 1024 )); then
        echo "${bytes}B"
    elif (( bytes < 1048576 )); then # 1024*1024
        printf "%.1fK\n" $(echo "scale=1; $bytes/1024" | bc)
    else
        printf "%.1fM\n" $(echo "scale=1; $bytes/1048576" | bc)
    fi
}
# --- 辅助函数结束 ---


# 检查必要命令
for cmd in magick curl jq bc notify-send; do
    if ! command -v $cmd &> /dev/null; then
        echo "错误: 命令 '$cmd' 未找到。请先安装。"
        echo "例如，在 Debian/Ubuntu 上: sudo apt install imagemagick curl jq bc libnotify-bin"
        exit 1
    fi
done

# 检查剪贴板工具 (优先使用 Wayland 环境的 wl-clipboard)
if ! command -v wl-copy &> /dev/null && ! command -v xclip &> /dev/null; then
    echo "错误: 未找到剪贴板工具。请安装 'wl-clipboard' (推荐) 或 'xclip'。"
    exit 1
fi

# 检查输入
if [ -z "$1" ]; then
    echo "用法: $0 <图片路径>"
    echo "例如: $0 my_screenshot.png"
    exit 1
fi
INPUT_IMAGE="$1"
if [ ! -f "$INPUT_IMAGE" ]; then
    echo "错误: 文件 '$INPUT_IMAGE' 不存在。"
    notify-send "上传失败" "文件 '$INPUT_IMAGE' 不存在" -i dialog-error
    exit 1
fi

# 检查占位符是否已被修改
if [[ "$API_URL" == "https://your.domain.com/api/index.php" ]] || [[ "$TOKEN" == "YOUR_API_TOKEN" ]]; then
    echo "错误: 请先修改脚本中的 API_URL 和 TOKEN 配置项。"
    notify-send "配置错误" "请先修改脚本中的 API_URL 和 TOKEN" -i dialog-warning
    exit 1
fi

# 准备路径和文件名
# 使用 eval 来解析路径中的 '~'
eval EXPANDED_COMPRESSED_DIR="$COMPRESSED_DIR"
mkdir -p "$EXPANDED_COMPRESSED_DIR"
FILENAME=$(basename -- "$INPUT_IMAGE")
BASENAME="${FILENAME%.*}"
OUTPUT_WEBP="$EXPANDED_COMPRESSED_DIR/${BASENAME}.webp"

echo "--- 开始处理图片 ---"
ORIGINAL_SIZE=$(stat -c%s "$INPUT_IMAGE")
ORIGINAL_SIZE_HR=$(format_size $ORIGINAL_SIZE)
echo "原始文件: $INPUT_IMAGE ($ORIGINAL_SIZE_HR)"

# 使用 magick 进行转换和压缩
echo "正在转换并压缩..."
magick "$INPUT_IMAGE" -resize "${MAX_DIMENSION}x${MAX_DIMENSION}>" -quality "$WEBP_QUALITY" "$OUTPUT_WEBP"

if [ $? -ne 0 ]; then
    echo "错误: 图片处理失败。"
    notify-send "上传失败" "使用ImageMagick处理图片时出错" -i dialog-error
    exit 1
fi

COMPRESSED_SIZE=$(stat -c%s "$OUTPUT_WEBP")
COMPRESSED_SIZE_HR=$(format_size $COMPRESSED_SIZE)
# 避免除以零的错误
if (( ORIGINAL_SIZE > 0 )); then
    PERCENTAGE=$(echo "scale=2; ($COMPRESSED_SIZE * 100) / $ORIGINAL_SIZE" | bc)
    printf "压缩后大小: %s (原始的 %.2f%%)\n" "$COMPRESSED_SIZE_HR" "$PERCENTAGE"
else
    printf "压缩后大小: %s\n" "$COMPRESSED_SIZE_HR"
fi

echo "--- 开始上传图片 ---"
RESPONSE=$(curl -s -X POST "$API_URL" -F "image=@$OUTPUT_WEBP" -F "token=$TOKEN")

# 处理上传结果
if echo "$RESPONSE" | jq -e '.result == "success"' > /dev/null; then
    UPLOADED_URL=$(echo "$RESPONSE" | jq -r '.url')
    MARKDOWN_LINK="![$BASENAME]($UPLOADED_URL)"

    # 复制到剪贴板 (Wayland环境使用 wl-copy)
    if command -v wl-copy &>/dev/null && [ -n "$WAYLAND_DISPLAY" ]; then
        echo -n "$MARKDOWN_LINK" | wl-copy
    elif command -v xclip &>/dev/null; then
        echo -n "$MARKDOWN_LINK" | xclip -selection clipboard
    fi

    # 输出到终端
    echo ""
    echo "✅ 上传成功！"
    echo "----------------------------------------"
    echo "图片链接: $UPLOADED_URL"
    echo "Markdown: $MARKDOWN_LINK"
    echo "----------------------------------------"
    echo "Markdown 链接已复制到剪贴板！"

    # 发送桌面通知
    NOTIFY_BODY="大小: $ORIGINAL_SIZE_HR → $COMPRESSED_SIZE_HR\n链接: $UPLOADED_URL\n\n(Markdown已复制)"
    notify-send "图片上传成功" "$NOTIFY_BODY" -i "network-transmit-receive"

else
    ERROR_MSG=$(echo "$RESPONSE" | jq -r '.result // "未知错误"')
    echo "❌ 上传失败。"
    echo "服务器返回信息: $RESPONSE"
    notify-send "图片上传失败" "服务器返回: $ERROR_MSG" -i "dialog-error"
    exit 1
fi


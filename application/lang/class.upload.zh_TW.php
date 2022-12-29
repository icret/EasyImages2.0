<?php
// +------------------------------------------------------------------------+
// | class.upload.zh_TW.php                                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Yang Chih-Wen 2009. All rights reserved.                 |
// | Version       0.28                                                     |
// | Last modified 15/08/2009                                               |
// | Email         chihwen.yang@gmail.com                                   |
// | Web           http://www.doubleservice.com/                            |
// +------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify   |
// | it under the terms of the GNU General Public License version 2 as      |
// | published by the Free Software Foundation.                             |
// |                                                                        |
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// |                                                                        |
// | You should have received a copy of the GNU General Public License      |
// | along with this program; if not, write to the                          |
// |   Free Software Foundation, Inc., 59 Temple Place, Suite 330,          |
// |   Boston, MA 02111-1307 USA                                            |
// |                                                                        |
// | Please give credit on sites that use class.upload and submit changes   |
// | of the script so other people can use them as well.                    |
// | This script is free to use, don't abuse.                               |
// +------------------------------------------------------------------------+

/**
 * Class upload Traditional Chinese translation
 *
 * @version   0.28
 * @author    Yang Chih-Wen (chihwen.yang@gmail.com)
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Yang Chih-Wen
 * @package   cmf
 * @subpackage external
 */

    $translation = array();
    $translation['file_error']                  = '檔案錯誤，請重試。';
    $translation['local_file_missing']          = '本地端的檔案不存在。';
    $translation['local_file_not_readable']     = '本地端的檔案不可讀取。';
    $translation['uploaded_too_big_ini']        = '檔案上傳出錯 (上傳的檔案超過了 php.ini 中 upload_max_filesize 指定的大小)。';
    $translation['uploaded_too_big_html']       = '檔案上傳出錯 (上傳的檔案超過了 HTML 表單中 MAX_FILE_SIZE 指定的大小)。';
    $translation['uploaded_partial']            = '檔案上傳出錯 (只有部份的檔案被上傳)。';
    $translation['uploaded_missing']            = '檔案上傳出錯 (沒有檔案被上傳)。';
    $translation['uploaded_no_tmp_dir']         = '檔案上傳出錯 (找不到暫存目錄)。';
    $translation['uploaded_cant_write']         = '檔案上傳出錯 (檔案寫入失敗)。';
    $translation['uploaded_err_extension']      = '檔案上傳出錯 (檔案上傳被 extension 中斷)。';
    $translation['uploaded_unknown']            = '檔案上傳出錯 (未知的錯誤)。';
    $translation['try_again']                   = '檔案上傳出錯，請重試。';
    $translation['file_too_big']                = '檔案太大了。';
    $translation['no_mime']                     = '未知的 MIME Type 檔案類型。';
    $translation['incorrect_file']              = '不正確的 MIME Type 檔案類型。';
    $translation['image_too_wide']              = '圖片寬度太大。';
    $translation['image_too_narrow']            = '圖片寬度太小。';
    $translation['image_too_high']              = '圖片高度太大。';
    $translation['image_too_short']             = '圖片高度太小。';
    $translation['ratio_too_high']              = '圖片寬高比率太大 (圖片寬度太大)。';
    $translation['ratio_too_low']               = '圖片寬高比率太小 (圖片高度太大)。';
    $translation['too_many_pixels']             = '圖片像素太多。';
    $translation['not_enough_pixels']           = '圖片像素太少。';
    $translation['file_not_uploaded']           = '檔案未上傳，無法繼續進行處理。';
    $translation['already_exists']              = '%s 已經存在，請更改檔名。';
    $translation['temp_file_missing']           = '暫存的原始檔案不正確，無法繼續進行處理。';
    $translation['source_missing']              = '已上傳的原始檔案不正確，無法繼續進行處理。';
    $translation['destination_dir']             = '無法創建目標目錄，無法繼續進行處理。';
    $translation['destination_dir_missing']     = '目標目錄不存在，無法繼續進行處理。';
    $translation['destination_path_not_dir']    = '目標路徑不是一個有效的目錄，無法繼續進行處理。';
    $translation['destination_dir_write']       = '目標目錄不能設定為可寫入，無法繼續進行處理。';
    $translation['destination_path_write']      = '目錄路徑不可寫入，無法繼續進行處理。';
    $translation['temp_file']                   = '不能創建暫存檔案，無法繼續進行處理。';
    $translation['source_not_readable']         = '原始檔案不可讀取，無法繼續進行處理。';
    $translation['no_create_support']           = '不支援 %s 創建功能。';
    $translation['create_error']                = '從原始檔案創建 %s 圖片過程中出錯。';
    $translation['source_invalid']              = '無法讀取原始圖片，請確認是否為正確的圖片檔？';
    $translation['gd_missing']                  = '無法使用 GD 函式庫。';
    $translation['watermark_no_create_support'] = '不支援 %s 創建功能，無法讀取浮水印。';
    $translation['watermark_create_error']      = '不支援 %s 讀取功能，無法創建浮水印。';
    $translation['watermark_invalid']           = '未知的圖片格式，無法讀取浮水印。';
    $translation['file_create']                 = '不支援 %s 創建功能。';
    $translation['no_conversion_type']          = '未定義的轉換類型。';
    $translation['copy_failed']                 = '在伺服端複製檔案時出錯，copy() 操作失敗。';
    $translation['reading_failed']              = '讀檔過程中出錯。';

<?php
// +------------------------------------------------------------------------+
// | class.upload.zh_CN.php                                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) caoshiwei 2008. All rights reserved.                     |
// | Version       0.25                                                     |
// | Last modified 09/29/2008                                               |
// | Email         caoshiwei@gmail.com                                      |
// | Web           http://www.hfut.edu.cn                                   |
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
 * Class upload Chinese translation
 *
 * @version   0.25
 * @author    Shiwei Cao (caoshiwei@gmail.com)
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Shiwei Cao
 * @package   cmf
 * @subpackage external
 */

    $translation = array();
    $translation['file_error']                  = '文件错误，请重试。';
    $translation['local_file_missing']          = '本地文件不存在。';
    $translation['local_file_not_readable']     = '本地文件不可读。';
    $translation['uploaded_too_big_ini']        = '文件上件出错 (上传的文件大小超过了php.ini中upload_max_filesize设置的大小)。';
    $translation['uploaded_too_big_html']       = '文件上件出错 (上传的文件大小超过了HTML 表单设置的大小)。';
    $translation['uploaded_partial']            = '文件上件出错 (上传的文件部分丢失)。';
    $translation['uploaded_missing']            = '文件上件出错 (上传文件丢失)。';
    $translation['uploaded_unknown']            = '文件上件出错 (未知错误).';
    $translation['try_again']                   = '文件上件出错。 请重试。';
    $translation['file_too_big']                = '文件太大。';
    $translation['no_mime']                     = '未知文件类型。';
    $translation['incorrect_file']              = '不正确的文件格式。';
    $translation['image_too_wide']              = '图片宽度太大。';
    $translation['image_too_narrow']            = '图片宽度太小。';
    $translation['image_too_high']              = '图片高度太大。';
    $translation['image_too_short']             = '图片高度太小。';
    $translation['ratio_too_high']              = '图片宽/高比率太高(图片宽度太大)。';
    $translation['ratio_too_low']               = '图片宽/高比率太低(图片高度太大).';
    $translation['too_many_pixels']             = '图片位数太高。';
    $translation['not_enough_pixels']           = '图片位数不够';
    $translation['file_not_uploaded']           = '文件未上传，不能进行处理。';
    $translation['already_exists']              = '%s 已经存在，请更换文件名。';
    $translation['temp_file_missing']           = '处理的(临时)源文件不正确，不能进行处理。';
    $translation['source_missing']              = '已上传的文件丢失，不能进行处理。';
    $translation['destination_dir']             = '目标文件目录不能被创建，不能进行处理。';
    $translation['destination_dir_missing']     = '目标文件目录不存在，不能进行处理。';
    $translation['destination_path_not_dir']    = '目录路径不是一个有效的目录，不能进行处理。';
    $translation['destination_dir_write']       = '不能让目标文件目录设置为可写的，不能进行处理。';
    $translation['destination_path_write']      = '目录路径是不可以写的，不能进行处理。';
    $translation['temp_file']                   = '不能创建临时文件，不能进行处理。';
    $translation['source_not_readable']         = '源文件不可以读，不能进行处理。';
    $translation['no_create_support']           = '%s 不支持创建';
    $translation['create_error']                = '从源文件创建 %s 图片过程中出错。';
    $translation['source_invalid']              = '无法读取原始图片，确认是不是正确的图片文件？';
    $translation['gd_missing']                  = 'GD 好像不可以使用。';
    $translation['watermark_no_create_support'] = '%s 创建不支持, 不能读取水印文件。';
    $translation['watermark_create_error']      = '%s 不支持读, 不能创建水印。';
    $translation['watermark_invalid']           = '未知文件格式, 无法读取水印文件。';
    $translation['file_create']                 = '%s 不支持创建。';
    $translation['no_conversion_type']          = '未定义转换类型';
    $translation['copy_failed']                 = '在服务器上复制文件时出错。 copy() 操作失败.';
    $translation['reading_failed']              = '读取过程中出错。';

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
 * @codepage  gb-2312
 * @author    Shiwei Cao (caoshiwei@gmail.com)
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Shiwei Cao
 * @package   cmf
 * @subpackage external
 */

    $translation = array();
    $translation['file_error']                  = '�ļ������������ԡ�';
    $translation['local_file_missing']          = '�����ļ������ڡ�';
    $translation['local_file_not_readable']     = '�����ļ����ɶ��';
    $translation['uploaded_too_big_ini']        = '�ļ��ϼ����� (�ϴ����ļ���С������php.ini��upload_max_filesize���õĴ�С)��';
    $translation['uploaded_too_big_html']       = '�ļ��ϼ����� (�ϴ����ļ���С������HTML �����õĴ�С)��';
    $translation['uploaded_partial']            = '�ļ��ϼ����� (�ϴ����ļ����ֶ�ʧ)��';
    $translation['uploaded_missing']            = '�ļ��ϼ����� (�ϴ��ļ���ʧ)��';
    $translation['uploaded_unknown']            = '�ļ��ϼ����� (δ֪����).';
    $translation['try_again']                   = '�ļ��ϼ����� �����ԡ�';
    $translation['file_too_big']                = '�ļ�̫����';
    $translation['no_mime']                     = 'δ֪�ļ����͡�';
    $translation['incorrect_file']              = '����ȷ���ļ���ʽ��';
    $translation['image_too_wide']              = 'ͼƬ����̫����';
    $translation['image_too_narrow']            = 'ͼƬ����̫С��';
    $translation['image_too_high']              = 'ͼƬ�߶�̫����';
    $translation['image_too_short']             = 'ͼƬ�߶�̫С��';
    $translation['ratio_too_high']              = 'ͼƬ��/�߱���̫��(ͼƬ����̫��)��';
    $translation['ratio_too_low']               = 'ͼƬ��/�߱���̫��(ͼƬ�߶�̫��).';
    $translation['too_many_pixels']             = 'ͼƬλ��̫�ߡ�';
    $translation['not_enough_pixels']           = 'ͼƬλ������';
    $translation['file_not_uploaded']           = '�ļ�δ�ϴ������ܽ��д���';
    $translation['already_exists']              = '%s �Ѿ����ڣ��������ļ�����';
    $translation['temp_file_missing']           = '������(��ʱ)Դ�ļ�����ȷ�����ܽ��д���';
    $translation['source_missing']              = '���ϴ����ļ���ʧ�����ܽ��д���';
    $translation['destination_dir']             = 'Ŀ���ļ�Ŀ¼���ܱ����������ܽ��д���';
    $translation['destination_dir_missing']     = 'Ŀ���ļ�Ŀ¼�����ڣ����ܽ��д���';
    $translation['destination_path_not_dir']    = 'Ŀ¼·������һ����Ч��Ŀ¼�����ܽ��д���';
    $translation['destination_dir_write']       = '������Ŀ���ļ�Ŀ¼����Ϊ��д�ģ����ܽ��д���';
    $translation['destination_path_write']      = 'Ŀ¼·���ǲ�����д�ģ����ܽ��д���';
    $translation['temp_file']                   = '���ܴ�����ʱ�ļ������ܽ��д���';
    $translation['source_not_readable']         = 'Դ�ļ������Զ�����ܽ��д���';
    $translation['no_create_support']           = '%s ��֧�ִ���';
    $translation['create_error']                = '��Դ�ļ����� %s ͼƬ�����г���';
    $translation['source_invalid']              = '�޷���ȡԭʼͼƬ��ȷ���ǲ�����ȷ��ͼƬ�ļ���';
    $translation['gd_missing']                  = 'GD ���񲻿���ʹ�á�';
    $translation['watermark_no_create_support'] = '%s ������֧��, ���ܶ�ȡˮӡ�ļ���';
    $translation['watermark_create_error']      = '%s ��֧�ֶ�, ���ܴ���ˮӡ��';
    $translation['watermark_invalid']           = 'δ֪�ļ���ʽ, �޷���ȡˮӡ�ļ���';
    $translation['file_create']                 = '%s ��֧�ִ�����';
    $translation['no_conversion_type']          = 'δ����ת������';
    $translation['copy_failed']                 = '�ڷ������ϸ����ļ�ʱ���� copy() ����ʧ��.';
    $translation['reading_failed']              = '��ȡ�����г���';

?>
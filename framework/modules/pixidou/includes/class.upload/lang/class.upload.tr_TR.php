<?php
// +------------------------------------------------------------------------+
// | class.upload.tr_TR.php                                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Volkan Metin 2008. All rights reserved.                  |
// | Version       0.25                                                     |
// | Last modified 19/01/2008                                               |
// | Email         metinsoft@gmail.com                                      |
// | Web           http://www.metinsoft.com                                 |
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
 * Class upload Turkish translation
 *
 * @version   0.25
 * @author    Volkan Metin (metinsoft@gmail.com)
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Volkan Metin
 * @package   cmf
 * @subpackage external
 */

    $translation = array();
    $translation['file_error']                  = 'Hata oluþtu. Lütfen tekrar deneyiniz.';
    $translation['local_file_missing']          = 'Dosya bulunamadý.';
    $translation['local_file_not_readable']     = 'Dosya okunamadý.';
    $translation['uploaded_too_big_ini']        = 'Hata oluþtu (izin verilen boyuttan büyük dosya yüklemezsiniz. Ancak php.ini dosyasýndan upload_max_filesize deðerini yükselterek tekrar deneyebilirsiniz.).';
    $translation['uploaded_too_big_html']       = 'Hata oluþtu (sayfanýzda belirttiðiniz MAX_FILE_SIZE boyutundan büyük bir dosya yüklemezsiniz.).';
    $translation['uploaded_partial']            = 'Hata oluþtu (dosyanýn sadece bir kýsmý yüklenebildi).';
    $translation['uploaded_missing']            = 'Hata oluþtu (dosya seçilmemiþ).';
    $translation['uploaded_unknown']            = 'Hata oluþtu (hata tesbit edilemedi).';
    $translation['try_again']                   = 'Hata oluþtu. Lütfen tekrar deneyiniz.';
    $translation['file_too_big']                = 'Dosya izin verilenden büyük.';
    $translation['no_mime']                     = 'Dosya türü bulunamadý.';
    $translation['incorrect_file']              = 'Bu dosyanýn uzantýsý geçersiz.';
    $translation['image_too_wide']              = 'Resim izin verilenden çok geniþ.';
    $translation['image_too_narrow']            = 'Resim izin verilenden çok dar.';
    $translation['image_too_high']              = 'Resim izin verilenden çok uzun.';
    $translation['image_too_short']             = 'Resim izin verilenden çok kýsa.';
    $translation['ratio_too_high']              = 'Resim oraný çok yüksek (resim çok geniþ).';
    $translation['ratio_too_low']               = 'Resim oraný çok düþük (resim çok uzun).';
    $translation['too_many_pixels']             = 'Resim izin verilenden büyük.';
    $translation['not_enough_pixels']           = 'Resim izin verilenden küçük.';
    $translation['file_not_uploaded']           = 'Dosya yüklenemedi. Ýþlem sonlandýrýldý.';
    $translation['already_exists']              = '%s dosyasý zaten var. Lütfen dosyanýzýn ismini deðiþtirerek tekrar deneyiniz.';
    $translation['temp_file_missing']           = 'Temp dizini doðru belirtilmemiþ. Ýþlem sonlandýrýldý.';
    $translation['source_missing']              = 'Dosyanýzýn içeriðinde izin vermeyen unsurlar var. Ýþlem sonlandýrýldý.';
    $translation['destination_dir']             = 'Dosyalarýn yükleneceði dizin oluþturulamadý. Ýþlem sonlandýrýldý.';
    $translation['destination_dir_missing']     = 'Dosyalarýn yükleneceði dizin oluþturulmamýþ. Ýþlem sonlandýrýldý.';
    $translation['destination_path_not_dir']    = 'Dosyalarýn yükleneceði adres bir dizin deðil. Ýþlem sonlandýrýldý.';
    $translation['destination_dir_write']       = 'Dosyalarýn yükleneceði dizinin yazma izinlerinde(CHMOD) problem var. Ýþlem sonlandýrýldý.';
    $translation['destination_path_write']      = 'Dosyalarýn yükleneceði adresin yazma izinlerinde(CHMOD) problem var. Ýþlem sonlandýrýldý.';
    $translation['temp_file']                   = 'Geçici dizine(temp) yazýlamýyor. Ýzinleri kontrol etmelisiniz. Ýþlem sonlandýrýldý.';
    $translation['source_not_readable']         = 'Dosyanýn içeriði okunamadý. Ýþlem sonlandýrýldý.';
    $translation['no_create_support']           = '%s dosyasý oluþturulamadý.';
    $translation['create_error']                = 'Kaynaktan %s resmi oluþturulurken hata oluþtu.';
    $translation['source_invalid']              = 'Resim dosyasý okunamadý. Dosyanýn bir resim olduðundan emin misiniz?';
    $translation['gd_missing']                  = 'Ýþleme devam edemiyorsunuz. Bu sunucunun GD kütüphanesine ihtiyacý var.';
    $translation['watermark_no_create_support'] = '%s resmi oluþturulamadýðý için filigran oluþturulamadý.';
    $translation['watermark_create_error']      = '%s resmi okunamadýðý için filigran oluþturulamadý.';
    $translation['watermark_invalid']           = 'Bilinmeyen dosya türü. Filigran oluþturulamadý.';
    $translation['file_create']                 = '%s dosyasý oluþturulamadý.';
    $translation['no_conversion_type']          = 'Belirtilen dosya türü dönüþtürülemedi.';
    $translation['copy_failed']                 = 'Dosya kopyalanýrken hata oluþtu. copy() iþlemi baþarýsýz.';
    $translation['reading_failed']              = 'Dosya okunurken hata oluþtu.';   
        
?>
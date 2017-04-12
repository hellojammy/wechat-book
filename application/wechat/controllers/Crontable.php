<?php

/**
 *
 * create at 16/09/22
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class Crontable extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $code = $this->input->get('code');
        if($code != 'olS2OjibAGSDhYypjCCNuI'){
            die('error code');
        }
    }

    /**
     * 下载图片到指定目录
     */
    public function download_wx_images(){
        try {
            $this->load->model('M_wx_img');
            $r = $this->M_wx_img->get_imgs_by_status(PIC_STATUS_UPLOAD);
            log_message('debug', '有' . count($r) . '张图片需要下载');
            if (count($r) == 0) {
                log_message('debug', '没有需要下载的图片' );
                return;
            }
            $this->load->service('s_anynote_img');
            $base_path = '/var/www/html/static/wechat';
            $suc_pic_id = array();
            $fail_pic_id = array();
            foreach ($r as $kv) {
                $flag = TRUE;
                $topic_base_path_0 = $base_path . DIRECTORY_SEPARATOR . $kv["open_id"] . DIRECTORY_SEPARATOR . '0';
                if ($this->mkdirs($topic_base_path_0)) {
                    $pic_name = $this->s_anynote_img->format_pic_name($kv);
                    $save_path = $topic_base_path_0 . DIRECTORY_SEPARATOR . $pic_name;
                    if ($this->download_pic($kv["url"], $save_path) < 0) {
                        $flag = FALSE;
                    }
                    //下载缩略图
                    $topic_base_path_300 = $base_path . DIRECTORY_SEPARATOR . $kv["open_id"] . DIRECTORY_SEPARATOR . '300';
                    if ($this->mkdirs($topic_base_path_300)) {
                        $thumbnail_path = preg_replace("/\d+$/", "300", $kv["url"]);
                        $save_path = $topic_base_path_300 . DIRECTORY_SEPARATOR . $pic_name;
                        if ($this->download_pic($thumbnail_path, $save_path) < 0) {
                            $flag = FALSE;
                        }
                        if ($flag) {
                            $suc_pic_id[] = $kv["id"];
                        }
                    }
                    if (!$flag) {
                        $fail_pic_id[] = $kv["id"];
                    }
                } else {
                    log_message('error', '创建目录失败:' .$topic_base_path_0 );
                }
            }

            $this->load->model('M_wx_img');
            //设置下载成功的图片状态
            if(count($suc_pic_id) > 0){
                $this->M_wx_img->set_image_status($suc_pic_id, PIC_STATUS_DOWNLOAD_OK);
            }
            //设置下载失败的图片状态
            if(count($fail_pic_id) > 0){
                $this->M_wx_img->set_image_status($fail_pic_id, PIC_STATUS_DOWNLOAD_FAIL);
            }
        } catch (Exception $exc) {
        }
    }

    /**
     * 新建目录
     * @param $dir
     * @param int $mode
     * @return bool
     */
    function mkdirs($dir, $mode = 0777) {
        if (is_dir($dir) || @mkdir($dir, $mode))
            return TRUE;
        if (!$this->mkdirs(dirname($dir), $mode))
            return FALSE;
        return @mkdir($dir, $mode);
    }

    /**
     * 下载图片
     * @param $src_path
     * @param $dest_path
     * @return int
     */
    private function download_pic($src_path, $dest_path) {
        try {
            if(file_exists($dest_path)){
                return 1;
            }
            $start_time = microtime(true);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $src_path);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $file = curl_exec($ch);
            curl_close($ch);
            if ($file) {
                $file_size = file_put_contents($dest_path, $file);
                $end_time = microtime(true);
                log_message('debug', "path:{$dest_path}. 耗时: " . ($end_time - $start_time) . '(s)');
                return $file_size > 0 ? 1 : -3;
            } else {
                log_message('error', '获取图片失败: ' . $src_path);
                return -1;
            }
        } catch (Exception $exc) {
            log_message('error', '下载图片出错: ' . $src_path . ',error:' . $exc->__toString());
            return -2;
        }
    }
}
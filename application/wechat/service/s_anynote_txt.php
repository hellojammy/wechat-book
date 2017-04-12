<?php

/**
 *
 * create at 16/09/19
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class S_anynote_txt extends CI_Service{

    /**
     * @param $content
     * @param $msg_id
     * @param $open_id
     * @return bool|string
     */
    public function topic($content, $msg_id, $open_id){
        //判断是否添加一个主题
        preg_match(ANYNOTE_SUBJECT_A, $content, $match);
        $cmd = isset($match[1]) ? $match[1] : '';
        $key = isset($match[2]) ? trim($match[2]) : '';
        if($cmd && $key){
            $key = str_ireplace('#', '', $key);
            $this->load->model('M_talk');
            $save_data = array(
                'msg_id' => $msg_id,
                'open_id' => $open_id,
                'topic' => $key,
                'content' => $cmd
            );
            $ret = $this->M_talk->save_entry($save_data);
            if($ret > 0){
                $_SESSION['key_anynote_text_msgid'] = $msg_id;
            }
            return ($ret >= 0 ? "/:,@-D成功添加到#{$key}#\n" . '回复图片即可添加到该主题' : "添加到#{$key}#失败") ;
        }

        //判断是否获取一个主题下的东西
        preg_match(ANYNOTE_RANDWORDS, $content, $match);
        $cmd = isset($match[1]) ? $match[1] : '';
        $key = isset($match[2]) ? trim($match[2]) : '';
        if($key && $cmd){
            $key = str_ireplace('#', '', $key);
            if($key == '排行榜'){
                return $this->get_ranking($open_id);
            }
            return $this->get_rand_words($key, $open_id);
        }

        return false;
    }

    /**
     * 获取指定主题下的主题回复
     * @param $topic
     * @param string $open_id
     * @return string
     */
    private function get_rand_words($topic, $open_id = ''){
        $rand_count = rand(1, 999);
        $this->load->model('M_talk');
        $rand_data = $this->M_talk->get_rand_words($topic, $rand_count, $open_id);
        $count = count($rand_data);
        if($count == 0){
            $add_topic_tips = "你还没有添加#" . $topic ."#主题\n\n";
            $add_topic_tips .= "添加方式 : 主题文字#主题名称#\n\n";
            $add_topic_tips .= "举个栗子 今天下班好早#生活点滴#，则可添加到#生活点滴#主题中\n\n";
            return $add_topic_tips;
        }
        $text = '';
        $index = 0;
        $this->load->library('wechat/wechatools');
        foreach ($rand_data as $kv) {
            $index++;
            $t = "\n\n[{$index}] " . date("Y.m.d", strtotime($kv["ctime"])) . "   " .  $kv["content"]  . "#{$topic}#\n";
            if($kv['imgIds'] != '1'){
                $preview_img_url = base_url('anynote/preview_images');
                $t .= count(explode(',', $kv['img_ids'])) . "张图 " . '<a href="' . "{$preview_img_url}/?t_id=" . $kv['id'] .'&open_id=' . $kv['open_id'] .'">点击查看</a>';

            }
            if(!Wechatools::maxLen($text, $t)){
                $text .= $t;
            }else{
                break;
            }
        }

        return "#{$topic}#({$index}个)\n{$text}";

    }

    /**
     * 获取主题排行榜
     * @param string $open_id
     * @return string
     */
    private function get_ranking($open_id = ''){
        $ranking_data = $this->get_ranking_stat($open_id);
        $count = count($ranking_data);
        if(empty($ranking_data)){
            return '抱歉，没有';
        }
        $text = "共{$count}个话题";
        $index = 0;
        foreach ($ranking_data as $kv) {
            $index++;
            $text .= "\n\n[{$index}]#" . $kv["topic"]  . "# " . $kv["count"] . "个";
        }
        return $text;
    }

    /**
     * 获取主题统计信息
     * @param $open_id
     * @return mixed
     */
    private function get_ranking_stat($open_id){
        $this->load->model('M_talk');
        return $this->M_talk->get_ranking_stat($open_id);
    }

}
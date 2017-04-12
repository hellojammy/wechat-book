<section class="container">
    <?php foreach($img_data as $item): ?>
        <div><a href="http://static.hello1010.com/wechat/<?php echo $item['open_id'] ?>/0/<?php echo date("Y_m_d_His", strtotime($item["ctime"])) . "_" . $item["id"] . ".jpg" ?>" target='_blank'><img style='width:100%' src="http://static.hello1010.com/wechat/<?php echo $item['open_id'] ?>/0/<?php echo date("Y_m_d_His", strtotime($item["ctime"])) . "_" . $item["id"] . ".jpg" ?>" /></a></div>
    <?php endforeach; ?>
</section>
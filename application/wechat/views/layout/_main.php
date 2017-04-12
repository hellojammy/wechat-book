<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <meta content="" name="pgv">
    <title><?php echo $head_title ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $app_path ?>resource/css/weui/weui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $app_path ?>resource/css/weui/example.css">
    <script type="text/javascript">
        var res_version = "<?php echo $deploy_ver ?>";
        var appPath = '<?php echo $app_path ?>';
    </script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js?v=20161204"></script>
    <script type="text/javascript" src="<?php echo $app_path ?>resource/js/main.js"></script>
</head>
<body>
<div id="main">
    <?php echo $content ?>
</div>
</body>
<!--<script type="text/javascript" src="--><?php //echo $app_path ?><!--resource/js/weui/weui.min.js"></script>-->
</html>
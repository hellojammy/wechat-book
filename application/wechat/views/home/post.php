<style type="text/css">
    #content{
        padding: 20px;
        font-size: 14px;
        color: #333;
    }

    #content img{
        width: 100%;
    }
</style>
<div id="content">
    <p>
        这是一个使用微信JS-SDK的示例,主要讲解分享接口,图片预览接口.
    </p>
    <p>
        这是第1张图片
    </p>
    <img src="<?php echo $app_path; ?>resource/images/demo/admin-bg-1.jpg" />
    <p>
        这是第2张图片
    </p>
    <img src="<?php echo $app_path; ?>resource/images/demo/admin-bg-2.jpg" />
    <p>
        这是第3张图片
    </p>
    <img src="<?php echo $app_path; ?>resource/images/demo/admin-bg-3.jpg" />
    <p>
        这是第4张图片
    </p>
    <img src="<?php echo $app_path; ?>resource/images/demo/audi-rs7-05.jpg" />
</div>

<script type="text/javascript">

    /**
     * 是否为微信客户端
     * @returns {boolean}
     */
    function isInWeixinApp() {
        return /MicroMessenger/.test(navigator.userAgent);
    }
    var sign = '<?php echo $sign ;?>';
    var share = '<?php echo isset($share) ? $share : '';?>';
    if(isInWeixinApp() && window.sign != 'undefined'){
        var signPackage = JSON.parse(window.sign);
        window.PREVIEWIMAGEARRAY = [];

        //配置文件注入
        wx.config({
            debug: false,
            appId: signPackage.appId,
            timestamp: signPackage.timestamp,
            nonceStr: signPackage.nonceStr,
            signature: signPackage.signature,
            jsApiList: ['onMenuShareAppMessage','onMenuShareTimeline','onMenuShareQQ','previewImage']
        });

        //在异步回调中实现业务代码
        wx.ready(function() {
            try{
                var shareInfo = JSON.parse(window.share);
            }catch(e){
                console.log(e.message);
            }
            //图片大图预览
            $('#content img').on('click' ,function(event) {
                var curImageSrc = $(this).attr('src');
                if (curImageSrc) {
                    if(window.PREVIEWIMAGEARRAY.length == 0){
                        $('#content img').each(function(index, el) {
                            var itemSrc = $(this).attr('src');
                            window.PREVIEWIMAGEARRAY.push(itemSrc);
                        });
                    }
                    wx.previewImage({
                        current: curImageSrc,
                        urls: window.PREVIEWIMAGEARRAY
                    });
                }
            });

            //微信消息分享
            wx.onMenuShareAppMessage({
                title: shareInfo.shareTitle,
                desc: shareInfo.shareDesc,
                link: shareInfo.shareLink,
                imgUrl: 'http://7xq01x.com1.z0.glb.clouddn.com/admin-bg-3.jpg', // 分享图标
                type: 'link',
                dataUrl: '',
                success: function () {
                },
                cancel: function () {
                }
            });

            //分享到朋友圈
            wx.onMenuShareTimeline({
                title: shareInfo.shareTitle,
                link: shareInfo.shareLink,
                imgUrl: 'http://7xq01x.com1.z0.glb.clouddn.com/admin-bg-3.jpg', // 分享图标
                success: function () {
                },
                cancel: function () {
                }
            });

            //分享到手机QQ
            wx.onMenuShareQQ({
                title: shareInfo.shareTitle,
                desc: shareInfo.shareDesc,
                link: shareInfo.shareLink,
                imgUrl: 'http://7xq01x.com1.z0.glb.clouddn.com/admin-bg-3.jpg', // 分享图标
                success: function () {
                },
                cancel: function () {
                }
            });

            //打印错误日志
            wx.error(function(res) {
                console.error(res.errMsg);
                alert(res.errMsg)
            });

        });
    }
</script>
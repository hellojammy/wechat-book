
<form enctype="multipart/form-data" method="post"  onsubmit="return check_form();">
    <div class="weui-cells">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" id="mobile_phone" name="mobile_phone" placeholder="请输入手机号">
            </div>
        </div>
    </div>

    <div class="weui-btn-area">
        <input type="submit" class="weui-btn weui-btn_primary" value="绑定账号" />
    </div>
</form>

<script type="text/javascript">
    function check_form(){
        if($('#mobile_phone').val() == ''){
            alert('请填写手机号');
            return false;
        }
        return true;
    }
</script>
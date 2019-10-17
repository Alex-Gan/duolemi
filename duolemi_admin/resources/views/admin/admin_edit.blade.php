<!--加载头部-->
@include('layous.header')

<meta name="X-CSRF-TOKEN" content="{{csrf_token()}}">
<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
<!--[if lt IE 9]>
<script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
<script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<body>
<div class="x-body">

    <form class="layui-form">
        {{csrf_field()}}
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                用户名
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <input type="text" id="username" name="username" value="{{$username}}" required="" lay-verify="required" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                昵称
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <input type="text" id="nickname" name="nickname" value="{{$nickname}}" required="" lay-verify="required|nickname" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                密码
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <input type="password" id="password" name="password" value="" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                确认密码
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <input type="password" id="confirmPassword" name="confirmPassword" value="" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label">
            </label>
            <button type="button"  class="layui-btn" lay-filter="modify" lay-submit="">
                修改
            </button>
        </div>
    </form>
</div>
<script>
    //获取token的值
    var _token = $("input[name=_token]").val();


    layui.use(['form','layer'], function(){

        $ = layui.jquery;
        var form = layui.form
            ,layer = layui.layer

        //监听提交
        form.on('submit(modify)', function(data) {
            var id = "{{$id}}";
            var param_data = data.field;
            param_data._token = _token;
            var load = layer.load();

            //密码
            var password = param_data.password;
            var confirmPassword = param_data.confirmPassword;

            if (password != '') {
                if (password.length < 6) {
                    layer.close(load);
                    layer.msg('密码必须6位以上', {icon: 2, time: 1500});
                    return false;
                }

                if (password != confirmPassword) {
                    layer.close(load);
                    layer.msg('两次密码不一致', {icon: 2, time: 1500});
                    return false;
                }
            }

            $.ajax({
                url: "/admin/admin/edit/"+id,
                data: param_data,
                type: "PUT",
                dataType: "json",
                success:function(res) {
                    layer.close(load);

                    if (res.code == 0) {
                        var index = parent.layer.getFrameIndex(window.name);//获取当前弹出层的层级

                        setTimeout(function () {
                            layer.msg('编辑账号成功!', {icon: 1, time: 1500});
                        }, 500);

                        setTimeout(function () {
                            window.parent.location.reload();//刷新父页面
                            parent.layer.close(index);//关闭弹出层
                        }, 2000);
                    } else {
                        layer.msg(res.msg, {icon: 2, time: 1500});
                        return false;
                    }
                },
                error:function(data){
                    $.messager.alert('错误',data.msg);
                }
            });
        });
    });
</script>
</body>
</html>
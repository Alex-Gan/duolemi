<!doctype html>
<html  class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>多乐米推广管理登录</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="{{asset('backend/css/font.css')}}">
    <link rel="stylesheet" href="{{asset('backend/css/xadmin.css')}}">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{asset('backend/lib/layui/layui.js')}}" charset="utf-8"></script>
    <script type="text/javascript" src="{{asset('backend/js/xadmin.js')}}"></script>
    <script type="text/javascript" src="{{asset('backend/js/cookie.js')}}"></script>

</head>
<body class="login-bg">

<div class="login layui-anim layui-anim-up">
    <div class="message">多乐米推广管理登录</div>
    <div id="darkbannerwrap"></div>

    <form method="post" class="layui-form" >
        {{csrf_field()}}
        <input name="username" placeholder="用户名"  type="text" lay-verify="required" class="layui-input" >
        <hr class="hr15">
        <input name="password" lay-verify="required" placeholder="密码"  type="password" class="layui-input">
        <hr class="hr15">
        <input value="登录" lay-submit lay-filter="login" style="width:100%;" type="submit">
        <hr class="hr20" >
    </form>
</div>

<script>
    $(function  () {
        layui.use('form', function(){
            var form = layui.form;

            //监听提交
            form.on('submit(login)', function(data){
                var data = data.field;
                var index = layer.load();

                $.ajax({
                    url: "/admin/login",
                    data: data,
                    type: "POST",
                    dataType: "json",
                    success:function(res) {
                        layer.close(index);
                        if (res.code == 0) {
                            layer.msg(res.msg, {icon: 1, time: 1500});
                            setInterval(function () {
                                location.href = '/admin/index'
                            }, 1000);
                        } else {
                            layer.msg(res.msg, {icon: 2, time: 1500});
                            return false;
                        }
                    },
                    error:function(data){
                        $.messager.alert('错误',data.msg);
                    }
                });
                return false;
            });
        });
    });
</script>
</body>
</html>
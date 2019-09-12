<!--加载头部-->
@include('layous.header')

<body>
<!-- 顶部开始 -->
<div class="container">
    <div class="logo"><a href="/admin/index">多乐米</a></div>
    <div class="left_open">
        <i title="展开左侧栏" class="iconfont">&#xe699;</i>
    </div>
    <ul class="layui-nav right" lay-filter="">
        <li class="layui-nav-item">
            <a href="javascript:;">{{$username}}</a>
            <dl class="layui-nav-child"> <!-- 二级菜单 -->
                <dd><a onclick="x_admin_show('个人信息', '/admin/modify', 550, 368)">个人信息</a></dd>
                <dd><a href="javascript:;" id="login">退出</a></dd>
            </dl>
        </li>
    </ul>

</div>
<!-- 顶部结束 -->
<!-- 中部开始 -->
<!-- 左侧菜单开始 -->
<!--加载菜单-->
@include('layous.menu')

<!-- <div class="x-slide_left"></div> -->
<!-- 左侧菜单结束 -->
<!-- 右侧主体开始 -->
<div class="page-content">
    <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="false">
        <ul class="layui-tab-title">
            <li class="home"><i class="layui-icon">&#xe68e;</i>我的桌面</li>
        </ul>
        <div class="layui-unselect layui-form-select layui-form-selected" id="tab_right">
            <dl>
                <dd data-type="this">关闭当前</dd>
                <dd data-type="other">关闭其它</dd>
                <dd data-type="all">关闭全部</dd>
            </dl>
        </div>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <iframe src='/admin/home' frameborder="0" scrolling="yes" class="x-iframe"></iframe>
            </div>
        </div>
        <div id="tab_show"></div>
    </div>
</div>
<div class="page-content-bg"></div>
<!-- 右侧主体结束 -->
<!-- 中部结束 -->
<!-- 底部开始 -->
@include('layous.footer')
<!-- 底部结束 -->

<script>
    $(function () {
        //退出
        $("#login").click(function () {
            layer.confirm('确认要退出吗?', {icon: 3, title:'提示'}, function(index){
                $.ajax({
                    url: "/admin/logout",
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        location.href = '/admin/login'
                    }
                });
            });
        });
    });
</script>
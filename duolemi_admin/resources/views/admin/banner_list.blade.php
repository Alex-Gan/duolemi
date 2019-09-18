<!--加载头部-->
@include('layous.header')
<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">

<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
<!--[if lt IE 9]>
<script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
<script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<style>
    a{text-decoration:none;}
    a:hover{text-decoration:none;/*指鼠标在链接*/}
</style>

<body>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="">轮播图管理</a>
        <a>
          <cite>轮播图列表</cite>
        </a>
      </span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i>
    </a>
</div>
<div class="x-body">
    @if (count($data['data']) < 5)
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加轮播图', '/admin/banner/add', 480, 500)">
            <i class="layui-icon"></i>添加
        </button>
    </xblock>
    @endif

    <table class="layui-table" style="text-align: center;">
        <thead>
        <tr>
            <th style="text-align: center">ID</th>
            <th style="text-align: center">图片</th>
            <th style="text-align: center">排序</th>
            <th style="text-align: center">操作</th>
        </thead>
        <tbody>
            @if (!$data['data']->isEmpty())
            @foreach($data['data'] as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>
                        <img style="width: 160px; height: 120px; max-width: 160px;" src="{{$item->image}}">
                    </td>
                    <td>{{$item->sort}}</td>
                    <td class="td-manage">
                        <a title="删除" onclick="banner_del(this, {{$item->id}})" href="javascript:;">
                            <i class="layui-icon">&#xe640;</i>删除
                        </a>
                    </td>
                </tr>
            @endforeach
            @else
            <tr style="text-align: center;"><td colspan="7">暂无数据</td></tr>
            @endif
        </tbody>
    </table>

    <div id="page"></div>
</div>
<script>
    layui.use('laypage', function(){

        var url = '/admin/purchase_history/list?'+"{{$data['_query']}}";

        var laypage = layui.laypage;

        //执行一个laypage实例
        laypage.render({
            elem: 'page' //注意，这里的 test1 是 ID，不用加 # 号
            ,count: '{{$data['_count']}}' //数据总数，从服务端得到
            ,limit: '{{$data['_limit']}}'
            ,curr: '{{$data['_curr']}}' //获取起始页
            ,layout: ['page', 'count']
            ,jump: function(obj, first) {
                //首次不执行
                if (!first) {
                    location.href = url + '&page='+ obj.curr;
                }
            }
        });
    });

    //搜索
    layui.use('form', function(){
        var form = layui.form;
        //监听提交
        form.on('submit(sreach)', function(data){
            location.href = '/admin/purchase_history/list?name=' + data.field.name +'&mobile=' + data.field.mobile+'&status='+data.field.status;
        });
    });

    /*删除*/
    function banner_del(obj, id) {
        layer.confirm('确认要删除吗？', function (index) {

            //删除服务器用户
            $.ajax({
                url: "/admin/banner/delete",
                data: {id: id, _token: '{{ csrf_token() }}' },
                type: "DELETE",
                dataType: "json",
                success:function(res) {
                    //发异步删除数据
                    layer.msg('已删除!', {icon: 1, time: 1500});
                    $(obj).parents("tr").remove();

                    setTimeout(function () {
                        window.location.reload();
                    }, 1600);
                }
            });

        });
    }
</script>
</body>
</html>
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
        <a href="">账号管理</a>
        <a>
          <cite>账号列表</cite>
        </a>
      </span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i>
    </a>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input type="text" name="username" placeholder="请输入账号" value="@if(!empty($data['_params']['username'])) {{$data['_params']['username']}}  @endif" autocomplete="off" class="layui-input">
            <input type="text" name="nickname" placeholder="请输入昵称" value="@if(!empty($data['_params']['nickname'])) {{$data['_params']['nickname']}}  @endif" autocomplete="off" class="layui-input">
            <button class="layui-btn" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加账号', '/admin/admin/add', 550, 368)">
            <i class="layui-icon"></i>添加
        </button>
    </xblock>

    <table class="layui-table" style="text-align: center;">
        <thead>
        <tr>
            <th style="text-align: center">ID</th>
            <th style="text-align: center">账号</th>
            <th style="text-align: center">昵称</th>
            <th style="text-align: center">创建时间</th>
            <th style="text-align: center" width="18%">操作</th>
        </thead>
        <tbody>
            @if (!$data['data']->isEmpty())
            @foreach($data['data'] as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{$item->username}}</td>
                    <td>{{$item->nickname}}</td>
                    <td>{{$item->created_at}}</td>
                    <td class="td-manage">
                        <a title="编辑" onclick="x_admin_show('编辑账号', '/admin/admin/edit/{{$item->id}}', 550, 368)" href="javascript:;">
                            <i class="layui-icon">&#xe642;</i>编辑
                        </a>
                        @if ($item->id != 1)
                        <a title="删除" onclick="admin_del(this, {{$item->id}})" href="javascript:;">
                            <i class="layui-icon">&#xe640;</i>删除
                        </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            @else
            <tr style="text-align: center;"><td colspan="5">暂无数据</td></tr>
            @endif
        </tbody>
    </table>

    <div id="page"></div>
</div>
<script>
    layui.use('laypage', function(){

        var url = '/admin/admin/list?'+"{{$data['_query']}}";

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
    layui.use(['form', 'laydate'], function(){
        var form = layui.form
        ,laydate = layui.laydate;

        //开始时间
        laydate.render({
            elem: '#start_date'
        });
        //截止时间
        laydate.render({
            elem: '#end_date'
        });

        //监听提交
        form.on('submit(sreach)', function(data){
            if (data.field.username == '') {
                location.href = '/admin/admin/list';
            }

            location.href = '/admin/admin/list?username=' + data.field.username + '&nickname='+data.field.nickname;
        });
    });

    /*删除*/
    function admin_del(obj, id) {
        layer.confirm('确认要删除吗？', function (index) {

            //删除服务器用户
            $.ajax({
                url: "/admin/admin/delete/"+id,
                data: {_token: '{{ csrf_token() }}' },
                type: "DELETE",
                dataType: "json",
                success:function(res) {
                    if(res.code == 0) {
                        //发异步删除数据
                        layer.msg('已删除!', {icon: 1, time: 1500});
                        $(obj).parents("tr").remove();

                        setTimeout(function () {
                            window.location.reload();
                        }, 1600);
                    }
                }
            });

        });
    }

    /*添加*/
    function add_admin() {
        location.href = "/admin/article/add";
    }

    /*编辑*/
    function edit_admin(id) {
        location.href = "/admin/admin/edit/"+id;
    }
</script>
</body>
</html>
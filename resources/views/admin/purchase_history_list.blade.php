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
        <a href="">购买记录管理</a>
        <a>
          <cite>购买记录</cite>
        </a>
      </span>
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon" style="line-height:30px">ဂ</i>
    </a>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input type="text" name="name" placeholder="请输入会员姓名" value="@if(!empty($data['_params']['name'])) {{$data['_params']['name']}}  @endif" autocomplete="off" class="layui-input">
            <input type="text" name="mobile" placeholder="请输入手机号" value="@if(!empty($data['_params']['mobile'])) {{$data['_params']['mobile']}}  @endif" autocomplete="off" class="layui-input">

            <div class="layui-inline">
                <div class="layui-input-inline">
                    <select name="status">
                        <option value="">请选择状态</option>
                        <option value="1" @if(!empty($data['_params']['status']) && $data['_params']['status'] == 1) selected  @endif>已购买</option>
                        <option value="2" @if(!empty($data['_params']['status']) && $data['_params']['status'] == 2) selected  @endif>已面试</option>
                        <option value="3" @if(!empty($data['_params']['status']) && $data['_params']['status'] == 3) selected  @endif>正在体验</option>
                        <option value="4" @if(!empty($data['_params']['status']) && $data['_params']['status'] == 4) selected  @endif>体验完成</option>
                    </select>
                </div>
            </div>

            <button class="layui-btn" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
        </form>
    </div>

    <table class="layui-table" style="text-align: center;">
        <thead>
        <tr>
            <th style="text-align: center">ID</th>
            <th style="text-align: center">会员头像</th>
            <th style="text-align: center">会员昵称</th>
            <th style="text-align: center">会员姓名</th>
            <th style="text-align: center">手机号</th>
            <th style="text-align: center">状态</th>
            <th style="text-align: center">购买时间</th>
            <th style="text-align: center" width="18%">操作</th>
        </thead>
        <tbody>
            @if (!$data['data']->isEmpty())
            @foreach($data['data'] as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>
                        <img width="64" height="64"  style="border-radius: 32px;" src="{{$item->avatar}}"/>
                    </td>
                    <td>{{$item->nickname}}</td>
                    <td>
                        {{$item->name}}
                    </td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$item->status_text}}</td>
                    <td>{{$item->created_at}}</td>
                    <td class="td-manage">
                        <a title="处理" onclick="edit_franchise_course({{$item->id}})" href="javascript:;">
                            <i class="layui-icon">&#xe642;</i>处理
                        </a>
                    </td>
                </tr>
            @endforeach
            @else
            <tr style="text-align: center;"><td colspan="8">暂无数据</td></tr>
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

    /*处理*/
    function edit_franchise_course(id) {
        layer.msg('该功能正在完善中，请耐心等待...');
        return false;
        location.href = "/admin/experience_course/edit/"+id;
    }
</script>
</body>
</html>
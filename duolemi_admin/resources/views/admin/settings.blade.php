<!--加载头部-->
@include('layous.header')

<meta name="X-CSRF-TOKEN" content="{{csrf_token()}}">
<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
<!--[if lt IE 9]>
<script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
<script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<script type="text/javascript" charset="utf-8" src="/admin/ueditor1.4.3/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/admin/ueditor1.4.3/ueditor.all.min.js"> </script>
<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="/admin/ueditor1.4.3/lang/zh-cn/zh-cn.js"></script>

<style>
    .layui-form-switch {
        margin-top: 6px;
    }
</style>

<body>
<div class="x-body">
    <form class="layui-form">
        {{csrf_field()}}
        @if (!$data->isEmpty())
            @foreach($data as $item)
            <div class="layui-form-item">
                <label class="layui-form-label">{{$item->name}}</label>
                <div class="layui-input-block">
                    <input type="checkbox" data-id="{{$item->id}}" @if ($item->value == 1) checked="" @endif name="open" lay-skin="switch" lay-filter="switchTest" lay-text="开启|关闭">
                </div>
            </div>
          @endforeach
        @endif
    </form>
</div>
<script>
    //获取token的值
    var upload_token = $("input[name=_token]").val();

    layui.use(['form'], function(){
        var form = layui.form;

        //监听指定开关
        form.on('switch(switchTest)', function(data){
            var type = this.checked;
            var id = this.dataset.id;

            $.ajax({
                url: "/admin/settings/set",
                data: {id: id, type: type, _token: upload_token },
                type: "PUT",
                dataType: "json",
                success:function(res) {
                    if (res.code == 0) {
                        layer.msg('设置成功!', {icon: 1, time: 1000});
                    } else {
                        layer.msg('设置是吧!', {icon: 2, time: 1000});
                    }
                }
            });
        });
    });
</script>
</body>
</html>
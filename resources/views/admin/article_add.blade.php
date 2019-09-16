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


<body>
<div class="x-body">
    <form class="layui-form">
        {{csrf_field()}}
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>文章标题
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <input type="text" id="title" name="title" required="" lay-verify="required" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_email" class="layui-form-label">
                <span class="x-red">*</span>文章内容
            </label>
            <div class="layui-input-inline">
                <script id="editor" type="text/plain" style="width:800px;height:500px;"></script>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label">
            </label>
            <button  class="layui-btn" lay-filter="add" lay-submit="">
                添加
            </button>
            <button class="layui-btn" lay-filter="back" onclick="javascript :history.back(-1);">
                返回
            </button>
        </div>
    </form>
</div>
<script>
    var local_franchise_course_data = new Array();

    //实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
    var ue = UE.getEditor('editor',{
        initialFrameWidth:  800,  //初始化编辑器宽度,默认1000
        initialFrameHeight: 500  //初始化编辑器高度,默认320
    });

    //获取token的值
    var upload_token = $("input[name=_token]").val();

    layui.use(['form','layer'], function(){

        $ = layui.jquery;
        var form = layui.form
            ,layer = layui.layer;

        //自定义验证规则
        /*
        form.verify({
            username: function(value){
                if(value.length < 6){
                    return '加盟标题至少得6个字符啊';
                }
            }
            ,pass: [/(.+){6,20}$/, '密码必须6到20位']
            ,repass: function(value){
                if($('#L_pass').val()!=$('#L_repass').val()){
                    return '两次密码不一致';
                }
            }
        });
        */

        //监听提交
        form.on('submit(add)', function(data) {
            //开启load，防止重复提交
            var index = layer.load();

            var param_data = data.field;

            var details = ue.getContent();

            if (details == '') {
                layer.close(index);
                layer.msg('请填写详情介绍');
                return false;
            }

            //将banner图存入数组中

            param_data.content = details;

            $.ajax({
                url: "/admin/article/add",
                data: data.field,
                type: "POST",
                dataType: "json",
                success:function(res) {
                    layer.close(index);

                    if (res.code == 0) {
                        layer.msg('添加成功!', {icon: 1, time: 1000});
                        setTimeout(function () {
                            window.location.href="/admin/article/list";
                        }, 1100);
                    } else {
                        layer.msg(res.msg);
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
</script>
</body>
</html>
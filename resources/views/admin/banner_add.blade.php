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
    {{csrf_field()}}
    <div class="layui-form-item">
        <label for="username" class="layui-form-label">
            轮播图
        </label>
        <div class="layui-input-inline" style="width: 280px;">
            <!--
            <input type="text" style="border: none;" id="username" name="username"  readonly required="" lay-verify="required" autocomplete="off" class="layui-input">
            -->
            <div class="layui-upload">
                <button type="button" class="layui-btn" id="test1">上传图片</button>
                <div class="layui-upload-list">
                    <img class="layui-upload-img" style="width: 160px; height: 120px;" id="demo1">
                    <input type="hidden" id="image" name="image" value=""/>
                    <p id="demoText"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label for="username" class="layui-form-label">
            排序
        </label>
        <div class="layui-input-inline" style="width: 68px;">
            <input type="text" id="sort" name="sort"  required="" value="0" lay-verify="required|nickname" autocomplete="off" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label for="L_repass" class="layui-form-label">
        </label>
        <button class="layui-btn" lay-filter="create" id="banner_add">
            添加
        </button>
    </div>
</div>
<script>
    //获取token的值
    var upload_token = $("input[name=_token]").val();


    layui.use(['form','layer', 'upload'], function(){

        $ = layui.jquery;
        var form = layui.form
            ,layer = layui.layer
            ,upload = layui.upload;


        //普通图片上传
        var uploadInst = upload.render({
            elem: '#test1'
            ,url: '/admin/upload/multi_upload'
            ,number: 1
            ,data: {_token: upload_token }
            ,accept: 'images' //只允许上传图片
            ,acceptMime: 'image/*' //只筛选图片
            ,size: 1024*2 //限定大小
            ,before: function(obj){
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result) {
                    $('#demo1').attr('src', result); //图片链接（base64）
                });
            }
            ,done: function(res){
                //如果上传失败
                if(res.code > 0){
                    return layer.msg('上传失败');
                }
                //上传成功，并回显
                $("#image").val(res.data.img);
            }
            ,error: function(){
                //演示失败状态，并实现重传
                /*
                var demoText = $('#demoText');
                demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                demoText.find('.demo-reload').on('click', function(){
                    uploadInst.upload();
                });
                */
            }
        });
    });

    $(function () {
        //表单提交
        $("#banner_add").click(function () {
            var image = $("#image").val();
            var sort = $("#sort").val();

            if (image == '') {
                layer.msg('请上传图片!', {icon: 2, time: 1500});
                return false;
            }

            var data = {
                image: image,
                sort: sort,
                _token:upload_token
            }

            var load = layer.load();
            $.ajax({
                url: "/admin/banner/create",
                data: data,
                type: "POST",
                dataType: "json",
                success:function(res) {
                    layer.close(load);

                    if (res.code == 0) {
                        var index = parent.layer.getFrameIndex(window.name);//获取当前弹出层的层级

                        setTimeout(function () {
                            layer.msg('添加成功!', {icon: 1, time: 1500});
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
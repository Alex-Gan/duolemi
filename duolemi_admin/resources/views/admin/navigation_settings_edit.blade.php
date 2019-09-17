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
                <span class="x-red">*</span>导航名称
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <input type="text" id="name" name="name" required="" lay-verify="required" value="{{$data->name}}" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>导航图标
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <div class="layui-upload">
                    <button type="button" class="layui-btn" id="test1">上传图片</button>
                    <div class="layui-upload-list">
                        <img class="layui-upload-img" style="width: 64px; height: 64px;" id="demo1" src="{{$data->icon}}">
                        <input type="hidden" id="icon" name="icon" value="{{$data->icon}}"/>
                        <p id="demoText"></p>
                    </div>
                </div>
            </div>

        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>导航类型
            </label>
            <div class="layui-input-inline">
                <select name="type" lay-verify="required" lay-search="" lay-filter="type">
                    <option value="1">内容页</option>
                    <option value="2">小程序跳转</option>
                    <option value="3">拨打电话</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item" id="content_page">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>内容页
            </label>
            <div class="layui-input-inline">
                <select name="content_val" lay-verify="required" lay-search="" lay-filter="type">
                    @foreach ($data->article_data as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item" id="small_program_page" style="display: none;">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>小程序页面
            </label>
            <div class="layui-input-inline">
                <select name="small_program_val" lay-verify="required" lay-search="" lay-filter="small_program">
                    @foreach ($data->small_program_page_data as $item)
                        <option value="{{$item['path']}}">{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item" id="call_phone_page" style="display: none;">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>拨打电话
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <input type="text" id="call_phone_value" name="call_phone_val" required="" value="" autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label">
            </label>

            <button class="layui-btn" lay-filter="edit" lay-submit="">
                修改
            </button>
            <button class="layui-btn" onclick="javascript :history.back(-1); return false;">
                返回
            </button>
        </div>
    </form>
</div>
<script>

    //获取token的值
    var upload_token = $("input[name=_token]").val();

    layui.use(['form','layer', 'upload'], function(){

        $ = layui.jquery;
        var form = layui.form
            ,layer = layui.layer
            ,upload = layui.upload;


        //自定义验证规则
        /*
        form.verify({
            title: function(value){
                if(value.length < 6){
                    return '加盟标题至少得6个字符啊';
                }
            }
        });
        */

        //监听提交

        form.on('submit(edit)', function(data) {
            //开启load，防止重复提交
            var index = layer.load();

            var param_data = data.field;

            if (param_data.icon == "") {
                layer.close(index);
                layer.msg('请上传导航图标', {icon: 2, time: 1000});
                return false;
            }

            $.ajax({
                url: "/admin/navigation_settings/editPut/"+"{{$data->id}}",
                data: param_data,
                type: "PUT",
                dataType: "json",
                success:function(res) {
                    layer.close(index);

                    if (res.code == 0) {
                        layer.msg('修改成功!', {icon: 1, time: 1000});
                        setTimeout(function () {
                            window.location.href="/admin/navigation_settings/list";
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
                $("#icon").val(res.data.img);
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

        /*导航类型更改*/
        form.on('select(type)', function(data){
            var type_val = data.value;

            if (type_val == 1) {
                $("#content_page").show();
                $("#small_program_page").hide();
                $("#call_phone_page").hide();
            } else if(type_val == 2) {
                $("#content_page").hide();
                $("#small_program_page").show();
                $("#call_phone_page").hide();
            } else if(type_val == 3) {
                $("#content_page").hide();
                $("#small_program_page").hide();
                $("#call_phone_page").show();
            }
        });
    });
</script>
</body>
</html>
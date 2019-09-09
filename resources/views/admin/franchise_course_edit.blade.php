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
                <span class="x-red">*</span>加盟标题
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <input type="text" id="title" name="title" required="" lay-verify="required" value="{{$edit_data->title}}" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>副标题
            </label>
            <div class="layui-input-inline" style="width: 280px;">
                <input type="text" id="subtitle" name="subtitle" required="" lay-verify="required" value="{{$edit_data->subtitle}}" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="phone" class="layui-form-label">
                <span class="x-red">*</span>banner图
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <div class="layui-upload">
                    <input type="hidden" name="banner" value="{{$edit_data->banner_json}}">
                    <button type="button" class="layui-btn layui-btn-normal" id="testList">上传多图片</button>
                    <div id="action_upload_imgs">
                        <div class="layui-upload-list" >
                            <table class="layui-table">
                                <thead>
                                <tr>
                                    <th>文件名</th>
                                    <th>图片</th>
                                    <th>大小</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody id="demoList">
                                    @foreach ($edit_data->banner as $banner)
                                        <tr id="upload-1568010458094-0">
                                            <td>{{$banner['name']}}</td>
                                            <td><img src="{{$banner['img']}}" /></td>
                                            <td>{{$banner['size']}}</td>
                                            <td><span style="color: #5FB878;">上传成功</span></td>
                                            <td>
                                                <button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>
                                                <button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="layui-btn" id="testListAction" style="display: none;">开始上传</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_email" class="layui-form-label">
                <span class="x-red">*</span>详情介绍
            </label>
            <div class="layui-input-inline">
                <script id="editor" type="text/plain" style="width:800px;height:500px;"></script>
                <input type="hidden" name="" value="{{$edit_data->details}}" id="editorValue">
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
    var local_franchise_course_data = new Array();

    /*给banner图赋值*/
    var bannerArr = $("input[name=banner]").val();
    var bannerJson = JSON.parse(bannerArr);

    for (var i=0; i< bannerJson.length; i++) {
        local_franchise_course_data.push(bannerJson[i]);
    }

    //实例化编辑器
    var ue = UE.getEditor('editor').addListener("ready", function () {
        var htmlStr = $("#editorValue").val();
        UE.getEditor('editor').setContent(htmlStr);
    },{
        allowDivTransToP:false
    });

    //获取token的值
    var upload_token = $("input[name=_token]").val();

    layui.use(['form','layer', 'upload'], function(){

        $ = layui.jquery;
        var form = layui.form
            ,layer = layui.layer
            ,upload = layui.upload;



        //自定义验证规则
        form.verify({
            title: function(value){
                if(value.length < 6){
                    return '加盟标题至少得6个字符啊';
                }
            }
        });

        //监听提交

        form.on('submit(edit)', function(data) {
            //开启load，防止重复提交
            var index = layer.load();

            var param_data = data.field;

            //banner图
            var banner = local_franchise_course_data;
            if (banner == '') {
                layer.close(index);
                layer.msg('请上传banner图');
                return false;
            }

            var details = UE.getEditor('editor').getContent();

            if (details == '') {
                layer.close(index);
                layer.msg('请填写详情介绍');
                return false;
            }

            //将banner图存入数组中
            param_data.banner = banner;

            param_data.details = details;

            $.ajax({
                url: "/admin/franchise_course/editPut/"+"{{$edit_data->id}}",
                data: param_data,
                type: "PUT",
                dataType: "json",
                success:function(res) {
                    layer.close(index);

                    if (res.code == 0) {
                        layer.msg('修改成功!', {icon: 1, time: 1000});
                        setTimeout(function () {
                            window.location.href="/admin/franchise_course/list";
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

        //多文件列表示例
        var demoListView = $('#demoList')
            ,uploadListIns = upload.render({
            elem: '#testList'
            ,url: '/admin/upload/multi_upload'
            ,number: 5
            ,data: {_token: upload_token }
            ,accept: 'images' //只允许上传图片
            ,acceptMime: 'image/*' //只筛选图片
            ,size: 1024*2 //限定大小
            ,multiple: true
            ,auto: false
            ,bindAction: '#testListAction'
            ,choose: function(obj){
                $('#testListAction').show();
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                //读取本地文件
                obj.preview(function(index, file, result){
                    var tr = $(['<tr id="upload-'+ index +'">'
                        ,'<td>'+ file.name +'</td>'
                        ,'<td><img width="100" height="70" src="'+ result +'" /></td>'
                        ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
                        ,'<td>等待上传</td>'
                        ,'<td>'
                        ,'<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                        ,'<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                        ,'</td>'
                        ,'</tr>'].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function(){
                        //console.log(file);
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function(){
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    demoListView.append(tr);
                });
            }
            ,done: function(res, index, upload){
                if(res.code == 0){ //上传成功

                    local_franchise_course_data.push(res.data);

                    var tr = demoListView.find('tr#upload-'+ index)
                        ,tds = tr.children();
                    tds.eq(3).html('<span style="color: #5FB878;">上传成功</span>');
                    //tds.eq(3).html(''); //清空操作
                    //tds.eq(3).html('<img width="100" height="75" src="'+res.data+'"/>'); //清空操作
                    return delete this.files[index]; //删除文件队列已经上传成功的文件
                }
                this.error(index, upload);
            }
            ,error: function(index, upload){
                var tr = demoListView.find('tr#upload-'+ index)
                    ,tds = tr.children();
                tds.eq(3).html('<span style="color: #FF5722;">上传失败</span>');
                tds.eq(4).find('.demo-reload').removeClass('layui-hide'); //显示重传
            }
        });
    });
</script>
</body>
</html>
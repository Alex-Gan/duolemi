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
        {{csrf_field()}}
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                真实姓名
            </label>
            <div class="layui-form-mid layui-word-aux">{{$data->real_name}}</div>
        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                银行名称
            </label>
            <div class="layui-form-mid layui-word-aux">{{$data->bank_name}}</div>
        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                支行名称
            </label>
            <div class="layui-form-mid layui-word-aux">{{$data->branch_name}}</div>
        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                银行账户
            </label>
            <div class="layui-form-mid layui-word-aux">{{$data->bank_account}}</div>
        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                提现时间
            </label>

            <div class="layui-form-mid layui-word-aux">{{$data->withdraw_at}}</div>
        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                提现金额
            </label>
            <div class="layui-form-mid layui-word-aux">{{$data->apply_money}}</div>
        </div>

        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                提现状态
            </label>
            <div class="layui-form-mid layui-word-aux">{{$data->status_text}}</div>
        </div>

        @if ($data->status == 1)
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label">
                </label>
                <button  class="layui-btn" lay-filter="pass" lay-submit="" id="audit_pass">
                    审核通过
                </button>
                <button class="layui-btn" lay-filter="back" id="audit_reject">
                    驳回
                </button>
            </div>
        @else
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label">
                </label>
                <button class="layui-btn" lay-filter="back" onclick="javascript :history.back(-1);">
                    返回
                </button>
            </div>
        @endif
    </div>
<script>
    $(function () {
        //获取token的值
        var _token = $("input[name=_token]").val();

        /*审核通过*/
        $("#audit_pass").click(function () {
            layer.confirm('确认审核通过吗?', {
                btn: ['确认','取消'] //按钮
            }, function(){
                //开启load，防止重复提交
                var index = layer.load();

                $.ajax({
                    url: "/admin/withdraw/audit_pass/" + "{{$data->id}}",
                    data: {
                        _token: _token
                    },
                    type: "PUT",
                    dataType: "json",
                    success: function (res) {
                        layer.close(index);
                        if (res.code == 0) {
                            layer.msg('审核通过!', {icon: 1, time: 1000});
                            setTimeout(function () {
                                window.location.href="/admin/withdraw/list";
                            }, 1100);
                        }
                    }
                });
            }, function(){

            });
        });

        /*驳回*/
        $("#audit_reject").click(function () {
            layer.confirm('确认审核驳回吗?', {
                btn: ['确认','取消'] //按钮
            }, function(){
                //alert('审核通过');
                //开启load，防止重复提交
                var index = layer.load();

                $.ajax({
                    url: "/admin/withdraw/audit_reject/" + "{{$data->id}}",
                    data: {
                        _token: _token
                    },
                    type: "PUT",
                    dataType: "json",
                    success: function (res) {
                        layer.close(index);
                        if (res.code == 0) {
                            layer.msg('审核已被驳回!', {icon: 1, time: 1000});
                            setTimeout(function () {
                                window.location.href="/admin/withdraw/list";
                            }, 1100);
                        }
                    }
                });
            }, function(){

            });
        });
        return false;
    });
</script>
</body>
</html>
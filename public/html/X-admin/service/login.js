/**
 * 登录业务
 */

$(function () {
    var isClick = true;

    layui.use('form', function () {
        var form = layui.form;

        //监听提交
        form.on('submit(login)', function (data) {
            if (isClick == false) {
                return false;
            }
            isClick = false;

            //加载效果
            var index = layer.load();

            _login(data.field, index);
            return false;
        });

    });
});


function _login(data, index) {
    $.ajax({
        url: "/admin/login",
        data: data,
        type: "POST",
        dataType: "json",
        success: function (res) {
            layer.close(index);

            if (res.code == 0) {
                layer.msg(res.msg);
                setInterval(function () {
                    location.href = 'index.html'
                }, 1000);
            } else {
                isClick = true;
                layer.msg(res.msg);
                return false;
            }

        },
        error: function (data) {
            $.messager.alert('错误', data.msg);
        }
    });

}




$(function () {
    $.ajax({
        url: "/admin/welcome",
        type: "GET",
        dataType: "json",
        success: function (res) {
            if (res.code == 0) {
                //对页面进行赋值
                $("body").attr("style","dispaly:block");
                $('.x-red').html(res.data.username);
                $('#open_time').html(res.data.open_time);
            } else {
                layer.msg(res.msg);
                return false;
            }
        },
        error: function (data) {
            //$.messager.alert('错误', data.msg);
        }
    });
});
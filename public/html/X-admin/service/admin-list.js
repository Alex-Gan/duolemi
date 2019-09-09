$(function () {

    $.ajax({
        url: "/admin/admin-list",
        type: "GET",
        dataType: "json",
        success: function (res){
            if (res.code == 0) {
                //对页面进行赋值
                $("body").attr("style","dispaly:block");

                var _data = res.data.data;

                var _html = "";

                if (_data.length > 0) {
                    for (var i = 0; i < _data.length; i++) {
                        var _html = _html + '<tr>';
                        var _html = _html +     '<td>';
                        var _html = _html +         '<div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id="2">';
                        var _html = _html +             '<i class="layui-icon">&#xe605;</i>';
                        var _html = _html +         '</div>';
                        var _html = _html +     '</td>';
                        var _html = _html +     '<td>'+_data[i].id+'</td>';
                        var _html = _html +     '<td>'+_data[i].username+'</td>';
                        var _html = _html +     '<td>'+_data[i].mobile+'</td>';
                        var _html = _html +     '<td>'+_data[i].email+'</td>';
                        var _html = _html +     '<td>超级管理员</td>';
                        var _html = _html +     '<td>'+_data[i].created_at+'</td>';
                        var _html = _html +     '<td class="td-status">';
                        var _html = _html +         '<span class="layui-btn layui-btn-normal layui-btn-mini">已启用</span>';
                        var _html = _html +     '</td>';
                        var _html = _html +     '<td class="td-manage">';
                        var _html = _html +         '<a onclick="member_stop(this,\'10001\')" href="javascript:;" title="启用">';
                        var _html = _html +         '<i class="layui-icon">&#xe601;</i>';
                        var _html = _html +         '</a>';
                        var _html = _html +         '<a title="编辑" onclick="x_admin_show(\'编辑\',\'admin-edit.html\')" href="javascript:;">';
                        var _html = _html +         '<i class="layui-icon">&#xe642;</i>';
                        var _html = _html +         '</a>';
                        var _html = _html +     '</td>';
                        var _html = _html + '</tr>';

                        $("#tbody-data").empty();
                        $("#tbody-data").append(_html);
                    }
                }
            } else {
                location.href = './login.html'
            }
        },
        error: function (data) {
            //$.messager.alert('错误', data.msg);
        }
    });

    //退出
    $("#login").click(function () {

        layer.confirm('确认要退出吗?', {icon: 3, title:'提示'}, function(index){
            $.ajax({
                url: "/admin/logout",
                type: "GET",
                dataType: "json",
                success:function(data) {
                    location.href = './login.html'
                }
            });
        });
    });
});
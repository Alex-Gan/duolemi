<div class="left-nav">
    <div id="side-nav">
        <ul id="nav">
            @foreach ($menu as $item)
            <li>
                <a href="javascript:;">
                    <i class="iconfont">{!! $item['icon'] !!}</i>
                    <cite>{{$item['name']}}</cite>
                    <i class="iconfont nav_right">&#xe697;</i>
                </a>
                <ul class="sub-menu">
                    @if (!empty($item['sub_menu']))
                        @foreach ($item['sub_menu'] as $sub_menu)
                            <li date-refresh="1">
                                <a _href="{{$sub_menu['srule']}}">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>{{$sub_menu['sname']}}</cite>
                                </a>
                            </li >
                        @endforeach
                    @endif
                </ul>
            </li>
            @endforeach
        </ul>
    </div>
</div>
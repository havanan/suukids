@if(!empty($breadcrumb))
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{ $breadcrumb['title'] }}</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            @php
            $active = !empty($breadcrumb['active']) ? $breadcrumb['active'] : [];
            $content = !empty($breadcrumb['content']) ? $breadcrumb['content'] : "";
            @endphp
            @if(!empty($content))
            @foreach($content as $title => $link)
            <li class="breadcrumb-item {{ in_array($title, $active) ? "active" : "" }}">
                @if(!in_array($title, $active))
                <a href="{{ !empty($link) ? $link : "javascript:voild(0);" }}">{{ $title }}</a>
                @else
                {{ $title }}
                @endif
            </li>
            @endforeach
            @endif
        </ol>
    </div><!-- /.col -->
</div><!-- /.row -->
@endif

@extends('layout.default')
@php
use Illuminate\Support\Arr
@endphp

@section('assets')
@include('layout.flash_message')
<script>
</script>
@stop

@section('content')
<form action="">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-2">
                                    <input type="text" name="date_begin" class="form-control date-picker" value="{{ request()->input('date_begin') ?: date('01/m/Y') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="date_end" class="form-control date-picker" value="{{ request()->input('date_end') ?: date('d/m/Y') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="source_id" id="source_id" class="select2 form-control">
                                        <option value="">- Chọn nguồn-</option>
                                        @foreach($sources as $source)
                                        <option {{ $source->id == request()->input('source_id') ? 'selected' : '' }} value="{{ $source->id }}">{{ $source->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="user_id" id="user_id" class="select2 form-control">
                                        <option value="">- Chọn cho-</option>
                                        @foreach($users as $user)
                                        <option {{ $user->id == request()->input('user_id') ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="type" id="type" class="select2 form-control">
                                        <option value="">- Chọn loại-</option>
                                        <option {{ request()->input('type') == 1 ? 'selected' : '' }} value="1">Cấp Ads</option>
                                        <option {{ request()->input('type') == 2 ? 'selected' : '' }} value="2">Chi phí Ads</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success">Tìm</button>
                                    <a href="/admin/cost" class="btn btn-default">Bỏ lọc</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Loại</th>
                                            <th>Nguồn</th>
                                            <th>Ngày</th>
                                            <th>Cho</th>
                                            <th>Quyền</th>
                                            <th>Cấp</th>
                                            <th>Tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                        <tr data-key="{{ $item->id }}">
                                            <td>{{ $loop->index+ 1}}</td>
                                            <td>{!! $item->type == 1 ? 'Cấp Ads' : 'Chi phí Ads' !!}</td>
                                            <td>{!! $item->source->name !!}</td>
                                            <td>{!! date('d/m/Y',strtotime($item->day)) !!}</td>
                                            <td>{!! $item->user->name !!}</td>
                                            <td>{!! implode(', ',collect($item->user->permissions)->pluck('name')->all()) !!}</td>
                                            <td class="text-right">{!! number_format($item->amount,0) !!}</td>
                                            <td>{!! date('d/m/Y H:i',strtotime($item->created_at)) !!} bởi {{ $item->created_info ? $item->created_info->name : 'N/A' }} </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $items->appends($_GET)->links() }}
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
        </div>
    </section>
</form>
@stop
@section('scripts')
<script>

    $(function(){
        $('.select2').select2({})
        Common.datePicker('.date-picker')
    });
</script>
@endsection

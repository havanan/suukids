@extends('layout.default')
@php
use Illuminate\Support\Arr
@endphp

@section('assets')
<script src="{{url('js/customer/overview.js')}}"></script>
@include('layout.flash_message')
<script>
</script>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Nội dung</th>
                                        <th>Thời gian hẹn</th>
                                        <th>Tác vụ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr data-key="{{ $item->id }}">
                                        <td>{{ $loop->index+ 1}}</td>
                                        <td>{!! $item->content !!}</td>
                                        <td>{!! $item->time !!}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning">Sửa</button>
                                        </td>
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
        <!-- /.row -->
    </div><!-- /.container-fluid -->


</section>
@stop

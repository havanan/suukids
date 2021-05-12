@extends('layout.default')
@section('title') Admin | Quản lý hình thức giao hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
@php
$breadcrumb = [
  'title' => __('Quản lý hình thức giao hàng'),
  'content' => [
  __('Quản lý hình thức giao hàng') => route("admin.delivery.index")
],
'active' => [__('Quản lý hình thức giao hàng')]
];
@endphp
@include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
    <script>
        function addRow() {
            var d = new Date();
            var id = d.getTime();

            var row = $('<tr id="row'+id+'">' +
                '<td></td>' +
                '<td><input type="text" class="form-control" name="name[]"></td>' +
                '<td class="text-center"><button class="btn btn-danger" onclick="removeRow('+id+')"><i class="fa fa-trash-alt"></i></button></td>' +
                '</tr>');

            $('#order-source-body').append(row);
        }
        function removeRow(id) {
            if(id){
                let deliveryId = $('#row'+id).attr('data-id');
                let removeId = $('input[name ="removeDelivery"]').val();
                if(removeId == ''){
                    $('input[name ="removeDelivery"]').val(deliveryId)
                }else $('input[name ="removeDelivery"]').val(removeId + ',' + deliveryId)
                $('#row'+id).remove();
            }
        }
        $(document).ready(function(){
            $('div#delivery table').on('change','input[name ="oldName[]"]',function(){
                let id = $(this).closest('tr').attr('data-id');
                let name = $(this).val()
                $(this).closest('tr').find('.oldDelivery').val(id + ':' + name)
            })
        })
        @if($message = Session::get('error'))
            toastr.error('{{$message}}')
        @endif
        @if($message = Session::get('success'))
            toastr.success('{{$message}}')
        @endif
        
    </script>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{route('admin.delivery.index')}}" method="post">
                        @csrf
                        <input type="hidden" name="removeDelivery">
                        <div class="card">
                            <div class="card-header">
                                <!-- /.modal -->
                                <div class="text-center" style="float: right">
                                  <button type="submit" class="btn btn-primary btn-custom-color float-right">Lưu</button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" id="delivery">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="width: 40px">ID</th>
                                        <th >Hình thức giao hàng</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="order-source-body">
                                        @foreach ($listDelivery as $index => $delivery )
                                            <tr id="row{{$delivery->id}}" data-id="{{$delivery->id}}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <input type="text" name="oldName[]" value="{{$delivery->name}}" class="form-control">
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-danger" onclick="removeRow({{$delivery->id}})"><i class="fa fa-trash-alt"></i></button>
                                                </td>
                                                <input type="hidden" class="oldDelivery" name="oldDelivery[]" value="{{$delivery->id.':'.$delivery->name}}">
                                            </tr>
                                        @endforeach
                                        
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary mt-3" onclick="addRow()">Thêm</button>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </form>
                    
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@stop

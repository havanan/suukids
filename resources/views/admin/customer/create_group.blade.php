@extends('layout.default')
@section('title') Admin | Thêm Phân loại khách hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Thêm Phân loại khách hàng'),
            'content' => [
                __('Thêm Phân loại khách hàng') => route('admin.customer.group.add')
            ],
            'active' => [__('Thêm Phân loại khách hàng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')

@stop

@section('content')
    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                  @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <h5><i class="icon fas fa-ban"></i> Lỗi!</h5>
                          @foreach ($errors->all() as $error)
                              <div>{{$error}}</div>
                          @endforeach
                    </div>
                  @endif
                  <form action="{{ route('admin.customer.group.add') }}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-header text-right">
                          <button type ="submit" class="btn btn-primary">Ghi lại</button>
                          <a href="{{route('admin.customer.group.list')}}"><button type="button" class="btn btn-default">Quay lại</button></a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="form-group row">
                              <label for="staticEmail" class="col-sm-2 col-form-label">Danh mục cha</label>
                              <div class="col-sm-10">
                                <select class="form-control" name="parent_id" id="">
                                  @foreach ($listParent as $parent )
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                  @endforeach
                                </select>
                              </div>
                            </div>
                            <div class="form-group row">
                              <label for="inputPassword" class="col-sm-2 col-form-label">Tên</label>
                              <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="inputPassword" placeholder="">
                              </div>
                            </div>
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

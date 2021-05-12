@extends('layout.default')
@section('title') Admin | Phân loại khách hàng @stop
{{-- Breadcrumb --}}
@section('breadcrumb')
    @php
        $breadcrumb = [
            'title' => __('Phân loại khách hàng'),
            'content' => [
                __('Danh mục phân loại khách hàng') => route('admin.customer.group.list')
            ],
            'active' => [__('Phân loại khách hàng')]
        ];
    @endphp
    @include("elements.breadcrumb", $breadcrumb)
@stop
{{-- End Breadcrumb --}}

@section('assets')
  <script>
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
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                      <form action="{{ $mode == 'list' ? route('admin.customer.group.list') : route('admin.customer.group.delete') }}" method="post">
                        @csrf
                        <div class="card-header text-right">
                          <a href="{{ route('admin.customer.group.add') }}"><button type="button" class="btn btn-primary">Thêm mới</button></a>
                          <button type="submit" class="btn btn-danger">Xóa</button>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                          <div class="row">
                            <table class="table">
                              
                              <thead>
                                <tr valign="middle" bgcolor="#EFEFEF">
                                  <td width="1%" title="Chọn tất cả">
                                    <input type="checkbox" @if($mode == 'delete') checked @endif value="1" id="CrmCustomerGroup_all_checkbox" >
                                  </td>
                                  <td nowrap="" align="left">
                                    <label for="CrmCustomerGroup_all_checkbox">Chọn tất cả</label>
                                  </td>
                                  <td>&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                              </thead>
                              <tbody>
                                @if($mode == 'list')
                                  {{ genCustomerGroup($customerGroup) }}
                                @else
                                  {{ genCustomerGroupDelete($customerGroup) }}
                                @endif

                                
                              </tbody>
                            </table>
                          </div>
                            <!-- /.row -->
                        </div>
                      </form>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            
        </div><!-- /.container-fluid -->
    </section>
@stop

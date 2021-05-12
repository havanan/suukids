@extends('layout.default')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>IP</th>
                                            <th>Order number</th>
                                            <th>Order reference</th>
                                            <th>Order status</th>
                                            <th>Location currently</th>
                                            <th>Note</th>
                                            <th>Product weight</th>
                                            <th>Body</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <td>{{ $loop->index+ 1 }}</td>
                                        <td>{{ $item->ip }}</td>
                                        <td>{{ $item->order_number }}</td>
                                        <td>{{ $item->order_reference }}</td>
                                        <td>{{ $item->order_status }}</td>
                                        <td>{{ $item->location_currenctly }}</td>
                                        <td>{{ $item->note }}</td>
                                        <td>{{ $item->product_weight }}</td>
                                        <td>{{ $item->json_body }}</td>
                                        <td>{{ date('d/m/Y H:i',strtotime($item->created_at)) }}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div>
                                {{ $items->appends(request()->all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

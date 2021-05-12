@if(!empty($waitingOrders) && !$waitingOrders->isEmpty())
<div style="overflow: hidden;padding-top:0px; max-height:200px; overflow: auto;">
    <div style="font-weight:bold;border-bottom: 1px solid #ffc2b8;">
    Bạn được chia số, vui lòng xử lý: 
    </div>
    <br>
    @foreach($waitingOrders as $key => $order)
        <div style="background: #ffa153;width:100px;float: left;padding:2px;margin-bottom: 2px; border:1px solid #FFF;margin:1px;border-radius: 3px;">
            {{ $order->code }}
        <a class="btn btn-warning pull-right btn-sm" style="background:#fff;color:#999;height: 20px;padding:2px;text-decoration: none;text-transform: uppercase;border:1px solid #333" href="{{route('admin.sell.order.index')}}?code={{ $order->code }}"> 
                <i class="fa fa-sign-in"></i> Xử lý
            </a>
        </div>
    @endforeach
</div>
@endif
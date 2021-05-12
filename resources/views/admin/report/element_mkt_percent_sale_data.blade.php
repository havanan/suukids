<style>
    .head-bottom{
        font-weight: bold;
    }
</style>
<table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th width="15%"></th>
            @if(isset($marketers) && count($marketers) > 0)
                @foreach($marketers as $marketer_id => $item)
                    <th style="text-align: center">{!! $item !!}</th>
                    <?php
                        $total[$marketer_id] = 0
                    ?>
                @endforeach
            @endif
            <?php
            $total[-1] = 0;$total[-2] = 0;
            ?>
            <th style="text-align: center">Hotline</th>
            <th style="text-align: center">Khách cũ</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($data) && !empty($data))
            @foreach($data as $name => $item)
                <tr>
                    <td><b>{{$name}}</b></td>
                    @if(isset($marketers) && count($marketers) > 0)
                        @foreach($marketers as $marketer_id => $marketer_data)
                            @if(isset($item[$marketer_id]['sum_order']) && $item[$marketer_id]['sum_order'] != null)
                                <td style="text-align: center">{{$item[$marketer_id]['sum_order']}}</td>
                                <?php
                                    $total[$marketer_id] += $item[$marketer_id]['sum_order'];
                                ?>
                            @else
                                <td style="text-align: center">0</td>
                            @endif
                        @endforeach
                    @endif

                    <td style="text-align: center">
                        {{$item[-1]['sum_order'] ?? 0}}
                        <?php
                            $total[-1] += $item[-1]['sum_order'];
                        ?>
                    </td>
                    <td style="text-align: center">
                        {{$item[-2]['sum_order'] ?? 0}}
                        <?php
                            $total[-2] += $item[-2]['sum_order'];
                        ?>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
    <thead class="head-bottom">
        <tr>
            <td><b>Khách mới</b></td>
                @if(!empty($old_customer))
                    @foreach($old_customer as $key => $customer)
                        <td style="text-align: center">{{ $total[$key] - $customer['sum_order'] ?? 0}}</td>
                    @endforeach
                @endif
            <td style="text-align: center"></td>
            <td style="text-align: center"></td>
        </tr>
        <tr>
            <td><b>Khách cũ</b></td>
                @if(!empty($old_customer))
                    @foreach($old_customer as $customer)
                        <td style="text-align: center">{{$customer['sum_order'] ?? 0}}</td>
                    @endforeach
                @endif
            <td style="text-align: center"></td>
            <td style="text-align: center"></td>
        </tr>
        <tr>
            <td><b>Tổng</b></td>
            @if(count($total) > 0)
                @foreach($total as $mkt)
                    <td style="text-align: center">{{$mkt}}</td>
                @endforeach
            @endif
        </tr>
    </thead>
</table>

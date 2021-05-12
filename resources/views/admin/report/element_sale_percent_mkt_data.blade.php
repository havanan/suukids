<table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
    <thead>
    <tr>
        <th width="15%"></th>
        @if(isset($marketers) && count($marketers) > 0)
            @foreach($marketers as $item)
            <th style="text-align: center">{!! $item !!}</th>
            @endforeach
        @endif
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
                        @if(isset($item[$marketer_id]['percent']) && $item[$marketer_id]['percent'] != null)
                                <td style="text-align: center">{{number_format($item[$marketer_id]['percent'],2)}} %</td>
                        @else
                            <td style="text-align: center">0.00 %</td>
                        @endif
                    @endforeach
                @endif
                <td style="text-align: center">{{number_format($item[-1]['percent'],2)}} %</td>
                <td style="text-align: center">{{number_format($item[-2]['percent'],2)}} %</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>

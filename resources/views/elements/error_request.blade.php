@if($errors->any())
    <div id="alert-message-content">
        <div class="alert alert-danger alert-dismissible mb-0">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Cảnh báo!</h5>

            @foreach ($errors->all() as $key=>$error)
                <i class="fa fa-exclamation-triangle"></i> {{$error}}<br>
            @endforeach
        </div>
    </div>
@endif

@if(!empty($messageSuccess))
    <div id="alert-message-content">
        <div class="alert alert-success alert-dismissible mb-0">
            {{ $messageSuccess }}
        </div>
    </div>
@endif

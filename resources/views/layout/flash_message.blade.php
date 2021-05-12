<script>
    @if($message = Session::get('error'))
        toastr.error('{{$message}}');
    @endif
    @if($message = Session::get('success'))
        toastr.success('{{$message}}');
    @endif

    @if($errors->any())
        @foreach ($errors->all() as $key=>$error)
            setTimeout(function(){
                {{--alertPopup('error','{{$error}}');--}}
                toastr.error('{{$error}}');
            }, {{$key*1000}});
        @endforeach
    @endif
</script>

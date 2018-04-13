@foreach (['danger', 'warning', 'success', 'info'] as $msg_type)
    @if(Session::has('alert-' . $msg_type))
        <?php $msg_content = ''; ?>
        @if(!is_array(Session::get('alert-' . $msg_type)))
            <?php $msg_content = Session::get('alert-' . $msg_type); ?>
        @else
            @foreach(Session::get('alert-' . $msg_type) as $msg)
                <?php $msg_content .= ($msg_content) ? ('<br/>'.$msg) : $msg; ?>
            @endforeach
        @endif
        <p class="alert alert-{{ $msg_type }}"><?php echo $msg_content ?> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
@endforeach
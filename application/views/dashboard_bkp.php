<section id="dashboard">
    <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>" id="csrf">
    <div class="row">
        <div class="col-md-2 sidebar vh-100 bg-secondary">
            <div class="row justify-content-center">
                <div class="profile bg-success rounded-circle text-white text-center d-flex justify-content-center align-items-center"
                    style="width: 100px;height:100px">
                    <?= $name ?>
                </div>
                <p class="text-white text-center"><?= $email ?></p>
            </div>
            <div class="friend mt-4 p-3">
                <?php foreach ($friends as $l) : ?>
                <div class="py-3">
                    <button class="btn btn-outline-info friend-btn w-100 text-center" id="friend_<?= $l->id ?>"
                        data-fid="<?= $l->id ?>"><?= $l->name ?></button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-md-10 chat-container vh-100">
            <div class="container-fluid my-5 d-none" id="chat-container">
                <div class="form-group">
                    <input type="message" class="form-control" id="msg" data-fid="">
                    <div id="error" class="invalid-feedback invalid">Please enter your message</div>
                    <button class="btn btn-sm btn-primary mt-2" id="send">send</button>
                </div>
                <div class="my-3">
                    <ul id="chat" class="list-group">

                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"
    integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$(function() {
    $('.friend-btn').on('click', function() {
        $('.friend-btn').removeClass('active')
        $(this).removeClass('btn-outline-warning')
        $(this).addClass('btn-outline-info')
        $(this).addClass('active')
        $('#chat').empty()
        let fid = $(this).attr('data-fid')
        $('#msg').attr('data-fid', fid)
        let csrf_value = $('#csrf').val()
        let data = {
            <?= $csrf_name ?>: csrf_value,
            fid: fid
        }
        $.ajax({
            url: '<?= base_url() ?>user/getchats',
            type: 'post',
            data,
            dataType: 'json',
            success: (data) => {
                $('#csrf').val(data.csrf_hash)
                data.chat.forEach(l => {
                    if (l.name == 'me') {
                        $('#chat').append('<li class="text-end list-group-item ">' +
                            l
                            .name + ': ' + l.message +
                            '&ensp;<code>(' +
                            l.created_at +
                            ')</code></li>')
                    } else {
                        $('#chat').append(
                            '<li class="text-left list-group-item ">' + l
                            .name + ': ' + l.message +
                            '&ensp;<code>(' + l
                            .created_at +
                            ')</code></li>')
                    }
                });
                $('#chat-container').removeClass('d-none')
            },
            error: (e) => {
                console.log(e);
            }

        })


    })


    // Create a new WebSocket.
    var socket = new WebSocket('ws://localhost:9500?<?= $user ?>');

    socket.onopen = () => {
        console.log('connetion establish');
    }

    $('#send').on('click', () => {
        let message = $('#msg').val()
        let fid = $('#msg').attr('data-fid')
        if (message != '') {
            socket.send(JSON.stringify({
                receiver: fid,
                message: message
            }))
            $('#msg').val('')
        } else {
            $('#error').show()
            $('#msg').focus()
        }
    })


    socket.onmessage = function(e) {
        let active = $('.friend-btn.active'),
            active_id = active.attr('data-fid')

        let data = JSON.parse(e.data);
        if (data.user == "me") {
            $('#chat').append('<li class="text-end list-group-item ">' + data.user + ': ' + data.msg +
                '&ensp;<code>(' + data
                .time +
                ')</code></li>')
        } else if (data.fid == active_id) {
            $('#chat').append('<li class="text-left list-group-item ">' + data.user + ': ' + data.msg +
                '&ensp;<code>(' +
                data.time +
                ')</code></li>')
        } else {
            let fid = '#friend_' + data.fid
            $(`${fid}`).removeClass('btn-outline-info')
            $(`${fid}`).addClass('btn-outline-warning')
            toastr.warning(data.user + ': ' + data.msg)
        }
    }
})
</script>
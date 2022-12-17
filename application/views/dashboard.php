<div id="container">
    <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>" id="csrf">
    <aside>
        <header>
            <input type="text" placeholder="search">
        </header>
        <ul class="friend">
            <?php foreach ($friends as $l) : ?>
            <li class="friend-btn" id="friend_<?= $l->id ?>" data-fid="<?= $l->id ?>" role="button">
                <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_01.jpg" alt="">
                <div>
                    <h2><?= $l->name ?></h2>
                    <h3 class="online-status">
                        <span class="status <?= $l->user_login_status ? 'green' : 'orange' ?>"></span>
                        <span class="status-text"><?= $l->user_login_status ? 'online' : 'offline' ?></span>
                    </h3>
                </div>
            </li>
            <?php endforeach; ?>
            <!-- <li>
                <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_02.jpg" alt="">
                <div>
                    <h2>Prénom Nom</h2>
                    <h3>
                        <span class="status green"></span>
                        online
                    </h3>
                </div>
            </li> -->
        </ul>
    </aside>
    <main>
        <div class="d-none" id="chat-container">
            <header>
                <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/chat_avatar_01.jpg" alt="">
                <div>
                    <h2>Chat with <span id="frnd-name"></span></h2>
                    <!-- <h3>already 1902 messages</h3> -->
                </div>
                <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/ico_star.png" alt="">
            </header>
            <ul id="chat"></ul>
            <footer class="row">
                <textarea placeholder="Type your message" id="msg" data-fid="" class="col-md-12"></textarea>
                <div class="col-md-12">
                    <!-- <span id="error" class="invalid-feedback invalid d-inline">Please enter your message</span> -->
                    <a href="" id="send" class="">Send</a>
                </div>
            </footer>
        </div>
    </main>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"
    integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$(function() {
    $('.friend-btn').on('click', function() {
        $('.friend-btn').find('img').removeClass('active')
        $(this).find('img').removeClass('unseen-msg')
        $(this).find('img').addClass('active')
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
                $('#frnd-name').html(data.frnd)
                data.chat.forEach(l => {
                    if (l.name == 'me') {
                        $('#chat').append(
                            `<li class="me">
                                <div class="entete">
                                    <h3>${l.created_at} </h3>
                                    <h2>${l.name}</h2>
                                    <span class="status blue"></span>
                                </div>
                                <div class="triangle"></div>
                                <div class="message">${l.message}</div>
                            </li>`
                        )
                    } else {
                        $('#chat').append(
                            `<li class="you">
                                    <div class="entete">
                                        <h3>${l.created_at} </h3>
                                        <h2>${l.name}</h2>
                                        <span class="status green"></span>
                                    </div>
                                    <div class="triangle"></div>
                                    <div class="message">${l.message}</div>
                                </li>`
                        )
                    }

                });
                $('#chat-container').removeClass('d-none')
                scrollBottm()
            },
            error: (e) => {
                console.log(e);
            }

        })


    })
    //scroll always bottom
    const scrollBottm = () => {
        let chat = document.getElementById('chat')
        chat.scrollTop = chat.scrollHeight;
    }



    // Create a new WebSocket.
    // var socket = new WebSocket('ws://192.168.1.5:9500?<?= $user ?>');
    var socket = new WebSocket('ws://localhost:9500?<?= $user ?>');

    socket.onopen = () => {
        // console.log('connetion establish');
    }
    //send message notice
    let timeout
    const msgNotifyTimeout = () => {
        timeout = setTimeout(() => {
            removeMsgNotify()
        }, 5000);
    }
    const removeMsgNotify = () => {
        $('#chat').find('li.msg-notify').remove()
        clearTimeout(timeout)
    }

    $('#msg').on('keyup', function() {
        let fid = $('#msg').attr('data-fid')
        socket.send(JSON.stringify({
            type: 'msg_notify',
            receiver: fid
        }))
    })


    //send message
    $('#send').on('click', (e) => {
        e.preventDefault()
        let message = $('#msg').val()
        let fid = $('#msg').attr('data-fid')
        if (message != '') {
            socket.send(JSON.stringify({
                type: 'msg',
                receiver: fid,
                message: message
            }))
            $('#msg').val('')
        } else {
            toastr.error('Please enter your msg.....')
            // $('#error').show()
            $('#msg').focus()
        }
    })

    socket.onmessage = function(e) {
        let active = $('.friend-btn.active'),
            active_id = active.attr('data-fid')
        let data = JSON.parse(e.data);
        if (data.type == 'online') {
            let fid = '#friend_' + data.fid
            let h3 = $(fid).find('.online-status')
            h3.find('.status-text').html('online')
            let status = h3.find('.status')
            status.removeClass('orange')
            status.addClass('green')
        }
        if (data.type == 'offline') {
            let fid = '#friend_' + data.fid
            let h3 = $(fid).find('.online-status')
            h3.find('.status-text').html('offline')
            let status = h3.find('.status')
            status.removeClass('green')
            status.addClass('orange')
        }
        if (data.type == 'msg_notify' && data.fid == active_id) {
            removeMsgNotify()
            $('#chat').append(
                `<li class='msg-notify'>
                    <div class="chat-bubble">
                        <div class="typing">
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                        </div>
                    </div>
                </li>`
            )
            scrollBottm()
            msgNotifyTimeout()
        }
        if (data.type == 'msg') {
            removeMsgNotify()
            if (data.user == "me") {
                $('#chat').append(
                    `<li class="me">
                    <div class="entete">
                        <h3>${data.time} </h3>
                        <h2>${data.user}</h2>
                        <span class="status blue"></span>
                    </div>
                    <div class="triangle"></div>
                    <div class="message">${data.msg}</div>
                </li>`
                )
                scrollBottm()
            } else if (data.fid == active_id) {
                $('#chat').append(
                    `<li class="you">
                        <div class="entete">
                            <h3>${data.time} </h3>
                            <h2>${data.user}</h2>
                            <span class="status green"></span>
                        </div>
                        <div class="triangle"></div>
                        <div class="message">${data.msg}</div>
                    </li>`
                )
                scrollBottm()
            } else {
                let fid = '#friend_' + data.fid
                $(`${fid} img`).addClass('unseen-msg')
                toastr.warning(data.user + ': ' + data.msg)
            }
        }
    }
})
</script>
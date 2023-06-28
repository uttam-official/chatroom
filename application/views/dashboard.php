<div id="frame">
    <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>" id="csrf">
    <div id="sidepanel">
        <div id="profile">
            <div class="wrap">
                <img id="profile-img" src="http://emilcarlsson.se/assets/mikeross.png" class="online" alt="" />
                <p><?= $name; ?></p>
                <i class="fa fa-chevron-down expand-button" aria-hidden="true"></i>
                <div id="status-options">
                    <ul>
                        <li id="status-online" class="active"><span class="status-circle"></span>
                            <p>Online</p>
                        </li>
                        <li id="status-away"><span class="status-circle"></span>
                            <p>Away</p>
                        </li>
                        <li id="status-busy"><span class="status-circle"></span>
                            <p>Busy</p>
                        </li>
                        <li id="status-offline"><span class="status-circle"></span>
                            <p>Offline</p>
                        </li>
                    </ul>
                </div>
                <div id="expanded">
                    <!-- <label for="twitter"><i class="fa fa-facebook fa-fw" aria-hidden="true"></i></label>
                    <input name="twitter" type="text" value="mikeross" />
                    <label for="twitter"><i class="fa fa-twitter fa-fw" aria-hidden="true"></i></label>
                    <input name="twitter" type="text" value="ross81" />
                    <label for="twitter"><i class="fa fa-instagram fa-fw" aria-hidden="true"></i></label>
                    <input name="twitter" type="text" value="mike.ross" /> -->
                    <label for="#logout">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                    </label>
                    <a href="/logout" id="logout">Logout</a>
                </div>
            </div>
        </div>
        <div id="search">
            <label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
            <input type="text" placeholder="Search contacts..." />
        </div>
        <div id="contacts">
            <ul>
                <?php foreach ($friends as $l) : ?>
                    <li class="contact" id="friend_<?= $l->id ?>" data-fid="<?= $l->id ?>">
                        <div class="wrap">
                            <span class="contact-status <?= $l->user_login_status ? 'online' : 'busy' ?>"></span>
                            <img src="http://emilcarlsson.se/assets/louislitt.png" alt="" />
                            <div class="meta">
                                <p class="name"><?= $l->name ?></p>
                                <p class="preview">
                                    <?= $l->user_login_status ? '<span class="text-success">online</span>' : '<span class="text-danger">offline</span>' ?>
                                </p>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="bottom-bar">
            <button id="addcontact"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i> <span>Add
                    contact</span></button>
            <button id="settings"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>Settings</span></button>
        </div>
    </div>
    <div class="content">
        <div class="contact-profile d-none">
            <img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
            <p id="frnd-name"></p>
            <div class="social-media">
                <i class="fa fa-facebook" aria-hidden="true"></i>
                <i class="fa fa-twitter" aria-hidden="true"></i>
                <i class="fa fa-instagram" aria-hidden="true"></i>
            </div>
        </div>
        <div class="messages">
            <ul id="chat">

            </ul>
        </div>
        <div class="message-input d-none">
            <div class="wrap">
                <input type="text" placeholder="Write your message..." id="msg" data-fid="" />
                <i class="fa fa-paperclip attachment" aria-hidden="true"></i>
                <button class="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
            </div>
        </div>
    </div>
</div>
<script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(function() {
        $('.contact').on('click', function() {
            $('.contact').removeClass('active')
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
                                `<li class="replies">
                                <img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
                                <p>${l.message}</p>
                                </li>`
                            )
                        } else {
                            $('#chat').append(
                                `<li class="sent">
                                <img src="http://emilcarlsson.se/assets/mikeross.png" alt="" />
                                <p>${l.message}</p>
                            </li>`
                            )
                        }

                    });
                    $('.contact-profile').removeClass('d-none')
                    $('.message-input').removeClass('d-none')
                    scrollBottm()
                },
                error: (e) => {
                    console.log(e);
                }

            })


        })


        // Create a new WebSocket.
        var socket = new WebSocket('ws://192.168.29.138:9500?<?= $user ?>');
        // var socket = new WebSocket('ws://localhost:9500?<?= $user ?>');

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

        function newMessage() {
            let fid = $('#msg').attr('data-fid')
            message = $(".message-input input").val();
            if ($.trim(message) == '' && fid) {
                return false;
            }
            //Send msg to server via socket 
            socket.send(JSON.stringify({
                type: 'msg',
                receiver: fid,
                message: message
            }))

            $('.message-input input').val(null);
            // $('.contact.active .preview').html('<span>You: </span>' + message);
            scrollBottm();
        };

        $('.submit').click(function() {
            newMessage();
        });

        $(window).on('keydown', function(e) {
            if (e.which == 13) {
                newMessage();
                return false;
            }
        });


        socket.onmessage = function(e) {
            let active = $('.contact.active'),
                active_id = active.attr('data-fid')
            let data = JSON.parse(e.data);
            if (data.type == 'online') {
                let fid = '#friend_' + data.fid
                $(fid).find('.preview').html('online')
                $(fid).find('.contact-status').removeClass('busy')
                $(fid).find('.contact-status').addClass('online')
            }
            if (data.type == 'offline') {
                let fid = '#friend_' + data.fid
                $(fid).find('.preview').html('offline')
                $(fid).find('.contact-status').removeClass('online')
                $(fid).find('.contact-status').addClass('busy')
            }
            if (data.type == 'msg_notify' && data.fid == active_id) {
                removeMsgNotify()
                $('#chat').append(
                    `<li class='msg-notify sent'>
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
                        `<li class="replies">
                                <img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
                                <p>${data.msg}</p>
                                </li>`
                    )
                    scrollBottm()
                } else if (data.fid == active_id) {
                    $('#chat').append(
                        `<li class="sent">
                            <img src="http://emilcarlsson.se/assets/mikeross.png" alt="" />
                            <p>${data.msg}</p>
                        </li>`
                    )

                    scrollBottm()
                } else {
                    let fid = '#friend_' + data.fid
                    $(`${fid} .contact-status`).addClass('away')
                    toastr.warning(data.user + ': ' + data.msg)
                }
            }
        }
    })



    function scrollBottm() {
        $(".messages").animate({
            scrollTop: $(document).height()
        }, "fast");
    }

    $("#profile-img").click(function() {
        $("#status-options").toggleClass("active");
    });

    $(".expand-button").click(function() {
        $("#profile").toggleClass("expanded");
        $("#contacts").toggleClass("expanded");
    });
</script>
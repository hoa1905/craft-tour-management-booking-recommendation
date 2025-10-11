var vnTComment = function () {
    var item_id = 0;
    var module = "news";
    var objResult = "comment_html";
    var objNav = "comment_nav";
    return {

        ShowWaiting: function () {
            $("#waiting").css("visibility", "visible");
        },
        HideWaiting: function () {
            $("#waiting").css("visibility", "hidden");
        },

        initFilter: function () {
            var $this = this;

            $(".vnt-review .toolsComment select").change(function () {
                $this.onSearch();
            });
        },

        do_Post: function () {
            var $this = this;
            var ok_post = true;
            var err_msg = '';

            if ($('.choose-evaluate').length) {
                var mark = $('input[name=com_mark]:checked').val();
                if (mark == '' || mark == 0 || mark == undefined) {
                    err_msg += '<div class="err-item">' + js_lang['err_mark_empty'] + '</div>';
                    ok_post = false;
                }
            }

            var content = $("#com_content").val();
            if (content.length < 10) {
                err_msg += '<div class="err-item">' + js_lang['err_conntent_minchar'] + '</div>';
                ok_post = false;
            }

            var name = $("#com_name").val()
            if (name == '') {
                err_msg += '<div class="err-item">' + js_lang['err_name_empty'] + '</div>';
                ok_post = false;
            }

            var re = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/gi;
            email = $("#com_email").val();
            if (email == '') {
                err_msg += '<div class="err-item">' + js_lang['err_email_empty'] + '</div>';
                ok_post = false;
            }

            if (email != '' && email.match(re) == null) {
                err_msg += '<div class="err-item">' + js_lang['err_email_invalid'] + '</div>';
                ok_post = false;
            }

            /*var phone = $("#com_phone").val();
            if (phone == '') {
                err_msg += '<div class="err-item">'+js_lang['err_phone_empty']+'</div>';
                ok_post = false;
            }*/


            if (ok_post) {

                $('#fComment').ajaxSubmit({
                    url: ROOT + "api/comment/post",
                    dataType: 'json',
                    headers: {
                        "Authorization": "Bearer " + API_KEY,
                        "csrf-token": $("#vnt_csrf_token").val()
                    },
                    beforeSend: function () {
                        $("#btnPostCom").attr('disabled', 'disabled');
                        $("#fComment").addClass('vnt-loading');
                    },
                    uploadProgress: function (event, position, total, percentComplete) {

                    },
                    success: function (rs) {

                        if (rs.success) {
                            $("#fComment")[0].reset();
                            //$(".content-info").stop().slideUp(700);

                            let data = rs.data;
                            console.log(data);

                            if (data.display == 1) {
                                var html = $("#comItemTmp").html();
                                $.each(data.item, function (key, value) {
                                    var key_rep = '\\[' + key + '\\]';
                                    html = html.replace(new RegExp(key_rep, 'g'), value);
                                });
                                $("#" + $this.objResult).append(html);
                            }

                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                html: rs.mess,
                                showConfirmButton: false,
                                timer: 1500
                            });

                        } else {
                            Swal.fire({ icon: 'error', title: js_lang['error'], html: rs.error_msg });
                        }
                        $("#fComment").removeClass('vnt-loading');
                        $("#btnPostCom").removeAttr('disabled');
                    }
                });
                return false;
            } else {
                Swal.fire({ icon: 'error', title: js_lang['error'], html: err_msg });
            }

            return false;
        },

        show_post_reply: function (cid) {
            $(".form_reply").remove();
            var html = '<form autocomplete="off" class="form_reply" id="form_reply_' + cid + '" method="post" onsubmit="return vnTComment.post_reply(' + cid + ');">';
            html += '<div class="reply-com"><textarea id="reply_content' + cid + '" name="reply_content' + cid + '" class="form-control" placeholder="' + js_lang['comment_reply_default'] + '"></textarea></div>';
            html += '<div class="reply-info">';
            html += '<div class="reply-name fl"><input type="text" name="reply_name' + cid + '" id="reply_name' + cid + '" class="form-control" placeholder="' + js_lang['your_name'] + '"  value=""></div>';
            html += '<div class="reply-button fr"><button name="btn-send" class="btn" type="submit" >' + js_lang['btn_send'] + '</button></div>';
            html += '</div>';
            html += '</form>';
            $("#com_reply" + cid).prepend(html);

            return false;
        },

        post_reply: function (cid) {

            var ok_post = 1;
            var com_mod = $("#com_mod").val();
            var com_csrf_token = $("#com_csrf_token").val();
            var name = $("#reply_name" + cid).val();
            var content = $("#reply_content" + cid).val();

            var err_msg = '';
            if (name == '') {
                err_msg += '<div class="err-item">' + js_lang['err_name_empty'] + '</div>';
                ok_post = 0;
            }
            if (content.length < 10) {
                err_msg += '<div class="err-item">' + js_lang['err_content_minchar'] + '</div>';
                ok_post = 0;
            }


            if (ok_post) {
                name = encodeURIComponent(name);
                content = encodeURIComponent(content);

                var mydata = "com_mod=" + com_mod + "&cid=" + cid + "&name=" + name + "&content=" + content + '&com_csrf_token=' + com_csrf_token + "&lang=" + lang;
                $.ajax({
                    async: true,
                    url: ROOT + "api/comment/reply",
                    dataType: 'json',
                    type: 'POST',
                    data: mydata,
                    headers: {
                        "Authorization": "Bearer " + API_KEY,
                        "csrf-token": $("#vnt_csrf_token").val()
                    },

                    beforeSend: function () {
                        $("#form_reply_" + cid).addClass('vnt-loading');
                    },
                    success: function (rs) {
                        $("#form_reply_" + cid).remove('vnt-loading');
                        if (rs.success) {
                            let data = rs.data;
                            console.log(data);

                            $("#form_reply_" + cid).remove();
                            if (data.display == 1) {
                                var html = $("#replyItemTmp").html();
                                $.each(data.item, function (key, value) {
                                    var key_rep = '\\[' + key + '\\]';
                                    html = html.replace(new RegExp(key_rep, 'g'), value);
                                });

                                $("#com_reply" + cid).prepend(html);
                            } else {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    html: rs.mess,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }

                        } else {
                            Swal.fire({ icon: 'error', title: js_lang['error'], html: rs.error_msg });
                        }
                    }
                })
            } else {
                Swal.fire({ icon: 'error', title: js_lang['error'], html: err_msg });
            }

            return false;

        },

        do_Like: function (cid) {
            var ok_post = 1;
            var com_mod = $("#com_mod").val();
            var com_csrf_token = $("#com_csrf_token").val();

            var like = 1;
            if ($("#com_like_" + cid).hasClass("active")) {
                like = 0;
            }


            if (ok_post) {

                var mydata = "mod=" + com_mod + "_comment&mod_id=cid&id=" + cid + "&like=" + like + '&csrf_token=' + com_csrf_token + "&lang=" + lang;
                $.ajax({
                    async: true,
                    dataType: 'json',
                    url: ROOT + "load_ajax.php?do=like",
                    type: 'POST',
                    data: mydata,
                    success: function (data) {
                        if (data.ok == 1) {
                            if (like == 1) {
                                $("#com_like_" + cid).addClass('active');
                            } else {
                                $("#com_like_" + cid).removeClass('active');
                            }

                            $("#com_like_" + cid).find('span').html(data.num_like);

                        } else {
                            jAlert(data.mess, js_lang['error']);
                        }
                    }
                })
            }

            return false;

        },



        initLoaded: function () {


        },

        goPager: function (p) {
            $("#com_page").val(p);
            this.onSearch()
        },

        BindDataItem: function (data) {

            var objShow = $("#" + this.objResult).html("");

            for (var item = 0; item < data.length; item++) {
                var html = $("#comItemTmp").html();
                $.each(data[item], function (key, value) {
                    var key_rep = '\\[' + key + '\\]';

                    if (key == 'list_reply') {
                        list_reply = '';
                        if (value.length) {
                            $.each(value, function (aKey, aValue) {
                                var html_reply = $("#replyItemTmp").html();
                                $.each(aValue, function (rKey, rVal) {
                                    var key_rep = '\\[' + rKey + '\\]';
                                    html_reply = html_reply.replace(new RegExp(key_rep, 'g'), rVal);
                                });
                                list_reply += html_reply;
                            });
                        }
                        value = list_reply;
                    }
                    html = html.replace(new RegExp(key_rep, 'g'), value);
                });
                objShow.append(html);
            }

        },



        loadResult: function (params) {
            var $this = this;

            this.ShowWaiting();
            $.ajax({
                url: ROOT + "api/comment/list",
                dataType: "json",
                type: "get",
                data: params,
                headers: {
                    "Authorization": "Bearer " + API_KEY,
                    "csrf-token": $("#vnt_csrf_token").val()
                },
                success: function (rs) {

                    if (rs.success) {
                        let data = rs.data;
                        $this.BindDataItem(data.items);
                        $("#" + $this.objNav).html(data.nav);
                    } else {
                        //$("#"+$this.objResult).html('<div class="noItem"><div class="alert alert-danger" role="alert">'+rs.mess+'</div></div>');
                        $("#" + $this.objResult).html('');
                        $("#" + $this.objNav).html('');
                    }
                    $this.HideWaiting();
                    $this.initLoaded();
                }
            });
            return true;

        },

        onSearch: function () {

            var params = {
                com_mod: this.module,
                com_id: this.item_id,
                p: $("#com_page").val(),
                isPageLoad: true
            };

            this.loadResult(params);
            return false;
        },


        init: function () {
            var $this = this;

            $(".choose-evaluate ul li").hover(function () {
                $(".choose-evaluate ul li").removeClass("star-red");
                $(this).addClass("star-red");
                $(this).prevAll().addClass("star-red");
                var $text = $(this).attr("title");
                $(this).parents(".choose-evaluate").find(".show-title").text($text);
                $(this).parents(".choose-evaluate").find(".show-title").addClass("show-active");
            }, function () {
                $(".choose-evaluate ul li").removeClass("star-red");
                $(this).parent().find("li.active").addClass("star-red");
                $(this).parent().find("li.active").prevAll().addClass("star-red");
                var $text = $(this).parent().find("li.active").attr("title");
                $(this).parents(".choose-evaluate").find(".show-title").text($text);
                $(this).parents(".choose-evaluate").find(".show-title").removeClass("show-active");
            });
            $(".choose-evaluate ul li").click(function () {
                $(".choose-evaluate ul li").removeClass("active");
                $(".choose-evaluate ul li").removeClass("star-red");
                $(this).addClass("active star-red");
                $(this).parent().find("li.active").prevAll().addClass("star-red");
                $(this).find("input:radio").prop('checked', true);
                var $text = $(this).attr("title");
                $(this).parents(".choose-evaluate").find(".show-title").text($text);
                $(this).parents(".choose-evaluate").find(".show-title").css({ opacity: 1 });
            });

            $("#com_content").focus(function () {
                $(this).css({ height: "115px" });
                $(this).parents(".w_content").find(".content-info").stop().slideDown(700);
            });

            $("#btnPostCom").click(function () {
                $this.do_Post();
            });

            $("#btnCloseCom").click(function () {
                $(this).parents(".w_content").find(".content-info").stop().slideUp(700);
            });

            this.onSearch();
        }

    };
}();
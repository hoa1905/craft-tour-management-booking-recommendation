$(document).ready(function () {
    $(".searchTop .icon").click(function () {
        if (!$(this).parents(".searchTop").hasClass("active")) {
            $(this).parents(".searchTop").addClass("active");
        }
        else {
            $(this).parents(".searchTop").removeClass("active");
        }
    });
    //
    $(window).bind("click", function (e) {
        var target = e.target;
        if (!$(target).parents(".searchTop").hasClass("active")) {
            $(".searchTop").removeClass("active");
        }
    });
    //
    $('.vhlinksub ul li').each(function () {
        var i = 0;
        var countsize = $(this).find("ul li").size();
        if (countsize > 0) {
            i += 1;
            $(this).find("a:first").wrap("<div class='m-sub'></div>");
            $(this).find(".m-sub:first").append("<div class='button-submenu'></div>");
        }
        if (!$(this).hasClass("active")) {
            $(this).find('ul:first').slideUp()
        }
        else {
            $(this).find('ul:first').slideDown()
        }
        $(this).find('.button-submenu').on('click', function () {
            if (!$(this).parents(".m-sub").parent("li").hasClass("active")) {
                $(this).parents(".m-sub").parent("li").addClass("active");
                $(this).parents('.m-sub').siblings('ul').stop().slideDown()
            }
            else {
                $(this).parents(".m-sub").parent("li").removeClass("active");
                $(this).parents('.m-sub').siblings('ul').stop().slideUp();
            }
        })
    })
    $('.likallsub .lkshow').on('click', function () {
        if (!$(this).parents(".vhcustomlk").hasClass("active")) {
            $(this).parents(".vhcustomlk").addClass("active");
            $(this).parents(".likallsub").addClass('active')
        }
        else {
            $(this).parents(".vhcustomlk").removeClass("active");
            $(this).parents(".likallsub").removeClass('active')
        }
    })
    //
    $(".fixed-menu").mnfixed({
        break: 992,
        zindex: 112,
        top: 0,
    });
    //
    $('.cclinkview a[href^="#"]').on('click', function (event) {
        var target = $($(this).attr('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 50
            }, 1000);
        }
        $('.hpviewcont .showview a').click()
    });
    //
    $(".cclinkview .vname").click(function () {
        if (!$(this).parents(".cclinkview").hasClass("active")) {
            $(this).parents(".cclinkview").addClass("active");
            $(this).parents(".cclinkview").find(".vhlink").stop().slideDown();
        }
        else {
            $(this).parents(".cclinkview").removeClass("active");
            $(this).parents(".cclinkview").find(".vhlink").stop().slideUp();
        }
    });
    $('.vhmenuFx a').on('click', function () {
        $('.hpmenufix').addClass('active')
    })
    $('.hpmenufix').each(function () {
        var that = $(this);
        that.find('.clmenu a').on('click', function () {
            $(this).parents('.hpmenufix').removeClass('active')
        })
        that.find('.ctmenu li a').on('click', function () {
            $(this).parents('.hpmenufix').removeClass('active')

        })

    })
    $('.hpmenufix .ctmenu a[href^="#"]').on('click', function (event) {
        var target = $($(this).attr('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 70
            }, 100);
        }
    });
    //
    $(window).scroll(function () {
        var scrollDistance = $(window).scrollTop();
        $(".viewtab").each(function (i) {
            if ($(this).offset().top - 80 <= scrollDistance) {

                $(".hpmenufix .ctmenu li.current").removeClass("current");
                $(".hpmenufix .ctmenu li").eq(i).addClass("current");
            }
        });
    }).scroll()

    $('.vvlinkview .vhlink a[href^="#"]').on('click', function (event) {
        var target = $($(this).attr('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 70
            }, 100);
        }
    });
    $(window).scroll(function () {
        var scrollDistance = $(window).scrollTop();
        $(".viewtab").each(function (i) {
            if ($(this).offset().top - 80 <= scrollDistance) {

                $(".vvlinkview .vhlink li.current").removeClass("current");
                $(".vvlinkview .vhlink li").eq(i).addClass("current");
            }
        });
    }).scroll()
    //
    $('.hpviewcont .thecontent').each(function () {
        var that = $(this);
        var innerhpc = that.innerHeight();
        that.parents(".hpviewcont").append('<div class="dsview"><div class="vhviewtt"><div class="showview"><a class="" href="javascript:void(0)" rel="nofollow">Xem thêm</a></div><div class="hideview"><a class="" href="javascript:void(0)" rel="nofollow">Thu gọn</a></div></div></div>')
        //$(window).on("load",function(){
        if (innerhpc > 250) {
            that.css({
                'height': 250,
                'overflow': 'hidden'
            })
            that.parents('.hpviewcont').addClass('active')
            that.siblings('.dsview').find('.showview').show();
            that.siblings('.dsview').find('.showview a').on('click', function () {
                that.css({
                    'height': 'auto',
                })
                that.parents('.hpviewcont').removeClass('active')
                that.siblings('.dsview').find('.showview').hide();
                that.siblings('.dsview').find('.hideview').show();
            })
            that.siblings('.dsview').find('.hideview a').on('click', function () {
                that.css({
                    'height': 250,
                })
                that.parents('.hpviewcont').addClass('active')

                that.siblings('.dsview').find('.showview').show();
                that.siblings('.dsview').find('.hideview').hide();
            })
        }
        //});
    })
    //
    $(".vhitemfaq .mctitle").click(function () {
        if (!$(this).parents(".vhitemfaq").hasClass("active")) {
            $(this).parents(".vhitemfaq").addClass("active");
            $(this).parents(".vhitemfaq").find(".mccont").stop().slideDown();
        }
        else {
            $(this).parents(".vhitemfaq").removeClass("active");
            $(this).parents(".vhitemfaq").find(".mccont").stop().slideUp();
        }
    });
    $(".fiter-comments .item .i-title").click(function (e) {
        if (!$(this).parents(".item").hasClass("active")) {
            $(this).parents(".item").siblings('.item').removeClass("active");
            $(this).parents(".item").addClass("active");
        } else {
            $(this).parents(".item").removeClass("active");
        }
    });
    //
    // $(".popup_edit").fancybox({
    //     padding     : 0,
    //     maxWidth	: 700,
    //     maxHeight   : '100%',
    //     width		: '98%',
    //     fitToView	: false,
    //     autoSize	: true,
    //     autoHeight  : true,
    //     autoWidth   : true,
    //     closeClick	: false,
    //     wrapCSS     : 'css_mota',
    //     openEffect	: 'elastic',
    //     closeEffect	: 'elastic'
    // });
    $(".popup_edit").fancybox({
        type: 'ajax',
        baseClass: 'vhstypop',
        toolsbar: false,
        smallBtn: true,
    });
    //
    $('.slupgradenm').slick({
        autoplay: true,
        autoplaySpeed: 4000,
        speed: 1000,
        arrows: true,
        dots: false,
        slidesToShow: 1,
        pauseOnHover: false,
    });
    $('.slupgradenh').slick({
        autoplay: true,
        autoplaySpeed: 5000,
        speed: 1000,
        arrows: true,
        dots: false,
        slidesToShow: 5,
        responsive: [
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 4,
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 3,
                }
            }
        ]
    });
    //
    $(".clpopmap a").fancybox({
        type: 'ajax',
        baseClass: 'vstypopmap',
        toolsbar: false,
        smallBtn: true,
        touch: false
    });
    //
    $(".clpopalbum a").fancybox({
        type: 'ajax',
        baseClass: 'vstypopimg',
        toolsbar: false,
        smallBtn: true,
        touch: false
    });
    //
    $(".clvideo a").fancybox({
        baseClass: 'vstypopvideo',
        toolsbar: false,
        smallBtn: true,
    });
    //
    $(".vhpopdepart .dpbackg, .vhpopdepart .vvclose").click(function () {
        $(this).parents(".vhpopdepart").removeClass("active");
    });
    $(".tpdeparthead .ifposi").click(function () {
        $(".vhpopdepart").addClass("active");
    });
    //
    $('.clchoose li').on("click", function () {
        var that = $(this);
        that.addClass('active');
        that.siblings().removeClass('active')
    })
    //
    /*$(".iquan .btn-down").click(function() {
        var $value = parseInt($(this).parents(".iquan").find(".ipt-qua").val());
        if (parseInt($value) >0) {
            $value = parseInt($value) - 1;
            $(this).parents(".iquan").find(".ipt-qua").val($value);
        }
    });
    $(".iquan .btn-up").click(function() {
        var $value = parseInt($(this).parents(".iquan").find(".ipt-qua").val());
        $value = parseInt($value) + 1;
        $(this).parents(".iquan").find(".ipt-qua").val($value);
    });*/
    //
    $('.clpopcalen').on('click', function () {
        $(this).parents(".vntinffix").addClass('active')
    });
    $('.vntinffix .ctbacks').on('click', function () {
        $(this).parents(".vntinffix").removeClass('active')
    });


    /****** 09/2024 ***********/

    $(".tpaccounts .nntitle, .tpaccounts .ddtitle").click(function () {
        if (!$(this).parents(".tpaccounts").hasClass("active")) {
            $(this).parents(".tpaccounts").addClass("active");
        }
        else {
            $(this).parents(".tpaccounts").removeClass("active");
        }
    });
    //
    var bgLay = $(".hpbgcloseacc");
    $('.hplogregis').each(function () {
        var that = $(this)
        var vregis = $('.clpopregis')
        var vlogin = $('.clpoplogin')
        vlogin.on('click', function () {
            $(this).addClass('active');
            that.addClass('active');
            that.find('.tppoplogin').addClass('active');
            that.find('.tppoplogin').siblings().removeClass('active');
            $(this).parents('.tpacclogin').addClass('active');
            $(this).parents('.tpacclogin').siblings().find(".content").stop().slideUp();
            $(this).parents('.tpacclogin').siblings().find(".lbb>input[type=radio]").prop("checked", false);
        })
        vregis.on('click', function () {
            that.addClass('active');
            that.find('.tppopregis').addClass('active');
            that.find('.tppopregis').siblings().removeClass('active');
            $(this).parents('.tpacclogin').addClass('active');
            $(this).parents('.tpacclogin').siblings().find(".content").stop().slideUp();
            $(this).parents('.tpacclogin').siblings().find(".lbb>input[type=radio]").prop("checked", false);
        })
        that.find('.clcloseacc, .lrbgpopacc').on('click', function () {
            that.removeClass('active');
            that.find('.tppoplogin').removeClass('active');
            that.find('.tppopregis').removeClass('active');
            $('.tpacclogin').removeClass('active')
        })
    })
    $('.hidepass').each(function () {
        var that = $(this);
        that.find('a').on('click', function () {
            if (!$(this).parents(".formFa").hasClass("changefa")) {
                $(this).parents(".formFa").addClass("changefa");
                $(this).parents('.formFa').find('input').attr('type', 'text');
            }
            else {
                $(this).parents(".formFa").removeClass("changefa");
                $(this).parents('.formFa').find('input').attr('type', 'password');
            }
        })
    })
    //
    $(".tpwrmethod .lbb").on("click", function () {
        $(this).parents(".tpwrmethod").siblings().find(".lbb>input[type=radio]").prop("checked", false);
        $(this).parents(".tpwrmethod").siblings().find(".content").stop().slideUp();
        if (!$(this).parents(".tpwrmethod").find(".lbb>input[type=radio]").prop("checked")) {
            $(this).parents(".tpwrmethod").find(".lbb>input[type=radio]").prop("checked", true);
            $(this).parents(".tpwrmethod").find(".content").stop().slideDown();
        }
        else {
            $(this).parents(".tpwrmethod").find(".lbb>input[type=radio]").prop("checked", false);
            $(this).parents(".tpwrmethod").find(".content").stop().slideUp();
        }
        return false;
    });
    $(".tpwrmethod .lbb").each(function () {
        if ($(this).parents(".tpwrmethod").find(".lbb>input[type=radio]").prop("checked")) {
            $(this).parents(".tpwrmethod").find(".content").show();
        }
    })



})
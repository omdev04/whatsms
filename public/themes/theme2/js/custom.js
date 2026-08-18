/*  jQuery Nice Select - v1.0
https://github.com/hernansartorio/jquery-nice-select
Made by Hernán Sartorio  */
!function (e) { e.fn.niceSelect = function (t) { function s(t) { t.after(e("<div></div>").addClass("nice-select").addClass(t.attr("class") || "").addClass(t.attr("disabled") ? "disabled" : "").attr("tabindex", t.attr("disabled") ? null : "0").html('<span class="current"></span><div class="list-wrp"><ul class="list"></ul></div>')); var s = t.next(), n = t.find("option"), i = t.find("option:selected"); s.find(".current").html(i.data("display") || i.text()), n.each(function (t) { var n = e(this), i = n.data("display"); s.find("ul").append(e("<li></li>").attr("data-value", n.val()).attr("data-display", i || null).addClass("option" + (n.is(":selected") ? " selected" : "") + (n.is(":disabled") ? " disabled" : "")).html(n.text())) }) } if ("string" == typeof t) return "update" == t ? this.each(function () { var t = e(this), n = e(this).next(".nice-select"), i = n.hasClass("open"); n.length && (n.remove(), s(t), i && t.next().trigger("click")) }) : "destroy" == t ? (this.each(function () { var t = e(this), s = e(this).next(".nice-select"); s.length && (s.remove(), t.css("display", "")) }), 0 == e(".nice-select").length && e(document).off(".nice_select")) : console.log('Method "' + t + '" does not exist.'), this; this.hide(), this.each(function () { var t = e(this); t.next().hasClass("nice-select") || s(t) }), e(document).off(".nice_select"), e(document).on("click.nice_select", ".nice-select", function (t) { var s = e(this); e(".nice-select").not(s).removeClass("open"), s.toggleClass("open"), s.hasClass("open") ? (s.find(".option"), s.find(".focus").removeClass("focus"), s.find(".selected").addClass("focus")) : s.focus() }), e(document).on("click.nice_select", function (t) { 0 === e(t.target).closest(".nice-select").length && e(".nice-select").removeClass("open").find(".option") }), e(document).on("click.nice_select", ".nice-select .option:not(.disabled)", function (t) { var s = e(this), n = s.closest(".nice-select"); n.find(".selected").removeClass("selected"), s.addClass("selected"); var i = s.data("display") || s.text(); n.find(".current").text(i), n.prev("select").val(s.data("value")).trigger("change") }), e(document).on("keydown.nice_select", ".nice-select", function (t) { var s = e(this), n = e(s.find(".focus") || s.find(".list .option.selected")); if (32 == t.keyCode || 13 == t.keyCode) return s.hasClass("open") ? n.trigger("click") : s.trigger("click"), !1; if (40 == t.keyCode) { if (s.hasClass("open")) { var i = n.nextAll(".option:not(.disabled)").first(); i.length > 0 && (s.find(".focus").removeClass("focus"), i.addClass("focus")) } else s.trigger("click"); return !1 } if (38 == t.keyCode) { if (s.hasClass("open")) { var l = n.prevAll(".option:not(.disabled)").first(); l.length > 0 && (s.find(".focus").removeClass("focus"), l.addClass("focus")) } else s.trigger("click"); return !1 } if (27 == t.keyCode) s.hasClass("open") && s.trigger("click"); else if (9 == t.keyCode && s.hasClass("open")) return !1 }); var n = document.createElement("a").style; return n.cssText = "pointer-events:auto", "auto" !== n.pointerEvents && e("html").addClass("no-csspointerevents"), this } }(jQuery);

$(document).ready(function () {

    /******  Nice Select  ******/
    $('select').niceSelect();

    /********* Search popup ********/
    $('.search-drp-btn').on('click', function (e) {
        e.preventDefault();
        $(".header-search-form").toggleClass("active");
    });

    /****  TAB Js ****/
    $("ul.tabs li").click(function () {
        var $this = $(this);
        var $theTab = $(this).attr("data-tab");
        if ($this.hasClass("active")) {
        } else {
            $this
                .closest(".tabs-wrapper")
                .find("ul.tabs li, .tabs-container .tab-content")
                .removeClass("active");
            $(
                '.tabs-container .tab-content[id="' +
                $theTab +
                '"], ul.tabs li[data-tab="' +
                $theTab +
                "]"
            ).addClass("active");
        }
        $(this).addClass("active");
    });
    $("ul.list-tabs li").click(function () {
        var $this = $(this);
        var $theTab = $(this).attr("data-tab");
        if ($this.hasClass("active")) {
        } else {
            $this
                .closest(".tabs-wrapper")
                .find("ul.list-tabs li, .tabs-container .list-tab-content")
                .removeClass("active");
            $(
                '.tabs-container .list-tab-content[id="' +
                $theTab +
                '"], ul.list-tabs li[data-tab="' +
                $theTab +
                "]"
            ).addClass("active");
        }
        $(this).addClass("active");
    });

    // quickview-modal
    $('.qv-btn').on('click', function (e) {
        $('#quickview-modal').addClass('active');
        $('body').addClass('no-scroll');
    });
    $('.modal-close').on('click', function (e) {
        $('#quickview-modal').removeClass('active');
        $('body').removeClass('no-scroll');
    });

    // variant-modal
    $('.variant-btn').on('click', function (e) {
        $('#variant-modal').addClass('active');
        $('body').addClass('no-scroll');
    });
    $('.modal-close').on('click', function (e) {
        $('#variant-modal').removeClass('active');
        $('body').removeClass('no-scroll');
    });

    // checkout-modal
    $('.checkout-btn').on('click', function (e) {
        $('#checkout-modal').addClass('active');
        $('body').addClass('no-scroll');
    });
    $('.modal-close, .login-modal-btn').on('click', function (e) {
        $('#checkout-modal').removeClass('active');
        $('body').removeClass('no-scroll');
    });

    // login-modal
    $('.login-modal-btn').on('click', function (e) {
        $('#login-modal').addClass('active');
        $('body').addClass('no_scroll');
    });
    $('.modal-close, .register-modal-btn').on('click', function (e) {
        $('#login-modal').removeClass('active');
        $('body').removeClass('no_scroll');
    });

    // register-modal
    $('.register-modal-btn').on('click', function (e) {
        $('#register-modal').addClass('active');
        $('body').addClass('no-scroll');
    });
    $('.modal-close, .login-modal-btn').on('click', function (e) {
        $('#register-modal').removeClass('active');
        $('body').removeClass('no-scroll');
    });

    // remove-item-modal
    $('.cart-item .remove-item').on('click', function (e) {
        $('#remove-item-modal').addClass('active');
        $('body').addClass('no-scroll');
    });
    $('.cancel-btn').on('click', function (e) {
        $('#remove-item-modal').removeClass('active');
        $('body').removeClass('no-scroll');
    });

    // order-modal
    $('.ac-viewbtn').on('click', function (e) {
        $('#orderview-modal').addClass('active');
        $('body').addClass('no-scroll');
    });
    $('.modal-close').on('click', function (e) {
        $('#orderview-modal').removeClass('active');
        $('body').removeClass('no-scroll');
    });

    // cart-modal
    $('.cart-btn').on('click', function (e) {
        $('#cart-modal').addClass('active');
        $('body').addClass('no-scroll');
    });
    $('.modal-close').on('click', function (e) {
        $('#cart-modal').removeClass('active');
        $('body').removeClass('no-scroll');
    });

    //profile-modal
    $('.my-profile').on('click', function (e) {
        $('#profileview-modal').addClass('active');
        $('body').addClass('no-scroll');
    });
    $('.modal-close').on('click', function (e) {
        $('#profileview-modal').removeClass('active');
        $('body').removeClass('no-scroll');
    });



    /** quickview slider **/
    $('.qv-main-slider').slick({
        dots: false,
        arrows: false,
        infinite: true,
        speed: 800,
        loop: true,
        slidesToShow: 1,
        asNavFor: '.qv-thumb-slider',
    });
    $('.qv-thumb-slider').slick({
        dots: false,
        arrows: false,
        speed: 800,
        slidesToScroll: 1,
        touchMove: true,
        focusOnSelect: true,
        loop: true,
        infinite: true,
        slidesToShow: 3,
        asNavFor: '.qv-main-slider',
    });

});

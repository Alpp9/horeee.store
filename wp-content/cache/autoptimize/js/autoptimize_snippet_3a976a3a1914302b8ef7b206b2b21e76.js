!function(o){o.fn.UItoTop=function(n){var i=!1,t=o.extend({text:"Back to Top",min:300,containerID:"back-top"},n),e="#"+t.containerID;window.innerWidth>1024&&(o("body").append('<a href="#top" id="'+t.containerID+'">'+t.text+"</a>"),o(e).on("click.UItoTop",function(){return window.scrollTo({top:0,behavior:"smooth"}),o(e).removeClass("scroll-btn-visible"),!1}),o(window).on("scroll",function(){var n=o(window).scrollTop();n>t.min&&!i?(o(e).addClass("scroll-btn-visible"),i=!0):n<=t.min&&(o(e).removeClass("scroll-btn-visible"),i=!1)}))}}(jQuery);
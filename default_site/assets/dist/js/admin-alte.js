
$(function () {
    if ($('body').hasClass('sidebar-collapse')) {
        $('.main-sidebar .user-panel .img').width(30).show();
    }

    $(document).on('collapsed.pushMenu', function (event) {
        $('.main-sidebar .user-panel .img').width(30);
        document.cookie = "sidebar_collapse=1; path=/";
    });

    $(document).on('expanded.pushMenu', function (event) {
        $('.main-sidebar .user-panel .img').width(45);
        document.cookie = "sidebar_collapse=0; path=/";
        $('.navbar-collapse').collapse('hide');
    });

    $(document).on('click', "[data-toggle='control-sidebar']", function (event) {
        var controlSidebar = $('.control-sidebar');
        if (controlSidebar.hasClass('control-sidebar-open')) {
            $('.navbar-collapse').collapse('hide');
            if (!controlSidebar.find('.control-sidebar-tabs li.active').length) {
                controlSidebar.find("[href='#control-sidebar-settings-tab']").trigger('click');
            }
        }
    });
});


ucm.upgrade = {
    upgrade_url: '',
    upgrade_plugins: [],
    upgrade_post_data: {
        install_upgrade: 'yes',
        via_ajax: 'yes'
    },
    lang:{
        processing:'Processing, please wait...'
    },
    init: function(){
        $('#upgrade_start').click(function(){
            $('.update_checkbox input').each(function(){
                var tr = $(this).parents('tr').first();
                if($(this)[0].checked) {
                    $('.update_progress',tr).slideDown();
                    ucm.upgrade.upgrade_plugins.push({
                        plugin_name: $(this).data('plugin'),
                        html: tr.find('.update_progress')
                    });
                }
                $(this).attr('disabled','disabled');
            });
            if(ucm.upgrade.upgrade_plugins.length > 0){
                $(this).val(ucm.upgrade.lang.processing);
                ucm.upgrade.run_next();
            }else{
                alert('Please select at least one update.');
            }
        });
    },
    current_upgrade:0,
    run_next: function(){
        if(typeof ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade] == 'undefined') {
            // completed all upgrades!
            alert('Done');
            window.location.href=window.location.href + (window.location.href.search(/\?/) ? '&' : '?') + 'done';
        }else {
            var post_data = {};
            for (var i in ucm.upgrade.upgrade_post_data) {
                if (ucm.upgrade.upgrade_post_data.hasOwnProperty(i)) {
                    post_data[i] = ucm.upgrade.upgrade_post_data[i];
                }
            }
            post_data.plugin_name = ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].plugin_name;
            post_data.doupdate = [ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].plugin_name];
            $.ajax({
                url: ucm.upgrade.upgrade_url,
                type: 'POST',
                dataType: 'json',
                data: post_data,
                success: function (d) {
                    // did it work? update the status..
                    if (typeof d.success != 'undefined') {
                        $(ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].html).html(d.message).addClass('success');
                    } else {
                        $(ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].html).html(d.message).addClass('error');
                    }
                    // do the processing...
                    //process_upgrade
                    $.ajax({
                        url: ucm.upgrade.upgrade_url,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            process_upgrade: d.plugin_name,
                            via_ajax: 1
                        },
                        success: function (d) {
                            // did it work? update the status..
                            if (typeof d.success != 'undefined') {
                                $(ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].html).append(d.message).addClass('success');
                            } else {
                                $(ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].html).append(d.message).addClass('error');
                            }
                            // do the processing...
                            //process_upgrade

                            ucm.upgrade.current_upgrade++;
                            ucm.upgrade.run_next();
                        },
                        error: function (d) {
                            alert('Failed to process ' + ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].plugin_name + '. Please try again.');
                            $(ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].html).append('Failed Final Processing').addClass('error');
                            ucm.upgrade.current_upgrade++;
                            ucm.upgrade.run_next();
                        }
                    });
                },
                error: function (d) {
                    alert('Failed to upgrade ' + ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].plugin_name + '. Please try again.');
                    $(ucm.upgrade.upgrade_plugins[ucm.upgrade.current_upgrade].html).html('Failed').addClass('error');
                    ucm.upgrade.current_upgrade++;
                    ucm.upgrade.run_next();
                }
            });
        }
    }
};
// JavaScript Document


// in a function so when we add multiple rows it can re-run to add calendars

var ucm = {

    social: {},
    messages: [],
    errors: [],
    lang:{
        ok: 'OK',
        mins: 'mins',
        hr: 'hr',
        hrs: 'hrs',
        am: 'am',
        AM: 'AM',
        pm: 'pm',
        PM: 'PM'
    },
    add_message: function(message){
        this.messages.push(message);
    },
    add_error: function(message){
        this.errors.push(message);
    },
    display_messages: function(fadeout){
        var html = '';
        for(var i in this.messages){
            html += '<div class="ui-widget" style="padding-top:10px;"><div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>';
            html += this.messages[i] + '<br/>';
            html += '</p> </div> </div>';
        }
        for(var i in this.errors){
            html += '<div class="ui-widget" style="padding-top:10px;"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>';
            html += this.errors[i] + '<br/>';
            html += '</p> </div> </div>';
        }
        $('#message_popdown').html(html);
        $('#message_popdown').fadeIn();
        this.messages=new Array();
        this.errors=new Array();
        if(typeof fadeout != 'undefined' && fadeout){
            setTimeout(function(){
                $('#message_popdown').fadeOut();
            },4000);
        }
    },
    init_buttons: function(){
        $('.submit_button').button();
        $('.uibutton').button();
    },
    load_calendars: function(){
        /*if(typeof js_cal_format == 'undefined'){
            var js_cal_format = 'dd/mm/yy';
        }*/
        $('.date_field').datepicker( {
            /*dateFormat: js_cal_format,*/
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            showAnim: false,
            constrainInput: false/*,
            yearRange: '-90:+3'*/

        });
        if(typeof jQuery.fn.timepicker != 'undefined'){
            $('.time_field').timepicker( {
                'timeFormat': 'g:ia',
                'lang': { decimal: '.', mins: ucm.lang.mins, hr: ucm.lang.hr, hrs: ucm.lang.hrs, am: ucm.lang.am, AM: ucm.lang.AM, pm: ucm.lang.pm, PM: ucm.lang.PM }
            });
        }
        $('.date_time_field').datepicker( {
            /*dateFormat: js_cal_format,*/
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            showTime: true,
            time24h: true,
            showAnim: false,
            constrainInput: false/*,
            yearRange: '-90:+3'*/
        });
    },
    init_interface: function(){

        // ui stuff:
        ucm.init_buttons();
        ucm.load_calendars();
        // tables:
        $('.tableclass_rows').each(function(){
            // check if there's a row action here.
            $('.row_action',this).each(function(){
                var row_action = this;
                var alink = $('a',row_action)[0];
                if(typeof alink == 'undefined')return;
                var row = $(this).parents('tr')[0];
                $(row).hover(function(){$(this).addClass('hover')},function(){$(this).removeClass('hover')});
                $('a',row).click(function(){
                    row_clicking=true;
                });
                $('input',row).click(function(){
                    row_clicking=true;
                });
                $(row).click(function(){
                    if(row_clicking)return true;
                    row_clicking=true;
                    if(!move_checking){
                        move_checking = true; // so we only do it once.
                        $('body').mousemove(function(){
                            row_clicking = false;
                        });
                    }
                    if(typeof alink != 'undefined'){
                        //$(alink).click();
                        /*var foo = $(alink).attr('href');
                        console.debug(row_action);
                        console.debug(alink);
                        console.debug(foo);return;
                        if(foo != '' && foo != '#'){
                            window.location.href=foo;
                        }*/
                        if(typeof alink.href != 'undefined' && alink.href != '' && alink.href != '#'){
                            window.location.href=alink.href;
                        }
                    }
                });
            });
        });

        // ajax search.
        /*$('#ajax_search_text').val(ajax_search_ini);*/
        $('#ajax_search_text').keyup(function(e){

            if($(this).val() == ''){
                $('#quick_search_placeholder div').fadeIn('fast');
            }else{
                $('#quick_search_placeholder div').fadeOut('fast');
            }


            if(!e)e = window.event;
            if(e.keyCode == 27){
                $('#ajax_search_result').hide();
                return;
            }
            if($(this).val()=='')return;
            try{ajax_search_xhr.abort();}catch(err){}
            ajax_search_xhr = $.ajax({
                type: "POST",
                url: ajax_search_url,
                data: {
                    ajax_search_text:$(this).val()
                },
                success: function(result){
                    if(result == ''){
                        $('#ajax_search_result').hide();
                    }else{
                        $('#ajax_search_result').html(result).show();
                    }
                }
            });
        });

    },
    open_help: function(help_id){
        var options = {
            autoOpen: true,
            height: 260,
            width: 300,
            modal: true,
            buttons: {}
        };
        options.buttons[ucm.lang.ok] = function() {
                        $(this).dialog('close');
                    };
        $("#help_"+help_id).dialog(options);
    }

};

var load_calendars = ucm.load_calendars;
var init_interface = ucm.init_interface;
var open_help = ucm.open_help;

if (!window.console) console = {};
console.log = console.log || function(){};
var row_clicking = false,move_checking = false;



function open_shut(id){
	var bloc = document.getElementById('show_hide_'+id);
	if(bloc){
		if(bloc.style.display=='none'){
			bloc.style.display='';
		}else{
			bloc.style.display='none';
		}
	}
	return false;
}
function set_add_del(id){
	$("#"+id+' .remove_addit').show();
	$("#"+id+' .add_addit').hide();
	$("#"+id+' .add_addit:last').show();
	$("#"+id+" .dynamic_block:only-child > .remove_addit").hide();
	$("#"+id+' .remove_addit').data('theid',id);
	$("#"+id+' .add_addit').data('theid',id);
}
function selrem(clickety){
	var id = $(clickety).data('theid');
	$(clickety).parents('.dynamic_block').remove();
	set_add_del(id); 
	return false;
}
function seladd(clickety){
	var id = $(clickety).data('theid');
	//var box = $('#'+id+' .dynamic_block:last').clone(true);
	var x=0,old_names=[];
	// these pointless looking loops are because IE doesn't handle
	// cloning the name="" part of dynamic input boxes very well... ?
	$('input',$(clickety).parents('.dynamic_block')).each(function(){
		old_names[x++] = $(this).attr('name');
	});
	$('select',$(clickety).parents('.dynamic_block')).each(function(){
		old_names[x++] = $(this).attr('name');
	});
	var box = $(clickety).parents('.dynamic_block').clone(true);
	x = 0;
	$('input',box).each(function(){
        if(typeof old_names[x] == 'string'){
		    $(this).attr('name', old_names[x]);
        }
        x++;
	});
	$('select',box).each(function(){
        if(typeof old_names[x] == 'string'){
            $(this).attr('name', old_names[x]);
        }
        x++;
	});
	$('input',box).val('');
	$('.dynamic_clear:input',box).val('');
	$('.dynamic_clear',box).html('');
	//$(clickety).after(box);
	$('#'+id+' .dynamic_block:last').after( box); 
	set_add_del(id); 
	load_calendars();
	return false;
}
function dynamic_select_box(element){
    if($(element).val()=='create_new_item'){
        var current_val = $(element).val();
        if(current_val=='create_new_item')current_val = '';
        var id = $(element).attr('id');
        if(typeof id == 'object')id = $(element).prop('id');
        var name = $(element).attr('name');
        if(typeof name == 'object')name = $(element).prop('name');
        var html = '<input type="text" name="'+name+'" id="'+id+'" value="'+current_val+'">';
        // add a new input box.
        $(element).after('<span id="dynamic_select_box_placeholder"></span>');
        $(element).remove();
        var box = $(html);
        $('#dynamic_select_box_placeholder').after(box).remove();
        box[0].focus();
        box[0].select();
    }
}

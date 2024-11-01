(function( $ ) {
    $(window).load(function() {
        "use strict";
   // $(".experiment_url").chosen();
        jQuery('.experiment_url').keyup(function(event) {
           jQuery(this).attr('autocomplete','off');
           if(jQuery(this).attr('readonly') == 'readonly'){
               return false;
           }
           jQuery('.experiment_url').css('background','url('+inputAjaxloaderimg+') no-repeat right center');
           var searchTerm = jQuery(this).val();
           var data = {
                'action': 'ab_testing_post_name_search',
                'term': searchTerm
            };
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('.url-ajax-search').fadeIn().html(response);
                jQuery('.experiment_url').css('background','none');
            });
           });
           jQuery('body').on('click', '.url-ajax-search li', function() {
               var url = jQuery(this).data('url');
               jQuery('.experiment_url').val(url);
               jQuery('.url-ajax-search').html('');
               if($(this).attr('id') != 'no-found') {
                   jQuery('.variation-outer').show();
                   jQuery('.url-targeting-main').show();
                   jQuery('.targeting_url').val(url);
                   jQuery('.experiment_url').attr('readonly','readonly');
               }else{
                   jQuery('.variation-outer').hide();
                   jQuery('.url-targeting-main').hide();
                   jQuery('.targeting_url').val('');
               }
           });

         jQuery('body').on('click', '.clear_exp_url', function() {
             jQuery('.experiment_url').val('');
             jQuery('.url-ajax-search').html('');
             jQuery('.experiment_url').removeAttr('readonly');
             jQuery('.variation-outer').hide();
             jQuery('.url-targeting-main').hide();
             jQuery('.targeting_url').val('');

           });

    /***** Start ADD Variation ********/
    $(document).on('click', '.wabt-table-main .add_variation', function(){
        var currentVarId =  $(this).parents('.variation-outer').attr('id');
  	var varId = currentVarId.replace("variation","");
  	var nextVarId = parseInt(varId) +1;
  	$('#variation'+varId+' .add_variation').remove();
  	$('#variation' + varId + ' .variation-detail').append('<input type="button" name="" class="del_variation delete_cheaf" value="">');
  	$('#variation'+varId).after('<tr class="variation-outer" id="variation'+nextVarId+'"><th class="titledesc" scope="row"><label for="variation">Variation '+nextVarId+':<span class="required-star">*</span></label></th><td class="forminp"> <div class="variation-detail"><div class="var-input"><input type="text" name="variation[variation_name][]" value="" class="VariationName" placeholder="Variation Name" required="1"> </div><div class="var-input"><input type="number" min="1" max="100" name="variation[percentage][]" value="" placeholder="Percentage" class="persantage_value" required="1"></div><input type="button" name="" value="Edit" class="edit_variation" ><input type="button" name="" class="del_variation delete_cheaf" value=""><input type="button" name="" class="add_variation edit-page-variation" value="+"></div><div class="edit-variation"><textarea rows="10" cols="100" name="variation[action][]" placeholder="Enter javascript"></textarea></div></td></tr>');
    });
    /*****  End ADD Variation  ********/

    /***** Start Delete Variation ********/
    $(document).on('click', '.wabt-table-main .del_variation', function(){
        if (confirm("Are you sure want to delete this?")){
            var data_id = $(this).data('id');
            $(this).parents('.variation-outer').remove();
            if(jQuery('.variation-outer').length < 2){
                $('.del_variation').remove();
            }
            var varID= $( ".variation-outer" ).last().attr('id');
            jQuery("#"+varID+" .variation-detail").append('<input type="button" name="" class="add_variation edit-page-variation" value="+">');
            if(data_id != 'undefined') {
                var data = {
                    'action': 'ab_testing_delete_variation_edit_page',
                    'data_id': data_id
                };
                jQuery.post(ajaxurl, data, function (response) {

                });
            }
        }
    });

    /***** End Delete Variation ********/

    /*****  Add URL Targeting  ********/
    $(document).on('click', '.wabt-table-main .add_url', function(){
        var currentUrlId =  $(this).parents('.url-targeting-outer').attr('id');
        var urlId = currentUrlId.replace("url","");
        var nexturlId = parseInt(urlId) +1;
        $('#url'+urlId+' .add_url').remove();
        $('#url'+urlId+' span').append('<input type="button" name="" class="del_url" value="">');
        //$('#url'+urlId).after('test');
        $('.url-targeting-main #url'+urlId).after('<div class="url-targeting-outer" id="url'+nexturlId+'"><span><input type="text" name="targeting_url[]" value="" required="1"><input type="button" name="" value="+" class="add_url"> </span></div>');
    });
    /*****  Add URL Targeting  ********/

    /***** Delete URL Targeting ********/
    $(document).on('click', '.wabt-table-main .del_url', function(){
        if (confirm("Are you sure want to delete this?")){
            $(this).parents('.url-targeting-outer').remove();
        }else{
            return false;
        }
    });
    /***** End Delete URL Targeting ********/

    /***** Delete Single Experiments ********/
    $(document).on('click', 'a.detete-single-experiment', function(){
        if (confirm('Are You Sure You Want to Delete?')) {
            var id = $(this).attr('id');
            var data = {
                'action': 'ab_testing_single_delete_experiment',
                'id': id
            };
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                location.reload();
            });
        }
    });
    /***** END Multiple Experiments ********/

    /***** Select All Checkbox ********/
    $(document).on('click', '.check-all-experiment', function(){
        $('input.multiple_delete_exp:checkbox').not(this).prop('checked', this.checked);
    });
    /***** END Select All Checkbox ********/

    /***** Delete Multiple Experiments ********/
    $(document).on('click', 'a.detete-multiple-experiment', function(){
            if ($('.multiple_delete_exp:checkbox:checked').length == 0) {
                alert('Please select at least one checkbox');
                return false;
            }
            if (confirm('Are You Sure You Want to Delete?')) {
                var allVals = [];
                $(".multiple_delete_exp:checked").each(function() {
                    allVals.push($(this).val());
                });
                var data = {
                    'action': 'ab_testing_multiple_delete_experiments',
                    'allVals': allVals
                };
                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(ajaxurl, data, function(response) {
                    if (response == 1) {
                        alert('Delete Successfully');
                        $(".multiple_delete_exp").prop("checked", false);
                        location.reload();
                    }
                });
            }
        });
    /***** END Multiple Experiments ********/
    /***** Status Change ******/
    $(document).on('click', '.experiment-status', function(){
        var confirm_st = confirm("Are you sure you want to change?");
        if(confirm_st==true){
            expStatus = $(this).data('status');
            exId = $(this).data('id');
            var data = {
                'action': 'ab_testing_change_experiment_status',
                'status': expStatus,
                'exId':exId
            };
            jQuery.post(ajaxurl, data, function(response) {
                 location.reload();
            });
        }
    });
    /**** status change end *****/
    /*****  Edit Variation  ********/
    $(document).on('click', '.wabt-table-main .edit_variation', function(){
        var variationId = $(this).closest('tr').attr('id');
        var exp_name = $('.wabt-table-main .experiment_name');
        var vari_name = $('.wabt-table-main tr#'+variationId+' .VariationName');
        var exp_url = $('.wabt-table-main .experiment_url');
        var var_per = $('.wabt-table-main tr#'+variationId+' .persantage_value');

        if( (exp_name.val() != '' ) && (vari_name.val() != '') && (exp_url.val() != '') && (var_per.val() != '')){
            $('body').append('<div class="loader"><img src="'+ajaxloaderimg+'" alt="loader"></div>');
            var actionVal =  $(this).parents('.variation-outer').find('.edit-variation textarea').val();
            var editedId =  $(this).parents('.variation-outer').attr("id");
            var expUrl = $('.experiment_url').val();
            expUrl = expUrl+'?edit_exp=true';
            var exp_id = $(this).attr('id');
            $.get( expUrl, function( result ) {
            result = result.replace(/src=\"wp-content/g, 'src="/wp-content');
            $('.loader').remove();
            $('#wpwrap').hide();
            $('body').after('<script>(function( $ ) {'+actionVal+'})( jQuery );</script><div class="ab_edit_popup" style="display:none;"><span class="ab_remove_div">remove</span></div><div class="variation-popup-main"><div class="variation-update"><button type="button" class="btn btn-default save">SAVE</button><a href="javascript:void(0);" class="close"></a></div><div class="variation-editor"><div class="variation-expand">Show Code</div><textarea>'+actionVal+'</textarea></div><div class="variation-content">'+result+'</div></div><input type="hidden" value="'+editedId+'" class="editedId"></div>');
            })
            .fail(function() {
                $('.loader').remove();
                alert( "Please Enter Valid URL" );
            });
        }else{
             (exp_name.val() == "") ? exp_name.focus() : "";
             (vari_name.val() == "") ? vari_name.focus() : "";
             (exp_url.val() == "") ? exp_url.focus() : "";
             (var_per.val() == "") ? var_per.focus() : "";
        }
    });
    /*****  Edit Variation  ********/
    /*****  Edit Variation close ********/
    $(document).on('click', '.variation-popup-main .close', function(){
        $('.variation-popup-main').remove();
        $('#wpwrap').show();
    });
    /*****  Edit Variation close ********/

    /*****  Show code In edit view ********/
    $(document).on('click', '.variation-popup-main .variation-expand', function(){
        if((this).innerHTML == 'Show Code') {
            $(".variation-popup-main").addClass("active");
            $( this ).html( 'Hide Code' );
        } else {
            $(".variation-popup-main").removeClass("active");
            $( this ).html( 'Show Code' );
        }
    });
    /*****  Show code In edit view ********/

    /*****  Save code In edit view ********/
    $(document).on('click', '.variation-popup-main .save', function(){
        var VarId = $('.editedId').val();
        var texareaVal = $('.variation-popup-main').find('textarea').val();
        $('#'+VarId).find('.edit-variation textarea').val(texareaVal);
        $('.variation-popup-main').remove();
        $('#wpwrap').show();
        $('.editedId').remove();
    });
    /*****  Save code In edit view ********/

    var element_id = '';
    var element_name = '';
    var element_classname = '';
    var element_left_pos = '';
    var element_top_pos = '';
    var variation_code = '';
    var element_offsetParent = '';
    $.fn.rightClick = function(method) {
        $(this).bind('contextmenu rightclick', function(rightClickevent){
        console.log(rightClickevent);
        element_id = rightClickevent.target.id;
        element_name = rightClickevent.target.nodeName;
        element_classname = rightClickevent.target.className;
        element_classname = element_classname.replace(/\  /g, '.');
        element_classname = element_classname.replace(/\ /g, '.');
        element_offsetParent = rightClickevent.target.closest( "div" ).className;
        element_offsetParent = element_offsetParent.replace(/\  /g, '.');
        element_offsetParent = element_offsetParent.replace(/\ /g, '.');
        element_left_pos = rightClickevent.pageX;
        element_left_pos = element_left_pos + 30;
        element_top_pos = rightClickevent.pageY;
        element_top_pos = element_top_pos + 30;

        //$('.ab_edit_page').text( "left: " + e.pageX + ", top: " + e.pageY );
        if(element_offsetParent != 'variation-editor' && $( ".variation-content" ).length > 0) {
                $('.ab_edit_popup').css({"top": element_top_pos, "left": element_left_pos, "position": "absolute", "display": "block","z-index": "99999999"});
        }
        rightClickevent.preventDefault();
        method();
        return false;
        })
    };

    $('html').delegate('.variation-content *', 'mouseover mouseout', function (e) {
        if (this === e.target) {
            $(this).attr('title',(e.type === 'mouseover' ? 'Right-click here to remove this section' : ''));
            $(e.target).css('box-shadow', (e.type === 'mouseover' ? '0px 0px 10px #2312b3' : ''));
        }
    });

    $('html').rightClick(function(rightClickevent){

    });
    $(document).on('click', 'html', function (event) {
            $('.ab_edit_popup').css({"display": "none"});
    });
    $(document).on('click', '.ab_edit_popup .ab_remove_div', function (event) {
            variation_code = $('.variation-editor textarea').val();
            if(element_id != '' && element_id != undefined) {
                    $('#'+element_id).hide();
                    variation_code += '$("#'+element_id+'").hide();\n';
                    $(".variation-editor textarea").val(variation_code);
            } else if ( element_classname != '' && element_classname != undefined )	{
                    $('.'+element_classname).hide();
                    variation_code += '$(".'+element_classname+'").hide();\n';
                    $(".variation-editor textarea").val(variation_code);
            } else {
                    $('.' + element_offsetParent + ' ' + element_name).hide();
                    variation_code += '$(".' + element_offsetParent + ' ' + element_name + '").hide();\n';
                    $(".variation-editor textarea").val(variation_code);
            }
            $('.ab_edit_popup').css({"display": "none"});
    });
    });
})( jQuery );

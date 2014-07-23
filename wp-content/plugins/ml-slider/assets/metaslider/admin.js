jQuery(document).ready(function($) {

    var file_frame;

    jQuery('.metaslider .add-slide').on('click', function(event){
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            multiple: 'add',
            frame: 'post',
            library: {type: 'image'}
        });

        // When an image is selected, run a callback.
        file_frame.on('insert', function() {

            jQuery(".metaslider .spinner").show();
            jQuery(".metaslider input[type=submit]").attr('disabled', 'disabled');

            var selection = file_frame.state().get('selection');
            var slide_ids = [];

            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                slide_ids.push(attachment.id);
            });

            var data = {
                action: 'create_image_slide',
                slider_id: metaslider_slider_id,
                selection: slide_ids,
                _wpnonce: metaslider.addslide_nonce
            };

            jQuery.post(metaslider.ajaxurl, data, function(response) {
                jQuery(".metaslider .left table").append(response);
                jQuery(".metaslider .left table").trigger('resizeSlides');
            });
        });

        file_frame.open();

        // Remove the Media Library tab (media_upload_tabs filter is broken in 3.6)
        jQuery(".media-menu a:contains('Media Library')").remove();

        if (!metaslider_pro_active) {
            jQuery(".media-menu a:contains('YouTube')").addClass('disabled');
            jQuery(".media-menu a:contains('Vimeo')").addClass('disabled');
            jQuery(".media-menu a:contains('Post Feed')").addClass('disabled');
            jQuery(".media-menu a:contains('Layer Slide')").addClass('disabled');
        }
    });

    jQuery("#screen-options-link-wrap").appendTo("#screen-meta-links").show();

    jQuery("#screen-options-switch-view-wrap").appendTo("#screen-meta-links").show();

    // Enable the correct options for this slider type
    var switchType = function(slider) {
        jQuery('.metaslider .option:not(.' + slider + ')').attr('disabled', 'disabled').parents('tr').hide();
        jQuery('.metaslider .option.' + slider).removeAttr('disabled').parents('tr').show();
        jQuery('.metaslider input.radio:not(.' + slider + ')').attr('disabled', 'disabled');
        jQuery('.metaslider input.radio.' + slider).removeAttr('disabled');

        jQuery('.metaslider .showNextWhenChecked:visible').parent().parent().next('tr').hide();
        jQuery('.metaslider .showNextWhenChecked:visible:checked').parent().parent().next('tr').show();

        // make sure that the selected option is available for this slider type
        if (jQuery('.effect option:selected').attr('disabled') === 'disabled') {
            jQuery('.effect option:enabled:first').attr('selected', 'selected');
        }

        // make sure that the selected option is available for this slider type
        if (jQuery('.theme option:selected').attr('disabled') === 'disabled') {
            jQuery('.theme option:enabled:first').attr('selected', 'selected');
        }
    };

    // enable the correct options on page load
    switchType(jQuery(".metaslider .select-slider:checked").attr("rel"));

    var toggleNextRow = function(checkbox) {
        if(checkbox.is(':checked')){
            checkbox.parent().parent().next("tr").show();
        } else {
            checkbox.parent().parent().next("tr").hide();
        }
    }

    toggleNextRow(jQuery(".metaslider .showNextWhenChecked"));

    jQuery(".metaslider .showNextWhenChecked").on("change", function() {
        toggleNextRow(jQuery(this));
    });

    // handle slide libary switching
    jQuery(".metaslider .select-slider").on("click", function() {
        switchType(jQuery(this).attr("rel"));
    });

    // return a helper with preserved width of cells
    var metaslider_sortable_helper = function(e, ui) {
        ui.children().each(function() {
            jQuery(this).width(jQuery(this).width());
        });
        return ui;
    };

    // drag and drop slides, update the slide order on drop
    jQuery(".metaslider .left table tbody").sortable({
        helper: metaslider_sortable_helper,
        handle: "td.col-1",
        stop: function() {
            jQuery(".metaslider .left table").trigger("updateSlideOrder");
            jQuery(".metaslider form #ms-save").click();
        }
    });

    // bind an event to the slides table to update the menu order of each slide
    jQuery(".metaslider .left table").live("updateSlideOrder", function(event) {
        jQuery("tr", this).each(function() {
            jQuery("input.menu_order", jQuery(this)).val(jQuery(this).index());
        });
    });

    // bind an event to the slides table to update the menu order of each slide
    jQuery(".metaslider .left table").live("resizeSlides", function(event) {
        var slideshow_width = jQuery("input.width").val();
        var slideshow_height = jQuery("input.height").val();

        jQuery("tr.slide input[name='resize_slide_id']", this).each(function() {
            $this = jQuery(this);

            var thumb_width = $this.attr("data-width");
            var thumb_height = $this.attr("data-height");

            if ((thumb_width != slideshow_width || thumb_height != slideshow_height)) {
                $this.attr("data-width", slideshow_width);
                $this.attr("data-height", slideshow_height);

                var data = {
                    action: "resize_image_slide",
                    slider_id: window.parent.metaslider_slider_id,
                    slide_id: $this.attr("data-slide_id"),
                    _wpnonce: metaslider.resize_nonce
                };

                jQuery.ajax({
                    type: "POST",
                    data : data,
                    cache: false,
                    url: metaslider.ajaxurl,
                    success: function(data) {
                        if (console && console.log) {
                            console.log(data);
                        }
                    }
                });
            }
        });
    });

    jQuery(document).ajaxStop(function() {
        jQuery(".metaslider .spinner").hide();
        jQuery(".metaslider input[type=submit]").removeAttr("disabled");
    });

    // show the confirm dialogue
    jQuery(".confirm").on("click", function() {
        return confirm(metaslider.confirm);
    });

    jQuery(".useWithCaution").on("change", function(){
        if(!this.checked) {
            return alert(metaslider.useWithCaution);
        }
    });

    // helptext tooltips
    jQuery(".metaslider .tipsy-tooltip").tipsy({className: 'msTipsy', live: true, delayIn: 500, html: true, gravity: 'e'});
    jQuery(".metaslider .tipsy-tooltip-top").tipsy({live: true, delayIn: 500, html: true, gravity: 'se'});

    // Select input field contents when clicked
    jQuery(".metaslider .shortcode input, .metaslider .shortcode textarea").on('click', function() {
        this.select();
    });

    // return lightbox width
    var getLightboxWidth = function() {
        var width = parseInt(jQuery('input.width').val(), 10);

        if (jQuery('.carouselMode').is(':checked')) {
            width = '75%';
        }

        return width;
    };

    // return lightbox height
    var getLightboxHeight = function() {
        var height = parseInt(jQuery('input.height').val(), 10);
        var thumb_height = parseInt(jQuery('input.thumb_height').val(), 10);

        if (isNaN(height)) {
            height = '70%';
        } else {
        	height = height + 50;

        	if (!isNaN(thumb_height)) {
        		height = height + thumb_height;
        	}
        }

        return height;
    };


    // IE10 treats placeholder text as the actual value of a textarea
    // http://stackoverflow.com/questions/13764607/html5-placeholder-attribute-on-textarea-via-jquery-in-ie10
    var fixIE10PlaceholderText = function() {
        jQuery("textarea").each(function() {
            if (jQuery(this).val() == jQuery(this).attr('placeholder')) {
                jQuery(this).val('');
            }
        });
    }

    jQuery(".metaslider .ms-toggle .hndle, .metaslider .ms-toggle .handlediv").on('click', function() {
    	jQuery(this).parent().toggleClass('closed');
    });

    jQuery(".metaslider").on('click', 'ul.tabs li', function() {
    	var tab = jQuery(this);
    	tab.parent().parent().children('.tabs-content').children('div.tab').hide();
    	tab.parent().parent().children('.tabs-content').children('div.'+tab.attr('rel')).show();
    	tab.siblings().removeClass("selected");
    	tab.addClass("selected");
    });

    // AJAX save & preview
    jQuery(".metaslider form").find("input[type=submit]").on("click", function(e) {
        e.preventDefault();

        jQuery(".metaslider .spinner").show();
        jQuery(".metaslider input[type=submit]").attr("disabled", "disabled");

        // update slide order
        jQuery(".metaslider .left table").trigger('updateSlideOrder');

        fixIE10PlaceholderText();

        // get some values from elements on the page:
        var the_form = jQuery(this).parents("form");
        var data = the_form.serialize();
        var url = the_form.attr("action");
        var button = e.target;

        jQuery.ajax({
            type: "POST",
            data : data,
            cache: false,
            url: url,
            success: function(data) {
                var response = jQuery(data);

                jQuery(".metaslider .left table").trigger("resizeSlides");

                jQuery("button[data-thumb]", response).each(function() {
                    var $this = jQuery(this);
                    var editor_id = $this.attr("data-editor_id");
                    jQuery("button[data-editor_id=" + editor_id + "]")
                        .attr("data-thumb", $this.attr("data-thumb"))
                        .attr("data-width", $this.attr("data-width"))
                        .attr("data-height", $this.attr("data-height"));
                });

                fixIE10PlaceholderText();

                if (button.id === "ms-preview") {
                    jQuery.colorbox({
                        iframe: true,
                        href: metaslider.iframeurl + "&slider_id=" + jQuery(button).data("slider_id"),
                        transition: "elastic",
                        innerHeight: getLightboxHeight(),
                        innerWidth: getLightboxWidth(),
                        scrolling: false,
                        fastIframe: false
                    });
                }
            }
        });
    });
});
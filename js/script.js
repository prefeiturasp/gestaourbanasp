jQuery(function() {


	/*Navigation*/
	jQuery('#wrapper .menu-menu-principal-container li ul').hide().removeClass('sub-menu');
	jQuery('#wrapper .menu-menu-principal-container li').click(
		function () {
			if (jQuery(this).hasClass('open'))
			{
				//jQuery('ul', this).stop().slideUp(100);
				//jQuery(this).removeClass('open');
			}
			else
			{
				jQuery('ul', this).stop().slideDown(100);
				jQuery(this).addClass('open');
			}
		}
	);
	jQuery('.wrapper .menu-menu-principal-container li').hover(
		function () {
			jQuery('ul', this).stop().slideDown(100);
			jQuery(this).addClass('open');
		},
		function () {
			jQuery('ul', this).stop().slideUp(100);
			jQuery(this).removeClass('open');
		}
	);

	var width = jQuery('#wrapper-second-home').width();
	if (width > 960)
	{
		width = 960;
	}

	/*Basicslider*/
	if (jQuery('#banner-slide').length > 0) {
		jQuery('#banner-slide').bjqs({
			animtype      : 'slide',
			height        : jQuery('#slide-first-home').height(),
			width         : width,
			responsive    : true,
			showmarkers : true,
			showcontrols : false,
			animduration : 1000,
			animspeed : 10000,
			nexttext : '<img src="'+template_url+'/images/btn-slide-next.png" />',
			prevtext : '<img src="'+template_url+'/images/btn-slide-prev.png" />'

		});
	}

	/*Perguntas e respostas*/
	jQuery('#wrapper-second-perguntas .text').click(function() {
		var last = this;
		if (!jQuery(last).find('.inner-text').hasClass('open')) {
			jQuery('.inner-text.open').slideUp(function() {
				jQuery('.inner-text.open').removeClass('open');
				console.log(jQuery(last).find('.inner-text').val());
				jQuery(last).find('.inner-text').slideDown(function() {
					jQuery(this).addClass('open');
				});
			});
		}
	});

	/*Inputs auto complement*/
	jQuery(".defaultText").focus(function(srcc)
    {
        if (jQuery(this).val() == jQuery(this)[0].title)
        {
        	jQuery(this).removeClass("defaultTextActive");
        	jQuery(this).val("");
        }
    });

	jQuery(".defaultText").blur(function()
    {
        if (jQuery(this).val() == "")
        {
        	jQuery(this).addClass("defaultTextActive");
        	jQuery(this).val(jQuery(this)[0].title);
        }
    });

	jQuery(".defaultText").blur();

	/*jQuery('#register-newsletter').submit(function() {

		var html = jQuery('#newsletter').html();

		jQuery.ajax({
		  type: "POST",
		  url: "ajax-cadastro.php",
		  data: { email:  jQuery('#register-newsletter-input').val()}
		}).done(function( msg ) {
		  jQuery('#newsletter').html(msg);
		  setTimeout(function() {
      		jQuery('#newsletter').html(html);
		  }, 3000);
		});
		return false;
	});*/

	jQuery('.ajax_submit_form').submit(function() {

		var _this = this;
		var html = jQuery(this).html();


		jQuery.ajax({
		  type: "POST",
		  url: jQuery(_this).attr('action'),
		  data: jQuery(_this).serialize()
		}).done(function( msg ) {
		  jQuery('#data').html(msg);
		  jQuery('a#inline').trigger('click');
		  setTimeout(function() {
      		jQuery(_this).html(html);
      		jQuery.fancybox.close();
      		jQuery("#data").html('');
		  }, 3000);
		});
		return false;
	});

	jQuery("a#inline").fancybox({
		//'hideOnContentClick': true,
		'modal' : true,
		'onClosed'		: function() {
		    jQuery("#data").html('');
		}
	});

});
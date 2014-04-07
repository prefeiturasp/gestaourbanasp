jQuery(document).ready(function($)
{
	$(".date").datepicker({
	    dateFormat: 'd/M/YYYY',
	    showOn: 'button',
	    buttonImage: '/images/admin/icon_news.png',
	    buttonImageOnly: true,
	    numberOfMonths: 3 
    });
});
// <![CDATA[
var pavan_global=
{
    /* ---------------------------------------------------- */
    /*		Navigation Dropdowns
    /* ---------------------------------------------------- */
    navigation :
    {
        setup : function()
        {
            $('#header').after('<div class="subnav-background"></div>');
            $('#nav ul').css('width', 'auto');
            this.clickBinding();
        },
        clickBinding : function()
        {
            $('#nav li').hover(function() {
                $(this).children('ul').hide().stop(true, true).slideDown(200);
            }, function() {
                $(this).children('ul').stop(true, true).fadeOut(0, function() {
                    $('.subnav-background').slideUp(200);
                    $('#header').removeClass('active');
                });
            });
            $('#nav li').hover(function() {
                if( $(this).children('ul').length > 0 ) {
                    var containerWidth = $('.container').width(),
                    subWidth = $(this).children('ul').width(),
                    pos = $(this).position(),
                    left = containerWidth - subWidth - ( pos.left + ( $(this).width() / 2 )),
                    margin = ( $(this).children('ul').children('li').size() - 1 ) * 30;
                    $(this).children('ul').css('right', left+margin);
                    $(this).addClass('hover');
                    $('.subnav-background').stop(true, true).slideDown(200);
                    $('#header').addClass('active');
                }
            }, function() {
                $(this).removeClass('hover');
            });
        }
    },
    login : 
    {
        clickBinding : function()
		{
			$('#user_logout').live('click',function()
			{
				var logoutUser = $.manageAjax.create('logoutUser'); 
				logoutUser.add(
				{
					success: function(html) 
					{
						jQuery.ajax(
						{
							url: $.url+"/login/controller/userController.php",
							data: "ref=userLogout&jsoncallback=?",
							dataType: "json",
							type: "GET",
							cache: true,
							beforeSend: function() {},
							success:function(data)
							{
								if(data.status)
								{
									window.location = "index.php";
								}
								else
								{
					
								}
								console.log(data.message);
							}
						});
					}
				});
			});
		}
    
    
    
    }
}
$(document).ready(function()
{
	pavan_global.navigation.setup();
    pavan_global.login.clickBinding();
});
// ]]>

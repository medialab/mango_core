// Added in startpage.pstpl

$(document).ready(
   function() {
	  $('.anim_help img').click(
         function() {
			var p = $(this).parent();
			var node = p.children('span.help_text');
            if(node.html() == null) {
               p.append('<span class="help_text">' + help + '</span>');
            } else {
               node.remove();
            }
         }
      );
   }
);
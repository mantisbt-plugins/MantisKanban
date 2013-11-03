jQuery(document).ready(function(){
    jQuery( ".card" ).draggable({ revert: "invalid" });
    jQuery( ".kanbanColumn" ).droppable({
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        drop: function( event, ui ) {
            moveColumnAjax( ui.draggable, this.id );
            ui.draggable.appendTo( this );
            ui.draggable.css('left','0').css('top','0');
        }
    });
    
    function moveColumnAjax( ticketObj,  targetColumnId )
    {
        ticketId    = ticketObj.data('ticketid');
        projectId   = ticketObj.data('projectid');
        userId      = ticketObj.data('userid');
        
        jQuery.ajax({
            type: "POST",
            url: kanbanAjaxUrl,
            data: { entrypoint: "bug_update_status",
                    id:         ticketId,
                    new_status: targetColumnId,
                    project_id: projectId,
                    user_id:	userId}
        })
        .done(function( msg ) {
            if(0 !== msg.length)
            {
                alert( msg );
            }
        });
    }
});
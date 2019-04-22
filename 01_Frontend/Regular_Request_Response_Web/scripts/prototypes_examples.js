Pep.prototype.moveElementsToBottomHorizon = function(){
    var obj;
    obj=this.$el;
    var horizonOffsetTop=$('.horizon').offset().top;
    $('.over').each(function(){
        if($(this).hasClass('vertical')){
            var cH = $(this).height();
            var calc=horizonOffsetTop-cH;
            $(this).offset({top:calc});
        }
    });
};





Pep.prototype.drawWorkSpace = function() {
    var workSpace=$('#workSpace').offset();
    workSpace.left=99999;
    workSpace.right=0;
    workSpace.bottom=0;
    workSpace.top=99999;
    $('.dropped.over').each(function(){
        workSpace.left=Math.min($(this).offset().left,workSpace.left);
        workSpace.top=Math.min($(this).offset().top,workSpace.top);
        workSpace.bottom=Math.max($(this).offset().top+$(this).outerHeight(),workSpace.bottom);
        workSpace.right=Math.max($(this).offset().left+$(this).outerWidth(),workSpace.right);
    });
    $('#workSpace').offset({left:workSpace.left,top:workSpace.top});
    $('#workSpace').width(workSpace.right-workSpace.left);
    $('#workSpace').height(workSpace.bottom-workSpace.top);


    /*JAK ELEMENTY PIONOWE WYCHODZA POZA OBSZAR OKNA*/
    var proportion=$('#application').attr('data-scaleProportion');
    var farestHintPositionPosibility=$('#rightFix').offset().left+($('#largestRightIndicator').attr('data-distance')*proportion)-$(window).scrollLeft();
    var diff=(viewport().width-farestHintPositionPosibility);
    if(diff<100 && $('.dropped.vertical').length>0){
        var calc1=Math.abs(diff)+50;
        var calc2=(Math.abs(diff)*2)+100;
        $('#workSpace').offset({left:workSpace.left-calc1,top:workSpace.top});
        $('#workSpace').width(workSpace.right-workSpace.left+calc2);
        $('#workSpace').height(workSpace.bottom-workSpace.top);
    }

}



Pep.prototype.highlighDropableAreas = function() {
    var obj;
    obj=this.$el;
    var maxOffsetLeft=0;
    var minOffsetLeft=99999;
    var calc=0;
    if($('.over.vertical').not(obj).length>=1 && obj.hasClass('vertical') && !$('.hirizonElementPlacementIndicator.right').hasClass('activated')){
        $('.over.vertical').not(obj).each(function(){
            maxOffsetLeft=Math.max($(this).offset().left,maxOffsetLeft);
            minOffsetLeft=Math.min($(this).offset().left,minOffsetLeft);
        })
        var proportion=$('#application').attr('data-scaleProportion');
        $('.hirizonElementPlacementIndicator.right').each(function(){
            calc=maxOffsetLeft+($(this).attr('data-distance')*proportion)-$(window).scrollLeft();
            $(this).offset({left:calc}).fadeIn(300).addClass('activated');
        })
        $('.hirizonElementPlacementIndicator.left').each(function(){
            calc=minOffsetLeft-($(this).attr('data-distance')*proportion)-$(window).scrollLeft();
            $(this).offset({left:calc}).fadeIn(300).addClass('activated');
        })
    }
}



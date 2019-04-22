var AutocompelteSection = function () {
    this.section;
    this.sectionID;
    this.field;
    this.data;
    this.url;
    this.maxLimit;
    this.cantFindMsg;
    this.additionalMsg;
};



$(function(){
    $('.autocomplete-section').each(function(){
        autocompleteSection = new AutocompelteSection();
        autocompleteSection.section = $(this);
        autocompleteSection.sectionID = '#'+$(this).attr('id');
        autocompleteSection.field = $(this).find('#membership-autocomplete-container input').eq(0);
        autocompleteSection.data = [];
        autocompleteSection.url = $(this).attr('data-autocomplete-path');
        autocompleteSection.maxLimit = $(this).attr('data-autocomplete-limit');
        autocompleteSection.cantFindMsg = $(this).attr('data-autocomplete-cant-find');
        autocompleteSection.additionalMsg = $(this).attr('data-autocomplete-additional-information-optional');
        autocompleteSection.init();
        autocompleteSection.updateListFormValue();
    })
    $( document ).ajaxStop(function() {
       $('.twitter-typeahead').removeClass('loading-process')
    });
});


AutocompelteSection.prototype = {
    init: function(){
        if(this.field .length > 0){
             this.setupEngine();
             this.initAutoComplete();
             this.onSelectEvent();
             this.initNoResultsAction();
             this.initRemoveAction();
        }
    },
    setupEngine: function(){
        /**
         * Setup suggestion engine
         */
        var tmpsection=this.section;
        this.data = new Bloodhound({
            remote:
                {
                    url: this.url+'?term=%QUERY',
                    cache: false,
                    replace: function(url, uriEncodedQuery) {
                        return url.replace("%QUERY",uriEncodedQuery) + '&exludes=' + getExcludes(tmpsection)
                    },
                }
            ,
            limit: 10,
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.val);
            },
            ajax: {
                xhrFields: {
                    withCredentials: true,
                },
                beforeSend: function(xhr, settings) {

                },
                complete: function(xhr, status) {

                }
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });
        this.data.initialize();
    },
    initAutoComplete: function(){
        /**
         * Setup autocomplete
         */
        $(this.field).typeahead
        ({
            minLength: 0,
            highlight: true,
            hint: false
        },
        {
            source: this.data.ttAdapter(),
            templates: {
                empty: $.proxy(this.cantFind, this)
            },
        })
        $(this.field).on( 'focus', function() {
            $(this).parent().addClass('loading-process')
            $(this).data().ttTypeahead.input.trigger('queryChanged', $(this).val());
        })
        .on('input', function(e){
            $(this).parent().addClass('loading-process')
        })
        .on('keydown', function(e){
            var code = e.keyCode || e.which;
            if(code == 13){
                event.preventDefault();
                return false;
            }
        })
    },
    onSelectEvent: function(){
        //$(this.field).on('typeahead:selected', $.proxy(this.addToList, this));
        $(this.field).on('typeahead:selected', $.proxy(this.addToList, this));
    },
    addToList: function (event, item) {
        var memberships = $(this.section).find('.selected-memberships li');
        $('<li class="meta-categories selected-listing-id-'+item.id+'">'
            + item.value
            + '<div class="selected-categories-buttons">'
                + '<span class="glyphicon glyphicon-remove-circle" data-id="'+item.id+'"></span>'
                + '<span class="badge">Added</span>'
            + '</div>'
            + '<div class="additional-option">'
                 + '<input name="tag-'+item.id+'" type="text" placeholder="'+this.additionalMsg+'" value="" class="form-control" />'
            + '</div>'
            +'</li>')
            .appendTo($(this.section).find('.selected-memberships'));

        this.updateListStatus();
        /**
         * I NEED TO DESTROY AUTOCOMPLETE AND RESET IT
         * BECOUSE IT IS REMEMBERING SELECTED VALUE AFTER
         * FOCUSING OUT OF THEFIELD
         */
        this.field.val('');
        this.field.typeahead('destroy');
        this.initAutoComplete();
        if(memberships.length+1 >= this.maxLimit){
            $(this.section).find('#autocomplete-limit-fix').removeClass('active');
            return -1;
        }
    },
    updateListStatus: function(){
        var membershipsCount = $(this.section).find('.selected-memberships li').length;
        if(membershipsCount>0){
            $(this.section).find('#membership-list-container').addClass('active');
        }else{
            $(this.section).find('#membership-list-container').removeClass('active');
        }
        this.updateListFormValue();
    },
    updateListFormValue: function(){
        data = [];
        $(this.section).find('.selected-memberships li .glyphicon-remove-circle').each(function(){
            data.push($(this).attr('data-id'));
        })
        $(this.section).find('#membership-autocomplete-container input').eq(1).val(JSON.stringify(data));
    },
    cantFind: function () {
        return '<span class="suggest_location_cant_find_your_location">'+this.cantFindMsg+' <a href="#" class="type_manually_trigger">Click here</a> to add it.</span>';
    },
    initNoResultsAction: function() {
        $(document).on('click','.type_manually_trigger',function(event){
            event.preventDefault();
            $(this.field).blur();
            $(this).parentsUntil('.autocomplete-section').find('.membership-missing-container textarea').focus()
            $(this).parentsUntil('.autocomplete-section').find('.membership-missing-container').addClass('active');
        })
    },
    initRemoveAction(){
        var assoOBJ = this;
        $(document).on('click',this.sectionID+' #membership-list-container li .glyphicon-remove-circle',function(event){
            event.preventDefault();
            $(this).parentsUntil('.autocomplete-section').find('#autocomplete-limit-fix').addClass('active');
            $(this).parent().parent().remove();
            assoOBJ.updateListStatus();
        })
    }
};



function getExcludes(section){
    var excludes='';
    $(section).find('li .glyphicon-remove-circle').each(function(key,item){
        if(key===0){
            excludes+=$(this).attr('data-id');
        }else{
            excludes+=','+$(this).attr('data-id');
        }
    })
    return excludes;
}

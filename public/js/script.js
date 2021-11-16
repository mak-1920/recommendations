jQuery(function(){
    var pages = {}
    var xhr = {}
    var isGeneration = {}
    var isEnd = {}

    function ajaxGenerate(page, type){
        if(isGeneration[type]) 
            return
        isGeneration[type] = true

        xhr[type] = $.ajax({
            url: "ajax/" + type + "/page/" + page,
            dataType: "html",
            beforeSend: function(){
                $("#generation-status-" + type).removeClass("d-none")
            },
            success: function(html){
                if($(html).length === 0) 
                    isEnd[type] = true
                $('.scrolling-block').append(html)
            },
            complete: function(){
                $("#generation-status-" + type).addClass("d-none")
                isGeneration = false
            }
        })
    }

    $('.scrolling-block').each((i, e) => {
        var type = $(e).attr('scrolling-data-type')
        $(e).append('<div class="loading" id="generation-status-' + type + '">loading...</div>')
        pages[type] = 1
        isEnd[type] = false
        ajaxGenerate(pages[type], type)
    })

    $(window).scroll(function() 
    {
        if($(window).scrollTop() + $(window).height() >= $(document).height() - 10) 
            $('.scrolling-block').each((i, e) => {
                var type = $(e).attr('scrolling-data-type')
                if(isEnd[type] || isGeneration[type]) 
                    return
                ajaxGenerate(pages[type], type)
            })
    });

    function split( val ) {
        return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }
    $(".tags-input").on("keydown", function(event) {
        if ( event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active ) {
            event.preventDefault();
        }
    })
    .autocomplete({
        minLength: 0,
        source: function(request, response) {
            response($.ui.autocomplete.filter($('.tags-data option'), extractLast(request.term)))
        },
        focus: function() {
            return false
        },
        select: function(event, ui) {
            var terms = split(this.value)
            terms.pop()
            terms.push($(ui.item).html())
            terms.push("")
            this.value = terms.join(", ")
            return false
        }
    });
})
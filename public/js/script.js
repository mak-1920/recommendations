jQuery(function(){
    var pages = {}
    var xhr = {}
    var isGeneration = {}
    var isEnd = {}

    function ajaxGenerate(page, type){
        if(isGeneration[type]) 
            xhr[type].abort()
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
        
        if(isEnd[type] || isGeneration[type]) return
        if($(window).scrollTop() + $(window).height() >= $(document).height() - 10) 
            $('.scrolling-block').each((i, e) => {
                var type = $(e).attr('scrolling-data-type')
                ajaxGenerate(pages[type], type)
            })
    });
})
jQuery(function(){
    var pages = {}
    var xhr = {}
    var isGeneration = {}
    var isEnd = {}

    function ajaxGenerate(type){
        if(isGeneration[type]) 
            return
        isGeneration[type] = true

        xhr[type] = $.ajax({
            url: "ajax/" + type + "/page/" + pages[type],
            dataType: "html",
            beforeSend: function(){
                $("#generation-status-" + type).removeClass("d-none")
            },
            success: function(html){
                if($(html).length === 0) 
                    isEnd[type] = true
                $('.scrolling-block').append(html)
                pages[type]++
            },
            complete: function(){
                $("#generation-status-" + type).addClass("d-none")
                isGeneration[type] = false
            }
        })
    }

    $('.scrolling-block').each((i, e) => {
        var type = $(e).attr('scrolling-data-type')
        $(e).after('<div class="loading display-5 text-center" id="generation-status-' + type + '">loading...</div>')
        pages[type] = 1
        isEnd[type] = false
        isGeneration[type] = false
        ajaxGenerate(type)
    })

    $(window).scroll(function() 
    {
        if($(window).scrollTop() + $(window).height() >= $(document).height() - 10) 
            $('.scrolling-block').each((i, e) => {
                var type = $(e).attr('scrolling-data-type')
                if(isEnd[type] || isGeneration[type]) 
                    return
                ajaxGenerate(type)
            })
    });

    $('.review-add-tag').click(function() {
        var list = $(this).parent().parent()
        var index = $(item).attr('data-index')
        var prototype = $(list).attr('data-prototype').replace(/__name__/g, index)
        // const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass)
        // console.log( e.currentTarget.dataset.collectionHolderClass)

        var item = $('<li></li>')
            .addClass('p-0 col list-group-item border-0')
            .html(prototype)

        $(this).parent().before($(item))

        // $(item).html(prototype)
            // .dataset
            // .prototype
            // .replace(
            //     /__name__/g,
            //     collectionHolder.dataset.index
            // ))
        $(list).attr('data-index', index + 1)
        // collectionHolder.appendChild(item)

        // collectionHolder.dataset.index++
    })

    // function split( val ) {
    //     return val.split( /,\s*/ );
    // }
    // function extractLast( term ) {
    //     return split( term ).pop();
    // }
    // $(".tags-input").select2({
    //     tags: true,
    //     theme: 'bootstrap-5',
    //     multiple: true,
    //     tokenSeparators: [',', ' '], 
    // })
    // .on('select2:select', e => {
    //     var data = e.params.data
    //     var input = $('.tags-input')
    //     var searchingElement = $(input).find("option:contains('" + data.text + "'):first")
    //     data.id = +$(searchingElement).val()
    //     if (!isNaN(data.id)) {
    //         // searchingElement.trigger('change')
    //         if(isNaN($(input).find('option:last').val()))
    //             $(input).find('option:last').remove()
    //         $(searchingElement).attr('selected', 'true')
    //     } else { 
    //         $(input).find('option:last').remove()
    //         // data.id = +$(input).find('option:last').val() + 1 ?? 1
    //         var newOption = new Option(data.text, data.text, true, true)
    //         $(input).append(newOption).trigger('change')
    //     } 
    //     return false
    // })
})
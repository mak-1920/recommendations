jQuery(function(){
    var pages = {}
    var xhr = {}
    var isGeneration = {}
    var isEnd = {}

    function getReviewID(){
        var location = window.location.pathname
        return (location.match(/id(\d+)$/i)[1])
    }

    function getLocale(){
        var path = window.location.pathname
        return path.match(/^\/(ru|en)\//)[1]
    }

    function ajaxGenerate(type, param = -1){
        if(isGeneration[type]) 
            return
        isGeneration[type] = true

        var data = {}
        if(param != -1)
            data = {'param': param}
        var locale = getLocale()

        xhr[type] = $.ajax({
            url: '/' + locale + '/ajax/' + type + '/page/' + pages[type],
            type: 'get',
            dataType: 'html',
            data: data,
            beforeSend: function(){
                $('#generation-status-' + type).removeClass('d-none')
            },
            success: function(html){
                if($(html).length === 0) 
                    isEnd[type] = true
                $('.scrolling-block').append(html)
                pages[type]++
            },
            complete: function(){
                $('#generation-status-' + type).addClass('d-none')
                isGeneration[type] = false
            }
        })
    }

    $('.scrolling-block').each((i, e) => {
        var type = $(e).attr('scrolling-data-type')
        var param = $(e).attr('scrolling-param')
        $(e).after('<div class="my-2 d-flex justify-content-center">'
            + '<div class="spinner-border text-primary" id="generation-status-' 
            + type 
            + '"><span class="sr-only"></span></div></div>')
        pages[type] = 1
        isEnd[type] = false
        isGeneration[type] = false
        ajaxGenerate(type, param)
    })

    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() >= $(document).height() - 10) 
            $('.scrolling-block').each((i, e) => {
                var type = $(e).attr('scrolling-data-type')
                var id = $(e).attr('scrolling-param')
                if(isEnd[type] || isGeneration[type]) 
                    return
                ajaxGenerate(type, id)
            })
    });

    function getTagInput(name) {
        var item = $('.tags :input').filter(function() {
            return this.value == name
        })
        return item
    }
    function creatTag(name) {
        var list = $('.tags')
        var index = +$(list).attr('data-index')
        var prototype = $(list).attr('data-prototype').replace(/__name__/g, index)

        var item = $('<fieldset></fieldset>').html(prototype)
        $(item).find('input').val(name)
        $(list).append($(item))
        $(list).attr('data-index', index + 1)
    }
    function removeTag(name) {
        var input = getTagInput(name)
        var listItem = $(input).closest('fieldset')
        $(listItem).remove()
    }

    var s2 = $(".tags-input").select2({
        tags: true,
        theme: 'bootstrap-5',
        multiple: true,
        tokenSeparators: [',', ' '], 
        width: '100%',
    }).on('select2:select', e => {
        var data = e.params.data
        var input = $('.tags-input')
        var options = []
        $(input).find("option").each(function(){
            if($(this).text() == data.text){
                options.push(this)
            }
        })
        var searchingElement = $(options).first()
        data.id = +($(searchingElement).val())
        if (options.length == 2)
        {
            $(searchingElement).remove()
        } 
        if($(getTagInput(e.params.data.text)).length)
            return false
        creatTag(e.params.data.text)
        return false
    }).on('select2:unselect', e => {
        removeTag(e.params.data.text)
        return false
    })

    $('.review-create-button').click(function() {
        $(".tags-input").val('')
    })

    var tags = $('.tags :input').map((i,e) => {
        return $('.tags-input')
            .find("option:contains('" + $(e).val() + "'):first")
            .val()
    })
    s2.val(tags).trigger('change')

    $('.add-comment').click(function(){
        var reviewId = getReviewID()
        $(this).attr('disabled', 'disabled')
        $.ajax({
            'url': '/ajax/comment/create',
            'type': 'post',
            'data': {
                'text': $('.comment-text').val(),
                'reviewId': reviewId
            },
            success: function(){
                $('.comment-text').val('')
            },
            complete: function(){
                $('.add-comment').removeAttr('disabled')
            }
        })
        return false;
    })

    $('.review-like-button').click(function(){
        var reviewId = getReviewID()
        var button = $(this)
        $.ajax({
            url: '/ajax/review/like/id' + reviewId,
            type: 'get',
            dataType: 'json',
            beforeSend: function(){
                $(button).attr('disabled', 'disabled')
            },
            success: function(res){
                if(res.result){
                    $(button)
                        .removeClass('btn-secondary')
                        .addClass('btn-success')
                } else {
                    $(button)
                        .addClass('btn-secondary')
                        .removeClass('btn-success')
                }
                $('.review-likes-count').html(res.likesCount)
            },
            complete: function(){
                $(button).removeAttr('disabled')
            }
        })
    })

    $(document).on({
        mouseenter: function(){
            var buttons = $(this).parent()
            var val = $(this).html()
            $(buttons).find('button').each((i, e) => {
                $(e).removeClass('btn-primary').addClass('btn-secondary')
            })
            for(i=1; i<=val; i++){
                $('.review-rating-button-' + i)
                    .addClass('btn-primary')
                    .removeClass('btn-secondary')
            }
        }, 
        mouseleave: function(){
            $(this).parent().find('button')
                .removeClass('btn-primary')
                .addClass('btn-secondary')
        }
    }, '.review-rating-buttons:not(.appreciated) button')
    $('.review-rating-buttons button').on('click', function(){
        var id = getReviewID()
        var buttons = $(this).parent()
        var value = $(this).html()

        $.ajax({
            url: '/ajax/review/set-rating/id' + id,
            type: 'post',
            data: {
                'value': value
            },
            dataType: 'json',
            beforeSend: function(){
                $(buttons).children().attr('disabled', 'disabled')
            },
            success: function(res){
                $('.review-rating-value').html(res.rateValue)
                if(!res.add){
                    $(buttons).removeClass('appreciated')
                    $(buttons).children()
                        .removeClass('btn-success')
                        .addClass('btn-secondary')
                } else {
                    $(buttons).addClass('appreciated')
                    for(i=1; i<=value; i++){
                        $('.review-rating-button-' + i)
                            .addClass('btn-success')
                            .removeClass('btn-secondary')
                    }
                }
            },
            complete: function(){
                $(buttons).children().removeAttr('disabled')
            }
        })
    })

    $('.comments').on('click', '.comment-remove', function(e, b) {
        var id = $(this).attr('data')
        var comment = $(this).closest('.comment')
        var button = $(this)

        $.ajax({
            url: '/ajax/comment/remove',
            type: 'post',
            dataType: 'json',
            data: {
                'id': id,
            },
            beforeSend: function(){
                $(button).addClass('d-none')
            },
            success: function(res){
                if(res.result){
                    $(comment).remove()
                }
            },
            complete: function(){
                if($(comment).length){
                    $(button).removeClass('d-none')
                }
            }
        })
    })

    $('.review-remove-button').on('click', function(){
        var text = $(this).attr('message-text')
        return confirm(text)
    })
})
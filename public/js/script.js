jQuery(function(){
    var lastId = {}
    var xhr = {}
    var isGeneration = {}
    var isEnd = {}

    var cloudinaryPath = 'https://res.cloudinary.com/ht74ky0yv/image/upload/v1638384344/'

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
            data = {
                'param': param,
                'lastId': lastId[type],
            }
        var locale = getLocale()

        xhr[type] = $.ajax({
            url: '/' + locale + '/ajax/' + type + '/page',
            type: 'post',
            dataType: 'json',
            data: data,
            beforeSend: function(){
                $('#generation-status-' + type).removeClass('d-none')
            },
            success: function(res){
                if(res.lastId === 0) 
                    isEnd[type] = true
                $('.scrolling-block').append(res.html.content)
                lastId[type] = res.lastId
                console.log(res)
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
        lastId[type] = -1
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
        // dropdownCssClass: 'select2-dropdown',
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
        $('.file-uploader').val('')
        saveImages = false
        for(var illustration of illustrations) {
            createIllustration(illustration)
        }
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
            url: '/ajax/comment/create',
            type: 'post',
            data: {
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
            type: 'post',
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

    // function getIllustrationInput(name) {
    //     var item = $('.illustrations :input').filter(function() {
    //         return this.value == name
    //     })
    //     return item
    // }
    function createIllustration(name) {
        var list = $('.illustrations')
        var index = +$(list).attr('data-index')
        var prototype = $(list).attr('data-prototype').replace(/__name__/g, index)

        var item = $('<fieldset></fieldset>').html(prototype)
        $(item).find('input').val(name)
        $(list).append($(item))
        $(list).attr('data-index', index + 1)
    }
    // function removeIllustration(name) {
    //     var input = getIllustrationInput(name)
    //     var listItem = $(input).closest('fieldset')
    //     $(listItem).remove()
    // }
    function setIllustrations() {
        $('.illustrations :input').each(function() {
            illustrations.push($(this).val())
            $(this).closest('fieldset').remove()
        })
        $('.illustrations').attr('data-index', 0)
    }
    function getIllustrationsImages(){
        var imgs = []

        for(var illustration of illustrations) {
            imgs.push("<img src='" + cloudinaryPath + illustration + "' class='file-preview-image'>")
        }

        return imgs
    }
    function getIllustrationsConfigs(){
        var imgs = []

        for(var illustration of illustrations) {
            imgs.push({
                'fileId': illustration,
                'key': illustration,
            })
        }

        return imgs
    }

    var illustrations = []

    setIllustrations()
    $('.file-uploader').fileinput({
        allowedFileExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        initialPreview: getIllustrationsImages(),
        initialPreviewConfig: getIllustrationsConfigs(),
        language: getLocale(),
        maxFileSize:2000,
        overwriteInitial: false,
        showClose: false,
        showRemove: false,
        theme: 'bs5',
        uploadUrl: '/ajax/add_illustration',
        deleteUrl: '/ajax/remove_illustration',
        fileActionSettings: {
            showDrag: false,
        },
    })
    .on('fileuploaded', function(event, previewId, index, fileId){
        var response = previewId.response
        var el = $('[id*="' + index + '"]')
        
        if(response.result) {
            $(el).attr('id', response.name)
            $(el).find('img').attr('src', cloudinaryPath + response.name)

            illustrations.push(response.name)
            return false
        }
    })
    .on('filesuccessremove', function(event, id){
        var uploader = $(this)
        
        $.ajax({
            url: '/ajax/remove_illustration',
            dataType: 'json',
            type: 'post',
            data: {
                'key': id
            },
            beforeSend: function(){
                $(uploader).fileinput('disable')
            },
            success: function(res){
                console.log(res, id)
                $('[id*="' + id + '"]').fadeOut(
                    300, 
                    function(){ 
                        $(this).remove()
                        console.log('remove')
                    }
                )
                illustrations.splice(illustrations.indexOf(ind), 1)
            },
            complete: function(){
                $(uploader).fileinput('enable')
            }
        })

        return false
    })
    .on('filedeleted', function(event, ind){
        // removeIllustration(ind)
        illustrations.splice(illustrations.indexOf(ind), 1)
        return false
    })

    var saveImages = true
    window.onbeforeunload = function(){
        var uploader = $('.file-uploader')

        if(uploader.length && saveImages){
            saveImages = false

            if(/\/edit\//i.test(location)){
                $.ajax({
                    url: '/ajax/save-illustrations',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'reviewId': getReviewID(),
                        'illustrations': illustrations,
                    }
                })
            } else {
                $.ajax({
                    url: '/ajax/remove-temporary-illustrations',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'illustrations': illustrations,
                    }
                })
            }
        }
    }
})
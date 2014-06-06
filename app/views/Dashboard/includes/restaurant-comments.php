<div id="restaurantSocialSelector" class="none left">
    <label><?php echo SOCIAL_SELECTOR_LABEL; ?></label>
    <select id="restSocialSelect" class="none"></select>
    <div class="preloader"></div>
</div>
<div id="ratingsContainer" class="right">
    <h1><?php echo RATING_LABEL; ?></h1>
    <ul class="ratingItem" id="<?php echo RATING_GLOBAL; ?>">
        <li class="catTitle"><?php echo ucfirst(RATING_GLOBAL); ?></li>
        <div class="preloaderSmall left"></div>
        <ul class="stars none"></ul>
        <li class="txtInfo none"></li>
    </ul>
    <?php foreach (explode(',', RATING_CATS) as $cat): ?>
    <ul class="ratingItem" id="<?php echo $cat; ?>">
        <li class="catTitle"><?php echo ucfirst($cat); ?></li>
        <div class="preloaderSmall left"></div>
        <ul class="stars none"></ul>
        <li class="txtInfo none"></li>
    </ul>
<?php endforeach; ?>
</div>
<div id="commentsContainer" class="left">
    <h1><?php echo COMMENT_LABEL; ?></h1>
    <label id="commentCounter"><?php echo COMMENT_COUNTER; ?><b>0</b></label>
    <div class="preloader"></div>
</div>
<div id="commentTemplate" class="none comment">
    <div class="img left">
        <img src="" alt="" width="40" height="40">
    </div>
    <div class="txt right">
        <p class="userName"></p>
        <p class="timestamp"></p>
        <p class="comment"></p>
    </div>
    <br class="clear" />
    <a href="" target="_blank" class="button"><?php echo COMMENT_SEE; ?></a>
</div>
<br class="clear" />
<script type="text/javascript">

    function populateRestaurantComments(comments) {
        for(var i = 0; i < comments.gigyaComments.comments.length; i++) {
            var template = $('#commentTemplate').clone();
            var commentId = 'comments_' + comments.gigyaComments.comments[i].ID;
            var salLink = '<?php echo SAL_URL; ?>/?redirect_restaurant_id=';
            salLink += comments.restaurant_id;
            salLink += '#' + commentId;

            var commentDate = new Date(comments.gigyaComments.comments[i].timestamp);
            var commentDateStr = commentDate.getDate() + '/' 
                + commentDate.getMonth() + '/'
                + commentDate.getFullYear() + ' '
                + commentDate.getHours() + ':'
                + commentDate.getMinutes() + ':'
                + commentDate.getSeconds();

            $(template).attr('id', commentId);
            $(template).find('.img img').attr('src', comments.gigyaComments.comments[i].sender.photoURL);
            $(template).find('.userName').html(comments.gigyaComments.comments[i].sender.name);
            $(template).find('.timestamp').html(commentDateStr);
            $(template).find('.comment').html(comments.gigyaComments.comments[i].commentText);
            $(template).find('.button').attr('href', salLink);
            $(template).appendTo('#commentsContainer');
            $(template).removeClass('none');
        }

        $('#commentCounter > b').html(comments.gigyaComments.comments.length);
        $('#commentsContainer .button').button({
            icons: {
                primary: 'ui-icon-comment'
            }
        });
        $('#commentsContainer .preloader').hide();
    }

    function populateRestaurantRatings(ratings) {
        for(var prop in ratings) {
            var itemHtml = '';
            var itemIndex = 0;
            for(var i = 0; i < Math.floor(ratings[prop].overall); i++) {
                itemHtml += '<li class="star full"></li>';
                itemIndex++;
            }

            if(Math.ceil(ratings[prop].overall) > itemIndex) {
                itemHtml += '<li class="star half"></li>';
                itemIndex++;
            }

            for(var i = itemIndex; i < 5; i++) {
                itemHtml += '<li class="star empty"></li>';
            }
            $('#' + prop + ' .stars').html(itemHtml);

            var ratingTemplate = '';
            if(prop == 'total') {
                ratingTemplate = '%1';
                ratingTemplate = ratingTemplate.replace('%1', ratings[prop].overall);
            } else {
                ratingTemplate = '%1 (%2 %3)';
                ratingTemplate = ratingTemplate.replace('%1', ratings[prop].overall);
                ratingTemplate = ratingTemplate.replace('%2', ratings[prop].count);
                if(ratings[prop].count >= 1) {
                    ratingTemplate = ratingTemplate.replace('%3', '<?php echo RATING_VOTES; ?>');
                } else {
                    ratingTemplate = ratingTemplate.replace('%3', '<?php echo RATING_VOTE; ?>');
                }
            }
            
            $('#' + prop + ' .txtInfo').html(ratingTemplate);
            $('#' + prop + ' .preloaderSmall').hide();
            $('#' + prop + ' .stars, #' + prop + ' .txtInfo').slideDown(400);
        }
    }

    function showSocialChildSelector(restaurant_id, restaurant_id_sal) {
        var optionTemplate = '<option value="%d">%s</option>';
        $('#restaurantSocialSelector').show();
        $.ajax({
            url: '/json/Restaurant/getFranchiseChilds/',
            type: 'get',
            data: { 'restaurant_id': restaurant_id },
            success: function(response) {
                if(typeof response != 'object') {
                    try {
                        response = $.parseJSON($.trim(response));
                    } catch(e) {
                        console.log(e);
                    }
                }
                if(response.code == 1) {
                    $('#restSocialSelect').append(
                        optionTemplate
                            .replace('%d', restaurant_id_sal)
                            .replace('%s', '<?php echo FRANCHISE; ?>')
                        );

                    for(var prop in response.response) {
                        $('#restSocialSelect').append(
                            optionTemplate
                                .replace('%d', prop)
                                .replace('%s', response.response[prop])
                            );
                    }

                    $('#restSocialSelect').show();
                }
            },
            complete: function() {
                $('#restaurantSocialSelector .preloader').hide();
            }
        });
    }

    function getSelectedRestSocial(event) {
        $('#commentsContainer > .comment').remove();
        $('#commentsContainer .preloader').show();
        $('#ratingsContainer .star').remove();
        $('#ratingsContainer .txtInfo').html('');
        $('#ratingsContainer .preloaderSmall').show();

        $.ajax({
            url: '/json/Comment/getComments/',
            type: 'get',
            data: {
                'restaurant_id': $(event.currentTarget).val(),
                'is_sal': '1'
            },
            success: function(response) {
                if(typeof response != 'object') {
                    try {
                        response = $.parseJSON($.trim(response));
                    } catch(e) {
                        console.log(e);
                    }
                }
                populateRestaurantComments(response.response);
            }
        });
        $.ajax({
            url: '/json/Comment/getRatings/',
            type: 'get',
            data: {
                'restaurant_id': $(event.currentTarget).val(),
                'is_sal': '1'
            },
            success: function(response) {
                if(typeof response != 'object') {
                    try {
                        response = $.parseJSON($.trim(response));
                    } catch(e) {
                        console.log(e);
                    }
                }
                populateRestaurantRatings(response.response);
            }
        });
    }
    $('#restSocialSelect').on('change', getSelectedRestSocial);

</script>
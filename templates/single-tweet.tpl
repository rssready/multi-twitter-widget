<li class="tweet clearfix">
    <a href="http://twitter.com/<?= $tweet['user']['screen_name']; ?>">
        <img class="twitter-avatar" src="<?= $tweet['user']['profile_image_url']; ?>" width="40" height="40" alt="<?= $tweet['user']['screen_name']; ?>" />
    </a>

    <div class="tweet-time">
        <em>&nbsp;-&nbsp; <?= $tweet['created_at']; ?></em>
    </div>

    <div style="clear:both"></div>

    <div class="tweet-message"><?= $tweet['text']; ?></div>
</li>
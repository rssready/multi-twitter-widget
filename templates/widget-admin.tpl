<fieldset style="border:1px solid gray;padding:10px;">
    <legend>Oauth Settings</legend>
    <p>
        <label for="multi_twitter_consumer_key">Comsumer Key: </label><br />
        <input type="text" class="widefat" id="multi_twitter_consumer_key" name="<?= $fields['consumer_key']; ?>" value="<?= $options['consumer_key']; ?>" />
    </p>
    <p>
        <label for="multi_twitter_consumer_secret">Consumer Secret: </label><br />
        <input type="text" class="widefat" id="multi_twitter_consumer_secret" name="<?= $fields['consumer_secret']; ?>" value="<?= $options['consumer_secret']; ?>" />
    </p>
    <p>
        <label for="multi_twitter_access_token">Access Token: </label><br />
        <input type="text" class="widefat" id="multi_twitter_access_token" name="<?= $fields['access_token']; ?>" value="<?= $options['access_token']; ?>" />
    </p>
    <p>
        <label for="multi_twitter_access_token_secret">Access Token Secret: </label><br />
        <input type="text" class="widefat" id="multi_twitter_access_token_secret" name="<?= $fields['access_token_secret']; ?>" value="<?= $options['access_token_secret']; ?>" />
    </p>
</fieldset>

    <p>
        <label for="multi_twitter-Title">Widget Title: </label><br />
        <input type="text" class="widefat" id="multi_twitter-Title" name="<?= $fields['title']; ?>" value="<?= $options['title']; ?>" />
    </p>
    <p>
        <label for="multi_twitter-Users">Users: </label><br />
        <input type="text" class="widefat" id="multi_twitter-Users" name="<?= $fields['users']; ?>" value="<?= $options['users'];?>" /><br />
        <small><em>enter accounts separated with a space</em></small>
    </p>
    <p>
        <label for="multi_twitter-Terms">Search Terms: </label><br />
        <input type="text" class="widefat" id="multi_twitter-Terms" name="<?= $fields['terms']; ?>" value="<?= $options['terms']; ?>" /><br />
        <small><em>enter search terms separated with a comma</em></small>
    </p>
    <p>
        <label for="multi_twitter-UserLimit">Limit user feed to: </label>
        <select id="multi_twitter-UserLimit" name="<?= $fields['user_limit']; ?>">
            <option value="<?= $options['user_limit']; ?>"><?= $options['user_limit']; ?></option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
        </select>
    </p>
    <p>
        <label for="multi_twitter-TermLimit">Limit search feed to: </label>
        <select id="multi_twitter-TermLimit" name="<?= $fields['term_limit']; ?>">
            <option value="<?= $options['term_limit']; ?>"><?= $options['term_limit']; ?></option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
        </select>
    </p>
    <p>
        <label for="multi_twitter-Links">Automatically convert links?</label>
        <input type="checkbox" name="<?= $fields['links']; ?>" id="multi_twitter-Links" <?= $options['links'] ? 'checked="checked"' : ''; ?> />
    </p>
    <p>
        <label for="multi_twitter-Reply">Automatically convert @replies?</label>
        <input type="checkbox" name="<?= $fields['reply']; ?>" id="multi_twitter-Reply" <?= $options['reply'] ? 'checked="checked"' : '' ?> />
    </p>
    <p>
        <label for="multi_twitter-Hash">Automatically convert #hashtags?</label>
        <input type="checkbox" name="<?= $fields['hash']; ?>" id="multi_twitter-Hash" <?= $options['hash'] ? 'checked="checked"' : ''; ?> />
    </p>
    <p>
        <label for="multi_twitter-Date">Show Date?</label>
        <input type="checkbox" name="<?= $fields['date']; ?>" id="multi_twitter-Date" <?= $options['date'] ? 'checked="checked"' : ''; ?> />
    </p>
    <p>
        <label for="multi_twitter-Styles">Use Default Styles?</label>
        <input type="checkbox" name="<?= $fields['styles']; ?>" id="multi_twitter-Styles" <?= $options['styles'] ? 'checked="checked"' : ''; ?> />
    </p>
    <p>
        <label>Single Tweet Template:</label>
        <select id="" name="<?= $fields['single_tweet_template']; ?>">
            <?php foreach( $tweet_templates as $tt ) { ?>
            <option value="<?= $tt; ?>" <?= ($options['single_tweet_template'] == $tt) ? 'selected="selected"' : ''; ?> ><?= $tt; ?></option>
            <?php } ?>
        </select>
    </p>
    <div>
        <p>If you prefer to use your own styles you can override the following in your stylesheet</p>
        <ul>
            <li>.twitter // the ul wrapper</li>
            <li>.tweet // the li</li>
            <li>.tweet a // anchors in the tweet</li>
            <li>.twitter-avatar // the thumbnail</li>
            <li>.tweet-time // the post date</li>
            <li>.tweet-message // the message</li>
        </ul>
    </div>
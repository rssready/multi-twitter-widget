=== Plugin Name ===
Contributors: rssready, clay.mcilrath, roger_hamilton, msenate
Tags: widgets, twitter, multi twitter, multiple twitter, twitter account
Requires at least: 2.8
Tested up to: 3.5.1

A fork and re-write of http://wordpress.org/plugins/multi-twitter-widget/ with
various improvements and bug fixes.

== Description ==

A plugin which provides a widget to show a stream of tweets. These tweets can be from
multiple users and multiple search terms. Most twitter widgets show tweets in
chronological order, and this is no different. However, we give each account equal 
representation. This means even if USER_A is more active than USER_B, the widget will show an equal number of tweets from both users.

Why the fork?
We have been using the multi-twitter-widget on many WP sites. It was small, simple, and easy to use. However, we were quickly bitten by it's shortcomings. It seems that development on both the original plugin, and it's other fork on github have stagnated. Since we intend to continue to use the plugin, it's in our best interest to keep development moving forward. And so, we've rolled up our sleeves and overhauled the plugin. 

New Features:
- You can use the widget multiple places.
- You can now select a single tweet template, so you can customize how you would like
  the tweets to be displayed.
- Added filter hooks to allow modify the wrapping <ul> by themes or other plugins.

Improvements:
- Fixed a bug where links were not being turned into <a> tags.
- Fixed a bug where hashtags were being linked to a deprecated service.
- Tweets are now correctly limited. For example, if you ask for 2 user tweets and 5 
  term tweets, you will now get 7 tweets (not the 2 you would have gotten before).
- Fixed a bug where an empty terms array would create an empty cache file.
- Fixed a bug where term tweets were being incorrectly written to the cache file.
- Plugin/Widget no longer use deprecated WP functions.
- Updated widget to be a WP_Widget class.
- DRY'ed up the code.
- Removed blatant self-promotion option.
- Removed "Default CSS" option.
- Removed separated PHP and HTML as best as possible.


== Installation ==
Estimated Time: 10 minutes

1. Upload the 'widget-twitter' folder to the /wp-content/plugins/ folder on your site
2. In WP-Admin, you should see 'Multi Twitter Widget' listed as an inactive plugin. Click the link to activate it. 
3. Once activated, go to Appearance > Widgets and Drop the Widget into your preferred sidebar
4. You will need oauth credentials for twitter, which means you'll have to create an app on https://dev.twitter.com/apps to get these credentials. Defaults have been provided, but there is no guarantee they will work long term.
5. Once you've dropped in the widget, enter twitter handles (space separated) and click save. 
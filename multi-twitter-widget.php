<?php
/* 
 Creates the Twitter Stream Widget class. Handles the actual work of pulling tweets
 and displaying them. 
*/
require_once('lib/helpers.php');

if( !class_exists('Codebird') ) {
    require_once('lib/codebird.php');
}

class Multi_Twitter_Widget extends WP_Widget {

    // Constructor (required by WP_Widget parent)
    function Multi_Twitter_Widget() {
        // Instantiate the parent object
        parent::__construct( false, 'Twitter Stream' );
        
        // Add tweet filter hooks
        add_filter('twitter_stream_tweet_text', array($this, 'at_replay_links_filter'), 10, 2);
        add_filter('twitter_stream_tweet_text', array($this, 'hashtag_links_filter'), 10, 2);
        add_filter('twitter_stream_tweet_text', array($this, 'link_links_filter'), 10, 2);
    }

    // Display the front-end for the widget. (required by WP_Widget parent)
    function widget( $args, $instance ) {
        // Widget output
        extract($args);
        
        echo $before_widget;
        echo $before_title;
        echo $instance['title'];
        echo $after_title;
    
        $this->twitter_stream($instance);
    
        echo $after_widget;
    }

    // Handle AJAX saves of the widget. (required by WP_Widget parent)
    function update( $new_instance, $old_instance ) {
        // Save widget options      
        $options = array();
        
        // oauth
        $options['consumer_key'] = htmlspecialchars($new_instance['consumer_key']);
        $options['consumer_secret'] = htmlspecialchars($new_instance['consumer_secret']);
        $options['access_token'] = htmlspecialchars($new_instance['access_token']);
        $options['access_token_secret'] = htmlspecialchars($new_instance['access_token_secret']);
        // twitter
        $options['title'] = htmlspecialchars($new_instance['title']);
        $options['users'] = htmlspecialchars($new_instance['users']);
        $options['terms'] = htmlspecialchars($new_instance['terms']);
        $options['user_limit'] = $new_instance['user_limit'];
        $options['term_limit'] = $new_instance['term_limit'];
        // options
        $options['hash'] = ($new_instance['hash']) ? true : false;
        $options['reply'] = ($new_instance['reply']) ? true : false;
        $options['links'] = ($new_instance['links']) ? true : false;
        $options['date'] = ($new_instance['date']) ? true : false;
        
        // Validate selected template
        list($template_directory, $template_name) = explode('/', $new_instance['single_tweet_template'], 2 );
        
        if( $this->is_real_template($template_directory, $template_name) ) {
            $options['single_tweet_template'] = $new_instance['single_tweet_template'];
            $options['single_tweet_template_path'] = $this->get_template_path($template_directory, $template_name);
        } else {
            // Don't change the options if the paths are weird.
            $options['single_tweet_template'] = $old_instance['single_tweet_template'];
            $options['single_tweet_template_path'] = $old_instance['single_tweet_template_path'];
        }
        
        return $options;
    }
    
    function get_template_directory($template_directory) {
        if( $template_directory == 'built-in' ) {
            $template_path = plugin_dir_path( __FILE__ ) . "templates/";
        } else {
            $template_path = get_stylesheet_directory() . '/';
        }
        
        return $template_path;
    }
    
    function get_template_path($template_directory, $template_name) {        
        return realpath($this->get_template_directory($template_directory) . $template_name);
    }
    
    function is_real_template($template_directory, $template_name) {
        // Ensure there is no relative path trickery going on.
        $template_path = $this->get_template_directory($template_directory);
        $real_path = realpath($template_path . $template_name);
        
        return starts_with($real_path, $template_path);

    }
    
    // Scans the templates directory and the current theme to find possible tweet
    // templates, and returns the valid ones.
    function find_tweet_templates() {
        $templates = array();
        
        $template_path = plugin_dir_path( __FILE__ ) . "templates/";
        $possible_templates = scandir($template_path);
        
        foreach($possible_templates as $pt) {
            if( !strncmp($pt, 'single-tweet', strlen('single-tweet')) ) {
                $templates[] = 'built-in/' . $pt;
            }
        }
        
        $current_theme_directory = get_stylesheet_directory();
        $possible_templates = scandir($current_theme_directory); 
        
        $current_theme = wp_get_theme();
        $current_theme_name = $current_theme->Name;
        
        foreach($possible_templates as $pt) {
            if( !strncmp($pt, 'single-tweet', strlen('single-tweet')) ) {
                $templates[] =  $current_theme_name . '/' . $pt;
            }
        }
        
        return $templates;
    }

    // Renders the form to edit widget settings. (required by WP_Widget parent)
    function form( $instance ) {
        // Output admin widget options form
        $options = $instance;
        
        $fields = array(
            'consumer_key' => $this->get_field_name('consumer_key'),
            'consumer_secret' => $this->get_field_name('consumer_secret'),
            'access_token' => $this->get_field_name('access_token'),
            'access_token_secret' => $this->get_field_name('access_token_secret'),
            'title' => $this->get_field_name('title'),
            'users' => $this->get_field_name('users'),
            'terms' => $this->get_field_name('terms'),
            'user_limit' => $this->get_field_name('user_limit'),
            'term_limit' => $this->get_field_name('term_limit'),
            'links' => $this->get_field_name('links'),
            'reply' => $this->get_field_name('reply'),
            'hash' => $this->get_field_name('hash'),
            'date' => $this->get_field_name('date'),
            'single_tweet_template' => $this->get_field_name('single_tweet_template')
        );
        
        $tweet_templates = $this->find_tweet_templates();
        
        require('templates/widget-admin.tpl');
    }
    
    // Returns the path to the cache directory.
    // It will also create the directory if it doesn't exist.
    function get_cache_dir() {
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'] . "/cache";
    
        if ( ! file_exists($upload_dir) )
        {
            if ( ! mkdir($upload_dir))
            {
                return false;
            }
        }
        
        return $upload_dir;
    }
    
    // Takes a path to a file and determines if it is new enough to load tweets from 
    function is_cache_file_unexpired( $file_path ) {
       $is_unexpired = false;
       
       if ( file_exists($file_path) ) {
            $modtime = filemtime($file_path);
            $timeago = time() - 1800;
            
            // 30 minutes ago
            if ( $modtime < $timeago )
            {
                // Set to false just in case as the cache needs to be renewed
                $is_unexpired = false;
            } 
            else
            {
                // The cache is not too old so the cache can be used.
                $is_unexpired = true;
            }
        }
        
        return $is_unexpired;
    }
    
    // Returns unserialized tweets from the provided file.
    function load_from_cache_file( $file_path ) {
        $str = file_get_contents($file_path);
        $content = unserialize($str);
                
        return $content;
    }
    
    // Writes pre-serialized content to a file.
    function write_to_cache_file( $content, $file_path ) {
        $fp = fopen($file_path, 'w');
                
        if (!$fp) {
            return false;
        } else {
            fwrite($fp, $content);
            fclose($fp);
            
            return true;
        }
    }
    
    // Returns a list of user's ($account) tweets.
    function get_user_tweets($account, $instance, $cb) {
        $params  = array(
            'screen_name' => $account, 
            'count' => $instance['user_limit']
        );
        
        // let Codebird make an authenticated request  Result is json
        $reply   = $cb->statuses_userTimeline($params);
        // turn the json into an array
        $json    = json_decode($reply, true);
        
        return $json;
    }
    
    // Returns a list of tweets related to search term.
    function get_term_tweets($term, $instance, $cb) {
        $search_params = array(
            'q' => $term,
            'count' => $instance['term_limit']
        );
        $reply = $cb->search_tweets($search_params);
        $json = json_decode($reply, true);
        
        return $json;
    }
    
    function at_replay_links_filter($tweet, $instance) {
        if( $instance['reply'] ) {
            $tweet = preg_replace('/(^|\s)@(\w+)/', '\1@<a href="http://twitter.com/\2">\2</a>', $tweet);
        }
        
        return $tweet;
    }
    
    function hashtag_links_filter($tweet, $instance) {
        if( $instance['hash'] ) {
            $tweet = preg_replace('/(^|\s)#(\w+)/', '\1#<a href="http://twitter.com/search?q=%23\2">\2</a>', $tweet);
        }
        
        return $tweet;
    }
    
    function link_links_filter($tweet, $instance) {
        if( $instance['links'] ) {
            $tweet = preg_replace('#(^|[\n ])(([\w]+?://[\w\#$%&~.\-;:=,?@\[\]+]*)(/[\w\#$%&~/.\-;:=,?@\[\]+]*)?)#is', '\\1<a href="\\2" target="_blank">\\2</a>', $tweet);
        }
        
        return $tweet;
    }
    
    // Builds the widget content for twitter stream widget $instance.
    function twitter_stream($instance) {
       
        // Initialize Codebird with our key.
        Codebird::setConsumerKey($instance['consumer_key'], $instance['consumer_secret']);
        
        // Create a Codebird instance and set the access_tokens.
        $cb = Codebird::getInstance();
        $cb->setToken($instance['access_token'], $instance['access_token_secret']);
        
        // Build all the output, piecewise.
        $output = '';
    
        // Get our root upload directory and create cache if necessary.
        $cache_dir = $this->get_cache_dir();
        
        // Check there wasn't an error with creating the cache directory.
        if( $cache_dir === false ) {
            $output .= '<span style="color: red;">could not create dir' . $cache_dir . ' please create this directory</span>';
            return $output;
        } 
    
        // split the accounts and search terms specified in the widget
        $accounts = explode(" ", $instance['users']);
        $terms = explode(", ", $instance['terms']);
    
        // Make sure user limits are set. 
        // If they aren't, choose something reasonable.
        if ( !$instance['user_limit'] ) {
            $instance['user_limit'] = 5;
        }
    
        if ( !$instance['term_limit'] ) {
            $instance['term_limit'] = 5;
        }
        
        // To begin, we're going to split the tweets between user and term.
        // Later, we'll recombine them into a single stream.
        $feeds = array(
            'user' => array(),
            'term' => array()
        );
        
        // Call the filter function to allow modifications to the opening <ul>
        $output .= apply_filters('twitter_stream_open_list_wrapper', '<ul>');
    
        // Go through each account, and load the tweets (from twitter or the cache)
        foreach ( $accounts as $account ) {
            // If an empty account slipped in, just skip it.
            if( trim($account) == "") { continue; }
            
            // Build path to the cache file.
            $cFile = "$cache_dir/users_$account.txt";
            
            // Test the cache file to see if it has expired, or not.
            $cache = $this->is_cache_file_unexpired($cFile);
            
            // If the cache has expired, pull the tweets.
            // Otherwise, load them from the cache file.
            if ( $cache === false ) {
                // Cache had expired, pull tweets from twitter.
                $tweets = $this->get_user_tweets($account, $instance, $cb);
                
                // Add the tweets to the user feed.
                $feeds['user'] = array_merge($feeds['user'], $tweets);
                
                // Save the tweets to the cache file.
                if( !$this->write_to_cache_file(serialize($tweets), $cFile) ) {
                    $output .= '<li style="color: red;">Permission to write cache dir to <em>' . $cFile . '</em> not granted</li>';
                }
            } else {
                // Cache is fresh, so we can just load tweets directly from it.
                $feeds['user'] = array_merge( $feeds['user'], $this->load_from_cache_file($cFile) );
            }
        }
        
        // Now all tweets have been loaded, we can sort them, then cut it down to the
        // max number of user tweets.
        usort($feeds['user'], "sort_tweets_by_created_at");
        
        $feeds['user'] = array_slice( $feeds['user'], 0, $instance['user_limit']);
        
        // Go through each term, and load the tweets (from twitter or the cache)
        foreach ( $terms as $term ) {
            // If an empty term slipped in, just skip it.
            if( trim($term) == "" ) { continue; }
            
            // Build path to the cache file.
            $cFile = "$cache_dir/term_$term.txt";
            
            // Test the cache file to see if it has expired, or not.
            $cache = $this->is_cache_file_unexpired($cFile);

            // If the cache has expired, pull the tweets.
            // Otherwise, load them from the cache file.
            if ( $cache === false ) {
                // Cache had expired, pull tweets from twitter.
                $tweets = $this->get_term_tweets($term, $instance, $cb);
                
                // Add the tweets to the term feed
                $feeds['term'] = array_merge($feeds['term'], $tweets);
    
                // Save the tweets to the cache file.
                if( !$this->write_to_cache_file(serialize($tweets), $cFile) ) {
                    $output .= '<li style="color: red;">Permission to write cache dir to <em>' . $cFile . '</em> not granted</li>';
                }
            } 
            else
            {
                // Cache is fresh, so we can just load tweets directly from it.
                $feeds['term'] = array_merge( $feeds['term'], $this->load_from_cache_file($cFile) );
            }
        }
        
        // Now all tweets have been loaded, sort, then cut it down to the max number
        // of term tweets.
        usort($feeds['term'], "sort_tweets_by_created_at");
        
        $feeds['term'] = array_slice( $feeds['term'], 0, $instance['term_limit']);
        
        // Combine user and term tweets
        $all_tweets = array_merge( $feeds['user'], $feeds['term'] );
    
        // Sort all the tweets together
        usort($all_tweets, "sort_tweets_by_created_at");
    
        // Format tweets for display
        foreach ( $all_tweets as $raw_tweet ) {
            if ( $raw_tweet['user']['screen_name'] != '' ) {
                $tweet = $raw_tweet;
                
                if( $instance['date'] ) {
                    $tweet['created_at'] = human_time(strtotime($raw_tweet['created_at']));
                }
                
                $tweet['text'] = apply_filters('twitter_stream_tweet_text', $raw_tweet['text'], $instance);
                
                ob_start();
                if( $instance['single_tweet_template_path'] != '' ) {
                    require($instance['single_tweet_template_path']);
                } else {
                    require('templates/single-tweet.tpl');
                }
                $output .= ob_get_clean();
            }
        }
        
        // Allow for modifications to the closing </ul> tag.
        $output .= apply_filters('twitter_stream_open_list_wrapper', '</ul>');
        
        echo $output;
    }
    
}

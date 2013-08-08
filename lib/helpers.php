<?php
/* A collection of helper functions. */

function human_time($datefrom, $dateto = -1)
{
    // Defaults and assume if 0 is passed in that its an error rather than the epoch
    if ( $datefrom <= 0 )
        return "A long time ago";

    if ( $dateto == -1 )
    {
        $dateto = time();
    }

    // Calculate the difference in seconds betweeen the two timestamps
    $difference = $dateto - $datefrom;

    // If difference is less than 60 seconds use 'seconds'
    if ($difference < 60)
    {
        $interval = "s";
    }
    // If difference is between 60 seconds and 60 minutes use 'minutes'
    else if ($difference >= 60 and $difference < (60 * 60))
        {
            $interval = "n";
        }
    // If difference is between 1 hour and 24 hours use 'hours'
    else if ($difference >= (60 * 60) and $difference < (60 * 60 * 24))
        {
            $interval = "h";
        }
    // If difference is between 1 day and 7 days use 'days'
    else if ($difference >= (60 * 60 * 24) and $difference < (60 * 60 * 24 * 7))
        {
            $interval = "d";
        }
    // If difference is between 1 week and 30 days use 'weeks'
    else if ($difference >= (60 * 60 * 24 * 7) and $difference < (60 * 60 * 24 * 30))
        {
            $interval = "ww";
        }
    // If difference is between 30 days and 365 days use 'months'
    else if ($difference >= (60 * 60 * 24 * 30) and $difference < (60 * 60 * 24 * 365))
        {
            $interval = "m";
        }
    // If difference is greater than or equal to 365 days use 'years'
    else if ($difference >= (60 * 60 * 24 * 365))
        {
            $interval = "y";
        }

    // Based on the interval, determine the number of units between the two dates
    // If the $datediff returned is 1, be sure to return the singular
    // of the unit, e.g. 'day' rather 'days'
    switch ( $interval )
    {
        case "m":
            $months_difference = floor($difference / 60 / 60 / 24 / 29);
            $date_from = mktime(
                date("H", $datefrom),
                date("i", $datefrom),
                date("s", $datefrom),
                date("n", $datefrom) + ($months_difference),
                date("j", $dateto), date("Y", $datefrom)
            );
    
            while ( $date_from < $dateto)
            {
                $months_difference++;
            }
            $datediff = $months_difference;
    
            // We need this in here because it is possible to have an 'm' interval and a months
            // difference of 12 because we are using 29 days in a month
            if ($datediff == 12)
            {
                $datediff--;
            }
            $res = ($datediff == 1) ? "$datediff month ago" : "$datediff months ago";
            break;
    
        case "y":
            $datediff = floor($difference / 60 / 60 / 24 / 365);
            $res      = ($datediff == 1) ? "$datediff year ago" : "$datediff years ago";
            break;
    
        case "d":
            $datediff = floor($difference / 60 / 60 / 24);
            $res      = ($datediff == 1) ? "$datediff day ago" : "$datediff days ago";
            break;
    
        case "ww":
            $datediff = floor($difference / 60 / 60 / 24 / 7);
            $res      = ($datediff == 1) ? "$datediff week ago" : "$datediff weeks ago";
            break;
    
        case "h":
            $datediff = floor($difference / 60 / 60);
            $res      = ($datediff == 1) ? "$datediff hour ago" : "$datediff hours ago";
            break;
    
        case "n":
            $datediff = floor($difference / 60);
            $res      = ($datediff == 1) ? "$datediff minute ago" : "$datediff minutes ago";
            break;
    
        case "s":
            $datediff = $difference;
            $res      = ($datediff == 1) ? "$datediff second ago" : "$datediff seconds ago";
            break;
    }

    return $res;
}

function starts_with($needle, $haystack) {
    return !strncmp($needle, $haystack, strlen($haystack));
}

function sort_tweets_by_created_at($a, $b)
{
    if ( $a[created_at] )
    {
        $a_t = strtotime($a[created_at]);
        $b_t = strtotime($b[created_at]);
    }
    else if ( $a[updated] )
    {
        $a_t = strtotime($a[updated]);
        $b_t = strtotime($b[updated]);
    }

    if ($a_t == $b_t)
        return 0;

    return ($a_t > $b_t) ? -1 : 1;
}
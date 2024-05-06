<?php

/**
 * Generates a human-readable string describing how long ago a timestamp occurred.
 *
 * @param int $timestamp The timestamp to check.
 * @param int $now       The current time reference point.
 *
 * @return string The time ago in a human-friendly format.
 *
 * @throws Exception if the timestamp is in the future.
 */
function time_ago( $timestamp = 0, $now = 0 ) {

    // Set up our variables.
    $minute_in_seconds = 60;
    $hour_in_seconds   = $minute_in_seconds * 60;
    $day_in_seconds    = $hour_in_seconds * 24;
    $week_in_seconds   = $day_in_seconds * 7;
    $month_in_seconds  = $day_in_seconds * 30;
    $year_in_seconds   = $day_in_seconds * 365;

    // Get the current time if a reference point has not been provided.
    if ( 0 === $now ) {
        $now = time();
    }

    // Make sure the timestamp to check is in the past.
    if ( $timestamp > $now ) {
        throw new Exception( 'Timestamp is in the future' );
    }

    // Calculate the time difference between the current time reference point and the timestamp we're comparing.
    $time_difference = (int) abs( $now - $timestamp );

    // Calculate the time ago using the smallest applicable unit.
    if ( $time_difference < $hour_in_seconds ) {

        $difference_value = round( $time_difference / $minute_in_seconds );
        $difference_label = 'minute';

    } elseif ( $time_difference < $day_in_seconds ) {

        $difference_value = round( $time_difference / $hour_in_seconds );
        $difference_label = 'hour';

    } elseif ( $time_difference < $week_in_seconds ) {

        $difference_value = round( $time_difference / $day_in_seconds );
        $difference_label = 'day';

    } elseif ( $time_difference < $month_in_seconds ) {

        $difference_value = round( $time_difference / $week_in_seconds );
        $difference_label = 'week';

    } elseif ( $time_difference < $year_in_seconds ) {

        $difference_value = round( $time_difference / $month_in_seconds );
        $difference_label = 'month';

    } else {

        $difference_value = round( $time_difference / $year_in_seconds );
        $difference_label = 'year';
    }

    if ( $difference_value <= 1 ) {
        $time_ago = sprintf( 'one %s ago',
            $difference_label
        );
    } else {
        $time_ago = sprintf( '%s %ss ago',
            $difference_value,
            $difference_label
        );
    }

    return $time_ago;
}

// Helper function to prepare html for links in comment body.
function displayTextWithLinks($s) {
  return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a target="blank" href="$1">$1</a>', $s);
}

// Helper function to preview image links beneath comments.
function getImagesLinks($s) {
  $matches = [];
  $num = preg_match_all('/(https?:\/\/\S+\.(?:jpg|jpeg|png|gif))/', $s, $matches);
  if ($num) {
    return array_values($matches);
  }
  else {
    return [];
  }
}

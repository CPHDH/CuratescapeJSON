<?php

// Start with an empty array of tours
$all_tours_metadata = array();

// Loop through all the tours
foreach( $tours as $tour )
{
   set_current_record( 'tour', $tour );
   $tourMetadata = $this->tourJsonifier( $tour );
   array_push( $all_tours_metadata, $tourMetadata );
}

$metadata = array(
   'tours'  => $all_tours_metadata,
);

// Encode and send
echo Zend_Json_Encoder::encode( $metadata );

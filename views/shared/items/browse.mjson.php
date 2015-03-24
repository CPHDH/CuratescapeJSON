<?php

// Start with an empty array of item metadata
$multipleItemMetadata = array();

// Loop through each item, picking up the minimum information needed.
// There will be no pagination, since the amount of information for each
// item will remain quite small.
foreach( loop( 'item' ) as $item )
{
   // If it doesn't have location data, we're not interested.
   $hasLocation = get_db()->getTable( 'Location' )->findLocationByItem( $item, true );
   if( $hasLocation )
   {
      $itemMetadata = $this->itemJsonifier( $item , false);
      array_push( $multipleItemMetadata, $itemMetadata );
   }
}

$metadata = array(
   'items'        => $multipleItemMetadata,
   'total_items'  => count( $multipleItemMetadata )
);

// I've heard that the Zend JSON encoder is really slow,
// if this becomes a problem, use the second line.
echo Zend_Json_Encoder::encode( $metadata );
//echo json_encode( $metadata );

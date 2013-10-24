<?php

// Start with an empty array of item metadata
$multipleItemMetadata = array();

// Loop through each item, picking up the minimum information needed.
// There will be no pagination, since the amount of information for each
// item will remain quite small.
foreach( loop( 'item' ) as $item )
{
   $itemMetadata = array();

   // Add the item ID and title
   $itemMetadata['id'] = $item->id;
   $itemMetadata['title'] = html_entity_decode(
      strip_formatting( metadata( 'item', array( 'Dublin Core', 'Title' ) ) ) );

   $itemMetadata['description'] = html_entity_decode(
      strip_formatting( metadata( 'item', array( 'Dublin Core', 'Description' ) ) ) );

   // Add location information if there is any available.
   $location = get_db()->getTable( 'Location' )->findLocationByItem( $item, true );
   if( $location )
   {
      $itemLatitude = $location['latitude'];
      $itemLongitude = $location['longitude'];

      $itemMetadata = array_merge( $itemMetadata,
         array(
            'latitude' => $itemLatitude,
            'longitude' => $itemLongitude,
         )
      );
   }

   array_push( $multipleItemMetadata, $itemMetadata );
}

$metadata = array(
   'items'        => $multipleItemMetadata,
   'total_items'  => count( $multipleItemMetadata )
);

// I've heard that the Zend JSON encoder is really slow,
// if this becomes a problem, use the second line.
echo Zend_Json_Encoder::encode( $metadata );
//echo json_encode( $metadata );

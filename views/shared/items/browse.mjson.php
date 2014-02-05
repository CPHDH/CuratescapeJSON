<?php

// Start with an empty array of item metadata
$multipleItemMetadata = array();

// Loop through each item, picking up the minimum information needed.
// There will be no pagination, since the amount of information for each
// item will remain quite small.

function getDublinText( $element, $formatted = false )
{
   $raw = metadata( 'item', array( 'Dublin Core', $element ) );
   if( ! $formatted )
      $raw = strip_formatting( $raw );

   return html_entity_decode( $raw );
}

foreach( loop( 'item' ) as $item )
{
   // If it doesn't have location data, we're not interested.
   $location = get_db()->getTable( 'Location' )->findLocationByItem( $item, true );
   if( $location )
   {
      $titles = metadata( 'item', array( 'Dublin Core', 'Title' ),
                          array( 'all' => true ) );
      $authors = metadata( 'item', array( 'Dublin Core', 'Creator' ),
                          array( 'all' => true ) );
      $authorsStripped = array();
      foreach( $authors as $auth )
      {
         $authorsStripped[] = html_entity_decode( strip_formatting( $auth ) );
      }

      $itemMetadata = array(
         'id'          => $item->id,
         'modified'    => $item->modified,
         'added'       => $item->added,
         'featured'    => $item->featured,

         'latitude'    => $location[ 'latitude' ],
         'longitude'   => $location[ 'longitude' ],

         'creator'     => $authorsStripped,
         'date'        => getDublinText( 'Date' ),
         'description' => getDublinText( 'Description', true ),
         'publisher'   => getDublinText( 'Publisher' ),
         'source'      => getDublinText( 'Source' ),
         'subject'     => getDublinText( 'Subject' ),
         'title'       => html_entity_decode( strip_formatting( $titles[0] ) )
      );

      // Add the subtitle (if available)
      if( count( $titles ) > 1 )
      {
         $itemMetadata[ 'subtitle' ] = html_entity_decode( strip_formatting( $titles[1] ) );
      }

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

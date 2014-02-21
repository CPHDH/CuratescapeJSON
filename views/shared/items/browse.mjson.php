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

function has_element( $name )
{
   return count( get_records( 'Element', array( 'element_name' => $name ) ) ) > 0;
}

$hasSponsor = has_element( 'Sponsor Name' );
$storage = Zend_Registry::get('storage');

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
         'featured'    => $item->featured,

         'latitude'    => $location[ 'latitude' ],
         'longitude'   => $location[ 'longitude' ],

         'creator'     => $authorsStripped,
         'description' => getDublinText( 'Description', true ),
         'title'       => html_entity_decode( strip_formatting( $titles[0] ) ),
      );

      // Add the subtitle (if available)
      if( count( $titles ) > 1 )
      {
         $itemMetadata[ 'subtitle' ] = html_entity_decode( strip_formatting( $titles[1] ) );
      }

      // Add sponsor (if it exists in the database)
      if( $hasSponsor )
      {
         if( $sponsor = metadata( 'item', array( 'Item Type Metadata', 'Sponsor Name' ) ) )
         {
            $itemMetadata[ 'sponsor' ] = html_entity_decode( strip_formatting( $sponsor ) );
         }
      }

      // Add files (version 2 does full sync immediately)
      $files = array();
      foreach( $item->Files as $file )
      {
         $path = $file->getWebPath( 'original' );
         $mimetype = metadata( $file, 'MIME Type' );

         $filedata = array(
            'id'        => $file->id,
            'mime-type' => $mimetype,
            'modified'  => $file->modified );

         $title = metadata( $file, array( 'Dublin Core', 'Title' ) );
         if( $title )
         {
            $filedata[ 'title' ] = strip_formatting( $title );
         }

         $description = metadata( $file, array( 'Dublin Core', 'Description' ) );
         if( $description )
         {
            $filedata[ 'description' ] = $description;
         }

         if( $file->hasThumbnail() )
         {
            $filedata[ 'thumbnail' ] = $file->getWebPath( 'thumbnail' );
         }

         if( strpos( $mimetype, 'image/' ) === 0 )
         {
            $p = $storage->getPathByType( $file->getStoragePath() );
            list( $width, $height ) = getimagesize( $p );
            $filedata[ 'width' ] = $width;
            $filedata[ 'height' ] = $height;
         }

         $files[ $path ] = $filedata;

      }

      if( count( $files ) > 0 )
      {
         $itemMetadata[ 'files' ] = $files;
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

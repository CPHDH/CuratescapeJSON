<?php

class MobileJson_View_Helper_ItemJsonifier extends Zend_View_Helper_Abstract
{
   public function __construct()
   {
      // Determine if the item type schemas have a 'Sponsor Name' element
      $this->hasSponsor = count( get_records( 'Element', array( 'element_name' => 'Sponsor Name' ) ) ) > 0;
      $this->storage = Zend_Registry::get('storage');
   }

   private static function getDublinText( $element, $formatted = false )
   {
      $raw = metadata( 'item', array( 'Dublin Core', $element ) );
      if( ! $formatted )
         $raw = strip_formatting( $raw );

      return html_entity_decode( $raw );
   }


   public function itemJsonifier( $item )
   {
      // If it doesn't have location data, we're not interested.
      $location = get_db()->getTable( 'Location' )->findLocationByItem( $item, true );
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
         'description' => self::getDublinText( 'Description', true ),
         'title'       => html_entity_decode( strip_formatting( $titles[0] ) ),
                            );

      // Add the subtitle (if available)
      if( count( $titles ) > 1 )
      {
         $itemMetadata[ 'subtitle' ] = html_entity_decode( strip_formatting( $titles[1] ) );
      }

      // Add sponsor (if it exists in the database)
      if( $this->hasSponsor )
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
            $p = $this->storage->getPathByType( $file->getStoragePath() );
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

      return $itemMetadata;
   }
}
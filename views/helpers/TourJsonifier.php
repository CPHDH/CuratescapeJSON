<?php

class MobileJson_View_Helper_TourJsonifier extends Zend_View_Helper_Abstract
{
   public function __construct()
   {

   }

   public function tourJsonifier( $tour )
   {
      // Add enumarations of the ordered items in this tour.
      $items = array();
      foreach( $tour->Items as $item )
      {
         set_current_record( 'item', $item );
         $location = get_db()->getTable('Location')->findLocationByItem($item, true);

         // If it has a location, we'll build the itemMetadata array and push it to items
         if($location){
            $item_metadata = array(
               'id'          => $item->id,
               'title'       => metadata( 'item', array( 'Dublin Core', 'Title' ) ),
               'latitude' 	=> $location['latitude'],
               'longitude' 	=> $location['longitude']
                );

				if( element_exists('Item Type Metadata','Street Address') )
				{
					$address=metadata( 'item', array( 'Item Type Metadata', 'Street Address' ) );
					if($address){
						$item_metadata['address']=$address;
					}
				}
                
            array_push( $items, $item_metadata );
         }

      }

      // Create the array of data
      $tour_metadata = array(
         'id'           => $tour->id,
         'title'        => $tour->title,
         'description'  => $tour->description,
         'items'        => $items );

       $tourFiles = array();
       if($tour->hasImage('original')) {
           $tourFiles = array(
               array(
                    'image' => "original",
                    'type' => "original",
                   'func' => "image"
               )
           );
       }
       
       // Add files (version 2 does full sync immediately)
      $files = array();
      foreach( $tourFiles as $file )
      {
          $uri = $tour->$file['func'](false);
          $image_path = str_replace('/',DIRECTORY_SEPARATOR,$uri);
          $serverPrefix = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
          $imgUrl = $serverPrefix . $uri;

         $filedata = array(
            'id'        => $tour->id,
            'mime-type' => 'image/jpeg',
            'modified'  => filemtime($_SERVER['DOCUMENT_ROOT'] . $image_path)
        );

             $filedata['title'] = 'tour_'.$tour->id.'.jpg';

         if( $tour->hasImage('thumbnails') )
         {
             $filedata['thumbnail'] = $serverPrefix . $tour->thumbnail(false);
         }

          if($tour->hasImage('original')) {
            list( $width, $height ) = getimagesize( $_SERVER['DOCUMENT_ROOT'] . $image_path );
            $filedata[ 'width' ] = $width;
            $filedata[ 'height' ] = $height;
         }

          // Keeping this in here in case
          // of later date we want the description
          // to pass down or something
          /*
         $caption = array();
         $description = metadata( $file, array( 'Dublin Core', 'Description' ) );
         if( $description )
         {
            $caption[] = $description;
         }

         $source = metadata( $file, array( 'Dublin Core', 'Source' ) );
         if( $source )
         {
            $caption[] = $source;
         }

         $creator = metadata( $file, array( 'Dublin Core', 'Creator' ) );
         if( $creator )
         {
            $caption[] = $creator;
         }

         if( count( $caption ) )
         {
            $filedata[ 'description' ] = implode( " | ", $caption );
         }
         */

         $files[ $imgUrl ] = $filedata;

      }

      if( count( $files ) > 0 )
      {
         $tour_metadata[ 'files' ] = $files;
      }
       
      return $tour_metadata;
   }
}
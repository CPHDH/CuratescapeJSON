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
            array_push( $items, $item_metadata );
         }

      }

      // Create the array of data
      $tour_metadata = array(
         'id'           => $tour->id,
         'title'        => $tour->title,
         'description'  => $tour->description,
         'image'        => $tour->image(false),
         'thumbnail'    => $tour->thumbnail(false),
         'square_thumbnail' => $tour->square_thumbnail(false),
         'items'        => $items );

      return $tour_metadata;
   }
}
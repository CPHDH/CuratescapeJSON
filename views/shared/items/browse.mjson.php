<?php

echo '{"items":[';

// Loop through each item, picking up the minimum information needed.
// There will be no pagination, since the amount of information for each
// item will remain quite small.
$itemCount = 0;
foreach( loop( 'item' ) as $item )
{
   // If it doesn't have location data, we're not interested.
   $hasLocation = get_db()->getTable( 'Location' )->findLocationByItem( $item, true );
   if( $hasLocation )
   {
      if( $itemCount > 0 )
      {
         echo ',';
      }

      $itemMetadata = $this->itemJsonifier( $item );
      echo Zend_Json_Encoder::encode( $itemMetadata );
      $itemCount += 1;
   }
}

echo '], "total_items":' . $itemCount . '}';

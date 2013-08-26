<?php

class MobileJsonPlugin extends Omeka_Plugin_AbstractPlugin
{
   protected $_filters = array(
      'public_items_show',
      'response_contexts',
      'action_contexts' );

   private $_controller;

   public function filterPublicItemsShow()
   {

   }

   public function filterResponseContexts( $contexts )
   {
      $contexts['mobile-json'] = array( 'suffix' => 'mjson',
                                        'headers' => array(
                                           'Content-Type' => 'application/json' ) );
      return $contexts;
   }

   public function filterActionContexts( $contexts, $args ) {
      $controller = $args['controller'];

      if( ($controller instanceof ItemsController) or
          is_a( $controller, 'TourBuilder_ToursController' ) )
      {
         $contexts['browse'] = array( 'mobile-json' );
      }

      return $contexts;
   }
}
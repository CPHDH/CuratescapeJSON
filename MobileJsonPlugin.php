<?php

class MobileJsonPlugin extends Omeka_Plugin_AbstractPlugin
{
   protected $_hooks = array(
      'initialize' );

   protected $_filters = array(
      'response_contexts',
      'action_contexts' );

   public function hookInitialize()
   {
      get_view()->addHelperPath( dirname( __FILE__ ) . '/views/helpers', 'MobileJson_View_Helper_' );
   }

   public function filterResponseContexts( $contexts )
   {
      $contexts['mobile-json'] = array(
         'suffix' => 'mjson',
         'headers' => array( 'Content-Type' => 'application/json' ) );
      $contexts['tiny-mobile-json'] = array(
         'suffix' => 'tmjson',
         'headers' => array( 'Content-Type' => 'application/json' ) );
      return $contexts;
   }

   public function filterActionContexts( $contexts, $args ) {
      $controller = $args['controller'];

      if( is_a( $controller, 'ItemsController' ) or
          is_a( $controller, 'TourBuilder_ToursController' ) or 
          is_a( $controller, 'SearchController' ) )
      {
         $contexts['browse'][] = 'mobile-json' ;
         $contexts['browse'][] = 'tiny-mobile-json' ;
         $contexts['show'][] = 'mobile-json' ;
         $contexts['show'][] = 'tiny-mobile-json' ;
         $contexts['index'][] = 'mobile-json' ;
      }

      return $contexts;
   }
}
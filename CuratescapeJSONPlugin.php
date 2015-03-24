<?php

class CuratescapeJSONPlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_hooks = array(
		'initialize' );

	protected $_filters = array(
		'response_contexts',
		'action_contexts' );

	public function hookInitialize()
	{
		get_view()->addHelperPath( dirname( __FILE__ ) . '/views/helpers', 'CuratescapeJSON_View_Helper_' );
	}

	public function filterResponseContexts( $contexts )
	{
		$contexts['mobile-json'] = array(
			'suffix' => 'mjson',
			'headers' => array( 'Content-Type' => 'application/json' ) );
		$contexts['mobile-json-extended'] = array(
			'suffix' => 'mjsonx',
			'headers' => array( 'Content-Type' => 'application/json' ) );
		return $contexts;
	}

	public function filterActionContexts( $contexts, $args ) {
		$controller = $args['controller'];

		if( is_a( $controller, 'ItemsController' ) or
			is_a( $controller, 'TourBuilder_ToursController' ) or
			is_a( $controller, 'SearchController' ) or
			is_a( $controller, 'SimplePages_PageController' ) )
		{
			$contexts['browse'][] = 'mobile-json' ;
			$contexts['browse'][] = 'mobile-json-extended' ;
			$contexts['show'][] = 'mobile-json' ;
			$contexts['show'][] = 'mobile-json-extended' ;
			$contexts['index'][] = 'mobile-json' ;
		}

		return $contexts;
	}
}
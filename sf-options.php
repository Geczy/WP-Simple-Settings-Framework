<?php
$options = array();

$options[] = array( 'name' => __( 'General', 'geczy' ), 'type' => 'heading' );
$options[] = array( 'name' => __( 'General options', 'geczy' ), 'type' => 'title', 'desc' => __( '', 'geczy' ) );

$options[] = array(
	'name' => __( 'Default commission (%)', 'geczy' ),
	'desc' => __( 'Choose the default rate for each product. If a product has a commission rate already set, this value will be ignored for that product.', 'geczy' ),
	'id'   => 'default_commission',
	'css' => 'width:70px;',
	'type' => 'number',
	'restrict' => array(
		'min' => 0,
		'max' => 100
	)
);

$options[] = array(
	'name' => __( 'Orders page', 'geczy' ),
	'desc' => __( 'Choose the page that has the shortcode <code>[pv_orders]</code>', 'geczy' ),
	'id'   => 'orders_page',
	'type' => 'single_select_page',
);

$options[] = array(
	'name' => __( 'Show orders', 'geczy' ),
	'desc' => __( 'Allow vendors to view order details for their products', 'geczy' ),
	'id'   => 'show_orders',
	'type' => 'checkbox',
);

$options[] = array( 'name' => __( 'Advanced', 'geczy' ), 'type' => 'heading' );
$options[] = array( 'name' => __( 'Advanced options', 'geczy' ), 'type' => 'title', 'desc' => __( 'There\'s awesome options on this page to configure!', 'geczy' ) );

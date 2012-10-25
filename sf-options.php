<?php
$options = array();

$options[] = array( 'name' => __( 'General', 'geczy' ), 'type' => 'heading' );
$options[] = array( 'name' => __( 'General options', 'geczy' ), 'type' => 'title', 'desc' => __( '', 'geczy' ) );

$options[] = array(
	'name' => __( 'Name', 'geczy' ),
	'desc' => __( 'Please tell me who you are.', 'geczy' ),
	'id'   => 'text_sample',
	'type' => 'text',
);

$options[] = array(
	'name' => __( 'Age', 'geczy' ),
	'desc' => __( 'What\'s your age, buddy?.', 'geczy' ),
	'tip'  => __('It\'s simple, just enter your age!', 'geczy'),
	'id'   => 'number_sample',
	'css' => 'width:70px;',
	'type' => 'number',
	'restrict' => array(
		'min' => 0,
		'max' => 100
	)
);

$options[] = array(
	'name' => __( 'Describe yourself', 'geczy' ),
	'desc' => __( 'Which word describes you best?.', 'geczy' ),
	'tip'  => __('If you can\'t choose, I\'ve defaulted an option for you.', 'geczy'),
	'std'  => 'gorgeous', //default is the slug value inputted here
	'id'   => 'radio_sample',
	'type' => 'radio',
	'options' => array(
		'gorgeous' => 'Gorgeous',
		'pretty' => 'Pretty'
	)
);

$options[] = array(
	'name' => __( 'Biography', 'geczy' ),
	'desc' => __( 'So tell me about yourself.', 'geczy' ),
	'id'   => 'textarea_sample',
	'type' => 'textarea',
);

$options[] = array(
	'name' => __( 'Wordpress page', 'geczy' ),
	'desc' => __( 'Pick your favorite page!', 'geczy' ),
	'tip'  => __('Or maybe you don\'t have a favorite?', 'geczy'),
	'id'   => 'single_select_page_sample',
	'type' => 'single_select_page',
);

$options[] = array(
	'name' => __( 'Would you rather have', 'geczy' ),
	'desc' => __( 'Which would you rather have?.', 'geczy' ),
	'id'   => 'select_sample',
	'type' => 'select',
	'options' => array(
		'tenbucks' => 'Ten dollars',
		'redhead' => 'A readheaded girlfriend',
		'tofly' => 'Flying powers',
		'lolwhat' => 'Three hearts',
	)
);

$options[] = array(
	'name' => __( 'Terms', 'geczy' ),
	'desc' => __( 'Agree to my terms...Or else.', 'geczy' ),
	'id'   => 'checkbox_sample',
	'type' => 'checkbox',
);

$options[] = array( 'name' => __( 'More tabs', 'geczy' ), 'type' => 'heading' );
$options[] = array( 'name' => __( 'More tabs', 'geczy' ), 'type' => 'title', 'desc' => __( 'There\'s awesome options on this page to configure!', 'geczy' ) );

$options[] = array(
	'name' => __( 'Awesome', 'geczy' ),
	'desc' => __( 'Is this awesome or what?', 'geczy' ),
	'id'   => 'checkbox_sample2',
	'type' => 'checkbox',
);
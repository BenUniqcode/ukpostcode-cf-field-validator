<?php
/**
 * @link              https://uniqcode.com
 * @since             1.0.0
 * @package           ukpostcode-cf-validator
 *
 * @wordpress-plugin
 * Plugin Name:       Caldera Forms UK Postcode Validator
 * Plugin URI:        https://uniqcode.com
 * Description:       Caldera Forms custom validator for UK postcodes
 * Version:           1.0.0
 * Author:            Ben Wheeler
 * Author URI:        https://uniqcode.com
 * Text Domain:       ukpostcode-cf-validator
 * Domain Path:       /languages
 */
add_filter('caldera_forms_get_form_processors', 'ukpostcode_cf_validator_processor');

/**
 * Add a custom processor for field validation
 *
 * @uses 'ukpostcode_cf_validator_processor'
 *
 * @param array $processors Processor configs
 *
 * @return array
 */
function ukpostcode_cf_validator_processor($processors){
    $processors['ukpostcode_cf_validator'] = array(
        'name' => __('UK Postcode Validator', 'ukpostcode-cf-validator' ),
        'description' => 'Validate and Canonicalise UK Postcodes',
        'pre_processor' => 'ukpostcode_validator',
        'template' => dirname(__FILE__) . '/config.php'

    );

    return $processors;
}

/**
 * Run field validation
 *
 * @param array $config Processor config
 * @param array $form Form config
 *
 * @return array|void Error array if needed, else void.
 */
function ukpostcode_validator( array $config, array $form ){

    //Processor data object
    $data = new Caldera_Forms_Processor_Get_Data( $config, $form, ukpostcode_cf_validator_fields() );

    //Value of field to be validated
    $value = $data->get_value( 'field-to-validate' );

    //if not valid, return an error
    if( false == ukpostcode_cf_validator_is_valid( $value ) ){

        //get ID of field to put error on
        $fields = $data->get_fields();
        $field_id = $fields[ 'field-to-validate' ][ 'config_field' ];

        //Get label of field to use in error message above form
        $field = $form[ 'fields' ][ $field_id ];
        $label = $field[ 'label' ];

        //this is error data to send back
        return array(
            'type' => 'error',
            //this message will be shown above form
            'note' => sprintf( 'Please Correct %s', $label ),
            //Add error messages for any form field
            'fields' => array(
                //This error message will be shown below the field that we are validating
                $field_id => __( 'This field is invalid', 'ukpostcode-cf-validator' )
            )
        );
    }

    //If everything is good, don't return anything!

}


/**
 * Check if value is valid
 *
 * UPDATE THIS! Use your array of values, or query the database here.
 *
 * @return bool
 */
function ukpostcode_cf_validator_is_valid( $value ){
    $value = preg_replace('/\s+/', '', $value);
    return preg_match('/^[a-z]{1,2}[0-9]{2,3}[a-z]{2}$/', $value);
}

/**
 * Processor fields
 *
 * @return array
 */
function ukpostcode_cf_validator_fields(){
    return array(
        array(
            'id' => 'field-to-validate',
            'type' => 'text',
            'required' => true,
            'magic' => true,
            'label' => __( 'Magic tag for field to validate.', 'ukpostcode-cf-validator' )
        ),
    );
}

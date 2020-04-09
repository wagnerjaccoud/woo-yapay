<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Yapay_Intermediador_Bankslip_Gateway' ) ) :

/**
 * WooCommerce Yapay Intermediador main class.
 */
class WC_Yapay_Intermediador_Bankslip_Gateway extends WC_Payment_Gateway {
    
    function __construct() {

        $version = "0.1.0";
        // The global ID for this Payment method
        $this->id = "wc_yapay_intermediador_bs";

        // The Title shown on the top of the Payment Gateways Page next to all the other Payment Gateways
        $this->method_title = __( "Yapay Intermediador - Boleto Bancário", 'wc-yapay_intermediador-bs' );

        // The description for this Payment Gateway, shown on the actual Payment options page on the backend
        $this->method_description = __( "Plugin Yapay Intermediador para WooCommerce", 'wc-yapay_intermediador-bs' );

        // The title to be used for the vertical tabs that can be ordered top to bottom
        $this->title = __( "Yapay Intermediador", 'wc-yapay_intermediador-bs' );

        // If you want to show an image next to the gateway's name on the frontend, enter a URL to an image.
        $this->icon = null;

        // Bool. Can be set to true if you want payment fields to show on the checkout 
        // if doing a direct integration, which we are doing in this case
        $this->has_fields = true;

        // Supports the default credit card form
        $this->supports = array( 'default_credit_card_form' );

        // This basically defines your settings which are then loaded with init_settings()
        $this->init_form_fields();

        // After init_settings() is called, you can get the settings and load them into variables, e.g:
        // $this->title = $this->get_option( 'title' );
        $this->init_settings();

        // Turn these settings into variables we can use
        foreach ( $this->settings as $setting_key => $value ) {
            $this->$setting_key = $value;
        }

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'receipt_page' ) );
        //add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );

        if ( is_admin() ) {
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }       
    }
    
    // Build the administration fields for this specific Gateway
    public function init_form_fields() {
        add_thickbox();
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __( 'Ativar / Desativar', 'wc-yapay_intermediador-bs' ),
                'label'     => __( 'Ativar Yapay Intermediador', 'wc-yapay_intermediador-bs' ),
                'type'      => 'checkbox',
                'default'   => 'no',
                'description'     => __( 'Ativar / Desativar pagamento por Yapay Intermediador', 'wc-yapay_intermediador-bs' ),
            ),
            'title' => array(
                'title'     => __( 'Titulo', 'wc-yapay_intermediador-bs' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Titulo do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-bs' ),
                'default'   => __( 'Yapay Intermediador - Boleto Bancário', 'wc-yapay_intermediador-bs' ),
            ),
            'description' => array(
                'title'     => __( 'Descrição', 'wc-yapay_intermediador-bs' ),
                'type'      => 'textarea',
                'desc_tip'  => __( 'Descrição do meio de pagamento que os compradores visualizarão durante o processo de finalização de compra.', 'wc-yapay_intermediador-bs' ),
                'default'   => __( 'A maneira mais fácil e segura e comprar pela internet.', 'wc-yapay_intermediador-bs' ),
                'css'       => 'max-width:350px;'
            ),
            'feriados'      => array(
        'type'              => 'multiselect',
        'title'             => __('Feriados', 'wc-yapay_intermediador-bs'),
        'desc_tip'          => __( 'Selecione os dias do ano que você deseja que o plugin evite gerar datas de vencimento para aumentar a conversão.', 'wc-yapay_intermediador-bs' ),
        'options'           =>__( array('01/01' => '01/01',
                                        '02/01' => '02/01',
                                        '03/01' => '03/01',
                                        '04/01' => '04/01',
                                        '05/01' => '05/01',
                                        '06/01' => '06/01',
                                        '07/01' => '07/01',
                                        '08/01' => '08/01',
                                        '09/01' => '09/01',
                                        '10/01' => '10/01',
                                        '11/01' => '11/01',
                                        '12/01' => '12/01',
                                        '13/01' => '13/01',
                                        '14/01' => '14/01',
                                        '15/01' => '15/01',
                                        '16/01' => '16/01',
                                        '17/01' => '17/01',
                                        '18/01' => '18/01',
                                        '19/01' => '19/01',
                                        '20/01' => '20/01',
                                        '21/01' => '21/01',
                                        '22/01' => '22/01',
                                        '23/01' => '23/01',
                                        '24/01' => '24/01',
                                        '25/01' => '25/01',
                                        '26/01' => '26/01',
                                        '27/01' => '27/01',
                                        '28/01' => '28/01',
                                        '29/01' => '29/01',
                                        '30/01' => '30/01',
                                        '31/01' => '31/01',
                                        '01/02' => '01/02',
                                        '02/02' => '02/02',
                                        '03/02' => '03/02',
                                        '04/02' => '04/02',
                                        '05/02' => '05/02',
                                        '06/02' => '06/02',
                                        '07/02' => '07/02',
                                        '08/02' => '08/02',
                                        '09/02' => '09/02',
                                        '10/02' => '10/02',
                                        '11/02' => '11/02',
                                        '12/02' => '12/02',
                                        '13/02' => '13/02',
                                        '14/02' => '14/02',
                                        '15/02' => '15/02',
                                        '16/02' => '16/02',
                                        '17/02' => '17/02',
                                        '18/02' => '18/02',
                                        '19/02' => '19/02',
                                        '20/02' => '20/02',
                                        '21/02' => '21/02',
                                        '22/02' => '22/02',
                                        '23/02' => '23/02',
                                        '24/02' => '24/02',
                                        '25/02' => '25/02',
                                        '26/02' => '26/02',
                                        '27/02' => '27/02',
                                        '28/02' => '28/02',
                                        '29/02' => '29/02',
                                        '01/03' => '01/03',
                                        '02/03' => '02/03',
                                        '03/03' => '03/03',
                                        '04/03' => '04/03',
                                        '05/03' => '05/03',
                                        '06/03' => '06/03',
                                        '07/03' => '07/03',
                                        '08/03' => '08/03',
                                        '09/03' => '09/03',
                                        '10/03' => '10/03',
                                        '11/03' => '11/03',
                                        '12/03' => '12/03',
                                        '13/03' => '13/03',
                                        '14/03' => '14/03',
                                        '15/03' => '15/03',
                                        '16/03' => '16/03',
                                        '17/03' => '17/03',
                                        '18/03' => '18/03',
                                        '19/03' => '19/03',
                                        '20/03' => '20/03',
                                        '21/03' => '21/03',
                                        '22/03' => '22/03',
                                        '23/03' => '23/03',
                                        '24/03' => '24/03',
                                        '25/03' => '25/03',
                                        '26/03' => '26/03',
                                        '27/03' => '27/03',
                                        '28/03' => '28/03',
                                        '29/03' => '29/03',
                                        '30/03' => '30/03',
                                        '31/03' => '31/03',
                                        '01/04' => '01/04',
                                        '02/04' => '02/04',
                                        '03/04' => '03/04',
                                        '04/04' => '04/04',
                                        '05/04' => '05/04',
                                        '06/04' => '06/04',
                                        '07/04' => '07/04',
                                        '08/04' => '08/04',
                                        '09/04' => '09/04',
                                        '10/04' => '10/04',
                                        '11/04' => '11/04',
                                        '12/04' => '12/04',
                                        '13/04' => '13/04',
                                        '14/04' => '14/04',
                                        '15/04' => '15/04',
                                        '16/04' => '16/04',
                                        '17/04' => '17/04',
                                        '18/04' => '18/04',
                                        '19/04' => '19/04',
                                        '20/04' => '20/04',
                                        '21/04' => '21/04',
                                        '22/04' => '22/04',
                                        '23/04' => '23/04',
                                        '24/04' => '24/04',
                                        '25/04' => '25/04',
                                        '26/04' => '26/04',
                                        '27/04' => '27/04',
                                        '28/04' => '28/04',
                                        '29/04' => '29/04',
                                        '30/04' => '30/04',
                                        '01/05' => '01/05',
                                        '02/05' => '02/05',
                                        '03/05' => '03/05',
                                        '04/05' => '04/05',
                                        '05/05' => '05/05',
                                        '06/05' => '06/05',
                                        '07/05' => '07/05',
                                        '08/05' => '08/05',
                                        '09/05' => '09/05',
                                        '10/05' => '10/05',
                                        '11/05' => '11/05',
                                        '12/05' => '12/05',
                                        '13/05' => '13/05',
                                        '14/05' => '14/05',
                                        '15/05' => '15/05',
                                        '16/05' => '16/05',
                                        '17/05' => '17/05',
                                        '18/05' => '18/05',
                                        '19/05' => '19/05',
                                        '20/05' => '20/05',
                                        '21/05' => '21/05',
                                        '22/05' => '22/05',
                                        '23/05' => '23/05',
                                        '24/05' => '24/05',
                                        '25/05' => '25/05',
                                        '26/05' => '26/05',
                                        '27/05' => '27/05',
                                        '28/05' => '28/05',
                                        '29/05' => '29/05',
                                        '30/05' => '30/05',
                                        '31/05' => '31/05',
                                        '01/06' => '01/06',
                                        '02/06' => '02/06',
                                        '03/06' => '03/06',
                                        '04/06' => '04/06',
                                        '05/06' => '05/06',
                                        '06/06' => '06/06',
                                        '07/06' => '07/06',
                                        '08/06' => '08/06',
                                        '09/06' => '09/06',
                                        '10/06' => '10/06',
                                        '11/06' => '11/06',
                                        '12/06' => '12/06',
                                        '13/06' => '13/06',
                                        '14/06' => '14/06',
                                        '15/06' => '15/06',
                                        '16/06' => '16/06',
                                        '17/06' => '17/06',
                                        '18/06' => '18/06',
                                        '19/06' => '19/06',
                                        '20/06' => '20/06',
                                        '21/06' => '21/06',
                                        '22/06' => '22/06',
                                        '23/06' => '23/06',
                                        '24/06' => '24/06',
                                        '25/06' => '25/06',
                                        '26/06' => '26/06',
                                        '27/06' => '27/06',
                                        '28/06' => '28/06',
                                        '29/06' => '29/06',
                                        '30/06' => '30/06',
                                        '01/07' => '01/07',
                                        '02/07' => '02/07',
                                        '03/07' => '03/07',
                                        '04/07' => '04/07',
                                        '05/07' => '05/07',
                                        '06/07' => '06/07',
                                        '07/07' => '07/07',
                                        '08/07' => '08/07',
                                        '09/07' => '09/07',
                                        '10/07' => '10/07',
                                        '11/07' => '11/07',
                                        '12/07' => '12/07',
                                        '13/07' => '13/07',
                                        '14/07' => '14/07',
                                        '15/07' => '15/07',
                                        '16/07' => '16/07',
                                        '17/07' => '17/07',
                                        '18/07' => '18/07',
                                        '19/07' => '19/07',
                                        '20/07' => '20/07',
                                        '21/07' => '21/07',
                                        '22/07' => '22/07',
                                        '23/07' => '23/07',
                                        '24/07' => '24/07',
                                        '25/07' => '25/07',
                                        '26/07' => '26/07',
                                        '27/07' => '27/07',
                                        '28/07' => '28/07',
                                        '29/07' => '29/07',
                                        '30/07' => '30/07',
                                        '31/07' => '31/07',
                                        '01/08' => '01/08',
                                        '02/08' => '02/08',
                                        '03/08' => '03/08',
                                        '04/08' => '04/08',
                                        '05/08' => '05/08',
                                        '06/08' => '06/08',
                                        '07/08' => '07/08',
                                        '08/08' => '08/08',
                                        '09/08' => '09/08',
                                        '10/08' => '10/08',
                                        '11/08' => '11/08',
                                        '12/08' => '12/08',
                                        '13/08' => '13/08',
                                        '14/08' => '14/08',
                                        '15/08' => '15/08',
                                        '16/08' => '16/08',
                                        '17/08' => '17/08',
                                        '18/08' => '18/08',
                                        '19/08' => '19/08',
                                        '20/08' => '20/08',
                                        '21/08' => '21/08',
                                        '22/08' => '22/08',
                                        '23/08' => '23/08',
                                        '24/08' => '24/08',
                                        '25/08' => '25/08',
                                        '26/08' => '26/08',
                                        '27/08' => '27/08',
                                        '28/08' => '28/08',
                                        '29/08' => '29/08',
                                        '30/08' => '30/08',
                                        '31/08' => '31/08',
                                        '01/09' => '01/09',
                                        '02/09' => '02/09',
                                        '03/09' => '03/09',
                                        '04/09' => '04/09',
                                        '05/09' => '05/09',
                                        '06/09' => '06/09',
                                        '07/09' => '07/09',
                                        '08/09' => '08/09',
                                        '09/09' => '09/09',
                                        '10/09' => '10/09',
                                        '11/09' => '11/09',
                                        '12/09' => '12/09',
                                        '13/09' => '13/09',
                                        '14/09' => '14/09',
                                        '15/09' => '15/09',
                                        '16/09' => '16/09',
                                        '17/09' => '17/09',
                                        '18/09' => '18/09',
                                        '19/09' => '19/09',
                                        '20/09' => '20/09',
                                        '21/09' => '21/09',
                                        '22/09' => '22/09',
                                        '23/09' => '23/09',
                                        '24/09' => '24/09',
                                        '25/09' => '25/09',
                                        '26/09' => '26/09',
                                        '27/09' => '27/09',
                                        '28/09' => '28/09',
                                        '29/09' => '29/09',
                                        '30/09' => '30/09',
                                        '01/10' => '01/10',
                                        '02/10' => '02/10',
                                        '03/10' => '03/10',
                                        '04/10' => '04/10',
                                        '05/10' => '05/10',
                                        '06/10' => '06/10',
                                        '07/10' => '07/10',
                                        '08/10' => '08/10',
                                        '09/10' => '09/10',
                                        '10/10' => '10/10',
                                        '11/10' => '11/10',
                                        '12/10' => '12/10',
                                        '13/10' => '13/10',
                                        '14/10' => '14/10',
                                        '15/10' => '15/10',
                                        '16/10' => '16/10',
                                        '17/10' => '17/10',
                                        '18/10' => '18/10',
                                        '19/10' => '19/10',
                                        '20/10' => '20/10',
                                        '21/10' => '21/10',
                                        '22/10' => '22/10',
                                        '23/10' => '23/10',
                                        '24/10' => '24/10',
                                        '25/10' => '25/10',
                                        '26/10' => '26/10',
                                        '27/10' => '27/10',
                                        '28/10' => '28/10',
                                        '29/10' => '29/10',
                                        '30/10' => '30/10',
                                        '31/10' => '31/10',
                                        '01/11' => '01/11',
                                        '02/11' => '02/11',
                                        '03/11' => '03/11',
                                        '04/11' => '04/11',
                                        '05/11' => '05/11',
                                        '06/11' => '06/11',
                                        '07/11' => '07/11',
                                        '08/11' => '08/11',
                                        '09/11' => '09/11',
                                        '10/11' => '10/11',
                                        '11/11' => '11/11',
                                        '12/11' => '12/11',
                                        '13/11' => '13/11',
                                        '14/11' => '14/11',
                                        '15/11' => '15/11',
                                        '16/11' => '16/11',
                                        '17/11' => '17/11',
                                        '18/11' => '18/11',
                                        '19/11' => '19/11',
                                        '20/11' => '20/11',
                                        '21/11' => '21/11',
                                        '22/11' => '22/11',
                                        '23/11' => '23/11',
                                        '24/11' => '24/11',
                                        '25/11' => '25/11',
                                        '26/11' => '26/11',
                                        '27/11' => '27/11',
                                        '28/11' => '28/11',
                                        '29/11' => '29/11',
                                        '30/11' => '30/11',
                                        '01/12' => '01/12',
                                        '02/12' => '02/12',
                                        '03/12' => '03/12',
                                        '04/12' => '04/12',
                                        '05/12' => '05/12',
                                        '06/12' => '06/12',
                                        '07/12' => '07/12',
                                        '08/12' => '08/12',
                                        '09/12' => '09/12',
                                        '10/12' => '10/12',
                                        '11/12' => '11/12',
                                        '12/12' => '12/12',
                                        '13/12' => '13/12',
                                        '14/12' => '14/12',
                                        '15/12' => '15/12',
                                        '16/12' => '16/12',
                                        '17/12' => '17/12',
                                        '18/12' => '18/12',
                                        '19/12' => '19/12',
                                        '20/12' => '20/12',
                                        '21/12' => '21/12',
                                        '22/12' => '22/12',
                                        '23/12' => '23/12',
                                        '24/12' => '24/12',
                                        '25/12' => '25/12',
                                        '26/12' => '26/12',
                                        '27/12' => '27/12',
                                        '28/12' => '28/12',
                                        '29/12' => '29/12',
                                        '30/12' => '30/12',
                                        '31/12' => '31/12',), 'wc-yapay_intermediador-bs'),
                'class'             => __('wc-enhanced-select', 'wc-yapay_intermediador-bs'),
                'css'               => __('width: 400px;', 'wc-yapay_intermediador-bs'),
      ),
            'dias_vencimento_boleto' => array(
                'title'     => __( 'Dias para vencimento do boleto', 'wc-yapay_intermediador-bs' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Insira quantidade de dias para vencimento do boleto.', 'wc-yapay_intermediador-bs' ),
                'required' => 'required',
                'default'   => '1',
            ),
            'environment' => array(
                'title'     => __( 'Sandbox', 'wc-yapay_intermediador-bs' ),
                'label'     => __( 'Ativar Sandbox', 'wc-yapay_intermediador-bs' ),
                'type'      => 'checkbox',
                'description' => __( 'Ativar / Desativar o ambiente de teste (sandbox)', 'wc-yapay_intermediador-bs' ),
                'default'   => 'no',
            ),
            'token_account' => array(
                'title'     => __( 'Token da Conta', 'wc-yapay_intermediador-bs' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Token de Integração utilizado para identificação da loja.', 'wc-yapay_intermediador-bs' ),
            ),
            'prefixo' => array(
                'title'     => __( 'Prefixo do Pedido', 'wc-yapay_intermediador-bs' ),
                'type'      => 'text',
                'desc_tip'  => __( 'Prefixo do pedido enviado para o Yapay Intermediador.', 'wc-yapay_intermediador-bs' ),
            ),
            'consumer_key' => array(
                'type'      => 'hidden'
            ),
            'consumer_secret' => array(
                'type'      => 'hidden'
            )
        );       
    }
    
    public function payment_fields() {
        global $woocommerce;
        
        if ( $description = $this->get_description() ) {
                echo wpautop( wptexturize( $description ) );
        }
        
        wc_get_template( $this->id.'_form.php', array(
                'url_images'           => plugins_url( 'woo-yapay/assets/images/', plugin_dir_path( __FILE__ ) )
        ), 'woocommerce/'.$this->id.'/', plugin_dir_path( __FILE__ ) . 'templates/' );
    }
    
    public function add_error( $messages ) {
        global $woocommerce;

        // Remove duplicate messages.
        $messages = array_unique( $messages );

        if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
            foreach ( $messages as $message ) {
                wc_add_notice( $message, 'error' );
            }
        } else {
            foreach ( $messages as $message ) {
                $woocommerce->add_error( $message );
            }
        }
    }
    
    /**
    * Get WooCommerce return URL.
    *
    * @return string
    */
    public function get_wc_request_url($order_id) {
        return get_site_url()."/?wc_yapay_intermediador_notification=1&order_id=$order_id";
    }
        
    public function process_payment( $order_id ) {
        global $woocommerce;
        
        include_once("includes/class-wc-yapay_intermediador-request.php");

        $order = new WC_Order( $order_id );


        $params["token_account"] = $this->get_option("token_account");
		$params['transaction[free]']= "WOOCOMMERCE_INTERMEDIADOR_v0.6.0";
        $params["customer[name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
        $params["customer[cpf]"] = $_POST["billing_cpf"];


        if ($_POST["billing_persontype"] == 2) {
            $params["customer[trade_name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
            $params["customer[company_name]"] = $_POST["billing_company"];
            $params["customer[cnpj]"] = $_POST["billing_cnpj"];

            if ($_POST["yapay_cpfB"] == "") {
                $params["customer[cpf]"] = $_POST["billing_cpf"];
            }
            else {
                $params["customer[cpf]"] = $_POST["yapay_cpfB"];
            } 
        } else {
            if (($_POST["billing_persontype"] == NULL) AND ($_POST["billing_cpf"] == NULL) ) {
                $params["customer[cpf]"] = $_POST["yapay_cpfB"];
                $params["customer[trade_name]"] = $_POST["billing_first_name"] . " " . $_POST["billing_last_name"];
                $params["customer[company_name]"] = $_POST["billing_company"];
                $params["customer[cnpj]"] = $_POST["billing_cnpj"];
            } 
        }

        $params["customer[inscricao_municipal]"] = "";
        $params["customer[email]"] = $_POST["billing_email"];
        $params["customer[contacts][][type_contact]"] = "H";
        $params["customer[contacts][][number_contact]"] = $_POST["billing_phone"];
        
        $params["customer[addresses][0][type_address]"] = "B";
        $params["customer[addresses][0][postal_code]"] = $_POST["billing_postcode"];
        $params["customer[addresses][0][street]"] = $_POST["billing_address_1"];
        $params["customer[addresses][0][number]"] = $_POST["billing_number"];
        $params["customer[addresses][0][neighborhood]"] = $_POST["billing_neighborhood"];
        $params["customer[addresses][0][completion]"] = $_POST["billing_address_2"];
        $params["customer[addresses][0][city]"] = $_POST["billing_city"];
        $params["customer[addresses][0][state]"] = $_POST["billing_state"];
        
        if (isset($_POST["ship_to_different_address"])){
            if ($_POST["ship_to_different_address"]){
                $params["customer[addresses][1][type_address]"] = "D";
                $params["customer[addresses][1][postal_code]"] = $_POST["shipping_postcode"];
                $params["customer[addresses][1][street]"] = $_POST["shipping_address_1"];
                $params["customer[addresses][1][number]"] = $_POST["shipping_number"];
                $params["customer[addresses][1][neighborhood]"] = $_POST["shipping_neighborhood"];
                $params["customer[addresses][1][completion]"] = $_POST["shipping_address_2"];
                $params["customer[addresses][1][city]"] = $_POST["shipping_city"];
                $params["customer[addresses][1][state]"] = $_POST["shipping_state"];
            }else{
                $params["customer[addresses][1][type_address]"] = "D";
                $params["customer[addresses][1][postal_code]"] = $_POST["billing_postcode"];
                $params["customer[addresses][1][street]"] = $_POST["billing_address_1"];
                $params["customer[addresses][1][number]"] = $_POST["billing_number"];
                $params["customer[addresses][1][neighborhood]"] = $_POST["billing_neighborhood"];
                $params["customer[addresses][1][completion]"] = $_POST["billing_address_2"];
                $params["customer[addresses][1][city]"] = $_POST["billing_city"];
                $params["customer[addresses][1][state]"] = $_POST["billing_state"];
            }
        }else{
            $params["customer[addresses][1][type_address]"] = "D";
            $params["customer[addresses][1][postal_code]"] = $_POST["billing_postcode"];
            $params["customer[addresses][1][street]"] = $_POST["billing_address_1"];
            $params["customer[addresses][1][number]"] = $_POST["billing_number"];
            $params["customer[addresses][1][neighborhood]"] = $_POST["billing_neighborhood"];
            $params["customer[addresses][1][completion]"] = $_POST["billing_address_2"];
            $params["customer[addresses][1][city]"] = $_POST["billing_city"];
            $params["customer[addresses][1][state]"] = $_POST["billing_state"];
        }
        
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
          $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }

        $params["transaction[customer_ip]"] = $_SERVER['REMOTE_ADDR'];        
        
        $params["transaction[order_number]"] = $this->get_option("prefixo").$order_id;
        $shippingData = $order->get_shipping_methods();
        $shipping_type = "";
        foreach ($shippingData as $shipping){            
            $shipping_type .= $shipping["name"];
            if(count($shippingData) > 1){
                $shipping_type .= " / ";
            }
        }
        
        if($shipping_type != ""){
            $params["transaction[shipping_type]"] = $shipping_type;
            $params["transaction[shipping_price]"] = $order->get_shipping_total();
        }
         // OBTER DESCONTOS
        $the_order = wc_get_order( $order_id );
        $fee_total = 0;
        // BUSCAR DESCONTO APLICADOS NO PEDIDO
        foreach( $the_order->get_items('fee') as $item_id => $item_fee ){

        // NOME DO DESCONTO
         $fee_name = $item_fee->get_name();

        // VALOR TOTAL DO DESCONTO
        $fee_total = $fee_total + $item_fee->get_total();

        // VALOR TOTAL DA TAXA COM DESCONTO
        $fee_total_tax = $item_fee->get_total_tax();
        }
        $fee_total = abs($fee_total);
        $fee_total = $fee_total + $the_order->get_total_discount();
        $params["transaction[price_discount]"] = $fee_total;

        //$params["transaction[price_discount]"] = $order->discount_total;
        $params["transaction[url_notification]"] = $this->get_wc_request_url($order_id);
        $params["transaction[available_payment_methods]"] = "6";
        
        if ( 0 < sizeof( $order->get_items() ) ) {
            $i = 0;
            foreach ($order->get_items() as $product) {
                $params["transaction_product[$i][code]"] = $product["product_id"];
                $params["transaction_product[$i][description]"] = $product['name'];
                $params["transaction_product[$i][price_unit]"] = $order->get_item_subtotal( $product, false ) ;
                $params["transaction_product[$i][quantity]"] = $product['qty'];
                $i++;
            }
        }
        
        $params["payment[payment_method_id]"] = "6";

        //////////////////            CALCULAR DATA VENCIMENTO DO BOLETO         //////////////////////////
        $dias_vencimeto = $this->get_option("dias_vencimento_boleto");

        date_default_timezone_set('America/Sao_Paulo');
        $data_vencimento = date( 'd/m/Y', time() + ( $dias_vencimeto * 86400 ) );
        $datetime = DateTime::createFromFormat('d/m/Y', $data_vencimento);

        //$feriados = array( '01/01', '24/02', '25/02', '26/02', '21/04', '01/05', '21/04', '15/11', '20/11', '25/12', '31/12' );
        $feriados = $this->get_option( "feriados" );
		while(in_array($datetime->format('d/m'), $feriados)){
			$datetime->modify('+1 day');
		}
		/*
        if(in_array($datetime->format('d/m'), $feriados) ){
            $datetime->modify('+1 day');
        }
        if(in_array($datetime->format('d/m'), $feriados) ){
            $datetime->modify('+1 day');
        }
        if(in_array($datetime->format('d/m'), $feriados) ){
            $datetime->modify('+1 day');
        }
		*/

        $dia_semana = $datetime->format("l");

        if($dia_semana == 'Saturday' ){
            $datetime->modify('+2 days');
        }else{
            if($dia_semana == 'Sunday'){
                $datetime->modify('+1 day');
            }
        }
        $params["payment[billet_date_expiration]"] =  $datetime->format('d/m/Y');
        $data_vencimento_interno = $datetime->format('Y-m-d');
        update_post_meta( $order_id, 'data_vencimento_boleto', $data_vencimento_interno );
        //////////////////            CALCULAR DATA VENCIMENTO DO BOLETO         //////////////////////////

        $params["payment[split]"] = "1";

        $tcRequest = new WC_Yapay_Intermediador_Request();
                
        $tcResponse = $tcRequest->requestData("v2/transactions/pay_complete",$params,$this->get_option("environment"),false);
        
        if($tcResponse->message_response->message == "success"){
            // Remove cart.  
            include_once("includes/class-wc-yapay_intermediador-transactions.php");
            
            $transactionData = new WC_Yapay_Intermediador_Transactions();
            
            $transactionParams["order_id"] = (string)$tcResponse->data_response->transaction->order_number;
            $transactionParams["transaction_id"] = (int)$tcResponse->data_response->transaction->transaction_id;
            $transactionParams["split_number"] = (int)$tcResponse->data_response->transaction->order_number;
            $transactionParams["payment_method"] = (int)$tcResponse->data_response->transaction->payment->payment_method_id;
            $transactionParams["token_transaction"] = (string)$tcResponse->data_response->transaction->token_transaction;
            $transactionParams["url_payment"] = (string)$tcResponse->data_response->transaction->payment->url_payment;
            $transactionParams["typeful_line"] = (string)$tcResponse->data_response->transaction->payment->linha_digitavel;
            
            $transactionData->addTransaction($transactionParams);

            if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
                WC()->cart->empty_cart();
            } else {
                $woocommerce->cart->empty_cart();
            }
            if(!isset($use_shipping)){
                $use_shipping = isset($use_shipping);
            }

            if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url( $order )
                    // 'redirect' => add_query_arg( array( 'use_shipping' => $use_shipping ), $order->get_checkout_payment_url( true ) )
                );
            } else {
                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url( $order )
                    // 'redirect' => add_query_arg( array( 'order' => $order->id, 'key' => $order->order_key, 'use_shipping' => $use_shipping ), get_permalink( woocommerce_get_page_id( 'pay' ) ) )
                );
            }

        }else{
            $errors = array();
            if(isset($tcResponse->error_response->general_errors)){
                foreach ($tcResponse->error_response->general_errors->general_error as $error){
                    $errors[] = "<strong>Código:</strong> ".$error->code ." | <strong>Mensagem:</strong> ".$error->message;
                }
            }else if(isset($tcResponse->error_response->validation_errors)){
                foreach ($tcResponse->error_response->validation_errors->validation_error as $error){
                    $errors[] = "<strong>Mensagem:</strong> ".$error->message_complete;
                }
            }else{
                $errors[] = "<strong>Código:</strong> 9999 | <strong>Mensagem:</strong> Não foi possível finalizar o pedido. Tente novamente mais tarde!";
            }
            $this->add_error($errors);
        }
        
    }
    
    public function validate_fields() { 
        
        return true; 
    }
    
    public function receipt_page( $order_id ) {
        global $woocommerce;

        $order        = new WC_Order( $order_id );
        $request_data = $_POST;
        
        include_once("includes/class-wc-yapay_intermediador-transactions.php");
            
        $transactionData = new WC_Yapay_Intermediador_Transactions();
        
        $tcTransaction = $transactionData->getTransactionByOrderId($this->get_option("prefixo").$order_id);

        $html = "";
        $html .= "<ul class='order_details'>";
        $html .= "<li>";
        $html .= "Número da Transação: <strong>{$tcTransaction->transaction_id}</strong>";
        $html .= "</li>";
        $html .= "<li>";
        $html .= "<a href='{$tcTransaction->url_payment}' target='_blank' class='button'>Imprimir Boleto</a>";
        $html .= "</li>";
        $html .= "<li>";
        $html .= "Linha Digitável do Boleto: <strong>{$tcTransaction->typeful_line}</strong>";
        $html .= "</li>";
        $html .= "</ul>";
        
        echo $html;


        if($order->get_status() == "pending"){
            $order->update_status( 'on-hold', 'Pedido registrado no Yapay Intermediador. Transação: '.$tcTransaction->transaction_id );
        }
        update_post_meta( $order_id, 'linha_digitavel', preg_replace( '([^0-9])', '', $tcTransaction->typeful_line ) );
        update_post_meta( $order_id, 'link_boleto', $tcTransaction->url_payment );
        update_post_meta( $order_id, 'transacao', $tcTransaction->transaction_id );

    }
}
endif;
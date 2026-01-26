<?php

class CHBSPaymentStripe
{
    public $apiVersion;
    public $paymentMethod;
    public $event;

    function __construct()
    {
        $this->apiVersion = '2020-08-27';
        
        $this->paymentMethod = array
        (
            'alipay'       => array(__('Alipay', 'chauffeur-booking-system')),
            'card'         => array(__('Cards', 'chauffeur-booking-system')),          
            'ideal'        => array(__('iDEAL', 'chauffeur-booking-system')),
            'fpx'          => array(__('FPX', 'chauffeur-booking-system')),
            'bacs_debit'   => array(__('Bacs Direct Debit', 'chauffeur-booking-system')),
            'bancontact'   => array(__('Bancontact', 'chauffeur-booking-system')),
            'giropay'      => array(__('Giropay', 'chauffeur-booking-system')),
            'p24'          => array(__('Przelewy24', 'chauffeur-booking-system')),
            'eps'          => array(__('EPS', 'chauffeur-booking-system')),
            'sofort'       => array(__('Sofort', 'chauffeur-booking-system')),
            'sepa_debit'   => array(__('SEPA Direct Debit', 'chauffeur-booking-system'))
        );
        
        $this->event = array
        (
            'payment_intent.canceled',
            'payment_intent.created',
            'payment_intent.payment_failed',
            'payment_intent.processing',
            'payment_intent.requires_action',
            'payment_intent.succeeded',
            'payment_method.attached'
        );
        
        asort($this->paymentMethod);
    }

    function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    function isPaymentMethod($paymentMethod)
    {
        return array_key_exists($paymentMethod, $this->paymentMethod);
    }

    function getWebhookEndpointUrlAdress()
    {
        $address = add_query_arg('action', 'payment_stripe', home_url() . '/');
        return $address;
    }

    function createWebhookEndpoint($bookingForm)
    {
        $StripeClient = new \Stripe\StripeClient([
            'api_key'        => $bookingForm['meta']['payment_stripe_api_key_secret'],
            'stripe_version' => $this->apiVersion
        ]);
        
        $webhookEndpoint = $StripeClient->webhookEndpoints->create([
            'url'            => $this->getWebhookEndpointUrlAdress(),
            'enabled_events' => $this->event
        ]);      
        
        CHBSOption::updateOption(array('payment_stripe_webhook_endpoint_id' => $webhookEndpoint->id));
    }

    function updateWebhookEndpoint($bookingForm, $webhookEndpointId)
    {
        $StripeClient = new \Stripe\StripeClient([
            'api_key'        => $bookingForm['meta']['payment_stripe_api_key_secret'],
            'stripe_version' => $this->apiVersion
        ]);
        
        $StripeClient->webhookEndpoints->update($webhookEndpointId, [
            'url' => $this->getWebhookEndpointUrlAdress()
        ]);
    }

    function createSession($booking, $bookingBilling, $bookingForm)
    {
        try
        {
            \Stripe\Stripe::setApiVersion($this->apiVersion);
            
            $Validation = new CHBSValidation();

            $currentURLAddress = home_url();

            /***/

            \Stripe\Stripe::setApiKey($bookingForm['meta']['payment_stripe_api_key_secret']);

            /***/

            $webhookEndpointId = CHBSOption::getOption('payment_stripe_webhook_endpoint_id');

            if ($Validation->isEmpty($webhookEndpointId)) {
                $this->createWebhookEndpoint($bookingForm);
            } else {
                try {
                    $this->updateWebhookEndpoint($bookingForm, $webhookEndpointId);
                } catch (Exception $ex) {
                    $this->createWebhookEndpoint($bookingForm);
                }
            }

            /***/

            $productId = $bookingForm['meta']['payment_stripe_product_id'];

            if ($Validation->isEmpty($productId))
            {
                $product = \Stripe\Product::create([
                    'name' => sprintf(__('Chauffeur services - %s', 'chauffeur-booking-system'), $booking['post']->post_title)
                ]);      

                $productId = $product->id;

                CHBSPostMeta::updatePostMeta($bookingForm['post']->ID, 'payment_stripe_product_id', $productId);
            }

            /***/

            $price = \Stripe\Price::create([
                'product'     => $productId,
                'unit_amount' => $bookingBilling['summary']['pay'] * 100,
                'currency'    => $booking['meta']['currency_id'],
            ]);

            /***/

            if ($Validation->isEmpty($bookingForm['meta']['payment_stripe_success_url_address'])) {
                $bookingForm['meta']['payment_stripe_success_url_address'] = $currentURLAddress;
            }
            if ($Validation->isEmpty($bookingForm['meta']['payment_stripe_cancel_url_address'])) {
                $bookingForm['meta']['payment_stripe_cancel_url_address'] = $currentURLAddress;
            }

            $session = \Stripe\Checkout\Session::create([
                'mode'       => 'payment',
                'line_items' => [
                    [
                        'price'    => $price->id,
                        'quantity' => 1
                    ]
                ],
                'success_url' => $bookingForm['meta']['payment_stripe_success_url_address'],
                'cancel_url'  => $bookingForm['meta']['payment_stripe_cancel_url_address']
            ]);

            CHBSPostMeta::updatePostMeta($booking['post']->ID, 'payment_stripe_intent_id', $session->payment_intent);

            return $session->id;
        }
        catch (Exception $ex) 
        {
            $LogManager = new CHBSLogManager();
            $LogManager->add('stripe', 1, $ex->__toString()); 
            return false;
        }
    }

    // Function to add client in Fakturownia
    public function addClient($client_data)
    {
        $api_token = '5HCFkIztd99lIyNWnSJ';
        $endpoint = 'https://ksiegowosc-vipmartour.fakturownia.pl/clients.json';

        $client_payload = [
            'api_token' => $api_token,
            'client'    => [
                'name'       => $client_data['name'],
                'tax_no'     => $client_data['tax_no'],
                'bank'       => '',
                'bank_account' => '',
                'city'       => $client_data['city'],
                'country'    => $client_data['country'],
                'email'      => $client_data['email'],
                'person'     => $client_data['name'],
                'post_code'  => $client_data['postal_code'],
                'phone'      => $client_data['phone'],
                'street'     => $client_data['address']
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($client_payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // Function to create invoice in Fakturownia
    public function createInvoice($data, $client_id)
    {
        $api_token = '5HCFkIztd99lIyNWnSJ';
        $endpoint  = 'https://ksiegowosc-vipmartour.fakturownia.pl/invoices.json';

        $LogManager = new CHBSLogManager();
        $LogManager->add('stripe', 2, 'Entered createInvoice function.');

        // Get passenger counts
        $passenger_adult_number    = isset($data['meta']['passenger_adult_number']) ? (int)$data['meta']['passenger_adult_number'] : 0;
        $passenger_children_number = isset($data['meta']['passenger_children_number']) ? (int)$data['meta']['passenger_children_number'] : 0;
        $total_passengers          = $passenger_adult_number + $passenger_children_number;

        // Get prices per passenger
        $price_passenger_adult_value    = isset($data['meta']['price_passenger_adult_value']) ? (float)$data['meta']['price_passenger_adult_value'] : 0;
        $price_passenger_children_value = isset($data['meta']['price_passenger_children_value']) ? (float)$data['meta']['price_passenger_children_value'] : 0;

        // Calculate total price before discount
        $total_price_before_discount = ($price_passenger_adult_value * $passenger_adult_number) + ($price_passenger_children_value * $passenger_children_number);

        // Get discount percentage
        $discount_percentage = isset($data['meta']['coupon_discount_percentage']) ? (float)$data['meta']['coupon_discount_percentage'] : 0;

        // Calculate discount multiplier
        $discount_multiplier = 1 - ($discount_percentage / 100);

        // Calculate total price after discount
        $total_price_after_discount = $total_price_before_discount * $discount_multiplier;

        // Set foreign zone price per passenger (fixed price)
        $foreign_price_per_passenger = 70; // Adjust this value as needed
        $foreign_price = $foreign_price_per_passenger * $total_passengers;

        // Calculate domestic price
        $domestic_price = $total_price_after_discount - $foreign_price;

        // Ensure prices are valid
        if ($domestic_price < 0) {
            $domestic_price = 0;
        }

        // Round prices
        $domestic_price            = round($domestic_price, 2);
        $foreign_price             = round($foreign_price, 2);
        $total_price_after_discount = round($total_price_after_discount, 2);

        // Log prices
        $log_file    = __DIR__ . '/invoice_data_log.txt';
        $log_content = "Total Price Before Discount: $total_price_before_discount\nTotal Price After Discount: $total_price_after_discount\nDomestic Price: $domestic_price\nForeign Price: $foreign_price\n";
        try {
            $LogManager->add('stripe', 2, 'About to write to log files.');
            file_put_contents($log_file, $log_content, FILE_APPEND);
            $LogManager->add('stripe', 2, 'Successfully wrote to log files.');
        } catch (Exception $e) {
            $LogManager->add('stripe', 2, "Error writing to log files: " . $e->getMessage());
        }

        $issue_date  = date('Y-m-d');
        $payment_to  = date('Y-m-d', strtotime('+7 days'));

        $invoice_data = [
            'api_token' => $api_token,
            'invoice'   => [
                'kind'        => 'receipt',
                'number'      => null,
                'sell_date'   => $issue_date,
                'issue_date'  => $issue_date,
                'payment_to'  => $payment_to,
                'buyer_name'  => $data['meta']['client_contact_detail_first_name'] . ' ' . $data['meta']['client_contact_detail_last_name'],
                'buyer_tax_no'=> $data['meta']['client_billing_detail_tax_number'],
                'positions'   => [
                    [
                        'name'              => 'Przewóz osób strefa krajowa',
                        'tax'               => 8,
                        'total_price_gross' => $domestic_price,
                        'quantity'          => $total_passengers > 0 ? $total_passengers : 1,
                    ],
                    [
                        'name'              => 'Przewóz osób strefa zagraniczna',
                        'tax'               => 0,
                        'total_price_gross' => $foreign_price,
                        'quantity'          => $total_passengers,
                    ]
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($invoice_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // Function to print fiscal receipt
    public function printFiscalReceipt($invoice_id)
    {
        $api_token = '5HCFkIztd99lIyNWnSJ';
        $endpoint = 'https://ksiegowosc-vipmartour.fakturownia.pl/invoices/fiscal_print';
        $fiskator_name = 'ELZAB ZETA online/MD09 - EBI 01144518';

        $params = [
            'api_token'      => $api_token,
            'invoice_ids[]'  => $invoice_id,
            'fiskator_name'  => $fiskator_name
        ];

        $url = $endpoint . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    function receivePayment()
    {
        $LogManager = new CHBSLogManager();
        
        if (!array_key_exists('action', $_REQUEST)) return false;
        
        if ($_REQUEST['action'] == 'payment_stripe')
        {
            $LogManager->add('stripe', 2, __('[1] Receiving a payment.', 'chauffeur-booking-system'));
            
            global $post;
            
            $event   = null;
            $content = @file_get_contents('php://input');
    
            try 
            {
                $event = \Stripe\Event::constructFrom(json_decode($content, true));
            } 
            catch (\UnexpectedValueException $e) 
            {
                $LogManager->add('stripe', 2, __('[2] Error during parsing data in JSON format.', 'chauffeur-booking-system'));  
                http_response_code(400);
                exit();
            }

            // Check if event has already been processed
            $processed_events = get_option('chbs_processed_stripe_events', []);
            if (in_array($event->id, $processed_events)) {
                $LogManager->add('stripe', 2, sprintf(__('[Duplicate] Event %s has already been processed.', 'chauffeur-booking-system'), $event->id));
                http_response_code(200);
                exit();
            } else {
                $processed_events[] = $event->id;
                update_option('chbs_processed_stripe_events', $processed_events);
            }
    
            if (in_array($event->type, $this->event))
            {
                $LogManager->add('stripe', 2, __('[4] Checking a booking.', 'chauffeur-booking-system'));
    
                $Booking        = new CHBSBooking();
                $BookingForm    = new CHBSBookingForm();             
                $BookingStatus  = new CHBSBookingStatus();
                
                $argument = array
                (
                    'post_type'      => CHBSBooking::getCPTName(),
                    'posts_per_page' => -1,
                    'meta_query'     => array
                    (
                        array
                        (
                            'key'   => PLUGIN_CHBS_CONTEXT . '_payment_stripe_intent_id',
                            'value' => $event->data->object->id
                        )                     
                    )
                );
                
                CHBSHelper::preservePost($post, $bPost);
                
                $query = new WP_Query($argument);
                if ($query !== false) 
                {
                    if ($query->found_posts)
                    {
                        $LogManager->add('stripe', 2, sprintf(__('[6] Booking %s is found.', 'chauffeur-booking-system'), $event->data->object->id));    
                        
                        while ($query->have_posts())
                        {
                            $query->the_post();

                            $meta = CHBSPostMeta::getPostMeta($post);

                            if (!array_key_exists('payment_stripe_data', $meta)) {
                                $meta['payment_stripe_data'] = array();
                            }

                            $meta['payment_stripe_data'][] = $event;

                            CHBSPostMeta::updatePostMeta($post->ID, 'payment_stripe_data', $meta['payment_stripe_data']);

                            $LogManager->add('stripe', 2, __('[7] Updating a booking about transaction details.', 'chauffeur-booking-system'));
                            
                            if ($event->type == 'payment_intent.succeeded')
                            {
                                if (CHBSOption::getOption('booking_status_payment_success') != -1)
                                {
                                    if ($BookingStatus->isBookingStatus(CHBSOption::getOption('booking_status_payment_success')))
                                    {
                                        $LogManager->add('stripe', 2, __('[11] Updating booking status.', 'chauffeur-booking-system'));   
                                        
                                        $bookingOld = $Booking->getBooking($post->ID);

                                        CHBSPostMeta::updatePostMeta($post->ID, 'booking_status_id', CHBSOption::getOption('booking_status_payment_success'));

                                        $bookingNew = $Booking->getBooking($post->ID);
                                        
                                        $emailAdminSend  = false;
                                        $emailClientSend = false;
                                        
                                        $bookingFormDictionary = $BookingForm->getDictionary();
                                        
                                        if (array_key_exists($bookingNew['meta']['booking_form_id'], $bookingFormDictionary))
                                        {
                                            $bookingForm = $bookingFormDictionary[$bookingNew['meta']['booking_form_id']];
                                            
                                            $subject = sprintf(__('New booking "%s" has been received', 'chauffeur-booking-system'), $bookingNew['post']->post_title);
                                            
                                            if (((int)$bookingForm['meta']['email_notification_booking_new_client_enable'] === 1) && ((int)$bookingForm['meta']['email_notification_booking_new_client_payment_success_enable'] === 1))
                                            {
                                                $chbs_logEvent   = 1;
                                                $emailClientSend = true;
                                                $Booking->sendEmail($post->ID, $bookingForm['meta']['booking_new_sender_email_account_id'], 'booking_new_client', array($bookingNew['meta']['client_contact_detail_email_address']), $subject);
                                            }
                                            
                                            if (((int)$bookingForm['meta']['email_notification_booking_new_admin_enable'] === 1) && ((int)$bookingForm['meta']['email_notification_booking_new_admin_payment_success_enable'] === 1))
                                            {
                                                $chbs_logEvent  = 2;
                                                $emailAdminSend = true;
                                                $Booking->sendEmail($post->ID, $bookingForm['meta']['booking_new_sender_email_account_id'], 'booking_new_admin', preg_split('/;/', $bookingForm['meta']['booking_new_recipient_email_address']), $subject);
                                            }
                                        }
                                        
                                        if (!$emailClientSend)
                                        {
                                            $emailSend = false;

                                            $WooCommerce = new CHBSWooCommerce();
                                            $WooCommerce->changeStatus(-1, $post->ID, $emailSend);                                   

                                            if (!$emailSend) {
                                                $Booking->sendEmailBookingChangeStatus($bookingOld, $bookingNew);
                                            }
                                        }
                                        
                                        $GoogleCalendar = new CHBSGoogleCalendar();
                                        $GoogleCalendar->sendBooking($post->ID, false, 'after_booking_status_change');
                                    }
                                    else
                                    {
                                        $LogManager->add('stripe', 2, __('[10] Cannot find a valid booking status.', 'chauffeur-booking-system'));   
                                    }
                                }
                                else
                                {
                                    $LogManager->add('stripe', 2, __('[9] Changing status of the booking after successful payment is off.', 'chauffeur-booking-system'));    
                                }

                                // Integrate with Fakturownia
                                // Prepare client data
                                $client_data = [
                                    'name'        => ($bookingNew['meta']['client_contact_detail_first_name'] ?? '') . ' ' . ($bookingNew['meta']['client_contact_detail_last_name'] ?? ''),
                                    'email'       => $bookingNew['meta']['client_contact_detail_email_address'] ?? '',
                                    'phone'       => $bookingNew['meta']['client_contact_detail_phone_number'] ?? '',
                                    'address'     => ($bookingNew['meta']['client_billing_detail_street_name'] ?? '') . ' ' . ($bookingNew['meta']['client_billing_detail_street_number'] ?? ''),
                                    'city'        => $bookingNew['meta']['client_billing_detail_city'] ?? '',
                                    'postal_code' => $bookingNew['meta']['client_billing_detail_postal_code'] ?? '',
                                    'country'     => $bookingNew['meta']['client_billing_detail_country_code'] ?? '',
                                    'tax_no'      => $bookingNew['meta']['client_billing_detail_tax_number'] ?? ''
                                ];

                                // Log client data
                                $LogManager->add('stripe', 2, "Client data: " . print_r($client_data, true));

                                // Add client to Fakturownia
                                $client_response = $this->addClient($client_data);

                                if (isset($client_response['id'])) {
                                    // Client added successfully
                                    $client_id = $client_response['id'];

                                    // Prepare invoice data
                                    $invoice_data = [
                                        'meta'    => $bookingNew['meta'],
                                        'billing' => [
                                            'summary' => [
                                                'pay' => $bookingNew['billing']['summary']['pay'] ?? 0
                                            ]
                                        ]
                                    ];

                                    // Create invoice
                                    $invoice_response = $this->createInvoice($invoice_data, $client_id);

                                    if (isset($invoice_response['id'])) {
                                        // Invoice created successfully
                                        $invoice_id = $invoice_response['id'];

                                        // Print fiscal receipt
                                        $fiscal_print_response = $this->printFiscalReceipt($invoice_id);

                                        if (isset($fiscal_print_response['success']) && $fiscal_print_response['success'] == true) {
                                            // Success - fiscal receipt printed
                                        } else {
                                            // Error printing fiscal receipt
                                            $LogManager->add('stripe', 2, "Error printing fiscal receipt: " . json_encode($fiscal_print_response));
                                        }
                                    } else {
                                        // Error creating invoice
                                        $LogManager->add('stripe', 2, "Error creating invoice: " . json_encode($invoice_response));
                                    }
                                } else {
                                    // Error adding client
                                    $LogManager->add('stripe', 2, "Error adding client: " . json_encode($client_response));
                                }

                            }
                            else
                            {
                                $LogManager->add('stripe', 2, sprintf(__('[8] Event %s is not supported.', 'chauffeur-booking-system'), $event->type));   
                            }

                            break;
                        }
                    }
                    else
                    {
                        $LogManager->add('stripe', 2, sprintf(__('[5] Booking %s is not found.', 'chauffeur-booking-system'), $event->data->object->id));    
                    }
                }
            
                CHBSHelper::preservePost($post, $bPost, 0);
            }
            else 
            {
                $LogManager->add('stripe', 2, sprintf(__('[3] Event %s is not supported.', 'chauffeur-booking-system'), $event->type));   
            }
        
            http_response_code(200);
            exit();
        }
    }
}

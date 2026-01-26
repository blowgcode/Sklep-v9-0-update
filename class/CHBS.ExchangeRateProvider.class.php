<?php

/******************************************************************************/
/******************************************************************************/

class CHBSExchangeRateProvider
{
    public $provider;

    public function __construct()
    {
        $this->provider = array(
            1 => array(__('Fixer.io', 'chauffeur-booking-system'))
        );
    }

    public function getProvider()
    {
        return ($this->provider);
    }

    public function isProvider($provider)
    {
        return (array_key_exists($provider, $this->provider));
    }

    public function importExchangeRate()
    {
        $post = CHBSHelper::getPostOption();
        $option = CHBSOption::getOptionObject();

        $rate = false;
        $currency = array();
        $response = array('global' => array('error' => 1, 'reload' => 0));

        if ((int)$post['exchange_rate_provider'] === 1) {
            $FixerIo = new CHBSFixerIo();
            $rate = $FixerIo->getRate();
        }

        if ($rate !== false) {
            $Currency = new CHBSCurrency();

            foreach ($Currency->getCurrency() as $index => $value) {
                if (isset($rate->{$index}))
                    $currency[$index] = number_format($rate->{$index}, 2, '.', '');
            }

            if (!is_array($option['currency_exchange_rate']))
                $option['currency_exchange_rate'] = array();

            $option['currency_exchange_rate'] = array_merge($option['currency_exchange_rate'], $currency);

            $response['global']['error'] = 0;
            $response['global']['reload'] = 1;

            $subtitle = __('Exchange rates have been imported. Page will be reloaded...', 'chauffeur-booking-system');

            CHBSOption::updateOption($option);
        } else {
            $response['global']['error'] = 1;
            $subtitle = __('Exchange rates cannot be imported.', 'chauffeur-booking-system');
        }

        $Notice = new CHBSNotice();

        $response['global']['notice'] = $Notice->createHTML(PLUGIN_CHBS_TEMPLATE_PATH . 'admin/notice.php', false, $response['global']['error'], $subtitle);

        echo json_encode($response);
        exit;
    }
}

/******************************************************************************/
/******************************************************************************/
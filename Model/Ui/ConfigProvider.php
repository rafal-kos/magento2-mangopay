<?php

namespace Empisoft\Mangopay\Model\Ui;

use Empisoft\Mangopay\Gateway\Http\Client\ClientMock;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'mangopay';

    /** @var \Magento\Payment\Model\CcConfig */
    protected $config;

    /**
     * @param \Magento\Payment\Model\CcConfig $config
     */
    public function __construct(
        \Magento\Payment\Model\CcConfig $config
    ) {
        $this->config = $config;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        ClientMock::SUCCESS => __('Success'),
                        ClientMock::FAILURE => __('Fraud')
                    ],
                    'availableTypes' => [
                        self::CODE => $this->config->getCcAvailableTypes()
                    ],
                    'months' => [
                        self::CODE => $this->config->getCcMonths()
                    ],
                    'years' => [
                        self::CODE => $this->config->getCcYears()
                    ],
                    'hasVerification' => [
                        self::CODE => $this->config->hasVerification()
                    ],
                    'cvvImageUrl' => [
                        self::CODE => $this->getCvvImageUrl()
                    ]
                ]
            ]
        ];
    }

    /**
     * Retrieve CVV tooltip image url
     *
     * @return string
     */
    protected function getCvvImageUrl()
    {
        return $this->config->getCvvImageUrl();
    }
}

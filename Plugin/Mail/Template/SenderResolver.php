<?php

/**
 * Xtendable_MailAttachment for Magento 2
 *
 * The extension module for \Magento\Framework\Mail
 *
 * @version     1.0.0, last modified Oct 2018
 * @author      Didi Kusnadi<jalapro08@gmail.com>
 *
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 *
 * @copyright   Copyright © 2017 Kemana. All rights reserved.
 * @link        http://www.kemana.com Driving Digital Commerce
 *
 */

namespace Xtendable\MailAttachment\Plugin\Mail\Template;

/**
 * Class SenderResolver
 * fix issue in office365, if from email not registered as valid account in office356
 */

// @codingStandardsIgnoreFile

class SenderResolver
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterResolve(\Magento\Email\Model\Template\SenderResolver $subject, $result)
    {
        $identityAddress = $this->scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $originalFrom = $result['email'];

        $allowedDomain = $this->getDomain($identityAddress);
        $targetDomain = $this->getDomain($result['email']);
        if ($targetDomain != $allowedDomain) {
            $result['email'] = $identityAddress;
            $result['reply_to'] = $originalFrom;
        }

        return $result;
    }

    /**
     * get domain from email address
     * @param  string $emailAddress
     * @return string
     */
    protected function getDomain($emailAddress = null)
    {
        if (empty($emailAddress) || !preg_match('/@/', $emailAddress)) {
            return;
        }

        $string = explode("@", $emailAddress);
        return strtolower($string[1]);
    }
}

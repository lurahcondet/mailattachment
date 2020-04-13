<?php

/**
 * Xtendable_MailAttachment
 *
 * @see    README.md
 * @author Didi Kusnadi<jalapro08@gmail.com>
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
        if(!$this->scopeConfig->getValue(
            'system/smtp/domain_validation',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )){
           return $result; 
        }

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

<?php

/**
 * Xtendable_MailAttachment
 *
 * @see    README.md
 * @author Didi Kusnadi<jalapro08@gmail.com>
 *
 */

namespace Xtendable\MailAttachment\Plugin\Mail\Template;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * add attachment
     * @param string $file
     * @param string $name
     */
    public function addAttachment($file, $name)
    {
        if (!empty($file) && file_exists($file)) {
            $this->message
            ->createAttachment(
                file_get_contents($file),
                \Zend_Mime::TYPE_OCTETSTREAM,
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                basename($name)
            );
        }
        return $this;
    }

    /**
     * Set mail from address
     *
     * @param string|array $from
     * @return $this
     */
    public function setFrom($from)
    {
        $result = $this->_senderResolver->resolve($from);
        $this->message->setFrom($result['email'], $result['name']);
        if (isset($result['reply_to']) && $result['reply_to']) {
            $this->setReplyTo($result['reply_to'], $result['name']);
        }
        return $this;
    }

    /**
     * Set Reply-To Header
     *
     * @param string $email
     * @param string|null $name
     * @return $this
     */
    public function setReplyTo($email, $name = null)
    {
        if ($this->message->getReplyTo()) {
            $this->message->clearReplyTo();
        }
        $this->message->setReplyTo($email, $name);
        return $this;
    }
}

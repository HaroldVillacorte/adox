<?php

namespace TeamContact\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class ContactFormModel implements InputFilterAwareInterface
{

    public $replyTo = NULL;
    public $subject = NULL;
    public $message = NULL;

    /**
     * @var object Zend\InputFilter\InputFilter
     */
    public $inputFilter;

    /**
     * Populate object from post array.
     *
     * @param array $array
     */
    public function populate($array = array())
    {
        $this->replyTo = ($array['replyTo']) ? (string)$array['replyTo'] : '';
        $this->subject = ($array['subject']) ? (string)$array['subject'] : '';
        $this->message = ($array['message']) ? (string)$array['message'] : '';
    }

    public function send()
    {
        $mail = new Message();

        $mail->addTo(getenv('EM_USER'));
        $mail->setFrom(getenv('EM_USER'));
        $mail->addReplyTo($this->replyTo);

        $mail->setSubject($this->subject);
        $mail->setBody($this->message);

        $options = new SmtpOptions(array(
            'host'              => getenv('EM_HOST'),
            'port'              => getenv('EM_PORT'),
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => getenv('EM_USER'),
                'password' => getenv('EM_PASS'),
                'ssl' => getenv('EM_SSL'),
            ),
        ));
        $transport = new SmtpTransport();
        $transport->setOptions($options);
        try
        {
            $transport->send($mail);
            return TRUE;
        }
        catch (\Exception $e)
        {
            return FALSE;
        }

    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter)
        {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'replyTo',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'emailaddress',
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'subject',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name' => 'message',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 2000,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}

<?php namespace Lyubaev\XMLUtil\Validator;

interface ValidatorInterface
{
    public function isValid(\DOMDocument $document);

    public function getMessages();
}
<?php
/**
 * This file is part of the XMLUtil package.
 *
 * @link      https://github.com/Lyubaev/XMLUtil
 * @copyright Copyright (c) 2015 Kirill Lyubaev
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD New
 */

namespace Lyubaev\XMLUtil\Validator;

/**
 * Interface ValidatorInterface
 *
 * @package XMLUtil
 */
interface ValidatorInterface
{
    public function isValid(\DOMDocument $document);

    public function getMessages();
}
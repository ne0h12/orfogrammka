<?php
/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */

namespace Orfogrammka\Exception;


use Kayex\HttpCodes;

class UnauthorizedResponse extends \Exception
{
    protected $message = 'Authorization is failed. Check your email and password.';
    protected $code = HttpCodes::HTTP_UNAUTHORIZED;

}
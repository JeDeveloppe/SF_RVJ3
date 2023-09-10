<?php

namespace App\Service;

class DocumentService
{
    public function __construct(
        ){
    }

    public function quoteNumberGenerator($numberWithoutPrefix)
    {
        return $_ENV['QUOTE_TAG'].$numberWithoutPrefix;
    }

    public function billingNumberGenerator($numberWithoutPrefix)
    {
        return $_ENV['BILLING_TAG'].$numberWithoutPrefix;
    }
}
<?php

namespace Safe4Work\Domain\Queue\Workers;

enum Workers: string
{
    case EMAILS = 'email';
    case HTTPREQUESTS = 'httprequests';

    case DEFAULT = 'default';
}

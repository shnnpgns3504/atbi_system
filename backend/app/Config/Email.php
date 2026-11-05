<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    // YOUR GMAIL ADDRESS
    public string $fromEmail  = 'atbiintertrade@gmail.com';
    public string $fromName   = 'ATBI Intertrade System';
    public string $recipients = '';

    public string $userAgent = 'CodeIgniter';

    // CHANGE THIS TO SMTP
    public string $protocol = 'smtp';

    public string $mailPath = '/usr/sbin/sendmail';

    // GMAIL SMTP SETTINGS
    public string $SMTPHost = 'smtp.gmail.com';
    public string $SMTPAuthMethod = 'login';
    public string $SMTPUser = 'atbiintertrade@gmail.com';
    
    // PASTE YOUR 16-LETTER APP PASSWORD HERE (no spaces)
    public string $SMTPPass = 'vdqxzbubxdnkbqjj'; 

    public int $SMTPPort = 587;
    public int $SMTPTimeout = 30;
    public bool $SMTPKeepAlive = false;
    public string $SMTPCrypto = 'tls';

    public bool $wordWrap = true;
    public int $wrapChars = 76;
    
    // CHANGE TO HTML SO THE EMAIL LOOKS NICE
    public string $mailType = 'html';

    public string $charset = 'UTF-8';
    public bool $validate = false;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public bool $DSN = false;
}
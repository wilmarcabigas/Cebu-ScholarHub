<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'jamestrocio842@gmail.com';
    public string $fromName   = 'ScholarHub System';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost = 'smtp.gmail.com';

    /**
     * SMTP Username
     */
    public string $SMTPUser = 'jamestrocio842@gmail.com';

    /**
     * SMTP Password (Gmail App Password)
     */
    public string $SMTPPass = 'mnrw vxns kplo gtwl';

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587;

    /**
     * SMTP Timeout
     */
    public int $SMTPTimeout = 4;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption
     */
    public string $SMTPCrypto = 'tls';

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail
     */
    public string $mailType = 'html';

    /**
     * Character set
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = false;

    /**
     * Email Priority
     */
    public int $priority = 3;

    /**
     * Newline character
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;
}
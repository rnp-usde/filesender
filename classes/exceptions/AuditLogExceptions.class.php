<?php

/*
 * FileSender www.filesender.org
 *
 * Copyright (c) 2009-2012, AARNet, Belnet, HEAnet, SURFnet, UNINETT
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * *    Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 * *    Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in the
 *     documentation and/or other materials provided with the distribution.
 * *    Neither the name of AARNet, Belnet, HEAnet, SURFnet and UNINETT nor the
 *     names of its contributors may be used to endorse or promote products
 *     derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/*
 * Unknown AuditLog exception
 */
class AuditLogNotFoundException extends DetailedException
{
    /**
     * Constructor
     *
     * @param string $selector column used to select user
     */
    public function __construct($selector)
    {
        parent::__construct(
            'auditlog_not_found', // Message to give to the user
            ['selector' => $selector] // Real message to log
        );
    }
}

/**
 * Unknown event exception
 */
class AuditLogUnknownEventException extends DetailedException
{
    /**
     * Constructor
     *
     * @param string $event
     */
    public function __construct($event)
    {
        parent::__construct(
            'auditlog_unknown_event', // Message to give to the user
            ['event' => $event] // Real message to log
        );
    }
}

/**
 * Log not enabled exception
 */
class AuditLogNotEnabledException extends DetailedException
{

    /**
     * Constructor
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct(
                'auditlog_not_enabled', // Message to give to the user
                ['message' => $message] // Real message to log
        );
    }
}

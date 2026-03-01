<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>TMS Error Alert</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td {
            font-family: Arial, Helvetica, sans-serif !important;
        }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f4;">
    <tr>
        <td style="padding: 20px 0;">
            <!-- Main Container -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="95%"
                   style="max-width: 1200px; margin: 0 auto; background-color: #ffffff; border-radius: 8px;">
                <!-- Header -->
                <tr>
                    <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); background-color: #667eea; padding: 25px 30px; text-align: center; border-radius: 8px 8px 0 0;">
                        <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold;">🚨 TMS Error
                            Alert</h1>
                        <p style="margin: 5px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">A new error has
                            been logged in the system</p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding: 30px;">
                        <!-- Alert Box -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                               style="background-color: #fee; border-left: 4px solid #c00; margin-bottom: 25px;">
                            <tr>
                                <td style="padding: 15px 20px;">
                                    <h2 style="margin: 0 0 8px 0; color: #c00; font-size: 16px; font-weight: bold;">
                                        Error Message</h2>
                                    <p style="margin: 0; font-size: 14px; color: #333;">{{ $errorData['message'] }}</p>
                                </td>
                            </tr>
                        </table>

                        <!-- Info Cards - Row 1 -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                               style="margin-bottom: 15px;">
                            <tr>
                                <!-- Log ID -->
                                <td width="23%" style="padding: 0 7px 0 0; vertical-align: top;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                           style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
                                        <tr>
                                            <td style="padding: 15px;">
                                                <div
                                                    style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                                    LOG ID
                                                </div>
                                                <div style="font-size: 14px; color: #2d3748; font-weight: 500;"><strong
                                                        style="color: #c00;">#{{ $errorData['log_id'] }}</strong></div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- Error Level -->
                                <td width="23%" style="padding: 0 7px; vertical-align: top;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                           style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
                                        <tr>
                                            <td style="padding: 15px;">
                                                <div
                                                    style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                                    ERROR LEVEL
                                                </div>
                                                <div style="font-size: 14px; color: #2d3748; font-weight: 500;"><strong
                                                        style="color: #c00;">{{ $errorData['level_name'] }}</strong>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- Timestamp -->
                                <td width="23%" style="padding: 0 7px; vertical-align: top;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                           style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
                                        <tr>
                                            <td style="padding: 15px;">
                                                <div
                                                    style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                                    TIMESTAMP
                                                </div>
                                                <div
                                                    style="font-size: 14px; color: #2d3748; font-weight: 500;">{{ $errorData['timestamp'] }}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- IP Address -->
                                <td width="23%" style="padding: 0 0 0 7px; vertical-align: top;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                           style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
                                        <tr>
                                            <td style="padding: 15px;">
                                                <div
                                                    style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                                    IP ADDRESS
                                                </div>
                                                <div
                                                    style="font-size: 14px; color: #2d3748; font-weight: 500;">{{ $errorData['ip_address'] ?? 'N/A' }}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Info Cards - Row 2 -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                               style="margin-bottom: 15px;">
                            <tr>
                                <!-- HTTP Method -->
                                <td width="48%" style="padding: 0 7px 0 0; vertical-align: top;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                           style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
                                        <tr>
                                            <td style="padding: 15px;">
                                                <div
                                                    style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                                    HTTP METHOD
                                                </div>
                                                <div
                                                    style="font-size: 14px; color: #2d3748; font-weight: 500;">{{ $errorData['http_method'] ?? 'N/A' }}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- Log Hash -->
                                <td width="48%" style="padding: 0 0 0 7px; vertical-align: top;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                           style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
                                        <tr>
                                            <td style="padding: 15px;">
                                                <div
                                                    style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                                    LOG HASH
                                                </div>
                                                <div
                                                    style="font-size: 11px; color: #2d3748; font-weight: 500; font-family: monospace;">{{ $errorData['log_hash'] ?? 'N/A' }}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- URL -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                               style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; margin-bottom: 15px;">
                            <tr>
                                <td style="padding: 15px;">
                                    <div
                                        style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                        URL
                                    </div>
                                    <div
                                        style="font-size: 12px; color: #2d3748; font-weight: 500; word-break: break-all;">{{ $errorData['url'] ?? 'N/A' }}</div>
                                </td>
                            </tr>
                        </table>

                        <!-- Referer URL -->
                        @if(data_get($errorData, 'referer_url'))
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                   style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; margin-bottom: 15px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <div
                                            style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                            REFERER URL
                                        </div>
                                        <div
                                            style="font-size: 12px; color: #2d3748; font-weight: 500; word-break: break-all;">{{ $errorData['referer_url'] }}</div>
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <!-- User Information -->
                        @if(isset($errorData['user_name']))
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                   style="margin-bottom: 15px;">
                                <tr>
                                    <!-- User Name -->
                                    <td width="31%" style="padding: 0 7px 0 0; vertical-align: top;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                               width="100%"
                                               style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
                                            <tr>
                                                <td style="padding: 15px;">
                                                    <div
                                                        style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                                        USER
                                                    </div>
                                                    <div
                                                        style="font-size: 14px; color: #2d3748; font-weight: 500;">{{ $errorData['user_name'] }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <!-- User Email -->
                                    <td width="31%" style="padding: 0 7px; vertical-align: top;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                               width="100%"
                                               style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
                                            <tr>
                                                <td style="padding: 15px;">
                                                    <div
                                                        style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                                        USER EMAIL
                                                    </div>
                                                    <div
                                                        style="font-size: 14px; color: #2d3748; font-weight: 500;">{{ $errorData['user_email'] }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <!-- User Group -->
                                    @if(isset($errorData['user_group']))
                                        <td width="31%" style="padding: 0 0 0 7px; vertical-align: top;">
                                            <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                                   width="100%"
                                                   style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
                                                <tr>
                                                    <td style="padding: 15px;">
                                                        <div
                                                            style="font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 600;">
                                                            USER GROUP
                                                        </div>
                                                        <div
                                                            style="font-size: 14px; color: #2d3748; font-weight: 500;">{{ $errorData['user_group'] }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        @endif

                        <!-- Additional Context -->
                        @if(isset($errorData['context']) && count($errorData['context']) > 0)
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                   style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #667eea;">Additional
                                            Context</h3>
                                        <pre
                                            style="background: #ffffff; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px; font-family: monospace; font-size: 12px; color: #495057; ">{{ json_encode($errorData['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre
                                           >
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <!-- Button -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="text-align: center; padding: 20px 0;">
                                    <a href="{{ route('log-viewer.show', ['id' => $errorData['log_id']]) }}"
                                       style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); background-color: #667eea; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">View
                                        Error Details</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 8px 8px;">
                        <p style="margin: 0 0 5px 0; color: #666; font-size: 12px;">This is an automated notification
                            from TMS</p>
                        <p style="margin: 0; color: #666; font-size: 12px;">Please do not reply to this email</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

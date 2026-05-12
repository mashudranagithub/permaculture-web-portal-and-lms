<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body
    style="font-family: serif; text-align: center; border: 15px double #198754; padding: 40px; background: white; margin: 0 auto; color: #111;">

    <div style="border: 1px solid #b8860b; padding: 25px; height: 100%;">

        <div style="text-align: right; font-size: 8px; color: #aaa;">Verify ID: {{ $certificate_no }}</div>

        <div style="margin-top: 10px; text-align: center;">
            @if($organization['logo_path'] && file_exists($organization['logo_path']))
                <img src="{{ $organization['logo_path'] }}" style="max-height: 80px;">
            @else
                <div
                    style="font-size: 24px; font-weight: bold; color: #198754; font-family: sans-serif; text-transform: uppercase; letter-spacing: 2px;">
                    {{ $organization['name'] }}
                </div>
            @endif
        </div>

        <h1 style="font-size: 42pt; margin: 5px 0; font-weight: bold; letter-spacing: 5px;">CERTIFICATE</h1>
        <h3 style="font-size: 16pt; font-style: italic; color: #666; margin-bottom: 20px;">OF COMPLETION</h3>

        <p style="font-size: 15pt; margin: 0;">This is to certify that</p>
        <div
            style="font-size: 36pt; font-weight: bold; color: #198754; border-bottom: 1px solid #b8860b; display: inline-block; padding: 0 40px; margin: 20px 0;">
            {{ $student }}
        </div>

        <p style="font-size: 15pt; margin: 0;">has successfully completed</p>
        <div style="font-size: 24pt; font-weight: bold; margin-bottom: 25px;">{{ $course }}</div>

        <p style="font-size: 13pt; margin-bottom: 15px;">Issued on {{ $issue_date }}</p>

        <table style="width: 100%; margin-top: 30px;">
            <tr>
                <td style="width: 35%; text-align: center; vertical-align: middle;">
                    <div
                        style="border-top: 1px solid #444; width: 280px; margin: 0 auto; padding-top: 5px; font-size: 11pt;">
                        Registrar</div>
                </td>
                <td style="width: 30%; text-align: center; vertical-align: middle;">
                    <div
                        style="width: 65px; height: 65px; border: 3px double #b8860b; border-radius: 50%; color: #b8860b; font-size: 8px; font-weight: bold; padding-top: 20px; margin: 0 auto;">
                        OFFICIAL</div>
                </td>
                <td style="width: 35%; text-align: right; vertical-align: bottom;">
                    <barcode code="{{ $verify_url }}" type="QR" size="0.65" error="M" />
                    <div style="font-size: 8px; color: #999; margin-top: 2px;">Verify Authenticity</div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 20px; font-size: 8px; color: #aaa; font-family: sans-serif;">
            Verified Digital Credential • Permaculture LMS Excellence
        </div>
    </div>

</body>

</html>
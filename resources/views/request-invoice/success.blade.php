<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>Request Submitted</title></head>
<body style="font-family:Arial;padding:20px">
<h2>Request Submitted Successfully</h2>
<p>Request ID: #{{ $requestInvoice->id }}</p>
@if($whatsAppUrl)
<a href="{{ $whatsAppUrl }}" target="_blank" style="display:inline-block;padding:10px 14px;background:#25D366;color:#fff;border-radius:6px;text-decoration:none">Send Request on WhatsApp</a>
@else
<p>Please configure <code>COMPANY_WHATSAPP_NUMBER</code> in environment settings.</p>
@endif
</body></html>

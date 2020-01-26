{{ $booking->email }}
QR Code:
<img src="{{ asset("storage/" . $booking->qr_code_path) }}" />

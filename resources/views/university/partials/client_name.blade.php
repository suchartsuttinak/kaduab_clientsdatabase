{{ $client->full_name ?? $client->fullname ?? $client->name ?? ('ผู้รับบริการ #' . $client->id) }}

<?php

namespace App\Filament\Resources\Resource\Widgets;

use App\Models\Booking;
use Filament\Widgets\Widget;

class BookingCalendarWidget extends Widget
{
    protected static string $view = 'filament.resources.resource.widgets.booking-calendar-widget';
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $bookings = Booking::with('product', 'user')
            ->whereIn('status', ['pending', 'approved'])
            ->whereBetween('tanggal_mulai', [
                now()->startOfMonth()->subMonth(),
                now()->endOfMonth()->addMonth()
            ])
            ->get();

        $events = $bookings->map(function ($booking) {
            $statusColors = [
                'pending' => '#f59e0b',
                'approved' => '#10b981',
            ];

            return [
                'id' => $booking->id,
                'title' => $booking->product->nama . ' - ' . $booking->user->name,
                'start' => $booking->tanggal_mulai->format('Y-m-d'),
                'end' => $booking->tanggal_selesai->addDay()->format('Y-m-d'),
                'backgroundColor' => $statusColors[$booking->status] ?? '#6b7280',
                'borderColor' => $statusColors[$booking->status] ?? '#6b7280',
                'extendedProps' => [
                    'status' => $booking->status,
                    'product' => $booking->product->nama,
                    'user' => $booking->user->name,
                ],
            ];
        });

        return [
            'events' => $events,
        ];
    }
}

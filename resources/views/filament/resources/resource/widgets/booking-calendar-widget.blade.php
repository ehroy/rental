<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-4">
            <div id="calendar"></div>
        </div>
    </x-filament::section>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    locale: 'id',
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        list: 'List'
                    },
                    events: @json($this->getViewData()['events']),
                    eventClick: function(info) {
                        const event = info.event;
                        const props = event.extendedProps;
                        
                        alert(
                            'Produk: ' + props.product + '\n' +
                            'User: ' + props.user + '\n' +
                            'Status: ' + props.status.toUpperCase() + '\n' +
                            'Mulai: ' + event.start.toLocaleDateString('id-ID') + '\n' +
                            'Selesai: ' + event.end.toLocaleDateString('id-ID')
                        );
                    }
                });
                
                calendar.render();
            });
        </script>
    @endpush
</x-filament-widgets::widget>
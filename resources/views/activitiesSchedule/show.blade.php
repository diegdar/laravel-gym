<x-layouts.app>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mx-2 sm:mx-5"> 
        <!-- name -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Actividad:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $activitySchedule->activity->name }}</p>
            </div>
        </article>
        <!-- sala -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Sala:</span>
                <p name="description" class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3"
                    id="description">
                    {{ $activitySchedule->room->name }}</p>
            </div>
        </article>
        <!-- day date -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Dia/Fecha:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $dayDateFormatted }}</p>
            </div>
        </article>
        <!-- start time -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Hora inicio:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $startTimeFormatted }}</p>
            </div>
        </article>
        <!-- duration -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Duración:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $activitySchedule->activity->duration }} minutos</p>
            </div>
        </article>
        <!-- available slots -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Plazas disponibles:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $availableSlots }}</p>
            </div>
        </article>
        <!-- total slots -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Total plazas:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $activitySchedule->max_enrollment }}</p>
            </div>
        </article>
        <!-- current enrollment -->
        <article class="flex flex-col gap-2 mt-5">
            <div class="flex flex-col md:flex-row">
                <span class="font-bold dark:text-red-300">
                    Total inscritos:</span>
                <p class="text-gray-700 dark:text-gray-300 text-lg font-semibold mb-2 ml-3" id="description">
                    {{ $activitySchedule->current_enrollment }}</p>
            </div>
        </article>
    </div>

</x-layouts.app>
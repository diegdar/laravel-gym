<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityScheduleListService
{
    public function __construct()
    {
        Carbon::setLocale('es');
    }

    /**
     * Invoke service to get schedules for the next 7 days including today.
     *
     * @return array [schedules: array, allTimes: \Illuminate\Support\Collection]
     */
    public function __invoke(): array
    {
        $schedules = $this->getNestedSchedules();
        $allTimes  = $this->extractAllTimes($schedules);

        return [$schedules, $allTimes];
    }

    /**
     * Fetch raw pivot records joined with activities and rooms,
     * limited to activities starting between today and +6 days.
     *
     * @return Collection
     */
    public function getActivityScheduleRecords(): Collection
    {
        $start = Carbon::today()->startOfDay();
        $end   = Carbon::today()->addDays(6)->endOfDay();

        return DB::table('activity_schedules as asch')
            ->join('activities as act', 'asch.activity_id', '=', 'act.id')
            ->join('rooms as r', 'asch.room_id', '=', 'r.id')
            ->whereBetween('asch.start_datetime', [$start, $end])
            ->select(
                'asch.id as pivot_id',
                'asch.start_datetime as start_time',
                'asch.end_datetime as end_time',
                'act.id as activity_id',
                'act.name as activity_name',
                'act.duration',
                'asch.max_enrollment',
                'asch.current_enrollment',
                'r.id as room_id',
                'r.name as room_name'
            )
            ->get();
    }

    /**
     * Build nested schedule array for the upcoming 7 days.
     *
     * @return array
     */
    public function getNestedSchedules(): array
    {
        $records = $this->getActivityScheduleRecords();
        $tree    = $this->buildScheduleTree($records);

        $sortedDays = $this->sortDays($tree);
        return $this->sortSlots($sortedDays);
    }

    /**
     * Build tree structure [dayKey => ['label' => ..., 'slots' => [...]]]
     *
     * @param Collection $records
     * @return array
     */
    private function buildScheduleTree(Collection $records): array
    {
        $tree = [];

        foreach ($records as $row) {
            $startDt   = Carbon::parse($row->start_time);
            $dayKey    = $startDt->format('Y-m-d');
            $dayLabel  = ucfirst($startDt->isoFormat('dddd')) . '/' . $startDt->format('d');
            $timeLabel = $startDt->format('G:i');

            $tree[$dayKey]['label']  = $dayLabel;
            $tree[$dayKey]['slots'][$timeLabel][] = $this->mapRecordToEntry($row);
        }

        return $tree;
    }

    /**
     * Map DB record to schedule entry array.
     *
     * @param object $row
     * @return array
     */
    private function mapRecordToEntry(object $row): array
    {
        return [
            'pivot_id'       => $row->pivot_id,
            'activity_id'    => $row->activity_id,
            'room_id'        => $row->room_id,
            'start_time'     => Carbon::parse($row->start_time)->format('G:i'),
            'end_time'       => Carbon::parse($row->end_time)->format('G:i'),
            'activity_name'  => $row->activity_name,
            'room_name'      => $row->room_name,
            'duration'       => $row->duration,
            'max_enrollment' => $row->max_enrollment,
        ];
    }

    /**
     * Sort days ascending by date key.
     *
     * @param array $tree
     * @return array
     */
    private function sortDays(array $tree): array
    {
        ksort($tree);
        return $tree;
    }

    /**
     * Sort slots within each day and map to final labels.
     *
     * @param array $sortedDays
     * @return array
     */
    private function sortSlots(array $sortedDays): array
    {
        $nested = [];

        foreach ($sortedDays as $dayData) {
            uksort($dayData['slots'], fn($a, $b) =>
                Carbon::createFromFormat('G:i', $a)->timestamp <=>
                Carbon::createFromFormat('G:i', $b)->timestamp
            );

            $nested[$dayData['label']] = $dayData['slots'];
        }

        return $nested;
    }

    /**
     * Extract all unique times sorted ascending from nested schedules.
     *
     * @param array $schedules
     * @return Collection<string>
     */
    public function extractAllTimes(array $schedules): Collection
    {
        return collect($schedules)
            ->flatMap(fn(array $slots): array => array_keys($slots))
            ->unique()
            ->sortBy(fn(string $time): int => Carbon::createFromFormat('H:i', $time)->timestamp)
            ->values();
    }
}

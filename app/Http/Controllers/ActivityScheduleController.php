<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityScheduleFormRequest;
use App\Models\ActivitySchedule;
use App\Models\Activity;
use App\Models\Room;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateActivityScheduleFormRequest;
use App\Services\EnrollUserInScheduleService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\ListActivityScheduleService;
use App\Services\StoreActivityScheduleService;
use Carbon\Carbon;

class ActivityScheduleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:activity.schedules.index', only: ['index']),
            new Middleware('permission:activity.schedules.show', only: ['show']),
            new Middleware('permission:activity.schedules.create', only: ['create', 'store']),
            new Middleware('permission:activity.schedules.edit', only: ['edit', 'update']),
            new Middleware('permission:activity.schedules.destroy', only: ['destroy']),
            new Middleware('permission:activity.schedules.enroll', only: ['enrollUserInSchedule']),
            new Middleware('permission:activity.schedules.unenroll', only: ['unenrollUserInSchedule']),
        ];
    }
    
    /**
     * Displays a list of activity schedules.
     *
     * @param ListActivityScheduleService $scheduleService The service to list activity schedules.
     * @return \Illuminate\View\View The view displaying the list of activity schedules.
     */
    public function index(ListActivityScheduleService $scheduleService): View
    {
        [$schedules, $allTimes] = $scheduleService();

        return view('activitiesSchedule.index', compact('schedules', 'allTimes'));
    }

    /**
     * Displays an activity schedule.
     *
     * Retrieves the activity schedule from the given model binding and formats
     * the start datetime for display.
     *
     * @param ActivitySchedule $activitySchedule The activity schedule to be displayed.
     *
     * @return \Illuminate\View\View The view displaying the activity schedule.
     */
    public function show(ActivitySchedule $activitySchedule): View
    {
        $startTimeFormatted = Carbon::parse
            ($activitySchedule->start_datetime)
            ->format('G:i');
        $dayDateFormatted = Carbon::parse   
            ($activitySchedule->start_datetime)
                ->translatedFormat('l, d F');
        $availableSlots = $activitySchedule->max_enrollment 
            - $activitySchedule->current_enrollment;     

        return view('activitiesSchedule.show', compact(['activitySchedule', 'startTimeFormatted', 'dayDateFormatted', 'availableSlots']));
    }

    /**
     * Shows the form to create a new activity schedule.
     *
     * Retrieves all activities and rooms to populate the form.
     *
     * @return \Illuminate\View\View The view with the form to create a new activity schedule.
     */
    public function create(): View
    {
        $activities = Activity::all();;
        $rooms = Room::all();
        return view('activitiesSchedule.create', compact(['activities', 'rooms']));
    }

    /**
     * Validates and stores a new activity schedule in the database.
     *
     * The request must contain the activity ID, start and end datetime, room ID, and maximum and current enrollment.
     * The activity ID must exist in the activities table.
     * The start datetime must be after or equal to today.
     * The room ID must exist in the rooms table.
     * The end datetime must be after the start datetime.
     * The maximum enrollment must be between 10 and 50.
     * The current enrollment must be between 0 and the maximum enrollment.
     *
     * If the request is valid, the activity schedule will be stored in the database.
     * The user will then be redirected to the activities schedule list with a success message.
     */
    public function store(
        StoreActivityScheduleFormRequest $request, StoreActivityScheduleService $storeActivityScheduleService
    ): RedirectResponse
    {
        $storeActivityScheduleService($request);

        return redirect()->route('activity.schedules.index')->with('success', 'Horario creado correctamente.');
    }

    /**
     * Shows the form to edit an existing activity schedule.
     *
     * Retrieves all activities and rooms to populate the form.
     *
     * @param ActivitySchedule $activitySchedule The activity schedule to be edited.
     * @return \Illuminate\View\View The view with the form to edit the activity schedule.
     */
    public function edit(ActivitySchedule $activitySchedule): View
    {
        $activities = Activity::all();
        $rooms = Room::all();
        return view('activitiesSchedule.edit', compact(['activitySchedule', 'activities', 'rooms']));
    }

    /**
     * Updates an existing activity schedule in the database.
     *
     * The request must contain the activity ID, start datetime, room ID, and maximum enrollment.
     * The activity ID must exist in the activities table.
     * The start datetime must be after today.
     * The room ID must exist in the rooms table.
     * The maximum enrollment must be between 10 and 50.
     *
     * If the request is valid, the activity schedule will be updated in the database.
     * The user will then be redirected to the activities schedule list with a success message.
     */
    public function update(UpdateActivityScheduleFormRequest $request, ActivitySchedule $activitySchedule): RedirectResponse
    {
        $activitySchedule->update($request->all());
        return redirect()->route('activity.schedules.index')->with('success', 'Horario actualizado correctamente.');
    }

    /**
     * Deletes an activity schedule from the database.
     *
     * @param ActivitySchedule $activitySchedule The activity schedule to be deleted.
     * @return \Illuminate\Http\RedirectResponse The redirect response with a success message.
     */
    public function destroy(ActivitySchedule $activitySchedule): RedirectResponse
    {
        $activitySchedule->delete();
        return redirect()->route('activity.schedules.index')->with('success', 'Horario eliminado correctamente.');
    }

    public function enrollUserInSchedule(ActivitySchedule $activitySchedule, EnrollUserInScheduleService $enrollUserInScheduleService): RedirectResponse
    {
        $result = $enrollUserInScheduleService
                    ->enrollUser($activitySchedule);
        
        if($result['status'] == 'success') {
            return redirect()->route('activity.schedules.index')->with('success', $result['message']);
        } else {
            return redirect()->route('activity.schedules.index')->with('error', $result['message']);
        }
    }

    public function unenrollUserInSchedule(ActivitySchedule $activitySchedule, EnrollUserInScheduleService $enrollUserInScheduleService): RedirectResponse
    {
        $result = $enrollUserInScheduleService
                    ->unenrollUser($activitySchedule);
        
        if($result['status'] == 'success') {
            return redirect()->route('activity.schedules.index')->with('success', $result['message']);
        } else {
            return redirect()->route('activity.schedules.index')->with('error', $result['message']);
        }
    }

}

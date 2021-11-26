<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use learntotrade\salesforce\CoachingSession;
use learntotrade\salesforce\fields\CoachingSessionFields;
use learntotrade\salesforce\fields\PersonFields;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Repositories\Interfaces\CoachRepositoryInterface;
use App\Repositories\Interfaces\SaleRepositoryInterface;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    private $result = [];

    private $scheduleDate = [];

    private $coachingSessionStatus = null;

    private $statuses = [
        'booked' => 'Booked',
        'noshow' => 'No Show',
        'attended' => 'Attended',
    ];

    public function all($resource=''): array
    {
        if (config('app.enable_api_dummy_data')) {
            $this->dummy();

        } else {
            $this->live($resource);
        }

        return $this->result;
    }

    public function live($resource=''): void
    {
        $coachingSessions = [];

        $sale = resolve(SaleRepositoryInterface::class)->all();
        $saleId = Arr::pluck($sale['sales'], 'id');

        $coaches = resolve(CoachRepositoryInterface::class)->all();
        $coachesTimezone = $this->getCoachesTimezone($coaches);
        $coachCountry = $this->getCoachesCountry($coaches);
        
        $where = [
            CoachingSessionFields::COACH . ' IN (\'' . implode('\',\'', array_keys($coachCountry)) . '\') and ' .
            CoachingSessionFields::STATUS . ' = \'Pending\''
        ];

        if ($this->coachingSessionStatus == "all") {
            $otherStatuses = resolve(CoachingSession::class)->query(
                array_values(config('api.sf_schedule')),
                [
                    CoachingSessionFields::SALE . ' IN (\'' . implode('\',\'', $saleId) . '\') and ' .
                    CoachingSessionFields::STATUS . ' IN (\'' . implode('\',\'', $this->statuses) . '\') ', 
                ],
                ['order' => CoachingSessionFields::DATE . ' ASC, ' . CoachingSessionFields::START_TIME . ' ASC']
            )['records'] ?? [];

            if (! empty($this->scheduleDate)) {
                $where[] = CoachingSessionFields::DATE . ' >= '.$this->scheduleDate['from'] .' and ' .
                           CoachingSessionFields::DATE . ' <= '.$this->scheduleDate['to'];
            } 
            $available = resolve(CoachingSession::class)->query(
                array_values(config('api.sf_schedule')),
                $where,
                ['order' => CoachingSessionFields::DATE . ' ASC, ' . CoachingSessionFields::START_TIME . ' ASC']
            )['records'] ?? [];

            $coachingSessions = array_merge($otherStatuses, $available);

        } else {
            if (! is_null($this->coachingSessionStatus) and in_array($this->coachingSessionStatus, array_keys($this->statuses))) {
                $where = ['(' . CoachingSessionFields::SALE . ' IN (\'' . implode('\',\'', $saleId) . '\') and ' . 
                                CoachingSessionFields::STATUS . ' = \''.$this->statuses[$this->coachingSessionStatus].'\')'];
            }

            if (! empty($this->scheduleDate)) {
                $where[] = CoachingSessionFields::DATE . ' >= '.$this->scheduleDate['from'] .' and ' .
                            CoachingSessionFields::DATE . ' <= '.$this->scheduleDate['to'];
            }

            $coachingSessions = resolve(CoachingSession::class)->query(
                                    array_values(config('api.sf_schedule')),
                                    $where
                                )['records'] ?? [];
        }

        $data = [];
        if (count($coachingSessions) > 0) {
            foreach ($coachingSessions as $coachSession) {
                $data[] = $this->mapColumn($coachSession, $coachesTimezone);
            }
        }

        $this->result = [
            'schedules' => $data,
        ];
    }

    public function mapColumn(array $coachSession, array $coachesTimezone=[])
    {
        return collect($this->getFields())->map(function($val, $key) use($coachSession) {
            return $coachSession[$val];
        })->merge($this->convertDateTime($coachSession, $coachesTimezone));
    }

    public function convertDateTime(array $coachSession, array $coachesTimezone=[])
    {
        $customerTimezone = session('sf_customer')[PersonFields::TIMEZONE];
        $coachId = $coachSession[$this->getFields('coach_id')];
        $coachDate = $coachSession[$this->getFields('date')];
        $coachStartTime = $coachSession[$this->getFields('start_time')];
        $coachEndTime = $coachSession[$this->getFields('end_time')];
        $coachTimezone = $coachesTimezone[$coachId] ?? '';
        $startTime = [];
        $endTime = [];

        if (! empty($coachTimezone)) {
            $startTime = Carbon::createFromFormat('Y-m-d H:i', $coachDate . ' ' . $coachStartTime, $coachTimezone);
            $endTime = Carbon::createFromFormat('Y-m-d H:i', $coachDate . ' ' . $coachEndTime, $coachTimezone);

            $startTime->setTimezone($customerTimezone);
            $endTime->setTimezone($customerTimezone);

            $startTime = explode(' ', $startTime->format('Y-m-d h:i'));
            $endTime = explode(' ', $endTime->format('Y-m-d h:i'));
        }

        return [
            'date' => $startTime[0] ?? '',
            'start_time' => $startTime[1] ?? '',
            'end_time' => $endTime[1] ?? '',
            'timezone' => $customerTimezone,
            'schedule' => [
                'timezone' => $coachTimezone,
                'start_time' => $coachStartTime,
                'end_time' => $coachEndTime,
                'date' => $coachDate
            ],
        ];
    }

    public function setDate($dateFrom, $dateTo): void
    {
        $this->scheduleDate = [
            'from' => $dateFrom,
            'to' => $dateTo
        ];
    }

    public function setStatus($status): void
    {
        $this->coachingSessionStatus = $status;
    }

    public function getCountries(string $name='')
    {
        $countries = array_flip(array_reverse(__('country')));

        if (empty($name)) {
            return $countries;
        }

        return optional($countries)[$name];
    }

    public function getCoachesCountry($coaches, string $coachId='')
    {
        $countries = Arr::pluck($coaches['coaches'], 'country', 'id');

        if (empty($coachId)) {
            return $countries;
        }

        return optional($countries)[$coachId];
    }

    public function getCoachesTimezone($coaches)
    {
        return Arr::pluck($coaches['coaches'], 'timezone_sid_key', 'id');
    }

    public function getFields(string $name='')
    {
        $fields = config('api.sf_schedule');
        
        if (empty($name)) {
            return $fields;
        }

        return optional($fields)[$name];
    }

    public function dummy(): void
    {
        $this->result = [
            'schedules' => [],
        ];
    }
}
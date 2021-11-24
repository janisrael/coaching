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
use learntotrade\salesforce\fields\UserFields;

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
        $coachCountry = $this->getCoachesCountry($coaches);
        $coachTimeZone = $this->getCoachesTimezone($coaches);
        
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
                $data[] = $this->mapColumn($coachSession, $coachCountry, $coachTimeZone);
            }
        }

        $this->result = [
            'schedules' => $data,
        ];
    }

    public function mapColumn(array $coachSession, array $coachCountry=[], array $coachTimeZone=[])
    {
        return collect($this->getFields())->map(function($val, $key) use($coachSession) {
            return $coachSession[$val];
        })->merge($this->convertDateTime($coachSession, $coachCountry, $coachTimeZone));
    }

    public function convertDateTime(array $coachSession, array $coachCountry=[], array $timeZone=[])
    {
        $customerTimeZone = session('sf_customer')[PersonFields::TIMEZONE];
        $coachId = $coachSession[$this->getFields('coach_id')];
        $coachDate = $coachSession[$this->getFields('date')];
        $coachStartTime = $coachSession[$this->getFields('start_time')];
        $coachEndTime = $coachSession[$this->getFields('end_time')];
        $coachTimeZone = $timeZone[$coachId] ?? '';
        $startTime = [];
        $endTime = [];

        if (! empty($coachTimeZone)) {
            $startTime = Carbon::createFromFormat('Y-m-d H:i', $coachDate . ' ' . $coachStartTime, $coachTimeZone);
            $endTime = Carbon::createFromFormat('Y-m-d H:i', $coachDate . ' ' . $coachEndTime, $coachTimeZone);

            $startTime->setTimezone($customerTimeZone);
            $endTime->setTimezone($customerTimeZone);

            $startTime = explode(' ', $startTime->format('Y-m-d h:i'));
            $endTime = explode(' ', $endTime->format('Y-m-d h:i'));
        }

        return [
            'date' => $startTime[0] ?? '',
            'start_time' => $startTime[1] ?? '',
            'end_time' => $endTime[1] ?? '',
            'timezone' => $customerTimeZone,
            'schedule' => [
                'timezone' => $coachTimeZone,
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
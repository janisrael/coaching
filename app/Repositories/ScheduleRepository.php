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
        $coachCountry = $this->getCoaches();
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
                $data[] = $this->mapColumn($coachSession, $coachCountry);
            }
        }

        $this->result = [
            'schedules' => $data,
        ];
    }

    public function mapColumn(array $coachSession, array $coachCountry=[])
    {
        return collect($this->getFields())->map(function($val, $key) use($coachSession) {
            return $coachSession[$val];
        })->merge($this->convertDateTime($coachSession, $coachCountry));
    }

    public function convertDateTime(array $coachSession, array $coachCountry=[])
    {
        $customerTimeZone = session('sf_customer')[PersonFields::TIMEZONE];
        $coachId = $coachSession[$this->getFields('coach_id')];
        $coachDate = $coachSession[$this->getFields('date')];
        $coachStartTime = $coachSession[$this->getFields('start_time')];
        $coachEndTime = $coachSession[$this->getFields('end_time')];
        $coachCountry = $coachCountry[$coachId] ?? $this->getCoaches($coachId);
        $startTime = [];
        $endTime = [];

        if (! is_null($coachCountry)) {
            $countryCode = $this->getCountries($coachCountry) ?: false;
            $coachTimezone = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $countryCode)[0];
            $startTime = Carbon::createFromFormat('Y-m-d H:i', $coachDate . ' ' . $coachStartTime, $coachTimezone);
            $endTime = Carbon::createFromFormat('Y-m-d H:i', $coachDate . ' ' . $coachEndTime, $coachTimezone);

            $startTime->setTimezone($customerTimeZone);
            $endTime->setTimezone($customerTimeZone);

            $startTime = explode(' ', $startTime->format('Y-m-d h:i'));
            $endTime = explode(' ', $endTime->format('Y-m-d h:i'));
        }

        return [
            'converted_date' => $startTime[0] ?? '',
            'converted_start_time' => $startTime[1] ?? '',
            'converted_end_time' => $endTime[1] ?? '',
            'customer_timezone' => $customerTimeZone,
            'coach_timezone' => $coachTimezone ?? '',
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

    public function getCoaches(string $coachId='')
    {
        $coach = resolve(CoachRepositoryInterface::class)->all();
        
        $countries = Arr::pluck($coach['coaches'], 'country', 'id');

        if (empty($coachId)) {
            return $countries;
        }

        $country = optional($countries)[$coachId];

        if (is_null($country)) {
            // $sfCoach = resolve(CoachRepositoryInterface::class)->getCoachById($coachId);
            // $country = $sfCoach[UserFields::COACH_TIME_ZONE];
            $country = null; 
        }

        return $country;
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
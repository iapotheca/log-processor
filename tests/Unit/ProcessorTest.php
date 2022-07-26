<?php

namespace Tests\Unit;

use Iapotheca\LogProcessor\Processor;
use Tests\TestCase;

class ProcessorTest extends TestCase
{
    public function test_can_ingest_data_from_logs()
    {
        $estimateId = 1234;
        $courierId = 2345;
        $customCourier = 'flashbox';
        $requestId = 'sdahjkdhjksa';
        $submissionId = 3456;
        $action = 'get-run-details-request';
        $runId = 4567;
        $webhook = 'parcel-created';
        $teamId = 5678;
        $notification = 'run-submission';

        $record = [];
        $record['message'] = '[ESTIMATE ' . $estimateId . '] [COURIER ' . $courierId . '] [CUSTOM_COURIER ' . $customCourier . '] [REQUEST_ID ' . $requestId . '] [SUBMISSION ' . $submissionId . '] [ACTION ' . $action . '] [RUN ' . $runId . '] [WEBHOOK ' . $webhook . '] [TEAM ' . $teamId . '] [NOTIFICATION ' . $notification . '] test savio 3';

        $result = (new Processor('my-app', [
            'ESTIMATE',
            'COURIER',
            'CUSTOM_COURIER',
            'REQUEST_ID',
            'SUBMISSION',
            'ACTION',
            'RUN',
            'WEBHOOK',
            'TEAM',
            'NOTIFICATION',
        ]))($record);

        $this->assertEquals($estimateId, $result['estimate']);
        $this->assertEquals($courierId, $result['courier']);
        $this->assertEquals($customCourier, $result['custom_courier']);
        $this->assertEquals($requestId, $result['request_id']);
        $this->assertEquals($submissionId, $result['submission']);
        $this->assertEquals($action, $result['action']);
        $this->assertEquals($runId, $result['run']);
        $this->assertEquals($webhook, $result['webhook']);
        $this->assertEquals($teamId, $result['team']);
        $this->assertEquals($notification, $result['notification']);
    }
}

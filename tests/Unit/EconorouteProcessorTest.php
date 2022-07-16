<?php

namespace Tests\Unit;

use Iapotheca\CrLogProcessor\EconorouteProcessor;
use Tests\TestCase;

class ShipmentRatesTest extends TestCase
{
    public function test_can_get_simple_rates()
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

        $result = (new EconorouteProcessor('my-app'))($record);

        $this->assertEquals($estimateId, $result['estimate_id']);
        $this->assertEquals($courierId, $result['courier_id']);
        $this->assertEquals($customCourier, $result['courier_name']);
        $this->assertEquals($requestId, $result['request_id']);
        $this->assertEquals($submissionId, $result['submission_id']);
        $this->assertEquals($action, $result['action']);
        $this->assertEquals($runId, $result['run_id']);
        $this->assertEquals($webhook, $result['webhook']);
        $this->assertEquals($teamId, $result['team_id']);
        $this->assertEquals($notification, $result['notification']);
    }
}

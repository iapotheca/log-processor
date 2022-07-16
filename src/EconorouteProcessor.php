<?php

namespace Iapotheca\CrLogProcessor;

use Monolog\Processor\ProcessorInterface;

class EconorouteProcessor implements ProcessorInterface
{
    protected string $appName;

    public function __construct(?string $appName)
    {
        $this->appName = $appName;
    }

    public function __invoke(array $record): array
    {
        $record['app'] = $this->appName;
        $record['submission_id'] = $this->getSubmission($record['message']);
        $record['webhook'] = $this->getWebhook($record['message']);
        $record['request_id'] = $this->getRequestId($record['message']);
        $record['estimate_id'] = $this->getEstimate($record['message']);
        $record['courier_id'] = $this->getCourier($record['message']);
        $record['courier_name'] = $this->getCourierName($record['message']);
        $record['action'] = $this->getAction($record['message']);
        $record['team_id'] = $this->getTeam($record['message']);
        $record['run_id'] = $this->getRun($record['message']);
        $record['notification'] = $this->getNotification($record['message']);

        return $record;
    }

    /**
     * Extracts webhook name from log text. e.g.: [WEBHOOK parcel-created] => parcel-created
     *
     * @param string $message
     * @return string|null
     */
    protected function getWebhook(string $message): ?string
    {
        $webhookMatches = [];
        preg_match('/WEBHOOK.[a-zA-Z\-]+/', $message, $webhookMatches, PREG_OFFSET_CAPTURE);
        $webhookMatches = $this->getFirstMatch($webhookMatches);

        if (null === $webhookMatches) {
            return null;
        }

        $webhook = $this->getValue($webhookMatches);

        return null === $webhook ? null : $webhook;
    }

    /**
     * Extracts webhook name from log text. e.g.: [NOTIFICATION run-submission] => run-submission
     *
     * @param string $message
     * @return string|null
     */
    protected function getNotification(string $message): ?string
    {
        $notificationMatches = [];
        preg_match('/NOTIFICATION.[a-zA-Z\-]+/', $message, $notificationMatches, PREG_OFFSET_CAPTURE);
        $notificationMatches = $this->getFirstMatch($notificationMatches);

        if (null === $notificationMatches) {
            return null;
        }

        $notification = $this->getValue($notificationMatches);

        return null === $notification ? null : $notification;
    }

    /**
     * Extracts request id from log text. e.g.: [REQUEST_ID sdahjkdhjksa] => sdahjkdhjksa
     *
     * @param string $message
     * @return string|null
     */
    protected function getRequestId(string $message): ?string
    {
        $requestIdMatches = [];
        preg_match('/REQUEST_ID.[a-zA-Z\-]+/', $message, $requestIdMatches, PREG_OFFSET_CAPTURE);
        $requestIdMatches = $this->getFirstMatch($requestIdMatches);

        if (null === $requestIdMatches) {
            return null;
        }

        $requestId = $this->getValue($requestIdMatches);

        return null === $requestId ? null : $requestId;
    }

    /**
     * Extracts estimate id from log text. e.g.: [ESTIMATE 1234] => 1234
     *
     * @param string $message
     * @return int|null
     */
    protected function getEstimate(string $message): ?int
    {
        $estimateIdMatches = [];
        preg_match('/ESTIMATE.\d+/', $message, $estimateIdMatches, PREG_OFFSET_CAPTURE);
        $estimateIdMatches = $this->getFirstMatch($estimateIdMatches);

        if (null === $estimateIdMatches) {
            return null;
        }

        $estimateId = $this->getValue($estimateIdMatches);

        return null === $estimateId ? null : (int) $estimateId;
    }

    /**
     * Extracts courier id from log text. e.g.: [COURIER 1234] => 1234
     *
     * @param string $message
     * @return int|null
     */
    protected function getCourier(string $message): ?int
    {
        $courierIdMatches = [];
        preg_match('/COURIER.\d+/', $message, $courierIdMatches, PREG_OFFSET_CAPTURE);
        $courierIdMatches = $this->getFirstMatch($courierIdMatches);

        if (null === $courierIdMatches) {
            return null;
        }

        $courierId = $this->getValue($courierIdMatches);

        return null === $courierId ? null : (int) $courierId;
    }

    /**
     * Extracts courier name from log text. e.g.: [CUSTOM_COURIER flashbox] => flashbox
     *
     * @param string $message
     * @return string|null
     */
    protected function getCourierName(string $message): ?string
    {
        $customCourierMatches = [];
        preg_match('/CUSTOM_COURIER.[a-zA-Z\-]+/', $message, $customCourierMatches, PREG_OFFSET_CAPTURE);
        $customCourierMatches = $this->getFirstMatch($customCourierMatches);

        if (null === $customCourierMatches) {
            return null;
        }

        $customCourierName = $this->getValue($customCourierMatches);

        return null === $customCourierName ? null : $customCourierName;
    }

    /**
     * Extracts action from log text. e.g.: [ACTION get-run-details-request] => get-run-details-request
     *
     * @param string $message
     * @return string|null
     */
    protected function getAction(string $message): ?string
    {
        $actionMatches = [];
        preg_match('/ACTION.[a-zA-Z\-]+/', $message, $actionMatches, PREG_OFFSET_CAPTURE);
        $actionMatches = $this->getFirstMatch($actionMatches);

        if (null === $actionMatches) {
            return null;
        }

        $action = $this->getValue($actionMatches);

        return null === $action ? null : $action;
    }

    /**
     * Extracts team id from log text. e.g.: [TEAM 1234] => 1234
     *
     * @param string $message
     * @return int|null
     */
    protected function getTeam(string $message): ?int
    {
        $teamMatches = [];
        preg_match('/TEAM.\d+/', $message, $teamMatches, PREG_OFFSET_CAPTURE);
        $teamMatches = $this->getFirstMatch($teamMatches);

        if (null === $teamMatches) {
            return null;
        }

        $teamId = $this->getValue($teamMatches);

        return null === $teamId ? null : (int) $teamId;
    }

    /**
     * Extracts run id from log text. e.g.: [RUN 1234] => 1234
     *
     * @param string $message
     * @return int|null
     */
    protected function getRun(string $message): ?int
    {
        $runMatches = [];
        preg_match('/RUN.\d+/', $message, $runMatches, PREG_OFFSET_CAPTURE);
        $runMatches = $this->getFirstMatch($runMatches);

        if (null === $runMatches) {
            return null;
        }

        $runId = $this->getValue($runMatches);

        return null === $runId ? null : (int) $runId;
    }

    /**
     * Extracts submission id from log text. e.g.: [SUBMISSION 1234] => 1234
     *
     * @param string $message
     * @return int|null
     */
    protected function getSubmission(string $message): ?int
    {
        $submissionMatches = [];
        preg_match('/SUBMISSION.\d+/', $message, $submissionMatches, PREG_OFFSET_CAPTURE);
        $submissionMatches = $this->getFirstMatch($submissionMatches);

        if (null === $submissionMatches) {
            return null;
        }

        $submissionId = $this->getValue($submissionMatches);

        return null === $submissionId ? null : (int) $submissionId;
    }

    /**
     * @param array $matches
     * @return mixed
     */
    private function getFirstMatch(array $matches)
    {
        $firstMatch = isset($matches[0]) ? $matches[0] : null;

        if (null === $firstMatch) {
            return null;
        }

        return isset($firstMatch[0]) ? $firstMatch[0] : null;
    }

    /**
     * @param string $metadata
     * @return mixed
     */
    private function getValue(string $metadata)
    {
        $exploded = explode(' ', $metadata);
        return isset($exploded[1]) ? $exploded[1] : null;
    }
}
